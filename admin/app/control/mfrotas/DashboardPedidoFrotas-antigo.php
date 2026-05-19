<?php

use Adianti\Database\TTransaction;

class DashboardPedidoFrotas extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_DashboardPedidoFrotas';

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
          $basename   = urlencode('dashboard-pedido-frotas.pdf');
$download   = "download.php?file=app/manual/dashboard-pedido-frotas.pdf&basename={$basename}";

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
        $this->form->setFormTitle("<span style='font-weight: bold;'>Dashboard Pedido Frotas {$manual}</span>");

        
        $criteria_pedidos_em_elaboracao = new TCriteria();
        $criteria_pedidos_em_analise_comercial = new TCriteria();
        $criteria_pedidos_em_anlise_de_credito = new TCriteria();
        $criteria_pedidos_em_processamento = new TCriteria();
        $criteria_pedidos_em_faturamento = new TCriteria();
        $criteria_pedidos_em_faturamento_aguardando = new TCriteria();
        $criteria_pedidos_em_faturamento_entregue = new TCriteria();


        $criteria_pedidos_aguardando_entrega = new TCriteria();
        $criteria_pedidos_finalizados = new TCriteria();
        $criteria_pedidos_cancelados = new TCriteria();
        $criteria_valor_empenho = new TCriteria();
        $criteria_valores_consumidos = new TCriteria();
        $criteria_saldo_atual = new TCriteria();
        $criteria_total_de_vendas_por_mes = new TCriteria();
        $criteria_total_de_vendas_por_dia = new TCriteria();
        $criteria_total_por_cliente = new TCriteria();
        $criteria_dbmanutencao = new TCriteria();
        $criteria_dstatuscnh = new TCriteria();

        $usuarioDepartamentoId = (int) (TSession::getValue('iduser') ?: TSession::getValue('userid'));
        TTransaction::open('minierp');
        $departamentosPermitidos = SystemUserDepartamentoUnit::where('system_users_id', '=', $usuarioDepartamentoId)
            ->load();
        $departamentosPermitidosIds = [];

        if ($departamentosPermitidos)
        {
            foreach ($departamentosPermitidos as $departamentoPermitido)
            {
                if (!empty($departamentoPermitido->departamento_unit_id))
                {
                    $departamentosPermitidosIds[] = (int) $departamentoPermitido->departamento_unit_id;
                }
            }
        }
        TTransaction::close();
        if (empty($departamentosPermitidosIds))
        {
            $departamentosPermitidosIds = [-1];
        }


        $filterVar = EstadoPedidoFrotas::PENDENTE;
        $criteria_pedidos_em_elaboracao->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar)); 
        $criteria_pedidos_em_elaboracao->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_pedidos_em_elaboracao->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
    
        $filterVar = EstadoPedidoFrotas::ENVIADO;
        $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar)); 
        $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
     
        $filterVar = EstadoPedidoFrotas::COMPROPOSTA;
        $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar)); 
        $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
      
        $filterVar = EstadoPedidoFrotas::APROVADO;
        $criteria_pedidos_em_processamento->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar)); 
        $criteria_pedidos_em_processamento->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_pedidos_em_processamento->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
      
        $filterVar = EstadoPedidoFrotas::PGTOAPROVADO;
        $criteria_pedidos_em_faturamento->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar)); 
        $criteria_pedidos_em_faturamento->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_pedidos_em_faturamento->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
       
        $filterVar = EstadoPedidoFrotas::AGUARDANDO;
        $criteria_pedidos_em_faturamento_aguardando->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar));
        $criteria_pedidos_em_faturamento_aguardando->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_pedidos_em_faturamento_aguardando->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
       
       
        $filterVar = EstadoPedidoFrotas::ENTREGUE;
        $criteria_pedidos_em_faturamento_entregue->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar));
        $criteria_pedidos_em_faturamento_entregue->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_pedidos_em_faturamento_entregue->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));

        $filterVar = EstadoPedidoFrotas::REPROVADO;
        $criteria_pedidos_aguardando_entrega->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar)); 
        $criteria_pedidos_aguardando_entrega->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_pedidos_aguardando_entrega->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
        
        $filterVar = EstadoPedidoFrotas::FINALIZADO;
        $criteria_pedidos_finalizados->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar)); 
        $criteria_pedidos_finalizados->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_pedidos_finalizados->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
        
        $filterVar = EstadoPedidoFrotas::CANCELADO;
        $criteria_pedidos_cancelados->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar)); 
        $criteria_pedidos_cancelados->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_pedidos_cancelados->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
       
        $criteria_valor_empenho->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_valor_empenho->add(new TFilter('id', 'in', $departamentosPermitidosIds));
     
        $filterVar = EstadoPedidoFrotas::FINALIZADO;
        $criteria_valores_consumidos->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar)); 
        $criteria_valores_consumidos->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_valores_consumidos->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
        
        $filterVar = EstadoPedidoFrotas::FINALIZADO;
        $criteria_saldo_atual->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', '=', $filterVar)); 
        $criteria_saldo_atual->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_saldo_atual->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));

        $filterVar = [EstadoPedidoFrotas::REPROVADO, EstadoPedidoFrotas::CANCELADO];
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', 'not in', $filterVar)); 
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));

        $filterVar = [EstadoPedidoFrotas::REPROVADO, EstadoPedidoFrotas::CANCELADO];
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', 'not in', $filterVar)); 
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));

        $filterVar = [EstadoPedidoFrotas::REPROVADO, EstadoPedidoFrotas::CANCELADO];
        $criteria_total_por_cliente->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', 'not in', $filterVar)); 
        $criteria_total_por_cliente->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_total_por_cliente->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
  
        $criteria_cidade_id = new TCriteria();
        $login = new LoginForm([]);
        $AlertMensagem = $login->onMensagem('DashboardPedidoFrotas');

        $TAlert = new TAlert('danger',$AlertMensagem);
        $mes = new TCombo('mes');
        $ano = new TCombo('ano');
        $button_buscar = new TButton('button_buscar');
        $pedidos_em_elaboracao = new BIndicator('pedidos_em_elaboracao');
        $pedidos_em_analise_comercial = new BIndicator('pedidos_em_analise_comercial');
        $pedidos_em_anlise_de_credito = new BIndicator('pedidos_em_anlise_de_credito');
        $pedidos_em_processamento = new BIndicator('pedidos_em_processamento');
        $pedidos_em_faturamento = new BIndicator('pedidos_em_faturamento');
        $pedidos_em_faturamento_aguardando = new BIndicator('pedidos_em_faturamento_aguardando');
        $pedidos_em_faturamento_entregue = new BIndicator('pedidos_em_faturamento_entregue');
        $pedidos_aguardando_entrega = new BIndicator('pedidos_aguardando_entrega');
        $pedidos_finalizados = new BIndicator('pedidos_finalizados');
        $pedidos_cancelados = new BIndicator('pedidos_cancelados');
        $valor_empenho = new BIndicator('valor_empenho');
        $valores_consumidos = new BIndicator('valores_consumidos');
        $saldo_atual = new BIndicator('saldo_atual');
        $saldo_contratual_total = new BIndicator('saldo_contratual_total');
        $saldo_contratual_atual = new BIndicator('saldo_contratual_atual');
        $total_de_vendas_por_mes = new BBarChart('total_de_vendas_por_mes');
        $total_de_vendas_por_dia = new BLineChart('total_de_vendas_por_dia');
        $pedidos = new BTableChart('pedidos');
        $total_por_cliente = new BTableChart('total_por_cliente');
        $dbmanutencao = new BTableChart('dbmanutencao');
        $dstatuscnh = new BTableChart('dstatuscnh');


        $button_buscar->setAction(new TAction(['DashboardPedidoFrotas', 'onShow']), "Buscar");
        // $button_buscar->setAction(new TAction([$this, 'onShow'], $this->form->getData()->toArray()), "Buscar");
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
        $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $saldo_contratual_total->setIcon(new TImage($icone.' #ffffff'));
        $saldo_contratual_total->setValueSize("20");
        $saldo_contratual_total->setValueColor("#ffffff", 'B');
        $saldo_contratual_total->setSize('100%', 95);
        $saldo_contratual_total->setLayout('horizontal', 'left');
        $saldo_contratual_total->setTransformerValue(function($value)
        {

            TTransaction::open('minierp');

            $credito = SaldoEntidadeContrato::where('entidade_id', '=', TSession::getValue('entidade'))
                                            ->load();

            $credito_saldo = 0;
            if($credito)
            {
                foreach($credito as $cred)
                {
                    $credito_saldo += (float)$cred->valor_saldo;
                }
            }    

            TTransaction::close();

            $value = $credito_saldo;

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
          $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $saldo_contratual_total->setIcon(new TImage($icone. ' #ffffff'));
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
        
        $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $saldo_contratual_total->setIcon(new TImage($icone. ' #ffffff'));
        $saldo_contratual_atual->setValueSize("20");
        $saldo_contratual_atual->setValueColor("#ffffff", 'B');
        $saldo_contratual_atual->setSize('100%', 95);
        $saldo_contratual_atual->setLayout('horizontal', 'left');
        $saldo_contratual_atual->setTransformerValue(function($value)
        {

            TTransaction::open('minierp');


            $credito = SaldoEntidadeContrato::where('entidade_id', '=', TSession::getValue('entidade'))
                                           ->load();


            $credito_saldo = 0;
            if($credito)
            {
                foreach($credito as $cred)
                {
                    $credito_saldo += (float)$cred->valor_saldo;
                }
            }    

            $ped = PedidoFrotas::where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::FINALIZADO)
                               ->where('system_unit_id', '=', TSession::getValue('idunit'))
                               ->load();

            $pedidototal = 0;
            if($ped)
            {
                foreach($ped as $pedido)
                {
                    $pedidototal += $pedido->valor_liquido_proposta;
                }
            }


            TTransaction::close();

            $saldoctotal = $credito_saldo;

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
        $saldo_contratual_atual->setTotal('sum');
        $saldo_contratual_atual->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1.000.000,00";
        });
        $saldo_contratual_atual->setColors('rgb(114, 128, 107)', '#ffffff', 'rgb(53, 65, 50)', '#ffffff');
        $saldo_contratual_atual->setTitle("SALDO CONTRATUAL ATUAL", '#ffffff', '20', '');
        // $criteria_saldo_contratual_atual->add(new TFilter('saldo_entidade_contrato.deleted_at', 'is', NULL));
        // $saldo_contratual_atual->setCriteria($criteria_saldo_contratual_atual);
            $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $saldo_contratual_atual->setIcon(new TImage($icone. ' #ffffff'));
        $saldo_contratual_atual->setValueSize("20");
        $saldo_contratual_atual->setValueColor("#ffffff", 'B');
        $saldo_contratual_atual->setSize('100%', 95);
        $saldo_contratual_atual->setLayout('horizontal', 'left');

        $pedidos_em_elaboracao->setDatabase('minierp');
        $pedidos_em_elaboracao->setFieldValue("pedido_frotas.id");
        $pedidos_em_elaboracao->setModel('PedidoFrotas');
        $pedidos_em_elaboracao->setTotal('count');
        $pedidos_em_elaboracao->setColors('#BBE3E3', '#ffffff', '#81ECEC', '#ffffff');
        $pedidos_em_elaboracao->setTitle("pendente", '#ffffff', '20', '');
        $criteria_pedidos_em_elaboracao->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $pedidos_em_elaboracao->setCriteria($criteria_pedidos_em_elaboracao);
                $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $pedidos_em_elaboracao->setIcon(new TImage($icone. ' #ffffff'));

        $pedidos_em_elaboracao->setValueSize("20");
        $pedidos_em_elaboracao->setValueColor("#ffffff", 'B');
        $pedidos_em_elaboracao->setSize('100%', 95);
        $pedidos_em_elaboracao->setLayout('horizontal', 'left');

        $pedidos_em_elaboracao->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);

        $pedidos_em_analise_comercial->setDatabase('minierp');
        $pedidos_em_analise_comercial->setFieldValue("pedido_frotas.id");
        $pedidos_em_analise_comercial->setModel('PedidoFrotas');
        $pedidos_em_analise_comercial->setTotal('count');
        $pedidos_em_analise_comercial->setColors('#E1B1AC', '#FFFFFF', '#C0392B', '#FFFFFF');
        $pedidos_em_analise_comercial->setTitle("enviado", '#FFFFFF', '20', '');
        $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $pedidos_em_analise_comercial->setCriteria($criteria_pedidos_em_analise_comercial);
                    $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $pedidos_em_analise_comercial->setIcon(new TImage($icone. ' #ffffff'));
        $pedidos_em_analise_comercial->setValueSize("20");
        $pedidos_em_analise_comercial->setValueColor("#FFFFFF", 'B');
        $pedidos_em_analise_comercial->setSize('100%', 95);
        $pedidos_em_analise_comercial->setLayout('horizontal', 'left');

        $pedidos_em_analise_comercial->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);


        $pedidos_em_anlise_de_credito->setDatabase('minierp');
        $pedidos_em_anlise_de_credito->setFieldValue("pedido_frotas.id");
        $pedidos_em_anlise_de_credito->setModel('PedidoFrotas');
        $pedidos_em_anlise_de_credito->setTotal('count');
        $pedidos_em_anlise_de_credito->setColors('#A8E1A0', '#FFFFFF', '#44BD32', '#FFFFFF');
        $pedidos_em_anlise_de_credito->setTitle("Com proposta", '#FFFFFF', '20', '');
        $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $pedidos_em_anlise_de_credito->setCriteria($criteria_pedidos_em_anlise_de_credito);
                    $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $pedidos_em_anlise_de_credito->setIcon(new TImage($icone. ' #ffffff'));

        $pedidos_em_anlise_de_credito->setValueSize("20");
        $pedidos_em_anlise_de_credito->setValueColor("#FFFFFF", 'B');
        $pedidos_em_anlise_de_credito->setSize('100%', 95);
        $pedidos_em_anlise_de_credito->setLayout('horizontal', 'left');

        $pedidos_em_anlise_de_credito->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);

        $pedidos_em_processamento->setDatabase('minierp');
        $pedidos_em_processamento->setFieldValue("pedido_frotas.id");
        $pedidos_em_processamento->setModel('PedidoFrotas');
        $pedidos_em_processamento->setTotal('count');
        $pedidos_em_processamento->setColors('#54A0FF', '#FFFFFF', '#3498DB', '#FFFFFF');
        $pedidos_em_processamento->setTitle("aprovado", '#FFFFFF', '20', '');
        $criteria_pedidos_em_processamento->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $pedidos_em_processamento->setCriteria($criteria_pedidos_em_processamento);
                    $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $pedidos_em_processamento->setIcon(new TImage($icone. ' #ffffff'));
        $pedidos_em_processamento->setValueSize("20");
        $pedidos_em_processamento->setValueColor("#FFFFFF", 'B');
        $pedidos_em_processamento->setSize('100%', 95);
        $pedidos_em_processamento->setLayout('horizontal', 'left');

        $pedidos_em_processamento->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);

        $pedidos_em_faturamento->setDatabase('minierp');
        $pedidos_em_faturamento->setFieldValue("pedido_frotas.id");
        $pedidos_em_faturamento->setModel('PedidoFrotas');
        $pedidos_em_faturamento->setTotal('count');
        $pedidos_em_faturamento->setColors('#E6C17C', '#FFFFFF', '#FFA500', '#FFFFFF');
        $pedidos_em_faturamento->setTitle("Pagamento Aprovado", '#FFFFFF', '20', '');
        $criteria_pedidos_em_faturamento->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $pedidos_em_faturamento->setCriteria($criteria_pedidos_em_faturamento);
                $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $pedidos_em_faturamento->setIcon(new TImage($icone. ' #ffffff'));

        $pedidos_em_faturamento->setValueSize("20");
        $pedidos_em_faturamento->setValueColor("#FFFFFF", 'B');
        $pedidos_em_faturamento->setSize('100%', 95);
        $pedidos_em_faturamento->setLayout('horizontal', 'left');

        $pedidos_em_faturamento->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);


        $pedidos_em_faturamento_aguardando->setDatabase('minierp');
        $pedidos_em_faturamento_aguardando->setFieldValue("pedido_frotas.id");
        $pedidos_em_faturamento_aguardando->setModel('PedidoFrotas');
        $pedidos_em_faturamento_aguardando->setTotal('count');
        $pedidos_em_faturamento_aguardando->setColors('', '#FFFFFF', '#fd79a8', '#FFFFFF');
        $pedidos_em_faturamento_aguardando->setTitle("Aguardando aprovação", '#FFFFFF', '20', '');
        $criteria_pedidos_em_faturamento_aguardando->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $pedidos_em_faturamento_aguardando->setCriteria($criteria_pedidos_em_faturamento_aguardando);
                $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $pedidos_em_faturamento_aguardando->setIcon(new TImage($icone. ' #ffffff'));
        $pedidos_em_faturamento_aguardando->setValueSize("20");
        $pedidos_em_faturamento_aguardando->setValueColor("#FFFFFF", 'B');
        $pedidos_em_faturamento_aguardando->setSize('100%', 95);
        $pedidos_em_faturamento_aguardando->setLayout('horizontal', 'left');

        $pedidos_em_faturamento_entregue->setDatabase('minierp');
        $pedidos_em_faturamento_entregue->setFieldValue("pedido_frotas.id");
        $pedidos_em_faturamento_entregue->setModel('PedidoFrotas');
        $pedidos_em_faturamento_entregue->setTotal('count');
        $pedidos_em_faturamento_entregue->setColors('', '#FFFFFF', '#6f9b63', '#FFFFFF');
        $pedidos_em_faturamento_entregue->setTitle("ENTREGUE", '#FFFFFF', '20', '');
        $criteria_pedidos_em_faturamento_entregue->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $pedidos_em_faturamento_entregue->setCriteria($criteria_pedidos_em_faturamento_entregue);
                    $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $pedidos_em_faturamento_entregue->setIcon(new TImage($icone. ' #ffffff'));
    
        $pedidos_em_faturamento_entregue->setValueSize("20");
        $pedidos_em_faturamento_entregue->setValueColor("#FFFFFF", 'B');
        $pedidos_em_faturamento_entregue->setSize('100%', 95);
        $pedidos_em_faturamento_entregue->setLayout('horizontal', 'left');

        $pedidos_em_faturamento_entregue->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);

        $pedidos_aguardando_entrega->setDatabase('minierp');
        $pedidos_aguardando_entrega->setFieldValue("pedido_frotas.id");
        $pedidos_aguardando_entrega->setModel('PedidoFrotas');
        $pedidos_aguardando_entrega->setTotal('count');
        $pedidos_aguardando_entrega->setColors('#949191', '#FFFFFF', '#000000', '#FFFFFF');
        $pedidos_aguardando_entrega->setTitle("reprovado", '#FFFFFF', '20', '');
        $criteria_pedidos_aguardando_entrega->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $pedidos_aguardando_entrega->setCriteria($criteria_pedidos_aguardando_entrega);
                $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $pedidos_aguardando_entrega->setIcon(new TImage($icone. ' #ffffff'));
        $pedidos_aguardando_entrega->setValueSize("20");
        $pedidos_aguardando_entrega->setValueColor("#FFFFFF", 'B');
        $pedidos_aguardando_entrega->setSize('100%', 95);
        $pedidos_aguardando_entrega->setLayout('horizontal', 'left');

        $pedidos_finalizados->setDatabase('minierp');
        $pedidos_finalizados->setFieldValue("pedido_frotas.id");
        $pedidos_finalizados->setModel('PedidoFrotas');
        $pedidos_finalizados->setTotal('count');
        $pedidos_finalizados->setColors('#90D2AC', '#FFFFFF', '#2ECC71', '#FFFFFF');
        $pedidos_finalizados->setTitle("finalizados", '#FFFFFF', '20', '');
        $criteria_pedidos_finalizados->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $pedidos_finalizados->setCriteria($criteria_pedidos_finalizados);
                    $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $pedidos_finalizados->setIcon(new TImage($icone. ' #ffffff'));

        $pedidos_finalizados->setValueSize("20");
        $pedidos_finalizados->setValueColor("#FFFFFF", 'B');
        $pedidos_finalizados->setSize('100%', 95);
        $pedidos_finalizados->setLayout('horizontal', 'left');

        $pedidos_finalizados->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);

        $pedidos_cancelados->setDatabase('minierp');
        $pedidos_cancelados->setFieldValue("pedido_frotas.id");
        $pedidos_cancelados->setModel('PedidoFrotas');
        $pedidos_cancelados->setTotal('count');
        $pedidos_cancelados->setColors('#FF7675', '#FFFFFF', '#E74C3C', '#FFFFFF');
        $pedidos_cancelados->setTitle("cancelados", '#FFFFFF', '20', '');
        $criteria_pedidos_cancelados->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $pedidos_cancelados->setCriteria($criteria_pedidos_cancelados);
                $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $pedidos_cancelados->setIcon(new TImage($icone. ' #ffffff'));

        $pedidos_cancelados->setValueSize("20");
        $pedidos_cancelados->setValueColor("#FFFFFF", 'B');
        $pedidos_cancelados->setSize('100%', 95);
        $pedidos_cancelados->setLayout('horizontal', 'left');

        $pedidos_cancelados->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);

        
        $valor_empenho->setDatabase('minierp');
        $valor_empenho->setFieldValue("departamento_unit.valor_empenho");
        $valor_empenho->setModel('DepartamentoUnit');
         $valor_empenho->setTransformerValue(function($value)
        {
            //code here
               TTransaction::open('minierp');
               $depuser = SystemUserDepartamentoUnit::where('system_users_id','=', TSession::getValue('userid'))
                                       ->load();
                
                if ($depuser)
                {
                    foreach ($depuser as $departamentouser)
                    {
                        $depuser1[] = $departamentouser->departamento_unit_id;
                    }                
                }

                if (!$depuser1)
                {
                    $dep = DepartamentoUnit::where('system_unit_id','=',  TSession::getValue('idunit'))
                                       ->load();
                } else {
                    $dep = DepartamentoUnit::where('system_unit_id','=',  TSession::getValue('idunit'))
                                           ->where('id','in',$depuser1)
                                           ->load();
                }

        //        var_dump(TSession::getValue('idunit'));
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
            return "{$percentage}% de R$ 1.000.000,00";
        });
        $valor_empenho->setColors('#0F4626', '#ffffff', '#0C371E', '#ffffff');
        $valor_empenho->setTitle("Valores do empenho", '#ffffff', '20', '');
        $valor_empenho->setCriteria($criteria_valor_empenho);
                $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $valor_empenho->setIcon(new TImage($icone. ' #ffffff'));

        $valor_empenho->setValueSize("20");
        $valor_empenho->setValueColor("#ffffff", 'B');
        $valor_empenho->setSize('100%', 95);
        $valor_empenho->setLayout('horizontal', 'left');

        $valores_consumidos->setDatabase('minierp');
        $valores_consumidos->setFieldValue("pedido_frotas.valor_liquido_proposta");
        $valores_consumidos->setModel('PedidoFrotas');

          $valores_consumidos->setTransformerValue(function($value)
        {
            //code here
               TTransaction::open('minierp');

                 $depuser = SystemUserDepartamentoUnit::where('system_users_id','=', TSession::getValue('userid'))
                                       ->load();
                
                if ($depuser)
                {
                    foreach ($depuser as $departamentouser)
                    {
                        $depuser1[] = $departamentouser->departamento_unit_id;
                    }                
                }

                if (!$depuser1)
                {
                    $dep = DepartamentoUnit::where('system_unit_id','=',  TSession::getValue('idunit'))
                                       ->load();
                } else {
                    $dep = DepartamentoUnit::where('system_unit_id','=',  TSession::getValue('idunit'))
                                           ->where('id','in',$depuser1)
                                           ->load();
                }
                $value=0;
                if ($dep)
                {
                    foreach ($dep as $departamento)

                    {

                       $pedido = PedidoFrotas::where('departamento_unit_id','=',$departamento->id) 
                                       ->where('estado_pedido_frotas_id','=',EstadoPedidoFrotas::FINALIZADO)
                                       ->load();

                       foreach($pedido as $pedidos)                               
                       {
                           $value += $pedidos->valor_liquido_proposta;
                       }

                    }                
                }
                TTransaction::close();
                return "R$ " . number_format($value, 2, ",", ".");

        });
        $valores_consumidos->setTotal('sum');
        $valores_consumidos->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1.000.000,00";
        });
        $valores_consumidos->setColors('#9852A3', '#ffffff', '#9D27AE', '#ffffff');
        $valores_consumidos->setTitle("Valores consumidos", '#ffffff', '20', '');
        $criteria_valores_consumidos->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $valores_consumidos->setCriteria($criteria_valores_consumidos);
                            $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $valores_consumidos->setIcon(new TImage($icone. ' #ffffff'));
        $valores_consumidos->setValueSize("20");
        $valores_consumidos->setValueColor("#ffffff", 'B');
        $valores_consumidos->setSize('100%', 95);
        $valores_consumidos->setLayout('horizontal', 'left');

        $saldo_atual->setDatabase('minierp');
        $saldo_atual->setFieldValue("pedido_frotas.valor_liquido_proposta");
        $saldo_atual->setModel('PedidoFrotas');
        $saldo_atual->setTotal('sum');
        $saldo_atual->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1.000.000,00";
        });
        $saldo_atual->setColors('#A1C07D', '#ffffff', '#8BC34A', '#ffffff');
        $saldo_atual->setTitle("SALDO ATUAL", '#ffffff', '20', '');
        $criteria_saldo_atual->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $saldo_atual->setCriteria($criteria_saldo_atual);
            $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $saldo_atual->setIcon(new TImage($icone. ' #ffffff'));
        $saldo_atual->setValueSize("20");
        $saldo_atual->setValueColor("#ffffff", 'B');
        $saldo_atual->setSize('100%', 95);
        $saldo_atual->setLayout('horizontal', 'left');
        $saldo_atual->setTransformerValue(function($value)
        {

            TTransaction::open('minierp');

             $depuser = SystemUserDepartamentoUnit::where('system_users_id','=', TSession::getValue('userid'))
                                       ->load();
                
                if ($depuser)
                {
                    foreach ($depuser as $departamentouser)
                    {
                        $depuser1[] = $departamentouser->departamento_unit_id;
                    }                
                }

                if (!$depuser1)
                {
                    $dep = DepartamentoUnit::where('system_unit_id','=',  TSession::getValue('idunit'))
                                       ->load();
                } else {
                    $dep = DepartamentoUnit::where('system_unit_id','=',  TSession::getValue('idunit'))
                                           ->where('id','in',$depuser1)
                                           ->load();
                }

       //     var_dump(TSession::getValue('idunit'));
            $value=0;
            if ($dep)
            {
                foreach ($dep as $departamento)

                {

                   $pedido = PedidoFrotas::where('departamento_unit_id','=',$departamento->id) 
                                   ->where('estado_pedido_frotas_id','=',EstadoPedidoFrotas::FINALIZADO)
                                   ->load();

                   foreach($pedido as $pedidos)                               
                   {
                       $value += $pedidos->valor_liquido_proposta;
                   }

                }                
            }

           $valueempenho=0;    

            $depuser = SystemUserDepartamentoUnit::where('system_users_id','=', TSession::getValue('userid'))
                                       ->load();
                
                if ($depuser)
                {
                    foreach ($depuser as $departamentouser)
                    {
                        $depuser1[] = $departamentouser->departamento_unit_id;
                    }                
                }

                if (!$depuser1)
                {
                    $objects = DepartamentoUnit::where('system_unit_id','=',  TSession::getValue('idunit'))
                                       ->load();
                } else {
                    $objects = DepartamentoUnit::where('system_unit_id','=',  TSession::getValue('idunit'))
                                           ->where('id','in',$depuser1)
                                           ->load();
                }
                // $objects = DepartamentoUnit::where('system_unit_id','=',TSession::getValue('idunit'))
                //                            ->load();

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
            return "{$percentage}% de R$ 1.000.000,00";
        });
        $saldo_atual->setColors('#A1C07D', '#ffffff', '#8BC34A', '#ffffff');
        $saldo_atual->setTitle("SALDO ATUAL", '#ffffff', '20', '');
        $criteria_saldo_atual->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $saldo_atual->setCriteria($criteria_saldo_atual);
                $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $saldo_atual->setIcon(new TImage($icone. ' #ffffff'));
        $saldo_atual->setValueSize("20");
        $saldo_atual->setValueColor("#ffffff", 'B');
        $saldo_atual->setSize('100%', 95);
        $saldo_atual->setLayout('horizontal', 'left');


        //Garantia ha vencer:

        $dbmanutencao_column_id = new BTableColumnChart('id', "id", 'center', '5%');
        $dbmanutencao_column_proposta_id = new BTableColumnChart('propostas_id', "id Propostas", 'center', '5%');
        $dbmanutencao_column_tipo = new BTableColumnChart('tipo', "Tipo", 'center', '8%');
        $dbmanutencao_column_produto_id = new BTableColumnChart('produto.nome', "Produto/Serviço", 'left', '20%');
        $dbmanutencao_column_descricao = new BTableColumnChart('descricao', "Obs", 'left', '20%');
        $dbmanutencao_column_placa = new BTableColumnChart('veiculos.placa', "Placa", 'center', '7%');
        $dbmanutencao_column_quantidade = new BTableColumnChart('qtde', "Qtde", 'center', '5%');
        $dbmanutencao_column_modelo_descricao = new BTableColumnChart('modelo.descricao', "Modelo", 'center', '10%');
        $dbmanutencao_column_marca_descricao = new BTableColumnChart('marca.descricao', "Marca", 'center', '10%');
        $dbmanutencao_column_datagarantia = new BTableColumnChart('datagarantia', "Data Garantia", 'center', '10%');
        $dbmanutencao_column_ativo = new BTableColumnChart('ativo', "Notificação ativa", 'center', '12%');

        $dbmanutencao_column_tipo->setTransformer(function($value, $object, $row, $cell, $left_row)
        {
            if($object->tipo == 1){
                return "<span style='background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 8px; font-weight: bold;'> Produto </span>";
            }
            else{
                return "<span style='background-color: #2196F3; color: white; padding: 2px 8px; border-radius: 8px; font-weight: bold;'> Serviço </span>";
            }
        });

        $dbmanutencao_column_datagarantia->setTransformer(function($value, $object, $row)
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

        $dbmanutencao_column_ativo->setTransformer(function($value, $object)
        {
            // $bgcolor = '#4CAF50';

            $action = new TAction(['ManutencaoGarantiaForm', 'onSetProject'], ['key'=>$object->id, 'redirect' => 'formdashboard']);
            // $a = new TElement('a');
            // $a->class = 'btn btn-link';
            // $a->style = "<span background-color: #4CAF50; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none;>Desativar</span>";
            // $a->generator = 'adianti';
            // $a->href = $action->serialize();
            // $a->add('Ativar');
            // return $a;

            $a = new TElement('a');
            $a->generator = 'adianti';
            $a->href = $action->serialize();

            // Estilo mínimo para garantir que o texto apareça
            $a->style = 'background-color:rgb(153, 153, 153); color: white; padding: 0px 6px; border-radius: 12px; text-decoration: none; color: white; cursor: pointer;';
            
            // Adiciona o texto puro
            $label = new TElement('span');
            $label->add($value == 1 ? 'Desativar' : 'Ativar/Desativar');

            $a->add($label);

            return $a;

        });



        $mesSelecionado = $mes->getValue() ?? date('m');
        $anoSelecionado = $ano->getValue() ?? date('y');

        $dataFiltro = "{$anoSelecionado}-{$mesSelecionado}-01";

        // $criteria_dbmanutencao->setProperty('limitade');
        // $criteria_dbmanutencao->add(new TFilter('veiculos.updated_at', 'is', NULL));
        // $criteria_dbmanutencao->add(new TFilter('manutencao_garantia.deleted_at', 'is', NULL));
        $criteria_dbmanutencao->add(new TFilter('manutencao_garantia.ativo', '=', 'S'));
        $criteria_dbmanutencao->add(new TFilter('datagarantia', '>=', $dataFiltro));
        $criteria_dbmanutencao->add(new TFilter('veiculos.system_unit_id', '=', TSession::getValue('idunit')));


        $dbmanutencao->setDatabase('minierp');
        $dbmanutencao->setModel('ManutencaoGarantia');
        $dbmanutencao->setTitle("<span style='font-weight: bold;'>Manutenção de Garantia há Vencer</span>");
        $dbmanutencao->setSize('100%', 280);
        $dbmanutencao->setColumns([$dbmanutencao_column_id, $dbmanutencao_column_proposta_id, $dbmanutencao_column_tipo,$dbmanutencao_column_produto_id,$dbmanutencao_column_descricao, $dbmanutencao_column_quantidade, $dbmanutencao_column_placa, $dbmanutencao_column_marca_descricao,$dbmanutencao_column_modelo_descricao,$dbmanutencao_column_datagarantia,$dbmanutencao_column_ativo]);
        $dbmanutencao->setCriteria($criteria_dbmanutencao);
        $dbmanutencao->setJoins([
             'veiculos' => ['manutencao_garantia.veiculos_id', 'veiculos.id'],
             'marca' => ['veiculos.marca_id', 'marca.id'],
             'modelo' => ['veiculos.modelo_id', 'modelo.id'],
             'produto' => ['manutencao_garantia.produto_id', 'produto.id'],
             'departamento_unit' => ['veiculos.departamento_unit_id', 'departamento_unit.id']
        ]);

        $dbmanutencao->setRowColorOdd('#F9F9F9');
        $dbmanutencao->setRowColorEven('#FFFFFF');
        $dbmanutencao->setFontRowColorOdd('#333333');
        $dbmanutencao->setFontRowColorEven('#333333');
        $dbmanutencao->setBorderColor('#A03939');
        $dbmanutencao->setTableHeaderColor('#FFFFFF');
        $dbmanutencao->setTableHeaderFontColor('#333333');
        $dbmanutencao->setTableFooterColor('#F28181');
        $dbmanutencao->setTableFooterFontColor('#333333');

        // $dbmanutencao->setGroupColumn("propostas_id", function($value, $object, $row)
        // {
        //     if(!$value)
        //     {
        //         $value = 0;
        //     }

        //     if(is_numeric($value))
        //     {
        //         return "R$ " . number_format($value, 2, ",", ".");
        //     }
        //     else
        //     {
        //         return $value;
        //     }
        // }, true);

        $dstatuscnh_column_id = new BTableColumnChart('id', "id", 'center', '5%');
        $dstatuscnh_column_nome = new BTableColumnChart('nome', "Nome", 'center', '15%');
        $dstatuscnh_column_numerocnh = new BTableColumnChart('numero_registro_cnh', "N° CNH", 'center', '10%');
        $dstatuscnh_column_datavalidade = new BTableColumnChart('data_validade_cnh', "Data Vencimento CNH", 'center', '10%');
        $dstatuscnh_column_diasavencer = new BTableColumnChart('dias_para_vencer', "Status CNH", 'center', '10%');
        $dstatuscnh_column_hiddenstatuscnh = new BTableColumnChart('status_cnh', '', 'center', '1%');
        $dstatuscnh_column_systemunit = new BTableColumnChart('system_unit_name', "Unidade", 'center', '5%');

        $dstatuscnh_column_numerocnh->setTransformer(function($value)
        {
            return empty($value) ? "Não cadastrado" : $value;
        });

        $dstatuscnh_column_datavalidade->setTransformer(function($value, $object, $row)
        {
                if(!empty($value)){
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                else
                {
                    return "Não cadastrada";
                }
            
        });

        $dstatuscnh_column_diasavencer->setTransformer(function($value, $object, $row, $cell, $left_row)
        {
            // $value aqui é status_cnh (texto)
            $status = $object->status_cnh;

            if ($status === 'NAO_CADASTRADA') {
                return "<span style='background-color: #FD9203;color:white;padding:2px 8px;border-radius:8px;font-weight:bold;'>Não cadastrada</span>";
            }

            // vem da VIEW: dias_para_vencer (negativo = vencida)
            $dias = isset($object->dias_para_vencer) ? (int) $object->dias_para_vencer : null;

            if ($status === 'VENCIDA') {
                $diasvenc = $dias !== null ? abs($dias) : 0;
                return "<span style='background-color:#f32121;color:white;padding:2px 8px;border-radius:8px;font-weight:bold;'>Vencida há {$diasvenc} dia(s)</span>";
            }

            if ($status === 'AVENCER') {
                $diasfalt = $dias !== null ? $dias : 0; // aqui normalmente é positivo
                return "<span style='background-color:#2195f3;color:white;padding:2px 8px;border-radius:8px;font-weight:bold;'>A vencer / {$diasfalt} dia(s)</span>";
            }

            return "";
        });

        $dstatuscnh_column_hiddenstatuscnh->setTransformer(function() 
        { 
            return ''; 
        });


        $criteria_dstatuscnh->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit'))); //func
        $criteria_dstatuscnh->add(new TFilter('status_cnh', 'in', ['VENCIDA','AVENCER','NAO_CADASTRADA']));
        $criteria_dstatuscnh->setProperty('order', 'ordem_status, data_validade_cnh');
        $criteria_dstatuscnh->setProperty('direction', 'asc');

        $criteria_dstatuscnh->setProperty(
            'order',
            "ordem_status,
            CASE 
                WHEN status_cnh = 'AVENCER' THEN dias_para_vencer
                WHEN status_cnh = 'VENCIDA' THEN -dias_para_vencer
                ELSE -1
            END ASC,
            data_validade_cnh"
        );
        $criteria_dstatuscnh->setProperty('direction', 'desc');


        $dstatuscnh->setDatabase('minierp');
        $dstatuscnh->setModel('ViewPessoaCnh');
        $dstatuscnh->setTitle("<span style='font-weight: bold;'>Status CNH Condutor</span>");
        $dstatuscnh->setSize('100%', 280);
        $dstatuscnh->setColumns([$dstatuscnh_column_id, $dstatuscnh_column_nome, $dstatuscnh_column_numerocnh,$dstatuscnh_column_datavalidade, $dstatuscnh_column_diasavencer, $dstatuscnh_column_hiddenstatuscnh,$dstatuscnh_column_systemunit]);
        $dstatuscnh->setCriteria($criteria_dstatuscnh);

        // $dstatuscnh->setJoins([
        //      'system_unit' => ['pessoa.system_unit_id', 'system_unit.id'],
        //     'pessoa_grupo' => ['pessoa_grupo.pessoa_id', 'pessoa.id']
        // ]);
        // $dstatuscnh->setJoins([
        //     'system_unit' => ['system_unit_id', 'system_unit.id'],
        // ]);

        $dstatuscnh->setRowColorOdd('#F9F9F9');
        $dstatuscnh->setRowColorEven('#FFFFFF');
        $dstatuscnh->setFontRowColorOdd('#333333');
        $dstatuscnh->setFontRowColorEven('#333333');
        $dstatuscnh->setBorderColor('#A03939');
        $dstatuscnh->setTableHeaderColor('#FFFFFF');
        $dstatuscnh->setTableHeaderFontColor('#333333');
        $dstatuscnh->setTableFooterColor('#F28181');
        $dstatuscnh->setTableFooterFontColor('#333333');


        $total_de_vendas_por_mes->setDatabase('minierp');
        $total_de_vendas_por_mes->setFieldValue("pedido_frotas.valor_liquido_proposta");
        $total_de_vendas_por_mes->setFieldGroup(["pedido_frotas.mes"]);
        $total_de_vendas_por_mes->setModel('PedidoFrotas');
        $total_de_vendas_por_mes->setTitle("Total de Pedidos por Mês");
        $total_de_vendas_por_mes->setSize('100%', 280);
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
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $total_de_vendas_por_mes->setCriteria($criteria_total_de_vendas_por_mes);
        $total_de_vendas_por_mes->setLabelValue("Total no mês");
        $total_de_vendas_por_mes->setSize('100%', 280);
        $total_de_vendas_por_mes->disableZoom();

        $total_de_vendas_por_mes->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);

        $total_de_vendas_por_dia->setDatabase('minierp');
        $total_de_vendas_por_dia->setFieldValue("pedido_frotas.valor_liquido_proposta");
        $total_de_vendas_por_dia->setFieldGroup(["pedido_frotas.dt_pedido"]);
        $total_de_vendas_por_dia->setModel('PedidoFrotas');
        $total_de_vendas_por_dia->setTitle("Total de Pedidos por Dia");
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
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $total_de_vendas_por_dia->setCriteria($criteria_total_de_vendas_por_dia);
        $total_de_vendas_por_dia->setLabelValue("Total no dia");
        $total_de_vendas_por_dia->setRotateLegend('35',60);
        $total_de_vendas_por_dia->setSize('100%', 280);
        $total_de_vendas_por_dia->disableZoom();

        $total_de_vendas_por_dia->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);

        $total_por_cliente_column_pessoa_nome = new BTableColumnChart('pessoa.nome', "Fornecedor", 'left','33%');
        $total_por_cliente_column_id = new BTableColumnChart('id', "Pedidos", 'center');
        $total_por_cliente_column_valor_total = new BTableColumnChart('valor_liquido_proposta', "Valor Liquido", 'right');
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
        $total_por_cliente->setModel('PedidoFrotas');
        $total_por_cliente->setTitle("<span style='font-weight: bold;'>Totalizadores por Fornecedores</span>");
        $total_por_cliente->setSize('100%', 250);
        $total_por_cliente->setColumns([$total_por_cliente_column_pessoa_nome,$total_por_cliente_column_id,$total_por_cliente_column_valor_total]);
        $total_por_cliente->setCriteria($criteria_total_por_cliente);
        $total_por_cliente->setJoins([
             'pessoa' => ['pedido_frotas.estabelecimento_id', 'pessoa.id'],
             'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
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
        $row2->layout = ['col-sm-3','col-sm-3',' col-sm-3',' col-sm-3', 'col-sm-3'];

        $row3 = $this->form->addFields([$pedidos_em_faturamento],[$pedidos_cancelados],[$pedidos_finalizados],[$pedidos_em_faturamento_entregue]);
        $row3->layout = ['col-sm-3','col-sm-3',' col-sm-3',' col-sm-3'];

     //   $row8 = $this->form->addFields([$pedidos_em_faturamento_aguardando],[$pedidos_em_faturamento_entregue],[],[]);
     //   $row8->layout = ['col-sm-3','col-sm-3',' col-sm-3',' col-sm-3'];

        $row4 = $this->form->addFields([$valor_empenho],[$valores_consumidos],[$saldo_atual]);
        $row4->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        // Adiciona ao formulário
        $row_garantias = $this->form->addFields([$dbmanutencao]);
        $row_garantias->layout = [' col-sm-12'];
        
        $row_statuscnh = $this->form->addFields([$dstatuscnh]);
        $row_statuscnh->layout = [' col-sm-12'];

        $row5 = $this->form->addFields([$total_de_vendas_por_mes]);
        $row5->layout = [' col-sm-12'];

        $row6 = $this->form->addFields([$total_de_vendas_por_dia]);
        $row6->layout = [' col-sm-12'];

        $row7 = $this->form->addFields([$total_por_cliente]);
        $row7->layout = [' col-sm-12'];

        if(!isset($param['mes']) && $mes->getValue())
        {
            $_POST['mes'] = $mes->getValue();
        }
        if(!isset($param['ano']) && $ano->getValue())
        {
            $_POST['ano'] = $ano->getValue();
        }

        $searchData = $this->form->getData();
        $this->form->setData($searchData);

        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_total_de_vendas_por_mes->add(new TFilter('pedido_frotas.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_total_de_vendas_por_dia->add(new TFilter('pedido_frotas.mes', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_total_de_vendas_por_dia->add(new TFilter('pedido_frotas.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_total_por_cliente->add(new TFilter('pedido_frotas.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_total_por_cliente->add(new TFilter('pedido_frotas.mes', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_pedidos_em_elaboracao->add(new TFilter('pedido_frotas.ano', '=', $filterVar));
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_pedidos_em_elaboracao->add(new TFilter('pedido_frotas.mes', '=', $filterVar));
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido_frotas.ano', '=', $filterVar));
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_pedidos_em_analise_comercial->add(new TFilter('pedido_frotas.mes', '=', $filterVar));
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido_frotas.ano', '=', $filterVar));
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_pedidos_em_anlise_de_credito->add(new TFilter('pedido_frotas.mes', '=', $filterVar));
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_pedidos_em_processamento->add(new TFilter('pedido_frotas.ano', '=', $filterVar));
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_pedidos_em_processamento->add(new TFilter('pedido_frotas.mes', '=', $filterVar));
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_pedidos_em_faturamento->add(new TFilter('pedido_frotas.ano', '=', $filterVar));
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_pedidos_em_faturamento->add(new TFilter('pedido_frotas.mes', '=', $filterVar));
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_pedidos_em_faturamento_entregue->add(new TFilter('pedido_frotas.ano', '=', $filterVar));
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_pedidos_em_faturamento_entregue->add(new TFilter('pedido_frotas.mes', '=', $filterVar));
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_pedidos_finalizados->add(new TFilter('pedido_frotas.ano', '=', $filterVar));
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_pedidos_finalizados->add(new TFilter('pedido_frotas.mes', '=', $filterVar));
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_pedidos_cancelados->add(new TFilter('pedido_frotas.ano', '=', $filterVar));
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_pedidos_cancelados->add(new TFilter('pedido_frotas.mes', '=', $filterVar));
        }
        

        BChart::generate($saldo_contratual_total, $saldo_contratual_atual, $pedidos_em_elaboracao, $pedidos_em_analise_comercial, $pedidos_em_anlise_de_credito, $pedidos_em_processamento, $pedidos_em_faturamento, $pedidos_aguardando_entrega, $pedidos_finalizados, $pedidos_cancelados, $valor_empenho, $valores_consumidos, $saldo_atual, $dbmanutencao, $dstatuscnh, $total_de_vendas_por_mes, $total_de_vendas_por_dia, $total_por_cliente);

        // create the form actions

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
           // $container->add(TBreadCrumb::create(["Pedido","Dashboard"]));
           if (!empty($AlertMensagem)) {
                $container->add($TAlert);
           }           
        }
        $container->add($this->form);

        parent::add($container);
        if (TSession::getValue('exibir_popup_plano_manutencao')==1) {
           TScript::create("setTimeout(function(){ __adianti_load_page('engine.php?class=ManutencaoGarantiaPopup'); }, 300);");
        }

    }

    public function onShow($param = null)
    {               

    } 

    public function getIconeFrota($tipoFrota)
{

    switch ((int) $tipoFrota) {
        case 2: return 'fas:plane';   // Aeronave
        case 3: return 'fas:tractor'; // Equipamentos
        case 1: return 'fas:car';
        default: return 'fas:car';    // Veículo
    }
}

}

