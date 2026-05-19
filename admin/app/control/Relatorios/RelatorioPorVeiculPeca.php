<?php
// Importando as classes para o formulário
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Widget\Container\TTable as ContainerTTable;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Wrapper\BootstrapFormBuilder as WrapperBootstrapFormBuilder;

// Definindo a classe
class RelatorioPorVeiculPeca extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private static $formName = 'form_TipoRelatorio';
    private $limit = 20;
    private $arrayDeRelatorios = []; 

    public function __construct()
    {
        parent::__construct();
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->limit = 20;

        $criteria_veiculo = new TCriteria();
                $criteria_veiculo->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));


        $dt_inicial = new BDateRange('dt_pedido', 'dt_pedido_final');
        $veiculo_id = new TDBCombo('veiculo_id', 'minierp', 'Veiculos', 'id', '{placa}','placa asc' , $criteria_veiculo );
        
        $dt_inicial->setMask('dd/mm/yyyy');
        $veiculo_id->enableSearch();

        $dt_inicial->setSize(220);
        $veiculo_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Veiculo:", null, '14px', null, '100%'),$veiculo_id],[new TLabel("Período:", null, '14px', null, '100%'),$dt_inicial]);
        $row1->layout = ['col-sm-6',' col-sm-6'];

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "OS", 'center' , '70px');
        $column_descricao = new TDataGridColumn('descricao', "Descricao", 'left');
        $column_peridoServico = new TDataGridColumn('dt_pedido', "Período", 'ccenter');
        // $column_pagamentoAutorizado = new TDataGridColumn('pagamento_aprovado', 'Pagamento Autorizado','center');
        $column_dataEHoraDeAprovacao = new TDataGridColumn('dt_aprovacao', 'Data Aprovacao', 'center');
        $column_vPecas = new TDataGridColumn('valor_pecas', 'Valor Peças', 'center');
        $column_vServicos = new TDataGridColumn('valor_servicos', 'Valor Serviço', 'center');
        $column_vTotal = new TDataGridColumn('valor_total', 'Valor Total', 'center');
        $column_vTotalDesconto = new TDataGridColumn('valor_total_desconto', 'Valor Total Com Desconto', 'center');

        $column_descricao->setTransformer(function ($value, $object, $row) {
            $estabelecimento_id = $object->estabelecimento_id ?? '';
            $IdProposta = $object->IdProposta ?? '';
            $nome_estabelecimento = $object->nome_estabelecimento ?? '';
            $descricaopedido = $object->descricaopedido ?? '';
            $placa = $object->placa ?? '';

            if(empty($estabelecimento_id) && empty($IdProposta) && empty($nome_estabelecimento) && empty($descricaopedido) && empty( $placa))
            {
                return"<b> {$object->descricao} </b>";
            }

            return "<b>Rede:</b> {$estabelecimento_id} <br>
                    <b>Nº: {$IdProposta} - Cliente: {$nome_estabelecimento} <br>
                    <b>Pedido:</b> {$descricaopedido} <br>
                    <b>Placa</b> {$placa}";
        });

        $column_peridoServico->setTransformer(function ($value, $object, $row){

            $dataFormatadaInicio = isset($object->dt_pedido) ? date("d/m/Y", strtotime($object->dt_pedido)) : "00/00/0000";
            $dataFormatadaFinal = isset($object->dt_finalizacao) ? date("d/m/Y", strtotime($object->dt_finalizacao)) : "00/00/0000";
            
            return "<b>Início:</b> {$dataFormatadaInicio} <br>
                    <b>Final:</b> {$dataFormatadaFinal}" ;
        });

        $column_dataEHoraDeAprovacao->setTransformer(function ($value, $object, $row){

            if (!isset($object->dt_aprovacao) || empty($object->dt_aprovacao)) {
                return "00/00/0000";
            }
        
            return date("d/m/Y", strtotime($object->dt_aprovacao));
        });

        $column_vPecas->setTransformer(function ($value) {
            return is_numeric($value) ? 'R$ ' . number_format((float) $value, 2, ',', '.') : '';
        });
        
        $column_vServicos->setTransformer(function ($value) {
            return is_numeric($value) ? 'R$ ' . number_format((float) $value, 2, ',', '.') : '';
        });

        $column_vTotal->setTransformer(function ($object) {
            if (is_object($object) && isset($object->valor_total) && is_numeric($object->valor_total)) {
                return 'R$ ' . number_format((float) $object->valor_total, 2, ',', '.');
            } elseif (is_numeric($object)) {
                // Caso venha direto um número
                return 'R$ ' . number_format((float) $object, 2, ',', '.');
            }
            return '';
        });
        
        $column_vTotalDesconto->setTransformer(function ($object) {
            if (is_object($object) && isset($object->valor_total) && is_numeric($object->valor_total)) {
                return 'R$ ' . number_format((float) $object->valor_total, 2, ',', '.');
            } elseif (is_numeric($object)) {
                return 'R$ ' . number_format((float) $object, 2, ',', '.');
            }
            return '';
        });
        

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_peridoServico);
        // $this->datagrid->addColumn($column_pagamentoAutorizado);
        $this->datagrid->addColumn($column_dataEHoraDeAprovacao);
        $this->datagrid->addColumn($column_vPecas);
        $this->datagrid->addColumn($column_vServicos);
        $this->datagrid->addColumn($column_vTotal);
        $this->datagrid->addColumn($column_vTotalDesconto);

        // $action_onGerarRelatorio = new TDataGridAction(array('PedidoFrotasList', 'onImprimePedido'));
        // $action_onGerarRelatorio->setUseButton(false);
        // $action_onGerarRelatorio->setButtonClass('btn btn-default btn-sm');
        // $action_onGerarRelatorio->setLabel("Gera Os");
        // $action_onGerarRelatorio->setImage('fas:print rgb(45, 22, 218)');
        // $action_onGerarRelatorio->setField('id');

        // $this->datagrid->addAction($action_onGerarRelatorio);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['RelatorioPorVeiculPeca', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['RelatorioPorVeiculPeca', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['RelatorioPorVeiculPeca', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['RelatorioPorVeiculPeca', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

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

        $head_right_actions->add($dropdown_button_exportar);

        $panel->getBody()->insert(0, $headerActions);

        //<onAfterHeaderActionsCreation>

        //</onAfterHeaderActionsCreation>

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Relatórios","Listagem de Relatórios"]));
        }
        $container->add($this->form);
        $container->add($panel);
        //<onAfterPageCreation>

        //</onAfterPageCreation>

        $this->onReload();

        parent::add($container);
    }

    public function onReload($param = NULL)
    {
        try
        {

            TTransaction::open('minierp'); // Abre uma conexão com o banco de dados

            $order = isset($param['order']) ? $param['order'] : 'id'; // padrão: 'id'
            $direction = isset($param['direction']) ? $param['direction'] : 'asc'; // padrão: 'asc'


            $conn = TTransaction::get(); // Obtém a conexão ativa
            $offset = isset($param['offset']) ? (int) $param['offset'] : 0;
            $limit  = $this->limit;

         

            $count_sql = "SELECT COUNT(DISTINCT p.id) AS total
                    FROM pedido_frotas pf 
                    LEFT JOIN propostas p ON pf.id = p.pedido_frotas_id 
                    LEFT JOIN itens_propostas ip ON p.id = ip.propostas_id
                    LEFT JOIN propostas_historico ph ON p.id = ph.propostas_id
                    LEFT JOIN system_users su ON ph.aprovador_frotas_id = su.id
                    LEFT JOIN pessoa pes ON pf.estabelecimento_id = pes.id
                    WHERE data_historico is not null AND pf.estabelecimento_id is not null
                    AND ph.estado_pedido_frotas_id IN (8,13,18)";

            $count_result = $conn->query($count_sql);
            $total = $count_result->fetch(PDO::FETCH_OBJ)->total;
            $idUnidade = (int)TSession::getValue('idunit');

            // Agora: buscar os registros paginados
            $sql = "SELECT DISTINCT p.id as IdProposta, p.desconto_contratual, pf.id, pf.dt_pedido, pf.descricaopedido, 
                        pf.estabelecimento_id, pes.nome AS nome_estabelecimento, pf.veiculos_id, pf.dt_finalizacao, 
                        ph.data_historico, ph.aprovador_frotas_id, su.name, v.placa,
                        ph.estado_pedido_frotas_id, ip.valor_total, ip.valor, ip.tipo, ip.qtde 
                    FROM pedido_frotas pf 
                    LEFT JOIN propostas p ON pf.id = p.pedido_frotas_id 
                    LEFT JOIN itens_propostas ip ON p.id = ip.propostas_id
                    LEFT JOIN propostas_historico ph ON p.id = ph.propostas_id
                    LEFT JOIN system_users su ON ph.aprovador_frotas_id = su.id
                    LEFT JOIN pessoa pes ON pf.estabelecimento_id = pes.id
                    LEFT JOIN veiculos v ON pf.veiculos_id = v.id
                    WHERE data_historico is not null AND pf.estabelecimento_id is not null
                    AND ph.estado_pedido_frotas_id IN (8,13,18) AND pf.system_unit_id = {$idUnidade}
					ORDER BY `IdProposta` DESC
                    LIMIT {$limit} OFFSET {$offset}";

            $result = $conn->prepare($sql);
            $result->execute();
            $rows = $result->fetchAll(PDO::FETCH_OBJ);

            $groupedData = [];

            foreach ($rows as $row)
            {
                $idProposta = $row->IdProposta;
                $estaAprovado = false;

                $sql = "SELECT * FROM pedido_frotas_historico WHERE pedido_frotas_id = {$row->id} AND estado_pedido_frotas_id = 18";

                $result = $conn->prepare($sql);
                $result->execute();
                $pedido = $result->fetch(PDO::FETCH_OBJ);
                $pagamentoAprovado = false;
                if($pedido != null){
                    $pagamentoAprovado = true;
                }

                $sqlAprovacao = "SELECT * FROM pedido_frotas_historico WHERE pedido_frotas_id = {$row->id} AND estado_pedido_frotas_id = 13";
                $resultAprovacao = $conn->prepare($sqlAprovacao);
                $resultAprovacao->execute();
                $pedidoAprovado = $resultAprovacao->fetch(PDO::FETCH_OBJ);

                if($pedidoAprovado != null){
                    $estaAprovado = true;
                }

                if (!isset($groupedData[$idProposta])) {
                    $groupedData[$idProposta] = (object) [
                        'IdProposta' => $idProposta,
                        'id' => $row->id,
                        'dt_pedido' => $row->dt_pedido,
                        'descricaopedido' => $row->descricaopedido,
                        'estabelecimento_id' => $row->estabelecimento_id,
                        'nome_estabelecimento' => $row->nome_estabelecimento,
                        'veiculos_id' => $row->veiculos_id,
                        'dt_finalizacao' => $row->dt_finalizacao,
                        'data_historico' => $row->data_historico,
                        'aprovador' => $row->name,
                        'dt_aprovacao' => $estaAprovado ? $pedidoAprovado->data_operacao : "",
                        'estado_pedido_frotas_id' => $row->estado_pedido_frotas_id,
                        'placa' => $row->placa,
                        'valor_servicos' => 0,
                        'valor_pecas' => 0,
                        'valor_total' => 0,
                        'valor_total_desconto' => 0,
                        'desconto' => $row->desconto_contratual,
                        'pagamento_aprovado' => $pagamentoAprovado  ? "sim" : "não"
                    ];
                }

                if ($row->tipo == 1) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_servicos += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    if($groupedData[$idProposta]->desconto > 0){
                        $groupedData[$idProposta]->valor_total_desconto = ($groupedData[$idProposta]->valor_total * ($groupedData[$idProposta]->desconto / 100)) - $groupedData[$idProposta]->valor_total;
                    }
                } else if ($row->tipo == 2) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_pecas += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    if($groupedData[$idProposta]->desconto > 0){
                        $groupedData[$idProposta]->valor_total_desconto = $groupedData[$idProposta]->valor_total - ($groupedData[$idProposta]->valor_total * ($groupedData[$idProposta]->desconto / 100));
                    }
                }    
                
            }

            $this->datagrid->clear();
            
            $totalPecas = 0;
            $totalServicos = 0;
            $total_geral = 0;
            $total_geral_desconto = 0;

            foreach ($groupedData as $item)
            {
                
                $this->datagrid->addItem($item);

                $totalPecas += (float) $item->valor_pecas;
                $totalServicos += (float) $item->valor_servicos;
                $total_geral += (float) $item->valor_total;
                $total_geral_desconto += (float) $item->valor_total_desconto;
            }

            $this->pageNavigation->setCount($total); // total de registros
            $this->pageNavigation->setProperties($param); // offset, limit, etc
            $this->pageNavigation->setLimit($limit); 

            $footerTotalecas = new stdClass;
            $footerTotalecas->id = '';
            $footerTotalecas->dt_pedido = '';
            $footerTotalecas->pagamento_aprovado = '';
            $footerTotalecas->dt_aprovacao = '';
            $footerTotalecas->valor_pecas = '';
            $footerTotalecas->valor_servicos = '';
            $footerTotalecas->descricao = 'Valor Peças';
            $footerTotalecas->valor_total = $totalPecas;

            $footerTotalServicos = new stdClass;
            $footerTotalServicos->id = '';
            $footerTotalServicos->dt_pedido = '';
            $footerTotalServicos->pagamento_aprovado = '';
            $footerTotalServicos->dt_aprovacao = '';
            $footerTotalServicos->valor_pecas = '';
            $footerTotalServicos->valor_servicos = '';
            $footerTotalServicos->descricao = 'Valor Serviços';
            $footerTotalServicos->valor_total = $totalServicos;

            $footer = new stdClass;
            $footer->id = '';
            $footer->dt_pedido = '';
            $footer->pagamento_aprovado = '';
            $footer->dt_aprovacao = '';
            $footer->valor_pecas = '';
            $footer->valor_servicos = '';
            $footer->descricao = 'Valor Total';
            $footer->valor_total = $total_geral;
            $footer->valor_total_desconto = $total_geral_desconto;

            // Adiciona o footer na datagrid
            $this->datagrid->addItem($footerTotalecas);
            $this->datagrid->addItem($footerTotalServicos);
            $this->datagrid->addItem($footer);

            TTransaction::close(); // Fecha a conexão com o banco de dados
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            TTransaction::rollback(); // Desfaz alterações em caso de erro
            new TMessage('error', $e->getMessage());
        }
    }

    public function pegaObjetsDaTabelaParaexportar($param = NULL)
    {
        try
        {
            TTransaction::open('minierp'); // Abre uma conexão com o banco de dados
            $idUnidade = (int)TSession::getValue('idunit');

            $conn = TTransaction::get(); // Obtém a conexão ativa
            $sql = "SELECT DISTINCT p.id as IdProposta, p.desconto_contratual, pf.id, pf.dt_pedido, pf.descricaopedido, 
                        pf.estabelecimento_id, pes.nome AS nome_estabelecimento, pf.veiculos_id, pf.dt_finalizacao, 
                        ph.data_historico, ph.aprovador_frotas_id, su.name, v.placa,
                        ph.estado_pedido_frotas_id, ip.valor_total, ip.valor, ip.tipo, ip.qtde 
                    FROM pedido_frotas pf 
                    LEFT JOIN propostas p ON pf.id = p.pedido_frotas_id 
                    LEFT JOIN itens_propostas ip ON p.id = ip.propostas_id
                    LEFT JOIN propostas_historico ph ON p.id = ph.propostas_id
                    LEFT JOIN system_users su ON ph.aprovador_frotas_id = su.id
                    LEFT JOIN pessoa pes ON pf.estabelecimento_id = pes.id
                    LEFT JOIN veiculos v ON pf.veiculos_id = v.id
                    WHERE data_historico is not null AND pf.estabelecimento_id is not null
                    AND ph.estado_pedido_frotas_id IN (8,13,18) AND pf.departamento_unit_id = {$idUnidade}
					ORDER BY `IdProposta` DESC";

            $result = $conn->prepare($sql);
            $result->execute();
            $rows = $result->fetchAll(PDO::FETCH_OBJ);

            $groupedData = [];

            foreach ($rows as $row)
            {
                $idProposta = $row->IdProposta;
                $estaAprovado = false;

                    $sql = "SELECT * FROM pedido_frotas_historico WHERE pedido_frotas_id = {$row->id} AND estado_pedido_frotas_id = 18";

                $result = $conn->prepare($sql);
                $result->execute();
                $pedido = $result->fetch(PDO::FETCH_OBJ);
                $pagamentoAprovado = false;
                if($pedido != null){
                    $pagamentoAprovado = true;
                }

                $sqlAprovacao = "SELECT * FROM pedido_frotas_historico WHERE pedido_frotas_id = {$row->id} AND estado_pedido_frotas_id = 13";
                $resultAprovacao = $conn->prepare($sqlAprovacao);
                $resultAprovacao->execute();
                $pedidoAprovado = $resultAprovacao->fetch(PDO::FETCH_OBJ);

                if($pedidoAprovado != null){
                    $estaAprovado = true;
                }

                if (!isset($groupedData[$idProposta])) {
                    $groupedData[$idProposta] = (object) [
                        'IdProposta' => $idProposta,
                        'id' => $row->id,
                        'dt_pedido' => $row->dt_pedido,
                        'descricaopedido' => $row->descricaopedido,
                        'estabelecimento_id' => $row->estabelecimento_id,
                        'nome_estabelecimento' => $row->nome_estabelecimento,
                        'veiculos_id' => $row->veiculos_id,
                        'dt_finalizacao' => $row->dt_finalizacao,
                        'data_historico' => $row->data_historico,
                        'aprovador' => $row->name,
                        'dt_aprovacao' => $estaAprovado ? $pedidoAprovado->data_operacao : "",
                        'estado_pedido_frotas_id' => $row->estado_pedido_frotas_id,
                        'placa' => $row->placa,
                        'valor_servicos' => 0,
                        'valor_pecas' => 0,
                        'valor_total' => 0,
                        'valor_total_desconto' => 0,
                        'desconto' => $row->desconto_contratual,
                        'pagamento_aprovado' => $pagamentoAprovado  ? "sim" : "não"
                    ];
                }

                if ($row->tipo == 1) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_servicos += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    if($groupedData[$idProposta]->desconto > 0){
                        $groupedData[$idProposta]->valor_total_desconto = ($groupedData[$idProposta]->valor_total * ($groupedData[$idProposta]->desconto / 100)) - $groupedData[$idProposta]->valor_total;
                    }
                } else if ($row->tipo == 2) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_pecas += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    if($groupedData[$idProposta]->desconto > 0){
                        $groupedData[$idProposta]->valor_total_desconto = $groupedData[$idProposta]->valor_total - ($groupedData[$idProposta]->valor_total * ($groupedData[$idProposta]->desconto / 100));
                    }
                }  
     
            }

            $this->datagrid->clear();

            $totalPecas = 0;
            $totalServicos = 0;
            $total_geral = 0;
            $total_geral_desconto = 0;

            foreach ($groupedData as $item)
            {
                
                $this->datagrid->addItem($item);

                $totalPecas += (float) $item->valor_pecas;
                $totalServicos += (float) $item->valor_servicos;
                $total_geral += (float) $item->valor_total;
                $total_geral_desconto += (float) $item->valor_total_desconto;
            }
            
            $footerTotalecas = new stdClass;
            $footerTotalecas->id = '';
            $footerTotalecas->dt_pedido = '';
            $footerTotalecas->pagamento_aprovado = '';
            $footerTotalecas->dt_aprovacao = '';
            $footerTotalecas->valor_pecas = '';
            $footerTotalecas->valor_servicos = '';
            $footerTotalecas->descricao = 'Valor Peças';
            $footerTotalecas->valor_total = $totalPecas;

            $footerTotalServicos = new stdClass;
            $footerTotalServicos->id = '';
            $footerTotalServicos->dt_pedido = '';
            $footerTotalServicos->pagamento_aprovado = '';
            $footerTotalServicos->dt_aprovacao = '';
            $footerTotalServicos->valor_pecas = '';
            $footerTotalServicos->valor_servicos = '';
            $footerTotalServicos->descricao = 'Valor Serviços';
            $footerTotalServicos->valor_total = $totalServicos;

            $footer = new stdClass;
            $footer->id = '';
            $footer->dt_pedido = '';
            $footer->pagamento_aprovado = '';
            $footer->dt_aprovacao = '';
            $footer->valor_pecas = '';
            $footer->valor_servicos = '';
            $footer->descricao = 'Valor Total';
            $footer->valor_total = $total_geral;
            $footer->valor_total_desconto = $total_geral_desconto;

            // Adiciona o footer na datagrid
            $this->datagrid->addItem($footerTotalecas);
            $this->datagrid->addItem($footerTotalServicos);
            $this->datagrid->addItem($footer);

            TTransaction::close(); // Fecha a conexão com o banco de dados
            $this->loaded = true;
            return array_values($groupedData);  
        }
        catch (Exception $e)
        {
            TTransaction::rollback(); // Desfaz alterações em caso de erro
            new TMessage('error', $e->getMessage());
        }
    }


    public function onGerarRelatorio($param)
    {
        // Aqui você pode implementar a lógica de exclusão
        new TMessage('info', "Relatório excluído com sucesso!");
        $this->onReload();
    }

    public function onSearch($param)
    {
        
        try
        {
            TTransaction::open('minierp'); // Abre uma conexão com o banco de dados

            $conn = TTransaction::get(); // Obtém a conexão ativa
            $offset = isset($param['offset']) ? (int) $param['offset'] : 0;
            $limit  = $this->limit;

            $idUnidade = (int)TSession::getValue('idunit');

            $count_sql = "SELECT COUNT(DISTINCT p.id) AS total
                    FROM pedido_frotas pf 
                    LEFT JOIN propostas p ON pf.id = p.pedido_frotas_id 
                    LEFT JOIN itens_propostas ip ON p.id = ip.propostas_id
                    LEFT JOIN propostas_historico ph ON p.id = ph.propostas_id
                    LEFT JOIN system_users su ON ph.aprovador_frotas_id = su.id
                    LEFT JOIN pessoa pes ON pf.estabelecimento_id = pes.id
                    WHERE data_historico is not null AND pf.estabelecimento_id is not null
                    AND ph.estado_pedido_frotas_id IN (8,13,18)";

            $count_result = $conn->query($count_sql);
            $total = $count_result->fetch(PDO::FETCH_OBJ)->total;

            $data = $this->form->getData();
            $idVeiculo = (int)$data->veiculo_id;
            $conn = TTransaction::get(); // Obtém a conexão ativa
            $sql = "SELECT DISTINCT p.id as IdProposta, p.desconto_contratual, pf.id, pf.dt_pedido, pf.descricaopedido, 
                        pf.estabelecimento_id, pes.nome AS nome_estabelecimento, pf.veiculos_id, pf.dt_finalizacao, 
                        ph.data_historico, ph.aprovador_frotas_id, su.name, v.placa,
                        ph.estado_pedido_frotas_id, ip.valor_total, ip.valor, ip.tipo, ip.qtde 
                    FROM pedido_frotas pf 
                    LEFT JOIN propostas p ON pf.id = p.pedido_frotas_id 
                    LEFT JOIN itens_propostas ip ON p.id = ip.propostas_id
                    LEFT JOIN propostas_historico ph ON p.id = ph.propostas_id
                    LEFT JOIN system_users su ON ph.aprovador_frotas_id = su.id
                    LEFT JOIN pessoa pes ON pf.estabelecimento_id = pes.id
                    LEFT JOIN veiculos v ON pf.veiculos_id = v.id
                    WHERE data_historico is not null AND pf.estabelecimento_id is not null
                    AND ph.estado_pedido_frotas_id IN (8,13,18) AND pf.system_unit_id = {$idUnidade}";

            if (isset($idVeiculo) AND $idVeiculo != 0 AND ( (is_scalar($idVeiculo) AND $idVeiculo !== '') OR (is_array($idVeiculo) AND (!empty($idVeiculo)) )) )
            {
                $sql .= " AND pf.veiculos_id = {$idVeiculo}";
            }

            if (isset($data->dt_pedido) AND ( (is_scalar($data->dt_pedido) AND $data->dt_pedido !== '') OR (is_array($data->dt_pedido) AND (!empty($data->dt_pedido)) )) )
            {
                $sql .= " AND pf.dt_pedido BETWEEN '{$data->dt_pedido}' AND '{$data->dt_pedido_final}'";
            }

            $result = $conn->prepare($sql);
            $result->execute();
            $rows = $result->fetchAll(PDO::FETCH_OBJ);

            $groupedData = [];

            foreach ($rows as $row)
            {
                $idProposta = $row->IdProposta;
                $estaAprovado = false;

                    $sql = "SELECT * FROM pedido_frotas_historico WHERE pedido_frotas_id = {$row->id} AND estado_pedido_frotas_id = 18";

                $result = $conn->prepare($sql);
                $result->execute();
                $pedido = $result->fetch(PDO::FETCH_OBJ);
                $pagamentoAprovado = false;
                if($pedido != null){
                    $pagamentoAprovado = true;
                }

                $sqlAprovacao = "SELECT * FROM pedido_frotas_historico WHERE pedido_frotas_id = {$row->id} AND estado_pedido_frotas_id = 13";
                $resultAprovacao = $conn->prepare($sqlAprovacao);
                $resultAprovacao->execute();
                $pedidoAprovado = $resultAprovacao->fetch(PDO::FETCH_OBJ);

                if($pedidoAprovado != null){
                    $estaAprovado = true;
                }

                if (!isset($groupedData[$idProposta])) {
                    $groupedData[$idProposta] = (object) [
                        'IdProposta' => $idProposta,
                        'id' => $row->id,
                        'dt_pedido' => $row->dt_pedido,
                        'descricaopedido' => $row->descricaopedido,
                        'estabelecimento_id' => $row->estabelecimento_id,
                        'nome_estabelecimento' => $row->nome_estabelecimento,
                        'veiculos_id' => $row->veiculos_id,
                        'dt_finalizacao' => $row->dt_finalizacao,
                        'data_historico' => $row->data_historico,
                        'aprovador' => $row->name,
                        'dt_aprovacao' => $estaAprovado ? $pedidoAprovado->data_operacao : "",
                        'estado_pedido_frotas_id' => $row->estado_pedido_frotas_id,
                        'placa' => $row->placa,
                        'valor_servicos' => 0,
                        'valor_pecas' => 0,
                        'valor_total' => 0,
                        'valor_total_desconto' => 0,
                        'desconto' => $row->desconto_contratual,
                        'pagamento_aprovado' => $pagamentoAprovado  ? "sim" : "não"
                    ];
                }

                if ($row->tipo == 1) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_servicos += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    if($groupedData[$idProposta]->desconto > 0){
                        $groupedData[$idProposta]->valor_total_desconto = $groupedData[$idProposta]->valor_total - ($groupedData[$idProposta]->valor_total * ($groupedData[$idProposta]->desconto / 100));
                    }
                } else if ($row->tipo == 2) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_pecas += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    if($groupedData[$idProposta]->desconto > 0){
                        $groupedData[$idProposta]->valor_total_desconto = $groupedData[$idProposta]->valor_total - ($groupedData[$idProposta]->valor_total * ($groupedData[$idProposta]->desconto / 100));
                    }
                }  
                

                
            }

            $this->datagrid->clear();
            $totalPecas = 0;
            $totalServicos = 0;
            $total_geral = 0;
            $total_geral_desconto = 0;

            foreach ($groupedData as $item)
            {
                
                $this->datagrid->addItem($item);

                $totalPecas += (float) $item->valor_pecas;
                $totalServicos += (float) $item->valor_servicos;
                $total_geral += (float) $item->valor_total;
                $total_geral_desconto += (float) $item->valor_total_desconto;
            }

            $this->pageNavigation->setCount($total); // total de registros
            $this->pageNavigation->setProperties($param); // offset, limit, etc
            $this->pageNavigation->setLimit($limit); 

            $footerTotalecas = new stdClass;
            $footerTotalecas->id = '';
            $footerTotalecas->dt_pedido = '';
            $footerTotalecas->pagamento_aprovado = '';
            $footerTotalecas->dt_aprovacao = '';
            $footerTotalecas->valor_pecas = '';
            $footerTotalecas->valor_servicos = '';
            $footerTotalecas->descricao = 'Valor Peças';
            $footerTotalecas->valor_total = $totalPecas;

            $footerTotalServicos = new stdClass;
            $footerTotalServicos->id = '';
            $footerTotalServicos->dt_pedido = '';
            $footerTotalServicos->pagamento_aprovado = '';
            $footerTotalServicos->dt_aprovacao = '';
            $footerTotalServicos->valor_pecas = '';
            $footerTotalServicos->valor_servicos = '';
            $footerTotalServicos->descricao = 'Valor Serviços';
            $footerTotalServicos->valor_total = $totalServicos;

            $footer = new stdClass;
            $footer->id = '';
            $footer->dt_pedido = '';
            $footer->pagamento_aprovado = '';
            $footer->dt_aprovacao = '';
            $footer->valor_pecas = '';
            $footer->valor_servicos = '';
            $footer->descricao = 'Valor Total';
            $footer->valor_total = $total_geral;
            $footer->valor_total_desconto = $total_geral_desconto;

            // Adiciona o footer na datagrid
            $this->datagrid->addItem($footerTotalecas);
            $this->datagrid->addItem($footerTotalServicos);
            $this->datagrid->addItem($footer);

            TTransaction::close(); // Fecha a conexão com o banco de dados
            $this->loaded = true; 
        }
        catch (Exception $e)
        {
            TTransaction::rollback(); // Desfaz alterações em caso de erro
            new TMessage('error', $e->getMessage());
        }
    }

    public function onAcessar($param)
    {
        // Aqui você pode implementar a lógica de exclusão
        new TMessage('info', "Relatório excluído com sucesso!");
        $this->onReload();
    }

    public function onExportCsv($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->pegaObjetsDaTabelaParaexportar();

                if ($objects)
                {
                    $handler = fopen($output, 'w');
                    TTransaction::open('minierp');

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
                $objects = $this->pegaObjetsDaTabelaParaexportar();

                TTransaction::open('minierp');
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
            $logoPath = 'app/images/logo.png'; // Caminho da logo
            $reportTitle = isset($param['title']) ? $param['title'] : 'Relatório Por Veículo/Equipamento'; // Título dinâmico

            if ((!file_exists($output) && is_writable(dirname($output))) OR is_writable($output)) 
            {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->pegaObjetsDaTabelaParaexportar();

                // Clona a tabela para incluir no PDF
                $html = clone $this->datagrid;
                $columnCount = count($this->datagrid->getColumns());
                $pdfFontSize = $columnCount >= 20 ? 5 : ($columnCount >= 16 ? 6 : ($columnCount >= 12 ? 7 : 8));
                $pdfBodyFontSize = $pdfFontSize + 1;
                $pdfCellPadding = $columnCount >= 18 ? '1px 2px' : '2px 3px';
                
                // Garante que a imagem será renderizada corretamente
                $logoBase64 = '';
                if (file_exists($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoBase64 = 'data:image/png;base64,' . $logoData;
                }
                
                $contents = '
                <html>
                <head>
                    <link rel="stylesheet" type="text/css" href="app/resources/styles-print.html">
                    <style>
                        @page { margin: 12px; }
                        body { font-size: ' . $pdfBodyFontSize . 'px; }
                        table { width: 100% !important; max-width: 100% !important; table-layout: fixed !important; }
                        table th, table td, .tdatagrid_cell {
                            font-size: ' . $pdfFontSize . 'px !important;
                            line-height: 1.15 !important;
                            padding: ' . $pdfCellPadding . ' !important;
                            white-space: normal !important;
                            overflow-wrap: anywhere !important;
                            word-break: break-word !important;
                        }
                        .label {
                            width: auto !important;
                            min-width: 0 !important;
                            max-width: 100% !important;
                            font-size: ' . $pdfFontSize . 'px !important;
                            line-height: 1.15 !important;
                            white-space: normal !important;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        .header img {
                            width: 120px;
                            height: auto;
                        }
                        .header h1 {
                            font-size: 18px;
                            margin-top: 15px;
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <img src="' . $logoBase64 . '">
                        <h1>' . htmlspecialchars($reportTitle) . '</h1>
                    </div>
                    ' . $html->getContents() . '
                </body>
                </html>';

                // Criação do PDF com Dompdf
                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                // Salva o arquivo PDF gerado
                file_put_contents($output, $dompdf->output());

                // Exibe o PDF em um iframe
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
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
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
                $objects = $this->pegaObjetsDaTabelaParaexportar();

                if ($objects)
                {
                    TTransaction::open('minierp');

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->{'formatOutput'} = true;
                    $dataset = $dom->appendChild( $dom->createElement('dataset') );

                    foreach ($objects as $object)
                    {
                        $row = $dataset->appendChild( $dom->createElement('idProposta') );

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
}
