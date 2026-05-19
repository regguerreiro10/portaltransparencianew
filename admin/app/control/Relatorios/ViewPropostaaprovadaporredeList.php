<?php

class ViewPropostaaprovadaporredeList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private $systemUnitNameMap = [];
    private $departamentoNameMap = [];
    private $veiculoPlacaMap = [];
    private $estadoPedidoMap = [];
    private $propostaTemDocumentoMap = [];
    private static $database = 'minierp';
    private static $activeRecord = 'ViewPropostaaprovadaporrede';
    private static $primaryKey = 'id';
    private static $formName = 'form_ViewPropostaaprovadaporredeList';
    private $showMethods = ['onShow', 'onReload', 'onSearch', 'onRefresh', 'onClearFilters', 'onGlobalSearch'];
    private $limit = 20;

    use BuilderDatagridTrait;

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
        $this->form->setFormTitle("Listagem de Pedidos Aprovados por Estabelecimento, Veiculos, Aeronaves e/ou Equipamentos, Estado Pedido e Analítico");
        $this->limit = 20;

        $criteria_system_unit_id = new TCriteria();
        $criteria_departamento_unit_id = new TCriteria();
        $criteria_pessoa_id = new TCriteria();
        $criteria_veiculos_id = new TCriteria();
        $criteria_estado_pedido_frotas_id = new TCriteria();
        $criteria_marca_id = new TCriteria();
        $criteria_modelo_id = new TCriteria();
        $criteria_tipo_veiculo_id = new TCriteria();
        $criteria_motorista_entrada_id = new TCriteria();
        $criteria_aprovador_frotas_id = new TCriteria();
        $criteria_produto_id = new TCriteria();
        $criteria_servico_id = new TCriteria();

        $filterVar = TSession::getValue('idunit');
        $criteria_system_unit_id->add(new TFilter('id', '=', $filterVar)); 
        $filterVar = TSession::getValue('idunit');
        $criteria_departamento_unit_id->add(new TFilter('system_unit_id', '=', $filterVar)); 
        $filterVar = TSession::getValue('idunit');
        $criteria_veiculos_id->add(new TFilter('system_unit_id', '=', $filterVar)); 

        $criteria_produto_id->add(new TFilter('tipo_produto_id', '=', 1));
        $criteria_servico_id->add(new TFilter('tipo_produto_id', '=', 2));

        $system_unit_id = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_system_unit_id );
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_departamento_unit_id );
        $pessoa_id = new TDBUniqueSearch('pessoa_id', 'minierp', 'Pessoa', 'id', 'nome','nome asc' , $criteria_pessoa_id );
        $data_aprovacao = new BDateRange('data_aprovacao', 'data_aprovacao_final');
        $dt_pedido = new BDateRange('dt_pedido', 'dt_pedido_final');
        $veiculos_id = new TDBUniqueSearch('veiculos_id', 'minierp', 'Veiculos', 'id', 'placa','placa asc' , $criteria_veiculos_id );
        $marca_id = new TDBUniqueSearch('marca_id', 'minierp', 'Marca', 'id', 'descricao','descricao asc' , $criteria_marca_id );
        $modelo_id = new TDBUniqueSearch('modelo_id', 'minierp', 'Modelo', 'id', 'descricao','descricao asc' , $criteria_modelo_id );
        $anof = new TEntry('anof');
        $tipo_veiculo_id = new TDBCombo('tipo_veiculo_id', 'minierp', 'TipoVeiculo', 'id', '{descricao}','descricao asc' , $criteria_tipo_veiculo_id );
        $motorista_entrada_id = new TDBUniqueSearch('motorista_entrada_id', 'minierp', 'Pessoa', 'id', 'nome','nome asc' , $criteria_motorista_entrada_id );
        $aprovador_frotas_id = new TDBCombo('aprovador_frotas_id', 'minierp', 'AprovadorFrotas', 'id', '{system_users->name}','id asc' , $criteria_aprovador_frotas_id );
        $valor_total_inicial = new TNumeric('valor_total_inicial', '2', ',', '.');
        $valor_total_final = new TNumeric('valor_total_final', '2', ',', '.');
        $produto_id = new TDBUniqueSearch('produto_id', 'minierp', 'Produto', 'id', 'nome','nome asc' , $criteria_produto_id );
        $servico_id = new TDBUniqueSearch('servico_id', 'minierp', 'Produto', 'id', 'nome','nome asc' , $criteria_servico_id );
        $estado_pedido_frotas_id = new TDBSelect('estado_pedido_frotas_id', 'minierp', 'EstadoPedidoFrotas', 'id', '{nome}','nome asc' , $criteria_estado_pedido_frotas_id );

        $data_aprovacao->setMask('dd/mm/yyyy');
        $data_aprovacao->setDatabaseMask('yyyy-mm-dd');
        $dt_pedido->setMask('dd/mm/yyyy');
        $dt_pedido->setDatabaseMask('yyyy-mm-dd');
        $pessoa_id->setMinLength(2);
        $veiculos_id->setMinLength(2);
        $system_unit_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $marca_id->setMinLength(2);
        $modelo_id->setMinLength(2);
        $tipo_veiculo_id->enableSearch();
        $motorista_entrada_id->setMinLength(2);
        $aprovador_frotas_id->enableSearch();
        $produto_id->setMinLength(2);
        $servico_id->setMinLength(2);
        $estado_pedido_frotas_id->enableSearch();

        $pessoa_id->setMask('{nome}');
        $veiculos_id->setMask('{placa}');
        $motorista_entrada_id->setMask('{nome}');
        $produto_id->setMask('{nome}');
        $servico_id->setMask('{nome}');
        $pessoa_id->setFilterColumns(["nome"]);
        $veiculos_id->setFilterColumns(["placa"]);
        $motorista_entrada_id->setFilterColumns(["nome"]);
        $produto_id->setFilterColumns(["nome"]);
        $servico_id->setFilterColumns(["nome"]);

        $pessoa_id->setSize('100%');
        $data_aprovacao->setSize(220);
        $dt_pedido->setSize(220);
        $veiculos_id->setSize('100%');
        $system_unit_id->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $marca_id->setSize('100%');
        $modelo_id->setSize('100%');
        $anof->setSize('100%');
        $tipo_veiculo_id->setSize('100%');
        $motorista_entrada_id->setSize('100%');
        $aprovador_frotas_id->setSize('100%');
        $valor_total_inicial->setSize('100%');
        $valor_total_final->setSize('100%');
        $produto_id->setSize('100%');
        $servico_id->setSize('100%');
        $estado_pedido_frotas_id->setSize('100%', 70);

        $row1 = $this->form->addFields([new TLabel("Unidade:", null, '14px', null, '100%'),$system_unit_id],[new TLabel("Unidade / Dep / Secretaria:", null, '14px', null, '100%'),$departamento_unit_id]);
        $row1->layout = ['col-sm-6',' col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Estabelecimento:", null, '14px', null, '100%'),$pessoa_id],[new TLabel("Data Inicial e Final de aprovação:", null, '14px', null, '100%'),$data_aprovacao]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Placa:", null, '14px', null, '100%'),$veiculos_id],[new TLabel("Estado do pedido:", null, '14px', null, '100%'),$estado_pedido_frotas_id]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Marca:", null, '14px', null, '100%'),$marca_id],[new TLabel("Modelo:", null, '14px', null, '100%'),$modelo_id]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Ano do veiculo/equipamento:", null, '14px', null, '100%'),$anof],[new TLabel("Tipo do veiculo/equipamento:", null, '14px', null, '100%'),$tipo_veiculo_id]);
        $row5->layout = ['col-sm-6','col-sm-6'];

        $row6 = $this->form->addFields([new TLabel("Periodo de manutencao (Dt Pedido):", null, '14px', null, '100%'),$dt_pedido],[]);
        $row6->layout = ['col-sm-6','col-sm-6'];

        $row7 = $this->form->addFields([new TLabel("Motorista:", null, '14px', null, '100%'),$motorista_entrada_id],[new TLabel("Aprovador:", null, '14px', null, '100%'),$aprovador_frotas_id]);
        $row7->layout = ['col-sm-6','col-sm-6'];

        $row8 = $this->form->addFields([new TLabel("Valor total inicial:", null, '14px', null, '100%'),$valor_total_inicial],[new TLabel("Valor total final:", null, '14px', null, '100%'),$valor_total_final]);
        $row8->layout = ['col-sm-6','col-sm-6'];

        $row9 = $this->form->addFields([new TLabel("Produto:", null, '14px', null, '100%'),$produto_id],[new TLabel("Servico:", null, '14px', null, '100%'),$servico_id]);
        $row9->layout = ['col-sm-6','col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_pedido_frotas_id = new TDataGridColumn('pedido_frotas_id', "ID Pedido", 'left');
        $column_dt_pedido_transformed = new TDataGridColumn('dt_pedido', "Dt Pedido", 'left');
        $column_descricaopedido = new TDataGridColumn('descricaopedido', "Descricaopedido", 'left');
        $column_id = new TDataGridColumn('id', "ID Proposta", 'center' , '70px');
        $column_nome = new TDataGridColumn('nome', "Estabelecimento", 'left');
        $column_system_unit_id = new TDataGridColumn('system_unit_id', "Unidade", 'left');
        $column_departamento_unit_id = new TDataGridColumn('departamento_unit_id', "Unidade / Dep / Secretaria", 'left');
        $column_data_autorizacao_pagamento_transformed = new TDataGridColumn('data_autorizacao_pagamento', "Data Autorização PGTO", 'left');
        $column_nomeaprovadorpagamento = new TDataGridColumn('nomeaprovadorpagamento', "Nome Autorizou PGTO", 'left');
        $column_data_aprovacao_transformed = new TDataGridColumn('data_aprovacao', "Data Aprovação", 'left');
        $column_nomeaprovador = new TDataGridColumn('nomeaprovador', "Nome Aprovador", 'left');
        $column_veiculos_id = new TDataGridColumn('veiculos_id', "Placa", 'left');
        $column_total_produtos_sem_desconto_transformed = new TDataGridColumn('total_produtos_sem_desconto', "V. Produto SD", 'left');
        $column_total_servicos_sem_desconto_transformed = new TDataGridColumn('total_servicos_sem_desconto', "V. Serviço SD", 'left');
        $column_total_geral_sem_desconto_transformed = new TDataGridColumn('total_geral_sem_desconto', "V. Geral SD", 'left');
        $column_total_produtos_com_desconto_transformed = new TDataGridColumn('total_produtos_com_desconto', "V. Produto CD", 'left');
        $column_total_servicos_com_desconto_transformed = new TDataGridColumn('total_servicos_com_desconto', "V. Serviço CD", 'left');
        $column_total_geral_com_desconto_transformed = new TDataGridColumn('total_geral_com_desconto', "V. Geral CD", 'left');
        $column_datahora_inicioservico_transformed = new TDataGridColumn('datahora_inicioservico', "Dt Inicio Serviço", 'left');
        $column_datahora_fimservico_transformed = new TDataGridColumn('datahora_fimservico', "Dt Final Serviço", 'left');
        $column_diasindisponiveis_transformed = new TDataGridColumn('', "Dias Indisponiveis", 'left');
        $column_estado_pedido_frotas_id_transformed = new TDataGridColumn('estado_pedido_frotas_id', "Estado Pedido", 'left');

        $column_dt_pedido_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y H:i');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_diasindisponiveis_transformed->setTransformer(function($value, $object)
        {
            if (empty($object->datahora_inicioservico) || empty($object->datahora_fimservico)) {
                return '';
            }

             // Usa a função criada e pega o timestamp das duas datas:
                $time_inicial = $this->geraTimestamp($object->datahora_inicioservico);
                if (empty($object->datahora_fimservico)) {
                    $time_final = $this->geraTimestamp(Date('Y-m-d'));
                }
                else {
                   $time_final = $this->geraTimestamp($object->datahora_fimservico);
                }

                // Calcula a diferença de segundos entre as duas datas:
                $diferenca = $time_final - $time_inicial; // 19522800 segundos

                // Calcula a diferença de dias
                $nrdiasatras = (int)floor($diferenca / (60 * 60 * 24));

                return $nrdiasatras;

            // $ini = DateTime::createFromFormat('d/m/Y', $object->datahora_inicioservico);
            // $fim = DateTime::createFromFormat('d/m/Y', $object->datahora_fimservico);

            // if (!$ini || !$fim) {
            //     return '';
            // }

            // $dias = ($fim - $ini); // diferença em dias
            // return $dias; // (se quiser incluir o dia inicial: return $dias + 1;)
        });





        $column_data_autorizacao_pagamento_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y H:i');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_data_aprovacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y H:i');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_total_produtos_sem_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_total_servicos_sem_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_total_geral_sem_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_total_produtos_com_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_total_servicos_com_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_total_geral_com_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_datahora_inicioservico_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_datahora_fimservico_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_system_unit_id->setTransformer(function($value)
        {
            return $this->systemUnitNameMap[(int) $value] ?? '';
        });

        $column_departamento_unit_id->setTransformer(function($value)
        {
            return $this->departamentoNameMap[(int) $value] ?? '';
        });

        $column_veiculos_id->setTransformer(function($value)
        {
            return $this->veiculoPlacaMap[(int) $value] ?? '';
        });

       // troque todo o transformer atual por este:
          $column_estado_pedido_frotas_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
              //code here
            $temnotafiscal = !empty($this->propostaTemDocumentoMap[(int) $object->id]);
            $revisao = '';
            if (TSession::getValue('testar_revisao')==1) {            
                //entrou em revisão
                $revisao = '';
                if (!empty($object->estado_pedido_frotas1_id)) {
                    $nomeRevisao = $this->estadoPedidoMap[(int) $object->estado_pedido_frotas1_id]['nome'] ?? '';
                    if ($nomeRevisao) {
                        $revisao = "<span style='font-size: 10px; font-style: italic; color: #eee;'>({$nomeRevisao})</span>";
                    }
                }
            }
             if ($object->estado_pedido_frotas_id) {
                $estado_pedido_frotas = $this->estadoPedidoMap[(int) $object->estado_pedido_frotas_id] ?? ['nome' => '', 'cor' => '#777'];
                if ($temnotafiscal) {
                    $anexo = $estado_pedido_frotas['nome'] . " <i class='fa fa-paperclip' aria-hidden='true'></i>";
                } else {
                    $anexo = $estado_pedido_frotas['nome'];
                }

                return "<span class='label label-default' style='width:260px; background-color:{$estado_pedido_frotas['cor']}; display:inline-block;'> {$anexo} {$revisao} </span>";
            } else {
                return '';
            }

        });


        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_pedido_frotas_id);
        $this->datagrid->addColumn($column_dt_pedido_transformed);
        $this->datagrid->addColumn($column_descricaopedido);
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_system_unit_id);
        $this->datagrid->addColumn($column_departamento_unit_id);
        $this->datagrid->addColumn($column_data_autorizacao_pagamento_transformed);
        $this->datagrid->addColumn($column_nomeaprovadorpagamento);
        $this->datagrid->addColumn($column_data_aprovacao_transformed);
        $this->datagrid->addColumn($column_nomeaprovador);
        $this->datagrid->addColumn($column_veiculos_id);
        $this->datagrid->addColumn($column_total_produtos_sem_desconto_transformed);
        $this->datagrid->addColumn($column_total_servicos_sem_desconto_transformed);
        $this->datagrid->addColumn($column_total_geral_sem_desconto_transformed);
        $this->datagrid->addColumn($column_total_produtos_com_desconto_transformed);
        $this->datagrid->addColumn($column_total_servicos_com_desconto_transformed);
        $this->datagrid->addColumn($column_total_geral_com_desconto_transformed);
        $this->datagrid->addColumn($column_datahora_inicioservico_transformed);
        $this->datagrid->addColumn($column_datahora_fimservico_transformed);
        $this->datagrid->addColumn($column_diasindisponiveis_transformed);
        $this->datagrid->addColumn($column_estado_pedido_frotas_id_transformed);
        $action_onImprimir = new TDataGridAction(['ViewPropostaaprovadaporredeList', 'onImprimir']);
        $action_onImprimir->setUseButton(false);
        $action_onImprimir->setButtonClass('btn btn-default btn-sm');
        $action_onImprimir->setLabel("Orçamento");
        $action_onImprimir->setImage('far:file-pdf #000000');
        $action_onImprimir->setField(self::$primaryKey);

        // Passar propostas_id como parâmetro adicional
        $action_onImprimir->setParameter('id', '{propostas_id}');

        // Mostrar apenas se o pedido_frotas_id for numérico
        $action_onImprimir->setDisplayCondition(function($object) {
            return is_numeric($object->pedido_frotas_id);
        });

        $this->datagrid->addAction($action_onImprimir);

        $this->applyDatagridProperties();
        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de Pedidos Aprovados por Estabelecimento, Veiculos, Aeronaves e/ou Equipamentos, Estado Pedido e Analítico");
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;

        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $this->datagrid_form->add($headerActions);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['ViewPropostaaprovadaporredeList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['ViewPropostaaprovadaporredeList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['ViewPropostaaprovadaporredeList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['ViewPropostaaprovadaporredeList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['ViewPropostaaprovadaporredeList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['ViewPropostaaprovadaporredeList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['ViewPropostaaprovadaporredeList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );
        $dropdown_button_exportar->addPostAction( "HTML", new TAction(['ViewPropostaaprovadaporredeList', 'onExportHtml'],['static' => 1]), 'datagrid_'.self::$formName, 'fab:html5 #E34F26'  );

        $head_left_actions->add($button_atualizar);
        $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($btnShowCurtainFilters);

        $head_right_actions->add($dropdown_button_exportar);

        $this->datagrid_form->add($this->datagrid);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
        //    $container->add(TBreadCrumb::create(["Manutenção Frotas","Listagem das propostas aprovadas por rede"]));
        }

        $container->add($panel);

        parent::add($container);

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
                $columnCount = count($this->datagrid->getColumns());
                $pdfFontSize = $columnCount >= 20 ? 5 : ($columnCount >= 16 ? 6 : ($columnCount >= 12 ? 7 : 8));
                $pdfBodyFontSize = $pdfFontSize + 1;
                $pdfCellPadding = $columnCount >= 18 ? '1px 2px' : '2px 3px';
                $pdfStyles = '
                <style>
                    @page { margin: 12px; }
                    body { font-size: ' . $pdfBodyFontSize . 'px; }
                    table {
                        width: 100% !important;
                        max-width: 100% !important;
                        table-layout: fixed !important;
                    }
                    table th,
                    table td,
                    .tdatagrid_cell {
                        font-size: ' . $pdfFontSize . 'px !important;
                        line-height: 1.15 !important;
                        padding: ' . $pdfCellPadding . ' !important;
                        white-space: normal !important;
                        overflow-wrap: anywhere !important;
                        word-break: break-word !important;
                    }
                    .label {
                        width: auto !important;
                        min-width: 0 !important;
                        max-width: 100% !important;
                        font-size: ' . $pdfFontSize . 'px !important;
                        line-height: 1.15 !important;
                        white-space: normal !important;
                    }
                </style>';
                $contents = file_get_contents('app/resources/styles-print.html') . file_get_contents('app/resources/styles-print-list.html') . PdfListHeader::render(__CLASS__) . $pdfStyles . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'landscape');
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

    public function onRefresh($param = null) 
    {
        $this->onReload([]);
    }

    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

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

    public static function onShowCurtainFilters($param = null) 
    {
        try 
        {
            //code here

                        $filter = new self([]);

            $btnClose = new TButton('closeCurtain');
            $btnClose->class = 'btn btn-sm btn-default';
            $btnClose->style = 'margin-right:10px;';
            $btnClose->onClick = "Template.closeRightPanel();";
            $btnClose->setLabel("Fechar");
            $btnClose->setImage('fas:times');

            $filter->form->addHeaderWidget($btnClose);

            $page = new TPage();
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('page-name', 'ViewPropostaaprovadaporredeListSearch');
            $page->setProperty('page_name', 'ViewPropostaaprovadaporredeListSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();

            $style = new TStyle('right-panel > .container-part[page-name=ViewPropostaaprovadaporredeListSearch]');
            $style->width = '50% !important';
            $style->show(true);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->system_unit_id) AND ( (is_scalar($data->system_unit_id) AND $data->system_unit_id !== '') OR (is_array($data->system_unit_id) AND (!empty($data->system_unit_id)) )) )
        {

            $filters[] = new TFilter('system_unit_id', '=', $data->system_unit_id);// create the filter 
        }

        if (isset($data->departamento_unit_id) AND ( (is_scalar($data->departamento_unit_id) AND $data->departamento_unit_id !== '') OR (is_array($data->departamento_unit_id) AND (!empty($data->departamento_unit_id)) )) )
        {

            $filters[] = new TFilter('departamento_unit_id', '=', $data->departamento_unit_id);// create the filter 
        }

        if (isset($data->pessoa_id) AND ( (is_scalar($data->pessoa_id) AND $data->pessoa_id !== '') OR (is_array($data->pessoa_id) AND (!empty($data->pessoa_id)) )) )
        {

            $filters[] = new TFilter('pessoa_id', '=', $data->pessoa_id);// create the filter 
        }

        if (isset($data->data_aprovacao_final) AND ( (is_scalar($data->data_aprovacao_final) AND $data->data_aprovacao_final !== '') OR (is_array($data->data_aprovacao_final) AND (!empty($data->data_aprovacao_final)) )) )
        {

            $filters[] = new TFilter('data_aprovacao', '<=', $data->data_aprovacao_final);// create the filter 
        }

        if (isset($data->data_aprovacao) AND ( (is_scalar($data->data_aprovacao) AND $data->data_aprovacao !== '') OR (is_array($data->data_aprovacao) AND (!empty($data->data_aprovacao)) )) )
        {

            $filters[] = new TFilter('data_aprovacao', '>=', $data->data_aprovacao);// create the filter 
        }

        if (isset($data->estado_pedido_frotas_id) AND ( (is_scalar($data->estado_pedido_frotas_id) AND $data->estado_pedido_frotas_id !== '') OR (is_array($data->estado_pedido_frotas_id) AND (!empty($data->estado_pedido_frotas_id)) )) )
        {

            $filters[] = new TFilter('estado_pedido_frotas_id', '=', $data->estado_pedido_frotas_id);// create the filter 
        }

        if (isset($data->veiculos_id) AND ( (is_scalar($data->veiculos_id) AND $data->veiculos_id !== '') OR (is_array($data->veiculos_id) AND (!empty($data->veiculos_id)) )) )
        {

            $filters[] = new TFilter('veiculos_id', '=', $data->veiculos_id);// create the filter 
        }

        if (isset($data->marca_id) AND ( (is_scalar($data->marca_id) AND $data->marca_id !== '') OR (is_array($data->marca_id) AND (!empty($data->marca_id)) )) )
        {
            $filters[] = new TFilter('veiculos_id', 'in', '(select id from veiculos where marca_id = '.(int) $data->marca_id.')');
        }

        if (isset($data->modelo_id) AND ( (is_scalar($data->modelo_id) AND $data->modelo_id !== '') OR (is_array($data->modelo_id) AND (!empty($data->modelo_id)) )) )
        {
            $filters[] = new TFilter('veiculos_id', 'in', '(select id from veiculos where modelo_id = '.(int) $data->modelo_id.')');
        }

        if (isset($data->anof) AND ( (is_scalar($data->anof) AND $data->anof !== '') OR (is_array($data->anof) AND (!empty($data->anof)) )) )
        {
            $filters[] = new TFilter('veiculos_id', 'in', "(select id from veiculos where anof = '{$data->anof}')");
        }

        if (isset($data->tipo_veiculo_id) AND ( (is_scalar($data->tipo_veiculo_id) AND $data->tipo_veiculo_id !== '') OR (is_array($data->tipo_veiculo_id) AND (!empty($data->tipo_veiculo_id)) )) )
        {
            $filters[] = new TFilter('veiculos_id', 'in', '(select id from veiculos where tipo_veiculo_id = '.(int) $data->tipo_veiculo_id.')');
        }

        if (isset($data->dt_pedido_final) AND ( (is_scalar($data->dt_pedido_final) AND $data->dt_pedido_final !== '') OR (is_array($data->dt_pedido_final) AND (!empty($data->dt_pedido_final)) )) )
        {
            $filters[] = new TFilter('dt_pedido', '<=', $data->dt_pedido_final);
        }

        if (isset($data->dt_pedido) AND ( (is_scalar($data->dt_pedido) AND $data->dt_pedido !== '') OR (is_array($data->dt_pedido) AND (!empty($data->dt_pedido)) )) )
        {
            $filters[] = new TFilter('dt_pedido', '>=', $data->dt_pedido);
        }

        if (isset($data->motorista_entrada_id) AND ( (is_scalar($data->motorista_entrada_id) AND $data->motorista_entrada_id !== '') OR (is_array($data->motorista_entrada_id) AND (!empty($data->motorista_entrada_id)) )) )
        {
            $filters[] = new TFilter('motorista_entrada_id', '=', $data->motorista_entrada_id);
        }

        if (isset($data->aprovador_frotas_id) AND ( (is_scalar($data->aprovador_frotas_id) AND $data->aprovador_frotas_id !== '') OR (is_array($data->aprovador_frotas_id) AND (!empty($data->aprovador_frotas_id)) )) )
        {
            $filters[] = new TFilter('pedido_frotas_id', 'in', '(select pedido_frotas_id from pedido_frotas_historico where estado_pedido_frotas_id = '.EstadoPedidoFrotas::APROVADO.' and aprovador_frotas_id = '.(int) $data->aprovador_frotas_id.')');
        }

        if (isset($data->valor_total_inicial) AND ( (is_scalar($data->valor_total_inicial) AND $data->valor_total_inicial !== '') OR (is_array($data->valor_total_inicial) AND (!empty($data->valor_total_inicial)) )) )
        {
            $filters[] = new TFilter('total_geral_com_desconto', '>=', $this->parseMoneyToFloat($data->valor_total_inicial));
        }

        if (isset($data->valor_total_final) AND ( (is_scalar($data->valor_total_final) AND $data->valor_total_final !== '') OR (is_array($data->valor_total_final) AND (!empty($data->valor_total_final)) )) )
        {
            $filters[] = new TFilter('total_geral_com_desconto', '<=', $this->parseMoneyToFloat($data->valor_total_final));
        }

        if (isset($data->produto_id) AND ( (is_scalar($data->produto_id) AND $data->produto_id !== '') OR (is_array($data->produto_id) AND (!empty($data->produto_id)) )) )
        {
            $filters[] = new TFilter('id', 'in', '(select propostas_id from itens_propostas where produto_id = '.(int) $data->produto_id.' and produto_id in (select id from produto where tipo_produto_id = 1))');
        }

        if (isset($data->servico_id) AND ( (is_scalar($data->servico_id) AND $data->servico_id !== '') OR (is_array($data->servico_id) AND (!empty($data->servico_id)) )) )
        {
            $filters[] = new TFilter('id', 'in', '(select propostas_id from itens_propostas where produto_id = '.(int) $data->servico_id.' and produto_id in (select id from produto where tipo_produto_id = 2))');
        }

        // fill the form with data again
        $this->form->setData($data);

        if (false && empty($filters))
        {
            TSession::setValue(__CLASS__.'_filter_data', $data);
            TSession::setValue(__CLASS__.'_filters', NULL);
            new TMessage('warning', 'Informe pelo menos um filtro para consultar o relatório.');

            return;
        }

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
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

            // creates a repository for ViewPropostaaprovadaporrede
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

            $filters = TSession::getValue(__CLASS__.'_filters');

            if (false && empty($filters))
            {
                $this->datagrid->clear();
                $this->pageNavigation->setCount(0);
                $this->pageNavigation->setProperties($param);
                $this->pageNavigation->setLimit($this->limit);
                TTransaction::close();
                $this->loaded = true;

                return [];
            }

            if($filters)
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            //</blockLine><btnShowCurtainFiltersAutoCode>
            if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }
            //</blockLine></btnShowCurtainFiltersAutoCode>
            $criteria->add(new TFilter('system_unit_id', '=',TSession::getValue('idunit')));

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            $this->preloadGridRelations($objects);

            $total_produtos_sem_desconto =0;
            $total_servicos_sem_desconto =0;
            $total_geral_sem_desconto =0;
            $total_produtos_com_desconto =0;
            $total_servicos_com_desconto =0;
            $total_geral_com_desconto =0;
            $total_qtde=0;

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->pedido_frotas_id}";
                        $total_produtos_sem_desconto = $total_produtos_sem_desconto + $object->total_produtos_sem_desconto;
                        $total_servicos_sem_desconto = $total_servicos_sem_desconto + $object->total_servicos_sem_desconto;
                        $total_geral_sem_desconto = $total_geral_sem_desconto + $object->total_geral_sem_desconto;
                        $total_produtos_com_desconto = $total_produtos_com_desconto + $object->total_produtos_com_desconto;
                        $total_servicos_com_desconto = $total_servicos_com_desconto + $object->total_servicos_com_desconto;
                        $total_geral_com_desconto = $total_geral_com_desconto + $object->total_geral_com_desconto;
                        $total_qtde ++;
                }
            }

            $total_object = new stdClass;
            $total_object->descricaopedido = 'TOTAL GERAL'; // necessário para evitar erro
            $total_object->pedido_frotas_id = ''; // necessário para evitar erro
            $total_object->id = ''; // necessário para evitar erro
            $total_object->dt_pedido = ''; // necessário para evitar erro
            $total_object->estado_pedido_frotas_id = '';
            $total_object->system_unit_id = '';
            $total_object->departamento_unit_id = '';
            $total_object->nome = '';
            $total_object->data_aprovacao = '';
            $total_object->nomeaprovador = '';
            $total_object->data_aprovacao_pagamento = '';
            $total_object->nomeaprovadorpagamento = '';
            $total_object->datahora_inicioservico = '';
            $total_object->datahora_fimservico = '';
            $total_object->veiculos_id     = $total_qtde;
            $total_object->total_produtos_sem_desconto = number_format($total_produtos_sem_desconto ?? 0, 2, ',', '.');
            $total_object->total_servicos_sem_desconto = number_format($total_servicos_sem_desconto ?? 0, 2, ',', '.');
            $total_object->total_geral_sem_desconto = number_format($total_geral_sem_desconto ?? 0, 2, ',', '.');
            $total_object->total_produtos_com_desconto = number_format($total_produtos_com_desconto ?? 0, 2, ',', '.');
            $total_object->total_servicos_com_desconto = number_format($total_servicos_com_desconto ?? 0, 2, ',', '.');
            $total_object->total_geral_com_desconto = number_format($total_geral_com_desconto ?? 0, 2, ',', '.');

            $row = $this->datagrid->addItem($total_object);
            $row->id = "row_TOTAL";
            $row->style = 'font-weight: bold; background: #f1f1f1';
 
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

    private function preloadGridRelations($objects)
    {
        $this->systemUnitNameMap = [];
        $this->departamentoNameMap = [];
        $this->veiculoPlacaMap = [];
        $this->estadoPedidoMap = [];
        $this->propostaTemDocumentoMap = [];

        if (empty($objects))
        {
            return;
        }

        $systemUnitIds = [];
        $departamentoIds = [];
        $veiculoIds = [];
        $estadoIds = [];
        $propostaIds = [];

        foreach ($objects as $object)
        {
            if (!empty($object->system_unit_id))           $systemUnitIds[] = (int) $object->system_unit_id;
            if (!empty($object->departamento_unit_id))     $departamentoIds[] = (int) $object->departamento_unit_id;
            if (!empty($object->veiculos_id))              $veiculoIds[] = (int) $object->veiculos_id;
            if (!empty($object->estado_pedido_frotas_id))  $estadoIds[] = (int) $object->estado_pedido_frotas_id;
            if (!empty($object->estado_pedido_frotas1_id)) $estadoIds[] = (int) $object->estado_pedido_frotas1_id;
            if (!empty($object->id))                       $propostaIds[] = (int) $object->id;
        }

        foreach ($this->loadByIds('SystemUnit', $systemUnitIds) as $item)
        {
            $this->systemUnitNameMap[(int) $item->id] = $item->name;
        }

        foreach ($this->loadByIds('DepartamentoUnit', $departamentoIds) as $item)
        {
            $this->departamentoNameMap[(int) $item->id] = $item->name;
        }

        foreach ($this->loadByIds('Veiculos', $veiculoIds) as $item)
        {
            $this->veiculoPlacaMap[(int) $item->id] = $item->placa;
        }

        foreach ($this->loadByIds('EstadoPedidoFrotas', $estadoIds) as $item)
        {
            $this->estadoPedidoMap[(int) $item->id] = [
                'nome' => $item->nome,
                'cor'  => $item->cor ?: '#777'
            ];
        }

        $propostaIds = array_values(array_unique(array_filter($propostaIds)));
        if (!empty($propostaIds))
        {
            $criteria = new TCriteria;
            $criteria->add(new TFilter('propostas_id', 'in', $propostaIds));
            $documentos = (new TRepository('DocumentosPropostas'))->load($criteria, FALSE);

            if ($documentos)
            {
                foreach ($documentos as $documento)
                {
                    $this->propostaTemDocumentoMap[(int) $documento->propostas_id] = true;
                }
            }
        }
    }

    private function loadByIds($activeRecord, array $ids)
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (empty($ids))
        {
            return [];
        }

        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', 'in', $ids));

        return (new TRepository($activeRecord))->load($criteria, FALSE) ?: [];
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

        $object = new ViewPropostaaprovadaporrede($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }
    
//<generated-DatagridAction-onImprimir>
    public function onImprimir($param = null) 
    {
        try 
        {
            $param['id'] = $param['key'];
            //code here
            include_once 'app/control/mfrotas/PedidoFrotasOrcamento.php';

            $orcamento = new PropostaOrcamento;
            $orcamento->gerar($param);
            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
  public function onExportHtml($param = null)
    {
        try
        {
            $output = 'app/output/' . uniqid() . '.html';

            if ((!file_exists($output) && is_writable(dirname($output))) || is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    TTransaction::open(self::$database);

                    $datas = array_filter(array_map(fn($obj) => $obj->data_aprovacao ?? null, $objects));
                    $periodo_txt = !empty($datas) 
                        ? 'Período: ' . date('d/m/Y', strtotime(min($datas))) . ' a ' . date('d/m/Y', strtotime(max($datas)))
                        : 'Período não informado';

                    $html = '<html><head><meta charset="utf-8"><title>RelatorioPedidosAprovadosEstabelecimento</title>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px; }
                        .header { display: flex; align-items: center; margin-bottom: 20px; }
                        .logo { width: 150px; }
                        .title-block { flex: 1; text-align: center; }
                        .title-block h1 { margin: 0; font-size: 18px; }
                        .title-block h3 { margin: 0; font-weight: normal; font-size: 14px; }
                        table.bordasimples { border-collapse: collapse; width: 100%; table-layout: auto; }
                        table.bordasimples th, table.bordasimples td {
                            border: 1px solid #646161;
                            padding: 4px 6px;
                            text-align: left;
                            white-space: nowrap;
                            width: auto;
                            font-size: 11px;
                        }
                        table.bordasimples thead { background: #ccc; }
                        tr.total { background: #eee; font-weight: bold; }
                        .col-descricao { min-width: 200px; max-width: 400px; white-space: normal; }
                        .col-valor_total_produto, .col-valor_total_servico { max-width: 120px; text-align: right; }
                    </style>
                    </head><body>';

                    $html .= '<div class="header">
                                <img src="app/images/logo.png" class="logo">
                                <div class="title-block">
                                    <h1>Relatório de Pedidos Aprovados por Itens Produtos/Serviços, Estabelecimento, Veículo, Estado Pedido e Analítico</h1>
                                    <h3>' . $periodo_txt . '</h3>
                                </div>
                            </div>';

                    $html .= '<table class="bordasimples"><thead><tr>';

                    foreach ($this->datagrid->getColumns() as $column)
                    {
                        $column_name = $column->getName();
                        $html .= '<th class="col-' . $column_name . '">' . $column->getLabel() . '</th>';
                    }

                    $html .= '</tr></thead><tbody>';

                    $totais = [
                        'qtd' => 0,
                        'total_produtos_com_desconto' => 0,
                        'total_servicos_com_desconto' => 0,
                        'total_geral_com_desconto' => 0,
                        'total_produtos_sem_desconto' => 0,
                        'total_servicos_sem_desconto' => 0,
                        'total_geral_sem_desconto' => 0,
                    ];

                    $campos_monetarios = [
                        'total_produtos_com_desconto',
                        'total_servicos_com_desconto',
                        'total_geral_com_desconto',
                        'total_produtos_sem_desconto',
                        'total_servicos_sem_desconto',
                        'total_geral_sem_desconto',
                    ];

                    foreach ($objects as $object)
                    {
                        $html .= '<tr>';

                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $value = '';

                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';

                                if (preg_match('/^(dt_|data)/i', $column_name) && strtotime($value)) {
                                    $value = date('d/m/Y', strtotime($value));
                                }

                                if ($column_name == 'tipo') {
                                    $value = ($object->$column_name == 1) ? 'Produto' :
                                            (($object->$column_name == 2) ? 'Serviço' : 'Outro');
                                }

                                if ($column_name == 'estado_pedido_frotas_id') {
                                    try {
                                        $estado = new EstadoPedidoFrotas($object->$column_name);
                                        $value = $estado->nome;
                                    } catch (Exception $e) {
                                        $value = 'N/A';
                                    }
                                }
                                elseif ($column_name == 'motorista_entrada_id' || $column_name == 'motorista_retirada_id') {
                                    try {
                                        $pessoa = new Pessoa($object->$column_name);
                                        $value = $pessoa->nome;
                                    } catch (Exception $e) {
                                        $value = 'N/A';
                                    }
                                }
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ('{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                            }

                            if (array_key_exists($column_name, $totais) && is_numeric($value)) {
                                $totais[$column_name] += $value;
                            }

                            if (in_array($column_name, $campos_monetarios) && is_numeric($value)) {
                                $value = 'R$ ' . number_format($value, 2, ',', '.');
                            }
                            elseif (in_array($column_name, ['qtd']) && is_numeric($value)) {
                                $value = number_format($value, 0, ',', '.');
                            }

                            $html .= '<td class="col-' . $column_name . '">' . htmlspecialchars($value) . '</td>';
                        }

                        $html .= '</tr>';
                    }

                    $html .= '<tr class="total">';
                    foreach ($this->datagrid->getColumns() as $column)
                    {
                        $col_name = $column->getName();
                        if (isset($totais[$col_name]))
                        {
                            if (in_array($col_name, ['qtd'])) {
                                $html .= '<td class="col-' . $col_name . '">' . number_format($totais[$col_name], 0, ',', '.') . '</td>';
                            }
                            elseif (in_array($col_name, $campos_monetarios)) {
                                $html .= '<td class="col-' . $col_name . '">R$ ' . number_format($totais[$col_name], 2, ',', '.') . '</td>';
                            }
                            else {
                                $html .= '<td class="col-' . $col_name . '">' . $totais[$col_name] . '</td>';
                            }
                        }
                        else {
                            $html .= '<td></td>';
                        }
                    }
                    $html .= '</tr>';

                    $emissao = $this->getCurrentEmissionDateTime();
                    $urlAtual = htmlspecialchars($this->getCurrentReportUrl(), ENT_QUOTES, 'UTF-8');
                    $html .= "<br><br><div style='font-size:14px; text-align:right; color:#555;'>
                                Emitido em: {$urlAtual}    Data e Hora: {$emissao}
                            </div>";

                    $html .= '</body></html>';
                    file_put_contents($output, $html);
                    TTransaction::close();

                    TPage::openFile($output);
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }
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
    }
    function geraTimestamp($data)
    {
        if (empty($data)) {
            return null;
        }

        // Aceita "YYYY-mm-dd" e também "YYYY-mm-dd HH:ii:ss"
        $data = substr(trim($data), 0, 10);

        $ano = (int) substr($data, 0, 4);
        $mes = (int) substr($data, 5, 2);
        $dia = (int) substr($data, 8, 2);

        return mktime(0, 0, 0, $mes, $dia, $ano);
    }

    private function getCurrentReportUrl(): string
    {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        return $scheme . '://' . $host . $uri;
    }

    private function getCurrentEmissionDateTime(): string
    {
        return (new DateTime('now', new DateTimeZone('America/Cuiaba')))->format('d/m/Y H:i');
    }

    private function parseMoneyToFloat($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = (string) $value;
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }

   
}

