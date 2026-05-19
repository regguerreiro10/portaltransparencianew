<?php

//<fileHeader>

//</fileHeader>

class DispositivosSolicitadosList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $toolbar;
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'DispositivosSolicitados';
    private static $primaryKey = 'id';
    private static $formName = 'form_DispositivosSolicitadosList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;
    private static $estadosDisponiveisCache;

    //<classProperties>

    //</classProperties>

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        $podeGerenciarDispositivos = self::usuarioPodeGerenciarDispositivos();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        $basename   = urlencode('propostas-disponiveis-list.pdf');
        $download   = "download.php?file=app/manual/listagem-dispositivos-solicitados.pdf&basename={$basename}";

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
        $this->form->setFormTitle("Dispositivos solicitados {$manual}");
        $this->limit = 20;

        $criteria_dispositivos_id = new TCriteria();
        $criteria_veiculos_id = new TCriteria();
        $criteria_status_dispositivos_id = new TCriteria();
        $criteria_system_unit_id = new TCriteria();
        $criteria_pessoa = new TCriteria();
        $criteria_pessoa->add(new TFilter('id', 'in', "(SELECT pessoa_id 
                                               FROM pessoa_grupo 
                                               WHERE grupo_pessoa_id IN (".GrupoPessoa::CONDUTOR.", ".GrupoPessoa::USUARIODISPOSITIVO."))"));
        $criteria_pessoa->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));

        //<onBeginPageCreation>

        //</onBeginPageCreation>

        $id = new TEntry('id');
        $numerocartao = new TEntry('numerocartao');
        $datasolicitacao = new BDateRange('datasolicitacao', 'datasolicitacao_final');
        $dispositivos_id = new TDBCombo('dispositivos_id', 'minierp', 'Dispositivos', 'id', '{descricao}','descricao asc' , $criteria_dispositivos_id );
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa}','placa asc' , $criteria_veiculos_id );
        $status_dispositivos_id = new TDBCombo('status_dispositivos_id', 'minierp', 'StatusDispositivos', 'id', '{descricao}','descricao asc' , $criteria_status_dispositivos_id );
        $pessoa = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa );
        $datasolicitacao->setMask('dd/mm/yyyy');
        $datasolicitacao->setDatabaseMask('yyyy-mm-dd');
        $id->setProperty('placeholder', 'Codigo');
        $numerocartao->setProperty('placeholder', 'Numero do cartao ou UID');

        $veiculos_id->enableSearch();
        $dispositivos_id->enableSearch();
        $status_dispositivos_id->enableSearch();
        $pessoa->enableSearch();

        $id->setSize('100%');
        $pessoa->setSize('100%');
        $veiculos_id->setSize('100%');
        $numerocartao->setSize('100%');
        $datasolicitacao->setSize(220);
        $dispositivos_id->setSize('100%');
        $status_dispositivos_id->setSize('100%');

        //<onBeforeAddFieldsToForm>

        //</onBeforeAddFieldsToForm>
        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Número do cartão / UID Tag:", null, '14px', null, '100%'),$numerocartao]);
        $row1->layout = ['col-sm-3','col-sm-9'];

        $row2 = $this->form->addFields([new TLabel("Data Solicitação:", null, '14px', null, '100%'),$datasolicitacao],[new TLabel("Status:", null, '14px', null, '100%'),$status_dispositivos_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Dispositivos:", null, '14px', null, '100%'),$dispositivos_id],[new TLabel("Veiculos:", null, '14px', null, '100%'),$veiculos_id]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Condutor ou Usuário:", null, '14px', null, '100%'),$pessoa]);
        $row4->layout = ['col-sm-12'];


        $observacao = new TElement('div');
        $observacao->style = 'margin:10px 0 0 0;padding:12px;border:1px solid #e5e7eb;background:#f8fafc;color:#334155;border-radius:4px;';
        $observacao->add('Use os filtros para localizar rapidamente por periodo, status, dispositivo, veiculo ou usuario responsavel.');
        $this->form->addContent([$observacao]);

        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        $btn_onclearfilters = $this->form->addAction("Limpar filtros", new TAction([$this, 'onClearFilters']), 'fas:eraser #dd5a43');
        $this->btn_onclearfilters = $btn_onclearfilters;

        if ($podeGerenciarDispositivos)
        {
            $btn_onshow = $this->form->addAction("Cadastrar", new TAction(['DispositivosSolicitadosForm', 'onShow']), 'fas:plus #69aa46');
            $this->btn_onshow = $btn_onshow;
        }

        $this->toolbar = new TForm('toolbar_' . self::$formName);
        $this->toolbar->style = 'margin-bottom:10px;';

        $btnShowCurtainFilters = new TButton('btn_show_curtain_filters');
        $btnShowCurtainFilters->setAction(new TAction([__CLASS__, 'onShowCurtainFilters']), 'Filtros');
        $btnShowCurtainFilters->addStyleClass('btn btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');
        $this->toolbar->addField($btnShowCurtainFilters);

        $buttonLimparFiltros = new TButton('btn_limpar_filtros');
        $buttonLimparFiltros->setAction(new TAction([$this, 'onClearFilters']), 'Limpar filtros');
        $buttonLimparFiltros->addStyleClass('btn btn-default');
        $buttonLimparFiltros->setImage('fas:eraser #f44336');
        $this->toolbar->addField($buttonLimparFiltros);

        $buttonAtualizar = new TButton('btn_atualizar');
        $buttonAtualizar->setAction(new TAction([$this, 'onReload']), 'Atualizar');
        $buttonAtualizar->addStyleClass('btn btn-default');
        $buttonAtualizar->setImage('fas:sync-alt #03a9f4');
        $this->toolbar->addField($buttonAtualizar);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

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
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_numerocartao = new TDataGridColumn('numerocartao', "Número do cartão / UID Tag", 'left');
        $column_datasolicitacao = new TDataGridColumn('datasolicitacao', "Data Solicitação", 'left');
        $column_dispositivos_id = new TDataGridColumn('dispositivos->descricao', "Dispositivos", 'left');
        $column_veiculos_id = new TDataGridColumn('veiculos->placa', "Placa", 'left');
        $column_pessoa = new TDataGridColumn('pessoa->nome', "Condutor ou Usuário", 'left');
        $column_status_dispositivos_id = new TDataGridColumn('status_dispositivos->descricao', "Status", 'left');
       
        $column_via = new TDataGridColumn('via', "Via", 'left');
        $column_rastreio = new TDataGridColumn('rastreio', "Rastreio", 'left');
        $column_coringa = new TDataGridColumn('coringa', "Coringa", 'left');
        $column_system_unit_name = new TDataGridColumn('system_unit->name', "Unidade", 'left');
        $column_departamento_unit_name = new TDataGridColumn('departamento_unit->name', "Departamento", 'left');
        $column_saldo_atual = new TDataGridColumn('saldo_atual', "Saldo atual", 'right');
        $column_saldo_limite = new TDataGridColumn('saldo_limite', "Saldo limite", 'right');
        $column_system_users_name = new TDataGridColumn('system_users->name', "Usuário", 'left');
        $column_datasolicitacao->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_status_dispositivos_id->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (empty($object->status_dispositivos))
            {
                return '';
            }

            return "<span class='label label-default' style='min-width:150px; display:inline-block; background-color:{$object->status_dispositivos->cor}'>{$object->status_dispositivos->descricao}</span>";
        });
        $column_coringa->setTransformer(function($value)
        {
            if ($value === 'S')
            {
                return "<span class='label label-success' style='min-width:70px; display:inline-block;'>Sim</span>";
            }

            return "<span class='label label-default' style='min-width:70px; display:inline-block;'>Nao</span>";
        });
        $column_via->setTransformer(function($value)
        {
            return !empty($value) ? "<span class='label label-info' style='min-width:55px; display:inline-block;'>{$value}</span>" : '';
        });
        $column_saldo_atual->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            return 'R$ ' . number_format((float) $value, 2, ',', '.');
        });
        $column_saldo_limite->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            return 'R$ ' . number_format((float) $value, 2, ',', '.');
        });
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        //<onBeforeColumnsCreation>

        //</onBeforeColumnsCreation>

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_numerocartao);
        $this->datagrid->addColumn($column_datasolicitacao);
        $this->datagrid->addColumn($column_dispositivos_id);
        $this->datagrid->addColumn($column_pessoa);
        $this->datagrid->addColumn($column_veiculos_id);
        $this->datagrid->addColumn($column_via);
        $this->datagrid->addColumn($column_rastreio);
        $this->datagrid->addColumn($column_coringa);
        $this->datagrid->addColumn($column_system_unit_name);
        $this->datagrid->addColumn($column_departamento_unit_name);
        $this->datagrid->addColumn($column_system_users_name);
        $this->datagrid->addColumn($column_saldo_atual);
        $this->datagrid->addColumn($column_saldo_limite);
        $this->datagrid->addColumn($column_status_dispositivos_id);
            
        // creates two datagrid actions
        $action1 = new TDataGridAction(['DispositivosSolicitadosForm', 'onEdit'],     ['id' => '{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'],   ['id' => '{id}']);
        $action3 = new TDataGridAction(['MovimentoDispositivosList', 'onSetProject'],   ['id' => '{id}']);
        $action5 = new TDataGridAction(['GravarTagNfcForm', 'onShow'],   ['key' => '{id}']);
        $action6 = new TDataGridAction(['DispositivosSolicitadosExtratoList', 'onSetProject'],   ['id' => '{id}']);

        $action1->setLabel('Editar');
        $action1->setImage('far:edit #478fca');
        $action1->setDisplayCondition('DispositivosSolicitadosList::onExibirEditar');

        $action2->setLabel('Excluir');
        $action2->setImage('fas:trash-alt #dd5a43');
        $action2->setDisplayCondition('DispositivosSolicitadosList::onExibirExcluir');

        $action3->setLabel('Movimentações');
        $action3->setImage('fas:exchange-alt #000000');

        $action5->setLabel('Gravar TAG');
        $action5->setImage('fas:wifi #2e7d32');
        $action6->setLabel('Extrato mensal');
        $action6->setImage('fas:file-invoice-dollar #000000');
       // $action4->setDisplayCondition('DispositivosSolicitadosList::onExibirDocCotacao');
        //<onAfterColumnsCreation>

        //</onAfterColumnsCreation>
        $action_group = new TDataGridActionGroup('Clique Ações ', 'fa:th red');

        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action5);
        $action_group->addAction($action6);
     

        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);
        /*
        $action_onEdit = new TDataGridAction(array('DispositivosSolicitadosForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('DispositivosSolicitadosList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);*/

        //<onAfterActionsCreation>

        //</onAfterActionsCreation>

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
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['DispositivosSolicitadosList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['DispositivosSolicitadosList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['DispositivosSolicitadosList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['DispositivosSolicitadosList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($buttonLimparFiltros);
        $head_left_actions->add($buttonAtualizar);

        if ($podeGerenciarDispositivos)
        {
            $buttonCadastrar = new TButton('btn_cadastrar_toolbar');
            $buttonCadastrar->setAction(new TAction(['DispositivosSolicitadosForm', 'onShow']), 'Cadastrar');
            $buttonCadastrar->addStyleClass('btn btn-default');
            $buttonCadastrar->setImage('fas:plus #69aa46');
            $this->toolbar->addField($buttonCadastrar);
            $head_left_actions->add($buttonCadastrar);
        }

        $head_right_actions->add($dropdown_button_exportar);

        //<onAfterHeaderActionsCreation>

        //</onAfterHeaderActionsCreation>

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
      //      $container->add(TBreadCrumb::create(["Compras","Dispositivos solicitadoss"]));
        }
        $container->add($this->toolbar);
        $container->add($panel);
        //<onAfterPageCreation>

        //</onAfterPageCreation>

        parent::add($container);

    }

//<generated-DatagridAction-onDelete>
    public function onDelete($param = null) 
    { 
        self::validarPermissaoGerenciarDispositivos('excluir dispositivos solicitados');

        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'] ?? $param['id'] ?? null;
                // open a transaction with database
                TTransaction::open(self::$database);

                // instantiates object
                $object = new DispositivosSolicitados($key, FALSE); //</blockLine>

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
//</generated-DatagridAction-onDelete>
    public function onImportacao($param = null){
        new TMessage('info', "Botão desabilitado.");
    }

    public function onClearFilters($param = null)
    {
        TSession::setValue(__CLASS__.'_filter_data', null);
        TSession::setValue(__CLASS__.'_filters', null);
        $this->form->clear(true);
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }
//<generated-DatagridHeaderAction-onExportCsv>
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
//</generated-DatagridHeaderAction-onExportCsv>
//<generated-DatagridHeaderAction-onExportXls>
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
//</generated-DatagridHeaderAction-onExportXls>
//<generated-DatagridHeaderAction-onExportPdf>
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
//</generated-DatagridHeaderAction-onExportPdf>
//<generated-DatagridHeaderAction-onExportXml>
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
//</generated-DatagridHeaderAction-onExportXml>

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        //<onBeforeDatagridSearch>

        //</onBeforeDatagridSearch> 

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->numerocartao) AND ( (is_scalar($data->numerocartao) AND $data->numerocartao !== '') OR (is_array($data->numerocartao) AND (!empty($data->numerocartao)) )) )
        {

            $filters[] = new TFilter('numerocartao', '=', $data->numerocartao);// create the filter 
        }

        if (isset($data->datasolicitacao_final) AND ( (is_scalar($data->datasolicitacao_final) AND $data->datasolicitacao_final !== '') OR (is_array($data->datasolicitacao_final) AND (!empty($data->datasolicitacao_final)) )) )
        {

            $filters[] = new TFilter('datasolicitacao', '<=', $data->datasolicitacao_final);// create the filter 
        }

        if (isset($data->datasolicitacao) AND ( (is_scalar($data->datasolicitacao) AND $data->datasolicitacao !== '') OR (is_array($data->datasolicitacao) AND (!empty($data->datasolicitacao)) )) )
        {

            $filters[] = new TFilter('datasolicitacao', '>=', $data->datasolicitacao);// create the filter 
        }

        if (isset($data->dispositivos_id) AND ( (is_scalar($data->dispositivos_id) AND $data->dispositivos_id !== '') OR (is_array($data->dispositivos_id) AND (!empty($data->dispositivos_id)) )) )
        {

            $filters[] = new TFilter('dispositivos_id', '=', $data->dispositivos_id);// create the filter 
        }

        if (isset($data->veiculos_id) AND ( (is_scalar($data->veiculos_id) AND $data->veiculos_id !== '') OR (is_array($data->veiculos_id) AND (!empty($data->veiculos_id)) )) )
        {

            $filters[] = new TFilter('veiculos_id', '=', $data->veiculos_id);// create the filter 
        }

        if (isset($data->status_dispositivos_id) AND ( (is_scalar($data->status_dispositivos_id) AND $data->status_dispositivos_id !== '') OR (is_array($data->status_dispositivos_id) AND (!empty($data->status_dispositivos_id)) )) )
        {

            $filters[] = new TFilter('status_dispositivos_id', '=', $data->status_dispositivos_id);// create the filter 
        }

        if (isset($data->created_at) AND ( (is_scalar($data->created_at) AND $data->created_at !== '') OR (is_array($data->created_at) AND (!empty($data->created_at)) )) )
        {

            $filters[] = new TFilter('created_at', '=', $data->created_at);// create the filter 
        }

        if (isset($data->updated_at) AND ( (is_scalar($data->updated_at) AND $data->updated_at !== '') OR (is_array($data->updated_at) AND (!empty($data->updated_at)) )) )
        {

            $filters[] = new TFilter('updated_at', '=', $data->updated_at);// create the filter 
        }

        if (isset($data->deleted_at) AND ( (is_scalar($data->deleted_at) AND $data->deleted_at !== '') OR (is_array($data->deleted_at) AND (!empty($data->deleted_at)) )) )
        {

            $filters[] = new TFilter('deleted_at', '=', $data->deleted_at);// create the filter 
        }

        if (isset($data->via) AND ( (is_scalar($data->via) AND $data->via !== '') OR (is_array($data->via) AND (!empty($data->via)) )) )
        {

            $filters[] = new TFilter('via', '=', $data->via);// create the filter 
        }

        if (isset($data->rastreio) AND ( (is_scalar($data->rastreio) AND $data->rastreio !== '') OR (is_array($data->rastreio) AND (!empty($data->rastreio)) )) )
        {

            $filters[] = new TFilter('rastreio', 'like', "%{$data->rastreio}%");// create the filter 
        }

        if (isset($data->coringa) AND ( (is_scalar($data->coringa) AND $data->coringa !== '') OR (is_array($data->coringa) AND (!empty($data->coringa)) )) )
        {

            $filters[] = new TFilter('coringa', '=', $data->coringa);// create the filter 
        }

        if (isset($data->system_unit_id) AND ( (is_scalar($data->system_unit_id) AND $data->system_unit_id !== '') OR (is_array($data->system_unit_id) AND (!empty($data->system_unit_id)) )) )
        {

            $filters[] = new TFilter('system_unit_id', '=', $data->system_unit_id);// create the filter 
        }

        if (isset($data->departamento_unit_id) AND ( (is_scalar($data->departamento_unit_id) AND $data->departamento_unit_id !== '') OR (is_array($data->departamento_unit_id) AND (!empty($data->departamento_unit_id)) )) )
        {

            $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        }

        if (isset($data->system_users_id) AND ( (is_scalar($data->system_users_id) AND $data->system_users_id !== '') OR (is_array($data->system_users_id) AND (!empty($data->system_users_id)) )) )
        {

            $filters[] = new TFilter('system_users_id', '=', $data->system_users_id);// create the filter 
        }

        if (isset($data->saldo_atual) AND ( (is_scalar($data->saldo_atual) AND $data->saldo_atual !== '') OR (is_array($data->saldo_atual) AND (!empty($data->saldo_atual)) )) )
        {

            $filters[] = new TFilter('saldo_atual', '=', str_replace(['.', ','], ['', '.'], $data->saldo_atual));// create the filter 
        }

        if (isset($data->saldo_limite) AND ( (is_scalar($data->saldo_limite) AND $data->saldo_limite !== '') OR (is_array($data->saldo_limite) AND (!empty($data->saldo_limite)) )) )
        {

            $filters[] = new TFilter('saldo_limite', '=', str_replace(['.', ','], ['', '.'], $data->saldo_limite));// create the filter 
        }

        $this->fireEvents($data);

        //<onDatagridSearch>

        //</onDatagridSearch>

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        TScript::create('Template.closeRightPanel();');

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

            // creates a repository for DispositivosSolicitados
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

            //<onBeforeDatagridLoad>

            //</onBeforeDatagridLoad>

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    //<onBeforeDatagridAddItem>

                    //</onBeforeDatagridAddItem>
                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";
                    //<onAfterDatagridAddItem>

                    //</onAfterDatagridAddItem>
                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

            //<onBeforeDatagridTransactionClose>

            //</onBeforeDatagridTransactionClose>

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
        //<onShow>

        //</onShow>
    }

    public static function onShowCurtainFilters($param = null)
    {
        try
        {
            $filter = new self([]);

            $btnClose = new TButton('closeCurtain');
            $btnClose->class = 'btn btn-sm btn-default';
            $btnClose->style = 'margin-right:10px;';
            $btnClose->onClick = "Template.closeRightPanel();";
            $btnClose->setLabel('Fechar');
            $btnClose->setImage('fas:times');
            $filter->form->addHeaderWidget($btnClose);

            $page = new TPage();
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('page-name', 'DispositivosSolicitadosListSearch');
            $page->setProperty('page_name', 'DispositivosSolicitadosListSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
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

    //</hideLine> <addUserFunctionsCode/>

    public static function manageRow($id)
    {
        $list = new self([]);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new DispositivosSolicitados($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

   
    public function fireEvents( $object )
    {
        $obj = new stdClass;
        if(is_object($object) && get_class($object) == 'stdClass')
        {
            if(isset($object->system_unit_id))
            {
                $value = $object->system_unit_id;

                $obj->system_unit_id = $value;
            }
            if(isset($object->departamento_unit_id))
            {
                $value = $object->departamento_unit_id;

                $obj->departamento_unit_id = $value;
            }
        }
        elseif(is_object($object))
        {
            if(isset($object->system_unit_id))
            {
                $value = $object->system_unit_id;

                $obj->system_unit_id = $value;
            }
            if(isset($object->departamento_unit_id))
            {
                $value = $object->departamento_unit_id;

                $obj->departamento_unit_id = $value;
            }
        }
        TForm::sendData(self::$formName, $obj);
    }

    public static function onExibirEditar($object = null)
    {
        return self::usuarioPodeGerenciarDispositivos();
    }

    public static function onExibirExcluir($object = null)
    {
        return self::usuarioPodeGerenciarDispositivos();
    }

    private static function usuarioPodeGerenciarDispositivos()
    {
        try
        {
            $openTransaction = TTransaction::getDatabase() != self::$database;

            if ($openTransaction)
            {
                TTransaction::open(self::$database);
            }

            $user = new SystemUsers((int) TSession::getValue('userid'));
            $permitido = ($user->login ?? null) === 'admin'
                || in_array(EstadoPedidoFrotas::APROVACAODISPOSITIVO, self::getEstadosDisponiveisCache());

            if ($openTransaction)
            {
                TTransaction::close();
            }

            return $permitido;
        }
        catch (Exception $e)
        {
            if (TTransaction::getDatabase() == self::$database)
            {
                TTransaction::rollback();
            }

            return false;
        }
    }

    private static function getEstadosDisponiveisCache()
    {
        if (self::$estadosDisponiveisCache === null)
        {
            self::$estadosDisponiveisCache = AprovadorFrotas::getEstadosDisponiveis();
        }

        return self::$estadosDisponiveisCache;
    }

    private static function validarPermissaoGerenciarDispositivos($acao)
    {
        if (!self::usuarioPodeGerenciarDispositivos())
        {
            throw new Exception("Somente aprovadores de dispositivos podem {$acao}.");
        }
    }

}
