<?php

use Adianti\Base\AdiantiFileSaveTrait;
use Adianti\Widget\Form\TMultiFile;

class ProcessoAdministrativoTramitacaoForm extends TPage
{
    use AdiantiFileSaveTrait;

    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $formName = 'form_ProcessoAdministrativoTramitacaoForm';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Despacho e tramitacao do processo');

        $id = new TEntry('id');
        $processo_administrativo_id = new THidden('processo_administrativo_id');
        $data_tramitacao = new TDateTime('data_tramitacao');
        $tipo_evento = new TCombo('tipo_evento');
        $acao_executada = new TCombo('acao_executada');
        $departamento_origem = new TEntry('departamento_origem');
        $departamento_destino_nome = new TEntry('departamento_destino_nome');
        $departamento_destino_id = new TDBCombo('departamento_destino_id', 'minierp', 'SystemDepartamento', 'id', '{nome}', 'nome asc');
        $usuario_responsavel = new TEntry('usuario_responsavel');
        $despacho_texto = new TText('despacho_texto');
        $observacao = new TText('observacao');
        $prazo_tipo = new TCombo('prazo_tipo');
        $prazo_dias = new TSpinner('prazo_dias');
        $prazo_status = new TCombo('prazo_status');
        $anexos = new TMultiFile('anexos');

        $tipo_evento->addItems([
            'Recebimento' => 'Recebimento',
            'Despacho' => 'Despacho',
            'Encaminhamento' => 'Encaminhamento',
            'Devolucao' => 'Devolucao',
            'Prorrogacao' => 'Prorrogacao',
            'Interrupcao' => 'Interrupcao',
            'Arquivamento' => 'Arquivamento',
            'Desarquivamento' => 'Desarquivamento',
            'Conclusao' => 'Conclusao',
        ]);

        $acao_executada->addItems([
            'Receber processo' => 'Receber processo',
            'Despachar' => 'Despachar',
            'Enviar para outro departamento' => 'Enviar para outro departamento',
            'Devolver processo' => 'Devolver processo',
            'Salvar rascunho' => 'Salvar rascunho',
            'Arquivar' => 'Arquivar',
            'Desarquivar' => 'Desarquivar',
            'Interromper prazo' => 'Interromper prazo',
            'Prorrogar prazo' => 'Prorrogar prazo',
            'Concluir' => 'Concluir',
        ]);

        $prazo_tipo->addItems([
            'Dias corridos' => 'Dias corridos',
            'Dias uteis' => 'Dias uteis',
        ]);
        $prazo_status->addItems(ProcessoAdministrativoHelper::getPrazoStatusOptions());

        $tipo_evento->addValidation('Tipo de evento', new TRequiredValidator());
        $acao_executada->addValidation('Acao executada', new TRequiredValidator());
        $despacho_texto->addValidation('Despacho', new TRequiredValidator());

        $id->setEditable(false);
        $departamento_origem->setEditable(false);
        $departamento_destino_nome->setEditable(false);
        $usuario_responsavel->setEditable(false);

        foreach ([$tipo_evento, $acao_executada, $departamento_destino_id, $prazo_tipo, $prazo_status] as $field) {
            $field->enableSearch();
            $field->setSize('100%');
        }
        foreach ([$departamento_origem, $departamento_destino_nome, $usuario_responsavel] as $field) {
            $field->setSize('100%');
        }

        $data_tramitacao->setMask('dd/mm/yyyy hh:ii');
        $data_tramitacao->setDatabaseMask('yyyy-mm-dd hh:ii:ss');
        $prazo_dias->setRange(0, 9999, 1);
        $prazo_dias->setValue(0);
        $despacho_texto->setSize('100%', 120);
        $observacao->setSize('100%', 80);
        $anexos->setSize('100%');
        $anexos->enableFileHandling();
        $anexos->setAllowedExtensions(['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'xlsx', 'zip']);

        $row1 = $this->form->addFields(
            [new TLabel('Id:', null, '14px', null, '100%'), $id],
            [$processo_administrativo_id],
            [new TLabel('Data/hora:', '#ff0000', '14px', null, '100%'), $data_tramitacao]
        );
        $row1->layout = ['col-sm-2', 'col-sm-4', 'col-sm-6'];

        $row2 = $this->form->addFields(
            [new TLabel('Tipo de evento:', '#ff0000', '14px', null, '100%'), $tipo_evento],
            [new TLabel('Acao executada:', '#ff0000', '14px', null, '100%'), $acao_executada]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $row3 = $this->form->addFields(
            [new TLabel('Departamento origem:', null, '14px', null, '100%'), $departamento_origem],
            [new TLabel('Departamento atual:', null, '14px', null, '100%'), $departamento_destino_nome]
        );
        $row3->layout = ['col-sm-6', 'col-sm-6'];

        $row4 = $this->form->addFields(
            [new TLabel('Encaminhar para departamento:', null, '14px', null, '100%'), $departamento_destino_id],
            [new TLabel('Usuario responsavel:', null, '14px', null, '100%'), $usuario_responsavel]
        );
        $row4->layout = ['col-sm-6', 'col-sm-6'];

        $row5 = $this->form->addFields(
            [new TLabel('Despacho:', '#ff0000', '14px', null, '100%'), $despacho_texto]
        );
        $row5->layout = ['col-sm-12'];

        $row6 = $this->form->addFields(
            [new TLabel('Observacao complementar:', null, '14px', null, '100%'), $observacao]
        );
        $row6->layout = ['col-sm-12'];

        $row7 = $this->form->addFields(
            [new TLabel('Prazo tipo:', null, '14px', null, '100%'), $prazo_tipo],
            [new TLabel('Prazo em dias:', null, '14px', null, '100%'), $prazo_dias],
            [new TLabel('Status do prazo:', null, '14px', null, '100%'), $prazo_status]
        );
        $row7->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row8 = $this->form->addFields(
            [new TLabel('Anexos do despacho:', null, '14px', null, '100%'), $anexos]
        );
        $row8->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar despacho', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Voltar', new TAction(['ProcessoAdministrativoList', 'onShow']), 'fas:arrow-left #000000');

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
            ProcessoAdministrativoSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $processo = new ProcessoAdministrativo((int) $data->processo_administrativo_id);

            if (!ProcessoAdministrativoHelper::canAccess($processo)) {
                throw new Exception('Voce nao tem permissao para tramitar este processo.');
            }

            $context = ProcessoAdministrativoHelper::getCurrentUserContext();
            $object = !empty($data->id) ? new ProcessoAdministrativoTramitacao((int) $data->id) : new ProcessoAdministrativoTramitacao();
            $object->processo_administrativo_id = $processo->id;
            $object->data_tramitacao = $data->data_tramitacao ?: date('Y-m-d H:i:s');
            $object->tipo_evento = $data->tipo_evento;
            $object->acao_executada = $data->acao_executada;
            $object->departamento_origem = $processo->departamento_atual ?: $processo->departamento_origem;

            $departamentoDestino = null;
            if (!empty($data->departamento_destino_id)) {
                $departamentoDestino = SystemDepartamento::find((int) $data->departamento_destino_id);
            }

            $object->departamento_destino = $departamentoDestino instanceof SystemDepartamento ? $departamentoDestino->nome : $data->departamento_destino_nome;
            $object->usuario_responsavel = $context['name'];
            $object->despacho_texto = $data->despacho_texto;
            $object->observacao = $data->observacao;
            $object->prazo_tipo = $data->prazo_tipo ?: $processo->prazo_tipo;
            $object->prazo_dias = (int) ($data->prazo_dias ?: $processo->prazo_dias);

            if (!empty($data->prazo_dias)) {
                $object->prazo_final = ProcessoAdministrativoHelper::calculatePrazoFinal(
                    date('Y-m-d'),
                    (int) $data->prazo_dias,
                    $data->prazo_tipo ?: 'Dias corridos'
                );
            } else {
                $object->prazo_final = $processo->prazo_final;
            }

            $object->prazo_status = !empty($data->prazo_status)
                ? $data->prazo_status
                : ProcessoAdministrativoHelper::determinePrazoStatus($object->prazo_final, $processo->prazo_status);
            $object->created_at = $object->created_at ?: date('Y-m-d H:i:s');
            $object->store();

            $savedAttachments = $this->saveFiles(
                $processo,
                $data,
                'anexos',
                'app/files/processos_administrativos',
                'ProcessoAdministrativoAnexo',
                'arquivo',
                'processo_administrativo_id'
            );

            $attachmentNames = [];
            foreach ($savedAttachments as $attachment) {
                if ($attachment instanceof ProcessoAdministrativoAnexo) {
                    $attachment->nome = $attachment->nome ?: basename($attachment->arquivo);
                    $attachment->store();
                    $attachmentNames[] = $attachment->nome;
                }
            }

            if ($attachmentNames) {
                $object->anexo_descricao = implode(', ', $attachmentNames);
                $object->store();
            }

            $now = date('Y-m-d H:i:s');
            switch ($data->acao_executada) {
                case 'Receber processo':
                    $processo->status = 'Recebido';
                    $processo->status_leitura = 'Lido';
                    $processo->lido_em = $now;
                    break;

                case 'Enviar para outro departamento':
                    if (!$departamentoDestino instanceof SystemDepartamento) {
                        throw new Exception('Selecione o departamento de destino para encaminhar o processo.');
                    }
                    $processo->departamento_atual_id = $departamentoDestino->id;
                    $processo->departamento_atual = $departamentoDestino->nome;
                    $processo->departamento_destino_id = $departamentoDestino->id;
                    $processo->departamento_destino = $departamentoDestino->nome;
                    $processo->status = 'Despachado';
                    $processo->status_leitura = 'Nao lido';
                    break;

                case 'Devolver processo':
                    $processo->departamento_atual_id = $processo->departamento_origem_id;
                    $processo->departamento_atual = $processo->departamento_origem;
                    $processo->status = 'Devolvido';
                    $processo->status_leitura = 'Nao lido';
                    break;

                case 'Salvar rascunho':
                    $processo->status = 'Rascunho';
                    $processo->status_leitura = 'Rascunho';
                    $processo->rascunho_em = $now;
                    break;

                case 'Arquivar':
                    $processo->status = 'Arquivado';
                    $processo->status_leitura = 'Arquivado';
                    $processo->prazo_status = 'Arquivado';
                    $processo->arquivado_em = $now;
                    break;

                case 'Desarquivar':
                    $processo->status = 'Em andamento';
                    $processo->status_leitura = 'Lido';
                    $processo->prazo_status = ProcessoAdministrativoHelper::determinePrazoStatus($processo->prazo_final);
                    $processo->arquivado_em = null;
                    break;

                case 'Interromper prazo':
                    $processo->prazo_status = 'Interrompido';
                    $processo->prazo_interrompido_em = $now;
                    $processo->status = 'Em analise';
                    break;

                case 'Prorrogar prazo':
                    if (empty($data->prazo_dias)) {
                        throw new Exception('Informe a quantidade de dias para prorrogar o prazo.');
                    }
                    $baseDate = $processo->prazo_final ?: date('Y-m-d');
                    $processo->prazo_tipo = $data->prazo_tipo ?: $processo->prazo_tipo ?: 'Dias corridos';
                    $processo->prazo_dias = (int) $data->prazo_dias;
                    $processo->prazo_prorrogado_ate = ProcessoAdministrativoHelper::calculatePrazoFinal($baseDate, (int) $data->prazo_dias, $processo->prazo_tipo);
                    $processo->prazo_final = $processo->prazo_prorrogado_ate;
                    $processo->prazo_status = 'Prorrogado';
                    $processo->status = 'Em analise';
                    break;

                case 'Concluir':
                    $processo->status = 'Concluido';
                    $processo->status_leitura = 'Lido';
                    break;

                default:
                    $processo->status = 'Em analise';
                    $processo->status_leitura = 'Lido';
                    break;
            }

            if (!empty($data->prazo_dias) && !in_array($data->acao_executada, ['Prorrogar prazo', 'Interromper prazo'], true)) {
                $processo->prazo_tipo = $data->prazo_tipo ?: 'Dias corridos';
                $processo->prazo_dias = (int) $data->prazo_dias;
                $processo->prazo_inicio = date('Y-m-d');
                $processo->prazo_final = $object->prazo_final;
                $processo->prazo_status = $object->prazo_status;
            }

            if (empty($processo->prazo_status)) {
                $processo->prazo_status = ProcessoAdministrativoHelper::determinePrazoStatus($processo->prazo_final);
            }

            $processo->responsavel = $context['name'];
            $processo->updated_at = $now;
            $processo->store();

            TTransaction::close();

            TToast::show('success', 'Tramitacao salva com sucesso.', 'topRight', 'far:check-circle');
            TApplication::loadPage('ProcessoAdministrativoFormView', 'onShow', ['key' => $processo->id]);
            TScript::create("Template.closeRightPanel();");
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
        }
    }

    public function onEdit($param)
    {
        try {
            TTransaction::open(self::$database);
            ProcessoAdministrativoSchemaHelper::ensureSchema();

            if (isset($param['processo_administrativo_id'])) {
                $processo = new ProcessoAdministrativo((int) $param['processo_administrativo_id']);

                if (!ProcessoAdministrativoHelper::canAccess($processo)) {
                    throw new Exception('Voce nao tem permissao para tramitar este processo.');
                }

                $context = ProcessoAdministrativoHelper::getCurrentUserContext();
                $data = new stdClass;
                $data->processo_administrativo_id = $processo->id;
                $data->usuario_responsavel = $context['name'];
                $data->data_tramitacao = date('d/m/Y H:i');
                $data->departamento_origem = $processo->departamento_atual ?: $processo->departamento_origem;
                $data->departamento_destino_nome = $processo->departamento_atual ?: $processo->departamento_destino;
                $data->prazo_tipo = $processo->prazo_tipo ?: 'Dias corridos';
                $data->prazo_status = $processo->prazo_status ?: 'No prazo';
                $data->tipo_evento = 'Despacho';
                $data->acao_executada = 'Despachar';
                $this->form->setData($data);
            }

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
        }
    }

    public function onShow($param = null)
    {
        $this->onEdit($param ?? []);
    }
}
