<?php

class PedidoFornecedorForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Pedido';
    private static $primaryKey = 'id';
    private static $formName = 'form_PedidoFornecedorForm';

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
        $this->form->setFormTitle("PedidoFornecedorForm");

        $criteria_cliente_id = new TCriteria();
        $criteria_departamento_unit_id = new TCriteria();
        $criteria_itens_pedido_pedido_venda_produto_id = new TCriteria();

        $filterVar = TSession::getValue("userid");
        $criteria_cliente_id->add(new TFilter('system_user_id', '=', $filterVar)); 
        $filterVar = TSession::getValue("userid");
        $criteria_departamento_unit_id->add(new TFilter('system_users_id', '=', $filterVar)); 

        $id = new TEntry('id');
        $dt_pedido = new TDate('dt_pedido');
        $descricaopedido = new TEntry('descricaopedido');
        $cliente_id = new TDBCombo('cliente_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_cliente_id );
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'SystemUserDepartamentoUnit', 'departamento_unit_id', '{system_users->name} - {departamento_unit->name}','departamento_unit_id asc' , $criteria_departamento_unit_id );
        $obs = new TText('obs');
        $itens_pedido_pedido_venda_id = new THidden('itens_pedido_pedido_venda_id[]');
        $itens_pedido_pedido_venda___row__id = new THidden('itens_pedido_pedido_venda___row__id[]');
        $itens_pedido_pedido_venda___row__data = new THidden('itens_pedido_pedido_venda___row__data[]');
        $itens_pedido_pedido_venda_produto_id = new TDBUniqueSearch('itens_pedido_pedido_venda_produto_id[]', 'minierp', 'Produto', 'id', 'nome','nome asc' , $criteria_itens_pedido_pedido_venda_produto_id );
        $itens_pedido_pedido_venda_quantidade = new TNumeric('itens_pedido_pedido_venda_quantidade[]', '2', ',', '.' );
        $itens_pedido_pedido_venda_valor = new TNumeric('itens_pedido_pedido_venda_valor[]', '2', ',', '.' );
        $itens_pedido_pedido_venda_valor_total = new TNumeric('itens_pedido_pedido_venda_valor_total[]', '2', ',', '.' );
        $itens_pedido_pedido_venda_valor_cotacao = new TNumeric('itens_pedido_pedido_venda_valor_cotacao[]', '2', ',', '.' );
        $itens_pedido_pedido_venda_valor_cotacao_total = new TNumeric('itens_pedido_pedido_venda_valor_cotacao_total[]', '2', ',', '.' );
        $this->fieldList_669924ff82798 = new TFieldList();

        $this->fieldList_669924ff82798->addField(null, $itens_pedido_pedido_venda_id, []);
        $this->fieldList_669924ff82798->addField(null, $itens_pedido_pedido_venda___row__id, ['uniqid' => true]);
        $this->fieldList_669924ff82798->addField(null, $itens_pedido_pedido_venda___row__data, []);
        $this->fieldList_669924ff82798->addField(new TLabel("Produto", null, '14px', null), $itens_pedido_pedido_venda_produto_id, ['width' => '20%']);
        $this->fieldList_669924ff82798->addField(new TLabel("Quantidade", null, '14px', null), $itens_pedido_pedido_venda_quantidade, ['width' => '10%','sum' => true]);
        $this->fieldList_669924ff82798->addField(new TLabel("Valor", null, '14px', null), $itens_pedido_pedido_venda_valor, ['width' => '15%']);
        $this->fieldList_669924ff82798->addField(new TLabel("Valor total", null, '14px', null), $itens_pedido_pedido_venda_valor_total, ['width' => '15%','sum' => true]);
        $this->fieldList_669924ff82798->addField(new TLabel("Valor Cotacao", null, '14px', null), $itens_pedido_pedido_venda_valor_cotacao, ['width' => '15%']);
        $this->fieldList_669924ff82798->addField(new TLabel("Vl Cotação total", null, '14px', null), $itens_pedido_pedido_venda_valor_cotacao_total, ['width' => '15%','sum' => true]);

        $this->fieldList_669924ff82798->width = '100%';
        $this->fieldList_669924ff82798->setFieldPrefix('itens_pedido_pedido_venda');
        $this->fieldList_669924ff82798->name = 'fieldList_669924ff82798';

        $this->criteria_fieldList_669924ff82798 = new TCriteria();
        $this->default_item_fieldList_669924ff82798 = new stdClass();

        $this->form->addField($itens_pedido_pedido_venda_id);
        $this->form->addField($itens_pedido_pedido_venda___row__id);
        $this->form->addField($itens_pedido_pedido_venda___row__data);
        $this->form->addField($itens_pedido_pedido_venda_produto_id);
        $this->form->addField($itens_pedido_pedido_venda_quantidade);
        $this->form->addField($itens_pedido_pedido_venda_valor);
        $this->form->addField($itens_pedido_pedido_venda_valor_total);
        $this->form->addField($itens_pedido_pedido_venda_valor_cotacao);
        $this->form->addField($itens_pedido_pedido_venda_valor_cotacao_total);

        $this->fieldList_669924ff82798->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $itens_pedido_pedido_venda_produto_id->setChangeAction(new TAction([$this,'onBuscaProduto']));

        $itens_pedido_pedido_venda_quantidade->setExitAction(new TAction([$this,'onCalculaValor']));
        $itens_pedido_pedido_venda_valor_cotacao->setExitAction(new TAction([$this,'onMudaValor']));

        $dt_pedido->addValidation("Data", new TRequiredValidator()); 
        $descricaopedido->addValidation("Nome/Titulo", new TRequiredValidator()); 
        $cliente_id->addValidation("Fornecedor", new TRequiredValidator()); 
        $departamento_unit_id->addValidation("Departamento", new TRequiredValidator()); 

        $dt_pedido->setDatabaseMask('yyyy-mm-dd');
        $descricaopedido->setMaxLength(60);
        $itens_pedido_pedido_venda_produto_id->setMinLength(2);
        $dt_pedido->setMask('dd/mm/yyyy');
        $itens_pedido_pedido_venda_produto_id->setMask('{nome}');

        $cliente_id->enableSearch();
        $departamento_unit_id->enableSearch();

        $id->setEditable(false);
        $itens_pedido_pedido_venda_valor->setEditable(false);
        $itens_pedido_pedido_venda_valor_total->setEditable(false);

        $id->setSize(100);
        $dt_pedido->setSize(110);
        $obs->setSize('100%', 70);
        $cliente_id->setSize('100%');
        $descricaopedido->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $itens_pedido_pedido_venda_valor->setSize('100%');
        $itens_pedido_pedido_venda_produto_id->setSize(350);
        $itens_pedido_pedido_venda_quantidade->setSize('100%');
        $itens_pedido_pedido_venda_valor_total->setSize('100%');
        $itens_pedido_pedido_venda_valor_cotacao->setSize('100%');
        $itens_pedido_pedido_venda_valor_cotacao_total->setSize('100%');

        $tab_66982e2340ffa = new BootstrapFormBuilder('tab_66982e2340ffa');
        $this->tab_66982e2340ffa = $tab_66982e2340ffa;
        $tab_66982e2340ffa->setProperty('style', 'border:none; box-shadow:none;');

        $tab_66982e2340ffa->appendPage("Dados/Itens");

        $tab_66982e2340ffa->addFields([new THidden('current_tab_tab_66982e2340ffa')]);
        $tab_66982e2340ffa->setTabFunction("$('[name=current_tab_tab_66982e2340ffa]').val($(this).attr('data-current_page'));");

        $row1 = $tab_66982e2340ffa->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Dt Pedido", '#FF0000', '14px', null, '100%'),$dt_pedido]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $tab_66982e2340ffa->addFields([new TLabel("Nome ou titulo deste pedido para localização futura:", '#FF0000', '14px', null, '100%'),$descricaopedido],[new TLabel("Fornecedor", '#FF0000', '14px', null, '100%'),$cliente_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $tab_66982e2340ffa->addFields([new TLabel("Departamento / Secretárias", '#FF0000', '14px', null, '100%'),$departamento_unit_id],[new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row3->layout = ['col-sm-6',' col-sm-6'];

        $row4 = $tab_66982e2340ffa->addFields([new TFormSeparator("Produtos", '#333', '18', '#eee')]);
        $row4->layout = [' col-sm-12'];

        $row5 = $tab_66982e2340ffa->addFields([$this->fieldList_669924ff82798]);
        $row5->layout = [' col-sm-12'];

        $row6 = $this->form->addFields([$tab_66982e2340ffa]);
        $row6->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Compras","PedidoFornecedorForm"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public static function onCalculaValor($param = null) 
    {
        try 
        {
            //code here
//code here
            TTransaction::open(self::$database); // open a transaction
            $produto = new Produto(TSession::getValue('idproduto'));
            $id=$param['_field_id'];
            $qtde = $param['_field_value'];
            $qtde=str_replace('.','',$qtde);
            $qtde=str_replace(',','.',$qtde);
            $qtde=(float) $qtde;
            TSession::setValue('qtde',NULL);
            TSession::setValue('qtde',$qtde);

//            $qtde=(float) $param['_field_value'];
           // var_dump($qtde, $param['_field_value']);
        //    die();
           // $valortotal=str_replace('.',',',$produto->preco_venda * $qtde);
               $valortotal=str_replace('.',',',$produto->preco_venda * $qtde);
      //      TScript::create("$(document).grandtotal_itens_pedido_pedido_venda_valor.reload()");
            TScript::create("$('#{$id}').parent().parent().find('[name=\"itens_pedido_pedido_venda_valor_total[]\"]').val('{$valortotal}')");

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onMudaValor($param = null) 
    {
        try 
        {
            //code here
  TTransaction::open(self::$database); // open a transaction
          //  $produto = new Produto(TSession::getValue('idproduto'));
            $id=$param['_field_id'];
            $valor = $param['_field_value'];
            $valor=str_replace('.','',$valor);
            $valor=str_replace(',','.',$valor);
            $valor=(float) $valor;
            $produto = new Produto(TSession::getValue('idproduto'));
            if ($valor > $produto->preco_venda) {
                throw new Exception('Você deve informar valor menor ou igual a tabela SINAPI !!!');
                $valortotal = str_replace('.',',', $produto->preco_venda * TSession::getValue('qtde'));
                TScript::create("$('#{$id}').parent().parent().find('[name=\"itens_pedido_pedido_venda_valor_cotacao[]\"]').val('{$produto->preco_venda}')");
                TScript::create("$('#{$id}').parent().parent().find('[name=\"itens_pedido_pedido_venda_valor_cotacao_total[]\"]').val('{$valortotal}')");
                exit();
             } else {
           // if ($valor > $produto->preco_venda  ) {
         //       $valor=$produto->preco_venda;
         //   }
        //    new TMessage('info','Valor do produto maior que valor da tabela SINAPI');
//            $qtde=(float) $param['_field_value'];
           // var_dump($qtde, $param['_field_value']);
        //    die();
           // $valortotal=str_replace('.',',',$produto->preco_venda * $qtde);
               $valortotal=str_replace('.',',',$valor * TSession::getValue('qtde'));
//            opener.location.reload()
      //      TScript::create("$(document).grandtotal_itens_pedido_pedido_venda_valor.reload()");
            TScript::create("$('#{$id}').parent().parent().find('[name=\"itens_pedido_pedido_venda_valor_cotacao_total[]\"]').val('{$valortotal}')");
             }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onBuscaProduto($param = null) 
    {
        try 
        {
            //code here
 //code here
            TSession::setValue('idproduto', NULL);

            TTransaction::open(self::$database); // open a transaction
            $produto = new Produto($param['_field_value']);
            TSession::setValue('idproduto',$param['_field_value']);
            $id=$param['_field_id'];
            $valor=str_replace('.',',',$produto->preco_venda);
            $valor_cotacao=str_replace('.',',',$produto->preco_venda);
          //  var_dump($valor);
            TScript::create("$('#{$id}').parent().parent().find('[name=\"itens_pedido_pedido_venda_valor[]\"]').val('{$valor}')");
            TScript::create("$('#{$id}').parent().parent().find('[name=\"itens_pedido_pedido_venda_valor_cotacao[]\"]').val('{$valor_cotacao}')");

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
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            $itens_pedido_pedido_venda_items = $this->storeItems('ItensPedido', 'pedido_venda_id', $object, $this->fieldList_669924ff82798, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_669924ff82798); 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $object->estado_pedido_venda_id = EstadoPedido::PENDENTE;
            $object->system_users_id = TSession::getValue('userid');

            $dt_pedido = new DateTime($data->dt_pedido);

            $object->mes = $dt_pedido->format('m');
            $object->ano = $dt_pedido->format('Y');

            $object->valor_total = 0;

            $itenspedido = ItensPedido::where('pedido_venda_id','=',$object->id)
                                      ->load();
            $somatotal=0;
            $somatotalcotacao=0;
            if ($itenspedido){
                foreach ($itenspedido as $itensp){
                    $itensp->valor_total         = $itensp->valor * $itensp->quantidade;
                    $itensp->valor_total_cotacao = $itensp->valor_cotacao * $itensp->quantidade;
                    $itensp->store();
                    $somatotal += ($itensp->valor * $itensp->quantidade);
                    $somatotalcotacao += ($itensp->valor_cotacao * $itensp->quantidade);
                }
            }          
         //   var_dump($somatotal);
            $object->valor_total = $somatotal;
            $object->valor_total_cotacao = $somatotalcotacao;
            $object->store();

            $pessoa = Pessoa::where('system_user_id','=',TSession::getValue('userid'))
                          ->load();
            if ($pessoa) {
                foreach ($pessoa as $pessoas) {
                    $cidade = PessoaEndereco::where('pessoa_id','=',$pessoas->id)
                                            ->load();
                    if ($cidade) {
                        foreach($cidade as $cidades) {
                            $cidadepedido = new CidadePedido();
                            $cidadepedido->pedido_id =  $object->id; 
                            $cidadepedido->cidade_id = $cidades->cidade_id;
                            $cidadepedido->store();
                        }
                    }
                    $seguimento = SeguimentoPessoa::where('pessoa_id','=',$pessoas->id)
                                                  ->load();
                    if ($seguimento) {
                        foreach($seguimento as $seguimentos) {
                            $pedidoseguimento = new PedidoSeguimento();
                            $pedidoseguimento->pedido_id =  $object->id; 
                            $pedidoseguimento->seguimento_id = $seguimentos->id;
                            $pedidoseguimento->store();
                        }
                    }
                }
            }

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            new TMessage('info', "Registro salvo", $messageAction); 

            TForm::sendData(self::$formName, (object)['id' => $object->id]);

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

                $this->fieldList_669924ff82798_items = $this->loadItems('ItensPedido', 'pedido_venda_id', $object, $this->fieldList_669924ff82798, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_669924ff82798); 

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                TTransaction::open(self::$database);

                $this->form->clear();
                $this->onClear($param);

                // TFieldList::clearRows('fieldList_669835e76c280');

                //seguimento -> achar um jeito de colocar mais q um no input

                // TFieldList::clearRows('tab_66982e2340ffa');
                // TFieldList::addRows('tab_66982e2340ffa', $seguimento->descricao, 1);

                //$forn = buscar o id e jogar no campo do pedido;
                //nesse mesmo fornecedor que vc buscou, pegar os dados da cidade(pessoa_endereco) e do seguimento (pessoa_seguimento)
/*
                $this->fieldList_669835e76c280_items = $this->loadItems('CidadePedido', 'pedido_id', $object, $this->fieldList_669835e76c280, function($masterObject, $detailObject, $objectItems){ 
                }, $this->criteria_fieldList_669835e76c280); 

                $this->fieldList_669832bc6c27e_items = $this->loadItems('ItensPedido', 'pedido_venda_id', $object, $this->fieldList_669832bc6c27e, function($masterObject, $detailObject, $objectItems){ 
                }, $this->criteria_fieldList_669832bc6c27e); 

                $this->fieldList_66982f194100c_items = $this->loadItems('DocumentosPedido', 'pedido_id', $object, $this->fieldList_66982f194100c, function($masterObject, $detailObject, $objectItems){ 
                }, $this->criteria_fieldList_66982f194100c); 

                $this->fieldList_66982ee341008_items = $this->loadItems('PedidoSeguimento', 'pedido_id', $object, $this->fieldList_66982ee341008, function($masterObject, $detailObject, $objectItems){ 
                }, $this->criteria_fieldList_66982ee341008); 
*/
                //$forn = buscar o id e jogar no campo do pedido;
                //nesse mesmo fornecedor que vc buscou, pegar os dados da cidade(pessoa_endereco) e do seguimento (pessoa_seguimento)

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

        $this->fieldList_669924ff82798->addHeader();
        $this->fieldList_669924ff82798->addDetail($this->default_item_fieldList_669924ff82798);

        $this->fieldList_669924ff82798->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    }

    public function onShow($param = null)
    {
        $this->fieldList_669924ff82798->addHeader();
        $this->fieldList_669924ff82798->addDetail($this->default_item_fieldList_669924ff82798);

        $this->fieldList_669924ff82798->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

