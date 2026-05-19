<?php

class AuditoriaPedidoFrotasPopup extends TWindow
{
    private const DATABASE = 'minierp';
    private const ESTADOS_AUDITORIA = [13, 8, 20, 18];

    public function __construct($param = null)
    {
        parent::__construct();
        parent::setSize(0.85, null);
        parent::setTitle('Auditoria de pedidos de frotas');
        parent::setProperty('class', 'window_modal');

        if (!self::isAllowedUser()) {
            parent::add($this->buildNotice('Acesso nao autorizado para esta auditoria.'));
            return;
        }

        $pendencias = self::getPendencias();

        $container = new TVBox;
        $container->style = 'width: 100%; padding: 10px;';

        $container->add($this->buildSummary($pendencias));
        $container->add($this->buildDatagrid($pendencias));

        parent::add($container);
    }

    public static function shouldShow(): bool
    {
        if (!self::isAllowedUser()) {
            return false;
        }

        return self::countPendencias() > 0;
    }

    private static function isAllowedUser(): bool
    {
        if (TSession::getValue('login') === 'admin') {
            return true;
        }

        $unitName = (string) TSession::getValue('userunitname');

        if ($unitName === '' && TSession::getValue('idunit')) {
            try {
                TTransaction::open(self::DATABASE);
                $unit = new SystemUnit((int) TSession::getValue('idunit'));
                $unitName = (string) ($unit->name ?? '');
                TTransaction::close();
            } catch (Exception $e) {
                TTransaction::rollback();
                return false;
            }
        }

        $unitName = strtoupper(trim($unitName));

        return strpos($unitName, 'NP3') !== false || strpos($unitName, 'XP3') !== false;
    }

    private static function countPendencias(): int
    {
        try {
            TTransaction::open(self::DATABASE);
            $conn = TTransaction::get();
            $stmt = $conn->prepare(self::getAuditSql('COUNT(*) AS total'));
            self::bindAuditParams($stmt);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            TTransaction::close();

            return (int) ($row['total'] ?? 0);
        } catch (Exception $e) {
            TTransaction::rollback();
            return 0;
        }
    }

    private static function getPendencias(): array
    {
        try {
            TTransaction::open(self::DATABASE);
            $conn = TTransaction::get();
            $stmt = $conn->prepare(self::getAuditSql(
                'p.id AS pedido_id,
                 p.descricaopedido,
                 p.estado_pedido_frotas_id AS pedido_estado_id,
                 ep.nome AS pedido_estado,
                 p.valor_total AS pedido_valor_total,
                 p.valor_total_proposta AS pedido_valor_total_proposta,
                 p.valor_desconto_proposta AS pedido_valor_desconto_proposta,
                 p.valor_liquido_proposta AS pedido_valor_liquido_proposta,
                 pr.id AS proposta_id,
                 pr.estado_pedido_frotas_id AS proposta_estado_id,
                 epr.nome AS proposta_estado,
                 pr.valor_total AS proposta_valor_total,
                 pr.valor_desconto AS proposta_valor_desconto,
                 pr.valor_liquido AS proposta_valor_liquido,
                 pr.total_produtos_sem_desconto,
                 pr.total_servicos_sem_desconto,
                 pr.total_geral_com_desconto,
                 pr.total_geral_sem_desconto,
                 pr.desconto_contratual,
                 CONCAT_WS(\', \',
                     CASE WHEN COALESCE(p.valor_total, 0) = 0 THEN \'pedido.valor_total\' END,
                     CASE WHEN COALESCE(p.valor_total_proposta, 0) = 0 THEN \'pedido.valor_total_proposta\' END,
                     CASE WHEN ' . (self::getTaxaContrato() != 0 ? '1 = 1' : '1 = 0') . ' AND COALESCE(p.valor_desconto_proposta, 0) = 0 THEN \'pedido.valor_desconto_proposta\' END,
                     CASE WHEN COALESCE(p.valor_liquido_proposta, 0) = 0 THEN \'pedido.valor_liquido_proposta\' END,
                     CASE WHEN COALESCE(pr.valor_total, 0) = 0 THEN \'proposta.valor_total\' END,
                     CASE WHEN COALESCE(pr.valor_desconto, 0) = 0 THEN \'proposta.valor_desconto\' END,
                     CASE WHEN COALESCE(pr.valor_liquido, 0) = 0 THEN \'proposta.valor_liquido\' END,
                     CASE WHEN EXISTS (
                         SELECT 1
                           FROM itens_propostas ip_prod
                          WHERE ip_prod.propostas_id = pr.id
                            AND ip_prod.deleted_at IS NULL
                            AND ip_prod.tipo = 1
                     ) AND COALESCE(pr.total_produtos_sem_desconto, 0) = 0 THEN \'proposta.total_produtos_sem_desconto\' END,
                     CASE WHEN EXISTS (
                         SELECT 1
                           FROM itens_propostas ip_serv
                          WHERE ip_serv.propostas_id = pr.id
                            AND ip_serv.deleted_at IS NULL
                            AND ip_serv.tipo = 2
                     ) AND COALESCE(pr.total_servicos_sem_desconto, 0) = 0 THEN \'proposta.total_servicos_sem_desconto\' END,
                     CASE WHEN EXISTS (
                         SELECT 1
                           FROM itens_propostas ip_geral
                          WHERE ip_geral.propostas_id = pr.id
                            AND ip_geral.deleted_at IS NULL
                     ) AND COALESCE(pr.total_geral_com_desconto, 0) = 0 THEN \'proposta.total_geral_com_desconto\' END,
                     CASE WHEN COALESCE(pr.total_geral_sem_desconto, 0) = 0 THEN \'proposta.total_geral_sem_desconto\' END,
                     CASE WHEN COALESCE(pr.desconto_contratual, 0) = 0 THEN \'proposta.desconto_contratual\' END,
                     CASE WHEN EXISTS (
                         SELECT 1
                           FROM conta c_valor
                          WHERE c_valor.pedido_frotas_id = p.id
                            AND c_valor.deleted_at IS NULL
                            AND COALESCE(c_valor.valor, 0) = 0
                     ) THEN \'conta.valor\' END,
                     CASE WHEN EXISTS (
                         SELECT 1
                           FROM conta c_txcontrato
                          WHERE c_txcontrato.pedido_frotas_id = p.id
                            AND c_txcontrato.deleted_at IS NULL
                            AND COALESCE(c_txcontrato.valor_txcontrato, 0) = 0
                     ) THEN \'conta.valor_txcontrato\' END,
                     CASE WHEN EXISTS (
                         SELECT 1
                           FROM conta c_liquido
                          WHERE c_liquido.pedido_frotas_id = p.id
                            AND c_liquido.deleted_at IS NULL
                            AND COALESCE(c_liquido.valor_liquido, 0) = 0
                     ) THEN \'conta.valor_liquido\' END,
                     CASE WHEN EXISTS (
                         SELECT 1
                           FROM conta c_total_liq
                          WHERE c_total_liq.pedido_frotas_id = p.id
                            AND c_total_liq.deleted_at IS NULL
                            AND COALESCE(c_total_liq.valor_total_liq_tx_conta, 0) = 0
                     ) THEN \'conta.valor_total_liq_tx_conta\' END
                 ) AS campos_pendentes'
            ) . ' ORDER BY p.id DESC LIMIT 50');

            self::bindAuditParams($stmt);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            TTransaction::close();

            return $rows ?: [];
        } catch (Exception $e) {
            TTransaction::rollback();
            return [];
        }
    }

    private static function getAuditSql(string $select): string
    {
        return "
            SELECT {$select}
              FROM pedido_frotas p
              JOIN propostas pr ON pr.pedido_frotas_id = p.id
              LEFT JOIN estado_pedido_frotas ep ON ep.id = p.estado_pedido_frotas_id
              LEFT JOIN estado_pedido_frotas epr ON epr.id = pr.estado_pedido_frotas_id
             WHERE p.deleted_at IS NULL
               AND pr.deleted_at IS NULL
               AND p.system_unit_id = :system_unit_id
               AND p.estado_pedido_frotas_id IN (:estado_auditoria_1, :estado_auditoria_2, :estado_auditoria_3, :estado_auditoria_4)
               AND (
                   COALESCE(p.valor_total, 0) = 0
                   OR COALESCE(p.valor_total_proposta, 0) = 0
                   OR (:taxa_contrato <> 0 AND COALESCE(p.valor_desconto_proposta, 0) = 0)
                   OR COALESCE(p.valor_liquido_proposta, 0) = 0
                   OR COALESCE(pr.valor_total, 0) = 0
                   OR COALESCE(pr.valor_desconto, 0) = 0
                   OR COALESCE(pr.valor_liquido, 0) = 0
                   OR (
                       EXISTS (
                           SELECT 1
                             FROM itens_propostas ip_prod
                            WHERE ip_prod.propostas_id = pr.id
                              AND ip_prod.deleted_at IS NULL
                              AND ip_prod.tipo = 1
                       )
                       AND COALESCE(pr.total_produtos_sem_desconto, 0) = 0
                   )
                   OR (
                       EXISTS (
                           SELECT 1
                             FROM itens_propostas ip_serv
                            WHERE ip_serv.propostas_id = pr.id
                              AND ip_serv.deleted_at IS NULL
                              AND ip_serv.tipo = 2
                       )
                       AND COALESCE(pr.total_servicos_sem_desconto, 0) = 0
                   )
                   OR (
                       EXISTS (
                           SELECT 1
                             FROM itens_propostas ip_geral
                            WHERE ip_geral.propostas_id = pr.id
                              AND ip_geral.deleted_at IS NULL
                       )
                       AND COALESCE(pr.total_geral_com_desconto, 0) = 0
                   )
                   OR COALESCE(pr.total_geral_sem_desconto, 0) = 0
                   OR COALESCE(pr.desconto_contratual, 0) = 0
                   OR EXISTS (
                       SELECT 1
                         FROM conta c
                        WHERE c.pedido_frotas_id = p.id
                          AND c.deleted_at IS NULL
                          AND (
                              COALESCE(c.valor, 0) = 0
                              OR COALESCE(c.valor_txcontrato, 0) = 0
                              OR COALESCE(c.valor_liquido, 0) = 0
                              OR COALESCE(c.valor_total_liq_tx_conta, 0) = 0
                          )
                   )
               )";
    }

    private static function bindAuditParams(PDOStatement $stmt): void
    {
        $stmt->bindValue(':system_unit_id', (int) TSession::getValue('idunit'), PDO::PARAM_INT);
        $stmt->bindValue(':taxa_contrato', self::getTaxaContrato());

        foreach (self::ESTADOS_AUDITORIA as $index => $estadoId) {
            $stmt->bindValue(':estado_auditoria_' . ($index + 1), $estadoId, PDO::PARAM_INT);
        }
    }

    private static function getTaxaContrato(): float
    {
        $taxaContrato = TSession::getValue('taxacontrato');

        if (is_string($taxaContrato) && strpos($taxaContrato, ',') !== false) {
            $taxaContrato = str_replace('.', '', $taxaContrato);
            $taxaContrato = str_replace(',', '.', $taxaContrato);
        }

        return (float) $taxaContrato;
    }

    private function buildSummary(array $pendencias): TElement
    {
        $summary = new TElement('div');
        $summary->style = 'margin-bottom: 12px; padding: 12px 14px; border: 1px solid #f2c94c; background: #fff8dc; color: #5f4600;';

        $count = count($pendencias);
        $summary->add("<strong>{$count} pendencia(s) encontrada(s)</strong><br>");
        $summary->add('Regra teste: pedidos nos estados 13, 8, 20 ou 18 nao podem ter campos financeiros zerados no pedido, proposta ou conta. Totais de produtos/servicos da proposta sao auditados quando existirem itens_propostas correspondentes.');

        return $summary;
    }

    private function buildNotice(string $message): TElement
    {
        $notice = new TElement('div');
        $notice->style = 'margin: 10px; padding: 12px 14px; border: 1px solid #d0d7de; background: #f6f8fa; color: #24292f;';
        $notice->add($message);

        return $notice;
    }

    private function buildDatagrid(array $pendencias): TDataGrid
    {
        $datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $datagrid->style = 'width: 100%';
        $datagrid->setHeight(320);

        $datagrid->addColumn(new TDataGridColumn('pedido_id', 'Pedido', 'center', '80px'));
        $datagrid->addColumn(new TDataGridColumn('proposta_id', 'Proposta', 'center', '90px'));
        $datagrid->addColumn(new TDataGridColumn('descricaopedido', 'Descricao', 'left'));
        $datagrid->addColumn(new TDataGridColumn('pedido_estado', 'Estado pedido', 'left', '160px'));
        $datagrid->addColumn(new TDataGridColumn('proposta_estado', 'Estado proposta', 'left', '160px'));
        $datagrid->addColumn(new TDataGridColumn('campos_pendentes', 'Campos pendentes', 'left', '360px'));
        $datagrid->addColumn(new TDataGridColumn('pedido_valor_total', 'Pedido total', 'right', '120px'));
        $datagrid->addColumn(new TDataGridColumn('pedido_valor_total_proposta', 'Pedido proposta', 'right', '120px'));
        $datagrid->addColumn(new TDataGridColumn('pedido_valor_desconto_proposta', 'Pedido desconto', 'right', '120px'));
        $datagrid->addColumn(new TDataGridColumn('pedido_valor_liquido_proposta', 'Pedido liquido', 'right', '120px'));
        $datagrid->addColumn(new TDataGridColumn('proposta_valor_total', 'Proposta total', 'right', '120px'));
        $datagrid->addColumn(new TDataGridColumn('proposta_valor_desconto', 'Proposta desconto', 'right', '120px'));
        $datagrid->addColumn(new TDataGridColumn('proposta_valor_liquido', 'Proposta liquido', 'right', '120px'));

        $action = new TDataGridAction(['PedidoFrotasForm', 'onEdit'], ['id' => '{pedido_id}', 'editando' => '1']);
        $action->setLabel('Abrir pedido');
        $action->setImage('fas:external-link-alt #1f6feb');
        $action->setField('pedido_id');
        $datagrid->addAction($action);

        $datagrid->createModel();

        foreach ($pendencias as $pendencia) {
            $pendencia->pedido_valor_total = number_format((float) $pendencia->pedido_valor_total, 2, ',', '.');
            $pendencia->pedido_valor_total_proposta = number_format((float) $pendencia->pedido_valor_total_proposta, 2, ',', '.');
            $pendencia->pedido_valor_desconto_proposta = number_format((float) $pendencia->pedido_valor_desconto_proposta, 2, ',', '.');
            $pendencia->pedido_valor_liquido_proposta = number_format((float) $pendencia->pedido_valor_liquido_proposta, 2, ',', '.');
            $pendencia->proposta_valor_total = number_format((float) $pendencia->proposta_valor_total, 2, ',', '.');
            $pendencia->proposta_valor_desconto = number_format((float) $pendencia->proposta_valor_desconto, 2, ',', '.');
            $pendencia->proposta_valor_liquido = number_format((float) $pendencia->proposta_valor_liquido, 2, ',', '.');
            $datagrid->addItem($pendencia);
        }

        return $datagrid;
    }
}
