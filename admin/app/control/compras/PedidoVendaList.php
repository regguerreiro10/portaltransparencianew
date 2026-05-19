<?php

class PedidoVendaList extends TPage
{
    
    use BuilderDatagridTrait;
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

        $basename   = urlencode('pedido-compra-list.pdf');
        $download   = "download.php?file=app/manual/pedido-compra-list.pdf&basename={$basename}";

        $manual = "
            <span style='float:right;'>
                <a href='{$download}'
                target='_blank'
                style='text-decoration:none;margin-left:10px;'>
                    <i class='fa fa-question-circle'> </i>
                </a>
            </span>
        "; 

        // define the form title
        $this->form->setFormTitle("Listagem de pedidos {$manual}");
        $this->limit = 20;

        $criteria_departamento_unit_id = new TCriteria();
        $criteria_estado_pedido_venda_id = new TCriteria();
        $criteria_centrocusto_id = new TCriteria();
        $criteria_system_users_id = new TCriteria();
        $criteria_cliente_id = new TCriteria();
        $criteria_cidade_id = new TCriteria();

        $login = new LoginForm([]);
        $AlertMensagem = $login->onMensagem('PedidoVendaList');
        $TAlert = new TAlert('danger',$AlertMensagem); 

        $id = new TEntry('id');
        $descricaopedido = new TEntry('descricaopedido');
        $departamento_unit_id = new TDBUniqueSearch('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', 'name','name asc' , $criteria_departamento_unit_id );
        $estado_pedido_venda_id = new TDBSelect('estado_pedido_venda_id', 'minierp', 'EstadoPedido', 'id', '{nome}','nome asc' , $criteria_estado_pedido_venda_id );
        $dt_pedido = new BDateRange('dt_pedido', 'dt_pedido_fim');
        $centrocusto_id = new TDBUniqueSearch('centrocusto_id', 'minierp', 'Centrocusto', 'id', 'nome','nome asc' , $criteria_centrocusto_id );
        $system_users_id = new TDBUniqueSearch('system_users_id', 'minierp', 'SystemUsers', 'id', 'name','name asc' , $criteria_system_users_id );
        $cliente_id = new TDBUniqueSearch('cliente_id', 'minierp', 'Pessoa', 'id', 'nome','nome asc' , $criteria_cliente_id );
        $cidade_id = new TDBUniqueSearch('cidade_id', 'minierp', 'Cidade', 'id', 'nome','nome asc' , $criteria_cidade_id );
        $dt_finalizacao = new BDateRange('dt_finalizacao', 'dt_finalizacao_fim');


        $dt_pedido->setMask('dd/mm/yyyy');
        $dt_finalizacao->setMask('dd/mm/yyyy');

        $dt_pedido->setDatabaseMask('yyyy-mm-dd');
        $dt_finalizacao->setDatabaseMask('yyyy-mm-dd');

        $cidade_id->setMinLength(2);
        $cidade_id->setFilterColumns(['nome']);
        $cidade_id->setMask('{nome} - {estado->sigla}');
        $cliente_id->setMinLength(2);
        $cliente_id->setFilterColumns(['nome']);
        $departamento_unit_id->setMinLength(2);
        $departamento_unit_id->setFilterColumns(['name']);
        $departamento_unit_id->setMask('{system_unit->name} - {name}');
        $centrocusto_id->setMinLength(2);
        $centrocusto_id->setFilterColumns(['nome']);
        $system_users_id->setMinLength(2);
        $system_users_id->setFilterColumns(['name']);
        $estado_pedido_venda_id->enableSearch();

        $dt_pedido->setSize(380);
        $id->setSize('100%');
        $cidade_id->setSize('100%');
        $cliente_id->setSize('100%');
        $dt_finalizacao->setSize(380);
        $centrocusto_id->setSize('100%');
        $descricaopedido->setSize('100%');
        $system_users_id->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $estado_pedido_venda_id->setSize('100%', 70);

        $row1 = $this->form->addFields([new TLabel("ID do pedido:", null, '14px', null, '100%'),$id],[new TLabel("Descrição do pedido:", null, '14px', null, '100%'),$descricaopedido]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Unidades / Dep / Secretárias ", null, '14px', null),$departamento_unit_id],[new TLabel("Estado de pedido:", null, '14px', null, '100%'),$estado_pedido_venda_id]);
        $row2->layout = ['col-sm-6',' col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Data do pedido:", null, '14px', null, '100%'),$dt_pedido],[new TLabel("Data Finalização", null, '14px', null, '100%'),$dt_finalizacao]);
        $row3->layout = ['col-sm-6',' col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Centro de custo:", null, '14px', null, '100%'),$centrocusto_id],[new TLabel("Usuário:", null, '14px', null, '100%'),$system_users_id]);
        $row4->layout = ['col-sm-6',' col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Fornecedor:", null, '14px', null),$cliente_id],[new TLabel("Cidade:", null, '14px', null),$cidade_id]);
        $row5->layout = ['col-sm-6',' col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

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

        $column_id = new TDataGridColumn('id', "Id Pedido", 'center' , '70px');
        $column_descricaopedido = new TDataGridColumn('descricaopedido', "Descrição Pedido", 'left' , '180px');
        $column_cliente_nome = new TDataGridColumn('cliente_nome', "Fornecedor", 'left' , '180px');
        $column_departamento_unit_name = new TDataGridColumn('departamento_nome', "Departamento", 'left');
        $column_dt_pedido_transformed = new TDataGridColumn('dt_pedido', "Data ", 'left' , '10px');
        $column_dt_finalizacao_transformed = new TDataGridColumn('dt_finalizacao', "Dt finalização", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_total', "Vl Pedido", 'right' , '120px');
        $column_valor_total_cotacao_transformed = new TDataGridColumn('valor_total_cotacao', "Vl total cotação", 'right' , '120px');
        $column_valor_desconto_transformed = new TDataGridColumn('valor_desconto_cotacao', "Vl desconto cotação", 'right' , '120px');
        $column_valor_liquido_transformed = new TDataGridColumn('valor_liquido_cotacao', "Vl liquido cotação", 'right' , '120px');
        $column_estado_pedido_venda_nome_transformed = new TDataGridColumn('estado_pedido_venda_nome', "Estado pedido", 'center' , '240px');
        $column_system_users_login = new TDataGridColumn('system_users_login_text', "Usuário", 'center' , '50px');
        $column_cidade_id_transformed = new TDataGridColumn('cidade_nome_estado', "Cidade", 'left');

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

        $column_dt_finalizacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

            return $value;
        });


        $column_valor_total_cotacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
          $column_valor_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
            TTransaction::close();
        });
          $column_valor_liquido_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
            TTransaction::close();
        });
       
        $column_estado_pedido_venda_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $revisao = '';
            if (TSession::getValue('testar_revisao')==1) {
                if (!empty($object->estado_revisao_nome)) {
                    $revisao = "<span style='font-size: 10px; font-style: italic; color: #eee;'>({$object->estado_revisao_nome})</span>";
                }
            }

            $temnotafiscal = !empty($object->tem_nota_fiscal);
            $estado_nome = $object->estado_pedido_venda_nome ?? $value ?? '';
            $estado_cor = $object->estado_pedido_venda_cor ?? '#777';

            if ($temnotafiscal) {
                $anexo = $estado_nome . " <i class='fa fa-paperclip' aria-hidden='true'></i>";
            } else {
                $anexo = $estado_nome;
            }

            return "<span class='label label-default' style='width:260px; background-color:{$estado_cor}; display:inline-block;'> {$anexo} {$revisao} </span>";

        });

/*        $column_cidade_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
  */      
        $column_cidade_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (!empty($object->cidade_nome_estado)) {
                return $object->cidade_nome_estado;
            }
            return "Nao informado!!!";
        });

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        $order_descricaopedido = new TAction(array($this, 'onReload'));
        $order_descricaopedido->setParameter('order', 'descricaopedido');
        $column_descricaopedido->setAction($order_descricaopedido);
        $order_cidade_id_transformed = new TAction(array($this, 'onReload'));
        $order_cidade_id_transformed->setParameter('order', 'cidade_id');
        $column_cidade_id_transformed->setAction($order_cidade_id_transformed);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricaopedido);
        $this->datagrid->addColumn($column_cliente_nome);
        $this->datagrid->addColumn($column_departamento_unit_name);
        $this->datagrid->addColumn($column_dt_pedido_transformed);
        $this->datagrid->addColumn($column_dt_finalizacao_transformed);
         $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column_valor_total_cotacao_transformed);
         $this->datagrid->addColumn($column_valor_desconto_transformed);
         $this->datagrid->addColumn($column_valor_liquido_transformed);
        $this->datagrid->addColumn($column_estado_pedido_venda_nome_transformed);
        $this->datagrid->addColumn($column_system_users_login);
        $this->datagrid->addColumn($column_cidade_id_transformed);

        // creates two datagrid actions
        $action1 = new TDataGridAction(['PedidoVendaFormView', 'onShow'],     ['id' => '{id}']);
        $action2 = new TDataGridAction(['PedidoVendaForm', 'onEdit'],   ['id' => '{id}']);
        $action3 = new TDataGridAction([$this, 'onDelete'],   ['id' => '{id}']);
      //  $action4 = new TDataGridAction([$this, 'onImprimePedido'],   ['key' => '{id}']);
        $action5 = new TDataGridAction([$this, 'onEnviarCotacao'],   ['id' => '{id}']);
    //    $action6 = new TDataGridAction(['CotacaoPendenteList', 'onSetProject'],   ['id' => '{id}']);
   //     $action7 = new TDataGridAction(['CotacaoPendenteList', 'onSetProject'],   ['id' => '{id}']);
        $action8 = new TDataGridAction([$this, 'onCancelarPedido'],   ['id' => '{id}']);
         if (TSession::getValue('aprovacao_por_item')==2) {
            $action9 = new TDataGridAction([$this, 'onGerarFinanceiro'],   ['id' => '{id}']);
        } else {
            $action9 = new TDataGridAction([$this, 'onGerarFinanceiroItem'],   ['id' => '{id}']);
        }
        $action10 = new TDataGridAction([$this, 'onFinalizarPedido'],   ['id' => '{id}']);
         $action11 = new TDataGridAction(['DocumentosCotacaoSimpleList', 'onSetProject'],   ['id' => '{id}']);
        $action12 = new TDataGridAction([$this, 'onCancelarAprovacao'],   ['id' => '{id}']);
    //    $action13 = new TDataGridAction([$this, 'onImprimeCotacao'],   ['id' => '{id}']);
         $action14 = new TDataGridAction([$this, 'onExibirDetalhe'],   ['id' => '{id}']);

        //  $action15 = new TDataGridAction([$this, 'onExibirDetalhe'],   ['id' => '{id}']);

        // $action14 = new TDataGridAction([$this, 'onExibirDetalhe'], ['id' => '{id}']);

        $action1->setLabel('Visualizar Pedido');
        $action1->setImage('fas:search-plus #673AB7');
        $action1->setDisplayCondition('PedidoVendaList::onExibirView');

        $action2->setLabel('Editar');
        $action2->setImage('far:edit #478fca');
        $action2->setDisplayCondition('PedidoVendaList::onExibirEditar');

        $action3->setLabel('Excluir');
        $action3->setImage('fas:trash-alt #dd5a43');
        $action3->setDisplayCondition('PedidoVendaList::onExibirExcluir');

    //    $action4->setLabel('Documento Pedido');
 //       $action4->setImage('far:file-pdf #000000');

   //     $action13->setLabel('Documento Cotação');
 //       $action13->setImage('fas:file-pdf #F44336');
   //     $action13->setDisplayCondition('PedidoVendaList::onExibirDocCotacao');

        $action5->setLabel('Gerar Cotação');
        $action5->setImage('fas:envelope #E91E63');
        $action5->setDisplayCondition('PedidoVendaList::onExibirEnvio');

      /*  $action6->setLabel('Aprovar');
        $action6->setImage('fas:thumbs-up #9C27B0');
        $action6->setDisplayCondition('PedidoVendaList::onExibirAprovar');

        $action7->setLabel('Reprovar');
        $action7->setImage('fas:thumbs-down #F44336');
        $action7->setDisplayCondition('PedidoVendaList::onExibirReprovar');*/

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

        $action14->setLabel('Detalhes Cotação');
        $action14->setImage('fas:plus #69aa46');
        $action14->setDisplayCondition('PedidoVendaList::onExibirCotacao');

        $action_group = new TDataGridActionGroup('Clique Ações ', 'fa:th red');

        $action_group->addAction($action14);
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action3);
     //   $action_group->addAction($action4);
   //     $action_group->addAction($action13);
        $action_group->addAction($action5);
      //  $action_group->addAction($action6);
      //  $action_group->addAction($action7);
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

        $action_onEdit = new TDataGridAction(array('PedidoVendaForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);
        $action_onEdit->setDisplayCondition('PedidoVendaList::onExibirEditar');

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('PedidoVendaList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);
        $action_onDelete->setDisplayCondition('PedidoVendaList::onExibirExcluir');

        $this->datagrid->addAction($action_onDelete);

        $action_onImprimePedido = new TDataGridAction(array('PedidoVendaList', 'onImprimePedido'));
        $action_onImprimePedido->setUseButton(false);
        $action_onImprimePedido->setButtonClass('btn btn-default btn-sm');
        $action_onImprimePedido->setLabel("Documento Pedido");
        $action_onImprimePedido->setImage('far:file-pdf #000000');
        $action_onImprimePedido->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onImprimePedido);

        $action_onImprimeCotacao = new TDataGridAction(array('PedidoVendaList', 'onImprimeCotacao'));
        $action_onImprimeCotacao->setUseButton(false);
        $action_onImprimeCotacao->setButtonClass('btn btn-default btn-sm');
        $action_onImprimeCotacao->setLabel("Documento Fornecedor");
        $action_onImprimeCotacao->setImage('fas:file-pdf #F44336');
        $action_onImprimeCotacao->setField(self::$primaryKey);
        $action_onImprimeCotacao->setDisplayCondition('PedidoVendaList::onExibirDocCotacao');

        $this->datagrid->addAction($action_onImprimeCotacao);

        $action_onEnviarCotacao = new TDataGridAction(array('PedidoVendaList', 'onEnviarCotacao'));
        $action_onEnviarCotacao->setUseButton(false);
        $action_onEnviarCotacao->setButtonClass('btn btn-default btn-sm');
        $action_onEnviarCotacao->setLabel("Gerar Cotação");
        $action_onEnviarCotacao->setImage('fas:envelope #E91E63');
        $action_onEnviarCotacao->setField(self::$primaryKey);
        $action_onEnviarCotacao->setDisplayCondition('PedidoVendaList::onExibirEnvio');

        $this->datagrid->addAction($action_onEnviarCotacao);

        $action_onSetProject = new TDataGridAction(array('CotacaoPendenteList', 'onSetProject'));
        $action_onSetProject->setUseButton(false);
        $action_onSetProject->setButtonClass('btn btn-default btn-sm');
        $action_onSetProject->setLabel("");
        $action_onSetProject->setImage('fas:thumbs-up #9C27B0');
        $action_onSetProject->setField(self::$primaryKey);
        $action_onSetProject->setDisplayCondition('PedidoVendaList::onExibirAprovar');

        $this->datagrid->addAction($action_onSetProject);

        $action_CotacaoPendenteList_onSetProject = new TDataGridAction(array('CotacaoPendenteList', 'onSetProject'));
        $action_CotacaoPendenteList_onSetProject->setUseButton(false);
        $action_CotacaoPendenteList_onSetProject->setButtonClass('btn btn-default btn-sm');
        $action_CotacaoPendenteList_onSetProject->setLabel("Reprovar");
        $action_CotacaoPendenteList_onSetProject->setImage('fas:thumbs-down #F44336');
        $action_CotacaoPendenteList_onSetProject->setField(self::$primaryKey);
        $action_CotacaoPendenteList_onSetProject->setDisplayCondition('PedidoVendaList::onExibirReprovar');

        $this->datagrid->addAction($action_CotacaoPendenteList_onSetProject);

        $action_onCancelarPedido = new TDataGridAction(array('PedidoVendaList', 'onCancelarPedido'));
        $action_onCancelarPedido->setUseButton(false);
        $action_onCancelarPedido->setButtonClass('btn btn-default btn-sm');
        $action_onCancelarPedido->setLabel("Cancelar pedido");
        $action_onCancelarPedido->setImage('fas:times-circle #E91E63');
        $action_onCancelarPedido->setField(self::$primaryKey);
        $action_onCancelarPedido->setDisplayCondition('PedidoVendaList::onExibirCancelado');

        $this->datagrid->addAction($action_onCancelarPedido);

        $action_onGerarFinanceiro = new TDataGridAction(array('PedidoVendaList', 'onGerarFinanceiro'));
        $action_onGerarFinanceiro->setUseButton(false);
        $action_onGerarFinanceiro->setButtonClass('btn btn-default btn-sm');
        $action_onGerarFinanceiro->setLabel("Aprovar pagamento");
        $action_onGerarFinanceiro->setImage('fas:money-bill-wave #FFA500');
        $action_onGerarFinanceiro->setField(self::$primaryKey);
        $action_onGerarFinanceiro->setDisplayCondition('PedidoVendaList::onExibirGerarFinanceiro');

        $this->datagrid->addAction($action_onGerarFinanceiro);

        $action_onFinalizarPedido = new TDataGridAction(array('PedidoVendaList', 'onFinalizarPedido'));
        $action_onFinalizarPedido->setUseButton(false);
        $action_onFinalizarPedido->setButtonClass('btn btn-default btn-sm');
        $action_onFinalizarPedido->setLabel("Finalizar pedido");
        $action_onFinalizarPedido->setImage('fas:door-closed #009688');
        $action_onFinalizarPedido->setField(self::$primaryKey);
        $action_onFinalizarPedido->setDisplayCondition('PedidoVendaList::onExibirFinalizar');

        $this->datagrid->addAction($action_onFinalizarPedido);

        $action_DocumentosCotacaoPedidoList_onSetProject = new TDataGridAction(array('DocumentosCotacaoPedidoList', 'onSetProject'));
        $action_DocumentosCotacaoPedidoList_onSetProject->setUseButton(false);
        $action_DocumentosCotacaoPedidoList_onSetProject->setButtonClass('btn btn-default btn-sm');
        $action_DocumentosCotacaoPedidoList_onSetProject->setLabel("Anexos");
        $action_DocumentosCotacaoPedidoList_onSetProject->setImage('fas:paperclip #795548');
        $action_DocumentosCotacaoPedidoList_onSetProject->setField(self::$primaryKey);
        $action_DocumentosCotacaoPedidoList_onSetProject->setDisplayCondition('PedidoVendaList::onExibirAnexos');

        $this->datagrid->addAction($action_DocumentosCotacaoPedidoList_onSetProject);

        $action_onCancelarAprovacao = new TDataGridAction(array('PedidoVendaList', 'onCancelarAprovacao'));
        $action_onCancelarAprovacao->setUseButton(false);
        $action_onCancelarAprovacao->setButtonClass('btn btn-default btn-sm');
        $action_onCancelarAprovacao->setLabel("Cancelar Aprovação");
        $action_onCancelarAprovacao->setImage('fas:undo #009688');
        $action_onCancelarAprovacao->setField(self::$primaryKey);
        $action_onCancelarAprovacao->setDisplayCondition('PedidoVendaList::onExibirCancelarAprovacao');

        $this->datagrid->addAction($action_onCancelarAprovacao);

*/

        // create the datagrid model
        $this->applyDatagridProperties();

        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de pedidos {$manual}");
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

        $button_cadastrar = new TButton('button_button_cadastrar');
        $button_cadastrar->setAction(new TAction(['PedidoVendaForm', 'onShow']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['PedidoVendaList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['PedidoVendaList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['PedidoVendaList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['PedidoVendaList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['PedidoVendaList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['PedidoVendaList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['PedidoVendaList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_cadastrar);
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
              if (!empty($AlertMensagem)) {
                $container->add($TAlert);
           } 
       //     $container->add(TBreadCrumb::create(["Compras","Pedidos de venda"]));
        }

        $container->add($panel);

        parent::add($container);

    }
    public static function onExibirView($object)
    {

        try 
        {
            $pes1 = Pessoa::where('system_user_id','=',TSession::getValue('userid'))
            ->load();
            if ($pes1) {
                return false;
            } else {
                return true;
            }
        
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }

    }

    public static function onExibirEditar($object)
    {

        try 
        {
            if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::PENDENTE, EstadoPedido::ENVIADO, EstadoPedido::COMPROPOSTA]) )
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
    public function onDelete($param = null) 
    { 
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);
                $conn = TConnection::open('minierp');

                // instantiates object
                $object = new Pedido($key, FALSE); 

                // deletes the object from the database
                $object->delete();

                $sql = 'delete FROM itens_pedido where pedido_venda_id  =  ' . $param['key'];
                $Recordsudu = $conn->query($sql);

                $sql = 'delete FROM pedido_seguimento where pedido_id  =  ' . $param['key'];
                $Recordsudu = $conn->query($sql);

                $sql = 'delete FROM cidade_pedido where pedido_id  =  ' . $param['key'];
                $Recordsudu = $conn->query($sql);

                $sql = 'delete FROM documentos_pedido where pedido_id  =  ' . $param['key'];
                $Recordsudu = $conn->query($sql);

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
    }
    public static function onExibirExcluir($object)
    {
        try 
        {
            if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::PENDENTE]) )
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
    public function onImprimePedido($param = null) 
    {
        try 
        {
             //code here
             $data = $this->form->getData();
             //code here
             TTransaction::open('minierp');

             $conn = TConnection::open('minierp');

             //code here
             $pdf = new FPDF("L","pt","A4");

             $pedido = new Pedido($param['key']);                   

             $linha=0;   
             $pag=1;
             $alturalinha=255;
             $unidade='';
             $qt = 0;
             $vl = 0;
             $vlt = 0;
             $qtitens=0;

             $itenspedido = ItensPedido::where('pedido_venda_id','=',$pedido->id)
                                        ->load();
             if ($itenspedido) {
                 $gruposItens = [
                    'produtos' => ['titulo' => 'PRODUTOS', 'itens' => []],
                    'servicos' => ['titulo' => 'SERVICOS', 'itens' => []],
                 ];

                 foreach ($itenspedido as $itemPedido)
                 {
                    $produtoItem = new Produto($itemPedido->produto_id);
                    $tipoProdutoId = (int) ($produtoItem->tipo_produto_id ?? 0);
                    $chaveGrupo = ($tipoProdutoId === 2) ? 'servicos' : 'produtos';
                    $gruposItens[$chaveGrupo]['itens'][] = [
                        'item' => $itemPedido,
                        'produto' => $produtoItem
                    ];
                 }

                 //cabecalho
                 if ( ($linha==0) || ($linha >= 25) ){
                   $this->cabecalhoDocPedido($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido);
                   $pag=$pag + 1; 
                   $alturalinha = 255;
                   $linha = 12;
                }
                 foreach (['produtos', 'servicos'] as $grupoKey) {
                     if (empty($gruposItens[$grupoKey]['itens'])) {
                         continue;
                     }

                     if ( ($linha==0) || ($linha >= 25) ){
                        $this->cabecalhoDocPedido($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido);
                        $linha = 12;
                        $pag=$pag + 1; 
                        $alturalinha = 255;
                     }

                     $pdf->SetFont('arial','B',9);
                     $pdf->SetFillColor(220, 226, 230);
                     $pdf->Rect(26, $alturalinha-5, 542, 15, 'F');
                     $pdf->SetXY(30,$alturalinha);
                     $pdf->Cell(200,5,utf8_decode('ITENS - '.$gruposItens[$grupoKey]['titulo']),0,1,'L');
                     $alturalinha += 18;
                     $linha += 1;

                     $qtGrupo = 0;
                     $vlGrupo = 0;
                     $vltGrupo = 0;
                     $qtitensGrupo = 0;

                     foreach ($gruposItens[$grupoKey]['itens'] as $registro) {
                     $itens = $registro['item'];
                     $produto = $registro['produto'];
                     //detalhes
                     if ( ($linha==0) || ($linha >= 25) ){
                        $this->cabecalhoDocPedido($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido);
                       $linha = 12;
                       $pag=$pag + 1; 
                       $alturalinha = 255;

                       $pdf->SetFont('arial','B',9);
                       $pdf->SetFillColor(220, 226, 230);
                       $pdf->Rect(26, $alturalinha-5, 542, 15, 'F');
                       $pdf->SetXY(30,$alturalinha);
                       $pdf->Cell(200,5,utf8_decode('ITENS - '.$gruposItens[$grupoKey]['titulo'].' (continua)'),0,1,'L');
                       $alturalinha += 18;
                       $linha += 1;
                     }

                     $pdf->setFont('arial','',8);   
                     $pdf->SetXY(25,$alturalinha);
                     $pdf->Cell(70,5,utf8_decode($produto->id),0,1,'L');

                     $tamanho = strlen($produto->nome);
                      if ($tamanho>=71) {
                        $pdf->SetXY(53,$alturalinha-2);
                        $pdf->MultiCell(340,10,substr(utf8_decode($produto->nome),0,255),0,'L',false);
                        $pdf->setFont('arial','',8);   
                        $pdf->SetXY(350,$alturalinha);
                        $pdf->Cell(70,5,$produto->unidade_medida->nome,0,1,'C');
                        $pdf->SetXY(380,$alturalinha);
                        $pdf->Cell(70,5,$itens->quantidade,0,1,'C');
                        $pdf->SetXY(420,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($itens->valor, 2),0,1,'R');
                        $pdf->SetXY(500,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($itens->valor_total, 2),0,1,'R');
                        $alturalinha += 24;
                      } else {
                        $pdf->SetXY(53,$alturalinha);
                        $pdf->MultiCell(340,5,utf8_decode($produto->nome),0,'L', false);
                        $pdf->setFont('arial','',8);   
                        $pdf->SetXY(350,$alturalinha);
                        $pdf->Cell(70,5,$produto->unidade_medida->nome,0,1,'C');
                        $pdf->SetXY(380,$alturalinha);
                        $pdf->Cell(70,5,$itens->quantidade,0,1,'C');
                        $pdf->SetXY(420,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($itens->valor, 2),0,1,'R');
                        $pdf->SetXY(500,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($itens->valor_total, 2),0,1,'R');
                      }

                     $alturalinha += 15; 
                     $linha +=1;

                     $pdf->ln(1); 
                     $qtitens++;
                     $qt += $itens->quantidade;
                     $vl += $itens->valor;
                     $vlt += $itens->valor_total;
                     $qtitensGrupo++;
                     $qtGrupo += $itens->quantidade;
                     $vlGrupo += $itens->valor;
                     $vltGrupo += $itens->valor_total;
                 }

                     $alturalinha += 5;
                     $pdf->SetFont('arial','B',8);
                     $pdf->SetFillColor(245,247,248);
                     $pdf->Rect(26, $alturalinha-5, 542, 15, 'F');
                     $pdf->SetXY(30,$alturalinha);
                     $pdf->Cell(220,5,utf8_decode('Subtotal '.$gruposItens[$grupoKey]['titulo'].' ('.$qtitensGrupo.' itens)'),0,1,'L');
                     $pdf->SetXY(360,$alturalinha);
                     $pdf->Cell(70,5,$qtGrupo,0,1,'C');
                     $pdf->SetXY(420,$alturalinha);
                     $pdf->Cell(70,5,'R$ '.number_format($vlGrupo, 2),0,1,'R');
                     $pdf->SetXY(500,$alturalinha);
                     $pdf->Cell(70,5,'R$ '.number_format($vltGrupo, 2),0,1,'R');
                     $alturalinha += 18;
                     $linha += 1;
                 }
                 $alturalinha+=15;
                 //rodape
                 $pdf->Cell(0,25,"","B",1,'C');
                 $pdf->SetFont('arial','B',10); 
                 $pdf->SetXY(25,$alturalinha);
                 $pdf->SetFillColor(235,239,240);
                 $pdf->Rect(26, $alturalinha-5, 542, 15, 'F');
                 $pdf->Cell(70,5,utf8_decode('Total Geral '.$qtitens.' Itens'),0,1,'L');

                 $pdf->SetXY(360,$alturalinha);
                 $pdf->Cell(70,5,$qt,0,1,'C');
                 $pdf->SetXY(420,$alturalinha);
                 $pdf->Cell(70,5,'R$ '.number_format($vl, 2),0,1,'R');
                 $pdf->SetXY(500,$alturalinha);
                 $pdf->Cell(70,5,'R$ '.number_format($vlt, 2),0,1,'R');

             }

              $nome = 'documentopedido.pdf';

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
          //   new TMessage('info', 'Pedidos gerado com sucesso. Por favor, habilite popups no navegador.');

             // fill the form with the active record data
             $this->form->setData($data);
             TTransaction::close();

             //</autoCode>
            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onExibirCotacao($object)
    {
        try
        {
            if (in_array(EstadoPedido::VISUALIZARCOTACAO, Aprovador::getEstadosDisponiveis()))
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

             $pedido = new Pedido($param['id']);

             $objects = Cotacao::where('pessoa_id','=',$pedido->cliente_id)
                               ->where('pedido_id','=',$pedido->id)
                               ->load();
            if ($objects){
                foreach ($objects as $obj) {

                }
            }

             $linha=0;   
             $pag=1;
             $alturalinha=255;
             $unidade='';
             $qt = 0;
             $vl = 0;
             $vlt = 0;
             $qtitens=0;

             $itenscotacao = ItensCotacao::where('cotacao_id','=',$obj->id)
                                        ->load();
             if ($itenscotacao) {
                 //cabecalho
                 if ( ($linha==0) || ($linha >= 25) ){
                   $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id);
                   $pag=$pag + 1; 
                   $alturalinha = 255;
                   $linha = 12;
                }
                 foreach ($itenscotacao as $itens) {
                     //detalhes
                     if ( ($linha==0) || ($linha >= 25) ){
                        $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id);
                       $linha = 12;
                       $pag=$pag + 1; 
                       $alturalinha = 255;
                     }
                     $produto = new Produto($itens->produto_id);

                     $pdf->setFont('arial','',8);   
                     $pdf->SetXY(25,$alturalinha);
                     $pdf->Cell(70,5,utf8_decode($produto->id),0,1,'L');

                     $tamanho = strlen($produto->nome);
                      if ($tamanho>=71) {
                        $pdf->SetXY(45,$alturalinha-2);
                        $pdf->MultiCell(340,10,substr(utf8_decode($produto->nome),0,255),0,'L',false);
                        $pdf->setFont('arial','',8);   
                        $pdf->SetXY(360,$alturalinha);
                        $pdf->Cell(70,5,$itens->qtde,0,1,'C');
                        $pdf->SetXY(420,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($itens->valor, 2),0,1,'R');
                        $pdf->SetXY(500,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($itens->valor_total, 2),0,1,'R');
                        $alturalinha += 24;
                      } else {
                        $pdf->SetXY(45,$alturalinha);
                        $pdf->MultiCell(340,5,utf8_decode($produto->nome),0,'L', false);
                        $pdf->setFont('arial','',8);   
                        $pdf->SetXY(360,$alturalinha);
                        $pdf->Cell(70,5,$itens->qtde,0,1,'C');
                        $pdf->SetXY(420,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($itens->valor, 2),0,1,'R');
                        $pdf->SetXY(500,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($itens->valor_total, 2),0,1,'R');
                      }

                     $alturalinha += 15; 
                     $linha +=1;

                     $pdf->ln(1); 
                     $qtitens++;
                     $qt += $itens->qtde;
                     $vl += $itens->valor;
                     $vlt += $itens->valor_total;
                 }
                 $alturalinha+=15;
                 //rodape
              //   $pdf->SetXY(25,$alturalinha);
                 $pdf->Cell(0,15,"","B",1,'C');
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
                 $pdf->SetXY(500,$alturalinha);
                 $pdf->Cell(70,5,'R$ '.number_format($vlt, 2),0,1,'R');

                 $pdf->Cell(0,15,"","B",1,'C');

                 $pdf->SetFont('arial','I',10); 
                 $alturalinha+=26;
                 
                 $pdf->SetXY(82,$alturalinha);
                 $pdf->Cell(70,5,'Valor Bruto: '.'R$ '.number_format($obj->valor_total, 2),0,1,'R');
                                     
                 $pdf->SetXY(320,$alturalinha);
                
                 $taxas = ((TSession::getValue('taxacontrato'))) ;
                 $pdf->Cell(70,5,'Valor Desconto: ('.number_format($taxas, 2).'%)'.' R$ '.number_format($obj->valor_desconto, 2),0,1,'R');
                 
                 $pdf->SetXY(500,$alturalinha);
                 $pdf->Cell(70,5,'Valor Liquido: '.'R$ '.number_format($obj->valor_liquido, 2),0,1,'R');
                 
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
             new TMessage('info', 'Pedidos gerado com sucesso. Por favor, habilite popups no navegador.');

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
    public static function onExibirDocCotacao($object)
    {
        try 
        {
            if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::APROVADO, EstadoPedido::FINALIZADO, EstadoPedido::PGTOAPROVADO, EstadoPedido::ENTREGUE]) )
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
    public function onEnviarCotacao($param = null) 
    {

            if (isset($param['confirmEnviarCotacao']) && $param['confirmEnviarCotacao']) {
            try {
                TTransaction::open(self::$database);
                $conexao   = TTransaction::get(); 
                //$conexao->exec( "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));" );

                $pedido = new Pedido($param['id'], false);

                $pessoass = Pessoa::where('system_user_id','=',TSession::getValue('userid'))
                                  ->load();
                if ($pessoass) {
                    foreach ($pessoass as $pessoasss) {
                    
                    }
                    
                }
                $repository = new TRepository('PedidocompraAsCliente'); 
                $criteria = new TCriteria;

                if (!$pessoass) {
                    $criteria->add(new TFilter('pedido_id', '=', $pedido->id), TExpression::AND_OPERATOR);
                    $fornecedores = $repository->load($criteria);
                } else {
                    $criteria->add(new TFilter('pedido_id', '=', $pedido->id), TExpression::AND_OPERATOR);
                    $criteria->add(new TFilter('pessoa_id', '=', $pessoasss->id), TExpression::AND_OPERATOR);
                    $fornecedores = $repository->load($criteria);
                }
                
                
                if ($fornecedores) {

                    $this->gerarCotacoes($fornecedores, $pedido);

                    if (in_array($pedido->estado_pedido_venda_id, [EstadoPedido::PENDENTE, EstadoPedido::ENVIADO]) ){
                        
                        // Atualiza o status do pedido e registra histórico
                        //var_dump($criteria);
                        $pedido->estado_pedido_venda_id = EstadoPedido::ENVIADO;
                        $pedido->store();

                        $this->registrarHistoricoPedido($pedido);

                        //   $this->atualizaDetalhesPedido($pedido);
                    }

                    TToast::show('success', "Emails enviados!!", 'topRight', 'far:check-circle');
                    TApplication::loadPage('PedidoVendaList', 'onSetProject');
                } else {
                    new TMessage('info', 'Sr(a) Usuario checar as Redes que deseja enviar as cotacoes!');
                }
                $this->form->setData($pedido); 
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onEnviarCotacao'));
            $action->setParameters($param);
            $action->setParameter('confirmEnviarCotacao', true);

            new TQuestion('Tem certeza que deseja Gerar a Cotaçao?', $action);
        }
/*
        try 
        {
            //code here

*/
            //</autoCode>
  /*      }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }
    public static function onExibirEnvio($object)
    {
        try 
        {
            if (in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::PENDENTE, EstadoPedido::ENVIADO, EstadoPedido::COMPROPOSTA]) )
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
                $pedido->cliente_id=null;
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
                TApplication::loadPage('PedidoVendaList', 'onSetProject');
                $this->form->setData($pedido); 
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

             if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::PENDENTE, EstadoPedido::NAOENVIADO, EstadoPedido::ENVIADO, EstadoPedido::APROVADO, EstadoPedido::COMPROPOSTA]) ) 
            {
                if (!in_array(EstadoPedido::CANCELADO, Aprovador::getEstadosDisponiveis()))
                {
                   return false;
                } else {
                    return true;

                }
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
                $taxacontrato = TSession::getValue('taxacontrato');
                $vertxpessoa = new Pessoa($pedido->cliente_id);

                $conta = new Conta();
                $conta->pessoa_id            = $pedido->cliente_id;
                $conta->forma_pagamento_id   = 1; //dinheiro 2024-01-01
                $conta->pedido_venda_id      = $pedido->id;
                $conta->dt_emissao           = date('Y-m-d');
                $conta->dt_vencimento        = self::calcularVencimentoFinanceiro($conta->dt_emissao);
                $conta->mes_vencimento       = intval(substr($conta->dt_vencimento,5,2));
                $conta->ano_vencimento       = intval(substr($conta->dt_vencimento,0,4));
                $conta->ano_mes_vencimento   = intval(substr($conta->dt_vencimento,0,4).substr($conta->dt_vencimento,5,2));
                $conta->valor                = $pedido->valor_total_cotacao;
                $conta->valor_txcontrato       = $pedido->valor_desconto_cotacao;

           
                //pessoa
                $pessoass = new Pessoa($pedido->cliente_id);
            
                $taxaspessoa = TaxasPessoa::where('pessoa_id','=',$pessoass->id)
                                        ->where('entidade_id','=',TSession::getValue('entidade'))
                                        ->where('system_unit_id','=',TSession::getValue('idunit'))
                                        ->load();
                if ($taxaspessoa) {
                    foreach ($taxaspessoa as $tx) {
                        $taxaadm = $tx->taxaadm;
                        break;
                    }
                } else {
                $taxaadm=0;
                }


                //calculo taxa administracao
                $valortaxaadm = ($conta->valor - $conta->valor_txcontrato) * ($taxaadm/100);
                $conta->valor_txadm           = $valortaxaadm; 
                $conta->valor_txbancaria     = 0;
                $conta->valor_txantecipacao  = 0;
                $conta->valor_liquido        = ($conta->valor-($pedido->valor_desconto_cotacao+$conta->valor_txadm));
                $conta->parcela              = 1;
                $conta->descricao            = $pedido->descricaopedido;        
                $conta->tipo_conta_id        = TipoConta::PAGAR;
                $conta->mes_emissao          = intval(substr($conta->dt_emissao,5,2));
                $conta->ano_emissao          = intval(substr($conta->dt_emissao,0,4));
                $conta->mes_ano_emissao      = intval(substr($conta->dt_emissao,0,4).substr($conta->dt_emissao,5,2));
               $conta->departamento_unit_id = $pedido->departamento_unit_id;
                        $conta->system_users_id      = $pedido->system_users_id;
                        $conta->entidade_id        = $pedido->entidade_id;
                        $conta->system_unit_id      = $pedido->system_unit_id;
                                                $conta->pedido_venda_id      = $pedido->id;

                $conta->store();

                $pedido->estado_pedido_venda_id = EstadoPedido::PGTOAPROVADO;
                $pedido->store();

                $this->registrarHistoricoPedidoAprovado($pedido);

                TToast::show('success', "Pagamento programado com sucesso!!! Consulte financeiro", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoVendaList', 'onSetProject');
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
     public function onGerarFinanceiroItem($param = null) 
    {
       if (isset($param['confirmagerarfinanceiroitem']) && $param['confirmagerarfinanceiroitem']) {
            try {
                 TTransaction::open(self::$database);
                //$this->form->setData($pedido); 
                $pedido = new Pedido($param['key']);

                $cot = Cotacao::where('pedido_id','=',$pedido->id)
                               ->load();
                if ($cot) {
                   foreach($cot as $cotacao){
                     if ($cotacao->estado_pedido_id==EstadoPedido::ENTREGUE) {
                        $valoritens=0;$valordesconto=0;$valortotal=0;
                        $itenscotacao = ItensCotacao::where('cotacao_id','=',$cotacao->id)
                                                        ->load();

                         
                        if ($itenscotacao) {
                            foreach ($itenscotacao as $itensc) {
                                  $valoritens += ($itensc->valor*$itensc->qtde);
                                
                                $valortotal += ($itensc->valor_total);
                            }
                        }
               
                         $txcontrato = ((TSession::getValue('taxacontrato'))) ;

                        $conta = new Conta();
                        $conta->pessoa_id            = $cotacao->pessoa_id;
                        $conta->forma_pagamento_id   = 1; //dinheiro 2024-01-01
                        $conta->dt_emissao           = date('Y-m-d');
                        $conta->dt_vencimento        = self::calcularVencimentoFinanceiro($conta->dt_emissao);
                        $conta->mes_vencimento       = intval(substr($conta->dt_vencimento,5,2));
                        $conta->ano_vencimento       = intval(substr($conta->dt_vencimento,0,4));
                        $conta->ano_mes_vencimento   = intval(substr($conta->dt_vencimento,0,4).substr($conta->dt_vencimento,5,2));
                        $conta->valor                =  $valoritens;
                        $conta->valor_txcontrato       =  ($valortotal*($txcontrato/100));

                          $conta->valor_liquido        = $valortotal - $conta->valor_txcontrato ;
                        $conta->parcela              = 1;
                        $conta->descricao            = $pedido->descricaopedido;        
                        $conta->tipo_conta_id        = TipoConta::PAGAR;
                        $conta->mes_emissao          = intval(substr($conta->dt_emissao,5,2));
                        $conta->ano_emissao          = intval(substr($conta->dt_emissao,0,4));
                        $conta->mes_ano_emissao      = intval(substr($conta->dt_emissao,0,4).substr($conta->dt_emissao,5,2));
                        $conta->departamento_unit_id = $pedido->departamento_unit_id;
                        $conta->system_users_id      = $pedido->system_users_id;
                        $conta->entidade_id        = $pedido->entidade_id;
                        $conta->system_unit_id      = $pedido->system_unit_id;
                        $conta->pedido_venda_id      = $pedido->id;
                        $conta->store();
                           $this->registrarHistoricoCotacaoAprovado($cotacao);

                           $cotacao->estado_pedido_id = EstadoPedido::PGTOAPROVADO;
                        $cotacao->store();
                        
                    }

                }
    
                

                    $pedido->estado_pedido_venda_id = EstadoPedido::PGTOAPROVADO;
                    $pedido->store();

                    $this->registrarHistoricoPedidoAprovado($pedido);


                     }
             
                

              


                TToast::show('success', "Pagamento programado com sucesso!!! Consulte financeiro", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoVendaList', 'onShow', ['key' => $pedido->id]);
                $this->form->setData($pedido); 
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onGerarFinanceiroItem'));
            $action->setParameters($param);
            $action->setParameter('confirmagerarfinanceiroitem', true);

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
    // public static function onExibirGerarFinanceiro($object)
    // {
    //     try 
    //     {

    //          if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::APROVADO]) )
    //         {
    //             return true;
    //         }

    //         return false;
    //     }
    //     catch (Exception $e) 
    //     {
    //         new TMessage('error', $e->getMessage());    
    //     }
    // }

     public static function onExibirGerarFinanceiro($object)
    {
        try 
        {

            if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::ENTREGUE]) )
            {
                TTransaction::open(self::$database);
                $cotacaos = Cotacao::where('pedido_id', '=', $object->id)
                                      ->load();
                foreach ($cotacaos as $cotacao) {
                    if ($cotacao->estado_pedido_id == EstadoPedido::APROVADO) {
                        TTransaction::close();
                        return false;
                    }
                }
                TTransaction::close();

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

                // Atualiza o status do pedido e registra histórico
                $pedido = new Pedido($param['id'], false);
                $pedido->estado_pedido_venda_id = EstadoPedido::FINALIZADO;
                $pedido->dt_finalizacao = date('Y-m-d');
                $pedido->store();

                $this->registrarHistoricoPedidoFinalizar($pedido);

                $cot = Cotacao::where('estado_pedido_id','=',EstadoPedido::PGTOAPROVADO)
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
                TApplication::loadPage('PedidoVendaList', 'onShow', ['key' => $pedido->id]);
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
              if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::PGTOAPROVADO, EstadoPedido::FINALIZADO,EstadoPedido::APROVADO, EstadoPedido::ENTREGUE ]) )
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
                $pedido->cliente_id = null;
                $pedido->store();

                $this->registrarHistoricoPedidocomproposta($pedido);

                $cotacao = Cotacao::where('pedido_id','=',$pedido->id)
                                  ->where('estado_pedido_id','=',EstadoPedido::APROVADO)
                                  ->load();
                if ($cotacao){
                    foreach ($cotacao as $cot) 
                    {
                        $cot->estado_pedido_id = EstadoPedido::AGUARDANDO;
                        $cot->store();
                        $this->registrarHistoricoCotacaoAguardando($cot);
                        $itens = ItensCotacao::where('cotacao_id','=',$cot->id)->load();
                        if ($itens) {
                            foreach ($itens as $item) {
                                $item->estado_pedido_id = null;
                                $item->store();
                            }
                        }
                    }
                }
                // // retirar itens da tabela manutencao_garantia
                // $manutencao_garantia = ManutencaoGarantia::where('pedido_frotas_id','=',$pedido->id)->load();
                // if ($manutencao_garantia) {
                //     foreach ($manutencao_garantia as $mg) {
                //         $mg->delete();
                //     }
                // }
                

                TTransaction::close();
                TToast::show('success', "Cancelamento da aprovação feito com sucesso!!!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoVendaList', 'onSetProject');
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

                            if(($column_name == 'cidade_id') || ($column_name == 'descricaopedido') || ($column_name == 'cliente->nome') || ($column_name == 'system_users->name')){

                                if($column_name == 'cidade_id'){
                                    $cidade = new Cidade($object->$column_name);
                                    $estado = new Estado($cidade->estado_id);
                                    $row[] = mb_convert_encoding($cidade->nome.'-'.$estado->sigla, 'ISO-8859-1', 'UTF-8');

                                } else{
                                    $row[] = mb_convert_encoding($object->$column_name, 'ISO-8859-1', 'UTF-8');

                                }

                            }   
                            else{ 

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
                            //var_dump($column);

                                if(($column_name == 'cidade_id') || ($column_name == 'descricaopedido') || ($column_name == 'cliente->nome') || ($column_name == 'system_users->name')){

                                    if($column_name =='cidade_id'){
                                    $cidade = new Cidade($object->$column_name);
                                    $estado = new Estado($cidade->estado_id);

                                    $value = mb_convert_encoding($cidade->nome.'-'.$estado->sigla, 'ISO-8859-1', 'UTF-8');

                                    } else{
                                    $value = mb_convert_encoding($object->$column_name, 'ISO-8859-1', 'UTF-8');
                                    }

                                } else{

                                    $value = '';
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

                                }
                                $table->addCell($value, 'center', 'data');  
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
                $contents = file_get_contents('app/resources/styles-print.html') . file_get_contents('app/resources/styles-print-list.html') . PdfListHeader::render(__CLASS__) . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'landscape');
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
            $pdf = new FPDF("L","pt","A4");

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
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
            $pes1 = Pessoa::where('system_user_id','=',TSession::getValue('userid'))
            ->load();
            if ($pes1) {
            foreach ($pes1 as $pessoass) {

            }
            $criteria->add(new TFilter('cliente_id', '=', $pessoass->id), TExpression::AND_OPERATOR);
            }
            // load the objects according to criteria
            $objects = $repository->load($criteria);

            $linha          = 0;
            $pag            = 1; 
            $alturalinha    = 94;
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
               if ( ($linha==0) || ($linha >= 39) ){
                  $this->cabecalho($pdf, $linha,$pag,$unidade,$cnpj,$filters);
	              $linha = 0;
	              $pag=$pag + 1; 
	              $alturalinha = 94;
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
               if ($pessoa->nome=='') {
                  $pdf->Cell(70,5,'',0,1,'L');
               } else {
                   $pdf->Cell(70,5,mb_convert_encoding(substr($pessoa->nome,0,60), 'ISO-8859-1', 'UTF-8'),0,1,'L');
               }
               //$pdf->Cell(70,5,$pessoa->nome,0,1,'L');

               $pdf->SetXY(225,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding(substr($object->descricaopedido,0,60), 'ISO-8859-1', 'UTF-8'),0,1,'L');

               $estadopedido = new EstadoPedido($object->estado_pedido_venda_id);
               $pdf->SetXY(442,$alturalinha);
               $pdf->Cell(70,5,$estadopedido->nome,0,1,'L');

               $pdf->SetXY(495,$alturalinha);
               $pdf->Cell(70,5,number_format($object->valor_total, 2),0,1,'R');

               $pdf->SetXY(550,$alturalinha);
               $pdf->Cell(70,5,number_format($object->valor_liquido_cotacao, 2),0,1,'R');
               $pdf->setFont('arial','',5);
               $dep = new DepartamentoUnit($object->departamento_unit_id);      
               $pdf->SetXY(620,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding(substr(strtoupper($dep->name),0,36), 'ISO-8859-1', 'UTF-8'),0,1,'L');
               $pdf->setFont('arial','',6);
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

               $pdf->SetXY(730,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding($cidadeestado, 'ISO-8859-1', 'UTF-8'),0,1,'L');

               $pdf->ln(1);
               $alturalinha=$alturalinha + 10;
               $linha = $linha + 1;
               //somatoria
               $qtd = $qtd + 1;
               $vltotal += $object->valor_total;
               $vltotalcotacao += $object->valor_liquido_cotacao;

               } 
             } 

             $alturalinha=$alturalinha + 10; 

             $pdf->ln(1); 
             $pdf->Cell(0,4,"","B",1,'C');

             $pdf->SetXY(27,$alturalinha);
             $pdf->Cell(70,5,'Total Geral : '.$qtd,0,1,'L');

             $pdf->SetXY(495,$alturalinha);
             $pdf->Cell(70,5,number_format($vltotal, 2),0,1,'R');

             $pdf->SetXY(550,$alturalinha);
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
                                } else{
                                    $value = mb_convert_encoding($object->$column_name, 'ISO-8859-1', 'UTF-8');
                                }

                            } else {
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
            $page->setProperty('page-name', 'PedidoVendaListSearch');
            $page->setProperty('page_name', 'PedidoVendaListSearch');
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

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

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

            $filters[] = new TFilter('system_users_id', '=', $data->system_users_id);// create the filter 
        }

        if (isset($data->cliente_id) AND ( (is_scalar($data->cliente_id) AND $data->cliente_id !== '') OR (is_array($data->cliente_id) AND (!empty($data->cliente_id)) )) )
        {

            $filters[] = new TFilter('cliente_id', '=', $data->cliente_id);// create the filter 
        }

        if (isset($data->cidade_id) AND ( (is_scalar($data->cidade_id) AND $data->cidade_id !== '') OR (is_array($data->cidade_id) AND (!empty($data->cidade_id)) )) )
        {

            $filters[] = new TFilter('cidade_id', '=', $data->cidade_id);// create the filter 
        }

        if (isset($data->dt_finalizacao_fim) AND ( (is_scalar($data->dt_finalizacao_fim) AND $data->dt_finalizacao_fim !== '') OR (is_array($data->dt_finalizacao_fim) AND (!empty($data->dt_finalizacao_fim)) )) )
        {

            $filters[] = new TFilter('dt_finalizacao', '<=', $data->dt_finalizacao_fim);// create the filter 
        }

        if (isset($data->dt_finalizacao) AND ( (is_scalar($data->dt_finalizacao) AND $data->dt_finalizacao !== '') OR (is_array($data->dt_finalizacao) AND (!empty($data->dt_finalizacao)) )) )
        {

            $filters[] = new TFilter('dt_finalizacao', '>=', $data->dt_finalizacao);// create the filter 
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

            // Ajuste visual do botão de filtros
            if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }

            // Filtro por cliente vinculado ao usuário
            $pes1 = Pessoa::where('system_user_id','=',TSession::getValue('userid'))
                               ->load();
            if ($pes1) {
                foreach ($pes1 as $pessoass) {}
                $criteria->add(new TFilter('cliente_id', '=', $pessoass->id), TExpression::AND_OPERATOR);
            }

            // Filtro por departamentos do usuário
            $suserdep = SystemUserDepartamentoUnit::where('system_users_id','=',TSession::getValue('userid'))
                                                  ->load();
            if ($suserdep)
            {
                $departamento_unit_ids = [];
                foreach($suserdep as $suserdeps){
                    $departamento_unit_ids[] = $suserdeps->departamento_unit_id;
                }
                if ($departamento_unit_ids) {
                    $criteria->add(new TFilter('departamento_unit_id', 'in', $departamento_unit_ids), TExpression::AND_OPERATOR);
                }
            }
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));

            // Carrega os pedidos
            $objects = $repository->load($criteria, FALSE);

            // OTIMIZAÇÃO: Carregar dados relacionados em lote para evitar N+1 queries
            $cliente_ids = [];
            $cidade_ids = [];
            $departamento_unit_ids = [];
            $system_users_ids = [];
            $estado_pedido_ids = [];
            $estado_revisao_ids = [];
            $pedido_ids = [];
            foreach (($objects ?: []) as $object) {
                $pedido_ids[$object->id] = true;
                if (!empty($object->cliente_id)) $cliente_ids[$object->cliente_id] = true;
                if (!empty($object->cidade_id)) $cidade_ids[$object->cidade_id] = true;
                if (!empty($object->departamento_unit_id)) $departamento_unit_ids[$object->departamento_unit_id] = true;
                if (!empty($object->system_users_id)) $system_users_ids[$object->system_users_id] = true;
                if (!empty($object->estado_pedido_venda_id)) $estado_pedido_ids[$object->estado_pedido_venda_id] = true;
                if (!empty($object->estado_pedido1_id)) $estado_revisao_ids[$object->estado_pedido1_id] = true;
            }

            // Carregar clientes em lote
            $clientes = [];
            if ($cliente_ids) {
                $clientes_raw = Pessoa::where('id', 'in', array_keys($cliente_ids))->load();
                foreach ($clientes_raw as $c) {
                    $clientes[$c->id] = $c;
                }
            }

            // Carregar cidades e estados em lote
            $cidades = [];
            $estados = [];
            if ($cidade_ids) {
                $cidades_raw = Cidade::where('id', 'in', array_keys($cidade_ids))->load();
                $estado_ids = [];
                foreach ($cidades_raw as $cid) {
                    $cidades[$cid->id] = $cid;
                    if (!empty($cid->estado_id)) $estado_ids[$cid->estado_id] = true;
                }
                if ($estado_ids) {
                    $estados_raw = Estado::where('id', 'in', array_keys($estado_ids))->load();
                    foreach ($estados_raw as $est) {
                        $estados[$est->id] = $est;
                    }
                }
            }

            // Carregar departamentos em lote
            $departamentos = [];
            if ($departamento_unit_ids) {
                $departamentos_raw = DepartamentoUnit::where('id', 'in', array_keys($departamento_unit_ids))->load();
                foreach ($departamentos_raw as $dep) {
                    $departamentos[$dep->id] = $dep;
                }
            }

            // Carregar usuÇ­rios em lote
            $usuarios = [];
            if ($system_users_ids) {
                $usuarios_raw = SystemUsers::where('id', 'in', array_keys($system_users_ids))->load();
                foreach (($usuarios_raw ?: []) as $usr) {
                    $usuarios[$usr->id] = $usr;
                }
            }

            // Carregar estados do pedido (atual e revisÇœo) em lote
            $estados_pedido = [];
            $todos_estado_ids = array_unique(array_merge(array_keys($estado_pedido_ids), array_keys($estado_revisao_ids)));
            if ($todos_estado_ids) {
                $estados_pedido_raw = EstadoPedido::where('id', 'in', $todos_estado_ids)->load();
                foreach (($estados_pedido_raw ?: []) as $estado_pedido) {
                    $estados_pedido[$estado_pedido->id] = $estado_pedido;
                }
            }

            // Identificar pedidos com documentos de cotaÇõÇœo em lote
            $pedidos_com_nota = [];
            if ($pedido_ids) {
                $cotacoes_raw = Cotacao::where('pedido_id', 'in', array_keys($pedido_ids))->load();
                $cotacao_para_pedido = [];
                $cotacao_ids = [];
                foreach (($cotacoes_raw ?: []) as $cotacao) {
                    $cotacao_para_pedido[$cotacao->id] = $cotacao->pedido_id;
                    $cotacao_ids[$cotacao->id] = true;
                }

                if ($cotacao_ids) {
                    $docs_raw = DocumentosCotacao::where('cotacao_id', 'in', array_keys($cotacao_ids))->load();
                    foreach (($docs_raw ?: []) as $doc) {
                        if (isset($cotacao_para_pedido[$doc->cotacao_id])) {
                            $pedidos_com_nota[$cotacao_para_pedido[$doc->cotacao_id]] = true;
                        }
                    }
                }
            }

            $this->datagrid->clear();
            TSession::setValue(__CLASS__.'_detalhes_abertos', []);
            $cont=1;
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    // Anexar dados relacionados já carregados
                    if (!empty($object->cliente_id) && isset($clientes[$object->cliente_id])) {
                        $object->cliente_nome = $clientes[$object->cliente_id]->nome;
                    }
                    if (!empty($object->cidade_id) && isset($cidades[$object->cidade_id])) {
                        $cid = $cidades[$object->cidade_id];
                        $estado_sigla = isset($estados[$cid->estado_id]) ? $estados[$cid->estado_id]->sigla : '';
                        $object->cidade_nome_estado = $cid->nome . ' - ' . $estado_sigla;
                    }
                    if (!empty($object->departamento_unit_id) && isset($departamentos[$object->departamento_unit_id])) {
                        $object->departamento_nome = $departamentos[$object->departamento_unit_id]->name;
                    }
                    if (!empty($object->system_users_id) && isset($usuarios[$object->system_users_id])) {
                        $object->system_users_login_text = $usuarios[$object->system_users_id]->login;
                    }
                    if (!empty($object->estado_pedido_venda_id) && isset($estados_pedido[$object->estado_pedido_venda_id])) {
                        $object->estado_pedido_venda_nome = $estados_pedido[$object->estado_pedido_venda_id]->nome;
                        $object->estado_pedido_venda_cor = $estados_pedido[$object->estado_pedido_venda_id]->cor;
                    }
                    if (!empty($object->estado_pedido1_id) && isset($estados_pedido[$object->estado_pedido1_id])) {
                        $object->estado_revisao_nome = $estados_pedido[$object->estado_pedido1_id]->nome;
                    }
                    $object->tem_nota_fiscal = !empty($pedidos_com_nota[$object->id]);

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                    $row = new TTableRow;
                    $div = new TElement('div');
                    $div->id = "container_propostas_{$object->id}";
                    $cell=$row->addCell($div);
                    $cell->colspan = $this->datagrid->getTotalColumns();
                    $cell->style = 'padding: 10px; ';
                    $row->style = 'display:none;';
                    $this->datagrid->insert($cont+1, $row);
                    $cont+=3;
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
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {
            if (isset($param['inserido'])) 
            {   
                TTransaction::open('minierp');

                $pedido = new Pedido($param['pedido_id']);

                if ($pedido) {
                    // Carrega os itens atuais
                    $itenspedido = ItensPedido::where('pedido_venda_id', '=', $param['pedido_id'])->load();

                    // Carrega os itens antigos da sessão (vindo de outro formulário)
                    $old_items = TSession::getValue('old_items') ?? [];

                    // Mapeia os itens antigos por ID
                    $old_map = [];
                    foreach ($old_items as $old) {
                        $old_map[$old->id] = $old;
                    }

                    if ($itenspedido) {
                        foreach ($itenspedido as $itensp) {
                            $revisarcotacao = false;
                            $item_existe_antigo = isset($old_map[$itensp->id]);
                            $antigo = $item_existe_antigo ? $old_map[$itensp->id] : null;

                            // Busca as propostas vinculadas ainda em estados editáveis
                            $cotacaorevisao = Cotacao::where('pedido_id', '=', $param['pedido_id'])
                                                        ->where('estado_pedido_id', 'in', [
                                                            EstadoPedidoFrotas::NAOENVIADO,
                                                            EstadoPedidoFrotas::PREAPROVADO,
                                                            EstadoPedidoFrotas::AGUARDANDO
                                                        ])
                                                        ->load();

                            foreach ($cotacaorevisao as $pr) {
                                $itens = ItensCotacao::where('cotacao_id', '=', $pr->id)
                                                    ->where('itens_pedido_id', '=', $itensp->id)
                                                    ->load();

                                if (!$itens) {
                                    // Novo item adicionado ao pedido, incluir na proposta
                                    $novo = new ItensCotacao();
                                    $novo->produto_id = $itensp->produto_id;
                                    $novo->qtde = $itensp->qtde;
                                    $novo->cotacao_id = $pr->id;
                                    $novo->itens_pedido_id = $itensp->id;
                                    $novo->store();
                                    $revisarcotacao = true;
                                } else {
                                    foreach ($itens as $itemCot) {
                                        // Se houve aumento de quantidade
                                        if ($item_existe_antigo && $antigo->qtde < $itensp->qtde) {
                                            $itemCot->qtde = $itensp->qtde;
                                            $itemCot->valor = 0;
                                            $itemCot->perc_desconto = 0;
                                            $itemCot->valor_total = 0;
                                            $revisarcotacao = true;
                                        }

                                        // Se houve mudança na descrição
                                        if ($item_existe_antigo && $antigo->produto_id !== $itensp->produto_id) {
                                            $itemCot->produto_id = $itensp->produto_id;
                                            $revisarcotacao = true;
                                        }

                                        if ($revisarcotacao) {
                                            $itemCot->store();
                                        }
                                    }
                                }

                                if ($revisarcotacao) {
                                    $pr->estado_pedido1_id = EstadoPedido::REVISAO;
                                    $pr->store();
                                }
                            }
                        }
                    }
                }

                // ==== EXCLUSÃO DE ITENS ====

                $idsAntigos = array_map(function ($old) {
                    return (int) ($old->id ?? 0);
                }, $old_items);

                $idsAtuais = [];
                if ($itenspedido) {
                    foreach ($itenspedido as $itemAtual) {
                        $idsAtuais[] = (int) $itemAtual->id;
                    }
                }

                $idsExcluidosNestaEdicao = array_diff(
                    array_filter($idsAntigos),
                    array_filter($idsAtuais)
                );

                foreach ($idsExcluidosNestaEdicao as $itemPedidoExcluidoId) {
                    $itensCotacao = ItensCotacao::where('itens_pedido_id', '=', (int) $itemPedidoExcluidoId)->load();

                    foreach ($itensCotacao as $ip) {
                        $cotacao = new Cotacao($ip->cotacao_id);

                        if (in_array($cotacao->estado_pedido_id, [
                            EstadoPedido::NAOENVIADO,
                            EstadoPedido::PREAPROVADO,
                            EstadoPedido::AGUARDANDO
                        ])) {
                            $ip->delete();
                            $cotacao->estado_pedido1_id = EstadoPedido::REVISAO;
                            $cotacao->store();
                        }
                    }
                }

                TSession::setValue('old_items', null);

                if (false) {
                $conn = TTransaction::get();
                $result = $conn->query("SELECT * FROM itens_pedido WHERE pedido_venda_id = " . $param['pedido_id']);
                $itens_excluidos = [];

                foreach ($result as $old_item) {
                    // Supondo que deleted_at esteja na posição 15 da linha (ajuste se necessário)
                    if (!empty($old_item[11])) {
                        $itens_excluidos[] = $old_item;
                    }
                }

                foreach ($itens_excluidos as $excluido) {
                    $itensCotacao = ItensCotacao::where('itens_pedido_id', '=', $excluido[0])->load();

                    foreach ($itensCotacao as $ip) {
                        $cotacao = new Cotacao($ip->cotacao_id);

                        if (in_array($cotacao->estado_pedido_id, [
                            EstadoPedido::NAOENVIADO,
                            EstadoPedido::PREAPROVADO,
                            EstadoPedido::AGUARDANDO
                        ])) {
                            $ip->delete(); // ou $ip->deleted_at = date(...); $ip->store(); para exclusão lógica
                            $cotacao->estado_pedido1_id = EstadoPedido::REVISAO;
                            $cotacao->store();
                        }
                    }
                }

                }
                TTransaction::close();
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
            $pessoa_id = $fornecedor->pessoa_id ?? $fornecedor->id ?? null;
            if (empty($pessoa_id))
            {
                continue;
            }

            $cidade_id = $fornecedor->cidade_id ?? null;
            if (empty($cidade_id))
            {
                $endereco = PessoaEndereco::where('pessoa_id', '=', $pessoa_id)
                                          ->where('principal', '=', 'T')
                                          ->where('cidade_id', '<>', 0)
                                          ->first();
                if ($endereco)
                {
                    $cidade_id = $endereco->cidade_id;
                }
            }

            $cotacaoCriteria = Cotacao::where('pedido_id','=',$pedido->id)
                                      ->where('pessoa_id','=',$pessoa_id);
            if (!empty($cidade_id))
            {
                $cotacaoCriteria->where('cidade_id','=',$cidade_id);
            }

            $cot = $cotacaoCriteria->load();
            if ((!$cot)) {

            $cotacao = new Cotacao();
            $cotacao->pedido_id = $pedido->id;
            $cotacao->pessoa_id = $pessoa_id;
            $cotacao->data_cotacao = date('Y-m-d');
            $cotacao->estado_pedido_id = EstadoPedido::PENDENTE;
            $cotacao->system_users_id = TSession::getValue('iduser');
            $cotacao->system_unit_id = TSession::getValue('idunit');
            $cotacao->entidade_id = TSession::getValue('entidade');
            $cotacao->departamento_unit_id = $pedido->departamento_unit_id;
            $cotacao->cidade_id = $cidade_id;
            $cotacao->data_limite_resposta = $pedido->data_limite_resposta;
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
                            if($fornecedor->email)
                                {

                                    // MailService::send($fornecedor->email, $titulo, $mensagem,  'html');

                                }
            }

            }
        }
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

        $unids = new SystemUnit(TSession::getValue('idunit'));
        $unidade = utf8_decode($unids->name);
        if(!empty(TSession::getValue('data_inicial'))){     
            $datai = new DateTime(TSession::getValue('data_inicial'));
            $datai = $datai->format('d/m/Y');

            $dataf = new DateTime(TSession::getValue('data_final'));
            $dataf = $dataf->format('d/m/Y');

            $label = 'Periodo: de '. $datai . ' ate '. $dataf;
        }

        $pdf->AddPage();
        $pdf->Image('app/images/logo.png', 26, 8, 46);
        $pdf->SetFont('arial','B',8);
        $pdf->SetXY(78,14);
        $pdf->Cell(70,5, $unidade, 0,1,'L');
        $pdf->SetXY(330,14);
        $pdf->Cell(70,5,utf8_decode('Relatório de pedidos de compra '),0,1,'C');
        $pdf->SetXY(660,14);
        $pdf->Cell(70,5,'Hora: '.date("H:i:s"),0,1,'C');
        $pdf->SetXY(748,14);
        $pdf->Cell(70,5,'Data: '.date("d/m/Y"),0,1,'C');
        $pdf->Ln(4);

        $pdf->SetXY(78,28);
        $pdf->Cell(70,5,$cnpj.'      '. $label,0,1,'L');
        $pdf->SetXY(115,20);
        $pdf->Cell(70,5,'',0,1,'L');
        $pdf->SetXY(748,28);
        $pdf->Cell(70,5,utf8_decode(' Página: ').$pag,0,1,'R');
        $pdf->Ln(1);

        //nome
        $pdf->SetXY(26,56);
        $pdf->Cell(0,5,"","B",1,'C');
        $pdf->SetXY(27,72);
        $pdf->Cell(70,5,'ID',0,1,'L');

        $pdf->SetXY(47,72);
        $pdf->Cell(70,5,'Data',0,1,'L');

        $pdf->SetXY(85,72);
        $pdf->Cell(70,5,'Nome',0,1,'L');

        $pdf->SetXY(225,72);
        $pdf->Cell(100,5,utf8_decode('Descrição do pedido'),0,1,'L');

        $pdf->SetXY(442,72);
        $pdf->Cell(70,5,'Status',0,1,'L');

        $pdf->SetXY(495,72);
        $pdf->Cell(70,5,'Valor Pedido',0,1,'R');

         $pdf->SetXY(515,72);
         $pdf->Cell(100,5,'Valor Cotado',0,1,'R');

         $pdf->SetXY(579,72);
         $pdf->Cell(100,5,'Departamento',0,1,'R');

         $pdf->SetXY(662,72);
         $pdf->Cell(100,5,'Cidade',0,1,'R');
         //                123456789012 

        $pdf->ln(1);

        $pdf->SetXY(26,84);
        $pdf->Cell(0,4,"","B",1,'C');
        $linha = 12;
     }
     public function onSetProject($param) {
    //     TTransaction::open(self::$database);
    //     $unit=SystemUnit::where('id','=',TSession::getValue('idunit'))
    //     ->load();
    //     if ($unit)
    //     {
    //         foreach($unit as $unitss) {
    //         //echo ' unit ' .$unitss->utilizasinapi;
    //         if ($unitss->utilizasinapi=='S'){
    //         //    ImportarTabelaSinapi::import();  
    //         }
    //        }  
    //     }
    //     TTransaction::close();
    //   //  $this->onShow();
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

            //</autoCode>

   private function cabecalhoDCot($pdf, $linha,$pag, $unidade, $id, $datacotacao, $idcot)
    {
        $label = '';
        $datacotacao = new DateTime($datacotacao);
        $datacotacao = $datacotacao->format('d/m/Y');

        $ped = new Pedido($id);
        $dep = new DepartamentoUnit($ped->departamento_unit_id);
        $unit = new SystemUnit($dep->system_unit_id);

        $pessoa = new Pessoa($ped->cliente_id);                           
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
                                          ->load();
        $usuario='';
        if ($historicopedido) {
            foreach($historicopedido as $histped) {
               $user = new SystemUsers($histped->aprovador_id);
               $usuario = $user->name;                
               break;              
            }
        } else {$usuario = '';}

        $pdf->AddPage();
        $pdf->SetFont('arial','B',10);

        $pdf->Image('app/images/logo.png', 25, 03, 80);
        $pdf->SetXY(300,40);
        $pdf->Cell(70,5,utf8_decode('Pedido de Compra: '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(345,40);
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
        $pdf->Cell(70,5,utf8_decode($dep->name),0,1,'L');

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
        $pdf->Cell(70,5,utf8_decode('Produto'),0,1,'L');
        $pdf->SetXY(360,238);
        $pdf->Cell(70,5,utf8_decode('Quantidade'),0,1,'R');
        $pdf->SetXY(420,238);
        $pdf->Cell(70,5,utf8_decode('Valor'),0,1,'R');
        $pdf->SetXY(500,238);
        $pdf->Cell(70,5,utf8_decode('Valor Total'),0,1,'R');

        $pdf->ln(1);
     }

  private function cabecalhoDocPedido($pdf, $linha,$pag, $unidade, $id, $datapedido)
    {
        $label = '';
        $datapedido = new DateTime($datapedido);
        $datapedido = $datapedido->format('d/m/Y');

        $ped = new Pedido($id);
        $dep = new DepartamentoUnit($ped->departamento_unit_id);
        $unit = new SystemUnit($dep->system_unit_id);
        $centrocusto = new Centrocusto($ped->centrocusto_id);

        $pdf->AddPage();
        $pdf->SetFont('arial','B',10);

        $pdf->Image('app/images/logo.png', 25, 03, 80);
        $pdf->SetXY(445,40);
        $pdf->Cell(70,5,utf8_decode('Pedido de Compra: '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(500,40);
        $pdf->Cell(70,5,'#'.$id,0,1,'R');
        $pdf->Ln(4);

        $pdf->SetFont('arial','B',10);
        $pdf->SetXY(425,55);
        $pdf->Cell(70,5,utf8_decode('Data do Pedido: '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(500,55);
        $pdf->Cell(70,5,$datapedido,0,1,'R');
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
        $pdf->Cell(70,5,utf8_decode('Dados do Pedido'),0,1,'L');

        $pdf->SetXY(25,118);
        $pdf->Cell(70,5,utf8_decode('Descrição do Pedido '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(25,133);
        $pdf->Cell(70,5,utf8_decode($ped->descricaopedido),0,1,'L');

        $pdf->SetFont('arial','B',10);
        $pdf->SetXY(355,118);
        $pdf->Cell(70,5,utf8_decode('Unidade Gestora '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(355,128);
        $pdf->MultiCell(220,12,utf8_decode($unit->name),0,'L',false);

        $pdf->SetFont('arial','B',10);
        $pdf->SetXY(25,163);
        $pdf->Cell(70,5,utf8_decode('Centro de Custo '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(25,178);
        $pdf->Cell(70,5,utf8_decode($centrocusto->nome),0,1,'L');

        $pdf->SetFont('arial','B',10);
        $pdf->SetXY(355,163);
        $pdf->Cell(70,5,utf8_decode('Departamento'),0,1,'L');
        $pdf->SetFont('arial','',8);
        $pdf->SetXY(355,178);
        $pdf->Cell(70,5,utf8_decode($dep->name),0,1,'L');

        $pdf->SetFont('arial','B',10); 
        $pdf->SetXY(25,208);
        $pdf->SetFillColor(235,239,240);
        $pdf->Rect(26, 203, 542, 15, 'F');
        $pdf->Cell(70,5,utf8_decode('Itens da Pedido '),0,1,'L');

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
        $pdf->SetXY(500,238);
        $pdf->Cell(70,5,utf8_decode('Valor Total'),0,1,'R');

        $pdf->ln(1);
     }

     public static function onExibirDetalhe($param=null)
    {

        try 
        {
            $pedidoId = (int) ($param['key'] ?? 0);
            if ($pedidoId <= 0)
            {
                return;
            }

            $sessionKey = __CLASS__.'_detalhes_abertos';
            $detalhesAbertos = TSession::getValue($sessionKey) ?: [];
            $containerId = "container_propostas_{$pedidoId}";

            if (!empty($detalhesAbertos[$pedidoId]))
            {
                unset($detalhesAbertos[$pedidoId]);
                TSession::setValue($sessionKey, $detalhesAbertos);

                TScript::create("
                    (function () {
                        var el = document.getElementById('{$containerId}');
                        if (!el) return;
                        el.innerHTML = '';
                        el.style.display = 'none';
                        var tr = el.closest ? el.closest('tr') : null;
                        if (tr) tr.style.display = 'none';
                    })();
                ");
                return;
            }

            $detalhesAbertos[$pedidoId] = true;
            TSession::setValue($sessionKey, $detalhesAbertos);

            TScript::create("
                (function () {
                    var el = document.getElementById('{$containerId}');
                    if (!el) return;
                    el.style.display = '';
                    var tr = el.closest ? el.closest('tr') : null;
                    if (tr) tr.style.display = '';
                })();
            ");

            TApplication::loadPage('CotacaoPendenteList', 'onShow', [
                'target_container' => $containerId,
                'pedido_id' => $pedidoId
            ]);
        
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }

    }

    public function onToggleCotacoes($param)
    {
        try {
            $pedido_id = (int) ($param['id'] ?? 0);
            if (!$pedido_id) {
                return;
            }

            // ID do container que vai receber a lista de cotações
            $container_id = "container_cotacoes_{$pedido_id}";
            $detail_row_id = "detail_row_{$pedido_id}";

            // Se você quiser “toggle” (abre/fecha), pode controlar via JS:
            // Se a linha já existir, só alterna display e retorna
            // (Adianti não tem um "exists row" direto; então normalmente a gente usa JS)
            TScript::create("
                (function(){
                    var tr = document.getElementById('{$detail_row_id}');
                    if(tr){
                        tr.style.display = (tr.style.display === 'none' ? '' : 'none');
                    }
                })();
            ");

            // Agora cria a linha de detalhe (HTML) e injeta logo após a linha do pedido
            // Vamos montar um <tr> manual e inserir via JS pra ficar leve.
            // Isso evita criar TTableRow no servidor pra cada registro no onReload.

            $html = "
                <tr id='{$detail_row_id}'>
                    <td colspan='{$this->datagrid->getTotalColumns()}' style='padding:10px; background:#fafafa'>
                        <div id='{$container_id}' style='min-height:60px'></div>
                    </td>
                </tr>
            ";
            $html_js = json_encode($html);

            TScript::create("
                (function(){
                    var base = document.getElementById('row_{$pedido_id}');
                    if(!base) return;

                    var next = base.nextElementSibling;
                    // se já existe a linha, não recria
                    if(next && next.id === '{$detail_row_id}') {
                        return;
                    }

                    base.insertAdjacentHTML('afterend', {$html_js});
                })();
            ");

            // Carrega a página de cotações dentro do container
            TApplication::loadPage('CotacaoPendenteList', 'onShow', [
                'target_container' => $container_id,
                'pedido_id'        => $pedido_id
            ]);

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    private static function calcularVencimentoFinanceiro($dtFinalizacao)
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
     

}




