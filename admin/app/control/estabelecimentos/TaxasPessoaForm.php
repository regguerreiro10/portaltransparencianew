<?php

class TaxasPessoaForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'TaxasPessoa';
    private static $primaryKey = 'id';
    private static $formName = 'form_TaxasPessoaForm';

    // private $pessoa_optante;
    // private $iss_servico;

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
        $this->form->setFormTitle("Cadastro de taxas pessoa");

        $criteria_entidade_id = new TCriteria();
        $criteria_system_unit_id = new TCriteria();


        $entidade_id = new TDBCombo('entidade_id', 'minierp', 'Entidade', 'id', '{nome}','nome asc' , $criteria_entidade_id );
        $id = new THidden('id');
        $system_unit_id = new TCombo('system_unit_id');
        $taxaadm = new TNumeric('taxaadm', '2', ',', '.' );
        $taxabancaria = new TNumeric('taxabancaria', '2', ',', '.' );
        // $taxabancaria = 'R$ ' . number_format($taxabancaria, 2, ',', '.');
        $taxaantecipacao = new TNumeric('taxaantecipacao', '2', ',', '.' );
        $taxacontrato = new TNumeric('taxacontrato', '2', ',', '.' );
        // $taxadesconto = new TNumeric('taxadesconto', '2', ',', '.' );
        $ir = new TNumeric('ir', '2', ',', '.' );
        $csll = new TNumeric('csll', '2', ',', '.' );
        $cofins = new TNumeric('cofins', '2', ',', '.' );
        $pis = new TNumeric('pis', '2', ',', '.' );
        $pessoa_optante = new TCheckButton('optante');
        $ir_servico = new TNumeric('ir_servico', '2', ',', '.' );
        $csll_servico = new TNumeric('csll_servico', '2', ',', '.' );
        $cofins_servico = new TNumeric('cofins_servico', '2', ',', '.' );
        $pis_servico = new TNumeric('pis_servico', '2', ',', '.' );
        $iss_servico = new TNumeric('iss_servico', '2', ',', '.' );
        $pessoa_optante->setChangeAction(new TAction([$this,'onChangePessoaOptante']));
        $entidade_id->setChangeAction(new TAction([$this,'onChangeentidade_id']));

        $entidade_id->addValidation("Entidade", new TRequiredValidator()); 
        $system_unit_id->addValidation("Unidade", new TRequiredValidator()); 


        // $this->$pessoa_optante->setValue('2');
        $pessoa_optante->setUseSwitch(true, 'blue');
        $pessoa_optante->setIndexValue("1");
        $pessoa_optante->setInactiveIndexValue("2");

        TNumeric::disableField(self::$formName, 'iss_servico');

        $entidade_id->enableSearch();
        $system_unit_id->enableSearch();

        $id->setSize(200);
        $ir->setSize('100%');
        $pis->setSize('100%');
        $csll->setSize('100%');
        $taxaadm->setSize('100%');
        $cofins->setSize('100%');
        $ir_servico->setSize('100%');
        $entidade_id->setSize('100%');
        $pis_servico->setSize('100%');
        $iss_servico->setSize('100%');
        $taxabancaria->setSize('100%');
        $taxacontrato->setSize('100%');
        // $taxadesconto->setSize('100%');
        $csll_servico->setSize('100%');
        $system_unit_id->setSize('100%');
        $taxaantecipacao->setSize('100%');
        $cofins_servico->setSize('100%');


        $row1 = $this->form->addFields([new TLabel("Entidade:", null, '14px', null, '100%'),$entidade_id,$id],[new TLabel("Unidade:", null, '14px', null, '100%'),$system_unit_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TFormSeparator("<br>Taxas (%)", '#333', '18', '#eee')]);
        $row2->layout = [' col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("Taxa administração:", null, '14px', null, '100%'),$taxaadm],[new TLabel("Taxa bancária:", null, '14px', null, '100%'),$taxabancaria],[new TLabel("Taxa antecipação:", null, '14px', null, '100%'),$taxaantecipacao]);

        // $row3 = $this->form->addFields([new TLabel("Taxaadm:", null, '14px', null, '100%'),$taxaadm],[new TLabel("Taxabancaria:", null, '14px', null, '100%'),$taxabancaria],[new TLabel("Taxaantecipacao:", null, '14px', null, '100%'),$taxaantecipacao],[]);

        $row3->layout = [' col-sm-2',' col-sm-2','col-sm-2','col-sm-2'];

        $row4 = $this->form->addFields([new TFormSeparator("<br>Taxa de imposto de produtos (%)", '#333', '18', '#eee')]);
        $row4->layout = [' col-sm-12'];

        $row5 = $this->form->addFields([new TLabel("Ir:", null, '14px', null, '100%'),$ir],[new TLabel("Csll:", null, '14px', null, '100%'),$csll],[new TLabel("Cofins:", null, '14px', null, '100%'),$cofins],[new TLabel("Pis:", null, '14px', null, '100%'),$pis]);
        $row5->layout = [' col-sm-2',' col-sm-2','col-sm-2','col-sm-2','col-sm-2','col-sm-2'];

        $row6 = $this->form->addFields([new TFormSeparator("<br>Taxa de imposto de serviços (%)", '#333', '18', '#eee')]);
        $row6->layout = [' col-sm-12'];

        $row7 = $this->form->addFields([new TLabel("Ir:", null, '14px', null, '100%'),$ir_servico],[new TLabel("Csll:", null, '14px', null, '100%'),$csll_servico],[new TLabel("Cofins:", null, '14px', null, '100%'),$cofins_servico],[new TLabel("Pis:", null, '14px', null, '100%'),$pis_servico],[new TLabel("Iss:", null, '14px', null, '100%'),$iss_servico],[new TLabel("Optante:", null, '14px', null, '100%'),$pessoa_optante]);
        $row7->layout = [' col-sm-2',' col-sm-2','col-sm-2','col-sm-2','col-sm-2'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['TaxasPessoaList', 'onShow']), 'fas:arrow-left #000000');
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
 $style = new TStyle('right-panel > .container-part[page-name=TaxasPessoaForm]');
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

            $object = new TaxasPessoa(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data
            $object->system_users_id = TSession::getValue('userid'); // assign the user id
            $object->pessoa_id = TSession::getValue('idpessoa'); // assign the user id

            $object->store(); // save the object 
    
            $this->fireEvents($object);

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam['id'] = $object->pessoa_id;

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('TaxasPessoaList', 'onSetProject', $loadPageParam); 

                        TScript::create("Template.closeRightPanel();"); 
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

                $object = new TaxasPessoa($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form 
                $this->fireEvents($object);

                $object->pessoa_optante = $object->pessoa->optante;
                
                if($object->optante == '1'){
                    TNumeric::enableField(self::$formName, 'iss_servico');
                }
                else{
                    TNumeric::disableField(self::$formName, 'iss_servico');
                }

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

    public static function onChangePessoaOptante($param){
        try{
            // TToast::show('info', 'Mudou optante: ' . ($param['optante'] ?? 'NULL'), 'topRight');

            if ( $param['key'] == '1' )
            {
                // Código gerado pelo snippet: "Habilitar campo"
                TNumeric::enableField(self::$formName, 'iss_servico');    
            }
            else
            {
                TNumeric::disableField(self::$formName, 'iss_servico');
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
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
     public static function onChangeentidade_id($param)
    {
        try
        {

            if (isset($param['entidade_id']) && $param['entidade_id'])
            { 
                $criteria = TCriteria::create(['entidade_id' => $param['entidade_id']]);
                TDBCombo::reloadFromModel(self::$formName, 'system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}', 'name asc', $criteria, TRUE); 
            } 
            else 
            { 
                TCombo::clearField(self::$formName, 'system_unit_id'); 
            }  

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    } 
     public function fireEvents( $object )
    {
        $obj = new stdClass;
        if(is_object($object) && get_class($object) == 'stdClass')
        {
            if(isset($object->entidade_id))
            {
                $value = $object->entidade_id;

                $obj->entidade_id = $value;
            }
            if(isset($object->system_unit_id))
            {
                $value = $object->system_unit_id;

                $obj->system_unit_id = $value;
            }
        }
        elseif(is_object($object))
        {
            if(isset($object->entidade_id))
            {
                $value = $object->entidade_id;

                $obj->entidade_id = $value;
            }
            if(isset($object->system_unit_id))
            {
                $value = $object->system_unit_id;

                $obj->system_unit_id = $value;
            }
        }
        TForm::sendData(self::$formName, $obj);
    }  



}

