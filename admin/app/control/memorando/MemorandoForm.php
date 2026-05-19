<?php

use Adianti\Base\AdiantiFileSaveTrait;
use Adianti\Widget\Form\THtmlEditor;
use Adianti\Widget\Form\TMultiFile;

class MemorandoForm extends TPage
{
    use AdiantiFileSaveTrait;

    protected BootstrapFormBuilder $form;
    private TFieldList $destinatariosList;
    private bool $destinatariosListInitialized = false;
    private static $database = 'minierp';
    private static $activeRecord = 'Memorando';
    private static $primaryKey = 'id';
    private static $formName = 'form_MemorandoForm';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro e envio de memorandos');

        $id = new TEntry('id');
        $numero_memorando = new TEntry('numero_memorando');
        $status = new TCombo('status');
        $data_memorando = new TDateTime('data_memorando');
        $responder_id = new THidden('responder_id');
        $encaminhar_id = new THidden('encaminhar_id');
        $memorando_pai_id = new THidden('memorando_pai_id');
        $system_unit_id = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}', 'name asc', self::getUnitCriteria());
        $system_users_remetente_id = new TDBCombo('system_users_remetente_id', 'minierp', 'SystemUsers', 'id', '{name}', 'name asc', self::getUserCriteria((int) TSession::getValue('idunit')));
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}', 'name asc', self::getDepartmentCriteria((int) TSession::getValue('idunit')));
        $tipo = new TCombo('tipo');
        $template_codigo = new TCombo('template_codigo');
        $assunto = new TEntry('assunto');
        $texto_memorando = new THtmlEditor('texto_memorando');
        $anexos = new TMultiFile('anexos');
        $processo_referencia = new TEntry('processo_referencia');

        $destinatario_tipo = new TCombo('destinatario_tipo[]');
        $destinatario_usuario = new TDBCombo('destinatario_usuario[]', 'minierp', 'SystemUsers', 'id', '{name}', 'name asc');
        $destinatario_departamento = new TDBCombo('destinatario_departamento[]', 'minierp', 'DepartamentoUnit', 'id', '{name}', 'name asc', self::getDepartmentCriteria((int) TSession::getValue('idunit')));
        $this->destinatariosList = new TFieldList;

        $status->addItems(MemorandoHelper::getStatusOptions());
        $tipo->addItems([
            'Normal' => 'Normal',
            'Com copia' => 'Com copia',
            'Resposta' => 'Resposta',
            'Encaminhamento' => 'Encaminhamento',
        ]);
        $template_codigo->addItems(['' => 'Selecione'] + MemorandoHelper::getTemplates());
        $destinatario_tipo->addItems([
            'Para' => 'Para',
            'Copia' => 'Copia',
        ]);

        $template_codigo->setChangeAction(new TAction([$this, 'onChangeTemplate']));

        $assunto->addValidation('Assunto', new TRequiredValidator());
        $texto_memorando->addValidation('Texto do memorando', new TRequiredValidator());
        $tipo->addValidation('Tipo', new TRequiredValidator());
        $system_unit_id->addValidation('Unidade', new TRequiredValidator());
        $system_users_remetente_id->addValidation('Remetente', new TRequiredValidator());
        $departamento_unit_id->addValidation('Departamento origem', new TRequiredValidator());

        $id->setEditable(false);
        $numero_memorando->setEditable(MemorandoHelper::isAdmin());
        $status->setEditable(false);
        $data_memorando->setEditable(false);
        $status->setValue('Enviado');
        $system_unit_id->setValue(TSession::getValue('idunit'));
        $system_users_remetente_id->setValue(TSession::getValue('userid'));
        $departamento_unit_id->setValue(TSession::getValue('depunitid'));
        $tipo->setValue('Normal');
        $data_memorando->setValue(date('d/m/Y H:i'));

        $status->enableSearch();
        $system_unit_id->enableSearch();
        $system_users_remetente_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $tipo->enableSearch();
        $template_codigo->enableSearch();
        $destinatario_tipo->enableSearch();
        $destinatario_departamento->enableSearch();
        $destinatario_usuario->enableSearch();

        $data_memorando->setMask('dd/mm/yyyy hh:ii');
        $data_memorando->setDatabaseMask('yyyy-mm-dd hh:ii:ss');

        $anexos->enableFileHandling();
        $anexos->setAllowedExtensions(['pdf', 'doc', 'docx', 'odt', 'txt', 'png', 'jpg', 'jpeg', 'xlsx', 'zip']);

        $id->setSize('100%');
        $numero_memorando->setSize('100%');
        $status->setSize('100%');
        $data_memorando->setSize('100%');
        $system_unit_id->setSize('100%');
        $system_users_remetente_id->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $tipo->setSize('100%');
        $template_codigo->setSize('100%');
        $assunto->setSize('100%');
        $texto_memorando->setSize('100%', 320);
        $anexos->setSize('100%');
        $processo_referencia->setSize('100%');
        $destinatario_tipo->setSize('100%');
        $destinatario_usuario->setSize('100%');
        $destinatario_departamento->setSize('100%');

        $this->destinatariosList->addField(new TLabel('Tipo', null, '14px', null), $destinatario_tipo, ['width' => '15%']);
        $this->destinatariosList->addField(new TLabel('Destinatario', null, '14px', null), $destinatario_usuario, ['width' => '45%']);
        $this->destinatariosList->addField(new TLabel('Departamento destino', null, '14px', null), $destinatario_departamento, ['width' => '40%']);
        $this->destinatariosList->width = '100%';
        $this->destinatariosList->name = 'fieldList_destinatarios';
        $this->destinatariosList->setRemoveAction(null, 'fas:times #dd5a43', 'Excluir');

        $this->form->addField($destinatario_tipo);
        $this->form->addField($destinatario_usuario);
        $this->form->addField($destinatario_departamento);

        $hiddenRow = $this->form->addFields([$responder_id, $encaminhar_id, $memorando_pai_id]);
        $hiddenRow->style = 'display:none';

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Numero do memorando:', MemorandoHelper::isAdmin() ? '#ff0000' : null, '14px', null, '100%'), $numero_memorando],
            [new TLabel('Status:', null, '14px', null, '100%'), $status],
            [new TLabel('Data/hora:', null, '14px', null, '100%'), $data_memorando]
        );
        $row1->layout = ['col-sm-1', 'col-sm-4', 'col-sm-3', 'col-sm-4'];

        $row2 = $this->form->addFields(
            [new TLabel('Unidade:', '#ff0000', '14px', null, '100%'), $system_unit_id],
            [new TLabel('Remetente:', '#ff0000', '14px', null, '100%'), $system_users_remetente_id],
            [new TLabel('Departamento origem:', '#ff0000', '14px', null, '100%'), $departamento_unit_id]
        );
        $row2->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row3 = $this->form->addFields(
            [new TLabel('Tipo:', '#ff0000', '14px', null, '100%'), $tipo],
            [new TLabel('Modelo padrao:', null, '14px', null, '100%'), $template_codigo],
            [new TLabel('Referencia de processo:', null, '14px', null, '100%'), $processo_referencia]
        );
        $row3->layout = ['col-sm-3', 'col-sm-4', 'col-sm-5'];

        $row4 = $this->form->addFields(
            [new TLabel('Assunto:', '#ff0000', '14px', null, '100%'), $assunto]
        );
        $row4->layout = ['col-sm-12'];

        $row5 = $this->form->addFields(
            [new TFormSeparator('Destinatarios e copias', '#333', '18', '#eee')]
        );
        $row5->layout = ['col-sm-12'];

        $row6 = $this->form->addFields([$this->destinatariosList]);
        $row6->layout = ['col-sm-12'];

        $row7 = $this->form->addFields(
            [new TLabel('Texto do memorando:', '#ff0000', '14px', null, '100%'), $texto_memorando]
        );
        $row7->layout = ['col-sm-12'];

        $row8 = $this->form->addFields(
            [new TLabel('Anexos:', null, '14px', null, '100%'), $anexos]
        );
        $row8->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar e enviar', new TAction([$this, 'onSave']), 'fas:paper-plane #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Limpar formulario', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->form->addAction('Voltar', new TAction(['MemorandoList', 'onShow']), 'fas:arrow-left #000000');

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel('Fechar');
        $btnClose->setImage('fas:times');
        $this->form->addHeaderWidget($btnClose);

        $isEditing = !empty($param['key']) || !empty($param['id']) || (($param['method'] ?? null) === 'onEdit');
        if (!$isEditing) {
            $this->bootstrapCurrentUser();
        }

        if (!empty($param) && !$isEditing) {
            $this->applyContextParams($param);
        }

        parent::add($this->form);
    }

    public static function onChangeTemplate($param = null)
    {
        $data = new stdClass;
        $codigo = $param['template_codigo'] ?? null;
        if (!empty($codigo)) {
            $data->texto_memorando = MemorandoHelper::getTemplateContent($codigo);
        }
        TForm::sendData(self::$formName, $data, false, false);
    }

    public static function onChangeUnit($param = null)
    {
        $unitId = (int) ($param['system_unit_id'] ?? 0);
        $selectedDepartmentId = (int) ($param['departamento_unit_id'] ?? 0);
        $selectedUserId = (int) ($param['system_users_remetente_id'] ?? 0);
        $memorandoId = (int) ($param['id'] ?? $param['key'] ?? 0);
        $isEditing = $memorandoId > 0;

        if ($isEditing && (!$selectedDepartmentId || !$selectedUserId)) {
            $openedTransaction = false;
            if (!TTransaction::get()) {
                TTransaction::open(self::$database);
                $openedTransaction = true;
            }

            $memorando = Memorando::find($memorandoId);
            if ($memorando instanceof Memorando) {
                $selectedDepartmentId = $selectedDepartmentId ?: (int) $memorando->departamento_unit_id;
                $selectedUserId = $selectedUserId ?: (int) $memorando->system_users_remetente_id;
                $unitId = $unitId ?: (int) $memorando->system_unit_id;
            }

            if ($openedTransaction) {
                TTransaction::close();
            }
        }

        TDBCombo::reloadFromModel(
            self::$formName,
            'departamento_unit_id',
            self::$database,
            'DepartamentoUnit',
            'id',
            '{name}',
            'name asc',
            self::getDepartmentCriteria($unitId, $isEditing ? $selectedDepartmentId : null),
            true,
            false
        );

        TDBCombo::reloadFromModel(
            self::$formName,
            'system_users_remetente_id',
            self::$database,
            'SystemUsers',
            'id',
            '{name}',
            'name asc',
            self::getUserCriteria($unitId, $isEditing ? $selectedUserId : null),
            true,
            false
        );

        if ($isEditing || $selectedDepartmentId || $selectedUserId) {
            $data = new stdClass;
            $data->departamento_unit_id = $selectedDepartmentId ?: null;
            $data->system_users_remetente_id = $selectedUserId ?: null;
            TForm::sendData(self::$formName, $data, false, false);
        }
    }

    public function onSave($param = null)
    {
        $fieldListData = [];
        try {
            TTransaction::open(self::$database);
            MemorandoSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $fieldListData = $this->destinatariosList->getPostData();
            $this->validateDestinatarios($fieldListData);

            $isNew = empty($data->id);
            $object = $isNew ? new Memorando() : new Memorando((int) $data->id);

            if (!$isNew && !MemorandoHelper::canEditMemorando($object)) {
                throw new Exception('Voce nao tem permissao para editar este memorando.');
            }

            $context = MemorandoHelper::getCurrentUserContext();
            $currentNumber = $isNew ? null : $object->numero_memorando;
            $unitId = (int) $data->system_unit_id;
            $remetenteUserId = (int) $data->system_users_remetente_id;
            $departamentoOrigemId = (int) $data->departamento_unit_id;
            $this->validateOriginContext($unitId, $remetenteUserId, $departamentoOrigemId);

            $object->assunto = $data->assunto;
            $object->texto_memorando = $data->texto_memorando;
            $object->tipo = $data->tipo;
            $object->template_codigo = $data->template_codigo ?: null;
            $object->template_nome = MemorandoHelper::getTemplates()[$data->template_codigo] ?? null;
            $object->system_unit_id = $unitId;
            $unit = new SystemUnit($unitId);
            $object->entidade_id = TSession::getValue('entidade') ?: $unit->entidade_id ?: $context['entidade_id'];
            $object->system_users_remetente_id = $remetenteUserId;
            $object->departamento_unit_id = $departamentoOrigemId;
            $object->processo_referencia = $data->processo_referencia ?: null;
            $object->downloads = (int) ($object->downloads ?? 0);

            if ($isNew || empty($object->numero_memorando)) {
                $object->numero_memorando = $this->generateNumeroMemorando();
            } elseif (!MemorandoHelper::isAdmin()) {
                $object->numero_memorando = $currentNumber;
            } else {
                $object->numero_memorando = $data->numero_memorando;
            }

            $object->status = 'Enviado';
            MemorandoHelper::applyStatusColor($object);
            $object->data_memorando = !empty($object->data_memorando) ? $object->data_memorando : date('Y-m-d H:i:s');

            $responderId = (int) ($data->responder_id ?? $param['responder_id'] ?? 0);
            $encaminharId = (int) ($data->encaminhar_id ?? $param['encaminhar_id'] ?? 0);
            $memorandoPaiId = (int) ($data->memorando_pai_id ?? 0);

            if ($responderId > 0) {
                $object->memorando_pai_id = $responderId;
                $object->tipo = 'Resposta';
            }

            if ($encaminharId > 0) {
                $object->memorando_pai_id = $encaminharId;
                $object->tipo = 'Encaminhamento';
                $object->status = 'Encaminhado';
            }

            if (!$responderId && !$encaminharId && $memorandoPaiId > 0) {
                $object->memorando_pai_id = $memorandoPaiId;
            }

            $now = date('Y-m-d H:i:s');
            $object->updated_at = $now;
            if (empty($object->created_at)) {
                $object->created_at = $now;
            }

            $object->store();

            $this->clearDestinatarios($object->id);
            $savedDestinatarios = $this->storeDestinatarios($object, $fieldListData);

            $attachments = $this->saveFiles(
                $object,
                $data,
                'anexos',
                'app/files/memorandos',
                'MemorandoAnexo',
                'arquivo',
                'memorando_id'
            );
            $this->updateAttachmentMetadata($attachments);

            $descricao = $isNew
                ? 'Memorando criado e enviado para ' . count($savedDestinatarios) . ' destinatario(s).'
                : 'Memorando atualizado pelo remetente.';

            MemorandoHelper::createTramitacao($object->id, $isNew ? 'Criado' : 'Editado', $object->status, $descricao);

            foreach ($savedDestinatarios as $destinatario) {
                MemorandoHelper::createTramitacao(
                    $object->id,
                    'Enviado',
                    'Enviado',
                    sprintf(
                        'Enviado para %s (%s).',
                        $destinatario->destinatario_nome,
                        $destinatario->departamento_destino_nome
                    ),
                    $destinatario->id
                );
            }

            if ($responderId > 0) {
                $this->markParentAsResponded($responderId);
            }

            $data->id = $object->id;
            $data->numero_memorando = $object->numero_memorando;
            $data->status = $object->status;
            $data->system_unit_id = $object->system_unit_id;
            $data->system_users_remetente_id = $object->system_users_remetente_id;
            $data->departamento_unit_id = $object->departamento_unit_id;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Memorando salvo com sucesso.', 'topRight', 'far:check-circle');
            TApplication::loadPage('MemorandoList', 'onShow', []);
            TScript::create("Template.closeRightPanel();");
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            if (!$fieldListData) {
                $fieldListData = $this->getDestinatariosRowsFromParam($param ?? []);
            }
            $this->populateDestinatariosList($fieldListData);
            $this->form->setData($this->form->getData());
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
        }
    }

    public function onEdit($param)
    {
        try {
            if (!isset($param['key'])) {
                $this->form->clear(true);
                 $this->bootstrapCurrentUser();
                $this->initializeDestinatariosList();
                return;
            }

            TTransaction::open(self::$database);
            MemorandoSchemaHelper::ensureSchema();
            $object = new Memorando((int) $param['key']);


            if (!MemorandoHelper::canAccessMemorando($object)) {
                throw new Exception('Voce nao tem permissao para acessar este memorando.');
            }

            $data = (object) $object->toArray();
            $this->applyOriginFallbacks($data);
            $data->anexos = [];

            foreach ($object->getMemorandoAnexos() as $anexo) {
                $data->anexos[$anexo->id] = $anexo->arquivo;
            }

            $destinatarios = $object->getMemorandoDestinatarios();
            $this->populateDestinatariosList($destinatarios);

            if (!empty($data->system_unit_id)) {
                TDBCombo::reloadFromModel(
                    self::$formName,
                    'departamento_unit_id',
                    self::$database,
                    'DepartamentoUnit',
                    'id',
                    '{name}',
                    'name asc',
                    self::getDepartmentCriteria((int) $data->system_unit_id, (int) $data->departamento_unit_id),
                    true,
                    false
                );

                TDBCombo::reloadFromModel(
                    self::$formName,
                    'system_users_remetente_id',
                    self::$database,
                    'SystemUsers',
                    'id',
                    '{name}',
                    'name asc',
                    self::getUserCriteria((int) $data->system_unit_id, (int) $data->system_users_remetente_id),
                    true,
                    false
                );
            }

            $this->form->setData($data);

            $originData = new stdClass;
            $originData->system_unit_id = $data->system_unit_id;
            $originData->system_users_remetente_id = $data->system_users_remetente_id;
            $originData->departamento_unit_id = $data->departamento_unit_id;
            TForm::sendData(self::$formName, $originData, false, false);
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear($param)
    {
        $this->form->clear(true);
        $this->bootstrapCurrentUser();
        $this->initializeDestinatariosList();
    }

    public function onShow($param = null)
    {
        $this->initializeDestinatariosList();
    }

    private function initializeDestinatariosList(): void
    {
        if ($this->destinatariosListInitialized) {
            return;
        }

        $this->destinatariosList->addHeader();
        $this->destinatariosList->addDetail(new stdClass);
        $this->destinatariosList->addCloneAction(null, 'fas:plus #69aa46', 'Clonar');
        $this->destinatariosListInitialized = true;
    }

    private function populateDestinatariosList(array $destinatarios): void
    {
        if ($this->destinatariosListInitialized) {
            return;
        }

        $this->destinatariosList->addHeader();
        $prefix = $this->destinatariosList->getFieldPrefix();

        if ($destinatarios) {
            foreach ($destinatarios as $destinatario) {
                $detail = new stdClass;
                $tipoField = $prefix ? "{$prefix}_destinatario_tipo" : 'destinatario_tipo';
                $usuarioField = $prefix ? "{$prefix}_destinatario_usuario" : 'destinatario_usuario';
                $departamentoField = $prefix ? "{$prefix}_destinatario_departamento" : 'destinatario_departamento';

                $detail->{$tipoField} = $destinatario->tipo_destino ?? $destinatario->destinatario_tipo ?? 'Para';
                $detail->{$usuarioField} = $destinatario->system_users_id ?? $destinatario->destinatario_usuario ?? null;
                $detail->{$departamentoField} = $destinatario->departamento_unit_id ?? $destinatario->destinatario_departamento ?? null;
                $this->destinatariosList->addDetail($detail);
            }
        } else {
            $this->destinatariosList->addDetail(new stdClass);
        }

        $this->destinatariosList->addCloneAction(null, 'fas:plus #69aa46', 'Clonar');
        $this->destinatariosListInitialized = true;
    }

    private function bootstrapCurrentUser(): void
    {
        $current = $this->form->getData();
        if (!empty($current->id)) {
            return;
        }

        $context = MemorandoHelper::getCurrentUserContext();
        $data = new stdClass;
        $data->status = 'Enviado';
        $data->data_memorando = date('d/m/Y H:i');
        $data->system_unit_id = $context['system_unit_id'];
        $data->system_users_remetente_id = $context['user_id'];
        $data->departamento_unit_id = $context['primary_department_id'];
        $this->form->setData($data);
    }

    private static function getUnitCriteria(): TCriteria
    {
        $criteria = new TCriteria;

        if (!MemorandoHelper::isAdmin() && TSession::getValue('idunit')) {
            $criteria->add(new TFilter('id', '=', (int) TSession::getValue('idunit')));
        } elseif (TSession::getValue('entidade')) {
            $criteria->add(new TFilter('entidade_id', '=', (int) TSession::getValue('entidade')));
        }

        return $criteria;
    }

    private static function getDepartmentCriteria(int $unitId, ?int $selectedDepartmentId = null): TCriteria
    {
        $criteria = new TCriteria;
        if ($unitId > 0) {
            $sql = "SELECT id FROM departamento_unit WHERE system_unit_id = {$unitId}";
            if (!empty($selectedDepartmentId)) {
                $sql .= " UNION SELECT id FROM departamento_unit WHERE id = {$selectedDepartmentId}";
            }
            $criteria->add(new TFilter('id', 'IN', "({$sql})"));
        } else {
            $criteria->add(new TFilter('id', '=', 0));
        }

        return $criteria;
    }

    private static function getUserCriteria(int $unitId, ?int $selectedUserId = null): TCriteria
    {
        $criteria = new TCriteria;
        if ($unitId > 0) {
            $sql = "SELECT system_user_id FROM system_user_unit WHERE system_unit_id = {$unitId}
                    UNION
                    SELECT id FROM system_users WHERE system_unit_id = {$unitId}";
            if (!empty($selectedUserId)) {
                $sql .= " UNION SELECT id FROM system_users WHERE id = {$selectedUserId}";
            }
            $criteria->add(new TFilter('id', 'IN', "({$sql})"));
        } else {
            $criteria->add(new TFilter('id', '=', 0));
        }

        return $criteria;
    }

    private function applyOriginFallbacks(stdClass $data): void
    {
        $context = MemorandoHelper::getCurrentUserContext();

        if (empty($data->system_unit_id)) {
            $data->system_unit_id = $context['system_unit_id'];
        }

        if (empty($data->system_users_remetente_id)) {
            $data->system_users_remetente_id = $context['user_id'];
        }

        if (empty($data->departamento_unit_id)) {
            $data->departamento_unit_id = $context['primary_department_id'];
        }
    }

    private function getDestinatariosRowsFromParam(array $param): array
    {
        $rows = [];
        $tipos = $param['destinatario_tipo'] ?? [];
        $usuarios = $param['destinatario_usuario'] ?? [];
        $departamentos = $param['destinatario_departamento'] ?? [];
        $total = max(count((array) $tipos), count((array) $usuarios), count((array) $departamentos));

        for ($i = 0; $i < $total; $i++) {
            $row = new stdClass;
            $row->destinatario_tipo = $tipos[$i] ?? 'Para';
            $row->destinatario_usuario = $usuarios[$i] ?? null;
            $row->destinatario_departamento = $departamentos[$i] ?? null;
            $rows[] = $row;
        }

        return $rows;
    }

    private function applyContextParams(array $param): void
    {
        if (empty($param['responder_id']) && empty($param['encaminhar_id'])) {
            return;
        }

        try {
            TTransaction::open(self::$database);
            MemorandoSchemaHelper::ensureSchema();
            $origemId = !empty($param['responder_id']) ? (int) $param['responder_id'] : (int) $param['encaminhar_id'];
            $memorando = new Memorando($origemId);

            if (!MemorandoHelper::canAccessMemorando($memorando)) {
                throw new Exception('Voce nao tem permissao para usar este memorando como base.');
            }

            $data = $this->form->getData();
            $data->assunto = !empty($param['responder_id']) ? 'RES: ' . $memorando->assunto : 'ENC: ' . $memorando->assunto;
            $data->texto_memorando = '<p></p><hr><p><strong>Referencia:</strong> ' . $memorando->numero_memorando . '</p>' . $memorando->texto_memorando;
            $data->processo_referencia = $memorando->processo_referencia;
            $data->tipo = !empty($param['responder_id']) ? 'Resposta' : 'Encaminhamento';
            $data->memorando_pai_id = $origemId;
            $data->responder_id = !empty($param['responder_id']) ? $origemId : null;
            $data->encaminhar_id = !empty($param['encaminhar_id']) ? $origemId : null;
            $this->form->setData($data);

            if (!empty($param['responder_id'])) {
                $destinatario = new stdClass;
                $destinatario->tipo_destino = 'Para';
                $destinatario->system_users_id = $memorando->system_users_remetente_id;
                $destinatario->departamento_unit_id = $memorando->departamento_unit_id;
                $this->populateDestinatariosList([$destinatario]);
            }

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    private function validateDestinatarios(array $rows): void
    {
        $validRows = 0;
        foreach ($rows as $row) {
            if (!empty($row->destinatario_usuario) && !empty($row->destinatario_departamento)) {
                $validRows++;
            }
        }

        if ($validRows === 0) {
            throw new Exception('Informe ao menos um destinatario com departamento.');
        }
    }

    private function validateOriginContext(int $unitId, int $userId, int $departmentId): void
    {
        if ($unitId <= 0 || $userId <= 0 || $departmentId <= 0) {
            throw new Exception('Informe unidade, remetente e departamento de origem.');
        }

        if (!MemorandoHelper::isAdmin() && (int) TSession::getValue('idunit') !== $unitId) {
            throw new Exception('A unidade informada nao corresponde a unidade selecionada no login.');
        }

        $department = new DepartamentoUnit($departmentId);
        if ((int) $department->system_unit_id !== $unitId) {
            throw new Exception('O departamento de origem nao pertence a unidade informada.');
        }

        $conn = TTransaction::get();
        $stmt = $conn->prepare(
            "SELECT COUNT(*)
               FROM (
                    SELECT system_user_id AS user_id FROM system_user_unit WHERE system_unit_id = ?
                    UNION
                    SELECT id AS user_id FROM system_users WHERE system_unit_id = ?
               ) users_unit
              WHERE users_unit.user_id = ?"
        );
        $stmt->execute([$unitId, $unitId, $userId]);

        if (!MemorandoHelper::isAdmin() && (int) $stmt->fetchColumn() === 0) {
            throw new Exception('O remetente informado nao pertence a unidade selecionada.');
        }
    }

    private function storeDestinatarios(Memorando $memorando, array $rows): array
    {
        $saved = [];
        $seen = [];
        $hasCopy = false;

        foreach ($rows as $row) {
            if (empty($row->destinatario_usuario) || empty($row->destinatario_departamento)) {
                continue;
            }

            $key = $row->destinatario_tipo . '-' . $row->destinatario_usuario . '-' . $row->destinatario_departamento;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $destinatario = new MemorandoDestinatario();
            $destinatario->memorando_id = $memorando->id;
            $destinatario->tipo_destino = $row->destinatario_tipo ?: 'Para';
            $destinatario->system_users_id = (int) $row->destinatario_usuario;
            $destinatario->departamento_unit_id = (int) $row->destinatario_departamento;
            $destinatario->status = 'Enviado';
            $destinatario->recebido_em = date('Y-m-d H:i:s');
            $destinatario->created_at = date('Y-m-d H:i:s');
            $destinatario->updated_at = date('Y-m-d H:i:s');
            $destinatario->store();
            $saved[] = $destinatario;

            if ($destinatario->tipo_destino === 'Copia') {
                $hasCopy = true;
            }
        }

        if ($hasCopy && $memorando->tipo === 'Normal') {
            $memorando->tipo = 'Com copia';
            $memorando->store();
        }

        return $saved;
    }

    private function clearDestinatarios(int $memorandoId): void
    {
        $destinatarios = MemorandoDestinatario::where('memorando_id', '=', $memorandoId)->load();

        if (!$destinatarios) {
            return;
        }

        foreach ($destinatarios as $destinatario) {
            MemorandoTramitacao::where('memorando_destinatario_id', '=', $destinatario->id)
                ->set('memorando_destinatario_id', null)
                ->update();

            $destinatario->delete();
        }
    }

    private function updateAttachmentMetadata(array $attachments): void
    {
        foreach ($attachments as $index => $attachment) {
            if (!$attachment instanceof MemorandoAnexo) {
                continue;
            }

            $attachment->ordem = $index + 1;
            if (empty($attachment->nome) && !empty($attachment->arquivo)) {
                $attachment->nome = basename($attachment->arquivo);
            }
            $attachment->store();
        }
    }

    private function generateNumeroMemorando(): string
    {
        $conn = TTransaction::get();
        $result = $conn->query('SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM memorando');
        $nextId = (int) $result->fetchColumn();
        return sprintf('MEM-%s-%06d', date('Y'), $nextId);
    }

    private function markParentAsResponded(int $memorandoId): void
    {
        $parent = new Memorando($memorandoId);
        $context = MemorandoHelper::getCurrentUserContext();
        $updated = false;

        foreach ($parent->getMemorandoDestinatarios() as $destinatario) {
            if ((int) $destinatario->system_users_id === (int) $context['user_id']) {
                $destinatario->status = 'Respondido';
                $destinatario->respondido_em = date('Y-m-d H:i:s');
                $destinatario->updated_at = date('Y-m-d H:i:s');
                $destinatario->store();
                MemorandoHelper::createTramitacao(
                    $parent->id,
                    'Respondido',
                    'Respondido',
                    'Resposta registrada a partir do memorando vinculado.',
                    $destinatario->id
                );
                $updated = true;
            }
        }

        if (!$updated) {
            $parent->status = 'Respondido';
            MemorandoHelper::applyStatusColor($parent);
            $parent->updated_at = date('Y-m-d H:i:s');
            $parent->store();
            MemorandoHelper::createTramitacao($parent->id, 'Respondido', 'Respondido', 'Resposta registrada.');
        } else {
            MemorandoHelper::updateOverallStatus($parent);
        }
    }
}
