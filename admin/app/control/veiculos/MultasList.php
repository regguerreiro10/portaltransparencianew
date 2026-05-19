<?php

class MultasList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Multas';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Multas';
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

        $basename   = urlencode('multas-list.pdf');
$download   = "download.php?file=app/manual/multas-list.pdf&basename={$basename}";

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

        $criteria_veiculos_id = new TCriteria();
        $criteria_condutor_id = new TCriteria();
        $criteria_system_unit_id = new TCriteria();
        $criteria_departamento_unit_id = new TCriteria();
        $criteria_status_multas_id = new TCriteria();

        $id = new TEntry('id');
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa}','id asc' , $criteria_veiculos_id );
        $condutor_id = new TDBCombo('condutor_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_condutor_id );
        $system_unit_id = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_system_unit_id );
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_departamento_unit_id );
        $status_multas_id = new TDBCombo('status_multas_id', 'minierp', 'StatusMultas', 'descricao', '{id}','id asc' , $criteria_status_multas_id );
        $numero_alt = new TEntry('numero_alt');
        $enquadramento = new TEntry('enquadramento');
        $descricao = new TEntry('descricao');
        $data_infracao = new TEntry('data_infracao');
        $local_infracao = new TEntry('local_infracao');
        $orgao_autuador = new TEntry('orgao_autuador');
        $pontos_cnh = new TEntry('pontos_cnh');
        $valor_original = new TEntry('valor_original');
        $valor_desconto = new TEntry('valor_desconto');
        $parcela = new TEntry('parcela');
        $data_vencimento = new TEntry('data_vencimento');
        $data_pagamento = new TEntry('data_pagamento');
        $valor_pago = new TEntry('valor_pago');
        $motivo_cancelamento = new TEntry('motivo_cancelamento');
        $obs = new TEntry('obs');

        $id->exitOnEnter();
        $numero_alt->exitOnEnter();
        $enquadramento->exitOnEnter();
        $descricao->exitOnEnter();
        $data_infracao->exitOnEnter();
        $local_infracao->exitOnEnter();
        $orgao_autuador->exitOnEnter();
        $pontos_cnh->exitOnEnter();
        $valor_original->exitOnEnter();
        $valor_desconto->exitOnEnter();
        $parcela->exitOnEnter();
        $data_vencimento->exitOnEnter();
        $data_pagamento->exitOnEnter();
        $valor_pago->exitOnEnter();
        $motivo_cancelamento->exitOnEnter();
        $obs->exitOnEnter();

        $id->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $numero_alt->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $enquadramento->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $descricao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $data_infracao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $local_infracao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $orgao_autuador->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $pontos_cnh->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $valor_original->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $valor_desconto->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $parcela->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $data_vencimento->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $data_pagamento->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $valor_pago->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $motivo_cancelamento->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $obs->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $veiculos_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $condutor_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $system_unit_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $departamento_unit_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $status_multas_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $veiculos_id->enableSearch();
        $condutor_id->enableSearch();
        $system_unit_id->enableSearch();
        $status_multas_id->enableSearch();
        $departamento_unit_id->enableSearch();

        $id->setSize('100%');
        $obs->setSize('100%');
        $parcela->setSize('100%');
        $descricao->setSize('100%');
        $numero_alt->setSize('100%');
        $pontos_cnh->setSize('100%');
        $valor_pago->setSize('100%');
        $veiculos_id->setSize('100%');
        $condutor_id->setSize('100%');
        $enquadramento->setSize('100%');
        $data_infracao->setSize('100%');
        $system_unit_id->setSize('100%');
        $local_infracao->setSize('100%');
        $orgao_autuador->setSize('100%');
        $valor_original->setSize('100%');
        $valor_desconto->setSize('100%');
        $data_pagamento->setSize('100%');
        $data_vencimento->setSize('100%');
        $status_multas_id->setSize('100%');
        $motivo_cancelamento->setSize('100%');
        $departamento_unit_id->setSize('100%');

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $filterVar = TSession::getValue('idunit');
        $this->filter_criteria->add(new TFilter('system_unit_id', '=', $filterVar));

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_veiculos_id = new TDataGridColumn('veiculos->placa', "Placa", 'left');
        $column_condutor_nome = new TDataGridColumn('condutor->nome', "Condutor", 'left');
        $column_system_unit_name = new TDataGridColumn('system_unit->name', "Unidade", 'left');
        $column_departamento_unit_name = new TDataGridColumn('departamento_unit->name', "Unidade / Dep / Secretaria", 'left');
        $column_status_multas_id = new TDataGridColumn('status_multas->descricao', "Status", 'left');
        $column_numero_alt = new TDataGridColumn('numero_alt', "Numero alt", 'left');
        $column_enquadramento = new TDataGridColumn('enquadramento', "Enquadramento", 'left');
        $column_descricao = new TDataGridColumn('descricao', "Descricao", 'left');
        $column_data_infracao_transformed = new TDataGridColumn('data_infracao', "Data infracao", 'left');
        $column_local_infracao = new TDataGridColumn('local_infracao', "Local infracao", 'left');
        $column_orgao_autuador = new TDataGridColumn('orgao_autuador', "Orgao autuador", 'left');
        $column_pontos_cnh = new TDataGridColumn('pontos_cnh', "Pontos cnh", 'left');
        $column_valor_original_transformed = new TDataGridColumn('valor_original', "Valor original", 'left');
        $column_valor_desconto_transformed = new TDataGridColumn('valor_desconto', "Valor desconto", 'left');
        $column_parcela = new TDataGridColumn('parcela', "Parcela", 'left');
        $column_data_vencimento_transformed = new TDataGridColumn('data_vencimento', "Data vencimento", 'left');
        $column_data_pagamento_transformed = new TDataGridColumn('data_pagamento', "Data pagamento", 'left');
        $column_valor_pago = new TDataGridColumn('valor_pago', "Valor pago", 'left');
        $column_motivo_cancelamento = new TDataGridColumn('motivo_cancelamento', "Motivo cancelamento", 'left');
        $column_obs = new TDataGridColumn('obs', "Obs", 'left');

        $column_data_infracao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y H:i');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_valor_original_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_valor_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_data_vencimento_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y H:i');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_data_pagamento_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
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
        $column_status_multas_id->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $status = new StatusMultas($object->status_multas_id);
           
            $cor = $status->cor ?? '#777';
            $badge = new TElement('span');
            $badge->style = "background: {$cor}; color: #fff; padding: 2px 8px; border-radius: 12px; display: inline-block;";
            $badge->add($value);
            return $badge;        
        }); 
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_veiculos_id);
        $this->datagrid->addColumn($column_condutor_nome);
        $this->datagrid->addColumn($column_system_unit_name);
        $this->datagrid->addColumn($column_departamento_unit_name);
        $this->datagrid->addColumn($column_status_multas_id);
        $this->datagrid->addColumn($column_numero_alt);
        $this->datagrid->addColumn($column_enquadramento);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_data_infracao_transformed);
        $this->datagrid->addColumn($column_local_infracao);
        $this->datagrid->addColumn($column_orgao_autuador);
        $this->datagrid->addColumn($column_pontos_cnh);
        $this->datagrid->addColumn($column_valor_original_transformed);
        $this->datagrid->addColumn($column_valor_desconto_transformed);
        $this->datagrid->addColumn($column_parcela);
        $this->datagrid->addColumn($column_data_vencimento_transformed);
        $this->datagrid->addColumn($column_data_pagamento_transformed);
        $this->datagrid->addColumn($column_valor_pago);
        $this->datagrid->addColumn($column_motivo_cancelamento);
        $this->datagrid->addColumn($column_obs);

        $action_onEdit = new TDataGridAction(array('MultasForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('MultasList', 'onDelete'));
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
        $td_veiculos_id = TElement::tag('td', $veiculos_id);
        $tr->add($td_veiculos_id);
        $td_condutor_id = TElement::tag('td', $condutor_id);
        $tr->add($td_condutor_id);
        $td_system_unit_id = TElement::tag('td', $system_unit_id);
        $tr->add($td_system_unit_id);
        $td_departamento_unit_id = TElement::tag('td', $departamento_unit_id);
        $tr->add($td_departamento_unit_id);
        $td_status_multas_id = TElement::tag('td', $status_multas_id);
        $tr->add($td_status_multas_id);
        $td_numero_alt = TElement::tag('td', $numero_alt);
        $tr->add($td_numero_alt);
        $td_enquadramento = TElement::tag('td', $enquadramento);
        $tr->add($td_enquadramento);
        $td_descricao = TElement::tag('td', $descricao);
        $tr->add($td_descricao);
        $td_data_infracao = TElement::tag('td', $data_infracao);
        $tr->add($td_data_infracao);
        $td_local_infracao = TElement::tag('td', $local_infracao);
        $tr->add($td_local_infracao);
        $td_orgao_autuador = TElement::tag('td', $orgao_autuador);
        $tr->add($td_orgao_autuador);
        $td_pontos_cnh = TElement::tag('td', $pontos_cnh);
        $tr->add($td_pontos_cnh);
        $td_valor_original = TElement::tag('td', $valor_original);
        $tr->add($td_valor_original);
        $td_valor_desconto = TElement::tag('td', $valor_desconto);
        $tr->add($td_valor_desconto);
        $td_parcela = TElement::tag('td', $parcela);
        $tr->add($td_parcela);
        $td_data_vencimento = TElement::tag('td', $data_vencimento);
        $tr->add($td_data_vencimento);
        $td_data_pagamento = TElement::tag('td', $data_pagamento);
        $tr->add($td_data_pagamento);
        $td_valor_pago = TElement::tag('td', $valor_pago);
        $tr->add($td_valor_pago);
        $td_motivo_cancelamento = TElement::tag('td', $motivo_cancelamento);
        $tr->add($td_motivo_cancelamento);
        $td_obs = TElement::tag('td', $obs);
        $tr->add($td_obs);

        $this->datagrid_form->addField($id);
        $this->datagrid_form->addField($veiculos_id);
        $this->datagrid_form->addField($condutor_id);
        $this->datagrid_form->addField($system_unit_id);
        $this->datagrid_form->addField($departamento_unit_id);
        $this->datagrid_form->addField($status_multas_id);
        $this->datagrid_form->addField($numero_alt);
        $this->datagrid_form->addField($enquadramento);
        $this->datagrid_form->addField($descricao);
        $this->datagrid_form->addField($data_infracao);
        $this->datagrid_form->addField($local_infracao);
        $this->datagrid_form->addField($orgao_autuador);
        $this->datagrid_form->addField($pontos_cnh);
        $this->datagrid_form->addField($valor_original);
        $this->datagrid_form->addField($valor_desconto);
        $this->datagrid_form->addField($parcela);
        $this->datagrid_form->addField($data_vencimento);
        $this->datagrid_form->addField($data_pagamento);
        $this->datagrid_form->addField($valor_pago);
        $this->datagrid_form->addField($motivo_cancelamento);
        $this->datagrid_form->addField($obs);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de multas {$manual}");
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
        $button_cadastrar->setAction(new TAction(['MultasForm', 'onShow']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['MultasList', 'onExportCsv'],['static' => 1]), self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['MultasList', 'onExportXls'],['static' => 1]), self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['MultasList', 'onExportPdf'],['static' => 1]), self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['MultasList', 'onExportXml'],['static' => 1]), self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_cadastrar);

        $head_right_actions->add($dropdown_button_exportar);

        $this->datagrid_form->add($this->datagrid);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
     //       $container->add(TBreadCrumb::create(["Veiculos","Multas"]));
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
                $object = new Multas($key, FALSE); 

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

        if (isset($data->veiculos_id) AND ( (is_scalar($data->veiculos_id) AND $data->veiculos_id !== '') OR (is_array($data->veiculos_id) AND (!empty($data->veiculos_id)) )) )
        {

            $filters[] = new TFilter('veiculos_id', '=', $data->veiculos_id);// create the filter 
        }

        if (isset($data->condutor_id) AND ( (is_scalar($data->condutor_id) AND $data->condutor_id !== '') OR (is_array($data->condutor_id) AND (!empty($data->condutor_id)) )) )
        {

            $filters[] = new TFilter('condutor_id', '=', $data->condutor_id);// create the filter 
        }

        if (isset($data->system_unit_id) AND ( (is_scalar($data->system_unit_id) AND $data->system_unit_id !== '') OR (is_array($data->system_unit_id) AND (!empty($data->system_unit_id)) )) )
        {

            $filters[] = new TFilter('system_unit_id', '=', $data->system_unit_id);// create the filter 
        }

        if (isset($data->departamento_unit_id) AND ( (is_scalar($data->departamento_unit_id) AND $data->departamento_unit_id !== '') OR (is_array($data->departamento_unit_id) AND (!empty($data->departamento_unit_id)) )) )
        {

            $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        }

        if (isset($data->status_multas_id) AND ( (is_scalar($data->status_multas_id) AND $data->status_multas_id !== '') OR (is_array($data->status_multas_id) AND (!empty($data->status_multas_id)) )) )
        {

            $filters[] = new TFilter('status_multas_id', '=', $data->status_multas_id);// create the filter 
        }

        if (isset($data->numero_alt) AND ( (is_scalar($data->numero_alt) AND $data->numero_alt !== '') OR (is_array($data->numero_alt) AND (!empty($data->numero_alt)) )) )
        {

            $filters[] = new TFilter('numero_alt', 'like', "%{$data->numero_alt}%");// create the filter 
        }

        if (isset($data->enquadramento) AND ( (is_scalar($data->enquadramento) AND $data->enquadramento !== '') OR (is_array($data->enquadramento) AND (!empty($data->enquadramento)) )) )
        {

            $filters[] = new TFilter('enquadramento', 'like', "%{$data->enquadramento}%");// create the filter 
        }

        if (isset($data->descricao) AND ( (is_scalar($data->descricao) AND $data->descricao !== '') OR (is_array($data->descricao) AND (!empty($data->descricao)) )) )
        {

            $filters[] = new TFilter('descricao', 'like', "%{$data->descricao}%");// create the filter 
        }

        if (isset($data->data_infracao) AND ( (is_scalar($data->data_infracao) AND $data->data_infracao !== '') OR (is_array($data->data_infracao) AND (!empty($data->data_infracao)) )) )
        {

            $filters[] = new TFilter('data_infracao', '=', $data->data_infracao);// create the filter 
        }

        if (isset($data->local_infracao) AND ( (is_scalar($data->local_infracao) AND $data->local_infracao !== '') OR (is_array($data->local_infracao) AND (!empty($data->local_infracao)) )) )
        {

            $filters[] = new TFilter('local_infracao', 'like', "%{$data->local_infracao}%");// create the filter 
        }

        if (isset($data->orgao_autuador) AND ( (is_scalar($data->orgao_autuador) AND $data->orgao_autuador !== '') OR (is_array($data->orgao_autuador) AND (!empty($data->orgao_autuador)) )) )
        {

            $filters[] = new TFilter('orgao_autuador', 'like', "%{$data->orgao_autuador}%");// create the filter 
        }

        if (isset($data->pontos_cnh) AND ( (is_scalar($data->pontos_cnh) AND $data->pontos_cnh !== '') OR (is_array($data->pontos_cnh) AND (!empty($data->pontos_cnh)) )) )
        {

            $filters[] = new TFilter('pontos_cnh', '=', $data->pontos_cnh);// create the filter 
        }

        if (isset($data->valor_original) AND ( (is_scalar($data->valor_original) AND $data->valor_original !== '') OR (is_array($data->valor_original) AND (!empty($data->valor_original)) )) )
        {

            $filters[] = new TFilter('valor_original', '=', $data->valor_original);// create the filter 
        }

        if (isset($data->valor_desconto) AND ( (is_scalar($data->valor_desconto) AND $data->valor_desconto !== '') OR (is_array($data->valor_desconto) AND (!empty($data->valor_desconto)) )) )
        {

            $filters[] = new TFilter('valor_desconto', '=', $data->valor_desconto);// create the filter 
        }

        if (isset($data->parcela) AND ( (is_scalar($data->parcela) AND $data->parcela !== '') OR (is_array($data->parcela) AND (!empty($data->parcela)) )) )
        {

            $filters[] = new TFilter('parcela', '=', $data->parcela);// create the filter 
        }

        if (isset($data->data_vencimento) AND ( (is_scalar($data->data_vencimento) AND $data->data_vencimento !== '') OR (is_array($data->data_vencimento) AND (!empty($data->data_vencimento)) )) )
        {

            $filters[] = new TFilter('data_vencimento', '=', $data->data_vencimento);// create the filter 
        }

        if (isset($data->data_pagamento) AND ( (is_scalar($data->data_pagamento) AND $data->data_pagamento !== '') OR (is_array($data->data_pagamento) AND (!empty($data->data_pagamento)) )) )
        {

            $filters[] = new TFilter('data_pagamento', '=', $data->data_pagamento);// create the filter 
        }

        if (isset($data->valor_pago) AND ( (is_scalar($data->valor_pago) AND $data->valor_pago !== '') OR (is_array($data->valor_pago) AND (!empty($data->valor_pago)) )) )
        {

            $filters[] = new TFilter('valor_pago', '=', $data->valor_pago);// create the filter 
        }

        if (isset($data->motivo_cancelamento) AND ( (is_scalar($data->motivo_cancelamento) AND $data->motivo_cancelamento !== '') OR (is_array($data->motivo_cancelamento) AND (!empty($data->motivo_cancelamento)) )) )
        {

            $filters[] = new TFilter('motivo_cancelamento', 'like', "%{$data->motivo_cancelamento}%");// create the filter 
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

            // creates a repository for Multas
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

        $object = new Multas($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

