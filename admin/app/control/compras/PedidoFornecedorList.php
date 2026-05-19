<?php

class PedidoFornecedorList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Pedido';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Pedido';
    private $showMethods = ['onReload', 'onSearch'];
    private $limit = 20;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();
        // creates the form

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->limit = 20;

        $criteria_cliente_id = new TCriteria();
        $criteria_departamento_unit_id = new TCriteria();
        $criteria_centrocusto_id = new TCriteria();
        $criteria_estado_pedido_venda_id = new TCriteria();
        $criteria_system_users_id = new TCriteria();

        $filterVar = TSession::getValue("userid");
        $criteria_departamento_unit_id->add(new TFilter('system_users_id', '=', $filterVar)); 
        $filterVar = TSession::getValue("userid");
        $criteria_system_users_id->add(new TFilter('id', '=', $filterVar)); 

        $id = new TEntry('id');
        $descricaopedido = new TEntry('descricaopedido');
        $cliente_id = new TDBCombo('cliente_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_cliente_id );
        $dt_pedido = new TEntry('dt_pedido');
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'SystemUserDepartamentoUnit', 'departamento_unit_id', '{departamento_unit->name}','departamento_unit_id asc' , $criteria_departamento_unit_id );
        $centrocusto_id = new TDBCombo('centrocusto_id', 'minierp', 'Centrocusto', 'id', '{nome}','nome asc' , $criteria_centrocusto_id );
        $valor_total = new TEntry('valor_total');
        $valor_total_cotacao = new TEntry('valor_total_cotacao');
        $estado_pedido_venda_id = new TDBCombo('estado_pedido_venda_id', 'minierp', 'EstadoPedido', 'id', '{nome}','nome asc' , $criteria_estado_pedido_venda_id );
        $system_users_id = new TDBCombo('system_users_id', 'minierp', 'SystemUsers', 'id', '{name}','name asc' , $criteria_system_users_id );

        $id->exitOnEnter();
        $descricaopedido->exitOnEnter();
        $dt_pedido->exitOnEnter();
        $valor_total->exitOnEnter();
        $valor_total_cotacao->exitOnEnter();

        $id->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $descricaopedido->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $dt_pedido->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $valor_total->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $valor_total_cotacao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $cliente_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $departamento_unit_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $centrocusto_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $estado_pedido_venda_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $system_users_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $cliente_id->enableSearch();
        $centrocusto_id->enableSearch();
        $system_users_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $estado_pedido_venda_id->enableSearch();

        $id->setSize('100%');
        $dt_pedido->setSize('100%');
        $cliente_id->setSize('100%');
        $valor_total->setSize('100%');
        $centrocusto_id->setSize('100%');
        $descricaopedido->setSize('100%');
        $system_users_id->setSize('100%');
        $valor_total_cotacao->setSize('100%');
        $estado_pedido_venda_id->setSize(240);
        $departamento_unit_id->setSize('100%');

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_descricaopedido = new TDataGridColumn('descricaopedido', "Descricaopedido", 'left');
        $column_cliente_nome = new TDataGridColumn('cliente->nome', "Cliente", 'left');
        $column_dt_pedido_transformed = new TDataGridColumn('dt_pedido', "Dt pedido", 'left');
        $column_departamento_unit_name = new TDataGridColumn('departamento_unit->name', "Departamento / Secretária", 'left');
        $column_centrocusto_nome = new TDataGridColumn('centrocusto->nome', "Centro de Custo", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_total', "Vl pedido", 'left');
        $column_valor_total_cotacao_transformed = new TDataGridColumn('valor_total_cotacao', "Vl cotação", 'left');
        $column_estado_pedido_venda_nome_transformed = new TDataGridColumn('estado_pedido_venda->nome', "Estado pedido", 'left');
        $column_system_users_name = new TDataGridColumn('system_users->name', "Usuário", 'left');
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

        $column_estado_pedido_venda_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
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
               return "<span class='label label-default' style='width:230px; background-color:{$object->estado_pedido_venda->cor}'> {$anexo} <span>";    
            } else {
               return "<span class='label label-default' style='width:230px; background-color:{$object->estado_pedido_venda->cor}'> {$object->estado_pedido_venda->nome} <span>";    
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
        $this->datagrid->addColumn($column_departamento_unit_name);
        $this->datagrid->addColumn($column_centrocusto_nome);
        $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column_valor_total_cotacao_transformed);
        $this->datagrid->addColumn($column_estado_pedido_venda_nome_transformed);
        $this->datagrid->addColumn($column_system_users_name);
        $this->datagrid->addColumn($column_cidade_id_transformed);

        $action_onEdit = new TDataGridAction(array('PedidoFornecedorForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);
        $action_onEdit->setDisplayCondition('PedidoFornecedorList::onExibirEdicao');

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('PedidoFornecedorList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);
        $action_onDelete->setDisplayCondition('PedidoFornecedorList::onExibirExcluir');

        $this->datagrid->addAction($action_onDelete);

        $action_onEnviarCotacao = new TDataGridAction(array('PedidoFornecedorList', 'onEnviarCotacao'));
        $action_onEnviarCotacao->setUseButton(false);
        $action_onEnviarCotacao->setButtonClass('btn btn-default btn-sm');
        $action_onEnviarCotacao->setLabel("Enviar Cotação");
        $action_onEnviarCotacao->setImage('fas:envelope #E91E63');
        $action_onEnviarCotacao->setField(self::$primaryKey);
        $action_onEnviarCotacao->setDisplayCondition('PedidoFornecedorList::onExibirEnvioCotacao');

        $this->datagrid->addAction($action_onEnviarCotacao);

        $action_onGenerate = new TDataGridAction(array('PedidoVendaDocumentForn', 'onGenerate'));
        $action_onGenerate->setUseButton(false);
        $action_onGenerate->setButtonClass('btn btn-default btn-sm');
        $action_onGenerate->setLabel("");
        $action_onGenerate->setImage('far:file-pdf #000000');
        $action_onGenerate->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onGenerate);

        $action_onSetProject = new TDataGridAction(array('DocumentosCotacaoFornList', 'onSetProject'));
        $action_onSetProject->setUseButton(false);
        $action_onSetProject->setButtonClass('btn btn-default btn-sm');
        $action_onSetProject->setLabel("");
        $action_onSetProject->setImage('fas:paperclip #795548');
        $action_onSetProject->setField(self::$primaryKey);
        $action_onSetProject->setDisplayCondition('PedidoFornecedorList::onExibirAnexo');

        $this->datagrid->addAction($action_onSetProject);

        // create the datagrid model
        $this->datagrid->createModel();

        $tr = new TElement('tr');
        $this->datagrid->prependRow($tr);

        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $td_id = TElement::tag('td', $id);
        $tr->add($td_id);
        $td_descricaopedido = TElement::tag('td', $descricaopedido);
        $tr->add($td_descricaopedido);
        $td_cliente_id = TElement::tag('td', $cliente_id);
        $tr->add($td_cliente_id);
        $td_dt_pedido = TElement::tag('td', $dt_pedido);
        $tr->add($td_dt_pedido);
        $td_departamento_unit_id = TElement::tag('td', $departamento_unit_id);
        $tr->add($td_departamento_unit_id);
        $td_centrocusto_id = TElement::tag('td', $centrocusto_id);
        $tr->add($td_centrocusto_id);
        $td_valor_total = TElement::tag('td', $valor_total);
        $tr->add($td_valor_total);
        $td_valor_total_cotacao = TElement::tag('td', $valor_total_cotacao);
        $tr->add($td_valor_total_cotacao);
        $td_estado_pedido_venda_id = TElement::tag('td', $estado_pedido_venda_id);
        $tr->add($td_estado_pedido_venda_id);
        $td_system_users_id = TElement::tag('td', $system_users_id);
        $tr->add($td_system_users_id);
        $td_empty = TElement::tag('td', "");
        $tr->add($td_empty);

        $this->datagrid_form->addField($id);
        $this->datagrid_form->addField($descricaopedido);
        $this->datagrid_form->addField($cliente_id);
        $this->datagrid_form->addField($dt_pedido);
        $this->datagrid_form->addField($departamento_unit_id);
        $this->datagrid_form->addField($centrocusto_id);
        $this->datagrid_form->addField($valor_total);
        $this->datagrid_form->addField($valor_total_cotacao);
        $this->datagrid_form->addField($estado_pedido_venda_id);
        $this->datagrid_form->addField($system_users_id);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $panel->getHeader()->style = ' display:none !important; ';
        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $this->datagrid_form->add($this->datagrid);
        $panel->add($headerActions);
        $panel->add($this->datagrid_form);

        $button_cadastrar = new TButton('button_button_cadastrar');
        $button_cadastrar->setAction(new TAction(['PedidoFornecedorForm', 'onEdit']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['PedidoFornecedorList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['PedidoFornecedorList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['PedidoFornecedorList', 'onExportCsv'],['static' => 1]), self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['PedidoFornecedorList', 'onExportXls'],['static' => 1]), self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['PedidoFornecedorList', 'onExportXml'],['static' => 1]), self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_cadastrar);
        $head_left_actions->add($button_atualizar);
        $head_left_actions->add($button_limpar_filtros);

        $head_right_actions->add($dropdown_button_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Compras","PedidoFornecedorList"]));
        }

        $container->add($panel);

        parent::add($container);

    }

    public static function onExibirEdicao($object)
    {
         try 
        {
            if( in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::PENDENTE, EstadoPedido::COMPROPOSTA]) )
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

                // instantiates object
                $object = new Pedido($key, FALSE); 

                // deletes the object from the database
                $object->delete();

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
    public function onEnviarCotacao($param = null) 
    {

      if (isset($param['confirmEnviarCotacao']) && $param['confirmEnviarCotacao']) {
            try {
                TTransaction::open(self::$database);
                $conexao   = TTransaction::get(); 
                //$conexao->exec( "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));" );

                $pedido = new Pedido($param['id'], false);

                $this->gerarCotacoes($pedido);

                // Atualiza o status do pedido e registra histórico
                $pedido->estado_pedido_venda_id = EstadoPedido::COMPROPOSTA;
                $pedido->store();

                $this->registrarHistoricoPedido($pedido);

               // $this->geracotacao($pedido);

                //GERAR A COTACAO E OS ITENS DE COTACAO E DEVOLVER COM O STATUS COM PROPOSTA AGUARDANDO APROVAÇÃO;
                //TABELA COTACAO E ITENS_COTACAO 

                TToast::show('success', "Emails enviados!!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoFornecedorList', 'onShow');
            //    var_dump($pedido);
              //  die();

               // $this->form->setData($pedido); 
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

    /*    try 
        {
            //code here

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }
    public static function onExibirEnvioCotacao($object)
    {
         try 
        {
            if (in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::PENDENTE]) )
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
    public static function onExibirAnexo($object)
    {
        try 
        {
            if (in_array($object->estado_pedido_venda_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_venda_id, [EstadoPedido::FINALIZADO, EstadoPedido::APROVADO, EstadoPedido::PGTOAPROVADO]) )
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

                                }else{
                                    $row[] = mb_convert_encoding($object->$column_name, 'ISO-8859-1', 'UTF-8');
                                }

                            }else{

                                    if (isset($object->$column_name))
                                    {
                                        $row[] = is_scalar($object->$column_name) ? $object->$column_name : '';
                                    }
                                    else if (method_exists($object, 'render'))
                                    {
                                        $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
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
                    else if (strpos((string)$column->getWidth(), '%') !== false)
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
                                        $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                        $value = $object->render($column_name);
                                    }

                                    $transformer = $column->getTransformer();
                                    if ($transformer)
                                    {
                                        $value = strip_tags((string)call_user_func($transformer, $value, $object, null));
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
                                        $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
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
    public function onRefresh($param = null) 
    {
        $this->onReload([]);
    }
    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        // get the search form data
        $data = $this->datagrid_form->getData();
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

        if (isset($data->cliente_id) AND ( (is_scalar($data->cliente_id) AND $data->cliente_id !== '') OR (is_array($data->cliente_id) AND (!empty($data->cliente_id)) )) )
        {

            $filters[] = new TFilter('cliente_id', '=', $data->cliente_id);// create the filter 
        }

        if (isset($data->dt_pedido) AND ( (is_scalar($data->dt_pedido) AND $data->dt_pedido !== '') OR (is_array($data->dt_pedido) AND (!empty($data->dt_pedido)) )) )
        {

            $filters[] = new TFilter('dt_pedido', '=', $data->dt_pedido);// create the filter 
        }

        if (isset($data->departamento_unit_id) AND ( (is_scalar($data->departamento_unit_id) AND $data->departamento_unit_id !== '') OR (is_array($data->departamento_unit_id) AND (!empty($data->departamento_unit_id)) )) )
        {

            $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        }

        if (isset($data->centrocusto_id) AND ( (is_scalar($data->centrocusto_id) AND $data->centrocusto_id !== '') OR (is_array($data->centrocusto_id) AND (!empty($data->centrocusto_id)) )) )
        {

            $filters[] = new TFilter('centrocusto_id', '=', $data->centrocusto_id);// create the filter 
        }

        if (isset($data->valor_total) AND ( (is_scalar($data->valor_total) AND $data->valor_total !== '') OR (is_array($data->valor_total) AND (!empty($data->valor_total)) )) )
        {

            $filters[] = new TFilter('valor_total', '=', $data->valor_total);// create the filter 
        }

        if (isset($data->valor_total_cotacao) AND ( (is_scalar($data->valor_total_cotacao) AND $data->valor_total_cotacao !== '') OR (is_array($data->valor_total_cotacao) AND (!empty($data->valor_total_cotacao)) )) )
        {

            $filters[] = new TFilter('valor_total_cotacao', '=', $data->valor_total_cotacao);// create the filter 
        }

        if (isset($data->estado_pedido_venda_id) AND ( (is_scalar($data->estado_pedido_venda_id) AND $data->estado_pedido_venda_id !== '') OR (is_array($data->estado_pedido_venda_id) AND (!empty($data->estado_pedido_venda_id)) )) )
        {

            $filters[] = new TFilter('estado_pedido_venda_id', '=', $data->estado_pedido_venda_id);// create the filter 
        }

        if (isset($data->system_users_id) AND ( (is_scalar($data->system_users_id) AND $data->system_users_id !== '') OR (is_array($data->system_users_id) AND (!empty($data->system_users_id)) )) )
        {

            $filters[] = new TFilter('system_users_id', '=', $data->system_users_id);// create the filter 
        }

        // fill the form with data again
        $this->datagrid_form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        if (isset($param['static']) && ($param['static'] == '1') )
        {
            $class = get_class($this);
            $onReloadParam = ['offset' => 0, 'first_page' => 1, 'target_container' => $param['target_container'] ?? null];
            AdiantiCoreApplication::loadPage($class, 'onReload', $onReloadParam);
            TScript::create('$(".select2").prev().select2("close");');
        }
        else
        {
            $this->onReload(['offset' => 0, 'first_page' => 1]);
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

            $criteria->add(new TFilter('system_users_id', '=',TSession::getValue('userid') ));

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

        $object = new Pedido($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

     private function registrarHistoricoPedido($pedido)
    {

        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::COMPROPOSTA; 
        $hist->aprovador_id = TSession::getValue('userid');
        $hist->store();

    }
 private function registrarHistoricoCotacao($cotacao)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::AGUARDANDO; 
        $histcotacao->aprovador_id = TSession::getValue('userid');
        $histcotacao->store();
    }
private function gerarCotacoes($pedido)
    {

            $cotacao = new Cotacao();
            $cotacao->pedido_id = $pedido->id;
            $cotacao->pessoa_id = $pedido->cliente_id;
            $cotacao->data_cotacao = date('Y-m-d');
            $cotacao->estado_pedido_id = EstadoPedido::AGUARDANDO;
            $cotacao->system_users_id = TSession::getValue('iduser');
            $cotacao->valor_total = $pedido->valor_total_cotacao;
            $cotacao->cidade_id = $pedido->cidade_id;
            $cotacao->store();

            //gerar os ITENS_COTACAO
            $ic = ItensCotacao::where('cotacao_id','=',$cotacao->id)
                              ->load();

            if (empty($ic)) {

               $itenspedido = ItensPedido::where('pedido_venda_id','=',$pedido->id)
                                         ->load();
               if ($itenspedido)
               {
                  foreach ($itenspedido as $itensp)
                  {  
                      $produto = new Produto($itensp->produto_id);

                      $itenscotacao = new ItensCotacao();
                      $itenscotacao->produto_id = $itensp->produto_id;
                      $itenscotacao->qtde       = $itensp->quantidade;
                      $itenscotacao->cotacao_id = $cotacao->id;
                      $itenscotacao->valor      = $itensp->valor_cotacao;
                      $itenscotacao->valor_total= $itensp->valor_cotacao * $itensp->quantidade;
                      $itenscotacao->store();
                     // $itensp->valor = $produto->preco_venda;
                     // $itensp->valor_total = ($produto->preco_venda * $itensp->quantidade);
                     // $itensp->store();
                  }

               }
            }  

            $this->registrarHistoricoCotacao($cotacao);

            $codido_email_template_id =  EmailTemplate::PEDIDO_AGUARDANDO_APROVACAO; //PEDIDO ENVIADO
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

               //PedidoVendaService::notificarAprovador($pedido);

               //ENVIAR UM EMAIL PARA O GESTOR E NOTIFICAR O USUARIO QUE APROVA O PEDIDO
               $dep = new DepartamentoUnit($pedido->departamento_unit_id);

               if($dep->email)
                {

                    MailService::send($dep->email, $titulo, $mensagem,  'html');

                }
            }

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
        $pdf->SetXY(230,8);
        $pdf->Cell(70,5,utf8_decode('Relatório de pedidos de compra '),0,1,'C');
        $pdf->SetXY(420,8);
        $pdf->Cell(70,5,'Hora: '.date("H:i:s"),0,1,'C');
        $pdf->SetXY(500,8);
        $pdf->Cell(70,5,'Data: '.date("d/m/Y"),0,1,'C');
        $pdf->Ln(4);
        $pdf->SetXY(26,20);
        $pdf->Cell(70,5,$cnpj.'      '. $label,0,1,'L');
        $pdf->SetXY(115,20);
        $pdf->Cell(70,5,'',0,1,'L');
        $pdf->SetXY(500,20);
        $pdf->Cell(70,5,utf8_decode(' Página: ').$pag,0,1,'R');
        $pdf->Ln(1);
        //nome
        $pdf->Cell(0,5,"","B",1,'C');
        $pdf->SetXY(27,35);
        $pdf->Cell(70,5,'ID',0,1,'L');

        $pdf->SetXY(47,35);
        $pdf->Cell(70,5,'Data',0,1,'L');

        $pdf->SetXY(97,35);
        $pdf->Cell(70,5,'Nome',0,1,'L');

        $pdf->SetXY(207,35);
        $pdf->Cell(100,5,utf8_decode('Descrição do pedido'),0,1,'L');

        $pdf->SetXY(377,35);
        $pdf->Cell(70,5,'Estado Pedido',0,1,'L');

        $pdf->SetXY(445,35);
        $pdf->Cell(70,5,'Valor Pedido',0,1,'R');

         $pdf->SetXY(470,35);
         $pdf->Cell(100,5,'Valor Cotado',0,1,'R');
         //                123456789012 

        $pdf->ln(1);

        $pdf->Cell(0,4,"","B",1,'C');
        $linha = 12;
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

}

