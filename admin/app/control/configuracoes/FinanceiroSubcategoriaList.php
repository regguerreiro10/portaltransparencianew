<?php

class FinanceiroSubcategoriaList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'FinanceiroSubcategoria';
    private static $primaryKey = 'id';
    private static $formName = 'formList_FinanceiroSubcategoria';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $nome = new TEntry('nome');
        $ano = new TEntry('ano');
        $visivel = new TCombo('visivel');
        $visivel->addItems(['' => 'Todos', 'Y' => 'Sim', 'N' => 'Nao']);

        $nome->setSize('100%');
        $ano->setSize('100%');
        $visivel->setSize('100%');
        $visivel->enableSearch();

        $nome->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $ano->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $visivel->setChangeAction(new TAction([$this, 'onSearch'], ['static' => '1']));

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $this->filter_criteria = new TCriteria;

        if (!empty($param['financeiro_cadastro_id'])) {
            TSession::setValue(__CLASS__ . '_financeiro_cadastro_id', $param['financeiro_cadastro_id']);
        }

        $financeiroCadastroId = TSession::getValue(__CLASS__ . '_financeiro_cadastro_id');
        if ($financeiroCadastroId) {
            $this->filter_criteria->add(new TFilter('financeiro_cadastro_id', '=', $financeiroCadastroId));
        }

        $column_nome = new TDataGridColumn('nome', 'Subcategoria', 'left');
        $column_cadastro = new TDataGridColumn('financeiro_cadastro->nome', 'Cadastro', 'left', '25%');
        $column_categoria = new TDataGridColumn('categoria->nome', 'Categoria', 'left', '20%');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'center', '10%');
        $column_visivel = new TDataGridColumn('visivel', 'Visivel', 'center', '10%');

        $column_visivel->setTransformer(function ($value) {
            return $value === 'Y' ? 'Sim' : 'Nao';
        });

        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cadastro);
        $this->datagrid->addColumn($column_categoria);
        $this->datagrid->addColumn($column_ano);
        $this->datagrid->addColumn($column_visivel);

        $actionEdit = new TDataGridAction(['FinanceiroSubcategoriaForm', 'onEdit']);
        $actionEdit->setLabel('Editar');
        $actionEdit->setImage('far:edit #478fca');
        $actionEdit->setUseButton(false);
        $actionEdit->setButtonClass('btn btn-default btn-sm');
        $actionEdit->setField(self::$primaryKey);
        $this->datagrid->addAction($actionEdit);

        $actionFiles = new TDataGridAction(['FinanceiroArquivoList', 'onShow']);
        $actionFiles->setLabel('Arquivos');
        $actionFiles->setImage('fas:paperclip #0f4c81');
        $actionFiles->setUseButton(false);
        $actionFiles->setButtonClass('btn btn-default btn-sm');
        $actionFiles->setField(self::$primaryKey);
        $actionFiles->setParameter('subcategoria_id', '{id}');
        $this->datagrid->addAction($actionFiles);

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
        $this->datagrid_form->addField($nome);
        $this->datagrid_form->addField($ano);
        $this->datagrid_form->addField($visivel);
        $this->datagrid_form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $nome));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $ano));
        $tr->add(TElement::tag('td', $visivel));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Subcategorias financeiras');
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

        $buttonCadastrar = new TButton('button_cadastrar_financeiro_subcategoria');
        $actionCadastrar = new TAction(['FinanceiroSubcategoriaForm', 'onShow']);
        if ($financeiroCadastroId) {
            $actionCadastrar->setParameter('financeiro_cadastro_id', $financeiroCadastroId);
        }
        $buttonCadastrar->setAction($actionCadastrar, 'Cadastrar');
        $buttonCadastrar->setImage('fas:plus #69aa46');
        $this->datagrid_form->addField($buttonCadastrar);
        $headLeft->add($buttonCadastrar);

        $buttonVoltar = new TButton('button_voltar_financeiro_cadastro');
        $buttonVoltar->setAction(new TAction(['FinanceiroCadastroList', 'onShow']), 'Voltar');
        $buttonVoltar->setImage('fas:arrow-left #000000');
        $this->datagrid_form->addField($buttonVoltar);
        $headRight->add($buttonVoltar);

        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Modulo Financeiro', 'Subcategorias']));
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
            FinanceiroPublicoSchemaHelper::ensureSchema();

            $repository = new TRepository(self::$activeRecord);
            $criteria = clone $this->filter_criteria;
            $criteria->setProperties($param);
            $criteria->setProperty('order', $param['order'] ?? 'ano');
            $criteria->setProperty('direction', $param['direction'] ?? 'desc');
            $criteria->setProperty('limit', $this->limit);

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!empty($data->nome)) {
                $criteria->add(new TFilter('nome', 'like', "%{$data->nome}%"));
            }
            if (!empty($data->ano)) {
                $criteria->add(new TFilter('ano', '=', (int) $data->ano));
            }
            if (!empty($data->visivel)) {
                $criteria->add(new TFilter('visivel', '=', $data->visivel));
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
                FinanceiroPublicoSchemaHelper::ensureSchema();
                $object = new FinanceiroSubcategoria($param['key'], false);
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
