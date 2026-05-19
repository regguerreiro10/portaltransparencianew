<?php

//<fileHeader>

//</fileHeader>

use Adianti\Control\TWindow;

class MovimentoDispositivosForm extends TWindow
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'MovimentoDispositivos';
    private static $primaryKey = 'id';
    private static $formName = 'form_MovimentoDispositivosForm';

    //<classProperties>

    //</classProperties>

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
        $this->form->setFormTitle("Cadastro de movimento dispositivos");

        $criteria_estabelecimento_id = new TCriteria();
        $criteria_condutor_id = new TCriteria();

        $filterVar = GrupoPessoa::FORNECEDOR;
        $criteria_estabelecimento_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '{$filterVar}')")); 
        $filterVar = GrupoPessoa::CONDUTOR;
        $criteria_condutor_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '{$filterVar}')")); 

        //<onBeginPageCreation>

        //</onBeginPageCreation>

        $id = new TEntry('id');
        $estabelecimento_id = new TDBCombo('estabelecimento_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_estabelecimento_id );
        $condutor_id = new TDBCombo('condutor_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_condutor_id );
        $datahora = new TDateTime('datahora');
        $qtde = new TEntry('qtde');
        $valor_unitario = new TNumeric('valor_unitario', '2', ',', '.' );
        $valor_total = new TNumeric('valor_total', '2', ',', '.' );
        $valor_desconto = new TNumeric('valor_desconto', '2', ',', '.' );
        $valor_liquido = new TNumeric('valor_liquido', '2', ',', '.' );
        $obs = new TEntry('obs');
        $localizacao = new TEntry('localizacao');
        $sentidodapassagem = new TEntry('sentidodapassagem');
        $operadorapedagio = new TEntry('operadorapedagio');
        $tipodavia = new TEntry('tipodavia');
        $idtransacao = new TEntry('idtransacao');


        $id->setEditable(false);
        $datahora->setMask('dd/mm/yyyy hh:ii');
        $datahora->setDatabaseMask('yyyy-mm-dd hh:ii');
        $condutor_id->enableSearch();
        $estabelecimento_id->enableSearch();

        $obs->setMaxLength(255);
        $tipodavia->setMaxLength(255);
        $localizacao->setMaxLength(255);
        $operadorapedagio->setMaxLength(255);
        $sentidodapassagem->setMaxLength(255);

        $id->setSize(100);
        $obs->setSize('100%');
        $qtde->setSize('100%');
        $datahora->setSize(150);
        $tipodavia->setSize('100%');
        $condutor_id->setSize('100%');
        $valor_total->setSize('100%');
        $localizacao->setSize('100%');
        $idtransacao->setSize('100%');
        $valor_liquido->setSize('100%');
        $valor_unitario->setSize('100%');
        $valor_desconto->setSize('100%');
        $operadorapedagio->setSize('100%');
        $sentidodapassagem->setSize('100%');
        $estabelecimento_id->setSize('100%');

        //<onBeforeAddFieldsToForm>

        //</onBeforeAddFieldsToForm>
        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Estabelecimento id:", null, '14px', null, '100%'),$estabelecimento_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Condutor id:", null, '14px', null, '100%'),$condutor_id],[new TLabel("Datahora:", null, '14px', null, '100%'),$datahora]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Qtde:", null, '14px', null, '100%'),$qtde],[new TLabel("Valor unitario:", null, '14px', null, '100%'),$valor_unitario]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Valor total:", null, '14px', null, '100%'),$valor_total],[new TLabel("Valor desconto:", null, '14px', null, '100%'),$valor_desconto]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Valor liquido:", null, '14px', null, '100%'),$valor_liquido],[new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row5->layout = ['col-sm-6','col-sm-6'];

        $row6 = $this->form->addFields([new TLabel("Localizacao:", null, '14px', null, '100%'),$localizacao],[new TLabel("Sentidodapassagem:", null, '14px', null, '100%'),$sentidodapassagem]);
        $row6->layout = ['col-sm-6','col-sm-6'];

        $row7 = $this->form->addFields([new TLabel("Operadorapedagio:", null, '14px', null, '100%'),$operadorapedagio],[new TLabel("Tipodavia:", null, '14px', null, '100%'),$tipodavia]);
        $row7->layout = ['col-sm-6','col-sm-6'];

        $row8 = $this->form->addFields([new TLabel("Idtransacao:", null, '14px', null, '100%'),$idtransacao],[]);
        $row8->layout = ['col-sm-6','col-sm-6'];

        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['MovimentoDispositivosList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

     /*   parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);*/

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

            $object = new MovimentoDispositivos(); // create an empty object //</blockLine>

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data
            $object->dispositivos_solicitados_id = TSession::getValue('dispositivossolicitadosid');

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

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; //</blockLine>

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            //</messageAutoCode> //</blockLine>
//<generatedAutoCode>
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('MovimentoDispositivosList', 'onShow', $loadPageParam);
//</generatedAutoCode>

            //</endTryAutoCode> //</blockLine>
//<generatedAutoCode>
            TScript::create("Template.closeRightPanel();");
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
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new MovimentoDispositivos($key); // instantiates the Active Record //</blockLine>

                //</beforeSetDataAutoCode> //</blockLine>

                $this->form->setData($object); // fill the form //</blockLine>

                //</afterSetDataAutoCode> //</blockLine>
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

        //<onFormClear>

        //</onFormClear>

    }

    public function onShow($param = null)
    {

        //<onShow>

        //</onShow>
    } 

    public static function getFormName()
    {
        return self::$formName;
    }

    //</hideLine> <addUserFunctionsCode/>

    //<userCustomFunctions>

    //</userCustomFunctions>

}