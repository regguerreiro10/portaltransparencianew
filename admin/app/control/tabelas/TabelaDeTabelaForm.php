<?php

class TabelaDeTabelaForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $activeRecord = 'TabelaDeTabela';
    private static $primaryKey = 'id';
    private static $formName = 'form_TabelaDeTabelaForm';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Tabela de tabela');

        $id = new TEntry('id');
        $tabela_id = new TDBCombo('tabela_id', self::$database, 'Tabela', 'id', '{descricao}', 'descricao asc');
        $descricao = new TEntry('descricao');
        $cor = new TColor('cor');

        $tabela_id->addValidation('Tabela', new TRequiredValidator());
        $descricao->addValidation('Descricao', new TRequiredValidator());

        $id->setEditable(false);
        $tabela_id->enableSearch();

        $id->setSize('100%');
        $tabela_id->setSize('100%');
        $descricao->setSize('100%');
        $cor->setSize('100%');

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Tabela:', '#ff0000', '14px', null, '100%'), $tabela_id]
        );
        $row1->layout = ['col-sm-2', 'col-sm-10'];

        $row2 = $this->form->addFields(
            [new TLabel('Descricao:', '#ff0000', '14px', null, '100%'), $descricao],
            [new TLabel('Cor:', null, '14px', null, '100%'), $cor]
        );
        $row2->layout = ['col-sm-8', 'col-sm-4'];

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Voltar', new TAction(['TabelaDeTabelaList', 'onShow']), 'fas:arrow-left #000000');

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
            TabelaSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $object = !empty($data->id) ? new TabelaDeTabela((int) $data->id) : new TabelaDeTabela();
            $object->fromArray((array) $data);

            $now = date('Y-m-d H:i:s');
            $object->updated_at = $now;
            if (empty($object->created_at)) {
                $object->created_at = $now;
            }

            $object->store();
            $data->id = $object->id;
            $this->form->setData($data);

            $loadPageParam = [];
            if (!empty($object->tabela_id)) {
                $loadPageParam['tabela_id'] = $object->tabela_id;
            }

            TTransaction::close();
            TToast::show('success', 'Item salvo', 'topRight', 'far:check-circle');
            TApplication::loadPage('TabelaDeTabelaList', 'onShow', $loadPageParam);
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
                TabelaSchemaHelper::ensureSchema();
                $object = new TabelaDeTabela($param['key']);
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
        if (!empty($param['tabela_id'])) {
            $data = new stdClass;
            $data->tabela_id = $param['tabela_id'];
            $this->form->setData($data);
        }
    }
}
