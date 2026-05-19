<?php

class ProcessoAdministrativoList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $datagrid_form;
    private $builder_datagrid_check_all;
    private static $database = 'minierp';
    private static $activeRecord = 'ProcessoAdministrativo';
    private static $primaryKey = 'id';
    private static $formName = 'formList_ProcessoAdministrativo';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $numero_processo = new TEntry('numero_processo');
        $numero_protocolo = new TEntry('numero_protocolo');
        $tipo_processo = new TCombo('tipo_processo');
        $texto_busca = new TEntry('texto_busca');
        $conteudo_arquivo = new TEntry('conteudo_arquivo');
        $status = new TCombo('status');
        $status_leitura = new TCombo('status_leitura');
        $prazo_status = new TCombo('prazo_status');
        $nivel_sigilo = new TCombo('nivel_sigilo');
        $autor_nome = new TEntry('autor_nome');
        $departamento = new TEntry('departamento');
        $data_inicial = new TDate('data_inicial');
        $data_final = new TDate('data_final');
        $acao_lote = new TCombo('acao_lote');

        TTransaction::open(self::$database);
        $departamento_lote_id = new TDBCombo('departamento_lote_id', 'minierp', 'SystemDepartamento', 'id', '{nome}', 'nome asc');
        TTransaction::close();

        $tipo_processo->addItems(['' => 'Todos'] + ProcessoAdministrativoHelper::getTiposProcesso());
        $status->addItems(ProcessoAdministrativoHelper::getSituacoes());
        $status_leitura->addItems(ProcessoAdministrativoHelper::getStatusLeitura());
        $prazo_status->addItems(ProcessoAdministrativoHelper::getPrazoStatusOptions());
        $nivel_sigilo->addItems(ProcessoAdministrativoHelper::getSigiloOptions(true));
        $acao_lote->addItems([
            '' => 'Acoes em lote',
            'despachar' => 'Despachar',
            'arquivar' => 'Arquivar',
            'desarquivar' => 'Desarquivar',
            'carimbar' => 'Carimbar',
            'interromper_prazo' => 'Interromper prazo',
        ]);

        foreach ([$tipo_processo, $status, $status_leitura, $prazo_status, $nivel_sigilo, $acao_lote, $departamento_lote_id] as $combo) {
            $combo->enableSearch();
            $combo->setSize('100%');
        }

        foreach ([$numero_processo, $numero_protocolo, $texto_busca, $conteudo_arquivo, $autor_nome, $departamento] as $entry) {
            $entry->setSize('100%');
            $entry->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        }

        foreach ([$tipo_processo, $status, $status_leitura, $prazo_status, $nivel_sigilo] as $field) {
            $field->setChangeAction(new TAction([$this, 'onSearch'], ['static' => '1']));
        }

        $data_inicial->setSize('100%');
        $data_final->setSize('100%');
        $data_inicial->setMask('dd/mm/yyyy');
        $data_inicial->setDatabaseMask('yyyy-mm-dd');
        $data_final->setMask('dd/mm/yyyy');
        $data_final->setDatabaseMask('yyyy-mm-dd');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setId(__CLASS__ . '_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(400);

        $this->builder_datagrid_check_all = new TCheckButton('builder_datagrid_check_all');
        $this->builder_datagrid_check_all->setIndexValue('on');
        $this->builder_datagrid_check_all->onclick = "Builder.checkAll(this)";
        $this->builder_datagrid_check_all->style = 'cursor:pointer';
        $this->builder_datagrid_check_all->setProperty('class', 'filled-in');
        $this->builder_datagrid_check_all->id = 'builder_datagrid_check_all';

        $label = new TLabel('');
        $label->style = 'margin:0';
        $label->class = 'checklist-label';
        $this->builder_datagrid_check_all->after($label);
        $label->for = 'builder_datagrid_check_all';

        $this->datagrid->addColumn(new TDataGridColumn('builder_datagrid_check', $this->builder_datagrid_check_all, 'center', '2%'));
        $this->datagrid->addColumn(new TDataGridColumn('numero_processo', 'Nº processo', 'left', '11%'));
        $this->datagrid->addColumn(new TDataGridColumn('tipo_processo', 'Tipo', 'left', '9%'));
        $this->datagrid->addColumn(new TDataGridColumn('numero_protocolo', 'Protocolo', 'left', '11%'));

        $columnAssunto = new TDataGridColumn('assunto', 'Assunto / ementa', 'left');
        $columnAutor = new TDataGridColumn('autor_nome', 'Autor', 'left', '9%');
        $columnDepartamento = new TDataGridColumn('departamento_atual', 'Departamento', 'left', '10%');
        $columnSituacao = new TDataGridColumn('status', 'Situacao', 'center', '9%');
        $columnEnvio = new TDataGridColumn('data_envio', 'Envio', 'center', '9%');
        $columnPrazo = new TDataGridColumn('prazo_final', 'Prazo', 'center', '8%');
        $columnLeitura = new TDataGridColumn('status_leitura', 'Leitura', 'center', '8%');
        $columnArquivo = new TDataGridColumn('arquivo_principal', 'Arquivo principal', 'center', '8%');

        $columnAssunto->setTransformer(function ($value, $object) {
            $sigilo = '';
            if (!empty($object->nivel_sigilo) && $object->nivel_sigilo !== 'Publico') {
                $color = $object->nivel_sigilo === 'Sigiloso' ? '#b91c1c' : '#b45309';
                $sigilo = "<span style='display:inline-block; margin-left:8px; padding:2px 8px; border-radius:999px; background:{$color}; color:#fff; font-size:11px;'>{$object->nivel_sigilo}</span>";
            }

            $extra = !empty($object->ementa) ? '<br><small style="color:#64748b">' . $object->ementa . '</small>' : '';
            $prefix = $object->status_leitura === 'Nao lido'
                ? "<span style='display:inline-block; width:9px; height:9px; border-radius:50%; background:#b91c1c; margin-right:8px;'></span>"
                : '';
            $subject = $object->status_leitura === 'Nao lido' ? '<strong>' . $value . '</strong>' : $value;

            return $prefix . $subject . $sigilo . $extra;
        });

        $columnEnvio->setTransformer(function ($value) {
            return !empty($value) ? TDateTime::convertToMask($value, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii') : '';
        });

        $columnSituacao->setTransformer(function ($value) {
            $colors = [
                'Rascunho' => '#7c3aed',
                'Enviado' => '#1d4ed8',
                'Recebido' => '#15803d',
                'Despachado' => '#0f4c81',
                'Devolvido' => '#b45309',
                'Arquivado' => '#6b7280',
                'Concluido' => '#0f766e',
            ];
            $color = $colors[$value] ?? '#334155';
            return "<span style='display:inline-block; padding:4px 10px; border-radius:999px; background:{$color}; color:#fff; font-size:12px;'>{$value}</span>";
        });

        $columnPrazo->setTransformer(function ($value, $object) {
            $label = !empty($value) ? TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy') : 'Sem prazo';
            $statusPrazo = $object->prazo_status ?: 'No prazo';
            $colors = [
                'No prazo' => '#0f766e',
                'Proximo do vencimento' => '#b45309',
                'Vencido' => '#b91c1c',
                'Interrompido' => '#6b7280',
                'Prorrogado' => '#1d4ed8',
                'Arquivado' => '#475569',
            ];
            $color = $colors[$statusPrazo] ?? '#334155';
            return "<span style='display:inline-block; color:{$color}; font-weight:600'>{$label}</span>";
        });

        $columnLeitura->setTransformer(function ($value) {
            $colors = [
                'Nao lido' => '#b91c1c',
                'Lido' => '#15803d',
                'Rascunho' => '#7c3aed',
                'Arquivado' => '#6b7280',
            ];
            $color = $colors[$value] ?? '#334155';
            return "<span style='display:inline-block; padding:4px 10px; border-radius:999px; background:{$color}; color:#fff; font-size:12px;'>{$value}</span>";
        });

        $columnArquivo->setTransformer(function ($value, $object) {
            $anexos = $object->getProcessoAdministrativoAnexos();
            if ($anexos && isset($anexos[0])) {
                $anexo = $anexos[0];
                return '<a href="download.php?file=' . $anexo->arquivo . '&basename=' . urlencode($anexo->nome ?: basename($anexo->arquivo)) . '" target="_blank">Abrir</a>';
            }

            return '<span style="color:#94a3b8">Sem arquivo</span>';
        });

        $this->datagrid->addColumn($columnAssunto);
        $this->datagrid->addColumn($columnAutor);
        $this->datagrid->addColumn($columnDepartamento);
        $this->datagrid->addColumn($columnSituacao);
        $this->datagrid->addColumn($columnEnvio);
        $this->datagrid->addColumn($columnPrazo);
        $this->datagrid->addColumn($columnLeitura);
        $this->datagrid->addColumn($columnArquivo);

        $actionView = new TDataGridAction(['ProcessoAdministrativoFormView', 'onShow']);
        $actionView->setLabel('Abrir');
        $actionView->setImage('far:eye #0f4c81');
        $actionView->setUseButton(false);
        $actionView->setButtonClass('btn btn-default btn-sm');
        $actionView->setField(self::$primaryKey);
        $this->datagrid->addAction($actionView);

        $actionDespacho = new TDataGridAction(['ProcessoAdministrativoTramitacaoForm', 'onShow']);
        $actionDespacho->setLabel('Despachar');
        $actionDespacho->setImage('fas:file-signature #7c3aed');
        $actionDespacho->setUseButton(false);
        $actionDespacho->setButtonClass('btn btn-default btn-sm');
        $actionDespacho->setField(self::$primaryKey);
        $actionDespacho->setParameter('processo_administrativo_id', '{id}');
        $this->datagrid->addAction($actionDespacho);

        $actionTimeline = new TDataGridAction(['ProcessoAdministrativoFormView', 'onShow']);
        $actionTimeline->setLabel('Timeline');
        $actionTimeline->setImage('fas:stream #0f766e');
        $actionTimeline->setUseButton(false);
        $actionTimeline->setButtonClass('btn btn-default btn-sm');
        $actionTimeline->setField(self::$primaryKey);
        $this->datagrid->addAction($actionTimeline);

        $this->datagrid->createModel();

        $this->datagrid_form = new TForm('datagrid_' . self::$formName);
        $this->datagrid_form->onsubmit = 'return false';
        foreach ([$numero_processo, $numero_protocolo, $tipo_processo, $texto_busca, $conteudo_arquivo, $status, $status_leitura, $prazo_status, $nivel_sigilo, $autor_nome, $departamento, $data_inicial, $data_final, $acao_lote, $departamento_lote_id] as $field) {
            $this->datagrid_form->addField($field);
        }
        $this->datagrid_form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $numero_processo));
        $tr->add(TElement::tag('td', $tipo_processo));
        $tr->add(TElement::tag('td', $numero_protocolo));
        $tr->add(TElement::tag('td', $texto_busca));
        $tr->add(TElement::tag('td', $autor_nome));
        $tr->add(TElement::tag('td', $departamento));
        $tr->add(TElement::tag('td', $status));
        $tr->add(TElement::tag('td', $data_inicial));
        $tr->add(TElement::tag('td', $prazo_status));
        $tr->add(TElement::tag('td', $status_leitura));
        $tr->add(TElement::tag('td', $conteudo_arquivo));

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup('Caixa de Entrada de Processos');
        $panel->datagrid = 'datagrid-container';
        $panel->getBody()->class .= ' table-responsive';
        $panel->addFooter($this->pageNavigation);

        $legend = new TElement('div');
        $legend->style = 'padding:10px 14px; margin-bottom:10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; color:#334155;';
        $legend->add('Legenda: ');
        $legend->add("<span style='display:inline-block; padding:2px 8px; border-radius:999px; background:#b91c1c; color:#fff; margin-right:8px;'>Nao lido</span>");
        $legend->add("<span style='display:inline-block; padding:2px 8px; border-radius:999px; background:#15803d; color:#fff; margin-right:8px;'>Lido</span>");
        $legend->add("<span style='display:inline-block; padding:2px 8px; border-radius:999px; background:#7c3aed; color:#fff; margin-right:8px;'>Rascunho</span>");
        $legend->add("<span style='display:inline-block; padding:2px 8px; border-radius:999px; background:#6b7280; color:#fff; margin-right:8px;'>Arquivado</span>");
        $legend->add("<span style='margin-left:12px; color:#64748b'>O ponto vermelho destaca processos nao lidos.</span>");

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headLeft = new TElement('div');
        $headLeft->class = ' datagrid-header-actions-left-actions ';
        $headRight = new TElement('div');
        $headRight->class = ' datagrid-header-actions-left-actions ';
        $headerActions->add($headLeft);
        $headerActions->add($headRight);
        $this->datagrid_form->add($headerActions);

        $buttonInbox = new TButton('button_inbox_processo_administrativo');
        $buttonInbox->setAction(new TAction(['ProcessoAdministrativoList', 'onShow']), 'Caixa de entrada');
        $buttonInbox->setImage('fas:inbox #0f766e');
        $this->datagrid_form->addField($buttonInbox);
        $headLeft->add($buttonInbox);

        $buttonCadastrar = new TButton('button_cadastrar_processo_administrativo');
        $buttonCadastrar->setAction(new TAction(['ProcessoAdministrativoForm', 'onShow']), 'Novo processo');
        $buttonCadastrar->setImage('fas:plus #69aa46');
        $this->datagrid_form->addField($buttonCadastrar);
        $headLeft->add($buttonCadastrar);

        $buttonRelatorio = new TButton('button_relatorio_processo_administrativo');
        $buttonRelatorio->setAction(new TAction([$this, 'onReport']), 'Relatorio');
        $buttonRelatorio->setImage('fas:file-alt #1d4ed8');
        $this->datagrid_form->addField($buttonRelatorio);
        $headLeft->add($buttonRelatorio);

        $buttonLote = new TButton('button_lote_processo_administrativo');
        $buttonLote->setAction(new TAction([$this, 'onBatchAction']), 'Aplicar em lote');
        $buttonLote->setImage('fas:tasks #7c3aed');
        $this->datagrid_form->addField($buttonLote);
        $headLeft->add($buttonLote);

        $headLeft->add($acao_lote);
        $headLeft->add($departamento_lote_id);

        $buttonLimpar = new TButton('button_limpar_filtros_processo_administrativo');
        $buttonLimpar->setAction(new TAction([$this, 'onClearFilters']), 'Limpar filtros');
        $buttonLimpar->setImage('fas:broom #dd5a43');
        $this->datagrid_form->addField($buttonLimpar);
        $headRight->add($buttonLimpar);

        $headRight->add($nivel_sigilo);

        $panel->add($legend);
        $panel->add($this->datagrid_form);
        $this->datagrid_form->add($this->datagrid);

        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(['Processo administrativo', 'Caixa de Entrada de Processos']));
        }
        $container->add($panel);

        parent::add($container);
    }

    public function onClearFilters($param = null)
    {
        TSession::setValue(__CLASS__ . '_filter_data', null);
        $this->datagrid_form->clear();
        TSession::setValue(__CLASS__ . '_selected_ids', []);
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onSearch($param = null)
    {
        $data = $this->datagrid_form->getData();
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public function onBatchAction($param = null)
    {
        try {
            TTransaction::open(self::$database);
            ProcessoAdministrativoSchemaHelper::ensureSchema();

            $selectedIds = $param['builder_datagrid_check'] ?? [];
            if (empty($selectedIds)) {
                throw new Exception('Selecione ao menos um processo para aplicar a acao em lote.');
            }

            $action = $param['acao_lote'] ?? '';
            if (empty($action)) {
                throw new Exception('Selecione a acao em lote desejada.');
            }

            $departmentId = !empty($param['departamento_lote_id']) ? (int) $param['departamento_lote_id'] : null;
            $departmentName = ProcessoAdministrativoHelper::resolveDepartmentName($departmentId);

            foreach ($selectedIds as $id) {
                $processo = new ProcessoAdministrativo((int) $id);
                if (!ProcessoAdministrativoHelper::canAccess($processo)) {
                    continue;
                }

                switch ($action) {
                    case 'despachar':
                        if (empty($departmentId)) {
                            throw new Exception('Informe o departamento de destino para despachar em lote.');
                        }
                        $processo->departamento_atual_id = $departmentId;
                        $processo->departamento_atual = $departmentName;
                        $processo->departamento_destino_id = $departmentId;
                        $processo->departamento_destino = $departmentName;
                        $processo->status = 'Despachado';
                        $processo->status_leitura = 'Nao lido';
                        $mensagem = 'Despacho em lote para o departamento ' . $departmentName . '.';
                        break;

                    case 'arquivar':
                        $processo->status = 'Arquivado';
                        $processo->status_leitura = 'Arquivado';
                        $processo->prazo_status = 'Arquivado';
                        $processo->arquivado_em = date('Y-m-d H:i:s');
                        $mensagem = 'Processo arquivado em lote.';
                        break;

                    case 'desarquivar':
                        $processo->status = 'Em andamento';
                        $processo->status_leitura = 'Lido';
                        $processo->arquivado_em = null;
                        $processo->prazo_status = ProcessoAdministrativoHelper::determinePrazoStatus($processo->prazo_final);
                        $mensagem = 'Processo desarquivado em lote.';
                        break;

                    case 'carimbar':
                        if (stripos((string) $processo->assunto, '[CARIMBADO]') === false) {
                            $processo->assunto = '[CARIMBADO] ' . $processo->assunto;
                        }
                        $mensagem = 'Processo carimbado em lote.';
                        break;

                    case 'interromper_prazo':
                        $processo->prazo_status = 'Interrompido';
                        $processo->prazo_interrompido_em = date('Y-m-d H:i:s');
                        $mensagem = 'Prazo interrompido em lote.';
                        break;

                    default:
                        throw new Exception('Acao em lote invalida.');
                }

                $processo->updated_at = date('Y-m-d H:i:s');
                $processo->store();

                ProcessoAdministrativoHelper::createTramitacao(
                    $processo,
                    'Lote',
                    ucfirst(str_replace('_', ' ', $action)),
                    $mensagem,
                    $mensagem,
                    $processo->departamento_origem,
                    $processo->departamento_atual
                );
            }

            TTransaction::close();
            TToast::show('success', 'Acao em lote aplicada com sucesso.', 'topRight', 'far:check-circle');
            $this->onReload(['offset' => 0, 'first_page' => 1]);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
        }
    }

    public function onReport($param = null)
    {
        try {
            TTransaction::open(self::$database);
            ProcessoAdministrativoSchemaHelper::ensureSchema();

            $repository = new TRepository(self::$activeRecord);
            $criteria = $this->buildCriteria([], false);
            $objects = $repository->load($criteria, false);

            if (!$objects) {
                throw new Exception('Nenhum processo encontrado para o relatorio atual.');
            }

            $output = 'app/output/' . uniqid('processos_') . '.html';
            $html = [];
            $html[] = '<html><head><meta charset="utf-8"><title>Relatorio - Caixa de Entrada de Processos</title></head><body style="font-family:Arial,sans-serif; padding:20px">';
            $html[] = '<h2>Caixa de Entrada de Processos</h2>';
            $html[] = '<table border="1" cellspacing="0" cellpadding="8" width="100%">';
            $html[] = '<tr><th>Nº Processo</th><th>Protocolo</th><th>Tipo</th><th>Assunto</th><th>Autor</th><th>Departamento</th><th>Status</th><th>Prazo</th></tr>';

            foreach ($objects as $object) {
                $html[] = '<tr>'
                    . '<td>' . $object->numero_processo . '</td>'
                    . '<td>' . $object->numero_protocolo . '</td>'
                    . '<td>' . $object->tipo_processo . '</td>'
                    . '<td>' . $object->assunto . '</td>'
                    . '<td>' . $object->autor_nome . '</td>'
                    . '<td>' . $object->departamento_atual . '</td>'
                    . '<td>' . $object->status . '</td>'
                    . '<td>' . (!empty($object->prazo_final) ? TDate::convertToMask($object->prazo_final, 'yyyy-mm-dd', 'dd/mm/yyyy') : '-') . '</td>'
                    . '</tr>';
            }

            $html[] = '</table></body></html>';
            file_put_contents($output, implode('', $html));

            TTransaction::close();
            TPage::openFile($output);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
        }
    }

    public function onReload($param = null)
    {
        try {
            TTransaction::open(self::$database);
            ProcessoAdministrativoSchemaHelper::ensureSchema();

            $repository = new TRepository(self::$activeRecord);
            $criteria = $this->buildCriteria($param ?? [], true);
            $objects = $repository->load($criteria, false);
            $this->datagrid->clear();

            if ($objects) {
                foreach ($objects as $object) {
                    $newPrazoStatus = ProcessoAdministrativoHelper::determinePrazoStatus($object->prazo_final, $object->prazo_status);
                    if ($newPrazoStatus !== $object->prazo_status && !in_array($object->prazo_status, ['Arquivado', 'Interrompido'], true)) {
                        $object->prazo_status = $newPrazoStatus;
                        $object->store();
                    }

                    $check = new TCheckGroup('builder_datagrid_check');
                    $check->addItems([$object->id => '']);
                    $check->setLayout('horizontal');
                    $check->setUseButton();
                    $check->style = 'margin:0;';

                    if (!$this->datagrid_form->getField('builder_datagrid_check')) {
                        $this->datagrid_form->addField($check);
                    }

                    $object->builder_datagrid_check = $check;
                    $row = $this->datagrid->addItem($object);

                    if ($object->status_leitura === 'Nao lido') {
                        $row->style = 'font-weight:600; background:#fff7f7;';
                    }
                }
            }

            $countCriteria = $this->buildCriteria([], false);
            $count = $repository->count($countCriteria);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($this->limit);

            TTransaction::close();
            $this->loaded = true;
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
        }
    }

    public function onDelete($param = null)
    {
        if (isset($param['delete']) && $param['delete'] == 1) {
            try {
                TTransaction::open(self::$database);
                ProcessoAdministrativoSchemaHelper::ensureSchema();
                $object = new ProcessoAdministrativo((int) $param['key'], false);
                if (!ProcessoAdministrativoHelper::canAccess($object)) {
                    throw new Exception('Voce nao tem permissao para excluir este processo.');
                }
                $object->delete();
                TTransaction::close();
                $this->onReload($param);
                TToast::show('success', 'Processo excluido.', 'topRight', 'far:check-circle');
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                if (TTransaction::get()) {
                    TTransaction::rollback();
                }
            }
        } else {
            $action = new TAction([$this, 'onDelete']);
            $action->setParameters($param);
            $action->setParameter('delete', 1);
            new TQuestion('Deseja realmente excluir?', $action);
        }
    }

    public function onShow($param = null)
    {
        if (!$this->loaded) {
            $this->onReload(func_get_arg(0));
        }
    }

    private function buildCriteria(array $param = [], bool $withLimit = true): TCriteria
    {
        $criteria = new TCriteria;
        $criteria->setProperties($param);
        $criteria->setProperty('order', $param['order'] ?? 'data_envio');
        $criteria->setProperty('direction', $param['direction'] ?? 'desc');
        if ($withLimit) {
            $criteria->setProperty('limit', $this->limit);
        }

        $accessSql = ProcessoAdministrativoHelper::buildAccessFilterSql();
        if ($accessSql) {
            $criteria->add(new TFilter('id', 'IN', $accessSql));
        }

        $data = TSession::getValue(__CLASS__ . '_filter_data');
        if (!empty($data->numero_processo)) {
            $criteria->add(new TFilter('numero_processo', 'like', "%{$data->numero_processo}%"));
        }
        if (!empty($data->numero_protocolo)) {
            $criteria->add(new TFilter('numero_protocolo', 'like', "%{$data->numero_protocolo}%"));
        }
        if (!empty($data->tipo_processo)) {
            $criteria->add(new TFilter('tipo_processo', '=', $data->tipo_processo));
        }
        if (!empty($data->texto_busca)) {
            $texto = addslashes($data->texto_busca);
            $criteria->add(new TFilter('id', 'IN', "(SELECT id FROM processo_administrativo WHERE assunto LIKE '%{$texto}%' OR ementa LIKE '%{$texto}%' OR descricao LIKE '%{$texto}%')"));
        }
        if (!empty($data->conteudo_arquivo)) {
            $textoArquivo = addslashes($data->conteudo_arquivo);
            $criteria->add(new TFilter('id', 'IN', "(SELECT processo_administrativo_id FROM processo_administrativo_anexo WHERE nome LIKE '%{$textoArquivo}%' OR arquivo LIKE '%{$textoArquivo}%')"));
        }
        if (!empty($data->status)) {
            $criteria->add(new TFilter('status', '=', $data->status));
        }
        if (!empty($data->status_leitura)) {
            $criteria->add(new TFilter('status_leitura', '=', $data->status_leitura));
        }
        if (!empty($data->prazo_status)) {
            $criteria->add(new TFilter('prazo_status', '=', $data->prazo_status));
        }
        if (!empty($data->nivel_sigilo)) {
            $criteria->add(new TFilter('nivel_sigilo', '=', $data->nivel_sigilo));
        }
        if (!empty($data->autor_nome)) {
            $criteria->add(new TFilter('autor_nome', 'like', "%{$data->autor_nome}%"));
        }
        if (!empty($data->departamento)) {
            $criteria->add(new TFilter('departamento_atual', 'like', "%{$data->departamento}%"));
        }
        if (!empty($data->data_inicial)) {
            $criteria->add(new TFilter('DATE(data_envio)', '>=', TDate::date2us($data->data_inicial)));
        }
        if (!empty($data->data_final)) {
            $criteria->add(new TFilter('DATE(data_envio)', '<=', TDate::date2us($data->data_final)));
        }

        return $criteria;
    }
}
