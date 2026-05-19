<?php

class SaldoDepartamentoList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'SaldoDepartamento';
    private static $primaryKey = 'id';
    private static $formName = 'formList_SaldoDepartamento';
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

        $criteria_departamento_unit_id = new TCriteria();
        $criteria = new TCriteria();

        $id = new TEntry('id');
        $created_at = new TEntry('created_at');
        $updated_at = new TEntry('updated_at');
        $deleted_at = new TEntry('deleted_at');
        // $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_departamento_unit_id );
        $departamento_unit_name = new TEntry('departamento_unit_name');
        $tipotransacao = new TCombo('tipotransacao');
        // $tipo = new TCombo('tipo');
        $datatransacao = new TDate('datatransacao');
        $historico = new TEntry('historico');
        $saldo_produto = new TEntry('saldo_produto');
        $saldo_servico = new TEntry('saldo_servico');
        $status_saldo_departamento_id = new TDBCombo(
            'status_saldo_departamento_id',
            'minierp',
            'StatusSaldoDepartamento',
            'id',
            '{descricao}',
            'id asc'
        );
        $saldo_total = new TEntry('saldo_total');
        $numero_documento_empenho = new TEntry('numero_documento_empenho'); 

        $id->exitOnEnter();
        $created_at->exitOnEnter();
        $updated_at->exitOnEnter();
        $deleted_at->exitOnEnter();
        $departamento_unit_name->exitOnEnter();
        // $tipo->exitOnEnter();
        // $tipotransacao->exitOnEnter();
        // $datatransacao->exitOnEnter();
        $historico->exitOnEnter();
        $saldo_produto->exitOnEnter();
        $saldo_servico->exitOnEnter();
        $saldo_total->exitOnEnter();
        $numero_documento_empenho->exitOnEnter();

        $id->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $created_at->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $updated_at->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $deleted_at->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        // $tipo->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        // $tipotransacao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $datatransacao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $historico->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $saldo_produto->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $saldo_servico->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $saldo_total->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $numero_documento_empenho->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        // $departamento_unit_name->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $tipotransacao->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $status_saldo_departamento_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        // $departamento_unit_name->enableSearch();

        $datatransacao->setMask('dd/mm/yyyy');
        $datatransacao->setDatabaseMask('yyyy-mm-dd');
        $tipotransacao->addItems(["C" => "Crédito", "D" => "Débito"]);
        $status_saldo_departamento_id->enableSearch();
        // $tipo->addItems(["1" => "Serviço", "2" => "Produto"]);

        $departamento_unit_name->setEditable(false);

        $id->setSize('100%');
        // $tipo->setSize('100%');
        $historico->setSize('100%');
        $created_at->setSize('100%');
        $updated_at->setSize('100%');
        $deleted_at->setSize('100%');
        $tipotransacao->setSize('100%');
        $datatransacao->setSize('100%');
        $saldo_produto->setSize('100%');
        $saldo_servico->setSize('100%');
        $status_saldo_departamento_id->setSize('100%');
        $saldo_total->setSize('100%');
        $departamento_unit_name->setSize('100%');
        $numero_documento_empenho->setSize('100%');

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
        $column_departamento_unit_name = new TDataGridColumn('departamento_unit->name', "Departamento", 'left');
        // $column_tipo = new TDataGridColumn('tipo', "Tipo", 'Center');
        $column_datatransacao = new TDataGridColumn('datatransacao', "Data Transação", 'Center');
        $column_tipotransacao = new TDataGridColumn('tipotransacao', "Transação", 'Center');
        $column_n_documento_empenho = new TDataGridColumn('numero_documento_empenho', "N° Doc Empenho", 'center');
        $column_saldo_produto = new TDataGridColumn('saldo_produto', "Saldo Empenho Produto", 'right');
        $column_saldo_servico = new TDataGridColumn('saldo_servico', "Saldo Empenho Serviço", 'right');
        $column_status_saldo_departamento = new TDataGridColumn('status_saldo_departamento', "Status Saldo Empenho", 'center');
        $column_status_saldo_departamento->disableHtmlConversion();
        $column_saldo_total = new TDataGridColumn('saldo_total', "Saldo Total", 'right');

        $column_datatransacao->setTransformer(function($value)
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

        $column_tipotransacao->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
            $value = '';
            if($object->tipotransacao == 'C')
            {
                $value = "<span style='background-color:rgba(92, 211, 23, 0.86); color: white; padding: 2px 6px; border-radius: 8px; font-weight: bold;'> Crédito </span>";
            } elseif($object->tipotransacao == 'D')
            {  
                $value = "<span style='background-color:rgba(168, 14, 14, 0.88); color: white; padding: 2px 9px; border-radius: 8px; font-weight: bold;'> Débito </span>";
            }

            return $value;

        });

        $column_saldo_produto->setTransformer(function($value)
        {
            return 'R$ ' . number_format((float) $value, 2, ',', '.');
        });

        $column_saldo_servico->setTransformer(function($value)
        {
            return 'R$ ' . number_format((float) $value, 2, ',', '.');
        });

        // $column_status_saldo_departamento->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        // {
        //     //code here
        //     $value = '';
        //     if($object->status_saldo_departamento == '1')
        //     {
        //         $value = "<span style='background-color:rgba(75, 173, 19, 0.86); color: white; padding: 2px 6px; border-radius: 8px; font-weight: bold;'> Aguardando Início </span>";
        //     } elseif($object->status_saldo_departamento == '2')
        //     {  
        //         $value = "<span style='background-color:rgba(255, 242, 183, 0.88); color: white; padding: 2px 9px; border-radius: 8px; font-weight: bold;'> Em Andamento </span>";
        //     } elseif($object->status_saldo_departamento == '3')
        //     {  
        //         $value = "<span style='background-color:rgba(203, 17, 17, 0.88); color: white; padding: 2px 9px; border-radius: 8px; font-weight: bold;'> Encerrado </span>";
        //     } elseif($object->status_saldo_departamento == '4')
        //     {  
        //         $value = "<span style='background-color:rgba(47, 7, 7, 0.88); color: white; padding: 2px 9px; border-radius: 8px; font-weight: bold;'> Anulado </span>";
        //     }

        //     return $value;

        // });

        $column_saldo_total->setTransformer(function($value)
        {
            return 'R$ ' . number_format((float) $value, 2, ',', '.');
        });

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_departamento_unit_name);
        // $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_datatransacao);
        $this->datagrid->addColumn($column_tipotransacao);
        $this->datagrid->addColumn($column_saldo_produto);
        $this->datagrid->addColumn($column_saldo_servico);
        $this->datagrid->addColumn($column_saldo_total);
        $this->datagrid->addColumn($column_n_documento_empenho);
        $this->datagrid->addColumn($column_status_saldo_departamento);


        $action_onEdit = new TDataGridAction(array($this, 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('SaldoDepartamentoList', 'onDelete'));
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
        $td_departamento_unit_name = TElement::tag('td', $departamento_unit_name);
        $tr->add($td_departamento_unit_name);
        $td_datatransacao = TElement::tag('td', $datatransacao);
        $tr->add($td_datatransacao);
        $td_tipotransacao = TElement::tag('td', $tipotransacao);
        $tr->add($td_tipotransacao);
        $td_saldo_produto = TElement::tag('td', $saldo_produto);
        $tr->add($td_saldo_produto);
        $td_saldo_servico = TElement::tag('td', $saldo_servico);
        $tr->add($td_saldo_servico);
        $td_saldo_total = TElement::tag('td', $saldo_total);
        $tr->add($td_saldo_total);
        $td_numero_documento_empenho = TElement::tag('td', $numero_documento_empenho);
        $tr->add($td_numero_documento_empenho);
        $td_status_saldo_departamento = TElement::tag('td', $status_saldo_departamento_id);
        $tr->add($td_status_saldo_departamento);

        $this->datagrid_form->addField($id);
        $this->datagrid_form->addField($departamento_unit_name);
        // $this->datagrid_form->addField($tipo);
        $this->datagrid_form->addField($datatransacao);
        $this->datagrid_form->addField($tipotransacao);
        $this->datagrid_form->addField($saldo_produto);
        $this->datagrid_form->addField($saldo_servico);
        $this->datagrid_form->addField($saldo_total);
        $this->datagrid_form->addField($numero_documento_empenho);
        $this->datagrid_form->addField($status_saldo_departamento_id);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de Saldo Departamento (dotação orçamentária)");
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
        $button_cadastrar->setAction(new TAction(['SaldoDepartamentoForm', 'onShow']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);
       // no __construct, troque a action do botão:
        $button_voltar = new TButton('button_button_voltar');
        $button_voltar->setAction(new TAction([$this, 'onBack']), 'Voltar'); // chama onBack()
        $button_voltar->addStyleClass('btn-default');
        $button_voltar->setImage('fas:arrow-alt-circle-left #000000');

        $this->datagrid_form->addField($button_voltar);

     /*   $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');
        $btnClose->addFunction("Template.closeRightPanel();");
        
        // Se você estiver usando um formulário com datagrid, adicione o botão como campo
        $this->datagrid_form->addField($btnClose);*/
        
        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['SaldoDepartamentoList', 'onExportCsv'],['static' => 1]), self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['SaldoDepartamentoList', 'onExportXls'],['static' => 1]), self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['SaldoDepartamentoList', 'onExportPdf'],['static' => 1]), self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['SaldoDepartamentoList', 'onExportXml'],['static' => 1]), self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_voltar);
        $head_left_actions->add($button_cadastrar);

        $head_right_actions->add($dropdown_button_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
       //     $container->add(TBreadCrumb::create(["Compras","Saldo Departamento"]));
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
                $object = new SaldoDepartamento($key, FALSE); 
                
                // Verifica dependências em outras tabelas
                $relations = [
                    'DotacaoPedidoFrotas'     => ['column' => 'saldo_departamento_id', 'alias' => 'Dotacao Pedido Frotas'],
                    'Pedido'                  => ['column' => 'saldo_departamento_id', 'alias' => 'Pedido Compras']
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
                // deletes the object from the database
                $object->delete();
 

                    $saldo_empenho = 0;

                 $saldo = SaldoDepartamento::where('departamento_unit_id', '=', TSession::getValue('depunitid'))->load();

                if($saldo){

                    $saldo_credito = 0;
                    $saldo_debito = 0;
                    $saldo_empenho = '';

                    foreach($saldo as $sdo){
                        //Credito
                        if($sdo->tipotransacao == 'C'){
                            $saldo_credito += $sdo->saldo_produto + $sdo->saldo_servico;

                        }elseif($sdo->tipotransacao == 'D'){
                            $saldo_debito += $sdo->saldo_produto + $sdo->saldo_servico;

                        }
                    }

                    $saldo_empenho = $saldo_credito - $saldo_debito;

                }

                $departamento = DepartamentoUnit::where('id', '=', TSession::getValue('depunitid'))
                                                ->where('system_unit_id', '=', TSession::getValue('unit1'))
                                                ->load();

                if($departamento){

                    foreach($departamento as $dp){
                        $dp->valor_empenho = $saldo_empenho;
                        $dp->store();
                    }
                }

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
            try
            {
                TTransaction::open(self::$database);
                $this->validarExclusaoPorStatus((int) ($param['key'] ?? 0));
                TTransaction::close();
            }
            catch (Exception $e)
            {
                if (TTransaction::getDatabase())
                {
                    TTransaction::rollback();
                }

                new TMessage('warning', $e->getMessage());
                return;
            }

            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);

            // mensagem customizada para status Anulado
            $mensagemConfirmacao = AdiantiCoreTranslator::translate('Do you really want to delete ?');
            try
            {
                TTransaction::open(self::$database);
                $saldoVerif = new SaldoDepartamento((int) ($param['key'] ?? 0));
                if ((string) $saldoVerif->status_saldo_departamento_id === (string) StatusSaldoDepartamento::ANULADO)
                {
                    $mensagemConfirmacao = 'Este empenho está <b>ANULADO</b>. Deseja realmente excluir este registro?';
                }
                TTransaction::close();
            }
            catch (Exception $e)
            {
                if (TTransaction::getDatabase())
                {
                    TTransaction::rollback();
                }
            }

            // shows a dialog to the user
            new TQuestion($mensagemConfirmacao, $action);
        }
    }

    public function onEdit($param = null)
    {
        try
        {
            TTransaction::open(self::$database);
            $this->validarEdicaoPorStatus((int) ($param['key'] ?? 0));
            TTransaction::close();

            $action = new TAction(['SaldoDepartamentoForm', 'onEdit']);
            $action->setParameters($param);
            AdiantiCoreApplication::loadPage('SaldoDepartamentoForm', 'onEdit', $param);
        }
        catch (Exception $e)
        {
            if (TTransaction::getDatabase())
            {
                TTransaction::rollback();
            }

            new TMessage('warning', $e->getMessage());
        }
    }

    private function validarEdicaoPorStatus(int $saldoDepartamentoId): void
    {
        if ($saldoDepartamentoId <= 0)
        {
            return;
        }

        $saldoDepartamento = new SaldoDepartamento($saldoDepartamentoId);
        $statusId = (string) $saldoDepartamento->status_saldo_departamento_id;

        if ($statusId !== (string) StatusSaldoDepartamento::ANULADO)
        {
            return;
        }

        $vinculos = $this->getVinculosPorContexto($saldoDepartamentoId);

        if ($vinculos['count'] > 0)
        {
            throw new Exception("Não é permitido alterar este empenho porque ele está anulado e já possui {$vinculos['alias']} vinculados.");
        }
    }

    private function validarExclusaoPorStatus(int $saldoDepartamentoId): void
    {
        if ($saldoDepartamentoId <= 0)
        {
            return;
        }

        $saldoDepartamento = new SaldoDepartamento($saldoDepartamentoId);
        $statusId = (string) $saldoDepartamento->status_saldo_departamento_id;

        if (!in_array($statusId, [StatusSaldoDepartamento::EMANDAMENTO, StatusSaldoDepartamento::ENCERRADO], true))
        {
            return;
        }

        $vinculos = $this->getVinculosPorContexto($saldoDepartamentoId);

        if ($vinculos['count'] > 0)
        {
            $statusTexto = ($statusId === StatusSaldoDepartamento::EMANDAMENTO) ? 'em andamento' : 'encerrado';
            throw new Exception("Não é permitido excluir este empenho porque ele está {$statusTexto} e já possui {$vinculos['alias']} vinculados.");
        }
    }

    private function getVinculosPorContexto(int $saldoDepartamentoId): array
    {
        $sistema = (string) TSession::getValue('sistema');

        $countDotacoes = $this->countVinculos('DotacaoPedidoFrotas', $saldoDepartamentoId);
        $countPedidos  = $this->countVinculos('Pedido', $saldoDepartamentoId);

        if ($sistema === 'frotas')
        {
            return ['count' => $countDotacoes, 'alias' => 'dotações'];
        }

        if ($sistema === 'compras')
        {
            return ['count' => $countPedidos, 'alias' => 'pedidos'];
        }

        $countTotal = $countDotacoes + $countPedidos;
        $alias = ($countDotacoes > 0 && $countPedidos > 0) ? 'dotações/pedidos' : (($countDotacoes > 0) ? 'dotações' : 'pedidos');

        return ['count' => $countTotal, 'alias' => $alias ?: 'vínculos'];
    }

    private function countVinculos(string $model, int $saldoDepartamentoId): int
    {
        $repository = new TRepository($model);
        $criteria = new TCriteria;
        $criteria->add(new TFilter('saldo_departamento_id', '=', $saldoDepartamentoId));
        return (int) $repository->count($criteria);
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

        // if (isset($data->departamento_unit_name) AND ( (is_scalar($data->departamento_unit_name) AND $data->departamento_unit_name !== '') OR (is_array($data->departamento_unit_name) AND (!empty($data->departamento_unit_id)) )) )
        // {

        //     $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        // }

        if (isset($data->tipo) AND ( (is_scalar($data->tipo) AND $data->tipo !== '') OR (is_array($data->tipo) AND (!empty($data->tipo)) )) )
        {

            $filters[] = new TFilter('tipo', '=', $data->tipo);// create the filter 
        }

        if (isset($data->tipotransacao) AND ( (is_scalar($data->tipotransacao) AND $data->tipotransacao !== '') OR (is_array($data->tipotransacao) AND (!empty($data->tipotransacao)) )) )
        {

            $filters[] = new TFilter('tipotransacao', '=', $data->tipotransacao);// create the filter 
        }

        if (isset($data->datatransacao) AND ( (is_scalar($data->datatransacao) AND $data->datatransacao !== '') OR (is_array($data->datatransacao) AND (!empty($data->datatransacao)) )) )
        {

            $filters[] = new TFilter('datatransacao', '=', $data->datatransacao);// create the filter 
        }

        if (isset($data->historico) AND ( (is_scalar($data->historico) AND $data->historico !== '') OR (is_array($data->historico) AND (!empty($data->historico)) )) )
        {

            $filters[] = new TFilter('historico', 'like', "%{$data->historico}%");// create the filter 
        }

        if (isset($data->saldo_produto) AND ( (is_scalar($data->saldo_produto) AND $data->saldo_produto !== '') OR (is_array($data->saldo_produto) AND (!empty($data->saldo_produto)) )) )
        {

            $filters[] = new TFilter('saldo_produto', '=', $data->saldo_produto);// create the filter 
        }
        if (isset($data->saldo_servico) AND ( (is_scalar($data->saldo_servico) AND $data->saldo_servico !== '') OR (is_array($data->saldo_servico) AND (!empty($data->saldo_servico)) )) )
        {

            $filters[] = new TFilter('saldo_servico', '=', $data->saldo_servico);// create the filter 
        }
        if (isset($data->status_saldo_departamento_id) AND ( (is_scalar($data->status_saldo_departamento_id) AND $data->status_saldo_departamento_id !== '') OR (is_array($data->status_saldo_departamento_id) AND (!empty($data->status_saldo_departamento_id)) )) )
        {

            $filters[] = new TFilter('status_saldo_departamento_id', '=', $data->status_saldo_departamento_id);// create the filter 
        }
        if (isset($data->numero_documento_empenho) AND ( (is_scalar($data->numero_documento_empenho) AND $data->numero_documento_empenho !== '') OR (is_array($data->numero_documento_empenho) AND (!empty($data->numero_documento_empenho)) )) )
        {

            $filters[] = new TFilter('numero_documento_empenho', '=', $data->numero_documento_empenho);// create the filter 
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

            // creates a repository for SaldoDepartamento
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
            $criteria->add(new TFilter('departamento_unit_id', '=',TSession::getValue('depunitid')));

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

        $object = new SaldoDepartamento($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    public function onSetProject($param = null)
    {
        $conteudo = unserialize(base64_decode($param['__row__data']));
        TSession::setValue('depunitid', null);
        TSession::setValue('depunitid', $conteudo->id);
        TSession::setValue('unit1', $conteudo->system_unit_id);
 TSession::setValue('voltar_para',null);
TSession::setValue('voltar_para', 'unidade'); // ou 'unidade' em outros fluxos
        // $criteria->add(new TFilter('departamento_unit_id', '=', $conteudo->id));

        TSession::setValue('SaldoDepartamentoList_filters', [new TFilter('departamento_unit_id', '=', $conteudo->id)]);

        AdiantiCoreApplication::loadPage(__CLASS__, 'onReload');
    }
    public function onSetProject2($param = null)
    {
      //  $conteudo = unserialize(base64_decode($param['__row__data']));
        TSession::setValue('depunitid', null);
        TSession::setValue('depunitid', $param['id']);
        TSession::setValue('unit1',TSession::getValue('idunit'));
        TSession::setValue('voltar_para',null);
TSession::setValue('voltar_para', 'departamento'); // ou 'unidade' em outros fluxos

        // $criteria->add(new TFilter('departamento_unit_id', '=', $conteudo->id));

        TSession::setValue('SaldoDepartamentoList_filters', [new TFilter('departamento_unit_id', '=', $param['id'])]);

        AdiantiCoreApplication::loadPage(__CLASS__, 'onReload');
    }
    public function onBack($param = null)
    {
        // Descubra o target_container se estiver usando painéis
        $target = $param['target_container'] 
                ?? ($this->adianti_target_container ?? null);

        $voltarPara = TSession::getValue('voltar_para');
        $unit1      = TSession::getValue('unit1');

        // Limpa a flag ANTES de redirecionar
        TSession::setValue('voltar_para', null);

        if ($voltarPara === 'unidade' && !empty($unit1)) {
            // Vai para SystemUnitForm/onEdit
            $params = [
                'key'              => (string) $unit1,
                'id'               => (string) $unit1,
                'target_container' => $target,
            ];
            AdiantiCoreApplication::loadPage('SystemUnitForm', 'onEdit', $params);
        } else {
            // Vai para a lista de departamentos
            $params = [
                'target_container' => $target,
            ];
            AdiantiCoreApplication::loadPage('DepartamentoUnitSimpleList', 'onShow', $params);
        }
    }

}

