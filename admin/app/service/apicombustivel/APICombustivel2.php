<?php
namespace app\service;

use Adianti\Database\TTransaction;
use DOMDocument;
use Exception;

class APICombustivel2
{
    private const DEBUG_NP3 = true;
    private const DEBUG_LOG_FILE = 'app/output/np3_api_debug.log';
    private const DEFAULT_GRUPO = '4';
    private const DEFAULT_LOGIN = 'App.Np3';
    private const DEFAULT_SENHA = 'a1#j%5ht';

    public static function getCredenciaisPadrao(): array
    {
        return [
            'grupo' => self::DEFAULT_GRUPO,
            'login' => self::DEFAULT_LOGIN,
            'senha' => self::DEFAULT_SENHA,
        ];
    }

    public static function getApp_ListarContasPorCPF(
        string $token,
        string $grupo,
        string $login,
        string $senha,
        string $cpf,
        string $dtInicial = '01/01/2024',
        string $dtFinal = '31/12/2026',
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

        $response = self::curlPostNp3($url, $token, $xml);
        self::debugLog('API_ListarContasPorCPF.response', [
            'cpf' => $cpf,
            'url' => $url,
            'xml_enviado' => $xml,
            'response' => $response,
        ]);
        $xmlInterno = self::extrairXmlInterno($response);
        $contas = $xmlInterno->contas->conta ?? [];

        $resultado = [
            'cpf' => $cpf,
            'contas' => []
        ];

        foreach ($contas as $conta) {
            $cartao = (string) ($conta->nu_cartao ?? '');
            $rede = (string) ($conta->cd_rede ?? '');

            $respRdc = self::getAPI_RecuperarDadosCadastrais($token, $grupo, $login, $senha, $cartao);
            $xmlRdc = self::extrairXmlInterno($respRdc);

            $respConta = self::getAPI_RecuperarDadosConta($token, $grupo, $login, $senha, $cartao);
            $xmlConta = self::extrairXmlInterno($respConta);

            $cdCliente = (string) (($xmlConta->cd_cliente ?? '') ?: ($xmlRdc->cd_cliente ?? ''));
            $rede = (string) (($xmlConta->cd_rede ?? '') ?: $rede);
            $dadosConta = self::normalizarDadosContaCartao($xmlConta, $cartao, $rede, $cdCliente);
            $dadosCadastrais = self::normalizarDadosCadastraisCartao($xmlRdc, $cartao, $cpf, $rede, $cdCliente, $dadosConta);

            $respAut = self::getAtend_ListarAutorizacoesCompra(
                $token,
                $grupo,
                $login,
                $senha,
                $cartao,
                $dtInicial,
                $dtFinal,
                $tpSituacao,
                $rede,
                $cdCliente
            );

            $xmlAut = self::extrairXmlInterno($respAut);
            $erroCod = (string) ($xmlAut->erro->cod ?? '');
            $erroMsg = (string) ($xmlAut->erro->msg ?? '');

            $autorizacoes = [];
            $autNodes = $xmlAut->autorizacoes->autorizacao ?? [];

            foreach ($autNodes as $a) {
                $autorizacoes[] = [
                    'id_autoriz' => (string) ($a->id_autoriz ?? ''),
                    'cd_loja_autoriz' => (string) ($a->cd_loja_autoriz ?? ''),
                    'nm_loja' => (string) ($a->nm_loja ?? ''),
                    'dt_hora_autoriz' => (string) ($a->dt_hora_autoriz ?? ''),
                    'nu_cartao' => (string) ($a->nu_cartao ?? ''),
                    'qt_parc' => (string) ($a->qt_parc ?? ''),
                    'vl_autoriz' => (string) ($a->vl_autoriz ?? ''),
                    'cd_autoriz' => (string) ($a->cd_autoriz ?? ''),
                    'tp_status' => (string) ($a->tp_status ?? ''),
                    'de_motivo_recusa' => (string) ($a->de_motivo_recusa ?? ''),
                    'fl_cancel_permitido' => (string) ($a->fl_cancel_permitido ?? ''),
                ];
            }

            $lojasCredenciadas = self::getAPI_ListarLojasCredenciadas(
                $token,
                $grupo,
                $login,
                $senha,
                $rede,
                '',
                '0',
                '',
                '',
                '',
                '',
                '',
                'N',
                'N',
                'N',
                $rede,
                $cdCliente
            );

            $resultado['contas'][] = [
                'cartao' => $cartao,
                'rede' => $rede,
                'cd_cliente' => $cdCliente,
                'dados_cadastrais' => $dadosCadastrais,
                'dados_conta' => $dadosConta,
                'erro' => [
                    'cod' => $erroCod,
                    'msg' => $erroMsg
                ],
                'autorizacoes' => $autorizacoes,
                'lojas_credenciadas' => $lojasCredenciadas['lojas'] ?? [],
                'lojas_credenciadas_erro' => $lojasCredenciadas['erro'] ?? ['cod' => '', 'msg' => '']
            ];

            self::debugLog('API_ListarContasPorCPF.conta_processada', [
                'cpf' => $cpf,
                'cartao' => $cartao,
                'rede' => $rede,
                'cd_cliente' => $cdCliente,
                'dados_cadastrais' => $dadosCadastrais,
                'dados_conta' => $dadosConta,
                'erro_autorizacoes' => ['cod' => $erroCod, 'msg' => $erroMsg],
                'qtd_autorizacoes' => count($autorizacoes),
                'qtd_lojas' => count($lojasCredenciadas['lojas'] ?? []),
                'erro_lojas' => $lojasCredenciadas['erro'] ?? null,
            ]);
        }

        self::debugLog('API_ListarContasPorCPF.resultado_final', $resultado);
        return $resultado;
    }

    public static function getApp_ListarLancamentosPorCPF(
        string $token,
        string $grupo,
        string $login,
        string $senha,
        string $cpf,
        string $dtInicial = '01/01/2024',
        string $dtFinal = '31/12/2026'
    ): array
    {
        $contasResultado = self::getApp_ListarContasPorCPF($token, $grupo, $login, $senha, $cpf, $dtInicial, $dtFinal, '0');
        $resultado = [
            'cpf' => $cpf,
            'contas' => [],
        ];

        foreach (($contasResultado['contas'] ?? []) as $conta)
        {
            $cartao = (string) ($conta['cartao'] ?? '');
            $rede = (string) ($conta['rede'] ?? '');
            $cliente = (string) ($conta['cd_cliente'] ?? '');

            $respFaturas = self::getApp_RecuperarFaturas($token, $grupo, $login, $senha, $cpf, $cartao, $rede);
            $xmlFaturas = self::extrairXmlInterno($respFaturas);
            $cliente = (string) ($xmlFaturas->cd_cliente ?? $cliente);
            $rede = (string) ($xmlFaturas->cd_rede ?? $rede);

            $faturas = [];
            $faturaNodes = $xmlFaturas->faturas->fatura ?? [];
            foreach ($faturaNodes as $fatura)
            {
                $nuSeqFatura = trim((string) ($fatura->nu_seq_fatura ?? ''));
                if ($nuSeqFatura === '')
                {
                    continue;
                }

                $respLancamentos = self::getApp_RecuperarFaturaComLancamentos($token, $grupo, $login, $senha, $rede, $cliente, $nuSeqFatura);
                $xmlLancamentos = self::extrairXmlInterno($respLancamentos);
                $lancamentos = [];

                foreach (self::localizarNodesLancamentos($xmlLancamentos) as $node)
                {
                    $lancamento = self::normalizarLancamentoFatura($node);
                    if (!self::lancamentoDentroPeriodo($lancamento['dt_hora_lancamento'] ?? '', $dtInicial, $dtFinal))
                    {
                        continue;
                    }

                    if (empty($lancamento['numero_cartao']))
                    {
                        $lancamento['numero_cartao'] = $cartao;
                    }

                    $lancamentos[] = $lancamento;
                }

                $faturas[] = [
                    'nu_seq_fatura' => $nuSeqFatura,
                    'lancamentos' => $lancamentos,
                    'erro' => [
                        'cod' => (string) ($xmlLancamentos->erro->cod ?? ''),
                        'msg' => (string) ($xmlLancamentos->erro->msg ?? ''),
                    ],
                ];
            }

            $resultado['contas'][] = [
                'cartao' => $cartao,
                'rede' => $rede,
                'cd_cliente' => $cliente,
                'dados_cadastrais' => $conta['dados_cadastrais'] ?? [],
                'erro' => $conta['erro'] ?? ['cod' => '', 'msg' => ''],
                'autorizacoes' => $conta['autorizacoes'] ?? [],
                'lojas_credenciadas' => $conta['lojas_credenciadas'] ?? [],
                'lojas_credenciadas_erro' => $conta['lojas_credenciadas_erro'] ?? ['cod' => '', 'msg' => ''],
                'faturas' => $faturas,
            ];
        }

        self::debugLog('API_ListarLancamentosPorCPF.resultado_final', $resultado);

        return $resultado;
    }

    /* ===================== HELPERS ===================== */

    private static function curlPostNp3(string $url, string $token, string $xml): string
    {
        $postData = http_build_query([
            'strXMLDados' => $xml
        ], '', '&', PHP_QUERY_RFC3986);

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'NP3Beneficios/1.0',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                'Accept: */*',
            ],
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $info = curl_getinfo($ch);
        $httpCode = $info['http_code'] ?? 0;

        if ($response === false) {
            curl_close($ch);
            throw new Exception('Erro cURL: ' . $err);
        }

        curl_close($ch);

        if ($httpCode >= 400) {
            throw new Exception('HTTP ' . $httpCode . ' | URL: ' . $url);
        }

        return $response;
    }

    private static function extrairXmlInterno(string $response): \SimpleXMLElement
    {
        $xmlExterno = simplexml_load_string($response);

        if ($xmlExterno === false) {
            throw new Exception('Nao foi possivel ler o XML externo da NP3.');
        }

        $xmlInternoString = html_entity_decode((string) $xmlExterno, ENT_QUOTES, 'UTF-8');
        $xmlInterno = simplexml_load_string($xmlInternoString);

        if ($xmlInterno === false) {
            throw new Exception('Nao foi possivel ler o XML interno da NP3.');
        }

        return $xmlInterno;
    }

    public static function debugLog(string $label, $data = null): void
    {
        if (!self::DEBUG_NP3) {
            return;
        }

        $basePath = dirname(__DIR__, 3);
        $file = $basePath . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, self::DEBUG_LOG_FILE);
        $dir = dirname($file);

        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $payload = is_string($data) ? $data : print_r($data, true);
        $message = '[' . date('Y-m-d H:i:s') . '] ' . $label . PHP_EOL . $payload . PHP_EOL . str_repeat('-', 120) . PHP_EOL;
        @file_put_contents($file, $message, FILE_APPEND);
    }

    private static function xmlNodeToArray(\SimpleXMLElement $node): array
    {
        $data = [];

        foreach ($node->children() as $child) {
            $name = $child->getName();

            if ($child->count() > 0) {
                $data[$name] = self::xmlNodeToArray($child);
            } else {
                $data[$name] = trim((string) $child);
            }
        }

        return $data;
    }

    private static function firstAvailableTag(\SimpleXMLElement $node, array $tags): string
    {
        foreach ($tags as $tag) {
            if (isset($node->{$tag}) && trim((string) $node->{$tag}) !== '') {
                return trim((string) $node->{$tag});
            }
        }

        return '';
    }

    private static function normalizarDocumento(?string $documento): string
    {
        return preg_replace('/\D+/', '', (string) $documento);
    }

    private static function normalizarEnderecoDadosCadastrais(?\SimpleXMLElement $endereco): array
    {
        if (!$endereco) {
            return [
                'logradouro' => '',
                'numero' => '',
                'complemento' => '',
                'bairro' => '',
                'cidade' => '',
                'uf' => '',
                'cep' => '',
                'telefone' => '',
                'raw' => [],
            ];
        }

        return [
            'logradouro' => self::firstAvailableTag($endereco, ['end_nm_logradouro', 'logradouro', 'endereco']),
            'numero' => self::firstAvailableTag($endereco, ['end_numero', 'numero']),
            'complemento' => self::firstAvailableTag($endereco, ['end_complemento', 'complemento']),
            'bairro' => self::firstAvailableTag($endereco, ['end_nm_bairro', 'bairro']),
            'cidade' => self::firstAvailableTag($endereco, ['end_nm_cidade', 'cidade']),
            'uf' => strtoupper(self::firstAvailableTag($endereco, ['end_sg_uf', 'uf'])),
            'cep' => self::normalizarDocumento(self::firstAvailableTag($endereco, ['end_nu_cep', 'cep'])),
            'telefone' => self::firstAvailableTag($endereco, ['end_telefone', 'telefone']),
            'raw' => self::xmlNodeToArray($endereco),
        ];
    }

    private static function normalizarDadosCadastraisCartao(
        \SimpleXMLElement $xmlRdc,
        string $cartao = '',
        string $cpfConsulta = '',
        string $rede = '',
        string $cdCliente = '',
        array $dadosConta = []
    ): array
    {
        $endResidencial = isset($xmlRdc->end_residencial) ? $xmlRdc->end_residencial : null;
        $telefoneCadastro = self::firstAvailableTag($xmlRdc, ['nu_telefone', 'de_telefone', 'telefone']);
        $emailCadastro = self::firstAvailableTag($xmlRdc, ['de_email', 'email']);

        return [
            'cartao' => trim($cartao),
            'rede' => trim($rede),
            'cd_cliente' => trim($cdCliente),
            'nome' => self::firstAvailableTag($xmlRdc, ['nm_cliente', 'nm_pessoa', 'nome', 'nm_titular']),
            'cpf' => self::normalizarDocumento(self::firstAvailableTag($xmlRdc, ['nu_cpf', 'cpf', 'documento'])) ?: self::normalizarDocumento($cpfConsulta),
            'rg' => self::firstAvailableTag($xmlRdc, ['nu_rg', 'rg']),
            'email' => $emailCadastro,
            'telefone' => $telefoneCadastro,
            'saldo_atual' => $dadosConta['saldo_atual'] ?? '',
            'saldo_limite' => $dadosConta['saldo_limite'] ?? '',
            'dados_conta' => $dadosConta,
            'endereco_residencial' => self::normalizarEnderecoDadosCadastrais($endResidencial),
            'raw' => self::xmlNodeToArray($xmlRdc),
        ];
    }

    private static function normalizarDadosContaCartao(
        ?\SimpleXMLElement $xmlConta,
        string $cartao = '',
        string $rede = '',
        string $cdCliente = ''
    ): array
    {
        if (!$xmlConta)
        {
            return [
                'cartao' => trim($cartao),
                'rede' => trim($rede),
                'cd_cliente' => trim($cdCliente),
                'saldo_atual' => '',
                'saldo_limite' => '',
                'cartoes' => [],
                'limites_adicionais' => [],
                'raw' => [],
            ];
        }

        $dadosConta = isset($xmlConta->dados_conta) ? $xmlConta->dados_conta : null;
        $cartoes = [];

        if (isset($xmlConta->cartoes->cartao))
        {
            foreach ($xmlConta->cartoes->cartao as $cartaoNode)
            {
                $cartoes[] = [
                    'nm_no_cartao' => self::firstAvailableTag($cartaoNode, ['nm_no_cartao']),
                    'nu_cartao' => self::firstAvailableTag($cartaoNode, ['nu_cartao']),
                    'tp_membro' => self::firstAvailableTag($cartaoNode, ['tp_membro']),
                    'tp_status' => self::firstAvailableTag($cartaoNode, ['tp_status']),
                    'tp_estagio' => self::firstAvailableTag($cartaoNode, ['tp_estagio']),
                    'vl_lim_diario' => self::firstAvailableTag($cartaoNode, ['vl_lim_diario']),
                    'vl_max_compra' => self::firstAvailableTag($cartaoNode, ['vl_max_compra']),
                    'raw' => self::xmlNodeToArray($cartaoNode),
                ];
            }
        }

        $limitesAdicionais = [];
        if (isset($xmlConta->limites_adicionais->limite_adicional))
        {
            foreach ($xmlConta->limites_adicionais->limite_adicional as $limiteNode)
            {
                $limitesAdicionais[] = [
                    'sg_limite_gasto' => self::firstAvailableTag($limiteNode, ['sg_limite_gasto']),
                    'pc_limite_mensal' => self::firstAvailableTag($limiteNode, ['pc_limite_mensal']),
                    'vl_disp_mensal' => self::firstAvailableTag($limiteNode, ['vl_disp_mensal']),
                    'pc_limite_global' => self::firstAvailableTag($limiteNode, ['pc_limite_global']),
                    'vl_disp_global' => self::firstAvailableTag($limiteNode, ['vl_disp_global']),
                    'raw' => self::xmlNodeToArray($limiteNode),
                ];
            }
        }

        $saldoAtual = self::firstAvailableTag($dadosConta, ['vl_saldo_disp_mes', 'vl_saldo_disp_global', 'saldo_atual']);
        $saldoLimite = self::firstAvailableTag($dadosConta, ['vl_lim_cred_global', 'vl_lim_cred_mes', 'vl_limite']);

        if ($saldoLimite === '' && !empty($cartoes))
        {
            $saldoLimite = (string) (($cartoes[0]['vl_max_compra'] ?? '') ?: ($cartoes[0]['vl_lim_diario'] ?? ''));
        }

        return [
            'cartao' => trim($cartao),
            'rede' => trim((string) (($xmlConta->cd_rede ?? '') ?: $rede)),
            'cd_cliente' => trim((string) (($xmlConta->cd_cliente ?? '') ?: $cdCliente)),
            'nm_origem' => self::firstAvailableTag($dadosConta, ['nm_origem']),
            'vl_lim_cred_mes' => self::firstAvailableTag($dadosConta, ['vl_lim_cred_mes']),
            'vl_lim_cred_global' => self::firstAvailableTag($dadosConta, ['vl_lim_cred_global']),
            'vl_saldo_disp_mes' => self::firstAvailableTag($dadosConta, ['vl_saldo_disp_mes']),
            'vl_saldo_dev_acm' => self::firstAvailableTag($dadosConta, ['vl_saldo_dev_acm']),
            'nu_dia_venc' => self::firstAvailableTag($dadosConta, ['nu_dia_venc']),
            'dt_melhor_dia' => self::firstAvailableTag($dadosConta, ['dt_melhor_dia']),
            'tp_status_conta' => self::firstAvailableTag($dadosConta, ['tp_status_conta']),
            'qt_dias_atraso' => self::firstAvailableTag($dadosConta, ['qt_dias_atraso']),
            'tp_envio_fatura' => self::firstAvailableTag($dadosConta, ['tp_envio_fatura']),
            'tp_layout_cartao' => self::firstAvailableTag($dadosConta, ['tp_layout_cartao']),
            'dt_prox_carga' => self::firstAvailableTag($dadosConta, ['dt_prox_carga']),
            'vl_prox_carga' => self::firstAvailableTag($dadosConta, ['vl_prox_carga']),
            'tp_cartao' => self::firstAvailableTag($dadosConta, ['tp_cartao']),
            'tp_cartao_convenio' => self::firstAvailableTag($dadosConta, ['tp_cartao_convenio']),
            'saldo_atual' => $saldoAtual,
            'saldo_limite' => $saldoLimite,
            'cartoes' => $cartoes,
            'limites_adicionais' => $limitesAdicionais,
            'raw' => self::xmlNodeToArray($xmlConta),
        ];
    }

    private static function localizarNodesLojas(\SimpleXMLElement $xmlInterno): array
    {
        $caminhos = [
            ['lojas_credenciadas', 'loja_credenciada'],
            ['lojas_credenciadas', 'loja'],
            ['lojas', 'loja_credenciada'],
            ['lojas', 'loja'],
            ['estabelecimentos', 'estabelecimento'],
        ];

        foreach ($caminhos as $caminho) {
            [$container, $item] = $caminho;

            if (isset($xmlInterno->{$container}->{$item})) {
                $resultado = [];
                foreach ($xmlInterno->{$container}->{$item} as $node) {
                    $resultado[] = $node;
                }
                return $resultado;
            }
        }

        $nodes = $xmlInterno->xpath('.//*[contains(local-name(), "loja")]');
        if (!$nodes) {
            return [];
        }

        $resultado = [];
        foreach ($nodes as $node) {
            if ($node->count() > 0) {
                $resultado[] = $node;
            }
        }

        return $resultado;
    }

    private static function normalizarLojaCredenciada(\SimpleXMLElement $loja): array
    {
        return [
            'codigo_loja' => self::firstAvailableTag($loja, ['cd_loja', 'cd_loja_autoriz', 'id_loja']),
            'nome' => self::firstAvailableTag($loja, ['nm_loja', 'nm_loja_abrev', 'nm_fantasia', 'nome_fantasia']),
            'razao_social' => self::firstAvailableTag($loja, ['nm_razao_social', 'razao_social', 'nome', 'nm_loja']),
            'cnpj' => self::normalizarDocumento(self::firstAvailableTag($loja, ['nu_cnpj', 'nu_cnpj_loja', 'cnpj', 'nr_cnpj', 'documento'])),
            'cep' => self::normalizarDocumento(self::firstAvailableTag($loja, ['nu_cep', 'cep'])),
            'logradouro' => self::firstAvailableTag($loja, ['de_endereco', 'end_nm_logradouro', 'logradouro', 'endereco']),
            'numero' => self::firstAvailableTag($loja, ['nu_endereco', 'end_numero', 'numero', 'nr_endereco']),
            'bairro' => self::firstAvailableTag($loja, ['nm_bairro', 'end_nm_bairro', 'bairro']),
            'complemento' => self::firstAvailableTag($loja, ['de_complemento', 'end_complemento', 'complemento']),
            'cidade' => self::firstAvailableTag($loja, ['nm_cidade', 'end_nm_cidade', 'cidade']),
            'uf' => strtoupper(self::firstAvailableTag($loja, ['sg_uf', 'end_sg_uf', 'uf'])),
            'telefone' => self::firstAvailableTag($loja, ['nu_telefone', 'de_tel_comercial', 'de_tel_0800', 'telefone']),
            'email' => self::firstAvailableTag($loja, ['de_email', 'de_end_email', 'email']),
            'latitude' => self::firstAvailableTag($loja, ['vl_latitude', 'end_latitude', 'latitude']),
            'longitude' => self::firstAvailableTag($loja, ['vl_longitude', 'end_longitude', 'longitude']),
            'ramo_atividade' => self::firstAvailableTag($loja, ['nm_ramo_atividade', 'ramo_atividade']),
            'raw' => self::xmlNodeToArray($loja),
        ];
    }

    private static function localizarNodesLancamentos(\SimpleXMLElement $xmlInterno): array
    {
        $caminhos = [
            ['lancamentos', 'lancamento'],
            ['lancamentos_fatura', 'lancamento'],
            ['movimentos', 'movimento'],
            ['compras', 'compra'],
        ];

        foreach ($caminhos as $caminho)
        {
            [$container, $item] = $caminho;

            if (isset($xmlInterno->{$container}->{$item}))
            {
                $resultado = [];
                foreach ($xmlInterno->{$container}->{$item} as $node)
                {
                    $resultado[] = $node;
                }
                return $resultado;
            }
        }

        $nodes = $xmlInterno->xpath('.//*[contains(local-name(), "lanc")]');
        if (!$nodes)
        {
            return [];
        }

        $resultado = [];
        foreach ($nodes as $node)
        {
            if ($node->count() > 0)
            {
                $resultado[] = $node;
            }
        }

        return $resultado;
    }

    private static function normalizarLancamentoFatura(\SimpleXMLElement $lancamento): array
    {
        return [
            'id_lancamento' => self::firstAvailableTag($lancamento, ['id_lancamento', 'nu_seq_lancamento', 'cd_lancamento', 'nu_seq_movto', 'id']),
            'codigo_autorizacao' => self::firstAvailableTag($lancamento, ['cd_autoriz', 'cd_autorizacao', 'nu_autorizacao']),
            'codigo_loja' => self::firstAvailableTag($lancamento, ['cd_loja', 'cd_loja_autoriz', 'id_loja']),
            'nome_loja' => self::firstAvailableTag($lancamento, ['nm_loja', 'nm_estabelecimento', 'nm_fantasia', 'nome_fantasia']),
            'cnpj_loja' => self::normalizarDocumento(self::firstAvailableTag($lancamento, ['nu_cnpj', 'cnpj', 'nr_cnpj', 'documento'])),
            'dt_hora_lancamento' => self::firstAvailableTag($lancamento, ['dt_hora_lancamento', 'dt_lancamento', 'dt_movimento', 'dt_compra', 'dt_transacao']),
            'numero_cartao' => self::firstAvailableTag($lancamento, ['nu_cartao', 'numero_cartao']),
            'valor_lancamento' => self::firstAvailableTag($lancamento, ['vl_lancamento', 'vl_total', 'vl_compra', 'vl_transacao', 'vl_parcela', 'vl_lanc']),
            'status' => self::firstAvailableTag($lancamento, ['tp_status', 'de_status', 'status']),
            'motivo' => self::firstAvailableTag($lancamento, ['de_motivo_recusa', 'de_motivo', 'motivo']),
            'raw' => self::xmlNodeToArray($lancamento),
        ];
    }

    private static function parseDataFiltro(string $valor): ?\DateTime
    {
        $valor = trim($valor);
        $formatos = ['d/m/Y', 'Y-m-d', 'd/m/Y H:i:s', 'Y-m-d H:i:s'];

        foreach ($formatos as $formato)
        {
            $data = \DateTime::createFromFormat($formato, $valor);
            if ($data instanceof \DateTime)
            {
                return $data;
            }
        }

        try
        {
            return new \DateTime($valor);
        }
        catch (Exception $e)
        {
            return null;
        }
    }

    private static function lancamentoDentroPeriodo(string $dataLancamento, string $dtInicial, string $dtFinal): bool
    {
        $data = self::parseDataFiltro($dataLancamento);
        if (!$data)
        {
            return true;
        }

        $inicio = self::parseDataFiltro($dtInicial);
        $fim = self::parseDataFiltro($dtFinal);

        if ($inicio)
        {
            $inicio->setTime(0, 0, 0);
            if ($data < $inicio)
            {
                return false;
            }
        }

        if ($fim)
        {
            $fim->setTime(23, 59, 59);
            if ($data > $fim)
            {
                return false;
            }
        }

        return true;
    }

    public static function getAtend_ListarAutorizacoesCompra(
        string $token,
        string $grupo,
        string $login,
        string $senha,
        string $cartao,
        string $dtInicial,
        string $dtFinal,
        string $tpSituacao = '0',
        string $rede = '',
        string $cdCliente = ''
    ): string
    {
        $url = 'https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/API_ListarAutorizacoesCompra';

        $redeXml = $rede !== '' ? '<cd_rede>' . htmlspecialchars($rede, ENT_XML1, 'UTF-8') . '</cd_rede>' : '';
        $clienteXml = $cdCliente !== '' ? '<cd_cliente>' . htmlspecialchars($cdCliente, ENT_XML1, 'UTF-8') . '</cd_cliente>' : '';

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
                    '.$redeXml.'
                    '.$clienteXml.'
                </identificacao_cliente>
            </atend>
        ');

        return self::curlPostNp3($url, $token, $xml);
    }

    public static function getAPI_ListarLojasCredenciadas(
        string $token,
        string $grupo,
        string $login,
        string $senha,
        string $redeFiltro = '0',
        string $sgUf = '',
        string $cdMunic = '0',
        string $nmBairro = '',
        string $cdRamoAtividade = '',
        string $nmLoja = '',
        string $dePosicao = '',
        string $nuDistancia = '',
        string $flAutorizApp = 'N',
        string $flCashback = 'N',
        string $flDelivery = 'N',
        string $cdRedeCliente = '0',
        string $cdCliente = '0'
    ): array
    {
        $base = 'https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/';
        $url  = $base . 'API_ListarLojasCredenciadas';

        $xml = '<atend>
            <identificacao_origem>
                <cd_grupo>'.htmlspecialchars($grupo, ENT_XML1, 'UTF-8').'</cd_grupo>
                <de_login_usu>'.htmlspecialchars($login, ENT_XML1, 'UTF-8').'</de_login_usu>
                <de_senha_usu>'.htmlspecialchars($senha, ENT_XML1, 'UTF-8').'</de_senha_usu>
            </identificacao_origem>
            <identificacao_filtro>
                <cd_rede>'.htmlspecialchars($redeFiltro, ENT_XML1, 'UTF-8').'</cd_rede>
                <sg_uf>'.htmlspecialchars($sgUf, ENT_XML1, 'UTF-8').'</sg_uf>
                <cd_munic>'.htmlspecialchars($cdMunic, ENT_XML1, 'UTF-8').'</cd_munic>
                <nm_bairro>'.htmlspecialchars($nmBairro, ENT_XML1, 'UTF-8').'</nm_bairro>
                <cd_ramo_atividade>'.htmlspecialchars($cdRamoAtividade, ENT_XML1, 'UTF-8').'</cd_ramo_atividade>
                <nm_loja>'.htmlspecialchars($nmLoja, ENT_XML1, 'UTF-8').'</nm_loja>
                <de_posicao>'.htmlspecialchars($dePosicao, ENT_XML1, 'UTF-8').'</de_posicao>
                <nu_distancia>'.htmlspecialchars($nuDistancia, ENT_XML1, 'UTF-8').'</nu_distancia>
                <fl_autoriz_app>'.htmlspecialchars($flAutorizApp, ENT_XML1, 'UTF-8').'</fl_autoriz_app>
                <fl_cashback>'.htmlspecialchars($flCashback, ENT_XML1, 'UTF-8').'</fl_cashback>
                <fl_delivery>'.htmlspecialchars($flDelivery, ENT_XML1, 'UTF-8').'</fl_delivery>
            </identificacao_filtro>
            <identificacao_cliente>
                <cd_rede>'.htmlspecialchars($cdRedeCliente, ENT_XML1, 'UTF-8').'</cd_rede>
                <cd_cliente>'.htmlspecialchars($cdCliente, ENT_XML1, 'UTF-8').'</cd_cliente>
            </identificacao_cliente>
        </atend>';

        $response = self::curlPostNp3($url, $token, $xml);
        $xmlInterno = self::extrairXmlInterno($response);

        $lojas = [];
        foreach (self::localizarNodesLojas($xmlInterno) as $lojaNode) {
            $lojas[] = self::normalizarLojaCredenciada($lojaNode);
        }

        self::debugLog('API_ListarLojasCredenciadas', [
            'url' => $url,
            'xml_enviado' => $xml,
            'response' => $response,
            'erro' => [
                'cod' => (string) ($xmlInterno->erro->cod ?? ''),
                'msg' => (string) ($xmlInterno->erro->msg ?? ''),
            ],
            'qtd_lojas' => count($lojas),
            'lojas' => $lojas,
        ]);

        return [
            'erro' => [
                'cod' => (string) ($xmlInterno->erro->cod ?? ''),
                'msg' => (string) ($xmlInterno->erro->msg ?? ''),
            ],
            'lojas' => $lojas,
            'xml' => $response,
        ];
    }

    public static function getApp_RecuperarFaturas(string $token, string $grupo, string $login, string $senha, string $cpf, string $cartao, string $rede): ?string
    {
        $base = 'https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/';
        $url  = $base . 'API_RecuperarFaturas';

        $xml = '<atend>
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

        return self::curlPostNp3($url, $token, $xml);
    }

    public static function getAPI_RecuperarDadosCadastrais(string $token, string $grupo, string $login, string $senha, string $cartao): ?string
    {
        $base = 'https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/';
        $url  = $base . 'API_RecuperarDadosCadastrais';

        $xml = '<atend>
            <identificacao_origem>
                <cd_grupo>'.$grupo.'</cd_grupo>
                <de_login_usu>'.$login.'</de_login_usu>
                <de_senha_usu>'.$senha.'</de_senha_usu>
            </identificacao_origem>
            <identificacao_cliente>
                <nu_cartao>'.$cartao.'</nu_cartao>
            </identificacao_cliente>
        </atend>';

        return self::curlPostNp3($url, $token, $xml);
    }

    public static function getAPI_RecuperarDadosConta(string $token, string $grupo, string $login, string $senha, string $cartao): ?string
    {
        $base = 'https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/';
        $url  = $base . 'API_RecuperarDadosConta';

        $xml = '<atend>
            <identificacao_origem>
                <cd_grupo>'.$grupo.'</cd_grupo>
                <de_login_usu>'.$login.'</de_login_usu>
                <de_senha_usu>'.$senha.'</de_senha_usu>
            </identificacao_origem>
            <identificacao_cliente>
                <nu_cartao>'.$cartao.'</nu_cartao>
            </identificacao_cliente>
        </atend>';

        return self::curlPostNp3($url, $token, $xml);
    }

    public static function getApp_RecuperarFaturaComLancamentos(string $token, string $grupo, string $login, string $senha, string $rede, string $cliente, string $nu_seq_fatura): ?string
    {
        $base = 'https://cartao.np3beneficios.com.br/np3/wsInterfaceAtendimentoAPI.asmx/';
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

        return self::curlPostNp3($url, $token, $xml);
    }

    public static function getPegarToken(string $grupo, string $login, string $senha): ?string
    {
        $grupo = trim($grupo);
        $login = trim($login);
        $senha = trim($senha);

        if ($grupo === '' || $login === '' || $senha === '') {
            throw new Exception('grupo/login/senha nao podem ser vazios.');
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
            curl_close($ch);
            throw new Exception('Nao foi possivel obter o token JWT.');
        }

        curl_close($ch);

        $xmlExterno = simplexml_load_string($response);
        if ($xmlExterno === false) {
            throw new Exception('Resposta invalida ao obter token JWT.');
        }

        $xmlInternoString = html_entity_decode((string) $xmlExterno, ENT_QUOTES, 'UTF-8');
        $xmlInterno = simplexml_load_string($xmlInternoString);

        if ($xmlInterno === false) {
            throw new Exception('XML interno invalido ao obter token JWT.');
        }

        $token = (string) $xmlInterno->de_token;
        return $token !== '' ? $token : null;
    }

    private static function domValue(DOMDocument $dom, string $tag): ?string
    {
        $nodes = $dom->getElementsByTagName($tag);
        return $nodes->length ? trim($nodes->item(0)->nodeValue) : null;
    }
}
