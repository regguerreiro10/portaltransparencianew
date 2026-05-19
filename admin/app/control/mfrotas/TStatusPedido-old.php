<?php

use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TMinLengthValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Dialog\TMessage;

class TStatusPedido extends TPage {
    protected $form;
    private static $database = 'minierp';
    private static $formName = 'form_StatusPedidoForm';

    public function __construct($param) {
        parent::__construct();
       

         if (isset($param['tipoacao'])) {
            TSession::setValue('tipoacao', $param['tipoacao']);
        } 
      

        // cria o formulário
        $this->form = new BootstrapFormBuilder(self::$formName);
       $this->form->setFormTitle(
            (TSession::getValue('tipoacao') === 'Aprovar') ? 'Aprovar Proposta' :
            ((TSession::getValue('tipoacao') === 'PreAprovar') ? 'Pré-Aprovar Proposta' : 'Reprovar Proposta')
        );

  
        $obs = new TText('obs');
        
        $obs->setMaxLength(255);
  
        $obs->setSize('100%');


        $row2 = $this->form->addFields([new TLabel("Obs e/ou Justificativa*:", "#FF0000", '14px', null, '100%'),$obs]);
        $row2->layout = ['col-sm-12'];

        // Botão de salvar
        if (TSession::getValue('tipoacao') === 'Aprovar') {
            $btn_onsave = $this->form->addAction("Aprovar", new TAction([$this, 'onSaveAprovar']), 'fas:check #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary');
        } elseif (TSession::getValue('tipoacao') === 'Reprovar') {
            $btn_onsave = $this->form->addAction("Reprovar", new TAction([$this, 'onSaveReprovar']), 'fas:times #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary');
        } elseif (TSession::getValue('tipoacao') === 'PreAprovar') {
            $btn_onsave = $this->form->addAction("Pré Aprovar", new TAction([$this, 'onSavePreAprovar']), 'fas:check #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary');

        }

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=TStatusPedido]');
        $style->width = '60% !important';   
        $style->show(true);
    }

    // Método para exibir o modal com os dados
    public function onShowModal($param) {
      
        if (isset($param['id'])) {
            // Abre a transação para leitura do banco
            TTransaction::open(self::$database);

            // Recupera o id do Pedido
            $id = $param['id'];

            // Busca os dados do Pedido
            $proposta = new Propostas($id);
            TSession::setValue('idpedido', null);
            TSession::setValue('idpedido', $proposta->pedido_frotas_id);
            TSession::setValue('idproposta', null);
            TSession::setValue('idproposta', $proposta->id);
            TSession::setValue('tipoacao', null);
            TSession::setValue('tipoacao', $param['tipoacao']);
            // Finaliza a transação
             if (TSession::getValue('tipoacao') == 'Aprovar')
                {
                    $pedidofrotas = new PedidoFrotas(TSession::getValue('idpedido'));
                    if ($pedidofrotas->estado_pedido_frotas_id == EstadoPedidoFrotas::PREAPROVADO) {
                        //PEGAR JUSTIFICATIVA NO PEDIDO HISTORICO
                        $justificativa = PedidoFrotasHistorico::where('pedido_frotas_id', '=', TSession::getValue('idpedido'))
                            ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::PREAPROVADO)
                            ->orderBy('data_operacao', 'desc')
                            ->first();
                        if ($justificativa) {
                            $proposta->justificativa = $justificativa->obs;
                        } else {
                            $proposta->justificativa = '';
                        }
                    }

                }
                 $this->form->setData($proposta); // fill the form //</blockLine>
                TTransaction::close();

        } else {
            new TMessage('error', 'Nenhum Pedido selecionado.');
        }
    }

 

    public function onSaveAprovar($param)
    {
        try {
            TTransaction::open(self::$database);

            $pedidoId = TSession::getValue('idpedido');
            $propostaId = TSession::getValue('idproposta');
            $tipoAcao = TSession::getValue('tipoacao');
            $userId = TSession::getValue('userid');

            $object = new PedidoFrotas($pedidoId);
            $data = $this->form->getData();
            $object->fromArray((array) $data);

            if (empty($object->obs)) {
                throw new Exception('Campos vazios não são permitidos.');
            }

            $proposta = new Propostas($propostaId);

            // === Controle de aprovação por valor-base (onSaveAprovar) ===
            $unit = SystemUnit::where('id', '=', $object->system_unit_id)->load();

            // Se não houver unidade ou não houver valor base (>0), não testa nada
            if ($unit && (float)$unit[0]->valor_base_aprovacao > 0) {

                $valorBase  = (float)$unit[0]->valor_base_aprovacao;
                $valorTotal = (float)$proposta->valor_liquido;

                // Se o total ultrapassa o valor-base, avaliar alçada
                if ($valorTotal > $valorBase) {

                    // Se o pedido já está na etapa de aprovação por VB, e o usuário tem alçada, deixa seguir
                    // $usuarioPodeAprovarVB = $this->usuarioPodeAprovarVB(); // implemente conforme sua regra de permissão

                    // if ($usuarioPodeAprovarVB) {
                    //     // Usuário tem alçada → segue a aprovação normalmente
                    //     // (não faça nenhum teste adicional)
                    // } else {
                        // Usuário NÃO tem alçada → encaminha para APROVACAOVB, notifica gestor e barra aprovação agora
                        if (in_array(EstadoPedidoFrotas::APROVACAOVB, AprovadorFrotas::getEstadosDisponiveis())) {

                            // Notificar o gestor responsável pela aprovação por VB
                            $this->notificarGestorAprovacaoVB($object, $proposta, $valorBase, $valorTotal, $unit);

                            new TMessage(
                                'error',
                                sprintf(
                                    'Valor total da proposta (R$ %s) é MAIOR que o valor-base de aprovação da unidade (R$ %s). ' .
                                    'Pedido encaminhado para aprovação do gestor.',
                                    number_format($valorTotal, 2, ',', '.'),
                                    number_format($valorBase, 2, ',', '.')
                                )
                            );
                            return; 

                        } else {
                            
                            // Notificar o gestor responsável pela aprovação por VB
                            $this->notificarGestorAprovacaoVB($object, $proposta, $valorBase, $valorTotal, $unit);

                            new TMessage(
                                'error',
                                sprintf(
                                    'Valor total da proposta (R$ %s) é MAIOR que o valor-base de aprovação da unidade (R$ %s). ' .
                                    'Pedido encaminhado para aprovação do gestor.
                                    Confirmar o usuário que vai fazer aprovação.',
                                    number_format($valorTotal, 2, ',', '.'),
                                    number_format($valorBase, 2, ',', '.')
                                )
                            );
                            return; 
                        }
                    }
                //}
            }
            // === fim do controle VB ===

            // Verifica se já existe uma proposta aprovada para esse pedido
            $pedidoAprovado = false;
            $propostasDoPedido = Propostas::where('pedido_frotas_id', '=', $pedidoId)->load();

            foreach ($propostasDoPedido as $p) {
                if ($p->estado_pedido_frotas_id == EstadoPedidoFrotas::APROVADO) {
                    $pedidoAprovado = true;
                    break;
                }
            }
            if ($pedidoAprovado) {
                throw new Exception('Já existe uma proposta aprovada para este pedido.');
            }
            
            // Define estado da proposta conforme ação
            
            $proposta->obs = $object->obs;
            $proposta->estado_pedido_frotas_id = ($tipoAcao === 'Aprovar') ? EstadoPedidoFrotas::APROVADO : EstadoPedidoFrotas::REPROVADO;
            $proposta->store();

            // Atualiza o pedido
            $object->estabelecimento_id = $proposta->pessoa_id;
            $object->estado_pedido_frotas_id = EstadoPedidoFrotas::APROVADO;
            $object->valor_total_proposta = $proposta->valor_total;
            $object->valor_desconto_proposta = $proposta->valor_desconto;
            $object->valor_liquido_proposta = $proposta->valor_liquido;
            $object->store(); 

            // Históricos
            $aprovador = AprovadorFrotas::where('system_users_id', '=', $userId)->load();

            foreach ($aprovador as $aprovadores) {
                 $histPedido = new PedidoFrotasHistorico();
                $histPedido->pedido_frotas_id = $object->id;
                $histPedido->aprovador_frotas_id = $aprovadores->id;
                $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $object->obs;
                $histPedido->store();


                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id =$aprovadores->id;
                $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs = $object->obs;
                $histProposta->store();
                break;
            }

           

        

            $this->AtualizarItensPedido($object->id, $proposta->id);

            // Criar manutenção com base na proposta aprovada
            if ($tipoAcao === 'Aprovar') {
                $itens = ItensPropostas::where('propostas_id', '=', $proposta->id)->load();
                foreach ($itens as $item) {
                    $add = false;
                    $data_garantia = null;

                    if ($item->qtdekmgarantia > 0) {
                        $km_atual = $object->km;
                        $media_km_dia = 50;
                        $km_faltante = $item->qtdekmgarantia - $km_atual;

                        if ($km_faltante > 0 && $media_km_dia > 0) {
                            $dias_estimados = ceil($km_faltante / $media_km_dia);
                            $data_garantia = date('Y-m-d', strtotime("+$dias_estimados days"));
                            $add = true;
                        }
                    }

                    if ($item->diasdegarantia > 0) {
                        $data_garantia = date('Y-m-d', strtotime($item->created_at . " +{$item->diasdegarantia} days"));
                        $add = true;
                    }

                    if ($add) {
                        $manutencao = new ManutencaoGarantia();
                        $manutencao->itens_propostas_id = $item->id;
                        $manutencao->veiculos_id = $object->veiculos_id;
                        $manutencao->pedido_frotas_id = $object->id;
                        $manutencao->propostas_id = $proposta->id;
                        $manutencao->created_at = date('Y-m-d H:i:s');
                        $manutencao->tipo = $item->tipo;
                        $manutencao->km_manutencao = $item->qtdekmgarantia;
                        $manutencao->dias_garantia = $item->diasdegarantia;
                        $manutencao->datagarantia = $data_garantia;
                        $manutencao->descricao = $item->descricao;
                        $manutencao->produto_id = $item->produto_id;
                        $manutencao->obs = $object->obs;
                        $manutencao->qtde = $item->qtde;
                        $manutencao->ativo = 'S';
                        $manutencao->store();
                    }
                }
            }
            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = $object->id;

      

            TTransaction::close();

            TApplication::loadPage('PedidoFrotasList', 'onReload');
            TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);
       //     new TMessage('info', 'Aprovação da proposta realizada com sucesso!');
            TScript::create("Template.closeRightPanel();"); 

        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onSavePreAprovar($param)
    {
        try {
            TTransaction::open(self::$database);
                        $messageAction = null;


            $pedidoId = TSession::getValue('idpedido');
            $propostaId = TSession::getValue('idproposta');
            $userId = TSession::getValue('userid');

            $object = new PedidoFrotas($pedidoId);
            $data = $this->form->getData();
            $object->fromArray((array) $data);

            if (empty($object->obs)) {
                throw new Exception('Campos vazios não são permitidos.');
            }

          
            // Define estado da proposta conforme ação
            $proposta = new Propostas($propostaId);
            $proposta->obs = $object->obs;
            $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
            $proposta->store();

            // Atualiza o pedido
          //  $object->estabelecimento_id = $proposta->pessoa_id;
            $object->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
            $object->valor_total_proposta = $proposta->valor_total;
            $object->valor_desconto_proposta = $proposta->valor_desconto;
            $object->valor_liquido_proposta = $proposta->valor_liquido;
            $object->store(); 

            // Históricos
            $aprovador = AprovadorFrotas::where('system_users_id', '=', $userId)->load();

            foreach ($aprovador as $aprovadores) {
                 $histPedido = new PedidoFrotasHistorico();
                $histPedido->pedido_frotas_id = $object->id;
                $histPedido->aprovador_frotas_id = $aprovadores->id;
                $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $object->obs;
                $histPedido->store();


                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id =$aprovadores->id;
                $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs = $object->obs;
                $histProposta->store();
                break;
            }
            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = $object->id;

      

            TTransaction::close();

            TApplication::loadPage('PedidoFrotasList', 'onReload');

        //    new TMessage('info','Pré Aprovação da proposta realizada com sucesso!');
            TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);
            TScript::create("Template.closeRightPanel();"); 


        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
    public function AtualizarItensPedido($pedidoId, $propostaId)
    {
        // Lógica para atualizar os itens do pedido
        $itens = ItensPropostas::where('propostas_id', '=', $propostaId)->load();
        // Verifica se existem itens para atualizar
        if (empty($itens)) {
            throw new Exception('Nenhum item encontrado para atualizar.');
        }
     
        // Adiciona os novos itens ao pedido
        // Verifica se existem itens para adicionar

        foreach ($itens as $item) {
            // Atualiza o pedido_frotas_id para cada item
            if ($item->itens_pedido_frotas_id) {
                $itemPedidoFrotas = new ItensPedidoFrotas($item->itens_pedido_frotas_id);
            } else {
                $itemPedidoFrotas = new ItensPedidoFrotas();
            }
            $itemPedidoFrotas->tipo = $item->tipo;
            $itemPedidoFrotas->descricao = $item->descricao;
            $itemPedidoFrotas->qtde = $item->qtde;
            $itemPedidoFrotas->valor_unitario = $item->valor_unitario;
            $itemPedidoFrotas->valor_desconto = $item->valor_desconto;
            $itemPedidoFrotas->valor_total = $item->valor_total;
            $itemPedidoFrotas->marca_modelo = $item->marca_modelo;
            $itemPedidoFrotas->fabricante = $item->fabricante;
            $itemPedidoFrotas->codigo = $item->codigo;
            $itemPedidoFrotas->qtdekmgarantia = $item->qtdekmgarantia;
            $itemPedidoFrotas->diasdegarantia = $item->diasdegarantia;
            $itemPedidoFrotas->qtdehoras = $item->qtdehoras;
            $itemPedidoFrotas->perc_desconto = $item->perc_desconto;
            $itemPedidoFrotas->pedido_frotas_id = $pedidoId;
            $itemPedidoFrotas->created_at = date('Y-m-d H:i:s');
            $itemPedidoFrotas->store();
        }
        // remover itens pedido frotas
        $itensPedido = ItensPedidoFrotas::where('pedido_frotas_id', '=', $pedidoId)->load();
        foreach ($itensPedido as $item) {
            $itenspropostas = ItensPropostas::where('propostas_id', '=', $propostaId)
                                            ->where('descricao', '=', $item->descricao)
                                            ->where('tipo', '=', $item->tipo)
                                            ->where('qtde', '=', $item->qtde)
                                            ->load();
            // Verifica se o item não está mais na proposta
            if (empty($itenspropostas)) {
            ///    $item->delete();
            }
           
        }
    }
    

    public function onSaveReprovar($param)
    {
        try {
            TTransaction::open(self::$database);

            $pedidoId = TSession::getValue('idpedido');
            $propostaId = TSession::getValue('idproposta');
            $tipoAcao = TSession::getValue('tipoacao');
            $userId = TSession::getValue('userid');

            $object = new PedidoFrotas($pedidoId);
            $data = $this->form->getData();
            $object->fromArray((array) $data);

            if (empty($object->obs)) {
                throw new Exception('Campos vazios não são permitidos.');
            }
   

           

          
            // Verifica se já existe uma proposta aprovada para esse pedido
            $propostasDoPedido = Propostas::where('pedido_frotas_id', '=', $pedidoId)->load();

            $pedidoReprovado = true;

            foreach ($propostasDoPedido as $p) {
                if ($p->estado_pedido_frotas_id != EstadoPedidoFrotas::REPROVADO) {
                    $pedidoReprovado = false;
                    break; // não precisa continuar se já encontrou um não reprovado
                }
            }
            
           
            if ($pedidoReprovado)
            {
                $object->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                $object->store();

                // Históricos
                $aprovador = AprovadorFrotas::where('system_users_id', '=', $userId)->load();

                $histPedido = new PedidoFrotasHistorico();
                $histPedido->pedido_frotas_id = $object->id;
                $histPedido->aprovador_frotas_id = $aprovador[0]->id;
                $histPedido->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $object->obs;
                $histPedido->store();

                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id = $aprovador[0]->id;
                $histProposta->estado_pedido_frotas_id =EstadoPedidoFrotas::REPROVADO;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs = $object->obs;
                $histProposta->store();

            } else {

                  // Define estado da proposta conforme ação
                $proposta = new Propostas($propostaId);
                $proposta->obs = $object->obs;
                $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                $proposta->store();

                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id = $aprovador[0]->id;
                $histProposta->estado_pedido_frotas_id =EstadoPedidoFrotas::REPROVADO;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs = $object->obs;
                $histProposta->store();
 
            }
            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = $object->id;

      

            TTransaction::close();

            TApplication::loadPage('PedidoFrotasList', 'onReload');
            TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);
     //       new TMessage('info', 'Reprovação da proposta realizada com sucesso!');
            TScript::create("Template.closeRightPanel();"); 

           

        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    private function notificarGestorAprovacaoVB($pedido, $propostas, float $valorBase, float $valorTotal, $unit): void
    {
        // ID correto do template
        $codigo_email_template_id = EmailTemplate::NOTIFICACAO_VALORBASE;

        try {
            $emailTemplate = new EmailTemplate($codigo_email_template_id);
            if (!$emailTemplate) {
                throw new Exception('Template de e-mail não encontrado.');
            }

        
                     // Dados básicos

            // Carrega veículo e compõe identificação
            $veiculo = $pedido->veiculos_id ? new Veiculos($pedido->veiculos_id) : null;
            $marca  = $veiculo && $veiculo->marca ? $veiculo->marca->descricao : '';
            $modelo = $veiculo && $veiculo->modelo ? $veiculo->modelo->descricao : '';
            $placa  = $veiculo && $veiculo->placa ? $veiculo->placa : '';
            $identificacaoVeiculo = trim($placa . ' - ' . $marca . ' - ' . $modelo, ' -');

            // Mensagem / título originais
            $titulo   = (string) $emailTemplate->titulo;
            $mensagem = (string) $emailTemplate->mensagem;

            // Formatações numéricas (BR)
            $valorTotalFmt = number_format($valorTotal, 2, ',', '.');
            $valorBaseFmt  = number_format($valorBase, 2, ',', '.');


                //aprovadores 
            // usuario do mesmo orgão e que tenha o aprovacaovb
            $repo     = new TRepository('AprovadorFrotas');
            $criteria = new TCriteria();

            // filtro por subselect do estado APROVADORVB
            $criteria->add(new TFilter(
                'id',
                'IN',
                '(SELECT aprovador_frotas_id
                    FROM estado_pedido_frotas_aprovador
                WHERE estado_pedido_frotas_id = ' . EstadoPedidoFrotas::APROVACAOVB . ')'
            ));

            // filtro por unidade ativa da sessão
            $criteria->add(new TFilter(
                'system_users_id', // ou 'system_user_id'
                'IN',
                '(SELECT system_user_id
                    FROM system_user_unit
                WHERE system_unit_id = ' . (int) TSession::getValue('idunit') . ')'
            ));

            $aprovadores = $repo->load($criteria);   

            foreach ($aprovadores as $aps)
            {
                $usr = new SystemUsers($aps->system_users_id);

                // Placeholders suportados no template
                $replacements = [
                    '{nome_aprovador}'       => $usr->name ?? '',
                    '{id}'                   => $pedido->id ?? '',
                    '{data_pedido}'          => isset($pedido->dt_pedido) ? TDate::date2br($pedido->dt_pedido) : '',
                    '{valor_pedido}'         => $valorTotalFmt,
                    '{descricao_pedido}'     => $pedido->descricaopedido ?? '',
                    '{identificacao_veiculo}'=> $identificacaoVeiculo,
                    '{unidade}'              => $pedido->system_unit->name ?? ($unit->name ?? ''), // usa $unit se vier
                    '{departamento}'         => $pedido->departamento_unit->name ?? '',
                ];

                // Aplica substituições de uma vez
                $titulo   = strtr($titulo, $replacements);
                $mensagem = strtr($mensagem, $replacements);

                // Caso use renderização com variáveis do ActiveRecord
                if (method_exists($pedido, 'render')) {
                    $titulo   = $pedido->render($titulo);
                    $mensagem = $pedido->render($mensagem);
                }

                // Notificação + e-mail
                if (!empty($propostas->pessoa->email)) {
                    $pessoa = new Pessoa($propostas->pessoa_id);

                    $notificationParam = ['key' => $propostas->id];
                    $icon = 'fas fa-file-invoice-dollar';

                    SystemNotification::registerpedidofrotas(
                        $usr->id,
                        $titulo,
                        $mensagem,
                        new TAction(['PropostaPendenteList', 'onShow'], $notificationParam),
                        'Visualizar Proposta',
                        $icon
                    );

                    MailService::send($usr->email, $titulo, $mensagem, 'html');
                }
            }
        } catch (Exception $e) {
            // Logue de acordo com seu padrão
            TLog::error('EMAIL_NOTIF_APROVACAO', $e->getMessage());
            // opcional: TToast::show('error', 'Falha ao enviar notificação: ' . $e->getMessage());
        }
    }

}


?>
