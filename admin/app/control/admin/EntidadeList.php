<?php

class EntidadeList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Entidade';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Entidade';
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
        $nome = new TEntry('nome');
        $cnpj = new TEntry('cnpj');
        $email = new TEntry('email');
        $cep = new TEntry('cep');
        $rua = new TEntry('rua');
        $numero = new TEntry('numero');
        $bairro = new TEntry('bairro');
        $complemento = new TEntry('complemento');
        $cidade_id = new TEntry('cidade_id');
        $telefone01 = new TEntry('telefone01');
        $telefone02 = new TEntry('telefone02');
        $taxacontrato = new TEntry('taxacontrato');
        $id->exitOnEnter();
        $nome->exitOnEnter();
        $cnpj->exitOnEnter();
        $email->exitOnEnter();
        $cep->exitOnEnter();
        $rua->exitOnEnter();
        $numero->exitOnEnter();
        $bairro->exitOnEnter();
        $complemento->exitOnEnter();
        $cidade_id->exitOnEnter();
        $telefone01->exitOnEnter();
        $telefone02->exitOnEnter();

        $id->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $nome->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $cnpj->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $email->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $cep->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $rua->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $numero->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $bairro->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $complemento->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $cidade_id->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $telefone01->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $telefone02->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $id->setSize('100%');
        $cep->setSize('100%');
        $rua->setSize('100%');
        $nome->setSize('100%');
        $cnpj->setSize('100%');
        $email->setSize('100%');
        $numero->setSize('100%');
        $bairro->setSize('100%');
        $cidade_id->setSize('100%');
        $telefone01->setSize('100%');
        $telefone02->setSize('100%');
        $complemento->setSize('100%');

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_nome = new TDataGridColumn('nome', "Nome", 'left');
        $column_cnpj = new TDataGridColumn('cnpj', "Cnpj", 'left');
        $column_email = new TDataGridColumn('email', "Email", 'left');
        $column_cep = new TDataGridColumn('cep', "Cep", 'left');
        $column_rua = new TDataGridColumn('rua', "Rua", 'left');
        $column_numero = new TDataGridColumn('numero', "Numero", 'left');
        $column_taxacontrato = new TDataGridColumn('taxacontrato', "Taxa Contrato(%)", 'left');
        // $column_complemento = new TDataGridColumn('complemento', "Complemento", 'left');
        $column_cidade_nome = new TDataGridColumn('cidade_nome', "Cidade", 'left');
        // $column_telefone01 = new TDataGridColumn('telefone01', "Telefone01", 'left');
        // $column_telefone02 = new TDataGridColumn('telefone02', "Telefone02", 'left');

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cnpj);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_cep);
        $this->datagrid->addColumn($column_rua);
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_taxacontrato);
        // $this->datagrid->addColumn($column_complemento);
        $this->datagrid->addColumn($column_cidade_nome);
        // $this->datagrid->addColumn($column_telefone01);
        // $this->datagrid->addColumn($column_telefone02);
   $column_taxacontrato->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "" . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });  
        $action_onEdit = new TDataGridAction(array('EntidadeForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('EntidadeList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

        $action_onSaldoContrato = new TDataGridAction(array('SaldoEntidadeContratoList', 'onSetProject'));
        $action_onSaldoContrato->setUseButton(false);
        $action_onSaldoContrato->setButtonClass('btn btn-default btn-sm');
        $action_onSaldoContrato->setLabel("Saldo Contratual");
        $action_onSaldoContrato->setImage('fas: fa-money-bill-alt #bf930d'); 
        $action_onSaldoContrato->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onSaldoContrato);

        $action_onSystemUnit = new TDataGridAction(array('EntidadeSystemUnitFormList', 'onShow'));
        $action_onSystemUnit->setUseButton(false);
        $action_onSystemUnit->setButtonClass('btn btn-default btn-sm');
        $action_onSystemUnit->setLabel("Unidades Consolidadas");
        $action_onSystemUnit->setImage('fas: fa-layer-group #bf0d6f'); 
        $action_onSystemUnit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onSystemUnit);

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
        if(!$action_onSaldoContrato->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        $td_id = TElement::tag('td', $id);
        $tr->add($td_id);
        $td_nome = TElement::tag('td', $nome);
        $tr->add($td_nome);
        $td_cnpj = TElement::tag('td', $cnpj);
        $tr->add($td_cnpj);
        $td_email = TElement::tag('td', $email);
        $tr->add($td_email);
        $td_cep = TElement::tag('td', $cep);
        $tr->add($td_cep);
        $td_rua = TElement::tag('td', $rua);
        $tr->add($td_rua);
        $td_numero = TElement::tag('td', $numero);
        $tr->add($td_numero);
        $td_taxacontrato = TElement::tag('td', $taxacontrato);
        $tr->add($td_taxacontrato);
        // $td_complemento = TElement::tag('td', $complemento);
        // $tr->add($td_complemento);
        $td_cidade_id = TElement::tag('td', $cidade_id);
        $tr->add($td_cidade_id);
        // $td_telefone01 = TElement::tag('td', $telefone01);
        // $tr->add($td_telefone01);
        // $td_telefone02 = TElement::tag('td', $telefone02);
        // $tr->add($td_telefone02);

        $this->datagrid_form->addField($id);
        $this->datagrid_form->addField($nome);
        $this->datagrid_form->addField($cnpj);
        $this->datagrid_form->addField($email);
        $this->datagrid_form->addField($cep);
        $this->datagrid_form->addField($rua);
        $this->datagrid_form->addField($numero);
        $this->datagrid_form->addField($taxacontrato);
        // $this->datagrid_form->addField($complemento);
        $this->datagrid_form->addField($cidade_id);
        // $this->datagrid_form->addField($telefone01);
        // $this->datagrid_form->addField($telefone02);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de entidades");
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
        $button_cadastrar->setAction(new TAction(['EntidadeForm', 'onShow']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);


        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['EntidadeList', 'onExportCsv'],['static' => 1]), self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['EntidadeList', 'onExportXls'],['static' => 1]), self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['EntidadeList', 'onExportPdf'],['static' => 1]), self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['EntidadeList', 'onExportXml'],['static' => 1]), self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_cadastrar);

        $head_right_actions->add($dropdown_button_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
     //       $container->add(TBreadCrumb::create(["Compras","Entidades"]));
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

                $relations = [
                    'PedidoFrotas' => ['column' => 'entidade_id', 'alias' => 'Pedido de Frotas']
                ];

                foreach ($relations as $model => $info)
                {
                    $repository = new TRepository($model);
                    $criteria = new TCriteria;
                    $criteria->add(new TFilter($info['column'], '=', $key));
                    $count = $repository->count($criteria);

                    if ($count > 0)
                    {
                        throw new Exception("Não é possível excluir. Existem registros relacionados em {$info['alias']}");
                    }
                }

                // instantiates object
                $object = new Entidade($key, FALSE); 

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

        if (isset($data->nome) AND ( (is_scalar($data->nome) AND $data->nome !== '') OR (is_array($data->nome) AND (!empty($data->nome)) )) )
        {

            $filters[] = new TFilter('nome', 'like', "%{$data->nome}%");// create the filter 
        }

        if (isset($data->cnpj) AND ( (is_scalar($data->cnpj) AND $data->cnpj !== '') OR (is_array($data->cnpj) AND (!empty($data->cnpj)) )) )
        {

            $filters[] = new TFilter('cnpj', 'like', "%{$data->cnpj}%");// create the filter 
        }

        if (isset($data->email) AND ( (is_scalar($data->email) AND $data->email !== '') OR (is_array($data->email) AND (!empty($data->email)) )) )
        {

            $filters[] = new TFilter('email', 'like', "%{$data->email}%");// create the filter 
        }

        if (isset($data->cep) AND ( (is_scalar($data->cep) AND $data->cep !== '') OR (is_array($data->cep) AND (!empty($data->cep)) )) )
        {

            $filters[] = new TFilter('cep', 'like', "%{$data->cep}%");// create the filter 
        }

        if (isset($data->rua) AND ( (is_scalar($data->rua) AND $data->rua !== '') OR (is_array($data->rua) AND (!empty($data->rua)) )) )
        {

            $filters[] = new TFilter('rua', 'like', "%{$data->rua}%");// create the filter 
        }

        if (isset($data->numero) AND ( (is_scalar($data->numero) AND $data->numero !== '') OR (is_array($data->numero) AND (!empty($data->numero)) )) )
        {

            $filters[] = new TFilter('numero', 'like', "%{$data->numero}%");// create the filter 
        }

        if (isset($data->bairro) AND ( (is_scalar($data->bairro) AND $data->bairro !== '') OR (is_array($data->bairro) AND (!empty($data->bairro)) )) )
        {

            $filters[] = new TFilter('bairro', 'like', "%{$data->bairro}%");// create the filter 
        }

        if (isset($data->complemento) AND ( (is_scalar($data->complemento) AND $data->complemento !== '') OR (is_array($data->complemento) AND (!empty($data->complemento)) )) )
        {

            $filters[] = new TFilter('complemento', 'like', "%{$data->complemento}%");// create the filter 
        }

        if (isset($data->cidade_id) AND ( (is_scalar($data->cidade_id) AND $data->cidade_id !== '') OR (is_array($data->cidade_id) AND (!empty($data->cidade_id)) )) )
        {

            $filters[] = new TFilter('cidade_id', '=', $data->cidade_id);// create the filter 
        }

        if (isset($data->telefone01) AND ( (is_scalar($data->telefone01) AND $data->telefone01 !== '') OR (is_array($data->telefone01) AND (!empty($data->telefone01)) )) )
        {

            $filters[] = new TFilter('telefone01', 'like', "%{$data->telefone01}%");// create the filter 
        }

        if (isset($data->telefone02) AND ( (is_scalar($data->telefone02) AND $data->telefone02 !== '') OR (is_array($data->telefone02) AND (!empty($data->telefone02)) )) )
        {

            $filters[] = new TFilter('telefone02', 'like', "%{$data->telefone02}%");// create the filter 
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

        // creates a repository for Entidade
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

        if ($filters = TSession::getValue(__CLASS__.'_filters'))
        {
            foreach ($filters as $filter)
            {
                $criteria->add($filter);
            }
        }

        $entidade_id = (int) TSession::getValue('entidade');

        $filter_data = TSession::getValue(__CLASS__ . '_filter_data'); // dados do filtro (caixinhas)
        $id_digitado = isset($filter_data->id) && trim((string)$filter_data->id) !== '';

        if ($entidade_id > 0 && !$id_digitado)
        {
            $criteria->add(new TFilter('id', '=', $entidade_id));
        }
        else if (!$id_digitado && $entidade_id <= 0)
        {
            // sem entidade e sem id digitado -> não retorna nada
            $criteria->add(new TFilter('id', '=', 0));
    }


        // load the objects according to criteria
        $objects = $repository->load($criteria, FALSE);

        $this->datagrid->clear();
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $row = $this->datagrid->addItem($object);
                $row->id = "row_{$object->id}";
            }
        }

        // reset the criteria for record count
        $criteria->resetProperties();
        $count = $repository->count($criteria);

        $this->pageNavigation->setCount($count);
        $this->pageNavigation->setProperties($param);
        $this->pageNavigation->setLimit($this->limit);

        // close the transaction
        TTransaction::close();
        $this->loaded = true;

        return $objects;
    }
    catch (Exception $e)
    {
        new TMessage('error', $e->getMessage());
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

        $object = new Entidade($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

