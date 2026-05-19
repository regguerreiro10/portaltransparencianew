<?php

class CadastroProdutoForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Produto';
    private static $primaryKey = 'id';
    private static $formName = 'form_CadastroProdutoForm';

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
        $this->form->setFormTitle("Cadastro de produto");

        $criteria_unidade_medida_id = new TCriteria();
        $criteria_tipo_produto_id = new TCriteria();

        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $unidade_medida_id = new TDBCombo('unidade_medida_id', 'minierp', 'UnidadeMedida', 'id', '{nome}','nome asc' , $criteria_unidade_medida_id );
        $preco_venda = new TNumeric('preco_venda', '2', ',', '.' );
        $obs = new TEntry('obs');
        $tipo_produto_id = new TDBCombo('tipo_produto_id', 'minierp', 'TipoProduto', 'id', '{nome}','nome asc' , $criteria_tipo_produto_id );

        $nome->addValidation("Nome", new TRequiredValidator()); 
        $unidade_medida_id->addValidation("Unidade de medida", new TRequiredValidator()); 
        $preco_venda->addValidation("Preço", new TRequiredValidator()); 
        $tipo_produto_id->addValidation("Tipo Produto/Serviço", new TRequiredValidator()); 

        $tipo_produto_id->enableSearch();
        $tipo_produto_id->setSize('100%');
        $obs->setSize('100%');

        $id->setEditable(false);
        $nome->setMaxLength(255);
        $unidade_medida_id->enableSearch();
        $unidade_medida_id->configureNoResultsQuickRegister(new TAction(['UnidadeMedidaForm', 'onQuickSave']), "Cadastrar", "fas:plus #69AA46", "btn-default");
        $unidade_medida_id->setNoResultsMessage("Unidade de medida não encontrado click em cadastrar");
        $id->setSize(100);
        $nome->setSize('100%');
        $preco_venda->setSize('100%');
        $unidade_medida_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Nome:*", '#ff0000', '14px', null, '100%'),$nome]);
        $row1->layout = ['col-sm-6',' col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Unidade de medida:*",  '#FF0000', '14px', null, '100%'),$unidade_medida_id],[new TLabel("Preco venda:*",  '#FF0000', '14px', null, '100%'),$preco_venda]);
        $row2->layout = ['col-sm-6','col-sm-6'];
        
        $row3 = $this->form->addFields([new TLabel("Tipo Produto/Serviço:", '#FF0000', '14px', null, '100%'),$tipo_produto_id],[new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['ConsultaProdutoList', 'onShow']), 'fas:arrow-left #000000');
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

            $object = new Produto(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->ativo = 'T';
            $object->system_unit_id = TSession::getValue('idunit');
            $object->system_users_id = TSession::getValue('iduser');

            $object->store(); // save the object 

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $objectpro = new stdClass();
            $objectpro->itens_pedido_pedido_venda_produto_id = $object->id;
            TForm::sendData('form_PedidoVendaForm', $objectpro);


            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            if(BComboNoResultsService::getProperties($param))
            {
                BComboNoResultsService::handleRefreshComponent($param, $object);
            }
            else
            {
//                TApplication::loadPage('ConsultaProdutoList', 'onShow', $loadPageParam);
            }

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');

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

                $object = new Produto($key); // instantiates the Active Record 

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

    public static function onQuickSave($param = null)
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $object = new Produto(); // create an empty object
            $object->nome = BComboNoResultsService::getQuickFieldValue($param);

            $object->ativo = 'T';
            $object->system_unit_id = TSession::getValue('idunit');
            $object->system_users_id = TSession::getValue('iduser');
            $object->tipo_produto_id = 1;

            $object->store();

            BComboNoResultsService::handleRefreshComponent($param, $object);

            TTransaction::close();

            TToast::show('success', _t('Record saved'), 'topRight', 'far:check-circle');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
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

