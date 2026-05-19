<?php

use Adianti\Database\TTransaction;
use app\service\APICombustivel2;

class PedidoFrotasAbastecimentoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
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
        $download   = "download.php?file=app/manual/pedido-frotas-abastecimento-list.pdf&basename={$basename}";

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
        $this->form->setFormTitle("Listagem Pedido Frotas Abastecimento{$manual}");
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
        $criteria_estabelecimento_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE  deleted_at is null AND grupo_pessoa_id  = '{$filterVar}')")); 
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
        $estabelecimento_id = new TDBCombo('estabelecimento_id', 'minierp', 'Pessoa', 'id', '{nome}- CNPJ: {documento}','nome asc' , $criteria_estabelecimento_id );
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
        $estabelecimento_id->enableSearch();
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

        $row5 = $this->form->addFields([new TLabel("Estabelecimento:", null, '14px', null, '100%'),$estabelecimento_id],[]);
        $row5->layout = ['col-sm-12'];

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
        $column_veiculos_id_transformed = new TDataGridColumn('veiculos->placa', "Placa", 'center', '300px');
        $column_estabelecimento_nome = new TDataGridColumn('estabelecimento->nome', "Estabelecimento", 'center', '300px');
        $column_departamento_unit_name = new TDataGridColumn('departamento_unit->name', "Departamento", 'center', '300px');
        $column_dt_pedido_transformed = new TDataGridColumn('dt_pedido', "Dt Pedido", 'center', '300px');
        $column_dt_finalizacao_transformed = new TDataGridColumn('dt_finalizacao', "Dt Finalização", 'center', '300px');
        $column_tipo_manutencao_id = new TDataGridColumn('tipo_manutencao->descricao', "Tipo Manutenção", 'center', '300px');
        $column_valor_liquido_proposta = new TDataGridColumn('valor_liquido_proposta', "Vl líquido proposta", 'center', '300px');
        $column_estado_pedido_frotas_id_transformed = new TDataGridColumn('estado_pedido_frotas_id', "Estado Pedido", 'center', '300px');
        $column_system_users_name = new TDataGridColumn('system_users->name', "Usuário", 'center', '300px');
        $column_orcamento_base = new TDataGridColumn('orcamento_base', "Orçamento base", 'center', '300px');
        $column_numerodispositivo_transformed = new TDataGridColumn('numero_dispositivo', "Número Dispositivo", 'center', '300px');
        $column_tipocombustivel_id_transformed = new TDataGridColumn('tipo_combustivel_id', "Combustivel", 'center', '300px');
        $column_qtde_liquido_proposta = new TDataGridColumn('qtde_liquido_proposta', "Qtde líquido proposta", 'center', '300px');
        $column_qtde_liquido_proposta->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            
            return '15.33';
        });
        $column_numerodispositivo_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            
            return '4108 6335 0607 9901';
        });
          $column_tipocombustivel_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            
            return 'Gasolina';
        });

        //    $column_cidade_id_transformed = new TDataGridColumn('cidade_id', "Cidade", 'center', '300px');
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
            $temnotafiscal = false;

            if ($object->estado_pedido_frotas_id==EstadoPedidoFrotas::FINALIZADO || $object->estado_pedido_frotas_id==EstadoPedidoFrotas::APROVADO || $object->estado_pedido_frotas_id==EstadoPedidoFrotas::PGTOAPROVADO || $object->estado_pedido_frotas_id==EstadoPedidoFrotas::ENTREGUE) {
                TTransaction::open('minierp');

                $cot = Propostas::where('pedido_frotas_id','=',$object->id)
                                ->load();

                if ($cot)
                {
                    foreach ($cot as $cots) {
                        $doccot = DocumentosPropostas::where('propostas_id','=',$cots->id)
                                                   ->load();
                        if ($doccot){
                            $temnotafiscal = true;
                        }
                    }
                }

                TTransaction::close();
            }
            $revisao = '';
            if (TSession::getValue('testar_revisao')==1) {            
                //entrou em revisão
                $revisao = '';
                if ($object->estado_pedido_frotas1_id !== null) {
                    $estadorevisao = new EstadoPedidoFrotas($object->estado_pedido_frotas1_id);
                    $revisao = "<span style='font-size: 10px; font-style: italic; color: #eee;'>({$estadorevisao->nome})</span>";
                }
            }
            if ($temnotafiscal) {
                $anexo = $object->estado_pedido_frotas->nome . " <i class='fa fa-paperclip' aria-hidden='true'></i>";
            } else {
                $anexo = $object->estado_pedido_frotas->nome;
            }

            return "<span class='label label-default' style='width:260px; background-color:{$object->estado_pedido_frotas->cor}; display:inline-block;'> {$anexo} {$revisao} </span>";

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
        $this->datagrid->addColumn($column_dt_pedido_transformed);
        $this->datagrid->addColumn($column_departamento_unit_name);
        // $this->datagrid->addColumn($column_descricaopedido);
        $this->datagrid->addColumn($column_veiculos_id_transformed);
        $this->datagrid->addColumn($column_numerodispositivo_transformed);
        $this->datagrid->addColumn($column_tipocombustivel_id_transformed);
        $this->datagrid->addColumn($column_estabelecimento_nome);
        $this->datagrid->addColumn($column_dt_finalizacao_transformed);
        $this->datagrid->addColumn($column_tipo_manutencao_id);
        $this->datagrid->addColumn($column_valor_liquido_proposta);
        $this->datagrid->addColumn($column_qtde_liquido_proposta);
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
    //    $action4 = new TDataGridAction([$this, 'onImprimePedido'],   ['key' => '{id}']);
        $action5 = new TDataGridAction([$this, 'onEnviarCotacao'],   ['id' => '{id}']);
     //   $action61 = new TDataGridAction(['PropostaPendenteList', 'onSetProject'],   ['id' => '{id}']);
     //   $action6 = new TDataGridAction(['PropostaPendenteList', 'onSetProject'],   ['id' => '{id}']);
   //     $action7 = new TDataGridAction(['PropostaPendenteList', 'onSetProject'],   ['id' => '{id}']);
        $action8 = new TDataGridAction(['TStatusPedidoCancelar', 'onEdit'],   ['id' => '{id}']);
        if (TSession::getValue('aprovacao_por_item')==2) {
            $action9 = new TDataGridAction(['PedidoFrotasList', 'onGerarFinanceiro'],   ['id' => '{id}']);
        } else {
            $action9 = new TDataGridAction(['PedidoFrotasList', 'onGerarFinanceiroItem'],   ['id' => '{id}']);
        }
        $action10 = new TDataGridAction([$this, 'onFinalizarPedido'],   ['id' => '{id}']);
        $action11 = new TDataGridAction(['DocumentosPropostasSimpleList', 'onSetProject'],   ['id' => '{id}']);
     //   $action111 = new TDataGridAction(['DocumentosPropostasSimpleList', 'onSetProject'],   ['id' => '{id}']);
        $action12 = new TDataGridAction([$this, 'onCancelarAprovacao'],   ['id' => '{id}']);
   //     $action13 = new TDataGridAction(['PedidoFrotasList', 'onImprimir'],   ['id' => '{id}']);

        $action14 = new TDataGridAction(['AutorizarPedidoList', 'onSetProject'],   ['id' => '{id}']);
        $action15 = new TDataGridAction(['RetiradaVeiculo', 'onShow'],   ['id' => '{id}']);
        $action16 = new TDataGridAction([$this, 'onExibirDetalhe'],   ['id' => '{id}']);
 
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

        $action9->setLabel('Gerar financeiro');
        $action9->setImage('fas:money-bill-wave #FFA500');
       $action9->setDisplayCondition('PedidoFrotasList::onExibirGerarFinanceiro');

        $action10->setLabel('Finalizar pedido');
        $action10->setImage('fas:door-closed #009688');
        $action10->setDisplayCondition('PedidoFrotasList::onExibirFinalizar');

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

        $panel = new TPanelGroup("Listagem Pedido Frotas Abastecimento{$manual}");
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

        


        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['PedidoFrotasList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['PedidoFrotasList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF RELATÓRIO", new TAction(['PedidoFrotasList', 'onExportPdfRel'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['PedidoFrotasList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );
        $dropdown_button_exportar->addPostAction( "PDF/HTML", new TAction(['PedidoFrotasList', 'onExportHtml'],['static' => 1]), 'datagrid_'.self::$formName, 'fab:html5 #E34F26'  );

       
        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($button_atualizar);


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
            $dataform       = $this->form->getData();

            //code here
            $pdf = new FPDF("L","pt","A4");

            
            $repository = new TRepository('ViewPedidoFrotasPropostas'); // creates a repository
            $limit = 999999999999;

            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('order', 'dt_pedido');
            $criteria->setProperty('limit', $limit);  

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       

                }
            }
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

        $criteriaproposta = new TCriteria();

        $filters = TSession::getValue(__CLASS__.'_filters');

                if ($filters) {
                    foreach ($filters as $f) {
                        $criteriaproposta->add($f);
                    }
                    $xxxnome ='';
                    if (!empty($criteriaproposta)) {
                        $repository = new TRepository('ViewPropostas'); // creates a repository
                        $criteriaproposta->add(new TFilter('pedido_frotas_id', '=', $object->id), TExpression::AND_OPERATOR);
                        $criteriaproposta->add(new TFilter('estado_pedido_frotas_id', '=', $object->estado_pedido_frotas_id), TExpression::AND_OPERATOR);
                        $proposta = $repository->load($criteriaproposta);
                        $pessoa = new Pessoa($proposta[0]->estabelecimento_id);
                        $xxxnome = $pessoa->nome;
                    }
                } else {

                    $proposta = Propostas::where('pedido_frotas_id', '=', $object->id)
                                         ->where('estado_pedido_frotas_id', '=', $object->estado_pedido_frotas_id)
                                         ->first();
                    $pessoa = new Pessoa($proposta->pessoa_id);
                    $xxxnome = $pessoa->nome;
                }
               $pdf->SetXY(85,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding(substr($xxxnome,0,36), 'ISO-8859-1', 'UTF-8'),0,1,'L');

               $xxdescricaopedido = $object->descricaopedido ?? '';
               $pdf->SetXY(225,$alturalinha);
               $pdf->Cell(70,5,mb_convert_encoding(substr($xxdescricaopedido,0,60), 'ISO-8859-1', 'UTF-8'),0,1,'L');

               $estadopedido = new EstadoPedidoFrotas($object->estado_pedido_frotas_id);
               $pdf->SetXY(442,$alturalinha);
               $pdf->Cell(70,5,$estadopedido->nome,0,1,'L');

               $pdf->SetXY(455,$alturalinha);
               $pdf->Cell(70,5,number_format($object->valor_total, 2),0,1,'R');

               $xxvalor_desconto_proposta = $object->valor_desconto_proposta ?? 0;
               $pdf->SetXY(500,$alturalinha);
               $pdf->Cell(70,5,number_format($xxvalor_desconto_proposta, 2),0,1,'R');

               $xxvalor_liquido_proposta = $object->valor_liquido_proposta ?? 0;
               $pdf->SetXY(545,$alturalinha);
               $pdf->Cell(70,5,number_format($xxvalor_liquido_proposta, 2),0,1,'R');

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
               $vltotal += $object->valor_total;
               $vltotaldesconto += $object->valor_desconto_proposta;
               $vltotalcotacao += $object->valor_liquido_proposta;

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
        $pdf->SetXY(330,8);
        $pdf->Cell(70,5,mb_convert_encoding('Relatório de pedidos de frotas', 'ISO-8859-1', 'UTF-8'));
        $pdf->SetXY(660,8);
        $pdf->Cell(70,5,'Hora: '.date("H:i:s"),0,1,'C');
        $pdf->SetXY(748,8);
        $pdf->Cell(70,5,'Data: '.date("d/m/Y"),0,1,'C');
        $pdf->Ln(4);

        $pdf->SetXY(58,20);
        $pdf->Cell(70,5,$cnpj.'      '. $label,0,1,'L');
        $pdf->SetXY(115,20);
        $pdf->Cell(70,5,'',0,1,'L');
        $pdf->SetXY(748,20);
        $pdf->Cell(70,5,utf8_decode(' Página: ').$pag,0,1,'R');
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
        $pdf->Cell(100,5,utf8_decode('Descrição do pedido'),0,1,'L');

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

        if (isset($data->estado_pedido_frotas_id) AND ( (is_scalar($data->estado_pedido_frotas_id) AND $data->estado_pedido_frotas_id !== '') OR (is_array($data->estado_pedido_frotas_id) AND (!empty($data->estado_pedido_frotas_id)) )) )
        {

            $filters[] = new TFilter('estado_pedido_frotas_id', 'in', $data->estado_pedido_frotas_id);// create the filter 
        }

        if (isset($data->veiculos_id) AND ( (is_scalar($data->veiculos_id) AND $data->veiculos_id !== '') OR (is_array($data->veiculos_id) AND (!empty($data->veiculos_id)) )) )
        {

            $filters[] = new TFilter('veiculos_id', '=', $data->veiculos_id);// create the filter 
        }

        if (isset($data->estabelecimento_id) AND ( (is_scalar($data->estabelecimento_id) AND $data->estabelecimento_id !== '') OR (is_array($data->estabelecimento_id) AND (!empty($data->estabelecimento_id)) )) )
        {

            $filters[] = new TFilter('estabelecimento_id', '=', $data->estabelecimento_id);// create the filter 
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
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
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

            if (TSession::getValue('abastecimento') == '1') {
                $criteria->add(new TFilter('abastecimento', '=', 1));
            }

            // Sempre aplica o filtro da unidade do sistema
            $criteria->add(new TFilter('system_unit_id', '=', $idunit));

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
 
    public function onShow($param = null)
    {
            if (TSession::getValue('abastecimento') == 1) {
                 TTransaction::open('minierp');
                $administradora = new Administradora(2);
                $token = APICombustivel2::getPegarToken(
                    (string) $administradora->cd_grupo,
                    (string) $administradora->de_login_usu,
                    (string) $administradora->de_senha_usu
                );
                 $pessoa = Pessoa::where('system_unit_id', '=', TSession::getValue('idunit'))
                        ->where('id', 'in', '(select pessoa_id from pessoa_grupo where grupo_pessoa_id in(5,9))')
                        ->load();

                $lojasSincronizadas = [];
                $cachePessoasLoja = [];

                if ($pessoa) {
                    foreach ($pessoa as $obj) { 
                        $cpf = preg_replace('/[^0-9]/', '', $obj->cpf);
                        //$cpf = $obj->cpf;
                        $resultado = APICombustivel2::getApp_ListarContasPorCPF($token, 
                                    (string) $administradora->cd_grupo,
                                    (string) $administradora->de_login_usu,
                                    (string) $administradora->de_senha_usu,
                                    (string) $cpf);
                        APICombustivel2::debugLog('PedidoFrotasAbastecimentoList.onShow.resultado_np3', [
                            'cpf' => $cpf,
                            'resultado' => $resultado,
                        ]);
                        $lojasSincronizadas += $this->sincronizarLojasCredenciadasNp3($resultado, $cachePessoasLoja);
                        //var_dump($resultado, $cpf);    
                        //verificação cnpj da endp se existe em pessoa, se não cadastrar. testar
                        
                    }
                }
                TSession::setValue('abastecimento_lojas_credenciadas_ids', $lojasSincronizadas);
                TTransaction::close();
            }   
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

    private function sincronizarLojasCredenciadasNp3(array $resultado, array &$cachePessoasLoja = []): array
    {
        $lojasSincronizadas = [];

        foreach (($resultado['contas'] ?? []) as $conta) {
            APICombustivel2::debugLog('PedidoFrotasAbastecimentoList.sincronizarLojas.conta', [
                'cartao' => $conta['cartao'] ?? null,
                'rede' => $conta['rede'] ?? null,
                'cd_cliente' => $conta['cd_cliente'] ?? null,
                'qtd_lojas' => count($conta['lojas_credenciadas'] ?? []),
                'erro_lojas' => $conta['lojas_credenciadas_erro'] ?? null,
            ]);

            foreach (($conta['lojas_credenciadas'] ?? []) as $loja) {
                $pessoaId = $this->sincronizarPessoaLojaNp3($loja, $cachePessoasLoja);

                if (!empty($loja['cnpj']) && $pessoaId) {
                    $lojasSincronizadas[$loja['cnpj']] = $pessoaId;
                }
            }
        }

        return $lojasSincronizadas;
    }

    private function sincronizarPessoaLojaNp3(array $loja, array &$cachePessoasLoja = []): ?int
    {
        $cnpj = preg_replace('/\D+/', '', (string) ($loja['cnpj'] ?? ''));
        if (strlen($cnpj) !== 14) {
            APICombustivel2::debugLog('PedidoFrotasAbastecimentoList.sincronizarPessoa.cnpj_invalido', $loja);
            return null;
        }

        if (isset($cachePessoasLoja[$cnpj])) {
            return $cachePessoasLoja[$cnpj];
        }

        $pessoa = Pessoa::where('documento', '=', $cnpj)->first();
        APICombustivel2::debugLog('PedidoFrotasAbastecimentoList.sincronizarPessoa.lookup', [
            'cnpj' => $cnpj,
            'pessoa_existente_id' => $pessoa->id ?? null,
            'loja' => $loja,
        ]);

        if (!$pessoa) {
            $pessoa = new Pessoa();
            $pessoa->nome = trim((string) (($loja['razao_social'] ?? '') ?: ($loja['nome'] ?? '') ?: ('LOJA ' . $cnpj)));
            $pessoa->documento = $cnpj;
            $pessoa->fone = trim((string) ($loja['telefone'] ?? ''));
            $pessoa->email = trim((string) ($loja['email'] ?? ''));
            $pessoa->ativo = 'T';
            $pessoa->tipo_cliente_id = TipoCliente::JURIDICA;
            $pessoa->system_unit_id = TSession::getValue('idunit');
            $pessoa->store();
            APICombustivel2::debugLog('PedidoFrotasAbastecimentoList.sincronizarPessoa.criada', [
                'cnpj' => $cnpj,
                'pessoa_id' => $pessoa->id,
            ]);
        } else {
            $alterouPessoa = false;

            if (empty($pessoa->nome) && !empty($loja['razao_social'])) {
                $pessoa->nome = trim((string) $loja['razao_social']);
                $alterouPessoa = true;
            }

            if (empty($pessoa->fone) && !empty($loja['telefone'])) {
                $pessoa->fone = trim((string) $loja['telefone']);
                $alterouPessoa = true;
            }

            if (empty($pessoa->email) && !empty($loja['email'])) {
                $pessoa->email = trim((string) $loja['email']);
                $alterouPessoa = true;
            }

            if (empty($pessoa->system_unit_id)) {
                $pessoa->system_unit_id = TSession::getValue('idunit');
                $alterouPessoa = true;
            }

            if ($alterouPessoa) {
                $pessoa->store();
                APICombustivel2::debugLog('PedidoFrotasAbastecimentoList.sincronizarPessoa.atualizada', [
                    'cnpj' => $cnpj,
                    'pessoa_id' => $pessoa->id,
                ]);
            }
        }

        if (!PessoaGrupo::where('pessoa_id', '=', $pessoa->id)->where('grupo_pessoa_id', '=', GrupoPessoa::FORNECEDOR)->first()) {
            $pessoaGrupo = new PessoaGrupo();
            $pessoaGrupo->pessoa_id = $pessoa->id;
            $pessoaGrupo->grupo_pessoa_id = GrupoPessoa::FORNECEDOR;
            $pessoaGrupo->store();
            APICombustivel2::debugLog('PedidoFrotasAbastecimentoList.sincronizarPessoa.grupo_criado', [
                'cnpj' => $cnpj,
                'pessoa_id' => $pessoa->id,
                'grupo_pessoa_id' => GrupoPessoa::FORNECEDOR,
            ]);
        }

        $temDadosEndereco = !empty($loja['logradouro']) || !empty($loja['bairro']) || !empty($loja['cidade']) || !empty($loja['uf']) || !empty($loja['cep']);
        if ($temDadosEndereco && !PessoaEndereco::where('pessoa_id', '=', $pessoa->id)->where('principal', '=', 'T')->first()) {
            $cidade = $this->obterOuCriarCidadeLojaNp3((string) ($loja['cidade'] ?? ''), (string) ($loja['uf'] ?? ''));

            $pessoaEndereco = new PessoaEndereco();
            $pessoaEndereco->pessoa_id = $pessoa->id;
            $pessoaEndereco->nome = trim((string) (($loja['nome'] ?? '') ?: ($loja['razao_social'] ?? '') ?: $pessoa->nome));
            $pessoaEndereco->principal = 'T';
            $pessoaEndereco->cep = preg_replace('/\D+/', '', (string) ($loja['cep'] ?? ''));
            $pessoaEndereco->rua = trim((string) ($loja['logradouro'] ?? ''));
            $pessoaEndereco->numero = trim((string) ($loja['numero'] ?? ''));
            $pessoaEndereco->bairro = trim((string) ($loja['bairro'] ?? ''));
            $pessoaEndereco->complemento = trim((string) ($loja['complemento'] ?? ''));
            $pessoaEndereco->latitude = trim((string) ($loja['latitude'] ?? ''));
            $pessoaEndereco->longitude = trim((string) ($loja['longitude'] ?? ''));

            if ($cidade) {
                $pessoaEndereco->cidade_id = $cidade->id;
            }

            $pessoaEndereco->store();
            APICombustivel2::debugLog('PedidoFrotasAbastecimentoList.sincronizarPessoa.endereco_criado', [
                'cnpj' => $cnpj,
                'pessoa_id' => $pessoa->id,
                'cidade_id' => $pessoaEndereco->cidade_id ?? null,
            ]);
        }

        $cachePessoasLoja[$cnpj] = (int) $pessoa->id;
        return (int) $pessoa->id;
    }

    private function obterOuCriarCidadeLojaNp3(string $cidadeNome, string $uf): ?Cidade
    {
        $cidadeNome = trim($cidadeNome);
        $uf = strtoupper(trim($uf));

        if ($cidadeNome === '' || $uf === '') {
            return null;
        }

        $estado = Estado::where('sigla', '=', $uf)->first();
        if (!$estado) {
            $estado = new Estado();
            $estado->sigla = $uf;
            $estado->nome = $uf;
            $estado->store();
        }

        $cidade = Cidade::where('nome', '=', $cidadeNome)
                        ->where('estado_id', '=', $estado->id)
                        ->first();

        if (!$cidade) {
            $cidade = new Cidade();
            $cidade->nome = $cidadeNome;
            $cidade->estado_id = $estado->id;
            $cidade->store();
        }

        return $cidade;
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
                        'cidade_id'   => null,
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
                        'cidade_id'   => $cidadeId,
                        'status'      => 'NÃO ENVIADO',
                        'motivo'      => 'Já existe proposta para este pedido/pessoa/cidade (IDs: '.implode(', ', $idsExistentes).')',
                        'proposta_id' => null,
                        'email'       => $pessoaEmail,
                        'email_ok'    => false,
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

                            MailService::send($pessoa->email, $titulo, $mensagem, 'html');
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
                    'cidade_id'   => $cidadeId,
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
                    'cidade_id'   => isset($cidadeId) ? $cidadeId : null,
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
            $html .= '<td style="border-bottom:1px solid #eee">'.($r['cidade_id'] ?? '-').'</td>';
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

    public function onGerarFinanceiroItem($param = null) 
    {
       if (isset($param['confirmagerarfinanceiroitem']) && $param['confirmagerarfinanceiroitem']) {
            try {
                 TTransaction::open(self::$database);
                
                $pedidoId = (int) $param['id'];

                 
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
                    throw new Exception('Não é possível finalizar o pedido. Nenhum documento de proposta foi anexado.');
                }
                 //$this->form->setData($pedido); 
                $pedido = new PedidoFrotas($param['key']);


                $cot = Propostas::where('pedido_frotas_id','=',$pedido->id)
                               ->load();
                if ($cot) {
                   foreach($cot as $cotacao){
                     if ($cotacao->estado_pedido_frotas_id==EstadoPedidoFrotas::ENTREGUE) {
                        $valoritens=0;$valordesconto=0;$valortotal=0;
                        $itenscotacao = ItensPropostas::where('propostas_id','=',$cotacao->id)
                                                        ->where('deleted_at','is',null)
                                                        ->load();

                        if ($itenscotacao) {
                            foreach ($itenscotacao as $itensc) {
                                $valoritens += ($itensc->valor*$itensc->qtde);
                                $valordesconto +=($itensc->perc_desconto);
                                $valortotal += ($itensc->valor_total);
                            }
                        }
                    //    $pedido->valor_total_cotacao += $valoritens;
                            $taxaspessoa = TaxasPessoa::where('pessoa_id','=',$cotacao->pessoa_id)
                                                    ->where('deleted_at','is',null)
                                                    ->where('entidade_id','=',TSession::getValue('entidade'))
                                                    ->where('system_unit_id','=',TSession::getValue('idunit'))
                                                    ->load();
                            if ($taxaspessoa) {
                                foreach ($taxaspessoa as $vertxpessoa) {
                                    $taxaadm = $vertxpessoa->taxaadm;
                                    $taxabancaria = $vertxpessoa->taxabancaria;
                                    $taxaantecipacao = $vertxpessoa->taxaantecipacao;
                                    $taxadesconto = $vertxpessoa->taxadesconto;
                                    break;
                                }
                            }  else {
                                $taxaadm = 0;
                                $taxabancaria = 0;
                                $taxaantecipacao = 0;
                                $taxadesconto = 0;

                            }
                         $txcontrato = ((TSession::getValue('taxacontrato'))) ;

                        $conta = new Conta();
                        $conta->pessoa_id            = $cotacao->pessoa_id;
                        $conta->forma_pagamento_id   = 1; //dinheiro 2024-01-01
                        $conta->pedido_frotas_id     = $pedido->id;
                        $conta->dt_vencimento        = date('Y-m-d', strtotime("+35 days"));
                        $conta->mes_vencimento       = intval(substr($conta->dt_vencimento,5,2));
                        $conta->ano_vencimento       = intval(substr($conta->dt_vencimento,0,4));
                        $conta->ano_mes_vencimento   = intval(substr($conta->dt_vencimento,0,4).substr($conta->dt_vencimento,5,2));
                        $conta->valor                =  $valoritens;
                        $conta->valor_txcontrato       =  $valordesconto;

                        //calculo taxa administracao
/*                        $valortaxaadm = ($conta->valor - $conta->valor_txcontrato) * ($taxaadm/100);
                        $conta->valor_txadm           = $valortaxaadm; 

                        //valor taxa bancaria
                        $conta->valor_txbancaria     = 0;
                        $subtotal = ($conta->valor - $conta->valor_desconto - $valortaxaadm) - $taxabancaria;

                        //calculo taxa antecipacao
                        $valorantecipacao =  $subtotal * ($taxaantecipacao/100);
                        $conta->valor_txantecipacao  = $valorantecipacao;

                        $conta->valor_liquido        = ($conta->valor-($pedido->valor_desconto_proposta+$conta->valor_txadm+$conta->valor_txbancaria+$conta->valor_txantecipacao));
                        */
                        $conta->valor_liquido        = $valortotal;
                        $conta->parcela              = 1;
                        $conta->descricao            = $pedido->descricaopedido;        
                        $conta->tipo_conta_id        = TipoConta::PAGAR;
                        $conta->dt_emissao           = date('Y-m-d');
                        $conta->mes_emissao          = intval(substr($conta->dt_emissao,5,2));
                        $conta->ano_emissao          = intval(substr($conta->dt_emissao,0,4));
                        $conta->mes_ano_emissao      = intval(substr($conta->dt_emissao,0,4).substr($conta->dt_emissao,5,2));
                        $conta->departamento_unit_id = $pedido->departamento_unit_id;
                        $conta->system_users_id      = $pedido->system_users_id;
                        $conta->entidade_id        = $pedido->entidade_id;
                        $conta->system_unit_id      = $pedido->system_unit_id;
                        $conta->store();
                           $this->registrarHistoricoCotacaoAprovado($cotacao);

                           $cotacao->estado_pedido_frotas_id = EstadoPedidoFrotas::PGTOAPROVADO;
                        $cotacao->store();
                        
                    }

                }
    
                

                    $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::PGTOAPROVADO;
                    $pedido->store();

                    $this->registrarHistoricoPedidoAprovado($pedido);


                     }
             
                

              


                TToast::show('success', "Pagamento programado com sucesso!!! Consulte financeiro", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoFrotasList', 'onSetProject');
                $this->form->setData($pedido); 
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onGerarFinanceiroItem'));
            $action->setParameters($param);
            $action->setParameter('confirmagerarfinanceiroitem', true);

            new TQuestion('Tem certeza que deseja Gerar o financeiro?', $action);
        }

        /*try 
        {
            //code here

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }

     public function onGerarFinanceiro($param = null) 
    {
       if (isset($param['confirmagerarfinanceiro']) && $param['confirmagerarfinanceiro']) {
            try {
                 TTransaction::open(self::$database);
                 $pedidoId = (int) $param['id'];

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
                    throw new Exception('Não é possível finalizar o pedido. Nenhum documento de proposta foi anexado.');
                }
                //$this->form->setData($pedido); 
                $pedido = new PedidoFrotas($param['key']);

                $cot = Propostas::where('pessoa_id','=',$pedido->estabelecimento_id)
                               ->where('pedido_frotas_id','=',$pedido->id)
                               ->load();
                if ($cot) {
                   foreach($cot as $cotacao){
                      $cotacao->estado_pedido_frotas_id = EstadoPedidoFrotas::PGTOAPROVADO;
                      $cotacao->store();
                      $valoritens=0;
                      $itenscotacao = ItensPropostas::where('propostas_id','=',$cotacao->id)
                                                   ->load();

                      if ($itenscotacao) {
                         foreach ($itenscotacao as $itensc) {
                            $valoritens += $itensc->valor_total ;
                         }
                      }
                      $pedido->valor_total_cotacao = $valoritens;
                      $this->registrarHistoricoCotacaoAprovado($cotacao);
                      break;
                   }
                }
 
                $taxaspessoa = TaxasPessoa::where('pessoa_id','=',$pedido->estabelecimento_id)
                                                 ->where('deleted_at','is',null)
                                                 ->where('entidade_id','=',TSession::getValue('entidade'))
                                                 ->where('system_unit_id','=',TSession::getValue('idunit'))
                                                 ->load();
                    if ($taxaspessoa) {
                        foreach ($taxaspessoa as $vertxpessoa) {
                            $taxaadm = $vertxpessoa->taxaadm;
                            $taxabancaria = $vertxpessoa->taxabancaria;
                            $taxaantecipacao = $vertxpessoa->taxaantecipacao;
                            $taxacontrato = $vertxpessoa->taxacontrato;
                            $taxadesconto = $vertxpessoa->taxadesconto;
                            break;
                        }
                    }  else {
                        $taxaadm = 0;
                        $taxabancaria = 0;
                        $taxaantecipacao = 0;
                        $taxacontrato = 0;
                        $taxadesconto = 0;

                    }
                 $taxacontrato = ((TSession::getValue('taxacontrato'))) ;

                $conta = new Conta();
                $conta->pessoa_id            = $pedido->estabelecimento_id;
                $conta->forma_pagamento_id   = 1; //dinheiro 2024-01-01
                $conta->pedido_frotas_id     = $pedido->id;
                $conta->dt_vencimento        = date('Y-m-d', strtotime("+35 days"));
                $conta->mes_vencimento       = intval(substr($conta->dt_vencimento,5,2));
                $conta->ano_vencimento       = intval(substr($conta->dt_vencimento,0,4));
                $conta->ano_mes_vencimento   = intval(substr($conta->dt_vencimento,0,4).substr($conta->dt_vencimento,5,2));
                $conta->valor                = $pedido->valor_total_proposta;
                $conta->valor_txcontrato       = $pedido->valor_desconto_proposta;

                //calculo taxa administracao
                $valortaxaadm = ($conta->valor - $conta->valor_txcontrato) * ($taxaadm/100);
                $conta->valor_txadm           = $valortaxaadm; 

                //valor taxa bancaria
                $conta->valor_txbancaria     = 0;
                $subtotal = ($conta->valor - $conta->valor_desconto - $valortaxaadm) - $taxabancaria;

                //calculo taxa antecipacao
                $valorantecipacao =  $subtotal * ($taxaantecipacao/100);
                $conta->valor_txantecipacao  = $valorantecipacao;

                $conta->valor_liquido        = ($conta->valor-($pedido->valor_desconto_proposta+$conta->valor_txadm+$conta->valor_txbancaria+$conta->valor_txantecipacao));
               
                $conta->parcela              = 1;
                $conta->descricao            = $pedido->descricaopedido;        
                $conta->tipo_conta_id        = TipoConta::PAGAR;
                $conta->dt_emissao           = date('Y-m-d');
                $conta->mes_emissao          = intval(substr($conta->dt_emissao,5,2));
                $conta->ano_emissao          = intval(substr($conta->dt_emissao,0,4));
                $conta->mes_ano_emissao      = intval(substr($conta->dt_emissao,0,4).substr($conta->dt_emissao,5,2));
                $conta->departamento_unit_id = $pedido->departamento_unit_id;
                $conta->system_users_id      = $pedido->system_users_id;
                $conta->entidade_id        = $pedido->entidade_id;
                $conta->system_unit_id      = $pedido->system_unit_id;
                $conta->store();

                $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::PGTOAPROVADO;
                $pedido->store();

                $this->registrarHistoricoPedidoAprovado($pedido);

             
                

              


                TToast::show('success', "Pagamento programado com sucesso!!! Consulte financeiro", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoFrotasList', 'onSetProject');
                $this->form->setData($pedido); 
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onGerarFinanceiro'));
            $action->setParameters($param);
            $action->setParameter('confirmagerarfinanceiro', true);

            new TQuestion('Tem certeza que deseja Gerar o financeiro?', $action);
        }

        /*try 
        {
            //code here

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }

    private function registrarHistoricoPedidoAprovado($pedido)
     {
         $hist = new PedidoFrotasHistorico();
         $hist->pedido_frotas_id = $pedido->id;
         $hist->data_operacao = date('Y-m-d H:i:s');
         $hist->estado_pedido_frotas_id = EstadoPedido::PGTOAPROVADO; 
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
            $pageParam = [];
            TSession::setValue('pedido_frotas_id', NULL);
            TSession::setValue('pedido_frotas_id', $param['key']);
            TApplication::loadPage('PropostaPendenteList', 'onShow', [
                'target_container' => "container_propostas_{$param['key']}",
                'pedido_frotas_id' => $param['key']
            ]);
        
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }

    }

  

    public static function onExibirView($object)
    {

        try 
        {
            $pes1 = Pessoa::where('system_user_id','=',TSession::getValue('userid'))
            ->load();
            if ($pes1) {
                $pessoa_grupo = PessoaGrupo::where('pessoa_id', '=', $pes1[0]->id)
                ->where('grupo_id', '=', 5); // Grupo de condutor
                if ($pessoa_grupo) {
                    return true;
                } else {
                   return false;
                }
            } else {
                return true;
            }
        
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
                if( in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PENDENTE, EstadoPedidoFrotas::ENVIADO, EstadoPedidoFrotas::COMPROPOSTA, EstadoPedidoFrotas::PREAPROVADO]) )
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
           if( in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::APROVADO]) )
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
           if( in_array(EstadoPedidoFrotas::VALORVENAL, AprovadorFrotas::getEstadosDisponiveis()) )
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

    public static function onExibirAnexos($object)
    {
        try 
        {
              if( in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PGTOAPROVADO, EstadoPedidoFrotas::FINALIZADO,EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::ENTREGUE, EstadoPedidoFrotas::COMPROPOSTA ]) )
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
              if( in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PGTOAPROVADO, EstadoPedidoFrotas::FINALIZADO,EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::ENTREGUE ]) )
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


    public static function onExibirFinalizar($object)
    { 
        try 
        {
             if( in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PGTOAPROVADO]) )
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

    public static function onExibirGerarFinanceiro($object)
    {
        try 
        {

            if( in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::ENTREGUE]) )
            {
                TTransaction::open(self::$database);
                $propostas = Propostas::where('pedido_frotas_id', '=', $object->id)
                                      ->load();
                foreach ($propostas as $proposta) {
                    if ($proposta->estado_pedido_frotas_id == EstadoPedidoFrotas::APROVADO) {
                        TTransaction::close();
                        return false;
                    }
                }
                TTransaction::close();

                return true;
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

             if( in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PENDENTE, EstadoPedidoFrotas::ENVIADO, EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::COMPROPOSTA, EstadoPedidoFrotas::PREAPROVADO]) ) 
            {
                if (!in_array(EstadoPedidoFrotas::CANCELADO, AprovadorFrotas::getEstadosDisponiveis()))
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
            $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

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
            $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

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
            $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

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



    public function onFinalizarPedido($param = null) 
    {

       if (isset($param['confirmFinalizacao']) && $param['confirmFinalizacao']) {
            try 
            {
                TTransaction::open(self::$database);
               $pedidoId = (int) $param['id'];

              $documentospropostas = DocumentosPropostas::where(
                    'propostas_id',
                    'IN',
                    "(SELECT id
                    FROM propostas
                    WHERE pedido_frotas_id = {$pedidoId}
                        AND estado_pedido_frotas_id IN ("
                            .EstadoPedidoFrotas::APROVADO.", "
                            .EstadoPedidoFrotas::ENTREGUE.", "
                            .EstadoPedidoFrotas::PGTOAPROVADO.
                    ")
                    )"
                )->load();

                if (empty($documentospropostas)) {
                    throw new Exception('Não é possível finalizar o pedido. Nenhum documento de proposta foi anexado.');
                }
                // Atualiza o status do pedido e registra histórico
                $pedido = new PedidoFrotas($param['id'], false);
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

                TToast::show('success', "Pedido finalizado com sucesso!!", 'topRight', 'far:check-circle');
                TApplication::loadPage('PedidoFrotasList', 'onSetProject');
                $this->form->setData($pedido); 
                TTransaction::close();

            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Confirmação antes de gerar a cotação
            $action = new TAction(array($this, 'onFinalizarPedido'));
            $action->setParameters($param);
            $action->setParameter('confirmFinalizacao', true);

            new TQuestion('Tem certeza que deseja Finalizar este pedido?', $action);
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

    public static function onExibirEnvio($object)
    {
        try 
        {
            if (in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PENDENTE,EstadoPedidoFrotas::NAOENVIADO, EstadoPedidoFrotas::ENVIADO, EstadoPedidoFrotas::COMPROPOSTA, EstadoPedidoFrotas::PREAPROVADO  ]) )
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
            if (in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::APROVADO]) && $object->condutor_entrada_id == null)
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
            if (in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::FINALIZADO]) )
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
            if( in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::FINALIZADO, EstadoPedidoFrotas::PGTOAPROVADO, EstadoPedidoFrotas::ENTREGUE]) )
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
            if( in_array($object->estado_pedido_frotas_id, AprovadorFrotas::getEstadosDisponiveis()) && in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::PENDENTE]) )
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
                $pedido->store();

                $this->registrarHistoricoPedidocomproposta($pedido);

                $propostas = Propostas::where('pedido_frotas_id','=',$pedido->id)
                                  ->where('estado_pedido_frotas_id','=',EstadoPedidoFrotas::APROVADO)
                                  ->load();
                if ($propostas){
                    foreach ($propostas as $prop) 
                    {
                        $prop->estado_pedido_frotas_id = EstadoPedidoFrotas::AGUARDANDO;
                        $prop->store();
                        $this->registrarHistoricoCotacaoAguardando($prop);
                        $itens = ItensPropostas::where('propostas_id','=',$prop->id)->load();
                        if ($itens) {
                            foreach ($itens as $item) {
                                $item->estado_pedido_frotas_id = null;
                                $item->store();
                            }
                        }
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


function criteriaHasFilterField($filters, string $field): bool
{
    if (empty($filters) || !is_array($filters)) {
        return false;
    }

    foreach ($filters as $f) {
        if (!is_object($f)) continue;

        // Em Adianti, normalmente é TFilter
        if (get_class($f) !== 'TFilter') continue;

        $ref = new ReflectionClass($f);

        // dependendo da versão, pode ser 'variable' ou 'field'
        foreach (['variable', 'field'] as $propName) {
            if ($ref->hasProperty($propName)) {
                $p = $ref->getProperty($propName);
                $p->setAccessible(true);

                if ($p->getValue($f) === $field) {
                    return true;
                }
            }
        }
    }

    return false;
}

    
}
