<?php

class DocumentoPublicoAnexoList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'DocumentoPublicoAnexo';
    private static $primaryKey = 'id';
    private static $formName = 'formList_DocumentoPublicoAnexo';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $nome = new TEntry('nome');
        $nome->setSize('100%');
        $nome->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $this->filter_criteria = new TCriteria;

        if (!empty($param['documento_publico_id'])) {
            TSession::setValue(__CLASS__ . '_documento_publico_id', $param['documento_publico_id']);
        }

        $documentoId = TSession::getValue(__CLASS__ . '_documento_publico_id');
        TSession::setValue('documento_publico_anexo_documento_id', $documentoId);

        if ($documentoId) {
            $this->filter_criteria->add(new TFilter('documento_publico_id', '=', $documentoId));
        }

        $column_nome = new TDataGridColumn('nome', 'Nome do anexo', 'left');
        $column_arquivo = new TDataGridColumn('arquivo', 'Arquivo', 'left', '35%');
        $column_ordem = new TDataGridColumn('ordem', 'Ordem', 'center', '10%');

        $column_arquivo->setTransformer(function ($value) {
            if (empty($value)) {
                return '';
            }

            $href = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars(basename($value), ENT_QUOTES, 'UTF-8');

            return "<a href=\"{$href}\" target=\"_blank\"><i class=\"fas fa-external-link-alt\"></i> {$label}</a>";
        });

        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_arquivo);
        $this->datagrid->addColumn($column_ordem);

        $actionEdit = new TDataGridAction(['DocumentoPublicoAnexoForm', 'onEdit']);
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
        $this->datagrid_form->addField($nome);
        $this->datagrid_form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $nome));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Anexos do documento');
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

        $buttonCadastrar = new TButton('button_cadastrar_documento_publico_anexo');
        $actionCadastrar = new TAction(['DocumentoPublicoAnexoForm', 'onShow']);
        $actionCadastrar->setParameter('documento_publico_id', $documentoId);
        $buttonCadastrar->setAction($actionCadastrar, 'Cadastrar');
        $buttonCadastrar->setImage('fas:plus #69aa46');
        $this->datagrid_form->addField($buttonCadastrar);
        $headLeft->add($buttonCadastrar);

        $buttonVoltar = new TButton('button_voltar_documento_publico');
        $buttonVoltar->setAction(new TAction(['DocumentoPublicoList', 'onShow']), 'Voltar');
        $buttonVoltar->setImage('fas:arrow-left #000000');
        $this->datagrid_form->addField($buttonVoltar);
        $headRight->add($buttonVoltar);

        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Comunicacao', 'Anexos de documentos']));
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
            DocumentoPublicoSchemaHelper::ensureSchema();

            $repository = new TRepository(self::$activeRecord);
            $criteria = clone $this->filter_criteria;

            $criteria->setProperties($param);
            $criteria->setProperty('order', $param['order'] ?? 'ordem');
            $criteria->setProperty('direction', $param['direction'] ?? 'asc');
            $criteria->setProperty('limit', $this->limit);

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!empty($data->nome)) {
                $criteria->add(new TFilter('nome', 'like', "%{$data->nome}%"));
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
                DocumentoPublicoSchemaHelper::ensureSchema();
                $object = new DocumentoPublicoAnexo($param['key'], false);
                $documentoId = $object->documento_publico_id;
                $object->delete();
                TTransaction::close();
                TSession::setValue(__CLASS__ . '_documento_publico_id', $documentoId);
                TSession::setValue('documento_publico_anexo_documento_id', $documentoId);
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
