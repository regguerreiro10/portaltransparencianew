<?php

use Adianti\Base\AdiantiFileSaveTrait;
use Adianti\Widget\Form\THtmlEditor;
use Adianti\Widget\Form\TMultiFile;

class ProcessoAdministrativoForm extends TPage
{
    use AdiantiFileSaveTrait;

    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $activeRecord = 'ProcessoAdministrativo';
    private static $primaryKey = 'id';
    private static $formName = 'form_ProcessoAdministrativoForm';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro de processos administrativos');

        $id = new TEntry('id');
        $numero_processo = new TEntry('numero_processo');
        $numero_protocolo = new TEntry('numero_protocolo');
        $data_envio = new TDateTime('data_envio');
        $data_abertura = new TDate('data_abertura');
        $tipo_processo = new TCombo('tipo_processo');
        $status = new TCombo('status');
        $status_leitura = new TCombo('status_leitura');
        $nivel_sigilo = new TCombo('nivel_sigilo');
        $sigilo_motivo = new TText('sigilo_motivo');
        $assunto = new TEntry('assunto');
        $ementa = new TText('ementa');
        $descricao = new THtmlEditor('descricao');
        $departamento_origem = new TEntry('departamento_origem');
        $departamento_destino_id = new TDBCombo('departamento_destino_id', 'minierp', 'SystemDepartamento', 'id', '{nome}', 'nome asc');
        $destinatario_nome = new TEntry('destinatario_nome');
        $responsavel = new TEntry('responsavel');
        $autor_nome = new TEntry('autor_nome');
        $requerente = new TEntry('requerente');
        $solicitante = new TEntry('solicitante');
        $prazo_tipo = new TCombo('prazo_tipo');
        $prazo_dias = new TSpinner('prazo_dias');
        $prazo_inicio = new TDate('prazo_inicio');
        $prazo_final = new TDate('prazo_final');
        $prazo_status = new TCombo('prazo_status');
        $observacoes = new TText('observacoes');
        $anexos = new TMultiFile('anexos');

        $tipo_processo->addItems(ProcessoAdministrativoHelper::getTiposProcesso());
        $status->addItems([
            'Rascunho' => 'Rascunho',
            'Enviado' => 'Enviado',
            'Recebido' => 'Recebido',
            'Em andamento' => 'Em andamento',
            'Em analise' => 'Em analise',
            'Aguardando parecer' => 'Aguardando parecer',
            'Despachado' => 'Despachado',
            'Devolvido' => 'Devolvido',
            'Concluido' => 'Concluido',
            'Arquivado' => 'Arquivado',
        ]);
        $status_leitura->addItems(ProcessoAdministrativoHelper::getStatusLeitura());
        $nivel_sigilo->addItems(ProcessoAdministrativoHelper::getSigiloOptions());
        $prazo_tipo->addItems([
            'Dias corridos' => 'Dias corridos',
            'Dias uteis' => 'Dias uteis',
        ]);
        $prazo_status->addItems(ProcessoAdministrativoHelper::getPrazoStatusOptions());

        $tipo_processo->addValidation('Tipo do processo', new TRequiredValidator());
        $assunto->addValidation('Assunto', new TRequiredValidator());
        $departamento_destino_id->addValidation('Departamento destino', new TRequiredValidator());
        $status->addValidation('Status', new TRequiredValidator());
        $nivel_sigilo->addValidation('Sigilo', new TRequiredValidator());

        $id->setEditable(false);
        $numero_processo->setEditable(false);
        $numero_protocolo->setEditable(false);
        $departamento_origem->setEditable(false);
        $data_envio->setEditable(false);
        $responsavel->setEditable(false);
        $autor_nome->setEditable(false);
        $prazo_final->setEditable(false);
        $prazo_status->setEditable(false);

        foreach ([$tipo_processo, $status, $status_leitura, $nivel_sigilo, $departamento_destino_id, $prazo_tipo] as $field) {
            $field->enableSearch();
            $field->setSize('100%');
        }

        foreach ([$numero_processo, $numero_protocolo, $assunto, $destinatario_nome, $responsavel, $autor_nome, $requerente, $solicitante, $departamento_origem] as $field) {
            $field->setSize('100%');
        }

        $data_abertura->setMask('dd/mm/yyyy');
        $data_abertura->setDatabaseMask('yyyy-mm-dd');
        $data_envio->setMask('dd/mm/yyyy hh:ii');
        $data_envio->setDatabaseMask('yyyy-mm-dd hh:ii:ss');
        $prazo_inicio->setMask('dd/mm/yyyy');
        $prazo_inicio->setDatabaseMask('yyyy-mm-dd');
        $prazo_final->setMask('dd/mm/yyyy');
        $prazo_final->setDatabaseMask('yyyy-mm-dd');

        $prazo_dias->setRange(0, 9999, 1);
        $prazo_dias->setValue(0);

        $descricao->setSize('100%', 220);
        $ementa->setSize('100%', 60);
        $sigilo_motivo->setSize('100%', 70);
        $observacoes->setSize('100%', 90);
        $anexos->setSize('100%');
        $anexos->enableFileHandling();
        $anexos->setAllowedExtensions(['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'xlsx', 'zip']);

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [new TLabel('Numero do processo:', null, '14px', null, '100%'), $numero_processo],
            [new TLabel('Numero do protocolo:', null, '14px', null, '100%'), $numero_protocolo],
            [new TLabel('Data e hora:', null, '14px', null, '100%'), $data_envio]
        );
        $row1->layout = ['col-sm-1', 'col-sm-3', 'col-sm-4', 'col-sm-4'];

        $row2 = $this->form->addFields(
            [new TLabel('Tipo do processo:', '#ff0000', '14px', null, '100%'), $tipo_processo],
            [new TLabel('Status:', '#ff0000', '14px', null, '100%'), $status],
            [new TLabel('Leitura:', null, '14px', null, '100%'), $status_leitura],
            [new TLabel('Sigilo:', '#ff0000', '14px', null, '100%'), $nivel_sigilo]
        );
        $row2->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];

        $row3 = $this->form->addFields(
            [new TLabel('Assunto / resumo:', '#ff0000', '14px', null, '100%'), $assunto]
        );
        $row3->layout = ['col-sm-12'];

        $row4 = $this->form->addFields(
            [new TLabel('Ementa:', null, '14px', null, '100%'), $ementa]
        );
        $row4->layout = ['col-sm-12'];

        $row5 = $this->form->addFields(
            [new TLabel('Texto do processo:', null, '14px', null, '100%'), $descricao]
        );
        $row5->layout = ['col-sm-12'];

        $row6 = $this->form->addFields(
            [new TLabel('Departamento de origem:', null, '14px', null, '100%'), $departamento_origem],
            [new TLabel('Departamento destino:', '#ff0000', '14px', null, '100%'), $departamento_destino_id]
        );
        $row6->layout = ['col-sm-6', 'col-sm-6'];

        $row7 = $this->form->addFields(
            [new TLabel('Destinatario:', null, '14px', null, '100%'), $destinatario_nome],
            [new TLabel('Usuario criador:', null, '14px', null, '100%'), $autor_nome],
            [new TLabel('Responsavel atual:', null, '14px', null, '100%'), $responsavel]
        );
        $row7->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row8 = $this->form->addFields(
            [new TLabel('Requerente:', null, '14px', null, '100%'), $requerente],
            [new TLabel('Solicitante:', null, '14px', null, '100%'), $solicitante],
            [new TLabel('Data de abertura:', null, '14px', null, '100%'), $data_abertura]
        );
        $row8->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row9 = $this->form->addFields(
            [new TLabel('Prazo tipo:', null, '14px', null, '100%'), $prazo_tipo],
            [new TLabel('Prazo em dias:', null, '14px', null, '100%'), $prazo_dias],
            [new TLabel('Inicio do prazo:', null, '14px', null, '100%'), $prazo_inicio],
            [new TLabel('Prazo final:', null, '14px', null, '100%'), $prazo_final]
        );
        $row9->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];

        $row10 = $this->form->addFields(
            [new TLabel('Status do prazo:', null, '14px', null, '100%'), $prazo_status],
            [new TLabel('Motivo do sigilo / observacao:', null, '14px', null, '100%'), $sigilo_motivo]
        );
        $row10->layout = ['col-sm-4', 'col-sm-8'];

        $row11 = $this->form->addFields(
            [new TLabel('Observacoes internas:', null, '14px', null, '100%'), $observacoes]
        );
        $row11->layout = ['col-sm-12'];

        $row12 = $this->form->addFields(
            [new TLabel('Anexos:', null, '14px', null, '100%'), $anexos]
        );
        $row12->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar processo', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Limpar formulario', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->form->addAction('Voltar', new TAction(['ProcessoAdministrativoList', 'onShow']), 'fas:arrow-left #000000');

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel('Fechar');
        $btnClose->setImage('fas:times');
        $this->form->addHeaderWidget($btnClose);

        $this->bootstrapCurrentUserContext();

        parent::add($this->form);
    }

    public function onSave($param = null)
    {
        try {
            TTransaction::open(self::$database);
            ProcessoAdministrativoSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $isNew = empty($data->id);
            $object = $isNew ? new ProcessoAdministrativo() : new ProcessoAdministrativo((int) $data->id);
            $oldSigilo = $object->nivel_sigilo ?? null;
            $context = ProcessoAdministrativoHelper::getCurrentUserContext();

            if (!$isNew && !ProcessoAdministrativoHelper::canAccess($object)) {
                throw new Exception('Voce nao tem permissao para editar este processo.');
            }

            $object->fromArray((array) $data);

            $object->remetente_user_id = $context['user_id'];
            $object->remetente_nome = $context['name'];
            $object->autor_nome = $object->autor_nome ?: $context['name'];
            $object->responsavel = $object->responsavel ?: $context['name'];
            $object->departamento_origem_id = $context['primary_department_id'];
            $object->departamento_origem = $context['primary_department_name'];

            $object->departamento_destino = ProcessoAdministrativoHelper::resolveDepartmentName((int) $object->departamento_destino_id);
            $object->departamento_atual_id = (int) $object->departamento_destino_id;
            $object->departamento_atual = $object->departamento_destino;

            if (empty($object->numero_processo) || $isNew) {
                $object->numero_processo = $this->generateNumeroProcesso();
            }

            if (empty($object->numero_protocolo) || $isNew) {
                $object->numero_protocolo = $this->generateNumeroProtocolo();
            }

            if (empty($object->data_envio)) {
                $object->data_envio = date('Y-m-d H:i:s');
            }

            if (empty($object->data_abertura)) {
                $object->data_abertura = date('Y-m-d');
            }

            if (empty($object->prazo_inicio)) {
                $object->prazo_inicio = $object->data_abertura;
            }

            if (!empty($object->prazo_dias) && !empty($object->prazo_inicio)) {
                $object->prazo_final = ProcessoAdministrativoHelper::calculatePrazoFinal(
                    $object->prazo_inicio,
                    (int) $object->prazo_dias,
                    $object->prazo_tipo ?: 'Dias corridos'
                );
            }

            $object->prazo_status = ProcessoAdministrativoHelper::determinePrazoStatus($object->prazo_final, $object->prazo_status);
            $object->updated_at = date('Y-m-d H:i:s');
            if (empty($object->created_at)) {
                $object->created_at = $object->updated_at;
            }

            if ($isNew) {
                $object->status = $object->status ?: 'Enviado';
                $object->status_leitura = $object->status_leitura ?: 'Nao lido';
            }

            if ($oldSigilo !== null && $oldSigilo !== $object->nivel_sigilo) {
                $object->sigilo_alterado_em = date('Y-m-d H:i:s');
                $object->sigilo_alterado_por = $context['name'];
            }

            $object->store();

            $attachments = $this->saveFiles(
                $object,
                $data,
                'anexos',
                'app/files/processos_administrativos',
                'ProcessoAdministrativoAnexo',
                'arquivo',
                'processo_administrativo_id'
            );
            $this->updateAttachmentMetadata($attachments);

            if ($isNew) {
                ProcessoAdministrativoHelper::createTramitacao(
                    $object,
                    'Cadastro',
                    'Criado e enviado',
                    'Processo administrativo criado no sistema.',
                    'Cadastro inicial do processo.',
                    $object->departamento_origem,
                    $object->departamento_destino
                );
            } else {
                ProcessoAdministrativoHelper::createTramitacao(
                    $object,
                    'Atualizacao',
                    'Editado',
                    'Dados do processo atualizados.',
                    'Atualizacao manual do cadastro.',
                    $object->departamento_origem,
                    $object->departamento_atual
                );
            }

            if ($oldSigilo !== null && $oldSigilo !== $object->nivel_sigilo) {
                ProcessoAdministrativoHelper::createTramitacao(
                    $object,
                    'Sigilo',
                    'Alteracao de sigilo',
                    'Nivel de sigilo alterado para ' . $object->nivel_sigilo . '.',
                    $object->sigilo_motivo ?: 'Alteracao registrada no processo.',
                    $object->departamento_origem,
                    $object->departamento_atual
                );
            }

            $data->id = $object->id;
            $data->numero_processo = $object->numero_processo;
            $data->numero_protocolo = $object->numero_protocolo;
            $data->departamento_origem = $object->departamento_origem;
            $data->responsavel = $object->responsavel;
            $data->autor_nome = $object->autor_nome;
            $data->prazo_final = $object->prazo_final;
            $data->prazo_status = $object->prazo_status;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Processo salvo com sucesso.', 'topRight', 'far:check-circle');
            TApplication::loadPage('ProcessoAdministrativoList', 'onShow', $param ?? []);
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
                ProcessoAdministrativoSchemaHelper::ensureSchema();
                $object = new ProcessoAdministrativo((int) $param['key']);

                if (!ProcessoAdministrativoHelper::canAccess($object)) {
                    throw new Exception('Voce nao tem permissao para acessar este processo.');
                }

                $formData = (object) $object->toArray();
                $formData->anexos = [];

                foreach ($object->getProcessoAdministrativoAnexos() as $anexo) {
                    $formData->anexos[$anexo->id] = $anexo->arquivo;
                }

                $this->form->setData($formData);
                TTransaction::close();
            } else {
                $this->form->clear();
                $this->bootstrapCurrentUserContext();
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
        }
    }

    public function onClear($param)
    {
        $this->form->clear(true);
        $this->bootstrapCurrentUserContext();
    }

    public function onShow($param = null)
    {
    }

    private function bootstrapCurrentUserContext(): void
    {
        $context = ProcessoAdministrativoHelper::getCurrentUserContext();
        $data = new stdClass;
        $data->status = 'Enviado';
        $data->status_leitura = 'Nao lido';
        $data->nivel_sigilo = 'Publico';
        $data->prazo_tipo = 'Dias corridos';
        $data->prazo_status = 'No prazo';
        $data->data_abertura = date('d/m/Y');
        $data->data_envio = date('d/m/Y H:i');
        $data->departamento_origem = $context['primary_department_name'];
        $data->responsavel = $context['name'];
        $data->autor_nome = $context['name'];
        $data->solicitante = $context['name'];
        $data->prazo_dias = 0;
        $this->form->setData($data);
    }

    private function updateAttachmentMetadata(array $attachments): void
    {
        foreach ($attachments as $index => $attachment) {
            if (!$attachment instanceof ProcessoAdministrativoAnexo) {
                continue;
            }

            $attachment->ordem = $index + 1;
            if (empty($attachment->nome) && !empty($attachment->arquivo)) {
                $attachment->nome = basename($attachment->arquivo);
            }
            $attachment->store();
        }
    }

    private function generateNumeroProcesso(): string
    {
        $conn = TTransaction::get();
        $result = $conn->query('SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM processo_administrativo');
        $nextId = (int) $result->fetchColumn();

        return sprintf('PA-%s-%06d', date('Y'), $nextId);
    }

    private function generateNumeroProtocolo(): string
    {
        $conn = TTransaction::get();
        $result = $conn->query('SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM processo_administrativo');
        $nextId = (int) $result->fetchColumn();

        return sprintf('PROTOCOLO-%s%06d', date('Y'), $nextId);
    }
}
