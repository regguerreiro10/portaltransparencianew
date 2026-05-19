<?php

use Adianti\Base\AdiantiFileSaveTrait;
use Adianti\Widget\Form\TMultiFile;

class LicitacaoForm extends TPage
{
    use AdiantiFileSaveTrait;

    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $activeRecord = 'Licitacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_LicitacaoForm';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro de licitacoes');

        $id = new TEntry('id');
        $numero_edital = new TEntry('numero_edital');
        $processo_origem = new TEntry('processo_origem');
        $data_licitacao = new TDate('data_licitacao');
        $status = new TCombo('status');
        $modalidade = new TCombo('modalidade');
        $gestor = new TEntry('gestor');
        $objeto = new TEntry('objeto');
        $fornecedor_nome = new TEntry('fornecedor_nome');
        $fornecedor_documento = new TEntry('fornecedor_documento');
        $valor_estimado = new TNumeric('valor_estimado', 2, ',', '.');
        $anexos = new TMultiFile('anexos');

        $status->addItems([
            'Em andamento' => 'Em andamento',
            'Homologada' => 'Homologada',
            'Suspensa' => 'Suspensa',
            'Revogada' => 'Revogada',
            'Cancelada' => 'Cancelada',
            'Concluida' => 'Concluida',
        ]);

        $modalidade->addItems([
            'Pregao Eletronico' => 'Pregao Eletronico',
            'Concorrencia' => 'Concorrencia',
            'Tomada de Precos' => 'Tomada de Precos',
            'Carta Convite' => 'Carta Convite',
            'Dispensa' => 'Dispensa',
            'Inexigibilidade' => 'Inexigibilidade',
            'Leilao' => 'Leilao',
            'Outro' => 'Outro',
        ]);

        $numero_edital->addValidation('Numero do edital', new TRequiredValidator());
        $processo_origem->addValidation('Processo de origem', new TRequiredValidator());
        $data_licitacao->addValidation('Data', new TRequiredValidator());
        $status->addValidation('Status', new TRequiredValidator());
        $modalidade->addValidation('Modalidade', new TRequiredValidator());
        $gestor->addValidation('Gestor', new TRequiredValidator());
        $objeto->addValidation('Objeto', new TRequiredValidator());
        $valor_estimado->addValidation('Valor estimado', new TRequiredValidator());

        $id->setEditable(false);
        $status->setValue('Em andamento');
        $status->enableSearch();
        $modalidade->enableSearch();
        $data_licitacao->setMask('dd/mm/yyyy');
        $data_licitacao->setDatabaseMask('yyyy-mm-dd');
        $anexos->enableFileHandling();
        $anexos->setAllowedExtensions(['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'gif', 'webp']);

        $id->setSize('100%');
        $numero_edital->setSize('100%');
        $processo_origem->setSize('100%');
        $data_licitacao->setSize('100%');
        $status->setSize('100%');
        $modalidade->setSize('100%');
        $gestor->setSize('100%');
        $objeto->setSize('100%');
        $fornecedor_nome->setSize('100%');
        $fornecedor_documento->setSize('100%');
        $valor_estimado->setSize('100%');
        $anexos->setSize('100%');

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Numero do edital:', '#ff0000', '14px', null, '100%'), $numero_edital],
            [new TLabel('Processo de origem:', '#ff0000', '14px', null, '100%'), $processo_origem]
        );
        $row1->layout = ['col-sm-2', 'col-sm-5', 'col-sm-5'];

        $row2 = $this->form->addFields(
            [new TLabel('Data:', '#ff0000', '14px', null, '100%'), $data_licitacao],
            [new TLabel('Status:', '#ff0000', '14px', null, '100%'), $status],
            [new TLabel('Modalidade:', '#ff0000', '14px', null, '100%'), $modalidade]
        );
        $row2->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row3 = $this->form->addFields(
            [new TLabel('Gestor responsavel:', '#ff0000', '14px', null, '100%'), $gestor],
            [new TLabel('Valor estimado:', '#ff0000', '14px', null, '100%'), $valor_estimado]
        );
        $row3->layout = ['col-sm-8', 'col-sm-4'];

        $row4 = $this->form->addFields(
            [new TLabel('Objeto da licitacao:', '#ff0000', '14px', null, '100%'), $objeto]
        );
        $row4->layout = ['col-sm-12'];

        $row5 = $this->form->addFields(
            [new TLabel('Fornecedor:', null, '14px', null, '100%'), $fornecedor_nome],
            [new TLabel('CPF/CNPJ do fornecedor:', null, '14px', null, '100%'), $fornecedor_documento]
        );
        $row5->layout = ['col-sm-7', 'col-sm-5'];

        $row6 = $this->form->addFields(
            [new TLabel('Upload do edital e anexos (PDF, DOC/DOCX, imagens):', null, '14px', null, '100%'), $anexos]
        );
        $row6->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Limpar formulario', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->form->addAction('Voltar', new TAction(['LicitacaoList', 'onShow']), 'fas:arrow-left #000000');

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel('Fechar');
        $btnClose->setImage('fas:times');
        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);
    }

    public function onSave($param = null)
    {
        try {
            TTransaction::open(self::$database);
            LicitacaoSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $object = !empty($data->id) ? new Licitacao((int) $data->id) : new Licitacao();

            $object->fromArray((array) $data);
            $object->downloads = isset($object->downloads) ? (int) $object->downloads : 0;

            $now = date('Y-m-d H:i:s');
            $object->updated_at = $now;
            if (empty($object->created_at)) {
                $object->created_at = $now;
            }

            $object->store();

            $attachments = $this->saveFiles(
                $object,
                $data,
                'anexos',
                'app/files/licitacoes',
                'LicitacaoAnexo',
                'arquivo',
                'licitacao_id'
            );
            $this->updateAttachmentMetadata($attachments);

            $data->id = $object->id;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Licitacao salva', 'topRight', 'far:check-circle');
            TApplication::loadPage('LicitacaoList', 'onShow', $param ?? []);
            TScript::create("Template.closeRightPanel();");
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                TTransaction::open(self::$database);
                LicitacaoSchemaHelper::ensureSchema();
                $object = new Licitacao($param['key']);
                $object->anexos = [];

                foreach ($object->getLicitacaoAnexos() as $anexo) {
                    $object->anexos[$anexo->id] = $anexo->arquivo;
                }

                $this->form->setData($object);
                TTransaction::close();
            } else {
                $this->form->clear();
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear($param)
    {
        $this->form->clear(true);
    }

    public function onShow($param = null)
    {
    }

    private function updateAttachmentMetadata(array $attachments): void
    {
        foreach ($attachments as $index => $attachment) {
            if (!$attachment instanceof LicitacaoAnexo) {
                continue;
            }

            $attachment->ordem = $index + 1;

            if (empty($attachment->nome) && !empty($attachment->arquivo)) {
                $attachment->nome = basename($attachment->arquivo);
            }

            $attachment->store();
        }
    }
}
