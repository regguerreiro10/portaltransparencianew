<?php

use app\service\APICombustivel2;

class LojaCredenciadaSyncService
{
    public const SESSION_KEY = __CLASS__ . '_preview';
    public const SESSION_META_KEY = __CLASS__ . '_meta';

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
            throw new Exception('Informe um CPF valido para consultar as lojas credenciadas.');
        }

        return $cpf;
    }

    private static function normalizarNome(?string $valor): string
    {
        $valor = trim((string) $valor);
        $valor = preg_replace('/\s+/', ' ', $valor);

        return mb_strtoupper($valor, 'UTF-8');
    }

    private static function formatarCnpj(?string $cnpj): string
    {
        $cnpj = self::normalizarDocumento($cnpj);

        if (strlen($cnpj) !== 14)
        {
            return '';
        }

        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
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

    private static function listarCadastrosCartao(?string $cpf = null): array
    {
        $query = DispositivosSolicitados::where('deleted_at', 'is', null)
            ->where('numerocartao', 'is not', null);

        if (TSession::getValue('idunit'))
        {
            $query->where('system_unit_id', '=', TSession::getValue('idunit'));
        }

        $cadastros = $query->load() ?: [];
        $cpf = self::normalizarDocumento($cpf);
        $retorno = [];

        foreach ($cadastros as $cadastro)
        {
            $pessoa = !empty($cadastro->pessoa_id) ? $cadastro->pessoa : null;
            $cpfPessoa = self::normalizarDocumento($pessoa->documento ?? '');

            if ($cpf !== '' && $cpfPessoa !== $cpf)
            {
                continue;
            }

            if ($cpfPessoa === '')
            {
                continue;
            }

            $retorno[] = $cadastro;
        }

        return $retorno;
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

    private static function localizarEnderecoPrincipal(int $pessoaId): ?PessoaEndereco
    {
        return PessoaEndereco::where('pessoa_id', '=', $pessoaId)
            ->where('principal', '=', 'T')
            ->first();
    }

    private static function localizarPessoaPorDocumentoNormalizado(string $cnpj): ?Pessoa
    {
        $cnpj = self::normalizarDocumento($cnpj);
        if (strlen($cnpj) !== 14)
        {
            return null;
        }

        $criteria = new TCriteria();
        $criteria->add(new TFilter('documento', 'is not', null));

        if (TSession::getValue('idunit'))
        {
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
        }

        $repository = new TRepository('Pessoa');
        $pessoas = $repository->load($criteria, false) ?: [];

        foreach ($pessoas as $pessoa)
        {
            if (self::normalizarDocumento($pessoa->documento ?? '') === $cnpj)
            {
                return $pessoa;
            }
        }

        return null;
    }

    private static function localizarPessoaPorNomeLoja(?string $nomeLoja): ?Pessoa
    {
        $nomeLoja = self::normalizarNome($nomeLoja);
        if ($nomeLoja === '')
        {
            return null;
        }

        $criteria = new TCriteria();
        if (TSession::getValue('idunit'))
        {
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
        }

        $repository = new TRepository('Pessoa');
        $pessoas = $repository->load($criteria, false) ?: [];

        foreach ($pessoas as $fornecedor)
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

    private static function localizarPessoaExistenteLoja(array $loja): ?Pessoa
    {
        $cnpj = self::normalizarDocumento($loja['cnpj'] ?? '');

        if (strlen($cnpj) === 14)
        {
            $pessoa = self::localizarPessoaPorDocumentoNormalizado($cnpj);
            if ($pessoa)
            {
                return $pessoa;
            }

            $pessoa = Pessoa::where('documento', '=', $cnpj)->first();
            if ($pessoa)
            {
                return $pessoa;
            }

            $cnpjFormatado = self::formatarCnpj($cnpj);
            if ($cnpjFormatado !== '')
            {
                $pessoa = Pessoa::where('documento', '=', $cnpjFormatado)->first();
                if ($pessoa)
                {
                    return $pessoa;
                }
            }
        }

        $pessoa = self::localizarPessoaPorNomeLoja($loja['razao_social'] ?? '');
        if ($pessoa)
        {
            return $pessoa;
        }

        return self::localizarPessoaPorNomeLoja($loja['nome'] ?? '');
    }

    private static function localizarOuCriarSeguimento(?string $descricao): ?Seguimento
    {
        $descricao = trim((string) $descricao);
        if ($descricao === '')
        {
            return null;
        }

        $seguimento = Seguimento::where('descricao', '=', $descricao)->first();
        if ($seguimento)
        {
            return $seguimento;
        }

        $repository = new TRepository('Seguimento');
        $seguimentos = $repository->load(new TCriteria()) ?: [];
        $descricaoNormalizada = self::normalizarNome($descricao);

        foreach ($seguimentos as $item)
        {
            if (self::normalizarNome($item->descricao ?? '') === $descricaoNormalizada)
            {
                return $item;
            }
        }

        $seguimento = new Seguimento();
        $seguimento->descricao = $descricao;
        $seguimento->store();

        return $seguimento;
    }

    private static function sincronizarSeguimentoPessoa(Pessoa $pessoa, array $loja): string
    {
        $seguimento = self::localizarOuCriarSeguimento($loja['ramo_atividade'] ?? '');
        if (!$seguimento)
        {
            return 'ignored';
        }

        $vinculo = SeguimentoPessoa::where('pessoa_id', '=', $pessoa->id)
            ->where('seguimento_id', '=', $seguimento->id)
            ->first();

        if ($vinculo)
        {
            return 'unchanged';
        }

        $vinculo = new SeguimentoPessoa();
        $vinculo->pessoa_id = $pessoa->id;
        $vinculo->seguimento_id = $seguimento->id;
        $vinculo->store();

        return 'created';
    }

    private static function montarChaveLoja(array $loja): string
    {
        $cnpj = self::normalizarDocumento($loja['cnpj'] ?? '');
        if (strlen($cnpj) === 14)
        {
            return 'cnpj:' . $cnpj;
        }

        $codigo = trim((string) ($loja['codigo_loja'] ?? ''));
        $nome = self::normalizarNome($loja['razao_social'] ?? ($loja['nome'] ?? ''));

        return 'loja:' . $codigo . '|' . $nome;
    }

    private static function mergeLoja(array $atual, array $nova, string $cpfOrigem, string $cartaoOrigem, string $redeOrigem): array
    {
        $campos = ['nome', 'razao_social', 'cnpj', 'cep', 'logradouro', 'numero', 'bairro', 'complemento', 'cidade', 'uf', 'telefone', 'email', 'latitude', 'longitude'];

        foreach ($campos as $campo)
        {
            if (empty($atual[$campo]) && !empty($nova[$campo]))
            {
                $atual[$campo] = $nova[$campo];
            }
        }

        $atual['cpfs_origem'][$cpfOrigem] = $cpfOrigem;
        if ($cartaoOrigem !== '')
        {
            $atual['cartoes_origem'][$cartaoOrigem] = $cartaoOrigem;
        }
        if ($redeOrigem !== '')
        {
            $atual['redes_origem'][$redeOrigem] = $redeOrigem;
        }

        return $atual;
    }

    private static function enriquecerPreviewLoja(array $loja): array
    {
        $cnpj = self::normalizarDocumento($loja['cnpj'] ?? '');
        $pessoa = null;
        $endereco = null;
        $grupoFornecedor = false;

        if (strlen($cnpj) === 14)
        {
            $pessoa = self::localizarPessoaExistenteLoja($loja);
        }

        if ($pessoa)
        {
            $endereco = self::localizarEnderecoPrincipal((int) $pessoa->id);
            $grupoFornecedor = (bool) PessoaGrupo::where('pessoa_id', '=', $pessoa->id)
                ->where('grupo_pessoa_id', '=', GrupoPessoa::FORNECEDOR)
                ->first();
        }

        $loja['pessoa_id'] = $pessoa ? (int) $pessoa->id : null;
        $loja['pessoa_nome'] = $pessoa->nome ?? '';
        $loja['endereco_principal_id'] = $endereco ? (int) $endereco->id : null;
        $loja['fornecedor_vinculado'] = $grupoFornecedor ? 'Sim' : 'Nao';
        $loja['tem_cnpj'] = strlen($cnpj) === 14 ? 'Sim' : 'Nao';

        if (strlen($cnpj) !== 14)
        {
            $loja['status_sync'] = 'Sem CNPJ';
        }
        elseif (!$pessoa)
        {
            $loja['status_sync'] = 'Novo fornecedor';
        }
        elseif (!$grupoFornecedor)
        {
            $loja['status_sync'] = 'Pessoa sem grupo fornecedor';
        }
        elseif (!$endereco)
        {
            $loja['status_sync'] = 'Fornecedor sem endereco';
        }
        else
        {
            $loja['status_sync'] = 'Atualizar cadastro';
        }

        $loja['cpfs_origem'] = array_values($loja['cpfs_origem'] ?? []);
        $loja['cartoes_origem'] = array_values($loja['cartoes_origem'] ?? []);
        $loja['redes_origem'] = array_values($loja['redes_origem'] ?? []);
        $loja['row_key'] = $loja['row_key'] ?? md5(json_encode([
            $loja['codigo_loja'] ?? '',
            $loja['cnpj'] ?? '',
            $loja['nome'] ?? '',
            $loja['razao_social'] ?? '',
        ]));

        return $loja;
    }

    private static function atualizarPessoaComLoja(Pessoa $pessoa, array $loja, bool $atualizarExistentes): string
    {
        $alterou = false;

        $nomeLoja = trim((string) (($loja['razao_social'] ?? '') ?: ($loja['nome'] ?? '')));
        $telefone = trim((string) ($loja['telefone'] ?? ''));
        $email = trim((string) ($loja['email'] ?? ''));
        $cnpj = self::normalizarDocumento($loja['cnpj'] ?? '');
        $cnpjFormatado = self::formatarCnpj($cnpj);

        if ($cnpjFormatado !== '' && trim((string) ($pessoa->documento ?? '')) !== $cnpjFormatado)
        {
            if (self::normalizarDocumento($pessoa->documento ?? '') === $cnpj || trim((string) ($pessoa->documento ?? '')) === '')
            {
                $pessoa->documento = $cnpjFormatado;
                $alterou = true;
            }
        }

        if (empty($pessoa->nome) || ($atualizarExistentes && $nomeLoja !== ''))
        {
            if ($nomeLoja !== '' && trim((string) $pessoa->nome) !== $nomeLoja)
            {
                $pessoa->nome = $nomeLoja;
                $alterou = true;
            }
        }

        if (empty($pessoa->fone) || ($atualizarExistentes && $telefone !== ''))
        {
            if ($telefone !== '' && trim((string) $pessoa->fone) !== $telefone)
            {
                $pessoa->fone = $telefone;
                $alterou = true;
            }
        }

        if (empty($pessoa->email) || ($atualizarExistentes && $email !== ''))
        {
            if ($email !== '' && trim((string) $pessoa->email) !== $email)
            {
                $pessoa->email = $email;
                $alterou = true;
            }
        }

        if (empty($pessoa->tipo_cliente_id))
        {
            $pessoa->tipo_cliente_id = TipoCliente::JURIDICA;
            $alterou = true;
        }

        if (empty($pessoa->ativo))
        {
            $pessoa->ativo = 'T';
            $alterou = true;
        }

        if (empty($pessoa->system_unit_id) && TSession::getValue('idunit'))
        {
            $pessoa->system_unit_id = TSession::getValue('idunit');
            $alterou = true;
        }

        if ($alterou)
        {
            $pessoa->store();
            return 'updated';
        }

        return 'unchanged';
    }

    private static function sincronizarEnderecoPrincipal(Pessoa $pessoa, array $loja, bool $atualizarExistentes): string
    {
        $temDadosEndereco = !empty($loja['logradouro']) || !empty($loja['bairro']) || !empty($loja['cidade']) || !empty($loja['uf']) || !empty($loja['cep']) || !empty($loja['latitude']) || !empty($loja['longitude']);
        if (!$temDadosEndereco)
        {
            return 'ignored';
        }

        $cidade = self::obterOuCriarCidadeLoja((string) ($loja['cidade'] ?? ''), (string) ($loja['uf'] ?? ''));
        $endereco = self::localizarEnderecoPrincipal((int) $pessoa->id);
        $alterou = false;

        if (!$endereco)
        {
            $endereco = new PessoaEndereco();
            $endereco->pessoa_id = $pessoa->id;
            $endereco->principal = 'T';
            $alterou = true;
            $acao = 'created';
        }
        else
        {
            $acao = 'unchanged';
        }

        $mapaCampos = [
            'nome' => trim((string) (($loja['nome'] ?? '') ?: ($loja['razao_social'] ?? '') ?: ($pessoa->nome ?? ''))),
            'cep' => self::normalizarDocumento($loja['cep'] ?? ''),
            'rua' => trim((string) ($loja['logradouro'] ?? '')),
            'numero' => trim((string) ($loja['numero'] ?? '')),
            'bairro' => trim((string) ($loja['bairro'] ?? '')),
            'complemento' => trim((string) ($loja['complemento'] ?? '')),
            'latitude' => trim((string) ($loja['latitude'] ?? '')),
            'longitude' => trim((string) ($loja['longitude'] ?? '')),
        ];

        foreach ($mapaCampos as $campo => $valor)
        {
            $valorAtual = trim((string) ($endereco->{$campo} ?? ''));

            if ($valorAtual === '' || ($atualizarExistentes && $valor !== ''))
            {
                if ($valor !== '' && $valorAtual !== $valor)
                {
                    $endereco->{$campo} = $valor;
                    $alterou = true;
                }
            }
        }

        if ($cidade && (empty($endereco->cidade_id) || ($atualizarExistentes && (int) $endereco->cidade_id !== (int) $cidade->id)))
        {
            $endereco->cidade_id = $cidade->id;
            $alterou = true;
        }

        if ($alterou)
        {
            $endereco->store();

            if ($cidade && (empty($pessoa->cidade_id) || ($atualizarExistentes && (int) $pessoa->cidade_id !== (int) $cidade->id)))
            {
                $pessoa->cidade_id = $cidade->id;
                $pessoa->store();
            }

            return $acao === 'created' ? 'created' : 'updated';
        }

        if ($cidade && (empty($pessoa->cidade_id) || ($atualizarExistentes && (int) $pessoa->cidade_id !== (int) $cidade->id)))
        {
            $pessoa->cidade_id = $cidade->id;
            $pessoa->store();
        }

        return $acao;
    }

    private static function sincronizarLoja(array $loja, bool $atualizarExistentes): array
    {
        $cnpj = self::normalizarDocumento($loja['cnpj'] ?? '');

        if (strlen($cnpj) !== 14)
        {
            return [
                'status' => 'ignored',
                'motivo' => 'Loja ignorada por nao possuir CNPJ valido.',
            ];
        }

        $pessoa = self::localizarPessoaExistenteLoja($loja);
        $pessoaAcao = 'unchanged';

        if (!$pessoa)
        {
            $pessoa = new Pessoa();
            $pessoa->nome = trim((string) (($loja['razao_social'] ?? '') ?: ($loja['nome'] ?? '') ?: ('LOJA ' . $cnpj)));
            $pessoa->documento = self::formatarCnpj($cnpj);
            $pessoa->fone = trim((string) ($loja['telefone'] ?? ''));
            $pessoa->email = trim((string) ($loja['email'] ?? ''));
            $pessoa->ativo = 'T';
            $pessoa->tipo_cliente_id = TipoCliente::JURIDICA;
            $pessoa->system_unit_id = TSession::getValue('idunit');
            $pessoa->store();
            $pessoaAcao = 'created';
        }
        else
        {
            $pessoaAcao = self::atualizarPessoaComLoja($pessoa, $loja, $atualizarExistentes);
        }

        $grupoAcao = 'unchanged';
        if (!PessoaGrupo::where('pessoa_id', '=', $pessoa->id)->where('grupo_pessoa_id', '=', GrupoPessoa::FORNECEDOR)->first())
        {
            $pessoaGrupo = new PessoaGrupo();
            $pessoaGrupo->pessoa_id = $pessoa->id;
            $pessoaGrupo->grupo_pessoa_id = GrupoPessoa::FORNECEDOR;
            $pessoaGrupo->store();
            $grupoAcao = 'created';
        }

        $enderecoAcao = self::sincronizarEnderecoPrincipal($pessoa, $loja, $atualizarExistentes);
        $seguimentoAcao = self::sincronizarSeguimentoPessoa($pessoa, $loja);

        return [
            'status' => 'success',
            'pessoa_id' => (int) $pessoa->id,
            'pessoa_acao' => $pessoaAcao,
            'grupo_acao' => $grupoAcao,
            'endereco_acao' => $enderecoAcao,
            'seguimento_acao' => $seguimentoAcao,
        ];
    }

    public static function consultarLojas(array $filtros): array
    {
        $cpf = self::normalizarCpf($filtros['cpf'] ?? '');
        $somenteNovas = ($filtros['somente_novas'] ?? 'N') === 'S';

        $credenciais = self::obterCredenciaisApi();
        $token = APICombustivel2::getPegarToken($credenciais['grupo'], $credenciais['login'], $credenciais['senha']);

        if (empty($token))
        {
            throw new Exception('Nao foi possivel obter o token da integracao de cartao.');
        }

        $cadastrosCartao = self::listarCadastrosCartao($cpf);
        if (!$cadastrosCartao)
        {
            throw new Exception($cpf !== ''
                ? 'Nenhum cartao foi encontrado em dispositivos_solicitados para o CPF informado.'
                : 'Nenhum cartao foi encontrado em dispositivos_solicitados para a unidade atual.');
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
            throw new Exception('Os cartoes encontrados precisam ter um usuario vinculado com CPF informado.');
        }

        $lojasIndexadas = [];
        $metadados = [
            'cpf' => $cpf,
            'qtd_usuarios_consultados' => count($cadastrosPorCpf),
            'qtd_cartoes_cadastrados' => count($cadastrosCartao),
            'qtd_contas' => 0,
            'qtd_lojas_brutas' => 0,
            'qtd_lojas_unicas' => 0,
            'erros_api' => [],
        ];

        foreach ($cadastrosPorCpf as $cpfPessoa => $cadastrosUsuario)
        {
            $resultado = APICombustivel2::getApp_ListarContasPorCPF(
                $token,
                $credenciais['grupo'],
                $credenciais['login'],
                $credenciais['senha'],
                $cpfPessoa
            );

            $metadados['qtd_contas'] += count($resultado['contas'] ?? []);

            foreach (($resultado['contas'] ?? []) as $conta)
            {
                $erroLojas = $conta['lojas_credenciadas_erro'] ?? [];
                if (!empty($erroLojas['cod']) || !empty($erroLojas['msg']))
                {
                    $metadados['erros_api'][] = [
                        'cpf' => $cpfPessoa,
                        'cartao' => $conta['cartao'] ?? '',
                        'cod' => $erroLojas['cod'] ?? '',
                        'msg' => $erroLojas['msg'] ?? '',
                    ];
                }

                foreach (($conta['lojas_credenciadas'] ?? []) as $loja)
                {
                    $metadados['qtd_lojas_brutas']++;
                    $chave = self::montarChaveLoja($loja);
                    $cartaoOrigem = trim((string) ($conta['cartao'] ?? ''));
                    $redeOrigem = trim((string) ($conta['rede'] ?? ''));

                    if (!isset($lojasIndexadas[$chave]))
                    {
                        $lojasIndexadas[$chave] = $loja;
                        $lojasIndexadas[$chave]['cpfs_origem'] = [$cpfPessoa => $cpfPessoa];
                        $lojasIndexadas[$chave]['cartoes_origem'] = $cartaoOrigem !== '' ? [$cartaoOrigem => $cartaoOrigem] : [];
                        $lojasIndexadas[$chave]['redes_origem'] = $redeOrigem !== '' ? [$redeOrigem => $redeOrigem] : [];
                    }
                    else
                    {
                        $lojasIndexadas[$chave] = self::mergeLoja($lojasIndexadas[$chave], $loja, $cpfPessoa, $cartaoOrigem, $redeOrigem);
                    }
                }
            }
        }

        $preview = [];
        foreach ($lojasIndexadas as $loja)
        {
            $loja = self::enriquecerPreviewLoja($loja);

            if ($somenteNovas && $loja['status_sync'] !== 'Novo fornecedor')
            {
                continue;
            }

            $preview[] = $loja;
        }

        usort($preview, function ($a, $b) {
            return strcmp(
                self::normalizarNome($a['razao_social'] ?? ($a['nome'] ?? '')),
                self::normalizarNome($b['razao_social'] ?? ($b['nome'] ?? ''))
            );
        });

        $metadados['qtd_lojas_unicas'] = count($preview);

        TSession::setValue(self::SESSION_KEY, $preview);
        TSession::setValue(self::SESSION_META_KEY, $metadados);

        return $preview;
    }

    public static function sincronizarPreview(array $opcoes = []): array
    {
        $preview = TSession::getValue(self::SESSION_KEY) ?? [];
        if (!$preview)
        {
            throw new Exception('Consulte as lojas credenciadas antes de sincronizar.');
        }

        $atualizarExistentes = ($opcoes['atualizar_existentes'] ?? 'S') === 'S';
        $resumo = [
            'processadas' => 0,
            'criadas' => 0,
            'atualizadas' => 0,
            'grupos_criados' => 0,
            'enderecos_criados' => 0,
            'enderecos_atualizados' => 0,
            'seguimentos_vinculados' => 0,
            'ignoradas' => 0,
        ];

        foreach ($preview as $indice => $loja)
        {
            $resultado = self::sincronizarLoja($loja, $atualizarExistentes);
            $resumo['processadas']++;

            if (($resultado['status'] ?? '') === 'ignored')
            {
                $resumo['ignoradas']++;
                $preview[$indice]['status_sync'] = 'Ignorada';
                $preview[$indice]['sync_resultado'] = $resultado['motivo'] ?? '';
                continue;
            }

            if (($resultado['pessoa_acao'] ?? '') === 'created')
            {
                $resumo['criadas']++;
            }
            elseif (($resultado['pessoa_acao'] ?? '') === 'updated')
            {
                $resumo['atualizadas']++;
            }

            if (($resultado['grupo_acao'] ?? '') === 'created')
            {
                $resumo['grupos_criados']++;
            }

            if (($resultado['endereco_acao'] ?? '') === 'created')
            {
                $resumo['enderecos_criados']++;
            }
            elseif (($resultado['endereco_acao'] ?? '') === 'updated')
            {
                $resumo['enderecos_atualizados']++;
            }

            if (($resultado['seguimento_acao'] ?? '') === 'created')
            {
                $resumo['seguimentos_vinculados']++;
            }

            $preview[$indice] = self::enriquecerPreviewLoja($preview[$indice]);
            $preview[$indice]['sync_resultado'] = 'Sincronizada';
        }

        TSession::setValue(self::SESSION_KEY, $preview);

        $meta = TSession::getValue(self::SESSION_META_KEY) ?? [];
        $meta['ultimo_resumo_sync'] = $resumo;
        TSession::setValue(self::SESSION_META_KEY, $meta);

        return $resumo;
    }

    public static function sincronizarItemPreview(string $rowKey, array $opcoes = []): array
    {
        $preview = TSession::getValue(self::SESSION_KEY) ?? [];
        if (!$preview)
        {
            throw new Exception('Consulte as lojas credenciadas antes de sincronizar.');
        }

        $atualizarExistentes = ($opcoes['atualizar_existentes'] ?? 'S') === 'S';

        foreach ($preview as $indice => $loja)
        {
            if (($loja['row_key'] ?? '') !== $rowKey)
            {
                continue;
            }

            $resultado = self::sincronizarLoja($loja, $atualizarExistentes);

            if (($resultado['status'] ?? '') === 'ignored')
            {
                $preview[$indice]['status_sync'] = 'Ignorada';
                $preview[$indice]['sync_resultado'] = $resultado['motivo'] ?? '';
                TSession::setValue(self::SESSION_KEY, $preview);

                return $resultado;
            }

            $preview[$indice] = self::enriquecerPreviewLoja($preview[$indice]);
            $preview[$indice]['sync_resultado'] = 'Sincronizada';
            TSession::setValue(self::SESSION_KEY, $preview);

            return $resultado;
        }

        throw new Exception('Item do preview nao encontrado para sincronizacao.');
    }
}
