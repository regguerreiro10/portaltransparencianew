<?php

class DispositivosSolicitadosExtratoList extends TPage
{
    
    use BuilderDatagridTrait;
private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $summaryBox;

    private static $database = 'minierp';
    private static $activeRecord = 'PedidoFrotas';
    private static $formName = 'form_DispositivosSolicitadosExtratoList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

    public static function onSetProject($param = null)
    {
        $id = (int) ($param['id'] ?? 0);
        if ($id > 0)
        {
            TSession::setValue(__CLASS__ . '_current_id', $id);

            $data = TSession::getValue(__CLASS__ . '_filter_data') ?: new stdClass();
            $data->dispositivos_solicitados_id = $id;
            $data->mes = $data->mes ?? date('m');
            $data->ano = $data->ano ?? date('Y');
            TSession::setValue(__CLASS__ . '_filter_data', $data);
        }

        TApplication::loadPage(__CLASS__, 'onShow', ['id' => $id]);
    }

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Extrato mensal do dispositivo');

        $id = new THidden('dispositivos_solicitados_id');
        $mes = new TCombo('mes');
        $ano = new TCombo('ano');

        $mes->addItems([
            '01' => 'Janeiro',
            '02' => 'Fevereiro',
            '03' => 'Marco',
            '04' => 'Abril',
            '05' => 'Maio',
            '06' => 'Junho',
            '07' => 'Julho',
            '08' => 'Agosto',
            '09' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro',
        ]);

        $anos = [];
        $anoAtual = (int) date('Y');
        for ($i = $anoAtual - 3; $i <= $anoAtual + 2; $i++)
        {
            $anos[(string) $i] = (string) $i;
        }
        $ano->addItems($anos);

        $mes->setValue(date('m'));
        $ano->setValue(date('Y'));
        $mes->enableSearch();
        $ano->enableSearch();
        $mes->setSize('100%');
        $ano->setSize('100%');

        $row1 = $this->form->addFields(
            [$id],
            [new TLabel('Mes de referencia', null, '14px', null, '100%'), $mes],
            [new TLabel('Ano de referencia', null, '14px', null, '100%'), $ano]
        );
        $row1->layout = ['col-sm-0', 'col-sm-6', 'col-sm-6'];

        $btnSearch = $this->form->addAction('Filtrar', new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $btnSearch->addStyleClass('btn-primary');

        // $btnCreate = $this->form->addAction('Criar aqui', new TAction(['DispositivosSolicitadosForm', 'onShow']), 'fas:plus #69aa46');
        // $btnCreate->addStyleClass('btn-default');

        $btnPrint = $this->form->addAction('Imprimir extrato', new TAction([$this, 'onPrintPdf']), 'fas:print #3f51b5');
        $btnPrint->addStyleClass('btn-default');

        $this->form->addAction('Limpar filtros', new TAction([$this, 'onClearFilters']), 'fas:eraser #dd5a43');
        $this->form->addAction('Voltar', new TAction(['DispositivosSolicitadosList', 'onShow']), 'fas:arrow-left #000000');

        $this->summaryBox = new TElement('div');
        $this->summaryBox->style = 'margin:10px 0 15px 0;padding:14px;border:1px solid #e5e7eb;background:#f8fafc;border-radius:8px;color:#334155;';
        $this->summaryBox->add('Selecione um dispositivo para visualizar o extrato.');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->style = 'width:100%';
        $this->datagrid->setHeight(360);

        $columnPedido = new TDataGridColumn('id', 'Pedido', 'center', '8%');
        $columnData = new TDataGridColumn('dt_pedido', 'Data/Hora', 'left', '14%');
        $columnEstabelecimento = new TDataGridColumn('estabelecimento->nome', 'Estabelecimento', 'left', '20%');
        $columnDescricao = new TDataGridColumn('descricaopedido', 'Descricao', 'left', '22%');
        $columnObservacao = new TDataGridColumn('obs', 'Observacao', 'left', '18%');
        $columnStatus = new TDataGridColumn('estado_pedido_frotas->nome', 'Status', 'left', '12%');
        $columnLiquido = new TDataGridColumn('valor_total', 'Valor total', 'right', '10%');

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

        $moneyTransformer = function ($value) {
            return 'R$ ' . number_format((float) ($value ?? 0), 2, ',', '.');
        };

        $columnLiquido->setTransformer($moneyTransformer);
        $columnStatus->setTransformer(function ($value) {
            return $value ?: '';
        });

        $orderData = new TAction([$this, 'onReload']);
        $orderData->setParameter('order', 'dt_pedido');
        $columnData->setAction($orderData);

        $this->datagrid->addColumn($columnPedido);
        $this->datagrid->addColumn($columnData);
        $this->datagrid->addColumn($columnEstabelecimento);
        $this->datagrid->addColumn($columnDescricao);
        $this->datagrid->addColumn($columnObservacao);
        $this->datagrid->addColumn($columnStatus);
        $this->datagrid->addColumn($columnLiquido);
        $this->applyDatagridProperties();

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        $panel->getBody()->class .= ' table-responsive';

        $container = new TVBox;
        $container->style = 'width:100%';
        $container->add($this->form);
        $container->add($this->summaryBox);
        $container->add($panel);

        parent::add($container);
    }

    public function onSearch($param = null)
    {
        $data = $this->form->getData();

        if (empty($data->dispositivos_solicitados_id) && !empty($param['id']))
        {
            $data->dispositivos_solicitados_id = $param['id'];
        }

        if (!empty($data->dispositivos_solicitados_id))
        {
            TSession::setValue(__CLASS__ . '_current_id', $data->dispositivos_solicitados_id);
        }

        $this->form->setData($data);
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        $this->loaded = false;
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onClearFilters($param = null)
    {
        $current = $this->form->getData();
        $data = new stdClass();
        $data->dispositivos_solicitados_id = $current->dispositivos_solicitados_id ?? TSession::getValue(__CLASS__ . '_current_id') ?? ($param['id'] ?? null);
        $data->mes = date('m');
        $data->ano = date('Y');

        if (!empty($data->dispositivos_solicitados_id))
        {
            TSession::setValue(__CLASS__ . '_current_id', $data->dispositivos_solicitados_id);
        }

        TSession::setValue(__CLASS__ . '_filter_data', $data);
        $this->form->setData($data);
        $this->loaded = false;
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onRefresh($param = null)
    {
        $this->loaded = false;
        $this->onReload($param);
    }

    public function onReload($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!$data)
            {
                $data = new stdClass();
                $data->dispositivos_solicitados_id = $param['id'] ?? TSession::getValue(__CLASS__ . '_current_id') ?? null;
                $data->mes = date('m');
                $data->ano = date('Y');
                TSession::setValue(__CLASS__ . '_filter_data', $data);
            }

            if (empty($data->dispositivos_solicitados_id) && !empty($param['id']))
            {
                $data->dispositivos_solicitados_id = $param['id'];
                TSession::setValue(__CLASS__ . '_filter_data', $data);
            }

            if (empty($data->dispositivos_solicitados_id))
            {
                $data->dispositivos_solicitados_id = TSession::getValue(__CLASS__ . '_current_id');
            }

            if (!empty($data->dispositivos_solicitados_id))
            {
                TSession::setValue(__CLASS__ . '_current_id', $data->dispositivos_solicitados_id);
            }

            $this->form->setData($data);

            if (empty($data->dispositivos_solicitados_id))
            {
                throw new Exception('Dispositivo nao informado para gerar o extrato.');
            }

            $mes = str_pad((string) ($data->mes ?? date('m')), 2, '0', STR_PAD_LEFT);
            $ano = (string) ($data->ano ?? date('Y'));
            $dataInicial = "{$ano}-{$mes}-01 00:00:00";
            $dataFinal = date('Y-m-t 23:59:59', strtotime("{$ano}-{$mes}-01"));

            $repository = new TRepository(self::$activeRecord);

            $param = (array) $param;
            if (empty($param['order']))
            {
                $param['order'] = 'dt_pedido';
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $objetosResumo = $this->loadExtratoPedidos($repository, (int) $data->dispositivos_solicitados_id, $dataInicial, $dataFinal);
            $objects = $objetosResumo;
            $this->datagrid->clear();

            $totalLiquido = 0;
            $totalMovimentos = 0;

            if ($objetosResumo)
            {
                foreach ($objetosResumo as $objetoResumo)
                {
                    $totalLiquido += (float) $objetoResumo->valor_total;
                    $totalMovimentos++;
                }
            }

            usort($objects, function ($a, $b) use ($param) {
                $direction = strtolower((string) ($param['direction'] ?? 'desc')) === 'asc' ? 1 : -1;
                $campo = $param['order'] ?? 'dt_pedido';
                $valorA = $a->{$campo} ?? null;
                $valorB = $b->{$campo} ?? null;
                return $direction * strcmp((string) $valorA, (string) $valorB);
            });

            $count = count($objects);
            $offset = (int) ($param['offset'] ?? 0);
            $objects = array_slice($objects, $offset, $this->limit);

            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($this->limit);

            $dispositivo = new DispositivosSolicitados((int) $data->dispositivos_solicitados_id);
            $this->summaryBox->clearChildren();
            $this->summaryBox->add($this->buildSummary($dispositivo, $mes, $ano, $totalMovimentos, $totalLiquido));

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

    public function onShow($param = null)
    {
        $id = (int) ($param['id'] ?? 0);
        if ($id > 0)
        {
            TSession::setValue(__CLASS__ . '_current_id', $id);

            $data = TSession::getValue(__CLASS__ . '_filter_data') ?: new stdClass();
            $data->dispositivos_solicitados_id = $id;
            $data->mes = $data->mes ?? date('m');
            $data->ano = $data->ano ?? date('Y');
            TSession::setValue(__CLASS__ . '_filter_data', $data);
        }

        if (!$this->loaded)
        {
            $this->onReload($param);
        }
    }

    public function onPrintPdf($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $data = TSession::getValue(__CLASS__ . '_filter_data');
            if (!$data)
            {
                $data = new stdClass();
                $data->dispositivos_solicitados_id = $param['id'] ?? TSession::getValue(__CLASS__ . '_current_id') ?? null;
                $data->mes = date('m');
                $data->ano = date('Y');
            }

            if (empty($data->dispositivos_solicitados_id))
            {
                $formData = $this->form->getData();
                $data->dispositivos_solicitados_id = $formData->dispositivos_solicitados_id ?? TSession::getValue(__CLASS__ . '_current_id') ?? null;
            }

            if (empty($data->dispositivos_solicitados_id))
            {
                throw new Exception('Dispositivo nao informado para imprimir o extrato.');
            }

            TSession::setValue(__CLASS__ . '_current_id', $data->dispositivos_solicitados_id);

            $mes = str_pad((string) ($data->mes ?? date('m')), 2, '0', STR_PAD_LEFT);
            $ano = (string) ($data->ano ?? date('Y'));
            $dataInicial = "{$ano}-{$mes}-01 00:00:00";
            $dataFinal = date('Y-m-t 23:59:59', strtotime("{$ano}-{$mes}-01"));

            $objetos = $this->loadExtratoPedidos(new TRepository(self::$activeRecord), (int) $data->dispositivos_solicitados_id, $dataInicial, $dataFinal);
            usort($objetos, function ($a, $b) {
                return strcmp((string) ($b->dt_pedido ?? ''), (string) ($a->dt_pedido ?? ''));
            });
            $dispositivo = new DispositivosSolicitados((int) $data->dispositivos_solicitados_id);

            $totalLiquido = 0;
            $totalMovimentos = 0;
            $linhas = '';

            if ($objetos)
            {
                foreach ($objetos as $object)
                {
                    $totalLiquido += (float) $object->valor_total;
                    $totalMovimentos++;

                    $dataPedido = !empty($object->dt_pedido) ? (new DateTime($object->dt_pedido))->format('d/m/Y H:i') : '';
                    $estabelecimento = $object->estabelecimento->nome ?? '-';
                    $descricao = $this->text($object->descricaopedido ?? '');
                    $obs = $this->text($object->obs ?? '');
                    $status = $this->text($object->estado_pedido_frotas->nome ?? '');
                    $valor = 'R$ ' . number_format((float) ($object->valor_total ?? 0), 2, ',', '.');

                    $linhas .= "
                        <tr>
                            <td>{$object->id}</td>
                            <td>{$dataPedido}</td>
                            <td>{$this->text($estabelecimento)}</td>
                            <td>{$descricao}</td>
                            <td>{$obs}</td>
                            <td>{$status}</td>
                            <td style='text-align:right;'>{$valor}</td>
                        </tr>
                    ";
                }
            }

            if ($linhas === '')
            {
                $linhas = "<tr><td colspan='7' style='text-align:center;'>Nenhum abastecimento encontrado no periodo.</td></tr>";
            }

            $veiculo = $dispositivo->veiculos->placa ?? '-';
            $usuario = $dispositivo->pessoa->nome ?? '-';
            $numero = $dispositivo->numerocartao ?: 'Nao informado';
            $saldoAtual = 'R$ ' . number_format((float) ($dispositivo->saldo_atual ?? 0), 2, ',', '.');
            $saldoLimite = 'R$ ' . number_format((float) ($dispositivo->saldo_limite ?? 0), 2, ',', '.');
            $consumoMes = 'R$ ' . number_format($totalLiquido, 2, ',', '.');
            $referencia = "{$mes}/{$ano}";

            $html = "
                <style>
                    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #1f2937; }
                    h1 { font-size: 18px; margin-bottom: 6px; }
                    .pdf-list-header { width: 100%; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px solid #8c96a5; }
                    .pdf-list-header table { width: 100%; border-collapse: collapse; }
                    .pdf-list-header td { border: 0; padding: 0; vertical-align: middle; }
                    .pdf-list-logo { width: 26px; height: auto; display: block; }
                    .pdf-list-title { font-size: 11px; font-weight: bold; text-align: center; }
                    .pdf-list-meta { font-size: 8px; font-weight: bold; text-align: right; line-height: 1.35; }
                    .meta { margin-bottom: 16px; }
                    .meta div { margin-bottom: 4px; }
                    .cards { width: 100%; margin-bottom: 18px; }
                    .cards td { border: 1px solid #dbe3ea; padding: 10px; width: 33%; }
                    .cards .label { font-size: 10px; text-transform: uppercase; color: #64748b; }
                    .cards .value { font-size: 18px; font-weight: bold; margin-top: 4px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #dbe3ea; padding: 8px; vertical-align: top; }
                    th { background: #464e5c; color: #ffffff; text-align: left; }
                    tbody tr:nth-child(even) td { background: #e8ebf0; }
                </style>
                <div class='pdf-list-header'>
                    <table>
                        <tr>
                            <td style='width:42px;'><img src='app/images/logo.png' class='pdf-list-logo'></td>
                            <td class='pdf-list-title'>Extrato mensal do dispositivo</td>
                            <td class='pdf-list-meta'>Pagina gerada pelo sistema</td>
                        </tr>
                    </table>
                </div>
                <h1>Extrato mensal do dispositivo</h1>
                <div class='meta'>
                    <div><strong>Dispositivo:</strong> #{$dispositivo->id}</div>
                    <div><strong>Cartao/UID:</strong> {$this->text($numero)}</div>
                    <div><strong>Veiculo:</strong> {$this->text($veiculo)}</div>
                    <div><strong>Condutor/Usuario:</strong> {$this->text($usuario)}</div>
                    <div><strong>Referencia:</strong> {$referencia}</div>
                </div>
                <table class='cards' cellspacing='0' cellpadding='0'>
                    <tr>
                        <td>
                            <div class='label'>Saldo atual</div>
                            <div class='value'>{$saldoAtual}</div>
                        </td>
                        <td>
                            <div class='label'>Saldo limite</div>
                            <div class='value'>{$saldoLimite}</div>
                        </td>
                        <td>
                            <div class='label'>Consumo no mes</div>
                            <div class='value'>{$consumoMes}</div>
                            <div>{$totalMovimentos} movimentacoes</div>
                        </td>
                    </tr>
                </table>
                <table cellspacing='0' cellpadding='0'>
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Data/Hora</th>
                            <th>Estabelecimento</th>
                            <th>Descricao</th>
                            <th>Observacao</th>
                            <th>Status</th>
                            <th>Valor total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$linhas}
                    </tbody>
                </table>
            ";

            TTransaction::close();

            $output = 'app/output/' . uniqid('extrato_dispositivo_') . '.pdf';
            $dompdf = new \Dompdf\Dompdf;
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            file_put_contents($output, $dompdf->output());

            TPage::openFile($output);
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

    private function buildSummary(DispositivosSolicitados $dispositivo, string $mes, string $ano, int $totalMovimentos, float $totalLiquido): string
    {
        $veiculo = $dispositivo->veiculos->placa ?? '-';
        $usuario = $dispositivo->pessoa->nome ?? '-';
        $numero = $dispositivo->numerocartao ?: 'Nao informado';
        $referencia = "{$mes}/{$ano}";
        $saldoAtual = number_format(abs((float) ($dispositivo->saldo_atual ?? 0)), 2, ',', '.');
        $saldoLimite = number_format(abs((float) ($dispositivo->saldo_limite ?? 0)), 2, ',', '.');
        $consumoMes = number_format($totalLiquido, 2, ',', '.');

        return "
            <div style='display:flex;gap:12px;flex-wrap:wrap;align-items:stretch;'>
                <div style='flex:2;min-width:260px;'>
                    <div style='font-size:18px;font-weight:700;color:#0f172a;'>Extrato do dispositivo #{$dispositivo->id}</div>
                    <div style='margin-top:6px;color:#475569;'>Cartao/UID: {$numero}</div>
                    <div style='margin-top:4px;color:#475569;'>Veiculo: {$veiculo}</div>
                    <div style='margin-top:4px;color:#475569;'>Condutor/Usuario: {$usuario}</div>
                    <div style='margin-top:4px;color:#475569;'>Referencia: {$referencia}</div>
                </div>
                <div style='flex:1;min-width:180px;padding:12px;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;'>
                    <div style='font-size:12px;text-transform:uppercase;color:#64748b;'>Saldo atual</div>
                    <div style='font-size:22px;font-weight:700;color:#0f766e;'>R$ {$saldoAtual}</div>
                </div>
                <div style='flex:1;min-width:180px;padding:12px;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;'>
                    <div style='font-size:12px;text-transform:uppercase;color:#64748b;'>Saldo limite</div>
                    <div style='font-size:22px;font-weight:700;color:#1d4ed8;'>R$ {$saldoLimite}</div>
                </div>
                <div style='flex:1;min-width:180px;padding:12px;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;'>
                    <div style='font-size:12px;text-transform:uppercase;color:#64748b;'>Consumo no mes</div>
                    <div style='font-size:22px;font-weight:700;color:#b45309;'>R$ {$consumoMes}</div>
                    <div style='margin-top:4px;color:#64748b;'>{$totalMovimentos} movimentacoes</div>
                </div>
            </div>
        ";
    }

    private function text(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    private function loadExtratoPedidos(TRepository $repository, int $dispositivoId, string $dataInicial, string $dataFinal): array
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('deleted_at', 'is', null));
        $criteria->add(new TFilter('abastecimento', '=', 1));
        $criteria->add(new TFilter('dispositivos_solicitados_id', '=', $dispositivoId));
        $criteria->add(new TFilter('dt_pedido', '>=', $dataInicial));
        $criteria->add(new TFilter('dt_pedido', '<=', $dataFinal));

        $pedidos = $repository->load($criteria, false) ?: [];
        $idsCarregados = [];

        foreach ($pedidos as $pedido)
        {
            $idsCarregados[(int) $pedido->id] = true;
        }

        $criteriaLegacy = new TCriteria;
        $criteriaLegacy->add(new TFilter('deleted_at', 'is', null));
        $criteriaLegacy->add(new TFilter('abastecimento', '=', 1));
        $criteriaLegacy->add(new TFilter('dispositivos_solicitados_id', 'is', null));
        $criteriaLegacy->add(new TFilter('obs', 'like', '%DISPOSITIVO_SOLICITADO_ID=' . $dispositivoId . '%'));
        $criteriaLegacy->add(new TFilter('dt_pedido', '>=', $dataInicial));
        $criteriaLegacy->add(new TFilter('dt_pedido', '<=', $dataFinal));

        $pedidosLegacy = $repository->load($criteriaLegacy, false) ?: [];

        foreach ($pedidosLegacy as $pedidoLegacy)
        {
            if (!isset($idsCarregados[(int) $pedidoLegacy->id]))
            {
                $pedidos[] = $pedidoLegacy;
            }
        }

        return $pedidos;
    }
}
