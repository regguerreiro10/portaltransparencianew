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
class RelatorioOsPagamentosAprovados extends TPage
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
        $estabelecimento = new TDBCombo('estabelecimento_id', 'minierp', 'Pessoa', 'id', '{nome}', 'nome asc', $criteria_estabelecimento_id);
        
        $dt_inicial->setMask('dd/mm/yyyy');
        $estabelecimento->enableSearch();

        $dt_inicial->setSize(220);
        $estabelecimento->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Eestabelecimento:", null, '14px', null, '100%'),$estabelecimento],[new TLabel("Período:", null, '14px', null, '100%'),$dt_inicial]);
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
        $column_caminho = new TDataGridColumn('caminho', "Nota Fiscal", 'center');
        $column_os = new TDataGridColumn('caminho', "Os", 'center');
        $column_vPecas = new TDataGridColumn('valor_pecas', 'Valor Peças', 'center');
        $column_vServicos = new TDataGridColumn('valor_servicos', 'Valor Serviço', 'center');
        $column_vTotal = new TDataGridColumn('valor_total', 'Valor Total', 'center');

        $column_descricao->setTransformer(function ($value, $object, $row) {
            return "<b>Rede:</b> {$object->estabelecimento_id} <br>
                    <b>Nº: {$object->IdProposta} - Cliente: {$object->nome_estabelecimento} <br>
                    <b>Pedido:</b> {$object->descricaopedido} | Placa: {$object->placa} <br>
                    <b>Autorizado por:</b> {$object->aprovador}";
        });

        $column_peridoServico->setTransformer(function ($value, $object, $row){

            $dataFormatadaInicio = date("d/m/Y", strtotime($object->dt_pedido));
            $dataFormatadaFinal = ($object->dt_finalizacao) ? date("d/m/Y", strtotime($object->dt_finalizacao)) : "00/00/0000";
            return "<b>Início:</b> {$dataFormatadaInicio} <br>
                    <b>Final:</b> {$dataFormatadaFinal}" ;
        });

        $column_caminho->setTransformer(function($value, $object, $row) {
            $botoes = '';
            if (!empty($object->caminho)  && is_array($object->caminho)) {
                foreach ($object->caminho as $index => $caminho) {
                    $url = str_replace(['(','{','->', '-','>','}',')', ' '], ['','','_','','','','','_'], $caminho);
                    $botoes .= "<a href='download.php?file={$url}' target='_blank' class='btn btn-sm btn-success' style='margin:2px'>NF " . ($index + 1) . "</a>";
                }
            }
            return $botoes ?: 'Sem arquivos';
        });
        
        $column_os->setTransformer(function($value, $object, $row) {
            $botoes = '';
            
            $botoes .= "<a href='index.php?class=PedidoFrotasList&method=onImprimePedido&key={$object->id}&id={$object->id}' target='_blank' class='btn btn-sm btn-success' style='margin:2px'>OS</a>";
            return $botoes ?: 'Sem arquivos';
        });

        

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_peridoServico);
        $this->datagrid->addColumn($column_caminho);
        $this->datagrid->addColumn($column_os);
        $this->datagrid->addColumn($column_vPecas);
        $this->datagrid->addColumn($column_vServicos);
        $this->datagrid->addColumn($column_vTotal);

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

        if (!isset($_GET['method']) || $_GET['method'] === 'onReload') {
            $this->onReload($_GET);
        }
        
        parent::add($container);
    }

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('minierp'); // Abre uma conexão com o banco de dados
            $idEstabelecimento = isset($param['id']) ? (int) $param['id'] : null;
            $idUnidade = (int)TSession::getValue('idunit');

            $conn = TTransaction::get(); // Obtém a conexão ativa
            $sql = "SELECT DISTINCT  p.id as IdProposta, pf.id, pf.dt_pedido, pf.descricaopedido, 
                    pf.estabelecimento_id, pes.nome, pf.veiculos_id, pf.dt_finalizacao, 
                    ph.data_historico, ph.aprovador_frotas_id, su.name, 
                    ph.estado_pedido_frotas_id, ip.valor_total, ip.valor, ip.tipo, v.placa, ip.qtde, dp.caminho
                    FROM pedido_frotas pf 
                    LEFT JOIN propostas p ON pf.id = p.pedido_frotas_id 
                    LEFT JOIN itens_propostas ip ON p.id = ip.propostas_id
                    LEFT JOIN propostas_historico ph ON p.id = ph.propostas_id
                    LEFT JOIN system_users su ON ph.aprovador_frotas_id = su.id
                    LEFT JOIN pessoa pes ON pf.estabelecimento_id = pes.id
                    LEFT JOIN veiculos v ON pf.veiculos_id = v.id
                    LEFT JOIN documentos_propostas dp ON p.id = dp.propostas_id
                    WHERE data_historico is not null AND ph.estado_pedido_frotas_id IN (8,13,18) AND pf.departamento_unit_id = {$idUnidade}";

            $result = $conn->prepare($sql);
            $result->execute();
            $rows = $result->fetchAll(PDO::FETCH_OBJ);

            if ($idEstabelecimento) {
                $sql .= " AND pf.estabelecimento_id = {$idEstabelecimento}";
            }

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
                        'nome_estabelecimento' => $row->nome,
                        'veiculos_id' => $row->veiculos_id,
                        'dt_finalizacao' => $row->dt_finalizacao,
                        'data_historico' => $row->data_historico,
                        'aprovador' => $row->name,
                        'dt_aprovacao' => $estaAprovado ? $pedidoAprovado->data_operacao : "",
                        'estado_pedido_frotas_id' => $row->estado_pedido_frotas_id,
                        'valor_servicos' => 0,
                        'valor_pecas' => 0,
                        'valor_total' => 0,
                        'pagamento_aprovado' => $pagamentoAprovado  ? "sim" : "não",
                        'placa' => $row->placa,
                        'caminho' => []
                    ];
                }

                if($row->caminho != null && $groupedData[$idProposta]->IdProposta == $row->IdProposta)
                {
                    $groupedData[$idProposta]->caminho[] = $row->caminho;
                }

                if($row->estado_pedido_frotas_id == 8){
                    if ($row->tipo == 1) {
                        $groupedData[$idProposta]->valor_servicos += $row->valor;
                        $groupedData[$idProposta]->valor_total += $row->valor * $row->qtde;
                    } else if ($row->tipo == 2) {
                        $groupedData[$idProposta]->valor_pecas += $row->valor;
                        $groupedData[$idProposta]->valor_total += $row->valor * $row->qtde;
                    }
                }
                

                
            }

            $this->datagrid->clear();
            foreach ($groupedData as $item)
            {
                $this->datagrid->addItem($item);
            }

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

            $conn = TTransaction::get(); // Obtém a conexão ativa
            $idUnidade = (int)TSession::getValue('idunit');

            $sql = "SELECT DISTINCT  p.id as IdProposta, pf.id, pf.dt_pedido, pf.descricaopedido, 
                    pf.estabelecimento_id, pes.nome, pf.veiculos_id, pf.dt_finalizacao, 
                    ph.data_historico, ph.aprovador_frotas_id, su.name, 
                    ph.estado_pedido_frotas_id, ip.valor_total, ip.valor, ip.tipo, v.placa, ip.qtde, dp.caminho
                    FROM pedido_frotas pf 
                    LEFT JOIN propostas p ON pf.id = p.pedido_frotas_id 
                    LEFT JOIN itens_propostas ip ON p.id = ip.propostas_id
                    LEFT JOIN propostas_historico ph ON p.id = ph.propostas_id
                    LEFT JOIN system_users su ON ph.aprovador_frotas_id = su.id
                    LEFT JOIN pessoa pes ON pf.estabelecimento_id = pes.id
                    LEFT JOIN veiculos v ON pf.veiculos_id = v.id
                    LEFT JOIN documentos_propostas dp ON p.id = dp.propostas_id
                    WHERE data_historico is not null AND ph.estado_pedido_frotas_id IN (8,13,18) AND pf.departamento_unit_id = {$idUnidade}";
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
                        'nome_estabelecimento' => $row->nome,
                        'veiculos_id' => $row->veiculos_id,
                        'dt_finalizacao' => $row->dt_finalizacao,
                        'data_historico' => $row->data_historico,
                        'aprovador' => $row->name,
                        'dt_aprovacao' => $estaAprovado ? $pedidoAprovado->data_operacao : "",
                        'estado_pedido_frotas_id' => $row->estado_pedido_frotas_id,
                        'valor_servicos' => 0,
                        'valor_pecas' => 0,
                        'valor_total' => 0,
                        'pagamento_aprovado' => $pagamentoAprovado  ? "sim" : "não",
                        'placa' => $row->placa
                    ];
                }

                if($row->estado_pedido_frotas_id == 8){
                    if ($row->tipo == 1) {
                        $groupedData[$idProposta]->valor_servicos += $row->valor;
                        $groupedData[$idProposta]->valor_total += $row->valor * $row->qtde;
                    } else if ($row->tipo == 2) {
                        $groupedData[$idProposta]->valor_pecas += $row->valor;
                        $groupedData[$idProposta]->valor_total += $row->valor * $row->qtde;
                    }
                }
                

                
            }

            $this->datagrid->clear();
            foreach ($groupedData as $item)
            {
                $this->datagrid->addItem($item);
            }

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

            $data = $this->form->getData();
            $idEstabelecimento = (int)$data->estabelecimento_id;
            $conn = TTransaction::get(); // Obtém a conexão ativa
            $idUnidade = (int)TSession::getValue('idunit');

            $sql = "SELECT DISTINCT  p.id as IdProposta, pf.id, pf.dt_pedido, pf.descricaopedido, 
                    pf.estabelecimento_id, pes.nome, pf.veiculos_id, pf.dt_finalizacao, 
                    ph.data_historico, ph.aprovador_frotas_id, su.name, 
                    ph.estado_pedido_frotas_id, ip.valor_total, ip.valor, ip.tipo, v.placa, ip.qtde
                    FROM pedido_frotas pf 
                    LEFT JOIN propostas p ON pf.id = p.pedido_frotas_id 
                    LEFT JOIN itens_propostas ip ON p.id = ip.propostas_id
                    LEFT JOIN propostas_historico ph ON p.id = ph.propostas_id
                    LEFT JOIN system_users su ON ph.aprovador_frotas_id = su.id
                    LEFT JOIN pessoa pes ON pf.estabelecimento_id = pes.id
                    LEFT JOIN veiculos v ON pf.veiculos_id = v.id
                    WHERE data_historico is not null AND ph.estado_pedido_frotas_id IN (8,13,18) AND pf.departamento_unit_id = {$idUnidade}s";

            if (isset($idEstabelecimento) AND $idEstabelecimento != 0 AND ( (is_scalar($idEstabelecimento) AND $idEstabelecimento !== '') OR (is_array($idEstabelecimento) AND (!empty($idEstabelecimento)) )) )
            {
                $sql .= " AND pf.estabelecimento_id = {$idEstabelecimento}";
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
                        'nome_estabelecimento' => $row->nome,
                        'veiculos_id' => $row->veiculos_id,
                        'dt_finalizacao' => $row->dt_finalizacao,
                        'data_historico' => $row->data_historico,
                        'aprovador' => $row->name,
                        'dt_aprovacao' => $estaAprovado ? $pedidoAprovado->data_operacao : "",
                        'estado_pedido_frotas_id' => $row->estado_pedido_frotas_id,
                        'valor_servicos' => 0,
                        'valor_pecas' => 0,
                        'valor_total' => 0,
                        'pagamento_aprovado' => $pagamentoAprovado  ? "sim" : "não",
                        'placa' => $row->placa
                    ];
                }

                if($row->estado_pedido_frotas_id == 8){
                    if ($row->tipo == 1) {
                        $groupedData[$idProposta]->valor_servicos += $row->valor;
                        $groupedData[$idProposta]->valor_total += $row->valor * $row->qtde;
                    } else if ($row->tipo == 2) {
                        $groupedData[$idProposta]->valor_pecas += $row->valor;
                        $groupedData[$idProposta]->valor_total += $row->valor * $row->qtde;
                    }
                }
                

                
            }

            $this->datagrid->clear();
            foreach ($groupedData as $item)
            {
                $this->datagrid->addItem($item);
            }

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
