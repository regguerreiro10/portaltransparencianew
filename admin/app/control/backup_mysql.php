<?php

$log_file = __DIR__ . '/backup_log.log';

function registrarLog($mensagem) {
    global $log_file;
    $data = date('[Y-m-d H:i:s]');
    file_put_contents($log_file, "$data $mensagem\n", FILE_APPEND);
}

registrarLog("Iniciando execução do script de backup.");

// Configurações do banco
$host = '177.53.140.113';
$user = 'gestaonp3benefic_regina';
$password = 'QJ6$@rAfdUbd70TG';
$database = 'gestaonp3benefic_dbgestao';


// Diretório para salvar os backups
$backupDir = __DIR__ . '/backups';
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Nome do arquivo
$dataHora = date('Y-m-d_H-i-s');
$sqlFile  = "{$backupDir}/{$database}_{$dataHora}.sql";
$zipFile  = "{$backupDir}/backup_{$database}_{$dataHora}.zip";

// Comando mysqldump
$dumpCommand = "mysqldump --user={$user} --password={$password} --host={$host} {$database} > {$sqlFile}";
exec($dumpCommand, $output, $retorno);

// Verifica se o dump foi gerado
if ($retorno !== 0 || !file_exists($sqlFile)) {
    echo "Erro ao criar o backup SQL.";
    exit;
}

// Cria o ZIP
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
    $zip->addFile($sqlFile, basename($sqlFile));
    $zip->close();
    unlink($sqlFile); // remove o .sql após zipar
    echo "Backup criado com sucesso: {$zipFile}";
} else {
    echo "Erro ao criar o arquivo ZIP.";
}

// (Opcional) Limpar backups com mais de 7 dias
$limiteDias = 7;
foreach (glob("{$backupDir}/backup_*.zip") as $file) {
    if (filemtime($file) < time() - ($limiteDias * 86400)) {
        unlink($file);
    }
}

registrarLog("Script finalizado.");
?>
