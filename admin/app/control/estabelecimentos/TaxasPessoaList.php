<?php

class TaxasPessoaList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'TaxasPessoa';
    private static $primaryKey = 'id';
    private static $formName = 'form_TaxasPessoaList';
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
        $this->form->setFormTitle("Listagem de taxas pessoas");
        $this->limit = 20;

        $id = new THidden('id');


        $id->setSize(200);

        $row1 = $this->form->addFields([$id]);
        $row1->layout = ['col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onshow = $this->form->addAction("Cadastrar", new TAction(['TaxasPessoaForm', 'onShow']), 'fas:plus #69aa46');
        $this->btn_onshow = $btn_onshow;

          $btn_onshow = $this->form->addAction("Voltar", new TAction(['PessoaList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

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
        $column_entidade_id = new TDataGridColumn('entidade->nome', "Entidade", 'left');
        $column_system_unit_name = new TDataGridColumn('system_unit->name', "Unidade", 'left');
        $column_taxaadm = new TDataGridColumn('taxaadm', "Taxa Administrativa", 'left');
        $column_taxabancaria = new TDataGridColumn('taxabancaria', "Taxa Bancária", 'left');
        $column_taxaantecipacao = new TDataGridColumn('taxaantecipacao', "Taxa Antecipação", 'left');
   //     $column_taxacontrato = new TDataGridColumn('taxacontrato', "Taxa de contrato", 'left');
        // $column_taxadesconto = new TDataGridColumn('taxadesconto', "Taxa de desconto", 'left');
        $column_ir = new TDataGridColumn('ir', "Ir", 'left');
        $column_csll = new TDataGridColumn('csll', "Csll", 'left');
        $column_cofins = new TDataGridColumn('cofins', "Cofins", 'left');
        $column_pis = new TDataGridColumn('pis', "Pis", 'left');
        $column_iss = new TDataGridColumn('iss', "Iss", 'left');
        $column_ir_servico = new TDataGridColumn('ir_servico', "Ir servico", 'left');
        $column_csll_servico = new TDataGridColumn('csll_servico', "Csll servico", 'left');
        $column_cofins_servico = new TDataGridColumn('cofins_servico', "Cofins servico", 'left');
        $column_pis_servico = new TDataGridColumn('pis_servico', "Pis servico", 'left');
        $column_iss_servico = new TDataGridColumn('iss_servico', "Iss servico", 'left');

        $column_taxaadm->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });
        $column_taxabancaria->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });
        $column_taxaantecipacao->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });
    /*    $column_taxacontrato->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });*/
        // $column_taxadesconto->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        // {
        //     if(!$value)
        //     {
        //         $value = 0;
        //     }

        //     if(is_numeric($value))
        //     {
        //         return number_format($value, 2, ",", ".");
        //     }
        //     else
        //     {
        //         return $value;
        //     }
        // });

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_entidade_id);
        $this->datagrid->addColumn($column_system_unit_name);
        $this->datagrid->addColumn($column_taxaadm);
        $this->datagrid->addColumn($column_taxabancaria);
        $this->datagrid->addColumn($column_taxaantecipacao);
    //    $this->datagrid->addColumn($column_taxacontrato);
   //     $this->datagrid->addColumn($column_taxadesconto);
        $this->datagrid->addColumn($column_ir);
        $this->datagrid->addColumn($column_csll);
        $this->datagrid->addColumn($column_cofins);
        $this->datagrid->addColumn($column_pis);
        $this->datagrid->addColumn($column_iss);
        $this->datagrid->addColumn($column_ir_servico);
        $this->datagrid->addColumn($column_csll_servico);
        $this->datagrid->addColumn($column_cofins_servico);
        $this->datagrid->addColumn($column_pis_servico);
        $this->datagrid->addColumn($column_iss_servico);

        $action_onEdit = new TDataGridAction(array('TaxasPessoaForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('TaxasPessoaList', 'onDelete'));
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
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Estabelecimentos","Taxas pessoas"]));
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
                $object = new TaxasPessoa($key, FALSE); 

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

            // creates a repository for TaxasPessoa
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
            $criteria->add(new TFilter('pessoa_id', '=', TSession::getValue('idpessoa')));

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
    public function onSetProject($param = null)
    {
        TSession::setValue('idpessoa', null);
        TSession::setValue('idpessoa', $param['id']);
        $this->onReload();
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

        $object = new TaxasPessoa($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

