<?php

use Adianti\Database\TTransaction;

class TStatusPedidoCancelar extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'PedidoFrotas';
    private static $primaryKey = 'id';
    private static $formName = 'form_TStatusPedidoFrotasForm';

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
        $this->form->setFormTitle("Registrar Cancelamento de Pedido");

           
        $id = new TEntry('id');
        $justificativa = new TText('justificativa');
        $justificativa->addValidation("Justificativa", new TRequiredValidator()); 
       

         $id->setSize('100%');
        $justificativa->setSize('100%', 70);
     
        $id->setEditable(false);
    

        $row0 = $this->form->addFields([new TLabel("ID Pedido", null, '14px', null, '100%'),$id]);
        $row0->layout = ['col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Justificativa: *", '#FF0000', '14px', null),$justificativa]);
        $row2->layout = [' col-sm-12'];
      

        // create the form actions
        $btn_onsave = $this->form->addAction("Cancelar Pedido", new TAction([$this, 'onCancelarPedido'], ['static' => 1]), 'fas:times #ffffff');

        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

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

    
        parent::setTargetContainer('adianti_right_panel');

        parent::add($this->form);

    }

 


    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new PedidoFrotas($key); // instantiates the Active Record 

                
                $histPedido = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $object->id)
                    ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::COMPROPOSTA)
                    ->orderBy('data_operacao', 'desc')
                    ->last();
                if ($histPedido) {
                    $object->justificativa = $histPedido->obs;
                } else {
                    $object->justificativa = '';
                }

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

    public function onCancelarPedido($param = null) 
    {
        if (!isset($param['confirmCancelar']) || !$param['confirmCancelar']) 
        {
            // VERIFICA A JUSTIFICATIVA
            $data = $this->form->getData();
            if (empty(trim($data->justificativa))) {
                throw new Exception('A justificativa é obrigatória antes de cancelar o pedido.');
            }

            $action = new TAction([$this, 'onCancelarPedido']);
            $action->setParameters(['id' => $param['id'],'justificativa' => $data->justificativa]);
            $action->setParameter('confirmCancelar', true);

            new TQuestion('Tem certeza que deseja cancelar este pedido?', $action);
            return;
        }

        // EXECUTA
        try 
        {
            TTransaction::open(self::$database);

            // VERIFICA A JUSTIFICATIVA
            $justificativa = $param['justificativa'] ?? '';
            if (empty(trim($justificativa))) {
                throw new Exception('A justificativa é obrigatória!');
            }

            $object = new PedidoFrotas($param['id'], false);
            $object->estado_pedido_frotas_id = EstadoPedidoFrotas::CANCELADO;
            $object->justificativa = $justificativa;
            $object->store();

            $this->registrarHistoricoCancelamento($object, $justificativa);

            // CANCELA AS PROPOSTAS
            $propostas = Propostas::where('pedido_frotas_id', '=', $object->id)->load();
            if ($propostas) {
                foreach ($propostas as $proposta) {
                    $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::CANCELADO;
                    $proposta->store();
                }
            }

            // ATUALIZA
            $this->form->setData(['id' => $object->id,'justificativa' => $object->justificativa]);

            TToast::show('success', "Pedido/Propostas canceladas com sucesso!", 'topRight', 'far fa-check-circle');

            TTransaction::close();
            TApplication::loadPage('PedidoFrotasList', 'onSetProject');

        } 
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

   
    private function registrarHistoricoCancelamento($pedido, $justificativa)    
    {

        $hist = new PedidoFrotasHistorico();
        $hist->pedido_frotas_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d H:i:s');
        $hist->estado_pedido_frotas_id = EstadoPedido::CANCELADO; 
        $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $hist->aprovador_frotas_id = $aprovador[0]->id;
        }
        $hist->obs =$justificativa;
        $hist->store();

    }

   


}

