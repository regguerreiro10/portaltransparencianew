<?php

class PessoaFormold extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Pessoa';
    private static $primaryKey = 'id';
    private static $formName = 'form_PessoaForm';

    use BuilderMasterDetailTrait;
    use BuilderMasterDetailFieldListTrait;
    use Adianti\Base\AdiantiFileSaveTrait;

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
        $this->form->setFormTitle("Cadastro de Estabelecimentos");

        $criteria_system_user_id = new TCriteria();
        $criteria_tipo_cliente_id = new TCriteria();
        $criteria_pessoa_endereco_pessoa_cidade_estado_id = new TCriteria();
        $criteria_grupos = new TCriteria();
        $criteria_grupos->add(new TFilter('id', '=', GrupoPessoa::FORNECEDOR));
        $criteria_pessoa_departamento_pessoa_departamento_unit_id = new TCriteria();
        $criteria_seguimento_pessoa_pessoa_seguimento_id = new TCriteria();

        // $filterVar = TSession::getValue('idunit');;
        // $criteria_system_user_id->add(new TFilter('system_unit_id', '=', $filterVar)); 

        $id = new TEntry('id');
        $system_user_id = new TDBCombo('system_user_id', 'minierp', 'ViewEmailUsuarios', 'system_users_id', '{name} - {email}','name asc' , $criteria_system_user_id );
        $from_seek_form_name = new THidden('from_seek_form_name');
        $from_page = new THidden('from_page');
        $from_page_field = new THidden('from_page_field');
        $abrirpedido = new TCombo('abrirpedido');
        $ativo = new TRadioGroup('ativo');
        $nome = new TEntry('nome');
        $tipo_cliente_id = new TDBCombo('tipo_cliente_id', 'minierp', 'TipoCliente', 'id', '{nome}','nome asc' , $criteria_tipo_cliente_id );
        $documento = new TEntry('documento');
        $button_buscar_cnpj = new TButton('button_buscar_cnpj');
        $obs = new TText('obs');
        $email = new TEntry('email');
        $fone = new TEntry('fone');
                $horariofuncionamento = new TText('horariofuncionamento');
        $horariofuncionamento->setSize('100%', 70);

        $pessoa_endereco_pessoa_nome = new TEntry('pessoa_endereco_pessoa_nome');
        $pessoa_endereco_pessoa_principal = new TCombo('pessoa_endereco_pessoa_principal');
        $pessoa_endereco_pessoa_cep = new TEntry('pessoa_endereco_pessoa_cep');
        $button_buscar_pessoa_endereco_pessoa = new TButton('button_buscar_pessoa_endereco_pessoa');
        $pessoa_endereco_pessoa_cidade_estado_id = new TDBCombo('pessoa_endereco_pessoa_cidade_estado_id', 'minierp', 'Estado', 'id', '{nome}','nome asc' , $criteria_pessoa_endereco_pessoa_cidade_estado_id );
        $pessoa_endereco_pessoa_id = new THidden('pessoa_endereco_pessoa_id');
        $pessoa_endereco_pessoa_cidade_id = new TCombo('pessoa_endereco_pessoa_cidade_id');
        $pessoa_endereco_pessoa_rua = new TEntry('pessoa_endereco_pessoa_rua');
        $pessoa_endereco_pessoa_numero = new TEntry('pessoa_endereco_pessoa_numero');
        $pessoa_endereco_pessoa_bairro = new TEntry('pessoa_endereco_pessoa_bairro');
        $pessoa_endereco_pessoa_complemento = new TEntry('pessoa_endereco_pessoa_complemento');
        $pessoa_endereco_pessoa_longitude = new TEntry('pessoa_endereco_pessoa_longitude');
        $pessoa_endereco_pessoa_latitude = new TEntry('pessoa_endereco_pessoa_latitude');
        $button_adicionar_pessoa_endereco_pessoa = new TButton('button_adicionar_pessoa_endereco_pessoa');
        $pessoa_contato_pessoa_id = new THidden('pessoa_contato_pessoa_id[]');
        $pessoa_contato_pessoa___row__id = new THidden('pessoa_contato_pessoa___row__id[]');
        $pessoa_contato_pessoa___row__data = new THidden('pessoa_contato_pessoa___row__data[]');
        $pessoa_contato_pessoa_nome = new TEntry('pessoa_contato_pessoa_nome[]');
        $pessoa_contato_pessoa_email = new TEntry('pessoa_contato_pessoa_email[]');
        $pessoa_contato_pessoa_telefone = new TEntry('pessoa_contato_pessoa_telefone[]');
        $pessoa_contato_pessoa_obs = new TEntry('pessoa_contato_pessoa_obs[]');
        $this->detalhe_de_contatos = new TFieldList();
        $grupos = new TDBCheckGroup('grupos', 'minierp', 'GrupoPessoa', 'id', '{nome}','nome asc' , $criteria_grupos );
        $pessoa_departamento_pessoa_id = new THidden('pessoa_departamento_pessoa_id[]');
        $pessoa_departamento_pessoa___row__id = new THidden('pessoa_departamento_pessoa___row__id[]');
        $pessoa_departamento_pessoa___row__data = new THidden('pessoa_departamento_pessoa___row__data[]');
        $pessoa_departamento_pessoa_departamento_unit_id = new TDBCombo('pessoa_departamento_pessoa_departamento_unit_id[]', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_pessoa_departamento_pessoa_departamento_unit_id );
        $this->fieldList_668d1b5795fc2 = new TFieldList();
        $seguimento_pessoa_pessoa_id = new THidden('seguimento_pessoa_pessoa_id[]');
        $seguimento_pessoa_pessoa___row__id = new THidden('seguimento_pessoa_pessoa___row__id[]');
        $seguimento_pessoa_pessoa___row__data = new THidden('seguimento_pessoa_pessoa___row__data[]');
        $seguimento_pessoa_pessoa_seguimento_id = new TDBCombo('seguimento_pessoa_pessoa_seguimento_id[]', 'minierp', 'Seguimento', 'id', '{descricao}','id asc' , $criteria_seguimento_pessoa_pessoa_seguimento_id );
        $seguimento_pessoa_pessoa_seguimento_id->configureNoResultsQuickRegister(new TAction(['SeguimentoForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $seguimento_pessoa_pessoa_seguimento_id->setNoResultsMessage("Nenhum seguimento encontrado. Clique no cadastrar");
        $this->fieldList_666bb0ef7738c = new TFieldList();
        $banco = new TEntry('banco');
        $agencia = new TEntry('agencia');
        $conta = new TEntry('conta');
        $operacao = new TCombo('operacao');
        $favorecido = new TEntry('favorecido');
        $tipochavepix = new TCombo('tipochavepix');
        $chavepix = new TEntry('chavepix');
        // $selo = new TFile('selo');
        $selo1 = new TCheckButton('selo');

           // Define os valores que devem vir checkados
        $valores_checkados = [GrupoPessoa::FORNECEDOR]; // Substitua pelos IDs corretos dos grupos

        $grupos->setValue($valores_checkados);

        $selo1->setUseSwitch(true, 'blue');
    
        $this->detalhe_de_contatos->addField(null, $pessoa_contato_pessoa_id, []);
        $this->detalhe_de_contatos->addField(null, $pessoa_contato_pessoa___row__id, ['uniqid' => true]);
        $this->detalhe_de_contatos->addField(null, $pessoa_contato_pessoa___row__data, []);
        $this->detalhe_de_contatos->addField(new TLabel("Nome", null, '14px', null), $pessoa_contato_pessoa_nome, ['width' => '25%']);
        $this->detalhe_de_contatos->addField(new TLabel("Email", null, '14px', null), $pessoa_contato_pessoa_email, ['width' => '25%']);
        $this->detalhe_de_contatos->addField(new TLabel("Telefone", null, '14px', null), $pessoa_contato_pessoa_telefone, ['width' => '25%']);
        $this->detalhe_de_contatos->addField(new TLabel("Obs", null, '14px', null), $pessoa_contato_pessoa_obs, ['width' => '25%']);

        $this->detalhe_de_contatos->width = '100%';
        $this->detalhe_de_contatos->setFieldPrefix('pessoa_contato_pessoa');
        $this->detalhe_de_contatos->name = 'detalhe_de_contatos';

        $this->criteria_detalhe_de_contatos = new TCriteria();
        $this->default_item_detalhe_de_contatos = new stdClass();

        $this->form->addField($pessoa_contato_pessoa_id);
        $this->form->addField($pessoa_contato_pessoa___row__id);
        $this->form->addField($pessoa_contato_pessoa___row__data);
        $this->form->addField($pessoa_contato_pessoa_nome);
        $this->form->addField($pessoa_contato_pessoa_email);
        $this->form->addField($pessoa_contato_pessoa_telefone);
        $this->form->addField($pessoa_contato_pessoa_obs);

        $this->detalhe_de_contatos->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $this->fieldList_668d1b5795fc2->addField(null, $pessoa_departamento_pessoa_id, []);
        $this->fieldList_668d1b5795fc2->addField(null, $pessoa_departamento_pessoa___row__id, ['uniqid' => true]);
        $this->fieldList_668d1b5795fc2->addField(null, $pessoa_departamento_pessoa___row__data, []);
        $this->fieldList_668d1b5795fc2->addField(new TLabel("Departamentos / Secretárias", null, '14px', null), $pessoa_departamento_pessoa_departamento_unit_id, ['width' => '100%']);

        $this->fieldList_668d1b5795fc2->width = '100%';
        $this->fieldList_668d1b5795fc2->setFieldPrefix('pessoa_departamento_pessoa');
        $this->fieldList_668d1b5795fc2->name = 'fieldList_668d1b5795fc2';

        $this->criteria_fieldList_668d1b5795fc2 = new TCriteria();
        $this->default_item_fieldList_668d1b5795fc2 = new stdClass();

        $this->form->addField($pessoa_departamento_pessoa_id);
        $this->form->addField($pessoa_departamento_pessoa___row__id);
        $this->form->addField($pessoa_departamento_pessoa___row__data);
        $this->form->addField($pessoa_departamento_pessoa_departamento_unit_id);

        $this->fieldList_668d1b5795fc2->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $this->fieldList_666bb0ef7738c->addField(null, $seguimento_pessoa_pessoa_id, []);
        $this->fieldList_666bb0ef7738c->addField(null, $seguimento_pessoa_pessoa___row__id, ['uniqid' => true]);
        $this->fieldList_666bb0ef7738c->addField(null, $seguimento_pessoa_pessoa___row__data, []);
        $this->fieldList_666bb0ef7738c->addField(new TLabel("Seguimento id", '#FF0000', '14px', null), $seguimento_pessoa_pessoa_seguimento_id, ['width' => '100%']);

        $this->fieldList_666bb0ef7738c->width = '100%';
        $this->fieldList_666bb0ef7738c->setFieldPrefix('seguimento_pessoa_pessoa');
        $this->fieldList_666bb0ef7738c->name = 'fieldList_666bb0ef7738c';

        $this->criteria_fieldList_666bb0ef7738c = new TCriteria();
        $this->default_item_fieldList_666bb0ef7738c = new stdClass();

        $this->form->addField($seguimento_pessoa_pessoa_id);
        $this->form->addField($seguimento_pessoa_pessoa___row__id);
        $this->form->addField($seguimento_pessoa_pessoa___row__data);
        $this->form->addField($seguimento_pessoa_pessoa_seguimento_id);

        $this->fieldList_666bb0ef7738c->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $pessoa_endereco_pessoa_cidade_estado_id->setChangeAction(new TAction([$this,'onChangepessoa_endereco_pessoa_cidade_estado_id']));

        $nome->addValidation("Nome", new TRequiredValidator()); 
        $tipo_cliente_id->addValidation("Tipo de cliente", new TRequiredValidator()); 
        $documento->addValidation("Documento", new TRequiredValidator()); 
        $email->addValidation("Email", new TRequiredValidator()); 
        $fone->addValidation("Telefone", new TRequiredValidator()); 
        $grupos->addValidation("Grupos", new TRequiredValidator()); 
        $pessoa_departamento_pessoa_departamento_unit_id->addValidation("Departamento unit id", new TRequiredListValidator()); 
        $seguimento_pessoa_pessoa_seguimento_id->addValidation("Seguimento id", new TRequiredListValidator()); 
        $email->addValidation("Email", new TEmailValidator(), []); 
        $ativo->addValidation("Ativo", new TRequiredValidator()); 

        $selo1->setIndexValue("1");
        $selo1->setInactiveIndexValue("2");
        $selo1->setValue('2');

        $id->setEditable(false);

        $ativo->setValue('T');
        $ativo->setUseButton();
        $ativo->setLayout('horizontal');
     
        $grupos->setLayout('horizontal');
        // $selo->enableFileHandling();
        // $selo->setSize('100%');

        $fone->setMask('(99) 99999-9999');
        $pessoa_contato_pessoa_telefone->setMask('(99) 99999-9999');

        $button_buscar_cnpj->setAction(new TAction([$this, 'onBuscarDadosCNPJ']), "Buscar CNPJ");
        $button_buscar_pessoa_endereco_pessoa->setAction(new TAction([$this, 'onBuscarCep']), "Buscar");
        $button_adicionar_pessoa_endereco_pessoa->setAction(new TAction([$this, 'onAddDetailPessoaEnderecoPessoa'],['static' => 1]), "Adicionar");

        $button_buscar_cnpj->addStyleClass('btn-default');
        $button_buscar_pessoa_endereco_pessoa->addStyleClass('btn-default');
        $button_adicionar_pessoa_endereco_pessoa->addStyleClass('btn-default');

        $button_buscar_cnpj->setImage('fas:address-card #000000');
        $button_buscar_pessoa_endereco_pessoa->setImage('fas:search #000000');
        $button_adicionar_pessoa_endereco_pessoa->setImage('fas:plus #2ecc71');

        $abrirpedido->addItems(["Sim"=>"Sim","Nao"=>"Não"]);
          $ativo->addItems(["T"=>"Sim","F"=>"Não"]);
        $pessoa_endereco_pessoa_principal->addItems(["T"=>"Sim","F"=>"Não"]);
        $tipochavepix->addItems(["Celular"=>"Celular","CPF"=>"CPF","CNPJ"=>"CNPJ","E-mail"=>"E-mail","Chave Aleatória"=>"Chave Aleatória"]);
        $operacao->addItems(["Conta corrente"=>"Conta corrente","Poupança"=>"Poupança","Investimento"=>"Investimento","Digital"=>"Digital"]);

        $operacao->enableSearch();
        $abrirpedido->enableSearch();
        $tipochavepix->enableSearch();
        $seguimento_pessoa_pessoa_seguimento_id->enableSearch();
        $pessoa_endereco_pessoa_cidade_estado_id->enableSearch();
        $pessoa_departamento_pessoa_departamento_unit_id->enableSearch();

        $nome->setMaxLength(500);
        $fone->setMaxLength(255);
        $email->setMaxLength(255);
        $documento->setMaxLength(20);
        $pessoa_endereco_pessoa_cep->setMaxLength(10);
        $pessoa_endereco_pessoa_rua->setMaxLength(500);
        $pessoa_endereco_pessoa_nome->setMaxLength(255);
        $pessoa_endereco_pessoa_numero->setMaxLength(20);
        $pessoa_endereco_pessoa_bairro->setMaxLength(500);
        $pessoa_endereco_pessoa_complemento->setMaxLength(500);
        

        $id->setSize('100%');
        $ativo->setSize(80);
        $grupos->setSize(180);
        $nome->setSize('100%');
        $fone->setSize('100%');
        $email->setSize('100%');
        $banco->setSize('100%');
        $conta->setSize('100%');
        $from_page->setSize(200);
        $obs->setSize('100%', 70);
        $agencia->setSize('100%');
        $operacao->setSize('100%');
        $chavepix->setSize('100%');
        $favorecido->setSize('100%');
        $abrirpedido->setSize('100%');
        $from_page_field->setSize(200);
        $tipochavepix->setSize('100%');
        
        $system_user_id->setSize('100%');
        $tipo_cliente_id->setSize('100%');
        $from_seek_form_name->setSize(200);
        $pessoa_endereco_pessoa_id->setSize(200);
        $documento->setSize('calc(100% - 140px)');
        $pessoa_contato_pessoa_obs->setSize('100%');
        $pessoa_endereco_pessoa_rua->setSize('100%');
        $pessoa_contato_pessoa_nome->setSize('100%');
        $pessoa_endereco_pessoa_nome->setSize('100%');
        $pessoa_contato_pessoa_email->setSize('100%');
        $pessoa_endereco_pessoa_numero->setSize('100%');
        $pessoa_endereco_pessoa_bairro->setSize('100%');
        $pessoa_contato_pessoa_telefone->setSize('100%');
        $pessoa_endereco_pessoa_principal->setSize('100%');
        $pessoa_endereco_pessoa_cidade_id->setSize('100%');
        $pessoa_endereco_pessoa_complemento->setSize('100%');
        $pessoa_endereco_pessoa_longitude->SetSize('100%');
        $pessoa_endereco_pessoa_latitude->SetSize('100%');
        $seguimento_pessoa_pessoa_seguimento_id->setSize('100%');
        $pessoa_endereco_pessoa_cidade_estado_id->setSize('100%');
        $pessoa_endereco_pessoa_cep->setSize('calc(100% - 100px)');
        $pessoa_departamento_pessoa_departamento_unit_id->setSize('100%');


        $button_adicionar_pessoa_endereco_pessoa->id = '622937d6f9f19';

  

        $this->form->appendPage("Dados gerais");

        $this->form->addFields([new THidden('current_tab')]);
        $this->form->setTabFunction("$('[name=current_tab]').val($(this).attr('data-current_page'));");

        $tab_668d1f3795fd6 = new BootstrapFormBuilder('tab_668d1f3795fd6');
        $this->tab_668d1f3795fd6 = $tab_668d1f3795fd6;
        $tab_668d1f3795fd6->setProperty('style', 'border:none; box-shadow:none;');

        $tab_668d1f3795fd6->appendPage("Dados");

        $tab_668d1f3795fd6->addFields([new THidden('current_tab_tab_668d1f3795fd6')]);
        $tab_668d1f3795fd6->setTabFunction("$('[name=current_tab_tab_668d1f3795fd6]').val($(this).attr('data-current_page'));");

        $row1 = $tab_668d1f3795fd6->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Usuário do Sistema:", '#FF0000', '14px', null, '100%'),$system_user_id],[$from_seek_form_name,$from_page,$from_page_field,new TLabel("Abrir pedido:", null, '14px', null),$abrirpedido],[new TLabel("Ativo:*", '#ff0000', '14px', null, '100%'),$ativo]);
        $row1->layout = [' col-sm-2','col-sm-6',' col-sm-2',' col-sm-2'];

        // $row1 = $tab_668d1f3795fd6->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Usuário do Sistema:", '#FF0000', '14px', null, '100%'),$system_user_id],[$from_seek_form_name,$from_page,$from_page_field],[new TLabel("Abrir pedido:", null, '14px', null),$abrirpedido]);
        // $row1->layout = [' col-sm-2','col-sm-6',' col-sm-2',' col-sm-2']; 

        $row2 = $tab_668d1f3795fd6->addFields([new TLabel("Nome:", '#ff0000', '14px', null, '100%'),$nome],[new TLabel("Tipo de cliente:", '#ff0000', '14px', null, '100%'),$tipo_cliente_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $tab_668d1f3795fd6->addFields([new TLabel("Documento:", '#ff0000', '14px', null, '100%'),$documento,$button_buscar_cnpj],[new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row3->layout = ['col-sm-6',' col-sm-6'];

        $row4 = $tab_668d1f3795fd6->addFields([new TLabel("Email:", '#FF0000', '14px', null, '100%'),$email],[new TLabel("Fone:", '#FF0000', '14px', null, '100%'),$fone]);
        $row4->layout = ['col-sm-6','col-sm-6'];
        
        $row9 = $tab_668d1f3795fd6->addFields([new TLabel("<br>Ativar selo ambiental ?", null, '14px', null, '100%'),$selo1]);
        $row9->layout = [' col-sm-12'];
        $row10 = $tab_668d1f3795fd6->addFields([new TLabel("<br>Horário de Funcionamento:", null, '14px', null, '100%'),$horariofuncionamento]);
        $row10->layout = ['col-sm-12'];
        $tab_668d1f3795fd6->appendPage("Endereço");

        $this->detailFormPessoaEnderecoPessoa = new BootstrapFormBuilder('detailFormPessoaEnderecoPessoa');
        $this->detailFormPessoaEnderecoPessoa->setProperty('style', 'border:none; box-shadow:none; width:100%;');

        $this->detailFormPessoaEnderecoPessoa->setProperty('class', 'form-horizontal builder-detail-form');

        $row5 = $this->detailFormPessoaEnderecoPessoa->addFields([new TLabel("Nome:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_nome],[new TLabel("Principal:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_principal]);
        $row5->layout = ['col-sm-6',' col-sm-6'];

        $row6 = $this->detailFormPessoaEnderecoPessoa->addFields([new TLabel("CEP:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_cep,$button_buscar_pessoa_endereco_pessoa]);
        $row6->layout = ['col-sm-6'];

        $row7 = $this->detailFormPessoaEnderecoPessoa->addFields([new TLabel("Estado:", '#FF0000', '14px', null, '100%'),$pessoa_endereco_pessoa_cidade_estado_id,$pessoa_endereco_pessoa_id],[new TLabel("Cidade:", '#ff0000', '14px', null, '100%'),$pessoa_endereco_pessoa_cidade_id]);
        $row7->layout = ['col-sm-6','col-sm-6'];

        $row8 = $this->detailFormPessoaEnderecoPessoa->addFields([new TLabel("Rua:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_rua],[new TLabel("Numero:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_numero]);
        $row8->layout = ['col-sm-6','col-sm-6'];

        $row9 = $this->detailFormPessoaEnderecoPessoa->addFields([new TLabel("Bairro:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_bairro],[new TLabel("Complemento:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_complemento]);
        $row9->layout = ['col-sm-6','col-sm-6'];

        $row11 = $this->detailFormPessoaEnderecoPessoa->addFields([new TLabel("Longitude:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_longitude],[new TLabel("Latitude:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_latitude]);
        $row11->layout = ['col-sm-6','col-sm-6'];

        $row10 = $this->detailFormPessoaEnderecoPessoa->addFields([$button_adicionar_pessoa_endereco_pessoa]);
        $row10->layout = [' col-sm-12'];

        $row11 = $this->detailFormPessoaEnderecoPessoa->addFields([new THidden('pessoa_endereco_pessoa__row__id')]);
        $this->pessoa_endereco_pessoa_criteria = new TCriteria();

        $this->pessoa_endereco_pessoa_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->pessoa_endereco_pessoa_list->disableHtmlConversion();;
        $this->pessoa_endereco_pessoa_list->generateHiddenFields();
        $this->pessoa_endereco_pessoa_list->setId('pessoa_endereco_pessoa_list');

        $this->pessoa_endereco_pessoa_list->style = 'width:100%';
        $this->pessoa_endereco_pessoa_list->class .= ' table-bordered';

        $column_pessoa_endereco_pessoa_nome = new TDataGridColumn('nome', "Nome", 'left');
        $column_pessoa_endereco_pessoa_cidade_nome = new TDataGridColumn('cidade->nome', "Cidade", 'left');
        $column_pessoa_endereco_pessoa_rua = new TDataGridColumn('rua', "Rua", 'left');
        $column_pessoa_endereco_pessoa_numero = new TDataGridColumn('numero', "Numero", 'left');
        $column_pessoa_endereco_pessoa_bairro = new TDataGridColumn('bairro', "Bairro", 'left');
        $column_pessoa_endereco_pessoa_principal_transformed = new TDataGridColumn('principal', "Principal", 'left');
        $column_pessoa_endereco_pessoa_selo1 = new TDataGridColumn('selo', "Selo Ambiental", 'left');

        $column_pessoa_endereco_pessoa__row__data = new TDataGridColumn('__row__data', '', 'center');
        $column_pessoa_endereco_pessoa__row__data->setVisibility(false);

        $action_onEditDetailPessoaEndereco = new TDataGridAction(array('PessoaForm', 'onEditDetailPessoaEndereco'));
        $action_onEditDetailPessoaEndereco->setUseButton(false);
        $action_onEditDetailPessoaEndereco->setButtonClass('btn btn-default btn-sm');
        $action_onEditDetailPessoaEndereco->setLabel("Editar");
        $action_onEditDetailPessoaEndereco->setImage('far:edit #478fca');
        $action_onEditDetailPessoaEndereco->setFields(['__row__id', '__row__data']);

        $this->pessoa_endereco_pessoa_list->addAction($action_onEditDetailPessoaEndereco);
        $action_onDeleteDetailPessoaEndereco = new TDataGridAction(array('PessoaForm', 'onDeleteDetailPessoaEndereco'));
        $action_onDeleteDetailPessoaEndereco->setUseButton(false);
        $action_onDeleteDetailPessoaEndereco->setButtonClass('btn btn-default btn-sm');
        $action_onDeleteDetailPessoaEndereco->setLabel("Excluir");
        $action_onDeleteDetailPessoaEndereco->setImage('fas:trash-alt #dd5a43');
        $action_onDeleteDetailPessoaEndereco->setFields(['__row__id', '__row__data']);

        $this->pessoa_endereco_pessoa_list->addAction($action_onDeleteDetailPessoaEndereco);

        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_nome);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_cidade_nome);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_rua);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_numero);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_bairro);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_principal_transformed);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_selo1);

        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa__row__data);

        $this->pessoa_endereco_pessoa_list->createModel();
        $tableResponsiveDiv = new TElement('div');
        $tableResponsiveDiv->class = 'table-responsive';
        $tableResponsiveDiv->add($this->pessoa_endereco_pessoa_list);
        $this->detailFormPessoaEnderecoPessoa->addContent([$tableResponsiveDiv]);

        $column_pessoa_endereco_pessoa_principal_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if($value === true || $value == 't' || $value === 1 || $value == '1' || $value == 's' || $value == 'S' || $value == 'T')
            {
                return 'Sim';
            }
            elseif($value === false || $value == 'f' || $value === 0 || $value == '0' || $value == 'n' || $value == 'N' || $value == 'F')   
            {
                return 'Não';
            }

            return $value;

        });

        
        $row12 = $tab_668d1f3795fd6->addFields([$this->detailFormPessoaEnderecoPessoa]);
        $row12->layout = [' col-sm-12'];

        $tab_668d1f3795fd6->appendPage("Contatos");
        $row13 = $tab_668d1f3795fd6->addFields([$this->detalhe_de_contatos]);
        $row13->layout = [' col-sm-12'];

        $tab_668d1f3795fd6->appendPage("Grupo");
        $row14 = $tab_668d1f3795fd6->addFields([$grupos]);
        $row14->layout = [' col-sm-12'];

        $tab_668d1f3795fd6->appendPage("Departamentos / Secretarias");
        $row15 = $tab_668d1f3795fd6->addFields([$this->fieldList_668d1b5795fc2]);
        $row15->layout = [' col-sm-12'];

        $tab_668d1f3795fd6->appendPage("Seguimento");
        $row16 = $tab_668d1f3795fd6->addFields([$this->fieldList_666bb0ef7738c]);
        $row16->layout = [' col-sm-12'];

        $tab_668d1f3795fd6->appendPage("Dados bancários");
      
        
      /*  $row17 = $tab_668d1f3795fd6->addFields([new TLabel("Banco:", null, '14px', null)],[$banco]);
        $row18 = $tab_668d1f3795fd6->addFields([new TLabel("Agência:", null, '14px', null)],[$agencia]);
        $row19 = $tab_668d1f3795fd6->addFields([new TLabel("Conta:", null, '14px', null)],[$conta]);
        $row20 = $tab_668d1f3795fd6->addFields([new TLabel("Tipo conta:", null, '14px', null)],[$operacao]);
        $row21 = $tab_668d1f3795fd6->addFields([new TLabel("Nome favorecido:", null, '14px', null)],[$favorecido]);
        $row22 = $tab_668d1f3795fd6->addFields([new TLabel("Tipo chave PIX:", null, '14px', null)],[$tipochavepix]);
        $row23 = $tab_668d1f3795fd6->addFields([new TLabel("Chave PIX:", null, '14px', null)],[$chavepix]);
*/
  $row17 = $this->tab_668d1f3795fd6->addFields([new TLabel("Banco:", null, '14px', null, '100%'),$banco],[new TLabel("Agência:", null, '14px', null, '100%'),$agencia], [new TLabel("Conta:", null, '14px', null, '100%'),$conta]);
        $row17->layout = ['col-sm-4','col-sm-4', 'col-sm-4'];

        $row18 = $this->tab_668d1f3795fd6->addFields([new TLabel("Tipo conta:", null, '14px', null, '100%'),$operacao],[new TLabel("Nome do favorecido:", null, '14px', null, '100%'),$favorecido]);
        $row18->layout = ['col-sm-6','col-sm-6'];

        $row19 = $this->tab_668d1f3795fd6->addFields([new TLabel("Tipo chave PIX:", null, '14px', null, '100%'),$tipochavepix],[new TLabel("Chave PIX:",null, '14px', null, '100%'),$chavepix]);
        $row19->layout = ['col-sm-6','col-sm-6'];

        


        $row29 = $this->form->addFields([$tab_668d1f3795fd6]);
        $row29->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['PessoaList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=PessoaForm]');
        $style->width = '70% !important';   
        $style->show(true);

    }

    public static function onChangepessoa_endereco_pessoa_cidade_estado_id($param)
    {
        try
        {

            if (isset($param['pessoa_endereco_pessoa_cidade_estado_id']) && $param['pessoa_endereco_pessoa_cidade_estado_id'])
            { 
                $criteria = TCriteria::create(['estado_id' => $param['pessoa_endereco_pessoa_cidade_estado_id']]);
                TDBCombo::reloadFromModel(self::$formName, 'pessoa_endereco_pessoa_cidade_id', 'minierp', 'Cidade', 'id', '{nome}', 'nome asc', $criteria, TRUE); 
            } 
            else 
            { 
                TCombo::clearField(self::$formName, 'pessoa_endereco_pessoa_cidade_id'); 
            }  

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    } 

    public static function onBuscarDadosCNPJ($param = null) 
    {
        try 
        {

            if($param['tipo_cliente_id'] != TipoCliente::JURIDICA)
            {
                throw new Exception('A busca de CNPJ é apenas para pessoa júridica ');
            }

            TTransaction::open(self::$database);
            $cnpj = str_replace(['.','-',' ','/'],['','',''], $param['documento']);
            $dados = CNPJService::get($cnpj);

            // iremos recarregar a combo de estado, pois pode ser que o estado encontrado para aquele CNPJ
            // ainda não foi cadastrado no sistema
            TCombo::reload(self::$formName, 'pessoa_endereco_pessoa_cidade_estado_id', Estado::getIndexedArray('id', 'nome'), true);

            TTransaction::close();

            $object = new stdClass();

            // dados principais
            $object->nome = $dados->razao_social;
            $object->fone = $dados->ddd_telefone_1;

            // dados relacionados ao endereço
            $object->pessoa_endereco_pessoa_cep = $dados->cep;
            $object->pessoa_endereco_pessoa_rua = $dados->logradouro;
            $object->pessoa_endereco_pessoa_bairro = $dados->bairro;
            $object->pessoa_endereco_pessoa_numero = $dados->numero;
            $object->pessoa_endereco_pessoa_complemento = $dados->complemento;
            $object->pessoa_endereco_pessoa_longitude = $dados->longitude;
            $object->pessoa_endereco_pessoa_latitude = $dados->latitude;
            $object->pessoa_endereco_pessoa_cidade_estado_id = $dados->estado_id;
            $object->pessoa_endereco_pessoa_cidade_id = $dados->cidade_id;

            TForm::sendData(self::$formName, $object);

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onBuscarCep($param = null) 
    {
        try 
        {
            if(!empty($param['pessoa_endereco_pessoa_cep']))
            {
                TTransaction::open(self::$database);
                $cep = $param['pessoa_endereco_pessoa_cep'];
                self::onBuscarCoordenadas($cep);
                $dadosCep = CEPService::get($param['pessoa_endereco_pessoa_cep']);

                if($dadosCep)
                {
                    $object = new stdClass();
                    $object->pessoa_endereco_pessoa_cidade_estado_id = $dadosCep->estado_id;
                    $object->pessoa_endereco_pessoa_cidade_id = $dadosCep->cidade_id;
                    $object->pessoa_endereco_pessoa_rua = $dadosCep->rua;
                    $object->pessoa_endereco_pessoa_bairro = $dadosCep->bairro;

                    // Código gerado pelo snippet: "Recarregar combo"

                    TCombo::reload(self::$formName, 'pessoa_endereco_pessoa_cidade_estado_id', Estado::getIndexedArray('id', 'nome'), true);
                    // -----

                    TForm::sendData(self::$formName, $object);    
                }

                

                TTransaction::close();
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onBuscarCoordenadas($cepRecebido)
    {
        try {
            $cep = preg_replace('/[^0-9]/', '', $cepRecebido);

            if (!$cep) {
                throw new Exception("Informe um CEP válido.");
            }

            // 1️⃣ Buscar Endereço pelo ViaCEP
            $viacep_url = "https://viacep.com.br/ws/{$cep}/json/";
            $endereco_data = @file_get_contents($viacep_url);
            $endereco_data = json_decode($endereco_data, true);

            if (isset($endereco_data['erro'])) {
                new TMessage('erro',"CEP não encontrado.");
            }

             
            $endereco = "{$endereco_data['logradouro']}, {$endereco_data['bairro']}, {$endereco_data['localidade']}, {$endereco_data['uf']}, Brasil";

            // 2️⃣ Buscar Latitude e Longitude no OpenStreetMap (Nominatim)
            $nominatim_url = "https://nominatim.openstreetmap.org/search?q=" . $cep.',+'.urlencode($endereco_data['localidade']) . "&format=json&limit=1";
//            $nominatim_url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($endereco) . "&format=json&limit=1";
            
            // 3️⃣ Função para buscar dados usando cURL
            $geo_data_raw = self::fetchDataWithCurl($nominatim_url);
            $geo_data = json_decode($geo_data_raw, true);

            // 4️⃣ Depuração: Verificar resposta da API
            if (empty($geo_data)) {
                new TMessage('error', "A API Nominatim retornou vazio. Resposta: " . htmlentities($geo_data_raw));
                return;
            }

            // 5️⃣ Garantir que temos latitude e longitude
            if (!isset($geo_data[0]['lat']) || !isset($geo_data[0]['lon'])) {
                new TMessage('error', "A resposta da API não contém latitude e longitude.");
                return;
            }


            $latitude = $geo_data[0]['lat'];
            $longitude = $geo_data[0]['lon'];

            // 3️⃣ Atualizar os campos do formulário
            $object = new stdClass();
            $object->pessoa_endereco_pessoa_latitude = $latitude;
            $object->pessoa_endereco_pessoa_longitude = $longitude;
            $estado = Estado::where('sigla', '=', $endereco_data['uf'])->first();
            if (!$estado) {
                $estado = new Estado();
                $estado->nome = $endereco_data['estado'];
                $estado->sigla = $endereco_data['uf'];
                $estado->ibge = $endereco_data['ibge'] ?? null; // Adicionando o IBGE se disponível
                $estado->store();
            }
            $cidade = Cidade::where('nome', '=', $endereco_data['localidade'])
                            ->where('estado_id', '=', $estado->id)
                            ->first();
            if (!$cidade) {
                $cidade = new Cidade();
                $cidade->nome = $endereco_data['localidade'];
                $cidade->estado_id = $estado->id;
                $cidade->store();
            }
            $object->pessoa_endereco_pessoa_cidade_estado_id = $estado->id;
            $object->pessoa_endereco_pessoa_cidade_id = $cidade->id;
            $object->pessoa_endereco_pessoa_rua = $endereco_data['logradouro'];
            $object->pessoa_endereco_pessoa_bairro = $endereco_data['bairro'];
            $object->pessoa_endereco_pessoa_cep = $cep;
             TCombo::reload(self::$formName, 'pessoa_endereco_pessoa_cidade_estado_id', Estado::getIndexedArray('id', 'nome'), true);
                    // -----

              
            TForm::sendData(self::$formName, $object);
            // new TMessage('info', "Endereço encontrado:\n$endereco\nLatitude: $latitude\nLongitude: $longitude");
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
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


    public  function onAddDetailPessoaEnderecoPessoa($param = null) 
    {
        try
        {
            $data = $this->form->getData();

            $errors = [];
            $requiredFields = [];
            $requiredFields[] = ['label'=>"Endereço Principal?", 'name'=>"pessoa_endereco_pessoa_principal", 'class'=>'TRequiredValidator', 'value'=>[]];
            $requiredFields[] = ['label'=>"Estado", 'name'=>"pessoa_endereco_pessoa_cidade_estado_id", 'class'=>'TRequiredValidator', 'value'=>[]];
            $requiredFields[] = ['label'=>"Cidade", 'name'=>"pessoa_endereco_pessoa_cidade_id", 'class'=>'TRequiredValidator', 'value'=>[]];
            foreach($requiredFields as $requiredField)
            {
                try
                {
                    (new $requiredField['class'])->validate($requiredField['label'], $data->{$requiredField['name']}, $requiredField['value']);
                }
                catch(Exception $e)
                {
                    $errors[] = $e->getMessage() . '.';
                }
             }
             if(count($errors) > 0)
             {
                 throw new Exception(implode('<br>', $errors));
             }

            $__row__id = !empty($data->pessoa_endereco_pessoa__row__id) ? $data->pessoa_endereco_pessoa__row__id : 'b'.uniqid();

            TTransaction::open(self::$database);

            $grid_data = new PessoaEndereco();
            $grid_data->__row__id = $__row__id;
            $grid_data->nome = $data->pessoa_endereco_pessoa_nome;
            $grid_data->principal = $data->pessoa_endereco_pessoa_principal;
            $grid_data->cep = $data->pessoa_endereco_pessoa_cep;
            $grid_data->cidade_estado_id = $data->pessoa_endereco_pessoa_cidade_estado_id;
            $grid_data->id = $data->pessoa_endereco_pessoa_id;
            $grid_data->cidade_id = $data->pessoa_endereco_pessoa_cidade_id;
            $grid_data->rua = $data->pessoa_endereco_pessoa_rua;
            $grid_data->numero = $data->pessoa_endereco_pessoa_numero;
            $grid_data->bairro = $data->pessoa_endereco_pessoa_bairro;
            $grid_data->complemento = $data->pessoa_endereco_pessoa_complemento;
            $grid_data->longitude = $data->pessoa_endereco_pessoa_longitude;
            $grid_data->latitude = $data->pessoa_endereco_pessoa_latitude;

            $__row__data = array_merge($grid_data->toArray(), (array)$grid_data->getVirtualData());
            $__row__data['__row__id'] = $__row__id;
            $__row__data['__display__']['nome'] =  $param['pessoa_endereco_pessoa_nome'] ?? null;
            $__row__data['__display__']['principal'] =  $param['pessoa_endereco_pessoa_principal'] ?? null;
            $__row__data['__display__']['cep'] =  $param['pessoa_endereco_pessoa_cep'] ?? null;
            $__row__data['__display__']['cidade_estado_id'] =  $param['pessoa_endereco_pessoa_cidade_estado_id'] ?? null;
            $__row__data['__display__']['id'] =  $param['pessoa_endereco_pessoa_id'] ?? null;
            $__row__data['__display__']['cidade_id'] =  $param['pessoa_endereco_pessoa_cidade_id'] ?? null;
            $__row__data['__display__']['rua'] =  $param['pessoa_endereco_pessoa_rua'] ?? null;
            $__row__data['__display__']['numero'] =  $param['pessoa_endereco_pessoa_numero'] ?? null;
            $__row__data['__display__']['bairro'] =  $param['pessoa_endereco_pessoa_bairro'] ?? null;
            $__row__data['__display__']['complemento'] =  $param['pessoa_endereco_pessoa_complemento'] ?? null;
            $__row__data['__display__']['longitude'] =  $param['pessoa_endereco_pessoa_longitude'] ?? null;
            $__row__data['__display__']['latitude'] =  $param['pessoa_endereco_pessoa_latitude'] ?? null;

            $grid_data->__row__data = base64_encode(serialize((object)$__row__data));
            $row = $this->pessoa_endereco_pessoa_list->addItem($grid_data);
            $row->id = $grid_data->__row__id;

            TDataGrid::replaceRowById('pessoa_endereco_pessoa_list', $grid_data->__row__id, $row);

            TTransaction::close();

            $data = new stdClass;
            $data->pessoa_endereco_pessoa_nome = '';
            $data->pessoa_endereco_pessoa_principal = '';
            $data->pessoa_endereco_pessoa_cep = '';
            $data->pessoa_endereco_pessoa_cidade_estado_id = '';
            $data->pessoa_endereco_pessoa_id = '';
            $data->pessoa_endereco_pessoa_cidade_id = '';
            $data->pessoa_endereco_pessoa_rua = '';
            $data->pessoa_endereco_pessoa_numero = '';
            $data->pessoa_endereco_pessoa_bairro = '';
            $data->pessoa_endereco_pessoa_complemento = '';
            $data->pessoa_endereco_pessoa_longitude = '';
            $data->pessoa_endereco_pessoa_latitude = '';
            $data->pessoa_endereco_pessoa__row__id = '';

            TForm::sendData(self::$formName, $data);
            TScript::create("
               var element = $('#622937d6f9f19');
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
    public static function onEditDetailPessoaEndereco($param = null) 
    {
        try
        {

            $__row__data = unserialize(base64_decode($param['__row__data']));
            $__row__data->__display__ = is_array($__row__data->__display__) ? (object) $__row__data->__display__ : $__row__data->__display__;
            $fireEvents = true;
            $aggregate = false;

            $data = new stdClass;
            $data->pessoa_endereco_pessoa_nome = $__row__data->__display__->nome ?? null;
            $data->pessoa_endereco_pessoa_principal = $__row__data->__display__->principal ?? null;
            $data->pessoa_endereco_pessoa_cep = $__row__data->__display__->cep ?? null;
            $data->pessoa_endereco_pessoa_cidade_estado_id = $__row__data->__display__->cidade_estado_id ?? null;
            $data->pessoa_endereco_pessoa_id = $__row__data->__display__->id ?? null;
            $data->pessoa_endereco_pessoa_cidade_id = $__row__data->__display__->cidade_id ?? null;
            $data->pessoa_endereco_pessoa_rua = $__row__data->__display__->rua ?? null;
            $data->pessoa_endereco_pessoa_numero = $__row__data->__display__->numero ?? null;
            $data->pessoa_endereco_pessoa_bairro = $__row__data->__display__->bairro ?? null;
            $data->pessoa_endereco_pessoa_complemento = $__row__data->__display__->complemento ?? null;
            $data->pessoa_endereco_pessoa_longitude = $__row__data->__display__->longitude ?? null;
            $data->pessoa_endereco_pessoa_latitude = $__row__data->__display__->latitude ?? null;
            $data->pessoa_endereco_pessoa__row__id = $__row__data->__row__id;

            TForm::sendData(self::$formName, $data, $aggregate, $fireEvents);
            TScript::create("
               var element = $('#622937d6f9f19');
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
    public static function onDeleteDetailPessoaEndereco($param = null) 
    {
        try
        {

            $__row__data = unserialize(base64_decode($param['__row__data']));

            $data = new stdClass;
            $data->pessoa_endereco_pessoa_nome = '';
            $data->pessoa_endereco_pessoa_principal = '';
            $data->pessoa_endereco_pessoa_cep = '';
            $data->pessoa_endereco_pessoa_cidade_estado_id = '';
            $data->pessoa_endereco_pessoa_id = '';
            $data->pessoa_endereco_pessoa_cidade_id = '';
            $data->pessoa_endereco_pessoa_rua = '';
            $data->pessoa_endereco_pessoa_numero = '';
            $data->pessoa_endereco_pessoa_bairro = '';
            $data->pessoa_endereco_pessoa_complemento = '';
            $data->pessoa_endereco_pessoa_longitude = '';
            $data->pessoa_endereco_pessoa_latitude = '';
            $data->pessoa_endereco_pessoa__row__id = '';

            TForm::sendData(self::$formName, $data);

            TDataGrid::removeRowById('pessoa_endereco_pessoa_list', $__row__data->__row__id);
            TScript::create("
               var element = $('#622937d6f9f19');
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

            $object = new Pessoa(); // create an empty object 
 
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            
            // $selo_dir = 'app/documentos/selo';

            if($object->login && !$data->id)
            {
                if(Pessoa::where('login', '=', $object->login)->first())
                {
                    throw new Exception('O login informado já existe!');
                }
            }
            elseif($object->login && $data->id)
            {
                if(Pessoa::where('login', '=', $object->login)->where('id', '!=', $data->id)->first())
                {
                    throw new Exception('O login informado já existe!');
                }
            }

            if( $object->senha_cliente)
            {
                $object->senha = md5($data->senha_cliente);
            }
            $object->departamento_unit_id = TSession::getValue('idunit');
            $object->system_users_id = TSession::getValue('userid');
            if ($object->ativo=='F') {
                if ($object->data_desativacao==null) {
                    $object->data_desativacao = date('Y-m-d H:i:s');

                    $codido_email_template_id = EmailTemplate::DESCREDENCIAMENTO; // Código do template correto
                    $emailTemplate = new EmailTemplate($codido_email_template_id);

                    if ($emailTemplate) {

                        $mensagem = $emailTemplate->mensagem;
                        $titulo = $emailTemplate->titulo;
                        $usr = new SystemUsers(TSession::getValue('userid'));
                        $unit = new SystemUnit(TSession::getValue('idunit'));
                        // Substituições básicas
                        $mensagem = str_replace('{nome}', $object->nome, $mensagem);
                        $mensagem = str_replace('{nome_rede}', $object->nome, $mensagem);
                        $mensagem = str_replace('{id}', $object->id, $mensagem);
                        $mensagem = str_replace('{hora_descredenciamento}', $object->data_desativacao, $mensagem);
                        $mensagem = str_replace('{unidade}', $unit->name, $mensagem);
                        $mensagem = str_replace('{usuarioresponsavel}', $usr->name, $mensagem);

                    
                        // Renderiza título e mensagem (se usar render no seu objeto)
                        $titulo = $object->render($titulo);
                        $mensagem = $object->render($mensagem);

                        // --- Criteria final para SystemUsers ---
                        $criteria = new TCriteria;
                        $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
                        $criteria->setProperty('order', 'name');

                        $repo = new TRepository('ViewAprovadoresUnidade');
                        $gestores = $repo->load($criteria, FALSE); // retorna ARs

                        // Monta lista de e-mails (deduplicando e validando)
                        $emails = [];
                        if ($gestores) {
                            foreach ($gestores as $g) {
                                $email = trim((string) $g->email);
                                if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    $emails[$email] = $g->name ?: $email;
                                }
                            }
                        }
                        
                        // Envia e-mail para cada gestor
                        foreach ($emails as $email => $nomeGestor) {
                            $mensagemPersonalizada = str_replace('{nome_gestor}', $nomeGestor, $mensagem);
                            MailService::send($email, $titulo, $mensagemPersonalizada, 'html');
                        }
                        //quero abrir uma transação para o banco do minierp
                        //ler todos os registros pegar o email e enviar para todos os gestores

                        // if ($aprovadores->email) {
                                                     
                        //     MailService::send($pessoa->email, $titulo, $mensagem, 'html');
                        // }
                    }
                }
            } else {
                $object->data_desativacao = NULL;                
            }

            $object->store(); // save the object 

            $this->fireEvents($object);

            $repository = PessoaGrupo::where('pessoa_id', '=', $object->id);
            $repository->delete(); 

            if ($data->grupos) 
            {
                foreach ($data->grupos as $grupos_value) 
                {
                    $pessoa_grupo = new PessoaGrupo;

                    $pessoa_grupo->grupo_pessoa_id = $grupos_value;
                    $pessoa_grupo->pessoa_id = $object->id;
                    $pessoa_grupo->store();
                }
            } 
            // if (!empty($data->selo)) {
            //    $this->saveFile($object, $data, 'selo', $selo_dir);
            // }

            $pessoa_departamento_pessoa_items = $this->storeItems('PessoaDepartamento', 'pessoa_id', $object, $this->fieldList_668d1b5795fc2, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_668d1b5795fc2); 

            $seguimento_pessoa_pessoa_items = $this->storeItems('SeguimentoPessoa', 'pessoa_id', $object, $this->fieldList_666bb0ef7738c, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_666bb0ef7738c); 
            TSession::setValue('idcidade',NULL);
            TSession::setValue('idpessoa',NULL);
            $pessoa_endereco_pessoa_items = $this->storeMasterDetailItems('PessoaEndereco', 'pessoa_id', 'pessoa_endereco_pessoa', $object, $param['pessoa_endereco_pessoa_list___row__data'] ?? [], $this->form, $this->pessoa_endereco_pessoa_list, function($masterObject, $detailObject){ 

                //code here

               if ($detailObject->principal=='T') {
                   TSession::setValue('idcidade',$detailObject->cidade_id);
                   TSession::setValue('idpessoa',$detailObject->pessoa_id);
               }

            }, $this->pessoa_endereco_pessoa_criteria); 

             if (TSession::getValue('idcidade')<>NULL){
                $pessoa = new Pessoa(TSession::getValue('idpessoa'));
                $pessoa->cidade_id = TSession::getValue('idcidade');
                $pessoa->store();                 
             }        

            $pessoa_contato_pessoa_items = $this->storeItems('PessoaContato', 'pessoa_id', $object, $this->detalhe_de_contatos, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_detalhe_de_contatos); 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 


            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            if($data->from_seek_form_name)
            {
                $obj = new stdClass();
                $obj->cliente_id = $object->id;
                $obj->cliente_nome  = $object->nome;

                TForm::sendData($data->from_seek_form_name, $obj);
            }
            elseif($data->from_page == 'PedidoVendaForm' && $data->from_page_field == 'transportadora_id')
            {
                $obj = new stdClass();
                $obj->transportadora_id = $object->id;

                $criteria = new TCriteria();

                $filterVar = GrupoPessoa::TRANSPORTADORA;
                $criteria->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '{$filterVar}')"));

                TDBCombo::reloadFromModel(PedidoVendaForm::getFormName(), $data->from_page_field, self::$database, 'Pessoa', 'id', 'nome', 'nome', $criteria);

                TForm::sendData(PedidoVendaForm::getFormName(), $obj);
            }
            else
            {
                TApplication::loadPage('PessoaList', 'onShow');
            }

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle'); 

                        TScript::create("Template.closeRightPanel();");
            TForm::sendData(self::$formName, (object)['id' => $object->id]);

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

                $object = new Pessoa($key); // instantiates the Active Record 
                     
               

                $object->grupos = PessoaGrupo::where('pessoa_id', '=', $object->id)->getIndexedArray('grupo_pessoa_id', 'grupo_pessoa_id');

                $this->fieldList_668d1b5795fc2_items = $this->loadItems('PessoaDepartamento', 'pessoa_id', $object, $this->fieldList_668d1b5795fc2, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_668d1b5795fc2); 

                $this->fieldList_666bb0ef7738c_items = $this->loadItems('SeguimentoPessoa', 'pessoa_id', $object, $this->fieldList_666bb0ef7738c, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_666bb0ef7738c); 

                $pessoa_endereco_pessoa_items = $this->loadMasterDetailItems('PessoaEndereco', 'pessoa_id', 'pessoa_endereco_pessoa', $object, $this->form, $this->pessoa_endereco_pessoa_list, $this->pessoa_endereco_pessoa_criteria, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                    $objectItems->pessoa_endereco_pessoa_cidade_estado_id = null;
                    if(isset($detailObject->cidade->estado_id) && $detailObject->cidade->estado_id)
                    {
                        $objectItems->__display__->cidade_estado_id = $detailObject->cidade->estado_id;
                    }

                    $objectItems->pessoa_endereco_pessoa_cidade_id = null;
                    if(isset($detailObject->cidade_id) && $detailObject->cidade_id)
                    {
                        $objectItems->__display__->cidade_id = $detailObject->cidade_id;
                    }

                }); 

                $this->detalhe_de_contatos_items = $this->loadItems('PessoaContato', 'pessoa_id', $object, $this->detalhe_de_contatos, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_detalhe_de_contatos); 

                $this->form->setData($object); // fill the form 

                $this->fireEvents($object);

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

        $this->detalhe_de_contatos->addHeader();
        $this->detalhe_de_contatos->addDetail($this->default_item_detalhe_de_contatos);

        $this->detalhe_de_contatos->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_668d1b5795fc2->addHeader();
        $this->fieldList_668d1b5795fc2->addDetail($this->default_item_fieldList_668d1b5795fc2);

        $this->fieldList_668d1b5795fc2->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_666bb0ef7738c->addHeader();
        $this->fieldList_666bb0ef7738c->addDetail($this->default_item_fieldList_666bb0ef7738c);

        $this->fieldList_666bb0ef7738c->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    }

    public function onShow($param = null)
    {
        $this->detalhe_de_contatos->addHeader();
        $this->detalhe_de_contatos->addDetail($this->default_item_detalhe_de_contatos);

        $this->detalhe_de_contatos->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_668d1b5795fc2->addHeader();
        $this->fieldList_668d1b5795fc2->addDetail($this->default_item_fieldList_668d1b5795fc2);

        $this->fieldList_668d1b5795fc2->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_666bb0ef7738c->addHeader();
        $this->fieldList_666bb0ef7738c->addDetail($this->default_item_fieldList_666bb0ef7738c);

        $this->fieldList_666bb0ef7738c->addCloneAction(null, 'fas:plus #69aa46', "Clonar");
        
    } 

    public function fireEvents( $object )
    {
        $obj = new stdClass;
        if(is_object($object) && get_class($object) == 'stdClass')
        {
            if(isset($object->pessoa_endereco_pessoa_cidade_estado_id))
            {
                $value = $object->pessoa_endereco_pessoa_cidade_estado_id;

                $obj->pessoa_endereco_pessoa_cidade_estado_id = $value;
            }
            if(isset($object->pessoa_endereco_pessoa_cidade_id))
            {
                $value = $object->pessoa_endereco_pessoa_cidade_id;

                $obj->pessoa_endereco_pessoa_cidade_id = $value;
            }
        }
        elseif(is_object($object))
        {
            if(isset($object->pessoa_endereco->pessoa->cidade->estado_id))
            {
                $value = $object->pessoa_endereco->pessoa->cidade->estado_id;

                $obj->pessoa_endereco_pessoa_cidade_estado_id = $value;
            }
            if(isset($object->pessoa_endereco->pessoa->cidade_id))
            {
                $value = $object->pessoa_endereco->pessoa->cidade_id;

                $obj->pessoa_endereco_pessoa_cidade_id = $value;
            }
        }
        TForm::sendData(self::$formName, $obj);
    }  

    public static function getFormName()
    {
        return self::$formName;
    }

    public function onShowFromSeek($param = null)
    {
        // fechamos a seek
        TWindow::closeWindow();

        $from_seek_form_name = $this->form->getField('from_seek_form_name');
        $from_seek_form_name->setValue($param['_form_name']);
    }

    public function onShowNovaPessoa($param = null)
    {
        $from_page = $this->form->getField('from_page');
        $from_page->setValue($param['from_page']);

        $from_page_field = $this->form->getField('from_page_field');
        $from_page_field->setValue($param['from_page_field']);
    }

}

