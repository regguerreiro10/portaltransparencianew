<?php

class PessoaContaPagarEmAbertoDocument extends TPage
{
    private static $database = 'minierp';
    private static $activeRecord = 'Pessoa';
    private static $primaryKey = 'id';
    private static $htmlFile = 'app/documents/PessoaContaPagarEmAbertoDocumentTemplate.html';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

    }

    public static function onGenerate($param)
    {
        try 
        {   
            include_once 'app/service/CalculoTaxasImpostosService.php';

            $checkselected = TSession::getValue('ContaPagarEmAbertoSimpleListbuilder_datagrid_check');

            if(empty($checkselected)){
                new TMessage('info', 'Não foi selecionado nem um item.');
                return;
            }

            $checkselected = array_map('intval', array_values($checkselected));
            TSession::setValue('PessoaContaPagarEmAbertoDocument_last_selected_' . (int) $param['key'], $checkselected);

            TSession::setValue('ContaPagarEmAbertoSimpleListbuilder_datagrid_check', null);

            // new TMessage('info', json_encode($checkselected));

            TTransaction::open(self::$database);

            $class = self::$activeRecord;
            $object = new $class($param['key']);

            $html = new AdiantiHTMLDocumentParser(self::$htmlFile);
            $html->setMaster($object);

            $ent = new Entidade(TSession::getValue('entidade'));
            $taxacontrato = $ent->taxacontrato;

                $taxaspessoa = TaxasPessoa::where('pessoa_id', '=', $param['key'])
                                        ->where('system_unit_id', '=', TSession::getValue('idunit'))
                                        ->where('entidade_id', '=', TSession::getValue('entidade'))
                                        ->load();

                //separar endereço para pegar cidade para proposta

            $taxaadm = 0;
            $taxaantecipacao = 0;
            // $taxacontrato = 0;
            $taxabancaria = 0;
            $ir_produto = 0;
            $csll_produto = 0;
            $cofins_produto = 0;
            $pis_produto = 0;
            $ir_servico = 0;
            $csll_servico = 0;
            $cofins_servico = 0;
            $pis_servico = 0;
            $iss_servico = 0;

            if ($taxaspessoa) {
                foreach($taxaspessoa as $taxa) {
                  $taxaadm = $taxa->taxaadm;
                  $taxaantecipacao = $taxa->taxaantecipacao;
                    $taxabancaria = $taxa->taxabancaria;
                    // $taxacontrato = $taxa->taxacontrato;

                    $ir_produto = $taxa->ir;
                    $csll_produto = $taxa->csll;
                    $cofins_produto = $taxa->cofins;
                    $pis_produto = $taxa->pis;

                    $ir_servico = $taxa->ir_servico;
                    $csll_servico = $taxa->csll_servico;
                    $cofins_servico = $taxa->cofins_servico;
                    $pis_servico = $taxa->pis_servico;
                    $iss_servico = $taxa->iss_servico;
                  break;
                }
            }        
            

            $criteriaConta = new TCriteria;
            $criteriaConta->add(new TFilter('pessoa_id',      '=', $param['key']));
            $criteriaConta->add(new TFilter('tipo_conta_id',  '=', TipoConta::PAGAR));
            $criteriaConta->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
            $criteriaConta->add(new TFilter('dt_pagamento',   'is', NULL));
            $criteriaConta->add(new TFilter('id',             'IN', $checkselected));

            $conta = Conta::getObjects($criteriaConta);

            $html->setDetail('Conta.pessoa_id', $conta);

            $pedidoIds = [];
            if($conta){
                foreach($conta as $c){
                    if(!empty($c->pedido_frotas_id)){
                        $pedidoIds[] = (int) $c->pedido_frotas_id;
                    }
                }
            }

            $pedidoIds = array_values(array_unique($pedidoIds));

            $mixPorPedido = [];

            // pega propostas por pedido
            if ($pedidoIds) {
                $propostas = Propostas::where('pessoa_id', '=', $object->id)
                                    ->where('pedido_frotas_id', 'IN', $pedidoIds)
                                    ->load();

                $propostasPorPedidoF = [];
                if ($propostas) {
                    foreach ($propostas as $p) {
                        $propostasPorPedidoF[(int)$p->pedido_frotas_id][] = (int)$p->id;
                    }
                }

                $propostaToPedido = [];
                $propostaIdsAll = [];

                foreach($propostasPorPedidoF as $pedido_frotas_id => $propostasIds){
                    $mixPorPedido[$pedido_frotas_id] = ['prod' => 0.0, 'serv' => 0.0, 'total' => 0.0, 'desconto' => 0.0];

                    foreach($propostasIds as $propIds){
                        $propostaToPedido[$propIds] = $pedido_frotas_id;
                        $propostaIdsAll[] = $propIds;
                    }
                }

                $propostasIdsAll = array_values(array_unique($propostaIdsAll));

                if($propostasIdsAll){
                    $itens = ItensPropostas::where('propostas_id', 'IN', $propostaIdsAll)->load();

                    if($itens){
                        foreach($itens as $item){
                            $propostaId = (int)($item->propostas_id ?? 0);
                            $pedidoId   = $propostaToPedido[$propostaId] ?? 0;
                            if (!$pedidoId) {
                                continue;
                            }

                            $valorUnitario = (float)($item->valor ?? 0);
                            $qtd          = (float)($item->qtde ?? 1);
                            $tipo         = (int)($item->tipo ?? 0);

                            $valorItem = CalculoTaxasImpostosService::money($valorUnitario * $qtd);

                            if ($tipo === 1) {
                                $mixPorPedido[$pedidoId]['prod'] = CalculoTaxasImpostosService::money($mixPorPedido[$pedidoId]['prod'] + $valorItem);
                            } elseif ($tipo === 2) {
                                $mixPorPedido[$pedidoId]['serv'] = CalculoTaxasImpostosService::money($mixPorPedido[$pedidoId]['serv'] + $valorItem);
                            }
                            $mixPorPedido[$pedidoId]['desconto'] = CalculoTaxasImpostosService::money(
                                $mixPorPedido[$pedidoId]['desconto'] + ((float) ($item->perc_desconto ?? 0))
                            );
                        }
                    }
                }

                // 3) calcula total por pedido
                foreach ($mixPorPedido as $pedidoId => $vals) {
                    $mixPorPedido[$pedidoId]['total'] = CalculoTaxasImpostosService::money($vals['prod'] + $vals['serv']);
                }
            }

            

            if ($conta) {
                // // Evita divisão por zero
                // $valortotalbruto = (float) $valortotalbruto;
                // $porcProduto_s_tx_c = ($valortotalbruto > 0) ? ((float)$total_produtos / $valortotalbruto) : 0.0;//encontra porcentagem 
                // $porcServico_s_tx_c = ($valortotalbruto > 0) ? ((float)$total_servicos / $valortotalbruto) : 0.0;//encontra porcentagem

                // percentuais
                $perc_tx_contrato = (float) $taxacontrato;
                $perc_tx_adm      = (float) $taxaadm;
                $perc_tx_ant      = (float) $taxaantecipacao;

                // impostos totais (%)
                $totalPorcProduto = (float) $ir_produto + $csll_produto + $cofins_produto + $pis_produto;
                $totalPorcServico = (float) $ir_servico + $csll_servico + $cofins_servico + $pis_servico + $iss_servico;

                foreach ($conta as $c) {

                    $pedidoIdConta = (int)($c->pedido_frotas_id ?? 0);

                    $totPedido = $mixPorPedido[$pedidoIdConta]['total'] ?? 0.0;
                    $totProd   = $mixPorPedido[$pedidoIdConta]['prod']  ?? 0.0;
                    $totServ   = $mixPorPedido[$pedidoIdConta]['serv']  ?? 0.0;
                    $totDesc   = $mixPorPedido[$pedidoIdConta]['desconto'] ?? 0.0;

                    // percentuais por CONTA (pelo pedido dela)
                    $porcProduto_s_tx_c = ($totPedido > 0) ? ($totProd / $totPedido) : 0.0;
                    $porcServico_s_tx_c = ($totPedido > 0) ? ($totServ / $totPedido) : 0.0;

                    // 0) Bruto da própria conta (NÃO mexe!)
                    $bruto = ($totPedido > 0) ? CalculoTaxasImpostosService::money($totPedido) : CalculoTaxasImpostosService::money((float) ($c->valor ?? 0));

                    // 0.1) Bruto separado por mix produto/serviço (só para exibir colunas)
                    $bruto_prod_s_tx_cont = CalculoTaxasImpostosService::money($bruto * $porcProduto_s_tx_c); //valor bruto produto //
                    $bruto_serv_s_tx_cont = CalculoTaxasImpostosService::money($bruto - $bruto_prod_s_tx_cont); // valor bruto servico

                    // 1) Taxa contratual em cima do bruto da conta
                    $valor_txcontrato = ($totDesc > 0) ? CalculoTaxasImpostosService::money($totDesc) : CalculoTaxasImpostosService::money($bruto * ($perc_tx_contrato / 100));
                    $base_pos_txcontrato = max(0.0, CalculoTaxasImpostosService::money($bruto - $valor_txcontrato));

                    // 1.1) Base pós-contrato separada por mix
                    $base_prod_ctxc = CalculoTaxasImpostosService::money($base_pos_txcontrato * $porcProduto_s_tx_c);
                    $base_serv_ctxc = CalculoTaxasImpostosService::money($base_pos_txcontrato - $base_prod_ctxc);

                    // 2) Impostos em cima das bases (produto e serviço)
                    $vl_imp_prod = CalculoTaxasImpostosService::money($base_prod_ctxc * ($totalPorcProduto / 100));
                    $vl_imp_serv = CalculoTaxasImpostosService::money($base_serv_ctxc * ($totalPorcServico / 100));

                    $liq_prod_pos_imp = CalculoTaxasImpostosService::money($base_prod_ctxc - $vl_imp_prod);
                    $liq_serv_pos_imp = CalculoTaxasImpostosService::money($base_serv_ctxc - $vl_imp_serv);

                    $total_pos_impostos_ps = CalculoTaxasImpostosService::money($liq_prod_pos_imp + $liq_serv_pos_imp);

                    // 3) Taxa Adm e Antecipação em cima do total pós impostos
                    $valor_txadm = CalculoTaxasImpostosService::money($total_pos_impostos_ps * ($perc_tx_adm / 100));
                    $base_pos_adm_imp = CalculoTaxasImpostosService::money($total_pos_impostos_ps - $valor_txadm);

                    $valor_txantecipacao = CalculoTaxasImpostosService::money($base_pos_adm_imp * ($perc_tx_ant / 100));
                    $valor_final_imp_adm_ant = CalculoTaxasImpostosService::money($base_pos_adm_imp - $valor_txantecipacao);

                    // ===== Preenche campos da tabela =====

                    // Linha de cima
                    $c->valor_txcontrato = $valor_txcontrato ?? 0;

                    // Linha de baixo
                    $c->valor_produto_s_desc_txc = $bruto_prod_s_tx_cont ?? 0;       // vl produto bruto (antes tx contrato) = não usado
                    $c->valor_servico_s_desc_txc = $bruto_serv_s_tx_cont ?? 0;       // vl serviço bruto = não usado

                    // "Valor Liquido" (na sua tabela de cima) = pós taxa contratual
                    $c->valor_liquido = $base_pos_txcontrato ?? 0;

                    $c->valor_produto_c_desc_txc = $base_prod_ctxc ?? 0;     // liq produto pós impostos
                    $c->valor_servico_c_desc_txc = $base_serv_ctxc ?? 0;     // liq serviço pós impostos

                    $c->valor_liqbase_prod_posimp = $liq_prod_pos_imp ?? 0;
                    $c->valor_liqbase_serv_posimp = $liq_serv_pos_imp ?? 0;

                    $c->valor_txc_imp_produto_servico = $total_pos_impostos_ps ?? 0; // liq produto+serviço pós impostos
                    

                    //todo array tem indice => valor, o indece é int que cada chave inicia representando 0 em diante.
                    $impostosGrupos = [
                        [
                            'base_valor' => $base_prod_ctxc,
                            'campos_base' => [
                                'valor_produto_c_desc_txc' => $base_prod_ctxc,
                                'valor_liqbase_prod_posimp' => $liq_prod_pos_imp
                            ],
                            'impostos' => [
                                'ir' => $ir_produto,
                                'csll' => $csll_produto,
                                'cofins' => $cofins_produto,
                                'pis' => $pis_produto
                            ],
                        ],
                        [
                            'base_valor' => $base_serv_ctxc,
                            'campos_base' => [
                                'valor_servico_c_desc_txc' => $base_serv_ctxc,
                                'valor_liqbase_serv_posimp' => $liq_serv_pos_imp
                            ],
                            'impostos' => [
                                'ir_servico' => $ir_servico,
                                'csll_servico' => $csll_servico,
                                'cofins_servico' => $cofins_servico,
                                'pis_servico' => $pis_servico,
                                'iss_servico' => $iss_servico
                            ],
                        ],
                    ];

                    foreach($impostosGrupos as $grupos){
                        
                        if($grupos['base_valor'] == 0){

                            foreach($grupos['campos_base'] as $campo => $valor){
                                $c->$campo = 0;
                            }

                            foreach($grupos['impostos'] as $campoimposto => $valor){
                                $c->$campoimposto = 0; 
                            }
                            continue;
                        }

                        foreach($grupos['campos_base'] as $base_liq => $valor){
                            $c->$base_liq = (float) $valor;
                        }

                        foreach($grupos['impostos'] as $imposto => $valor){

                            if($valor == 0 || $valor == '' || $valor == null){
                                $c->$imposto = 0;
                                continue; 
                            }
                            $c->$imposto = (float) $valor;

                        }
                    }


                    $taxa_adm_ant = [
                        'taxas' => [
                            'valor_txadm' => $valor_txadm,
                            'valor_txantecipacao' => $valor_txantecipacao
                        ],
                    ];

                    foreach($taxa_adm_ant['taxas'] as $tx_adm_ant => $valor){
                        if($valor == 0 || $valor == '' || $valor == null){
                            $c->$tx_adm_ant = 0;
                            continue;
                        }

                        $c->$tx_adm_ant = (float) $valor;
                    }

                    // $c->valor_txadm = $valor_txadm ?? 0;
                    // $c->valor_txantecipacao = $valor_txantecipacao ?? 0;

                    // Total final da conta
                    $c->valor_total_liq_tx_conta = $valor_final_imp_adm_ant ?? 0;

                    $c->store();
                }
            }

            
            $unit_id = TSession::getValue('idunit');

            $unit_name = $unit_name ?? '';

            if (!empty($unit_id)) {
                TTransaction::open('minierp');
                $unit = new SystemUnit((int) $unit_id);
                $object->unit_name = $unit->name ?? $unit_name;
                TTransaction::close();
            }


            $endereco = PessoaEndereco::where('pessoa_id','=',$param['key'])
                                      ->where('principal','=','T')
                                      ->load();

            if ($endereco) {
                foreach($endereco as $end) {
                     $object->rua = $end->rua;
                     $object->bairro = $end->bairro;
                     $object->cep = $end->cep;

                     $cid = new Cidade($end->cidade_id);
                     $object->cidade = $cid->nome;

                     $est = new Estado($cid->estado_id);
                     $object->estado = $est->sigla;
                     break;
                }
            } else {
                $object->rua = '';
                $object->bairro = '';
            }
                   
            $object->taxa = number_format($perc_tx_contrato, 2, ',', '.');     
            $object->taxaadministrativa = number_format($taxaadm, 2, ',', '.');
            $object->taxaantecipacao = number_format((float)($taxaantecipacao ?? 0), 2, ',', '.');

            $object->total = 0;
            $object->subtotal = 0;
            $object->pessoa_id = $param['key'];

            //fomatação/ajuste valores
            if($conta)
            {
                foreach($conta as $c)
                {

                    if ($c->pedido_venda_id<>null) {
                    $object->pedido_venda_id = $c->pedido_venda_id;
                    }
                    else {
                    $object->pedido_frotas_id = $c->pedido_frotas_id;
                    }
                    $c->dt_emissao = TDate::date2br($c->dt_emissao);
                    $c->dt_vencimento = TDate::date2br($c->dt_vencimento);

                    $c->valor = 'R$ ' . number_format($c->valor, 2, ',', '.');

                    $c->valor_txcontrato = 'R$ ' . number_format($c->valor_txcontrato, 2, ',', '.');
                    // $c->valor_txadm = 'R$ ' . number_format($c->valor_txadm, 2, ',', '.');
                    // $c->valor_txantecipacao = 'R$ ' . number_format($c->valor_txantecipacao, 2, ',', '.');
                    
                    $c->valor_produto_s_desc_txc = 'R$ ' . number_format($c->valor_produto_s_desc_txc, 2, ',', '.');
                    $c->valor_servico_s_desc_txc = 'R$ ' . number_format($c->valor_servico_s_desc_txc, 2, ',', '.');

                    $c->valor_liquido = 'R$ ' . number_format($c->valor_liquido, 2, ',', '.');

                    // $c->valor_produto_c_desc_txc = 'R$ ' . number_format($c->valor_produto_c_desc_txc, 2, ',', '.');
                    // $c->valor_servico_c_desc_txc = 'R$ ' . number_format($c->valor_servico_c_desc_txc, 2, ',', '.');
                    
                    
                    // $c->ir = number_format($c->ir, 2, ',', '.') . '%';
                    // $c->csll = number_format($c->csll, 2, ',', '.') . '%';
                    // $c->cofins = number_format($c->cofins, 2, ',', '.') . '%';
                    // $c->pis = number_format($c->pis, 2, ',', '.') . '%';

                    // $c->ir_servico = number_format($c->ir_servico, 2, ',', '.') . '%';
                    // $c->csll_servico = number_format($c->csll_servico, 2, ',', '.') . '%';
                    // $c->cofins_servico = number_format($c->cofins_servico, 2, ',', '.') . '%';
                    // $c->pis_servico = number_format($c->pis_servico, 2, ',', '.') . '%';
                    // $c->iss_servico = number_format($c->iss_servico, 2, ',', '.') . '%';

                    // $c->valor_liqbase_prod_posimp = 'R$ ' . number_format($c->valor_liqbase_prod_posimp, 2, ',', '.');
                    // $c->valor_liqbase_serv_posimp = 'R$ ' . number_format($c->valor_liqbase_serv_posimp, 2, ',', '.');

                    $c->valor_txc_imp_produto_servico = 'R$ ' . number_format($c->valor_txc_imp_produto_servico, 2, ',', '.');

                    $object->total += $c->valor_total_liq_tx_conta;
                    // $c->valor_desconto = number_format($c->valor_txcontrato+$c->valor_txadm+$c->valor_txantecipacao, 2, ',', '.');
                    $c->valor_total_liq_tx_conta = 'R$ ' . number_format($c->valor_total_liq_tx_conta, 2, ',', '.');
                }
            }
            $totalx = ($object->total - $taxabancaria);
            $totaladmx=0;
            // if ($taxaadm>0) {
            //     $totaladmx = $totalx * ($taxaadm/100);
            // }
            $totalx = $totalx - $totaladmx;
            $object->subtotal = 'R$ ' . number_format($totalx , 2, ',', '.');
            $object->taxabancaria = 'R$ ' . number_format((float)($taxabancaria ?? 0), 2, ',', '.');
            $object->taxaadm = number_format($totaladmx, 2, ',', '.');
            $object->perctaxaadm = number_format($taxaadm, 2, ',', '.');
            $object->total = 'R$ ' . number_format($object->total, 2, ',', '.');
            

            $pageSize = 'A4';
            $document = 'tmp/'.uniqid().'.pdf'; 

            $html->process();

            $html->saveAsPDF($document, $pageSize, 'portrait');

            TTransaction::close();

              $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam['key'] = $object->pessoa_id;



            if(empty($param['returnFile']))
            {
                parent::openFile($document);

                new TMessage('info', _t('Document successfully generated'));   
                if (TSession::getValue('sistema') == 'frotas') {
                    TApplication::loadPage('PessoaFormFrotasView', 'onShow', $loadPageParam); 
  
                } elseif (TSession::getValue('sistema') == 'compras') {
                    TApplication::loadPage('PessoaFormView', 'onShow', $loadPageParam); 
                }

                 
            }
            else
            {
                return $document;
            }
        } 
        catch (Exception $e) 
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());

            // undo all pending operations
            TTransaction::rollback();
        }
    }

}

