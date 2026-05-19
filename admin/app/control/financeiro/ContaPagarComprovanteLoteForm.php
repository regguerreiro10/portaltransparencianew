<?php

class ContaPagarComprovanteLoteForm extends TPage
{
    protected $form;
    private static $database = 'minierp';
    private static $formName = 'form_ContaPagarComprovanteLoteForm';

    use Adianti\Base\AdiantiFileSaveTrait;

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $pessoaId = (int) ($param['key'] ?? $param['pessoa_id'] ?? 0);

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Anexar comprovante de pagamento');

        $criteria_tipo_anexo_id = new TCriteria();

        $pessoa_id = new THidden('pessoa_id');
        $tipo_anexo_id = new TDBCombo('tipo_anexo_id', 'minierp', 'TipoAnexo', 'id', '{nome}', 'nome asc', $criteria_tipo_anexo_id);
        $descricao = new TEntry('descricao');
        $arquivo = new TFile('arquivo');

        $pessoa_id->setValue($pessoaId);
        $descricao->setValue('Comprovante de pagamento');
        $tipo_anexo_id->enableSearch();
        $arquivo->enableFileHandling();

        $tipo_anexo_id->addValidation('Tipo anexo', new TRequiredValidator());
        $arquivo->addValidation('Arquivo', new TRequiredValidator());

        $tipo_anexo_id->setSize('100%');
        $descricao->setSize('100%');
        $arquivo->setSize('100%');

        $contasSelecionadas = TSession::getValue('ContaPagarEmAbertoSimpleListbuilder_datagrid_check') ?: [];
        if (empty($contasSelecionadas) && $pessoaId > 0) {
            $contasSelecionadas = TSession::getValue('PessoaContaPagarEmAbertoDocument_last_selected_' . $pessoaId) ?: [];
        }
        $qtdeSelecionada = count($contasSelecionadas);

        $info = new TElement('div');
        $info->class = 'alert alert-info';
        $info->add("O comprovante sera anexado em {$qtdeSelecionada} conta(s) selecionada(s) desta pessoa. Se aparecer 0, selecione as contas em Financeiro > Contas a pagar em aberto antes de anexar.");

        $this->form->addContent([$info]);
        $this->form->addFields([$pessoa_id]);
        $row1 = $this->form->addFields(
            [new TLabel('Tipo anexo:', '#ff0000', '14px', null, '100%'), $tipo_anexo_id],
            [new TLabel('Descricao:', null, '14px', null, '100%'), $descricao]
        );
        $row1->layout = ['col-sm-6', 'col-sm-6'];

        $row2 = $this->form->addFields([new TLabel('Arquivo:', '#ff0000', '14px', null, '100%'), $arquivo]);
        $row2->layout = ['col-sm-12'];

        $btn_onsave = $this->form->addAction('Salvar comprovante', new TAction([$this, 'onSave']), 'fas:paperclip #ffffff');
        $btn_onsave->addStyleClass('btn-primary');

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = 'Template.closeRightPanel();';
        $btnClose->setLabel('Fechar');
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);
    }

    public function onSave($param = null)
    {
        try {
            $this->form->validate();
            $data = $this->form->getData();
            $pessoaId = (int) ($data->pessoa_id ?? 0);
            $contasSelecionadas = TSession::getValue('ContaPagarEmAbertoSimpleListbuilder_datagrid_check') ?: [];
            if (empty($contasSelecionadas)) {
                $contasSelecionadas = TSession::getValue('PessoaContaPagarEmAbertoDocument_last_selected_' . $pessoaId) ?: [];
            }
            $contasSelecionadas = array_values(array_unique(array_map('intval', $contasSelecionadas)));

            if ($pessoaId <= 0) {
                throw new Exception('Pessoa nao informada.');
            }

            if (empty($contasSelecionadas)) {
                throw new Exception('Selecione ao menos uma conta/pedido antes de anexar o comprovante.');
            }

            TTransaction::open(self::$database);

            $criteria = new TCriteria();
            $criteria->add(new TFilter('id', 'in', $contasSelecionadas));
            $criteria->add(new TFilter('pessoa_id', '=', $pessoaId));
            $criteria->add(new TFilter('tipo_conta_id', '=', TipoConta::PAGAR));
            $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
            $contas = (new TRepository('Conta'))->load($criteria, false);

            if (!$contas || count($contas) !== count($contasSelecionadas)) {
                throw new Exception('Existem contas selecionadas que nao pertencem a esta pessoa/unidade.');
            }

            $arquivoDir = 'app/anexos';
            $arquivoSalvo = null;
            $total = 0;

            foreach ($contas as $conta) {
                $anexo = new ContaAnexo();
                $anexo->conta_id = $conta->id;
                $anexo->tipo_anexo_id = $data->tipo_anexo_id;
                $anexo->descricao = $data->descricao ?: 'Comprovante de pagamento';
                $anexo->created_at = date('Y-m-d H:i:s');
                $anexo->updated_at = date('Y-m-d H:i:s');

                if ($arquivoSalvo) {
                    $anexo->arquivo = $arquivoSalvo;
                    $anexo->store();
                } else {
                    $anexo->store();
                    $this->saveFile($anexo, $data, 'arquivo', $arquivoDir);
                    if (empty($anexo->arquivo) && !empty($anexo->id)) {
                        $anexo = new ContaAnexo($anexo->id);
                    }
                    $arquivoSalvo = $anexo->arquivo;
                    if (empty($arquivoSalvo)) {
                        throw new Exception('Nao foi possivel salvar o arquivo do comprovante.');
                    }
                }

                $total++;
            }

            TSession::setValue('ContaPagarEmAbertoSimpleListbuilder_datagrid_check', null);
            TSession::setValue('PessoaContaPagarEmAbertoDocument_last_selected_' . $pessoaId, null);

            TTransaction::close();

            TToast::show('success', "Comprovante anexado em {$total} conta(s).", 'topRight', 'far:check-circle');
            $paginaRetorno = TSession::getValue('sistema') == 'compras' ? 'PessoaFormView' : 'PessoaFormFrotasView';
            TApplication::loadPage($paginaRetorno, 'onShow', ['key' => $pessoaId, 'current_tab_tab_622940daf9f3b' => 3]);
            TScript::create('Template.closeRightPanel();');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {
    }
}
