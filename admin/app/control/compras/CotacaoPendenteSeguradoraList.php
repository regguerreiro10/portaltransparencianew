<?php

class CotacaoPendenteSeguradoraList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Cotacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_CotacaoPendenteList';
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
        $this->form->setFormTitle("Consulta Cotação Pendente");
        $this->limit = 20;

        $criteria_pessoa_id = new TCriteria();

        $id = new TEntry('id');
        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa_id );


        $pessoa_id->enableSearch();
        $id->setSize(100);
        $pessoa_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Pessoa id:", null, '14px', null, '100%'),$pessoa_id]);
        $row1->layout = ['col-sm-6',' col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "Id", 'left');
        $column_pedido_id = new TDataGridColumn('pedido_id', "Pedido id", 'left');
        $column_pessoa_nome = new TDataGridColumn('pessoa->nome', "Pessoa id", 'left');
        $column_data_cotacao_transformed = new TDataGridColumn('data_cotacao', "Data cotação", 'left');
        $column__transformed = new TDataGridColumn('', "Vl Total Pedido", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_total', "Vl Total Cotação", 'left');
        $column_system_users_name = new TDataGridColumn('system_users->name', "Usuário", 'left');
        $column_estado_pedido_nome_transformed = new TDataGridColumn('estado_pedido->nome', "Estado da Cotação", 'left');
        $column_cidade_id_transformed = new TDataGridColumn('cidade_id', "Cidade", 'left');

        $column_data_cotacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column__transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
            // Código gerado pelo snippet: "Conexão com banco de dados"
            TTransaction::open('minierp');

                        $value=0;    
                        $objects = ItensPedido::where('pedido_venda_id','=',$object->pedido_id)
                                                  ->load();

                        if ($objects) {
                            foreach ($objects as $obj) {
                               // code...
                               $value = $value + ($obj->valor*$obj->quantidade) ;
                            }
                        }

                        return 'R$ '.number_format($value, 2, ',', '.');
                        // code

                        TTransaction::close();
                // -----

        });

        $column_valor_total_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_estado_pedido_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
                                    $temnotafiscal = false;

            if ($object->estado_pedido::FINALIZADO || $object->estado_pedido::APROVADO || $object->estado_pedido::PGTOAPROVADO || $object->estado_pedido::ENTREGUE ) {
                // var_dump($object);
            //die();  
                TTransaction::open('minierp');

                $doccot = DocumentosCotacao::where('cotacao_id','=',$object->id)
                                           ->load();
                if ($doccot){
                    $temnotafiscal = true;
                }

                TTransaction::close();
            }
            if ($temnotafiscal) {
               $anexo = $object->estado_pedido->nome.' <i class="fa fa-paperclip" aria-hidden="true"></i>';
                return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido->cor}'> {$anexo} <span>";
            } else {
                return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido->cor}'> {$object->estado_pedido->nome} <span>";
            }            

        });

        $column_cidade_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here

                TTransaction::open('minierp');

                $cidade = new Cidade($object->cidade_id);
                if ($cidade) {
                    $estado = new Estado($cidade->estado_id);
                    return "{$cidade->nome} - {$estado->sigla}";

                } else {
                    return "Não informado!!!";

                }

                TTransaction::close();

        });        

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_pedido_id);
        $this->datagrid->addColumn($column_pessoa_nome);
        $this->datagrid->addColumn($column_data_cotacao_transformed);
        $this->datagrid->addColumn($column__transformed);
        $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column_system_users_name);
        $this->datagrid->addColumn($column_estado_pedido_nome_transformed);
        $this->datagrid->addColumn($column_cidade_id_transformed);

        $action_onAprovar = new TDataGridAction(array('CotacaoPendenteSeguradoraList', 'onAprovar'));
        $action_onAprovar->setUseButton(false);
        $action_onAprovar->setButtonClass('btn btn-default btn-sm');
        $action_onAprovar->setLabel("Aprovar");
        $action_onAprovar->setImage('fas:thumbs-up #9C27B0');
        $action_onAprovar->setField(self::$primaryKey);
        $action_onAprovar->setDisplayCondition('CotacaoPendenteSeguradoraList::onExibirAprovada');

        $this->datagrid->addAction($action_onAprovar);

        $action_onReprovar = new TDataGridAction(array('CotacaoPendenteSeguradoraList', 'onReprovar'));
        $action_onReprovar->setUseButton(false);
        $action_onReprovar->setButtonClass('btn btn-default btn-sm');
        $action_onReprovar->setLabel("Reprovar");
        $action_onReprovar->setImage('fas:thumbs-down #F44336');
        $action_onReprovar->setField(self::$primaryKey);
        $action_onReprovar->setDisplayCondition('CotacaoPendenteSeguradoraList::onExibirReprovada');

        $this->datagrid->addAction($action_onReprovar);

        $action_onGenerate = new TDataGridAction(array('CotacaoVendaDocument', 'onGenerate'));
        $action_onGenerate->setUseButton(false);
        $action_onGenerate->setButtonClass('btn btn-default btn-sm');
        $action_onGenerate->setLabel("Documento");
        $action_onGenerate->setImage('far:file-pdf #000000');
        $action_onGenerate->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onGenerate);

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
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['CotacaoPendenteSeguradoraList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['CotacaoPendenteSeguradoraList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['CotacaoPendenteSeguradoraList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['CotacaoPendenteSeguradoraList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_right_actions->add($dropdown_button_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Compras","CotacaoPendenteSeguradoraList"]));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public function onAprovar($param = null) 
    {

        if (isset($param['confirmAprovarEnviarEmail']) && $param['confirmAprovarEnviarEmail']) {
            try {

                TTransaction::open(self::$database);
                $cotacao = new Cotacao($param['id']);
                $cotacao->estado_pedido_id = EstadoPedido::APROVADO;
                $cotacao->store();

          //     var_dump($param);

                $pedido = new Pedido($cotacao->pedido_id);
                $pedido->cliente_id = $cotacao->pessoa_id;
                $pedido->estado_pedido_venda_id = EstadoPedido::APROVADO;

                $pessoaendereco = PessoaEndereco::where('pessoa_id','=',$cotacao->pessoa_id)
                                                ->where('principal','=','T')
                                                ->load();
                if ($pessoaendereco) {
                    foreach($pessoaendereco as $pe) {
                       $pedido->cidade_id = $pe->cidade_id;
                    }
                }

                $pedido->store();

                $this->registrarHistoricoPedido($pedido);

                $this->registrarHistoricoCotacao($cotacao);

                TTransaction::close();
                TToast::show('success', "Aprovação da proposta realizada com sucesso!!!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoCompraSeguradoraList', 'onSetProject');

            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onAprovar'));
            $action->setParameters($param);
            $action->setParameter('confirmAprovarEnviarEmail', true);

            new TQuestion('Tem certeza que deseja aprovar esta proposta?', $action);
        }

       /* try 
        {
            //code here
*/
            //</autoCode>
  /*      }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }
    public static function onExibirAprovada($object)
    {
        try 
        {
            if( in_array($object->estado_pedido_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_id, [EstadoPedido::AGUARDANDO]) )
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onReprovar($param = null) 
    {
        try 
        {
            //code here
            if (isset($param['confirmReprovarEnviarEmail']) && $param['confirmReprovarEnviarEmail']) {
            try {

                TTransaction::open(self::$database);

                $cotacao = new Cotacao($param['id']);
                $cotacao->estado_pedido_id = EstadoPedido::REPROVADO;
                $cotacao->store();

                $reprovarpedido=true;
                $cotacao1 = Cotacao::where('pedido_id','=',$cotacao->pedido_id)
                                   ->load();
                foreach ($cotacao1 as $cot) {
                     if (!in_array($cot->estado_pedido_id, [EstadoPedido::REPROVADO]) ){
                         $reprovarpedido=false;
                    } 
                }

                if ($reprovarpedido)
                {
                    $pedido = new Pedido($cotacao->pedido_id);
                    $pedido->estado_pedido_venda_id = EstadoPedido::REPROVADO;
                    $pedido->store();

                    $this->registrarHistoricoPedidoReprovar($pedido);
                }

          //     var_dump($param);

            //    $pedido = new Pedido($cotacao->pedido_id);
             //   $pedido->estado_pedido_venda_id = EstadoPedido::REPROVADO;
            //    $pedido->store();

               // $this->registrarHistoricoPedidoReprovar($pedido);

                $this->registrarHistoricoCotacaoReprovar($cotacao);

                TTransaction::close();
                TToast::show('success', "Proposta reprovada com sucesso!!!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoCompraSeguradoraList', 'onSetProject');
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onReprovar'));
            $action->setParameters($param);
            $action->setParameter('confirmReprovarEnviarEmail', true);

            new TQuestion('Tem certeza que deseja reprovar esta proposta?', $action);
        }
            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public static function onExibirReprovada($object)
    {
        try 
        {
            if( in_array($object->estado_pedido_id, Aprovador::getEstadosDisponiveis()) && in_array($object->estado_pedido_id, [EstadoPedido::AGUARDANDO]) )
            {
                return true;
            }

            return false;
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

        if (isset($data->pessoa_id) AND ( (is_scalar($data->pessoa_id) AND $data->pessoa_id !== '') OR (is_array($data->pessoa_id) AND (!empty($data->pessoa_id)) )) )
        {

            $filters[] = new TFilter('pessoa_id', '=', $data->pessoa_id);// create the filter 
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

            // creates a repository for Cotacao
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

            $criteria->add(new TFilter('pedido_id', '=', TSession::getValue('idpedidocp'))); 

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

    public static function manageRow($id)
    {
        $list = new self([]);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Cotacao($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    function onSetProject($param = null) {
        TSession::setValue('idpedidocp',NULL);
       TSession::setValue('idpedidocp',$param['id']);  
       $this->onReload();
    }
    private function registrarHistoricoPedido($pedido)
    {
        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::APROVADO; 
        $hist->aprovador_id = TSession::getValue('iduser');
        $hist->store();
    }

    private function registrarHistoricoCotacao($cotacao)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::APROVADO; 
        $histcotacao->aprovador_id = TSession::getValue('iduser');
        $histcotacao->store();
    }
     private function registrarHistoricoPedidoReprovar($pedido)
    {
        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::REPROVADO; 
        $hist->aprovador_id = TSession::getValue('iduser');
        $hist->store();
    }

    private function registrarHistoricoCotacaoReprovar($cotacao)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::REPROVADO; 
        $histcotacao->aprovador_id = TSession::getValue('iduser');
        $histcotacao->store();
    }

}

