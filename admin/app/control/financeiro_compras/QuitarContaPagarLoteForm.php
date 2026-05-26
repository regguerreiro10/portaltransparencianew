<?php

use Adianti\Registry\TSession;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TNumeric;
include_once 'app/service/CalculoTaxasImpostosService.php';


class QuitarContaPagarLoteForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_QuitarContaPagarLoteForm';

    private static function money($value): float
    {
        return CalculoTaxasImpostosService::money((float) ($value ?? 0));
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
            $vltItem = self::money($vItem * $qtd);

            if ((int) ($item->tipo ?? 0) === 1) {
                $valorProd = self::money($valorProd + $vltItem);
            } elseif ((int) ($item->tipo ?? 0) === 2) {
                $valorServ = self::money($valorServ + $vltItem);
            }

            $valorDesconto = self::money($valorDesconto + ((float) ($item->perc_desconto ?? 0)));
        }

        return [
            'valorProd' => self::money($valorProd),
            'valorServ' => self::money($valorServ),
            'valorDesconto' => self::money($valorDesconto),
        ];
    }

    private static function calcularContaComoAtualizarTaxas(Conta $conta): array
    {
        $entidadeConta = $conta->entidade_id ?: TSession::getValue('entidade');
        $unitConta = $conta->system_unit_id ?: TSession::getValue('idunit');

        $taxaspessoa = TaxasPessoa::where('pessoa_id', '=', $conta->pessoa_id)
            ->where('deleted_at', 'is', null)
            ->where('entidade_id', '=', $entidadeConta)
            ->where('system_unit_id', '=', $unitConta)
            ->first();

        if(!$taxaspessoa){
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

        // No lote, prioriza sempre a base já gravada na conta para evitar divergência
        // quando há múltiplas propostas/itens por pedido.
        $baseItens = null;
        if (($valorProd + $valorServ) <= 0.0) {
            $baseItens = self::obterBaseCalculoPorItens($conta);
            if ($baseItens) {
                $valorProd = (float) $baseItens['valorProd'];
                $valorServ = (float) $baseItens['valorServ'];
                $valorBrutoConta = self::money($valorProd + $valorServ);
            }
        }

        $somaMix = $valorProd + $valorServ;
        if ($valorBrutoConta > 0) {
            if ($somaMix <= 0) {
                $valorProd = $valorBrutoConta;
                $valorServ = 0;
            } elseif (abs($somaMix - $valorBrutoConta) > 0.01) {
                $fator = $valorBrutoConta / $somaMix;
                $valorProd = self::money($valorProd * $fator);
                $valorServ = self::money($valorBrutoConta - $valorProd);
            }
        }

        $imp = CalculoTaxasImpostosService::montarContextoConta($conta, $valorProd, $valorServ, $taxaspessoa);

        // No lote, calcula o desconto contratual pela porcentagem vigente
        // (ex.: 30%), evitando herdar valor fixo antigo salvo na conta.
        $percTxContrato = (float) ($conta->txcontrato ?? TSession::getValue('taxacontrato') ?? 0);
        $imp['perc_tx_contrato'] = $percTxContrato;
        unset($imp['valor_txcontrato_fixado']);

        $calc = CalculoTaxasImpostosService::calcularPorContexto($imp);
        $valorTxContrato = self::money($calc['valor_txcontrato'] ?? 0);

        return [
            'valor' => (float) ($imp['bruto'] ?? $valorBrutoConta),
            'valor_txcontrato' => self::money($valorTxContrato),
            'valor_produto_s_desc_txc' => self::money($valorProd),
            'valor_servico_s_desc_txc' => self::money($valorServ),
            'valor_txadm' => self::money($calc['valor_txadm'] ?? 0),
            'valor_txantecipacao' => self::money($calc['valor_txantecipacao'] ?? 0),
            'vl_imp_prod' => self::money($calc['vl_imp_prod'] ?? 0),
            'vl_imp_serv' => self::money($calc['vl_imp_serv'] ?? 0),
            'valor_total_liq_tx_conta' => self::money($calc['valor_total_liq_tx_conta'] ?? 0),
            'valor_liquido' => self::money($calc['base_pos_txcontrato'] ?? 0),
        ];
    }

    private static function getSystemUnitIdFromSelectedContas(): ?int
    {
        $contasSelecionadas = TSession::getValue('ContaPagarListbuilder_datagrid_check');
        if (!$contasSelecionadas || !is_array($contasSelecionadas)) {
            return null;
        }

        foreach ($contasSelecionadas as $conta_id) {
            if (!is_numeric($conta_id)) {
                continue;
            }

            $conta = new Conta((int) $conta_id);
            if (!empty($conta->system_unit_id)) {
                return (int) $conta->system_unit_id;
            }
        }

        return null;
    }

    private static function getNextNumeroFaturaBySystemUnit(?int $system_unit_id): string
    {
        if (empty($system_unit_id)) {
            return '';
        }

        $faturas = Fatura::where('system_unit_id', '=', $system_unit_id)->load();
        $maxNumero = 0;

        if ($faturas) {
            foreach ($faturas as $fatura) {
                $numero = preg_replace('/\D/', '', (string) ($fatura->numero_fatura ?? ''));
                if ($numero === '') {
                    continue;
                }

                $maxNumero = max($maxNumero, (int) $numero);
            }
        }

        return (string) ($maxNumero + 1);
    }

    private static function normalizarIdsContas($contas): array
    {
        $ids = [];

        if ($contas && is_array($contas)) {
            foreach ($contas as $key => $value) {
                $id = is_numeric($value) ? $value : $key;
                if (is_numeric($id)) {
                    $ids[] = (int) $id;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    private static function getContasComPedidosNaoFinalizados(array $contasSelecionadas): array
    {
        $pendencias = [];
        $sistema = TSession::getValue('sistema');

        foreach ($contasSelecionadas as $conta_id) {
            if (!is_numeric($conta_id)) {
                continue;
            }

            $conta = new Conta((int) $conta_id);
            $pedidoId = null;
            $pedido = null;
            $tipoPedido = null;

            if ($sistema == 'compras') {
                $pedidoId = $conta->pedido_venda_id;
                $pedido = !empty($pedidoId) ? Pedido::find($pedidoId) : null;
                $tipoPedido = 'compras';
            } elseif ($sistema == 'frotas') {
                $pedidoId = $conta->pedido_frotas_id;
                $pedido = !empty($pedidoId) ? PedidoFrotas::find($pedidoId) : null;
                $tipoPedido = 'frotas';
            } else {
                continue;
            }

            if (!$pedido || empty($pedido->dt_finalizacao)) {
                $pendencias[] = [
                    'conta_id' => (int) $conta->id,
                    'pedido_id' => $pedidoId ?: 'nao informado',
                    'tipo_pedido' => $tipoPedido,
                    'fornecedor' => $conta->pessoa->nome ?? '',
                    'motivo' => $pedido ? 'pedido sem data de finalizacao' : 'pedido nao encontrado',
                ];
            }
        }

        return $pendencias;
    }

    private static function montarMensagemPedidosNaoFinalizados(array $pendencias): string
    {
        $linhas = [];

        foreach ($pendencias as $pendencia) {
            $fornecedor = !empty($pendencia['fornecedor']) ? ' - '.$pendencia['fornecedor'] : '';
            $linhas[] = 'Conta '.$pendencia['conta_id'].$fornecedor
                .' | Pedido '.$pendencia['pedido_id'].' ('.$pendencia['tipo_pedido'].')'
                .' | '.$pendencia['motivo'];
        }

        return 'Fatura bloqueada. Existem contas vinculadas a pedidos ainda nao finalizados:<br>'
            . implode('<br>', $linhas)
            . '<br><br>Finalize os pedidos antes de gerar a fatura.';
    }

    private static function formatDateToDisplay($value, string $format): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return (new DateTime($value))->format($format);
        } catch (Exception $e) {
            return $value;
        }
    }

    public static function onChange_numero_fatura($param = null)
    {
        try {
            TTransaction::open('minierp');
            $system_unit_id = self::getSystemUnitIdFromSelectedContas() ?: (int) TSession::getValue('idunit');
            $numero_fatura = self::getNextNumeroFaturaBySystemUnit($system_unit_id);
            TTransaction::close();

            $obj = new StdClass;
            $obj->numero_fatura = $numero_fatura;
            TForm::sendData(self::$formName, $obj);
        } catch (Exception $e) {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null)
    {
        parent::__construct();
       
 
       if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $faturaId = $param['fatura_id'] ?? $param['id'] ?? $param['key'] ?? null;
        $modoConsultaFatura = !empty($faturaId);
        $faturaConsulta = null;

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle($modoConsultaFatura ? "Consultar Fatura" : "Gerar Fatura");

        $criteria_forma_pagamento_id = new TCriteria();
        $criteria_contas = new TCriteria();
        $criteria_tipo_documentos_propostas_id = new TCriteria();

        $contasSelecionadas = [];
        if ($modoConsultaFatura) {
            $criteria_contas->add(new TFilter('fatura_id', '=', (int) $faturaId));
        } else {
            $contasSelecionadas = self::normalizarIdsContas(TSession::getValue('ContaPagarListbuilder_datagrid_check'));
            if ($contasSelecionadas) {
                $criteria_contas->add(new TFilter('id', 'in', $contasSelecionadas));
            }
        }

        if(!$modoConsultaFatura && !$contasSelecionadas)
        {
            new TMessage('info', 'Seleciona ao menos uma conta', new TAction(['ContaPagarList', 'onShow']));
            return true;
        } else {
            
              // pessoa_id
              TTransaction::open('minierp');
                
                if ($modoConsultaFatura) {
                    $faturaConsulta = new Fatura((int) $faturaId);
                    $contasFatura = Conta::where('fatura_id', '=', (int) $faturaId)->load();
                    $contasSelecionadas = [];

                    if ($contasFatura) {
                        foreach ($contasFatura as $contaFatura) {
                            $contasSelecionadas[] = (int) $contaFatura->id;
                        }
                    }

                    if (!$contasSelecionadas) {
                        TTransaction::close();
                        new TMessage('info', 'Nenhuma conta vinculada a esta fatura.', new TAction(['ContaPagarList', 'onShow']));
                        return true;
                    }
                }

                if (!$modoConsultaFatura) {
                    $pendenciasFinalizacao = self::getContasComPedidosNaoFinalizados($contasSelecionadas);

                    if ($pendenciasFinalizacao) {
                        TTransaction::close();
                        new TMessage('error', self::montarMensagemPedidosNaoFinalizados($pendenciasFinalizacao), new TAction(['ContaPagarList', 'onShow']));
                        return true;
                    }
                }

                $calcContas = [];
                if ($contasSelecionadas && is_array($contasSelecionadas)) {
                    $totalx      = 0;
                    $descontox   = 0;
                    // $totalgeralx = 0;
                    $totalprodutox=0;
                    $totalservicox=0;
                    $txadmx = 0;
                    $txantecipacaox = 0;
                    $imp_prodx = 0;
                    $imp_servx = 0;
                    $total_liq_tx_contax = 0;
                    foreach ($contasSelecionadas as $conta_id) {
                        if (is_numeric($conta_id)) { 
                            $conta = new Conta($conta_id);
                            if ($conta->pessoa_id) {
                                // if ($conta->dt_pagamento != null) {
                                //   new TMessage('error', 'A conta '.$conta->id.' do fornecedor '.$conta->pessoa->nome.' j est paga!', new TAction(['ContaPagarList', 'onShow']));
                                //   return true;
                                // }
                                 if (!$modoConsultaFatura && $conta->fatura_id != null) {
                                    TTransaction::close();
                                    new TMessage('error', 'A conta '.$conta->id.' do fornecedor '.$conta->pessoa->nome.' j est vinculada a uma fatura!', new TAction(['ContaPagarList', 'onShow']));
                                    return true;
                                }
                                
                            }
                            $calcConta = self::calcularContaComoAtualizarTaxas($conta);
                            $calcContas[(int) $conta->id] = $calcConta;

                            $totalx += (float) ($calcConta['valor'] ?? 0);
                            $descontox += (float) ($calcConta['valor_txcontrato'] ?? 0);

                            $totalprodutox += (float) ($calcConta['valor_produto_s_desc_txc'] ?? 0);
                            $totalservicox += (float) ($calcConta['valor_servico_s_desc_txc'] ?? 0);

                            $txadmx += (float) ($calcConta['valor_txadm'] ?? 0);
                            $txantecipacaox += (float) ($calcConta['valor_txantecipacao'] ?? 0);
                            $imp_prodx += (float) ($calcConta['vl_imp_prod'] ?? 0);
                            $imp_servx += (float) ($calcConta['vl_imp_serv'] ?? 0);

                            $total_liq_tx_contax += (float) ($calcConta['valor_total_liq_tx_conta'] ?? 0);
                            // $totalgeralx += (float) ($calcConta['valor_total_liq_tx_conta'] ?? 0);
          
                        }     
                    }
                    $totalx = $totalx;
                    $descontox = (($totalx * TSession::getValue('taxacontrato')) / 100);
                    $totalprodutox = round($totalprodutox, 2);
                    $totalservicox = round($totalservicox, 2);
                    $txadmx = round($txadmx, 2);
                    $txantecipacaox = round($txantecipacaox, 2);
                    $imp_prodx = round($imp_prodx, 2);
                    $imp_servx = round($imp_servx, 2);
                    $total_liq_tx_contax = round($total_liq_tx_contax, 2);
                    // $totalgeralx = round($totalgeralx, 2);
                } 
                TTransaction::close();

        }
        $calcContas = $calcContas ?? [];

        TTransaction::open('minierp');
        $systemUnitFatura = self::getSystemUnitIdFromSelectedContas() ?: (int) TSession::getValue('idunit');
        $numeroFaturaAutomatico = self::getNextNumeroFaturaBySystemUnit($systemUnitFatura);
        TTransaction::close();

        $primeiroDiaProximoMes = new DateTime('first day of next month 00:00');
        $primeiroDiaProximoMesBr = $primeiroDiaProximoMes->format('d/m/Y');
        $primeiroDiaProximoMesBrDateTime = $primeiroDiaProximoMes->format('d/m/Y H:i');

        $login = new LoginForm([]);
        $AlertMensagem = $login->onMensagem('QuitarContaPagarLoteForm');
        $TAlert = new TAlert('danger',$AlertMensagem); 

        $id = new TEntry('id');
        $data_emissao = new TDateTime('data_emissao');
        $data_emissao->setMask('dd/mm/yyyy hh:ii');
        $data_vencimento = new TDateTime('data_vencimento');
        $data_vencimento->setMask('dd/mm/yyyy hh:ii');
        $forma_pagamento_id = new TDBCombo('forma_pagamento_id', 'minierp', 'FormaPagamento', 'id', '{nome}','nome asc' , $criteria_forma_pagamento_id );
        $obs = new TText('obs');
        $total = new TNumeric('total', '2', ',', '.' );
        $desconto = new TNumeric('desconto', '2', ',', '.' );
        $total_com_desconto = new TNumeric('total_com_desconto', '2', ',', '.' );
        // $totalgeral = new TNumeric('totalgeral', '2', ',', '.' );
        $totalproduto = new TNumeric('totalproduto', '2', ',', '.' );
        $totalservico = new TNumeric('totalservico', '2', ',', '.' );
        $total_txadm = new TNumeric('total_txadm', '2', ',', '.' );
        $total_txantecipacao = new TNumeric('total_txantecipacao', '2', ',', '.' );
        $total_imp_prod = new TNumeric('total_imp_prod', '2', ',', '.' );
        $total_imp_serv = new TNumeric('total_imp_serv', '2', ',', '.' );
        $total_liq_tx_conta = new TNumeric('total_liq_tx_conta', '2', ',', '.' );
        $contas = new TCheckList('contas');
        $periodo_apuracao_inicial = new TDate('periodo_apuracao_inicial');
        $periodo_apuracao_final = new TDate('periodo_apuracao_final');
        $numero_fatura = new TEntry('numero_fatura');
        $tipo_documentos_propostas_id = new TDBSelect('tipo_documentos_propostas_id', 'minierp', 'TipoDocumentosPropostas', 'id', '{descricao}','id asc' , $criteria_tipo_documentos_propostas_id);

        // $contas->setHeight(200);
        // $contas->setWidth(300);

        // desabilita os checkboxes
        // $contas->setProperty('disabled', 'disabled');

        $forma_pagamento_id->enableSearch();
        $contas->setValue($contasSelecionadas ?: TSession::getValue('ContaPagarListbuilder_datagrid_check'));
        $numero_fatura->setValue($faturaConsulta->numero_fatura ?? $numeroFaturaAutomatico);
        $total->setValue($faturaConsulta->totalgeral ?? $totalx);
        $desconto->setValue($faturaConsulta->desconto ?? $descontox);
        $total_com_desconto->setValue($faturaConsulta->total ?? (($totalx ?? 0) - ($descontox ?? 0)));
        // $totalgeral->setValue($totalgeralx);
        $totalproduto->setValue($faturaConsulta->totalproduto ?? $totalprodutox);
        $totalservico->setValue($faturaConsulta->totalservico ?? $totalservicox);
        $total_txadm->setValue($txadmx ?? 0);
        $total_txantecipacao->setValue($txantecipacaox ?? 0);
        $total_imp_prod->setValue($imp_prodx ?? 0);
        $total_imp_serv->setValue($imp_servx ?? 0);
        $total_liq_tx_conta->setValue($total_liq_tx_contax ?? 0);
        $tipo_documentos_propostas_id->enableSearch();


        $obs->setSize('100%', 70);
        $forma_pagamento_id->setSize('100%');
        $data_emissao->setSize('100%');
        $data_emissao->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_emissao->setValue(self::formatDateToDisplay($faturaConsulta->data_emissao ?? null, 'd/m/Y H:i') ?? date('d/m/Y H:i'));
        $data_vencimento->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_vencimento->setValue(self::formatDateToDisplay($faturaConsulta->data_vencimento ?? null, 'd/m/Y H:i') ?? $primeiroDiaProximoMesBrDateTime);
        $periodo_apuracao_inicial->setMask('dd/mm/yyyy');
        $periodo_apuracao_final->setMask('dd/mm/yyyy');
        $numero_fatura->setSize('100%');
        $periodo_apuracao_inicial->setDatabaseMask('yyyy-mm-dd');
        $periodo_apuracao_final->setDatabaseMask('yyyy-mm-dd');
        $periodo_apuracao_inicial->setValue(self::formatDateToDisplay($faturaConsulta->periodo_apuracao_inicial ?? null, 'd/m/Y') ?? $primeiroDiaProximoMesBr);
        $periodo_apuracao_final->setValue(self::formatDateToDisplay($faturaConsulta->periodo_apuracao_final ?? null, 'd/m/Y') ?? $primeiroDiaProximoMesBr);
        $id->setValue($faturaConsulta->id ?? null);
        $forma_pagamento_id->setValue($faturaConsulta->forma_pagamento_id ?? null);
        $obs->setValue($faturaConsulta->obs ?? null);

        $id->setEditable(false);
        $id->setSize('100%');
        $total->setSize('100%');
        $desconto->setSize('100%');
        $total_com_desconto->setSize('100%');
        // $totalgeral->setSize('100%');
        $total->setEditable(false);
        $desconto->setEditable(false);
        $total_com_desconto->setEditable(false);
        // $totalgeral->setEditable(false);
        $totalproduto->setEditable(false);
        $totalservico->setEditable(false);
        $total_txadm->setEditable(false);
        $total_txantecipacao->setEditable(false);
        $total_imp_prod->setEditable(false);
        $total_imp_serv->setEditable(false);
        $total_liq_tx_conta->setEditable(false);
        $totalproduto->setSize('100%');
        $totalservico->setSize('100%');
        $total_txadm->setSize('100%');
        $total_txantecipacao->setSize('100%');
        $total_imp_prod->setSize('100%');
        $total_imp_serv->setSize('100%');
        $total_liq_tx_conta->setSize('100%');
        $numero_fatura->setSize('100%');
        $periodo_apuracao_inicial->setSize('100%');
        $periodo_apuracao_final->setSize('100%');
        $data_emissao->setSize('100%');
        $data_vencimento->setSize('100%');
        $tipo_documentos_propostas_id->setSize('100%');

        if ($modoConsultaFatura) {
            $data_emissao->setEditable(false);
            $data_vencimento->setEditable(false);
            $forma_pagamento_id->setEditable(false);
            $obs->setEditable(false);
            $periodo_apuracao_inicial->setEditable(false);
            $periodo_apuracao_final->setEditable(false);
            $numero_fatura->setEditable(false);
            $contas->setProperty('onclick', 'return false;');
        }

        
        $periodo_apuracao_inicial->addValidation("Periodo de apurao inicial", new TRequiredValidator()); 
        $periodo_apuracao_final->addValidation("Periodo de apurao final", new TRequiredValidator()); 
        $numero_fatura->addValidation("Nmero fatura", new TRequiredValidator()); 
        $data_emissao->addValidation("Data de emisso", new TRequiredValidator());
        $forma_pagamento_id->addValidation("Forma de pagamento", new TRequiredValidator());
        $contas->setIdColumn('id');

        $column_contas_id = $contas->addColumn('id', "Id", 'center' , '5%');
        if (TSession::getValue('sistema') == 'frotas') {
            $column_contas_idpedido = $contas->addColumn('pedido_frotas_id', "Id Pedido", 'center' , '5%');
        } elseif (TSession::getValue('sistema') == 'compras') {
            $column_contas_idpedido = $contas->addColumn('pedido_venda_id', "Id Pedido", 'center' , '5%');
        }
//        $column_contas_pessoa_id = $contas->addColumn('pessoa_id', "ID Pessoa", 'left' , '5%');
        $column_contas_pessoa_nome = $contas->addColumn('pessoa->nome', "Fornecedor", 'left' , '20%');
        $column_contas_dt_vencimento_transformed = $contas->addColumn('dt_vencimento', "Vencimento", 'center' , '5%');
        $column_contas_valor_transformed = $contas->addColumn('valor', "Total", 'center' , '10%');
        $column_contas_valor_txcontrato = $contas->addColumn('valor_txcontrato', "Vl TxContrato", 'center' , '10%');
        // $column_contas_parcela = $contas->addColumn('parcela', "Parcela", 'center' , '20%');
        $column_contas_valor_produto = $contas->addColumn('valor_produto_s_desc_txc', "Vl Produto", 'center' , '10%');
        $column_contas_valor_servico = $contas->addColumn('valor_servico_s_desc_txc', "Vl Servico", 'center' , '10%');
        $column_contas_valor_liquido = $contas->addColumn('valor_liquido', "Vl liquido", 'center' , '10%');
        $column_contas_valor_txadm = $contas->addColumn('valor_txadm', "Vl TxAdm", 'center' , '10%');
        $column_contas_valor_txantecipacao = $contas->addColumn('valor_txantecipacao', "Vl TxAntecipao", 'center' , '10%');
        $column_contas_vl_imp_prod = $contas->addColumn('vl_imp_prod', "Vl Imp Prod.", 'center' , '10%');
        $column_contas_vl_imp_serv = $contas->addColumn('vl_imp_serv', "Vl Imp Serv.", 'center' , '10%');
        $column_contas_valor_total_liq_tx_conta = $contas->addColumn('valor_total_liq_tx_conta', "Vl Total Lquido", 'center' , '10%');

        $column_contas_dt_vencimento_transformed->setTransformer(function($value, $object, $row) 
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_contas_valor_transformed->setTransformer(function($value, $object, $row) use ($calcContas)
        {
            if (isset($calcContas[(int) $object->id]['valor'])) {
                $value = $calcContas[(int) $object->id]['valor'];
            }
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });   

            $column_contas_valor_txcontrato->setTransformer(function($value, $object, $row) use ($calcContas)
        {
            if (isset($calcContas[(int) $object->id]['valor_txcontrato'])) {
                $value = $calcContas[(int) $object->id]['valor_txcontrato'];
            }
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });   

            $column_contas_valor_liquido->setTransformer(function($value, $object, $row) use ($calcContas)
        {
            if (isset($calcContas[(int) $object->id]['valor_liquido'])) {
                $value = $calcContas[(int) $object->id]['valor_liquido'];
            }
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });   

        $column_contas_valor_txadm->setTransformer(function($value, $object, $row) use ($calcContas)
        {
            if (isset($calcContas[(int) $object->id]['valor_txadm'])) {
                $value = $calcContas[(int) $object->id]['valor_txadm'];
            }
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_contas_valor_txantecipacao->setTransformer(function($value, $object, $row) use ($calcContas)
        {
            if (isset($calcContas[(int) $object->id]['valor_txantecipacao'])) {
                $value = $calcContas[(int) $object->id]['valor_txantecipacao'];
            }
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_contas_vl_imp_prod->setTransformer(function($value, $object, $row) use ($calcContas)
        {
            if (isset($calcContas[(int) $object->id]['vl_imp_prod'])) {
                $value = $calcContas[(int) $object->id]['vl_imp_prod'];
            }
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_contas_vl_imp_serv->setTransformer(function($value, $object, $row) use ($calcContas)
        {
            if (isset($calcContas[(int) $object->id]['vl_imp_serv'])) {
                $value = $calcContas[(int) $object->id]['vl_imp_serv'];
            }
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_contas_valor_total_liq_tx_conta->setTransformer(function($value, $object, $row) use ($calcContas)
        {
            if (isset($calcContas[(int) $object->id]['valor_total_liq_tx_conta'])) {
                $value = $calcContas[(int) $object->id]['valor_total_liq_tx_conta'];
            }
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });
        
         $column_contas_valor_servico->setTransformer(function($value, $object, $row) use ($calcContas)
        {
            if (isset($calcContas[(int) $object->id]['valor_servico_s_desc_txc'])) {
                $value = $calcContas[(int) $object->id]['valor_servico_s_desc_txc'];
            }
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });   

           $column_contas_valor_produto->setTransformer(function($value, $object, $row) use ($calcContas)
        {
            if (isset($calcContas[(int) $object->id]['valor_produto_s_desc_txc'])) {
                $value = $calcContas[(int) $object->id]['valor_produto_s_desc_txc'];
            }
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        }); 

        $contas->setHeight(250);
        $contas->makeScrollable();

          if (TSession::getValue('sistema') == 'frotas') {
                    $contas->fillWith('minierp', 'Conta', 'id', 'pedido_frotas_id desc' , $criteria_contas);

            } elseif (TSession::getValue('sistema') == 'compras') {
                    $contas->fillWith('minierp', 'Conta', 'id', 'pedido_venda_id desc' , $criteria_contas);

            }

        if ($modoConsultaFatura) {
            $contas->disableCheckAll();

            foreach ($contas->getFields() as $field) {
                $field->setEditable(false);
            }
        }


        $row1 = $this->form->addFields([new Tlabel("ID",null,'14px',null,'100%'),$id], [new Tlabel("Data de Emissão: *",'#ff0000','14px',null,'100%'),$data_emissao],[new TLabel("Forma de pagamento: *", '#ff0000', '14px', null, '100%'),$forma_pagamento_id]);
        $row1->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];
       
        $row01 = $this->form->addFields([new TLabel("Periodo de Apurção Inicial:*", '#ff0000', '14px', null, '100%'),$periodo_apuracao_inicial],[new TLabel("Periodo de Apuração Inicial:*", '#ff0000', '14px', null, '100%'),$periodo_apuracao_final],[new TLabel("Número Fatura:*", '#ff0000', '14px', null, '100%'),$numero_fatura]);
        $row01->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row02 = $this->form->addFields([new TLabel("Data de vencimento:", null, '14px', null, '100%'),$data_vencimento],[new TLabel("Total:", null, '14px', null, '100%'),$total],[new TLabel("Vl TxContrato:", null, '14px', null, '100%'),$desconto]);
        $row02->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row0 = $this->form->addFields(
            [new TLabel("Total de produtos:", null, '14px', null, '100%'),$totalproduto],
            [new TLabel("Total de serviços:", null, '14px', null, '100%'),$totalservico],
            [new TLabel("Total - Vl TxContrato:", null, '14px', null, '100%'),$total_com_desconto]
        );
        $row0->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row0b = $this->form->addFields(
            [new TLabel("Vl TxAdm:", null, '14px', null, '100%'),$total_txadm],
            [new TLabel("Vl TxAntecipao:", null, '14px', null, '100%'),$total_txantecipacao],
            [new TLabel("Vl Imp Prod.:", null, '14px', null, '100%'),$total_imp_prod],
            [new TLabel("Vl Imp Serv.:", null, '14px', null, '100%'),$total_imp_serv],
            [new TLabel("Vl Total Lquido:", null, '14px', null, '100%'),$total_liq_tx_conta]
        );
        $row0b->layout = [' col-sm-2',' col-sm-2',' col-sm-2',' col-sm-2',' col-sm-4'];

        $row02 = $this->form->addFields([new TLabel("Observao:", null, '14px', null, '100%'),$obs],[new TLabel("Tipo Documentos Proposta:", null, '14px', null, '100%'), $tipo_documentos_propostas_id]);
        $row02->layout = [' col-sm-8',' col-sm-4'];


        $row2 = $this->form->addFields([$contas]);
        $row2->layout = [' col-sm-12'];

        if (!$modoConsultaFatura) {
            // create the form actions
            $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
            $this->btn_onsave = $btn_onsave;
            $btn_onsave->addStyleClass('btn-primary');
        }

           // create the form actions
        $actionImprimir = new TAction([$this, 'onImprimirFatura']);
        $actionPdfNotas = new TAction([$this, 'onGerarPDFUnico']);
        if ($modoConsultaFatura) {
            $actionImprimir->setParameter('fatura_id', (int) $faturaId);
            $actionPdfNotas->setParameter('fatura_id', (int) $faturaId);
        }

        $btn_onimprimir = $this->form->addAction("Imprimir Fatura", $actionImprimir, 'fas:print #ffffff');
        $this->btn_onimprimir = $btn_onimprimir;
        $btn_onimprimir->addStyleClass('btn-danger'); 

        $btn_unir = $this->form->addAction("Gerar PDF Unico das Notas Fiscais", $actionPdfNotas, 'fas:file-pdf #ffffff');
        $btn_unir->addStyleClass('btn-success');


        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);
 if (!empty($AlertMensagem)) {
        parent::add($TAlert);
}
        parent::add($this->form);

        if ($modoConsultaFatura) {
            TCheckList::disableField('contas');
        }

        $style = new TStyle('right-panel > .container-part[page-name=QuitarContaPagarLoteForm]');
        $style->width = '80% !important';   
        $style->show(true);



    }

    public function onSave($param = null)
    {
        try {
            // Valida o form
            $this->form->validate();

            // Guarda os dados atuais do form numa sesso temporria
            $data = $this->form->getData();
            TSession::setValue('form_QuitarContaPagarLoteForm', $data);

            // Pergunta de confirmao
            new TQuestion('Confirma a gerao das faturas selecionadas?', 
                // Se SIM
                new TAction([__CLASS__, 'onSaveConfirm']),
                // Se NO
                null
            );
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Executa a quitao aps confirmao
     */
    public static function onSaveConfirm($param = null)
    {
        try {
            TTransaction::open('minierp');

            // Recupera dados do form salvos antes da pergunta
            $data = TSession::getValue('form_QuitarContaPagarLoteForm');
            if (!$data) {
                throw new Exception('No foi possvel recuperar os dados do formulrio.');
            }

            $contasSelecionadas = self::normalizarIdsContas(TSession::getValue('ContaPagarListbuilder_datagrid_check'));
            $pendenciasFinalizacao = self::getContasComPedidosNaoFinalizados($contasSelecionadas);

            if ($pendenciasFinalizacao) {
                throw new Exception(self::montarMensagemPedidosNaoFinalizados($pendenciasFinalizacao));
            }

            // Salva/atualiza a Fatura
            $object = new Fatura();
            $object->fromArray((array) $data);
            $faturaNova = empty($data->id);
            $object->system_unit_id = self::getSystemUnitIdFromSelectedContas() ?: TSession::getValue('idunit');
            $object->system_users_id = TSession::getValue('userid');
            $object->store();
            if ($faturaNova) {
                $data->id = $object->id;
            
            }

            //Quita as contas selecionadas
            if ($contasSelecionadas) {
                foreach ($contasSelecionadas as $conta_id) {
                    if (!is_numeric($conta_id)) {
                        continue;
                    }
                    $conta = new Conta((int) $conta_id);
                    $conta->obs                = $data->obs ?? null;
                    $conta->fatura_id          = $object->id; // Vinculafatura
                    $conta->store();
                }
            }

            TTransaction::close();

            // Limpa sesses temporrias
            TSession::setValue('ContaPagarListbuilder_datagrid_check', null);
            TSession::setValue('form_QuitarContaPagarLoteForm', null);

            // Mensagem + redirecionamento
            new TMessage('info', 'Fatura gerada com sucesso!', new TAction(['ContaPagarList', 'onShow']));
        }
        catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {               

    } 

    public function onShowFatura($param = null)
    {

    }

    public function onImprimirFatura($param = null) 
    {
        try   
        
        {
             $data = $this->form->getData(); // get form data as object
             $faturaImpressaoId = $data->id ?? $param['fatura_id'] ?? $param['id'] ?? $param['key'] ?? null;
             if (!empty($faturaImpressaoId)) {
                 $data->id = $faturaImpressaoId;
                 TTransaction::open('minierp');
                 $faturaImpressao = new Fatura((int) $faturaImpressaoId);
                 foreach ([
                     'numero_fatura',
                     'forma_pagamento_id',
                     'periodo_apuracao_inicial',
                     'periodo_apuracao_final',
                     'data_vencimento',
                     'obs',
                     'data_emissao',
                     'totalgeral',
                     'totalservico',
                     'totalproduto',
                     'desconto',
                     'total',
                 ] as $campoFatura) {
                     if (empty($data->{$campoFatura}) && isset($faturaImpressao->{$campoFatura})) {
                         $data->{$campoFatura} = $faturaImpressao->{$campoFatura};
                     }
                 }

                 $contasFatura = Conta::where('fatura_id', '=', (int) $faturaImpressaoId)->load();
                 $contasImpressao = [];

                 if ($contasFatura) {
                     foreach ($contasFatura as $contaFatura) {
                         $contasImpressao[] = (int) $contaFatura->id;
                     }
                 }

                 TTransaction::close();

                 if ($contasImpressao) {
                     $data->contas = $contasImpressao;
                     TSession::setValue('ContaPagarListbuilder_datagrid_check', $contasImpressao);
                 }
             }

             $printParam = array_merge((array) ($param ?? []), (array) $data);

             // Compatibilidade com templates/rotinas que ainda esperam totalgeral
             if (empty($printParam['totalgeral'])) {
                 $printParam['totalgeral'] = $printParam['total_liq_tx_conta'] ?? null;
             }

             // Mantem descricao de fatura customizavel via @param quando informado
             if (isset($printParam['descricaofatura'])) {
                 $printParam['descricaofatura'] = (string) $printParam['descricaofatura'];
             }

            if (TSession::getValue('sistema') == 'frotas') {
                include_once 'app/control/mfrotas/FaturaOrgao.php';
                $orcamento = new FaturaOrgao();
                $orcamento->gerar($printParam);
            } elseif (TSession::getValue('sistema') == 'compras') {
                include_once 'app/control/compras/FaturaOrgaoCompras.php';
                $orcamento = new FaturaOrgaoCompras();
                $orcamento->gerar($printParam);
            }
              $this->form->setData($data); // fill form data

        }
        catch (Exception $e) 
        {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onGerarPDFUnico($param = null)
    {
        $data = $this->form->getData();
        $faturaId = $data->id ?? $param['fatura_id'] ?? $param['id'] ?? $param['key'] ?? null;

        /* ==== RESOLVE CAMINHOS (root fora de /app) ==== */
        $dir = realpath(__DIR__);
        for ($i=0; $i<12 && $dir && basename($dir) !== 'app'; $i++) {
            $p = dirname($dir);
            if ($p === $dir) break;
            $dir = $p;
        }
        if (basename($dir) === 'app') {
            $baseApp  = $dir;
            $baseRoot = dirname($dir);
        } else {
            $baseApp  = realpath(dirname(__FILE__, 2));
            $baseRoot = dirname($baseApp);
        }
        if (basename($baseRoot) === 'app') { $baseRoot = dirname($baseRoot); }

        /* ==== LOGGER ==== */
        $resolveLogPath = function() use ($baseApp) {
            foreach ([$baseApp.'/tmp', $baseApp.'/log', sys_get_temp_dir()] as $d) {
                if (!is_dir($d)) @mkdir($d, 0777, true);
                if (is_dir($d) && is_writable($d)) return $d;
            }
            return null;
        };
        $logDir  = $resolveLogPath();
        $logFile = $logDir ? ($logDir.'/gerapdf_'.date('Ymd_His').'.log') : null;
        $LOG = function($m) use ($logFile){
            $l='['.date('H:i:s')."] $m\n";
            $logFile?@file_put_contents($logFile,$l,FILE_APPEND):error_log($l);
        };

        /* ==== HELPERS ==== */
        $isRealPdf = function($file) {
            if (!preg_match('/\.pdf$/i', $file)) return false;
            $fh = @fopen($file, 'rb');
            if (!$fh) return false;
            $chunk = @fread($fh, 1024);
            @fclose($fh);
            if ($chunk === false) return false;
            return (strpos($chunk, '%PDF-') !== false);
        };

        $ensureDir = function($dir) {
            if (!is_dir($dir)) {
                if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
                    throw new Exception('No foi possvel criar: '.$dir);
                }
            }
            if (!is_writable($dir)) throw new Exception('Sem permisso de escrita: '.$dir);
        };

        // acha comando no servidor
        $findCmd = function(array $cands) {
            $probe = stripos(PHP_OS_FAMILY,'Windows')!==false ? 'where' : 'which';
            foreach ($cands as $cmd) {
                @exec($probe.' '.escapeshellarg($cmd), $o, $r);
                if ($r===0 && !empty($o[0])) return $o[0];
                if (is_file($cmd) && is_executable($cmd)) return $cmd;
            }
            return null;
        };

        // normaliza PDF (resolve muito compression technique not supported)
        $normalizePdf = function($input, $tmpdir) use ($findCmd, $LOG) {
            $out = rtrim($tmpdir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.
                pathinfo($input, PATHINFO_FILENAME).'__norm_'.substr(md5($input.microtime(true)),0,8).'.pdf';

            // 1) Ghostscript
            $gs = $findCmd(['gs','/usr/bin/gs','/usr/local/bin/gs','gswin64c','gswin32c']);
            if ($gs) {
                $cmd = (stripos(PHP_OS_FAMILY,'Windows')!==false ? 'cmd /C ' : '') .
                    escapeshellarg($gs).' -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -dSAFER '.
                    '-sOutputFile='.escapeshellarg($out).' '.escapeshellarg($input);
                @exec($cmd.' 2>&1', $outLines, $ret);
                $LOG('GS: '.implode(' | ', (array)$outLines));
                if ($ret===0 && is_file($out) && filesize($out)>0) return $out;
            }

            // 2) qpdf
            $qpdf = $findCmd(['qpdf','/usr/bin/qpdf','/usr/local/bin/qpdf']);
            if ($qpdf) {
                $cmd = (stripos(PHP_OS_FAMILY,'Windows')!==false ? 'cmd /C ' : '') .
                    escapeshellarg($qpdf).' --linearize '.escapeshellarg($input).' '.escapeshellarg($out);
                @exec($cmd.' 2>&1', $outLines, $ret);
                $LOG('QPDF: '.implode(' | ', (array)$outLines));
                if ($ret===0 && is_file($out) && filesize($out)>0) return $out;
            }

            return null;
        };

        $findLibreOffice = function() {
            $cands = stripos(PHP_OS_FAMILY,'Windows')!==false
                ? ['soffice','libreoffice','C:\Program Files\LibreOffice\program\soffice.exe','C:\Program Files (x86)\LibreOffice\program\soffice.exe']
                : ['soffice','libreoffice','/usr/bin/soffice','/usr/local/bin/soffice'];
            foreach ($cands as $cmd) {
                $probe = stripos(PHP_OS_FAMILY,'Windows')!==false ? 'where' : 'which';
                @exec($probe.' '.escapeshellarg($cmd), $o, $r);
                if ($r===0 && !empty($o[0])) return $o[0];
                if (is_file($cmd) && is_executable($cmd)) return $cmd;
            }
            return null;
        };

        $convertWithLibre = function($input, $tmpdir) use ($findLibreOffice, $LOG) {
            $lo = $findLibreOffice();
            if (!$lo) { $LOG('LibreOffice no encontrado  pulando Office: '.$input); return null; }
            $cmd = (stripos(PHP_OS_FAMILY,'Windows')!==false ? 'cmd /C ' : '') .
                escapeshellarg($lo).' --headless --convert-to pdf --outdir '.escapeshellarg($tmpdir).' '.escapeshellarg($input);
            @exec($cmd.' 2>&1', $out, $ret);
            $LOG('LibreOffice: '.implode(' | ', (array)$out));
            if ($ret!==0) return null;
            $bn = pathinfo($input, PATHINFO_FILENAME).'.pdf';
            $outFile = rtrim($tmpdir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$bn;
            return is_file($outFile) ? $outFile : null;
        };

        // imagem/CSV/TXT iguais aos seus (mantive como voc j tinha)
        $imageToPdf = function($input, $outPdf) use ($LOG) {
            if (!class_exists('FPDF')) {
                if (is_file(__DIR__.'/../../lib/fpdf/fpdf.php')) require_once __DIR__.'/../../lib/fpdf/fpdf.php';
            }
            if (!class_exists('FPDF')) { $LOG('FPDF no disponvel p/ imagens: '.$input); return null; }

            $info = @getimagesize($input);
            if (!$info || empty($info[0]) || empty($info[1])) { $LOG('getimagesize falhou: '.$input); return null; }
            $ipw = (float)$info[0]; $iph = (float)$info[1];

            $W = 210.0; $H = 297.0; $m=10.0; $maxW = $W - 2*$m; $maxH = $H - 2*$m;
            $wmm = $ipw * 25.4 / 96.0; $hmm = $iph * 25.4 / 96.0;
            $scale = min($maxW / max($wmm,1e-9), $maxH / max($hmm,1e-9));
            $scale = min($scale, 1.0);

            $w = min($wmm * $scale, $maxW);
            $h = min($hmm * $scale, $maxH);
            $x = ($W - $w)/2.0; $y = ($H - $h)/2.0;

            $orient = ($w > $h) ? 'L' : 'P';
            $pdf = new \FPDF($orient, 'mm', 'A4');
            $pdf->SetAutoPageBreak(false);
            $pdf->AddPage();
            $pdf->Image($input, $x, $y, $w, $h);
            $pdf->Output($outPdf, 'F');
            return (is_file($outPdf) && filesize($outPdf)>0) ? $outPdf : null;
        };

        $csvToPdf = function($input, $outPdf) use ($LOG) {
            if (!class_exists('FPDF')) {
                if (is_file(__DIR__.'/../../lib/fpdf/fpdf.php')) require_once __DIR__.'/../../lib/fpdf/fpdf.php';
            }
            if (!class_exists('FPDF')) { $LOG('FPDF no disponvel p/ CSV: '.$input); return null; }

            $fh = @fopen($input,'r'); if (!$fh) return null;
            $first = fgets($fh);
            $delim = (substr_count($first, ',') > substr_count($first, ';')) ? ',' : ';';
            rewind($fh);

            $rows = [];
            $maxRows = 1000;
            while (($row = fgetcsv($fh, 0, $delim)) !== false && count($rows) < $maxRows) $rows[] = $row;
            fclose($fh);
            if (!$rows) $rows = [['(vazio)']];

            $cols = 0; foreach ($rows as $r) $cols = max($cols, count($r));
            $W = 297.0; $H = 210.0;
            $m=10.0; $usable = $W - 2*$m; $colW = $cols ? ($usable / $cols) : $usable;

            $pdf = new \FPDF('L','mm','A4');
            $pdf->SetAutoPageBreak(true, 12);
            $pdf->AddPage();
            $pdf->SetFont('Arial','',9);

            foreach ($rows as $r) {
                if ($pdf->GetY() > ($H - 20)) $pdf->AddPage();
                for ($i=0; $i<$cols; $i++) {
                    $txt = isset($r[$i]) ? (string)$r[$i] : '';
                    $pdf->Cell($colW, 6, mb_convert_encoding($txt, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
                }
                $pdf->Ln();
            }
            $pdf->Output($outPdf, 'F');
            return (is_file($outPdf) && filesize($outPdf)>0) ? $outPdf : null;
        };

        $txtToPdf = function($input, $outPdf) use ($LOG) {
            if (!class_exists('FPDF')) {
                if (is_file(__DIR__.'/../../lib/fpdf/fpdf.php')) require_once __DIR__.'/../../lib/fpdf/fpdf.php';
            }
            if (!class_exists('FPDF')) { $LOG('FPDF no disponvel p/ TXT: '.$input); return null; }

            $content = @file_get_contents($input);
            if ($content === false) $content = '(no foi possvel ler o arquivo)';

            $pdf = new \FPDF('P','mm','A4');
            $pdf->SetAutoPageBreak(true, 15);
            $pdf->AddPage();
            $pdf->SetFont('Courier','',10);

            $lines = preg_split("/\\r?\\n/", $content);
            $maxWidth = 180.0;
            foreach ($lines as $line) {
                $txt = mb_convert_encoding($line, 'ISO-8859-1', 'UTF-8');
                while ($pdf->GetStringWidth($txt) > $maxWidth) {
                    $n = strlen($txt);
                    $cut = (int)floor($n * ($maxWidth / max($pdf->GetStringWidth($txt),1)));
                    $pdf->Cell(0, 5, substr($txt, 0, max($cut,1)), 0, 1);
                    $txt = substr($txt, max($cut,1));
                }
                $pdf->Cell(0, 5, $txt, 0, 1);
            }
            $pdf->Output($outPdf, 'F');
            return (is_file($outPdf) && filesize($outPdf)>0) ? $outPdf : null;
        };

        try {
            @ini_set('memory_limit', '1024M');
            @set_time_limit(0);

            $nome    = 'notas'.rand(1,100000).'.pdf';
            $outDir  = $baseApp . '/output';
            $outFile = $outDir  . '/'.$nome;
            $ensureDir($outDir);

            /* ==== BUSCA DOS ARQUIVOS ==== */
            TTransaction::open('minierp'); $LOG('Transao aberta');
            $arquivos = [];
            $contas = TSession::getValue('ContaPagarListbuilder_datagrid_check');
            if (!empty($faturaId)) {
                $contasFatura = Conta::where('fatura_id', '=', (int) $faturaId)->load();
                $contas = [];

                if ($contasFatura) {
                    foreach ($contasFatura as $contaFatura) {
                        $contas[] = (int) $contaFatura->id;
                    }
                }
            }
            if (!$contas || !is_array($contas)) throw new Exception('Nenhuma conta selecionada.');

            foreach ($contas as $conta_id) {
                if (!is_numeric($conta_id)) continue;

                $conta = new Conta((int)$conta_id);
                $docs  = null;

                if (TSession::getValue('sistema') === 'frotas') {
                    $pedidoId = (int)$conta->pedido_frotas_id;
                    $subq = "(SELECT id FROM propostas WHERE pedido_frotas_id = {$pedidoId})";
                    if ($data->tipo_documentos_propostas_id) {
                        $docs = DocumentosPropostas::where('propostas_id','IN',$subq)
                            ->where('tipo_documentos_propostas_id', 'IN', $data->tipo_documentos_propostas_id)
                            ->load();
                        $LOG("Conta {$conta_id} (frotas) -> pedido {$pedidoId}");
                    } else {
                        $docs = DocumentosPropostas::where('propostas_id','IN',$subq)->load();
                    }
                } elseif (TSession::getValue('sistema') === 'compras') {
                    $pedidoId = (int)$conta->pedido_venda_id;
                    $subq = "(SELECT id FROM cotacao WHERE pedido_id = {$pedidoId})";
                    $docs = DocumentosCotacao::where('cotacao_id','IN',$subq)->load();
                    $LOG("Conta {$conta_id} (financeiro) -> fatura {$pedidoId}");
                }

                if ($docs) foreach ($docs as $doc) {
                    $raw = trim((string)($doc->caminho ?? ''), " \t\n\r\0\x0B\"'");
                    $paths = preg_split('/\s*,\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY);

                    foreach ($paths as $path) {
                        $path = trim($path, " \t\n\r\0\x0B\"'");
                        $isAbsWin = preg_match('/^[A-Za-z]:[\/\\\\]/', $path);
                        $isAbsNix = (substr($path, 0, 1) === '/');

                        if (!$isAbsWin && !$isAbsNix) {
                            $rel  = ltrim(str_replace(['\\','/'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
                            $path = $baseRoot . DIRECTORY_SEPARATOR . $rel;
                        }

                        if (!is_file($path)) { $LOG("IGNORADO (no existe): $path"); continue; }

                        $arquivos[] = $path;
                        $LOG("ARQ: $path");
                    }
                }
            }
            TTransaction::close(); $LOG('Transao fechada');

            if (empty($arquivos)) throw new Exception('Nenhum arquivo localizado.');

            /* ==== CONVERSES -> PDF ==== */
            $imgExts  = ['jpg','jpeg','png'];
            $csvExts  = ['csv'];
            $txtExts  = ['txt'];
            $offExts  = ['doc','docx','xls','xlsx','ppt','pptx','odt','ods','rtf'];

            $toMerge = [];
            $tmpFiles = [];
            $tmpdir = $logDir ? ($logDir.'/conv_'.uniqid()) : (sys_get_temp_dir().'/conv_'.uniqid());
            $ensureDir($tmpdir);

            foreach ($arquivos as $src) {
                $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
                if ($ext === 'pdf' && $isRealPdf($src)) { $toMerge[] = $src; continue; }

                $outPdf = $tmpdir . DIRECTORY_SEPARATOR . pathinfo($src, PATHINFO_FILENAME) . '.pdf';
                $ok = null;

                if (in_array($ext, $imgExts, true)) {
                    $LOG("Convertendo imagem -> PDF: $src");
                    $ok = $imageToPdf($src, $outPdf);
                } elseif (in_array($ext, $csvExts, true)) {
                    $LOG("Convertendo CSV -> PDF: $src");
                    $ok = $csvToPdf($src, $outPdf);
                } elseif (in_array($ext, $txtExts, true)) {
                    $LOG("Convertendo TXT -> PDF: $src");
                    $ok = $txtToPdf($src, $outPdf);
                } elseif (in_array($ext, $offExts, true)) {
                    $LOG("Tentando LibreOffice -> PDF: $src");
                    $ok = $convertWithLibre($src, $tmpdir);
                } else {
                    $LOG("Formato no suportado: $src");
                }

                if ($ok && is_file($outPdf) && $isRealPdf($outPdf)) {
                    $toMerge[] = $outPdf; $tmpFiles[] = $outPdf;
                } elseif ($ok && is_string($ok) && is_file($ok) && $isRealPdf($ok)) {
                    $toMerge[] = $ok; $tmpFiles[] = $ok;
                } else {
                    $LOG("Falha na converso: $src");
                }
            }

            $toMerge = array_values(array_unique(array_filter($toMerge, $isRealPdf)));
            if (empty($toMerge)) throw new Exception('Nenhum PDF vlido aps converso.');

            /* ==== FPDI ==== */
            $autoloads = [$baseRoot.'/vendor/autoload.php', $baseApp.'/vendor/autoload.php'];
            $autoloadLoaded = false;
            foreach ($autoloads as $a) if (is_file($a)) { require_once $a; $autoloadLoaded = true; $LOG('Autoload: '.$a); break; }

            if (!$autoloadLoaded) {
                if (is_file($baseApp.'/lib/fpdf/fpdf.php')) require_once $baseApp.'/lib/fpdf/fpdf.php';
                elseif (is_file($baseApp.'/lib/fpdf182/fpdf.php')) require_once $baseApp.'/lib/fpdf182/fpdf.php';
                else throw new Exception('FPDF no encontrado.');

                if (is_file($baseApp.'/lib/fpdi/src/autoload.php')) require_once $baseApp.'/lib/fpdi/src/autoload.php';
                else throw new Exception('FPDI no encontrado.');
            }

            $pdf = new \setasign\Fpdi\Fpdi('P', 'mm', 'A4');
            $pdf->SetAutoPageBreak(false);

            $totalPag = 0; $erros = [];

            foreach ($toMerge as $src) {
                $useSrc = $src;

                // 1) tenta abrir normal
                try {
                    $count = $pdf->setSourceFile($useSrc);
                    $LOG("Abrindo $useSrc -> {$count} pginas");
                } catch (\Throwable $e) {

                    $LOG("Falhou setSourceFile: $useSrc | ".$e->getMessage());

                    // 2) tenta normalizar (GS/QPDF) e abrir de novo
                    $norm = $normalizePdf($useSrc, $tmpdir);
                    if ($norm && is_file($norm) && $isRealPdf($norm)) {
                        $LOG("Normalizado: $useSrc -> $norm");
                        $tmpFiles[] = $norm; // limpa depois
                        $useSrc = $norm;

                        try {
                            $count = $pdf->setSourceFile($useSrc);
                            $LOG("Abrindo (norm) $useSrc -> {$count} pginas");
                        } catch (\Throwable $e2) {
                            $msg = "ERRO setSourceFile em $src (mesmo aps normalizar): ".$e2->getMessage();
                            $LOG($msg); $erros[] = $msg;
                            continue;
                        }
                    } else {
                        $msg = "ERRO setSourceFile em $src (sem normalizao disponvel): ".$e->getMessage();
                        $LOG($msg); $erros[] = $msg;
                        continue;
                    }
                }

                if ($count <= 0) { $LOG("Sem pginas em $useSrc"); continue; }

                for ($n=1; $n<=$count; $n++) {
                    try {
                        $tpl  = $pdf->importPage($n);
                        $size = $pdf->getTemplateSize($tpl);

                        $w = (float)($size['width']  ?? ($size['w']  ?? 210));
                        $h = (float)($size['height'] ?? ($size['h'] ?? 297));
                        if ($w <= 0 || $h <= 0) { $w = 210.0; $h = 297.0; }

                        $orient = ($w > $h) ? 'L' : 'P';
                        $pdf->AddPage($orient, [$w, $h]);
                        $pdf->useTemplate($tpl);
                        $totalPag++;
                    } catch (\Throwable $e) {
                        $msg = "ERRO importar/usar pgina {$n} de $useSrc: ".$e->getMessage();
                        $LOG($msg); $erros[] = $msg;
                    }
                }
            }

            if ($totalPag === 0) {
                $primeiro = $erros ? $erros[0] : 'Nenhuma pgina importada.';
                throw new Exception($primeiro . ($logFile ? " | Log: ".basename($logFile) : ''));
            }

            $pdf->Output($outFile, 'F');
            if (!is_file($outFile) || filesize($outFile) === 0) throw new Exception('Falha ao gravar o PDF final em: '.$outFile);

            // Limpa temporrios
            foreach (array_unique($tmpFiles) as $t) @unlink($t);
            @rmdir($tmpdir);

           $pdfHref = 'app/output/'.$nome;

            $msg = 'PDF unificado gerado: <b>'.htmlspecialchars($nome).'</b><br>Total de pginas: '.$totalPag;
            if ($erros)  $msg .= '<br><small>Alguns arquivos/pginas foram ignorados.</small>';

            $msg .= '<br><br><a href="'.htmlspecialchars($pdfHref).'" target="_blank"
                        style="display:inline-block;padding:8px 12px;background:#2d6cdf;color:#fff;border-radius:6px;text-decoration:none;">
                         Abrir PDF mesclado
                    </a>';

            $LOG("PDF FINAL: {$outFile} | URL: {$pdfHref}");
            
            if ($logFile) {
                $a = str_replace('\\','/',$logFile);
                $b = str_replace('\\','/',$baseApp);
                $logHref = null;
                if (strpos($a, $b . '/') === 0) {
                    $rel = substr($a, strlen($b) + 1);
                    $logHref = 'app/' . $rel;
                }
                $msg .= '<br><small>Log: '.
                    ($logHref ? '<a href="'.htmlspecialchars($logHref).'" target="_blank">'.htmlspecialchars(basename($logFile)).'</a>'
                            : htmlspecialchars(basename($logFile)).' (fora de /app)').
                    '</small>';
            }

            $this->form->setData($data);

            try {
                TPage::openFile('app/output/'.$nome);
                new TMessage('info', $msg);
            } catch (\Throwable $e) {
                $LOG('Falha openFile: '.$e->getMessage());
                new TMessage('info', $msg.'<br><a href="'.('app/output/'.$nome).'" target="_blank"><b>Abrir PDF</b></a>');
            }

        } catch (Exception $e) {
            try { TTransaction::rollback(); } catch (\Throwable $ignore) {}
            if ($logFile) @file_put_contents($logFile, '['.date('H:i:s')."] EXCEPTION: ".$e->getMessage()."\n", FILE_APPEND);
            new TMessage('error', $e->getMessage() . ($logFile?'<br><small>Log: '.htmlspecialchars(basename($logFile)).'</small>':''));
        }
    }


}



