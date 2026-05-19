<?php

class ProcessoLegislativoTramitacaoForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $formName = 'form_ProcessoLegislativoTramitacaoForm';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Tramitacao do processo legislativo');

        $id = new TEntry('id');
        $processo_legislativo_id = new THidden('processo_legislativo_id');
        $data_tramitacao = new TDateTime('data_tramitacao');
        $situacao = new TCombo('situacao');
        $descricao_andamento = new TEntry('descricao_andamento');
        $remetente = new TEntry('remetente');
        $destinatario = new TEntry('destinatario');
        $usuario_responsavel = new TEntry('usuario_responsavel');
        $observacao = new TText('observacao');

        $situacao->addItems([
            'Protocolado' => 'Protocolado',
            'Em analise' => 'Em analise',
            'Em pauta' => 'Em pauta',
            'Apreciado' => 'Apreciado',
            'Arquivado' => 'Arquivado',
        ]);

        $situacao->addValidation('Situacao', new TRequiredValidator());
        $descricao_andamento->addValidation('Descricao do andamento', new TRequiredValidator());
        $destinatario->addValidation('Destinatario', new TRequiredValidator());
        $usuario_responsavel->addValidation('Usuario responsavel', new TRequiredValidator());

        $id->setEditable(false);
        $data_tramitacao->setMask('dd/mm/yyyy hh:ii');
        $data_tramitacao->setDatabaseMask('yyyy-mm-dd hh:ii:ss');
        $data_tramitacao->setValue(date('d/m/Y H:i'));
        $situacao->setValue('Em analise');
        $situacao->enableSearch();

        $id->setSize('100%');
        $data_tramitacao->setSize('100%');
        $situacao->setSize('100%');
        $descricao_andamento->setSize('100%');
        $remetente->setSize('100%');
        $destinatario->setSize('100%');
        $usuario_responsavel->setSize('100%');
        $observacao->setSize('100%', 100);

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [$processo_legislativo_id]
        );
        $row1->layout = ['col-sm-2', 'col-sm-10'];

        $row2 = $this->form->addFields(
            [new TLabel('Data/hora:', '#ff0000', '14px', null, '100%'), $data_tramitacao],
            [new TLabel('Situacao:', '#ff0000', '14px', null, '100%'), $situacao]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $row3 = $this->form->addFields(
            [new TLabel('Descricao do andamento:', '#ff0000', '14px', null, '100%'), $descricao_andamento]
        );
        $row3->layout = ['col-sm-12'];

        $row4 = $this->form->addFields(
            [new TLabel('Remetente:', null, '14px', null, '100%'), $remetente],
            [new TLabel('Destinatario:', '#ff0000', '14px', null, '100%'), $destinatario]
        );
        $row4->layout = ['col-sm-6', 'col-sm-6'];

        $row5 = $this->form->addFields(
            [new TLabel('Usuario responsavel:', '#ff0000', '14px', null, '100%'), $usuario_responsavel]
        );
        $row5->layout = ['col-sm-12'];

        $row6 = $this->form->addFields(
            [new TLabel('Observacao:', null, '14px', null, '100%'), $observacao]
        );
        $row6->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Voltar', new TAction(['ProcessoLegislativoTramitacaoList', 'onShow']), 'fas:arrow-left #000000');

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
            ProcessoLegislativoSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $object = !empty($data->id) ? new ProcessoLegislativoTramitacao((int) $data->id) : new ProcessoLegislativoTramitacao();
            $object->fromArray((array) $data);
            $object->created_at = $object->created_at ?: date('Y-m-d H:i:s');
            $object->store();

            $processo = new ProcessoLegislativo($object->processo_legislativo_id);
            $processo->situacao_status = $object->situacao;
            if (!empty($object->destinatario)) {
                $processo->departamento_gabinete = $object->destinatario;
            }
            $processo->updated_at = date('Y-m-d H:i:s');
            $processo->store();

            TTransaction::close();

            TToast::show('success', 'Tramitacao salva', 'topRight', 'far:check-circle');
            TApplication::loadPage('ProcessoLegislativoTramitacaoList', 'onShow', ['processo_legislativo_id' => $object->processo_legislativo_id]);
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
            TTransaction::open(self::$database);
            ProcessoLegislativoSchemaHelper::ensureSchema();

            if (isset($param['processo_legislativo_id'])) {
                TSession::setValue('processo_legislativo_tramitacao_processo_id', $param['processo_legislativo_id']);
            }

            if (isset($param['key'])) {
                $object = new ProcessoLegislativoTramitacao($param['key']);
                $this->form->setData($object);
            } else {
                $data = new stdClass;
                $data->processo_legislativo_id = $param['processo_legislativo_id'] ?? TSession::getValue('processo_legislativo_tramitacao_processo_id');
                $data->usuario_responsavel = TSession::getValue('login');
                $data->data_tramitacao = date('d/m/Y H:i');
                $this->form->setData($data);
            }

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {
        $this->onEdit($param ?? []);
    }
}
