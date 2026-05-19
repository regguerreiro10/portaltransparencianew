<?php

class FrenteCaixaAbastecimentoTagForm extends TPage
{
    protected $form;

    private static $database = 'minierp';
    private static $formName = 'form_FrenteCaixaAbastecimentoTagForm';

    private static function obterEstabelecimentoUsuarioLogadoId(): ?int
    {
        $userId = TSession::getValue('userid');
        if (empty($userId))
        {
            return null;
        }

        $openedTransaction = false;

        if (!TTransaction::get())
        {
            TTransaction::open(self::$database);
            $openedTransaction = true;
        }

        $pessoa = Pessoa::where('system_user_id', '=', $userId)->first();

        if ($openedTransaction)
        {
            TTransaction::close();
        }

        return $pessoa ? (int) $pessoa->id : null;
    }

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        TTransaction::open(self::$database);

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Frente de Caixa de Abastecimento por TAG');

        $criteria_estabelecimento = new TCriteria();
        $criteria_estabelecimento->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE deleted_at is null AND grupo_pessoa_id = '" . GrupoPessoa::FORNECEDOR . "')"));

        $criteria_tipo_combustivel = new TCriteria();

        $id = new TEntry('id');
        $uid_tag = new TEntry('uid_tag');
        $buscar_tag = new TButton('buscar_tag');
        $ler_qrcode = new TButton('ler_qrcode');
        $ler_nfc = new TButton('ler_nfc');
        $dispositivos_solicitados_id = new THidden('dispositivos_solicitados_id');
        $veiculos_id = new THidden('veiculos_id');

        $placa_modelo = new TEntry('placa_modelo');
        $responsavel_nome = new TEntry('responsavel_nome');
        $departamento_nome = new TEntry('departamento_nome');
        $unidade_nome = new TEntry('unidade_nome');
        $status_tag = new TEntry('status_tag');
        $saldo_atual = new TNumeric('saldo_atual', 2, ',', '.');

        $data_abastecimento = new TDateTime('data_abastecimento');
        $estabelecimento_id = new TDBCombo('estabelecimento_id', 'minierp', 'Pessoa', 'id', '{nome} - {documento}', 'nome asc', $criteria_estabelecimento);
        $tipo_combustivel_id = new TDBCombo('tipo_combustivel_id', 'minierp', 'TipoCombustivel', 'id', '{descricao}', 'descricao asc', $criteria_tipo_combustivel);
        $km = new TEntry('km');
        $qtde_litros = new TNumeric('qtde_litros', 3, ',', '.');
        $valor_litro = new TNumeric('valor_litro', 3, ',', '.');
        $valor_total = new TNumeric('valor_total', 2, ',', '.');
        $descricaopedido = new TEntry('descricaopedido');
        $obs = new TText('obs');

        $id->setEditable(false);
        $placa_modelo->setEditable(false);
        $responsavel_nome->setEditable(false);
        $departamento_nome->setEditable(false);
        $unidade_nome->setEditable(false);
        $status_tag->setEditable(false);
        $saldo_atual->setEditable(false);
        $valor_total->setEditable(false);

        $uid_tag->addValidation('UID da tag', new TRequiredValidator());
        $data_abastecimento->addValidation('Data do abastecimento', new TRequiredValidator());
        $estabelecimento_id->addValidation('Estabelecimento', new TRequiredValidator());
        $tipo_combustivel_id->addValidation('Tipo de combustível', new TRequiredValidator());
        $qtde_litros->addValidation('Qtde litros', new TRequiredValidator());
        $valor_litro->addValidation('Valor por litro', new TRequiredValidator());

        $uid_tag->setExitAction(new TAction([__CLASS__, 'onBuscarTag']));
        $qtde_litros->setExitAction(new TAction([__CLASS__, 'onCalcularTotais']));
        $valor_litro->setExitAction(new TAction([__CLASS__, 'onCalcularTotais']));

        $buscar_tag->setAction(new TAction([__CLASS__, 'onBuscarTag']), 'Validar TAG');
        $buscar_tag->addStyleClass('btn-default');
        $buscar_tag->setImage('fas:search #3f51b5');

        $ler_qrcode->setLabel('Ler QR Code');
        $ler_qrcode->setImage('fas:qrcode #ef6c00');
        $ler_qrcode->addStyleClass('btn-default');
        $ler_qrcode->setProperty('type', 'button');
        $ler_qrcode->setProperty('onclick', 'FrenteCaixaAbastecimentoTagForm_readQrCode(); return false;');

        $ler_nfc->setLabel('Ler NFC');
        $ler_nfc->setImage('fas:mobile-alt #2e7d32');
        $ler_nfc->addStyleClass('btn-default');
        $ler_nfc->setProperty('type', 'button');
        $ler_nfc->setProperty('onclick', 'FrenteCaixaAbastecimentoTagForm_readNfc(); return false;');

        $data_abastecimento->setMask('dd/mm/yyyy hh:ii');
        $data_abastecimento->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_abastecimento->setValue(date('d/m/Y H:i'));

        $qtde_litros->setValue('0,000');
        $valor_litro->setValue('0,000');
        $valor_total->setValue('0,00');
        $saldo_atual->setValue('0,00');

        $estabelecimento_id->enableSearch();
        $tipo_combustivel_id->enableSearch();

        $id->setSize(100);
        $uid_tag->setSize('100%');
        $placa_modelo->setSize('100%');
        $responsavel_nome->setSize('100%');
        $departamento_nome->setSize('100%');
        $unidade_nome->setSize('100%');
        $status_tag->setSize('100%');
        $saldo_atual->setSize('100%');
        $data_abastecimento->setSize('100%');
        $estabelecimento_id->setSize('100%');
        $tipo_combustivel_id->setSize('100%');
        $km->setSize('100%');
        $qtde_litros->setSize('100%');
        $valor_litro->setSize('100%');
        $valor_total->setSize('100%');
        $descricaopedido->setSize('100%');
        $obs->setSize('100%', 70);

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('UID da TAG *', '#FF0000', '14px', null, '100%'), $uid_tag, $buscar_tag, $ler_qrcode, $ler_nfc]
        );
        $row1->layout = ['col-sm-3', 'col-sm-9'];

        $row2 = $this->form->addFields(
            [$dispositivos_solicitados_id],
            [$veiculos_id]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $row3 = $this->form->addFields(
            [new TLabel('Veículo', null, '14px', null, '100%'), $placa_modelo],
            [new TLabel('Responsável', null, '14px', null, '100%'), $responsavel_nome]
        );
        $row3->layout = ['col-sm-6', 'col-sm-6'];

        $row4 = $this->form->addFields(
            [new TLabel('Unidade', null, '14px', null, '100%'), $unidade_nome],
            [new TLabel('Departamento', null, '14px', null, '100%'), $departamento_nome]
        );
        $row4->layout = ['col-sm-6', 'col-sm-6'];

        $row5 = $this->form->addFields(
            [new TLabel('Status da TAG', null, '14px', null, '100%'), $status_tag],
            [new TLabel('Saldo disponível', null, '14px', null, '100%'), $saldo_atual]
        );
        $row5->layout = ['col-sm-6', 'col-sm-6'];

        $row6 = $this->form->addFields(
            [new TLabel('Data/Hora *', '#FF0000', '14px', null, '100%'), $data_abastecimento],
            [new TLabel('Estabelecimento *', '#FF0000', '14px', null, '100%'), $estabelecimento_id]
        );
        $row6->layout = ['col-sm-4', 'col-sm-8'];

        $row7 = $this->form->addFields(
            [new TLabel('Tipo de combustível *', '#FF0000', '14px', null, '100%'), $tipo_combustivel_id],
            [new TLabel('KM/Hodômetro', null, '14px', null, '100%'), $km]
        );
        $row7->layout = ['col-sm-6', 'col-sm-6'];

        $row8 = $this->form->addFields(
            [new TLabel('Qtde litros *', '#FF0000', '14px', null, '100%'), $qtde_litros],
            [new TLabel('Valor/litro *', '#FF0000', '14px', null, '100%'), $valor_litro],
            [new TLabel('Valor total', null, '14px', null, '100%'), $valor_total]
        );
        $row8->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row9 = $this->form->addFields([new TLabel('Descrição do pedido', null, '14px', null, '100%'), $descricaopedido]);
        $row9->layout = ['col-sm-12'];

        $row10 = $this->form->addFields([new TLabel('Observação', null, '14px', null, '100%'), $obs]);
        $row10->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Registrar abastecimento', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');

        $btnClear = $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $btnBack = $this->form->addAction('Voltar', new TAction(['PedidoFrotasAbastecimentoList', 'onShow']), 'fas:arrow-left #000000');

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = 'Template.closeRightPanel();';
        $btnClose->setLabel('Fechar');
        $btnClose->setImage('fas:times');
        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=FrenteCaixaAbastecimentoTagForm]');
        $style->width = '95% !important';
        $style->show(true);

        TScript::create(<<<'JS'
window.FrenteCaixaAbastecimentoTagForm_applyTagValue = function (value) {
    let serial = (value || '').trim();
    if (serial && (serial.startsWith('{') || serial.startsWith('['))) {
        try {
            const parsed = JSON.parse(serial);
            serial = (parsed.numerocartao || parsed.uid_tag || parsed.codigo || parsed.tag || serial).toString().trim();
        } catch (e) {
        }
    }

    if (!serial) {
        alert('Nenhum codigo foi identificado.');
        return;
    }

    const field = document.querySelector('[name="uid_tag"]');
    if (!field) {
        return;
    }

    field.value = serial;
    field.dispatchEvent(new Event('change', { bubbles: true }));

    const buscar = document.querySelector('[name="buscar_tag"]');
    if (buscar) {
        buscar.click();
    }
};

window.FrenteCaixaAbastecimentoTagForm_readQrCode = async function () {
    if (!('BarcodeDetector' in window) || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Leitura de QR Code via navegador esta disponivel principalmente no Android/Chrome mais recente.');
        return;
    }

    let stream = null;
    let animationId = null;
    let overlay = null;

    try {
        const detector = new BarcodeDetector({ formats: ['qr_code'] });
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: 'environment' } },
            audio: false
        });

        overlay = document.createElement('div');
        overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.82);z-index:99999;display:flex;align-items:center;justify-content:center;padding:16px;';
        overlay.innerHTML = ''
            + '<div style="width:min(420px,100%);background:#111827;border-radius:14px;padding:16px;color:#fff;box-shadow:0 18px 45px rgba(0,0,0,.35);">'
            + '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">'
            + '<strong>Ler QR Code da TAG</strong>'
            + '<button type="button" style="background:#dc2626;border:none;color:#fff;border-radius:8px;padding:8px 12px;cursor:pointer;">Fechar</button>'
            + '</div>'
            + '<div style="font-size:12px;color:#d1d5db;margin-bottom:10px;">Aponte a camera para o QR Code impresso na TAG.</div>'
            + '<video autoplay playsinline muted style="width:100%;border-radius:10px;background:#000;"></video>'
            + '</div>';

        document.body.appendChild(overlay);

        const video = overlay.querySelector('video');
        const closeButton = overlay.querySelector('button');
        video.srcObject = stream;

        const closeOverlay = function () {
            if (animationId) {
                cancelAnimationFrame(animationId);
                animationId = null;
            }
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            if (overlay && overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
                overlay = null;
            }
        };

        closeButton.addEventListener('click', closeOverlay);

        const scan = async function () {
            if (!video || video.readyState < 2) {
                animationId = requestAnimationFrame(scan);
                return;
            }

            try {
                const barcodes = await detector.detect(video);
                if (barcodes && barcodes.length > 0) {
                    const rawValue = barcodes[0].rawValue || '';
                    closeOverlay();
                    window.FrenteCaixaAbastecimentoTagForm_applyTagValue(rawValue);
                    return;
                }
            } catch (error) {
            }

            animationId = requestAnimationFrame(scan);
        };

        animationId = requestAnimationFrame(scan);
    } catch (error) {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        if (overlay && overlay.parentNode) {
            overlay.parentNode.removeChild(overlay);
        }
        alert('Nao foi possivel iniciar a leitura do QR Code: ' + error.message);
    }
};

window.FrenteCaixaAbastecimentoTagForm_readNfc = async function () {
    if (!('NDEFReader' in window)) {
        alert('Leitura NFC via navegador está disponível principalmente no Android/Chrome.');
        return;
    }

    try {
        const reader = new NDEFReader();
        await reader.scan();

        reader.onreading = (event) => {
            const serial = event.serialNumber || '';
            const field = document.querySelector('[name="uid_tag"]');
            if (!field) {
                return;
            }

            field.value = serial;
            field.dispatchEvent(new Event('change', { bubbles: true }));

            const buscar = document.querySelector('[name="buscar_tag"]');
            if (buscar) {
                buscar.click();
            }
        };

        alert('Aproxime a TAG NFC do celular para leitura.');
    } catch (error) {
        alert('Não foi possível iniciar a leitura NFC: ' + error.message);
    }
};
JS);

        TTransaction::close();
    }

    public static function onBuscarTag($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $uid = trim((string) ($param['uid_tag'] ?? ''));
            if ($uid === '')
            {
                throw new Exception('Informe o UID da tag para continuar.');
            }

            $resumo = AbastecimentoTagService::obterResumoTag($uid, TSession::getValue('idunit'));

            $data = new stdClass();
            foreach ($resumo as $campo => $valor)
            {
                $data->{$campo} = $campo === 'saldo_atual'
                    ? number_format((float) $valor, 2, ',', '.')
                    : $valor;
            }

            TForm::sendData(self::$formName, $data);

            TTransaction::close();
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onCalcularTotais($param = null)
    {
        $qtde = self::toFloat($param['qtde_litros'] ?? 0);
        $valorLitro = self::toFloat($param['valor_litro'] ?? 0);
        $valorTotal = $qtde * $valorLitro;

        $data = new stdClass();
        $data->valor_total = number_format($valorTotal, 2, ',', '.');

        TForm::sendData(self::$formName, $data, false, false);
    }

    public function onSave($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $this->form->validate();

            $data = $this->form->getData();
            $resultado = AbastecimentoTagService::registrarAbastecimento([
                'uid_tag' => $data->uid_tag,
                'data_abastecimento' => $data->data_abastecimento,
                'estabelecimento_id' => $data->estabelecimento_id,
                'tipo_combustivel_id' => $data->tipo_combustivel_id,
                'km' => $data->km,
                'qtde_litros' => $data->qtde_litros,
                'valor_litro' => $data->valor_litro,
                'descricaopedido' => $data->descricaopedido,
                'obs' => $data->obs,
                'system_users_id' => TSession::getValue('userid'),
                'entidade_id' => TSession::getValue('entidade'),
                'system_unit_id' => TSession::getValue('idunit'),
            ]);

            $data->id = $resultado['pedido_id'];
            $data->valor_total = number_format($resultado['valor_total'], 2, ',', '.');
            $data->saldo_atual = number_format($resultado['saldo_atual'], 2, ',', '.');
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Abastecimento registrado com sucesso. Pedido #' . $resultado['pedido_id'], 'topRight', 'far:check-circle');
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
        }
    }

    public function onClear($param = null)
    {
        try
        {
            $this->form->clear(true);

            $data = new stdClass();
            $data->data_abastecimento = date('d/m/Y H:i');
            $data->qtde_litros = '0,000';
            $data->valor_litro = '0,000';
            $data->valor_total = '0,00';
            $data->saldo_atual = '0,00';
            $data->estabelecimento_id = self::obterEstabelecimentoUsuarioLogadoId();

            $this->form->setData($data);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {
        try
        {
            $data = new stdClass();
            $data->data_abastecimento = date('d/m/Y H:i');
            $data->qtde_litros = '0,000';
            $data->valor_litro = '0,000';
            $data->valor_total = '0,00';
            $data->saldo_atual = '0,00';
            $data->estabelecimento_id = self::obterEstabelecimentoUsuarioLogadoId();

            $this->form->setData($data);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public static function getFormName()
    {
        return self::$formName;
    }

    private static function toFloat($value): float
    {
        if (is_numeric($value))
        {
            return (float) $value;
        }

        $value = str_replace('.', '', (string) $value);
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }
}
