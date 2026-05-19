<?php

class ManutencaoGarantiaForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'ManutencaoGarantia';
    private static $primaryKey = 'id';
    private static $formName = 'form_ManutencaoGarantiaForm';

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
        $this->form->setFormTitle("Cadastro de manutencao garantia");

        $criteria_veiculos_id = new TCriteria();
                $criteria_produto_id = new TCriteria();

  $filterVar = TSession::getValue('idunit');
        $criteria_produto_id->add(new TFilter('system_unit_id', '=', $filterVar)); 

        $id = new TEntry('id');
        $tipo = new TCombo('tipo');
        $pedido_frotas_id = new TEntry('pedido_frotas_id');
        $propostas_id = new TEntry('propostas_id');
        $itens_propostas_id = new TEntry('itens_propostas_id');
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa}','id asc' , $criteria_veiculos_id );
        $descricao = new TEntry('descricao');
        $qtde = new TEntry('qtde');
        $dias_garantia = new TEntry('dias_garantia');
        $km_manutencao = new TEntry('km_manutencao');
        $datagarantia = new TDate('datagarantia');
        $ativo = new TCheckButton('ativo');
        $obs = new TText('obs'); 
                $produto_id = new TDBUniqueSearch('produto_id', 'minierp', 'Produto', 'id', 'nome','nome asc' , $criteria_produto_id );

        $produto_id->addValidation("Produto", new TRequiredValidator()); 

        $produto_id->setMinLength(2);
        $produto_id->setFilterColumns(["nome"]);
                         $produto_id->setSize('100%');


        $tipo->addItems(["1"=>"Serviço","2"=>"Produto"]);
        $datagarantia->setMask('dd/mm/yyyy');
        $datagarantia->setDatabaseMask('yyyy-mm-dd');
        $ativo->setValue('N');
        $ativo->setUseSwitch(true, 'blue');
        $ativo->setIndexValue("S");
        $ativo->setInactiveIndexValue("N");
        $tipo->enableSearch();
        $veiculos_id->enableSearch();

        $id->setEditable(false);
        $tipo->setEditable(false);
        $qtde->setEditable(false);
        $descricao->setEditable(false);
        $veiculos_id->setEditable(false);
        $propostas_id->setEditable(false);
        $datagarantia->setEditable(false);
        $dias_garantia->setEditable(false);
        $km_manutencao->setEditable(false);
        $pedido_frotas_id->setEditable(false);
        $itens_propostas_id->setEditable(false);
        $produto_id->setEditable(false);

        $id->setSize(100);
        $tipo->setSize('100%');
        $qtde->setSize('100%');
        $obs->setSize('100%', 70);
        $descricao->setSize('100%');
        $veiculos_id->setSize('100%');
        $propostas_id->setSize('100%');
        $datagarantia->setSize('100%');
        $dias_garantia->setSize('100%');
        $km_manutencao->setSize('100%');
        $pedido_frotas_id->setSize('100%');
        $itens_propostas_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Tipo", null, '14px', null, '100%'),$tipo]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("ID Pedido", null, '14px', null, '100%'),$pedido_frotas_id],[new TLabel("ID Proposta:", null, '14px', null, '100%'),$propostas_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("ID Item da proposta", null, '14px', null, '100%'),$itens_propostas_id],[new TLabel("Veiculo:", null, '14px', null, '100%'),$veiculos_id]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Produto:", null, '14px', null, '100%'),$produto_id], [new TLabel("Descrição:", null, '14px', null, '100%'),$descricao]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Qtde:", null, '14px', null, '100%'),$qtde], [new TLabel("Dias garantia:", null, '14px', null, '100%'),$dias_garantia]);
        $row5->layout = ['col-sm-6','col-sm-6'];

   
        $row6 = $this->form->addFields([new TLabel("Km:", null, '14px', null, '100%'),$km_manutencao], [new TLabel("Data:", null, '14px', null, '100%'),$datagarantia]);
        $row6->layout = ['col-sm-6','col-sm-6'];

        $row7 = $this->form->addFields([new TLabel("Obs:", null, '14px', null, '100%'),$obs], [new TLabel("Notificação Ativa?", null, '14px', null, '100%'),$ativo]);
        $row7->layout = ['col-sm-6','col-sm-6'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

  ///      $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
 //       $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['ManutencaoGarantiaList', 'onShow']), 'fas:arrow-left #000000');
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

            $object = new ManutencaoGarantia(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

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


            

                if(TSession::getValue('formdashboard') == 'formdashboard'){
                    TSession::setValue('formdashboard', null); // limpa a flag
                    TApplication::loadPage('DashboardPedidoFrotas', 'onShow', $loadPageParam);
                }else{
                    TApplication::loadPage('ManutencaoGarantiaList', 'onShow', $loadPageParam); 
                }

                // if (!empty($param['redirect']) && $param['redirect'] == 'dashboard') {
                //     TApplication::loadPage('DashboardPedidoFrotas', 'onShow', $loadPageParam);
                // } else {
                //     TApplication::loadPage('ManutencaoGarantiaList', 'onShow', $loadPageParam);
                // }


                             
            
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

                $object = new ManutencaoGarantia($key); // instantiates the Active Record 

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

    public function onSetProject($param = null)
    {
        TSession::setValue('formdahboard', null);

        TSession::setValue('formdashboard', 'formdashboard');
        $this->onEdit($param);

    } 
}

