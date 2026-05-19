<?php

use Adianti\Base\AdiantiFileSaveTrait;
use Adianti\Widget\Form\TMultiFile;

class ProcessoLegislativoForm extends TPage
{
    use AdiantiFileSaveTrait;

    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $activeRecord = 'ProcessoLegislativo';
    private static $primaryKey = 'id';
    private static $formName = 'form_ProcessoLegislativoForm';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro de processos legislativos');

        $id = new TEntry('id');
        $tipo_processo = new TCombo('tipo_processo');
        $numero_processo = new TEntry('numero_processo');
        $ano = new TEntry('ano');
        $numero_protocolo = new TEntry('numero_protocolo');
        $ementa = new TEntry('ementa');
        $assunto = new TEntry('assunto');
        $autor_principal = new TEntry('autor_principal');
        $coautores = new TText('coautores');
        $tipo_autor = new TCombo('tipo_autor');
        $situacao_status = new TCombo('situacao_status');
        $departamento_gabinete = new TEntry('departamento_gabinete');
        $sessao_vinculada = new TEntry('sessao_vinculada');
        $sessao_apresentacao = new TEntry('sessao_apresentacao');
        $sessao_apreciacao = new TEntry('sessao_apreciacao');
        $data_sessao = new TDateTime('data_sessao');
        $status_sessao = new TCombo('status_sessao');
        $despacho_texto = new TText('despacho_texto');
        $anexos = new TMultiFile('anexos');

        $tipo_processo->addItems([
            'Projeto de Lei' => 'Projeto de Lei',
            'Projeto de Resolucao' => 'Projeto de Resolucao',
            'Requerimento' => 'Requerimento',
            'Indicacao' => 'Indicacao',
            'Emenda' => 'Emenda',
        ]);

        $tipo_autor->addItems([
            'Vereador' => 'Vereador',
            'Comissao' => 'Comissao',
            'Mesa Diretora' => 'Mesa Diretora',
            'Executivo' => 'Executivo',
        ]);

        $situacao_status->addItems([
            'Protocolado' => 'Protocolado',
            'Em analise' => 'Em analise',
            'Em pauta' => 'Em pauta',
            'Apreciado' => 'Apreciado',
            'Arquivado' => 'Arquivado',
        ]);

        $status_sessao->addItems([
            'Vinculado' => 'Vinculado',
            'Em pauta' => 'Em pauta',
            'Apreciado' => 'Apreciado',
            'Retirado' => 'Retirado',
        ]);

        $tipo_processo->addValidation('Tipo de processo', new TRequiredValidator());
        $ementa->addValidation('Ementa', new TRequiredValidator());
        $autor_principal->addValidation('Autor principal', new TRequiredValidator());
        $tipo_autor->addValidation('Tipo do autor', new TRequiredValidator());
        $situacao_status->addValidation('Situacao', new TRequiredValidator());
        $ano->addValidation('Ano', new TRequiredValidator());

        $id->setEditable(false);
        $numero_processo->setEditable(false);
        $numero_protocolo->setEditable(false);
        $ano->setValue(date('Y'));
        $tipo_processo->enableSearch();
        $tipo_autor->enableSearch();
        $situacao_status->enableSearch();
        $status_sessao->enableSearch();
        $data_sessao->setMask('dd/mm/yyyy hh:ii');
        $data_sessao->setDatabaseMask('yyyy-mm-dd hh:ii:ss');
        $anexos->enableFileHandling();
        $anexos->setAllowedExtensions(['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'xlsx', 'zip']);

        $id->setSize('100%');
        $tipo_processo->setSize('100%');
        $numero_processo->setSize('100%');
        $ano->setSize('100%');
        $numero_protocolo->setSize('100%');
        $ementa->setSize('100%');
        $assunto->setSize('100%');
        $autor_principal->setSize('100%');
        $coautores->setSize('100%', 80);
        $tipo_autor->setSize('100%');
        $situacao_status->setSize('100%');
        $departamento_gabinete->setSize('100%');
        $sessao_vinculada->setSize('100%');
        $sessao_apresentacao->setSize('100%');
        $sessao_apreciacao->setSize('100%');
        $data_sessao->setSize('100%');
        $status_sessao->setSize('100%');
        $despacho_texto->setSize('100%', 100);
        $anexos->setSize('100%');

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Tipo de processo:', '#ff0000', '14px', null, '100%'), $tipo_processo],
            [new TLabel('Ano:', '#ff0000', '14px', null, '100%'), $ano]
        );
        $row1->layout = ['col-sm-2', 'col-sm-6', 'col-sm-4'];

        $row2 = $this->form->addFields(
            [new TLabel('Numero do processo:', null, '14px', null, '100%'), $numero_processo],
            [new TLabel('Numero do protocolo:', null, '14px', null, '100%'), $numero_protocolo]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $row3 = $this->form->addFields(
            [new TLabel('Ementa:', '#ff0000', '14px', null, '100%'), $ementa]
        );
        $row3->layout = ['col-sm-12'];

        $row4 = $this->form->addFields(
            [new TLabel('Assunto:', null, '14px', null, '100%'), $assunto]
        );
        $row4->layout = ['col-sm-12'];

        $row5 = $this->form->addFields(
            [new TLabel('Autor principal:', '#ff0000', '14px', null, '100%'), $autor_principal],
            [new TLabel('Tipo do autor:', '#ff0000', '14px', null, '100%'), $tipo_autor],
            [new TLabel('Situacao / status:', '#ff0000', '14px', null, '100%'), $situacao_status]
        );
        $row5->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row6 = $this->form->addFields(
            [new TLabel('Coautores:', null, '14px', null, '100%'), $coautores]
        );
        $row6->layout = ['col-sm-12'];

        $row7 = $this->form->addFields(
            [new TLabel('Departamento / gabinete:', null, '14px', null, '100%'), $departamento_gabinete],
            [new TLabel('Sessao vinculada:', null, '14px', null, '100%'), $sessao_vinculada]
        );
        $row7->layout = ['col-sm-6', 'col-sm-6'];

        $row8 = $this->form->addFields(
            [new TLabel('Sessao de apresentacao:', null, '14px', null, '100%'), $sessao_apresentacao],
            [new TLabel('Sessao de apreciacao:', null, '14px', null, '100%'), $sessao_apreciacao]
        );
        $row8->layout = ['col-sm-6', 'col-sm-6'];

        $row9 = $this->form->addFields(
            [new TLabel('Data da sessao:', null, '14px', null, '100%'), $data_sessao],
            [new TLabel('Status na sessao:', null, '14px', null, '100%'), $status_sessao]
        );
        $row9->layout = ['col-sm-6', 'col-sm-6'];

        $row10 = $this->form->addFields(
            [new TLabel('Despacho:', null, '14px', null, '100%'), $despacho_texto]
        );
        $row10->layout = ['col-sm-12'];

        $row11 = $this->form->addFields(
            [new TLabel('Anexos do processo:', null, '14px', null, '100%'), $anexos]
        );
        $row11->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Limpar formulario', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->form->addAction('Voltar', new TAction(['ProcessoLegislativoList', 'onShow']), 'fas:arrow-left #000000');

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
            ProcessoLegislativoSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $isNew = empty($data->id);
            $object = $isNew ? new ProcessoLegislativo() : new ProcessoLegislativo((int) $data->id);

            $object->fromArray((array) $data);
            $object->downloads = isset($object->downloads) ? (int) $object->downloads : 0;

            if (empty($object->numero_processo) || $isNew) {
                $object->numero_processo = $this->generateNumeroProcesso();
            }

            if (empty($object->numero_protocolo) || $isNew) {
                $object->numero_protocolo = $this->generateNumeroProtocolo();
            }

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
                'app/files/processos_legislativos',
                'ProcessoLegislativoAnexo',
                'arquivo',
                'processo_legislativo_id'
            );
            $this->updateAttachmentMetadata($attachments);

            if ($isNew) {
                $this->createInitialTramitacao($object);
            }

            $data->id = $object->id;
            $data->numero_processo = $object->numero_processo;
            $data->numero_protocolo = $object->numero_protocolo;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Processo legislativo salvo', 'topRight', 'far:check-circle');
            TApplication::loadPage('ProcessoLegislativoList', 'onShow', $param ?? []);
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
                ProcessoLegislativoSchemaHelper::ensureSchema();
                $object = new ProcessoLegislativo($param['key']);
                $formData = (object) $object->toArray();
                $formData->anexos = [];

                foreach ($object->getProcessoLegislativoAnexos() as $anexo) {
                    $formData->anexos[$anexo->id] = $anexo->arquivo;
                }

                $this->form->setData($formData);
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
            if (!$attachment instanceof ProcessoLegislativoAnexo) {
                continue;
            }

            $attachment->ordem = $index + 1;
            $attachment->principal = ($index === 0) ? 1 : 0;

            if (empty($attachment->nome) && !empty($attachment->arquivo)) {
                $attachment->nome = basename($attachment->arquivo);
            }

            $attachment->store();
        }
    }

    private function createInitialTramitacao(ProcessoLegislativo $processo): void
    {
        $tramite = new ProcessoLegislativoTramitacao();
        $tramite->processo_legislativo_id = $processo->id;
        $tramite->data_tramitacao = date('Y-m-d H:i:s');
        $tramite->situacao = $processo->situacao_status ?: 'Protocolado';
        $tramite->descricao_andamento = 'Cadastro inicial do processo legislativo.';
        $tramite->remetente = $processo->departamento_gabinete;
        $tramite->destinatario = $processo->departamento_gabinete;
        $tramite->usuario_responsavel = TSession::getValue('login') ?: $processo->autor_principal;
        $tramite->observacao = 'Processo legislativo criado no sistema.';
        $tramite->created_at = date('Y-m-d H:i:s');
        $tramite->store();
    }

    private function generateNumeroProcesso(): string
    {
        $conn = TTransaction::get();
        $result = $conn->query('SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM processo_legislativo');
        $nextId = (int) $result->fetchColumn();

        return sprintf('PL-%s-%04d', date('Y'), $nextId);
    }

    private function generateNumeroProtocolo(): string
    {
        $conn = TTransaction::get();
        $result = $conn->query('SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM processo_legislativo');
        $nextId = (int) $result->fetchColumn();

        return sprintf('PROTOCOLO-%s%06d', date('Y'), $nextId);
    }
}
