<?php

class EstadoPedidoFrotasForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'EstadoPedidoFrotas';
    private static $primaryKey = 'id';
    private static $formName = 'form_EstadoPedidoFrotasForm';

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
        $basename   = urlencode('estado-pedido-frotas.pdf');
        $download   = "download.php?file=app/manual/estado-pedido-frotas.pdf&basename={$basename}";

        $manual = "
            <span style='float:right;'>
                <a href='{$download}'
                target='_blank'
                style='text-decoration:none;margin-left:10px;'>
                    <i class='fa fa-question-circle'> </i>
                </a>
            </span>
        "; 
        // define the form title
        $this->form->setFormTitle("Cadastro de estado de pedido de frotas{$manual}");

        $criteria_estados_pedido_venda = new TCriteria();

        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $cor = new TColor('cor');
        $ordem = new TEntry('ordem');
        $estado_final = new TRadioGroup('estado_final');
        $kanban = new TRadioGroup('kanban');
        $estado_inicial = new TRadioGroup('estado_inicial');
        $permite_edicao = new TRadioGroup('permite_edicao');
        $permite_exclusao = new TRadioGroup('permite_exclusao');
        $estados_pedido_frotas = new TDBCheckGroup('estados_pedido_frotas', 'minierp', 'EstadoPedidoFrotas', 'id', '{nome}','ordem asc' , $criteria_estados_pedido_venda );


        $id->setEditable(false);
        $nome->setMaxLength(255);
        $estados_pedido_frotas->setBreakItems(2);
        $kanban->addItems(["T"=>"Sim","F"=>"Não"]);
        $estado_final->addItems(["T"=>"Sim","F"=>"Não"]);
        $estado_inicial->addItems(["T"=>"Sim","F"=>"Não"]);
        $permite_edicao->addItems(["T"=>"Sim","F"=>"Não"]);
        $permite_exclusao->addItems(["T"=>"Sim","F"=>"Não"]);

        $kanban->setUseButton();
        $estado_final->setUseButton();
        $estado_inicial->setUseButton();
        $permite_edicao->setUseButton();
        $permite_exclusao->setUseButton();

        $kanban->setLayout('horizontal');
        $estado_final->setLayout('horizontal');
        $estado_inicial->setLayout('horizontal');
        $permite_edicao->setLayout('horizontal');
        $permite_exclusao->setLayout('horizontal');
        $estados_pedido_frotas->setLayout('horizontal');

        $id->setSize(100);
        $cor->setSize(120);
        $nome->setSize('100%');
        $ordem->setSize('100%');
        $kanban->setSize('100%');
        $estado_final->setSize('100%');
        $estado_inicial->setSize('100%');
        $permite_edicao->setSize('100%');
        $permite_exclusao->setSize('100%');
        $estados_pedido_frotas->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Nome:", null, '14px', null, '100%'),$nome]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Cor:", null, '14px', null, '100%'),$cor],[new TLabel("Ordem:", null, '14px', null, '100%'),$ordem]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Estado final:", null, '14px', null, '100%'),$estado_final],[new TLabel("Kanban:", null, '14px', null, '100%'),$kanban]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Estado inicial:", null, '14px', null, '100%'),$estado_inicial],[new TLabel("Permite edicao:", null, '14px', null, '100%'),$permite_edicao]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Permite exclusao:", null, '14px', null, '100%'),$permite_exclusao]);
        $row5->layout = ['col-sm-6'];

        $row6 = $this->form->addFields([new TFormSeparator("Próximos Estados", '#333', '18', '#eee')]);
        $row6->layout = [' col-sm-12'];

        $row7 = $this->form->addFields([$estados_pedido_frotas]);
        $row7->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['EstadoPedidoFrotasHeaderList', 'onShow']), 'fas:arrow-left #000000');
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

            $object = new EstadoPedidoFrotas(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            $repository = MatrizEstadoPedidoFrotas::where('estado_pedido_frotas_origem_id', '=', $object->id);
            $repository->delete(); 

            if ($data->estados_pedido_frotas) 
            {
                foreach ($data->estados_pedido_frotas as $estados_pedido_frotas_value) 
                {
                    $matriz_estado_pedido = new MatrizEstadoPedidoFrotas;

                    $matriz_estado_pedido->estado_pedido_frotas_destino_id = $estados_pedido_frotas_value;
                    $matriz_estado_pedido->estado_pedido_frotas_origem_id = $object->id;
                    $matriz_estado_pedido->store();
                }
            }

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
            TApplication::loadPage('EstadoPedidoFrotasHeaderList', 'onShow', $loadPageParam); 

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

                $object = new EstadoPedidoFrotas($key); // instantiates the Active Record 

                $object->estados_pedido_frotas = MatrizEstadoPedidoFrotas::where('estado_pedido_frotas_origem_id', '=', $object->id)->getIndexedArray('estado_pedido_frotas_destino_id', 'estado_pedido_frotas_destino_id');

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

