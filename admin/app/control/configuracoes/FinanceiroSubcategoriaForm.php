<?php

class FinanceiroSubcategoriaForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $activeRecord = 'FinanceiroSubcategoria';
    private static $primaryKey = 'id';
    private static $formName = 'form_FinanceiroSubcategoriaForm';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Subcategoria financeira');

        $id = new TEntry('id');
        $financeiro_cadastro_id = new TDBCombo('financeiro_cadastro_id', 'minierp', 'FinanceiroCadastro', 'id', '{nome}', 'nome asc');
        $categoria_id = new TDBCombo('categoria_id', 'minierp', 'FinanceiroCategoria', 'id', '{nome}', 'nome asc');
        $nome = new TEntry('nome');
        $ano = new TEntry('ano');
        $visivel = new TCombo('visivel');

        $visivel->addItems(['Y' => 'Sim', 'N' => 'Nao']);

        $financeiro_cadastro_id->addValidation('Cadastro financeiro', new TRequiredValidator());
        $categoria_id->addValidation('Categoria', new TRequiredValidator());
        $nome->addValidation('Nome', new TRequiredValidator());
        $ano->addValidation('Ano', new TRequiredValidator());
        $visivel->addValidation('Visivel', new TRequiredValidator());

        $id->setEditable(false);
        $financeiro_cadastro_id->enableSearch();
        $categoria_id->enableSearch();
        $visivel->enableSearch();
        $visivel->setValue('Y');

        $id->setSize('100%');
        $financeiro_cadastro_id->setSize('100%');
        $categoria_id->setSize('100%');
        $nome->setSize('100%');
        $ano->setSize('100%');
        $visivel->setSize('100%');

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Cadastro principal:', '#ff0000', '14px', null, '100%'), $financeiro_cadastro_id]
        );
        $row1->layout = ['col-sm-2', 'col-sm-10'];

        $row2 = $this->form->addFields(
            [new TLabel('Categoria:', '#ff0000', '14px', null, '100%'), $categoria_id],
            [new TLabel('Nome:', '#ff0000', '14px', null, '100%'), $nome]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $row3 = $this->form->addFields(
            [new TLabel('Ano:', '#ff0000', '14px', null, '100%'), $ano],
            [new TLabel('Visivel no site:', '#ff0000', '14px', null, '100%'), $visivel]
        );
        $row3->layout = ['col-sm-6', 'col-sm-6'];

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Voltar', new TAction(['FinanceiroSubcategoriaList', 'onShow']), 'fas:arrow-left #000000');

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
            $object = !empty($data->id) ? new FinanceiroSubcategoria((int) $data->id) : new FinanceiroSubcategoria();
            $object->fromArray((array) $data);
            $object->ano = (int) $object->ano;

            $now = date('Y-m-d H:i:s');
            $object->updated_at = $now;
            if (empty($object->created_at)) {
                $object->created_at = $now;
            }

            $object->store();
            $data->id = $object->id;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Subcategoria salva', 'topRight', 'far:check-circle');
            TApplication::loadPage('FinanceiroSubcategoriaList', 'onShow', $param ?? []);
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
                $object = new FinanceiroSubcategoria($param['key']);
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

    public function onShow($param = null)
    {
        if (!empty($param['financeiro_cadastro_id'])) {
            $data = new stdClass;
            $data->financeiro_cadastro_id = $param['financeiro_cadastro_id'];
            $data->visivel = 'Y';
            $this->form->setData($data);
        }
    }
}
