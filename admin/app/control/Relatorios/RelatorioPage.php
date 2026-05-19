<?php
// Importando as classes para o formulário
use Adianti\Control\TPage;
use Adianti\Widget\Container\TTable as ContainerTTable;
use Adianti\Widget\Form\TButton;
use Adianti\Wrapper\BootstrapFormBuilder as WrapperBootstrapFormBuilder;

// Definindo a classe
class RelatorioPage extends TPage
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

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

                $basename   = urlencode('relatorios-frotas-list.pdf');
        $download   = "download.php?file=app/manual/relatorios-frotas-list.pdf&basename={$basename}";

        $manual = "
            <span style='float:right;'>
                <a href='{$download}'
                target='_blank'
                style='text-decoration:none;margin-left:10px;'>
                    <i class='fa fa-question-circle'> </i>
                </a>
            </span>
        "; 

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_descricao = new TDataGridColumn('descricao', "Descricao", 'left');

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        //<onBeforeColumnsCreation>

        //</onBeforeColumnsCreation>

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricao);

        //<onAfterColumnsCreation>

        //</onAfterColumnsCreation>

        $action_onAcessar = new TDataGridAction(array($this, 'onAcessar'));
        $action_onAcessar->setUseButton(false);
        $action_onAcessar->setButtonClass('btn btn-default btn-sm');
        $action_onAcessar->setLabel("Acessar");
        $action_onAcessar->setImage('fas:print rgb(0, 123, 255)');
        $action_onAcessar->setField('id');

        $this->datagrid->addAction($action_onAcessar);
        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de Relatórios {$manual}");
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
            // Aqui você deve carregar os dados dos relatórios do banco de dados
            // Exemplo de dados estáticos para demonstração
           $desc8 = (TSession::getValue('tipofrota') == 2)
                ? 'Relatório de Gastos por Horímetro Rodado'
                : 'Relatório de Gastos por KM Rodado';

            $this->arrayDeRelatorios = [
                ['id' => 1, 'descricao' => 'Relatório de Pedidos Aprovados por Estabelecimento, Veiculos, Aeronaves e/ou Equipamentos, Estado Pedido e Analítico*'],
                ['id' => 2, 'descricao' => 'Relatório de Pedidos Aprovados por Itens Produtos/Serviços, Estabelecimento, Veiculos, Aeronaves e/ou Equipamentos, Estado Pedido e Analítico*'],
                ['id' => 3, 'descricao' => 'Relatório de Pedidos Aprovados por Itens Produtos/Serviços, Tipo, Estabelecimento, Veiculos, Aeronaves e/ou Equipamentos, Estado Pedido e Analítico*'],
                ['id' => 4, 'descricao' => 'Relatório de Pedidos por Tipo de Manutenção, Estabelecimento, Veiculos, Aeronaves e/ou Equipamentos, Estado Pedido e Analítico*'],
                ['id' => 5, 'descricao' => 'Relatório De Notas Fiscais Gestor*'],
                ['id' => 6, 'descricao' => 'Relatório das Manutenções por Estabelecimento Sintéticos*'],
                ['id' => 7, 'descricao' => 'Relatório dos Estabelecimentos Credenciados **'],
                ['id' => 8, 'descricao' => $desc8],
                ['id' => 9, 'descricao' => 'Relatório de Média de Preço de Produtos/Serviços por Veiculos, Aeronaves e/ou Equipamentos'],
                ['id' => 10, 'descricao' => 'Relatório de Pedidos de Frotas Consolidado'],
                ['id' => 11, 'descricao' => 'Relatório de Recorrência de Produtos/Serviços por Marca e Modelo'],
            ];

            $this->datagrid->clear();
            foreach ($this->arrayDeRelatorios as $row)
            {
                $this->datagrid->addItem((object) $row);
            }

            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onAcessar($param)
    {
        try {
            $id = $param['id'] ?? null;
            if ($id) {
                switch ($id) {
                    case 1:
                        //new TMessage('info', 'Relatório por rede temporariamente desativado para manutenção.');
                        //AdiantiCoreApplication::loadPage('RelatorioPorRede');
                        AdiantiCoreApplication::loadPage('ViewPropostaaprovadaporredeList');

                        break;
                    case 2:
                        AdiantiCoreApplication::loadPage('ViewProdutosServicosAprovadosList');
                        //AdiantiCoreApplication::loadPage('RelatorioPorPeca');
                        break;
                    case 3:
//                        AdiantiCoreApplication::loadPage('RelatorioPecasVeiculoAnalitico');
                        AdiantiCoreApplication::loadPage('ViewRelatoriopecasveiculosList');
                        break;
                    case 4:
//                        AdiantiCoreApplication::loadPage('RelatorioDeManutencao');
                        AdiantiCoreApplication::loadPage('ViewRelatoriomanutencaoList');
                        break;
                    case 5:
                        AdiantiCoreApplication::loadPage('RelNotasSystemUnitList');
                        break;
                    case 6:
                        AdiantiCoreApplication::loadPage('ViewRelatorioporredeSinteticoList');
                        break;
                    case 7:
                         AdiantiCoreApplication::loadPage('ViewRedescredenciadasList');
                        
                        break;
                    case 8:
                        new TMessage('info', 'Relatório de Gastos por KM Rodado temporariamente desativado para manutenção.');
                        break;
                    case 9:
                        new TMessage('info', 'Relatório de Média de Preço de Produtos/Serviços por Veículo temporariamente desativado para manutenção.');
                        break;
                    case 10:
                        // new TMessage('info', 'Relatório de Pedidos de Frotas Consolidado temporariamente desativado para manutenção.');
                        AdiantiCoreApplication::loadPage('PedidoRelFrotasList');
                        break;
                    case 11:
                        AdiantiCoreApplication::loadPage('ViewRelatorioTrocasProdutoVeiculoList');
                        break;
                    default:
                        new TMessage('error', "Relatório não encontrado.");
                        break;
                }
            } else {
                new TMessage('error', "ID do relatório não foi informado.");
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    

    public function onExportCsv($param)
    {
        // Aqui você pode implementar a lógica de exportação para CSV
        new TMessage('info', "Exportado para CSV!");
    }

    public function onExportXls($param)
    {
        // Aqui você pode implementar a lógica de exportação para XLS
        new TMessage('info', "Exportado para XLS!");
    }

    public function onExportPdf($param)
    {
        // Aqui você pode implementar a lógica de exportação para PDF
        new TMessage('info', "Exportado para PDF!");
    }

    public function onExportXml($param)
    {
        // Aqui você pode implementar a lógica de exportação para XML
        new TMessage('info', "Exportado para XML!");
    }

    public function onShow(){
        // Caso precise de alguma lógica adicional quando a página for exibida
    }
}
