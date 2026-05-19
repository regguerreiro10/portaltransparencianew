<?php

class FrenteCaixaAbastecimentoTagDocumentoForm extends TPage
{
    protected $form;

    private static $database = 'minierp';
    private static $activeRecord = 'DocumentosPropostas';
    private static $formName = 'form_FrenteCaixaAbastecimentoTagDocumentoForm';

    use Adianti\Base\AdiantiFileSaveTrait;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Anexar cupom fiscal / nota fiscal');

        $criteriaTipo = new TCriteria;

        $id = new THidden('id');
        $numero = new TEntry('numero');
        $valor = new TNumeric('valor', 2, ',', '.');
        $caminho = new TMultiFile('caminho');
        $tipo = new TDBCombo('tipo_documentos_propostas_id', 'minierp', 'TipoDocumentosPropostas', 'id', '{descricao}', 'id asc', $criteriaTipo);

        $caminho->enableFileHandling();
        $tipo->enableSearch();

        $caminho->addValidation('Arquivo', new TRequiredValidator());
        $tipo->addValidation('Tipo do documento', new TRequiredValidator());

        $numero->setSize('100%');
        $valor->setSize('100%');
        $caminho->setSize('100%');
        $tipo->setSize('100%');

        $row1 = $this->form->addFields(
            [$id],
            [new TLabel('Numero do documento', null, '14px', null, '100%'), $numero],
            [new TLabel('Valor', null, '14px', null, '100%'), $valor]
        );
        $row1->layout = ['col-sm-0', 'col-sm-6', 'col-sm-6'];

        $row2 = $this->form->addFields(
            [new TLabel('Tipo do documento *', '#ff0000', '14px', null, '100%'), $tipo],
            [new TLabel('Arquivo *', '#ff0000', '14px', null, '100%'), $caminho]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $observacao = new TElement('div');
        $observacao->style = 'margin:10px 0 0 0;padding:12px;border:1px solid #d9edf7;background:#f4fbff;color:#245269;border-radius:4px;';
        $observacao->add('Anexe aqui o cupom fiscal emitido pelo posto ou a nota fiscal do abastecimento.');
        $this->form->addContent([$observacao]);

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');

        $this->form->addAction('Limpar formulario', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->form->addAction('Voltar', new TAction(['FrenteCaixaAbastecimentoTagDocumentoList', 'onShow']), 'fas:arrow-left #000000');

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
        try
        {
            TTransaction::open(self::$database);

            $this->form->validate();
            $data = $this->form->getData();

            $pedidoId = (int) TSession::getValue('FrenteCaixaAbastecimentoTagDocumentoList_pedido_id');
            $propostaId = (int) TSession::getValue('FrenteCaixaAbastecimentoTagDocumentoList_proposta_id');

            if ($pedidoId <= 0 || $propostaId <= 0)
            {
                throw new Exception('Nao foi possivel identificar a proposta do abastecimento para anexar o documento.');
            }

            $object = !empty($data->id) ? new DocumentosPropostas((int) $data->id) : new DocumentosPropostas();
            $object->propostas_id = $propostaId;
            $object->numero = trim((string) ($data->numero ?? '')) ?: null;
            $object->valor = $data->valor ?? null;
            $object->tipo_documentos_propostas_id = $data->tipo_documentos_propostas_id ?? null;
            $object->caminho = $data->caminho;
            $object->store();

            $this->saveFilesByComma($object, $data, 'caminho', 'app/documentos/redes');

            $data->id = $object->id;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Documento anexado com sucesso.', 'topRight', 'far:check-circle');
            TApplication::loadPage('FrenteCaixaAbastecimentoTagDocumentoList', 'onShow');
            TScript::create('Template.closeRightPanel();');
        }
        catch (Exception $e)
        {
            if (TTransaction::get())
            {
                TTransaction::rollback();
            }

            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
        }
    }

    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                TTransaction::open(self::$database);

                $object = new DocumentosPropostas((int) $param['key']);
                $object->caminho = !empty($object->caminho) ? explode(',', (string) $object->caminho) : [];
                $this->form->setData($object);

                TTransaction::close();
            }
            else
            {
                $this->form->clear(true);
            }
        }
        catch (Exception $e)
        {
            if (TTransaction::get())
            {
                TTransaction::rollback();
            }

            new TMessage('error', $e->getMessage());
        }
    }

    public function onClear($param = null)
    {
        $this->form->clear(true);
    }

    public function onShow($param = null)
    {
    }
}
