<?php
namespace app\service;

use Adianti\Database\TTransaction;
use Exception;
use DOMDocument;

class APICombustivel
{

    public static function getApp_ListarContasPorCPF(
    string $token,
    string $grupo,
    string $login,
    string $senha,
    string $cpf,
    string $dtInicial = '01/01/2024',
    string $dtFinal   = '31/12/2026',
    string $tpSituacao = '0'
    ): array
    {
        $base = 'https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/';
        $url  = $base . 'API_ListarContasPorCPF';

        $xml = trim('
            <atend>
                <identificacao_origem>
                    <cd_grupo>'.$grupo.'</cd_grupo>
                    <de_login_usu>'.$login.'</de_login_usu>
                    <de_senha_usu>'.$senha.'</de_senha_usu>
                </identificacao_origem>
                <identificacao_cliente>
                    <nu_cpf>'.$cpf.'</nu_cpf>
                </identificacao_cliente>
            </atend>
        ');

        $postData = 'strXMLDados=' . $xml;

        $response = self::curlPostNp3($url, $token, $postData);

        // extrai XML interno
        $xmlInterno = self::extrairXmlInterno($response);

        $contas = $xmlInterno->contas->conta ?? [];

        $resultado = [
            'cpf'   => $cpf,
            'contas' => []
        ];

        foreach ($contas as $conta) {
            $cartao = (string) ($conta->nu_cartao ?? '');
            $rede   = (string) ($conta->cd_rede   ?? ''); // se vier nessa API, ok guardar

            // 🔹 Autorizações (manual: ATEND_ListarAutorizacoesCompra)
            $respAut = self::getAtend_ListarAutorizacoesCompra(
                $token,
                $grupo,
                $login,
                $senha,
                $cartao,
                $dtInicial,
                $dtFinal,
                $tpSituacao
            );
            // var_dump($token, $grupo, $login,$senha, $cartao, $dtInicial, $dtFinal, $tpSituacao); // debug: mostra a resposta bruta das autorizações
            // die();

            $xmlAut = self::extrairXmlInterno($respAut);

            // captura erro (se existir)
            $erroCod = (string) ($xmlAut->erro->cod ?? '');
            $erroMsg = (string) ($xmlAut->erro->msg ?? '');

            $autorizacoes = [];
            $autNodes = $xmlAut->autorizacoes->autorizacao ?? [];

            foreach ($autNodes as $a) {
                $autorizacoes[] = [
                    'id_autoriz'         => (string) ($a->id_autoriz ?? ''),
                    'cd_loja_autoriz'    => (string) ($a->cd_loja_autoriz ?? ''),
                    'nm_loja'            => (string) ($a->nm_loja ?? ''),
                    'dt_hora_autoriz'    => (string) ($a->dt_hora_autoriz ?? ''),
                    'nu_cartao'          => (string) ($a->nu_cartao ?? ''),
                    'qt_parc'            => (string) ($a->qt_parc ?? ''),
                    'vl_autoriz'         => (string) ($a->vl_autoriz ?? ''),
                    'cd_autoriz'         => (string) ($a->cd_autoriz ?? ''),
                    'tp_status'          => (string) ($a->tp_status ?? ''),
                    'de_motivo_recusa'   => (string) ($a->de_motivo_recusa ?? ''),
                    'fl_cancel_permitido'=> (string) ($a->fl_cancel_permitido ?? ''),
                ];
            }

            $resultado['contas'][] = [
                'cartao' => $cartao,
                'rede'   => $rede,
                'erro'   => [
                    'cod' => $erroCod,
                    'msg' => $erroMsg
                ],
                'autorizacoes' => $autorizacoes
            ];
        }

        return $resultado;
    }

    /* ===================== HELPERS ===================== */

    private static function curlPostNp3(string $url, string $token, string $postData): string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . strlen($postData),
            ],
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new Exception('Erro cURL: ' . $err);
        }
        curl_close($ch);

        return $response;
    }
   
    private static function extrairXmlInterno(string $response): \SimpleXMLElement
    {
        //recuperar faturas inicio
        $xmlExterno = simplexml_load_string($response);

        if ($xmlExterno === false) {
            exit;
        }

        $xmlInternoString = html_entity_decode((string) $xmlExterno, ENT_QUOTES, 'UTF-8');

        $xmlInterno = simplexml_load_string($xmlInternoString);

        if ($xmlInterno === false) {
            exit;
        }
        // var_dump($response, $xmlExterno, $xmlInterno); // debug: mostra o XML interno extraído
        return $xmlInterno;
    }


   public static function getAtend_ListarAutorizacoesCompra(
    string $token,
    string $grupo,
    string $login,
    string $senha,
    string $cartao,
    string $dtInicial,
    string $dtFinal,
    string $tpSituacao = '0' // 0=todas | 1=autorizadas | 2=recusadas | 3=canceladas
    ): string
    {
        $url = 'https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/API_ListarAutorizacoesCompra';

        
        $xml = trim('
            <atend>
                <identificacao_usuario>
                    <cd_grupo>'.$grupo.'</cd_grupo>
                    <de_login_usu>'.$login.'</de_login_usu>
                    <de_senha_usu>'.$senha.'</de_senha_usu>
                </identificacao_usuario>
                <identificacao_cliente>
                    <nu_cartao>'.$cartao.'</nu_cartao>
                    <dt_inicial>'.$dtInicial.'</dt_inicial>
                    <dt_final>'.$dtFinal.'</dt_final>
                    <tp_situacao>'.$tpSituacao.'</tp_situacao>
                </identificacao_cliente>
            </atend>
        ');

     

        $postData = 'strXMLDados=' . $xml;
      
         var_dump($postData); // debug: mostra a resposta bruta da API

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . strlen($postData),
            ],
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
           var_dump(curl_error($ch)); // throw new Exception('Erro cURL: ' . curl_error($ch));
        }
        curl_close($ch);

        var_dump('<br>', '<br>', '<br>', $response); // debug: mostra a resposta bruta da API
        die();
        return $response;
    }



    public static function getApp_RecuperarFaturas(string $token, string $grupo, string $login, string $senha, string $cpf, string $cartao, string $rede): ?string
    {

        $base='https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/';
        $url  = $base . 'API_RecuperarFaturas';

                $post_data = 'strXMLDados=
                <atend>
                <identificacao_origem>
                    <cd_grupo>'.$grupo.'</cd_grupo>
                    <de_login_usu>'.$login.'</de_login_usu>
                    <de_senha_usu>'.$senha.'</de_senha_usu>
                </identificacao_origem>
                <identificacao_cliente>
                    <cd_rede>'.$rede.'</cd_rede>
                    <nu_cpf>'.$cpf.'</nu_cpf>
                    <nu_cartao>'.$cartao.'</nu_cartao>
                </identificacao_cliente>
                </atend>';
                
                $bearer_token = $token; //'app/output/token.txt';
              //  var_dump($post_data);
        
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

                return $response;

    }


    public static function getApp_RecuperarFaturaComLancamentos(string $token, string $grupo, string $login, string $senha, string $rede, string $cliente, string $nu_seq_fatura ): ?string
    {
       

        $base='https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/';
        $url  = $base . 'API_RecuperarFaturaComLancamentos';

                $xml = '<atend>
                    <identificacao_origem>
                        <cd_grupo>'.$grupo.'</cd_grupo>
                        <de_login_usu>'.$login.'</de_login_usu>
                        <de_senha_usu>'.$senha.'</de_senha_usu>
                    </identificacao_origem>
                    <identificacao_cliente>
                        <cd_rede>'.$rede.'</cd_rede>
                        <cd_cliente>'.$cliente.'</cd_cliente>
                        <nu_seq_fatura>'.$nu_seq_fatura.'</nu_seq_fatura>
                    </identificacao_cliente>
                </atend>';
             
                $post_data = 'strXMLDados=' . $xml;

                
                $bearer_token = $token; //'app/output/token.txt';

          
//var_dump(urldecode($post_data));
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

                // var_dump($grupo, $login, $senha, $rede, $cliente, $nu_seq_fatura, $bearer_token, $ch, $response);

               
                if ($response === false) {
                    // var_dump(curl_error($ch));

                    curl_close($ch);
       
                    exit;
                }

                curl_close($ch);
              //  var_dump($response);

                return $response;

    }

    

    public static function getPegarToken(string $grupo, string $login, string $senha): ?string
    {
        $grupo = trim($grupo);
        $login = trim($login);
        $senha = trim($senha);

        if ($grupo === '' || $login === '' || $senha === '') {
            throw new Exception('grupo/login/senha não podem ser vazios.');
        }

        $xml = '<atend><identificacao_origem>'
            . '<cd_grupo>'.htmlspecialchars($grupo, ENT_XML1, 'UTF-8').'</cd_grupo>'
            . '<de_login_usu>'.htmlspecialchars($login, ENT_XML1, 'UTF-8').'</de_login_usu>'
            . '<de_senha_usu>'.htmlspecialchars($senha, ENT_XML1, 'UTF-8').'</de_senha_usu>'
            . '</identificacao_origem></atend>';

        $url = 'https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/API_PegarTokenJwt';

        $postFields = http_build_query(['strXMLDados' => $xml]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded; charset=utf-8'
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
        // echo "Token obtido: " . $token . "\n";
        // if ($token !== '') {
        //     file_put_contents('token.txt', $token);
        // }
        // echo "Token salvo com sucesso: " . $token;
        return $token !== '' ? $token : null;
    }
    private static function domValue(DOMDocument $dom, string $tag): ?string
    {
        $nodes = $dom->getElementsByTagName($tag);
        return $nodes->length ? trim($nodes->item(0)->nodeValue) : null;
    }




}

