<?php

$base='https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/';
$url  = $base . 'API_RecuperarLancamentosLoja';
$post_data = 'strXMLDados=<atend><identificacao_origem><cd_grupo>4</cd_grupo><de_login_usu>App.Np3</de_login_usu><de_senha_usu>a1#j%5</de_senha_usu></identificacao_origem><identificacao_cliente><nu_cnpj>08298336000194</nu_cnpj><data_inicial>01/12/2025</data_inicial><data_final>31/01/2026</data_final></identificacao_cliente></atend>';

$tokenFile = 'token.txt';

if (!file_exists($tokenFile)) {
    exit;
}

$bearer_token = trim(file_get_contents($tokenFile));

if ($bearer_token === '') {
    exit;
}

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $bearer_token,
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Length: ' . strlen($post_data)
]);

$response = curl_exec($ch);

if ($response === false) {

    curl_close($ch);

    exit;
}

curl_close($ch);

echo $response;