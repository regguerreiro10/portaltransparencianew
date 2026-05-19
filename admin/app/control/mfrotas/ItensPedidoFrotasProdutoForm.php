<?php

//<fileHeader>
//<fileHeader>
//</fileHeader>
$__suivClientPath = __DIR__ . '/../../service/SuivClient.php';
if (!file_exists($__suivClientPath)) {
    throw new Exception('SuivClient.php não encontrado em: ' . $__suivClientPath);
}
require_once $__suivClientPath;

if (!class_exists(\app\service\SuivClient::class)) {
    throw new Exception('Classe \app\service\SuivClient não foi carregada');
}

use Adianti\Registry\TSession;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use app\service\SuivClient; // importa o nome curto
//</fileHeader>
//</fileHeader>
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TToast;

class ItensPedidoFrotasProdutoForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'ItensPedidoFrotas';
    private static $primaryKey = 'id';
    private static $formName = 'form_ItensPedidoFrotasProdutoForm';

    //<classProperties>

    //</classProperties>

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
        $this->form->setFormTitle("Cadastro de itens pedido frotas de Produto");
        $criteria_produto_id = new TCriteria();

        $criteria_familia_produto_id = new TCriteria();

        // if (TSession::getValue('familiaIDS')) {
        //     $criteria_familia_produto_id->add(new TFilter('id', 'IN', TSession::getValue('familiaIDS')));
        // }

         $criteria_produto_id->add(
            new TFilter('system_unit_id', 'IN',
                "(SELECT su.id FROM system_unit su 
                LEFT JOIN entidade e ON e.id = su.entidade_id 
                WHERE e.frotas = 1)"
            )
        );

                $criteria_produto_id->add(new TFilter('tipo_produto_id', '=', 1));


        //<onBeginPageCreation>

        //</onBeginPageCreation>

        $id = new THidden('id');
        $familia_produto_id = new TDBCombo('familia_produto_id', 'minierp', 'FamiliaProduto', 'id', '{nome_familia_suiv}','nome asc' , $criteria_familia_produto_id );
        // $produto_id = new TCombo('produto_id');
    
                $produto_id = new TSeekButton('produto_id');

              $produto_id1 = new TDBUniqueSearch('produto_id1', 'minierp', 'Produto', 'id', 'nome_com_familia','nome asc' , $criteria_produto_id );
        //                $produto_id->configureNoResultsQuickRegister(new TAction(['ProdutoSimpleFormSuiv', 'onQuickSave']), "Cadastrar", "fas:plus #69AA46", "btn-default");
        // $produto_id->setNoResultsMessage("Cadastre um novo produto/serviço");
        //    $produto_id->setMinLength(2);
        //    $produto_id->setFilterColumns(["nome"]);

           $obj = new ProdutoSeekWindow;
        $produto_id->setAction(new TAction(array($obj, 'onShow')));

        $qtde = new TEntry('qtde');
        $descricao = new TText('descricao'); 

        $familia_produto_id->setChangeAction(new TAction([$this,'onChangefamilia_produto_id']));

        // pega o ID HTML do campo (adianti gera isso)
        $id_field = $familia_produto_id->getId();
        $produto_id_name = 'produto_id';

        // injeta JS pra bloquear a UI assim que o usuário mudar o valor
        TScript::create("
            $(function() {
                window.gfSeekProdutoLoading = function(element) {
                    if (typeof __adianti_block_ui === 'function') {
                        __adianti_block_ui('Abrindo busca...');
                    }

                    \$(document).off('.gfSeekProduto');
                    \$(document).one('ajaxComplete.gfSeekProduto ajaxError.gfSeekProduto', function() {
                        if (typeof __adianti_unblock_ui === 'function') {
                            __adianti_unblock_ui();
                        }
                    });

                    setTimeout(function() {
                        if (typeof __adianti_unblock_ui === 'function') {
                            __adianti_unblock_ui();
                        }
                        \$(document).off('.gfSeekProduto');
                    }, 10000);

                    return;
                    var \$button = $(element);

                    if (!\$button.data('original-html')) {
                        \$button.data('original-html', \$button.html());
                    }

                    var restoreButton = function() {
                        \$button.html(\$button.data('original-html'));
                        \$button.css('pointer-events', '');
                        \$button.css('opacity', '');
                    };

                    $(document).off('.gfSeekProduto');
                    $(document).one('ajaxComplete.gfSeekProduto ajaxError.gfSeekProduto', function() {
                        restoreButton();
                    });

                    \$button.html('<i class=\"fas fa-spinner fa-spin\"></i>');
                    \$button.css('pointer-events', 'none');
                    \$button.css('opacity', '0.8');

                    setTimeout(function() {
                        restoreButton();
                        $(document).off('.gfSeekProduto');
                    }, 10000);
                };

                $('#{$id_field}').on('change', function() {
                    if ($(this).val()) {
                        __adianti_block_ui('Carregando');
                    }
                });

                $(document).on('click', 'span.tseekbutton[name=\"_{$produto_id_name}_seek\"], span.tseekbutton[for=\"{$produto_id_name}\"], span[name=\"_{$produto_id_name}_seek\"], span[for=\"{$produto_id_name}\"]', function() {
                    var element = this;
                    setTimeout(function() {
                        window.gfSeekProdutoLoading(element);
                    }, 0);
                });
            });
        ");

        
        
        $produto_id1->enableSearch();
        $familia_produto_id->enableSearch();

        $qtde->setSize('100%');
        $id->setSize('100%');
        $produto_id->setSize('100%');
        $produto_id1->setSize('100%');
        $descricao->setSize('100%', 70);
        $familia_produto_id->setSize('100%');


        // $familia_produto_id->addValidation("Grupo", new TRequiredValidator()); 
                $produto_id->addValidation("Produto", new TRequiredValidator()); 
        $qtde->addValidation("Quantidade", new TRequiredValidator()); 

        //<onBeforeAddFieldsToForm>
        //<onBeforeAddFieldsToForm>

        //</onBeforeAddFieldsToForm>
        // $row1 = $this->form->addFields([new TLabel("Grupo:", '#FF0000', '14px', null, '100%'),$id,$familia_produto_id],[new TLabel("Produto:", '#FF0000', '14px', null, '100%'),$produto_id]);
        $row1 = $this->form->addFields([new TLabel("Produto:", '#FF0000', '14px', null, '100%'),$id, $produto_id],[new TLabel("Nome:", null, '14px', null, '100%'),$produto_id1]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Qtde:", '#FF0000', '14px', null, '100%'),$qtde],[new TLabel("Obs:", null, '14px', null, '100%'),$descricao]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onsave_add = $this->form->addAction("Salvar e adicionar outro", new TAction([$this, 'onSave'], ['keep_open' => 1]), 'fas:plus #ffffff');
        $this->btn_onsave_add = $btn_onsave_add;
        $btn_onsave_add->addStyleClass('btn-success');

        // $btn_onshow = $this->form->addAction("Voltar", new TAction(['PedidoFrotasForm', 'onEdit']), 'fas:arrow-left #000000');
        // $this->btn_onshow = $btn_onshow;

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

        $style = new TStyle('right-panel > .container-part[page-name=ItensPedidoFrotasProdutoForm]');
        $style->width = '30% !important';   
        $style->show(true);

    }

    

    public static function onChangefamilia_produto_id($param)
    {
        try
        {

            TTransaction::open(self::$database);
            $suiv_url = 'https://api.suiv.com.br/api/v4';
            TSession::setValue('tipo',1); //produto
            TSession::setValue('familia_produto_id', null);
            TSession::setValue('familia_produto_id', $param['familia_produto_id'] ?? null);

            $session_pedido = TSession::getValue('pedido_frotas_form_data');
            $familia_produto = FamiliaProduto::where('id','=',TSession::getValue('familia_produto_id'))->first();

            $set = [
                'id'          => $familia_produto->suiv_id,
                'description' => $familia_produto->nome
            ];
            // new TMessage('info', 'Sincronizando peças do grupo: '.TSession::getValue('familia_produto_id').$set['id'].' - '.$set['description'].$session_pedido->veiculos_id);

            $vehicletoken = Vehicletoken::where('veiculos_id', '=', $session_pedido->veiculos_id)
                                        ->first();
            if ($vehicletoken) {
                $token = $vehicletoken->token;                       
            } else {
               $token = ''; // throw new Exception("Veículos, aeronaves e/ou equipamentos sem token cadastrado e selecione para cadastrar o token.");
            }
            if ($token<>'') { 
                try {
                    $nicks  = SuivClient::getNicknames($token, $set['id']);
                } catch (Exception $e) {
                    if (!SuivClient::shouldUseLocalFallback($e)) {
                        throw $e;
                    }

                    $nicks = [];
                }

                 $nickss = [];

                if ($nicks) {
                    foreach ($nicks as $nicksitem) {
                        $conteudojson = $nicksitem;
                        $idnick_json  = json_encode($conteudojson, JSON_UNESCAPED_UNICODE);
                        $dados        = json_decode($idnick_json, true);
                        // new TMessage('info', 'Sincronizando peças do grupo: '.$nicksitem->description);

                        $idnick  = $dados['id'] ?? null;
                        $descnick= $dados['description'] ?? null;
                                                                
                        $idnick   = isset($idnick) ? (int) $idnick : 0;
                        $nickss[] = $idnick;

                        try {
                            $parts = SuivClient::getParts($token, (int) $idnick);
                        } catch (Exception $e) {
                            if (!SuivClient::shouldUseLocalFallback($e)) {
                                throw $e;
                            }

                            $parts = [];
                        }

                        if (!empty($parts) && is_iterable($parts)) {
                            foreach ($parts as $p) {
                                $idparts          = is_array($p) ? ($p['id'] ?? null)           : ($p->id ?? null);
                                $descriptionparts = is_array($p) ? ($p['description'] ?? null)  : ($p->description ?? null);
                                $partnumberparts  = is_array($p) ? ($p['partNumber'] ?? null)   : ($p->partNumber ?? null);
                                $priceparts       = is_array($p) ? ($p['price'] ?? 0)           : ($p->price ?? 0);

                                if (!$idparts) {
                                    continue;
                                }

                                $produtosuiv = Produto::where('suiv_peca_id', '=', $idparts)->first();

                                $status = $produtosuiv ? 'Atualizado' : 'Criado';

                                $msg  = "Inserindo/atualizando peça: {$descriptionparts}";
                                $msg .= "<br>Peça ID: {$idparts}";
                                $msg .= "<br>Grupo: {$set['description']}";
                                $msg .= "<br>Preço: {$priceparts}";
                                $msg .= "<br>Produto: {$status}";

                                // new TMessage('info', $msg); 

                                if ($produtosuiv) {
                                    // atualiza
                                    $produtosuiv->nome               = $descriptionparts ?: 'Peça S/Descrição';
                                    $produtosuiv->familia_produto_id = $familia_produto->id;
                                    $produtosuiv->preco_venda        = (float) $priceparts;
                                    $produtosuiv->suiv_grupo_id      = $set['id'];
                                    $produtosuiv->suiv_peca_id       = $idparts;
                                    $produtosuiv->suiv_nickname_id   = $idnick;
                                    $produtosuiv->suiv_partnumber    = $partnumberparts;
                                    $produtosuiv->suiv_preco_peca    = (float) $priceparts;
                                    $produtosuiv->tipo_produto_id    = 1;
                                    $produtosuiv->store();
                                } else {
                                    // cria
                                    $produtosuiv = new Produto();
                                    $produtosuiv->nome               = $descriptionparts ?: 'Peça S/Descrição';
                                    $produtosuiv->familia_produto_id = $familia_produto->id;
                                    $produtosuiv->preco_venda        = (float) $priceparts;

                                    $produtosuiv->suiv_grupo_id      = $set['id'];
                                    $produtosuiv->suiv_peca_id       = $idparts;
                                    $produtosuiv->suiv_nickname_id   = $idnick;
                                    $produtosuiv->suiv_partnumber    = $partnumberparts;
                                    $produtosuiv->suiv_preco_peca    = (float) $priceparts;

                                    $produtosuiv->tipo_produto_id    = 1;
                                    $produtosuiv->ativo              = 'T';
                                    $produtosuiv->system_unit_id     = TSession::getValue('idunit');
                                    $produtosuiv->store();
                                }
                            }
                        }
                    }
                }
            }
            TTransaction::close();

            if (!empty($param['key']))
            { 
                $criteria = new TCriteria();
                $criteria = TCriteria::create([
                    'familia_produto_id' => TSession::getValue('familia_produto_id'),
                    'tipo_produto_id'    => 1,
                ]);                
                TDBCombo::reloadFromModel(self::$formName, 'produto_id', 'minierp', 'Produto', 'id', '{nome_suiv}', 'nome asc', $criteria, TRUE); 
            } 
            else 
            { 
                TCombo::clearField(self::$formName, 'produto_id'); 
            }  

             TTransaction::close(); // close the transaction

             TScript::create("__adianti_unblock_ui();");

        }
        catch (Exception $e)
        {
            // garante remoção do spinner em erro
             TScript::create("__adianti_unblock_ui();");

            // rollback da transação
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }

            if (!SuivClient::shouldUseLocalFallback($e)) {
                new TMessage('error', $e->getMessage());
            }
        }
    }

    public function onSave($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $this->form->validate();

            if ($param['qtde'] <= 0) {
                throw new Exception("A quantidade deve ser maior que zero.");
            }
            if (empty($param['produto_id'])) {
                throw new Exception("Selecione um produto.");
            }
            // if (empty($param['familia_produto_id'])) {
            //     throw new Exception("Selecione um grupo.");
            // }

            // --- SALVAR/CRIAR O PEDIDO PRINCIPAL ---
            $session_pedido = TSession::getValue('pedido_frotas_form_data');

            if (empty($session_pedido) || empty($session_pedido->id))
            {
                // Dados que vieram da sessão
                $session_pedido = TSession::getValue('pedido_frotas_form_data');

                $pedido = new PedidoFrotas;
                $pedido->fromArray((array) $session_pedido);

                // Ajustes de campos
                $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::PENDENTE;

                $dt_pedido       = new DateTime($pedido->dt_pedido);
                $pedido->mes     = $dt_pedido->format('m');
                $pedido->ano     = $dt_pedido->format('Y');
                $pedido->system_users_id = TSession::getValue('userid');
                $pedido->system_unit_id = TSession::getValue('idunit');
                $pedido->entidade_id = TSession::getValue('entidade');                
                $pedido->valor_total = 0;

                $pedido->store();

                 $this->registrarHistoricoPedidoFrotasPendente($pedido);
 
                // Atualiza sessão com o ID gerado
                $session_pedido->id = $pedido->id;
                $session_pedido->descricaopedido = $pedido->descricaopedido;
                $session_pedido->veiculos_id = $pedido->veiculos_id;
                $session_pedido->dt_pedido = $pedido->dt_pedido;
                $session_pedido->data_limite_resposta = $pedido->data_limite_resposta;
                $session_pedido->km = $pedido->km;
                $session_pedido->tipo_manutencao_id = $pedido->tipo_manutencao_id;
                $session_pedido->estado_pedido_frotas_id = $pedido->estado_pedido_frotas_id;
                $session_pedido->mes = $pedido->mes;
                $session_pedido->ano = $pedido->ano;
                $session_pedido->valor_total = $pedido->valor_total;
                $session_pedido->system_users_id = $pedido->system_users_id;
                $session_pedido->system_unit_id = $pedido->system_unit_id;
                $session_pedido->entidade_id = $pedido->entidade_id;
                TSession::setValue('pedido_frotas_id',$pedido->id);
                TSession::setValue('pedido_frotas_form_data', $session_pedido);

                // Atualiza o formulário principal com o ID do pedido
            }
            else
            {
                // Se já tiver id na sessão, você pode carregar o pedido se precisar:
                // $pedido = new PedidoFrotas($session_pedido->id);
            }

            // --- SALVAR ITEM DO PEDIDO ---
            $object = new ItensPedidoFrotas($param['id'] ?? null);

            $data = $this->form->getData();
            $object->fromArray((array) $data);

            // Garante a FK do pedido no item
            $session_pedido = TSession::getValue('pedido_frotas_form_data');
            if (!empty($session_pedido->id)) {
                $object->deleted_at = null;
                $object->pedido_frotas_id = $session_pedido->id;
                $object->tipo = 1;
            }

            $object->store();

            $this->fireEvents($object);

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["id"] = $session_pedido->id;
            $loadPageParam["key"] = $session_pedido->id;            

            // atualiza o ID do item no form (se precisar)
            $data->id = $object->id;

            $this->form->setData($data);
            TForm::sendData('form_PedidoFrotasForm', $session_pedido);

            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');

            if (!empty($param['keep_open'])) {
                self::reloadPedidoItemLists();
                $this->clearItemForm();
            } else {
                TApplication::loadPage('PedidoFrotasForm', 'onEdit', $loadPageParam); 

                TScript::create("Template.closeRightPanel();"); 
            }

        }
        catch (Exception $e)
        {
            $this->fireEvents($this->form->getData());
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
            TTransaction::rollback();
        }
    }

    private function clearItemForm()
    {
        $data = new stdClass;
        $data->id = '';
        $data->familia_produto_id = '';
        $data->produto_id = '';
        $data->produto_id1 = '';
        $data->qtde = '';
        $data->descricao = '';

        $this->form->setData($data);
        TForm::sendData(self::$formName, $data, false, false);
    }

    private static function reloadPedidoItemLists()
    {
        TScript::create("
            if (typeof __adianti_load_page === 'function') {
                __adianti_load_page('index.php?class=ItensPedidoFrotasProdutoList&method=onReload&target_container=b691a018e1c120');
                __adianti_load_page('index.php?class=ItensPedidoFrotasServicoList&method=onReload&target_container=b691a018e1c121');
            }
        ");
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

                $object = new ItensPedidoFrotas($key); // instantiates the Active Record //</blockLine>
                $object->produto_id1 = $object->produto_id;

                //</beforeSetDataAutoCode> //</blockLine>

                $this->form->setData($object); // fill the form //</blockLine>

                //</afterSetDataAutoCode> //</blockLine>
//<generatedAutoCode>

                $this->fireEvents($object);

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

        //<onFormClear>

        //</onFormClear>

    }

     public function onShow($param = null)
    {

        //<onShow>
                        TTransaction::open(self::$database);

           $session_pedido = TSession::getValue('pedido_frotas_form_data');
           TSession::setValue('tipo',1); //produto

           if (TSession::getValue('utiliza_temparia')==1) {
                    $veiculo = new Veiculos($session_pedido->veiculos_id);
                   
                    $vehicletoken = Vehicletoken::where('veiculos_id', '=', $session_pedido->veiculos_id)
                                                ->first();
                    if ($vehicletoken) {
                        $token = $vehicletoken->token;
                    } 
                    else 
                    {
                        $placa = !empty($veiculo->placa) ? trim($veiculo->placa) : '';
                        try {
                            $token = $placa !== '' ? SuivClient::getVehicleTokenByPlate($placa) : '';
                        } catch (Exception $e) {
                            if (!SuivClient::shouldUseLocalFallback($e)) {
                                throw $e;
                            }

                            $token = '';
                        }
                        if ($token) {
                             $newVehicletoken = new Vehicletoken();
                             $newVehicletoken->veiculos_id = $param['veiculos_id'];
                             $newVehicletoken->token = $token;
                             $newVehicletoken->store();
                          } else {
                               $token = ''; // throw new Exception("Veículos, aeronaves e/ou equipamentos sem token cadastrado e selecione para cadastrar o token.");
                         }

                if ($token<>'') {
                    // TSession::setValue('familiaIDS',null);

                   // chama a API de verdade
                        try {
                            $grupopecas = SuivClient::getSets($token);
                        } catch (Exception $e) {
                            if (!SuivClient::shouldUseLocalFallback($e)) {
                                throw $e;
                            }

                            $grupopecas = [];
                        }

                        TSession::setValue('grupo_pecas_suiv',null);  

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

                                    }
                                    // $familiaIDS = TSession::getValue('familiaIDS');
                                    // $familiaIDS[] = $familia_produto->id;
                                    // TSession::setValue('familiaIDS',$familiaIDS);
                                    // new TMessage('info', 'Grupo de peças sincronizado: '.$desc);

                                }
                            }
                            // var_dump('Grupos de peças sincronizados com sucesso.', $familiaIDS);
                        }
                     }
           
                        $criteria = new TCriteria();
                        // if (TSession::getValue('familiaIDS')) {
                        //     $criteria->add(new TFilter('id', 'IN',TSession::getValue('familiaIDS') ));
                        // }                        
                                    
                        TDBCombo::reloadFromModel(
                            self::$formName,
                            'familia_produto_id',       // <- campo da família no form
                            'minierp',
                            'FamiliaProduto',           // <- ActiveRecord da família
                            'id',
                            '{nome_familia_suiv}',      // <- usa o getter da família
                            'nome asc',
                            $criteria,                  // ou null, se não tiver filtro
                            TRUE
                        );

                }
           }
            TTransaction::close();
        //</onShow>
    } 
    public static function fireEvents( $object )
    {
        $obj = new stdClass;
        if(is_object($object) && get_class($object) == 'stdClass')
        {
            if(isset($object->familia_produto_id))
            {
                $value = $object->familia_produto_id;

                $obj->familia_produto_id = $value;
            }
            if(isset($object->produto_id))
            {
                $value = $object->produto_id;

                $obj->produto_id = $value;
            }
        }
        elseif(is_object($object))
        {
            if(isset($object->familia_produto_id))
            {
                $value = $object->familia_produto_id;

                $obj->familia_produto_id = $value;
            }
            if(isset($object->produto_id))
            {
                $value = $object->produto_id;

                $obj->produto_id = $value;
            }
        }
        TForm::sendData(self::$formName, $obj);
    }  

    public static function getFormName()
    {
        return self::$formName;
    }

    public static function onSetProject($param) {
        try {
            TTransaction::open(self::$database);
            $idveiculo = TSession::getValue('pedido_frotas_form_data')->veiculos_id;

            if ($idveiculo == null) {         
                throw new Exception("Selecione um veículo!");

            }


            TTransaction::close();
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
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
    public function hideModal()
    {
        echo '<script> $("#loadMe").modal("hide");console.log("modal finalizado"); </script>';
    }

    //</hideLine> <addUserFunctionsCode/>

    //<userCustomFunctions>

    //</userCustomFunctions>

}
