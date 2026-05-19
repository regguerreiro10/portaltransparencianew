<?php
 
//<fileHeader>

//</fileHeader>

class ItensCotacaoSimpleList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private static $database = 'minierp';
    private static $activeRecord = 'ItensCotacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_ItensCotacaoFormList';
    private $limit = 20;

    //<classProperties>

    //</classProperties>

    private static function validarBloqueioDaCotacao($cotacaoId, $itemIds = null)
    {
        $divergencias = ItensCotacao::getDivergenciasBloqueioPorCotacao($cotacaoId, $itemIds);

        if (!empty($divergencias)) {
            throw new Exception("Não é permitido pré-aprovar/aprovar cotação com item acima da tabela.\n" . implode("\n", $divergencias));
        }
    }

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param)
    {
        parent::__construct();
        // creates the form

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setProperty('style', 'width: 100%;');

          if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }
//$this->form->setProperty('style', 'width: 100%; margin: auto;');

        if (isset($param['tipoacao'])) {
            TSession::setValue('tipoacao', $param['tipoacao']);
        } 
      

        if (TSession::getValue('tipoacao') == 'Aprovar')
        {
            $this->form->setFormTitle("Aprovar Itens da cotacao");
        } elseif (TSession::getValue('tipoacao') == 'Reprovar')
        {
            $this->form->setFormTitle("Reprovar Itens da cotacao");
        } elseif (TSession::getValue('tipoacao') == 'PreAprovar'){
            $this->form->setFormTitle("Pré Aprovar Itens da cotacao");
        }
        // define the form title
        //$this->form->setFormTitle("Aprovar/Reprovar Itens da proposta");
        $this->limit = 0;

        //<onBeginPageCreation>

        //</onBeginPageCreation>

      //  $id = new THidden('id');
        $obs = new TText('obs');
    $TAlert = new TAlert('danger', "Atenção: valores em vermelho e negrito indicam divergência entre o preço da tabela e o valor informado. Verifique antes de Pre-aprovar/Aprovar.");
   //     $id->setEditable(false);
  //      $id->setSize(100);
        $obs->setSize('100%', 70);
          $obs->addValidation("Justificativa", new TRequiredValidator()); 

        //<onBeforeAddFieldsToForm>
 $row0 = $this->form->addFields([$TAlert]);
        $row0->layout = [' col-sm-12'];
        //</onBeforeAddFieldsToForm>
        $row1 = $this->form->addFields([new TLabel("Obs e/ou Justificativa*:", '#FF0000', '14px', null, '100%'),$obs]);
        $row1->layout = [' col-sm-12'];

        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onaction = $this->form->addAction("Voltar", new TAction([$this, 'onAction']), 'fas:arrow-left #000000');
        $this->btn_onaction = $btn_onaction;

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "ID", 'center' , '70px');
        $column_cotacao_id = new TDataGridColumn('cotacao_id', "ID Cotacao", 'left');
        $column_descricao = new TDataGridColumn('produto->nome', "Descrição", 'left');
        $column_qtde = new TDataGridColumn('qtde', "Qtde", 'left');
        $column_valor_transformed = new TDataGridColumn('valor', "Valor Unitário s/ desconto", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_total', "Valor total", 'left');
        $column_valor_liquido_transformed = new TDataGridColumn('', "Valor Liquido c/ desconto", 'left');
        $column_valor_unitario_com_desconto_transformed = new TDataGridColumn('valor', "Valor Unitário c/ desconto", 'left');
        $column_valor_sinapi_transformed = new TDataGridColumn('valor', "Valor Tabela", 'left');
        $column_estado_pedido_id_transformed = new TDataGridColumn('estado_pedido_id', "Estado Cotação", 'left');

           $column_valor_sinapi_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (!TTransaction::getDatabase()) {
                TTransaction::open(self::$database);
                $close = true;
            } else {
                $close = false;
            }

             $taxa = Entidade::where('id','=',TSession::getValue('entidade'))
                              ->load();
            if ($taxa) {
               $taxacontrato = $taxa[0]->taxacontrato/100;
            }
            else {
                  $taxacontrato = 0;
            }

            $value = $object->valor - ($object->valor *  $taxacontrato);

            $produto = Produto::find($object->produto_id);
            $mens = '';

            if ($produto &&  $value > $produto->preco_venda ) {
                $preco_formatado = number_format($produto->preco_venda, 2, ',', '.');
                $mens = "<span style='color:red; font-weight: bold;'>R$ {$preco_formatado}</span>";
            } else {
                if (empty($produto->preco_venda)) {
                    $preco_formatado = '0,00';
                } else {
                    $preco_formatado = number_format($produto->preco_venda, 2, ',', '.');
                }
                //  $preco_formatado = 'R$ '.number_format($produto->preco_venda, 2, ',', '.');
            }

            if ($close) {
                TTransaction::close();
            }

            return $mens ?: $preco_formatado;
        });
        $column_valor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_valor_total_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });
        $column_valor_unitario_com_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $taxa = Entidade::where('id','=',TSession::getValue('entidade'))
                              ->load();
            if ($taxa) {
               $taxacontrato = $taxa[0]->taxacontrato/100;
            }
            else {
                  $taxacontrato = 0;
            }

            $value = $object->valor - ($object->valor *  $taxacontrato);
            if(is_numeric($value))
            {
                $preco_formatado = number_format($value, 2, ',', '.');
                $mens = "<span style='color:blue; font-weight: bold;'>R$ {$preco_formatado}</span>";
                return $mens;
            }
            else
            {
                return $value;
            }
        });

           $column_valor_liquido_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $taxa = Entidade::where('id','=',TSession::getValue('entidade'))
                              ->load();
            if ($taxa) {
               $taxacontrato = $taxa[0]->taxacontrato/100;
            }
            else {
                  $taxacontrato = 0;
            }

            $value = $object->valor_total - ($object->valor_total * $taxacontrato);

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_estado_pedido_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
             TTransaction::open(self::$database);
          $estado_pedido = EstadoPedido::find($object->estado_pedido_id);
            TTransaction::close();
           if (!$estado_pedido) {
                return '';
            }
            return "<span class='label label-default' style='width:240px; background-color:{$estado_pedido->cor}'> {$estado_pedido->nome} <span>";
           

        });

     

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        //<onBeforeColumnsCreation>

        //</onBeforeColumnsCreation>

        $this->builder_datagrid_check_all = new TCheckButton('builder_datagrid_check_all');
        $this->builder_datagrid_check_all->setIndexValue('on');
        $this->builder_datagrid_check_all->onclick = "Builder.checkAll(this)";
        $this->builder_datagrid_check_all->style = 'cursor:pointer';
        $this->builder_datagrid_check_all->setProperty('class', 'filled-in');
        $this->builder_datagrid_check_all->id = 'builder_datagrid_check_all';

        $label = new TLabel('');
        $label->style = 'margin:0';
        $label->class = 'checklist-label';
        $this->builder_datagrid_check_all->after($label);
        $label->for = 'builder_datagrid_check_all';

        $this->builder_datagrid_check = $this->datagrid->addColumn( new TDataGridColumn('builder_datagrid_check', $this->builder_datagrid_check_all, 'center',  '1%') );

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_cotacao_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_qtde);
        $this->datagrid->addColumn($column_valor_transformed);
        $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column_valor_liquido_transformed);
        $this->datagrid->addColumn($column_valor_unitario_com_desconto_transformed);
        $this->datagrid->addColumn($column_valor_sinapi_transformed);
        $this->datagrid->addColumn($column_estado_pedido_id_transformed);

        //<onAfterColumnsCreation>

        //</onAfterColumnsCreation>

        //<onAfterActionsCreation>

        //</onAfterActionsCreation>

        // create the datagrid model
        $this->datagrid->createModel();

        $panel = new TPanelGroup();
       // $panel->setProperty('style', 'width: 80%; margin: auto;'); // <- AQUI aumenta o painel inteiro
        $panel->setProperty('style', 'width: 100%;');

        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        //<onAfterHeaderActionsCreation>

        //</onAfterHeaderActionsCreation>

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        //<onAfterPageCreation>

        //</onAfterPageCreation>

        parent::add($this->form);
        parent::add($panel);
$style = new TStyle('right-panel > .container-part');
        $style->width = '60% !important';
      //  $style = new TStyle('right-panel > .container-part[page-name=ItensPropostasFormList]');
    //    $style->width = '100% !important';   
        $style->show(true);

    }

    public function onSave($param)
    {
        try {
            TTransaction::open('minierp'); // ajuste o banco de dados

             $this->form->validate(); // validate form data
             $object = new Pedido(TSession::getValue('idpedido'));

             $data = $this->form->getData(); // get form data as array
             $object->fromArray( (array) $data); // load the object with data
             $obs = $data->obs ?? '';
             $obs = $data->obs;

             $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_id"] = TSession::getValue('idpedido');

         /*   $data = $this->datagrid_form->getData(); // pega os dados do form
            $checked_items = $data->builder_datagrid_check ?? [];

            if (empty($checked_items)) {
                throw new Exception('Nenhum item selecionado.');
            }*/

            // Agora salva as redes selecionadas novamente
            $itensSelecionadas = TSession::getValue('ItensCotacaoSimpleListbuilder_datagrid_check');
            $contar_itensselecionados = 0;
            if ($itensSelecionadas && is_array($itensSelecionadas)) {
                if (in_array(TSession::getValue('tipoacao'), ['PreAprovar', 'Aprovar'])) {
                    self::validarBloqueioDaCotacao(TSession::getValue('idcotacao'), $itensSelecionadas);
                }

                foreach ($itensSelecionadas as $item_id) {
                    $item = new ItensCotacao($item_id);
                    // Exemplo: alterar status ou marcar como aprovado
                    if (TSession::getValue('tipoacao') == 'Aprovar') {
                        $item->estado_pedido_id = EstadoPedido::APROVADO;
                    } elseif (TSession::getValue('tipoacao') == 'Reprovar') {
                        $item->estado_pedido_id = EstadoPedido::REPROVADO;
                    } elseif (TSession::getValue('tipoacao') == 'PreAprovar') {
                        $item->estado_pedido_id = EstadoPedido::PREAPROVADO;
                    }
                    $item->store();
                    $contar_itensselecionados++;
                }
                if (TSession::getValue('tipoacao') == 'Aprovar') {
                    //reprovar os itens que não foram selecionados
                    $itensNaoSelecionados = ItensCotacao::where('cotacao_id', '=', TSession::getValue('idcotacao'))
                        ->where('id', 'not in', $itensSelecionadas)
                        ->load();
                    if ($itensNaoSelecionados) {
                        foreach ($itensNaoSelecionados as $item) {
                            $item->estado_pedido_id = EstadoPedido::REPROVADO;
                            $item->store();
                        }
                    }
                }
                if (TSession::getValue('tipoacao') == 'PreAprovar') {
                    // Define estado da proposta conforme ação
                    $cotacao = new Cotacao(TSession::getValue('idcotacao'));
                    $cotacao->obs = $obs;
                    $cotacao->estado_pedido_id = EstadoPedido::PREAPROVADO;
                    $cotacao->store();

                    // Atualiza o pedido
                    //  $object->estabelecimento_id = $proposta->pessoa_id;
                    $object->estado_pedido_venda_id = EstadoPedido::PREAPROVADO;
                  //  $object->valor_total_proposta = $proposta->valor_total;
                  //  $object->valor_desconto_proposta = $proposta->valor_desconto;
                  //  $object->valor_liquido_proposta = $proposta->valor_liquido;
                    $object->store();

                    // Históricos
                    $aprovador = Aprovador::where('system_user_id', '=', TSession::getValue('userid'))->load();

                    foreach ($aprovador as $aprovadores) {
                        $histPedido = new PedidoHistorico();
                        $histPedido->pedido_venda_id = $object->id;
                        $histPedido->aprovador_id = $aprovadores->id;
                        $histPedido->estado_pedido_venda_id = $cotacao->estado_pedido_id;
                        $histPedido->data_operacao = date('Y-m-d H:i:s');
                        $histPedido->obs = $obs . ' n º Cotação: ' . TSession::getValue('idcotacao');
                        $histPedido->store();


                        $histCotacao = new CotacaoHistorico();
                        $histCotacao->cotacao_id = $cotacao->id;
                        $histCotacao->aprovador_id =$aprovadores->id;
                        $histCotacao->estado_pedido_id = $cotacao->estado_pedido_id;
                        $histCotacao->data_historico = date('Y-m-d H:i:s');
                        $histCotacao->obs = $obs;
                        $histCotacao->store();
                        break;
                    }
                } elseif (TSession::getValue('tipoacao') == 'Aprovar') {

                    $object = new Pedido(TSession::getValue('idpedido'));

                    // Obter o pedido atual (da cotação que está sendo salva)
                   
                    // Obter o pedido atual (da cotação que está sendo salva)
                    $dotacaoorcamentaria = $object->saldo_departamento_id;

                    if (!$dotacaoorcamentaria) {
                        throw new Exception('Dotação orçamentária não encontrada no pedido atual.');
                    }

                    // Buscar o saldo da dotação orçamentária
                    $sddotacaoorcamentaria = SaldoDepartamento::where('id', '=', $dotacaoorcamentaria)
                        ->load();
                    $saldo_disponivel = 0;            
                    if (!$sddotacaoorcamentaria) {
                        throw new Exception('Dotação orçamentária não encontrada ou não foi cadastrada.');
                    }
                    if ($sddotacaoorcamentaria) {
                        foreach ($sddotacaoorcamentaria as $s) {
                        $saldo_disponivel += (float) $s->saldo_produto;

                        }
                    }

                    // Buscar cotações aprovadas/finalizadas da unidade
                    $cotacoes = Cotacao::where('system_unit_id', '=', TSession::getValue('idunit'))
                        ->where('estado_pedido_id', 'in', [EstadoPedido::APROVADO, EstadoPedido::FINALIZADO])
                        ->load();

                    // Somar os valores das cotações com a mesma dotação orçamentária
                    $total_valor_liquido = 0;

                    if ($cotacoes) {
                        foreach ($cotacoes as $cotacao) {
                            $pedidoRelacionado = new Pedido($cotacao->pedido_id);

                            if ($pedidoRelacionado->saldo_departamento_id == $dotacaoorcamentaria) {
                                $total_valor_liquido += (float) $cotacao->valor_liquido;
                            }
                        }
                    }



                    // Define estado da proposta conforme ação
                    $cotacao = new Cotacao(TSession::getValue('idcotacao'));


                    // Adiciona o valor da cotação atual
                    $total_valor_liquido += (float) $cotacao->valor_liquido;

                    // Verifica contra o saldo disponível
                    // if ($total_valor_liquido > $saldo_disponivel) {
                    //     throw new Exception(
                    //         'O valor do orçamento (R$ ' . number_format($total_valor_liquido, 2, ',', '.') .
                    //         ') ultrapassa o valor disponível da dotação orçamentária (R$ ' . number_format($saldo_disponivel, 2, ',', '.') . ').'
                    //     );
                    // }

                        // Atualiza o pedido
                    $object->valor_total = 0;
                    $object->valor_total_cotacao    = 0;
                    $object->valor_desconto_cotacao = 0;
                    $object->valor_liquido_cotacao  = 0;
                    $object->store();

                    $cotacao->obs = $obs;
                    $cotacao->estado_pedido_id = EstadoPedido::APROVADO;
                    $cotacao->store();

                     $cotacoesaprovadas = Cotacao::where('pedido_id', '=', TSession::getValue('idpedido'))
                                                    ->where('estado_pedido_id', '=', EstadoPedido::APROVADO)
                                                    ->load(); 
                    if ($cotacoesaprovadas) {
                        foreach ($cotacoesaprovadas as $cotacao) {
                            //buscar os itens aprovados da proposta
                            $itensAprovados = ItensCotacao::where('cotacao_id', '=', $cotacao->id)
                                ->where('estado_pedido_id', '=', EstadoPedido::APROVADO)
                                ->load();
                            if ($itensAprovados) {
                                // Adiciona os novos itens ao pedido
                                // Verifica se existem itens para adicionar
                                foreach ($itensAprovados as $item) {
                                if ($item->estado_pedido_id==EstadoPedido::APROVADO) {
                                      $taxa = Entidade::where('id','=',TSession::getValue('entidade'))
                              ->load();
                                    if ($taxa) {
                                       $taxacontrato = $taxa[0]->taxacontrato/100;
                                    }
                                    else {
                                        $taxacontrato = 0;
                                    }
                                    $object = new Pedido(TSession::getValue('idpedido'));
                                    $object->estado_pedido_venda_id = EstadoPedido::APROVADO;
                                    $object->valor_total += $item->valor_total;
                                    $object->valor_total_cotacao += $item->valor_total;
                                    $object->valor_desconto_cotacao += ($item->valor_total * $taxacontrato);
                                    $object->valor_liquido_cotacao += ($item->valor_total -($item->valor_total * $taxacontrato));
                                    $object->store();
                                    }
                                }
                            }
                        }

                    }

                    // Históricos
                    $aprovador = Aprovador::where('system_user_id', '=', TSession::getValue('userid'))->load();

                    foreach ($aprovador as $aprovadores) {
                        $histPedido = new PedidoHistorico();
                        $histPedido->pedido_venda_id = $object->id;
                        $histPedido->aprovador_id = $aprovadores->id;
                        $histPedido->estado_pedido_venda_id = $cotacao->estado_pedido_id;
                        $histPedido->data_operacao = date('Y-m-d H:i:s');
                        $histPedido->obs = $obs . ' n º Cotação: ' . TSession::getValue('idcotacao');
                        $histPedido->store();


                        $histCotacao = new CotacaoHistorico();
                        $histCotacao->cotacao_id = $cotacao->id;
                        $histCotacao->aprovador_id =$aprovadores->id;
                        $histCotacao->estado_pedido_id = $cotacao->estado_pedido_id;
                        $histCotacao->data_historico = date('Y-m-d H:i:s');
                        $histCotacao->obs = $obs;
                        $histCotacao->store();
                        break;
                    }
                    //atualizar os itens do pedido
                    $this->AtualizarItensPedido(TSession::getValue('idpedido'), TSession::getValue('idproposta'));

                    // Criar manutenção com base na proposta aprovada
                    // if (TSession::getValue('tipoacao') == 'Aprovar') {
                    //     $itens = ItensCotacao::where('cotacao_id', '=', $cotacao->id)
                    //     ->where('estado_pedido_id', '=', EstadoPedido::APROVADO)
                    //     ->load();
                    //     foreach ($itens as $item) {
                    //         $add = false;
                    //         $data_garantia = null;

                    //         if ($item->qtdekmgarantia > 0) {
                    //             $km_atual = $object->km;
                    //             $media_km_dia = 50;
                    //             $km_faltante = $item->qtdekmgarantia - $km_atual;

                    //             if ($km_faltante > 0 && $media_km_dia > 0) {
                    //                 $dias_estimados = ceil($km_faltante / $media_km_dia);
                    //                 $data_garantia = date('Y-m-d', strtotime("+$dias_estimados days"));
                    //                 $add = true;
                    //             }
                    //         }

                    //         if ($item->diasdegarantia > 0) {
                    //             $data_garantia = date('Y-m-d', strtotime($item->created_at . " +{$item->diasdegarantia} days"));
                    //             $add = true;
                    //         }

                    //         if ($add) {
                    //             $manutencao = new ManutencaoGarantia();
                    //             $manutencao->itens_propostas_id = $item->id;
                    //             $manutencao->veiculos_id = $object->veiculos_id;
                    //             $manutencao->pedido_frotas_id = $object->id;
                    //             $manutencao->propostas_id = $proposta->id;
                    //             $manutencao->created_at = date('Y-m-d H:i:s');
                    //             $manutencao->tipo = $item->tipo;
                    //             $manutencao->km_manutencao = $item->qtdekmgarantia;
                    //             $manutencao->dias_garantia = $item->diasdegarantia;
                    //             $manutencao->datagarantia = $data_garantia;
                    //             $manutencao->descricao = $item->descricao;
                    //             $manutencao->obs = $obs;
                    //             $manutencao->qtde = $item->qtde;
                    //             $manutencao->ativo = 'S';
                    //             $manutencao->store();
                    //         }
                    //     }
                    // }

                } elseif (TSession::getValue('tipoacao') == 'Reprovar') {
                  if ($contar_itensselecionados == TSession::getValue('contador_itens')) 
                  {    
                        // Define estado da proposta conforme ação
                        $cotacao = new Cotacao(TSession::getValue('idcotacao'));
                        $cotacao->obs = $obs;
                        $cotacao->estado_pedido_id = EstadoPedido::REPROVADO;
                        $cotacao->store();

                     ///   $action = new TAction([$this, 'onConfirmReprovarPedido']);
                      //  $action->setParameter('pedido_id', $proposta->pedido_frotas_id);
                      //  $action->setParameter('proposta_id', $proposta->id);

                       // new TQuestion('Todos os itens foram reprovados. Deseja também reprovar o pedido?', $action);

                        // Históricos
                        $aprovador = Aprovador::where('system_user_id', '=', TSession::getValue('userid'))->load();

                        foreach ($aprovador as $aprovadores) 
                        {
                            $histPedido = new PedidoHistorico();
                            $histPedido->pedido_venda_id = $object->id;
                            $histPedido->aprovador_id = $aprovadores->id;
                            $histPedido->estado_pedido_venda_id = $cotacao->estado_pedido_id;
                            $histPedido->data_operacao = date('Y-m-d H:i:s');
                            $histPedido->obs =$obs . ' n º Cotação: ' . TSession::getValue('idcotacao');
                            $histPedido->store();


                            $histCotacao = new CotacaoHistorico();
                            $histCotacao->cotacao_id = $cotacao->id;
                            $histCotacao->aprovador_id =$aprovadores->id;
                            $histCotacao->estado_pedido_id = $cotacao->estado_pedido_id;
                            $histCotacao->data_historico = date('Y-m-d H:i:s');
                            $histCotacao->obs = $obs;
                            $histCotacao->store();
                            break;
                        }
                    }
                }

            }
          
           
            // Limpa a sessão de itens selecionados
            TTransaction::close();


            if (TSession::getValue('tipoacao') == 'Aprovar') {
               new TMessage('info', 'Itens marcados foram aprovados com sucesso.');
            } elseif (TSession::getValue('tipoacao') == 'Reprovar') {
                new TMessage('info', 'Itens marcados foram reprovados com sucesso.');
            } elseif (TSession::getValue('tipoacao') == 'PreAprovar') {
                new TMessage('info', 'Itens marcados foram pré-aprovados com sucesso.');
                
            }
           

      
            TSession::setValue('tipoacao', null);
            TTransaction::close();

            TApplication::loadPage('PedidoVendaList', 'onReload');
            TApplication::loadPage('CotacaoPendenteList', 'onShow', $loadPageParam);
       //     new TMessage('info', 'Aprovação da proposta realizada com sucesso!');
            TScript::create("Template.closeRightPanel();"); 

        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
/*
//<generated-FormAction-onSave>
    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            //$object = new ItensPropostas(); // create an empty object //</blockLine>

            //$data = $this->form->getData(); // get form data as array
            //$object->fromArray( (array) $data); // load the object with data

            //</beforeStoreAutoCode> //</blockLine>

           // $object->store(); // save the object //</blockLine>

            //</afterStoreAutoCode> //</blockLine>

            // get the generated {PRIMARY_KEY}
            //$data->id = $object->id; //</blockLine>


             // Captura os dados do formulário
            $data = $this->datagrid_form->getData();

            // Recupera os itens marcados
            $checks = $data->builder_datagrid_check ?? [];

            if (empty($checks)) {
                throw new Exception('Nenhum item selecionado.');
            }

            foreach ($checks as $id) {
                // Exemplo de ação com os IDs marcados
                $item = new PedidoFrotas($id); // ou sua ActiveRecord
                $item->status = 'APROVADO';
                $item->store();
            }


            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            //</messageAutoCode> //</blockLine>
//<generatedAutoCode>
            new TMessage('info', "Registro salvo", $messageAction);
//</generatedAutoCode>

            $this->onReload();
            //</endTryAutoCode> //</blockLine>
//<generatedAutoCode>
            TScript::create("Template.closeRightPanel();");
//</generatedAutoCode>

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> //</blockLine>

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
//</generated-FormAction-onSave>
*/
//<generated-FormAction-onAction>
    public function onAction($param = null) 
    {
        try 
        {
            //code here
                TSession::setValue('tipoacao', null);

                    $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_id"] = TSession::getValue('idpedido');

            TApplication::loadPage('PedidoVendaList', 'onReload');
            TApplication::loadPage('CotacaoPendenteList', 'onShow', $loadPageParam);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }//</end>
//</generated-FormAction-onAction>

//<generated-onEdit>
    public function onEdit( $param )//</ini>
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction
                
                $object1 = new Cotacao($key);
                $object = ItensCotacao::where('cotacao_id','=',$key)->load(); // instantiates the Active Record //</blockLine>

                TSession::setValue('idpedido', null);
                TSession::setValue('idpedido', $param['pedido_id']);
                TSession::setValue('idcotacao', null);
                TSession::setValue('idcotacao', $param['key']);
                TSession::setValue('tipoacao', null);
                TSession::setValue('tipoacao', $param['tipoacao']);
                //</beforeSetDataAutoCode> //</blockLine>
                TSession::setValue(__CLASS__.'builder_datagrid_check', null);

                $this->form->setData($object1); // fill the form //</blockLine>
                $this->onReload();
                //</afterSetDataAutoCode> //</blockLine>
                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }//</end>
//</generated-onEdit>

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
           
            // open a transaction with database 'minierp'
            TTransaction::open(self::$database);

            // creates a repository for ItensPropostas
            $repository = new TRepository(self::$activeRecord);
            // creates a criteria
            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
            }
            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            $session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');
            //<onBeforeDatagridLoad>

            //</onBeforeDatagridLoad>
            if (TSession::getValue('idcotacao')) {
                $criteria->add(new TFilter('cotacao_id','=',TSession::getValue('idcotacao')));
            }
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            TSession::setValue('contador_itens', null);
            TSession::setValue('contador_itens', count($objects));
            // if no objects were loaded, show a message

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    $check = new TCheckGroup('builder_datagrid_check');
                    $check->addItems([$object->id => '']);
                    $check->getButtons()[$object->id]->onclick = 'event.stopPropagation()';

                    if(!$this->datagrid_form->getField('builder_datagrid_check[]'))
                    {
                        $this->datagrid_form->setFields([$check]);
                    }

                    $check->setChangeAction(new TAction([$this, 'builderSelectCheck']));
                    $object->builder_datagrid_check = $check;

                    // Recupera o array da sessão ou cria um novo
                    $session_checks = TSession::getValue(__CLASS__ . 'builder_datagrid_check') ?? [];

                    // Se o checkbox estiver na sessão OU o estado já estiver definido, marca como selecionado
                    if (!empty($session_checks[$object->id])) {
                        $object->builder_datagrid_check->setValue([$object->id => $object->id]);

                        // Garante que o item seja adicionado na sessão
                        $session_checks[$object->id] = $object->id;
                        TSession::setValue(__CLASS__ . 'builder_datagrid_check', $session_checks);
                    }
                    //<onBeforeDatagridAddItem>

                    //</onBeforeDatagridAddItem>
                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";
                    //<onAfterDatagridAddItem>

                    //</onAfterDatagridAddItem>
                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            //<onBeforeDatagridTransactionClose>

            //</onBeforeDatagridTransactionClose>

            // close the transaction
            TTransaction::close();
            $this->loaded = true;

            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onClear( $param )
    {
        $this->form->clear(true);

        //<onFormClear>

        //</onFormClear>

    }

    public function onShow($param = null)
    {

        //<onShow>

        //</onShow>
    } 

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    //</hideLine> <addUserFunctionsCode/>

    public static function builderSelectCheck($param)
    {
        $session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');

        $valueOn = null;
        if(!empty($param['_field_data_json']))
        {
            $obj = json_decode($param['_field_data_json']);
            if($obj)
            {
                $valueOn = $obj->valueOn;
            }
        }

        $key = empty($param['key']) ? $valueOn : $param['key'];

        if(empty($param['builder_datagrid_check']) && !empty($session_checks[$key]))
        {
            unset($session_checks[$key]);
        }
        elseif(!empty($param['builder_datagrid_check']) && !in_array($key, $param['builder_datagrid_check']) && !empty($session_checks[$key]))
        {
            unset($session_checks[$key]);
        }
        elseif(!empty($param['builder_datagrid_check']) && in_array($key, $param['builder_datagrid_check']))
        {
            $session_checks[$key] = $key;
        }

        //<onBeforeSetSessionCheckValue>

        //</onBeforeSetSessionCheckValue>

        TSession::setValue(__CLASS__.'builder_datagrid_check', $session_checks);
    }

    public static function manageRow($id, $param = [])
    {
        $list = new self($param);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new ItensPropostas($id);

        $session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');

        $check = new TCheckGroup('builder_datagrid_check');
        $check->addItems([$object->id => '']);
        $check->getButtons()[$object->id]->onclick = 'event.stopPropagation()';

        if(!$list->datagrid_form->getField('builder_datagrid_check[]'))
        {
            $list->datagrid_form->setFields([$check]);
        }

        $check->setChangeAction(new TAction([$list, 'builderSelectCheck']));
        $object->builder_datagrid_check = $check;

        if(!empty($session_checks[$object->id]))
        {
            $object->builder_datagrid_check->setValue([$object->id=>$object->id]);
        }

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    public function AtualizarItensPedido($pedidoId)
    {

        $cotacoesaprovadas = Cotacao::where('pedido_id', '=', $pedidoId)
            ->where('estado_pedido_id', '=', EstadoPedido::APROVADO)
            ->load(); 
        if ($cotacoesaprovadas) {
           foreach ($cotacoesaprovadas as $cotacao) {
           
             $cotacaoId = $cotacao->id;
             //pegar somente os itens da proposta que estão aprovados
            $itensAprovados = ItensCotacao::where('cotacao_id', '=', $cotacaoId)
                ->where('estado_pedido_id', '=', EstadoPedido::APROVADO)
                ->load();
            if ($itensAprovados) {
            // Adiciona os novos itens ao pedido
            // Verifica se existem itens para adicionar
                foreach ($itensAprovados as $item) {
                    // Atualiza o pedido_frotas_id para cada item
                    if ($item->itens_pedido_id) {
                        $itemPedido = new ItensPedido($item->itens_pedido_id);
                    } else {
                        $itemPedido = new ItensPedido();
                    }
                    $itemPedido->produto_id = $item->produto_id;
                    $itemPedido->quantidade = $item->qtde;
                    $itemPedido->valor = $item->valor;
                    $itemPedido->desconto = 0;
                    $itemPedido->valor_total = $item->valor_total;
                    $itemPedido->pedido_venda_id = $pedidoId;
                    $itemPedido->created_at = date('Y-m-d H:i:s');
                    $itemPedido->store();
                }
               /* // Carrega os itens do pedido atual
                $itensPedido = ItensPedidoFrotas::where('pedido_frotas_id', '=', $pedidoId)->load();

                foreach ($itensPedido as $item) {
                    // Verifica se existe algum item aprovado em qualquer proposta aprovada que combine com esse do pedido
                    $itensPropostasAprovadas = ItensPropostas::where('descricao', '=', $item->descricao)
                        ->where('tipo', '=', $item->tipo)
                        ->where('qtde', '=', $item->qtde)
                        ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
                        ->whereIn('propostas_id', function($criteria) use ($pedidoId) {
                            $criteria->add(new TFilter('pedido_frotas_id', '=', $pedidoId));
                            $criteria->add(new TFilter('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO));
                        })
                        ->load();

                    // Se nenhum item aprovado correspondente for encontrado, então remove do pedido
                    if (empty($itensPropostasAprovadas)) {
                        $item->delete();
                    }
                }*/
            }
          }
       }



    }

    public function onConfirmReprovarPedido($param)
    {
        try {
          //  TTransaction::open('minierp');

            $pedido_id = TSession::getValue('idpedido');
            $proposta_id = TSession::getValue('idproposta');

            $proposta = new Propostas($proposta_id);
            $pedido   = new PedidoFrotas($pedido_id);

            $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
            $pedido->valor_total_proposta    = $proposta->valor_total;
            $pedido->valor_desconto_proposta = $proposta->valor_desconto;
            $pedido->valor_liquido_proposta  = $proposta->valor_liquido;
            $pedido->store();

     //       TTransaction::close();

       //     new TMessage('info', 'Pedido reprovado com sucesso.');
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
    


    //<userCustomFunctions>

    //</userCustomFunctions>

}
