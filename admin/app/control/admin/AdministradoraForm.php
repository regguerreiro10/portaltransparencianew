<?php

class AdministradoraForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Administradora';
    private static $primaryKey = 'id';
    private static $formName = 'form_AdministradoraForm';

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
        $this->form->setFormTitle("Cadastro de administradora");

        $criteria_cidade_id = new TCriteria();

        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $cnpj = new TEntry('cnpj');
        $button_buscar_cnpj = new TButton('button_buscar_cnpj');
        $email = new TEntry('email');
        $cep = new TEntry('cep');
        $button_buscar_cep = new TButton('button_buscar_cep');
        $rua = new TEntry('rua');
        $numero = new TEntry('numero');
        $bairro = new TEntry('bairro');
        $complemento = new TEntry('complemento');
        $cidade_id = new TDBCombo('cidade_id', 'minierp', 'Cidade', 'id', '{nome} - {estado->sigla}','nome asc' , $criteria_cidade_id );
        $telefone01 = new TEntry('telefone01');
        $telefone02 = new TEntry('telefone02');


        $id->setEditable(false);
        $cidade_id->enableSearch();
        $button_buscar_cep->setAction(new TAction([$this, 'onBuscarCep']), "Buscar CEP");
        $button_buscar_cnpj->setAction(new TAction([$this, 'onBuscaCNPJ']), "Buscar CNPJ");

        $button_buscar_cep->addStyleClass('btn-default');
        $button_buscar_cnpj->addStyleClass('btn-default');

        $button_buscar_cep->setImage('fas:search #000000');
        $button_buscar_cnpj->setImage('fas:address-card #000000');

        $cep->setMask('99999-999');
        $cnpj->setMask('99.999.999/9999-94');
        $telefone01->setMask('(99)99999-9999');
        $telefone02->setMask('(99)99999-9999');

        $cep->setMaxLength(10);
        $cnpj->setMaxLength(50);
        $rua->setMaxLength(500);
        $nome->setMaxLength(200);
        $email->setMaxLength(150);
        $numero->setMaxLength(10);
        $bairro->setMaxLength(500);
        $telefone01->setMaxLength(255);
        $telefone02->setMaxLength(255);
        $complemento->setMaxLength(500);


        $id->setSize(100);
        $cep->setSize('70%');
        $cnpj->setSize('70%');
        $rua->setSize('100%');
        $nome->setSize('100%');
        $email->setSize('100%');
        $numero->setSize('100%');
        $bairro->setSize('100%');
        $cidade_id->setSize('100%');
        $telefone01->setSize('100%');
        $telefone02->setSize('100%');
        $complemento->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Nome:", null, '14px', null, '100%'),$nome]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Cnpj:", null, '14px', null, '100%'),$cnpj,$button_buscar_cnpj],[new TLabel("Email:", null, '14px', null, '100%'),$email]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Cep:", null, '14px', null, '100%'),$cep,$button_buscar_cep],[new TLabel("Rua:", null, '14px', null, '100%'),$rua]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Numero:", null, '14px', null, '100%'),$numero],[new TLabel("Bairro:", null, '14px', null, '100%'),$bairro]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Complemento:", null, '14px', null, '100%'),$complemento],[new TLabel("Cidade id:", null, '14px', null, '100%'),$cidade_id]);
        $row5->layout = ['col-sm-6','col-sm-6'];

        $row6 = $this->form->addFields([new TLabel("Telefone01:", null, '14px', null, '100%'),$telefone01],[new TLabel("Telefone02:", null, '14px', null, '100%'),$telefone02]);
        $row6->layout = ['col-sm-6','col-sm-6'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['AdministradoraList', 'onShow']), 'fas:arrow-left #000000');
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
        $style = new TStyle('right-panel > .container-part[page-name=AdministradoraForm]');
        $style->width = '50% !important';   
        $style->show(true);

    }

    public  function onBuscaCNPJ($param = null) 
    {
        try 
        {
            //code here

            TTransaction::open(self::$database);

            $data = $this->form->getData(); // get form data as array
            $object = new Administradora();
            $object->fromArray( (array) $data); // load the object with data

            $cnpj = str_replace(['.','-',' ','/'],['','',''], $param['cnpj']);
            $dados = CNPJService::get($cnpj);
           // var_dump($dados,$cnpj);

            //$object = new stdClass();

            //dados principais
            $object->nome = $dados->razao_social;
            $object->telefone01 = $dados->ddd_telefone_1;

            // dados relacionados ao endereço
            $object->cep = $dados->cep;
            $object->rua = $dados->logradouro;
            $object->bairro = $dados->bairro;
            $object->numero = $dados->numero;
            $object->complemento = $dados->complemento;

            $idcidade = 0;
            $cidadeestado = ViewCidadeestado::where('nomecidade','=',strtoupper(trim($dados->municipio)) )
                                             ->where('sigla','=',strtoupper(trim($dados->uf)))
                                             ->load();
            if ($cidadeestado) {
                foreach ($cidadeestado as $cidest)
                {
                    $idcidade = $cidest->idcidade;
                }
            }                                             

            $object->cidade_id = $idcidade;
/*
            $this->fieldList_66772d13ad2ad_items = $this->loadItems('DepartamentoUnit', 'system_unit_id', $object, $this->fieldList_66772d13ad2ad, function($masterObject, $detailObject, $objectItems){ //</blockLine>
            }, $this->criteria_fieldList_66772d13ad2ad); //</blockLine>
*/
            TForm::sendData(self::$formName, $object);     //code here

            TTransaction::close();

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public  function onBuscarCep($param = null) 
    {
        try 
        {
            //code here
            if(!empty($param['cep']))
            {
                TTransaction::open(self::$database);
                $dadosCep = CEPService::get($param['cep']);

                if($dadosCep)
                {
                    $object = new stdClass();
                    $object->cidade_id = $dadosCep->cidade_id;
                    $object->cep = $dadosCep->cep;
                    $object->rua = $dadosCep->rua;
                    $object->bairro = $dadosCep->bairro;

                    // Código gerado pelo snippet: "Recarregar combo"

                   // TCombo::reload(self::$formName, 'pessoa_endereco_pessoa_cidade_estado_id', Estado::getIndexedArray('id', 'nome'), true);
                    // -----

                    TForm::sendData(self::$formName, $object);    
                }

                TTransaction::close();
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Administradora(); // create an empty object 

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
            TApplication::loadPage('AdministradoraList', 'onShow', $loadPageParam); 

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

                $object = new Administradora($key); // instantiates the Active Record 

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

