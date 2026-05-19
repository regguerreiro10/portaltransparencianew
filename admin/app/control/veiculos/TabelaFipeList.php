<?php

class TabelaFipeList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'TabelaFipeFinalCorrigida';
    private static $primaryKey = 'id';
    private static $formName = 'form_TabelaFipeList';
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
        $basename   = urlencode('tabela-fipe-list.pdf');
        $download   = "download.php?file=app/manual/tabela-fipe-list.pdf&basename={$basename}";

        $manual = "
            <span style='float:right;'>
                <a href='{$download}'
                target='_blank'
                style='text-decoration:none;margin-left:10px;'>
                    <i class='fa fa-question-circle'> </i>
                </a>
            </span>
        "; 
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("TabelaFipeList {$manual}");
        $this->limit = 20;

        $Brand_Value = new TEntry('Brand_Value');
        $Model_Value = new TEntry('Model_Value');
        $Year_Value = new TEntry('Year_Value');
        $Fipe_Code = new TEntry('Fipe_Code');
        $Especie = new TEntry('Especie');
        $Familia = new TEntry('Familia');
        $Propriedade = new TEntry('Propriedade');
        $Type = new TEntry('Type');
        $Fuel_Type = new TEntry('Fuel_Type');
        $Price = new TEntry('Price');


        $Type->setSize('100%');
        $Price->setSize('100%');
        $Especie->setSize('100%');
        $Familia->setSize('100%');
        $Fipe_Code->setSize('100%');
        $Fuel_Type->setSize('100%');
        $Year_Value->setSize('100%');
        $Brand_Value->setSize('100%');
        $Model_Value->setSize('100%');
        $Propriedade->setSize('100%');

        $Type->setMaxLength(255);
        $Price->setMaxLength(255);
        $Especie->setMaxLength(255);
        $Familia->setMaxLength(255);
        $Fipe_Code->setMaxLength(255);
        $Fuel_Type->setMaxLength(255);
        $Year_Value->setMaxLength(255);
        $Brand_Value->setMaxLength(255);
        $Model_Value->setMaxLength(255);
        $Propriedade->setMaxLength(255);

        $row1 = $this->form->addFields([new TLabel("Marca:", null, '14px', null, '100%'),$Brand_Value],[new TLabel("Modelo:", null, '14px', null, '100%'),$Model_Value]);
        $row1->layout = ['col-sm-6',' col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Ano fabricação", null, '14px', null, '100%'),$Year_Value],[new TLabel("Código Fipe:", null, '14px', null, '100%'),$Fipe_Code]);
        $row2->layout = ['col-sm-6',' col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Especie:", null, '14px', null, '100%'),$Especie],[new TLabel("Familia:", null, '14px', null, '100%'),$Familia]);
        $row3->layout = ['col-sm-6',' col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Propriedade/Classificação:", null, '14px', null, '100%'),$Propriedade],[new TLabel("Tipo Veiculo", null, '14px', null, '100%'),$Type]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Tipo de Combustível:", null, '14px', null, '100%'),$Fuel_Type],[new TLabel("Valor tabela Fipe:", null, '14px', null, '100%'),$Price]);
        $row5->layout = ['col-sm-6','col-sm-6'];

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

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_Brand_Value = new TDataGridColumn('Brand_Value', "Marca", 'left');
        $column_Model_Value = new TDataGridColumn('Model_Value', "Modelo", 'left');
        $column_Year_Value = new TDataGridColumn('Year_Value', "Ano", 'left');
        $column_Fipe_Code = new TDataGridColumn('Fipe_Code', "Código Fipe", 'left');
        $column_Type = new TDataGridColumn('Type', "Tipo", 'left');
        $column_Especie = new TDataGridColumn('Especie', "Especie", 'left');
        $column_Familia = new TDataGridColumn('Familia', "Familia", 'left');
        $column_Propriedade = new TDataGridColumn('Propriedade', "Propriedade/Classificação", 'left');
        $column_Fuel_Type = new TDataGridColumn('Fuel_Type', "Tipo de combustivel", 'left');
        $column_Price = new TDataGridColumn('Price', "Valor tabela Fipe", 'left');
        $column_Month = new TDataGridColumn('Month', "Ultima atualização", 'left');

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_Brand_Value);
        $this->datagrid->addColumn($column_Model_Value);
        $this->datagrid->addColumn($column_Year_Value);
        $this->datagrid->addColumn($column_Fipe_Code);
        $this->datagrid->addColumn($column_Type);
        $this->datagrid->addColumn($column_Especie);
        $this->datagrid->addColumn($column_Familia);
        $this->datagrid->addColumn($column_Propriedade);
        $this->datagrid->addColumn($column_Fuel_Type);
        $this->datagrid->addColumn($column_Price);
        $this->datagrid->addColumn($column_Month);

        // create the datagrid model
        $this->applyDatagridProperties();

        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem tabela fipe {$manual}");
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

        $button_importar = new TButton('button_button_importar');
        $button_importar->setAction(new TAction([$this, 'onImportar']), "Importar");
        $button_importar->addStyleClass('btn-default');
        $button_importar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_importar);

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['TabelaFipeList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['TabelaFipeList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['TabelaFipeList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['TabelaFipeList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['TabelaFipeList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['TabelaFipeList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['TabelaFipeList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_importar);
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
            $page->setProperty('page-name', 'TabelaFipeListSearch');
            $page->setProperty('page_name', 'TabelaFipeListSearch');
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

        if (isset($data->Brand_Value) AND ( (is_scalar($data->Brand_Value) AND $data->Brand_Value !== '') OR (is_array($data->Brand_Value) AND (!empty($data->Brand_Value)) )) )
        {

            $filters[] = new TFilter('Brand_Value', 'like', "%{$data->Brand_Value}%");// create the filter 
        }

        if (isset($data->Model_Value) AND ( (is_scalar($data->Model_Value) AND $data->Model_Value !== '') OR (is_array($data->Model_Value) AND (!empty($data->Model_Value)) )) )
        {

            $filters[] = new TFilter('Model_Value', 'like', "%{$data->Model_Value}%");// create the filter 
        }

        if (isset($data->Year_Value) AND ( (is_scalar($data->Year_Value) AND $data->Year_Value !== '') OR (is_array($data->Year_Value) AND (!empty($data->Year_Value)) )) )
        {

            $filters[] = new TFilter('Year_Value', 'like', "%{$data->Year_Value}%");// create the filter 
        }

        if (isset($data->Fipe_Code) AND ( (is_scalar($data->Fipe_Code) AND $data->Fipe_Code !== '') OR (is_array($data->Fipe_Code) AND (!empty($data->Fipe_Code)) )) )
        {

            $filters[] = new TFilter('Fipe_Code', 'like', "%{$data->Fipe_Code}%");// create the filter 
        }

        if (isset($data->Especie) AND ( (is_scalar($data->Especie) AND $data->Especie !== '') OR (is_array($data->Especie) AND (!empty($data->Especie)) )) )
        {

            $filters[] = new TFilter('Especie', 'like', "%{$data->Especie}%");// create the filter 
        }

        if (isset($data->Familia) AND ( (is_scalar($data->Familia) AND $data->Familia !== '') OR (is_array($data->Familia) AND (!empty($data->Familia)) )) )
        {

            $filters[] = new TFilter('Familia', 'like', "%{$data->Familia}%");// create the filter 
        }

        if (isset($data->Propriedade) AND ( (is_scalar($data->Propriedade) AND $data->Propriedade !== '') OR (is_array($data->Propriedade) AND (!empty($data->Propriedade)) )) )
        {

            $filters[] = new TFilter('Propriedade', 'like', "%{$data->Propriedade}%");// create the filter 
        }

        if (isset($data->Type) AND ( (is_scalar($data->Type) AND $data->Type !== '') OR (is_array($data->Type) AND (!empty($data->Type)) )) )
        {

            $filters[] = new TFilter('Type', 'like', "%{$data->Type}%");// create the filter 
        }

        if (isset($data->Fuel_Type) AND ( (is_scalar($data->Fuel_Type) AND $data->Fuel_Type !== '') OR (is_array($data->Fuel_Type) AND (!empty($data->Fuel_Type)) )) )
        {

            $filters[] = new TFilter('Fuel_Type', 'like', "%{$data->Fuel_Type}%");// create the filter 
        }

        if (isset($data->Price) AND ( (is_scalar($data->Price) AND $data->Price !== '') OR (is_array($data->Price) AND (!empty($data->Price)) )) )
        {

            $filters[] = new TFilter('Price', 'like', "%{$data->Price}%");// create the filter 
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

            // creates a repository for TabelaFipeFinalCorrigida
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'Brand_Value, Model_Value';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'asc';
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

        $object = new TabelaFipeFinalCorrigida($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    public static function onImportar($param = null)
    {
        /* LOAD DATA LOCAL INFILE 'C:/Users/SONY/Downloads/tabela_fipe_final_corrigida.csv'
        INTO TABLE tabela_fipe_final_corrigida
        CHARACTER SET latin1
        FIELDS TERMINATED BY ',' 
        ENCLOSED BY '"' 
        LINES TERMINATED BY '\r\n'
        IGNORE 1 LINES
        (Type, Brand_Code, Brand_Value, Model_Code, Model_Value, Year_Code, Year_Value, Fipe_Code, Fuel_Letter, Fuel_Type, Price, Month, Especie, Familia, Propriedade);
        */
        TTransaction::open('minierp');    

        $repositorio = new TRepository('TabelaFipeFinalCorrigida');
        $criterio = new TCriteria(); // sem filtros
        //$criterio->add(new TFilter('id', '>=', 48519));
        //$criterio->add(new TFilter('id', '<=', 48520));

        $tabelafipe = $repositorio->load($criterio);

        foreach ($tabelafipe as $fipe) {
            //tipo veiculo
            $tipoveiculo = TipoVeiculo::where('LOWER(descricao)', '=', strtolower($fipe->Type))->load();
            if ($tipoveiculo)
            {
                $idtipoveiculo = $tipoveiculo[0]->id;
            }
            else {
                $tipoveiculo = new TipoVeiculo();
                $tipoveiculo->descricao = $fipe->Type;
                $tipoveiculo->store();
                $idtipoveiculo = $tipoveiculo->id;
            }
            //marca
            $marca = Marca::where('LOWER(descricao)', '=', strtolower($fipe->Brand_Value))->load();
            if ($marca)
            {
                $idmarca = $marca[0]->id;
            }
            else {
                $marca = new Marca();
                $marca->descricao = $fipe->Brand_Value;
                $marca->store();
                $idmarca = $marca->id;
            }

            //tipocombustivel
            $tipocombustivel = TipoCombustivel::where('LOWER(descricao)', '=', strtolower($fipe->Fuel_Type))->load();
            if ($tipocombustivel)
            {
                $idtipocombustivel = $tipocombustivel[0]->id;
            }
            else {
                $tipocombustivel = new TipoCombustivel();
                $tipocombustivel->descricao = $fipe->Fuel_Type;
                $tipocombustivel->store();
                $idtipocombustivel = $tipocombustivel->id;
            }
              //especie 
            $especie = Especie::where('LOWER(descricao)', '=', strtolower($fipe->Especie))->load();
            if ($especie)
            {
                $idespecie = $especie[0]->id;
            }
            else {
                $especie = new Especie();
                $especie->descricao = $fipe->Especie;
                $especie->store();
                $idespecie = $especie->id;
            }
            //familia 
            $familia = Familia::where('LOWER(descricao)', '=', strtolower($fipe->Familia))->load();
            if ($familia)
            {
                $idfamilia = $familia[0]->id;
            }
            else {
                $familia = new familia();
                $familia->descricao = $fipe->Familia;
                $familia->store();
                $idfamilia = $familia->id;
            }
             //propriedade 
            $propriedade = Propriedade::where('LOWER(descricao)', '=', strtolower($fipe->Propriedade))->load();
            if ($propriedade)
            {
                $idpropriedade = $propriedade[0]->id;
            }
            else {
                $propriedade = new Propriedade();
                $propriedade->descricao = $fipe->Propriedade;
                $propriedade->store();
                $idpropriedade = $propriedade->id;
            }

            $descmodelo = $fipe->Model_Value;
             //modelo
            $modelo = Modelo::where('LOWER(descricao)', '=', strtolower($fipe->Model_Value))
                            ->where('marca_id','=',$idmarca)
                            ->load();
            if ($modelo)
            {
                $idmodelo = $modelo[0]->id;
            }
            else {
                $modelo = new Modelo();
                $modelo->descricao = $fipe->Model_Value;
                $modelo->marca_id = $idmarca;
                $modelo->especie_id = $idespecie;
                $modelo->tipo_veiculo_id = $idtipoveiculo;
                $modelo->tipo_combustivel_id = $idtipocombustivel;
                $modelo->familia_id = $idfamilia;
                $modelo->propriedade_id = $idpropriedade;
                $modelo->store();
                $idmodelo = $modelo->id;
            }

                //modelo
                $modeloanos = ModeloAno::where('ano', '=', $modeloss->Year_Code)
                                ->where('modelo_id','=',$idmodelo)
                                ->load();
                if ($modeloanos)
                {
                }
                else {
                    $valor = $fipe->Year_Code;
                    $ano = explode('-', $valor)[0]; 

                    $preco_limpo = str_replace(['R$', ' '], '', $fipe->Price); // remove R$ e espaço
                    $preco_limpo = str_replace('.', '', $preco_limpo);   // remove ponto dos milhares
                    $preco_limpo = str_replace(',', '.', $preco_limpo);  // converte vírgula decimal para ponto
                    $preco_float = floatval($preco_limpo);               // transforma em número

                     $modeloanos = new ModeloAno();
                    $modeloanos->ano = $ano;
                    $modeloanos->preco = $preco_float;
                    $modeloanos->modelo_id = $idmodelo;
                    $modeloanos->store();
                }

        }

        TTransaction::close();    

    }

}

