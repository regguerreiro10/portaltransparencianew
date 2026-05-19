ALTER TABLE conta_anexo
    ADD COLUMN created_at datetime DEFAULT NULL AFTER arquivo,
    ADD COLUMN updated_at datetime DEFAULT NULL AFTER created_at,
    ADD COLUMN deleted_at datetime DEFAULT NULL AFTER updated_at;
