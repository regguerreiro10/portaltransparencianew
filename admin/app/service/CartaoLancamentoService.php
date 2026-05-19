<?php

use app\service\APICombustivel2;

class CartaoLancamentoService
{
    public const SESSION_KEY = __CLASS__ . '_launches';
    public const SESSION_META_KEY = __CLASS__ . '_meta';

    private static function parseNumero($valor): float
    {
        if ($valor === null || $valor === '')
        {
            return 0.0;
        }

        $texto = trim((string) $valor);
        $texto = preg_replace('/[^0-9.,\\-]/', '', $texto);

        $posVirgula = strrpos($texto, ',');
        $posPonto = strrpos($texto, '.');

        if ($posVirgula !== false || $posPonto !== false)
        {
            $separadorDecimal = ($posVirgula !== false && $posVirgula > $posPonto) ? ',' : '.';
            $partes = explode($separadorDecimal, $texto);
            $decimal = array_pop($partes);
            $inteiro = implode('', $partes);
            $inteiro = str_replace([',', '.'], '', $inteiro);
            $normalizado = $inteiro . '.' . $decimal;
        }
        else
        {
            $normalizado = str_replace([',', '.'], '', $texto);
        }

        return is_numeric($normalizado) ? (float) $normalizado : 0.0;
    }

    private static function normalizarValorLancamento($valor): float
    {
        return round(abs(self::parseNumero($valor)), 2);
    }

    private static function obterTaxaContratoPercentual($taxaContrato): float
    {
        $taxa = round(self::parseNumero($taxaContrato), 2);

        if ($taxa > 0 && $taxa <= 1)
        {
            $taxa *= 100;
        }

        return $taxa;
    }

    private static function calcularTotaisComTaxaContrato(float $valorUnitario, float $quantidade, $taxaContrato): array
    {
        $subtotalCents = (int) round(($valorUnitario * $quantidade) * 100, 0, PHP_ROUND_HALF_UP);
        $taxaPercentual = self::obterTaxaContratoPercentual($taxaContrato);
        $taxaBps = (int) round($taxaPercentual * 100, 0, PHP_ROUND_HALF_UP);
        $descontoCents = (int) floor((($subtotalCents * $taxaBps) + 5000) / 10000);
        $valorLiquidoCents = $subtotalCents - $descontoCents;

        return [
            'taxa_percentual' => $taxaPercentual,
            'subtotal' => $subtotalCents / 100,
            'desconto' => $descontoCents / 100,
            'valor_liquido' => $valorLiquidoCents / 100,
        ];
    }

    private static function calcularVencimentoFinanceiro($dtFinalizacao): string
    {
        if (empty($dtFinalizacao))
        {
            $dtFinalizacao = date('Y-m-d');
        }

        try
        {
            $dataBase = new DateTime(substr((string) $dtFinalizacao, 0, 10));
        }
        catch (Exception $e)
        {
            $dataBase = new DateTime(date('Y-m-d'));
        }

        $dataBase->modify('first day of next month');
        $dataBase->modify('+35 days');

        return $dataBase->format('Y-m-d');
    }

    private static function normalizarDocumento(?string $documento): string
    {
        return preg_replace('/\D+/', '', (string) $documento);
    }

    private static function normalizarCpf(?string $cpf): string
    {
        $cpf = self::normalizarDocumento($cpf);

        if ($cpf === '')
        {
            return '';
        }

        if (strlen($cpf) !== 11)
        {
            throw new Exception('Informe um CPF valido para consultar os lancamentos do cartao.');
        }

        return $cpf;
    }

    private static function normalizarNome(?string $valor): string
    {
        $valor = trim((string) $valor);
        $valor = preg_replace('/\s+/', ' ', $valor);

        return mb_strtoupper($valor, 'UTF-8');
    }

    private static function normalizarIdentificadorCartao(?string $valor): string
    {
        $valor = trim((string) $valor);
        $valor = self::expandirNotacaoCientifica($valor);
        $valor = strtoupper($valor);
        $valor = preg_replace('/[^A-Z0-9]/', '', $valor);

        return $valor;
    }

    private static function expandirNotacaoCientifica(string $valor): string
    {
        $valor = trim($valor);
        if ($valor === '' || stripos($valor, 'E') === false)
        {
            return $valor;
        }

        if (!preg_match('/^([+-]?\d+)(?:\.(\d+))?[eE]\+?(\d+)$/', $valor, $partes))
        {
            return $valor;
        }

        $inteiro = ltrim($partes[1], '+');
        $decimal = $partes[2] ?? '';
        $expoente = (int) ($partes[3] ?? 0);
        $sinal = '';

        if (strpos($inteiro, '-') === 0)
        {
            $sinal = '-';
            $inteiro = substr($inteiro, 1);
        }

        $digitos = $inteiro . $decimal;
        $casasMover = $expoente - strlen($decimal);

        if ($casasMover >= 0)
        {
            return $sinal . $digitos . str_repeat('0', $casasMover);
        }

        $posicao = strlen($digitos) + $casasMover;
        if ($posicao <= 0)
        {
            return $sinal . '0.' . str_repeat('0', abs($posicao)) . $digitos;
        }

        return $sinal . substr($digitos, 0, $posicao) . '.' . substr($digitos, $posicao);
    }

    private static function obterCredenciaisApi(): array
    {
        $credenciais = APICombustivel2::getCredenciaisPadrao();

        if (empty($credenciais['grupo']) || empty($credenciais['login']) || empty($credenciais['senha']))
        {
            throw new Exception('As credenciais fixas da integracao de cartao nao estao configuradas na classe da API.');
        }

        return $credenciais;
    }

    private static function obterOuCriarCidadeLoja(string $cidadeNome, string $uf): ?Cidade
    {
        $cidadeNome = trim($cidadeNome);
        $uf = strtoupper(trim($uf));

        if ($cidadeNome === '' || $uf === '')
        {
            return null;
        }

        $estado = Estado::where('sigla', '=', $uf)->first();
        if (!$estado)
        {
            $estado = new Estado();
            $estado->sigla = $uf;
            $estado->nome = $uf;
            $estado->store();
        }

        $cidade = Cidade::where('nome', '=', $cidadeNome)
            ->where('estado_id', '=', $estado->id)
            ->first();

        if (!$cidade)
        {
            $cidade = new Cidade();
            $cidade->nome = $cidadeNome;
            $cidade->estado_id = $estado->id;
            $cidade->store();
        }

        return $cidade;
    }

    private static function sincronizarPessoaLoja(array $loja, array &$cachePessoasLoja = []): ?int
    {
        $cnpj = self::normalizarDocumento($loja['cnpj'] ?? '');
        if (strlen($cnpj) !== 14)
        {
            return null;
        }

        if (isset($cachePessoasLoja[$cnpj]))
        {
            return $cachePessoasLoja[$cnpj];
        }

        $pessoa = Pessoa::where('documento', '=', $cnpj)->first();

        if (!$pessoa)
        {
            $pessoa = new Pessoa();
            $pessoa->nome = trim((string) (($loja['razao_social'] ?? '') ?: ($loja['nome'] ?? '') ?: ('LOJA ' . $cnpj)));
            $pessoa->documento = $cnpj;
            $pessoa->fone = trim((string) ($loja['telefone'] ?? ''));
            $pessoa->email = trim((string) ($loja['email'] ?? ''));
            $pessoa->ativo = 'T';
            $pessoa->tipo_cliente_id = TipoCliente::JURIDICA;
            $pessoa->system_unit_id = TSession::getValue('idunit');
            $pessoa->store();
        }
        else
        {
            $alterouPessoa = false;

            if (empty($pessoa->nome) && !empty($loja['razao_social']))
            {
                $pessoa->nome = trim((string) $loja['razao_social']);
                $alterouPessoa = true;
            }

            if (empty($pessoa->fone) && !empty($loja['telefone']))
            {
                $pessoa->fone = trim((string) $loja['telefone']);
                $alterouPessoa = true;
            }

            if (empty($pessoa->email) && !empty($loja['email']))
            {
                $pessoa->email = trim((string) $loja['email']);
                $alterouPessoa = true;
            }

            if (empty($pessoa->system_unit_id))
            {
                $pessoa->system_unit_id = TSession::getValue('idunit');
                $alterouPessoa = true;
            }

            if ($alterouPessoa)
            {
                $pessoa->store();
            }
        }

        if (!PessoaGrupo::where('pessoa_id', '=', $pessoa->id)->where('grupo_pessoa_id', '=', GrupoPessoa::FORNECEDOR)->first())
        {
            $pessoaGrupo = new PessoaGrupo();
            $pessoaGrupo->pessoa_id = $pessoa->id;
            $pessoaGrupo->grupo_pessoa_id = GrupoPessoa::FORNECEDOR;
            $pessoaGrupo->store();
        }

        $temDadosEndereco = !empty($loja['logradouro']) || !empty($loja['bairro']) || !empty($loja['cidade']) || !empty($loja['uf']) || !empty($loja['cep']);
        if ($temDadosEndereco && !PessoaEndereco::where('pessoa_id', '=', $pessoa->id)->where('principal', '=', 'T')->first())
        {
            $cidade = self::obterOuCriarCidadeLoja((string) ($loja['cidade'] ?? ''), (string) ($loja['uf'] ?? ''));

            $pessoaEndereco = new PessoaEndereco();
            $pessoaEndereco->pessoa_id = $pessoa->id;
            $pessoaEndereco->nome = trim((string) (($loja['nome'] ?? '') ?: ($loja['razao_social'] ?? '') ?: $pessoa->nome));
            $pessoaEndereco->principal = 'T';
            $pessoaEndereco->cep = self::normalizarDocumento($loja['cep'] ?? '');
            $pessoaEndereco->rua = trim((string) ($loja['logradouro'] ?? ''));
            $pessoaEndereco->numero = trim((string) ($loja['numero'] ?? ''));
            $pessoaEndereco->bairro = trim((string) ($loja['bairro'] ?? ''));
            $pessoaEndereco->complemento = trim((string) ($loja['complemento'] ?? ''));
            $pessoaEndereco->latitude = trim((string) ($loja['latitude'] ?? ''));
            $pessoaEndereco->longitude = trim((string) ($loja['longitude'] ?? ''));

            if ($cidade)
            {
                $pessoaEndereco->cidade_id = $cidade->id;
            }

            $pessoaEndereco->store();
        }

        $cachePessoasLoja[$cnpj] = (int) $pessoa->id;

        return (int) $pessoa->id;
    }

    private static function localizarPessoaPorNomeLoja(?string $nomeLoja): ?Pessoa
    {
        $nomeLoja = self::normalizarNome($nomeLoja);
        if ($nomeLoja === '')
        {
            return null;
        }

        $fornecedores = Pessoa::where('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE deleted_at is null AND grupo_pessoa_id = '" . GrupoPessoa::FORNECEDOR . "')")
            ->load();

        if (!$fornecedores)
        {
            return null;
        }

        foreach ($fornecedores as $fornecedor)
        {
            $nomeFornecedor = self::normalizarNome($fornecedor->nome ?? '');
            if ($nomeFornecedor === $nomeLoja)
            {
                return $fornecedor;
            }

            if ($nomeFornecedor !== '' && (str_contains($nomeFornecedor, $nomeLoja) || str_contains($nomeLoja, $nomeFornecedor)))
            {
                return $fornecedor;
            }
        }

        return null;
    }

    private static function localizarPessoaEstabelecimento(array $lojaAssociada, ?string $nomeLojaApi): ?Pessoa
    {
        $cnpj = self::normalizarDocumento($lojaAssociada['cnpj'] ?? '');
        if (strlen($cnpj) === 14)
        {
            $pessoa = Pessoa::where('documento', '=', $cnpj)->first();
            if ($pessoa)
            {
                return $pessoa;
            }
        }

        $pessoa = self::localizarPessoaPorNomeLoja($lojaAssociada['razao_social'] ?? '');
        if ($pessoa)
        {
            return $pessoa;
        }

        $pessoa = self::localizarPessoaPorNomeLoja($lojaAssociada['nome'] ?? '');
        if ($pessoa)
        {
            return $pessoa;
        }

        return self::localizarPessoaPorNomeLoja($nomeLojaApi);
    }

    private static function dispositivoSolicitadoEhCartao(DispositivosSolicitados $cadastro): bool
    {
        $descricao = self::normalizarNome($cadastro->dispositivos->descricao ?? '');
        if (strpos($descricao, 'CART') !== false)
        {
            return true;
        }

        return (float) ($cadastro->saldo_limite ?? 0) > 0;
    }

    private static function listarCadastrosCartao(?string $cpf = null, ?string $numeroCartao = null): array
    {
        $query = DispositivosSolicitados::where('deleted_at', 'is', null)
            ->where('numerocartao', 'is not', null);

        if (TSession::getValue('idunit'))
        {
            $query->where('system_unit_id', '=', TSession::getValue('idunit'));
        }

        $cadastros = $query->load() ?: [];
        $cpf = self::normalizarDocumento($cpf);
        $numeroCartao = self::normalizarIdentificadorCartao($numeroCartao);
        $retorno = [];

        foreach ($cadastros as $cadastro)
        {
            $numeroCadastro = self::normalizarIdentificadorCartao($cadastro->numerocartao ?? '');
            if ($numeroCadastro === '')
            {
                continue;
            }

            if (!self::dispositivoSolicitadoEhCartao($cadastro))
            {
                continue;
            }

            $pessoa = !empty($cadastro->pessoa_id) ? $cadastro->pessoa : null;
            $cpfPessoa = self::normalizarDocumento($pessoa->documento ?? '');

            if ($cpf !== '' && $cpfPessoa !== $cpf)
            {
                continue;
            }

            if ($numeroCartao !== '' && strpos($numeroCadastro, $numeroCartao) === false)
            {
                continue;
            }

            $retorno[] = $cadastro;
        }

        return $retorno;
    }

    private static function localizarCadastroCartaoPorNumero(array $cadastros, ?string $numeroCartao): ?DispositivosSolicitados
    {
        return self::localizarCadastroCartaoPorNumeros($cadastros, [$numeroCartao]);
    }

    private static function localizarCadastroCartaoPorNumeros(array $cadastros, array $numerosCartao): ?DispositivosSolicitados
    {
        $numerosNormalizados = [];

        foreach ($numerosCartao as $numeroCartao)
        {
            $numeroCartaoNormalizado = self::normalizarIdentificadorCartao($numeroCartao);
            if ($numeroCartaoNormalizado !== '')
            {
                $numerosNormalizados[$numeroCartaoNormalizado] = $numeroCartaoNormalizado;
            }
        }

        if (!$numerosNormalizados)
        {
            return null;
        }

        $numerosNormalizados = array_values($numerosNormalizados);
        $candidatosMesmoFinal = [];

        foreach ($numerosNormalizados as $numeroCartaoNormalizado)
        {
            foreach ($cadastros as $cadastro)
            {
                $numeroCadastro = self::normalizarIdentificadorCartao($cadastro->numerocartao ?? '');
                if ($numeroCadastro !== '' && self::numerosCartaoCoincidem($numeroCadastro, $numeroCartaoNormalizado))
                {
                    return $cadastro;
                }

                if ($numeroCadastro !== '' && self::finaisCartaoCoincidem($numeroCadastro, $numeroCartaoNormalizado, 6))
                {
                    $candidatosMesmoFinal[(int) $cadastro->id] = $cadastro;
                }
            }
        }

        if (count($candidatosMesmoFinal) === 1)
        {
            return array_values($candidatosMesmoFinal)[0];
        }

        if (count($cadastros) === 1)
        {
            return $cadastros[0];
        }

        foreach ($numerosNormalizados as $numeroCartaoNormalizado)
        {
            $cadastroGlobal = self::localizarCadastroCartaoGlobal($numeroCartaoNormalizado);
            if ($cadastroGlobal)
            {
                return $cadastroGlobal;
            }
        }

        return null;
    }

    private static function descreverCartoesCadastro(array $cadastros): array
    {
        $descricao = [];

        foreach ($cadastros as $cadastro)
        {
            $raw = trim((string) ($cadastro->numerocartao ?? ''));
            if ($raw === '')
            {
                continue;
            }

            $descricao[] = $raw . ' [' . self::normalizarIdentificadorCartao($raw) . ']';
        }

        return array_values(array_unique($descricao));
    }

    private static function descreverCartoesTentados(array $numerosCartao): array
    {
        $descricao = [];

        foreach ($numerosCartao as $numeroCartao)
        {
            $raw = trim((string) $numeroCartao);
            if ($raw === '')
            {
                continue;
            }

            $descricao[] = $raw . ' [' . self::normalizarIdentificadorCartao($raw) . ']';
        }

        return array_values(array_unique($descricao));
    }

    private static function localizarCadastroCartaoGlobal(string $numeroCartaoNormalizado): ?DispositivosSolicitados
    {
        $numeroCartaoNormalizado = self::normalizarIdentificadorCartao($numeroCartaoNormalizado);
        if ($numeroCartaoNormalizado === '')
        {
            return null;
        }

        $cadastros = self::listarCadastrosCartao();
        $candidatosMesmoFinal = [];

        foreach ($cadastros as $cadastro)
        {
            $numeroCadastro = self::normalizarIdentificadorCartao($cadastro->numerocartao ?? '');
            if ($numeroCadastro === '')
            {
                continue;
            }

            if (self::numerosCartaoCoincidem($numeroCadastro, $numeroCartaoNormalizado))
            {
                return $cadastro;
            }

            if (self::finaisCartaoCoincidem($numeroCadastro, $numeroCartaoNormalizado, 6))
            {
                $candidatosMesmoFinal[] = $cadastro;
            }
        }

        return count($candidatosMesmoFinal) === 1 ? $candidatosMesmoFinal[0] : null;
    }

    private static function finaisCartaoCoincidem(string $numeroCadastro, string $numeroApi, int $tamanhoMinimo = 4): bool
    {
        if ($numeroCadastro === '' || $numeroApi === '')
        {
            return false;
        }

        $tamanho = min(strlen($numeroCadastro), strlen($numeroApi));
        if ($tamanho < $tamanhoMinimo)
        {
            return false;
        }

        return substr($numeroCadastro, -$tamanho) === substr($numeroApi, -$tamanho);
    }

    private static function numerosCartaoCoincidem(string $numeroCadastro, string $numeroApi): bool
    {
        if ($numeroCadastro === '' || $numeroApi === '')
        {
            return false;
        }

        if ($numeroCadastro === $numeroApi)
        {
            return true;
        }

        if (str_contains($numeroCadastro, $numeroApi) || str_contains($numeroApi, $numeroCadastro))
        {
            return true;
        }

        if (self::finaisCartaoCoincidem($numeroCadastro, $numeroApi, 6))
        {
            return true;
        }

        $finalCadastro = substr($numeroCadastro, -4);
        $finalApi = substr($numeroApi, -4);
        if ($finalCadastro !== '' && $finalCadastro === $finalApi)
        {
            return true;
        }

        $inicioCadastro = substr($numeroCadastro, 0, 4);
        $inicioApi = substr($numeroApi, 0, 4);
        if (strlen($numeroCadastro) >= 8 && strlen($numeroApi) >= 8 && $inicioCadastro === $inicioApi && $finalCadastro === $finalApi)
        {
            return true;
        }

        return false;
    }

    private static function localizarVeiculoUsuarioCartao(?Pessoa $pessoa): ?Veiculos
    {
        if (!$pessoa || empty($pessoa->id))
        {
            return null;
        }

        $query = Veiculos::where('responsavel_id', '=', $pessoa->id)
            ->where('deleted_at', 'is', null);

        if (TSession::getValue('idunit'))
        {
            $query->where('system_unit_id', '=', TSession::getValue('idunit'));
        }

        $veiculo = $query->first();
        if ($veiculo)
        {
            return $veiculo;
        }

        $cpf = self::normalizarDocumento($pessoa->documento ?? '');
        if ($cpf !== '')
        {
            $condutor = Condutor::where('cpf', '=', $cpf)->first();
            if ($condutor && !empty($condutor->numero_dispositivo))
            {
                $queryVeiculo = Veiculos::where('numero_dispositivo', '=', $condutor->numero_dispositivo)
                    ->where('deleted_at', 'is', null);

                if (TSession::getValue('idunit'))
                {
                    $queryVeiculo->where('system_unit_id', '=', TSession::getValue('idunit'));
                }

                $veiculo = $queryVeiculo->first();
                if ($veiculo)
                {
                    return $veiculo;
                }
            }
        }

        return null;
    }

    private static function localizarLojaAssociada(array $conta, array $autorizacao): array
    {
        $codigoLoja = trim((string) ($autorizacao['cd_loja_autoriz'] ?? ''));
        $nomeLoja = trim((string) ($autorizacao['nm_loja'] ?? ''));

        foreach (($conta['lojas_credenciadas'] ?? []) as $loja)
        {
            if ($codigoLoja !== '' && trim((string) ($loja['codigo_loja'] ?? '')) === $codigoLoja)
            {
                return $loja;
            }
        }

        foreach (($conta['lojas_credenciadas'] ?? []) as $loja)
        {
            if (self::normalizarNome($loja['nome'] ?? '') === self::normalizarNome($nomeLoja))
            {
                return $loja;
            }
        }

        return [
            'codigo_loja' => $codigoLoja,
            'nome' => $nomeLoja,
            'razao_social' => $nomeLoja,
            'cnpj' => '',
            'cidade' => '',
            'uf' => '',
        ];
    }

    private static function formatarDataConsulta(?string $valor, bool $fim = false): string
    {
        $valor = trim((string) $valor);

        if ($valor === '')
        {
            return $fim ? date('d/m/Y') : date('01/m/Y');
        }

        $formatos = ['Y-m-d', 'd/m/Y', 'Y-m-d H:i:s', 'd/m/Y H:i:s'];
        foreach ($formatos as $formato)
        {
            $data = DateTime::createFromFormat($formato, $valor);
            if ($data instanceof DateTime)
            {
                return $data->format('d/m/Y');
            }
        }

        try
        {
            return (new DateTime($valor))->format('d/m/Y');
        }
        catch (Exception $e)
        {
            return $fim ? date('d/m/Y') : date('01/m/Y');
        }
    }

    private static function parseDataLancamento(?string $valor): DateTime
    {
        $valor = trim((string) $valor);
        $formatos = [
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'd/m/Y',
            'Y-m-d',
        ];

        foreach ($formatos as $formato)
        {
            $data = DateTime::createFromFormat($formato, $valor);
            if ($data instanceof DateTime)
            {
                return $data;
            }
        }

        try
        {
            return new DateTime($valor);
        }
        catch (Exception $e)
        {
            return new DateTime();
        }
    }

    private static function serializarLancamento(array $lancamento): string
    {
        return md5(json_encode([
            $lancamento['cpf'] ?? '',
            $lancamento['numero_cartao'] ?? '',
            $lancamento['id_autoriz'] ?? '',
            $lancamento['cd_autoriz'] ?? '',
            $lancamento['cd_loja_autoriz'] ?? '',
            $lancamento['dt_hora_autoriz'] ?? '',
            $lancamento['valor_total'] ?? 0,
        ]));
    }

    private static function obterFaixaStatusConsulta(): array
    {
        return ['0', '1', '2', '3'];
    }

    private static function autorizacaoEhValida(array $autorizacao): bool
    {
        $status = self::normalizarNome($autorizacao['tp_status'] ?? '');
        $codigoAutorizacao = trim((string) ($autorizacao['cd_autoriz'] ?? ''));

        if ($codigoAutorizacao === '' || $codigoAutorizacao === '0')
        {
            return false;
        }

        return str_contains($status, 'AUTORIZ');
    }

    public static function consultarLancamentos(array $filtros): array
    {
        $cpf = self::normalizarCpf($filtros['cpf'] ?? '');
        $numeroCartaoFiltro = trim((string) ($filtros['numero_cartao'] ?? ''));
        $dtInicial = self::formatarDataConsulta($filtros['dt_inicial'] ?? null, false);
        $dtFinal = self::formatarDataConsulta($filtros['dt_final'] ?? null, true);

        $credenciais = self::obterCredenciaisApi();
        $token = APICombustivel2::getPegarToken($credenciais['grupo'], $credenciais['login'], $credenciais['senha']);

        if (empty($token))
        {
            throw new Exception('Nao foi possivel obter o token da integracao de cartao.');
        }

        $cadastrosCartao = self::listarCadastrosCartao($cpf, $numeroCartaoFiltro);
        if (!$cadastrosCartao)
        {
            throw new Exception($cpf !== ''
                ? 'Nenhum cartao foi encontrado em dispositivos_solicitados para o CPF informado. Cadastre o cartao com limite e usuario vinculado.'
                : 'Nenhum cartao foi encontrado em dispositivos_solicitados para a unidade atual. Cadastre o cartao com limite e usuario vinculado.');
        }

        $cadastrosPorCpf = [];
        foreach ($cadastrosCartao as $cadastroCartao)
        {
            $pessoa = !empty($cadastroCartao->pessoa_id) ? $cadastroCartao->pessoa : null;
            $cpfPessoa = self::normalizarDocumento($pessoa->documento ?? '');

            if ($cpfPessoa === '')
            {
                continue;
            }

            $cadastrosPorCpf[$cpfPessoa][] = $cadastroCartao;
        }

        if (!$cadastrosPorCpf)
        {
            throw new Exception('Os cartoes encontrados em dispositivos_solicitados precisam ter um usuario vinculado com CPF informado.');
        }

        $cachePessoasLoja = [];
        $lancamentos = [];
        $metadados = [
            'cpf' => $cpf,
            'dt_inicial' => $dtInicial,
            'dt_final' => $dtFinal,
            'status_consultados' => [],
            'qtd_contas' => 0,
            'qtd_autorizacoes_brutas' => 0,
            'qtd_autorizacoes_vinculadas' => 0,
            'erros_api' => [],
            'cartoes_nao_vinculados' => [],
            'qtd_usuarios_consultados' => count($cadastrosPorCpf),
            'qtd_cartoes_cadastrados' => count($cadastrosCartao),
        ];
        $contasIndexadas = [];

        foreach ($cadastrosPorCpf as $cpfPessoa => $cadastrosUsuario)
        {
            foreach (self::obterFaixaStatusConsulta() as $tpSituacao)
            {
                $resultado = APICombustivel2::getApp_ListarContasPorCPF(
                    $token,
                    $credenciais['grupo'],
                    $credenciais['login'],
                    $credenciais['senha'],
                    $cpfPessoa,
                    $dtInicial,
                    $dtFinal,
                    $tpSituacao
                );

                if (!in_array($tpSituacao, $metadados['status_consultados'], true))
                {
                    $metadados['status_consultados'][] = $tpSituacao;
                }
                $metadados['qtd_contas'] += count($resultado['contas'] ?? []);

                foreach (($resultado['contas'] ?? []) as $conta)
                {
                    $erroConta = $conta['erro'] ?? [];
                    if (!empty($erroConta['cod']) || !empty($erroConta['msg']))
                    {
                        $metadados['erros_api'][] = [
                            'cartao' => $conta['cartao'] ?? '',
                            'status_consulta' => $tpSituacao,
                            'cod' => $erroConta['cod'] ?? '',
                            'msg' => $erroConta['msg'] ?? '',
                            'cpf' => $cpfPessoa,
                        ];
                    }

                    $cartaoKey = trim((string) ($conta['cartao'] ?? '')) . '|' . $cpfPessoa;
                    if (!isset($contasIndexadas[$cartaoKey]))
                    {
                        $contasIndexadas[$cartaoKey] = $conta;
                    }
                    elseif (!empty($conta['autorizacoes']))
                    {
                        $contasIndexadas[$cartaoKey]['autorizacoes'] = array_merge(
                            $contasIndexadas[$cartaoKey]['autorizacoes'] ?? [],
                            $conta['autorizacoes']
                        );
                    }

                    foreach (($conta['autorizacoes'] ?? []) as $autorizacao)
                    {
                        $metadados['qtd_autorizacoes_brutas']++;

                        if (!self::autorizacaoEhValida($autorizacao))
                        {
                            continue;
                        }

                        $numerosCartaoTentativa = [
                            $autorizacao['nu_cartao'] ?? '',
                            $conta['cartao'] ?? '',
                            $conta['dados_cadastrais']['cartao'] ?? '',
                        ];
                        $numeroCartao = trim((string) (($autorizacao['nu_cartao'] ?? '') ?: ($conta['cartao'] ?? '') ?: ($conta['dados_cadastrais']['cartao'] ?? '')));
                        if ($numeroCartaoFiltro !== '' && stripos($numeroCartao, $numeroCartaoFiltro) === false)
                        {
                            continue;
                        }

                        $lojaAssociada = self::localizarLojaAssociada($conta, $autorizacao);
                        $estabelecimentoId = self::sincronizarPessoaLoja($lojaAssociada, $cachePessoasLoja);

                        if (!$estabelecimentoId)
                        {
                            $fornecedor = self::localizarPessoaEstabelecimento($lojaAssociada, $autorizacao['nm_loja'] ?? '');
                            $estabelecimentoId = $fornecedor ? (int) $fornecedor->id : null;
                        }

                        $cadastroCartao = self::localizarCadastroCartaoPorNumeros($cadastrosUsuario, $numerosCartaoTentativa);
                        if (!$cadastroCartao)
                        {
                            if (count($metadados['cartoes_nao_vinculados'] ?? []) < 5)
                            {
                                $metadados['cartoes_nao_vinculados'][] = [
                                    'cpf' => $cpfPessoa,
                                    'api' => self::descreverCartoesTentados($numerosCartaoTentativa),
                                    'cadastro' => self::descreverCartoesCadastro($cadastrosUsuario),
                                ];
                            }
                            continue;
                        }
                        $metadados['qtd_autorizacoes_vinculadas']++;

                        $pessoaUsuarioCartao = !empty($cadastroCartao->pessoa_id) ? $cadastroCartao->pessoa : null;
                        $veiculoUsuarioCartao = self::localizarVeiculoUsuarioCartao($pessoaUsuarioCartao);

                        if (!$veiculoUsuarioCartao && !empty($cadastroCartao->veiculos_id))
                        {
                            $veiculoUsuarioCartao = new Veiculos((int) $cadastroCartao->veiculos_id);
                        }

                        $valorTotal = self::normalizarValorLancamento($autorizacao['vl_autoriz'] ?? 0);
                        $lancamento = [
                            'cpf' => $cpfPessoa,
                            'numero_cartao' => $numeroCartao,
                            'dispositivos_solicitados_id' => (int) $cadastroCartao->id,
                            'rede' => (string) ($conta['rede'] ?? ''),
                            'cd_cliente' => (string) ($conta['cd_cliente'] ?? ''),
                            'id_autoriz' => trim((string) ($autorizacao['id_autoriz'] ?? '')),
                            'cd_autoriz' => trim((string) ($autorizacao['cd_autoriz'] ?? '')),
                            'cd_loja_autoriz' => trim((string) ($autorizacao['cd_loja_autoriz'] ?? '')),
                            'nm_loja' => trim((string) ($autorizacao['nm_loja'] ?? '')),
                            'dt_hora_autoriz' => trim((string) ($autorizacao['dt_hora_autoriz'] ?? '')),
                            'valor_total' => $valorTotal,
                            'tp_status' => trim((string) ($autorizacao['tp_status'] ?? '')),
                            'de_motivo_recusa' => trim((string) ($autorizacao['de_motivo_recusa'] ?? '')),
                            'fl_cancel_permitido' => trim((string) ($autorizacao['fl_cancel_permitido'] ?? '')),
                            'loja' => $lojaAssociada,
                            'estabelecimento_id' => $estabelecimentoId,
                            'estabelecimento_nome' => $estabelecimentoId ? ((new Pessoa($estabelecimentoId))->nome ?? '') : '',
                            'usuario_cartao_id' => (int) ($pessoaUsuarioCartao->id ?? 0),
                            'usuario_cartao_nome' => $pessoaUsuarioCartao->nome ?? '',
                            'usuario_cartao_documento' => $pessoaUsuarioCartao->documento ?? $cpfPessoa,
                            'veiculos_id' => $veiculoUsuarioCartao->id ?? null,
                            'veiculo_placa' => $veiculoUsuarioCartao->placa ?? '',
                            'veiculo_descricao' => $veiculoUsuarioCartao ? trim(($veiculoUsuarioCartao->placa ?? '') . ' - ' . ($veiculoUsuarioCartao->marca->descricao ?? '') . ' - ' . ($veiculoUsuarioCartao->modelo->descricao ?? '')) : '',
                            'saldo_atual' => round((float) ($cadastroCartao->saldo_atual ?? 0), 2),
                            'saldo_limite' => round((float) ($cadastroCartao->saldo_limite ?? 0), 2),
                        ];

                        $lancamento['launch_key'] = self::serializarLancamento($lancamento);
                        $lancamentos[$lancamento['launch_key']] = $lancamento;
                    }
                }
            }
        }

        uasort($lancamentos, function (array $a, array $b) {
            return strcmp(
                self::parseDataLancamento($b['dt_hora_autoriz'] ?? 'now')->format('Y-m-d H:i:s'),
                self::parseDataLancamento($a['dt_hora_autoriz'] ?? 'now')->format('Y-m-d H:i:s')
            );
        });

        TSession::setValue(self::SESSION_KEY, $lancamentos);
        $metadados['contas'] = array_values(array_map(function (array $conta) {
            return [
                'cartao' => $conta['cartao'] ?? '',
                'rede' => $conta['rede'] ?? '',
                'cd_cliente' => $conta['cd_cliente'] ?? '',
                'qtd_autorizacoes' => count($conta['autorizacoes'] ?? []),
                'erro' => $conta['erro'] ?? [],
            ];
        }, $contasIndexadas));
        TSession::setValue(self::SESSION_META_KEY, $metadados);

        return array_values($lancamentos);
    }

    public static function obterLancamentoSessao(string $launchKey): array
    {
        $launches = TSession::getValue(self::SESSION_KEY) ?? [];
        $lancamento = $launches[$launchKey] ?? null;

        if (!$lancamento)
        {
            throw new Exception('O lancamento selecionado nao foi encontrado na sessao. Consulte a API novamente.');
        }

        return $lancamento;
    }

    private static function obterCidadePessoaId(?int $pessoaId): ?int
    {
        if (empty($pessoaId))
        {
            return null;
        }

        $endereco = PessoaEndereco::where('pessoa_id', '=', $pessoaId)
            ->where('principal', '=', 'S')
            ->first();

        if (!$endereco)
        {
            $endereco = PessoaEndereco::where('pessoa_id', '=', $pessoaId)
                ->where('principal', '=', 'T')
                ->first();
        }

        if (!$endereco)
        {
            $endereco = PessoaEndereco::where('pessoa_id', '=', $pessoaId)->first();
        }

        return $endereco ? (int) $endereco->cidade_id : null;
    }

    private static function obterCondutorVeiculoId(Veiculos $veiculo): ?int
    {
        if (empty($veiculo->responsavel_id))
        {
            return null;
        }

        $responsavel = new Pessoa($veiculo->responsavel_id);
        if (empty($responsavel->nome))
        {
            return null;
        }

        $criteria = Condutor::where('nome', '=', $responsavel->nome);
        if (!empty($veiculo->system_unit_id))
        {
            $criteria->where('system_unit_id', '=', $veiculo->system_unit_id);
        }

        $condutor = $criteria->first();
        if (!$condutor)
        {
            $condutor = Condutor::where('nome', '=', $responsavel->nome)->first();
        }

        return $condutor ? (int) $condutor->id : null;
    }

    private static function obterCondutorPessoaId(?Pessoa $pessoa, ?int $systemUnitId = null): ?int
    {
        if (!$pessoa || empty($pessoa->nome))
        {
            return null;
        }

        $cpf = self::normalizarDocumento($pessoa->documento ?? '');
        if ($cpf !== '')
        {
            $criteria = Condutor::where('cpf', '=', $cpf);
            if ($systemUnitId)
            {
                $criteria->where('system_unit_id', '=', $systemUnitId);
            }

            $condutor = $criteria->first();
            if ($condutor)
            {
                return (int) $condutor->id;
            }
        }

        $criteria = Condutor::where('nome', '=', $pessoa->nome);
        if ($systemUnitId)
        {
            $criteria->where('system_unit_id', '=', $systemUnitId);
        }

        $condutor = $criteria->first();
        if (!$condutor)
        {
            $condutor = Condutor::where('nome', '=', $pessoa->nome)->first();
        }

        return $condutor ? (int) $condutor->id : null;
    }

    private static function obterAprovadorIdPorUsuario($systemUsersId): ?int
    {
        if (empty($systemUsersId))
        {
            return null;
        }

        $aprovador = AprovadorFrotas::where('system_users_id', '=', $systemUsersId)->first();

        return $aprovador ? (int) $aprovador->id : null;
    }

    private static function obterTaxasPessoa(?int $pessoaId, ?int $entidadeId, ?int $systemUnitId)
    {
        $taxasPessoa = TaxasPessoa::where('pessoa_id', '=', $pessoaId)
            ->where('deleted_at', 'is', null)
            ->where('entidade_id', '=', $entidadeId)
            ->where('system_unit_id', '=', $systemUnitId)
            ->first();

        return $taxasPessoa ?: new stdClass();
    }

    private static function obterSaldoBaseDotacao(SaldoDepartamento $saldoDepartamento): float
    {
        $saldoProduto = (float) ($saldoDepartamento->saldo_produto ?? 0);
        if ($saldoProduto > 0)
        {
            return $saldoProduto;
        }

        return (float) ($saldoDepartamento->saldo_total ?? 0);
    }

    private static function calcularSaldoDisponivelDotacao(SaldoDepartamento $saldoDepartamento, ?int $ignorarPedidoFrotasId = null): float
    {
        $saldoDisponivel = self::obterSaldoBaseDotacao($saldoDepartamento);

        $subquery = 'SELECT id FROM pedido_frotas WHERE estado_pedido_frotas_id IN (' .
            EstadoPedidoFrotas::APROVADO . ',' .
            EstadoPedidoFrotas::FINALIZADO . ',' .
            EstadoPedidoFrotas::ENTREGUE . ',' .
            EstadoPedidoFrotas::PGTOAPROVADO . ')';

        if (!empty($saldoDepartamento->departamento_unit_id))
        {
            $subquery .= ' AND departamento_unit_id = ' . (int) $saldoDepartamento->departamento_unit_id;
        }

        $dotacoes = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $saldoDepartamento->id)
            ->where('pedido_frotas_id', 'IN', '(' . $subquery . ')')
            ->load();

        if ($dotacoes)
        {
            foreach ($dotacoes as $dotacao)
            {
                if ($ignorarPedidoFrotasId && (int) $dotacao->pedido_frotas_id === $ignorarPedidoFrotasId)
                {
                    continue;
                }

                $saldoDisponivel -= (float) ($dotacao->valor ?? 0);
            }
        }

        return round($saldoDisponivel, 2);
    }

    private static function localizarDotacaoParaPedido(PedidoFrotas $pedido, float $valorTotal): SaldoDepartamento
    {
        if (empty($pedido->departamento_unit_id))
        {
            throw new Exception('O veiculo informado nao possui departamento definido para localizar a dotacao.');
        }

        $dotacoesQuery = SaldoDepartamento::where('departamento_unit_id', '=', $pedido->departamento_unit_id);

        if (!empty($pedido->entidade_id))
        {
            $dotacoesQuery->where(
                'saldo_entidade_contrato_id',
                'in',
                '(SELECT id FROM saldo_entidade_contrato WHERE deleted_at is null AND entidade_id = ' . (int) $pedido->entidade_id . ')'
            );
        }

        $dotacoes = $dotacoesQuery
            ->orderBy('datatransacao')
            ->orderBy('id')
            ->load();

        if (!$dotacoes)
        {
            throw new Exception('Nenhuma dotacao foi encontrada para o departamento deste lancamento.');
        }

        $dotacaoSelecionada = null;
        $maiorSaldoEncontrado = 0.0;

        foreach ($dotacoes as $dotacao)
        {
            $saldoDisponivel = self::calcularSaldoDisponivelDotacao($dotacao, $pedido->id);

            if ($saldoDisponivel > $maiorSaldoEncontrado)
            {
                $maiorSaldoEncontrado = $saldoDisponivel;
            }

            if ($saldoDisponivel >= $valorTotal)
            {
                $dotacaoSelecionada = $dotacao;
                break;
            }
        }

        if (!$dotacaoSelecionada)
        {
            throw new Exception(
                'Nenhuma dotacao com saldo disponivel foi encontrada para o departamento deste lancamento. ' .
                'Maior saldo identificado: R$ ' . number_format($maiorSaldoEncontrado, 2, ',', '.')
            );
        }

        return $dotacaoSelecionada;
    }

    private static function registrarDotacaoPedidoFrotas(PedidoFrotas $pedido, Propostas $proposta, SaldoDepartamento $dotacao, float $valorTotal): void
    {
        $saldoDisponivel = self::calcularSaldoDisponivelDotacao($dotacao, $pedido->id);

        if ($saldoDisponivel < $valorTotal)
        {
            throw new Exception(
                'A dotacao selecionada nao possui saldo suficiente. Saldo disponivel: R$ ' .
                number_format($saldoDisponivel, 2, ',', '.')
            );
        }

        $pedido->saldo_departamento_id = $dotacao->id;
        $pedido->store();

        $dotacaoPedido = new DotacaoPedidoFrotas();
        $dotacaoPedido->pedido_frotas_id = $pedido->id;
        $dotacaoPedido->propostas_id = $proposta->id;
        $dotacaoPedido->saldo_departamento_id = $dotacao->id;
        $dotacaoPedido->valor = $valorTotal;
        $dotacaoPedido->saldo_atual = round($saldoDisponivel - $valorTotal, 2);
        $dotacaoPedido->store();
    }

    private static function lancamentoJaImportado(array $lancamento): bool
    {
        return self::localizarPedidoImportado($lancamento) !== null;
    }

    private static function localizarPedidoImportado(array $lancamento): ?PedidoFrotas
    {
        foreach (self::obterMarcadoresDuplicidadeLancamento($lancamento) as $marcador)
        {
            $query = PedidoFrotas::where('obs', 'like', '%' . $marcador . '%');

            if (TSession::getValue('idunit'))
            {
                $query->where('system_unit_id', '=', TSession::getValue('idunit'));
            }

            if ($query->first())
            {
                return $query->first();
            }
        }

        return null;
    }

    private static function obterMarcadoresDuplicidadeLancamento(array $lancamento): array
    {
        $marcadores = [];
        $codigoAutorizacao = trim((string) ($lancamento['cd_autoriz'] ?? ''));
        $idAutorizacao = trim((string) ($lancamento['id_autoriz'] ?? ''));

        if ($codigoAutorizacao !== '')
        {
            $marcadores[] = 'NACAO_CARD_AUTORIZACAO=' . $codigoAutorizacao;
            $marcadores[] = 'NACAO_CARD_CD_AUTORIZ=' . $codigoAutorizacao;
        }

        if ($idAutorizacao !== '')
        {
            $marcadores[] = 'NACAO_CARD_ID_AUTORIZ=' . $idAutorizacao;
        }

        return array_values(array_unique($marcadores));
    }

    private static function montarObservacaoIntegracao(array $lancamento, ?string $obsAdicional = null): string
    {
        $linhas = [];
        $linhas[] = 'Origem integracao cartao';
        $codigoAutorizacao = trim((string) ($lancamento['cd_autoriz'] ?? ''));
        $idAutorizacao = trim((string) ($lancamento['id_autoriz'] ?? ''));
        if ($codigoAutorizacao !== '')
        {
            $linhas[] = 'NACAO_CARD_AUTORIZACAO=' . $codigoAutorizacao;
            $linhas[] = 'NACAO_CARD_CD_AUTORIZ=' . $codigoAutorizacao;
        }
        if ($idAutorizacao !== '')
        {
            $linhas[] = 'NACAO_CARD_ID_AUTORIZ=' . $idAutorizacao;
        }
        $linhas[] = 'NACAO_CARD_CARTAO=' . trim((string) ($lancamento['numero_cartao'] ?? ''));
        if (!empty($lancamento['dispositivos_solicitados_id']))
        {
            $linhas[] = 'DISPOSITIVO_SOLICITADO_ID=' . trim((string) $lancamento['dispositivos_solicitados_id']);
        }
        $linhas[] = 'NACAO_CARD_LOJA=' . trim((string) ($lancamento['nm_loja'] ?? ''));
        $linhas[] = 'NACAO_CARD_STATUS=' . trim((string) ($lancamento['tp_status'] ?? ''));
        if (!empty($lancamento['usuario_cartao_nome']))
        {
            $linhas[] = 'USUARIO_CARTAO=' . trim((string) $lancamento['usuario_cartao_nome']);
        }
        if (!empty($lancamento['usuario_cartao_documento']))
        {
            $linhas[] = 'USUARIO_CARTAO_DOC=' . trim((string) $lancamento['usuario_cartao_documento']);
        }
        if (array_key_exists('saldo_limite', $lancamento))
        {
            $linhas[] = 'LIMITE_CARTAO=' . number_format((float) $lancamento['saldo_limite'], 2, '.', '');
        }

        if (!empty($lancamento['de_motivo_recusa']))
        {
            $linhas[] = 'NACAO_CARD_MOTIVO=' . trim((string) $lancamento['de_motivo_recusa']);
        }

        if (!empty($obsAdicional))
        {
            $linhas[] = trim($obsAdicional);
        }

        return implode(PHP_EOL, $linhas);
    }

    public static function registrarLancamentosAutomaticamente(array $lancamentos): array
    {
        $resultado = [
            'importados' => 0,
            'ja_importados' => 0,
            'ignorados_sem_cadastro' => 0,
            'erros' => [],
            'pedidos_ids' => [],
        ];

        foreach ($lancamentos as $lancamento)
        {
            if (empty($lancamento['launch_key']))
            {
                continue;
            }

            try
            {
                TTransaction::open('minierp');

                if (self::lancamentoJaImportado($lancamento))
                {
                    $resultado['ja_importados']++;
                    TTransaction::close();
                    continue;
                }

                $veiculoId = (int) ($lancamento['veiculos_id'] ?? 0);
                $estabelecimentoId = (int) ($lancamento['estabelecimento_id'] ?? 0);
                if ($veiculoId <= 0 || $estabelecimentoId <= 0)
                {
                    $resultado['ignorados_sem_cadastro']++;
                    TTransaction::close();
                    continue;
                }

                $retorno = self::registrarLancamento([
                    'launch_key' => $lancamento['launch_key'],
                    'veiculos_id' => $veiculoId,
                    'estabelecimento_id' => $estabelecimentoId,
                ]);
                TTransaction::close();

                $resultado['importados']++;
                $resultado['pedidos_ids'][] = $retorno['pedido_id'] ?? null;
            }
            catch (Exception $e)
            {
                if (TTransaction::get())
                {
                    TTransaction::rollback();
                }

                if (count($resultado['erros']) < 5)
                {
                    $resultado['erros'][] = [
                        'autorizacao' => trim((string) ($lancamento['cd_autoriz'] ?? '')),
                        'mensagem' => $e->getMessage(),
                    ];
                }
            }
        }

        $resultado['pedidos_ids'] = array_values(array_filter(array_unique($resultado['pedidos_ids'])));

        return $resultado;
    }

    public static function obterPedidoImportadoPorLaunchKey(string $launchKey): ?PedidoFrotas
    {
        $launchKey = trim($launchKey);
        if ($launchKey === '')
        {
            return null;
        }

        $lancamento = self::obterLancamentoSessao($launchKey);

        return self::localizarPedidoImportado($lancamento);
    }

    public static function registrarLancamento(array $dados): array
    {
        $launchKey = trim((string) ($dados['launch_key'] ?? ''));
        if ($launchKey === '')
        {
            throw new Exception('Lancamento do cartao nao informado.');
        }

        $lancamento = self::obterLancamentoSessao($launchKey);
        if (self::lancamentoJaImportado($lancamento))
        {
            throw new Exception('Este lancamento do cartao ja foi importado para pedido_frotas.');
        }

        $veiculoId = (int) ($dados['veiculos_id'] ?? 0);
        $estabelecimentoId = (int) (($dados['estabelecimento_id'] ?? 0) ?: ($lancamento['estabelecimento_id'] ?? 0));
        if ($veiculoId <= 0)
        {
            throw new Exception('Informe o veiculo para continuar.');
        }

        if ($estabelecimentoId <= 0)
        {
            throw new Exception('Informe o estabelecimento para continuar.');
        }

        $veiculo = new Veiculos($veiculoId);
        if (empty($veiculo->id))
        {
            throw new Exception('Veiculo informado nao foi encontrado.');
        }

        $cidadeId = self::obterCidadePessoaId($estabelecimentoId);
        if (empty($cidadeId))
        {
            throw new Exception('O estabelecimento selecionado precisa ter cidade cadastrada para gerar a proposta.');
        }

        $valorTotal = self::normalizarValorLancamento($lancamento['valor_total'] ?? 0);
        if ($valorTotal <= 0)
        {
            throw new Exception('O lancamento selecionado nao possui valor valido para gerar o pedido.');
        }

        $dataPedido = self::parseDataLancamento($lancamento['dt_hora_autoriz'] ?? 'now');
        $systemUsersId = $dados['system_users_id'] ?? TSession::getValue('userid');
        $entidadeId = $dados['entidade_id'] ?? TSession::getValue('entidade');
        $systemUnitId = $veiculo->system_unit_id ?: ($dados['system_unit_id'] ?? TSession::getValue('idunit'));
        $descricaoPedido = trim((string) ($dados['descricaopedido'] ?? ''));
        if ($descricaoPedido === '')
        {
            $descricaoPedido = 'Lancamento cartao - ' . trim((string) ($lancamento['nm_loja'] ?? ''));
        }

        $calculoTaxaContrato = self::calcularTotaisComTaxaContrato(
            $valorTotal,
            1,
            $dados['taxa_contrato'] ?? TSession::getValue('taxacontrato') ?? 0
        );

        $pessoaUsuarioCartao = !empty($lancamento['usuario_cartao_id']) ? new Pessoa((int) $lancamento['usuario_cartao_id']) : null;
        $condutorId = self::obterCondutorPessoaId($pessoaUsuarioCartao, $systemUnitId) ?: self::obterCondutorVeiculoId($veiculo);
        $aprovadorId = self::obterAprovadorIdPorUsuario($systemUsersId);
        $obs = self::montarObservacaoIntegracao($lancamento, $dados['obs'] ?? null);
        $descricaoItem = trim((string) (($dados['descricao_item'] ?? '') ?: ('Lancamento cartao - ' . trim((string) ($lancamento['nm_loja'] ?? '')))));
        $marcaModelo = trim($veiculo->placa . ' - ' . ($veiculo->marca->descricao ?? '') . ' - ' . ($veiculo->modelo->descricao ?? ''));

        $pedido = new PedidoFrotas();
        $pedido->dt_pedido = $dataPedido->format('Y-m-d H:i:s');
        $pedido->descricaopedido = $descricaoPedido;
        $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::FINALIZADO;
        $pedido->tipo_manutencao_id = 174;
        $pedido->veiculos_id = $veiculo->id;
        $pedido->estabelecimento_id = $estabelecimentoId;
        $pedido->cidade_id = $cidadeId;
        $pedido->km = $dados['km'] ?? null;
        $pedido->obs = $obs;
        $pedido->mes = $dataPedido->format('m');
        $pedido->ano = $dataPedido->format('Y');
        $pedido->data_limite_resposta = $dataPedido->format('Y-m-d H:i:s');
        $pedido->dt_finalizacao = $dataPedido->format('Y-m-d');
        $pedido->condutor_entrada_id = $condutorId;
        $pedido->condutor_retirada_id = $condutorId;
        $pedido->dataentrada = $dataPedido->format('Y-m-d H:i:s');
        $pedido->dataretirada = $dataPedido->format('Y-m-d H:i:s');
        $pedido->valor_total = $valorTotal;
        $pedido->valor_total_proposta = $valorTotal;
        $pedido->valor_desconto_proposta = $calculoTaxaContrato['desconto'];
        $pedido->valor_liquido_proposta = $calculoTaxaContrato['valor_liquido'];
        $pedido->system_unit_id = $systemUnitId;
        $pedido->departamento_unit_id = $veiculo->departamento_unit_id;
        $pedido->system_users_id = $systemUsersId;
        $pedido->entidade_id = $entidadeId;
        $pedido->abastecimento = 1;
        $pedido->dispositivos_solicitados_id = (int) ($lancamento['dispositivos_solicitados_id'] ?? 0) ?: null;
        $pedido->store();

        $itemPedido = new ItensPedidoFrotas();
        $itemPedido->pedido_frotas_id = $pedido->id;
        $itemPedido->tipo = 1;
        $itemPedido->qtde = 1;
        $itemPedido->descricao = $descricaoItem;
        $itemPedido->valor_unitario = $valorTotal;
        $itemPedido->valor_desconto = $calculoTaxaContrato['desconto'];
        $itemPedido->valor_total = $calculoTaxaContrato['valor_liquido'];
        $itemPedido->marca_modelo = $marcaModelo;
        $itemPedido->created_at = date('Y-m-d H:i:s');
        $itemPedido->updated_at = date('Y-m-d H:i:s');
        $itemPedido->store();

        $historicoPedido = new PedidoFrotasHistorico();
        $historicoPedido->pedido_frotas_id = $pedido->id;
        $historicoPedido->data_operacao = $dataPedido->format('Y-m-d H:i:s');
        $historicoPedido->aprovador_frotas_id = $aprovadorId;
        $historicoPedido->obs = $pedido->obs;
        $historicoPedido->estado_pedido_frotas_id = EstadoPedidoFrotas::FINALIZADO;
        $historicoPedido->store();

        $pedidoAsCliente = new PedidoAsCliente();
        $pedidoAsCliente->pedido_frotas_id = $pedido->id;
        $pedidoAsCliente->pessoa_id = $estabelecimentoId;
        $pedidoAsCliente->store();

        $proposta = new Propostas();
        $proposta->pedido_frotas_id = $pedido->id;
        $proposta->pessoa_id = $estabelecimentoId;
        $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::FINALIZADO;
        $proposta->veiculos_id = $veiculo->id;
        $proposta->placa = $veiculo->placa;
        $proposta->modelo = $veiculo->modelo->descricao ?? '';
        $proposta->data_cotacao = $dataPedido->format('Y-m-d');
        $proposta->data_limite_resposta = $dataPedido->format('Y-m-d');
        $proposta->obs = $pedido->obs;
        $proposta->valor_total = $valorTotal;
        $proposta->valor_desconto = $calculoTaxaContrato['desconto'];
        $proposta->valor_liquido = $calculoTaxaContrato['valor_liquido'];
        $proposta->system_unit_id = $pedido->system_unit_id;
        $proposta->departamento_unit_id = $pedido->departamento_unit_id;
        $proposta->system_users_id = $systemUsersId;
        $proposta->data_entrada_veiculo = $dataPedido->format('Y-m-d H:i:s');
        $proposta->data_retirada_veiculo = $dataPedido->format('Y-m-d H:i:s');
        $proposta->data_previsao_entrega = $dataPedido->format('Y-m-d');
        $proposta->motorista_entrada_id = $condutorId;
        $proposta->motorista_retirada_id = $condutorId;
        $proposta->km = $dados['km'] ?? null;
        $proposta->cidade_id = $cidadeId;
        $proposta->entidade_id = $entidadeId;
        $proposta->total_produtos_sem_desconto = $valorTotal;
        $proposta->total_servicos_sem_desconto = 0;
        $proposta->total_geral_sem_desconto = $valorTotal;
        $proposta->total_produtos_com_desconto = $calculoTaxaContrato['valor_liquido'];
        $proposta->desconto_contratual = $calculoTaxaContrato['taxa_percentual'];
        $proposta->total_servicos_com_desconto = 0;
        $proposta->total_geral_com_desconto = $calculoTaxaContrato['valor_liquido'];
        $proposta->abastecimento = 1;
        $proposta->store();

        $dotacao = self::localizarDotacaoParaPedido($pedido, $valorTotal);
        self::registrarDotacaoPedidoFrotas($pedido, $proposta, $dotacao, $valorTotal);

        $historicoProposta = new PropostasHistorico();
        $historicoProposta->propostas_id = $proposta->id;
        $historicoProposta->estado_pedido_frotas_id = EstadoPedidoFrotas::FINALIZADO;
        $historicoProposta->aprovador_frotas_id = $aprovadorId;
        $historicoProposta->data_historico = $dataPedido->format('Y-m-d H:i:s');
        $historicoProposta->obs = $proposta->obs;
        $historicoProposta->store();

        $itemProposta = new ItensPropostas();
        $itemProposta->propostas_id = $proposta->id;
        $itemProposta->tipo = 1;
        $itemProposta->descricao = $descricaoItem;
        $itemProposta->qtde = 1;
        $itemProposta->valor = $valorTotal;
        $itemProposta->perc_desconto = $calculoTaxaContrato['desconto'];
        $itemProposta->valor_total = $calculoTaxaContrato['valor_liquido'];
        $itemProposta->marca_modelo = $marcaModelo;
        $itemProposta->itens_pedido_frotas_id = $itemPedido->id;
        $itemProposta->estado_pedido_frotas_id = EstadoPedidoFrotas::FINALIZADO;
        $itemProposta->created_at = date('Y-m-d H:i:s');
        $itemProposta->updated_at = date('Y-m-d H:i:s');
        $itemProposta->store();

        $conta = new Conta();
        $taxasPessoa = self::obterTaxasPessoa($estabelecimentoId, $pedido->entidade_id, $pedido->system_unit_id);
        $imp = CalculoTaxasImpostosService::montarContextoConta($pedido, $valorTotal, 0, $taxasPessoa);
        if (($imp['bruto'] ?? 0) > 0 && $calculoTaxaContrato['desconto'] > 0)
        {
            $imp['perc_tx_contrato'] = ($calculoTaxaContrato['desconto'] / $imp['bruto']) * 100;
        }
        $imp['valor_txcontrato_fixado'] = CalculoTaxasImpostosService::money($calculoTaxaContrato['desconto']);
        $calcConta = CalculoTaxasImpostosService::calcularPorContexto($imp);

        $conta->pessoa_id = $estabelecimentoId;
        $conta->forma_pagamento_id = 1;
        $conta->pedido_frotas_id = $pedido->id;
        $conta->dt_emissao = date('Y-m-d');
        $conta->dt_vencimento = self::calcularVencimentoFinanceiro($conta->dt_emissao);
        $conta->mes_vencimento = intval(substr($conta->dt_vencimento, 5, 2));
        $conta->ano_vencimento = intval(substr($conta->dt_vencimento, 0, 4));
        $conta->ano_mes_vencimento = intval(substr($conta->dt_vencimento, 0, 4) . substr($conta->dt_vencimento, 5, 2));
        $conta->valor_produto_s_desc_txc = CalculoTaxasImpostosService::money($valorTotal);
        $conta->valor_servico_s_desc_txc = 0;
        $conta->valor = CalculoTaxasImpostosService::money($imp['bruto'] ?? 0);
        $conta->valor_txcontrato = CalculoTaxasImpostosService::money($calcConta['valor_txcontrato'] ?? 0);
        $conta->valor_liquido = CalculoTaxasImpostosService::money($calcConta['base_pos_txcontrato'] ?? 0);
        $conta->valor_produto_c_desc_txc = $calcConta['valor_produto_c_desc_txc'] ?? 0;
        $conta->valor_servico_c_desc_txc = $calcConta['valor_servico_c_desc_txc'] ?? 0;
        $conta->ir = $imp['impostos']['ir'] ?? 0;
        $conta->csll = $imp['impostos']['csll'] ?? 0;
        $conta->cofins = $imp['impostos']['cofins'] ?? 0;
        $conta->pis = $imp['impostos']['pis'] ?? 0;
        $conta->ir_servico = $imp['impostos']['ir_servico'] ?? 0;
        $conta->csll_servico = $imp['impostos']['csll_servico'] ?? 0;
        $conta->cofins_servico = $imp['impostos']['cofins_servico'] ?? 0;
        $conta->pis_servico = $imp['impostos']['pis_servico'] ?? 0;
        $conta->iss_servico = $imp['impostos']['iss_servico'] ?? 0;
        $conta->vl_imp_prod = $calcConta['vl_imp_prod'] ?? 0;
        $conta->vl_imp_serv = $calcConta['vl_imp_serv'] ?? 0;
        $conta->valor_liqbase_prod_posimp = $calcConta['valor_liqbase_prod_posimp'] ?? 0;
        $conta->valor_liqbase_serv_posimp = $calcConta['valor_liqbase_serv_posimp'] ?? 0;
        $conta->valor_txc_imp_produto_servico = $calcConta['valor_txc_imp_produto_servico'] ?? 0;
        $conta->txadm = $imp['perc_tx_adm'] ?? 0;
        $conta->valor_txadm = CalculoTaxasImpostosService::money($calcConta['valor_txadm'] ?? 0);
        $conta->valor_txantecipacao = CalculoTaxasImpostosService::money($calcConta['valor_txantecipacao'] ?? 0);
        $conta->valor_total_liq_tx_conta = CalculoTaxasImpostosService::money($calcConta['valor_total_liq_tx_conta'] ?? 0);
        $conta->parcela = 1;
        $conta->descricao = $pedido->descricaopedido;
        $conta->tipo_conta_id = TipoConta::PAGAR;
        $conta->mes_emissao = intval(substr($conta->dt_emissao, 5, 2));
        $conta->ano_emissao = intval(substr($conta->dt_emissao, 0, 4));
        $conta->mes_ano_emissao = intval(substr($conta->dt_emissao, 0, 4) . substr($conta->dt_emissao, 5, 2));
        $conta->departamento_unit_id = $pedido->departamento_unit_id;
        $conta->system_users_id = $pedido->system_users_id;
        $conta->entidade_id = $pedido->entidade_id;
        $conta->system_unit_id = $pedido->system_unit_id;
        $conta->store();

        $dispositivoSolicitadoId = (int) ($lancamento['dispositivos_solicitados_id'] ?? 0);
        $saldoLimiteCartao = self::normalizarValorLancamento($lancamento['saldo_limite'] ?? 0);
        $saldoAnteriorCartao = self::normalizarValorLancamento($lancamento['saldo_atual'] ?? 0);

        if ($dispositivoSolicitadoId > 0)
        {
            $dispositivoSolicitado = new DispositivosSolicitados($dispositivoSolicitadoId);
            if (!empty($dispositivoSolicitado->id))
            {
                $saldoCadastroAtual = self::normalizarValorLancamento($dispositivoSolicitado->saldo_atual ?? 0);
                $saldoCadastroLimite = self::normalizarValorLancamento($dispositivoSolicitado->saldo_limite ?? 0);

                if ($saldoCadastroAtual > 0)
                {
                    $saldoAnteriorCartao = $saldoCadastroAtual;
                }
                elseif ($saldoAnteriorCartao <= 0)
                {
                    $saldoAnteriorCartao = $saldoCadastroLimite > 0 ? $saldoCadastroLimite : $saldoLimiteCartao;
                }

                $saldoAtualCartao = round(max(0, $saldoAnteriorCartao - $valorTotal), 2);
                $dispositivoSolicitado->saldo_atual = $saldoAtualCartao;
                $dispositivoSolicitado->store();
            }
        }
        else
        {
            if ($saldoAnteriorCartao <= 0 && $saldoLimiteCartao > 0)
            {
                $saldoAnteriorCartao = $saldoLimiteCartao;
            }

            $saldoAtualCartao = round(max(0, $saldoAnteriorCartao - $valorTotal), 2);
        }

        if (!empty($dados['km']))
        {
            $veiculo->hodometroatual = $dados['km'];
            $veiculo->store();
        }

        return [
            'pedido_id' => $pedido->id,
            'proposta_id' => $proposta->id,
            'item_pedido_id' => $itemPedido->id,
            'item_proposta_id' => $itemProposta->id,
            'valor_total' => $valorTotal,
            'saldo_anterior' => $saldoAnteriorCartao,
            'saldo_atual' => $saldoAtualCartao,
            'launch_key' => $launchKey,
        ];
    }
}
