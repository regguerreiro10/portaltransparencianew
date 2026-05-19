<?php

declare(strict_types=1);

/**
 * Cria dotacoes para pedidos de frotas sem dotacao da unidade 26.
 *
 * Por seguranca, roda em simulacao por padrao.
 *
 * Uso:
 *   php app/scripts/dotar_pedidos_frotas_sem_dotacao_unit26.php
 *   php app/scripts/dotar_pedidos_frotas_sem_dotacao_unit26.php --apply
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Este script deve ser executado via CLI.\n");
    exit(1);
}

$apply = in_array('--apply', $argv, true);
$unitId = 26;

$configFile = __DIR__ . '/../config/minierp.php';
if (!file_exists($configFile)) {
    $configFile = __DIR__ . '/../config/conexao.php';
}

if (!file_exists($configFile)) {
    fwrite(STDERR, "Arquivo de configuracao nao encontrado.\n");
    exit(1);
}

$config = require $configFile;
if (!is_array($config)) {
    fwrite(STDERR, "Configuracao invalida em {$configFile}.\n");
    exit(1);
}

$host = $config['host'] ?? 'localhost';
$db   = $config['name'] ?? ($config['db'] ?? null);
$user = $config['user'] ?? null;
$pass = $config['pass'] ?? ($config['password'] ?? '');

if (empty($db) || empty($user)) {
    fwrite(STDERR, "Parametros de conexao incompletos.\n");
    exit(1);
}

$dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

const STATUS_FINALIZADO = 8;
const STATUS_APROVADO = 13;
const STATUS_PGTO_APROVADO = 18;
const STATUS_ENTREGUE = 20;
const STATUS_SALDO_ANULADO = 4;

try {
    $pdo = new PDO($dsn, $user, (string) $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->beginTransaction();

    $saldos = carregarSaldosDisponiveis($pdo, $unitId);
    $pedidos = carregarPedidosSemDotacao($pdo, $unitId);

    $stmtInsert = $pdo->prepare(
        "INSERT INTO dotacao_pedido_frotas
            (created_at, updated_at, pedido_frotas_id, saldo_departamento_id, valor, saldo_atual, propostas_id)
         VALUES
            (NOW(), NOW(), :pedido_frotas_id, :saldo_departamento_id, :valor, :saldo_atual, NULL)"
    );

    $resumo = [
        'pedidos_analisados' => count($pedidos),
        'pedidos_dotados' => 0,
        'dotacoes_inseridas' => 0,
        'valor_dotado_centavos' => 0,
        'pedidos_sem_itens' => 0,
        'pedidos_sem_saldo' => 0,
        'pedidos_com_erro' => 0,
    ];

    foreach ($pedidos as $pedido) {
        $pedidoId = (int) $pedido['id'];
        $departamentoId = (int) $pedido['departamento_unit_id'];
        $valorLiquidoCents = moneyToCents($pedido['valor_liquido_proposta']);

        $valoresPorTipo = calcularValoresPorTipo($pdo, $pedidoId, $valorLiquidoCents);
        if (empty($valoresPorTipo)) {
            $resumo['pedidos_sem_itens']++;
            imprimirLinha("SKIP", $pedidoId, "sem itens com valor para separar produto/servico");
            continue;
        }

        $alocacoesPedido = [];
        $erroPedido = false;

        foreach ($valoresPorTipo as $tipo => $valorTipoCents) {
            if ($valorTipoCents <= 0) {
                continue;
            }

            $alocacoesTipo = alocarEmSaldos($saldos, $departamentoId, $tipo, $valorTipoCents);

            if (sumCents($alocacoesTipo, 'valor_centavos') !== $valorTipoCents) {
                $erroPedido = true;
                $resumo['pedidos_sem_saldo']++;
                imprimirLinha(
                    "SKIP",
                    $pedidoId,
                    "saldo insuficiente para tipo {$tipo}; necessario " . formatMoney($valorTipoCents)
                );
                break;
            }

            foreach ($alocacoesTipo as $alocacao) {
                $alocacoesPedido[] = $alocacao;
            }
        }

        if ($erroPedido || empty($alocacoesPedido)) {
            continue;
        }

        foreach ($alocacoesPedido as $alocacao) {
            if ($apply) {
                $stmtInsert->execute([
                    ':pedido_frotas_id' => $pedidoId,
                    ':saldo_departamento_id' => $alocacao['saldo_id'],
                    ':valor' => centsToDecimal($alocacao['valor_centavos']),
                    ':saldo_atual' => centsToDecimal($alocacao['saldo_atual_centavos']),
                ]);
            }

            $resumo['dotacoes_inseridas']++;
            $resumo['valor_dotado_centavos'] += $alocacao['valor_centavos'];
            imprimirLinha(
                $apply ? "OK" : "DRY",
                $pedidoId,
                "saldo {$alocacao['saldo_id']} tipo {$alocacao['tipo']} valor " . formatMoney($alocacao['valor_centavos'])
            );
        }

        $resumo['pedidos_dotados']++;
    }

    if ($apply) {
        $pdo->commit();
    } else {
        $pdo->rollBack();
    }

    fwrite(STDOUT, "\nResumo:\n");
    fwrite(STDOUT, "Modo: " . ($apply ? "APLICADO" : "SIMULACAO") . "\n");
    fwrite(STDOUT, "Unidade: {$unitId}\n");
    fwrite(STDOUT, "Pedidos analisados: {$resumo['pedidos_analisados']}\n");
    fwrite(STDOUT, "Pedidos dotados: {$resumo['pedidos_dotados']}\n");
    fwrite(STDOUT, "Dotacoes " . ($apply ? "inseridas" : "simuladas") . ": {$resumo['dotacoes_inseridas']}\n");
    fwrite(STDOUT, "Valor dotado: " . formatMoney($resumo['valor_dotado_centavos']) . "\n");
    fwrite(STDOUT, "Pedidos sem itens: {$resumo['pedidos_sem_itens']}\n");
    fwrite(STDOUT, "Pedidos sem saldo: {$resumo['pedidos_sem_saldo']}\n");

    if (!$apply) {
        fwrite(STDOUT, "\nNada foi gravado. Para aplicar, execute com --apply.\n");
    }

    exit(0);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, "Erro: {$e->getMessage()}\n");
    exit(1);
}

function carregarPedidosSemDotacao(PDO $pdo, int $unitId): array
{
    $sql = "
        SELECT
            pf.id,
            pf.departamento_unit_id,
            pf.valor_liquido_proposta
        FROM pedido_frotas pf
        WHERE pf.system_unit_id = :unit_id
          AND pf.deleted_at IS NULL
          AND pf.departamento_unit_id IS NOT NULL
          AND pf.estado_pedido_frotas_id IN (:aprovado, :finalizado, :entregue, :pgto_aprovado)
          AND COALESCE(pf.valor_liquido_proposta, 0) > 0
          AND NOT EXISTS (
              SELECT 1
              FROM dotacao_pedido_frotas dpf
              WHERE dpf.pedido_frotas_id = pf.id
                AND dpf.deleted_at IS NULL
          )
        ORDER BY pf.departamento_unit_id, pf.id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':unit_id' => $unitId,
        ':aprovado' => STATUS_APROVADO,
        ':finalizado' => STATUS_FINALIZADO,
        ':entregue' => STATUS_ENTREGUE,
        ':pgto_aprovado' => STATUS_PGTO_APROVADO,
    ]);

    return $stmt->fetchAll();
}

function carregarSaldosDisponiveis(PDO $pdo, int $unitId): array
{
    $sql = "
        SELECT
            sd.id,
            sd.departamento_unit_id,
            sd.tipo,
            CASE
                WHEN sd.tipo = 'P' THEN COALESCE(NULLIF(sd.saldo_produto, 0), sd.saldo_total, 0)
                WHEN sd.tipo = 'S' THEN COALESCE(NULLIF(sd.saldo_servico, 0), sd.saldo_total, 0)
                ELSE COALESCE(sd.saldo_total, 0)
            END AS valor_empenho,
            COALESCE((
                SELECT SUM(dpf.valor)
                FROM dotacao_pedido_frotas dpf
                INNER JOIN pedido_frotas pf ON pf.id = dpf.pedido_frotas_id
                WHERE dpf.saldo_departamento_id = sd.id
                  AND dpf.deleted_at IS NULL
                  AND pf.deleted_at IS NULL
                  AND pf.system_unit_id = :unit_id_usado
                  AND pf.estado_pedido_frotas_id <> 9
            ), 0) AS valor_usado
        FROM saldo_departamento sd
        INNER JOIN departamento_unit du ON du.id = sd.departamento_unit_id
        WHERE du.system_unit_id = :unit_id
          AND sd.deleted_at IS NULL
          AND sd.tipotransacao = 'C'
          AND sd.status_saldo_departamento_id <> :status_anulado
          AND sd.tipo IN ('P', 'S')
        ORDER BY sd.departamento_unit_id, sd.tipo, sd.datatransacao, sd.id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':unit_id_usado' => $unitId,
        ':unit_id' => $unitId,
        ':status_anulado' => STATUS_SALDO_ANULADO,
    ]);

    $saldos = [];
    foreach ($stmt->fetchAll() as $row) {
        $key = saldoKey((int) $row['departamento_unit_id'], (string) $row['tipo']);
        $valorEmpenhoCents = moneyToCents($row['valor_empenho']);
        $valorUsadoCents = moneyToCents($row['valor_usado']);
        $disponivelCents = $valorEmpenhoCents - $valorUsadoCents;

        if ($disponivelCents <= 0) {
            continue;
        }

        $saldos[$key][] = [
            'id' => (int) $row['id'],
            'tipo' => (string) $row['tipo'],
            'disponivel_centavos' => $disponivelCents,
        ];
    }

    return $saldos;
}

function calcularValoresPorTipo(PDO $pdo, int $pedidoId, int $valorLiquidoCents): array
{
    $stmt = $pdo->prepare(
        "SELECT tipo, SUM(valor_total) AS total
         FROM itens_pedido_frotas
         WHERE pedido_frotas_id = :pedido_id
           AND deleted_at IS NULL
           AND tipo IN (1, 2)
         GROUP BY tipo"
    );
    $stmt->execute([':pedido_id' => $pedidoId]);

    $totaisItens = [];
    $totalItensCents = 0;

    foreach ($stmt->fetchAll() as $row) {
        $tipoSaldo = ((int) $row['tipo'] === 1) ? 'P' : 'S';
        $totalCents = moneyToCents($row['total']);

        if ($totalCents <= 0) {
            continue;
        }

        $totaisItens[$tipoSaldo] = ($totaisItens[$tipoSaldo] ?? 0) + $totalCents;
        $totalItensCents += $totalCents;
    }

    if ($totalItensCents <= 0 || $valorLiquidoCents <= 0) {
        return [];
    }

    $valores = [];
    $tipos = array_keys($totaisItens);
    $acumulado = 0;

    foreach ($tipos as $index => $tipo) {
        if ($index === count($tipos) - 1) {
            $valorTipo = $valorLiquidoCents - $acumulado;
        } else {
            $valorTipo = (int) round($valorLiquidoCents * ($totaisItens[$tipo] / $totalItensCents));
            $acumulado += $valorTipo;
        }

        if ($valorTipo > 0) {
            $valores[$tipo] = $valorTipo;
        }
    }

    return $valores;
}

function alocarEmSaldos(array &$saldos, int $departamentoId, string $tipo, int $valorCents): array
{
    $key = saldoKey($departamentoId, $tipo);
    $restante = $valorCents;
    $alocacoes = [];

    if (empty($saldos[$key])) {
        return [];
    }

    foreach ($saldos[$key] as &$saldo) {
        if ($restante <= 0) {
            break;
        }

        if ($saldo['disponivel_centavos'] <= 0) {
            continue;
        }

        $valorAlocar = min($restante, $saldo['disponivel_centavos']);
        $saldo['disponivel_centavos'] -= $valorAlocar;
        $restante -= $valorAlocar;

        $alocacoes[] = [
            'saldo_id' => $saldo['id'],
            'tipo' => $tipo,
            'valor_centavos' => $valorAlocar,
            'saldo_atual_centavos' => $saldo['disponivel_centavos'],
        ];
    }
    unset($saldo);

    if ($restante > 0) {
        foreach ($alocacoes as $alocacao) {
            foreach ($saldos[$key] as &$saldoRollback) {
                if ($saldoRollback['id'] === $alocacao['saldo_id']) {
                    $saldoRollback['disponivel_centavos'] += $alocacao['valor_centavos'];
                    break;
                }
            }
            unset($saldoRollback);
        }

        return [];
    }

    return $alocacoes;
}

function saldoKey(int $departamentoId, string $tipo): string
{
    return $departamentoId . '|' . $tipo;
}

function sumCents(array $rows, string $field): int
{
    $sum = 0;
    foreach ($rows as $row) {
        $sum += (int) $row[$field];
    }

    return $sum;
}

function moneyToCents($value): int
{
    return (int) round(((float) $value) * 100);
}

function centsToDecimal(int $cents): string
{
    return number_format($cents / 100, 2, '.', '');
}

function formatMoney(int $cents): string
{
    return 'R$ ' . number_format($cents / 100, 2, ',', '.');
}

function imprimirLinha(string $status, int $pedidoId, string $mensagem): void
{
    fwrite(STDOUT, "[{$status}] Pedido {$pedidoId}: {$mensagem}\n");
}
