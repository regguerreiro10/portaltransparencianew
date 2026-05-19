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

        $filterVar = [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PGTOAPROVADO, EstadoPedidoFrotas::ENTREGUE, EstadoPedidoFrotas::FINALIZADO, EstadoPedidoFrotas::PREAPROVADO];
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', 'in', $filterVar)); 
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));

        $filterVar = [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PGTOAPROVADO, EstadoPedidoFrotas::ENTREGUE, EstadoPedidoFrotas::FINALIZADO, EstadoPedidoFrotas::PREAPROVADO];
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', 'in', $filterVar)); 
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));

        $filterVar = [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PGTOAPROVADO, EstadoPedidoFrotas::ENTREGUE, EstadoPedidoFrotas::FINALIZADO, EstadoPedidoFrotas::PREAPROVADO];
        $criteria_total_por_cliente->add(new TFilter('pedido_frotas.estado_pedido_frotas_id', 'in', $filterVar)); 
        $criteria_total_por_cliente->add(new TFilter('pedido_frotas.system_unit_id', '=', TSession::getValue('idunit')));
        $criteria_total_por_cliente->add(new TFilter('pedido_frotas.departamento_unit_id', 'in', $departamentosPermitidosIds));
        $criteria_total_por_cliente->add(new TFilter('pedido_frotas.estabelecimento_id', 'is not', NULL));
  
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


        $button_buscar->setAction(new TAction(['DashboardPedidoFrotas', 'onShow']), "Atualizar");
        // $button_buscar->setAction(new TAction([$this, 'onShow'], $this->form->getData()->toArray()), "Buscar");
        $button_buscar->addStyleClass('btn-primary');
        $button_buscar->setImage('fas:search #FFFFFF');
        $mes->setSize('100%');
        $ano->setSize('100%');

        $ano->addItems(TempoService::getAnos());
        $mes->addItems(['' => 'Todos'] + TempoService::getMeses());

        $mes->setValue($param['mes'] ?? '');
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
                                            ->where('deleted_at', 'is', NULL)
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
        $saldo_contratual_atual->setTransformerValue(function($value) use ($departamentosPermitidosIds)
        {
            TTransaction::open('minierp');

            $credito = SaldoEntidadeContrato::where('entidade_id', '=', TSession::getValue('entidade'))
                                           ->where('deleted_at', 'is', NULL)
                                           ->load();

            $credito_saldo = 0;
            if($credito)
            {
                foreach($credito as $cred)
                {
                    $credito_saldo += (float)$cred->valor_saldo;
                }
            }

            $consumo = 0;
            $pedidos = PedidoFrotas::where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::FINALIZADO)
                                   ->where('system_unit_id', '=', TSession::getValue('idunit'))
                                   ->where('departamento_unit_id', 'in', $departamentosPermitidosIds)
                                   ->where('deleted_at', 'is', NULL)
                                   ->load();

            if($pedidos)
            {
                foreach($pedidos as $pedido)
                {
                    $dotacoes = DotacaoPedidoFrotas::where('pedido_frotas_id', '=', $pedido->id)
                        ->where('deleted_at', 'is', NULL)
                        ->load();

                    if ($dotacoes)
                    {
                        foreach($dotacoes as $dotacao)
                        {
                            $consumo += (float) $dotacao->valor;
                        }
                    }
                    else
                    {
                        $consumo += (float) $pedido->valor_liquido_proposta;
                    }
                }
            }

            TTransaction::close();

            $value = $credito_saldo - $consumo;

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
    TTransaction::open('minierp');

    $value = 0;

    $departamentos = DepartamentoUnit::where('system_unit_id', '=', TSession::getValue('idunit'))
        ->where('deleted_at', 'is', NULL)
        ->load();

    if ($departamentos)
    {
        foreach ($departamentos as $departamento)
        {
            $pedidos = PedidoFrotas::where('departamento_unit_id', '=', $departamento->id)
                ->where('estado_pedido_frotas_id', 'in', [
                    EstadoPedidoFrotas::FINALIZADO,
                    EstadoPedidoFrotas::ENTREGUE,
                    EstadoPedidoFrotas::PGTOAPROVADO,
                    EstadoPedidoFrotas::PREAPROVADO,
                    EstadoPedidoFrotas::APROVADO
                ])
                ->where('deleted_at', 'is', NULL)
                ->load();

            if ($pedidos)
            {
                foreach($pedidos as $pedido)
                {
                    $value += $pedido->valor_liquido_proposta ?? 0;
                }
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

    $value = 0;
    $valueempenho = 0;

    $departamentos = DepartamentoUnit::where('system_unit_id', '=', TSession::getValue('idunit'))
        ->where('deleted_at', 'is', NULL)
        ->load();

    if ($departamentos)
    {
        foreach ($departamentos as $departamento)
        {
            $valueempenho += $departamento->valor_empenho ?? 0;

$pedidos = PedidoFrotas::where('departamento_unit_id', '=', $departamento->id)
    ->where('estado_pedido_frotas_id', 'in', [
        EstadoPedidoFrotas::FINALIZADO,
        EstadoPedidoFrotas::ENTREGUE,
        EstadoPedidoFrotas::PGTOAPROVADO,
        EstadoPedidoFrotas::PREAPROVADO,
        EstadoPedidoFrotas::APROVADO
    ])
    ->where('deleted_at', 'is', NULL)
    ->load();
            if ($pedidos)
            {
                foreach ($pedidos as $pedido)
                {
                    $value += $pedido->valor_liquido_proposta ?? 0;
                }
            }
        }
    }

    TTransaction::close();

    $saldo = $valueempenho - $value;

    return "R$ " . number_format($saldo, 2, ",", ".");
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



        $mesSelecionado = $mes->getValue();
        $anoSelecionado = $ano->getValue() ?: date('Y');

        $dataFiltro = $mesSelecionado ? sprintf('%04d-%02d-01', (int) $anoSelecionado, (int) $mesSelecionado) : date('Y-m-d');

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
        $total_de_vendas_por_mes->setTitle("Valor total dos pedidos por mês");
        $total_de_vendas_por_mes->setSize('100%', 300);
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
        $total_de_vendas_por_mes->showGrid(true);
        $total_de_vendas_por_mes->setColors(['#315a8a']);
        $criteria_total_de_vendas_por_mes->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $total_de_vendas_por_mes->setCriteria($criteria_total_de_vendas_por_mes);
        $total_de_vendas_por_mes->setLabelValue("Valor no mês");
        $total_de_vendas_por_mes->setSize('100%', 300);
        $total_de_vendas_por_mes->disableZoom();

        $total_de_vendas_por_mes->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);

        $total_de_vendas_por_dia->setDatabase('minierp');
        $total_de_vendas_por_dia->setFieldValue("pedido_frotas.valor_liquido_proposta");
        $total_de_vendas_por_dia->setFieldGroup(["pedido_frotas.dt_pedido"]);
        $total_de_vendas_por_dia->setModel('PedidoFrotas');
        $total_de_vendas_por_dia->setTitle("Valor total dos pedidos por dia");
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
        $total_de_vendas_por_dia->showGrid(true);
        $total_de_vendas_por_dia->setColors(['#237a3b']);
        $criteria_total_de_vendas_por_dia->add(new TFilter('pedido_frotas.deleted_at', 'is', NULL));
        $total_de_vendas_por_dia->setCriteria($criteria_total_de_vendas_por_dia);
        $total_de_vendas_por_dia->setLabelValue("Valor no dia");
        $total_de_vendas_por_dia->setRotateLegend('35',60);
        $total_de_vendas_por_dia->setSize('100%', 300);
        $total_de_vendas_por_dia->disableZoom();

        $total_de_vendas_por_dia->setJoins([
            'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);

        $total_por_cliente_column_pessoa_nome = new BTableColumnChart('pessoa.nome', "Fornecedor", 'left','55%');
        $total_por_cliente_column_id = new BTableColumnChart('id', "Pedidos", 'center');
        $total_por_cliente_column_valor_total = new BTableColumnChart('valor_liquido_proposta', "Valor líquido", 'right', '', 'desc');
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
        $total_por_cliente->setTitle("<span style='font-weight: bold;'>Totalizadores por fornecedores</span>");
        $total_por_cliente->setSize('100%', 330);
        $total_por_cliente->setColumns([$total_por_cliente_column_pessoa_nome,$total_por_cliente_column_id,$total_por_cliente_column_valor_total]);
        $total_por_cliente->setCriteria($criteria_total_por_cliente);
        $total_por_cliente->setJoins([
             'pessoa' => ['pedido_frotas.estabelecimento_id', 'pessoa.id'],
             'departamento_unit' => ['pedido_frotas.departamento_unit_id', 'departamento_unit.id']
        ]);

        $total_por_cliente->setRowColorOdd('#F7F9FC');
        $total_por_cliente->setRowColorEven('#FFFFFF');
        $total_por_cliente->setFontRowColorOdd('#3f4d67');
        $total_por_cliente->setFontRowColorEven('#3f4d67');
        $total_por_cliente->setBorderColor('#E4EBF3');
        $total_por_cliente->setTableHeaderColor('#F7F9FC');
        $total_por_cliente->setTableHeaderFontColor('#3f4d67');
        $total_por_cliente->setTableFooterColor('#F7F9FC');
        $total_por_cliente->setTableFooterFontColor('#3f4d67');

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

        $rowEmpenhos = $this->form->addFields([$valor_empenho],[$valores_consumidos],[$saldo_atual]);
        $rowEmpenhos->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $resumoDepartamentos = $this->buildResumoDepartamentosPanel($departamentosPermitidosIds);
        $row4 = $this->form->addFields([$resumoDepartamentos]);
        $row4->layout = [' col-sm-12'];

        if ($this->usuarioPodeVerDivergenciasDotacao())
        {
            $divergenciasDotacao = $this->buildDivergenciasDotacaoPanel($departamentosPermitidosIds);
            $rowDivergenciasDotacao = $this->form->addFields([$divergenciasDotacao]);
            $rowDivergenciasDotacao->layout = [' col-sm-12'];
        }

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

    private function buildResumoDepartamentosPanel(array $departamentosPermitidosIds)
    {
        $panel = new TPanelGroup("<span style='font-weight: bold;'>Dotações e saldos por departamento (Valores empenhados)</span>");
        $panel->getBody()->style = 'padding:0;';

        $linhas = $this->getResumoDotacoesDepartamento($departamentosPermitidosIds);
        $panel->add($this->buildResumoDepartamentosHeader($linhas));

        $wrapper = new TElement('div');
        $wrapper->style = 'width:100%; max-height:460px; overflow:auto; border-top:1px solid #e6edf5;';

        $table = new TElement('table');
        $table->class = 'table table-hover';
        $table->style = 'margin:0; min-width:1120px; border-collapse:separate; border-spacing:0;';

        $thead = new TElement('thead');
        $headRow = new TElement('tr');
        foreach (['Departamento', 'Tipo', 'Empenhos', 'Saldo Empenho', 'Total Pedidos', 'Saldo Atual', 'Qtde Pedidos', 'Uso'] as $label)
        {
            $th = new TElement('th');
            $th->style = 'font-size:11px; text-transform:uppercase; letter-spacing:0; color:#3f4d67; border-bottom:1px solid #d9e2ef; white-space:nowrap; position:sticky; top:0; z-index:2; background:#f7f9fc; padding:10px 12px;';
            $th->add($label);
            $headRow->add($th);
        }
        $thead->add($headRow);
        $table->add($thead);

        $tbody = new TElement('tbody');

        if (empty($linhas))
        {
            $tr = new TElement('tr');
            $td = new TElement('td');
            $td->colspan = 8;
            $td->style = 'text-align:center; color:#777; padding:20px;';
            $td->add('Nenhum departamento com dotação orçamentária encontrado.');
            $tr->add($td);
            $tbody->add($tr);
        }

        foreach ($linhas as $linha)
        {
            $tr = new TElement('tr');
            $tr->style = 'background:#fff;';
            $percentual = $this->calcularPercentualUso($linha);
            $cor = '#28a745';
            if ($percentual >= 90)
            {
                $cor = '#dc3545';
            }
            elseif ($percentual >= 70)
            {
                $cor = '#f0ad4e';
            }

            $cells = [
                htmlspecialchars($linha['departamento'], ENT_QUOTES, 'UTF-8'),
                $this->renderTipoDotacao($linha['tipo']),
                $this->renderQtdeEmpenhos($linha['qtde_empenhos']),
                $this->renderValorFinanceiro($linha['saldo_empenho']),
                $this->renderValorFinanceiro($linha['total_pedidos']),
                $this->renderValorFinanceiro($linha['saldo_atual'], true),
                $this->renderQtdePedidos($linha['qtde_pedidos']),
            ];

            foreach ($cells as $index => $value)
            {
                $td = new TElement('td');
                if ($index === 0)
                {
                    $td->style = 'vertical-align:middle; font-weight:600; min-width:320px; color:#3f4d67; padding:10px 12px; border-top:1px solid #edf1f7;';
                }
                elseif (in_array($index, [3, 4, 5]))
                {
                    $td->style = 'vertical-align:middle; white-space:nowrap; text-align:right; padding:10px 12px; border-top:1px solid #edf1f7;';
                }
                else
                {
                    $td->style = 'vertical-align:middle; white-space:nowrap; padding:10px 12px; border-top:1px solid #edf1f7;';
                }
                $td->add($value);
                $tr->add($td);
            }

            $usage = new TElement('td');
            $usage->style = 'vertical-align:middle; min-width:190px; padding:10px 12px; border-top:1px solid #edf1f7;';

            $barOuter = new TElement('div');
            $barOuter->style = 'height:8px; background:#edf1f5; border-radius:8px; overflow:hidden; margin-bottom:5px;';

            $barInner = new TElement('div');
            $barInner->style = "height:8px; width:{$percentual}%; background:{$cor};";
            $barOuter->add($barInner);

            $usageText = new TElement('span');
            $usageText->style = "font-size:12px; font-weight:bold; color:{$cor};";
            $usageText->add(number_format($percentual, 1, ',', '.') . '%');

            $usage->add($barOuter);
            $usage->add($usageText);
            $tr->add($usage);
            $tbody->add($tr);
        }

        $table->add($tbody);
        $wrapper->add($table);
        $panel->add($wrapper);

        return $panel;
    }

    private function buildResumoDepartamentosHeader(array $linhas)
    {
        $totais = [
            'saldo_empenho' => 0,
            'total_pedidos' => 0,
            'saldo_atual' => 0,
            'qtde_pedidos' => 0,
        ];

        foreach ($linhas as $linha)
        {
            $totais['saldo_empenho'] += (float) $linha['saldo_empenho'];
            $totais['total_pedidos'] += (float) $linha['total_pedidos'];
            $totais['saldo_atual'] += (float) $linha['saldo_atual'];
            $totais['qtde_pedidos'] += (int) $linha['qtde_pedidos'];
        }

        $header = new TElement('div');
        $header->style = 'display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:10px; padding:12px; background:#fbfcfe;';

        $header->add($this->buildResumoKpi('Total empenhado', $this->formatCurrency($totais['saldo_empenho']), '#315a8a'));
        $header->add($this->buildResumoKpi('Total consumido', $this->formatCurrency($totais['total_pedidos']), '#9b5c12'));
        $header->add($this->buildResumoKpi('Saldo atual', $this->formatCurrency($totais['saldo_atual']), $totais['saldo_atual'] < 0 ? '#c0392b' : '#237a3b'));
        $header->add($this->buildResumoKpi('Total de pedidos', (string) $totais['qtde_pedidos'], '#3f4d67'));

        return $header;
    }

    private function buildResumoKpi(string $label, string $value, string $color)
    {
        $box = new TElement('div');
        $box->style = 'background:#fff; border:1px solid #e4ebf3; border-radius:6px; padding:10px 12px; min-height:64px;';

        $labelElement = new TElement('div');
        $labelElement->style = 'font-size:11px; text-transform:uppercase; letter-spacing:0; color:#6b7890; font-weight:700; margin-bottom:5px;';
        $labelElement->add($label);

        $valueElement = new TElement('div');
        $valueElement->style = "font-size:16px; font-weight:800; color:{$color};";
        $valueElement->add($value);

        $box->add($labelElement);
        $box->add($valueElement);

        return $box;
    }


    private function buildDivergenciasDotacaoPanel(array $departamentosPermitidosIds)
    {
       
        $panel = new TPanelGroup("<span style='font-weight: bold;'>Pedidos consumidos com diferença na dotação orçamentária</span>");
        $panel->getBody()->style = 'padding:0;';

        $linhas = $this->getPedidosDivergenciaDotacao($departamentosPermitidosIds);
        $totalPedidos = count($linhas);
        $totalValorPedido = 0;
        $totalValorDotado = 0;
        $totalDiferenca = 0;

        foreach ($linhas as $linha)
        {
            $totalValorPedido += (float) $linha['valor_pedido'];
            $totalValorDotado += (float) $linha['valor_dotado'];
            $totalDiferenca += (float) $linha['diferenca'];
        }

        // $resumo = new TElement('div');
        // $resumo->style = 'display:flex; gap:12px; flex-wrap:wrap; padding:12px; border-bottom:1px solid #e6edf5; background:#fff;';
$resumo = new TElement('div');
$resumo->style = '
    display: flex;
    flex-direction: row;
    gap: 10px;
    width: 100%;
    padding: 10px;
    box-sizing: border-box;
    background: #fff;
';

        // $resumo->add($this->buildResumoBox('Pedidos com diferença', $totalPedidos, '#315a8a'));
        // $resumo->add($this->buildResumoBox('Total pedido', 'R$ ' . number_format($totalValorPedido, 2, ',', '.'), '#237a3b'));
        // $resumo->add($this->buildResumoBox('Total dotado', 'R$ ' . number_format($totalValorDotado, 2, ',', '.'), '#315a8a'));
        // $resumo->add($this->buildResumoBox('Diferença total', 'R$ ' . number_format($totalDiferenca, 2, ',', '.'), $totalDiferenca < 0 ? '#dc3545' : '#237a3b'));

$box1 = $this->buildResumoBox('Pedidos com diferença', $totalPedidos, '#315a8a');
$box1->style .= '; margin-right:10px; width:25%;';

$box2 = $this->buildResumoBox(
    'Total pedido',
    'R$ ' . number_format($totalValorPedido, 2, ',', '.'),
    '#237a3b'
);
$box2->style .= '; margin-right:10px; width:25%;';

$box3 = $this->buildResumoBox(
    'Total dotado',
    'R$ ' . number_format($totalValorDotado, 2, ',', '.'),
    '#315a8a'
);
$box3->style .= '; margin-right:10px; width:25%;';

$box4 = $this->buildResumoBox(
    'Diferença total',
    'R$ ' . number_format($totalDiferenca, 2, ',', '.'),
    $totalDiferenca < 0 ? '#dc3545' : '#237a3b'
);
$box4->style .= '; width:25%;';

$resumo->add($box2);
$resumo->add($box3);
$resumo->add($box4);
$resumo->add($box1);

        $panel->add($resumo);

        $wrapper = new TElement('div');
        $wrapper->style = 'width:100%; max-height:340px; overflow:auto;';

        $table = new TElement('table');
        $table->class = 'table table-hover';
        $table->style = 'margin:0; min-width:1060px; border-collapse:separate; border-spacing:0;';

        $thead = new TElement('thead');
        $headRow = new TElement('tr');
        foreach (['Departamento', 'Pedido', 'Estado', 'Data', 'Fornecedor', 'Valor pedido', 'Valor dotado', 'Diferença', 'Situação'] as $label)
        {
            $th = new TElement('th');
            $th->style = 'font-size:11px; text-transform:uppercase; letter-spacing:0; color:#3f4d67; border-bottom:1px solid #d9e2ef; white-space:nowrap; position:sticky; top:0; z-index:2; background:#f7f9fc; padding:10px 12px;';
            $th->add($label);
            $headRow->add($th);
        }
        $thead->add($headRow);
        $table->add($thead);

        $tbody = new TElement('tbody');

        if (empty($linhas))
        {
            $tr = new TElement('tr');
            $td = new TElement('td');
            $td->colspan = 9;
            $td->style = 'text-align:center; color:#237a3b; padding:18px; font-weight:700;';
            $td->add('Nenhum pedido consumido com diferença de dotação encontrado.');
            $tr->add($td);
            $tbody->add($tr);
        }

        foreach ($linhas as $linha)
        {
            $tr = new TElement('tr');
            $cells = [
                htmlspecialchars($linha['departamento'], ENT_QUOTES, 'UTF-8'),
                (string) $linha['pedido_id'],
                $this->renderEstadoPedidoDotacao($linha),
                htmlspecialchars($linha['data_pedido'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($linha['fornecedor'], ENT_QUOTES, 'UTF-8'),
                $this->renderValorFinanceiro($linha['valor_pedido']),
                $this->renderValorFinanceiro($linha['valor_dotado']),
                $this->renderValorFinanceiro($linha['diferenca'], true),
                $this->renderSituacaoDivergenciaDotacao($linha),
            ];

            foreach ($cells as $index => $value)
            {
                $td = new TElement('td');
                if ($index === 0)
                {
                    $td->style = 'vertical-align:middle; min-width:280px; font-weight:600; color:#3f4d67; padding:10px 12px; border-top:1px solid #edf1f7;';
                }
                elseif (in_array($index, [5, 6, 7]))
                {
                    $td->style = 'vertical-align:middle; white-space:nowrap; text-align:right; padding:10px 12px; border-top:1px solid #edf1f7;';
                }
                else
                {
                    $td->style = 'vertical-align:middle; white-space:nowrap; padding:10px 12px; border-top:1px solid #edf1f7;';
                }
                $td->add($value);
                $tr->add($td);
            }

            $tbody->add($tr);
        }

        $table->add($tbody);
        $wrapper->add($table);
        $panel->add($wrapper);

        return $panel;
    }

    private function usuarioPodeVerDivergenciasDotacao(): bool
    {
        $login = (string) TSession::getValue('login');
        $email = '';

        try
        {
            $userId = (int) (TSession::getValue('iduser') ?: TSession::getValue('userid'));

            if ($userId > 0)
            {
                TTransaction::open('permission');
                $usuario = new SystemUsers($userId);
                $login = $login ?: (string) ($usuario->login ?? '');
                $email = (string) ($usuario->email ?? '');
                TTransaction::close();
            }
        }
        catch (Exception $e)
        {
            if (TTransaction::get())
            {
                TTransaction::rollback();
            }
        }

        $identificacao = mb_strtolower($login . ' ' . $email, 'UTF-8');

        return strpos($identificacao, 'xp3') !== false || strpos($identificacao, 'np3') !== false;
    }

    private function getPedidosDivergenciaDotacao(array $departamentosPermitidosIds): array
    {
        TTransaction::open('minierp');
        $idunit = (int) TSession::getValue('idunit');

        $statusConsumidos = [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PGTOAPROVADO, EstadoPedidoFrotas::ENTREGUE, EstadoPedidoFrotas::FINALIZADO, EstadoPedidoFrotas::PREAPROVADO];
        $pedidos = PedidoFrotas::where('estado_pedido_frotas_id', 'in', $statusConsumidos)
            ->where('system_unit_id', '=', $idunit)
            ->where('departamento_unit_id', 'in', $departamentosPermitidosIds)
            ->where('deleted_at', 'is', NULL)
            ->orderBy('departamento_unit_id')
            ->orderBy('id', 'desc')
            ->load();

        $linhas = [];
        if ($pedidos)
        {
            foreach ($pedidos as $pedido)
            {
                $dotacoes = DotacaoPedidoFrotas::where('pedido_frotas_id', '=', $pedido->id)
                    ->where('deleted_at', 'is', NULL)
                    ->load();

                $valorDotado = 0;
                $qtdeDotacoes = 0;
                if ($dotacoes)
                {
                    $qtdeDotacoes = count($dotacoes);
                    foreach ($dotacoes as $dotacao)
                    {
                        $valorDotado += (float) $dotacao->valor;
                    }
                }

                $valorPedido = (float) $pedido->valor_liquido_proposta;
                $diferenca = round($valorPedido - $valorDotado, 2);

                if (abs($diferenca) < 0.01)
                {
                    continue;
                }

                $dataPedido = '';
                if (!empty($pedido->dt_pedido))
                {
                    try
                    {
                        $dataPedido = (new DateTime($pedido->dt_pedido))->format('d/m/Y');
                    }
                    catch (Exception $e)
                    {
                        $dataPedido = $pedido->dt_pedido;
                    }
                }

                $linhas[] = [
                    'departamento' => $pedido->departamento_unit->name ?? '',
                    'pedido_id' => $pedido->id,
                    'estado_pedido_frotas_id' => (int) $pedido->estado_pedido_frotas_id,
                    'estado_pedido_nome' => $pedido->estado_pedido_frotas->nome ?? '',
                    'estado_pedido_cor' => $pedido->estado_pedido_frotas->cor ?? '#777777',
                    'data_pedido' => $dataPedido,
                    'fornecedor' => $pedido->estabelecimento->nome ?? '',
                    'valor_pedido' => $valorPedido,
                    'valor_dotado' => $valorDotado,
                    'diferenca' => $diferenca,
                    'qtde_dotacoes' => $qtdeDotacoes,
                ];
            }
        }

        TTransaction::close();

        usort($linhas, function ($a, $b) {
            $dep = strnatcasecmp($a['departamento'] ?? '', $b['departamento'] ?? '');
            if ($dep !== 0)
            {
                return $dep;
            }

            return abs($b['diferenca']) <=> abs($a['diferenca']);
        });

        return $linhas;
    }

    private function getResumoDotacoesDepartamento(array $departamentosPermitidosIds): array
    {
        TTransaction::open('minierp');
        $idunit = (int) TSession::getValue('idunit');

        $saldos = SaldoDepartamento::where('departamento_unit_id', 'in', $departamentosPermitidosIds)
            ->where('departamento_unit_id', 'in', "(SELECT id FROM departamento_unit WHERE system_unit_id = {$idunit})")
            ->where('status_saldo_departamento_id', '<>', StatusSaldoDepartamento::ANULADO)
            ->orderBy('departamento_unit_id')
            ->orderBy('id', 'desc')
            ->load();

        $resumo = [];
        if ($saldos)
        {
            foreach ($saldos as $saldo)
            {
                $totalPedidos = 0;
                $qtdePedidos = 0;
                $dotacoes = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $saldo->id)
                    ->where('pedido_frotas_id', 'in', "(SELECT id FROM pedido_frotas WHERE system_unit_id = {$idunit} AND estado_pedido_frotas_id = " . EstadoPedidoFrotas::FINALIZADO . " AND deleted_at IS NULL)")
                    ->load();

                if ($dotacoes)
                {
                    $pedidoIds = [];
                    foreach ($dotacoes as $dotacao)
                    {
                        $pedidoIds[$dotacao->pedido_frotas_id] = true;
                        $totalPedidos += (float) $dotacao->valor;
                    }
                    $qtdePedidos = count($pedidoIds);
                }

                $tipo = $this->normalizeTipoDotacao($saldo);
                $valor = $tipo === 'P' ? (float) $saldo->saldo_produto : (float) $saldo->saldo_servico;

                if ($valor == 0 && (float) $saldo->saldo_total > 0)
                {
                    $valor = (float) $saldo->saldo_total;
                }

                if ($saldo->tipotransacao === SaldoDepartamento::DEBITO)
                {
                    $valor *= -1;
                }

                $departamentoId = (int) $saldo->departamento_unit_id;
                $key = $departamentoId . '-' . $tipo;

                if (!isset($resumo[$key]))
                {
                    $resumo[$key] = [
                        'departamento' => $saldo->departamento_unit->name ?? '',
                        'tipo' => $tipo,
                        'saldo_empenho' => 0,
                        'total_pedidos' => 0,
                        'saldo_atual' => 0,
                        'qtde_pedidos' => 0,
                        'qtde_empenhos' => 0,
                    ];
                }

                $resumo[$key]['qtde_empenhos']++;
                $resumo[$key]['saldo_empenho'] += $valor;
                $resumo[$key]['total_pedidos'] += $totalPedidos;
                $resumo[$key]['qtde_pedidos'] += $qtdePedidos;
            }
        }

        TTransaction::close();

        foreach ($resumo as &$linha)
        {
            $linha['saldo_atual'] = $linha['saldo_empenho'] - $linha['total_pedidos'];
        }
        unset($linha);

        $resumo = array_values($resumo);

        usort($resumo, function ($a, $b) {
            $departamentoA = mb_strtoupper($a['departamento'] ?? '', 'UTF-8');
            $departamentoB = mb_strtoupper($b['departamento'] ?? '', 'UTF-8');

            if ($departamentoA === $departamentoB)
            {
                return strnatcasecmp($a['tipo'] ?? '', $b['tipo'] ?? '');
            }

            return strnatcasecmp($departamentoA, $departamentoB);
        });

        return $resumo;
    }

    private function renderTipoDotacao($tipo): string
    {
        $tipo = $this->normalizeTipoValue($tipo);

        if ($tipo === 'P')
        {
            return "<span style='background:#e8f6ee; color:#227442; border:1px solid #bfe6ce; padding:3px 8px; border-radius:999px; font-weight:bold; font-size:12px;'>Produto</span>";
        }

        if ($tipo === 'S')
        {
            return "<span style='background:#fdeceb; color:#b4332a; border:1px solid #f5c6c2; padding:3px 8px; border-radius:999px; font-weight:bold; font-size:12px;'>Serviço</span>";
        }

        return '-';
    }

    private function normalizeTipoDotacao(SaldoDepartamento $saldo): string
    {
        $tipo = $this->normalizeTipoValue($saldo->tipo ?? null);

        if ($tipo)
        {
            return $tipo;
        }

        return (float) $saldo->saldo_servico > 0 ? 'S' : 'P';
    }

    private function normalizeTipoValue($tipo): string
    {
        if ($tipo === 'P' || (int) $tipo === SaldoDepartamento::PRODUTO)
        {
            return 'P';
        }

        if ($tipo === 'S' || (int) $tipo === SaldoDepartamento::SERVICO)
        {
            return 'S';
        }

        return '';
    }

    private function renderQtdeEmpenhos($qtde): string
    {
        return "<span style='font-weight:bold; color:#3f4d67;'>" . (int) $qtde . "</span>";
    }

    private function renderQtdePedidos($qtde): string
    {
        $qtde = (int) $qtde;
        if ($qtde > 0)
        {
            return "<span style='font-weight:bold; color:#f44336;'>{$qtde}</span>";
        }

        return '0';
    }

    private function renderValorFinanceiro($value, bool $destacarSaldo = false): string
    {
        $value = (float) $value;
        $color = '#52627a';

        if ($destacarSaldo)
        {
            $color = $value < 0 ? '#c0392b' : '#237a3b';
        }

        return "<span style='font-weight:700; color:{$color};'>" . $this->formatCurrency($value) . "</span>";
    }

    private function renderEstadoPedidoDotacao(array $linha): string
    {
        $nome = trim((string) ($linha['estado_pedido_nome'] ?? ''));
        $cor = $this->normalizeDashboardColor($linha['estado_pedido_cor'] ?? '#777777');
        $texto = $this->getDashboardContrastColor($cor);

        if ($nome === '')
        {
            $nome = 'Estado ' . (int) ($linha['estado_pedido_frotas_id'] ?? 0);
        }

        $nome = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');

        return "<span style='display:inline-flex; align-items:center; justify-content:center; min-width:112px; max-width:180px; padding:4px 10px; border-radius:999px; background:{$cor}; color:{$texto}; font-weight:800; font-size:11px; line-height:1.2; box-shadow:inset 0 -1px 0 rgba(0,0,0,.12);'>{$nome}</span>";
    }

    private function renderSituacaoDivergenciaDotacao(array $linha): string
    {
        if ((int) $linha['qtde_dotacoes'] === 0)
        {
            return "<span style='background:#fdeceb; color:#b4332a; border:1px solid #f5c6c2; padding:3px 8px; border-radius:999px; font-weight:bold; font-size:12px;'>Sem dotação</span>";
        }

        if ((float) $linha['diferenca'] > 0)
        {
            return "<span style='background:#fff6e5; color:#9b5c12; border:1px solid #f3d49b; padding:3px 8px; border-radius:999px; font-weight:bold; font-size:12px;'>Dotação menor</span>";
        }

        return "<span style='background:#eef3ff; color:#315a8a; border:1px solid #cbd8f2; padding:3px 8px; border-radius:999px; font-weight:bold; font-size:12px;'>Dotação maior</span>";
    }

    private function calcularPercentualUso(array $linha): float
    {
        $saldoEmpenho = (float) ($linha['saldo_empenho'] ?? 0);

        if ($saldoEmpenho <= 0)
        {
            return 0;
        }

        return min(100, round(((float) ($linha['total_pedidos'] ?? 0) / $saldoEmpenho) * 100, 1));
    }

    private function formatCurrency($value): string
    {
        return 'R$ ' . number_format((float) $value, 2, ',', '.');
    }

    private function normalizeDashboardColor($color): string
    {
        $color = trim((string) $color);

        if ($color === '')
        {
            return '#777777';
        }

        if (preg_match('/^[0-9a-fA-F]{6}$/', $color))
        {
            return '#' . $color;
        }

        if (preg_match('/^#[0-9a-fA-F]{6}$/', $color))
        {
            return $color;
        }

        return '#777777';
    }

    private function getDashboardContrastColor(string $backgroundColor): string
    {
        if (!preg_match('/^#([0-9a-fA-F]{6})$/', $backgroundColor, $matches))
        {
            return '#ffffff';
        }

        $hex = $matches[1];
        $red = hexdec(substr($hex, 0, 2));
        $green = hexdec(substr($hex, 2, 2));
        $blue = hexdec(substr($hex, 4, 2));
        $luminance = (($red * 299) + ($green * 587) + ($blue * 114)) / 1000;

        return $luminance >= 160 ? '#1f2937' : '#ffffff';
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

private function buildResumoBox($label, $value, $color)
{
    $box = new TElement('div');
    $box->style = "
        flex: 1 1 0;
        min-width: 0;
        background: #ffffff;
        border: 1px solid #dfe6ee;
        border-radius: 6px;
        padding: 10px 14px;
        min-height: 53px;
        box-sizing: border-box;
        overflow: hidden;
    ";

    $labelElement = new TElement('div');
    $labelElement->style = '
        font-size: 10px;
        text-transform: uppercase;
        color: #315a8a;
        font-weight: 700;
        margin-bottom: 8px;
        white-space: nowrap;
    ';
    $labelElement->add($label);

    $valueElement = new TElement('div');
    $valueElement->style = "
        font-size: 15px;
        font-weight: 800;
        color: {$color};
        white-space: nowrap;
    ";
    $valueElement->add($value);

    $box->add($labelElement);
    $box->add($valueElement);

    return $box;
}

}

