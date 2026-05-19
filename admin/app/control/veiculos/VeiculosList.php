<?php

class VeiculosList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Veiculos';
    private static $primaryKey = 'id';
    private static $formName = 'form_VeiculosList';
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
        $basename   = urlencode('veiculos-list.pdf');
        $download   = "download.php?file=app/manual/veiculos-list.pdf&basename={$basename}";

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
        $this->form->setFormTitle("Listagem de veículos, aeronaves e/ou equipamentos {$manual}");
        $this->limit = 20;

        $criteria_system_unit_id = new TCriteria();
        $criteria_tipo_veiculo_id = new TCriteria();
        $criteria_marca_id = new TCriteria();
        $criteria_modelo_id = new TCriteria();
        $criteria_corveiculo_id = new TCriteria();
        $criteria_status_veiculo_id = new TCriteria();
        $criteria_dispositivos_id = new TCriteria();

       // var_dump(TSession::getValue('idunit'));
        $criteria_system_unit_id->add(new TFilter('id', '=', TSession::getValue('idunit'))); 
        // $criteria_system_unit_id->add(new TFilter('departamento_unit_id', '=', $filterVar));

        $id = new TEntry('id');
        $status_veiculo_id = new TDBCombo('status_veiculo_id', 'minierp', 'StatusVeiculo', 'id', '{nome}','nome asc' , $criteria_status_veiculo_id );
        // ==== Badge "Idade média" ====
        // garante alinhamento vertical entre badge e botões
       // $head_right_actions->style = 'display:flex; align-items:center; gap:8px;';

        // calcula idade média da unidade logada
        $idadeMedia = self::calcularIdadeMediaVeiculos((int) TSession::getValue('idunit'));
        $textoIdade = $idadeMedia !== null ? self::formatarAnosParaAnosMeses((float)$idadeMedia) : '—';

        // monta o badge com padding e cor neutra
        $badgeIdade = new TElement('span');
        $badgeIdade->class = 'label label-default';
        $badgeIdade->style = 'margin-right:12px; padding:4px 10px; font-size:12px; display:inline-flex; align-items:center; justify-content:center; border-radius:12px; background-color:#6c757d; color:#fff; line-height:1;';
        $badgeIdade->add('Idade média: ' . $textoIdade);

        // adiciona o badge antes do menu Exportar 
       // $head_right_actions->add($badgeIdade);


        $system_unit_id = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_system_unit_id );
        $departamento_unit_id = new TCombo('departamento_unit_id');
        $tipo_veiculo_id = new TDBCombo('tipo_veiculo_id', 'minierp', 'TipoVeiculo', 'descricao', '{descricao}','id asc' , $criteria_tipo_veiculo_id );
        $prefixo = new TEntry('prefixo');
        $placa = new TEntry('placa');
        $chassi = new TEntry('chassi');
        $renavam = new TEntry('renavam');
        $marca_id = new TDBUniqueSearch('marca_id', 'minierp', 'Marca', 'id', 'descricao','descricao asc' , $criteria_marca_id );
        $modelo_id = new TDBUniqueSearch('modelo_id', 'minierp', 'Modelo', 'id', 'descricao','descricao asc' , $criteria_modelo_id );
        $corveiculo_id = new TDBCombo('corveiculo_id', 'minierp', 'Corveiculo', 'id', '{descricao}','descricao asc' , $criteria_corveiculo_id );
        $dispositivos_id = new TDBCombo('dispositivos_id', 'minierp', 'Dispositivos', 'id', '{descricao}','descricao asc' , $criteria_dispositivos_id );
        $valor_tabela_fipe = new TEntry('valor_tabela_fipe');

        $system_unit_id->setChangeAction(new TAction([$this,'onChangesystem_unit_id']));

      //  $status->addItems(["ativo"=>"Ativo","inativo"=>"Inativo","devolvido"=>"Devolvido","cedido"=>"Cedido"]);
        $placa->setMaxLength(140);
        $chassi->setMaxLength(100);
        $prefixo->setMaxLength(100);
        $renavam->setMaxLength(100);

        $status_veiculo_id->enableSearch();
        $marca_id->setMinLength(2);
        $modelo_id->setMinLength(2);
        $corveiculo_id->enableSearch();
        $system_unit_id->enableSearch();
        $tipo_veiculo_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $dispositivos_id->enableSearch();

        $id->setSize(100);
        $placa->setSize('40%');
        $status_veiculo_id->setSize('100%');
        $prefixo->setSize('38%');
        $chassi->setSize('100%');
        $renavam->setSize('100%');
        $marca_id->setSize('99%');
        $modelo_id->setSize('100%');
        $corveiculo_id->setSize('100%');
        $system_unit_id->setSize('100%');
        $tipo_veiculo_id->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $dispositivos_id->setSize('100%');
        $valor_tabela_fipe->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', '', '100%'),$id],[new TLabel("Status:", null, '14px', '', '100%'),$status_veiculo_id]);
        $row1->layout = [' col-sm-6',' col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Unidade:", null, '14px', '', '100%'),$system_unit_id],[new TLabel("Sub Unidade:", null, '14px', '', '100%'),$departamento_unit_id]);
        $row2->layout = [' col-sm-6',' col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Cor", null, '14px', '', '100%'),$corveiculo_id],[new TLabel("Tipo", null, '14px', '', '100%'),$tipo_veiculo_id]);
        $row3->layout = [' col-sm-6',' col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Prefixo:", null, '14px', '', '100%'),$prefixo],[new TLabel("Placa:", null, '14px', '', '100%'),$placa]);
        $row4->layout = [' col-sm-6',' col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Chassi:", null, '14px', '', '100%'),$chassi],[new TLabel("Renavam:", null, '14px', '', '100%'),$renavam]);
        $row5->layout = [' col-sm-6',' col-sm-6'];

        $row6 = $this->form->addFields([new TLabel("Marca", null, '14px', '', '100%'),$marca_id],[new TLabel("Modelo", null, '14px', '', '100%'),$modelo_id]);
        $row6->layout = [' col-sm-6',' col-sm-6'];

        $row7 = $this->form->addFields([new TLabel("Dispositivos", null, '14px', '', '100%'),$dispositivos_id],[new TLabel("Valor tabela fipe", null, '14px', '', '100%'),$valor_tabela_fipe]);
        $row7->layout = [' col-sm-6',' col-sm-6'];


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        $this->fireEvents( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

    //    $btn_onshow = $this->form->addAction("Cadastrar", new TAction(['VeiculosForm', 'onShow']), 'fas:plus #69aa46');
   //     $this->btn_onshow = $btn_onshow;

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        // $filterVar = TSession::getValue('userunitid');
        // $this->filter_criteria->add(new TFilter('system_unit_id', '=', $filterVar));
        // $this->filter_criteria->add(new TFilter('departamento_unit_id', '=', $filterVar));

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_anof = new TDataGridColumn('anof', "AnoF", 'left');
        $column_placa = new TDataGridColumn('placa', "Placa", 'left');
        $column_marca_descricao = new TDataGridColumn('marca->descricao', "Marca", 'left');
        $column_modelo_descricao = new TDataGridColumn('modelo->descricao', "Modelo", 'left');
        $column_chassi = new TDataGridColumn('chassi', "Chassi", 'left');
        $column_renavam = new TDataGridColumn('renavam', "Renavam", 'left');
        $column_tipo_combustivel_descricao = new TDataGridColumn('tipo_combustivel->descricao', "Tipo combustivel", 'left');
        if (TSession::getValue('tipofrota')==2) {
            $column_hodometroatual = new TDataGridColumn('hodometroatual', "Horimetro", 'left');
        } else {
            $column_hodometroatual = new TDataGridColumn('hodometroatual', "Km", 'left');
        }
        $column_ciclos = new TDataGridColumn('ciclos', "Ciclos", 'left');
        $column_corveiculo_descricao = new TDataGridColumn('corveiculo->descricao', "Cor", 'left');
        $column_tipo_veiculo_descricao = new TDataGridColumn('tipo_veiculo->descricao', "Tipo", 'left');
        $column_valor_transformed = new TDataGridColumn('valor_tabela_fipe', "Valor Fipe", 'left');
        $column_status_transformed = new TDataGridColumn('status_veiculo->nome', "Status", 'left');

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

        $column_status_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
            return "<span class='label label-default' style='width:240px; background-color:{$object->status_veiculo->cor}'> {$object->status_veiculo->nome} <span>";

        

        });        

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_placa);
        $this->datagrid->addColumn($column_anof);
        $this->datagrid->addColumn($column_marca_descricao);
        $this->datagrid->addColumn($column_modelo_descricao);
        $this->datagrid->addColumn($column_chassi);
        $this->datagrid->addColumn($column_renavam);
        $this->datagrid->addColumn($column_tipo_combustivel_descricao);
        $this->datagrid->addColumn($column_hodometroatual);
        if (TSession::getValue('tipofrota')==2) {
            $this->datagrid->addColumn($column_ciclos);
        }
        $this->datagrid->addColumn($column_corveiculo_descricao);
        $this->datagrid->addColumn($column_tipo_veiculo_descricao);
        $this->datagrid->addColumn($column_valor_transformed);
        $this->datagrid->addColumn($column_status_transformed);

         // creates two datagrid actions
         $action1 = new TDataGridAction(['VeiculosFormView', 'onShow'],     ['id' => '{id}']);
         $action2 = new TDataGridAction(['VeiculosForm', 'onEdit'],   ['id' => '{id}']);
         $action3 = new TDataGridAction([$this, 'onDelete'],   ['id' => '{id}']);
         $action4 = new TDataGridAction(['ManutencaoGarantiaList', 'onSetProject'],   ['id' => '{id}']);

         $action1->setLabel('Visualizar');
         $action1->setImage('fas:search-plus #673AB7');
    //     $action1->setDisplayCondition('VeiculosList::onExibirView');
 
         $action2->setLabel('Editar');
         $action2->setImage('far:edit #478fca');
    //     $action2->setDisplayCondition('VeiculosList::onExibirEditar');
 
         $action3->setLabel('Excluir');
         $action3->setImage('fas:trash-alt #dd5a43');

         $action4->setLabel('Garantia');
         $action4->setImage('fas:tools #2196F3');
    //     $action3->setDisplayCondition('VeiculosList::onExibirExcluir');
  
 
       
 
        $action_group = new TDataGridActionGroup('Clique Ações ', 'fa:th red');

        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action3);
        $action_group->addAction($action4);

       
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);

        // create the datagrid model
        $this->applyDatagridProperties();

        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem Veiculos {$manual}");
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
        $button_cadastrar->setAction(new TAction(['VeiculosForm', 'onShow']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['VeiculosList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['VeiculosList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['VeiculosList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['VeiculosList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['VeiculosList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['VeiculosList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['VeiculosList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_cadastrar);
        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_atualizar);
        $head_left_actions->add($button_limpar_filtros);
$head_right_actions->add($badgeIdade);

        $head_right_actions->add($dropdown_button_exportar);
        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
        //    $container->add(TBreadCrumb::create(["Veiculos","Veiculos"]));
        }
    //    $container->add($this->form);

        $container->add($panel);

        parent::add($container);


    }
   
   
    public static function onChangesystem_unit_id($param)
    {
        try
        {

            if (isset($param['system_unit_id']) && $param['system_unit_id'])
            { 
                $criteria = TCriteria::create(['system_unit_id' => $param['system_unit_id']]);
                TDBCombo::reloadFromModel(self::$formName, 'departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}', 'name asc', $criteria, TRUE); 
                
                return;
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

    public static function onChangemarca_id($param)
    {
        try
        {

            if (isset($param['marca_id']) && $param['marca_id'])
            { 
                $criteria = TCriteria::create(['marca_id' => $param['marca_id']]);
                TDBCombo::reloadFromModel(self::$formName, 'modelo_id', 'minierp', 'Modelo', 'id', '{descricao}', 'descricao asc', $criteria, TRUE); 
            } 
            else 
            { 
                TDBCombo::reloadFromModel(self::$formName, 'modelo_id', 'minierp', 'Modelo', 'id', '{descricao}', 'descricao asc', new TCriteria, TRUE); 
            }  

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
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

                
                $veiculos_id = $param['id'];
                $tables = [
                    'FotosVeiculos' => ['column' => 'veiculos_id', 'alias' => 'Foto veiculo'],
                    'AnexosVeiculo' => ['column' => 'veiculos_id', 'alias' => 'Anexos/documentos'],
                    'SaldoVeiculo' => ['column' => 'veiculos_id', 'alias' => 'Saldo veiculo'],
                    'PedidoFrotas' => ['column' => 'veiculos_id', 'alias' => 'Pedido Frota'],
                    'Propostas' => ['column' => 'veiculos_id', 'alias' => 'Propostas'],
                    'DispositivosSolicitados' => ['column' => 'veiculos_id', 'alias' => 'Dispositivos Solicitados'],
                    'ManutencaoGarantia' => ['column' => 'veiculos_id', 'alias' => 'Manutenção Garantia']
                ];
                foreach ($tables as $table => $info) {
                    $repository = new TRepository($table);
                    $criteria = new TCriteria();
                    $criteria->add(new TFilter($info['column'], '=', $veiculos_id)); // Corrigido aqui
                    
                    if ($repository->count($criteria) > 0) {
                        throw new Exception("Não é possível excluir este veículo, aeronave e/ou equipamento, porque existem registros associados em {$info['alias']}.");
                    }
                } 

                       
                // instantiates object
                $object = new Veiculos($key, FALSE); 

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
            if(isset($object->marca_id))
            {
                $value = $object->marca_id;

                $obj->marca_id = $value;
            }
            if(isset($object->modelo_id))
            {
                $value = $object->modelo_id;

                $obj->modelo_id = $value;
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
            if(isset($object->marca_id))
            {
                $value = $object->marca_id;

                $obj->marca_id = $value;
            }
            if(isset($object->modelo_id))
            {
                $value = $object->modelo_id;

                $obj->modelo_id = $value;
            }
        }
        TForm::sendData(self::$formName, $obj);
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

        if (isset($data->status) AND ( (is_scalar($data->status) AND $data->status !== '') OR (is_array($data->status) AND (!empty($data->status)) )) )
        {

            $filters[] = new TFilter('status', '=', $data->status);// create the filter 
        }

        if (isset($data->system_unit_id) AND ( (is_scalar($data->system_unit_id) AND $data->system_unit_id !== '') OR (is_array($data->system_unit_id) AND (!empty($data->system_unit_id)) )) )
        {

            $filters[] = new TFilter('system_unit_id', '=', $data->system_unit_id);// create the filter 
        }

        if (isset($data->departamento_unit_id) AND ( (is_scalar($data->departamento_unit_id) AND $data->departamento_unit_id !== '') OR (is_array($data->departamento_unit_id) AND (!empty($data->departamento_unit_id)) )) )
        {

            $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        }

        if (isset($data->status_veiculo_id) AND ( (is_scalar($data->status_veiculo_id) AND $data->status_veiculo_id !== '') OR (is_array($data->status_veiculo_id) AND (!empty($data->status_veiculo_id)) )) )
        {

            $filters[] = new TFilter('status_veiculo_id', '=', $data->status_veiculo_id);// create the filter 
        }
        if (isset($data->dispositivos_id) AND ( (is_scalar($data->dispositivos_id) AND $data->dispositivos_id !== '') OR (is_array($data->dispositivos_id) AND (!empty($data->dispositivos_id)) )) )
        {

            $filters[] = new TFilter('dispositivos_id', '=', $data->dispositivos_id);// create the filter 
        }

        if (isset($data->tipo_veiculo_id) AND ( (is_scalar($data->tipo_veiculo_id) AND $data->tipo_veiculo_id !== '') OR (is_array($data->tipo_veiculo_id) AND (!empty($data->tipo_veiculo_id)) )) )
        {

            $filters[] = new TFilter('tipo_veiculo_id', '=', $data->tipo_veiculo_id);// create the filter 
        }

        if (isset($data->prefixo) AND ( (is_scalar($data->prefixo) AND $data->prefixo !== '') OR (is_array($data->prefixo) AND (!empty($data->prefixo)) )) )
        {

            $filters[] = new TFilter('prefixo', 'like', "%{$data->prefixo}%");// create the filter 
        }

        if (isset($data->placa) AND ( (is_scalar($data->placa) AND $data->placa !== '') OR (is_array($data->placa) AND (!empty($data->placa)) )) )
        {

            $filters[] = new TFilter('placa', 'like', "%{$data->placa}%");// create the filter 
        }

        if (isset($data->chassi) AND ( (is_scalar($data->chassi) AND $data->chassi !== '') OR (is_array($data->chassi) AND (!empty($data->chassi)) )) )
        {

            $filters[] = new TFilter('chassi', 'like', "%{$data->chassi}%");// create the filter 
        }

        if (isset($data->renavam) AND ( (is_scalar($data->renavam) AND $data->renavam !== '') OR (is_array($data->renavam) AND (!empty($data->renavam)) )) )
        {

            $filters[] = new TFilter('renavam', 'like', "%{$data->renavam}%");// create the filter 
        }

        if (isset($data->marca_id) AND ( (is_scalar($data->marca_id) AND $data->marca_id !== '') OR (is_array($data->marca_id) AND (!empty($data->marca_id)) )) )
        {

            $filters[] = new TFilter('marca_id', '=', $data->marca_id);// create the filter 
        }

        if (isset($data->modelo_id) AND ( (is_scalar($data->modelo_id) AND $data->modelo_id !== '') OR (is_array($data->modelo_id) AND (!empty($data->modelo_id)) )) )
        {

            $filters[] = new TFilter('modelo_id', '=', $data->modelo_id);// create the filter 
        }

        if (isset($data->tipo_combustivel_id) AND ( (is_scalar($data->tipo_combustivel_id) AND $data->tipo_combustivel_id !== '') OR (is_array($data->tipo_combustivel_id) AND (!empty($data->tipo_combustivel_id)) )) )
        {

            $filters[] = new TFilter('tipo_combustivel_id', '=', $data->tipo_combustivel_id);// create the filter 
        }

        if (isset($data->corveiculo_id) AND ( (is_scalar($data->corveiculo_id) AND $data->corveiculo_id !== '') OR (is_array($data->corveiculo_id) AND (!empty($data->corveiculo_id)) )) )
        {

            $filters[] = new TFilter('corveiculo_id', '=', $data->corveiculo_id);// create the filter 
        }

        $this->fireEvents($data);

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

            // creates a repository for Veiculos
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

            //</blockLine><btnShowCurtainFiltersAutoCode>
            if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }
   //</            
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
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

        $object = new Veiculos($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
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
            $page->setProperty('page-name', 'VeiculosListSearch');
            $page->setProperty('page_name', 'VeiculosListSearch');
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
    // Calcula a idade média (em anos, com decimais) da unidade informada ou da unidade logada
    private static function calcularIdadeMediaVeiculos(?int $systemUnitId = null, ?int $anoAtual = null): ?float
    {
        $anoAtual = $anoAtual ?? (int) date('Y');
        $unit = $systemUnitId ?? (int) TSession::getValue('idunit');
        $cacheKey = __CLASS__ . '_idade_media_' . $unit . '_' . $anoAtual;

        if (TSession::getValue($cacheKey) !== null)
        {
            return TSession::getValue($cacheKey);
        }

        try {
            TTransaction::open(self::$database);
            $pdo = TTransaction::get();

            $sql = "
                SELECT AVG(
                    CASE
                        WHEN CAST(v.anof AS UNSIGNED) BETWEEN 1900 AND :ano_atual
                            AND CAST(v.anof AS UNSIGNED) <= :ano_atual
                        THEN :ano_atual - CAST(v.anof AS UNSIGNED)
                        ELSE NULL
                    END
                ) AS media
                FROM veiculos v
                WHERE v.system_unit_id = :unit
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':ano_atual' => $anoAtual,
                ':unit'      => $unit,
            ]);

            $media = $stmt->fetchColumn();
            TTransaction::close();

            $mediaFormatada = $media !== null ? (float) round((float) $media, 2) : null;
            TSession::setValue($cacheKey, $mediaFormatada);

            return $mediaFormatada;
        } catch (Exception $e) {
            TTransaction::rollback();
            throw $e;
        }
    }

    // Converte anos decimais em "X anos e Y meses"
    private static function formatarAnosParaAnosMeses(float $anos): string
    {
        $totalMeses = (int) round($anos * 12); // arredonda para o mês mais próximo
        $anosInt    = intdiv($totalMeses, 12);
        $meses      = $totalMeses % 12;

        $sAnos  = $anosInt === 1 ? 'ano'  : 'anos';
        $sMeses = $meses   === 1 ? 'mês'  : 'meses';

        return $meses > 0 ? "{$anosInt} {$sAnos} e {$meses} {$sMeses}" : "{$anosInt} {$sAnos}";
    }

}

