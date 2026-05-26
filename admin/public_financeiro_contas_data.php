<?php

function output_financeiro_contas_data(int $defaultTipoContaId, string $listClass, string $formClass): void
{
    header('Content-Type: application/json; charset=utf-8');

    try {
        $config = require __DIR__ . '/app/config/minierp.php';

        $pdo = new PDO(
            "mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4",
            $config['user'],
            $config['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        $tipoContaId = filter_input(INPUT_GET, 'tipo_conta_id', FILTER_VALIDATE_INT) ?: $defaultTipoContaId;
        $period = trim((string) ($_GET['period'] ?? ''));
        $where = ['c.tipo_conta_id = :tipo_conta_id', 'c.deleted_at IS NULL'];
        $params = ['tipo_conta_id' => $tipoContaId];

        if ($period !== '' && preg_match('/^\d{4}-\d{2}$/', $period)) {
            $where[] = "DATE_FORMAT(COALESCE(c.dt_vencimento, c.dt_emissao, c.created_at), '%Y-%m') = :period";
            $params['period'] = $period;
        }

        $whereSql = implode(' AND ', $where);
        $valueExpression = 'COALESCE(c.valor, c.valor_liquido, c.valor_total_liq_tx_conta, 0)';

        $totalStmt = $pdo->prepare("SELECT COALESCE(SUM({$valueExpression}), 0) FROM conta c WHERE {$whereSql}");
        $totalStmt->execute($params);
        $total = (float) $totalStmt->fetchColumn();

        $stmt = $pdo->prepare(
            "SELECT c.id,
                    c.descricao,
                    c.dt_vencimento,
                    c.dt_emissao,
                    {$valueExpression} AS valor,
                    COALESCE(
                        NULLIF((
                            SELECT cat.nome
                            FROM categoria cat
                            WHERE cat.id = c.categoria_id
                              AND cat.tipo_conta_id = c.tipo_conta_id
                            ORDER BY cat.id
                            LIMIT 1
                        ), ''),
                        'Sem categoria'
                    ) AS categoria,
                    COALESCE((
                        SELECT p.nome
                        FROM pessoa p
                        WHERE p.id = c.pessoa_id
                        ORDER BY p.id
                        LIMIT 1
                    ), '') AS pessoa
             FROM conta c
             WHERE {$whereSql}
             ORDER BY COALESCE(c.dt_vencimento, c.dt_emissao, c.created_at) DESC, c.id DESC
             LIMIT 200"
        );
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $items = array_map(static function (array $row) use ($total): array {
            $value = (float) $row['valor'];
            $date = $row['dt_vencimento'] ?: $row['dt_emissao'];
            $category = trim((string) $row['categoria']) ?: 'Sem categoria';
            $description = trim((string) $row['descricao']);

            return [
                'id' => (int) $row['id'],
                'label' => $description !== '' ? $description : $category,
                'category' => $category,
                'client' => $row['pessoa'],
                'supplier' => $row['pessoa'],
                'due_date' => $date ? date('d/m/Y', strtotime($date)) : null,
                'value' => $value,
                'percentage' => $total > 0 ? ($value / $total) * 100 : 0,
            ];
        }, $rows);

        $periodStmt = $pdo->prepare(
            "SELECT DISTINCT DATE_FORMAT(COALESCE(c.dt_vencimento, c.dt_emissao, c.created_at), '%Y-%m') AS value
             FROM conta c
             WHERE c.tipo_conta_id = :tipo_conta_id
               AND c.deleted_at IS NULL
               AND COALESCE(c.dt_vencimento, c.dt_emissao, c.created_at) IS NOT NULL
             ORDER BY value DESC"
        );
        $periodStmt->execute(['tipo_conta_id' => $tipoContaId]);
        $periods = array_map(static function (string $value): array {
            [$year, $month] = explode('-', $value);

            return [
                'value' => $value,
                'label' => "{$month}/{$year}",
            ];
        }, $periodStmt->fetchAll(PDO::FETCH_COLUMN));

        echo json_encode([
            'total' => $total,
            'period' => $period,
            'periods' => $periods,
            'items' => $items,
            'links' => [
                'list' => "admin/index.php?class={$listClass}",
                'create' => "admin/index.php?class={$formClass}",
            ],
        ], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
    }
}
