<?php

use Adianti\Base\AdiantiFileSaveTrait;
use Adianti\Widget\Form\TMultiFile;

class DocumentoPublicoForm extends TPage
{
    use AdiantiFileSaveTrait;

    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $activeRecord = 'DocumentoPublico';
    private static $primaryKey = 'id';
    private static $formName = 'form_DocumentoPublicoForm';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro de documentos publicos');

        $criteria_unit = new TCriteria;
        $unitId = $this->getSessionUnitId();
        if ($unitId) {
            $criteria_unit->add(new TFilter('system_unit_id', '=', $unitId));
        }
        $criteria_tipo = new TCriteria;
        $criteria_tipo->add(new TFilter('tabela_id', '=', 1));

        $id = new TEntry('id');
        $numero_documento = new TEntry('numero_documento');
        $documento_publico_tipo_id = new TDBCombo('documento_publico_tipo_id', self::$database, 'TabelaDeTabela', 'id', '{descricao}', 'descricao asc', $criteria_tipo);
        $data_documento = new TDate('data_documento');
        $assunto = new TEntry('assunto');
        $system_users_id = new TDBCombo('system_users_id', self::$database, 'SystemUsers', 'id', '{name}', 'name asc', $criteria_unit);
        $departamento_unit_id = new TDBCombo('departamento_unit_id', self::$database, 'DepartamentoUnit', 'id', '{name}', 'name asc', $criteria_unit);
        $status = new TCombo('status');
        $anexos = new TMultiFile('anexos');

        $status->addItems([
            'published' => 'Publicado',
            'draft' => 'Rascunho',
        ]);

        $numero_documento->addValidation('Numero do documento', new TRequiredValidator());
        $documento_publico_tipo_id->addValidation('Tipo', new TRequiredValidator());
        $data_documento->addValidation('Data', new TRequiredValidator());
        $assunto->addValidation('Assunto', new TRequiredValidator());
        $system_users_id->addValidation('Nome', new TRequiredValidator());
        $departamento_unit_id->addValidation('Orgao', new TRequiredValidator());
        $status->addValidation('Status', new TRequiredValidator());

        $id->setEditable(false);
        $status->setValue('published');
        $system_users_id->setValue($this->getSessionUserId());
        $departamento_unit_id->setValue($this->getDefaultDepartamentoUnitId());
        $documento_publico_tipo_id->enableSearch();
        $system_users_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $status->enableSearch();
        $anexos->enableFileHandling();
        $anexos->setAllowedExtensions(['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'gif', 'webp']);

        $id->setSize('100%');
        $numero_documento->setSize('100%');
        $documento_publico_tipo_id->setSize('100%');
        $data_documento->setSize('100%');
        $assunto->setSize('100%');
        $system_users_id->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $status->setSize('100%');
        $anexos->setSize('100%');

        $data_documento->setMask('dd/mm/yyyy');
        $data_documento->setDatabaseMask('yyyy-mm-dd');

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Numero do documento:', '#ff0000', '14px', null, '100%'), $numero_documento]
        );
        $row1->layout = ['col-sm-2', 'col-sm-10'];

        $row2 = $this->form->addFields(
            [new TLabel('Tipo:', '#ff0000', '14px', null, '100%'), $documento_publico_tipo_id],
            [new TLabel('Data:', '#ff0000', '14px', null, '100%'), $data_documento],
            [new TLabel('Status:', '#ff0000', '14px', null, '100%'), $status]
        );
        $row2->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row3 = $this->form->addFields(
            [new TLabel('Nome:', '#ff0000', '14px', null, '100%'), $system_users_id],
            [new TLabel('Orgao responsavel:', '#ff0000', '14px', null, '100%'), $departamento_unit_id]
        );
        $row3->layout = ['col-sm-6', 'col-sm-6'];

        $row4 = $this->form->addFields(
            [new TLabel('Assunto:', '#ff0000', '14px', null, '100%'), $assunto]
        );
        $row4->layout = ['col-sm-12'];

        $row5 = $this->form->addFields(
            [new TLabel('Upload de arquivos (PDF, DOC/DOCX, imagens):', null, '14px', null, '100%'), $anexos]
        );
        $row5->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Limpar formulario', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->form->addAction('Voltar', new TAction(['DocumentoPublicoList', 'onShow']), 'fas:arrow-left #000000');

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel('Fechar');
        $btnClose->setImage('fas:times');
        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);
    }

    public function onSave($param = null)
    {
        try {
            TTransaction::open(self::$database);
            DocumentoPublicoSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $object = !empty($data->id) ? new DocumentoPublico((int) $data->id) : new DocumentoPublico();

            $object->fromArray((array) $data);
            $object->documento_publico_tipo_id = $data->documento_publico_tipo_id ?? null;
            $object->system_unit_id = $this->getSessionUnitId();
            $object->entidade_id = $this->getSessionEntidadeId();
            $object->system_users_id = $data->system_users_id ?: $this->getSessionUserId();
            $object->departamento_unit_id = $data->departamento_unit_id ?: $this->getDefaultDepartamentoUnitId();
            $object->nome = $this->getSystemUserName($object->system_users_id);
            $object->orgao = $this->getDepartamentoUnitName($object->departamento_unit_id);
            $object->tipo = $this->getDocumentoPublicoTipoDescricao($object->documento_publico_tipo_id);
            $object->downloads = isset($object->downloads) ? (int) $object->downloads : 0;

            $now = date('Y-m-d H:i:s');
            $object->updated_at = $now;
            if (empty($object->created_at)) {
                $object->created_at = $now;
            }

            $object->store();
            $attachments = $this->saveFiles(
                $object,
                $data,
                'anexos',
                'app/files/documentos_publicos',
                'DocumentoPublicoAnexo',
                'arquivo',
                'documento_publico_id'
            );
            $this->updateAttachmentMetadata($attachments);

            $data->id = $object->id;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Documento salvo', 'topRight', 'far:check-circle');
            TApplication::loadPage('DocumentoPublicoList', 'onShow', $param ?? []);
            TScript::create("Template.closeRightPanel();");
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                TTransaction::open(self::$database);
                DocumentoPublicoSchemaHelper::ensureSchema();
                $object = new DocumentoPublico($param['key']);
                $object->anexos = [];

                foreach ($object->getDocumentoPublicoAnexos() as $anexo) {
                    $object->anexos[$anexo->id] = $anexo->arquivo;
                }

                $this->form->setData($object);
                TTransaction::close();
            } else {
                $this->form->clear();
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear($param)
    {
        $this->form->clear(true);
    }

    public function onShow($param = null)
    {
        $data = new stdClass;
        $data->status = 'published';
        $data->system_users_id = $this->getSessionUserId();
        $data->departamento_unit_id = $this->getDefaultDepartamentoUnitId();
        $this->form->setData($data);
    }

    private function updateAttachmentMetadata(array $attachments): void
    {
        foreach ($attachments as $index => $attachment) {
            if (!$attachment instanceof DocumentoPublicoAnexo) {
                continue;
            }

            $attachment->ordem = $index + 1;

            if (empty($attachment->nome) && !empty($attachment->arquivo)) {
                $attachment->nome = basename($attachment->arquivo);
            }

            $attachment->store();
        }
    }

    private function getSessionUserId()
    {
        return TSession::getValue('userid') ?: TSession::getValue('iduser');
    }

    private function getDefaultDepartamentoUnitId()
    {
        $userId = $this->getSessionUserId();
        $openedTransaction = false;

        try {
            if (!TTransaction::get()) {
                TTransaction::open(self::$database);
                DocumentoPublicoSchemaHelper::ensureSchema();
                $openedTransaction = true;
            }
        } catch (Exception $e) {
            TTransaction::open(self::$database);
            DocumentoPublicoSchemaHelper::ensureSchema();
            $openedTransaction = true;
        }

        if ($userId && class_exists('SystemUserDepartamentoUnit')) {
            $userDepartment = SystemUserDepartamentoUnit::where('system_users_id', '=', $userId)->first();
            if ($userDepartment) {
                if ($openedTransaction) {
                    TTransaction::close();
                }
                return $userDepartment->departamento_unit_id;
            }
        }

        $unitId = $this->getSessionUnitId();
        if ($unitId) {
            $department = DepartamentoUnit::where('system_unit_id', '=', $unitId)->first();
            if ($department) {
                if ($openedTransaction) {
                    TTransaction::close();
                }
                return $department->id;
            }
        }

        if ($openedTransaction) {
            TTransaction::close();
        }

        return null;
    }

    private function getSystemUserName($userId): string
    {
        if (!$userId) {
            return '';
        }

        $user = new SystemUsers((int) $userId);
        return (string) $user->name;
    }

    private function getSessionUnitId()
    {
        return TSession::getValue('idunit') ?: TSession::getValue('userunitid');
    }

    private function getSessionEntidadeId()
    {
        return TSession::getValue('entidade_id') ?: TSession::getValue('entidade');
    }

    private function getDepartamentoUnitName($departmentId): string
    {
        if (!$departmentId) {
            return '';
        }

        $department = new DepartamentoUnit((int) $departmentId);
        return (string) $department->name;
    }

    private function getDocumentoPublicoTipoDescricao($tipoId): string
    {
        if (!$tipoId) {
            return '';
        }

        $tipo = new TabelaDeTabela((int) $tipoId);
        return (string) $tipo->descricao;
    }
}
