<?php

//<fileHeader>

//</fileHeader>

class DispositivosSolicitadosForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'DispositivosSolicitados';
    private static $primaryKey = 'id';
    private static $formName = 'form_DispositivosSolicitadosForm';
    private static $estadosDisponiveisCache;

    //<classProperties>

    //</classProperties>

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        $podeGerenciarDispositivos = self::usuarioPodeGerenciarDispositivos();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Solicitacao de dispositivo");

        $criteria_system_unit_id = new TCriteria();
        $criteria_system_unit_id->add(new TFilter('id', '=', TSession::getValue('idunit')));
        $criteria_departamento_unit_id = new TCriteria();
        $criteria_departamento_unit_id->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_dispositivos_id = new TCriteria();
        $criteria_status_dispositivos_id = new TCriteria();
        $criteria_veiculos_id = new TCriteria();
        $criteria_veiculos_id->add(new TFilter('status_veiculo_id', '=', 1));
        $criteria_veiculos_id->add(new TFilter('system_unit_id', '=', (int) TSession::getValue('idunit')));
        $criteria_pessoa = new TCriteria();
        $criteria_pessoa->add(new TFilter('id', 'in', "(SELECT pessoa_id 
                                               FROM pessoa_grupo 
                                               WHERE grupo_pessoa_id IN (".GrupoPessoa::CONDUTOR.", ".GrupoPessoa::USUARIODISPOSITIVO."))"));
        $criteria_pessoa->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
        //<onBeginPageCreation>

        //</onBeginPageCreation>

        $id = new TEntry('id');
        $numerocartao = new TEntry('numerocartao');
        $ler_qrcode = new TButton('ler_qrcode');
        $ler_nfc = new TButton('ler_nfc');
        $system_unit_id = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_system_unit_id );
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_departamento_unit_id );
        $datasolicitacao = new TDate('datasolicitacao');
        $dispositivos_id = new TDBCombo('dispositivos_id', 'minierp', 'Dispositivos', 'id', '{descricao}','descricao asc' , $criteria_dispositivos_id );
        $status_dispositivos_id = new TDBCombo('status_dispositivos_id', 'minierp', 'StatusDispositivos', 'id', '{descricao}','descricao asc' , $criteria_status_dispositivos_id );
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa}','placa asc' , $criteria_veiculos_id );
        $pessoa = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa );
        $via = new TSpinner('via');
        $coringa = new TRadioGroup('coringa');
        $rastreio = new TEntry('rastreio');
        $saldo_atual = new TNumeric('saldo_atual', '2', ',', '.');
        $saldo_limite = new TNumeric('saldo_limite', '2', ',', '.');

        $coringa->addValidation("É um dispositivo coringa", new TRequiredValidator()); 
        $pessoa->addValidation("Grupos", new TRequiredValidator()); 

        $datasolicitacao->addValidation("Data da solicitacao", new TRequiredValidator());
        $system_unit_id->addValidation("Unidade", new TRequiredValidator());
        $dispositivos_id->addValidation("Dispositivo", new TRequiredValidator());
        $status_dispositivos_id->addValidation("Status", new TRequiredValidator());

        $id->setEditable(false);
        $saldo_atual->setEditable(false);
        $datasolicitacao->setMask('dd/mm/yyyy');
        $datasolicitacao->setDatabaseMask('yyyy-mm-dd');
        $via->setRange(1, 2000, 1);
        $coringa->addItems(["S"=>"Sim","N"=>"Não"]);
        $coringa->setLayout('horizontal');
       // $coringa->setBooleanMode();
        $coringa->setUseButton();
        $system_unit_id->setValue(TSession::getValue('idunit'));
        $status_dispositivos_id->setValue(1);
        $via->setValue(1);
        $coringa->setValue('N');
        $rastreio->setValue('');
        $numerocartao->setValue('');
        $datasolicitacao->setValue(date('d/m/Y'));
        $saldo_atual->setValue('0,00');
        $saldo_limite->setValue('0,00');
        $numerocartao->setProperty('placeholder', 'Informe o numero do cartao ou UID da TAG');
        $rastreio->setProperty('placeholder', 'Codigo de rastreio, se houver');
        $ler_qrcode->setLabel('Ler QR Code');
        $ler_qrcode->setImage('fas:qrcode #ef6c00');
        $ler_qrcode->addStyleClass('btn-default');
        $ler_qrcode->setProperty('type', 'button');
        $ler_qrcode->setProperty('onclick', 'DispositivosSolicitadosForm_readQrCode(); return false;');
        $ler_nfc->setLabel('Ler NFC');
        $ler_nfc->setImage('fas:mobile-alt #2e7d32');
        $ler_nfc->addStyleClass('btn-default');
        $ler_nfc->setProperty('type', 'button');
        $ler_nfc->setProperty('onclick', 'DispositivosSolicitadosForm_readNfc(); return false;');

        $pessoa->enableSearch();
        $veiculos_id->enableSearch();
        $system_unit_id->enableSearch();
        $dispositivos_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $status_dispositivos_id->enableSearch();

        $id->setSize('100%');
        $via->setSize('100%');
        $pessoa->setSize('100%');
        $coringa->setSize('100%');
        $rastreio->setSize('100%');
        $veiculos_id->setSize('100%');
        $numerocartao->setSize('100%');
        $datasolicitacao->setSize(110);
        $saldo_atual->setSize('100%');
        $saldo_limite->setSize('100%');
        $system_unit_id->setSize('100%');
        $dispositivos_id->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $status_dispositivos_id->setSize('100%');

        //<onBeforeAddFieldsToForm>

        //</onBeforeAddFieldsToForm>
        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Número do cartão / UID Tag:", null, '14px', null, '100%'),$numerocartao, $ler_qrcode, $ler_nfc]);
        $row1->layout = ['col-sm-3','col-sm-9'];

        $row2 = $this->form->addFields([new TLabel("Unidade:", null, '14px', null, '100%'),$system_unit_id],[new TLabel("Departamento:", null, '14px', null, '100%'),$departamento_unit_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Data Solicitação:", null, '14px', null, '100%'),$datasolicitacao],[new TLabel("Dispositivos:", null, '14px', null, '100%'),$dispositivos_id]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Status", null, '14px', null, '100%'),$status_dispositivos_id],[new TLabel("Veículos:", null, '14px', null, '100%'),$veiculos_id]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Via:", null, '14px', null, '100%'),$via],[new TLabel("Coringa:", 'red', '14px', null, '100%'),$coringa],[new TLabel("Rastreio:", null, '14px', null, '100%'),$rastreio]);
        $row5->layout = [' col-sm-3',' col-sm-3','col-sm-6'];

        $row6 = $this->form->addFields([new TLabel("Condutor ou Usuário:", 'Red', '14px', null, '100%'),$pessoa]);
        $row6->layout = [' col-sm-12'];

        $row7 = $this->form->addFields([new TLabel("Saldo atual:", null, '14px', null, '100%'),$saldo_atual],[new TLabel("Saldo limite:", null, '14px', null, '100%'),$saldo_limite]);
        $row7->layout = ['col-sm-6','col-sm-6'];

        $observacao = new TElement('div');
        $observacao->style = 'margin:10px 0 0 0;padding:12px;border:1px solid #d9edf7;background:#f4fbff;color:#245269;border-radius:4px;';
        $observacao->add('Preencha a data da solicitacao, o dispositivo e o status. O numero do cartao/UID pode ser informado no cadastro ou depois, no processo de gravacao da TAG.');
        $this->form->addContent([$observacao]);

        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        if (!$podeGerenciarDispositivos)
        {
            $btn_onsave->setProperty('style', 'display:none !important');
        }

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['DispositivosSolicitadosList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        //<onAfterPageCreation>

        //</onAfterPageCreation>

        parent::add($this->form);

        TScript::create(<<<'JS'
window.DispositivosSolicitadosForm_applyTagValue = function (value) {
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

    const field = document.querySelector('[name="numerocartao"]');
    if (!field) {
        return;
    }

    field.value = serial;
    field.dispatchEvent(new Event('change', { bubbles: true }));
};

window.DispositivosSolicitadosForm_readQrCode = async function () {
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
                    window.DispositivosSolicitadosForm_applyTagValue(rawValue);
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

window.DispositivosSolicitadosForm_readNfc = async function () {
    if (!('NDEFReader' in window)) {
        alert('Leitura NFC via navegador esta disponivel principalmente no Android/Chrome.');
        return;
    }

    try {
        const reader = new NDEFReader();
        await reader.scan();

        reader.onreading = (event) => {
            const serial = event.serialNumber || '';
            const field = document.querySelector('[name="numerocartao"]');
            if (!field) {
                return;
            }

            field.value = serial;
            field.dispatchEvent(new Event('change', { bubbles: true }));
        };

        alert('Aproxime a TAG NFC do celular para leitura.');
    } catch (error) {
        alert('Nao foi possivel iniciar a leitura NFC: ' + error.message);
    }
};
JS);

    }

//<generated-FormAction-onSave>
    public function onSave($param = null) 
    {
        try
        {
            self::validarPermissaoGerenciarDispositivos('salvar dispositivos solicitados');

            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new DispositivosSolicitados(); // create an empty object //</blockLine>

            $data = $this->form->getData(); // get form data as array
            $data->datasolicitacao = self::normalizeDateToDatabase($data->datasolicitacao ?? null);
            $object->fromArray( (array) $data); // load the object with data
            $object->numerocartao = trim((string) $object->numerocartao) ?: null;
            $object->rastreio = trim((string) $object->rastreio) ?: null;
            $object->saldo_atual = (float) $object->saldo_atual;
            $object->saldo_limite = (float) $object->saldo_limite;

            if ((float) $object->saldo_atual < 0)
            {
                throw new Exception('O saldo atual nao pode ser negativo.');
            }

            if ((float) $object->saldo_limite < 0)
            {
                throw new Exception('O saldo limite nao pode ser negativo.');
            }

            if ((float) $object->saldo_limite > 0 && (float) $object->saldo_atual > (float) $object->saldo_limite)
            {
                throw new Exception('O saldo atual nÃ£o pode ser maior que o saldo limite.');
            }

            if (!empty($object->numerocartao))
            {
                $registroExistente = DispositivosSolicitados::where('numerocartao', '=', $object->numerocartao)
                    ->where('id', '<>', (int) ($object->id ?? 0))
                    ->first();

                if ($registroExistente)
                {
                    throw new Exception('Ja existe outro dispositivo com este numero do cartao ou UID.');
                }
            }

            //</beforeStoreAutoCode> //</blockLine>

            if (empty($object->system_users_id))
            {
                $object->system_users_id = TSession::getValue('userid');
            }

            $object->store(); // save the object //</blockLine>

            //</afterStoreAutoCode> //</blockLine>
 //<generatedAutoCode>

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

//</generatedAutoCode>

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; //</blockLine>

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            //</messageAutoCode> //</blockLine>
//<generatedAutoCode>
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('DispositivosSolicitadosList', 'onShow', $loadPageParam);
//</generatedAutoCode>

            //</endTryAutoCode> //</blockLine>
//<generatedAutoCode>
            TScript::create("Template.closeRightPanel();");
//</generatedAutoCode>

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> //</blockLine>

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
//</generated-FormAction-onSave>

//<generated-onEdit>
    public function onEdit( $param )//</ini>
    {
        try
        {
            self::validarPermissaoGerenciarDispositivos('editar dispositivos solicitados');

            if (isset($param['key']) || isset($param['id']))
            {
                $key = $param['key'] ?? $param['id'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new DispositivosSolicitados($key); // instantiates the Active Record //</blockLine>

                //</beforeSetDataAutoCode> //</blockLine>
                if (!empty($object->datasolicitacao))
                {
                    $object->datasolicitacao = self::normalizeDateToDisplay($object->datasolicitacao);
                }

                $this->form->setData($object); // fill the form //</blockLine>

                //</afterSetDataAutoCode> //</blockLine>
                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }//</end>
//</generated-onEdit>

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

        $data = new stdClass();
        $data->system_unit_id = TSession::getValue('idunit');
        $data->status_dispositivos_id = 1;
        $data->via = 1;
        $data->coringa = 'N';
        $data->datasolicitacao = date('d/m/Y');
        $data->saldo_atual = '0,00';
        $data->saldo_limite = '0,00';
        $this->form->setData($data);

        //<onFormClear>

        //</onFormClear>

    }

    public function onShow($param = null)
    {
        if ((empty($param['key']) && empty($param['id'])) && !self::usuarioPodeGerenciarDispositivos())
        {
            new TMessage('warning', 'Somente aprovadores de dispositivos podem incluir novos registros.');
            TApplication::loadPage('DispositivosSolicitadosList', 'onShow');
            return;
        }

        if (empty($param['key']) && empty($param['id']))
        {
            $data = new stdClass();
            $data->system_unit_id = TSession::getValue('idunit');
            $data->status_dispositivos_id = 1;
            $data->via = 1;
            $data->coringa = 'N';
            $data->datasolicitacao = date('d/m/Y');
            $data->saldo_atual = '0,00';
            $data->saldo_limite = '0,00';
            $this->form->setData($data);
        }

        //<onShow>

        //</onShow>
    } 

    public static function getFormName()
    {
        return self::$formName;
    }

    //</hideLine> <addUserFunctionsCode/>

    //<userCustomFunctions>
    private static function normalizeDateToDatabase($value)
    {
        if (empty($value))
        {
            return date('Y-m-d');
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value))
        {
            return $value;
        }

        $date = DateTime::createFromFormat('d/m/Y', $value);

        return $date ? $date->format('Y-m-d') : date('Y-m-d');
    }

    private static function normalizeDateToDisplay($value)
    {
        if (empty($value))
        {
            return '';
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value))
        {
            return $value;
        }

        $date = DateTime::createFromFormat('Y-m-d', $value);

        return $date ? $date->format('d/m/Y') : $value;
    }

    private static function usuarioPodeGerenciarDispositivos()
    {
        try
        {
            $openTransaction = TTransaction::getDatabase() != self::$database;

            if ($openTransaction)
            {
                TTransaction::open(self::$database);
            }

            $user = new SystemUsers((int) TSession::getValue('userid'));
            $permitido = ($user->login ?? null) === 'admin'
                || in_array(EstadoPedidoFrotas::APROVACAODISPOSITIVO, self::getEstadosDisponiveisCache());

            if ($openTransaction)
            {
                TTransaction::close();
            }

            return $permitido;
        }
        catch (Exception $e)
        {
            if (TTransaction::getDatabase() == self::$database)
            {
                TTransaction::rollback();
            }

            return false;
        }
    }

    private static function getEstadosDisponiveisCache()
    {
        if (self::$estadosDisponiveisCache === null)
        {
            self::$estadosDisponiveisCache = AprovadorFrotas::getEstadosDisponiveis();
        }

        return self::$estadosDisponiveisCache;
    }

    private static function validarPermissaoGerenciarDispositivos($acao)
    {
        if (!self::usuarioPodeGerenciarDispositivos())
        {
            throw new Exception("Somente aprovadores de dispositivos podem {$acao}.");
        }
    }

    //</userCustomFunctions>

}
