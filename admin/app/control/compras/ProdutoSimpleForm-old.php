<?php

class ProdutoSimpleForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Produto';
    private static $primaryKey = 'id';
    private static $formName = 'form_ProdutoSimpleForm';

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
        $this->form->setFormTitle("ProdutoSimpleForm");


        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $tipo_produto_id = new TCombo('tipo_produto_id');
        $tipo_produto_id->addItems(['1'=>'Produto','2'=>'Serviço']);



        $id->setEditable(false);
        $id->setSize(100);
        $nome->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Nome:", null, '14px', null, '100%'),$nome]);
        $row1->layout = ['col-sm-6',' col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Tipo:", null, '14px', null, '100%'),$tipo_produto_id]);
        $row2->layout = [' col-sm-6'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 60%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Pedido","ProdutoSimpleForm"]));
        }
     //   $container->add($this->form);

        parent::add($container);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Produto(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data
            $object->system_unit_id  = TSession::getValue('idunit');
            $object->tipo_produto_id = $data->tipo_produto_id;
            $object->ativo           = 'T';
            $object->nome            = $data->nome;
            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            new TMessage('info', "Registro salvo", $messageAction); 

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

    // public static function onQuickSave($param = null)
    // {
    //     try
    //     {
            
    //         TTransaction::open(self::$database); // open a transaction
    //    //     return;
    //         $conteudojson = $param['_field_data_json'];
    //         $nome = json_decode($conteudojson);
           
    //         // Captura o tipo correspondente à linha atual
    //         $tipo_array = $param['itens_pedido_frotas_pedido_frotas_tipo'] ?? null;

    //         if ($tipo_array[0] === "")
    //         {
    //             throw new Exception('Tipo de produto não informado.');
    //             return;
    //         }
    //          if ($nome->quick_register_value === "")
    //         {
    //             throw new Exception('Nome do produto não informado.');
    //             return;
    //         }

    //         if ($tipo_array && is_array($tipo_array)) {
    //             // Você pode usar a primeira posição (ou buscar pela chave correta da linha se necessário)
    //             $tipo = $tipo_array[0];
    //         } else {
    //             $tipo = null;
    //         }

    //         $object = new Produto(); // create an empty object
    //         $object->system_unit_id = TSession::getValue('idunit');
    //         $object->tipo_produto_id = TSession::getValue('tipo');
    //         $object->ativo = 'T';
    //         $object->nome = $nome->quick_register_value;

    //         //<onBeforeStoreQuickSave>

    //         //</onBeforeStoreQuickSave>

    //         $object->store();

    //         // 3) Garante que o refresh aconteça SOMENTE no campo que chamou o quick register
    //         // (muitas vezes o handleRefreshComponent usa _field_id pra saber quem atualizar)
    //         if (!empty($param['_field_name']) && empty($param['_field_id']))
    //         {
    //             $base = str_replace('[]', '', $param['_field_name']);
    //             $param['_field_id'] = "{$base}_{$row}";
    //         }


    //         //<onAfterStoreQuickSave>

    //         //</onAfterStoreQuickSave>

    //          BComboNoResultsService::handleRefreshComponent($param, $object);

    //         TTransaction::close();

    //         TToast::show('success', _t('Record saved'), 'topRight', 'far:check-circle');
    //     }
    //     catch (Exception $e)
    //     {
    //         new TMessage('error', $e->getMessage());
    //         TTransaction::rollback();
    //     }
    // }

     public static function onQuickSave($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            // 1) Ler JSON do quick register (Adianti)
            $idBtn = $param['_field_id'] ?? ''; // ex: itens_..._produto_id_3931433_btn
            $selectId = preg_replace('/_btn$/', '', $idBtn);
            $conteudojson = $param['_field_data_json'] ?? '';
            $json = json_decode($conteudojson);

            $nome_produto = $json->quick_register_value ?? null;
            if (empty($nome_produto)) {
                throw new Exception('Nome do produto não informado.');
            }

          

            if (empty($selectId)) {
                throw new Exception('Não foi possível identificar o campo do FieldList (linha ativa).');
            }

            // 3) Descobrir o TIPO da mesma linha (usando o mesmo sufixo do id)
            // Ex: ..._produto_id_3931433 => ..._tipo_3931433
            $tipoFieldId = str_replace('produto_id', 'tipo', $selectId);
            $tipo = $param[$tipoFieldId] ?? null;

            // fallback (se por algum motivo não veio o id da linha como campo)
            if (empty($tipo)) {
                $produto_ids = $param['itens_pedido_frotas_pedido_frotas_produto_id'] ?? [];
                $tipo_array  = $param['itens_pedido_frotas_pedido_frotas_tipo'] ?? [];

                $idx = null;
                foreach ($produto_ids as $i => $v) {
                    if ($v === '' || $v === null) $idx = $i;
                }
                $tipo = (is_array($tipo_array) ? ($tipo_array[$idx] ?? null) : null);
            }

            if (empty($tipo)) {
                throw new Exception('Tipo (Produto/Serviço) não informado na linha ativa.');
            }

            // 4) Salvar Produto
            $object = new Produto();
            $object->system_unit_id  = TSession::getValue('idunit');
            $object->tipo_produto_id = $tipo;
            $object->ativo           = 'T';
            $object->nome            = $nome_produto;
            $object->store();
$props = BComboNoResultsService::getProperties($param);
if (!$props) {
    throw new Exception('Não encontrei as propriedades do Quick Register (noresultsbtnprops).');
}

$fieldId = $props->field_id; // EX: itens_pedido_frotas_pedido_frotas_produto_id_1950853
$val     = (int) $object->id;
$label   = addslashes($object->render($props->column ?? 'nome')); // ou $object->nome

// ✅ garante que o option exista (pra select2 não "apagar")
TCombo::addOption($props->field_form, preg_replace('/\[\]$/', '', $props->field_name), $val, $label);

// ✅ atualiza SOMENTE o select dessa linha, no formulário pai
TScript::create("
setTimeout(function(){
    var w = window, guard = 0;

    // sobe até achar um parent com jQuery
    while (w && !w.jQuery && w.parent && w !== w.parent && guard++ < 10) w = w.parent;
    if (window.top && window.top.jQuery) w = window.top;

    if (!w || !w.jQuery) { console.log('Sem jQuery no parent/top'); return; }

    var \$sel = w.jQuery('#{$fieldId}', w.document);
    if (!\$sel.length) { console.log('Select não encontrado no pai: {$fieldId}'); return; }

    if (\$sel.find('option[value=\"{$val}\"]').length === 0) {
        \$sel.append(new w.Option('{$label}', '{$val}', true, true));
    }

    \$sel.val('{$val}').trigger('change').trigger('change.select2');

    // fecha a janela do quick register depois de setar
    if (w.TWindow && w.TWindow.closeWindow) w.TWindow.closeWindow();
}, 80);
");


            TTransaction::close();

            TToast::show('success', 'Registro salvo e inserido na linha ativa', 'topRight', 'far:check-circle');
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }





}

