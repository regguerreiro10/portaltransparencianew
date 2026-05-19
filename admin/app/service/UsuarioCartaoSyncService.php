<?php

use app\service\APICombustivel2;

class UsuarioCartaoSyncService
{
    public const SESSION_KEY = __CLASS__ . '_preview';
    public const SESSION_META_KEY = __CLASS__ . '_meta';

    private static function normalizarDocumento(?string $documento): string
    {
        return preg_replace('/\D+/', '', (string) $documento);
    }

    private static function normalizarIdentificadorCartao(?string $valor): string
    {
        $valor = trim((string) $valor);
        $valor = self::expandirNotacaoCientifica($valor);
        $valor = strtoupper($valor);
        return preg_replace('/[^A-Z0-9]/', '', $valor);
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

    private static function formatarCpf(?string $cpf): string
    {
        $cpf = self::normalizarDocumento($cpf);
        if (strlen($cpf) !== 11)
        {
            return '';
        }

        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
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
            throw new Exception('Informe um CPF valido para sincronizar os cartoes.');
        }

        return $cpf;
    }

    private static function normalizarNome(?string $valor): string
    {
        $valor = trim((string) $valor);
        $valor = preg_replace('/\s+/', ' ', $valor);
        return mb_strtoupper($valor, 'UTF-8');
    }

    private static function obterUsuarioSessaoId(): ?int
    {
        $usuarioId = (int) (TSession::getValue('userid') ?: TSession::getValue('iduser') ?: 0);
        return $usuarioId > 0 ? $usuarioId : null;
    }

    private static function obterDepartamentoUsuarioSessaoId(): ?int
    {
        $usuarioId = self::obterUsuarioSessaoId();
        if (!$usuarioId)
        {
            return null;
        }

        $relacao = SystemUserDepartamentoUnit::where('system_users_id', '=', $usuarioId)->first();
        $departamentoId = (int) ($relacao->departamento_unit_id ?? 0);

        return $departamentoId > 0 ? $departamentoId : null;
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

    private static function localizarPessoaPorCpf(string $cpf): ?Pessoa
    {
        $cpf = self::normalizarDocumento($cpf);
        if (strlen($cpf) !== 11)
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

        foreach ($pessoas as $pessoa)
        {
            if (self::normalizarDocumento($pessoa->documento ?? '') === $cpf || self::normalizarDocumento($pessoa->cpf ?? '') === $cpf)
            {
                return $pessoa;
            }
        }

        return null;
    }

    private static function listarCpfsCartoesLocal(): array
    {
        $criteria = new TCriteria();
        $criteria->add(new TFilter('deleted_at', 'is', null));
        $criteria->add(new TFilter('numerocartao', 'is not', null));

        if (TSession::getValue('idunit'))
        {
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
        }

        $repository = new TRepository('DispositivosSolicitados');
        $cadastros = $repository->load($criteria, false) ?: [];
        $cpfs = [];

        foreach ($cadastros as $cadastro)
        {
            $pessoa = !empty($cadastro->pessoa_id) ? $cadastro->pessoa : null;
            $cpf = self::normalizarDocumento($pessoa->cpf ?? ($pessoa->documento ?? ''));
            if (strlen($cpf) === 11)
            {
                $cpfs[$cpf] = $cpf;
            }
        }

        return array_values($cpfs);
    }

    private static function localizarOuCriarCidade(string $cidadeNome, string $uf): ?Cidade
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

        $cidade = Cidade::where('nome', '=', $cidadeNome)->where('estado_id', '=', $estado->id)->first();
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
        return PessoaEndereco::where('pessoa_id', '=', $pessoaId)->where('principal', '=', 'T')->first();
    }

    private static function garantirGrupoPessoa(int $pessoaId, string $grupoId): string
    {
        if (PessoaGrupo::where('pessoa_id', '=', $pessoaId)->where('grupo_pessoa_id', '=', $grupoId)->first())
        {
            return 'unchanged';
        }

        $grupo = new PessoaGrupo();
        $grupo->pessoa_id = $pessoaId;
        $grupo->grupo_pessoa_id = $grupoId;
        $grupo->store();
        return 'created';
    }

    private static function localizarDispositivoPadraoCartao(): ?Dispositivos
    {
        $repository = new TRepository('Dispositivos');
        $dispositivos = $repository->load(new TCriteria(), false) ?: [];

        foreach ($dispositivos as $dispositivo)
        {
            if (str_contains(self::normalizarNome($dispositivo->descricao ?? ''), 'CART'))
            {
                return $dispositivo;
            }
        }

        return $dispositivos[0] ?? null;
    }

    private static function localizarCadastroCartao(?string $numeroCartao): ?DispositivosSolicitados
    {
        $numeroCartao = self::normalizarIdentificadorCartao($numeroCartao);
        if ($numeroCartao === '')
        {
            return null;
        }

        $criteria = new TCriteria();
        $criteria->add(new TFilter('deleted_at', 'is', null));
        if (TSession::getValue('idunit'))
        {
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
        }

        $repository = new TRepository('DispositivosSolicitados');
        $cadastros = $repository->load($criteria, false) ?: [];
        $candidatosMesmoFinal = [];

        foreach ($cadastros as $cadastro)
        {
            $numeroCadastro = self::normalizarIdentificadorCartao($cadastro->numerocartao ?? '');
            if ($numeroCadastro === '')
            {
                continue;
            }

            if ($numeroCadastro === $numeroCartao || str_contains($numeroCadastro, $numeroCartao) || str_contains($numeroCartao, $numeroCadastro))
            {
                return $cadastro;
            }

            if (self::finaisCartaoCoincidem($numeroCadastro, $numeroCartao, 6))
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

    private static function localizarVeiculoPessoa(?Pessoa $pessoa): ?Veiculos
    {
        if (!$pessoa || empty($pessoa->id))
        {
            return null;
        }

        $query = Veiculos::where('responsavel_id', '=', $pessoa->id)->where('deleted_at', 'is', null);
        if (TSession::getValue('idunit'))
        {
            $query->where('system_unit_id', '=', TSession::getValue('idunit'));
        }

        return $query->first();
    }

    private static function numerico($valor): float
    {
        $texto = trim((string) $valor);
        $texto = preg_replace('/[^0-9,\.\-]/', '', $texto);
        $texto = str_replace('.', '', $texto);
        $texto = str_replace(',', '.', $texto);

        return is_numeric($texto) ? abs((float) $texto) : 0.0;
    }

    private static function montarPreviewConta(array $conta, string $cpfConsulta): array
    {
        $dados = $conta['dados_cadastrais'] ?? [];
        $dadosConta = $conta['dados_conta'] ?? ($dados['dados_conta'] ?? []);
        $cpf = self::normalizarDocumento($dados['cpf'] ?? $cpfConsulta);
        $cartao = trim((string) ($conta['cartao'] ?? $dados['cartao'] ?? ''));
        $pessoa = self::localizarPessoaPorCpf($cpf);
        $cadastro = self::localizarCadastroCartao($cartao);
        $veiculo = $cadastro && !empty($cadastro->veiculos_id) ? new Veiculos((int) $cadastro->veiculos_id) : self::localizarVeiculoPessoa($pessoa);
        $dispositivoPadrao = self::localizarDispositivoPadraoCartao();
        $saldoAtual = self::numerico($dadosConta['saldo_atual'] ?? $dados['saldo_atual'] ?? $dadosConta['vl_saldo_disp_mes'] ?? $dados['raw']['saldo_atual'] ?? $dados['raw']['vl_saldo_atual'] ?? 0);
        $saldoLimite = self::numerico($dadosConta['saldo_limite'] ?? $dados['saldo_limite'] ?? $dadosConta['vl_lim_cred_global'] ?? $dadosConta['vl_lim_cred_mes'] ?? $dados['raw']['saldo_limite'] ?? $dados['raw']['vl_limite'] ?? 0);

        return [
            'row_key' => md5(json_encode([$cpf, $cartao, $conta['cd_cliente'] ?? '', $conta['rede'] ?? ''])),
            'cpf' => $cpf,
            'nome' => trim((string) ($dados['nome'] ?? '')),
            'email' => trim((string) ($dados['email'] ?? '')),
            'telefone' => trim((string) (($dados['telefone'] ?? '') ?: ($dados['endereco_residencial']['telefone'] ?? ''))),
            'cartao' => $cartao,
            'rede' => trim((string) ($conta['rede'] ?? '')),
            'cd_cliente' => trim((string) ($conta['cd_cliente'] ?? '')),
            'saldo_atual' => $saldoAtual,
            'saldo_limite' => $saldoLimite,
            'dados_conta' => $dadosConta,
            'endereco' => $dados['endereco_residencial'] ?? [],
            'pessoa_id' => $pessoa->id ?? null,
            'pessoa_nome' => $pessoa->nome ?? '',
            'tem_grupo_condutor' => $pessoa ? (bool) PessoaGrupo::where('pessoa_id', '=', $pessoa->id)->where('grupo_pessoa_id', '=', GrupoPessoa::CONDUTOR)->first() : false,
            'tem_grupo_usuario' => $pessoa ? (bool) PessoaGrupo::where('pessoa_id', '=', $pessoa->id)->where('grupo_pessoa_id', '=', GrupoPessoa::USUARIODISPOSITIVO)->first() : false,
            'dispositivos_solicitados_id' => $cadastro->id ?? null,
            'veiculos_id' => $veiculo->id ?? ($cadastro->veiculos_id ?? null),
            'veiculo_descricao' => $veiculo ? trim((string) (($veiculo->placa ?? '') . ' ' . ($veiculo->modelo->descricao ?? ''))) : '',
            'dispositivo_padrao_id' => $dispositivoPadrao->id ?? null,
            'dispositivo_padrao_nome' => $dispositivoPadrao->descricao ?? '',
            'status_sync' => '',
            'sync_resultado' => '',
            'raw' => $conta,
        ];
    }

    private static function classificarPreview(array $item): array
    {
        if (empty($item['cpf']) || empty($item['cartao']))
        {
            $item['status_sync'] = 'Dados incompletos';
        }
        elseif (empty($item['dispositivo_padrao_id']) && empty($item['dispositivos_solicitados_id']))
        {
            $item['status_sync'] = 'Sem dispositivo padrao';
        }
        elseif (empty($item['pessoa_id']))
        {
            $item['status_sync'] = 'Novo usuario/cartao';
        }
        elseif (empty($item['dispositivos_solicitados_id']))
        {
            $item['status_sync'] = 'Usuario sem cartao local';
        }
        else
        {
            $item['status_sync'] = 'Atualizar cadastro';
        }

        return $item;
    }

    public static function consultarPorCpf(array $filtros): array
    {
        $cpf = self::normalizarCpf($filtros['cpf'] ?? '');
        $credenciais = self::obterCredenciaisApi();
        $token = APICombustivel2::getPegarToken($credenciais['grupo'], $credenciais['login'], $credenciais['senha']);
        if (!$token)
        {
            throw new Exception('Nao foi possivel obter o token da integracao de cartao.');
        }

        $preview = [];
        $cpfsConsulta = $cpf !== '' ? [$cpf] : self::listarCpfsCartoesLocal();

        if (!$cpfsConsulta)
        {
            throw new Exception('Nenhum cartao com usuario vinculado foi encontrado em dispositivos_solicitados para a unidade atual.');
        }

        foreach ($cpfsConsulta as $cpfConsulta)
        {
            $resultado = APICombustivel2::getApp_ListarContasPorCPF(
                $token,
                $credenciais['grupo'],
                $credenciais['login'],
                $credenciais['senha'],
                $cpfConsulta
            );

            foreach (($resultado['contas'] ?? []) as $conta)
            {
                $preview[] = self::classificarPreview(self::montarPreviewConta($conta, $cpfConsulta));
            }
        }

        TSession::setValue(self::SESSION_KEY, $preview);
        TSession::setValue(self::SESSION_META_KEY, [
            'cpf' => $cpf,
            'qtd_contas' => count($preview),
            'qtd_cpfs_consultados' => count($cpfsConsulta),
        ]);

        return $preview;
    }

    private static function sincronizarPessoa(array $item, bool $atualizarExistentes): array
    {
        $cpf = self::normalizarDocumento($item['cpf'] ?? '');
        $nome = trim((string) ($item['nome'] ?? ''));
        $email = trim((string) ($item['email'] ?? ''));
        $telefone = trim((string) ($item['telefone'] ?? ''));

        $pessoa = self::localizarPessoaPorCpf($cpf);
        $acao = 'unchanged';

        if (!$pessoa)
        {
            $pessoa = new Pessoa();
            $pessoa->nome = $nome !== '' ? $nome : ('USUARIO CARTAO ' . $cpf);
            $pessoa->documento = self::formatarCpf($cpf);
            $pessoa->cpf = self::formatarCpf($cpf);
            $pessoa->email = $email;
            $pessoa->fone = $telefone;
            $pessoa->tipo_cliente_id = TipoCliente::FISICA;
            $pessoa->ativo = 'T';
            $pessoa->system_unit_id = TSession::getValue('idunit');
            $pessoa->system_users_id = self::obterUsuarioSessaoId();
            $pessoa->store();
            $acao = 'created';
        }
        else
        {
            $alterou = false;
            $cpfFormatado = self::formatarCpf($cpf);

            if ($cpfFormatado !== '' && (self::normalizarDocumento($pessoa->documento ?? '') === '' || $atualizarExistentes) && trim((string) ($pessoa->documento ?? '')) !== $cpfFormatado)
            {
                $pessoa->documento = $cpfFormatado;
                $alterou = true;
            }

            if ($cpfFormatado !== '' && (self::normalizarDocumento($pessoa->cpf ?? '') === '' || $atualizarExistentes) && trim((string) ($pessoa->cpf ?? '')) !== $cpfFormatado)
            {
                $pessoa->cpf = $cpfFormatado;
                $alterou = true;
            }

            if ($nome !== '' && (empty($pessoa->nome) || $atualizarExistentes) && trim((string) $pessoa->nome) !== $nome)
            {
                $pessoa->nome = $nome;
                $alterou = true;
            }

            if ($email !== '' && (empty($pessoa->email) || $atualizarExistentes) && trim((string) $pessoa->email) !== $email)
            {
                $pessoa->email = $email;
                $alterou = true;
            }

            if ($telefone !== '' && (empty($pessoa->fone) || $atualizarExistentes) && trim((string) $pessoa->fone) !== $telefone)
            {
                $pessoa->fone = $telefone;
                $alterou = true;
            }

            if (empty($pessoa->tipo_cliente_id))
            {
                $pessoa->tipo_cliente_id = TipoCliente::FISICA;
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

            if (empty($pessoa->system_users_id) && self::obterUsuarioSessaoId())
            {
                $pessoa->system_users_id = self::obterUsuarioSessaoId();
                $alterou = true;
            }

            if ($alterou)
            {
                $pessoa->store();
                $acao = 'updated';
            }
        }

        return [$pessoa, $acao];
    }

    private static function sincronizarEnderecoPessoa(Pessoa $pessoa, array $item, bool $atualizarExistentes): string
    {
        $enderecoApi = $item['endereco'] ?? [];
        $temDados = !empty($enderecoApi['logradouro']) || !empty($enderecoApi['bairro']) || !empty($enderecoApi['cidade']) || !empty($enderecoApi['uf']) || !empty($enderecoApi['cep']);
        if (!$temDados)
        {
            return 'ignored';
        }

        $cidade = self::localizarOuCriarCidade((string) ($enderecoApi['cidade'] ?? ''), (string) ($enderecoApi['uf'] ?? ''));
        $endereco = self::localizarEnderecoPrincipal((int) $pessoa->id);
        $alterou = false;
        $acao = 'unchanged';

        if (!$endereco)
        {
            $endereco = new PessoaEndereco();
            $endereco->pessoa_id = $pessoa->id;
            $endereco->principal = 'T';
            $alterou = true;
            $acao = 'created';
        }

        $mapa = [
            'nome' => trim((string) ($pessoa->nome ?? '')),
            'cep' => self::normalizarDocumento($enderecoApi['cep'] ?? ''),
            'rua' => trim((string) ($enderecoApi['logradouro'] ?? '')),
            'numero' => trim((string) ($enderecoApi['numero'] ?? '')),
            'bairro' => trim((string) ($enderecoApi['bairro'] ?? '')),
            'complemento' => trim((string) ($enderecoApi['complemento'] ?? '')),
        ];

        foreach ($mapa as $campo => $valor)
        {
            $valorAtual = trim((string) ($endereco->{$campo} ?? ''));
            if (($valorAtual === '' || $atualizarExistentes) && $valor !== '' && $valorAtual !== $valor)
            {
                $endereco->{$campo} = $valor;
                $alterou = true;
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
            return $acao;
        }

        return $acao;
    }

    private static function sincronizarDispositivo(Pessoa $pessoa, array $item, bool $atualizarExistentes): array
    {
        $cadastro = self::localizarCadastroCartao($item['cartao'] ?? '');
        $acao = 'unchanged';
        $veiculoId = (int) ($item['veiculos_id'] ?? 0);
        $veiculo = $veiculoId > 0 ? new Veiculos($veiculoId) : null;
        $systemUsersId = self::obterUsuarioSessaoId();
        $departamentoUnitId = (int) ($veiculo->departamento_unit_id ?? 0);

        if ($departamentoUnitId <= 0)
        {
            $departamentoUnitId = (int) (self::obterDepartamentoUsuarioSessaoId() ?? 0);
        }

        if (!$cadastro)
        {
            $dispositivoPadrao = !empty($item['dispositivo_padrao_id']) ? new Dispositivos((int) $item['dispositivo_padrao_id']) : null;
            if (!$dispositivoPadrao)
            {
                return [null, 'ignored'];
            }

            $cadastro = new DispositivosSolicitados();
            $cadastro->datasolicitacao = date('Y-m-d');
            $cadastro->dispositivos_id = $dispositivoPadrao->id;
            $cadastro->status_dispositivos_id = 1;
            $cadastro->via = 1;
            $cadastro->coringa = 'N';
            $cadastro->system_unit_id = TSession::getValue('idunit');
            $cadastro->system_users_id = $systemUsersId;
            $cadastro->departamento_unit_id = $departamentoUnitId ?: null;
            $cadastro->veiculos_id = $veiculoId ?: null;
            $acao = 'created';
        }

        $alterou = $acao === 'created';
        $cartao = trim((string) ($item['cartao'] ?? ''));
        $saldoAtual = abs((float) ($item['saldo_atual'] ?? 0));
        $saldoLimite = abs((float) ($item['saldo_limite'] ?? 0));

        if ($cartao !== '' && trim((string) ($cadastro->numerocartao ?? '')) !== $cartao)
        {
            $cadastro->numerocartao = $cartao;
            $alterou = true;
        }

        if ((int) ($cadastro->pessoa_id ?? 0) !== (int) $pessoa->id)
        {
            $cadastro->pessoa_id = $pessoa->id;
            $alterou = true;
        }

        if ($veiculoId > 0 && (int) ($cadastro->veiculos_id ?? 0) !== $veiculoId)
        {
            $cadastro->veiculos_id = $veiculoId;
            $alterou = true;
        }

        if (empty($cadastro->status_dispositivos_id))
        {
            $cadastro->status_dispositivos_id = 1;
            $alterou = true;
        }

        if (empty($cadastro->system_unit_id) && TSession::getValue('idunit'))
        {
            $cadastro->system_unit_id = TSession::getValue('idunit');
            $alterou = true;
        }

        if (empty($cadastro->system_users_id) && $systemUsersId)
        {
            $cadastro->system_users_id = $systemUsersId;
            $alterou = true;
        }

        if (empty($cadastro->departamento_unit_id) && $departamentoUnitId > 0)
        {
            $cadastro->departamento_unit_id = $departamentoUnitId;
            $alterou = true;
        }

        if (($saldoAtual > 0 || $atualizarExistentes) && (float) ($cadastro->saldo_atual ?? 0) !== $saldoAtual)
        {
            $cadastro->saldo_atual = $saldoAtual;
            $alterou = true;
        }

        if (($saldoLimite > 0 || $atualizarExistentes) && (float) ($cadastro->saldo_limite ?? 0) !== $saldoLimite)
        {
            $cadastro->saldo_limite = $saldoLimite;
            $alterou = true;
        }

        if ($alterou)
        {
            $cadastro->store();
            if ($acao !== 'created')
            {
                $acao = 'updated';
            }
        }

        return [$cadastro, $acao];
    }

    private static function sincronizarItem(array $item, bool $atualizarExistentes): array
    {
        [$pessoa, $pessoaAcao] = self::sincronizarPessoa($item, $atualizarExistentes);
        $grupoCondutor = self::garantirGrupoPessoa((int) $pessoa->id, GrupoPessoa::CONDUTOR);
        $grupoUsuario = self::garantirGrupoPessoa((int) $pessoa->id, GrupoPessoa::USUARIODISPOSITIVO);
        $enderecoAcao = self::sincronizarEnderecoPessoa($pessoa, $item, $atualizarExistentes);
        [$cadastro, $cadastroAcao] = self::sincronizarDispositivo($pessoa, $item, $atualizarExistentes);

        return [
            'status' => 'success',
            'pessoa_id' => (int) $pessoa->id,
            'pessoa_acao' => $pessoaAcao,
            'grupo_condutor_acao' => $grupoCondutor,
            'grupo_usuario_acao' => $grupoUsuario,
            'endereco_acao' => $enderecoAcao,
            'dispositivo_acao' => $cadastroAcao,
            'dispositivos_solicitados_id' => $cadastro->id ?? null,
        ];
    }

    public static function sincronizarPreview(array $opcoes = []): array
    {
        $preview = TSession::getValue(self::SESSION_KEY) ?? [];
        if (!$preview)
        {
            throw new Exception('Consulte os cartoes antes de sincronizar.');
        }

        $atualizarExistentes = ($opcoes['atualizar_existentes'] ?? 'S') === 'S';
        $resumo = [
            'processados' => 0,
            'pessoas_criadas' => 0,
            'pessoas_atualizadas' => 0,
            'grupos_criados' => 0,
            'enderecos_criados' => 0,
            'enderecos_atualizados' => 0,
            'cartoes_criados' => 0,
            'cartoes_atualizados' => 0,
        ];

        foreach ($preview as $indice => $item)
        {
            $resultado = self::sincronizarItem($item, $atualizarExistentes);
            $resumo['processados']++;
            if (($resultado['pessoa_acao'] ?? '') === 'created') $resumo['pessoas_criadas']++;
            if (($resultado['pessoa_acao'] ?? '') === 'updated') $resumo['pessoas_atualizadas']++;
            if (($resultado['grupo_condutor_acao'] ?? '') === 'created') $resumo['grupos_criados']++;
            if (($resultado['grupo_usuario_acao'] ?? '') === 'created') $resumo['grupos_criados']++;
            if (($resultado['endereco_acao'] ?? '') === 'created') $resumo['enderecos_criados']++;
            if (($resultado['endereco_acao'] ?? '') === 'updated') $resumo['enderecos_atualizados']++;
            if (($resultado['dispositivo_acao'] ?? '') === 'created') $resumo['cartoes_criados']++;
            if (($resultado['dispositivo_acao'] ?? '') === 'updated') $resumo['cartoes_atualizados']++;

            $preview[$indice]['pessoa_id'] = $resultado['pessoa_id'] ?? null;
            $preview[$indice]['dispositivos_solicitados_id'] = $resultado['dispositivos_solicitados_id'] ?? null;
            $preview[$indice]['sync_resultado'] = 'Sincronizado';
            $preview[$indice] = self::classificarPreview($preview[$indice]);
        }

        TSession::setValue(self::SESSION_KEY, $preview);
        return $resumo;
    }

    public static function sincronizarItemPreview(string $rowKey, array $opcoes = []): array
    {
        $preview = TSession::getValue(self::SESSION_KEY) ?? [];
        if (!$preview)
        {
            throw new Exception('Consulte os cartoes antes de sincronizar.');
        }

        $atualizarExistentes = ($opcoes['atualizar_existentes'] ?? 'S') === 'S';

        foreach ($preview as $indice => $item)
        {
            if (($item['row_key'] ?? '') !== $rowKey)
            {
                continue;
            }

            $resultado = self::sincronizarItem($item, $atualizarExistentes);
            $preview[$indice]['pessoa_id'] = $resultado['pessoa_id'] ?? null;
            $preview[$indice]['dispositivos_solicitados_id'] = $resultado['dispositivos_solicitados_id'] ?? null;
            $preview[$indice]['sync_resultado'] = 'Sincronizado';
            $preview[$indice] = self::classificarPreview($preview[$indice]);
            TSession::setValue(self::SESSION_KEY, $preview);

            return $resultado;
        }

        throw new Exception('Item do preview nao encontrado para sincronizacao.');
    }
}
