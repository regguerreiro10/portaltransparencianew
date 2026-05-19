<?php

class PropostaFormEmailVenda extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_PropostaFormEmailVenda';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Enviar email de Proposta");

       // $filterVar = $param['id'];
       // $criteria_cotacao_id->add(new TFilter('id', '=', $filterVar)); 

        //$criteria->add(new TFilter('id', '=', $param['id']));

        $mensagem = new THtmlEditor('mensagem');


        $mensagem->setSize('100%', 110);


        $row1 = $this->form->addFields([new TFormSeparator("Mensagem", '#333', '18', '#eee')]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([$mensagem]);
        $row2->layout = [' col-sm-12'];

        // create the form actions
        $btn_onenviaremailcotacao = $this->form->addAction("Enviar", new TAction([$this, 'onEnviarEmailCotacao']), 'fas:rocket #ffffff');
        $this->btn_onenviaremailcotacao = $btn_onenviaremailcotacao;
        $btn_onenviaremailcotacao->addStyleClass('btn-primary'); 

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['PropostasDisponiveisList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Propostas","PropostaFormEmailVenda"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onEnviarEmailCotacao($param) 
    {
        try
        {

             $this->form->validate();
            $data = $this->form->getData();
            $mensagem = $data->mensagem;

           // if($data->cotacao_id)
           // {
                TTransaction::open('minierp');
                $emailTemplate = EmailTemplate::PEDIDO_AGUARDANDO_APROVACAO;

                //foreach($data->cotacao_id as $cotacao_id)
                //{
                    $cotacao = new Propostas(TSession::getValue('idproposta'));

                 //   $mensagem = str_replace('{id}', $cotacao->id, $mensagem);
                    $mensagem = str_replace('{nome}', $cotacao->pessoa->nome, $mensagem);
                    $mensagem = str_replace('{id1}', $cotacao->id, $mensagem);
                    $mensagem = str_replace('{nome1}', $cotacao->pessoa->nome, $mensagem);

             //       $emailTemplate->titulo = str_replace('{id}', $cotacao->id, $emailTemplate->titulo);

                    // Atualiza o status do pedido e registra histórico
                    $pedido = new PedidoFrotas($cotacao->pedido_frotas_id);
                    $departamento = new DepartamentoUnit($pedido->departamento_unit_id);
                    $this->validarPropostaParaEnvio($cotacao);
                       //17-enviado
                       //19-comproposta

                      $aprovacao_por_item = TSession::getValue('aprovacao_por_item');

                    // Define os estados válidos conforme o tipo de aprovação
                    if ($aprovacao_por_item == 2) {
                        // Aprovação por item: estados 17, 19
                        $estados_validos = [17, 19, 24];
                    } else {
                        // Aprovação por contrato: estados 13, 17, 19
                        $estados_validos = [13, 17, 19, 24];
                    }
                    
                    if (in_array($pedido->estado_pedido_frotas_id, $estados_validos)) {


                    if($departamento->email)
                    {
                        $emailTemplate = new EmailTemplate(EmailTemplate::PEDIDO_AGUARDANDO_APROVACAO);
                        
                        $titulo = $emailTemplate->titulo;
                        $mensagem = $emailTemplate->mensagem;
                        
                        $titulo = $pedido->render($titulo);
                        $mensagem = $pedido->render($mensagem);
                        
                        $notificationParam = [
                            'key' => $pedido->id
                        ];
                        $icon = 'fas fa-file-invoice-dollar';
                        
                        $userdepartamento = SystemUserDepartamentoUnit::where('departamento_unit_id','=', $departamento->id)->load();
                        if ($userdepartamento)
                        {
                            foreach ($userdepartamento as $userdepartamento)
                            {
                                $aprovadores = AprovadorFrotas::where('system_users_id','=',$userdepartamento->system_users_id)->load();
                                if ($aprovadores)
                                {
                                    foreach ($aprovadores as $aprovadores)
                                    {
                                        $useraprovador = $aprovadores->system_users_id;

                                        SystemNotification::registerpedidofrotas( $useraprovador, $titulo, $mensagem, new TAction(['PedidoFrotasFormView', 'onShow'], $notificationParam), 'Visualizar Pedido', $icon);
                                    }
            
                    
                                }
                            }
                        }


                    
                        //  MailService::send($departamento->email, $emailTemplate->titulo, $mensagem,  'html');

                        // Atualiza o status da cotacao e registra histórico
                        $cotacao->estado_pedido_frotas_id = EstadoPedidoFrotas::AGUARDANDO; 
                        $cotacao->store();

                           if (in_array($pedido->estado_pedido_frotas_id, [EstadoPedidoFrotas::COMPROPOSTA, EstadoPedidoFrotas::ENVIADO])) {
                                $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::COMPROPOSTA;
                                $pedido->store();

                                $this->registrarHistoricoPedido($pedido);
                            }
                        

                        $this->registrarHistoricoCotacao($cotacao);

                     //   $this->registrarHistoricoPedido($pedido);

                       // $this->atualizaDetalhesPedido($pedido);

                      new TMessage('info', 'Emails enviados!');
                    } else {
                        new TMessage('info', 'Emails não enviado, verifique o email do Departamento/Secretaria e/ou Unidade!');
                    }
                    } else {
                       new TMessage('error', 'Verifique com o gestor se este pedido ainda esta ativo!');                        
                    }
                  //  var_dump($cotacao);

              //  }
                TTransaction::close();
            //}

            $this->form->setData($data);

            // veio da listagem
           // if(!$data->cotacao_id)
          //  {
                // limpa a variavel de sessao
                AdiantiCoreApplication::loadPage('PropostasDisponiveisList', 'onShow', $param);

          //  }

            // fecha a cortina lateral
            TScript::create("Template.closeRightPanel();");

        }
        catch (Exception $e)
        {
            if (TTransaction::getDatabase())
            {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {               

    } 

    private function validarPropostaParaEnvio(Propostas $proposta)
    {
        $criteria = new TCriteria();
        $criteria->add(new TFilter('propostas_id', '=', $proposta->id));
        $criteria->add(new TFilter('deleted_at', 'IS', NULL));

        $repo = new TRepository('ItensPropostas');
        $itens = $repo->load($criteria) ?: [];

        if (count($itens) === 0)
        {
            throw new Exception('Nao e permitido enviar proposta sem itens.');
        }

        PropostasForm::recalcularTotaisDaProposta($proposta->id);
        $proposta = new Propostas($proposta->id);

        if ((float) $proposta->total_geral_com_desconto <= 0)
        {
            throw new Exception('Nao e permitido enviar proposta com totalizadores zerados.');
        }
    }

 private function registrarHistoricoPedido($pedido)
    {
        $hist = new PedidoFrotasHistorico();
        $hist->pedido_frotas_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d H:i:s');
        $hist->estado_pedido_frotas_id = EstadoPedido::AGUARDANDO; 
           $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $hist->aprovador_frotas_id = $aprovador[0]->id;
        }
  //      $hist->aprovador_frotas_id = TSession::getValue('iduser');
        $hist->store();
    }

    private function registrarHistoricoCotacao($cotacao)
    {
//        var_dump($cotacao);

        $histcotacao = new PropostasHistorico();
        $histcotacao->propostas_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d H:i:s');
        $histcotacao->estado_pedido_frotas_id = EstadoPedido::AGUARDANDO; 
           $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $histcotacao->aprovador_frotas_id = $aprovador[0]->id;
        }
    //    $histcotacao->aprovador_frotas_id = TSession::getValue('iduser');
        $histcotacao->store();
    }
    function onSetProject($param = null) {

        TSession::setValue('idproposta',null);
        TSession::setValue('idproposta',$param['id']);
        $this->onShow();

    }

}

