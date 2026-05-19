<?php

class ConsultaSaldoDepartamentoFrotasList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'SaldoDepartamento';
    private static $primaryKey = 'id';
    private static $formName = 'form_ConsultaSaldoDepartamentoFrotasList';
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
        $this->form->setFormTitle("Consulta Saldos de Dotações e Valores Empenhados de Frotas");
        $this->limit = 0;

        $criteria_departamento_unit_id = new TCriteria();

        $filterVar = (int) TSession::getValue('idunit');
        $criteria_departamento_unit_id->add(new TFilter('system_unit_id', '=', $filterVar));

        $departamento_unit_id = new TDBCombo(
            'departamento_unit_id', 
            'minierp', 
            'DepartamentoUnit', 
            'id', 
            '{name}', 
            'name asc', 
            $criteria_departamento_unit_id
        );
        $id = new THidden('id');
        // $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_departamento_unit_id );
        $datatransacao = new BDateRange('datatransacao', 'datatransacaof');
        $historico = new TEntry('historico');
        $numero_documento_empenho = new TEntry('numero_documento_empenho');


        $departamento_unit_id->enableSearch();
        $datatransacao->setMask('dd/mm/yyyy');
        $datatransacao->setDatabaseMask('yyyy-mm-dd');
        $historico->setMaxLength(100);
        $numero_documento_empenho->setMaxLength(30);

        $id->setSize(200);
        $historico->setSize('100%');
        $datatransacao->setSize(220);
        $departamento_unit_id->setSize('100%');
        $numero_documento_empenho->setSize('100%');

        $row1 = $this->form->addFields([$id,new TLabel("Departamento:", null, '14px', null, '100%'),$departamento_unit_id],[new TLabel("Data transação:", null, '14px', null, '100%'),$datatransacao]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Historico:", null, '14px', null, '100%'),$historico],[new TLabel("Nº empenho:", null, '14px', null, '100%'),$numero_documento_empenho]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        $dropdownHeader = new TDropDown("", 'fas:cog #000000');
        $dropdownHeader->setPullSide('right');
        $dropdownHeader->setButtonClass('btn btn-sm btn-default dropdown-toggle');

        $this->form->addHeaderWidget( $dropdownHeader );

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

        $column_id = new TDataGridColumn('id', "ID", 'center' , '70px');
        $column_datatransacao = new TDataGridColumn('datatransacao', "Data transação", 'left');
        $column_departamento_unit_name = new TDataGridColumn('departamento_unit->name', "Departamento", 'left');
        $column_numero_documento_empenho = new TDataGridColumn('numero_documento_empenho', "Nº Empenho", 'left');
        $column_historico = new TDataGridColumn('historico', "Histórico", 'left');
        $column_saldo_produto = new TDataGridColumn('saldo_produto', "Saldo Empenho", 'left');
        $column_total_pedido = new TDataGridColumn('valor', "Total Pedidos", 'left');
        $column_saldo_atual = new TDataGridColumn('saldo_atual', "Saldo Atual", 'left');
        $column_documento_empenho = new TDataGridColumn('documento_empenho', "Documento empenho", 'left');
        $column_qtde_pedidos = new TDataGridColumn('', "Qtde Pedidos", 'left');
        $column_documento_empenho->setTransformer(function($value, $object)
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

        $column_saldo_produto->setTransformer(function($value, $object)
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
        $column_total_pedido->setTransformer(function($value, $object)
        {
            $valor_total = 0;

            // Subquery para filtrar apenas pedidos da unidade ativa
            $filterUnit = TSession::getValue('idunit');

            // Carrega dotações do saldo_departamento atual e da unidade
            $pedidos = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $object->id)
                ->where('pedido_frotas_id', 'IN', 
                    '(SELECT id FROM pedido_frotas WHERE system_unit_id = ' . $filterUnit . ')'
                )->load();

            if ($pedidos) {
                foreach ($pedidos as $ped) {
                    if (!empty($ped->valor)) {
                        $valor_total += $ped->valor;
                    }
                }
            }

            // Calcula saldo atual (saldo_produto - total comprometido)
            if (is_numeric($valor_total)) {
                return "R$ " . number_format($valor_total, 2, ",", ".");
            } else {
                return "0";
            }
        });
  

       $column_qtde_pedidos->setTransformer(function($value, $object)
        {
            $value = 0;

            $pedidos = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $object->id)->load();

            if ($pedidos) {
                foreach ($pedidos as $ped) {
                    $value = $value + 1;
                }
            }
            if ($value>0) {
                return "<span style='font-weight: bold; color: #f44336;'>{$value}</span>";
            } else {
                return $value;
            }

        });  

          $column_saldo_atual->setTransformer(function($value, $object)
        {
            $value = 0;

            // $pedido = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $object->id)
            //                 ->where('pedido_frotas_id', 'in', 'select id from pedido_frotas')
            //                 ->load();
            // if ($pedido) {
            //    foreach ($pedido as $ped) {
            //         if ($ped->valor_liquido_proposta === null || $ped->valor_liquido_proposta === '')
            //         {
            //             $value =  $value +  $ped->valor_total_proposta;
            //         }
            //         else
            //         {
            //             $value = $value + $ped->valor_liquido_proposta;
            //         }
            //     }
            // }

            

            if(is_numeric($value))
            {
//                return "R$ " . number_format($object->saldo_produto - $value, 2, ",", ".");
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });   
         $column_datatransacao->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_datatransacao);
        $this->datagrid->addColumn($column_departamento_unit_name);
        $this->datagrid->addColumn($column_numero_documento_empenho);
        $this->datagrid->addColumn($column_historico);
        $this->datagrid->addColumn($column_saldo_produto);
        $this->datagrid->addColumn($column_total_pedido);
        $this->datagrid->addColumn($column_saldo_atual);
        $this->datagrid->addColumn($column_documento_empenho);
        $this->datagrid->addColumn($column_qtde_pedidos);

        $action_onExibirDetalhesPedido = new TDataGridAction(array('ConsultaSaldoDepartamentoFrotasList', 'onExibirDetalhesPedido'));
        $action_onExibirDetalhesPedido->setUseButton(false);
        $action_onExibirDetalhesPedido->setButtonClass('btn btn-default btn-sm');
        $action_onExibirDetalhesPedido->setLabel("Detalhes dos pedidos");
        $action_onExibirDetalhesPedido->setImage('fas:plus #69AA46');
        $action_onExibirDetalhesPedido->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onExibirDetalhesPedido);

        // create the datagrid model
        $this->applyDatagridProperties();

        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Consulta Saldos de Dotações e Valores Empenhados Frotas");
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
        $btnShowCurtainFilters->setAction(new TAction(['ConsultaSaldoDepartamentoFrotasList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['ConsultaSaldoDepartamentoFrotasList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['ConsultaSaldoDepartamentoFrotasList', 'onAtualizar']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['ConsultaSaldoDepartamentoFrotasList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['ConsultaSaldoDepartamentoFrotasList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['ConsultaSaldoDepartamentoFrotasList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['ConsultaSaldoDepartamentoFrotasList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

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
            // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        }

        $container->add($panel);

        parent::add($container);

    }

    public function onExibirDetalhesPedido($param = null) 
    {
        try 
        {
            //code here
            $pageParam = [];
            TApplication::loadPage('PedidoSaldoDepartamentoFrotasList', 'onShow', [
                'target_container' => "container_pedidos_{$param['key']}",
                'saldo_departamento_id' => $param['key']
            ]);
            //</autoCode>
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
            $page->setProperty('page-name', 'ConsultaSaldoDepartamentoFrotasListSearch');
            $page->setProperty('page_name', 'ConsultaSaldoDepartamentoFrotasListSearch');
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

        $this->onReload(['offset' => 0, 'first_page' => 1]);
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

        if (isset($data->departamento_unit_id) AND ( (is_scalar($data->departamento_unit_id) AND $data->departamento_unit_id !== '') OR (is_array($data->departamento_unit_id) AND (!empty($data->departamento_unit_id)) )) )
        {

            $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        }

        if (isset($data->datatransacaof) AND ( (is_scalar($data->datatransacaof) AND $data->datatransacaof !== '') OR (is_array($data->datatransacaof) AND (!empty($data->datatransacaof)) )) )
        {

            $filters[] = new TFilter('datatransacao', '<=', $data->datatransacaof);// create the filter 
        }

        if (isset($data->datatransacao) AND ( (is_scalar($data->datatransacao) AND $data->datatransacao !== '') OR (is_array($data->datatransacao) AND (!empty($data->datatransacao)) )) )
        {

            $filters[] = new TFilter('datatransacao', '>=', $data->datatransacao);// create the filter 
        }

        if (isset($data->historico) AND ( (is_scalar($data->historico) AND $data->historico !== '') OR (is_array($data->historico) AND (!empty($data->historico)) )) )
        {

            $filters[] = new TFilter('historico', 'like', "%{$data->historico}%");// create the filter 
        }

        if (isset($data->numero_documento_empenho) AND ( (is_scalar($data->numero_documento_empenho) AND $data->numero_documento_empenho !== '') OR (is_array($data->numero_documento_empenho) AND (!empty($data->numero_documento_empenho)) )) )
        {

            $filters[] = new TFilter('numero_documento_empenho', 'like', "%{$data->numero_documento_empenho}%");// create the filter 
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

            // creates a repository for SaldoDepartamento
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

            $idunit = TSession::getValue('idunit');

            if ($idunit) {
                $criteria->add(new TFilter('departamento_unit_id', 'IN', "(SELECT id FROM departamento_unit WHERE system_unit_id = {$idunit})"));
            }            //</blockLine></btnShowCurtainFiltersAutoCode>
            $cont=1;

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

                    $row = new TTableRow;
                    $div = new TElement('div');
                    $div->id = "container_pedidos_{$object->id}";
                     $cell=$row->addCell($div);

                    $cell->colspan = $this->datagrid->getTotalColumns();
                    $cell->style = 'padding: 10px; ';

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

        $object = new SaldoDepartamento($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }
    public static function onAtualizar($param = [])
    {
        TTransaction::open('minierp');
        $idunit = TSession::getValue('idunit');

        // Carrega todos os DotacaoPedidoFrotas da unidade, ordenados por saldo_departamento_id e data
        $dotacoes = ViewDotacaoPedidoFrotas::where('system_unit_id', '=', $idunit)
                                           ->orderBy('saldo_departamento_id')
                                           ->orderBy('dt_pedido') // ou 'id' se não houver created_at
                                           ->load();

        // Armazena os saldos parciais para cada departamento
        $saldos = 0;

        if ($dotacoes) {
            foreach ($dotacoes as $dpf) {
                $sd_id = $dpf->id;

                // Pega o saldo atual (do array se já iniciado, ou do banco)
                if (!isset($saldos)) {
                    $saldodepartamento = new SaldoDepartamento($dpf->saldo_departamento_id);
                    $saldos = $saldodepartamento->saldo_total;
                }
                // Subtrai valor da dotação
                $saldos -= $dpf->valor;

                $dotacaopf = new DotacaoPedidoFrotas($dpf->id);
                $dotacaopf->saldo_atual = $saldos; 
                $dpf->store();
            }

        }

        TTransaction::close();
    }
}

