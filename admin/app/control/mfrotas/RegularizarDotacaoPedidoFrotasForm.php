<?php

class RegularizarDotacaoPedidoFrotasForm extends TPage
{
    private $form;
    private $fieldList;
    private static $database = 'minierp';
    private static $formName = 'form_RegularizarDotacaoPedidoFrotasForm';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Dotaçao/empenho do pedido');

        $criteriaSaldo = new TCriteria();
        $criteriaSaldo->add(new TFilter('saldo_entidade_contrato_id', 'in', "(SELECT id FROM saldo_entidade_contrato WHERE deleted_at is null AND entidade_id = '" . TSession::getValue('entidade') . "')"));
        $criteriaSaldo->add(new TFilter('departamento_unit_id', 'in', "(SELECT id FROM departamento_unit WHERE system_unit_id = " . (int) TSession::getValue('idunit') . ")"));
        $criteriaSaldo->add(new TFilter('status_saldo_departamento_id', '<>', StatusSaldoDepartamento::ANULADO));

        $id = new TEntry('id');
        $estado_pedido = new TEntry('estado_pedido');
        $valor_liquido_proposta = new TNumeric('valor_liquido_proposta', 2, ',', '.');
        $valor_proposta_aprovada = new TNumeric('valor_proposta_aprovada', 2, ',', '.');
        $total_dotacoes = new TNumeric('total_dotacoes', 2, ',', '.');
        $corrigir_valor_pedido = new TCheckButton('corrigir_valor_pedido');
        $justificativa = new TText('justificativa');

        $id->setEditable(false);
        $estado_pedido->setEditable(false);
        $valor_liquido_proposta->setEditable(false);
        $valor_proposta_aprovada->setEditable(false);
        $total_dotacoes->setEditable(false);
        $corrigir_valor_pedido->setUseSwitch(true, 'blue');
        $corrigir_valor_pedido->setIndexValue('1');
        $corrigir_valor_pedido->setInactiveIndexValue('2');
        $corrigir_valor_pedido->setValue('2');
        $justificativa->addValidation('Justificativa', new TRequiredValidator());

        $id->setSize('100%');
        $estado_pedido->setSize('100%');
        $valor_liquido_proposta->setSize('100%');
        $valor_proposta_aprovada->setSize('100%');
        $total_dotacoes->setSize('100%');
        $corrigir_valor_pedido->setSize('100%');
        $justificativa->setSize('100%', 80);

        $dotacao_id = new THidden('dotacao_id[]');
        $saldo_departamento_id = new TDBCombo('saldo_departamento_id[]', self::$database, 'SaldoDepartamento', 'id', '{departamento_unit->name} - {numero_documento_empenho} - {valor_empenho_formatado} - {tipos} - {status_saldo_departamento}', 'numero_documento_empenho asc', $criteriaSaldo);
        $saldo_atual = new TNumeric('saldo_atual[]', 2, ',', '.');
        $valor = new TNumeric('valor[]', 2, ',', '.');

        $saldo_departamento_id->enableSearch();
        $saldo_departamento_id->setChangeAction(new TAction([self::class, 'onCalcValor']));
        $saldo_departamento_id->setSize('100%');
        $saldo_atual->setEditable(false);
        $saldo_atual->setSize('100%');
        $valor->setSize('100%');

        $this->fieldList = new TFieldList();
        $this->fieldList->width = '100%';
        $this->fieldList->name = 'regularizar_dotacoes_list';
        $this->fieldList->addField(null, $dotacao_id, []);
        $this->fieldList->addField(new TLabel('Empenho *', '#FF0000', '14px', null), $saldo_departamento_id, ['width' => '55%']);
        $this->fieldList->addField(new TLabel('Saldo atual', null, '14px', null), $saldo_atual, ['width' => '20%']);
        $this->fieldList->addField(new TLabel('Valor *', '#FF0000', '14px', null), $valor, ['width' => '25%', 'sum' => true, 'totalFormField' => 'total_dotacoes']);

        $this->form->addField($dotacao_id);
        $this->form->addField($saldo_departamento_id);
        $this->form->addField($saldo_atual);
        $this->form->addField($valor);

        $row1 = $this->form->addFields(
            [new TLabel('Pedido', null, '14px', null), $id],
            [new TLabel('Estado', null, '14px', null), $estado_pedido],
            [new TLabel('Valor do pedido', null, '14px', null), $valor_liquido_proposta]
        );
        $row1->layout = ['col-sm-2', 'col-sm-4', 'col-sm-3'];

        $row2 = $this->form->addFields(
            [new TLabel('Valor proposta aprovada', null, '14px', null), $valor_proposta_aprovada],
            [new TLabel('Total dotacoes', null, '14px', null), $total_dotacoes],
            [new TLabel('Corrigir valor do pedido pela proposta?', null, '14px', null), $corrigir_valor_pedido]
        );
        $row2->layout = ['col-sm-3', 'col-sm-3', 'col-sm-4'];

        $this->form->addContent([new TFormSeparator('Dotacoes / empenhos', '#333', '18', '#eee')]);
        $this->form->addContent([$this->fieldList]);
        $this->form->addFields([new TLabel('Justificativa *', '#FF0000', '14px', null), $justificativa]);

        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff')->addStyleClass('btn-primary');
        $this->form->addAction('Voltar', new TAction(['PedidoFrotasList', 'onShow']), 'fas:arrow-left #000000');

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        parent::add($container);
    }

    public function onEdit($param)
    {
        try {
            $this->checkPermission();
            TTransaction::open(self::$database);

            $pedidoId = (int) ($param['id'] ?? $param['key'] ?? 0);
            if ($pedidoId <= 0) {
                throw new Exception('Pedido nao informado.');
            }

            $pedido = new PedidoFrotas($pedidoId);
            $this->validarEstadoPermitido($pedido);

            $proposta = $this->getPropostaReferencia($pedido);
            $dotacoes = DotacaoPedidoFrotas::where('pedido_frotas_id', '=', $pedido->id)
                ->where('deleted_at', 'is', NULL)
                ->load();

            $data = new stdClass();
            $data->id = $pedido->id;
            $data->estado_pedido = $pedido->estado_pedido_frotas->nome ?? $pedido->estado_pedido_frotas_id;
            $data->valor_liquido_proposta = $pedido->valor_liquido_proposta;
            $data->valor_proposta_aprovada = $proposta ? $this->getValorProposta($proposta) : 0;
            $data->total_dotacoes = $this->sumDotacoes($dotacoes);
            $data->corrigir_valor_pedido = '2';
            $data->justificativa = $this->getJustificativaAprovacao($pedido);

            $this->form->setData($data);
            $this->fieldList->addHeader();

            if ($dotacoes) {
                foreach ($dotacoes as $dotacao) {
                    $item = new stdClass();
                    $item->dotacao_id = $dotacao->id;
                    $item->saldo_departamento_id = $dotacao->saldo_departamento_id;
                    $item->saldo_atual = $dotacao->saldo_atual;
                    $item->valor = $dotacao->valor;
                    $this->fieldList->addDetail($item);
                }
            } else {
                $this->fieldList->addDetail(new stdClass());
            }

            $this->fieldList->addCloneAction(null, 'fas:plus #69aa46', 'Adicionar');

            TTransaction::close();
        } catch (Exception $e) {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {
        $this->fieldList->addHeader();
        $this->fieldList->addDetail(new stdClass());
        $this->fieldList->addCloneAction(null, 'fas:plus #69aa46', 'Adicionar');
    }

    public function onSave($param = null)
    {
        $data = null;

        try {
            $this->checkPermission();
            TTransaction::open(self::$database);

            $data = $this->form->getData();
            $this->form->validate();
            $pedidoId = (int) ($data->id ?? 0);
            if ($pedidoId <= 0) {
                throw new Exception('Pedido nao informado.');
            }

            $pedido = new PedidoFrotas($pedidoId);
            $this->validarEstadoPermitido($pedido);

            $proposta = $this->getPropostaReferencia($pedido);
            if (!$proposta) {
                throw new Exception('Nao foi encontrada proposta aprovada/valida para conferir o valor do pedido.');
            }

            $valorPedidoAtual = round((float) ($pedido->valor_liquido_proposta ?? 0), 2);
            $valorProposta = round($this->getValorProposta($proposta), 2);
            $corrigirValorPedido = (string) ($data->corrigir_valor_pedido ?? '2') === '1';

            if (abs($valorPedidoAtual - $valorProposta) > 0.01 && !$corrigirValorPedido) {
                throw new Exception(
                    sprintf(
                        'O valor liquido do pedido diverge da proposta aprovada. Pedido: R$ %s. Proposta: R$ %s. Marque a opcao de correcao ou ajuste antes de salvar.',
                        number_format($valorPedidoAtual, 2, ',', '.'),
                        number_format($valorProposta, 2, ',', '.')
                    )
                );
            }

            $valorReferencia = $corrigirValorPedido ? $valorProposta : $valorPedidoAtual;
            $linhas = $this->normalizarDotacoesPostadas($param, $pedido->id);
            $totalDotacoes = round(array_sum(array_column($linhas, 'valor')), 2);

            if (abs($totalDotacoes - $valorReferencia) > 0.01) {
                throw new Exception(
                    sprintf(
                        'A soma das dotacoes deve ser igual ao valor do pedido. Valor referencia: R$ %s. Total informado: R$ %s.',
                        number_format($valorReferencia, 2, ',', '.'),
                        number_format($totalDotacoes, 2, ',', '.')
                    )
                );
            }

            $this->validarSaldoDasDotacoes($linhas, $pedido->id);

            if ($corrigirValorPedido) {
                $pedido->valor_total = $proposta->valor_total;
                $pedido->valor_total_proposta = $proposta->valor_total;
                $pedido->valor_desconto_proposta = $proposta->valor_desconto;
                $pedido->valor_liquido_proposta = $valorProposta;
                $pedido->store();
            }

            $saldoIdsParaRecalcular = $this->getSaldoIdsDotacoesAtivasPedido($pedido->id);
            foreach ($linhas as $linha) {
                $saldoId = (int) ($linha['saldo_departamento_id'] ?? 0);
                if ($saldoId > 0) {
                    $saldoIdsParaRecalcular[$saldoId] = $saldoId;
                }
            }

            $this->salvarDotacoes($pedido, $proposta, $linhas);
            $this->atualizarStatusSaldosUtilizados($saldoIdsParaRecalcular);
            $this->garantirHistoricoAprovacao($pedido, (string) $data->justificativa);
            $this->registrarHistoricoRegularizacao($pedido, (string) $data->justificativa, $totalDotacoes, $corrigirValorPedido);

            TTransaction::close();

            TToast::show('success', 'Dotacao/empenho regularizado com sucesso.', 'topRight', 'far:check-circle');
            TApplication::loadPage('PedidoFrotasList', 'onShow');
        } catch (Exception $e) {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }

            if (!$data) {
                $data = $this->form->getData();
            }

            $this->form->setData($data);
            $this->rebuildFieldListFromPost($param ?? []);

            new TMessage('error', $e->getMessage());
        }
    }

    public static function onCalcValor($param = null)
    {
        try {
            TTransaction::open(self::$database);

            $fieldId = $param['_field_id'] ?? null;
            $fieldDataJson = $param['_field_data_json'] ?? null;
            $fieldData = $fieldDataJson ? json_decode($fieldDataJson) : null;

            if (!isset($fieldData->{'row'})) {
                TTransaction::close();
                return;
            }

            $row = (int) $fieldData->{'row'};
            $saldoId = (int) ($param['_field_value'] ?? 0);
            if ($saldoId <= 0) {
                $saldoId = (int) ($param['saldo_departamento_id'][$row] ?? 0);
            }

            if ($saldoId <= 0) {
                TTransaction::close();
                return;
            }

            self::validarUsoSaldoDepartamentoId($saldoId);

            $pedidoId = (int) ($param['id'] ?? 0);
            $saldoAtual = self::getSaldoDisponivelDepartamentoStatic($saldoId, $pedidoId);
            $saldoFormatado = number_format($saldoAtual, 2, ',', '.');

            $saldoJs = json_encode($saldoFormatado);
            $rowJs = json_encode($row);
            $fieldIdJs = json_encode((string) $fieldId);

            TScript::create("
                (function() {
                    var saldo = {$saldoJs};
                    var row = {$rowJs};
                    var fieldId = {$fieldIdJs};
                    var fieldName = 'saldo_atual[]';
                    var \$field = $('[name=\"' + fieldName + '\"]').eq(row);

                    if (!\$field.length && fieldId) {
                        \$field = $('#' + fieldId).closest('tr, .row, .form-group').find('[name=\"' + fieldName + '\"]').first();
                    }

                    if (\$field.length) {
                        \$field.val(saldo).trigger('change').trigger('blur');
                    }
                })();
            ");

            TTransaction::close();
        } catch (Exception $e) {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }

            new TMessage('error', $e->getMessage());
        }
    }

    private function normalizarDotacoesPostadas($param, int $pedidoId): array
    {
        $saldoIds = $param['saldo_departamento_id'] ?? [];
        $valores = $param['valor'] ?? [];
        $ids = $param['dotacao_id'] ?? [];

        $linhas = [];
        foreach ($saldoIds as $index => $saldoId) {
            $saldoId = (int) $saldoId;
            $valor = $this->toFloat($valores[$index] ?? 0);
            $dotacaoId = (int) ($ids[$index] ?? 0);

            if ($saldoId <= 0 && $valor <= 0) {
                continue;
            }

            if ($saldoId <= 0) {
                throw new Exception('Informe o empenho em todas as linhas de dotacao.');
            }

            if ($valor <= 0) {
                throw new Exception('Informe valor maior que zero em todas as linhas de dotacao.');
            }

            $linhas[] = [
                'id' => $dotacaoId,
                'pedido_frotas_id' => $pedidoId,
                'saldo_departamento_id' => $saldoId,
                'valor' => round($valor, 2),
            ];
        }

        if (empty($linhas)) {
            throw new Exception('Informe ao menos uma dotacao/empenho para o pedido.');
        }

        return $linhas;
    }

    private function rebuildFieldListFromPost(array $param): void
    {
        $saldoIds = $param['saldo_departamento_id'] ?? [];
        $valores = $param['valor'] ?? [];
        $ids = $param['dotacao_id'] ?? [];
        $saldosAtuais = $param['saldo_atual'] ?? [];

        $this->fieldList->addHeader();

        $hasDetail = false;
        foreach ($saldoIds as $index => $saldoId) {
            $item = new stdClass();
            $item->dotacao_id = $ids[$index] ?? null;
            $item->saldo_departamento_id = $saldoId;
            $item->saldo_atual = isset($saldosAtuais[$index]) ? $this->toFloat($saldosAtuais[$index]) : null;
            $item->valor = isset($valores[$index]) ? $this->toFloat($valores[$index]) : null;

            if (empty($item->saldo_departamento_id) && empty($item->valor) && empty($item->dotacao_id)) {
                continue;
            }

            $this->fieldList->addDetail($item);
            $hasDetail = true;
        }

        if (!$hasDetail) {
            $this->fieldList->addDetail(new stdClass());
        }

        $this->fieldList->addCloneAction(null, 'fas:plus #69aa46', 'Adicionar');
    }

    private function validarSaldoDasDotacoes(array $linhas, int $pedidoId): void
    {
        $totaisPorSaldo = [];
        $saldosJaUsadosNoPedido = DotacaoPedidoFrotas::where('pedido_frotas_id', '=', $pedidoId)
            ->where('deleted_at', 'is', NULL)
            ->getIndexedArray('saldo_departamento_id', 'saldo_departamento_id');

        foreach ($linhas as $linha) {
            $saldoId = (int) $linha['saldo_departamento_id'];
            $saldo = new SaldoDepartamento($saldoId);
            $statusId = (string) $saldo->status_saldo_departamento_id;

            $this->validarOrgaoAtivoSaldoDepartamento($saldo);

            if ($statusId === (string) StatusSaldoDepartamento::ANULADO) {
                throw new Exception("Nao e permitido usar empenho anulado ({$saldo->numero_documento_empenho}).");
            }

            if ($statusId === (string) StatusSaldoDepartamento::ENCERRADO && empty($saldosJaUsadosNoPedido[$saldoId])) {
                throw new Exception("Nao e permitido incluir novo empenho encerrado ({$saldo->numero_documento_empenho}).");
            }

            if (!isset($totaisPorSaldo[$saldoId])) {
                $totaisPorSaldo[$saldoId] = 0.0;
            }
            $totaisPorSaldo[$saldoId] += (float) $linha['valor'];
        }

        foreach ($totaisPorSaldo as $saldoId => $valorInformado) {
            $saldoDisponivel = $this->getSaldoDisponivelDepartamento((int) $saldoId, $pedidoId);
            if ($valorInformado > ($saldoDisponivel + 0.01)) {
                $saldo = new SaldoDepartamento((int) $saldoId);
                $numero = $saldo->numero_documento_empenho ?? $saldoId;
                throw new Exception(
                    sprintf(
                        'O valor informado para o empenho %s ultrapassa o saldo disponivel. Disponivel: R$ %s. Informado: R$ %s.',
                        $numero,
                        number_format($saldoDisponivel, 2, ',', '.'),
                        number_format($valorInformado, 2, ',', '.')
                    )
                );
            }
        }
    }

    private static function validarUsoSaldoDepartamentoId(int $saldoId): void
    {
        $saldo = new SaldoDepartamento($saldoId);
        $statusId = (string) $saldo->status_saldo_departamento_id;

        self::validarOrgaoAtivoSaldoDepartamento($saldo);

        if ($statusId === (string) StatusSaldoDepartamento::ANULADO) {
            throw new Exception("Nao e permitido utilizar o empenho selecionado, pois ele esta Anulado.");
        }
    }

    private static function validarOrgaoAtivoSaldoDepartamento(SaldoDepartamento $saldo): void
    {
        $idUnit = (int) TSession::getValue('idunit');
        if ($idUnit <= 0 || empty($saldo->departamento_unit_id)) {
            return;
        }

        $departamento = new DepartamentoUnit((int) $saldo->departamento_unit_id);
        if ((int) $departamento->system_unit_id !== $idUnit) {
            throw new Exception('O empenho selecionado nao pertence ao orgao ativo.');
        }
    }

    private function salvarDotacoes(PedidoFrotas $pedido, Propostas $proposta, array $linhas): void
    {
        $idsMantidos = [];
        $saldoCorrente = [];

        foreach ($linhas as $linha) {
            $saldoId = (int) $linha['saldo_departamento_id'];
            if (!isset($saldoCorrente[$saldoId])) {
                $saldoCorrente[$saldoId] = $this->getSaldoDisponivelDepartamento($saldoId, (int) $pedido->id);
            }

            $dotacao = !empty($linha['id']) ? new DotacaoPedidoFrotas((int) $linha['id']) : new DotacaoPedidoFrotas();
            if (!empty($dotacao->id) && (int) $dotacao->pedido_frotas_id !== (int) $pedido->id) {
                throw new Exception('Dotacao informada nao pertence ao pedido.');
            }

            $saldoCorrente[$saldoId] = round($saldoCorrente[$saldoId] - (float) $linha['valor'], 2);

            $dotacao->pedido_frotas_id = $pedido->id;
            $dotacao->propostas_id = $proposta->id;
            $dotacao->saldo_departamento_id = $saldoId;
            $dotacao->valor = (float) $linha['valor'];
            $dotacao->saldo_atual = $saldoCorrente[$saldoId];
            $dotacao->deleted_at = null;
            $dotacao->store();

            $idsMantidos[] = (int) $dotacao->id;
        }

        $existentes = DotacaoPedidoFrotas::where('pedido_frotas_id', '=', $pedido->id)
            ->where('deleted_at', 'is', NULL)
            ->load();

        if ($existentes) {
            foreach ($existentes as $existente) {
                if (!in_array((int) $existente->id, $idsMantidos, true)) {
                    $existente->deleted_at = date('Y-m-d H:i:s');
                    $existente->store();
                }
            }
        }
    }

    private function getSaldoIdsDotacoesAtivasPedido(int $pedidoId): array
    {
        $saldoIds = [];

        $dotacoes = DotacaoPedidoFrotas::where('pedido_frotas_id', '=', $pedidoId)
            ->where('deleted_at', 'is', NULL)
            ->load();

        if ($dotacoes) {
            foreach ($dotacoes as $dotacao) {
                $saldoId = (int) ($dotacao->saldo_departamento_id ?? 0);
                if ($saldoId > 0) {
                    $saldoIds[$saldoId] = $saldoId;
                }
            }
        }

        return $saldoIds;
    }

    private function atualizarStatusSaldosUtilizados(array $saldoIds): void
    {
        if (empty($saldoIds)) {
            return;
        }

        foreach ($saldoIds as $saldoId) {
            $saldoId = (int) $saldoId;
            if ($saldoId <= 0) {
                continue;
            }

            $saldoDepartamento = new SaldoDepartamento($saldoId);
            $statusAtual = (string) $saldoDepartamento->status_saldo_departamento_id;

            if ($statusAtual === (string) StatusSaldoDepartamento::ANULADO) {
                continue;
            }

            $saldoTotal = (float) $saldoDepartamento->saldo_total;
            if ($saldoTotal <= 0) {
                $saldoTotal = (float) $saldoDepartamento->saldo_produto + (float) $saldoDepartamento->saldo_servico;
            }

            $totalUtilizado = 0.0;
            $dotacoes = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $saldoId)->load();
            if ($dotacoes) {
                foreach ($dotacoes as $dotacao) {
                    if (empty($dotacao->deleted_at)) {
                        $totalUtilizado += (float) $dotacao->valor;
                    }
                }
            }

            if ($saldoTotal > 0 && $totalUtilizado >= ($saldoTotal - 0.01)) {
                if ($statusAtual !== (string) StatusSaldoDepartamento::ENCERRADO) {
                    $saldoDepartamento->status_saldo_departamento_id = StatusSaldoDepartamento::ENCERRADO;
                    $saldoDepartamento->store();
                }
                continue;
            }

            $novoStatus = $totalUtilizado > 0
                ? StatusSaldoDepartamento::EMANDAMENTO
                : StatusSaldoDepartamento::AGUARDANDOINIC;

            if ($statusAtual !== (string) $novoStatus) {
                $saldoDepartamento->status_saldo_departamento_id = $novoStatus;
                $saldoDepartamento->store();
            }
        }
    }

    private function registrarHistoricoRegularizacao(PedidoFrotas $pedido, string $justificativa, float $totalDotacoes, bool $corrigiuValorPedido): void
    {
        $aprovador = AprovadorFrotas::where('system_users_id', '=', TSession::getValue('userid'))->first();

        $historico = new PedidoFrotasHistorico();
        $historico->pedido_frotas_id = $pedido->id;
        $historico->aprovador_frotas_id = $aprovador->id ?? null;
        $historico->estado_pedido_frotas_id = $pedido->estado_pedido_frotas_id;
        $historico->data_operacao = date('Y-m-d H:i:s');
        $historico->obs = trim($justificativa);
        $historico->store();
    }

    private function getJustificativaAprovacao(PedidoFrotas $pedido): string
    {
        $historico = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $pedido->id)
            ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
            ->orderBy('data_operacao', 'desc')
            ->first();

        return $historico ? $this->extractJustificativaUsuario((string) $historico->obs) : '';
    }

    private function extractJustificativaUsuario(string $obs): string
    {
        $pos = stripos($obs, 'Justificativa:');
        if ($pos !== false) {
            return trim(substr($obs, $pos + strlen('Justificativa:')));
        }

        return trim($obs);
    }

    private function garantirHistoricoAprovacao(PedidoFrotas $pedido, string $justificativa): void
    {
        $historico = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $pedido->id)
            ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
            ->first();

        if ($historico) {
            return;
        }

        $aprovador = AprovadorFrotas::where('system_users_id', '=', TSession::getValue('userid'))->first();

        $historico = new PedidoFrotasHistorico();
        $historico->pedido_frotas_id = $pedido->id;
        $historico->aprovador_frotas_id = $aprovador->id ?? null;
        $historico->estado_pedido_frotas_id = EstadoPedidoFrotas::APROVADO;
        $historico->data_operacao = date('Y-m-d H:i:s');
        $historico->obs = trim($justificativa);
        $historico->store();
    }

    private function getSaldoDisponivelDepartamento(int $saldoId, int $pedidoId): float
    {
        $saldo = new SaldoDepartamento($saldoId);
        $saldoDisponivel = $this->getValorEmpenhoPorTipo($saldo);

        $subquery = 'SELECT id FROM pedido_frotas WHERE estado_pedido_frotas_id IN (' .
            EstadoPedidoFrotas::FINALIZADO . ',' .
            EstadoPedidoFrotas::APROVADO . ',' .
            EstadoPedidoFrotas::PGTOAPROVADO . ',' .
            EstadoPedidoFrotas::ENTREGUE . ',' .
            EstadoPedidoFrotas::PREAPROVADO . ')';

        $dotacoes = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $saldoId)
            ->where('pedido_frotas_id', 'IN', "($subquery)")
            ->where('pedido_frotas_id', '<>', $pedidoId)
            ->load();

        if ($dotacoes) {
            foreach ($dotacoes as $dotacao) {
                if (empty($dotacao->deleted_at)) {
                    $saldoDisponivel -= (float) $dotacao->valor;
                }
            }
        }

        return round($saldoDisponivel, 2);
    }

    private static function getSaldoDisponivelDepartamentoStatic(int $saldoId, int $pedidoId): float
    {
        $saldo = new SaldoDepartamento($saldoId);
        $saldoDisponivel = self::getValorEmpenhoPorTipoStatic($saldo);

        $subquery = 'SELECT id FROM pedido_frotas WHERE estado_pedido_frotas_id IN (' .
            EstadoPedidoFrotas::FINALIZADO . ',' .
            EstadoPedidoFrotas::APROVADO . ',' .
            EstadoPedidoFrotas::PGTOAPROVADO . ',' .
            EstadoPedidoFrotas::ENTREGUE . ',' .
            EstadoPedidoFrotas::PREAPROVADO . ')';

        $dotacoesQuery = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $saldoId)
            ->where('pedido_frotas_id', 'IN', "($subquery)");

        if ($pedidoId > 0) {
            $dotacoesQuery->where('pedido_frotas_id', '<>', $pedidoId);
        }

        $dotacoes = $dotacoesQuery->load();
        if ($dotacoes) {
            foreach ($dotacoes as $dotacao) {
                if (empty($dotacao->deleted_at)) {
                    $saldoDisponivel -= (float) $dotacao->valor;
                }
            }
        }

        return round($saldoDisponivel, 2);
    }

    private function getValorEmpenhoPorTipo(SaldoDepartamento $saldo): float
    {
        $tipo = strtoupper((string) ($saldo->tipo ?? ''));
        if ($tipo === 'P' || (int) $tipo === (int) SaldoDepartamento::PRODUTO) {
            $valor = (float) ($saldo->saldo_produto ?? 0);
            return $valor > 0 ? $valor : (float) ($saldo->saldo_total ?? 0);
        }

        if ($tipo === 'S' || (int) $tipo === (int) SaldoDepartamento::SERVICO) {
            $valor = (float) ($saldo->saldo_servico ?? 0);
            return $valor > 0 ? $valor : (float) ($saldo->saldo_total ?? 0);
        }

        $saldoTotal = (float) ($saldo->saldo_total ?? 0);
        return $saldoTotal > 0 ? $saldoTotal : (float) ($saldo->saldo_produto ?? 0) + (float) ($saldo->saldo_servico ?? 0);
    }

    private static function getValorEmpenhoPorTipoStatic(SaldoDepartamento $saldo): float
    {
        $tipo = strtoupper((string) ($saldo->tipo ?? ''));
        if ($tipo === 'P' || (int) $tipo === (int) SaldoDepartamento::PRODUTO) {
            $valor = (float) ($saldo->saldo_produto ?? 0);
            return $valor > 0 ? $valor : (float) ($saldo->saldo_total ?? 0);
        }

        if ($tipo === 'S' || (int) $tipo === (int) SaldoDepartamento::SERVICO) {
            $valor = (float) ($saldo->saldo_servico ?? 0);
            return $valor > 0 ? $valor : (float) ($saldo->saldo_total ?? 0);
        }

        $saldoTotal = (float) ($saldo->saldo_total ?? 0);
        return $saldoTotal > 0 ? $saldoTotal : (float) ($saldo->saldo_produto ?? 0) + (float) ($saldo->saldo_servico ?? 0);
    }

    private function getPropostaReferencia(PedidoFrotas $pedido): ?Propostas
    {
        $propostas = Propostas::where('pedido_frotas_id', '=', $pedido->id)
            ->where('estado_pedido_frotas_id', 'in', [
                EstadoPedidoFrotas::FINALIZADO,
                EstadoPedidoFrotas::APROVADO,
                EstadoPedidoFrotas::PGTOAPROVADO,
                EstadoPedidoFrotas::ENTREGUE,
                EstadoPedidoFrotas::PREAPROVADO,
            ])
            ->orderBy('id', 'desc')
            ->load();

        if (!$propostas) {
            return null;
        }

        foreach ($propostas as $proposta) {
            if ((string) $proposta->estado_pedido_frotas_id === (string) $pedido->estado_pedido_frotas_id) {
                return $proposta;
            }
        }

        return $propostas[0] ?? null;
    }

    private function getValorProposta(Propostas $proposta): float
    {
        foreach (['valor_liquido', 'total_geral_com_desconto', 'valor_total'] as $campo) {
            $valor = round((float) ($proposta->{$campo} ?? 0), 2);
            if ($valor > 0) {
                return $valor;
            }
        }

        return 0.0;
    }

    private function validarEstadoPermitido(PedidoFrotas $pedido): void
    {
        $permitidos = [
            EstadoPedidoFrotas::FINALIZADO,
            EstadoPedidoFrotas::APROVADO,
            EstadoPedidoFrotas::PGTOAPROVADO,
            EstadoPedidoFrotas::ENTREGUE,
            EstadoPedidoFrotas::PREAPROVADO,
        ];

        if (!in_array((string) $pedido->estado_pedido_frotas_id, array_map('strval', $permitidos), true)) {
            throw new Exception('Este pedido nao esta em estado permitido para regularizacao de dotacao/empenho.');
        }
    }

    private function sumDotacoes($dotacoes): float
    {
        $total = 0.0;
        if ($dotacoes) {
            foreach ($dotacoes as $dotacao) {
                if (empty($dotacao->deleted_at)) {
                    $total += (float) $dotacao->valor;
                }
            }
        }

        return round($total, 2);
    }

    private function toFloat($valor): float
    {
        if (is_string($valor) && strpos($valor, ',') !== false && strpos($valor, '.') !== false) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } elseif (is_string($valor) && strpos($valor, ',') !== false) {
            $valor = str_replace(',', '.', $valor);
        }

        return (float) $valor;
    }

    private function checkPermission(): void
    {
        $openTransaction = TTransaction::getDatabase() !== self::$database;

        if ($openTransaction) {
            TTransaction::open(self::$database);
        }

        try {
            $temPermissao = in_array(EstadoPedidoFrotas::APROVADO, AprovadorFrotas::getEstadosDisponiveis());

            if ($openTransaction) {
                TTransaction::close();
            }

            if (!$temPermissao) {
                throw new Exception('Usuario sem permissao para regularizar dotacao/empenho de pedidos.');
            }
        } catch (Exception $e) {
            if ($openTransaction && TTransaction::getDatabase()) {
                TTransaction::rollback();
            }

            throw $e;
        }
    }
}
