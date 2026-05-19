<?php

class PedidoVendaFormCred extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Pedido';
    private static $primaryKey = 'id';
    private static $formName = 'form_PedidoVendaForm';

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
        $this->form->setFormTitle("Cadastro de pedido");

        $criteria_centrocusto_id = new TCriteria();
        $criteria_departamento_unit_id = new TCriteria();
        $criteria_cartao_id = new TCriteria();
        $criteria_itens_pedido_pedido_venda_produto_id = new TCriteria();
        $criteria_cidade_pedido_pedido_cidade_id = new TCriteria();
        $criteria_pedido_seguimento_pedido_seguimento_id = new TCriteria();
        $criteria_cotacao_pedido_pessoa_id = new TCriteria();
        $criteria_cotacao_pedido_system_users_id = new TCriteria();
        $criteria_cotacao_pedido_estado_pedido_id = new TCriteria();

        $filterVar = TSession::getValue("userunitid");
        $criteria_departamento_unit_id->add(new TFilter('system_users_id', '=', $filterVar)); 

        // aqui posso escrever

        $id = new TEntry('id');
        $dt_pedido = new TDate('dt_pedido');
        $descricaopedido = new TEntry('descricaopedido');
        $centrocusto_id = new TDBCombo('centrocusto_id', 'minierp', 'Centrocusto', 'id', '{nome}','nome asc' , $criteria_centrocusto_id );
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'SystemUserDepartamentoUnit', 'departamento_unit_id', '{departamento_unit->name}','departamento_unit_id asc' , $criteria_departamento_unit_id );
        $cartao_id = new TDBCombo('cartao_id', 'minierp', 'Cartao', 'id', '{numero_cartao}','id asc' , $criteria_cartao_id );
        $obs = new TText('obs');
        $itens_pedido_pedido_venda_id = new THidden('itens_pedido_pedido_venda_id[]');
        $itens_pedido_pedido_venda___row__id = new THidden('itens_pedido_pedido_venda___row__id[]');
        $itens_pedido_pedido_venda___row__data = new THidden('itens_pedido_pedido_venda___row__data[]');
        $itens_pedido_pedido_venda_produto_id = new TDBUniqueSearch('itens_pedido_pedido_venda_produto_id[]', 'minierp', 'Produto', 'id', 'nome','nome asc' , $criteria_itens_pedido_pedido_venda_produto_id );
        $itens_pedido_pedido_venda_quantidade = new TNumeric('itens_pedido_pedido_venda_quantidade[]', '2', ',', '.' );
        $itens_pedido_pedido_venda_valor = new TNumeric('itens_pedido_pedido_venda_valor[]', '2', ',', '.' );
        $itens_pedido_pedido_venda_valor_total = new TNumeric('itens_pedido_pedido_venda_valor_total[]', '2', ',', '.' );
        $itens_pedido_pedido_venda_obs = new TEntry('itens_pedido_pedido_venda_obs[]');
        $this->fieldList_666d9353ef312 = new TFieldList();
        $cidade_pedido_pedido_id = new THidden('cidade_pedido_pedido_id[]');
        $cidade_pedido_pedido___row__id = new THidden('cidade_pedido_pedido___row__id[]');
        $cidade_pedido_pedido___row__data = new THidden('cidade_pedido_pedido___row__data[]');
        $cidade_pedido_pedido_cidade_id = new TDBCombo('cidade_pedido_pedido_cidade_id[]', 'minierp', 'Cidade', 'id', '{nome} - {estado->sigla}','nome asc' , $criteria_cidade_pedido_pedido_cidade_id );
        $this->fieldList_666dab775c342 = new TFieldList();
        $pedido_seguimento_pedido_id = new THidden('pedido_seguimento_pedido_id[]');
        $pedido_seguimento_pedido___row__id = new THidden('pedido_seguimento_pedido___row__id[]');
        $pedido_seguimento_pedido___row__data = new THidden('pedido_seguimento_pedido___row__data[]');
        $pedido_seguimento_pedido_seguimento_id = new TDBCombo('pedido_seguimento_pedido_seguimento_id[]', 'minierp', 'Seguimento', 'id', '{descricao}','id asc' , $criteria_pedido_seguimento_pedido_seguimento_id );
        $this->fieldList_666dab925c346 = new TFieldList();
        $documentos_pedido_pedido_id = new THidden('documentos_pedido_pedido_id[]');
        $documentos_pedido_pedido___row__id = new THidden('documentos_pedido_pedido___row__id[]');
        $documentos_pedido_pedido___row__data = new THidden('documentos_pedido_pedido___row__data[]');
        $documentos_pedido_pedido_caminho = new TFile('documentos_pedido_pedido_caminho[]');
        $this->fieldList_666f5868dd659 = new TFieldList();
        $cotacao_pedido_id = new THidden('cotacao_pedido_id[]');
        $cotacao_pedido___row__id = new THidden('cotacao_pedido___row__id[]');
        $cotacao_pedido___row__data = new THidden('cotacao_pedido___row__data[]');
        $cotacao_pedido_pessoa_id = new TDBCombo('cotacao_pedido_pessoa_id[]', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_cotacao_pedido_pessoa_id );
        $cotacao_pedido_data_cotacao = new TDate('cotacao_pedido_data_cotacao[]');
        $cotacao_pedido_system_users_id = new TDBCombo('cotacao_pedido_system_users_id[]', 'minierp', 'SystemUsers', 'id', '{name}','name asc' , $criteria_cotacao_pedido_system_users_id );
        $cotacao_pedido_estado_pedido_id = new TDBCombo('cotacao_pedido_estado_pedido_id[]', 'minierp', 'EstadoPedido', 'id', '{nome}','nome asc' , $criteria_cotacao_pedido_estado_pedido_id );
        $this->fieldList_666dd89f2e46f = new TFieldList();

        $this->fieldList_666d9353ef312->addField(null, $itens_pedido_pedido_venda_id, []);
        $this->fieldList_666d9353ef312->addField(null, $itens_pedido_pedido_venda___row__id, ['uniqid' => true]);
        $this->fieldList_666d9353ef312->addField(null, $itens_pedido_pedido_venda___row__data, []);
        $this->fieldList_666d9353ef312->addField(new TLabel("Produto", null, '14px', null), $itens_pedido_pedido_venda_produto_id, ['width' => '20%']);
        $this->fieldList_666d9353ef312->addField(new TLabel("Quantidade", null, '14px', null), $itens_pedido_pedido_venda_quantidade, ['width' => '20%','sum' => true]);
        $this->fieldList_666d9353ef312->addField(new TLabel("Valor", null, '14px', null), $itens_pedido_pedido_venda_valor, ['width' => '20%','sum' => true]);
        $this->fieldList_666d9353ef312->addField(new TLabel("Valor total", null, '14px', null), $itens_pedido_pedido_venda_valor_total, ['width' => '20%','sum' => true]);
        $this->fieldList_666d9353ef312->addField(new TLabel("Obs", null, '14px', null), $itens_pedido_pedido_venda_obs, ['width' => '20%']);

        $this->fieldList_666d9353ef312->width = '100%';
        $this->fieldList_666d9353ef312->setFieldPrefix('itens_pedido_pedido_venda');
        $this->fieldList_666d9353ef312->name = 'fieldList_666d9353ef312';

        $this->criteria_fieldList_666d9353ef312 = new TCriteria();
        $this->default_item_fieldList_666d9353ef312 = new stdClass();

        $this->form->addField($itens_pedido_pedido_venda_id);
        $this->form->addField($itens_pedido_pedido_venda___row__id);
        $this->form->addField($itens_pedido_pedido_venda___row__data);
        $this->form->addField($itens_pedido_pedido_venda_produto_id);
        $this->form->addField($itens_pedido_pedido_venda_quantidade);
        $this->form->addField($itens_pedido_pedido_venda_valor);
        $this->form->addField($itens_pedido_pedido_venda_valor_total);
        $this->form->addField($itens_pedido_pedido_venda_obs);

        $this->fieldList_666d9353ef312->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $this->fieldList_666dab775c342->addField(null, $cidade_pedido_pedido_id, []);
        $this->fieldList_666dab775c342->addField(null, $cidade_pedido_pedido___row__id, ['uniqid' => true]);
        $this->fieldList_666dab775c342->addField(null, $cidade_pedido_pedido___row__data, []);
        $this->fieldList_666dab775c342->addField(new TLabel("Cidade", null, '14px', null), $cidade_pedido_pedido_cidade_id, ['width' => '100%']);

        $this->fieldList_666dab775c342->width = '100%';
        $this->fieldList_666dab775c342->setFieldPrefix('cidade_pedido_pedido');
        $this->fieldList_666dab775c342->name = 'fieldList_666dab775c342';

        $this->criteria_fieldList_666dab775c342 = new TCriteria();
        $this->default_item_fieldList_666dab775c342 = new stdClass();

        $this->form->addField($cidade_pedido_pedido_id);
        $this->form->addField($cidade_pedido_pedido___row__id);
        $this->form->addField($cidade_pedido_pedido___row__data);
        $this->form->addField($cidade_pedido_pedido_cidade_id);

        $this->fieldList_666dab775c342->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $this->fieldList_666dab925c346->addField(null, $pedido_seguimento_pedido_id, []);
        $this->fieldList_666dab925c346->addField(null, $pedido_seguimento_pedido___row__id, ['uniqid' => true]);
        $this->fieldList_666dab925c346->addField(null, $pedido_seguimento_pedido___row__data, []);
        $this->fieldList_666dab925c346->addField(new TLabel("Seguimento", null, '14px', null), $pedido_seguimento_pedido_seguimento_id, ['width' => '100%']);

        $this->fieldList_666dab925c346->width = '100%';
        $this->fieldList_666dab925c346->setFieldPrefix('pedido_seguimento_pedido');
        $this->fieldList_666dab925c346->name = 'fieldList_666dab925c346';

        $this->criteria_fieldList_666dab925c346 = new TCriteria();
        $this->default_item_fieldList_666dab925c346 = new stdClass();

        $this->form->addField($pedido_seguimento_pedido_id);
        $this->form->addField($pedido_seguimento_pedido___row__id);
        $this->form->addField($pedido_seguimento_pedido___row__data);
        $this->form->addField($pedido_seguimento_pedido_seguimento_id);

        $this->fieldList_666dab925c346->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $this->fieldList_666f5868dd659->addField(null, $documentos_pedido_pedido_id, []);
        $this->fieldList_666f5868dd659->addField(null, $documentos_pedido_pedido___row__id, ['uniqid' => true]);
        $this->fieldList_666f5868dd659->addField(null, $documentos_pedido_pedido___row__data, []);
        $this->fieldList_666f5868dd659->addField(new TLabel("Caminho", null, '14px', null), $documentos_pedido_pedido_caminho, ['width' => '100%']);

        $this->fieldList_666f5868dd659->width = '100%';
        $this->fieldList_666f5868dd659->setFieldPrefix('documentos_pedido_pedido');
        $this->fieldList_666f5868dd659->name = 'fieldList_666f5868dd659';

        $this->criteria_fieldList_666f5868dd659 = new TCriteria();
        $this->default_item_fieldList_666f5868dd659 = new stdClass();

        $this->form->addField($documentos_pedido_pedido_id);
        $this->form->addField($documentos_pedido_pedido___row__id);
        $this->form->addField($documentos_pedido_pedido___row__data);
        $this->form->addField($documentos_pedido_pedido_caminho);

        $this->fieldList_666f5868dd659->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $this->fieldList_666dd89f2e46f->addField(null, $cotacao_pedido_id, []);
        $this->fieldList_666dd89f2e46f->addField(null, $cotacao_pedido___row__id, ['uniqid' => true]);
        $this->fieldList_666dd89f2e46f->addField(null, $cotacao_pedido___row__data, []);
        $this->fieldList_666dd89f2e46f->addField(new TLabel("Fornecedor", null, '14px', null), $cotacao_pedido_pessoa_id, ['width' => '25%']);
        $this->fieldList_666dd89f2e46f->addField(new TLabel("Data cotação", null, '14px', null), $cotacao_pedido_data_cotacao, ['width' => '25%']);
        $this->fieldList_666dd89f2e46f->addField(new TLabel("Usuário", null, '14px', null), $cotacao_pedido_system_users_id, ['width' => '25%']);
        $this->fieldList_666dd89f2e46f->addField(new TLabel("Status", null, '14px', null), $cotacao_pedido_estado_pedido_id, ['width' => '25%']);

        $this->fieldList_666dd89f2e46f->width = '100%';
        $this->fieldList_666dd89f2e46f->setFieldPrefix('cotacao_pedido');
        $this->fieldList_666dd89f2e46f->name = 'fieldList_666dd89f2e46f';

        $this->criteria_fieldList_666dd89f2e46f = new TCriteria();
        $this->default_item_fieldList_666dd89f2e46f = new stdClass();

        $this->form->addField($cotacao_pedido_id);
        $this->form->addField($cotacao_pedido___row__id);
        $this->form->addField($cotacao_pedido___row__data);
        $this->form->addField($cotacao_pedido_pessoa_id);
        $this->form->addField($cotacao_pedido_data_cotacao);
        $this->form->addField($cotacao_pedido_system_users_id);
        $this->form->addField($cotacao_pedido_estado_pedido_id);

        $this->fieldList_666dd89f2e46f->disableRemoveButton();

        $this->fieldList_666dd89f2e46f->disableCloneButton();

        $departamento_unit_id->setChangeAction(new TAction([$this,'onGuardaDepartamento']));
        $itens_pedido_pedido_venda_produto_id->setChangeAction(new TAction([$this,'onBuscaProduto']));

        $itens_pedido_pedido_venda_quantidade->setExitAction(new TAction([$this,'onCalcValor']));

        $dt_pedido->addValidation("Data do Pedido", new TRequiredValidator()); 
        $pedido_seguimento_pedido_seguimento_id->addValidation("Seguimento id", new TRequiredListValidator()); 

        $itens_pedido_pedido_venda_produto_id->setMinLength(2);
        $documentos_pedido_pedido_caminho->enableFileHandling();
        $dt_pedido->setDatabaseMask('yyyy-mm-dd');
        $cotacao_pedido_data_cotacao->setDatabaseMask('yyyy-mm-dd');

        $dt_pedido->setMask('dd/mm/yyyy');
        $cotacao_pedido_data_cotacao->setMask('dd/mm/yyyy');
        $itens_pedido_pedido_venda_produto_id->setMask('{nome}');

        $id->setEditable(false);
        $cotacao_pedido_pessoa_id->setEditable(false);
        $cotacao_pedido_data_cotacao->setEditable(false);
        $cotacao_pedido_system_users_id->setEditable(false);
        $cotacao_pedido_estado_pedido_id->setEditable(false);

        $cartao_id->enableSearch();
        $centrocusto_id->enableSearch();
        $departamento_unit_id->enableSearch();
        $cotacao_pedido_pessoa_id->enableSearch();
        $cidade_pedido_pedido_cidade_id->enableSearch();
        $cotacao_pedido_system_users_id->enableSearch();
        $cotacao_pedido_estado_pedido_id->enableSearch();
        $pedido_seguimento_pedido_seguimento_id->enableSearch();

        $id->setSize(100);
        $dt_pedido->setSize(110);
        $obs->setSize('100%', 70);
        $cartao_id->setSize('100%');
        $centrocusto_id->setSize('100%');
        $descricaopedido->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $cotacao_pedido_pessoa_id->setSize('100%');
        $cotacao_pedido_data_cotacao->setSize(110);
        $itens_pedido_pedido_venda_obs->setSize('100%');
        $itens_pedido_pedido_venda_valor->setSize('80%');
        $cidade_pedido_pedido_cidade_id->setSize('100%');
        $cotacao_pedido_system_users_id->setSize('100%');
        $cotacao_pedido_estado_pedido_id->setSize('100%');
        $documentos_pedido_pedido_caminho->setSize('100%');
        $itens_pedido_pedido_venda_produto_id->setSize(400);
        $itens_pedido_pedido_venda_quantidade->setSize('100%');
        $itens_pedido_pedido_venda_valor_total->setSize('80%');
        $pedido_seguimento_pedido_seguimento_id->setSize('100%');


        $tab_666d91088c834 = new BootstrapFormBuilder('tab_666d91088c834');
        $this->tab_666d91088c834 = $tab_666d91088c834;
        $tab_666d91088c834->setProperty('style', 'border:none; box-shadow:none;');

        $tab_666d91088c834->appendPage("Dados/Itens");

        $tab_666d91088c834->addFields([new THidden('current_tab_tab_666d91088c834')]);
        $tab_666d91088c834->setTabFunction("$('[name=current_tab_tab_666d91088c834]').val($(this).attr('data-current_page'));");

        $row1 = $tab_666d91088c834->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Data Pedido:", '#FF0000', '14px', null, '100%'),$dt_pedido],[new TLabel("Nome ou titulo deste pedido para localização futura:", '#ff0000', '14px', null, '100%'),$descricaopedido]);
        $row1->layout = [' col-sm-3',' col-sm-3','col-sm-6'];

        $row2 = $tab_666d91088c834->addFields([new TLabel("Centro de Custo", '#FF0000', '14px', null),$centrocusto_id],[new TLabel("Departamentos / Secretárias", '#FF0000', '14px', null),$departamento_unit_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $tab_666d91088c834->addFields([new TLabel("Cartão", null, '14px', null),$cartao_id],[new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row3->layout = [' col-sm-6',' col-sm-6'];

        $tab_62caa82503358 = new BootstrapFormBuilder('tab_62caa82503358');
        $this->tab_62caa82503358 = $tab_62caa82503358;
        $tab_62caa82503358->setProperty('style', 'border:none; box-shadow:none;');

        $tab_62caa82503358->appendPage("Produtos");

        $tab_62caa82503358->addFields([new THidden('current_tab_tab_62caa82503358')]);
        $tab_62caa82503358->setTabFunction("$('[name=current_tab_tab_62caa82503358]').val($(this).attr('data-current_page'));");

        $row4 = $tab_62caa82503358->addFields([$this->fieldList_666d9353ef312]);
        $row4->layout = [' col-sm-12'];

        $row5 = $tab_666d91088c834->addFields([$tab_62caa82503358]);
        $row5->layout = [' col-sm-12'];

        $tab_666d91088c834->appendPage("Cidades a Receber");
        $row6 = $tab_666d91088c834->addFields([$this->fieldList_666dab775c342]);
        $row6->layout = [' col-sm-12'];

        $tab_666d91088c834->appendPage("Seguimentos a Receber");
        $row7 = $tab_666d91088c834->addFields([$this->fieldList_666dab925c346]);
        $row7->layout = [' col-sm-12'];

        $tab_666d91088c834->appendPage("Arquivos");
        $row8 = $tab_666d91088c834->addFields([$this->fieldList_666f5868dd659]);
        $row8->layout = [' col-sm-12'];

        $tab_666d91088c834->appendPage("Propostas em Andamento");
        $row9 = $tab_666d91088c834->addFields([$this->fieldList_666dd89f2e46f]);
        $row9->layout = [' col-sm-12'];

        $row10 = $this->form->addFields([new TLabel("Tipo do pedido:", '#FF0000', '14px', null, '100%'),$tab_666d91088c834]);
        $row10->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onsetproject = $this->form->addAction("Voltar", new TAction(['PedidoVendaListCred', 'onSetProject']), 'fas:arrow-left #000000');
        $this->btn_onsetproject = $btn_onsetproject;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=PedidoVendaFormCred]');
        $style->width = '80% !important';   
        $style->show(true);

    }

    public static function onCalcValor($param = null) 
    {
        try 
        {
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
//            opener.location.reload()
      //      TScript::create("$(document).grandtotal_itens_pedido_pedido_venda_valor.reload()");
            TScript::create("$('#{$id}').parent().parent().find('[name=\"itens_pedido_pedido_venda_valor_total[]\"]').val('{$valortotal}')");

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onGuardaDepartamento($param = null) 
    {
        try 
        {
            //code here
            TSession::setValue('departamento', NULL);
            TSession::setValue('departamento', $param['departamento_unit_id']);

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
            TSession::setValue('idproduto',NULL);
            TSession::setValue('idproduto',$param['_field_value']);
            $id=$param['_field_id'];
            $valor=str_replace('.',',',$produto->preco_venda);
          //  var_dump($valor);
            TScript::create("$('#{$id}').parent().parent().find('[name=\"itens_pedido_pedido_venda_valor[]\"]').val('{$valor}')");

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

            $documentos_pedido_pedido_caminho_dir = 'app/documentos';  

            $pedidoNovo = false ;

            if(!$data->id)
            {
                $pedidoNovo = true;
                $object->estado_pedido_venda_id = EstadoPedido::PENDENTE;
                $object->system_users_id = TSession::getValue('userid');
            }

            $dt_pedido = new DateTime($data->dt_pedido);

            $object->mes = $dt_pedido->format('m');
            $object->ano = $dt_pedido->format('Y');

            $object->valor_total = 0;

            $object->store(); // save the object 
/*

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            $documentos_pedido_pedido_items = $this->storeItems('DocumentosPedido', 'pedido_id', $object, $this->fieldList_666f5868dd659, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_666f5868dd659); 
            if(!empty($documentos_pedido_pedido_items))
            {
                foreach ($documentos_pedido_pedido_items as $item)
                {
                    $dataFile = new stdClass();
                    $dataFile->caminho = $item->caminho;
                    $this->saveFile($item, $dataFile, 'caminho', $documentos_pedido_pedido_caminho_dir);
                }
            }

*/

            $cotacao_pedido_items = $this->storeItems('Cotacao', 'pedido_id', $object, $this->fieldList_666dd89f2e46f, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_666dd89f2e46f); 

            $pedido_seguimento_pedido_items = $this->storeItems('PedidoSeguimento', 'pedido_id', $object, $this->fieldList_666dab925c346, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_666dab925c346); 

            $cidade_pedido_pedido_items = $this->storeItems('CidadePedido', 'pedido_id', $object, $this->fieldList_666dab775c342, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_666dab775c342); 

            $itens_pedido_pedido_venda_items = $this->storeItems('ItensPedido', 'pedido_venda_id', $object, $this->fieldList_666d9353ef312, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_666d9353ef312); 
            $object->system_users_id = TSession::getValue('userid');
            $object->store();

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $itenspedido = ItensPedido::where('pedido_venda_id','=',$object->id)
                                      ->load();
            $somatotal=0;
            if ($itenspedido){
                foreach ($itenspedido as $itensp){
                    $itensp->valor_total = $itensp->valor * $itensp->quantidade;
                    $itensp->store();
                    $somatotal += ($itensp->valor * $itensp->quantidade);
                }
            }          
         //   var_dump($somatotal);
            $object->valor_total = $somatotal;
            $object->store();

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            if($pedidoNovo)
            {
                TTransaction::open(self::$database);

                PedidoVendaService::notificarAprovador($object);

                TTransaction::close();
            }

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('PedidoVendaListCred', 'onSetProject');
            /*

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('PedidoVendaListCred', 'onShow', $loadPageParam); 

           */
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

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Pedido($key); // instantiates the Active Record 

                $this->fieldList_666f5868dd659_items = $this->loadItems('DocumentosPedido', 'pedido_id', $object, $this->fieldList_666f5868dd659, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_666f5868dd659); 

                $this->fieldList_666dd89f2e46f_items = $this->loadItems('Cotacao', 'pedido_id', $object, $this->fieldList_666dd89f2e46f, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_666dd89f2e46f); 

                $this->fieldList_666dab925c346_items = $this->loadItems('PedidoSeguimento', 'pedido_id', $object, $this->fieldList_666dab925c346, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_666dab925c346); 

                $this->fieldList_666dab775c342_items = $this->loadItems('CidadePedido', 'pedido_id', $object, $this->fieldList_666dab775c342, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_666dab775c342); 

                $this->fieldList_666d9353ef312_items = $this->loadItems('ItensPedido', 'pedido_venda_id', $object, $this->fieldList_666d9353ef312, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_666d9353ef312); 

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

        $this->fieldList_666d9353ef312->addHeader();
        $this->fieldList_666d9353ef312->addDetail($this->default_item_fieldList_666d9353ef312);

        $this->fieldList_666d9353ef312->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_666dab775c342->addHeader();
        $this->fieldList_666dab775c342->addDetail($this->default_item_fieldList_666dab775c342);

        $this->fieldList_666dab775c342->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_666dab925c346->addHeader();
        $this->fieldList_666dab925c346->addDetail($this->default_item_fieldList_666dab925c346);

        $this->fieldList_666dab925c346->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_666f5868dd659->addHeader();
        $this->fieldList_666f5868dd659->addDetail($this->default_item_fieldList_666f5868dd659);

        $this->fieldList_666f5868dd659->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_666dd89f2e46f->addHeader();
        $this->fieldList_666dd89f2e46f->addDetail($this->default_item_fieldList_666dd89f2e46f);

    }

    public function onShow($param = null)
    {
        $this->fieldList_666d9353ef312->addHeader();
        $this->fieldList_666d9353ef312->addDetail($this->default_item_fieldList_666d9353ef312);

        $this->fieldList_666d9353ef312->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_666dab775c342->addHeader();
        $this->fieldList_666dab775c342->addDetail($this->default_item_fieldList_666dab775c342);

        $this->fieldList_666dab775c342->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_666dab925c346->addHeader();
        $this->fieldList_666dab925c346->addDetail($this->default_item_fieldList_666dab925c346);

        $this->fieldList_666dab925c346->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_666f5868dd659->addHeader();
        $this->fieldList_666f5868dd659->addDetail($this->default_item_fieldList_666f5868dd659);

        $this->fieldList_666f5868dd659->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_666dd89f2e46f->addHeader();
        $this->fieldList_666dd89f2e46f->addDetail($this->default_item_fieldList_666dd89f2e46f);

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

 private function obterFornecedores($cidades, $seguimentos)
    {
        $query = new ViewEnviarcotacao();

        if ($cidades) {
            $idCidades = array_map(function($cidade){ return $cidade->cidade_id;}, $cidades);
            $query->where('cidade_id', 'in', $idCidades);
        }

        if ($seguimentos) {
            $idSeguimentos = array_map(function($seguimento){ return $seguimento->seguimento_id;}, $seguimentos);
            $query->where('seguimento_id', 'in', $idSeguimentos);
        }

        return $query->getObjects();
    }

    private function gerarCotacoes($fornecedores, $pedido)
    {
       foreach ($fornecedores as $fornecedor) {
            $cotacao = new Cotacao();
            $cotacao->pedido_id = $pedido->id;
            $cotacao->pessoa_id = $fornecedor->id;
            $cotacao->data_cotacao = date('Y-m-d');
            $cotacao->estado_pedido_id = EstadoPedido::PENDENTE;
            $cotacao->system_users_id = TSession::getValue('iduser');
            $cotacao->store();
            $this->registrarHistoricoCotacao($cotacao);
        }
    }

private function registrarHistoricoPedido($pedido)
    {
        $hist = new PedidoHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d');
        $hist->estado_pedido_venda_id = EstadoPedido::NAOENVIADO; 
        $hist->aprovador_id = TSession::getValue('iduser');
        $hist->store();
    }

    private function registrarHistoricoCotacao($cotacao)
    {
        $histcotacao = new CotacaoHistorico();
        $histcotacao->cotacao_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d');
        $histcotacao->estado_pedido_id = EstadoPedido::PENDENTE; 
        $histcotacao->aprovador_id = TSession::getValue('iduser');
        $histcotacao->store();
    }

  /*  private function atualizaDetalhesPedido($pedido){

         $this->fieldList_666990603bdb0_items = $this->loadItems('Cotacao', 'pedido_id', $pedido, $this->fieldList_666990603bdb0, function($masterObject, $detailObject, $objectItems){ //</blockLine>
         }, $this->criteria_fieldList_666990603bdb0); //</blockLine>

         $this->FieldList_66646708b9376_items = $this->loadItems('DocumentosPedido', 'pedido_id', $pedido, $this->fieldList_66646708b9376, function($masterObject, $detailObject, $objectItems){ //</blockLine>
         }, $this->criteria_fieldList_66646708b9376); //</blockLine>

         $this->fieldList_666466aab936f_items = $this->loadItems('PedidoSeguimento', 'pedido_id', $pedido, $this->fieldList_666466aab936f, function($masterObject, $detailObject, $objectItems){ //</blockLine>
         }, $this->criteria_fieldList_666466aab936f); //</blockLine>

         $this->fieldList_66646678b936a_items = $this->loadItems('CidadePedido', 'pedido_id', $pedido, $this->fieldList_66646678b936a, function($masterObject, $detailObject, $objectItems){ //</blockLine>
         }, $this->criteria_fieldList_66646678b936a); //</blockLine>

         $this->fieldList_6664644db9360_items = $this->loadItems('ItensPedido', 'pedido_id', $pedido, $this->fieldList_6664644db9360, function($masterObject, $detailObject, $objectItems){ //</blockLine>
         }, $this->criteria_fieldList_6664644db9360); //</blockLine>        
    }*/
     private function atualizaDetalhesPedido($pedido){
         TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('PedidoVendaListCred', 'onShow');
         /*
                if ( $this->fieldList_666dd89f2e46f_items) {
                $this->fieldList_666dd89f2e46f_items = $this->loadItems('Cotacao', 'pedido_id', $object, $this->fieldList_666dd89f2e46f, function($masterObject, $detailObject, $objectItems){ //</blockLine>
                }, $this->criteria_fieldList_666dd89f2e46f); //</blockLine>
                }
                if ($this->fieldList_666db3cc7acea) {
                $this->fieldList_666db3cc7acea_items = $this->loadItems('DocumentosPedido', 'pedido_id', $object, $this->fieldList_666db3cc7acea, function($masterObject, $detailObject, $objectItems){ //</blockLine>
                }, $this->criteria_fieldList_666db3cc7acea); //</blockLine>
                }
                if ($this->fieldList_666dab925c346_items ){
                $this->fieldList_666dab925c346_items = $this->loadItems('PedidoSeguimento', 'pedido_id', $object, $this->fieldList_666dab925c346, function($masterObject, $detailObject, $objectItems){ //</blockLine>
                }, $this->criteria_fieldList_666dab925c346); //</blockLine>
                }
                if ( $this->fieldList_666dab775c342_items){
                $this->fieldList_666dab775c342_items = $this->loadItems('CidadePedido', 'pedido_id', $object, $this->fieldList_666dab775c342, function($masterObject, $detailObject, $objectItems){ //</blockLine>
                }, $this->criteria_fieldList_666dab775c342); //</blockLine>
                }
                if ($this->fieldList_666d9353ef312_items) {
                $this->fieldList_666d9353ef312_items = $this->loadItems('ItensPedido', 'pedido_venda_id', $object, $this->fieldList_666d9353ef312, function($masterObject, $detailObject, $objectItems){ //</blockLine>
                }, $this->criteria_fieldList_666d9353ef312); //</blockLine>

                */    
    }  

/* public static function onBuscaProduto-COPIA-NAOAPAGAR($param = null) 
    {
        try 
        {
            //code here
            TSession::setValue('idproduto', NULL);

            TTransaction::open(self::$database); // open a transaction
            $produto = new Produto($param['_field_value']);
            TSession::setValue('idproduto',$param['_field_value']);
            $id=$param['_field_id'];
            $valor=str_replace('.',',',$produto->preco_venda);
          //  var_dump($valor);
            TScript::create("$('#{$id}').parent().parent().find('[name=\"itens_pedido_pedido_venda_valor[]\"]').val('{$valor}')");
            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }*/

}

