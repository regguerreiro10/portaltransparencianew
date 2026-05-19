/*
  Dota pedidos de frotas sem dotacao - somente system_unit_id = 26.

  Como usar no phpMyAdmin:
    1. Rode primeiro com @APLICAR = 0 para simular.
    2. Confira os SELECTs finais.
    3. Se estiver correto, altere para @APLICAR = 1 e rode novamente.

  Regras:
    - Unidade: pedido_frotas.system_unit_id = 26
    - Status consumidos: aprovado(13), finalizado(8), entregue(20), pagamento aprovado(18)
    - Ignora pedidos que ja possuem dotacao_pedido_frotas ativa
    - Produto: itens_pedido_frotas.tipo = 1 -> saldo_departamento.tipo = 'P'
    - Servico: itens_pedido_frotas.tipo = 2 -> saldo_departamento.tipo = 'S'
    - Valor usado: pedido_frotas.valor_liquido_proposta, rateado proporcionalmente pelos itens
*/

SET @APLICAR = 0;
SET @SYSTEM_UNIT_ID = 26;

START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS tmp_pedidos_sem_dotacao;
CREATE TEMPORARY TABLE tmp_pedidos_sem_dotacao AS
SELECT
    pf.id,
    pf.departamento_unit_id,
    ROUND(COALESCE(pf.valor_liquido_proposta, 0) * 100) AS valor_pedido_cents
FROM pedido_frotas pf
WHERE pf.system_unit_id = @SYSTEM_UNIT_ID
  AND pf.deleted_at IS NULL
  AND pf.departamento_unit_id IS NOT NULL
  AND pf.estado_pedido_frotas_id IN (8, 13, 18, 20)
  AND COALESCE(pf.valor_liquido_proposta, 0) > 0
  AND NOT EXISTS (
      SELECT 1
      FROM dotacao_pedido_frotas dpf
      WHERE dpf.pedido_frotas_id = pf.id
        AND dpf.deleted_at IS NULL
  );

ALTER TABLE tmp_pedidos_sem_dotacao
    ADD PRIMARY KEY (id),
    ADD INDEX idx_tmp_pedidos_depto (departamento_unit_id);

DROP TEMPORARY TABLE IF EXISTS tmp_itens_tipo;
CREATE TEMPORARY TABLE tmp_itens_tipo AS
SELECT
    p.id AS pedido_id,
    p.departamento_unit_id,
    CASE WHEN ip.tipo = 1 THEN 'P' ELSE 'S' END AS tipo,
    SUM(ROUND(COALESCE(ip.valor_total, 0) * 100)) AS total_tipo_cents,
    p.valor_pedido_cents
FROM tmp_pedidos_sem_dotacao p
INNER JOIN itens_pedido_frotas ip
        ON ip.pedido_frotas_id = p.id
       AND ip.deleted_at IS NULL
       AND ip.tipo IN (1, 2)
GROUP BY
    p.id,
    p.departamento_unit_id,
    CASE WHEN ip.tipo = 1 THEN 'P' ELSE 'S' END,
    p.valor_pedido_cents
HAVING total_tipo_cents > 0;

ALTER TABLE tmp_itens_tipo
    ADD INDEX idx_tmp_itens_pedido (pedido_id),
    ADD INDEX idx_tmp_itens_tipo (departamento_unit_id, tipo);

DROP TEMPORARY TABLE IF EXISTS tmp_itens_total;
CREATE TEMPORARY TABLE tmp_itens_total AS
SELECT
    pedido_id,
    SUM(total_tipo_cents) AS total_itens_cents,
    COUNT(*) AS qtde_tipos
FROM tmp_itens_tipo
GROUP BY pedido_id;

ALTER TABLE tmp_itens_total
    ADD PRIMARY KEY (pedido_id);

DROP TEMPORARY TABLE IF EXISTS tmp_demandas;
CREATE TEMPORARY TABLE tmp_demandas AS
SELECT
    it.pedido_id,
    it.departamento_unit_id,
    it.tipo,
    CASE
        WHEN tt.qtde_tipos = 1 THEN it.valor_pedido_cents
        WHEN it.tipo = 'P' THEN ROUND(it.valor_pedido_cents * it.total_tipo_cents / tt.total_itens_cents)
        ELSE it.valor_pedido_cents - COALESCE((
            SELECT ROUND(ip.valor_pedido_cents * ip.total_tipo_cents / tt.total_itens_cents)
            FROM tmp_itens_tipo ip
            WHERE ip.pedido_id = it.pedido_id
              AND ip.tipo = 'P'
            LIMIT 1
        ), 0)
    END AS valor_cents
FROM tmp_itens_tipo it
INNER JOIN tmp_itens_total tt
        ON tt.pedido_id = it.pedido_id
WHERE tt.total_itens_cents > 0;

DELETE FROM tmp_demandas
WHERE valor_cents <= 0;

ALTER TABLE tmp_demandas
    ADD PRIMARY KEY (pedido_id, tipo),
    ADD INDEX idx_tmp_demanda_saldo (departamento_unit_id, tipo);

DROP TEMPORARY TABLE IF EXISTS tmp_saldos;
CREATE TEMPORARY TABLE tmp_saldos AS
SELECT *
FROM (
    SELECT
        sd.id AS saldo_departamento_id,
        sd.departamento_unit_id,
        sd.tipo,
        sd.datatransacao,
        ROUND((
            CASE
                WHEN sd.tipo = 'P' THEN COALESCE(NULLIF(sd.saldo_produto, 0), sd.saldo_total, 0)
                WHEN sd.tipo = 'S' THEN COALESCE(NULLIF(sd.saldo_servico, 0), sd.saldo_total, 0)
                ELSE COALESCE(sd.saldo_total, 0)
            END
            - COALESCE((
                SELECT SUM(dpf.valor)
                FROM dotacao_pedido_frotas dpf
                INNER JOIN pedido_frotas pf2
                        ON pf2.id = dpf.pedido_frotas_id
                WHERE dpf.saldo_departamento_id = sd.id
                  AND dpf.deleted_at IS NULL
                  AND pf2.deleted_at IS NULL
                  AND pf2.system_unit_id = @SYSTEM_UNIT_ID
                  AND pf2.estado_pedido_frotas_id <> 9
            ), 0)
        ) * 100) AS disponivel_cents
    FROM saldo_departamento sd
    INNER JOIN departamento_unit du
            ON du.id = sd.departamento_unit_id
    WHERE du.system_unit_id = @SYSTEM_UNIT_ID
      AND sd.deleted_at IS NULL
      AND sd.tipotransacao = 'C'
      AND sd.status_saldo_departamento_id <> 4
      AND sd.tipo IN ('P', 'S')
) s
WHERE s.disponivel_cents > 0;

ALTER TABLE tmp_saldos
    ADD PRIMARY KEY (saldo_departamento_id),
    ADD INDEX idx_tmp_saldos_alocacao (departamento_unit_id, tipo, datatransacao, saldo_departamento_id);

DROP TEMPORARY TABLE IF EXISTS tmp_alocacoes;
CREATE TEMPORARY TABLE tmp_alocacoes (
    pedido_frotas_id BIGINT NOT NULL,
    saldo_departamento_id BIGINT NOT NULL,
    tipo CHAR(1) NOT NULL,
    valor_cents BIGINT NOT NULL,
    saldo_atual_cents BIGINT NOT NULL,
    INDEX idx_tmp_aloc_pedido (pedido_frotas_id),
    INDEX idx_tmp_aloc_saldo (saldo_departamento_id)
);

DROP TEMPORARY TABLE IF EXISTS tmp_pedidos_nao_alocados;
CREATE TEMPORARY TABLE tmp_pedidos_nao_alocados (
    pedido_frotas_id BIGINT NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    PRIMARY KEY (pedido_frotas_id)
);

DROP TEMPORARY TABLE IF EXISTS tmp_pedidos_para_processar;
CREATE TEMPORARY TABLE tmp_pedidos_para_processar AS
SELECT DISTINCT
    d.pedido_id,
    d.departamento_unit_id
FROM tmp_demandas d
ORDER BY d.departamento_unit_id, d.pedido_id;

ALTER TABLE tmp_pedidos_para_processar
    ADD PRIMARY KEY (pedido_id);

INSERT INTO tmp_pedidos_nao_alocados (pedido_frotas_id, motivo)
SELECT p.id, 'Sem itens com valor para separar produto/servico'
FROM tmp_pedidos_sem_dotacao p
LEFT JOIN tmp_pedidos_para_processar pp
       ON pp.pedido_id = p.id
WHERE pp.pedido_id IS NULL;

DROP PROCEDURE IF EXISTS sp_dotar_pedidos_frotas_unit26;

DELIMITER $$

CREATE PROCEDURE sp_dotar_pedidos_frotas_unit26()
BEGIN
    DECLARE v_done INT DEFAULT 0;
    DECLARE v_pedido_id BIGINT DEFAULT 0;
    DECLARE v_departamento_id BIGINT DEFAULT 0;
    DECLARE v_tipo CHAR(1);
    DECLARE v_restante BIGINT DEFAULT 0;
    DECLARE v_saldo_id BIGINT DEFAULT 0;
    DECLARE v_disponivel BIGINT DEFAULT 0;
    DECLARE v_alocar BIGINT DEFAULT 0;
    DECLARE v_sem_saldo INT DEFAULT 0;

    DECLARE cur_pedidos CURSOR FOR
        SELECT pedido_id, departamento_unit_id
        FROM tmp_pedidos_para_processar
        ORDER BY departamento_unit_id, pedido_id;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

    OPEN cur_pedidos;

    loop_pedidos: LOOP
        FETCH cur_pedidos INTO v_pedido_id, v_departamento_id;

        IF v_done = 1 THEN
            LEAVE loop_pedidos;
        END IF;

        SET v_sem_saldo = 0;

        IF EXISTS (
            SELECT 1
            FROM tmp_demandas d
            WHERE d.pedido_id = v_pedido_id
              AND d.valor_cents > COALESCE((
                  SELECT SUM(s.disponivel_cents)
                  FROM tmp_saldos s
                  WHERE s.departamento_unit_id = d.departamento_unit_id
                    AND s.tipo = d.tipo
              ), 0)
        ) THEN
            SET v_sem_saldo = 1;
        END IF;

        IF v_sem_saldo = 1 THEN
            INSERT IGNORE INTO tmp_pedidos_nao_alocados (pedido_frotas_id, motivo)
            VALUES (v_pedido_id, 'Saldo insuficiente no departamento/tipo');
        ELSE
            SET v_tipo = 'P';
            SET v_restante = COALESCE((
                SELECT valor_cents
                FROM tmp_demandas
                WHERE pedido_id = v_pedido_id
                  AND tipo = v_tipo
                LIMIT 1
            ), 0);

            WHILE v_restante > 0 DO
                SELECT s.saldo_departamento_id, s.disponivel_cents
                INTO v_saldo_id, v_disponivel
                FROM tmp_saldos s
                WHERE s.departamento_unit_id = v_departamento_id
                  AND s.tipo = v_tipo
                  AND s.disponivel_cents > 0
                ORDER BY s.datatransacao, s.saldo_departamento_id
                LIMIT 1;

                SET v_alocar = LEAST(v_restante, v_disponivel);

                UPDATE tmp_saldos
                SET disponivel_cents = disponivel_cents - v_alocar
                WHERE saldo_departamento_id = v_saldo_id;

                INSERT INTO tmp_alocacoes
                    (pedido_frotas_id, saldo_departamento_id, tipo, valor_cents, saldo_atual_cents)
                VALUES
                    (v_pedido_id, v_saldo_id, v_tipo, v_alocar, v_disponivel - v_alocar);

                SET v_restante = v_restante - v_alocar;
            END WHILE;

            SET v_tipo = 'S';
            SET v_restante = COALESCE((
                SELECT valor_cents
                FROM tmp_demandas
                WHERE pedido_id = v_pedido_id
                  AND tipo = v_tipo
                LIMIT 1
            ), 0);

            WHILE v_restante > 0 DO
                SELECT s.saldo_departamento_id, s.disponivel_cents
                INTO v_saldo_id, v_disponivel
                FROM tmp_saldos s
                WHERE s.departamento_unit_id = v_departamento_id
                  AND s.tipo = v_tipo
                  AND s.disponivel_cents > 0
                ORDER BY s.datatransacao, s.saldo_departamento_id
                LIMIT 1;

                SET v_alocar = LEAST(v_restante, v_disponivel);

                UPDATE tmp_saldos
                SET disponivel_cents = disponivel_cents - v_alocar
                WHERE saldo_departamento_id = v_saldo_id;

                INSERT INTO tmp_alocacoes
                    (pedido_frotas_id, saldo_departamento_id, tipo, valor_cents, saldo_atual_cents)
                VALUES
                    (v_pedido_id, v_saldo_id, v_tipo, v_alocar, v_disponivel - v_alocar);

                SET v_restante = v_restante - v_alocar;
            END WHILE;
        END IF;
    END LOOP;

    CLOSE cur_pedidos;
END$$

DELIMITER ;

CALL sp_dotar_pedidos_frotas_unit26();

DROP PROCEDURE IF EXISTS sp_dotar_pedidos_frotas_unit26;

SELECT
    IF(@APLICAR = 1, 'APLICAR', 'SIMULACAO') AS modo,
    COUNT(DISTINCT pedido_frotas_id) AS pedidos_a_dotar,
    COUNT(*) AS dotacoes_a_inserir,
    CONCAT('R$ ', REPLACE(REPLACE(REPLACE(FORMAT(COALESCE(SUM(valor_cents), 0) / 100, 2), ',', 'X'), '.', ','), 'X', '.')) AS valor_total
FROM tmp_alocacoes;

SELECT
    COUNT(*) AS pedidos_nao_alocados
FROM tmp_pedidos_nao_alocados;

SELECT
    pna.pedido_frotas_id,
    pna.motivo
FROM tmp_pedidos_nao_alocados pna
ORDER BY pna.pedido_frotas_id;

SELECT
    a.pedido_frotas_id,
    a.saldo_departamento_id,
    a.tipo,
    CONCAT('R$ ', REPLACE(REPLACE(REPLACE(FORMAT(a.valor_cents / 100, 2), ',', 'X'), '.', ','), 'X', '.')) AS valor_dotacao,
    CONCAT('R$ ', REPLACE(REPLACE(REPLACE(FORMAT(a.saldo_atual_cents / 100, 2), ',', 'X'), '.', ','), 'X', '.')) AS saldo_atual_empenho
FROM tmp_alocacoes a
ORDER BY a.pedido_frotas_id, a.tipo, a.saldo_departamento_id;

INSERT INTO dotacao_pedido_frotas
    (created_at, updated_at, pedido_frotas_id, saldo_departamento_id, valor, saldo_atual, propostas_id)
SELECT
    NOW(),
    NOW(),
    a.pedido_frotas_id,
    a.saldo_departamento_id,
    a.valor_cents / 100,
    a.saldo_atual_cents / 100,
    NULL
FROM tmp_alocacoes a
WHERE @APLICAR = 1;

SELECT ROW_COUNT() AS dotacoes_inseridas;

COMMIT;
