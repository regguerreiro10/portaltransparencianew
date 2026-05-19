<?php

class ProdutoPrecoVeiculoTempariaList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private static $database = 'minierp';
    private static $activeRecord = 'ProdutoPrecoVeiculo';
    private static $primaryKey = 'id';
    private static $formName = 'form_ProdutoPrecoVeiculoTempariaList';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Tabela Temparia do Veiculo');

        $criteria_familia_produto_id = new TCriteria();
        $criteria_tipo_produto_id = new TCriteria();

        $produto_nome = new TEntry('produto_nome');
        $familia_produto_id = new TDBCombo('familia_produto_id', self::$database, 'FamiliaProduto', 'id', '{nome}', 'nome asc', $criteria_familia_produto_id);
        $tipo_produto_id = new TDBCombo('tipo_produto_id', self::$database, 'TipoProduto', 'id', '{nome}', 'nome asc', $criteria_tipo_produto_id);

        $produto_nome->setSize('100%');
        $familia_produto_id->setSize('100%');
        $tipo_produto_id->setSize('100%');
        $familia_produto_id->enableSearch();
        $tipo_produto_id->enableSearch();

        $row1 = $this->form->addFields(
            [new TLabel('Grupo:', null, '14px', null, '100%'), $familia_produto_id],
            [new TLabel('Tipo do produto:', null, '14px', null, '100%'), $tipo_produto_id]
        );
        $row1->layout = ['col-sm-6', 'col-sm-6'];

        $row2 = $this->form->addFields(
            [new TLabel('Produto:', null, '14px', null, '100%'), $produto_nome]
        );
        $row2->layout = ['col-sm-12'];

        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $btn_onsearch = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $btn_onsearch->addStyleClass('btn-primary');

        $btn_clear = $this->form->addAction('Limpar filtros', new TAction([$this, 'onClearFilters']), 'fas:eraser #dd5a43');
        $btn_clear->addStyleClass('btn-default');

        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__ . '_datagrid');

        $this->datagrid_form = new TForm('datagrid_' . self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_grupo = new TDataGridColumn('produto->familia_produto->nome', 'Grupo', 'left');
        $column_tipo = new TDataGridColumn('produto->tipo_produto->nome', 'Tipo do produto', 'left');
        $column_produto = new TDataGridColumn('produto->nome', 'Produto', 'left');
        $column_valor = new TDataGridColumn('suiv_preco_peca', 'Valor tabela temparia', 'right', '180px');

        $column_valor->setTransformer(function($value, $object) {
            $valor = (float) ($object->suiv_preco_peca ?? 0);
            if ($valor <= 0) {
                $valor = (float) ($object->preco_venda ?? 0);
            }

            return 'R$ ' . number_format($valor, 2, ',', '.');
        });

        $this->datagrid->addColumn($column_grupo);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_produto);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);
        $panel->getBody()->class .= ' table-responsive';
        $panel->addFooter($this->pageNavigation);

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);
    }

    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        if (!empty($data->produto_nome))
        {
            $produto_nome = addslashes(trim($data->produto_nome));
            $filters[] = new TFilter('produto_id', 'in', "(SELECT id FROM produto WHERE nome LIKE '%{$produto_nome}%')");
        }

        if (!empty($data->familia_produto_id))
        {
            $familia_produto_id = (int) $data->familia_produto_id;
            $filters[] = new TFilter('produto_id', 'in', "(SELECT id FROM produto WHERE familia_produto_id = {$familia_produto_id})");
        }

        if (!empty($data->tipo_produto_id))
        {
            $tipo_produto_id = (int) $data->tipo_produto_id;
            $filters[] = new TFilter('produto_id', 'in', "(SELECT id FROM produto WHERE tipo_produto_id = {$tipo_produto_id})");
        }

        TSession::setValue(__CLASS__ . '_filters', $filters);
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        $this->form->setData($data);

        $param['offset'] = 0;
        $this->onReload($param);
    }

    public function onClearFilters($param = null)
    {
        TSession::setValue(__CLASS__ . '_filters', []);
        TSession::setValue(__CLASS__ . '_filter_data', null);
        $this->form->clear();
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onReload($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $repository = new TRepository(self::$activeRecord);
            $criteria = new TCriteria;

            $veiculos_id = $param['veiculos_id'] ?? TSession::getValue(__CLASS__ . '_veiculos_id');
            TSession::setValue(__CLASS__ . '_veiculos_id', $veiculos_id);

            if (!empty($veiculos_id))
            {
                $criteria->add(new TFilter('veiculos_id', '=', (int) $veiculos_id));
            }

            $criteria->add(new TFilter('produto_id', 'is not', null));

            if ($filters = TSession::getValue(__CLASS__ . '_filters'))
            {
                foreach ($filters as $filter)
                {
                    $criteria->add($filter);
                }
            }

            if (empty($param['order']))
            {
                $param['order'] = 'id';
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param);
            $criteria->setProperty('limit', $this->limit);

            $objects = $repository->load($criteria, false);

            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $row = $this->datagrid->addItem($object);
                    $row->id = 'row_' . $object->id;
                }
            }

            $criteria->resetProperties();
            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($this->limit);

            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {
    }

    public function show()
    {
        if (!$this->loaded && (!isset($_GET['method']) || !(in_array($_GET['method'], ['onReload', 'onSearch']))))
        {
            if (func_num_args() > 0)
            {
                $this->onReload(func_get_arg(0));
            }
            else
            {
                $this->onReload();
            }
        }

        parent::show();
    }
}
