<?php

class DashBoard extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        try
        {
            $html = new THtmlRenderer('app/resources/admin_dashboard.html');
            
            TTransaction::open('permission');
            $indicator1 = new THtmlRenderer('app/resources/info-box.html');
            $indicator2 = new THtmlRenderer('app/resources/info-box.html');
            $indicator3 = new THtmlRenderer('app/resources/info-box.html');
            $indicator4 = new THtmlRenderer('app/resources/info-box.html');
            $indicator5 = new THtmlRenderer('app/resources/info-box.html');
            $indicator6 = new THtmlRenderer('app/resources/info-box.html');

            $aprovados = TCriteria::create(['situacao_pedido_id' => SituacaoPedido::APROVADO]);
            $pendentes = TCriteria::create(['situacao_pedido_id' => SituacaoPedido::PENDENTE]);
            $aguardando = TCriteria::create(['situacao_pedido_id' => SituacaoPedido::AGUARDANDO]);
            $enviados = TCriteria::create(['situacao_pedido_id' => SituacaoPedido::ENVIADO]);
            $naoenviados = TCriteria::create(['situacao_pedido_id' => SituacaoPedido::NAOENVIADO]);
            $finalizados = TCriteria::create(['situacao_pedido_id' => SituacaoPedido::FINALIZADO]);
            $saldoatual = TCriteria::create(['saldo_atual' => SituacaoPedido::FINALIZADO]);

            $indicator1->enableSection('main', ['title' => 'Pedidos Aprovados',   'icon' => 'check-circle',      'background' => 'green',   'value' => Pedido::count($aprovados)]);
            $indicator2->enableSection('main', ['title' => 'Pedidos Pendentes',    'icon' => 'exclamation-circle',       'background' => 'yellow', 'value' => Pedido::count($pendentes)]);
            $indicator3->enableSection('main', ['title' => 'Pedidos Aguardando Aprovação',    'icon' => 'exclamation-circle', 'background' => 'orange', 'value' => Pedido::count($aguardando)]);
            $indicator4->enableSection('main', ['title' => 'Pedidos Não Enviados', 'icon' => 'exclamation-circle',       'background' => 'red',  'value' => Pedido::count($naoenviados)]);
            $indicator5->enableSection('main', ['title' => 'Pedidos Finalizados', 'icon' => 'check-circle',       'background' => 'black',  'value' => Pedido::count($finalizados)]);
            $indicator6->enableSection('main', ['title' => 'Saldo Atual', 'icon' => 'check-circle',       'background' => 'blue',  'value' => Pedido::count($finalizados)]);
            
            
            // $chart1 = new THtmlRenderer('app/resources/google_bar_chart.html');
            // $data1 = [];
            // $data1[] = [ 'Group', 'Users' ];
            
            // $stats1 = SystemUserGroup::groupBy('system_group_id')->countBy('system_user_id', 'count');
            // if ($stats1)
            // {
            //     foreach ($stats1 as $row)
            //     {
            //         $data1[] = [ SystemGroup::find($row->system_group_id)->name, (int) $row->count];
            //     }
            // }
            
            // replace the main section variables
            // $chart1->enableSection('main', ['data'   => json_encode($data1),
            //                                 'width'  => '100%',
            //                                 'height'  => '500px',
            //                                 'title'  => _t('Users by group'),
            //                                 'ytitle' => _t('Users'), 
            //                                 'xtitle' => _t('Count'),
            //                                 'uniqid' => uniqid()]);
            
            // $chart2 = new THtmlRenderer('app/resources/google_pie_chart.html');
            // $data2 = [];
            // $data2[] = [ 'Unit', 'Users' ];
            
            // $stats2 = SystemUserUnit::groupBy('system_unit_id')->countBy('system_user_id', 'count');
            
            // if ($stats2)
            // {
            //     foreach ($stats2 as $row)
            //     {
            //         $data2[] = [ SystemUnit::find($row->system_unit_id)->name, (int) $row->count];
            //     }
            // }
            // // replace the main section variables
            // $chart2->enableSection('main', ['data'   => json_encode($data2),
            //                                 'width'  => '100%',
            //                                 'height'  => '500px',
            //                                 'title'  => _t('Users by unit'),
            //                                 'ytitle' => _t('Users'), 
            //                                 'xtitle' => _t('Count'),
            //                                 'uniqid' => uniqid()]);
            
            $html->enableSection('main', ['indicator1' => $indicator1,
                                          'indicator2' => $indicator2,
                                          'indicator3' => $indicator3,
                                          'indicator4' => $indicator4,
                                          'indicator5' => $indicator5,
                                          'indicator6' => $indicator6,
                                       
                                          ] );
            
            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($html);
            
            parent::add($container);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            parent::add($e->getMessage());
        }
    }
 }

