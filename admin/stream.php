<?php
if (!isset($_GET['file'])) {
    die('Arquivo não especificado.');
}

$file = $_GET['file'];
//$base_dir = 'app/documentos/'; // ajuste para o caminho real dos vídeos
$path = basename($file);

if (!file_exists($file)) {
    die('Arquivo não encontrado.');
}

$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$mime = [
    'mp4' => 'video/mp4',
    'webm' => 'video/webm',
    'ogg' => 'video/ogg'
][$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
header('Accept-Ranges: bytes');
readfile($path);
exit;
?>
