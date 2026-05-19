<?php

class FrenteCaixaCartaoLancamentoForm extends TPage
{
    protected $form;

    private static $database = 'minierp';
    private static $formName = 'form_FrenteCaixaCartaoLancamentoForm';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        TTransaction::open(self::$database);

        $criteria_veiculo = new TCriteria();
        $criteria_veiculo->add(new TFilter('status_veiculo_id', '=', 1));
        if (TSession::getValue('idunit'))
        {
            $criteria_veiculo->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
        }

        $criteria_estabelecimento = new TCriteria();
        $criteria_estabelecimento->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE deleted_at is null AND grupo_pessoa_id = '" . GrupoPessoa::FORNECEDOR . "')"));

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Gerar pedido a partir do cartao');

        $launch_key = new THidden('launch_key');
        $numero_cartao = new TEntry('numero_cartao');
        $dispositivos_solicitados_id = new TEntry('dispositivos_solicitados_id');
        $usuario_cartao_nome = new TEntry('usuario_cartao_nome');
        $usuario_cartao_documento = new TEntry('usuario_cartao_documento');
        $veiculo_localizado = new TEntry('veiculo_localizado');
        $saldo_atual = new TNumeric('saldo_atual', 2, ',', '.');
        $saldo_limite = new TNumeric('saldo_limite', 2, ',', '.');
        $dt_hora_autoriz = new TEntry('dt_hora_autoriz');
        $nm_loja = new TEntry('nm_loja');
        $valor_total = new TNumeric('valor_total', 2, ',', '.');
        $tp_status = new TEntry('tp_status');
        $cd_autoriz = new TEntry('cd_autoriz');
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa} - {modelo->descricao}', 'placa asc', $criteria_veiculo);
        $estabelecimento_id = new TDBCombo('estabelecimento_id', 'minierp', 'Pessoa', 'id', '{nome} - {documento}', 'nome asc', $criteria_estabelecimento);
        $km = new TEntry('km');
        $descricaopedido = new TEntry('descricaopedido');
        $descricao_item = new TEntry('descricao_item');
        $obs = new TText('obs');

        $numero_cartao->setEditable(false);
        $dispositivos_solicitados_id->setEditable(false);
        $usuario_cartao_nome->setEditable(false);
        $usuario_cartao_documento->setEditable(false);
        $veiculo_localizado->setEditable(false);
        $saldo_atual->setEditable(false);
        $saldo_limite->setEditable(false);
        $dt_hora_autoriz->setEditable(false);
        $nm_loja->setEditable(false);
        $valor_total->setEditable(false);
        $tp_status->setEditable(false);
        $cd_autoriz->setEditable(false);

        $veiculos_id->addValidation('Veiculo', new TRequiredValidator());
        $estabelecimento_id->addValidation('Estabelecimento', new TRequiredValidator());

        $veiculos_id->enableSearch();
        $estabelecimento_id->enableSearch();

        $numero_cartao->setSize('100%');
        $dispositivos_solicitados_id->setSize('100%');
        $usuario_cartao_nome->setSize('100%');
        $usuario_cartao_documento->setSize('100%');
        $veiculo_localizado->setSize('100%');
        $saldo_atual->setSize('100%');
        $saldo_limite->setSize('100%');
        $dt_hora_autoriz->setSize('100%');
        $nm_loja->setSize('100%');
        $valor_total->setSize('100%');
        $tp_status->setSize('100%');
        $cd_autoriz->setSize('100%');
        $veiculos_id->setSize('100%');
        $estabelecimento_id->setSize('100%');
        $km->setSize('100%');
        $descricaopedido->setSize('100%');
        $descricao_item->setSize('100%');
        $obs->setSize('100%', 90);

        $this->form->addFields([$launch_key]);

        $row1 = $this->form->addFields(
            [new TLabel('Cartao', null, '14px', null, '100%'), $numero_cartao],
            [new TLabel('Cadastro dispositivo', null, '14px', null, '100%'), $dispositivos_solicitados_id],
            [new TLabel('Usuario do cartao', null, '14px', null, '100%'), $usuario_cartao_nome],
            [new TLabel('CPF usuario', null, '14px', null, '100%'), $usuario_cartao_documento]
        );
        $row1->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];

        $row2 = $this->form->addFields(
            [new TLabel('Veiculo localizado', null, '14px', null, '100%'), $veiculo_localizado],
            [new TLabel('Saldo atual cartao', null, '14px', null, '100%'), $saldo_atual],
            [new TLabel('Limite cartao', null, '14px', null, '100%'), $saldo_limite],
            [new TLabel('Data/Hora API', null, '14px', null, '100%'), $dt_hora_autoriz]
        );
        $row2->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];

        $row3 = $this->form->addFields(
            [new TLabel('Loja da API', null, '14px', null, '100%'), $nm_loja],
            [new TLabel('Valor', null, '14px', null, '100%'), $valor_total],
            [new TLabel('Status API', null, '14px', null, '100%'), $tp_status],
            [new TLabel('Codigo autorizacao', null, '14px', null, '100%'), $cd_autoriz]
        );
        $row3->layout = ['col-sm-5', 'col-sm-3', 'col-sm-2', 'col-sm-2'];

        $row4 = $this->form->addFields(
            [new TLabel('Veiculo *', '#FF0000', '14px', null, '100%'), $veiculos_id],
            [new TLabel('Estabelecimento *', '#FF0000', '14px', null, '100%'), $estabelecimento_id]
        );
        $row4->layout = ['col-sm-6', 'col-sm-6'];

        $row5 = $this->form->addFields(
            [new TLabel('KM/Hodometro', null, '14px', null, '100%'), $km],
            [new TLabel('Descricao do pedido', null, '14px', null, '100%'), $descricaopedido]
        );
        $row5->layout = ['col-sm-4', 'col-sm-8'];

        $row6 = $this->form->addFields(
            [new TLabel('Descricao do item', null, '14px', null, '100%'), $descricao_item]
        );
        $row6->layout = ['col-sm-12'];

        $row7 = $this->form->addFields(
            [new TLabel('Observacao complementar', null, '14px', null, '100%'), $obs]
        );
        $row7->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Gerar pedido', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');

        $btnBack = $this->form->addAction('Voltar', new TAction(['FrenteCaixaCartaoLancamentoList', 'onShow']), 'fas:arrow-left #000000');

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = 'Template.closeRightPanel();';
        $btnClose->setLabel('Fechar');
        $btnClose->setImage('fas:times');
        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=FrenteCaixaCartaoLancamentoForm]');
        $style->width = '92% !important';
        $style->show(true);

        if (!empty($param['launch_key']))
        {
            $this->carregarLancamento($param['launch_key']);
        }

        TTransaction::close();
    }

    private function carregarLancamento(string $launchKey): void
    {
        $lancamento = CartaoLancamentoService::obterLancamentoSessao($launchKey);
        $data = new stdClass();
        $data->launch_key = $lancamento['launch_key'];
        $data->numero_cartao = $lancamento['numero_cartao'] ?? '';
        $data->dispositivos_solicitados_id = $lancamento['dispositivos_solicitados_id'] ?? '';
        $data->usuario_cartao_nome = $lancamento['usuario_cartao_nome'] ?? '';
        $data->usuario_cartao_documento = $lancamento['usuario_cartao_documento'] ?? '';
        $data->veiculo_localizado = $lancamento['veiculo_descricao'] ?? '';
        $data->saldo_atual = number_format((float) ($lancamento['saldo_atual'] ?? 0), 2, ',', '.');
        $data->saldo_limite = number_format((float) ($lancamento['saldo_limite'] ?? 0), 2, ',', '.');
        $data->dt_hora_autoriz = $lancamento['dt_hora_autoriz'] ?? '';
        $data->nm_loja = $lancamento['nm_loja'] ?? '';
        $data->valor_total = number_format((float) ($lancamento['valor_total'] ?? 0), 2, ',', '.');
        $data->tp_status = $lancamento['tp_status'] ?? '';
        $data->cd_autoriz = $lancamento['cd_autoriz'] ?? '';
        $data->estabelecimento_id = $lancamento['estabelecimento_id'] ?? null;
        $data->veiculos_id = $lancamento['veiculos_id'] ?? null;
        $data->descricaopedido = 'Lancamento cartao - ' . trim((string) ($lancamento['nm_loja'] ?? ''));
        $data->descricao_item = 'Lancamento cartao - ' . trim((string) ($lancamento['nm_loja'] ?? ''));
        $this->form->setData($data);
    }

    public function onSave($param = null)
    {
        try
        {
            if (!TTransaction::get())
            {
                TTransaction::open(self::$database);
            }

            $data = $this->form->getData();
            $this->form->validate();

            $retorno = CartaoLancamentoService::registrarLancamento((array) $data);

            TTransaction::close();

            new TMessage('info', 'Pedido #' . $retorno['pedido_id'] . ' gerado com sucesso a partir do lancamento do cartao.');
            TScript::create('Template.closeRightPanel();');
            TScript::create("__adianti_load_page('index.php?class=FrenteCaixaCartaoLancamentoList');");
        }
        catch (Exception $e)
        {
            if (TTransaction::get())
            {
                TTransaction::rollback();
            }

            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {
    }
}
