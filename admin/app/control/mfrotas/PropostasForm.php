<?php

class PropostasForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Propostas';
    private static $primaryKey = 'id';
    private static $formName = 'form_PropostasForm';

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
        $this->form->setFormTitle("Cadastro de propostas");

        $criteria_pessoa_id = new TCriteria();
        $criteria_estado_pedido_frotas_id = new TCriteria();
        $criteria_veiculos_id = new TCriteria();
        $criteria_motorista_entrada_id = new TCriteria();
        $criteria_motorista_retirada_id = new TCriteria();

        $filterVar = GrupoPessoa::CONDUTOR;
        $criteria_motorista_entrada_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '{$filterVar}')")); 
        $filterVar = GrupoPessoa::CONDUTOR;
        $criteria_motorista_retirada_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '{$filterVar}')")); 

        $id = new TEntry('id');
        $pedido_frotas_id = new TEntry('pedido_frotas_id');
        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa_id );
        $estado_pedido_frotas_id = new TDBCombo('estado_pedido_frotas_id', 'minierp', 'EstadoPedidoFrotas', 'id', '{nome}','id asc' , $criteria_estado_pedido_frotas_id );
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa}','id asc' , $criteria_veiculos_id );
        $data_cotacao = new TDate('data_cotacao');
        $data_previsao_entrega = new TDate('data_previsao_entrega');
        $obs = new TText('obs');
        $itens_produtos = new BPageContainer();
        $itens_servicos = new BPageContainer();
        $total_produtos_sem_desconto = new TNumeric('total_produtos_sem_desconto', '2', ',', '.' );
        $total_servicos_sem_desconto = new TNumeric('total_servicos_sem_desconto', '2', ',', '.' );
        $total_geral_sem_desconto = new TNumeric('total_geral_sem_desconto', '2', ',', '.' );
        $desconto_contratual = new TNumeric('desconto_contratual', '2', ',', '.' );
        $total_produtos_com_desconto = new TNumeric('total_produtos_com_desconto', '2', ',', '.' );
        $total_servicos_com_desconto = new TNumeric('total_servicos_com_desconto', '2', ',', '.' );
        $total_geral_com_desconto = new TNumeric('total_geral_com_desconto', '2', ',', '.' );
        $button_enviar = new TButton('button_enviar');
        $datahora_inicioservico = new TDateTime('datahora_inicioservico');
        $datahora_fimservico = new TDateTime('datahora_fimservico');
        $km = new TEntry('km');
        $responsavel_tecnico = new TEntry('responsavel_tecnico');
        $data_entrada_veiculo = new TDateTime('data_entrada_veiculo');
        $motorista_entrada_id = new TDBCombo('motorista_entrada_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_motorista_entrada_id );
        $data_retirada_veiculo = new TDateTime('data_retirada_veiculo');
        $motorista_retirada_id = new TDBCombo('motorista_retirada_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_motorista_retirada_id );


        $button_enviar->addStyleClass('btn-default');
        $button_enviar->setImage('fas:paper-plane #FF0000');
    //    $km->setValue('NULL');
        $data_previsao_entrega->setValue('NULL');

        $itens_produtos->setId('b67eab55f35fb6');
        $itens_servicos->setId('b67eb5821b8051');

        $itens_produtos->hide();
        $itens_servicos->hide();

        $button_enviar->setAction(new TAction([$this, 'onEnviar']), "Enviar");
        $itens_produtos->setAction(new TAction(['ItensPropostasProdutosList', 'onShow']));
        $itens_servicos->setAction(new TAction(['ItensPropostasServicosList', 'onShow']));

        $pessoa_id->enableSearch();
        $veiculos_id->enableSearch();
        $motorista_entrada_id->enableSearch();
        $motorista_retirada_id->enableSearch();
        $estado_pedido_frotas_id->enableSearch();

        $data_cotacao->setMask('dd/mm/yyyy');
        $data_previsao_entrega->setMask('dd/mm/yyyy');
        $datahora_fimservico->setMask('dd/mm/yyyy hh:ii');
        $data_entrada_veiculo->setMask('dd/mm/yyyy hh:ii');
        $data_retirada_veiculo->setMask('dd/mm/yyyy hh:ii');
        $datahora_inicioservico->setMask('dd/mm/yyyy hh:ii');

        $data_cotacao->setDatabaseMask('yyyy-mm-dd');
        $data_previsao_entrega->setDatabaseMask('yyyy-mm-dd');
        $datahora_fimservico->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_entrada_veiculo->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_retirada_veiculo->setDatabaseMask('yyyy-mm-dd hh:ii');
        $datahora_inicioservico->setDatabaseMask('yyyy-mm-dd hh:ii');

        $id->setEditable(false);
        $pessoa_id->setEditable(false);
        $veiculos_id->setEditable(false);
        $data_cotacao->setEditable(false);
        $pedido_frotas_id->setEditable(false);
        $desconto_contratual->setEditable(false);
        $estado_pedido_frotas_id->setEditable(false);
        $total_geral_sem_desconto->setEditable(false);
        $total_geral_com_desconto->setEditable(false);
        $total_produtos_sem_desconto->setEditable(false);
        $total_servicos_sem_desconto->setEditable(false);
        $total_produtos_com_desconto->setEditable(false);
        $total_servicos_com_desconto->setEditable(false);

        $id->setSize(100);
        $km->setSize('100%');
        $obs->setSize('100%', 70);
        $pessoa_id->setSize('100%');
        $data_cotacao->setSize(110);
        $veiculos_id->setSize('100%');
        $itens_produtos->setSize('100%');
        $itens_servicos->setSize('100%');
        $pedido_frotas_id->setSize('100%');
        $datahora_fimservico->setSize(160);
        $data_entrada_veiculo->setSize(160);
        $data_previsao_entrega->setSize(110);
        $data_retirada_veiculo->setSize(160);
        $desconto_contratual->setSize('100%');
        $datahora_inicioservico->setSize(160);
        $responsavel_tecnico->setSize('100%');
        $motorista_entrada_id->setSize('100%');
        $motorista_retirada_id->setSize('100%');
        $estado_pedido_frotas_id->setSize('100%');
        $total_geral_sem_desconto->setSize('100%');
        $total_geral_com_desconto->setSize('100%');
        $total_produtos_sem_desconto->setSize('100%');
        $total_servicos_sem_desconto->setSize('100%');
        $total_produtos_com_desconto->setSize('100%');
        $total_servicos_com_desconto->setSize('100%');

        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $itens_produtos->add($loadingContainer);
        $itens_produtos->setParameter("tipo", 1);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $itens_servicos->add($loadingContainer);
        $itens_servicos->setParameter("tipo", 2);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');


        $this->itens_produtos = $itens_produtos;
        $this->itens_servicos = $itens_servicos;

        $tab_67e6af8606113 = new BootstrapFormBuilder('tab_67e6af8606113');
        $this->tab_67e6af8606113 = $tab_67e6af8606113;
        $tab_67e6af8606113->setProperty('style', 'border:none; box-shadow:none;');

        $tab_67e6af8606113->appendPage("Dados proposta");

        $tab_67e6af8606113->addFields([new THidden('current_tab_tab_67e6af8606113')]);
        $tab_67e6af8606113->setTabFunction("$('[name=current_tab_tab_67e6af8606113]').val($(this).attr('data-current_page'));");

        $row1 = $tab_67e6af8606113->addFields([new TLabel("ID Proposta", null, '14px', null, '100%'),$id],[new TLabel("ID Pedido", null, '14px', null, '100%'),$pedido_frotas_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $tab_67e6af8606113->addFields([new TLabel("Pessoa:", null, '14px', null, '100%'),$pessoa_id],[new TLabel("Estado pedido:", null, '14px', null, '100%'),$estado_pedido_frotas_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $tab_67e6af8606113->addFields([new TLabel("Veiculos:", null, '14px', null, '100%'),$veiculos_id],[new TLabel("Data proposta:", null, '14px', null, '100%'),$data_cotacao]);
        $row3->layout = ['col-sm-6',' col-sm-6'];

        $row4 = $tab_67e6af8606113->addFields([new TLabel("Data previsao entrega:", null, '14px', null, '100%'),$data_previsao_entrega],[new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row4->layout = ['col-sm-6',' col-sm-6'];

        $row5 = $tab_67e6af8606113->addFields([$itens_produtos]);
        $row5->layout = [' col-sm-12'];

        $row6 = $tab_67e6af8606113->addFields([$itens_servicos]);
        $row6->layout = [' col-sm-12'];

        $row7 = $tab_67e6af8606113->addFields([new TFormSeparator("Valor Total dos itens", '#333', '18', '#eee')]);
        $row7->layout = ['col-sm-12'];

        $row8 = $tab_67e6af8606113->addFields([new TLabel("Total dos Produtos sem desconto:", null, '14px', null, '100%'),$total_produtos_sem_desconto],[new TLabel("Total dos Serviços sem desconto:", null, '14px', null, '100%'),$total_servicos_sem_desconto],[new TLabel("Total geral sem descontos:", null, '14px', null, '100%'),$total_geral_sem_desconto]);
        $row8->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row9 = $tab_67e6af8606113->addFields([],[],[new TLabel("(%) Desconto contratual:", '#FF0000', '14px', 'B', '100%'),$desconto_contratual]);
        $row9->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row10 = $tab_67e6af8606113->addFields([new TLabel("Total dos Produtos com desconto:", null, '14px', null, '100%'),$total_produtos_com_desconto],[new TLabel("Total dos Serviços com desconto:", null, '14px', null, '100%'),$total_servicos_com_desconto],[new TLabel("Total geral com desconto:", null, '14px', null, '100%'),$total_geral_com_desconto]);
        $row10->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

   
       

       /* $tab_67e6af8606113->appendPage("Registro do serviço");
        $row18 = $tab_67e6af8606113->addFields([new TFormSeparator("Dados da entrada do veículo", '#333', '18', '#eee')]);
        $row18->layout = [' col-sm-12'];

        $row19 = $tab_67e6af8606113->addFields([new TLabel("Data e hora da entrada do veiculo", null, '14px', null, '100%'),$data_entrada_veiculo],[new TLabel("Condutor do veículo:", null, '14px', null, '100%'),$motorista_entrada_id]);
        $row19->layout = [' col-sm-6',' col-sm-6'];

        $row15 = $tab_67e6af8606113->addFields([new TFormSeparator("Registro inicio e fim do serviço", '#333', '18', '#eee')]);
        $row15->layout = [' col-sm-12'];

        $row16 = $tab_67e6af8606113->addFields([new TLabel("Data e hora do inicio do serviço:", null, '14px', null, '100%'),$datahora_inicioservico],[new TLabel("Data e hora do fim do serviço:", null, '14px', null, '100%'),$datahora_fimservico]);
        $row16->layout = [' col-sm-6',' col-sm-6'];

        $row17 = $tab_67e6af8606113->addFields([new TLabel("Km:", null, '14px', null, '100%'),$km],[new TLabel("Responsável técnico:", null, '14px', null, '100%'),$responsavel_tecnico]);
        $row17->layout = ['col-sm-6','col-sm-6'];

     

        $row20 = $tab_67e6af8606113->addFields([new TFormSeparator("Dados da retirada do veículo", '#333', '18', '#eee')]);
        $row20->layout = [' col-sm-12'];

        $row21 = $tab_67e6af8606113->addFields([new TLabel("Data e hora da retirada do veiculo", null, '14px', null, '100%'),$data_retirada_veiculo],[new TLabel("Condutor do veículo", null, '14px', null, '100%'),$motorista_retirada_id]);
        $row21->layout = [' col-sm-6',' col-sm-6'];
*/
        $row22 = $this->form->addFields([$tab_67e6af8606113]);
        $row22->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

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

        $style = new TStyle('right-panel > .container-part[page-name=PropostasForm]');
        $style->width = '80% !important';   
        $style->show(true);

        

    }

    public  function onEnviar($param = null) 
    {
        /*try 
        {
              TTransaction::open(self::$database); // open a transaction

              $object = new ComentarioProposta(); // create an empty object //</blockLine>

              $data = $this->form->getData(); // get form data as array
              $object->fromArray( (array) $data); // load the object with data

              $objectcom = new stdClass();
              $objectcom->created_at      = date();
              $objectcom->comentario      = $object->comentario;
              $objectcom->propostas_id    = $object->id;
              $objectcom->system_users_id = TSession::getValue('userid');

             TForm::sendData('form_PropostasForm', $objectcom);

             TTransaction::close();*/

             try {
                   TTransaction::open(self::$database); // open a transaction

                   $id = (int) TSession::getValue('propostas_id');

                    $object = new Propostas($id);

                    $data = $this->form->getData();
                    $data->id = $id;                // <- GARANTA o id
                    $object->fromArray((array) $data);

                /*  $objectcom = new ComentarioProposta();
                  $objectcom->created_at      = date('Y-m-d H:i:s');
                  $objectcom->comentario      = $data->comentario;
                  $objectcom->propostas_id    = $object->id;
                  $objectcom->system_users_id = TSession::getValue('userid');
                  $objectcom->store();*/

                 TForm::sendData('form_PropostasForm', $object);

                        $this->itens_produtos->unhide();
                $this->itens_produtos->setParameter('id', $object->id);
                $this->itens_servicos->unhide();
                $this->itens_servicos->setParameter('id', $object->id);

                 TTransaction::close();

            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }

/*

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

           $id = (int) TSession::getValue('propostas_id');

            $object = new Propostas($id);

            $data = $this->form->getData();
            $data->id = $id;                // <- GARANTA o id
            $object->fromArray((array) $data);

            if ($object->desconto_contratual>0)
            {
                $object->valor_total = $object->total_geral_sem_desconto;
                $object->valor_desconto = $object->total_geral_sem_desconto-$object->total_geral_com_desconto;
                $object->valor_liquido =  $object->total_geral_com_desconto;         
            } else {
                $object->valor_total = $object->total_geral_sem_desconto;
                $object->valor_desconto = 0;
                $object->valor_liquido =  $object->total_geral_sem_desconto;         
            }

            $object->store(); // save the object 
            $emrevisao = false;
            $propostarevisao = new Propostas($object->id);
            if ($propostarevisao) {                            
                if ($propostarevisao->estado_pedido_frotas1_id==EstadoPedidoFrotas::REVISAO) {
                    $propostarevisao->estado_pedido_frotas1_id=null;
                    $propostarevisao->store();
                }

            }
            //preciso criar um script de atualizacao no itens_pedido_frotas para atualizar os valores totais
            //caso essa proposta tenha sido alterada e ja tenha sido aprovada, entregue, finalizada, pre-aprovada, 
            self::recalcularTotaisDaProposta($object->id);

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = "$object->pedido_frotas_id";
            $loadPageParam["propostas_id"] = "$object->id";

            if ($emrevisao) {
                $pedido = PedidoFrotas::where('id', '=', $object->pedido_frotas_id)->first();
                //enviar email avisando gestor
                   $aprovadores = []; // Inicializa o array de aprovadores
                if ($mensagensGarantia) {
                   
                    $usuario = SystemUsers::where('system_unit_id', '=', TSession::getValue('idunit'))
                                          ->load();
                    if ($usuario){
                        foreach ($usuario as $user) {
                            $aprovador_frotas = AprovadorFrotas::where('system_users_id', '=', $user->id)
                                                                ->load();
                            if ($aprovador_frotas) {
                                // Verifica se o aprovador está ativo e se o estado de pedido é aprovado
                                // e se o estado de pedido frotas aprovador está ativo
                                foreach ($aprovador_frotas as $aprovador_frotas) {
                                    $estado_pedido_frotas_aprovador = EstadoPedidoFrotasAprovador::where('aprovador_frotas_id', '=', $aprovador_frotas->id)
                                                                            ->where('estado_pedido_frotas_id', '=', EstadoPedido::APROVADO)
                                                                            ->first();
                                    if ($estado_pedido_frotas_aprovador) {
                                        //ENVIAR EMAIL PARA OS APROVADORES GUARDAR OS EMAILS EM UM ARRAY 
                                        $aprovadores[] = $user;
                                    }
                                }                        
                            } 

                        }
                    } 
                  if ($aprovadores) {
                    foreach ($aprovadores as $dadosAprovador) {
                        if ($dadosAprovador) {
                            $emailTemplate = new EmailTemplate(EmailTemplate::NOTIFICACAO_ATUALIZACAO_ORCAMENTO);
                            $veiculos = new Veiculos($pedido->veiculos_id);
                            $identificacaoveiculo = $veiculos->placa . ' - ' . $veiculos->marca->descricao . ' - ' . $veiculos->modelo->descricao;

                            $titulo = $emailTemplate->titulo;
                            $mensagem = $emailTemplate->mensagem;
                            $usr = new SystemUsers(TSession::getValue('userid'));

                            $mensagem = str_replace('{nome_gestor}', $dadosAprovador->name, $mensagem);
                            $mensagem = str_replace('{data_identificacao}', date('d/m/Y H:i'), $mensagem);
                            $mensagem = str_replace('{identificacao_veiculo}', $identificacaoveiculo, $mensagem);
                            $mensagem = str_replace('{descricao_servico_original}', $pedido->descricao, $mensagem);
                            $mensagem = str_replace('{nome_rede}', $object->pessoa->nome, $mensagem);
                            $mensagem = str_replace('{nome_usuario_adicao}', $usr->name, $mensagem);
                            $mensagem = str_replace('{id_pedido}', $object->pedido_frotas_id, $mensagem);
                            $mensagem = str_replace('{propostas_id}', $object->propostas_id, $mensagem);

                            $titulo = $pedido->render($titulo);
                            $mensagem = $pedido->render($mensagem);

                          //  MailService::send($dadosAprovador->email, $titulo, $mensagem, 'html');
                        }
                    }
                }
                   
                }
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
                $param['id'] = $key; // add the parameter $key to the array $param
                TSession::setValue('propostas_id', null);
                TSession::setValue('propostas_id', $key);
            
                TTransaction::open(self::$database); // open a transaction

                self::recalcularTotaisDaProposta($key);
                $object = new Propostas($key); // instantiates the Active Record 

                TSession::setValue('parametros',null);
                TSession::setValue('parametros',$param);
                TSession::setValue('inseridoitem', null);

                $this->itens_produtos->unhide();
                $this->itens_produtos->setParameter('id', $object->id);
                $this->itens_servicos->unhide();
                $this->itens_servicos->setParameter('id', $object->id);

                    $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = "$object->pedido_frotas_id";
            $loadPageParam["propostas_id"] = "$object->id";

                    // Converte ambas as datas para Y-m-d (sem hora)
                    if (date('Y-m-d', strtotime($object->data_limite_resposta)) < date('Y-m-d')) {
                        $dataLimite = (new DateTime($object->data_limite_resposta))->format('d/m/Y');

                        // Mensagem de erro amigável
                        new TMessage('error', "A data limite para responder essa OS é {$dataLimite} e já expirou. A edição não é permitida.");

                                   TApplication::loadPage('PropostasDisponiveisList', 'onShow', $loadPageParam); 
                                      TScript::create("Template.closeRightPanel();");

                    }

                /* $criteria = new TCriteria();
                $criteria->add(new TFilter('propostas_id', '=', $object->id));
                $criteria->add(new TFilter('system_users_id', '<>', TSession::getValue('userid')));
                $criteria->add(new TFilter('leitura_dt', 'IS', NULL));

                $repo = new TRepository('ComentarioProposta');
                $com = $repo->load($criteria);

                if ($com)
                {
                    foreach ($com as $comm) {
                          $comm->leitura_dt=date('Y-m-d H:i:s');
                          $comm->store();
                    }
                }*/
                TSession::setValue('token', null);
                $vehicletoken = Vehicletoken::where('veiculos_id','=',$object->veiculos_id)
                                            ->where('deleted_at','is',null)
                                            ->first();
                if ($vehicletoken) {
                    TSession::setValue('token', $vehicletoken->token);
                } 

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
        TScript::create("
            window.parent.document.querySelector('.window .close').title = 'Salve o formulário antes de fechar';
        ");
        TScript::create("
            window.parent.document.querySelector('.window .close').style.pointerEvents = 'auto';
            window.parent.document.querySelector('.window .close').style.opacity = '1';
            window.parent.document.querySelector('.window .close').title = '';
        ");
    } 


    public static function getFormName()
    {
        return self::$formName;
    }

    private static function parseNumero($valor): float
    {
        if ($valor === null || $valor === '')
        {
            return 0.0;
        }

        $s = trim((string) $valor);
        $s = preg_replace('/[^0-9.,\\-]/', '', $s);

        $posVirgula = strrpos($s, ',');
        $posPonto = strrpos($s, '.');

        if ($posVirgula !== false || $posPonto !== false)
        {
            $sepDecimal = ($posVirgula !== false && $posVirgula > $posPonto) ? ',' : '.';
            $partes = explode($sepDecimal, $s);
            $decimal = array_pop($partes);
            $inteiro = implode('', $partes);
            $inteiro = str_replace([',', '.'], '', $inteiro);
            $normalizado = $inteiro . '.' . $decimal;
        }
        else
        {
            $normalizado = str_replace([',', '.'], '', $s);
        }

        return is_numeric($normalizado) ? (float) $normalizado : 0.0;
    }

    private static function normalizarTaxaContrato($taxaBase, int $totalGeralSemDescontoCents = 0, int $totalDescontoItensCents = 0): float
    {
        $taxa = self::parseNumero($taxaBase);

        if ($totalGeralSemDescontoCents > 0 && $totalDescontoItensCents > 0)
        {
            $taxaCalculada = round(($totalDescontoItensCents / $totalGeralSemDescontoCents) * 100, 4);

            if ($taxa > 1 && $taxaCalculada > 0 && $taxaCalculada <= 1)
            {
                return $taxaCalculada;
            }
        }

        return $taxa;
    }

    public static function recalcularTotaisDaProposta($propostaId): stdClass
    {
        $criteria = new TCriteria();
        $criteria->add(new TFilter('propostas_id', '=', $propostaId));
        $criteria->add(new TFilter('deleted_at', 'IS', NULL));
        $criteria->setProperty('order', 'id asc');

        $repo = new TRepository('ItensPropostas');
        $itens = $repo->load($criteria) ?: [];

        $proposta = new Propostas($propostaId);

        $taxaBase = TSession::getValue('taxacontrato');
        if ($taxaBase === null || $taxaBase === '')
        {
            $taxaBase = $proposta->desconto_contratual ?? 0;
        }

        $totalGeralSemDescontoCents = 0;
        $totalDescontoItensCents = 0;
        $ultimoIndice = count($itens) - 1;
        $ultimoIndiceElegivel = null;

        foreach ($itens as $indice => $item)
        {
            $brutoItemCents = (int) round((float) ($item->valor ?? 0) * (float) ($item->qtde ?? 0) * 100, 0, PHP_ROUND_HALF_UP);
            $descontoAtualCents = (int) round((float) ($item->perc_desconto ?? 0) * 100, 0, PHP_ROUND_HALF_UP);
            $valorTotalEsperadoCents = $brutoItemCents - $descontoAtualCents;
            $valorTotalAtualCents = (int) round((float) ($item->valor_total ?? 0) * 100, 0, PHP_ROUND_HALF_UP);
            $ajusteAtualCents = (int) round((float) ($item->valor_ajuste_arredondamento ?? 0) * 100, 0, PHP_ROUND_HALF_UP);

            if ($valorTotalAtualCents !== $valorTotalEsperadoCents || $ajusteAtualCents !== 0)
            {
                $item->valor_total = $valorTotalEsperadoCents / 100;
                $item->valor_ajuste_arredondamento = 0;
                $item->store();
            }

            $totalGeralSemDescontoCents += $brutoItemCents;
            $totalDescontoItensCents += $descontoAtualCents;

            if ($brutoItemCents > 0)
            {
                $ultimoIndiceElegivel = $indice;
            }
        }

        $txcontrato = round(self::normalizarTaxaContrato($taxaBase, $totalGeralSemDescontoCents, $totalDescontoItensCents), 2);

        if ($ultimoIndiceElegivel === null)
        {
            $ultimoIndiceElegivel = $ultimoIndice;
        }

        $taxaBps = (int) round($txcontrato * 100, 0, PHP_ROUND_HALF_UP);
        $descontoEsperadoCents = (int) floor((($totalGeralSemDescontoCents * $taxaBps) + 5000) / 10000);
        $ajusteArredondamentoCents = $totalDescontoItensCents - $descontoEsperadoCents;

        foreach ($itens as $indice => $item)
        {
            $novoAjusteCents = ($indice === $ultimoIndiceElegivel) ? $ajusteArredondamentoCents : 0;
            $ajusteAtualCents = (int) round((float) ($item->valor_ajuste_arredondamento ?? 0) * 100, 0, PHP_ROUND_HALF_UP);
            $precisaSalvar = false;

            if ($indice === $ultimoIndiceElegivel && $ajusteArredondamentoCents !== 0)
            {
                $brutoItemCents = (int) round((float) ($item->valor ?? 0) * (float) ($item->qtde ?? 0) * 100, 0, PHP_ROUND_HALF_UP);
                $descontoAtualCents = (int) round((float) ($item->perc_desconto ?? 0) * 100, 0, PHP_ROUND_HALF_UP);
                $valorTotalAtualCents = (int) round((float) ($item->valor_total ?? 0) * 100, 0, PHP_ROUND_HALF_UP);

                $novoDescontoCents = $descontoAtualCents - $ajusteArredondamentoCents;
                $novoValorTotalCents = $brutoItemCents - $novoDescontoCents;

                if ($novoDescontoCents !== $descontoAtualCents || $novoValorTotalCents !== $valorTotalAtualCents)
                {
                    $item->perc_desconto = $novoDescontoCents / 100;
                    $item->valor_total = $novoValorTotalCents / 100;
                    $precisaSalvar = true;
                }
            }

            if ($ajusteAtualCents !== $novoAjusteCents)
            {
                $item->valor_ajuste_arredondamento = $novoAjusteCents / 100;
                $precisaSalvar = true;
            }

            if ($precisaSalvar)
            {
                $item->store();
            }
        }

        $totalProdutosSemDescontoCents = 0;
        $totalProdutosComDescontoCents = 0;
        $totalServicosSemDescontoCents = 0;
        $totalServicosComDescontoCents = 0;

        foreach ($itens as $item)
        {
            $liquidoItemCents = (int) round((float) ($item->valor_total ?? 0) * 100, 0, PHP_ROUND_HALF_UP);
            $brutoItemCents = (int) round((float) ($item->valor ?? 0) * (float) ($item->qtde ?? 0) * 100, 0, PHP_ROUND_HALF_UP);

            if ($item->tipo == 1)
            {
                $totalProdutosSemDescontoCents += $brutoItemCents;
                $totalProdutosComDescontoCents += $liquidoItemCents;
            }
            else
            {
                $totalServicosSemDescontoCents += $brutoItemCents;
                $totalServicosComDescontoCents += $liquidoItemCents;
            }
        }

        $proposta->total_produtos_sem_desconto = $totalProdutosSemDescontoCents / 100;
        $proposta->total_servicos_sem_desconto = $totalServicosSemDescontoCents / 100;
        $proposta->total_geral_sem_desconto = $proposta->total_produtos_sem_desconto + $proposta->total_servicos_sem_desconto;
        $proposta->desconto_contratual = $txcontrato;
        $proposta->total_produtos_com_desconto = $totalProdutosComDescontoCents / 100;
        $proposta->total_servicos_com_desconto = $totalServicosComDescontoCents / 100;
        $proposta->total_geral_com_desconto = $proposta->total_produtos_com_desconto + $proposta->total_servicos_com_desconto;

        if ($proposta->desconto_contratual > 0)
        {
            $proposta->valor_total = $proposta->total_geral_sem_desconto;
            $proposta->valor_desconto = $proposta->total_geral_sem_desconto - $proposta->total_geral_com_desconto;
            $proposta->valor_liquido = $proposta->total_geral_com_desconto;
        }
        else
        {
            $proposta->valor_total = $proposta->total_geral_sem_desconto;
            $proposta->valor_desconto = 0;
            $proposta->valor_liquido = $proposta->total_geral_sem_desconto;
        }

        $proposta->store();

        $objectpro = new stdClass();
        $objectpro->total_produtos_sem_desconto = number_format($proposta->total_produtos_sem_desconto, 2, ',', '.');
        $objectpro->total_servicos_sem_desconto = number_format($proposta->total_servicos_sem_desconto, 2, ',', '.');
        $objectpro->total_geral_sem_desconto = number_format($proposta->total_geral_sem_desconto, 2, ',', '.');
        $objectpro->desconto_contratual = number_format($proposta->desconto_contratual, 2, ',', '.');
        $objectpro->total_produtos_com_desconto = number_format($proposta->total_produtos_com_desconto, 2, ',', '.');
        $objectpro->total_servicos_com_desconto = number_format($proposta->total_servicos_com_desconto, 2, ',', '.');
        $objectpro->total_geral_com_desconto = number_format($proposta->total_geral_com_desconto, 2, ',', '.');

        return $objectpro;
    }

    public static function onRefreshTotais($param = null)
    {
        try
        {
            $propostaId = $param['propostas_id']
                ?? $param['id']
                ?? TSession::getValue('propostas_id')
                ?? (TSession::getValue('parametros')['id'] ?? null);

            if (empty($propostaId))
            {
                return;
            }

            TTransaction::open(self::$database);

            $objectpro = self::recalcularTotaisDaProposta($propostaId);

            TTransaction::close();

            TForm::sendData(self::$formName, $objectpro, false, true, 200);
            return $objectpro;
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
            return null;
        }
    }

}
