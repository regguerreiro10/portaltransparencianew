<?php

class DashboardVeiculos extends TPage
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
        $basename   = urlencode('dashboard-veiculos.pdf');
        $download   = "download.php?file=app/manual/dashboard-veiculos.pdf&basename={$basename}";

        $manual = "
            <span style='float:right;'>
                <a href='{$download}'
                target='_blank'
                style='text-decoration:none;margin-left:10px;'>
                    <i class='fa fa-question-circle'> </i>
                </a>
            </span>
        "; 
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Dashboard - Veiculos, Aeronaves e/ou Equipamentos {$manual}");

        $criteria_veiculos_ativo = new TCriteria();
        $criteria_veiculos_inativo = new TCriteria();
        $criteria_veiculos_cedido = new TCriteria();
        $criteria_veiculos_devolvido = new TCriteria();
        $criteria_veiculos_sinistro = new TCriteria();
        $criteria_veiculos_leilao = new TCriteria();
        $criteria_saldo_veiculo = new TCriteria();
        $criteria_movimento_veiculo = new TCriteria();
        $criteria_saldo_atual_veiculo = new TCriteria();
                $criteria_dbmanutencao = new TCriteria();


        $filterVar = StatusVeiculo::ATIVO;
        $criteria_veiculos_ativo->add(new TFilter('veiculos.status_veiculo_id', '=', $filterVar)); 
        $criteria_veiculos_ativo->add(new TFilter('veiculos.system_unit_id', '=', TSession::getValue('idunit'))); 

        $filterVar = StatusVeiculo::INATIVO;
        $criteria_veiculos_inativo->add(new TFilter('veiculos.status_veiculo_id', '=', $filterVar)); 
        $criteria_veiculos_inativo->add(new TFilter('veiculos.system_unit_id', '=', TSession::getValue('idunit'))); 

        $filterVar = StatusVeiculo::CEDIDO;
        $criteria_veiculos_cedido->add(new TFilter('veiculos.status_veiculo_id', '=', $filterVar)); 
        $criteria_veiculos_cedido->add(new TFilter('veiculos.system_unit_id', '=', TSession::getValue('idunit')));

        $filterVar =  StatusVeiculo::DEVOLVIDO;
        $criteria_veiculos_devolvido->add(new TFilter('veiculos.status_veiculo_id', '=', $filterVar)); 
        $criteria_veiculos_devolvido->add(new TFilter('veiculos.system_unit_id', '=', TSession::getValue('idunit')));
        
        $filterVar = StatusVeiculo::SINISTRO;
        $criteria_veiculos_sinistro->add(new TFilter('veiculos.status_veiculo_id', '=', $filterVar)); 
        $criteria_veiculos_sinistro->add(new TFilter('veiculos.system_unit_id', '=', TSession::getValue('idunit')));

        $filterVar = StatusVeiculo::LEILAO;
        $criteria_veiculos_leilao->add(new TFilter('veiculos.status_veiculo_id', '=', $filterVar)); 
        $criteria_veiculos_leilao->add(new TFilter('veiculos.system_unit_id', '=', TSession::getValue('idunit')));
        
        $filterVar = StatusVeiculo::ATIVO;
        $criteria_saldo_veiculo->add(new TFilter('veiculos.status_veiculo_id', '=', $filterVar)); 
        $criteria_saldo_veiculo->add(new TFilter('veiculos.system_unit_id', '=', TSession::getValue('idunit')));

        $filterVar = StatusVeiculo::ATIVO;
        $criteria_movimento_veiculo->add(new TFilter('veiculos.status_veiculo_id', '=', $filterVar)); 
        $criteria_movimento_veiculo->add(new TFilter('veiculos.system_unit_id', '=', TSession::getValue('idunit')));
        
        $filterVar = StatusVeiculo::ATIVO;
        $criteria_saldo_atual_veiculo->add(new TFilter('veiculos.status_veiculo_id', '=', $filterVar)); 
        $criteria_saldo_atual_veiculo->add(new TFilter('veiculos.system_unit_id', '=', TSession::getValue('idunit')));
        

        $mes = new TCombo('mes');
        $ano = new TCombo('ano');

        $button_buscar = new TButton('button_buscar');
        $veiculos_ativo = new BIndicator('veiculos_ativo');
        $veiculos_inativo = new BIndicator('veiculos_inativo');
        $veiculos_cedido = new BIndicator('veiculos_cedido');
        $veiculos_devolvido = new BIndicator('veiculos_devolvido');
        $veiculos_sinistro = new BIndicator('veiculos_sinistro');
        $veiculos_leilao = new BIndicator('veiculos_leilao');
        $saldo_veiculo = new BIndicator('saldo_veiculo');
        $movimento_veiculo = new BIndicator('movimento_veiculo');
        $saldo_atual_veiculo = new BIndicator('saldo_atual_veiculo');
        $dbmanutencao = new BTableChart('dbmanutencao');

        $button_buscar->setAction(new TAction(['DashboardVeiculos', 'onShow']), "Buscar");
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

        $veiculos_ativo->setDatabase('minierp');
        $veiculos_ativo->setFieldValue("veiculos.id");
        $veiculos_ativo->setModel('Veiculos');
        $veiculos_ativo->setTotal('count');
        $veiculos_ativo->setColors('#A8E1A0', '#FFFFFF', '#44BD32', '#FFFFFF');
        $veiculos_ativo->setTitle("ativo", '#ffffff', '20', '');
        $criteria_veiculos_ativo->add(new TFilter('veiculos.deleted_at', 'is', NULL));
        $veiculos_ativo->setCriteria($criteria_veiculos_ativo);
                $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $veiculos_ativo->setIcon(new TImage($icone.' #ffffff'));

        $veiculos_ativo->setValueSize("20");
        $veiculos_ativo->setValueColor("#ffffff", 'B');
        $veiculos_ativo->setSize('100%', 95);
        $veiculos_ativo->setLayout('horizontal', 'left');

        $veiculos_inativo->setDatabase('minierp');
        $veiculos_inativo->setFieldValue("veiculos.id");
        $veiculos_inativo->setModel('Veiculos');
        $veiculos_inativo->setTotal('count');
        $veiculos_inativo->setColors('#E1B1AC', '#FFFFFF', '#C0392B', '#FFFFFF');
        $veiculos_inativo->setTitle("inativo", '#FFFFFF', '20', '');
        $criteria_veiculos_inativo->add(new TFilter('veiculos.deleted_at', 'is', NULL));
        $veiculos_inativo->setCriteria($criteria_veiculos_inativo);
                        $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $veiculos_inativo->setIcon(new TImage($icone.' #ffffff'));
        $veiculos_inativo->setValueSize("20");
        $veiculos_inativo->setValueColor("#FFFFFF", 'B');
        $veiculos_inativo->setSize('100%', 95);
        $veiculos_inativo->setLayout('horizontal', 'left');

        $veiculos_cedido->setDatabase('minierp');
        $veiculos_cedido->setFieldValue("veiculos.id");
        $veiculos_cedido->setModel('Veiculos');
        $veiculos_cedido->setTotal('count');
        $veiculos_cedido->setColors('#949191', '#FFFFFF', '#000000', '#FFFFFF');
        $veiculos_cedido->setTitle("cedido", '#FFFFFF', '20', '');
        $criteria_veiculos_cedido->add(new TFilter('veiculos.deleted_at', 'is', NULL));
        $veiculos_cedido->setCriteria($criteria_veiculos_cedido);
        $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $veiculos_cedido->setIcon(new TImage($icone.' #ffffff'));
        $veiculos_cedido->setValueSize("20");
        $veiculos_cedido->setValueColor("#FFFFFF", 'B');
        $veiculos_cedido->setSize('100%', 95);
        $veiculos_cedido->setLayout('horizontal', 'left');

        $veiculos_devolvido->setDatabase('minierp');
        $veiculos_devolvido->setFieldValue("veiculos.id");
        $veiculos_devolvido->setModel('Veiculos');
        $veiculos_devolvido->setTotal('count');
        $veiculos_devolvido->setColors('#54A0FF', '#FFFFFF', '#3498DB', '#FFFFFF');
        $veiculos_devolvido->setTitle("devolvido", '#FFFFFF', '20', '');
        $criteria_veiculos_devolvido->add(new TFilter('veiculos.deleted_at', 'is', NULL));
        $veiculos_devolvido->setCriteria($criteria_veiculos_devolvido);
            $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $veiculos_devolvido->setIcon(new TImage($icone.' #ffffff'));
        $veiculos_devolvido->setValueSize("20");
        $veiculos_devolvido->setValueColor("#FFFFFF", 'B');
        $veiculos_devolvido->setSize('100%', 95);
        $veiculos_devolvido->setLayout('horizontal', 'left');

        $veiculos_sinistro->setDatabase('minierp');
        $veiculos_sinistro->setFieldValue("veiculos.id");
        $veiculos_sinistro->setModel('Veiculos');
        $veiculos_sinistro->setTotal('count');
        $veiculos_sinistro->setColors('#FF7675', '#FFFFFF', '#E74C3C', '#FFFFFF');
        $veiculos_sinistro->setTitle("Sinistro", '#FFFFFF', '20', '');
        $criteria_veiculos_sinistro->add(new TFilter('veiculos.deleted_at', 'is', NULL));
        $veiculos_sinistro->setCriteria($criteria_veiculos_sinistro);
            $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $veiculos_sinistro->setIcon(new TImage($icone.' #ffffff'));
        $veiculos_sinistro->setValueSize("20");
        $veiculos_sinistro->setValueColor("#FFFFFF", 'B');
        $veiculos_sinistro->setSize('100%', 95);
        $veiculos_sinistro->setLayout('horizontal', 'left');

        $veiculos_leilao->setDatabase('minierp');
        $veiculos_leilao->setFieldValue("veiculos.id");
        $veiculos_leilao->setModel('Veiculos');
        $veiculos_leilao->setTotal('count');
        $veiculos_leilao->setColors('#90D2AC', '#FFFFFF', '#2ECC71', '#FFFFFF');
        $veiculos_leilao->setTitle("Leilao", '#FFFFFF', '20', '');
        $criteria_veiculos_leilao->add(new TFilter('veiculos.deleted_at', 'is', NULL));
        $veiculos_leilao->setCriteria($criteria_veiculos_leilao);
             $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $veiculos_leilao->setIcon(new TImage($icone.' #ffffff'));
        $veiculos_leilao->setValueSize("20");
        $veiculos_leilao->setValueColor("#FFFFFF", 'B');
        $veiculos_leilao->setSize('100%', 95);
        $veiculos_leilao->setLayout('horizontal', 'left');

        $saldo_veiculo->setDatabase('minierp');
        $saldo_veiculo->setFieldValue("veiculos.saldo_veiculo");
        $saldo_veiculo->setModel('Veiculos');
        $saldo_veiculo->setTransformerValue(function($value)
        {
            //code here
            TTransaction::open('minierp');

            $repository = new TRepository('Veiculos'); 
            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('status_veiculo_id', 'IN', array(StatusVeiculo::ATIVO, StatusVeiculo::CEDIDO)));
            $vei = $repository->load($criteria);

            $value=0;
            if ($vei)
            {
                    foreach ($vei as $veiculo)

                    {
                        $sdo = SaldoVeiculo::where('veiculos_id', '=', $veiculo->id)
                                           ::where('system_unit_id', '=', TSession::getValue('idunit'))
                                           ->load();

                        $saldo_credito=0;
                        $saldo_debito=0;
                        $saldo_atual=0;
                        if ($sdo){
                            foreach ($sdo as $saldo) {
                                if ($saldo->tipo_transacao=='C'){
                                    $saldo_credito+=$saldo->valor_transacao;
                                } else {
                                    $saldo_debito+=$saldo->valor_transacao;
                                } 
                            }
                            $saldo_atual = ($saldo_credito-$saldo_debito);
                        }

                         $value += $saldo_atual;
                    }                
            }
            TTransaction::close();

            return "R$ " . number_format($value, 2, ",", ".");

        });

        $saldo_veiculo->setTotal('sum');
        $saldo_veiculo->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1000.000,00";
        });
        $saldo_veiculo->setColors('#0F4626', '#ffffff', '#0C371E', '#ffffff');
        $saldo_veiculo->setTitle("Saldo", '#ffffff', '20', '');
        $saldo_veiculo->setCriteria($criteria_saldo_veiculo);
            $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $saldo_veiculo->setIcon(new TImage($icone.' #ffffff'));
        $saldo_veiculo->setValueSize("20");
        $saldo_veiculo->setValueColor("#ffffff", 'B');
        $saldo_veiculo->setSize('100%', 95);
        $saldo_veiculo->setLayout('horizontal', 'left');

         $saldo_veiculo->setDatabase('minierp');
        $saldo_veiculo->setFieldValue("veiculos.saldo_veiculo");
        $saldo_veiculo->setModel('Veiculos');
        $saldo_veiculo->setTransformerValue(function($value)
        {
            //code here
            TTransaction::open('minierp');

            $repository = new TRepository('Veiculos'); 
            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('status_veiculo_id', 'IN', array(StatusVeiculo::ATIVO, StatusVeiculo::CEDIDO)));
            $vei = $repository->load($criteria);

            $value=0;
            if ($vei)
            {
                    foreach ($vei as $veiculo)

                    {
                        $sdo = SaldoVeiculo::where('veiculos_id', '=', $veiculo->id)
                                           ->load();

                        $saldo_credito=0;
                        $saldo_debito=0;
                        $saldo_atual=0;
                        if ($sdo){
                            foreach ($sdo as $saldo) {
                                if ($saldo->tipo_transacao=='C'){
                                    $saldo_credito+=$saldo->valor_transacao;
                                } else {
                                    $saldo_debito+=$saldo->valor_transacao;
                                } 
                            }
                            $saldo_atual = ($saldo_credito-$saldo_debito);
                        }

                         $value += $saldo_atual;
                    }                
            }
            TTransaction::close();

            return "R$ " . number_format($value, 2, ",", ".");

        });

        $saldo_veiculo->setTotal('sum');
        $saldo_veiculo->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1000.000,00";
        });
        $saldo_veiculo->setColors('#0F4626', '#ffffff', '#0C371E', '#ffffff');
        $saldo_veiculo->setTitle("Saldo", '#ffffff', '20', '');
        $saldo_veiculo->setCriteria($criteria_saldo_veiculo);
                $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $saldo_veiculo->setIcon(new TImage($icone.' #ffffff'));
        $saldo_veiculo->setValueSize("20");
        $saldo_veiculo->setValueColor("#ffffff", 'B');
        $saldo_veiculo->setSize('100%', 95);
        $saldo_veiculo->setLayout('horizontal', 'left');

        /** movimento */

        $movimento_veiculo->setDatabase('minierp');
        $movimento_veiculo->setFieldValue("veiculos.saldo_veiculo");
        $movimento_veiculo->setModel('Veiculos');
        $movimento_veiculo->setTransformerValue(function($value)
        {
            //code here
            TTransaction::open('minierp');

            $repository = new TRepository('Veiculos'); 
            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('status_veiculo_id', 'IN', array(StatusVeiculo::ATIVO, StatusVeiculo::CEDIDO)));
            $vei = $repository->load($criteria);

            $value=0;
            if ($vei)
            {
                    foreach ($vei as $veiculo)

                    {
                        $sdo = SaldoVeiculo::where('veiculos_id', '=', $veiculo->id)
                                           ->load();

                        $saldo_credito=0;
                        $saldo_debito=0;
                        $saldo_atual=0;
                        if ($sdo){
                            foreach ($sdo as $saldo) {
                                if ($saldo->tipo_transacao=='C'){
                                    $saldo_credito+=$saldo->valor_transacao;
                                } else {
                                    $saldo_debito+=$saldo->valor_transacao;
                                } 
                            }
                            $saldo_atual = ($saldo_credito-$saldo_debito);
                        }

                         $value += $saldo_atual;
                    }                
            }
            TTransaction::close();

            return "R$ " . number_format($value, 2, ",", ".");

        });

        $movimento_veiculo->setTotal('sum');
        $movimento_veiculo->setTarget(1000000, '#ffffff', function($percentage, $target){
            return "{$percentage}% de R$ 1000.000,00";
        });
        $movimento_veiculo->setColors('#9852A3', '#ffffff', '#9D27AE', '#ffffff');
        $movimento_veiculo->setTitle("Movimento", '#ffffff', '20', '');
        $movimento_veiculo->setCriteria($criteria_saldo_veiculo);
                $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $movimento_veiculo->setIcon(new TImage($icone.' #ffffff'));
        $movimento_veiculo->setValueSize("20");
        $movimento_veiculo->setValueColor("#ffffff", 'B');
        $movimento_veiculo->setSize('100%', 95);
        $movimento_veiculo->setLayout('horizontal', 'left');

       /**** movimento */

       $saldo_atual_veiculo->setDatabase('minierp');
       $saldo_atual_veiculo->setFieldValue("veiculos.saldo_veiculo");
       $saldo_atual_veiculo->setModel('Veiculos');
       $saldo_atual_veiculo->setTransformerValue(function($value)
       {
           //code here
           TTransaction::open('minierp');

           $repository = new TRepository('Veiculos'); 
           $criteria = new TCriteria;
           $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')), TExpression::AND_OPERATOR);
           $criteria->add(new TFilter('status_veiculo_id', 'IN', array(StatusVeiculo::ATIVO, StatusVeiculo::CEDIDO)));
           $vei = $repository->load($criteria);

           $value=0;
           if ($vei)
           {
                   foreach ($vei as $veiculo)

                   {
                       $sdo = SaldoVeiculo::where('veiculos_id', '=', $veiculo->id)
                                          ->load();

                       $saldo_credito=0;
                       $saldo_debito=0;
                       $saldo_atual=0;
                       if ($sdo){
                           foreach ($sdo as $saldo) {
                               if ($saldo->tipo_transacao=='C'){
                                   $saldo_credito+=$saldo->valor_transacao;
                               } else {
                                   $saldo_debito+=$saldo->valor_transacao;
                               } 
                           }
                           $saldo_atual = ($saldo_credito-$saldo_debito);
                       }

                        $value += $saldo_atual;
                   }                
           }
           TTransaction::close();

           return "R$ " . number_format($value, 2, ",", ".");

       });

       $saldo_atual_veiculo->setTotal('sum');
       $saldo_atual_veiculo->setTarget(1000000, '#ffffff', function($percentage, $target){
           return "{$percentage}% de R$ 1000.000,00";
       });
       $saldo_atual_veiculo->setColors('#A1C07D', '#ffffff', '#8BC34A', '#ffffff');
       $saldo_atual_veiculo->setTitle("SALDO ATUAL", '#ffffff', '20', '');
       $saldo_atual_veiculo->setCriteria($criteria_saldo_atual_veiculo);
                $icone = $this->getIconeFrota(TSession::getValue('tipofrota'));
        $saldo_atual_veiculo->setIcon(new TImage($icone.' #ffffff'));
       $saldo_atual_veiculo->setValueSize("20");
       $saldo_atual_veiculo->setValueColor("#ffffff", 'B');
       $saldo_atual_veiculo->setSize('100%', 95);
       $saldo_atual_veiculo->setLayout('horizontal', 'left');

         //Garantia ha vencer:

    $dbmanutencao_column_id                     = new BTableColumnChart('pedido_frotas_id', "ID", 'left', '4%');
    $dbmanutencao_column_proposta_id           = new BTableColumnChart('propostas_id', "ID Propostas", 'left', '6%');
    $dbmanutencao_column_placa                 = new BTableColumnChart('placa', "Placa", 'left', '6%');
    $dbmanutencao_column_marca                 = new BTableColumnChart('marca', "Marca", 'left', '6%');
    $dbmanutencao_column_modelo                = new BTableColumnChart('modelo', "Modelo", 'left', '6%');
    $dbmanutencao_column_anof                  = new BTableColumnChart('anof', "Ano", 'left', '3%');
    $dbmanutencao_column_fornecedor            = new BTableColumnChart('fornecedor', "Estabelecimento", 'left', '10%');
    $dbmanutencao_column_totalcd               = new BTableColumnChart('total_geral_com_desconto', "Total Itens (P/S)", 'right', '9%');
    $dbmanutencao_column_cidade                = new BTableColumnChart('nome_cidade', "Cidade", 'left', '9%');
    $dbmanutencao_column_estado                = new BTableColumnChart('sigla_estado', "UF", 'center', '2%');
    $dbmanutencao_column_saldo                 = new BTableColumnChart('saldo_atual', "Saldo Atual", 'right', '8%');
    $dbmanutencao_column_kmanterior            = new BTableColumnChart('km_anterior', "Km Anterior", 'right', '4%');
    $dbmanutencao_column_kmrodado              = new BTableColumnChart('km_rodado', "Km Rodado", 'right', '4%');
    $dbmanutencao_column_custo_por_km          = new BTableColumnChart('custo_por_km', "Custo por Km", 'right', '5%');
    $dbmanutencao_column_custo_medio_m         = new BTableColumnChart('custo_medio_mensal', "Custo Médio Mês", 'right', '7%');
    $dbmanutencao_column_qtd_pedido_mes        = new BTableColumnChart('qtd_pedidos_mes', "Qtd", 'right', '3%');
    $dbmanutencao_column_total_mensal_manutencao = new BTableColumnChart('total_mensal_manutencao', "Total Mês", 'right', '7%');

    $dbmanutencao_column_saldo->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
         $dbmanutencao_column_total_mensal_manutencao->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
         $dbmanutencao_column_totalcd->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $dbmanutencao_column_custo_medio_m->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $dbmanutencao_column_custo_por_km->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $mesSelecionado = $mes->getValue() ?? date('m');
        $anoSelecionado = $ano->getValue() ?? date('y');

        $dataFiltro = "{$anoSelecionado}-{$mesSelecionado}-01";

        // $criteria_dbmanutencao->setProperty('limitade');
        // $criteria_dbmanutencao->add(new TFilter('veiculos.updated_at', 'is', NULL));
         $criteria_dbmanutencao->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
      //  $criteria_dbmanutencao->add(new TFilter('manutencao_garantia.ativo', '=', 'S'));
        $criteria_dbmanutencao->add(new TFilter('mes', '=', $mesSelecionado));
        $criteria_dbmanutencao->add(new TFilter('ano', '=', $anoSelecionado));
$criteria_dbmanutencao->setProperty('order', 'placa'); // <- aqui define a ordenação


        $dbmanutencao->setDatabase('minierp');
        $dbmanutencao->setModel('ViewConsumosrealizados');
        $dbmanutencao->setTitle("Consumos realizados");
        $dbmanutencao->setSize('100%', 350);
        $dbmanutencao->setColumns([ $dbmanutencao_column_id, $dbmanutencao_column_proposta_id, $dbmanutencao_column_placa, $dbmanutencao_column_marca,
 $dbmanutencao_column_modelo, $dbmanutencao_column_anof, $dbmanutencao_column_fornecedor, $dbmanutencao_column_totalcd,
 $dbmanutencao_column_cidade, $dbmanutencao_column_estado, $dbmanutencao_column_saldo,  $dbmanutencao_column_kmanterior,
 $dbmanutencao_column_kmrodado, $dbmanutencao_column_custo_por_km, $dbmanutencao_column_custo_medio_m,$dbmanutencao_column_qtd_pedido_mes, 
 $dbmanutencao_column_total_mensal_manutencao]);
        $dbmanutencao->setCriteria($criteria_dbmanutencao);
        // $dbmanutencao->setJoins([
        //      'veiculos' => ['manutencao_garantia.veiculos_id', 'veiculos.id'],
        //      'marca' => ['veiculos.marca_id', 'marca.id'],
        //      'modelo' => ['veiculos.modelo_id', 'modelo.id'],
        //      'produto' => ['manutencao_garantia.produto_id', 'produto.id'],
        //      'departamento_unit' => ['veiculos.departamento_unit_id', 'departamento_unit.id']
        // ]);

        $dbmanutencao->setRowColorOdd('#F9F9F9');
        $dbmanutencao->setRowColorEven('#FFFFFF');
        $dbmanutencao->setFontRowColorOdd('#333333');
        $dbmanutencao->setFontRowColorEven('#333333');
        $dbmanutencao->setBorderColor('#A03939');
        $dbmanutencao->setTableHeaderColor('#FFFFFF');
        $dbmanutencao->setTableHeaderFontColor('#333333');
        $dbmanutencao->setTableFooterColor('#F28181');
        $dbmanutencao->setTableFooterFontColor('#333333');

        $row1 = $this->form->addFields([new TLabel("Mês:", null, '14px', null, '100%'),$mes],[new TLabel("Ano:", null, '14px', null),$ano],[new TLabel(" ", null, '14px', null, '100%'),$button_buscar]);
        $row1->layout = [' col-sm-2',' col-sm-2','col-sm-2'];

        $row2 = $this->form->addFields([$veiculos_ativo],[$veiculos_inativo],[$veiculos_cedido]);
        $row2->layout = ['col-sm-4','col-sm-4',' col-sm-4'];

        $row3 = $this->form->addFields([$veiculos_devolvido],[$veiculos_sinistro],[$veiculos_leilao]);
        $row3->layout = ['col-sm-4','col-sm-4',' col-sm-4'];

        $row4 = $this->form->addFields([$saldo_veiculo],[$movimento_veiculo],[$saldo_atual_veiculo]);
        $row4->layout = ['col-sm-4','col-sm-4',' col-sm-4'];

        
        // Adiciona ao formulário
        $row_garantias = $this->form->addFields([$dbmanutencao]);
        $row_garantias->layout = [' col-sm-12'];
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


        
        BChart::generate($veiculos_ativo, $veiculos_inativo, $veiculos_cedido, $veiculos_devolvido, $veiculos_sinistro, $veiculos_leilao, $saldo_veiculo);

        // create the form actions

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            // $container->add(TBreadCrumb::create(["Pedido","Dashboard"]));
        }
        $container->add($this->form);

        parent::add($container);

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

