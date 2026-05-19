<?php

$base='https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/';
$url  = $base . 'API_RecuperarLancamentosLoja';
$post_data = 'strXMLDados=<atend><identificacao_origem><cd_grupo>4</cd_grupo><de_login_usu>App.Np3</de_login_usu><de_senha_usu>a1#j%5</data_inicial><data_final>31/01/2026</data_final></identificacao_cliente></atend>';

$tokenFile = 'token.txt';

if (!file_exists($tokenFile)) {
    exit;
}

$bearer_token = trim(file_get_contents($tokenFile));

if ($bearer_token === '') {
    exit;
}