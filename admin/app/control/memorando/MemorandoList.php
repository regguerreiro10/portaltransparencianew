<?php

class MemorandoList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private static $database = 'minierp';
    private static $activeRecord = 'Memorando';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Memorando';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder('formFilter_MemorandoList');
        $this->form->setFormTitle('Filtros de memorandos');

        $numero_memorando = new TEntry('numero_memorando');
        $assunto = new TEntry('assunto');
        $status = new TCombo('status');
        $caixa = new TCombo('caixa');
        $data_inicio = new TDate('data_inicio');
        $departamento_id = new TDBCombo('departamento_id', 'minierp', 'DepartamentoUnit', 'id', '{name}', 'name asc');
        $usuario_id = new TDBCombo('usuario_id', 'minierp', 'SystemUsers', 'id', '{name}', 'name asc');

        $status->addItems(MemorandoHelper::getStatusOptions());
        $caixa->addItems([
            '' => 'Todas',
            'entrada' => 'Entrada',
            'saida' => 'Saida',
            'arquivados' => 'Arquivados',
        ]);

        $status->enableSearch();
        $caixa->enableSearch();
        $departamento_id->enableSearch();
        $usuario_id->enableSearch();

        $numero_memorando->setSize('100%');
        $assunto->setSize('100%');
        $status->setSize('100%');
        $caixa->setSize('100%');
        $data_inicio->setSize('100%');
        $departamento_id->setSize('100%');
        $usuario_id->setSize('100%');

        $data_inicio->setMask('dd/mm/yyyy');
        $data_inicio->setDatabaseMask('yyyy-mm-dd');

        $numero_memorando->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $assunto->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $status->setChangeAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $caixa->setChangeAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $departamento_id->setChangeAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $usuario_id->setChangeAction(new TAction([$this, 'onSearch'], ['static' => '1']));

        $row1 = $this->form->addFields(
            [new TLabel('Memorando:', null, '14px', null, '100%'), $numero_memorando],
            [new TLabel('Assunto:', null, '14px', null, '100%'), $assunto]
        );
        $row1->layout = ['col-sm-6', 'col-sm-6'];

        $row2 = $this->form->addFields(
            [new TLabel('Departamento:', null, '14px', null, '100%'), $departamento_id],
            [new TLabel('Remetente/destinatario:', null, '14px', null, '100%'), $usuario_id]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $row3 = $this->form->addFields(
            [new TLabel('Status:', null, '14px', null, '100%'), $status],
            [new TLabel('Caixa:', null, '14px', null, '100%'), $caixa],
            [new TLabel('Data inicial:', null, '14px', null, '100%'), $data_inicio]
        );
        $row3->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $buttonBuscar = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $buttonBuscar->addStyleClass('btn-primary');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(360);

        $column_numero = new TDataGridColumn('numero_memorando', 'Memorando', 'left', '12%');
        $column_vinculo = new TDataGridColumn('origem_vinculo', 'Origem', 'left', '15%');
        $column_origem = new TDataGridColumn('departamento_origem_nome', 'Depto origem', 'left', '12%');
        $column_destino = new TDataGridColumn('departamentos_destino_resumo', 'Depto destino', 'left', '14%');
        $column_remetente = new TDataGridColumn('remetente_nome', 'Remetente', 'left', '11%');
        $column_destinatarios = new TDataGridColumn('destinatarios_resumo', 'Destinatario(s)', 'left', '14%');
        $column_assunto = new TDataGridColumn('assunto', 'Assunto', 'left');
        $column_data = new TDataGridColumn('data_memorando', 'Data', 'center', '11%');
        $column_status = new TDataGridColumn('status', 'Status', 'center', '9%');

        $column_data->setTransformer(function ($value) {
            return TDateTime::convertToMask($value, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii');
        });

        $column_status->setTransformer(function ($value, $object = null) {
            $color = MemorandoHelper::getStatusColor($value);
            return "<span style='display:inline-block; padding:4px 10px; border-radius:999px; background:{$color}; color:#fff; font-size:12px;'>{$value}</span>";
        });

        $column_vinculo->setTransformer(function ($value, $object = null) {
            $color = '#64748b';
            if ($object && $object->tipo === 'Resposta') {
                $color = '#7c3aed';
            } elseif ($object && $object->tipo === 'Encaminhamento') {
                $color = '#0f766e';
            }

            return "<span style='display:inline-block; padding:4px 9px; border-radius:6px; background:{$color}; color:#fff; font-size:12px; line-height:1.3;'>{$value}</span>";
        });

        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_vinculo);
        $this->datagrid->addColumn($column_origem);
        $this->datagrid->addColumn($column_destino);
        $this->datagrid->addColumn($column_remetente);
        $this->datagrid->addColumn($column_destinatarios);
        $this->datagrid->addColumn($column_assunto);
        $this->datagrid->addColumn($column_data);
        $this->datagrid->addColumn($column_status);

        $actionView = new TDataGridAction(['MemorandoFormView', 'onShow']);
        $actionView->setLabel('Visualizar');
        $actionView->setImage('fas:search-plus #0f4c81');
        $actionView->setUseButton(false);
        $actionView->setButtonClass('btn btn-default btn-sm');
        $actionView->setField(self::$primaryKey);
        $this->datagrid->addAction($actionView);

        $actionEdit = new TDataGridAction(['MemorandoForm', 'onEdit']);
        $actionEdit->setLabel('Editar');
        $actionEdit->setImage('far:edit #478fca');
        $actionEdit->setUseButton(false);
        $actionEdit->setButtonClass('btn btn-default btn-sm');
        $actionEdit->setField(self::$primaryKey);
        $this->datagrid->addAction($actionEdit);

        $actionDelete = new TDataGridAction([$this, 'onDelete']);
        $actionDelete->setLabel('Excluir');
        $actionDelete->setImage('fas:trash-alt #dd5a43');
        $actionDelete->setUseButton(false);
        $actionDelete->setButtonClass('btn btn-default btn-sm');
        $actionDelete->setField(self::$primaryKey);
        $this->datagrid->addAction($actionDelete);

        $this->datagrid->createModel();

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Memorandos');
        $panel->datagrid = 'datagrid-container';
        $panel->getBody()->class .= ' table-responsive';
        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';
        $headLeft = new TElement('div');
        $headLeft->class = ' datagrid-header-actions-left-actions ';
        $headRight = new TElement('div');
        $headRight->class = ' datagrid-header-actions-left-actions ';
        $headerActions->add($headLeft);
        $headerActions->add($headRight);

        $buttonCadastrar = new TButton('button_cadastrar_memorando');
        $buttonCadastrar->setAction(new TAction(['MemorandoForm', 'onShow']), 'Novo memorando');
        $buttonCadastrar->setImage('fas:plus #69aa46');
        $this->datagrid_form->addField($buttonCadastrar);
        $headLeft->add($buttonCadastrar);

        $buttonEntrada = new TButton('button_entrada_memorando');
        $buttonEntrada->setAction(new TAction([$this, 'onApplyBoxFilter'], ['caixa' => 'entrada']), 'Entrada');
        $buttonEntrada->setImage('fas:inbox #0f766e');
        $this->datagrid_form->addField($buttonEntrada);
        $headLeft->add($buttonEntrada);

        $buttonSaida = new TButton('button_saida_memorando');
        $buttonSaida->setAction(new TAction([$this, 'onApplyBoxFilter'], ['caixa' => 'saida']), 'Saida');
        $buttonSaida->setImage('fas:paper-plane #1d4ed8');
        $this->datagrid_form->addField($buttonSaida);
        $headLeft->add($buttonSaida);

        $buttonArquivados = new TButton('button_arquivados_memorando');
        $buttonArquivados->setAction(new TAction([$this, 'onApplyBoxFilter'], ['caixa' => 'arquivados']), 'Arquivados');
        $buttonArquivados->setImage('fas:archive #6b7280');
        $this->datagrid_form->addField($buttonArquivados);
        $headLeft->add($buttonArquivados);

        $buttonFiltros = new TButton('button_filtros_memorando');
        $buttonFiltros->setAction(new TAction([__CLASS__, 'onShowCurtainFilters']), 'Filtros');
        $buttonFiltros->addStyleClass('btn-default');
        $buttonFiltros->setImage('fas:filter #000000');
        $this->datagrid_form->addField($buttonFiltros);
        $headLeft->add($buttonFiltros);

        $buttonLimpar = new TButton('button_limpar_filtros_memorando');
        $buttonLimpar->setAction(new TAction([__CLASS__, 'onClearFilters']), 'Limpar filtros');
        $buttonLimpar->addStyleClass('btn-default');
        $buttonLimpar->setImage('fas:eraser #f44336');
        $this->datagrid_form->addField($buttonLimpar);
        $headLeft->add($buttonLimpar);

        $buttonAtualizar = new TButton('button_atualizar_memorando');
        $buttonAtualizar->setAction(new TAction([__CLASS__, 'onRefresh']), 'Atualizar');
        $buttonAtualizar->addStyleClass('btn-default');
        $buttonAtualizar->setImage('fas:sync-alt #03a9f4');
        $this->datagrid_form->addField($buttonAtualizar);
        $headLeft->add($buttonAtualizar);

        $panel->add($headerActions);
        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Comunicacao interna', 'Memorandos']));
        }
        $container->add($panel);

        parent::add($container);
    }

    public function onApplyBoxFilter($param = null)
    {
        $data = TSession::getValue(__CLASS__ . '_filter_data') ?: new stdClass;
        $data->caixa = $param['caixa'] ?? '';
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        $this->form->setData($data);
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public static function onShowCurtainFilters($param = null)
    {
        try {
            $filter = new self([]);

            $buttonClose = new TButton('closeCurtain');
            $buttonClose->class = 'btn btn-sm btn-default';
            $buttonClose->style = 'margin-right:10px;';
            $buttonClose->onClick = 'Template.closeRightPanel();';
            $buttonClose->setLabel('Fechar');
            $buttonClose->setImage('fas:times');

            $filter->form->addHeaderWidget($buttonClose);

            $page = new TPage;
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('page-name', 'MemorandoListSearch');
            $page->setProperty('page_name', 'MemorandoListSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onClearFilters($param = null)
    {
        TSession::setValue(__CLASS__ . '_filter_data', null);
        TSession::setValue(__CLASS__ . '_filters', null);
        $this->form->clear();
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onRefresh($param = null)
    {
        $this->onReload($param ?: []);
    }

    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onReload($param = null)
    {
        try {
            TTransaction::open(self::$database);
            MemorandoSchemaHelper::ensureSchema();

            $repository = new TRepository(self::$activeRecord);
            $criteria = new TCriteria;

            $criteria->setProperties($param);
            $criteria->setProperty('order', $param['order'] ?? 'data_memorando');
            $criteria->setProperty('direction', $param['direction'] ?? 'desc');
            $criteria->setProperty('limit', $this->limit);
            $criteria->add(new TFilter('deleted_at', 'is', null));

            $accessSql = MemorandoHelper::buildAccessFilterSql();
            if ($accessSql) {
                $criteria->add(new TFilter('id', 'IN', $accessSql));
            }

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!empty($data->numero_memorando)) {
                $criteria->add(new TFilter('numero_memorando', 'like', "%{$data->numero_memorando}%"));
            }
            if (!empty($data->assunto)) {
                $criteria->add(new TFilter('assunto', 'like', "%{$data->assunto}%"));
            }
            if (!empty($data->status)) {
                $criteria->add(new TFilter('status', '=', $data->status));
            }
            if (!empty($data->data_inicio)) {
                $criteria->add(new TFilter('DATE(data_memorando)', '>=', TDate::date2us($data->data_inicio)));
            }
            if (!empty($data->departamento_id)) {
                $criteria->add(new TFilter(
                    'id',
                    'IN',
                    "(SELECT memorando_id FROM memorando_destinatario WHERE departamento_unit_id = " . (int) $data->departamento_id . "
                      UNION
                      SELECT id FROM memorando WHERE departamento_unit_id = " . (int) $data->departamento_id . ')'
                ));
            }
            if (!empty($data->usuario_id)) {
                $criteria->add(new TFilter(
                    'id',
                    'IN',
                    "(SELECT memorando_id FROM memorando_destinatario WHERE system_users_id = " . (int) $data->usuario_id . "
                      UNION
                      SELECT id FROM memorando WHERE system_users_remetente_id = " . (int) $data->usuario_id . ')'
                ));
            }
            if (!empty($data->caixa)) {
                $context = MemorandoHelper::getCurrentUserContext();
                $deptSql = !empty($context['department_ids']) ? implode(',', array_map('intval', $context['department_ids'])) : '0';
                if ($data->caixa === 'entrada') {
                    $criteria->add(new TFilter(
                        'id',
                        'IN',
                        "(SELECT memorando_id FROM memorando_destinatario WHERE system_users_id = " . (int) $context['user_id'] . " OR departamento_unit_id IN ({$deptSql}))"
                    ));
                } elseif ($data->caixa === 'saida') {
                    $criteria->add(new TFilter('system_users_remetente_id', '=', (int) $context['user_id']));
                } elseif ($data->caixa === 'arquivados') {
                    $criteria->add(new TFilter('status', '=', 'Arquivado'));
                }
            }

            $objects = $repository->load($criteria, false);
            $this->datagrid->clear();

            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }

            $countCriteria = clone $criteria;
            $countCriteria->resetProperties();
            $count = $repository->count($countCriteria);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($this->limit);

            TTransaction::close();
            $this->loaded = true;
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onDelete($param = null)
    {
        if (isset($param['delete']) && $param['delete'] == 1) {
            try {
                TTransaction::open(self::$database);
                MemorandoSchemaHelper::ensureSchema();
                $object = new Memorando($param['key'], false);
                if (!MemorandoHelper::canEditMemorando($object)) {
                    throw new Exception('Voce nao tem permissao para excluir este memorando.');
                }
                $object->delete();
                TTransaction::close();
                $this->onReload($param);
                TToast::show('success', 'Memorando excluido.', 'topRight', 'far:check-circle');
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            $action = new TAction([$this, 'onDelete']);
            $action->setParameters($param);
            $action->setParameter('delete', 1);
            new TQuestion('Deseja realmente excluir este memorando?', $action);
        }
    }

    public function onShow($param = null)
    {
        if (!$this->loaded) {
            $this->onReload(func_get_arg(0));
        }
    }
}
