<?php

class ViewDotacaoPedidoFrotasList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'ViewDotacaoPedidoFrotas';
    private static $primaryKey = 'saldo_departamento_id';
    private static $formName = 'form_ViewDotacaoPedidoFrotasList';
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
      //  $this->form->setFormTitle("ViewDotacaoPedidoFrotasList");
        $this->limit = 20;


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

        $column_pedido_frotas_id = new TDataGridColumn('pedido_frotas_id', "Id Pedido", 'left');
        $column_propostas_id = new TDataGridColumn('propostas_id', "Id Proposta", 'left');
        $column_descricaopedido = new TDataGridColumn('descricaopedido', "Descrição do pedido", 'left');
        $column_departamento_unit_id = new TDataGridColumn('departamento_unit->name', "Departamento", 'left');
        $column_estabelecimento_id = new TDataGridColumn('estabelecimento->nome', "Estabelecimento", 'left');
        $column_dt_pedido = new TDataGridColumn('dt_pedido', "Dt pedido", 'left');
        $column_dt_finalizacao = new TDataGridColumn('dt_finalizacao', "Dt finalização", 'left');
        $column_veiculos_id = new TDataGridColumn('veiculos->placa', "Veiculos", 'left');
        $column_km = new TDataGridColumn('km', "Km", 'left');
        $column_valor_liquido_proposta = new TDataGridColumn('valor_liquido_proposta', "Valor liquido proposta", 'left');
        $column_valor = new TDataGridColumn('valor', "Valor", 'left');
        $column_saldo_atual = new TDataGridColumn('saldo_atual', "Saldo atual", 'left');
        $column_estado_pedido_frotas_id = new TDataGridColumn('estado_pedido_frotas_id', "Estado pedido", 'left');
        $column_system_users_id = new TDataGridColumn('system_users->name', "Usuário", 'left');
        $column_cidade_id = new TDataGridColumn('cidade_id', "Cidade", 'left');
        $column_cidade_id->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here

                TTransaction::open('minierp');

                $cidade = new Cidade($object->cidade_id);
                $nomecidade = '';
                if ($cidade) {
                    $estado = new Estado($cidade->estado_id);
                    $nomecidade = "{$cidade->nome} - {$estado->sigla}";

                } else {
                    $nomecidade = "Não informado!!!";

                }

                TTransaction::close();
                return $nomecidade;

        }); 
        $column_valor_liquido_proposta->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_valor->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_saldo_atual->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_dt_pedido->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y h:i:s');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });
        $column_dt_finalizacao->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y h:i:s');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });
        $column_estado_pedido_frotas_id->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
              //code here
            $temnotafiscal = false;

            if ($object->estado_pedido_frotas_id==EstadoPedidoFrotas::FINALIZADO || $object->estado_pedido_frotas_id==EstadoPedidoFrotas::APROVADO || $object->estado_pedido_frotas_id==EstadoPedidoFrotas::PGTOAPROVADO || $object->estado_pedido_frotas_id==EstadoPedidoFrotas::ENTREGUE) {
                TTransaction::open('minierp');

                $cot = Propostas::where('pedido_frotas_id','=',$object->id)
                                ->load();

                if ($cot)
                {
                    foreach ($cot as $cots) {
                        $doccot = DocumentosPropostas::where('propostas_id','=',$cots->id)
                                                   ->load();
                        if ($doccot){
                            $temnotafiscal = true;
                        }
                    }
                }

                TTransaction::close();
            }
                            //entrou em revisão
            $revisao = '';
            if ($object->estado_pedido_frotas1_id !== null) {
                $estadorevisao = new EstadoPedidoFrotas($object->estado_pedido_frotas1_id);
                $revisao = "<span style='font-size: 10px; font-style: italic; color: #eee;'>({$estadorevisao->nome})</span>";
            }

            if ($temnotafiscal) {
                $anexo = $object->estado_pedido_frotas->nome . " <i class='fa fa-paperclip' aria-hidden='true'></i>";
            } else {
                $anexo = $object->estado_pedido_frotas->nome;
            }

            return "<span class='label label-default' style='width:260px; background-color:{$object->estado_pedido_frotas->cor}; display:inline-block;'> {$anexo} {$revisao} </span>";

        });   
          $order_saldo_atual = new TAction(array($this, 'onReload'));
        $order_saldo_atual->setParameter('order', 'saldo_atual');
        $column_saldo_atual->setAction($order_saldo_atual);     
        $this->datagrid->addColumn($column_pedido_frotas_id);
        $this->datagrid->addColumn($column_propostas_id);
        $this->datagrid->addColumn($column_descricaopedido);
        $this->datagrid->addColumn($column_departamento_unit_id);
        $this->datagrid->addColumn($column_estabelecimento_id);
        $this->datagrid->addColumn($column_dt_pedido);
        $this->datagrid->addColumn($column_dt_finalizacao);
        $this->datagrid->addColumn($column_veiculos_id);
        $this->datagrid->addColumn($column_km);
        $this->datagrid->addColumn($column_valor_liquido_proposta);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_saldo_atual);
        $this->datagrid->addColumn($column_estado_pedido_frotas_id);
        $this->datagrid->addColumn($column_system_users_id);
        $this->datagrid->addColumn($column_cidade_id);

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
            // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

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

            // creates a repository for ViewDotacaoPedidoFrotas
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'saldo_departamento_id, id';    
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

            if (!empty(TSession::getValue('saldo_departamento_id'))) {
                $criteria->add(new TFilter('saldo_departamento_id','=',TSession::getValue('saldo_departamento_id')));
            }
            
            if (!empty(TSession::getValue('datatransacao')) && !empty(TSession::getValue('datatransacaof'))) {

                $dtIni = TSession::getValue('datatransacao');
                $dtFim = TSession::getValue('datatransacaof');

                $criteria->add(new TFilter(
                    'pedido_frotas_id',
                    'in',
                    "(SELECT id
                        FROM pedido_frotas
                    WHERE dt_finalizacao >= '{$dtIni}'
                        AND dt_finalizacao <= '{$dtFim}')"
                ));
            }

            $criteria->add(new TFilter('system_unit_id','=',TSession::getValue('idunit')));
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
                    $row->id = "row_{$object->saldo_departamento_id}";

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

        $object = new ViewDotacaoPedidoFrotas($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->saldo_departamento_id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

