<?php

class ProcessoAdministrativoTramitacaoList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'ProcessoAdministrativoTramitacao';
    private static $primaryKey = 'id';
    private static $formName = 'formList_ProcessoAdministrativoTramitacao';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $tipo_evento = new TEntry('tipo_evento');
        $usuario_responsavel = new TEntry('usuario_responsavel');

        $tipo_evento->setSize('100%');
        $usuario_responsavel->setSize('100%');
        $tipo_evento->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $usuario_responsavel->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $this->filter_criteria = new TCriteria;

        if (!empty($param['processo_administrativo_id'])) {
            TSession::setValue(__CLASS__ . '_processo_administrativo_id', $param['processo_administrativo_id']);
        }

        $processoId = TSession::getValue(__CLASS__ . '_processo_administrativo_id');
        TSession::setValue('processo_administrativo_tramitacao_processo_id', $processoId);

        if ($processoId) {
            $this->filter_criteria->add(new TFilter('processo_administrativo_id', '=', $processoId));
        }

        $column_data = new TDataGridColumn('data_tramitacao', 'Data/hora', 'center', '16%');
        $column_evento = new TDataGridColumn('tipo_evento', 'Evento', 'left', '14%');
        $column_origem = new TDataGridColumn('departamento_origem', 'Origem', 'left', '18%');
        $column_destino = new TDataGridColumn('departamento_destino', 'Destino', 'left', '18%');
        $column_usuario = new TDataGridColumn('usuario_responsavel', 'Responsavel', 'left', '16%');
        $column_observacao = new TDataGridColumn('observacao', 'Observacao', 'left');

        $column_data->setTransformer(function ($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii');
        });

        $this->datagrid->addColumn($column_data);
        $this->datagrid->addColumn($column_evento);
        $this->datagrid->addColumn($column_origem);
        $this->datagrid->addColumn($column_destino);
        $this->datagrid->addColumn($column_usuario);
        $this->datagrid->addColumn($column_observacao);

        $actionEdit = new TDataGridAction(['ProcessoAdministrativoTramitacaoForm', 'onEdit']);
        $actionEdit->setUseButton(false);
        $actionEdit->setButtonClass('btn btn-default btn-sm');
        $actionEdit->setLabel('Editar');
        $actionEdit->setImage('far:edit #478fca');
        $actionEdit->setField(self::$primaryKey);
        $this->datagrid->addAction($actionEdit);

        $actionDelete = new TDataGridAction([$this, 'onDelete']);
        $actionDelete->setUseButton(false);
        $actionDelete->setButtonClass('btn btn-default btn-sm');
        $actionDelete->setLabel('Excluir');
        $actionDelete->setImage('fas:trash-alt #dd5a43');
        $actionDelete->setField(self::$primaryKey);
        $this->datagrid->addAction($actionDelete);

        $this->datagrid->createModel();

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';
        $this->datagrid_form->addField($tipo_evento);
        $this->datagrid_form->addField($usuario_responsavel);
        $this->datagrid_form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $tipo_evento));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $usuario_responsavel));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Tramitacoes do processo');
        $panel->datagrid = 'datagrid-container';
        $panel->getBody()->class .= ' table-responsive';
        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headLeft = new TElement('div');
        $headLeft->class = ' datagrid-header-actions-left-actions ';
        $headRight = new TElement('div');
        $headRight->class = ' datagrid-header-actions-left-actions ';
        $headerActions->add($headLeft);
        $headerActions->add($headRight);
        $this->datagrid_form->add($headerActions);

        $buttonCadastrar = new TButton('button_cadastrar_processo_tramitacao');
        $actionCadastrar = new TAction(['ProcessoAdministrativoTramitacaoForm', 'onShow']);
        $actionCadastrar->setParameter('processo_administrativo_id', $processoId);
        $buttonCadastrar->setAction($actionCadastrar, 'Cadastrar');
        $buttonCadastrar->setImage('fas:plus #69aa46');
        $this->datagrid_form->addField($buttonCadastrar);
        $headLeft->add($buttonCadastrar);

        $buttonVoltar = new TButton('button_voltar_processo_administrativo');
        $buttonVoltar->setAction(new TAction(['ProcessoAdministrativoList', 'onShow']), 'Voltar');
        $buttonVoltar->setImage('fas:arrow-left #000000');
        $this->datagrid_form->addField($buttonVoltar);
        $headRight->add($buttonVoltar);

        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Processo administrativo', 'Tramitacoes']));
        }
        $container->add($panel);

        parent::add($container);
    }

    public function onSearch($param = null)
    {
        $data = $this->datagrid_form->getData();
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onReload($param = null)
    {
        try {
            TTransaction::open(self::$database);
            ProcessoAdministrativoSchemaHelper::ensureSchema();

            $repository = new TRepository(self::$activeRecord);
            $criteria = clone $this->filter_criteria;

            $criteria->setProperties($param);
            $criteria->setProperty('order', $param['order'] ?? 'data_tramitacao');
            $criteria->setProperty('direction', $param['direction'] ?? 'desc');
            $criteria->setProperty('limit', $this->limit);

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!empty($data->tipo_evento)) {
                $criteria->add(new TFilter('tipo_evento', 'like', "%{$data->tipo_evento}%"));
            }
            if (!empty($data->usuario_responsavel)) {
                $criteria->add(new TFilter('usuario_responsavel', 'like', "%{$data->usuario_responsavel}%"));
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

            return $objects;
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
                ProcessoAdministrativoSchemaHelper::ensureSchema();
                $object = new ProcessoAdministrativoTramitacao($param['key'], false);
                $processoId = $object->processo_administrativo_id;
                $object->delete();
                TTransaction::close();
                TSession::setValue(__CLASS__ . '_processo_administrativo_id', $processoId);
                TSession::setValue('processo_administrativo_tramitacao_processo_id', $processoId);
                $this->onReload($param);
                TToast::show('success', 'Registro excluido', 'topRight', 'far:check-circle');
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            $action = new TAction([$this, 'onDelete']);
            $action->setParameters($param);
            $action->setParameter('delete', 1);
            new TQuestion('Deseja realmente excluir?', $action);
        }
    }

    public function onShow($param = null)
    {
        if (!$this->loaded) {
            $this->onReload(func_get_arg(0));
        }
    }
}
