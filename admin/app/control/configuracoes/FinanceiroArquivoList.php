<?php

class FinanceiroArquivoList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'FinanceiroArquivo';
    private static $primaryKey = 'id';
    private static $formName = 'formList_FinanceiroArquivo';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $nome_arquivo = new TEntry('nome_arquivo');
        $tipo = new TCombo('tipo');
        $tipo->addItems(['' => 'Todos', 'arquivo' => 'Arquivo', 'link' => 'Link externo']);

        $nome_arquivo->setSize('100%');
        $tipo->setSize('100%');
        $tipo->enableSearch();
        $nome_arquivo->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $tipo->setChangeAction(new TAction([$this, 'onSearch'], ['static' => '1']));

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $this->filter_criteria = new TCriteria;

        if (!empty($param['financeiro_cadastro_id'])) {
            TSession::setValue(__CLASS__ . '_financeiro_cadastro_id', $param['financeiro_cadastro_id']);
        }
        if (!empty($param['subcategoria_id'])) {
            TSession::setValue(__CLASS__ . '_subcategoria_id', $param['subcategoria_id']);
        }

        $financeiroCadastroId = TSession::getValue(__CLASS__ . '_financeiro_cadastro_id');
        $subcategoriaId = TSession::getValue(__CLASS__ . '_subcategoria_id');

        if ($subcategoriaId) {
            $this->filter_criteria->add(new TFilter('subcategoria_id', '=', $subcategoriaId));
        } elseif ($financeiroCadastroId) {
            $this->filter_criteria->add(new TFilter('financeiro_cadastro_id', '=', $financeiroCadastroId));
        }

        $column_nome = new TDataGridColumn('nome_arquivo', 'Nome do arquivo', 'left');
        $column_cadastro = new TDataGridColumn('financeiro_cadastro->nome', 'Cadastro', 'left', '20%');
        $column_subcategoria = new TDataGridColumn('subcategoria->nome', 'Subcategoria', 'left', '18%');
        $column_tipo = new TDataGridColumn('tipo', 'Origem', 'center', '10%');
        $column_referencia = new TDataGridColumn('extensao', 'Tipo', 'center', '10%');

        $column_tipo->setTransformer(function ($value) {
            return $value === 'link' ? 'Link externo' : 'Arquivo';
        });
        $column_referencia->setTransformer(function ($value) {
            $value = trim((string) $value);
            return $value !== '' ? strtoupper($value) : '-';
        });

        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cadastro);
        $this->datagrid->addColumn($column_subcategoria);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_referencia);

        $actionEdit = new TDataGridAction(['FinanceiroArquivoForm', 'onEdit']);
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
        $this->datagrid_form->addField($nome_arquivo);
        $this->datagrid_form->addField($tipo);
        $this->datagrid_form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $nome_arquivo));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $tipo));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Arquivos financeiros');
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

        $buttonCadastrar = new TButton('button_cadastrar_financeiro_arquivo');
        $actionCadastrar = new TAction(['FinanceiroArquivoForm', 'onShow']);
        if ($financeiroCadastroId) {
            $actionCadastrar->setParameter('financeiro_cadastro_id', $financeiroCadastroId);
        }
        if ($subcategoriaId) {
            $actionCadastrar->setParameter('subcategoria_id', $subcategoriaId);
        }
        $buttonCadastrar->setAction($actionCadastrar, 'Cadastrar');
        $buttonCadastrar->setImage('fas:plus #69aa46');
        $this->datagrid_form->addField($buttonCadastrar);
        $headLeft->add($buttonCadastrar);

        $buttonVoltar = new TButton('button_voltar_financeiro_arquivo');
        $buttonVoltar->setAction(new TAction(['FinanceiroCadastroList', 'onShow']), 'Voltar');
        $buttonVoltar->setImage('fas:arrow-left #000000');
        $this->datagrid_form->addField($buttonVoltar);
        $headRight->add($buttonVoltar);

        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Modulo Financeiro', 'Arquivos']));
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
            $criteria->setProperty('order', $param['order'] ?? 'id');
            $criteria->setProperty('direction', $param['direction'] ?? 'desc');
            $criteria->setProperty('limit', $this->limit);

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!empty($data->nome_arquivo)) {
                $criteria->add(new TFilter('nome_arquivo', 'like', "%{$data->nome_arquivo}%"));
            }
            if (!empty($data->tipo)) {
                $criteria->add(new TFilter('tipo', '=', $data->tipo));
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
                $object = new FinanceiroArquivo($param['key'], false);
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
