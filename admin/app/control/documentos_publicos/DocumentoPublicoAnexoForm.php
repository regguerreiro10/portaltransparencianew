<?php

use Adianti\Base\AdiantiFileSaveTrait;

class DocumentoPublicoAnexoForm extends TPage
{
    use AdiantiFileSaveTrait;

    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $activeRecord = 'DocumentoPublicoAnexo';
    private static $primaryKey = 'id';
    private static $formName = 'form_DocumentoPublicoAnexoForm';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro de anexos do documento');

        $id = new THidden('id');
        $documento_publico_id = new THidden('documento_publico_id');
        $nome = new TEntry('nome');
        $ordem = new TEntry('ordem');
        $arquivo = new TFile('arquivo');

        $documento_publico_id->setValue($param['documento_publico_id'] ?? TSession::getValue('documento_publico_anexo_documento_id'));
        $arquivo->enableFileHandling();

        $nome->addValidation('Nome do anexo', new TRequiredValidator());

        $nome->setSize('100%');
        $ordem->setSize('100%');
        $arquivo->setSize('100%');
        $ordem->setValue(1);

        $row1 = $this->form->addFields([$id, $documento_publico_id]);
        $row1->layout = ['col-sm-6'];

        $row2 = $this->form->addFields(
            [new TLabel('Nome do anexo:', '#ff0000', '14px', null, '100%'), $nome],
            [new TLabel('Ordem:', null, '14px', null, '100%'), $ordem]
        );
        $row2->layout = ['col-sm-8', 'col-sm-4'];

        $row3 = $this->form->addFields(
            [new TLabel('Arquivo:', null, '14px', null, '100%'), $arquivo]
        );
        $row3->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');

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
            $object = !empty($data->id) ? new DocumentoPublicoAnexo((int) $data->id) : new DocumentoPublicoAnexo();
            $object->fromArray((array) $data);

            if (empty($object->documento_publico_id)) {
                throw new Exception('Documento nao informado para o anexo.');
            }

            $object->ordem = (int) ($object->ordem ?: 1);
            if (empty($object->created_at)) {
                $object->created_at = date('Y-m-d H:i:s');
            }

            $object->store();
            $this->saveFile($object, $data, 'arquivo', 'app/files/documentos_publicos');

            $loadPageParam = ['documento_publico_id' => $object->documento_publico_id];
            if (!empty($param['target_container'])) {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            $data->id = $object->id;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Anexo salvo', 'topRight', 'far:check-circle');
            TApplication::loadPage('DocumentoPublicoAnexoList', 'onShow', $loadPageParam);
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
                $object = new DocumentoPublicoAnexo($param['key']);
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
}
