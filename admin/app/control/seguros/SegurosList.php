<?php

class SegurosList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Seguros';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Seguros';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters', 'onGlobalSearch'];
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

        $basename   = urlencode('seguros-list.pdf');
        $download   = "download.php?file=app/manual/seguros-list.pdf&basename={$basename}";

        $manual = "
            <span style='float:right;'>
                <a href='{$download}'
                target='_blank'
                style='text-decoration:none;margin-left:10px;'>
                    <i class='fa fa-question-circle'> </i>
                </a>
            </span>
        "; 

        $this->limit = 20;

        $criteria_saldo_entidade_contrato_id = new TCriteria();
        $criteria_tipo_seguro_id = new TCriteria();

        $id = new TEntry('id');
        $saldo_entidade_contrato_id = new TDBCombo('saldo_entidade_contrato_id', 'minierp', 'SaldoEntidadeContrato', 'id', '{id} - {historico} - R${valor_saldo}','id asc' , $criteria_saldo_entidade_contrato_id );
        $tipo_seguro_id = new TDBCombo('tipo_seguro_id', 'minierp', 'TipoSeguro', 'id', '{descricao}','id asc' , $criteria_tipo_seguro_id );
        $data_inicio = new TEntry('data_inicio');
        $data_final = new TEntry('data_final');
        $numero_apolice = new TEntry('numero_apolice');
        $valor_cobertura = new TEntry('valor_cobertura');
        $obs = new TEntry('obs');

        $id->exitOnEnter();
        $data_inicio->exitOnEnter();
        $data_final->exitOnEnter();
        $numero_apolice->exitOnEnter();
        $valor_cobertura->exitOnEnter();
        $obs->exitOnEnter();

        $id->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $data_inicio->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $data_final->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $numero_apolice->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $valor_cobertura->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $obs->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $saldo_entidade_contrato_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $tipo_seguro_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $tipo_seguro_id->enableSearch();
        $saldo_entidade_contrato_id->enableSearch();

        $id->setSize('100%');
        $obs->setSize('100%');
        $data_final->setSize('100%');
        $data_inicio->setSize('100%');
        $tipo_seguro_id->setSize('100%');
        $numero_apolice->setSize('100%');
        $valor_cobertura->setSize('100%');
        $saldo_entidade_contrato_id->setSize('100%');

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

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_saldo_entidade_contrato_id = new TDataGridColumn('saldo_entidade_contrato_id', "Saldo entidade contrato id", 'left');
        $column_tipo_seguro_id = new TDataGridColumn('tipo_seguro->descricao', "Tipo seguro", 'left');
        $column_data_inicio = new TDataGridColumn('data_inicio', "Data inicio", 'left');
        $column_data_final = new TDataGridColumn('data_final', "Data final", 'left');
        $column_numero_apolice = new TDataGridColumn('numero_apolice', "Número apólice", 'left');
        $column_valor_cobertura = new TDataGridColumn('valor_cobertura', "Valor cobertura", 'left');
        $column_obs = new TDataGridColumn('obs', "Obs", 'left');

        $column_saldo_entidade_contrato_id->setTransformer(function($value, $object, $row){
            $rel = $object->saldo_entidade_contrato ?? null;

            if($rel){
                $id = $rel->id ?? '';
                $historico = $rel->historico ?? '';
                $valor_saldo = isset($rel->valor_saldo) ? number_format((float)$rel->valor_saldo, 2, ',', '.') : '0,00';

                return "{$id} - {$historico} - R$ {$valor_saldo}";
            }

            return '';
        });

        $column_valor_cobertura->setTransformer(function($value, $object, $row){
            if($value === null || $value === ''){
                return '';
            }
            
            $v = (float) $value;
            return 'R$ ' . number_format($v, 2, ',', '.');
        });

        $column_data_inicio->setTransformer(function($value)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $data = new DateTime($value);
                    return $data->format('d/m/y');
                }
                catch(Exception $e)
                {
                    return $value;
                }
            }

        });

        $column_data_final->setTransformer(function($value)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $data = new DateTime($value);
                    return $data->format('d/m/y');
                }
                catch(Exception $e)
                {
                    return $value;
                }
            }

        });

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_saldo_entidade_contrato_id);
        $this->datagrid->addColumn($column_tipo_seguro_id);
        $this->datagrid->addColumn($column_data_inicio);
        $this->datagrid->addColumn($column_data_final);
        $this->datagrid->addColumn($column_numero_apolice);
        $this->datagrid->addColumn($column_valor_cobertura);
        $this->datagrid->addColumn($column_obs);

        $action_onEdit = new TDataGridAction(array('SegurosForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('SegurosList', 'onDelete'));
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
        $tr->id = 'datagrid-header-filter-row';
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
        $td_saldo_entidade_contrato_id = TElement::tag('td', $saldo_entidade_contrato_id);
        $tr->add($td_saldo_entidade_contrato_id);
        $td_tipo_seguro_id = TElement::tag('td', $tipo_seguro_id);
        $tr->add($td_tipo_seguro_id);
        $td_data_inicio = TElement::tag('td', $data_inicio);
        $tr->add($td_data_inicio);
        $td_data_final = TElement::tag('td', $data_final);
        $tr->add($td_data_final);
        $td_numero_apolice = TElement::tag('td', $numero_apolice);
        $tr->add($td_numero_apolice);
        $td_valor_cobertura = TElement::tag('td', $valor_cobertura);
        $tr->add($td_valor_cobertura);
        $td_obs = TElement::tag('td', $obs);
        $tr->add($td_obs);

        $this->datagrid_form->addField($id);
        $this->datagrid_form->addField($saldo_entidade_contrato_id);
        $this->datagrid_form->addField($tipo_seguro_id);
        $this->datagrid_form->addField($data_inicio);
        $this->datagrid_form->addField($data_final);
        $this->datagrid_form->addField($numero_apolice);
        $this->datagrid_form->addField($valor_cobertura);
        $this->datagrid_form->addField($obs);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de seguros {$manual}");
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

        $this->datagrid_form->add($headerActions);
        $panel->add($this->datagrid_form);

        $button_cadastrar = new TButton('button_button_cadastrar');
        $button_cadastrar->setAction(new TAction(['SegurosForm', 'onShow']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['SegurosList', 'onExportCsv'],['static' => 1]), self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['SegurosList', 'onExportXls'],['static' => 1]), self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['SegurosList', 'onExportPdf'],['static' => 1]), self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['SegurosList', 'onExportXml'],['static' => 1]), self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_cadastrar);

        $head_right_actions->add($dropdown_button_exportar);

        $this->datagrid_form->add($this->datagrid);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
         //   $container->add(TBreadCrumb::create(["Compras","Seguros"]));
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

                $seguros_id = $param['id'];
                $tables = [
                    'AnexosSeguros' => ['column' => 'seguros_id', 'alias' => 'Anexos Seguros']
                ];
                foreach($tables as $table => $info)
                {
                    $repository = new TRepository($table);
                    $criteria = new TCriteria();
                    $criteria->add(new TFilter($info['column'], '=', $seguros_id));

                    if($repository->count($criteria) > 0)
                    {
                        throw new Exception("Não é possivel excluir esse seguro, pois existem arquivos associados em {$info['alias']}.");
                    }
                }
                
                // instantiates object
                $object = new Seguros($key, FALSE); 

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

        if (isset($data->saldo_entidade_contrato_id) AND ( (is_scalar($data->saldo_entidade_contrato_id) AND $data->saldo_entidade_contrato_id !== '') OR (is_array($data->saldo_entidade_contrato_id) AND (!empty($data->saldo_entidade_contrato_id)) )) )
        {

            $filters[] = new TFilter('saldo_entidade_contrato_id', '=', $data->saldo_entidade_contrato_id);// create the filter 
        }

        if (isset($data->tipo_seguro_id) AND ( (is_scalar($data->tipo_seguro_id) AND $data->tipo_seguro_id !== '') OR (is_array($data->tipo_seguro_id) AND (!empty($data->tipo_seguro_id)) )) )
        {

            $filters[] = new TFilter('tipo_seguro_id', '=', $data->tipo_seguro_id);// create the filter 
        }

        if (isset($data->data_inicio) AND ( (is_scalar($data->data_inicio) AND $data->data_inicio !== '') OR (is_array($data->data_inicio) AND (!empty($data->data_inicio)) )) )
        {

            $filters[] = new TFilter('data_inicio', '=', $data->data_inicio);// create the filter 
        }

        if (isset($data->data_final) AND ( (is_scalar($data->data_final) AND $data->data_final !== '') OR (is_array($data->data_final) AND (!empty($data->data_final)) )) )
        {

            $filters[] = new TFilter('data_final', '=', $data->data_final);// create the filter 
        }

        if (isset($data->numero_apolice) AND ( (is_scalar($data->numero_apolice) AND $data->numero_apolice !== '') OR (is_array($data->numero_apolice) AND (!empty($data->numero_apolice)) )) )
        {

            $filters[] = new TFilter('numero_apolice', 'like', "%{$data->numero_apolice}%");// create the filter 
        }

        if (isset($data->valor_cobertura) AND ( (is_scalar($data->valor_cobertura) AND $data->valor_cobertura !== '') OR (is_array($data->valor_cobertura) AND (!empty($data->valor_cobertura)) )) )
        {

            $filters[] = new TFilter('valor_cobertura', '=', $data->valor_cobertura);// create the filter 
        }

        if (isset($data->obs) AND ( (is_scalar($data->obs) AND $data->obs !== '') OR (is_array($data->obs) AND (!empty($data->obs)) )) )
        {

            $filters[] = new TFilter('obs', 'like', "%{$data->obs}%");// create the filter 
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

            // creates a repository for Seguros
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
            $criteria->add(new TFilter('saldo_entidade_contrato_id', 'in', "(SELECT id FROM saldo_entidade_contrato WHERE entidade_id = ".TSession::getValue('entidade').")"));

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

        $object = new Seguros($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

