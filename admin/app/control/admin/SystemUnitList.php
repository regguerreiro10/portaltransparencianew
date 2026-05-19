<?php

class SystemUnitList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'SystemUnit';
    private static $primaryKey = 'id';
    private static $formName = 'formList_SystemUnit';
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
        $this->form->setFormTitle("Listagem dos Orgãos / Unidades / Dep / Secretárias");
        $this->limit = 20;

        $criteria_cidade_id = new TCriteria();

        $id = new TEntry('id');
        $cnpj = new TEntry('cnpj');
        $name = new TEntry('name');
        $email = new TEntry('email');
        $cidade_id = new TDBCombo('cidade_id', 'minierp', 'Cidade', 'id', '{nome} -{estado->sigla}','nome asc' , $criteria_cidade_id );
        $bairro = new TEntry('bairro');


        $cidade_id->enableSearch();
        $id->setSize(100);
        $cnpj->setSize('100%');
        $name->setSize('100%');
        $email->setSize('100%');
        $bairro->setSize('100%');
        $cidade_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null)],[$id],[new TLabel("CNPJ:", null, '14px', null)],[$cnpj]);
        $row2 = $this->form->addFields([new TLabel("Nome:", null, '14px', null)],[$name],[new TLabel("Email:", null, '14px', null)],[$email]);
        $row3 = $this->form->addFields([new TLabel("Cidade:", null, '14px', null)],[$cidade_id],[new TLabel("Bairro:", null, '14px', null)],[$bairro]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        $btn_onshow = $this->form->addAction("Novo", new TAction(['SystemUnitForm', 'onShow']), 'fas:plus #69aa46');
        $this->btn_onshow = $btn_onshow;

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
        $column_cnpj = new TDataGridColumn('cnpj', "Cnpj", 'left');
        $column_name = new TDataGridColumn('name', "Nome", 'left');
        $column_bairro = new TDataGridColumn('bairro', "Bairro", 'left');
        $column_cidade_nome_cidade_estado_sigla = new TDataGridColumn('{cidade->nome} -{cidade->estado->sigla}', "Cidade", 'left');
        $column_email = new TDataGridColumn('email', "Email", 'left');

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_cnpj);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_bairro);
        $this->datagrid->addColumn($column_cidade_nome_cidade_estado_sigla);
        $this->datagrid->addColumn($column_email);

        $action_onEdit = new TDataGridAction(array('SystemUnitForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('SystemUnitList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('far:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

        // $action_onSaldo = new TDataGridAction(array('SaldoDepartamentoList', 'onSaldo'));
        // $action_onSaldo->setUseButton(false);
        // $action_onSaldo->setButtonClass('btn btn-default btn-sm');
        // $action_onSaldo->setLabel("Saldo Empenho");
        // $action_onSaldo->setImage('far:trash-alt #dd5a43');
        // $action_onSaldo->setField(self::$primeiraKey);

        // $this->datagrid->addAction($action_onSaldo);

        // create the datagrid model
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

        $panel->addFooter($this->pageNavigation);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
    //        $container->add(TBreadCrumb::create(["admin","Orgãos / Unidades / Dep / Secretárias"]));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

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

                $relations = [
                    'DepartamentoUnit' => ['column' => 'system_unit_id', 'alias' => 'Departamento Unidade']
                ];

                foreach ($relations as $model => $info)
                {
                    $repository = new TRepository($model);
                    $criteria = new TCriteria;
                    $criteria->add(new TFilter($info['column'], '=', $key));
                    $count = $repository->count($criteria);

                    if ($count > 0)
                    {
                        throw new Exception("Não é possível excluir. Existem registros relacionados em {$info['alias']}");
                    }
                }

                // instantiates object
                $object = new SystemUnit($key, FALSE); 

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

        if (isset($data->cnpj) AND ( (is_scalar($data->cnpj) AND $data->cnpj !== '') OR (is_array($data->cnpj) AND (!empty($data->cnpj)) )) )
        {

            $filters[] = new TFilter('cnpj', 'like', "%{$data->cnpj}%");// create the filter 
        }

        if (isset($data->name) AND ( (is_scalar($data->name) AND $data->name !== '') OR (is_array($data->name) AND (!empty($data->name)) )) )
        {

            $filters[] = new TFilter('name', 'like', "%{$data->name}%");// create the filter 
        }

        if (isset($data->email) AND ( (is_scalar($data->email) AND $data->email !== '') OR (is_array($data->email) AND (!empty($data->email)) )) )
        {

            $filters[] = new TFilter('email', 'like', "%{$data->email}%");// create the filter 
        }

        if (isset($data->cidade_id) AND ( (is_scalar($data->cidade_id) AND $data->cidade_id !== '') OR (is_array($data->cidade_id) AND (!empty($data->cidade_id)) )) )
        {

            $filters[] = new TFilter('cidade_id', '=', $data->cidade_id);// create the filter 
        }

        if (isset($data->bairro) AND ( (is_scalar($data->bairro) AND $data->bairro !== '') OR (is_array($data->bairro) AND (!empty($data->bairro)) )) )
        {

            $filters[] = new TFilter('bairro', 'like', "%{$data->bairro}%");// create the filter 
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

            // creates a repository for SystemUnit
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
            $criteria->add(new TFilter('id', '=', TSession::getValue('idunit')));
            // load the objects according to criteria
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

    // public function onReload($param = NULL)
    // {
    //     try
    //     {
    //         TTransaction::open(self::$database);

    //         $repository = new TRepository(self::$activeRecord);
    //         $criteria = clone $this->filter_criteria;

    //         if (empty($param['order'])) {
    //             $param['order'] = 'id';
    //         }

    //         if (empty($param['direction'])) {
    //             $param['direction'] = 'desc';
    //         }

    //         $criteria->setProperties($param);
    //         $criteria->setProperty('limit', $this->limit);

    //         if ($filters = TSession::getValue(__CLASS__ . '_filters')) {
    //             foreach ($filters as $filter) {
    //                 $criteria->add($filter);
    //             }
    //         }

    //         $objects = $repository->load($criteria, FALSE);

    //         $this->datagrid->clear();

    //         if ($objects) {
    //             foreach ($objects as $object) {
    //                 $departamentos = DepartamentoUnit::where('system_unit_id', '=', $object->id)->load();

    //                 $total_empenho = 0;
    //                 $total_saldo = 0;

    //                 foreach ($departamentos as $dep) {
    //                     $total_empenho += (float) $dep->valor_empenho;

    //                     $saldos = SaldoDepartamento::where('departamento_unit_id', '=', $dep->id)->load();

    //                     foreach ($saldos as $saldo) {
    //                         $valor = (float) $saldo->valor;

    //                         if ($saldo->tipotransacao == 'C') {
    //                             $total_saldo += $valor;
    //                         } elseif ($saldo->tipotransacao == 'D') {
    //                             $total_saldo -= $valor;
    //                         }
    //                     }
    //                 }

    //                 $object->valor_total_empenho = number_format($total_empenho, 2, ',', '.');
    //                 $object->valor_total_saldo   = number_format($total_saldo, 2, ',', '.');

    //                 $row = $this->datagrid->addItem($object);
    //                 $row->id = "row_{$object->id}";
    //             }
    //         }

    //         $criteria->resetProperties();
    //         $count = $repository->count($criteria);

    //         $this->pageNavigation->setCount($count);
    //         $this->pageNavigation->setProperties($param);
    //         $this->pageNavigation->setLimit($this->limit);

    //         TTransaction::close();
    //         $this->loaded = true;

    //         return $objects;
    //     }
    //     catch (Exception $e)
    //     {
    //         new TMessage('error', $e->getMessage());
    //         TTransaction::rollback();
    //     }
    // }
    public function onAutenticarUsuario($param = null) 
    {

            if (isset($param['confirmEnviarCotacao']) && $param['confirmEnviarCotacao']) {
            try {
             
                TTransaction::open(self::$database);
               // $conexao   = TTransaction::get(); 
                //$conexao->exec( "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));" );

                $pedido = new PedidoFrotas($param['id'], false);

                $unit = new SystemUnit($pedido->system_unit_id);
                if ($unit->testar_valor_venal == 1) {
                    $autorizacaopedido = AutorizacaoPedido::where('pedido_frotas_id', '=', $pedido->id)
                                                           ->load();

                    $total = self::onVerificaValorVenal($pedido); // se estiver em método estático

                    if ($total && count($autorizacaopedido) == 0) {
                        throw new Exception("Este veículo já ultrapassou 40% do valor venal em manutenções. É necessária autorização especial para encaminhar propostas para as redes credenciadas.");
                    }
                }

                //verifica se é um clliente que abriu o pedido atraves do usuario logado
                $pessoass = Pessoa::where('system_user_id','=',TSession::getValue('userid'))
                                  ->where('id','not in', '(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '.GrupoPessoa::CONDUTOR.')')
                                  ->load();
                  
                $repository = new TRepository('PedidoAsCliente'); 
                $criteria = new TCriteria;
                  
              
                if (!$pessoass) {
                    $criteria->add(new TFilter('pedido_frotas_id', '=', $pedido->id), TExpression::AND_OPERATOR);
                    $fornecedores = $repository->load($criteria);
                } else {
                    $criteria->add(new TFilter('pedido_frotas_id', '=', $pedido->id), TExpression::AND_OPERATOR);
                    $criteria->add(new TFilter('pessoa_id', '=', $pessoass[0]->id), TExpression::AND_OPERATOR);
                    $fornecedores = $repository->load($criteria);
                }
               
                
                if ($fornecedores) { 

                    $this->gerarCotacoes($fornecedores, $pedido);

                    if (in_array($pedido->estado_pedido_frotas_id, [EstadoPedidoFrotas::PENDENTE, EstadoPedidoFrotas::ENVIADO]) ){
                        
                        // Atualiza o status do pedido e registra histórico
                        //var_dump($criteria);
                        $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::ENVIADO;
                        $pedido->store();

                         $this->registrarHistoricoPedidoFrotas($pedido);

                        //   $this->atualizregistrarHistoricoPedidoaDetalhesPedido($pedido);
                    }

                    TToast::show('success', "Emails enviados!!", 'topRight', 'far:check-circle');
                    TApplication::loadPage('PedidoFrotasList', 'onSetProject');
                } else {
                    new TMessage('info', 'Sr(a) Usuário checar as Redes que deseja enviar as propostas !');
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

            new TQuestion('Tem certeza que deseja Gerar a Proposta para Cotação?', $action);
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

        $object = new SystemUnit($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

