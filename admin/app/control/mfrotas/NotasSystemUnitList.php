<?php

class NotasSystemUnitList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'NotasSystemUnit';
    private static $primaryKey = 'id';
    private static $formName = 'form_NotasSystemUnitList';
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
 $basename   = urlencode('nota-system-unit-list.pdf');
$download   = "download.php?file=app/manual/nota-system-unit-list.pdf&basename={$basename}";

$manual = "
    <span style='float:right;'>
        <a href='{$download}'
           target='_blank'
           style='text-decoration:none;margin-left:10px;'>
            <i class='fa fa-question-circle'> </i>
        </a>
    </span>
"; 
        // define the form title
        $this->form->setFormTitle("Listagem de documentos fiscais {$manual}");
        $this->limit = 20;

        $id = new TEntry('id');
        $created_at = new BDateRange('created_at', 'created_at_fim');
        $mes_ano = new TEntry('mes_ano');
        $valor = new TNumeric('valor', '2', ',', '.' );
        $mes_ano->setMask('99/9999', true);
        $valor->setSize('100%');
        $mes_ano->setSize('29%');
        $numero = new TEntry('numero');
        $numero->setSize('100%');

        $criteria_departamento_unit_id = new TCriteria();
        $criteria_departamento_unit_id->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));

        $created_at->setMask('dd/mm/yyyy');
        $created_at->setDatabaseMask('yyyy-mm-dd');
        $id->setSize(100);
        $created_at->setSize(280);
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_departamento_unit_id );
        $departamento_unit_id->addValidation("Unidades / Dep / Secretárias", new TRequiredValidator()); 
        $departamento_unit_id->enableSearch();
        $departamento_unit_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Data criação", null, '14px', null, '100%'),$created_at]);
        $row1->layout = ['col-sm-6','col-sm-6'];
     
        $row10 = $this->form->addFields([new TLabel("Mês/Ano de referência: ", null, '14px', null, '100%'),$mes_ano],[new TLabel("Valor: ", null, '14px', null, '100%'),$valor]);
        $row10->layout = ['col-sm-6','col-sm-6'];

        $row20 = $this->form->addFields([new TLabel("Unidades / Dep / Secretárias",null, null, '14px', null, '100%'),$departamento_unit_id], [new TLabel("Número",null, null, '14px', null, '100%'),$numero]);
        $row20->layout = ['col-sm-6','col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // $btn_onshow = $this->form->addAction("Cadastrar", new TAction(['NotasSystemUnitForm', 'onShow']), 'fas:plus #69aa46');
        // $this->btn_onshow = $btn_onshow;

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $filterVar = TSession::getValue('idunit');
        $this->filter_criteria->add(new TFilter('system_unit_id', '=', $filterVar));

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_departamento_transformed = new TDataGridColumn('departamento_unit->name', "Unidades / Dep / Secretárias", 'left');
        $column_caminho_transformed = new TDataGridColumn('caminho', "Caminho", 'left');
        $column_numero = new TDataGridColumn('numero', "Número", 'left');
        $column_mes_ano = new TDataGridColumn('mes_ano', "Mês/Ano de referência", 'left');
        $column_valor = new TDataGridColumn('valor', "Valor", 'left');
        $column_notificar_transformed = new TDataGridColumn('notificar', "Notificar", 'left');
        $column_created_at = new TDataGridColumn('created_at', "Criado em", 'left');
        $column_created_at->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
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
        $column_caminho_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $value = explode(',', $value);
            if(count($value) == 0)
            {
                $value = $value[0];
            }

            if(is_array($value))
            {
                $files = $value;
                $divFiles = new TElement('div');
                foreach($files as $file)
                {
                    $fileName = $file;
                    if (strpos($file, '%7B') !== false) 
                    {
                        if (!empty($file)) 
                        {
                            $fileObject = json_decode(urldecode($file));

                            $fileName = $fileObject->fileName;
                        }
                    }

                    $a = new TElement('a');
                    $a->href = "download.php?file={$fileName}";
                    $a->class = 'btn btn-link';
                    $a->add($fileName);
                    $a->target = '_blank';

                    $divFiles->add($a);

                }

                return $divFiles;
            }
            else
            {
                if (strpos($value, '%7B') !== false) 
                {
                    if (!empty($value)) 
                    {
                        $value_object = json_decode(urldecode($value));
                        $value = $value_object->fileName;
                    }
                }

                if($value)
                {
                    $a = new TElement('a');
                    $a->href = "download.php?file={$value}";
                    $a->class = 'btn btn-default';
                    $a->add($value);
                    $a->target = '_blank';

                    return $a;
                }

                return $value;
            }
        });

        $column_notificar_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if($value === true || $value == 't' || $value === 1 || $value == '1' || $value == 's' || $value == 'S' || $value == 'T')
            {
                return '<span class="label label-success">Sim</span>';
            }
            elseif($value === false || $value == 'f' || $value === 2 || $value == '2' || $value == 'n' || $value == 'N' || $value == 'F')   
            {
                return '<span class="label label-danger">Não</span>';
            }

            return $value;

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
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_departamento_transformed);
        $this->datagrid->addColumn($column_caminho_transformed);
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_mes_ano);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_notificar_transformed);
        $this->datagrid->addColumn($column_created_at);

        $action_onEdit = new TDataGridAction(array('NotasSystemUnitForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('NotasSystemUnitList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

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
        $button_cadastrar = new TButton('button_button_cadastrar');
        $button_cadastrar->setAction(new TAction(['NotasSystemUnitForm', 'onShow']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['NotasSystemUnitList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['NotasSystemUnitList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['NotasSystemUnitList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['NotasSystemUnitList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['NotasSystemUnitList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['NotasSystemUnitList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );
        $dropdown_button_exportar->addPostAction( "HTML", new TAction(['NotasSystemUnitList', 'onExportHtml'],['static' => 1]), 'datagrid_'.self::$formName, 'fab:html5 #E34F26'  );

        $head_right_actions->add($dropdown_button_exportar);
                   $head_left_actions->add($button_cadastrar);

           $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($button_atualizar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
     //       $container->add(TBreadCrumb::create(["Manutenção Frotas","Notas system units"]));
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
                $object = new NotasSystemUnit($key, FALSE); 

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

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }
        
         if (isset($data->departamento_unit_id) AND ( (is_scalar($data->departamento_unit_id) AND $data->departamento_unit_id !== '') OR (is_array($data->departamento_unit_id) AND (!empty($data->departamento_unit_id)) )) )
        {

            $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        }
         if (isset($data->valor) AND ( (is_scalar($data->valor) AND $data->valor !== '') OR (is_array($data->valor) AND (!empty($data->valor)) )) )
        {

            $filters[] = new TFilter('valor', '=', $data->valor);// create the filter 
        }
        if (isset($data->mes_ano) AND ( (is_scalar($data->mes_ano) AND $data->mes_ano !== '') OR (is_array($data->mes_ano) AND (!empty($data->mes_ano)) )) )
        {

            $filters[] = new TFilter('mes_ano', 'like', "%{$data->mes_ano}%");// create the filter 
        }
          if (isset($data->numero) AND ( (is_scalar($data->numero) AND $data->numero !== '') OR (is_array($data->numero) AND (!empty($data->numero)) )) )
        {

            $filters[] = new TFilter('numero', 'like', "%{$data->numero}%");// create the filter 
        }

        if (isset($data->created_at_fim) AND ( (is_scalar($data->created_at_fim) AND $data->created_at_fim !== '') OR (is_array($data->created_at_fim) AND (!empty($data->created_at_fim)) )) )
        {

            $filters[] = new TFilter('created_at', '<=', $data->created_at_fim);// create the filter 
        }

        if (isset($data->created_at) AND ( (is_scalar($data->created_at) AND $data->created_at !== '') OR (is_array($data->created_at) AND (!empty($data->created_at)) )) )
        {

            $filters[] = new TFilter('created_at', '>=', $data->created_at);// create the filter 
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

            // creates a repository for NotasSystemUnit
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

        $object = new NotasSystemUnit($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }
public static function onChangesystem_unit_id($param)
    {
        try
        {

            if (isset($param['system_unit_id']) && $param['system_unit_id'])
            { 
                $criteria = TCriteria::create(['system_unit_id' => $param['system_unit_id']]);
                TDBCombo::reloadFromModel(self::$formName, 'departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}', 'name asc', $criteria, TRUE); 
            } 
            else 
            { 
                TCombo::clearField(self::$formName, 'departamento_unit_id'); 
            }  

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
   //     TSession::setValue('data_inicial', NULL);
    //    TSession::setValue('data_final', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
      //   $this->datagrid->clear();
    }
       public function onRefresh($param = null) 
    {
        $this->onReload([]);
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

                if (!$objects) {
                    throw new Exception(_t('No records found'));
                }

                TTransaction::open(self::$database);

                $periodo_txt = '';
                $dt_ini = null;
                $dt_fim = null;

                $filters = TSession::getValue(__CLASS__ . '_filters');

                if ($filters && is_array($filters))
                {
                    foreach ($filters as $filter)
                    {
                        $field = $this->getFilterProp($filter, 'variable');
                        $op    = $this->getFilterProp($filter, 'operator');
                        $val   = $this->getFilterProp($filter, 'value');

                        // se sua versão usar "field" ao invés de "variable"
                        if (!$field) {
                            $field = $this->getFilterProp($filter, 'field');
                        }

                        if ($field === 'created_at')
                        {
                            if ($op === '>=') $dt_ini = $val;
                            if ($op === '<=') $dt_fim = $val;
                        }
                    }
                }

                $fmt = function($d) {
                    $d = trim((string)$d);
                    if (!$d) return '';
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $d)) return $d; // dd/mm/yyyy
                    if (strtotime($d)) return date('d/m/Y', strtotime($d)); // yyyy-mm-dd ou datetime
                    return $d;
                };

                $ini = $fmt($dt_ini);
                $fim = $fmt($dt_fim);

                if ($ini && $fim) {
                    $periodo_txt = "Período (criação): {$ini} a {$fim}";
                } elseif ($ini) {
                    $periodo_txt = "Período (criação): a partir de {$ini}";
                } elseif ($fim) {
                    $periodo_txt = "Período (criação): até {$fim}";
                }


                $fmt = function($d) {
                    $d = trim((string)$d);
                    if (!$d) return '';
                    // se já vier dd/mm/yyyy, mantém
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $d)) return $d;
                    // se vier yyyy-mm-dd / datetime, converte
                    if (strtotime($d)) return date('d/m/Y', strtotime($d));
                    return $d;
                };

                $ini = $fmt($dt_ini);
                $fim = $fmt($dt_fim);

                $periodo_txt = '';
                if ($ini && $fim) {
                    $periodo_txt = "Período (criação): {$ini} a {$fim}";
                } elseif ($ini) {
                    $periodo_txt = "Período (criação): a partir de {$ini}";
                } elseif ($fim) {
                    $periodo_txt = "Período (criação): até {$fim}";
                }

                // ====== HTML ======
                $html = '<html><head><meta charset="utf-8"><title>Relatório</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
                    .header { display: flex; align-items: center; margin-bottom: 20px; gap: 16px; }
                    .logo { width: 150px; }
                    .title-block { flex: 1; text-align: center; }
                    .title-block h1 { margin: 0; font-size: 18px; }
                    .title-block h3 { margin: 4px 0 0; font-weight: normal; font-size: 13px; color:#333; }
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
                </style>
                </head><body>';

                $html .= '<div class="header">
                            <img src="app/images/logo.png" class="logo">
                            <div class="title-block">
                                <h1>Listagem de Documentos Fiscais </h1>'
                            . (!empty($periodo_txt) ? '<h3>' . htmlspecialchars($periodo_txt) . '</h3>' : '')
                            . '</div>
                        </div>';

                // colunas automáticas do datagrid
                $columns = $this->datagrid->getColumns();

                $html .= '<table class="bordasimples"><thead><tr>';
                foreach ($columns as $column)
                {
                    $column_name = $column->getName();
                    $html .= '<th class="col-' . $column_name . '">' . $column->getLabel() . '</th>';
                }
                $html .= '</tr></thead><tbody>';

                foreach ($objects as $object)
                {
                    $html .= '<tr>';

                    foreach ($columns as $column)
                    {
                        $column_name = $column->getName();
                        $value = '';

                        if (isset($object->$column_name))
                        {
                            $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                        }
                        else if (method_exists($object, 'render'))
                        {
                            $col_render = (strpos((string)$column_name, '{') === FALSE) ? ('{' . $column_name . '}') : $column_name;
                            $value = $object->render($col_render);
                        }

                        // formata datas por nome de coluna (dt_ ou data*)
                        if (is_string($value) && preg_match('/^(dt_|data)/i', $column_name) && strtotime($value)) {
                            $value = date('d/m/Y', strtotime($value));
                        }

                        $html .= '<td class="col-' . $column_name . '">' . htmlspecialchars((string)$value) . '</td>';
                    }

                    $html .= '</tr>';
                }

                $emissao = $this->getCurrentEmissionDateTime();
                $urlAtual = htmlspecialchars($this->getCurrentReportUrl(), ENT_QUOTES, 'UTF-8');

                $html .= "</tbody></table>
                        <div style='margin-top:16px; font-size:12px; text-align:right; color:#555;'>
                            Emitido em: {$urlAtual} &nbsp;&nbsp; Data e Hora: {$emissao}
                        </div>
                        </body></html>";

                file_put_contents($output, $html);

                TTransaction::close();
                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e)
        {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());
        }
    }

    private function getFilterProp($filter, string $prop)
    {
        // tenta getter primeiro (se existir)
        $map = [
            'variable' => ['getVariable', 'getField'],
            'operator' => ['getOperator'],
            'value'    => ['getValue'],
        ];

        if (isset($map[$prop])) {
            foreach ($map[$prop] as $m) {
                if (method_exists($filter, $m)) {
                    return $filter->$m();
                }
            }
        }

        // fallback: Reflection (para propriedades private)
        try {
            $ref = new ReflectionClass($filter);
            if ($ref->hasProperty($prop)) {
                $p = $ref->getProperty($prop);
                $p->setAccessible(true);
                return $p->getValue($filter);
            }
        } catch (Exception $e) {
            // ignora
        }

        return null;
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

