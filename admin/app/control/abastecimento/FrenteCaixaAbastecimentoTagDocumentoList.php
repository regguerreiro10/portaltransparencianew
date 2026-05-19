<?php

class FrenteCaixaAbastecimentoTagDocumentoList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];

    private static $database = 'minierp';
    private static $activeRecord = 'DocumentosPropostas';
    private static $formName = 'form_FrenteCaixaAbastecimentoTagDocumentoList';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->style = 'width:100%';
        $this->datagrid->setHeight(360);

        $columnId = new TDataGridColumn('id', 'Id', 'center', '8%');
        $columnNumero = new TDataGridColumn('numero', 'Numero documento', 'left', '16%');
        $columnTipo = new TDataGridColumn('tipo_documentos_propostas->descricao', 'Tipo', 'left', '18%');
        $columnValor = new TDataGridColumn('valor', 'Valor', 'right', '12%');
        $columnPreview = new TDataGridColumn('caminho', 'Imagem', 'center', '16%');
        $columnArquivo = new TDataGridColumn('caminho', 'Arquivo', 'left', '30%');
        $columnData = new TDataGridColumn('created_at', 'Data anexo', 'center', '16%');

        $columnValor->setTransformer(function ($value) {
            return 'R$ ' . number_format((float) ($value ?? 0), 2, ',', '.');
        });

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

        $columnPreview->setTransformer([$this, 'transformPreview']);
        $columnArquivo->setTransformer([$this, 'transformArquivo']);

        $this->datagrid->addColumn($columnId);
        $this->datagrid->addColumn($columnNumero);
        $this->datagrid->addColumn($columnTipo);
        $this->datagrid->addColumn($columnValor);
        $this->datagrid->addColumn($columnPreview);
        $this->datagrid->addColumn($columnArquivo);
        $this->datagrid->addColumn($columnData);

        $actionEdit = new TDataGridAction(['FrenteCaixaAbastecimentoTagDocumentoForm', 'onEdit'], ['key' => '{id}']);
        $actionEdit->setLabel('Editar anexo');
        $actionEdit->setImage('far:edit #478fca');

        $actionDelete = new TDataGridAction([$this, 'onDelete'], ['key' => '{id}']);
        $actionDelete->setLabel('Excluir anexo');
        $actionDelete->setImage('fas:trash-alt #dd5a43');

        $this->datagrid->addAction($actionEdit);
        $this->datagrid->addAction($actionDelete);
        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Cupom fiscal / nota fiscal do abastecimento');
        $panel->addHeaderActionLink('Anexar documento', new TAction(['FrenteCaixaAbastecimentoTagDocumentoForm', 'onShow']), 'fas:paperclip #ffffff', 'btn btn-sm btn-primary');
        $panel->addHeaderActionLink('Imprimir', new TAction([$this, 'onPrint']), 'fas:print #ffffff', 'btn btn-sm btn-info');
        $panel->addHeaderActionLink('Voltar', new TAction(['FrenteCaixaAbastecimentoTagList', 'onShow']), 'fas:arrow-left #000000', 'btn btn-sm btn-default');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        $panel->getBody()->class .= ' table-responsive';

        $container = new TVBox;
        $container->style = 'width:100%';
        $container->add($panel);

        parent::add($container);
    }

    public static function onSetProject($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $pedidoId = (int) ($param['id'] ?? 0);
            if ($pedidoId <= 0)
            {
                throw new Exception('Pedido de abastecimento nao informado.');
            }

            $propostaId = self::obterPropostaIdPorPedido($pedidoId);
            if (!$propostaId)
            {
                throw new Exception('Nenhuma proposta vinculada ao abastecimento foi encontrada para anexar o cupom fiscal.');
            }

            TSession::setValue(__CLASS__ . '_pedido_id', $pedidoId);
            TSession::setValue(__CLASS__ . '_proposta_id', $propostaId);

            TTransaction::close();

            TApplication::loadPage(__CLASS__, 'onShow');
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

    public function onReload($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $propostaId = (int) TSession::getValue(__CLASS__ . '_proposta_id');
            if ($propostaId <= 0)
            {
                throw new Exception('Proposta do abastecimento nao localizada.');
            }

            $criteria = new TCriteria;
            $criteria->add(new TFilter('deleted_at', 'is', null));
            $criteria->add(new TFilter('propostas_id', '=', $propostaId));

            if (empty($param['order']))
            {
                $param['order'] = 'id';
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param);
            $criteria->setProperty('limit', $this->limit);

            $repository = new TRepository(self::$activeRecord);
            $objects = $repository->load($criteria, false);

            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }

            $criteria->resetProperties();
            $count = $repository->count($criteria);
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($this->limit);

            TTransaction::close();
            $this->loaded = true;

            return $objects;
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

    public function onDelete($param = null)
    {
        if (isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                $key = $param['key'];

                TTransaction::open(self::$database);

                $documento = new DocumentosPropostas($key, false);
                $documento->delete();

                TTransaction::close();

                $this->onReload($param);
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        }
        else
        {
            $action = new TAction([$this, 'onDelete']);
            $action->setParameters($param);
            $action->setParameter('delete', 1);
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
        }
    }

    public function onShow($param = null)
    {
        if (!$this->loaded)
        {
            $this->onReload($param);
        }
    }

    public function onPrint($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $pedidoId = (int) TSession::getValue(__CLASS__ . '_pedido_id');
            $propostaId = (int) TSession::getValue(__CLASS__ . '_proposta_id');

            if ($pedidoId <= 0 || $propostaId <= 0)
            {
                throw new Exception('Nao foi possivel localizar o abastecimento para impressao.');
            }

            $pedido = new PedidoFrotas($pedidoId);
            $criteria = new TCriteria;
            $criteria->add(new TFilter('deleted_at', 'is', null));
            $criteria->add(new TFilter('propostas_id', '=', $propostaId));
            $criteria->setProperty('order', 'id');
            $criteria->setProperty('direction', 'desc');

            $documentos = (new TRepository('DocumentosPropostas'))->load($criteria, false);

            $linhas = '';
            if ($documentos)
            {
                foreach ($documentos as $documento)
                {
                    $tipo = $documento->tipo_documentos_propostas->descricao ?? '';
                    $numero = $documento->numero ?? '';
                    $valor = 'R$ ' . number_format((float) ($documento->valor ?? 0), 2, ',', '.');
                    $data = !empty($documento->created_at) ? (new DateTime($documento->created_at))->format('d/m/Y H:i') : '';
                    $arquivos = $this->renderArquivosPrint($this->normalizarArquivos($documento->caminho ?? ''));

                    $linhas .= "
                        <tr>
                            <td>{$documento->id}</td>
                            <td>{$this->text($tipo)}</td>
                            <td>{$this->text($numero)}</td>
                            <td>{$valor}</td>
                            <td>{$data}</td>
                            <td>{$arquivos}</td>
                        </tr>
                    ";
                }
            }
            else
            {
                $linhas = "<tr><td colspan='6' style='text-align:center;'>Nenhum documento anexado.</td></tr>";
            }

            $veiculo = $pedido->veiculos->placa ?? '-';
            $estabelecimento = $pedido->estabelecimento->nome ?? '-';
            $dataPedido = !empty($pedido->dt_pedido) ? (new DateTime($pedido->dt_pedido))->format('d/m/Y H:i') : '';

            $html = "
                <!DOCTYPE html>
                <html lang='pt-BR'>
                <head>
                    <meta charset='utf-8'>
                    <title>Relatorio de documentos do abastecimento</title>
                    <style>
                        body { font-family: Arial, Helvetica, sans-serif; margin: 24px; color: #1f2937; }
                        h1 { margin: 0 0 8px 0; font-size: 22px; }
                        .meta { margin-bottom: 18px; color: #475569; }
                        .meta div { margin-bottom: 4px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        th, td { border: 1px solid #dbe3ea; padding: 8px; vertical-align: top; font-size: 12px; }
                        th { background: #f8fafc; text-align: left; }
                        img { max-width: 120px; max-height: 120px; border: 1px solid #dbe3ea; border-radius: 4px; margin: 2px 0; }
                        .print-actions { margin-bottom: 16px; }
                        .print-actions button { padding: 10px 16px; border: 0; border-radius: 6px; background: #2563eb; color: #fff; cursor: pointer; }
                        @media print {
                            .print-actions { display: none; }
                            body { margin: 10px; }
                        }
                    </style>
                </head>
                <body>
                    <div class='print-actions'>
                        <button onclick='window.print()'>Imprimir</button>
                    </div>
                    <h1>Relatorio de cupons / notas do abastecimento</h1>
                    <div class='meta'>
                        <div><strong>Pedido:</strong> #{$pedido->id}</div>
                        <div><strong>Data/Hora:</strong> {$dataPedido}</div>
                        <div><strong>Veiculo:</strong> {$this->text($veiculo)}</div>
                        <div><strong>Estabelecimento:</strong> {$this->text($estabelecimento)}</div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Numero</th>
                                <th>Valor</th>
                                <th>Data anexo</th>
                                <th>Documento</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$linhas}
                        </tbody>
                    </table>
                </body>
                </html>
            ";

            $arquivo = 'app/output/relatorio_documentos_abastecimento_tag_' . $pedidoId . '.html';
            file_put_contents($arquivo, $html);

            TTransaction::close();
            parent::openFile($arquivo);
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

    public function show()
    {
        if (!$this->loaded && (!isset($_GET['method']) || !in_array($_GET['method'], $this->showMethods)))
        {
            if (func_num_args() > 0)
            {
                $this->onReload(func_get_arg(0));
            }
            else
            {
                $this->onReload();
            }
        }

        parent::show();
    }

    public static function obterPropostaIdPorPedido(int $pedidoId): ?int
    {
        $proposta = Propostas::where('pedido_frotas_id', '=', $pedidoId)
            ->where('deleted_at', 'is', null)
            ->first();

        return $proposta ? (int) $proposta->id : null;
    }

    public function transformArquivo($value, $object, $row, $cell = null, $last_row = null)
    {
        if (empty($value))
        {
            return '';
        }

        $wrap = new TElement('div');
        $arquivos = $this->normalizarArquivos($value);

        foreach ($arquivos as $arquivo)
        {
            if ($arquivo === '')
            {
                continue;
            }

            $label = basename($arquivo);
            $link = new TElement('a');
            $link->href = 'download.php?file=' . rawurlencode($arquivo);
            $link->target = '_blank';
            $link->add($label);
            $wrap->add($link);
            $wrap->add('<br>');
        }

        return $wrap;
    }

    public function transformPreview($value, $object, $row, $cell = null, $last_row = null)
    {
        if (empty($value))
        {
            return '';
        }

        $wrap = new TElement('div');
        $arquivos = $this->normalizarArquivos($value);

        foreach ($arquivos as $arquivo)
        {
            if (!$this->isImageFile($arquivo) || !file_exists($arquivo))
            {
                continue;
            }

            $link = new TElement('a');
            $link->href = 'download.php?file=' . rawurlencode($arquivo);
            $link->target = '_blank';

            $image = new TImage($arquivo);
            $image->style = 'width:80px;height:80px;object-fit:cover;border:1px solid #dbe3ea;border-radius:6px;margin:2px;';

            $link->add($image);
            $wrap->add($link);
        }

        return $wrap;
    }

    private function normalizarArquivos($value): array
    {
        return is_array($value) ? $value : array_filter(array_map('trim', explode(',', (string) $value)));
    }

    private function isImageFile(string $arquivo): bool
    {
        $ext = strtolower((string) pathinfo($arquivo, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'], true);
    }

    private function renderArquivosPrint(array $arquivos): string
    {
        $html = '';

        foreach ($arquivos as $arquivo)
        {
            if ($arquivo === '')
            {
                continue;
            }

            if ($this->isImageFile($arquivo) && file_exists($arquivo))
            {
                $src = $this->text($arquivo);
                $html .= "<div><img src='{$src}' alt='Documento'></div>";
            }
            else
            {
                $html .= '<div>' . $this->text(basename($arquivo)) . '</div>';
            }
        }

        return $html ?: '-';
    }

    private function text(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
