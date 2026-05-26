<?php

class DocumentoPublicoSchemaHelper
{
    public static function ensureSchema(): void
    {
        $conn = TTransaction::get();
        self::executeStatements($conn);
    }

    public static function ensureSchemaWithPdo(PDO $pdo): void
    {
        self::executeStatements($pdo);
    }

    private static function executeStatements($conn): void
    {
        $conn->exec(
            "CREATE TABLE IF NOT EXISTS documento_publico (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                numero_documento VARCHAR(80) NOT NULL,
                tipo VARCHAR(80) NOT NULL,
                data_documento DATE NOT NULL,
                assunto VARCHAR(255) NOT NULL,
                nome VARCHAR(255) NOT NULL,
                orgao VARCHAR(255) NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'published',
                documento_publico_tipo_id INT NULL,
                documento_publico_status_id INT NULL,
                system_unit_id INT NULL,
                system_users_id INT NULL,
                entidade_id INT NULL,
                departamento_unit_id INT NULL,
                downloads INT UNSIGNED NOT NULL DEFAULT 0,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                deleted_at DATETIME NULL,
                PRIMARY KEY (id),
                KEY idx_documento_publico_tipo (tipo),
                KEY idx_documento_publico_data (data_documento),
                KEY idx_documento_publico_status (status),
                KEY idx_documento_publico_unit (system_unit_id),
                KEY idx_documento_publico_user (system_users_id),
                KEY idx_documento_publico_departamento (departamento_unit_id),
                KEY idx_documento_publico_entidade (entidade_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        self::ensureColumn($conn, 'documento_publico', 'tipo', "VARCHAR(80) NOT NULL DEFAULT ''");
        self::ensureColumn($conn, 'documento_publico', 'status', "VARCHAR(20) NOT NULL DEFAULT 'published'");
        self::ensureColumn($conn, 'documento_publico', 'nome', "VARCHAR(255) NOT NULL DEFAULT ''");
        self::ensureColumn($conn, 'documento_publico', 'orgao', "VARCHAR(255) NOT NULL DEFAULT ''");
        self::ensureColumn($conn, 'documento_publico', 'documento_publico_tipo_id', 'INT NULL');
        self::ensureColumn($conn, 'documento_publico', 'documento_publico_status_id', 'INT NULL');
        self::ensureColumn($conn, 'documento_publico', 'system_unit_id', 'INT NULL');
        self::ensureColumn($conn, 'documento_publico', 'system_users_id', 'INT NULL');
        self::ensureColumn($conn, 'documento_publico', 'entidade_id', 'INT NULL');
        self::ensureColumn($conn, 'documento_publico', 'departamento_unit_id', 'INT NULL');
        self::ensureColumn($conn, 'documento_publico', 'deleted_at', 'DATETIME NULL');
        self::ensureColumnType($conn, 'documento_publico', 'departamento_unit_id', 'INT NULL');
        self::migrateLegacyTipo($conn);
        self::migrateSessionContext($conn);

        $conn->exec(
            "CREATE TABLE IF NOT EXISTS documento_publico_anexo (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                documento_publico_id INT UNSIGNED NOT NULL,
                nome VARCHAR(255) NOT NULL,
                arquivo VARCHAR(255) NOT NULL,
                ordem INT UNSIGNED NOT NULL DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                deleted_at DATETIME NULL,
                PRIMARY KEY (id),
                KEY idx_documento_publico_anexo_documento (documento_publico_id),
                CONSTRAINT fk_documento_publico_anexo_documento
                    FOREIGN KEY (documento_publico_id) REFERENCES documento_publico(id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        self::ensureColumn($conn, 'documento_publico_anexo', 'updated_at', 'DATETIME NULL');
        self::ensureColumn($conn, 'documento_publico_anexo', 'deleted_at', 'DATETIME NULL');
    }

    private static function ensureColumn($conn, string $table, string $column, string $definition): void
    {
        if (!self::columnExists($conn, $table, $column)) {
            $conn->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
        }
    }

    private static function ensureColumnType($conn, string $table, string $column, string $definition): void
    {
        $info = self::getColumnInfo($conn, $table, $column);
        if ($info && strtolower((string) $info['Type']) !== strtolower(strtok($definition, ' '))) {
            $conn->exec("ALTER TABLE {$table} MODIFY COLUMN {$column} {$definition}");
        }
    }

    private static function columnExists($conn, string $table, string $column): bool
    {
        $stmt = $conn->prepare("SHOW COLUMNS FROM {$table} LIKE :column_name");
        $stmt->execute(['column_name' => $column]);

        return (bool) $stmt->fetch();
    }

    private static function getColumnInfo($conn, string $table, string $column)
    {
        $stmt = $conn->prepare("SHOW COLUMNS FROM {$table} LIKE :column_name");
        $stmt->execute(['column_name' => $column]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private static function migrateLegacyTipo($conn): void
    {
        if (!self::tableExists($conn, 'tabela_de_tabela')) {
            return;
        }

        $conn->exec(
            "UPDATE documento_publico dp
             INNER JOIN tabela_de_tabela tdt ON tdt.descricao = dp.tipo AND tdt.tabela_id = 1
             SET dp.documento_publico_tipo_id = tdt.id
             WHERE dp.documento_publico_tipo_id IS NULL
                OR dp.documento_publico_tipo_id = 0"
        );
    }

    private static function migrateSessionContext($conn): void
    {
        if (!class_exists('TSession')) {
            return;
        }

        $unitId = TSession::getValue('idunit') ?: TSession::getValue('userunitid');
        $entidadeId = TSession::getValue('entidade_id') ?: TSession::getValue('entidade');

        if ($unitId) {
            $stmt = $conn->prepare(
                'UPDATE documento_publico
                    SET system_unit_id = :system_unit_id
                  WHERE system_unit_id IS NULL OR system_unit_id = 0'
            );
            $stmt->execute(['system_unit_id' => $unitId]);
        }

        if ($entidadeId) {
            $stmt = $conn->prepare(
                'UPDATE documento_publico
                    SET entidade_id = :entidade_id
                  WHERE entidade_id IS NULL OR entidade_id = 0'
            );
            $stmt->execute(['entidade_id' => $entidadeId]);
        }
    }

    private static function tableExists($conn, string $table): bool
    {
        $stmt = $conn->prepare('SHOW TABLES LIKE :table_name');
        $stmt->execute(['table_name' => $table]);

        return (bool) $stmt->fetch();
    }
}
