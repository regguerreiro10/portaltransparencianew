<?php

class FrenteCaixaAbastecimentoTagList extends TPage
{
    
    use BuilderDatagridTrait;
private $form;
    private $datagrid;
    private $loaded;
    private $toolbar;
    private $pedidoTemCupomMap = [];
    private $limit = 20;

    private static $database = 'minierp';
    private static $activeRecord = 'PedidoFrotas';
    private static $formName = 'form_FrenteCaixaAbastecimentoTagList';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $criteria_estabelecimento = new TCriteria();
        $criteria_estabelecimento->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE deleted_at is null AND grupo_pessoa_id = '" . GrupoPessoa::FORNECEDOR . "')"));

        $criteria_status = new TCriteria();
        $criteria_veiculo = new TCriteria();
        if (TSession::getValue('idunit'))
        {
            $criteria_veiculo->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Abastecimentos TAG');

        $id = new TEntry('id');
        $dt_pedido = new BDateRange('dt_pedido', 'dt_pedido_final');
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa} - {modelo->descricao}', 'placa asc', $criteria_veiculo);
        $estabelecimento_id = new TDBCombo('estabelecimento_id', 'minierp', 'Pessoa', 'id', '{nome} - {documento}', 'nome asc', $criteria_estabelecimento);
        $estado_pedido_frotas_id = new TDBCombo('estado_pedido_frotas_id', 'minierp', 'EstadoPedidoFrotas', 'id', '{nome}', 'ordem asc', $criteria_status);
        $descricaopedido = new TEntry('descricaopedido');

        $dt_pedido->setMask('dd/mm/yyyy');
        $dt_pedido->setDatabaseMask('yyyy-mm-dd');

        $id->setProperty('placeholder', 'Codigo do pedido');
        $descricaopedido->setProperty('placeholder', 'Descricao, placa ou observacao');

        $veiculos_id->enableSearch();
        $estabelecimento_id->enableSearch();
        $estado_pedido_frotas_id->enableSearch();

        $id->setSize('100%');
        $dt_pedido->setSize(220);
        $veiculos_id->setSize('100%');
        $estabelecimento_id->setSize('100%');
        $estado_pedido_frotas_id->setSize('100%');
        $descricaopedido->setSize('100%');

        $row1 = $this->form->addFields(
            [new TLabel('Pedido', null, '14px', null, '100%'), $id],
            [new TLabel('Periodo', null, '14px', null, '100%'), $dt_pedido]
        );
        $row1->layout = ['col-sm-3', 'col-sm-9'];

        $row2 = $this->form->addFields(
            [new TLabel('Veiculo', null, '14px', null, '100%'), $veiculos_id],
            [new TLabel('Estabelecimento', null, '14px', null, '100%'), $estabelecimento_id]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $row3 = $this->form->addFields(
            [new TLabel('Status', null, '14px', null, '100%'), $estado_pedido_frotas_id],
            [new TLabel('Descricao', null, '14px', null, '100%'), $descricaopedido]
        );
        $row3->layout = ['col-sm-4', 'col-sm-8'];

        $observacao = new TElement('div');
        $observacao->style = 'margin:10px 0 0 0;padding:12px;border:1px solid #e5e7eb;background:#f8fafc;color:#334155;border-radius:6px;';
        $observacao->add('Use os filtros para localizar abastecimentos TAG. Se nenhum filtro for informado, a tela mostra todos os registros.');
        $this->form->addContent([$observacao]);

        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $btnFilter = $this->form->addAction('Filtrar', new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $btnFilter->addStyleClass('btn-primary');

        $btnClear = $this->form->addAction('Limpar filtros', new TAction([$this, 'onClearFilters']), 'fas:eraser #dd5a43');

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction([__CLASS__, 'onShowCurtainFilters']), 'Filtros');
        $btnShowCurtainFilters->addStyleClass('btn btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');
        $this->form->addField($btnShowCurtainFilters);

        $buttonLimparFiltros = new TButton('button_button_limpar_filtros');
        $buttonLimparFiltros->setAction(new TAction([$this, 'onClearFilters']), 'Limpar filtros');
        $buttonLimparFiltros->addStyleClass('btn btn-default');
        $buttonLimparFiltros->setImage('fas:eraser #f44336');
        $this->form->addField($buttonLimparFiltros);

        $buttonAtualizar = new TButton('button_button_atualizar');
        $buttonAtualizar->setAction(new TAction([$this, 'onRefresh']), 'Atualizar');
        $buttonAtualizar->addStyleClass('btn btn-default');
        $buttonAtualizar->setImage('fas:sync-alt #03a9f4');
        $this->form->addField($buttonAtualizar);

        $buttonNovo = new TButton('button_button_novo');
        $buttonNovo->setAction(new TAction(['FrenteCaixaAbastecimentoTagForm', 'onShow']), 'Novo abastecimento TAG');
        $buttonNovo->addStyleClass('btn btn-primary');
        $buttonNovo->setImage('fas:plus #ffffff');
        $this->form->addField($buttonNovo);

        $dropdownExportar = new TDropDown('Exportar', 'fas:file-export #2d3436');
        $dropdownExportar->setPullSide('right');
        $dropdownExportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdownExportar->addPostAction('PDF', new TAction([$this, 'onExportPdf'], ['static' => 1]), self::$formName, 'far:file-pdf #e74c3c');
        $dropdownExportar->addPostAction('XLS', new TAction([$this, 'onExportXls'], ['static' => 1]), self::$formName, 'fas:file-excel #4CAF50');
        $dropdownExportar->addPostAction('HTML', new TAction([$this, 'onExportHtml'], ['static' => 1]), self::$formName, 'fab:html5 #E34F26');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(420);

        $columnId = new TDataGridColumn('id', 'Pedido', 'center', '8%');
        $columnData = new TDataGridColumn('dt_pedido', 'Data/Hora', 'center', '16%');
        $columnVeiculo = new TDataGridColumn('veiculos->placa', 'Veiculo', 'left', '12%');
        $columnDescricao = new TDataGridColumn('descricaopedido', 'Descricao', 'left', '28%');
        $columnEstabelecimento = new TDataGridColumn('estabelecimento->nome', 'Estabelecimento', 'left', '20%');
        $columnValor = new TDataGridColumn('valor_total', 'Valor total', 'right', '10%');
        $columnStatus = new TDataGridColumn('estado_pedido_frotas_id', 'Status', 'center', '14%');

        $orderId = new TAction([$this, 'onReload']);
        $orderId->setParameter('order', 'id');
        $columnId->setAction($orderId);

        $columnData->setTransformer(function ($value) {
            if (empty($value))
            {
                return '';
            }

            try
            {
                return (new DateTime($value))->format('d/m/Y H:i');
            }
            catch (Exception $e)
            {
                return $value;
            }
        });

        $columnValor->setTransformer(function ($value) {
            return 'R$ ' . number_format((float) ($value ?? 0), 2, ',', '.');
        });

        $columnStatus->setTransformer(function ($value, $object) {
            $nome = $object->estado_pedido_frotas->nome ?? $object->estado_pedido_frotas->descricao ?? 'Sem status';
            $temCupom = !empty($this->pedidoTemCupomMap[(int) $object->id]);

            if ($temCupom && (int) $object->estado_pedido_frotas_id === (int) EstadoPedidoFrotas::FINALIZADO)
            {
                $nome .= " <i class='fa fa-paperclip' aria-hidden='true'></i>";
            }

            return "<span class='label label-default' style='min-width:160px;display:inline-block;background:#198754;color:#fff;border-radius:999px;padding:6px 10px;'>{$nome}</span>";
        });

        $this->datagrid->addColumn($columnId);
        $this->datagrid->addColumn($columnData);
        $this->datagrid->addColumn($columnVeiculo);
        $this->datagrid->addColumn($columnDescricao);
        $this->datagrid->addColumn($columnEstabelecimento);
        $this->datagrid->addColumn($columnValor);
        $this->datagrid->addColumn($columnStatus);

        $actionView = new TDataGridAction(['FrenteCaixaAbastecimentoTagFormView', 'onShow'], ['id' => '{id}']);
        $actionView->setLabel('Visualizar abastecimento');
        $actionView->setImage('fas:search-plus #673AB7');

        $actionDocumentos = new TDataGridAction(['FrenteCaixaAbastecimentoTagDocumentoList', 'onSetProject'], ['id' => '{id}']);
        $actionDocumentos->setLabel('Anexar cupom / nota fiscal');
        $actionDocumentos->setImage('fas:paperclip #795548');

        $actionGroup = new TDataGridActionGroup('Clique Ações', 'fa:th red');
        $actionGroup->addAction($actionView);
        $actionGroup->addAction($actionDocumentos);
        $this->datagrid->addActionGroup($actionGroup);

        $this->applyDatagridProperties();

        $this->datagrid->createModel();

        $panel = new TPanelGroup('Abastecimentos TAG');
        $panel->add($this->datagrid);
        $panel->getBody()->class .= ' table-responsive';

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';

        $headLeftActions = new TElement('div');
        $headLeftActions->class = ' datagrid-header-actions-left-actions ';

        $headRightActions = new TElement('div');
        $headRightActions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($headLeftActions);
        $headerActions->add($headRightActions);

        $headLeftActions->add($btnShowCurtainFilters);
        $headLeftActions->add($buttonLimparFiltros);
        $headLeftActions->add($buttonAtualizar);
        $headLeftActions->add($buttonNovo);
        $headRightActions->add($dropdownExportar);

        $panel->getBody()->insert(0, $headerActions);

        $container = new TVBox;
        $container->style = 'width:100%';
        $container->add($panel);

        parent::add($container);
    }

    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $this->form->setData($data);
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        TScript::create('Template.closeRightPanel();');
        $this->loaded = false;
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onClearFilters($param = null)
    {
        TSession::setValue(__CLASS__ . '_filter_data', null);

        $this->form->clear(true);
        $data = new stdClass();
        $this->form->setData($data);

        $this->loaded = false;
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onRefresh($param = null)
    {
        $this->loaded = false;
        $this->onReload([]);
    }

    private function getBaseCriteria(array $param = [], ?int $limit = null): TCriteria
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('deleted_at', 'is', null));
        $criteria->add(new TFilter('abastecimento', '=', 1));

        $data = TSession::getValue(__CLASS__ . '_filter_data');

        if ($data)
        {
            if (!empty($data->id))
            {
                $criteria->add(new TFilter('id', '=', (int) $data->id));
            }

            if (!empty($data->dt_pedido))
            {
                $criteria->add(new TFilter('dt_pedido', '>=', $data->dt_pedido . ' 00:00:00'));
            }

            if (!empty($data->dt_pedido_final))
            {
                $criteria->add(new TFilter('dt_pedido', '<=', $data->dt_pedido_final . ' 23:59:59'));
            }

            if (!empty($data->veiculos_id))
            {
                $criteria->add(new TFilter('veiculos_id', '=', $data->veiculos_id));
            }

            if (!empty($data->estabelecimento_id))
            {
                $criteria->add(new TFilter('estabelecimento_id', '=', $data->estabelecimento_id));
            }

            if (!empty($data->estado_pedido_frotas_id))
            {
                $criteria->add(new TFilter('estado_pedido_frotas_id', '=', $data->estado_pedido_frotas_id));
            }

            if (!empty($data->descricaopedido))
            {
                $criteria->add(new TFilter('descricaopedido', 'like', '%' . trim($data->descricaopedido) . '%'));
            }
        }

        if (TSession::getValue('idunit'))
        {
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
        }

        $order = $param['order'] ?? 'id';
        $direction = $param['direction'] ?? 'desc';
        $criteria->setProperty('order', $order);
        $criteria->setProperty('direction', $direction);

        if ($limit !== null)
        {
            $criteria->setProperty('limit', $limit);
        }

        return $criteria;
    }

    public static function onShowCurtainFilters($param = null)
    {
        try
        {
            $filter = new self([]);

            $btnClose = new TButton('closeCurtain');
            $btnClose->class = 'btn btn-sm btn-default';
            $btnClose->style = 'margin-right:10px;';
            $btnClose->onClick = 'Template.closeRightPanel();';
            $btnClose->setLabel('Fechar');
            $btnClose->setImage('fas:times');

            $filter->form->addHeaderWidget($btnClose);

            $page = new TPage();
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('page-name', 'FrenteCaixaAbastecimentoTagListSearch');
            $page->setProperty('page_name', 'FrenteCaixaAbastecimentoTagListSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onReload($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $repository = new TRepository(self::$activeRecord);
            $criteria = $this->getBaseCriteria((array) $param);
            $criteria->setProperty('limit', $this->limit);

            $items = $repository->load($criteria, false);

            $this->pedidoTemCupomMap = [];
            if ($items)
            {
                $pedidoIds = array_map(function ($item) {
                    return (int) $item->id;
                }, $items);

                if (!empty($pedidoIds))
                {
                    $criteriaPropostas = new TCriteria;
                    $criteriaPropostas->add(new TFilter('deleted_at', 'is', null));
                    $criteriaPropostas->add(new TFilter('pedido_frotas_id', 'in', $pedidoIds));

                    $propostas = (new TRepository('Propostas'))->load($criteriaPropostas, false);
                    $propostaPedidoMap = [];
                    $propostaIds = [];

                    if ($propostas)
                    {
                        foreach ($propostas as $proposta)
                        {
                            $propostaPedidoMap[(int) $proposta->id] = (int) $proposta->pedido_frotas_id;
                            $propostaIds[] = (int) $proposta->id;
                        }
                    }

                    if (!empty($propostaIds))
                    {
                        $criteriaDocs = new TCriteria;
                        $criteriaDocs->add(new TFilter('deleted_at', 'is', null));
                        $criteriaDocs->add(new TFilter('propostas_id', 'in', $propostaIds));

                        $documentos = (new TRepository('DocumentosPropostas'))->load($criteriaDocs, false);
                        if ($documentos)
                        {
                            foreach ($documentos as $documento)
                            {
                                $pedidoId = $propostaPedidoMap[(int) $documento->propostas_id] ?? null;
                                if ($pedidoId)
                                {
                                    $this->pedidoTemCupomMap[(int) $pedidoId] = true;
                                }
                            }
                        }
                    }
                }
            }

            $this->datagrid->clear();
            if ($items)
            {
                foreach ($items as $item)
                {
                    $this->datagrid->addItem($item);
                }
            }

            TTransaction::close();
            $this->loaded = true;
            return $items;
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onExportXls($param = null)
    {
        try
        {
            $output = 'app/output/' . uniqid() . '.xls';

            if ((!file_exists($output) && is_writable(dirname($output))) || is_writable($output))
            {
                $widths = [];
                $titles = [];

                foreach ($this->datagrid->getColumns() as $column)
                {
                    $titles[] = $column->getLabel();
                    $width = 100;

                    if (is_null($column->getWidth()))
                    {
                        $width = 100;
                    }
                    else if (strpos((string) $column->getWidth(), '%') !== false)
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
                $table->addStyle('title', 'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                $table->addStyle('data', 'Helvetica', '10', '', '#000000', '#FFFFFF', 'LR');

                $table->addRow();
                foreach ($titles as $title)
                {
                    $table->addCell($title, 'center', 'title');
                }

                TTransaction::open(self::$database);
                $objects = (new TRepository(self::$activeRecord))->load($this->getBaseCriteria((array) $param), false);

                if ($objects)
                {
                    $this->pedidoTemCupomMap = $this->buildPedidoTemCupomMap($objects);

                    foreach ($objects as $object)
                    {
                        $table->addRow();
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $columnName = $column->getName();
                            $value = '';

                            if (isset($object->$columnName))
                            {
                                $value = is_scalar($object->$columnName) ? $object->$columnName : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $columnName = (strpos((string) $columnName, '{') === false) ? ('{' . $columnName . '}') : $columnName;
                                $value = $object->render($columnName);
                            }

                            $transformer = $column->getTransformer();
                            if ($transformer)
                            {
                                $value = strip_tags((string) call_user_func($transformer, $value, $object, null));
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
        catch (Exception $e)
        {
            if (TTransaction::get())
            {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());
        }
    }

    public function onExportPdf($param = null)
    {
        try
        {
            $output = 'app/output/' . uniqid() . '.pdf';

            if ((!file_exists($output) && is_writable(dirname($output))) || is_writable($output))
            {
                $this->datagrid->prepareForPrinting();
                $this->loaded = false;
                $this->limit = 0;
                $this->onReload((array) $param);

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . file_get_contents('app/resources/styles-print-list.html') . PdfListHeader::render(__CLASS__) . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('iframe');
                $object->src = $output;
                $object->type = 'application/pdf';
                $object->style = 'width: 100%; height:calc(100% - 10px)';
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
        finally
        {
            $this->limit = 20;
            $this->loaded = false;
        }
    }

    public function onExportHtml($param = null)
    {
        try
        {
            $output = 'app/output/' . uniqid() . '.html';

            if ((!file_exists($output) && is_writable(dirname($output))) || is_writable($output))
            {
                $this->datagrid->prepareForPrinting();
                $this->loaded = false;
                $this->limit = 0;
                $this->onReload((array) $param);

                $html = clone $this->datagrid;
                $css = file_get_contents('app/resources/styles-print.html');

                $contents = "<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Relatorio</title>
{$css}
</head>
<body>
{$html->getContents()}
</body>
</html>";

                file_put_contents($output, $contents);
                TScript::create("window.open('{$output}', '_blank');");
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
        finally
        {
            $this->limit = 20;
            $this->loaded = false;
        }
    }

    private function buildPedidoTemCupomMap(array $items): array
    {
        $map = [];
        $pedidoIds = array_map(function ($item) {
            return (int) $item->id;
        }, $items);

        if (empty($pedidoIds))
        {
            return $map;
        }

        $criteriaPropostas = new TCriteria;
        $criteriaPropostas->add(new TFilter('deleted_at', 'is', null));
        $criteriaPropostas->add(new TFilter('pedido_frotas_id', 'in', $pedidoIds));

        $propostas = (new TRepository('Propostas'))->load($criteriaPropostas, false);
        $propostaPedidoMap = [];
        $propostaIds = [];

        if ($propostas)
        {
            foreach ($propostas as $proposta)
            {
                $propostaPedidoMap[(int) $proposta->id] = (int) $proposta->pedido_frotas_id;
                $propostaIds[] = (int) $proposta->id;
            }
        }

        if (empty($propostaIds))
        {
            return $map;
        }

        $criteriaDocs = new TCriteria;
        $criteriaDocs->add(new TFilter('deleted_at', 'is', null));
        $criteriaDocs->add(new TFilter('propostas_id', 'in', $propostaIds));

        $documentos = (new TRepository('DocumentosPropostas'))->load($criteriaDocs, false);
        if ($documentos)
        {
            foreach ($documentos as $documento)
            {
                $pedidoId = $propostaPedidoMap[(int) $documento->propostas_id] ?? null;
                if ($pedidoId)
                {
                    $map[(int) $pedidoId] = true;
                }
            }
        }

        return $map;
    }

    public function onShow($param = null)
    {
        if (!$this->loaded)
        {
            $this->onReload($param);
        }
    }
}
