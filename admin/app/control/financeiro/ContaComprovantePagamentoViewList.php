<?php

class ContaComprovantePagamentoViewList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'ContaAnexo';
    private static $formName = 'formList_ContaComprovantePagamentoView';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $contaId = (int) ($param['conta_id'] ?? $param['key'] ?? $param['id'] ?? 0);
        $this->validarAcessoConta($contaId);

        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__ . '_datagrid');

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;
        $this->filter_criteria->add(new TFilter('conta_id', '=', $contaId));
        $this->filter_criteria->add(new TFilter('deleted_at', 'is', null));

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(260);

        $column_conta = new TDataGridColumn('conta_id', 'Conta', 'center', '70px');
        $column_pedido = new TDataGridColumn('conta->pedido_frotas_id', 'Pedido', 'center', '80px');
        $column_tipo = new TDataGridColumn('tipo_anexo->nome', 'Tipo', 'left', '120px');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_created_at = new TDataGridColumn('created_at', 'Criado em', 'center', '120px');
        $column_arquivo = new TDataGridColumn('arquivo', 'Arquivo', 'left');

        $column_created_at->setTransformer(function ($value) {
            return !empty($value) ? TDateTime::convertToMask($value, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii') : '';
        });

        $column_arquivo->setTransformer(function ($value) {
            if (empty($value)) {
                return '';
            }

            $files = explode(',', $value);
            $divFiles = new TElement('div');

            foreach ($files as $file) {
                $fileName = trim($file);
                if ($fileName === '') {
                    continue;
                }

                if (strpos($fileName, '%7B') !== false) {
                    $fileObject = json_decode(urldecode($fileName));
                    if (!empty($fileObject->fileName)) {
                        $fileName = $fileObject->fileName;
                    }
                }

                $a = new TElement('a');
                $a->href = 'download.php?file=' . rawurlencode($fileName);
                $a->class = 'btn btn-link';
                $a->target = '_blank';
                $a->onclick = 'event.stopPropagation();';
                $a->add(basename($fileName));

                $divFiles->add($a);
                $divFiles->add('<br>');
            }

            return $divFiles;
        });

        $this->datagrid->addColumn($column_conta);
        $this->datagrid->addColumn($column_pedido);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_created_at);
        $this->datagrid->addColumn($column_arquivo);

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload'], ['conta_id' => $contaId]));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Comprovantes da conta');
        $panel->datagrid = 'datagrid-container';
        $panel->getBody()->class .= ' table-responsive';
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);
        $panel->addFooter($this->pageNavigation);

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($panel);

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = 'Template.closeRightPanel();';
        $btnClose->setLabel('Fechar');
        $btnClose->setImage('fas:times');

        $panel->addHeaderWidget($btnClose);

        parent::add($container);
    }

    public function onReload($param = null)
    {
        try {
            TTransaction::open(self::$database);

            $repository = new TRepository(self::$activeRecord);
            $criteria = clone $this->filter_criteria;

            if (empty($param['order'])) {
                $param['order'] = 'id';
            }
            if (empty($param['direction'])) {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param);
            $criteria->setProperty('limit', $this->limit);

            $objects = $repository->load($criteria, false);
            $this->datagrid->clear();

            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }

            $criteria->resetProperties();
            $this->pageNavigation->setCount($repository->count($criteria));
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

    public function onShow($param = null)
    {
    }

    public function show()
    {
        if (!$this->loaded) {
            $this->onReload();
        }

        parent::show();
    }

    private function validarAcessoConta(int $contaId): void
    {
        if ($contaId <= 0) {
            throw new Exception('Conta nao informada.');
        }

        TTransaction::open(self::$database);

        $conta = new Conta($contaId);
        $grupoAdmin = SystemUserGroup::where('system_user_id', '=', TSession::getValue('userid'))
                                    ->where('system_group_id', '=', 1)
                                    ->first();

        if (!$grupoAdmin) {
            $pessoa = Pessoa::where('system_user_id', '=', TSession::getValue('userid'))->first();
            if (!$pessoa || (int) $conta->pessoa_id !== (int) $pessoa->id) {
                TTransaction::close();
                throw new Exception('Voce nao tem permissao para ver este comprovante.');
            }
        }

        TTransaction::close();
    }
}
