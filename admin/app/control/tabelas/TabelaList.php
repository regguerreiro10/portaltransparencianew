<?php

class TabelaList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private static $database = 'minierp';
    private static $activeRecord = 'Tabela';
    private static $primaryKey = 'id';
    private static $formName = 'form_TabelaList';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Listagem de tabelas');

        $id = new TEntry('id');
        $descricao = new TEntry('descricao');
        $id->setSize('100%');
        $descricao->setSize('100%');

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Descricao:', null, '14px', null, '100%'), $descricao]
        );
        $row1->layout = ['col-sm-3', 'col-sm-9'];

        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $btnBuscar = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $btnBuscar->addStyleClass('btn-primary');
        $this->form->addAction('Cadastrar', new TAction(['TabelaForm', 'onShow']), 'fas:plus #69aa46');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $this->datagrid->addColumn(new TDataGridColumn('id', 'Id', 'center', '80px'));
        $this->datagrid->addColumn(new TDataGridColumn('descricao', 'Descricao', 'left'));

        $actionEdit = new TDataGridAction(['TabelaForm', 'onEdit']);
        $actionEdit->setLabel('Editar');
        $actionEdit->setImage('far:edit #478fca');
        $actionEdit->setUseButton(false);
        $actionEdit->setButtonClass('btn btn-default btn-sm');
        $actionEdit->setField(self::$primaryKey);
        $this->datagrid->addAction($actionEdit);

        $actionItems = new TDataGridAction(['TabelaDeTabelaList', 'onShow']);
        $actionItems->setLabel('Itens');
        $actionItems->setImage('fas:list #0f4c81');
        $actionItems->setUseButton(false);
        $actionItems->setButtonClass('btn btn-default btn-sm');
        $actionItems->setField(self::$primaryKey);
        $actionItems->setParameter('tabela_id', '{id}');
        $this->datagrid->addAction($actionItems);

        $actionDelete = new TDataGridAction([$this, 'onDelete']);
        $actionDelete->setLabel('Excluir');
        $actionDelete->setImage('fas:trash-alt #dd5a43');
        $actionDelete->setUseButton(false);
        $actionDelete->setButtonClass('btn btn-default btn-sm');
        $actionDelete->setField(self::$primaryKey);
        $this->datagrid->addAction($actionDelete);

        $this->datagrid->createModel();

        $this->datagrid_form = new TForm('datagrid_' . self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Tabelas');
        $panel->datagrid = 'datagrid-container';
        $panel->getBody()->class .= ' table-responsive';
        $panel->addFooter($this->pageNavigation);

        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Configuracoes', 'Tabelas']));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);
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
            TabelaSchemaHelper::ensureSchema();

            $repository = new TRepository(self::$activeRecord);
            $criteria = new TCriteria;
            $criteria->setProperties($param);
            $criteria->setProperty('order', $param['order'] ?? 'descricao');
            $criteria->setProperty('direction', $param['direction'] ?? 'asc');
            $criteria->setProperty('limit', $this->limit);
            $criteria->add(new TFilter('deleted_at', 'is', null));

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!empty($data->id)) {
                $criteria->add(new TFilter('id', '=', (int) $data->id));
            }
            if (!empty($data->descricao)) {
                $criteria->add(new TFilter('descricao', 'like', "%{$data->descricao}%"));
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
                TabelaSchemaHelper::ensureSchema();
                $object = new Tabela($param['key'], false);
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
