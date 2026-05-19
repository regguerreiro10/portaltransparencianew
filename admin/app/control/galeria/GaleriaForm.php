<?php

use Adianti\Base\AdiantiFileSaveTrait;

class GaleriaForm extends TPage
{
    use AdiantiFileSaveTrait;

    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $activeRecord = 'GaleriaItem';
    private static $primaryKey = 'id';
    private static $formName = 'form_GaleriaForm';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro da galeria');

        $id = new TEntry('id');
        $titulo = new TEntry('titulo');
        $tipo = new TCombo('tipo');
        $status = new TCombo('status');
        $midia = new TFile('arquivo');
        $descricao = new TText('descricao');

        $tipo->addItems([
            'foto' => 'Foto',
            'video' => 'Video',
            'audio' => 'Audio',
        ]);

        $status->addItems([
            'published' => 'Publicado',
            'draft' => 'Rascunho',
        ]);

        $titulo->addValidation('Titulo', new TRequiredValidator());
        $tipo->addValidation('Tipo', new TRequiredValidator());
        $status->addValidation('Status', new TRequiredValidator());

        $id->setEditable(false);
        $id->setSize(100);
        $titulo->setSize('100%');
        $tipo->setSize('100%');
        $status->setSize('100%');
        $midia->setSize('100%');
        $descricao->setSize('100%', 110);

        $tipo->setValue('foto');
        $status->setValue('published');
        $midia->enableFileHandling();
        $midia->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'ogg', 'mov', 'm4v', 'mp3', 'wav', 'm4a']);
        $midia->setLimitUploadSize(100);

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Titulo:', '#ff0000', '14px', null, '100%'), $titulo]
        );
        $row1->layout = ['col-sm-2', 'col-sm-10'];

        $row2 = $this->form->addFields(
            [new TLabel('Tipo:', '#ff0000', '14px', null, '100%'), $tipo],
            [new TLabel('Status:', '#ff0000', '14px', null, '100%'), $status]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $row3 = $this->form->addFields(
            [new TLabel('Midia:', null, '14px', null, '100%'), $midia]
        );
        $row3->layout = ['col-sm-12'];

        $row4 = $this->form->addFields(
            [new TLabel('Descricao:', null, '14px', null, '100%'), $descricao]
        );
        $row4->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Limpar formulario', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->form->addAction('Voltar', new TAction(['GaleriaList', 'onShow']), 'fas:arrow-left #000000');

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
            GaleriaSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $object = !empty($data->id) ? new GaleriaItem((int) $data->id) : new GaleriaItem();
            $previousFile = $object->arquivo ?? '';

            if (empty($data->arquivo) && empty($previousFile)) {
                throw new Exception('Informe a midia da galeria.');
            }

            $object->fromArray((array) $data);
            $object->ordem = $object->ordem !== null && $object->ordem !== '' ? (int) $object->ordem : 0;
            $object->texto_alternativo = $object->texto_alternativo ?: $object->titulo;

            $now = date('Y-m-d H:i:s');
            $object->updated_at = $now;
            if (empty($object->created_at)) {
                $object->created_at = $now;
            }

            $uploadDir = 'app/files/galeria';

            $object->store();
            if (!empty($data->arquivo)) {
                $this->saveFile($object, $data, 'arquivo', $uploadDir);
            }

            if (empty($object->arquivo) && !empty($previousFile)) {
                $object->arquivo = $previousFile;
                $object->store();
            }

            $data->id = $object->id;
            $data->arquivo = $object->arquivo;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Item da galeria salvo', 'topRight', 'far:check-circle');
            TApplication::loadPage('GaleriaList', 'onShow', $param ?? []);
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
                GaleriaSchemaHelper::ensureSchema();
                $object = new GaleriaItem($param['key']);
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
