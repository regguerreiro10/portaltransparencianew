<?php

class PedidoVendaGerarFinanceiroForm extends TWindow
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Pedido';
    private static $primaryKey = 'id';
    private static $formName = 'form_PedidoVendaGerarFinanceiroForm';

    use BuilderMasterDetailFieldListTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(0.60, null);
        parent::setTitle("Gerar financeiro do pedido de venda");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Gerar financeiro do pedido de venda");

        $criteria_condicao_pagamento_id = new TCriteria();
        $criteria_forma_pagamento_id = new TCriteria();
        $criteria_departamento_unit_id = new TCriteria();
        $criteria_conta_pedido_venda_forma_pagamento_id = new TCriteria();

        $id = new TEntry('id');
        $descricaopedido = new TEntry('descricaopedido');
        $valor_total = new TNumeric('valor_total', '2', ',', '.' );
        $condicao_pagamento_id = new TDBCombo('condicao_pagamento_id', 'minierp', 'CondicaoPagamento', 'id', '{nome}','nome asc' , $criteria_condicao_pagamento_id );
        $forma_pagamento_id = new TDBCombo('forma_pagamento_id', 'minierp', 'FormaPagamento', 'id', '{nome}','nome asc' , $criteria_forma_pagamento_id );
        $valor_total_cotacao = new TNumeric('valor_total_cotacao', '2', ',', '.' );
        $dt_vencimento_inicial = new TDate('dt_vencimento_inicial');
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_departamento_unit_id );
        $button_gerar_contas = new TButton('button_gerar_contas');
        $conta_pedido_venda_id = new THidden('conta_pedido_venda_id[]');
        $conta_pedido_venda___row__id = new THidden('conta_pedido_venda___row__id[]');
        $conta_pedido_venda___row__data = new THidden('conta_pedido_venda___row__data[]');
        $conta_pedido_venda_descricao = new TEntry('conta_pedido_venda_descricao[]');
        $conta_pedido_venda_parcela = new TEntry('conta_pedido_venda_parcela[]');
        $conta_pedido_venda_forma_pagamento_id = new TDBCombo('conta_pedido_venda_forma_pagamento_id[]', 'minierp', 'FormaPagamento', 'id', '{nome}','nome asc' , $criteria_conta_pedido_venda_forma_pagamento_id );
        $conta_pedido_venda_dt_vencimento = new TDate('conta_pedido_venda_dt_vencimento[]');
        $conta_pedido_venda_valor = new TNumeric('conta_pedido_venda_valor[]', '2', ',', '.' );
        $this->fieldlist_contas = new TFieldList();

        $this->fieldlist_contas->addField(null, $conta_pedido_venda_id, []);
        $this->fieldlist_contas->addField(null, $conta_pedido_venda___row__id, ['uniqid' => true]);
        $this->fieldlist_contas->addField(null, $conta_pedido_venda___row__data, []);
        $this->fieldlist_contas->addField(new TLabel("Descrição da Conta", null, '14px', null), $conta_pedido_venda_descricao, ['width' => '25%']);
        $this->fieldlist_contas->addField(new TLabel("Parcela", null, '14px', null), $conta_pedido_venda_parcela, ['width' => '10%']);
        $this->fieldlist_contas->addField(new TLabel("Forma pagamento", null, '14px', null), $conta_pedido_venda_forma_pagamento_id, ['width' => '25%']);
        $this->fieldlist_contas->addField(new TLabel("Data vcto", null, '14px', null), $conta_pedido_venda_dt_vencimento, ['width' => '25%']);
        $this->fieldlist_contas->addField(new TLabel("Valor", null, '14px', null), $conta_pedido_venda_valor, ['width' => '40%']);

        $this->fieldlist_contas->width = '100%';
        $this->fieldlist_contas->setFieldPrefix('conta_pedido_venda');
        $this->fieldlist_contas->name = 'fieldlist_contas';

        $this->criteria_fieldlist_contas = new TCriteria();
        $this->default_item_fieldlist_contas = new stdClass();

        $this->form->addField($conta_pedido_venda_id);
        $this->form->addField($conta_pedido_venda___row__id);
        $this->form->addField($conta_pedido_venda___row__data);
        $this->form->addField($conta_pedido_venda_descricao);
        $this->form->addField($conta_pedido_venda_parcela);
        $this->form->addField($conta_pedido_venda_forma_pagamento_id);
        $this->form->addField($conta_pedido_venda_dt_vencimento);
        $this->form->addField($conta_pedido_venda_valor);

        $this->fieldlist_contas->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $condicao_pagamento_id->addValidation("Condição de pagamento", new TRequiredValidator()); 
        $forma_pagamento_id->addValidation("Forma de pagamento", new TRequiredValidator()); 
        $dt_vencimento_inicial->addValidation("Data vencimento inicial", new TRequiredValidator()); 
        $conta_pedido_venda_forma_pagamento_id->addValidation("Forma de pagamento", new TRequiredListValidator()); 

        $dt_vencimento_inicial->setValue(date('d/m/Y'));
        $button_gerar_contas->setAction(new TAction([$this, 'onGerarContas']), "Gerar Contas");
        $button_gerar_contas->addStyleClass('btn-default');
        $button_gerar_contas->setImage('fas:cogs #4CAF50');
        $dt_vencimento_inicial->setMask('dd/mm/yyyy');
        $conta_pedido_venda_dt_vencimento->setMask('dd/mm/yyyy');

        $dt_vencimento_inicial->setDatabaseMask('yyyy-mm-dd');
        $conta_pedido_venda_dt_vencimento->setDatabaseMask('yyyy-mm-dd');

        $forma_pagamento_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $condicao_pagamento_id->enableSearch();
        $conta_pedido_venda_forma_pagamento_id->enableSearch();

        $id->setEditable(false);
        $valor_total->setEditable(false);
        $descricaopedido->setEditable(false);
        $valor_total_cotacao->setEditable(false);
        $departamento_unit_id->setEditable(false);

        $id->setSize('100%');
        $valor_total->setSize('100%');
        $descricaopedido->setSize('100%');
        $forma_pagamento_id->setSize('100%');
        $dt_vencimento_inicial->setSize(170);
        $valor_total_cotacao->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $condicao_pagamento_id->setSize('100%');
        $conta_pedido_venda_valor->setSize('100%');
        $conta_pedido_venda_parcela->setSize('100%');
        $conta_pedido_venda_descricao->setSize('100%');
        $conta_pedido_venda_dt_vencimento->setSize(110);
        $conta_pedido_venda_forma_pagamento_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Código Pedido de Venda", null, '14px', null, '100%'),$id],[new TLabel("Descrição do pedido", null, '14px', null, '100%'),$descricaopedido]);
        $row1->layout = ['col-sm-4',' col-sm-8'];

        $row2 = $this->form->addFields([new TLabel("Valor total:", '#FF0000', '14px', null, '100%'),$valor_total],[new TLabel("Condição de pagamento:", '#FF0000', '14px', null),$condicao_pagamento_id],[new TLabel("Forma de pagamento:", '#FF0000', '14px', null),$forma_pagamento_id]);
        $row2->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row3 = $this->form->addFields([new TLabel("Valor Total da Cotação", '#FF0000', '14px', null),$valor_total_cotacao],[new TLabel("Data vencimento inicial:", '#FF0000', '14px', null),$dt_vencimento_inicial],[new TLabel("Departamentos / Secretarias", '#FF0000', '14px', null),$departamento_unit_id]);
        $row3->layout = ['col-sm-4','col-sm-4',' col-sm-4'];

        $row4 = $this->form->addFields([new TFormSeparator("", '#333', '18', '#eee')]);
        $row4->layout = [' col-sm-12'];

        $row5 = $this->form->addFields([],[],[],[$button_gerar_contas]);
        $row5->layout = ['col-sm-4','col-sm-4',' col-sm-2','col-sm-2'];

        $row6 = $this->form->addContent([new TFormSeparator("Contas", '#333', '18', '#eee')]);
        $row7 = $this->form->addFields([$this->fieldlist_contas]);
        $row7->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        parent::add($this->form);

    }

    public static function onGerarContas($param = null) 
    {
        try 
        {

            (new TRequiredValidator)->validate('Valor total', $param['valor_total_cotacao'] );
            (new TRequiredValidator)->validate('Data vencimento inicial', $param['dt_vencimento_inicial']);
            (new TRequiredValidator)->validate('Condição de pagamento', $param['condicao_pagamento_id']);
            (new TRequiredValidator)->validate('Forma de pagamento', $param['forma_pagamento_id']);

            //popula as variáveis com os valores que vem por parâmetro 
            $valor_total = (double) str_replace(',', '.', str_replace('.', '', $param['valor_total_cotacao']));
            $forma_pagamento_id = $param['forma_pagamento_id'];
            $condicao_pagamento_id = $param['condicao_pagamento_id'];

            $dt_vencimento_inicial = TDate::date2us($param['dt_vencimento_inicial']);
            $dt_vencimento_inicial = date($dt_vencimento_inicial, strtotime("+35 days"));

            TTransaction::open(self::$database);

            $condicaoPagamento = new CondicaoPagamento($condicao_pagamento_id);

            TTransaction::close();

            // calcula o valor da parcela
            $valorParcela = $valor_total / $condicaoPagamento->numero_parcelas;

            // transforma a data de vencimento em um objeto da classe DateTime
            $dt_vencimento = new DateTime($dt_vencimento_inicial);
            $dt_vencimento->add(new DateInterval("P{$condicaoPagamento->inicio}D"));

            $data = new stdClass();
            $data->conta_pedido_venda_valor = [];
            $data->conta_pedido_venda_parcela = [];
            $data->conta_pedido_venda_dt_vencimento = [];
            $data->conta_pedido_venda_forma_pagamento_id = [];

            for($i = 0 ; $i < $condicaoPagamento->numero_parcelas; $i++)
            {
                // populando o array das propriedades do fieldlist
                $data->conta_pedido_venda_descricao[] = $param['descricaopedido'];
                $data->conta_pedido_venda_valor[] = number_format($valorParcela, 2, ',','.');
                $data->conta_pedido_venda_parcela[] = $i+1;
                $data->conta_pedido_venda_dt_vencimento[] = $dt_vencimento->format('d/m/Y');
                $data->conta_pedido_venda_forma_pagamento_id[] = $forma_pagamento_id;

                // acrescenta X dias na data de vencimento
                $dt_vencimento->add(new DateInterval("P{$condicaoPagamento->intervalo}D"));
            }

            // limpa o TFieldList
            // o primeiro parâmetro é o nome da variável definida para o TFieldList
            TFieldList::clearRows('fieldlist_contas');
            // adicionamos as linhas novas
            // primeiro parâmetro é o nome da variável definida para o TFieldList
            TFieldList::addRows('fieldlist_contas', $condicaoPagamento->numero_parcelas - 1, 1);
            // enviando os dados para o field list
            TForm::sendData(self::$formName, $data, false, true, 500);

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

            $object = new Pedido(); // create an empty object 

            $data = $this->form->getData(); // get form data as array

            $object = $object->load($data->id);

            /*
            $object->store(); // save the object 
            */
            /*

            $messageAction = new TAction(['PedidoVendaPendenteList', 'onShow']);   

            if(!empty($param['target_container']))
            {
                $messageAction->setParameter('target_container', $param['target_container']);
            }

            */
            $messageAction = new TAction(['PedidoCompraSeguradoraList', 'onSetProject']);   

            if(!empty($param['target_container']))
            {
                $messageAction->setParameter('target_container', $param['target_container']);
            }

            $conta_pedido_venda_items = $this->storeItems('Conta', 'pedido_venda_id', $object, $this->fieldlist_contas, function($masterObject, $detailObject){ 
                $detailObject->pessoa_id = $masterObject->cliente_id;
               // $detailObject->descricao = $object->descricaopedido;
                $detailObject->tipo_conta_id = TipoConta::PAGAR;
                $detailObject->dt_emissao = date('Y-m-d');

                //$detailObject->categoria_id = 1;
                $detailObject->departamento_unit_id = $masterObject->departamento_unit_id;
                $detailObject->system_users_id = $masterObject->system_users_id;

            }, $this->criteria_fieldlist_contas); 

            $proximoEstado = EstadoPedido::PGTOAPROVADO; //getProximoEstadoPedidoVenda($object->estado_pedido_venda_id);

            $object->estado_pedido_venda_id = EstadoPedido::PGTOAPROVADO;
            $object->store();

            $this->registrarHistoricoPedido($object);

            $cot = Cotacao::where('pessoa_id','=',TSession::getValue('idpessoafin'))
                          ->where('pedido_id','=',$object->id)
                          ->load();
                if ($cot) {
                    foreach($cot as $cotacao){
                      $cotacao->estado_pedido_id = EstadoPedido::PGTOAPROVADO;
                      $cotacao->store();
                      $this->registrarHistoricoCotacao($cotacao);
                    }
                }
            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            new TMessage('info', "Registro salvo", $messageAction); 

                TWindow::closeWindow(parent::getId());
            TForm::sendData(self::$formName, (object)['id' => $object->id]);

            TApplication::loadPage('PedidoCompraSeguradoraList', 'onSetProject');
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

                $object = new Pedido($key); // instantiates the Active Record 

                TSession::setValue('idpessoafin',NULL);
                TSession::setValue('idpessoafin',$object->cliente_id);

 $value = 0;
 $cotacao1 = Cotacao::where('pedido_id','=',$object->id)
                       ->load();
    if ($cotacao1) { 
       foreach($cotacao1 as $cot1)
       {
            $objects = ItensCotacao::where('cotacao_id','=',$cot1->id)
                                   ->load();

            if ($objects) {
               foreach ($objects as $obj) {
                   // code...
                   $value = $value + $obj->valor_total ;
                }
            }
       }
       $object->valor_total_cotacao = $value;
    }

                $this->fieldlist_contas_items = $this->loadItems('Conta', 'pedido_venda_id', $object, $this->fieldlist_contas, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldlist_contas); 

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

        $this->fieldlist_contas->addHeader();
        $this->fieldlist_contas->addDetail($this->default_item_fieldlist_contas);

        $this->fieldlist_contas->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    }

    public function onShow($param = null)
    {
        $this->fieldlist_contas->addHeader();
        $this->fieldlist_contas->addDetail($this->default_item_fieldlist_contas);

        $this->fieldlist_contas->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

   private function registrarHistoricoPedido($pedido)
    {
        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::PGTOAPROVADO; 
        $hist->aprovador_id = TSession::getValue('iduser');
        $hist->store();
    }

    private function registrarHistoricoCotacao($cotacao)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::PGTOAPROVADO; 
        $histcotacao->aprovador_id = TSession::getValue('iduser');
        $histcotacao->store();
    }

}

