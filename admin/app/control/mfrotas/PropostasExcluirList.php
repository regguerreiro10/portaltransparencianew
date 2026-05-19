<?php

class PropostasExcluirList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Propostas';
    private static $primaryKey = 'id';
    private static $formName = 'form_PropostasExcluirList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters', 'onGlobalSearch'];
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
        $this->form->setFormTitle("Listagem de propostass");
        $this->limit = 20;

        $criteria_pedido_frotas_id = new TCriteria();
        $criteria_pessoa_id = new TCriteria();
        $criteria_estado_pedido_frotas_id = new TCriteria();

        $id = new TEntry('id');
        $pedido_frotas_id = new TDBCombo('pedido_frotas_id', 'minierp', 'PedidoFrotas', 'id', '{id}','id asc' , $criteria_pedido_frotas_id );
        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa_id );
        $estado_pedido_frotas_id = new TDBCombo('estado_pedido_frotas_id', 'minierp', 'EstadoPedidoFrotas', 'id', '{id}','id asc' , $criteria_estado_pedido_frotas_id );


        $pessoa_id->enableSearch();
        $pedido_frotas_id->enableSearch();
        $estado_pedido_frotas_id->enableSearch();

        $id->setSize(100);
        $pessoa_id->setSize('100%');
        $pedido_frotas_id->setSize('100%');
        $estado_pedido_frotas_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Pedido frotas id:", null, '14px', null, '100%'),$pedido_frotas_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Pessoa id:", null, '14px', null, '100%'),$pessoa_id],[new TLabel("Estado pedido frotas id:", null, '14px', null, '100%'),$estado_pedido_frotas_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 


        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_pedido_frotas_id = new TDataGridColumn('pedido_frotas_id', "Pedido frotas id", 'left');
        $column_pessoa_nome = new TDataGridColumn('pessoa->nome', "Pessoa id", 'left');
        $column_estado_pedido_frotas_nome = new TDataGridColumn('estado_pedido_frotas->nome', "Estado pedido frotas id", 'left');
        $column_cidade_nome = new TDataGridColumn('cidade->nome', "Cidade id", 'left');
        $column_data_cotacao = new TDataGridColumn('data_cotacao', "Data cotacao", 'left');
        $column_obs = new TDataGridColumn('obs', "Obs", 'left');
        $column_valor_total = new TDataGridColumn('valor_total', "Valor total", 'left');
        $column_valor_desconto = new TDataGridColumn('valor_desconto', "Valor desconto", 'left');
        $column_valor_liquido = new TDataGridColumn('valor_liquido', "Valor liquido", 'left');
        $column_system_unit_name = new TDataGridColumn('system_unit->name', "System unit id", 'left');
        $column_departamento_unit_name = new TDataGridColumn('departamento_unit->name', "Departamento unit id", 'left');
        $column_system_users_name = new TDataGridColumn('system_users->name', "System users id", 'left');
        $column_veiculos_id = new TDataGridColumn('veiculos_id', "Veiculos id", 'left');
        $column_modelo = new TDataGridColumn('modelo', "Modelo", 'left');
        $column_placa = new TDataGridColumn('placa', "Placa", 'left');
        $column_data_entrada_veiculo = new TDataGridColumn('data_entrada_veiculo', "Data entrada veiculo", 'left');
        $column_horimetro_entrada_aeronave = new TDataGridColumn('horimetro_entrada_aeronave', "Horimetro entrada aeronave", 'left');
        $column_ciclos_entrada_aeronave = new TDataGridColumn('ciclos_entrada_aeronave', "Ciclos entrada aeronave", 'left');
        $column_data_retirada_veiculo = new TDataGridColumn('data_retirada_veiculo', "Data retirada veiculo", 'left');
        $column_horimetro_retirada_aeronave = new TDataGridColumn('horimetro_retirada_aeronave', "Horimetro retirada aeronave", 'left');
        $column_ciclos_retirada_aeronave = new TDataGridColumn('ciclos_retirada_aeronave', "Ciclos retirada aeronave", 'left');
        $column_data_previsao_entrega = new TDataGridColumn('data_previsao_entrega', "Data previsao entrega", 'left');
        $column_km = new TDataGridColumn('km', "Km", 'left');
        $column_ciclos = new TDataGridColumn('ciclos', "Ciclos", 'left');
        $column_created_at = new TDataGridColumn('created_at', "Created at", 'left');
        $column_updated_at = new TDataGridColumn('updated_at', "Updated at", 'left');
        $column_deleted_at = new TDataGridColumn('deleted_at', "Deleted at", 'left');
        $column_responsavel_tecnico = new TDataGridColumn('responsavel_tecnico', "Responsavel tecnico", 'left');
        $column_datahora_inicioservico = new TDataGridColumn('datahora_inicioservico', "Datahora inicioservico", 'left');
        $column_horimetro_inicioservico = new TDataGridColumn('horimetro_inicioservico', "Horimetro inicioservico", 'left');
        $column_ciclos_inicioservico = new TDataGridColumn('ciclos_inicioservico', "Ciclos inicioservico", 'left');
        $column_datahora_fimservico = new TDataGridColumn('datahora_fimservico', "Datahora fimservico", 'left');
        $column_horimetro_fimservico = new TDataGridColumn('horimetro_fimservico', "Horimetro fimservico", 'left');
        $column_ciclos_fimservico = new TDataGridColumn('ciclos_fimservico', "Ciclos fimservico", 'left');
        $column_total_produtos_sem_desconto = new TDataGridColumn('total_produtos_sem_desconto', "Total produtos sem desconto", 'left');
        $column_total_servicos_sem_desconto = new TDataGridColumn('total_servicos_sem_desconto', "Total servicos sem desconto", 'left');
        $column_total_geral_sem_desconto = new TDataGridColumn('total_geral_sem_desconto', "Total geral sem desconto", 'left');
        $column_total_produtos_com_desconto = new TDataGridColumn('total_produtos_com_desconto', "Total produtos com desconto", 'left');
        $column_total_servicos_com_desconto = new TDataGridColumn('total_servicos_com_desconto', "Total servicos com desconto", 'left');
        $column_desconto_contratual = new TDataGridColumn('desconto_contratual', "Desconto contratual", 'left');
        $column_motorista_entrada_nome = new TDataGridColumn('motorista_entrada->nome', "Motorista entrada id", 'left');
        $column_total_geral_com_desconto = new TDataGridColumn('total_geral_com_desconto', "Total geral com desconto", 'left');
        $column_entidade_id = new TDataGridColumn('entidade_id', "Entidade id", 'left');
        $column_motorista_retirada_nome = new TDataGridColumn('motorista_retirada->nome', "Motorista retirada id", 'left');
        $column_data_limite_resposta = new TDataGridColumn('data_limite_resposta', "Data limite resposta", 'left');
        $column_estado_pedido_frotas1_id = new TDataGridColumn('estado_pedido_frotas1_id', "Estado pedido frotas1 id", 'left');

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_pedido_frotas_id);
        $this->datagrid->addColumn($column_pessoa_nome);
        $this->datagrid->addColumn($column_estado_pedido_frotas_nome);
        $this->datagrid->addColumn($column_cidade_nome);
        $this->datagrid->addColumn($column_data_cotacao);
        $this->datagrid->addColumn($column_obs);
        $this->datagrid->addColumn($column_valor_total);
        $this->datagrid->addColumn($column_valor_desconto);
        $this->datagrid->addColumn($column_valor_liquido);
        $this->datagrid->addColumn($column_system_unit_name);
        $this->datagrid->addColumn($column_departamento_unit_name);
        $this->datagrid->addColumn($column_system_users_name);
        $this->datagrid->addColumn($column_veiculos_id);
        $this->datagrid->addColumn($column_modelo);
        $this->datagrid->addColumn($column_placa);
        $this->datagrid->addColumn($column_data_entrada_veiculo);
        $this->datagrid->addColumn($column_horimetro_entrada_aeronave);
        $this->datagrid->addColumn($column_ciclos_entrada_aeronave);
        $this->datagrid->addColumn($column_data_retirada_veiculo);
        $this->datagrid->addColumn($column_horimetro_retirada_aeronave);
        $this->datagrid->addColumn($column_ciclos_retirada_aeronave);
        $this->datagrid->addColumn($column_data_previsao_entrega);
        $this->datagrid->addColumn($column_km);
        $this->datagrid->addColumn($column_ciclos);
        $this->datagrid->addColumn($column_created_at);
        $this->datagrid->addColumn($column_updated_at);
        $this->datagrid->addColumn($column_deleted_at);
        $this->datagrid->addColumn($column_responsavel_tecnico);
        $this->datagrid->addColumn($column_datahora_inicioservico);
        $this->datagrid->addColumn($column_horimetro_inicioservico);
        $this->datagrid->addColumn($column_ciclos_inicioservico);
        $this->datagrid->addColumn($column_datahora_fimservico);
        $this->datagrid->addColumn($column_horimetro_fimservico);
        $this->datagrid->addColumn($column_ciclos_fimservico);
        $this->datagrid->addColumn($column_total_produtos_sem_desconto);
        $this->datagrid->addColumn($column_total_servicos_sem_desconto);
        $this->datagrid->addColumn($column_total_geral_sem_desconto);
        $this->datagrid->addColumn($column_total_produtos_com_desconto);
        $this->datagrid->addColumn($column_total_servicos_com_desconto);
        $this->datagrid->addColumn($column_desconto_contratual);
        $this->datagrid->addColumn($column_motorista_entrada_nome);
        $this->datagrid->addColumn($column_total_geral_com_desconto);
        $this->datagrid->addColumn($column_entidade_id);
        $this->datagrid->addColumn($column_motorista_retirada_nome);
        $this->datagrid->addColumn($column_data_limite_resposta);
        $this->datagrid->addColumn($column_estado_pedido_frotas1_id);

      

        $action_onDelete = new TDataGridAction(array('PropostasExcluirList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

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

        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $this->datagrid_form->add($this->datagrid);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Manutenção Frotas","Propostass"]));
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

                // instantiates object
                $object = new Propostas($key, FALSE); 

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

        if (isset($data->pedido_frotas_id) AND ( (is_scalar($data->pedido_frotas_id) AND $data->pedido_frotas_id !== '') OR (is_array($data->pedido_frotas_id) AND (!empty($data->pedido_frotas_id)) )) )
        {

            $filters[] = new TFilter('pedido_frotas_id', '=', $data->pedido_frotas_id);// create the filter 
        }

        if (isset($data->pessoa_id) AND ( (is_scalar($data->pessoa_id) AND $data->pessoa_id !== '') OR (is_array($data->pessoa_id) AND (!empty($data->pessoa_id)) )) )
        {

            $filters[] = new TFilter('pessoa_id', '=', $data->pessoa_id);// create the filter 
        }

        if (isset($data->estado_pedido_frotas_id) AND ( (is_scalar($data->estado_pedido_frotas_id) AND $data->estado_pedido_frotas_id !== '') OR (is_array($data->estado_pedido_frotas_id) AND (!empty($data->estado_pedido_frotas_id)) )) )
        {

            $filters[] = new TFilter('estado_pedido_frotas_id', '=', $data->estado_pedido_frotas_id);// create the filter 
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

            // creates a repository for Propostas
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'pedido_frotas_id, pessoa_id';    
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

    public static function manageRow($id, $param = [])
    {
        $list = new self($param);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Propostas($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

