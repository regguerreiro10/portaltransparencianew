<?php

header('Content-Type: application/json; charset=utf-8');

$config = require __DIR__ . '/admin/app/config/minierp.php';

$response = [
    'items' => [],
];

function gallery_public_path(?string $path, string $fallback = ''): string
{
    $path = trim((string) $path);

    if ($path === '') {
        return $fallback;
    }

    if (preg_match('/^(?:https?:)?\/\//i', $path) || strpos($path, 'assets/') === 0 || strpos($path, 'admin/') === 0 || strpos($path, 'video/') === 0) {
        return $path;
    }

    return 'admin/' . ltrim($path, '/');
}

try {
    $dsn = sprintf('%s:host=%s;dbname=%s;charset=utf8mb4', $config['type'], $config['host'], $config['name']);
    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS galeria_item (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            titulo VARCHAR(255) NOT NULL,
            descricao TEXT NULL,
            tipo VARCHAR(20) NOT NULL DEFAULT 'foto',
            arquivo VARCHAR(255) NULL,
            imagem_capa VARCHAR(255) NULL,
            texto_alternativo VARCHAR(255) NULL,
            ordem INT NOT NULL DEFAULT 0,
            status VARCHAR(20) NOT NULL DEFAULT 'published',
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY (id),
            KEY idx_galeria_tipo_status_ordem (tipo, status, ordem),
            KEY idx_galeria_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $tipo = isset($_GET['tipo']) && is_scalar($_GET['tipo']) ? trim((string) $_GET['tipo']) : '';
    $where = ["status = 'published'"];
    $params = [];

    if (in_array($tipo, ['foto', 'video', 'audio'], true)) {
        $where[] = 'tipo = :tipo';
        $params[':tipo'] = $tipo;
    }

    $stmt = $pdo->prepare(
        "SELECT id, titulo, descricao, tipo, arquivo, imagem_capa, texto_alternativo, ordem
         FROM galeria_item
         WHERE " . implode(' AND ', $where) . "
         ORDER BY tipo ASC, ordem ASC, id DESC"
    );
    $stmt->execute($params);
    $items = $stmt->fetchAll();

    foreach ($items as &$item) {
        $arquivo = gallery_public_path($item['arquivo'] ?? '');
        $imagemCapa = gallery_public_path($item['imagem_capa'] ?? '');

        $item['arquivo'] = $arquivo !== '' ? $arquivo : $imagemCapa;
        $item['imagem_capa'] = $imagemCapa !== '' ? $imagemCapa : 'assets/img/bg/breadcrumb_bg.jpg';
        $item['descricao'] = (string) ($item['descricao'] ?? '');
        $item['texto_alternativo'] = (string) ($item['texto_alternativo'] ?: $item['titulo']);
        $item['ordem'] = (int) ($item['ordem'] ?? 0);
    }
    unset($item);

    $response['items'] = array_values(array_filter($items, static function ($item) {
        return $item['tipo'] === 'foto' || $item['arquivo'] !== '';
    }));
} catch (Throwable $e) {
    http_response_code(500);
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
