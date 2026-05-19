<?php

class CotacaoForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Cotacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_CotacaoForm';

    use BuilderMasterDetailFieldListTrait;
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
        $this->form->setFormTitle("Cadastro de cotação");

        $criteria_pedido_id = new TCriteria();
        $criteria_pessoa_id = new TCriteria();
        $criteria_estado_pedido_id = new TCriteria();
        $criteria_itens_cotacao_cotacao_produto_id = new TCriteria();
        $criteria_itens_cotacao_cotacao_unidade_medida_id = new TCriteria();

/*

        $id = new TEntry('id');
        $pedido_id = new TDBCombo('pedido_id', 'minierp', 'Pedido', 'id', '{descricaopedido}','id asc' , $criteria_pedido_id );
        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa_id );
        $estado_pedido_id = new TDBCombo('estado_pedido_id', 'minierp', 'EstadoPedido', 'id', '{nome}','nome asc' , $criteria_estado_pedido_id );
        $data_cotacao = new TDate('data_cotacao');
        $obs = new TEntry('obs');
        $itens_cotacao_cotacao_id = new THidden('itens_cotacao_cotacao_id[]');
        $itens_cotacao_cotacao___row__id = new THidden('itens_cotacao_cotacao___row__id[]');
        $itens_cotacao_cotacao___row__data = new THidden('itens_cotacao_cotacao___row__data[]');
        $itens_cotacao_cotacao_produto_tipo_produto_nome = new TEntry('itens_cotacao_cotacao_produto_tipo_produto_nome[]');
        $itens_cotacao_cotacao_produto_id = new TDBUniqueSearch('itens_cotacao_cotacao_produto_id[]', 'minierp', 'Produto', 'id', 'nome','nome asc' , $criteria_itens_cotacao_cotacao_produto_id );
        $itens_cotacao_cotacao_qtde = new TEntry('itens_cotacao_cotacao_qtde[]');
        $itens_cotacao_cotacao_valor = new TNumeric('itens_cotacao_cotacao_valor[]', '2', ',', '.' );
        $itens_cotacao_cotacao_valor_total = new TEntry('itens_cotacao_cotacao_valor_total[]');
        $this->fieldList_6676230c8dc2b = new TFieldList();

        $this->fieldList_6676230c8dc2b->addField(null, $itens_cotacao_cotacao_id, []);
        $this->fieldList_6676230c8dc2b->addField(null, $itens_cotacao_cotacao___row__id, ['uniqid' => true]);
        $this->fieldList_6676230c8dc2b->addField(null, $itens_cotacao_cotacao___row__data, []);
        $this->fieldList_6676230c8dc2b->addField(new TLabel("Tipo", null, '14px', null), $itens_cotacao_cotacao_produto_tipo_produto_nome, ['width' => '100%']);
        $this->fieldList_6676230c8dc2b->addField(new TLabel("Produto id", null, '14px', null), $itens_cotacao_cotacao_produto_id, ['width' => '25%']);
        $this->fieldList_6676230c8dc2b->addField(new TLabel("Qtde", null, '14px', null), $itens_cotacao_cotacao_qtde, ['width' => '15%']);
        $this->fieldList_6676230c8dc2b->addField(new TLabel("Valor", null, '14px', null), $itens_cotacao_cotacao_valor, ['width' => '25%','sum' => true]);
        $this->fieldList_6676230c8dc2b->addField(new TLabel("Valor total", null, '14px', null), $itens_cotacao_cotacao_valor_total, ['width' => '25%','sum' => true]);

        $this->fieldList_6676230c8dc2b->width = '100%';
        $this->fieldList_6676230c8dc2b->setFieldPrefix('itens_cotacao_cotacao');
        $this->fieldList_6676230c8dc2b->name = 'fieldList_6676230c8dc2b';

        $this->criteria_fieldList_6676230c8dc2b = new TCriteria();
        $this->default_item_fieldList_6676230c8dc2b = new stdClass();

        $this->form->addField($itens_cotacao_cotacao_id);
        $this->form->addField($itens_cotacao_cotacao___row__id);
        $this->form->addField($itens_cotacao_cotacao___row__data);
        $this->form->addField($itens_cotacao_cotacao_produto_tipo_produto_nome);
        $this->form->addField($itens_cotacao_cotacao_produto_id);
        $this->form->addField($itens_cotacao_cotacao_qtde);
        $this->form->addField($itens_cotacao_cotacao_valor);
        $this->form->addField($itens_cotacao_cotacao_valor_total);

        $this->fieldList_6676230c8dc2b->disableRemoveButton();

        $this->fieldList_6676230c8dc2b->disableCloneButton();

        $itens_cotacao_cotacao_valor->setExitAction(new TAction([$this,'onCalcValor']));

        $data_cotacao->setDatabaseMask('yyyy-mm-dd');
        $obs->setMaxLength(500);
        $itens_cotacao_cotacao_produto_id->setMinLength(2);
        $pedido_id->enableSearch();
        $pessoa_id->enableSearch();
        $estado_pedido_id->enableSearch();

        $data_cotacao->setMask('dd/mm/yyyy');
        $itens_cotacao_cotacao_produto_id->setMask('{nome}');
        $itens_cotacao_cotacao_valor_total->setMask('999 999 999.99');

        $id->setEditable(false);
        $pedido_id->setEditable(false);
        $pessoa_id->setEditable(false);
        $data_cotacao->setEditable(false);
        $estado_pedido_id->setEditable(false);
        $itens_cotacao_cotacao_qtde->setEditable(false);
        $itens_cotacao_cotacao_produto_id->setEditable(false);
        $itens_cotacao_cotacao_produto_tipo_produto_nome->setEditable(false);

        $id->setSize(100);
        $obs->setSize('100%');
        $pedido_id->setSize('100%');
        $pessoa_id->setSize('100%');
        $data_cotacao->setSize(110);
        $estado_pedido_id->setSize('100%');
        $itens_cotacao_cotacao_qtde->setSize('100%');
        $itens_cotacao_cotacao_valor->setSize('100%');
        $itens_cotacao_cotacao_produto_id->setSize(400);
        $itens_cotacao_cotacao_valor_total->setSize('100%');
        $itens_cotacao_cotacao_produto_tipo_produto_nome->setSize('100%');


*/
        $id = new TEntry('id');
        $pedido_id = new TDBCombo('pedido_id', 'minierp', 'Pedido', 'id', '{descricaopedido}','id asc' , $criteria_pedido_id );
        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa_id );
        $estado_pedido_id = new TDBCombo('estado_pedido_id', 'minierp', 'EstadoPedido', 'id', '{nome}','nome asc' , $criteria_estado_pedido_id );
        $data_cotacao = new TDate('data_cotacao');
        $obs = new TEntry('obs');
        $itens_cotacao_cotacao_id = new THidden('itens_cotacao_cotacao_id[]');
        $itens_cotacao_cotacao_itens_pedido_id = new THidden('itens_cotacao_cotacao_itens_pedido_id[]');
        $itens_cotacao_cotacao___row__id = new THidden('itens_cotacao_cotacao___row__id[]');
        $itens_cotacao_cotacao___row__data = new THidden('itens_cotacao_cotacao___row__data[]');
        $itens_cotacao_cotacao_produto_id = new TDBUniqueSearch('itens_cotacao_cotacao_produto_id[]', 'minierp', 'Produto', 'id', 'nome','nome asc' , $criteria_itens_cotacao_cotacao_produto_id );
        $itens_cotacao_cotacao_unidade_medida_id = new TDBCombo('itens_cotacao_cotacao_unidade_medida_id[]', 'minierp', 'UnidadeMedida', 'id', '{nome}','nome asc' , $criteria_itens_cotacao_cotacao_unidade_medida_id );
        $itens_cotacao_cotacao_qtde = new TEntry('itens_cotacao_cotacao_qtde[]');
        $itens_cotacao_cotacao_valor = new TNumeric('itens_cotacao_cotacao_valor[]', '2', ',', '.' );
        $itens_cotacao_cotacao_valor_total = new TEntry('itens_cotacao_cotacao_valor_total[]');

      
        $this->fieldList_6676230c8dc2b = new TFieldList();
           $valor_total = new TNumeric('valor_total', '2', ',', '.' );
        $valor_desconto = new TNumeric('valor_desconto', '2', ',', '.' );
        $valor_liquido = new TNumeric('valor_liquido', '2', ',', '.' );
        $Documentos = new BPageContainer();

        $this->fieldList_6676230c8dc2b->addField(null, $itens_cotacao_cotacao_id, []);
        $this->fieldList_6676230c8dc2b->addField(null, $itens_cotacao_cotacao_itens_pedido_id, []);
        $this->fieldList_6676230c8dc2b->addField(null, $itens_cotacao_cotacao___row__id, ['uniqid' => true]);
        $this->fieldList_6676230c8dc2b->addField(null, $itens_cotacao_cotacao___row__data, []);
        $this->fieldList_6676230c8dc2b->addField(new TLabel("Produto", null, '14px', null), $itens_cotacao_cotacao_produto_id, ['width' => '60%']);
        $this->fieldList_6676230c8dc2b->addField(new TLabel("Und", null, '14px', null), $itens_cotacao_cotacao_unidade_medida_id, ['width' => '10%']);
        $this->fieldList_6676230c8dc2b->addField(new TLabel("Qtde", null, '14px', null), $itens_cotacao_cotacao_qtde, ['width' => '10%']);
        $this->fieldList_6676230c8dc2b->addField(new TLabel("Valor", null, '14px', null), $itens_cotacao_cotacao_valor, ['width' => '10%','sum' => true]);
        $this->fieldList_6676230c8dc2b->addField(new TLabel("Valor total", null, '14px', null), $itens_cotacao_cotacao_valor_total, ['width' => '10%','sum' => true]);
        
//        $itens_cotacao_cotacao_qtde->setChangeAction(new TAction([$this,'onBuscaProduto']));
      //  $itens_cotacao_cotacao_qtde->setExitAction(new TAction([$this,'onCalcValor']));


        $this->fieldList_6676230c8dc2b->width = '100%';
        $this->fieldList_6676230c8dc2b->setFieldPrefix('itens_cotacao_cotacao');
        $this->fieldList_6676230c8dc2b->name = 'fieldList_6676230c8dc2b';

        $this->criteria_fieldList_6676230c8dc2b = new TCriteria();
        $this->default_item_fieldList_6676230c8dc2b = new stdClass();

        $this->form->addField($itens_cotacao_cotacao_id);
        $this->form->addField($itens_cotacao_cotacao_itens_pedido_id);
        $this->form->addField($itens_cotacao_cotacao___row__id);
        $this->form->addField($itens_cotacao_cotacao___row__data);
        $this->form->addField($itens_cotacao_cotacao_produto_id);
        $this->form->addField($itens_cotacao_cotacao_unidade_medida_id);
        $this->form->addField($itens_cotacao_cotacao_qtde);
        $this->form->addField($itens_cotacao_cotacao_valor);
        $this->form->addField($itens_cotacao_cotacao_valor_total);

   //     $this->fieldList_6676230c8dc2b->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

       $this->fieldList_6676230c8dc2b->disableRemoveButton();

        $this->fieldList_6676230c8dc2b->disableCloneButton();

     //   $itens_cotacao_cotacao_qtde->setExitAction(new TAction([$this,'onCalcValor']));
         $itens_cotacao_cotacao_valor->setExitAction(new TAction([$this,'onCalcValor']));
    //    $itens_cotacao_cotacao_valor->setExitAction(new TAction([$this,'onCalcValorErrado']));
        

        $data_cotacao->setDatabaseMask('yyyy-mm-dd');
        $obs->setMaxLength(500);
        $itens_cotacao_cotacao_produto_id->setMinLength(2);
        $Documentos->setAction(new TAction(['DocumentosCotacaoList', 'onSetProject']));
        $Documentos->setId('b6679c45b92467');
        $Documentos->hide();
        $pedido_id->enableSearch();
        $pessoa_id->enableSearch();
        $estado_pedido_id->enableSearch();

        $data_cotacao->setMask('dd/mm/yyyy');
        $itens_cotacao_cotacao_produto_id->setMask('{nome}');
        //$itens_cotacao_cotacao_valor_total->setMask('999 999 999.99');
        $itens_cotacao_cotacao_valor_total->setNumericMask(2, '.', ',');
                $itens_cotacao_cotacao_valor->setNumericMask(2, '.', ',');


        $id->setEditable(false);
        $itens_cotacao_cotacao_unidade_medida_id->setEditable(false);
        $pedido_id->setEditable(false);
        $pessoa_id->setEditable(false);
        $data_cotacao->setEditable(false);
        $estado_pedido_id->setEditable(false);
     //   $itens_cotacao_cotacao_qtde->setEditable(false);
        $itens_cotacao_cotacao_produto_id->setEditable(false);
                $itens_cotacao_cotacao_qtde->setEditable(false);

        $TAlert = new TAlert('danger', "Atenção: Os valores iniciais foram carregados automaticamente a partir da tabela de preços. Verifique com atenção e insira o seu valor antes de prosseguir com o envio da cotação.");

        $id->setSize(100);
        $obs->setSize('100%');
        $pedido_id->setSize('100%');
        $pessoa_id->setSize('100%');
        $data_cotacao->setSize(110);
        $Documentos->setSize('100%');
        $estado_pedido_id->setSize('100%');
        $itens_cotacao_cotacao_qtde->setSize('100%');
        $itens_cotacao_cotacao_valor->setSize('100%');
        $itens_cotacao_cotacao_produto_id->setSize(700);
        $itens_cotacao_cotacao_qtde->setSize('100%');
        $itens_cotacao_cotacao_valor->setSize('100%');
        $itens_cotacao_cotacao_unidade_medida_id->setSize('100%');
        $itens_cotacao_cotacao_valor_total->setSize('100%');

        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $Documentos->add($loadingContainer);

        $this->Documentos = $Documentos;

        //<onBeforeAddFieldsToForm>

        $tab_666f95d65f34b = new BootstrapFormBuilder('tab_666f95d65f34b');
        $this->tab_666f95d65f34b = $tab_666f95d65f34b;
        $tab_666f95d65f34b->setProperty('style', 'border:none; box-shadow:none;');

        $tab_666f95d65f34b->appendPage("Dados da cotação");

        $tab_666f95d65f34b->addFields([new THidden('current_tab_tab_666f95d65f34b')]);
        $tab_666f95d65f34b->setTabFunction("$('[name=current_tab_tab_666f95d65f34b]').val($(this).attr('data-current_page'));");

        $row0 = $this->form->addFields([$TAlert]);
        $row0->layout = [' col-sm-12'];

        $row1 = $tab_666f95d65f34b->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Descrição do pedido:", null, '14px', null, '100%'),$pedido_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $tab_666f95d65f34b->addFields([new TLabel("Estabelecimento:", null, '14px', null, '100%'),$pessoa_id],[new TLabel("Estado da cotação", null, '14px', null),$estado_pedido_id]);
        $row2->layout = ['col-sm-6',' col-sm-6'];

        $row3 = $tab_666f95d65f34b->addFields([new TLabel("Data cotação:", null, '14px', null, '100%'),$data_cotacao],[new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row3->layout = ['col-sm-6',' col-sm-6'];

        $row4 = $tab_666f95d65f34b->addFields([new TFormSeparator("<br>Itens da cotação", '#333', '18', '#eee')]);
        $row4->layout = [' col-sm-12'];

        $row5 = $tab_666f95d65f34b->addFields([$this->fieldList_6676230c8dc2b]);
        $row5->layout = [' col-sm-12'];

        $row6 = $this->form->addFields([$tab_666f95d65f34b]);
        $row6->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['CotacaoList', 'onShow']), 'fas:arrow-left #000000');
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

        $style = new TStyle('right-panel > .container-part[page-name=CotacaoForm]');
        $style->width = '80% !important';   
        $style->show(true);

    }

    public static function onCalcValor($param = null) 
    {
        try 
        {
            //code here
            TTransaction::open(self::$database); // open a transaction
            $id1=$param['_field_id'];
            $conteudojson = $param['_field_data_json'];
            $idproduto = json_decode($conteudojson);
            if (isset($idproduto->{'row'})) {
            $idproduto1 = $idproduto->{'row'}; // 1234

            $idproduto =  (int) str_replace(['.', ','], [',', '.'],($param['itens_cotacao_cotacao_produto_id'][$idproduto1]));
            $qtde =  (float) str_replace(['.', ','], [',', '.'],($param['itens_cotacao_cotacao_qtde'][$idproduto1]));

           $valorStr = $param['itens_cotacao_cotacao_valor'][$idproduto1] ?? '0';

            // Remove vírgula (separador de milhar)
            $valorNormalizado = str_replace(',', '', $valorStr);

            // Converte para float
            $valor = (float) $valorNormalizado;
            $valortotal=($valor * $qtde);
            TScript::create("$('#{$id1}').parent().parent().find('[name=\"itens_cotacao_cotacao_valor[]\"]').val({$valor})");   
            TScript::create("$('#{$id1}').parent().parent().find('[name=\"itens_cotacao_cotacao_valor_total[]\"]').val({$valortotal})");   
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

            $object = new Cotacao(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $this->protegerItensCotacaoNoSave($data);
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 
            
            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            $itens_cotacao_cotacao_items = $this->storeItems('ItensCotacao', 'cotacao_id', $object, $this->fieldList_6676230c8dc2b, function($masterObject, $detailObject){ 

                //code here
                $produto = new Produto($detailObject->produto_id);
                if ($detailObject->valor>$produto->preco_venda) {
                    $unit = new SystemUnit(TSession::getValue('idunit'));
                    if ($unit->utilizasinapi=='S') {
//                        throw new Exception("Valor do produto {$produto->nome} não confere com o valor da tabela Sinapi. Valor do produto: {$produto->preco_venda} Valor informado: {$detailObject->valor}");
                    }
                }

            }, $this->criteria_fieldList_6676230c8dc2b); 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            //pessoa
            $pessoass = new Pessoa($object->pessoa_id);
          
            // $taxaspessoa = TaxasPessoa::where('pessoa_id','=',$pessoass->id)
            //                           ->where('entidade_id','=',TSession::getValue('entidade'))
            //                           ->where('system_unit_id','=',TSession::getValue('idunit'))
            //                           ->load();
            // if ($taxaspessoa) {
            //     foreach ($taxaspessoa as $tx) {
            //         $taxaadm = $tx->taxaadm;
            //         $taxaantecipacao = $tx->taxaantecipacao;
            //         $taxabancaria = $tx->taxabancaria;
            //         break;
            //     }
            // } else {
               $taxaadm=0;
               $taxaantecipacao = 0;
               $taxabancaria = 0;
            // }
            $taxacontrato = TSession::getValue('taxacontrato');


            //calcular taxa administrativa
            $vltaxas = ( $taxacontrato) / 100;

            $valortotal =0;  
            $itenscotacao = ItensCotacao::where('cotacao_id','=',$object->id)
                                     ->load();

            if ($itenscotacao){
                foreach($itenscotacao as $itensc){
                    $itensc->valor_total = $itensc->valor * $itensc->qtde;
                    $itensc->store();
                    $valortotal = $valortotal + $itensc->valor_total;
                }
            }
            $object->valor_total = $valortotal;
            $object->valor_desconto = ( ($valortotal * $vltaxas) );
            $object->valor_liquido = $valortotal - $object->valor_desconto;
            $object->txadm = $taxaadm;
            $object->txantecipacao = $taxaantecipacao;
            $object->txcontrato = $taxacontrato;
            $object->txbancaria = $taxabancaria;
            $object->estado_pedido1_id = null;
            $object->store();

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction


            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('CotacaoList', 'onShow', $loadPageParam); 


       
                        TScript::create("Template.closeRightPanel();");
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

    private function protegerItensCotacaoNoSave(stdClass $data): void
    {
        $cotacaoId = (int) ($data->id ?? 0);
        if ($cotacaoId <= 0)
        {
            return;
        }

        $postedIds = isset($data->itens_cotacao_cotacao_id) && is_array($data->itens_cotacao_cotacao_id)
            ? array_map('intval', $data->itens_cotacao_cotacao_id)
            : [];

        $itensExistentes = ItensCotacao::where('cotacao_id', '=', $cotacaoId)->load() ?: [];
        $faltantes = [];

        foreach ($itensExistentes as $itemExistente)
        {
            if (!in_array((int) $itemExistente->id, $postedIds, true))
            {
                $faltantes[] = $itemExistente;
            }
        }

        if ($faltantes)
        {
            $nomes = [];
            foreach ($faltantes as $faltante)
            {
                $nomes[] = $faltante->produto->nome ?? ('Item #' . $faltante->id);
            }

            $lista = implode(', ', array_unique($nomes));
            throw new Exception("Nao foi possivel salvar porque um ou mais itens da cotacao desapareceram da tela: {$lista}. Reabra a cotacao e tente novamente.");
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

                $object = new Cotacao($key); // instantiates the Active Record 

                $itens = ItensCotacao::where('cotacao_id', '=', $object->id)->load();

                foreach ($itens as $item) {
                    if (empty($item->valor) || $item->valor == 0) {
                        try {
                            $produto = new Produto($item->produto_id);
                            $item->valor = ($produto->preco_venda > 0) ? $produto->preco_venda : 0;
                        } catch (Exception $e) {
                            $item->valor = 0;
                        }
                       $item->valor_total = $item->valor * $item->qtde;
                       $item->store(); // ✅ grava no banco
                    }
                }
                $this->fieldList_6676230c8dc2b_items = $this->loadItems('ItensCotacao', 'cotacao_id', $object, $this->fieldList_6676230c8dc2b, function($masterObject, $detailObject, $objectItems){ 

                    //code here
                    

                }, $this->criteria_fieldList_6676230c8dc2b); 


                // var_dump($this->fieldList_6676230c8dc2b_items);
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

        $this->fieldList_6676230c8dc2b->addHeader();
        $this->fieldList_6676230c8dc2b->addDetail($this->default_item_fieldList_6676230c8dc2b);

    }

    public function onShow($param = null)
    {
        $this->fieldList_6676230c8dc2b->addHeader();
        $this->fieldList_6676230c8dc2b->addDetail($this->default_item_fieldList_6676230c8dc2b);

    } 

    public static function getFormName()
    {
        return self::$formName;
    }
  

    

}

