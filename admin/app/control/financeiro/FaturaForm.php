<?php

class FaturaForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Fatura';
    private static $primaryKey = 'id';
    private static $formName = 'form_FaturaForm';

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
        $this->form->setFormTitle("Cadastro de fatura");

        $criteria_forma_pagamento_id = new TCriteria();

        $id = new TEntry('id');
        $numero_fatura = new TEntry('numero_fatura');
        $forma_pagamento_id = new TDBCombo('forma_pagamento_id', 'minierp', 'FormaPagamento', 'id', '{nome}','nome asc' , $criteria_forma_pagamento_id );
        $data_emissao = new TDateTime('data_emissao');
        $periodo_apuracao_inicial = new TDate('periodo_apuracao_inicial');
        $periodo_apuracao_final = new TDate('periodo_apuracao_final');
        $data_vencimento = new TDate('data_vencimento');
        $data_pagamento = new TDate('data_pagamento');
        $totalgeral = new TNumeric('totalgeral', '2', ',', '.' );
        $totalservico = new TNumeric('totalservico', '2', ',', '.' );
        $totalproduto = new TNumeric('totalproduto', '2', ',', '.' );
        $desconto = new TNumeric('desconto', '2', ',', '.' );
        $total = new TNumeric('total', '2', ',', '.' );
        $obs = new TText('obs');


        $numero_fatura->setMaxLength(10);
        $forma_pagamento_id->enableSearch();
        $data_pagamento->setMask('dd/mm/yyyy');
        $data_vencimento->setMask('dd/mm/yyyy');
        $data_emissao->setMask('dd/mm/yyyy hh:ii');
        $periodo_apuracao_final->setMask('dd/mm/yyyy');
        $periodo_apuracao_inicial->setMask('dd/mm/yyyy');

        $data_pagamento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_emissao->setDatabaseMask('yyyy-mm-dd hh:ii');
        $periodo_apuracao_final->setDatabaseMask('yyyy-mm-dd');
        $periodo_apuracao_inicial->setDatabaseMask('yyyy-mm-dd');

        $id->setEditable(false);
        $obs->setEditable(false);
        $total->setEditable(false);
        $desconto->setEditable(false);
        $totalgeral->setEditable(false);
        $data_emissao->setEditable(false);
        $totalservico->setEditable(false);
        $totalproduto->setEditable(false);
        $numero_fatura->setEditable(false);
        $data_vencimento->setEditable(false);
        $forma_pagamento_id->setEditable(false);
        $periodo_apuracao_final->setEditable(false);
        $periodo_apuracao_inicial->setEditable(false);

        $id->setSize(100);
        $total->setSize('100%');
        $obs->setSize('100%', 70);
        $desconto->setSize('100%');
        $data_emissao->setSize(150);
        $totalgeral->setSize('100%');
        $data_pagamento->setSize(150);
        $numero_fatura->setSize('30%');
        $data_vencimento->setSize(150);
        $totalservico->setSize('100%');
        $totalproduto->setSize('100%');
        $forma_pagamento_id->setSize('100%');
        $periodo_apuracao_final->setSize(150);
        $periodo_apuracao_inicial->setSize(150);

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Numero fatura:", null, '14px', null, '100%'),$numero_fatura]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Forma pagamento:", null, '14px', null, '100%'),$forma_pagamento_id],[new TLabel("Data emissao:", null, '14px', null, '100%'),$data_emissao]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Periodo apuracao inicial:", null, '14px', null, '100%'),$periodo_apuracao_inicial],[new TLabel("Periodo apuracao final:", null, '14px', null, '100%'),$periodo_apuracao_final]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Data vencimento:", null, '14px', null, '100%'),$data_vencimento],[new TLabel("Data Recebimento:", null, '14px', null, '100%'),$data_pagamento]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Totalgeral:", null, '14px', null, '100%'),$totalgeral],[new TLabel("Totalservico:", null, '14px', null, '100%'),$totalservico]);
        $row5->layout = ['col-sm-6','col-sm-6'];

        $row6 = $this->form->addFields([new TLabel("Totalproduto:", null, '14px', null, '100%'),$totalproduto],[new TLabel("Desconto:", null, '14px', null, '100%'),$desconto]);
        $row6->layout = ['col-sm-6','col-sm-6'];

        $row7 = $this->form->addFields([new TLabel("Total:", null, '14px', null, '100%'),$total],[new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row7->layout = ['col-sm-6',' col-sm-6'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['FaturaList', 'onShow']), 'fas:arrow-left #000000');
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

            $object = new Fatura(); // create an empty object 

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
            TApplication::loadPage('FaturaList', 'onShow', $loadPageParam); 

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
            $key = $param['key'] ?? $param['id'] ?? null;

            if ($key)
            {
                TTransaction::open(self::$database); // open a transaction

                $object = new Fatura($key); // instantiates the Active Record 

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
