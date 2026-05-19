<?php

$xml = '<atend><identificacao_origem><cd_grupo>4</cd_grupo><de_login_usu>App.Np3</de_login_usu><de_senha_usu>a1#j%5ht</de_senha_usu></identificacao_origem></atend>';

$url = 'https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/API_PegarTokenJwt';

$postFields = http_build_query([
    'strXMLDados' => $xml
]);

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded'
    ],
]);

$response = curl_exec($ch);

if ($response === false) {
    exit;
}

curl_close($ch);

$xmlExterno = simplexml_load_string($response);

if ($xmlExterno === false) {
    exit;
}

$xmlInternoString = html_entity_decode((string) $xmlExterno, ENT_QUOTES, 'UTF-8');

$xmlInterno = simplexml_load_string($xmlInternoString);

if ($xmlInterno === false) {
    exit;
}

$token = (string) $xmlInterno->de_token;
echo "Token obtido: " . $token . "\n";
if ($token !== '') {
    file_put_contents('token.txt', $token);
}
echo "Token salvo com sucesso: " . $token;
