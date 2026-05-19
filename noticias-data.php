<?php

header('Content-Type: application/json; charset=utf-8');

$config = require __DIR__ . '/admin/app/config/minierp.php';

$response = [
    'items' => [],
    'categories' => [],
];

function cleanPlainText($value)
{
    $value = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = strip_tags($value);
    $value = str_replace("\xc2\xa0", ' ', $value);
    $value = preg_replace('/\s+/u', ' ', $value);

    return trim((string) $value);
}

try {
    $dsn = sprintf('%s:host=%s;dbname=%s;charset=utf8mb4', $config['type'], $config['host'], $config['name']);
    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS noticia (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            titulo VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            categoria VARCHAR(120) NOT NULL,
            data_publicacao DATE NOT NULL,
            resumo TEXT NULL,
            conteudo MEDIUMTEXT NULL,
            imagem VARCHAR(255) NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'published',
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_noticia_slug (slug),
            KEY idx_noticia_status_data (status, data_publicacao),
            KEY idx_noticia_categoria (categoria)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $where = ["status = 'published'"];
    $params = [];

    $id = isset($_GET['id']) && is_scalar($_GET['id']) ? (int) $_GET['id'] : 0;
    $categoria = isset($_GET['categoria']) && is_scalar($_GET['categoria']) ? trim((string) $_GET['categoria']) : '';

    if ($id > 0) {
        $where[] = 'id = :id';
        $params[':id'] = $id;
    }

    if ($categoria !== '') {
        $where[] = 'categoria = :categoria';
        $params[':categoria'] = $categoria;
    }

    $stmt = $pdo->prepare(
        "SELECT id, titulo, slug, categoria, data_publicacao, resumo, conteudo, imagem
         FROM noticia
         WHERE " . implode(' AND ', $where) . "
         ORDER BY data_publicacao DESC, id DESC"
    );
    $stmt->execute($params);
    $items = $stmt->fetchAll();

    $categoryStmt = $pdo->query(
        "SELECT DISTINCT categoria
         FROM noticia
         WHERE status = 'published' AND categoria <> ''
         ORDER BY categoria ASC"
    );
    $allCategories = array_map(static function ($row) {
        return $row['categoria'];
    }, $categoryStmt->fetchAll());

    foreach ($items as &$item) {
        $image = trim((string) ($item['imagem'] ?? ''));
        if ($image === '') {
            $image = 'assets/img/blog/blog01.jpg';
        } elseif (!preg_match('/^(?:https?:)?\\/\\//i', $image) && strpos($image, 'assets/') !== 0 && strpos($image, 'admin/') !== 0) {
            $image = 'admin/' . ltrim($image, '/');
        }

        $item['imagem'] = $image;
        $item['data_publicacao_formatada'] = !empty($item['data_publicacao']) ? date('d/m/Y', strtotime($item['data_publicacao'])) : '';
        $item['resumo'] = cleanPlainText($item['resumo'] ?? '');
        $item['conteudo'] = (string) ($item['conteudo'] ?? '');
        $plainContent = cleanPlainText($item['conteudo']);
        if (function_exists('mb_substr') && function_exists('mb_strlen')) {
            $item['resumo_curto'] = $item['resumo'] !== ''
                ? $item['resumo']
                : mb_substr($plainContent, 0, 180) . (mb_strlen($plainContent) > 180 ? '...' : '');
        } else {
            $item['resumo_curto'] = $item['resumo'] !== ''
                ? $item['resumo']
                : substr($plainContent, 0, 180) . (strlen($plainContent) > 180 ? '...' : '');
        }
    }
    unset($item);

    $response['items'] = $items;
    $response['categories'] = array_values($allCategories);
} catch (Throwable $e) {
    http_response_code(500);
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
