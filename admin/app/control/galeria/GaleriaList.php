<?php

class GaleriaList extends TPage
{
    private $datagrid;
    private $datagrid_form;
    private $pageNavigation;
    private $loaded;
    private static $database = 'minierp';
    private static $activeRecord = 'GaleriaItem';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Galeria';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $titulo = new TEntry('titulo');
        $tipo = new TCombo('tipo');
        $status = new TCombo('status');

        $tipo->addItems([
            '' => 'Todos',
            'foto' => 'Foto',
            'video' => 'Video',
            'audio' => 'Audio',
        ]);
        $status->addItems([
            '' => 'Todos',
            'published' => 'Publicado',
            'draft' => 'Rascunho',
        ]);

        $titulo->setSize('100%');
        $tipo->setSize('100%');
        $status->setSize('100%');

        $titulo->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $tipo->setChangeAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $status->setChangeAction(new TAction([$this, 'onSearch'], ['static' => '1']));

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', 'Id', 'center', '70px');
        $column_titulo = new TDataGridColumn('titulo', 'Titulo', 'left');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'center', '14%');
        $column_ordem = new TDataGridColumn('ordem', 'Ordem', 'center', '10%');
        $column_status = new TDataGridColumn('status', 'Status', 'center', '12%');

        $column_tipo->setTransformer(function ($value) {
            $labels = ['foto' => 'Foto', 'video' => 'Video', 'audio' => 'Audio'];
            return $labels[$value] ?? $value;
        });

        $column_status->setTransformer(function ($value) {
            return $value === 'published' ? 'Publicado' : 'Rascunho';
        });

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_titulo);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_ordem);
        $this->datagrid->addColumn($column_status);

        $actionEdit = new TDataGridAction(['GaleriaForm', 'onEdit']);
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
        $this->datagrid_form->addField($titulo);
        $this->datagrid_form->addField($tipo);
        $this->datagrid_form->addField($status);
        $this->datagrid_form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $titulo));
        $tr->add(TElement::tag('td', $tipo));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $status));
        $tr->add(TElement::tag('td', ''));

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Listagem da galeria');
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

        $buttonCadastrar = new TButton('button_cadastrar_galeria');
        $buttonCadastrar->setAction(new TAction(['GaleriaForm', 'onShow']), 'Cadastrar');
        $buttonCadastrar->setImage('fas:plus #69aa46');
        $this->datagrid_form->addField($buttonCadastrar);
        $headLeft->add($buttonCadastrar);

        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Comunicacao', 'Galeria']));
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
            GaleriaSchemaHelper::ensureSchema();

            $repository = new TRepository(self::$activeRecord);
            $criteria = new TCriteria;
            $criteria->setProperties($param);
            $criteria->setProperty('order', $param['order'] ?? 'ordem');
            $criteria->setProperty('direction', $param['direction'] ?? 'asc');
            $criteria->setProperty('limit', $this->limit);

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!empty($data->titulo)) {
                $criteria->add(new TFilter('titulo', 'like', "%{$data->titulo}%"));
            }
            if (!empty($data->tipo)) {
                $criteria->add(new TFilter('tipo', '=', $data->tipo));
            }
            if (!empty($data->status)) {
                $criteria->add(new TFilter('status', '=', $data->status));
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
            $transactionOpen = false;

            try {
                TTransaction::open(self::$database);
                $transactionOpen = true;
                GaleriaSchemaHelper::ensureSchema();
                $object = new GaleriaItem($param['key'], false);
                $object->delete();
                TTransaction::close();
                $transactionOpen = false;

                TToast::show('success', 'Registro excluido', 'topRight', 'far:check-circle');
            } catch (Exception $e) {
                if ($transactionOpen) {
                    TTransaction::rollback();
                }

                new TMessage('error', $e->getMessage());
                return;
            }

            $this->onReload($param);
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
