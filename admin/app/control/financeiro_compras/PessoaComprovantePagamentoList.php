<?php

class PessoaComprovantePagamentoList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'ContaAnexo';
    private static $formName = 'formList_PessoaComprovantePagamento';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__ . '_datagrid');

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        if (!empty($param['pessoa_id'])) {
            TSession::setValue(__CLASS__ . 'load_filter_pessoa_id', (int) $param['pessoa_id']);
        }

        $pessoaId = (int) TSession::getValue(__CLASS__ . 'load_filter_pessoa_id');
        $this->filter_criteria->add(new TFilter('conta_id', 'in', "(SELECT id FROM conta WHERE pessoa_id = {$pessoaId} AND tipo_conta_id = " . TipoConta::PAGAR . ")"));
        $this->filter_criteria->add(new TFilter('deleted_at', 'is', null));

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_conta = new TDataGridColumn('contas', 'Contas', 'left', '18%');
        $column_pedido = new TDataGridColumn('pedidos', 'Pedidos', 'left', '18%');
        $column_tipo = new TDataGridColumn('tipo_nome', 'Tipo', 'left', '14%');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_created_at = new TDataGridColumn('created_at_formatado', 'Criado em', 'center', '120px');
        $column_arquivo = new TDataGridColumn('arquivo', 'Arquivo', 'left');

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

        $actionExcluir = new TDataGridAction([$this, 'onDeleteGrupo']);
        $actionExcluir->setUseButton(false);
        $actionExcluir->setButtonClass('btn btn-default btn-sm');
        $actionExcluir->setLabel('Excluir');
        $actionExcluir->setImage('fas:trash-alt #dd5a43');
        $actionExcluir->setField('grupo_id');

        $this->datagrid->addAction($actionExcluir);

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Comprovantes de pagamento');
        $panel->datagrid = 'datagrid-container';
        $panel->getBody()->class .= ' table-responsive';
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);
        $panel->addFooter($this->pageNavigation);

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($panel);

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
            $objects = $this->agruparComprovantes($objects);
            $this->datagrid->clear();

            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }

            $this->pageNavigation->setCount(count($objects ?? []));
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

    private function agruparComprovantes($objects): array
    {
        if (!$objects) {
            return [];
        }

        $grupos = [];

        foreach ($objects as $object) {
            $arquivo = trim((string) ($object->arquivo ?? ''));
            $tipoId = (int) ($object->tipo_anexo_id ?? 0);
            $descricao = trim((string) ($object->descricao ?? ''));
            $chave = md5($arquivo . '|' . $tipoId . '|' . $descricao);

            if (empty($grupos[$chave])) {
                $linha = new stdClass();
                $linha->arquivo = $arquivo;
                $linha->descricao = $descricao;
                $linha->tipo_nome = $object->tipo_anexo->nome ?? '';
                $linha->created_at = $object->created_at ?? null;
                $linha->grupo_id = $chave;
                $linha->anexos_lista = [];
                $linha->contas_lista = [];
                $linha->pedidos_lista = [];
                $grupos[$chave] = $linha;
            }

            $anexoId = (int) ($object->id ?? 0);
            if ($anexoId > 0) {
                $grupos[$chave]->anexos_lista[$anexoId] = $anexoId;
            }

            if (!empty($object->created_at) && (empty($grupos[$chave]->created_at) || $object->created_at < $grupos[$chave]->created_at)) {
                $grupos[$chave]->created_at = $object->created_at;
            }

            $contaId = (int) ($object->conta_id ?? 0);
            if ($contaId > 0) {
                $grupos[$chave]->contas_lista[$contaId] = $contaId;
            }

            $pedidoId = (int) ($object->conta->pedido_frotas_id ?? 0);
            if ($pedidoId > 0) {
                $grupos[$chave]->pedidos_lista[$pedidoId] = $pedidoId;
            }
        }

        foreach ($grupos as $linha) {
            ksort($linha->contas_lista);
            ksort($linha->pedidos_lista);
            $linha->contas = implode(', ', $linha->contas_lista);
            $linha->pedidos = implode(', ', $linha->pedidos_lista);
            $linha->created_at_formatado = !empty($linha->created_at)
                ? TDateTime::convertToMask($linha->created_at, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii')
                : '';
            TSession::setValue(__CLASS__ . '_grupo_' . $linha->grupo_id, array_values($linha->anexos_lista));
            unset($linha->contas_lista, $linha->pedidos_lista, $linha->anexos_lista);
        }

        return array_values($grupos);
    }

    public function onDeleteGrupo($param = null)
    {
        if (empty($param['delete'])) {
            $action = new TAction([$this, 'onDeleteGrupo']);
            $action->setParameters($param);
            $action->setParameter('delete', 1);

            new TQuestion('Deseja excluir este comprovante de todos os pedidos/contas vinculados?', $action);
            return;
        }

        try {
            $grupoId = $param['key'] ?? $param['grupo_id'] ?? null;
            $anexoIds = TSession::getValue(__CLASS__ . '_grupo_' . $grupoId) ?: [];
            $anexoIds = array_values(array_unique(array_map('intval', $anexoIds)));

            if (empty($anexoIds)) {
                throw new Exception('Comprovante nao localizado para exclusao.');
            }

            TTransaction::open(self::$database);

            foreach ($anexoIds as $anexoId) {
                $anexo = new ContaAnexo($anexoId);
                $anexo->deleted_at = date('Y-m-d H:i:s');
                $anexo->updated_at = date('Y-m-d H:i:s');
                $anexo->store();
            }

            TTransaction::close();

            TToast::show('success', 'Comprovante excluido.', 'topRight', 'far:check-circle');
            $this->onReload();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function show()
    {
        if (!$this->loaded) {
            $this->onReload();
        }

        parent::show();
    }
}
