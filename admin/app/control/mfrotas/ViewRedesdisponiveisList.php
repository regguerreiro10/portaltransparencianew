<?php

//<fileHeader>

//</fileHeader>

use Adianti\Widget\Wrapper\TDBMultiSearch;

class ViewRedesdisponiveisList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private $cidadeEstadoMap = [];
    private $reputacaoMap = [];
    private $seguimentoMap = [];
    private static $database = 'minierp';
    private static $activeRecord = 'ViewRedesdisponiveis';
    private static $primaryKey = 'id';
    private static $formName = 'form_ViewRedesdisponiveisList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 10;
    private $embeddedMode = false;

    //<classProperties>

    //</classProperties>

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Redes Disponíveis");
        $this->embeddedMode = !empty(TSession::getValue('pedido_frotas_id'));
        $this->limit = $this->embeddedMode ? 200 : 10;

        $criteria_seguimento_id = new TCriteria();
        $criteria_cidade_id = new TCriteria();

        //<onBeginPageCreation>

        //</onBeginPageCreation>

        $nome = new TEntry('nome');
        $seguimento_id = new TDBMultiSearch('seguimento_id', 'minierp', 'Seguimento', 'id', 'descricao','descricao asc' , $criteria_seguimento_id );
        $seguimento_descricao = new TEntry('seguimento_descricao');
        $email = new TEntry('email');
        $cidade_id = new TDBMultiSearch('cidade_id', 'minierp', 'Cidade', 'id', 'nome','nome asc'  , $criteria_cidade_id );
        $selo = new TEntry('selo');


        $seguimento_id->setMinLength(2);
        $seguimento_id->setFilterColumns(['descricao']);
        $cidade_id->setMinLength(2);
        $cidade_id->setMask('{nome} - {estado->sigla}');

        $nome->setSize('100%');
        $email->setSize('100%');
        $cidade_id->setSize('100%');
        $seguimento_id->setSize('100%');
        $seguimento_descricao->setSize('100%');

        //<onBeforeAddFieldsToForm>

        //</onBeforeAddFieldsToForm>
        $row1 = $this->form->addFields([new TLabel("Segmentos:", null, '14px', null, '100%'),$seguimento_id],[new TLabel("Palavra-chave do segmento:", null, '14px', null, '100%'),$seguimento_descricao],[new TLabel("Cidade:", null, '14px', null, '100%'),$cidade_id]);
        $row1->layout = ['col-sm-4','col-sm-4','col-sm-4'];

        if (TSession::getValue('selecao_redes_aleatoria')==2) {
            $row2 = $this->form->addFields([new TLabel("Nome:", null, '14px', null, '100%'),$nome],[new TLabel("Email:", null, '14px', null, '100%'),$email]);
            $row2->layout = ['col-sm-6','col-sm-6'];
        }


        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight($this->embeddedMode ? 450 : 250);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_nome = new TDataGridColumn('nome', "Nome", 'left');
        $column_documento = new TDataGridColumn('documento', "Cnpj", 'left');
        $column_fone = new TDataGridColumn('fone', "Fone", 'left');
        $column_email = new TDataGridColumn('email', "Email", 'left');
        $column_cidade_id_transformed = new TDataGridColumn('cidade_id', "Cidade", 'left');
        $column_pessoa_seguimento = new TDataGridColumn('column_pessoa_seguimento', "Segmento", 'left');
        $column_selo = new TDataGridColumn('selo', "Selo Ambiental", 'center');
                $column_reputacao_transformed = new TDataGridColumn('reputacao', "Reputação", 'center');

                $column_reputacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
                        {
                            // cache pra não consultar o banco várias vezes pro mesmo pessoa_id
                            if (TSession::getValue('pedido_frotas_id'))
                            {
                                return '';
                            }

                            $pessoa_id = (int) ($object->id ?? 0);
                            if ($pessoa_id && isset($this->reputacaoMap[$pessoa_id]))
                            {
                                $data = $this->reputacaoMap[$pessoa_id];
                                $nota  = (int) ($data['nota'] ?? 0);
                                $total = (int) ($data['total'] ?? 0);
                                $cor   = (string) ($data['cor'] ?? '#FFD700');
                                $filled = $nota > 0 ? str_repeat('&#9733;', $nota) : '';
                                $emptyCount = max(0, $total - $nota);
                                $empty  = $emptyCount > 0 ? str_repeat('&#9734;', $emptyCount) : '';
                                return "<span style='color: {$cor}; font-size: 1.1em; white-space:nowrap;'>{$filled}{$empty}</span>";
                            }

                            static $cache = [];

                            $idunit    = (int) (TSession::getValue('idunit') ?? 0);
                            $pessoa_id = (int) ($object->id ?? 0);

                            if (!$pessoa_id) {
                                return '';
                            }

                            $cacheKey = $idunit . ':' . $pessoa_id;
                            $cont = 0;
                            if (!isset($cache[$cacheKey])) {
                                
                                $ouvidoria = Ouvidoria::where('pessoa_id', '=', $pessoa_id)
                                    ->where('system_unit_id', '=', $idunit)
                                    ->load();

                                $cont = is_array($ouvidoria) ? count($ouvidoria) : 0;

                                $ouvidoria = Ouvidoria::where('pessoa_id', '=', $pessoa_id)
                                    ->where('system_unit_id', '=', $idunit)
                                    ->first();

                                if ($ouvidoria && !empty($ouvidoria->tipo_ouvidoria_id)) {
                                    $tipo = new TipoOuvidoria($ouvidoria->tipo_ouvidoria_id);

                                    // AJUSTE AQUI: use o campo numérico correto (ex: reputacao, nota, estrelas...)
                                    $nota = $cont;
                                    $cor = $tipo->cor ?? '#FFD700';

                                    $cache[$cacheKey] = [
                                        'nota' => $nota,
                                        'cor'  => $cor
                                    ];
                                } else {
                                    $cache[$cacheKey] = [
                                        'nota' => 0,
                                        'cor'  => '#FFD700'
                                    ];
                                }
                            }

                            $nota = $cache[$cacheKey]['nota'];
                            $cor  = $cache[$cacheKey]['cor'];

                            if ($nota==0) {
                                $filled = '';
                            } else {
                            $filled = str_repeat('&#9733;', $nota);         // ★

                            }
                            $estrelasVazias = max(0, (int) $cont - (int) $nota);
                            if($estrelasVazias == 0){
                                $empty = '';
                            } else {
                            $empty  = str_repeat('&#9734;', $estrelasVazias);     // ☆
                            }

                            return "<span style='color: {$cor}; font-size: 1.1em; white-space:nowrap;'>{$filled}{$empty}</span>";
                        });

        $column_cidade_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $cidadeId = (int) ($object->cidade_id ?? 0);
            if ($cidadeId && isset($this->cidadeEstadoMap[$cidadeId]))
            {
                return $this->cidadeEstadoMap[$cidadeId];
            }

            //code here
                    TTransaction::open('minierp');

                $cidade = new Cidade($object->cidade_id);
                if ($cidade) {
                    $estado = new Estado($cidade->estado_id);
                    return "{$cidade->nome} - {$estado->sigla}";

                } else {
                    return "Não informado!!!";

                }

                TTransaction::close();

        });        

        $column_selo->setTransformer(function($value)
        {
            if ($value === 1) {
                return '<i class="fab fa-pagelines" style="color:#28a745;"></i>';                  // ou retorne $icon direto
            }
            return ''; // nada quando não for 1
            
        });

        $column_pessoa_seguimento->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $pessoaId = (int) ($object->id ?? 0);
            if ($pessoaId && isset($this->seguimentoMap[$pessoaId]))
            {
                return $this->renderSeguimentoBadges($this->seguimentoMap[$pessoaId]);
            }
            return 'Não informado!';
        });

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        //<onBeforeColumnsCreation>

        //</onBeforeColumnsCreation>
       if (TSession::getValue('selecao_redes_aleatoria')==2) {
            $this->builder_datagrid_check_all = new TCheckButton('builder_datagrid_check_all');
            $this->builder_datagrid_check_all->setIndexValue('on');
            $this->builder_datagrid_check_all->onclick = "Builder.checkAll(this)";
            $this->builder_datagrid_check_all->style = 'cursor:pointer';
            $this->builder_datagrid_check_all->setProperty('class', 'filled-in');
            $this->builder_datagrid_check_all->id = 'builder_datagrid_check_all';

            $label = new TLabel('');
            $label->style = 'margin:0';
            $label->class = 'checklist-label';
            $this->builder_datagrid_check_all->after($label);
            $label->for = 'builder_datagrid_check_all';

            $this->builder_datagrid_check = $this->datagrid->addColumn( new TDataGridColumn('builder_datagrid_check', $this->builder_datagrid_check_all, 'center',  '1%') );
        }
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_documento);
        $this->datagrid->addColumn($column_fone);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_cidade_id_transformed);
        $this->datagrid->addColumn($column_pessoa_seguimento);
        $this->datagrid->addColumn($column_selo);
        $this->datagrid->addColumn($column_reputacao_transformed);


        //<onAfterColumnsCreation>

        //</onAfterColumnsCreation>

        //<onAfterActionsCreation>

        //</onAfterActionsCreation>

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';
        if ($this->embeddedMode)
        {
            $panel->getBody()->style = trim(($panel->getBody()->style ?? '') . '; max-height: 560px; overflow-y: auto; overflow-x: auto;');
        }
        else
        {
            $panel->addFooter($this->pageNavigation);
        }

        //<onAfterHeaderActionsCreation>

        //</onAfterHeaderActionsCreation>

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Manutenção Frotas","ViewRedesdisponiveisList"]));
        }
        $container->add($this->form);
        $container->add($panel);
        //<onAfterPageCreation>

        //</onAfterPageCreation>

        parent::add($container);

    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_show_all_after_search', 1);

        //<onBeforeDatagridSearch>

        //</onBeforeDatagridSearch> 

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);
        TSession::setValue('filtrocidade_id', NULL);
        TSession::setValue('filtroseguimento_id', NULL);
        TSession::setValue('filtroseguimento_descricao', NULL);

        if (isset($data->nome) AND ( (is_scalar($data->nome) AND $data->nome !== '') OR (is_array($data->nome) AND (!empty($data->nome)) )) )
        {

            $filters[] = new TFilter('nome', 'like', "%{$data->nome}%");// create the filter 
        }

        if (isset($data->email) AND ( (is_scalar($data->email) AND $data->email !== '') OR (is_array($data->email) AND (!empty($data->email)) )) )
        {

            $filters[] = new TFilter('email', 'like', "%{$data->email}%");// create the filter 
        }

        //<onDatagridSearch>

        if (isset($data->cidade_id) AND ( (is_scalar($data->cidade_id) AND $data->cidade_id !== '') OR (is_array($data->cidade_id) AND (!empty($data->cidade_id)) )) )
        {

            $filters[] = new TFilter('cidade_id', 'in', $data->cidade_id);// create the filter 
            TSession::setValue('filtrocidade_id', $data->cidade_id);
        }

        $seguimentoIds = [];
        if (isset($data->seguimento_id) AND ( (is_scalar($data->seguimento_id) AND $data->seguimento_id !== '') OR (is_array($data->seguimento_id) AND (!empty($data->seguimento_id)) )) )
        {
            $seguimentoIds = is_array($data->seguimento_id) ? $data->seguimento_id : preg_split('/\s*,\s*/', (string) $data->seguimento_id);
            $seguimentoIds = array_values(array_unique(array_filter(array_map('intval', $seguimentoIds))));
        }

        $seguimentoDescricao = trim((string) ($data->seguimento_descricao ?? ''));
        if (!empty($seguimentoIds) || $seguimentoDescricao !== '')
        {
            try {
                TTransaction::open('minierp');

                if ($seguimentoDescricao !== '')
                {
                    $criteriaSeguimento = new TCriteria;
                    $criteriaSeguimento->add(new TFilter('descricao', 'like', "%{$seguimentoDescricao}%"));

                    $seguimentos = (new TRepository('Seguimento'))->load($criteriaSeguimento, FALSE) ?: [];
                    foreach ($seguimentos as $seguimento)
                    {
                        $seguimentoIds[] = (int) $seguimento->id;
                    }
                }

                $seguimentoIds = $this->expandSeguimentoIdsPorDescricao($seguimentoIds);
                $seguimentoIds = array_values(array_unique(array_filter(array_map('intval', $seguimentoIds))));

                if (!empty($seguimentoIds))
                {
                    $criteria = new TCriteria;
                    $criteria->add(new TFilter('seguimento_id', 'in', $seguimentoIds));

                    $registros = (new TRepository('SeguimentoPessoa'))->load($criteria, FALSE) ?: [];

                    $ids = [];
                    foreach ($registros as $item)
                    {
                        $ids[] = (int) $item->pessoa_id;
                    }

                    $ids = array_values(array_unique(array_filter($ids)));
                }
                else
                {
                    $ids = [];
                }

                TTransaction::close();

                TSession::setValue('filtroseguimento_id', $seguimentoIds);
                TSession::setValue('filtroseguimento_descricao', $seguimentoDescricao !== '' ? $seguimentoDescricao : NULL);

                if (!empty($ids))
                {
                    $filters[] = new TFilter('id', 'IN', $ids);
                }
                else
                {
                    $filters[] = new TFilter('id', '=', 0);
                }

            } catch (Exception $e) {
                TTransaction::rollback();
                new TMessage('error', 'Erro ao buscar segmentos: ' . $e->getMessage());
            }
        }

        //</onDatagridSearch>

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    private function expandSeguimentoIdsPorDescricao(array $seguimentoIds): array
    {
        $seguimentoIds = array_values(array_unique(array_filter(array_map('intval', $seguimentoIds))));
        if (empty($seguimentoIds))
        {
            return [];
        }

        $criteriaSelecionados = new TCriteria;
        $criteriaSelecionados->add(new TFilter('id', 'in', $seguimentoIds));
        $selecionados = (new TRepository('Seguimento'))->load($criteriaSelecionados, FALSE) ?: [];

        $gruposTermos = [];
        foreach ($selecionados as $seguimento)
        {
            $termos = $this->extrairTermosSeguimento((string) ($seguimento->descricao ?? ''));
            if (!empty($termos))
            {
                $gruposTermos[] = $termos;
            }
        }

        if (empty($gruposTermos))
        {
            return $seguimentoIds;
        }

        $todosSeguimentos = (new TRepository('Seguimento'))->load(new TCriteria, FALSE) ?: [];
        foreach ($todosSeguimentos as $seguimento)
        {
            $descricaoNormalizada = $this->normalizarTextoSeguimento((string) ($seguimento->descricao ?? ''));

            foreach ($gruposTermos as $termos)
            {
                $encontrouTodos = true;
                foreach ($termos as $termo)
                {
                    if (strpos($descricaoNormalizada, $termo) === false)
                    {
                        $encontrouTodos = false;
                        break;
                    }
                }

                if ($encontrouTodos)
                {
                    $seguimentoIds[] = (int) $seguimento->id;
                    break;
                }
            }
        }

        return array_values(array_unique(array_filter($seguimentoIds)));
    }

    private function extrairTermosSeguimento(string $descricao): array
    {
        $texto = $this->normalizarTextoSeguimento($descricao);
        $partes = preg_split('/\s+/', $texto) ?: [];
        $ignorar = ['a', 'as', 'o', 'os', 'de', 'da', 'das', 'do', 'dos', 'e', 'em', 'para', 'por', 'com', 'servico', 'servicos'];
        $termos = [];

        foreach ($partes as $parte)
        {
            $parte = trim($parte);
            if (strlen($parte) < 4 || in_array($parte, $ignorar, true))
            {
                continue;
            }

            if (substr($parte, -2) === 'es' && strlen($parte) > 5)
            {
                $parte = substr($parte, 0, -2);
            }
            elseif (substr($parte, -1) === 's' && strlen($parte) > 4)
            {
                $parte = substr($parte, 0, -1);
            }

            $termos[] = $parte;
        }

        return array_values(array_unique($termos));
    }

    private function normalizarTextoSeguimento(string $texto): string
    {
        $texto = function_exists('mb_strtolower') ? mb_strtolower($texto, 'UTF-8') : strtolower($texto);
        $semAcento = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
        if ($semAcento !== false)
        {
            $texto = $semAcento;
        }

        $texto = preg_replace('/[^a-z0-9]+/', ' ', $texto);
        return trim((string) $texto);
    }

    private function renderSeguimentoBadges(string $seguimentos): string
    {
        $descricoes = array_values(array_unique(array_filter(array_map('trim', explode(',', $seguimentos)))));
        if (empty($descricoes))
        {
            return 'Nao informado!';
        }

        $gruposTermosFiltro = $this->getGruposTermosFiltroSeguimento();
        $descricoesOrdenadas = [];
        $descricoesRestantes = [];

        foreach ($descricoes as $descricao)
        {
            if ($this->descricaoCombinaComFiltroSeguimento($descricao, $gruposTermosFiltro))
            {
                $descricoesOrdenadas[] = $descricao;
            }
            else
            {
                $descricoesRestantes[] = $descricao;
            }
        }

        if (empty($descricoesOrdenadas))
        {
            $descricoesOrdenadas = $descricoesRestantes;
            $descricoesRestantes = [];
        }

        $limite = 4;
        $descricoesParaRenderizar = array_merge($descricoesOrdenadas, $descricoesRestantes);
        $descricoesExibidas = array_slice($descricoesParaRenderizar, 0, $limite);
        $descricoesOcultas = array_slice($descricoesParaRenderizar, $limite);
        $ocultos = count($descricoesOcultas);

        $badges = [];
        foreach ($descricoesExibidas as $descricao)
        {
            $descricao = htmlspecialchars($descricao, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $badges[] = "<span style='display:inline-block; margin:2px 4px 2px 0; padding:3px 7px; border:1px solid #9eb6d8; border-radius:3px; background:#eef5ff; color:#24456f; font-size:11px; line-height:1.3; white-space:normal;'>{$descricao}</span>";
        }

        if ($ocultos > 0)
        {
            $conteudo = $this->renderSeguimentoTooltipContent($descricoesOcultas);
            $badges[] = "<span class='js-seguimento-tooltip' data-seguimento-html='{$conteudo}' style='display:inline-block; margin:2px 4px 2px 0; padding:3px 7px; border:1px solid #b8c0cc; border-radius:3px; background:#f4f6f8; color:#596579; font-size:11px; line-height:1.3; white-space:nowrap; cursor:help;'>+{$ocultos}</span>";
        }

        return implode(' ', $badges);
    }

    private function renderSeguimentoTooltipContent(array $descricoes): string
    {
        $items = [];
        foreach ($descricoes as $descricao)
        {
            $descricao = htmlspecialchars($descricao, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $items[] = "<span style='display:inline-block; margin:2px 3px 2px 0; padding:3px 6px; border:1px solid #9eb6d8; border-radius:3px; background:#eef5ff; color:#24456f; font-size:11px; line-height:1.3;'>{$descricao}</span>";
        }

        $html = "<div style='max-width:520px; max-height:220px; overflow:auto; text-align:left;'><div style='font-weight:600; margin-bottom:6px; color:#34495e;'>Outros segmentos</div>" . implode(' ', $items) . "</div>";
        return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function getGruposTermosFiltroSeguimento(): array
    {
        static $gruposTermos = null;
        if ($gruposTermos !== null)
        {
            return $gruposTermos;
        }

        $gruposTermos = [];
        $filterData = TSession::getValue(__CLASS__.'_filter_data');
        $seguimentoDescricao = trim((string) ($filterData->seguimento_descricao ?? ''));
        if ($seguimentoDescricao !== '')
        {
            $termos = $this->extrairTermosSeguimento($seguimentoDescricao);
            if (!empty($termos))
            {
                $gruposTermos[] = $termos;
            }
        }

        $seguimentoIds = $filterData->seguimento_id ?? [];
        if (!empty($seguimentoIds))
        {
            $seguimentoIds = is_array($seguimentoIds) ? $seguimentoIds : preg_split('/\s*,\s*/', (string) $seguimentoIds);
            $seguimentoIds = array_values(array_unique(array_filter(array_map('intval', $seguimentoIds))));

            if (!empty($seguimentoIds))
            {
                $criteria = new TCriteria;
                $criteria->add(new TFilter('id', 'in', $seguimentoIds));
                $seguimentos = (new TRepository('Seguimento'))->load($criteria, FALSE) ?: [];

                foreach ($seguimentos as $seguimento)
                {
                    $termos = $this->extrairTermosSeguimento((string) ($seguimento->descricao ?? ''));
                    if (!empty($termos))
                    {
                        $gruposTermos[] = $termos;
                    }
                }
            }
        }

        return $gruposTermos;
    }

    private function descricaoCombinaComFiltroSeguimento(string $descricao, array $gruposTermosFiltro): bool
    {
        if (empty($gruposTermosFiltro))
        {
            return false;
        }

        $descricaoNormalizada = $this->normalizarTextoSeguimento($descricao);
        foreach ($gruposTermosFiltro as $termos)
        {
            $encontrouTodos = true;
            foreach ($termos as $termo)
            {
                if (strpos($descricaoNormalizada, $termo) === false)
                {
                    $encontrouTodos = false;
                    break;
                }
            }

            if ($encontrouTodos)
            {
                return true;
            }
        }

        return false;
    }

    private function injectSeguimentoTooltipScript(): void
    {
        TScript::create("
            (function () {
                function ensureTooltip() {
                    var tooltip = document.getElementById('seguimento-list-tooltip');
                    if (!tooltip) {
                        tooltip = document.createElement('div');
                        tooltip.id = 'seguimento-list-tooltip';
                        tooltip.style.position = 'fixed';
                        tooltip.style.zIndex = '99999';
                        tooltip.style.display = 'none';
                        tooltip.style.background = '#fff';
                        tooltip.style.color = '#333';
                        tooltip.style.border = '1px solid rgba(0,0,0,0.18)';
                        tooltip.style.borderRadius = '6px';
                        tooltip.style.boxShadow = '0 8px 24px rgba(0,0,0,0.18)';
                        tooltip.style.padding = '10px 12px';
                        tooltip.style.fontSize = '12px';
                        tooltip.style.pointerEvents = 'auto';
                        document.body.appendChild(tooltip);
                    }
                    return tooltip;
                }

                var hideTimer = null;
                function scheduleHide() {
                    clearTimeout(hideTimer);
                    hideTimer = setTimeout(function () {
                        var tooltip = ensureTooltip();
                        tooltip.style.display = 'none';
                    }, 180);
                }

                function cancelHide() {
                    clearTimeout(hideTimer);
                }

                function positionTooltip(event, tooltip) {
                    tooltip.style.left = '0px';
                    tooltip.style.top = '0px';
                    var width = tooltip.offsetWidth || 520;
                    var height = tooltip.offsetHeight || 220;
                    var left = Math.min(event.clientX + 12, window.innerWidth - width - 12);
                    var top = Math.min(event.clientY + 12, window.innerHeight - height - 12);
                    tooltip.style.left = Math.max(12, left) + 'px';
                    tooltip.style.top = Math.max(12, top) + 'px';
                }

                document.querySelectorAll('.js-seguimento-tooltip').forEach(function (el) {
                    if (el.dataset.tooltipBound === '1') {
                        return;
                    }

                    el.dataset.tooltipBound = '1';
                    el.addEventListener('mouseenter', function (event) {
                        cancelHide();
                        var tooltip = ensureTooltip();
                        tooltip.innerHTML = this.getAttribute('data-seguimento-html') || '';
                        tooltip.style.display = 'block';
                        positionTooltip(event, tooltip);
                    });

                    el.addEventListener('mousemove', function (event) {
                        var tooltip = ensureTooltip();
                        if (tooltip.style.display === 'block') {
                            positionTooltip(event, tooltip);
                        }
                    });

                    el.addEventListener('mouseleave', function () {
                        scheduleHide();
                    });
                });

                var tooltip = ensureTooltip();
                if (tooltip.dataset.tooltipBound !== '1') {
                    tooltip.dataset.tooltipBound = '1';
                    tooltip.addEventListener('mouseenter', cancelHide);
                    tooltip.addEventListener('mouseleave', scheduleHide);
                }
            })();
        ");
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // 🧹 limpa a sessão dos checks ao entrar na tela
            //TSession::setValue(__CLASS__.'builder_datagrid_check', []);

            $session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');
            $session_checks = is_array($session_checks) ? $session_checks : [];
            $checked_ids = $session_checks;

            // open a transaction with database 'minierp'
            TTransaction::open(self::$database);

            // creates a repository for ViewRedesdisponiveis
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            $criteria->add(new TFilter('id', 'IN', "(SELECT id FROM pessoa WHERE ativo IN ('Sim','S','T','1',1))"));

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $sessionFilters = TSession::getValue(__CLASS__.'_filters');
            $hasActiveFilters = !empty($sessionFilters);
            if($filters = $sessionFilters)
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            //<onBeforeDatagridLoad>

            //</onBeforeDatagridLoad>

            // Verifica se o usuário é um condutor e não deve aparecer na lista
            $criteria->add(new TFilter('id', 'not in', '(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '.GrupoPessoa::CONDUTOR.')')); // Ex
            $criteria_count = clone $criteria;
            $criteria->setProperties($param); // order, offset
            if ($this->embeddedMode)
            {
                $criteria->setProperty('offset', 0);
                $criteria->setProperty('limit', null);
            }
            else
            {
                $criteria->setProperty('limit', $this->limit);
            }
            if (TSession::getValue('pedido_frotas_id')) {
                try {
                    $pedido_frota_id = TSession::getValue('pedido_frotas_id');

                    $criteriaChecked = new TCriteria();
                    $criteriaChecked->add(new TFilter('pedido_frotas_id', '=', $pedido_frota_id));

                    $repoChecked = new TRepository('PedidoAsCliente');
                    $results = $repoChecked->load($criteriaChecked);

                    foreach ($results as $result) {
                        $checked_ids[$result->pessoa_id] = $result->pessoa_id;
                    }

                } catch (Exception $e) {
                    new TMessage('error', 'Erro ao buscar clientes do pedido: ' . $e->getMessage());
                }
            }

            // Prioriza na primeira pÃ¡gina as redes jÃ¡ marcadas (checklist/sessÃ£o/pedido)
            $isFirstPage = empty($param['offset']) || ((int) $param['offset'] === 0);
            if ($isFirstPage && !empty($checked_ids))
            {
                // sem ORDER BY CASE no SQL (muito caro em view); prioriza depois em memÃ³ria
            }

            $showAllAfterSearch = !$this->embeddedMode || (int) TSession::getValue(__CLASS__.'_show_all_after_search') === 1;
            $embeddedUnmarkedLimit = 300;

            // load the objects according to criteria
            if ($this->embeddedMode && !$showAllAfterSearch)
            {
                $checkedList = array_values(array_unique(array_map('intval', array_keys($checked_ids))));
                $checkedList = array_values(array_filter($checkedList));

                if (!empty($checkedList))
                {
                    $criteriaMarked = clone $criteria_count;
                    $criteriaMarked->add(new TFilter('id', 'in', $checkedList));
                    $criteriaMarked->setProperty('order', $param['order'] ?? 'id');
                    $criteriaMarked->setProperty('direction', $param['direction'] ?? 'desc');
                    $criteriaMarked->setProperty('offset', 0);
                    $criteriaMarked->setProperty('limit', null);
                    $objects = $repository->load($criteriaMarked, FALSE) ?: [];
                }
                else
                {
                    $objects = [];
                }
            }
            elseif ($isFirstPage && !empty($checked_ids))
            {
                $checkedList = array_values(array_unique(array_map('intval', array_keys($checked_ids))));
                $checkedList = array_values(array_filter($checkedList));

                $objects = [];
                $loadedIds = [];

                if (!empty($checkedList))
                {
                    $criteriaMarked = clone $criteria_count;
                    $criteriaMarked->add(new TFilter('id', 'in', $checkedList));
                    $criteriaMarked->setProperty('order', $param['order'] ?? 'id');
                    $criteriaMarked->setProperty('direction', $param['direction'] ?? 'desc');
                    $criteriaMarked->setProperty('limit', $this->embeddedMode ? null : $this->limit);
                    $criteriaMarked->setProperty('offset', 0);

                    $markedObjects = $repository->load($criteriaMarked, FALSE) ?: [];
                    foreach ($markedObjects as $markedObject)
                    {
                        $objects[] = $markedObject;
                        $loadedIds[(int) $markedObject->id] = (int) $markedObject->id;
                    }
                }

                $remaining = $this->embeddedMode
                    ? ($hasActiveFilters ? null : $embeddedUnmarkedLimit)
                    : max(0, $this->limit - count($objects));
                if ($this->embeddedMode || $remaining > 0)
                {
                    $criteriaRest = clone $criteria_count;
                    if (!empty($loadedIds))
                    {
                        $criteriaRest->add(new TFilter('id', 'not in', array_values($loadedIds)));
                    }
                    $criteriaRest->setProperty('order', $param['order'] ?? 'id');
                    $criteriaRest->setProperty('direction', $param['direction'] ?? 'desc');
                    $criteriaRest->setProperty('limit', $this->embeddedMode ? $remaining : $remaining);
                    $criteriaRest->setProperty('offset', 0);

                    $restObjects = $repository->load($criteriaRest, FALSE) ?: [];
                    $objects = array_merge($objects, $restObjects);
                }
            }
            else
            {
                if ($this->embeddedMode && $showAllAfterSearch && !$hasActiveFilters)
                {
                    $criteriaFast = clone $criteria_count;
                    $criteriaFast->setProperty('order', $param['order'] ?? 'id');
                    $criteriaFast->setProperty('direction', $param['direction'] ?? 'desc');
                    $criteriaFast->setProperty('limit', $embeddedUnmarkedLimit);
                    $criteriaFast->setProperty('offset', 0);
                    $objects = $repository->load($criteriaFast, FALSE);
                }
                else
                {
                    $objects = $repository->load($criteria, FALSE);
                }
            }

            $this->preloadCidadeEstadoMap($objects);
            $this->preloadSeguimentoMap($objects);
            if (empty(TSession::getValue('pedido_frotas_id')))
            {
                $this->preloadReputacaoMap($objects);
            }
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $check = new TCheckGroup('builder_datagrid_check');
                    $check->addItems([$object->id => '']);
                    $check->getButtons()[$object->id]->onclick = 'event.stopPropagation()';

                    if(!$this->datagrid_form->getField('builder_datagrid_check[]'))
                    {
                        $this->datagrid_form->setFields([$check]);
                    }

                    $check->setChangeAction(new TAction([$this, 'builderSelectCheck']));
                    $object->builder_datagrid_check = $check;

                    // ✅ Aqui você checa se está no session OR está vinculado ao pedido
                    if (!empty($session_checks[$object->id]) || !empty($checked_ids[$object->id]))
                    {
                        $object->builder_datagrid_check->setValue([$object->id => $object->id]);
                    }

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";
                }
            }
            $this->injectSeguimentoTooltipScript();
            TSession::setValue(__CLASS__.'builder_datagrid_check', $checked_ids);
            if (!$this->embeddedMode)
            {
                // reset the criteria for record count (sem limit/offset/order)
                $criteria_count->resetProperties();
                $criteria_count->setProperty('limit', null);
                $criteria_count->setProperty('offset', null);
                $criteria_count->setProperty('order', null);
                $criteria_count->setProperty('direction', null);
                $count= $repository->count($criteria_count);

                $this->pageNavigation->setCount($count); // count of records
                $this->pageNavigation->setProperties($param); // order, page
                $this->pageNavigation->setLimit($this->limit); // limit
            }

            //<onBeforeDatagridTransactionClose>

            //</onBeforeDatagridTransactionClose>

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
        //<onShow>
        // O método show() já chama onReload() automaticamente quando necessário
        //</onShow>
    }

    private function preloadCidadeEstadoMap($objects)
    {
        $this->cidadeEstadoMap = [];
        if (empty($objects) || !is_array($objects))
        {
            return;
        }

        $cidadeIds = [];
        foreach ($objects as $object)
        {
            if (!empty($object->cidade_id))
            {
                $cidadeIds[] = (int) $object->cidade_id;
            }
        }

        $cidadeIds = array_values(array_unique(array_filter($cidadeIds)));
        if (empty($cidadeIds))
        {
            return;
        }

        $criteriaCidade = new TCriteria;
        $criteriaCidade->add(new TFilter('id', 'in', $cidadeIds));
        $cidades = (new TRepository('Cidade'))->load($criteriaCidade, FALSE) ?: [];

        $estadoIds = [];
        foreach ($cidades as $cidade)
        {
            if (!empty($cidade->estado_id))
            {
                $estadoIds[] = (int) $cidade->estado_id;
            }
        }

        $estadoMap = [];
        $estadoIds = array_values(array_unique(array_filter($estadoIds)));
        if (!empty($estadoIds))
        {
            $criteriaEstado = new TCriteria;
            $criteriaEstado->add(new TFilter('id', 'in', $estadoIds));
            $estados = (new TRepository('Estado'))->load($criteriaEstado, FALSE) ?: [];
            foreach ($estados as $estado)
            {
                $estadoMap[(int) $estado->id] = $estado->sigla ?? '';
            }
        }

        foreach ($cidades as $cidade)
        {
            $sigla = $estadoMap[(int) $cidade->estado_id] ?? '';
            $this->cidadeEstadoMap[(int) $cidade->id] = trim(($cidade->nome ?? '') . ($sigla ? " - {$sigla}" : ''));
        }
    }

    private function preloadSeguimentoMap($objects)
    {
        $this->seguimentoMap = [];
        if (empty($objects) || !is_array($objects))
        {
            return;
        }

        $pessoaIds = [];
        foreach ($objects as $object)
        {
            if (!empty($object->id))
            {
                $pessoaIds[] = (int) $object->id;
            }
        }

        $pessoaIds = array_values(array_unique(array_filter($pessoaIds)));
        if (empty($pessoaIds))
        {
            return;
        }

        $criteriaSegPessoa = new TCriteria;
        $criteriaSegPessoa->add(new TFilter('pessoa_id', 'in', $pessoaIds));
        $seguimentosPessoa = (new TRepository('SeguimentoPessoa'))->load($criteriaSegPessoa, FALSE) ?: [];

        $seguimentoIds = [];
        $seguimentosPorPessoa = [];
        foreach ($seguimentosPessoa as $item)
        {
            $pessoaId = (int) ($item->pessoa_id ?? 0);
            $seguimentoId = (int) ($item->seguimento_id ?? 0);

            if (!$pessoaId || !$seguimentoId)
            {
                continue;
            }

            $seguimentosPorPessoa[$pessoaId][] = $seguimentoId;
            $seguimentoIds[] = $seguimentoId;
        }

        $seguimentoIds = array_values(array_unique(array_filter($seguimentoIds)));
        if (empty($seguimentoIds))
        {
            return;
        }

        $criteriaSeg = new TCriteria;
        $criteriaSeg->add(new TFilter('id', 'in', $seguimentoIds));
        $seguimentos = (new TRepository('Seguimento'))->load($criteriaSeg, FALSE) ?: [];

        $seguimentoDescricaoMap = [];
        foreach ($seguimentos as $seguimento)
        {
            $seguimentoDescricaoMap[(int) $seguimento->id] = (string) ($seguimento->descricao ?? '');
        }

        foreach ($seguimentosPorPessoa as $pessoaId => $ids)
        {
            $descricoes = [];
            foreach (array_unique($ids) as $seguimentoId)
            {
                if (!empty($seguimentoDescricaoMap[$seguimentoId]))
                {
                    $descricoes[] = $seguimentoDescricaoMap[$seguimentoId];
                }
            }

            if (!empty($descricoes))
            {
                $this->seguimentoMap[$pessoaId] = implode(', ', $descricoes);
            }
        }
    }
    private function preloadReputacaoMap($objects)
    {
        $this->reputacaoMap = [];
        if (empty($objects) || !is_array($objects))
        {
            return;
        }

        $pessoaIds = [];
        foreach ($objects as $object)
        {
            if (!empty($object->id))
            {
                $pessoaIds[] = (int) $object->id;
            }
        }

        $pessoaIds = array_values(array_unique(array_filter($pessoaIds)));
        if (empty($pessoaIds))
        {
            return;
        }

        $idunit = (int) (TSession::getValue('idunit') ?? 0);
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', 'in', $pessoaIds));
        if ($idunit > 0)
        {
            $criteria->add(new TFilter('system_unit_id', '=', $idunit));
        }

        $ouvidorias = (new TRepository('Ouvidoria'))->load($criteria, FALSE) ?: [];
        if (empty($ouvidorias))
        {
            return;
        }

        $tipoIds = [];
        foreach ($ouvidorias as $o)
        {
            $pid = (int) ($o->pessoa_id ?? 0);
            if (!isset($this->reputacaoMap[$pid]))
            {
                $this->reputacaoMap[$pid] = ['nota' => 0, 'total' => 0, 'cor' => '#FFD700', 'tipo_ouvidoria_id' => null];
            }
            $this->reputacaoMap[$pid]['total']++;
            $this->reputacaoMap[$pid]['nota']++;
            if (empty($this->reputacaoMap[$pid]['tipo_ouvidoria_id']) && !empty($o->tipo_ouvidoria_id))
            {
                $this->reputacaoMap[$pid]['tipo_ouvidoria_id'] = (int) $o->tipo_ouvidoria_id;
                $tipoIds[] = (int) $o->tipo_ouvidoria_id;
            }
        }

        $tipoIds = array_values(array_unique(array_filter($tipoIds)));
        if (!empty($tipoIds))
        {
            $criteriaTipo = new TCriteria;
            $criteriaTipo->add(new TFilter('id', 'in', $tipoIds));
            $tipos = (new TRepository('TipoOuvidoria'))->load($criteriaTipo, FALSE) ?: [];
            $corMap = [];
            foreach ($tipos as $tipo)
            {
                $corMap[(int) $tipo->id] = $tipo->cor ?? '#FFD700';
            }

            foreach ($this->reputacaoMap as $pid => $data)
            {
                $tipoId = (int) ($data['tipo_ouvidoria_id'] ?? 0);
                if ($tipoId && isset($corMap[$tipoId]))
                {
                    $this->reputacaoMap[$pid]['cor'] = $corMap[$tipoId];
                }
                unset($this->reputacaoMap[$pid]['tipo_ouvidoria_id']);
            }
        }
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

    //</hideLine> <addUserFunctionsCode/>

    public static function builderSelectCheck($param)
    {
        $session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');

        $valueOn = null;
        if(!empty($param['_field_data_json']))
        {
            $obj = json_decode($param['_field_data_json']);
            if($obj)
            {
                $valueOn = $obj->valueOn;
            }
        }

        $key = empty($param['key']) ? $valueOn : $param['key'];

        if(empty($param['builder_datagrid_check']) && !empty($session_checks[$key]))
        {
            unset($session_checks[$key]);
        }
        elseif(!empty($param['builder_datagrid_check']) && !in_array($key, $param['builder_datagrid_check']) && !empty($session_checks[$key]))
        {
            unset($session_checks[$key]);
        }
        elseif(!empty($param['builder_datagrid_check']) && in_array($key, $param['builder_datagrid_check']))
        {
            $session_checks[$key] = $key;
        }

        //<onBeforeSetSessionCheckValue>

        //</onBeforeSetSessionCheckValue>

        TSession::setValue(__CLASS__.'builder_datagrid_check', $session_checks);
    }

    public static function manageRow($id, $param = [])
    {
        $list = new self($param);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new ViewRedesdisponiveis($id);

        $session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');

        $check = new TCheckGroup('builder_datagrid_check');
        $check->addItems([$object->id => '']);
        $check->getButtons()[$object->id]->onclick = 'event.stopPropagation()';

        if(!$list->datagrid_form->getField('builder_datagrid_check[]'))
        {
            $list->datagrid_form->setFields([$check]);
        }

        $check->setChangeAction(new TAction([$list, 'builderSelectCheck']));
        $object->builder_datagrid_check = $check;

        if(!empty($session_checks[$object->id]))
        {
            $object->builder_datagrid_check->setValue([$object->id=>$object->id]);
        }

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    //<userCustomFunctions>

    //</userCustomFunctions>
    function onSetProject($param = null)
    {
        TSession::setValue(__CLASS__.'_show_all_after_search', 0);
        TSession::setValue(__CLASS__.'builder_datagrid_check', []);
        $this->onReload($param);
    }

}
