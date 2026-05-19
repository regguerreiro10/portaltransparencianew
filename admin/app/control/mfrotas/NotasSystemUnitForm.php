<?php

class NotasSystemUnitForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'NotasSystemUnit';
    private static $primaryKey = 'id';
    private static $formName = 'form_NotasSystemUnitForm';

    use Adianti\Base\AdiantiFileSaveTrait;

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
        $this->form->setFormTitle("Cadastro de documentos fiscais");

        $criteria_departamento_unit_id  = new TCriteria();
        $criteria_departamento_unit_id ->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));

        $id = new THidden('id');
        $mes_ano = new TEntry('mes_ano');
        $valor = new TNumeric('valor', '2', ',', '.' );
        $caminho = new TFile('caminho');
        $notificar = new TCheckButton('notificar');
        $numero = new TEntry('numero');
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_departamento_unit_id );


        $mes_ano->setMask('99/9999', true);
        $caminho->enableFileHandling();
        $notificar->setUseSwitch(true, 'blue');
        $notificar->setIndexValue("1");
        $notificar->setInactiveIndexValue("2");
        $departamento_unit_id->addValidation("Unidades / Dep / Secretárias", new TRequiredValidator()); 
        $departamento_unit_id->enableSearch();
        $departamento_unit_id->setSize('100%');

        $id->setSize(200);
        $valor->setSize('100%');
        $mes_ano->setSize('29%');
        $caminho->setSize('100%');
        $numero->setSize('100%');

        $row1 = $this->form->addFields([$id,new TLabel("Mês/Ano de referência: *", '#FF0000', '14px', null, '100%'),$mes_ano],[new TLabel("Valor: *", '#FF0000', '14px', null, '100%'),$valor]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row20 = $this->form->addFields([new TLabel("Unidades / Dep / Secretárias *",'#FF0000', null, '14px', null, '100%'),$departamento_unit_id],[new TLabel("Número documento: *", '#FF0000', '14px', null),$numero]);
        $row20->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Caminho: *", '#FF0000', '14px', null, '100%'),$caminho],[new TLabel("Enviar email de notificação? *", '#FF0000', '14px', null, '100%'),$notificar]);
        $row2->layout = ['col-sm-6',' col-sm-3',' col-sm-3'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['NotasSystemUnitList', 'onShow']), 'fas:arrow-left #000000');
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

            $object = new NotasSystemUnit(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $caminho_dir = 'app/documentos/notas';  

            $object->system_unit_id = TSession::getValue('idunit');
            $object->store(); // save the object 

            $this->saveFile($object, $data, 'caminho', $caminho_dir);
            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 
            
            
            if ($object->notificar==1) {
                //enviar email de notificação

                $entidade = SystemUnit::where('entidade_id', '=', TSession::getValue('entidade'))
                                       ->where('id', '=', TSession::getValue('idunit'))
                                       ->first();
                if ($entidade) {
                        $emailTemplate = new EmailTemplate(EmailTemplate::NOTIFICACAO_DOCUMENTO);

                        $titulo = $emailTemplate->titulo;
                        $mensagem = $emailTemplate->mensagem;

                        $mensagem = str_replace('{nome_gestor}', $entidade->nome_fantasia, $mensagem);
                        $mensagem = str_replace('{numero}', $object->numero, $mensagem);
                        $mensagem = str_replace('{mes_ano_referencia0}', $object->mes_ano, $mensagem);
                        $mensagem = str_replace('{mes_ano_referencia1}', $object->mes_ano, $mensagem);
                        $mensagem = str_replace('{valor_total}', $object->valor, $mensagem);
                    
                        $titulo = $object->render($titulo);
                        $mensagem = $object->render($mensagem);

                        MailService::send($entidade->email, $titulo, $mensagem, 'html');
                        $menssalvar = "Registro salvo e email enviado com sucesso";
                }
                

                // $aprovadores = []; // Inicializa o array de aprovadores
                //     $usuario = SystemUsers::where('system_unit_id', '=', TSession::getValue('idunit'))
                //                           ->load();
                //     if ($usuario){
                //         foreach ($usuario as $user) {
                //             $pessoa = Pessoa::where('system_user_id', '=', $user->id)
                //                             ->first();
                //             if ($pessoa) {
                //                 continue; // Pula para a próxima iteração se não houver pessoa associada
                //             }
                //             $pessoaemail = Pessoa::where('email', '=', $user->email)
                //                             ->first();
                //             if ($pessoaemail) {
                //                 continue; // Pula para a próxima iteração se não houver pessoa associada
                //             }
                //             $aprovador_frotas = AprovadorFrotas::where('system_users_id', '=', $user->id)
                //                                                 ->load();
                //             if ($aprovador_frotas) {
                //                 // Verifica se o aprovador está ativo e se o estado de pedido é aprovado
                //                 // e se o estado de pedido frotas aprovador está ativo
                //                 foreach ($aprovador_frotas as $aprovador_frotas) {
                //                     $estado_pedido_frotas_aprovador = EstadoPedidoFrotasAprovador::where('aprovador_frotas_id', '=', $aprovador_frotas->id)
                //                                                             ->where('estado_pedido_frotas_id', '=', EstadoPedido::APROVADO)
                //                                                             ->first();
                //                     if ($estado_pedido_frotas_aprovador) {
                //                         //ENVIAR EMAIL PARA OS APROVADORES GUARDAR OS EMAILS EM UM ARRAY 
                //                         $aprovadores[] = $user;
                //                     }
                //                 }                        
                //             } 

                //         }

                //     if ($aprovadores) {
                //         foreach ($aprovadores as $dadosAprovador) {
                //             if ($dadosAprovador) {
                //                 $emailTemplate = new EmailTemplate(EmailTemplate::NOTIFICACAO_DOCUMENTO);

                //                 $titulo = $emailTemplate->titulo;
                //                 $mensagem = $emailTemplate->mensagem;

                //                 $mensagem = str_replace('{nome_gestor}', $dadosAprovador->name, $mensagem);
                //                 $mensagem = str_replace('{numero}', $object->numero, $mensagem);
                //                 $mensagem = str_replace('{mes_ano_referencia0}', $object->mes_ano, $mensagem);
                //                 $mensagem = str_replace('{mes_ano_referencia1}', $object->mes_ano, $mensagem);
                //                 $mensagem = str_replace('{valor_total}', $object->valor, $mensagem);


                //                 $titulo = $object->render($titulo);
                //                 $mensagem = $object->render($mensagem);

                //                 MailService::send($dadosAprovador->email, $titulo, $mensagem, 'html');
                //                 $menssalvar = "Registro salvo e email enviado com sucesso";
                //             }


                //         }
                //     }
                // }
            } else {
            $menssalvar = "Registro salvo";

            }

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', $menssalvar, 'topRight', 'far:check-circle');
            TApplication::loadPage('NotasSystemUnitList', 'onShow', $loadPageParam); 

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

                $object = new NotasSystemUnit($key); // instantiates the Active Record 

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
     public static function onChangesystem_unit_id($param)
    {
        try
        {

            if (isset($param['system_unit_id']) && $param['system_unit_id'])
            { 
                $criteria = TCriteria::create(['system_unit_id' => $param['system_unit_id']]);
                TDBCombo::reloadFromModel(self::$formName, 'departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}', 'name asc', $criteria, TRUE); 
            } 
            else 
            { 
                TCombo::clearField(self::$formName, 'departamento_unit_id'); 
            }  

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    } 

}

