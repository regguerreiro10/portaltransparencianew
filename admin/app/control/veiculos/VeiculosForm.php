<?php

//<fileHeader>

//</fileHeader>

use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\THidden;

class VeiculosForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Veiculos';
    private static $primaryKey = 'id';
    private static $formName = 'form_VeiculosForm';

    //<classProperties>

    //</classProperties>

    use Adianti\Base\AdiantiFileSaveTrait;
    use BuilderMasterDetailFieldListTrait;

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
        $this->form->setFormTitle("Cadastro de Veiculos, Aeronaves e/ou Equipamentos");

        $criteria_status_veiculo_id = new TCriteria();
        $criteria_system_unit_id = new TCriteria();
        $criteria_system_unit_id->add(new TFilter('id', '=', TSession::getValue('idunit')));
        $criteria_marca_id = new TCriteria();
        $criteria_tipo_combustivel_id = new TCriteria();
        $criteria_tipo_veiculo_id = new TCriteria();
        $criteria_corveiculo_id = new TCriteria();
        $criteria_propriedade_id = new TCriteria();
        $criteria_dispositivos_id = new TCriteria();
        $criteria_especie_id = new TCriteria();
        $criteria_classificacao_id = new TCriteria();
        $criteria_familia_id = new TCriteria();
        $criteria_responsavel_id = new TCriteria();
        
        $filterVar = "5";
        $criteria_responsavel_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '{$filterVar}')")); 
        //<onBeginPageCreation>

        //</onBeginPageCreation>

        $id = new TEntry('id');
        $responsavel_id = new TDBCombo('responsavel_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_responsavel_id );

        $status_veiculo_id = new TDBCombo('status_veiculo_id', 'minierp', 'StatusVeiculo', 'id', '{nome}','nome asc' , $criteria_status_veiculo_id );
        $system_unit_id = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_system_unit_id );
        $departamento_unit_id = new TCombo('departamento_unit_id');
        $prefixo = new TEntry('prefixo');
        $placa = new TEntry('placa');
        $chassi = new TEntry('chassi');
        $renavam = new TEntry('renavam');
        $ciclos = new TEntry('ciclos');
        $codigo_patrimonio = new TEntry('codigo_patrimonio');
        $numero_dispositivo = new TEntry('numero_dispositivo');
        $marca_id = new TDBCombo('marca_id', 'minierp', 'Marca', 'id', '{descricao}','descricao asc' , $criteria_marca_id );
        $marca_id->configureNoResultsQuickRegister(new TAction(['MarcaForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $marca_id->setNoResultsMessage("Nenhuma marca encontrada. Clique no cadastrar");

        $especie_id = new TDBCombo('especie_id', 'minierp', 'Especie', 'id', '{descricao}','descricao asc' , $criteria_especie_id );
        $especie_id->configureNoResultsQuickRegister(new TAction(['EspecieForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $especie_id->setNoResultsMessage("Nenhuma especie encontrada. Clique no cadastrar");

        $familia_id = new TDBCombo('familia_id', 'minierp', 'Familia', 'id', '{descricao}','descricao asc' , $criteria_familia_id );
        $familia_id->configureNoResultsQuickRegister(new TAction(['FamiliaForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $familia_id->setNoResultsMessage("Nenhuma especie encontrada. Clique no cadastrar");
        

        $modelo_id = new TCombo('modelo_id');
        $btnModeloEditar = new TButton('btnModeloEditar');
        $btnModelo = new TButton('btnModelo');
        // $importar_marca_modelo = new TButton('importar_marca_modelo');
        // $importar_marca_modelo->setAction(new TAction([$this, 'onImportarMarcasModelos']), "Importar Marca e Modelo");
        // $importar_marca_modelo->addStyleClass('btn-default');
        // $importar_marca_modelo->setImage('fas:file-import #000000');
        $anosencontrados = new TEntry('anosencontrados');
        $anosencontrados->setProperty('style', 'padding: 0px; height:25px; background-color: white !important; color: black; border: 1px solid white !important; color: black;');
        $anosencontrados->setEditable(false);
        $anosencontrados->setSize('100%');

         $anof = new TEntry('anof');
        $anom = new TEntry('anom');
        $tipo_combustivel_id = new TDBCombo('tipo_combustivel_id', 'minierp', 'TipoCombustivel', 'id', '{descricao}','descricao asc' , $criteria_tipo_combustivel_id );
        $tipo_combustivel_id->configureNoResultsQuickRegister(new TAction(['TipoCombustivelForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $tipo_combustivel_id->setNoResultsMessage("Nenhum tipo de combustivel encontrado. Clique no cadastrar");
        $capacidade_tanque = new TEntry('capacidade_tanque');
        $tipo_veiculo_id = new TDBCombo('tipo_veiculo_id', 'minierp', 'TipoVeiculo', 'id', '{descricao}','descricao asc' , $criteria_tipo_veiculo_id );
        $tipo_veiculo_id->configureNoResultsQuickRegister(new TAction(['TipoVeiculoForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $tipo_veiculo_id->setNoResultsMessage("Nenhum tipo de veiculo encontrado. Clique no cadastrar");
        $hodometroatual = new TEntry('hodometroatual');
        $corveiculo_id = new TDBCombo('corveiculo_id', 'minierp', 'Corveiculo', 'id', '{descricao}','descricao asc' , $criteria_corveiculo_id );
        $corveiculo_id->configureNoResultsQuickRegister(new TAction(['CorveiculoForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $corveiculo_id->setNoResultsMessage("Nenhuma cor do veiculo encontrada. Clique no cadastrar");
        $saldo_veiculo = new TNumeric('saldo_veiculo', '2', ',', '.' );
        $valor_tabela_fipe = new TNumeric('valor_tabela_fipe', '2', ',', '.' );

        $propriedade_id = new TDBCombo('propriedade_id', 'minierp', 'Propriedade', 'id', '{descricao}','descricao asc' , $criteria_propriedade_id );
        $propriedade_id->configureNoResultsQuickRegister(new TAction(['PropriedadeForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $propriedade_id->setNoResultsMessage("Nenhuma propriedade encontrada. Clique no cadastrar");
       
        $identificacao = new TCombo('identificacao');
        $dispositivos_id = new TDBCombo('dispositivos_id', 'minierp', 'Dispositivos', 'id', '{descricao}','descricao asc' , $criteria_dispositivos_id );

        $anexos_veiculo_veiculos_id = new THidden('anexos_veiculo_veiculos_id[]');
        $anexos_veiculo_veiculos___row__id = new THidden('anexos_veiculo_veiculos___row__id[]');
        $anexos_veiculo_veiculos___row__data = new THidden('anexos_veiculo_veiculos___row__data[]');
        $anexos_veiculo_veiculos_descricao = new TFile('anexos_veiculo_veiculos_descricao[]');
        $this->fieldList_672d41ca04112 = new TFieldList();
        $saldo_veiculo_veiculos_id = new THidden('saldo_veiculo_veiculos_id[]');
        $saldo_veiculo_veiculos___row__id = new THidden('saldo_veiculo_veiculos___row__id[]');
        $saldo_veiculo_veiculos___row__data = new THidden('saldo_veiculo_veiculos___row__data[]');
        $saldo_veiculo_veiculos_tipo_transacao = new TCombo('saldo_veiculo_veiculos_tipo_transacao[]');
      //  $saldo_veiculo_veiculos_system_users_id = new TEntry('saldo_veiculo_veiculos_system_users_id[]');
        $saldo_veiculo_veiculos_motivo_transacao = new TEntry('saldo_veiculo_veiculos_motivo_transacao[]');
        $saldo_veiculo_veiculos_data_transacao = new TDate('saldo_veiculo_veiculos_data_transacao[]');
        $saldo_veiculo_veiculos_valor_transacao = new TNumeric('saldo_veiculo_veiculos_valor_transacao[]', '2', ',', '.' );
        $saldo_veiculo_veiculos_saldo_disponivel = new TNumeric('saldo_veiculo_veiculos_saldo_disponivel[]', '2', ',', '.' );
        $saldo_veiculo_veiculos_mes_transacao = new TEntry('saldo_veiculo_veiculos_mes_transacao[]');
        $saldo_veiculo_veiculos_ano_transacao = new TEntry('saldo_veiculo_veiculos_ano_transacao[]');
        $this->fieldList_672d41ef04116 = new TFieldList();

        $fotos_veiculos_veiculos_id = new THidden('fotos_veiculos_veiculos_id[]');
        $fotos_veiculos_veiculos___row__id = new THidden('fotos_veiculos_veiculos___row__id[]');
        $fotos_veiculos_veiculos___row__data = new THidden('fotos_veiculos_veiculos___row__data[]');
        $fotos_veiculos_veiculos_caminho = new TFile('fotos_veiculos_veiculos_caminho[]');
        $this->fieldList_67cafdd5cae14 = new TFieldList();

        $this->fieldList_672d41ca04112->addField(null, $anexos_veiculo_veiculos_id, []);
        $this->fieldList_672d41ca04112->addField(null, $anexos_veiculo_veiculos___row__id, ['uniqid' => true]);
        $this->fieldList_672d41ca04112->addField(null, $anexos_veiculo_veiculos___row__data, []);
        $this->fieldList_672d41ca04112->addField(new TLabel("Descricao", null, '14px', null), $anexos_veiculo_veiculos_descricao, ['width' => '100%']);

        $this->fieldList_672d41ca04112->width = '100%';
        $this->fieldList_672d41ca04112->setFieldPrefix('anexos_veiculo_veiculos');
        $this->fieldList_672d41ca04112->name = 'fieldList_672d41ca04112';

        $this->criteria_fieldList_672d41ca04112 = new TCriteria();
        $this->default_item_fieldList_672d41ca04112 = new stdClass();

        $this->form->addField($anexos_veiculo_veiculos_id);
        $this->form->addField($anexos_veiculo_veiculos___row__id);
        $this->form->addField($anexos_veiculo_veiculos___row__data);
        $this->form->addField($anexos_veiculo_veiculos_descricao);

        $this->fieldList_672d41ca04112->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $this->fieldList_672d41ef04116->addField(null, $saldo_veiculo_veiculos_id, []);
        $this->fieldList_672d41ef04116->addField(null, $saldo_veiculo_veiculos___row__id, ['uniqid' => true]);
        $this->fieldList_672d41ef04116->addField(null, $saldo_veiculo_veiculos___row__data, []);
        $this->fieldList_672d41ef04116->addField(new TLabel("Tipo transacao", '#FF0000', '14px', null), $saldo_veiculo_veiculos_tipo_transacao, ['width' => '20%']);
        //$this->fieldList_672d41ef04116->addField(new TLabel("System users id", null, '14px', null), $saldo_veiculo_veiculos_system_users_id, ['width' => '20%']);
        $this->fieldList_672d41ef04116->addField(new TLabel("Motivo transacao", '#FF0000', '14px', null), $saldo_veiculo_veiculos_motivo_transacao, ['width' => '20%']);
        $this->fieldList_672d41ef04116->addField(new TLabel("Data transacao", '#FF0000', '14px', null), $saldo_veiculo_veiculos_data_transacao, ['width' => '20%']);
        $this->fieldList_672d41ef04116->addField(new TLabel("Valor transacao", '#FF0000', '14px', null), $saldo_veiculo_veiculos_valor_transacao, ['width' => '20%']);
        $this->fieldList_672d41ef04116->addField(new TLabel("Saldo disponivel", null, '14px', null), $saldo_veiculo_veiculos_saldo_disponivel, ['width' => '20%']);
        $this->fieldList_672d41ef04116->addField(new TLabel("Mes transacao", null, '14px', null), $saldo_veiculo_veiculos_mes_transacao, ['width' => '10%']);
        $this->fieldList_672d41ef04116->addField(new TLabel("Ano transacao", null, '14px', null), $saldo_veiculo_veiculos_ano_transacao, ['width' => '10%']);

        $this->fieldList_672d41ef04116->width = '100%';
        $this->fieldList_672d41ef04116->setFieldPrefix('saldo_veiculo_veiculos');
        $this->fieldList_672d41ef04116->name = 'fieldList_672d41ef04116';

        
        $this->criteria_fieldList_672d41ef04116 = new TCriteria();
        $this->default_item_fieldList_672d41ef04116 = new stdClass();

        $this->form->addField($saldo_veiculo_veiculos_id);
        $this->form->addField($saldo_veiculo_veiculos___row__id);
        $this->form->addField($saldo_veiculo_veiculos___row__data);
        $this->form->addField($saldo_veiculo_veiculos_tipo_transacao);
      //  $this->form->addField($saldo_veiculo_veiculos_system_users_id);
        $this->form->addField($saldo_veiculo_veiculos_motivo_transacao);
        $this->form->addField($saldo_veiculo_veiculos_data_transacao);
        $this->form->addField($saldo_veiculo_veiculos_valor_transacao);
        $this->form->addField($saldo_veiculo_veiculos_saldo_disponivel);
        $this->form->addField($saldo_veiculo_veiculos_mes_transacao);
        $this->form->addField($saldo_veiculo_veiculos_ano_transacao);

        $this->fieldList_672d41ef04116->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $this->fieldList_67cafdd5cae14->addField(null, $fotos_veiculos_veiculos_id, []);
        $this->fieldList_67cafdd5cae14->addField(null, $fotos_veiculos_veiculos___row__id, ['uniqid' => true]);
        $this->fieldList_67cafdd5cae14->addField(null, $fotos_veiculos_veiculos___row__data, []);
        $this->fieldList_67cafdd5cae14->addField(new TLabel("Caminho", null, '14px', null), $fotos_veiculos_veiculos_caminho, ['width' => '100%']);

        $this->fieldList_67cafdd5cae14->width = '100%';
        $this->fieldList_67cafdd5cae14->setFieldPrefix('fotos_veiculos_veiculos');
        $this->fieldList_67cafdd5cae14->name = 'fieldList_67cafdd5cae14';

        $this->criteria_fieldList_67cafdd5cae14 = new TCriteria();
        $this->default_item_fieldList_67cafdd5cae14 = new stdClass();

        $this->form->addField($fotos_veiculos_veiculos_id);
        $this->form->addField($fotos_veiculos_veiculos___row__id);
        $this->form->addField($fotos_veiculos_veiculos___row__data);
        $this->form->addField($fotos_veiculos_veiculos_caminho);

        $this->fieldList_67cafdd5cae14->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $system_unit_id->setChangeAction(new TAction([$this,'onChangesystem_unit_id']));
                $modelo_id->setChangeAction(new TAction([$this,'onChangemodelo_id']));

        $marca_id->setChangeAction(new TAction([$this,'onChangemarca_id']));
        $anof->setExitAction(new TAction([__CLASS__, 'onChangeanof']));
        $status_veiculo_id->addValidation("Status", new TRequiredValidator()); 
        $system_unit_id->addValidation("Unidade", new TRequiredValidator()); 
        $departamento_unit_id->addValidation("Sub Unidade", new TRequiredValidator()); 
        $placa->addValidation("Placa", new TRequiredValidator()); 
        $marca_id->addValidation("Marca", new TRequiredValidator()); 
        $modelo_id->addValidation("Modelo", new TRequiredValidator()); 
        $tipo_combustivel_id->addValidation("Tipo de Combustível", new TRequiredValidator()); 
        $tipo_veiculo_id->addValidation("Tipo de Veículo", new TRequiredValidator()); 
        $corveiculo_id->addValidation("Cor", new TRequiredValidator()); 
        $especie_id->addValidation("Espécie", new TRequiredValidator()); 
        $familia_id->addValidation("Familia", new TRequiredValidator()); 
        $propriedade_id->addValidation("Propriedade/Classificação", new TRequiredValidator()); 
        $anof->addValidation("Ano de fabricação", new TRequiredValidator()); 

        $saldo_veiculo_veiculos_tipo_transacao->addValidation("Tipo de transação do saldo", new TRequiredValidator()); 
        $saldo_veiculo_veiculos_motivo_transacao->addValidation("Motivo da transação do saldo", new TRequiredValidator()); 
        $saldo_veiculo_veiculos_data_transacao->addValidation("Data da transação do saldo", new TRequiredValidator()); 
        $saldo_veiculo_veiculos_valor_transacao->addValidation("Valor do saldo do Veiculos, Aeronaves e/ou Equipamentos", new TRequiredValidator()); 

        
        
        $responsavel_id->enableSearch();
       // $anof->enableSearch();

        $id->setEditable(false);
        $especie_id->setEditable(false);
        $valor_tabela_fipe->setEditable(false);
        $familia_id->setEditable(false);
        $propriedade_id->setEditable(false);
        $tipo_veiculo_id->setEditable(false);
        $tipo_combustivel_id->setEditable(false);
        $btnModelo->setAction(new TAction(['ModeloForm', 'onSetProject']), "");
        $btnModelo->addStyleClass('btn-default');
        $btnModelo->setImage('fas:plus #69AA46');

        $btnModeloEditar->setAction(new TAction(['ModeloForm', 'onEdit'],""));
        $btnModeloEditar->addStyleClass('btn-default');
        $btnModeloEditar->setImage('far:edit #478fca');

       
        // $placa->setMask('AAA-9A99');
        $anexos_veiculo_veiculos_descricao->enableFileHandling();
        $saldo_veiculo_veiculos_data_transacao->setMask('dd/mm/yyyy');
        $saldo_veiculo_veiculos_data_transacao->setDatabaseMask('yyyy-mm-dd');
        $saldo_veiculo_veiculos_tipo_transacao->addItems(["C"=>"Crédito","D"=>"Débito"]);
        $identificacao->addItems(["adesivado"=>"Adesivado","naoadesivado"=>"Não Adesivado"]);
        $fotos_veiculos_veiculos_caminho->enableFileHandling();
        $anexos_veiculo_veiculos_descricao->enableFileHandling();

        $fotos_veiculos_veiculos_caminho->enableImageGallery('100%', 200);
   //     $anexos_veiculo_veiculos_descricao->enableImageGallery('100%', 200);

        $placa->setMaxLength(100);
        $chassi->setMaxLength(100);
        $prefixo->setMaxLength(100);
        $renavam->setMaxLength(100);
        $responsavel_id->setSize('100%');
        $ciclos->setSize('100%');
        $codigo_patrimonio->setSize('100%');
        $numero_dispositivo->setSize('100%');

        // $anof->setValue('NULL');
        // $anom->setValue('NULL');
        // $placa->setValue('NULL');
        // $chassi->setValue('NULL');
        // $prefixo->setValue('NULL');
        // $renavam->setValue('NULL');
        $saldo_veiculo->setValue('0');
        $identificacao->setValue('NULL');
        $hodometroatual->setValue('0');
        $capacidade_tanque->setValue('0');
        $valor_tabela_fipe->setValue('0');

        $marca_id->enableSearch();
        $especie_id->enableSearch();
        $familia_id->enableSearch();
        $modelo_id->enableSearch();
        $corveiculo_id->enableSearch();
        $identificacao->enableSearch();
        $system_unit_id->enableSearch();
        $propriedade_id->enableSearch();
        $tipo_veiculo_id->enableSearch();
        $status_veiculo_id->enableSearch();
        $tipo_combustivel_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $saldo_veiculo_veiculos_tipo_transacao->enableSearch();
        $dispositivos_id->enableSearch();

        $id->setSize(100);
        $anof->setSize('100%');
        $anom->setSize('100%');
        $placa->setSize('100%');
        $chassi->setSize('100%');
        $prefixo->setSize('100%');
        $renavam->setSize('100%');
        $marca_id->setSize('100%');
        $modelo_id->setSize('70%');

        $corveiculo_id->setSize('100%');
        $especie_id->setSize('100%');
        $familia_id->setSize('100%');
        $saldo_veiculo->setSize('100%');
        $identificacao->setSize('100%');
        $system_unit_id->setSize('100%');
        $hodometroatual->setSize('100%');
        $propriedade_id->setSize('100%');
        $tipo_veiculo_id->setSize('100%');
        $status_veiculo_id->setSize('100%');
        $capacidade_tanque->setSize('100%');
        $valor_tabela_fipe->setSize('100%');
        $tipo_combustivel_id->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $dispositivos_id->setSize('100%');
        $anexos_veiculo_veiculos_descricao->setSize('100%');
        $saldo_veiculo_veiculos_data_transacao->setSize(150);
        $saldo_veiculo_veiculos_tipo_transacao->setSize('100%');
      //  $saldo_veiculo_veiculos_system_users_id->setSize('100%');
        $saldo_veiculo_veiculos_valor_transacao->setSize('100%');
        $saldo_veiculo_veiculos_saldo_disponivel->setSize('100%');
        $saldo_veiculo_veiculos_mes_transacao->setSize('100%');
        $saldo_veiculo_veiculos_ano_transacao->setSize('100%');
        $saldo_veiculo_veiculos_motivo_transacao->setSize('100%');
        $fotos_veiculos_veiculos_caminho->setSize('100%');

        //<onBeforeAddFieldsToForm>
        
        //</onBeforeAddFieldsToForm>

        $tab_672d414104108 = new BootstrapFormBuilder('tab_672d414104108');
        $this->tab_672d414104108 = $tab_672d414104108;
        $tab_672d414104108->setProperty('style', 'border:none; box-shadow:none;');

        $tab_672d414104108->appendPage("Dados");

        $tab_672d414104108->addFields([new THidden('current_tab_tab_672d414104108')]);
        $tab_672d414104108->setTabFunction("$('[name=current_tab_tab_672d414104108]').val($(this).attr('data-current_page'));");

        $row1 = $tab_672d414104108->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Status *", '#FF0000', null, '14px', null, '100%'),$status_veiculo_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $tab_672d414104108->addFields([new TLabel("Unidade *",'#FF0000', null, '14px', null, '100%'),$system_unit_id],[new TLabel("Sub Unidade *",'#FF0000', null, '14px', null, '100%'),$departamento_unit_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $tab_672d414104108->addFields([new TLabel("Prefixo:", null, '14px', null, '100%'),$prefixo],[new TLabel("Placa *", '#FF0000',null, '14px', null, '100%'),$placa]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $tab_672d414104108->addFields([new TLabel("Chassi:", null, '14px', null, '100%'),$chassi],[new TLabel("Renavam:", null, '14px', null, '100%'),$renavam]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $tab_672d414104108->addFields([new TLabel("Marca *", '#FF0000',null, '14px', null, '100%'),$marca_id],[new TLabel("Modelo *", '#FF0000',null, '14px', null, '100%'),$modelo_id, $btnModeloEditar, $btnModelo]);
        $row5->layout = ['col-sm-6','col-sm-6'];

        $row6 = $tab_672d414104108->addFields([new TLabel("Ano Fabricação:*{$anosencontrados}", '#FF0000', '14px', null, '100%'),$anof],[new TLabel("Ano Modelo:", null, '14px', null, '100%'),$anom]);
        $row6->layout = ['col-sm-6','col-sm-6'];
 
        $row51 = $tab_672d414104108->addFields([new TLabel("Especie *", '#FF0000',null, '14px', null, '100%'),$especie_id],[new TLabel("Familia *", '#FF0000',null, '14px', null, '100%'),$familia_id]);
        $row51->layout = ['col-sm-6','col-sm-6'];

        $row51 = $tab_672d414104108->addFields([new TLabel("Propriedade/Classificação *", '#FF0000',null, '14px', null, '100%'),$propriedade_id],[new TLabel("Tipo: *", '#FF0000',null, '14px', null, '100%'),$tipo_veiculo_id]);
        $row51->layout = ['col-sm-6','col-sm-6'];


        $row7 = $tab_672d414104108->addFields([new TLabel("Tipo combustível *", '#FF0000',null,  '14px', null, '100%'),$tipo_combustivel_id],[new TLabel("Valor tabela fipe:", null, '14px', null, '100%'),$valor_tabela_fipe]);
        $row7->layout = ['col-sm-6','col-sm-6'];

        if (TSession::getValue('tipofrota')==2) {
            $row9 = $tab_672d414104108->addFields([new TLabel("Horimetro atual *", '#FF0000',null, '14px', null, '100%'),$hodometroatual],[new TLabel("Ciclos:", null,null, '14px', null, '100%'),$ciclos], [new TLabel("Cor *", '#FF0000',null,  '14px', null, '100%'),$corveiculo_id]);
            $row9->layout = ['col-sm-3','col-sm-3','col-sm-6'];
        } else {
            $row9 = $tab_672d414104108->addFields([new TLabel("KM/Hodometro atual *", '#FF0000',null, '14px', null, '100%'),$hodometroatual],[new TLabel("Cor *", '#FF0000',null,  '14px', null, '100%'),$corveiculo_id]);
            $row9->layout = ['col-sm-6','col-sm-6'];
        }

        $row11 = $tab_672d414104108->addFields([new TLabel("Capacidade tanque:", null, '14px', null, '100%'),$capacidade_tanque],[new TLabel("Identificação:", null, '14px', null, '100%'),$identificacao]);
        $row11->layout = ['col-sm-6','col-sm-6'];

        $row111 = $tab_672d414104108->addFields([new TLabel("Dispositivo *",'#FF0000', null,  '14px', null, '100%'),$dispositivos_id],[new TLabel("Responsável *", '#FF0000', '14px', null, '100%'),$responsavel_id]);
        $row111->layout = ['col-sm-6','col-sm-6'];

        $row112 = $tab_672d414104108->addFields([new TLabel("Número do Dispositivo *",'#FF0000', null,  '14px', null, '100%'),$numero_dispositivo],[new TLabel("Código Patrimonio:", null, '14px', null, '100%'),$codigo_patrimonio]);
        $row112->layout = ['col-sm-6','col-sm-6'];

        $tab_672d414104108->appendPage("Anexos");
        $row12 = $tab_672d414104108->addFields([$this->fieldList_672d41ca04112]);
        $row12->layout = [' col-sm-12'];

        $tab_672d414104108->appendPage("Saldo");
        $row13 = $tab_672d414104108->addFields([$this->fieldList_672d41ef04116]);
        $row13->layout = [' col-sm-12'];


        $tab_672d414104108->appendPage("Fotos");
        $row14 = $tab_672d414104108->addFields([$this->fieldList_67cafdd5cae14]);
        $row14->layout = [' col-sm-12'];


        $row14 = $this->form->addFields([$tab_672d414104108]);
        $row14->layout = [' col-sm-12'];

        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['VeiculosList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        //<onAfterPageCreation>

        //</onAfterPageCreation>

        parent::add($this->form);
        $style = new TStyle('right-panel > .container-part[page-name=VeiculosForm]');
        $style->width = '80% !important';   
        $style->show(true);


    }

    public static function onChangesystem_unit_id($param)
    {
        try
        {

            if (isset($param['system_unit_id']) && $param['system_unit_id'])
            { 
                $criteria = TCriteria::create(['system_unit_id' => $param['system_unit_id']]);
                TDBCombo::reloadFromModel(self::$formName, 'departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}', 'name asc', $criteria, TRUE); 
            } 
            else 
            { 
                TCombo::clearField(self::$formName, 'departamento_unit_id'); 
            }  

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    } 

//<generated-FormAction-onSave>
    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Veiculos(); // create an empty object //</blockLine>

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            if($object->placa && !$data->id)
            {
                if(Veiculos::where('placa', '=', $object->placa)->first())
                {
                    throw new Exception('Placa informada já existe!');
                }
            }

            $abastecimento = TSession::getValue('abastecimento'); 
            
            if($abastecimento == 1){
                if(empty($object->responsavel_id)){
                    throw new Exception("Obrigatório informar o responsável do veículo.");
                }
            }
            

            // Validação da combinação marca, modelo e ano
            if (!$object->id) {
                $veiculoExistente = Veiculos::where('marca_id', '=', $object->marca_id)
                                            ->where('modelo_id', '=', $object->modelo_id)
                                            ->where('anof', '=', $object->ano_fabricacao)
                                            ->where('placa', '=', $object->placa)
                                            ->first();

                if ($veiculoExistente) {
                    throw new Exception('Já existe um Veiculos, Aeronaves e/ou Equipamentos cadastrado com esta placa, marca, modelo e ano.');
                }
            }
          
            //</beforeStoreAutoCode> //</blockLine> 
//<generatedAutoCode>

            $anexos_veiculo_veiculos_descricao_dir = 'app/documentos';
            $fotos_veiculos_veiculos_caminho_dir = 'app/fotos/veiculos';
//</generatedAutoCode> 
//</generatedAutoCode> 
            $object->system_users_id = TSession::getValue('userid');
            $object->store(); // save the object //</blockLine>

            //</afterStoreAutoCode> //</blockLine>
 //<generatedAutoCode>

            $this->fireEvents($object);

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

//</generatedAutoCode>
  
            $fotos_veiculos_veiculos_items = $this->storeItems('FotosVeiculos', 'veiculos_id', $object, $this->fieldList_67cafdd5cae14, function($masterObject, $detailObject){ //</blockLine>

                //code here

                //</autoCode>
            }, $this->criteria_fieldList_67cafdd5cae14); //</blockLine>

            if(!empty($fotos_veiculos_veiculos_items))
            {
                foreach ($fotos_veiculos_veiculos_items as $item)
                {
                    $dataFile = new stdClass();
                    $dataFile->caminho = $item->caminho;
                    $this->saveFile($item, $dataFile, 'caminho', $fotos_veiculos_veiculos_caminho_dir);
                }
            }
    //<fieldList-2262095-18290682> //</hideLine>
            $saldo_veiculo_veiculos_items = $this->storeItems('SaldoVeiculo', 'veiculos_id', $object, $this->fieldList_672d41ef04116, function($masterObject, $detailObject){ //</blockLine>
                $detailObject->system_users_id = TSession::getValue('iduser');
                if (!empty($detailObject->data_transacao))
                {
                    $dataTransacao = new DateTime($detailObject->data_transacao);
                    $detailObject->mes_transacao = $detailObject->mes_transacao ?: $dataTransacao->format('m');
                    $detailObject->ano_transacao = $detailObject->ano_transacao ?: $dataTransacao->format('Y');
                }
                //code here

                //</autoCode>
            }, $this->criteria_fieldList_672d41ef04116); //</blockLine>
    //</hideLine> //</fieldList-2262095-18290682>
            if(!$data->id)
            {
            } else {
                if(Veiculos::where('id', '=', $object->id)->first())
                {
                    
                } else {
                    $saldo_veiculo = new SaldoVeiculo();
                    $saldo_veiculo->tipo_transacao='C';
                    $saldo_veiculo->motivo_transacao='Inserção de saldo';
                    $saldo_veiculo->data_transacao= date('Y-m-d');
                    $saldo_veiculo->valor_transacao=$object->saldo_veiculo;
                    $saldo_veiculo->saldo_disponivel=$object->saldo_veiculo;
                    $saldo_veiculo->mes_transacao=date('m');
                    $saldo_veiculo->ano_transacao=date('Y');
                    $saldo_veiculo->system_users_id=TSession::getValue('userid');
                    $saldo_veiculo->veiculos_id=$object->id;
                    $saldo_veiculo->store();
                }
                
            }
    //<fieldList-2262092-18290652> //</hideLine>
            $anexos_veiculo_veiculos_items = $this->storeItems('AnexosVeiculo', 'veiculos_id', $object, $this->fieldList_672d41ca04112, function($masterObject, $detailObject){ //</blockLine>

                //code here

                //</autoCode>
            }, $this->criteria_fieldList_672d41ca04112); //</blockLine>
    //</hideLine> //</fieldList-2262092-18290652>
//<generatedAutoCode>
            if(!empty($anexos_veiculo_veiculos_items))
            {
                foreach ($anexos_veiculo_veiculos_items as $item)
                {
                    $dataFile = new stdClass();
                    $dataFile->descricao = $item->descricao;
                    $this->saveFile($item, $dataFile, 'descricao', $anexos_veiculo_veiculos_descricao_dir);
                }
            }

//</generatedAutoCode>

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; //</blockLine>

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            //</messageAutoCode> //</blockLine>
//<generatedAutoCode>
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('VeiculosList', 'onShow', $loadPageParam);
//</generatedAutoCode>

            //</endTryAutoCode> //</blockLine>
//<generatedAutoCode>
            TScript::create("Template.closeRightPanel();");
            TForm::sendData(self::$formName, (object)['id' => $object->id]);

//</generatedAutoCode>

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> //</blockLine> 
//<generatedAutoCode>

            $this->fireEvents($this->form->getData());
//</generatedAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
//</generated-FormAction-onSave>

//<generated-onEdit>
    public function onEdit( $param )//</ini>
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Veiculos($key); // instantiates the Active Record //</blockLine>
                // $objectvei = new stdClass();
                // $objectvei->anof = $object->anof;
            
                //</beforeSetDataAutoCode> //</blockLine> 

                $this->fieldList_67cafdd5cae14_items = $this->loadItems('FotosVeiculos', 'veiculos_id', $object, $this->fieldList_67cafdd5cae14, function($masterObject, $detailObject, $objectItems){ //</blockLine>

                    //code here

                    //</autoCode>
                }, $this->criteria_fieldList_67cafdd5cae14); //</blockLine>
    //</hideLine> //</fieldList-2347998-18988994>
    //<fieldList-2262095-18290682> //</hideLine>
                $this->fieldList_672d41ef04116_items = $this->loadItems('SaldoVeiculo', 'veiculos_id', $object, $this->fieldList_672d41ef04116, function($masterObject, $detailObject, $objectItems){ //</blockLine>

                    //code here

                    //</autoCode>
                }, $this->criteria_fieldList_672d41ef04116); //</blockLine>
    //</hideLine> //</fieldList-2262095-18290682>

    //<fieldList-2262092-18290652> //</hideLine>
                $this->fieldList_672d41ca04112_items = $this->loadItems('AnexosVeiculo', 'veiculos_id', $object, $this->fieldList_672d41ca04112, function($masterObject, $detailObject, $objectItems){ //</blockLine>

                    //code here

                    //</autoCode>
                }, $this->criteria_fieldList_672d41ca04112); //</blockLine>

            //     // 2) MODELO: recarrega opções e re-seleciona o valor salvo
            // self::onChangemarca_id([
            //     'marca_id' => $object->marca_id ?? null, // use o filtro que você tiver; se não houver, pode passar []
            // ]);
            // if (!empty($object->modelo_id)) {
            //     TForm::sendData(self::$formName, (object) ['modelo_id' => $object->modelo_id]);
            // }

            // // 3) ANO DO MODELO: recarrega opções e re-seleciona
            // if (!empty($object->modelo_id)) {
            //     self::onChangemodelo_id(['modelo_id' => $object->modelo_id]);
            // }
            // if (!empty($object->anof)) {
            //     TForm::sendData(self::$formName, (object) ['anof' => $object->anof]);
            // }

            // // 4) Campos dependentes de (modelo + ano): FIPE, espécie, etc.
            // if (!empty($object->modelo_id) && !empty($object->anof)) {
            //     self::onChangeanof([
            //         'modelo_id' => $object->modelo_id,
            //         'anof'      => $object->anof,
            //     ]);
            // }
    //</hideLine> //</fieldList-2262092-18290652>
                // TForm::sendData('form_VeiculosForm', $objectvei);
                $this->form->setData($object); // fill the form //</blockLine>

                //</afterSetDataAutoCode> //</blockLine>
//<generatedAutoCode>

                $this->fireEvents($object);

//</generatedAutoCode>

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
    }//</end>
//</generated-onEdit>

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

        $this->fieldList_672d41ca04112->addHeader();
        $this->fieldList_672d41ca04112->addDetail($this->default_item_fieldList_672d41ca04112);

        $this->fieldList_672d41ca04112->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_672d41ef04116->addHeader();
        $this->fieldList_672d41ef04116->addDetail($this->default_item_fieldList_672d41ef04116);

        $this->fieldList_672d41ef04116->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        //<onFormClear>

        //</onFormClear>

    }

    public function onShow($param = null)
    {
        $this->fieldList_672d41ca04112->addHeader();
        $this->fieldList_672d41ca04112->addDetail($this->default_item_fieldList_672d41ca04112);

        $this->fieldList_672d41ca04112->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_672d41ef04116->addHeader();
        $this->fieldList_672d41ef04116->addDetail($this->default_item_fieldList_672d41ef04116);

        $this->fieldList_672d41ef04116->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        //<onShow>

        //</onShow>
    } 

    public function fireEvents( $object )
    {
        $obj = new stdClass;
        if(is_object($object) && get_class($object) == 'stdClass')
        {
            if(isset($object->system_unit_id))
            {
                $value = $object->system_unit_id;

                $obj->system_unit_id = $value;
            }
            if(isset($object->departamento_unit_id))
            {
                $value = $object->departamento_unit_id;

                $obj->departamento_unit_id = $value;
            }
            if(isset($object->marca_id))
            {
                $value = $object->marca_id;

                $obj->marca_id = $value;
            }
            if(isset($object->modelo_id))
            {
                $value = $object->modelo_id;

                $obj->modelo_id = $value;
            }
        }
        elseif(is_object($object))
        {
            if(isset($object->system_unit_id))
            {
                $value = $object->system_unit_id;

                $obj->system_unit_id = $value;
            }
            if(isset($object->departamento_unit_id))
            {
                $value = $object->departamento_unit_id;

                $obj->departamento_unit_id = $value;
            }
            if(isset($object->marca_id))
            {
                $value = $object->marca_id;

                $obj->marca_id = $value;
            }
            if(isset($object->modelo_id))
            {
                $value = $object->modelo_id;

                $obj->modelo_id = $value;
            }
        }
        TForm::sendData(self::$formName, $obj);
    }  

    public static function getFormName()
    {
        return self::$formName;
    }

    //</hideLine> <addUserFunctionsCode/>

    //<userCustomFunctions>
    public static function onChangemarca_id($param)
    {
        try
        {

            if (isset($param['marca_id']) && $param['marca_id'])
            { 
                $criteria = TCriteria::create(['marca_id' => $param['marca_id']]);
                TDBCombo::reloadFromModel(self::$formName, 'modelo_id', 'minierp', 'Modelo', 'id', '{descricao}', 'descricao asc', $criteria, TRUE); 
            } 
            else 
            { 
                TCombo::clearField(self::$formName, 'modelo_id'); 
            }  

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    } 

    public static function onChangeanof($param)
    {
        try
        {
         //   $data = $this->form->getData(); // get form data as array

            if (!empty($param['anof']) && !empty($param['modelo_id']))
            { 
                TTransaction::open('minierp');

                $modelo = new Modelo($param['modelo_id']);
                if (!$modelo) {
                    throw new Exception('Modelo não encontrado.');
                }

                $modeloanos = ModeloAno::where('modelo_id', '=', $modelo->id)
                                    ->where('ano', '=', $param['anof'])
                                    ->load();

                if (!$modeloanos || !isset($modeloanos[0])) {
                    throw new Exception("Ano modelo '{$param['anof']}' não encontrado para este modelo.");
                }

                $obj = new stdClass();
                $obj->especie_id = $modelo->especie_id;
                $obj->familia_id = $modelo->familia_id;
                $obj->propriedade_id = $modelo->propriedade_id;
                $obj->tipo_veiculo_id = $modelo->tipo_veiculo_id;
                $obj->tipo_combustivel_id = $modelo->tipo_combustivel_id;
                $obj->valor_tabela_fipe = $modeloanos[0]->preco;

                TForm::sendData(self::$formName, $obj); 
                TTransaction::close();
            } 
       //     else {
       //         throw new Exception('Informe o ano de fabricação e o modelo do veículo.');
       //     }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    //</userCustomFunctions>
    // public  function onImportarMarcasModelos($param = null) 
    // {
    //     try 
    //     {
    //         //code here
    //         TTransaction::open('minierp');

    //         $urlMarcas = 'https://parallelum.com.br/fipe/api/v1/carros/marcas';
    //         $jsonMarcas = file_get_contents($urlMarcas);
    //         $marcas = json_decode($jsonMarcas, true);

    //         foreach ($marcas as $marcaData) {
    //             $nomeMarca = trim($marcaData['nome']);

    //             // Verifica se já existe a marca com o mesmo nome (case-insensitive)
    //             $marca = Marca::where('LOWER(descricao)', '=', strtolower($nomeMarca))->load();
    //             if ($marca) {
    //                $idmarca = $marca[0]->id;
    //             }

    //             if (!$marca) {
    //                 $marca = new Marca();
    //                 $marca->descricao = $nomeMarca;
    //                 $marca->store();
    //                 $idmarca=$marca->id;
    //             }

    //             // Buscar modelos dessa marca
    //             $urlModelos = "https://parallelum.com.br/fipe/api/v1/carros/marcas/{$marcaData['codigo']}/modelos";
    //             $jsonModelos = file_get_contents($urlModelos);
    //             $modelosData = json_decode($jsonModelos, true);

    //             foreach ($modelosData['modelos'] as $modeloData) {
    //                 $nomeModelo = trim($modeloData['nome']);

    //                 // Verifica se já existe o modelo com o mesmo nome e marca_id
    //                 $modelo = Modelo::where('marca_id', '=', $idmarca)
    //                                 ->where('LOWER(descricao)', '=', strtolower($nomeModelo))
    //                                 ->first();

    //                 if (!$modelo) {
    //                     $modelo = new Modelo();
    //                     $modelo->descricao = $nomeModelo;
    //                     $modelo->marca_id = $idmarca;
    //                     $modelo->codigo_fipe = $modelosData['codigoFipe'];
    //                     $modelo->anomodelo = $modelosData['anoModelo'];
    //                     $modelo->combustivel = $modelosData['combustivel'];
    //                     $modelo->tipo_veiculo = $modelosData['tipoVeiculo'];
    //                     $modelo->valor = $modelosData['valor'];
    //                     $modelo->store();
    //                 }
    //             }

    //             usleep(200000); // Evita sobrecarga na API
    //         }

    //         TTransaction::close();
    //         new TMessage('info', 'Importação concluída com sucesso.');
    //         //</autoCode>
    //     }
    //     catch (Exception $e) 
    //     {
    //         new TMessage('error', $e->getMessage());    
    //     }
    // }
    // public static function onImportarMarcasModelos()
    // {
    //     try {
    //         TTransaction::open('minierp'); // Substitua pelo seu banco

    //         $conn = TTransaction::get(); // conexão atual

    //         // Busca todas as marcas da sua tabela 'marca'
      
    //         $urlMarcas = 'https://parallelum.com.br/fipe/api/v1/carros/marcas';
    //         $jsonMarcas = file_get_contents($urlMarcas);
    //         $marcas = json_decode($jsonMarcas, true);

    //         foreach ($marcas as $marcaData) {

    //             $nomeMarca = trim($marcaData['nome']);

    //             //Verifica se já existe a marca com o mesmo nome (case-insensitive)
    //             $marca = Marca::where('LOWER(descricao)', '=', strtolower($nomeMarca))->load();
    //             if ($marca) {
    //                $idmarca = $marca[0]->id;
    //             }

    //             if (!$marca) {
    //                 $marca = new Marca();
    //                 $marca->descricao = $nomeMarca;
    //                 $marca->store();
    //                 $idmarca=$marca->id;
    //             }
    //             // Buscar modelos dessa marca
    //             $urlModelos = "https://parallelum.com.br/fipe/api/v1/carros/marcas/{$marcaData['codigo']}/modelos";
    //             $jsonModelos = file_get_contents($urlModelos);
    //             $modelosData = json_decode($jsonModelos, true);

    //             if (!isset($dados_modelos['modelos'])) {
    //                 continue;
    //             }

    //             foreach ($dados_modelos['modelos'] as $modelo) {
    //                 $modelo_id_fipe = $modelo['codigo'];

    //                 $url_anos = "https://parallelum.com.br/fipe/api/v1/carros/marcas/{$marca_id_fipe}/modelos/{$modelo_id_fipe}/anos";
    //                 $json_anos = @file_get_contents($url_anos);
    //                 $dados_anos = json_decode($json_anos, true);

    //                 if (!is_array($dados_anos)) continue;

    //                 foreach ($dados_anos as $ano) {
    //                     $codigo_ano = $ano['codigo'];

    //                     $url_detalhe = "https://parallelum.com.br/fipe/api/v1/carros/marcas/{$marca_id_fipe}/modelos/{$modelo_id_fipe}/anos/{$codigo_ano}";
    //                     $json_detalhe = @file_get_contents($url_detalhe);
    //                     $detalhe = json_decode($json_detalhe, true);

    //                     if (!isset($detalhe['modelo'])) continue;

    //                     // Registrar no banco (ou imprimir para depuração)
    //                     $registro = [
    //                         'marca_id'         => $marca_id_fipe,
    //                         'descricao'       => $detalhe['modelo'],
    //                         'codigo_fipe'      => $detalhe['codigoFipe'],
    //                         'anomodelo'              => $detalhe['anoModelo'],
    //                         'combustivel'      => $detalhe['combustivel'],
    //                         'tipoveiculo'     => $detalhe['tipoVeiculo'],
    //                         'valor'            => $detalhe['valor']
    //                     ];

    //                     // Salvar na tabela auxiliar 'fipe_modelo'
    //                     $obj = new Modelo();
    //                     $obj->fromArray($registro);
    //                     $obj->store();

    //                     sleep(1); // evita sobrecarga na API
    //                 }
    //             }
    //         }

    //         TTransaction::close();
    //         new TMessage('info', 'Consulta finalizada com sucesso!');

    //     } catch (Exception $e) {
    //         new TMessage('error', 'Erro: ' . $e->getMessage());
    //     }
    // }


    // public static function onChangeanof($param)
    // {
    //     try {
    //         TTransaction::open('minierp');

    //         // Validação inicial
    //         if (empty($param['marca_id']) || empty($param['modelo_id']) || empty($param['anof'])) {
    //             throw new Exception('Marca, modelo ou ano não definido.');
    //         }

    //         $marca = new Marca($param['marca_id']);
    //         $modelo = new Modelo($param['modelo_id']);

    //         $inputMarca  = strtolower(trim($marca->descricao));
    //         $inputModelo = strtolower(trim($modelo->descricao));
    //         $anoInformado = trim($param['anof']);

    //         // Buscar código da marca na FIPE
    //         $urlMarcas = 'https://parallelum.com.br/fipe/api/v1/carros/marcas';
    //         $marcas = json_decode(file_get_contents($urlMarcas), true);
    //         $codigoMarcaFipe = null;

    //         foreach ($marcas as $m) {
    //             if (strtolower($m['nome']) == $inputMarca) {
    //                 $codigoMarcaFipe = $m['codigo'];
    //                 break;
    //             }
    //         }

    //         if (!$codigoMarcaFipe) {
    //             throw new Exception("Marca '{$marca->descricao}' não encontrada na FIPE.");
    //         }

    //         // Buscar código do modelo
    //         $urlModelos = "https://parallelum.com.br/fipe/api/v1/carros/marcas/{$codigoMarcaFipe}/modelos";
    //         $modelos = json_decode(file_get_contents($urlModelos), true);
    //         $codigoModeloFipe = null;

    //         foreach ($modelos['modelos'] as $mod) {
    //             if (strtolower($mod['nome']) == $inputModelo) {
    //                 $codigoModeloFipe = $mod['codigo'];
    //                 break;
    //             }
    //         }

    //         if (!$codigoModeloFipe) {
    //             throw new Exception("Modelo '{$modelo->descricao}' não encontrado na FIPE.");
    //         }

    //         // Buscar lista de anos disponíveis para esse modelo
    //         $urlAnos = "https://parallelum.com.br/fipe/api/v1/carros/marcas/{$codigoMarcaFipe}/modelos/{$codigoModeloFipe}/anos";
    //         $anosDisponiveis = json_decode(file_get_contents($urlAnos), true);

    //         $codigoAnoFipe = null;
    //         foreach ($anosDisponiveis as $item) {
    //             // item['nome'] = "2023 Gasolina", "2022 Diesel", etc.
    //             if (strpos($item['nome'], $anoInformado) !== false) {
    //                 $codigoAnoFipe = $item['codigo'];
    //                 break;
    //             }
    //         }

    //         if (!$codigoAnoFipe) {
    //             throw new Exception("Ano '{$anoInformado}' não encontrado na FIPE para este modelo.");
    //         }

    //         // Buscar os dados finais da FIPE com o código correto do ano
    //         $urlDetalhes = "https://parallelum.com.br/fipe/api/v1/carros/marcas/{$codigoMarcaFipe}/modelos/{$codigoModeloFipe}/anos/{$codigoAnoFipe}";
    //         $dados = json_decode(file_get_contents($urlDetalhes), true);

    //         if (!isset($dados['Valor'])) {
    //             throw new Exception('Não foi possível obter o valor FIPE.');
    //         }

    //         $valor       = $dados['Valor'];
    //         $combustivel = $dados['Combustivel'];
    //         $mes         = $dados['MesReferencia'];
    //         $codigoFipe  = $dados['CodigoFipe'];

    //         throw new Exception("Os dados encontrados para este veículo foram:<br><br>
    //                             <b>Valor FIPE:</b> {$valor}<br>
    //                             <b>Codigo Fipe:</b> {$combustivel}<br>
    //                             <b>Combustível:</b> {$codigoFipe}<br>
    //                             <b>Mês de Referência:</b> {$mes}"
    //                         );

    //         TTransaction::close();

    //     } catch (Exception $e) {
    //         new TMessage('info', $e->getMessage());
    //         TTransaction::rollback();
    //     }
    // }
    public static function onChangemodelo_id($param)
    {
        try
        {

            if (isset($param['modelo_id']) && $param['modelo_id'])
            { 
           //     $criteria = TCriteria::create(['modelo_id' => $param['modelo_id']]);
                TTransaction::open('minierp');
                $modelox = new Modelo($param['modelo_id']);
                $modeloano = ModeloAno::where('modelo_id','=',$param['modelo_id'])
                                   ->load();
                $anos = [];
                if ($modeloano)
                {
                    foreach ($modeloano as $ma) {
                        $anos[] = $ma->ano;
                    }
                }
                $data = new stdClass();
                $data->anosencontrados = $anos;
                $data->especie_id = $modelox->especie_id;
                $data->familia_id = $modelox->familia_id;
                $data->propriedade_id = $modelox->propriedade_id;
                $data->tipo_veiculo_id = $modelox->tipo_veiculo_id;
                $data->tipo_combustivel_id = $modelox->tipo_combustivel_id;
                
                TTransaction::close();

                TForm::sendData(self::$formName, $data);
        //        TDBCombo::reloadFromModel(self::$formName, 'anof', 'minierp', 'ModeloAno', 'ano', '{ano}', 'ano asc', $criteria, TRUE); 
            } 
            else 
            { 
          //      TCombo::clearField(self::$formName, 'anof'); 
            }  

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    } 


}
