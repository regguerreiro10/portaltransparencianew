<?php

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

    require_once __DIR__ . '/app/control/documentos_publicos/DocumentoPublicoSchemaHelper.php';
    DocumentoPublicoSchemaHelper::ensureSchemaWithPdo($pdo);

    $where = ["d.status = 'published'"];
    $params = [];

    $textFilters = [
        'number' => ['expression' => 'd.numero_documento LIKE :number', 'param' => 'number'],
        'subject' => ['expression' => 'd.assunto LIKE :subject', 'param' => 'subject'],
        'name' => ['expression' => 'COALESCE(su.name, d.nome) LIKE :name', 'param' => 'name'],
    ];

    foreach ($textFilters as $requestKey => $filter) {
        $value = trim($_GET[$requestKey] ?? '');
        if ($value !== '') {
            $where[] = $filter['expression'];
            $params[$filter['param']] = "%{$value}%";
        }
    }

    $type = trim($_GET['type'] ?? '');
    if ($type !== '') {
        if (ctype_digit($type)) {
            $where[] = 'd.documento_publico_tipo_id = :type_id';
            $params['type_id'] = (int) $type;
        } else {
            $where[] = 'COALESCE(tdt.descricao, d.tipo) LIKE :type';
            $params['type'] = "%{$type}%";
        }
    }

    $office = trim($_GET['office'] ?? '');
    if ($office !== '') {
        if (ctype_digit($office)) {
            $where[] = 'd.departamento_unit_id = :office_id';
            $params['office_id'] = (int) $office;
        } else {
            $where[] = 'COALESCE(du.name, d.orgao) LIKE :office';
            $params['office'] = "%{$office}%";
        }
    }

    $date = trim($_GET['date'] ?? '');
    if ($date !== '') {
        $where[] = 'd.data_documento = :date';
        $params['date'] = $date;
    }

    $period = trim($_GET['period'] ?? '');
    if ($period !== '') {
        $where[] = 'YEAR(d.data_documento) = :period';
        $params['period'] = (int) $period;
    }

    $downloadMin = trim($_GET['download_min'] ?? '');
    if ($downloadMin !== '') {
        $where[] = 'd.downloads >= :download_min';
        $params['download_min'] = max(0, (int) $downloadMin);
    }

    $attachmentFilter = $_GET['attachment_filter'] ?? '';
    if ($attachmentFilter === 'com-anexos') {
        $where[] = 'EXISTS (SELECT 1 FROM documento_publico_anexo a WHERE a.documento_publico_id = d.id)';
    } elseif ($attachmentFilter === 'sem-anexos') {
        $where[] = 'NOT EXISTS (SELECT 1 FROM documento_publico_anexo a WHERE a.documento_publico_id = d.id)';
    }

    $order = 'd.data_documento DESC, d.id DESC';
    if (($_GET['sort'] ?? '') === 'downloads') {
        $order = 'd.downloads DESC, d.data_documento DESC';
    } elseif (($_GET['sort'] ?? '') === 'name') {
        $order = 'COALESCE(su.name, d.nome) ASC, d.data_documento DESC';
    }

    $sql = 'SELECT d.id, d.numero_documento,
                   d.documento_publico_tipo_id, d.system_users_id, d.departamento_unit_id,
                   COALESCE(tdt.descricao, d.tipo) AS tipo,
                   d.data_documento, d.assunto,
                   COALESCE(su.name, d.nome) AS nome,
                   COALESCE(du.name, d.orgao) AS orgao,
                   d.downloads
            FROM documento_publico d
            LEFT JOIN tabela_de_tabela tdt ON tdt.id = d.documento_publico_tipo_id
            LEFT JOIN system_users su ON su.id = d.system_users_id
            LEFT JOIN departamento_unit du ON du.id = d.departamento_unit_id
            WHERE ' . implode(' AND ', $where) . "
            ORDER BY {$order}";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $documents = $stmt->fetchAll();

    $ids = array_column($documents, 'id');
    $attachmentsByDocument = [];

    if ($ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $attachmentStmt = $pdo->prepare(
            "SELECT documento_publico_id, nome, arquivo
             FROM documento_publico_anexo
             WHERE documento_publico_id IN ({$placeholders})
             ORDER BY ordem ASC, id ASC"
        );
        $attachmentStmt->execute($ids);

        foreach ($attachmentStmt as $attachment) {
            $file = ltrim(str_replace('\\', '/', $attachment['arquivo']), '/');
            $fileName = trim((string) $attachment['nome']) ?: basename($file);
            $attachmentsByDocument[$attachment['documento_publico_id']][] = [
                'name' => $fileName,
                'download_url' => 'admin/' . implode('/', array_map('rawurlencode', explode('/', $file))),
            ];
        }
    }

    $rows = [];
    $attachmentsTotal = 0;
    $downloadsTotal = 0;

    foreach ($documents as $document) {
        $attachments = $attachmentsByDocument[$document['id']] ?? [];
        $attachmentsTotal += count($attachments);
        $downloadsTotal += (int) $document['downloads'];

        $rows[] = [
            'id' => (int) $document['id'],
            'number' => $document['numero_documento'],
            'type_id' => $document['documento_publico_tipo_id'] ? (int) $document['documento_publico_tipo_id'] : null,
            'user_id' => $document['system_users_id'] ? (int) $document['system_users_id'] : null,
            'department_id' => $document['departamento_unit_id'] ? (int) $document['departamento_unit_id'] : null,
            'type' => $document['tipo'],
            'date' => $document['data_documento'],
            'subject' => $document['assunto'],
            'name' => $document['nome'],
            'office' => $document['orgao'],
            'downloads' => (int) $document['downloads'],
            'attachments' => $attachments,
        ];
    }

    $types = array_map(function ($item) {
        return [
            'value' => $item['id'] ?: $item['label'],
            'label' => $item['label'],
        ];
    }, $pdo->query(
        "SELECT DISTINCT d.documento_publico_tipo_id AS id, COALESCE(tdt.descricao, d.tipo) AS label
         FROM documento_publico d
         LEFT JOIN tabela_de_tabela tdt ON tdt.id = d.documento_publico_tipo_id
         WHERE d.status = 'published' AND COALESCE(tdt.descricao, d.tipo) <> ''
         ORDER BY label"
    )->fetchAll());
    $offices = array_map(function ($item) {
        return [
            'value' => $item['id'] ?: $item['label'],
            'label' => $item['label'],
        ];
    }, $pdo->query(
        "SELECT DISTINCT d.departamento_unit_id AS id, COALESCE(du.name, d.orgao) AS label
         FROM documento_publico d
         LEFT JOIN departamento_unit du ON du.id = d.departamento_unit_id
         WHERE d.status = 'published' AND COALESCE(du.name, d.orgao) <> ''
         ORDER BY label"
    )->fetchAll());
    $periods = $pdo->query("SELECT DISTINCT YEAR(data_documento) AS ano FROM documento_publico WHERE status = 'published' ORDER BY ano DESC")->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'rows' => $rows,
        'summary' => [
            'records' => count($rows),
            'attachments' => $attachmentsTotal,
            'downloads' => $downloadsTotal,
        ],
        'filters' => [
            'types' => $types,
            'offices' => $offices,
            'periods' => $periods,
            'number' => $_GET['number'] ?? '',
            'type' => $_GET['type'] ?? '',
            'date' => $_GET['date'] ?? '',
            'subject' => $_GET['subject'] ?? '',
            'name' => $_GET['name'] ?? '',
            'office' => $_GET['office'] ?? '',
            'period' => $_GET['period'] ?? '',
            'attachment_filter' => $_GET['attachment_filter'] ?? '',
            'download_min' => $_GET['download_min'] ?? '',
            'sort' => $_GET['sort'] ?? 'recent',
        ],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
