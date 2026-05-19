<?php

class GravarTagNfcForm extends TPage
{
    protected $form;

    private static $database = 'minierp';
    private static $formName = 'form_GravarTagNfcForm';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $criteria_dispositivo = new TCriteria();
        $criteria_dispositivo->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Gravacao de TAG NFC');

        $dispositivos_solicitados_id = new TDBCombo('dispositivos_solicitados_id', self::$database, 'DispositivosSolicitados', 'id', '{id} - {numerocartao}', 'id desc', $criteria_dispositivo);
        $carregar_cadastro = new TButton('carregar_cadastro');
        $ler_tag = new TButton('ler_tag');
        $gravar_tag = new TButton('gravar_tag');

        $numerocartao = new TEntry('numerocartao');
        $uid_lida = new TEntry('uid_lida');
        $placa_modelo = new TEntry('placa_modelo');
        $responsavel_nome = new TEntry('responsavel_nome');
        $unidade_nome = new TEntry('unidade_nome');
        $departamento_nome = new TEntry('departamento_nome');
        $status_nome = new TEntry('status_nome');
        $saldo_limite = new TNumeric('saldo_limite', 2, ',', '.');
        $payload_json = new TText('payload_json');
        $conteudo_lido = new TText('conteudo_lido');

        $dispositivos_solicitados_id->addValidation('Dispositivo solicitado', new TRequiredValidator());
        $dispositivos_solicitados_id->setChangeAction(new TAction([__CLASS__, 'onCarregarCadastro']));

        $carregar_cadastro->setAction(new TAction([__CLASS__, 'onCarregarCadastro']), 'Carregar cadastro');
        $carregar_cadastro->setImage('fas:sync-alt #3f51b5');
        $carregar_cadastro->addStyleClass('btn-default');

        $ler_tag->setLabel('Ler TAG');
        $ler_tag->setImage('fas:wifi #2e7d32');
        $ler_tag->addStyleClass('btn-default');
        $ler_tag->setProperty('type', 'button');
        $ler_tag->setProperty('onclick', 'GravarTagNfcForm_readTag(); return false;');

        $gravar_tag->setLabel('Gravar TAG');
        $gravar_tag->setImage('fas:save #ffffff');
        $gravar_tag->addStyleClass('btn-primary');
        $gravar_tag->setProperty('type', 'button');
        $gravar_tag->setProperty('onclick', 'GravarTagNfcForm_writeTag(); return false;');

        $dispositivos_solicitados_id->enableSearch();

        $numerocartao->setEditable(false);
        $uid_lida->setEditable(false);
        $placa_modelo->setEditable(false);
        $responsavel_nome->setEditable(false);
        $unidade_nome->setEditable(false);
        $departamento_nome->setEditable(false);
        $status_nome->setEditable(false);
        $saldo_limite->setEditable(false);
        $conteudo_lido->setEditable(false);

        $dispositivos_solicitados_id->setSize('100%');
        $numerocartao->setSize('100%');
        $uid_lida->setSize('100%');
        $placa_modelo->setSize('100%');
        $responsavel_nome->setSize('100%');
        $unidade_nome->setSize('100%');
        $departamento_nome->setSize('100%');
        $status_nome->setSize('100%');
        $saldo_limite->setSize('100%');
        $payload_json->setSize('100%', 220);
        $conteudo_lido->setSize('100%', 160);

        $row1 = $this->form->addFields(
            [new TLabel('Dispositivo solicitado *', '#FF0000', '14px', null, '100%'), $dispositivos_solicitados_id, $carregar_cadastro],
            [new TLabel('UID lida da TAG', null, '14px', null, '100%'), $uid_lida, $ler_tag]
        );
        $row1->layout = ['col-sm-6', 'col-sm-6'];

        $row2 = $this->form->addFields(
            [new TLabel('Numero do cartao / UID Tag', null, '14px', null, '100%'), $numerocartao],
            [new TLabel('Status da TAG', null, '14px', null, '100%'), $status_nome]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $row3 = $this->form->addFields(
            [new TLabel('Veiculo', null, '14px', null, '100%'), $placa_modelo],
            [new TLabel('Responsavel', null, '14px', null, '100%'), $responsavel_nome]
        );
        $row3->layout = ['col-sm-6', 'col-sm-6'];

        $row4 = $this->form->addFields(
            [new TLabel('Unidade', null, '14px', null, '100%'), $unidade_nome],
            [new TLabel('Departamento', null, '14px', null, '100%'), $departamento_nome]
        );
        $row4->layout = ['col-sm-6', 'col-sm-6'];

        $row5 = $this->form->addFields([new TLabel('Saldo limite', null, '14px', null, '100%'), $saldo_limite]);
        $row5->layout = ['col-sm-6'];

        $row6 = $this->form->addFields([new TLabel('Payload que sera gravado na TAG', null, '14px', null, '100%'), $payload_json]);
        $row6->layout = ['col-sm-12'];

        $row7 = $this->form->addFields([new TLabel('Conteudo lido da TAG', null, '14px', null, '100%'), $conteudo_lido, $gravar_tag]);
        $row7->layout = ['col-sm-12'];

        $observacao = new TElement('div');
        $observacao->style = 'margin:10px 0 0 0;padding:12px;border:1px solid #d9edf7;background:#f4fbff;color:#245269;border-radius:4px;';
        $observacao->add('A TAG NFC sera gravada com o mesmo codigo salvo em Numero do cartao / UID Tag. Na leitura, o sistema prioriza esse conteudo gravado e usa o UID fisico apenas como contingencia.');
        $this->form->addContent([$observacao]);

        $this->form->addAction('Montar payload', new TAction([__CLASS__, 'onCarregarCadastro']), 'fas:code #4b4b4b');
        $this->form->addAction('Voltar', new TAction(['DispositivosSolicitadosList', 'onShow']), 'fas:arrow-left #000000');

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = 'Template.closeRightPanel();';
        $btnClose->setLabel('Fechar');
        $btnClose->setImage('fas:times');
        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=GravarTagNfcForm]');
        $style->width = '95% !important';
        $style->show(true);

        TScript::create(<<<'JS'
window.GravarTagNfcForm_readTag = async function () {
    if (!('NDEFReader' in window)) {
        alert('Leitura NFC via navegador esta disponivel principalmente no Android/Chrome.');
        return;
    }

    try {
        const reader = new NDEFReader();
        await reader.scan();

        reader.onreading = (event) => {
            const uidField = document.querySelector('[name="uid_lida"]');
            const contentField = document.querySelector('[name="conteudo_lido"]');
            let content = '';

            if (event.message && event.message.records) {
                for (const record of event.message.records) {
                    try {
                        if (record.recordType === 'text') {
                            content += new TextDecoder(record.encoding || 'utf-8').decode(record.data);
                        } else {
                            content += '[Registro NFC tipo ' + record.recordType + ']';
                        }
                    } catch (e) {
                        content += '[Nao foi possivel decodificar um registro]';
                    }
                }
            }

            if (uidField) {
                uidField.value = event.serialNumber || '';
            }

            if (contentField) {
                contentField.value = content || event.serialNumber || '';
            }
        };

        alert('Aproxime a TAG NFC do celular para leitura.');
    } catch (error) {
        alert('Nao foi possivel iniciar a leitura NFC: ' + error.message);
    }
};

window.GravarTagNfcForm_writeTag = async function () {
    if (!('NDEFReader' in window)) {
        alert('Gravacao NFC via navegador esta disponivel principalmente no Android/Chrome.');
        return;
    }

    const numerocartaoField = document.querySelector('[name="numerocartao"]');
    const payloadField = document.querySelector('[name="payload_json"]');
    if (!numerocartaoField || !numerocartaoField.value.trim()) {
        alert('Carregue o cadastro e confirme o Numero do cartao / UID Tag antes de gravar.');
        return;
    }

    try {
        const writer = new NDEFReader();
        await writer.write({
            records: [
                {
                    recordType: 'text',
                    data: numerocartaoField.value.trim()
                }
            ]
        });

        if (payloadField) {
            payloadField.value = numerocartaoField.value.trim();
        }

        alert('TAG gravada com sucesso.');
    } catch (error) {
        alert('Nao foi possivel gravar a TAG: ' + error.message);
    }
};
JS);

        if (!empty($param['key']) || !empty($param['dispositivos_solicitados_id']))
        {
            self::onCarregarCadastro([
                'dispositivos_solicitados_id' => $param['key'] ?? $param['dispositivos_solicitados_id']
            ]);
        }
    }

    public static function onCarregarCadastro($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $id = (int) ($param['dispositivos_solicitados_id'] ?? 0);
            if ($id <= 0)
            {
                throw new Exception('Selecione um dispositivo solicitado para montar a gravacao.');
            }

            $dispositivo = new DispositivosSolicitados($id);
            if (empty($dispositivo->id))
            {
                throw new Exception('Dispositivo solicitado nao encontrado.');
            }

            $veiculo = !empty($dispositivo->veiculos_id) ? $dispositivo->veiculos : null;
            $modeloDescricao = ($veiculo && !empty($veiculo->modelo_id) && !empty($veiculo->modelo->descricao)) ? $veiculo->modelo->descricao : '';

            $data = new stdClass();
            $data->dispositivos_solicitados_id = $dispositivo->id;
            $data->numerocartao = $dispositivo->numerocartao;
            $data->uid_lida = $param['uid_lida'] ?? '';
            $data->placa_modelo = $veiculo ? trim($veiculo->placa . ($modeloDescricao ? ' - ' . $modeloDescricao : '')) : '';
            $data->responsavel_nome = !empty($dispositivo->pessoa_id) ? $dispositivo->pessoa->nome : '';
            $data->unidade_nome = !empty($dispositivo->system_unit_id) ? $dispositivo->system_unit->name : '';
            $data->departamento_nome = !empty($dispositivo->departamento_unit_id) ? $dispositivo->departamento_unit->name : '';
            $data->status_nome = !empty($dispositivo->status_dispositivos_id) ? $dispositivo->status_dispositivos->descricao : '';
            $data->saldo_limite = number_format((float) $dispositivo->saldo_limite, 2, ',', '.');
            $data->payload_json = (string) $dispositivo->numerocartao;

            TForm::sendData(self::$formName, $data);

            TTransaction::close();
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {
    }
}
