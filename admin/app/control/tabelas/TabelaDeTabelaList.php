<?php

class TabelaDeTabelaList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'TabelaDeTabela';
    private static $primaryKey = 'id';
    private static $formName = 'form_TabelaDeTabelaList';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Listagem de tabela de tabela');

        $id = new TEntry('id');
        $tabela_id = new TDBCombo('tabela_id', self::$database, 'Tabela', 'id', '{descricao}', 'descricao asc');
        $descricao = new TEntry('descricao');
        $cor = new TColor('cor');
        $id->setSize('100%');
        $tabela_id->setSize('100%');
        $descricao->setSize('100%');
        $cor->setSize('100%');
        $tabela_id->enableSearch();

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Tabela:', null, '14px', null, '100%'), $tabela_id]
        );
        $row1->layout = ['col-sm-3', 'col-sm-9'];

        $row2 = $this->form->addFields(
            [new TLabel('Descricao:', null, '14px', null, '100%'), $descricao],
            [new TLabel('Cor:', null, '14px', null, '100%'), $cor]
        );
        $row2->layout = ['col-sm-8', 'col-sm-4'];

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->disableHtmlConversion();

        $this->filter_criteria = new TCriteria;
        $this->filter_criteria->add(new TFilter('deleted_at', 'is', null));

        if (!empty($param['tabela_id'])) {
            TSession::setValue(__CLASS__ . '_tabela_id', $param['tabela_id']);
        }

        $tabelaId = TSession::getValue(__CLASS__ . '_tabela_id');
        if ($tabelaId) {
            $this->filter_criteria->add(new TFilter('tabela_id', '=', $tabelaId));
        }

        $filterData = TSession::getValue(__CLASS__ . '_filter_data');
        if ($tabelaId) {
            $filterData = $filterData ?: new stdClass;
            $filterData->tabela_id = $tabelaId;
        }

        $this->form->setData($filterData);

        $btnBuscar = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $btnBuscar->addStyleClass('btn-primary');

        $actionCadastrarForm = new TAction(['TabelaDeTabelaForm', 'onShow']);
        if ($tabelaId) {
            $actionCadastrarForm->setParameter('tabela_id', $tabelaId);
        }
        $this->form->addAction('Cadastrar', $actionCadastrarForm, 'fas:plus #69aa46');
        $this->form->addAction('Voltar', new TAction(['TabelaList', 'onShow']), 'fas:arrow-left #000000');

        $this->datagrid->addColumn(new TDataGridColumn('id', 'Id', 'center', '80px'));
        $this->datagrid->addColumn(new TDataGridColumn('tabela->descricao', 'Tabela', 'left', '25%'));
        $this->datagrid->addColumn(new TDataGridColumn('descricao', 'Descricao', 'left'));

        $column_cor = new TDataGridColumn('cor', 'Cor', 'center', '120px');
        $column_cor->setTransformer(function ($value) {
            if (empty($value)) {
                return '';
            }

            return '<span style="display:inline-flex;align-items:center;gap:6px;"><span style="width:18px;height:18px;border-radius:3px;border:1px solid #ccc;background:' . $value . ';display:inline-block;"></span>' . $value . '</span>';
        });
        $this->datagrid->addColumn($column_cor);

        $actionEdit = new TDataGridAction(['TabelaDeTabelaForm', 'onEdit']);
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

        $this->datagrid_form = new TForm('datagrid_' . self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Tabela de tabela');
        $panel->datagrid = 'datagrid-container';
        $panel->getBody()->class .= ' table-responsive';
        $panel->addFooter($this->pageNavigation);

        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Configuracoes', 'Tabela de tabela']));
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
            $criteria = clone $this->filter_criteria;
            $criteria->setProperties($param);
            $criteria->setProperty('order', $param['order'] ?? 'descricao');
            $criteria->setProperty('direction', $param['direction'] ?? 'asc');
            $criteria->setProperty('limit', $this->limit);

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!empty($data->id)) {
                $criteria->add(new TFilter('id', '=', (int) $data->id));
            }
            if (!empty($data->tabela_id)) {
                $criteria->add(new TFilter('tabela_id', '=', (int) $data->tabela_id));
            }
            if (!empty($data->descricao)) {
                $criteria->add(new TFilter('descricao', 'like', "%{$data->descricao}%"));
            }
            if (!empty($data->cor)) {
                $criteria->add(new TFilter('cor', 'like', "%{$data->cor}%"));
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
                $object = new TabelaDeTabela($param['key'], false);
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
