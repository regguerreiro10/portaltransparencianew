<?php

class ProcessoLegislativoList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private static $database = 'minierp';
    private static $activeRecord = 'ProcessoLegislativo';
    private static $primaryKey = 'id';
    private static $formName = 'formList_ProcessoLegislativo';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $numero_processo = new TEntry('numero_processo');
        $numero_protocolo = new TEntry('numero_protocolo');
        $ementa = new TEntry('ementa');
        $autor_principal = new TEntry('autor_principal');
        $situacao_status = new TCombo('situacao_status');

        $situacao_status->addItems([
            '' => 'Todos',
            'Protocolado' => 'Protocolado',
            'Em analise' => 'Em analise',
            'Em pauta' => 'Em pauta',
            'Apreciado' => 'Apreciado',
            'Arquivado' => 'Arquivado',
        ]);

        $numero_processo->setSize('100%');
        $numero_protocolo->setSize('100%');
        $ementa->setSize('100%');
        $autor_principal->setSize('100%');
        $situacao_status->setSize('100%');
        $situacao_status->enableSearch();

        $numero_processo->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $numero_protocolo->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $ementa->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $autor_principal->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $situacao_status->setChangeAction(new TAction([$this, 'onSearch'], ['static' => '1']));

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $this->datagrid->addColumn(new TDataGridColumn('id', 'Id', 'center', '60px'));
        $this->datagrid->addColumn(new TDataGridColumn('numero_processo', 'Processo', 'left', '12%'));
        $this->datagrid->addColumn(new TDataGridColumn('numero_protocolo', 'Protocolo', 'left', '14%'));
        $this->datagrid->addColumn(new TDataGridColumn('tipo_processo', 'Tipo', 'left', '14%'));
        $this->datagrid->addColumn(new TDataGridColumn('ementa', 'Ementa', 'left'));
        $this->datagrid->addColumn(new TDataGridColumn('autor_principal', 'Autor principal', 'left', '16%'));
        $this->datagrid->addColumn(new TDataGridColumn('situacao_status', 'Situacao', 'center', '12%'));

        $actionEdit = new TDataGridAction(['ProcessoLegislativoForm', 'onEdit']);
        $actionEdit->setLabel('Editar');
        $actionEdit->setImage('far:edit #478fca');
        $actionEdit->setUseButton(false);
        $actionEdit->setButtonClass('btn btn-default btn-sm');
        $actionEdit->setField(self::$primaryKey);
        $this->datagrid->addAction($actionEdit);

        $actionTramitacoes = new TDataGridAction(['ProcessoLegislativoTramitacaoList', 'onShow']);
        $actionTramitacoes->setLabel('Tramitacoes');
        $actionTramitacoes->setImage('fas:history #0f4c81');
        $actionTramitacoes->setUseButton(false);
        $actionTramitacoes->setButtonClass('btn btn-default btn-sm');
        $actionTramitacoes->setField(self::$primaryKey);
        $actionTramitacoes->setParameter('processo_legislativo_id', '{id}');
        $this->datagrid->addAction($actionTramitacoes);

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
        $this->datagrid_form->addField($numero_processo);
        $this->datagrid_form->addField($numero_protocolo);
        $this->datagrid_form->addField($ementa);
        $this->datagrid_form->addField($autor_principal);
        $this->datagrid_form->addField($situacao_status);
        $this->datagrid_form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $numero_processo));
        $tr->add(TElement::tag('td', $numero_protocolo));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $ementa));
        $tr->add(TElement::tag('td', $autor_principal));
        $tr->add(TElement::tag('td', $situacao_status));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Processos legislativos');
        $panel->datagrid = 'datagrid-container';
        $panel->getBody()->class .= ' table-responsive';
        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headLeft = new TElement('div');
        $headLeft->class = ' datagrid-header-actions-left-actions ';
        $headerActions->add($headLeft);
        $this->datagrid_form->add($headerActions);

        $buttonCadastrar = new TButton('button_cadastrar_processo_legislativo');
        $buttonCadastrar->setAction(new TAction(['ProcessoLegislativoForm', 'onShow']), 'Cadastrar');
        $buttonCadastrar->setImage('fas:plus #69aa46');
        $this->datagrid_form->addField($buttonCadastrar);
        $headLeft->add($buttonCadastrar);

        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Processo legislativo', 'Cadastro']));
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
            ProcessoLegislativoSchemaHelper::ensureSchema();

            $repository = new TRepository(self::$activeRecord);
            $criteria = new TCriteria;

            $criteria->setProperties($param);
            $criteria->setProperty('order', $param['order'] ?? 'id');
            $criteria->setProperty('direction', $param['direction'] ?? 'desc');
            $criteria->setProperty('limit', $this->limit);

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!empty($data->numero_processo)) {
                $criteria->add(new TFilter('numero_processo', 'like', "%{$data->numero_processo}%"));
            }
            if (!empty($data->numero_protocolo)) {
                $criteria->add(new TFilter('numero_protocolo', 'like', "%{$data->numero_protocolo}%"));
            }
            if (!empty($data->ementa)) {
                $criteria->add(new TFilter('ementa', 'like', "%{$data->ementa}%"));
            }
            if (!empty($data->autor_principal)) {
                $criteria->add(new TFilter('autor_principal', 'like', "%{$data->autor_principal}%"));
            }
            if (!empty($data->situacao_status)) {
                $criteria->add(new TFilter('situacao_status', '=', $data->situacao_status));
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
                ProcessoLegislativoSchemaHelper::ensureSchema();
                $object = new ProcessoLegislativo($param['key'], false);
                $object->delete();
                TTransaction::close();
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
