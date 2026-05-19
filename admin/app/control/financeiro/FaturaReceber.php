<?php

class FaturaReceber extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Conta';
    private static $primaryKey = 'id';
    private static $formName = 'form_ContaPagarList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

    private $filtrarContasAtrasadas = false;
    private $filtrarContasAbertas = false;
    private $filtrarContasQuitadas = false;

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

        $basename   = urlencode('pedido-compra-list.pdf');
        $download   = "download.php?file=app/manual/listagem-contas-pagar.pdf&basename={$basename}";

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
        $this->form->setFormTitle("Listagem de faturas {$manual}");
        $this->limit = 20;

        $criteria_categoria_id = new TCriteria();
        $criteria_forma_pagamento_id = new TCriteria();

        $filterVar = TipoConta::PAGAR;
        $criteria_categoria_id->add(new TFilter('tipo_conta_id', '=', $filterVar)); 

        $id = new TEntry('id');
        $dt_emissao = new BDateRange('dt_emissao', 'dt_emissao_fim');
        $categoria_id = new TDBCombo('categoria_id', 'minierp', 'Categoria', 'id', '{nome}','nome asc' , $criteria_categoria_id );
        $forma_pagamento_id = new TDBCombo('forma_pagamento_id', 'minierp', 'FormaPagamento', 'id', '{nome}','nome asc' , $criteria_forma_pagamento_id );
        $dt_vencimento = new BDateRange('dt_vencimento', 'dt_vencimento_fim');
        $filtro_rapido = new TRadioGroup('filtro_rapido');


        $filtro_rapido->addItems(["atrasadas"=>"Atrasadas","abertas"=>"Abertas","quitadas"=>"Quitadas"]);
        $filtro_rapido->setLayout('horizontal');
        $filtro_rapido->setUseButton();
        $dt_emissao->setMask('dd/mm/yyyy');
        $dt_vencimento->setMask('dd/mm/yyyy');

        $dt_emissao->setDatabaseMask('yyyy-mm-dd');
        $dt_vencimento->setDatabaseMask('yyyy-mm-dd');

        $categoria_id->enableSearch();
        $forma_pagamento_id->enableSearch();

        $id->setSize('100%');
        $dt_emissao->setSize(220);
        $filtro_rapido->setSize(80);
        $dt_vencimento->setSize(220);
        $categoria_id->setSize('100%');
        $forma_pagamento_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Data Aprovação:", null, '14px', null, '100%'),$dt_emissao]);
        $row1->layout = ['col-sm-6',' col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Categoria:", null, '14px', null, '100%'),$categoria_id],[new TLabel("Forma de pagamento:", null, '14px', null, '100%'),$forma_pagamento_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Data de vencimento:", null, '14px', null, '100%'),$dt_vencimento],[new TLabel("Filtros rápidos:", null, '14px', null, '100%'),$filtro_rapido]);
        $row3->layout = ['col-sm-6',' col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $filterVar = TipoConta::PAGAR;
        $this->filter_criteria->add(new TFilter('tipo_conta_id', '=', $filterVar));

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);
 
        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_pedido_venda_id = new TDataGridColumn('pedido_venda_id', "Pedido  id", 'left');
        $column_pessoa_nome = new TDataGridColumn('pessoa->nome', "Fornecedor", 'left');
        $column_categoria_nome = new TDataGridColumn('categoria->nome', "Categoria", 'left');
        $column_forma_pagamento_nome = new TDataGridColumn('forma_pagamento->nome', "Forma pgto", 'left');
        $column_dt_emissao_transformed = new TDataGridColumn('dt_emissao', "Data Aprovação", 'center');
        $column_dt_vencimento_transformed = new TDataGridColumn('dt_vencimento', "Data vcto", 'center');
        $column_dt_pagamento_transformed = new TDataGridColumn('dt_pagamento', "Data pgto", 'center');
        $column_parcela = new TDataGridColumn('parcela', "Parcela", 'center');
        $column_valor_transformed = new TDataGridColumn('valor', "Valor Bruto", 'left');
        $column_valor_txcontrato_transformed = new TDataGridColumn('valor_txcontrato', "Vl TxContrato", 'left');
        $column_valor_txadm_transformed = new TDataGridColumn('valor_txadm', "Vl TxAdm", 'left');
        $column_valor_txantecipacao_transformed = new TDataGridColumn('valor_txantecipacao', "Vl TxAntecipação", 'left');
        $column_valor_liquido_transformed = new TDataGridColumn('valor_liquido', "Valor Liquido", 'left');
        $column_valor_imp_prod_transformed = new TDataGridColumn('vl_imp_prod', "Vl Imp Prod.", 'left');
        $column_valor_imp_serv_transformed = new TDataGridColumn('vl_imp_serv', "Vl Imp Serv.", 'left');
        $column_valor_total_liq_tx_conta_transformed = new TDataGridColumn('valor_total_liq_tx_conta', "Vl Total Liquido", 'left');
        $column_status = new TDataGridColumn('status', "Status", 'center');
        $column_faturada = new TDataGridColumn('fatura_id', "Faturada", 'center');

        $column_dt_emissao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_dt_vencimento_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_dt_pagamento_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_valor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_valor_txcontrato_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_valor_txadm_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
 
        $column_valor_txantecipacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_valor_liquido_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
  $column_valor_imp_prod_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_valor_imp_serv_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_valor_total_liq_tx_conta_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $this->builder_datagrid_check_all = new TCheckButton('builder_datagrid_check_all');
        $this->builder_datagrid_check_all->setIndexValue('on');
        $this->builder_datagrid_check_all->onclick = "Builder.checkAll(this)";
        $this->builder_datagrid_check_all->style = 'cursor:pointer';
        $this->builder_datagrid_check_all->setProperty('class', 'filled-in');
        $this->builder_datagrid_check_all->id = 'builder_datagrid_check_all';

        $label = new TLabel('');
        $label->style = 'margin:0';
        $label->class = 'checklist-label';
        $this->builder_datagrid_check_all->after($label);
        $label->for = 'builder_datagrid_check_all';

        $this->builder_datagrid_check = $this->datagrid->addColumn( new TDataGridColumn('builder_datagrid_check', $this->builder_datagrid_check_all, 'center',  '1%') );

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_pedido_venda_id);
        $this->datagrid->addColumn($column_pessoa_nome);
        $this->datagrid->addColumn($column_categoria_nome);
        $this->datagrid->addColumn($column_forma_pagamento_nome);
        $this->datagrid->addColumn($column_dt_emissao_transformed);
        $this->datagrid->addColumn($column_dt_vencimento_transformed);
        $this->datagrid->addColumn($column_dt_pagamento_transformed);
        $this->datagrid->addColumn($column_parcela);
        $this->datagrid->addColumn($column_valor_transformed);
        $this->datagrid->addColumn($column_valor_txcontrato_transformed);
        $this->datagrid->addColumn($column_valor_txadm_transformed);
        $this->datagrid->addColumn($column_valor_txantecipacao_transformed);
        $this->datagrid->addColumn($column_valor_imp_prod_transformed);
        $this->datagrid->addColumn($column_valor_imp_serv_transformed);
        $this->datagrid->addColumn($column_valor_total_liq_tx_conta_transformed);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_faturada);


        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de contas a pagar {$manual}");
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
        $btnShowCurtainFilters->setAction(new TAction(['FaturaReceber', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['FaturaReceber', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['FaturaReceber', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atrasadas = new TButton('button_button_atrasadas');
        $button_atrasadas->setAction(new TAction(['FaturaReceber', 'onFiltrarAtrasadas']), "Atrasadas");
        $button_atrasadas->addStyleClass('btn-default');
        $button_atrasadas->setImage('fas:money-bill-wave #F44336');

        $this->datagrid_form->addField($button_atrasadas);

        $button_abertas = new TButton('button_button_abertas');
        $button_abertas->setAction(new TAction(['FaturaReceber', 'onFiltrarAbertas']), "Abertas");
        $button_abertas->addStyleClass('btn-default');
        $button_abertas->setImage('fas:money-bill-wave #FFC107');

        $this->datagrid_form->addField($button_abertas);

        $button_quitadas = new TButton('button_button_quitadas');
        $button_quitadas->setAction(new TAction(['FaturaReceber', 'onFiltrarQuitadas']), "Quitadas");
        $button_quitadas->addStyleClass('btn-default');
        $button_quitadas->setImage('fas:money-bill-wave #4CAF50');

        $this->datagrid_form->addField($button_quitadas);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['FaturaReceber', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['FaturaReceber', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['FaturaReceber', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['FaturaReceber', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_atualizar);
        $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($button_atrasadas);
        $head_left_actions->add($button_abertas);
        $head_left_actions->add($button_quitadas);

        $head_right_actions->add($dropdown_button_exportar);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Financeiro","Fatura a receber"]));
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
                                $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
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
                    else if (strpos($column->getWidth(), '%') !== false)
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
                                $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                            }

                            $transformer = $column->getTransformer();
                            if ($transformer)
                            {
                                $value = strip_tags(call_user_func($transformer, $value, $object, null));
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
                $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = $output;
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
                                $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
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
            $page->setProperty('page-name', 'FaturaReceberSearch');
            $page->setProperty('page_name', 'FaturaReceberSearch');
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
    public function onRefresh($param = null) 
    {
        $this->onReload([]);
    }
    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }
    public function onFiltrarAtrasadas($param = null) 
    {
        try 
        {

            $this->filtrarContasAtrasadas = true;
            $this->onSearch([]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onFiltrarAbertas($param = null) 
    {
        try 
        {

            $this->filtrarContasAbertas = true;
            $this->onSearch([]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onFiltrarQuitadas($param = null) 
    {
        try 
        {

            $this->filtrarContasQuitadas = true;
            $this->onSearch([]);

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

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->dt_emissao_fim) AND ( (is_scalar($data->dt_emissao_fim) AND $data->dt_emissao_fim !== '') OR (is_array($data->dt_emissao_fim) AND (!empty($data->dt_emissao_fim)) )) )
        {

            $filters[] = new TFilter('dt_emissao', '<=', $data->dt_emissao_fim);// create the filter 
        }

        if (isset($data->dt_emissao) AND ( (is_scalar($data->dt_emissao) AND $data->dt_emissao !== '') OR (is_array($data->dt_emissao) AND (!empty($data->dt_emissao)) )) )
        {

            $filters[] = new TFilter('dt_emissao', '>=', $data->dt_emissao);// create the filter 
        }

        if (isset($data->categoria_id) AND ( (is_scalar($data->categoria_id) AND $data->categoria_id !== '') OR (is_array($data->categoria_id) AND (!empty($data->categoria_id)) )) )
        {

            $filters[] = new TFilter('categoria_id', '=', $data->categoria_id);// create the filter 
        }

        if (isset($data->forma_pagamento_id) AND ( (is_scalar($data->forma_pagamento_id) AND $data->forma_pagamento_id !== '') OR (is_array($data->forma_pagamento_id) AND (!empty($data->forma_pagamento_id)) )) )
        {

            $filters[] = new TFilter('forma_pagamento_id', '=', $data->forma_pagamento_id);// create the filter 
        }

        if (isset($data->dt_vencimento) AND ( (is_scalar($data->dt_vencimento) AND $data->dt_vencimento !== '') OR (is_array($data->dt_vencimento) AND (!empty($data->dt_vencimento)) )) )
        {

            $filters[] = new TFilter('dt_vencimento', '>=', $data->dt_vencimento);// create the filter 
        }

        if($this->filtrarContasQuitadas || $data->filtro_rapido == 'quitadas')
        {
            $data->filtro_rapido = 'quitadas';

            $filters[] = new TFilter('dt_pagamento', 'is not', NULL);
        }
        elseif($this->filtrarContasAtrasadas || $data->filtro_rapido == 'atrasadas')
        {
            $data->filtro_rapido = 'atrasadas';

            $filters[] = new TFilter('dt_vencimento', '<', date('Y-m-d'));
            $filters[] = new TFilter('dt_pagamento', 'is', NULL);
        }
        elseif($this->filtrarContasAbertas || $data->filtro_rapido == 'abertas')
        {
            $data->filtro_rapido = 'abertas';

            $filters[] = new TFilter('dt_vencimento', '>=', date('Y-m-d'));
            $filters[] = new TFilter('dt_pagamento', 'is', NULL);
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

            // creates a repository for Conta
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

            $session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');

            //</blockLine><btnShowCurtainFiltersAutoCode>
            if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }
            //</blockLine></btnShowCurtainFiltersAutoCode>

            $pessoa = Pessoa::where('system_user_id','=',TSession::getValue('userid')) // system_user_id do fornecedor
                            ->load();
            $grupo = SystemUserGroup::where('system_user_id','=',TSession::getValue('userid'))
                                ->where('system_group_id','=',1 ) //admin
                            ->load();
          //  var_dump($pessoa);
            if ($pessoa) {
                foreach($pessoa as $pe){
                    $codpessoa = $pe->id;
                }
                if ($grupo){
                } else {
                   if ($pessoa){
                       //var_dump($pessoa);
                      $criteria->add(new TFilter('pessoa_id', '=', $codpessoa));
                    }
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
                    $check = new TCheckGroup('builder_datagrid_check');
                    $check->addItems([$object->id => '']);
                    $check->getButtons()[$object->id]->onclick = 'event.stopPropagation()';

                    if(!$this->datagrid_form->getField('builder_datagrid_check[]'))
                    {
                        $this->datagrid_form->setFields([$check]);
                    }

                    $check->setChangeAction(new TAction([$this, 'builderSelectCheck']));
                    $object->builder_datagrid_check = $check;

                    if(!empty($session_checks[$object->id]))
                    {
                        $object->builder_datagrid_check->setValue([$object->id=>$object->id]);
                    }

                    if($object->dt_pagamento)
                    {
                        unset($object->builder_datagrid_check);
                    }

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

    public static function builderSelectCheck($param)
    {
        $session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');

        $valueOn = null;
        if(!empty($param['_field_data_json']))
        {
            $obj = json_decode($param['_field_data_json']);
            if($obj)
            {
                $valueOn = $obj->valueOn;
            }
        }

        $key = empty($param['key']) ? $valueOn : $param['key'];

        if(empty($param['builder_datagrid_check']) && !empty($session_checks[$key]))
        {
            unset($session_checks[$key]);
        }
        elseif(!empty($param['builder_datagrid_check']) && !in_array($key, $param['builder_datagrid_check']) && !empty($session_checks[$key]))
        {
            unset($session_checks[$key]);
        }
        elseif(!empty($param['builder_datagrid_check']) && in_array($key, $param['builder_datagrid_check']))
        {
            $session_checks[$key] = $key;
        }

        TSession::setValue(__CLASS__.'builder_datagrid_check', $session_checks);
    }

    public static function manageRow($id)
    {
        $list = new self([]);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Conta($id);

        $session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');

        $check = new TCheckGroup('builder_datagrid_check');
        $check->addItems([$object->id => '']);
        $check->getButtons()[$object->id]->onclick = 'event.stopPropagation()';

        if(!$list->datagrid_form->getField('builder_datagrid_check[]'))
        {
            $list->datagrid_form->setFields([$check]);
        }

        $check->setChangeAction(new TAction([$list, 'builderSelectCheck']));
        $object->builder_datagrid_check = $check;

        if(!empty($session_checks[$object->id]))
        {
            $object->builder_datagrid_check->setValue([$object->id=>$object->id]);
        }

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

