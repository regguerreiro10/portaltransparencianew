<?php

class CotacaoPendenteList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Cotacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_CotacaoPendenteList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

    private static function cotacaoTemBloqueio($cotacaoId)
    {
        return !empty(ItensCotacao::getDivergenciasBloqueioPorCotacao($cotacaoId));
    }

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Consulta Cotação Pendente");
        $this->limit = 20;

        $criteria_pessoa_id = new TCriteria();

        $id = new TEntry('id');
        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa_id );


        $pessoa_id->enableSearch();
        $id->setSize(100);
        $pessoa_id->setSize('100%');
/*
        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Pessoa id:", null, '14px', null, '100%'),$pessoa_id]);
        $row1->layout = ['col-sm-6',' col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); */

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "Id", 'left');
        $column_pedido_id = new TDataGridColumn('pedido_id', "Pedido id", 'left');
        $column_pessoa_nome = new TDataGridColumn('pessoa->nome', "Estabelecimento", 'left');
        $column_data_cotacao_transformed = new TDataGridColumn('data_cotacao', "Data cotação", 'left');
        $column__transformed = new TDataGridColumn('valor_liquido', "Vl Liquido", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_liquido', "Vl Liquido Itens", 'left');
        $column_valor_liquido_transformed = new TDataGridColumn('', "Vl Cotação Itens", 'left');
        $column_system_users_name = new TDataGridColumn('system_users->name', "Usuário", 'left');
        $column_bloqueio_transformed = new TDataGridColumn('id', "Bloqueio", 'left');
        $column_estado_pedido_nome_transformed = new TDataGridColumn('estado_pedido->nome', "Estado da Cotação", 'left');
        $column_cidade_id_transformed = new TDataGridColumn('cidade_id', "Cidade", 'left');

        $order_pessoa_id = new TAction(array($this, 'onReload'));
        $order_pessoa_id->setParameter('order', 'pessoa_id');
        $column_pessoa_nome->setAction($order_pessoa_id);

        $column_data_cotacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column__transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

             if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
            //code here
            // Código gerado pelo snippet: "Conexão com banco de dados"
        //    TTransaction::open('minierp');

                        // $objects = ItensPedido::where('pedido_venda_id','=',$object->pedido_id)
                        //                           ->load();

                        // if ($objects) {
                        //     foreach ($objects as $obj) {
                        //        // code...
                        //        $value = $value + ($obj->valor*$obj->quantidade) ;
                        //     }
                        // }

                  //      return 'R$ '.number_format($value, 2, ',', '.');
                        // code

             //           TTransaction::close();
                // -----

        });

        $column_valor_total_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $openTransaction = TTransaction::getDatabase() ? false : true;

            if ($openTransaction) {
                TTransaction::open('minierp');
            }

            try {
                $value = 0;
                $taxacontrato = (float) TSession::getValue('taxacontrato') / 100;

                if (TSession::getValue('aprovacao_por_item')==1) {
                    $objects = ItensCotacao::where('cotacao_id','=',$object->id)
                                          ->where('estado_pedido_id','=',EstadoPedido::APROVADO)
                                          ->load();
                } else {
                    $objects = ItensCotacao::where('cotacao_id','=',$object->id)
                                           ->load();
                }

                if ($objects) {
                    foreach ($objects as $obj) {
                        $desconto = (($obj->valor * $obj->qtde) * $taxacontrato);
                        $value = $value + ($obj->valor * $obj->qtde) - $desconto;
                    }
                }

                if(is_numeric($value))
                {
                    return "R$ " . number_format($value, 2, ",", ".");
                }
                else
                {
                    return $value;
                }
            } finally {
                if ($openTransaction && TTransaction::getDatabase()) {
                    TTransaction::close();
                }
            }
        });
       
        $column_valor_liquido_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $openTransaction = TTransaction::getDatabase() ? false : true;

            if ($openTransaction) {
                TTransaction::open('minierp');
            }

            try {
                $taxacontrato = 0;
                $value = 0;

                if (TSession::getValue('aprovacao_por_item')==1) {
                    $objects = ItensCotacao::where('cotacao_id','=',$object->id)
                                          ->where('estado_pedido_id','=',EstadoPedido::APROVADO)
                                          ->load();
                } else {
                    $objects = ItensCotacao::where('cotacao_id','=',$object->id)
                                           ->load();
                }

                if ($objects) {
                    foreach ($objects as $obj) {
                        $desconto = (($obj->valor * $obj->qtde) * $taxacontrato);
                        $value = $value + ($obj->valor * $obj->qtde) - $desconto;
                    }
                }

                if(is_numeric($value))
                {
                    return "R$ " . number_format($value, 2, ",", ".");
                }
                else
                {
                    return $value;
                }
            } finally {
                if ($openTransaction && TTransaction::getDatabase()) {
                    TTransaction::close();
                }
            }
        });
        $column_estado_pedido_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $temnotafiscal = false;

            if (in_array((int) $object->estado_pedido_id, [
                (int) EstadoPedido::FINALIZADO,
                (int) EstadoPedido::APROVADO,
                (int) EstadoPedido::PGTOAPROVADO,
                (int) EstadoPedido::ENTREGUE
            ], true)) {
                $openTransaction = TTransaction::getDatabase() ? false : true;

                if ($openTransaction) {
                    TTransaction::open('minierp');
                }

                try {
                    $doccot = DocumentosCotacao::where('cotacao_id','=',$object->id)
                                               ->load();
                    if ($doccot){
                        $temnotafiscal = true;
                    }
                } finally {
                    if ($openTransaction && TTransaction::getDatabase()) {
                        TTransaction::close();
                    }
                }
            }
           
            $revisao = '';
            if (TSession::getValue('testar_revisao')==1) {
                //entrou em revisão
                if ($object->estado_pedido1_id !== null) {
                    $estadorevisao = new EstadoPedido($object->estado_pedido1_id);
                    $revisao = "<span style='font-size: 10px; font-style: italic; color: #eee;'>({$estadorevisao->nome})</span>";
                }
            }

            if ($temnotafiscal) {
                $anexo = $object->estado_pedido->nome . " <i class='fa fa-paperclip' aria-hidden='true'></i>";
            } else {
                $anexo = $object->estado_pedido->nome;
            }

            return "<span class='label label-default' style='width:250px; background-color:{$object->estado_pedido->cor}; display:inline-block;'> {$anexo} {$revisao} </span>";           
        });

        $column_cidade_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $openTransaction = TTransaction::getDatabase() ? false : true;

            if ($openTransaction) {
                TTransaction::open('minierp');
            }

            try {
                $cidade = new Cidade($object->cidade_id);
                if ($cidade) {
                    $estado = new Estado($cidade->estado_id);
                    return "{$cidade->nome} - {$estado->sigla}";

                } else {
                    return "Não informado!!!";

                }
            } finally {
                if ($openTransaction && TTransaction::getDatabase()) {
                    TTransaction::close();
                }
            }

        });        

        $column_bloqueio_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (!ItensCotacao::isBloqueioValorTempariaAtivo()) {
                return '';
            }

            $divergencias = ItensCotacao::getDivergenciasBloqueioPorCotacao($object->id);

            if (empty($divergencias)) {
                return '';
            }

            $tooltip = htmlspecialchars(implode(' | ', $divergencias), ENT_QUOTES, 'UTF-8');

            return "<span class='label label-danger' title='{$tooltip}' style='display:inline-block; background-color:#d9534f; cursor:help;'>Bloqueio</span>";
        });

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_pedido_id);
        $this->datagrid->addColumn($column_pessoa_nome);
        $this->datagrid->addColumn($column_data_cotacao_transformed);
        $this->datagrid->addColumn($column__transformed);
        $this->datagrid->addColumn($column_valor_liquido_transformed);
        $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column_system_users_name);
        $this->datagrid->addColumn($column_bloqueio_transformed);
        $this->datagrid->addColumn($column_estado_pedido_nome_transformed);
        $this->datagrid->addColumn($column_cidade_id_transformed);
        
        if (TSession::getValue('aprovacao_por_item')==2) {
            $action1 = new TDataGridAction(['TStatusPedidoCotacao', 'onShowModal'], [
                'id'       => '{id}',
                'tipoacao' => 'Aprovar' // sem chaves se for valor fixo
            ]);     
             $action2 = new TDataGridAction(['TStatusPedidoCotacao', 'onShowModal'], [
                'id'       => '{id}',
                'tipoacao' => 'Reprovar' // sem chaves se for valor fixo
            ]);     
             $action3 = new TDataGridAction(['TStatusPedidoCotacao', 'onShowModal'], [
                'id'       => '{id}',
                'tipoacao' => 'PreAprovar' // sem chaves se for valor fixo
            ]);            
        } else {
            // Ações para aprovar, reprovar e pré-aprovar

               $action1 = new TDataGridAction(['ItensCotacaoSimpleList', 'onEdit'], [
                'id'       => '{id}',
                'tipoacao' => 'Aprovar' // sem chaves se for valor fixo
            ]);     
             $action2 = new TDataGridAction(['ItensCotacaoSimpleList', 'onEdit'], [
                'id'       => '{id}',
                'tipoacao' => 'Reprovar' // sem chaves se for valor fixo
            ]);     
               $action3 = new TDataGridAction(['ItensCotacaoSimpleList', 'onEdit'], [
                'id'       => '{id}',
                'tipoacao' => 'PreAprovar' // sem chaves se for valor fixo
            ]);   
                
            


        }
         
     //   $action5 = new TDataGridAction(['PedidoFrotasFormView', 'onShow'],     ['id' => '{id}']); // EDITAR (APROVAR; PRE-APROVAR; E REPROVAR)
    //    $action6 = new TDataGridAction(['PedidoFrotasFormView', 'onShow'],     ['id' => '{id}']); // GERAR FINANCEIRO

            $action1->setLabel('Aprovar');
            $action1->setImage('fas:thumbs-up #9C27B0');
            $action1->setDisplayCondition('CotacaoPendenteList::onExibirAprovada');
            $action1->setParameter('pedido_id', '{pedido_id}');

            

 
            $action2->setLabel('Reprovar');
            $action2->setImage('fas:thumbs-down #F44336');
            $action2->setDisplayCondition('CotacaoPendenteList::onExibirReprovada');
            $action2->setParameter('pedido_id', '{pedido_id}');

             $action3->setLabel('Pré-Aprovar');
            $action3->setImage('far:thumbs-up #9C27B0');
            $action3->setDisplayCondition('CotacaoPendenteList::onExibirPreAprovada');
            $action3->setParameter('pedido_id', '{pedido_id}');
            
        $action4 = new TDataGridAction(['CotacaoPendenteList', 'onImprimeCotacao'],     ['id' => '{id}']);
        $action4->setLabel('Orçamento');
        $action4->setImage('far:file-pdf #000000');
        $action4->setParameter('pedido_id', '{pedido_id}');

        $action11 = new TDataGridAction(['DocumentosCotacaoPedidoList', 'onSetProject'],   ['id' => '{id}']);
        $action11->setLabel('Anexos');
        $action11->setImage('fas:paperclip #795548');
        $action11->setDisplayCondition('CotacaoPendenteList::onExibirAnexos');

        $action_group = new TDataGridActionGroup('Clique Ações ', 'fa:th red');

        $action_group->addAction($action3);
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action4);
        $action_group->addAction($action11);

/*
        $action_onAprovar = new TDataGridAction(array('CotacaoPendenteList', 'onAprovar'));
        $action_onAprovar->setUseButton(false);
        $action_onAprovar->setButtonClass('btn btn-default btn-sm');
        $action_onAprovar->setLabel("Aprovar");
        $action_onAprovar->setImage('fas:thumbs-up #9C27B0');
        $action_onAprovar->setField(self::$primaryKey);
        $action_onAprovar->setDisplayCondition('CotacaoPendenteList::onExibirAprovada');

        $this->datagrid->addAction($action_onAprovar);

        $action_onReprovar = new TDataGridAction(array('CotacaoPendenteList', 'onReprovar'));
        $action_onReprovar->setUseButton(false);
        $action_onReprovar->setButtonClass('btn btn-default btn-sm');
        $action_onReprovar->setLabel("Reprovar");
        $action_onReprovar->setImage('fas:thumbs-down #F44336');
        $action_onReprovar->setField(self::$primaryKey);
        $action_onReprovar->setDisplayCondition('CotacaoPendenteList::onExibirReprovada');

        $this->datagrid->addAction($action_onReprovar);

        $action_onGenerate = new TDataGridAction([$this, 'onImprimeCotacao'],   ['id' => '{id}']);
        $action_onGenerate->setUseButton(false);
        $action_onGenerate->setButtonClass('btn btn-default btn-sm');
        $action_onGenerate->setLabel("Documento");
        $action_onGenerate->setImage('far:file-pdf #000000');
        $action_onGenerate->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onGenerate);*/

         $this->datagrid->addActionGroup($action_group);


        // create the datagrid model
        $this->applyDatagridProperties();

        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $panel->getBody()->insert(0, $headerActions);
/*
        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['CotacaoPendenteList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['CotacaoPendenteList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['CotacaoPendenteList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['CotacaoPendenteList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_right_actions->add($dropdown_button_exportar);*/

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
      //      $container->add(TBreadCrumb::create(["Compras","CotacaoPendenteList"]));
        }
       // $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

//     public function onAprovar($param = null) 
//     {

//         if (isset($param['confirmAprovarEnviarEmail']) && $param['confirmAprovarEnviarEmail']) {
//             try {

//                 TTransaction::open(self::$database);
//                 $cotacao = new Cotacao($param['id']);
//                 $cotacao->estado_pedido_id = EstadoPedido::APROVADO;
//                 $cotacao->store();


//                 $pedido = new Pedido($cotacao->pedido_id);
//                 $pedido->cliente_id = $cotacao->pessoa_id;
//                 $pedido->estado_pedido_venda_id = EstadoPedido::APROVADO;
//                 $pedido->valor_total_cotacao = $cotacao->valor_total;
//                 $pedido->valor_desconto_cotacao = $cotacao->valor_desconto;
//                 $pedido->valor_liquido_cotacao = $cotacao->valor_liquido;
//                 $pedido->cidade_id = $cotacao->cidade_id;
//                 $pedido->store();

//                 $unit = new SystemUnit(TSession::getValue('idunit'));
//                 if ($unit->utilizasinapi=='S') {
//                     $itens_cotacao = ItensCotacao::where('cotacao_id', '=', $cotacao->id)
//                         ->where('estado_pedido_id', '=', EstadoPedido::AGUARDANDO)
//                         ->load();
//                     if ($itens_cotacao) {
//                         foreach ($itens_cotacao as $item) {
//                             $idproduto1 = $item->produto_id;
//                             $produto = new Produto($idproduto1);
//                             if ($produto->preco_venda > $item->valor)) {
//                                 new TMessage('info', "Valor do produto {$produto->nome} não confere com o valor da tabela Sinapi. Valor do produto: {$produto->preco_venda} Valor informado: {$item->valor}");
//                             }
//                         }
//                     }

//                 $this->registrarHistoricoPedido($pedido);

//                 $this->registrarHistoricoCotacao($cotacao);

//                 TTransaction::close();
//                 TToast::show('success', "Aprovação da proposta realizada com sucesso!!!", 'topRight', 'far:check-circle');
//                 TApplication::loadPage('PedidoVendaList', 'onSetProject');

//             } catch (Exception $e) {
//                 new TMessage('error', $e->getMessage());
//                 TTransaction::rollback();
//             }
//         } else {
//             // Confirmação antes de gerar a cotação
//             $action = new TAction(array($this, 'onAprovar'));
//             $action->setParameters($param);
//             $action->setParameter('confirmAprovarEnviarEmail', true);

//             new TQuestion('Tem certeza que deseja aprovar esta proposta?', $action);
//         }

      
//     }
//     public function onPreAprovar($param = null) 
//     {

//         if (isset($param['confirmPreAprovarEnviarEmail']) && $param['confirmPreAprovarEnviarEmail']) {
//             try {

//                 TTransaction::open(self::$database);
//                 $cotacao = new Cotacao($param['id']);
//                 $cotacao->estado_pedido_id = EstadoPedido::PREAPROVADO;
//                 $cotacao->store();

//           //     var_dump($param);

//                 $pedido = new Pedido($cotacao->pedido_id);
//                 $pedido->cliente_id = $cotacao->pessoa_id;
//                 $pedido->estado_pedido_venda_id = EstadoPedido::PREAPROVADO;
//                 $pedido->valor_total_cotacao = $cotacao->valor_total;
//                 $pedido->valor_desconto_cotacao = $cotacao->valor_desconto;
//                 $pedido->valor_liquido_cotacao = $cotacao->valor_liquido;
//                 $pedido->cidade_id = $cotacao->cidade_id;
//                 $pedido->store();

//                 $this->registrarHistoricoPrePedido($pedido);

//                 $this->registrarHistoricoPreCotacao($cotacao);

//                 TTransaction::close();
//                 TToast::show('success', "Pré-Aprovação da cotacao realizada com sucesso!!!", 'topRight', 'far:check-circle');
//                 TApplication::loadPage('PedidoVendaList', 'onSetProject');

//             } catch (Exception $e) {
//                 new TMessage('error', $e->getMessage());
//                 TTransaction::rollback();
//             }
//         } else {
//             // Confirmação antes de gerar a cotação
//             $action = new TAction(array($this, 'onAprovar'));
//             $action->setParameters($param);
//             $action->setParameter('confirmPreAprovarEnviarEmail', true);

//             new TQuestion('Tem certeza que deseja pre-aprovar esta cotacao?', $action);
//         }

        

//        /* try 
//         {
//             //code here
// */
//             //</autoCode>
//   /*      }
//         catch (Exception $e) 
//         {
//             new TMessage('error', $e->getMessage());    
//         }*/
//     }
    public static function onExibirAprovada($object)
    {
        try 
        {
            if (self::cotacaoTemBloqueio($object->id)) {
                return false;
            }

            $estado  = $object->estado_pedido_id;
            $estado1 = $object->estado_pedido1_id;
            $estadosPermitidos = Aprovador::getEstadosDisponiveis();

            if (TSession::getValue('testar_revisao')==1) {            
                // Impede exibição se estiver em revisão
                if ($estado1 == EstadoPedido::REVISAO) {
                    return false;
                } 
            }

            // Exibe botão apenas se o estado atual for AGUARDANDO ou PREAPROVADO
            // E se o usuário puder aprovar (tem o estado APROVADO nos permitidos)
            if (in_array($estado, [EstadoPedido::AGUARDANDO, EstadoPedido::PREAPROVADO]) &&
                in_array(EstadoPedido::APROVADO, $estadosPermitidos))
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    // public function onReprovar($param = null) 
    // {
    //     try 
    //     {
    //         //code here
    //         if (isset($param['confirmReprovarEnviarEmail']) && $param['confirmReprovarEnviarEmail']) {
    //         try {

    //             TTransaction::open(self::$database);

    //             $cotacao = new Cotacao($param['id']);
    //             $cotacao->estado_pedido_id = EstadoPedido::REPROVADO;
    //             $cotacao->store();

    //             $reprovarpedido=true;
    //             $cotacao1 = Cotacao::where('pedido_id','=',$cotacao->pedido_id)
    //                                ->load();
    //             foreach ($cotacao1 as $cot) {
    //                  if (!in_array($cot->estado_pedido_id, [EstadoPedido::REPROVADO]) ){
    //                      $reprovarpedido=false;
    //                 } 
    //             }

    //             if ($reprovarpedido)
    //             {
    //                 $pedido = new Pedido($cotacao->pedido_id);
    //                 $pedido->estado_pedido_venda_id = EstadoPedido::REPROVADO;
    //                 $pedido->store();

    //                 $this->registrarHistoricoPedidoReprovar($pedido);
    //             }

    //       //     var_dump($param);

    //         //    $pedido = new Pedido($cotacao->pedido_id);
    //          //   $pedido->estado_pedido_venda_id = EstadoPedido::REPROVADO;
    //         //    $pedido->store();

    //            // $this->registrarHistoricoPedidoReprovar($pedido);

    //             $this->registrarHistoricoCotacaoReprovar($cotacao);

    //             TTransaction::close();
    //             TToast::show('success', "Proposta reprovada com sucesso!!!", 'topRight', 'far:check-circle');
    //             TApplication::loadPage('PedidoVendaList', 'onSetProject');
    //         } catch (Exception $e) {
    //             new TMessage('error', $e->getMessage());
    //             TTransaction::rollback();
    //         }
    //     } else {
    //         // Confirmação antes de gerar a cotação
    //         $action = new TAction(array($this, 'onReprovar'));
    //         $action->setParameters($param);
    //         $action->setParameter('confirmReprovarEnviarEmail', true);

    //         new TQuestion('Tem certeza que deseja reprovar esta proposta?', $action);
    //     }
    //         //</autoCode>
    //     }
    //     catch (Exception $e) 
    //     {
    //         new TMessage('error', $e->getMessage());    
    //     }
    // }
     public static function onExibirReprovada($object)
    {
        try 
        {
            $estado  = $object->estado_pedido_id;
            $estado1 = $object->estado_pedido1_id;
            $estadosPermitidos = Aprovador::getEstadosDisponiveis();

            if (TSession::getValue('testar_revisao')==1) {            
                // Impede exibição se estiver em revisão
                if ($estado1 == EstadoPedido::REVISAO) {
                    return false;
                } 
            }


            // Exibe o botão apenas se:
            // - O estado atual for AGUARDANDO ou PREAPROVADO
            // - E o usuário tiver permissão para REPROVAR
            if (in_array($estado, [EstadoPedido::AGUARDANDO, EstadoPedido::PREAPROVADO]) &&
                in_array(EstadoPedido::REPROVADO, $estadosPermitidos))
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'minierp'
            TTransaction::open(self::$database);

            // creates a repository for Cotacao
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'valor_liquido';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'asc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

          //  $criteria->add(new TFilter('pedido_id', '=', TSession::getValue('idpedidocp'))); 

              if (!empty($param['pedido_id'])) {
                $criteria->add(new TFilter('pedido_id','=',$param['pedido_id']));
            }
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

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

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
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

    public static function manageRow($id)
    {
        $list = new self([]);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Cotacao($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    function onSetProject($param = null) {
        TSession::setValue('idpedidocp',NULL);
       TSession::setValue('idpedidocp',$param['id']);  
       $this->onReload();
    }
    private function registrarHistoricoPedido($pedido)
    {
        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::APROVADO; 
        $hist->aprovador_id = TSession::getValue('iduser');
        $hist->store();
    }
      private function registrarHistoricoPrePedido($pedido)
    {
        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::PREAPROVADO; 
        $hist->aprovador_id = TSession::getValue('iduser');
        $hist->store();
    }

    private function registrarHistoricoCotacao($cotacao)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::APROVADO; 
        $histcotacao->aprovador_id = TSession::getValue('iduser');
        $histcotacao->store();
    }
       private function registrarHistoricoPreCotacao($cotacao)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::PREAPROVADO; 
        $histcotacao->aprovador_id = TSession::getValue('iduser');
        $histcotacao->store();
    }
     private function registrarHistoricoPedidoReprovar($pedido)
    {
        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::REPROVADO; 
        $hist->aprovador_id = TSession::getValue('iduser');
        $hist->store();
    }

    private function registrarHistoricoCotacaoReprovar($cotacao)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::REPROVADO; 
        $histcotacao->aprovador_id = TSession::getValue('iduser');
        $histcotacao->store();
    }

    public function onImprimeCotacao($param = null) 
    {
      try 
         {
            include 'app/control/compras/qrcode.php';
    //        $data = $this->form->getData();
             //code here
             TTransaction::open('minierp');

             $conn = TConnection::open('minierp');

             //code here
             $pdf = new FPDF("L","pt","A4");

             

             $objects = new Cotacao($param['id']);
             
             $pedido = new Pedido($objects->pedido_id);
             
             $linha=0;   
             $pag=1;
             $alturalinha=255;
             $limitePagina = 740;
             $unidade='';
             $qt = 0;
             $vl = 0;
             $vlt = 0;
             $qtitens=0;

             $itenscotacao = ItensCotacao::where('cotacao_id','=',$param['id'])
                                        ->load();
             if ($itenscotacao) {
                 $gruposItens = [
                    'produtos' => ['titulo' => 'PRODUTOS', 'itens' => []],
                    'servicos' => ['titulo' => 'SERVICOS', 'itens' => []],
                 ];

                 foreach ($itenscotacao as $itemCotacao)
                 {
                    $produtoItem = new Produto($itemCotacao->produto_id);
                    $tipoProdutoId = (int) ($produtoItem->tipo_produto_id ?? 0);
                    $chaveGrupo = ($tipoProdutoId === 2) ? 'servicos' : 'produtos';

                    $gruposItens[$chaveGrupo]['itens'][] = [
                        'item' => $itemCotacao,
                        'produto' => $produtoItem
                    ];
                 }

                 foreach (['produtos', 'servicos'] as $grupoKey) {
                     if (empty($gruposItens[$grupoKey]['itens'])) {
                        continue;
                     }

                     $alturalinha = $this->garantirEspacoCotacao(
                        $pdf,
                        $alturalinha,
                        18,
                        $linha,
                        $pag,
                        $unidade,
                        $pedido,
                        $objects,
                        'ITENS - '.$gruposItens[$grupoKey]['titulo'],
                        $limitePagina
                     );

                     $qtGrupo = 0;
                     $vlGrupo = 0;
                     $vltGrupo = 0;
                     $qtitensGrupo = 0;

                     foreach ($gruposItens[$grupoKey]['itens'] as $registro) {
                     $itens = $registro['item'];
                     $produto = $registro['produto'];
                     //detalhes
                     $pdf->setFont('arial','',7);
                     $nome_produto = utf8_decode($produto->nome);

                    // Define largura da célula do nome e número máximo de linhas
                    $largura_nome = 340;
                    $altura_linha = 8;
                    $max_linhas = 3;

                    // Calcula altura necessária com base no número de linhas que o nome ocuparia
                    $linhas_necessarias = ceil($pdf->GetStringWidth($nome_produto) / $largura_nome);
                    $linhas_exibidas = min($linhas_necessarias, $max_linhas);
                    $altura_total = $linhas_exibidas * $altura_linha;

                    // Se ultrapassar o limite de linhas, corta e adiciona reticências
                    if ($linhas_necessarias > $max_linhas) {
                        // Estimativa segura para cortar
                        $nome_cortado = mb_strimwidth($nome_produto, 0, 200, '...');
                    } else {
                        $nome_cortado = $nome_produto;
                    }

                    // Nome do produto com quebra automática controlada
                    $alturaItem = max(24, $altura_total + 8);
                    $alturalinha = $this->garantirEspacoCotacao(
                        $pdf,
                        $alturalinha,
                        $alturaItem,
                        $linha,
                        $pag,
                        $unidade,
                        $pedido,
                        $objects,
                        'ITENS - '.$gruposItens[$grupoKey]['titulo'].' (continua)',
                        $limitePagina
                    );

                    $pdf->SetXY(22,$alturalinha);
                    $pdf->Cell(70,5,utf8_decode($produto->id),0,1,'L');
                    $pdf->SetXY(47, $alturalinha-1);
                    $pdf->MultiCell($largura_nome, $altura_linha, $nome_cortado, 0, 'L', false);

                    // Mantém os demais campos alinhados na mesma linha base
                     $pdf->SetFont('arial', '', 7);
                     $pdf->SetXY(350, $alturalinha);
                     $pdf->Cell(70, 5, $produto->unidade_medida->nome ?? '', 0, 1, 'C');
                     $pdf->SetXY(380, $alturalinha);
                     $pdf->Cell(70, 5, $itens->qtde, 0, 1, 'C');
                     $pdf->SetXY(420, $alturalinha);
                    $pdf->Cell(70, 5, 'R$ ' . number_format($itens->valor, 2), 0, 1, 'R');
                    $pdf->SetXY(490, $alturalinha);
                    $pdf->Cell(70, 5, 'R$ ' . number_format($itens->valor_total, 2), 0, 1, 'R');

                     
                     $alturalinha += $alturaItem; 
                     $linha +=1;

                     $pdf->ln(1); 
                  //  if ($itens->estado_pedido_id == EstadoPedido::APROVADO || is_null($itens->estado_pedido_id)) {
                            $qtitens++;
                            $qt  += $itens->qtde;
                            $vl  += $itens->valor;
                            $vlt += $itens->valor_total;
                            $qtitensGrupo++;
                            $qtGrupo += $itens->qtde;
                            $vlGrupo += $itens->valor;
                            $vltGrupo += $itens->valor_total;
                 //   }
                 }
                     $alturalinha = $this->garantirEspacoCotacao(
                        $pdf,
                        $alturalinha,
                        24,
                        $linha,
                        $pag,
                        $unidade,
                        $pedido,
                        $objects,
                        'ITENS - '.$gruposItens[$grupoKey]['titulo'].' (continua)',
                        $limitePagina
                     );
                     $alturalinha += 4;
                     $pdf->SetFont('arial','B',8);
                     $pdf->SetFillColor(245,247,248);
                     $pdf->Rect(26, $alturalinha-2, 542, 14, 'F');
                     $pdf->SetXY(30,$alturalinha+1);
                     $pdf->Cell(180,5,utf8_decode('Subtotal '.$gruposItens[$grupoKey]['titulo'].' ('.$qtitensGrupo.' itens)'),0,1,'L');
                     $pdf->SetXY(360,$alturalinha+1);
                     $pdf->Cell(70,5,$qtGrupo,0,1,'C');
                     $pdf->SetXY(420,$alturalinha+1);
                     $pdf->Cell(70,5,'R$ '.number_format($vlGrupo, 2),0,1,'R');
                     $pdf->SetXY(490,$alturalinha+1);
                     $pdf->Cell(70,5,'R$ '.number_format($vltGrupo, 2),0,1,'R');
                     $alturalinha += 20;
                     $linha += 1;
                 }
                 $alturalinha = $this->garantirEspacoCotacao(
                    $pdf,
                    $alturalinha,
                    95,
                    $linha,
                    $pag,
                    $unidade,
                    $pedido,
                    $objects,
                    null,
                    $limitePagina
                 );
                 $alturalinha+=15;
                 //rodape
              //   $pdf->SetXY(25,$alturalinha);
              //   $pdf->Cell(0,25,"","B",1,'C');
            //     $alturalinha+=15;
                 $pdf->SetFont('arial','B',10); 
                 $pdf->SetXY(25,$alturalinha);
                 $pdf->SetFillColor(235,239,240);
                 $pdf->Rect(26, $alturalinha-5, 542, 15, 'F');
                 $pdf->Cell(70,5,utf8_decode('Total Geral '.$qtitens.' Itens'),0,1,'L');

                 $pdf->SetXY(360,$alturalinha);
                 $pdf->Cell(70,5,$qt,0,1,'C');
                 $pdf->SetXY(420,$alturalinha);
                 $pdf->Cell(70,5,'R$ '.number_format($vl, 2),0,1,'R');
                 $pdf->SetXY(490,$alturalinha);
                 $pdf->Cell(70,5,'R$ '.number_format($vlt, 2),0,1,'R');

                 $pdf->Cell(0,15,"","B",1,'C');

                 $pdf->SetFont('arial','I',10); 
                 $alturalinha+=26;
                 
                 $pdf->SetXY(82,$alturalinha);
                 $pdf->Cell(70,5,'Valor Bruto: '.'R$ '.number_format($vlt, 2),0,1,'R');
                                     
                 $pdf->SetXY(320,$alturalinha);
                
                 $taxas = ((TSession::getValue('taxacontrato'))) ;
                 $desconto = $vlt * ($taxas / 100);
                 $pdf->Cell(70,5,'Valor Desconto: ('.number_format($taxas, 2).'%)'.' R$ '.number_format($desconto, 2),0,1,'R');
                 
                 $pdf->SetXY(490,$alturalinha);
                 $liquido = $vlt - $desconto;
                 $pdf->Cell(70,5,'Valor Liquido: '.'R$ '.number_format($liquido, 2),0,1,'R');
                 
                    $alturalinha+=25;
                    $alturalinha = $this->garantirEspacoCotacao(
                        $pdf,
                        $alturalinha,
                        120,
                        $linha,
                        $pag,
                        $unidade,
                        $pedido,
                        $objects,
                        null,
                        $limitePagina
                    );
                    $pdf->Cell(0,15,"","B",1,'C');
                    $pdf->SetXY(45,$alturalinha);
                    $pdf->Cell(70,5,utf8_decode('Legenda: P: PRE-APROVADO; A: APROVADO; R: REPROVADO '),0,1,'L');
                 //qrcode
                 $text = $pedido->id.".png";
                 $file = "app/documents/{$text}";
                 $options = array(
                        'w' => 500,
                        'h' => 500
                 );

                 $generator = new QRCode($pedido->id, $options);
                 $image = $generator->render_image();
                 imagepng($image, $file);
                 $pdf->SetXY(255,750);
                 $pdf->Cell(70,5,'Agora falta pouco, escaneie o QR Code para efetuar a entrega dos seus produtos.',0,1,'C');

                 $pdf->Image('app/documents/'.$pedido->id.'.png', 250, 760, 80);

             }

              $nome = 'documentocotacao.pdf';

             // stores the file
             if (!file_exists("app/output/{$nome}") OR is_writable("app/output/{$nome}"))
             {
                $pdf->Output("app/output/{$nome}","F");
             }
             else
             {
                throw new Exception(_t('Permission denied') . ': ' . "app/output/{$nome}");
             }

             // open the report file
             parent::openFile("app/output/{$nome}");
             // shows the success message
      //       new TMessage('info', 'Pedidos gerado com sucesso. Por favor, habilite popups no navegador.');

                $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_id"] = $objects->pedido_id;

      


           // TApplication::loadPage('PedidoVendaList', 'onReload');
            TApplication::loadPage('CotacaoPendenteList', 'onShow', $loadPageParam);

             // fill the form with the active record data
         //    $this->form->setData($data);
             TTransaction::close();

             //</autoCode>
         }
         catch (Exception $e) 
         {
             new TMessage('error', $e->getMessage());    
         }

            //</autoCode>
       /* }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }
    
    private function abrirPaginaCotacao($pdf, &$linha, &$pag, $unidade, $pedido, $objects, $tituloGrupo = null)
    {
        $this->cabecalhoDCot($pdf, $linha, $pag, $unidade, $pedido->id, $pedido->dt_pedido, $objects->id);
        $pag++;
        $linha = 12;
        $alturalinha = 255;

        if ($tituloGrupo) {
            $pdf->SetFont('arial','B',9);
            $pdf->SetFillColor(220, 226, 230);
            $pdf->Rect(26, $alturalinha-4, 542, 14, 'F');
            $pdf->SetXY(30,$alturalinha);
            $pdf->Cell(200,5,utf8_decode($tituloGrupo),0,1,'L');
            $alturalinha += 18;
            $linha += 1;
        }

        return $alturalinha;
    }

    private function garantirEspacoCotacao($pdf, $alturalinha, $alturaNecessaria, &$linha, &$pag, $unidade, $pedido, $objects, $tituloGrupo = null, $limitePagina = 740)
    {
        if ($pdf->PageNo() == 0 || ($alturalinha + $alturaNecessaria) > $limitePagina) {
            return $this->abrirPaginaCotacao($pdf, $linha, $pag, $unidade, $pedido, $objects, $tituloGrupo);
        }

        return $alturalinha;
    }
    
   private function cabecalhoDCot($pdf, $linha,$pag, $unidade, $id, $datacotacao, $idcot)
   {
       $label = '';
       $datacotacao = new DateTime($datacotacao);
       $datacotacao = $datacotacao->format('d/m/Y');

       $cot = new Cotacao($idcot);

       $ped = new Pedido($id);
       $dep = new DepartamentoUnit($ped->departamento_unit_id);
       $unit = new SystemUnit($dep->system_unit_id);

       $pessoa = new Pessoa($cot->pessoa_id);                           
       $cnpj = $pessoa->documento;
       $nome = $pessoa->nome;

       $pessoa_endereco = PessoaEndereco::where('pessoa_id','=',$ped->cliente_id)
                                        ->where('principal','=','T')
                                        ->load();
       $nomecidade = '';
       if ($pessoa_endereco) {
           foreach ($pessoa_endereco as $pe) {
           $cidade = new Cidade($pe->cidade_id);
           $estado = new Estado($cidade->estado_id);
           $nomecidade = $cidade->nome.'/'.$estado->sigla;
           }
       }

       $historicopedido = PedidoHistorico::where('pedido_venda_id','=',$ped->id)
                                         ->where('estado_pedido_venda_id','=',EstadoPedido::APROVADO)
                                         ->orderBy('data_operacao','desc')
                                         ->first();
       $usuario='';
       if ($historicopedido) {
            $aprovadorx = new Aprovador($historicopedido->aprovador_id);
                    $usuarioaprovado = new SystemUsers($aprovadorx->system_user_id);
                    $usuario = $usuarioaprovado->name;
       } else {$usuario = '';}

       $pdf->AddPage();
       $pdf->SetFont('arial','B',10);

       $pdf->Image('app/images/logo.png', 25, 02, 80);
       $pdf->SetXY(300,40);
       $pdf->Cell(70,5,utf8_decode('Pedido de Compra:'),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(350,40);
       $pdf->Cell(70,5,'#'.$id,0,1,'R');

       $pdf->SetFont('arial','B',10);
       $pdf->SetXY(445,40);
       $pdf->Cell(70,5,utf8_decode('Cotação de Venda: '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(500,40);
       $pdf->Cell(70,5,'#'.$idcot,0,1,'R');
       $pdf->Ln(4);

       $pdf->SetFont('arial','B',10);
       $pdf->SetXY(425,55);
       $pdf->Cell(70,5,utf8_decode('Data da Cotação: '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(500,55);
       $pdf->Cell(70,5,$datacotacao,0,1,'R');
       $pdf->ln(1);

       $pdf->SetFont('arial','B',10); 
       $pdf->SetXY(500,70);
       $pdf->Cell(70,5,utf8_decode(' Página: '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(500,70);
       $pdf->Cell(70,5,$pag,0,1,'R');
       $pdf->ln(1);

       $pdf->Cell(0,5,"","B",1,'C');
       $pdf->SetFont('arial','B',10); 
       $pdf->SetXY(25,100);
       $pdf->SetFillColor(235,239,240);
       $pdf->Rect(26, 95, 542, 15, 'F');
       $pdf->Cell(70,5,utf8_decode('Dados da Cotação - '.$unit->name),0,1,'L');

       $pdf->SetXY(25,118);
       $pdf->Cell(70,5,utf8_decode('Descrição do Pedido '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(25,133);
       $pdf->Cell(70,5,utf8_decode($ped->descricaopedido),0,1,'L');

       $pdf->SetFont('arial','B',10);
       $pdf->SetXY(355,118);
       $pdf->Cell(70,5,utf8_decode('Departamento '),0,1,'L');
       $pdf->SetFont('arial','',8);
       $pdf->SetXY(355,133);
       $depNome = utf8_decode((string) ($dep->name ?? ''));
       $larguraMaxDep = 210; // evita ultrapassar a margem direita
       while ($depNome !== '' && $pdf->GetStringWidth($depNome) > $larguraMaxDep)
       {
           $depNome = substr($depNome, 0, -1);
       }
       if ($depNome !== utf8_decode((string) ($dep->name ?? '')))
       {
           $depNome = rtrim($depNome) . '...';
           while ($depNome !== '...' && $pdf->GetStringWidth($depNome) > $larguraMaxDep)
           {
               $depNome = substr($depNome, 0, -4) . '...';
           }
       }
       $pdf->Cell($larguraMaxDep,5,$depNome,0,1,'L');

       $pdf->SetFont('arial','B',10);
       $pdf->SetXY(25,148);
       $pdf->Cell(70,5,utf8_decode('Fornecedor '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(25,163);
       $pdf->Cell(70,5,utf8_decode($cnpj.' - '.substr($nome,0,38)),0,1,'L');
       $pdf->SetXY(25,178);
       $pdf->Cell(70,5,utf8_decode($nomecidade),0,1,'L');

       $pdf->SetFont('arial','B',10);
       $pdf->SetXY(355,148);
       $pdf->Cell(70,5,utf8_decode('Autorizador por '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(355,163);
       $pdf->Cell(70,5,utf8_decode($usuario),0,1,'L');

       $pdf->SetFont('arial','B',10); 
       $pdf->SetXY(25,208);
       $pdf->SetFillColor(235,239,240);
       $pdf->Rect(26, 203, 542, 15, 'F');
       $pdf->Cell(70,5,utf8_decode('Itens da Cotação '),0,1,'L');

       $pdf->SetFont('arial','B',10); 
       $pdf->SetFillColor(149,192,230);
       $pdf->Rect(26, 233, 542, 15, 'F');

       $pdf->SetXY(25,238);
       $pdf->Cell(70,5,utf8_decode('ID'),0,1,'L');
       $pdf->SetXY(45,238);
       $pdf->Cell(70,5,utf8_decode('Descrição'),0,1,'L');
        $pdf->SetXY(350,238);
        $pdf->Cell(70,5,utf8_decode('Und'),0,1,'C');
        $pdf->SetXY(380,238);
        $pdf->Cell(70,5,utf8_decode('Qtde'),0,1,'C');
       $pdf->SetXY(420,238);
       $pdf->Cell(70,5,utf8_decode('Valor'),0,1,'R');
       $pdf->SetXY(490,238);
       $pdf->Cell(70,5,utf8_decode('Valor Total'),0,1,'R');

       $pdf->ln(1);
    }
   public static function onExibirPreAprovada($object)
    {
        try 
        {
            if (self::cotacaoTemBloqueio($object->id)) {
                return false;
            }

            $estado  = $object->estado_pedido_id;
            $estado1 = $object->estado_pedido1_id;
            $estadosPermitidos = Aprovador::getEstadosDisponiveis();

            if (TSession::getValue('testar_revisao')==1) {            
                // Impede exibição se estiver em revisão
                if ($estado1 == EstadoPedido::REVISAO) {
                    return false;
                } 
            }


            // Exibe o botão apenas se:
            // - O estado atual for AGUARDANDO
            // - E o usuário tiver permissão para PREAPROVAR
            if ($estado == EstadoPedido::AGUARDANDO &&
                in_array(EstadoPedido::PREAPROVADO, $estadosPermitidos))
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

     public static function onExibirAnexos($object)
    {
        try 
        {
              if( in_array($object->estado_pedido_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_id, [EstadoPedido::PGTOAPROVADO, EstadoPedido::FINALIZADO,EstadoPedido::APROVADO ]) )
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }


}

