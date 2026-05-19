<?php

//<fileHeader>
//<fileHeader>
// ===== SUIV client (carregamento manual) =====
$__suivClientPath = __DIR__ . '/../../service/SuivClient.php';
if (!file_exists($__suivClientPath)) {
    throw new Exception('SuivClient.php não encontrado em: ' . $__suivClientPath);
}
require_once $__suivClientPath;

if (!class_exists(\app\service\SuivClient::class)) {
    throw new Exception('Classe \app\service\SuivClient não foi carregada');
}

use Adianti\Widget\Wrapper\TDBUniqueSearch;
use app\service\SuivClient; // importa o nome curto
//</fileHeader>

//</fileHeader>

class PedidoFrotasForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'PedidoFrotas';
    private static $primaryKey = 'id';
    private static $formName = 'form_PedidoFrotasForm';

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
        if (isset($param['editando'])) {
            TSession::setValue('editando',null);
            if( in_array($param['status'], [EstadoPedidoFrotas::ENVIADO, EstadoPedidoFrotas::COMPROPOSTA, EstadoPedidoFrotas::PREAPROVADO]) )
            {
               TSession::setValue('editando',null);
               TSession::setValue('editando',1);
            }
        } 

           $basename   = urlencode('pedido-frotas-list.pdf');
$download   = "download.php?file=app/manual/pedido-frotas-list.pdf&basename={$basename}";

$manual = "
    <span style='float:right;'>
        <a href='{$download}'
           target='_blank'
           style='text-decoration:none;margin-left:10px;'>
            <i class='fa fa-question-circle'> </i>
        </a>
    </span>
"; 
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title 
        $this->form->setFormTitle("Cadastro de Pedido de Frotas {$manual}");

        $criteria_orcamento_base_id = new TCriteria();
        $criteria_veiculos_id = new TCriteria();
        $criteria_veiculos_id->add(new TFilter('status_veiculo_id', '=', 1));
        $criteria_veiculos_id->add(new TFilter('system_unit_id', '=', (int) TSession::getValue('idunit')));
        $criteria_saldo_departamento_id = new TCriteria();
        $criteria_itens_pedido_frotas_pedido_frotas_produto_familia_produto_id = new TCriteria();
        $criteria_itens_pedido_frotas_pedido_frotas_produto_familia_produto_id->add(new TFilter('suiv_id', 'is not ', null)); 
        $criteria_itens_pedido_frotas_pedido_frotas_produto_familia_produto_id->add(new TFilter('suiv_id', '<>', -1)); 

        $orcamento_base = new TCheckButton('orcamento_base');
        $orcamento_base->setValue('2');
        $orcamento_base->setUseSwitch(true, 'blue');
        $orcamento_base->setIndexValue("1");
        $orcamento_base->setInactiveIndexValue("2");
        $orcamento_base->setSize('100%');

                $criteria_departamento_unit_id = new TCriteria();
        $criteria_tipo_manutencao_id = new TCriteria();
        $criteria_itens_pedido_frotas_pedido_frotas_produto_id = new TCriteria();
        $identidade = TSession::getValue('entidade');
        $filterVar = TSession::getValue('entidade');
        $criteria_saldo_departamento_id->add(new TFilter('saldo_entidade_contrato_id', 'in', "(SELECT id FROM saldo_entidade_contrato WHERE  deleted_at is null AND entidade_id in (SELECT id FROM entidade WHERE id = '{$filterVar}'))")); 
    
    
        $criteria_itens_pedido_frotas_pedido_frotas_produto_id->add(
            new TFilter('system_unit_id', 'IN',
                "(SELECT su.id FROM system_unit su 
                LEFT JOIN entidade e ON e.id = su.entidade_id 
                WHERE e.frotas = 1)"
            )
        );

        
        $filterVar = TSession::getValue('idunit');
        $criteria_departamento_unit_id->add(new TFilter('departamento_unit_id', 'in', "(SELECT id FROM departamento_unit WHERE system_unit_id = '{$filterVar}')")); 
        $criteria_orcamento_base_id = new TCriteria();

        if (isset($param['id'])) {
            $filterVar = $param['id'];
            $criteria_orcamento_base_id->add(new TFilter('id', '<>', $filterVar));
        }

        $criteria_orcamento_base_id->add(new TFilter('orcamento_base', '=', 1)); 
        $criteria_orcamento_base_id->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit'))); 

        // $filterVar = TSession::getValue('idunit');
        // $criteria_itens_pedido_frotas_pedido_frotas_produto_id->add(new TFilter('system_unit_id', '=', $filterVar)); 
        //<onBeginPageCreation>

        //</onBeginPageCreation>
        $login = new LoginForm([]);
        $AlertMensagem = $login->onMensagem('PedidoFrotasForm');
        $TAlert = new TAlert('danger',$AlertMensagem);

        $id = new TEntry('id');
        $dt_pedido = new TDateTime('dt_pedido');
        $data_limite_resposta = new TDateTime('data_limite_resposta');
        $descricaopedido = new TEntry('descricaopedido');
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa} - {marca->descricao} - {modelo->descricao}', 'placa asc', $criteria_veiculos_id);
        $orcamento_base_id = new TDBCombo('orcamento_base_id', 'minierp', 'PedidoFrotas', 'id', 'id','id asc' , $criteria_orcamento_base_id );
        $veiculos_id->setChangeAction(new TAction([$this, 'onExitVeiculo']));
        $ciclos = new TEntry('ciclos');
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'SystemUserDepartamentoUnit', 'departamento_unit_id', '{departamento_unit->system_unit->name}   - {departamento_unit->name}','departamento_unit_id asc' , $criteria_departamento_unit_id );
        $saldo_departamento_id = new TDBCombo('saldo_departamento_id', 'minierp', 'SaldoDepartamento', 'id', '{departamento_unit->name} - {numero_documento_empenho} - {historico} - {valor_empenho_formatado}', 'numero_documento_empenho asc', $criteria_saldo_departamento_id);
       

        $tipo_manutencao_id = new TDBCombo('tipo_manutencao_id', 'minierp', 'TipoManutencao', 'id', '{descricao}','descricao asc' , $criteria_tipo_manutencao_id );
        $km = new TEntry('km');
        $kmultima = new THidden('kmultima');
        $kmcalculada = new TEntry('kmcalculada');
        $kmcalculada->setProperty('style', 'padding: 0px; height:25px; background-color: white !important; color: black; border: 1px solid white !important; color: black;');
        $obs = new TText('obs');
        $itens_pedido_frotas_pedido_frotas_id = new THidden('itens_pedido_frotas_pedido_frotas_id[]');
        $itens_pedido_frotas_pedido_frotas___row__id = new THidden('itens_pedido_frotas_pedido_frotas___row__id[]');
        $itens_pedido_frotas_pedido_frotas___row__data = new THidden('itens_pedido_frotas_pedido_frotas___row__data[]');
        $itens_pedido_frotas_pedido_frotas_tipo = new TCombo('itens_pedido_frotas_pedido_frotas_tipo[]');
        $itens_pedido_frotas_pedido_frotas_produto_familia_produto_id = new TDBCombo('itens_pedido_frotas_pedido_frotas_produto_familia_produto_id[]', 'minierp', 'FamiliaProduto', 'id', '{nome}','nome asc' , $criteria_itens_pedido_frotas_pedido_frotas_produto_familia_produto_id );
        if (TSession::getValue('utiliza_temparia')==1) {
           $itens_pedido_frotas_pedido_frotas_produto_id = new TDBUniqueSearch('itens_pedido_frotas_pedido_frotas_produto_id[]', 'minierp', 'Produto', 'id', '{nome} - Grupo: {familia_produto->nome} - PartNumber: {suiv_partnumber}','nome asc' , $criteria_itens_pedido_frotas_pedido_frotas_produto_id );
        } else {
           $itens_pedido_frotas_pedido_frotas_produto_id = new TDBUniqueSearch('itens_pedido_frotas_pedido_frotas_produto_id[]', 'minierp', 'Produto', 'id', 'nome','nome asc' , $criteria_itens_pedido_frotas_pedido_frotas_produto_id );

        }
        // if (TSession::getValue('utiliza_temparia')==1) {
        //    $itens_pedido_frotas_pedido_frotas_produto_id = new TCombo('itens_pedido_frotas_pedido_frotas_produto_id[]');
        // } else {
      
        // }
        $itens_pedido_frotas_pedido_frotas_qtde = new TEntry('itens_pedido_frotas_pedido_frotas_qtde[]');
        $itens_pedido_frotas_pedido_frotas_descricao = new TText('itens_pedido_frotas_pedido_frotas_descricao[]');
        $this->fieldList_67410e656dea3 = new TFieldList();
        $documentos_pedido_frotas_pedido_frotas_id = new THidden('documentos_pedido_frotas_pedido_frotas_id[]');
        $documentos_pedido_frotas_pedido_frotas___row__id = new THidden('documentos_pedido_frotas_pedido_frotas___row__id[]');
        $documentos_pedido_frotas_pedido_frotas___row__data = new THidden('documentos_pedido_frotas_pedido_frotas___row__data[]');
        $documentos_pedido_frotas_pedido_frotas_caminho = new TFile('documentos_pedido_frotas_pedido_frotas_caminho[]');
        $this->fieldList_674111506deb6 = new TFieldList();
        $pedido_frotas_id_produto = new BPageContainer();
         $pedido_frotas_id_servico = new BPageContainer();
        $redes1 = new BPageContainer();
       
        $this->fieldList_67410e656dea3->addField(null, $itens_pedido_frotas_pedido_frotas_id, []);
        $this->fieldList_67410e656dea3->addField(null, $itens_pedido_frotas_pedido_frotas___row__id, ['uniqid' => true]);
        $this->fieldList_67410e656dea3->addField(null, $itens_pedido_frotas_pedido_frotas___row__data, []);
        $this->fieldList_67410e656dea3->addField(new TLabel("Tipo", null, '14px', null), $itens_pedido_frotas_pedido_frotas_tipo, ['width' => '15%']);
         if (TSession::getValue('utiliza_temparia')==1) {
            $this->fieldList_67410e656dea3->addField(new TLabel("Grupo", null, '14px', null), $itens_pedido_frotas_pedido_frotas_produto_familia_produto_id, ['width' => '15%']);
            $this->fieldList_67410e656dea3->addField(new TLabel("Descrição", null, '14px', null), $itens_pedido_frotas_pedido_frotas_produto_id, ['width' => '35%']);
         } else {
          $this->fieldList_67410e656dea3->addField(new TLabel("Descrição", null, '14px', null), $itens_pedido_frotas_pedido_frotas_produto_id, ['width' => '50%']);
         }
        $this->fieldList_67410e656dea3->addField(new TLabel("Qtde", null, '14px', null), $itens_pedido_frotas_pedido_frotas_qtde, ['width' => '10%']);
        $this->fieldList_67410e656dea3->addField(new TLabel("Obs", null, '14px', null), $itens_pedido_frotas_pedido_frotas_descricao, ['width' => '25%']);

        $this->fieldList_67410e656dea3->width = '100%';
        $this->fieldList_67410e656dea3->setFieldPrefix('itens_pedido_frotas_pedido_frotas');
        $this->fieldList_67410e656dea3->name = 'fieldList_67410e656dea3';

        $this->criteria_fieldList_67410e656dea3 = new TCriteria();
        $this->default_item_fieldList_67410e656dea3 = new stdClass();


        $this->form->addField($itens_pedido_frotas_pedido_frotas_id);
        $this->form->addField($itens_pedido_frotas_pedido_frotas___row__id);
        $this->form->addField($itens_pedido_frotas_pedido_frotas___row__data);
        $this->form->addField($itens_pedido_frotas_pedido_frotas_tipo);
        $this->form->addField($itens_pedido_frotas_pedido_frotas_produto_familia_produto_id);
        $this->form->addField($itens_pedido_frotas_pedido_frotas_produto_id);
        $this->form->addField($itens_pedido_frotas_pedido_frotas_qtde);
        $this->form->addField($itens_pedido_frotas_pedido_frotas_descricao);
        $this->form->addField($itens_pedido_frotas_pedido_frotas_qtde);

        $this->fieldList_67410e656dea3->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");
         $itens_pedido_frotas_pedido_frotas_produto_familia_produto_id->setChangeAction(new TAction([$this,'onChangeitens_pedido_frotas_pedido_frotas_produto_familia_produto_id']));

        $this->fieldList_674111506deb6->addField(null, $documentos_pedido_frotas_pedido_frotas_id, []);
        $this->fieldList_674111506deb6->addField(null, $documentos_pedido_frotas_pedido_frotas___row__id, ['uniqid' => true]);
        $this->fieldList_674111506deb6->addField(null, $documentos_pedido_frotas_pedido_frotas___row__data, []);
        $this->fieldList_674111506deb6->addField(new TLabel("Caminho", null, '14px', null), $documentos_pedido_frotas_pedido_frotas_caminho, ['width' => '100%']);

        $this->fieldList_674111506deb6->width = '100%';
        $this->fieldList_674111506deb6->setFieldPrefix('documentos_pedido_frotas_pedido_frotas');
        $this->fieldList_674111506deb6->name = 'fieldList_674111506deb6';

        $this->criteria_fieldList_674111506deb6 = new TCriteria();
        $this->default_item_fieldList_674111506deb6 = new stdClass();

        $this->form->addField($documentos_pedido_frotas_pedido_frotas_id);
        $this->form->addField($documentos_pedido_frotas_pedido_frotas___row__id);
        $this->form->addField($documentos_pedido_frotas_pedido_frotas___row__data);
        $this->form->addField($documentos_pedido_frotas_pedido_frotas_caminho);

        $this->fieldList_674111506deb6->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");
                      $itens_pedido_frotas_pedido_frotas_tipo->setChangeAction(new TAction([$this, 'onExitTipo']));

        $dt_pedido->addValidation("Data pedido não foi informado!", new TRequiredValidator()); 
        $data_limite_resposta->addValidation("Data limite resposta do pedido não foi informado!", new TRequiredValidator()); 
        $veiculos_id->addValidation("Veículo não foi informado!", new TRequiredValidator()); 
        $departamento_unit_id->addValidation("Departamento não foi informado!", new TRequiredValidator()); 
        $tipo_manutencao_id->addValidation("Tipo de manutenção não foi informado!", new TRequiredValidator()); 
        $km->addValidation("Kilometragem não foi informado!", new TRequiredValidator()); 
        
      
        $saldo_departamento_id->addValidation("Dotação orçamentária", new TRequiredValidator()); 

        $id->setEditable(false);
        $kmcalculada->setEditable(false);
        $dt_pedido->setMask('dd/mm/yyyy hh:ii');
        $dt_pedido->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_limite_resposta->setMask('dd/mm/yyyy hh:ii');
        $data_limite_resposta->setDatabaseMask('yyyy-mm-dd hh:ii');
        $descricaopedido->setMaxLength(60); 
        $tipo_manutencao_id->configureNoResultsQuickRegister(new TAction(['TipoManutencaoForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $tipo_manutencao_id->setNoResultsMessage("Nenhum tipo de manutenção encontrado. Clique no cadastrar");
        // $itens_pedido_frotas_pedido_frotas_produto_id->configureNoResultsQuickRegister(new TAction(['ProdutoSimpleForm', 'onQuickSave']), "Cadastrar", "fas:plus #69AA46", "btn-default");
        // $itens_pedido_frotas_pedido_frotas_produto_id->setNoResultsMessage("Cadastre um novo produto/serviço");

        $itens_pedido_frotas_pedido_frotas_tipo->addItems(["1"=>"Produto","2"=>"Serviço"]);
        $itens_pedido_frotas_pedido_frotas_tipo->setValue('1');
        // if (TSession::getValue('utiliza_temparia')==2) {
         //  $itens_pedido_frotas_pedido_frotas_produto_id->setMinLength(2);
        //   $itens_pedido_frotas_pedido_frotas_produto_id->setFilterColumns(["nome"]);
        // }
        $documentos_pedido_frotas_pedido_frotas_caminho->enableFileHandling();

        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';
        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';
        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');
        $pedido_frotas_id_produto->setId('b691a018e1c120');
        $pedido_frotas_id_produto->setAction(new TAction(['ItensPedidoFrotasProdutoList', 'onShow']));
        
        $pedido_frotas_id_produto->setSize('100%');
        $pedido_frotas_id_produto->add($loadingContainer);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';
        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';
        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');
        $this->pedido_frotas_id_produto = $pedido_frotas_id_produto;


        //servico
         $pedido_frotas_id_servico->setId('b691a018e1c121');
        $pedido_frotas_id_servico->setAction(new TAction(['ItensPedidoFrotasServicoList', 'onShow']));
        
        $pedido_frotas_id_servico->setSize('100%');
        $pedido_frotas_id_servico->add($loadingContainer);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';
        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';
        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');
        $this->pedido_frotas_id_servico = $pedido_frotas_id_servico;
        
        $redes1->setAction(new TAction(['ViewRedesdisponiveisList', 'onSetProject']));
        $redes1->setId('b67e069c874c14');

        $veiculos_id->enableSearch();
        $tipo_manutencao_id->enableSearch();
        $departamento_unit_id->enableSearch();
         $orcamento_base_id->enableSearch();
        $itens_pedido_frotas_pedido_frotas_tipo->enableSearch(); 
          $saldo_departamento_id->enableSearch();
        $itens_pedido_frotas_pedido_frotas_produto_id->enableSearch();

        $id->setSize(100);
        $km->setSize('100%');
        $ciclos->setSize('100%');
        $obs->setSize('100%');
        $dt_pedido->setSize(110);
        $data_limite_resposta->setSize(110);
        $kmcalculada->setSize(80);
        $redes1->setSize('100%');
        $veiculos_id->setSize('100%');
        $descricaopedido->setSize('100%');
        $tipo_manutencao_id->setSize('100%');
        $departamento_unit_id->setSize('100%');
         $orcamento_base_id->setSize('100%');
        $itens_pedido_frotas_pedido_frotas_tipo->setSize('100%');
                $itens_pedido_frotas_pedido_frotas_produto_id->setSize('100%');
         $itens_pedido_frotas_pedido_frotas_produto_familia_produto_id->setSize('100%');

        $itens_pedido_frotas_pedido_frotas_qtde->setSize('100%');
        $itens_pedido_frotas_pedido_frotas_descricao->setSize('100%','50%');
        $documentos_pedido_frotas_pedido_frotas_caminho->setSize('100%');
        $saldo_departamento_id->setSize('100%');

        $loadingContainer1 = new TElement('div');
        $loadingContainer1->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer1->add($icon);
        $loadingContainer1->add('<br>Carregando');

        $redes1->add($loadingContainer1);

        $this->redes1 = $redes1;



        $tab_67410dd86de9d = new BootstrapFormBuilder('tab_67410dd86de9d');
        $this->tab_67410dd86de9d = $tab_67410dd86de9d;
        $tab_67410dd86de9d->setProperty('style', 'border:none; box-shadow:none;');

        $tab_67410dd86de9d->appendPage("Dados / Itens do pedido");

        $tab_67410dd86de9d->addFields([new THidden('current_tab_tab_67410dd86de9d')]);
        $tab_67410dd86de9d->setTabFunction("$('[name=current_tab_tab_67410dd86de9d]').val($(this).attr('data-current_page'));");

        if (!empty($AlertMensagem)) {
            $row0 = $this->form->addFields([$TAlert]);
            $row0->layout = [' col-sm-12'];
        }
        $row1 = $tab_67410dd86de9d->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Dt pedido: *", '#FF0000', '14px', null, '100%'),$dt_pedido],[new TLabel("Dt limite resposta: *", '#FF0000', '14px', null, '100%'),$data_limite_resposta]);
        $row1->layout = ['col-sm-6','col-sm-3', 'col-sm-3'];

        $row2 = $tab_67410dd86de9d->addFields([new TLabel("Nome ou titulo deste pedido para localização futura: *", '#FF0000', '14px', null, '100%'),$descricaopedido],[new TLabel("Veículos, aeronaves e/ou equipamentos: *", '#FF0000', '14px', null, '100%'),$veiculos_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $tab_67410dd86de9d->addFields([new TLabel("Unidade / Departamento *", '#FF0000', '14px', null, '100%'),$departamento_unit_id],[new TLabel("Tipo de Manutenção *", '#FF0000', '14px', null, '100%'),$tipo_manutencao_id]);
        $row3->layout = ['col-sm-6','col-sm-6'];
        if (TSession::getValue('tipofrota')==2) {
            $row4 = $tab_67410dd86de9d->addFields([new TLabel("Horimetro Atual*. O Ultimo registro foi: {$kmcalculada}", '#FF0000', '14px', null, '100%'),$km,$kmultima],[new TLabel("Ciclos: ", null, '14px', null, '100%'),$ciclos],[new TLabel("Obs", '#FF0000', '14px', null, '100%'),$obs]);
            $row4->layout = ['col-sm-3', 'col-sm-3',' col-sm-6'];
        } else {
            $row4 = $tab_67410dd86de9d->addFields([new TLabel("KM/horimetro Atual*. O Ultimo registro foi: {$kmcalculada}", '#FF0000', '14px', null, '100%'),$km,$kmultima],[new TLabel("Obs", '#FF0000', '14px', null, '100%'),$obs]);
            $row4->layout = ['col-sm-6',' col-sm-6'];
        }
  
        
        if (TSession::getValue('pedido_base')==1) {
           $row40 = $tab_67410dd86de9d->addFields([new TLabel("Marcar como orçamento base: ", null, '14px', null, '100%'),$orcamento_base],[new TLabel("Selecione o orçamento base: ", null, '14px', null, '100%'),$orcamento_base_id]);
           $row40->layout = ['col-sm-6',' col-sm-6'];
        }
      
        // Linha com o campo + botão
        
        
        // $row5 = $tab_67410dd86de9d->addFields([new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        // $row5->layout = ['col-sm-12'];6
        if (TSession::getValue('utiliza_temparia')==1){
           // Botão Cadastrar
           $Cadastrar = new TButton('button_Cadastrar');
           $Cadastrar->setAction(new TAction(['PedidoFrotasForm', 'onCadastrarItemProduto']), "Cadastrar");
           $Cadastrar->addStyleClass('btn-default');
           $Cadastrar->setImage('fas:plus #69AA46');

           $row41 = $tab_67410dd86de9d->addFields([new TFormSeparator("Listagem dos itens do produto", '#333', '18', '#eee')]);
           $row41->layout = [' col-sm-12'];

           $row66 = $tab_67410dd86de9d->addFields([$Cadastrar]);
           $row66->layout = ['col-sm-10', 'col-sm-2']; // ajusta como quiser

           
           $row41 = $tab_67410dd86de9d->addFields([$pedido_frotas_id_produto]);
           $row41->layout = ['col-sm-12'];

           //servico
                      // Botão Cadastrar
           $Cadastrar1 = new TButton('button_Cadastrar1');
           $Cadastrar1->setAction(new TAction(['PedidoFrotasForm', 'onCadastrarItemServico']), "Cadastrar");
           $Cadastrar1->addStyleClass('btn-default');
           $Cadastrar1->setImage('fas:plus #69AA46');

           $row410 = $tab_67410dd86de9d->addFields([new TFormSeparator("Listagem dos itens do Servico", '#333', '18', '#eee')]);
           $row410->layout = [' col-sm-12'];

           $row660 = $tab_67410dd86de9d->addFields([$Cadastrar1]);
           $row660->layout = ['col-sm-10', 'col-sm-2']; // ajusta como quiser

           
           $row410 = $tab_67410dd86de9d->addFields([$pedido_frotas_id_servico]);
           $row410->layout = ['col-sm-12'];
         
        } else {
            $tab_6740ee3775766 = new BootstrapFormBuilder('tab_6740ee3775766');
            $this->tab_6740ee3775766 = $tab_6740ee3775766;
            $tab_6740ee3775766->setProperty('style', 'border:none; box-shadow:none;');

            $tab_6740ee3775766->appendPage("Produtos/Serviços");

            $tab_6740ee3775766->addFields([new THidden('current_tab_tab_6740ee3775766')]);
            $tab_6740ee3775766->setTabFunction("$('[name=current_tab_tab_6740ee3775766]').val($(this).attr('data-current_page'));");

            $row5 = $tab_6740ee3775766->addFields([$this->fieldList_67410e656dea3]);
            $row5->layout = [' col-sm-12'];

            $row6 = $tab_67410dd86de9d->addFields([$tab_6740ee3775766]);
            $row6->layout = [' col-sm-12'];
        }

      
        
        $tab_67410dd86de9d->appendPage("Documentos/Anexos");
        $row7 = $tab_67410dd86de9d->addFields([$this->fieldList_674111506deb6]);
        $row7->layout = [' col-sm-12'];
        

        $tab_67410dd86de9d->appendPage("Redes disponiveis");
        $row8 = $tab_67410dd86de9d->addFields([$redes1]);
        $row8->layout = [' col-sm-12'];

        $row9 = $this->form->addFields([$tab_67410dd86de9d]);
        $row9->layout = [' col-sm-12'];

        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar e Enviar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onshow = $this->form->addAction("Adicionar produtos/Serviços", new TAction(['ProdutoSimpleForm', 'onSetProject']), 'fas:shopping-basket #FF9800');
        $this->btn_onshow = $btn_onshow;



        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;
       

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['PedidoFrotasList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        //<onAfterPageCreation>

        //</onAfterPageCreation>

        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=PedidoFrotasForm]');
        $style->width = '80% !important';   
        $style->show(true);

    }

//<generated-FormAction-onSave>
    public function onSave($param = null) 
    {
        $retryAttempt = (int) ($param['__deadlock_retry_attempt'] ?? 0);
        $maxDeadlockRetries = 3;

        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $id = (int) TSession::getValue('pedido_frotas_id');

            $object = new PedidoFrotas($id); // create an empty object //</blockLine>

            $data = $this->form->getData(); // get form data as array
             $data->id = $id; 
            $object->fromArray( (array) $data); // load the object with data

            if ($object->km <= $data->kmultima)
            {
                throw new Exception("A KM atual não pode ser menor ou igual a última KM registrada {$data->kmultima}");
            }

            if (TSession::getValue('pedido_base')==1) {
                if ($object->orcamento_base_id > 0) // foi selecionado um orçamento base
                {
                    // Validação: o orçamento base precisa ser do mesmo veículo
                    $orcamento_base = new PedidoFrotas($object->orcamento_base_id);
                    if ($orcamento_base) {
                        if ($orcamento_base->veiculos_id != $object->veiculos_id) {
                            throw new Exception("O orçamento base selecionado não pertence ao veículo informado!");
                        }

                    }

                    // Validação: esse orçamento base já está vinculado a outro pedido?
                    $pedido_com_mesmo_base = PedidoFrotas::where('orcamento_base_id', '=', $object->orcamento_base_id)
                                                        ->where('id', '<>', $object->id) // evita erro ao editar o próprio pedido
                                                        ->first();

                    if ($pedido_com_mesmo_base) {
                        throw new Exception("Este orçamento base já foi utilizado por outro pedido de frota!");
                    }

                    // Validação: não pode marcar como base e também usar outro como base
                    if ($object->orcamento_base == 1) {
                        throw new Exception("O pedido está marcado como orçamento base, então não deve ter outro orçamento base vinculado.");
                    }
                }
                else // Nenhum orçamento base foi selecionado
                {
                    if ($object->orcamento_base != 1) {
                        // Se o pedido não for um orçamento base, precisa selecionar um
                        throw new Exception("Você deve selecionar um orçamento base para continuar!");
                    }

                    // Se for um orçamento base, tudo certo — não precisa vincular a outro
                }
            }

            //</beforeStoreAutoCode> //</blockLine> 
//<generatedAutoCode>

            $documentos_pedido_frotas_pedido_frotas_caminho_dir = 'app/anexos';
//</generatedAutoCode> 

            $pedidoNovo = false ; 

            if(!$data->id)
            {
                $pedidoNovo = true;
                $object->estado_pedido_frotas_id = EstadoPedido::PENDENTE;
                $object->system_users_id = TSession::getValue('userid');
                $object->system_unit_id = TSession::getValue('idunit');
                $object->entidade_id = TSession::getValue('entidade');
            }

            $data_limite_resposta = new DateTime($data->data_limite_resposta);
            $dt_pedido = new DateTime($data->dt_pedido);
            $hoje = new DateTime('today');

            if(!$data->id) {
                if ($data_limite_resposta < $hoje)
                {
                    throw new Exception("A data limite de resposta nao pode ser menor que hoje.");
                }

                if ($dt_pedido < $hoje)
                {
                    throw new Exception("A data do pedido nao pode ser menor que hoje.");
                }
            }

            $object->data_limite_resposta = $data_limite_resposta->format('Y-m-d H:i:s');
            $propostas = Propostas::where('pedido_frotas_id','=', $object->id)
                                  ->load();
            if ($propostas) {
                foreach ($propostas as $proposta) {
                    $proposta->data_limite_resposta	 = $data_limite_resposta->format('Y-m-d H:i:s');
                    if ($proposta->departamento_unit_id != $object->departamento_unit_id) {
                        $proposta->departamento_unit_id = $object->departamento_unit_id;
                    }
                    if ($proposta->veiculos_id != $object->veiculos_id) {
                        $proposta->veiculos_id = $object->veiculos_id;
                    }
                    if ($proposta->obs != $object->obs) {
                        $proposta->obs = $object->obs;
                    }
                    if ($proposta->km != $object->km) {
                        $proposta->km = $object->km;
                    }
                    $proposta->store();
                }
                
            }


            $object->mes = $dt_pedido->format('m');
            $object->ano = $dt_pedido->format('Y');
            $object->filtro_redes = json_encode($this->getFiltroRedesPayload(), JSON_UNESCAPED_UNICODE);

            $object->valor_total = 0;

            $object->store(); // save the object //</blockLine>

            $propostaveiculos = Propostas::where('pedido_frotas_id', '=', $object->id)
                                    ->load();
            if ($propostaveiculos) {
                foreach ($propostaveiculos as $prop) {
                    if ($prop->veiculos_id != $object->veiculos_id) {
                        $prop->veiculos_id = $object->veiculos_id;
                        $prop->store();
                    }
                    
                }
            }

            //</afterStoreAutoCode> //</blockLine>
 //<generatedAutoCode>



            $loadPageParam = []; 

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            

//</generatedAutoCode>

    //<fieldList-2290168-18514537> //</hideLine>
            $documentos_pedido_frotas_pedido_frotas_items = $this->storeItems('DocumentosPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_674111506deb6, function($masterObject, $detailObject){ //</blockLine>
                $detailObject->system_users_id = TSession::getValue('userid');
            }, $this->criteria_fieldList_674111506deb6); //</blockLine>
    //</hideLine> //</fieldList-2290168-18514537>
//<generatedAutoCode>
            if(!empty($documentos_pedido_frotas_pedido_frotas_items))
            {
                foreach ($documentos_pedido_frotas_pedido_frotas_items as $item)
                {
                    $dataFile = new stdClass();
                    $dataFile->caminho = $item->caminho;
                    $this->saveFile($item, $dataFile, 'caminho', $documentos_pedido_frotas_pedido_frotas_caminho_dir);
                }
            }

            if (TSession::getValue('checklist_vistoria_veiculo') == 1) {
                $this->gerarChecklistVeiculoDocumento($object);
            }

//</generatedAutoCode>

    //<fieldList-2233352-18072779> //</hideLine>
//<generatedAutoCode>
            $this->criteria_fieldList_67410e656dea3->setProperty('order', 'tipo asc');
//</generatedAutoCode>
           $mensagensGarantia = []; // Inicializa fora

           if (TSession::getValue('utiliza_temparia')!=1){

           $itens_pedido_frotas_pedido_frotas_items = $this->storeItems(
                'ItensPedidoFrotas',
                'pedido_frotas_id',
                $object,
                $this->fieldList_67410e656dea3,
                function($masterObject, $detailObject) use (&$mensagensGarantia) {
                    $tipo        = trim($detailObject->tipo ?? '');
                    $quantidade  = $detailObject->qtde ?? 0;
                    $produto_id  = $detailObject->produto_id ?? 0;

                    $preencheuAlgo = $tipo !== '' || $quantidade != 0 || $produto_id != 0;

                    if ($preencheuAlgo) {
                        $erros = [];

                        if ($tipo === '') {
                            $erros[] = 'O campo "Tipo" é obrigatório.';
                        }

                        if ((int) $quantidade === 0) {
                            $erros[] = 'O campo "Quantidade" é obrigatório e deve ser maior que zero.';
                        }

                        if ((int) $produto_id === 0) {
                            $erros[] = 'O campo "Produto/Serviço" é obrigatório.';
                        }

                        if (!empty($erros)) {
                            throw new Exception(implode("\n", $erros));
                        }

                        // Validação de garantia
                        $criteria = new TCriteria();
                        $criteria->add(new TFilter('veiculos_id', '=', $masterObject->veiculos_id));
                        $criteria->add(new TFilter('tipo', '=', $detailObject->tipo));
                        $criteria->add(new TFilter('produto_id', '=', $detailObject->produto_id));
                        $criteria->add(new TFilter('ativo', '=', 'S'));

                        $repo = new TRepository('ManutencaoGarantia');
                        $garantias = $repo->load($criteria);

                        $data_hoje = new DateTime();

                        foreach ($garantias as $garantia) {
                            $data_garantia = new DateTime($garantia->datagarantia);
                            $fim_garantia = (clone $data_garantia)->modify("+{$garantia->dias_garantia} days");

                            $dentro_data = $data_hoje <= $fim_garantia;
                            $dentro_km   = $masterObject->km !== null && $masterObject->km <= $garantia->km_manutencao;

                            if ($dentro_data || $dentro_km) {
                                $nome_tipo = ($detailObject->tipo == 1) ? 'Produto/peça' : 'Serviço';
                                $mensagensGarantia[] = "$nome_tipo '{$detailObject->produto->nome}' já está em garantia para o veículo selecionado.";
                            }
                        }
                    }
                },
                $this->criteria_fieldList_67410e656dea3
            );
            } else {

                $itenspedido = ItensPedidoFrotas::where('pedido_frotas_id','=',$object->id)
                                       ->load();
                if ($itenspedido) {
                    foreach ($itenspedido as $itenspff) {
                     // Validação de garantia
                        $criteria = new TCriteria();
                        $criteria->add(new TFilter('veiculos_id', '=', $object->veiculos_id));
                        $criteria->add(new TFilter('tipo', '=', $itenspff->tipo));
                        $criteria->add(new TFilter('produto_id', '=', $itenspff->produto_id));
                        $criteria->add(new TFilter('ativo', '=', 'S'));

                        $repo = new TRepository('ManutencaoGarantia');
                        $garantias = $repo->load($criteria);

                        $data_hoje = new DateTime();

                        foreach ($garantias as $garantia) {
                            $data_garantia = new DateTime($garantia->datagarantia);
                            $fim_garantia = (clone $data_garantia)->modify("+{$garantia->dias_garantia} days");

                            $dentro_data = $data_hoje <= $fim_garantia;
                            $dentro_km   = $object->km !== null && $object->km <= $garantia->km_manutencao;

                            if ($dentro_data || $dentro_km) {
                                $nome_tipo = ($itenspff->tipo == 1) ? 'Produto/peça' : 'Serviço';
                                $mensagensGarantia[] = "$nome_tipo '{$itenspff->produto->nome}' já está em garantia para o veículo selecionado.";
                            }
                        }
                    }
                }

            }

            // Fora do storeItems: lança exceção se houver mensagens de garantia
            if (!empty($mensagensGarantia)) {
        

                $aprovadores = []; // Inicializa o array de aprovadores
                if ($mensagensGarantia) {
                    $dep_id = (int) $object->departamento_unit_id;

                    $usuario = SystemUsers::where('system_unit_id', '=', TSession::getValue('idunit'))
                        ->where('id', 'in', "(SELECT sudu.system_users_id
                                            FROM system_user_departamento_unit sudu
                                            inner join system_users su on su.id = sudu.system_users_id
                                            WHERE sudu.departamento_unit_id = '.$dep_id.'
                                                AND su.notificarusuario = 1)");
                    if ($usuario){
                        foreach ($usuario as $user) {
                            
                            $aprovador_frotas = AprovadorFrotas::where('system_users_id', '=', $user->id)
                                                                ->load();
                            if ($aprovador_frotas) {
                                // Verifica se o aprovador está ativo e se o estado de pedido é aprovado
                                // e se o estado de pedido frotas aprovador está ativo
                                foreach ($aprovador_frotas as $aprovador_frotas) {
                                    $estado_pedido_frotas_aprovador = EstadoPedidoFrotasAprovador::where('aprovador_frotas_id', '=', $aprovador_frotas->id)
                                                                            ->where('estado_pedido_frotas_id', '=', EstadoPedido::APROVADO)
                                                                            ->first();
                                    if ($estado_pedido_frotas_aprovador) {
                                        //ENVIAR EMAIL PARA OS APROVADORES GUARDAR OS EMAILS EM UM ARRAY 
                                        $aprovadores[] = $user;
                                    }
                                }                        
                            } 

                        }
                    } 

                    if ($aprovadores) {
                        foreach ($aprovadores as $dadosAprovador) {
                            if ($dadosAprovador) {
                                $emailTemplate = new EmailTemplate(EmailTemplate::GARANTIA);
                                $veiculos = new Veiculos($object->veiculos_id);
                                $identificacaoveiculo = $veiculos->placa. ' - ' . $veiculos->marca->descricao . ' - ' . $veiculos->modelo->descricao;   

                                $titulo = $emailTemplate->titulo;
                                $mensagem = $emailTemplate->mensagem;
                                $usr = new SystemUsers(TSession::getValue('userid'));

                                $mensagem = str_replace('{nome_usuario}', $usr->name, $mensagem);
                                $mensagem = str_replace('{nome_gestor}', $dadosAprovador->name, $mensagem);
                                $mensagem = str_replace('{nome_aprovador}', $dadosAprovador->name, $mensagem);
                                $mensagem = str_replace('{data_hora_aprovacao}', date('d/m/Y H:i'), $mensagem);
                                $mensagem = str_replace('{identificacao_veiculo}', $identificacaoveiculo, $mensagem);
                                $mensagem = str_replace('{motivo}', $object->descricao ?? '', $mensagem);

                                $titulo = $object->render($titulo);
                                $mensagem = $object->render($mensagem);

                          //      MailService::send($dadosAprovador->email, $titulo, $mensagem, 'html');
                            }
                        }
                    }
                   
                }
            }
            if (!empty($mensagensGarantia)) {
                throw new Exception(implode("\n", $mensagensGarantia));
            }

              
            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; //</blockLine>

            $itenspedido = ItensPedidoFrotas::where('pedido_frotas_id','=',$object->id)
                                       ->load();
             $somatotal=0;
             if ($itenspedido){
                 foreach ($itenspedido as $itensp){
                     $itensp->valor_total = $itensp->valor * $itensp->quantidade;
                     $itensp->store();
                     $somatotal += ($itensp->valor * $itensp->quantidade);
                      // Mapeia os itens antigos por ID
                 } 
             }          
             $pes1 = Pessoa::where('system_user_id','=',TSession::getValue('userid'))
                                  ->where('id','not in', '(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '.GrupoPessoa::CONDUTOR.')')
             ->load();
             if ($pes1) {
                 foreach ($pes1 as $pessoass) {

                 }
                 $object->estabelecimento_id = $pessoass->id;
             }
             $object->estado_pedido_frotas1_id=null;
             $object->valor_total = $somatotal;
             $object->store();

               // Carrega todas as relações do pedido atual
                $relacoesAntigas = PedidoAsCliente::where('pedido_frotas_id', '=', $data->id)->load();

                foreach ($relacoesAntigas as $relacao) {
                    
                        // Verifica se existe proposta ativa
                        $criteria = new TCriteria();
                        //  $criteria->add(new TFilter('ativo', '=', 'Sim'));
                        $criteria->add(new TFilter('pedido_frotas_id', '=', $data->id));
                        $criteria->add(new TFilter('pessoa_id', '=', $relacao->pessoa_id));
                        // $criteria->add(new TFilter('deleted_at', 'IS', NULL));

                        $repo = new TRepository('Propostas');
                        $propostasAtivas = $repo->load($criteria);

                        // Se NÃO existir proposta ativa, remove a relação
                        if (!$propostasAtivas) {
                            $relacao->delete();
                        }
                }
              if ($pes1) {
                 $relacao = new PedidoAsCliente();
                 $relacao->pedido_frotas_id = $data->id;
                 $relacao->pessoa_id = $pes1[0]->id;
                 $relacao->store();
             } else {

             
             if (TSession::getValue('selecao_redes_aleatoria')==2) {
 
              
                // Agora salva as redes selecionadas novamente
                $redesSelecionadas = TSession::getValue('ViewRedesdisponiveisListbuilder_datagrid_check');
                
                if ($redesSelecionadas && is_array($redesSelecionadas)) {
                    foreach ($redesSelecionadas as $rede_id) {
                        if (is_numeric($rede_id)) {
                            // Evita duplicar
                            $existe = PedidoAsCliente::where('pedido_frotas_id', '=', $data->id)
                                                    ->where('pessoa_id', '=', $rede_id)
                                                    ->first();
                
                            if (!$existe) {
                                $relacao = new PedidoAsCliente();
                                $relacao->pedido_frotas_id = $data->id;
                                $relacao->pessoa_id = $rede_id;
                                $relacao->store();
                            }
                        }
                    }
                }
            } else {
                $idcidade = TSession::getValue('filtrocidade_id');
                $idseguimento = TSession::getValue('filtroseguimento_id');
                $idgrupopessoa = GrupoPessoa::CONDUTOR;

                if (empty($idcidade) && empty($idseguimento)) {
                    throw new Exception('Informe pelo menos um filtro de cidade ou segmento antes de salvar o pedido.');
                }

                // Verifica se existe proposta ativa
                $criteriapessoa = new TCriteria();

                if ($idcidade) {
                    // Se for array, transforma em string
                    if (is_array($idcidade)) {
                        $idcidade = implode(',', $idcidade);
                    }

                    $subqueryCidade = "(SELECT pessoa_id FROM pessoa_endereco pe WHERE pe.cidade_id IN ({$idcidade}))";
                    $criteriapessoa->add(new TFilter('id', 'IN', $subqueryCidade));
                }

                if ($idseguimento) {
                    if (is_array($idseguimento)) {
                        $idseguimento = implode(',', array_filter(array_map('intval', $idseguimento)));
                    } else {
                        $idseguimento = implode(',', array_filter(array_map('intval', preg_split('/\s*,\s*/', (string) $idseguimento))));
                    }

                    if (!empty($idseguimento)) {
                        $subquerySeguimento = "(SELECT pessoa_id FROM seguimento_pessoa se WHERE se.seguimento_id IN ({$idseguimento}))";
                        $criteriapessoa->add(new TFilter('id', 'IN', $subquerySeguimento));
                    }
                }

                $subquerySeguimento = "(SELECT pessoa_id FROM pessoa_grupo pg WHERE pg.grupo_pessoa_id = {$idgrupopessoa})";
                $criteriapessoa->add(new TFilter('id', 'NOT IN', $subquerySeguimento));

                $repopessoa = new TRepository('Pessoa');
                $pessoasfiltradas = $repopessoa->load($criteriapessoa);

                if ($pessoasfiltradas) {
                    foreach ($pessoasfiltradas as $pessoa) {
                        // Evita duplicar
                        $existe = PedidoAsCliente::where('pedido_frotas_id', '=', $data->id)
                                                ->where('pessoa_id', '=', $pessoa->id)
                                                ->first();

                        if (!$existe) {
                            $relacao = new PedidoAsCliente();
                            $relacao->pedido_frotas_id = $data->id;
                            $relacao->pessoa_id = $pessoa->id;
                            $relacao->store();
                        }
                    }
                }

            } 
        }

            if ($pedidoNovo)
            {
                $this->registrarHistoricoPedidoFrotasPendente($object);

            } else {
                   $loadPageParam["pedido_frotas_id"] = $object->id;
                   $loadPageParam["inserido"] = true;
              
            }


            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

             if($pedidoNovo)
            {
             
            }

            //</messageAutoCode> //</blockLine>
            //<generatedAutoCode>
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('PedidoFrotasList', 'onShow', $loadPageParam);
            //</generatedAutoCode>

            //</endTryAutoCode> //</blockLine>
            //<generatedAutoCode>
            TScript::create("Template.closeRightPanel();");
            TForm::sendData(self::$formName, (object)['id' => $object->id]);

//</generatedAutoCode>

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> //</blockLine>
            if (TTransaction::get())
            {
                TTransaction::rollback(); // undo all pending operations
            }

            if ($this->isDeadlockException($e) && $retryAttempt < $maxDeadlockRetries)
            {
                $param['__deadlock_retry_attempt'] = $retryAttempt + 1;
                usleep(random_int(120000, 320000));
                $this->onSave($param);
                return;
            }

            new TMessage('error', $this->getFriendlySaveErrorMessage($e)); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data

            
        }
    }
  
   //<generated-onEdit>
    public function onEdit( $param )//</ini>
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new PedidoFrotas($key); // instantiates the Active Record //</blockLine>

                //</beforeSetDataAutoCode> //</blockLine>
//<generatedAutoCode>
            //     $this->redes1->unhide();
            //     $this->pedido_frotas_id_produto->unhide();
            // //    $this->pedido_frotas_id_produto->setParameter('id', $object->id);
            //     $this->pedido_frotas_id_servico->unhide();
            //  //   $this->pedido_frotas_id_servico->setParameter('id', $object->id);
//</generatedAutoCode>
                TSession::setValue('pedido_frotas_id',null);
                TSession::setValue('pedido_frotas_id',$object->id);
                TSession::setValue('pedido_frotas_form_data',null);
                    TSession::setValue('pedido_frotas_form_data',$object);
                $this->restaurarFiltroRedes($object);

               
    //<fieldList-2290168-18514537> //</hideLine>
                $this->fieldList_674111506deb6_items = $this->loadItems('DocumentosPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_674111506deb6, function($masterObject, $detailObject, $objectItems){ //</blockLine>

                    //code here

                    //</autoCode>
                }, $this->criteria_fieldList_674111506deb6); //</blockLine>
    //</hideLine> //</fieldList-2290168-18514537>

    //<fieldList-2233352-18072779> //</hideLine>
                $old_items = [];

                $this->fieldList_67410e656dea3_items = $this->loadItems(
                    'ItensPedidoFrotas',
                    'pedido_frotas_id',
                    $object,
                    $this->fieldList_67410e656dea3,
                    function($masterObject, $detailObject, $objectItems) use (&$old_items) {
                        $old_items[] = $detailObject;
                    },
                    $this->criteria_fieldList_67410e656dea3
                );

                // Salva todos os itens antigos na sessão
                TSession::setValue('old_items', $old_items);
    //</hideLine> //</fieldList-2233352-18072779>

                $this->form->setData($object); // fill the form //</blockLine>


                if($this->fieldList_67410e656dea3_items)
                {
                    $fieldListData = new stdClass();
                     $fieldListData->itens_pedido_frotas_pedido_frotas_produto_familia_produto_id = [];
                    $fieldListData->itens_pedido_frotas_pedido_frotas_produto_id = [];

                    foreach ($this->fieldList_67410e656dea3_items as $item) 
                    {
                        if(isset($item->produto->familia_produto_id))
                        {
                            $value = $item->produto->familia_produto_id;

                            $fieldListData->itens_pedido_frotas_pedido_frotas_produto_familia_produto_id[] = $value;
                        }
                        if(isset($item->produto_id))
                        {
                            $value = $item->produto_id;

                            $fieldListData->itens_pedido_frotas_pedido_frotas_produto_id[] = $value;
                        }
                    }

                    TScript::create('tjquerydialog_block_ui(); tform_events_stop( function() {tjquerydialog_unblock_ui()} );');

                    TForm::sendData(self::$formName, $fieldListData);
                }

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

        $this->fieldList_67410e656dea3->addHeader();
        $this->fieldList_67410e656dea3->addDetail($this->default_item_fieldList_67410e656dea3);

        $this->fieldList_67410e656dea3->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        $this->fieldList_674111506deb6->addHeader();
        $this->fieldList_674111506deb6->addDetail($this->default_item_fieldList_674111506deb6);

        $this->fieldList_674111506deb6->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

        //<onFormClear>

        // $this->redes1->unhide();
        // $this->redes1->setAction(new TAction(['ViewRedesdisponiveisList', 'onShow']));

          TSession::setValue('pedido_frotas_id', NULL);
          TSession::setValue('filtrocidade_id', NULL);
          TSession::setValue('filtroseguimento_id', NULL);
          TSession::setValue('filtroseguimento_descricao', NULL);
          TSession::setValue('ViewRedesdisponiveisList_filter_data', NULL);
          TSession::setValue('ViewRedesdisponiveisList_filters', NULL);
          TSession::setValue('ViewRedesdisponiveisList_show_all_after_search', 0);
          TSession::setValue('ViewRedesdisponiveisListbuilder_datagrid_check', []);
        //</onFormClear>

    } 

    public function onShow($param = null)
    {
        $this->fieldList_67410e656dea3->addHeader();
        $this->fieldList_67410e656dea3->addDetail($this->default_item_fieldList_67410e656dea3);
    
        $this->fieldList_67410e656dea3->addCloneAction(null, 'fas:plus #69aa46', "Clonar");
    
        $this->fieldList_674111506deb6->addHeader();
        $this->fieldList_674111506deb6->addDetail($this->default_item_fieldList_674111506deb6);
    
        $this->fieldList_674111506deb6->addCloneAction(null, 'fas:plus #69aa46', "Clonar");
    
        //<onShow>
    
//         $this->redes1->unhide();
//      //   $this->redes1->setAction(new TAction(['ViewRedesdisponiveisList', 'onSetProject']));

         
//         $this->pedido_frotas_id_produto->unhide();
//  //       $this->pedido_frotas_id_produto->setAction(new TAction(['ItensPedidoFrotasServicoList', 'onShow']));

//         $this->pedido_frotas_id_servico->unhide();
//    //     $this->pedido_frotas_id_servico->setAction(new TAction(['ItensPedidoFrotasProdutoList', 'onShow']));


    
    
        TSession::setValue('pedido_frotas_id', NULL);
        TSession::setValue('filtrocidade_id', NULL);
        TSession::setValue('filtroseguimento_id', NULL);
        TSession::setValue('filtroseguimento_descricao', NULL);
        TSession::setValue('ViewRedesdisponiveisList_filter_data', NULL);
        TSession::setValue('ViewRedesdisponiveisList_filters', NULL);
        TSession::setValue('ViewRedesdisponiveisList_show_all_after_search', 0);
        TSession::setValue('ViewRedesdisponiveisListbuilder_datagrid_check', []);
        TSession::setValue('editando',null);
    
        //</onShow>
    }
    
    

    public static function getFormName()
    {
        return self::$formName;
    }

    private function getFiltroRedesPayload()
    {
        $cidadeId = TSession::getValue('filtrocidade_id');
        $seguimentoId = TSession::getValue('filtroseguimento_id');
        $seguimentoDescricao = TSession::getValue('filtroseguimento_descricao');

        return [
            'selecao_redes_aleatoria' => (int) TSession::getValue('selecao_redes_aleatoria'),
            'cidade_id' => $cidadeId ?: null,
            'seguimento_id' => $seguimentoId ?: null,
            'seguimento_descricao' => $seguimentoDescricao ?: null,
        ];
    }

    private function restaurarFiltroRedes(PedidoFrotas $object)
    {
        $filtroRedes = json_decode((string) $object->filtro_redes, true);

        if (!is_array($filtroRedes)) {
            TSession::setValue('filtrocidade_id', NULL);
            TSession::setValue('filtroseguimento_id', NULL);
            TSession::setValue('filtroseguimento_descricao', NULL);
            TSession::setValue('ViewRedesdisponiveisList_filter_data', NULL);
            TSession::setValue('ViewRedesdisponiveisList_filters', NULL);
            TSession::setValue('ViewRedesdisponiveisList_show_all_after_search', 0);
            return;
        }

        $cidadeId = $filtroRedes['cidade_id'] ?? null;
        $seguimentoId = $filtroRedes['seguimento_id'] ?? null;
        $seguimentoDescricao = $filtroRedes['seguimento_descricao'] ?? null;

        TSession::setValue('filtrocidade_id', $cidadeId);
        TSession::setValue('filtroseguimento_id', $seguimentoId);
        TSession::setValue('filtroseguimento_descricao', $seguimentoDescricao);

        $filterData = new stdClass();
        $filterData->cidade_id = $cidadeId;
        $filterData->seguimento_id = $seguimentoId;
        $filterData->seguimento_descricao = $seguimentoDescricao;
        TSession::setValue('ViewRedesdisponiveisList_filter_data', $filterData);

        $filters = [];

        if (!empty($cidadeId)) {
            $filters[] = new TFilter('cidade_id', 'in', $cidadeId);
        }

        if (!empty($seguimentoId)) {
            $criteria = new TCriteria();
            $criteria->add(new TFilter('seguimento_id', 'in', is_array($seguimentoId) ? $seguimentoId : [$seguimentoId]));

            $repository = new TRepository('SeguimentoPessoa');
            $registros = $repository->load($criteria);

            $ids = [];
            if ($registros) {
                foreach ($registros as $item) {
                    $ids[] = $item->pessoa_id;
                }
            }

            if (!empty($ids)) {
                $filters[] = new TFilter('id', 'IN', $ids);
            } else {
                $filters[] = new TFilter('id', '=', 0);
            }
        }

        TSession::setValue('ViewRedesdisponiveisList_filters', $filters);
        TSession::setValue('ViewRedesdisponiveisList_show_all_after_search', !empty($filters) ? 1 : 0);
    }

    private function isDeadlockException(Exception $e): bool
    {
        $message = $e->getMessage();

        return stripos($message, 'SQLSTATE[40001]') !== false
            || stripos($message, '1213') !== false
            || stripos($message, 'deadlock') !== false;
    }

    private function getFriendlySaveErrorMessage(Exception $e): string
    {
        $message = $e->getMessage();
        $isUniqueViolation = stripos($message, 'SQLSTATE[23000]') !== false
            && stripos($message, 'CHK_UNIQUE') !== false;

        if ($isUniqueViolation)
        {
            return 'Ja existe item com o mesmo produto para este pedido de venda. Revise os itens para nao duplicar produto.';
        }

        return $message;
    }

    //</hideLine> <addUserFunctionsCode/>

    //<userCustomFunctions>

    private function gerarChecklistVeiculoDocumento(PedidoFrotas $pedido)
    {
        if (empty($pedido->veiculos_id) || empty($pedido->id)) {
            return;
        }

        $veiculo = new Veiculos($pedido->veiculos_id);

        if (!$veiculo || empty($veiculo->id)) {
            return;
        }

        $diretorio = 'app/anexos/' . $pedido->id;
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0777, true);
        }

        $arquivo = $diretorio . '/checklist_veiculo_pedido_' . $pedido->id . '.pdf';

        $html = $this->montarHtmlChecklistVeiculo($pedido, $veiculo);
        $this->gerarPdfChecklistVeiculo($html, $arquivo);
        $this->salvarDocumentoChecklistPedido($pedido->id, $arquivo);
    }

    private function montarHtmlChecklistVeiculo(PedidoFrotas $pedido, Veiculos $veiculo): string
    {
        $logoDataUri = $this->getChecklistLogoDataUri();

        $marca = !empty($veiculo->marca_id) ? ($veiculo->marca->descricao ?? '') : '';
        $modelo = !empty($veiculo->modelo_id) ? ($veiculo->modelo->descricao ?? '') : '';
        $departamento = !empty($pedido->departamento_unit_id) ? ($pedido->departamento_unit->name ?? '') : '';
        $unidade = !empty($veiculo->system_unit_id) ? ($veiculo->system_unit->name ?? '') : '';
        $responsavel = !empty($veiculo->responsavel_id) ? ($veiculo->responsavel->nome ?? '') : '';
        $tipoManutencao = !empty($pedido->tipo_manutencao_id) ? ($pedido->tipo_manutencao->descricao ?? '') : '';

        $dadosVeiculo = [
            'Pedido' => $pedido->id,
            'Data do pedido' => $this->formatChecklistDate($pedido->dt_pedido ?? null),
            'Placa' => $veiculo->placa ?? '',
            'Prefixo' => $veiculo->prefixo ?? '',
            'Marca / Modelo' => trim($marca . ' / ' . $modelo, ' /'),
            'Ano' => trim(($veiculo->anom ?? '') . '/' . ($veiculo->anof ?? ''), '/'),
            'KM informado' => $pedido->km ?? '',
            'Chassi' => $veiculo->chassi ?? '',
            'Renavam' => $veiculo->renavam ?? '',
            'Unidade' => $unidade,
            'Departamento' => $departamento,
            'Tipo manutencao' => $tipoManutencao,
        ];

        $checklistItens = [
            'Lataria e para-choques sem avarias aparentes',
            'Pintura e acabamento externo em boas condicoes',
            'Faros, lanternas e setas funcionando',
            'Retrovisores e vidros em boas condicoes',
            'Limpadores e esguichos funcionando',
            'Pneus em bom estado de conservacao',
            'Estepe, macaco e chave de roda disponiveis',
            'Documentacao do veiculo disponivel',
            'Painel e indicadores funcionando',
            'Bancos, cintos e acabamento interno preservados',
            'Nivel de combustivel conferido',
            'Observacoes gerais registradas no pedido',
        ];

        $linhasDados = '';
        foreach (array_chunk($dadosVeiculo, 3, true) as $linha) {
            $linhasDados .= '<tr>';
            foreach ($linha as $rotulo => $valor) {
                $linhasDados .= sprintf(
                    '<td><div class="label">%s</div><div class="value">%s</div></td>',
                    htmlspecialchars((string) $rotulo, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars((string) ($valor ?: '-'), ENT_QUOTES, 'UTF-8')
                );
            }

            $faltantes = 3 - count($linha);
            for ($i = 0; $i < $faltantes; $i++) {
                $linhasDados .= '<td></td>';
            }

            $linhasDados .= '</tr>';
        }

        $linhasChecklist = '';
        foreach ($checklistItens as $indice => $item) {
            $numero = $indice + 1;
            $linhasChecklist .= sprintf(
                '<tr><td class="item-num">%d</td><td class="item-desc">%s</td><td class="item-status"></td><td class="item-status"></td><td class="item-obs"></td></tr>',
                $numero,
                htmlspecialchars($item, ENT_QUOTES, 'UTF-8')
            );
        }

        $observacoes = htmlspecialchars((string) ($pedido->obs ?: ''), ENT_QUOTES, 'UTF-8');

        $urlAtual = htmlspecialchars($this->getChecklistCurrentUrl(), ENT_QUOTES, 'UTF-8');
        $geradoEm = $this->formatChecklistDate(date('Y-m-d H:i:s'));

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px 28px 34px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        .header { width: 100%; border-bottom: 2px solid #0f4c81; padding-bottom: 6px; margin-bottom: 10px; }
        .header-table, .dados-table, .checklist-table, .assinaturas-table { width: 100%; border-collapse: collapse; }
        .logo { width: 92px; }
        .titulo { text-align: right; }
        .titulo h1 { margin: 0; font-size: 16px; color: #0f4c81; }
        .titulo p { margin: 2px 0 0 0; font-size: 10px; color: #4b5563; }
        .section-title { background: #0f4c81; color: #fff; font-weight: bold; padding: 7px 10px; margin: 14px 0 0 0; }
        .dados-table td { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; width: 33.33%; }
        .label { font-size: 9px; text-transform: uppercase; color: #6b7280; margin-bottom: 4px; }
        .value { font-size: 11px; font-weight: bold; min-height: 14px; }
        .checklist-table th, .checklist-table td { border: 1px solid #cbd5e1; padding: 4px 6px; }
        .checklist-table th { background: #eaf2f9; color: #0f172a; font-size: 10px; }
        .item-num { width: 28px; text-align: center; }
        .item-desc { width: 58%; }
        .item-status { width: 55px; height: 16px; }
        .item-obs { height: 16px; }
        .observacoes { border: 1px solid #d1d5db; min-height: 72px; padding: 10px; }
        .assinaturas-table td { width: 50%; padding-top: 40px; text-align: center; }
        .assinatura-linha { border-top: 1px solid #374151; margin: 0 16px 6px 16px; }
        .rodape { margin-top: 18px; font-size: 9px; color: #6b7280; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 40%;">
                    <img class="logo" src="{$logoDataUri}" alt="NP3">
                </td>
                <td class="titulo" style="width: 60%;">
                    <h1>Checklist de Veiculo</h1>
                    <p>Documento gerado automaticamente no cadastro do pedido de frotas</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Dados do Veiculo</div>
    <table class="dados-table">
        {$linhasDados}
    </table>

    <div class="section-title">Itens do Checklist</div>
    <table class="checklist-table">
        <thead>
            <tr>
                <th>N</th>
                <th>Item verificado</th>
                <th>OK</th>
                <th>NC</th>
                <th>Observacoes</th>
            </tr>
        </thead>
        <tbody>
            {$linhasChecklist}
        </tbody>
    </table>

    <div class="section-title">Observacoes do Pedido</div>
    <div class="observacoes">{$observacoes}</div>

    <div class="section-title">Assinaturas</div>
    <table class="assinaturas-table">
        <tr>
            <td>
                <div class="assinatura-linha"></div>
                Responsavel pela conferencia
            </td>
            <td>
                <div class="assinatura-linha"></div>
                Condutor / Responsavel pelo veiculo
            </td>
        </tr>
    </table>

    <div class="rodape">
        Gerado em {$geradoEm} | URL: {$urlAtual}
    </div>
</body>
</html>
HTML;
    }

    private function gerarPdfChecklistVeiculo(string $html, string $arquivo): void
    {
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        file_put_contents($arquivo, $dompdf->output());
    }

    private function salvarDocumentoChecklistPedido(int $pedidoId, string $arquivo): void
    {
        $documento = DocumentosPedidoFrotas::where('pedido_frotas_id', '=', $pedidoId)
            ->where('caminho', '=', $arquivo)
            ->first();

        if (!$documento) {
            $documento = new DocumentosPedidoFrotas();
            $documento->pedido_frotas_id = $pedidoId;
        }

        $documento->caminho = $arquivo;
        $documento->store();
    }

    private function getChecklistLogoDataUri(): string
    {
        $logos = [
            'app/images/logo.png',
            'app/images/gestaoxp3/logo.png',
            'app/images/builderLayoutLogo.png',
        ];

        foreach ($logos as $logo) {
            if (is_file($logo)) {
                $tipo = pathinfo($logo, PATHINFO_EXTENSION);
                $conteudo = base64_encode(file_get_contents($logo));
                return 'data:image/' . $tipo . ';base64,' . $conteudo;
            }
        }

        return '';
    }

    private function formatChecklistDate($value): string
    {
        if (empty($value)) {
            return '-';
        }

        try {
            return (new DateTime($value))->format('d/m/Y H:i');
        } catch (Exception $e) {
            return (string) $value;
        }
    }

    private function getChecklistCurrentUrl(): string
    {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        return $scheme . '://' . $host . $uri;
    }

    //</userCustomFunctions>

  public static function onExitVeiculo($param)
    {
        try {
            // $session_pedido = TSession::getValue('pedido_frotas_form_data');

            // if ($session_pedido && $session_pedido->veiculos_id <> $param['veiculos_id']) {
                TTransaction::open(self::$database);
                $suiv_url = 'https://api.suiv.com.br/api/v4';

                $km = null;
                $veiculo = new Veiculos($param['veiculos_id']);

                // Tenta buscar a última KM do pedido de frotas
                $ultimoPedido = PedidoFrotas::where('veiculos_id', '=', $param['veiculos_id'])
                                    ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::FINALIZADO)
                                    ->orderBy('id', 'desc')
                                    ->first();

                if ($ultimoPedido && $ultimoPedido->km) {
                    $km = $ultimoPedido->km;
                } else {
                    // Se não tiver pedido com km, pega da tabela veiculos
                    if ($veiculo && $veiculo->hodometroatual) {
                        $km = $veiculo->hodometroatual;
                    }
                }

                $datax = new stdClass();
                $datax->km = $km+1;
                $datax->kmcalculada = $km;
                $datax->kmultima = $km;
                
               
          
                if (TSession::getValue('utiliza_temparia')==1) {
                    $token = null;
                    $grupopecas = [];

                    $vehicletoken = Vehicletoken::where('veiculos_id', '=', $param['veiculos_id'])
                                                ->first();
                    if ($vehicletoken && !empty($vehicletoken->token)) {
                        $token = $vehicletoken->token;
                    } else {
                        try {
                            $token = SuivClient::getVehicleTokenByPlate($veiculo->placa);

                            if (!empty($token)) {
                                $newVehicletoken = new Vehicletoken();
                                $newVehicletoken->veiculos_id = $param['veiculos_id'];
                                $newVehicletoken->token = $token;
                                $newVehicletoken->store();
                            }
                        } catch (\Exception $e) {
                            // Falhas esperadas da SUIV seguem com dados locais sem bloquear o formulario.
                            if (!SuivClient::shouldUseLocalFallback($e)) {
                                throw $e;
                            }
                        }
                    }

                    TSession::setValue('familiaIDS',null);
                    TSession::setValue('grupo_pecas_suiv',null);

                    if (!empty($token)) {
                        try {
                            $grupopecas = SuivClient::getSets($token);
                        } catch (\Exception $e) {
                            // Token invalido, sem saldo ou SUIV indisponivel: continua sem sincronizar.
                            if (!SuivClient::shouldUseLocalFallback($e)) {
                                throw $e;
                            }
                        }
                    }

                    if (!empty($grupopecas) && is_iterable($grupopecas)) {

                        foreach ($grupopecas as $set) {

                           
                            $id   = is_array($set) ? ($set['id'] ?? null)         : ($set->id ?? null);
                            $desc = is_array($set) ? ($set['description'] ?? null): ($set->description ?? null);

                           
                            TSession::setValue('grupo_pecas_suiv',$id);
                            if ($desc) {

                                $familia_produto = FamiliaProduto::where('nome', '=', $desc)->first();
 
                                if (!$familia_produto) {

                                    $familia_produto = new FamiliaProduto;
                                    $familia_produto->nome   = $desc;
                                    $familia_produto->suiv_id = $id;
                                    $familia_produto->store();

                                } else {
                                        $familia_produto->nome   = $desc;
                                        $familia_produto->suiv_id = $id;
                                        $familia_produto->store();
                                }
                                $familiaIDS = TSession::getValue('familiaIDS');
                                $familiaIDS[] = $familia_produto->id;
                                TSession::setValue('familiaIDS',$familiaIDS);

                            }
                            // //inserir na tabela produto
                            // $nicks = SuivClient::getNicknames($token, $set['id']) ;
                            // new TMessage('info', 'Identificação de peças do veículo obtido do SUIV.');

                            // $nickss = [];
                            // if ($nicks) {
                            //     foreach($nicks as $nicksitem){
                            //         $conteudojson = $nicksitem;
                            //         $idnick_json = json_encode($conteudojson, JSON_UNESCAPED_UNICODE);
                            //         // Decodifica de volta em array associativo
                            //         $dados = json_decode($idnick_json, true);
                            //         // Pega o valor do campo 'id'
                            //         $idnick = $dados['id'] ?? null;
                            //         $descnick = $dados['description'] ?? null;
                                                                        
                            //         $idnick = isset($idnick) ? (int)$idnick : 0;
                            //         $nickss[] = $idnick;

                            //         $parts = SuivClient::getParts($token, (int) $idnick);

                            //         if (!empty($parts) && is_iterable($parts)) {
                            //             foreach ($parts as $p) {
                            //                 // Aceita tanto array quanto objeto
                            //                 $idparts         = is_array($p) ? ($p['id'] ?? null)           : ($p->id ?? null);
                            //                 $descriptionparts= is_array($p) ? ($p['description'] ?? null)  : ($p->description ?? null);
                            //                 $partnumberparts = is_array($p) ? ($p['partNumber'] ?? null)   : ($p->partNumber ?? null);
                            //                 $priceparts      = is_array($p) ? ($p['price'] ?? 0)           : ($p->price ?? 0);

                            //                 if (!$idparts) {
                            //                     continue; // pula registros inválidos
                            //                 }

                            //                 // Verifica se já existe produto vinculado a esta peça
                            //                 $produtosuiv = Produto::where('suiv_peca_id', '=', $idparts)->first();

                            //                 if (!$produtosuiv) {
                            //                     $produtosuiv = new Produto();
                            //                     $produtosuiv->nome              = $descriptionparts ?: 'Peça S/Descrição';
                            //                     $produtosuiv->familia_produto_id= $familia_produto->id;
                            //                     $produtosuiv->preco_venda       = (float) $priceparts;

                            //                     $produtosuiv->suiv_grupo_id     = $set['id'];
                            //                     $produtosuiv->suiv_peca_id      = $idparts;
                            //                     $produtosuiv->suiv_nickname_id  = $idnick;
                            //                     $produtosuiv->suiv_partnumber   = $partnumberparts;
                            //                     $produtosuiv->suiv_preco_peca   = (float) $priceparts;

                            //                     $produtosuiv->tipo_produto_id   = (int) TSession::getValue('tipo');
                            //                     $produtosuiv->ativo             = 'T';
                            //                     $produtosuiv->system_unit_id    = TSession::getValue('idunit');
                            //                     $produtosuiv->store();
                            //                 }
                            //                 //de acordo com a peça do suiv
                            //                 //inserir os serviços padroes do suiv

                            //             }
                            //         }


                            //     } 

                            // } else {
                            //         foreach($nicks as $nicksitem){
                            //             $conteudojson = $nicksitem;
                            //             $idnick_json = json_encode($conteudojson, JSON_UNESCAPED_UNICODE);
                            //             // Decodifica de volta em array associativo
                            //             $dados = json_decode($idnick_json, true);
                            //             // Pega o valor do campo 'id'
                            //             $idnick = $dados['id'] ?? null;
                            //             $descnick = $dados['description'] ?? null;
                                                                            
                            //             $idnick = isset($idnick) ? (int)$idnick : 0;
                            //             $nickss[] = $idnick;

                            //         }
                            // }                           
                        
                        }
                    }
                }
              
                TForm::sendData(self::$formName, $datax);

                TTransaction::close();
           
        } catch (Exception $e) {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }

            if (!SuivClient::shouldUseLocalFallback($e)) {
                new TMessage('error', $e->getMessage());
            }
        }
    }
    
  

    public static function onExitTipo($param)
    {
        try {
            $conteudojson = $param['_field_data_json'];
            $idproduto = json_decode($conteudojson);
            if (isset($idproduto->{'row'})) {
               $idproduto1 = $idproduto->{'row'}; // 1234
                TSession::setValue('tipo', null);
                TSession::setValue('tipo', $param['itens_pedido_frotas_pedido_frotas_tipo'][$idproduto1]);
            }

             // Atualiza sessão
            // Se utilizar temparia
            if (TSession::getValue('utiliza_temparia') == 1) {

                $field_id = explode('_', $param['_field_id']);
                $field_id = end($field_id);

                if (!empty($param['key'])) { 

                    $criteria = new TCriteria();
                    $criteria->add(new TFilter('suiv_id', 'is not ', null)); 
                    $criteria->add(new TFilter('suiv_id', '<>', -1)); 


                    TDBCombo::reloadFromModel(
                        self::$formName,
                        'itens_pedido_frotas_pedido_frotas_produto_familia_produto_id_' . $field_id,
                        self::$database,
                        'FamiliaProduto',
                        'id',
                        '{nome}',
                        'nome asc',
                        $criteria,
                        true
                    ); 
                } else { 
                    TCombo::clearField(
                        self::$formName,
                        'itens_pedido_frotas_pedido_frotas_produto_familia_produto_id_' . $field_id
                    ); 
                }  
            }

        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    private function registrarHistoricoPedidoFrotasPendente($pedido)
    {

        $hist = new PedidoFrotasHistorico();
        $hist->pedido_frotas_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d H:i:s');
        $hist->estado_pedido_frotas_id = EstadoPedidoFrotas::PENDENTE; 
           $aprovador = AprovadorFrotas::where('system_users_id','=',TSession::getValue('userid'))->load();
        if ($aprovador) {
            $hist->aprovador_frotas_id = $aprovador[0]->id;
        }
        $hist->store();

    }
    
    public static function notificarAprovadorGarantia($pedido_venda)
    {
        $emailTemplate = new EmailTemplate(EmailTemplate::GARANTIA);
        
        $titulo = $emailTemplate->titulo;
        $mensagem = $emailTemplate->mensagem;
        
        $titulo = $pedido_venda->render($titulo);
        $mensagem = $pedido_venda->render($mensagem);
        
        $user_id = $pedido_venda->system_users_id;

        $user = new SystemUsers($user_id);                           

        
        $notificationParam = [
            'key' => $pedido_venda->id
        ];
        $icon = 'fas fa-file-invoice-dollar';
        
        SystemNotification::register( $user_id, $titulo, $mensagem, new TAction(['PedidoVendaFormView', 'onShow'], $notificationParam), 'Visualizar Pedido', $icon);
        
        SendGridMailService::enviar($user->email, $titulo, $mensagem);
        
    }
    
    public static function onChangeitens_pedido_frotas_pedido_frotas_produto_familia_produto_id($param)
    {
        try
        {
            TTransaction::open(self::$database);
            $suiv_url = 'https://api.suiv.com.br/api/v4';

            
            $field_id = explode('_', $param['_field_id']);
            $field_id = end($field_id);
          
            $familia_produto = new FamiliaProduto($param['key']);
          
            $set = [
                'id' => $familia_produto->suiv_id,
                'description' => $familia_produto->nome
            ];

    
            $vehicletoken = Vehicletoken::where('veiculos_id', '=', $param['veiculos_id'])
                                                ->first();
            if ($vehicletoken) {
                $token = $vehicletoken->token;                       
            } else {
                throw new Exception("Veículos, aeronaves e/ou equipamentos sem token cadastrado e selecione para cadastrar o token.");
            }

            $nicks = SuivClient::getNicknames($token, $set['id']) ;
            // var_dump($familia_produto);
            $nickss = [];
            if ($nicks) {
                foreach($nicks as $nicksitem){
                    $conteudojson = $nicksitem;
                    $idnick_json = json_encode($conteudojson, JSON_UNESCAPED_UNICODE);
                    // Decodifica de volta em array associativo
                    $dados = json_decode($idnick_json, true);
                    // Pega o valor do campo 'id'
                    $idnick = $dados['id'] ?? null;
                    $descnick = $dados['description'] ?? null;
                                                        
                    $idnick = isset($idnick) ? (int)$idnick : 0;
                    $nickss[] = $idnick;

                    $parts = SuivClient::getParts($token, (int) $idnick);

                    if (!empty($parts) && is_iterable($parts)) {
                        foreach ($parts as $p) {
                            // Aceita tanto array quanto objeto
                            $idparts         = is_array($p) ? ($p['id'] ?? null)           : ($p->id ?? null);
                            $descriptionparts= is_array($p) ? ($p['description'] ?? null)  : ($p->description ?? null);
                            $partnumberparts = is_array($p) ? ($p['partNumber'] ?? null)   : ($p->partNumber ?? null);
                            $priceparts      = is_array($p) ? ($p['price'] ?? 0)           : ($p->price ?? 0);

                            if (!$idparts) {
                                continue; // pula registros inválidos
                            }

                            // Verifica se já existe produto vinculado a esta peça
                            $produtosuiv = Produto::where('suiv_peca_id', '=', $idparts)->first();

                            if (!$produtosuiv) {
                                $produtosuiv = new Produto();
                                $produtosuiv->nome              = $descriptionparts ?: 'Peça S/Descrição';
                                $produtosuiv->familia_produto_id= $familia_produto->id;
                                $produtosuiv->preco_venda       = (float) $priceparts;

                                $produtosuiv->suiv_grupo_id     = $set['id'];
                                $produtosuiv->suiv_peca_id      = $idparts;
                                $produtosuiv->suiv_nickname_id  = $idnick;
                                $produtosuiv->suiv_partnumber   = $partnumberparts;
                                $produtosuiv->suiv_preco_peca   = (float) $priceparts;

                                $produtosuiv->tipo_produto_id   = (int) TSession::getValue('tipo');
                                $produtosuiv->ativo             = 'T';
                                $produtosuiv->system_unit_id    = TSession::getValue('idunit');
                                $produtosuiv->store();
                            }
                        }
                    }


                } 

            } else {
                    foreach($nicks as $nicksitem){
                        $conteudojson = $nicksitem;
                        $idnick_json = json_encode($conteudojson, JSON_UNESCAPED_UNICODE);
                        // Decodifica de volta em array associativo
                        $dados = json_decode($idnick_json, true);
                        // Pega o valor do campo 'id'
                        $idnick = $dados['id'] ?? null;
                        $descnick = $dados['description'] ?? null;
                                                            
                        $idnick = isset($idnick) ? (int)$idnick : 0;
                        $nickss[] = $idnick;

                    }
            }
            TTransaction::close();
            if (!empty($param['key']))
            { 
                $criteria = new TCriteria();
                    // sempre filtra pelo grupo selecionado
                    if (!empty($familia_produto->suiv_id)) {
                        $criteria->add(new TFilter('suiv_grupo_id', '=', $familia_produto->suiv_id));
                    }

                    // se houver apelidos (nicknames), usa IN (NÃO usar '=' com array!)
                    $nickss = array_values(array_unique(array_filter($nickss, fn($v) => $v > 0)));
                    if (!empty($nickss)) {
                        $criteria->add(new TFilter('suiv_nickname_id', 'IN', $nickss));
                    }
         
                // $criteria->add(new TFilter('tipo_produto_id', '=', 1)); // produtos
                TDBCombo::reloadFromModel(self::$formName, 'itens_pedido_frotas_pedido_frotas_produto_id_'.$field_id, 'minierp', 'Produto', 'id', '{nome}', 'nome asc', $criteria, TRUE); 
            } 
            else 
            { 
                TCombo::clearField(self::$formName, 'itens_pedido_frotas_pedido_frotas_produto_id_'.$field_id); 
            }  

        }
        catch (Exception $e)
        {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }

            if (!SuivClient::shouldUseLocalFallback($e)) {
                new TMessage('error', $e->getMessage());
            }
        }
    } 

    public function onCadastrarItemProduto($param=null)
    {

        try 
        {
            
            $pageParam = [];
            TSession::setValue('pedido_frotas_form_data',null);
            $data = $this->form->getData(); // get form data as array
            $data->id = TSession::getValue('pedido_frotas_id');
            $data->key = TSession::getValue('pedido_frotas_id');
            TSession::setValue('pedido_frotas_form_data',$data);

            if ($data->veiculos_id==null)
            {
                throw new Exception("Selecione o veículo, aeronave e/ou equipamento para cadastrar os itens do pedido de frotas.");
            }
            if ($data->dt_pedido==null)
            {
                throw new Exception("Informe a data do pedido de frotas para cadastrar os itens do pedido de frotas.");
            }
            if ($data->data_limite_resposta == null)
            {
                throw new Exception("Informe a data limite do pedido de frotas para cadastrar os itens do pedido de frotas.");
            }
            if ($data->departamento_unit_id == null)
            {
                throw new Exception("Informe o departamento para cadastrar os itens do pedido de frotas.");
            }
            if ($data->tipo_manutencao_id == null)
            {
                throw new Exception("Informe o tipo de manutenção para cadastrar os itens do pedido de frotas.");
            }
            if ($data->km == null)
            {
                throw new Exception("Informe a KM atual para cadastrar os itens do pedido de frotas.");
            }
            if ($data->descricaopedido == null)
            {
                throw new Exception("Informe a descrição do pedido de frotas para cadastrar os itens do pedido de frotas.");
            }
            $this->form->setData($data); // fill form data
            if (!empty($data->id)) {
                $pedido = new PedidoFrotas($data->id);
                $this->fieldList_674111506deb6_items = $this->loadItems('DocumentosPedidoFrotas', 'pedido_frotas_id', $pedido, $this->fieldList_674111506deb6, function($masterObject, $detailObject, $objectItems){ //</blockLine>
                }, $this->criteria_fieldList_674111506deb6); //</blockLine>
            } else {
                $this->fieldList_674111506deb6->addHeader();
                $this->fieldList_674111506deb6->addDetail($this->default_item_fieldList_674111506deb6);
            }        
            // $this->redes1->unhide();

            TApplication::loadPage('ItensPedidoFrotasProdutoForm', 'onShow', [
                'pedido_frotas_id' => TSession::getValue('pedido_frotas_id')
            ]);
        
        }
        catch (Exception $e) 
        {
                        $this->form->setData( $this->form->getData() ); // keep form data
                         if (!empty($data->id)) {
                $pedido = new PedidoFrotas($data->id);
                $this->fieldList_674111506deb6_items = $this->loadItems('DocumentosPedidoFrotas', 'pedido_frotas_id', $pedido, $this->fieldList_674111506deb6, function($masterObject, $detailObject, $objectItems){ //</blockLine>
                }, $this->criteria_fieldList_674111506deb6); //</blockLine>
            } else {
                $this->fieldList_674111506deb6->addHeader();
                $this->fieldList_674111506deb6->addDetail($this->default_item_fieldList_674111506deb6);
            }        
          //  $this->redes1->unhide();

            new TMessage('error', $e->getMessage());    
        }

    }

    public function onCadastrarItemServico($param=null)
    {

        try 
        {
            $pageParam = [];
            TSession::setValue('pedido_frotas_form_data',null);
            $data = $this->form->getData(); // get form data as array
            $data->id = TSession::getValue('pedido_frotas_id');
            $data->key = TSession::getValue('pedido_frotas_id');
            TSession::setValue('pedido_frotas_form_data',$data);

            // $itens = ItensPedidoFrotas::where('pedido_frotas_id','=',$data->id)
            //                           ->where('tipo','=',1)
            //                           ->load();
            // if (!$itens) {
            //     throw new Exception("Para cadastrar um serviço, primeiro cadastre os produtos correspondentes. Os serviços dependem dos produtos selecionados.");
            // }

            if ($data->veiculos_id==null)
            {
                throw new Exception("Selecione o veículo, aeronave e/ou equipamento para cadastrar os itens do pedido de frotas.");
            }
            if ($data->dt_pedido==null)
            {
                throw new Exception("Informe a data do pedido de frotas para cadastrar os itens do pedido de frotas.");
            }
            if ($data->data_limite_resposta == null)
            {
                throw new Exception("Informe a data limite do pedido de frotas para cadastrar os itens do pedido de frotas.");
            }
            if ($data->departamento_unit_id == null)
            {
                throw new Exception("Informe o departamento para cadastrar os itens do pedido de frotas.");
            }
            if ($data->tipo_manutencao_id == null)
            {
                throw new Exception("Informe o tipo de manutenção para cadastrar os itens do pedido de frotas.");
            }
            if ($data->km == null)
            {
                throw new Exception("Informe a KM atual para cadastrar os itens do pedido de frotas.");
            }
            if ($data->descricaopedido == null)
            {
                throw new Exception("Informe a descrição do pedido de frotas para cadastrar os itens do pedido de frotas.");
            }
            $this->form->setData($data); // fill form data
            if (!empty($data->id)) {
                $pedido = new PedidoFrotas($data->id);
                $this->fieldList_674111506deb6_items = $this->loadItems('DocumentosPedidoFrotas', 'pedido_frotas_id', $pedido, $this->fieldList_674111506deb6, function($masterObject, $detailObject, $objectItems){ //</blockLine>
                }, $this->criteria_fieldList_674111506deb6); //</blockLine>
            } else {
                $this->fieldList_674111506deb6->addHeader();
                $this->fieldList_674111506deb6->addDetail($this->default_item_fieldList_674111506deb6);
            }        
        //    $this->redes1->unhide();

            TApplication::loadPage('ItensPedidoFrotasServicoForm', 'onShow', [
                'pedido_frotas_id' => TSession::getValue('pedido_frotas_id')
            ]);
        
        }
        catch (Exception $e) 
        {
                        $this->form->setData( $this->form->getData() ); // keep form data
                         if (!empty($data->id)) {
                $pedido = new PedidoFrotas($data->id);
                $this->fieldList_674111506deb6_items = $this->loadItems('DocumentosPedidoFrotas', 'pedido_frotas_id', $pedido, $this->fieldList_674111506deb6, function($masterObject, $detailObject, $objectItems){ //</blockLine>
                }, $this->criteria_fieldList_674111506deb6); //</blockLine>
            } else {
                $this->fieldList_674111506deb6->addHeader();
                $this->fieldList_674111506deb6->addDetail($this->default_item_fieldList_674111506deb6);
            }        
       //     $this->redes1->unhide();

            new TMessage('error', $e->getMessage());    
        }

    }
  
 
}
