<?php

class TabelaSchemaHelper
{
    public static function ensureSchema(): void
    {
        $conn = TTransaction::get();

        $conn->exec(
            "CREATE TABLE IF NOT EXISTS tabela (
                id INT NOT NULL AUTO_INCREMENT,
                descricao VARCHAR(255) NOT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                deleted_at DATETIME NULL,
                PRIMARY KEY (id),
                KEY idx_tabela_descricao (descricao)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $conn->exec(
            "CREATE TABLE IF NOT EXISTS tabela_de_tabela (
                id INT NOT NULL AUTO_INCREMENT,
                tabela_id INT NOT NULL,
                descricao VARCHAR(255) NOT NULL,
                cor VARCHAR(10) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                deleted_at DATETIME NULL,
                PRIMARY KEY (id),
                KEY idx_tabela_de_tabela_tabela (tabela_id),
                KEY idx_tabela_de_tabela_descricao (descricao),
                CONSTRAINT fk_tabela_de_tabela_tabela
                    FOREIGN KEY (tabela_id) REFERENCES tabela(id)
                    ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }
}
