<?php

class DocumentoPublicoList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private static $database = 'minierp';
    private static $activeRecord = 'DocumentoPublico';
    private static $primaryKey = 'id';
    private static $formName = 'formList_DocumentoPublico';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $numero_documento = new TEntry('numero_documento');
        $tipo = new TEntry('tipo');
        $nome = new TEntry('nome');
        $status = new TCombo('status');
        $status->addItems([
            '' => 'Todos',
            'published' => 'Publicado',
            'draft' => 'Rascunho',
        ]);

        $numero_documento->setSize('100%');
        $tipo->setSize('100%');
        $nome->setSize('100%');
        $status->setSize('100%');

        $numero_documento->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $tipo->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $nome->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        $status->setChangeAction(new TAction([$this, 'onSearch'], ['static' => '1']));

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', 'Id', 'center', '60px');
        $column_numero = new TDataGridColumn('numero_documento', 'Numero', 'left', '15%');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'left', '12%');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_orgao = new TDataGridColumn('orgao', 'Orgao', 'left', '20%');
        $column_data = new TDataGridColumn('data_documento', 'Data', 'center', '12%');
        $column_status = new TDataGridColumn('status', 'Status', 'center', '10%');

        $column_data->setTransformer(function ($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });

        $column_status->setTransformer(function ($value) {
            return $value === 'published' ? 'Publicado' : 'Rascunho';
        });

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_orgao);
        $this->datagrid->addColumn($column_data);
        $this->datagrid->addColumn($column_status);

        $actionEdit = new TDataGridAction(['DocumentoPublicoForm', 'onEdit']);
        $actionEdit->setLabel('Editar');
        $actionEdit->setImage('far:edit #478fca');
        $actionEdit->setUseButton(false);
        $actionEdit->setButtonClass('btn btn-default btn-sm');
        $actionEdit->setField(self::$primaryKey);
        $this->datagrid->addAction($actionEdit);

        $actionAttachments = new TDataGridAction(['DocumentoPublicoAnexoList', 'onShow']);
        $actionAttachments->setLabel('Anexos');
        $actionAttachments->setImage('fas:paperclip #0f4c81');
        $actionAttachments->setUseButton(false);
        $actionAttachments->setButtonClass('btn btn-default btn-sm');
        $actionAttachments->setField(self::$primaryKey);
        $actionAttachments->setParameter('documento_publico_id', '{id}');
        $this->datagrid->addAction($actionAttachments);

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
        $this->datagrid_form->addField($numero_documento);
        $this->datagrid_form->addField($tipo);
        $this->datagrid_form->addField($nome);
        $this->datagrid_form->addField($status);
        $this->datagrid_form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $numero_documento));
        $tr->add(TElement::tag('td', $tipo));
        $tr->add(TElement::tag('td', $nome));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $status));
        $tr->add(TElement::tag('td', ''));

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Gestao de documentos publicos');
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

        $buttonCadastrar = new TButton('button_cadastrar_documento_publico');
        $buttonCadastrar->setAction(new TAction(['DocumentoPublicoForm', 'onShow']), 'Cadastrar');
        $buttonCadastrar->setImage('fas:plus #69aa46');
        $this->datagrid_form->addField($buttonCadastrar);
        $headLeft->add($buttonCadastrar);

        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Comunicacao', 'Documentos publicos']));
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
            $criteria = new TCriteria;

            $criteria->setProperties($param);
            $criteria->setProperty('order', $param['order'] ?? 'data_documento');
            $criteria->setProperty('direction', $param['direction'] ?? 'desc');
            $criteria->setProperty('limit', $this->limit);

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!empty($data->numero_documento)) {
                $criteria->add(new TFilter('numero_documento', 'like', "%{$data->numero_documento}%"));
            }
            if (!empty($data->tipo)) {
                $criteria->add(new TFilter('tipo', 'like', "%{$data->tipo}%"));
            }
            if (!empty($data->nome)) {
                $criteria->add(new TFilter('nome', 'like', "%{$data->nome}%"));
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
            try {
                TTransaction::open(self::$database);
                DocumentoPublicoSchemaHelper::ensureSchema();
                $object = new DocumentoPublico($param['key'], false);
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
