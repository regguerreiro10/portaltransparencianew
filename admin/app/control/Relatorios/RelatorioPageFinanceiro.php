<?php
// Importando as classes para o formulário
use Adianti\Control\TPage;
use Adianti\Widget\Container\TTable as ContainerTTable;
use Adianti\Widget\Form\TButton;
use Adianti\Wrapper\BootstrapFormBuilder as WrapperBootstrapFormBuilder;

// Definindo a classe
class RelatorioPageFinanceiro extends TPage
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

        $basename   = urlencode('consulta-frotas-list.pdf');
        $download   = "download.php?file=app/manual/consulta-frotas-list.pdf&basename={$basename}";

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
        $action_onAcessar->setImage('fas:print rgb(45, 22, 218)');
        $action_onAcessar->setField('id');

        $this->datagrid->addAction($action_onAcessar);
        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Consulta de frotas {$manual}");
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
            $this->arrayDeRelatorios = [
                ['id' => 1, 'descricao' => 'Movimentações Dotação Orçamentária'],
                ['id' => 2, 'descricao' => 'Manutenção de garantias dos Veiculos, Aeronaves e/ou Equipamentos'],
                ['id' => 3, 'descricao' => 'Comparação de Preços Praticados'],
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
                      //  new TMessage('info', 'Relatório Antecipacoes em analise temporariamente desativado para manutenção.');
                        AdiantiCoreApplication::loadPage('ConsultaDotacaoSaldoDepartamentoFrotasList');
                        break;
                    case 2:
                       // new TMessage('info', 'Relatório OS pagamentos aprovados temporariamente desativado para manutenção.');
                        AdiantiCoreApplication::loadPage('ManutencaoGarantiaRelatorioList');
                        break;
                    case 3:
                       // new TMessage('info', 'Relatório OS pagamentos aprovados temporariamente desativado para manutenção.');
                        AdiantiCoreApplication::loadPage('ViewComparacaoprodutosList');
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