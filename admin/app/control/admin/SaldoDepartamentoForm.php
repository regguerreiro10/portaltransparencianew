<?php

class SaldoDepartamentoForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'SaldoDepartamento';
    private static $primaryKey = 'id';
    private static $formName = 'form_SaldoDepartamentoForm';

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
        $this->form->setFormTitle("Cadastro de Saldo Departamento (dotação orçamentária)");

        $criteria_saldo_entidade_contrato_id = new TCriteria();
        $filterVar = TSession::getValue('entidade');
        $criteria_saldo_entidade_contrato_id->add(new TFilter('entidade_id', '=', $filterVar)); 

        $id = new TEntry('id');
        $datatransacao = new TDate('datatransacao');
        $tipotransacao = new TCombo('tipotransacao');
        $tipo = new TCombo('tipo');
        $historico = new TEntry('historico');
        $saldo_produto = new TNumeric('saldo_produto', '2', ',', '.' );
        $saldo_servico = new TNumeric('saldo_servico', '2', ',', '.' );
        $saldo_total = new TNumeric('saldo_total', '2', ',', '.' );
        $numero_documento_empenho = new TEntry('numero_documento_empenho');
        $documento_empenho = new TFile('documento_empenho');
        $data_documento_empenho = new TDate('data_documento_empenho');
        $numero_processo = new TEntry('numero_processo');
        $data_processo = new TDate('data_processo');
        $status_saldo_departamento_id = new TDBCombo(
            'status_saldo_departamento_id',
            'minierp',
            'StatusSaldoDepartamento',
            'id',
            '{descricao}',
            'id asc'
        );
       
        $saldo_entidade_contrato_id = new TDBCombo(
            'saldo_entidade_contrato_id',
            'minierp',
            'SaldoEntidadeContrato',
            'id',
            '{historico} - de {dtinicio_br} até {dtfinal_br} - {valor_saldo_br}',
            'id asc',
            $criteria_saldo_entidade_contrato_id
        );

        $saldo_produto->setExitAction(new TAction([$this,'onExitSaldoProduto']));
        $saldo_servico->setExitAction(new TAction([$this,'onExitSaldoServico']));
        $saldo_total->setExitAction(new TAction([$this,'onExitSaldoTotal']));

        $datatransacao->addValidation("Data Transação", new TRequiredValidator()); 
        $tipotransacao->addValidation("Tipo de transação", new TRequiredValidator()); 
        $historico->addValidation("Histórico ", new TRequiredValidator()); 
        $saldo_produto->addValidation("Valor do empenho do produto", new TRequiredValidator()); 
        $saldo_total->addValidation("Saldo total do empenho", new TRequiredValidator()); 
        $numero_documento_empenho->addValidation("Número do documento", new TRequiredValidator()); 
        $saldo_entidade_contrato_id->addValidation("Qual contrato pertence", new TRequiredValidator()); 
        $data_documento_empenho->addValidation("Data documento empenho", new TRequiredValidator()); 
        $numero_processo->addValidation("Numero processo", new TRequiredValidator()); 
        $data_processo->addValidation("Data processo", new TRequiredValidator()); 
        $status_saldo_departamento_id->addValidation("Status Saldo Empenho", new TRequiredValidator());

        $id->setEditable(false);
        $datatransacao->setMask('dd/mm/yyyy');
        $datatransacao->setDatabaseMask('yyyy-mm-dd');
        $data_documento_empenho->setMask('dd/mm/yyyy');
        $data_documento_empenho->setDatabaseMask('yyyy-mm-dd');
        $data_processo->setMask('dd/mm/yyyy');
        $data_processo->setDatabaseMask('yyyy-mm-dd');
        $tipotransacao->addItems(["C"=>"Crédito","D"=>"Débito"]);
        $tipotransacao->setValue('C');

        $tipotransacao->enableSearch();
        $tipo->addItems(["P"=>"Produto","S"=>"Serviço"]);
        $tipo->setValue('P');
        $tipo->enableSearch();
        $status_saldo_departamento_id->setValue(self::getDefaultStatusSaldoDepartamentoId());
        $status_saldo_departamento_id->enableSearch();
        $documento_empenho->enableFileHandling();
        $saldo_entidade_contrato_id->enableSearch();

        $id->setSize(100);
        $datatransacao->setSize(110);
        $tipotransacao->setSize('100%');
        $tipo->setSize('100%');
        $historico->setSize('100%', 70);
        $saldo_produto->setSize('100%');
        $saldo_servico->setSize('100%');
        $saldo_total->setSize('100%');
        $data_documento_empenho->setSize('100%');
        $numero_processo->setSize('100%');
        $data_processo->setSize('100%');
        $status_saldo_departamento_id->setSize('100%');

        $documento_empenho->setSize('100%');
        $numero_documento_empenho->setSize('100%');
        $saldo_entidade_contrato_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null)],[$id],[new TLabel("Datatransacao: *", '#FF0000', '14px', null)],[$datatransacao]);
        $row2 = $this->form->addFields([new TLabel("Tipo transação: *", '#FF0000', '14px', null)],[$tipotransacao], [new TLabel("Tipo: ", null, '14px', null)],[$tipo]);
        $row3 = $this->form->addFields([new TLabel("SD empenho produto:", null, '14px', null)],[$saldo_produto],[new TLabel("SD empenho serviço: ", null, '14px', null)],[$saldo_servico]);
        $row4 = $this->form->addFields([new TLabel("SD total: ", null, '14px', null)],[$saldo_total], [new TLabel("Histórico: *", '#FF0000', '14px', null)],[$historico]);
        $row5 = $this->form->addFields([new TLabel("Anexar documento: ", null, '14px', null)],[$documento_empenho],[new TLabel("Qual contrato pertence? *", '#FF0000', '14px', null)],[$saldo_entidade_contrato_id]);
        // $row6 = $this->form->addFields([new TLabel("Historico: *", '#FF0000', '14px', null)],[$historico]);
        $row7 = $this->form->addFields([new TLabel("Numero do empenho: *", '#FF0000', '14px', null)],[$numero_documento_empenho],[new TLabel("Data do empenho: *", '#FF0000', '14px', null)],[$data_documento_empenho]);
        $row8 = $this->form->addFields([new TLabel("Numero do processo: *", '#FF0000', '14px', null)],[$numero_processo],[new TLabel("Data do processo: *", '#FF0000', '14px', null)],[$data_processo]);
        $row9 = $this->form->addFields([new TLabel("Status Saldo Empenho: *", '#FF0000', '14px', null)],[$status_saldo_departamento_id]);

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['SaldoDepartamentoList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        // Define largura visual do formulário
     //   $this->form->getContainer()->style = 'width: 80%;';

        // Define largura do painel lateral
     //   $style = new TStyle('right-panel > .container-part');
   //     $style->width = '80% !important';

        parent::add($this->form);
        $style = new TStyle('right-panel > .container-part[page-name=SaldoDepartamentoForm]');
        $style->width = '50% !important';   
        $style->show(true);
    }

    public function onSave($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $this->form->validate();

            $data = $this->form->getData();
            if (!empty($data->id))
            {
                $object = new SaldoDepartamento($data->id);
            }
            else
            {
                $object = new SaldoDepartamento();
                unset($data->id);
            }
            $object->fromArray((array) $data);

            if (empty($object->status_saldo_departamento_id))
            {
                $object->status_saldo_departamento_id = self::getDefaultStatusSaldoDepartamentoId();
            }

            $this->validarAlteracaoSaldoEncerrado($object, $data);
            $this->recalcularStatusPorSaldoUtilizado($object);
            $this->validarStatusAguardandoInicioSemDotacao($object);
            $this->validarStatusEmAndamentoComDotacao($object);
            $this->gravarDataAnulado($object);

            $mensagemConfirmacao = $this->verificarTransicaoDeAnulado($object);
            if (!$mensagemConfirmacao)
            {
                $mensagemConfirmacao = $this->verificarTransicaoDeEncerrado($object);
            }
            if ($mensagemConfirmacao)
            {
                TSession::setValue('saldo_dep_pending_data', (array) $data);
                TTransaction::close();
                $this->form->setData($data);

                $actionParam = [];
                if (!empty($param['target_container']))
                {
                    $actionParam['target_container'] = $param['target_container'];
                }
                $action = new TAction([$this, 'onSaveConfirmado']);
                $action->setParameters($actionParam);
                new TQuestion($mensagemConfirmacao, $action);
                return;
            }

            $this->executarStore($object, $data, $param);
            TTransaction::close();

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            $loadPageParam = [];
            if (!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            TApplication::loadPage('SaldoDepartamentoList', 'onShow', $loadPageParam);
            TScript::create("Template.closeRightPanel();");
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
            TTransaction::rollback();
        }
    }

    public function onSaveConfirmado($param = null)
    {
        try
        {
            $pendingData = TSession::getValue('saldo_dep_pending_data');
            TSession::setValue('saldo_dep_pending_data', null);

            if (empty($pendingData))
            {
                new TMessage('error', 'Sessão expirada. Por favor, tente salvar novamente.');
                return;
            }

            TTransaction::open(self::$database);

            $data = (object) $pendingData;

            if (!empty($data->id))
            {
                $object = new SaldoDepartamento($data->id);
            }
            else
            {
                $object = new SaldoDepartamento();
            }
            $object->fromArray((array) $data);
            $this->recalcularStatusPorSaldoUtilizado($object);
            $this->gravarDataAnulado($object);

            $this->executarStore($object, $data, $param);
            TTransaction::close();

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            $loadPageParam = [];
            if (!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            TApplication::loadPage('SaldoDepartamentoList', 'onShow', $loadPageParam);
            TScript::create("Template.closeRightPanel();");
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    private function executarStore(SaldoDepartamento $object, stdClass $data, $param): void
    {
        $documento_empenho_dir = 'app/documentos/empenho';
        $object->departamento_unit_id = TSession::getValue('depunitid');
        $object->system_users_id = TSession::getValue('userid');

        $object->store();

        if ($data->documento_empenho)
        {
            $this->saveFile($object, $data, 'documento_empenho', $documento_empenho_dir);
        }

        $data->id = $object->id;

        $saldo = SaldoDepartamento::where('departamento_unit_id', '=', TSession::getValue('depunitid'))->load();
        $saldo_empenho = 0;

        if ($saldo)
        {
            $saldo_credito = 0;
            $saldo_debito  = 0;

            foreach ($saldo as $sdo)
            {
                if ($sdo->tipotransacao == 'C')
                {
                    $saldo_credito += $sdo->saldo_produto + $sdo->saldo_servico;
                }
                elseif ($sdo->tipotransacao == 'D')
                {
                    $saldo_debito += $sdo->saldo_produto + $sdo->saldo_servico;
                }
            }

            $saldo_empenho = $saldo_credito - $saldo_debito;
        }

        $departamento = DepartamentoUnit::where('id', '=', TSession::getValue('depunitid'))
                                        ->where('system_unit_id', '=', TSession::getValue('unit1'))
                                        ->load();

        if ($departamento)
        {
            foreach ($departamento as $dp)
            {
                $dp->valor_empenho = $saldo_empenho;
                $dp->store();
            }
        }

        $criteria = new TCriteria();
        $criteria->add(new TFilter('entidade_id', '=', TSession::getValue('entidade')));
        $criteria->add(new TFilter('dtinicio', '<=', $object->datatransacao));
        $criteria->add(new TFilter('dtfinal', '>=', $object->datatransacao));
        $criteria->setProperty('limit', 1);

        $saldo_entidade_contrato = SaldoEntidadeContrato::getObjects($criteria);

        if (!$saldo_entidade_contrato)
        {
            throw new Exception("Antes de cadastrar a dotação orçamentária e valores de empenho, é necessário cadastrar o saldo contratual no intervalo de data correto!");
        }

        $objectsdo = new stdClass();
        $objectsdo->valor_empenho = $saldo_empenho;
        TForm::sendData('form_SaldoDepartamentoForm', $objectsdo);
        $this->form->setData($data);
    }

    private function verificarTransicaoDeAnulado(SaldoDepartamento $object): ?string
    {
        if (empty($object->id))
        {
            return null;
        }

        $saldoAtual = new SaldoDepartamento($object->id);
        if ((string) $saldoAtual->status_saldo_departamento_id !== (string) StatusSaldoDepartamento::ANULADO)
        {
            return null;
        }

        $novoStatus = (string) $object->status_saldo_departamento_id;

        if ($novoStatus === (string) StatusSaldoDepartamento::ANULADO)
        {
            return null;
        }

        $vinculos = $this->getVinculosPorContexto($object->id);
        $temDotacoes = $vinculos['count'] > 0;

        if ($novoStatus === (string) StatusSaldoDepartamento::EMANDAMENTO && $temDotacoes)
        {
            return "Este empenho está <b>ANULADO</b> e possui {$vinculos['alias']} vinculados. Deseja reativar para <b>Em Andamento</b>?";
        }

        if ($novoStatus === (string) StatusSaldoDepartamento::ENCERRADO && $temDotacoes)
        {
            throw new Exception("Não é permitido alterar de Anulado para Encerrado quando já existem {$vinculos['alias']} vinculados.");
        }

        if ($novoStatus === (string) StatusSaldoDepartamento::AGUARDANDOINIC && $temDotacoes)
        {
            throw new Exception("Não é permitido alterar de Anulado para Aguardando Início quando já existem {$vinculos['alias']} vinculados.");
        }

        return null;
    }

    private function verificarTransicaoDeEncerrado(SaldoDepartamento $object): ?string
    {
        if (empty($object->id))
        {
            return null;
        }

        $saldoAtual = new SaldoDepartamento($object->id);
        if ((string) $saldoAtual->status_saldo_departamento_id !== (string) StatusSaldoDepartamento::ENCERRADO)
        {
            return null;
        }

        $novoStatus = (string) $object->status_saldo_departamento_id;
        if ($novoStatus !== (string) StatusSaldoDepartamento::ANULADO)
        {
            return null;
        }

        if (!$this->temDotacoesVinculadas($object->id))
        {
            return null;
        }

        $vinculos = $this->getVinculosPorContexto($object->id);
        return "Este empenho está <b>ENCERRADO</b> e possui {$vinculos['alias']} vinculados. Deseja alterar o status para <b>Anulado</b>?";
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new SaldoDepartamento($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
                $obj = new stdClass;
                $obj->status_saldo_departamento_id = self::getDefaultStatusSaldoDepartamentoId();
                TForm::sendData(self::$formName, $obj);
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
        $obj = new stdClass;
        $obj->status_saldo_departamento_id = self::getDefaultStatusSaldoDepartamentoId();
        TForm::sendData(self::$formName, $obj);

    }

    public function onShow($param = null)
    {
        if (empty($param['key']))
        {
            $obj = new stdClass;
            $obj->status_saldo_departamento_id = self::getDefaultStatusSaldoDepartamentoId();
            TForm::sendData(self::$formName, $obj);
        }
    } 

    private static function getDefaultStatusSaldoDepartamentoId()
    {
        $openedHere = false;
        try
        {
            $openedHere = !TTransaction::get();
            if ($openedHere)
            {
                TTransaction::open(self::$database);
            }

            $criteria = new TCriteria;
            $criteria->add(new TFilter('descricao', 'like', 'Aguardando%'));
            $criteria->setProperty('order', 'id');
            $criteria->setProperty('limit', 1);

            $status = StatusSaldoDepartamento::getObjects($criteria);
            $defaultId = $status ? $status[0]->id : null;

            if ($openedHere)
            {
                TTransaction::close();
            }

            return $defaultId;
        }
        catch (Exception $e)
        {
            if ($openedHere && TTransaction::get())
            {
                TTransaction::rollback();
            }

            return null;
        }
    }

    public static function getFormName()
    {
        return self::$formName;
    }

    private function validarAlteracaoSaldoEncerrado(SaldoDepartamento $object, stdClass $data): void
    {
        if (empty($object->id))
        {
            return;
        }

        $saldoAtual = new SaldoDepartamento($object->id);
        if ((string) $saldoAtual->status_saldo_departamento_id !== (string) StatusSaldoDepartamento::ENCERRADO)
        {
            return;
        }

        $novoSaldoProduto = $this->parseMoneyToFloat($data->saldo_produto ?? $object->saldo_produto ?? 0);
        $novoSaldoServico = $this->parseMoneyToFloat($data->saldo_servico ?? $object->saldo_servico ?? 0);
        $saldoProdutoAtual = $this->parseMoneyToFloat($saldoAtual->saldo_produto ?? 0);
        $saldoServicoAtual = $this->parseMoneyToFloat($saldoAtual->saldo_servico ?? 0);
        $houveAumento = $novoSaldoProduto > ($saldoProdutoAtual + 0.00001) || $novoSaldoServico > ($saldoServicoAtual + 0.00001);

        if ($novoSaldoProduto < ($saldoProdutoAtual - 0.00001))
        {
            throw new Exception('Para alterar um empenho encerrado, o saldo empenho produto não pode ser menor que o valor atual.');
        }

        if ($novoSaldoServico < ($saldoServicoAtual - 0.00001))
        {
            throw new Exception('Para alterar um empenho encerrado, o saldo empenho serviço não pode ser menor que o valor atual.');
        }

        if ((string) $object->status_saldo_departamento_id === (string) StatusSaldoDepartamento::EMANDAMENTO && !$houveAumento)
        {
            throw new Exception('Não é permitido alterar um empenho encerrado para Em Andamento sem aumentar o saldo do empenho.');
        }

        if ($houveAumento)
        {
            $object->status_saldo_departamento_id = StatusSaldoDepartamento::EMANDAMENTO;
        }
    }

    private function validarStatusAguardandoInicioSemDotacao(SaldoDepartamento $object): void
    {
        if (empty($object->id))
        {
            return;
        }

        if ((string) $object->status_saldo_departamento_id !== (string) StatusSaldoDepartamento::AGUARDANDOINIC)
        {
            return;
        }

        if ($this->temDotacoesVinculadas($object->id))
        {
            $vinculos = $this->getVinculosPorContexto($object->id);
            throw new Exception("Não é permitido definir o status como Aguardando Início porque já existem {$vinculos['alias']} vinculados.");
        }
    }

    private function validarStatusEmAndamentoComDotacao(SaldoDepartamento $object): void
    {
        if (empty($object->id))
        {
            return;
        }

        if ((string) $object->status_saldo_departamento_id !== (string) StatusSaldoDepartamento::EMANDAMENTO)
        {
            return;
        }

        if (!$this->temDotacoesVinculadas($object->id))
        {
            throw new Exception("Não pode trocar status porque não existem {$this->getAliasContextoPlural()} para este empenho.");
        }
    }

    private function recalcularStatusPorSaldoUtilizado(SaldoDepartamento $object): void
    {
        if (empty($object->id))
        {
            return;
        }

        if ((string) $object->status_saldo_departamento_id === (string) StatusSaldoDepartamento::ANULADO)
        {
            return;
        }

        $saldoTotal = $this->getValorEmpenhoPorTipo($object);
        $totalUtilizado = $this->getTotalUtilizadoPorContexto((int) $object->id);

        if ($saldoTotal > 0 && $totalUtilizado >= ($saldoTotal - 0.01))
        {
            $object->status_saldo_departamento_id = StatusSaldoDepartamento::ENCERRADO;
            return;
        }

        $object->status_saldo_departamento_id = $totalUtilizado > 0
            ? StatusSaldoDepartamento::EMANDAMENTO
            : StatusSaldoDepartamento::AGUARDANDOINIC;
    }

    private function getValorEmpenhoPorTipo(SaldoDepartamento $object): float
    {
        $tipo = strtoupper((string) ($object->tipo ?? ''));

        if ($tipo === 'P' || (int) $tipo === (int) SaldoDepartamento::PRODUTO)
        {
            $valorProduto = $this->parseMoneyToFloat($object->saldo_produto ?? 0);
            return $valorProduto > 0 ? $valorProduto : $this->parseMoneyToFloat($object->saldo_total ?? 0);
        }

        if ($tipo === 'S' || (int) $tipo === (int) SaldoDepartamento::SERVICO)
        {
            $valorServico = $this->parseMoneyToFloat($object->saldo_servico ?? 0);
            return $valorServico > 0 ? $valorServico : $this->parseMoneyToFloat($object->saldo_total ?? 0);
        }

        $saldoTotal = $this->parseMoneyToFloat($object->saldo_total ?? 0);
        return $saldoTotal > 0
            ? $saldoTotal
            : $this->parseMoneyToFloat($object->saldo_produto ?? 0) + $this->parseMoneyToFloat($object->saldo_servico ?? 0);
    }

    private function getTotalUtilizadoPorContexto(int $saldoDepartamentoId): float
    {
        $sistema = (string) TSession::getValue('sistema');

        if ($sistema === 'frotas')
        {
            return $this->getTotalDotacoesFrotas($saldoDepartamentoId);
        }

        if ($sistema === 'compras')
        {
            return $this->getTotalPedidosCompras($saldoDepartamentoId);
        }

        return $this->getTotalDotacoesFrotas($saldoDepartamentoId) + $this->getTotalPedidosCompras($saldoDepartamentoId);
    }

    private function getTotalDotacoesFrotas(int $saldoDepartamentoId): float
    {
        $total = 0.0;
        $dotacoes = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $saldoDepartamentoId)
            ->where('deleted_at', 'is', NULL)
            ->load();

        if ($dotacoes)
        {
            foreach ($dotacoes as $dotacao)
            {
                $total += (float) ($dotacao->valor ?? 0);
            }
        }

        return round($total, 2);
    }

    private function getTotalPedidosCompras(int $saldoDepartamentoId): float
    {
        $total = 0.0;
        $pedidos = Pedido::where('saldo_departamento_id', '=', $saldoDepartamentoId)
            ->where('deleted_at', 'is', NULL)
            ->load();

        if ($pedidos)
        {
            foreach ($pedidos as $pedido)
            {
                $total += (float) ($pedido->valor_liquido_cotacao ?? $pedido->valor_total_cotacao ?? $pedido->valor_total ?? 0);
            }
        }

        return round($total, 2);
    }

    private function temDotacoesVinculadas(int $saldoDepartamentoId): bool
    {
        if ($saldoDepartamentoId <= 0)
        {
            return false;
        }

        $vinculos = $this->getVinculosPorContexto($saldoDepartamentoId);
        return $vinculos['count'] > 0;
    }

    private function getVinculosPorContexto(int $saldoDepartamentoId): array
    {
        $sistema = (string) TSession::getValue('sistema');

        $countDotacoes = $this->countVinculos('DotacaoPedidoFrotas', $saldoDepartamentoId);
        $countPedidos  = $this->countVinculos('Pedido', $saldoDepartamentoId);

        if ($sistema === 'frotas')
        {
            return ['count' => $countDotacoes, 'alias' => 'dotações'];
        }

        if ($sistema === 'compras')
        {
            return ['count' => $countPedidos, 'alias' => 'pedidos'];
        }

        $countTotal = $countDotacoes + $countPedidos;
        $alias = ($countDotacoes > 0 && $countPedidos > 0) ? 'dotações/pedidos' : (($countDotacoes > 0) ? 'dotações' : 'pedidos');

        return ['count' => $countTotal, 'alias' => $alias ?: 'vínculos'];
    }

    private function countVinculos(string $model, int $saldoDepartamentoId): int
    {
        $repository = new TRepository($model);
        $criteria = new TCriteria;
        $criteria->add(new TFilter('saldo_departamento_id', '=', $saldoDepartamentoId));

        return (int) $repository->count($criteria);
    }

    private function getAliasContextoPlural(): string
    {
        $sistema = (string) TSession::getValue('sistema');

        if ($sistema === 'frotas')
        {
            return 'dotações';
        }

        if ($sistema === 'compras')
        {
            return 'pedidos';
        }

        return 'dotações ou pedidos';
    }

    private function gravarDataAnulado(SaldoDepartamento $object): void
    {
        if (empty($object->id))
        {
            return;
        }

        if ((string) $object->status_saldo_departamento_id !== (string) StatusSaldoDepartamento::ANULADO)
        {
            return;
        }

        $saldoAtual = new SaldoDepartamento($object->id);
        if ((string) $saldoAtual->status_saldo_departamento_id === (string) StatusSaldoDepartamento::ANULADO)
        {
            return; // já estava anulado, não sobrescreve a data
        }

        $object->data_anulado = date('Y-m-d H:i:s');
    }

    private function parseMoneyToFloat($value): float
    {
        if (is_numeric($value))
        {
            return (float) $value;
        }

        $value = (string) $value;
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }

      public static function onExitSaldoTotal($param = null) 
    {
        try 
        {
            //code here
               if(!empty($param['saldo_produto']) OR !empty($param['saldo_servico']))
            {
                $saldo_produto = (double) str_replace(',', '.', str_replace('.', '', $param['saldo_produto']));
                $saldo_servico = (double) str_replace(',', '.', str_replace('.', '', $param['saldo_servico']));

                $saldo_total = $saldo_produto + $saldo_servico ;
                $object = new stdClass();
                $object->saldo_total = number_format($saldo_total, 2, ',', '.');
                TForm::sendData(self::$formName, $object);    
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public static function onExitSaldoProduto($param = null) 
    {
        try 
        {
            self::onExitSaldoTotal($param);

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public static function onExitSaldoServico($param = null) 
    {
        try 
        {
            self::onExitSaldoTotal($param);

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

}
