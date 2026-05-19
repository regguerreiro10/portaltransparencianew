<?php

class PropostasInicioServicoForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Propostas';
    private static $primaryKey = 'id';
    private static $formName = 'form_PropostasInicioServicoForm';

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
        $this->form->setFormTitle("Aguardando registro de início do serviço");

        $criteria_pessoa_id = new TCriteria();
        $criteria_estado_pedido_frotas_id = new TCriteria();
        $criteria_veiculos_id = new TCriteria();
        $criteria_motorista_entrada_id = new TCriteria();
        $subquery = "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = " . GrupoPessoa::CONDUTOR . ")";
        $criteria_motorista_entrada_id->add(new TFilter('id', 'IN', $subquery));
        $criteria_motorista_entrada_id->add(new TFilter('system_unit_id', '=',TSession::getValue('idunit')));

        $criteria_motorista_retirada_id = new TCriteria();
        $subquery = "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = " . GrupoPessoa::CONDUTOR . ")";
        $criteria_motorista_retirada_id->add(new TFilter('id', 'IN', $subquery));
        $criteria_motorista_retirada_id->add(new TFilter('system_unit_id', '=',TSession::getValue('idunit')));

        $id = new TEntry('id');
        $pedido_frotas_id = new TEntry('pedido_frotas_id');
        $horimetro_inicioservico = new TEntry('horimetro_inicioservico');
        $ciclos_inicioservico = new TEntry('ciclos_inicioservico');
        $horimetro_fimservico = new TEntry('horimetro_fimservico');
        $ciclos_fimservico = new TEntry('ciclos_fimservico');
        $ciclos = new TEntry('ciclos');

        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa_id );
        $estado_pedido_frotas_id = new TDBCombo('estado_pedido_frotas_id', 'minierp', 'EstadoPedidoFrotas', 'id', '{nome}','id asc' , $criteria_estado_pedido_frotas_id );
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa}','placa asc' , $criteria_veiculos_id );
        $data_cotacao = new TDate('data_cotacao');
        $datahora_inicioservico = new TDateTime('datahora_inicioservico');
        $datahora_fimservico = new TDateTime('datahora_fimservico');
        $data_entrada_veiculo = new TDate('data_entrada_veiculo');
        $horimetro_entrada_aeronave = new TEntry('horimetro_entrada_aeronave');
        $ciclos_entrada_aeronave = new TEntry('ciclos_entrada_aeronave');
        $horimetro_retirada_aeronave = new TEntry('horimetro_retirada_aeronave');
        $ciclos_retirada_aeronave = new TEntry('ciclos_retirada_aeronave');

        $motorista_entrada_id = new TDBCombo('motorista_entrada_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_motorista_entrada_id );
        $km = new TEntry('km');
        $responsavel_tecnico = new TEntry('responsavel_tecnico');
        $data_retirada_veiculo = new TDateTime('data_retirada_veiculo');
        $motorista_retirada_id = new TDBCombo('motorista_retirada_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_motorista_retirada_id );
        $senha_motorista_entrada = new TPassword('senha_motorista_entrada');
        $senha_motorista_entrada->setExitAction(new TAction([$this, 'onValidarSenhaMotoristaEntrada']));

        $senha_motorista_retirada = new TPassword('senha_motorista_retirada');
        $senha_motorista_retirada->setExitAction(new TAction([$this, 'onValidarSenhaMotoristaRetirada']));

  //           $senha_motorista_entrada->addValidation("Senha do motorista entrada", new TRequiredValidator()); 
 //       $senha_motorista_retirada->addValidation("Senha do motorista retirada", new TRequiredValidator()); 

        $responsavel_tecnico->setMaxLength(255);
        $km->setValue('NULL');
        $data_cotacao->setValue('NULL');
        $data_entrada_veiculo->setValue('NULL');
        $data_retirada_veiculo->setValue('NULL');

        $pessoa_id->enableSearch();
        $veiculos_id->enableSearch();
        $motorista_entrada_id->enableSearch();
        $motorista_retirada_id->enableSearch();
        $estado_pedido_frotas_id->enableSearch();

        $data_cotacao->setMask('dd/mm/yyyy');
        $data_entrada_veiculo->setMask('dd/mm/yyyy');
        $datahora_fimservico->setMask('dd/mm/yyyy hh:ii');
        $data_retirada_veiculo->setMask('dd/mm/yyyy hh:ii');
        $datahora_inicioservico->setMask('dd/mm/yyyy hh:ii');

        $data_cotacao->setDatabaseMask('yyyy-mm-dd');
        $data_entrada_veiculo->setDatabaseMask('yyyy-mm-dd');
        $datahora_fimservico->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_retirada_veiculo->setDatabaseMask('yyyy-mm-dd hh:ii');
        $datahora_inicioservico->setDatabaseMask('yyyy-mm-dd hh:ii');

        $id->setEditable(false);
        $pessoa_id->setEditable(false);
        $veiculos_id->setEditable(false);
        $data_cotacao->setEditable(false);
        $pedido_frotas_id->setEditable(false);
        $estado_pedido_frotas_id->setEditable(false);

        $id->setSize(100);
        $km->setSize('100%');
        $pessoa_id->setSize('100%');
        $data_cotacao->setSize(110);
        $veiculos_id->setSize('100%');
        $pedido_frotas_id->setSize('100%');
        $datahora_fimservico->setSize(150);
        $data_entrada_veiculo->setSize(110);
        $data_retirada_veiculo->setSize(150);
        $datahora_inicioservico->setSize(150);
        $responsavel_tecnico->setSize('100%');
        $motorista_entrada_id->setSize('100%');
        $motorista_retirada_id->setSize('100%');
        $estado_pedido_frotas_id->setSize('100%');
        $senha_motorista_entrada->setSize('100%');
        $senha_motorista_retirada->setSize('100%');
        $horimetro_entrada_aeronave->setSize('100%');
        $horimetro_retirada_aeronave->setSize('100%');

        $ciclos_entrada_aeronave->setSize('100%');
        $ciclos_retirada_aeronave->setSize('100%');
        $horimetro_inicioservico->setSize('100%');
        $ciclos_inicioservico->setSize('100%');
        $horimetro_fimservico->setSize('100%');
        $ciclos_fimservico->setSize('100%');
        $ciclos->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("ID Pedido:", null, '14px', null, '100%'),$pedido_frotas_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Pessoa:", null, '14px', null, '100%'),$pessoa_id],[new TLabel("Estado pedido frotas id:", null, '14px', null, '100%'),$estado_pedido_frotas_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Placa:", null, '14px', null, '100%'),$veiculos_id],[new TLabel("Data proposta:", null, '14px', null, '100%'),$data_cotacao]);
        $row3->layout = ['col-sm-6','col-sm-6'];
         if (TSession::getValue('tipofrota')==2) {
            $row4 = $this->form->addFields([new TLabel("Data e Hora de início do serviço:", null, '14px', null, '100%'),$datahora_inicioservico],[new TLabel("Horimetro início do serviço:", null, '14px', null, '100%'),$horimetro_inicioservico], [new TLabel("Ciclo inicio do serviço:", null, '14px', null, '100%'),$ciclos_inicioservico]);
            $row4->layout = ['col-sm-4','col-sm-4','col-sm-4'];

            $row40 = $this->form->addFields([new TLabel("Data e Hora de fim do serviço:", null, '14px', null, '100%'),$datahora_fimservico],[new TLabel("Horimetro final do serviço:", null, '14px', null, '100%'),$horimetro_fimservico], [new TLabel("Ciclo fim do serviço:", null, '14px', null, '100%'),$ciclos_fimservico]);
            $row40->layout = ['col-sm-4','col-sm-4','col-sm-4'];

            $row5 = $this->form->addFields([new TLabel("Data de entrada:", null, '14px', null, '100%'),$data_entrada_veiculo],[new TLabel("Horimetro de entrada:", null, '14px', null, '100%'),$horimetro_entrada_aeronave],[new TLabel("Ciclo de Entrada:", null, '14px', null, '100%'),$ciclos_entrada_aeronave]);
            $row5->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

             $row50 = $this->form->addFields([new TLabel("Condutor entrada :", null, '14px', null, '100%'),$motorista_entrada_id],[new TLabel("Senha Condutor entrada:", null, '14px', null, '100%'),$senha_motorista_entrada]);
             $row50->layout = [' col-sm-6',' col-sm-6'];


            $row7 = $this->form->addFields([new TLabel("Data retirada:", null, '14px', null, '100%'),$data_retirada_veiculo],[new TLabel("Horimetro retirada:", null, '14px', null, '100%'),$horimetro_retirada_aeronave],[new TLabel("Ciclo retirada:", null, '14px', null, '100%'),$ciclos_retirada_aeronave]);
            $row7->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

            $row70 = $this->form->addFields([new TLabel("Condutor retirada:", null, '14px', null, '100%'),$motorista_retirada_id],[new TLabel("Condutor retirada:", null, '14px', null, '100%'),$senha_motorista_retirada]);
            $row70->layout = [' col-sm-6','col-sm-6'];

            $row6 = $this->form->addFields([new TLabel("Horimetro:", null, '14px', null, '100%'),$km], [new TLabel("Ciclos:", null, '14px', null, '100%'),$ciclos], [new TLabel("Responsavel técnico:", null, '14px', null, '100%'),$responsavel_tecnico]);
            $row6->layout = ['col-sm-4','col-sm-4','col-sm-4'];
         } else {
            $row4 = $this->form->addFields([new TLabel("Data e Hora de início do serviço:", null, '14px', null, '100%'),$datahora_inicioservico],[new TLabel("Data e Hora de fim do serviço:", null, '14px', null, '100%'),$datahora_fimservico]);
            $row4->layout = ['col-sm-6','col-sm-6'];

            $row5 = $this->form->addFields([new TLabel("Data de entrada:", null, '14px', null, '100%'),$data_entrada_veiculo],[new TLabel("Condutor entrada id:", null, '14px', null, '100%'),$motorista_entrada_id],[new TLabel("Senha Condutor entrada id:", null, '14px', null, '100%'),$senha_motorista_entrada]);
            $row5->layout = [' col-sm-6',' col-sm-3',' col-sm-3'];

            $row6 = $this->form->addFields([new TLabel("Km:", null, '14px', null, '100%'),$km],[new TLabel("Responsavel técnico:", null, '14px', null, '100%'),$responsavel_tecnico]);
            $row6->layout = ['col-sm-6','col-sm-6'];

            $row7 = $this->form->addFields([new TLabel("Data retirada:", null, '14px', null, '100%'),$data_retirada_veiculo],[new TLabel("Condutor retirada:", null, '14px', null, '100%'),$motorista_retirada_id],[new TLabel("Condutor retirada:", null, '14px', null, '100%'),$senha_motorista_retirada]);
            $row7->layout = [' col-sm-6',' col-sm-3',' col-sm-3'];
         }
        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

       // $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
    //    $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['PropostasDisponiveisList', 'onShow']), 'fas:arrow-left #000000');
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

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Propostas(TSession::getValue('propostas_id'));
            // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            $pessoa = new Pessoa($object->motorista_entrada_id);
            $usuario = new SystemUsers($pessoa->system_user_id);
        
          //   $user = GenesisAuthenticationService::authenticate( $usuario->email, $data->senha_motorista_entrada);
         //   if (!$user) {
        //        new TMessage('error', 'Senha do motorista de entrada inválida.');
        //        return;
        //    }
        //         $pessoa = new Pessoa($object->motorista_retirada_id);
        //    $usuario = new SystemUsers($pessoa->system_user_id);
       // 
        //     $user = GenesisAuthenticationService::authenticate( $usuario->email, $data->senha_motorista_retirada);
        //    if (!$user) {
        //        new TMessage('error', 'Senha do motorista de retirada inválida.');
        //        return;
        //    }
            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $estadosPermitidos = [
                EstadoPedidoFrotas::APROVADO,
                EstadoPedidoFrotas::PGTOAPROVADO,
                EstadoPedidoFrotas::FINALIZADO,
                EstadoPedidoFrotas::ENTREGUE
            ];
            if ($object->data_retirada_veiculo) {
               
                    // Verifica se a data de entrada do veículo foi informada
                    if (empty($object->data_entrada_veiculo)) {
                        new TMessage('error', 'Não é possível definir a data de entrega. A data de entrada deve ser informada.');
                        return;
                    }
                
                    // Verifica se o serviço foi iniciado e finalizado
                    if (empty($object->datahora_inicioservico) || empty($object->datahora_fimservico)) {
                        new TMessage('error', 'Não é possível definir a data de entrega. Antes, é necessário informar a data/hora de início e término do serviço.');
                        return;
                    }
                
                    // Verifica se o estado é APROVADO
                    if (!in_array($object->estado_pedido_frotas_id, $estadosPermitidos)) {
                        new TMessage('error', 'A entrega só pode ser registrada quando o pedido estiver no estado APROVADO, PGTO APROVADO, FINALIZADO ou ENTREGUE.');
                        return;
                    }
                    
                
                    // Tudo certo, registra o histórico de entrega
                    $this->registrarHistoricoPedidoFrotasEntregue($object);
            }
            if (in_array($object->estado_pedido_frotas_id, $estadosPermitidos)) {
                if ($object->datahora_inicioservico) {
                    if (empty($object->km) || empty($object->responsavel_tecnico)) {
                        new TMessage('error', 'Para iniciar o serviço, informe a quilometragem e o responsável técnico.');
                        return;
                    }
                }
            } else {
                if ($object->datahora_inicioservico || $object->km || $object->responsavel_tecnico_id) {
                    new TMessage('error', 'Você só pode informar a quilometragem, o responsável técnico e a data de início do serviço após a aprovação do pedido.');
                    return;
                }
            }
            if (in_array($object->estado_pedido_frotas_id, $estadosPermitidos)) {
                if ($object->data_entrada_veiculo) {
                    if (empty($object->motorista_entrada_id)) {
                        new TMessage('error', 'Para registrar a entrada, é necessário informar o Condutor.');
                        return;
                    }
                }
            } else {
                if ($object->data_entrada_veiculo || $object->motorista_entrada_id) {
                    new TMessage('error', 'Só é permitido informar a entrada após a aprovação do pedido.');
                    return;
                }
            }
            if (in_array($object->estado_pedido_frotas_id, $estadosPermitidos)) {
                if ($object->data_retirada_veiculo) {
                    if (empty($object->motorista_retirada_id)) {
                        new TMessage('error', 'Para registrar a retirada, é necessário informar o Condutor.');
                        return;
                    }
                }
            } else {
                if ($object->data_retirada_veiculo || $object->motorista_retirada_id) {
                    new TMessage('error', 'Só é permitido informar a retirada após a aprovação do pedido.');
                    return;
                }
            }    

            if (!( ($object->data_retirada_veiculo >= $object->data_cotacao) && 
                ($object->data_retirada_veiculo >= $object->data_entrada_veiculo) )) 
            {
                new TMessage('error', 'Data da retirada deve ser maior ou igual à data da cotação e à data de entrada do veículo.');
                return;
            }
            
            // if (!( ($object->data_entrada_veiculo >= $object->data_cotacao) && 
            //     ($object->data_entrada_veiculo >= $object->datahora_inicioservico) )) 
            // {
            //     new TMessage('error', 'Data de entrada do veículo deve ser maior ou igual à data da cotação e à data/hora de início do serviço.');
              
            //     return;
            // }

             
            if (!( ($object->datahora_inicioservico >= $object->data_cotacao)  )) 
            {
                new TMessage('error', 'Data/hora de início do serviço deve ser maior ou igual à data da cotação.');
             
                return;
            }
             if (!( ($object->datahora_fimservico >= $object->data_cotacao)  && 
                ($object->datahora_fimservico >= $object->datahora_inicioservico) )) 
            {
                new TMessage('error', 'Data/hora de fim do serviço deve ser maior ou igual à data da cotação e à data/hora de início do serviço.');           
                return;
            }
            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('PropostasDisponiveisList', 'onShow', $loadPageParam); 

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
                TSession::setValue('propostas_id', null);
                TSession::setValue('propostas_id', $key);
                TTransaction::open(self::$database); // open a transaction

                $object = new Propostas($key); // instantiates the Active Record 

                $object->horimetro_inicioservico = ($object->horimetro_inicioservico == 'NULL') ? '' : $object->km;
                $object->ciclos_inicioservico = ($object->ciclos_inicioservico == 'NULL') ? '' : $object->ciclos;
                $object->horimetro_fimservico = ($object->horimetro_fimservico == 'NULL') ? '' : $object->km;
                $object->ciclos_fimservico = ($object->ciclos_fimservico == 'NULL') ? '' : $object->ciclos;
                $object->ciclos_entrada_aeronave = ($object->ciclos_entrada_aeronave == 'NULL') ? '' : $object->ciclos;
                $object->horimetro_entrada_aeronave = ($object->horimetro_entrada_aeronave == 'NULL') ? '' : $object->km;
                $object->ciclos_retirada_aeronave = ($object->ciclos_retirada_aeronave == 'NULL') ? '' : $object->ciclos;
                $object->horimetro_retirada_aeronave = ($object->horimetro_retirada_aeronave == 'NULL') ? '' : $object->km;

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
    private function registrarHistoricoPedidoFrotasEntregue($proposta)
    {

        $historico = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $proposta->pedido_frotas_id)
                                           ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::ENTREGUE)
                                           ->load();
        if ($historico) {
            foreach ($historico as $hist) {
                $hist->delete();
            }
        }
        $hist = new PedidoFrotasHistorico();
        $hist->pedido_frotas_id = $proposta->pedido_frotas_id;
        $hist->data_operacao = $proposta->data_retirada_veiculo;
        $hist->estado_pedido_frotas_id = EstadoPedidoFrotas::ENTREGUE; 
         $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $hist->aprovador_frotas_id = $aprovador[0]->id;
        }
       // $hist->aprovador_frotas_id = TSession::getValue('userid');
        $hist->store();
        
        $pedido = new PedidoFrotas($proposta->pedido_frotas_id); 
        $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::ENTREGUE;
        $pedido->store();

        $historicoproposta = PropostasHistorico::where('propostas_id', '=', $proposta->id)
                                          ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::ENTREGUE)
                                          ->load();
        if ($historicoproposta) {
        foreach ($historicoproposta as $histproposta) {
              $histproposta->delete();
            }
        }
        $historicoproposta = new PropostasHistorico();
        $historicoproposta->propostas_id = $proposta->id;
        $historicoproposta->data_operacao = $proposta->data_retirada_veiculo;
        $historicoproposta->estado_pedido_frotas_id = EstadoPedidoFrotas::ENTREGUE; 
         $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $historicoproposta->aprovador_frotas_id = $aprovador[0]->id;
        }
        $historicoproposta->aprovador_frotas_id = TSession::getValue('userid');
        $historicoproposta->store();
        
        $pedido = new Propostas($proposta->id); 
        $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::ENTREGUE;
        $pedido->store();



    }
  

    public static function onValidarSenhaMotoristaEntrada($param)
    {
        try {
                TTransaction::open('minierp');

            // Obtém os dados do formulário via TForm::sendData se necessário
            $idMotorista = $param['motorista_entrada_id'] ?? null;
            $senhaDigitada = $param['senha_motorista_entrada'] ?? null;

            // Valida os campos
            if (empty($senhaDigitada)) {
                throw new Exception('Informe a senha do Condutor que vai dar entrada');
            }

            if (!$idMotorista) {
                throw new Exception('ID do Condutor que vai dar entrada não informado.');
            }

            $motorista = new Pessoa($idMotorista);

            if (!$motorista) {
                throw new Exception('Condutor que vai dar entrada não encontrado');
            }

            $user = new SystemUsers($motorista->system_users_id);

            if (!$user) {
                throw new Exception('Usuário não encontrado');
            }

            // if (md5($senhaDigitada) !== $user->password) {
            //     throw new Exception('Senha incorreta!');
            // }

            // Opcional: confirmação visual
       //     new TMessage('info', 'Senha validada com sucesso!');

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }    

    public static function onValidarSenhaMotoristaRetirada($param)
    {
        try {
            TTransaction::open('minierp');

            // Obtém os dados do formulário via TForm::sendData se necessário
            $idMotorista = $param['motorista_retirada_id'] ?? null;
            $senhaDigitada = $param['senha_motorista_retirada'] ?? null;

            // Valida os campos
            if (empty($senhaDigitada)) {
                throw new Exception('Informe a senha do Condutor que vai retirar');
            }

            if (!$idMotorista) {
                throw new Exception('ID do Condutor que vai retirar não informado.');
            }

            $motorista = new Pessoa($idMotorista);

            if (!$motorista) {
                throw new Exception('Condutor que vai retirar não encontrado');
            }

            $user = new SystemUsers($motorista->system_users_id);

            if (!$user) {
                throw new Exception('Usuário não encontrado');
            }

            // if (md5($senhaDigitada) !== $user->password) {
            //     throw new Exception('Senha incorreta!');
            // }

            // Opcional: confirmação visual
       //     new TMessage('info', 'Senha validada com sucesso!');

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }    
}

