<?php

//<fileHeader>

//</fileHeader>

class ModeloForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Modelo';
    private static $primaryKey = 'id';
    private static $formName = 'form_ModeloForm';

    //<classProperties>

    //</classProperties>

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
        $this->form->setFormTitle("Cadastro de modelo");

        $criteria_marca_id = new TCriteria();
        $criteria_especie_id = new TCriteria();
        $criteria_familia_id = new TCriteria();
        $criteria_propriedade_id = new TCriteria();
        $criteria_tipo_veiculo_id = new TCriteria();
        $criteria_tipo_combustivel_id = new TCriteria();

        //<onBeginPageCreation>

        //</onBeginPageCreation>

        $id = new TEntry('id');
        $descricao = new TEntry('descricao');

        $marca_id = new TDBCombo('marca_id', 'minierp', 'Marca', 'id', '{descricao}','id asc' , $criteria_marca_id );
        $marca_id->configureNoResultsQuickRegister(new TAction(['MarcaForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $marca_id->setNoResultsMessage("Nenhuma marca encontrada. Clique no cadastrar");

        $especie_id = new TDBCombo('especie_id', 'minierp', 'Especie', 'id', '{descricao}','descricao asc' , $criteria_especie_id );
        $especie_id->configureNoResultsQuickRegister(new TAction(['EspecieForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $especie_id->setNoResultsMessage("Nenhuma especie encontrada. Clique no cadastrar");

        $familia_id = new TDBCombo('familia_id', 'minierp', 'Familia', 'id', '{descricao}','descricao asc' , $criteria_familia_id );
        $familia_id->configureNoResultsQuickRegister(new TAction(['FamiliaForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $familia_id->setNoResultsMessage("Nenhuma familia encontrada. Clique no cadastrar");

        $propriedade_id = new TDBCombo('propriedade_id', 'minierp', 'Propriedade', 'id', '{descricao}','descricao asc' , $criteria_propriedade_id );
        $propriedade_id->configureNoResultsQuickRegister(new TAction(['PropriedadeForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $propriedade_id->setNoResultsMessage("Nenhuma propriedade encontrada. Clique no cadastrar");


        $tipo_veiculo_id = new TDBCombo('tipo_veiculo_id', 'minierp', 'TipoVeiculo', 'id', '{descricao}','descricao asc' , $criteria_tipo_veiculo_id );
        $tipo_veiculo_id->configureNoResultsQuickRegister(new TAction(['TipoVeiculoForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $tipo_veiculo_id->setNoResultsMessage("Nenhum tipo de veiculo encontrado. Clique no cadastrar");

        $tipo_combustivel_id = new TDBCombo('tipo_combustivel_id', 'minierp', 'TipoCombustivel', 'id', '{descricao}','descricao asc' , $criteria_tipo_combustivel_id );
        $tipo_combustivel_id->configureNoResultsQuickRegister(new TAction(['TipoCombustivelForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $tipo_combustivel_id->setNoResultsMessage("Nenhum tipo de combustivel encontrado. Clique no cadastrar");

        $modelo_ano_modelo_id = new THidden('modelo_ano_modelo_id[]');
        $modelo_ano_modelo___row__id = new THidden('modelo_ano_modelo___row__id[]');
        $modelo_ano_modelo___row__data = new THidden('modelo_ano_modelo___row__data[]');
        $modelo_ano_modelo_ano = new TEntry('modelo_ano_modelo_ano[]');
        $modelo_ano_modelo_preco = new TNumeric('modelo_ano_modelo_preco[]', '2', ',', '.' );
        $this->fieldList_68619cac3f7ab = new TFieldList();

        $this->fieldList_68619cac3f7ab->addField(null, $modelo_ano_modelo_id, []);
        $this->fieldList_68619cac3f7ab->addField(null, $modelo_ano_modelo___row__id, ['uniqid' => true]);
        $this->fieldList_68619cac3f7ab->addField(null, $modelo_ano_modelo___row__data, []);
        $this->fieldList_68619cac3f7ab->addField(new TLabel("Ano *", '#FF0000', '14px', null), $modelo_ano_modelo_ano, ['width' => '50%']);
        $this->fieldList_68619cac3f7ab->addField(new TLabel("Preço *", '#FF0000', '14px', null), $modelo_ano_modelo_preco, ['width' => '50%']);

        $this->fieldList_68619cac3f7ab->width = '100%';
        $this->fieldList_68619cac3f7ab->setFieldPrefix('modelo_ano_modelo');
        $this->fieldList_68619cac3f7ab->name = 'fieldList_68619cac3f7ab';

        $this->criteria_fieldList_68619cac3f7ab = new TCriteria();
        $this->default_item_fieldList_68619cac3f7ab = new stdClass();

        $this->form->addField($modelo_ano_modelo_id);
        $this->form->addField($modelo_ano_modelo___row__id);
        $this->form->addField($modelo_ano_modelo___row__data);
        $this->form->addField($modelo_ano_modelo_ano);
        $this->form->addField($modelo_ano_modelo_preco);

        $this->fieldList_68619cac3f7ab->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $modelo_ano_modelo_ano->addValidation("Ano modelo", new TRequiredListValidator()); 
        $modelo_ano_modelo_preco->addValidation("Valor do modelo neste ano", new TRequiredListValidator()); 

        $id->setEditable(false);
        $descricao->setMaxLength(500);
        $modelo_ano_modelo_ano->setMask('9999');
        $marca_id->setValue('NULL');
        $descricao->setValue('NULL');

        $marca_id->enableSearch();
        $especie_id->enableSearch();
        $familia_id->enableSearch();
        $propriedade_id->enableSearch();
        $tipo_veiculo_id->enableSearch();
        $tipo_combustivel_id->enableSearch();

        $id->setSize(100);
        $marca_id->setSize('100%');
        $descricao->setSize('100%');
        $especie_id->setSize('100%');
        $familia_id->setSize('100%');
        $propriedade_id->setSize('100%');
        $tipo_veiculo_id->setSize('100%');
        $tipo_combustivel_id->setSize('100%');
        $modelo_ano_modelo_ano->setSize('100%');
        $modelo_ano_modelo_preco->setSize('100%');

        //<onBeforeAddFieldsToForm>

        //</onBeforeAddFieldsToForm>
        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Marca: *", '#FF0000', '14px', null, '100%'),$marca_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Descrição: *", '#FF0000', '14px', null, '100%'),$descricao],[new TLabel("Espécie: *", '#FF0000', '14px', null, '100%'),$especie_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Família: *", '#FF0000', '14px', null, '100%'),$familia_id],[new TLabel("Propriedade/Classificação: *", '#FF0000', '14px', null, '100%'),$propriedade_id]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Tipo: *", '#FF0000', '14px', null, '100%'),$tipo_veiculo_id],[new TLabel("Tipo de Combustivel: *", '#FF0000', '14px', null, '100%'),$tipo_combustivel_id]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields([new TFormSeparator("Modelo/Ano/Valor", '#333', '18', '#eee')]);
        $row5->layout = [' col-sm-12'];

        $row6 = $this->form->addFields([$this->fieldList_68619cac3f7ab]);
        $row6->layout = [' col-sm-12'];

        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['ModeloList', 'onShow']), 'fas:arrow-left #000000');
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

    }

//<generated-FormAction-onSave>
    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Modelo(); // create an empty object //</blockLine>

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            //</beforeStoreAutoCode> //</blockLine>

            $object->store(); // save the object //</blockLine>

            //</afterStoreAutoCode> //</blockLine>
 //<generatedAutoCode>

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

//</generatedAutoCode>

    //<fieldList-2702650-22193800> //</hideLine>
            $modelo_ano_modelo_items = $this->storeItems('ModeloAno', 'modelo_id', $object, $this->fieldList_68619cac3f7ab, function($masterObject, $detailObject){ //</blockLine>

                //code here

                //</autoCode>
            }, $this->criteria_fieldList_68619cac3f7ab); //</blockLine>
    //</hideLine> //</fieldList-2702650-22193800>

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; //</blockLine>




            //</messageAutoCode> //</blockLine>
//<generatedAutoCode>
            if (TSession::getValue('formVeiculo') =='SIM') {
                TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
                //TApplication::loadPage('VeiculoForm', 'onShow', $loadPageParam);
                $modeloanos = ModeloAno::where('modelo_id', '=', $object->id)
                                    ->where('ano', '=', TSession::getValue('anoveiculo'))
                                    ->load();

                $objectpro = new stdClass();
                $objectpro->especie_id = $object->especie_id;
                $objectpro->familia_id = $object->familia_id;
                $objectpro->propriedade_id    = $object->propriedade_id;
                $objectpro->tipo_veiculo_id         = $object->tipo_veiculo_id;
                $objectpro->tipo_combustivel_id = $object->tipo_combustivel_id;
                $objectpro->modelo_id=$object->id;
                $objectpro->marca_id=$object->marca_id;
                if ($modeloanos) {
                    $objectpro->valor_tabela_fipe = $modeloanos[0]->preco;
                } 
                TForm::sendData('form_VeiculosForm', $objectpro);
    


            } else {
               TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
               TApplication::loadPage('ModeloList', 'onShow', $loadPageParam);

            }
            TTransaction::close(); // close the transaction

            $this->form->setData($data); // fill form data
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
            if (isset($param['modelo_id'])) {
                $param['key'] = $param['modelo_id'];
                TSession::setValue('formVeiculo',null);
                TSession::setValue('formVeiculo','SIM');
                TSession::setValue('anoveiculo',null);
                TSession::setValue('anoveiculo',$param['anof']);
            }
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Modelo($key); // instantiates the Active Record //</blockLine>

                //</beforeSetDataAutoCode> //</blockLine> 

    //<fieldList-2702650-22193800> //</hideLine>
                $this->fieldList_68619cac3f7ab_items = $this->loadItems('ModeloAno', 'modelo_id', $object, $this->fieldList_68619cac3f7ab, function($masterObject, $detailObject, $objectItems){ //</blockLine>

                    //code here

                    //</autoCode>
                }, $this->criteria_fieldList_68619cac3f7ab); //</blockLine>
    //</hideLine> //</fieldList-2702650-22193800>

                $this->form->setData($object); // fill the form //</blockLine>

                //</afterSetDataAutoCode> //</blockLine>
//<generatedAutoCode>

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

        $this->fieldList_68619cac3f7ab->addHeader();
        $this->fieldList_68619cac3f7ab->addDetail($this->default_item_fieldList_68619cac3f7ab);

        $this->fieldList_68619cac3f7ab->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        //<onFormClear>

        //</onFormClear>

    }

    public function onShow($param = null)
    {
        $this->fieldList_68619cac3f7ab->addHeader();
        $this->fieldList_68619cac3f7ab->addDetail($this->default_item_fieldList_68619cac3f7ab);

        $this->fieldList_68619cac3f7ab->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        //<onShow>

        //</onShow>
    } 

    public static function getFormName()
    {
        return self::$formName;
    }
     public function onSetProject($param = null)
    {
       $obj = $param;
       TSession::setValue('formDados', $obj); // fill the form )
       TSession::setValue('formVeiculo', 'SIM');
       $this->onShow();
    } 

    //</hideLine> <addUserFunctionsCode/>

    //<userCustomFunctions>

    //</userCustomFunctions>

}