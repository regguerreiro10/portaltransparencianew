<?php

use Adianti\Database\TTransaction;

class CotacaoFormEmailVenda extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_CotacaoFormEmailVenda';

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
        $this->form->setFormTitle("Enviar email para aprovação da cotação");

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

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            // $container->add(TBreadCrumb::create(["Compras","CotacaoFormEmailVenda"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onEnviarEmailCotacao($param = null) 
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
                    $cotacao = new Cotacao(TSession::getValue('idcotacao'));

                 //   $mensagem = str_replace('{id}', $cotacao->id, $mensagem);
                    $mensagem = str_replace('{nome}', $cotacao->pessoa->nome, $mensagem);
                    $mensagem = str_replace('{id1}', $cotacao->id, $mensagem);
                    $mensagem = str_replace('{nome1}', $cotacao->pessoa->nome, $mensagem);

             //       $emailTemplate->titulo = str_replace('{id}', $cotacao->id, $emailTemplate->titulo);

                    // Atualiza o status do pedido e registra histórico
                    $pedido = new Pedido($cotacao->pedido_id);
                    $departamento = new DepartamentoUnit($pedido->departamento_unit_id);
                       //17-enviado
                       //19-comproposta
                    
                    $aprovacao_por_item = TSession::getValue('aprovacao_por_item');

                    // Define os estados válidos conforme o tipo de aprovação
                    if ($aprovacao_por_item == 2) {
                        // Aprovação por item: estados 17, 19
                        $estados_validos = [17, 19];
                    } else {
                        // Aprovação por contrato: estados 13, 17, 19
                        $estados_validos = [13, 17, 19];
                    }

                    if (in_array($pedido->estado_pedido_venda_id, $estados_validos)) {

                        if ($departamento->email) {
                            // MailService::send($departamento->email, $emailTemplate->titulo, $mensagem, 'html');

                            $cotacao->estado_pedido_id = EstadoPedido::AGUARDANDO;
                            $cotacao->store();
                            $this->registrarHistoricoCotacao($cotacao);

                           if (in_array($pedido->estado_pedido_venda_id, [EstadoPedido::COMPROPOSTA, EstadoPedido::ENVIADO])) {
                                $pedido->estado_pedido_venda_id = EstadoPedido::COMPROPOSTA;
                                $pedido->store();

                                $this->registrarHistoricoPedido($pedido);
                            

                            }
                          

                            new TMessage('info', 'Emails enviados!');
                        } else {
                            new TMessage('info', 'Emails não enviados, verifique o email do Departamento/Secretaria e/ou Unidade!');
                        }

                    } else {
                        new TMessage('error', 'Verifique com o gestor se este pedido ainda está ativo!');
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
                AdiantiCoreApplication::loadPage('CotacaoList', 'onShow', $param);

          //  }

            // fecha a cortina lateral
            TScript::create("Template.closeRightPanel();");

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {               

    } 

 private function registrarHistoricoPedido($pedido)
    {
        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::AGUARDANDO; 
        $hist->aprovador_id = TSession::getValue('iduser');
        $hist->store();
    }

    private function registrarHistoricoCotacao($cotacao)
    {
//        var_dump($cotacao);

        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::AGUARDANDO; 
        $histcotacao->aprovador_id = TSession::getValue('iduser');
        $histcotacao->store();
    }
    public function onSetProject($param = null)
    {
        try
        {
            TTransaction::open('minierp');

            if (empty($param['id'])) {
                throw new Exception('ID da cotação não informado.');
            }

            $cotacao_id = $param['id'];

            $itenscotacao = ItensCotacao::where('cotacao_id', '=', $cotacao_id)->load();

            if (!$itenscotacao) {
                throw new Exception('Não é possível enviar e-mail sem ter feito o orçamento!');
            }

            // (Opcional) Validação com valores, se necessário:
            /*
            $valor_total = 0;
            foreach ($itenscotacao as $item) {
                $valor_total += $item->valor ?? 0;
            }

            $total_produtos_servicos = TSession::getValue('total_produtos_servicos');
            if ($valor_total < $total_produtos_servicos) {
                throw new Exception('O valor do orçamento é inferior ao total de produtos/serviços.');
            }
            */

            TSession::setValue('idcotacao', $cotacao_id);

            $this->onShow();

            TTransaction::close();
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }


}

