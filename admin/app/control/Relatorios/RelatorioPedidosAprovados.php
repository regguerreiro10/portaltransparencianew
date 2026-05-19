<?php
// Importando as classes para o formulário
use Adianti\Control\TPage;
use Adianti\Widget\Container\TTable as ContainerTTable;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TEntry;
use Adianti\Wrapper\BootstrapFormBuilder as WrapperBootstrapFormBuilder;

// Definindo a classe
class RelatorioPedidosAprovados extends TPage
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

        $criteria_estabelecimento_id = new TCriteria();
        $dt_inicial = new BDateRange('dt_pedido', 'dt_pedido_final');
        $estabelecimento_id = new TDBCombo('estabelecimento_id', 'minierp', 'Pessoa', 'id', '{nome}', 'nome asc', $criteria_estabelecimento_id);
        
        $dt_inicial->setMask('dd/mm/yyyy');

        $dt_inicial->setSize(220);
        $estabelecimento_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Estabelecimento:", null, '14px', null, '100%'),$estabelecimento_id],[new TLabel("Período:", null, '14px', null, '100%'),$dt_inicial]);
        $row1->layout = ['col-sm-6',' col-sm-6'];

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_data_autorizacao = new TDataGridColumn('dt_aprovacao', "Data Autorizacao", 'center');
        $column_id = new TDataGridColumn('id', "N° Do Pedido", 'center');
        $column_proposta = new TDataGridColumn('IdProposta', "N° Da Prosposta", 'center');
        $column_Placa = new TDataGridColumn('placa', 'Placa', 'center');
        $column_peca_item = new TDataGridColumn('pecaItem','Peça/Item', 'center');
        $column_estabelicimento_id = new TDataGridColumn('nome_estabelecimento', 'Estabelecimento', 'center');
        $column_qtdServico = new TDataGridColumn('qtd_servico', 'Qtde. Serviços', 'center');
        $column_valorUniServico = new TDataGridColumn('valor_uni_servico', 'Valor Uni. Serviço', 'center');
        $column_qtdPeca = new TDataGridColumn('qtdepecaItem', 'Qtde. Peças', 'center');
        $column_valorUni = new TDataGridColumn('valor_uni_peca', 'Valor Uni. Peça', 'center');      
        $column_subtotal = new TDataGridColumn('valor_total', 'SubTotal', 'center');  
        $column_total_desconto = new TDataGridColumn('valor_total_desconto', 'Total com desconto', 'center');  

        $column_valorUni->setTransformer(function ($value) {
            return is_numeric($value) ? 'R$ ' . number_format((float) $value, 2, ',', '.') : '';
        });
        
        $column_valorUniServico->setTransformer(function ($value) {
            return is_numeric($value) ? 'R$ ' . number_format((float) $value, 2, ',', '.') : '';
        });
        
        $column_subtotal->setTransformer(function ($value) {
            return is_numeric($value) ? 'R$ ' . number_format((float) $value, 2, ',', '.') : '';
        });
        
        $column_total_desconto->setTransformer(function ($value) {
            return is_numeric($value) ? 'R$ ' . number_format((float) $value, 2, ',', '.') : '';
        });

        $column_data_autorizacao->setTransformer(function ($value, $object, $row){

            $dtAutorizacao = $object->dt_aprovacao;

            if(empty($dtAutorizacao))
            {
                return"<b> {$object->descricao} </b>";
            }

            if (empty($dtAutorizacao)) {
                return "00/00/0000";
            }
        
            return date("d/m/Y", strtotime($dtAutorizacao));
        });

        
        

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        // $column_data_autorizacao->setAction($order_id);

        $this->datagrid->addColumn($column_data_autorizacao);
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_proposta);
        $this->datagrid->addColumn($column_Placa);
        $this->datagrid->addColumn($column_peca_item);
        $this->datagrid->addColumn($column_estabelicimento_id);
        $this->datagrid->addColumn($column_qtdServico);
        $this->datagrid->addColumn($column_valorUniServico);
        $this->datagrid->addColumn($column_qtdPeca);
        $this->datagrid->addColumn($column_valorUni);
        $this->datagrid->addColumn($column_subtotal);
        $this->datagrid->addColumn($column_total_desconto);

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
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['RelatorioPedidosAprovados', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['RelatorioPedidosAprovados', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['RelatorioPedidosAprovados', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['RelatorioPedidosAprovados', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

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

            $conn = TTransaction::get();
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
                    AND ph.estado_pedido_frotas_id = 13";
           
           $count_result = $conn->query($count_sql);
           $total = $count_result->fetch(PDO::FETCH_OBJ)->total;

            $sql = "SELECT DISTINCT  p.id as IdProposta, p.desconto_contratual, pf.id, pf.dt_pedido, pf.descricaopedido, 
                    pf.estabelecimento_id, epf.nome AS statusPedido, pes.nome, pf.veiculos_id, v.placa, m.descricao as descricaoModelo, v.hodometroatual, 
                    pf.dt_finalizacao, ph.data_historico, ph.aprovador_frotas_id, su.name, 
                    ph.estado_pedido_frotas_id, ip.valor_total, ip.valor, ip.descricao AS pecaItem, ip.tipo, ip.qtde,
                    ip.diasdegarantia, ip.perc_desconto
                    FROM pedido_frotas pf 
                    LEFT JOIN propostas p ON pf.id = p.pedido_frotas_id 
                    LEFT JOIN itens_propostas ip ON p.id = ip.propostas_id
                    LEFT JOIN propostas_historico ph ON p.id = ph.propostas_id
                    LEFT JOIN system_users su ON ph.aprovador_frotas_id = su.id
                    LEFT JOIN pessoa pes ON pf.estabelecimento_id = pes.id
                    LEFT JOIN veiculos v ON pf.veiculos_id = v.id
                    LEFT JOIN modelo m ON v.modelo_id = m.id
                    LEFT JOIN estado_pedido_frotas epf ON pf.estado_pedido_frotas_id = epf.id
                    WHERE data_historico is not null AND pf.estabelecimento_id is not null
                    AND ph.estado_pedido_frotas_id = 13 AND pf.departamento_unit_id = {$idUnidade};";

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

                if (!isset($groupedData[$idProposta])) {
                    $groupedData[$idProposta] = (object) [
                        'IdProposta' => $idProposta,
                        'id' => $row->id,
                        'dt_pedido' => $row->dt_pedido,
                        'descricaopedido' => $row->descricaopedido,
                        'estabelecimento_id' => $row->estabelecimento_id,
                        'nome_estabelecimento' => $row->nome,
                        'veiculos_id' => $row->veiculos_id,
                        'placa' => $row->placa,
                        'modelo' => $row->descricaoModelo,
                        'hodometroatual' => $row->hodometroatual,
                        'dt_finalizacao' => $row->dt_finalizacao,
                        'data_pg_autorizado' => $pagamentoAprovado  ? $row->data_historico : '',   
                        'aprovador' => $row->name,
                        'dt_aprovacao' => $row->data_historico,
                        'estado_pedido_frotas_id' => $row->estado_pedido_frotas_id,
                        'valor_servicos' => 0,
                        'valor_pecas' => 0,
                        'valor_total' => 0,
                        'valor_total_desconto' => 0,
                        'pagamento_aprovado' => $pagamentoAprovado  ? "sim" : "não",
                        'pecaItem' => $row->pecaItem,
                        'qtdepecaItem' => '',
                        'valor_uni_servico' => '',
                        'qtd_servico' => '',
                        'valor_uni_peca' => '',
                        'desconto' => $row->desconto_contratual,
                        'garantia' => $row->diasdegarantia
                    ];
                }

                if ($row->tipo == 1) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_servicos += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    $groupedData[$idProposta]->qtd_servico = $row->qtde;
                    $groupedData[$idProposta]->valor_uni_servico = $row->valor;
                    if($groupedData[$idProposta]->desconto > 0){
                        $groupedData[$idProposta]->valor_total_desconto = $groupedData[$idProposta]->valor_total - ($groupedData[$idProposta]->valor_total * ($groupedData[$idProposta]->desconto / 100));
                    }
                } else if ($row->tipo == 2) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_pecas += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    $groupedData[$idProposta]->qtdepecaItem = $row->qtde;
                    $groupedData[$idProposta]->valor_uni_peca = $row->valor;
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
            $footerTotalecas->valor_total_desconto  = '';

            $footerTotalServicos = new stdClass;
            $footerTotalServicos->id = '';
            $footerTotalServicos->dt_pedido = '';
            $footerTotalServicos->pagamento_aprovado = '';
            $footerTotalServicos->dt_aprovacao = '';
            $footerTotalServicos->valor_pecas = '';
            $footerTotalServicos->valor_servicos = '';
            $footerTotalServicos->descricao = 'Valor Serviços';
            $footerTotalServicos->valor_total = $totalServicos;
            $footerTotalServicos->valor_total = '';

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
            $sql = "SELECT DISTINCT  p.id as IdProposta, p.desconto_contratual, pf.id, pf.dt_pedido, pf.descricaopedido, 
                    pf.estabelecimento_id, epf.nome AS statusPedido, pes.nome, pf.veiculos_id, v.placa, m.descricao as descricaoModelo, v.hodometroatual, 
                    pf.dt_finalizacao, ph.data_historico, ph.aprovador_frotas_id, su.name, 
                    ph.estado_pedido_frotas_id, ip.valor_total, ip.valor, ip.descricao AS pecaItem, ip.tipo, ip.qtde,
                    ip.diasdegarantia, ip.perc_desconto
                    FROM pedido_frotas pf 
                    LEFT JOIN propostas p ON pf.id = p.pedido_frotas_id 
                    LEFT JOIN itens_propostas ip ON p.id = ip.propostas_id
                    LEFT JOIN propostas_historico ph ON p.id = ph.propostas_id
                    LEFT JOIN system_users su ON ph.aprovador_frotas_id = su.id
                    LEFT JOIN pessoa pes ON pf.estabelecimento_id = pes.id
                    LEFT JOIN veiculos v ON pf.veiculos_id = v.id
                    LEFT JOIN modelo m ON v.modelo_id = m.id
                    LEFT JOIN estado_pedido_frotas epf ON pf.estado_pedido_frotas_id = epf.id
                    WHERE data_historico is not null AND pf.estabelecimento_id is not null
                    AND ph.estado_pedido_frotas_id = 13 AND pf.departamento_unit_id = {$idUnidade};";

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

                if (!isset($groupedData[$idProposta])) {
                    $groupedData[$idProposta] = (object) [
                        'IdProposta' => $idProposta,
                        'id' => $row->id,
                        'dt_pedido' => $row->dt_pedido,
                        'descricaopedido' => $row->descricaopedido,
                        'estabelecimento_id' => $row->estabelecimento_id,
                        'nome_estabelecimento' => $row->nome,
                        'veiculos_id' => $row->veiculos_id,
                        'placa' => $row->placa,
                        'modelo' => $row->descricaoModelo,
                        'hodometroatual' => $row->hodometroatual,
                        'dt_finalizacao' => $row->dt_finalizacao,
                        'data_pg_autorizado' => $pagamentoAprovado  ? $row->data_historico : '',   
                        'aprovador' => $row->name,
                        'dt_aprovacao' => $row->data_historico,
                        'estado_pedido_frotas_id' => $row->estado_pedido_frotas_id,
                        'valor_servicos' => 0,
                        'valor_pecas' => 0,
                        'valor_total' => 0,
                        'valor_total_desconto' => 0,
                        'pagamento_aprovado' => $pagamentoAprovado  ? "sim" : "não",
                        'pecaItem' => $row->pecaItem,
                        'qtdepecaItem' => '',
                        'valor_uni_servico' => '',
                        'qtd_servico' => '',
                        'valor_uni_peca' => '',
                        'desconto' => $row->desconto_contratual,
                        'garantia' => $row->diasdegarantia
                    ];
                }

                if ($row->tipo == 1) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_servicos += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    $groupedData[$idProposta]->qtd_servico = $row->qtde;
                    $groupedData[$idProposta]->valor_uni_servico = $row->valor;
                    if($groupedData[$idProposta]->desconto > 0){
                        $groupedData[$idProposta]->valor_total_desconto = $groupedData[$idProposta]->valor_total - ($groupedData[$idProposta]->valor_total * ($groupedData[$idProposta]->desconto / 100));
                    }
                } else if ($row->tipo == 2) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_pecas += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    $groupedData[$idProposta]->qtdepecaItem = $row->qtde;
                    $groupedData[$idProposta]->valor_uni_peca = $row->valor;
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
            $footerTotalecas->valor_total_desconto  = '';

            $footerTotalServicos = new stdClass;
            $footerTotalServicos->id = '';
            $footerTotalServicos->dt_pedido = '';
            $footerTotalServicos->pagamento_aprovado = '';
            $footerTotalServicos->dt_aprovacao = '';
            $footerTotalServicos->valor_pecas = '';
            $footerTotalServicos->valor_servicos = '';
            $footerTotalServicos->descricao = 'Valor Serviços';
            $footerTotalServicos->valor_total = $totalServicos;
            $footerTotalServicos->valor_total = '';

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
                    AND ph.estado_pedido_frotas_id = 13";
           
           $count_result = $conn->query($count_sql);
           $total = $count_result->fetch(PDO::FETCH_OBJ)->total;

            $data = $this->form->getData();

            $sql = "SELECT DISTINCT  p.id as IdProposta, p.desconto_contratual, pf.id, pf.dt_pedido, pf.descricaopedido, 
                    pf.estabelecimento_id, epf.nome AS statusPedido, pes.nome, pf.veiculos_id, v.placa, m.descricao as descricaoModelo, v.hodometroatual, 
                    pf.dt_finalizacao, ph.data_historico, ph.aprovador_frotas_id, su.name, 
                    ph.estado_pedido_frotas_id, ip.valor_total, ip.valor, ip.descricao AS pecaItem, ip.tipo, ip.qtde,
                    ip.diasdegarantia, ip.perc_desconto
                    FROM pedido_frotas pf 
                    LEFT JOIN propostas p ON pf.id = p.pedido_frotas_id 
                    LEFT JOIN itens_propostas ip ON p.id = ip.propostas_id
                    LEFT JOIN propostas_historico ph ON p.id = ph.propostas_id
                    LEFT JOIN system_users su ON ph.aprovador_frotas_id = su.id
                    LEFT JOIN pessoa pes ON pf.estabelecimento_id = pes.id
                    LEFT JOIN veiculos v ON pf.veiculos_id = v.id
                    LEFT JOIN modelo m ON v.modelo_id = m.id
                    LEFT JOIN estado_pedido_frotas epf ON pf.estado_pedido_frotas_id = epf.id
                    WHERE data_historico is not null AND pf.estabelecimento_id is not null
                    AND ph.estado_pedido_frotas_id = 13 AND pf.departamento_unit_id = {$idUnidade}";

            if (isset($data->estabelecimento_id) AND ( (is_scalar($data->estabelecimento_id) AND $data->estabelecimento_id !== '') OR (is_array($data->estabelecimento_id) AND (!empty($data->estabelecimento_id)) )) )
            {
                $sql .= " AND pf.estabelecimento_id = {$data->estabelecimento_id}";
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

                if (!isset($groupedData[$idProposta])) {
                    $groupedData[$idProposta] = (object) [
                        'IdProposta' => $idProposta,
                        'id' => $row->id,
                        'dt_pedido' => $row->dt_pedido,
                        'descricaopedido' => $row->descricaopedido,
                        'estabelecimento_id' => $row->estabelecimento_id,
                        'nome_estabelecimento' => $row->nome,
                        'veiculos_id' => $row->veiculos_id,
                        'placa' => $row->placa,
                        'modelo' => $row->descricaoModelo,
                        'hodometroatual' => $row->hodometroatual,
                        'dt_finalizacao' => $row->dt_finalizacao,
                        'data_pg_autorizado' => $pagamentoAprovado  ? $row->data_historico : '',   
                        'aprovador' => $row->name,
                        'dt_aprovacao' => $row->data_historico,
                        'estado_pedido_frotas_id' => $row->estado_pedido_frotas_id,
                        'valor_servicos' => 0,
                        'valor_pecas' => 0,
                        'valor_total' => 0,
                        'valor_total_desconto' => 0,
                        'pagamento_aprovado' => $pagamentoAprovado  ? "sim" : "não",
                        'pecaItem' => $row->pecaItem,
                        'qtdepecaItem' => '',
                        'valor_uni_servico' => '',
                        'qtd_servico' => '',
                        'valor_uni_peca' => '',
                        'desconto' => $row->desconto_contratual,
                        'garantia' => $row->diasdegarantia
                    ];
                }

                if ($row->tipo == 1) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_servicos += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    $groupedData[$idProposta]->qtd_servico = $row->qtde;
                    $groupedData[$idProposta]->valor_uni_servico = $row->valor;
                    if($groupedData[$idProposta]->desconto > 0){
                        $groupedData[$idProposta]->valor_total_desconto = $groupedData[$idProposta]->valor_total - ($groupedData[$idProposta]->valor_total * ($groupedData[$idProposta]->desconto / 100));
                    }
                } else if ($row->tipo == 2) {
                    $valor = $row->valor * $row->qtde;
                    $groupedData[$idProposta]->valor_pecas += $valor;
                    $groupedData[$idProposta]->valor_total += $valor;
                    $groupedData[$idProposta]->qtdepecaItem = $row->qtde;
                    $groupedData[$idProposta]->valor_uni_peca = $row->valor;
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
            $footerTotalecas->valor_total_desconto  = '';

            $footerTotalServicos = new stdClass;
            $footerTotalServicos->id = '';
            $footerTotalServicos->dt_pedido = '';
            $footerTotalServicos->pagamento_aprovado = '';
            $footerTotalServicos->dt_aprovacao = '';
            $footerTotalServicos->valor_pecas = '';
            $footerTotalServicos->valor_servicos = '';
            $footerTotalServicos->descricao = 'Valor Serviços';
            $footerTotalServicos->valor_total = $totalServicos;
            $footerTotalServicos->valor_total = '';

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
            $reportTitle = isset($param['title']) ? $param['title'] : 'Relatório De Pedidos Aprovados'; // Título dinâmico

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
                        body{
                            font-size: ' . $pdfBodyFontSize . 'px;
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
                        table {
                            width: 100% !important;
                            max-width: 100% !important;
                            table-layout: fixed !important;
                            border-collapse: collapse; /* Para unir as bordas */
                        }
                        table, th, td {
                            border: 1px solid black; /* Adicionando bordas nas tabelas */
                        }
                        th, td {
                            padding: ' . $pdfCellPadding . ' !important;
                            text-align: left;
                            font-size: ' . $pdfFontSize . 'px !important;
                            line-height: 1.15 !important;
                            white-space: normal !important;
                            overflow-wrap: anywhere !important;
                            word-break: break-word !important;
                        }
                        .tdatagrid_cell {
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
                        .divider {
                            border-top: 2px solid black; /* Linha divisória entre seções */
                            margin: 10px 0;
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
