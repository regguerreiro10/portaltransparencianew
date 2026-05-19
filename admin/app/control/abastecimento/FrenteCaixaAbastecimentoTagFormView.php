<?php

class FrenteCaixaAbastecimentoTagFormView extends TPage
{
    protected $form;

    private static $database = 'minierp';
    private static $formName = 'formView_FrenteCaixaAbastecimentoTag';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        try
        {
            TTransaction::open(self::$database);

            $id = $param['id'] ?? $param['key'] ?? null;
            if (empty($id))
            {
                throw new Exception('Pedido nÃ£o informado para visualizaÃ§Ã£o.');
            }

            $pedido = new PedidoFrotas($id);
            $this->form = new BootstrapFormBuilder(self::$formName);
            $this->form->setTagName('div');
            $this->form->setFormTitle('Consulta de Abastecimento TAG');

            $statusLabel = $pedido->estado_pedido_frotas->nome ?? '';
            $status = new TElement('span');
            $status->class = 'label label-default';
            $status->style = 'background:#198754;color:#fff;padding:8px 12px;border-radius:999px;display:inline-block;';
            $status->add($statusLabel);

            $labelId = new TLabel('Pedido', '', '14px', 'B', '100%');
            $textId = new TTextDisplay($pedido->id, '', '16px', '');
            $labelData = new TLabel('Data/Hora', '', '14px', 'B', '100%');
            $textData = new TTextDisplay(!empty($pedido->dt_pedido) ? (new DateTime($pedido->dt_pedido))->format('d/m/Y H:i') : '', '', '16px', '');
            $labelVeiculo = new TLabel('Veiculo', '', '14px', 'B', '100%');
            $textVeiculo = new TTextDisplay(trim(($pedido->veiculos->placa ?? '') . ' - ' . ($pedido->veiculos->marca->descricao ?? '') . ' - ' . ($pedido->veiculos->modelo->descricao ?? '')), '', '16px', '');
            $labelFornecedor = new TLabel('Estabelecimento', '', '14px', 'B', '100%');
            $textFornecedor = new TTextDisplay($pedido->estabelecimento->nome ?? '', '', '16px', '');
            $labelValor = new TLabel('Valor total', '', '14px', 'B', '100%');
            $textValor = new TTextDisplay('R$ ' . number_format((float) ($pedido->valor_total ?? 0), 2, ',', '.'), '', '16px', '');
            $labelKm = new TLabel('KM/Hodometro', '', '14px', 'B', '100%');
            $textKm = new TTextDisplay((string) ($pedido->km ?? ''), '', '16px', '');
            $labelDescricao = new TLabel('Descricao', '', '14px', 'B', '100%');
            $textDescricao = new TTextDisplay($pedido->descricaopedido ?? '', '', '16px', '');
            $labelObs = new TLabel('Observacao', '', '14px', 'B', '100%');
            $textObs = new TTextDisplay($pedido->obs ?? '', '', '16px', '');

            $row1 = $this->form->addFields([$labelId, $textId], [$labelData, $textData], [new TLabel('Status', '', '14px', 'B', '100%'), $status]);
            $row1->layout = ['col-sm-2', 'col-sm-5', 'col-sm-5'];

            $row2 = $this->form->addFields([$labelVeiculo, $textVeiculo], [$labelFornecedor, $textFornecedor]);
            $row2->layout = ['col-sm-6', 'col-sm-6'];

            $row3 = $this->form->addFields([$labelValor, $textValor], [$labelKm, $textKm]);
            $row3->layout = ['col-sm-6', 'col-sm-6'];

            $row4 = $this->form->addFields([$labelDescricao, $textDescricao]);
            $row4->layout = ['col-sm-12'];

            $row5 = $this->form->addFields([$labelObs, $textObs]);
            $row5->layout = ['col-sm-12'];

            $grid = new TQuickGrid;
            $grid->disableHtmlConversion();
            $grid->style = 'width:100%';
            $grid->disableDefaultClick();

            $colTipo = $grid->addQuickColumn('Tipo', 'tipo', 'center', '10%');
            $colDescricao = $grid->addQuickColumn('Descricao', 'descricao', 'left', '42%');
            $colQtde = $grid->addQuickColumn('Quantidade', 'qtde', 'right', '16%');
            $colValorUnitario = $grid->addQuickColumn('Valor unitario', 'valor_unitario', 'right', '16%');
            $colValorTotal = $grid->addQuickColumn('Valor total', 'valor_total', 'right', '16%');

            $colTipo->setTransformer(function ($value) {
                return ((int) $value === 1) ? 'Produto' : 'Servico';
            });

            $formatMoney = function ($value) {
                return 'R$ ' . number_format((float) ($value ?? 0), 2, ',', '.');
            };

            $colValorUnitario->setTransformer($formatMoney);
            $colValorTotal->setTransformer($formatMoney);

            $grid->createModel();

            $criteriaItens = new TCriteria;
            $criteriaItens->add(new TFilter('pedido_frotas_id', '=', $pedido->id));
            $criteriaItens->setProperty('order', 'id asc');
            $itens = ItensPedidoFrotas::getObjects($criteriaItens);
            if ($itens)
            {
                $grid->addItems($itens);
            }

            $panel = new TPanelGroup('Itens do abastecimento', '#f5f5f5');
            $panel->class = 'panel panel-default formView-detail';
            $panel->add(new BootstrapDatagridWrapper($grid));

            $row6 = $this->form->addFields([$panel]);
            $row6->layout = ['col-sm-12'];

            $printAction = new TAction(['FrenteCaixaAbastecimentoTagTicket', 'onShow'], ['id' => $pedido->id]);
            $printLabel = new TLabel('Imprimir ticket');
            $printLabel->setFontSize('12px');
            $printLabel->setFontColor('#333');
            $this->form->addHeaderAction($printLabel, $printAction, 'fas:print #1565C0');

            parent::setTargetContainer('adianti_right_panel');

            $btnClose = new TButton('closeCurtain');
            $btnClose->class = 'btn btn-sm btn-default';
            $btnClose->style = 'margin-right:10px;';
            $btnClose->onClick = 'Template.closeRightPanel();';
            $btnClose->setLabel('Fechar');
            $btnClose->setImage('fas:times');
            $this->form->addHeaderWidget($btnClose);

            parent::add($this->form);

            $style = new TStyle('right-panel > .container-part[page-name=FrenteCaixaAbastecimentoTagFormView]');
            $style->width = '90% !important';
            $style->show(true);

            TTransaction::close();
        }
        catch (Exception $e)
        {
            if (TTransaction::get())
            {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {
    }
}
