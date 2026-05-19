<?php 

class ClienteAPIRest
{
    const DATABASE      = 'minierp';

    public static function myCliente($param)
    {
        try
        {
            TTransaction::open(self::DATABASE);

             $idunit = 26;

            // parâmetros opcionais da API
            $dtFinalizacaoIni = $param['dt_finalizacao_ini'] ?? null;
            $dtFinalizacaoFim = $param['dt_finalizacao_fim'] ?? null;

            // base da query de propostas
            $propostasQuery = Propostas::where('system_unit_id', '=', $idunit)
                ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::FINALIZADO);

            // aplica filtro por data de finalização se informado
            if ($dtFinalizacaoIni || $dtFinalizacaoFim) {

                // busca pedidos no intervalo
                $pedidosQuery = PedidoFrotas::where('system_unit_id', '=', $idunit);

                if ($dtFinalizacaoIni) {
                    $pedidosQuery = $pedidosQuery->where('dt_finalizacao', '>=', $dtFinalizacaoIni);
                }

                if ($dtFinalizacaoFim) {
                    $pedidosQuery = $pedidosQuery->where('dt_finalizacao', '<=', $dtFinalizacaoFim);
                }

                $pedidos = $pedidosQuery->load();

                $pedidoIds = [];

                if ($pedidos) {
                    foreach ($pedidos as $ped) {
                        $pedidoIds[] = $ped->id;
                    }
                }

                // se encontrou pedidos, aplica IN na proposta
                if (!empty($pedidoIds)) {
                    $propostasQuery = $propostasQuery->where('pedido_frotas_id', 'IN', $pedidoIds);
                } else {
                    // nenhum pedido no período → retorno vazio
                    $propostas = [];
                }
            }

            // só carrega se não foi definido como vazio
            if (!isset($propostas)) {
                $propostas = $propostasQuery
                    ->orderBy('id', 'desc')
                    ->load();
            }

            // só carrega se ainda não setou $propostas vazio acima
            if (!isset($propostas)) {
                $propostas = $propostasQuery
                    ->orderBy('id', 'desc')
                    ->load();
            }
            // $propostas = Propostas::where('system_unit_id', '=', $idunit)
            //                     ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::FINALIZADO)
            //                     ->where('id', 'IN', [8716, 8723, 8734, 8740, 8742])
            //                     ->orderBy('id', 'desc')
            //                     ->load();

            $data = [];

            if ($propostas)
            {
                foreach ($propostas as $prop)
                {
                              // Ajuste os campos conforme sua tabela Propostas
                    $cliente = Pessoa::where('id', '=', $prop->pessoa_id)->first();
                    $veiculo = Veiculos::where('id', '=', $prop->veiculos_id)->first();

                    // Monta o "pacote" dessa proposta/pedido
                    $registro = [
                        'pedido_proposta' => null, // todos os campos da Propostas
                        'unidade' => null, // todos os campos da Propostas
                        'condutor' => null, // todos os campos da Propostas
                        'estabelecimento'  => null,
                        'veiculo'  => null,
                        'dotacao_orcamentaria'  => null,
                        // 'faturas'  => null,
                        // 'nfs_consumo'  => null,
                        'nfsdocumentos_credenciado'  => null,
                        'itens'    => []
                    ];
                    $pedido = PedidoFrotas::where('id','=',$prop->pedido_frotas_id)->first();
                    $tipomanutencao = TipoManutencao::where('id','=',$pedido->tipo_manutencao_id)->first();
                    $pedidofrotashistorico = PedidoFrotasHistorico::where('pedido_frotas_id','=',$pedido->id)
                                                                  ->where('estado_pedido_frotas_id','=',EstadoPedidoFrotas::FINALIZADO)
                                                                  ->last();
                    $estadopedidofrotas = new EstadoPedidoFrotas($prop->estado_pedido_frotas_id);
                    //propostas
                    $propArr = [];
                    $propArr['id'] = $prop->id;
                    $propArr['pedido_id'] = $prop->pedido_frotas_id;
                    $propArr['id_status'] = $prop->estado_pedido_frotas_id;
                    $propArr['dc_status'] = $estadopedidofrotas->nome;
                    $propArr['categoriaOS'] = $tipomanutencao->desscricao;
                    $propArr['dt_abertura'] = $pedido->dt_pedido;
                    $propArr['dt_orcamento'] = $prop->data_cotacao;
                    $propArr['dt_entrega'] = $prop->data_retirada_veiculo;
                    $propArr['dt_finalizacao'] = $pedidofrotashistorico->data_operacao;
                    $propArr['dt_cancelamento'] = null;
                    $propArr['dt_rejeita'] = null;
                    $propArr['data_atualizacao_km'] = null;
                    $propArr['km_veiculos_os'] = $pedido->km;
                    $propArr['dc_observacao'] = $pedido->obs;
                    $propArr['id_orgao'] = 26;
                    $propArr['id_sub_unidade'] = $pedido->departamento_unit_id;

                    $registro['pedido_proposta'] = $propArr;

                    $notasystemunit = NotasSystemUnit::where('system_unit_id', '=', $prop->system_unit_id)->load();

                    // $faturasArr = [];

                    // if ($notasystemunit) {
                    //     foreach ($notasystemunit as $nots) {
                    //         $dep = new DepartamentoUnit($nots->departamento_unit_id) ;
                    //         $faturasArr[] = [
                    //             'id_fatura'             => $nots->id,
                    //             'mes_ano'  => $nots->mes_ano,
                    //             'link' => $nots->caminho,
                    //             'numero' => $nots->numero,
                    //             'valor'          => number_format((float) $nots->valor, 2, '.', ''),
                    //             'sub_unidade' => $dep->name,
                    //         ];
                    //     }
                    // }

                    // $registro['faturas'] = $faturasArr;

                    $documentospropostas = DocumentosPropostas::where('propostas_id', '=', $prop->id)->load(); 
                    $docArr = [];
                    if ($documentospropostas) {
                        foreach ($documentospropostas as $docp) {

                            $tipodoc = new TipoDocumentosPropostas($docp->tipo_documentos_propostas_id);

                            $docArr[] = [
                                'id'             => $docp->id,
                                'num_documento'  => $docp->numero,
                                'link_documento' => $docp->caminho,
                                'tipo_documento' => $tipodoc->descricao,
                                'data' => $docp->created_at,
                                'valor'          => null,
                            ];
                        }
                    }

                    $registro['nfsdocumentos_credenciado'] = $docArr;

                    $dotacao_pedido_frotas = DotacaoPedidoFrotas::where('pedido_frotas_id', '=', $prop->pedido_frotas_id)->load(); 
                    $dotArr = [];
                    if ($dotacao_pedido_frotas) {
                        foreach ($dotacao_pedido_frotas as $dot) {
                            $sd = new SaldoDepartamento($dot->saldo_departamento_id);
                            $dep = new DepartamentoUnit($sd->departamento_unit_id) ;
                            $tipodotacao = null;
                            if ($sd->tipo == 'P') {
                               $tipodotacao='Produto';
                            } else {
                               $tipodotacao='Serviço';
                            }
                            $dotArr[] = [
                                'id'             => $dot->id,
                                'pedido_id'  => $dot->pedido_frotas_id,
                                'sub_unidade' => $dep->name,
                                'tipo_dotacao' => $tipodotacao,
                                'empenho' => $dep->numero_documento_empenho,
                                'processo' => $dep->numero_processo,
                                'valor'          => number_format((float) $dot->valor, 2, '.', ''),
                            ];
                        }
                    }

                    $registro['dotacao_orcamentaria'] = $dotArr;

                    // $nfsconsumoArr=[];
                    // $registro['nfs_consumo'] = $nfsconsumoArr;


                    $unidade = new SystemUnit(26);
                    $subunidade = new DepartamentoUnit($pedido->departamento_unit_id);
                    $unidArr = [];
                    $unidArr['id_unidade'] = $unidade->id;
                    $unidArr['dc_unidade'] = $unidade->name;
                    $unidArr['id_sub_unidade'] = $subunidade->id;
                    $unidArr['dc_sub_unidade'] = $subunidade->name;
                    $registro['unidade'] = $unidArr;

                    $condutor = new Pessoa($prop->motorista_retirada_id);
                    $condutorArr = [];
                    $condutorArr['registrocondutor_retirou'] = $condutor->id;
                    $condutorArr['condutor_retirou'] = $condutor->nome;
                    $condutorArr['cpfcondutor_retirou'] = $condutor->cpf;
                    $condutorArr['cnhcondutor_retirou'] = $condutor->numero_registro_cnh;
                    $registro['condutor'] = $condutorArr;

                    // CLIENTE (todos os campos)
                    if ($cliente)
                    {
                        $cliArr = [];

                        // endereço principal
                        $pessoaendereco = PessoaEndereco::where('pessoa_id', '=', $cliente->id)
                                                        ->where('principal', '=', 'T')
                                                        ->first();

                        $cidade = null;
                        $estado = null;

                        if ($pessoaendereco) {
                            $cidade = Cidade::where('id', '=', $pessoaendereco->cidade_id)->first();
                            if ($cidade) {
                                $estado = Estado::where('id', '=', $cidade->estado_id)->first();
                            }
                        }

                        $cliArr['cod']                 = $cliente->id;
                        $cliArr['cnpj']                = $cliente->documento;
                        $cliArr['inscricao_estadual']  = null;
                        $cliArr['dc_estabelecimento']  = $cliente->nome;
                        $cliArr['rz_estabelecimento']  = null;

                        if ($pessoaendereco) {
                            $cliArr['endereco'] = $pessoaendereco->rua;
                            $cliArr['numero']   = $pessoaendereco->numero;
                            $cliArr['bairro']   = $pessoaendereco->bairro;
                            $cliArr['cidade']   = $cidade ? $cidade->nome  : null;
                            $cliArr['estado']   = $estado ? $estado->sigla : null;
                            $cliArr['cep']      = $pessoaendereco->cep;
                        } else {
                            $cliArr['endereco'] = null;
                            $cliArr['numero']   = null;
                            $cliArr['bairro']   = null;
                            $cliArr['cidade']   = null;
                            $cliArr['estado']   = null;
                            $cliArr['cep']      = null;
                        }

                        $cliArr['telefone']  = $cliente->fone;
                        $cliArr['fax']       = null;
                        $cliArr['contato']   = null;
                        $cliArr['cpf']       = null;
                        $cliArr['email']     = $cliente->email;

                        if ($pessoaendereco) {
                            $cliArr['longitude'] = $pessoaendereco->longitude;
                            $cliArr['latitude']  = $pessoaendereco->latitude;
                        } else {
                            $cliArr['longitude'] = null;
                            $cliArr['latitude']  = null;
                        }

                        $registro['estabelecimento'] = $cliArr;
                    }


                    // VEÍCULO (todos os campos)
                    // VEÍCULO (todos os campos)
                   // VEÍCULO (campos selecionados)
                    if ($veiculo)
                    {
                        $veiArr = [];

                        // busca marca e modelo (se existirem)
                        $marca  = Marca::where('id', '=', $veiculo->marca_id)->first();
                        $modelo = Modelo::where('id', '=', $veiculo->modelo_id)->first();

                        $veiArr['id']             = $veiculo->id;
                        $veiArr['prefixo']        = $veiculo->prefixo;
                        $veiArr['placa']          = $veiculo->placa;
                        $veiArr['km']             = $veiculo->hodometroatual;
                        $veiArr['km_pedido']      = $pedido->km;
                        $veiArr['id_marca']       = $veiculo->marca_id;
                        $veiArr['dc_marca']       = $marca  ? $marca->descricao  : null;
                        $veiArr['id_modelo']      = $veiculo->modelo_id;
                        $veiArr['dc_modelo']      = $modelo ? $modelo->descricao : null;
                        $veiArr['ano_fabricacao'] = $veiculo->anof; // ou o campo correto

                        $registro['veiculo'] = $veiArr;
                    }


                    // ITENS DA PROPOSTA (todos os campos)
                    $itens = ItensPropostas::where('propostas_id', '=', $prop->id)->load();
                    if ($itens)
                    {
                        foreach ($itens as $it)
                        {
                            $manutencaogarantia = ManutencaoGarantia::where('itens_propostas_id', '=', $it->id)
                                                                    ->first();

                            $produto = new Produto($it->produto_id);
                            $familiaproduto = FamiliaProduto::where('id', '=', $produto->familia_produto_id)
                                                            ->first();
                            $tipopeca = new TipoPecas($it->tipo_pecas_id);

                            $valor_unitario = $it->valor ?? 0;        // evita null no round
                            $valor_desconto    = $it->perc_desconto ?? 0;  // evita null no round
                            $valor_total    = $it->valor_total ?? 0;  // evita null no round

                            $itensArr = [];
                            $itensArr['id_item']        = $it->id;
                            $itensArr['pedido_id']        = $prop->pedido_frotas_id;
                            $itensArr['propostas_id']        = $prop->id;
                            $itensArr['id_tipo']        = $it->tipo;
                            if ($it->tipo==1) {
                                $itensArr['dc_tipo']        = 'Produto';
                            } else {
                                $itensArr['dc_tipo']        = 'Serviço';
                            }
                            $itensArr['id_categoria']   = $familiaproduto ? $familiaproduto->id        : null;
                            $itensArr['dc_categoria']   = $familiaproduto ? $familiaproduto->nome : null;
                            $itensArr['dc_item']        = $produto->nome;
                            $itensArr['qtd']            = $it->qtde;
                            $itensArr['vl_unitario']   = number_format((float) $valor_unitario, 2, '.', '');
                            $itensArr['vl_desconto']   = number_format((float) $valor_desconto, 2, '.', '');
                            $itensArr['vl_total_item'] = number_format((float) $valor_total, 2, '.', '');

                            $itensArr['dt_garantia']    = $manutencaogarantia ? $manutencaogarantia->datagarantia   : null;
                            $itensArr['tipo_peca']      = $tipopeca->descricao;
                            $itensArr['qtd_percorrer']  = null;
                            $itensArr['garantia']       = $manutencaogarantia ? $manutencaogarantia->dias_garantia  : null;
                            $itensArr['dc_marca']       = $it->marca_modelo;
                            $itensArr['id_num_serie']   = $it->codigo;
                            $itensArr['dc_pos_aplicacao']= null;
                            $itensArr['cod_os_unico']   = $it->produto_id;
                            $itensArr['valor_referencia']= null;

                            $registro['itens'][] = $itensArr;
                        }
                    }


                    // Adiciona esse registro no array final
                    $data[] = $registro;
                }
            }

            TTransaction::close();

            return [
                'status' => 'success',
                'total'  => count($data),
                'data'   => $data
            ];
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            return [
                'status' => 'error',
                'erro'   => $e->getMessage()
            ];
        }
    }
}
