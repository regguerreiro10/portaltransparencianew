<?php

//<fileHeader>

//</fileHeader>

class AutorizarPedidoForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'AutorizacaoPedido';
    private static $primaryKey = 'id';
    private static $formName = 'form_AutorizarPedidoForm';

    //<classProperties>

    //</classProperties>

    use Adianti\Base\AdiantiFileSaveTrait;
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
        $this->form->setFormTitle("Cadastro de autorização de pedido");

        $criteria_system_users_id = new TCriteria();

        //<onBeginPageCreation>

        //</onBeginPageCreation>

        $id = new TEntry('id');
        $system_users_id = new TDBCombo('system_users_id', 'minierp', 'SystemUsers', 'id', '{name}','name asc' , $criteria_system_users_id );
        $data_autorizacao = new TDate('data_autorizacao');
        $historico = new TText('historico');
        $documento_autorizacao_pedido_autorizacao_pedido_id = new THidden('documento_autorizacao_pedido_autorizacao_pedido_id[]');
        $documento_autorizacao_pedido_autorizacao_pedido___row__id = new THidden('documento_autorizacao_pedido_autorizacao_pedido___row__id[]');
        $documento_autorizacao_pedido_autorizacao_pedido___row__data = new THidden('documento_autorizacao_pedido_autorizacao_pedido___row__data[]');
        $documento_autorizacao_pedido_autorizacao_pedido_caminho = new TFile('documento_autorizacao_pedido_autorizacao_pedido_caminho[]');
        $this->fieldList_682e245f16109 = new TFieldList();

        $this->fieldList_682e245f16109->addField(null, $documento_autorizacao_pedido_autorizacao_pedido_id, []);
        $this->fieldList_682e245f16109->addField(null, $documento_autorizacao_pedido_autorizacao_pedido___row__id, ['uniqid' => true]);
        $this->fieldList_682e245f16109->addField(null, $documento_autorizacao_pedido_autorizacao_pedido___row__data, []);
        $this->fieldList_682e245f16109->addField(new TLabel("Caminho", '#FF0000', '14px', null), $documento_autorizacao_pedido_autorizacao_pedido_caminho, ['width' => '100%']);

        $this->fieldList_682e245f16109->width = '100%';
        $this->fieldList_682e245f16109->setFieldPrefix('documento_autorizacao_pedido_autorizacao_pedido');
        $this->fieldList_682e245f16109->name = 'fieldList_682e245f16109';

        $this->criteria_fieldList_682e245f16109 = new TCriteria();
        $this->default_item_fieldList_682e245f16109 = new stdClass();

        $this->form->addField($documento_autorizacao_pedido_autorizacao_pedido_id);
        $this->form->addField($documento_autorizacao_pedido_autorizacao_pedido___row__id);
        $this->form->addField($documento_autorizacao_pedido_autorizacao_pedido___row__data);
        $this->form->addField($documento_autorizacao_pedido_autorizacao_pedido_caminho);

        $this->fieldList_682e245f16109->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $system_users_id->addValidation("usuário aprovador", new TRequiredValidator()); 
        $data_autorizacao->addValidation("data de autorização", new TRequiredValidator()); 
        $historico->addValidation("histórico", new TRequiredValidator()); 
        $documento_autorizacao_pedido_autorizacao_pedido_caminho->addValidation("documento de autorização", new TRequiredListValidator()); 

        $id->setEditable(false);
        $system_users_id->enableSearch();
        $data_autorizacao->setMask('dd/mm/yyyy');
        $data_autorizacao->setDatabaseMask('yyyy-mm-dd');
        $documento_autorizacao_pedido_autorizacao_pedido_caminho->enableFileHandling();
        $id->setSize(100);
        $data_autorizacao->setSize(110);
        $historico->setSize('100%', 70);
        $system_users_id->setSize('100%');
        $documento_autorizacao_pedido_autorizacao_pedido_caminho->setSize('100%');

        //<onBeforeAddFieldsToForm>

        //</onBeforeAddFieldsToForm>

        $tab_682e23c116103 = new BootstrapFormBuilder('tab_682e23c116103');
        $this->tab_682e23c116103 = $tab_682e23c116103;
        $tab_682e23c116103->setProperty('style', 'border:none; box-shadow:none;');

        $tab_682e23c116103->appendPage("Dados da autorização");

        $tab_682e23c116103->addFields([new THidden('current_tab_tab_682e23c116103')]);
        $tab_682e23c116103->setTabFunction("$('[name=current_tab_tab_682e23c116103]').val($(this).attr('data-current_page'));");

        $row1 = $tab_682e23c116103->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Usuário: *", '#FF0000', '14px', null, '100%'),$system_users_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $tab_682e23c116103->addFields([new TLabel("Data autorização:", '#FF0000', '14px', null, '100%'),$data_autorizacao],[new TLabel("Histórico:", '#FF0000', '14px', null, '100%'),$historico]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $tab_682e23c116103->appendPage("Documento de autorização");
        $row3 = $tab_682e23c116103->addFields([$this->fieldList_682e245f16109]);
        $row3->layout = [' col-sm-12'];

        $row4 = $this->form->addFields([$tab_682e23c116103]);
        $row4->layout = [' col-sm-12'];

        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['AutorizarPedidoList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Manutenção Frotas","Cadastro de autorização de pedido"]));
        }
        $container->add($this->form);

        //<onAfterPageCreation>

        //</onAfterPageCreation>

        parent::add($container);

    }

//<generated-FormAction-onSave>
    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new AutorizacaoPedido(); // create an empty object //</blockLine>

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data
            $object->veiculos_id = TSession::getValue('idveiculoaut');
            $object->pedido_frotas_id = TSession::getValue('idpedidoaut');

            $aprovadores = AprovadorFrotas::where('system_users_id','=', $object->system_users_id)->load();
            $aprovador = $aprovadores[0] ?? null;
            if ($aprovador) {
                $estado_pedido = EstadoPedidoFrotasAprovador::where('aprovador_frotas_id','=', $aprovador->id)
                                                            ->where('estado_pedido_frotas_id','=', EstadoPedidoFrotas::VALORVENAL)
                                                            ->load();
                if (!$estado_pedido) {
                    throw new Exception('Usuário não tem autorização para liberar esse pedido. Contacte o administrador do sistema!');
                }
            } else {
                throw new Exception('Usuário não tem autorização para liberar esse pedido. Contacte o administrador do sistema!');
            }

            //</beforeStoreAutoCode> //</blockLine> 
//<generatedAutoCode>

            $documento_autorizacao_pedido_autorizacao_pedido_caminho_dir = 'app/documentos/autorizacao';
//</generatedAutoCode> 

            $object->store(); // save the object //</blockLine>

            //</afterStoreAutoCode> //</blockLine>
 //<generatedAutoCode>

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

//</generatedAutoCode>

    //<fieldList-2628764-21477500> //</hideLine>
            $documento_autorizacao_pedido_autorizacao_pedido_items = $this->storeItems('DocumentoAutorizacaoPedido', 'autorizacao_pedido_id', $object, $this->fieldList_682e245f16109, function($masterObject, $detailObject){ //</blockLine>

                //code here

                //</autoCode>
            }, $this->criteria_fieldList_682e245f16109); //</blockLine>
    //</hideLine> //</fieldList-2628764-21477500>
//<generatedAutoCode>
            if(!empty($documento_autorizacao_pedido_autorizacao_pedido_items))
            {
                foreach ($documento_autorizacao_pedido_autorizacao_pedido_items as $item)
                {
                    $dataFile = new stdClass();
                    $dataFile->caminho = $item->caminho;
                    $this->saveFile($item, $dataFile, 'caminho', $documento_autorizacao_pedido_autorizacao_pedido_caminho_dir);
                }
            }

//</generatedAutoCode>

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; //</blockLine>

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            //</messageAutoCode> //</blockLine>
//<generatedAutoCode>
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('AutorizarPedidoList', 'onShow', $loadPageParam);
//</generatedAutoCode>

            //</endTryAutoCode> //</blockLine>
//<generatedAutoCode>

            TForm::sendData(self::$formName, (object)['id' => $object->id]);

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

                $object = new AutorizacaoPedido($key); // instantiates the Active Record //</blockLine>

                //</beforeSetDataAutoCode> //</blockLine> 

    //<fieldList-2628764-21477500> //</hideLine>
                $this->fieldList_682e245f16109_items = $this->loadItems('DocumentoAutorizacaoPedido', 'autorizacao_pedido_id', $object, $this->fieldList_682e245f16109, function($masterObject, $detailObject, $objectItems){ //</blockLine>

                    //code here

                    //</autoCode>
                }, $this->criteria_fieldList_682e245f16109); //</blockLine>
    //</hideLine> //</fieldList-2628764-21477500>

                $this->form->setData($object); // fill the form //</blockLine>

                //</afterSetDataAutoCode> //</blockLine>
//<generatedAutoCode>

//</generatedAutoCode>

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

        $this->fieldList_682e245f16109->addHeader();
        $this->fieldList_682e245f16109->addDetail($this->default_item_fieldList_682e245f16109);

        $this->fieldList_682e245f16109->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        //<onFormClear>

        //</onFormClear>

    }

    public function onShow($param = null)
    {
        $this->fieldList_682e245f16109->addHeader();
        $this->fieldList_682e245f16109->addDetail($this->default_item_fieldList_682e245f16109);

        $this->fieldList_682e245f16109->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

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