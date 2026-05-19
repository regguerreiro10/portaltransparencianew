<?php

class PedidoVendaFormViewForn extends TPage
{
    protected $form; // form
    private static $database = 'minierp';
    private static $activeRecord = 'Pedido';
    private static $primaryKey = 'id';
    private static $formName = 'formView_Pedido';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        TTransaction::open(self::$database);
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setTagName('div');

        $pedido = new Pedido($param['key']);
        // define the form title
        $this->form->setFormTitle("Consulta de Pedido ");

        $criteria_tdbarrowstep1 = new TCriteria();

        $filterVar = "T";
        $criteria_tdbarrowstep1->add(new TFilter('kanban', '=', $filterVar)); 

        $tdbarrowstep1 = new TDBArrowStep('tdbarrowstep1', 'minierp', 'EstadoPedido', 'id', '{nome}','ordem asc' , $criteria_tdbarrowstep1);
        $label = new TLabel("Id:", '', '14px', 'B', '100%');
        $text1 = new TTextDisplay($pedido->id, '', '16px', '');
        $fornecedor = new TLabel("Fornecedor:", '', '14px', 'B', '100%');
        $text7 = new TTextDisplay($pedido->cliente->nome, '', '16px', '');
        $label6 = new TLabel("Depart/Secretária", '', '14px', 'B', '100%');
        $text12 = new TTextDisplay(TDateTime::convertToMask($pedido->departamento_unit->name, 'yyyy-mm-dd hh:ii', 'dd/mm/yyyy hh:ii'), '', '16px', '');
        $label8 = new TLabel("Data do Pedido:", '', '14px', 'B', '100%');
        $text5 = new TTextDisplay(TDate::convertToMask($pedido->dt_pedido, 'yyyy-mm-dd', 'dd/mm/yyyy'), '', '16px', '');
        $label10 = new TLabel("Descrição do Pedido", '', '14px', 'B', '100%');
        $text2 = new TTextDisplay($pedido->descricaopedido, '', '16px', '');
        $label12 = new TLabel("Usuário", '', '14px', 'B', '100%');
        $text3 = new TTextDisplay($pedido->system_users->name, '', '16px', '');
        $label50 = new TLabel("Centro de Custo", '', '14px', 'B', '100%');
        $textccusto = new TTextDisplay($pedido->centrocusto->nome, '', '16px', '');
        $label18 = new TLabel("Valor total:", '', '14px', 'B', '100%');
        $text10 = new TTextDisplay(number_format((double)$pedido->valor_total, '2', ',', '.'), '', '16px', '');
        $label14 = new TLabel("Observações:", '', '14px', 'B', '100%');
        $text6 = new TTextDisplay($pedido->obs, '', '16px', '');
        $label44 = new TLabel("Rótulo:", '', '12px', '');
        $label66 = new TLabel("Rótulo:", '', '12px', '');
        $label88 = new TLabel("Rótulo:", '', '12px', '');
        $linha_do_tempo = new BPageContainer();

        $tdbarrowstep1->setEditable(false);
        $tdbarrowstep1->setColorColumn('cor');
        $tdbarrowstep1->setFilledColor('#fd9308');
        $tdbarrowstep1->setFilledFontColor('#ffffff');
        $tdbarrowstep1->setUnfilledColor('#d3d3d3');
        $tdbarrowstep1->setUnfilledFontColor('#333333');
        $tdbarrowstep1->setWidth('100%');
        $tdbarrowstep1->setHeight('60');
        $tdbarrowstep1->setValue($pedido->estado_pedido_venda_id);
        $linha_do_tempo->setAction(new TAction(['PedidoVendaHistoricoTimeLine', 'onShow'], ['key' => $pedido->id]));
        $linha_do_tempo->setId('b627af0e8e2a08');
        $text7->setSize('100%');
        $textccusto->setSize('100%');
        $linha_do_tempo->setSize('100%');

        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $linha_do_tempo->add($loadingContainer);


/*
        $row1 = $this->form->addFields([$tdbarrowstep1]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([$label,$text1],[$fornecedor,$text7],[$label6,$text12],[$label8,$text5]);
        $row2->layout = [' col-sm-2','col-sm-3',' col-sm-3',' col-sm-3'];

        $row3 = $this->form->addFields([$label10,$text2],[$label12,$text3],[$label50,$textccusto],[$label18,$text10]);
        $row3->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $row4 = $this->form->addFields([$label14,$text6]);
        $row4->layout = [' col-sm-12'];

        $tab_66adfb0d6cf63 = new BootstrapFormBuilder('tab_66adfb0d6cf63');
        $this->tab_66adfb0d6cf63 = $tab_66adfb0d6cf63;
        $tab_66adfb0d6cf63->setProperty('style', 'border:none; box-shadow:none;');

        $tab_66adfb0d6cf63->appendPage("Produtos");

        $tab_66adfb0d6cf63->addFields([new THidden('current_tab_tab_66adfb0d6cf63')]);
        $tab_66adfb0d6cf63->setTabFunction("$('[name=current_tab_tab_66adfb0d6cf63]').val($(this).attr('data-current_page'));");

        $this->itens_pedido_pedido_venda_id_list = new TQuickGrid;
        $this->itens_pedido_pedido_venda_id_list->disableHtmlConversion();
        $this->itens_pedido_pedido_venda_id_list->style = 'width:100%';
        $this->itens_pedido_pedido_venda_id_list->disableDefaultClick();

        $column_produto_familia_produto_nome = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Família", 'produto->familia_produto->nome', 'left');
        $column_produto_nome = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Produto", 'produto->nome', 'left');
        $column_quantidade = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Quantidade", 'quantidade', 'left');
        $column_valor_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Valor", 'valor', 'left');
        $column_desconto_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Desconto", 'desconto', 'left');
        $column_valor_total_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Valor total", 'valor_total', 'left');
        $column_id = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Nova coluna", 'id', 'left');

        $column_valor_total_transformed->setTotalFunction( function($values) { 
            return array_sum((array) $values); 
        }); 

        $column_valor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_valor_total_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $this->itens_pedido_pedido_venda_id_list->createModel();

        $criteria_itens_pedido_pedido_venda_id = new TCriteria();
        $criteria_itens_pedido_pedido_venda_id->add(new TFilter('pedido_venda_id', '=', $pedido->id));

        $criteria_itens_pedido_pedido_venda_id->setProperty('order', 'id desc');

        $itens_pedido_pedido_venda_id_items = ItensPedido::getObjects($criteria_itens_pedido_pedido_venda_id);

        $this->itens_pedido_pedido_venda_id_list->addItems($itens_pedido_pedido_venda_id_items);

        $icon = new TImage('fas:boxes #2196F3');
        $title = new TTextDisplay("{$icon} PRODUTOS", '#333', '16px', '{$fontStyle}');

        $panel = new TPanelGroup($title, '#f5f5f5');
        $panel->class = 'panel panel-default formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->itens_pedido_pedido_venda_id_list));

        $tab_66adfb0d6cf63->addContent([$panel]);

        $tab_66adfb0d6cf63->appendPage("Cidades a Receber");
        $row5 = $tab_66adfb0d6cf63->addFields([$label44]);
        $row5->layout = [' col-sm-12'];

        $tab_66adfb0d6cf63->appendPage("Seguimentos a receber");
        $row6 = $tab_66adfb0d6cf63->addFields([$label66]);
        $row6->layout = [' col-sm-12'];

        $tab_66adfb0d6cf63->appendPage("Arquivos");
        $row7 = $tab_66adfb0d6cf63->addFields([$label88]);
        $row7->layout = [' col-sm-12'];

        $row8 = $this->form->addFields([$tab_66adfb0d6cf63]);
        $row8->layout = [' col-sm-12'];

        $row9 = $this->form->addFields([$linha_do_tempo]);
        $row9->layout = [' col-sm-12'];

        if(!empty($param['current_tab']))
        {
            $this->form->setCurrentPage($param['current_tab']);
        }

        if(!empty($param['current_tab_tab_66adfb0d6cf63']))
        {
            $this->tab_66adfb0d6cf63->setCurrentPage($param['current_tab_tab_66adfb0d6cf63']);
        }

*/
 //</onBeforeAddFieldsToForm>
         $row1 = $this->form->addFields([$tdbarrowstep1]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([$label,$text1],[$fornecedor,$text7],[$label6,$text12],[$label8,$text5]);
        $row2->layout = ['col-sm-3','col-sm-3',' col-sm-3',' col-sm-3'];

        $row3 = $this->form->addFields([$label10,$text2],[$label12,$text3],[$label50,$textccusto],[$label18,$text10]);
        $row3->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $row4 = $this->form->addFields([$label14,$text6]);
        $row4->layout = [' col-sm-12'];

        $tab_66adfb0d6cf63 = new BootstrapFormBuilder('tab_66adfb0d6cf63');
        $this->tab_66adfb0d6cf63 = $tab_66adfb0d6cf63;
        $tab_66adfb0d6cf63->setProperty('style', 'border:none; box-shadow:none;');

        $tab_66adfb0d6cf63->appendPage("Produtos");

        $tab_66adfb0d6cf63->addFields([new THidden('current_tab_tab_66adfb0d6cf63')]);
        $tab_66adfb0d6cf63->setTabFunction("$('[name=current_tab_tab_66adfb0d6cf63]').val($(this).attr('data-current_page'));");

        $this->itens_pedido_pedido_venda_id_list = new TQuickGrid;
        $this->itens_pedido_pedido_venda_id_list->disableHtmlConversion();
        $this->itens_pedido_pedido_venda_id_list->style = 'width:100%';
        $this->itens_pedido_pedido_venda_id_list->disableDefaultClick();

        $column_produto_familia_produto_nome = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Família", 'produto->familia_produto->nome', 'left');
        $column_produto_nome = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Produto", 'produto->nome', 'left');
        $column_quantidade = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Quantidade", 'quantidade', 'left');
        $column_valor_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Valor", 'valor', 'left');
        $column_desconto_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Desconto", 'desconto', 'left');
        $column_valor_total_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Valor total", 'valor_total', 'left');

        $column_valor_total_transformed->setTotalFunction( function($values) { 
            return array_sum((array) $values); 
        }); 

        $column_valor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_valor_total_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $this->itens_pedido_pedido_venda_id_list->createModel();

        $criteria_itens_pedido_pedido_venda_id = new TCriteria();
        $criteria_itens_pedido_pedido_venda_id->add(new TFilter('pedido_venda_id', '=', $pedido->id));

        $criteria_itens_pedido_pedido_venda_id->setProperty('order', 'id desc');

        $itens_pedido_pedido_venda_id_items = ItensPedido::getObjects($criteria_itens_pedido_pedido_venda_id);

        $this->itens_pedido_pedido_venda_id_list->addItems($itens_pedido_pedido_venda_id_items);

        $icon1 = new TImage('fas:boxes #2196F3');
        $title1 = new TTextDisplay("{$icon1} PRODUTOS", '#333', '16px', '{$fontStyle}');

        $panel1 = new TPanelGroup($title1, '#f5f5f5');
        $panel1->class = 'panel panel-default formView-detail';
        $panel1->add(new BootstrapDatagridWrapper($this->itens_pedido_pedido_venda_id_list));

        $tab_66adfb0d6cf63->addContent([$panel1]);
        $row5 = $this->form->addFields([$tab_66adfb0d6cf63]);
        $row5->layout = [' col-sm-12'];

        $this->cidade_pedido_list = new TQuickGrid;
        $this->cidade_pedido_list->disableHtmlConversion();
        $this->cidade_pedido_list->style = 'width:100%';
        $this->cidade_pedido_list->disableDefaultClick();

        $column_nomecidade = $this->cidade_pedido_list->addQuickColumn("Descrição", 'cidade->nome', 'left');

        $this->cidade_pedido_list->createModel();

        $criteria_cidade_pedido = new TCriteria();
        $criteria_cidade_pedido->add(new TFilter('pedido_id', '=', $pedido->id));

        $criteria_cidade_pedido->setProperty('order', 'id desc');

        $detalhes_cidade_pedido = CidadePedido::getObjects($criteria_cidade_pedido);

        $this->cidade_pedido_list->addItems($detalhes_cidade_pedido);

        $icon = new TImage('fa:city #2196F3'); 
        $title = new TTextDisplay("{$icon} CIDADE A RECEBER", '#333', '16px', '{$fontStyle}');

        $panel = new TPanelGroup($title, '#f5f5f5');
        $panel->class = 'panel panel-default formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->cidade_pedido_list));

        $tab_66adfb0d6cf63->appendPage("Cidades a Receber");
        $row5 = $tab_66adfb0d6cf63->addFields([$panel]);
        $row5->layout = [' col-sm-12'];

        // seguimento

        $this->seguimento_pedido_list = new TQuickGrid;
        $this->seguimento_pedido_list->disableHtmlConversion();
        $this->seguimento_pedido_list->style = 'width:100%';
        $this->seguimento_pedido_list->disableDefaultClick();

        $column_nomeseguimento = $this->seguimento_pedido_list->addQuickColumn("Descrição", 'seguimento->descricao', 'left');

        $this->seguimento_pedido_list->createModel();

        $criteria_seguimento_pedido = new TCriteria();
        $criteria_seguimento_pedido->add(new TFilter('pedido_id', '=', $pedido->id));

        $criteria_seguimento_pedido->setProperty('order', 'id desc');

        $detalhes_seguimento_pedido = PedidoSeguimento::getObjects($criteria_seguimento_pedido);

        $this->seguimento_pedido_list->addItems($detalhes_seguimento_pedido);

        $icon3 = new TImage('fa:building #2196F3');
        $title3 = new TTextDisplay("{$icon3} SEGUIMENTOS A RECEBER", '#333', '16px', '{$fontStyle}');

        $panel2 = new TPanelGroup($title3, '#f5f5f5');
        $panel2->class = 'panel panel-default formView-detail';
        $panel2->add(new BootstrapDatagridWrapper($this->seguimento_pedido_list));

        $tab_66adfb0d6cf63->appendPage("Seguimentos a receber");
        $row6 = $tab_66adfb0d6cf63->addFields([$panel2]);
        $row6->layout = [' col-sm-12'];

         // arquivos

        $this->documentos_pedido_list = new TQuickGrid;
        $this->documentos_pedido_list->disableHtmlConversion();
        $this->documentos_pedido_list->style = 'width:100%';
        $this->documentos_pedido_list->disableDefaultClick();

        $column_nomeseguimento = $this->documentos_pedido_list->addQuickColumn("Descrição", 'caminho', 'left');

        $this->documentos_pedido_list->createModel();

        $criteria_documentos_pedido = new TCriteria();
        $criteria_documentos_pedido->add(new TFilter('pedido_id', '=', $pedido->id));

        $criteria_documentos_pedido->setProperty('order', 'id desc');

        $detalhes_documentos_pedido = DocumentosPedido::getObjects($criteria_documentos_pedido);

        $this->documentos_pedido_list->addItems($detalhes_documentos_pedido);

        $icon4 = new TImage('fa:file #2196F3');
        $title4 = new TTextDisplay("{$icon4} ARQUIVOS", '#333', '16px', '{$fontStyle}');

        $panel3 = new TPanelGroup($title4, '#f5f5f5');
        $panel3->class = 'panel panel-default formView-detail';
        $panel3->add(new BootstrapDatagridWrapper($this->documentos_pedido_list));

        $tab_66adfb0d6cf63->appendPage("Arquivos");
        $row7 = $tab_66adfb0d6cf63->addFields([$panel3]);
        $row7->layout = [' col-sm-12'];

        $row8 = $this->form->addFields([$linha_do_tempo]);
        $row8->layout = [' col-sm-12'];

        $btnPedidoVendaDocumentFornOnGenerateAction = new TAction(['PedidoVendaDocumentForn', 'onGenerate'],['key'=>$pedido->id]);
        $btnPedidoVendaDocumentFornOnGenerateLabel = new TLabel("Documento");

        $btnPedidoVendaDocumentFornOnGenerate = $this->form->addHeaderAction($btnPedidoVendaDocumentFornOnGenerateLabel, $btnPedidoVendaDocumentFornOnGenerateAction, 'far:file-pdf #9C27B0'); 
        $btnPedidoVendaDocumentFornOnGenerateLabel->setFontSize('12px'); 
        $btnPedidoVendaDocumentFornOnGenerateLabel->setFontColor('#333'); 

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        TTransaction::close();
        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=PedidoVendaFormViewForn]');
        $style->width = '90% !important';   
        $style->show(true);

    }

    public function onShow($param = null)
    {     

    }

}

