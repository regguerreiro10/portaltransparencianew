<?php

class ViewSaldodotacaoempenhoList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'ViewSaldoempenhocompras';
    private static $primaryKey = 'saldo_departamento_id';
    private static $formName = 'form_ViewSaldodotacaoempenhoList';
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
        $this->form->setFormTitle("Consulta Saldos de Dotações e Valores Empenhados");
        $this->limit = 20;

        $criteria_departamento_unit_id = new TCriteria();
                $criteria_estado_pedido_venda_id = new TCriteria();

$filterVar = TSession::getValue("userid");
        $criteria_departamento_unit_id->add(new TFilter('system_users_id', '=', $filterVar));

        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'SystemUserDepartamentoUnit', 'departamento_unit_id', '{departamento_unit->system_unit->name}   - {departamento_unit->name}','departamento_unit_id asc' , $criteria_departamento_unit_id );
        $estado_pedido_venda_id = new TDBSelect('estado_pedido_venda_id', 'minierp', 'EstadoPedido', 'id', '{nome}','nome asc' , $criteria_estado_pedido_venda_id );
        $mes = new TEntry('mes');
        $ano = new TEntry('ano');
        $datatransacao = new BDateRange('datatransacao', 'datatransacaof');


        $departamento_unit_id->enableSearch();
        $datatransacao->setMask('dd/mm/yyyy');
        $datatransacao->setDatabaseMask('yyyy-mm-dd');
        $mes->setSize('53%');
        $ano->setSize('53%');
        $datatransacao->setSize(220);
        $departamento_unit_id->setSize('100%');
        $estado_pedido_venda_id->setSize('100%');
        $estado_pedido_venda_id->enableSearch();
        $estado_pedido_venda_id->setSize('100%', 70);

        $row1 = $this->form->addFields([new TLabel("Departamento:", null, '14px', null, '100%'),$departamento_unit_id],[new TLabel("Status", null, '14px', null, '100%'),$estado_pedido_venda_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Mes:", null, '14px', null, '100%'),$mes],[new TLabel("Ano:", null, '14px', null, '100%'),$ano],[new TLabel("Período data transação:", null, '14px', null, '100%'),$datatransacao]);
        $row2->layout = [' col-sm-3',' col-sm-3',' col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_saldo_departamento_id = new TDataGridColumn('saldo_departamento_id', "ID", 'center' , '70px');
        $column_datatransacao_transformed = new TDataGridColumn('datatransacao', "Data transação", 'left');
        $column_departamento_unit_name = new TDataGridColumn('departamento_unit_id', "Departamento", 'left');
        $column_mes = new TDataGridColumn('mes', "Mes", 'left');
        $column_ano = new TDataGridColumn('ano', "Ano", 'left');
        $column_total_produtos_transformed = new TDataGridColumn('total_produtos', "Total de produtos", 'left');
        $column_saldo_empenho_transformed = new TDataGridColumn('saldo_empenho', "Saldo de empenho", 'left');
        $column_saldoatual_transformed = new TDataGridColumn('saldoatual', "Saldo atual", 'left');
        $column_numero_documento_empenho = new TDataGridColumn('numero_documento_empenho', "Nº Empenho", 'left');
        $column_estado_pedido_venda_id_transformed = new TDataGridColumn('estado_pedido_venda_id', "Estado pedido", 'left');
        $column_documento_empenho_transformed = new TDataGridColumn('documento_empenho', "Documento empenho", 'left');
        
        $column_documento_empenho_transformed->setTransformer(function($value, $object)
        {
            if (!empty($object->documento_empenho)) {
                $path = $object->documento_empenho;

                if ($path) {
                    return "<a href='{$path}' target='_blank' style='margin:0px; background: rgba(131, 186, 238, 0.81); color: rgba(121, 121, 121, 0.81); padding: 0px 8px;  border-radius: 100px;'>
                                <i class='fas fa-file-pdf'></i> Baixar
                            </a>";
                } 
            } 
        });
         $column_departamento_unit_name->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
                TTransaction::open('minierp');
                if ($object->departamento_unit_id<>'TOTAL GERAL') {
                    $dep = new DepartamentoUnit($object->departamento_unit_id);
                    if ($dep) {
                    return $dep->name;
                    } else {
                    return '';

                    }
                }
                return '';
                TTransaction::close();
        });
        $column_datatransacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_total_produtos_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_saldo_empenho_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_saldoatual_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_estado_pedido_venda_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if ($object->estado_pedido_venda_id=='') {return '';}
            //code here
            $temnotafiscal = false;

            if (in_array($object->estado_pedido_venda_id, [EstadoPedido::FINALIZADO, EstadoPedido::APROVADO, EstadoPedido::PGTOAPROVADO, EstadoPedido::ENTREGUE])) {
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
            $estado_pedido_venda = new EstadoPedido($object->estado_pedido_venda_id);
            if ($temnotafiscal) {
               $anexo = $estado_pedido_venda->nome.' <i class="fa fa-paperclip" aria-hidden="true"></i>';
                return "<span class='label label-default' style='width:240px; background-color:{$estado_pedido_venda->cor}'> {$anexo} <span>";
            } else {
                return "<span class='label label-default' style='width:240px; background-color:{$estado_pedido_venda->cor}'> {$estado_pedido_venda->nome} <span>";
            }

        });

          

        $order_saldo_departamento_id = new TAction(array($this, 'onReload'));
        $order_saldo_departamento_id->setParameter('order', 'saldo_departamento_id');
        $column_saldo_departamento_id->setAction($order_saldo_departamento_id);

        $this->datagrid->addColumn($column_saldo_departamento_id);
        $this->datagrid->addColumn($column_datatransacao_transformed);
        $this->datagrid->addColumn($column_departamento_unit_name);
        $this->datagrid->addColumn($column_numero_documento_empenho);
        $this->datagrid->addColumn($column_mes);
        $this->datagrid->addColumn($column_ano);

        $this->datagrid->addColumn($column_saldo_empenho_transformed);
        $this->datagrid->addColumn($column_total_produtos_transformed);
        $this->datagrid->addColumn($column_saldoatual_transformed);
        $this->datagrid->addColumn($column_estado_pedido_venda_id_transformed);
        $this->datagrid->addColumn($column_documento_empenho_transformed);

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

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['ViewSaldodotacaoempenhoList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['ViewSaldodotacaoempenhoList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['ViewSaldodotacaoempenhoList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['ViewSaldodotacaoempenhoList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );
        $dropdown_button_exportar->addPostAction( "HTML", new TAction(['ViewSaldodotacaoempenhoList', 'onExportHtml'],['static' => 1]), 'datagrid_'.self::$formName, 'fab:html5 #E34F26' );

        $head_right_actions->add($dropdown_button_exportar);

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
            $output = 'app/output/'.uniqid().'.pdf';

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
                $object = new TElement('iframe');
                $object->src  = $output;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($object);
                $window->show();
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

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->departamento_unit_id) AND ( (is_scalar($data->departamento_unit_id) AND $data->departamento_unit_id !== '') OR (is_array($data->departamento_unit_id) AND (!empty($data->departamento_unit_id)) )) )
        {

            $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        }

        if (isset($data->estado_pedido_venda_id) AND ( (is_scalar($data->estado_pedido_venda_id) AND $data->estado_pedido_venda_id !== '') OR (is_array($data->estado_pedido_venda_id) AND (!empty($data->estado_pedido_venda_id)) )) )
        {

            $filters[] = new TFilter('estado_pedido_venda_id', 'in', $data->estado_pedido_venda_id);// create the filter 
        }        
        if (isset($data->mes) AND ( (is_scalar($data->mes) AND $data->mes !== '') OR (is_array($data->mes) AND (!empty($data->mes)) )) )
        {

            $filters[] = new TFilter('mes', '=', $data->mes);// create the filter 
        }

        if (isset($data->ano) AND ( (is_scalar($data->ano) AND $data->ano !== '') OR (is_array($data->ano) AND (!empty($data->ano)) )) )
        {

            $filters[] = new TFilter('ano', '=', $data->ano);// create the filter 
        }

        if (isset($data->datatransacaof) AND ( (is_scalar($data->datatransacaof) AND $data->datatransacaof !== '') OR (is_array($data->datatransacaof) AND (!empty($data->datatransacaof)) )) )
        {

            $filters[] = new TFilter('datatransacao', '<=', $data->datatransacaof);// create the filter 
        }

        if (isset($data->datatransacao) AND ( (is_scalar($data->datatransacao) AND $data->datatransacao !== '') OR (is_array($data->datatransacao) AND (!empty($data->datatransacao)) )) )
        {

            $filters[] = new TFilter('datatransacao', '>=', $data->datatransacao);// create the filter 
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

            // creates a repository for ViewSaldoempenhocompras
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

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
            $total_total_produtos=0;
            $total_saldo_empenho=0;
            $total_saldoatual=0;

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->saldo_departamento_id}";
                    $total_total_produtos = $total_total_produtos + $object->total_produtos;
                    $total_saldo_empenho = $total_saldo_empenho + $object->saldo_empenho;
                    $total_saldoatual = $total_saldoatual + $object->saldoatual;


                }
            }

            $total_object = new stdClass;
            $total_object->saldo_departamento_id = ''; // necessário para evitar erro
            $total_object->departamento_unit_id = 'TOTAL GERAL'; // necessário para evitar erro
            $total_object->estado_pedido_venda_id = ''; // necessário para evitar erro
            $total_object->total_produtos = 'R$ '.number_format($total_total_produtos ?? 0, 2, ',', '.');
            $total_object->saldo_empenho     = 'R$ '.number_format($total_saldo_empenho ?? 0, 2, ',', '.');
            $total_object->saldoatual  = 'R$ '.number_format($total_saldoatual ?? 0, 2, ',', '.');

            $row = $this->datagrid->addItem($total_object);
            $row->id = "row_TOTAL";
            $row->style = 'font-weight: bold; background: #f1f1f1';

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

        $object = new ViewSaldoempenhocompras($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->saldo_departamento_id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    
    public function onExportHtml($param = null)
    {
        try
        {
            $output = 'app/output/' . uniqid() . '.html';

            if ((!file_exists($output) && is_writable(dirname($output))) || is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    TTransaction::open(self::$database);

                    $datas = array_filter(array_map(fn($obj) => $obj->dt_pedido ?? null, $objects));
                    $periodo_txt = !empty($datas) 
                        ? 'Período: ' . date('d/m/Y', strtotime(min($datas))) . ' a ' . date('d/m/Y', strtotime(max($datas)))
                        : 'Período não informado';

                    $html = '<html><head><meta charset="utf-8"><title>SaldosDotacoesValoresEmpenhados</title>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
                        .header { display: flex; align-items: center; margin-bottom: 20px; }
                        .logo { width: 150px; }
                        .title-block { flex: 1; text-align: center; }
                        .title-block h1 { margin: 0; font-size: 18px; }
                        .title-block h3 { margin: 0; font-weight: normal; font-size: 14px; }
                        table.bordasimples { border-collapse: collapse; width: 100%; table-layout: auto; }
                        table.bordasimples th, table.bordasimples td {
                            border: 1px solid #646161;
                            padding: 4px 6px;
                            text-align: left;
                            white-space: nowrap;
                            width: auto;
                            font-size: 11px;
                        }
                        table.bordasimples thead { background: #ccc; }
                        tr.total { background: #eee; font-weight: bold; }
                        .col-descricao { min-width: 200px; max-width: 400px; white-space: normal; }
                        .col-valor_total_produto, .col-valor_total_servico { max-width: 120px; text-align: right; }
                    </style>
                    </head><body>';

                    $html .= '<div class="header">
                                <img src="app/images/logo.png" class="logo">
                                <div class="title-block">
                                    <h1>Relatório Consulta Saldos de Dotações e Valores Empenhados</h1>
                                    <h3>' . $periodo_txt . '</h3>
                                </div>
                            </div>';

                    $html .= '<table class="bordasimples"><thead><tr>';

                    foreach ($this->datagrid->getColumns() as $column)
                    {
                        $column_name = $column->getName();
                        $html .= '<th class="col-' . $column_name . '">' . $column->getLabel() . '</th>';
                    }

                    $html .= '</tr></thead><tbody>';

                    $totais = [
                        'total_produtos' => 0,
                        'saldo_empenho' => 0,
                        'saldoatual' => 0,
                    ];

                    $campos_monetarios = [
                        'total_produtos',
                        'saldo_empenho',
                        'saldoatual',
                    ];

                    foreach ($objects as $object)
                    {
                        $html .= '<tr>';

                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $value = '';

                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';

                                if (preg_match('/^(dt_|data)/i', $column_name) && strtotime($value)) {
                                    $value = date('d/m/Y', strtotime($value));
                                }

                                if ($column_name == 'tipo') {
                                    $value = ($object->$column_name == 1) ? 'Produto' :
                                            (($object->$column_name == 2) ? 'Serviço' : 'Outro');
                                }

                                if ($column_name == 'estado_pedido_venda_id') {
                                    try {
                                        $estado = new EstadoPedido($object->$column_name);
                                        $value = $estado->nome;
                                    } catch (Exception $e) {
                                        $value = 'N/A';
                                    }
                                }
                                elseif ($column_name == 'departamento_unit_id') {
                                    try {
                                        $dep = new DepartamentoUnit($object->$column_name);
                                        $value = $dep->name;
                                    } catch (Exception $e) {
                                        $value = 'N/A';
                                    }
                                }
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ('{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                            }

                            if (array_key_exists($column_name, $totais) && is_numeric($value)) {
                                $totais[$column_name] += $value;
                            }

                            if (in_array($column_name, $campos_monetarios) && is_numeric($value)) {
                                $value = 'R$ ' . number_format($value, 2, ',', '.');
                            }

                            $html .= '<td class="col-' . $column_name . '">' . htmlspecialchars($value) . '</td>';
                        }

                        $html .= '</tr>';
                    }

                    $html .= '<tr class="total">';
                    foreach ($this->datagrid->getColumns() as $column)
                    {
                        $col_name = $column->getName();
                        if (isset($totais[$col_name]))
                        {
                            if (in_array($col_name, ['qtd_servico', 'qtd_produto'])) {
                                $html .= '<td class="col-' . $col_name . '">' . number_format($totais[$col_name], 0, ',', '.') . '</td>';
                            }
                            elseif (in_array($col_name, $campos_monetarios)) {
                                $html .= '<td class="col-' . $col_name . '">R$ ' . number_format($totais[$col_name], 2, ',', '.') . '</td>';
                            }
                            else {
                                $html .= '<td class="col-' . $col_name . '">' . $totais[$col_name] . '</td>';
                            }
                        }
                        else {
                            $html .= '<td></td>';
                        }
                    }
                    $html .= '</tr>';

                    $emissao = $this->getCurrentEmissionDateTime();
                    $urlAtual = htmlspecialchars($this->getCurrentReportUrl(), ENT_QUOTES, 'UTF-8');
                    $html .= "<br><br><div style='font-size:14px; text-align:right; color:#555;'>
                                Emitido em: {$urlAtual}    Data e Hora: {$emissao}
                            </div>";

                    $html .= '</body></html>';
                    file_put_contents($output, $html);
                    TTransaction::close();

                    TPage::openFile($output);
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    private function getCurrentReportUrl(): string
    {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        return $scheme . '://' . $host . $uri;
    }

    private function getCurrentEmissionDateTime(): string
    {
        return (new DateTime('now', new DateTimeZone('America/Cuiaba')))->format('d/m/Y H:i');
    }

}

