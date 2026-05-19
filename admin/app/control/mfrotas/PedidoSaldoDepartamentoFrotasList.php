<?php

class PedidoSaldoDepartamentoFrotasList extends TPage
{

    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private static $database = 'minierp';
    private static $activeRecord = 'PedidoFrotas';
    private static $primaryKey = 'id';
    private static $formName = 'formList_PedidoFrotas';
    private $limit = 5;

    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->limit = 5;

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_descricaopedido = new TDataGridColumn('descricaopedido', "Descrição do pedido", 'left');
        $column_cliente_nome = new TDataGridColumn('estabelecimento->nome', "Estabelecimento", 'left');
        $column_departamento_unit_name = new TDataGridColumn('departamento_unit->name', "Departamento", 'left');
        $column_dt_pedido_transformed = new TDataGridColumn('dt_pedido', "Data", 'left');
        $column_dt_finalizacao_transformed = new TDataGridColumn('dt_finalizacao', "Dt finalização", 'left');
        $column_valor_liquido_cotacao_transformed = new TDataGridColumn('valor_liquido_proposta', "Valor liquido proposta", 'left');
        $column_estado_pedido_venda_nome = new TDataGridColumn('estado_pedido_frotas->nome', "Estado pedido", 'left');
        $column_system_users_name = new TDataGridColumn('system_users->name', "Usuário", 'left');
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
         $column_cliente_nome->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here

                TTransaction::open('minierp');
                if (TSession::getValue('aprovacao_por_item') == 1) {
                    $nomecliente = '';

                    $cot = Propostas::where('pedido_frotas_id', '=', $object->id)
                        ->where('estado_pedido_frotas_id', 'IN', [
                            EstadoPedidoFrotas::FINALIZADO,
                            EstadoPedidoFrotas::AGUARDANDO,
                            EstadoPedidoFrotas::APROVADO,
                            EstadoPedidoFrotas::PGTOAPROVADO,
                            EstadoPedidoFrotas::COMPROPOSTA,
                            EstadoPedidoFrotas::ENTREGUE,
                            EstadoPedidoFrotas::REVISAO,
                            EstadoPedidoFrotas::PREAPROVADO
                        ])
                        ->load();
                    if ($cot) {
                        foreach ($cot as $cots) {
                            $pessoa = new Pessoa($cots->pessoa_id);
                            $nomecliente .= $pessoa->nome . '; ';
                        }
                        $nomecliente = rtrim($nomecliente, ', ');
                    } else {
                        $nomecliente = "";
                    }
                } else {
                    $pessoa = new Pessoa($object->cliente_id);
                    $nomecliente = $pessoa->nome;
                }



                TTransaction::close();
                return $nomecliente;

        });  
        $column_estado_pedido_venda_nome->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
            $temnotafiscal = false;

            if ($object->estado_pedido_frotas::FINALIZADO || $object->estado_pedido_frotas::APROVADO || $object->estado_pedido_frotas::PGTOAPROVADO || $object->estado_pedido_frotas::ENTREGUE) {
                // var_dump($object);
            //die();  
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
                   //     break;
                    }
                }

                TTransaction::close();
            }
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
        $column_dt_pedido_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
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
            if(!empty(trim((string) $value)))
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

        $column_valor_liquido_cotacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if ($object->valor_liquido_proposta === null || $object->valor_liquido_proposta === '')
            {
                $value = $object->valor_total_proposta;
            }
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

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricaopedido);
        $this->datagrid->addColumn($column_cliente_nome);
        $this->datagrid->addColumn($column_departamento_unit_name);
        $this->datagrid->addColumn($column_dt_pedido_transformed);
        $this->datagrid->addColumn($column_dt_finalizacao_transformed);
        $this->datagrid->addColumn($column_valor_liquido_cotacao_transformed);
        $this->datagrid->addColumn($column_estado_pedido_venda_nome);
        $this->datagrid->addColumn($column_system_users_name);
        $this->datagrid->addColumn($column_cidade_id);

        // $action_onDelete = new TDataGridAction(array('PedidoSaldoDepartamentoFrotasList', 'onImprimirPedido'));
        // $action_onDelete->setUseButton(false);
        // $action_onDelete->setButtonClass('btn btn-default btn-sm');
        // $action_onDelete->setLabel("Orçamento");
        // $action_onDelete->setImage('far:file-pdf #000000');
        // $action_onDelete->setField(self::$primaryKey);

        // $this->datagrid->addAction($action_onDelete);

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
                $object = new PedidoFrotas($key, FALSE); 

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
            // creates a criteria
            $criteria = new TCriteria;

            if (empty($param['order']))
            {
                $param['order'] = 'dt_pedido';    
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
              if (!empty($param['saldo_departamento_id'])) {
                $criteria->add(new TFilter('saldo_departamento_id','=',$param['saldo_departamento_id']));
            }
            $criteria->add(new TFilter('system_unit_id','=',TSession::getValue('idunit')));
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
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
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

        $object = new PedidoFrotas($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

