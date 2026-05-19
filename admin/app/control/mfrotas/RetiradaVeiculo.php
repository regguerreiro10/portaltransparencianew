<?php

use Adianti\Database\TCriteria;
use Adianti\Registry\TSession;

class RetiradaVeiculo extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'PedidoFrotas';
    private static $primaryKey = 'id';
    private static $formName = 'form_EntradaVeiculoForm';

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
        $idPedido = TSession::getValue("pedidoFrotas");
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de aprovador frotas");

        $criteria_condutor_retirada_id = new TCriteria();

        $condutor_retirada_id = new TDBCombo('condutor_retirada_id', 'minierp', 'Condutor', 'id', '{nome}','nome asc' , $criteria_condutor_retirada_id );
        $dataretirada = new TDate('dataretirada');

        $row1 = $this->form->addFields([new TLabel("Condutor de retirada do veículo:", null, '14px', null, '100%'),$condutor_retirada_id],[new TLabel("Data de retirada:", null, '14px', null, '100%'),$dataretirada]);
        $row1->layout = [' col-sm-8',' col-sm-4'];

        $action = new TAction([$this, 'onSave']);
        $action->SetParameter('id', $idPedido);
        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", $action, 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['PedidoFrotasList', 'onShow']), 'fas:arrow-left #000000');
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

    public function onSave($param) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;
            $idPedido = TSession::getValue("pedidoFrotas");

            $this->form->validate(); // validate form data

            $object = new PedidoFrotas($idPedido); // create an empty object 

            $data = $this->form->getData(); // get form data as array

            $object->dataretirada = $data->dataretirada;
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('PedidoFrotasList', 'onShow'); 

            TScript::create("Template.closeRightPanel();"); 
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

                $object = new AprovadorFrotas($key); // instantiates the Active Record 

                $object->estados_pedido_frotas = EstadoPedidoFrotasAprovador::where('aprovador_frotas_id', '=', $object->id)->getIndexedArray('estado_pedido_frotas_id', 'estado_pedido_frotas_id');

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

    public function onShow($param)
    {
        TSession::setValue("pedidoFrotas", $param['id']);
    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

