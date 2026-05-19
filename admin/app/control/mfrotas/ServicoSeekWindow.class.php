<?php


//<fileHeader>
//<fileHeader>
//</fileHeader>
$__suivClientPath = __DIR__ . '/../../service/SuivClient.php';
if (!file_exists($__suivClientPath)) {
    throw new Exception('SuivClient.php não encontrado em: ' . $__suivClientPath);
}
require_once $__suivClientPath;

if (!class_exists(\app\service\SuivClient::class)) {
    throw new Exception('Classe \app\service\SuivClient não foi carregada');
}

use app\service\SuivClient; // importa o nome curto
//</fileHeader>
/**
 * ProdutoSeekWindow Listing
 */
class ServicoSeekWindow extends TWindow
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private static $database = 'minierp';
    private static $formName = 'form_search_Produto';
    private static $produtosVeiculoMap = [];
    private static $familiasPedidoMap = [];

    private static function hasPrefixHighlight($nome): bool
    {
        return preg_match('/^\[[^\]]+\]/', trim((string) $nome)) === 1;
    }

    private static function normalizeSortName($nome): string
    {
        $nome = trim((string) $nome);
        return preg_replace('/^\[[^\]]+\]\s*/', '', $nome);
    }

    private static function getSyncSessionKey($veiculo_id, $familia_produto_id): string
    {
        return __CLASS__ . '_sync_done_' . (int) $veiculo_id . '_' . (int) $familia_produto_id;
    }

    private static function markFamilyAsSynced($veiculo_id, $familia_produto_id): void
    {
        TSession::setValue(self::getSyncSessionKey($veiculo_id, $familia_produto_id), true);
    }

    private static function isFamilyAlreadySynced($veiculo_id, $familia_produto_id): bool
    {
        return (bool) TSession::getValue(self::getSyncSessionKey($veiculo_id, $familia_produto_id));
    }

    private static function hasLocalProdutosByVehicleFamily($veiculo_id, $familia_produto_id): bool
    {
        if (empty($veiculo_id) || empty($familia_produto_id)) {
            return false;
        }

        $conn = TTransaction::get();
        $veiculo_id = (int) $veiculo_id;
        $familia_produto_id = (int) $familia_produto_id;

        $sql = "SELECT 1
                  FROM produto_preco_veiculo ppv
                  INNER JOIN produto p ON p.id = ppv.produto_id
                 WHERE ppv.veiculos_id = {$veiculo_id}
                   AND p.familia_produto_id = {$familia_produto_id}
                 LIMIT 1";

        $result = $conn->query($sql);
        return (bool) $result->fetch(PDO::FETCH_ASSOC);
    }

    private static function hasLocalServicosByVehicleFamily($veiculo_id, $familia_produto_id): bool
    {
        if (empty($veiculo_id) || empty($familia_produto_id)) {
            return false;
        }

        $conn = TTransaction::get();
        $veiculo_id = (int) $veiculo_id;
        $familia_produto_id = (int) $familia_produto_id;

        $sql = "SELECT 1
                  FROM produto_preco_veiculo ppv
                  INNER JOIN produto p ON p.id = ppv.produto_id
                 WHERE ppv.veiculos_id = {$veiculo_id}
                   AND p.familia_produto_id = {$familia_produto_id}
                   AND p.tipo_produto_id = 2
                 LIMIT 1";

        $result = $conn->query($sql);
        return (bool) $result->fetch(PDO::FETCH_ASSOC);
    }

    private static function getServiceFamilyIdsByVehicle($veiculo_id): array
    {
        if (empty($veiculo_id)) {
            return [];
        }

        $conn = TTransaction::get();
        $veiculo_id = (int) $veiculo_id;

        $sql = "SELECT DISTINCT p.familia_produto_id
                  FROM produto_preco_veiculo ppv
                  INNER JOIN produto p ON p.id = ppv.produto_id
                 WHERE ppv.veiculos_id = {$veiculo_id}
                   AND p.tipo_produto_id = 2
                   AND p.familia_produto_id IS NOT NULL";

        $result = $conn->query($sql);

        if (!$result) {
            return [];
        }

        $familia_ids = $result->fetchAll(PDO::FETCH_COLUMN);
        $familia_ids = array_map('intval', $familia_ids ?: []);

        return array_values(array_unique(array_filter($familia_ids)));
    }

    private static function getProductFamilyIdsByPedido($pedido_id): array
    {
        if (empty($pedido_id)) {
            return [];
        }

        $familia_ids = [];
        $itens_pedido_frotas = ItensPedidoFrotas::where('pedido_frotas_id', '=', $pedido_id)->load();

        if ($itens_pedido_frotas) {
            foreach ($itens_pedido_frotas as $item) {
                if (empty($item->produto_id)) {
                    continue;
                }

                $produto_item = new Produto($item->produto_id);

                if ((int) ($produto_item->tipo_produto_id ?? 0) !== 1) {
                    continue;
                }

                if (!empty($produto_item->familia_produto_id)) {
                    $familia_ids[] = (int) $produto_item->familia_produto_id;
                }
            }
        }

        return array_values(array_unique(array_filter($familia_ids)));
    }

    private static function ensureFamilyProductsSynced($familia_produto_id, $veiculo_id, $pedido_id = null, $forceSync = false): void
    {
        if (TSession::getValue('utiliza_temparia') != 1 || empty($familia_produto_id) || empty($veiculo_id)) {
            return;
        }

        if (!$forceSync && self::isFamilyAlreadySynced($veiculo_id, $familia_produto_id)) {
            return;
        }

        if (!$forceSync && self::hasLocalServicosByVehicleFamily($veiculo_id, $familia_produto_id)) {
            self::markFamilyAsSynced($veiculo_id, $familia_produto_id);
            return;
        }

        if (!$forceSync && !empty($pedido_id)) {
            $familias_pedido = self::getProductFamilyIdsByPedido($pedido_id);
            $forceSync = in_array((int) $familia_produto_id, array_map('intval', $familias_pedido), true);
        }

        $familia_produto = FamiliaProduto::where('id', '=', $familia_produto_id)->first();
        if (!$familia_produto) {
            return;
        }

        self::syncProdutosDaFamiliaPorVeiculo($familia_produto, $veiculo_id);
        self::syncServicosDaFamiliaPorVeiculo($familia_produto, $veiculo_id);
        self::markFamilyAsSynced($veiculo_id, $familia_produto_id);
    }

    private static function getSelectedVehiclePlate(): string
    {
        $session_pedido = TSession::getValue('pedido_frotas_form_data');
        $veiculo_id = $session_pedido->veiculos_id ?? null;

        if (empty($veiculo_id)) {
            return 'VEICULO';
        }

        try {
            $veiculo = new Veiculos($veiculo_id);
            $placa = trim((string) ($veiculo->placa ?? ''));
            return $placa !== '' ? $placa : 'VEICULO';
        } catch (Exception $e) {
            return 'VEICULO';
        }
    }

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        parent::setTitle(AdiantiCoreTranslator::translate('Search record'));
        parent::setSize(0.7, null);

        TSession::setValue(__CLASS__ . '_filter_tipo_produto_id', new TFilter('tipo_produto_id', '=', 2));

        $this->form = new BootstrapFormBuilder(self::$formName);

        $criteria_familia_produto_id = new TCriteria();
        $familia_produto_id = new TDBCombo('familia_produto_id', 'minierp', 'FamiliaProduto', 'id', '{nome_familia_servico_suiv_veiculo}', 'nome asc', $criteria_familia_produto_id);
        $familia_produto_id->enableSearch();
        $familia_produto_id->setSize('100%');
        $familia_produto_id->setChangeAction(new TAction([$this, 'onChangefamilia_produto_id']));

        $produto_nome = new TEntry('produto_nome');
        $produto_nome->setSize('100%');
        $produto_nome->placeholder = 'Digite o nome do servico dentro do grupo selecionado';
        $somente_destaques = new TCheckButton('somente_destaques');
        $somente_destaques->setUseSwitch(true, 'green');
        $somente_destaques->setIndexValue('1');
        $somente_destaques->setInactiveIndexValue('0');

        $this->form->addFields([new TLabel('Grupo')], [$familia_produto_id], [new TLabel('Servico')], [$produto_nome]);
        $this->form->addFields([new TLabel('Somente destaques')], [$somente_destaques]);
        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));
        $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_nome = new TDataGridColumn('nome', 'Produto', 'left');
        $column_familia = new TDataGridColumn('familia_produto->nome', 'Grupo', 'left');

        $column_nome->setTransformer(function ($value, $object, $row) {
            if (!empty($object->destacado_veiculo)) {
                $row->style = 'background-color: #e8fff1; font-weight: 600;';
                return "<span style='color:#1e7e34;'>[" . self::getSelectedVehiclePlate() . "] {$value}</span>";
            }

            return $value;
        });

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_familia);

        $action_select = new TDataGridAction([$this, 'onSelect']);
        $action_select->setUseButton(true);
        $action_select->setButtonClass('nopadding');
        $action_select->setLabel('');
        $action_select->setImage('fa:hand-pointer green');
        $action_select->setField('id');
        $this->datagrid->addAction($action_select);

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $container = new TVBox;
        $container->style = 'width: 100%;margin-bottom:0;border-radius:0';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));

        parent::add($container);
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $familia_produto_id = $data->familia_produto_id ?? null;
        $produto_nome = trim((string) ($data->produto_nome ?? ''));
        $somente_destaques = !empty($data->somente_destaques) ? '1' : '0';
        $session_pedido = TSession::getValue('pedido_frotas_form_data');
        $veiculo_id = $session_pedido->veiculos_id ?? null;

        if (empty($familia_produto_id) && !empty($param['familia_produto_id'])) {
            $familia_produto_id = $param['familia_produto_id'];
        }

        if (empty($familia_produto_id)) {
            $familia_produto_id = TSession::getValue('familia_produto_id');
        }

        $data->familia_produto_id = $familia_produto_id;
        $data->produto_nome = $produto_nome;
        $data->somente_destaques = $somente_destaques;

        TSession::setValue(__CLASS__ . '_filter_familia_produto_id', null);
        TSession::setValue(__CLASS__ . '_filter_produto_nome', null);

        if (!empty($familia_produto_id)) {
            $filter = new TFilter('familia_produto_id', '=', $familia_produto_id);
            TSession::setValue(__CLASS__ . '_filter_familia_produto_id', $filter);
        }

        if ($produto_nome !== '') {
            $filter_nome = new TFilter('nome', 'like', "%{$produto_nome}%");
            TSession::setValue(__CLASS__ . '_filter_produto_nome', $filter_nome);
        }

        $this->form->setData($data);
        TSession::setValue(__CLASS__ . '_filter_data', $data);

        if (!empty($familia_produto_id) && !empty($veiculo_id)) {
            try {
                TTransaction::open(self::$database);
                self::ensureFamilyProductsSynced($familia_produto_id, $veiculo_id, $session_pedido->id ?? null);
                TTransaction::close();
            } catch (Exception $e) {
                if (TTransaction::getDatabase()) {
                    TTransaction::rollback();
                }

                if (!SuivClient::shouldUseLocalFallback($e)) {
                    throw $e;
                }
            }
        }

        $param = [];
        $param['offset'] = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = null)
    {
        try {
            TTransaction::open(self::$database);

            $repository = new TRepository('Produto');
            $limit = 10;
            $criteria = new TCriteria;
            $session_pedido = TSession::getValue('pedido_frotas_form_data');
            $veiculo_id = $session_pedido->veiculos_id ?? null;
            $offset = isset($param['offset']) ? (int) $param['offset'] : 0;

            $criteria->resetProperties();

            if (TSession::getValue(__CLASS__ . '_filter_tipo_produto_id')) {
                $criteria->add(TSession::getValue(__CLASS__ . '_filter_tipo_produto_id'));
            }

            if (TSession::getValue(__CLASS__ . '_filter_familia_produto_id')) {
                $criteria->add(TSession::getValue(__CLASS__ . '_filter_familia_produto_id'));
            }

            if (TSession::getValue(__CLASS__ . '_filter_produto_nome')) {
                $criteria->add(TSession::getValue(__CLASS__ . '_filter_produto_nome'));
            }

            $criteria->add(
                new TFilter(
                    'system_unit_id',
                    'IN',
                    "(SELECT su.id FROM system_unit su
                    LEFT JOIN entidade e ON e.id = su.entidade_id
                    WHERE e.frotas = 1)"
                )
            );

            $filter_data = TSession::getValue(__CLASS__ . '_filter_data') ?: new StdClass;
            $somente_destaques = !empty($filter_data->somente_destaques);

            if ($somente_destaques) {
                if (!empty($veiculo_id)) {
                    $criteria->add(new TFilter('id', 'IN', "(SELECT produto_id FROM produto_preco_veiculo WHERE veiculos_id = " . (int) $veiculo_id . ")"));
                } else {
                    $criteria->add(new TFilter('id', '=', 0));
                }
            }

            $criteria->setProperty('order', 'nome asc');
            $count = $repository->count($criteria);
            $criteria->setProperty('limit', $limit);
            $criteria->setProperty('offset', $offset);
            $objects = $repository->load($criteria, false);
            self::$produtosVeiculoMap = $this->getProdutosDoVeiculoMap($objects);

            if ($objects) {
                foreach ($objects as $object) {
                    $object->destacado_veiculo = !empty(self::$produtosVeiculoMap[$object->id]);
                }
            }

            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

            TTransaction::close();
            $this->loaded = true;
        } catch (Exception $e) {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }

            if (!SuivClient::shouldUseLocalFallback($e)) {
                new TMessage('error', $e->getMessage());
            }
        }
    }

    private function getProdutosDoVeiculoMap($objects)
    {
        $session_pedido = TSession::getValue('pedido_frotas_form_data');
        $veiculo_id = $session_pedido->veiculos_id ?? null;

        if (empty($veiculo_id) || empty($objects)) {
            return [];
        }

        $produto_ids = [];
        foreach ($objects as $object) {
            if (!empty($object->id)) {
                $produto_ids[] = (int) $object->id;
            }
        }

        $produto_ids = array_values(array_unique(array_filter($produto_ids)));
        if (empty($produto_ids)) {
            return [];
        }

        $criteria = new TCriteria();
        $criteria->add(new TFilter('veiculos_id', '=', $veiculo_id));
        $criteria->add(new TFilter('produto_id', 'IN', $produto_ids));

        $repository = new TRepository('ProdutoPrecoVeiculo');
        $items = $repository->load($criteria, false);

        $map = [];
        if ($items) {
            foreach ($items as $item) {
                $map[$item->produto_id] = true;
            }
        }

        return $map;
    }

    private function getFamiliasDoPedidoMap()
    {
        $session_pedido = TSession::getValue('pedido_frotas_form_data');
        $pedido_id = $session_pedido->id ?? null;
        $familia_ids = self::getProductFamilyIdsByPedido($pedido_id);
        $map = [];

        foreach ($familia_ids as $familia_id) {
            $map[(int) $familia_id] = true;
        }

        return $map;
    }

    /**
     * Executed when the user chooses the record
     */
    public static function onSelect($param)
    {
        try {
            $key = $param['key'];
            TTransaction::open(self::$database);

            $object = new Produto($key);

            TTransaction::close();

            $send = new StdClass;
            $send->produto_id = $object->id;
            $send->produto_id1 = $object->id;
            $send->familia_produto_id = $object->familia_produto_id;
            $send->produto_nome = $object->nome;
            TForm::sendData('form_ItensPedidoFrotasServicoForm', $send);

            parent::closeWindow();
        } catch (Exception $e) {
            $send = new StdClass;
            $send->produto_id = '';
            $send->familia_produto_id = '';
            $send->produto_nome = '';
            TForm::sendData('form_Contrato', $send);

            TTransaction::rollback();
        }
    }

    private function loadFamiliasSuiv($param = null)
    {
        try {
            TTransaction::open(self::$database);

            $session_pedido = TSession::getValue('pedido_frotas_form_data');
            TSession::setValue('tipo', 2);
            $filter_data = TSession::getValue(__CLASS__ . '_filter_data') ?: new StdClass;
            $selected_familia_id = $filter_data->familia_produto_id ?? null;

            if (TSession::getValue('utiliza_temparia') == 1 && !empty($session_pedido->veiculos_id)) {
                $veiculo = new Veiculos($session_pedido->veiculos_id);

                $vehicletoken = Vehicletoken::where('veiculos_id', '=', $session_pedido->veiculos_id)->first();
                if ($vehicletoken) {
                    $token = $vehicletoken->token;
                } else {
                    $placa = !empty($veiculo->placa) ? trim($veiculo->placa) : '';
                    $token = $placa !== '' ? SuivClient::getVehicleTokenByPlate($placa) : '';

                    if ($token) {
                        $newVehicletoken = new Vehicletoken();
                        $newVehicletoken->veiculos_id = $session_pedido->veiculos_id;
                        $newVehicletoken->token = $token;
                        $newVehicletoken->store();
                    }
                }

                if (!empty($token)) {
                    $grupopecas = SuivClient::getSets($token);
                    TSession::setValue('grupo_pecas_suiv', null);
                    TSession::setValue('familiaIDS', []);

                    if (!empty($grupopecas) && is_iterable($grupopecas)) {
                        foreach ($grupopecas as $set) {
                            $id = is_array($set) ? ($set['id'] ?? null) : ($set->id ?? null);
                            $desc = is_array($set) ? ($set['description'] ?? null) : ($set->description ?? null);

                            TSession::setValue('grupo_pecas_suiv', $id);

                            if ($desc) {
                                $familia_produto = FamiliaProduto::where('nome', '=', $desc)->first();

                                if (!$familia_produto) {
                                    $familia_produto = new FamiliaProduto;
                                    $familia_produto->nome = $desc;
                                    $familia_produto->suiv_id = $id;
                                    $familia_produto->store();
                                }

                                $familia_ids = TSession::getValue('familiaIDS') ?: [];
                                $familia_ids[] = $familia_produto->id;
                                TSession::setValue('familiaIDS', array_values(array_unique($familia_ids)));
                            }
                        }
                    }

                    $criteria = new TCriteria();
                    $ordem_familias = 'nome asc';
                    $familia_ids_suiv = array_map('intval', TSession::getValue('familiaIDS') ?: []);
                    $familia_ids_pedido = self::getProductFamilyIdsByPedido($session_pedido->id ?? null);
                    $familia_ids_prioritarios = !empty($familia_ids_pedido)
                        ? array_values(array_intersect($familia_ids_suiv, $familia_ids_pedido))
                        : [];

                    if (empty($familia_ids_prioritarios)) {
                        $familia_ids_prioritarios = self::getServiceFamilyIdsByVehicle($session_pedido->veiculos_id);
                    }

                    if (empty($familia_ids_prioritarios)) {
                        $familia_ids_prioritarios = $familia_ids_suiv;
                    }

                    TSession::setValue('familiaIDS', $familia_ids_prioritarios);

                    if (!empty($familia_ids_prioritarios)) {
                        $ordem_familias = '(CASE WHEN id IN (' . implode(',', $familia_ids_prioritarios) . ') THEN 0 ELSE 1 END), nome asc';
                    }

                    TDBCombo::reloadFromModel(
                        self::$formName,
                        'familia_produto_id',
                        self::$database,
                        'FamiliaProduto',
                        'id',
                        '{nome_familia_servico_suiv_veiculo}',
                        $ordem_familias,
                        $criteria,
                        true
                    );

                    $send = new StdClass;
                    $send->familia_produto_id = $selected_familia_id;
                    $send->produto_nome = $filter_data->produto_nome ?? '';
                    TForm::sendData(self::$formName, $send, false, true);
                }
            }

            TTransaction::close();
        } catch (Exception $e) {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }

            if (!SuivClient::shouldUseLocalFallback($e)) {
                new TMessage('error', $e->getMessage());
            }
        }
    }

    public function onShow($param = null)
    {
        if ($param) {
            $this->loadFamiliasSuiv($param);
        } else {
            $this->loadFamiliasSuiv();
        }

        $this->loaded = true;
    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        if (!$this->loaded && (!isset($_GET['method']) || !in_array($_GET['method'], ['onReload', 'onSearch']))) {
            if (func_num_args() > 0) {
                $this->loadFamiliasSuiv(func_get_arg(0));
            } else {
                $this->loadFamiliasSuiv();
            }
        }
        parent::show();
    }

    private static function getVehicleToken($veiculo_id)
    {
        $vehicletoken = Vehicletoken::where('veiculos_id', '=', $veiculo_id)->first();
        if ($vehicletoken && !empty($vehicletoken->token)) {
            return $vehicletoken->token;
        }

        $veiculo = new Veiculos($veiculo_id);
        $placa = !empty($veiculo->placa) ? trim($veiculo->placa) : '';
        if ($placa === '') {
            return '';
        }

        $token = SuivClient::getVehicleTokenByPlate($placa);
        if ($token) {
            $newVehicletoken = new Vehicletoken();
            $newVehicletoken->veiculos_id = $veiculo_id;
            $newVehicletoken->token = $token;
            $newVehicletoken->store();
        }

        return $token ?: '';
    }

    private static function syncProdutoPrecoVeiculo($produto, $veiculo_id, $grupo_id, $nickname_id, $peca_id, $part_number, $preco_peca, $tempo_mao_obra = null, $tempo_servico = null)
    {
        $produto_preco_veiculo = ProdutoPrecoVeiculo::where('produto_id', '=', $produto->id)
                                                    ->where('veiculos_id', '=', $veiculo_id)
                                                    ->first();

        if (!$produto_preco_veiculo) {
            $produto_preco_veiculo = new ProdutoPrecoVeiculo();
            $produto_preco_veiculo->produto_id = $produto->id;
            $produto_preco_veiculo->veiculos_id = $veiculo_id;
        }

        $produto_preco_veiculo->preco_venda = (float) $preco_peca;
        $produto_preco_veiculo->suiv_grupo = $grupo_id;
        $produto_preco_veiculo->suiv_nickname_id = $nickname_id;
        $produto_preco_veiculo->suiv_peca_id = $peca_id;
        $produto_preco_veiculo->suiv_preco_peca = (float) $preco_peca;
        $produto_preco_veiculo->suiv_part_number = $part_number;
        $produto_preco_veiculo->suiv_tempo_mao_obra_id = $tempo_mao_obra;
        $produto_preco_veiculo->suiv_tempo_servico = $tempo_servico;
        $produto_preco_veiculo->store();
    }

    private static function syncProdutosDaFamiliaPorVeiculo($familia_produto, $veiculo_id)
    {
        if (empty($familia_produto) || empty($familia_produto->suiv_id) || empty($veiculo_id)) {
            return;
        }

        $token = self::getVehicleToken($veiculo_id);
        if (!$token) {
            return;
        }

        $nicks = SuivClient::getNicknames($token, $familia_produto->suiv_id);
        if (empty($nicks)) {
            return;
        }

        foreach ($nicks as $nicksitem) {
            $nickname_id = is_array($nicksitem) ? ($nicksitem['id'] ?? null) : ($nicksitem->id ?? null);
            if (!$nickname_id) {
                continue;
            }

            $parts = SuivClient::getParts($token, (int) $nickname_id);
            if (empty($parts) || !is_iterable($parts)) {
                continue;
            }

            foreach ($parts as $p) {
                $peca_id = is_array($p) ? ($p['id'] ?? null) : ($p->id ?? null);
                $descricao = is_array($p) ? ($p['description'] ?? null) : ($p->description ?? null);
                $part_number = is_array($p) ? ($p['partNumber'] ?? null) : ($p->partNumber ?? null);
                $preco_peca = is_array($p) ? ($p['price'] ?? 0) : ($p->price ?? 0);

                if (!$peca_id) {
                    continue;
                }

                $produto = Produto::where('suiv_peca_id', '=', $peca_id)
                                  ->where('tipo_produto_id', '=', 1)
                                  ->first();

                if (!$produto) {
                    $produto = new Produto();
                    $produto->tipo_produto_id = 1;
                    $produto->ativo = 'T';
                    $produto->system_unit_id = TSession::getValue('idunit');
                }

                $produto->nome = $descricao ?: 'Peça S/Descrição';
                $produto->familia_produto_id = $familia_produto->id;
                $produto->preco_venda = (float) $preco_peca;
                $produto->suiv_grupo_id = $familia_produto->suiv_id;
                $produto->suiv_nickname_id = (int) $nickname_id;
                $produto->suiv_peca_id = $peca_id;
                $produto->suiv_preco_peca = (float) $preco_peca;
                $produto->suiv_partnumber = $part_number;
                $produto->store();

                self::syncProdutoPrecoVeiculo(
                    $produto,
                    $veiculo_id,
                    $familia_produto->suiv_id,
                    (int) $nickname_id,
                    $peca_id,
                    $part_number,
                    $preco_peca
                );
            }
        }
    }

    private static function syncServicosDaFamiliaPorVeiculo($familia_produto, $veiculo_id)
    {
        if (empty($familia_produto) || empty($familia_produto->suiv_id) || empty($veiculo_id)) {
            return;
        }

        $token = self::getVehicleToken($veiculo_id);
        if (!$token) {
            return;
        }

        $nicksApi = SuivClient::getNicknames($token, $familia_produto->suiv_id);
        if (empty($nicksApi) || !is_iterable($nicksApi)) {
            return;
        }

        $nicksMap = [];
        foreach ($nicksApi as $nicksitem) {
            $nickname_id = is_array($nicksitem) ? ($nicksitem['id'] ?? null) : ($nicksitem->id ?? null);
            if (!$nickname_id) {
                continue;
            }

            $nicksMap[(int) $nickname_id] = [
                'id' => (int) $nickname_id,
                'description' => is_array($nicksitem) ? ($nicksitem['description'] ?? null) : ($nicksitem->description ?? null),
            ];
        }

        $session_pedido = TSession::getValue('pedido_frotas_form_data');
        $pedido_id = $session_pedido->id ?? null;
        $nicks = [];

        if (!empty($pedido_id)) {
            $itens_pedido_frotas = ItensPedidoFrotas::where('pedido_frotas_id', '=', $pedido_id)->load();

            if ($itens_pedido_frotas) {
                foreach ($itens_pedido_frotas as $item) {
                    if (empty($item->produto_id)) {
                        continue;
                    }

                    $produto_item = new Produto($item->produto_id);

                    if ((int) ($produto_item->tipo_produto_id ?? 0) !== 1) {
                        continue;
                    }

                    if ((int) ($produto_item->familia_produto_id ?? 0) !== (int) $familia_produto->id) {
                        continue;
                    }

                    $produto_preco_veiculo = ProdutoPrecoVeiculo::where('produto_id', '=', $produto_item->id)
                                                                ->where('veiculos_id', '=', $veiculo_id)
                                                                ->first();

                    $nickname_id = !empty($produto_preco_veiculo->suiv_nickname_id)
                        ? (int) $produto_preco_veiculo->suiv_nickname_id
                        : (int) ($produto_item->suiv_nickname_id ?? 0);

                    if (empty($nickname_id) || empty($nicksMap[$nickname_id])) {
                        continue;
                    }

                    $nicks[$nickname_id] = $nicksMap[$nickname_id];
                }
            }
        }

        if (empty($nicks)) {
            $nicks = array_values($nicksMap);
        }

        $tiposServico = [
            'bodyshop'   => 'Funilaria',
            'painting'   => 'Pintura',
            'tapestry'   => 'Tapeçaria',
            'electrical' => 'Elétrica',
            'mechanic'   => 'Mecânica',
            'glazing'    => 'Vidraçaria',
        ];

        foreach ($nicks as $nicksitem) {
            $nickname_id = is_array($nicksitem) ? ($nicksitem['id'] ?? null) : ($nicksitem->id ?? null);
            $descricao_nick = is_array($nicksitem) ? ($nicksitem['description'] ?? null) : ($nicksitem->description ?? null);

            if (!$nickname_id) {
                continue;
            }

            $temposMaoDeObra = SuivClient::getLaborTimesByNicknames($token, (int) $nickname_id);
            if (empty($temposMaoDeObra) || !is_iterable($temposMaoDeObra)) {
                continue;
            }

            foreach ($temposMaoDeObra as $tempo) {
                $nicknameIdApi = isset($tempo['nicknameId']) ? (int) $tempo['nicknameId'] : null;

                if (!$nicknameIdApi) {
                    continue;
                }

                foreach ($tiposServico as $campoApi => $descricaoPt) {
                    $tempoStr = isset($tempo[$campoApi]) ? $tempo[$campoApi] : '00:00:00';

                    if ($tempoStr === '00:00:00' || !$tempoStr) {
                        continue;
                    }

                    $nomeBaseServico = $descricao_nick ?: 'Servico S/Descricao';
                    $nomeProduto = $nomeBaseServico . ' - ' . $descricaoPt . ' - ' . $tempoStr;
                    $qtdeHoras = SuivClient::convertTimeStringToDecimalHours($tempoStr);

                    $produto = Produto::where('familia_produto_id', '=', $familia_produto->id)
                                      ->where('tipo_produto_id', '=', 2)
                                      ->where('suiv_nickname_id', '=', (int) $nickname_id)
                                      ->where('nome', '=', $nomeProduto)
                                      ->first();

                    if (!$produto) {
                        $produto = new Produto();
                        $produto->tipo_produto_id = 2;
                        $produto->ativo = 'T';
                        $produto->system_unit_id = TSession::getValue('idunit');
                    }

                    $produto->nome = $nomeProduto;
                    $produto->familia_produto_id = $familia_produto->id;
                    $produto->preco_venda = 0.0;
                    $produto->suiv_grupo_id = $familia_produto->suiv_id;
                    $produto->suiv_nickname_id = (int) $nickname_id;
                    $produto->suiv_tempo_mao_obra_id = $qtdeHoras;
                    $produto->suiv_tempo_servico = $tempoStr;
                    $produto->store();

                    self::syncProdutoPrecoVeiculo(
                        $produto,
                        $veiculo_id,
                        $familia_produto->suiv_id,
                        (int) $nickname_id,
                        null,
                        null,
                        0,
                        $qtdeHoras,
                        $tempoStr
                    );
                }
            }
        }
    }

    public static function onChangefamilia_produto_id($param)
    {
        try {
            TTransaction::open(self::$database);

            TSession::setValue('tipo', 2);

            $familia_produto_id = $param['familia_produto_id'] ?? null;
            TSession::setValue('familia_produto_id', $familia_produto_id);
            TSession::setValue(__CLASS__ . '_filter_familia_produto_id', null);
            TSession::setValue(__CLASS__ . '_filter_produto_nome', null);

            $filter_data = TSession::getValue(__CLASS__ . '_filter_data') ?: new StdClass;
            $filter_data->familia_produto_id = $familia_produto_id;

            if (empty($familia_produto_id)) {
                $filter_data->produto_nome = '';
            }

            TSession::setValue(__CLASS__ . '_filter_data', $filter_data);

            if (!empty($familia_produto_id)) {
                TSession::setValue(__CLASS__ . '_filter_familia_produto_id', new TFilter('familia_produto_id', '=', $familia_produto_id));

                $session_pedido = TSession::getValue('pedido_frotas_form_data');
                $veiculo_id = $session_pedido->veiculos_id ?? null;

                if (!empty($veiculo_id)) {
                    self::ensureFamilyProductsSynced($familia_produto_id, $veiculo_id, $session_pedido->id ?? null);
                }
            }

            TTransaction::close();

        } catch (Exception $e) {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }

            if (!SuivClient::shouldUseLocalFallback($e)) {
                new TMessage('error', $e->getMessage());
            }
        }
    }
}
