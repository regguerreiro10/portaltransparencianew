<?php

use Adianti\Widget\Wrapper\TDBCombo;
 
class PedidoVendaForm extends TPage
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
        $perf_start = microtime(true);

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
        $criteria_itens_pedido_pedido_venda_produto_id = new TCriteria();
        $criteria_itens_pedido_pedido_venda_unidade_medida_id = new TCriteria();
        $criteria_saldo_departamento_id = new TCriteria();


        $identidade = TSession::getValue('entidade');

    
        $criteria_itens_pedido_pedido_venda_produto_id->add( 
            new TFilter('system_unit_id', 'IN',
                "(SELECT su.id FROM system_unit su 
                LEFT JOIN entidade e ON e.id = su.entidade_id 
                WHERE e.compras = 1)"
            )
        );


        $criteria_cidade_pedido_pedido_cidade_id = new TCriteria();
        $criteria_pedido_seguimento_pedido_seguimento_id = new TCriteria();
        $criteria_cotacao_pedido_pessoa_id = new TCriteria();
        $criteria_cotacao_pedido_system_users_id = new TCriteria();
        $criteria_cotacao_pedido_estado_pedido_id = new TCriteria();

        $filterVar = TSession::getValue("userid");
        $criteria_departamento_unit_id->add(new TFilter('system_users_id', '=', $filterVar)); 
        $filterVar = TSession::getValue('idunit');
        $criteria_departamento_unit_id->add(new TFilter('departamento_unit_id', 'in', "(SELECT id FROM departamento_unit WHERE system_unit_id = '{$filterVar}')")); 

        $filterVar = TSession::getValue('entidade');
        $criteria_saldo_departamento_id->add(new TFilter('saldo_entidade_contrato_id', 'in', "(SELECT id FROM saldo_entidade_contrato WHERE  deleted_at is null AND entidade_id in (SELECT id FROM entidade WHERE id = '{$filterVar}'))")); 

        // aqui posso escrever

        $id = new TEntry('id');
        $dt_pedido = new TDate('dt_pedido');
        $descricaopedido = new TEntry('descricaopedido');
        $centrocusto_id = new TDBUniqueSearch('centrocusto_id', 'minierp', 'Centrocusto', 'id', 'nome','nome asc' , $criteria_centrocusto_id );
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'SystemUserDepartamentoUnit', 'departamento_unit_id', '{departamento_unit->system_unit->name}   - {departamento_unit->name}','departamento_unit_id asc' , $criteria_departamento_unit_id );
        //         $departamento_unit_id->setMinLength(2);
        // $departamento_unit_id->setFilterColumns(['name']);
        // $departamento_unit_id->setMask('{system_unit->name} - {name}');

        $saldo_departamento_id = new TDBUniqueSearch('saldo_departamento_id', 'minierp', 'SaldoDepartamento', 'id', 'numero_documento_empenho', 'numero_documento_empenho asc', $criteria_saldo_departamento_id);
        $data_limite_resposta = new TDateTime('data_limite_resposta');
        $obs = new TText('obs');
        $itens_pedido_pedido_venda_id = new THidden('itens_pedido_pedido_venda_id[]');
        $itens_pedido_pedido_venda___row__id = new THidden('itens_pedido_pedido_venda___row__id[]');
        $itens_pedido_pedido_venda___row__data = new THidden('itens_pedido_pedido_venda___row__data[]');
        $itens_pedido_pedido_venda_produto_id = new TDBUniqueSearch('itens_pedido_pedido_venda_produto_id[]', 'minierp', 'Produto', 'id', 'nome','nome asc' , $criteria_itens_pedido_pedido_venda_produto_id );
        $itens_pedido_pedido_venda_unidade_medida_id = new TDBCombo('itens_pedido_pedido_venda_unidade_medida_id[]', 'minierp', 'UnidadeMedida', 'id', '{nome}','nome asc' , $criteria_itens_pedido_pedido_venda_unidade_medida_id );
        $itens_pedido_pedido_venda_quantidade = new TNumeric('itens_pedido_pedido_venda_quantidade[]', '2', ',', '.' );
        $itens_pedido_pedido_venda_valor = new TNumeric('itens_pedido_pedido_venda_valor[]', '2', ',', '.' );
        $itens_pedido_pedido_venda_valor_total = new TNumeric('itens_pedido_pedido_venda_valor_total[]', '2', ',', '.' );
        $itens_pedido_pedido_venda_obs = new TEntry('itens_pedido_pedido_venda_obs[]');
        $this->fieldList_666d9353ef312 = new TFieldList();
        $cidade_pedido_pedido_id = new THidden('cidade_pedido_pedido_id[]');
        $cidade_pedido_pedido___row__id = new THidden('cidade_pedido_pedido___row__id[]');
        $cidade_pedido_pedido___row__data = new THidden('cidade_pedido_pedido___row__data[]');
        $cidade_pedido_pedido_cidade_id = new TDBUniqueSearch('cidade_pedido_pedido_cidade_id[]', 'minierp', 'Cidade', 'id', 'nome','nome asc' , $criteria_cidade_pedido_pedido_cidade_id );
//         $cidade_pedido_pedido_cidade_id->setMinLength(2);
//   //      $cidade_pedido_pedido_cidade_id->setFilterColumns(["nome"]);
//         $cidade_pedido_pedido_cidade_id->setFilterColumns(["nome", "estado->sigla"]);

        $this->fieldList_666dab775c342 = new TFieldList();
        $pedido_seguimento_pedido_id = new THidden('pedido_seguimento_pedido_id[]');
        $pedido_seguimento_pedido___row__id = new THidden('pedido_seguimento_pedido___row__id[]');
        $pedido_seguimento_pedido___row__data = new THidden('pedido_seguimento_pedido___row__data[]');
        $pedido_seguimento_pedido_seguimento_id = new TDBUniqueSearch('pedido_seguimento_pedido_seguimento_id[]', 'minierp', 'Seguimento', 'id', 'descricao','id asc' , $criteria_pedido_seguimento_pedido_seguimento_id );
        //  $pedido_seguimento_pedido_seguimento_id->setMinLength(2);
        // $pedido_seguimento_pedido_seguimento_id->setFilterColumns(["nome"]);
        $this->fieldList_666dab925c346 = new TFieldList();
       $documentos_pedido_pedido_id = new THidden('documentos_pedido_pedido_id[]');
        $documentos_pedido_pedido___row__id = new THidden('documentos_pedido_pedido___row__id[]');
        $documentos_pedido_pedido___row__data = new THidden('documentos_pedido_pedido___row__data[]');
        $documentos_pedido_pedido_caminho = new TFile('documentos_pedido_pedido_caminho[]');
        $this->fieldList_6881456478881 = new TFieldList();
        $redes1 = new BPageContainer();
        $cotacao_pedido_id = new THidden('cotacao_pedido_id[]');
        $cotacao_pedido___row__id = new THidden('cotacao_pedido___row__id[]');
        $cotacao_pedido___row__data = new THidden('cotacao_pedido___row__data[]');
        $cotacao_pedido_pessoa_id = new TDBUniqueSearch('cotacao_pedido_pessoa_id[]', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_cotacao_pedido_pessoa_id );
        $cotacao_pedido_pessoa_id->setMinLength(2);
        $cotacao_pedido_pessoa_id->setFilterColumns(["nome"]);
                $cotacao_pedido_data_cotacao = new TDate('cotacao_pedido_data_cotacao[]');
        $cotacao_pedido_system_users_id = new TDBUniqueSearch('cotacao_pedido_system_users_id[]', 'minierp', 'SystemUsers', 'id', '{name}','name asc' , $criteria_cotacao_pedido_system_users_id );
    $cotacao_pedido_system_users_id->setMinLength(2);
        $cotacao_pedido_system_users_id->setFilterColumns(["nome"]);        
        $cotacao_pedido_estado_pedido_id = new TDBCombo('cotacao_pedido_estado_pedido_id[]', 'minierp', 'EstadoPedido', 'id', '{nome}','nome asc' , $criteria_cotacao_pedido_estado_pedido_id );
        $this->fieldList_666dd89f2e46f = new TFieldList();

        $this->fieldList_666d9353ef312->addField(null, $itens_pedido_pedido_venda_id, []);
        $this->fieldList_666d9353ef312->addField(null, $itens_pedido_pedido_venda___row__id, ['uniqid' => true]);
        $this->fieldList_666d9353ef312->addField(null, $itens_pedido_pedido_venda___row__data, []);
        $this->fieldList_666d9353ef312->addField(new TLabel("Produto/Serviço", null, '14px', null), $itens_pedido_pedido_venda_produto_id, ['width' => '30%']);
        $this->fieldList_666d9353ef312->addField(new TLabel("Unidade", null, '14px', null), $itens_pedido_pedido_venda_unidade_medida_id, ['width' => '15%','sum' => true]);
        $this->fieldList_666d9353ef312->addField(new TLabel("Quantidade", null, '14px', null), $itens_pedido_pedido_venda_quantidade, ['width' => '10%','sum' => true]);
        $this->fieldList_666d9353ef312->addField(new TLabel("Valor", null, '14px', null), $itens_pedido_pedido_venda_valor, ['width' => '10%','sum' => true]);
        $this->fieldList_666d9353ef312->addField(new TLabel("Valor total", null, '14px', null), $itens_pedido_pedido_venda_valor_total, ['width' => '10%','sum' => true]);
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
        $this->form->addField($itens_pedido_pedido_venda_unidade_medida_id);
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

         $this->fieldList_6881456478881->addField(null, $documentos_pedido_pedido_id, []);
        $this->fieldList_6881456478881->addField(null, $documentos_pedido_pedido___row__id, ['uniqid' => true]);
        $this->fieldList_6881456478881->addField(null, $documentos_pedido_pedido___row__data, []);
        $this->fieldList_6881456478881->addField(new TLabel("Caminho", null, '14px', null), $documentos_pedido_pedido_caminho, ['width' => '100%']);

        $this->fieldList_6881456478881->width = '100%';
        $this->fieldList_6881456478881->setFieldPrefix('documentos_pedido_pedido');
        $this->fieldList_6881456478881->name = 'fieldList_6881456478881';

        $this->criteria_fieldList_6881456478881 = new TCriteria();
        $this->default_item_fieldList_6881456478881 = new stdClass();

        $this->form->addField($documentos_pedido_pedido_id);
        $this->form->addField($documentos_pedido_pedido___row__id);
        $this->form->addField($documentos_pedido_pedido___row__data);
        $this->form->addField($documentos_pedido_pedido_caminho);

        $this->fieldList_6881456478881->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

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

        error_log('[PedidoVendaForm::__construct] widgets+fieldlists: '.round(microtime(true) - $perf_start, 3).'s');

        $itens_pedido_pedido_venda_produto_id->setChangeAction(new TAction([$this,'onBuscaProduto']));

        $itens_pedido_pedido_venda_quantidade->setExitAction(new TAction([$this,'onCalcValor']));

        $dt_pedido->addValidation("Data do Pedido", new TRequiredValidator()); 
        $departamento_unit_id->addValidation("Departamentos", new TRequiredValidator()); 
        $itens_pedido_pedido_venda_produto_id->addValidation("Buscar Produto", new TRequiredListValidator()); 
        $itens_pedido_pedido_venda_quantidade->addValidation("Quantidade do Produto", new TRequiredListValidator());
        $data_limite_resposta->addValidation("Data limite resposta do pedido não foi informado!", new TRequiredValidator());  
        $saldo_departamento_id->addValidation("Dotação orçamentária", new TRequiredValidator()); 

        $itens_pedido_pedido_venda_produto_id->setMinLength(2);
        $itens_pedido_pedido_venda_produto_id->setFilterColumns(["nome"]);
        // $itens_pedido_pedido_venda_produto_id->configureNoResultsCreateButton(new TAction(['CadastroProdutoForm', 'onShow']), "Cadastrar", "fas:plus #69AA46", "btn-default");
        // $itens_pedido_pedido_venda_produto_id->setNoResultsMessage("Produto não encontrado click no botão cadastrar");

        $documentos_pedido_pedido_caminho->enableFileHandling();
        $dt_pedido->setDatabaseMask('yyyy-mm-dd');
        $cotacao_pedido_data_cotacao->setDatabaseMask('yyyy-mm-dd');
        $data_limite_resposta->setMask('dd/mm/yyyy hh:ii');
        $data_limite_resposta->setDatabaseMask('yyyy-mm-dd hh:ii');
        error_log('[PedidoVendaForm::__construct] total: '.round(microtime(true) - $perf_start, 3).'s');

        $dt_pedido->setMask('dd/mm/yyyy');
        $cotacao_pedido_data_cotacao->setMask('dd/mm/yyyy');
        $itens_pedido_pedido_venda_produto_id->setMask('{tipo_produto->nome}: {nome}');

        $id->setEditable(false);
                $itens_pedido_pedido_venda_unidade_medida_id->setEditable(false);

        $cotacao_pedido_pessoa_id->setEditable(false);
        $cotacao_pedido_data_cotacao->setEditable(false);
        $cotacao_pedido_system_users_id->setEditable(false);
        $cotacao_pedido_estado_pedido_id->setEditable(false);

        $centrocusto_id->setMinLength(2);
        $centrocusto_id->setFilterColumns(['nome']);
        $departamento_unit_id->enableSearch();
        $cotacao_pedido_pessoa_id->enableSearch();
        $cidade_pedido_pedido_cidade_id->setMinLength(2);
        $cidade_pedido_pedido_cidade_id->setFilterColumns(['nome']);
        $cidade_pedido_pedido_cidade_id->setMask('{nome} - {estado->sigla}');
        $cotacao_pedido_system_users_id->enableSearch();
        $cotacao_pedido_estado_pedido_id->enableSearch();
        $pedido_seguimento_pedido_seguimento_id->setMinLength(2);
        $pedido_seguimento_pedido_seguimento_id->setFilterColumns(['descricao']);
        $saldo_departamento_id->setMinLength(2);
        $saldo_departamento_id->setFilterColumns(['numero_documento_empenho', 'historico']);
        $saldo_departamento_id->setMask('{departamento_unit->name} - {numero_documento_empenho} - {historico} - {valor_empenho_formatado}');

        $id->setSize(100);
        $dt_pedido->setSize(110);
        $obs->setSize('100%', 70);
        $centrocusto_id->setSize('100%');
        $descricaopedido->setSize('100%');
        $data_limite_resposta->setSize(110);
        $departamento_unit_id->setSize('100%');
        $cotacao_pedido_pessoa_id->setSize('100%');
        $cotacao_pedido_data_cotacao->setSize(110);
        $itens_pedido_pedido_venda_obs->setSize('100%');
        $itens_pedido_pedido_venda_valor->setSize('80%');
        $cidade_pedido_pedido_cidade_id->setSize('100%');
        $cotacao_pedido_system_users_id->setSize('100%');
        $cotacao_pedido_estado_pedido_id->setSize('100%');
        $documentos_pedido_pedido_caminho->setSize('100%');
        $itens_pedido_pedido_venda_produto_id->setSize(750);
        $itens_pedido_pedido_venda_unidade_medida_id->setSize('100%');
        $itens_pedido_pedido_venda_quantidade->setSize('100%');
        $itens_pedido_pedido_venda_valor_total->setSize('100%');
        $itens_pedido_pedido_venda_obs->setSize('100%');
        $pedido_seguimento_pedido_seguimento_id->setSize('100%');
        $saldo_departamento_id->setSize('100%');
        $redes1->setSize('100%');

        $redes1->setAction(new TAction(['ViewRedesdisponiveisComprasList', 'onSetProject']));
        $redes1->setId('b67e069c874c15');

        $loadingContainer1 = new TElement('div');
        $loadingContainer1->style = 'text-align:center; padding:50px';
        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';
        $loadingContainer1->add($icon);
        $loadingContainer1->add('<br>Carregando');
        $redes1->add($loadingContainer1);
        $this->redes1 = $redes1;

        $tab_666d91088c834 = new BootstrapFormBuilder('tab_666d91088c834');
        $this->tab_666d91088c834 = $tab_666d91088c834;
        $tab_666d91088c834->setProperty('style', 'border:none; box-shadow:none;');
 
        $tab_666d91088c834->appendPage("Dados/Itens");

        $tab_666d91088c834->addFields([new THidden('current_tab_tab_666d91088c834')]);
        $tab_666d91088c834->setTabFunction("$('[name=current_tab_tab_666d91088c834]').val($(this).attr('data-current_page'));");

        $row1 = $tab_666d91088c834->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Data Pedido:", '#FF0000', '14px', null, '100%'),$dt_pedido],[new TLabel("Data Limite Resposta:", '#FF0000', '14px', null, '100%'),$data_limite_resposta],[new TLabel("Nome ou titulo deste pedido para localização futura:", '#ff0000', '14px', null, '100%'),$descricaopedido]);
        $row1->layout = [' col-sm-2',' col-sm-2',' col-sm-2','col-sm-6'];

        $row2 = $tab_666d91088c834->addFields([new TLabel("Centro de Custo", '#FF0000', '14px', null),$centrocusto_id],[new TLabel("Departamentos / Secretárias", '#FF0000', '14px', null),$departamento_unit_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $tab_666d91088c834->addFields([new TLabel("Dotação orçamentária: *", '#FF0000', '14px', null, '100%'),$saldo_departamento_id],[new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row3->layout = ['col-sm-6','col-sm-6'];

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

        $tab_666d91088c834->appendPage("Redes disponiveis");
        $row6 = $tab_666d91088c834->addFields([$redes1]);
        $row6->layout = [' col-sm-12'];

        $tab_666d91088c834->appendPage("Arquivos");
        $row7 = $tab_666d91088c834->addFields([$this->fieldList_6881456478881]);
        $row7->layout = [' col-sm-12'];

        $tab_666d91088c834->appendPage("Propostas em Andamento");
        $row8 = $tab_666d91088c834->addFields([$this->fieldList_666dd89f2e46f]);
        $row8->layout = [' col-sm-12'];

        $row10 = $this->form->addFields([new TLabel("Tipo do pedido:", '#FF0000', '14px', null, '100%'),$tab_666d91088c834]);
        $row10->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;
        
        $btn_onshow = $this->form->addAction("Consulta produtos", new TAction(['ConsultaProdutoList', 'onShow']), 'fas:search #000000');
        $this->btn_onshow = $btn_onshow;

        $btn_onsetproject = $this->form->addAction("Voltar", new TAction(['PedidoVendaList', 'onSetProject']), 'fas:arrow-left #000000');
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

        $style = new TStyle('right-panel > .container-part[page-name=PedidoVendaForm]');
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

    public static function onBuscaProduto($param = null) 
    {
        try 
        {
            TSession::setValue('idproduto', NULL);

            TTransaction::open(self::$database); // abre a transação

            // Carrega o produto selecionado
            $produto = new Produto($param['_field_value']);

            TSession::setValue('idproduto', $param['_field_value']);

            // Obtém o conteúdo JSON com os dados da linha
            $conteudojson = $param['_field_data_json'];
            $idlinha = json_decode($conteudojson);
            if (isset($idlinha->{'row'})) {
                $idlinha = $idlinha->{'row'}; // número da linha (ex: 51627171)
            }

            // ID do campo de produto (ex: itens_pedido_pedido_venda_produto_id_51627171)
            $id = $param['_field_id'];

            // Converte preço de venda com vírgula
            $valor = str_replace('.', ',', $produto->preco_venda);

            // Pega o ID da unidade de medida
            $unidade_medida_id = $produto->unidade_medida_id;

            // Extrai o sufixo numérico do ID do campo de produto
            $id_sufixo = str_replace('itens_pedido_pedido_venda_produto_id_', '', $id);

            // Preenche o valor no campo correspondente
            TScript::create("$('#{$id}').parent().parent().find('[name=\"itens_pedido_pedido_venda_valor[]\"]').val('{$valor}');");

            // Log para depuração (opcional)
            TScript::create("console.log('Setando unidade no campo: itens_pedido_pedido_venda_unidade_medida_id_{$id_sufixo}');");

            // Preenche a unidade de medida no campo correto
            TScript::create("$('#itens_pedido_pedido_venda_unidade_medida_id_{$id_sufixo}').val({$unidade_medida_id}).change();");

            TTransaction::close(); // fecha a transação
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

            $data = $this->form->getData(); // get form data as array
            $this->protegerItensCotadosNoSave($data);
            $object = !empty($data->id) ? new Pedido($data->id) : new Pedido();
            $object->fromArray( (array) $data); // load the object with data

            $documentos_pedido_pedido_caminho_dir = 'app/documentos';  

            $pedidoNovo = false ;

            if(!$data->id)
            {
                $pedidoNovo = true;
                $object->estado_pedido_venda_id = EstadoPedido::PENDENTE;
                $object->system_users_id = TSession::getValue('userid');
            }

            $data_limite_resposta = new DateTime($data->data_limite_resposta);
            $dt_pedido = new DateTime($data->dt_pedido);

            $object->data_limite_resposta = $data_limite_resposta->format('Y-m-d H:i:s');
            $cotacoes = Cotacao::where('pedido_id','=', $object->id)
                                  ->load();

            if($cotacoes){
                foreach($cotacoes as $cotacao){
                    $cotacao->data_limite_resposta = $data_limite_resposta->format('Y-m-d H:i:s');
                    if($cotacao->departamento_unit_id != $object->departamento_unit_id){
                        $cotacao->departamento_unit_id = $object->departamento_unit_id;
                    }
                    if($cotacao->obs != $object->obs){
                        $cotacao->obs = $object->obs;
                    }
                    $cotacao->store();
                }
            }

            $object->mes = $dt_pedido->format('m');
            $object->ano = $dt_pedido->format('Y');

            $object->valor_total = 0;

            $object->system_unit_id = TSession::getValue('idunit');
            $object->entidade_id = TSession::getValue('entidade');
            $object->store(); // save the object 
            $this->validateDuplicatedProdutosByPedido($object, $data);

            $cotacao = Cotacao::where('pedido_id','=',$object->id)
                                      ->load();
            if ($cotacao) {
                foreach ($cotacao as $cot) {
                    if ($cot->departamento_unit_id<>$object->departamento_unit_id) {
                        $cot->departamento_unit_id = $object->departamento_unit_id;
                        $cot->store();
                    }
                }
            }
            
            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

//</generatedAutoCode> 

    //<fieldList-2093438-17007069> //</hideLine>
            $documentos_pedido_pedido_items = $this->storeItems('DocumentosPedido', 'pedido_id', $object, $this->fieldList_6881456478881, function($masterObject, $detailObject){ //</blockLine>

                //code here

                //</autoCode>
            }, $this->criteria_fieldList_6881456478881); //</blockLine>
    //</hideLine> //</fieldList-2093438-17007069>
//<generatedAutoCode>
            if(!empty($documentos_pedido_pedido_items))
            {
                foreach ($documentos_pedido_pedido_items as $item)
                {
                    $dataFile = new stdClass();
                    $dataFile->caminho = $item->caminho;
                    $this->saveFile($item, $dataFile, 'caminho', $documentos_pedido_pedido_caminho_dir);
                }
            }

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
            $pes1 = Pessoa::where('system_user_id','=',TSession::getValue('userid'))
            ->load();
            if ($pes1) {
                foreach ($pes1 as $pessoass) {

                }
                $object->cliente_id = $pessoass->id;
            }
         //   var_dump($somatotal);
            $object->estado_pedido1_id=null;
            $object->valor_total = $somatotal;
            $object->store();

            // Sincroniza os vinculos de redes: remove desmarcados e grava somente marcados
            $pessoasSelecionadas = [];

            if ($pes1)
            {
                $pessoasSelecionadas[] = (int) $pes1[0]->id;
            }
            else if (TSession::getValue('selecao_redes_aleatoria') == 2)
            {
                $redesSelecionadas = TSession::getValue('ViewRedesdisponiveisComprasListbuilder_datagrid_check');

                if ($redesSelecionadas && is_array($redesSelecionadas))
                {
                    foreach ($redesSelecionadas as $key => $value)
                    {
                        if (is_numeric($value))
                        {
                            $pessoasSelecionadas[] = (int) $value;
                        }
                        else if (is_numeric($key))
                        {
                            $pessoasSelecionadas[] = (int) $key;
                        }
                    }
                }
            }
            else
            {
                $idcidade = TSession::getValue('filtrocidade_id');
                $idseguimento = TSession::getValue('filtroseguimento_id');
                $idgrupopessoa = GrupoPessoa::CONDUTOR;

                $criteriapessoa = new TCriteria();

                if ($idcidade)
                {
                    if (is_array($idcidade))
                    {
                        $idcidade = implode(',', $idcidade);
                    }

                    $subqueryCidade = "(SELECT pessoa_id FROM pessoa_endereco pe WHERE pe.cidade_id IN ({$idcidade}))";
                    $criteriapessoa->add(new TFilter('id', 'IN', $subqueryCidade));
                }

                if ($idseguimento)
                {
                    $subquerySeguimento = "(SELECT pessoa_id FROM seguimento_pessoa se WHERE se.seguimento_id = {$idseguimento})";
                    $criteriapessoa->add(new TFilter('id', 'IN', $subquerySeguimento));
                }

                $subqueryGrupo = "(SELECT pessoa_id FROM pessoa_grupo pg WHERE pg.grupo_pessoa_id = {$idgrupopessoa})";
                $criteriapessoa->add(new TFilter('id', 'NOT IN', $subqueryGrupo));

                $repopessoa = new TRepository('Pessoa');
                $pessoasfiltradas = $repopessoa->load($criteriapessoa);

                if ($pessoasfiltradas)
                {
                    foreach ($pessoasfiltradas as $pessoa)
                    {
                        $pessoasSelecionadas[] = (int) $pessoa->id;
                    }
                }
            }

            $pessoasSelecionadas = array_values(array_unique(array_filter($pessoasSelecionadas)));

            $criteriaRel = new TCriteria();
            $criteriaRel->add(new TFilter('pedido_id', '=', $data->id));
            $relacoesAtuais = (new TRepository('PedidocompraAsCliente'))->load($criteriaRel);

            if ($relacoesAtuais)
            {
                foreach ($relacoesAtuais as $relacaoAtual)
                {
                    $relacaoAtual->delete();
                }
            }

            foreach ($pessoasSelecionadas as $pessoaId)
            {
                $relacao = new PedidocompraAsCliente();
                $relacao->pedido_id = $data->id;
                $relacao->pessoa_id = $pessoaId;
                $relacao->store();
            }

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            if($pedidoNovo)
            {
                TTransaction::open(self::$database);

                PedidoVendaService::notificarAprovador($object);

                TTransaction::close();

                    $loadPageParam["pedido_id"] = $object->id;
                   $loadPageParam["inserido"] = false;
            } else {
                   $loadPageParam["pedido_id"] = $object->id;
                   $loadPageParam["inserido"] = true;
              
            }

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('PedidoVendaList', 'onShow', $loadPageParam);
            /*

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('PedidoVendaList', 'onShow', $loadPageParam); 

           */
                        TScript::create("Template.closeRightPanel();");
            TForm::sendData(self::$formName, (object)['id' => $object->id]);

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode>  

            new TMessage('error', $this->getFriendlySaveErrorMessage($e)); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    private function validateDuplicatedProdutosByPedido($pedido, stdClass $data): void
    {
        $produtoRows = $data->itens_pedido_pedido_venda_produto_id ?? [];
        $itemRows = $data->itens_pedido_pedido_venda_id ?? [];

        if (!is_array($produtoRows))
        {
            return;
        }

        $maxRows = max(count($produtoRows), is_array($itemRows) ? count($itemRows) : 0);
        $produtosByRow = [];
        $duplicados = [];

        for ($i = 0; $i < $maxRows; $i++)
        {
            $produtoId = (int) ($produtoRows[$i] ?? 0);
            $itemId = is_array($itemRows) ? (int) ($itemRows[$i] ?? 0) : 0;

            if ($produtoId <= 0)
            {
                continue;
            }

            if (isset($produtosByRow[$produtoId]) && $produtosByRow[$produtoId] !== $itemId)
            {
                $duplicados[$produtoId] = true;
                continue;
            }

            $produtosByRow[$produtoId] = $itemId;

            if (!empty($pedido->id))
            {
                $existente = ItensPedido::where('pedido_venda_id', '=', $pedido->id)
                    ->where('produto_id', '=', $produtoId)
                    ->first();

                if ($existente && (int) $existente->id !== $itemId)
                {
                    $duplicados[$produtoId] = true;
                }
            }
        }

        if (!empty($duplicados))
        {
            $produtoIds = array_map('intval', array_keys($duplicados));
            $nomes = Produto::where('id', 'in', $produtoIds)->getIndexedArray('id', 'nome');
            $lista = !empty($nomes) ? implode(', ', $nomes) : implode(', ', $produtoIds);
            throw new Exception("Produto duplicado no pedido: {$lista}.");
        }
    }

    private function protegerItensCotadosNoSave(stdClass $data): void
    {
        $pedidoId = (int) ($data->id ?? 0);
        if ($pedidoId <= 0)
        {
            return;
        }

        $itemRows = isset($data->itens_pedido_pedido_venda_id) && is_array($data->itens_pedido_pedido_venda_id)
            ? $data->itens_pedido_pedido_venda_id
            : [];
        $produtoRows = isset($data->itens_pedido_pedido_venda_produto_id) && is_array($data->itens_pedido_pedido_venda_produto_id)
            ? $data->itens_pedido_pedido_venda_produto_id
            : [];
        $unidadeRows = isset($data->itens_pedido_pedido_venda_unidade_medida_id) && is_array($data->itens_pedido_pedido_venda_unidade_medida_id)
            ? $data->itens_pedido_pedido_venda_unidade_medida_id
            : [];
        $obsRows = isset($data->itens_pedido_pedido_venda_obs) && is_array($data->itens_pedido_pedido_venda_obs)
            ? $data->itens_pedido_pedido_venda_obs
            : [];

        $quotedItems = ItensPedido::where('pedido_venda_id', '=', $pedidoId)->load() ?: [];
        if (!$quotedItems)
        {
            return;
        }

        $usedRows = [];

        foreach ($quotedItems as $item)
        {
            $cotacoes = ItensCotacao::where('itens_pedido_id', '=', $item->id)->count();
            if ($cotacoes <= 0)
            {
                continue;
            }

            $jaPresente = false;
            foreach ($itemRows as $rowId)
            {
                if ((int) $rowId === (int) $item->id)
                {
                    $jaPresente = true;
                    break;
                }
            }

            if ($jaPresente)
            {
                continue;
            }

            $rowMatch = $this->buscarLinhaCompativelItemCotado($item, $itemRows, $produtoRows, $unidadeRows, $obsRows, $usedRows);
            if ($rowMatch !== null)
            {
                $itemRows[$rowMatch] = $item->id;
                $usedRows[$rowMatch] = true;
            }
        }

        $data->itens_pedido_pedido_venda_id = $itemRows;

        $faltantes = [];
        foreach ($quotedItems as $item)
        {
            $cotacoes = ItensCotacao::where('itens_pedido_id', '=', $item->id)->count();
            if ($cotacoes <= 0)
            {
                continue;
            }

            if (!in_array((int) $item->id, array_map('intval', $itemRows), true))
            {
                $faltantes[] = $item;
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
            throw new Exception("Nao foi possivel salvar porque ha itens com cotacao vinculada que seriam removidos do pedido: {$lista}. Reabra a linha do item ou revise/exclua a cotacao antes.");
        }
    }

    private function buscarLinhaCompativelItemCotado(ItensPedido $item, array $itemRows, array $produtoRows, array $unidadeRows, array $obsRows, array $usedRows): ?int
    {
        foreach ($produtoRows as $index => $produtoId)
        {
            if (isset($usedRows[$index]))
            {
                continue;
            }

            $postedId = (int) ($itemRows[$index] ?? 0);
            if ($postedId > 0)
            {
                continue;
            }

            if ((int) $produtoId !== (int) $item->produto_id)
            {
                continue;
            }

            $unidadeOk = empty($unidadeRows[$index]) || (int) $unidadeRows[$index] === (int) $item->unidade_medida_id;
            if (!$unidadeOk)
            {
                continue;
            }

            $obsAtual = trim((string) ($obsRows[$index] ?? ''));
            $obsItem = trim((string) ($item->obs ?? ''));
            if ($obsAtual !== '' && $obsItem !== '' && $obsAtual !== $obsItem)
            {
                continue;
            }

            return $index;
        }

        return null;
    }

    private function getFriendlySaveErrorMessage(Exception $e): string
    {
        $message = $e->getMessage();
        $isUniqueViolation = stripos($message, 'SQLSTATE[23000]') !== false
            && stripos($message, 'CHK_UNIQUE') !== false;

        if ($isUniqueViolation)
        {
            return 'Ja existe item com o mesmo produto para este pedido. Remova o item duplicado e salve novamente.';
        }

        return $message;
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
                TSession::setValue('pedido_compra_id', null);
                TSession::setValue('pedido_compra_id', $object->id);

                 $this->fieldList_6881456478881_items = $this->loadItems('DocumentosPedido', 'pedido_id', $object, $this->fieldList_6881456478881, function($masterObject, $detailObject, $objectItems){ //</blockLine>

                                //code here

                                //</autoCode>
                            }, $this->criteria_fieldList_6881456478881); //</blockLine>
    //</hideLine> //</fieldList-2093438-17007069>

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

                TSession::setValue('old_items', $this->fieldList_666d9353ef312_items ?: []);

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
        TSession::setValue('pedido_compra_id', null);
        $this->form->clear(true);

        $this->fieldList_666d9353ef312->addHeader();
        $this->fieldList_666d9353ef312->addDetail($this->default_item_fieldList_666d9353ef312);

        $this->fieldList_666d9353ef312->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_6881456478881->addHeader();
        $this->fieldList_6881456478881->addDetail($this->default_item_fieldList_6881456478881);

        $this->fieldList_6881456478881->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_666dd89f2e46f->addHeader();
        $this->fieldList_666dd89f2e46f->addDetail($this->default_item_fieldList_666dd89f2e46f);

    }

    public function onShow($param = null)
    {
        if (empty($param['key']))
        {
            TSession::setValue('pedido_compra_id', null);
        }

        $this->fieldList_666d9353ef312->addHeader();
        $this->fieldList_666d9353ef312->addDetail($this->default_item_fieldList_666d9353ef312);

        $this->fieldList_666d9353ef312->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

       $this->fieldList_6881456478881->addHeader();
        $this->fieldList_6881456478881->addDetail($this->default_item_fieldList_6881456478881);

        $this->fieldList_6881456478881->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

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
            TApplication::loadPage('PedidoVendaList', 'onShow');
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
     private function onConsultaTabela($param = null){
       TApplication::loadPage('ConsultaProdutoList', 'onShow');
   
    }  

}

