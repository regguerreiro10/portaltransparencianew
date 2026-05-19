<?php

class SegurosForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Seguros';
    private static $primaryKey = 'id';
    private static $formName = 'form_SegurosForm';

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
        $this->form->setFormTitle("Cadastro de seguros");

       $criteria_saldo_entidade_contrato_id = new TCriteria();
        $filterVar = TSession::getValue('entidade');
        $criteria_saldo_entidade_contrato_id->add(new TFilter('entidade_id', '=', $filterVar)); 
        
        $criteria_tipo_seguro_id = new TCriteria();

        $id = new TEntry('id');
        $saldo_entidade_contrato_id = new TDBCombo('saldo_entidade_contrato_id', 'minierp', 'SaldoEntidadeContrato', 'id', '{id} - {historico} - R${valor_saldo}','id asc' , $criteria_saldo_entidade_contrato_id );
        $tipo_seguro_id = new TDBCombo('tipo_seguro_id', 'minierp', 'TipoSeguro', 'id', '{descricao}','id asc' , $criteria_tipo_seguro_id );
        $data_inicio = new TDate('data_inicio');
        $data_final = new TDate('data_final');
        $numero_apolice = new TEntry('numero_apolice');
        $valor_cobertura = new TNumeric('valor_cobertura', '2', ',', '.' );
        $obs = new TText('obs');
        $anexos_seguros_seguros_id = new THidden('anexos_seguros_seguros_id[]');
        $anexos_seguros_seguros___row__id = new THidden('anexos_seguros_seguros___row__id[]');
        $anexos_seguros_seguros___row__data = new THidden('anexos_seguros_seguros___row__data[]');
        $anexos_seguros_seguros_caminho = new TFile('anexos_seguros_seguros_caminho[]');
        $this->fieldList_68cc0454fcfc5 = new TFieldList();

        $this->fieldList_68cc0454fcfc5->addField(null, $anexos_seguros_seguros_id, []);
        $this->fieldList_68cc0454fcfc5->addField(null, $anexos_seguros_seguros___row__id, ['uniqid' => true]);
        $this->fieldList_68cc0454fcfc5->addField(null, $anexos_seguros_seguros___row__data, []);
        $this->fieldList_68cc0454fcfc5->addField(new TLabel("Arquivo:", null, '14px', null), $anexos_seguros_seguros_caminho, ['width' => '100%']);

        $this->fieldList_68cc0454fcfc5->width = '100%';
        $this->fieldList_68cc0454fcfc5->setFieldPrefix('anexos_seguros_seguros');
        $this->fieldList_68cc0454fcfc5->name = 'fieldList_68cc0454fcfc5';

        $this->criteria_fieldList_68cc0454fcfc5 = new TCriteria();
        $this->default_item_fieldList_68cc0454fcfc5 = new stdClass();

        $this->form->addField($anexos_seguros_seguros_id);
        $this->form->addField($anexos_seguros_seguros___row__id);
        $this->form->addField($anexos_seguros_seguros___row__data);
        $this->form->addField($anexos_seguros_seguros_caminho);

        $this->fieldList_68cc0454fcfc5->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $tipo_seguro_id->addValidation("Tipo Seguro", new TRequiredValidator()); 
        $numero_apolice->addValidation("Número Apólice", new TRequiredValidator());
        // $anexos_seguros_seguros_caminho->addValidation("Arquivo/Anexos Seguros", new TRequiredValidator()); 
        $saldo_entidade_contrato_id->addValidation("Saldo entidade contrato", new TRequiredValidator());
        $data_inicio->addValidation("Data Inicial", new TRequiredValidator());
        $data_final->addValidation("Data Final", new TRequiredValidator());
        $valor_cobertura->addValidation("Valor cobertura", new TRequiredValidator());

        $id->setEditable(false);
        $numero_apolice->setMaxLength(100);
        $anexos_seguros_seguros_caminho->enableFileHandling();
        $tipo_seguro_id->enableSearch();
        $saldo_entidade_contrato_id->enableSearch();

        $data_final->setMask('dd/mm/yyyy');
        $data_inicio->setMask('dd/mm/yyyy');

        $data_final->setDatabaseMask('yyyy-mm-dd');
        $data_inicio->setDatabaseMask('yyyy-mm-dd');

        $id->setSize(100);
        $data_final->setSize(110);
        $obs->setSize('100%', 70);
        $data_inicio->setSize(110);
        $tipo_seguro_id->setSize('100%');
        $numero_apolice->setSize('100%');
        $valor_cobertura->setSize('100%');
        $saldo_entidade_contrato_id->setSize('100%');
        $anexos_seguros_seguros_caminho->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Saldo entidade contrato (id, histórico e valor):", '#FF0000', '14px', null, '100%'),$saldo_entidade_contrato_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Tipo seguro:", '#FF0000', '14px', null, '100%'),$tipo_seguro_id],[new TLabel("Data inicio:", '#FF0000', '14px', null, '100%'),$data_inicio],[new TLabel("Data final:", '#FF0000', '14px', null, '100%'),$data_final]);
        $row2->layout = ['col-sm-6',' col-sm-3',' col-sm-3'];

        $row3 = $this->form->addFields([new TLabel("Número apólice:", '#FF0000', '14px', null, '100%'),$numero_apolice],[new TLabel("Valor cobertura:", '#FF0000', '14px', null, '100%'),$valor_cobertura]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row4->layout = [' col-sm-12'];

        $row5 = $this->form->addFields([$this->fieldList_68cc0454fcfc5]);
        $row5->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['SegurosList', 'onShow']), 'fas:arrow-left #000000');
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

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Seguros(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $anexos_seguros_seguros_caminho_dir = 'app/documentos/seguros'; 

            $object->store(); // save the object 

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            $anexos_seguros_seguros_items = $this->storeItems('AnexosSeguros', 'seguros_id', $object, $this->fieldList_68cc0454fcfc5, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_68cc0454fcfc5); 
            if(!empty($anexos_seguros_seguros_items))
            {
                foreach ($anexos_seguros_seguros_items as $item)
                {
                    $dataFile = new stdClass();
                    $dataFile->caminho = $item->caminho;
                    $this->saveFile($item, $dataFile, 'caminho', $anexos_seguros_seguros_caminho_dir);
                }
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('SegurosList', 'onShow', $loadPageParam); 

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

                $object = new Seguros($key); // instantiates the Active Record 

                $this->fieldList_68cc0454fcfc5_items = $this->loadItems('AnexosSeguros', 'seguros_id', $object, $this->fieldList_68cc0454fcfc5, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_68cc0454fcfc5); 

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

        $this->fieldList_68cc0454fcfc5->addHeader();
        $this->fieldList_68cc0454fcfc5->addDetail($this->default_item_fieldList_68cc0454fcfc5);

        $this->fieldList_68cc0454fcfc5->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    }

    public function onShow($param = null)
    {
        $this->fieldList_68cc0454fcfc5->addHeader();
        $this->fieldList_68cc0454fcfc5->addDetail($this->default_item_fieldList_68cc0454fcfc5);

        $this->fieldList_68cc0454fcfc5->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

