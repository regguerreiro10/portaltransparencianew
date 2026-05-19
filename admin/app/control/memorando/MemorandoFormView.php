<?php

class MemorandoFormView extends TPage
{
    private static $database = 'minierp';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }
    }

    public function onShow($param = null)
    {
        try {
            if (empty($param['key'])) {
                throw new Exception('Memorando nao informado.');
            }

            TTransaction::open(self::$database);
            MemorandoSchemaHelper::ensureSchema();

            $memorando = new Memorando((int) $param['key']);
            if (!MemorandoHelper::canAccessMemorando($memorando)) {
                throw new Exception('Voce nao tem permissao para visualizar este memorando.');
            }

            $container = new TVBox;
            $container->style = 'width:100%';

            $actions = new TForm('form_actions_memorando_view');
            $actions->style = 'margin-bottom: 15px;';

            $viewKey = ['key' => $memorando->id];

            $btnLido = new TButton('btn_lido_memorando');
            $btnLido->setAction(new TAction([$this, 'onMarkAsRead'], $viewKey), 'Marcar como lido');
            $btnLido->setImage('fas:check #15803d');
            $actions->add($btnLido);

            $btnResponder = new TButton('btn_responder_memorando');
            $btnResponder->setAction(new TAction(['MemorandoForm', 'onShow'], ['responder_id' => $memorando->id]), 'Responder');
            $btnResponder->setImage('fas:reply #1d4ed8');
            $actions->add($btnResponder);

            $btnEncaminhar = new TButton('btn_encaminhar_memorando');
            $btnEncaminhar->setAction(new TAction(['MemorandoForm', 'onShow'], ['encaminhar_id' => $memorando->id]), 'Encaminhar');
            $btnEncaminhar->setImage('fas:share #7c3aed');
            $actions->add($btnEncaminhar);

            $btnArquivar = new TButton('btn_arquivar_memorando');
            $btnArquivar->setAction(new TAction([$this, 'onArchive'], $viewKey), 'Arquivar');
            $btnArquivar->setImage('fas:archive #6b7280');
            $actions->add($btnArquivar);

            $btnRecuperar = new TButton('btn_recuperar_memorando');
            $btnRecuperar->setAction(new TAction([$this, 'onRecall'], $viewKey), 'Recuperar');
            $btnRecuperar->setImage('fas:undo #b45309');
            $actions->add($btnRecuperar);

            $btnProcesso = new TButton('btn_processo_memorando');
            $btnProcesso->setAction(new TAction([$this, 'onTransformToProcess'], $viewKey), 'Marcar para processo');
            $btnProcesso->setImage('fas:project-diagram #0f766e');
            $actions->add($btnProcesso);

            $btnVoltar = new TButton('btn_voltar_memorando');
            $btnVoltar->setAction(new TAction(['MemorandoList', 'onShow']), 'Voltar');
            $btnVoltar->setImage('fas:arrow-left #000000');
            $actions->add($btnVoltar);
            $actions->setFields([
                $btnLido,
                $btnResponder,
                $btnEncaminhar,
                $btnArquivar,
                $btnRecuperar,
                $btnProcesso,
                $btnVoltar,
            ]);

            $header = new BootstrapFormBuilder('form_view_memorando');
            $header->setTagName('div');
            $header->setFormTitle('Visualizacao do memorando');

            $header->addFields(
                [new TLabel('Numero:', '', '14px', 'B', '100%'), new TTextDisplay($memorando->numero_memorando, '', '15px', '')],
                [new TLabel('Status:', '', '14px', 'B', '100%'), new TTextDisplay($memorando->status, '', '15px', '')],
                [new TLabel('Data:', '', '14px', 'B', '100%'), new TTextDisplay(TDateTime::convertToMask($memorando->data_memorando, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii'), '', '15px', '')]
            )->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

            $header->addFields(
                [new TLabel('Remetente:', '', '14px', 'B', '100%'), new TTextDisplay($memorando->remetente_nome, '', '15px', '')],
                [new TLabel('Departamento origem:', '', '14px', 'B', '100%'), new TTextDisplay($memorando->departamento_origem_nome, '', '15px', '')]
            )->layout = ['col-sm-6', 'col-sm-6'];

            $header->addFields(
                [new TLabel('Destinatario(s):', '', '14px', 'B', '100%'), new TTextDisplay($memorando->destinatarios_resumo, '', '15px', '')],
                [new TLabel('Departamento(s) destino:', '', '14px', 'B', '100%'), new TTextDisplay($memorando->departamentos_destino_resumo, '', '15px', '')]
            )->layout = ['col-sm-6', 'col-sm-6'];

            $header->addFields(
                [new TLabel('Assunto:', '', '14px', 'B', '100%'), new TTextDisplay($memorando->assunto, '', '16px', '')]
            )->layout = ['col-sm-12'];

            $bodyPanel = new TPanelGroup('Conteudo');
            $bodyElement = new TElement('div');
            $bodyElement->style = 'padding:18px; line-height:1.6;';
            $bodyElement->add($memorando->texto_memorando ?: '<em>Sem texto.</em>');
            $bodyPanel->add($bodyElement);

            $attachmentsPanel = new TPanelGroup('Anexos');
            $attachmentsList = new TElement('div');
            $attachmentsList->style = 'padding:15px;';
            $anexos = $memorando->getMemorandoAnexos();
            if ($anexos) {
                foreach ($anexos as $anexo) {
                    $file = new TElement('a');
                    $file->href = 'download.php?file=' . $anexo->arquivo . '&basename=' . urlencode($anexo->nome);
                    $file->target = '_blank';
                    $file->style = 'display:block; margin-bottom:8px;';
                    $file->add($anexo->nome ?: basename($anexo->arquivo));
                    $attachmentsList->add($file);
                }
            } else {
                $attachmentsList->add('Nenhum anexo cadastrado.');
            }
            $attachmentsPanel->add($attachmentsList);

            $timelinePanel = new TPanelGroup('Historico de tramitacao');
            $timeline = new TElement('div');
            $timeline->style = 'padding:18px;';
            foreach ($memorando->getMemorandoTramitacoes() as $item) {
                $box = new TElement('div');
                $box->style = 'padding:12px 14px; border-left:4px solid #2563eb; background:#f8fafc; margin-bottom:12px; border-radius:8px;';
                $date = TDateTime::convertToMask($item->data_evento, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii');
                $box->add("<strong>{$item->acao}</strong> - {$date}<br>");
                $box->add("<span style='color:#475569'>{$item->usuario_nome}</span>");
                if (!empty($item->departamento_nome)) {
                    $box->add(" <span style='color:#94a3b8'>/ {$item->departamento_nome}</span>");
                }
                if (!empty($item->descricao)) {
                    $box->add("<div style='margin-top:6px; color:#334155'>{$item->descricao}</div>");
                }
                $timeline->add($box);
            }
            $timelinePanel->add($timeline);

            $container->add($actions);
            $container->add($header);
            $container->add($bodyPanel);
            $container->add($attachmentsPanel);
            $container->add($timelinePanel);

            parent::clearChildren();
            parent::add($container);

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
        }
    }

    public static function onMarkAsRead($param)
    {
        self::updateRecipientStatus((int) $param['key'], 'Lido', 'Marcou o memorando como lido.', 'lido_em');
    }

    public static function onArchive($param)
    {
        self::updateRecipientStatus((int) $param['key'], 'Arquivado', 'Arquivou o memorando.', 'arquivado_em');
    }

    public static function onRecall($param)
    {
        try {
            TTransaction::open(self::$database);
            MemorandoSchemaHelper::ensureSchema();

            $memorando = new Memorando((int) $param['key']);
            if (!MemorandoHelper::canEditMemorando($memorando)) {
                throw new Exception('Voce nao tem permissao para recuperar este memorando.');
            }

            $memorando->status = 'Recuperado';
            MemorandoHelper::applyStatusColor($memorando);
            $memorando->updated_at = date('Y-m-d H:i:s');
            $memorando->store();

            foreach ($memorando->getMemorandoDestinatarios() as $destinatario) {
                $destinatario->status = 'Recuperado';
                $destinatario->updated_at = date('Y-m-d H:i:s');
                $destinatario->store();
            }

            MemorandoHelper::createTramitacao($memorando->id, 'Recuperado', 'Recuperado', 'Memorando recuperado durante a tramitacao.');

            TTransaction::close();
            TApplication::loadPage(__CLASS__, 'onShow', ['key' => $memorando->id]);
            TToast::show('success', 'Memorando recuperado.', 'topRight', 'far:check-circle');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onTransformToProcess($param)
    {
        try {
            TTransaction::open(self::$database);
            MemorandoSchemaHelper::ensureSchema();

            $memorando = new Memorando((int) $param['key']);
            if (!MemorandoHelper::canEditMemorando($memorando)) {
                throw new Exception('Voce nao tem permissao para alterar este memorando.');
            }

            $memorando->pode_virar_processo = 'Y';
            if (empty($memorando->processo_referencia)) {
                $memorando->processo_referencia = 'PROC-PENDENTE-' . str_pad((string) $memorando->id, 6, '0', STR_PAD_LEFT);
            }
            $memorando->updated_at = date('Y-m-d H:i:s');
            $memorando->store();

            MemorandoHelper::createTramitacao($memorando->id, 'Processo', $memorando->status, 'Memorando marcado para transformacao em processo.');

            TTransaction::close();
            TApplication::loadPage(__CLASS__, 'onShow', ['key' => $memorando->id]);
            TToast::show('success', 'Memorando marcado para processo.', 'topRight', 'far:check-circle');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    private static function updateRecipientStatus(int $memorandoId, string $status, string $descricao, string $dateField): void
    {
        try {
            TTransaction::open(self::$database);
            MemorandoSchemaHelper::ensureSchema();

            $memorando = new Memorando($memorandoId);
            if (!MemorandoHelper::canAccessMemorando($memorando)) {
                throw new Exception('Voce nao tem permissao para alterar este memorando.');
            }

            $context = MemorandoHelper::getCurrentUserContext();
            $updated = false;

            foreach ($memorando->getMemorandoDestinatarios() as $destinatario) {
                if ((int) $destinatario->system_users_id === (int) $context['user_id']) {
                    $destinatario->status = $status;
                    $destinatario->{$dateField} = date('Y-m-d H:i:s');
                    if ($status === 'Respondido') {
                        $destinatario->respondido_em = date('Y-m-d H:i:s');
                    }
                    $destinatario->updated_at = date('Y-m-d H:i:s');
                    $destinatario->store();
                    MemorandoHelper::createTramitacao($memorando->id, $status, $status, $descricao, $destinatario->id);
                    $updated = true;
                }
            }

            if (!$updated && MemorandoHelper::canEditMemorando($memorando)) {
                $memorando->status = $status;
                MemorandoHelper::applyStatusColor($memorando);
                if ($status === 'Arquivado') {
                    $memorando->arquivado_em = date('Y-m-d H:i:s');
                }
                if ($status === 'Lido') {
                    $memorando->lido_em = date('Y-m-d H:i:s');
                }
                $memorando->updated_at = date('Y-m-d H:i:s');
                $memorando->store();
                MemorandoHelper::createTramitacao($memorando->id, $status, $status, $descricao);
            } else {
                MemorandoHelper::updateOverallStatus($memorando);
            }

            TTransaction::close();
            TApplication::loadPage(__CLASS__, 'onShow', ['key' => $memorandoId]);
            TToast::show('success', 'Status atualizado.', 'topRight', 'far:check-circle');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
