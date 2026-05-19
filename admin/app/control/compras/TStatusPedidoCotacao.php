<?php

use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TMinLengthValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Dialog\TMessage;


class TStatusPedidoCotacao extends TPage {
    protected $form;
    private static $database = 'minierp';
    private static $formName = 'form_StatusPedidoCotacaoForm';
     private static $activeRecord = 'ItensCotacao';

    private static function validarBloqueioDaCotacao($cotacaoId)
    {
        $divergencias = ItensCotacao::getDivergenciasBloqueioPorCotacao($cotacaoId);

        if (!empty($divergencias)) {
            throw new Exception("Não é permitido pré-aprovar/aprovar cotação com item acima da tabela.\n" . implode("\n", $divergencias));
        }
    }

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
         $TAlert = new TAlert('danger', "Atenção: valores em vermelho e negrito indicam divergência entre o preço da tabela e o valor informado. Verifique antes de Pre-aprovar/Aprovar.");

        $obs->setMaxLength(255);
  
        $obs->setSize('100%');

         $obs->addValidation("Justificativa", new TRequiredValidator()); 

  //</onBeforeAddFieldsToForm>
        $row1 = $this->form->addFields([$TAlert]);
        $row1->layout = [' col-sm-12'];

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
         // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "ID", 'center' , '70px');
        $column_cotacao_id = new TDataGridColumn('cotacao_id', "ID Cotacao", 'left');
        $column_descricao = new TDataGridColumn('produto->nome', "Descrição", 'left');
        $column_qtde = new TDataGridColumn('qtde', "Qtde", 'left');
        $column_valor_transformed = new TDataGridColumn('valor', "Valor", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_total', "Valor total", 'left');
        $column_valor_liquido_transformed = new TDataGridColumn('', "Valor Liquido", 'left');
        $column_valor_sinapi_transformed = new TDataGridColumn('valor', "Valor Tabela", 'left');
        $column_estado_pedido_id_transformed = new TDataGridColumn('estado_pedido_id', "Estado Cotação", 'left');

      
        $column_valor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_valor_total_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });
        $column_valor_liquido_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $taxa = Entidade::where('id','=',TSession::getValue('entidade'))
                              ->load();
            if ($taxa) {
               $taxacontrato = $taxa[0]->taxacontrato/100;
            }
            else {
                  $taxacontrato = 0;
            }

            $value = $object->valor_total - ($object->valor_total * $taxacontrato);

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_valor_sinapi_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (!TTransaction::getDatabase()) {
                TTransaction::open(self::$database);
                $close = true;
            } else {
                $close = false;
            }

            $produto = new Produto($object->produto_id);
            $mens = '';

            if ($produto &&  $object->valor > $produto->preco_venda ) {
                $preco_formatado = number_format($produto->preco_venda, 2, ',', '.');
                $mens = "<span style='color:red; font-weight: bold;'>R$ {$preco_formatado}</span>";
            } elseif ($produto) {
                $preco_formatado = number_format($produto->preco_venda, 2, ',', '.');
                $mens = "R$ {$preco_formatado}";
            }

            if ($close) {
                TTransaction::close();
            }

            return $mens ?: $value;
        });

        $column_estado_pedido_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
             TTransaction::open(self::$database);
          $estado_pedido = EstadoPedido::find($object->estado_pedido_id);
            TTransaction::close();
           if (!$estado_pedido) {
                return '';
            }
            return "<span class='label label-default' style='width:240px; background-color:{$estado_pedido->cor}'> {$estado_pedido->nome} <span>";
           

        });

     

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        //<onBeforeColumnsCreation>

        //</onBeforeColumnsCreation>

        // // // $this->builder_datagrid_check_all = new TCheckButton('builder_datagrid_check_all');
        // // // $this->builder_datagrid_check_all->setIndexValue('on');
        // // // $this->builder_datagrid_check_all->onclick = "Builder.checkAll(this)";
        // // // $this->builder_datagrid_check_all->style = 'cursor:pointer';
        // // // $this->builder_datagrid_check_all->setProperty('class', 'filled-in');
        // // // $this->builder_datagrid_check_all->id = 'builder_datagrid_check_all';

        // // $label = new TLabel('');
        // // $label->style = 'margin:0';
        // // $label->class = 'checklist-label';
        // // $this->builder_datagrid_check_all->after($label);
        // // $label->for = 'builder_datagrid_check_all';

        // $this->builder_datagrid_check = $this->datagrid->addColumn( new TDataGridColumn('builder_datagrid_check', $this->builder_datagrid_check_all, 'center',  '1%') );

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_cotacao_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_qtde);
        $this->datagrid->addColumn($column_valor_transformed);
        $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column_valor_liquido_transformed);
                $this->datagrid->addColumn($column_valor_sinapi_transformed);

        $this->datagrid->addColumn($column_estado_pedido_id_transformed);

        //<onAfterColumnsCreation>

        //</onAfterColumnsCreation>

        //<onAfterActionsCreation>

        //</onAfterActionsCreation>

       // create the datagrid model
        $this->datagrid->createModel();

        $panel = new TPanelGroup();
       // $panel->setProperty('style', 'width: 80%; margin: auto;'); // <- AQUI aumenta o painel inteiro
        $panel->setProperty('style', 'width: 100%;');

        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        //<onAfterHeaderActionsCreation>

        //</onAfterHeaderActionsCreation>

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
        parent::add($panel);
$style = new TStyle('right-panel > .container-part');
        $style->width = '60% !important';
      //  $style = new TStyle('right-panel > .container-part[page-name=ItensPropostasFormList]');
    //    $style->width = '100% !important';   
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
            $cotacao = new Cotacao($id);
            TSession::setValue('idpedido', null);
            TSession::setValue('idpedido', $cotacao->pedido_id);
            TSession::setValue('idcotacao', null);
            TSession::setValue('idcotacao', $cotacao->id);
            TSession::setValue('tipoacao', null);
            TSession::setValue('tipoacao', $param['tipoacao']);
            // Finaliza a transação
             if (TSession::getValue('tipoacao') == 'Aprovar')
                {
                    $pedido = new Pedido(TSession::getValue('idpedido'));
                    if ($pedido->estado_pedido_venda_id == EstadoPedido::PREAPROVADO) {
                        //PEGAR JUSTIFICATIVA NO PEDIDO HISTORICO
                        $justificativa = PedidoHistorico::where('pedido_venda_id', '=', TSession::getValue('idpedido'))
                            ->where('estado_pedido_venda_id', '=', EstadoPedido::PREAPROVADO)
                            ->orderBy('data_operacao', 'desc')
                            ->first();
                        if ($justificativa) {
                            $cotacao->justificativa = $justificativa->obs;
                        } else {
                            $cotacao->justificativa = '';
                        }
                    }

                }
                 $this->form->setData($cotacao); // fill the form //</blockLine>
                   $this->onReload();
                TTransaction::close();

        } else {
            new TMessage('error', 'Nenhum Pedido selecionado.');
        }
    }

 

    public function onSaveAprovar($param)
    {
        try {
            TTransaction::open(self::$database);

             $this->form->validate(); // validate form data

            $pedidoId = TSession::getValue('idpedido');
            $cotacaoId = TSession::getValue('idcotacao');
            $tipoAcao = TSession::getValue('tipoacao');
            $userId = TSession::getValue('userid');

            $object = new Pedido($pedidoId);
            $data = $this->form->getData();
            $object->fromArray((array) $data);

            if (empty($object->obs)) {
                throw new Exception('Campos vazios não são permitidos.');
            }

            self::validarBloqueioDaCotacao($cotacaoId);

           

            // Verifica se já existe uma cotacao aprovada para esse pedido
            $pedidoAprovado = false;
            $cotacoesDoPedido = Cotacao::where('pedido_id', '=', $pedidoId)->load();

            foreach ($cotacoesDoPedido as $p) {
                if ($p->estado_pedido_id == EstadoPedido::APROVADO) {
                    $pedidoAprovado = true;
                    break;
                }
            }
            if ($pedidoAprovado) {
                throw new Exception('Já existe uma cotacao aprovada para este pedido.');
            }

            // Obter o pedido atual (da cotação que está sendo salva)
            $dotacaoorcamentaria = $object->saldo_departamento_id;

            if (!$dotacaoorcamentaria) {
                throw new Exception('Dotação orçamentária não encontrada no pedido atual.');
            }

            // Buscar o saldo da dotação orçamentária
            $sddotacaoorcamentaria = SaldoDepartamento::where('id', '=', $dotacaoorcamentaria)
                ->load();
            $saldo_disponivel = 0;            
            if (!$sddotacaoorcamentaria) {
                throw new Exception('Dotação orçamentária não encontrada ou não foi cadastrada.');
            }
            if ($sddotacaoorcamentaria) {
                foreach ($sddotacaoorcamentaria as $s) {
                   $saldo_disponivel += (float) $s->saldo_produto;

                }
            }

            // Buscar cotações aprovadas/finalizadas da unidade
            $cotacoes = Cotacao::where('system_unit_id', '=', TSession::getValue('idunit'))
                ->where('estado_pedido_id', 'in', [EstadoPedido::APROVADO, EstadoPedido::FINALIZADO])
                ->load();

            // Somar os valores das cotações com a mesma dotação orçamentária
            $total_valor_liquido = 0;

            if ($cotacoes) {
                foreach ($cotacoes as $cotacao) {
                    $pedidoRelacionado = new Pedido($cotacao->pedido_id);

                    if ($pedidoRelacionado->saldo_departamento_id == $dotacaoorcamentaria) {
                        $total_valor_liquido += (float) $cotacao->valor_liquido;
                    }
                }
            }


            
            // Define estado da cotacao conforme ação
            $cotacao = new Cotacao($cotacaoId);


             // Adiciona o valor da cotação atual
            $total_valor_liquido += (float) $cotacao->valor_liquido;

            // Verifica contra o saldo disponível
            // if ($total_valor_liquido > $saldo_disponivel) {
            //     throw new Exception(
            //         'O valor do orçamento (R$ ' . number_format($total_valor_liquido, 2, ',', '.') .
            //         ') ultrapassa o valor disponível da dotação orçamentária (R$ ' . number_format($saldo_disponivel, 2, ',', '.') . ').'
            //     );
            // }

            $cotacao->obs = $object->obs;
            $cotacao->estado_pedido_id = ($tipoAcao === 'Aprovar') ? EstadoPedido::APROVADO : EstadoPedido::REPROVADO;
            $cotacao->store();

            // Atualiza o pedido
            $pessoa_endereco = PessoaEndereco::where('pessoa_id','=',$cotacao->pessoa_id)
                                                ->where('principal','=','T')
                                                ->first();
            $object->cliente_id = $cotacao->pessoa_id;
            $object->cidade_id = $pessoa_endereco->cidade_id ?? null;
            $object->estado_pedido_venda_id = EstadoPedido::APROVADO;
            $object->valor_total = $cotacao->valor_total;
            $object->valor_total_cotacao = $cotacao->valor_total;
            $object->valor_desconto_cotacao = $cotacao->valor_desconto;
            $object->valor_liquido_cotacao = $cotacao->valor_liquido;
            $object->store(); 

            // Históricos
            $aprovador = Aprovador::where('system_user_id', '=', $userId)->load();

            foreach ($aprovador as $aprovadores) {
                $histPedido = new PedidoHistorico();
                $histPedido->pedido_venda_id = $object->id;
                $histPedido->aprovador_id = $aprovadores->id;
                $histPedido->estado_pedido_venda_id = $cotacao->estado_pedido_id;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $object->obs;
                $histPedido->store();


                $histCotacao = new CotacaoHistorico();
                $histCotacao->cotacao_id = $cotacao->id;
                $histCotacao->aprovador_id =$aprovadores->id;
                $histCotacao->estado_pedido_id = $cotacao->estado_pedido_id;
                $histCotacao->data_historico = date('Y-m-d H:i:s');
                $histCotacao->obs = $object->obs;
                $histCotacao->store();
                break;
            }

            $this->AtualizarItensPedido($object->id, $cotacao->id);
            

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_id"] = $object->id;

      

            TTransaction::close();

            TApplication::loadPage('PedidoVendaList', 'onReload');
            TApplication::loadPage('CotacaoPendenteList', 'onShow', $loadPageParam);
       //     new TMessage('info', 'Aprovação da cotacao realizada com sucesso!');
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

               $this->form->validate(); // validate form data
                        $messageAction = null;


            $pedidoId = TSession::getValue('idpedido');
            $cotacaoId = TSession::getValue('idcotacao');
            $userId = TSession::getValue('userid');

            $object = new Pedido($pedidoId);
            $data = $this->form->getData();
            $object->fromArray((array) $data);

            if (empty($object->obs)) {
                throw new Exception('Campos vazios não são permitidos.');
            }

            self::validarBloqueioDaCotacao($cotacaoId);

          
            // Define estado da cotacao conforme ação
            $cotacao = new Cotacao($cotacaoId);
            $cotacao->obs = $object->obs;
            $cotacao->estado_pedido_id = EstadoPedido::PREAPROVADO;
            $cotacao->store();

            // Atualiza o pedido
            $object->estado_pedido_venda_id = EstadoPedido::PREAPROVADO;
            $object->valor_total_cotacao = $cotacao->valor_total;
            $object->valor_desconto_cotacao = $cotacao->valor_desconto;
            $object->valor_liquido_cotacao = $cotacao->valor_liquido;
            $object->store(); 
 
            // Históricos
            $aprovador = Aprovador::where('system_user_id', '=', $userId)->load();

            foreach ($aprovador as $aprovadores) {
                $histPedido = new PedidoHistorico();
                $histPedido->pedido_venda_id = $object->id;
                $histPedido->aprovador_id = $aprovadores->id;
                $histPedido->estado_pedido_venda_id = $cotacao->estado_pedido_id;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $object->obs;
                $histPedido->store();


                $histCotacao = new CotacaoHistorico();
                $histCotacao->cotacao_id = $cotacao->id;
                $histCotacao->aprovador_id =$aprovadores->id;
                $histCotacao->estado_pedido_id = $cotacao->estado_pedido_id;
                $histCotacao->data_historico = date('Y-m-d H:i:s');
                $histCotacao->obs = $object->obs;
                $histCotacao->store();
                break;
            }
            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_id"] = $object->id;

      

            TTransaction::close();

            TApplication::loadPage('PedidoVendaList', 'onReload');

        //    new TMessage('info','Pré Aprovação da cotacao realizada com sucesso!');
            TApplication::loadPage('CotacaoPendenteList', 'onShow', $loadPageParam);
            TScript::create("Template.closeRightPanel();"); 


        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
    public function AtualizarItensPedido($pedidoId, $cotacaoId)
    {
        // Lógica para atualizar os itens do pedido
        $itens = ItensCotacao::where('cotacao_id', '=', $cotacaoId)->load();
        // Verifica se existem itens para atualizar
        if (empty($itens)) {
            throw new Exception('Nenhum item encontrado para atualizar.');
        }
     
        // Adiciona os novos itens ao pedido
        // Verifica se existem itens para adicionar

        foreach ($itens as $item) {
            if ($item->itens_pedido_id) {
                $itemPedido = new ItensPedido($item->itens_pedido_id);
            } else {
                $itemPedido = new ItensPedido();
            }
            $itemPedido->produto_id = $item->produto_id;
            $itemPedido->quantidade = $item->qtde;
            $itemPedido->valor = $item->valor_unitario;
            $itemPedido->desconto = 0;
            $itemPedido->valor_total = $item->valor_total;
            $itemPedido->pedido_venda_id = $pedidoId;
            $itemPedido->created_at = date('Y-m-d H:i:s');
            $itemPedido->store();
        }
        $itensPedido = ItensPedido::where('pedido_venda_id', '=', $pedidoId)->load();
        foreach ($itensPedido as $item) {
            $itenscotacao = ItensCotacao::where('cotacao_id', '=', $cotacaoId)
                                            ->where('produto_id', '=', $item->produto_id)
                                            ->where('qtde', '=', $item->quantidade)
                                            ->load();
            // Verifica se o item não está mais na cotacao
            if (empty($itenscotacao)) {
                $item->delete();
            }
           
        }
    }
    

    public function onSaveReprovar($param)
    {
        try {
            TTransaction::open(self::$database);
   $this->form->validate(); // validate form data
            $pedidoId = TSession::getValue('idpedido');
            $cotacaoId = TSession::getValue('idcotacao');
            $tipoAcao = TSession::getValue('tipoacao');
            $userId = TSession::getValue('userid');

            $object = new Pedido($pedidoId);
            $data = $this->form->getData();
            $object->fromArray((array) $data);

            if (empty($object->obs)) {
                throw new Exception('Campos vazios não são permitidos.');
            }
   

           

          
            // Verifica se já existe uma cotacao aprovada para esse pedido
            $cotacoesDoPedido = Cotacao::where('pedido_id', '=', $pedidoId)->load();

            $pedidoReprovado = true;

            foreach ($cotacoesDoPedido as $p) {
                if ($p->estado_pedido_id != EstadoPedido::REPROVADO) {
                    $pedidoReprovado = false;
                    break; // não precisa continuar se já encontrou um não reprovado
                }
            }
            
           
            if ($pedidoReprovado)
            {
                $object->estado_pedido_venda_id = EstadoPedido::REPROVADO;
                $object->store();

                // Históricos
                $aprovador = Aprovador::where('system_user_id', '=', $userId)->load();

                $histPedido = new PedidoHistorico();
                $histPedido->pedido_venda_id = $object->id;
                $histPedido->aprovador_id = $aprovador[0]->id;
                $histPedido->estado_pedido_venda_id = EstadoPedido::REPROVADO;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $object->obs;
                $histPedido->store();

                $histCotacao = new CotacaoHistorico();
                $histCotacao->cotacao_id = $cotacao->id;
                $histCotacao->aprovador_id = $aprovador[0]->id;
                $histCotacao->estado_pedido_id =EstadoPedido::REPROVADO;
                $histCotacao->data_historico = date('Y-m-d H:i:s');
                $histCotacao->obs = $object->obs;
                $histCotacao->store();

            } else {

                  // Define estado da cotacao conforme ação
                $cotacao = new Cotacao($cotacaoId);
                $cotacao->obs = $object->obs;
                $cotacao->estado_pedido_id = EstadoPedido::REPROVADO;
                $cotacao->store();

                $histCotacao = new CotacaoHistorico();
                $histCotacao->cotacao_id = $cotacao->id;
                $histCotacao->aprovador_id = $aprovador[0]->id;
                $histCotacao->estado_pedido_id =EstadoPedido::REPROVADO;
                $histCotacao->data_historico = date('Y-m-d H:i:s');
                $histCotacao->obs = $object->obs;
                $histCotacao->store();
 
            }
            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_id"] = $object->id;

      

            TTransaction::close();

            TApplication::loadPage('PedidoVendaList', 'onReload');
            TApplication::loadPage('CotacaoPendenteList', 'onShow', $loadPageParam);
     //       new TMessage('info', 'Reprovação da cotacao realizada com sucesso!');
            TScript::create("Template.closeRightPanel();"); 

           

        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
           
            // open a transaction with database 'minierp'
            TTransaction::open(self::$database);

            // creates a repository for ItensPropostas
            $repository = new TRepository(self::$activeRecord);
            // creates a criteria
            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
            }
            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            //$session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');
            //<onBeforeDatagridLoad>

            //</onBeforeDatagridLoad>
            if (TSession::getValue('idcotacao')) {
                $criteria->add(new TFilter('cotacao_id','=',TSession::getValue('idcotacao')));
            }
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            TSession::setValue('contador_itens', null);
            TSession::setValue('contador_itens', count($objects));
            // if no objects were loaded, show a message

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
               //     $check = new TCheckGroup('builder_datagrid_check');
               //     $check->addItems([$object->id => '']);
                //    $check->getButtons()[$object->id]->onclick = 'event.stopPropagation()';
//
              //      if(!$this->datagrid_form->getField('builder_datagrid_check[]'))
              //      {
              //          $this->datagrid_form->setFields([$check]);
              //      }

              //      $check->setChangeAction(new TAction([$this, 'builderSelectCheck']));
              //      $object->builder_datagrid_check = $check;
//
                    // Recupera o array da sessão ou cria um novo
              //      $session_checks = TSession::getValue(__CLASS__ . 'builder_datagrid_check') ?? [];

                    // Se o checkbox estiver na sessão OU o estado já estiver definido, marca como selecionado
                    // if (!empty($session_checks[$object->id])) {
                    //     $object->builder_datagrid_check->setValue([$object->id => $object->id]);

                    //     // Garante que o item seja adicionado na sessão
                    //     $session_checks[$object->id] = $object->id;
                    //     TSession::setValue(__CLASS__ . 'builder_datagrid_check', $session_checks);
                    // }
                    //<onBeforeDatagridAddItem>

                    //</onBeforeDatagridAddItem>
                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";
                    //<onAfterDatagridAddItem>

                    //</onAfterDatagridAddItem>
                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            //<onBeforeDatagridTransactionClose>

            //</onBeforeDatagridTransactionClose>

            // close the transaction
            TTransaction::close();
            $this->loaded = true;

            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

}


?>
