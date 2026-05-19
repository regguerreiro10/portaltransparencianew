<?php

class SaldoEntidadeContratoList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'SaldoEntidadeContrato';
    private static $primaryKey = 'id';
    private static $formName = 'formList_SaldoEntidadeContrato';
    private $showMethods = ['onReload', 'onSearch'];
    private $limit = 20;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();
        // creates the form

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->limit = 20;

        $id = new TEntry('id');
        $entidade_id = new TEntry('entidade_id');
        $entidade_nome = new TEntry('entidade_nome');
        $tipotransacao = new TCombo('tipotransacao');
        $datatransacao = new TEntry('datatransacao');
        $dtinicio = new TEntry('dtinicio');
        $dtfinal = new TEntry('dtfinal');
        $historico = new TEntry('historico');
        $valor_saldo = new TEntry('valor_saldo');

        $tipotransacao->addItems([
            'C' => 'Crédito',
            'D' => 'Débito'
        ]);


        $id->exitOnEnter();
        $entidade_id->exitOnEnter();
        $entidade_nome->exitOnEnter();
        $datatransacao->exitOnEnter();
        $dtinicio->exitOnEnter();
        $dtfinal->exitOnEnter();
        $historico->exitOnEnter();
        $valor_saldo->exitOnEnter();

        $id->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $entidade_id->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $entidade_nome->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $datatransacao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $dtinicio->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $dtfinal->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $historico->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $valor_saldo->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $tipotransacao->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $tipotransacao->enableSearch();
        $id->setEditable(false);
        $historico->setEditable(false);
        $entidade_id->setEditable(false);
        $valor_saldo->setEditable(false);
        $entidade_nome->setEditable(false);
        $datatransacao->setEditable(false);
        $dtinicio->setEditable(false);
        $dtfinal->setEditable(false);

        $id->setSize('100%');
        $historico->setSize('100%');
        $entidade_id->setSize('100%');
        $valor_saldo->setSize('100%');
        $entidade_nome->setSize('100%');
        $tipotransacao->setSize('100%');
        $datatransacao->setSize('100%');
        $dtinicio->setSize('100%');
        $dtfinal->setSize('100%');

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '5%');
        $column_entidade_id = new TDataGridColumn('entidade_id', "Id Entidade", 'center' , '5%');
        $column_entidade_nome = new TDataGridColumn('entidade->nome', "Nome Entidade", 'left');
        $column_tipotransacao_transformed = new TDataGridColumn('tipotransacao', "Tipo Transação", 'center');
        $column_datatransacao_transformed = new TDataGridColumn('datatransacao', "Data Transação", 'center', '15%');
        $column_dtinicio = new TDataGridColumn('dtinicio','Data Inicial', 'center', '15%');
        $column_dtfinal = new TDataGridColumn('dtfinal', "Data Final", 'center', '15%');
        $column_historico = new TDataGridColumn('historico', "Histórico", 'left');
        $column_valor_saldo_transformed = new TDataGridColumn('valor_saldo', "Valor saldo", 'center');
        $column_ativo_transformed = new TDataGridColumn('ativo', "Ativo", 'center');
        $column_ativo_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if ($value === true || $value == 't' || $value === 1 || $value == '1' || $value == 's' || $value == 'S' || $value == 'T') {
                return "<span style='background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 8px; font-weight: bold;'>Sim</span>";
            } 
            elseif ($value === false || $value == 'f' || $value === 0 || $value == '0' || $value == 'n' || $value == 'N' || $value == 'F') {
                return "<span style='background-color: #F44336; color: white; padding: 2px 8px; border-radius: 8px; font-weight: bold;'>Não</span>";
            }
        
            return $value;
        });
        $column_datatransacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_cell = null)
        {
            if(!empty(trim((string) $value)))
            {
                try{
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

         $column_dtinicio->setTransformer(function($value, $object, $row, $cell = null, $last_cell = null)
        {
            if(!empty(trim((string) $value)))
            {
                try{
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });
           $column_dtfinal->setTransformer(function($value, $object, $row, $cell = null, $last_cell = null)
        {
            if(!empty(trim((string) $value)))
            {
                try{
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_valor_saldo_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_cell = null)
        {
            if(empty($value))
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ ". number_format($value, 2, ",", ".");
            }
            else{
                return $value;
            }
        });

        $column_tipotransacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_cell = null)
        {

                if($value == 'C')
                {
                    return "Crédito";
                }
                elseif($value == 'D')
                {
                    return "Débito";
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
        $this->datagrid->addColumn($column_entidade_id);
        $this->datagrid->addColumn($column_entidade_nome);
        $this->datagrid->addColumn($column_tipotransacao_transformed);
        $this->datagrid->addColumn($column_datatransacao_transformed);
        $this->datagrid->addColumn($column_dtinicio);
        $this->datagrid->addColumn($column_dtfinal);
        $this->datagrid->addColumn($column_historico);
        $this->datagrid->addColumn($column_valor_saldo_transformed);
        $this->datagrid->addColumn($column_ativo_transformed);
        $action_onEdit = new TDataGridAction(array('SaldoEntidadeContratoForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('SaldoEntidadeContratoList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

        // create the datagrid model
        $this->applyDatagridProperties();

        $this->datagrid->createModel();

        $tr = new TElement('tr');
        $this->datagrid->prependRow($tr);

        if(!$action_onEdit->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        if(!$action_onDelete->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        $td_id = TElement::tag('td', $id);
        $tr->add($td_id);
        $td_entidade_id = TElement::tag('td', $entidade_id);
        $tr->add($td_entidade_id);
        $td_entidade_nome = TElement::tag('td', $entidade_nome);
        $tr->add($td_entidade_nome);
        $td_tipotransacao = TElement::tag('td', $tipotransacao);
        $tr->add($td_tipotransacao);
        $td_datatransacao = TElement::tag('td', $datatransacao);
        $tr->add($td_datatransacao);
        $td_dtinicio = TElement::tag('td', $dtinicio);
        $tr->add($td_dtinicio);
        $td_dtfinal = TElement::tag('td', $dtfinal);
        $tr->add($td_dtfinal);
        $td_historico = TElement::tag('td', $historico);
        $tr->add($td_historico);
        $td_valor_saldo = TElement::tag('td', $valor_saldo);
        $tr->add($td_valor_saldo);


        $this->datagrid_form->addField($id);
        $this->datagrid_form->addField($entidade_id);
        $this->datagrid_form->addField($entidade_nome);
        $this->datagrid_form->addField($tipotransacao);
        $this->datagrid_form->addField($datatransacao);
        $this->datagrid_form->addField($dtinicio);
        $this->datagrid_form->addField($dtfinal);
        $this->datagrid_form->addField($historico);
        $this->datagrid_form->addField($valor_saldo);


        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de saldo entidade contratos");
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $this->datagrid_form->add($this->datagrid);
        $panel->add($headerActions);
        $panel->add($this->datagrid_form);

        $button_cadastrar = new TButton('button_button_cadastrar');
        $button_cadastrar->setAction(new TAction(['SaldoEntidadeContratoForm', 'onShow']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['SaldoEntidadeContratoList', 'onExportCsv'],['static' => 1]), self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['SaldoEntidadeContratoList', 'onExportXls'],['static' => 1]), self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['SaldoEntidadeContratoList', 'onExportPdf'],['static' => 1]), self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['SaldoEntidadeContratoList', 'onExportXml'],['static' => 1]), self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_cadastrar);

        $head_right_actions->add($dropdown_button_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Manutenção Frotas","Saldo entidade contratos"]));
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
                $object = new SaldoEntidadeContrato($key, FALSE); 

                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                TToast::show('success', AdiantiCoreTranslator::translate('Record deleted'), 'topRight', 'far:check-circle');
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
        // get the search form data
        $data = $this->datagrid_form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->tipotransacao) AND ( (is_scalar($data->tipotransacao) AND $data->tipotransacao !== '') OR (is_array($data->tipotransacao) AND (!empty($data->tipotransacao)) )) )
        {

            $filters[] = new TFilter('tipotransacao', '=', $data->tipotransacao);// create the filter 
        }

        if (isset($data->datatransacao) AND ( (is_scalar($data->datatransacao) AND $data->datatransacao !== '') OR (is_array($data->datatransacao) AND (!empty($data->datatransacao)) )) )
        {

            $filters[] = new TFilter('datatransacao', '=', $data->datatransacao);// create the filter 
        }
        if (isset($data->dtinicio) AND ( (is_scalar($data->dtinicio) AND $data->dtinicio !== '') OR (is_array($data->dtinicio) AND (!empty($data->dtinicio)) )) )
        {

            $filters[] = new TFilter('dtinicio', '=', $data->dtinicio);// create the filter 
        }
        if (isset($data->dtfinal) AND ( (is_scalar($data->dtfinal) AND $data->dtfinal !== '') OR (is_array($data->dtfinal) AND (!empty($data->dtfinal)) )) )
        {

            $filters[] = new TFilter('dtfinal', '=', $data->dtfinal);// create the filter 
        }

        if (isset($data->historico) AND ( (is_scalar($data->historico) AND $data->historico !== '') OR (is_array($data->historico) AND (!empty($data->historico)) )) )
        {

            $filters[] = new TFilter('historico', 'like', "%{$data->historico}%");// create the filter 
        }

        if (isset($data->valor_saldo) AND ( (is_scalar($data->valor_saldo) AND $data->valor_saldo !== '') OR (is_array($data->valor_saldo) AND (!empty($data->valor_saldo)) )) )
        {

            $filters[] = new TFilter('valor_saldo', '=', $data->valor_saldo);// create the filter 
        }

        // fill the form with data again
        $this->datagrid_form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        if (isset($param['static']) && ($param['static'] == '1') )
        {
            $class = get_class($this);
            $onReloadParam = ['offset' => 0, 'first_page' => 1, 'target_container' => $param['target_container'] ?? null];
            AdiantiCoreApplication::loadPage($class, 'onReload', $onReloadParam);
            TScript::create('$(".select2").prev().select2("close");');
        }
        else
        {
            $this->onReload(['offset' => 0, 'first_page' => 1]);
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

            // creates a repository for SaldoEntidadeContrato
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

            $criteria->add(new TFilter('entidade_id', '=', TSession::getValue('entidade_id')));

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
        TSession::setValue('entidade_id', null);
        TSession::setValue('entidade_id', $param['id']);
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

        $object = new SaldoEntidadeContrato($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

