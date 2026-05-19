<?php

class PedidoFrotasList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private $pedidoTemNotaFiscalMap = [];
    private $estadoPedidoFrotasCache = [];
    private $veiculoPlacaMap = [];
    private $estabelecimentoNomeMap = [];
    private $departamentoNomeMap = [];
    private $tipoManutencaoDescricaoMap = [];
    private $usuarioNomeMap = [];
    private static $estadosDisponiveisCache;
    private static $exibirViewCache;
    private static $pedidoTemPropostaAprovadaMap = [];
    private static $database = 'minierp';
    private static $activeRecord = 'PedidoFrotas';
    private static $primaryKey = 'id';
    private static $formName = 'form_PedidoFrotasList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters', 'onGlobalSearch'];
    private $limit = 10;

    use BuilderDatagridTrait;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }
 
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        $basename   = urlencode('pedido-frotas-list.pdf');
        $download   = "download.php?file=app/manual/pedido-frotas-list.pdf&basename={$basename}";

        $manual = "
            <span style='float:right;'>
                <a href='{$download}'
                target='_blank'
                style='text-decoration:none;margin-left:10px;'>
                    <i class='fa fa-question-circle'> </i>
                </a>
            </span>
        "; 
        // define the form title
        $this->form->setFormTitle("Listagem Pedido Frotas {$manual}");
        $this->limit = 10;

        $criteria_tipo_manutencao_id = new TCriteria();
        $criteria_departamento_unit_id = new TCriteria();
        $criteria_estado_pedido_frotas_id = new TCriteria();
        $criteria_veiculos_id = new TCriteria();
        $criteria_veiculos_id->add(new TFilter('status_veiculo_id', '=', 1));
        $criteria_veiculos_id->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
        
        $criteria_estabelecimento_id = new TCriteria();
        $criteria_cidade_id = new TCriteria();

        $filterVar = TSession::getValue('idunit');;
        $criteria_departamento_unit_id->add(new TFilter('departamento_unit_id', 'in', "(SELECT id FROM departamento_unit WHERE system_unit_id = '{$filterVar}')")); 

        $filterVar = GrupoPessoa::FORNECEDOR;
        $criteria_estabelecimento_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '{$filterVar}')"));
        $criteria_estabelecimento_id->add(new TFilter('deleted_at', 'is', null));
        $login = new LoginForm([]);
        $AlertMensagem = $login->onMensagem('PedidoFrotasList');
        $TAlert = new TAlert('danger',$AlertMensagem);
        $id = new TEntry('id');
        $dt_pedido = new BDateRange('dt_pedido', 'dt_pedido_final');
        $dt_finalizacao = new BDateRange('dt_finalizacao', 'dt_finalizacao_final');
        $descricaopedido = new TEntry('descricaopedido');
        $dtprevisaoentrega = new BDateRange('dtprevisaoentrega', 'dtprevisaoentrega_final');
        $dataretirada = new BDateRange('dataretirada', 'dataretirada_final');
        $tipo_manutencao_id = new TDBCombo('tipo_manutencao_id', 'minierp', 'TipoManutencao', 'id', '{descricao}','descricao asc' , $criteria_tipo_manutencao_id );
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'SystemUserDepartamentoUnit', 'departamento_unit_id', '{departamento_unit->system_unit->name}   - {departamento_unit->name}','departamento_unit_id asc' , $criteria_departamento_unit_id );
        $estado_pedido_frotas_id = new TDBSelect('estado_pedido_frotas_id', 'minierp', 'EstadoPedidoFrotas', 'id', '{nome}','nome asc' , $criteria_estado_pedido_frotas_id );
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa}-{marca->descricao}-{modelo->descricao}','placa asc' , $criteria_veiculos_id );
        $estabelecimento_id = new TDBUniqueSearch('estabelecimento_id', 'minierp', 'Pessoa', 'id', 'nome', 'nome asc', $criteria_estabelecimento_id);
        $pesquisar_propostas_estabelecimento = new TCheckButton('pesquisar_propostas_estabelecimento');
        $cidade_id = new TDBCombo('cidade_id', 'minierp', 'Cidade', 'id', '{nome}','nome asc' , $criteria_cidade_id );


        $descricaopedido->setMaxLength(60);
        $dt_pedido->setMask('dd/mm/yyyy');
        $dataretirada->setMask('dd/mm/yyyy');
        $dt_finalizacao->setMask('dd/mm/yyyy');
        $dtprevisaoentrega->setMask('dd/mm/yyyy');

        $dt_pedido->setDatabaseMask('yyyy-mm-dd');
        $dataretirada->setDatabaseMask('yyyy-mm-dd');
        $dt_finalizacao->setDatabaseMask('yyyy-mm-dd');
        $dtprevisaoentrega->setDatabaseMask('yyyy-mm-dd');

        $cidade_id->enableSearch();
        $veiculos_id->enableSearch();
        $tipo_manutencao_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $estado_pedido_frotas_id->enableSearch();

        $id->setSize(100);
        $dt_pedido->setSize(220);
        $dataretirada->setSize(220);
        $cidade_id->setSize('100%');
        $dt_finalizacao->setSize(220);
        $veiculos_id->setSize('100%');
        $dtprevisaoentrega->setSize(220);
        $descricaopedido->setSize('100%');
        $tipo_manutencao_id->setSize('100%');
        $estabelecimento_id->setSize('100%');
        $pesquisar_propostas_estabelecimento->setUseSwitch(true, 'blue');
        $pesquisar_propostas_estabelecimento->setIndexValue('1');
        $pesquisar_propostas_estabelecimento->setInactiveIndexValue('0');
        $pesquisar_propostas_estabelecimento->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $estado_pedido_frotas_id->setSize('100%', 70);

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Dt Pedido:", null, '14px', null, '100%'),$dt_pedido],[new TLabel("Dt Finalização:", null, '14px', null, '100%'),$dt_finalizacao]);
        $row1->layout = ['col-sm-6',' col-sm-3',' col-sm-3'];

        $row2 = $this->form->addFields([new TLabel("Descrição do Pedido:", null, '14px', null, '100%'),$descricaopedido],[new TLabel("Dt Previsão Entrega:", null, '14px', null, '100%'),$dtprevisaoentrega],[new TLabel("Data Retirada:", null, '14px', null, '100%'),$dataretirada]);
        $row2->layout = ['col-sm-6',' col-sm-3',' col-sm-3'];

        $row3 = $this->form->addFields([new TLabel("Tipo de Manutenção:", null, '14px', null, '100%'),$tipo_manutencao_id],[new TLabel("Unidade/Departamento", null, '14px', null, '100%'),$departamento_unit_id]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Estado de pedido:", null, '14px', null, '100%'),$estado_pedido_frotas_id],[new TLabel("Veiculos, Aeronaves e/ou Equipamentos:", null, '14px', null, '100%'),$veiculos_id]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields(
            [new TLabel("Estabelecimento:", null, '14px', null, '100%'), $estabelecimento_id],
            [new TLabel("Pesquisar nas propostas?", null, '14px', null, '100%'), $pesquisar_propostas_estabelecimento]
        );
        $row5->layout = ['col-sm-6','col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

      
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
         $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%'; 
        $this->datagrid->setHeight('100%'); 

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_descricaopedido = new TDataGridColumn('descricaopedido', "Descrição Pedido", 'center', '300px');
        $column_veiculos_id_transformed = new TDataGridColumn('veiculos_id', "Placa", 'center', '300px');
        $column_estabelecimento_nome = new TDataGridColumn('estabelecimento_id', "Estabelecimento", 'center', '300px');
        $column_departamento_unit_name = new TDataGridColumn('departamento_unit_id', "Departamento", 'center', '300px');
        $column_dt_pedido_transformed = new TDataGridColumn('dt_pedido', "Dt Pedido", 'center', '300px');
        $column_dt_finalizacao_transformed = new TDataGridColumn('dt_finalizacao', "Dt Finalização", 'center', '300px');
        $column_tipo_manutencao_id = new TDataGridColumn('tipo_manutencao_id', "Tipo Manutenção", 'center', '300px');
        $column_valor_liquido_proposta = new TDataGridColumn('valor_liquido_proposta', "Vl líquido proposta", 'center', '300px');
        $column_estado_pedido_frotas_id_transformed = new TDataGridColumn('estado_pedido_frotas_id', "Estado Pedido", 'center', '300px');
        $column_system_users_name = new TDataGridColumn('system_users_id', "Usuário", 'center', '300px');
        $column_orcamento_base = new TDataGridColumn('orcamento_base', "Orçamento base", 'center', '300px');
    //    $column_cidade_id_transformed = new TDataGridColumn('cidade_id', "Cidade", 'center', '300px');
        $column_id->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }
            if (!empty($object->idold))
            {
                return $value . "<span style='font-size: 12px; font-style: italic; color: #5c5b5b'> ({$object->idold})</span>";
            } else {
                return $value;
            }

        });
          $column_valor_liquido_proposta->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_dt_pedido_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y h:i:s');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });
 
        $column_dt_finalizacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });
        $column_veiculos_id_transformed->setTransformer(function($value)
        {
            return $this->veiculoPlacaMap[(int) $value] ?? '';
        });
        $column_estabelecimento_nome->setTransformer(function($value)
        {
            return $this->estabelecimentoNomeMap[(int) $value] ?? '';
        });
        $column_departamento_unit_name->setTransformer(function($value)
        {
            return $this->departamentoNomeMap[(int) $value] ?? '';
        });
        $column_tipo_manutencao_id->setTransformer(function($value)
        {
            return $this->tipoManutencaoDescricaoMap[(int) $value] ?? '';
        });
        $column_system_users_name->setTransformer(function($value)
        {
            return $this->usuarioNomeMap[(int) $value] ?? '';
        });
        $column_orcamento_base->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if ($value == 1) {
                return "<span class='label label-default' style='background-color: #28a745; color: white; width: 80px; display: inline-block; text-align: center;'>SIM</span>";
            } else {
                return "<span class='label label-default' style='background-color: #dc3545; color: white; width: 80px; display: inline-block; text-align: center;'>NÃO</span>";
            }
        });

        $column_estado_pedido_frotas_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
              //code here
            $temnotafiscal = !empty($this->pedidoTemNotaFiscalMap[$object->id]);

            // carregado em lote no onReload para evitar consultas por linha no datagrid
            $revisao = '';
            if (TSession::getValue('testar_revisao')==1) {            
                //entrou em revisão
                $revisao = '';
                if ($object->estado_pedido_frotas1_id !== null) {
                    if (!isset($this->estadoPedidoFrotasCache[$object->estado_pedido_frotas1_id])) {
                        $estadorevisao = new EstadoPedidoFrotas($object->estado_pedido_frotas1_id);
                        $this->estadoPedidoFrotasCache[$object->estado_pedido_frotas1_id] = $estadorevisao->nome ?? '';
                    }
                    $nomeRevisao = $this->estadoPedidoFrotasCache[$object->estado_pedido_frotas1_id];
                    if (!empty($nomeRevisao)) {
                        $revisao = "<span style='font-size: 10px; font-style: italic; color: #eee;'>({$nomeRevisao})</span>";
                    }
                }
            }
            $estadoAtual = $this->estadoPedidoFrotasCache[(int) $object->estado_pedido_frotas_id] ?? ['nome' => '', 'cor' => '#777'];
            $estadoNome = $estadoAtual['nome'] ?? '';
            $estadoCor  = $estadoAtual['cor'] ?? '#777';
            if ($temnotafiscal) {
                $anexo = $estadoNome . " <i class='fa fa-paperclip' aria-hidden='true'></i>";
            } else {
                $anexo = $estadoNome;
            }

            return "<span class='label label-default' style='width:260px; background-color:{$estadoCor}; display:inline-block;'> {$anexo} {$revisao} </span>";

        });

      /*  $column_cidade_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
                    TTransaction::open('minierp');

                $cidade = new Cidade($object->cidade_id);
                if ($cidade) {
                    $estado = new Estado($cidade->estado_id);
                    return "{$cidade->nome} - {$estado->sigla}";

                } else {
                    return "Não informado!!!";

                }

                TTransaction::close();

        });     */   

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        $order_descricaopedido = new TAction(array($this, 'onReload'));
        $order_descricaopedido->setParameter('order', 'descricaopedido');
        $column_descricaopedido->setAction($order_descricaopedido);
        $order_cidade_id_transformed = new TAction(array($this, 'onReload'));
        $order_cidade_id_transformed->setParameter('order', 'cidade_id');
     //   $column_cidade_id_transformed->setAction($order_cidade_id_transformed);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricaopedido);
        $this->datagrid->addColumn($column_veiculos_id_transformed);
        $this->datagrid->addColumn($column_estabelecimento_nome);
        $this->datagrid->addColumn($column_departamento_unit_name);
        $this->datagrid->addColumn($column_dt_pedido_transformed);
        $this->datagrid->addColumn($column_dt_finalizacao_transformed);
        $this->datagrid->addColumn($column_tipo_manutencao_id);
        $this->datagrid->addColumn($column_valor_liquido_proposta);
        $this->datagrid->addColumn($column_estado_pedido_frotas_id_transformed);        
        $this->datagrid->addColumn($column_system_users_name);
        if (TSession::getValue('pedido_base')==1) {
           $this->datagrid->addColumn($column_orcamento_base);
        }

        // creates two datagrid actions
        $action1 = new TDataGridAction(['PedidoFrotasFormView', 'onShow'],     ['id' => '{id}']);
        $action2 = new TDataGridAction(['PedidoFrotasForm', 'onEdit'], [
            'id'       => '{id}',
            'editando' => '1', // valor fixo
            'status'   => '{estado_pedido_frotas_id}' // variável dinâmica
        ]);
        
        $action3 = new TDataGridAction([$this, 'onDelete'],   ['id' => '{id}']);
        $action5 = new TDataGridAction([$this, 'onEnviarCotacao'],   ['id' => '{id}']);
        $action8 = new TDataGridAction(['TStatusPedidoCancelar', 'onEdit'],   ['id' => '{id}']);
        $action9 = new TDataGridAction(['PedidoFrotasList', 'onAprovarPagamento'],   ['id' => '{id}']);
        $action10 = new TDataGridAction([$this, 'onFinalizarPedidoEGerarFinanceiro'],   ['id' => '{id}']);
        $action11 = new TDataGridAction(['DocumentosPropostasSimpleList', 'onSetProject'],   ['id' => '{id}']);
        $action12 = new TDataGridAction([$this, 'onCancelarAprovacao'],   ['id' => '{id}']);

        $action14 = new TDataGridAction(['AutorizarPedidoList', 'onSetProject'],   ['id' => '{id}']);
        $action15 = new TDataGridAction(['RetiradaVeiculo', 'onShow'],   ['id' => '{id}']);
        $action16 = new TDataGridAction([$this, 'onExibirDetalhe'],   ['id' => '{id}']);
        $action17 = new TDataGridAction(['RegularizarDotacaoPedidoFrotasForm', 'onEdit'],   ['id' => '{id}']);
 
        $action1->setLabel('Visualizar pedido');
        $action1->setImage('fas:search-plus #673AB7');
       $action1->setDisplayCondition('PedidoFrotasList::onExibirView');

        $action2->setLabel('Editar');
        $action2->setImage('far:edit #478fca');
       $action2->setDisplayCondition('PedidoFrotasList::onExibirEditar');

        $action3->setLabel('Excluir');
        $action3->setImage('fas:trash-alt #dd5a43');
        $action3->setDisplayCondition('PedidoFrotasList::onExibirExcluir');

    //    $action4->setLabel('Documento Pedido');
     //   $action4->setImage('far:file-pdf #000000');

    //    $action13->setLabel('Orçamento');
   //     $action13->setImage('fas:file-pdf #F44336');
   //    $action13->setDisplayCondition('PedidoFrotasList::onExibirDocCotacao');

         $action16->setLabel('Detalhes Propostas');
        $action16->setImage('fas:plus #69aa46');
//       $action16->setDisplayCondition('PedidoFrotasList::onExibirDocCotacao');

        $action5->setLabel('Gerar Proposta');
        $action5->setImage('fas:envelope #E91E63');
       $action5->setDisplayCondition('PedidoFrotasList::onExibirEnvio');

     //   $action61->setLabel('Pré-Aprovar');
     //   $action61->setImage('far:thumbs-up #9C27B0');
     //  $action61->setDisplayCondition('PedidoFrotasList::onExibirPreAprovar');

    //   $action6->setLabel('Aprovar');
    //    $action6->setImage('fas:thumbs-up #9C27B0');
    //   $action6->setDisplayCondition('PedidoFrotasList::onExibirAprovar');

   //     $action7->setLabel('Reprovar');
   //     $action7->setImage('fas:thumbs-down #F44336');
  //     $action7->setDisplayCondition('PedidoFrotasList::onExibirReprovar');

        $action8->setLabel('Cancelar pedido');
        $action8->setImage('fas:times-circle #E91E63');
       $action8->setDisplayCondition('PedidoFrotasList::onExibirCancelado');

        $action9->setLabel('Aprovar pagamento');
        $action9->setImage('fas:money-bill-wave #FFA500');
       $action9->setDisplayCondition('PedidoFrotasList::onExibirAprovarPagamento');

        $action10->setLabel('Finalizar Pedido');
        $action10->setImage('fas:door-closed #009688');
        $action10->setDisplayCondition('PedidoFrotasList::onExibirFinalizarPedidoEGerarFinanceiro');

        $action11->setLabel('Anexos');
        $action11->setImage('fas:paperclip #795548');
       $action11->setDisplayCondition('PedidoFrotasList::onExibirAnexos');



        $action12->setLabel('Cancelar Aprovação');
        $action12->setImage('fas:undo #009688');
       $action12->setDisplayCondition('PedidoFrotasList::onExibirCancelarAprovacao');

       $action14->setLabel('Autorizar Pedido');
       $action14->setImage('fas:unlock #ffc83d');
       $action14->setDisplayCondition('PedidoFrotasList::onExibirAutorizacao');

 //       $action15->setLabel('Saída Veículo');
 //       $action15->setImage('fas:arrow-left #F44336');
 //      $action15->setDisplayCondition('PedidoFrotasList::onExibirRetiradaVeiculo');
        $action17->setLabel('Dotação/empenho');
        $action17->setImage('fas:receipt #795548');
        $action17->setDisplayCondition('PedidoFrotasList::onExibirRegularizarDotacao');

        $action_group = new TDataGridActionGroup('Clique Ações ', 'fa:th red');

        $action_group->addAction($action16);

        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action3);
       // $action_group->addAction($action4);
    //    $action_group->addAction($action13);
        $action_group->addAction($action5);
    //    $action_group->addAction($action61);
   //     $action_group->addAction($action6);
   //     $action_group->addAction($action7);
    //    $action_group->addAction($action14);
   //     $action_group->addAction($action15);
        $action_group->addAction($action8);
        $action_group->addAction($action9);
        $action_group->addAction($action10);
        $action_group->addAction($action11);
        $action_group->addAction($action12);
        $action_group->addAction($action14);
        $action_group->addAction($action17);



        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);

        $this->applyDatagridProperties();

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem Pedido Frotas {$manual}");
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';
        $this->datagrid_form->class = '';
     //   $this->datagrid_form->class = ' table-fixed-header ';
       // $this->datagrid_form->style = ' height:550px;';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $panel->getBody()->insert(0, $headerActions);

        $button_cadastrar = new TButton('button_button_cadastrar');
        $button_cadastrar->setAction(new TAction(['PedidoFrotasForm', 'onShow']), "Solicitar Pedido De Frota");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);
       

        

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['PedidoFrotasList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['PedidoFrotasList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['PedidoFrotasList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

       $button_cilia = new TButton('button_button_cilia');
$button_cilia->setAction(new TAction([$this, 'onCilia']), '<img src="app/images/logocilia.png" width="32" height="32" style="margin-right:5px; vertical-align:middle;"> Cilia');
$button_cilia->addStyleClass('btn-default');



        $this->datagrid_form->addField($button_cilia);

        


        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['PedidoFrotasList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['PedidoFrotasList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF RELATÓRIO", new TAction(['PedidoFrotasList', 'onExportPdfRel'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['PedidoFrotasList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );
        $dropdown_button_exportar->addPostAction( "PDF/HTML", new TAction(['PedidoFrotasList', 'onExportHtml'],['static' => 1]), 'datagrid_'.self::$formName, 'fab:html5 #E34F26'  );

             $dropdown_button_importar = new TDropDown("Importar", 'fas:file-upload #000000');
        $dropdown_button_importar->setPullSide('right');
        $dropdown_button_importar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_importar->addPostAction( "XLS", new TAction(['PedidoFrotasList', 'onImportarXLS'],['static' => 1]), self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_importar->addPostAction( "MYSQL", new TAction(['PedidoFrotasList', 'onImportarMYSQL'],['static' => 1]), self::$formName, 'fas:database  #614caf' );


        $head_left_actions->add($button_cadastrar);
       
        
        if (self::onExibirImportar())
        {
            $head_right_actions->add($dropdown_button_importar);
        }
        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($button_atualizar);
        $head_left_actions->add($button_cilia);


        $head_right_actions->add($dropdown_button_exportar);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            //$container->add(TBreadCrumb::create(["Manutenção Frotas","Listagem de Pedido de Frotas"]));
            if (!empty($AlertMensagem)) {
                $container->add($TAlert);
           }          }
        $container->add($panel);

        parent::add($container);

        // if (empty($param['target_container']) && !TSession::getValue(__CLASS__ . '_auditoria_popup_exibido') && AuditoriaPedidoFrotasPopup::shouldShow())
        // {
        //     TSession::setValue(__CLASS__ . '_auditoria_popup_exibido', true);
        //     TScript::create("setTimeout(function(){ __adianti_load_page('engine.php?class=AuditoriaPedidoFrotasPopup'); }, 300);");
        // }

    }

    public function onDelete($param = null) 
    { 
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);

                // instantiates object
                $object = new PedidoFrotas($key, FALSE); 

                if ($object->estado_pedido_frotas_id == EstadoPedidoFrotas::PENDENTE) {
                    //excluir os itens_pedido_frotas
                    $itens = ItensPedidoFrotas::where('pedido_frotas_id', '=', $key)->load();
                    if ($itens) {
                        foreach ($itens as $item) {
                            $item->delete();
                        }
                    }
                    //excluir os documentos_pedidos
                    $documentos = DocumentosPedidoFrotas::where('pedido_frotas_id', '=', $key)->load();
                    if ($documentos) {
                        foreach ($documentos as $documento) {
                            $documento->delete();
                        }
                    }
                    //excluir os pedidos_as_clientes
                    $pedidos_as_clientes = PedidoAsCliente::where('pedido_frotas_id', '=', $key)->load();
                    if ($pedidos_as_clientes) {
                        foreach ($pedidos_as_clientes as $pedido_as_cliente) {
                            $pedido_as_cliente->delete();
                        }
                    }
 
                    // deletes the object from the database
                    $object->delete();
                } else {
                    throw new Exception("Não é possível excluir o pedido de frota, pois ele está em outro estado que não permite exclusão.");
                }


                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
    }
    public function onExportCsv($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    $handler = fopen($output, 'w');
                    TTransaction::open(self::$database);

                    foreach ($objects as $object)
                    {
                        $row = [];
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();

                            if (isset($object->$column_name))
                            {
                                $row[] = is_scalar($object->$column_name) ? $object->$column_name : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $row[] = $object->render($column_name);
                            }
                        }

                        fputcsv($handler, $row);
                    }

                    fclose($handler);
                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXls($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xls';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $widths = [];
                $titles = [];

                foreach ($this->datagrid->getColumns() as $column)
                {
                    $titles[] = $column->getLabel();
                    $width    = 100;

                    if (is_null($column->getWidth()))
                    {
                        $width = 100;
                    }
                    else if (strpos((string)$column->getWidth(), '%') !== false)
                    {
                        $width = ((int) $column->getWidth()) * 5;
                    }
                    else if (is_numeric($column->getWidth()))
                    {
                        $width = $column->getWidth();
                    }

                    $widths[] = $width;
                }

                $table = new \TTableWriterXLS($widths);
                $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                $table->addStyle('data',   'Helvetica', '10', '',  '#000000', '#FFFFFF', 'LR');

                $table->addRow();

                foreach ($titles as $title)
                {
                    $table->addCell($title, 'center', 'title');
                }

                $this->limit = 0;
                $objects = $this->onReload();

                TTransaction::open(self::$database);
                if ($objects)
                {
                    foreach ($objects as $object)
                    {
                        $table->addRow();
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $value = '';
                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                            }

                            $transformer = $column->getTransformer();
                            if ($transformer)
                            {
                                $value = strip_tags((string)call_user_func($transformer, $value, $object, null));
                            }

                            $table->addCell($value, 'center', 'data');
                        }
                    }
                }
                $table->save($output);
                TTransaction::close();

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    // public function onExportPdf($param = null) 
    // {
    //     try
    //     {
    //         $output = 'app/output/'.uniqid().'.pdf';

    //         if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
    //         {
    //             $this->limit = 0;
    //             $this->datagrid->prepareForPrinting();
    //             $this->onReload();

    //             $html = clone $this->datagrid;
    //             $contents = file_get_contents('app/resources/styles-print.html') . file_get_contents('app/resources/styles-print-list.html') . PdfListHeader::render(__CLASS__) . $html->getContents();

    //             $dompdf = new \Dompdf\Dompdf;
    //             $dompdf->loadHtml($contents);
    //             $dompdf->setPaper('A4', 'landscape');
    //             $dompdf->render();

    //             file_put_contents($output, $dompdf->output());

    //             $window = TWindow::create('PDF', 0.8, 0.8);
    //             $object = new TElement('iframe');
    //             $object->src  = $output;
    //             $object->type  = 'application/pdf';
    //             $object->style = "width: 100%; height:calc(100% - 10px)";

    //             $window->add($object);
    //             $window->show();
    //         }
    //         else
    //         {
    //             throw new Exception(_t('Permission denied') . ': ' . $output);
    //         }
    //     }
    //     catch (Exception $e) // in case of exception
    //     {
    //         new TMessage('error', $e->getMessage()); // shows the exception error message
    //     }
    // }
    public function onExportPdfRel($param = null) 
    {
        try
        {
       /*     $output = 'app/output/'.uniqid().'.pdf';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->onReload();

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . file_get_contents('app/resources/styles-print-list.html') . PdfListHeader::render(__CLASS__) . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = $output;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($object);
                $window->show();*/

           // open a transaction with database 'conexao'
            TTransaction::open('minierp');

            $conn = TConnection::open('minierp');

            // get the form data into an active record
            $data       = $this->form->getData();

            //code here
            $pdf = new FPDF("L","pt","A4");

            
            $repository = new TRepository('ViewPedidoFrotasPropostas'); // creates a repository
            $limit = 999999999999;

            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('order', 'dt_pedido');
            $criteria->setProperty('limit', $limit);  

            $filters = TSession::getValue(__CLASS__.'_filters') ?: [];
            $this->addViewPedidoFrotasPropostasFilters($criteria, $filters);
            // $pes1 = Pessoa::where('system_user_id','=',TSession::getValue('userid'))
            // ->load();
            // if ($pes1) {
            // foreach ($pes1 as $pessoass) {

            // }
            // $criteria->add(new TFilter('pessoa_id', '=', $pessoass->id), TExpression::AND_OPERATOR);
            // }
            // load the objects according to criteria
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')), TExpression::AND_OPERATOR);

            $objects = $repository->load($criteria);

            $linha          = 0;
            $pag            = 1; 
            $alturalinha    = 50;
            $qtd            = 0;
            $vltotal        = 0;
            $vltotaldesconto = 0;
            $vltotalcotacao = 0;

            // $user = SystemUserUnit::where('system_user_id','=',TSession::getValue('userid'))
            //                       ->load();
            // if ($user) {
            //     foreach ($user as $users) {
            //       $unit = SystemUnit::where('id','=',$users->system_unit_id)
            //                         ->load();
            //       if ($unit) {
            //           foreach ($unit as $units)
            //           {
            //              $cnpj = $units->cnpj;
            //              $unidade = $units->name;
            //           }
            //       }
            //     }
            // } else {
            //       $cnpj = '';
            //       $unidade = '';
            // }
            $units = new SystemUnit(TSession::getValue('idunit'));
            if ($units) {
                $cnpj = $units->cnpj;
                $unidade = $units->name;
             } else {
                $cnpj = '';
                $unidade = '';
             }

            if ($objects) {
                foreach ($objects as $object) {

                    $listar = 1;
                    // $suserdep = SystemUserDepartamentoUnit::where('system_users_id','=',TSession::getValue('userid'))
                    //                                       ->load();

                    // //$objects->system_user_id verificar se ele pertence a unidade que logou e addItem
                    // if ($suserdep)
                    // {                    
                    //     foreach($suserdep as $suserdeps){
                    //         if ($suserdeps->departamento_unit_id==$object->departamento_unit_id) {
                    //             $listar=1;
                    //         }
                    //     }
                    // }
               if ($listar==1) {       
               if ( ($linha==0) || ($linha >= 46) ){
                  $this->cabecalho($pdf, $linha,$pag,$unidade,$cnpj,$filters);
	              $linha = 0;
	              $pag=$pag + 1; 
	              $alturalinha = 62;
               }
            //email
               $pdf->SetFillColor(($qtd % 2) ? 232 : 255, ($qtd % 2) ? 235 : 255, ($qtd % 2) ? 240 : 255);
               $pdf->Rect(24, $alturalinha - 2, 792, 10, 'F');
               $pdf->setFont('arial','',6);

               $pdf->SetXY(27,$alturalinha);
               $pdf->Cell(70,5,$object->id,0,1,'L');

               $data = TDate::date2br($object->dt_pedido);

               $pdf->SetXY(47,$alturalinha);
               $pdf->Cell(70,5,$data,0,1,'L');

               $estabelecimentoNome = '';
               if (!empty($object->estabelecimento_id)) {
                   $estabelecimento = new Pessoa($object->estabelecimento_id);
                   $estabelecimentoNome = $estabelecimento->nome ?? '';
               }
               $pdf->SetXY(85,$alturalinha);
               //$pdf->Cell(70,5,$pessoa->nome,0,1,'L');
               $pdf->Cell(70,5,mb_convert_encoding(substr((string) $estabelecimentoNome,0,32), 'ISO-8859-1', 'UTF-8'),0,1,'L');

               $pdf->SetXY(225,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding(substr((string) ($object->descricaopedido ?? ''),0,60), 'ISO-8859-1', 'UTF-8'),0,1,'L');

               $estadopedido = new EstadoPedidoFrotas($object->estado_pedido_frotas_id);
               $pdf->SetXY(442,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding(substr((string) ($estadopedido->nome ?? ''),0,12), 'ISO-8859-1', 'UTF-8'),0,1,'L');

               $pdf->SetXY(455,$alturalinha);
               $pdf->Cell(70,5,number_format((float) ($object->valor_total ?? 0), 2),0,1,'R');

               $pdf->SetXY(500,$alturalinha);
               $pdf->Cell(70,5,number_format((float) ($object->valor_desconto_proposta ?? 0), 2),0,1,'R');

               $pdf->SetXY(545,$alturalinha);
               $pdf->Cell(70,5,number_format((float) ($object->valor_liquido_proposta ?? 0), 2),0,1,'R');
               $pdf->setFont('arial','',5);
               $dep = new DepartamentoUnit($object->departamento_unit_id);      
               $pdf->SetXY(620,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding(substr(strtoupper($dep->name),0,36), 'ISO-8859-1', 'UTF-8'),0,1,'L');
               $pdf->setFont('arial','',6);
               $cidadeestado='';
               if ($object->cidade_id<>NULL){
                  $cid = new Cidade($object->cidade_id);
                  $est = Estado::where('id','=',$cid->estado_id)
                                ->load();
                  if ($est){
                      foreach($est as $estado)
                      $cidadeestado=rtrim($cid->nome).' - '.$estado->sigla;
                  }

               } 

               $pdf->SetXY(730,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding($cidadeestado, 'ISO-8859-1', 'UTF-8'),0,1,'L');

               $pdf->ln(1);
               $alturalinha=$alturalinha + 10;
               $linha = $linha + 1;
               //somatoria
               $qtd = $qtd + 1;
               $vltotal += (float) ($object->valor_total ?? 0);
               $vltotaldesconto += (float) ($object->valor_desconto_proposta ?? 0);
               $vltotalcotacao += (float) ($object->valor_liquido_proposta ?? 0);

               } 
             } 

             $alturalinha=$alturalinha + 10; 

             $pdf->ln(1); 
             $pdf->SetFillColor(232, 236, 241);
             $pdf->Rect(24, $alturalinha - 2, 792, 12, 'F');
             $pdf->Cell(0,4,"","B",1,'C');

             $pdf->SetXY(27,$alturalinha);
             $pdf->Cell(70,5,'Total Geral : '.$qtd,0,1,'L');

             $pdf->SetXY(455,$alturalinha);
             $pdf->Cell(70,5,number_format($vltotal, 2),0,1,'R');

             $pdf->SetXY(500,$alturalinha);
             $pdf->Cell(70,5,number_format($vltotaldesconto, 2),0,1,'R');

             $pdf->SetXY(545,$alturalinha);
             $pdf->Cell(70,5,number_format($vltotalcotacao, 2),0,1,'R');

             $nome = 'Pedidos.pdf';

            // stores the file
            if (!file_exists("app/output/{$nome}") OR is_writable("app/output/{$nome}"))
            {
               $pdf->Output("app/output/{$nome}","F");
            }
            else
            {
               throw new Exception(_t('Permission denied') . ': ' . "app/output/{$nome}");
            }

            // open the report file
            parent::openFile("app/output/{$nome}");
            // shows the success message
            new TMessage('info', 'Pedidos gerado com sucesso. Por favor, habilite popups no navegador.');
            }            
            // fill the form with the active record data
            $this->form->setData($data);
             TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    
   private function addViewPedidoFrotasPropostasFilters(TCriteria $criteria, array $filters)
    {
        $validFilters = [];

        foreach ($filters as $filter)
        {
            if (!($filter instanceof TFilter))
            {
                continue;
            }

            $variable = $this->getFilterProperty($filter, 'variable');

            if ($variable === 'veiculos_id')
            {
                $criteria->add($this->buildVeiculoFilterForView($filter));
            }
            else
            {
                $criteria->add($filter);
            }

            $validFilters[] = $filter;
        }

        if (count($validFilters) !== count($filters))
        {
            TSession::setValue(__CLASS__.'_filters', $validFilters);
        }
    }

   private function buildVeiculoFilterForView(TFilter $filter)
    {
        $operator = strtolower(trim((string) $this->getFilterProperty($filter, 'operator')));
        $value    = $this->getFilterProperty($filter, 'value');

        if ($operator === 'in' || is_array($value))
        {
            $ids = array_values(array_filter(array_map('intval', (array) $value)));
            $subquery = empty($ids)
                ? '(SELECT id FROM pedido_frotas WHERE 1 = 0)'
                : '(SELECT id FROM pedido_frotas WHERE veiculos_id IN (' . implode(',', $ids) . '))';
        }
        else
        {
            $subquery = '(SELECT id FROM pedido_frotas WHERE veiculos_id = ' . (int) $value . ')';
        }

        return new TFilter('id', 'in', $subquery);
    }

   private function getFilterProperty(TFilter $filter, $property)
    {
        $reflection = new ReflectionClass($filter);
        $propertyReflection = $reflection->getProperty($property);
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($filter);
    }

   private function cabecalho($pdf, $linha,$pag, $unidade, $cnpj, $filters)
    {
        $label = '';

        if(!empty(TSession::getValue('data_inicial'))){     
            $datai = new DateTime(TSession::getValue('data_inicial'));
            $datai = $datai->format('d/m/Y');

            $dataf = new DateTime(TSession::getValue('data_final'));
            $dataf = $dataf->format('d/m/Y');

            $label = 'Periodo: de '. $datai . ' ate '. $dataf;
        }

        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);
        $logo = 'app/images/logo.png';
        if (file_exists($logo)) {
            $pdf->Image($logo, 30, 7, 20);
        }

        $pdf->SetFont('arial','B',8);
        $pdf->SetXY(58,8);
        $pdf->Cell(70,5, mb_convert_encoding($unidade, 'ISO-8859-1', 'UTF-8'));
        $pdf->SetXY(300,8);
        $pdf->Cell(240,5,mb_convert_encoding('Relatorio de pedidos de frotas', 'ISO-8859-1', 'UTF-8'),0,1,'C');
        $pdf->SetXY(654,8);
        $pdf->Cell(76,5,'Hora: '.date("H:i:s"),0,1,'L');
        $pdf->SetXY(734,8);
        $pdf->Cell(82,5,'Data: '.date("d/m/Y"),0,1,'L');
        $pdf->Ln(4);

        $pdf->SetXY(58,20);
        $pdf->Cell(70,5,$cnpj.'      '. $label,0,1,'L');
        $pdf->SetXY(115,20);
        $pdf->Cell(70,5,'',0,1,'L');
        $pdf->SetXY(734,20);
        $pdf->Cell(82,5,utf8_decode('Pagina: ').$pag,0,1,'L');
        $pdf->Ln(1);

        //nome
        $pdf->SetDrawColor(140, 150, 165);
        $pdf->Cell(0,5,"","B",1,'C');
        $pdf->SetFillColor(70, 78, 92);
        $pdf->Rect(24, 34, 792, 14, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('arial','B',6);
        $pdf->SetXY(27,38);
        $pdf->Cell(70,5,'ID',0,1,'L');

        $pdf->SetXY(47,38);
        $pdf->Cell(70,5,'Data',0,1,'L');

        $pdf->SetXY(85,38);
        $pdf->Cell(70,5,'Nome',0,1,'L');

        $pdf->SetXY(225,38);
        $pdf->Cell(100,5,utf8_decode('Descricao do pedido'),0,1,'L');

        $pdf->SetXY(442,38);
        $pdf->Cell(70,5,'Status',0,1,'L');

        $pdf->SetXY(455,38);
        $pdf->Cell(70,5,'Vlr Bruto',0,1,'R');

        $pdf->SetXY(500,38);
        $pdf->Cell(70,5,'Desconto',0,1,'R');

        $pdf->SetXY(515,38);
        $pdf->Cell(100,5,'Vlr Liquido',0,1,'R');

         $pdf->SetXY(579,38);
         $pdf->Cell(100,5,'Departamento',0,1,'R');

         $pdf->SetXY(662,38);
         $pdf->Cell(100,5,'Cidade',0,1,'R');
         $pdf->SetTextColor(0, 0, 0);
         //                123456789012 

        $pdf->ln(1);

        $pdf->Cell(0,4,"","B",1,'C');
        $linha = 12;
     }
    public function onExportXml($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xml';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    TTransaction::open(self::$database);

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->{'formatOutput'} = true;
                    $dataset = $dom->appendChild( $dom->createElement('dataset') );

                    foreach ($objects as $object)
                    {
                        $row = $dataset->appendChild( $dom->createElement( self::$activeRecord ) );

                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $column_name_raw = str_replace(['(','{','->', '-','>','}',')', ' '], ['','','_','','','','','_'], $column_name);

                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                                $row->appendChild($dom->createElement($column_name_raw, $value)); 
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                                $row->appendChild($dom->createElement($column_name_raw, $value));
                            }
                        }
                    }

                    $dom->save($output);

                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->dt_pedido_final) AND ( (is_scalar($data->dt_pedido_final) AND $data->dt_pedido_final !== '') OR (is_array($data->dt_pedido_final) AND (!empty($data->dt_pedido_final)) )) )
        {

            $filters[] = new TFilter('dt_pedido', '<=', $data->dt_pedido_final);// create the filter 
        }

        if (isset($data->dt_pedido) AND ( (is_scalar($data->dt_pedido) AND $data->dt_pedido !== '') OR (is_array($data->dt_pedido) AND (!empty($data->dt_pedido)) )) )
        {

            $filters[] = new TFilter('dt_pedido', '>=', $data->dt_pedido);// create the filter 
        }

        if (isset($data->dt_finalizacao_final) AND ( (is_scalar($data->dt_finalizacao_final) AND $data->dt_finalizacao_final !== '') OR (is_array($data->dt_finalizacao_final) AND (!empty($data->dt_finalizacao_final)) )) )
        {

            $filters[] = new TFilter('dt_finalizacao', '<=', $data->dt_finalizacao_final);// create the filter 
        }

        if (isset($data->dt_finalizacao) AND ( (is_scalar($data->dt_finalizacao) AND $data->dt_finalizacao !== '') OR (is_array($data->dt_finalizacao) AND (!empty($data->dt_finalizacao)) )) )
        {

            $filters[] = new TFilter('dt_finalizacao', '>=', $data->dt_finalizacao);// create the filter 
        }

        if (isset($data->descricaopedido) AND ( (is_scalar($data->descricaopedido) AND $data->descricaopedido !== '') OR (is_array($data->descricaopedido) AND (!empty($data->descricaopedido)) )) )
        {

            $filters[] = new TFilter('descricaopedido', 'like', "%{$data->descricaopedido}%");// create the filter 
        }

        if (isset($data->dtprevisaoentrega_final) AND ( (is_scalar($data->dtprevisaoentrega_final) AND $data->dtprevisaoentrega_final !== '') OR (is_array($data->dtprevisaoentrega_final) AND (!empty($data->dtprevisaoentrega_final)) )) )
        {

            $filters[] = new TFilter('dtprevisaoentrega', '<=', $data->dtprevisaoentrega_final);// create the filter 
        }

        if (isset($data->dtprevisaoentrega) AND ( (is_scalar($data->dtprevisaoentrega) AND $data->dtprevisaoentrega !== '') OR (is_array($data->dtprevisaoentrega) AND (!empty($data->dtprevisaoentrega)) )) )
        {

            $filters[] = new TFilter('dtprevisaoentrega', '>=', $data->dtprevisaoentrega);// create the filter 
        }

        if (isset($data->dataretirada_final) AND ( (is_scalar($data->dataretirada_final) AND $data->dataretirada_final !== '') OR (is_array($data->dataretirada_final) AND (!empty($data->dataretirada_final)) )) )
        {

            $filters[] = new TFilter('dataretirada', '<=', $data->dataretirada_final);// create the filter 
        }

        if (isset($data->dataretirada) AND ( (is_scalar($data->dataretirada) AND $data->dataretirada !== '') OR (is_array($data->dataretirada) AND (!empty($data->dataretirada)) )) )
        {

            $filters[] = new TFilter('dataretirada', '>=', $data->dataretirada);// create the filter 
        }

        if (isset($data->tipo_manutencao_id) AND ( (is_scalar($data->tipo_manutencao_id) AND $data->tipo_manutencao_id !== '') OR (is_array($data->tipo_manutencao_id) AND (!empty($data->tipo_manutencao_id)) )) )
        {

            $filters[] = new TFilter('tipo_manutencao_id', '=', $data->tipo_manutencao_id);// create the filter 
        }

        if (isset($data->departamento_unit_id) AND ( (is_scalar($data->departamento_unit_id) AND $data->departamento_unit_id !== '') OR (is_array($data->departamento_unit_id) AND (!empty($data->departamento_unit_id)) )) )
        {

            $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        }
        if (isset($data->veiculos_id) AND ( (is_scalar($data->veiculos_id) AND $data->veiculos_id !== '') OR (is_array($data->veiculos_id) AND (!empty($data->veiculos_id)) )) )
        {

            $filters[] = new TFilter('veiculos_id', '=', $data->veiculos_id);// create the filter 
        }

    //    if (isset($data->estado_pedido_frotas_id) && 
    //     ((is_scalar($data->estado_pedido_frotas_id) && $data->estado_pedido_frotas_id !== '') || 
    //         (is_array($data->estado_pedido_frotas_id) && !empty($data->estado_pedido_frotas_id))))
    //     {
    //         $estadosEspeciais = [
    //             EstadoPedidoFrotas::NAOENVIADO,
    //             EstadoPedidoFrotas::AGUARDANDO,
    //             EstadoPedidoFrotas::REPROVADO
    //         ];

    //         $valor = $data->estado_pedido_frotas_id;

    //         $temEstadoEspecial = is_array($valor)
    //             ? !empty(array_intersect($valor, $estadosEspeciais))
    //             : in_array($valor, $estadosEspeciais);

    //         if ($temEstadoEspecial) {
    //             $subquery = "(SELECT pedido_frotas_id 
    //                         FROM propostas 
    //                         WHERE estado_pedido_frotas_id IN (" . implode(',', $valor) . "))";
    //             $filters[] = new TFilter('id', 'in', $subquery);
    //         } else {
    //                 $filters[] = new TFilter('estado_pedido_frotas_id', 'in', $valor);
    //         }
    //     }

       if (
            isset($data->estado_pedido_frotas_id) &&
            (
                (is_scalar($data->estado_pedido_frotas_id) && $data->estado_pedido_frotas_id !== '') ||
                (is_array($data->estado_pedido_frotas_id) && !empty($data->estado_pedido_frotas_id))
            )
        ) {
            $estadosEspeciais = [
                EstadoPedidoFrotas::NAOENVIADO,
                EstadoPedidoFrotas::AGUARDANDO,
                EstadoPedidoFrotas::REPROVADO
            ];

            $valor = $data->estado_pedido_frotas_id;

            // Garante array
            $valor = is_array($valor) ? $valor : [$valor];

            // Separa especiais e normais
            $estadosSelecionadosEspeciais = array_intersect($valor, $estadosEspeciais);
            $estadosSelecionadosNormais   = array_diff($valor, $estadosEspeciais);

            // Se tiver ambos, monta um unico filtro via subquery para evitar
            // misturar TCriteria dentro do array de filtros salvo em sessao.
            if (!empty($estadosSelecionadosEspeciais) && !empty($estadosSelecionadosNormais)) {
                $subquery = "(SELECT pedido_frotas_id
                            FROM propostas
                            WHERE estado_pedido_frotas_id IN (" . implode(',', $estadosSelecionadosEspeciais) . ")
                            UNION
                            SELECT id
                            FROM pedido_frotas
                            WHERE estado_pedido_frotas_id IN (" . implode(',', $estadosSelecionadosNormais) . "))";

                $filters[] = new TFilter('id', 'in', $subquery);
            }
            // Só especiais
            elseif (!empty($estadosSelecionadosEspeciais)) {
                $subquery = "(SELECT pedido_frotas_id
                            FROM propostas
                            WHERE estado_pedido_frotas_id IN (" . implode(',', $estadosSelecionadosEspeciais) . "))";

                $filters[] = new TFilter('id', 'in', $subquery);
            }
           // Só normais
            elseif (!empty($estadosSelecionadosNormais)) {
                $estadosSelecionadosNormais = array_values($estadosSelecionadosNormais);

                if (count($estadosSelecionadosNormais) == 1) {
                    $filters[] = new TFilter('estado_pedido_frotas_id', '=', $estadosSelecionadosNormais[0]);
                } else {
                    $filters[] = new TFilter('estado_pedido_frotas_id', 'in', $estadosSelecionadosNormais);
                }
            }
        }
        if (isset($data->cidade_id) AND ( (is_scalar($data->cidade_id) AND $data->cidade_id !== '') OR (is_array($data->cidade_id) AND (!empty($data->cidade_id)) )) )
        {

            $filters[] = new TFilter('cidade_id', '=', $data->cidade_id);// create the filter 
        }

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
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

            // creates a repository for PedidoFrotas
            $repository = new TRepository(self::$activeRecord);

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

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                $validFilters = [];

                foreach ($filters as $filter) 
                {
                    if ($filter instanceof TFilter)
                    {
                        $criteria->add($filter);
                        $validFilters[] = $filter;
                    }
                }

                if (count($validFilters) !== count($filters))
                {
                    TSession::setValue(__CLASS__.'_filters', $validFilters);
                }
            }
            if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }

            $this->datagrid->clear();
            TSession::setValue(__CLASS__.'_detalhes_abertos', []);

            // Recupera valores da sessão
            $userid = TSession::getValue('userid');
            $idunit = TSession::getValue('idunit');

            // Monta a subquery para verificar se a pessoa está no grupo CONDUTOR
            $subquery = '(SELECT pessoa_id FROM pessoa_grupo pg WHERE pg.grupo_pessoa_id = ' . GrupoPessoa::CONDUTOR . ')';

            // Busca pessoas ligadas ao usuário logado e que não sejam do grupo CONDUTOR
            $pes1 = Pessoa::where('system_user_id', '=', $userid)
                        ->where('id', 'NOT IN', $subquery)
                        ->load();

            // Cria os critérios
         //   $criteria = new TCriteria();

            if ($pes1) {
                // Extrai os IDs das pessoas encontradas
                  $ids = array_map(fn($p) => $p->id, $pes1);

                // Aplica filtro com IN
                $criteria->add(new TFilter('estabelecimento_id', 'IN', $ids));

            }

            // Sempre aplica o filtro da unidade do sistema
            $criteria->add(new TFilter('system_unit_id', '=', $idunit));
            $criteria->add(new TFilter('coalesce(abastecimento,0)', '<>', 1));

                $idsdep=[];
                $departamento_usuario = SystemUserDepartamentoUnit::where('system_users_id','=', (TSession::getValue('userid')))
                                                                  ->load();
                if ($departamento_usuario)
                {
                    foreach ($departamento_usuario as $depuser) {
                        $idsdep[]=$depuser->departamento_unit_id;

                    }
                    $criteria->add(new TFilter('departamento_unit_id', 'in', $idsdep) );
                }

            $objects = $repository->load($criteria, FALSE);
            $this->pedidoTemNotaFiscalMap = [];
            self::$pedidoTemPropostaAprovadaMap = [];
            $this->preloadGridRelations($objects);
            $this->preloadPedidosComNotaFiscal($objects);
            $this->preloadPedidosComPropostaAprovada($objects);

            $cont=1;
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                  /*  $suserdep = SystemUserDepartamentoUnit::where('system_users_id','=',TSession::getValue('userid'))
                                                          ->load();

                    //$objects->system_user_id verificar se ele pertence a unidade que logou e addItem
                    if ($suserdep )
                    {                    
                        foreach($suserdep as $suserdeps){
                            if ($suserdeps->departamento_unit_id==$object->departamento_unit_id) {*/

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";
                    
                        $row = new TTableRow;

                    $div = new TElement('div');
                    $div->id = "container_propostas_{$object->id}";

                 /*   $container = new BPageContainer();
                 /*   $container->setAction(new TAction(['PropostasSimpleList', 'onReload'], ['pedido_frotas_id' => $object->id]));
                    $container->setId($div->id);

                    $div->add($container);*/

                    $cell=$row->addCell($div);

                    $cell->colspan = $this->datagrid->getTotalColumns();
                    $cell->style = 'padding: 10px; ';
                    $row->style = 'display:none;';

                    $this->datagrid->insert($cont+1, $row);

                    $cont+=3;

                      /*      }
                        }
                    }*/
                }
            }
         /*   $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
          

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                        $row = new TTableRow;

                    $div = new TElement('div');
                    $div->id = "container_propostas_{$object->id}";

                    $container = new BPageContainer();
                    $container->setAction(new TAction(array('PropostasSimpleList', 'onReload')), ['pedido_frotas_id' => $object->id]);
                    $container->setId($div->id);

                    $div->add($container);

                    $cell=$row->addCell($div);

                    $cell->colspan = $this->datagrid->getTotalColumns();
                    $cell->style = 'padding: 10px; ';

                    $this->datagrid->insert($cont+1, $row);

                    $cont+=3;


                }
            }*/

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

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
 
    private function preloadGridRelations($objects)
    {
        $this->veiculoPlacaMap = [];
        $this->estabelecimentoNomeMap = [];
        $this->departamentoNomeMap = [];
        $this->tipoManutencaoDescricaoMap = [];
        $this->usuarioNomeMap = [];

        if (empty($objects) || !is_array($objects))
        {
            return;
        }

        $veiculoIds = [];
        $pessoaIds = [];
        $departamentoIds = [];
        $tipoIds = [];
        $usuarioIds = [];
        $estadoIds = [];

        foreach ($objects as $object)
        {
            if (!empty($object->veiculos_id))            $veiculoIds[] = (int) $object->veiculos_id;
            if (!empty($object->estabelecimento_id))     $pessoaIds[] = (int) $object->estabelecimento_id;
            if (!empty($object->departamento_unit_id))   $departamentoIds[] = (int) $object->departamento_unit_id;
            if (!empty($object->tipo_manutencao_id))     $tipoIds[] = (int) $object->tipo_manutencao_id;
            if (!empty($object->system_users_id))        $usuarioIds[] = (int) $object->system_users_id;
            if (!empty($object->estado_pedido_frotas_id))  $estadoIds[] = (int) $object->estado_pedido_frotas_id;
            if (!empty($object->estado_pedido_frotas1_id)) $estadoIds[] = (int) $object->estado_pedido_frotas1_id;
        }

        $this->veiculoPlacaMap = $this->loadIndexedMap('Veiculos', $veiculoIds, 'placa');
        $this->estabelecimentoNomeMap = $this->loadIndexedMap('Pessoa', $pessoaIds, 'nome');
        $this->departamentoNomeMap = $this->loadIndexedMap('DepartamentoUnit', $departamentoIds, 'name');
        $this->tipoManutencaoDescricaoMap = $this->loadIndexedMap('TipoManutencao', $tipoIds, 'descricao');
        $this->usuarioNomeMap = $this->loadIndexedMap('SystemUsers', $usuarioIds, 'name');

        $estados = $this->loadRecordsByIds('EstadoPedidoFrotas', $estadoIds);
        foreach ($estados as $estado)
        {
            $this->estadoPedidoFrotasCache[(int) $estado->id] = [
                'nome' => $estado->nome ?? '',
                'cor'  => $estado->cor ?? '#777'
            ];
        }
    }

    private function preloadPedidosComPropostaAprovada($objects)
    {
        if (empty($objects) || !is_array($objects))
        {
            return;
        }

        $pedidoIds = [];
        foreach ($objects as $object)
        {
            if ((int) $object->estado_pedido_frotas_id === (int) EstadoPedidoFrotas::ENTREGUE)
            {
                $pedidoIds[] = (int) $object->id;
            }
        }

        $pedidoIds = array_values(array_unique(array_filter($pedidoIds)));
        if (empty($pedidoIds))
        {
            return;
        }

        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_frotas_id', 'in', $pedidoIds));
        $criteria->add(new TFilter('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO));
        $propostasAprovadas = (new TRepository('Propostas'))->load($criteria, FALSE);

        if (!$propostasAprovadas)
        {
            return;
        }

        foreach ($propostasAprovadas as $proposta)
        {
            self::$pedidoTemPropostaAprovadaMap[(int) $proposta->pedido_frotas_id] = true;
        }
    }

    private function loadIndexedMap($activeRecord, $ids, $field)
    {
        $map = [];
        $records = $this->loadRecordsByIds($activeRecord, $ids);
        foreach ($records as $record)
        {
            $map[(int) $record->id] = $record->$field ?? '';
        }
        return $map;
    }

    private function loadRecordsByIds($activeRecord, $ids)
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', (array) $ids))));
        if (empty($ids))
        {
            return [];
        }

        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', 'in', $ids));
        return (new TRepository($activeRecord))->load($criteria, FALSE) ?: [];
    }

    private function preloadPedidosComNotaFiscal($objects)
    {
        if (empty($objects) || !is_array($objects))
        {
            return;
        }

        $pedidoIdsElegiveis = [];
        foreach ($objects as $object)
        {
            if (!empty($object->id))
            {
                $pedidoIdsElegiveis[] = (int) $object->id;
            }
        }

        $pedidoIdsElegiveis = array_values(array_unique(array_filter($pedidoIdsElegiveis)));
        if (empty($pedidoIdsElegiveis))
        {
            return;
        }

        $criteriaPropostas = new TCriteria;
        $criteriaPropostas->add(new TFilter('pedido_frotas_id', 'in', $pedidoIdsElegiveis));
        $propostas = (new TRepository('Propostas'))->load($criteriaPropostas, FALSE);

        if (empty($propostas))
        {
            return;
        }

        $propostaPedidoMap = [];
        $propostaIds = [];
        foreach ($propostas as $proposta)
        {
            $propostaId = (int) $proposta->id;
            $propostaPedidoMap[$propostaId] = (int) $proposta->pedido_frotas_id;
            $propostaIds[] = $propostaId;
        }

        $propostaIds = array_values(array_unique(array_filter($propostaIds)));
        if (empty($propostaIds))
        {
            return;
        }

        $criteriaDocs = new TCriteria;
        $criteriaDocs->add(new TFilter('propostas_id', 'in', $propostaIds));
        $documentos = (new TRepository('DocumentosPropostas'))->load($criteriaDocs, FALSE);

        if (empty($documentos))
        {
            return;
        }

        foreach ($documentos as $doc)
        {
            $propostaId = (int) ($doc->propostas_id ?? 0);
            if (isset($propostaPedidoMap[$propostaId]))
            {
                $this->pedidoTemNotaFiscalMap[$propostaPedidoMap[$propostaId]] = true;
            }
        }
    }

    public function onShow($param = null)
    {

            if (isset($param['inserido']))  
            {   
                TTransaction::open('minierp');

                $pedido = new PedidoFrotas($param['pedido_frotas_id']);
                if ($pedido) {
                    // Carrega os itens atuais
                    $itenspedido = ItensPedidoFrotas::where('pedido_frotas_id', '=', $param['pedido_frotas_id'])->load();

                    // Carrega os itens antigos da sessão (vindo de outro formulário)
                    $old_items = TSession::getValue('old_items') ?? [];

                    // Mapeia os itens antigos por ID
                    $old_map = [];
                    foreach ($old_items as $old) {
                        $old_map[$old->id] = $old;
                    }

                    if ($itenspedido) {
                        foreach ($itenspedido as $itensp) {
                            $revisarproposta = false;
                            $item_existe_antigo = isset($old_map[$itensp->id]);
                            $antigo = $item_existe_antigo ? $old_map[$itensp->id] : null;

                            // Busca as propostas vinculadas ainda em estados editáveis
                            $propostasrevisao = Propostas::where('pedido_frotas_id', '=', $param['pedido_frotas_id'])
                                                        ->where('estado_pedido_frotas_id', 'in', [
                                                            EstadoPedidoFrotas::NAOENVIADO,
                                                            EstadoPedidoFrotas::PREAPROVADO,
                                                            EstadoPedidoFrotas::AGUARDANDO
                                                        ])
                                                        ->load();

                            foreach ($propostasrevisao as $pr) {
                                $itens = ItensPropostas::where('propostas_id', '=', $pr->id)
                                                    ->where('itens_pedido_frotas_id', '=', $itensp->id)
                                                    ->load();

                                if (!$itens) {
                                    // Novo item adicionado ao pedido, incluir na proposta
                                    $novo = new ItensPropostas();
                                    $novo->produto_id = $itensp->produto_id;
                                    $novo->descricao = $itensp->descricao;
                                    $novo->qtde = $itensp->qtde;
                                    $novo->tipo = $itensp->tipo;
                                    $novo->propostas_id = $pr->id;
                                    $novo->itens_pedido_frotas_id = $itensp->id;
                                    $novo->store();
                                    $revisarproposta = true;
                                } else {
                                    foreach ($itens as $itemProp) {
                                        // Se houve aumento de quantidade
                                        if ($item_existe_antigo && $antigo->qtde < $itensp->qtde) {
                                            $itemProp->qtde = $itensp->qtde;
                                            $itemProp->perc_desconto = ($itensp->qtde * $itemProp->valor) * (TSession::getValue('taxacontrato') / 100);
                                            $itemProp->valor_total = ($itensp->qtde * $itemProp->valor)  - $itemProp->perc_desconto;
                                            // $itemProp->valor = 0;
                                            // $itemProp->perc_desconto = 0;
                                            // $itemProp->valor_total = 0;
                                            $revisarproposta = true;
                                        }

                                        // Se houve mudança na descrição
                                        if ($item_existe_antigo && $antigo->produto_id !== $itensp->produto_id) {
                                            $itemProp->produto_id = $itensp->produto_id;
                                            $itemProp->valor = 0;
                                            $itemProp->perc_desconto = 0;
                                            $itemProp->valor_total = 0;
                                            $revisarproposta = true;
                                        }

                                        if ($revisarproposta) {
                                            $itemProp->store();
                                        }
                                    }
                                }

                                if ($revisarproposta) {
                                    $pr->estado_pedido_frotas1_id = EstadoPedidoFrotas::REVISAO;
                                    $pr->store();
                                }
                            }
                        }
                    }
                }

                // ==== EXCLUSÃO DE ITENS ====

                $conn = TTransaction::get();
                $result = $conn->query("SELECT * FROM itens_pedido_frotas WHERE pedido_frotas_id = " . $param['pedido_frotas_id']);
                $itens_excluidos = [];

                foreach ($result as $old_item) {
                    // Supondo que deleted_at esteja na posição 15 da linha (ajuste se necessário)
                    if (!empty($old_item[15])) {
                        $itens_excluidos[] = $old_item;
                    }
                }

                foreach ($itens_excluidos as $excluido) {
                    $itensPropostas = ItensPropostas::where('itens_pedido_frotas_id', '=', $excluido[0])->load();

                    foreach ($itensPropostas as $ip) {
                        $proposta = new Propostas($ip->propostas_id);

                        if (in_array($proposta->estado_pedido_frotas_id, [
                            EstadoPedidoFrotas::NAOENVIADO,
                            EstadoPedidoFrotas::PREAPROVADO,
                            EstadoPedidoFrotas::AGUARDANDO
                        ])) {
                            $ip->delete(); // ou $ip->deleted_at = date(...); $ip->store(); para exclusão lógica
                            $proposta->estado_pedido_frotas1_id = EstadoPedidoFrotas::REVISAO;
                            $proposta->store();
                        }
                    }
                }
                // $this->onAtualizarValoresItensPropostas($param);
                //atualiza totais da propostas
                $propostasalteradas = Propostas::where('pedido_frotas_id', '=', $param['pedido_frotas_id'])
                                                ->load();
                foreach ($propostasalteradas as $p) {
                    //acerta os totais da proposta
                    $valortotalprodutos = 0;
                    $valortotalservicos = 0;

                    $criteria = new TCriteria();
                    $criteria->add(new TFilter('propostas_id', '=', $p->id));
                    $criteria->add(new TFilter('deleted_at', 'IS', NULL));

                    $repo = new TRepository('ItensPropostas');
                    $itensproprodutospropostas = $repo->load($criteria);
                  
                    $valor_desconto_servico = 0;
                    $valor_desconto_produto = 0;
                   
                    foreach ($itensproprodutospropostas as $itensprodutos) {
                        if ($itensprodutos->tipo==1) {
                        $valortotalprodutos += ( ($itensprodutos->qtde * $itensprodutos->valor) );
                        $valor_desconto_produto += $itensprodutos->perc_desconto;
                        } else {
                        $valortotalservicos += ( ($itensprodutos->qtde * $itensprodutos->valor) );
                        $valor_desconto_servico += $itensprodutos->perc_desconto;
                        }
                    }
                    

                    $txcontrato = ((TSession::getValue('taxacontrato'))) ;

                    $p->total_produtos_sem_desconto = $valortotalprodutos;
                    $p->total_servicos_sem_desconto = $valortotalservicos;
                    $p->total_geral_sem_desconto = $valortotalprodutos+$valortotalservicos;
                    $p->desconto_contratual = $txcontrato;

                    $valortotalprodutoscomdesconto = $valortotalprodutos - $valor_desconto_produto; //($valortotalprodutos * ($txcontrato/100));
                    $valortotalservicoscomdesconto = $valortotalservicos - $valor_desconto_servico; //($valortotalservicos * ($txcontrato/100));

                    $p->total_produtos_com_desconto = $valortotalprodutoscomdesconto;
                    $p->total_servicos_com_desconto =$valortotalservicoscomdesconto;
                    $p->total_geral_com_desconto = $valortotalprodutoscomdesconto+$valortotalservicoscomdesconto;
                    $p->valor_total = $p->total_geral_sem_desconto;
                    $p->valor_desconto = $p->total_geral_sem_desconto - $p->total_geral_com_desconto;
                    $p->valor_liquido = $p->total_geral_com_desconto;

                    $p->store();
                }

                TTransaction::close();
            }
    }

    public function onAtualizarValoresItensPropostas($param = null)
    {
        try {
            TTransaction::open(self::$database);

            $pedido = new PedidoFrotas($param['pedido_frotas_id']);

            $propostas = Propostas::where('pedido_frotas_id', '=', $pedido->id)
                                  ->load();
            $txcontrato = ((TSession::getValue('taxacontrato')/100)) ;
            if ($propostas) {
                foreach ($propostas as $p) {
                    //atualiza os valores dos itens propostas
                    $itenspropostas = ItensPropostas::where('propostas_id', '=', $p->id)
                                                    ->load();
                    $valor_produto_sem_desconto = 0;
                    $valor_servico_sem_desconto = 0;
                    $valor_produto_com_desconto = 0;
                    $valor_servico_com_desconto = 0;
                    if ($itenspropostas) {
                        foreach ($itenspropostas as $itp) {
                            $itp->perc_desconto = $txcontrato * ($itp->qtde * $itp->valor);
                            $itp->valor_total = ($itp->qtde * $itp->valor) - $itp->perc_desconto;
                            $itp->store();
                            if ($itp->tipo==1) {
                                $valor_produto_sem_desconto += ($itp->qtde * $itp->valor);
                                $valor_produto_com_desconto += ($itp->qtde * $itp->valor) - $itp->perc_desconto;
                            } else {
                                $valor_servico_sem_desconto += ($itp->qtde * $itp->valor);
                                $valor_servico_com_desconto += ($itp->qtde * $itp->valor) - $itp->perc_desconto;
                            }

                        }
                    }

                    //atualiza os totais da proposta
                    $p->total_produtos_sem_desconto = $valor_produto_sem_desconto;
                    $p->total_servicos_sem_desconto = $valor_servico_sem_desconto;
                    $p->total_geral_sem_desconto = $valor_produto_sem_desconto + $valor_servico_sem_desconto;
                    $p->total_produtos_com_desconto = $valor_produto_com_desconto;
                    $p->total_servicos_com_desconto = $valor_servico_com_desconto;
                    $p->total_geral_com_desconto = $valor_produto_com_desconto + $valor_servico_com_desconto;
                    $p->valor_total = $p->total_geral_sem_desconto;
                    $p->valor_desconto = $p->total_geral_sem_desconto - $p->total_geral_com_desconto;
                    $p->valor_liquido = $p->total_geral_com_desconto;
                    $p->store();

                }
            }

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public function onEnviarCotacao($param = null) 
    {

            if (isset($param['confirmEnviarCotacao']) && $param['confirmEnviarCotacao']) {
            try {
             
                TTransaction::open(self::$database);
               // $conexao   = TTransaction::get(); 
                //$conexao->exec( "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));" );

                $pedido = new PedidoFrotas($param['id'], false);

                $unit = new SystemUnit($pedido->system_unit_id);
                if ($unit->testar_valor_venal == 1) {
                    $autorizacaopedido = AutorizacaoPedido::where('pedido_frotas_id', '=', $pedido->id)
                                                           ->load();

                    $total = self::onVerificaValorVenal($pedido); // se estiver em método estático

                    if ($total && count($autorizacaopedido) == 0) {
                        throw new Exception("Este veículo já ultrapassou 40% do valor venal em manutenções. É necessária autorização especial para encaminhar propostas para as redes credenciadas.");
                    }
                }
                




                //verifica se é um clliente que abriu o pedido atraves do usuario logado
                $pessoass = Pessoa::where('system_user_id','=',TSession::getValue('userid'))
                                  ->where('id','not in', '(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '.GrupoPessoa::CONDUTOR.')')
                                  ->load();
                  
                $repository = new TRepository('PedidoAsCliente'); 
                $criteria = new TCriteria;
                  
              
                if (!$pessoass) {
                    $criteria->add(new TFilter('pedido_frotas_id', '=', $pedido->id), TExpression::AND_OPERATOR);
                    $fornecedores = $repository->load($criteria);
                } else {
                    $criteria->add(new TFilter('pedido_frotas_id', '=', $pedido->id), TExpression::AND_OPERATOR);
                    $criteria->add(new TFilter('pessoa_id', '=', $pessoass[0]->id), TExpression::AND_OPERATOR);
                    $fornecedores = $repository->load($criteria);
                }

                if (empty($fornecedores)) {
                    throw new Exception('Selecione ao menos uma rede disponível antes de enviar a cotação.');
                }
               
                
                if ($fornecedores) { 

                    $this->gerarCotacoes($fornecedores, $pedido);

                    if (in_array($pedido->estado_pedido_frotas_id, [EstadoPedidoFrotas::PENDENTE, EstadoPedidoFrotas::ENVIADO]) ){
                        
                        // Atualiza o status do pedido e registra histórico
                        //var_dump($criteria);
                        $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::ENVIADO;
                        $pedido->store();

                         $this->registrarHistoricoPedidoFrotas($pedido);

                        //   $this->atualizregistrarHistoricoPedidoaDetalhesPedido($pedido);
                    }

                    TToast::show('success', "Emails enviados!!", 'topRight', 'far:check-circle');
                    TApplication::loadPage('PedidoFrotasList', 'onSetProject');
                } else {
                    new TMessage('info', 'Sr(a) Usuário checar as Redes que deseja enviar as propostas !');
                }
                $this->form->setData($pedido); 
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onEnviarCotacao'));
            $action->setParameters($param);
            $action->setParameter('confirmEnviarCotacao', true);

            new TQuestion('Tem certeza que deseja Gerar a Proposta para Cotação?', $action);
        }
    }
    private function registrarHistoricoPedidoFrotas($pedido)
    {

        $hist = new PedidoFrotasHistorico();
        $hist->pedido_frotas_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d H:i:s');
        $hist->estado_pedido_frotas_id = EstadoPedidoFrotas::ENVIADO; 
        $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $hist->aprovador_frotas_id = $aprovador[0]->id;
        }
        $hist->store();

    }
    private function registrarHistoricoPropostas($propostas)
    {
        $histpropostas = new PropostasHistorico();
        $histpropostas->propostas_id = $propostas->id;
        $histpropostas->data_historico = date('Y-m-d H:i:s');
        $histpropostas->estado_pedido_frotas_id = EstadoPedidoFrotas::PENDENTE; 
       // $histpropostas->aprovador_frotas_id = TSession::getValue('userid');
        $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $histpropostas->aprovador_frotas_id = $aprovador[0]->id;
        }
        $histpropostas->store();
    }

    // private function gerarCotacoes($fornecedores, $pedido)
    // {

    //    foreach ($fornecedores as $fornecedor) 
    //    {

    //         $repositoryPE = new TRepository('PessoaEndereco'); 
    //         $criteriaPE = new TCriteria;
    //         $criteriaPE->add(new TFilter('pessoa_id', '=', $fornecedor->pessoa_id), TExpression::AND_OPERATOR);
    //         $criteriaPE->add(new TFilter('principal', '=', 'T'), TExpression::AND_OPERATOR);
    //         $criteriaPE->add(new TFilter('cidade_id', '<>',0), TExpression::AND_OPERATOR);
    //         $EnderecoPessoa = $repositoryPE->load($criteriaPE);

    //         if ($EnderecoPessoa && count($EnderecoPessoa)>0) {

    //             $repositoryCOT = new TRepository('Propostas'); 
    //             $criteriaCOT = new TCriteria;
    //             $criteriaCOT->add(new TFilter('pedido_frotas_id', '=', $pedido->id), TExpression::AND_OPERATOR);
    //             $criteriaCOT->add(new TFilter('pessoa_id', '=',  $fornecedor->pessoa_id), TExpression::AND_OPERATOR);
    //             $criteriaCOT->add(new TFilter('cidade_id', '=', $EnderecoPessoa[0]->cidade_id), TExpression::AND_OPERATOR);
    //             $cot = $repositoryCOT->load($criteriaCOT);

    //             // $cot = Propostas::where('pedido_frotas_id','=',$pedido->id)
    //             //                 ->where('pessoa_id','=',$fornecedor->pessoa_id)
    //             //                 ->where('cidade_id','=',$end0)
    //             //                 ->load();

    //             if ((!$cot)) 
    //             {

    //             $propostas = new Propostas();
    //             $propostas->pedido_frotas_id = $pedido->id;
    //             $propostas->pessoa_id = $fornecedor->pessoa_id;
    //             $propostas->data_cotacao = date('Y-m-d');
    //             $propostas->estado_pedido_frotas_id = EstadoPedidoFrotas::PENDENTE;
    //             $propostas->system_unit_id = $pedido->system_unit_id;
    //             $propostas->departamento_unit_id = $pedido->departamento_unit_id;
    //             $propostas->system_users_id = TSession::getValue('iduser');
    //             $propostas->cidade_id =  $EnderecoPessoa[0]->cidade_id;
    //             $propostas->veiculos_id =$pedido->veiculos_id;
    //             $propostas->data_limite_resposta = $pedido->data_limite_resposta;
    //             $propostas->km = $pedido->km;
    //             $propostas->ciclos = $pedido->ciclos;
    //             $propostas->obs = $pedido->obs;
    //             $propostas->store();

    //             $this->registrarHistoricoPropostas($propostas);


    //             $codido_email_template_id = EmailTemplate::PEDIDO_AGUARDANDO_ORCAMENTO; // Código do template correto
    //             $emailTemplate = new EmailTemplate($codido_email_template_id);

    //             if ($emailTemplate) {

    //                 $mensagem = $emailTemplate->mensagem;
    //                 $titulo = $emailTemplate->titulo;
    //                 $usr = new SystemUsers(TSession::getValue('userid'));

    //                 // Substituições básicas
    //                 $mensagem = str_replace('{nome}', $propostas->pessoa->nome, $mensagem);
    //                 $mensagem = str_replace('{id}', $pedido->id, $mensagem);
    //                 $mensagem = str_replace('{id1}', $pedido->id, $mensagem);
    //                 $mensagem = str_replace('{data_pedido}', TDate::date2br($pedido->dt_pedido), $mensagem);

    //                 // Substituir veículo: placa - marca - modelo
    //                 $veiculo = new Veiculos($pedido->veiculos_id);
    //                 $identificacaoveiculo = $veiculo->placa . ' - ' . $veiculo->marca->descricao . ' - ' . $veiculo->modelo->descricao;
    //                 $mensagem = str_replace('{identificacao_veiculo}', $identificacaoveiculo, $mensagem);

    //                 // Substituir unidade e departamento (assumindo que $pedido tem esses dados)
    //                 $mensagem = str_replace('{unidade}', $pedido->system_unit->name ?? '', $mensagem);
    //                 $mensagem = str_replace('{departamento}', $pedido->departamento_unit->name ?? '', $mensagem);

    //                 // Data limite (você deve definir essa variável antes, exemplo: 7 dias após pedido)
    //                 $dataLimite = date('d/m/Y', strtotime('+7 days', strtotime($pedido->data_limite_resposta)));
    //                 $mensagem = str_replace('{data_limite}', $dataLimite, $mensagem);

    //                 // Usuário que abriu o pedido (exemplo, você pode ajustar de acordo com seu contexto)
    //                 $usuarioAbriuPedido = $propostas->usuario_abriu_pedido ?? 'Equipe NP3 Benefícios';
    //                 $mensagem = str_replace('{usuario_abriu_pedido}', $usr->name, $mensagem);

    //                 // Montar itens do pedido
    //                 $mensItens = '';
    //                 $itensp = ItensPedidoFrotas::where('pedido_frotas_id', '=', $pedido->id)->load();

    //                 if ($itensp) {
    //                     foreach ($itensp as $item) {
    //                         if ($item->tipo == 2) {
    //                             $mensItens .= "ID: {$item->id} - Descrição do serviço: {$item->produto->nome} - Quantidade: {$item->qtde} - obs: {$item->descricao}<br>";
    //                         } else {
    //                             $mensItens .= "ID: {$item->id} - Descrição do produto: {$item->produto->nome} - Quantidade: {$item->qtde} - obs: {$item->descricao}<br>";
    //                         }
    //                     }
    //                 }

    //                 $mensagem = str_replace('{itens_pedido}', $mensItens, $mensagem);

    //                 // Renderiza título e mensagem (se usar render no seu objeto)
    //                 $titulo = $pedido->render($titulo);
    //                 $mensagem = $pedido->render($mensagem);

    //                 if ($propostas->pessoa->email) {
    //                     $pessoa = new Pessoa($propostas->pessoa_id);

    //                     $notificationParam = [
    //                         'key' => $propostas->id
    //                     ];
    //                     $icon = 'fas fa-file-invoice-dollar';

    //                     SystemNotification::registerpedidofrotas(
    //                         $pessoa->system_user_id,
    //                         $titulo,
    //                         $mensagem,
    //                         new TAction(['PropostasDisponiveisList', 'onShow'], $notificationParam),
    //                         'Visualizar Proposta',
    //                         $icon
    //                     );

    //                     MailService::send($pessoa->email, $titulo, $mensagem, 'html');
    //                 }
    //             }

    //         } else {
    //             // throw new Exception("Já existe uma proposta enviada para este fornecedor para o pedido {$pedido->id} na cidade selecionada.");
    //         }
    //     }


    //   }
    // }

    private function gerarCotacoes($fornecedores, $pedido)
    {
        // Relatório em memória
        $relatorio = [];
        $totalEnviados = 0;
        $totalIgnorados = 0;
        $totalSemCidade = 0;

        foreach ($fornecedores as $fornecedor)
        {
            try {
                // Dados auxiliares
                $pessoa    = new Pessoa($fornecedor->pessoa_id);
                $pessoaNome  = $pessoa->nome ?? ('ID '.$fornecedor->pessoa_id);
                $pessoaEmail = $pessoa->email ?? '';
                $pessoaCidade = new Cidade($pessoa->cidade_id);

                // 1) Endereço principal COM cidade válida
                $repositoryPE = new TRepository('PessoaEndereco');
                $criteriaPE = new TCriteria;
                $criteriaPE->add(new TFilter('pessoa_id', '=', $fornecedor->pessoa_id), TExpression::AND_OPERATOR);
                $criteriaPE->add(new TFilter('principal', '=', 'T'), TExpression::AND_OPERATOR);
                $criteriaPE->add(new TFilter('cidade_id', '<>', 0), TExpression::AND_OPERATOR);

                $EnderecoPessoa = $repositoryPE->load($criteriaPE);

                if (!($EnderecoPessoa && count($EnderecoPessoa) > 0)) {
                    // Sem cidade principal válida => loga e segue
                    $relatorio[] = [
                        'pedido'      => $pedido->id,
                        'pessoa_id'   => $fornecedor->pessoa_id,
                        'pessoa_nome' => $pessoaNome,
                        'cidade_nome'   => null,
                        'status'      => 'NÃO ENVIADO',
                        'motivo'      => 'Sem endereço principal com cidade válida',
                        'proposta_id' => null,
                        'email'       => $pessoaEmail,
                        'email_ok'    => false,
                    ];
                    $totalIgnorados++;
                    $totalSemCidade++;
                    continue;
                }

                $cidadeId = (int) $EnderecoPessoa[0]->cidade_id;
                $cidadeNome = new Cidade($cidadeId);

                // 2) Verifica se já existe proposta para o mesmo pedido/pessoa/cidade
                $repositoryCOT = new TRepository('Propostas');
                $criteriaCOT = new TCriteria;
                $criteriaCOT->add(new TFilter('pedido_frotas_id', '=', $pedido->id), TExpression::AND_OPERATOR);
                $criteriaCOT->add(new TFilter('pessoa_id', '=',  $fornecedor->pessoa_id), TExpression::AND_OPERATOR);
                $criteriaCOT->add(new TFilter('cidade_id', '=', $cidadeId), TExpression::AND_OPERATOR);
                $cot = $repositoryCOT->load($criteriaCOT);

                if ($cot && count($cot) > 0) {
                    // Já existe — registra log e segue
                    $idsExistentes = array_map(fn($c) => $c->id, $cot);
                    $relatorio[] = [
                        'pedido'      => $pedido->id,
                        'pessoa_id'   => $fornecedor->pessoa_id,
                        'pessoa_nome' => $pessoaNome,
                        'cidade_nome'   => $cidadeNome->nome ?? '',
                        'status'      => 'NÃO ENVIADO',
                        'motivo'      => 'Já existe proposta para este pedido/pessoa/cidade (IDs: '.implode(', ', $idsExistentes).')',
                        'proposta_id' => null,
                        'email'       => $pessoaEmail,
                        'email_ok'    => false,
                    ];
                    $totalIgnorados++;
                    continue;
                }

                if($pessoa->ativo == 'F'){
                    // Sem cidade principal válida => loga e segue
                    $relatorio[] = [
                        'pedido'      => $pedido->id,
                        'pessoa_id'   => $fornecedor->pessoa_id,
                        'pessoa_nome' => $pessoaNome,
                        'cidade_nome'   => $cidadeNome->nome ?? '',
                        'status'      => 'NÃO ENVIADO',
                        'motivo'      => 'Rede Credenciada Inativa',
                        'proposta_id' => null, //verificar teste
                        'email'       => $pessoaEmail,
                        'email_ok'    => false, //verificar teste
                    ];
                    $totalIgnorados++;
                    continue;
                }

                // 3) Cria nova proposta
                $propostas = new Propostas();
                $propostas->pedido_frotas_id       = $pedido->id;
                $propostas->pessoa_id              = $fornecedor->pessoa_id;
                $propostas->data_cotacao           = date('Y-m-d');
                $propostas->estado_pedido_frotas_id= EstadoPedidoFrotas::PENDENTE;
                $propostas->system_unit_id         = $pedido->system_unit_id;
                $propostas->departamento_unit_id   = $pedido->departamento_unit_id;
                $propostas->system_users_id        = TSession::getValue('iduser');
                $propostas->cidade_id              = $cidadeId;
                $propostas->veiculos_id            = $pedido->veiculos_id;
                $propostas->data_limite_resposta   = $pedido->data_limite_resposta;
                $propostas->km                     = $pedido->km;
                $propostas->ciclos                 = $pedido->ciclos;
                $propostas->obs                    = $pedido->obs;
                $propostas->store();

                $this->registrarHistoricoPropostas($propostas);

                // 4) Monta e envia e-mail/notificação
                $emailOk = false;
                $titulo  = ''; 
                try {
                    $codido_email_template_id = EmailTemplate::PEDIDO_AGUARDANDO_ORCAMENTO; // template
                    $emailTemplate = new EmailTemplate($codido_email_template_id);

                    if ($emailTemplate) {
                        $mensagem = $emailTemplate->mensagem;
                        $titulo   = $emailTemplate->titulo;

                        $usr = new SystemUsers(TSession::getValue('userid'));

                        // Substituições
                        $mensagem = str_replace('{nome}', $propostas->pessoa->nome, $mensagem);
                        $mensagem = str_replace('{id}', $pedido->id, $mensagem);
                        $mensagem = str_replace('{id1}', $pedido->id, $mensagem);
                        $mensagem = str_replace('{data_pedido}', TDate::date2br($pedido->dt_pedido), $mensagem);

                        $veiculo = new Veiculos($pedido->veiculos_id);
                        $identificacaoveiculo = ($veiculo->placa ?? '') . ' - ' . ($veiculo->marca->descricao ?? '') . ' - ' . ($veiculo->modelo->descricao ?? '');
                        $mensagem = str_replace('{identificacao_veiculo}', $identificacaoveiculo, $mensagem);

                        $mensagem = str_replace('{unidade}', $pedido->system_unit->name ?? '', $mensagem);
                        $mensagem = str_replace('{departamento}', $pedido->departamento_unit->name ?? '', $mensagem);

                        // Se já vem uma data limite em $pedido, usa ela — se não, +7 dias a partir de hoje
                        $baseLimite = $pedido->data_limite_resposta ?: date('Y-m-d');
                        $dataLimite = date('d/m/Y', strtotime('+7 days', strtotime($baseLimite)));
                        $mensagem = str_replace('{data_limite}', $dataLimite, $mensagem);

                        $mensagem = str_replace('{usuario_abriu_pedido}', $usr->name ?? 'Equipe NP3 Benefícios', $mensagem);

                        // Itens do pedido
                        $mensItens = '';
                        $itensp = ItensPedidoFrotas::where('pedido_frotas_id', '=', $pedido->id)->load();
                        if ($itensp) {
                            foreach ($itensp as $item) {
                                $nomeProduto = $item->produto->nome ?? '';
                                $linha = "ID: {$item->id} - " .
                                        ($item->tipo == 2 ? "Descrição do serviço: " : "Descrição do produto: ") .
                                        "{$nomeProduto} - Quantidade: {$item->qtde} - obs: {$item->descricao}<br>";
                                $mensItens .= $linha;
                            }
                        }
                        $mensagem = str_replace('{itens_pedido}', $mensItens, $mensagem);

                        // Render
                        $titulo   = $pedido->render($titulo);
                        $mensagem = $pedido->render($mensagem);

                        if (!empty($pessoa->email)) {
                            $notificationParam = ['key' => $propostas->id];
                            $icon = 'fas fa-file-invoice-dollar';

                            SystemNotification::registerpedidofrotas(
                                $pessoa->system_user_id,
                                $titulo,
                                $mensagem,
                                new TAction(['PropostasDisponiveisList', 'onShow'], $notificationParam),
                                'Visualizar Proposta',
                                $icon
                            );

                            // MailService::send($pessoa->email, $titulo, $mensagem, 'html');
                            $emailOk = true;
                        }
                    }
                } catch (\Throwable $eMail) {
                    // Não interrompe o fluxo; só marca que não foi possível enviar e-mail
                    $emailOk = false;
                }

                // 5) Loga como ENVIADO
                $relatorio[] = [
                    'pedido'      => $pedido->id,
                    'pessoa_id'   => $fornecedor->pessoa_id,
                    'pessoa_nome' => $pessoaNome,
                        'cidade_nome'   => $cidadeNome->nome ?? '',
                    'status'      => 'ENVIADO',
                    'motivo'      => $emailOk ? 'Proposta criada e e-mail/notification disparados' : 'Proposta criada (e-mail não enviado)',
                    'proposta_id' => $propostas->id ?? null,
                    'email'       => $pessoaEmail,
                    'email_ok'    => $emailOk,
                ];
                $totalEnviados++;

            } catch (\Throwable $e) {
                // Qualquer erro inesperado neste fornecedor
                $relatorio[] = [
                    'pedido'      => $pedido->id,
                    'pessoa_id'   => $fornecedor->pessoa_id,
                    'pessoa_nome' => $pessoaNome ?? ('ID '.$fornecedor->pessoa_id),
                        'cidade_nome'   => $cidadeNome->nome ?? '',
                    'status'      => 'NÃO ENVIADO',
                    'motivo'      => 'Erro: ' . $e->getMessage(),
                    'proposta_id' => null,
                    'email'       => $pessoaEmail ?? '',
                    'email_ok'    => false,
                ];
                $totalIgnorados++;
            }
        }

        // ============== RENDERIZAÇÃO EM TELA ==============
        // Monta uma tabela simples com os resultados
        $html  = '<div style="font-family:Arial, sans-serif; font-size:14px">';
        $html .= '<h3>Relatório de Geração de Propostas</h3>';
        $html .= '<p><strong>Pedido:</strong> '.$pedido->id.'</p>';
        $html .= '<p><strong>Resumo:</strong> Enviados: '.$totalEnviados.' | Ignorados: '.$totalIgnorados.' | Sem cidade: '.$totalSemCidade.'</p>';

        $html .= '<div style="max-height:420px; overflow:auto; border:1px solid #ddd; border-radius:6px">';
        $html .= '<table width="100%" cellspacing="0" cellpadding="6" style="border-collapse:collapse">';
        $html .= '<thead>';
        $html .= '<tr style="background:#f6f6f6">';
        $html .= '<th align="left" style="border-bottom:1px solid #ddd">Pessoa</th>';
        $html .= '<th align="left" style="border-bottom:1px solid #ddd">Pessoa ID</th>';
        $html .= '<th align="left" style="border-bottom:1px solid #ddd">Cidade ID</th>';
        $html .= '<th align="left" style="border-bottom:1px solid #ddd">Status</th>';
        $html .= '<th align="left" style="border-bottom:1px solid #ddd">Motivo</th>';
        $html .= '<th align="left" style="border-bottom:1px solid #ddd">Proposta ID</th>';
        $html .= '<th align="left" style="border-bottom:1px solid #ddd">E-mail</th>';
        $html .= '<th align="left" style="border-bottom:1px solid #ddd">E-mail OK?</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        foreach ($relatorio as $r) {
            $statusColor = $r['status'] === 'ENVIADO' ? '#0a7c2f' : '#b00020';
            $html .= '<tr>';
            $html .= '<td style="border-bottom:1px solid #eee">'.htmlspecialchars($r['pessoa_nome']).'</td>';
            $html .= '<td style="border-bottom:1px solid #eee">'.(int)$r['pessoa_id'].'</td>';
            $html .= '<td style="border-bottom:1px solid #eee">'.($r['cidade_nome'] ?? '-').'</td>';
            $html .= '<td style="border-bottom:1px solid #eee; color:'.$statusColor.'"><strong>'.$r['status'].'</strong></td>';
            $html .= '<td style="border-bottom:1px solid #eee">'.htmlspecialchars($r['motivo']).'</td>';
            $html .= '<td style="border-bottom:1px solid #eee">'.($r['proposta_id'] ?? '-').'</td>';
            $html .= '<td style="border-bottom:1px solid #eee">'.htmlspecialchars($r['email'] ?? '').'</td>';
            $html .= '<td style="border-bottom:1px solid #eee">'.($r['email_ok'] ? 'Sim' : 'Não').'</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        $html .= '</div>';
        $html = '
        <style>
        /* alarga TODOS os modais (inclui TMessage) */
        .modal-dialog { max-width: 66vw !important; width: 66vw !important; }
        </style>
        ' . $html;

        new TMessage('info', $html);

        // (Opcional) retornar o array de log para uso em testes ou integrações
        return $relatorio;
    }


    public static function manageRow($id)
    {
        $list = new self([]);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new PedidoFrotas($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    public function onCancelarPedido($param = null) 
    {
        if (isset($param['confirmCancelar']) && $param['confirmCancelar']) {
            try 
            {
                TTransaction::open(self::$database);

                $pedido = new PedidoFrotas($param['id'], false);

                // Atualiza o status do pedido
                $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::CANCELADO;
                $pedido->store();

                // Cancela todas as propostas ligadas ao pedido
                $propostas = Propostas::where('pedido_frotas_id', '=', $pedido->id)->load();
                if ($propostas) {
                    foreach ($propostas as $proposta) {
                        $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::CANCELADO;
                        $proposta->store();
                    }
                }
                
                 // retirar itens da tabela manutencao_garantia
                $manutencao_garantia = ManutencaoGarantia::where('pedido_frotas_id','=',$pedido->id)->load();
                if ($manutencao_garantia) {
                    foreach ($manutencao_garantia as $mg) {
                        $mg->delete();
                    }
                }

                TToast::show('success', "Pedido e propostas cancelados com sucesso!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoFrotasList', 'onSetProject');
                $this->form->setData($pedido); 

                TTransaction::close();

            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de cancelar
            $action = new TAction(array($this, 'onCancelarPedido'));
            $action->setParameters($param);
            $action->setParameter('confirmCancelar', true);

            new TQuestion('Tem certeza que deseja cancelar este pedido?', $action);
        }
    }


    public function onSetProject($param) {
    }
    public static function onShowCurtainFilters($param = null) 
    {
        try 
        {
            //code here

            $filter = new self([]);

            $btnClose = new TButton('closeCurtain');
            $btnClose->class = 'btn btn-sm btn-default';
            $btnClose->style = 'margin-right:10px;';
            $btnClose->onClick = "Template.closeRightPanel();";
            $btnClose->setLabel("Fechar");
            $btnClose->setImage('fas:times');

            $filter->form->addHeaderWidget($btnClose);

            $page = new TPage();
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('page-name', 'PedidoFrotasListSearch');
            $page->setProperty('page_name', 'PedidoFrotasListSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);
   //     TSession::setValue('data_inicial', NULL);
    //    TSession::setValue('data_final', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
      //   $this->datagrid->clear();
    }
    public function onRefresh($param = null) 
    {
        $this->onReload([]);
    }

    public function onAprovarPagamento($param = null) 
    {
       if (isset($param['confirmAprovarPagamento']) && $param['confirmAprovarPagamento']) {
            try {
                TTransaction::open(self::$database);
                
                $pedidoId = (int) ($param['id'] ?? $param['key'] ?? 0);

                $documentospropostas = DocumentosPropostas::where(
                    'propostas_id',
                    'IN',
                    "(SELECT id
                    FROM propostas
                    WHERE pedido_frotas_id = {$pedidoId}
                        AND estado_pedido_frotas_id IN ("
                        . EstadoPedidoFrotas::APROVADO . ", "
                        . EstadoPedidoFrotas::ENTREGUE .
                    "))"
                )->load();

                if (empty($documentospropostas)) {
                    throw new Exception('Não é possível aprovar o pagamento. Nenhum documento de proposta foi anexado.');
                }

                $pedido = new PedidoFrotas($pedidoId, false);
                $this->validarRegraFinanceiraPedido($pedido, 'aprovar o pagamento');

                $queryCotacoes = Propostas::where('pedido_frotas_id','=',$pedido->id)
                                           ->where('estado_pedido_frotas_id','in',[EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::ENTREGUE]);

                if (TSession::getValue('aprovacao_por_item')==2) {
                    $queryCotacoes->where('pessoa_id','=',$pedido->estabelecimento_id);
                }

                $cot = $queryCotacoes->load();
                if ($cot) {
                   foreach($cot as $cotacao){
                        $cotacao->estado_pedido_frotas_id = EstadoPedidoFrotas::PGTOAPROVADO;
                        $cotacao->store();
                        $this->registrarHistoricoCotacaoAprovado($cotacao);
                        if (TSession::getValue('aprovacao_por_item')==2) {
                            break;
                        }
                   }
                }

                $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::PGTOAPROVADO;
                $pedido->store();
                $this->registrarHistoricoPedidoAprovado($pedido);

                TToast::show('success', "Pagamento aprovado com sucesso!!!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoFrotasList', 'onSetProject');
                $this->form->setData($pedido); 
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            $action = new TAction(array($this, 'onAprovarPagamento'));
            $action->setParameters($param);
            $action->setParameter('confirmAprovarPagamento', true);

            new TQuestion('Tem certeza que deseja aprovar o pagamento?', $action);
        }
    }
    private function criarContasFinanceirasDoPedido(PedidoFrotas $pedido): void
    {
        include_once 'app/service/CalculoTaxasImpostosService.php';

        $aprovacaoNormal = TSession::getValue('aprovacao_por_item')==2;

        if ($aprovacaoNormal) {
            $this->validarContaFinanceiraExistente($pedido->id, $pedido->estabelecimento_id);
        }

        $queryPropostas = Propostas::where('pedido_frotas_id','=',$pedido->id)
                                   ->where('estado_pedido_frotas_id','=',EstadoPedidoFrotas::PGTOAPROVADO);

        if ($aprovacaoNormal) {
            $queryPropostas->where('pessoa_id','=',$pedido->estabelecimento_id);
        }

        $propostas = $queryPropostas->load();

        if (!$propostas) {
            $contaExistente = Conta::where('pedido_frotas_id','=',$pedido->id)
                                  ->where('tipo_conta_id','=',TipoConta::PAGAR)
                                  ->first();
            if ($contaExistente) {
                throw new Exception(
                    'Nao foi possivel gerar o financeiro: ja existe uma conta financeira gerada para este pedido. Conta: '
                    . $contaExistente->id
                );
            }

            throw new Exception('Nao foi possivel gerar o financeiro: nenhuma proposta com pagamento aprovado foi encontrada.');
        }

        foreach ($propostas as $cotacao) {
            $this->validarContaFinanceiraExistente($pedido->id, $cotacao->pessoa_id);

            $valorProd = 0;
            $valorServ = 0;
            $valordesconto = 0;

            $itenscotacao = ItensPropostas::where('propostas_id','=',$cotacao->id)
                                          ->where('deleted_at','is',null)
                                          ->load();

            if ($itenscotacao) {
                foreach ($itenscotacao as $itensc) {
                    $vItem = (float) $itensc->valor;
                    $qtd = (float) ($itensc->qtde ?? 1);
                    $vltItem = CalculoTaxasImpostosService::money($vItem * $qtd);

                    if ((int) $itensc->tipo === 1) {
                        $valorProd = CalculoTaxasImpostosService::money($valorProd + $vltItem);
                    } elseif ((int) $itensc->tipo === 2) {
                        $valorServ = CalculoTaxasImpostosService::money($valorServ + $vltItem);
                    }

                    $valordesconto = CalculoTaxasImpostosService::money($valordesconto + ((float) ($itensc->perc_desconto ?? 0)));
                }
            }

            $valorProd = CalculoTaxasImpostosService::money($valorProd);
            $valorServ = CalculoTaxasImpostosService::money($valorServ);

            $taxaspessoa = TaxasPessoa::where('pessoa_id','=',$cotacao->pessoa_id)
                                      ->where('deleted_at','is',null)
                                      ->where('entidade_id','=',TSession::getValue('entidade'))
                                      ->where('system_unit_id','=',TSession::getValue('idunit'))
                                      ->first();

            $imp = CalculoTaxasImpostosService::montarContextoConta($pedido, $valorProd, $valorServ, $taxaspessoa);
            if (($imp['bruto'] ?? 0) > 0 && $valordesconto > 0) {
                $imp['perc_tx_contrato'] = ($valordesconto / $imp['bruto']) * 100;
            }
            $imp['valor_txcontrato_fixado'] = CalculoTaxasImpostosService::money($valordesconto);
            $calc = CalculoTaxasImpostosService::calcularPorContexto($imp);

            $conta = new Conta();
            $conta->pessoa_id            = $cotacao->pessoa_id;
            $conta->forma_pagamento_id   = 1;
            $conta->pedido_frotas_id     = $pedido->id;
            $conta->dt_emissao           = date('Y-m-d');
            $conta->dt_vencimento        = self::calcularVencimentoFinanceiro($conta->dt_emissao);
            $conta->mes_vencimento       = intval(substr($conta->dt_vencimento,5,2));
            $conta->ano_vencimento       = intval(substr($conta->dt_vencimento,0,4));
            $conta->ano_mes_vencimento   = intval(substr($conta->dt_vencimento,0,4).substr($conta->dt_vencimento,5,2));

            $conta->valor_produto_s_desc_txc = $valorProd;
            $conta->valor_servico_s_desc_txc = $valorServ;
            $conta->valor                = CalculoTaxasImpostosService::money($imp['bruto'] ?? 0);
            $conta->valor_txcontrato     = CalculoTaxasImpostosService::money($calc['valor_txcontrato'] ?? 0);
            $conta->valor_liquido        = CalculoTaxasImpostosService::money($calc['base_pos_txcontrato'] ?? 0);
            $conta->valor_produto_c_desc_txc = $calc['valor_produto_c_desc_txc'] ?? 0;
            $conta->valor_servico_c_desc_txc = $calc['valor_servico_c_desc_txc'] ?? 0;

            $conta->ir              = $imp['impostos']['ir'] ?? 0;
            $conta->csll            = $imp['impostos']['csll'] ?? 0;
            $conta->cofins          = $imp['impostos']['cofins'] ?? 0;
            $conta->pis             = $imp['impostos']['pis'] ?? 0;
            $conta->ir_servico      = $imp['impostos']['ir_servico'] ?? 0;
            $conta->csll_servico    = $imp['impostos']['csll_servico'] ?? 0;
            $conta->cofins_servico  = $imp['impostos']['cofins_servico'] ?? 0;
            $conta->pis_servico     = $imp['impostos']['pis_servico'] ?? 0;
            $conta->iss_servico     = $imp['impostos']['iss_servico'] ?? 0;
            $conta->vl_imp_prod     = $calc['vl_imp_prod'] ?? 0;
            $conta->vl_imp_serv     = $calc['vl_imp_serv'] ?? 0;
            $conta->valor_liqbase_prod_posimp = $calc['valor_liqbase_prod_posimp'] ?? 0;
            $conta->valor_liqbase_serv_posimp = $calc['valor_liqbase_serv_posimp'] ?? 0;
            $conta->valor_txc_imp_produto_servico = $calc['valor_txc_imp_produto_servico'] ?? 0;
            $conta->txadm                 = $imp['perc_tx_adm'];
            $conta->valor_txadm           = CalculoTaxasImpostosService::money($calc['valor_txadm'] ?? 0);
            $conta->valor_txantecipacao   = CalculoTaxasImpostosService::money($calc['valor_txantecipacao'] ?? 0);
            $conta->valor_total_liq_tx_conta = CalculoTaxasImpostosService::money($calc['valor_total_liq_tx_conta'] ?? 0);
            $conta->parcela              = 1;
            $conta->descricao            = $pedido->descricaopedido;
            $conta->tipo_conta_id        = TipoConta::PAGAR;
            $conta->mes_emissao          = intval(substr($conta->dt_emissao,5,2));
            $conta->ano_emissao          = intval(substr($conta->dt_emissao,0,4));
            $conta->mes_ano_emissao      = intval(substr($conta->dt_emissao,0,4).substr($conta->dt_emissao,5,2));
            $conta->departamento_unit_id = $pedido->departamento_unit_id;
            $conta->system_users_id      = $pedido->system_users_id;
            $conta->entidade_id          = $pedido->entidade_id;
            $conta->system_unit_id       = $pedido->system_unit_id;

            $this->validarContaFinanceiraCalculada($taxaspessoa, $imp, $calc, 'finalizacao');
            $conta->store();

            if ($aprovacaoNormal) {
                break;
            }
        }
    }

    private function validarContaFinanceiraExistente(int $pedidoFrotasId, int $pessoaId): bool
    {
        if ($pedidoFrotasId <= 0 || $pessoaId <= 0) {
            return false;
        }

        $contaExistente = Conta::where('pedido_frotas_id','=',$pedidoFrotasId)
                              ->where('pessoa_id','=',$pessoaId)
                              ->where('tipo_conta_id','=',TipoConta::PAGAR)
                              ->first();

        if (!$contaExistente) {
            return false;
        }

        if (!empty($contaExistente->dt_pagamento)) {
            throw new Exception(
                'Nao foi possivel gerar o financeiro: ja existe uma conta paga para este pedido e fornecedor. Conta: '
                . $contaExistente->id
            );
        }

        if (!empty($contaExistente->fatura_id)) {
            throw new Exception(
                'Nao foi possivel gerar o financeiro: ja existe fatura gerada para este pedido e fornecedor. Conta: '
                . $contaExistente->id . ', Fatura: ' . $contaExistente->fatura_id
            );
        }

        throw new Exception(
            'Nao foi possivel gerar o financeiro: ja existe uma conta financeira gerada para este pedido e fornecedor. Conta: '
            . $contaExistente->id
        );
    }

    private function validarContaFinanceiraCalculada($taxaspessoa, array $imp, array $calc, string $origem = 'pedido'): void
    {
        $bruto = (float) ($imp['bruto'] ?? 0);
        $valorTxContrato = (float) ($calc['valor_txcontrato'] ?? 0);
        $basePosContrato = (float) ($calc['base_pos_txcontrato'] ?? 0);
        $valorTxAdm = (float) ($calc['valor_txadm'] ?? 0);
        $valorTxAntecipacao = (float) ($calc['valor_txantecipacao'] ?? 0);
        $valorFinal = (float) ($calc['valor_total_liq_tx_conta'] ?? 0);

        $temValorFinanceiro =
            $bruto > 0 ||
            $valorTxContrato > 0 ||
            $basePosContrato > 0 ||
            $valorTxAdm > 0 ||
            $valorTxAntecipacao > 0 ||
            $valorFinal > 0;

        if ($temValorFinanceiro) {
            return;
        }

        $taxaAdm = (float) ($taxaspessoa->taxaadm ?? 0);
        $taxaBancaria = (float) ($taxaspessoa->taxabancaria ?? 0);
        $taxaAntecipacao = (float) ($taxaspessoa->taxaantecipacao ?? 0);

        if ($taxaAdm <= 0 && $taxaBancaria <= 0 && $taxaAntecipacao <= 0) {
            throw new Exception('Nao foi possivel gerar o financeiro do ' . $origem . ': taxas da pessoa estao zeradas e o calculo da conta resultou em valores zerados.');
        }

        throw new Exception('Nao foi possivel gerar o financeiro do ' . $origem . ': o calculo da conta resultou em valores zerados.');
    }

    private function registrarHistoricoPedidoAprovado($pedido)
     {
         $hist = new PedidoFrotasHistorico();
         $hist->pedido_frotas_id = $pedido->id;
         $hist->data_operacao = date('Y-m-d H:i:s');
         $hist->estado_pedido_frotas_id = EstadoPedidoFrotas::PGTOAPROVADO; 
         $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
         if ($aprovador) {
            $hist->aprovador_frotas_id = $aprovador[0]->id;
         }
       //  $hist->aprovador_frotas_id = TSession::getValue('iduser');
         $hist->store();
     } 

   public static function onExibirDetalhe($param=null)
    {

        try 
        {
            $pedidoId = (int) ($param['key'] ?? 0);
            if ($pedidoId <= 0)
            {
                return;
            }

            $sessionKey = __CLASS__.'_detalhes_abertos';
            $detalhesAbertos = TSession::getValue($sessionKey) ?: [];
            $containerId = "container_propostas_{$pedidoId}";

            if (!empty($detalhesAbertos[$pedidoId]))
            {
                unset($detalhesAbertos[$pedidoId]);
                TSession::setValue($sessionKey, $detalhesAbertos);

                TScript::create("
                    (function () {
                        var el = document.getElementById('{$containerId}');
                        if (!el) return;
                        el.innerHTML = '';
                        el.style.display = 'none';
                        var tr = el.closest ? el.closest('tr') : null;
                        if (tr) tr.style.display = 'none';
                    })();
                ");
                return;
            }

            $detalhesAbertos[$pedidoId] = true;
            TSession::setValue($sessionKey, $detalhesAbertos);

            TSession::setValue('pedido_frotas_id', NULL);
            TSession::setValue('pedido_frotas_id', $pedidoId);

            TScript::create("
                (function () {
                    var el = document.getElementById('{$containerId}');
                    if (!el) return;
                    el.style.display = '';
                    var tr = el.closest ? el.closest('tr') : null;
                    if (tr) tr.style.display = '';
                })();
            ");

            TApplication::loadPage('PropostaPendenteList', 'onShow', [
                'target_container' => $containerId,
                'pedido_frotas_id' => $pedidoId
            ]);
        
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
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

    private static function calcularVencimentoFinanceiro($dtFinalizacao)
    {
        if (empty($dtFinalizacao))
        {
            $dtFinalizacao = date('Y-m-d');
        }

        try
        {
            $dataBase = new DateTime(substr((string) $dtFinalizacao, 0, 10));
        }
        catch (Exception $e)
        {
            $dataBase = new DateTime(date('Y-m-d'));
        }

        $dataBase->modify('first day of next month');
        $dataBase->modify('+35 days');

        return $dataBase->format('Y-m-d');
    }

    public static function onExibirView($object)
    {

        try 
        {
            if (self::$exibirViewCache !== null)
            {
                return self::$exibirViewCache;
            }

            $pes1 = Pessoa::where('system_user_id','=',TSession::getValue('userid'))->load();
            if ($pes1)
            {
                $pessoa_grupo = PessoaGrupo::where('pessoa_id', '=', $pes1[0]->id)
                                           ->where('grupo_pessoa_id', '=', 5)
                                           ->load();
                self::$exibirViewCache = !empty($pessoa_grupo);
                return self::$exibirViewCache;
            }

            self::$exibirViewCache = true;
            return true;
        
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }

    }

    public static function onExibirEditar($object)
    {

        try 
        {
                if( in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PENDENTE, EstadoPedidoFrotas::ENVIADO, EstadoPedidoFrotas::COMPROPOSTA, EstadoPedidoFrotas::PREAPROVADO]) )
                {
                    return true;
                }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }

    }

    public static function onExibirCancelarAprovacao($object)
    {
        try 
        {
           if( in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::APROVADO]) )
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public static function onExibirAutorizacao($object)
    {
        try 
        {
           if( in_array(EstadoPedidoFrotas::VALORVENAL, self::getEstadosDisponiveisCache()) )
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onExibirRegularizarDotacao($object)
    {
        try
        {
            $estadosPermitidos = [
                EstadoPedidoFrotas::FINALIZADO,
                EstadoPedidoFrotas::APROVADO,
                EstadoPedidoFrotas::PGTOAPROVADO,
                EstadoPedidoFrotas::ENTREGUE,
                EstadoPedidoFrotas::PREAPROVADO,
            ];

            return in_array((string) $object->estado_pedido_frotas_id, array_map('strval', $estadosPermitidos), true)
                && in_array(EstadoPedidoFrotas::APROVADO, self::getEstadosDisponiveisCache());
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onExibirAnexos($object)
    {
        try 
        {
              if( in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PGTOAPROVADO, EstadoPedidoFrotas::FINALIZADO,EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::ENTREGUE, EstadoPedidoFrotas::COMPROPOSTA ]) )
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public static function onExibirVerAnexos($object)
    {
        try 
        {
              if( in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PGTOAPROVADO, EstadoPedidoFrotas::FINALIZADO,EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::ENTREGUE ]) )
            {
                if ($object->estabelecimento_id) 
                {
                    return true;
                }                
                else {
                   return false;
                }
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }


    public static function onExibirFinalizarPedidoEGerarFinanceiro($object)
    { 
        try 
        {
             if( in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PGTOAPROVADO]) )
            {
                
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onExibirAprovarPagamento($object)
    {
        try 
        {
            if( in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::ENTREGUE]) )
            {
                return empty(self::$pedidoTemPropostaAprovadaMap[(int) $object->id]);
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onExibirCancelado($object)
    {
        try 
        {

             if( in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PENDENTE, EstadoPedidoFrotas::ENVIADO, EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::COMPROPOSTA, EstadoPedidoFrotas::PREAPROVADO]) ) 
            {
                if (!in_array(EstadoPedidoFrotas::CANCELADO, self::getEstadosDisponiveisCache()))
                {
                   return false;
                } else {
                    return true;

                }
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onExibirReprovar($object)
    {
        try 
        {
            $estado = $object->estado_pedido_frotas_id;
            $estadosPermitidos = self::getEstadosDisponiveisCache();

            // Exibe o botão apenas se:
            // - O estado atual for COMPROPOSTA ou PREAPROVADO
            // - E o usuário tiver permissão para REPROVAR
            if (in_array($estado, [EstadoPedidoFrotas::COMPROPOSTA, EstadoPedidoFrotas::PREAPROVADO]) &&
                in_array(EstadoPedidoFrotas::REPROVADO, $estadosPermitidos))
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onExibirAprovar($object)
    {
        try 
        {
            $estado = $object->estado_pedido_frotas_id;
            $estadosPermitidos = self::getEstadosDisponiveisCache();

            // Exibe botão apenas se o estado atual for COMPROPOSTA ou PREAPROVADO
            // E se o usuário puder aprovar (tem o estado APROVADO nos permitidos)
            if (in_array($estado, [EstadoPedidoFrotas::COMPROPOSTA, EstadoPedidoFrotas::PREAPROVADO]) &&
                in_array(EstadoPedidoFrotas::APROVADO, $estadosPermitidos))
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

   public static function onExibirPreAprovar($object)
    {
        try 
        {
            $estado = $object->estado_pedido_frotas_id;
            $estadosPermitidos = self::getEstadosDisponiveisCache();

            // Exibe o botão apenas se:
            // - O estado atual for COMPROPOSTA
            // - E o usuário tiver permissão para PREAPROVAR
            if ($estado == EstadoPedidoFrotas::COMPROPOSTA &&
                in_array(EstadoPedidoFrotas::PREAPROVADO, $estadosPermitidos))
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }



    public function onFinalizarPedidoEGerarFinanceiro($param = null) 
    {

       if (isset($param['confirmFinalizacao']) && $param['confirmFinalizacao']) {
            try 
            {
                TTransaction::open(self::$database);
               $pedidoId = (int) $param['id'];

                if (!$this->pedidoPossuiNotaFiscalProposta($pedidoId)) {
                    throw new Exception('Não é possível finalizar o pedido. Nenhuma nota fiscal de produto ou serviço foi anexada.');
                }
                // Atualiza o status do pedido e registra histórico
                $pedido = new PedidoFrotas($pedidoId, false);
                $this->validarRegraFinanceiraPedido($pedido, 'finalizar o pedido');
                $this->criarContasFinanceirasDoPedido($pedido);

                $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::FINALIZADO;
                $pedido->dt_finalizacao = date('Y-m-d');
                $pedido->store();

                $this->registrarHistoricoPedidoFinalizar($pedido);

                $cot = Propostas::where('estado_pedido_frotas_id','=',EstadoPedidoFrotas::PGTOAPROVADO)
                                  ->where('pedido_frotas_id','=',$pedido->id)
                                  ->load();
                if ($cot) {
                    foreach($cot as $cotacao){
                      $cotacao->estado_pedido_frotas_id = EstadoPedidoFrotas::FINALIZADO;
                      $cotacao->store();
                      $this->registrarHistoricoCotacaoFinalizar($cotacao);
                    }
                }

                TToast::show('success', "Pedido finalizado e financeiro gerado com sucesso!!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoFrotasList', 'onSetProject');
                $this->form->setData($pedido); 
                TTransaction::close();

            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onFinalizarPedidoEGerarFinanceiro'));
            $action->setParameters($param);
            $action->setParameter('confirmFinalizacao', true);

            new TQuestion('Tem certeza que deseja finalizar este pedido e gerar o financeiro?', $action);
        }
      /*  try 
        {
            //code here
      */
            //</autoCode>
     /*   }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }

    private function pedidoPossuiNotaFiscalProposta(int $pedidoId): bool
    {
        if ($pedidoId <= 0) {
            return false;
        }

        $propostas = Propostas::where('pedido_frotas_id','=',$pedidoId)->load();

        if (!$propostas) {
            return false;
        }

        foreach ($propostas as $proposta) {
            $documento = DocumentosPropostas::where('propostas_id','=',$proposta->id)
                                           ->where('tipo_documentos_propostas_id','in',[1, 2])
                                           ->first();
            if ($documento) {
                return true;
            }
        }

        return false;
    }

    private function validarRegraFinanceiraPedido(PedidoFrotas $pedido, string $acao): void
    {
        if ($this->unidadeExigeDotacaoEmpenhoFrotas($pedido)) {
            $this->validarDotacaoEmpenhoPedido($pedido, $acao);
            return;
        }

        $this->validarSaldoContratualPedido($pedido, $acao);
    }

    private function unidadeExigeDotacaoEmpenhoFrotas(PedidoFrotas $pedido): bool
    {
        $unitId = (int) ($pedido->system_unit_id ?? TSession::getValue('idunit'));
        if ($unitId <= 0) {
            return true;
        }

        $sessionUnitId = (int) TSession::getValue('idunit');
        $sessionConfig = TSession::getValue('exige_dotacao_empenho_frotas');
        if ($sessionUnitId === $unitId && $sessionConfig !== null && $sessionConfig !== '') {
            return in_array((string) $sessionConfig, ['1', 'S', 'Y', 'T'], true);
        }

        $unit = new SystemUnit($unitId);
        $config = $unit->exige_dotacao_empenho_frotas ?? null;

        if ($config === null || $config === '') {
            return true;
        }

        return in_array((string) $config, ['1', 'S', 'Y', 'T'], true);
    }

    private function validarDotacaoEmpenhoPedido(PedidoFrotas $pedido, string $acao = 'finalizar o pedido'): void
    {
        $pedidoId = (int) $pedido->id;
        if ($pedidoId <= 0) {
            throw new Exception('Nao foi possivel validar a dotacao orcamentaria do pedido.');
        }

        $dotacoes = DotacaoPedidoFrotas::where('pedido_frotas_id','=',$pedidoId)->load();
        if (!$dotacoes) {
            throw new Exception("Nao e possivel {$acao}. Nenhuma dotacao orcamentaria/empenho foi lancada para este pedido.");
        }

        $totaisPorSaldo = [];
        $possuiDotacaoValida = false;

        foreach ($dotacoes as $dotacao) {
            if (!empty($dotacao->deleted_at)) {
                continue;
            }

            $saldoId = (int) ($dotacao->saldo_departamento_id ?? 0);
            $valorDotado = round((float) ($dotacao->valor ?? 0), 2);

            if ($saldoId <= 0) {
                throw new Exception("Nao e possivel {$acao}. Existe dotacao orcamentaria sem empenho vinculado.");
            }

            if ($valorDotado <= 0) {
                throw new Exception("Nao e possivel {$acao}. Existe dotacao orcamentaria com valor zerado ou invalido.");
            }

            $saldoDepartamento = new SaldoDepartamento($saldoId);
            if ((string) $saldoDepartamento->status_saldo_departamento_id === (string) StatusSaldoDepartamento::ANULADO) {
                $numeroEmpenho = $saldoDepartamento->numero_documento_empenho ?? $saldoId;
                throw new Exception("Nao e possivel {$acao}. O empenho {$numeroEmpenho} esta anulado.");
            }

            if (!isset($totaisPorSaldo[$saldoId])) {
                $totaisPorSaldo[$saldoId] = 0.0;
            }

            $totaisPorSaldo[$saldoId] += $valorDotado;
            $possuiDotacaoValida = true;
        }

        if (!$possuiDotacaoValida) {
            throw new Exception("Nao e possivel {$acao}. Nenhuma dotacao orcamentaria/empenho ativo foi encontrado para este pedido.");
        }

        foreach ($totaisPorSaldo as $saldoId => $valorDotado) {
            $saldoDisponivel = $this->getSaldoDisponivelEmpenhoParaFinalizar((int) $saldoId, $pedidoId);
            $saldoAposFinalizar = round($saldoDisponivel - $valorDotado, 2);

            if ($saldoAposFinalizar < -0.01) {
                $saldoDepartamento = new SaldoDepartamento((int) $saldoId);
                $numeroEmpenho = $saldoDepartamento->numero_documento_empenho ?? $saldoId;

                throw new Exception(
                    sprintf(
                        'Nao e possivel %s. O empenho %s nao possui saldo suficiente. Disponivel antes deste pedido: R$ %s. Lancado no pedido: R$ %s. Saldo ficaria: R$ %s.',
                        $acao,
                        $numeroEmpenho,
                        number_format($saldoDisponivel, 2, ',', '.'),
                        number_format($valorDotado, 2, ',', '.'),
                        number_format($saldoAposFinalizar, 2, ',', '.')
                    )
                );
            }
        }
    }

    private function validarSaldoContratualPedido(PedidoFrotas $pedido, string $acao): void
    {
        $pedidoId = (int) $pedido->id;
        if ($pedidoId <= 0) {
            throw new Exception('Nao foi possivel validar o saldo contratual do pedido.');
        }

        $entidadeId = $this->getEntidadeIdPedido($pedido);
        if ($entidadeId <= 0) {
            throw new Exception("Nao e possivel {$acao}. A entidade do contrato nao foi identificada.");
        }

        $valorPedido = $this->getValorFinanceiroPedido($pedido);
        if ($valorPedido <= 0) {
            throw new Exception("Nao e possivel {$acao}. O pedido nao possui valor financeiro valido para abater do saldo contratual.");
        }

        $saldoInfo = $this->getSaldoContratualDisponivelPedido($pedido, $entidadeId);

        if ($saldoInfo['contratos'] <= 0) {
            throw new Exception("Nao e possivel {$acao}. Nenhum saldo contratual ativo foi encontrado para a entidade deste pedido.");
        }

        $saldoAposPedido = round($saldoInfo['disponivel'] - $valorPedido, 2);
        if ($saldoAposPedido < -0.01) {
            throw new Exception(
                sprintf(
                    'Nao e possivel %s. O saldo contratual nao possui saldo suficiente. Disponivel antes deste pedido: R$ %s. Valor do pedido: R$ %s. Saldo ficaria: R$ %s.',
                    $acao,
                    number_format($saldoInfo['disponivel'], 2, ',', '.'),
                    number_format($valorPedido, 2, ',', '.'),
                    number_format($saldoAposPedido, 2, ',', '.')
                )
            );
        }
    }

    private function getEntidadeIdPedido(PedidoFrotas $pedido): int
    {
        $entidadeId = (int) ($pedido->entidade_id ?? 0);
        if ($entidadeId > 0) {
            return $entidadeId;
        }

        $unitId = (int) ($pedido->system_unit_id ?? TSession::getValue('idunit'));
        if ($unitId > 0) {
            $unit = new SystemUnit($unitId);
            return (int) ($unit->entidade_id ?? 0);
        }

        return (int) TSession::getValue('entidade');
    }

    private function getValorFinanceiroPedido(PedidoFrotas $pedido): float
    {
        foreach (['valor_liquido_proposta', 'valor_total_proposta', 'valor_total'] as $campo) {
            $valor = round((float) ($pedido->{$campo} ?? 0), 2);
            if ($valor > 0) {
                return $valor;
            }
        }

        return 0.0;
    }

    private function getSaldoContratualDisponivelPedido(PedidoFrotas $pedido, int $entidadeId): array
    {
        $dataPedido = $pedido->dt_pedido ?: date('Y-m-d');
        $saldoBase = 0.0;
        $contratosAtivos = 0;

        $contratos = SaldoEntidadeContrato::where('entidade_id', '=', $entidadeId)
            ->where('deleted_at', 'is', NULL)
            ->load();

        if ($contratos) {
            foreach ($contratos as $contrato) {
                if (!$this->saldoContratualCobreDataPedido($contrato, $dataPedido)) {
                    continue;
                }

                $valor = (float) ($contrato->valor_saldo ?? 0);
                $tipo = strtoupper((string) ($contrato->tipotransacao ?? 'C'));
                $saldoBase += ($tipo === 'D') ? -$valor : $valor;
                $contratosAtivos++;
            }
        }

        $consumo = $this->getConsumoSaldoContratual($pedido, $entidadeId);

        return [
            'contratos' => $contratosAtivos,
            'base' => round($saldoBase, 2),
            'consumo' => round($consumo, 2),
            'disponivel' => round($saldoBase - $consumo, 2),
        ];
    }

    private function saldoContratualCobreDataPedido(SaldoEntidadeContrato $contrato, string $dataPedido): bool
    {
        $ativo = $contrato->ativo ?? null;
        if ($ativo !== null && $ativo !== '' && !in_array((string) $ativo, ['1', 'S', 'T', 'Y'], true)) {
            return false;
        }

        if (!empty($contrato->dtinicio) && $contrato->dtinicio > $dataPedido) {
            return false;
        }

        if (!empty($contrato->dtfinal) && $contrato->dtfinal < $dataPedido) {
            return false;
        }

        return true;
    }

    private function getConsumoSaldoContratual(PedidoFrotas $pedido, int $entidadeId): float
    {
        $pedidosQuery = PedidoFrotas::where('estado_pedido_frotas_id', 'in', [
                EstadoPedidoFrotas::APROVADO,
                EstadoPedidoFrotas::FINALIZADO,
                EstadoPedidoFrotas::ENTREGUE,
                EstadoPedidoFrotas::PGTOAPROVADO,
            ])
            ->where('entidade_id', '=', $entidadeId)
            ->where('deleted_at', 'is', NULL);

        $unitId = (int) ($pedido->system_unit_id ?? 0);
        if ($unitId > 0) {
            $pedidosQuery->where('system_unit_id', '=', $unitId);
        }

        $pedidoId = (int) ($pedido->id ?? 0);
        if ($pedidoId > 0) {
            $pedidosQuery->where('id', '<>', $pedidoId);
        }

        $consumo = 0.0;
        $pedidos = $pedidosQuery->load();
        if ($pedidos) {
            foreach ($pedidos as $pedidoConsumido) {
                $consumo += $this->getValorFinanceiroPedido($pedidoConsumido);
            }
        }

        return round($consumo, 2);
    }

    private function getSaldoDisponivelEmpenhoParaFinalizar(int $saldoId, int $pedidoId = 0): float
    {
        if ($saldoId <= 0) {
            return 0.0;
        }

        $saldoDepartamento = new SaldoDepartamento($saldoId);
        $saldoDisponivel = $this->getValorEmpenhoPorTipo($saldoDepartamento);

        $subquery = 'SELECT id FROM pedido_frotas WHERE estado_pedido_frotas_id IN (' .
                    EstadoPedidoFrotas::APROVADO . ',' .
                    EstadoPedidoFrotas::FINALIZADO . ',' .
                    EstadoPedidoFrotas::ENTREGUE . ',' .
                    EstadoPedidoFrotas::PGTOAPROVADO . ')';

        $dotacoesQuery = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $saldoId)
            ->where('pedido_frotas_id', 'IN', "($subquery)");

        if ($pedidoId > 0) {
            $dotacoesQuery->where('pedido_frotas_id', '<>', $pedidoId);
        }

        $dotacoes = $dotacoesQuery->load();
        if ($dotacoes) {
            foreach ($dotacoes as $dotacao) {
                if (empty($dotacao->deleted_at)) {
                    $saldoDisponivel -= (float) $dotacao->valor;
                }
            }
        }

        return round($saldoDisponivel, 2);
    }

    private function getValorEmpenhoPorTipo(SaldoDepartamento $saldoDepartamento): float
    {
        $tipo = strtoupper((string) ($saldoDepartamento->tipo ?? ''));

        if ($tipo === 'P' || (int) $tipo === (int) SaldoDepartamento::PRODUTO) {
            $valorProduto = (float) ($saldoDepartamento->saldo_produto ?? 0);
            return $valorProduto > 0 ? $valorProduto : (float) ($saldoDepartamento->saldo_total ?? 0);
        }

        if ($tipo === 'S' || (int) $tipo === (int) SaldoDepartamento::SERVICO) {
            $valorServico = (float) ($saldoDepartamento->saldo_servico ?? 0);
            return $valorServico > 0 ? $valorServico : (float) ($saldoDepartamento->saldo_total ?? 0);
        }

        $saldoTotal = (float) ($saldoDepartamento->saldo_total ?? 0);
        if ($saldoTotal > 0) {
            return $saldoTotal;
        }

        return (float) ($saldoDepartamento->saldo_produto ?? 0) + (float) ($saldoDepartamento->saldo_servico ?? 0);
    }

    public static function onExibirEnvio($object)
    {
        try 
        {
            if (in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PENDENTE,EstadoPedidoFrotas::NAOENVIADO, EstadoPedidoFrotas::ENVIADO, EstadoPedidoFrotas::COMPROPOSTA, EstadoPedidoFrotas::PREAPROVADO  ]) )
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onExibirEntradaVeiculo($object)
    {
        try 
        {
            if (in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::APROVADO]) && $object->condutor_entrada_id == null)
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onExibirRetiradaVeiculo($object)
    {
        try 
        {
            if (in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::FINALIZADO]) )
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }


    public static function onExibirDocCotacao($object)
    {
        try 
        {
            if( in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::FINALIZADO, EstadoPedidoFrotas::PGTOAPROVADO, EstadoPedidoFrotas::ENTREGUE]) )
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onExibirExcluir($object)
    {
        try 
        {
            if( in_array($object->estado_pedido_frotas_id, self::getEstadosDisponiveisCache()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PENDENTE]) )
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    private function registrarHistoricoPedidoFinalizar($pedido)
    {
        $hist = new PedidoFrotasHistorico();
        $hist->pedido_frotas_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d H:i:s');
        $hist->estado_pedido_frotas_id = EstadoPedidoFrotas::FINALIZADO; 
          $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $hist->aprovador_frotas_id = $aprovador[0]->id;
        }
      //  $hist->aprovador_frotas_id = TSession::getValue('userid');
        $hist->store();
    }

    private function registrarHistoricoCotacaoFinalizar($cotacao)
    {
        $histcotacao = new PropostasHistorico();
        $histcotacao->propostas_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d H:i:s');
        $histcotacao->estado_pedido_frotas_id = $cotacao->estado_pedido_frotas_id; 
        $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $histcotacao->aprovador_frotas_id = $aprovador[0]->id;
        }
     //   $histcotacao->aprovador_frotas_id = TSession::getValue('userid');
        $histcotacao->store();
    }

   /* public function onImprimir($param = null) 
    {
        try {

            include 'app/control/PedidoFrotasOrcamento.php';
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }

    }*/
     public function onImprimir($param = null) 
    {
        try 
        {
            include_once 'app/control/mfrotas/PedidoFrotasOrcamento.php';

            $orcamento = new PedidoFrotasOrcamento();
            $orcamento->gerar($param);

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    
    public function onCancelarAprovacao($param = null) 
    {

            //code here
            //voltar o status de aprovado para com proposta aguardando 
            //no pedido e na cotacao aguardando proposta.
            //gravar no historico da cotacao e pedido

        if (isset($param['confirmEnviarCancelarAprovacao']) && $param['confirmEnviarCancelarAprovacao']) {
            try {
                TTransaction::open(self::$database);

                $pedido = new PedidoFrotas($param['id'], false);

                // Atualiza o status do pedido e registra histórico
                $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::COMPROPOSTA;
                $pedido->estabelecimento_id = null;
                $pedido->data_aprovacao = null;
                $pedido->store();

                $this->registrarHistoricoPedidocomproposta($pedido);

                $propostas = Propostas::where('pedido_frotas_id','=',$pedido->id)
                                  ->where('estado_pedido_frotas_id','in',[EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::REPROVADO])
                                  ->load();
                if ($propostas){
                    foreach ($propostas as $prop) 
                    {
                        $estadoAnteriorProposta = (int) $prop->estado_pedido_frotas_id;

                        if ($estadoAnteriorProposta === (int) EstadoPedidoFrotas::REPROVADO && !$this->isPropostaReprovadaAutomaticamente($prop))
                        {
                            continue;
                        }

                        $prop->estado_pedido_frotas_id = EstadoPedidoFrotas::AGUARDANDO;
                        if ($estadoAnteriorProposta === (int) EstadoPedidoFrotas::REPROVADO)
                        {
                            $prop->obs = null;
                        }
                        $prop->store();
                        $this->registrarHistoricoCotacaoAguardando($prop);
                        $this->limparStatusItensProposta($prop);
                    }
                }
                // retirar itens da tabela manutencao_garantia
                $manutencao_garantia = ManutencaoGarantia::where('pedido_frotas_id','=',$pedido->id)->load();
                if ($manutencao_garantia) {
                    foreach ($manutencao_garantia as $mg) {
                        $mg->delete();
                    }
                }
                $dotacaopedidofrotas = DotacaoPedidoFrotas::where('pedido_frotas_id','=',$pedido->id)
                                  ->load();
                if ($dotacaopedidofrotas){
                    foreach ($dotacaopedidofrotas as $dpf) 
                    {
                       $dpf->delete();
                    }
                }

                TTransaction::close();
                TToast::show('success', "Cancelamento da aprovação feito com sucesso!!!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoFrotasList', 'onSetProject');
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onCancelarAprovacao'));
            $action->setParameters($param);
            $action->setParameter('confirmEnviarCancelarAprovacao', true);

            new TQuestion('Tem certeza que deseja fazer o cancelamento desta aprovação?', $action);
        }
    }

    private function isPropostaReprovadaAutomaticamente(Propostas $proposta): bool
    {
        if (stripos((string) $proposta->obs, 'Reprovada automaticamente') === 0)
        {
            return true;
        }

        $historico = PropostasHistorico::where('propostas_id', '=', $proposta->id)
            ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::REPROVADO)
            ->where('obs', 'like', 'Reprovada automaticamente%')
            ->first();

        return !empty($historico);
    }

    private function limparStatusItensProposta(Propostas $proposta): void
    {
        $itens = ItensPropostas::where('propostas_id','=',$proposta->id)->load();
        if ($itens) {
            foreach ($itens as $item) {
                $item->estado_pedido_frotas_id = null;
                $item->store();
            }
        }
    }

    private function registrarHistoricoPedidocomproposta($pedido)
    {
        $hist = new PedidoFrotasHistorico();
        $hist->pedido_frotas_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d H:i:s');
        $hist->estado_pedido_frotas_id = EstadoPedidoFrotas::COMPROPOSTA; 
        $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $hist->aprovador_frotas_id = $aprovador[0]->id;
        }    
 //       $hist->aprovador_frotas_id = TSession::getValue('iduser');
        $hist->store();
    }
    private function registrarHistoricoCotacaoAguardando($propostas)
    {
        $histpropostas = new PropostasHistorico();
        $histpropostas->propostas_id = $propostas->id;
        $histpropostas->data_historico = date('Y-m-d H:i:s');
        $histpropostas->estado_pedido_frotas_id = EstadoPedidoFrotas::AGUARDANDO; 
          $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $histpropostas->aprovador_frotas_id = $aprovador[0]->id;
        }
      //  $histpropostas->aprovador_frotas_id = TSession::getValue('iduser');
        $histpropostas->store();
    }
    public function onCilia($param = null)
    {
        TToast::show('info', 'Chamando onCilia...');
        TScript::create("window.open('https://sistema.cilia.com.br/users/sign_in', '_blank');");
    }

   

    public static function onVerificaValorVenal($pedido) 
    {
        TTransaction::open(self::$database);

        // Buscar valor venal do veículo
        $veiculo = new Veiculos($pedido->veiculos_id);
        $valor_venal = $veiculo->valor_tabela_fipe;
        $total_manutencao=0;

        // Buscar total de manutenções finalizadas
         $repository = new TRepository('PedidoFrotas'); 
  
        $criteria = new TCriteria;
        $criteria->add(new TFilter('veiculos_id', '=', $pedido->veiculos_id));
        $criteria->add(new TFilter('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::FINALIZADO));
        $pedidofrotas = $repository->load($criteria);
        if ($pedidofrotas) {
            foreach ($pedidofrotas as $ped) {
                $total_manutencao += $ped->valor_liquido_proposta;
            }
        }

    

        $total_manutencao += $pedido->valor_liquido_proposta;

        $limite = $valor_venal * 0.40;
        $ultrapassou_limite = ($total_manutencao >= $limite);

        TTransaction::close(); // isso precisa vir antes do return!

        return $ultrapassou_limite;
    }
    private function registrarHistoricoCotacaoAprovado($propostas)
    {
        $histpropostas = new PropostasHistorico();
        $histpropostas->propostas_id = $propostas->id;
        $histpropostas->data_historico = date('Y-m-d H:i:s');
        $histpropostas->estado_pedido_frotas_id = EstadoPedidoFrotas::PGTOAPROVADO;
        // Verifica se o usuário é um aprovador de frotas
        // e atribui o ID do aprovador ao histórico 
          $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $histpropostas->aprovador_frotas_id = $aprovador[0]->id;
        }
      //  $histpropostas->aprovador_frotas_id = TSession::getValue('iduser');
        $histpropostas->store();
    }

//  public function onExportHtml($param = null) 
//     {
//         try
//         {
//             $output = 'app/output/'.uniqid().'.pdf';

//             if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
//             {
//                 $this->limit = 0;
//                 $this->datagrid->prepareForPrinting();
//                 $this->onReload();

//                 $html = clone $this->datagrid;
//                 $contents = file_get_contents('app/resources/styles-print.html') . file_get_contents('app/resources/styles-print-list.html') . PdfListHeader::render(__CLASS__) . $html->getContents();

//                 $dompdf = new \Dompdf\Dompdf;
//                 $dompdf->loadHtml($contents);
//                 $dompdf->setPaper('A4', 'landscape');
//                 $dompdf->render();

//                 file_put_contents($output, $dompdf->output());

//                 $window = TWindow::create('PDF', 0.8, 0.8);
//                 $object = new TElement('iframe');
//                 $object->src  = $output;
//                 $object->type  = 'application/pdf';
//                 $object->style = "width: 100%; height:calc(100% - 10px)";

//                 $window->add($object);
//                 $window->show();
//             }
//             else
//             {
//                 throw new Exception(_t('Permission denied') . ': ' . $output);
//             }
//         }
//         catch (Exception $e) // in case of exception
//         {
//             new TMessage('error', $e->getMessage()); // shows the exception error message
//         }
//     }
//  public function onExportHtml($param = null)
// {
//     try
//     {
//         $output = 'app/output/' . uniqid() . '.html';

//         if ((!file_exists($output) && is_writable(dirname($output))) || is_writable($output))
//         {
//             $this->limit = 0;
//             $objects = $this->onReload();

//             if ($objects)
//             {
//                 TTransaction::open(self::$database);

//                // ✅ SOMENTE COLUNAS ATIVAS/IMPRIMÍVEIS (respeita disablePrinting do Adianti)
// $columns = array_values(array_filter($this->datagrid->getColumns(), function($col) {
//     if (method_exists($col, 'isPrintable')) {
//         return (bool) $col->isPrintable(); // <- chave do problema
//     }
//     return true;
// }));

//                 $datas = array_filter(array_map(fn($obj) => $obj->data_cotacao ?? null, $objects));
//                 $periodo_txt = !empty($datas)
//                     ? 'Período: ' . date('d/m/Y', strtotime(min($datas))) . ' a ' . date('d/m/Y', strtotime(max($datas)))
//                     : 'Período não informado';

//                 $html = '<html><head><meta charset="utf-8"><title>RelatorioPedidosFrotas</title>
//                 <style>
//                     body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
//                     .header { display: flex; align-items: center; margin-bottom: 20px; }
//                     .logo { width: 150px; }
//                     .title-block { flex: 1; text-align: center; }
//                     .title-block h1 { margin: 0; font-size: 18px; }
//                     .title-block h3 { margin: 0; font-weight: normal; font-size: 14px; }
//                     table.bordasimples { border-collapse: collapse; width: 100%; table-layout: auto; }
//                     table.bordasimples th, table.bordasimples td {
//                         border: 1px solid #646161;
//                         padding: 4px 6px;
//                         text-align: left;
//                         white-space: nowrap;
//                         width: auto;
//                         font-size: 11px;
//                     }
//                     table.bordasimples thead { background: #ccc; }
//                     tr.total { background: #eee; font-weight: bold; }
//                     .col-descricao { min-width: 200px; max-width: 400px; white-space: normal; }
//                     .col-valor_total_produto, .col-valor_total_servico { max-width: 120px; text-align: right; }
//                 </style>
//                 </head><body>';

//                 $html .= '<div class="header">
//                             <img src="app/images/logo.png" class="logo">
//                             <div class="title-block">
//                                 <h1>Relatório de Pedidos Frotas</h1>
//                                 <h3>'.$periodo_txt.'</h3>
//                             </div>
//                         </div>';

//                 $html .= '<table class="bordasimples"><thead><tr>';

//                 // ✅ usa SOMENTE colunas ativas
//                 foreach ($columns as $column)
//                 {
//                     $column_name = $column->getName();
//                     $html .= '<th class="col-' . $column_name . '">' . $column->getLabel() . '</th>';
//                 }

//                 $html .= '</tr></thead><tbody>';

//                 $totais = [
//                     'qtde' => 0,
//                     'valor' => 0,
//                     'perc_desconto' => 0,
//                     'valor_total' => 0,
//                 ];

//                 $campos_monetarios = ['valor','perc_desconto','valor_total'];

//                 foreach ($objects as $object)
//                 {
//                     $html .= '<tr>';

//                     // ✅ usa SOMENTE colunas ativas
//                     foreach ($columns as $column)
//                     {
//                         $column_name = $column->getName();
//                         $value = '';

//                         if (isset($object->$column_name))
//                         {
//                             $value = is_scalar($object->$column_name) ? $object->$column_name : '';

//                             if (preg_match('/^(dt_|data)/i', $column_name) && strtotime($value)) {
//                                 $value = date('d/m/Y', strtotime($value));
//                             }

//                             if ($column_name == 'tipo') {
//                                 $value = ($object->$column_name == 1) ? 'Produto' :
//                                         (($object->$column_name == 2) ? 'Serviço' : 'Outro');
//                             }

//                             if ($column_name == 'dt_finalizacao') {
//                                 $value = '';
//                             }

//                             if ($column_name == 'estado_pedido_frotas_id') {
//                                 try {
//                                     $estado = new EstadoPedidoFrotas($object->$column_name);
//                                     $value = $estado->nome;
//                                 } catch (Exception $e) {
//                                     $value = 'N/A';
//                                 }
//                             }
//                             elseif ($column_name == 'motorista_entrada_id' || $column_name == 'motorista_retirada_id') {
//                                 try {
//                                     $pessoa = new Pessoa($object->$column_name);
//                                     $value = $pessoa->nome;
//                                 } catch (Exception $e) {
//                                     $value = 'N/A';
//                                 }
//                             }
//                         }
//                         else if (method_exists($object, 'render'))
//                         {
//                             $col_render = (strpos((string)$column_name, '{') === FALSE) ? ('{' . $column_name . '}') : $column_name;
//                             $value = $object->render($col_render);
//                         }

//                         if (array_key_exists($column_name, $totais) && is_numeric($value)) {
//                             $totais[$column_name] += $value;
//                         }

//                         if (in_array($column_name, $campos_monetarios) && is_numeric($value)) {
//                             $value = 'R$ ' . number_format($value, 2, ',', '.');
//                         }
//                         elseif ($column_name === 'qtde' && is_numeric($value)) {
//                             $value = number_format($value, 0, ',', '.');
//                         }

//                         $html .= '<td class="col-' . $column_name . '">' . htmlspecialchars((string)$value) . '</td>';
//                     }

//                     $html .= '</tr>';
//                 }

//                 // ✅ total somente nas colunas ativas
//                 $html .= '<tr class="total">';
//                 foreach ($columns as $column)
//                 {
//                     $col_name = $column->getName();

//                     if (isset($totais[$col_name]))
//                     {
//                         if ($col_name === 'qtde') {
//                             $html .= '<td class="col-' . $col_name . '">' . number_format($totais[$col_name], 0, ',', '.') . '</td>';
//                         }
//                         elseif (in_array($col_name, $campos_monetarios)) {
//                             $html .= '<td class="col-' . $col_name . '">R$ ' . number_format($totais[$col_name], 2, ',', '.') . '</td>';
//                         }
//                         else {
//                             $html .= '<td class="col-' . $col_name . '">' . $totais[$col_name] . '</td>';
//                         }
//                     }
//                     else {
//                         $html .= '<td></td>';
//                     }
//                 }
//                 $html .= '</tr>';

//                 $html .= '</tbody></table>';

//                 $emissao = date('d/m/Y H:i');
//                 $html .= "<br><div style='font-size:14px; text-align:right; color:#555;'>
//                             Emitido no portal gestao.np3ebeneficios.com.br &nbsp;&nbsp; Data e Hora: {$emissao}
//                           </div>";

//                 $html .= '</body></html>';

//                 file_put_contents($output, $html);
//                 TTransaction::close();

//                 TPage::openFile($output);
//             }
//             else
//             {
//                 throw new Exception(_t('No records found'));
//             }
//         }
//         else
//         {
//             throw new Exception(_t('Permission denied') . ': ' . $output);
//         }
//     }
//     catch (Exception $e)
//     {
//         new TMessage('error', $e->getMessage());
//     }
// }


 public function onExportHtml($param = null)
{
    try
    {
        $output = 'app/output/' . uniqid() . '.html';

        if ((!file_exists($output) && is_writable(dirname($output))) || is_writable($output))
        {
            $this->limit = 0;
            $this->datagrid->prepareForPrinting();
            $this->onReload();

            $html = clone $this->datagrid;
            $css  = file_get_contents('app/resources/styles-print.html');

            $contents = "<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Relatório</title>
{$css}
</head>
<body>
{$html->getContents()}
</body>
</html>";

            file_put_contents($output, $contents);

            // abre direto na URL (mesma aba)
            TScript::create("window.location.href = '{$output}';");
        }
        else
        {
            throw new Exception(_t('Permission denied') . ': ' . $output);
        }
    }
    catch (Exception $e)
    {
        new TMessage('error', $e->getMessage());
    }
}


    private function executarImportacaoMYSQL()
    {
        try
        {
            TTransaction::open('minierp');

            $host ="localhost";
            $user = "gestaonp3benefic_regina";
            $password = "QJ6$@rAfdUbd70TG";
            $db="gestaonp3benefic_dbsystem_np3";

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            $mysqli = new mysqli($host, $user, $password, $db);
            $mysqli->set_charset('utf8mb4');

            // ✅ PREVIEW (não grava)
            $preview = [];
            $totalEncontrados = 0;
            $totalJaExistia = 0;

            // ⚠️ aqui tem um detalhe: você usou first() e depois foreach()
            // Para iterar várias unidades, use load()
            $units = SystemUnit::where('idold','in', [1461,3225,3602,3624,3815,3981028,3981069])->load();

            if ($units)
            {
                foreach ($units as $u)
                {
                    $pedidos = PedidoFrotas::where('system_unit_id','=',$u->id)->load();
                    

                    if ($pedidos)
                    {
                        foreach ($pedidos as $pedidosf)
                        {
                            if (empty($pedidosf->idold)) {
                                continue; // pula se idold for null ou vazio
                            }
                            // ⚠️ você tinha "SELECT SELECT" duplicado
                            $query = "
                                SELECT DISTINCT
                                    (select pf.id from gestaonp3benefic_dbgestao.pedido_frotas pf where pf.idold=ps.pedido_id limit 1) as pedido_frotas_id,
                                    (select pes.id from gestaonp3benefic_dbgestao.pessoa pes where pes.idold=c.id limit 1) as pessoa_id,
                                    c.empresa
                                FROM gestaonp3benefic_dbsystem_np3.pedido_as_cidade pc
                                INNER JOIN gestaonp3benefic_dbsystem_np3.pedido_as_seguimento ps
                                    ON ps.pedido_id = pc.pedido_id AND ps.status = 'on'
                                INNER JOIN gestaonp3benefic_dbsystem_np3.clientes c
                                    ON c.cidade_id = pc.cidade_id AND c.alto_gestao = 'Inativo'
                                INNER JOIN gestaonp3benefic_dbsystem_np3.cliente_has_seguimento cs
                                    ON cs.seguimento_id = ps.seguimento_id AND cs.status = 'on' AND cs.cliente_id = c.id
                                WHERE pc.pedido_id = {$pedidosf->idold}
                               ;
                            ";
                            
                            // var_dump($query); // debug: mostra a query gerada
                            // die();

                            $result = $mysqli->query($query);

                            if ($result && $result->num_rows > 0)
                            {
                                while ($row = $result->fetch_assoc())
                                {
                                    $totalEncontrados++;

                                    // pula se vier null
                                    if (empty($row['pedido_frotas_id']) || empty($row['pessoa_id'])) {
                                        $preview[] = [
                                            'pedido_idold' => $pedidosf->idold,
                                            'pedido_id'    => $pedidosf->id,
                                            'pedido_frotas_id_db' => $row['pedido_frotas_id'] ?? null,
                                            'pessoa_id'    => $row['pessoa_id'] ?? null,
                                            'empresa'      => $row['empresa'] ?? null,
                                            'status'       => 'PULADO (ids null)'
                                        ];
                                        continue;
                                    }

                                    $pedidoascliente = PedidoAsCliente::where('pedido_frotas_id','=',$pedidosf->id)
                                        ->where('pessoa_id','=',$row['pessoa_id'])
                                        ->first();

                                    if ($pedidoascliente) {
                                        $totalJaExistia++;
                                        $preview[] = [
                                            'pedido_idold' => $pedidosf->idold,
                                            'pedido_id'    => $pedidosf->id,
                                            'pessoa_id'    => $row['pessoa_id'],
                                            'empresa'      => $row['empresa'] ?? null,
                                            'status'       => 'JÁ EXISTE'
                                        ];
                                        continue;
                                    }

                                    // ✅ aqui seria o insert, mas vamos só listar
                                    $preview[] = [
                                        'pedido_idold' => $pedidosf->idold,
                                        'pedido_id'    => $pedidosf->id,
                                        'pessoa_id'    => $row['pessoa_id'],
                                        'empresa'      => $row['empresa'] ?? null,
                                        'status'       => 'INSERIRIA'
                                    ];

                                    
                                    // ✅ quando quiser gravar, descomenta:
                                    $pedidoascliente = new PedidoAsCliente();
                                    $pedidoascliente->pedido_frotas_id = $pedidosf->id;
                                    $pedidoascliente->pessoa_id = $row['pessoa_id'];
                                    $pedidoascliente->store();
                                    
                                }
                            }
                            else {
                                $preview[] = [
                                    'pedido_idold' => $pedidosf->idold,
                                    'pedido_id'    => $pedidosf->id,
                                    'pessoa_id'    => null,
                                    'empresa'      => null,
                                    'status'       => 'NENHUM CLIENTE ENCONTRADO'
                                ];
                            }

                            if ($result) {
                                $result->free();
                            }
                        }
                    }
                }
            }

            $mysqli->close();
            TTransaction::close();

            // ✅ Monta HTML pro popup
            $max = 240;
            $slice = array_slice($preview, 0, $max);

            $html = "<b>Preview pedido_as_cliente</b><br>";
            $html .= "Total linhas encontradas: <b>{$totalEncontrados}</b><br>";
            $html .= "Já existia: <b>{$totalJaExistia}</b><br>";
            $html .= "Mostrando: <b>".count($slice)."</b> de <b>".count($preview)."</b><br><br>";

            $html .= "<div style='max-height:420px; overflow:auto; font-family:monospace; font-size:12px;'>";
            foreach ($slice as $i => $r) {
                $html .= ($i+1).". "
                    ."pedido_idold={$r['pedido_idold']} | pedido_id={$r['pedido_id']} | pessoa_id={$r['pessoa_id']} | empresa={$r['empresa']} | {$r['status']}<br>";
            }
            if (count($preview) > $max) {
                $html .= "<br><b>... (+".(count($preview)-$max)." linhas não exibidas)</b>";
            }
            $html .= "</div>";

            new TMessage('info', $html);
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }





     public static function onExibirImportar($object = null)
    {
        try
        {
            return (TSession::getValue('iduser') == 1);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }

        return false;
    }

    public function onImportarMYSQL()
    {
        $form = new TForm('form_auth');
        $form->style = 'padding:20px';

        $senha = new TEntry('senha');
        $senha->setProperty('type', 'password');
        $senha->setSize('100%');

        $form->add(new TLabel('Senha'));
        $form->add($senha);
        $form->addField($senha);

        $action = new TAction([self::class, 'onValidarSenhaImportacao']);

        new TInputDialog(
            'Confirmação de Segurança',
            $form,
            $action,
            'Confirmar'
        );
    }
    public function onValidarSenhaImportacao($param)
    {
        try
        {
            if (empty($param['senha']))
            {
                throw new Exception('Informe a senha');
            }

            // 🔐 validação (exemplo)
            if ($param['senha'] !== '@codeg7')
            {
                throw new Exception('Senha incorreta');
            }

            // ✅ senha correta → executa importação
            $this->executarImportacaoMYSQL();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
       public function onImportarXLS($param = null) 
    {
        try
        {
            
          
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
  

            // $host ="localhost";
            // $user = "gestaonp3benefic_regina";
            // $password = "QJ6$@rAfdUbd70TG";
            // $db="gestaonp3benefic_dbsystem_np3";

    // private function executarImportacaoMYSQL_OLD()
    // {
    //     try
    //     {
    //         TTransaction::open('minierp');

    //         // ⚠️ Coloque isso no config/.env (não deixe credencial no código)
    //         // $host ="localhost";
    //         // $user = "gestaonp3benefic_regina";
    //         // $password = "QJ6$@rAfdUbd70TG";
    //         // $db="gestaonp3benefic_dbsystem_np3";

    //         $host     = "localhost";
    //         $user     = "root";
    //         $password = "";
    //         $db       = "dbsistema_np3";

    //         mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    //         $mysqli = new \mysqli($host, $user, $password, $db);
    //         $mysqli->set_charset('utf8mb4');

    //         // (opcional) melhora consistência de leitura
    //         // $mysqli->query("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");

    //         $clienteIds = "1461,3225,3602,3624,3815,3981028,3981069";

    //         $query = "
    //             SELECT *
    //             FROM dbsistema_np3.pedidos p
    //             WHERE p.cliente_id IN ($clienteIds)
    //         ";

    //         $result = $mysqli->query($query);

    //         if ($result && $result->num_rows > 0)
    //         {
    //             while ($row = $result->fetch_object())
    //             {
    //                 // Normaliza status
    //                 $status = trim(strtoupper($row->status ?? ''));

    //                 // Carrega mapeamentos (idold -> id novo)
    //                 $unit          = SystemUnit::where('idold','=', $row->cliente_id)->first();
    //                 $ent           = Entidade::where('idold','=', $row->cliente_id)->first();
    //                 $departamento  = DepartamentoUnit::where('idold','=', $row->departamento_id)->first();
    //                 $veiculos      = Veiculos::where('placa','=', $row->placa)->first();
    //                 $usuarioSistema = SystemUsers::where('idold','=', $row->usuario_id)->first();
    //                 $tipomanutencao = TipoManutencao::where('idold','=', $row->categoria_id)->first();

    //                 // Totais
    //                 $cliente_proposta_id   = null;
    //                 $totalgeralservico     = 0.0;
    //                 $totalgeralproduto     = 0.0;
    //                 $totalgeralproposta    = 0.0; // soma final (serv+prod)
    //                 $totalgeralsubproposta = 0.0; // soma subtotal
    //                 $totalgeraldesconto    = 0.0;

    //                 // Se aprovado, tenta buscar a proposta aprovada do pedido
    //                 $rowpropostaAprovada = null;

    //                 if ($status === 'APROVADO')
    //                 {
    //                     $queryprop = "
    //                         SELECT *
    //                         FROM dbsistema_np3.proposta_frotas prop
    //                         WHERE prop.status = 'APROVADO'
    //                         AND prop.pedido_id = {$row->id_pedido}
    //                         LIMIT 1
    //                     ";

    //                     $resultproposta = $mysqli->query($queryprop);

    //                     if ($resultproposta && $resultproposta->num_rows > 0)
    //                     {
    //                         $rowpropostaAprovada = $resultproposta->fetch_object();
    //                         $cliente_proposta_id = $rowpropostaAprovada->cliente_id ?? null;

    //                         // Soma itens por tipo (1=serviço, 2=produto)
    //                         $querySomatorio = "
    //                             SELECT
    //                                 ip.tipo,
    //                                 SUM(ip.subtotal)   AS subtotal,
    //                                 SUM(ip.valortotal) AS valortotal
    //                             FROM dbsistema_np3.itens_proposta_frotas ip
    //                             WHERE ip.pedido_id   = {$rowpropostaAprovada->pedido_id}
    //                             AND ip.proposta_id = {$rowpropostaAprovada->id_proposta}
    //                             GROUP BY ip.tipo
    //                         ";

    //                         $resultSomatorio = $mysqli->query($querySomatorio);

    //                         if ($resultSomatorio && $resultSomatorio->num_rows > 0)
    //                         {
    //                             while ($r = $resultSomatorio->fetch_object())
    //                             {
    //                                 $sub = (float) ($r->subtotal ?? 0);
    //                                 $vt  = (float) ($r->valortotal ?? 0);

    //                                 if ((string)$r->tipo === '2')
    //                                 {
    //                                     $totalgeralproduto = $vt;
    //                                 }
    //                                 elseif ((string)$r->tipo === '1')
    //                                 {
    //                                     $totalgeralservico = $vt;
    //                                 }

    //                                 $totalgeralsubproposta += $sub;
    //                                 $totalgeraldesconto    += ($sub - $vt);
    //                             }

    //                             $totalgeralproposta = $totalgeralservico + $totalgeralproduto;
    //                         }
    //                     }
    //                 }

    //                 // Pessoa (estabelecimento) vem do cliente da proposta aprovada (quando existir)
    //                 $pessoaEstab = null;

    //                 if (!empty($cliente_proposta_id))
    //                 {
    //                     $pessoaEstab = Pessoa::where('idold','=', $cliente_proposta_id)->first();
    //                 }

    //                 // Define estado do pedido
    //                 $estado_pedido_frotas_id = null;

    //                 if ($status === 'APROVADO')
    //                 {
    //                     $estado_pedido_frotas_id = EstadoPedidoFrotas::APROVADO;
    //                 }
    //                 elseif ($status === 'PENDENTE')
    //                 {
    //                     $estado_pedido_frotas_id = EstadoPedidoFrotas::PENDENTE;
    //                 }
    //                 elseif ($status === 'COTACAO')
    //                 {
    //                     $estado_pedido_frotas_id = EstadoPedidoFrotas::COMPROPOSTA;
    //                 }

    //                 // -------------------------
    //                 // SALVA PEDIDO FROTAS
    //                 // -------------------------
    //                 $pedido_frotas = new PedidoFrotas();
    //                 $pedido_frotas->veiculos_id             = $veiculos->id ?? null;
    //                 $pedido_frotas->estado_pedido_frotas_id = $estado_pedido_frotas_id;
    //                 $pedido_frotas->system_unit_id          = $unit->id ?? null;
    //                 $pedido_frotas->departamento_unit_id    = $departamento->id ?? null;
    //                 $pedido_frotas->entidade_id             = $ent->id ?? null;
    //                 $pedido_frotas->estabelecimento_id      = $pessoaEstab->id ?? null;

    //                 $pedido_frotas->descricao = $row->servico ?? null;
    //                 $pedido_frotas->km        = $row->km ?? null;
    //                 $pedido_frotas->obs       = $row->historico ?? null;

    //                 $pedido_frotas->data_aprovacao   = $row->data_aprovacao ?? null;
    //                 $pedido_frotas->dt_finalizacao  = $row->data_finalizacao ?? null;

    //                 $pedido_frotas->system_users_id = $usuarioSistema->id ?? null;

    //                 // cuidado com dataVisual nula
    //                 if (!empty($row->dataVisual))
    //                 {
    //                     $pedido_frotas->mes = date('m', strtotime($row->dataVisual));
    //                     $pedido_frotas->ano = date('Y', strtotime($row->dataVisual));
    //                 }

    //                 $pedido_frotas->valor_total            = $totalgeralsubproposta;
    //                 $pedido_frotas->valor_total_proposta   = $totalgeralsubproposta;
    //                 $pedido_frotas->valor_liquido_proposta = $totalgeralproposta;
    //                 $pedido_frotas->valor_desconto         = $totalgeraldesconto;

    //                 $pedido_frotas->data_limite_resposta = $row->dataVisual ?? null;
    //                 $pedido_frotas->tipo_manutencao_id   = $tipomanutencao->id ?? null;

    //                 // OLD
    //                 $pedido_frotas->idold                = $row->id_pedido ?? null;
    //                 $pedido_frotas->motorista_idold      = $row->motorista_id ?? null;
    //                 $pedido_frotas->motorista_old        = $row->motorista ?? null;

    //                 $pedido_frotas->status_old           = $row->status ?? null;
    //                 $pedido_frotas->enviar_old           = $row->enviar ?? null;

    //                 $pedido_frotas->system_users_idold   = $row->usuario_id ?? null;
    //                 $pedido_frotas->usuarioaprovou_old   = $row->usuario_aprovou ?? null;
    //                 $pedido_frotas->usuariocliente_idold = $row->usuariocliente_id ?? null;

    //                 $pedido_frotas->ip_old               = $row->ip ?? null;
    //                 $pedido_frotas->usuario_old          = $row->usuario ?? null;

    //                 $pedido_frotas->store();

    //                 // Histórico do pedido
    //                 $pedido_frotas_historico = new PedidoFrotasHistorico();
    //                 $pedido_frotas_historico->pedido_frotas_id        = $pedido_frotas->id;
    //                 $pedido_frotas_historico->data_operacao           = date('Y-m-d H:i:s');
    //                 $pedido_frotas_historico->estado_pedido_frotas_id = $pedido_frotas->estado_pedido_frotas_id;

    //                 $aprovador = AprovadorFrotas::where('system_users_id','=', TSession::getValue('userid'))->load();

    //                 if ($aprovador)
    //                 {
    //                     $pedido_frotas_historico->aprovador_frotas_id = $aprovador[0]->id;
    //                 }

    //                 $pedido_frotas_historico->store();

    //                 // -------------------------
    //                 // ITENS DO PEDIDO
    //                 // -------------------------
    //                 $queryitempedido = "
    //                     SELECT *
    //                     FROM dbsistema_np3.item ip
    //                     WHERE ip.pedido_id = {$row->id_pedido}
    //                 ";

    //                 $resultitempedido = $mysqli->query($queryitempedido);

    //                 if ($resultitempedido && $resultitempedido->num_rows > 0)
    //                 {
    //                     while ($rowitempedido = $resultitempedido->fetch_object())
    //                     {
    //                         $itens_pedido = new ItensPedidoFrotas();
    //                         $itens_pedido->pedido_frotas_id = $pedido_frotas->id;

    //                         // mantém como veio (1/2)
    //                         $itens_pedido->tipo = (string)($rowitempedido->tipo ?? null);

    //                         $produto = null;

    //                         if (!empty($rowitempedido->nome))
    //                         {
    //                             $produto = Produto::where('nome','=', $rowitempedido->nome)->first();
    //                         }

    //                         $itens_pedido->descricao  = $rowitempedido->nome ?? null;
    //                         $itens_pedido->qtde       = $rowitempedido->qtd ?? null;
    //                         $itens_pedido->produto_id = $produto->id ?? null;

    //                         // OLD
    //                         $itens_pedido->idold        = $rowitempedido->id_item ?? null;
    //                         $itens_pedido->pedido_idold = $rowitempedido->pedido_id ?? null;
    //                         $itens_pedido->tipo_old     = $rowitempedido->tipo ?? null;

    //                         $itens_pedido->store();
    //                     }
    //                 }

    //                 // -------------------------
    //                 // PROPOSTAS DO PEDIDO
    //                 // -------------------------
    //                 $queryproposta = "
    //                     SELECT *
    //                     FROM dbsistema_np3.proposta_frotas prop
    //                     WHERE prop.pedido_id = {$row->id_pedido}
    //                 ";

    //                 $resultproposta = $mysqli->query($queryproposta);

    //                 if ($resultproposta && $resultproposta->num_rows > 0)
    //                 {
    //                     while ($rowproposta = $resultproposta->fetch_object())
    //                     {
    //                         $pessoa = Pessoa::where('idold','=', $rowproposta->cliente_id)->first();

    //                         $proposta_frotas = new Propostas();
    //                         $proposta_frotas->pedido_frotas_id = $pedido_frotas->id;
    //                         $proposta_frotas->pessoa_id        = $pessoa->id ?? null;

    //                         $cidade = null;

    //                         if (!empty($pessoa->id))
    //                         {
    //                             $cidade = PessoaEndereco::where('pessoa_id','=', $pessoa->id)->first();
    //                         }

    //                         $proposta_frotas->cidade_id = $cidade->cidade_id ?? null;

    //                         // estado da proposta
    //                         $statusProp = trim(strtoupper($rowproposta->status ?? ''));

    //                         switch ($statusProp)
    //                         {
    //                             case 'APROVADO':
    //                                 $proposta_frotas->estado_pedido_frotas_id = EstadoPedidoFrotas::APROVADO;
    //                                 break;

    //                             case 'PENDENTE':
    //                                 $proposta_frotas->estado_pedido_frotas_id = EstadoPedidoFrotas::PENDENTE;
    //                                 break;

    //                             case 'COTACAO':
    //                                 $proposta_frotas->estado_pedido_frotas_id = EstadoPedidoFrotas::COMPROPOSTA;
    //                                 break;

    //                             case 'REJEITADA':
    //                                 $proposta_frotas->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
    //                                 break;

    //                             default:
    //                                 $proposta_frotas->estado_pedido_frotas_id = null;
    //                                 break;
    //                         }

    //                         // condutor
    //                         $pessoacondutor = null;

    //                         if (!empty($row->motorista_id))
    //                         {
    //                             $pessoacondutor = Pessoa::where('idold','=', $row->motorista_id)->first();
    //                         }

    //                         $proposta_frotas->motorista_entrada_id  = $pessoacondutor->id ?? null;
    //                         $proposta_frotas->motorista_retirada_id = $pessoacondutor->id ?? null;

    //                         $proposta_frotas->data_cotacao = $rowproposta->dataVisual ?? null;
    //                         $proposta_frotas->obs          = $rowproposta->historico ?? null;

    //                         // Totais por tipo
    //                         $totalgeralsemdesconto        = 0.0;
    //                         $totalgeralcomdesconto        = 0.0;
    //                         $totalgeralsemdescontoproduto = 0.0;
    //                         $totalgeralcomdescontoproduto = 0.0;
    //                         $totalgeralsemdescontoservico = 0.0;
    //                         $totalgeralcomdescontoservico = 0.0;

    //                         $querysomaitens = "
    //                             SELECT
    //                                 ip.tipo,
    //                                 SUM(ip.subtotal)   AS total_sem,
    //                                 SUM(ip.valortotal) AS total_com
    //                             FROM dbsistema_np3.itens_proposta_frotas ip
    //                             WHERE ip.pedido_id   = {$rowproposta->pedido_id}
    //                             AND ip.proposta_id = {$rowproposta->id_proposta}
    //                             GROUP BY ip.tipo
    //                         ";

    //                         $resultsomaitens = $mysqli->query($querysomaitens);

    //                         if ($resultsomaitens && $resultsomaitens->num_rows > 0)
    //                         {
    //                             while ($soma = $resultsomaitens->fetch_object())
    //                             {
    //                                 $sem = (float)($soma->total_sem ?? 0);
    //                                 $com = (float)($soma->total_com ?? 0);

    //                                 $totalgeralsemdesconto += $sem;
    //                                 $totalgeralcomdesconto += $com;

    //                                 if ((string)$soma->tipo === '2')
    //                                 {
    //                                     $totalgeralsemdescontoproduto = $sem;
    //                                     $totalgeralcomdescontoproduto = $com;
    //                                 }
    //                                 elseif ((string)$soma->tipo === '1')
    //                                 {
    //                                     $totalgeralsemdescontoservico = $sem;
    //                                     $totalgeralcomdescontoservico = $com;
    //                                 }
    //                             }
    //                         }

    //                         $proposta_frotas->valor_total    = $totalgeralsemdesconto;
    //                         $proposta_frotas->valor_desconto = ($totalgeralsemdesconto - $totalgeralcomdesconto);
    //                         $proposta_frotas->valor_liquido  = $totalgeralcomdesconto;

    //                         $proposta_frotas->total_produtos_sem_desconto = $totalgeralsemdescontoproduto;
    //                         $proposta_frotas->total_produtos_com_desconto = $totalgeralcomdescontoproduto;

    //                         $proposta_frotas->total_servicos_sem_desconto = $totalgeralsemdescontoservico;
    //                         $proposta_frotas->total_servicos_com_desconto = $totalgeralcomdescontoservico;

    //                         $proposta_frotas->total_geral_com_desconto = $totalgeralcomdesconto;

    //                         $proposta_frotas->system_users_id      = $usuarioSistema->id ?? null;
    //                         $proposta_frotas->system_unit_id       = $unit->id ?? null;
    //                         $proposta_frotas->departamento_unit_id = $departamento->id ?? null;

    //                         $proposta_frotas->datahora_inicioservico = $rowproposta->previsao_inicio_obra ?? null;
    //                         $proposta_frotas->datahora_fimservico    = $rowproposta->previsao_fim_obra ?? null;
    //                         $proposta_frotas->data_previsao_entrega  = $rowproposta->data_ser_finalizado ?? null;
    //                         $proposta_frotas->responsavel_tecnico    = $rowproposta->usuario_inicio_obra ?? null;

    //                         // OLD
    //                         $proposta_frotas->idold                  = $rowproposta->propostas_id ?? null;
    //                         $proposta_frotas->pedido_idold           = $rowproposta->pedido_id ?? null;
    //                         $proposta_frotas->status_old             = $rowproposta->status ?? null;
    //                         $proposta_frotas->cliente_idold          = $rowproposta->cliente_id ?? null;
    //                         $proposta_frotas->data_pag_autorizadoold = $rowproposta->data_pag_autorizado ?? null;

    //                         $proposta_frotas->store();

    //                         // Histórico da proposta
    //                         $propostas_historico = new PropostasHistorico();
    //                         $propostas_historico->propostas_id           = $proposta_frotas->id;
    //                         $propostas_historico->data_historico         = date('Y-m-d H:i:s');
    //                         $propostas_historico->estado_pedido_frotas_id = $proposta_frotas->estado_pedido_frotas_id;

    //                         $aprovador = AprovadorFrotas::where('system_users_id','=', TSession::getValue('userid'))->load();

    //                         if ($aprovador)
    //                         {
    //                             $propostas_historico->aprovador_frotas_id = $aprovador[0]->id;
    //                         }

    //                         $propostas_historico->store();

    //                         // Itens da proposta
    //                         $queryitemproposta = "
    //                             SELECT *
    //                             FROM dbsistema_np3.itens_proposta_frotas ip
    //                             WHERE ip.proposta_id = {$rowproposta->id_proposta}
    //                         ";

    //                         $resultitemproposta = $mysqli->query($queryitemproposta);

    //                         if ($resultitemproposta && $resultitemproposta->num_rows > 0)
    //                         {
    //                             while ($rowitemproposta = $resultitemproposta->fetch_object())
    //                             {
    //                                 $itens_proposta = new ItensPropostas();
    //                                 $itens_proposta->propostas_id = $proposta_frotas->id;

    //                                 // mantém como veio (1/2)
    //                                 $itens_proposta->tipo = (string)($rowitemproposta->tipo ?? null);

    //                                 $produto = null;

    //                                 if (!empty($rowitemproposta->nome))
    //                                 {
    //                                     $produto = Produto::where('nome','=', $rowitemproposta->nome)->first();
    //                                 }

    //                                 $itens_proposta->descricao     = $rowitemproposta->nome ?? null;
    //                                 $itens_proposta->qtde          = $rowitemproposta->qtd ?? null;
    //                                 $itens_proposta->valor         = $rowitemproposta->subtotal ?? null;
    //                                 $itens_proposta->valortotal    = $rowitemproposta->valortotal ?? null;
    //                                 $itens_proposta->perc_desconto = $rowitemproposta->desconto ?? null;
    //                                 $itens_proposta->produto_id    = $produto->id ?? null;

    //                                 // vincula ao item do pedido pelo idold do item antigo (se existir)
    //                                 $itens = null;

    //                                 if (!empty($rowitemproposta->item_id))
    //                                 {
    //                                     $itens = ItensPedidoFrotas::where('idold','=', $rowitemproposta->item_id)
    //                                         ->where('pedido_frotas_id','=', $pedido_frotas->id)
    //                                         ->first();
    //                                 }

    //                                 $itens_proposta->itens_pedido_frotas_id = $itens->id ?? null;

    //                                 // OLD
    //                                 $itens_proposta->idold           = $rowitemproposta->id_itens_propostas ?? null;
    //                                 $itens_proposta->pedido_idold    = $rowitemproposta->pedido_id ?? null;
    //                                 $itens_proposta->propostas_idold = $rowitemproposta->propostas_id ?? null;
    //                                 $itens_proposta->tipo_old        = $rowitemproposta->tipo ?? null;
    //                                 $itens_proposta->item_idold      = $rowitemproposta->item_id ?? null;

    //                                 $itens_proposta->store();
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         // ✅ importante (senão “nunca termina”/fica transação aberta)
    //         TTransaction::close();
    //     }
    //     catch (\Throwable $e)
    //     {
    //         TTransaction::rollback();
    //         throw $e;
    //     }
    // }

    
}
