<?php

class FrenteCaixaCartaoLancamentoFormView extends TPage
{
    protected $form;

    private static $database = 'minierp';
    private static $formName = 'formView_FrenteCaixaCartaoLancamento';

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

            $launchKey = $param['launch_key'] ?? $param['key'] ?? null;
            if (empty($launchKey))
            {
                throw new Exception('Lancamento do cartao nao informado para visualizacao.');
            }

            $lancamento = CartaoLancamentoService::obterLancamentoSessao($launchKey);
            $pedido = CartaoLancamentoService::obterPedidoImportadoPorLaunchKey($launchKey);

            $this->form = new BootstrapFormBuilder(self::$formName);
            $this->form->setTagName('div');
            $this->form->setFormTitle('Consulta de Lancamento de Cartao');

            $status = new TElement('span');
            $status->class = 'label label-default';
            $status->style = 'background:#198754;color:#fff;padding:8px 12px;border-radius:999px;display:inline-block;';
            $status->add(trim((string) ($lancamento['tp_status'] ?? 'Nao informado')));

            $labelPedido = new TLabel('Pedido gerado', '', '14px', 'B', '100%');
            $textPedido = new TTextDisplay($pedido->id ?? 'Nao gerado', '', '16px', '');
            $labelData = new TLabel('Data/Hora', '', '14px', 'B', '100%');
            $textData = new TTextDisplay($this->formatarData($lancamento['dt_hora_autoriz'] ?? ''), '', '16px', '');
            $labelAutoriz = new TLabel('Autorizacao', '', '14px', 'B', '100%');
            $textAutoriz = new TTextDisplay($lancamento['cd_autoriz'] ?? '', '', '16px', '');

            $labelCartao = new TLabel('Cartao', '', '14px', 'B', '100%');
            $textCartao = new TTextDisplay($lancamento['numero_cartao'] ?? '', '', '16px', '');
            $labelUsuario = new TLabel('Usuario do cartao', '', '14px', 'B', '100%');
            $textUsuario = new TTextDisplay($lancamento['usuario_cartao_nome'] ?? '', '', '16px', '');
            $labelDocumento = new TLabel('CPF usuario', '', '14px', 'B', '100%');
            $textDocumento = new TTextDisplay($lancamento['usuario_cartao_documento'] ?? '', '', '16px', '');

            $labelVeiculo = new TLabel('Veiculo', '', '14px', 'B', '100%');
            $textVeiculo = new TTextDisplay($lancamento['veiculo_descricao'] ?? '', '', '16px', '');
            $labelEstabelecimento = new TLabel('Estabelecimento', '', '14px', 'B', '100%');
            $textEstabelecimento = new TTextDisplay($lancamento['estabelecimento_nome'] ?? 'Nao localizado automaticamente', '', '16px', '');
            $labelLoja = new TLabel('Loja da API', '', '14px', 'B', '100%');
            $textLoja = new TTextDisplay($lancamento['nm_loja'] ?? '', '', '16px', '');

            $labelValor = new TLabel('Valor total', '', '14px', 'B', '100%');
            $textValor = new TTextDisplay('R$ ' . number_format((float) ($lancamento['valor_total'] ?? 0), 2, ',', '.'), '', '16px', '');
            $labelSaldoAtual = new TLabel('Saldo atual', '', '14px', 'B', '100%');
            $textSaldoAtual = new TTextDisplay('R$ ' . number_format((float) ($lancamento['saldo_atual'] ?? 0), 2, ',', '.'), '', '16px', '');
            $labelSaldoLimite = new TLabel('Limite cartao', '', '14px', 'B', '100%');
            $textSaldoLimite = new TTextDisplay('R$ ' . number_format((float) ($lancamento['saldo_limite'] ?? 0), 2, ',', '.'), '', '16px', '');

            $labelObs = new TLabel('Observacao do pedido', '', '14px', 'B', '100%');
            $textObs = new TTextDisplay($pedido->obs ?? 'Pedido ainda nao gerado para este lancamento.', '', '16px', '');

            $row1 = $this->form->addFields([$labelPedido, $textPedido], [$labelData, $textData], [$labelAutoriz, $textAutoriz], [new TLabel('Status API', '', '14px', 'B', '100%'), $status]);
            $row1->layout = ['col-sm-2', 'col-sm-3', 'col-sm-3', 'col-sm-4'];

            $row2 = $this->form->addFields([$labelCartao, $textCartao], [$labelUsuario, $textUsuario], [$labelDocumento, $textDocumento]);
            $row2->layout = ['col-sm-3', 'col-sm-5', 'col-sm-4'];

            $row3 = $this->form->addFields([$labelVeiculo, $textVeiculo], [$labelEstabelecimento, $textEstabelecimento]);
            $row3->layout = ['col-sm-6', 'col-sm-6'];

            $row4 = $this->form->addFields([$labelLoja, $textLoja], [$labelValor, $textValor], [$labelSaldoAtual, $textSaldoAtual], [$labelSaldoLimite, $textSaldoLimite]);
            $row4->layout = ['col-sm-5', 'col-sm-2', 'col-sm-2', 'col-sm-3'];

            $row5 = $this->form->addFields([$labelObs, $textObs]);
            $row5->layout = ['col-sm-12'];

            if ($pedido && !empty($pedido->id))
            {
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

                $panel = new TPanelGroup('Itens do pedido gerado', '#f5f5f5');
                $panel->class = 'panel panel-default formView-detail';
                $panel->add(new BootstrapDatagridWrapper($grid));

                $row6 = $this->form->addFields([$panel]);
                $row6->layout = ['col-sm-12'];
            }

            // if ($pedido && !empty($pedido->id))
            // {
            //     $openPedido = new TAction(['FrenteCaixaCartaoLancamentoForm', 'onShow'], ['launch_key' => $launchKey]);
            //     $openPedidoLabel = new TLabel('Abrir pedido gerado');
            //     $openPedidoLabel->setFontSize('12px');
            //     $openPedidoLabel->setFontColor('#333');
            //     $this->form->addHeaderAction($openPedidoLabel, $openPedido, 'fas:external-link-alt #1565C0');
            // }

            parent::setTargetContainer('adianti_right_panel');

            $btnClose = new TButton('closeCurtain');
            $btnClose->class = 'btn btn-sm btn-default';
            $btnClose->style = 'margin-right:10px;';
            $btnClose->onClick = 'Template.closeRightPanel();';
            $btnClose->setLabel('Fechar');
            $btnClose->setImage('fas:times');
            $this->form->addHeaderWidget($btnClose);

            parent::add($this->form);

            $style = new TStyle('right-panel > .container-part[page-name=FrenteCaixaCartaoLancamentoFormView]');
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

    private function formatarData(?string $dataHora): string
    {
        $dataHora = trim((string) $dataHora);
        if ($dataHora === '')
        {
            return '';
        }

        $formatos = ['d/m/Y H:i:s', 'd/m/Y H:i', 'Y-m-d H:i:s', 'Y-m-d H:i'];
        foreach ($formatos as $formato)
        {
            $data = DateTime::createFromFormat($formato, $dataHora);
            if ($data instanceof DateTime)
            {
                return $data->format('d/m/Y H:i');
            }
        }

        try
        {
            return (new DateTime($dataHora))->format('d/m/Y H:i');
        }
        catch (Exception $e)
        {
            return $dataHora;
        }
    }

    public function onShow($param = null)
    {
    }
}
