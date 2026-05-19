<?php

class ManutencaoGarantiaPopup extends TWindow
{
    private const PREVIEW_LIMIT = 13;
    private $datagrid;

    public function __construct($param = null)
    {
        parent::__construct();

        $this->setTitle('Plano de Manutencao Preventiva');
        $this->setSize(0.92, 0.82);
        $this->setDialogClass('window_modal');

        $bundle = self::loadVehicleBundle($param);

        $container = new TVBox;
        $container->style = 'width: 100%; padding: 10px;';

        $container->add(self::buildHeader($bundle));
        $container->add(self::buildSummary($bundle['summary']));
        $container->add($this->buildGrid($bundle['vehicles']));

        parent::add($container);
    }

    private static function buildHeader(array $bundle)
    {
        $wrapper = new TElement('div');
        $wrapper->style = 'margin-bottom: 12px;';

        $title = new TElement('div');
        $title->style = 'font-size: 22px; font-weight: 700; color: #1f2937; margin-bottom: 4px;';
        $title->add('Plano de Manutencao Preventiva por Veiculo');

        $subtitle = new TElement('div');
        $subtitle->style = 'font-size: 13px; color: #6b7280;';
        $subtitle->add('Previa com os primeiros 13 registros da tabela veiculos, indicando quem tem ou nao garantia cadastrada.');

        $toolbar = new TElement('div');
        $toolbar->style = 'margin-top: 10px;';

        if (!empty($bundle['has_more']) && empty($bundle['show_all'])) {
            $showAllLink = new TElement('a');
            $showAllLink->href = 'engine.php?class=ManutencaoGarantiaPopup&show_all=1';
            $showAllLink->{'generator'} = 'adianti';
            $showAllLink->class = 'btn btn-sm btn-default';
            $showAllLink->style = 'margin-right: 8px;';
            $showAllLink->add('Listar todos os veiculos');
            $toolbar->add($showAllLink);
        }

        if (!empty($bundle['show_all'])) {
            $previewLink = new TElement('a');
            $previewLink->href = 'engine.php?class=ManutencaoGarantiaPopup';
            $previewLink->{'generator'} = 'adianti';
            $previewLink->class = 'btn btn-sm btn-default';
            $previewLink->style = 'margin-right: 8px;';
            $previewLink->add('Voltar para previa');
            $toolbar->add($previewLink);
        }

        $wrapper->add($title);
        $wrapper->add($subtitle);
        $wrapper->add($toolbar);

        return $wrapper;
    }

    private static function buildSummary(array $summary)
    {
        $row = new TElement('div');
        $row->style = 'display:flex; gap:12px; flex-wrap:wrap; margin-bottom: 12px;';

        $cards = [
            ['Veiculos', $summary['vehicles'], '#0f172a', '#e2e8f0'],
            ['Planos cadastrados', $summary['plans'], '#1d4ed8', '#dbeafe'],
            ['Com garantia', $summary['with_plan'], '#166534', '#dcfce7'],
            ['Sem garantia', $summary['without_plan'], '#854d0e', '#fef9c3'],
            ['Com historico', $summary['with_history'], '#166534', '#dcfce7'],
            ['Sem historico', $summary['without_history'], '#7c3aed', '#ede9fe'],
            ['Atencao', $summary['attention'], '#991b1b', '#fee2e2'],
        ];

        foreach ($cards as $card) {
            $box = new TElement('div');
            $box->style = "min-width: 150px; padding: 14px 16px; border-radius: 10px; background: {$card[3]};";

            $label = new TElement('div');
            $label->style = "font-size: 12px; color: {$card[2]}; opacity: .9;";
            $label->add($card[0]);

            $value = new TElement('div');
            $value->style = "font-size: 24px; font-weight: 700; color: {$card[2]}; line-height: 1.2;";
            $value->add($card[1]);

            $box->add($label);
            $box->add($value);
            $row->add($box);
        }

        return $row;
    }

    private function buildGrid(array $vehicles)
    {
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        $this->datagrid->addColumn(new TDataGridColumn('vehicle_label', 'Veiculo', 'left'));
        $this->datagrid->addColumn(new TDataGridColumn('plate', 'Placa', 'center'));
        $this->datagrid->addColumn(new TDataGridColumn('plan_label', 'Garantia', 'center'));
        $this->datagrid->addColumn(new TDataGridColumn('plans_count', 'Planos', 'center'));
        $this->datagrid->addColumn(new TDataGridColumn('history_count', 'Manutencoes feitas', 'center'));
        $this->datagrid->addColumn(new TDataGridColumn('last_maintenance_display', 'Ultima manutencao', 'center'));
        $this->datagrid->addColumn(new TDataGridColumn('reference_display', 'Referencia preventiva', 'center'));

        $status = new TDataGridColumn('status_label', 'Status', 'center');
        $status->setTransformer(function ($value, $object) {
            $badge = new TElement('span');
            $badge->style = "display:inline-block; min-width:110px; padding:6px 10px; border-radius:14px; color:#fff; font-weight:600; background:{$object->status_color};";
            $badge->add($value);
            return $badge;
        });
        $this->datagrid->addColumn($status);

        $action = new TDataGridAction([__CLASS__, 'onGeneratePdf']);
        $action->setLabel('PDF');
        $action->setImage('far:file-pdf #e74c3c');
        $action->setField('veiculos_id');
        $action->setParameter('static', '1');
        $action->setParameter('register_state', 'false');
        $this->datagrid->addAction($action);

        $this->datagrid->createModel();

        foreach ($vehicles as $vehicle) {
            $this->datagrid->addItem((object) $vehicle);
        }

        return TPanelGroup::pack('', $this->datagrid);
    }

    public static function onGeneratePdf($param = null)
    {
        try {
            $vehicleId = isset($param['key']) ? (int) $param['key'] : (isset($param['veiculos_id']) ? (int) $param['veiculos_id'] : 0);
            if ($vehicleId <= 0) {
                throw new Exception('Veiculo nao informado para gerar o PDF.');
            }

            $bundle = self::loadVehiclePdfBundle($vehicleId);

            $fileName = 'plano_preventivo_veiculo_' . $vehicleId . '_' . date('Ymd_His') . '.pdf';
            $filePath = 'app/output/' . $fileName;

            if (!file_exists('app/output')) {
                mkdir('app/output', 0777, true);
            }

            $pdf = new FPDF('P', 'pt', 'A4');
            $pdf->SetAutoPageBreak(true, 36);
            $pdf->AddPage();

            self::drawVehiclePdfHeader($pdf, $bundle);
            self::drawVehiclePdfPlan($pdf, $bundle);
            self::drawVehiclePdfHistory($pdf, $bundle);
            self::drawGuaranteedVehiclesPdf($pdf, $bundle);

            if (!file_exists($filePath) || is_writable($filePath)) {
                $pdf->Output($filePath, 'F');
            } else {
                throw new Exception('Sem permissao para gravar o arquivo PDF.');
            }

            $window = TWindow::create('Plano Preventivo do Veiculo', 0.9, 0.85);
            $object = new TElement('object');
            $object->data = $filePath;
            $object->type = 'application/pdf';
            $object->style = 'width: 100%; height: calc(100% - 10px)';
            $object->add('O navegador nao suporta a exibicao deste conteudo, <a style="color:#007bff;" target=_blank href="' . $filePath . '">clique aqui para baixar</a>.');

            $window->add($object);
            $window->show();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    private static function drawVehiclePdfHeader(FPDF $pdf, array $bundle)
    {
        $vehicle = $bundle['vehicle'];

        $pdf->SetTextColor(31, 41, 55);
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 20, self::pdf('Plano de Manutencao Preventiva'), 0, 1, 'L');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 14, self::pdf('Veiculo: ' . $vehicle['vehicle_label']), 0, 1, 'L');
        $pdf->Cell(0, 14, self::pdf('Placa: ' . $vehicle['plate'] . '   Modelo: ' . $vehicle['model'] . '   Marca: ' . $vehicle['brand']), 0, 1, 'L');
        $pdf->Cell(0, 14, self::pdf('Data de emissao: ' . date('d/m/Y H:i')), 0, 1, 'L');
        $pdf->Ln(8);

        $pdf->SetFillColor(241, 245, 249);
        $pdf->Rect(28, $pdf->GetY(), 540, 42, 'F');
        $y = $pdf->GetY() + 8;

        $pdf->SetXY(36, $y);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(160, 12, self::pdf('Planos cadastrados: ' . $vehicle['plans_count']), 0, 0, 'L');
        $pdf->Cell(180, 12, self::pdf('Manutencoes feitas: ' . $vehicle['history_count']), 0, 0, 'L');
        $pdf->Cell(160, 12, self::pdf('Garantia: ' . $vehicle['plan_label']), 0, 1, 'L');

        $pdf->SetXY(36, $y + 16);
        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(520, 12, self::pdf('Este PDF lista o plano preventivo do veiculo a partir da tabela manutencao_garantia, o historico de manutencoes da tabela manutencao_veiculo e uma relacao dos veiculos que ja possuem garantia cadastrada.'));
        $pdf->Ln(6);
    }

    private static function drawVehiclePdfPlan(FPDF $pdf, array $bundle)
    {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 18, self::pdf('Listagem da tabela manutencao_garantia'), 0, 1, 'L');

        $columns = [
            ['label' => 'Item', 'width' => 34, 'align' => 'C'],
            ['label' => 'Descricao', 'width' => 180, 'align' => 'L'],
            ['label' => 'Tipo', 'width' => 48, 'align' => 'C'],
            ['label' => 'Qtd', 'width' => 36, 'align' => 'C'],
            ['label' => 'KM/Horimetro', 'width' => 82, 'align' => 'C'],
            ['label' => 'Dias', 'width' => 42, 'align' => 'C'],
            ['label' => 'Data referencia', 'width' => 82, 'align' => 'C'],
            ['label' => 'Ativo', 'width' => 36, 'align' => 'C'],
        ];

        self::drawSimpleTableHeader($pdf, $columns);

        if (empty($bundle['plans'])) {
            self::drawSimpleRow($pdf, [['text' => 'Nenhum plano preventivo encontrado para este veiculo.', 'width' => 540, 'align' => 'L']]);
            $pdf->Ln(6);
            return;
        }

        $i = 1;
        foreach ($bundle['plans'] as $plan) {
            self::drawSimpleRow($pdf, [
                ['text' => $i, 'width' => 34, 'align' => 'C'],
                ['text' => $plan['descricao'], 'width' => 180, 'align' => 'L'],
                ['text' => $plan['tipo_label'], 'width' => 48, 'align' => 'C'],
                ['text' => $plan['qtde_display'], 'width' => 36, 'align' => 'C'],
                ['text' => $plan['km_display'], 'width' => 82, 'align' => 'C'],
                ['text' => $plan['dias_display'], 'width' => 42, 'align' => 'C'],
                ['text' => $plan['data_display'], 'width' => 82, 'align' => 'C'],
                ['text' => $plan['ativo_label'], 'width' => 36, 'align' => 'C'],
            ]);
            $i++;
        }

        $pdf->Ln(10);
    }

    private static function drawVehiclePdfHistory(FPDF $pdf, array $bundle)
    {
        if ($pdf->GetY() > 680) {
            $pdf->AddPage();
        }

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 18, self::pdf('Manutencoes ja realizadas'), 0, 1, 'L');

        $columns = [
            ['label' => 'Data', 'width' => 64, 'align' => 'C'],
            ['label' => 'KM', 'width' => 58, 'align' => 'C'],
            ['label' => 'Servicos / Produtos', 'width' => 220, 'align' => 'L'],
            ['label' => 'Valor', 'width' => 62, 'align' => 'R'],
            ['label' => 'Obs', 'width' => 136, 'align' => 'L'],
        ];

        self::drawSimpleTableHeader($pdf, $columns);

        if (empty($bundle['history'])) {
            self::drawSimpleRow($pdf, [['text' => 'Nenhuma manutencao realizada foi encontrada para este veiculo.', 'width' => 540, 'align' => 'L']]);
            return;
        }

        foreach ($bundle['history'] as $history) {
            self::drawSimpleRow($pdf, [
                ['text' => $history['data_display'], 'width' => 64, 'align' => 'C'],
                ['text' => $history['km_display'], 'width' => 58, 'align' => 'C'],
                ['text' => $history['servicos_produtos'], 'width' => 220, 'align' => 'L'],
                ['text' => $history['valor_display'], 'width' => 62, 'align' => 'R'],
                ['text' => $history['obs'], 'width' => 136, 'align' => 'L'],
            ]);
        }

        $pdf->Ln(10);
    }

    private static function drawGuaranteedVehiclesPdf(FPDF $pdf, array $bundle)
    {
        if ($pdf->GetY() > 680) {
            $pdf->AddPage();
        }

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 18, self::pdf('Veiculos que ja tem garantia'), 0, 1, 'L');

        $columns = [
            ['label' => 'Veiculo', 'width' => 220, 'align' => 'L'],
            ['label' => 'Placa', 'width' => 80, 'align' => 'C'],
            ['label' => 'Planos', 'width' => 54, 'align' => 'C'],
            ['label' => 'Status', 'width' => 90, 'align' => 'C'],
            ['label' => 'Referencia', 'width' => 96, 'align' => 'C'],
        ];

        self::drawSimpleTableHeader($pdf, $columns);

        if (empty($bundle['guaranteed_vehicles'])) {
            self::drawSimpleRow($pdf, [['text' => 'Nenhum veiculo com garantia cadastrada foi encontrado.', 'width' => 540, 'align' => 'L']]);
            return;
        }

        foreach ($bundle['guaranteed_vehicles'] as $guaranteedVehicle) {
            self::drawSimpleRow($pdf, [
                ['text' => $guaranteedVehicle['vehicle_label'], 'width' => 220, 'align' => 'L'],
                ['text' => $guaranteedVehicle['plate'], 'width' => 80, 'align' => 'C'],
                ['text' => $guaranteedVehicle['plans_count'], 'width' => 54, 'align' => 'C'],
                ['text' => $guaranteedVehicle['status_label'], 'width' => 90, 'align' => 'C'],
                ['text' => $guaranteedVehicle['reference_display'], 'width' => 96, 'align' => 'C'],
            ]);
        }
    }

    private static function drawSimpleTableHeader(FPDF $pdf, array $columns)
    {
        if ($pdf->GetY() > 700) {
            $pdf->AddPage();
        }

        $pdf->SetFillColor(15, 23, 42);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);

        foreach ($columns as $column) {
            $pdf->Cell($column['width'], 18, self::pdf($column['label']), 1, 0, $column['align'], true);
        }
        $pdf->Ln();
        $pdf->SetTextColor(31, 41, 55);
    }

    private static function drawSimpleRow(FPDF $pdf, array $cells)
    {
        if ($pdf->GetY() > 720) {
            $pdf->AddPage();
        }

        $pdf->SetFont('Arial', '', 8);
        foreach ($cells as $cell) {
            $pdf->Cell($cell['width'], 18, self::pdf(self::truncate($cell['text'], self::guessLimit($cell['width']))), 1, 0, $cell['align']);
        }
        $pdf->Ln();
    }

    private static function loadVehicleBundle($param = null)
    {
        $vehicles = [];
        $sourceMessage = 'Clique em PDF para gerar o plano simples de cada veiculo com o historico do que ja foi executado.';
        $showAll = !empty($param['show_all']);

        try {
            TTransaction::open(self::getConnectionName());
            $conn = TTransaction::get();

            $vehiclesRows = self::fetchVehicleRows($conn);
            $plans = self::tableExists($conn, 'manutencao_garantia') ? self::fetchPlanRows($conn) : [];
            $historyByVehicle = self::fetchHistoryByVehicle($conn);
            TTransaction::close();

            $groupedPlans = self::groupPlansByVehicle($plans);
            foreach ($vehiclesRows as $vehicleRow) {
                $vehicleId = (int) $vehicleRow['veiculos_id'];
                $group = $groupedPlans[$vehicleId] ?? self::createEmptyVehicleGroup($vehicleRow);
                $history = $historyByVehicle[$vehicleId] ?? [];
                $vehicles[] = self::buildVehicleRow($group, $history);
            }

            usort($vehicles, function ($a, $b) {
                return strcmp($a['vehicle_label'], $b['vehicle_label']);
            });

            if (empty($vehicles)) {
                $sourceMessage = 'Nenhum veiculo foi encontrado para a unidade atual.';
            } elseif ($showAll) {
                $sourceMessage = 'Listagem completa da tabela veiculos, indicando quem tem ou nao garantia cadastrada em manutencao_garantia.';
            } else {
                $sourceMessage = 'Previa com os primeiros ' . self::PREVIEW_LIMIT . ' registros da tabela veiculos, indicando quem tem ou nao garantia cadastrada.';
            }
        } catch (Exception $e) {
            if (TTransaction::get()) {
                TTransaction::rollback();
            }

            $sourceMessage = 'Nao foi possivel carregar os planos preventivos no momento.';
        }

        $allVehicles = $vehicles;
        if (!$showAll) {
            $vehicles = array_slice($vehicles, 0, self::PREVIEW_LIMIT);
        }

        return [
            'vehicles' => $vehicles,
            'summary' => self::buildVehicleSummary($allVehicles),
            'source_message' => $sourceMessage,
            'has_more' => count($allVehicles) > self::PREVIEW_LIMIT,
            'show_all' => $showAll,
        ];
    }

    private static function loadVehiclePdfBundle($vehicleId)
    {
        TTransaction::open(self::getConnectionName());
        $conn = TTransaction::get();

        try {
            $vehiclesRows = self::fetchVehicleRows($conn, $vehicleId);
            $plans = self::fetchPlanRows($conn, $vehicleId);
            $historyByVehicle = self::fetchHistoryByVehicle($conn, [$vehicleId]);
            $history = $historyByVehicle[$vehicleId] ?? [];
            $group = self::groupPlansByVehicle($plans);
            $vehicleBase = !empty($vehiclesRows) ? $vehiclesRows[0] : null;
            if (!$vehicleBase) {
                throw new Exception('Veiculo nao encontrado para gerar o PDF.');
            }

            $vehicle = self::buildVehicleRow($group[$vehicleId] ?? self::createEmptyVehicleGroup($vehicleBase), $history);

            $allPlans = self::fetchPlanRows($conn);
            $allHistory = self::fetchHistoryByVehicle($conn);
            $guaranteedVehicles = [];
            foreach (self::groupPlansByVehicle($allPlans) as $guaranteedVehicleId => $guaranteedGroup) {
                $guaranteedVehicles[] = self::buildVehicleRow($guaranteedGroup, $allHistory[$guaranteedVehicleId] ?? []);
            }

            usort($guaranteedVehicles, function ($a, $b) {
                return strcmp($a['vehicle_label'], $b['vehicle_label']);
            });

            TTransaction::close();

            return [
                'vehicle' => $vehicle,
                'plans' => $group[$vehicleId]['plans'] ?? [],
                'history' => $history,
                'guaranteed_vehicles' => $guaranteedVehicles,
            ];
        } catch (Exception $e) {
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
            throw $e;
        }
    }

    private static function fetchPlanRows(PDO $conn, $vehicleId = null)
    {
        $sql = "SELECT mg.*,
                       v.placa,
                       v.prefixo,
                       v.hodometroatual,
                       m.descricao AS marca_descricao,
                       mo.descricao AS modelo_descricao
                  FROM manutencao_garantia mg
             LEFT JOIN veiculos v ON v.id = mg.veiculos_id
             LEFT JOIN marca m ON m.id = v.marca_id
             LEFT JOIN modelo mo ON mo.id = v.modelo_id
                 WHERE mg.deleted_at IS NULL";

        $params = [];

        if ($vehicleId) {
            $sql .= " AND mg.veiculos_id = ?";
            $params[] = $vehicleId;
        } else {
            $unitId = TSession::getValue('idunit');
            if ($unitId) {
                $sql .= " AND (v.system_unit_id = ? OR v.system_unit_id IS NULL)";
                $params[] = $unitId;
            }
        }

        $sql .= " ORDER BY COALESCE(v.placa, ''), COALESCE(mg.datagarantia, '9999-12-31'), mg.id";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $rows = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $rows[] = self::normalizePlanRow($row);
        }

        return $rows;
    }

    private static function fetchVehicleRows(PDO $conn, $vehicleId = null)
    {
        $sql = "SELECT v.id AS veiculos_id,
                       v.placa,
                       v.prefixo,
                       v.hodometroatual,
                       m.descricao AS marca_descricao,
                       mo.descricao AS modelo_descricao
                  FROM veiculos v
             LEFT JOIN marca m ON m.id = v.marca_id
             LEFT JOIN modelo mo ON mo.id = v.modelo_id
                 WHERE v.deleted_at IS NULL";

        $params = [];

        if ($vehicleId) {
            $sql .= " AND v.id = ?";
            $params[] = $vehicleId;
        } else {
            $unitId = TSession::getValue('idunit');
            if ($unitId) {
                $sql .= " AND (v.system_unit_id = ? OR v.system_unit_id IS NULL)";
                $params[] = $unitId;
            }
        }

        $sql .= " ORDER BY COALESCE(v.placa, ''), COALESCE(v.prefixo, ''), v.id";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $rows = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $rows[] = [
                'veiculos_id' => (int) ($row['veiculos_id'] ?? 0),
                'vehicle_label' => self::buildVehicleLabel($row),
                'plate' => trim((string) ($row['placa'] ?? '-')),
                'brand' => trim((string) ($row['marca_descricao'] ?? '')),
                'model' => trim((string) ($row['modelo_descricao'] ?? '')),
                'hodometro' => (float) ($row['hodometroatual'] ?? 0),
            ];
        }

        return $rows;
    }

    private static function fetchHistoryByVehicle(PDO $conn, array $vehicleIds = null)
    {
        if (!self::tableExists($conn, 'manutencao_veiculo')) {
            return [];
        }

        $sql = "SELECT *
                  FROM manutencao_veiculo
                 WHERE veiculos_id IS NOT NULL";
        $params = [];

        if ($vehicleIds !== null) {
            if (empty($vehicleIds)) {
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($vehicleIds), '?'));
            $sql .= " AND veiculos_id IN ({$placeholders})";
            $params = array_values($vehicleIds);
        } else {
            $unitId = TSession::getValue('idunit');
            if ($unitId && self::tableExists($conn, 'veiculos')) {
                $sql .= " AND veiculos_id IN (SELECT id FROM veiculos WHERE system_unit_id = ? OR system_unit_id IS NULL)";
                $params[] = $unitId;
            }
        }

        $sql .= " ORDER BY COALESCE(datamanutencao, '1900-01-01') DESC, id DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $historyByVehicle = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $vehicleId = (int) $row['veiculos_id'];
            $historyByVehicle[$vehicleId][] = self::normalizeHistoryRow($row);
        }

        return $historyByVehicle;
    }

    private static function groupPlansByVehicle(array $plans)
    {
        $grouped = [];

        foreach ($plans as $plan) {
            $vehicleId = (int) $plan['veiculos_id'];
            if ($vehicleId <= 0) {
                continue;
            }

            if (!isset($grouped[$vehicleId])) {
                $grouped[$vehicleId] = [
                    'veiculos_id' => $vehicleId,
                    'vehicle_label' => $plan['vehicle_label'],
                    'plate' => $plan['plate'],
                    'brand' => $plan['brand'],
                    'model' => $plan['model'],
                    'hodometro' => $plan['hodometro'],
                    'plans' => [],
                ];
            }

            $grouped[$vehicleId]['plans'][] = $plan;
        }

        return $grouped;
    }

    private static function buildVehicleRow(array $group, array $history)
    {
        $status = self::resolveVehicleStatus($group['plans'], $group['hodometro'], $history);
        $lastHistory = !empty($history) ? $history[0] : null;
        $closestReference = self::findClosestReference($group['plans'], $group['hodometro']);

        return [
            'veiculos_id' => $group['veiculos_id'],
            'vehicle_label' => $group['vehicle_label'],
            'plate' => $group['plate'],
            'brand' => $group['brand'],
            'model' => $group['model'],
            'plan_label' => count($group['plans']) > 0 ? 'Com garantia' : 'Sem garantia',
            'plans_count' => count($group['plans']),
            'history_count' => count($history),
            'last_maintenance_display' => $lastHistory ? $lastHistory['data_display'] : 'Nao informado',
            'reference_display' => $closestReference,
            'status_label' => $status[0],
            'status_color' => $status[1],
        ];
    }

    private static function buildVehicleSummary(array $vehicles)
    {
        $summary = [
            'vehicles' => count($vehicles),
            'plans' => 0,
            'with_plan' => 0,
            'without_plan' => 0,
            'with_history' => 0,
            'without_history' => 0,
            'attention' => 0,
        ];

        foreach ($vehicles as $vehicle) {
            $summary['plans'] += (int) $vehicle['plans_count'];

            if ((int) $vehicle['plans_count'] > 0) {
                $summary['with_plan']++;
            } else {
                $summary['without_plan']++;
            }

            if ((int) $vehicle['history_count'] > 0) {
                $summary['with_history']++;
            } else {
                $summary['without_history']++;
            }

            if (in_array($vehicle['status_label'], ['Vencido', 'Sem historico'], true)) {
                $summary['attention']++;
            }
        }

        return $summary;
    }

    private static function normalizePlanRow(array $row)
    {
        $tipo = (string) ($row['tipo'] ?? '');

        return [
            'id' => (int) ($row['id'] ?? 0),
            'veiculos_id' => (int) ($row['veiculos_id'] ?? 0),
            'descricao' => trim((string) ($row['descricao'] ?? 'Plano sem descricao')),
            'tipo_label' => $tipo === '1' ? 'Servico' : ($tipo === '2' ? 'Produto' : 'Plano'),
            'qtde_display' => self::formatNumeric($row['qtde'] ?? null),
            'dias_display' => self::formatNumeric($row['dias_garantia'] ?? null),
            'km_display' => self::formatNumeric($row['km_manutencao'] ?? null),
            'data_display' => self::formatDate($row['datagarantia'] ?? null),
            'ativo_label' => self::normalizeBooleanLabel($row['ativo'] ?? null),
            'brand' => trim((string) ($row['marca_descricao'] ?? '')),
            'model' => trim((string) ($row['modelo_descricao'] ?? '')),
            'plate' => trim((string) ($row['placa'] ?? '-')),
            'vehicle_label' => self::buildVehicleLabel($row),
            'hodometro' => (float) ($row['hodometroatual'] ?? 0),
            'km_manutencao' => isset($row['km_manutencao']) ? (float) $row['km_manutencao'] : null,
            'dias_garantia' => isset($row['dias_garantia']) ? (float) $row['dias_garantia'] : null,
            'datagarantia' => $row['datagarantia'] ?? null,
            'ativo' => $row['ativo'] ?? null,
        ];
    }

    private static function normalizeHistoryRow(array $row)
    {
        return [
            'data_display' => self::formatDate($row['datamanutencao'] ?? null),
            'km_display' => self::formatNumeric($row['km'] ?? null),
            'servicos_produtos' => trim((string) ($row['servicos_produtos'] ?? 'Nao informado')),
            'valor_display' => self::formatMoney($row['valor'] ?? null),
            'obs' => trim((string) ($row['obs'] ?? '')),
            'timestamp' => self::parseDateToTimestamp($row['datamanutencao'] ?? null),
            'km' => isset($row['km']) ? (float) $row['km'] : null,
        ];
    }

    private static function resolveVehicleStatus(array $plans, $hodometro, array $history)
    {
        $hasExpired = false;
        $hasUpcoming = false;

        foreach ($plans as $plan) {
            $dateStatus = self::resolveDateStatus($plan['datagarantia']);
            $kmStatus = self::resolveKmStatus($hodometro, $plan['km_manutencao']);

            if (in_array('expired', [$dateStatus, $kmStatus], true)) {
                $hasExpired = true;
            } elseif (in_array('upcoming', [$dateStatus, $kmStatus], true)) {
                $hasUpcoming = true;
            }
        }

        if ($hasExpired) {
            return ['Vencido', '#dc2626'];
        }

        if ($hasUpcoming) {
            return ['Proximo', '#eab308'];
        }

        if (empty($history)) {
            return ['Sem historico', '#92400e'];
        }

        return ['Em dia', '#16a34a'];
    }

    private static function findClosestReference(array $plans, $hodometro)
    {
        $bestText = 'Nao informado';
        $bestScore = null;
        $today = strtotime(date('Y-m-d'));

        foreach ($plans as $plan) {
            if (!empty($plan['datagarantia'])) {
                $timestamp = self::parseDateToTimestamp($plan['datagarantia']);
                if ($timestamp) {
                    $score = abs($timestamp - $today);
                    if ($bestScore === null || $score < $bestScore) {
                        $bestScore = $score;
                        $bestText = $plan['data_display'];
                    }
                }
            }

            if (!empty($plan['km_manutencao']) && $hodometro > 0) {
                $score = abs((float) $plan['km_manutencao'] - (float) $hodometro);
                if ($bestScore === null || $score < $bestScore) {
                    $bestScore = $score;
                    $bestText = $plan['km_display'];
                }
            }
        }

        return $bestText;
    }

    private static function resolveDateStatus($date)
    {
        $timestamp = self::parseDateToTimestamp($date);
        if (!$timestamp) {
            return null;
        }

        $today = strtotime(date('Y-m-d'));
        $diffDays = floor(($timestamp - $today) / 86400);

        if ($diffDays < 0) {
            return 'expired';
        }

        if ($diffDays <= 30) {
            return 'upcoming';
        }

        return 'ok';
    }

    private static function resolveKmStatus($currentKm, $targetKm)
    {
        if (!$currentKm || !$targetKm) {
            return null;
        }

        $diff = (float) $targetKm - (float) $currentKm;

        if ($diff < 0) {
            return 'expired';
        }

        if ($diff <= 1000) {
            return 'upcoming';
        }

        return 'ok';
    }

    private static function buildVehicleLabel(array $row)
    {
        $prefixo = trim((string) ($row['prefixo'] ?? ''));
        $placa = trim((string) ($row['placa'] ?? ''));
        $modelo = trim((string) ($row['modelo_descricao'] ?? ''));

        $parts = array_filter([$prefixo, $placa, $modelo]);
        return !empty($parts) ? implode(' - ', $parts) : 'Veiculo sem identificacao';
    }

    private static function createEmptyVehicleGroup(array $vehicleRow)
    {
        return [
            'veiculos_id' => $vehicleRow['veiculos_id'],
            'vehicle_label' => $vehicleRow['vehicle_label'],
            'plate' => $vehicleRow['plate'],
            'brand' => $vehicleRow['brand'],
            'model' => $vehicleRow['model'],
            'hodometro' => $vehicleRow['hodometro'],
            'plans' => [],
        ];
    }

    private static function getConnectionName()
    {
        $unitDatabase = TSession::getValue('unit_database');
        return $unitDatabase ? $unitDatabase : 'minierp';
    }

    private static function tableExists(PDO $conn, $table)
    {
        $stmt = $conn->prepare('SHOW TABLES LIKE ?');
        $stmt->execute([$table]);
        return (bool) $stmt->fetch(PDO::FETCH_NUM);
    }

    private static function formatDate($value)
    {
        $timestamp = self::parseDateToTimestamp($value);
        return $timestamp ? date('d/m/Y', $timestamp) : 'Nao informado';
    }

    private static function parseDateToTimestamp($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim((string) $value);
        $formats = ['Y-m-d', 'Y-m-d H:i:s', 'd/m/Y', 'd-m-Y'];

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $value);
            if ($date instanceof DateTime) {
                return strtotime($date->format('Y-m-d'));
            }
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            return strtotime(substr($value, 0, 10));
        }

        return null;
    }

    private static function formatNumeric($value)
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if (!is_numeric((string) $value)) {
            return (string) $value;
        }

        return number_format((float) $value, 0, ',', '.');
    }

    private static function formatMoney($value)
    {
        if ($value === null || $value === '' || !is_numeric((string) $value)) {
            return '-';
        }

        return 'R$ ' . number_format((float) $value, 2, ',', '.');
    }

    private static function normalizeBooleanLabel($value)
    {
        if (in_array($value, ['S', 's', '1', 1, true, 'T', 't'], true)) {
            return 'Sim';
        }

        return 'Nao';
    }

    private static function guessLimit($width)
    {
        return max(8, (int) floor($width / 4.8));
    }

    private static function truncate($value, $limit)
    {
        $value = trim((string) $value);
        if (strlen($value) <= $limit) {
            return $value;
        }

        return substr($value, 0, $limit - 3) . '...';
    }

    private static function pdf($value)
    {
        return utf8_decode((string) $value);
    }

    private static function escape($value)
    {
        return htmlspecialchars((string) $value);
    }
}
