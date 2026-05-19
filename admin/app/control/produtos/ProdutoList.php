<?php

class ProdutoList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private $totalProdutosBadge;
    private static $database = 'minierp';
    private static $activeRecord = 'Produto';
    private static $primaryKey = 'id';
    private static $formName = 'form_ProdutoList';
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
   
        $basename   = urlencode('produto-list.pdf');
        $download   = "download.php?file=app/manual/produto-list.pdf&basename={$basename}";

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
        $this->form->setFormTitle("Listagem de produtos {$manual}");
        $this->limit = 20;

        $criteria_tipo_produto_id = new TCriteria();
        $criteria_familia_produto_id = new TCriteria();
        $criteria_fabricante_id = new TCriteria();

        $nome = new TEntry('nome');
        $cod_barras = new TEntry('cod_barras');
        $tipo_produto_id = new TDBCombo('tipo_produto_id', 'minierp', 'TipoProduto', 'id', '{nome}','nome asc' , $criteria_tipo_produto_id );
        $familia_produto_id = new TDBCombo('familia_produto_id', 'minierp', 'FamiliaProduto', 'id', '{nome}','nome asc' , $criteria_familia_produto_id );
        $fabricante_id = new TDBCombo('fabricante_id', 'minierp', 'Fabricante', 'id', '{nome}','nome asc' , $criteria_fabricante_id );
        $ativo = new TCombo('ativo'); 


        $ativo->addItems(["T"=>"Sim","F"=>"Não"]);
        $nome->setMaxLength(255);
        $cod_barras->setMaxLength(255);

        $fabricante_id->enableSearch();
        $tipo_produto_id->enableSearch();
        $familia_produto_id->enableSearch();

        $nome->setSize('100%');
        $ativo->setSize('100%');
        $cod_barras->setSize('100%');
        $fabricante_id->setSize('100%');
        $tipo_produto_id->setSize('100%');
        $familia_produto_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Nome:", null, '14px', null, '100%'),$nome],[new TLabel("Código de barras:", null, '14px', null, '100%'),$cod_barras]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Tipo de produto:", null, '14px', null, '100%'),$tipo_produto_id],[new TLabel("Grupo do produto:", null, '14px', null, '100%'),$familia_produto_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Fabricante:", null, '14px', null, '100%'),$fabricante_id],[new TLabel("Ativo:", null, '14px', null, '100%'),$ativo]);
        $row3->layout = ['col-sm-6','col-sm-6'];

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

        $column_nome = new TDataGridColumn('nome', "Nome", 'left');
        $column_familia_produto_nome = new TDataGridColumn('familia_produto->nome', "Grupo do produto", 'left');
        $column_tipo_produto_nome = new TDataGridColumn('tipo_produto->nome', "Tipo de produto", 'left');
        $column_fabricante_nome = new TDataGridColumn('fabricante->nome', "Fabricante", 'left');
        $column_preco_venda_transformed = new TDataGridColumn('preco_venda', "Preço do produto", 'left');
        $column_qtde_estoque = new TDataGridColumn('qtde_estoque', "Qtde Estoque", 'left');
        $column_ativo_transformed = new TDataGridColumn('ativo', "Ativo", 'left');

        $column_preco_venda_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_ativo_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if($value === 'T' || $value === true || $value === 't' || $value === 's')
            {
                return '<span class="label label-success">Sim</span>';
            }

            return '<span class="label label-danger">Não</span>';

        });        

        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_familia_produto_nome);
        $this->datagrid->addColumn($column_tipo_produto_nome);
        $this->datagrid->addColumn($column_fabricante_nome);
        $this->datagrid->addColumn($column_preco_venda_transformed);
        $this->datagrid->addColumn($column_qtde_estoque);
        $this->datagrid->addColumn($column_ativo_transformed);

        $action_onShow = new TDataGridAction(array('ProdutoFormView', 'onShow'));
        $action_onShow->setUseButton(false);
        $action_onShow->setButtonClass('btn btn-default btn-sm');
        $action_onShow->setLabel("");
        $action_onShow->setImage('fas:search-plus #673AB7');
        $action_onShow->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onShow);

        $action_onEdit = new TDataGridAction(array('ProdutoForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('ProdutoList', 'onDelete'));
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

        $panel = new TPanelGroup("Listagem de produtos {$manual}");
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

        $this->totalProdutosBadge = new TElement('span');
        $this->totalProdutosBadge->id = __CLASS__ . '_total_produtos';
        $this->totalProdutosBadge->class = 'label label-default';
        $this->totalProdutosBadge->style = 'margin-right:12px; padding:4px 10px; font-size:12px; display:inline-flex; align-items:center; justify-content:center; border-radius:12px; background-color:#6c757d; color:#fff; line-height:1;';
        $this->totalProdutosBadge->add('Quantidade: 0');

        $button_cadastrar = new TButton('button_button_cadastrar');
        $button_cadastrar->setAction(new TAction(['ProdutoForm', 'onShow']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['ProdutoList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['ProdutoList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['ProdutoList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        // $button_orse = new TButton('button_orse');
        // $action = new TAction([$this, 'onConfirmaAtualizacao']);
        // $action->setParameter('target_container', 'adianti_right_panel'); // se estiver usando painéis

        // $button_orse->setAction(
        //     $action,
        //     '<img src="app/images/logoorse.png" width="32" height="32" style="margin-right:5px; vertical-align:middle;"> Orse'
        // );
        // $button_orse->addStyleClass('btn-default');
        // $this->datagrid_form->addField($button_orse);

        // $atuallizar = new TButton('button_atuallizar');
        // $atuallizar->setAction(new TAction(['ProdutoList', 'onAtualiza']), "Atualizar unidades no produto");
        // $atuallizar->addStyleClass('btn-default');
        // $atuallizar->setImage('far:circle #000000');

        // $this->datagrid_form->addField($atuallizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['ProdutoList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['ProdutoList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['ProdutoList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['ProdutoList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_cadastrar);
        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($button_atualizar);
    //    $head_left_actions->add($button_orse);

      //  $head_left_actions->add($atuallizar);

        $head_right_actions->add($this->totalProdutosBadge);
        $head_right_actions->add($dropdown_button_exportar);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
         //   $container->add(TBreadCrumb::create(["Produtos","Produtos"]));
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
                $object = new Produto($key, FALSE); 

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
                $objects = $this->onReload();
                $totalProdutos = is_array($objects) ? count($objects) : 0;

                $html = clone $this->datagrid;
                $summary = "<div style='font-family:Arial,sans-serif; font-size:12px; font-weight:bold; margin:0 0 10px 0;'>Quantidade de produtos: {$totalProdutos}</div>";
                $contents = file_get_contents('app/resources/styles-print.html') . file_get_contents('app/resources/styles-print-list.html') . PdfListHeader::render(__CLASS__) . $summary . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'landscape');
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
            $page->setProperty('page-name', 'ProdutoListSearch');
            $page->setProperty('page_name', 'ProdutoListSearch');
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
    public function onAtualizaTabelaOrse($param = null) 
    {
        try 
        {
            TTransaction::open(self::$database);

           $repository = new TRepository('TabelaOrse');
            $criteria = new TCriteria;
         //   $criteria->setProperties(['limit' => 5000, 'offset' => 0]); // <-- aqui
            $objects = $repository->load($criteria, FALSE);

            foreach ($objects as $orse) {
                if (empty($orse->codigo) || empty($orse->descricao) || empty($orse->valor)) {
                    continue; // ignora registros incompletos
                }
                // Buscar ou criar unidade de medida
                $unidadeDescricao = strtolower($orse->unidade);
                $unidades = UnidadeMedida::where('LOWER(nome)', '=', $unidadeDescricao)->load();

                if ($unidades) {
                    $idunidade = $unidades[0]->id;
                } else {
                    $novaUnidade = new UnidadeMedida();
                    $novaUnidade->descricao = $orse->unidade;
                    $novaUnidade->store();
                    $idunidade = $novaUnidade->id;
                }

                // Converter valor
                // $preco_limpo = str_replace(['R$', ' '], '', $orse->valor);
                // $preco_limpo = str_replace('.', '', $preco_limpo);
                // $preco_limpo = str_replace(',', '.', $preco_limpo);
                // $preco_float = floatval($preco_limpo);
                $preco_float = $this->converterParaFloat($orse->valor);


                // Buscar produto com o código ORSE
                $repository1 = new TRepository('Produto');
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('codigo_orse', '=', $orse->codigo));
                $produtos = $repository1->load($criteria1, FALSE);

                if ($produtos) {
                    // Atualiza se houve alteração
                    $produto = new Produto($produtos[0]->id);

                    if ($produto->nome !== $orse->descricao ||
                        $produto->unidade_medida_id != $idunidade ||
                        floatval($produto->preco_venda) != $preco_float)
                    {
                        $produto->nome = $orse->descricao;
                        $produto->unidade_medida_id = $idunidade;
                        $produto->familia_produto_id = 1;
                        $produto->fabricante_id = 1;
                        $produto->preco_venda = $preco_float;
                        $produto->store();
                    }

                } else {
                    // Criar novo produto
                    $produto = new Produto();
                    $produto->codigo_orse = $orse->codigo;
                    $produto->nome = $orse->descricao;
                    $produto->unidade_medida_id = $idunidade;
                    $produto->familia_produto_id = 1;
                    $produto->fabricante_id = 1;
                    $produto->preco_venda = $preco_float;
                    $produto->ativo = 'T';
                    $produto->system_unit_id = TSession::getValue('idunit');
                    $produto->system_users_id = TSession::getValue('iduser');
                    $produto->tipo_produto_id = 1;
                    $produto->store();
                }
            }

            TTransaction::close();
            new TMessage('info', 'Atualização concluída com sucesso!');
        } 
        catch (Exception $e) 
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());    
        }
    }
    function converterParaFloat($valor) {
        // Remove R$, espaços e normaliza
        $valor = str_replace(['R$', ' '], '', $valor);

        // Se o valor tiver vírgula como separador decimal (brasileiro)
        if (preg_match('/\d+(\.\d{3})*,\d{2}$/', $valor)) {
            $valor = str_replace('.', '', $valor); // remove separadores de milhar
            $valor = str_replace(',', '.', $valor); // troca vírgula decimal por ponto
        }
        // Se o valor tiver ponto como separador decimal (americano)
        elseif (preg_match('/\d{1,3}(,\d{3})*\.\d{2}$/', $valor)) {
            $valor = str_replace(',', '', $valor); // remove separadores de milhar
        }

        return floatval($valor);
    }

       public function onAtualiza($param = null) 
    {
        try 
        {
            //code here

           TTransaction::open(self::$database);
           $repository = new TRepository('Produto');
           $criteria = new TCriteria;
           $objects = $repository->load($criteria, FALSE);

           foreach ($objects as $prod) {
              
               $repository1 = new TRepository('SystemUnit');
               $criteria1 = new TCriteria;
               $objects1 = $repository1->load($criteria1, FALSE);
               foreach ($objects1 as $unidade) {
                   $produnit = new ProdutoSystemUnit();
                   $produnit->produto_id = $prod->id;
                   $produnit->system_unit_id = $unidade->id;
                   $produnit->store();
               }
           }
           TTransaction::close();
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

        if (isset($data->nome) AND ( (is_scalar($data->nome) AND $data->nome !== '') OR (is_array($data->nome) AND (!empty($data->nome)) )) )
        {

            $filters[] = new TFilter('nome', 'like', "%{$data->nome}%");// create the filter 
        }

        if (isset($data->cod_barras) AND ( (is_scalar($data->cod_barras) AND $data->cod_barras !== '') OR (is_array($data->cod_barras) AND (!empty($data->cod_barras)) )) )
        {

            $filters[] = new TFilter('cod_barras', 'like', "%{$data->cod_barras}%");// create the filter 
        }

        if (isset($data->tipo_produto_id) AND ( (is_scalar($data->tipo_produto_id) AND $data->tipo_produto_id !== '') OR (is_array($data->tipo_produto_id) AND (!empty($data->tipo_produto_id)) )) )
        {

            $filters[] = new TFilter('tipo_produto_id', '=', $data->tipo_produto_id);// create the filter 
        }

        if (isset($data->familia_produto_id) AND ( (is_scalar($data->familia_produto_id) AND $data->familia_produto_id !== '') OR (is_array($data->familia_produto_id) AND (!empty($data->familia_produto_id)) )) )
        {

            $filters[] = new TFilter('familia_produto_id', '=', $data->familia_produto_id);// create the filter 
        }

        if (isset($data->fabricante_id) AND ( (is_scalar($data->fabricante_id) AND $data->fabricante_id !== '') OR (is_array($data->fabricante_id) AND (!empty($data->fabricante_id)) )) )
        {

            $filters[] = new TFilter('fabricante_id', '=', $data->fabricante_id);// create the filter 
        }

        if (isset($data->ativo) AND ( (is_scalar($data->ativo) AND $data->ativo !== '') OR (is_array($data->ativo) AND (!empty($data->ativo)) )) )
        {

            $filters[] = new TFilter('ativo', '=', $data->ativo);// create the filter 
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

            // creates a repository for Produto
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'nome';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'asc';
            }

            $criteria->add( 
                new TFilter('system_unit_id', 'IN',
                    "(SELECT su.id FROM system_unit su 
                    LEFT JOIN entidade e ON e.id = su.entidade_id 
                    WHERE e.compras = 1)"
                )
            );

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

            if (!empty($this->totalProdutosBadge))
            {
                $this->totalProdutosBadge->clearChildren();
                $this->totalProdutosBadge->add("Quantidade: {$count}");
            }

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

        $object = new Produto($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    public static function onConfirmaAtualizacao($param = null)
    {
        new TQuestion('Tem certeza que deseja atualizar os dados da Tabela ORSE?', new TAction([__CLASS__, 'onAtualizaTabelaOrse']));
    }

}

