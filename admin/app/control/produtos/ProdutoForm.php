<?php

class ProdutoForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Produto';
    private static $primaryKey = 'id';
    private static $formName = 'form_ProdutoForm';

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
        $this->form->setFormTitle("Cadastro de produto");

        $criteria_tipo_produto_id = new TCriteria();
        $criteria_familia_produto_id = new TCriteria();
        $criteria_fabricante_id = new TCriteria();
        $criteria_unidade_medida_id = new TCriteria();
        $criteria_produto_system_unit_produto_system_unit_id = new TCriteria();

        $foto = new TImageCropper('foto');
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $qtde_estoque = new TNumeric('qtde_estoque', '2', ',', '.' );
        $estoque_minimo = new TNumeric('estoque_minimo', '2', ',', '.' );
        $estoque_maximo = new TNumeric('estoque_maximo', '2', ',', '.' );
        $ativo = new TRadioGroup('ativo');
        $tipo_produto_id = new TDBCombo('tipo_produto_id', 'minierp', 'TipoProduto', 'id', '{nome}','nome asc' , $criteria_tipo_produto_id );
        $familia_produto_id = new TDBCombo('familia_produto_id', 'minierp', 'FamiliaProduto', 'id', '{nome}','nome asc' , $criteria_familia_produto_id );
        $fabricante_id = new TDBCombo('fabricante_id', 'minierp', 'Fabricante', 'id', '{nome}','nome asc' , $criteria_fabricante_id );
        $preco_venda = new TNumeric('preco_venda', '2', ',', '.' );
        $peso_liquido = new TEntry('peso_liquido');
        $peso_bruto = new TEntry('peso_bruto');
        $largura = new TNumeric('largura', '2', ',', '.' );
        $altura = new TNumeric('altura', '2', ',', '.' );
        $unidade_medida_id = new TDBCombo('unidade_medida_id', 'minierp', 'UnidadeMedida', 'id', '{nome}','nome asc' , $criteria_unidade_medida_id );
        $volume = new TNumeric('volume', '2', ',', '.' );
        $obs = new TText('obs');
        $produto_system_unit_produto_id = new THidden('produto_system_unit_produto_id[]');
        $produto_system_unit_produto___row__id = new THidden('produto_system_unit_produto___row__id[]');
        $produto_system_unit_produto___row__data = new THidden('produto_system_unit_produto___row__data[]');
        $produto_system_unit_produto_system_unit_id = new TDBCombo('produto_system_unit_produto_system_unit_id[]', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_produto_system_unit_produto_system_unit_id );
        $this->fieldList_6724b99e26033 = new TFieldList();

        $this->fieldList_6724b99e26033->addField(null, $produto_system_unit_produto_id, []);
        $this->fieldList_6724b99e26033->addField(null, $produto_system_unit_produto___row__id, ['uniqid' => true]);
        $this->fieldList_6724b99e26033->addField(null, $produto_system_unit_produto___row__data, []);
        $this->fieldList_6724b99e26033->addField(new TLabel("Unidade", null, '14px', null), $produto_system_unit_produto_system_unit_id, ['width' => '100%']);

        $this->fieldList_6724b99e26033->width = '100%';
        $this->fieldList_6724b99e26033->setFieldPrefix('produto_system_unit_produto');
        $this->fieldList_6724b99e26033->name = 'fieldList_6724b99e26033';

        $this->criteria_fieldList_6724b99e26033 = new TCriteria();
        $this->default_item_fieldList_6724b99e26033 = new stdClass();

        $this->form->addField($produto_system_unit_produto_id);
        $this->form->addField($produto_system_unit_produto___row__id);
        $this->form->addField($produto_system_unit_produto___row__data);
        $this->form->addField($produto_system_unit_produto_system_unit_id);

        $this->fieldList_6724b99e26033->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $nome->addValidation("Nome", new TRequiredValidator()); 
        $tipo_produto_id->addValidation("Tipo de produto", new TRequiredValidator()); 
        $familia_produto_id->addValidation("Grupo do produto", new TRequiredValidator()); 
        $unidade_medida_id->addValidation("Unidade de medida", new TRequiredValidator()); 

        $foto->enableFileHandling();
        $foto->setAllowedExtensions(["jpg","jpeg","png","gif"]);
        $foto->setImagePlaceholder(new TImage("fas:file-upload #dde5ec"));
        $id->setEditable(false);
        $nome->setMaxLength(255);
        $ativo->addItems(["T"=>"Sim","F"=>"Não"]);
        $ativo->setLayout('horizontal');
        $ativo->setValue('T');
        $ativo->setUseButton();
        $tipo_produto_id->enableSearch();
        $unidade_medida_id->enableSearch();
        $familia_produto_id->enableSearch();
        $produto_system_unit_produto_system_unit_id->enableSearch();

        $id->setSize('100%');
        $nome->setSize('100%');
        $ativo->setSize('100%');
        $foto->setSize(160, 160);
        $altura->setSize('100%');
        $volume->setSize('100%');
        $largura->setSize('100%');
        $obs->setSize('100%', 100);
        $peso_bruto->setSize('100%');
        $preco_venda->setSize('100%');
        $peso_liquido->setSize('100%');
        $fabricante_id->setSize('100%');
        $tipo_produto_id->setSize('100%');
        $unidade_medida_id->setSize('100%');
        $familia_produto_id->setSize('100%');
        $estoque_minimo->setSize('100%');
        $estoque_maximo->setSize('100%');
        $qtde_estoque->setSize('100%');
        $produto_system_unit_produto_system_unit_id->setSize('100%');

        $row1 = $this->form->addContent([new TFormSeparator("Informações gerais", '#333', '18', '#eee')]);

        $bcontainer_62463e0849f16 = new BootstrapFormBuilder('bcontainer_62463e0849f16');
        $this->bcontainer_62463e0849f16 = $bcontainer_62463e0849f16;
        $bcontainer_62463e0849f16->setProperty('style', 'border:none; box-shadow:none;');
        $row2 = $bcontainer_62463e0849f16->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Nome:", '#ff0000', '14px', null, '100%'),$nome]);
        $row2->layout = [' col-sm-6','col-sm-6'];

        $row3 = $bcontainer_62463e0849f16->addFields([new TLabel("Ativo:", null, '14px', null, '100%'),$ativo],[new TLabel("Tipo de produto:", '#ff0000', '14px', null, '100%'),$tipo_produto_id]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([$foto],[$bcontainer_62463e0849f16]);
        $row4->layout = [' col-sm-3',' col-sm-9'];

        $row5 = $this->form->addFields([new TLabel("Grupo do produto:", '#ff0000', '14px', null, '100%'),$familia_produto_id],[new TLabel("Fabricante:", null, '14px', null, '100%'),$fabricante_id]);
        $row5->layout = ['col-sm-6','col-sm-6'];

        $row6 = $this->form->addContent([new TFormSeparator("<br>Preços e Pesos", '#333', '18', '#eee')]);
        $row7 = $this->form->addFields([new TLabel("Preço produto", null, '14px', null, '100%'),$preco_venda],[new TLabel("Peso Líquido", null, '14px', null, '100%'),$peso_liquido],[new TLabel("Peso Bruto", null, '14px', null, '100%'),$peso_bruto]);
        $row7->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row60 = $this->form->addContent([new TFormSeparator("<br>Quantidades", '#333', '18', '#eee')]);
        $row70 = $this->form->addFields([new TLabel("Quantidade", null, '14px', null, '100%'),$qtde_estoque],[new TLabel("Estoque mínimo", null, '14px', null, '100%'),$estoque_minimo],[new TLabel("Estoque máximo", null, '14px', null, '100%'),$estoque_maximo]);
        $row70->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row8 = $this->form->addContent([new TFormSeparator("<br>Pesos e medidas", '#333', '18', '#eee')]);
        $row9 = $this->form->addFields([new TLabel("Largura:", null, '14px', null, '100%'),$largura],[new TLabel("Altura:", null, '14px', null, '100%'),$altura]);
        $row9->layout = ['col-sm-6','col-sm-6'];

        $row10 = $this->form->addFields([new TLabel("Unidade de medida:", '#ff0000', '14px', null, '100%'),$unidade_medida_id],[new TLabel("Volume:", null, '14px', null, '100%'),$volume]);
        $row10->layout = ['col-sm-6',' col-sm-6'];

        $row11 = $this->form->addFields([new TLabel("Observações", null, '14px', null, '100%'),$obs]);
        $row11->layout = [' col-sm-12'];

        // $row12 = $this->form->addFields([new TFormSeparator("<br>Unidades que este produto pertence", '#333', '18', '#eee')]);
        // $row12->layout = [' col-sm-12'];

        // $row13 = $this->form->addFields([$this->fieldList_6724b99e26033]);
        // $row13->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['ProdutoList', 'onShow']), 'fas:arrow-left #000000');
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

        $style = new TStyle('right-panel > .container-part[page-name=ProdutoForm]');
        $style->width = '80% !important';   
        $style->show(true);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

           // $this->form->validate(); // validate form data

            $object = new Produto(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $foto_dir = 'app/fotos/produtos';  

            $object->system_users_id = TSession::getValue('userid');
                        $object->system_unit_id = TSession::getValue('idunit');

            $object->store(); // save the object 

          //  $this->saveFile($object, $data, 'foto', $foto_dir);
            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            $produto_system_unit_produto_items = $this->storeItems('ProdutoSystemUnit', 'produto_id', $object, $this->fieldList_6724b99e26033, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_6724b99e26033); 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('ProdutoList', 'onShow', $loadPageParam); 

                        TScript::create("Template.closeRightPanel();");
            TForm::sendData(self::$formName, (object)['id' => $object->id]);

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

                $this->fieldList_6724b99e26033_items = $this->loadItems('ProdutoSystemUnit', 'produto_id', $object, $this->fieldList_6724b99e26033, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_6724b99e26033); 

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

        $this->fieldList_6724b99e26033->addHeader();
        $this->fieldList_6724b99e26033->addDetail($this->default_item_fieldList_6724b99e26033);

        $this->fieldList_6724b99e26033->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    }

    public function onShow($param = null)
    {
        $this->fieldList_6724b99e26033->addHeader();
        $this->fieldList_6724b99e26033->addDetail($this->default_item_fieldList_6724b99e26033);

        $this->fieldList_6724b99e26033->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

