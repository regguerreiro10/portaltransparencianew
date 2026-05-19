<?php

class SaldoEntidadeContratoForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'SaldoEntidadeContrato';
    private static $primaryKey = 'id';
    private static $formName = 'form_SaldoEntidadeContratoForm';

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
        $this->form->setFormTitle("Cadastro de saldo entidade contrato");

        
        $criteria_system_users_id = new TCriteria();    

        $id = new TEntry('id');
        $entidade_id = new TEntry('entidade_id');
        $system_users_id = new TDBCombo('system_users_id', 'minierp', 'SystemUsers', 'id', '{name}','name asc' , $criteria_system_users_id );
     //   $tipotransacao = new TCombo('tipotransacao');
        $datatransacao = new TDate('datatransacao');

        $dtinicio = new TDate('dtinicio');
        $dtfinal = new TDate('dtfinal');

        $historico = new TEntry('historico');
        $valor_saldo = new TNumeric('valor_saldo', '2', ',', '.' );

        // $tipotransacao->addItems([
        //     'D' => 'Débito',
        //     'C' => 'Crédito'
        // ]);

        // if (!empty($param['id'])) {
        //     $entidade_id->setValue($param['id']);
        //     $entidade_id->setEditable(false); // se quiser travar o campo
        // }

        $id->setEditable(false);
        $datatransacao->setMask('dd/mm/yyyy');
        $datatransacao->setDatabaseMask('yyyy-mm-dd');
        $dtinicio->setMask('dd/mm/yyyy');
        $dtinicio->setDatabaseMask('yyyy-mm-dd');
        $dtfinal->setMask('dd/mm/yyyy');
        $dtfinal->setDatabaseMask('yyyy-mm-dd');
        $entidade_id->setEditable(false);
        $system_users_id->enableSearch();

        // $entidade_id->addValidation('Entidade', new TRequiredValidator);
        $system_users_id->addValidation('Usuario', new TRequiredValidator);
        // $tipotransacao->addValidation('Tipo Transação', new TRequiredValidator);
        $dtinicio->addValidation('Data Inicio', new TRequiredValidator);
        $dtfinal->addValidation('Data Final', new TRequiredValidator);
        $datatransacao->addValidation('Data Transação', new TRequiredValidator);
        $valor_saldo->addValidation('Valor Saldo', new TRequiredValidator);

        $id->setSize(100);
        $historico->setSize('100%');
        $datatransacao->setSize(110);
        $dtinicio->setSize(60);
        $dtfinal->setSize(60);
        $entidade_id->setSize('100%');
        $valor_saldo->setSize('100%');
        $ativo = new TCheckButton('ativo');
        
        
        $ativo->addValidation("Ativo", new TRequiredValidator()); 

        // $tipotransacao->setSize('100%'); 
        $system_users_id->setSize('100%');
        $ativo->setSize('100%');
        $ativo->setUseSwitch(true, 'blue');

        $ativo->setIndexValue("1");

        $ativo->setInactiveIndexValue("2");
        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null)],[$id], [new TLabel("Entidade:", '#ff0000', '14px', null)],[$entidade_id]);
        
        // $row2 = $this->form->addFields([new TLabel("Tipo Transação:", '#ff0000', '14px', null)],[$tipotransacao],[new TLabel("Data Transação:", '#ff0000', '14px', null)],[$datatransacao]);
        $row3 = $this->form->addFields([new TLabel("Histórico:", null, '14px', null)],[$historico], [new TLabel("Valor saldo:", '#ff0000', '14px', null)],[$valor_saldo]);
        $row4 = $this->form->addFields([new TLabel("Dt Inicio:", '#ff0000', '14px', null)],[$dtinicio], [new TLabel("Dt Final:", '#ff0000', '14px', null)],[$dtfinal]);
        $row5 = $this->form->addFields([new TLabel("Ativo:", '#ff0000', '14px', null)],[$ativo], [],[]);


        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['SaldoEntidadeContratoList', 'onShow']), 'fas:arrow-left #000000');
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

            $object = new SaldoEntidadeContrato(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->entidade_id = TSession::getValue('entidade_id');
            $object->system_users_id = TSession::getValue('userid');
            $object->tipotransacao = 'C'; // por padrão, toda vez que for salvo, é um crédito para o contrato. O débito só acontece quando for gerado um movimento de consumo, e nesse caso, o sistema irá gerar um registro de saldo com tipo de transação 'D' e valor negativo.   

     
            $object->store(); // save the object 

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('SaldoEntidadeContratoList', 'onShow', $loadPageParam); 

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

                $object = new SaldoEntidadeContrato($key); // instantiates the Active Record 

                                $object->system_users_id = $object->system_users->id;

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

}

