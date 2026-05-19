<?php

class ViewRelatorioTrocasProdutoVeiculoList extends TPage
{
    
    use BuilderDatagridTrait;
private $form;
    private $datagrid;
    private $datagrid_form;
    private $pageNavigation;
    private $loaded;
    private $limit = 20;

    private static $database = 'minierp';
    private static $formName = 'form_ViewRelatorioTrocasProdutoVeiculoList';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Relatorio de recorrencia de produtos/servicos por marca e modelo');

        $criteriaProduto = new TCriteria;
        $criteriaMarca = new TCriteria;
        $criteriaModelo = new TCriteria;

        $produto_id = new TDBUniqueSearch('produto_id', self::$database, 'Produto', 'id', 'nome', 'nome asc', $criteriaProduto);
        $marca_id = new TDBUniqueSearch('marca_id', self::$database, 'Marca', 'id', 'descricao', 'descricao asc', $criteriaMarca);
        $modelo_id = new TDBUniqueSearch('modelo_id', self::$database, 'Modelo', 'id', 'descricao', 'descricao asc', $criteriaModelo);
        $dt_pedido = new BDateRange('dt_pedido', 'dt_pedido_final');

        $produto_id->setMinLength(2);
        $produto_id->setMask('{nome}');
        $produto_id->setFilterColumns(['nome']);
        $marca_id->setMinLength(2);
        $modelo_id->setMinLength(2);
        $dt_pedido->setDatabaseMask('yyyy-mm-dd');
        $dt_pedido->setMask('dd/mm/yyyy');

        $produto_id->setSize('100%');
        $marca_id->setSize('100%');
        $modelo_id->setSize('100%');
        $dt_pedido->setSize(220);

        $row1 = $this->form->addFields(
            [new TLabel('Produto/Servico:', null, '14px', null, '100%'), $produto_id],
            [new TLabel('Periodo do pedido:', null, '14px', null, '100%'), $dt_pedido]
        );
        $row1->layout = ['col-sm-6', 'col-sm-6'];

        $row2 = $this->form->addFields(
            [new TLabel('Marca:', null, '14px', null, '100%'), $marca_id],
            [new TLabel('Modelo:', null, '14px', null, '100%'), $modelo_id]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $btn_onsearch = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $btn_onsearch->addStyleClass('btn-primary');
        $this->form->addAction('Limpar filtros', new TAction([$this, 'onClearFilters']), 'fas:eraser #dd5a43');
        $this->form->addAction('Atualizar', new TAction([$this, 'onRefresh']), 'fas:sync #478fca');

        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->disableHtmlConversion();

        $this->datagrid_form = new TForm('datagrid_' . self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_produto_servico_id = new TDataGridColumn('produto_servico_id', 'ID Produto', 'center', '90px');
        $column_produto_servico = new TDataGridColumn('produto_servico', 'Produto/Servico', 'left');
        $column_marca = new TDataGridColumn('marca', 'Marca', 'left');
        $column_modelo = new TDataGridColumn('modelo', 'Modelo', 'left');
        $column_qtd_trocas = new TDataGridColumn('qtd_trocas', 'Qtd trocas', 'center', '90px');
        $column_media_km = new TDataGridColumn('media_km', 'Media KM', 'right', '110px');
        $column_media_dias = new TDataGridColumn('media_dias', 'Media dias', 'right', '110px');

        $column_media_km->setTransformer(function ($value) {
            return $this->formatNumber($value, 0);
        });

        $column_media_dias->setTransformer(function ($value) {
            return $this->formatNumber($value, 1);
        });

        $this->datagrid->addColumn($column_produto_servico_id);
        $this->datagrid->addColumn($column_produto_servico);
        $this->datagrid->addColumn($column_marca);
        $this->datagrid->addColumn($column_modelo);
        $this->datagrid->addColumn($column_qtd_trocas);
        $this->datagrid->addColumn($column_media_km);
        $this->datagrid->addColumn($column_media_dias);

        $this->applyDatagridProperties();

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Recorrencia de produtos/servicos por marca e modelo');
        $panel->datagrid = 'datagrid-container';
        $panel->add($this->datagrid_form);
        $panel->getBody()->class .= ' table-responsive';
        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: flex-end;';

        $dropdown_button_exportar = new TDropDown('Exportar', 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction('CSV', new TAction([__CLASS__, 'onExportCsv'], ['static' => 1]), 'datagrid_' . self::$formName, 'fas:file-csv #00b894');
        $dropdown_button_exportar->addPostAction('XLS', new TAction([__CLASS__, 'onExportXls'], ['static' => 1]), 'datagrid_' . self::$formName, 'fas:file-excel #4CAF50');
        $dropdown_button_exportar->addPostAction('PDF', new TAction([__CLASS__, 'onExportPdf'], ['static' => 1]), 'datagrid_' . self::$formName, 'far:file-pdf #e74c3c');
        $headerActions->add($dropdown_button_exportar);

        $this->datagrid_form->add($headerActions);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);
    }

    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        $this->form->setData($data);

        $param['offset'] = 0;
        $param['first_page'] = 1;

        $this->onReload($param);
    }

    public function onClearFilters($param = null)
    {
        TSession::setValue(__CLASS__ . '_filter_data', null);
        $this->form->clear(true);
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onRefresh($param = null)
    {
        $this->onReload($param);
    }

    public function onReload($param = null)
    {
        try {
            TTransaction::open(self::$database);

            $conn = TTransaction::get();
            $offset = (int) ($param['offset'] ?? 0);
            $data = TSession::getValue(__CLASS__ . '_filter_data');
            $params = [];

            [$baseSql, $params] = $this->buildBaseSql($data);

            $countSql = "SELECT COUNT(*) AS total FROM ({$baseSql}) rel";
            $countStatement = $conn->prepare($countSql);
            $countStatement->execute($params);
            $count = (int) ($countStatement->fetchObject()->total ?? 0);

            $sql = $baseSql . ' ORDER BY media_km DESC, qtd_trocas DESC';
            if ($this->limit > 0) {
                $sql .= ' LIMIT :limit OFFSET :offset';
            }

            $statement = $conn->prepare($sql);
            foreach ($params as $key => $value) {
                $statement->bindValue($key, $value);
            }

            if ($this->limit > 0) {
                $statement->bindValue(':limit', $this->limit, PDO::PARAM_INT);
                $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            }

            $statement->execute();
            $objects = $statement->fetchAll(PDO::FETCH_OBJ);

            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param ?? []);
            $this->pageNavigation->setLimit($this->limit);

            TTransaction::close();
            $this->loaded = true;

            return $objects;
        } catch (Exception $e) {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }

            new TMessage('error', $e->getMessage());
        }
    }

    private function buildBaseSql($data = null)
    {
        $params = [
            ':estado_finalizado_pf' => EstadoPedidoFrotas::FINALIZADO,
        ];

        $where = [
            'pf.estado_pedido_frotas_id = :estado_finalizado_pf',
            'ip.deleted_at IS NULL',
            'p.deleted_at IS NULL',
            'pf.deleted_at IS NULL',
            'v.deleted_at IS NULL',
        ];

        $unitId = (int) (TSession::getValue('idunit') ?? TSession::getValue('userunitid') ?? 0);
        if ($unitId > 0) {
            $where[] = 'pf.system_unit_id = :system_unit_id';
            $params[':system_unit_id'] = $unitId;
        }

        if (!empty($data->produto_id)) {
            $where[] = 'ip.produto_id = :produto_id';
            $params[':produto_id'] = (int) $data->produto_id;
        }

        if (!empty($data->marca_id)) {
            $where[] = 'v.marca_id = :marca_id';
            $params[':marca_id'] = (int) $data->marca_id;
        }

        if (!empty($data->modelo_id)) {
            $where[] = 'v.modelo_id = :modelo_id';
            $params[':modelo_id'] = (int) $data->modelo_id;
        }

        if (!empty($data->dt_pedido)) {
            $where[] = 'DATE(pf.dt_pedido) >= :dt_pedido';
            $params[':dt_pedido'] = $data->dt_pedido;
        }

        if (!empty($data->dt_pedido_final)) {
            $where[] = 'DATE(pf.dt_pedido) <= :dt_pedido_final';
            $params[':dt_pedido_final'] = $data->dt_pedido_final;
        }

        $whereSql = implode("\n            AND ", $where);

        $sql = "
            SELECT
                COALESCE(ip.produto_id, '') AS produto_servico_id,
                COALESCE(NULLIF(prod.nome, ''), NULLIF(ip.descricao, ''), CONCAT('Item ', ip.id)) AS produto_servico,
                COALESCE(marca.descricao, '') AS marca,
                COALESCE(modelo.descricao, '') AS modelo,
                COUNT(*) AS qtd_trocas,
                AVG(NULLIF(COALESCE(p.km, pf.km), 0)) AS media_km,
                AVG(DATEDIFF(p.data_retirada_veiculo, p.data_entrada_veiculo)) AS media_dias
            FROM itens_propostas ip
            JOIN propostas p ON p.id = ip.propostas_id
            JOIN pedido_frotas pf ON pf.id = p.pedido_frotas_id
            JOIN veiculos v ON v.id = pf.veiculos_id
            LEFT JOIN produto prod ON prod.id = ip.produto_id
            LEFT JOIN marca marca ON marca.id = v.marca_id
            LEFT JOIN modelo modelo ON modelo.id = v.modelo_id
            WHERE {$whereSql}
            GROUP BY
                COALESCE(ip.produto_id, CONCAT('D-', MD5(UPPER(TRIM(COALESCE(ip.descricao, ip.id)))))),
                COALESCE(NULLIF(prod.nome, ''), NULLIF(ip.descricao, ''), CONCAT('Item ', ip.id)),
                marca.descricao,
                modelo.descricao
        ";

        return [$sql, $params];
    }

    public function onExportCsv($param = null)
    {
        try {
            $output = 'app/output/' . uniqid() . '.csv';
            if ((!file_exists($output) && is_writable(dirname($output))) || is_writable($output)) {
                $this->limit = 0;
                $objects = $this->onReload();

                if (!$objects) {
                    throw new Exception(_t('No records found'));
                }

                $handler = fopen($output, 'w');
                fputcsv($handler, ['ID Produto', 'Produto/Servico', 'Marca', 'Modelo', 'Qtd trocas', 'Media KM', 'Media dias']);

                foreach ($objects as $object) {
                    fputcsv($handler, [
                        $object->produto_servico_id,
                        $object->produto_servico,
                        $object->marca,
                        $object->modelo,
                        $object->qtd_trocas,
                        $object->media_km,
                        $object->media_dias,
                    ]);
                }

                fclose($handler);
                TPage::openFile($output);
            } else {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onExportXls($param = null)
    {
        try {
            $output = 'app/output/' . uniqid() . '.xls';
            if ((!file_exists($output) && is_writable(dirname($output))) || is_writable($output)) {
                $this->limit = 0;
                $objects = $this->onReload();

                if (!$objects) {
                    throw new Exception(_t('No records found'));
                }

                $table = new TTableWriterXLS([90, 260, 120, 160, 80, 100, 100]);
                $table->addStyle('title', 'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                $table->addStyle('data', 'Helvetica', '10', '', '#000000', '#FFFFFF', 'LR');
                $table->addRow();

                foreach (['ID Produto', 'Produto/Servico', 'Marca', 'Modelo', 'Qtd trocas', 'Media KM', 'Media dias'] as $title) {
                    $table->addCell($title, 'center', 'title');
                }

                foreach ($objects as $object) {
                    $table->addRow();
                    $table->addCell($object->produto_servico_id, 'center', 'data');
                    $table->addCell($object->produto_servico, 'left', 'data');
                    $table->addCell($object->marca, 'left', 'data');
                    $table->addCell($object->modelo, 'left', 'data');
                    $table->addCell($object->qtd_trocas, 'center', 'data');
                    $table->addCell($this->formatNumber($object->media_km, 0), 'right', 'data');
                    $table->addCell($this->formatNumber($object->media_dias, 1), 'right', 'data');
                }

                $table->save($output);
                TPage::openFile($output);
            } else {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onExportPdf($param = null)
    {
        try {
            $output = 'app/output/' . uniqid() . '.pdf';
            if ((!file_exists($output) && is_writable(dirname($output))) || is_writable($output)) {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->onReload();

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . file_get_contents('app/resources/styles-print-list.html') . PdfListHeader::render(__CLASS__) . $html->getContents();

                $dompdf = new Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('iframe');
                $object->src = $output;
                $object->type = 'application/pdf';
                $object->style = 'width: 100%; height: calc(100% - 10px)';
                $window->add($object);
                $window->show();
            } else {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    private function formatNumber($value, $decimals = 2)
    {
        if ($value === null || $value === '') {
            return '';
        }

        return number_format((float) $value, $decimals, ',', '.');
    }

    public function show()
    {
        if (!$this->loaded && (!isset($_GET['method']) || !in_array($_GET['method'], ['onReload', 'onSearch', 'onRefresh', 'onClearFilters']))) {
            $args = func_get_args();
            $this->onReload($args[0] ?? null);
        }

        parent::show();
    }

    public function onShow($param = null)
    {
    }
}
