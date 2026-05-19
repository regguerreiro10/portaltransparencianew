<?php

class MemorandoSchemaHelper
{
    public static function ensureSchema(): void
    {
        self::executeStatements(TTransaction::get());
    }

    private static function executeStatements($conn): void
    {
        $conn->exec(
            "CREATE TABLE IF NOT EXISTS memorando (
                id INT NOT NULL AUTO_INCREMENT,
                numero_memorando VARCHAR(80) NOT NULL,
                system_users_remetente_id INT NULL,
                departamento_unit_id INT NULL,
                status VARCHAR(20) NULL,
                data_memorando DATETIME NOT NULL,
                assunto VARCHAR(255) NOT NULL,
                texto_memorando TEXT NULL,
                tipo VARCHAR(40) NULL,
                template_codigo VARCHAR(80) NULL,
                template_nome VARCHAR(150) NULL,
                pode_virar_processo CHAR(1) NOT NULL DEFAULT 'N',
                processo_referencia VARCHAR(120) NULL,
                downloads INT NOT NULL DEFAULT 0,
                lido_em DATETIME NULL,
                arquivado_em DATETIME NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                deleted_at DATETIME NULL,
                system_unit_id INT NULL,
                entidade_id INT NULL,
                memorando_pai_id INT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_memorando_numero (numero_memorando),
                KEY idx_memorando_status (status),
                KEY idx_memorando_data (data_memorando),
                KEY idx_memorando_remetente (system_users_remetente_id),
                KEY idx_memorando_departamento (departamento_unit_id),
                KEY idx_memorando_unit (system_unit_id),
                KEY idx_memorando_entidade (entidade_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        );

        self::ensureColumn(
            $conn,
            'memorando',
            'system_users_remetente_id',
            "ALTER TABLE memorando ADD system_users_remetente_id INT NULL AFTER numero_memorando"
        );
        self::ensureColumn(
            $conn,
            'memorando',
            'departamento_unit_id',
            "ALTER TABLE memorando ADD departamento_unit_id INT NULL AFTER system_users_remetente_id"
        );
        self::ensureColumn(
            $conn,
            'memorando',
            'system_unit_id',
            "ALTER TABLE memorando ADD system_unit_id INT NULL AFTER deleted_at"
        );
        self::ensureColumn(
            $conn,
            'memorando',
            'entidade_id',
            "ALTER TABLE memorando ADD entidade_id INT NULL AFTER system_unit_id"
        );
        self::ensureColumn(
            $conn,
            'memorando',
            'deleted_at',
            "ALTER TABLE memorando ADD deleted_at DATETIME NULL AFTER updated_at"
        );
        self::migrateLegacyColumns($conn);

        $conn->exec(
            "CREATE TABLE IF NOT EXISTS memorando_destinatario (
                id INT NOT NULL AUTO_INCREMENT,
                memorando_id INT NULL,
                tipo_destino VARCHAR(20) NOT NULL,
                departamento_unit_id INT NULL,
                system_users_id INT NULL,
                status VARCHAR(40) NOT NULL,
                recebido_em DATETIME NULL,
                lido_em DATETIME NULL,
                respondido_em DATETIME NULL,
                arquivado_em DATETIME NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                deleted_at DATETIME NULL,
                PRIMARY KEY (id),
                KEY idx_memorando_destinatario_memorando (memorando_id),
                KEY idx_memorando_destinatario_usuario (system_users_id),
                KEY idx_memorando_destinatario_departamento (departamento_unit_id),
                KEY idx_memorando_destinatario_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        );

        self::ensureColumn($conn, 'memorando_destinatario', 'departamento_unit_id', 'ALTER TABLE memorando_destinatario ADD departamento_unit_id INT NULL AFTER tipo_destino');
        self::ensureColumn($conn, 'memorando_destinatario', 'system_users_id', 'ALTER TABLE memorando_destinatario ADD system_users_id INT NULL AFTER departamento_unit_id');
        self::ensureColumn($conn, 'memorando_destinatario', 'deleted_at', 'ALTER TABLE memorando_destinatario ADD deleted_at DATETIME NULL AFTER updated_at');
        self::migrateLegacyDestinatarioColumns($conn);

        $conn->exec(
            "CREATE TABLE IF NOT EXISTS memorando_anexo (
                id INT NOT NULL AUTO_INCREMENT,
                memorando_id INT NULL,
                nome VARCHAR(255) NOT NULL,
                arquivo VARCHAR(255) NOT NULL,
                ordem INT NOT NULL DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                deleted_at DATETIME NULL,
                PRIMARY KEY (id),
                KEY idx_memorando_anexo_memorando (memorando_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        );

        self::ensureColumn($conn, 'memorando_anexo', 'updated_at', 'ALTER TABLE memorando_anexo ADD updated_at DATETIME NULL AFTER created_at');
        self::ensureColumn($conn, 'memorando_anexo', 'deleted_at', 'ALTER TABLE memorando_anexo ADD deleted_at DATETIME NULL AFTER updated_at');

        $conn->exec(
            "CREATE TABLE IF NOT EXISTS memorando_tramitacao (
                id INT NOT NULL AUTO_INCREMENT,
                memorando_id INT NULL,
                memorando_destinatario_id INT NULL,
                system_users_id INT NULL,
                departamento_unit_id INT NULL,
                status_resultante VARCHAR(40) NULL,
                acao VARCHAR(80) NOT NULL,
                descricao TEXT NULL,
                data_evento DATETIME NOT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                deleted_at DATETIME NULL,
                PRIMARY KEY (id),
                KEY idx_memorando_tramitacao_memorando (memorando_id),
                KEY idx_memorando_tramitacao_destinatario (memorando_destinatario_id),
                KEY idx_memorando_tramitacao_usuario (system_users_id),
                KEY idx_memorando_tramitacao_departamento (departamento_unit_id),
                KEY idx_memorando_tramitacao_data (data_evento)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        );

        self::ensureColumn($conn, 'memorando_tramitacao', 'system_users_id', 'ALTER TABLE memorando_tramitacao ADD system_users_id INT NULL AFTER memorando_destinatario_id');
        self::ensureColumn($conn, 'memorando_tramitacao', 'departamento_unit_id', 'ALTER TABLE memorando_tramitacao ADD departamento_unit_id INT NULL AFTER system_users_id');
        self::ensureColumn($conn, 'memorando_tramitacao', 'updated_at', 'ALTER TABLE memorando_tramitacao ADD updated_at DATETIME NULL AFTER created_at');
        self::ensureColumn($conn, 'memorando_tramitacao', 'deleted_at', 'ALTER TABLE memorando_tramitacao ADD deleted_at DATETIME NULL AFTER updated_at');
        self::migrateLegacyTramitacaoColumns($conn);

        self::ensureForeignKey($conn, 'memorando', 'fk_memorando_1', 'ALTER TABLE memorando ADD CONSTRAINT fk_memorando_1 FOREIGN KEY (system_unit_id) REFERENCES system_unit(id)');
        self::ensureForeignKey($conn, 'memorando', 'fk_memorando_2', 'ALTER TABLE memorando ADD CONSTRAINT fk_memorando_2 FOREIGN KEY (entidade_id) REFERENCES entidade(id)');
        self::ensureForeignKey($conn, 'memorando', 'fk_memorando_3', 'ALTER TABLE memorando ADD CONSTRAINT fk_memorando_3 FOREIGN KEY (departamento_unit_id) REFERENCES departamento_unit(id)');
        self::ensureForeignKey($conn, 'memorando', 'fk_memorando_4', 'ALTER TABLE memorando ADD CONSTRAINT fk_memorando_4 FOREIGN KEY (system_users_remetente_id) REFERENCES system_users(id)');
        self::ensureForeignKey($conn, 'memorando_destinatario', 'fk_memorando_destinatario_1', 'ALTER TABLE memorando_destinatario ADD CONSTRAINT fk_memorando_destinatario_1 FOREIGN KEY (memorando_id) REFERENCES memorando(id)');
        self::ensureForeignKey($conn, 'memorando_destinatario', 'fk_memorando_destinatario_2', 'ALTER TABLE memorando_destinatario ADD CONSTRAINT fk_memorando_destinatario_2 FOREIGN KEY (departamento_unit_id) REFERENCES departamento_unit(id)');
        self::ensureForeignKey($conn, 'memorando_destinatario', 'fk_memorando_destinatario_3', 'ALTER TABLE memorando_destinatario ADD CONSTRAINT fk_memorando_destinatario_3 FOREIGN KEY (system_users_id) REFERENCES system_users(id)');
        self::ensureForeignKey($conn, 'memorando_tramitacao', 'fk_memorando_tramitacao_1', 'ALTER TABLE memorando_tramitacao ADD CONSTRAINT fk_memorando_tramitacao_1 FOREIGN KEY (memorando_id) REFERENCES memorando(id)');
        self::ensureForeignKey($conn, 'memorando_tramitacao', 'fk_memorando_tramitacao_2', 'ALTER TABLE memorando_tramitacao ADD CONSTRAINT fk_memorando_tramitacao_2 FOREIGN KEY (memorando_destinatario_id) REFERENCES memorando_destinatario(id)');
        self::ensureForeignKey($conn, 'memorando_tramitacao', 'fk_memorando_tramitacao_3', 'ALTER TABLE memorando_tramitacao ADD CONSTRAINT fk_memorando_tramitacao_3 FOREIGN KEY (system_users_id) REFERENCES system_users(id)');
        self::ensureForeignKey($conn, 'memorando_tramitacao', 'fk_memorando_tramitacao_4', 'ALTER TABLE memorando_tramitacao ADD CONSTRAINT fk_memorando_tramitacao_4 FOREIGN KEY (departamento_unit_id) REFERENCES departamento_unit(id)');
        self::ensureForeignKey($conn, 'memorando_anexo', 'fk_memorando_anexo_1', 'ALTER TABLE memorando_anexo ADD CONSTRAINT fk_memorando_anexo_1 FOREIGN KEY (memorando_id) REFERENCES memorando(id)');
    }

    private static function ensureColumn($conn, string $table, string $column, string $sql): void
    {
        if (!self::columnExists($conn, $table, $column)) {
            $conn->exec($sql);
        }
    }

    private static function columnExists($conn, string $table, string $column): bool
    {
        $stmt = $conn->prepare("SHOW COLUMNS FROM {$table} LIKE ?");
        $stmt->execute([$column]);
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private static function migrateLegacyColumns($conn): void
    {
        if (self::columnExists($conn, 'memorando', 'remetente_user_id')) {
            $conn->exec('UPDATE memorando SET system_users_remetente_id = remetente_user_id WHERE system_users_remetente_id IS NULL AND remetente_user_id IS NOT NULL');
        }

        if (self::columnExists($conn, 'memorando', 'departamento_origem_id')) {
            $conn->exec('UPDATE memorando SET departamento_unit_id = departamento_origem_id WHERE departamento_unit_id IS NULL AND departamento_origem_id IS NOT NULL');
        }
    }

    private static function migrateLegacyDestinatarioColumns($conn): void
    {
        if (self::columnExists($conn, 'memorando_destinatario', 'system_user_id')) {
            $conn->exec('UPDATE memorando_destinatario SET system_users_id = system_user_id WHERE system_users_id IS NULL AND system_user_id IS NOT NULL');
        }

        if (self::columnExists($conn, 'memorando_destinatario', 'system_departamento_id')) {
            $conn->exec('UPDATE memorando_destinatario SET departamento_unit_id = system_departamento_id WHERE departamento_unit_id IS NULL AND system_departamento_id IS NOT NULL');
        }
    }

    private static function migrateLegacyTramitacaoColumns($conn): void
    {
        if (self::columnExists($conn, 'memorando_tramitacao', 'usuario_id')) {
            $conn->exec('UPDATE memorando_tramitacao SET system_users_id = usuario_id WHERE system_users_id IS NULL AND usuario_id IS NOT NULL');
        }
    }

    private static function ensureForeignKey($conn, string $table, string $constraint, string $sql): void
    {
        $stmt = $conn->prepare(
            "SELECT CONSTRAINT_NAME
               FROM information_schema.TABLE_CONSTRAINTS
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND CONSTRAINT_NAME = ?
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
        );
        $stmt->execute([$table, $constraint]);

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            try {
                $conn->exec($sql);
            } catch (Exception $e) {
                // Existing legacy schemas may have incompatible column definitions.
                // The application can still run after columns are present.
            }
        }
    }
}
