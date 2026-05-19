<?php

class SystemUnitForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'SystemUnit';
    private static $primaryKey = 'id';
    private static $formName = 'form_SystemUnit';

    use BuilderMasterDetailTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Orgão");

        $criteria_cidade_id = new TCriteria();
        $criteria_entidade_id = new TCriteria();
        $criteria_departamento_unit_system_unit_cidade_id = new TCriteria();

        $id = new TEntry('id');
        $cnpj = new TEntry('cnpj');
        $button_buscar = new TButton('button_buscar');
        $name = new TEntry('name');
        $email = new TEntry('email');
        $cep = new TEntry('cep');
        $button_buscar_endereco = new TButton('button_buscar_endereco');
        $rua = new TEntry('rua');
        $numero = new TEntry('numero');
        $bairro = new TEntry('bairro');
        $cidade_id = new TDBCombo('cidade_id', 'minierp', 'Cidade', 'id', '{nome} - {estado->sigla}','nome asc' , $criteria_cidade_id );
        $complemento = new TEntry('complemento');
        $telefone01 = new TEntry('telefone01');
        $telefone02 = new TEntry('telefone02');
        $telefone03 = new TEntry('telefone03');
        $utilizasinapi = new TCheckButton('utilizasinapi');
        $selecao_redes_aleatoria = new TCheckButton('selecao_redes_aleatoria');
        $recaptcha = new TCheckButton('recaptcha');
        $testar_valor_venal = new TCheckButton('testar_valor_venal');
        $utiliza_temparia = new TCheckButton('utiliza_temparia');
        $bloqueio_valor_temparia = new TCheckButton('bloqueio_valor_temparia');
        $exige_dotacao_empenho_frotas = new TCheckButton('exige_dotacao_empenho_frotas');
        $button_google_maps = new TButton('button_google_maps');
                $button_google_maps->addStyleClass('btn-default');
        $button_google_maps->setImage('fas:external-link-alt #000000');

        $button_google_maps->setAction(new TAction([$this, 'onAcesso']), "google maps");

        $aprovacao_por_item = new TCheckButton('aprovacao_por_item');
        $pedido_base = new TCheckButton('pedido_base');
        $testar_revisao = new TCheckButton('testar_revisao');
        $exibir_popup_plano_manutencao = new TCheckButton('exibir_popup_plano_manutencao');
        $checklist_vistoria_veiculo = new TCheckButton('checklist_vistoria_veiculo');
        $enviar_email_auto_relatorio = new TCheckButton('enviar_email_auto_relatorio');
        $garantia_dias = new TEntry('garantia_dias');
        $garantia_km = new TNumeric('garantia_km', '2', ',', '.' );
        $percentual_produto_similar = new TNumeric('percentual_produto_similar', '2', ',', '.' );

        $entidade = new TDBCombo('entidade_id', 'minierp', 'Entidade', 'id', '{nome}', 'nome asc', $criteria_entidade_id);
        $longitude = new TEntry('longitude');
        $latitude = new TEntry('latitude');
                $valor_base_aprovacao = new TNumeric('valor_base_aprovacao', '2', ',', '.' );

        $valor_base_aprovacao->setSize('100%');

        $departamento_unit_system_unit_name = new TEntry('departamento_unit_system_unit_name');
        $departamento_unit_system_unit_id = new THidden('departamento_unit_system_unit_id');
        $departamento_unit_system_unit_rua = new TEntry('departamento_unit_system_unit_rua');
        $departamento_unit_system_unit_numero = new TEntry('departamento_unit_system_unit_numero');
        $departamento_unit_system_unit_bairro = new TEntry('departamento_unit_system_unit_bairro');
        $departamento_unit_system_unit_cep = new TEntry('departamento_unit_system_unit_cep');
        $departamento_unit_system_unit_cidade_id = new TDBCombo('departamento_unit_system_unit_cidade_id', 'minierp', 'Cidade', 'id', '{estado->nome} - {nome}','nome asc' , $criteria_departamento_unit_system_unit_cidade_id );
        $departamento_unit_system_unit_email = new TEntry('departamento_unit_system_unit_email');
        $departamento_unit_system_unit_valor_empenho = new TNumeric('departamento_unit_system_unit_valor_empenho', '2', ',', '.' );
        $button_adicionar_departamento_unit_system_unit = new TButton('button_adicionar_departamento_unit_system_unit');

        $name->addValidation("Nome", new TRequiredValidator()); 
        $email->addValidation("Email", new TRequiredValidator()); 
        $entidade->addValidation("Entidade", new TRequiredValidator());

        $id->setEditable(false);
        // $button_buscar->setAction(new TAction([$this, 'onBuscaCNPJ']), "Buscar");
        $button_buscar_endereco->setAction(new TAction([$this, 'onBuscaEndereco']), "Buscar");
        $button_adicionar_departamento_unit_system_unit->setAction(new TAction([$this, 'onAddDetailDepartamentoUnitSystemUnit'],['static' => 1]), "Adicionar");
        $recaptcha->setValue('S');
        $utilizasinapi->setValue('S');
        $testar_valor_venal->setValue('1');
        $utiliza_temparia->setValue('1');
        $bloqueio_valor_temparia->setValue('1');
        $exige_dotacao_empenho_frotas->setValue('1');
        $aprovacao_por_item->setValue('1');
        $selecao_redes_aleatoria->setValue('1');
        $pedido_base->setValue('1');
        $testar_revisao->setValue('1');
        $enviar_email_auto_relatorio->setValue('1');
        $exibir_popup_plano_manutencao->setValue('1');
        $checklist_vistoria_veiculo->setValue('1');



        $recaptcha->setUseSwitch(true, 'blue');
        $utilizasinapi->setUseSwitch(true, 'blue');
        $testar_valor_venal->setUseSwitch(true, 'blue');
        $utiliza_temparia->setUseSwitch(true, 'blue');
        $bloqueio_valor_temparia->setUseSwitch(true, 'blue');
        $exige_dotacao_empenho_frotas->setUseSwitch(true, 'blue');
        $aprovacao_por_item->setUseSwitch(true, 'blue');
        $selecao_redes_aleatoria->setUseSwitch(true, 'blue');
        $pedido_base->setUseSwitch(true, 'blue');
        $testar_revisao->setUseSwitch(true, 'blue');
        $enviar_email_auto_relatorio->setUseSwitch(true, 'blue');
        $exibir_popup_plano_manutencao->setUseSwitch(true, 'blue'); 
        $checklist_vistoria_veiculo->setUseSwitch(true, 'blue'); 


        $recaptcha->setIndexValue("S");
        $utilizasinapi->setIndexValue("S");
        $testar_valor_venal->setIndexValue("1");
        $utiliza_temparia->setIndexValue("1");
        $bloqueio_valor_temparia->setIndexValue("1");
        $exige_dotacao_empenho_frotas->setIndexValue("1");
        $aprovacao_por_item->setIndexValue("1");
        $selecao_redes_aleatoria->setIndexValue("1");
        $testar_revisao->setIndexValue("1");
        $pedido_base->setIndexValue("1");
        $enviar_email_auto_relatorio->setIndexValue("1");
        $exibir_popup_plano_manutencao->setIndexValue("1");
        $checklist_vistoria_veiculo->setIndexValue("1");



        $recaptcha->setInactiveIndexValue("N");
        $utilizasinapi->setInactiveIndexValue("N");
        $testar_valor_venal->setInactiveIndexValue("2");
        $utiliza_temparia->setInactiveIndexValue("2");
        $bloqueio_valor_temparia->setInactiveIndexValue("2");
        $exige_dotacao_empenho_frotas->setInactiveIndexValue("2");
        $aprovacao_por_item->setInactiveIndexValue("2");
        $selecao_redes_aleatoria->setInactiveIndexValue("2");
        $pedido_base->setInactiveIndexValue("2");
        $testar_revisao->setInactiveIndexValue("2");
        $enviar_email_auto_relatorio->setInactiveIndexValue("2");
        $exibir_popup_plano_manutencao->setInactiveIndexValue("2");
        $checklist_vistoria_veiculo->setInactiveIndexValue("2");



        // $button_buscar->addStyleClass('btn-default');
        $button_adicionar_departamento_unit_system_unit->addStyleClass('btn-default');

        // $button_buscar->setImage('fas:search #000000');
        $button_buscar_endereco->setImage('fas:search #000000');
        $button_adicionar_departamento_unit_system_unit->setImage('fas:plus #2ecc71');

      //  $recaptcha->addItems(["S"=>"Sim","N"=>"Não"]);
     //   $utilizasinapi->addItems(["S"=>"Sim","N"=>"Não"]);

        $entidade->enableSearch();
        $cidade_id->enableSearch();
     //   $recaptcha->enableSearch();
     //   $utilizasinapi->enableSearch();
     //   $testar_valor_venal->enableSearch();

        $departamento_unit_system_unit_cidade_id->enableSearch();

        $cnpj->setMask('99.999.999/9999-99');
        $telefone01->setMask('(99)9999-9999');
        $telefone02->setMask('(99)9999-9999');
        $telefone03->setMask('(99)9999-9999');
        $departamento_unit_system_unit_cep->setMask('99999-999');

        $cep->setMaxLength(10);
        $departamento_unit_system_unit_cep->setMaxLength(10);
        $departamento_unit_system_unit_rua->setMaxLength(500);
        $departamento_unit_system_unit_numero->setMaxLength(20);
        $departamento_unit_system_unit_email->setMaxLength(500);
        $departamento_unit_system_unit_bairro->setMaxLength(500);

        $id->setSize(100);
        $cnpj->setSize('100%');
        $rua->setSize('100%');
        $name->setSize('100%');
        $email->setSize('100%');
        $numero->setSize('100%');
        $bairro->setSize('100%');
        $cidade_id->setSize('100%');
        $recaptcha->setSize('100%');
        $telefone01->setSize('100%');
        $telefone02->setSize('100%');
        $telefone03->setSize('100%');
        $complemento->setSize('100%');
        $utilizasinapi->setSize('100%');
        $testar_valor_venal->setSize('100%');
        $utiliza_temparia->setSize('100%');
        $bloqueio_valor_temparia->setSize('100%');
        $exige_dotacao_empenho_frotas->setSize('100%');
        $testar_revisao->setSize('100%');
        $pedido_base->setSize('100%');
        $entidade->setSize('100%');
        $longitude->setSize('100%');
        $latitude->setSize('100%');
        $garantia_dias->setSize(200);
        $garantia_km->setSize(200);
        $percentual_produto_similar->setSize(200);
        $exibir_popup_plano_manutencao->setSize('100%') ;
        $checklist_vistoria_veiculo->setSize('100%') ;

        $departamento_unit_system_unit_id->setSize(200);
        $departamento_unit_system_unit_cep->setSize('94%');
        $departamento_unit_system_unit_rua->setSize('100%');
        $departamento_unit_system_unit_name->setSize('100%');
        $departamento_unit_system_unit_email->setSize('100%');
        $departamento_unit_system_unit_numero->setSize('100%');
        $departamento_unit_system_unit_bairro->setSize('100%');
        $departamento_unit_system_unit_cidade_id->setSize('100%');
        $departamento_unit_system_unit_valor_empenho->setSize('100%');

        $button_adicionar_departamento_unit_system_unit->id = '668d4aab95ff1';

        $email->addValidation("Email", new TRequiredValidator()); 
    //    $departamento_unit_system_unit_name->addValidation("Nome da Unidade/Dep/Secretárias", new TRequiredValidator()); 
    //    $departamento_unit_system_unit_email->addValidation("Email da Unidade/Dep/Secretárias", new TRequiredValidator()); 
    //    $departamento_unit_system_unit_valor_empenho->addValidation("Valor do Emprenho Unidade/Dep/Secretárias", new TRequiredValidator()); 

        $tab_667728e5aee4d = new BootstrapFormBuilder('tab_667728e5aee4d');
        $this->tab_667728e5aee4d = $tab_667728e5aee4d;
        $tab_667728e5aee4d->setProperty('style', 'border:none; box-shadow:none;');

        $tab_667728e5aee4d->appendPage("Orgão e/ou Secretárias");

        $tab_667728e5aee4d->addFields([new THidden('current_tab_tab_667728e5aee4d')]);
        $tab_667728e5aee4d->setTabFunction("$('[name=current_tab_tab_667728e5aee4d]').val($(this).attr('data-current_page'));");
        $bhelper_68e174a9bb7b4 = new BHelper();
         $bhelper_68e174a9bb7b4->setSize('18');

        $bhelper_68e174a9bb7b4->enableHover();
        $bhelper_68e174a9bb7b4->setSide("left");
        $bhelper_68e174a9bb7b4->setIcon(new TImage("fas:question #fa931f"));
        $bhelper_68e174a9bb7b4->setTitle(" 80% (oitenta por cento)");
        $bhelper_68e174a9bb7b4->setContent("Os valores das peças similares na execução deste contrato, não poderão ultrapassar o valor correspondente a do valor da mesma peça classificada como genuína, constante das tabelas dos fabricantes de veículos.");

        $row1 = $tab_667728e5aee4d->addFields([new TLabel("Id:", null, '14px', null)],[$id], [new TLabel("Cnpj:", '#FF0000', '14px', null)],[$cnpj]);
        $row2 = $tab_667728e5aee4d->addFields([new TLabel("Razão Social:", '#FF0000', '14px', null)],[$name],[new TLabel("Email:", '#ff0000', '14px', null)],[$email]);
        $row02 = $tab_667728e5aee4d->addFields([new TLabel("Cep:", null, '14px', null)],[$cep, $button_buscar_endereco], [new TLabel("Entidade:", '#FF0000', null, '14px', null)], [$entidade]);
        $row3 = $tab_667728e5aee4d->addFields([new TLabel("Rua:", null, '14px', null)],[$rua],[new TLabel("Número:", null, '14px', null)],[$numero]);
        $row4 = $tab_667728e5aee4d->addFields([new TLabel("Bairro:", null, '14px', null)],[$bairro],[new TLabel("Cidade:", null, '14px', null)],[$cidade_id]);
        $row5 = $tab_667728e5aee4d->addFields([new TLabel("Complemento:", null, '14px', null)],[$complemento],[new TLabel("Telefone 01:", null, '14px', null)],[$telefone01]);
        $row6 = $tab_667728e5aee4d->addFields([new TLabel("Telefone 02:", null, '14px', null)],[$telefone02],[new TLabel("Telefone 03:", null, '14px', null)],[$telefone03]);
        $row08 = $tab_667728e5aee4d->addFields([new TLabel("Latitude:", null, '14px', null)], [$latitude], [new TLabel("Longitude:", null, '14px', null)], [$longitude,$button_google_maps]);
        $row60 = $tab_667728e5aee4d->addFields([new TFormSeparator("Parâmetros", '#333', '18', '#eee')]);
        $row7 = $tab_667728e5aee4d->addFields([new TLabel("Utiliza SINAPI?", null, '14px', null)],[$utilizasinapi],[new TLabel("Utiliza recaptCha?", null, '14px', null)],[$recaptcha]);
        $row71 = $tab_667728e5aee4d->addFields([new TLabel("Testar valor venal do veículo ?", null, '14px', null)],[$testar_valor_venal],[new TLabel("Aprovação por item?", null, '14px', null)],[$aprovacao_por_item]);
        $row72 = $tab_667728e5aee4d->addFields([new TLabel("Seleção de redes aleatória ?", null, '14px', null)],[$selecao_redes_aleatoria],[new TLabel("Testa se o pedido entrou em revisão ?", null, '14px', null)],[$testar_revisao]);
        $row73 = $tab_667728e5aee4d->addFields([new TLabel("Abre pedido c/ base no orçamento ?", null, '14px', null)],[$pedido_base],[new TLabel("Valor base aprovação pedido:", null, '14px', null)],[$valor_base_aprovacao]);
        $row74 = $tab_667728e5aee4d->addFields([new TLabel("Envia relatorios automaticamente no email ?", null, '14px', null)],[$enviar_email_auto_relatorio],[new TLabel("% da peça similiar:", null, '14px', null)],[$percentual_produto_similar,$bhelper_68e174a9bb7b4]);
        $row75 = $tab_667728e5aee4d->addFields([new TLabel("Garantia mínima em dias ?", null, '14px', null)],[$garantia_dias],[new TLabel("Garantia mínima em km ?:", null, '14px', null)],[$garantia_km]);
        $row76 = $tab_667728e5aee4d->addFields([new TLabel("Utiliza tabela tempária ?", null, '14px', null)],[$utiliza_temparia],[new TLabel("Bloqueio valor API SUIV/SINAPI/ORSE?", null, '14px', null)],[$bloqueio_valor_temparia]);
        $row77 = $tab_667728e5aee4d->addFields([new TLabel("Exibir popup plano manutenção ?", null, '14px', null)],[$exibir_popup_plano_manutencao],[new TLabel("Checklist vistoria veículo ?", null, '14px', null)],[$checklist_vistoria_veiculo]);
        
        $row76b = $tab_667728e5aee4d->addFields([new TLabel("Exige dotacao/empenho em frotas?", null, '14px', null)],[$exige_dotacao_empenho_frotas]);

                $tab_667728e5aee4d->appendPage("Unidades / Dep / Secretárias");

        $this->detailFormDepartamentoUnitSystemUnit = new BootstrapFormBuilder('detailFormDepartamentoUnitSystemUnit');
        $this->detailFormDepartamentoUnitSystemUnit->setProperty('style', 'border:none; box-shadow:none; width:100%;');

        $this->detailFormDepartamentoUnitSystemUnit->setProperty('class', 'form-horizontal builder-detail-form');

        $row8 = $this->detailFormDepartamentoUnitSystemUnit->addFields([new TLabel("Name:", null, '14px', null, '100%'),$departamento_unit_system_unit_name,$departamento_unit_system_unit_id],[new TLabel("Rua:", null, '14px', null, '100%'),$departamento_unit_system_unit_rua]);
        $row8->layout = ['col-sm-6','col-sm-6'];

        $row9 = $this->detailFormDepartamentoUnitSystemUnit->addFields([new TLabel("Número:", null, '14px', null, '100%'),$departamento_unit_system_unit_numero],[new TLabel("Bairro:", null, '14px', null, '100%'),$departamento_unit_system_unit_bairro]);
        $row9->layout = ['col-sm-6','col-sm-6'];

        $row10 = $this->detailFormDepartamentoUnitSystemUnit->addFields([new TLabel("Cep:", null, '14px', null, '100%'),$departamento_unit_system_unit_cep],[new TLabel("Cidade", null, '14px', null, '100%'),$departamento_unit_system_unit_cidade_id]);
        $row10->layout = ['col-sm-6','col-sm-6'];

        $row11 = $this->detailFormDepartamentoUnitSystemUnit->addFields([new TLabel("Email:", null, '14px', null, '100%'),$departamento_unit_system_unit_email],[new TLabel("Valor Empenho:", null, '14px', null, '100%'),$departamento_unit_system_unit_valor_empenho]);
        $row11->layout = ['col-sm-6','col-sm-6'];

        $row12 = $this->detailFormDepartamentoUnitSystemUnit->addFields([$button_adicionar_departamento_unit_system_unit]);
        $row12->layout = [' col-sm-12'];

        $row13 = $this->detailFormDepartamentoUnitSystemUnit->addFields([new THidden('departamento_unit_system_unit__row__id')]);
        $this->departamento_unit_system_unit_criteria = new TCriteria();

        $this->departamento_unit_system_unit_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->departamento_unit_system_unit_list->disableHtmlConversion();;
        $this->departamento_unit_system_unit_list->generateHiddenFields();
        $this->departamento_unit_system_unit_list->setId('departamento_unit_system_unit_list');

        $this->departamento_unit_system_unit_list->style = 'width:100%';
        $this->departamento_unit_system_unit_list->class .= ' table-bordered';

        $column_departamento_unit_system_unit_name = new TDataGridColumn('name', "Name", 'left');
        $column_departamento_unit_system_unit_rua = new TDataGridColumn('rua', "Rua", 'left');
        $column_departamento_unit_system_unit_cep = new TDataGridColumn('cep', "Cep", 'left');
        $column_departamento_unit_system_unit_bairro = new TDataGridColumn('bairro', "Bairro", 'left');
        $column_departamento_unit_system_unit_numero = new TDataGridColumn('numero', "Número", 'left');
        $column_departamento_unit_system_unit_cidade_nome = new TDataGridColumn('cidade->nome', "Cidade id", 'left');
        $column_departamento_unit_system_unit_email = new TDataGridColumn('email', "Email", 'left');
        $column_departamento_unit_system_unit_valor_empenho = new TDataGridColumn('valor_empenho', "Valor Empenho", 'right');

        $column_departamento_unit_system_unit__row__data = new TDataGridColumn('__row__data', '', 'center');
        $column_departamento_unit_system_unit__row__data->setVisibility(false);

        $action_onEditDetailDepartamentoUnit = new TDataGridAction(array('SystemUnitForm', 'onEditDetailDepartamentoUnit'));
        $action_onEditDetailDepartamentoUnit->setUseButton(false);
        $action_onEditDetailDepartamentoUnit->setButtonClass('btn btn-default btn-sm');
        $action_onEditDetailDepartamentoUnit->setLabel("Editar");
        $action_onEditDetailDepartamentoUnit->setImage('far:edit #478fca');
        $action_onEditDetailDepartamentoUnit->setFields(['__row__id', '__row__data']);

        $this->departamento_unit_system_unit_list->addAction($action_onEditDetailDepartamentoUnit);

        $action_onDeleteDetailDepartamentoUnit = new TDataGridAction(array('SystemUnitForm', 'onDeleteDetailDepartamentoUnit'));
        $action_onDeleteDetailDepartamentoUnit->setUseButton(false);
        $action_onDeleteDetailDepartamentoUnit->setButtonClass('btn btn-default btn-sm');
        $action_onDeleteDetailDepartamentoUnit->setLabel("Excluir");
        $action_onDeleteDetailDepartamentoUnit->setImage('fas:trash-alt #dd5a43');
        $action_onDeleteDetailDepartamentoUnit->setFields(['__row__id', '__row__data']);

        $this->departamento_unit_system_unit_list->addAction($action_onDeleteDetailDepartamentoUnit);

        $action_onSaldoEmpenhoDetailDepartamentoUnit = new TDataGridAction(array('SaldoDepartamentoList', 'onSetProject'));
        $action_onSaldoEmpenhoDetailDepartamentoUnit->setUseButton(false);
        $action_onSaldoEmpenhoDetailDepartamentoUnit->setButtonClass('btn btn-default btn-sm');
        $action_onSaldoEmpenhoDetailDepartamentoUnit->setLabel("Saldo Empenho");
        $action_onSaldoEmpenhoDetailDepartamentoUnit->setImage('fas:money-bill #009688');
        $action_onSaldoEmpenhoDetailDepartamentoUnit->setFields(['__row__id', '__row__data']);

        $this->departamento_unit_system_unit_list->addAction($action_onSaldoEmpenhoDetailDepartamentoUnit);

        $this->departamento_unit_system_unit_list->addColumn($column_departamento_unit_system_unit_name);
        $this->departamento_unit_system_unit_list->addColumn($column_departamento_unit_system_unit_rua);
        $this->departamento_unit_system_unit_list->addColumn($column_departamento_unit_system_unit_cep);
        $this->departamento_unit_system_unit_list->addColumn($column_departamento_unit_system_unit_bairro);
        $this->departamento_unit_system_unit_list->addColumn($column_departamento_unit_system_unit_numero);
        $this->departamento_unit_system_unit_list->addColumn($column_departamento_unit_system_unit_cidade_nome);
        $this->departamento_unit_system_unit_list->addColumn($column_departamento_unit_system_unit_email);
        $this->departamento_unit_system_unit_list->addColumn($column_departamento_unit_system_unit_valor_empenho);

        $column_departamento_unit_system_unit_valor_empenho->setTransformer(function($value)
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

        $this->departamento_unit_system_unit_list->addColumn($column_departamento_unit_system_unit__row__data);

        $this->departamento_unit_system_unit_list->createModel();
        $tableResponsiveDiv = new TElement('div');
        $tableResponsiveDiv->class = 'table-responsive';
        $tableResponsiveDiv->add($this->departamento_unit_system_unit_list);
        $this->detailFormDepartamentoUnitSystemUnit->addContent([$tableResponsiveDiv]);
        $row14 = $tab_667728e5aee4d->addFields([$this->detailFormDepartamentoUnitSystemUnit]);
        $row14->layout = [' col-sm-12'];

        $row15 = $this->form->addFields([$tab_667728e5aee4d]);
        $row15->layout = [' col-sm-12'];

        $ini  = AdiantiApplicationConfig::get();

        if (!empty($ini['general']['multi_database']) and $ini['general']['multi_database'] == '1')
        {
            $database = new TCombo('connection_name');
            $database->addItems( SystemDatabaseInformationService::getConnections() );
            $this->form->addFields( [new TLabel(_t('Database'))], [$database] );
            $database->setSize('70%');
        }

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'far fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onreload = $this->form->addAction("Voltar", new TAction(['SystemUnitList', 'onReload']), 'far:arrow-alt-circle-left #478fca');
        $this->btn_onreload = $btn_onreload;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
  //          $container->add(TBreadCrumb::create(["admin","Cadastro de orgão"]));
        }
        $container->add($this->form);

        parent::add($container);

    }
    
    public function onBuscaEndereco($param)
    {
        try {
            if (!empty($param['cep'])) {
                TTransaction::open(self::$database);

                $cep = $param['cep'];

                // 1) Dados atuais do formulário
                $formData = $this->form->getData(); // stdClass

                // 2) Carrega o Active Record (modo edição) OU instancia vazio
                if (!empty($formData->id)) {
                    $object = new SystemUnit($formData->id); // TRecord válido
                } else {
                    $object = new SystemUnit;                // novo registro
                }

                // 3) Preserva o que já foi digitado no form (modo edição)
                $object->fromArray((array) $formData);

                // 4) Busca dados do CEP/Geocoding
                $cepData = self::onBuscarCoordenadas($cep); // stdClass ou null

                // 5) Mescla conforme regras
                if ($cepData) {
                    // Campos que só preenche se estiverem vazios no form
                    $preencherSeVazio = ['cep','rua','bairro','estado_id','cidade_id'];
                    // Campos que sempre atualizam se vier valor do CEP
                    $sempreAtualizar  = ['latitude','longitude'];

                    self::mergeCepIntoForm($object, $cepData, $preencherSeVazio, $sempreAtualizar);
                }

                // 6) Carrega itens Master-Detail apenas se houver ID (registro existente)
                if (!empty($object->id)) {
                    $this->loadMasterDetailItems(
                        'DepartamentoUnit',
                        'system_unit_id',
                        'departamento_unit_system_unit',
                        $object,
                        $this->form,
                        $this->departamento_unit_system_unit_list,
                        $this->departamento_unit_system_unit_criteria,
                        function($masterObject, $detailObject, $objectItems) {
                            // callback opcional
                        }
                    );
                }

                // 7) Reflete os dados no formulário (sem perder o que já estava)
                $this->form->setData((object) $object->toArray()); // mantém estado na memória
                TForm::sendData(self::$formName, (object) $object->toArray());

                TTransaction::close(self::$database);
            }
        }
        catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Mescla dados do CEP no objeto Active Record do formulário.
     *
     * @param TRecord  $object           Ex.: SystemUnit (herda de TRecord)
     * @param stdClass $cepData          Dados retornados pelo onBuscarCoordenadas
     * @param array    $preencherSeVazio Campos que só serão preenchidos se estiverem vazios no form
     * @param array    $sempreAtualizar  Campos que sempre serão sobrescritos se vier valor do CEP
     */
    private static function mergeCepIntoForm($object, $cepData, array $preencherSeVazio = [], array $sempreAtualizar = [])
    {
        // Função auxiliar para verificar "vazio" de forma segura
        $isEmpty = function($v) {
            return $v === null || $v === '' || (is_numeric($v) && $v === 0 && $v !== '0');
        };

        // Converte stdClass do CEP em array para facilitar
        $cepArr = (array) $cepData;

        foreach ($cepArr as $campo => $valor) {
            if ($valor === null || $valor === '') {
                continue; // ignora valores vazios do CEP
            }

            // Se for campo de "sempre atualizar"
            if (in_array($campo, $sempreAtualizar, true)) {
                $object->$campo = $valor;
                continue;
            }

            // Se for campo que "preenche se vazio"
            if (in_array($campo, $preencherSeVazio, true)) {
                $atual = isset($object->$campo) ? $object->$campo : null;
                if ($isEmpty($atual)) {
                    $object->$campo = $valor;
                }
                continue;
            }

            // Demais campos: por padrão, não sobrescreve (poderia ajustar conforme sua regra)
            // Ex.: $object->$campo = $object->$campo ?? $valor;
        }
    }


     public static function onBuscarCoordenadas($cepRecebido)
    {
        try {
            $cep = preg_replace('/[^0-9]/', '', $cepRecebido);

            if (!$cep) {
                throw new Exception("Informe um CEP válido.");
            }

            // Buscar Endereço pelo ViaCEP
            $viacep_url = "https://viacep.com.br/ws/{$cep}/json/";
            $endereco_data = @file_get_contents($viacep_url);
            $endereco_data = json_decode($endereco_data, true);

            if (isset($endereco_data['erro'])) {
                throw new Exception("CEP não encontrado.");
            }

            $endereco = "{$endereco_data['logradouro']}, {$endereco_data['bairro']}, {$endereco_data['localidade']}, {$endereco_data['uf']}, Brasil";

            // Buscar Latitude e Longitude no OpenStreetMap
            $nominatim_url = "https://nominatim.openstreetmap.org/search?q=" . $cep . ',+' . urlencode($endereco_data['localidade']) . "&format=json&limit=1";
            $geo_data_raw = self::fetchDataWithCurl($nominatim_url);
            $geo_data = json_decode($geo_data_raw, true);

            if (empty($geo_data)) {
                throw new Exception("A API Nominatim retornou vazio.");
            }

            if (!isset($geo_data[0]['lat']) || !isset($geo_data[0]['lon'])) {
                throw new Exception("A resposta da API não contém latitude e longitude.");
            }

            $latitude = $geo_data[0]['lat'];
            $longitude = $geo_data[0]['lon'];

            // Monta o objeto para retorno
            $object = new stdClass();
            $object->latitude = $latitude;
            $object->longitude = $longitude;

            // Estado
            $estado = Estado::where('sigla', '=', $endereco_data['uf'])->first();
            if (!$estado) {
                $estado = new Estado();
                $estado->nome = $endereco_data['estado'] ?? '';
                $estado->sigla = $endereco_data['uf'];
                $estado->ibge = $endereco_data['ibge'] ?? null;
                $estado->store();
            }

            // Cidade
            $cidade = Cidade::where('nome', '=', $endereco_data['localidade'])
                            ->where('estado_id', '=', $estado->id)
                            ->first();
            if (!$cidade) {
                $cidade = new Cidade();
                $cidade->nome = $endereco_data['localidade'];
                $cidade->estado_id = $estado->id;
                $cidade->store();
            }

            // Preenche os campos
            $object->estado_id = $estado->id;
            $object->cidade_id = $cidade->id;
            $object->rua = $endereco_data['logradouro'] ?? '';
            $object->bairro = $endereco_data['bairro'] ?? '';
            $object->cep = $cep;

            // Retorna para quem chamou
            return $object;

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            return null;
        }
    }


    public static function fetchDataWithCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout de 10s
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Se a resposta estiver vazia ou erro de conexão, exibir mensagem
        if ($response === false || $http_status != 200) {
            return json_encode(["error" => "Falha ao acessar API. HTTP Status: $http_status"]);
        }

        return $response;
    }
    public  function onAddDetailDepartamentoUnitSystemUnit($param = null) 
    {
        try
        {
            $data = $this->form->getData();

            $__row__id = !empty($data->departamento_unit_system_unit__row__id) ? $data->departamento_unit_system_unit__row__id : 'b'.uniqid();

            TTransaction::open(self::$database);

            $grid_data = new DepartamentoUnit();
            $grid_data->__row__id = $__row__id;
            $grid_data->name = $data->departamento_unit_system_unit_name;
            $grid_data->id = $data->departamento_unit_system_unit_id;
            $grid_data->rua = $data->departamento_unit_system_unit_rua;
            $grid_data->numero = $data->departamento_unit_system_unit_numero;
            $grid_data->bairro = $data->departamento_unit_system_unit_bairro;
            $grid_data->cep = $data->departamento_unit_system_unit_cep;
            $grid_data->cidade_id = $data->departamento_unit_system_unit_cidade_id;
            $grid_data->email = $data->departamento_unit_system_unit_email;
            $grid_data->valor_empenho = $data->departamento_unit_system_unit_valor_empenho;

            $__row__data = array_merge($grid_data->toArray(), (array)$grid_data->getVirtualData());
            $__row__data['__row__id'] = $__row__id;
            $__row__data['__display__']['name'] =  $param['departamento_unit_system_unit_name'] ?? null;
            $__row__data['__display__']['id'] =  $param['departamento_unit_system_unit_id'] ?? null;
            $__row__data['__display__']['rua'] =  $param['departamento_unit_system_unit_rua'] ?? null;
            $__row__data['__display__']['numero'] =  $param['departamento_unit_system_unit_numero'] ?? null;
            $__row__data['__display__']['bairro'] =  $param['departamento_unit_system_unit_bairro'] ?? null;
            $__row__data['__display__']['cep'] =  $param['departamento_unit_system_unit_cep'] ?? null;
            $__row__data['__display__']['cidade_id'] =  $param['departamento_unit_system_unit_cidade_id'] ?? null;
            $__row__data['__display__']['email'] =  $param['departamento_unit_system_unit_email'] ?? null;
            $__row__data['__display__']['valor_empenho'] =  $param['departamento_unit_system_unit_valor_empenho'] ?? null;

            $grid_data->__row__data = base64_encode(serialize((object)$__row__data));
            $row = $this->departamento_unit_system_unit_list->addItem($grid_data);
            $row->id = $grid_data->__row__id;

            TDataGrid::replaceRowById('departamento_unit_system_unit_list', $grid_data->__row__id, $row);

            TTransaction::close();

            $lista = $this->departamento_unit_system_unit_list->getItems();

            $total = 0;

            foreach ($lista as $item) {
                if (!empty($item->valor_empenho) && !empty($item->tipotransacao)) {
                    $valor = (float) str_replace(['.', ','], ['', '.'], $item->valor_empenho);

                    if ($item->tipotransacao === 'C') {
                        $total += $valor;
                    } elseif ($item->tipotransacao === 'D') {
                        $total -= $valor;
                    }
                }
            }

            $data = new stdClass;
            $data->departamento_unit_system_unit_name = '';
            $data->departamento_unit_system_unit_id = '';
            $data->departamento_unit_system_unit_rua = '';
            $data->departamento_unit_system_unit_numero = '';
            $data->departamento_unit_system_unit_bairro = '';
            $data->departamento_unit_system_unit_cep = '';
            $data->departamento_unit_system_unit_cidade_id = '';
            $data->departamento_unit_system_unit_email = '';
            $data->departamento_unit_system_unit_valor_empenho = '';
            $data->departamento_unit_system_unit_valor_empenho = number_format($total, 2, ',', '.');
            $data->departamento_unit_system_unit__row__id = '';

            TForm::sendData(self::$formName, $data);
            TScript::create("
               var element = $('#668d4aab95ff1');
               if(typeof element.attr('add') != 'undefined')
               {
                   element.html(base64_decode(element.attr('add')));
               }
            ");

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }

    public static function onEditDetailDepartamentoUnit($param = null) 
    {
        try
        {

            $__row__data = unserialize(base64_decode($param['__row__data']));
            $__row__data->__display__ = is_array($__row__data->__display__) ? (object) $__row__data->__display__ : $__row__data->__display__;
            $fireEvents = true;
            $aggregate = false;

            $data = new stdClass;
            $data->departamento_unit_system_unit_name = $__row__data->__display__->name ?? null;
            $data->departamento_unit_system_unit_id = $__row__data->__display__->id ?? null;
            $data->departamento_unit_system_unit_rua = $__row__data->__display__->rua ?? null;
            $data->departamento_unit_system_unit_numero = $__row__data->__display__->numero ?? null;
            $data->departamento_unit_system_unit_bairro = $__row__data->__display__->bairro ?? null;
            $data->departamento_unit_system_unit_cep = $__row__data->__display__->cep ?? null;
            $data->departamento_unit_system_unit_cidade_id = $__row__data->__display__->cidade_id ?? null;
            $data->departamento_unit_system_unit_email = $__row__data->__display__->email ?? null;
            $data->departamento_unit_system_unit_valor_empenho = $__row__data->__display__->valor_empenho ?? null;
            $data->departamento_unit_system_unit__row__id = $__row__data->__row__id;

            TForm::sendData(self::$formName, $data, $aggregate, $fireEvents);
            TScript::create("
               var element = $('#668d4aab95ff1');
               if(!element.attr('add')){
                   element.attr('add', base64_encode(element.html()));
               }
               element.html(\"<span><i class='far fa-edit' style='color:#478fca;padding-right:4px;'></i>Editar</span>\");
               if(!element.attr('edit')){
                   element.attr('edit', base64_encode(element.html()));
               }
            ");

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }

    private function calcularTotalValorEmpenho()
    {
        $lista = $this->departamento_unit_system_unit_list->getItems();
        $total = 0;

        foreach ($lista as $item) {
            if (!empty($item->valor_empenho)) {
                $valor = (float) str_replace(['.', ','], ['', '.'], $item->valor_empenho);

                // Se tiver tipotransacao, use-a. Senão, apenas some tudo
                if (isset($item->tipotransacao)) {
                    if ($item->tipotransacao === 'C') {
                        $total += $valor;
                    } elseif ($item->tipotransacao === 'D') {
                        $total -= $valor;
                    }
                } else {
                    $total += $valor;
                }
            }
        }

        return number_format($total, 2, ',', '.');
    }


    public static function onDeleteDetailDepartamentoUnit($param = null) 
    {
        try
        { 
            TTransaction::open(self::$database); // open a transaction

            // $produto = new Produto($param['_field_value']);

            // TSession::setValue('idproduto', $param['_field_value']);

            // // Obtém o conteúdo JSON com os dados da linha
            // $conteudojson = $param['_field_data_json'];
            // $idlinha = json_decode($conteudojson);
            // if (isset($idlinha->{'row'})) {
            //     $idlinha = $idlinha->{'row'}; // número da linha (ex: 51627171)
            // }
            $__row__data = unserialize(base64_decode($param['__row__data']));
            $key = $__row__data->id; // aqui você pega o campo "id" da linha

            $relations = [
                    'PedidoFrotas' => ['column' => 'departamento_unit_id', 'alias' => 'Pedido Frotas'],
                    'Pedido' => ['column' => 'departamento_unit_id', 'alias' => 'Pedido'],
                    'SaldoDepartamento' => ['column' => 'departamento_unit_id', 'alias' => 'Dotação orçamentária - empenho'],
                    'SystemUserDepartamentoUnit' => ['column' => 'departamento_unit_id', 'alias' => 'Departamento de usuário'],
                    'Veiculos' => ['column' => 'departamento_unit_id', 'alias' => 'Veiculos']
                ];

                foreach ($relations as $model => $info)
                {
                    $repository = new TRepository($model);
                    $criteria = new TCriteria;
                    $criteria->add(new TFilter($info['column'], '=', $key));
                    $count = $repository->count($criteria);

                    if ($count > 0)
                    {
                        throw new Exception("Não é possível excluir. Existem registros relacionados em {$info['alias']}");
                    }
                }
            TTransaction::close(); // close the transaction 
            
            $__row__data = unserialize(base64_decode($param['__row__data']));

            $data = new stdClass;
            $data->departamento_unit_system_unit_name = '';
            $data->departamento_unit_system_unit_id = '';
            $data->departamento_unit_system_unit_rua = '';
            $data->departamento_unit_system_unit_numero = '';
            $data->departamento_unit_system_unit_bairro = '';
            $data->departamento_unit_system_unit_cep = '';
            $data->departamento_unit_system_unit_cidade_id = '';
            $data->departamento_unit_system_unit_email = '';
            $data->departamento_unit_system_unit_valor_empenho = '';
            $data->departamento_unit_system_unit__row__id = '';

            TForm::sendData(self::$formName, $data);

            TDataGrid::removeRowById('departamento_unit_system_unit_list', $__row__data->__row__id);
            TScript::create("
               var element = $('#668d4aab95ff1');
               if(typeof element.attr('add') != 'undefined')
               {
                   element.html(base64_decode(element.attr('add')));
               }
            ");

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

         

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new SystemUnit(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            TForm::sendData(self::$formName, (object)['id' => $object->id]);

          //  $grid_data->tipotransacao = $data->departamento_unit_system_unit_tipotransacao;
 
            $departamento_unit_system_unit_items = $this->storeMasterDetailItems('DepartamentoUnit', 'system_unit_id', 'departamento_unit_system_unit', $object, $param['departamento_unit_system_unit_list___row__data'] ?? [], $this->form, $this->departamento_unit_system_unit_list, function($masterObject, $detailObject){ 

                //code here

            }, $this->departamento_unit_system_unit_criteria); 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

           

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $messageAction);
                TApplication::loadPage('SystemUnitList', 'onShow');

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new SystemUnit($key); // instantiates the Active Record 

                $departamento_unit_system_unit_items = $this->loadMasterDetailItems('DepartamentoUnit', 'system_unit_id', 'departamento_unit_system_unit', $object, $this->form, $this->departamento_unit_system_unit_list, $this->departamento_unit_system_unit_criteria, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }); 

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function onShow($param = null)
    {

    } 

    public static function getFormName()
    {
        return self::$formName;
    }
     public  function onAcesso($param = null) 
    {
        try 
        {
            TTransaction::open(self::$database); // open a transaction

            $id = $param['id'];
            $object = new SystemUnit($id); // create an empty object //</blockLine>

            //code here
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data
            $lat=$object->latitude;
            $lng=$object->longitude;
            $rua = $object->rua;
            $numero = $object->numero;
            $bairro = $object->bairro;
            $cidade = $object->cidade->nome;
            $estado = $object->cidade->estado->sigla;
            $cep = $object->cep;
             if (!$lat || !$lng) {
                new TMessage('warning', 'Informe Latitude e Longitude.');
                return;
            }

            $endereco = "{$rua}, {$numero} - {$bairro}, {$cidade} - {$estado}, {$cep}, Brasil";

            $url = 'https://www.google.com/maps/search/?api=1&query=' 
                . rawurlencode($endereco);
            TForm::sendData(self::$formName, $data); // fill form data

            // abre em nova guia
            TScript::create("window.open('{$url}', '_blank');");
                        TTransaction::close(); // close the transaction

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

}

