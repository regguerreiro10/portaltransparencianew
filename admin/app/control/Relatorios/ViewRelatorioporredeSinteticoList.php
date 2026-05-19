<?php

class ViewRelatorioporredeSinteticoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'ViewRelatorioporredeSintetico';
    private static $primaryKey = 'proposta_id';
    private static $formName = 'form_ViewRelatorioporredeSinteticoList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters', 'onGlobalSearch'];
    private $limit = 20;

    use BuilderDatagridTrait;

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
        $this->form->setFormTitle("Listagem das Manutenções por Estabelecimento Sintéticos");
        $this->limit = 20;

        $criteria_system_unit_id = new TCriteria();
        $criteria_departamento_unit_id = new TCriteria();
        $criteria_pessoa_id = new TCriteria();

        $filterVar = TSession::getValue('idunit');
        $criteria_system_unit_id->add(new TFilter('id', '=', $filterVar)); 
        $filterVar = TSession::getValue('idunit');
        $criteria_departamento_unit_id->add(new TFilter('system_unit_id', '=', $filterVar)); 

        $system_unit_id = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_system_unit_id );
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_departamento_unit_id );
        $pessoa_id = new TDBUniqueSearch('pessoa_id', 'minierp', 'Pessoa', 'id', 'nome','nome asc' , $criteria_pessoa_id );
        $dt_abertura = new BDateRange('dt_abertura', 'dt_abertura_final');
        $dt_finalizado = new BDateRange('dt_finalizado', 'dt_finalizado_final');
        $dt_aprovado = new BDateRange('dt_aprovado', 'dt_aprovado_final');


        $pessoa_id->setMinLength(2);
        $pessoa_id->setFilterColumns(["nome"]);
        $system_unit_id->enableSearch();
        $departamento_unit_id->enableSearch();

        $dt_abertura->setDatabaseMask('yyyy-mm-dd');
        $dt_aprovado->setDatabaseMask('yyyy-mm-dd');
        $dt_finalizado->setDatabaseMask('yyyy-mm-dd');

        $pessoa_id->setMask('{nome}');
        $dt_abertura->setMask('dd/mm/yyyy');
        $dt_aprovado->setMask('dd/mm/yyyy');
        $dt_finalizado->setMask('dd/mm/yyyy');

        $dt_abertura->setSize(220);
        $dt_aprovado->setSize(220);
        $pessoa_id->setSize('100%');
        $dt_finalizado->setSize(220);
        $system_unit_id->setSize('100%');
        $departamento_unit_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Unidade:", null, '14px', null, '100%'),$system_unit_id],[new TLabel("Unidade / Dep / Secretaria:", null, '14px', null, '100%'),$departamento_unit_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Estabelecimento:", null, '14px', null, '100%'),$pessoa_id],[new TLabel("Dt Inicial e Final abertura:", null, '14px', null, '100%'),$dt_abertura]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Dt Inicial e Final finalizado:", null, '14px', null, '100%'),$dt_finalizado],[new TLabel("Dt Inicial e Final aprovado:", null, '14px', null, '100%'),$dt_aprovado]);
        $row3->layout = ['col-sm-6','col-sm-6'];

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

        $column_proposta_id = new TDataGridColumn('proposta_id', "ID", 'center' , '70px');
        $column_pedido_id = new TDataGridColumn('pedido_id', "ID Pedido Frotas", 'left');
        $column_system_unit_id = new TDataGridColumn('system_unit->name', "Unidade", 'left');
        $column_departamento_unit_id = new TDataGridColumn('departamento_unit->name', "Unidade / Dep / Secretaria", 'left');
        $column_pessoa_id = new TDataGridColumn('pessoa->nome', "Estabelecimento", 'left');
        $column_dt_abertura_transformed = new TDataGridColumn('dt_abertura', "Dt abertura", 'left');
        $column_dt_finalizado_transformed = new TDataGridColumn('dt_finalizado', "Dt finalizado", 'left');
        $column_dt_aprovado_transformed = new TDataGridColumn('dt_aprovado', "Dt aprovado", 'left');
        $column_qtd_proposta_recebida = new TDataGridColumn('qtd_proposta_recebida', "Qtd Orçamentos Recebidos", 'left');
        $column_qtd_proposta_finalizado = new TDataGridColumn('qtd_proposta_finalizado', "Qtd Orçamentos Finalizados", 'left');
        $column_qtd_proposta_entregue = new TDataGridColumn('qtd_proposta_entregue', "Qtd Orçamentos Entregue", 'left');
        $column_qtd_proposta_aguardando = new TDataGridColumn('qtd_proposta_aguardando', "Qtd Orçamentos Respondidos", 'left');
        $column_vl_produto_transformed = new TDataGridColumn('vl_produto', "Vl produto", 'left');
        $column_vl_servico_transformed = new TDataGridColumn('vl_servico', "Vl servico", 'left');
        $column_vl_total_transformed = new TDataGridColumn('vl_total', "Vl total", 'left');

        $column_dt_abertura_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y H:i');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_dt_finalizado_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y H:i');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_dt_aprovado_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y H:i');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_vl_produto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_vl_servico_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_vl_total_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $order_proposta_id = new TAction(array($this, 'onReload'));
        $order_proposta_id->setParameter('order', 'proposta_id');
        $column_proposta_id->setAction($order_proposta_id);

        $this->datagrid->addColumn($column_proposta_id);
        $this->datagrid->addColumn($column_pedido_id);
        $this->datagrid->addColumn($column_system_unit_id);
        $this->datagrid->addColumn($column_departamento_unit_id);
        $this->datagrid->addColumn($column_pessoa_id);
        $this->datagrid->addColumn($column_dt_abertura_transformed);
        $this->datagrid->addColumn($column_dt_finalizado_transformed);
        $this->datagrid->addColumn($column_dt_aprovado_transformed);
        $this->datagrid->addColumn($column_qtd_proposta_recebida);
        $this->datagrid->addColumn($column_qtd_proposta_finalizado);
        $this->datagrid->addColumn($column_qtd_proposta_entregue);
        $this->datagrid->addColumn($column_qtd_proposta_aguardando);
        $this->datagrid->addColumn($column_vl_produto_transformed);
        $this->datagrid->addColumn($column_vl_servico_transformed);
        $this->datagrid->addColumn($column_vl_total_transformed);

        // $action_onEdit = new TDataGridAction(array('PropostasForm', 'onEdit'));
        // $action_onEdit->setUseButton(false);
        // $action_onEdit->setButtonClass('btn btn-default btn-sm');
        // $action_onEdit->setLabel("Editar");
        // $action_onEdit->setImage('far:edit #478fca');
        // $action_onEdit->setField(self::$primaryKey);

        // $this->datagrid->addAction($action_onEdit);

        $this->applyDatagridProperties();
        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem das Manutenções por Estabelecimento Sintéticos");
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

        // $button_cadastrar = new TButton('button_button_cadastrar');
        // $button_cadastrar->setAction(new TAction(['PropostasForm', 'onShow']), "Cadastrar");
        // $button_cadastrar->addStyleClass('btn-default');
        // $button_cadastrar->setImage('fas:plus #69aa46');

        // $this->datagrid_form->addField($button_cadastrar);

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['ViewRelatorioporredeSinteticoList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['ViewRelatorioporredeSinteticoList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['ViewRelatorioporredeSinteticoList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['ViewRelatorioporredeSinteticoList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['ViewRelatorioporredeSinteticoList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        // $head_left_actions->add($button_cadastrar);
        $head_left_actions->add($btnShowCurtainFilters);

        $head_right_actions->add($dropdown_button_exportar);

        $this->datagrid_form->add($this->datagrid);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
   //         $container->add(TBreadCrumb::create(["Manutenção Frotas","ViewRelatorioporredeSinteticoList"]));
        }

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
                        if (TSession::getValue('enviar_email_auto_relatorio') == 1) {
                            TToast::show('success', 'Relatório enviado com sucesso no seu e-mail!', 'topRight', 'fas fa-check-circle');
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
                $columnCount = count($this->datagrid->getColumns());
                $pdfFontSize = $columnCount >= 20 ? 5 : ($columnCount >= 16 ? 6 : ($columnCount >= 12 ? 7 : 8));
                $pdfBodyFontSize = $pdfFontSize + 1;
                $pdfCellPadding = $columnCount >= 18 ? '1px 2px' : '2px 3px';
                $pdfStyles = '
                <style>
                    @page { margin: 12px; }
                    body { font-size: ' . $pdfBodyFontSize . 'px; }
                    table {
                        width: 100% !important;
                        max-width: 100% !important;
                        table-layout: fixed !important;
                    }
                    table th,
                    table td,
                    .tdatagrid_cell {
                        font-size: ' . $pdfFontSize . 'px !important;
                        line-height: 1.15 !important;
                        padding: ' . $pdfCellPadding . ' !important;
                        white-space: normal !important;
                        overflow-wrap: anywhere !important;
                        word-break: break-word !important;
                    }
                    .label {
                        width: auto !important;
                        min-width: 0 !important;
                        max-width: 100% !important;
                        font-size: ' . $pdfFontSize . 'px !important;
                        line-height: 1.15 !important;
                        white-space: normal !important;
                    }
                </style>';
                $contents = file_get_contents('app/resources/styles-print.html') . file_get_contents('app/resources/styles-print-list.html') . PdfListHeader::render(__CLASS__) . $pdfStyles . $html->getContents();

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
            $page->setProperty('page-name', 'ViewRelatorioporredeSinteticoListSearch');
            $page->setProperty('page_name', 'ViewRelatorioporredeSinteticoListSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();

            $style = new TStyle('right-panel > .container-part[page-name=ViewRelatorioporredeSinteticoListSearch]');
            $style->width = '50% !important';
            $style->show(true);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
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

        if (isset($data->system_unit_id) AND ( (is_scalar($data->system_unit_id) AND $data->system_unit_id !== '') OR (is_array($data->system_unit_id) AND (!empty($data->system_unit_id)) )) )
        {

            $filters[] = new TFilter('system_unit_id', '=', $data->system_unit_id);// create the filter 
        }

        if (isset($data->departamento_unit_id) AND ( (is_scalar($data->departamento_unit_id) AND $data->departamento_unit_id !== '') OR (is_array($data->departamento_unit_id) AND (!empty($data->departamento_unit_id)) )) )
        {

            $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        }

        if (isset($data->pessoa_id) AND ( (is_scalar($data->pessoa_id) AND $data->pessoa_id !== '') OR (is_array($data->pessoa_id) AND (!empty($data->pessoa_id)) )) )
        {

            $filters[] = new TFilter('pessoa_id', '=', $data->pessoa_id);// create the filter 
        }

        if (isset($data->dt_abertura) AND ( (is_scalar($data->dt_abertura) AND $data->dt_abertura !== '') OR (is_array($data->dt_abertura) AND (!empty($data->dt_abertura)) )) )
        {

            $filters[] = new TFilter('dt_abertura', '<=', $data->dt_abertura);// create the filter 
        }

        if (isset($data->dt_abertura) AND ( (is_scalar($data->dt_abertura) AND $data->dt_abertura !== '') OR (is_array($data->dt_abertura) AND (!empty($data->dt_abertura)) )) )
        {

            $filters[] = new TFilter('dt_abertura', '>=', $data->dt_abertura);// create the filter 
        }

        if (isset($data->dt_finalizado_final) AND ( (is_scalar($data->dt_finalizado_final) AND $data->dt_finalizado_final !== '') OR (is_array($data->dt_finalizado_final) AND (!empty($data->dt_finalizado_final)) )) )
        {

            $filters[] = new TFilter('dt_finalizado', '<=', $data->dt_finalizado_final);// create the filter 
        }

        if (isset($data->dt_finalizado) AND ( (is_scalar($data->dt_finalizado) AND $data->dt_finalizado !== '') OR (is_array($data->dt_finalizado) AND (!empty($data->dt_finalizado)) )) )
        {

            $filters[] = new TFilter('dt_finalizado', '>=', $data->dt_finalizado);// create the filter 
        }

        if (isset($data->dt_aprovado_final) AND ( (is_scalar($data->dt_aprovado_final) AND $data->dt_aprovado_final !== '') OR (is_array($data->dt_aprovado_final) AND (!empty($data->dt_aprovado_final)) )) )
        {

            $filters[] = new TFilter('dt_aprovado', '<=', $data->dt_aprovado_final);// create the filter 
        }

        if (isset($data->dt_aprovado) AND ( (is_scalar($data->dt_aprovado) AND $data->dt_aprovado !== '') OR (is_array($data->dt_aprovado) AND (!empty($data->dt_aprovado)) )) )
        {

            $filters[] = new TFilter('dt_aprovado', '>=', $data->dt_aprovado);// create the filter 
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

            // creates a repository for ViewRelatorioporredeSintetico
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'proposta_id';    
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
            //</blockLine></btnShowCurtainFiltersAutoCode>
            $criteria->add(new TFilter('system_unit_id', '=',TSession::getValue('idunit')));

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            
            $total_qtd_recebidos=0;
            $total_qtd_finalizados=0;
            $total_qtd_entregue=0;
            $total_qtd_respondido=0;
            $total_vlproduto=0;
            $total_vlservico=0;
            $total_vltotal=0;

            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->pedido_frotas_id}";
                        $total_qtd_recebidos = $total_qtd_recebidos + $object->qtd_proposta_recebida;
                        $total_qtd_finalizados = $total_qtd_finalizados + $object->qtd_proposta_finalizado;
                        $total_qtd_entregue = $total_qtd_entregue + $object->qtd_proposta_entregue;
                        $total_qtd_respondido = $total_qtd_respondido + $object->qtd_proposta_aguardando;
                        $total_vlproduto = $total_vlproduto + $object->vl_produto;
                        $total_vlservico = $total_vlservico + $object->vl_servico;
                        $total_vltotal = $total_vltotal + $object->vl_total;
                }
            }

            $total_object = new stdClass;
            $total_object->id = ''; // necessário para evitar erro
            $total_object->pedido_frotas_id = ''; // necessário para evitar erro
            $total_object->pessoa_id = '';
            $total_object->dt_aprovado = 'TOTAL GERAL';
            $total_object->qtd_proposta_recebida     = $total_qtd_recebidos;
            $total_object->qtd_proposta_finalizado     = $total_qtd_finalizados;
            $total_object->qtd_proposta_entregue     = $total_qtd_entregue;
            $total_object->qtd_proposta_aguardando     = $total_qtd_respondido;
            $total_object->vl_produto = number_format($total_vlproduto ?? 0, 2, ',', '.');
            $total_object->vl_servico = number_format($total_vlservico ?? 0, 2, ',', '.');
            $total_object->vl_total = number_format($total_vltotal ?? 0, 2, ',', '.');

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

        $object = new ViewRelatorioporredeSintetico($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->proposta_id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

