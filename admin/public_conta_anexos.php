<?php

header('Content-Type: application/json; charset=utf-8');

try {
    $contaId = filter_input(INPUT_GET, 'conta_id', FILTER_VALIDATE_INT);

    if (!$contaId) {
        throw new InvalidArgumentException('Conta nao informada.');
    }

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

    $stmt = $pdo->prepare(
        "SELECT ca.id,
                ca.descricao,
                ca.arquivo,
                ca.created_at,
                COALESCE(ta.nome, '') AS tipo_anexo
         FROM conta_anexo ca
         LEFT JOIN tipo_anexo ta ON ta.id = ca.tipo_anexo_id
         WHERE ca.conta_id = :conta_id
         ORDER BY ca.created_at DESC, ca.id DESC"
    );
    $stmt->execute(['conta_id' => $contaId]);

    $items = array_map(static function (array $row): array {
        $file = ltrim(str_replace('\\', '/', (string) $row['arquivo']), '/');
        $description = trim((string) $row['descricao']);
        $type = trim((string) $row['tipo_anexo']);

        return [
            'id' => (int) $row['id'],
            'file_name' => basename($file),
            'description' => $description !== '' ? $description : $type,
            'url' => 'admin/' . implode('/', array_map('rawurlencode', explode('/', $file))),
            'created_at' => $row['created_at'],
        ];
    }, $stmt->fetchAll());

    echo json_encode([
        'items' => $items,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
