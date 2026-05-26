<?php

class DocumentoPublicoList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private $tipoDescricaoMap = [];
    private $usuarioNomeMap = [];
    private $departamentoNomeMap = [];
    private static $database = 'minierp';
    private static $activeRecord = 'DocumentoPublico';
    private static $primaryKey = 'id';
    private static $formName = 'form_DocumentoPublicoList';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Listagem de documentos publicos');

        $unitId = $this->getSessionUnitId();
        $criteria_unit = new TCriteria;
        if ($unitId) {
            $criteria_unit->add(new TFilter('system_unit_id', '=', $unitId));
        }
        $criteria_tipo = new TCriteria;
        $criteria_tipo->add(new TFilter('tabela_id', '=', 1));

        $numero_documento = new TEntry('numero_documento');
        $documento_publico_tipo_id = new TDBCombo('documento_publico_tipo_id', self::$database, 'TabelaDeTabela', 'id', '{descricao}', 'descricao asc', $criteria_tipo);
        $system_users_id = new TDBCombo('system_users_id', self::$database, 'SystemUsers', 'id', '{name}', 'name asc', $criteria_unit);
        $departamento_unit_id = new TDBCombo('departamento_unit_id', self::$database, 'DepartamentoUnit', 'id', '{name}', 'name asc', $criteria_unit);
        $status = new TCombo('status');
        $status->addItems([
            '' => 'Todos',
            'published' => 'Publicado',
            'draft' => 'Rascunho',
        ]);

        $numero_documento->setSize('100%');
        $documento_publico_tipo_id->setSize('100%');
        $system_users_id->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $status->setSize('100%');
        $documento_publico_tipo_id->enableSearch();
        $system_users_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $status->enableSearch();

        $row1 = $this->form->addFields(
            [new TLabel('Numero:', null, '14px', null, '100%'), $numero_documento],
            [new TLabel('Tipo:', null, '14px', null, '100%'), $documento_publico_tipo_id],
            [new TLabel('Status:', null, '14px', null, '100%'), $status]
        );
        $row1->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row2 = $this->form->addFields(
            [new TLabel('Nome:', null, '14px', null, '100%'), $system_users_id],
            [new TLabel('Orgao:', null, '14px', null, '100%'), $departamento_unit_id]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));
        TSession::setValue(__CLASS__ . '_filters', null);

        $btnBuscar = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $btnBuscar->addStyleClass('btn-primary');
        $this->form->addAction('Cadastrar', new TAction(['DocumentoPublicoForm', 'onShow']), 'fas:plus #69aa46');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $this->filter_criteria = new TCriteria;
        $this->filter_criteria->add(new TFilter('deleted_at', 'is', null));
        if ($unitId) {
            $this->filter_criteria->add(new TFilter('system_unit_id', '=', $unitId));
        }

        $column_id = new TDataGridColumn('id', 'Id', 'center', '60px');
        $column_numero = new TDataGridColumn('numero_documento', 'Numero', 'left', '15%');
        $column_tipo = new TDataGridColumn('documento_publico_tipo_id', 'Tipo', 'left', '12%');
        $column_nome = new TDataGridColumn('system_users_id', 'Nome', 'left');
        $column_orgao = new TDataGridColumn('departamento_unit_id', 'Orgao', 'left', '20%');
        $column_data = new TDataGridColumn('data_documento', 'Data', 'center', '12%');
        $column_status = new TDataGridColumn('status', 'Status', 'center', '10%');

        $column_data->setTransformer(function ($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });

        $column_tipo->setTransformer(function ($value, $object) {
            return $this->tipoDescricaoMap[(int) $value] ?? $object->tipo ?? '';
        });

        $column_nome->setTransformer(function ($value, $object) {
            return $this->usuarioNomeMap[(int) $value] ?? $object->nome ?? '';
        });

        $column_orgao->setTransformer(function ($value, $object) {
            return $this->departamentoNomeMap[(int) $value] ?? $object->orgao ?? '';
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

        $this->datagrid_form = new TForm('datagrid_' . self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Gestao de documentos publicos');
        $panel->datagrid = 'datagrid-container';
        $panel->getBody()->class .= ' table-responsive';
        $panel->addFooter($this->pageNavigation);

        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Comunicacao', 'Documentos publicos']));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);
        $this->onReload($param ?? []);
    }

    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        TSession::setValue(__CLASS__ . '_filters', null);
        $this->form->setData($data);
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onReload($param = null)
    {
        try {
            TTransaction::open(self::$database);
            DocumentoPublicoSchemaHelper::ensureSchema();

            $repository = new TRepository(self::$activeRecord);
            $criteria = clone $this->filter_criteria;

            if (empty($param['order'])) {
                $param['order'] = 'data_documento';
            }

            if (empty($param['direction'])) {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param);
            $criteria->setProperty('limit', $this->limit);

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!empty($data->numero_documento)) {
                $criteria->add(new TFilter('numero_documento', 'like', "%{$data->numero_documento}%"));
            }
            if (!empty($data->documento_publico_tipo_id)) {
                $criteria->add(new TFilter('documento_publico_tipo_id', '=', (int) $data->documento_publico_tipo_id));
            }
            if (!empty($data->system_users_id)) {
                $criteria->add(new TFilter('system_users_id', '=', (int) $data->system_users_id));
            }
            if (!empty($data->departamento_unit_id)) {
                $criteria->add(new TFilter('departamento_unit_id', '=', (int) $data->departamento_unit_id));
            }
            if (!empty($data->status)) {
                $criteria->add(new TFilter('status', '=', $data->status));
            }

            $objects = $repository->load($criteria, false);
            $this->loadColumnMaps($objects ?: []);
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

    private function getSessionUnitId()
    {
        return TSession::getValue('idunit') ?: TSession::getValue('userunitid');
    }

    private function loadColumnMaps(array $objects): void
    {
        $tipoIds = [];
        $usuarioIds = [];
        $departamentoIds = [];

        foreach ($objects as $object) {
            if (!empty($object->documento_publico_tipo_id)) {
                $tipoIds[] = (int) $object->documento_publico_tipo_id;
            }
            if (!empty($object->system_users_id)) {
                $usuarioIds[] = (int) $object->system_users_id;
            }
            if (!empty($object->departamento_unit_id)) {
                $departamentoIds[] = (int) $object->departamento_unit_id;
            }
        }

        $this->tipoDescricaoMap = $tipoIds ? TabelaDeTabela::where('id', 'in', array_unique($tipoIds))->getIndexedArray('id', 'descricao') : [];
        $this->usuarioNomeMap = $usuarioIds ? SystemUsers::where('id', 'in', array_unique($usuarioIds))->getIndexedArray('id', 'name') : [];
        $this->departamentoNomeMap = $departamentoIds ? DepartamentoUnit::where('id', 'in', array_unique($departamentoIds))->getIndexedArray('id', 'name') : [];
    }
}
