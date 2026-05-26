<?php

use Adianti\Database\TTransaction;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TNumeric;
use Adianti\Widget\Form\THidden;
include_once 'app/service/CalculoTaxasImpostosService.php';

class ContaPagarForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Conta';
    private static $primaryKey = 'id';
    private static $formName = 'form_ContaPagarForm';

    private static function formatNumericForm($value)
    {
        return number_format((float) ($value ?? 0), 2, ',', '.');
    }

    private static function parseNumericForm($value): float
    {
        if (is_null($value) || $value === '') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = str_replace('.', '', (string) $value);
        $value = str_replace(',', '.', $value);
        return (float) $value;
    }

    private static function obterBaseCalculoPorItens(Conta $conta): ?array
    {
        if (empty($conta->pedido_frotas_id)) {
            return null;
        }

        $proposta = Propostas::where('pedido_frotas_id', '=', $conta->pedido_frotas_id)
                            ->where('pessoa_id', '=', $conta->pessoa_id)
                            ->where('cidade_id', '=', $conta->cidade_id)
                            ->first();

        if (!$proposta) {
            $proposta = Propostas::where('pedido_frotas_id', '=', $conta->pedido_frotas_id)
                                ->where('cidade_id', '=', $conta->cidade_id)
                                ->first();
        }

        if (!$proposta) {
            return null;
        }

        $itens = ItensPropostas::where('propostas_id', '=', $proposta->id)
            ->where('deleted_at', 'is', null)
            ->load();

        if (!$itens) {
            return null;
        }

        $valorProd = 0.0;
        $valorServ = 0.0;
        $valorDesconto = 0.0;

        foreach ($itens as $item) {
            $vItem = (float) ($item->valor ?? 0);
            $qtd = (float) ($item->qtde ?? 1);
            $vltItem = CalculoTaxasImpostosService::money($vItem * $qtd);

            if ((int) ($item->tipo ?? 0) === 1) {
                $valorProd = CalculoTaxasImpostosService::money($valorProd + $vltItem);
            } elseif ((int) ($item->tipo ?? 0) === 2) {
                $valorServ = CalculoTaxasImpostosService::money($valorServ + $vltItem);
            }

            $valorDesconto = CalculoTaxasImpostosService::money($valorDesconto + ((float) ($item->perc_desconto ?? 0)));
        }

        return [
            'valorProd' => CalculoTaxasImpostosService::money($valorProd),
            'valorServ' => CalculoTaxasImpostosService::money($valorServ),
            'valorDesconto' => CalculoTaxasImpostosService::money($valorDesconto),
        ];
    }

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
        $this->form->setFormTitle("Cadastro de conta a pagar");

        $criteria_pessoa_id = new TCriteria();
        $criteria_categoria_id = new TCriteria();
        $criteria_forma_pagamento_id = new TCriteria();
        $criteria_departamento_unit_id = new TCriteria();
        $criteria_tipo_documentos_propostas_id = new TCriteria;

        $filterVar = GrupoPessoa::FORNECEDOR;
        $criteria_pessoa_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '{$filterVar}')")); 
        $filterVar = TipoConta::PAGAR;
        $criteria_categoria_id->add(new TFilter('tipo_conta_id', '=', $filterVar)); 

        $criteria_departamento_unit_id->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit'))); 

        $id = new TEntry('id');
        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa_id );
        $categoria_id = new TDBCombo('categoria_id', 'minierp', 'Categoria', 'id', '{nome}','nome asc' , $criteria_categoria_id );
        $forma_pagamento_id = new TDBCombo('forma_pagamento_id', 'minierp', 'FormaPagamento', 'id', '{nome}','nome asc' , $criteria_forma_pagamento_id );
        $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}', 'name asc', $criteria_departamento_unit_id);
        $dt_emissao = new TDate('dt_emissao');
        $dt_pagamento = new TDate('dt_pagamento');
        $dt_vencimento = new TDate('dt_vencimento');
        $tipo_documentos_propostas_id = new TDBSelect('tipo_documentos_propostas_id', 'minierp', 'TipoDocumentosPropostas', 'id', '{descricao}','id asc' , $criteria_tipo_documentos_propostas_id);
        $valor = new TNumeric('valor', '2', ',', '.' );
        $valor_txcontrato = new TNumeric('valor_txcontrato', '2', ',', '.' );
        $valor_txadm = new TNumeric('valor_txadm', '2', ',', '.' );
        $valor_txantecipacao = new TNumeric('valor_txantecipacao', '2', ',', '.' );
        $valor_liquido = new TNumeric('valor_liquido', '2', ',', '.' );
        $valor_total_liq_tx_conta = new TNumeric('valor_total_liq_tx_conta', '2', ',', '.' );
        $valor_produto_s_desc_txc = new TNumeric('valor_produto_s_desc_txc', '2', ',', '.' );
        $valor_servico_s_desc_txc = new TNumeric('valor_servico_s_desc_txc', '2', ',', '.' );
        $ir = new TNumeric('ir', '2', ',', '.' );
        $csll = new TNumeric('csll', '2', ',', '.' );
        $cofins = new TNumeric('cofins', '2', ',', '.' );
        $pis = new TNumeric('pis', '2', ',', '.' );
        $vl_imp_prod = new TNumeric('vl_imp_prod', '2', ',', '.' );
        $porcProd = new THidden('porcProd');
        $ir_servico = new TNumeric('ir_servico', '2', ',', '.' );
        $csll_servico = new TNumeric('csll_servico', '2', ',', '.' );
        $cofins_servico = new TNumeric('cofins_servico', '2', ',', '.' );
        $pis_servico = new TNumeric('pis_servico', '2', ',', '.' );
        $iss_servico = new TNumeric('iss_servico', '2', ',', '.' );
        $vl_imp_serv = new TNumeric('vl_imp_serv', '2', ',', '.' );
        $porcServ = new THidden('porcServ');
        $parcela = new TSpinner('parcela');
        $obs = new TText('obs');        

        $pessoa_id->setChangeAction(new TAction([$this, 'onChangeFornecedor'])); //trazer dados taxas
        $valor->setExitAction(new TAction([$this, 'onChangeTaxas'])); //calcular campos
        $valor_txcontrato->setExitAction(new TAction([$this, 'onChangeTaxas'])); //calcular campos
        $valor_txadm->setExitAction(new TAction([$this, 'onChangeTaxas'])); //calcular campos
        $valor_txantecipacao->setExitAction(new TAction([$this, 'onChangeTaxas'])); //calcular campos

        $this->form->addFields([$porcProd,$porcServ]);

        $pessoa_id->addValidation("Pessoa", new TRequiredValidator()); 
        // $categoria_id->addValidation("Categoria", new TRequiredValidator()); 
        $forma_pagamento_id->addValidation("Forma de pagamento", new TRequiredValidator()); 

        $id->setEditable(false);
        $ir->setEditable(false);
        $csll->setEditable(false);
        $cofins->setEditable(false);
        $pis->setEditable(false);
        $vl_imp_prod->setEditable(false);
        $ir_servico->setEditable(false);
        $csll_servico->setEditable(false);
        $cofins_servico->setEditable(false);
        $pis_servico->setEditable(false);
        $iss_servico->setEditable(false);
        $vl_imp_serv->setEditable(false);
        $parcela->setRange(1, 2000, 1);
        $categoria_id->enableSearch();
        $tipo_documentos_propostas_id->enableSearch();

        // No fluxo normal de cadastro/edicao os campos permanecem habilitados.
        // Restricoes pontuais continuam sendo aplicadas em onQuitarPagar.

        $dt_emissao->setMask('dd/mm/yyyy');
        $dt_pagamento->setMask('dd/mm/yyyy');
        $dt_vencimento->setMask('dd/mm/yyyy');

        $dt_emissao->setDatabaseMask('yyyy-mm-dd');
        $dt_pagamento->setDatabaseMask('yyyy-mm-dd');
        $dt_vencimento->setDatabaseMask('yyyy-mm-dd');

        $id->setSize(100);
        $ir->setSize('100%');
        $pis->setSize('100%');
        $csll->setSize('100%');
        $cofins->setSize('100%');
        $ir_servico->setSize('100%');
        $csll_servico->setSize('100%');
        $cofins_servico->setSize('100%');
        $pis_servico->setSize('100%');
        $iss_servico->setSize('100%');
        $vl_imp_prod->setSize('100%');
        $vl_imp_serv->setSize('100%');
        $valor->setSize('100%');
        $dt_emissao->setSize(110);
        $parcela->setSize('100%');
        $obs->setSize('100%', 100);
        $pessoa_id->setSize('100%');
        $dt_pagamento->setSize(110);
        $dt_vencimento->setSize(110);
        $categoria_id->setSize('100%');
        $forma_pagamento_id->setSize('100%');
        $departamento_unit_id->setSize('100%');
        $valor_total_liq_tx_conta->setSize('100%');
        $tipo_documentos_propostas_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Fornecedor:", '#ff0000', '14px', null, '100%'),$pessoa_id]);
        $row2->layout = ['col-sm-12'];

        $row20 = $this->form->addFields([new TLabel("Departamento:", '#ff0000', '14px', null, '100%'),$departamento_unit_id]);
        $row20->layout = ['col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("Forma de pagamento:", '#ff0000', '14px', null, '100%'),$forma_pagamento_id],[new TLabel("Categoria:", '#ff0000', '14px', null, '100%'),$categoria_id], [new TLabel("Parcela:", null, '14px', null, '100%'),$parcela]);
        $row3->layout = ['col-sm-4','col-sm-4','col-sm-4'];

        $row4 = $this->form->addFields([new TLabel("Data de Aprovação:", null, '14px', null, '100%'),$dt_emissao],[new TLabel("Data de pagamento:", null, '14px', null, '100%'),$dt_pagamento],[new TLabel("Data de vencimento:", null, '14px', null, '100%'),$dt_vencimento]);
        $row4->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row5 = $this->form->addFields([new TLabel("Valor Bruto:", null, '14px', null, '100%'),$valor],[new TLabel("Valor TxContrato:", null, '14px', null, '100%'),$valor_txcontrato]);
        $row5->layout = [' col-sm-4',' col-sm-4'];

        $row5 = $this->form->addFields([new TLabel("Ir:", null, '14px', null, '100%'),$ir],[new TLabel("Csll:", null, '14px', null, '100%'),$csll], [new TLabel("Cofins:", null, '14px', null, '100%'),$cofins], [new TLabel("Pis:", null, '14px', null, '100%'),$pis], [new TLabel("Valor Imp. Prod.:", null, '14px', null, '100%'),$vl_imp_prod]);
        $row5->layout = [' col-sm-2',' col-sm-2',' col-sm-2', 'col-sm-2', 'col-sm-2'];

        $row5 = $this->form->addFields([new TLabel("Ir Serviço:", null, '14px', null, '100%'),$ir_servico],[new TLabel("Csll Serviço:", null, '14px', null, '100%'),$csll_servico], [new TLabel("Cofins Serv.:", null, '14px', null, '100%'),$cofins_servico], [new TLabel("Pis Serviço:", null, '14px', null, '100%'),$pis_servico], [new TLabel("Iss Serviço:", null, '14px', null, '100%'),$iss_servico], [new TLabel("Valor Imp. Serv.:", null, '14px', null, '100%'),$vl_imp_serv]);
        $row5->layout = [' col-sm-2',' col-sm-2',' col-sm-2', 'col-sm-2','col-sm-2', 'col-sm-2'];

        $row7 = $this->form->addFields([new TLabel("Valor TxAdm:", null, '14px', null, '100%'),$valor_txadm], [new TLabel("Valor TxAntecipação:", null, '14px', null, '100%'),$valor_txantecipacao], [new TLabel("Valor Total Liquido:", null, '14px', null, '100%'),$valor_total_liq_tx_conta]);
        $row7->layout = [' col-sm-4', ' col-sm-4',' col-sm-4'];

        $row6 = $this->form->addFields([new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row6->layout = ['col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $this->form->addAction("Atualizar Taxas Impostos", new TAction([$this, 'onAtualizarTaxas']), 'fas:sync #1f6feb');

        // $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        // $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['ContaPagarList', 'onShow']), 'fas:arrow-left #000000');
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

    }

    public function onSave($param = null) 
    {
        $formDataSnapshot = null;

        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            // Preserva exatamente o que o usuário digitou para restaurar em caso de erro.
            $formDataSnapshot = clone $this->form->getData();
            // Alguns TNumeric desabilitados podem não vir corretamente em getData()
            // após post/validação. Captura direto dos widgets para restauração fiel.
            $snapshotNumericFields = [
                'valor_liquido',
                'valor_total_liq_tx_conta',
                'vl_imp_prod',
                'vl_imp_serv',
                'valor_txadm',
                'valor_txantecipacao',
                'valor_txcontrato',
            ];
            foreach ($snapshotNumericFields as $fieldName) {
                $field = $this->form->getField($fieldName);
                if ($field) {
                    $formDataSnapshot->$fieldName = $field->getValue();
                }
            }

            $this->form->validate(); // validate form data

            $data = clone $formDataSnapshot; // get form data as array

            if (!empty($data->id))
            {
                $object = new Conta($data->id); // edit existing record
            }
            else
            {
                $object = new Conta(); // create new record
                unset($data->id); // never force PK on insert
            }

            // Em edição, campos TNumeric desabilitados podem não voltar no POST.
            // Reaproveita os valores atuais da conta para não zerar cálculos no save.
            $currentConta = null;
            if (!empty($data->id)) {
                $currentConta = new Conta($data->id);

                $disabledCalcFields = [
                    'pessoa_id',
                    'departamento_unit_id',
                    'forma_pagamento_id',
                    'dt_emissao',
                    'dt_vencimento',
                    'parcela',
                    'valor_txcontrato',
                    'valor_produto_s_desc_txc',
                    'valor_servico_s_desc_txc',
                    'ir',
                    'csll',
                    'cofins',
                    'pis',
                    'ir_servico',
                    'csll_servico',
                    'cofins_servico',
                    'pis_servico',
                    'iss_servico',
                    'vl_imp_prod',
                    'vl_imp_serv',
                    'valor_txadm',
                    'valor_txantecipacao',
                    'valor_liquido',
                    'valor_total_liq_tx_conta',
                ];

                foreach ($disabledCalcFields as $field) {
                    if (!isset($data->$field) || $data->$field === '') {
                        $data->$field = $currentConta->$field ?? null;
                    }
                }
            }
            $object->fromArray( (array) $data); // load the object with data

            $object->entidade_id = TSession::getValue('entidade');
            $object->system_users_id = TSession::getValue('userid');
            $object->tipo_conta_id = TipoConta::PAGAR;
            $object->system_unit_id = TSession::getValue('idunit');

            // Recalcula com a mesma regra do PedidoFrotasList::onGerarFinanceiro
            // para evitar divergência de centavos entre UI e persistência.
            if (!empty($object->pessoa_id)) {
                $taxaspessoa = TaxasPessoa::where('pessoa_id', '=', $object->pessoa_id)
                    ->where('deleted_at', 'is', null)
                    ->where('entidade_id', '=', TSession::getValue('entidade'))
                    ->where('system_unit_id', '=', TSession::getValue('idunit'))
                    ->first();

                if ($taxaspessoa) {
                    $object->valor = self::parseNumericForm($object->valor ?? 0);

                    $valorProd = self::parseNumericForm($object->valor_produto_s_desc_txc ?? 0);
                    $valorServ = self::parseNumericForm($object->valor_servico_s_desc_txc ?? 0);
                    $valorBrutoConta = self::parseNumericForm($object->valor ?? 0);

                    $baseItens = self::obterBaseCalculoPorItens($object);
                    if ($baseItens) {
                        $valorProd = (float) $baseItens['valorProd'];
                        $valorServ = (float) $baseItens['valorServ'];
                        $valorBrutoConta = CalculoTaxasImpostosService::money($valorProd + $valorServ);
                        $object->valor = $valorBrutoConta;
                    }

                    $somaMix = $valorProd + $valorServ;
                    if ($valorBrutoConta > 0) {
                        if ($somaMix <= 0) {
                            $valorProd = $valorBrutoConta;
                            $valorServ = 0;
                        } elseif (abs($somaMix - $valorBrutoConta) > 0.01) {
                            $fator = $valorBrutoConta / $somaMix;
                            $valorProd = CalculoTaxasImpostosService::money($valorProd * $fator);
                            $valorServ = CalculoTaxasImpostosService::money($valorBrutoConta - $valorProd);
                        }
                    }

                    $imp = CalculoTaxasImpostosService::montarContextoConta($object, $valorProd, $valorServ, $taxaspessoa);
                    $valorTxContratoAtual = self::parseNumericForm($object->valor_txcontrato ?? 0);
                    if ($baseItens) {
                        $valorTxContratoAtual = (float) $baseItens['valorDesconto'];
                    }
                    if (($imp['bruto'] ?? 0) > 0 && $valorTxContratoAtual >= 0) {
                        $imp['perc_tx_contrato'] = ($valorTxContratoAtual / $imp['bruto']) * 100;
                    }
                    $imp['valor_txcontrato_fixado'] = CalculoTaxasImpostosService::money($valorTxContratoAtual);
                    $calc = CalculoTaxasImpostosService::calcularPorContexto($imp);

                    $object->valor_produto_s_desc_txc = $valorProd;
                    $object->valor_servico_s_desc_txc = $valorServ;

                    $object->valor_txcontrato = CalculoTaxasImpostosService::money($valorTxContratoAtual);
                    // No ContaPagarForm, valor_liquido segue o líquido final calculado pelo service
                    $object->valor_liquido = $calc['base_pos_txcontrato'] ?? 0;

                    $object->valor_produto_c_desc_txc = $calc['valor_produto_c_desc_txc'] ?? 0;
                    $object->valor_servico_c_desc_txc = $calc['valor_servico_c_desc_txc'] ?? 0;

                    $object->ir = $imp['impostos']['ir'] ?? 0;
                    $object->csll = $imp['impostos']['csll'] ?? 0;
                    $object->cofins = $imp['impostos']['cofins'] ?? 0;
                    $object->pis = $imp['impostos']['pis'] ?? 0;

                    $object->ir_servico = $imp['impostos']['ir_servico'] ?? 0;
                    $object->csll_servico = $imp['impostos']['csll_servico'] ?? 0;
                    $object->cofins_servico = $imp['impostos']['cofins_servico'] ?? 0;
                    $object->pis_servico = $imp['impostos']['pis_servico'] ?? 0;
                    $object->iss_servico = $imp['impostos']['iss_servico'] ?? 0;

                    $object->vl_imp_prod = $calc['vl_imp_prod'] ?? 0;
                    $object->vl_imp_serv = $calc['vl_imp_serv'] ?? 0;

                    $object->valor_liqbase_prod_posimp = $calc['valor_liqbase_prod_posimp'] ?? 0;
                    $object->valor_liqbase_serv_posimp = $calc['valor_liqbase_serv_posimp'] ?? 0;
                    $object->valor_txc_imp_produto_servico = $calc['valor_txc_imp_produto_servico'] ?? 0;

                    $object->txadm = $imp['perc_tx_adm'] ?? 0;
                    $object->valor_txadm = $calc['valor_txadm'] ?? 0;
                    $object->valor_txantecipacao = $calc['valor_txantecipacao'] ?? 0;
                    $object->valor_total_liq_tx_conta = $calc['valor_total_liq_tx_conta'] ?? 0;
                }
            }

            $object->store(); // save the object 

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            // Recarrega o que foi persistido para exibir os cálculos corretos no form
            $this->form->setData($object); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('ContaPagarList', 'onShow', $loadPageParam); 

                        TScript::create("Template.closeRightPanel();"); 

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            if ($formDataSnapshot) {
                $restoreData = clone $formDataSnapshot;

                // Evita distorção (x100) ao reidratar TNumeric após erro de validação/save
                // quando os valores chegam em formato numérico bruto (ex.: 5149.15).
                $numericFields = [
                    'valor',
                    'valor_txcontrato',
                    'valor_txadm',
                    'valor_txantecipacao',
                    'valor_liquido',
                    'valor_total_liq_tx_conta',
                    'valor_produto_s_desc_txc',
                    'valor_servico_s_desc_txc',
                    'ir',
                    'csll',
                    'cofins',
                    'pis',
                    'vl_imp_prod',
                    'ir_servico',
                    'csll_servico',
                    'cofins_servico',
                    'pis_servico',
                    'iss_servico',
                    'vl_imp_serv',
                ];

                foreach ($numericFields as $field) {
                    if (isset($restoreData->$field)) {
                        $restoreData->$field = self::formatNumericForm(self::parseNumericForm($restoreData->$field));
                    }
                }

                // Hidden usado no recálculo deve permanecer numérico "cru"
                if (isset($restoreData->porcProd)) {
                    $restoreData->porcProd = self::parseNumericForm($restoreData->porcProd);
                }
                if (isset($restoreData->porcServ)) {
                    $restoreData->porcServ = self::parseNumericForm($restoreData->porcServ);
                }
                $this->form->setData($restoreData); // keep original user input
            } else {
                $this->form->setData($this->form->getData());
            }
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

                $object = new Conta($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form 

                // if(!empty($object->pessoa_id)){
                //     self::onChangeFornecedor(['key' => $object->pessoa_id]);
                // }

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

    public static function onChangeFornecedor($param)
    {
        try{
            // new TMessage('info', '<pre>'.print_r($param, true).'</pre>');
            $id = $param['pessoa_id'] ?? $param['key'] ?? null;

            $obj = new stdClass;

            if(empty($id))
            {
                
                $obj->ir = null;
                $obj->csll = null;
                $obj->cofins = null;
                $obj->pis = null;

                //verificar se precisa adionar
                // $porcProd =
                // $obj->porcProd = $porcProd;

                $obj->ir_servico = null;
                $obj->csll_servico = null;
                $obj->cofins_servico = null;
                $obj->pis_servico = null;
                $obj->iss_servico = null;

                TForm::sendData(self::$formName, $obj, false, false);
                return;
            }

            TTransaction::open(self::$database);

            $taxas = TaxasPessoa::where('pessoa_id', '=', $id)
                                ->where('entidade_id', '=', TSession::getValue('entidade'))
                                ->where('system_unit_id', '=', TSession::getValue('idunit'))
                                ->where('deleted_at', 'is', null)
                                ->first();

            $obj->ir = $taxas->ir;
            $obj->csll = $taxas->csll;
            $obj->cofins = $taxas->cofins;
            $obj->pis = $taxas->pis;

            $porcProd = $taxas->ir + $taxas->csll + $taxas->cofins + $taxas->pis;
            $obj->porcProd = $porcProd;

            $obj->ir_servico = $taxas->ir_servico;
            $obj->csll_servico = $taxas->csll_servico;
            $obj->cofins_servico = $taxas->cofins_servico;
            $obj->pis_servico = $taxas->pis_servico;
            $obj->iss_servico = $taxas->iss_servico;

            $porcServ = $taxas->ir_servico + $taxas->csll_servico + $taxas->cofins_servico + $taxas->pis_servico + $taxas->iss_servico;
            $obj->porcServ = $porcServ;

            TTransaction::close();

            TForm::sendData(self::$formName, $obj, false, false);

            
            $param['porcProd'] = $porcProd;
            $param['porcServ'] = $porcServ;

            self::onChangeTaxas($param);
            
        }
        catch(Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onChangeTaxas($param){
        try{
            $key = $param['key'] ?? $param['id'] ?? null;
            $key = (is_scalar($key) && preg_match('/^\d+$/', (string) $key)) ? (int) $key : null;

            $valor = self::parseNumericForm($param['valor'] ?? 0);
            $valor_txcontrato = self::parseNumericForm($param['valor_txcontrato'] ?? 0);
            $valor_txadm = self::parseNumericForm($param['valor_txadm'] ?? 0);
            $valor_txantecipacao = self::parseNumericForm($param['valor_txantecipacao'] ?? 0);
            $porcImpProd = self::parseNumericForm($param['porcProd'] ?? 0);
            $porcImpServ = self::parseNumericForm($param['porcServ'] ?? 0);

            $valor_produto_s_desc_txc = 0;
            $valor_servico_s_desc_txc = 0;

            if (!empty($key)) {
                TTransaction::open(self::$database);
                $object = new Conta($key);
                $valor_produto_s_desc_txc = (float) ($object->valor_produto_s_desc_txc ?? 0);
                $valor_servico_s_desc_txc = (float) ($object->valor_servico_s_desc_txc ?? 0);
                TTransaction::close();
            }

            if ($valor <= 0) {
                $obj = new stdClass;
                $obj->vl_imp_prod = self::formatNumericForm(0);
                $obj->vl_imp_serv = self::formatNumericForm(0);
                $obj->valor_liquido = self::formatNumericForm(0);
                $obj->valor_total_liq_tx_conta = self::formatNumericForm(0);
                TForm::sendData(self::$formName, $obj, false, false);
                return;
            }

            $soma_mix_bruto = (float) $valor_produto_s_desc_txc + (float) $valor_servico_s_desc_txc;

            if ($soma_mix_bruto <= 0) {
                $valor_produto_s_desc_txc = 0;
                $valor_servico_s_desc_txc = 0;
                $soma_mix_bruto = $valor;
            }

            // O mix (produto/serviço) deve ser calculado pela própria soma do mix salvo,
            // e não pelo valor bruto atual, pois em edição esse bruto pode ter sido alterado.
            $porcProd_stxc = ($soma_mix_bruto > 0) ? ((float)$valor_produto_s_desc_txc / $soma_mix_bruto) : 0;
            $porcServ_stxc = ($soma_mix_bruto > 0) ? ((float)$valor_servico_s_desc_txc / $soma_mix_bruto) : 0;

            $base_pos_txcontrato_manual = max(0, $valor - $valor_txcontrato);
            $base_prod_ctxc_manual = $base_pos_txcontrato_manual * $porcProd_stxc;
            $base_serv_ctxc_manual = $base_pos_txcontrato_manual * $porcServ_stxc;
            $vl_imp_prod_manual = $base_prod_ctxc_manual * ($porcImpProd / 100);
            $vl_imp_serv_manual = $base_serv_ctxc_manual * ($porcImpServ / 100);
            $total_pos_impostos_manual = ($base_prod_ctxc_manual - $vl_imp_prod_manual) + ($base_serv_ctxc_manual - $vl_imp_serv_manual);
            $base_pos_adm_manual = $total_pos_impostos_manual - $valor_txadm;

            $ctx = [
                'bruto' => $valor,
                'porcProduto' => $porcProd_stxc,
                'porcServico' => $porcServ_stxc,
                'perc_tx_contrato' => ($valor > 0) ? (($valor_txcontrato / $valor) * 100) : 0,
                'perc_tx_adm' => ($total_pos_impostos_manual > 0) ? (($valor_txadm / $total_pos_impostos_manual) * 100) : 0,
                'perc_tx_ant' => ($base_pos_adm_manual > 0) ? (($valor_txantecipacao / $base_pos_adm_manual) * 100) : 0,
                'totalPorcProduto' => $porcImpProd,
                'totalPorcServico' => $porcImpServ,
            ];

            $calc = CalculoTaxasImpostosService::calcularPorContexto($ctx);

            $obj = new stdClass;
            $obj->vl_imp_prod = self::formatNumericForm($calc['vl_imp_prod'] ?? 0);
            $obj->vl_imp_serv = self::formatNumericForm($calc['vl_imp_serv'] ?? 0);
            // Em edição, o usuário pode digitar o valor da taxa contrato manualmente.
            // Para evitar diferença de centavos por "percentual invertido", usa o valor digitado.
            // No ContaPagarForm, o campo exibido "valor_liquido" representa o líquido final
            // (valor_total_liq_tx_conta), conforme cálculo do service.
            // $obj->valor_liquido = self::formatNumericForm($calc['valor_total_liq_tx_conta'] ?? 0);
            $obj->valor_total_liq_tx_conta = self::formatNumericForm($calc['valor_total_liq_tx_conta'] ?? 0);
            
            TForm::sendData(self::$formName, $obj, false, false);

        }
        catch (Exception $e) {
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onAtualizarTaxas($param){
        try{
            TTransaction::open(self::$database);

            $id = $param['id'] ?? $param['key'] ?? null;
            
            if(!$id){
                throw new Exception('Conta não identificada!');
            }
            
            $conta = new Conta($id);

            $entidadeConta = $conta->entidade_id ?: TSession::getValue('entidade');
            $unitConta = $conta->system_unit_id ?: TSession::getValue('idunit');

            $taxaspessoa = TaxasPessoa::where('pessoa_id', '=', $conta->pessoa_id)
                                        ->where('deleted_at', 'is', null)
                                        ->where('entidade_id', '=', $entidadeConta)
                                        ->where('system_unit_id', '=', $unitConta)
                                        ->first();

            if(!$taxaspessoa){
                // Sem cadastro de TaxasPessoa: usa as alíquotas já gravadas na conta
                // para não quebrar o recálculo do botão.
                $taxaspessoa = new stdClass;
                $taxaspessoa->ir = (float) ($conta->ir ?? 0);
                $taxaspessoa->csll = (float) ($conta->csll ?? 0);
                $taxaspessoa->cofins = (float) ($conta->cofins ?? 0);
                $taxaspessoa->pis = (float) ($conta->pis ?? 0);
                $taxaspessoa->ir_servico = (float) ($conta->ir_servico ?? 0);
                $taxaspessoa->csll_servico = (float) ($conta->csll_servico ?? 0);
                $taxaspessoa->cofins_servico = (float) ($conta->cofins_servico ?? 0);
                $taxaspessoa->pis_servico = (float) ($conta->pis_servico ?? 0);
                $taxaspessoa->iss_servico = (float) ($conta->iss_servico ?? 0);
                $taxaspessoa->txadm = (float) ($conta->txadm ?? 0);
                $taxaspessoa->txantecipacao = (float) ($conta->txantecipacao ?? 0);
            }

            $valorProd = (float) ($conta->valor_produto_s_desc_txc ?? 0);
            $valorServ = (float) ($conta->valor_servico_s_desc_txc ?? 0);
            $valorBrutoConta = (float) ($conta->valor ?? 0);

            $baseItens = self::obterBaseCalculoPorItens($conta);
            if ($baseItens) {
                $valorProd = (float) $baseItens['valorProd'];
                $valorServ = (float) $baseItens['valorServ'];
                $valorBrutoConta = CalculoTaxasImpostosService::money($valorProd + $valorServ);
            }

            // Corrige registros antigos/alterados em que o mix salvo não fecha com o valor bruto da conta.
            $somaMix = $valorProd + $valorServ;
            if ($valorBrutoConta > 0) {
                if ($somaMix <= 0) {
                    $valorProd = $valorBrutoConta;
                    $valorServ = 0;
                } elseif (abs($somaMix - $valorBrutoConta) > 0.01) {
                    $fator = $valorBrutoConta / $somaMix;
                    // Mantém soma EXATA igual ao bruto para não gerar diferença de centavos no service.
                    $valorProd = CalculoTaxasImpostosService::money($valorProd * $fator);
                    $valorServ = CalculoTaxasImpostosService::money($valorBrutoConta - $valorProd);
                }
            }

            $imp = CalculoTaxasImpostosService::montarContextoConta($conta, $valorProd, $valorServ, $taxaspessoa);
            $valorTxContratoConta = (float) ($conta->valor_txcontrato ?? 0);
            if ($baseItens) {
                $valorTxContratoConta = (float) $baseItens['valorDesconto'];
            }
            if (($imp['bruto'] ?? 0) > 0 && $valorTxContratoConta >= 0) {
                $imp['perc_tx_contrato'] = ($valorTxContratoConta / $imp['bruto']) * 100;
            }
            $imp['valor_txcontrato_fixado'] = CalculoTaxasImpostosService::money($valorTxContratoConta);
            $calc = CalculoTaxasImpostosService::calcularPorContexto($imp);

            $a = new stdClass();

            // $a->valor                = $imp['bruto'] ?? 0;
            $a->valor_txcontrato     = self::formatNumericForm($valorTxContratoConta);

            // No ContaPagarForm, exibe o líquido final calculado pelo service
            $a->valor_liquido        = self::formatNumericForm($calc['base_pos_txcontrato'] ?? 0);
            
            $a->ir = self::formatNumericForm($imp['impostos']['ir'] ?? 0);
            $a->csll = self::formatNumericForm($imp['impostos']['csll'] ?? 0);
            $a->cofins = self::formatNumericForm($imp['impostos']['cofins'] ?? 0);
            $a->pis = self::formatNumericForm($imp['impostos']['pis'] ?? 0);
            $a->porcProd = (float) ($imp['totalPorcProduto'] ?? 0);

            $a->ir_servico = self::formatNumericForm($imp['impostos']['ir_servico'] ?? 0);
            $a->csll_servico = self::formatNumericForm($imp['impostos']['csll_servico'] ?? 0);
            $a->cofins_servico = self::formatNumericForm($imp['impostos']['cofins_servico'] ?? 0);
            $a->pis_servico = self::formatNumericForm($imp['impostos']['pis_servico'] ?? 0);
            $a->iss_servico = self::formatNumericForm($imp['impostos']['iss_servico'] ?? 0);
            $a->porcServ = (float) ($imp['totalPorcServico'] ?? 0);

            $a->vl_imp_prod     = self::formatNumericForm($calc['vl_imp_prod'] ?? 0);
            $a->vl_imp_serv     = self::formatNumericForm($calc['vl_imp_serv'] ?? 0);

            $a->txadm                 = self::formatNumericForm($imp['perc_tx_adm'] ?? 0);
            $a->valor_txadm           = self::formatNumericForm($calc['valor_txadm'] ?? 0);

            $a->valor_txantecipacao   = self::formatNumericForm($calc['valor_txantecipacao'] ?? 0);

            $a->valor_total_liq_tx_conta = self::formatNumericForm($calc['valor_total_liq_tx_conta'] ?? 0);

            TTransaction::close();

            TForm::sendData(self::$formName, $a, false, false);

            TToast::show('success', 'Taxas atualizadas e valores recalculados. Resta apenas salvar!', 'topRight', 'far:check-circle');

        }
        catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
    /**
     * Clear form data
     * @param $param Request
     */
    // public function onClear( $param )
    // {
    //     $this->form->clear(true);

    // }

    public function onShow($param = null)
    {

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

    public  function onQuitarPagar($param = null) 
    {
        try 
        {
            $this->onEdit($param);

            $setEditableIfExists = function (string $fieldName, bool $editable): void {
                $field = $this->form->getField($fieldName);
                if ($field) {
                    $field->setEditable($editable);
                }
            };

            $setValueIfExists = function (string $fieldName, $value): void {
                $field = $this->form->getField($fieldName);
                if ($field) {
                    $field->setValue($value);
                }
            };

            $setEditableIfExists('pessoa_id', false);
            $setValueIfExists('categoria_id', 10);
            // $setEditableIfExists('categoria_id', false);
            $setEditableIfExists('valor', false);
            $setEditableIfExists('valor_txcontrato', false);
            $setEditableIfExists('valor_txadm', false);
            $setEditableIfExists('valor_txantecipacao', false);
            $setEditableIfExists('valor_liquido', false);
            $setEditableIfExists('dt_vencimento', false);
            $setEditableIfExists('dt_emissao', false);
            $setEditableIfExists('parcela', false);

            $setValueIfExists('dt_pagamento', date('d/m/Y'));

            $this->form->setFormTitle("Quitar conta a pagar");

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

}

