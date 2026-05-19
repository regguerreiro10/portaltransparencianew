<?php

class ItensPedidoFrotasProdutoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'ItensPedidoFrotas';
    private static $primaryKey = 'id';
    private static $formName = 'form_ItensPedidoFrotasProdutoList';
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
        // $this->form->setFormTitle("Listagem de itens pedido frotas");
        $this->limit = 20;

        $pedido_frotas_id = new THidden('pedido_frotas_id');
        $id = new THidden('id');


        $id->setSize(200);
        $pedido_frotas_id->setSize(200);

        $row1 = $this->form->addFields([$pedido_frotas_id,$id],[]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "ID", 'center' , '70px');
        $column_pedido_frotas_id = new TDataGridColumn('pedido_frotas_id', "ID Pedido", 'left', '70px');
        $column_tipo = new TDataGridColumn('tipo', "Tipo", 'left', '70px');
        $column_familia_produto_nome = new TDataGridColumn('familia_produto_id', "Grupo", 'left', '150px');
        $column_produto_id = new TDataGridColumn('produto_id', "ID Produto", 'left', '70px');
        $column_produto_nome = new TDataGridColumn('produto->nome', "Nome do Produto", 'left', '400px');
        $column_qtde = new TDataGridColumn('qtde', "Qtde", 'left', '70px');
        $column_descricao = new TDataGridColumn('descricao', "Obs", 'left', '70px');
        $column_tipo->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if ($object->tipo == 1) {
                return "<span style='background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 8px; font-weight: bold;'>Produto</span>";
            } else {
                return "<span style='background-color: #2196F3; color: white; padding: 2px 8px; border-radius: 8px; font-weight: bold;'>Serviço</span>";
            }
        });
        $column_familia_produto_nome->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (!empty($object->familia_produto_id)) {
                try {
                    return (new FamiliaProduto($object->familia_produto_id))->nome;
                } catch (Exception $e) {
                }
            }

            if (!empty($object->produto_id)) {
                try {
                    $produto = new Produto($object->produto_id);
                    if (!empty($produto->familia_produto_id)) {
                        return (new FamiliaProduto($produto->familia_produto_id))->nome;
                    }
                } catch (Exception $e) {
                }
            }

            return '';
        });
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_pedido_frotas_id);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_familia_produto_nome);
        $this->datagrid->addColumn($column_produto_id);
        $this->datagrid->addColumn($column_produto_nome);
        $this->datagrid->addColumn($column_qtde);
        $this->datagrid->addColumn($column_descricao);

        $action_onEdit = new TDataGridAction(array('ItensPedidoFrotasProdutoForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('ItensPedidoFrotasProdutoList', 'onDelete'));
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

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $this->datagrid_form->add($headerActions);

        // $Cadastrar = new TButton('button_Cadastrar');
        // $Cadastrar->setAction(new TAction(['PedidoFrotasForm', 'onCadastrarItem']), "Cadastrar");
        // $Cadastrar->addStyleClass('btn-default');
        // $Cadastrar->setImage('fas:plus #69AA46');

        // $this->datagrid_form->addField($Cadastrar);

        // $head_left_actions->add($Cadastrar);

        $this->datagrid_form->add($this->datagrid);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
      //      $container->add(TBreadCrumb::create(["Manutenção Frotas","Itens pedido frotas"]));
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
                $object = new ItensPedidoFrotas($key, FALSE); 

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

        if (isset($data->pedido_frotas_id) AND ( (is_scalar($data->pedido_frotas_id) AND $data->pedido_frotas_id !== '') OR (is_array($data->pedido_frotas_id) AND (!empty($data->pedido_frotas_id)) )) )
        {

            $filters[] = new TFilter('pedido_frotas_id', '=', $data->pedido_frotas_id);// create the filter 
        }

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
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

            // creates a repository for ItensPedidoFrotas
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
            
                $criteria->add(new TFilter('pedido_frotas_id','=',TSession::getValue('pedido_frotas_id')));
                $criteria->add(new TFilter('tipo','=',1));
            

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

        $object = new ItensPedidoFrotas($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }
    

}
