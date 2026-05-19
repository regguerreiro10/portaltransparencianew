<?php

use Adianti\Registry\TSession;

class DashboardPedidoVenda extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_DashboardPedidoVenda';

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

        $basename   = urlencode('dashboard-pedido-list.pdf');
        $download   = "download.php?file=app/manual/dashboard-pedido-list.pdf&basename={$basename}";

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
        $this->form->setFormTitle("Dashboard {$manual}");

        $criteria_pedidos_em_elaboracao = new TCriteria();
        $criteria_pedidos_em_analise_comercial = new TCriteria();
        $criteria_pedidos_em_anlise_de_credito = new TCriteria();
        $criteria_pedidos_em_processamento = new TCriteria();
        $criteria_pedidos_em_faturamento = new TCriteria();
        $criteria_pedidos_aguardando_entrega = new TCriteria();
        $criteria_pedidos_finalizados = new TCriteria();
        $criteria_pedidos_cancelados = new TCriteria();
        $criteria_valor_empenho = new TCriteria();
        $criteria_valores_consumidos = new TCriteria();
        $criteria_saldo_atual = new TCriteria();
        $criteria_total_de_vendas_por_mes = new TCriteria();
        $criteria_total_de_vendas_por_dia = new TCriteria();
        $criteria_total_por_cliente = new TCriteria();

        $idUnit = TSession::getValue('idunit');

        $criteria_pedidos_em_elaboracao->add(new TFilter('pedido.system_unit_id', '=', $idUnit));
        $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido.system_unit_id', '=', $idUnit));
        $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido.system_unit_id', '=', $idUnit));
        $criteria_pedidos_em_processamento->add(new TFilter('pedido.system_unit_id', '=', $idUnit));
        $criteria_pedidos_em_faturamento->add(new TFilter('pedido.system_unit_id', '=', $idUnit));
        $criteria_pedidos_aguardando_entrega->add(new TFilter('pedido.system_unit_id', '=', $idUnit));
        $criteria_pedidos_finalizados->add(new TFilter('pedido.system_unit_id', '=', $idUnit));
        $criteria_pedidos_cancelados->add(new TFilter('pedido.system_unit_id', '=', $idUnit));

        $criteria_valores_consumidos->add(new TFilter('pedido.system_unit_id', '=', $idUnit));
        $criteria_saldo_atual->add(new TFilter('pedido.system_unit_id', '=', $idUnit));
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido.system_unit_id', '=', $idUnit));
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido.system_unit_id', '=', $idUnit));
        $criteria_total_por_cliente->add(new TFilter('pedido.system_unit_id', '=', $idUnit));

        $filterVar = EstadoPedido::PENDENTE;
        $criteria_pedidos_em_elaboracao->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $criteria_pedidos_em_elaboracao->add(new TFilter('pedido.system_unit_id', '=', TSession::getValue("idunit"))); 
        $filterVar = EstadoPedido::ENVIADO;
        $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $filterVar = EstadoPedido::COMPROPOSTA;
        $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $filterVar = EstadoPedido::APROVADO;
        $criteria_pedidos_em_processamento->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $filterVar = EstadoPedido::PGTOAPROVADO;
        $criteria_pedidos_em_faturamento->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $filterVar = EstadoPedido::REPROVADO;
        $criteria_pedidos_aguardando_entrega->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $filterVar = EstadoPedido::FINALIZADO;
        $criteria_pedidos_finalizados->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $filterVar = EstadoPedido::CANCELADO;
        $criteria_pedidos_cancelados->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $filterVar = TSession::getValue("idunit");
        $criteria_valor_empenho->add(new TFilter('departamento_unit.system_unit_id', '=', $filterVar)); 
        $filterVar = EstadoPedido::FINALIZADO;
        $criteria_valores_consumidos->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $filterVar = EstadoPedido::FINALIZADO;
        $criteria_saldo_atual->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $filterVar = EstadoPedido::FINALIZADO;
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $filterVar = EstadoPedido::FINALIZADO;
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 
        $filterVar = EstadoPedido::FINALIZADO;
        $criteria_total_por_cliente->add(new TFilter('pedido.estado_pedido_venda_id', '=', $filterVar)); 

/*

        $mes = new TCombo('mes');
        $ano = new TCombo('ano');
        $button_buscar = new TButton('button_buscar');
        $pedidos_em_elaboracao = new BIndicator('pedidos_em_elaboracao');
        $pedidos_em_analise_comercial = new BIndicator('pedidos_em_analise_comercial');
        $pedidos_em_anlise_de_credito = new BIndicator('pedidos_em_anlise_de_credito');
        $pedidos_em_processamento = new BIndicator('pedidos_em_processamento');
        $pedidos_em_faturamento = new BIndicator('pedidos_em_faturamento');
        $pedidos_aguardando_entrega = new BIndicator('pedidos_aguardando_entrega');
        $pedidos_finalizados = new BIndicator('pedidos_finalizados');
        $pedidos_cancelados = new BIndicator('pedidos_cancelados');
        $valor_empenho = new BIndicator('valor_empenho');
        $valores_consumidos = new BIndicator('valores_consumidos');
        $saldo_atual = new BIndicator('saldo_atual');
        $total_de_vendas_por_mes = new BBarChart('total_de_vendas_por_mes');
        $total_de_vendas_por_dia = new BLineChart('total_de_vendas_por_dia');
        $total_por_cliente = new BTableChart('total_por_cliente');


        $button_buscar->setAction(new TAction(['DashboardPedidoVenda', 'onShow']), "Buscar");
        $button_buscar->addStyleClass('btn-primary');
        $button_buscar->setImage('fas:search #FFFFFF');
        $mes->setSize('100%');
        $ano->setSize('100%');

        $ano->addItems(TempoService::getAnos());
        $mes->addItems(TempoService::getMeses());

        $mes->setValue($param['mes'] ?? date('m'));
        $ano->setValue($param['ano'] ?? date('Y'));

        $mes->enableSearch();
        $ano->enableSearch();

        $pedidos_em_elaboracao->setDatabase('minierp');
        $pedidos_em_elaboracao->setFieldValue("pedido.id");
        $pedidos_em_elaboracao->setModel('Pedido');
        $pedidos_em_elaboracao->setTotal('count');
        $pedidos_em_elaboracao->setColors('#BBE3E3', '#ffffff', '#81ECEC', '#ffffff');
        $pedidos_em_elaboracao->setTitle("pendente", '#ffffff', '20', '');
        $criteria_pedidos_em_elaboracao->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_em_elaboracao->setCriteria($criteria_pedidos_em_elaboracao);
        $pedidos_em_elaboracao->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $pedidos_em_elaboracao->setValueSize("20");
        $pedidos_em_elaboracao->setValueColor("#ffffff", 'B');
        $pedidos_em_elaboracao->setSize('100%', 95);
        $pedidos_em_elaboracao->setLayout('horizontal', 'left');

        $pedidos_em_analise_comercial->setDatabase('minierp');
        $pedidos_em_analise_comercial->setFieldValue("pedido.id");
        $pedidos_em_analise_comercial->setModel('Pedido');
        $pedidos_em_analise_comercial->setTotal('count');
        $pedidos_em_analise_comercial->setColors('#E1B1AC', '#FFFFFF', '#C0392B', '#FFFFFF');
        $pedidos_em_analise_comercial->setTitle("enviado", '#FFFFFF', '20', '');
        $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_em_analise_comercial->setCriteria($criteria_pedidos_em_analise_comercial);
        $pedidos_em_analise_comercial->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_em_analise_comercial->setValueSize("20");
        $pedidos_em_analise_comercial->setValueColor("#FFFFFF", 'B');
        $pedidos_em_analise_comercial->setSize('100%', 95);
        $pedidos_em_analise_comercial->setLayout('horizontal', 'left');

        $pedidos_em_anlise_de_credito->setDatabase('minierp');
        $pedidos_em_anlise_de_credito->setFieldValue("pedido.id");
        $pedidos_em_anlise_de_credito->setModel('Pedido');
        $pedidos_em_anlise_de_credito->setTotal('count');
        $pedidos_em_anlise_de_credito->setColors('#A8E1A0', '#FFFFFF', '#44BD32', '#FFFFFF');
        $pedidos_em_anlise_de_credito->setTitle("Com proposta", '#FFFFFF', '20', '');
        $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_em_anlise_de_credito->setCriteria($criteria_pedidos_em_anlise_de_credito);
        $pedidos_em_anlise_de_credito->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_em_anlise_de_credito->setValueSize("20");
        $pedidos_em_anlise_de_credito->setValueColor("#FFFFFF", 'B');
        $pedidos_em_anlise_de_credito->setSize('100%', 95);
        $pedidos_em_anlise_de_credito->setLayout('horizontal', 'left');

        $pedidos_em_processamento->setDatabase('minierp');
        $pedidos_em_processamento->setFieldValue("pedido.id");
        $pedidos_em_processamento->setModel('Pedido');
        $pedidos_em_processamento->setTotal('count');
        $pedidos_em_processamento->setColors('#54A0FF', '#FFFFFF', '#3498DB', '#FFFFFF');
        $pedidos_em_processamento->setTitle("aprovado", '#FFFFFF', '20', '');
        $criteria_pedidos_em_processamento->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_em_processamento->setCriteria($criteria_pedidos_em_processamento);
        $pedidos_em_processamento->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_em_processamento->setValueSize("20");
        $pedidos_em_processamento->setValueColor("#FFFFFF", 'B');
        $pedidos_em_processamento->setSize('100%', 95);
        $pedidos_em_processamento->setLayout('horizontal', 'left');

        $pedidos_em_faturamento->setDatabase('minierp');
        $pedidos_em_faturamento->setFieldValue("pedido.id");
        $pedidos_em_faturamento->setModel('Pedido');
        $pedidos_em_faturamento->setTotal('count');
        $pedidos_em_faturamento->setColors('#E6C17C', '#FFFFFF', '#FFA500', '#FFFFFF');
        $pedidos_em_faturamento->setTitle("Pagamento Aprovado", '#FFFFFF', '20', '');
        $criteria_pedidos_em_faturamento->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_em_faturamento->setCriteria($criteria_pedidos_em_faturamento);
        $pedidos_em_faturamento->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_em_faturamento->setValueSize("20");
        $pedidos_em_faturamento->setValueColor("#FFFFFF", 'B');
        $pedidos_em_faturamento->setSize('100%', 95);
        $pedidos_em_faturamento->setLayout('horizontal', 'left');

        $pedidos_aguardando_entrega->setDatabase('minierp');
        $pedidos_aguardando_entrega->setFieldValue("pedido.id");
        $pedidos_aguardando_entrega->setModel('Pedido');
        $pedidos_aguardando_entrega->setTotal('count');
        $pedidos_aguardando_entrega->setColors('#949191', '#FFFFFF', '#000000', '#FFFFFF');
        $pedidos_aguardando_entrega->setTitle("reprovado", '#FFFFFF', '20', '');
        $criteria_pedidos_aguardando_entrega->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_aguardando_entrega->setCriteria($criteria_pedidos_aguardando_entrega);
        $pedidos_aguardando_entrega->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_aguardando_entrega->setValueSize("20");
        $pedidos_aguardando_entrega->setValueColor("#FFFFFF", 'B');
        $pedidos_aguardando_entrega->setSize('100%', 95);
        $pedidos_aguardando_entrega->setLayout('horizontal', 'left');

        $pedidos_finalizados->setDatabase('minierp');
        $pedidos_finalizados->setFieldValue("pedido.id");
        $pedidos_finalizados->setModel('Pedido');
        $pedidos_finalizados->setTotal('count');
        $pedidos_finalizados->setColors('#90D2AC', '#FFFFFF', '#2ECC71', '#FFFFFF');
        $pedidos_finalizados->setTitle("finalizados", '#FFFFFF', '20', '');
        $criteria_pedidos_finalizados->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_finalizados->setCriteria($criteria_pedidos_finalizados);
        $pedidos_finalizados->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_finalizados->setValueSize("20");
        $pedidos_finalizados->setValueColor("#FFFFFF", 'B');
        $pedidos_finalizados->setSize('100%', 95);
        $pedidos_finalizados->setLayout('horizontal', 'left');

        $pedidos_cancelados->setDatabase('minierp');
        $pedidos_cancelados->setFieldValue("pedido.id");
        $pedidos_cancelados->setModel('Pedido');
        $pedidos_cancelados->setTotal('count');
        $pedidos_cancelados->setColors('#FF7675', '#FFFFFF', '#E74C3C', '#FFFFFF');
        $pedidos_cancelados->setTitle("cancelados", '#FFFFFF', '20', '');
        $criteria_pedidos_cancelados->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_cancelados->setCriteria($criteria_pedidos_cancelados);
        $pedidos_cancelados->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_cancelados->setValueSize("20");
        $pedidos_cancelados->setValueColor("#FFFFFF", 'B');
        $pedidos_cancelados->setSize('100%', 95);
        $pedidos_cancelados->setLayout('horizontal', 'left');

        $valor_empenho->setDatabase('minierp');
        $valor_empenho->setFieldValue("departamento_unit.valor_empenho");
        $valor_empenho->setModel('DepartamentoUnit');
        $valor_empenho->setTransformerValue(function($value)
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
        $valor_empenho->setTotal('sum');
        $valor_empenho->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1000.000,00";
        });
        $valor_empenho->setColors('#0F4626', '#ffffff', '#0C371E', '#ffffff');
        $valor_empenho->setTitle("Valor do empenho", '#ffffff', '20', '');
        $valor_empenho->setCriteria($criteria_valor_empenho);
        $valor_empenho->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $valor_empenho->setValueSize("20");
        $valor_empenho->setValueColor("#ffffff", 'B');
        $valor_empenho->setSize('100%', 95);
        $valor_empenho->setLayout('horizontal', 'left');

        $valores_consumidos->setDatabase('minierp');
        $valores_consumidos->setFieldValue("pedido.valor_liquido_cotacao");
        $valores_consumidos->setModel('Pedido');
        $valores_consumidos->setTransformerValue(function($value)
        {
            //code here

                $dep = DepartamentoUnit::where('system_unit_id','=',TSession::getValue('session_userunitid'))
                                       ->load();

                $value=0;
                if ($dep)
                {
                    foreach ($dep as $departamento)
                    {
                       $pedido = Pedido::where('departamento_unit_id','=',$dep->departamento_unit_id) 
                                       ::where('estado_pedido_venda_id','=',EstadoPedido::FINALIZADO)
                                       ->load();
                       foreach($pedido as $pedidos)                               
                       {
                           $value += $pedidos->$valor_liquido_cotacao;
                       }

                    }                
                }
                return $value;

        });
        $valores_consumidos->setTotal('sum');
        $valores_consumidos->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1000.000,00";
        });
        $valores_consumidos->setColors('#9852A3', '#ffffff', '#9D27AE', '#ffffff');
        $valores_consumidos->setTitle("Valores consumidos", '#ffffff', '20', '');
        $criteria_valores_consumidos->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $valores_consumidos->setCriteria($criteria_valores_consumidos);
        $valores_consumidos->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $valores_consumidos->setValueSize("20");
        $valores_consumidos->setValueColor("#ffffff", 'B');
        $valores_consumidos->setSize('100%', 95);
        $valores_consumidos->setLayout('horizontal', 'left');

        $saldo_atual->setDatabase('minierp');
        $saldo_atual->setFieldValue("pedido.valor_liquido_cotacao");
        $saldo_atual->setModel('Pedido');
        $saldo_atual->setTransformerValue(function($value)
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
        $saldo_atual->setTotal('sum');
        $saldo_atual->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1000.000,00";
        });
        $saldo_atual->setColors('#A1C07D', '#ffffff', '#8BC34A', '#ffffff');
        $saldo_atual->setTitle("SALDO ATUAL", '#ffffff', '20', '');
        $criteria_saldo_atual->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $saldo_atual->setCriteria($criteria_saldo_atual);
        $saldo_atual->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $saldo_atual->setValueSize("20");
        $saldo_atual->setValueColor("#ffffff", 'B');
        $saldo_atual->setSize('100%', 95);
        $saldo_atual->setLayout('horizontal', 'left');

        $total_de_vendas_por_mes->setDatabase('minierp');
        $total_de_vendas_por_mes->setFieldValue("pedido.valor_total");
        $total_de_vendas_por_mes->setFieldGroup(["pedido.mes"]);
        $total_de_vendas_por_mes->setModel('Pedido');
        $total_de_vendas_por_mes->setTitle("Total de Compras por Mês");
        $total_de_vendas_por_mes->setTransformerLegend(function($value, $row, $data)
            {

                $value = str_pad($value, 2, "0", STR_PAD_LEFT);
                $meses = TempoService::getMeses();

                return $meses[$value] ?? '';

            });
        $total_de_vendas_por_mes->setTransformerValue(function($value, $row, $data)
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
        $total_de_vendas_por_mes->setLayout('vertical');
        $total_de_vendas_por_mes->setTotal('sum');
        $total_de_vendas_por_mes->showLegend(false);
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $total_de_vendas_por_mes->setCriteria($criteria_total_de_vendas_por_mes);
        $total_de_vendas_por_mes->setLabelValue("Total no mês");
        $total_de_vendas_por_mes->setSize('100%', 280);
        $total_de_vendas_por_mes->disableZoom();

        $total_de_vendas_por_dia->setDatabase('minierp');
        $total_de_vendas_por_dia->setFieldValue("pedido.valor_total");
        $total_de_vendas_por_dia->setFieldGroup(["pedido.dt_pedido"]);
        $total_de_vendas_por_dia->setModel('Pedido');
        $total_de_vendas_por_dia->setTitle("Total de Compras por Dia");
        $total_de_vendas_por_dia->setTransformerLegend(function($value, $row, $data)
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
        $total_de_vendas_por_dia->setTransformerValue(function($value, $row, $data)
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
        $total_de_vendas_por_dia->setTotal('sum');
        $total_de_vendas_por_dia->showLegend(false);
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $total_de_vendas_por_dia->setCriteria($criteria_total_de_vendas_por_dia);
        $total_de_vendas_por_dia->setLabelValue("Total no dia");
        $total_de_vendas_por_dia->setRotateLegend('35',60);
        $total_de_vendas_por_dia->setSize('100%', 280);
        $total_de_vendas_por_dia->disableZoom();

        $total_por_cliente_column_cliente_id = new BTableColumnChart('cliente_id', "Fornecedor", 'left','33%');
        $total_por_cliente_column_id = new BTableColumnChart('id', "Pedidos", 'right');
        $total_por_cliente_column_valor_liquido_cotacao = new BTableColumnChart('valor_liquido_cotacao', "Valor Total", 'right');
        $total_por_cliente_column_id->setTotal('sum');
        $total_por_cliente_column_valor_liquido_cotacao->setTotal('sum', function($value, $object, $row)
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
        $total_por_cliente_column_id->setAggregate('count');
        $total_por_cliente_column_valor_liquido_cotacao->setAggregate('sum');
        $total_por_cliente_column_valor_liquido_cotacao->setTransformer(function($value, $object, $row)
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

        $criteria_total_por_cliente->add(new TFilter('pedido.deleted_at', 'is', NULL));

        $total_por_cliente->setDatabase('minierp');
        $total_por_cliente->setModel('Pedido');
        $total_por_cliente->setTitle("Totalizadores por Fornecedores");
        $total_por_cliente->setSize('100%', 250);
        $total_por_cliente->setColumns([$total_por_cliente_column_cliente_id,$total_por_cliente_column_id,$total_por_cliente_column_valor_liquido_cotacao]);
        $total_por_cliente->setCriteria($criteria_total_por_cliente);

        $total_por_cliente->setRowColorOdd('#F9F9F9');
        $total_por_cliente->setRowColorEven('#FFFFFF');
        $total_por_cliente->setFontRowColorOdd('#333333');
        $total_por_cliente->setFontRowColorEven('#333333');
        $total_por_cliente->setBorderColor('#DDDDDD');
        $total_por_cliente->setTableHeaderColor('#FFFFFF');
        $total_por_cliente->setTableHeaderFontColor('#333333');
        $total_por_cliente->setTableFooterColor('#FFFFFF');
        $total_por_cliente->setTableFooterFontColor('#333333');


*/
 $login = new LoginForm([]);
        $AlertMensagem = $login->onMensagem('DashboardPedidoVenda');
        $TAlert = new TAlert('danger',$AlertMensagem);
 $mes = new TCombo('mes');
        $ano = new TCombo('ano');
        $button_buscar = new TButton('button_buscar');
        $pedidos_em_elaboracao = new BIndicator('pedidos_em_elaboracao');
        $pedidos_em_analise_comercial = new BIndicator('pedidos_em_analise_comercial');
        $pedidos_em_anlise_de_credito = new BIndicator('pedidos_em_anlise_de_credito');
        $pedidos_em_processamento = new BIndicator('pedidos_em_processamento');
        $pedidos_em_faturamento = new BIndicator('pedidos_em_faturamento');
        $pedidos_aguardando_entrega = new BIndicator('pedidos_aguardando_entrega');
        $pedidos_finalizados = new BIndicator('pedidos_finalizados');
        $pedidos_cancelados = new BIndicator('pedidos_cancelados');
        $valor_empenho = new BIndicator('valor_empenho');
        $valores_consumidos = new BIndicator('valores_consumidos');
        $saldo_atual = new BIndicator('saldo_atual');
        $total_de_vendas_por_mes = new BBarChart('total_de_vendas_por_mes');
        $total_de_vendas_por_dia = new BLineChart('total_de_vendas_por_dia');
        $pedidos = new BTableChart('pedidos');
        $total_por_cliente = new BTableChart('total_por_cliente');
        $saldo_contratual_total = new BIndicator('saldo_contratual_total');
        $saldo_contratual_atual = new BIndicator('saldo_contratual_atual');

        $button_buscar->setAction(new TAction(['DashboardPedidoVenda', 'onShow']), "Buscar");
        $button_buscar->addStyleClass('btn-primary');
        $button_buscar->setImage('fas:search #FFFFFF');
        $mes->setSize('100%');
        $ano->setSize('100%');

        $ano->addItems(TempoService::getAnos());
        $mes->addItems(TempoService::getMeses());

        $mes->setValue($param['mes'] ?? date('m'));
        $ano->setValue($param['ano'] ?? date('Y'));

        $mes->enableSearch();
        $ano->enableSearch();

   $saldo_contratual_total->setDatabase('minierp');
        $saldo_contratual_total->setFieldValue("saldo_entidade_contrato.valor_saldo");
        $saldo_contratual_total->setModel('SaldoEntidadeContrato');
        $saldo_contratual_total->setTotal('sum');
        $saldo_contratual_total->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1.000.000,00";
        });
        $saldo_contratual_total->setColors('rgb(209, 178, 101)', '#ffffff', ' #cd9a0e', '#ffffff');
        $saldo_contratual_total->setTitle("SALDO CONTRATUAL TOTAL", '#ffffff', '20', '');
        // $criteria_saldo_contratual_total->add(new TFilter('saldo_entidade_contrato.deleted_at', 'is', NULL));
        // $criteria_saldo_contratual_total->add(new TFilter('saldo_entidade_contrato.tipotransacao', '=', 'C'));
        // $saldo_contratual_total->setCriteria($criteria_saldo_contratual_total);
        $saldo_contratual_total->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $saldo_contratual_total->setValueSize("20");
        $saldo_contratual_total->setValueColor("#ffffff", 'B');
        $saldo_contratual_total->setSize('100%', 95);
        $saldo_contratual_total->setLayout('horizontal', 'left');
        $saldo_contratual_total->setTransformerValue(function($value)
        {

            TTransaction::open('minierp');

            $credito = SaldoEntidadeContrato::where('entidade_id', '=', TSession::getValue('entidade'))
                                            ->where('tipotransacao', '=', 'C')
                                            ->load();

            $debito = SaldoEntidadeContrato::where('entidade_id', '=', TSession::getValue('entidade'))
                                           ->where('tipotransacao', '=', 'D')
                                           ->load();

            $credito_saldo = 0;
            if($credito)
            {
                foreach($credito as $cred)
                {
                    $credito_saldo += (float)$cred->valor_saldo;
                }
            }    

            $debito_saldo = 0;
            if($debito)
            {
                foreach($debito as $deb)
                {
                    $debito_saldo += (float)$deb->valor_saldo;
                }
            }

            TTransaction::close();

            $value = $credito_saldo - $debito_saldo;

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
        $saldo_contratual_total->setTotal('sum');
        $saldo_contratual_total->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1.000.000,00";
        });
        $saldo_contratual_total->setColors('rgb(168, 140, 67)', '#ffffff', 'rgb(160, 118, 3)', '#ffffff');
        $saldo_contratual_total->setTitle("SALDO CONTRATUAL TOTAL", '#ffffff', '20', '');
        // $criteria_saldo_contratual_total->add(new TFilter('saldo_entidade_contrato.deleted_at', 'is', NULL));
        // $saldo_contratual_total->setCriteria($criteria_saldo_contratual_total);
        $saldo_contratual_total->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $saldo_contratual_total->setValueSize("20");
        $saldo_contratual_total->setValueColor("#ffffff", 'B');
        $saldo_contratual_total->setSize('100%', 95);
        $saldo_contratual_total->setLayout('horizontal', 'left');

        // Saldo Contratual Atual

        $saldo_contratual_atual->setDatabase('minierp');
        $saldo_contratual_atual->setFieldValue("saldo_entidade_contrato.valor_saldo");
        $saldo_contratual_atual->setModel('SaldoEntidadeContrato');
        $saldo_contratual_atual->setTotal('sum');
        $saldo_contratual_atual->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1.000.000,00";
        });
        $saldo_contratual_atual->setColors('rgb(114, 128, 107)', '#ffffff', 'rgb(53, 65, 50)', '#ffffff');
        $saldo_contratual_atual->setTitle("SALDO CONTRATUAL ATUAL", '#ffffff', '20', '');
        // $criteria_saldo_contratual_atual->add(new TFilter('saldo_entidade_contrato.deleted_at', 'is', NULL));
        // $saldo_contratual_atual->setCriteria($criteria_saldo_contratual_atual);
        $saldo_contratual_atual->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $saldo_contratual_atual->setValueSize("20");
        $saldo_contratual_atual->setValueColor("#ffffff", 'B');
        $saldo_contratual_atual->setSize('100%', 95);
        $saldo_contratual_atual->setLayout('horizontal', 'left');
        $saldo_contratual_atual->setTransformerValue(function($value)
        {

            TTransaction::open('minierp');


            $credito = SaldoEntidadeContrato::where('entidade_id', '=', TSession::getValue('entidade'))
                                            ->where('tipotransacao', '=', 'C')
                                            ->load();

            $debito = SaldoEntidadeContrato::where('entidade_id', '=', TSession::getValue('entidade'))
                                           ->where('tipotransacao', '=', 'D')
                                           ->load();


            $credito_saldo = 0;
            if($credito)
            {
                foreach($credito as $cred)
                {
                    $credito_saldo += (float)$cred->valor_saldo;
                }
            }    

            $debito_saldo = 0;
            if($debito)
            {
                foreach($debito as $deb)
                {
                    $debito_saldo += (float)$deb->valor_saldo;
                }
            }

            $ped = Pedido::where('estado_pedido_venda_id', '=', EstadoPedido::FINALIZADO)
                               ->where('system_unit_id', '=', TSession::getValue('idunit'))
                               ->load();

            $pedidototal = 0;
            if($ped)
            {
                foreach($ped as $pedido)
                {
                    $pedidototal += $pedido->valor_liquido_cotacao;
                }
            }


            TTransaction::close();

            $saldoctotal = $credito_saldo - $debito_saldo;

            $value = $saldoctotal - $pedidototal;

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
         $pedidos_em_elaboracao->setDatabase('minierp');
        $pedidos_em_elaboracao->setFieldValue("pedido.id");
        $pedidos_em_elaboracao->setModel('Pedido');
        $pedidos_em_elaboracao->setTotal('count');
        $pedidos_em_elaboracao->setColors('#BBE3E3', '#ffffff', '#81ECEC', '#ffffff');
        $pedidos_em_elaboracao->setTitle("pendente", '#ffffff', '20', '');
        $criteria_pedidos_em_elaboracao->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_em_elaboracao->setCriteria($criteria_pedidos_em_elaboracao);
        $pedidos_em_elaboracao->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $pedidos_em_elaboracao->setValueSize("20");
        $pedidos_em_elaboracao->setValueColor("#ffffff", 'B');
        $pedidos_em_elaboracao->setSize('100%', 95);
        $pedidos_em_elaboracao->setLayout('horizontal', 'left');

        $pedidos_em_analise_comercial->setDatabase('minierp');
        $pedidos_em_analise_comercial->setFieldValue("pedido.id");
        $pedidos_em_analise_comercial->setModel('Pedido');
        $pedidos_em_analise_comercial->setTotal('count');
        $pedidos_em_analise_comercial->setColors('#E1B1AC', '#FFFFFF', '#C0392B', '#FFFFFF');
        $pedidos_em_analise_comercial->setTitle("enviado", '#FFFFFF', '20', '');
        $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_em_analise_comercial->setCriteria($criteria_pedidos_em_analise_comercial);
        $pedidos_em_analise_comercial->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_em_analise_comercial->setValueSize("20");
        $pedidos_em_analise_comercial->setValueColor("#FFFFFF", 'B');
        $pedidos_em_analise_comercial->setSize('100%', 95);
        $pedidos_em_analise_comercial->setLayout('horizontal', 'left');

        $pedidos_em_anlise_de_credito->setDatabase('minierp');
        $pedidos_em_anlise_de_credito->setFieldValue("pedido.id");
        $pedidos_em_anlise_de_credito->setModel('Pedido');
        $pedidos_em_anlise_de_credito->setTotal('count');
        $pedidos_em_anlise_de_credito->setColors('#A8E1A0', '#FFFFFF', '#44BD32', '#FFFFFF');
        $pedidos_em_anlise_de_credito->setTitle("Com proposta", '#FFFFFF', '20', '');
        $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_em_anlise_de_credito->setCriteria($criteria_pedidos_em_anlise_de_credito);
        $pedidos_em_anlise_de_credito->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_em_anlise_de_credito->setValueSize("20");
        $pedidos_em_anlise_de_credito->setValueColor("#FFFFFF", 'B');
        $pedidos_em_anlise_de_credito->setSize('100%', 95);
        $pedidos_em_anlise_de_credito->setLayout('horizontal', 'left');

        $pedidos_em_processamento->setDatabase('minierp');
        $pedidos_em_processamento->setFieldValue("pedido.id");
        $pedidos_em_processamento->setModel('Pedido');
        $pedidos_em_processamento->setTotal('count');
        $pedidos_em_processamento->setColors('#54A0FF', '#FFFFFF', '#3498DB', '#FFFFFF');
        $pedidos_em_processamento->setTitle("aprovado", '#FFFFFF', '20', '');
        $criteria_pedidos_em_processamento->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_em_processamento->setCriteria($criteria_pedidos_em_processamento);
        $pedidos_em_processamento->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_em_processamento->setValueSize("20");
        $pedidos_em_processamento->setValueColor("#FFFFFF", 'B');
        $pedidos_em_processamento->setSize('100%', 95);
        $pedidos_em_processamento->setLayout('horizontal', 'left');

        $pedidos_em_faturamento->setDatabase('minierp');
        $pedidos_em_faturamento->setFieldValue("pedido.id");
        $pedidos_em_faturamento->setModel('Pedido');
        $pedidos_em_faturamento->setTotal('count');
        $pedidos_em_faturamento->setColors('#E6C17C', '#FFFFFF', '#FFA500', '#FFFFFF');
        $pedidos_em_faturamento->setTitle("Pagamento Aprovado", '#FFFFFF', '20', '');
        $criteria_pedidos_em_faturamento->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_em_faturamento->setCriteria($criteria_pedidos_em_faturamento);
        $pedidos_em_faturamento->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_em_faturamento->setValueSize("20");
        $pedidos_em_faturamento->setValueColor("#FFFFFF", 'B');
        $pedidos_em_faturamento->setSize('100%', 95);
        $pedidos_em_faturamento->setLayout('horizontal', 'left');

        $pedidos_aguardando_entrega->setDatabase('minierp');
        $pedidos_aguardando_entrega->setFieldValue("pedido.id");
        $pedidos_aguardando_entrega->setModel('Pedido');
        $pedidos_aguardando_entrega->setTotal('count');
        $pedidos_aguardando_entrega->setColors('#949191', '#FFFFFF', '#000000', '#FFFFFF');
        $pedidos_aguardando_entrega->setTitle("reprovado", '#FFFFFF', '20', '');
        $criteria_pedidos_aguardando_entrega->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_aguardando_entrega->setCriteria($criteria_pedidos_aguardando_entrega);
        $pedidos_aguardando_entrega->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_aguardando_entrega->setValueSize("20");
        $pedidos_aguardando_entrega->setValueColor("#FFFFFF", 'B');
        $pedidos_aguardando_entrega->setSize('100%', 95);
        $pedidos_aguardando_entrega->setLayout('horizontal', 'left');

        $pedidos_finalizados->setDatabase('minierp');
        $pedidos_finalizados->setFieldValue("pedido.id");
        $pedidos_finalizados->setModel('Pedido');
        $pedidos_finalizados->setTotal('count');
        $pedidos_finalizados->setColors('#90D2AC', '#FFFFFF', '#2ECC71', '#FFFFFF');
        $pedidos_finalizados->setTitle("finalizados", '#FFFFFF', '20', '');
        $criteria_pedidos_finalizados->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_finalizados->setCriteria($criteria_pedidos_finalizados);
        $pedidos_finalizados->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_finalizados->setValueSize("20");
        $pedidos_finalizados->setValueColor("#FFFFFF", 'B');
        $pedidos_finalizados->setSize('100%', 95);
        $pedidos_finalizados->setLayout('horizontal', 'left');

        $pedidos_cancelados->setDatabase('minierp');
        $pedidos_cancelados->setFieldValue("pedido.id");
        $pedidos_cancelados->setModel('Pedido');
        $pedidos_cancelados->setTotal('count');
        $pedidos_cancelados->setColors('#FF7675', '#FFFFFF', '#E74C3C', '#FFFFFF');
        $pedidos_cancelados->setTitle("cancelados", '#FFFFFF', '20', '');
        $criteria_pedidos_cancelados->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $pedidos_cancelados->setCriteria($criteria_pedidos_cancelados);
        $pedidos_cancelados->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $pedidos_cancelados->setValueSize("20");
        $pedidos_cancelados->setValueColor("#FFFFFF", 'B');
        $pedidos_cancelados->setSize('100%', 95);
        $pedidos_cancelados->setLayout('horizontal', 'left');

        $valor_empenho->setDatabase('minierp');
        $valor_empenho->setFieldValue("departamento_unit.valor_empenho");
        $valor_empenho->setModel('DepartamentoUnit');
         $valor_empenho->setTransformerValue(function($value)
        {
            //code here
               TTransaction::open('minierp');

                $dep = DepartamentoUnit::where('system_unit_id','=',  TSession::getValue('idunit'))
                                       ->load();

              //  var_dump(TSession::getValue('idunit'));
                $value=0;
                if ($dep)
                { 
                    foreach ($dep as $departamento)

                    {
                        $value += $departamento->valor_empenho;

                    }                
                }
                TTransaction::close();

                return "R$ " . number_format($value, 2, ",", ".");

        });

        $valor_empenho->setTotal('sum');
        $valor_empenho->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1000.000,00";
        });
        $valor_empenho->setColors('#0F4626', '#ffffff', '#0C371E', '#ffffff');
        $valor_empenho->setTitle("Valores do empenho", '#ffffff', '20', '');
        $valor_empenho->setCriteria($criteria_valor_empenho);
        $valor_empenho->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $valor_empenho->setValueSize("20");
        $valor_empenho->setValueColor("#ffffff", 'B');
        $valor_empenho->setSize('100%', 95);
        $valor_empenho->setLayout('horizontal', 'left');

        $valores_consumidos->setDatabase('minierp');
        $valores_consumidos->setFieldValue("pedido.valor_liquido_cotacao");
        $valores_consumidos->setModel('Pedido');

          $valores_consumidos->setTransformerValue(function($value)
        {
            //code here
               TTransaction::open('minierp');

                $dep = DepartamentoUnit::where('system_unit_id','=',  TSession::getValue('idunit'))
                                       ->load();

           //     var_dump(TSession::getValue('idunit'));
                $value=0;
                if ($dep)
                {
                    foreach ($dep as $departamento)

                    {

                       $pedido = Pedido::where('departamento_unit_id','=',$departamento->id) 
                                       ->where('estado_pedido_venda_id','=',EstadoPedido::FINALIZADO)
                                       ->load();

                       foreach($pedido as $pedidos)                               
                       {
                           $value += $pedidos->valor_liquido_cotacao;
                       }

                    }                
                }
                TTransaction::close();
                return "R$ " . number_format($value, 2, ",", ".");

        });
        $valores_consumidos->setTotal('sum');
        $valores_consumidos->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1000.000,00";
        });
        $valores_consumidos->setColors('#9852A3', '#ffffff', '#9D27AE', '#ffffff');
        $valores_consumidos->setTitle("Valores consumidos", '#ffffff', '20', '');
        $criteria_valores_consumidos->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $valores_consumidos->setCriteria($criteria_valores_consumidos);
        $valores_consumidos->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $valores_consumidos->setValueSize("20");
        $valores_consumidos->setValueColor("#ffffff", 'B');
        $valores_consumidos->setSize('100%', 95);
        $valores_consumidos->setLayout('horizontal', 'left');

        $saldo_atual->setDatabase('minierp');
        $saldo_atual->setFieldValue("pedido.valor_liquido_cotacao");
        $saldo_atual->setModel('Pedido');
        $saldo_atual->setTotal('sum');
        $saldo_atual->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1000.000,00";
        });
        $saldo_atual->setColors('#A1C07D', '#ffffff', '#8BC34A', '#ffffff');
        $saldo_atual->setTitle("SALDO ATUAL", '#ffffff', '20', '');
        $criteria_saldo_atual->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $saldo_atual->setCriteria($criteria_saldo_atual);
        $saldo_atual->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $saldo_atual->setValueSize("20");
        $saldo_atual->setValueColor("#ffffff", 'B');
        $saldo_atual->setSize('100%', 95);
        $saldo_atual->setLayout('horizontal', 'left');
        $saldo_atual->setTransformerValue(function($value)
        {

            TTransaction::open('minierp');

            $dep = DepartamentoUnit::where('system_unit_id','=',  TSession::getValue('idunit'))
                                   ->load();

       //     var_dump(TSession::getValue('idunit'));
            $value=0;
            if ($dep)
            {
                foreach ($dep as $departamento)

                {

                   $pedido = Pedido::where('departamento_unit_id','=',$departamento->id) 
                                   ->where('estado_pedido_venda_id','=',EstadoPedido::FINALIZADO)
                                   ->load();

                   foreach($pedido as $pedidos)                               
                   {
                       $value += $pedidos->valor_liquido_cotacao;
                   }

                }                
            }

           $valueempenho=0;    

                $objects = DepartamentoUnit::where('system_unit_id','=',TSession::getValue('idunit'))
                                           ->load();

                if ($objects) {
                    foreach ($objects as $obj) {
                       // code...
                       $valueempenho = $valueempenho + ($obj->valor_empenho) ;
                    }
                }

           TTransaction::close();

            $value = ($valueempenho-$value);

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
        $saldo_atual->setTotal('sum');
        $saldo_atual->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1000.000,00";
        });
        $saldo_atual->setColors('#A1C07D', '#ffffff', '#8BC34A', '#ffffff');
        $saldo_atual->setTitle("SALDO ATUAL", '#ffffff', '20', '');
        $criteria_saldo_atual->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $saldo_atual->setCriteria($criteria_saldo_atual);
        $saldo_atual->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $saldo_atual->setValueSize("20");
        $saldo_atual->setValueColor("#ffffff", 'B');
        $saldo_atual->setSize('100%', 95);
        $saldo_atual->setLayout('horizontal', 'left');

        $total_de_vendas_por_mes->setDatabase('minierp');
        $total_de_vendas_por_mes->setFieldValue("pedido.valor_liquido_cotacao");
        $total_de_vendas_por_mes->setFieldGroup(["pedido.mes"]);
        $total_de_vendas_por_mes->setModel('Pedido');
        $total_de_vendas_por_mes->setTitle("Total de Vendas por Mês");
        $total_de_vendas_por_mes->setTransformerLegend(function($value, $row, $data)
            {

                $value = str_pad($value, 2, "0", STR_PAD_LEFT);
                $meses = TempoService::getMeses();

                return $meses[$value] ?? '';

            });
        $total_de_vendas_por_mes->setTransformerValue(function($value, $row, $data)
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
        $total_de_vendas_por_mes->setLayout('vertical');
        $total_de_vendas_por_mes->setTotal('sum');
        $total_de_vendas_por_mes->showLegend(false);
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $total_de_vendas_por_mes->setCriteria($criteria_total_de_vendas_por_mes);
        $total_de_vendas_por_mes->setLabelValue("Total no mês");
        $total_de_vendas_por_mes->setSize('100%', 280);
        $total_de_vendas_por_mes->disableZoom();

        $total_de_vendas_por_dia->setDatabase('minierp');
        $total_de_vendas_por_dia->setFieldValue("pedido.valor_liquido_cotacao");
        $total_de_vendas_por_dia->setFieldGroup(["pedido.dt_pedido"]);
        $total_de_vendas_por_dia->setModel('Pedido');
        $total_de_vendas_por_dia->setTitle("Total de Vendas por Dia");
        $total_de_vendas_por_dia->setTransformerLegend(function($value, $row, $data)
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
        $total_de_vendas_por_dia->setTransformerValue(function($value, $row, $data)
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
        $total_de_vendas_por_dia->setTotal('sum');
        $total_de_vendas_por_dia->showLegend(false);
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido.deleted_at', 'is', NULL));
        $total_de_vendas_por_dia->setCriteria($criteria_total_de_vendas_por_dia);
        $total_de_vendas_por_dia->setLabelValue("Total no dia");
        $total_de_vendas_por_dia->setRotateLegend('35',60);
        $total_de_vendas_por_dia->setSize('100%', 280);
        $total_de_vendas_por_dia->disableZoom();
/*
      $pedidos_column_id = new BTableColumnChart('id', "Pedidos", 'center','33%');
        $pedidos_column_valor_liquido_cotacao = new BTableColumnChart('valor_liquido_cotacao', "Total Vendido", 'right','33%');
        $pedidos_column_estado_pedido_venda_id = new BTableColumnChart('estado_pedido_venda_id', "Estado do Pedido", 'center','33%');
        $pedidos_column_id->setTotal('sum');
        $pedidos_column_valor_liquido_cotacao->setTotal('sum', function($value, $object, $row)
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
        $pedidos_column_id->setAggregate('count');
        $pedidos_column_valor_liquido_cotacao->setAggregate('sum');
        $pedidos_column_valor_liquido_cotacao->setTransformer(function($value, $object, $row)
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
        $pedidos_column_estado_pedido_venda_id->setTransformer(function($value, $object, $row)
        {

            TTransaction::open('minierp');
            $estadoPedidoVenda = new EstadoPedidoVenda($value);
            TTransaction::close();

            return "<span class='label label-default' style='background-color:{$estadoPedidoVenda->cor}'> {$estadoPedidoVenda->nome} <span>";

        });

        $criteria_pedidos->add(new TFilter('pedido.deleted_at', 'is', NULL));

        $pedidos->setDatabase('minierp');
        $pedidos->setModel('Pedido');
        $pedidos->setTitle("Pedidos");
        $pedidos->setSize('100%', 250);
        $pedidos->setColumns([$pedidos_column_id,$pedidos_column_valor_liquido_cotacao,$pedidos_column_estado_pedido_venda_id]);
        $pedidos->setCriteria($criteria_pedidos);

        $pedidos->setRowColorOdd('#F9F9F9');
        $pedidos->setRowColorEven('#FFFFFF');
        $pedidos->setFontRowColorOdd('#333333');
        $pedidos->setFontRowColorEven('#333333');
        $pedidos->setBorderColor('#DDDDDD');
        $pedidos->setTableHeaderColor('#FFFFFF');
        $pedidos->setTableHeaderFontColor('#333333');
        $pedidos->setTableFooterColor('#FFFFFF');
        $pedidos->setTableFooterFontColor('#333333');
*/
        $total_por_cliente_column_pessoa_nome = new BTableColumnChart('pessoa.nome', "Fornecedor", 'left','33%');
        $total_por_cliente_column_id = new BTableColumnChart('id', "Pedidos", 'right');
        $total_por_cliente_column_valor_total = new BTableColumnChart('valor_liquido_cotacao', "Valor Liquido", 'right');
        $total_por_cliente_column_id->setTotal('sum');
        $total_por_cliente_column_valor_total->setTotal('sum', function($value, $object, $row)
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
        $total_por_cliente_column_id->setAggregate('count');
        $total_por_cliente_column_valor_total->setAggregate('sum');
        $total_por_cliente_column_valor_total->setTransformer(function($value, $object, $row)
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

        $criteria_total_por_cliente->add(new TFilter('pessoa.deleted_at', 'is', NULL));
        $total_por_cliente->setDatabase('minierp');
        $total_por_cliente->setModel('Pedido');
        $total_por_cliente->setTitle("Totalizadores por Fornecedores");
        $total_por_cliente->setSize('100%', 250);
        $total_por_cliente->setColumns([$total_por_cliente_column_pessoa_nome,$total_por_cliente_column_id,$total_por_cliente_column_valor_total]);
        $total_por_cliente->setCriteria($criteria_total_por_cliente);
        $total_por_cliente->setJoins([
             'pessoa' => ['pedido.system_unit_id', 'pessoa.id']
        ]);

        $total_por_cliente->setRowColorOdd('#F9F9F9');
        $total_por_cliente->setRowColorEven('#FFFFFF');
        $total_por_cliente->setFontRowColorOdd('#333333');
        $total_por_cliente->setFontRowColorEven('#333333');
        $total_por_cliente->setBorderColor('#DDDDDD');
        $total_por_cliente->setTableHeaderColor('#FFFFFF');
        $total_por_cliente->setTableHeaderFontColor('#333333');
        $total_por_cliente->setTableFooterColor('#FFFFFF');
        $total_por_cliente->setTableFooterFontColor('#333333');

        $row1 = $this->form->addFields([new TLabel("Mês:", null, '14px', null, '100%'),$mes],[new TLabel("Ano:", null, '14px', null),$ano],[new TLabel(" ", null, '14px', null, '100%'),$button_buscar]);
        $row1->layout = [' col-sm-2',' col-sm-2','col-sm-2'];

         $row12 = $this->form->addFields([$saldo_contratual_total], [$saldo_contratual_atual]);
        $row12->layout = [' col-sm-6', 'col-sm-6'];


        $row2 = $this->form->addFields([$pedidos_em_elaboracao],[$pedidos_em_analise_comercial],[$pedidos_em_anlise_de_credito],[$pedidos_em_processamento]);
        $row2->layout = ['col-sm-3','col-sm-3',' col-sm-3',' col-sm-3'];

        $row3 = $this->form->addFields([$pedidos_em_faturamento],[$pedidos_aguardando_entrega],[$pedidos_finalizados],[$pedidos_cancelados]);
        $row3->layout = ['col-sm-3','col-sm-3',' col-sm-3',' col-sm-3'];

        $row4 = $this->form->addFields([$valor_empenho],[$valores_consumidos],[$saldo_atual]);
        $row4->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row5 = $this->form->addFields([$total_de_vendas_por_mes]);
        $row5->layout = [' col-sm-12'];

        $row6 = $this->form->addFields([$total_de_vendas_por_dia]);
        $row6->layout = [' col-sm-12'];

        $row7 = $this->form->addFields([$total_por_cliente]);
        $row7->layout = [' col-sm-12'];

               ///             if(!isset($param['mes']) && $mes->getValue())
                  //          {
                    //            $_POST['mes'] = $mes->getValue();
                      //      }
                        //    if(!isset($param['ano']) && $ano->getValue())
                          //  {
                        //        $_POST['ano'] = $ano->getValue();
                       //     }

        $searchData = $this->form->getData();
        $this->form->setData($searchData);

        // aplicar filtro de mês/ano nos cards do dashboard
if (!empty($searchData->mes)) {
    $criteria_pedidos_em_elaboracao->add(new TFilter('pedido.mes', '=', $searchData->mes));
    $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido.mes', '=', $searchData->mes));
    $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido.mes', '=', $searchData->mes));
    $criteria_pedidos_em_processamento->add(new TFilter('pedido.mes', '=', $searchData->mes));
    $criteria_pedidos_em_faturamento->add(new TFilter('pedido.mes', '=', $searchData->mes));
    $criteria_pedidos_aguardando_entrega->add(new TFilter('pedido.mes', '=', $searchData->mes));
    $criteria_pedidos_finalizados->add(new TFilter('pedido.mes', '=', $searchData->mes));
    $criteria_pedidos_cancelados->add(new TFilter('pedido.mes', '=', $searchData->mes));
}

if (!empty($searchData->ano)) {
    $criteria_pedidos_em_elaboracao->add(new TFilter('pedido.ano', '=', $searchData->ano));
    $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido.ano', '=', $searchData->ano));
    $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido.ano', '=', $searchData->ano));
    $criteria_pedidos_em_processamento->add(new TFilter('pedido.ano', '=', $searchData->ano));
    $criteria_pedidos_em_faturamento->add(new TFilter('pedido.ano', '=', $searchData->ano));
    $criteria_pedidos_aguardando_entrega->add(new TFilter('pedido.ano', '=', $searchData->ano));
    $criteria_pedidos_finalizados->add(new TFilter('pedido.ano', '=', $searchData->ano));
    $criteria_pedidos_cancelados->add(new TFilter('pedido.ano', '=', $searchData->ano));
}


        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_total_de_vendas_por_mes->add(new TFilter('pedido.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_total_de_vendas_por_dia->add(new TFilter('pedido.mes', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_total_de_vendas_por_dia->add(new TFilter('pedido.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_total_por_cliente->add(new TFilter('pedido.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_total_por_cliente->add(new TFilter('pedido.mes', '=', $filterVar)); 
        }

        BChart::generate($saldo_contratual_total, $saldo_contratual_atual,$pedidos_em_elaboracao, $pedidos_em_analise_comercial, $pedidos_em_anlise_de_credito, $pedidos_em_processamento, $pedidos_em_faturamento, $pedidos_aguardando_entrega, $pedidos_finalizados, $pedidos_cancelados, $valor_empenho, $valores_consumidos, $saldo_atual, $total_de_vendas_por_mes, $total_de_vendas_por_dia, $total_por_cliente);

        // create the form actions

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
//            $container->add(TBreadCrumb::create(["Pedido","Dashboard"]));
if (!empty($AlertMensagem)) {
                $container->add($TAlert);
           } 
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onShow($param = null)
    {               

    } 

}

