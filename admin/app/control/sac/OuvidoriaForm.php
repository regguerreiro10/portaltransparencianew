<?php

class OuvidoriaForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Ouvidoria';
    private static $primaryKey = 'id';
    private static $formName = 'form_OuvidoriaForm';

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
        $this->form->setFormTitle("Cadastro de ouvidoria");

        $criteria_tipo_ouvidoria_id = new TCriteria();
        $criteria_pessoa_id = new TCriteria();
        $criteria_pessoa_id->add(new TFilter('id', 'not in', '(select pessoa_id from pessoa_grupo where grupo_pessoa_id = 5)'));
        $criteria_system_unit = new TCriteria();
        $criteria_system_unit->add(new TFilter('id', '=', TSession::getValue('idunit')));
        $unitId = TSession::getValue('idunit');
        $criteria_departamento_unit = new TCriteria();
        $criteria_departamento_unit->add(new TFilter('system_unit_id', '=', $unitId));

        $id = new TEntry('id');
        $tipo_ouvidoria_id = new TDBCombo('tipo_ouvidoria_id', 'minierp', 'TipoOuvidoria', 'id', '{nome}','nome asc' , $criteria_tipo_ouvidoria_id );
        $nome = new TEntry('nome');
        $telefone = new TEntry('telefone');
        $email = new TEntry('email');
        $mensagem = new TText('mensagem');
        $mensagem->setSize('100%', 120); // largura total e altura maior
        $system_unit = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}', 'name asc', $criteria_system_unit);
        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}', 'nome asc', $criteria_pessoa_id);
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}', 'name asc', $criteria_departamento_unit);

        $tipo_ouvidoria_id->addValidation("Tipo ouvidoria id", new TRequiredValidator()); 
        $nome->addValidation("Nome", new TRequiredValidator()); 
        $telefone->addValidation("Telefone", new TRequiredValidator()); 
        $mensagem->addValidation("Mensagem", new TRequiredValidator()); 
        $system_unit->addValidation("Unidade", new TRequiredValidator()); 
        $pessoa_id->addValidation("Fornecedor", new TRequiredValidator()); 
        $departamento_unit_id->addValidation("Departamento", new TRequiredValidator()); 

        $id->setEditable(false);
        $tipo_ouvidoria_id->enableSearch();
        $system_unit->enableSearch();
        $pessoa_id->enableSearch();
        $departamento_unit_id->enableSearch();

        $id->setSize(100);
        $nome->setSize('100%');
        $email->setSize('100%');
        $telefone->setSize('100%');
        $mensagem->setSize('100%');
        $system_unit->setSize('100%');
        $pessoa_id->setSize('100%');
        $tipo_ouvidoria_id->setSize('100%');
        $departamento_unit_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Tipo ouvidoria id:", '#ff0000', '14px', null, '100%'),$tipo_ouvidoria_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Nome:", '#ff0000', '14px', null, '100%'),$nome],[new TLabel("Telefone:", '#ff0000', '14px', null, '100%'),$telefone]);
        $row2->layout = ['col-sm-6','col-sm-6'];



        $row4 = $this->form->addFields([new TLabel("Fornecedor:", '#ff0000', '14px', null, '100%'),$pessoa_id],[new TLabel("Unidade:", '#ff0000', '14px', null, '100%'),$system_unit]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Departamento:", '#ff0000', '14px', null, '100%'),$departamento_unit_id]);
        $row5->layout = ['col-sm-6'];


                        // Email sozinho
        $row3 = $this->form->addFields(
            [new TLabel("Email:", null, '14px', null, '100%'), $email]
        );
        $row3->layout = ['col-sm-6'];

        // Mensagem embaixo ocupando tudo
        $rowMensagem = $this->form->addFields(
            [new TLabel("Mensagem:", '#ff0000', '14px', null, '100%'), $mensagem]
        );
        $rowMensagem->layout = ['col-sm-12'];



        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['OuvidoriaList', 'onShow']), 'fas:arrow-left #000000');
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

            $object = new Ouvidoria(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->system_users_id = TSession::getValue('userid');
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
            TApplication::loadPage('OuvidoriaList', 'onShow', $loadPageParam); 

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

                $object = new Ouvidoria($key); // instantiates the Active Record 

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

