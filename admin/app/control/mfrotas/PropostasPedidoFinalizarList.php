<?php

use Adianti\Widget\Form\THidden;

class PropostasPedidoFinalizarList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Propostas';
    private static $primaryKey = 'id';
    private static $formName = 'form_PropostasDisponiveisList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

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

        // define the form title
        $this->form->setFormTitle("Listagem de propostas");
        $this->limit = 20;

        $criteria_estado_pedido_frotas_id = new TCriteria();
        $criteria_veiculos_id = new TCriteria();
        $criteria_pedido_frotas_id = new TCriteria();
        $filterVar = TSession::getValue('idpedido') ?? null;
        $criteria_pedido_frotas_id->add(new TFilter('pedido_frotas_id', '=', $filterVar)); 
        $criteria_pedido_frotas_id->add(new TFilter('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::PGTOAPROVADO)); 

        $criteria_cidade_id = new TCriteria();
        $login = new LoginForm([]);
$AlertMensagem = $login->onMensagem('PropostasDisponiveisList');

        $TAlert = new TAlert('danger',$AlertMensagem);
        $id = new TEntry('id');
        // $pedido_frotas_id = new THidden('pedido_frotas_id');
        $estado_pedido_frotas_id = new TDBCombo('estado_pedido_frotas_id', 'minierp', 'EstadoPedidoFrotas', 'id', '{nome}','nome asc' , $criteria_estado_pedido_frotas_id );
        $pedido_frotas_id = new TDBCombo('pedido_frotas_id', 'minierp', 'Propostas', 'id', '{id}','id asc' , $criteria_pedido_frotas_id );
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa}','id asc' , $criteria_veiculos_id );
        $data_cotacao_inicial = new BDateRange('data_cotacao_inicial', 'data_cotacao_final');
        $cidade_id = new TDBUniqueSearch('cidade_id', 'minierp', 'Cidade', 'id', 'nome','nome asc' , $criteria_cidade_id );
        $emrevisao = new TCheckButton('emrevisao');
        $emrevisao->setValue('1');
           $emrevisao->setUseSwitch(true, 'blue');
            $emrevisao->setInactiveIndexValue("2");
        $data_cotacao_inicial->setDatabaseMask('yyyy-mm-dd');
        $cidade_id->setMinLength(2);
        $cidade_id->setFilterColumns(["nome"]);
        $veiculos_id->enableSearch();
        $estado_pedido_frotas_id->enableSearch();

        $data_cotacao_inicial->setMask('dd/mm/yyyy');
        $cidade_id->setMask('{nome}  - {estado->sigla}');

        $id->setSize(100);
        $cidade_id->setSize('100%');
        $veiculos_id->setSize('100%');
        $pedido_frotas_id->setSize('100%');
        $data_cotacao_inicial->setSize(220);
        $estado_pedido_frotas_id->setSize('100%');

        // $row2 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("ID Pedido:", null, '14px', null, '100%'),$pedido_frotas_id]);
        // $row2->layout = ['col-sm-6','col-sm-6'];

        // $row3 = $this->form->addFields([new TLabel("Estado pedido frotas:", null, '14px', null, '100%'),$estado_pedido_frotas_id],[new TLabel("Veiculos:", null, '14px', null, '100%'),$veiculos_id]);
        // $row3->layout = ['col-sm-6','col-sm-6'];

        // $row4 = $this->form->addFields([new TLabel("Periodo:", null, '14px', null, '100%'),$data_cotacao_inicial],[new TLabel("Cidade:", null, '14px', null, '100%'),$cidade_id]);
        // $row4->layout = ['col-sm-6',' col-sm-6'];

        // $row5 = $this->form->addFields([new TLabel("Em revisão:", null, '14px', null, '100%'),$emrevisao],[]);
        // $row5->layout = ['col-sm-6',' col-sm-6'];


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

      /*  $startHidden = true;

        if(TSession::getValue('PropostasDisponiveisList_expand_start_hidden') === false)
        {
            $startHidden = false;
        }
        elseif(TSession::getValue('PropostasDisponiveisList_expand_start_hidden') === true)
        {
            $startHidden = true; 
        }
        $expandButton = $this->form->addExpandButton("Expandir", 'fas:expand #000000', $startHidden);
        $expandButton->addStyleClass('btn-default');
        $expandButton->setAction(new TAction([$this, 'onExpandForm'], ['static'=>1]), "Expandir");
        $this->form->addField($expandButton);*/

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->filter_criteria->add(new TFilter('pedido_frotas_id', '=', $filterVar));
        $this->filter_criteria->add(new TFilter('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::PGTOAPROVADO));

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_pedido_frotas_id = new TDataGridColumn('pedido_frotas_id', "Pedido ID", 'left');
        // $column_system_unit_id = new TDataGridColumn('system_unit->name', "Unidade", 'left');
        // $column_departamento_unit_id = new TDataGridColumn('departamento_unit->name', "Unidade/Dep/Secretaria", 'left');
        $column_pessoa_nome = new TDataGridColumn('pessoa_id', "Estabelecimento", 'left');
        $column_veiculos_id = new TDataGridColumn('veiculos_id', "Placa", 'left');
        $column_data_cotacao_transformed = new TDataGridColumn('data_cotacao', "Data ", 'left');
        // $column_data_limite_transformed = new TDataGridColumn('data_limite_resposta', "Dt Limite Resposta ", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_total', "Valor total", 'left');
     //   $column_valor_desconto_transformed = new TDataGridColumn('valor_desconto', "Valor desconto", 'left');
      //  $column_valor_liquido_transformed = new TDataGridColumn('valor_liquido', "Valor liquido", 'left');
        $column_estado_pedido_frotas_nome_transformed = new TDataGridColumn('estado_pedido_frotas->nome', "Estado pedido ", 'left');
        // $column_system_users_name = new TDataGridColumn('system_users_id', "Usuário", 'left');
        $column_cidade_nome_cidade_estado_sigla = new TDataGridColumn('cidade_id', "Cidade", 'left');

         $column_cidade_nome_cidade_estado_sigla->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open('minierp');

            $cidades = new Cidade($object->cidade_id);
            $estados = new Estado($cidades->estado_id);
            TTransaction::close();
            return $cidades->nome.'-'.$estados->sigla;
        });
        //  $column_system_users_name->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        // {
        //     TTransaction::open('minierp');

        //     $user = new SystemUsers($object->system_users_id);
        //     if ($user) {
        //     return $user->name;
        //     }
        //     else {return '';}

        //     TTransaction::close();
        // });
        $column_pessoa_nome->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open('minierp');

            $pessoas = new Pessoa($object->pessoa_id);
            TTransaction::close();
            return $pessoas->nome;
        });
        $column_veiculos_id->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open('minierp');

            $veiculos = new Veiculos($object->veiculos_id);
            TTransaction::close();
            return $veiculos->placa;
        });
        $column_data_cotacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
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
//       $column_data_limite_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
// {
//     if (!empty(trim((string) $value))) {
//         try {
//             $date = new DateTime($value);
//             $formatted = $date->format('d/m/Y');

//             // Comparar somente datas (sem hora)
//             $hoje = new DateTime(date('Y-m-d'));
//             $dataLimite = new DateTime($date->format('Y-m-d'));

//             $span = new TElement('span');
//             $span->add($formatted);

//             if ($dataLimite < $hoje) {
//                 // Expirada → vermelho
//                 $span->style = 'color:red; font-weight:bold;';
//             } else {
//                 // Dentro do prazo → verde
//                 $span->style = 'color:green;';
//             }

//             return $span;
//         } catch (Exception $e) {
//             return $value;
//         }
//     }
// });

        $column_valor_total_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open('minierp');

            $value = 0;
            $proposta_id = $object->id;

            // Busca os aprovados
            $itens_aprovados = ItensPropostas::where('propostas_id', '=', $proposta_id)
                                            ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
                                            ->load();

            if ($itens_aprovados && count($itens_aprovados) > 0) {
                // Se houver aprovados, soma só eles
                foreach ($itens_aprovados as $item) {
                    $value += ($item->valor * $item->qtde) - $item->perc_desconto;
                }
            } else {
                // Caso contrário, soma todos os itens da proposta
                $itens_todos = ItensPropostas::where('propostas_id', '=', $proposta_id)->load();
                foreach ($itens_todos as $item) {
                    $value += ($item->valor * $item->qtde) - $item->perc_desconto;
                }
            }

            TTransaction::close();

            return is_numeric($value) ? "R$ " . number_format($value, 2, ",", ".") : $value;
        });

     /*   $column_valor_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
*/
        $column_estado_pedido_frotas_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
            //code here
                    $temnotafiscal = false;
                        TTransaction::open('minierp');

                    if ($object->estado_pedido_frotas::FINALIZADO || $object->estado_pedido_frotas::APROVADO || $object->estado_pedido_frotas::PGTOAPROVADO || $object->estado_pedido_frotas::ENTREGUE ) {
                        // var_dump($object);
                    //die();  

                        $doccot = DocumentosPropostas::where('propostas_id','=',$object->id)
                                                   ->load();
                        if ($doccot){
                            $temnotafiscal = true;
                        }

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
                        TTransaction::close();

            return "<span class='label label-default' style='width:250px; background-color:{$object->estado_pedido_frotas->cor}; display:inline-block;'> {$anexo} {$revisao} </span>";
/*
                    if ($temnotafiscal) {
                       $anexo = $object->estado_pedido_frotas->nome.' <i class="fa fa-paperclip" aria-hidden="true"></i>';
                        return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido_frotas->cor}'> {$anexo} <span>";
                    } else {
                        return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido_frotas->cor}'> {$object->estado_pedido_frotas->nome} <span>";
                    }            */

        });

      /*  $column_comentario_proposta_propostas_to_string_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
             //code here

                // var_dump($object);
            //die();  
            TTransaction::open('minierp');

            $criteria = new TCriteria();
            $criteria->add(new TFilter('propostas_id', '=', $object->id));
            $criteria->add(new TFilter('system_users_id', '<>', TSession::getValue('userid')));
            $criteria->add(new TFilter('leitura_dt', 'IS', NULL));

            $repo = new TRepository('ComentarioProposta');
            $com = $repo->load($criteria);

            $qtcom = 0;
            if ($com) {
                foreach ($com as $comm) {
                    $qtcom++;
                }
            }

            TTransaction::close();

            $anexo = ' <i class="fa fa-comment-alt"></i> ' . $qtcom . ' Comentários';

            $bgcolor = ($qtcom > 0) ? '#10c246' : '#6d799d';

            // Cria a ação que aponta para o PropostasForm::onEdit com o ID do objeto

            $action = new TAction(['PropostasForm', 'onEdit'], [
                'key' => $object->id,
                'id' => $object->id
            ]);
    
            $a = new TElement('a');
            $a->class = 'btn btn-link';
            $a->style = "background-color: {$bgcolor}; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none;";
            $a->generator = 'adianti';
            $a->href = $action->serialize();
            $a->add("{$anexo}");
            return $a;

        });    */


        
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_pedido_frotas_id);
        // $this->datagrid->addColumn($column_system_unit_id);
        // $this->datagrid->addColumn($column_departamento_unit_id);
        $this->datagrid->addColumn($column_pessoa_nome);
        $this->datagrid->addColumn($column_veiculos_id);
        $this->datagrid->addColumn($column_data_cotacao_transformed);
        // $this->datagrid->addColumn($column_data_limite_transformed);
        
        $this->datagrid->addColumn($column_valor_total_transformed);
      // $this->datagrid->addColumn($column_valor_desconto_transformed);
     //   $this->datagrid->addColumn($column_valor_liquido_transformed);
        $this->datagrid->addColumn($column_estado_pedido_frotas_nome_transformed);
        // $this->datagrid->addColumn($column_system_users_name);
     
        $this->datagrid->addColumn($column_cidade_nome_cidade_estado_sigla);



        // $action_onCadastrar = new TDataGridAction(array('DotacaoPedidoFrotasForm', 'onShow'));
        // $action_onCadastrar->setUseButton(false);
        // $action_onCadastrar->setButtonClass('btn btn-default btn-sm');
        // $action_onCadastrar->setLabel("Cadastrar");
        // $action_onCadastrar->setImage('fas:plus #69AA46');
        // $action_onCadastrar->setField(self::$primaryKey);

        // $this->datagrid->addAction($action_onCadastrar); 
        
        /*

        $action_onGerarItens = new TDataGridAction(array('PropostasDisponiveisList', 'onGerarItens'));
        $action_onGerarItens->setUseButton(false);
        $action_onGerarItens->setButtonClass('btn btn-default btn-sm');
        $action_onGerarItens->setLabel("Gerar itens da proposta");
        $action_onGerarItens->setImage('fas:cogs #000000');
        $action_onGerarItens->setField(self::$primaryKey);
        $action_onGerarItens->setDisplayCondition('PropostasDisponiveisList::onExibirGerar');

        $this->datagrid->addAction($action_onGerarItens);

        $action_onImprimir = new TDataGridAction(array('PropostasDisponiveisList', 'onImprimir'));
        $action_onImprimir->setUseButton(false);
        $action_onImprimir->setButtonClass('btn btn-default btn-sm');
        $action_onImprimir->setLabel("Imprimir");
        $action_onImprimir->setImage('fas:print #000000');
        $action_onImprimir->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onImprimir);

        $action_onShow = new TDataGridAction(array('NegociacaoAtividadeGlobalCalendarForm', 'onShow'));
        $action_onShow->setUseButton(false);
        $action_onShow->setButtonClass('btn btn-default btn-sm');
        $action_onShow->setLabel("Enviar proposta");
        $action_onShow->setImage('fas:envelope #000000');
        $action_onShow->setField(self::$primaryKey);
        $action_onShow->setDisplayCondition('PropostasDisponiveisList::onExibirEnviarProposta');

        $this->datagrid->addAction($action_onShow);
 
     */
     // creates two datagrid actions
        // $action1 = new TDataGridAction(['PropostasForm', 'onEdit'],     ['id' => '{id}']);
        // $action2 = new TDataGridAction(['PropostasDisponiveisList', 'onGerarItens'],   ['id' => '{id}']);
        // $action3 = new TDataGridAction(['PropostaFormEmailVenda', 'onSetProject'],   ['id' => '{id}']);
        // $action4 = new TDataGridAction(['PropostasDisponiveisList', 'onImprimir'],   ['id' => '{id}']);
        // $action5 = new TDataGridAction(['DocumentosPropostasList', 'onSetProject'],   ['id' => '{id}']);
        // $action6 = new TDataGridAction(['DocumentosPedidoFrotasSimpleList', 'onSetProject'], [
        //     'id' => '{id}',
        //     'pedido_frotas_id' => '{pedido_frotas_id}'
        // ]);        
        // $action7 = new TDataGridAction(['PropostasInicioServicoForm', 'onEdit'],   ['id' => '{id}']);
         
        // $action1->setLabel('Editar');
        // $action1->setImage('far:edit #478fca');
        // $action1->setDisplayCondition('PropostasDisponiveisList::onExibirEditar');

        // $action2->setLabel('Gerar');
        // $action2->setImage('fas:cogs #03A9F4');
        // $action2->setDisplayCondition('PropostasDisponiveisList::onExibirGerar');

        // $action7->setLabel('Início serviço');
        // $action7->setImage('fas:hammer #03A9F4');
        // $action7->setDisplayCondition('PropostasDisponiveisList::onExibirInicioServico');

        // $action3->setLabel('Enviar');
        // $action3->setImage('fas:envelope #E91E63');
        // $action3->setDisplayCondition('PropostasDisponiveisList::onExibirEnviarProposta');

        // $action4->setLabel('Orçamento');
        // $action4->setImage('fas:file-pdf #F44336');

        // $action5->setLabel('Anexar Arquivo');
        // $action5->setImage('fas:paperclip #2196F3');
        
        // $action6->setLabel('Ver Anexos');
        // $action6->setImage('fas:file-alt #FF9800');

        // $action_group = new TDataGridActionGroup('Clique Ações ', 'fa:th red');

        // $action_group->addAction($action1);
        // $action_group->addAction($action2);
        // $action_group->addAction($action3);
        // $action_group->addAction($action7);
        // $action_group->addAction($action4);
        // $action_group->addAction($action5);
        // $action_group->addAction($action6);

        // // add the actions to the datagrid
        // $this->datagrid->addActionGroup($action_group);

        // create the datagrid model
        $this->applyDatagridProperties();

        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

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

        // $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        // $button_limpar_filtros->setAction(new TAction(['PropostasDisponiveisList', 'onClearFilters']), "Limpar filtros");
        // $button_limpar_filtros->addStyleClass('btn-default');
        // $button_limpar_filtros->setImage('fas:eraser #f44336');

        // $this->datagrid_form->addField($button_limpar_filtros);

        // $button_atualizar = new TButton('button_button_atualizar');
        // $button_atualizar->setAction(new TAction(['PropostasDisponiveisList', 'onRefresh']), "Atualizar");
        // $button_atualizar->addStyleClass('btn-default');
        // $button_atualizar->setImage('fas:sync-alt #03a9f4');

        // $this->datagrid_form->addField($button_atualizar);

        //   $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        // $btnShowCurtainFilters->setAction(new TAction(['PropostasDisponiveisList', 'onShowCurtainFilters']), "Filtros");
        // $btnShowCurtainFilters->addStyleClass('btn-default');
        // $btnShowCurtainFilters->setImage('fas:filter #000000');

        // $this->datagrid_form->addField($btnShowCurtainFilters);

        // $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        // $dropdown_button_exportar->setPullSide('right');
        // $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        // $dropdown_button_exportar->addPostAction( "CSV", new TAction(['PropostasDisponiveisList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        // $dropdown_button_exportar->addPostAction( "XLS", new TAction(['PropostasDisponiveisList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        // $dropdown_button_exportar->addPostAction( "PDF", new TAction(['PropostasDisponiveisList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        // $dropdown_button_exportar->addPostAction( "XML", new TAction(['PropostasDisponiveisList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        // $head_left_actions->add($button_limpar_filtros);
        // $head_left_actions->add($button_atualizar);
        // $head_left_actions->add($btnShowCurtainFilters);

        

        // $head_right_actions->add($dropdown_button_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
          if (!empty($AlertMensagem)) {
                $container->add($TAlert);
          }
        }
       
  //      $container->add($this->form);

        $container->add($panel);

        parent::add($container);

    }

    public static function onExibirEditar($object)
    {
        try 
        {
              if(in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::NAOENVIADO, EstadoPedidoFrotas::AGUARDANDO]) )
            {
                return true;
           }

            return false;
        
            //return !empty($itenspropostas); // Exibe se houver itens
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            return false;
        } 
        finally 
        {
            TTransaction::close();
        }
    }
    public function onGerarItens($param = null) 
    {
        try 
        {
            TTransaction::open(self::$database);

            $object = new Propostas($param['id'], FALSE); 
            $pedido_frotas = new PedidoFrotas($object->pedido_frotas_id, FALSE);

            if (!empty($pedido_frotas->data_aprovacao)) {
                throw new Exception('Este pedido já foi aprovado. Gentileza consultar o gestor responsável.');
            }

            // Bloquear caso o estado do pedido seja um dos estados finais
            $estadosNaoPermitidos = [
                             EstadoPedidoFrotas::FINALIZADO
            ];

            if (in_array($pedido_frotas->estado_pedido_frotas_id, $estadosNaoPermitidos)) {
                new TMessage('error', 'Não é permitido gerar itens para pedidos já finalizados, aprovados ou entregues.');
                TTransaction::close();
                return;
            }

            // Verifica a data limite
            if ($pedido_frotas->data_limite_resposta < date('Y-m-d')) {
                new TMessage('error', 'Data limite de responder o pedido já foi atingida!');
                TTransaction::close();
                return;
            }

            $itenspedido = ItensPedidoFrotas::where('pedido_frotas_id', '=', $object->pedido_frotas_id)->load();
            $geroualgumitem = 0;

            if ($itenspedido)
            {
                foreach ($itenspedido as $itensp)
                {
                    $itensx = ItensPropostas::where('propostas_id', '=', $object->id)
                                            ->where('produto_id', '=', $itensp->produto_id)
                                            ->where('tipo', '=', $itensp->tipo)
                                            ->load();

                    if (!$itensx) {
                        $itenscotacao = new ItensPropostas();
                        $itenscotacao->produto_id = $itensp->produto_id;
                        $itenscotacao->descricao = $itensp->descricao;
                        $itenscotacao->qtde = $itensp->qtde;
                        $itenscotacao->tipo = $itensp->tipo;
                        $itenscotacao->propostas_id = $object->id;
                        $itenscotacao->itens_pedido_frotas_id = $itensp->id;
                        $itenscotacao->store();
                    

                    } else {
                        $geroualgumitem = 1;
                    }
                }
            }

            $itenscotx = ItensPropostas::where('propostas_id', '=', $object->id)->load();

            if ($itenscotx) {
                foreach ($itenscotx as $icx) {
                    $itenspedido1 = ItensPedidoFrotas::where('pedido_frotas_id', '=', $object->pedido_frotas_id)
                                                    ->where('produto_id', '=', $icx->produto_id)
                                                    ->where('tipo', '=', $icx->tipo)
                                                    ->load();

                    if (!$itenspedido1) {
                     //   $icx->delete();
                    } else {
                        if ($icx->qtde != $itenspedido1[0]->qtde) {
                            $icx->qtde = $itenspedido1[0]->qtde;
                            $icx->store();
                        }
                    }
                }
            }

            if ($geroualgumitem == 0 || $object->estado_pedido_frotas_id == EstadoPedidoFrotas::PENDENTE) {
                $object->datacotacao = date('Y-m-d');
                $object->estado_pedido_frotas_id = EstadoPedidoFrotas::NAOENVIADO;
                $object->system_users_id = TSession::getValue('iduser');
                $object->store();

                $this->registrarHistoricoCotacao($object);
            }
            $itensx = ItensPropostas::where('propostas_id', '=', $object->id)
                                            ->load();
            foreach ($itensx as $it) {
                $it->deleted_at = null;
                $it->store();
            }


            new TMessage('info', 'Itens da proposta gerados com sucesso!');
            TTransaction::close();
            $this->onReload();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

      private function registrarHistoricoCotacao($propostas)
    {
        $histpropostas = new PropostasHistorico();
        $histpropostas->propostas_id = $propostas->id;
        $histpropostas->data_historico = date('Y-m-d');
        $histpropostas->estado_pedido_frotas_id = EstadoPedido::PENDENTE; 
       // $histpropostas->aprovador_frotas_id = TSession::getValue('userid');
        $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $histpropostas->aprovador_frotas_id = $aprovador[0]->id;
        }
        $histpropostas->store();
    }
    public static function onExibirGerar($object)
    {
        try 
        {
            TTransaction::open('minierp'); // ajuste o nome do banco se necessário

            // Carrega o pedido pelo ID vindo do objeto
            $pedido = new Propostas($object->id);

            // Define os estados permitidos para exibir o botão
            $estadosPermitidos = [
                EstadoPedidoFrotas::PENDENTE,
                EstadoPedidoFrotas::NAOENVIADO,
                EstadoPedidoFrotas::AGUARDANDO,
                EstadoPedidoFrotas::PREAPROVADO
            ];

            // Retorna true apenas se o estado estiver dentro dos permitidos
            if (in_array($pedido->estado_pedido_frotas_id, $estadosPermitidos)) {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
        finally
        {
            TTransaction::close();
        }
    }

     public function onImprimir($param = null) 
    {
        try 
        {
            include_once 'app/control/mfrotas/PedidoFrotasOrcamento.php';

            $orcamento = new PropostaOrcamento;
            $orcamento->gerar($param);

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
  
    public static function onExibirInicioServico($object)
    {
        try 
        {
            if( in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PGTOAPROVADO]) )
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
    public static function onExibirEnviarProposta($object)
    {
        try 
        {
            TTransaction::open('minierp'); // ajuste o nome do banco se necessário

            $pedido = new Propostas($object->id);

            $estadosBloqueados = [
                EstadoPedidoFrotas::CANCELADO,
                EstadoPedidoFrotas::REPROVADO,
                EstadoPedidoFrotas::FINALIZADO,
                EstadoPedidoFrotas::APROVADO,
                EstadoPedidoFrotas::PGTOAPROVADO,
                EstadoPedidoFrotas::ENTREGUE
            ];

            if (in_array($pedido->estado_pedido_frotas_id, $estadosBloqueados)) {
                return false;
            }

            return true;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
        finally 
        {
            TTransaction::close();
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
    public function onExportPdf($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.pdf';

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
                $object = new TElement('iframe');
                $object->src  = $output;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($object);
                $window->show();
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
    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }
    public function onRefresh($param = null) 
    {
        $this->onReload([]);
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

        if (isset($data->pedido_frotas_id) AND ( (is_scalar($data->pedido_frotas_id) AND $data->pedido_frotas_id !== '') OR (is_array($data->pedido_frotas_id) AND (!empty($data->pedido_frotas_id)) )) )
        {

            $filters[] = new TFilter('pedido_frotas_id', '=', $data->pedido_frotas_id);// create the filter 
        }

        if (isset($data->estado_pedido_frotas_id) AND ( (is_scalar($data->estado_pedido_frotas_id) AND $data->estado_pedido_frotas_id !== '') OR (is_array($data->estado_pedido_frotas_id) AND (!empty($data->estado_pedido_frotas_id)) )) )
        {

            $filters[] = new TFilter('estado_pedido_frotas_id', '=', $data->estado_pedido_frotas_id);// create the filter 
        }

        if (isset($data->veiculos_id) AND ( (is_scalar($data->veiculos_id) AND $data->veiculos_id !== '') OR (is_array($data->veiculos_id) AND (!empty($data->veiculos_id)) )) )
        {

            $filters[] = new TFilter('veiculos_id', '=', $data->veiculos_id);// create the filter 
        }

        if (isset($data->data_cotacao_final) AND ( (is_scalar($data->data_cotacao_final) AND $data->data_cotacao_final !== '') OR (is_array($data->data_cotacao_final) AND (!empty($data->data_cotacao_final)) )) )
        {

            $filters[] = new TFilter('data_cotacao', '<=', $data->data_cotacao_final);// create the filter 
        }

        if (isset($data->data_cotacao_inicial) AND ( (is_scalar($data->data_cotacao_inicial) AND $data->data_cotacao_inicial !== '') OR (is_array($data->data_cotacao_inicial) AND (!empty($data->data_cotacao_inicial)) )) )
        {

            $filters[] = new TFilter('data_cotacao', '>=', $data->data_cotacao_inicial);// create the filter 
        }

        if (isset($data->cidade_id) AND ( (is_scalar($data->cidade_id) AND $data->cidade_id !== '') OR (is_array($data->cidade_id) AND (!empty($data->cidade_id)) )) )
        {

            $filters[] = new TFilter('cidade_id', '=', $data->cidade_id);// create the filter 
        }
        if (($data->emrevisao==1) OR ($data->emrevisao=='on'))
        {

            $filters[] = new TFilter('estado_pedido_frotas1_id', '=', 26);// create the filter 
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

            // creates a repository for Propostas
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
            $pessoa = Pessoa::where('system_user_id', '=', TSession::getValue('userid'))
                            ->where('id','not in', '(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '.GrupoPessoa::CONDUTOR.')')
                            ->load();
            $grupo  = SystemUserGroup::where('system_user_id', '=', TSession::getValue('userid'))
                                     ->where('system_group_id', '=', 1) // Admin
                                     ->load();
            
            if ($pessoa) {
                // Assume que o usuário só tem uma pessoa associada (se houver várias, pegará a última)
                foreach ($pessoa as $pe) {
                    $codpessoa = $pe->id;
                    break;
                }
            
                // Se não for grupo admin, aplicar filtro de pessoa
                if (!$grupo) {
                    $criteria->add(new TFilter('pessoa_id', '=', $codpessoa));
            
                    // Cálculo das taxas (com validação de divisão)
                 //   $txbancaria = isset($pe->taxabancaria) ? $pe->taxabancaria / 100 : 0;
                 //   $vltaxas = ($pe->taxaadm ?? 0) + ($pe->taxaantecipacao ?? 0) + ($pe->taxacontrato ?? 0) + $txbancaria;
                }
            }
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));

            
            // Subgrupo 1: estado diferente de PENDENTE
            $notPending = new TCriteria();
            $notPending->add(new TFilter('estado_pedido_frotas_id', '!=', EstadoPedidoFrotas::PENDENTE));

            // Subgrupo 2: estado igual a PENDENTE E data_limite_resposta <= hoje
            $pendingWithDate = new TCriteria();
            $pendingWithDate->add(new TFilter('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::PENDENTE));
            $pendingWithDate->add(new TFilter('data_limite_resposta', '>=', date('Y-m-d')));

            // Combina os dois subgrupos com OR
            $estadoComData = new TCriteria();
            $estadoComData->add($notPending, TExpression::OR_OPERATOR);
            $estadoComData->add($pendingWithDate, TExpression::OR_OPERATOR);

            // Aplica ao critério principal
            $criteria->add($estadoComData);

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

             if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                }
            }

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

    public static function onExpandForm($param = null)
    {
        try
        {
            $startHidden = true;

            if(TSession::getValue('PropostasDisponiveisList_expand_start_hidden') === false)
            {
                TSession::setValue('PropostasDisponiveisList_expand_start_hidden', true);
            }
            elseif(TSession::getValue('PropostasDisponiveisList_expand_start_hidden') === true)
            {
                TSession::setValue('PropostasDisponiveisList_expand_start_hidden', false);
            }
            else
            {
                TSession::setValue('PropostasDisponiveisList_expand_start_hidden', !$startHidden);
            }

        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {
        // // if (TSession::getValue('inseridoitem'))
        // // {   
        //     TTransaction::open('minierp');
        //     if (isset($param['propostas_id'])) {


        //         $proposta = new Propostas($param['propostas_id']);

        //         if ($proposta) {
        //             $pedido_id = $proposta->pedido_frotas_id;

        //             $itenspropostas = ItensPropostas::where('propostas_id', '=', $proposta->id)->load();
        //             $proposta_atual_foi_alterada = false;
        //             $propostas_alteradas = []; // guardar IDs de propostas alteradas
                    
        //             foreach ($itenspropostas as $ip) {
        //                 // 1. Criar ou atualizar o ItensPedidoFrotas
        //                 $item_pedido = ItensPedidoFrotas::where('id', '=', $ip->itens_pedido_frotas_id)->first();
                        
        //                 if (!$item_pedido || $ip->itens_pedido_frotas_id == null) {
        //                     $novo_item = new ItensPedidoFrotas();
        //                     $novo_item->qtde = $ip->qtde;
        //                     $novo_item->tipo = $ip->tipo;
        //                     $novo_item->pedido_frotas_id = $pedido_id;
        //                     $novo_item->descricao = $ip->descricao;
        //                     $novo_item->produto_id = $ip->produto_id;
        //                     $novo_item->store();

        //                     $ip->itens_pedido_frotas_id = $novo_item->id;
        //                     $ip->store();

        //                     $proposta_atual_foi_alterada = true;
        //                 } else {
        //                     $alterado = false;

        //                     if ($item_pedido->qtde != $ip->qtde) {
        //                         $item_pedido->qtde = $ip->qtde;
                                
        //                         $alterado = true;
        //                     }

        //                     if ($item_pedido->tipo != $ip->tipo) {
        //                         $item_pedido->tipo = $ip->tipo;
        //                         $alterado = true;
        //                     }

        //                     if ($alterado) {
        //                         $item_pedido->store();
        //                         $proposta_atual_foi_alterada = true;
        //                     }
        //                 }
        //                 if (!$proposta_atual_foi_alterada) {
        //                     // 2. Copiar para outras propostas válidas, mas só marcar revisão se inserido novo item
        //                     $outras_propostas = Propostas::where('pedido_frotas_id', '=', $pedido_id)
        //                         ->where('id', '!=', $proposta->id)
        //                         ->where('estado_pedido_frotas_id', 'in', [
        //                             EstadoPedidoFrotas::NAOENVIADO,
        //                             EstadoPedidoFrotas::PREAPROVADO,
        //                             EstadoPedidoFrotas::AGUARDANDO
        //                         ])
        //                         ->load();

        //                     foreach ($outras_propostas as $pr) {
        //                         $existe = ItensPropostas::where('propostas_id', '=', $pr->id)
        //                             ->where('itens_pedido_frotas_id', '=', $ip->itens_pedido_frotas_id)
        //                             ->first();

        //                         if (!$existe) {
        //                             $novo = new ItensPropostas();
        //                             $novo->descricao = $ip->descricao;
        //                             $novo->qtde = $ip->qtde;
        //                             $novo->tipo = $ip->tipo;
        //                             $novo->propostas_id = $pr->id;
        //                             $novo->itens_pedido_frotas_id = $ip->itens_pedido_frotas_id;
        //                             $novo->produto_id = $ip->produto_id;
        //                             $novo->store();

        //                             $propostas_alteradas[] = $pr->id; // marcar para revisão
        //                         }
        //                     }
        //                 }
                       
        //             }

        //             // 3. Marcar a proposta atual se foi alterada
        //             if ($proposta_atual_foi_alterada) {
        //                 $proposta->estado_pedido_frotas1_id = EstadoPedidoFrotas::REVISAO;
        //                 $proposta->store();
        //             }

        //             // 4. Marcar somente as propostas realmente alteradas como revisão
        //             if ($propostas_alteradas) {
        //                   $repo = new TRepository('Propostas');
        //                   $criteria = new TCriteria;
        //                   $criteria->add(new TFilter('id', 'in', array_unique($propostas_alteradas)));

        //                   $propostas_revisao = $repo->load($criteria);
                        
        //                   foreach ($propostas_revisao as $pr) {
        //                     $pr->estado_pedido_frotas1_id = EstadoPedidoFrotas::REVISAO;
        //                     $pr->store();
        //                   }
        //             }

        //             // 5. Remover itens do pedido que não estão mais em nenhuma proposta
        //             $itenspedido = ItensPedidoFrotas::where('pedido_frotas_id', '=', $pedido_id)->load();

        //             foreach ($itenspedido as $ipf) {
        //                 $usado_em_proposta = ItensPropostas::where('itens_pedido_frotas_id', '=', $ipf->id)->count();

        //                 if ($usado_em_proposta == 0) {
        //                     $ipf->delete(); // ou soft delete
        //                 }
        //             }

        //             // 6. Marcar o PedidoFrotas como em revisão
        //             $pedido = new PedidoFrotas($pedido_id);
        //             $pedido->estado_pedido_frotas1_id = EstadoPedidoFrotas::REVISAO;
        //             $pedido->store();
        //         }

        //         TTransaction::close();

        // }


        // // }
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

    public static function manageRow($id, $param = [])
    {
        $list = new self($param);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Propostas($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }
        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
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
            $page->setProperty('page-name', 'PropostasDisponiveisListSearch');
            $page->setProperty('page_name', 'PropostasDisponiveisListSearch');
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
}