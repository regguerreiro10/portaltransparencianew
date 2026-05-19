<?php

use Adianti\Database\TTransaction;

class TStatusPedido extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'PedidoFrotas';
    private static $primaryKey = 'id';
    private static $formName = 'form_TStatusPedidoFrotasForm';

    use BuilderMasterDetailFieldListTrait;

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
        $this->form->setFormTitle("Cadastro Aprovação de Pedido");

        $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id = new TCriteria();
        $filterVar = TSession::getValue('entidade');
        $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->add(new TFilter('saldo_entidade_contrato_id', 'in', "(SELECT id FROM saldo_entidade_contrato WHERE  deleted_at is null AND entidade_id in (SELECT id FROM entidade WHERE id = '{$filterVar}'))")); 

        if (isset($param['tipoacao'])) {
            TSession::setValue('tipoacao', $param['tipoacao']);
        } 
        $this->form->setFormTitle(
            (TSession::getValue('tipoacao') === 'Aprovar') ? 'Aprovar Proposta' :
            ((TSession::getValue('tipoacao') === 'PreAprovar') ? 'Pré-Aprovar Proposta' : 'Reprovar Proposta')
        );

      
        $id = new TEntry('id');
        $propostas_id = new TEntry('propostas_id');
        $justificativa = new TText('justificativa');
        $total_produtos = new TNumeric('total_produtos', '2', ',', '.' );
        $total_servicos = new TNumeric('total_servicos', '2', ',', '.' );
        $total_produtos_servicos = new TNumeric('total_produtos_servicos', '2', ',', '.' );


        if (TSession::getValue('tipoacao') != 'Reprovar'
            && TSession::getValue('idunit') != 26) {
            $dotacao_pedido_frotas_pedido_frotas_id = new THidden('dotacao_pedido_frotas_pedido_frotas_id[]');
            $dotacao_pedido_frotas_pedido_frotas___row__id = new THidden('dotacao_pedido_frotas_pedido_frotas___row__id[]');
            $dotacao_pedido_frotas_pedido_frotas___row__data = new THidden('dotacao_pedido_frotas_pedido_frotas___row__data[]');
        //    $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id = new TDBCombo('dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id[]', 'minierp', 'SaldoDepartamento', 'id', '{id}','id asc' , $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id );
            $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id = new TDBCombo('dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id[]', 'minierp', 'SaldoDepartamento', 'id', '{departamento_unit->name} - {numero_documento_empenho} - {valor_empenho_formatado} - {tipos}', 'numero_documento_empenho asc', $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id);
            $dotacao_pedido_frotas_pedido_frotas_saldo_atual = new TNumeric('dotacao_pedido_frotas_pedido_frotas_saldo_atual[]', '2', ',', '.' );
            $dotacao_pedido_frotas_pedido_frotas_valor = new TNumeric('dotacao_pedido_frotas_pedido_frotas_valor[]', '2', ',', '.' );
            $this->fieldList_6881430e7887f = new TFieldList();
            $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->setChangeAction(new TAction([$this,'onCalcValor']));

            $this->fieldList_6881430e7887f->addField(null, $dotacao_pedido_frotas_pedido_frotas_id, []);
            $this->fieldList_6881430e7887f->addField(null, $dotacao_pedido_frotas_pedido_frotas___row__id, ['uniqid' => true]);
            $this->fieldList_6881430e7887f->addField(null, $dotacao_pedido_frotas_pedido_frotas___row__data, []);
            $this->fieldList_6881430e7887f->addField(new TLabel("Saldo departamento: *", '#FF0000', '14px', null), $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id, ['width' => '100%']);
            $this->fieldList_6881430e7887f->addField(new TLabel("Saldo atual:", null, '14px', null), $dotacao_pedido_frotas_pedido_frotas_saldo_atual, ['width' => '100%']);
            $this->fieldList_6881430e7887f->addField(new TLabel("Valor: *", '#FF0000', '14px', null), $dotacao_pedido_frotas_pedido_frotas_valor, ['width' => '100%']);

            $this->fieldList_6881430e7887f->width = '100%';
            $this->fieldList_6881430e7887f->setFieldPrefix('dotacao_pedido_frotas_pedido_frotas');
            $this->fieldList_6881430e7887f->name = 'fieldList_6881430e7887f';

            $this->criteria_fieldList_6881430e7887f = new TCriteria();
            $this->default_item_fieldList_6881430e7887f = new stdClass();

            $this->form->addField($dotacao_pedido_frotas_pedido_frotas_id);
            $this->form->addField($dotacao_pedido_frotas_pedido_frotas___row__id);
            $this->form->addField($dotacao_pedido_frotas_pedido_frotas___row__data);
            $this->form->addField($dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id);
            $this->form->addField($dotacao_pedido_frotas_pedido_frotas_saldo_atual);
            $this->form->addField($dotacao_pedido_frotas_pedido_frotas_valor);

            $this->fieldList_6881430e7887f->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");
            $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->addValidation("dotação orçamentária", new TRequiredListValidator()); 
            $dotacao_pedido_frotas_pedido_frotas_valor->addValidation("Valor", new TRequiredListValidator()); 

            $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->enableSearch();
            $dotacao_pedido_frotas_pedido_frotas_saldo_atual->setEditable(false);
            // $dotacao_pedido_frotas_pedido_frotas_valor->setSize('100%');
            // $dotacao_pedido_frotas_pedido_frotas_saldo_atual->setSize('100%');
            // $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->setSize(400);
        }
        //  $itens_produtos = new BPageContainer();
        // $itens_servicos = new BPageContainer();

        //   $itens_produtos->setId('b67eab55f35fb6');
        // $itens_servicos->setId('b67eb5821b8051');

        // $itens_produtos->hide();
        // $itens_servicos->hide();
       // Passe os parâmetros que as listas precisam
        // $action = new TAction(['ItensPropostasProdutosStatusPedidoList', 'onShow']);
        // $action->setParameter('propostas_id', $param['key']); // valor fixo/variável no seu contexto
        // $itens_produtos->setAction($action);

        // $action1 = new TAction(['ItensPropostasServicosStatusPedidoList', 'onShow']);
        // $action1->setParameter('propostas_id', $param['key']); // valor fixo/variável no seu contexto
        // $itens_servicos->setAction($action1);

        // $itens_produtos->setAction(new TAction(['ItensPropostasProdutosStatusPedidoList', 'onShow']));
        // $itens_servicos->setAction(new TAction(['ItensPropostasServicosStatusPedidoList', 'onShow']));

        $justificativa->addValidation("Justificativa", new TRequiredValidator()); 
        $total_produtos->setEditable(false);
        $total_servicos->setEditable(false);
        $total_produtos_servicos->setEditable(false);

         $id->setSize('100%');
        $justificativa->setSize('100%', 70);
        $propostas_id->setSize('100%');
        $total_produtos->setSize('100%');
        $total_servicos->setSize('100%');
        $total_produtos_servicos->setSize('100%');
        $id->setEditable(false);
        $propostas_id->setEditable(false);
    //    $itens_produtos->setSize('100%');
    //     $itens_servicos->setSize('100%');

        // $loadingContainer = new TElement('div');
        // $loadingContainer->style = 'text-align:center; padding:50px';

        // $icon = new TElement('i');
        // $icon->class = 'fas fa-spinner fa-spin fa-3x';

        // $loadingContainer->add($icon);
        // $loadingContainer->add('<br>Carregando');

        // $itens_produtos->add($loadingContainer);

        // $loadingContainer = new TElement('div');
        // $loadingContainer->style = 'text-align:center; padding:50px';

        // $icon = new TElement('i');
        // $icon->class = 'fas fa-spinner fa-spin fa-3x';

        // $loadingContainer->add($icon);
        // $loadingContainer->add('<br>Carregando');

        // $itens_servicos->add($loadingContainer);

        // $loadingContainer = new TElement('div');
        // $loadingContainer->style = 'text-align:center; padding:50px';

        // $icon = new TElement('i');
        // $icon->class = 'fas fa-spinner fa-spin fa-3x';

        // $loadingContainer->add($icon);
        // $loadingContainer->add('<br>Carregando');


        // $this->itens_produtos = $itens_produtos;
        // $this->itens_servicos = $itens_servicos;


        
        $row0 = $this->form->addFields([new TLabel("ID Pedido", null, '14px', null),$id],[new TLabel("ID Proposta", null, '14px', null),$propostas_id]);
        $row0->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Justificativa: *", '#FF0000', '14px', null),$justificativa]);
        $row2->layout = ['col-sm-12'];

        // $row701 = $this->form->addFields([new TFormSeparator("<BR>Produtos", '#333', '18', '#eee')]);
        // $row701->layout = [' col-sm-12'];

        // $row801 = $this->form->addFields([$itens_produtos]);
        // $row801->layout = [' col-sm-12'];

        // // $row901 = $this->form->addFields([new TFormSeparator("<BR>Serviços", '#333', '18', '#eee')]);
        // // $row901->layout = [' col-sm-12'];

        // $row1001 = $this->form->addFields([$itens_servicos]);
        // $row1001->layout = [' col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("Total de Produtos", null, '14px', null, '100%'),$total_produtos],[new TLabel("Total de Serviços", null, '14px', null, '100%'),$total_servicos], [new TLabel("Total Geral", null, '14px', null, '100%'),$total_produtos_servicos]);
        $row3->layout = ['col-sm-4','col-sm-4','col-sm-4'];
        if (TSession::getValue('tipoacao') <> 'Reprovar'  && TSession::getValue('idunit') != 26) {
            $row4 = $this->form->addFields([new TFormSeparator("<br>"."Dotação Orçamentária *", '#FF0000', '18', '#eee')]);
            $row4->layout = [' col-sm-12'];

            $row5 = $this->form->addFields([$this->fieldList_6881430e7887f]);
            $row5->layout = [' col-sm-12'];
        }

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

        /*

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['TStatusPedidoFrotasList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

       */

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);
          $style = new TStyle('right-panel > .container-part');
        $style->width = '65% !important';
         $style->show(true);

    }

    public function onSaveAprovar($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $pedidoId = TSession::getValue('idpedido');
            $propostaId = TSession::getValue('idproposta');
            $tipoAcao = TSession::getValue('tipoacao');
            $userId = TSession::getValue('userid');

            $messageAction = null;

            //$this->form->validate(); // validate form data
            
          
            $object = new PedidoFrotas($pedidoId); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            if (empty($data->justificativa)) {
                throw new Exception('Justificativa é obrigatória.');
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

                            // new TMessage(
                            //     'error',
                                // sprintf(
                                //     'Valor total da proposta (R$ %s) é MAIOR que o valor-base de aprovação da unidade (R$ %s). ' .
                                //     'Pedido encaminhado para aprovação do gestor.',
                                //     number_format($valorTotal, 2, ',', '.'),
                                //     number_format($valorBase, 2, ',', '.')
                                // )
                            // );

                            throw new Exception(
                                sprintf(
                                    'Valor total da proposta (R$ %s) é MAIOR que o valor-base de aprovação da unidade (R$ %s). ' .
                                    'Pedido encaminhado para aprovação do gestor.',
                                    number_format($valorTotal, 2, ',', '.'),
                                    number_format($valorBase, 2, ',', '.')
                                ));

                            
                            

                        } else {
                            
                            // Notificar o gestor responsável pela aprovação por VB
                            $this->notificarGestorAprovacaoVB($object, $proposta, $valorBase, $valorTotal, $unit);

                            // new TMessage(
                            //     'error',
                            //     sprintf(
                            //         'Valor total da proposta (R$ %s) é MAIOR que o valor-base de aprovação da unidade (R$ %s). ' .
                            //         'Pedido encaminhado para aprovação do gestor.
                            //         Confirmar o usuário que vai fazer aprovação.',
                            //         number_format($valorTotal, 2, ',', '.'),
                            //         number_format($valorBase, 2, ',', '.')
                            //     )
                            // );

                            throw new Exception(
                                sprintf(
                                    'Valor total da proposta (R$ %s) é MAIOR que o valor-base de aprovação da unidade (R$ %s). ' .
                                    'Pedido encaminhado para aprovação do gestor.
                                    Confirmar o usuário que vai fazer aprovação.',
                                    number_format($valorTotal, 2, ',', '.'),
                                    number_format($valorBase, 2, ',', '.')
                                ));
                            
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
            $object->valor_total = $proposta->valor_total;
            $object->valor_total_proposta = $proposta->valor_total;
            $object->valor_desconto_proposta = $proposta->valor_desconto;
            $object->valor_liquido_proposta = $proposta->valor_liquido;
            $object->data_aprovacao = date('Y-m-d H:i:s');
            $object->store(); 

            $propostasDoPedido = Propostas::where('pedido_frotas_id','=', $object->id)->load();

            // Reprova todas as outras propostas do pedido, exceto a atual
            foreach ($propostasDoPedido as $p) {
                if ($p->id != $propostaId && $p->estado_pedido_frotas_id == EstadoPedidoFrotas::AGUARDANDO) {
                    $p->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                    $p->obs = 'Reprovada automaticamente após aprovação de outra proposta';
                    $p->store();
                    
                    // Opcional: salva histórico da reprovação
                    $histReprovada = new PropostasHistorico();
                    $histReprovada->propostas_id = $p->id;
                    $histReprovada->aprovador_frotas_id = $aprovadores->id ?? null;
                    $histReprovada->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                    $histReprovada->data_historico = date('Y-m-d H:i:s');
                    $histReprovada->obs = 'Reprovada automaticamente após aprovação de outra proposta';
                    $histReprovada->store();
                }
            }

            // Históricos
            $aprovador = AprovadorFrotas::where('system_users_id', '=', $userId)->load();

            foreach ($aprovador as $aprovadores) {
                $histPedido = new PedidoFrotasHistorico();
                $histPedido->pedido_frotas_id = $object->id;
                $histPedido->aprovador_frotas_id = $aprovadores->id;
                $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $data->justificativa;
                $histPedido->store();


                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id =$aprovadores->id;
                $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs =  $data->justificativa;
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
                        if (TSession::getValue('tipofrota')==2) {
                            // Frota de máquinas -> usar horas
                            $horimetro_atual = (float) ($object->km ?? 0);
                            $horas_garantia  = (float) $item->qtdekmgarantia; // campo já existente
                            
                            $horas_restantes = $horas_garantia - $horimetro_atual;
                            $media_horas_dia = 5; // ajuste conforme o uso real
                            
                            if ($horas_restantes > 0 && $media_horas_dia > 0) {
                                $dias_estimados = ceil($horas_restantes / $media_horas_dia);
                                $data_garantia  = date('Y-m-d', strtotime("+$dias_estimados days"));
                                $add = true;
                            }                       
                        } 
                        else {
                            $km_atual = $object->km;
                            $media_km_dia = 50;
                            $km_faltante = $item->qtdekmgarantia - $km_atual;

                            if ($km_faltante > 0 && $media_km_dia > 0) {
                                $dias_estimados = ceil($km_faltante / $media_km_dia);
                                $data_garantia = date('Y-m-d', strtotime("+$dias_estimados days"));
                                $add = true;
                            }
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
                        $manutencao->obs = $data->justificativa;
                        $manutencao->qtde = $item->qtde;
                        $manutencao->ativo = 'S';
                        $manutencao->ciclos_manutencao = $item->ciclos;
                        $manutencao->tbo_horas = $item->tbo_horas;
                        $manutencao->tbo_ciclos = $item->tbo_ciclos;
                        $manutencao->tsn_horas = $item->tsn_horas;
                        $manutencao->tso_horas = $item->tso_horas;
                        $manutencao->csn_ciclos = $item->csn_ciclos;
                        $manutencao->cso_ciclos = $item->cso_ciclos;
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

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $valor = 0;
            if ( TSession::getValue('idunit') != 26) {
                $dotacao_pedido_frotas_pedido_frotas_items = $this->storeItems(
                    'DotacaoPedidoFrotas',
                    'pedido_frotas_id',
                    $object,
                    $this->fieldList_6881430e7887f,
                    function($masterObject, $detailObject) use (&$valor) {
                        // Corrige valor numérico se vier sem ponto
                        $detailObject->valor = str_replace(',', '', $detailObject->valor);
                        $detailObject->saldo_atual = str_replace(',', '', $detailObject->saldo_atual);
                        $detailObject->propostas_id = $masterObject->propostas_id;
                        $valor += $detailObject->valor;
                    },
                    $this->criteria_fieldList_6881430e7887f
                );
            }
             $valor = (float) $valor;
            $total_produtos_servicos = (float) TSession::getValue('total_produtos_servicos');
            $valor = round($valor, 2);
            $total_produtos_servicos = round($total_produtos_servicos, 2);

            // if ($valor > ($total_produtos_servicos)) {
            //     throw new Exception('Valor total da dotação orçamentária não pode ser maior que o valor total do pedido.');
            // } elseif ($valor < $total_produtos_servicos) {
            //     throw new Exception('Valor total da dotação orçamentária não pode ser menor que o valor total do pedido.');
            // }
            
         

    //        $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro aprovado com sucesso!", 'topRight', 'far:check-circle');
            
           TApplication::loadPage('PedidoFrotasList', 'onReload');
           TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);

             TScript::create("Template.closeRightPanel();");
            //TForm::sendData(self::$formName, (object)['id' => $object->id]);

        }
        catch (Exception $e) // in case of exception
        {

            new TMessage('error', $e->getMessage()); // shows the exception error message

            $objectpro = new stdClass();
            $objectpro->id = $pedidoId;
            $objectpro->propostas_id = $propostaId;
            $objectpro->total_produtos = TSession::getValue('total_produtos');
            $objectpro->total_servicos = TSession::getValue('total_servicos');
            $objectpro->justificativa = $object->justificativa;
            $objectpro->total_produtos_servicos = TSession::getValue('total_produtos_servicos');


            $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

               //code here
               $detailObject->valor = str_replace(',', '', $detailObject->valor);
               $detailObject->saldo_atual = str_replace(',', '', $detailObject->saldo_atual);
               $detailObject->propostas_id = $masterObject->propostas_id;

            }, $this->criteria_fieldList_6881430e7887f); 
            TForm::sendData('form_TStatusPedidoFrotasForm', $objectpro);

            TTransaction::rollback(); // undo all pending operations
        }
    }

    function toFloat($valor) {
        // Se for string com vírgula como decimal (formato brasileiro): "4.719,48"
        if (is_string($valor) && preg_match('/\d+[\.,]?\d*/', $valor)) {
            $valor = str_replace('.', '', $valor); // remove separador de milhar
            $valor = str_replace(',', '.', $valor); // troca vírgula por ponto decimal
        }
        return (float) $valor;
    }
    public function onSavePreAprovar($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $pedidoId = TSession::getValue('idpedido');
            $propostaId = TSession::getValue('idproposta');
            $tipoAcao = TSession::getValue('tipoacao');
            $userId = TSession::getValue('userid');

            $messageAction = null;

            //$this->form->validate(); // validate form data
            
          
            $object = new PedidoFrotas($pedidoId); // create an empty object 
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            if (empty($data->justificativa)) {
                throw new Exception('Justificativa é obrigatória.');
            }

            // Define estado da proposta conforme ação
            $proposta = new Propostas($propostaId);
            $proposta->obs = $object->obs;
            $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
            $proposta->store();

            // Atualiza o pedido
          //  $object->estabelecimento_id = $proposta->pessoa_id;
            $object->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
            $object->valor_total = $proposta->valor_total;
            $object->valor_total_proposta = $proposta->valor_total;
            $object->valor_desconto_proposta = $proposta->valor_desconto;
            $object->valor_liquido_proposta = $proposta->valor_liquido;
            $object->data_aprovacao = date('Y-m-d H:i:s');
            $object->store(); 

            // Históricos
            $aprovador = AprovadorFrotas::where('system_users_id', '=', $userId)->load();

            foreach ($aprovador as $aprovadores) {
                 $histPedido = new PedidoFrotasHistorico();
                $histPedido->pedido_frotas_id = $object->id;
                $histPedido->aprovador_frotas_id = $aprovadores->id;
                $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $data->justificativa;
                $histPedido->store();


                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id =$aprovadores->id;
                $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs = $data->justificativa;
                $histProposta->store();
                break;
            }

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = $object->id;
            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $valor = 0;
            $dotacao_pedido_frotas_pedido_frotas_items = $this->storeItems(
                'DotacaoPedidoFrotas',
                'pedido_frotas_id',
                $object,
                $this->fieldList_6881430e7887f,
                function($masterObject, $detailObject) use (&$valor) {
                    $valor += $detailObject->valor;
                    $detailObject->propostas_id = $masterObject->propostas_id;
                },
                $this->criteria_fieldList_6881430e7887f
            );
            $valor = (float) $valor;
            $total_produtos_servicos = (float) TSession::getValue('total_produtos_servicos');
            $valor = round($valor, 2);
            $total_produtos_servicos = round($total_produtos_servicos, 2);

            // if ($valor > ($total_produtos_servicos)) {
            //     throw new Exception('Valor total da dotação orçamentária não pode ser maior que o valor total do pedido.');
            // } elseif ($valor < $total_produtos_servicos) {
            //     throw new Exception('Valor total da dotação orçamentária não pode ser menor que o valor total do pedido.');
            // }
            

    //        $this->form->setData($data); // fill form data
            TTransaction::close();

            TToast::show('success', "Registro pre-aprovado com sucesso!", 'topRight', 'far:check-circle');
            
           TApplication::loadPage('PedidoFrotasList', 'onReload');
           TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);

             TScript::create("Template.closeRightPanel();");
            //TForm::sendData(self::$formName, (object)['id' => $object->id]);

        }
        catch (Exception $e) // in case of exception
        {

            new TMessage('error', $e->getMessage()); // shows the exception error message

            $objectpro = new stdClass();
            $objectpro->id = $pedidoId;
            $objectpro->propostas_id = $propostaId;
            $objectpro->total_produtos = TSession::getValue('total_produtos');
            $objectpro->total_servicos = TSession::getValue('total_servicos');
            $objectpro->justificativa = $object->justificativa;
            $objectpro->total_produtos_servicos = TSession::getValue('total_produtos_servicos');


            $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

               //code here
               $detailObject->valor = str_replace(',', '', $detailObject->valor);
               $detailObject->saldo_atual = str_replace(',', '', $detailObject->saldo_atual);
               $detailObject->propostas_id = $masterObject->propostas_id;

            }, $this->criteria_fieldList_6881430e7887f); 
            TForm::sendData('form_TStatusPedidoFrotasForm', $objectpro);

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

                $object = new PedidoFrotas($key); // instantiates the Active Record 

                
                $histPedido = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $object->id)
                    ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::COMPROPOSTA)
                    ->orderBy('data_operacao', 'desc')
                    ->last();
                if ($histPedido) {
                    $object->justificativa = $histPedido->obs;
                } else {
                    $object->justificativa = '';
                }


                $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_6881430e7887f); 

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
        if (TSession::getValue('tipoacao') <> 'Reprovar'  && TSession::getValue('idunit') != 26) {
            $this->fieldList_6881430e7887f->addHeader();
            $this->fieldList_6881430e7887f->addDetail($this->default_item_fieldList_6881430e7887f);

            $this->fieldList_6881430e7887f->addCloneAction(null, 'fas:plus #69aa46', "Clonar");
        }

    }

    public function onShow($param = null)
    {
        if (TSession::getValue('tipoacao') <> 'Reprovar'  && TSession::getValue('idunit') != 26) {
            $this->fieldList_6881430e7887f->addHeader();
            $this->fieldList_6881430e7887f->addDetail($this->default_item_fieldList_6881430e7887f);

            $this->fieldList_6881430e7887f->addCloneAction(null, 'fas:plus #69aa46', "Clonar");
       }
    } 

    public static function getFormName()
    {
        return self::$formName;
    }

    // Método para exibir o modal com os dados
    public function onShowModal($param) {

        try
        {

            TTransaction::open(self::$database); // open a transaction
            
            $total_produtos = 0;
            $total_servicos = 0;
            $justificativa = '';
            if (isset($param['id'])) {

                $id = $param['id'];


                // Busca os dados do Pedido
                $proposta = new Propostas($id);
                TSession::setValue('idpedido', null);
                TSession::setValue('idpedido', $proposta->pedido_frotas_id);
                TSession::setValue('idproposta', null);
                TSession::setValue('idproposta', $id);
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
                                $justificativa = $justificativa->obs;
                            } else {
                                $justificativa->justificativa = '';
                            }
                        }

                    }
            }

            if (isset($param['pedido_frotas_id']))
            {
                
                $key = $param['pedido_frotas_id'];  // get the parameter $key

                $object = new PedidoFrotas($key); // instantiates the Active Record 

                $histPedido = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $id)
                    ->orderBy('data_operacao', 'desc')
                    ->last();
                if ($histPedido) {
                    $object->justificativa = $histPedido->obs;
                } else {
                    $object->justificativa = '';
                }
                if (TSession::getValue('tipoacao') <> 'Reprovar'  && TSession::getValue('idunit') != 26) {
                    $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

                        //code here

                    }, $this->criteria_fieldList_6881430e7887f); 
                }
                $itens_propostas = ItensPropostas::where('propostas_id', '=', $id)->load();
                $total_produtos = 0;
                $total_servicos = 0;
                $total_produtos_servicos = 0;
                if ($itens_propostas) {
                    foreach ($itens_propostas as $item) {
                        if ($item->tipo == 1) {
                            $total_produtos += $item->valor_total;
                        } elseif ($item->tipo == 2) {
                            $total_servicos += $item->valor_total;
                        }
                        $total_produtos_servicos = $total_produtos + $total_servicos;
                    }
                }
                $this->form->setData($object); // fill the form 

            }
            else
            {
                $this->form->clear();
            }
            
            $objectpro = new stdClass();
            $objectpro->propostas_id = $param['id'];
            $objectpro->total_produtos = round(str_replace(',', '', $total_produtos),2);
            $objectpro->total_servicos = round(str_replace(',', '', $total_servicos),2);
            $objectpro->justificativa = $justificativa;
            $objectpro->total_produtos_servicos = round(str_replace(',', '',$total_produtos_servicos),2);

             TSession::setValue('total_produtos', null);
                TSession::setValue('total_produtos', str_replace(',', '', round(str_replace(',', '', $total_produtos),2)));
             TSession::setValue('total_servicos', null);
                TSession::setValue('total_servicos', str_replace(',', '', round(str_replace(',', '', $total_servicos),2)));
             TSession::setValue('total_produtos_servicos', null);
                TSession::setValue('total_produtos_servicos', str_replace(',', '', round(str_replace(',', '',$total_produtos + $total_servicos),2)));
            TForm::sendData('form_TStatusPedidoFrotasForm', $objectpro);


            TTransaction::close(); // close the transaction 



        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
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
            $itemPedidoFrotas->produto_id = $item->produto_id;
            $itemPedidoFrotas->qtde = $item->qtde;
            $itemPedidoFrotas->valor_unitario = $item->valor;
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
            $itemPedidoFrotas->tbo_horas = $item->tbo_horas;
            $itemPedidoFrotas->tbo_ciclos = $item->tbo_ciclos;
            $itemPedidoFrotas->tsn_horas = $item->tsn_horas;
            $itemPedidoFrotas->tso_horas = $item->tso_horas;
            $itemPedidoFrotas->csn_ciclos = $item->csn_ciclos;
            $itemPedidoFrotas->cso_ciclos = $item->cso_ciclos;
            $itemPedidoFrotas->store();
        }
        // remover itens pedido frotas
        $itensPedido = ItensPedidoFrotas::where('pedido_frotas_id', '=', $pedidoId)->load();
        foreach ($itensPedido as $item) {
            $itenspropostas = ItensPropostas::where('propostas_id', '=', $propostaId)
                                            ->where('produto_id', '=', $item->produto_id)
                                            ->where('tipo', '=', $item->tipo)
                                            ->where('qtde', '=', $item->qtde)
                                            ->load();
            // Verifica se o item não está mais na proposta
            if (empty($itenspropostas)) {
            ///    $item->delete();
            }

        }
    }

   
    public function onSaveReprovar($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $pedidoId = TSession::getValue('idpedido');
            $propostaId = TSession::getValue('idproposta');
            $tipoAcao = TSession::getValue('tipoacao');
            $userId = TSession::getValue('userid');

            $messageAction = null;

            //$this->form->validate(); // validate form data
            
          
            $object = new PedidoFrotas($pedidoId); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            if (empty($data->justificativa)) {
                throw new Exception('Justificativa é obrigatória.');
            }

         // Verifica se já existe uma proposta aprovada para esse pedido
            $propostasDoPedido = Propostas::where('pedido_frotas_id', '=', $pedidoId)->load();

            $pedidoReprovado = true;

            foreach ($propostasDoPedido as $p) {
                // pula a proposta que está sendo reprovada
                if ($p->id != $propostaId && $p->estado_pedido_frotas_id != EstadoPedidoFrotas::REPROVADO) {
                    $pedidoReprovado = false;
                    break; // já encontrou uma ativa (exceto a reprovada)
                }
            }
            
            // Históricos
            $aprovador = AprovadorFrotas::where('system_users_id', '=', $userId)->load();

            if ($pedidoReprovado)
            {
                $object->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                $object->store();

                         // Define estado da proposta conforme ação
                $proposta = new Propostas($propostaId);
                $proposta->obs = $data->justificativa;
                $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                $proposta->store();

               

                $histPedido = new PedidoFrotasHistorico();
                $histPedido->pedido_frotas_id = $object->id;
                $histPedido->aprovador_frotas_id = $aprovador[0]->id;
                $histPedido->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $data->justificativa;
                $histPedido->store();

                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id = $aprovador[0]->id;
                $histProposta->estado_pedido_frotas_id =EstadoPedidoFrotas::REPROVADO;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs =$data->justificativa;
                $histProposta->store();

            } else {

                  // Define estado da proposta conforme ação
                $proposta = new Propostas($propostaId);
                $proposta->obs = $data->justificativa;
                $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                $proposta->store();

                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id = $aprovador[0]->id;
                $histProposta->estado_pedido_frotas_id =EstadoPedidoFrotas::REPROVADO;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs = $data->justificativa;
                $histProposta->store();
 
            }

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = $object->id;

             if (TSession::getValue('tipoacao') <> 'Reprovar') {
                $dotacao_pedido_frotas_pedido_frotas_items = $this->storeItems(
                    'DotacaoPedidoFrotas',
                    'pedido_frotas_id',
                    $object,
                    $this->fieldList_6881430e7887f,
                    function($masterObject, $detailObject) {
                        // Ajusta valor de campos numéricos
                        if (isset($detailObject->valor)) {
                            // Corrige valor numérico se vier sem ponto
                            // Corrige valor numérico se vier sem ponto
                            $detailObject->valor = str_replace(',', '', $detailObject->valor);
                            $detailObject->saldo_atual = str_replace(',', '', $detailObject->saldo_atual);
                            $detailObject->propostas_id = $masterObject->propostas_id;
                        }
                    },
                    $this->criteria_fieldList_6881430e7887f
                );
            }
            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

    //        $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro Reprovado com sucesso!", 'topRight', 'far:check-circle');
            
           TApplication::loadPage('PedidoFrotasList', 'onReload');
          TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);

             TScript::create("Template.closeRightPanel();");
            //TForm::sendData(self::$formName, (object)['id' => $object->id]);

        }
        catch (Exception $e) // in case of exception
        {

            new TMessage('error', $e->getMessage()); // shows the exception error message

            $objectpro = new stdClass();
            $objectpro->id = $pedidoId;
            $objectpro->propostas_id = $propostaId;
            $objectpro->total_produtos = TSession::getValue('total_produtos');
            $objectpro->total_servicos = TSession::getValue('total_servicos');
            $objectpro->justificativa = $object->justificativa;
            $objectpro->total_produtos_servicos = TSession::getValue('total_produtos_servicos');


            // $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

            //    //code here
            //    $detailObject->valor = str_replace(',', '', $detailObject->valor);
            //    $detailObject->saldo_atual = str_replace(',', '', $detailObject->saldo_atual);

            // }, $this->criteria_fieldList_6881430e7887f); 
            TForm::sendData('form_TStatusPedidoFrotasForm', $objectpro);

            TTransaction::rollback(); // undo all pending operations
        }
    }
     public static function onCalcValor($param = null) 
    {
        try 
        {
            //code here
            TTransaction::open(self::$database); // open a transaction
            $id1=$param['_field_id'];
            $conteudojson = $param['_field_data_json'];
            $idproduto = json_decode($conteudojson);
            if (isset($idproduto->{'row'})) {
            $idproduto1 = $idproduto->{'row'}; // 1234
        
            $idsaldo =  (int) str_replace(['.', ','], [',', '.'],($param['dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id'][$idproduto1]));

            $saldoatual = 0;
            $saldodepartamento = new SaldoDepartamento($idsaldo);
            if ($saldodepartamento) {
                $saldoatual = $saldodepartamento->saldo_total;
            } else {
                throw new Exception('Saldo do departamento não encontrado.');
            }

            $subquery = 'SELECT id FROM pedido_frotas WHERE estado_pedido_frotas_id IN (' . 
                        EstadoPedidoFrotas::APROVADO . ',' .
                        EstadoPedidoFrotas::FINALIZADO . ',' .
                        EstadoPedidoFrotas::ENTREGUE . ',' .
                        EstadoPedidoFrotas::PGTOAPROVADO . ')';

            $pedidofrotas = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $idsaldo)
                ->where('pedido_frotas_id', 'IN', "($subquery)")
                ->load();                                               
            if ($pedidofrotas) { 
               // $saldoatual = 0;
                foreach ($pedidofrotas as $pedido) {
                    $saldoatual -= $pedido->valor;
                }
            }

            $saldo_formatado = number_format((float) $saldoatual, 2, '.', '');


            TScript::create("$('#{$id1}').parent().parent().find('[name=\"dotacao_pedido_frotas_pedido_frotas_saldo_atual[]\"]').val({$saldo_formatado});");   

            TTransaction::close();
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    private function usuarioPodeAprovarVB(): bool
    {
        // Exemplo: checar se o usuário logado pertence a um grupo com a permissão
        // Ajuste às suas tabelas (system_user, system_user_group, system_group, etc.)
        $userId = TSession::getValue('userid');

        // Retorne true se o usuário tiver a permissão/grupo de “Gestor Aprovador VB”
        return SystemPermission::check('APROVADORVB', $userId);
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

