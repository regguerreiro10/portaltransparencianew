<?php

class AbastecimentoTagService
{
    private static function normalizarCodigoTagLido(?string $valor): string
    {
        $valor = trim((string) $valor);
        if ($valor === '')
        {
            return '';
        }

        if (strpos($valor, '{') === 0 || strpos($valor, '[') === 0)
        {
            $json = json_decode($valor, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json))
            {
                foreach (['numerocartao', 'uid_tag', 'codigo', 'tag'] as $campo)
                {
                    if (!empty($json[$campo]))
                    {
                        return trim((string) $json[$campo]);
                    }
                }
            }
        }

        return $valor;
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

    private static function localizarDotacaoParaAbastecimento(PedidoFrotas $pedido, float $valorTotal): SaldoDepartamento
    {
        if (empty($pedido->departamento_unit_id))
        {
            throw new Exception('O veiculo da TAG nao possui departamento definido para localizar a dotacao.');
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
            throw new Exception('Nenhuma dotacao foi encontrada para o departamento deste abastecimento.');
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
                'Nenhuma dotacao com saldo disponivel foi encontrada para o departamento deste abastecimento. ' .
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

    private static function obterSaldoDisponivel(Veiculos $veiculo, DispositivosSolicitados $dispositivo): float
    {
        $saldoVeiculo = (float) ($veiculo->saldo_veiculo ?? 0);
        if ($saldoVeiculo > 0)
        {
            return $saldoVeiculo;
        }

        $saldoAtualTag = (float) ($dispositivo->saldo_atual ?? 0);
        if ($saldoAtualTag > 0)
        {
            return $saldoAtualTag;
        }

        return (float) ($dispositivo->saldo_limite ?? 0);
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

    private static function obterDescricaoItemAbastecimento(array $dados, Veiculos $veiculo): string
    {
        $descricaoCombustivel = '';

        if (!empty($dados['tipo_combustivel_id']))
        {
            try
            {
                $tipoCombustivel = new TipoCombustivel($dados['tipo_combustivel_id']);
                $descricaoCombustivel = trim((string) ($tipoCombustivel->descricao ?? ''));
            }
            catch (Exception $e)
            {
                $descricaoCombustivel = '';
            }
        }

        if ($descricaoCombustivel !== '')
        {
            return 'Abastecimento de ' . $descricaoCombustivel . ' - ' . $veiculo->placa;
        }

        return 'Abastecimento TAG - ' . $veiculo->placa;
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

    public static function localizarTag(string $uid, ?int $systemUnitId = null): ?DispositivosSolicitados
    {
        $uid = self::normalizarCodigoTagLido($uid);
        if ($uid === '')
        {
            return null;
        }

        $query = DispositivosSolicitados::where('numerocartao', '=', $uid);
        if ($systemUnitId)
        {
            $query->where('system_unit_id', '=', $systemUnitId);
        }

        $dispositivo = $query->first();
        if ($dispositivo)
        {
            return $dispositivo;
        }

        $queryVeiculo = Veiculos::where('numero_dispositivo', '=', $uid);
        if ($systemUnitId)
        {
            $queryVeiculo->where('system_unit_id', '=', $systemUnitId);
        }

        $veiculo = $queryVeiculo->first();
        if (!$veiculo)
        {
            return null;
        }

        $queryDispositivo = DispositivosSolicitados::where('veiculos_id', '=', $veiculo->id);
        if ($systemUnitId)
        {
            $queryDispositivo->where('system_unit_id', '=', $systemUnitId);
        }

        return $queryDispositivo->first();
    }

    public static function obterResumoTag(string $uid, ?int $systemUnitId = null): array
    {
        $dispositivo = self::localizarTag($uid, $systemUnitId);
        if (!$dispositivo)
        {
            throw new Exception('TAG nÃ£o encontrada para o UID informado.');
        }

        if (empty($dispositivo->veiculos_id))
        {
            throw new Exception('A TAG localizada nÃ£o estÃ¡ vinculada a nenhum veÃ­culo.');
        }

        $veiculo = new Veiculos($dispositivo->veiculos_id);
        $responsavel = !empty($veiculo->responsavel_id) ? new Pessoa($veiculo->responsavel_id) : null;
        $departamento = !empty($veiculo->departamento_unit_id) ? new DepartamentoUnit($veiculo->departamento_unit_id) : null;
        $unidade = !empty($veiculo->system_unit_id) ? new SystemUnit($veiculo->system_unit_id) : null;
        $statusTag = !empty($dispositivo->status_dispositivos_id) ? new StatusDispositivos($dispositivo->status_dispositivos_id) : null;

        return [
            'uid_tag' => $uid,
            'dispositivos_solicitados_id' => $dispositivo->id,
            'veiculos_id' => $veiculo->id,
            'placa_modelo' => trim($veiculo->placa . ' - ' . ($veiculo->marca->descricao ?? '') . ' - ' . ($veiculo->modelo->descricao ?? '')),
            'responsavel_nome' => $responsavel ? $responsavel->nome : '',
            'departamento_nome' => $departamento ? $departamento->name : '',
            'unidade_nome' => $unidade ? $unidade->name : '',
            'status_tag' => $statusTag ? $statusTag->descricao : '',
            'saldo_atual' => self::obterSaldoDisponivel($veiculo, $dispositivo),
            'tipo_combustivel_id' => $veiculo->tipo_combustivel_id,
            'km' => $veiculo->hodometroatual,
            'descricaopedido' => 'Abastecimento TAG - ' . $veiculo->placa,
        ];
    }

    public static function registrarAbastecimento(array $dados): array
    {
        include_once 'app/service/CalculoTaxasImpostosService.php';

        $uid = trim((string) ($dados['uid_tag'] ?? ''));
        $systemUnitId = !empty($dados['system_unit_id']) ? (int) $dados['system_unit_id'] : null;
        $userid = $dados['system_users_id'] ?? TSession::getValue('userid');
        $entidadeId = $dados['entidade_id'] ?? TSession::getValue('entidade');
        $estabelecimentoId = !empty($dados['estabelecimento_id']) ? (int) $dados['estabelecimento_id'] : null;

        $dispositivo = self::localizarTag($uid, $systemUnitId);
        if (!$dispositivo)
        {
            throw new Exception('TAG nÃ£o encontrada para o UID informado.');
        }

        if (empty($dispositivo->veiculos_id))
        {
            throw new Exception('A TAG informada nÃ£o possui veÃ­culo vinculado.');
        }

        if (empty($estabelecimentoId))
        {
            throw new Exception('Informe o estabelecimento para continuar.');
        }

        $veiculo = new Veiculos($dispositivo->veiculos_id);
        $qtdeLitros = self::toFloat($dados['qtde_litros'] ?? 0);
        $valorLitro = self::toFloat($dados['valor_litro'] ?? 0);
        $valorTotal = round($qtdeLitros * $valorLitro, 2);
        $calculoTaxaContrato = self::calcularTotaisComTaxaContrato(
            $valorLitro,
            $qtdeLitros,
            $dados['taxa_contrato'] ?? TSession::getValue('taxacontrato') ?? 0
        );
        $saldoAtual = self::obterSaldoDisponivel($veiculo, $dispositivo);

        if ($qtdeLitros <= 0)
        {
            throw new Exception('A quantidade de litros deve ser maior que zero.');
        }

        if ($valorLitro <= 0)
        {
            throw new Exception('O valor por litro deve ser maior que zero.');
        }

        if ($saldoAtual < $valorTotal)
        {
            throw new Exception('Saldo insuficiente para realizar o abastecimento. Saldo atual: R$ ' . number_format($saldoAtual, 2, ',', '.'));
        }

        $dataPedido = new DateTime((string) ($dados['data_abastecimento'] ?? 'now'));
        $descricaoPedido = trim((string) ($dados['descricaopedido'] ?? ''));
        if ($descricaoPedido === '')
        {
            $descricaoPedido = 'Abastecimento TAG - ' . $veiculo->placa;
        }

        $condutorId = self::obterCondutorVeiculoId($veiculo);
        $cidadeId = self::obterCidadePessoaId($estabelecimentoId);
        if (empty($cidadeId))
        {
            throw new Exception('O estabelecimento selecionado precisa ter cidade cadastrada para gerar a proposta do abastecimento.');
        }

        $aprovadorId = self::obterAprovadorIdPorUsuario($userid);
        $descricaoItem = self::obterDescricaoItemAbastecimento($dados, $veiculo);

        $pedido = new PedidoFrotas();
        $pedido->dt_pedido = $dataPedido->format('Y-m-d H:i:s');
        $pedido->descricaopedido = $descricaoPedido;
        $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::FINALIZADO;
        $pedido->tipo_manutencao_id = 174;
        $pedido->veiculos_id = $veiculo->id;
        $pedido->estabelecimento_id = $estabelecimentoId;
        $pedido->cidade_id = $cidadeId;
        $pedido->km = $dados['km'] ?? null;
        $pedido->obs = $dados['obs'] ?? null;
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
        $pedido->system_unit_id = $veiculo->system_unit_id ?: $systemUnitId ?: TSession::getValue('idunit');
        $pedido->departamento_unit_id = $veiculo->departamento_unit_id;
        $pedido->system_users_id = $userid;
        $pedido->entidade_id = $entidadeId;
        $pedido->abastecimento = 1;
        $pedido->dispositivos_solicitados_id = $dispositivo->id;
        $pedido->valor_litro = $valorLitro;
        $pedido->store();

        $itemPedido = new ItensPedidoFrotas();
        $itemPedido->pedido_frotas_id = $pedido->id;
        $itemPedido->tipo = 1;
        $itemPedido->qtde = $qtdeLitros;
        $itemPedido->descricao = $descricaoItem;
        $itemPedido->valor_unitario = $valorLitro;
        $itemPedido->valor_desconto = $calculoTaxaContrato['desconto'];
        $itemPedido->valor_total = $calculoTaxaContrato['valor_liquido'];
        $itemPedido->marca_modelo = trim($veiculo->placa . ' - ' . ($veiculo->marca->descricao ?? '') . ' - ' . ($veiculo->modelo->descricao ?? ''));
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
        $proposta->system_users_id = $userid;
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

        $dotacao = self::localizarDotacaoParaAbastecimento($pedido, $valorTotal);
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
        $itemProposta->qtde = $qtdeLitros;
        $itemProposta->valor = $valorLitro;
        $itemProposta->perc_desconto = $calculoTaxaContrato['desconto'];
        $itemProposta->valor_total = $calculoTaxaContrato['valor_liquido'];
        $itemProposta->marca_modelo = $itemPedido->marca_modelo;
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

        $novoSaldo = round($saldoAtual - $valorTotal, 2);

        $movimentoSaldo = new SaldoVeiculo();
        $movimentoSaldo->tipo_transacao = 'D';
        $movimentoSaldo->system_users_id = $userid;
        $movimentoSaldo->motivo_transacao = 'Abastecimento TAG - pedido #' . $pedido->id;
        $movimentoSaldo->data_transacao = $dataPedido->format('Y-m-d');
        $movimentoSaldo->valor_transacao = $valorTotal;
        $movimentoSaldo->veiculos_id = $veiculo->id;
        $movimentoSaldo->saldo_disponivel = $novoSaldo;
        $movimentoSaldo->mes_transacao = $dataPedido->format('m');
        $movimentoSaldo->ano_transacao = $dataPedido->format('Y');
        $movimentoSaldo->store();

        $veiculo->saldo_veiculo = $novoSaldo;
        if (!empty($dados['km']))
        {
            $veiculo->hodometroatual = $dados['km'];
        }
        $veiculo->store();

        $dispositivo->saldo_atual = $novoSaldo;
        $dispositivo->store();

        return [
            'pedido_id' => $pedido->id,
            'abastecimento_id' => $pedido->id,
            'proposta_id' => $proposta->id,
            'item_pedido_id' => $itemPedido->id,
            'item_proposta_id' => $itemProposta->id,
            'veiculos_id' => $veiculo->id,
            'saldo_anterior' => $saldoAtual,
            'saldo_atual' => $novoSaldo,
            'valor_total' => $valorTotal,
        ];
    }

    public static function toFloat($value): float
    {
        if (is_numeric($value))
        {
            return (float) $value;
        }

        $value = str_replace('.', '', (string) $value);
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }
}
