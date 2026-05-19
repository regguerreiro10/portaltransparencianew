<?php

class ManutencaoGarantiaPopup extends TWindow
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Veiculos';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Veiculos';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters', 'onGlobalSearch'];
    private $limit = 20;
    private $cardFilterMap = [];

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();
        parent::setSize(0.8, null);
        parent::setTitle("Plano de Manutenção Preventiva de Veículos");
        parent::setProperty('class', 'window_modal');
        // creates the form

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->limit = 8;
        $this->cardFilterMap = $this->buildCardFilterMap();

        $id = new TEntry('id');
        $placa = new TEntry('placa');
        $searchUrl = 'class=ManutencaoGarantiaPopup&method=onSearch&static=1';
        if (!empty($param['target_container']))
        {
            $searchUrl .= '&target_container=' . $param['target_container'];
        }

        $id->exitOnEnter();
        $placa->exitOnEnter();
        $placa->setProperty('autocomplete', 'off');
        $placa->setProperty('onchange', "__adianti_post_data('" . self::$formName . "', '" . $searchUrl . "');");
        $placa->setProperty('onkeydown', "if (event.key === 'Enter') { __adianti_post_data('" . self::$formName . "', '" . $searchUrl . "'); return false; }");

        $id->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $placa->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $id->setSize('100%');
        $placa->setSize('100%');

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_placa = new TDataGridColumn('placa', "Placa", 'left');
        $column_garantia = new TDataGridColumn('plan_label', "Garantia", 'center');
        $column_planos = new TDataGridColumn('plans_count', "Planos", 'center');
        $column_tipo_preventiva = new TDataGridColumn('maintenance_type_label', "Tipo manutencao preventiva", 'center');
        $column_manutencoes = new TDataGridColumn('history_count', "Manutencoes feitas", 'center');
        $column_proxima_data = new TDataGridColumn('next_review_date_display', "Proxima revisao por data", 'center');
        $column_proxima_km = new TDataGridColumn('next_review_km_display', "Proxima revisao por km/horimetro", 'center');
        $column_ultima_manutencao = new TDataGridColumn('last_maintenance_display', "Ultima manutencao", 'center');
        $column_referencia = new TDataGridColumn('reference_display', "Referencia preventiva", 'center');
        $column_status_preventivo = new TDataGridColumn('preventive_status_label', "Status", 'center');
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_placa);
        $this->datagrid->addColumn($column_garantia);
        $this->datagrid->addColumn($column_planos);
        $this->datagrid->addColumn($column_tipo_preventiva);
        $this->datagrid->addColumn($column_manutencoes);
        $this->datagrid->addColumn($column_proxima_data);
        $this->datagrid->addColumn($column_proxima_km);
        $this->datagrid->addColumn($column_ultima_manutencao);
        $this->datagrid->addColumn($column_referencia);
        $this->datagrid->addColumn($column_status_preventivo);

        $column_status_preventivo->setTransformer(function($value, $object, $row, $cell = null, $last_row = null) {
            $badge = new TElement('span');
            $badge->style = "display:inline-block; min-width:110px; padding:6px 10px; border-radius:14px; color:#fff; font-weight:600; background:{$object->preventive_status_color};";
            $badge->add($value ?: 'Nao informado');
            return $badge;
        });

        $action_onDelete = new TDataGridAction(array('ManutencaoGarantiaPopup', 'onGeneratePdf'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("PDF");
        $action_onDelete->setImage('fas:file-pdf #dd5a43');
        $action_onDelete->setField('veiculos_id');

        $this->datagrid->addAction($action_onDelete);

        // create the datagrid model
        $this->datagrid->createModel();

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);

        if(!$action_onDelete->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        $td_id = TElement::tag('td', $id);
        $tr->add($td_id);
        $td_placa = TElement::tag('td', $placa);
        $tr->add($td_placa);
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));

        $this->datagrid_form->addField($id);
        $this->datagrid_form->addField($placa);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $panel->getHeader()->style = ' display:none !important; ';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $this->datagrid_form->add($headerActions);

        $content_wrapper = new TVBox;
        $content_wrapper->style = 'width: 100%';
        $content_wrapper->add($this->buildHeaderPanel());
        $content_wrapper->add($this->buildCardsPanel());
        $content_wrapper->add($this->datagrid_form);

        $panel->add($content_wrapper);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['ManutencaoGarantiaPopup', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['ManutencaoGarantiaPopup', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['ManutencaoGarantiaPopup', 'onExportCsv'],['static' => 1]), self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['ManutencaoGarantiaPopup', 'onExportXls'],['static' => 1]), self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['ManutencaoGarantiaPopup', 'onExportPdf'],['static' => 1]), self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['ManutencaoGarantiaPopup', 'onExportXml'],['static' => 1]), self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($button_atualizar);

        $head_right_actions->add($dropdown_button_exportar);

        $this->datagrid_form->add($this->datagrid);


        parent::add($panel);

    }

    private function buildHeaderPanel()
    {
        $wrapper = new TElement('div');
        $wrapper->style = 'margin-bottom: 12px;';

        $title = new TElement('div');
        $title->style = 'font-size: 22px; font-weight: 700; color: #1f2937; margin-bottom: 4px;';

        $subtitle = new TElement('div');
        $subtitle->style = 'font-size: 13px; color: #6b7280;';
        $subtitle->add('Previa com os primeiros 8 registros da tabela veiculos, indicando quem tem ou nao garantia cadastrada.');

        $wrapper->add($title);
        $wrapper->add($subtitle);

        return $wrapper;
    }

    private function buildCardsPanel()
    {
        $summary = $this->getCardsSummary();
        $activeCard = TSession::getValue(__CLASS__ . '_card_filter') ?: 'all';

        $style = new TElement('style');
        $style->add(
            '.mgp-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin:0 0 14px 0;}' .
            '.mgp-card{padding:14px 16px;border-radius:12px;border:1px solid #dfe5ef;background:#fff;transition:all .15s ease;}' .
            '.mgp-card:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(15,23,42,.08);}' .
            '.mgp-card.mgp-card-active{border:2px solid #1f2937;box-shadow:0 8px 20px rgba(15,23,42,.12);}' .
            '.mgp-card-label{font-size:12px;color:#6d7a90;margin-bottom:8px;}' .
            '.mgp-card-value{font-size:18px;line-height:1.1;font-weight:700;color:#1f2937;}'
        );
        parent::add($style);

        $wrap = new TElement('div');
        $wrap->class = 'mgp-cards';

        $cards = [
            ['all', 'Veiculos', $summary['total'], '#d8e1ed', '#2f3a4a'],
            ['plans', 'Planos cadastrados', $summary['planos_cadastrados'], '#d9e8ff', '#2563eb'],
            ['warranty_yes', 'Com garantia', $summary['com_garantia'], '#d9f6df', '#18794e'],
            ['warranty_no', 'Sem garantia', $summary['sem_garantia'], '#fff6bf', '#a16207'],
            ['history_yes', 'Com historico', $summary['com_historico'], '#d9f6e8', '#18794e'],
            ['history_no', 'Sem historico', $summary['sem_historico'], '#e6e0fb', '#7c3aed'],
            ['attention', 'Atencao', $summary['atencao'], '#f8d9dc', '#b42318'],
        ];

        foreach ($cards as $card)
        {
            $box = new TElement('a');
            $box->class = 'mgp-card' . ($activeCard === $card[0] ? ' mgp-card-active' : '');
            $box->style = 'background:' . $card[3] . ';display:block;text-decoration:none;cursor:pointer;';
            $box->href = 'engine.php?class=ManutencaoGarantiaPopup&method=onCardFilter&card=' . $card[0] . '&static=1';
            $box->{'generator'} = 'adianti';

            $label = new TElement('div');
            $label->class = 'mgp-card-label';
            $label->style = 'color:' . $card[4] . ';';
            $label->add($card[1]);

            $value = new TElement('div');
            $value->class = 'mgp-card-value';
            $value->style = 'color:' . $card[4] . ';';
            $value->add($card[2]);

            $box->add($label);
            $box->add($value);
            $wrap->add($box);
        }

        return $wrap;
    }

    public static function onGeneratePdf($param = null)
    {
        try
        {
            $vehicleId = isset($param['key']) ? (int) $param['key'] : (isset($param['veiculos_id']) ? (int) $param['veiculos_id'] : 0);
            if ($vehicleId <= 0)
            {
                throw new Exception('Veiculo nao informado para gerar o PDF.');
            }

            $instance = new self(['target_container' => $param['target_container'] ?? null]);
            $bundle = $instance->loadVehiclePdfBundle($vehicleId);

            $fileName = 'plano_preventivo_veiculo_' . $vehicleId . '_' . date('Ymd_His') . '.pdf';
            $filePath = 'app/output/' . $fileName;

            if (!file_exists('app/output'))
            {
                mkdir('app/output', 0777, true);
            }

            $pdf = new FPDF('P', 'pt', 'A4');
            $pdf->SetAutoPageBreak(true, 36);
            $pdf->AddPage();

            $instance->drawVehiclePdfHeader($pdf, $bundle);
            $instance->drawVehiclePdfSummary($pdf, $bundle);
            $instance->drawVehiclePdfTrackingPlan($pdf, $bundle);
            $instance->drawVehiclePdfGenericPlan($pdf);

            $pdf->Output($filePath, 'F');
            $downloadUrl = 'download.php?file=' . urlencode($filePath) . '&basename=' . urlencode($fileName);
            TScript::create("window.open('{$downloadUrl}', '_blank');");
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    private function buildCardFilterMap()
    {
        return [
            'plans' => 'EXISTS (SELECT 1 FROM manutencao_garantia mg WHERE mg.veiculos_id = v.id)',
            'warranty_yes' => "EXISTS (
                SELECT 1
                FROM manutencao_garantia mg
                WHERE mg.veiculos_id = v.id
                  AND (
                    (
                        mg.datagarantia IS NOT NULL
                        AND mg.dias_garantia IS NOT NULL
                        AND DATE_ADD(mg.datagarantia, INTERVAL mg.dias_garantia DAY) >= CURDATE()
                    )
                    OR mg.ativo = 'S'
                  )
            )",
            'warranty_no' => "NOT EXISTS (
                SELECT 1
                FROM manutencao_garantia mg
                WHERE mg.veiculos_id = v.id
                  AND (
                    (
                        mg.datagarantia IS NOT NULL
                        AND mg.dias_garantia IS NOT NULL
                        AND DATE_ADD(mg.datagarantia, INTERVAL mg.dias_garantia DAY) >= CURDATE()
                    )
                    OR mg.ativo = 'S'
                  )
            )",
            'history_yes' => "EXISTS (
                SELECT 1
                FROM manutencao_garantia mg
                WHERE mg.veiculos_id = v.id
                  AND (
                    (mg.obs IS NOT NULL AND TRIM(mg.obs) <> '')
                    OR (mg.updated_at IS NOT NULL AND mg.created_at IS NOT NULL AND mg.updated_at <> mg.created_at)
                  )
            )",
            'history_no' => "NOT EXISTS (
                SELECT 1
                FROM manutencao_garantia mg
                WHERE mg.veiculos_id = v.id
                  AND (
                    (mg.obs IS NOT NULL AND TRIM(mg.obs) <> '')
                    OR (mg.updated_at IS NOT NULL AND mg.created_at IS NOT NULL AND mg.updated_at <> mg.created_at)
                  )
            )",
            'attention' => "(
                NOT EXISTS (SELECT 1 FROM manutencao_garantia mg WHERE mg.veiculos_id = v.id)
                OR NOT EXISTS (
                    SELECT 1
                    FROM manutencao_garantia mg
                    WHERE mg.veiculos_id = v.id
                      AND (
                        (
                            mg.datagarantia IS NOT NULL
                            AND mg.dias_garantia IS NOT NULL
                            AND DATE_ADD(mg.datagarantia, INTERVAL mg.dias_garantia DAY) >= CURDATE()
                        )
                        OR mg.ativo = 'S'
                      )
                )
                OR NOT EXISTS (
                    SELECT 1
                    FROM manutencao_garantia mg
                    WHERE mg.veiculos_id = v.id
                      AND (
                        (mg.obs IS NOT NULL AND TRIM(mg.obs) <> '')
                        OR (mg.updated_at IS NOT NULL AND mg.created_at IS NOT NULL AND mg.updated_at <> mg.created_at)
                      )
                )
            )",
        ];
    }

    public function onCardFilter($param = null)
    {
        $card = $param['card'] ?? 'all';
        TSession::setValue(__CLASS__ . '_card_filter', $card === 'all' ? null : $card);

        $reload = ['offset' => 0, 'first_page' => 1];
        if (!empty($param['target_container'])) {
            $reload['target_container'] = $param['target_container'];
        }

        if (isset($param['static']) && $param['static'] == '1') {
            AdiantiCoreApplication::loadPage(get_class($this), 'onReload', $reload);
        } else {
            $this->onReload($reload);
        }
    }

    private function getCardFilterVehicleIds($conn, $cardFilter, $idunit)
    {
        if (empty($cardFilter) || !isset($this->cardFilterMap[$cardFilter]))
        {
            return null;
        }

        $sql = "
            SELECT v.id
            FROM veiculos v
            WHERE v.system_unit_id = :idunit
              AND {$this->cardFilterMap[$cardFilter]}
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':idunit' => $idunit]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function getCardsSummary()
    {
        $summary = [
            'total' => 0,
            'planos_cadastrados' => 0,
            'com_garantia' => 0,
            'sem_garantia' => 0,
            'com_historico' => 0,
            'sem_historico' => 0,
            'atencao' => 0,
        ];

        try
        {
            TTransaction::open(self::$database);
            $conn = TTransaction::get();
            $idunit = TSession::getValue('idunit');

            $summary['planos_cadastrados'] = $this->countPlanosCadastrados($conn, $idunit);

            $sql = "
                SELECT
                    v.id,
                    COUNT(mg.id) AS total_planos,
                    MAX(
                        CASE
                            WHEN (
                                (
                                    mg.datagarantia IS NOT NULL
                                    AND mg.dias_garantia IS NOT NULL
                                    AND DATE_ADD(mg.datagarantia, INTERVAL mg.dias_garantia DAY) >= CURDATE()
                                )
                                OR mg.ativo = 'S'
                            ) THEN 1
                            ELSE 0
                        END
                    ) AS tem_garantia,
                    MAX(
                        CASE
                            WHEN (
                                (mg.obs IS NOT NULL AND TRIM(mg.obs) <> '')
                                OR (mg.updated_at IS NOT NULL AND mg.created_at IS NOT NULL AND mg.updated_at <> mg.created_at)
                            ) THEN 1
                            ELSE 0
                        END
                    ) AS tem_historico
                FROM veiculos v
                LEFT JOIN manutencao_garantia mg ON mg.veiculos_id = v.id
                WHERE v.system_unit_id = :idunit
                GROUP BY v.id
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([':idunit' => $idunit]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row)
            {
                $summary['total']++;

                $tem_garantia = !empty($row['tem_garantia']);
                $tem_historico = !empty($row['tem_historico']);
                $tem_plano = !empty($row['total_planos']);

                if ($tem_garantia)
                {
                    $summary['com_garantia']++;
                }
                else
                {
                    $summary['sem_garantia']++;
                }

                if ($tem_historico)
                {
                    $summary['com_historico']++;
                }
                else
                {
                    $summary['sem_historico']++;
                }

                if (!$tem_plano || !$tem_garantia || !$tem_historico)
                {
                    $summary['atencao']++;
                }
            }

            TTransaction::close();
        }
        catch (Exception $e)
        {
            if (TTransaction::get())
            {
                TTransaction::rollback();
            }
        }

        return $summary;
    }

    private function countPlanosCadastrados($conn, $idunit)
    {
        try
        {
            $stmt = $conn->prepare("
                SELECT COUNT(*)
                FROM manutencao_garantia mg
                INNER JOIN veiculos v ON v.id = mg.veiculos_id
                WHERE v.system_unit_id = :idunit
            ");
            $stmt->execute([':idunit' => $idunit]);
            return (int) $stmt->fetchColumn();
        }
        catch (Exception $e)
        {
            return 0;
        }
    }

    private function tableExists($conn, $table)
    {
        $stmt = $conn->prepare('SHOW TABLES LIKE ?');
        $stmt->execute([$table]);
        return (bool) $stmt->fetch(PDO::FETCH_NUM);
    }

    private function normalizePlanRow(array $row)
    {
        $descricao = trim((string) ($row['produto_nome'] ?? ''));
        if ($descricao === '')
        {
            $descricao = trim((string) ($row['descricao'] ?? ''));
        }

        if ($descricao === '')
        {
            $descricao = 'Plano sem descricao';
        }

        return [
            'veiculos_id' => (int) ($row['veiculos_id'] ?? 0),
            'descricao' => $descricao,
            'tipo' => (string) ($row['tipo'] ?? ''),
            'pedido_frotas_id' => $row['pedido_frotas_id'] ?? null,
            'pedido_data' => $row['pedido_data'] ?? null,
            'pedido_data_display' => $this->formatDate($row['pedido_data'] ?? null),
            'pedido_km' => isset($row['pedido_km']) ? (float) $row['pedido_km'] : null,
            'km_manutencao' => isset($row['km_manutencao']) ? (float) $row['km_manutencao'] : null,
            'dias_garantia' => isset($row['dias_garantia']) ? (float) $row['dias_garantia'] : null,
            'datagarantia' => $row['datagarantia'] ?? null,
            'ativo' => $row['ativo'] ?? null,
            'km_display' => $this->formatNumeric($row['pedido_km'] ?? ($row['km_manutencao'] ?? null)),
            'data_display' => $this->formatDate($row['datagarantia'] ?? null),
        ];
    }

    private function normalizeHistoryRow(array $row)
    {
        $details = trim((string) ($row['servicos_produtos'] ?? ''));
        if ($details === '')
        {
            $details = trim((string) ($row['obs'] ?? 'Nao informado'));
        }

        return [
            'id' => (int) ($row['id'] ?? 0),
            'data_display' => $this->formatDate($row['datamanutencao'] ?? null),
            'timestamp' => $this->parseDateToTimestamp($row['datamanutencao'] ?? null),
            'km' => isset($row['km']) ? (float) $row['km'] : null,
            'km_display' => $this->formatNumeric($row['km'] ?? null),
            'obs' => trim((string) ($row['obs'] ?? 'Nao informado')),
            'details' => $details !== '' ? $details : 'Nao informado',
            'search_text' => $this->normalizeMatchText(
                trim((string) ($row['servicos_produtos'] ?? '')) . ' ' . trim((string) ($row['obs'] ?? ''))
            ),
        ];
    }

    private function fetchPlanRowsByVehicle($conn, array $vehicleIds)
    {
        if (empty($vehicleIds))
        {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($vehicleIds), '?'));
        $sql = "SELECT mg.*,
                       p.nome AS produto_nome,
                       pf.km AS pedido_km,
                       pf.dt_pedido AS pedido_data
                  FROM manutencao_garantia mg
             LEFT JOIN produto p ON p.id = mg.produto_id
             LEFT JOIN pedido_frotas pf ON pf.id = mg.pedido_frotas_id
                 WHERE mg.deleted_at IS NULL
                   AND mg.veiculos_id IN ({$placeholders})
              ORDER BY COALESCE(mg.datagarantia, '9999-12-31'), mg.id";

        $stmt = $conn->prepare($sql);
        $stmt->execute(array_values($vehicleIds));

        $plans = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
        {
            $vehicleId = (int) $row['veiculos_id'];
            $plans[$vehicleId][] = $this->normalizePlanRow($row);
        }

        return $plans;
    }

    private function fetchHistoryByVehicle($conn, array $vehicleIds)
    {
        if (empty($vehicleIds) || !$this->tableExists($conn, 'manutencao_veiculo'))
        {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($vehicleIds), '?'));
        $sql = "SELECT *
                  FROM manutencao_veiculo
                 WHERE veiculos_id IN ({$placeholders})
              ORDER BY COALESCE(datamanutencao, '1900-01-01') DESC, id DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute(array_values($vehicleIds));

        $history = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
        {
            $vehicleId = (int) $row['veiculos_id'];
            $history[$vehicleId][] = $this->normalizeHistoryRow($row);
        }

        return $history;
    }

    private function fetchLastMaintenanceByVehicle($conn, array $vehicleIds)
    {
        if (empty($vehicleIds) || !$this->tableExists($conn, 'pedido_frotas'))
        {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($vehicleIds), '?'));
        $sql = "SELECT pf.veiculos_id, MAX(pf.dt_pedido) AS ultima_data
                  FROM pedido_frotas pf
                 WHERE pf.veiculos_id IN ({$placeholders})
                   AND pf.dt_pedido IS NOT NULL
                   AND pf.deleted_at IS NULL
              GROUP BY pf.veiculos_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute(array_values($vehicleIds));

        $lastMaintenance = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
        {
            $vehicleId = (int) $row['veiculos_id'];
            $lastMaintenance[$vehicleId] = $this->formatDate($row['ultima_data'] ?? null);
        }

        return $lastMaintenance;
    }

    private function formatNumeric($value)
    {
        if ($value === null || $value === '')
        {
            return 'Nao informado';
        }

        if (!is_numeric($value))
        {
            return (string) $value;
        }

        return number_format((float) $value, 0, ',', '.');
    }

    private function formatDate($value)
    {
        $timestamp = $this->parseDateToTimestamp($value);
        return $timestamp ? date('d/m/Y', $timestamp) : 'Nao informado';
    }

    private function parseDateToTimestamp($value)
    {
        if (empty($value))
        {
            return null;
        }

        $timestamp = strtotime((string) $value);
        return $timestamp ?: null;
    }

    private function normalizeMatchText($value)
    {
        $value = strtolower((string) $value);
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value);
        $value = preg_replace('/\s+/', ' ', (string) $value);
        return trim((string) $value);
    }

    private function extractPlanKeywords($descricao)
    {
        $text = $this->normalizeMatchText($descricao);
        if ($text === '')
        {
            return [];
        }

        $stopWords = [
            'troca', 'de', 'da', 'do', 'das', 'dos', 'para', 'com', 'sem',
            'kit', 'jogo', 'item', 'itens', 'servico', 'servicos', 'produto',
            'produtos', 'manutencao', 'preventiva', 'preventivo', 'veiculo',
            'revisao', 'sistema', 'conjunto', 'geral'
        ];

        $tokens = preg_split('/\s+/', $text);
        $keywords = [];

        foreach ($tokens as $token)
        {
            if (strlen($token) < 4 || in_array($token, $stopWords, true))
            {
                continue;
            }

            $keywords[$token] = $token;
        }

        return array_values($keywords);
    }

    private function findMatchingHistoryForPlan(array $plan, array $history)
    {
        if (empty($history))
        {
            return null;
        }

        $keywords = $this->extractPlanKeywords($plan['descricao'] ?? '');
        $bestMatch = null;
        $bestScore = 0;
        $planTimestamp = $this->parseDateToTimestamp($plan['pedido_data'] ?? null);

        foreach ($history as $item)
        {
            $score = 0;
            $searchText = $item['search_text'] ?? '';

            foreach ($keywords as $keyword)
            {
                if ($keyword !== '' && strpos($searchText, $keyword) !== false)
                {
                    $score += 10;
                }
            }

            if (!empty($plan['pedido_frotas_id']) && !empty($item['timestamp']) && !empty($planTimestamp) && $item['timestamp'] >= $planTimestamp)
            {
                $score += 2;
            }

            if (!empty($plan['km_manutencao']) && !empty($item['km']))
            {
                $diffKm = abs((float) $item['km'] - (float) $plan['km_manutencao']);
                if ($diffKm <= 1000)
                {
                    $score += 1;
                }
            }

            if ($score > $bestScore)
            {
                $bestScore = $score;
                $bestMatch = $item;
            }
        }

        return $bestScore >= 10 ? $bestMatch : null;
    }

    private function resolvePdfStatusMeta($status)
    {
        $status = (string) $status;

        if ($status === 'Realizada')
        {
            return ['fill' => [22, 163, 74], 'text' => [255, 255, 255]];
        }

        if ($status === 'Proximo')
        {
            return ['fill' => [234, 179, 8], 'text' => [17, 24, 39]];
        }

        if ($status === 'Atrasado')
        {
            return ['fill' => [220, 38, 38], 'text' => [255, 255, 255]];
        }

        if ($status === 'Em dia')
        {
            return ['fill' => [59, 130, 246], 'text' => [255, 255, 255]];
        }

        return ['fill' => [107, 114, 128], 'text' => [255, 255, 255]];
    }

    private function buildPdfTrackingRows(array $plans, array $history, $hodometroAtual)
    {
        $rows = [];

        foreach ($plans as $plan)
        {
            $simple = $this->buildSimplePlanPdfRow($plan, $hodometroAtual);
            $matchedHistory = $this->findMatchingHistoryForPlan($plan, $history);
            $status = $matchedHistory ? 'Realizada' : $simple['status'];
            $statusMeta = $this->resolvePdfStatusMeta($status);

            $rows[] = [
                'descricao' => $plan['descricao'] ?? 'Plano',
                'intervalo' => $simple['intervalo'],
                'ultima_execucao' => $matchedHistory
                    ? trim($matchedHistory['data_display'] . ' / ' . ($matchedHistory['km_display'] ?? 'Nao informado'))
                    : $simple['ultima_execucao'],
                'proxima_execucao' => $matchedHistory ? 'Conferir novo ciclo' : $simple['proxima_execucao'],
                'historico' => $matchedHistory['details'] ?? 'Ainda nao localizada no historico',
                'status' => $status,
                'status_fill' => $statusMeta['fill'],
                'status_text' => $statusMeta['text'],
            ];
        }

        return $rows;
    }

    private function buildPdfTrackingSummary(array $trackingRows)
    {
        $summary = [
            'realizadas' => 0,
            'proximas' => 0,
            'atrasadas' => 0,
            'em_dia' => 0,
            'nao_informadas' => 0,
        ];

        foreach ($trackingRows as $row)
        {
            switch ($row['status'])
            {
                case 'Realizada':
                    $summary['realizadas']++;
                    break;
                case 'Proximo':
                    $summary['proximas']++;
                    break;
                case 'Atrasado':
                    $summary['atrasadas']++;
                    break;
                case 'Em dia':
                    $summary['em_dia']++;
                    break;
                default:
                    $summary['nao_informadas']++;
                    break;
            }
        }

        return $summary;
    }

    private function resolveDateStatus($date)
    {
        $timestamp = $this->parseDateToTimestamp($date);
        if (!$timestamp)
        {
            return null;
        }

        $today = strtotime(date('Y-m-d'));
        $diffDays = floor(($timestamp - $today) / 86400);

        if ($diffDays < 0)
        {
            return 'expired';
        }

        if ($diffDays <= 30)
        {
            return 'upcoming';
        }

        return 'ok';
    }

    private function resolveKmStatus($currentKm, $targetKm)
    {
        if (!$currentKm || !$targetKm)
        {
            return null;
        }

        $diff = (float) $targetKm - (float) $currentKm;

        if ($diff < 0)
        {
            return 'expired';
        }

        if ($diff <= 1000)
        {
            return 'upcoming';
        }

        return 'ok';
    }

    private function resolveVehicleStatus(array $plans, $hodometro, array $history)
    {
        $hasExpired = false;
        $hasUpcoming = false;

        foreach ($plans as $plan)
        {
            $dateStatus = $this->resolveDateStatus($plan['datagarantia']);
            $kmStatus = $this->resolveKmStatus($hodometro, $plan['km_manutencao']);

            if (in_array('expired', [$dateStatus, $kmStatus], true))
            {
                $hasExpired = true;
            }
            elseif (in_array('upcoming', [$dateStatus, $kmStatus], true))
            {
                $hasUpcoming = true;
            }
        }

        if ($hasExpired)
        {
            return ['Vencido', '#dc2626'];
        }

        if ($hasUpcoming)
        {
            return ['Proximo', '#eab308'];
        }

        if (empty($history))
        {
            return ['Sem historico', '#92400e'];
        }

        return ['Em dia', '#16a34a'];
    }

    private function findClosestReference(array $plans, $hodometro)
    {
        $bestText = 'Nao informado';
        $bestScore = null;
        $today = strtotime(date('Y-m-d'));

        foreach ($plans as $plan)
        {
            if (!empty($plan['datagarantia']))
            {
                $timestamp = $this->parseDateToTimestamp($plan['datagarantia']);
                if ($timestamp)
                {
                    $score = abs($timestamp - $today);
                    if ($bestScore === null || $score < $bestScore)
                    {
                        $bestScore = $score;
                        $bestText = $plan['data_display'];
                    }
                }
            }

            if (!empty($plan['km_manutencao']) && $hodometro > 0)
            {
                $score = abs((float) $plan['km_manutencao'] - (float) $hodometro);
                if ($bestScore === null || $score < $bestScore)
                {
                    $bestScore = $score;
                    $bestText = $plan['km_display'];
                }
            }
        }

        return $bestText;
    }

    private function findNextReviewDate(array $plans)
    {
        $bestTimestamp = null;
        $bestText = 'Nao informado';

        foreach ($plans as $plan)
        {
            $timestamp = $this->parseDateToTimestamp($plan['datagarantia'] ?? null);
            if ($timestamp && ($bestTimestamp === null || $timestamp < $bestTimestamp))
            {
                $bestTimestamp = $timestamp;
                $bestText = $plan['data_display'] ?? date('d/m/Y', $timestamp);
            }
        }

        return $bestText;
    }

    private function findNextReviewKm(array $plans, $hodometro)
    {
        $bestKm = null;
        $bestText = 'Nao informado';

        foreach ($plans as $plan)
        {
            $targetKm = $plan['km_manutencao'] ?? null;
            if (!$targetKm)
            {
                continue;
            }

            if ($bestKm === null)
            {
                $bestKm = $targetKm;
                $bestText = $plan['km_display'] ?? $this->formatNumeric($targetKm);
                continue;
            }

            $currentDiff = abs((float) $bestKm - (float) $hodometro);
            $newDiff = abs((float) $targetKm - (float) $hodometro);
            if ($newDiff < $currentDiff)
            {
                $bestKm = $targetKm;
                $bestText = $plan['km_display'] ?? $this->formatNumeric($targetKm);
            }
        }

        return $bestText;
    }

    private function detectMaintenanceType(array $plans)
    {
        if (empty($plans))
        {
            return 'Nao informado';
        }

        $types = [];
        foreach ($plans as $plan)
        {
            $descricao = strtolower((string) ($plan['descricao'] ?? ''));
            if (strpos($descricao, 'oleo') !== false)
            {
                $types[] = 'Troca de oleo';
            }
            elseif (strpos($descricao, 'filtro') !== false)
            {
                $types[] = 'Filtro';
            }
            elseif (!empty($plan['tipo']) && $plan['tipo'] === '2')
            {
                $types[] = 'Produto';
            }
            else
            {
                $types[] = 'Preventiva';
            }
        }

        $types = array_values(array_unique($types));
        return implode(', ', array_slice($types, 0, 2));
    }

    private function countExecutedMaintenances(array $plans)
    {
        $pedidoIds = [];

        foreach ($plans as $plan)
        {
            $pedidoId = $plan['pedido_frotas_id'] ?? null;
            if ($pedidoId !== null && $pedidoId !== '' && $pedidoId != 0)
            {
                $pedidoIds[] = (string) $pedidoId;
            }
        }

        return count(array_unique($pedidoIds));
    }

    private function loadVehicleIndicators($conn, $objects)
    {
        $vehicleIds = [];
        foreach ((array) $objects as $object)
        {
            $vehicleIds[] = (int) $object->id;
        }

        $vehicleIds = array_values(array_unique(array_filter($vehicleIds)));
        if (empty($vehicleIds))
        {
            return [];
        }

        $plansByVehicle = $this->fetchPlanRowsByVehicle($conn, $vehicleIds);
        $historyByVehicle = $this->fetchHistoryByVehicle($conn, $vehicleIds);
        $lastMaintenanceByVehicle = $this->fetchLastMaintenanceByVehicle($conn, $vehicleIds);
        $indicators = [];

        foreach ($vehicleIds as $vehicleId)
        {
            $plans = $plansByVehicle[$vehicleId] ?? [];
            $history = $historyByVehicle[$vehicleId] ?? [];
            $executedMaintenances = $this->countExecutedMaintenances($plans);
            $hodometro = 0;

            foreach ($objects as $object)
            {
                if ((int) $object->id === $vehicleId)
                {
                    $hodometro = (float) ($object->hodometroatual ?? 0);
                    break;
                }
            }

            $status = $this->resolveVehicleStatus($plans, $hodometro, $history);
            $indicators[$vehicleId] = [
                'plan_label' => count($plans) > 0 ? 'Com garantia' : 'Sem garantia',
                'plans_count' => count($plans),
                'maintenance_type_label' => $this->detectMaintenanceType($plans),
                'history_count' => $executedMaintenances,
                'next_review_date_display' => $this->findNextReviewDate($plans),
                'next_review_km_display' => $this->findNextReviewKm($plans, $hodometro),
                'last_maintenance_display' => $lastMaintenanceByVehicle[$vehicleId] ?? 'Nao informado',
                'reference_display' => $this->findClosestReference($plans, $hodometro),
                'preventive_status_label' => $status[0],
                'preventive_status_color' => $status[1],
            ];
        }

        return $indicators;
    }

    private function loadVehiclePdfBundle($vehicleId)
    {
        TTransaction::open(self::$database);
        $conn = TTransaction::get();

        try
        {
            $vehicle = new Veiculos($vehicleId, FALSE);
            if (!$vehicle || empty($vehicle->id))
            {
                throw new Exception('Veiculo nao encontrado para gerar o PDF.');
            }

            $plansByVehicle = $this->fetchPlanRowsByVehicle($conn, [$vehicleId]);
            $historyByVehicle = $this->fetchHistoryByVehicle($conn, [$vehicleId]);
            $lastMaintenanceByVehicle = $this->fetchLastMaintenanceByVehicle($conn, [$vehicleId]);

            $plans = $plansByVehicle[$vehicleId] ?? [];
            $history = $historyByVehicle[$vehicleId] ?? [];
            $status = $this->resolveVehicleStatus($plans, (float) ($vehicle->hodometroatual ?? 0), $history);
            $executedMaintenances = $this->countExecutedMaintenances($plans);
            $trackingRows = $this->buildPdfTrackingRows($plans, $history, (float) ($vehicle->hodometroatual ?? 0));
            $trackingSummary = $this->buildPdfTrackingSummary($trackingRows);

            $bundle = [
                'vehicle' => [
                    'id' => (int) $vehicle->id,
                    'vehicle_label' => trim((string) (($vehicle->prefixo ?? '') . ' - ' . ($vehicle->placa ?? '')) , ' -'),
                    'plate' => (string) ($vehicle->placa ?? '-'),
                    'brand' => (string) ($vehicle->marca->descricao ?? ''),
                    'model' => (string) ($vehicle->modelo->descricao ?? ''),
                    'hodometro_atual' => (float) ($vehicle->hodometroatual ?? 0),
                    'plans_count' => count($plans),
                    'history_count' => $executedMaintenances,
                    'plan_label' => count($plans) > 0 ? 'Com garantia' : 'Sem garantia',
                    'status_label' => $status[0],
                    'last_maintenance_display' => $lastMaintenanceByVehicle[$vehicleId] ?? 'Nao informado',
                ],
                'plans' => $plans,
                'history' => $history,
                'tracking_rows' => $trackingRows,
                'tracking_summary' => $trackingSummary,
            ];

            TTransaction::close();
            return $bundle;
        }
        catch (Exception $e)
        {
            if (TTransaction::get())
            {
                TTransaction::rollback();
            }
            throw $e;
        }
    }

    private function drawVehiclePdfHeader($pdf, array $bundle)
    {
        $vehicle = $bundle['vehicle'];
        $logoPath = 'app/images/logo.png';
        $titleX = 28;
        $headerTop = 12;

        if (file_exists($logoPath))
        {
            try
            {
                $pdf->Image($logoPath, 25, $headerTop, 80);
                $titleX = 126;
            }
            catch (Exception $e)
            {
                $titleX = 28;
            }
        }

        $pdf->SetTextColor(31, 41, 55);
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetXY($titleX, 22);
        $pdf->Cell(568 - $titleX, 20, $this->pdfText('Plano de Manutenção Preventiva'), 0, 1, 'L');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX($titleX);
        $pdf->Cell(568 - $titleX, 14, $this->pdfText('Veículo: ' . $vehicle['id']), 0, 1, 'L');
        $pdf->SetX($titleX);
        $pdf->Cell(568 - $titleX, 14, $this->pdfText('Placa: ' . $vehicle['plate'] . '   Modelo: ' . $vehicle['model']), 0, 1, 'L');
        $pdf->SetX($titleX);
        $pdf->Cell(568 - $titleX, 14, $this->pdfText('Marca: ' . $vehicle['brand'] . '   Data de emissão: ' . date('d/m/Y H:i')), 0, 1, 'L');
        $pdf->Ln(6);
    }

    private function drawVehiclePdfSummary($pdf, array $bundle)
    {
        $vehicle = $bundle['vehicle'];

        $pdf->SetFillColor(241, 245, 249);
        $top = $pdf->GetY() + 4;
        $pdf->Rect(28, $top, 540, 32, 'F');
        $y = $top + 8;

        $pdf->SetXY(40, $y);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(160, 12, $this->pdfText('Planos cadastrados: ' . $vehicle['plans_count']), 0, 0, 'L');
        $pdf->Cell(180, 12, $this->pdfText('Manutenções feitas: ' . $vehicle['history_count']), 0, 0, 'L');
        $pdf->Cell(160, 12, $this->pdfText('Garantia: ' . $vehicle['plan_label']), 0, 1, 'L');
        $pdf->SetY($top + 38);
    }

    private function drawVehiclePdfTrackingPlan($pdf, array $bundle)
    {
        if ($pdf->GetY() > 620)
        {
            $pdf->AddPage();
        }

        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->Cell(0, 18, $this->pdfText('Manutenções já realizadas'), 0, 1, 'L');

        $columns = [
            ['label' => 'Item', 'width' => 28, 'align' => 'C'],
            ['label' => 'Manutenção prevista', 'width' => 148, 'align' => 'L'],
            ['label' => 'Intervalo', 'width' => 64, 'align' => 'C'],
            ['label' => 'Ultima / KM', 'width' => 82, 'align' => 'C'],
            ['label' => 'Próxima', 'width' => 82, 'align' => 'C'],
            ['label' => 'Histórico localizado', 'width' => 100, 'align' => 'L'],
            ['label' => 'Status', 'width' => 40, 'align' => 'C'],
        ];

        $this->drawSimpleTableHeader($pdf, $columns);

        if (empty($bundle['tracking_rows']))
        {
            $this->drawSimpleRow($pdf, [['text' => 'Nenhum plano preventivo encontrado para este veículo.', 'width' => 544, 'align' => 'L']]);
            return;
        }

        $i = 1;
        foreach ($bundle['tracking_rows'] as $row)
        {
            $this->drawSimpleRow($pdf, [
                ['text' => $i, 'width' => 28, 'align' => 'C'],
                ['text' => $row['descricao'], 'width' => 148, 'align' => 'L'],
                ['text' => $row['intervalo'], 'width' => 64, 'align' => 'C'],
                ['text' => $row['ultima_execucao'], 'width' => 82, 'align' => 'C'],
                ['text' => $row['proxima_execucao'], 'width' => 82, 'align' => 'C'],
                ['text' => $row['historico'], 'width' => 100, 'align' => 'L'],
                ['text' => $row['status'], 'width' => 40, 'align' => 'C', 'fill' => $row['status_fill'], 'text_color' => $row['status_text']],
            ]);
            $i++;
        }

        $pdf->Ln(10);
    }

    private function drawVehiclePdfGenericPlan($pdf)
    {
        if ($pdf->GetY() > 520)
        {
            $pdf->AddPage();
        }

        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->Cell(0, 20, $this->pdfText('Plano de Manutenção Preventiva (Tabela Genérica)'), 0, 1, 'L');

        $columns = [
            ['label' => 'Item / Sistema', 'width' => 120, 'align' => 'L'],
            ['label' => 'Tipo de Manutenção', 'width' => 100, 'align' => 'L'],
            ['label' => 'Periodicidade', 'width' => 80, 'align' => 'C'],
            ['label' => 'Controle (KM / Tempo)', 'width' => 130, 'align' => 'C'],
            ['label' => 'Descrição', 'width' => 114, 'align' => 'L'],
        ];

        $rows = [
            ['Óleo do motor', 'Troca', '10.000 km', 'KM', 'Troca do óleo e filtro'],
            ['Filtro de ar', 'Troca', '10.000 km', 'KM', 'Substituição do filtro'],
            ['Filtro de combustível', 'Troca', '20.000 km', 'KM', 'Limpeza ou substituição'],
            ['Pastilhas de freio', 'Verificação', '5.000 km', 'KM', 'Checagem de desgaste'],
            ['Pneus', 'Rodízio', '10.000 km', 'KM', 'Rodízio e calibragem'],
            ['Correia dentada', 'Troca', '60.000 km', 'KM', 'Substituição completa'],
            ['Bateria', 'Verificação', '6 meses', 'Tempo', 'Teste de carga'],
            ['Sistema de freio', 'Revisão', '6 meses', 'Tempo', 'Fluido e inspeção geral'],
        ];

        $this->drawSimpleTableHeader($pdf, $columns);

        foreach ($rows as $row)
        {
            $this->drawSimpleRow($pdf, [
                ['text' => $row[0], 'width' => 120, 'align' => 'L'],
                ['text' => $row[1], 'width' => 100, 'align' => 'L'],
                ['text' => $row[2], 'width' => 80, 'align' => 'C'],
                ['text' => $row[3], 'width' => 130, 'align' => 'C'],
                ['text' => $row[4], 'width' => 114, 'align' => 'L'],
            ]);
        }
    }

    private function buildSimplePlanPdfRow(array $plan, $hodometroAtual)
    {
        $hasKmControl = !empty($plan['km_manutencao']);
        $hasDateControl = !empty($plan['dias_garantia']) || !empty($plan['datagarantia']);

        if ($hasKmControl && $hasDateControl)
        {
            $tipoControle = 'KM/Tempo';
        }
        elseif ($hasKmControl)
        {
            $tipoControle = 'KM';
        }
        elseif ($hasDateControl)
        {
            $tipoControle = 'Tempo';
        }
        else
        {
            $tipoControle = 'Nao informado';
        }

        if (!empty($plan['km_manutencao']))
        {
            $intervalo = $this->formatNumeric($plan['km_manutencao']) . ' km';
        }
        elseif (!empty($plan['dias_garantia']))
        {
            $intervalo = $this->formatNumeric($plan['dias_garantia']) . ' dias';
        }
        else
        {
            $intervalo = 'Nao informado';
        }

        if ($hasKmControl)
        {
            $ultimaExecucao = $plan['km_display'] ?? 'Nao informado';
            $proximaExecucao = 'Nao informado';

            if (!empty($plan['pedido_km']) && !empty($plan['km_manutencao']))
            {
                $proximaExecucao = $this->formatNumeric((float) $plan['pedido_km'] + (float) $plan['km_manutencao']) . ' km';
            }
            elseif (!empty($plan['km_manutencao']))
            {
                $proximaExecucao = $this->formatNumeric($plan['km_manutencao']) . ' km';
            }

            $status = $this->resolveSimplePlanStatus($plan, $hodometroAtual);
        }
        else
        {
            $ultimaExecucao = $plan['pedido_data_display'] ?? 'Nao informado';
            $proximaExecucao = $plan['data_display'] ?? 'Nao informado';
            $status = $this->resolveSimplePlanStatus($plan, $hodometroAtual);
        }

        return [
            'descricao' => $plan['descricao'] ?? 'Plano',
            'tipo_controle' => $tipoControle,
            'intervalo' => $intervalo,
            'ultima_execucao' => $ultimaExecucao,
            'proxima_execucao' => $proximaExecucao,
            'status' => $status,
        ];
    }

    private function resolveSimplePlanStatus(array $plan, $hodometroAtual)
    {
        $dateStatus = $this->resolveDateStatus($plan['datagarantia'] ?? null);

        $kmTarget = null;
        if (!empty($plan['pedido_km']) && !empty($plan['km_manutencao']))
        {
            $kmTarget = (float) $plan['pedido_km'] + (float) $plan['km_manutencao'];
        }
        elseif (!empty($plan['km_manutencao']))
        {
            $kmTarget = (float) $plan['km_manutencao'];
        }

        $kmStatus = $this->resolveKmStatus($hodometroAtual, $kmTarget);

        if (in_array('expired', [$dateStatus, $kmStatus], true))
        {
            return 'Atrasado';
        }

        if (in_array('upcoming', [$dateStatus, $kmStatus], true))
        {
            return 'Proximo';
        }

        if ($dateStatus || $kmStatus)
        {
            return 'Em dia';
        }

        return 'Nao informado';
    }

    private function drawSimpleTableHeader($pdf, array $columns)
    {
        $pdf->SetFillColor(15, 23, 42);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);

        foreach ($columns as $column)
        {
            $pdf->Cell($column['width'], 18, $this->pdfText($column['label']), 1, 0, $column['align'], true);
        }
        $pdf->Ln();
        $pdf->SetTextColor(31, 41, 55);
    }

    private function drawSimpleRow($pdf, array $cells)
    {
        $pdf->SetFont('Arial', '', 8);
        foreach ($cells as $cell)
        {
            $text = is_scalar($cell['text']) ? (string) $cell['text'] : '';
            $fill = !empty($cell['fill']) && is_array($cell['fill']);
            if ($fill)
            {
                $pdf->SetFillColor($cell['fill'][0], $cell['fill'][1], $cell['fill'][2]);
            }

            if (!empty($cell['text_color']) && is_array($cell['text_color']))
            {
                $pdf->SetTextColor($cell['text_color'][0], $cell['text_color'][1], $cell['text_color'][2]);
            }
            else
            {
                $pdf->SetTextColor(31, 41, 55);
            }

            $pdf->Cell($cell['width'], 18, $this->pdfText($this->truncatePdfText($text, $this->guessLimit($cell['width']))), 1, 0, $cell['align'], $fill);
        }
        $pdf->SetTextColor(31, 41, 55);
        $pdf->Ln();
    }

    private function truncatePdfText($value, $limit)
    {
        if (strlen($value) <= $limit)
        {
            return $value;
        }

        return substr($value, 0, $limit - 3) . '...';
    }

    private function guessLimit($width)
    {
        return max(8, (int) floor($width / 4.8));
    }

    private function pdfText($value)
    {
        return utf8_decode((string) $value);
    }

    public function onDelete($param = null) 
    { 
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);

                // instantiates object
                $object = new Veiculos($key, FALSE); 

                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
    }
    public function onExportCsv($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    $handler = fopen($output, 'w');
                    TTransaction::open(self::$database);

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
                $objects = $this->onReload();

                TTransaction::open(self::$database);
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

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->onReload();

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

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
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
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
                $objects = $this->onReload();

                if ($objects)
                {
                    TTransaction::open(self::$database);

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->{'formatOutput'} = true;
                    $dataset = $dom->appendChild( $dom->createElement('dataset') );

                    foreach ($objects as $object)
                    {
                        $row = $dataset->appendChild( $dom->createElement( self::$activeRecord ) );

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
    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);
        TSession::setValue(__CLASS__.'_card_filter', NULL);

        if(!empty($this->form))
        {
            $this->form->clear();
        }

        if(!empty($this->datagrid_form))
        {
            $this->datagrid_form->clear();
        }

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }
    public function onRefresh($param = null) 
    {
        $this->onReload([]);
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        // get the search form data
        $data = $this->datagrid_form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->placa) AND ( (is_scalar($data->placa) AND $data->placa !== '') OR (is_array($data->placa) AND (!empty($data->placa)) )) )
        {

            $filters[] = new TFilter('placa', 'like', "%{$data->placa}%");// create the filter 
        }

        // fill the form with data again
        $this->datagrid_form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        if (isset($param['static']) && ($param['static'] == '1') )
        {
            $class = get_class($this);
            $onReloadParam = ['offset' => 0, 'first_page' => 1, 'target_container' => $param['target_container'] ?? null];
            AdiantiCoreApplication::loadPage($class, 'onReload', $onReloadParam);
            TScript::create('$(".select2").prev().select2("close");');
        }
        else
        {
            $this->onReload(['offset' => 0, 'first_page' => 1]);
        }
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'minierp'
            TTransaction::open(self::$database);

            // creates a repository for Veiculos
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
            }
            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            $idunit = TSession::getValue('idunit');
            $criteria->add(new TFilter('system_unit_id', '=', $idunit));

            $cardFilter = TSession::getValue(__CLASS__.'_card_filter');
            $cardIds = $this->getCardFilterVehicleIds(TTransaction::get(), $cardFilter, $idunit);
            if (is_array($cardIds))
            {
                if (empty($cardIds))
                {
                    $criteria->add(new TFilter('id', '=', -1));
                }
                else
                {
                    $criteria->add(new TFilter('id', 'in', $cardIds));
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            $indicators = $this->loadVehicleIndicators(TTransaction::get(), $objects);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    $vehicleIndicators = $indicators[$object->id] ?? [
                        'plan_label' => 'Sem garantia',
                        'plans_count' => 0,
                        'maintenance_type_label' => 'Nao informado',
                        'history_count' => 0,
                        'next_review_date_display' => 'Nao informado',
                        'next_review_km_display' => 'Nao informado',
                        'last_maintenance_display' => 'Nao informado',
                        'reference_display' => 'Nao informado',
                        'preventive_status_label' => 'Sem historico',
                        'preventive_status_color' => '#92400e',
                    ];

                    foreach ($vehicleIndicators as $property => $value)
                    {
                        $object->{$property} = $value;
                    }
                    $object->veiculos_id = $object->id;

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;

            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public static function manageRow($id, $param = [])
    {
        $list = new self($param);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Veiculos($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}
