<?php

class EntidadeForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Entidade';
    private static $primaryKey = 'id';
    private static $formName = 'form_EntidadeForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de Entidade");

        $criteria_administradora_id = new TCriteria();
        $criteria_cidade_id = new TCriteria();

        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $cnpj = new TEntry('cnpj');
        $email = new TEntry('email');
        $cep = new TEntry('cep');
        $button_buscar_endereco = new TButton('button_buscar_endereco');
        $rua = new TEntry('rua');
        $numero = new TEntry('numero');
        $bairro = new TEntry('bairro');
        $complemento = new TEntry('complemento');
        $cidade_id = new TDBCombo('cidade_id', 'minierp', 'Cidade', 'id', '{nome} - {estado->sigla}','nome asc' , $criteria_cidade_id );
        $telefone01 = new TEntry('telefone01');
        $telefone02 = new TEntry('telefone02');
        $numero_documento = new TEntry('numero_documento');
        $numero_processo = new TEntry('numero_processo');
        $administradora = new TDBCombo('administradora_id', 'minierp', 'Administradora', 'id', '{nome}', 'nome asc', $criteria_administradora_id);
        $latitude = new TEntry('latitude');
        $longitude = new TEntry('longitude');
        $compras = new TCheckButton('compras');
        $abastecimento = new TCheckButton('abastecimento');
        $frotas = new TCheckButton('frotas');
        $taxacontrato = new TNumeric('taxacontrato', '2', ',', '.' );
        $tipo_frota = new TRadioGroup('tipo_frota');
       // $tipo_frota->setValue('1');

        $compras->addValidation("Compras", new TRequiredValidator()); 
        $nome->addValidation("nome", new TRequiredValidator()); 
        $frotas->addValidation("Frotas", new TRequiredValidator()); 
        $taxacontrato->addValidation("Taxa contrato", new TRequiredValidator()); 
        $tipo_frota->addItems(["1"=>"Veículo","2"=>"Aeronave","3"=>"Equipamentos"]);
        $tipo_frota->setLayout('vertical');
        $tipo_frota->setSize(200);

        $id->setEditable(false);
        $cep->setMaxLength(10);
        $cnpj->setMaxLength(50);
        $rua->setMaxLength(500);
        $nome->setMaxLength(200);
        $email->setMaxLength(150);
        $numero->setMaxLength(10);
        $bairro->setMaxLength(500);
        $telefone01->setMaxLength(255);
        $telefone02->setMaxLength(255);
        $complemento->setMaxLength(500);
        // $cidade_id->setMaxLength(20);
 $frotas->setUseSwitch(true, 'blue');
        $compras->setUseSwitch(true, 'blue');
        $abastecimento->setUseSwitch(true, 'blue');


        $frotas->setIndexValue("1");
        $compras->setIndexValue("1");
        $abastecimento->setIndexValue("1");


        $frotas->setInactiveIndexValue("2");
        $compras->setInactiveIndexValue("2");
        $abastecimento->setInactiveIndexValue("2");
        $administradora->enableSearch();
        $cidade_id->enableSearch();

        $button_buscar_endereco->setAction(new TAction([$this, 'onBuscarCep']), "Buscar");
        // $frotas->setChangeAction(new TAction([$this, 'onTipoFrota']));
        // $compras->setChangeAction(new TAction([$this,'onTIpoFrota']));

        $button_buscar_endereco->setImage('fas:search #000000');

        // $cep->setValue('NULL');
        // $rua->setValue('NULL');
        // $nome->setValue('NULL');
        // $cnpj->setValue('NULL');
        // $email->setValue('NULL');
        // $numero->setValue('NULL');
        // $bairro->setValue('NULL');
        // $cidade_id->setValue('NULL');
        // $telefone01->setValue('NULL');
        // $telefone02->setValue('NULL');
        // $complemento->setValue('NULL');

        $id->setSize(100);
        // $cep->setSize('100%');
        $rua->setSize('100%');
        $nome->setSize('100%');
        $cep->setSize('61%');
        $cnpj->setSize('100%');
        $email->setSize('100%');
        $numero->setSize('100%');
        $bairro->setSize('100%');
        $latitude->setSize('100%');
        $longitude->setSize('100%');
        $cidade_id->setSize('100%');
        $telefone01->setSize('100%');
        $telefone02->setSize('100%');
        $complemento->setSize('100%');
        $administradora->setSize('100%');
        $frotas->setValue('2');
        $compras->setValue('2');
        $abastecimento->setValue('2');
        $taxacontrato->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', '100%')],[$id],[new TLabel("Nome: *", '#FF0000' , '14px', '100%')],[$nome]);
        $row2 = $this->form->addFields([new TLabel("Cnpj:", null, '14px', '100%')],[$cnpj],[new TLabel("Email:", null, '14px', '100%')],[$email]);
        $row3 = $this->form->addFields([new TLabel("Cep:", null, '14px', '100%')],[$cep, $button_buscar_endereco],[new TLabel("Rua:", null, '14px', '100%')],[$rua]);
        $row4 = $this->form->addFields([new TLabel("Numero:", null, '14px', '100%')],[$numero],[new TLabel("Bairro:", null, '14px', '100%')],[$bairro]);
        $row5 = $this->form->addFields([new TLabel("Complemento:", null, '14px', '100%')],[$complemento],[new TLabel("Cidade:", null, '14px', '100%')],[$cidade_id]);
        $row6 = $this->form->addFields([new TLabel("Telefone01:", null, '14px', '100%')],[$telefone01],[new TLabel("Telefone02:", null, '14px', '100%')],[$telefone02]);
        $row7 = $this->form->addFields([new TLabel("Longitude:", null, '14px', '100%')], [$longitude], [new TLabel("Latitude:", null, '14px', '100%')], [$latitude]);
        $row8 = $this->form->addFields([new TLabel("Administradora:", null, '14px', '100%')],[$administradora]);
       
        $row11 = $this->form->addFields([new TLabel("Número Documento:", null, '14px', '100%')],[$numero_documento],[new TLabel("Número Processo:", null, '14px', '100%')],[$numero_processo]);

        $row60 = $this->form->addFields([new TFormSeparator("<br>Parâmetros", '#333', '18', '#eee')]);

        $row9 = $this->form->addFields([new TLabel("Compras?", '#FF0000', '14px', '100%')],[$compras],[new TLabel("Frotas?", '#FF0000', '14px', '100%')],[$frotas]);
        $row10 = $this->form->addFields([new TLabel("Tipo de Frota ?", null, '14px', '100%')],[$tipo_frota],[new TLabel("Taxa Contrato (%): *", '#FF0000', '14px', '100%')],[$taxacontrato]);
        $row11 = $this->form->addFields([new TLabel("Abastecimento?", '#FF0000', '14px', '100%')],[$abastecimento],[]);


        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['EntidadeList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);
        
        $style = new TStyle('right-panel > .container-part[page-name=EntidadeForm]');
        $style->width = '60% !important';   
        $style->show(true);

    }

    // public function onBuscaEndereco($param)
    // {
    //     try
    //     {
    //         if(!empty($param['cep']))
    //         {

    //             TTransaction::open(self::$database);
    //             $cep = $param['cep'];
    //             self::onBuscarCoordenadas($cep);
    //             $dadoscep = CEPService::get($param['cep']);

    //             if($dadoscep)
    //             {

    //                 $object = new stdClass();
    //                 $object->cidade_id = $dadoscep->cidade_id;
    //                 $object->bairro = $dadoscep->bairro;
    //                 $object->rua = $dadoscep->rua;
    //                 $object->cep = $dadoscep->cep;

    //                 TForm::sendData(self::$formName, $object);
                    
    //             }
    //             TTransaction::close(self::$database);
    //         }
    //     }
    //     catch (exception $e)
    //     {
    //         new TMessage('error', $e->getMessage());
    //     }
    // }

    // public static function onBuscarCoordenadas($cepRecebido)
    // {
    //     try {
    //         $cep = preg_replace('/[^0-9]/', '', $cepRecebido);

    //         if (!$cep) {
    //             throw new Exception("Informe um CEP válido.");
    //         }

    //         // 📦 Buscar endereço pelo ViaCEP
    //         $viacep_url = "https://viacep.com.br/ws/{$cep}/json/";

    //         $ch = curl_init();
    //         curl_setopt($ch, CURLOPT_URL, $viacep_url);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //         $response = curl_exec($ch);
    //         curl_close($ch);

    //         $endereco_data = json_decode($response, true);

    //         if (!is_array($endereco_data)) {
    //             new TMessage('error', "CEP não encontrado.");
    //             return;
    //         }

    //         if (isset($endereco_data['erro'])) {
    //             new TMessage('error', "CEP não encontrado.");
    //             return;
    //         }

    //         // 🔽 Monta o endereço detalhado
    //         $endereco_parts = [];
    //         if (!empty($endereco_data['logradouro'])) $endereco_parts[] = $endereco_data['logradouro'];
    //         if (!empty($endereco_data['bairro']))     $endereco_parts[] = $endereco_data['bairro'];
    //         if (!empty($endereco_data['localidade'])) $endereco_parts[] = $endereco_data['localidade'];
    //         if (!empty($endereco_data['uf']))         $endereco_parts[] = $endereco_data['uf'];
    //         $endereco_parts[] = 'Brasil';

    //         $endereco = implode(', ', $endereco_parts);

    //         // new TMessage('info', $endereco);

    //         // 🌍 Tenta buscar coordenadas com endereço detalhado
    //         $nominatim_url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($endereco) . "&format=json&limit=1";
    //         $geo_data_raw = self::fetchDataWithCurl($nominatim_url);
    //         // new TMessage('info', $geo_data_raw);
    //         $geo_data = json_decode($geo_data_raw, true);

    //         // 🔁 Se falhar, tenta apenas cidade + estado
    //         if (empty($geo_data)) {
    //             $endereco = "{$endereco_data['localidade']}, {$endereco_data['uf']}, Brasil";
    //             $nominatim_url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($endereco) . "&format=json&limit=1";
    //             $geo_data_raw = self::fetchDataWithCurl($nominatim_url);
    //             $geo_data = json_decode($geo_data_raw, true);

    //             if (!empty($geo_data)) {
    //                 new TMessage('info', 'Endereço detalhado não localizado. Usando coordenadas aproximadas da cidade.');                    
    //             }
    //         }

    //         // ❌ Ainda falhou?
    //         if (empty($geo_data) || !isset($geo_data[0]['lat']) || !isset($geo_data[0]['lon'])) {
    //             new TMessage('error', "A API Nominatim não retornou coordenadas. Resposta: " . htmlentities($geo_data_raw));
    //             return;
    //         }

    //         // ✅ Sucesso! Latitude e Longitude encontradas
    //         $latitude = $geo_data[0]['lat'];
    //         $longitude = $geo_data[0]['lon'];

    //         $object = new stdClass();
    //         $object->latitude = $latitude;
    //         $object->longitude = $longitude;

    //         // Busca cidade por nome + UF
    //         $cidade = Cidade::where('nome', '=', $endereco_data['localidade'])->load();
    //         if ($cidade) {
    //             $object->cidade_id = $cidade[0]->id;
    //         }

    //         // Preenche outros campos de endereço
    //         $object->rua = $endereco_data['logradouro'] ?? '';
    //         $object->bairro = $endereco_data['bairro'] ?? '';

    //         TForm::sendData(self::$formName, $object);

    //     } catch (Exception $e) {
    //         new TMessage('error', $e->getMessage());
    //     }
    // }

    // public static function fetchDataWithCurl($url)
    // {
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout de 10s
    //     curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    //     $response = curl_exec($ch);
    //     $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     curl_close($ch);

    //     // Se a resposta estiver vazia ou erro de conexão, exibir mensagem
    //     if ($response === false || $http_status != 200) {
    //         return json_encode(["error" => "Falha ao acessar API. HTTP Status: $http_status"]);
    //     }

    //     return $response;
    // }

    public static function onBuscarCep($param)
    {
        try
        {
            if(!empty($param['cep']))
            {
                TTransaction::open(self::$database);
                $cep = $param['cep'];
                self::onBuscarCoordenadasCep($cep);
                $dadoscep = CEPService::get($param['cep']);

                if($dadoscep)
                {
                    $object = new stdClass();
                    $object->rua = $dadoscep->rua;
                    $object->bairro = $dadoscep->bairro;
                    $object->cidade_id = $dadoscep->cidade_id;

                    TForm::sendData(self::$formName, $object);
                }

                TTransaction::close(self::$database);
            }
            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onBuscarCoordenadasCep($requestCep)
    {
        try
        {
            $cep = preg_replace('/[^0-9]/', '', $requestCep);

            if(!$cep)
            {
                throw new Exception("Cep não existe.");
            }

            $viacep_url = "https://viacep.com.br/ws/{$cep}/json/";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $viacep_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $enderecoCep = json_decode($response, true);

            if(!is_array($enderecoCep))
            {
                new TMessage('error', "Cep não encontrado");
                return;
            }

            if(isset($enderecoCep['erro']))
            {
                new TMessage('error', "Cep não encontrado");
                return;
            }

            $endereco_parts = [];
            if(!empty($enderecoCep['logradouro'])) $endereco_parts[] = $enderecoCep['logradouro'];
            if(!empty($enderecoCep['bairro']))     $endereco_parts[] = $enderecoCep['bairro'];
            if(!empty($enderecoCep['localidade'])) $endereco_parts[] = $enderecoCep['localidade'];
            if(!empty($enderecoCep['uf']))         $endereco_parts[] = $enderecoCep['uf'];
            $endereco_parts[] = 'Brasil';

            $endereco = implode(', ', $endereco_parts);
            // new TMessage('info', $endereco);

            $sitecoord = "https://nominatim.openstreetmap.org/search?q=" . urlencode($endereco) . "&format=json&limit=1";
            $verifyapi = self::fetchDataWithCurl($sitecoord);
            $geo_data = json_decode($verifyapi, true);

            // $geo = implode(', ', $geo_data);
            // new TMessage('info', $geo);

            if(empty($geo_data))
            {
                $geo = "{$enderecoCep['localidade']}, {$enderecoCep['uf']}, Brasil";
                $sitecoord = "https://nominatim.openstreetmap.org/search?q=" . urlencode($geo) . "&format=json&limit=1";
                $verifyapi = self::fetchDataWithCurl($sitecoord);
                $geo_data = json_decode($verifyapi, true);

                if(!empty($geo_data))
                {
                    new TMessage('info', "Localizamos as cordenadas por regiões aproximadas.");
                    
                }
            }

            if(empty($geo_data) || !isset($geo_data[0]['lat']) || !isset($geo_data[0]['lon']))
            {
                new TMessage('error', "Cep não encontrado");
                return;
            }   

            $longitude = $geo_data[0]['lon'];
            $latitude = $geo_data[0]['lat'];
            
            $object = new stdClass;
            $object->longitude = $longitude;
            $object->latitude = $latitude;
            
            TForm::sendData(self::$formName, $object);
            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public static function fetchDataWithCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout de 10s
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Se a resposta estiver vazia ou erro de conexão, exibir mensagem
        if ($response === false || $http_status != 200) {
            return json_encode(["error" => "Falha ao acessar API. HTTP Status: $http_status"]);
        }

        return $response;
    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Entidade(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data
           
            $toNum = function($v){
                $s = strtoupper((string)$v);
                if (in_array($s, ['1','Y','ON','TRUE'], true)) return 1;
                if ($s === '2') return 2;
                return (int)$v;
            };

            $frotas  = $toNum($data->frotas  ?? 2);
            $compras = $toNum($data->compras ?? 2);

            // Exige que exatamente um seja 1
            $isFrotas  = ($frotas === 1);
            $isCompras = ($compras === 1);
            if (($isFrotas xor $isCompras) === false) {
                throw new Exception('Selecione apenas UMA opção: Frotas (=1) OU Compras (=1). A outra ficará inativa (=2).');
            }

            // Se frotas=1, exige tipo_frota; se compras=1, limpa tipo_frota
            if ($isFrotas) {
                if (empty($data->tipo_frota)) {
                    throw new Exception('Selecione o Tipo de Frota.');
                }
                $object->compras = 2;
                $object->frotas  = 1;
            } else { // compras=1
                $object->frotas     = 2;
                $object->compras    = 1;
                $object->tipo_frota = null;
            }

            if (!empty($data->id))
            {
                $entidadeDB = new Entidade($data->id);

                // valor ORIGINAL do banco
                $taxaDB = $entidadeDB->taxacontrato;

                // valor que veio do formulário
             //   $novaTaxa = $data->taxacontrato;

                $propostas = Propostas::where('system_unit_id','=',TSession::getValue('idunit'))
                                      ->where('desconto_contratual','=',$taxaDB)
                                      ->load();
                if (($propostas) and ($taxaDB != $data->taxacontrato))
                {
                    throw new Exception(
                        'A Taxa Contratual não pode ser alterada, pois existe proposta utilizando esta taxa contratual.'
                    );
                }

                /**
                 * REGRA:
                 * - Se no banco NÃO é NULL
                 * - E o usuário tentou alterar
                 */
              //  if ($taxaDB !== null && (float)$taxaDB !== (float)$novaTaxa)
               // {
              //      // restaura o valor original no formulário
               //     $data->taxacontrato = $taxaDB;
               //     $this->form->setData($data);

                    
             //   }
            }
            
            $object->store(); // save the object 

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('EntidadeList', 'onShow', $loadPageParam); 

                        TScript::create("Template.closeRightPanel();"); 
        }
        catch (Exception $e) // in case of exception
        {

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Entidade($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form 

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
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function onShow($param = null)
    {

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

    // public static function onTipoFrota($param = null) 
    // {
    //     try 
    //     {
    //         //code here
    //        if (isset($param['frotas']) && isset($param['compras'])) {
    //         if ($param['frotas']==1) {
    //             $object = new stdClass;
    //             $object->tipo_frota = null;
    //             $object->compras = null;
    //             TForm::sendData(self::$formName, $object);
    //         } else if ($param['compras']==1) {
    //             $object = new stdClass;
    //             $object->tipo_frota = null;
    //             $object->frotas = null;
    //             TForm::sendData(self::$formName, $object);
    //         }
    //         }

    //     }
    //     catch (Exception $e) 
    //     {
    //         new TMessage('error', $e->getMessage());    
    //     }
    // }

    // public static function onTipoFrota($param = null)
    // {
    //     try {
    //         // Normaliza booleanos que podem vir como '1','on','S','Y', true, etc.
    //         $isTrue = function($v) {
    //             return in_array(strtoupper((string)$v), ['1','ON','S','Y','TRUE'], true);
    //         };

    //         $source  = $param['source']  ?? null;    // 'frotas' ou 'compras' (quem disparou)
    //         $frotas  = $isTrue($param['frotas']  ?? 0) ? 1 : 0;
    //         $compras = $isTrue($param['compras'] ?? 0) ? 1 : 0;

    //         // Se ambos vieram marcados, prevalece quem disparou a ação
    //         if ($frotas && $compras) {
    //             if ($source === 'frotas') {
    //                 $compras = 0;
    //             } elseif ($source === 'compras') {
    //                 $frotas = 0;
    //             } else {
    //                 // fallback: define uma prioridade (ex.: prioriza frotas)
    //                 $compras = 0;
    //             }
    //         }

    //         // Regras de limpeza de campos dependentes
    //         $tipo_frota = null;
    //         if ($frotas === 1) {
    //             // quando é frota, pode manter/validar tipo_frota
    //             // (aqui só não limpamos; se quiser exigir, valide no onSave)
    //             $tipo_frota = $param['tipo_frota'] ?? null;
    //         } else {
    //             // não é frota => limpar tipo_frota
    //             $tipo_frota = null;
    //         }

    //         // Devolve os valores coerentes pro formulário
    //         $obj = new stdClass;
    //         $obj->frotas     = $frotas;
    //         $obj->compras    = $compras;
    //         $obj->tipo_frota = $tipo_frota;

    //         TForm::sendData(self::$formName, $obj);

    //         // (Opcional) se preferir avisar quando o usuário tentar marcar ambos:
    //         // if (($param['frotas'] ?? null) && ($param['compras'] ?? null)) {
    //         //     new TMessage('info', 'Selecione apenas uma opção: Frotas OU Compras.');
    //         // }
    //     }
    //     catch (Exception $e) {
    //         new TMessage('error', $e->getMessage());
    //     }
    // }

  


}

