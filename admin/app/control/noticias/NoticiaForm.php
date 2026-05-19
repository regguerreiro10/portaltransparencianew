<?php

use Adianti\Base\AdiantiFileSaveTrait;
use Adianti\Widget\Form\THtmlEditor;

class NoticiaForm extends TPage
{
    use AdiantiFileSaveTrait;

    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $activeRecord = 'Noticia';
    private static $primaryKey = 'id';
    private static $formName = 'form_NoticiaForm';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro de noticias');

        $id = new TEntry('id');
        $titulo = new TEntry('titulo');
        $categoria = new TCombo('categoria');
        $data_publicacao = new TDate('data_publicacao');
        $status = new TCombo('status');
        $imagem = new TFile('imagem');
        $resumo = new TText('resumo');
        $conteudo = new THtmlEditor('conteudo');

        $categoria->addItems([
            'Cultura' => 'Cultura',
            'Recapeamento' => 'Recapeamento',
            'Meio Ambiente' => 'Meio Ambiente',
            'Oportunidade' => 'Oportunidade',
            'Esporte e Lazer' => 'Esporte e Lazer',
            'Saude' => 'Saude',
        ]);

        $status->addItems([
            'published' => 'Publicado',
            'draft' => 'Rascunho',
        ]);

        $titulo->addValidation('Titulo', new TRequiredValidator());
        $categoria->addValidation('Categoria', new TRequiredValidator());
        $data_publicacao->addValidation('Data de publicacao', new TRequiredValidator());
        $status->addValidation('Status', new TRequiredValidator());

        $id->setEditable(false);
        $id->setSize(100);
        $titulo->setSize('100%');
        $categoria->setSize('100%');
        $data_publicacao->setSize('100%');
        $status->setSize('100%');
        $imagem->setSize('100%');
        $resumo->setSize('100%', 90);
        $conteudo->setSize('100%', 320);

        $data_publicacao->setMask('dd/mm/yyyy');
        $data_publicacao->setDatabaseMask('yyyy-mm-dd');
        $categoria->enableSearch();
        $status->setValue('published');
        $imagem->enableFileHandling();

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Titulo:', '#ff0000', '14px', null, '100%'), $titulo]
        );
        $row1->layout = ['col-sm-2', 'col-sm-10'];

        $row2 = $this->form->addFields(
            [new TLabel('Categoria:', '#ff0000', '14px', null, '100%'), $categoria],
            [new TLabel('Data publicacao:', '#ff0000', '14px', null, '100%'), $data_publicacao],
            [new TLabel('Status:', '#ff0000', '14px', null, '100%'), $status]
        );
        $row2->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row3 = $this->form->addFields(
            [new TLabel('Imagem:', null, '14px', null, '100%'), $imagem]
        );
        $row3->layout = ['col-sm-12'];

        $row4 = $this->form->addFields(
            [new TLabel('Resumo:', null, '14px', null, '100%'), $resumo]
        );
        $row4->layout = ['col-sm-12'];

        $row5 = $this->form->addFields(
            [new TLabel('Conteudo:', null, '14px', null, '100%'), $conteudo]
        );
        $row5->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Limpar formulario', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->form->addAction('Voltar', new TAction(['NoticiaList', 'onShow']), 'fas:arrow-left #000000');

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
            NoticiaSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $object = !empty($data->id) ? new Noticia((int) $data->id) : new Noticia();

            $object->fromArray((array) $data);
            $object->slug = NoticiaSchemaHelper::uniqueSlug((string) $data->titulo, !empty($data->id) ? (int) $data->id : null);

            $now = date('Y-m-d H:i:s');
            $object->updated_at = $now;
            if (empty($object->created_at)) {
                $object->created_at = $now;
            }

            $uploadDir = 'app/files/noticias';

            $object->store();
            $this->saveFile($object, $data, 'imagem', $uploadDir);

            $data->id = $object->id;
            $data->imagem = $object->imagem;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Noticia salva', 'topRight', 'far:check-circle');
            TApplication::loadPage('NoticiaList', 'onShow', $param ?? []);
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
                NoticiaSchemaHelper::ensureSchema();
                $object = new Noticia($param['key']);
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
