<?php

use Adianti\Registry\TSession;

class CotacaoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Cotacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_CotacaoList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

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

        $basename   = urlencode('centrocusto-list.pdf');
        $download   = "download.php?file=app/manual/centrocusto-list.pdf&basename={$basename}";

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
        $this->form->setFormTitle("Listagem de cotacoes {$manual}");
        $this->limit = 20;

        $criteria_pedido_id = new TCriteria();
        $criteria_descricao_pedido = new TCriteria();
        $criteria_estado_pedido_id = new TCriteria();

        $login = new LoginForm([]);
        $AlertMensagem = $login->onMensagem('CotacaoList');
        $TAlert = new TAlert('danger',$AlertMensagem);
        $id = new TEntry('id');
        $pedido_id = new TEntry('pedido_id');
        $descricaopedido = new TDBCombo('descricaopedido', 'minierp', 'Pedido', 'id', '{descricaopedido}','id asc' , $criteria_descricao_pedido );
        $obs = new THidden('obs');
        $estado_pedido_id = new TDBMultiSearch('estado_pedido_id', 'minierp', 'EstadoPedido', 'id', 'nome', 'nome asc', $criteria_estado_pedido_id );
        $data_cotacao = new TDate('data_cotacao');


        $data_cotacao->setMask('dd/mm/yyyy');
        $data_cotacao->setDatabaseMask('yyyy-mm-dd');
        $descricaopedido->enableSearch();
        $estado_pedido_id->setMinLength(0);
        $estado_pedido_id->setMask('{nome}');

        $id->setSize(100);
        $obs->setSize(200);
        $descricaopedido->setSize('100%');
        $data_cotacao->setSize(110);
        $estado_pedido_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Pedido id:", null, '14px', null, '100%'),$pedido_id],[new TLabel("Descricao Pedido:", null, '14px', null, '100%'),$descricaopedido]);
        $row1->layout = ['col-sm-3','col-sm-3','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Rotulo:", null, '14px', null),$obs],[]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Estado pedido", null, '14px', null, '100%'),$estado_pedido_id],[new TLabel("Data cotacao:", null, '14px', null, '100%'),$data_cotacao]);
        $row3->layout = ['col-sm-6','col-sm-6'];

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
        $this->datagrid->setHeight(250);
        
        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_pedido_id = new TDataGridColumn('pedido_id', "Pedido id", 'left');
        $column_pessoa_nome = new TDataGridColumn('pessoa->nome', "Fornecedor", 'left');
        $column_data_cotacao_transformed = new TDataGridColumn('data_cotacao', "Data cotacao", 'left');
        $column_dt_limite_resposta_transformed = new TDataGridColumn('data_limite_resposta', "Dt Limite Resposta", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_total', "Valor Total", 'left');
        $column_valor_desconto_transformed = new TDataGridColumn('valor_desconto', 'Valor Desconto', 'left');
        $column__transformed = new TDataGridColumn('', "%", 'left');
        $column_valor_liquido_transformed = new TDataGridColumn('valor_liquido', "Valor liquido", 'left');
        $column_estado_pedido_nome_transformed = new TDataGridColumn('estado_pedido->nome', "Estado pedido id", 'left');
        $column_system_users_name = new TDataGridColumn('system_users->name', "Usuario", 'left');
        $column_cidade_id_transformed = new TDataGridColumn('cidade_id', "Cidade", 'left');

        $column_dt_limite_resposta_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (!empty(trim((string) $value))) {
                try {
                    $date = new DateTime($value);
                    $formatted = $date->format('d/m/Y');

                    // Comparar somente datas (sem hora)
                    $hoje = new DateTime(date('Y-m-d'));
                    $dataLimite = new DateTime($date->format('Y-m-d'));

                    $span = new TElement('span');
                    $span->add($formatted);

                    if ($dataLimite < $hoje) {
                        // Expirada â†’ vermelho
                        $span->style = 'color:red; font-weight:bold;';
                    } else {
                        // Dentro do prazo â†’ verde
                        $span->style = 'color:green;';
                    }

                    return $span;
                } catch (Exception $e) {
                    return $value;
                }
            }
        });
        $column__transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
           $taxas =  TSession::getValue('taxacontrato');

            return number_format($taxas, 2).'%';
        });
        $column_data_cotacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_valor_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_estado_pedido_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
                                    $temnotafiscal = false;

            if ($object->estado_pedido::FINALIZADO || $object->estado_pedido::APROVADO || $object->estado_pedido::PGTOAPROVADO || $object->estado_pedido::ENTREGUE ) {
                // var_dump($object);
            //die();  
                TTransaction::open('minierp');

                $doccot = DocumentosCotacao::where('cotacao_id','=',$object->id)
                                           ->load();
                if ($doccot){
                    $temnotafiscal = true;
                }

                TTransaction::close();
            }
            //entrou em revisÃ£o 
            $revisao = '';
            if (($object->estado_pedido1_id !== null) &&  (TSession::getValue('testar_revisao')==1))
           {
                $estadorevisao = new EstadoPedido($object->estado_pedido1_id);
                $revisao = "<span style='font-size: 10px; font-style: italic; color: #eee;'>({$estadorevisao->nome})</span>";
            }

            if ($temnotafiscal) {
                $anexo = $object->estado_pedido->nome . " <i class='fa fa-paperclip' aria-hidden='true'></i>";
            } else {
                $anexo = $object->estado_pedido->nome;
            }

            return "<span class='label label-default' style='width:250px; background-color:{$object->estado_pedido->cor}; display:inline-block;'> {$anexo} {$revisao} </span>";           

        });

        $column_cidade_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here

                TTransaction::open('minierp');

                $cidade = new Cidade($object->cidade_id);
                if ($cidade) {
                    $estado = new Estado($cidade->estado_id);
                    return "{$cidade->nome} - {$estado->sigla}";

                } else {
                    return "Nao informado!!!";

                }

                TTransaction::close();

        });        

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_pedido_id);
        $this->datagrid->addColumn($column_pessoa_nome);
        $this->datagrid->addColumn($column_data_cotacao_transformed);
        $this->datagrid->addColumn($column_dt_limite_resposta_transformed);
        $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column_valor_desconto_transformed);
        $this->datagrid->addColumn($column__transformed);
        $this->datagrid->addColumn($column_valor_liquido_transformed);
        $this->datagrid->addColumn($column_estado_pedido_nome_transformed);
        $this->datagrid->addColumn($column_system_users_name);
        $this->datagrid->addColumn($column_cidade_id_transformed);

        $action_onEdit = new TDataGridAction(array('CotacaoForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);
        $action_onEdit->setDisplayCondition('CotacaoList::onExibirEditar');

        $this->datagrid->addAction($action_onEdit);

        $action_onGerarItens = new TDataGridAction(array('CotacaoList', 'onGerarItens'));
        $action_onGerarItens->setUseButton(false);
        $action_onGerarItens->setButtonClass('btn btn-default btn-sm');
        $action_onGerarItens->setLabel("Gerar itens da cotacao");
        $action_onGerarItens->setImage('fas:cogs #03A9F4');
        $action_onGerarItens->setField(self::$primaryKey);
        $action_onGerarItens->setDisplayCondition('CotacaoList::onExibirGerar');

        $this->datagrid->addAction($action_onGerarItens);

        $action_onSetProject = new TDataGridAction(array('CotacaoFormEmailVenda', 'onSetProject'));
        $action_onSetProject->setUseButton(false);
        $action_onSetProject->setButtonClass('btn btn-default btn-sm');
        $action_onSetProject->setLabel("Enviar proposta");
        $action_onSetProject->setImage('fas:envelope #E91E63');
        $action_onSetProject->setField(self::$primaryKey);
        $action_onSetProject->setDisplayCondition('CotacaoList::onExibirEnviarProposta');

        $this->datagrid->addAction($action_onSetProject);

        $action_onImprimir = new TDataGridAction(array('CotacaoList', 'onImprimir'));
        $action_onImprimir->setUseButton(false);
        $action_onImprimir->setButtonClass('btn btn-default btn-sm');
        $action_onImprimir->setLabel("Documento");
        $action_onImprimir->setImage('far:file-pdf #000000');
        $action_onImprimir->setField(self::$primaryKey);

        $action_onImprimir->setParameter('id', '{id}');

        $this->datagrid->addAction($action_onImprimir);

         $action_onEntregar = new TDataGridAction(array('CotacaoList', 'onConfirmarEntrega'));
        $action_onEntregar->setUseButton(false);
        $action_onEntregar->setButtonClass('btn btn-default btn-sm');
        $action_onEntregar->setLabel("Registrar Entrega");
        $action_onEntregar->setImage('fas:box #03A9F4');
        $action_onEntregar->setField(self::$primaryKey);
        $action_onEntregar->setDisplayCondition('CotacaoList::onExibirRegistrarEntregaProduto');

        $action_onEntregar->setParameter('id', '{id}');

        $this->datagrid->addAction($action_onEntregar);



         $action_onVerAnexos = new TDataGridAction(array('DocumentosPedidoSimpleList', 'onSetProject'));
        $action_onVerAnexos->setUseButton(false);
        $action_onVerAnexos->setButtonClass('btn btn-default btn-sm');
        $action_onVerAnexos->setLabel("Ver Anexos");
        $action_onVerAnexos->setImage('fas:file-alt #FF9800');
        $action_onVerAnexos->setField(self::$primaryKey);

        $action_onVerAnexos->setParameter('id', '{id}');
        $action_onVerAnexos->setParameter('pedido_id', '{pedido_id}');
        $this->datagrid->addAction($action_onVerAnexos);



                $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['CotacaoList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['CotacaoList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $action_DocumentosCotacaoList_onSetProject = new TDataGridAction(array('DocumentosCotacaoList', 'onSetProject'));
        $action_DocumentosCotacaoList_onSetProject->setUseButton(false);
        $action_DocumentosCotacaoList_onSetProject->setButtonClass('btn btn-default btn-sm');
        $action_DocumentosCotacaoList_onSetProject->setLabel("Anexar Nota Fiscal");
        $action_DocumentosCotacaoList_onSetProject->setImage('fas:paperclip #4CAF50');
        $action_DocumentosCotacaoList_onSetProject->setField(self::$primaryKey);

        $action_DocumentosCotacaoList_onSetProject->setParameter('cot', '{id}');

        $this->datagrid->addAction($action_DocumentosCotacaoList_onSetProject);

        $this->applyDatagridProperties();

        // create the datagrid model
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

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['CotacaoList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['CotacaoList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['CotacaoList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['CotacaoList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_right_actions->add($dropdown_button_exportar);
        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_limpar_filtros);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
           //$container->add(TBreadCrumb::create(["Compras","CotaÃ§Ãµes disponiveis"]));
            if (!empty($AlertMensagem)) {
                $container->add($TAlert);
           } 
        }
     //   $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public static function onExibirEditar($object)
    {
        try 
        {
           if(in_array($object->estado_pedido_id, [EstadoPedido::NAOENVIADO, EstadoPedido::AGUARDANDO]) )
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

    public static function onExibirRegistrarEntregaProduto($object)
    {
        try 
        {
           if(in_array($object->estado_pedido_id, [EstadoPedido::APROVADO]) )
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
    public function onGerarItens($param = null) 
    {
        try 
        {
            //code here

             TTransaction::open(self::$database);

             //  $key=$param['key'];
             $object = new Cotacao($param['id'], FALSE); 

             $pedidocompras = new Pedido($object->pedido_id);

             // Verifica a data limite
            if ($pedidocompras->data_limite_resposta < date('Y-m-d')) {
                new TMessage('error', 'Data limite de responder o pedido ja foi atingida!');
                TTransaction::close();
                return;
            }


             $itenspedido = ItensPedido::where('pedido_venda_id','=',$object->pedido_id)
                                          ->load();
             $itensPedidoPorId = [];
             $itensPedidoPorProduto = [];
             if ($itenspedido)
             {
                foreach ($itenspedido as $itemPedido)
                {
                    $itensPedidoPorId[(int) $itemPedido->id] = $itemPedido;
                    $itensPedidoPorProduto[(int) $itemPedido->produto_id] = $itemPedido;
                }
             }
             $geroualgumitem=0;
            if ($itenspedido)
            { 
               foreach ($itenspedido as $itensp)
               {  
                   $itensx = ItensCotacao::where('cotacao_id','=',$object->id)
                                         ->where('itens_pedido_id','=',$itensp->id)
                                         ->load();

                    if (!$itensx){
                        $itensx = ItensCotacao::where('cotacao_id','=',$object->id)
                                              ->where('produto_id','=',$itensp->produto_id)
                                              ->load();
                    }

                    if (!$itensx){
                        $itenscotacao = new ItensCotacao();
                        $itenscotacao->produto_id = $itensp->produto_id;
                        $itenscotacao->qtde       = $itensp->quantidade;
                    //    $itenscotacao->valor      = $itensp->valor;
                        $itenscotacao->unidade_medida_id      = $itensp->unidade_medida_id;
                        $itenscotacao->cotacao_id = $object->id;
                        $itenscotacao->itens_pedido_id = $itensp->id;
                        $itenscotacao->store();
                    } else {
                        foreach ($itensx as $itemExistente)
                        {
                            $alterou = false;

                            if ((int) $itemExistente->itens_pedido_id !== (int) $itensp->id)
                            {
                                $itemExistente->itens_pedido_id = $itensp->id;
                                $alterou = true;
                            }

                            if ((int) $itemExistente->produto_id !== (int) $itensp->produto_id)
                            {
                                $itemExistente->produto_id = $itensp->produto_id;
                                $alterou = true;
                            }

                            if ((float) $itemExistente->qtde !== (float) $itensp->quantidade)
                            {
                                $itemExistente->qtde = $itensp->quantidade;
                                $alterou = true;
                            }

                            if ((int) $itemExistente->unidade_medida_id !== (int) $itensp->unidade_medida_id)
                            {
                                $itemExistente->unidade_medida_id = $itensp->unidade_medida_id;
                                $alterou = true;
                            }

                            if ($alterou)
                            {
                                $itemExistente->store();
                            }
                        }

                        $geroualgumitem=1;
                    }
               }

            }
            
                           
                $itenscotx = ItensCotacao::where('cotacao_id','=',$object->id)
                                         ->load();
                if ($itenscotx) {
                    foreach ($itenscotx as $icx) {
                        $itemPedidoVinculado = null;

                        if (!empty($icx->itens_pedido_id) && isset($itensPedidoPorId[(int) $icx->itens_pedido_id]))
                        {
                            $itemPedidoVinculado = $itensPedidoPorId[(int) $icx->itens_pedido_id];
                        }
                        elseif (isset($itensPedidoPorProduto[(int) $icx->produto_id]))
                        {
                            $itemPedidoVinculado = $itensPedidoPorProduto[(int) $icx->produto_id];
                        }

                        if (!$itemPedidoVinculado)               
                        {
                           $icx->delete();

                        } else {
                        $alterou = false;

                        if ((int) $icx->itens_pedido_id !== (int) $itemPedidoVinculado->id) {
                           $icx->itens_pedido_id = $itemPedidoVinculado->id;
                           $alterou = true;
                        }

                        if ((int) $icx->produto_id !== (int) $itemPedidoVinculado->produto_id) {
                           $icx->produto_id = $itemPedidoVinculado->produto_id;
                           $alterou = true;
                        }

                        if ((float) $icx->qtde !== (float) $itemPedidoVinculado->quantidade){
                           $icx->qtde = $itemPedidoVinculado->quantidade;
                           $alterou = true;
                        }

                        if ((int) $icx->unidade_medida_id !== (int) $itemPedidoVinculado->unidade_medida_id){
                           $icx->unidade_medida_id = $itemPedidoVinculado->unidade_medida_id;
                           $alterou = true;
                        }

                        if ($alterou) {
                           $icx->store();
                        }

                        }
                    }

                }
                

            if ($geroualgumitem==0) {
                $object->datacotacao = date('Y-m-d');
                $object->estado_pedido_id = EstadoPedido::NAOENVIADO;
                $object->system_users_id = TSession::getValue('iduser');
                $object->store();
   
                $this->registrarHistoricoCotacao($object);
   
   
            }
          //  var_dump($param,$object);
            new TMessage('info', 'Itens da Cotacao gerados com sucesso!');
         //   } else {
        //    new TMessage('info', 'Itens da CotaÃ§Ã£o ja foi gerado!');

      //      }
            TTransaction::close();
            $this->onReload();

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

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }
    public static function onExibirGerar($object)
    {
        try 
        {
           if( in_array($object->estado_pedido_id, [EstadoPedido::PENDENTE, EstadoPedido::NAOENVIADO, EstadoPedido::AGUARDANDO]) )
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
            if( in_array($object->estado_pedido_id, [EstadoPedido::NAOENVIADO]) )
            {
                TTransaction::open('minierp');
               $itenscotacao=ItensCotacao::where('cotacao_id','=',$object->id)    
                                         ->load();
                $semvalores=1;
                if ($itenscotacao)
                {
                    foreach ($itenscotacao as $itens)
                    {
                        if ($itens->valor>0){
                          $semvalores=0;
                        }
                   }
                }
               TTransaction::close();
               if ($semvalores==1){
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
    public function onImprimir($param = null) 
    {
        try 
         {
           include 'app/control/compras/qrcode.php';
                 TTransaction::open('minierp');

                 $conn = TConnection::open('minierp');
                 $pdf = new FPDF("L","pt","A4");

                 $linha=0;   
                 $pag=1;
                 $alturalinha=255;
                 $unidade='';
                 $qt = 0;
                 $vl = 0;
                 $vlt = 0;
                 $qtitens=0;

                 $obj = new Cotacao($param['id']);
                 $pedido = new Pedido($obj->pedido_id);

                 $itenscotacao = ItensCotacao::where('cotacao_id','=',$param['id'])->load();
                 if ($itenscotacao) {
                     $gruposItens = [
                        'produtos' => ['titulo' => 'PRODUTOS', 'itens' => []],
                        'servicos' => ['titulo' => 'SERVICOS', 'itens' => []],
                     ];

                     foreach ($itenscotacao as $itemCotacao) {
                        $produtoItem = new Produto($itemCotacao->produto_id);
                        $tipoProdutoId = (int) ($produtoItem->tipo_produto_id ?? 0);
                        $chaveGrupo = ($tipoProdutoId === 2) ? 'servicos' : 'produtos';
                        $gruposItens[$chaveGrupo]['itens'][] = [
                            'item' => $itemCotacao,
                            'produto' => $produtoItem
                        ];
                     }

                     if ( ($linha==0) || ($linha >= 33) ){
                       $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id);
                       $pag=$pag + 1; 
                       $alturalinha = 255;
                       $linha = 12;
                    }

                     foreach (['produtos', 'servicos'] as $grupoKey) {
                         if (empty($gruposItens[$grupoKey]['itens'])) {
                             continue;
                         }

                         if ( ($linha==0) || ($linha >= 33) ){
                            $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id);
                           $linha = 12;
                           $pag=$pag + 1; 
                           $alturalinha = 255;
                         }

                         $pdf->SetFont('arial','B',9);
                         $pdf->SetFillColor(220, 226, 230);
                         $pdf->Rect(26, $alturalinha-4, 542, 14, 'F');
                         $pdf->SetXY(30,$alturalinha);
                         $pdf->Cell(200,5,utf8_decode('ITENS - '.$gruposItens[$grupoKey]['titulo']),0,1,'L');
                         $alturalinha += 18;
                         $linha += 1;

                         $qtGrupo = 0;
                         $vlGrupo = 0;
                         $vltGrupo = 0;
                         $qtitensGrupo = 0;

                         foreach ($gruposItens[$grupoKey]['itens'] as $registro) {
                             $itens = $registro['item'];
                             $produto = $registro['produto'];

                             if ( ($linha==0) || ($linha >= 33) ){
                                $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id);
                               $linha = 12;
                               $pag=$pag + 1; 
                               $alturalinha = 255;

                               $pdf->SetFont('arial','B',9);
                               $pdf->SetFillColor(220, 226, 230);
                               $pdf->Rect(26, $alturalinha-4, 542, 14, 'F');
                               $pdf->SetXY(30,$alturalinha);
                               $pdf->Cell(200,5,utf8_decode('ITENS - '.$gruposItens[$grupoKey]['titulo'].' (continua)'),0,1,'L');
                               $alturalinha += 18;
                               $linha += 1;
                             }

                             $pdf->setFont('arial','',7);   
                             $pdf->SetXY(22,$alturalinha);
                             $pdf->Cell(70,5,utf8_decode($produto->id),0,1,'L');

                             $nome_produto = utf8_decode($produto->nome);
                             $largura_nome = 340;
                             $altura_linha = 8;
                             $max_linhas = 3;
                             $linhas_necessarias = ceil($pdf->GetStringWidth($nome_produto) / $largura_nome);

                             if ($linhas_necessarias > $max_linhas) {
                                 $nome_cortado = mb_strimwidth($nome_produto, 0, 200, '...');
                             } else {
                                 $nome_cortado = $nome_produto;
                             }

                             $pdf->SetXY(47, $alturalinha-1);
                             $pdf->MultiCell($largura_nome, $altura_linha, $nome_cortado, 0, 'L', false);

                             $pdf->SetFont('arial', '', 7);
                             $pdf->SetXY(350, $alturalinha);
                             $pdf->Cell(70, 5, $produto->unidade_medida->nome ?? '', 0, 1, 'C');
                             $pdf->SetXY(380, $alturalinha);
                             $pdf->Cell(70, 5, $itens->qtde, 0, 1, 'C');
                             $pdf->SetXY(420, $alturalinha);
                             $pdf->Cell(70, 5, 'R$ ' . number_format($itens->valor, 2), 0, 1, 'R');
                             $pdf->SetXY(490, $alturalinha);
                             $pdf->Cell(70, 5, 'R$ ' . number_format($itens->valor_total, 2), 0, 1, 'R');

                             $alturalinha += 24; 
                             $linha +=1;
                             $pdf->ln(1); 

                             $qtitens++;
                             $qt  += $itens->qtde;
                             $vl  += $itens->valor;
                             $vlt += $itens->valor_total;
                             $qtitensGrupo++;
                             $qtGrupo += $itens->qtde;
                             $vlGrupo += $itens->valor;
                             $vltGrupo += $itens->valor_total;
                         }

                         $alturalinha += 4;
                         $pdf->SetFont('arial','B',8);
                         $pdf->SetFillColor(245,247,248);
                         $pdf->Rect(26, $alturalinha-2, 542, 14, 'F');
                         $pdf->SetXY(30,$alturalinha+1);
                         $pdf->Cell(180,5,utf8_decode('Subtotal '.$gruposItens[$grupoKey]['titulo'].' ('.$qtitensGrupo.' itens)'),0,1,'L');
                         $pdf->SetXY(360,$alturalinha+1);
                         $pdf->Cell(70,5,$qtGrupo,0,1,'C');
                         $pdf->SetXY(420,$alturalinha+1);
                         $pdf->Cell(70,5,'R$ '.number_format($vlGrupo, 2),0,1,'R');
                         $pdf->SetXY(490,$alturalinha+1);
                         $pdf->Cell(70,5,'R$ '.number_format($vltGrupo, 2),0,1,'R');
                         $alturalinha += 20;
                         $linha +=1;
                     }

                     $alturalinha+=15;
                     $pdf->SetFont('arial','B',10); 
                     $pdf->SetXY(25,$alturalinha);
                     $pdf->SetFillColor(235,239,240);
                     $pdf->Rect(26, $alturalinha-5, 542, 15, 'F');
                     $pdf->Cell(70,5,utf8_decode('Total Geral '.$qtitens.' Itens'),0,1,'L');

                     $pdf->SetXY(360,$alturalinha);
                     $pdf->Cell(70,5,$qt,0,1,'C');
                     $pdf->SetXY(420,$alturalinha);
                     $pdf->Cell(70,5,'R$ '.number_format($vl, 2),0,1,'R');
                     $pdf->SetXY(490,$alturalinha);
                     $pdf->Cell(70,5,'R$ '.number_format($vlt, 2),0,1,'R');

                     $pdf->Cell(0,15,'','B',1,'C');

                     $pdf->SetFont('arial','I',10); 
                     $alturalinha+=26;
                     $pdf->SetXY(82,$alturalinha);
                     $pdf->Cell(70,5,'Valor Bruto: '.'R$ '.number_format($vlt, 2),0,1,'R');

                     $pdf->SetXY(320,$alturalinha);
                     $taxas = ((TSession::getValue('taxacontrato'))) ;
                     $desconto = ($vlt * $taxas) / 100;
                     $pdf->Cell(70,5,'Valor Desconto: ('.number_format($taxas, 2).'%)'.' R$ '.number_format($desconto, 2),0,1,'R');

                     $pdf->SetXY(490,$alturalinha);
                     $liquido = $vlt - $desconto;
                     $pdf->Cell(70,5,'Valor Liquido: '.'R$ '.number_format($liquido, 2),0,1,'R');

                     $alturalinha+=25;
                     $pdf->Cell(0,15,'','B',1,'C');
                     $pdf->SetXY(45,$alturalinha);
                     $pdf->Cell(70,5,utf8_decode('Legenda: P: PRE-APROVADO; A: APROVADO; R: REPROVADO '),0,1,'L');

                     $text = $pedido->id.'.png';
                     $file = "app/documents/{$text}";
                     $options = array(
                            'w' => 500,
                            'h' => 500
                     );

                     $generator = new QRCode($pedido->id, $options);
                     $image = $generator->render_image();
                     imagepng($image, $file);
                     $pdf->SetXY(255,750);
                     $pdf->Cell(70,5,'Agora falta pouco, escaneie o QR Code para efetuar a entrega dos seus produtos.',0,1,'C');
                     $pdf->Image('app/documents/'.$pedido->id.'.png', 250, 760, 80);
                 }

                 $nome = 'documentocotacao.pdf';

                 if (!file_exists("app/output/{$nome}") OR is_writable("app/output/{$nome}"))
                 {
                    $pdf->Output("app/output/{$nome}","F");
                 }
                 else
                 {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/{$nome}");
                 }

                 parent::openFile("app/output/{$nome}");
                 new TMessage('info', 'Pedidos gerado com sucesso. Por favor, habilite popups no navegador.');
                 TTransaction::close();
             }
             catch (Exception $e) 
             {
                 new TMessage('error', $e->getMessage());    
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

        if (isset($data->pedido_id) AND ( (is_scalar($data->pedido_id) AND $data->pedido_id !== '') OR (is_array($data->pedido_id) AND (!empty($data->pedido_id)) )) )
        {

            $filters[] = new TFilter('pedido_id', '=', $data->pedido_id);// create the filter 
        }

        if (isset($data->descricaopedido) AND ( (is_scalar($data->descricaopedido) AND $data->descricaopedido !== '') OR (is_array($data->descricaopedido) AND (!empty($data->descricaopedido)) )) )
        {

            $filters[] = new TFilter('pedido_id', '=', $data->descricaopedido);// create the filter 
        }

        if (isset($data->obs) AND ( (is_scalar($data->obs) AND $data->obs !== '') OR (is_array($data->obs) AND (!empty($data->obs)) )) )
        {

            $filters[] = new TFilter('obs', 'like', "%{$data->obs}%");// create the filter 
        }

        if (isset($data->estado_pedido_id) AND ( (is_scalar($data->estado_pedido_id) AND $data->estado_pedido_id !== '') OR (is_array($data->estado_pedido_id) AND (!empty($data->estado_pedido_id)) )) )
        {

            $filters[] = new TFilter('estado_pedido_id', 'in', $data->estado_pedido_id);// create the filter 
        }

        if (isset($data->data_cotacao) AND ( (is_scalar($data->data_cotacao) AND $data->data_cotacao !== '') OR (is_array($data->data_cotacao) AND (!empty($data->data_cotacao)) )) )
        {

            $filters[] = new TFilter('data_cotacao', '=', $data->data_cotacao);// create the filter 
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

            // creates a repository for Cotacao
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

            $pessoa = Pessoa::where('system_user_id','=',TSession::getValue('userid')) // system_user_id do fornecedor
                            ->load();
            $grupo = SystemUserGroup::where('system_user_id','=',TSession::getValue('userid'))
                                ->where('system_group_id','=',1 ) //admin
                            ->load();
          //  var_dump($pessoa);
            if ($pessoa) {
                foreach($pessoa as $pe){
                    $codpessoa = $pe->id;
                }
                if ($grupo){
                } else {
                   if ($pessoa){
                       //var_dump($pessoa);
                      $criteria->add(new TFilter('pessoa_id', '=', $codpessoa));
                      $taxacontrato = TSession::getValue('taxacontrato');
//                      $txbancaria = $pe->taxabancaria / 100;
//                      $vltaxas = ($pe->taxaadm + $pe->taxaantecipacao + $pe->taxacontrato + $txbancaria) ;
                      $vltaxas = ($taxacontrato / 100);
                      
                    }
                }
            }

            // // Subgrupo 1: estado diferente de PENDENTE
            // $notPending = new TCriteria();
            // $notPending->add(new TFilter('estado_pedido_id', '!=', EstadoPedidoFrotas::PENDENTE));

            // // Subgrupo 2: estado igual a PENDENTE E data_limite_resposta <= hoje
            // $pendingWithDate = new TCriteria();
            // $pendingWithDate->add(new TFilter('estado_pedido_id', '=', EstadoPedidoFrotas::PENDENTE));
            // $pendingWithDate->add(new TFilter('data_limite_resposta', '>=', date('Y-m-d')));

            // // Combina os dois subgrupos com OR
            // $estadoComData = new TCriteria();
            // $estadoComData->add($notPending, TExpression::OR_OPERATOR);
            // $estadoComData->add($pendingWithDate, TExpression::OR_OPERATOR);

            // // Aplica ao critÃ©rio principal
            // $criteria->add($estadoComData);
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit'))); // filtra pela unidade do usuário logado

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

            if ($objects) {
                foreach ($objects as $object) {
                    $pedido = new Pedido($object->pedido_id);
                    $estadoValido = in_array($pedido->estado_pedido_venda_id, [
                        EstadoPedido::APROVADO,
                        EstadoPedido::FINALIZADO,
                        EstadoPedido::PGTOAPROVADO,
                        EstadoPedido::ENTREGUE
                    ]);

                    if ($estadoValido) {
                        // Caso o cliente do pedido seja o mesmo da pessoa
                        if ($pedido->cliente_id == $object->pessoa_id) {
                            $row = $this->datagrid->addItem($object);
                            $row->id = "row_{$object->id}";
                        }
                        // Caso o cliente_id esteja vazio (NULL)
                        elseif (is_null($pedido->cliente_id)) {
                            $row = $this->datagrid->addItem($object);
                            $row->id = "row_{$object->id}";
                        }
                        // Se cliente_id for diferente da pessoa, nÃ£o adiciona (omitido)
                    } else {
                        // Caso o estado do pedido nÃ£o seja um dos vÃ¡lidos, ainda assim adiciona
                        $row = $this->datagrid->addItem($object);
                        $row->id = "row_{$object->id}";
                    }
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
            $this->onShow();
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

    public static function manageRow($id)
    {
        $list = new self([]);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Cotacao($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    private function registrarHistoricoCotacao($object)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $object->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::NAOENVIADO; 
      //  $histcotacao->aprovador_id = TSession::getValue('iduser');
        $histcotacao->store();
    }
    // public function onRegistrarEntregaProduto($param = null)
    // {
    //     try {
    //         $id = $param['id'];
    //           $cotacao = new Cotacao($id);
    //         $cotacao->estado_pedido_venda_id = EstadoPedido::ENTREGUE;
    //         $cotacao->store();

    //         TTransaction::open('minierp');
    //         $histcotacao = new CotacaoHistorico();
    //         $histcotacao->cotacao_id = $id;
    //         $histcotacao->data_historico = date('Y-m-d');
    //         $histcotacao->estado_pedido_id = EstadoPedido::ENTREGUE; 
    //         $histcotacao->store();
        
    //         $histpedido = new PedidoHistorico();
    //         $histpedido->pedido_venda_id = $cotaao->pedido_id;
    //         $histpedido->data_historico = date('Y-m-d');
    //         $histpedido->estado_pedido_id = EstadoPedido::ENTREGUE; 
    //         $histpedido->store();

    //         $pedido = new Pedido($cotacao->pedido_id);
    //         $pedido->estado_pedido_venda_id = EstadoPedido::ENTREGUE;
    //         $pedido->store();

          

    //         TTransaction::close();

    //     } catch (Exception $e) {
    //         new TMessage('error', $e->getMessage());
    //         return;
    //     }
      


    // }


    // 1) Acione esta aÃ§Ã£o a partir do botÃ£o/Ã­cone na lista ou formulÃ¡rio
    public function onConfirmarEntrega($param)
    {
        try {
            if (empty($param['id'])) {
                throw new Exception('ID da cotaÃ§Ã£o nÃ£o informado.');
            }

            $yes = new TAction([$this, 'onRegistrarEntregaProduto']);
            $yes->setParameters($param); // mantÃ©m id, etc.

            $no  = new TAction([$this, 'onCancelarConfirmacao']);
            $no->setParameters($param);

            new TQuestion('Confirma a entrega do produto para esta cotaÃ§Ã£o?', $yes, $no);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    // 2) Caso o usuÃ¡rio clique em "NÃ£o"
    public function onCancelarConfirmacao($param)
    {
        new TMessage('info', 'OperaÃ§Ã£o cancelada pelo usuÃ¡rio.');
    }

    // 3) Registro efetivo da entrega (corrigido e robusto)
    public function onRegistrarEntregaProduto($param = null)
    {
        try {
            if (empty($param['id'])) {
                throw new Exception('ID da cotaÃ§Ã£o nÃ£o informado.');
            }

            $id = (int) $param['id'];

            TTransaction::open('minierp');

            // Carrega a cotaÃ§Ã£o para alterar status
            $cotacao = new Cotacao($id);
            if (empty($cotacao->id)) {
                throw new Exception("CotaÃ§Ã£o #{$id} nÃ£o encontrada.");
            }

            // Atualiza status da cotaÃ§Ã£o
            $cotacao->estado_pedido_id = EstadoPedido::ENTREGUE;
            $cotacao->store();

            $agora = date('Y-m-d H:i:s');

            // HistÃ³rico da cotaÃ§Ã£o
            $histcotacao = new CotacaoHistorico();
            $histcotacao->cotacao_id       = $cotacao->id;
            $histcotacao->data_historico   = $agora;
            $histcotacao->estado_pedido_id = EstadoPedido::ENTREGUE;
            $histcotacao->observacao       = 'Entrega confirmada.'; // opcional, se existir o campo
            $histcotacao->store();

            // Se a cotaÃ§Ã£o estiver vinculada a um pedido, atualiza pedido e histÃ³rico
            if (!empty($cotacao->pedido_id)) {
                $pedido = new Pedido($cotacao->pedido_id);
                if (!empty($pedido->id)) {
                    $pedido->estado_pedido_venda_id = EstadoPedido::ENTREGUE;
                    $pedido->store();

                    $histpedido = new PedidoHistorico();
                    $histpedido->pedido_venda_id   = $pedido->id;
                    $histpedido->data_historico    = $agora;
                    $histpedido->estado_pedido_venda_id  = EstadoPedido::ENTREGUE;
                    $histpedido->observacao        = 'Entrega confirmada a partir da cotaÃ§Ã£o.'; // opcional
                    $histpedido->store();
                }
            }

            TTransaction::close();

           // new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            // Se precisar recarregar uma listagem:
            // TApplication::postData('form_AlgumaLista'); 
             $action = new TAction([$this, 'onReload']);
             new TMessage('info', 'Entrega confirmada com sucesso!', $action);

        } catch (Exception $e) {
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());
        }
    }


    private function cabecalhoDCot($pdf, $linha,$pag, $unidade, $id, $datacotacao, $idcot)
    {
        $label = '';
        $datacotacao = new DateTime($datacotacao);
        $datacotacao = $datacotacao->format('d/m/Y');

        $cot = new Cotacao($idcot);
        $ped = new Pedido($cot->pedido_id);
        $dep = new DepartamentoUnit($ped->departamento_unit_id);
        $unit = new SystemUnit($dep->system_unit_id);

        $pessoa = new Pessoa($cot->pessoa_id);                           
        $cnpj = $pessoa->documento;
        $nome = $pessoa->nome;

        $pessoa_endereco = PessoaEndereco::where('pessoa_id','=',$cot->pessoa_id)
                                         ->where('principal','=','T')
                                         ->load();
        $nomecidade = '';
        if ($pessoa_endereco) {
            foreach ($pessoa_endereco as $pe) {
            $cidade = new Cidade($pe->cidade_id);
            $estado = new Estado($cidade->estado_id);
            $nomecidade = $cidade->nome.'/'.$estado->sigla;
            }
        }

        $historicopedido = PedidoHistorico::where('pedido_venda_id','=',$ped->id)
                                          ->where('estado_pedido_venda_id','=',EstadoPedido::APROVADO)
                                          ->orderBy('data_operacao','desc')
                                          ->load();
        $usuario='';
        if ($historicopedido) {
            foreach($historicopedido as $histped) {
               $user = new SystemUsers($histped->aprovador_id);
               $usuario = $user->name;                
               break;              
            }
        } else {$usuario = '';}

        $pdf->AddPage();
        $pdf->SetFont('arial','B',10);

        $pdf->Image('app/images/logo.png', 25, 3, 80);
        $pdf->SetXY(300,40);
        $pdf->Cell(70,5,utf8_decode('Pedido de Compra: '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(350,40);
        $pdf->Cell(70,5,'#'.$id,0,1,'R');

        $pdf->SetFont('arial','B',10);
        $pdf->SetXY(445,40);
       $pdf->Cell(70,5,utf8_decode('Cotação de Venda: '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(500,40);
        $pdf->Cell(70,5,'#'.$idcot,0,1,'R');
        $pdf->Ln(4);

        $pdf->SetFont('arial','B',10);
        $pdf->SetXY(425,55);
       $pdf->Cell(70,5,utf8_decode('Data da Cotação: '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(500,55);
        $pdf->Cell(70,5,$datacotacao,0,1,'R');
        $pdf->ln(1);

        $pdf->SetFont('arial','B',10); 
        $pdf->SetXY(500,70);
       $pdf->Cell(70,5,utf8_decode(' Página: '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(500,70);
        $pdf->Cell(70,5,$pag,0,1,'R');
        $pdf->ln(1);

        $pdf->Cell(0,5,"","B",1,'C');
        $pdf->SetFont('arial','B',10); 
        $pdf->SetXY(25,100);
        $pdf->SetFillColor(235,239,240);
        $pdf->Rect(26, 95, 542, 15, 'F');
       $pdf->Cell(70,5,utf8_decode('Dados da Cotação - '.$unit->name),0,1,'L');

        $pdf->SetXY(25,118);
       $pdf->Cell(70,5,utf8_decode('Descrição do Pedido '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(25,133);
        $pdf->Cell(70,5,utf8_decode($ped->descricaopedido),0,1,'L');

        $pdf->SetFont('arial','B',10);
        $pdf->SetXY(355,118);
        $pdf->Cell(70,5,utf8_decode('Departamento '),0,1,'L');
        $pdf->SetFont('arial','',8);
        $pdf->SetXY(355,133);
        $depNome = utf8_decode((string) ($dep->name ?? ''));
        $larguraMaxDep = 210;
        while ($depNome !== '' && $pdf->GetStringWidth($depNome) > $larguraMaxDep)
        {
            $depNome = substr($depNome, 0, -1);
        }
        if ($depNome !== utf8_decode((string) ($dep->name ?? '')))
        {
            $depNome = rtrim($depNome) . '...';
            while ($depNome !== '...' && $pdf->GetStringWidth($depNome) > $larguraMaxDep)
            {
                $depNome = substr($depNome, 0, -4) . '...';
            }
        }
        $pdf->Cell($larguraMaxDep,5,$depNome,0,1,'L');

        $pdf->SetFont('arial','B',10);
        $pdf->SetXY(25,148);
        $pdf->Cell(70,5,utf8_decode('Fornecedor '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(25,163);
        $pdf->Cell(70,5,utf8_decode($cnpj.' - '.substr($nome,0,38)),0,1,'L');
        $pdf->SetXY(25,178);
        $pdf->Cell(70,5,utf8_decode($nomecidade),0,1,'L');

        $pdf->SetFont('arial','B',10);
        $pdf->SetXY(355,148);
        $pdf->Cell(70,5,utf8_decode('Autorizador por '),0,1,'L');
        $pdf->SetFont('arial','',10);
        $pdf->SetXY(355,163);
        $pdf->Cell(70,5,utf8_decode($usuario),0,1,'L');

        $pdf->SetFont('arial','B',10); 
        $pdf->SetXY(25,208);
        $pdf->SetFillColor(235,239,240);
        $pdf->Rect(26, 203, 542, 15, 'F');
       $pdf->Cell(70,5,utf8_decode('Itens da Cotação '),0,1,'L');

        $pdf->SetFont('arial','B',10); 
        $pdf->SetFillColor(149,192,230);
        $pdf->Rect(26, 233, 542, 15, 'F');

        $pdf->SetXY(25,238);
        $pdf->Cell(70,5,utf8_decode('ID'),0,1,'L');
        $pdf->SetXY(45,238);
        $pdf->Cell(70,5,utf8_decode('Descrição '),0,1,'L');
        $pdf->SetXY(350,238);
        $pdf->Cell(70,5,utf8_decode('Und'),0,1,'C');
        $pdf->SetXY(380,238);
        $pdf->Cell(70,5,utf8_decode('Qtde'),0,1,'C');
        $pdf->SetXY(420,238);
        $pdf->Cell(70,5,utf8_decode('Valor'),0,1,'R');
        $pdf->SetXY(490,238);
        $pdf->Cell(70,5,utf8_decode('Valor Total'),0,1,'R');

        $pdf->ln(1);
     }
     function onSetProject($param=null){

        TSession::setValue('idcotanexo',null);
        TSession::setValue('idcotanexo',$param['cot']);
        $this->onReload();
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
            $page->setProperty('page-name', 'CotacaoListSearch');
            $page->setProperty('page_name', 'CotacaoListSearch');
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

