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

        $id = new TEntry('id');
        $numero_documento = new TEntry('numero_documento');
        $tipo = new TCombo('tipo');
        $data_documento = new TDate('data_documento');
        $assunto = new TEntry('assunto');
        $nome = new TEntry('nome');
        $orgao = new TEntry('orgao');
        $status = new TCombo('status');
        $anexos = new TMultiFile('anexos');

        $tipo->addItems([
            'Lei' => 'Lei',
            'Edital' => 'Edital',
            'Portaria' => 'Portaria',
            'Decreto' => 'Decreto',
            'Ata' => 'Ata',
            'Resolucao' => 'Resolucao',
            'Instrucao Normativa' => 'Instrucao Normativa',
            'Outro' => 'Outro',
        ]);

        $status->addItems([
            'published' => 'Publicado',
            'draft' => 'Rascunho',
        ]);

        $numero_documento->addValidation('Numero do documento', new TRequiredValidator());
        $tipo->addValidation('Tipo', new TRequiredValidator());
        $data_documento->addValidation('Data', new TRequiredValidator());
        $assunto->addValidation('Assunto', new TRequiredValidator());
        $nome->addValidation('Nome', new TRequiredValidator());
        $orgao->addValidation('Orgao', new TRequiredValidator());
        $status->addValidation('Status', new TRequiredValidator());

        $id->setEditable(false);
        $status->setValue('published');
        $tipo->enableSearch();
        $status->enableSearch();
        $anexos->enableFileHandling();
        $anexos->setAllowedExtensions(['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'gif', 'webp']);

        $id->setSize('100%');
        $numero_documento->setSize('100%');
        $tipo->setSize('100%');
        $data_documento->setSize('100%');
        $assunto->setSize('100%');
        $nome->setSize('100%');
        $orgao->setSize('100%');
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
            [new TLabel('Tipo:', '#ff0000', '14px', null, '100%'), $tipo],
            [new TLabel('Data:', '#ff0000', '14px', null, '100%'), $data_documento],
            [new TLabel('Status:', '#ff0000', '14px', null, '100%'), $status]
        );
        $row2->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row3 = $this->form->addFields(
            [new TLabel('Nome:', '#ff0000', '14px', null, '100%'), $nome],
            [new TLabel('Orgao responsavel:', '#ff0000', '14px', null, '100%'), $orgao]
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
}
