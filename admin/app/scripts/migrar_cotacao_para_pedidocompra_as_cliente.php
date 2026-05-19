<?php

declare(strict_types=1);

/**
 * Migra vinculos de redes da tabela cotacao para pedidocompra_as_cliente.
 *
 * Uso:
 *   php app/scripts/migrar_cotacao_para_pedidocompra_as_cliente.php
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Este script deve ser executado via CLI.\n");
    exit(1);
}

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

try {
    $pdo = new PDO($dsn, $user, (string) $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->beginTransaction();

    $sql = "
        INSERT INTO pedidocompra_as_cliente (pedido_id, pessoa_id, created_at, updated_at)
        SELECT DISTINCT
            c.pedido_id,
            c.pessoa_id,
            NOW(),
            NOW()
        FROM cotacao c
        LEFT JOIN pedidocompra_as_cliente pc
            ON pc.pedido_id = c.pedido_id
           AND pc.pessoa_id = c.pessoa_id
           AND pc.deleted_at IS NULL
        WHERE c.pedido_id IS NOT NULL
          AND c.pessoa_id IS NOT NULL
          AND pc.id IS NULL
    ";

    $inserted = $pdo->exec($sql);
    $pdo->commit();

    fwrite(STDOUT, "Migracao concluida com sucesso.\n");
    fwrite(STDOUT, "Registros inseridos: " . (int) $inserted . "\n");
    exit(0);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, "Erro na migracao: {$e->getMessage()}\n");
    exit(1);
}

