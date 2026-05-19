<?php

class FinanceiroCadastroForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $activeRecord = 'FinanceiroCadastro';
    private static $primaryKey = 'id';
    private static $formName = 'form_FinanceiroCadastroForm';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro financeiro');

        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $descricao = new TText('descricao');

        $nome->addValidation('Nome', new TRequiredValidator());

        $id->setEditable(false);
        $id->setSize('100%');
        $nome->setSize('100%');
        $descricao->setSize('100%', 120);

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Nome:', '#ff0000', '14px', null, '100%'), $nome]
        );
        $row1->layout = ['col-sm-2', 'col-sm-10'];

        $row2 = $this->form->addFields(
            [new TLabel('Descricao:', null, '14px', null, '100%'), $descricao]
        );
        $row2->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Limpar formulario', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->form->addAction('Voltar', new TAction(['FinanceiroCadastroList', 'onShow']), 'fas:arrow-left #000000');

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
            FinanceiroPublicoSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $object = !empty($data->id) ? new FinanceiroCadastro((int) $data->id) : new FinanceiroCadastro();
            $object->fromArray((array) $data);

            $now = date('Y-m-d H:i:s');
            $object->updated_at = $now;
            if (empty($object->created_at)) {
                $object->created_at = $now;
            }

            $object->store();
            $data->id = $object->id;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Cadastro financeiro salvo', 'topRight', 'far:check-circle');
            TApplication::loadPage('FinanceiroCadastroList', 'onShow', $param ?? []);
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
                FinanceiroPublicoSchemaHelper::ensureSchema();
                $object = new FinanceiroCadastro($param['key']);
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
