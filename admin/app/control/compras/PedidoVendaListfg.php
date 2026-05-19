<?php

//Este formulario foi feito para atender a Prefeitura de Flores de Goiais e é para o gestor, e o gestor não faz pedido, quem faz pedido é o fornecedor.

class PedidoVendaListfg extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Pedido';
    private static $primaryKey = 'id';
    private static $formName = 'form_PedidoVendaList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

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
        $this->form->setFormTitle("Listagem de pedidos");
        $this->limit = 20;

        $criteria_departamento_unit_id = new TCriteria();
        $criteria_estado_pedido_venda_id = new TCriteria();
        $criteria_centrocusto_id = new TCriteria();
        $criteria_system_users_id = new TCriteria();
        $criteria_cliente_id = new TCriteria();
        $criteria_cidade_id = new TCriteria();

        $descricaopedido = new TEntry('descricaopedido');
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'SystemUserDepartamentoUnit', 'departamento_unit_id', '{system_users->name} - {departamento_unit->name}','departamento_unit_id asc' , $criteria_departamento_unit_id );
        $estado_pedido_venda_id = new TDBSelect('estado_pedido_venda_id', 'minierp', 'EstadoPedido', 'id', '{nome}','nome asc' , $criteria_estado_pedido_venda_id );
        $dt_pedido = new BDateRange('dt_pedido', 'dt_pedido_fim');
        $centrocusto_id = new TDBCombo('centrocusto_id', 'minierp', 'Centrocusto', 'id', '{nome}','nome asc' , $criteria_centrocusto_id );
        $system_users_id = new TDBCombo('system_users_id', 'minierp', 'SystemUsers', 'id', '{name}','name asc' , $criteria_system_users_id );
        $cliente_id = new TDBCombo('cliente_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_cliente_id );
        $cidade_id = new TDBCombo('cidade_id', 'minierp', 'Cidade', 'id', '{nome} - {estado->sigla}','nome asc' , $criteria_cidade_id );


        $dt_pedido->setMask('dd/mm/yyyy');
        $dt_pedido->setDatabaseMask('yyyy-mm-dd');
        $cidade_id->enableSearch();
        $cliente_id->enableSearch();
        $centrocusto_id->enableSearch();
        $system_users_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $estado_pedido_venda_id->enableSearch();

        $dt_pedido->setSize(380);
        $cidade_id->setSize('100%');
        $cliente_id->setSize('100%');
        $centrocusto_id->setSize('100%');
        $descricaopedido->setSize('100%');
        $system_users_id->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $estado_pedido_venda_id->setSize('100%', 70);

        $row1 = $this->form->addFields([new TLabel("Descrição do pedido:", null, '14px', null, '100%'),$descricaopedido],[new TLabel("Unidades / Dep / Secretárias ", null, '14px', null),$departamento_unit_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Estado de pedido:", null, '14px', null, '100%'),$estado_pedido_venda_id],[new TLabel("Data do pedido:", null, '14px', null, '100%'),$dt_pedido]);
        $row2->layout = ['col-sm-6',' col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Centro de custo:", null, '14px', null, '100%'),$centrocusto_id],[new TLabel("Usuário:", null, '14px', null, '100%'),$system_users_id]);
        $row3->layout = ['col-sm-6',' col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Fornecedor:", null, '14px', null),$cliente_id],[new TLabel("Cidade:", null, '14px', null),$cidade_id]);
        $row4->layout = ['col-sm-6',' col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_descricaopedido = new TDataGridColumn('descricaopedido', "Descrição Pedido", 'left' , '180px');
        $column_cliente_nome = new TDataGridColumn('cliente->nome', "Fornecedor", 'left' , '180px');
        $column_dt_pedido_transformed = new TDataGridColumn('dt_pedido', "Data ", 'left' , '10px');
        $column_valor_total_transformed = new TDataGridColumn('valor_total', "Vl Pedido", 'right' , '120px');
        $column__transformed = new TDataGridColumn('', "Vl Cotação", 'right' , '120px');
        $column_estado_pedido_venda_nome_transformed = new TDataGridColumn('estado_pedido_venda->nome', "Estado pedido", 'center' , '240px');
        $column_system_users_name = new TDataGridColumn('system_users->name', "Usuário", 'center' , '50px');
        $column_cidade_id_transformed = new TDataGridColumn('cidade_id', "Cidade", 'left');

        $column_dt_pedido_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column__transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here

            // Código gerado pelo snippet: "Conexão com banco de dados"
            TTransaction::open('minierp');

            $value=0;    
            $cotacao1 = Cotacao::where('pedido_id','=',$object->id)
                               ->where('pessoa_id','=',$object->cliente_id)
                               ->load();
            if ($cotacao1) { 
               foreach($cotacao1 as $cot1)
               {
                    $objects = ItensCotacao::where('cotacao_id','=',$cot1->id)
                                           ->load();

                    if ($objects) {
                       foreach ($objects as $obj) {
                           // code...
                           $value = $value + $obj->valor_total ;
                        }
                    }
               }
            }

            return 'R$ '.number_format($value, 2, ',', '.');
            // code

            TTransaction::close();
        });

        $column_estado_pedido_venda_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
            $temnotafiscal = false;

            if ($object->estado_pedido_venda::FINALIZADO || $object->estado_pedido_venda::APROVADO || $object->estado_pedido_venda::PGTOAPROVADO || $object->estado_pedido_venda::ENTREGUE) {
                // var_dump($object);
            //die();  
                TTransaction::open('minierp');

                $cot = Cotacao::where('pedido_id','=',$object->id)
                              ->where('pessoa_id','=',$object->cliente_id)
                              ->load();

                if ($cot)
                {
                    foreach ($cot as $cots) {
                        $doccot = DocumentosCotacao::where('cotacao_id','=',$cots->id)
                                                   ->load();
                        if ($doccot){
                            $temnotafiscal = true;
                        }
                        break;
                    }
                }

                TTransaction::close();
            }
            if ($temnotafiscal) {
               $anexo = $object->estado_pedido_venda->nome.' <i class="fa fa-paperclip" aria-hidden="true"></i>';
                return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido_venda->cor}'> {$anexo} <span>";
            } else {
                return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido_venda->cor}'> {$object->estado_pedido_venda->nome} <span>";
            }

        });

        $column_cidade_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here

                TTransaction::open('minierp');

                $cidade = new Cidade($object->cidade_id);
                if ($cidade) {
                    $estado = new Estado($cidade->estado_id);
                    return "{$cidade->nome} - {$estado->sigla}";

                } else {
                    return "Não informado!!!";

                }

                TTransaction::close();

        });        

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricaopedido);
        $this->datagrid->addColumn($column_cliente_nome);
        $this->datagrid->addColumn($column_dt_pedido_transformed);
        $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column__transformed);
        $this->datagrid->addColumn($column_estado_pedido_venda_nome_transformed);
        $this->datagrid->addColumn($column_system_users_name);
        $this->datagrid->addColumn($column_cidade_id_transformed);

        // creates two datagrid actions
        $action1 = new TDataGridAction(['PedidoVendaFormViewForn', 'onShow'],     ['id' => '{id}']);
        $action4 = new TDataGridAction(['PedidoVendaDocumentForn', 'onGenerate'],   ['id' => '{id}']);
        $action6 = new TDataGridAction(['CotacaoPendenteListForn', 'onSetProject'],   ['id' => '{id}']);
        $action7 = new TDataGridAction(['CotacaoPendenteListForn', 'onSetProject'],   ['id' => '{id}']);
        $action8 = new TDataGridAction([$this, 'onCancelarPedido'],   ['id' => '{id}']);
        $action9 = new TDataGridAction([$this, 'onGerarFinanceiro'],   ['id' => '{id}']);
        $action10 = new TDataGridAction([$this, 'onFinalizarPedido'],   ['id' => '{id}']);
        $action11 = new TDataGridAction(['DocumentosCotacaoFornList', 'onSetProject'],   ['id' => '{id}']);
        $action12 = new TDataGridAction([$this, 'onCancelarAprovacao'],   ['id' => '{id}']);

        $action1->setLabel('Visualizar pedido');
        $action1->setImage('fas:search-plus #673AB7');

        $action4->setLabel('Documento Pedido');
        $action4->setImage('far:file-pdf #000000');

        $action6->setLabel('Aprovar');
        $action6->setImage('fas:thumbs-up #9C27B0');
        $action6->setDisplayCondition('PedidoVendaList::onExibirAprovar');

        $action7->setLabel('Reprovar');
        $action7->setImage('fas:thumbs-down #F44336');
        $action7->setDisplayCondition('PedidoVendaList::onExibirReprovar');

        $action8->setLabel('Cancelar pedido');
        $action8->setImage('fas:times-circle #E91E63');
        $action8->setDisplayCondition('PedidoVendaList::onExibirCancelado');

        $action9->setLabel('Gerar financeiro');
        $action9->setImage('fas:money-bill-wave #FFA500');
        $action9->setDisplayCondition('PedidoVendaList::onExibirGerarFinanceiro');

        $action10->setLabel('Finalizar pedido');
        $action10->setImage('fas:door-closed #009688');
        $action10->setDisplayCondition('PedidoVendaList::onExibirFinalizar');

        $action11->setLabel('Anexos');
        $action11->setImage('fas:paperclip #795548');
        $action11->setDisplayCondition('PedidoVendaList::onExibirAnexos');

        $action12->setLabel('Cancelar Aprovação');
        $action12->setImage('fas:undo #009688');
        $action12->setDisplayCondition('PedidoVendaList::onExibirCancelarAprovacao');

        $action_group = new TDataGridActionGroup('Clique Ações ', 'fa:th red');

        $action_group->addAction($action1);
        $action_group->addAction($action4);
        $action_group->addAction($action6);
        $action_group->addAction($action7);
        $action_group->addAction($action8);
        $action_group->addAction($action9);
        $action_group->addAction($action10);
        $action_group->addAction($action11);
        $action_group->addAction($action12);

        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);

/*

        $action_onShow = new TDataGridAction(array('PedidoVendaFormView', 'onShow'));
        $action_onShow->setUseButton(false);
        $action_onShow->setButtonClass('btn btn-default btn-sm');
        $action_onShow->setLabel("Visualizar Pedido");
        $action_onShow->setImage('fas:search-plus #673AB7');
        $action_onShow->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onShow);

        $action_onGenerate = new TDataGridAction(array('PedidoVendaDocumentForn', 'onGenerate'));
        $action_onGenerate->setUseButton(false);
        $action_onGenerate->setButtonClass('btn btn-default btn-sm');
        $action_onGenerate->setLabel("Documento Pedido");
        $action_onGenerate->setImage('far:file-pdf #000000');
        $action_onGenerate->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onGenerate);

        $action_CotacaoPendenteListForn_onShow = new TDataGridAction(array('CotacaoPendenteListForn', 'onShow'));
        $action_CotacaoPendenteListForn_onShow->setUseButton(false);
        $action_CotacaoPendenteListForn_onShow->setButtonClass('btn btn-default btn-sm');
        $action_CotacaoPendenteListForn_onShow->setLabel("");
        $action_CotacaoPendenteListForn_onShow->setImage('fas:thumbs-up #9C27B0');
        $action_CotacaoPendenteListForn_onShow->setField(self::$primaryKey);
        $action_CotacaoPendenteListForn_onShow->setDisplayCondition('PedidoVendaListfg::onExibirAprovar');

        $this->datagrid->addAction($action_CotacaoPendenteListForn_onShow);

        $action_CotacaoPendenteListForn_onShow = new TDataGridAction(array('CotacaoPendenteListForn', 'onShow'));
        $action_CotacaoPendenteListForn_onShow->setUseButton(false);
        $action_CotacaoPendenteListForn_onShow->setButtonClass('btn btn-default btn-sm');
        $action_CotacaoPendenteListForn_onShow->setLabel("Reprovar");
        $action_CotacaoPendenteListForn_onShow->setImage('fas:thumbs-down #F44336');
        $action_CotacaoPendenteListForn_onShow->setField(self::$primaryKey);
        $action_CotacaoPendenteListForn_onShow->setDisplayCondition('PedidoVendaListfg::onExibirReprovar');

        $this->datagrid->addAction($action_CotacaoPendenteListForn_onShow);

        $action_onCancelarPedido = new TDataGridAction(array('PedidoVendaListfg', 'onCancelarPedido'));
        $action_onCancelarPedido->setUseButton(false);
        $action_onCancelarPedido->setButtonClass('btn btn-default btn-sm');
        $action_onCancelarPedido->setLabel("Cancelar pedido");
        $action_onCancelarPedido->setImage('fas:times-circle #E91E63');
        $action_onCancelarPedido->setField(self::$primaryKey);
        $action_onCancelarPedido->setDisplayCondition('PedidoVendaListfg::onExibirCancelado');

        $this->datagrid->addAction($action_onCancelarPedido);

        $action_onGerarFinanceiro = new TDataGridAction(array('PedidoVendaListfg', 'onGerarFinanceiro'));
        $action_onGerarFinanceiro->setUseButton(false);
        $action_onGerarFinanceiro->setButtonClass('btn btn-default btn-sm');
        $action_onGerarFinanceiro->setLabel("Aprovar pagamento");
        $action_onGerarFinanceiro->setImage('fas:money-bill-wave #FFA500');
        $action_onGerarFinanceiro->setField(self::$primaryKey);
        $action_onGerarFinanceiro->setDisplayCondition('PedidoVendaListfg::onExibirGerarFinanceiro');

        $this->datagrid->addAction($action_onGerarFinanceiro);

        $action_onFinalizarPedido = new TDataGridAction(array('PedidoVendaListfg', 'onFinalizarPedido'));
        $action_onFinalizarPedido->setUseButton(false);
        $action_onFinalizarPedido->setButtonClass('btn btn-default btn-sm');
        $action_onFinalizarPedido->setLabel("Finalizar pedido");
        $action_onFinalizarPedido->setImage('fas:door-closed #009688');
        $action_onFinalizarPedido->setField(self::$primaryKey);
        $action_onFinalizarPedido->setDisplayCondition('PedidoVendaListfg::onExibirFinalizar');

        $this->datagrid->addAction($action_onFinalizarPedido);

        $action_onSetProject = new TDataGridAction(array('DocumentosCotacaoFornList', 'onSetProject'));
        $action_onSetProject->setUseButton(false);
        $action_onSetProject->setButtonClass('btn btn-default btn-sm');
        $action_onSetProject->setLabel("Anexos");
        $action_onSetProject->setImage('fas:paperclip #795548');
        $action_onSetProject->setField(self::$primaryKey);
        $action_onSetProject->setDisplayCondition('PedidoVendaListfg::onExibirAnexos');

        $this->datagrid->addAction($action_onSetProject);

        $action_onCancelarAprovacao = new TDataGridAction(array('PedidoVendaListfg', 'onCancelarAprovacao'));
        $action_onCancelarAprovacao->setUseButton(false);
        $action_onCancelarAprovacao->setButtonClass('btn btn-default btn-sm');
        $action_onCancelarAprovacao->setLabel("Cancelar Aprovação");
        $action_onCancelarAprovacao->setImage('fas:undo #009688');
        $action_onCancelarAprovacao->setField(self::$primaryKey);
        $action_onCancelarAprovacao->setDisplayCondition('PedidoVendaListfg::onExibirCancelarAprovacao');

        $this->datagrid->addAction($action_onCancelarAprovacao);

*/

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de pedidos ");
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

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['PedidoVendaListfg', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['PedidoVendaListfg', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['PedidoVendaListfg', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['PedidoVendaListfg', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['PedidoVendaListfg', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['PedidoVendaListfg', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['PedidoVendaListfg', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($button_atualizar);

        $head_right_actions->add($dropdown_button_exportar);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Compras","Pedidos de venda Flores"]));
        }

        $container->add($panel);

        parent::add($container);

    }

    public static function onExibirAprovar($object)
    {
        try 
        {
            if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::COMPROPOSTA]) )
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
    public static function onExibirReprovar($object)
    {
        try 
        {
             if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::COMPROPOSTA]) )
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
    public function onCancelarPedido($param = null) 
    {

        if (isset($param['confirmCancelar']) && $param['confirmCancelar']) {
            try 
            {
                TTransaction::open(self::$database);

                $pedido = new Pedido($param['id'], false);

                // Atualiza o status do pedido e registra histórico
                $pedido->estado_pedido_venda_id = EstadoPedido::CANCELADO;
                $pedido->store();

                $this->registrarHistoricoPedidoCancelado($pedido);

                $cot = Cotacao::where('pessoa_id','=',$pedido->cliente_id)
                                  ->where('pedido_id','=',$pedido->id)
                                  ->load();
                if ($cot) {
                    foreach($cot as $cotacao){
                      $cotacao->estado_pedido_id = EstadoPedido::CANCELADO;
                      $cotacao->store();
                      $this->registrarHistoricoCotacaoCancelado($cotacao);
                    }
                }

                TToast::show('success', "Pedido cancelado com sucesso!!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoVendaListfg', 'onSetProject');
              //  $this->form->setData($pedido); 
                TTransaction::close();

            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onCancelarPedido'));
            $action->setParameters($param);
            $action->setParameter('confirmCancelar', true);

            new TQuestion('Tem certeza que deseja Cancelar este pedido?', $action);
        }
        /*try 
        {
            //code here
       */
            //</autoCode>
       /* }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }
    public static function onExibirCancelado($object)
    {
        try 
        {

             if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::PENDENTE, EstadoPedido::NAOENVIADO, EstadoPedido::APROVADO ]) )
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
    public function onGerarFinanceiro($param = null) 
    {
       if (isset($param['confirmagerarfinanceiro']) && $param['confirmagerarfinanceiro']) {
            try {
                 TTransaction::open(self::$database);
                //$this->form->setData($pedido); 
                $pedido = new Pedido($param['key']);

                $cot = Cotacao::where('pessoa_id','=',$pedido->cliente_id)
                               ->where('pedido_id','=',$pedido->id)
                               ->load();
                if ($cot) {
                   foreach($cot as $cotacao){
                      $cotacao->estado_pedido_id = EstadoPedido::PGTOAPROVADO;
                      $cotacao->store();
                      $valoritens=0;
                      $itenscotacao = ItensCotacao::where('cotacao_id','=',$cotacao->id)
                                                   ->load();

                      if ($itenscotacao) {
                         foreach ($itenscotacao as $itensc) {
                            $valoritens += $itensc->valor_total ;
                         }
                      }
                      $pedido->valor_total_cotacao = $valoritens;
                      $this->registrarHistoricoCotacaoAprovado($cotacao);
                      break;
                   }
                }

                $conta = new Conta();
                $conta->pessoa_id            = $pedido->cliente_id;
                $conta->forma_pagamento_id   = 1; //dinheiro 2024-01-01
                $conta->pedido_venda_id      = $pedido->id;
                $conta->dt_vencimento        = date('Y-m-d', strtotime("+35 days"));
                $conta->mes_vencimento       = intval(substr($conta->dt_vencimento,5,2));
                $conta->ano_vencimento       = intval(substr($conta->dt_vencimento,0,4));
                $conta->ano_mes_vencimento   = intval(substr($conta->dt_vencimento,0,4).substr($conta->dt_vencimento,5,2));
                $conta->valor                = $pedido->valor_total_cotacao;
                $conta->parcela              = 1;
                $conta->descricao            = $pedido->descricaopedido;        
                $conta->tipo_conta_id        = TipoConta::PAGAR;
                $conta->dt_emissao           = date('Y-m-d');
                $conta->mes_emissao          = intval(substr($conta->dt_emissao,5,2));
                $conta->ano_emissao          = intval(substr($conta->dt_emissao,0,4));
                $conta->mes_ano_emissao      = intval(substr($conta->dt_emissao,0,4).substr($conta->dt_emissao,5,2));
                $conta->departamento_unit_id = $pedido->departamento_unit_id;
                $conta->system_users_id      = $pedido->system_users_id;
                $conta->store();

                $pedido->estado_pedido_venda_id = EstadoPedido::PGTOAPROVADO;
                $pedido->store();

                $this->registrarHistoricoPedidoAprovado($pedido);

                TToast::show('success', "Pagamento programado com sucesso!!! Consulte financeiro", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoVendaListfg', 'onSetProject');
                $this->form->setData($pedido); 
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onGerarFinanceiro'));
            $action->setParameters($param);
            $action->setParameter('confirmagerarfinanceiro', true);

            new TQuestion('Tem certeza que deseja Gerar o financeiro?', $action);
        }

        /*try 
        {
            //code here

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }
    public static function onExibirGerarFinanceiro($object)
    {
        try 
        {

             if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::APROVADO]) )
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
    public function onFinalizarPedido($param = null) 
    {

       if (isset($param['confirmFinalizacao']) && $param['confirmFinalizacao']) {
            try 
            {
                TTransaction::open(self::$database);

                $pedido = new Pedido($param['id'], false);

                // Atualiza o status do pedido e registra histórico
                $pedido->estado_pedido_venda_id = EstadoPedido::FINALIZADO;
                $pedido->store();

                $this->registrarHistoricoPedidoFinalizar($pedido);

                $cot = Cotacao::where('pessoa_id','=',$pedido->cliente_id)
                                  ->where('pedido_id','=',$pedido->id)
                                  ->load();
                if ($cot) {
                    foreach($cot as $cotacao){
                      $cotacao->estado_pedido_id = EstadoPedido::FINALIZADO;
                      $cotacao->store();
                      $this->registrarHistoricoCotacaoFinalizar($cotacao);
                    }
                }

                TToast::show('success', "Pedido finalizado com sucesso!!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoVendaListfg', 'onProject');
                $this->form->setData($pedido); 
                TTransaction::close();

            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onFinalizarPedido'));
            $action->setParameters($param);
            $action->setParameter('confirmFinalizacao', true);

            new TQuestion('Tem certeza que deseja Finalizar este pedido?', $action);
        }
      /*  try 
        {
            //code here
      */
            //</autoCode>
     /*   }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }
    public static function onExibirFinalizar($object)
    {
        try 
        {
             if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::PGTOAPROVADO]) )
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
              if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::PGTOAPROVADO, EstadoPedido::FINALIZADO,EstadoPedido::APROVADO ]) )
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
    public function onCancelarAprovacao($param = null) 
    {

            //code here
            //voltar o status de aprovado para com proposta aguardando 
            //no pedido e na cotacao aguardando proposta.
            //gravar no historico da cotacao e pedido

        if (isset($param['confirmEnviarCancelarAprovacao']) && $param['confirmEnviarCancelarAprovacao']) {
            try {
                TTransaction::open(self::$database);

                $pedido = new Pedido($param['id'], false);

                // Atualiza o status do pedido e registra histórico
                $pedido->estado_pedido_venda_id = EstadoPedido::COMPROPOSTA;
                $pedido->store();

                $this->registrarHistoricoPedidocomproposta($pedido);

                $cotacao = Cotacao::where('pedido_id','=',$pedido->id)
                                  ->where('pessoa_id','=',$pedido->cliente_id)
                                  ->load();
                if ($cotacao){
                    foreach ($cotacao as $cot)
                    {
                        $cot->estado_pedido_id = EstadoPedido::AGUARDANDO;
                        $cot->store();
                        $this->registrarHistoricoCotacaoAguardando($cot);
                    }
                }

                TTransaction::close();
                TToast::show('success', "Cancelamento da aprovação feito com sucesso!!!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoVendaListfg', 'onSetProject');
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onCancelarAprovacao'));
            $action->setParameters($param);
            $action->setParameter('confirmEnviarCancelarAprovacao', true);

            new TQuestion('Tem certeza que deseja fazer o cancelamento desta aprovação?', $action);
        }     
     /*   try 
        {
            //code here

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }
    public static function onExibirCancelarAprovacao($object)
    {
        try 
        {
              if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::APROVADO]) )
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
    public function onExportCsv($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    $handler = fopen($output, 'w');
                    TTransaction::open(self::$database);

                    foreach ($objects as $object)
                    {
                        $row = [];
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            //var_dump($column);

                            if(($column_name == 'cidade_id') || ($column_name == 'descricaopedido') || ($column_name == 'cliente->nome') || ($column_name == 'system_users->name')){

                                if($column_name =='cidade_id'){
                                $cidade = new Cidade($object->$column_name);
                                $estado = new Estado($cidade->estado_id);

                                $row[] = mb_convert_encoding($cidade->nome.'-'.$estado->sigla, 'ISO-8859-1', 'UTF-8');

                                } else{
                                $row[] = mb_convert_encoding($object->$column_name, 'ISO-8859-1', 'UTF-8');
                                }

                            } else{

                                        if (isset($object->$column_name))
                                        {
                                            $row[] = is_scalar($object->$column_name) ? $object->$column_name : '';
                                        }
                                        else if (method_exists($object, 'render'))
                                        {
                                            $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                            $row[] = $object->render($column_name);
                                        }
                            }
                        }

                        fputcsv($handler, $row);
                    }

                    fclose($handler);
                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXls($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xls';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $widths = [];
                $titles = [];

                foreach ($this->datagrid->getColumns() as $column)
                {
                    $titles[] = $column->getLabel();
                    $width    = 100;

                    if (is_null($column->getWidth()))
                    {
                        $width = 100;
                    }
                    else if (strpos($column->getWidth(), '%') !== false)
                    {
                        $width = ((int) $column->getWidth()) * 5;
                    }
                    else if (is_numeric($column->getWidth()))
                    {
                        $width = $column->getWidth();
                    }

                    $widths[] = $width;
                }

                $table = new \TTableWriterXLS($widths);
                $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                $table->addStyle('data',   'Helvetica', '10', '',  '#000000', '#FFFFFF', 'LR');

                $table->addRow();

                foreach ($titles as $title)
                {
                    $table->addCell($title, 'center', 'title');
                }

                $this->limit = 0;
                $objects = $this->onReload();

                TTransaction::open(self::$database);
                if ($objects)
                {
                    foreach ($objects as $object)
                    {
                        $table->addRow();
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $value = '';

                            if(($column_name == 'cidade_id') || ($column_name == 'descricaopedido') || ($column_name == 'cliente->nome') || ($column_name == 'system_users->name')){

                                if($column_name == 'cidade_id'){
                                    $cidade = new Cidade($object->$column_name);
                                    $estado = new Estado($cidade->estado_id);

                                    $value = mb_convert_encoding($cidade->nome.'-'.$estado->sigla, 'ISO-8859-1', 'UTF-8');

                                }else{
                                     $value = mb_convert_encoding($object->$column_name, 'ISO-8859-1', 'UTF-8');
                                }

                            }else{

                                    if (isset($object->$column_name))
                                    {
                                        $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                                    }
                                    else if (method_exists($object, 'render'))
                                    {
                                        $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                        $value = $object->render($column_name);
                                    }

                                    $transformer = $column->getTransformer();
                                    if ($transformer)
                                    {
                                        $value = strip_tags(call_user_func($transformer, $value, $object, null));
                                    }

                            $table->addCell($value, 'center', 'data');
                            }
                        }
                    }
                }
                $table->save($output);
                TTransaction::close();

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportPdf($param = null) 
    {
        try
        {
       /*     $output = 'app/output/'.uniqid().'.pdf';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->onReload();

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = $output;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($object);
                $window->show();*/

           // open a transaction with database 'conexao'
            TTransaction::open('minierp');

            $conn = TConnection::open('minierp');

            // get the form data into an active record
            $data       = $this->form->getData();

            //code here
            $pdf = new FPDF("P","pt","A4");

            $repository = new TRepository('Pedido'); // creates a repository
            $limit = 999999999999;

            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('order', 'dt_pedido');
            $criteria->setProperty('limit', $limit);  

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       

                }
            }
            // load the objects according to criteria
            $objects = $repository->load($criteria);

            $linha          = 0;
            $pag            = 1; 
            $alturalinha    = 50;
            $qtd            = 0;
            $vltotal        = 0;
            $vltotalcotacao = 0;

            $user = SystemUserUnit::where('system_user_id','=',TSession::getValue('userid'))
                                  ->load();
            if ($user) {
                foreach ($user as $users) {
                  $unit = SystemUnit::where('id','=',$users->system_unit_id)
                                    ->load();
                  if ($unit) {
                      foreach ($unit as $units)
                      {
                         $cnpj = $units->cnpj;
                         $unidade = $units->name;
                      }
                  }
                }
            } else {
                  $cnpj = '';
                  $unidade = '';
            }

            if ($objects) {
                foreach ($objects as $object) {

                    $listar = 0;
                    $suserdep = SystemUserDepartamentoUnit::where('system_users_id','=',TSession::getValue('userid'))
                                                          ->load();

                    //$objects->system_user_id verificar se ele pertence a unidade que logou e addItem
                    if ($suserdep)
                    {                    
                        foreach($suserdep as $suserdeps){
                            if ($suserdeps->departamento_unit_id==$object->departamento_unit_id) {
                                $listar=1;
                            }
                        }
                    }
               if ($listar==1) {       
               if ( ($linha==0) || ($linha >= 46) ){
                  $this->cabecalho($pdf, $linha,$pag,$unidade,$cnpj,$filters);
	              $linha = 0;
	              $pag=$pag + 1; 
	              $alturalinha = 50;
               }
               //email
               $pdf->setFont('arial','',6);

               $pdf->SetXY(27,$alturalinha);
               $pdf->Cell(70,5,$object->id,0,1,'L');

               $data = TDate::date2br($object->dt_pedido);

               $pdf->SetXY(47,$alturalinha);
               $pdf->Cell(70,5,$data,0,1,'L');

               $pessoa = new Pessoa($object->cliente_id);
               $pdf->SetXY(85,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding($pessoa->nome, 'ISO-8859-1', 'UTF-8'),0,1,'L');

               $pdf->SetXY(220,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding($object->descricaopedido, 'ISO-8859-1', 'UTF-8'),0,1,'L');

               $estadopedido = new EstadoPedido($object->estado_pedido_venda_id);
               $pdf->SetXY(377,$alturalinha);
               $pdf->Cell(70,5,$estadopedido->nome,0,1,'L');

               $pdf->SetXY(445,$alturalinha);
               $pdf->Cell(70,5,number_format($object->valor_total, 2),0,1,'R');

               $pdf->SetXY(500,$alturalinha);
               $pdf->Cell(70,5,number_format($object->valor_total_cotacao, 2),0,1,'R');

               $user = new SystemUsers($object->system_users_id);      
               $nomeuser = $user->name;
               $pdf->SetXY(600,$alturalinha);
               $pdf->Cell(70,5,$nomeuser,0,1,'L');

               $cidadeestado='';
               if ($object->cidade_id<>NULL){
                  $cid = new Cidade($object->cidade_id);
                  $est = Estado::where('id','=',$cid->estado_id)
                                ->load();
                  if ($est){
                      foreach($est as $estado)
                      $cidadeestado=rtrim($cid->nome).' - '.$estado->sigla;
                  }

               } 

               $pdf->SetXY(700,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding($cidadeestado, 'ISO-8859-1', 'UTF-8'),0,1,'L');

               $pdf->ln(1);
               $alturalinha=$alturalinha + 10;
               $linha = $linha + 1;
               //somatoria
               $qtd = $qtd + 1;
               $vltotal += $object->valor_total;
               $vltotalcotacao += $object->valor_total_cotacao;

               } 
             } 

             $alturalinha=$alturalinha + 10; 

             $pdf->ln(1); 
             $pdf->Cell(0,4,"","B",1,'C');

             $pdf->SetXY(27,$alturalinha);
             $pdf->Cell(70,5,'Total Geral : '.$qtd,0,1,'L');

             $pdf->SetXY(445,$alturalinha);
             $pdf->Cell(70,5,number_format($vltotal, 2),0,1,'R');

             $pdf->SetXY(500,$alturalinha);
             $pdf->Cell(70,5,number_format($vltotalcotacao, 2),0,1,'R');

             $nome = 'Pedidos.pdf';

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
            new TMessage('info', 'Pedidos gerado com sucesso. Por favor, habilite popups no navegador.');
            }            
            // fill the form with the active record data
            $this->form->setData($data);
             TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXml($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xml';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    TTransaction::open(self::$database);

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->{'formatOutput'} = true;
                    $dataset = $dom->appendChild( $dom->createElement('dataset') );

                    foreach ($objects as $object)
                    {
                        $row = $dataset->appendChild( $dom->createElement( self::$activeRecord ) );

                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $column_name_raw = str_replace(['(','{','->', '-','>','}',')', ' '], ['','','_','','','','','_'], $column_name);

                            if(($column_name == 'cidade_id') || ($column_name == 'descricaopedido') || ($column_name == 'cliente->nome') || ($column_name == 'system_users->name')){

                                if($column_name == 'cidade_id'){
                                    $cidade = new Cidade($object->$column_name);
                                    $estado = new Estado($cidade->estado_id);

                                    $value = mb_convert_encoding($cidade->nome.'-'.$estado->sigla, 'ISO-8859-1', 'UTF-8');

                                }else{
                                     $value = mb_convert_encoding($object->$column_name, 'ISO-8859-1', 'UTF-8');
                                }

                            }else{

                                    if (isset($object->$column_name))
                                    {
                                        $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                                        $row->appendChild($dom->createElement($column_name_raw, $value)); 
                                    }
                                    else if (method_exists($object, 'render'))
                                    {
                                        $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                        $value = $object->render($column_name);
                                        $row->appendChild($dom->createElement($column_name_raw, $value));
                                    }
                            }
                        }
                    }

                    $dom->save($output);

                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    public static function onShowCurtainFilters($param = null) 
    {
        try 
        {
            //code here

                        $filter = new self([]);

            $btnClose = new TButton('closeCurtain');
            $btnClose->class = 'btn btn-sm btn-default';
            $btnClose->style = 'margin-right:10px;';
            $btnClose->onClick = "Template.closeRightPanel();";
            $btnClose->setLabel("Fechar");
            $btnClose->setImage('fas:times');

            $filter->form->addHeaderWidget($btnClose);

            $page = new TPage();
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('page-name', 'PedidoVendaListfgSearch');
            $page->setProperty('page_name', 'PedidoVendaListfgSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);
        TSession::setValue('data_inicial', NULL);
        TSession::setValue('data_final', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
      //   $this->datagrid->clear();
    }
    public function onRefresh($param = null) 
    {
        $this->onReload([]);
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->descricaopedido) AND ( (is_scalar($data->descricaopedido) AND $data->descricaopedido !== '') OR (is_array($data->descricaopedido) AND (!empty($data->descricaopedido)) )) )
        {

            $filters[] = new TFilter('descricaopedido', 'like', "%{$data->descricaopedido}%");// create the filter 
        }

        if (isset($data->estado_pedido_venda_id) AND ( (is_scalar($data->estado_pedido_venda_id) AND $data->estado_pedido_venda_id !== '') OR (is_array($data->estado_pedido_venda_id) AND (!empty($data->estado_pedido_venda_id)) )) )
        {

            $filters[] = new TFilter('estado_pedido_venda_id', 'in', $data->estado_pedido_venda_id);// create the filter 
        }

        if (isset($data->dt_pedido_fim) AND ( (is_scalar($data->dt_pedido_fim) AND $data->dt_pedido_fim !== '') OR (is_array($data->dt_pedido_fim) AND (!empty($data->dt_pedido_fim)) )) )
        {

            $filters[] = new TFilter('dt_pedido', '<=', $data->dt_pedido_fim);// create the filter 
        }

        if (isset($data->dt_pedido) AND ( (is_scalar($data->dt_pedido) AND $data->dt_pedido !== '') OR (is_array($data->dt_pedido) AND (!empty($data->dt_pedido)) )) )
        {

            $filters[] = new TFilter('dt_pedido', '>=', $data->dt_pedido);// create the filter 
        }

        if (isset($data->centrocusto_id) AND ( (is_scalar($data->centrocusto_id) AND $data->centrocusto_id !== '') OR (is_array($data->centrocusto_id) AND (!empty($data->centrocusto_id)) )) )
        {

            $filters[] = new TFilter('centrocusto_id', '=', $data->centrocusto_id);// create the filter 
        }

        if (isset($data->system_users_id) AND ( (is_scalar($data->system_users_id) AND $data->system_users_id !== '') OR (is_array($data->system_users_id) AND (!empty($data->system_users_id)) )) )
        {

            $filters[] = new TFilter('cliente_id', '=', $data->system_users_id);// create the filter 
        }

        if (isset($data->cliente_id) AND ( (is_scalar($data->cliente_id) AND $data->cliente_id !== '') OR (is_array($data->cliente_id) AND (!empty($data->cliente_id)) )) )
        {

            $filters[] = new TFilter('cliente_id', '=', $data->cliente_id);// create the filter 
        }

        if (isset($data->cidade_id) AND ( (is_scalar($data->cidade_id) AND $data->cidade_id !== '') OR (is_array($data->cidade_id) AND (!empty($data->cidade_id)) )) )
        {

            $filters[] = new TFilter('cidade_id', '=', $data->cidade_id);// create the filter 
        }

        TSession::setValue('data_inicial',NULL);
        TSession::setValue('data_final', NULL);

        if (isset($data->dt_pedido_fim) AND ( (is_scalar($data->dt_pedido_fim) AND $data->dt_pedido_fim !== '') OR (is_array($data->dt_pedido_fim) AND (!empty($data->dt_pedido_fim)) )) )
        {

            TSession::setValue('data_final', $data->dt_pedido_fim);
        }

        if (isset($data->dt_pedido) AND ( (is_scalar($data->dt_pedido) AND $data->dt_pedido !== '') OR (is_array($data->dt_pedido) AND (!empty($data->dt_pedido)) )) )
        {

            TSession::setValue('data_inicial', $data->dt_pedido);
        }

        if (isset($data->departamento_unit_id) AND ( (is_scalar($data->departamento_unit_id) AND $data->departamento_unit_id !== '') OR (is_array($data->departamento_unit_id) AND (!empty($data->departamento_unit_id)) )) )
        {

            $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        }

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
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

            // creates a repository for Pedido
            $repository = new TRepository(self::$activeRecord);

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

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            //</blockLine><btnShowCurtainFiltersAutoCode>
            if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }
            //</blockLine></btnShowCurtainFiltersAutoCode>

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $suserdep = SystemUserDepartamentoUnit::where('system_users_id','=',TSession::getValue('userid'))
                                                          ->load();

                    //$objects->system_user_id verificar se ele pertence a unidade que logou e addItem
                    if ($suserdep)
                    {                    
                        foreach($suserdep as $suserdeps){
                            if ($suserdeps->departamento_unit_id==$object->departamento_unit_id) {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                            }
                        }
                    }
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

         $unit=SystemUnit::where('id','=',TSession::getValue('userunitid'))
                         ->load();
        if ($unit)
        {
            foreach($unit as $unitss) {
                //echo ' unit ' .$unitss->utilizasinapi;
                if ($unitss->utilizasinapi=='S'){
                   ImportarTabelaSinapi::import();  
                }
            }
        }
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

        $object = new Pedido($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

private function obterFornecedores($cidades, $seguimentos)
    {
        $query = new ViewEnviarcotacao();

        if ($cidades) {
            $idCidades = array_map(function($cidade){ return $cidade->cidade_id;}, $cidades);
            $query->where('cidade_id', 'in', $idCidades);
        }

        if ($seguimentos) {
            $idSeguimentos = array_map(function($seguimento){ return $seguimento->seguimento_id;}, $seguimentos);
            $query->where('seguimento_id', 'in', $idSeguimentos);
        }

        return $query->getObjects();
    }

    private function gerarCotacoes($fornecedores, $pedido)
    {

       foreach ($fornecedores as $fornecedor) {
            $cot = Cotacao::where('pedido_id','=',$pedido->id)
                          ->where('pessoa_id','=',$fornecedor->id)
                          ->load();
            if (!$cot) {

            $cotacao = new Cotacao();
            $cotacao->pedido_id = $pedido->id;
            $cotacao->pessoa_id = $fornecedor->id;
            $cotacao->data_cotacao = date('Y-m-d');
            $cotacao->estado_pedido_id = EstadoPedido::PENDENTE;
            $cotacao->system_users_id = TSession::getValue('iduser');
            $cotacao->store();

            $this->registrarHistoricoCotacao($cotacao);

            $codido_email_template_id =  EmailTemplate::EMAIL_PEDIDO_ENVIADO; //PEDIDO ENVIADO
            $emailTemplate = new EmailTemplate( $codido_email_template_id);
            $titulo='';
            if ($emailTemplate) {

               $mensagem = $emailTemplate->mensagem;
               $mensagem = str_replace('{nome}', $cotacao->pessoa->nome, $mensagem);
               $mensagem = str_replace('{id}', $pedido->id, $mensagem);
               $mensagem = str_replace('{id1}', $pedido->id, $mensagem);
               $mensagem = str_replace('{data_pedido}', TDate::date2br($pedido->dt_pedido), $mensagem);
              //$mensagem = 'teste de envio de email';

 $mens='';
       $itensp = ItensPedido::where('pedido_venda_id','=',$pedido->id)
                            ->load();
       if ($itensp){
           foreach ($itensp as $itensps ) {
                $prod=new Produto($itensps->produto_id);
                $mens=$mens.' id: '.$itensps->id.' Nome produto: '.$prod->nome.' Quantidade: '.$itensps->quantidade.' Valor: '.$itensps->valor.' Valor Total: '.$itensps->valor_total.'<br>';
           }
       }
               $titulo = str_replace('{id}', $pedido->id, $titulo);

               $mensagem = str_replace('{itens_pedido}', $mens, $mensagem);
               if($cotacao->pessoa->email)
                {

                    MailService::send($cotacao->pessoa->email, $titulo, $mensagem,  'html');

                }
            }

        }}
    }

    private function registrarHistoricoPedido($pedido)
    {

        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::ENVIADO; 
        $hist->aprovador_id = TSession::getValue('userid');
        $hist->store();

    }

    private function registrarHistoricoCotacao($cotacao)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::PENDENTE; 
        $histcotacao->aprovador_id = TSession::getValue('userid');
        $histcotacao->store();
    }
    private function registrarHistoricoPedidoFinalizar($pedido)
    {
        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::FINALIZADO; 
        $hist->aprovador_id = TSession::getValue('userid');
        $hist->store();
    }

    private function registrarHistoricoCotacaoFinalizar($cotacao)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::FINALIZADO; 
        $histcotacao->aprovador_id = TSession::getValue('userid');
        $histcotacao->store();
    }
   private function registrarHistoricoPedidoCancelado($pedido)
    {
        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::CANCELADO; 
        $hist->aprovador_id = TSession::getValue('userid');
        $hist->store();
    }

    private function registrarHistoricoCotacaoCancelado($cotacao)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::CANCELADO; 
        $histcotacao->aprovador_id = TSession::getValue('userid');
        $histcotacao->store();
    }

    private function cabecalho($pdf, $linha,$pag, $unidade, $cnpj, $filters)
    {
        $label = '';

        if(!empty(TSession::getValue('data_inicial'))){     
            $datai = new DateTime(TSession::getValue('data_inicial'));
            $datai = $datai->format('d/m/Y');

            $dataf = new DateTime(TSession::getValue('data_final'));
            $dataf = $dataf->format('d/m/Y');

            $label = 'Periodo: de '. $datai . ' ate '. $dataf;
        }

        $pdf->AddPage();
        $pdf->SetFont('arial','B',8);
        $pdf->SetXY(26,8);
        $pdf->Cell(70,5, $unidade, 0,1,'L');
        $pdf->SetXY(330,8);
        $pdf->Cell(70,5,utf8_decode('Relatório de pedidos de compra '),0,1,'C');
        $pdf->SetXY(660,8);
        $pdf->Cell(70,5,'Hora: '.date("H:i:s"),0,1,'C');
        $pdf->SetXY(748,8);
        $pdf->Cell(70,5,'Data: '.date("d/m/Y"),0,1,'C');
        $pdf->Ln(4);
        $pdf->SetXY(26,20);
        $pdf->Cell(70,5,$cnpj.'      '. $label,0,1,'L');
        $pdf->SetXY(115,20);
        $pdf->Cell(70,5,'',0,1,'L');
        $pdf->SetXY(748,20);
        $pdf->Cell(70,5,utf8_decode(' Página: ').$pag,0,1,'R');
        $pdf->Ln(1);
        //nome
        $pdf->Cell(0,5,"","B",1,'C');
        $pdf->SetXY(27,35);
        $pdf->Cell(70,5,'ID',0,1,'L');

        $pdf->SetXY(47,35);
        $pdf->Cell(70,5,'Data',0,1,'L');

        $pdf->SetXY(85,35);
        $pdf->Cell(70,5,'Nome',0,1,'L');

        $pdf->SetXY(220,35);
        $pdf->Cell(100,5,utf8_decode('Descrição do pedido'),0,1,'L');

        $pdf->SetXY(377,35);
        $pdf->Cell(70,5,'Estado Pedido',0,1,'L');

        $pdf->SetXY(445,35);
        $pdf->Cell(70,5,'Valor Pedido',0,1,'R');

         $pdf->SetXY(470,35);
         $pdf->Cell(100,5,'Valor Cotado',0,1,'R');

         $pdf->SetXY(536,35);
         $pdf->Cell(100,5,'Usuario',0,1,'R');

         $pdf->SetXY(632,35);
         $pdf->Cell(100,5,'Cidade',0,1,'R');
         //                123456789012 

        $pdf->ln(1);

        $pdf->Cell(0,4,"","B",1,'C');
        $linha = 12;
     }
     public function onSetProject($param) {
     }

     private function registrarHistoricoPedidoAprovado($pedido)
     {
         $hist = new PedidoHistorico();
         $hist->pedido_venda_id = $pedido->id;
         $hist->data_operacao = date('Y-m-d');
         $hist->estado_pedido_venda_id = EstadoPedido::PGTOAPROVADO; 
         $hist->aprovador_id = TSession::getValue('iduser');
         $hist->store();
     }

     private function registrarHistoricoCotacaoAprovado($cotacao)
     {
         $histcotacao = new CotacaoHistorico();
         $histcotacao->cotacao_id = $cotacao->id;
         $histcotacao->data_historico = date('Y-m-d');
         $histcotacao->estado_pedido_id = EstadoPedido::PGTOAPROVADO; 
         $histcotacao->aprovador_id = TSession::getValue('iduser');
         $histcotacao->store();
     }
     private function registrarHistoricoPedidocomproposta($pedido)
     {
         $hist = new PedidoHistorico();
         $hist->pedido_venda_id = $pedido->id;
         $hist->data_operacao = date('Y-m-d');
         $hist->estado_pedido_venda_id = EstadoPedido::COMPROPOSTA; 
         $hist->aprovador_id = TSession::getValue('iduser');
         $hist->store();
     }
    private function registrarHistoricoCotacaoAguardando($cotacao)
     {
         $histcotacao = new CotacaoHistorico();
         $histcotacao->cotacao_id = $cotacao->id;
         $histcotacao->data_historico = date('Y-m-d');
         $histcotacao->estado_pedido_id = EstadoPedido::AGUARDANDO; 
         $histcotacao->aprovador_id = TSession::getValue('iduser');
         $histcotacao->store();
     }

}

