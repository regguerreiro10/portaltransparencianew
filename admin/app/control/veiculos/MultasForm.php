<?php

class MultasForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Multas';
    private static $primaryKey = 'id';
    private static $formName = 'form_MultasForm';

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
        $this->form->setFormTitle("Cadastro de multas");

        $criteria_veiculos_id = new TCriteria();
        $criteria_condutor_id = new TCriteria();
        $criteria_system_unit_id = new TCriteria();
        $criteria_departamento_unit_id = new TCriteria();
        $criteria_status_multas_id = new TCriteria();

        $filterVar = TSession::getValue('idunit');
        $criteria_veiculos_id->add(new TFilter('system_unit_id', '=', $filterVar)); 
        $filterVar = GrupoPessoa::CONDUTOR;
        $criteria_condutor_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '{$filterVar}')")); 
        $filterVar = TSession::getValue('idunit');
        $criteria_system_unit_id->add(new TFilter('id', '=', $filterVar)); 
        $filterVar = TSession::getValue('idunit');
        $criteria_departamento_unit_id->add(new TFilter('system_unit_id', '=', $filterVar)); 

        $id = new TEntry('id');
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{id}','id asc' , $criteria_veiculos_id );
        $condutor_id = new TDBCombo('condutor_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_condutor_id );
        $system_unit_id = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_system_unit_id );
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_departamento_unit_id );
        $status_multas_id = new TDBCombo('status_multas_id', 'minierp', 'StatusMultas', 'id', '{descricao}','descricao asc' , $criteria_status_multas_id );
        //    $status_multas_id->configureNoResultsCreateButton(new TAction(['StatusMultasForm', 'onShow']), "Cadastrar", "fas:plus #69AA46", "btn-default");
        // $status_multas_id->setNoResultsMessage("Status não encontrado");
        // $status_multas_id->setNoResultsMessage("Status não encontrado");

        $numero_alt = new TEntry('numero_alt');
        $enquadramento = new TEntry('enquadramento');
        $descricao = new TEntry('descricao');
        $data_infracao = new TDateTime('data_infracao');
        $local_infracao = new TEntry('local_infracao');
        $orgao_autuador = new TEntry('orgao_autuador');
        $pontos_cnh = new TEntry('pontos_cnh');
        $valor_original = new TNumeric('valor_original', '2', ',', '.' );
        $valor_desconto = new TNumeric('valor_desconto', '2', ',', '.' );
        $parcela = new TEntry('parcela');
        $data_vencimento = new TDate('data_vencimento');
        $data_pagamento = new TDateTime('data_pagamento');
        $valor_pago = new TNumeric('valor_pago', '2', ',', '.' );
        $motivo_cancelamento = new TEntry('motivo_cancelamento');
        $obs = new TEntry('obs');
        $multas_anexos_multas_id = new THidden('multas_anexos_multas_id[]');
        $multas_anexos_multas___row__id = new THidden('multas_anexos_multas___row__id[]');
        $multas_anexos_multas___row__data = new THidden('multas_anexos_multas___row__data[]');
        $multas_anexos_multas_arquivo = new TFile('multas_anexos_multas_arquivo[]');
        $this->fieldList_68d430bc8ed5c = new TFieldList();

        $this->fieldList_68d430bc8ed5c->addField(null, $multas_anexos_multas_id, []);
        $this->fieldList_68d430bc8ed5c->addField(null, $multas_anexos_multas___row__id, ['uniqid' => true]);
        $this->fieldList_68d430bc8ed5c->addField(null, $multas_anexos_multas___row__data, []);
        $this->fieldList_68d430bc8ed5c->addField(new TLabel("Arquivo", null, '14px', null), $multas_anexos_multas_arquivo, ['width' => '100%']);

        $this->fieldList_68d430bc8ed5c->width = '100%';
        $this->fieldList_68d430bc8ed5c->setFieldPrefix('multas_anexos_multas');
        $this->fieldList_68d430bc8ed5c->name = 'fieldList_68d430bc8ed5c';

        $this->criteria_fieldList_68d430bc8ed5c = new TCriteria();
        $this->default_item_fieldList_68d430bc8ed5c = new stdClass();

        $this->form->addField($multas_anexos_multas_id);
        $this->form->addField($multas_anexos_multas___row__id);
        $this->form->addField($multas_anexos_multas___row__data);
        $this->form->addField($multas_anexos_multas_arquivo);

        $this->fieldList_68d430bc8ed5c->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $veiculos_id->addValidation("Veiculo", new TRequiredValidator()); 
        $condutor_id->addValidation("Condutor", new TRequiredValidator()); 
        $system_unit_id->addValidation("Unidade", new TRequiredValidator()); 
        $departamento_unit_id->addValidation("Unidade / Dep / Secretaria", new TRequiredValidator()); 
        $status_multas_id->addValidation("Status", new TRequiredValidator()); 
        $valor_original->addValidation("Valor", new TRequiredValidator()); 
        $data_vencimento->addValidation("Data vencimento", new TRequiredValidator()); 

        $id->setEditable(false);
        $multas_anexos_multas_arquivo->enableFileHandling();
        $data_vencimento->setMask('dd/mm/yyyy');
        $data_infracao->setMask('dd/mm/yyyy hh:ii');
        $data_pagamento->setMask('dd/mm/yyyy hh:ii');

        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_infracao->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_pagamento->setDatabaseMask('yyyy-mm-dd hh:ii');

        $veiculos_id->enableSearch();
        $condutor_id->enableSearch();
        $system_unit_id->enableSearch();
        $status_multas_id->enableSearch();
        $departamento_unit_id->enableSearch();

        $numero_alt->setMaxLength(50);
        $descricao->setMaxLength(255);
        $enquadramento->setMaxLength(100);
        $local_infracao->setMaxLength(200);
        $orgao_autuador->setMaxLength(120);
        $motivo_cancelamento->setMaxLength(255);

        $id->setSize(100);
        $obs->setSize('100%');
        $parcela->setSize('100%');
        $descricao->setSize('100%');
        $numero_alt->setSize('100%');
        $data_infracao->setSize(150);
        $pontos_cnh->setSize('100%');
        $valor_pago->setSize('100%');
        $veiculos_id->setSize('100%');
        $condutor_id->setSize('100%');
        $data_pagamento->setSize(150);
        $data_vencimento->setSize(110);
        $enquadramento->setSize('100%');
        $system_unit_id->setSize('100%');
        $local_infracao->setSize('100%');
        $orgao_autuador->setSize('100%');
        $valor_original->setSize('100%');
        $valor_desconto->setSize('100%');
        $status_multas_id->setSize('100%');
        $motivo_cancelamento->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $multas_anexos_multas_arquivo->setSize('100%');


        $this->form->appendPage("Aba 68d2e50741727");

        $this->form->addFields([new THidden('current_tab')]);
        $this->form->setTabFunction("$('[name=current_tab]').val($(this).attr('data-current_page'));");

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel("Veiculos: *", '#FF0000', '14px', null)],[$veiculos_id]);
        $row3 = $this->form->addFields([new TLabel("Condutor: *", '#FF0000', '14px', null)],[$condutor_id]);
        $row4 = $this->form->addFields([new TLabel("Unidade: *", '#FF0000', '14px', null)],[$system_unit_id]);
        $row5 = $this->form->addFields([new TLabel("Unidade / Dep / Secretaria: *", '#FF0000', '14px', null)],[$departamento_unit_id]);
        $row6 = $this->form->addFields([new TLabel("Status: *", '#FF0000', '14px', null)],[$status_multas_id]);
        $row7 = $this->form->addFields([new TLabel("Numero alt:", null, '14px', null)],[$numero_alt]);
        $row8 = $this->form->addFields([new TLabel("Enquadramento:", null, '14px', null)],[$enquadramento]);
        $row9 = $this->form->addFields([new TLabel("Descricao:", null, '14px', null)],[$descricao]);
        $row10 = $this->form->addFields([new TLabel("Data infracao:*", '#FF0000', '14px', null)],[$data_infracao]);
        $row11 = $this->form->addFields([new TLabel("Local infracao:", null, '14px', null)],[$local_infracao]);
        $row12 = $this->form->addFields([new TLabel("Orgao autuador:", null, '14px', null)],[$orgao_autuador]);
        $row13 = $this->form->addFields([new TLabel("Pontos cnh:", null, '14px', null)],[$pontos_cnh]);
        $row14 = $this->form->addFields([new TLabel("Valor original: *", '#FF0000', '14px', null)],[$valor_original]);
        $row15 = $this->form->addFields([new TLabel("Valor desconto:", null, '14px', null)],[$valor_desconto]);
        $row16 = $this->form->addFields([new TLabel("Parcela:", null, '14px', null)],[$parcela]);
        $row17 = $this->form->addFields([new TLabel("Data vencimento: *", '#FF0000', '14px', null)],[$data_vencimento]);
        $row18 = $this->form->addFields([new TLabel("Data pagamento:", null, '14px', null)],[$data_pagamento]);
        $row19 = $this->form->addFields([new TLabel("Valor pago:", null, '14px', null)],[$valor_pago]);
        $row20 = $this->form->addFields([new TLabel("Motivo cancelamento:", null, '14px', null)],[$motivo_cancelamento]);
        $row21 = $this->form->addFields([new TLabel("Obs:", null, '14px', null)],[$obs]);
        $row22 = $this->form->addFields([$this->fieldList_68d430bc8ed5c]);
        $row22->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['MultasList', 'onShow']), 'fas:arrow-left #000000');
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

        $style = new TStyle('right-panel > .container-part[page-name=MultasForm]');
        $style->width = '80% !important';   
        $style->show(true);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Multas(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $multas_anexos_multas_arquivo_dir = 'app/documentos/multas';  

            $object->system_users_id = TSession::getValue('userid');
            $object->store(); // save the object 

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            $multas_anexos_multas_items = $this->storeItems('MultasAnexos', 'multas_id', $object, $this->fieldList_68d430bc8ed5c, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_68d430bc8ed5c); 
            if(!empty($multas_anexos_multas_items))
            {
                foreach ($multas_anexos_multas_items as $item)
                {
                    $dataFile = new stdClass();
                    $dataFile->arquivo = $item->arquivo;
                    $this->saveFile($item, $dataFile, 'arquivo', $multas_anexos_multas_arquivo_dir);
                }
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('MultasList', 'onShow', $loadPageParam); 

                        TScript::create("Template.closeRightPanel();");
            TForm::sendData(self::$formName, (object)['id' => $object->id]);

        }
        catch (Exception $e) // in case of exception
        {

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

                $object = new Multas($key); // instantiates the Active Record 

                $this->fieldList_68d430bc8ed5c_items = $this->loadItems('MultasAnexos', 'multas_id', $object, $this->fieldList_68d430bc8ed5c, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_68d430bc8ed5c); 

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

        $this->fieldList_68d430bc8ed5c->addHeader();
        $this->fieldList_68d430bc8ed5c->addDetail($this->default_item_fieldList_68d430bc8ed5c);

        $this->fieldList_68d430bc8ed5c->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    }

    public function onShow($param = null)
    {
        $this->fieldList_68d430bc8ed5c->addHeader();
        $this->fieldList_68d430bc8ed5c->addDetail($this->default_item_fieldList_68d430bc8ed5c);

        $this->fieldList_68d430bc8ed5c->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

