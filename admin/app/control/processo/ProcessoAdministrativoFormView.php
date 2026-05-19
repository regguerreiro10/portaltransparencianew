<?php

class ProcessoAdministrativoFormView extends TPage
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
        $this->renderView($param, false);
    }

    public function onPrint($param = null)
    {
        $this->renderView($param, true);
    }

    private function renderView($param, bool $printMode): void
    {
        try {
            if (empty($param['key'])) {
                throw new Exception('Processo nao informado.');
            }

            TTransaction::open(self::$database);
            ProcessoAdministrativoSchemaHelper::ensureSchema();

            $processo = new ProcessoAdministrativo((int) $param['key']);
            if (!ProcessoAdministrativoHelper::canAccess($processo)) {
                throw new Exception('Voce nao tem permissao para visualizar este processo.');
            }

            $container = new TVBox;
            $container->style = 'width:100%';

            if (!$printMode) {
                $container->add($this->buildActionBar($processo));
            }

            $container->add($this->buildHeader($processo, $printMode));
            $container->add($this->buildBodyPanel($processo));
            $container->add($this->buildAttachmentPanel($processo));
            $container->add($this->buildTimelinePanel($processo));

            parent::clearChildren();
            parent::add($container);

            if ($printMode) {
                $processo->integra_gerada_em = date('Y-m-d H:i:s');
                $processo->store();
                ProcessoAdministrativoHelper::createTramitacao(
                    $processo,
                    'Relatorio',
                    'Gerou integra',
                    'Integra do processo gerada para impressao.',
                    'Documento consolidado do processo.',
                    $processo->departamento_origem,
                    $processo->departamento_atual
                );
            }

            TTransaction::close();

            if ($printMode) {
                TScript::create('window.print();');
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
        }
    }

    private function buildActionBar(ProcessoAdministrativo $processo): TForm
    {
        $actions = new TForm('form_actions_processo_' . $processo->id);
        $actions->style = 'margin-bottom: 15px;';

        $wrapper = new TElement('div');
        $wrapper->style = 'display:flex; gap:8px; flex-wrap:wrap;';
        $actions->add($wrapper);

        $viewKey = ['key' => $processo->id];

        $btnReceber = new TButton('btn_receber_processo');
        $btnReceber->setAction(new TAction([$this, 'onReceive'], $viewKey), 'Receber');
        $btnReceber->setImage('fas:inbox #15803d');
        $actions->addField($btnReceber);
        $wrapper->add($btnReceber);

        $btnDespacho = new TButton('btn_despacho_processo');
        $btnDespacho->setAction(new TAction(['ProcessoAdministrativoTramitacaoForm', 'onShow'], ['processo_administrativo_id' => $processo->id]), 'Despachar');
        $btnDespacho->setImage('fas:file-signature #7c3aed');
        $actions->addField($btnDespacho);
        $wrapper->add($btnDespacho);

        $btnArquivar = new TButton('btn_arquivar_processo');
        $btnArquivar->setAction(new TAction([$this, 'onArchive'], $viewKey), 'Arquivar');
        $btnArquivar->setImage('fas:archive #6b7280');
        $actions->addField($btnArquivar);
        $wrapper->add($btnArquivar);

        $btnDesarquivar = new TButton('btn_desarquivar_processo');
        $btnDesarquivar->setAction(new TAction([$this, 'onUnarchive'], $viewKey), 'Desarquivar');
        $btnDesarquivar->setImage('fas:box-open #b45309');
        $actions->addField($btnDesarquivar);
        $wrapper->add($btnDesarquivar);

        $sigiloAction = $processo->nivel_sigilo === 'Sigiloso'
            ? ['label' => 'Tornar normal', 'nivel' => 'Publico', 'icon' => 'fas:lock-open #0f766e']
            : ['label' => 'Tornar sigiloso', 'nivel' => 'Sigiloso', 'icon' => 'fas:lock #b91c1c'];

        $btnSigilo = new TButton('btn_sigilo_processo');
        $btnSigilo->setAction(new TAction([$this, 'onChangeSigilo'], ['key' => $processo->id, 'nivel_sigilo' => $sigiloAction['nivel']]), $sigiloAction['label']);
        $btnSigilo->setImage($sigiloAction['icon']);
        $actions->addField($btnSigilo);
        $wrapper->add($btnSigilo);

        $btnIntegra = new TButton('btn_integra_processo');
        $btnIntegra->setAction(new TAction([$this, 'onPrint'], $viewKey), 'Gerar integra');
        $btnIntegra->setImage('fas:file-pdf #1d4ed8');
        $actions->addField($btnIntegra);
        $wrapper->add($btnIntegra);

        $btnVoltar = new TButton('btn_voltar_processo');
        $btnVoltar->setAction(new TAction(['ProcessoAdministrativoList', 'onShow']), 'Voltar');
        $btnVoltar->setImage('fas:arrow-left #000000');
        $actions->addField($btnVoltar);
        $wrapper->add($btnVoltar);

        return $actions;
    }

    private function buildHeader(ProcessoAdministrativo $processo, bool $printMode): BootstrapFormBuilder
    {
        $header = new BootstrapFormBuilder('form_view_processo_' . ($printMode ? 'print' : 'screen'));
        $header->setTagName('div');
        $header->setFormTitle($printMode ? 'Integra do processo administrativo' : 'Visualizacao do processo');

        $sigiloColor = [
            'Publico' => '#0f766e',
            'Restrito' => '#b45309',
            'Sigiloso' => '#b91c1c',
        ][$processo->nivel_sigilo] ?? '#334155';

        $sigiloBadge = "<span style='display:inline-block; padding:4px 10px; border-radius:999px; background:{$sigiloColor}; color:#fff'>{$processo->nivel_sigilo}</span>";

        $header->addFields(
            [new TLabel('Nº processo:', '', '14px', 'B', '100%'), new TTextDisplay($processo->numero_processo, '', '15px', '')],
            [new TLabel('Protocolo:', '', '14px', 'B', '100%'), new TTextDisplay($processo->numero_protocolo, '', '15px', '')],
            [new TLabel('Tipo:', '', '14px', 'B', '100%'), new TTextDisplay($processo->tipo_processo, '', '15px', '')],
            [new TLabel('Sigilo:', '', '14px', 'B', '100%'), new TTextDisplay($sigiloBadge, '', '15px', '')]
        )->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];

        $header->addFields(
            [new TLabel('Status:', '', '14px', 'B', '100%'), new TTextDisplay($processo->status, '', '15px', '')],
            [new TLabel('Leitura:', '', '14px', 'B', '100%'), new TTextDisplay($processo->status_leitura, '', '15px', '')],
            [new TLabel('Prazo:', '', '14px', 'B', '100%'), new TTextDisplay($this->formatDate($processo->prazo_final) ?: 'Sem prazo', '', '15px', '')],
            [new TLabel('Status do prazo:', '', '14px', 'B', '100%'), new TTextDisplay($processo->prazo_status ?: 'No prazo', '', '15px', '')]
        )->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];

        $header->addFields(
            [new TLabel('Criador:', '', '14px', 'B', '100%'), new TTextDisplay($processo->autor_nome ?: $processo->remetente_nome, '', '15px', '')],
            [new TLabel('Departamento origem:', '', '14px', 'B', '100%'), new TTextDisplay($processo->departamento_origem, '', '15px', '')],
            [new TLabel('Departamento atual:', '', '14px', 'B', '100%'), new TTextDisplay($processo->departamento_atual, '', '15px', '')],
            [new TLabel('Destino:', '', '14px', 'B', '100%'), new TTextDisplay($processo->departamento_destino ?: $processo->departamento_atual, '', '15px', '')]
        )->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];

        $header->addFields(
            [new TLabel('Requerente:', '', '14px', 'B', '100%'), new TTextDisplay($processo->requerente ?: '-', '', '15px', '')],
            [new TLabel('Destinatario:', '', '14px', 'B', '100%'), new TTextDisplay($processo->destinatario_nome ?: '-', '', '15px', '')],
            [new TLabel('Abertura:', '', '14px', 'B', '100%'), new TTextDisplay($this->formatDate($processo->data_abertura), '', '15px', '')],
            [new TLabel('Envio:', '', '14px', 'B', '100%'), new TTextDisplay($this->formatDateTime($processo->data_envio), '', '15px', '')]
        )->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];

        $header->addFields(
            [new TLabel('Assunto:', '', '14px', 'B', '100%'), new TTextDisplay($processo->assunto, '', '16px', '')]
        )->layout = ['col-sm-12'];

        $header->addFields(
            [new TLabel('Ementa:', '', '14px', 'B', '100%'), new TTextDisplay($processo->ementa ?: '-', '', '15px', '')]
        )->layout = ['col-sm-12'];

        return $header;
    }

    private function buildBodyPanel(ProcessoAdministrativo $processo): TPanelGroup
    {
        $bodyPanel = new TPanelGroup('Conteudo do processo');
        $body = new TElement('div');
        $body->style = 'padding:18px; line-height:1.6;';
        $body->add($processo->descricao ?: '<em>Sem descricao detalhada.</em>');

        if (!empty($processo->observacoes)) {
            $body->add("<hr><strong>Observacoes internas</strong><div style='margin-top:8px'>{$processo->observacoes}</div>");
        }

        if (!empty($processo->sigilo_motivo)) {
            $body->add("<hr><strong>Motivo do sigilo</strong><div style='margin-top:8px'>{$processo->sigilo_motivo}</div>");
        }

        $bodyPanel->add($body);
        return $bodyPanel;
    }

    private function buildAttachmentPanel(ProcessoAdministrativo $processo): TPanelGroup
    {
        $panel = new TPanelGroup('Anexos');
        $list = new TElement('div');
        $list->style = 'padding:15px;';

        $anexos = $processo->getProcessoAdministrativoAnexos();
        if ($anexos) {
            foreach ($anexos as $anexo) {
                $file = new TElement('a');
                $file->href = 'download.php?file=' . $anexo->arquivo . '&basename=' . urlencode($anexo->nome ?: basename($anexo->arquivo));
                $file->target = '_blank';
                $file->style = 'display:block; margin-bottom:8px;';
                $file->add($anexo->nome ?: basename($anexo->arquivo));
                $list->add($file);
            }
        } else {
            $list->add('Nenhum anexo cadastrado.');
        }

        $panel->add($list);
        return $panel;
    }

    private function buildTimelinePanel(ProcessoAdministrativo $processo): TPanelGroup
    {
        $panel = new TPanelGroup('Historico e timeline');
        $timeline = new TElement('div');
        $timeline->style = 'padding:18px;';

        foreach ($processo->getProcessoAdministrativoTramitacoes() as $item) {
            $box = new TElement('div');
            $box->style = 'padding:12px 14px; border-left:4px solid #0f4c81; background:#f8fafc; margin-bottom:12px; border-radius:8px;';

            $date = $this->formatDateTime($item->data_tramitacao);
            $title = $item->acao_executada ?: $item->tipo_evento;
            $box->add("<strong>{$title}</strong> - {$date}<br>");
            $box->add("<span style='color:#475569'>{$item->usuario_responsavel}</span>");

            if (!empty($item->departamento_origem) || !empty($item->departamento_destino)) {
                $origem = $item->departamento_origem ?: '-';
                $destino = $item->departamento_destino ?: '-';
                $box->add("<div style='margin-top:6px; color:#64748b'>Origem: {$origem} | Destino: {$destino}</div>");
            }

            if (!empty($item->despacho_texto)) {
                $box->add("<div style='margin-top:8px; color:#334155'>{$item->despacho_texto}</div>");
            }

            if (!empty($item->anexo_descricao)) {
                $box->add("<div style='margin-top:8px; color:#0f766e'><strong>Anexos:</strong> {$item->anexo_descricao}</div>");
            }

            if (!empty($item->observacao)) {
                $box->add("<div style='margin-top:8px; color:#475569'>{$item->observacao}</div>");
            }

            $timeline->add($box);
        }

        $panel->add($timeline);
        return $panel;
    }

    public static function onReceive($param)
    {
        self::updateStatus((int) $param['key'], 'Recebido', 'Lido', 'Receber processo', 'Processo recebido pelo departamento atual.');
    }

    public static function onArchive($param)
    {
        self::updateStatus((int) $param['key'], 'Arquivado', 'Arquivado', 'Arquivar', 'Processo arquivado.');
    }

    public static function onUnarchive($param)
    {
        self::updateStatus((int) $param['key'], 'Em andamento', 'Lido', 'Desarquivar', 'Processo desarquivado e devolvido ao fluxo.');
    }

    public static function onChangeSigilo($param)
    {
        try {
            TTransaction::open(self::$database);
            ProcessoAdministrativoSchemaHelper::ensureSchema();

            $processo = new ProcessoAdministrativo((int) $param['key']);
            if (!ProcessoAdministrativoHelper::canChangeSigilo($processo)) {
                throw new Exception('Voce nao tem permissao para alterar o sigilo deste processo.');
            }

            $context = ProcessoAdministrativoHelper::getCurrentUserContext();
            $oldLevel = $processo->nivel_sigilo;
            $processo->nivel_sigilo = $param['nivel_sigilo'] ?? 'Publico';
            $processo->sigilo_alterado_em = date('Y-m-d H:i:s');
            $processo->sigilo_alterado_por = $context['name'];
            if ($processo->nivel_sigilo === 'Sigiloso' && empty($processo->sigilo_motivo)) {
                $processo->sigilo_motivo = 'Processo marcado como sigiloso.';
            }
            $processo->updated_at = date('Y-m-d H:i:s');
            $processo->store();

            ProcessoAdministrativoHelper::createTramitacao(
                $processo,
                'Sigilo',
                'Alteracao de sigilo',
                "Sigilo alterado de {$oldLevel} para {$processo->nivel_sigilo}.",
                $processo->sigilo_motivo ?: 'Alteracao de sigilo registrada no sistema.',
                $processo->departamento_origem,
                $processo->departamento_atual
            );

            TTransaction::close();
            TApplication::loadPage(__CLASS__, 'onShow', ['key' => $processo->id]);
            TToast::show('success', 'Sigilo atualizado.', 'topRight', 'far:check-circle');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
        }
    }

    private static function updateStatus(int $processoId, string $status, string $statusLeitura, string $acao, string $descricao): void
    {
        try {
            TTransaction::open(self::$database);
            ProcessoAdministrativoSchemaHelper::ensureSchema();

            $processo = new ProcessoAdministrativo($processoId);
            if (!ProcessoAdministrativoHelper::canAccess($processo)) {
                throw new Exception('Voce nao tem permissao para alterar este processo.');
            }

            $processo->status = $status;
            $processo->status_leitura = $statusLeitura;
            $processo->updated_at = date('Y-m-d H:i:s');

            if ($status === 'Recebido') {
                $processo->lido_em = date('Y-m-d H:i:s');
            }

            if ($status === 'Arquivado') {
                $processo->arquivado_em = date('Y-m-d H:i:s');
                $processo->prazo_status = 'Arquivado';
            }

            if ($acao === 'Desarquivar') {
                $processo->arquivado_em = null;
                $processo->prazo_status = ProcessoAdministrativoHelper::determinePrazoStatus($processo->prazo_final);
            }

            $processo->store();

            ProcessoAdministrativoHelper::createTramitacao(
                $processo,
                'Atualizacao',
                $acao,
                $descricao,
                $descricao,
                $processo->departamento_origem,
                $processo->departamento_atual
            );

            TTransaction::close();
            TApplication::loadPage(__CLASS__, 'onShow', ['key' => $processoId]);
            TToast::show('success', 'Processo atualizado.', 'topRight', 'far:check-circle');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
        }
    }

    private function formatDate(?string $date): string
    {
        if (empty($date)) {
            return '';
        }

        return TDate::convertToMask(substr($date, 0, 10), 'yyyy-mm-dd', 'dd/mm/yyyy');
    }

    private function formatDateTime(?string $date): string
    {
        if (empty($date)) {
            return '';
        }

        $mask = strlen($date) > 10 ? 'yyyy-mm-dd hh:ii:ss' : 'yyyy-mm-dd';
        $output = strlen($date) > 10 ? 'dd/mm/yyyy hh:ii' : 'dd/mm/yyyy';

        return strlen($date) > 10
            ? TDateTime::convertToMask($date, $mask, $output)
            : TDate::convertToMask($date, $mask, $output);
    }
}
