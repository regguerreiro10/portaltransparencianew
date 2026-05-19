<?php

class FrenteCaixaCartaoLancamentoList extends TPage
{
    private $form;
    private $datagrid;

    private static $formName = 'form_FrenteCaixaCartaoLancamentoList';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Lancamentos de Cartao');

        $cpf = new TEntry('cpf');
        $numero_cartao = new TEntry('numero_cartao');
        $periodo = new BDateRange('dt_inicial', 'dt_final');

        $cpf->setMask('999.999.999-99');
        $periodo->setMask('dd/mm/yyyy');
        $periodo->setDatabaseMask('yyyy-mm-dd');

        $cpf->setSize('100%');
        $numero_cartao->setSize('100%');
        $periodo->setSize(220);

        $row1 = $this->form->addFields(
            [new TLabel('CPF', null, '14px', null, '100%'), $cpf],
            [new TLabel('Numero do cartao', null, '14px', null, '100%'), $numero_cartao],
            [new TLabel('Periodo', null, '14px', null, '100%'), $periodo]
        );
        $row1->layout = ['col-sm-3', 'col-sm-4', 'col-sm-5'];

        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $btnBuscar = $this->form->addAction('Consultar cartao', new TAction([$this, 'onSearch']), 'fas:sync-alt #ffffff');
        $btnBuscar->addStyleClass('btn-primary');

        $btnLimpar = $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');

        $observacao = new TElement('div');
        $observacao->style = 'margin:10px 0 0 0;padding:12px;border:1px solid #e5e7eb;background:#f8fafc;color:#334155;border-radius:6px;';
        $observacao->add('Se o CPF ficar em branco, a consulta busca os lancamentos de todos os cartoes cadastrados em dispositivos_solicitados na unidade. O cartao precisa estar vinculado ao usuario, ao veiculo e com limite informado para seguir o fluxo operacional.');
        $this->form->addContent([$observacao]);

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->style = 'width:100%';
        $this->datagrid->setHeight(420);

        $colData = new TDataGridColumn('dt_hora_autoriz_formatada', 'Data/Hora', 'center', '14%');
        $colCartao = new TDataGridColumn('numero_cartao', 'Cartao', 'center', '14%');
        $colUsuario = new TDataGridColumn('usuario_cartao_nome_exibicao', 'Usuario cartao', 'left', '18%');
        $colVeiculo = new TDataGridColumn('veiculo_exibicao', 'Veiculo', 'left', '18%');
        $colLimite = new TDataGridColumn('saldo_limite_formatado', 'Limite', 'right', '10%');
        $colLoja = new TDataGridColumn('nm_loja', 'Loja', 'left', '24%');
        $colEstabelecimento = new TDataGridColumn('estabelecimento_nome_exibicao', 'Estabelecimento interno', 'left', '22%');
        $colValor = new TDataGridColumn('valor_total_formatado', 'Valor', 'right', '10%');
        $colStatus = new TDataGridColumn('tp_status', 'Status API', 'center', '8%');
        $colAutoriz = new TDataGridColumn('cd_autoriz', 'Autorizacao', 'center', '8%');

        $this->datagrid->addColumn($colData);
        $this->datagrid->addColumn($colCartao);
        $this->datagrid->addColumn($colUsuario);
        $this->datagrid->addColumn($colVeiculo);
        $this->datagrid->addColumn($colLimite);
        $this->datagrid->addColumn($colLoja);
        $this->datagrid->addColumn($colEstabelecimento);
        $this->datagrid->addColumn($colValor);
        $this->datagrid->addColumn($colStatus);
        $this->datagrid->addColumn($colAutoriz);

        $actionView = new TDataGridAction(['FrenteCaixaCartaoLancamentoFormView', 'onShow'], ['launch_key' => '{launch_key}']);
        $actionView->setLabel('Visualizar lancamento');
        $actionView->setImage('fas:search-plus #673AB7');
        $this->datagrid->addAction($actionView, 'Visualizar lancamento', 'fas:search-plus #673AB7');

        $this->datagrid->createModel();

        $panel = new TPanelGroup('Retorno da API');
        $panel->add($this->datagrid);
        $panel->getBody()->class .= ' table-responsive';

        $container = new TVBox;
        $container->style = 'width:100%';
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);
    }

    private static function formatarLinhaLancamento(array $lancamento): stdClass
    {
        $item = new stdClass();
        $item->launch_key = $lancamento['launch_key'];
        $item->numero_cartao = $lancamento['numero_cartao'] ?? '';
        $item->nm_loja = $lancamento['nm_loja'] ?? '';
        $item->tp_status = $lancamento['tp_status'] ?? '';
        $item->cd_autoriz = $lancamento['cd_autoriz'] ?? '';
        $item->usuario_cartao_nome_exibicao = !empty($lancamento['usuario_cartao_nome']) ? $lancamento['usuario_cartao_nome'] : 'Nao localizado';
        $item->veiculo_exibicao = !empty($lancamento['veiculo_descricao']) ? $lancamento['veiculo_descricao'] : 'Nao localizado';
        $item->dt_hora_autoriz_formatada = '';
        $item->saldo_limite_formatado = 'R$ ' . number_format((float) ($lancamento['saldo_limite'] ?? 0), 2, ',', '.');
        $item->valor_total_formatado = 'R$ ' . number_format((float) ($lancamento['valor_total'] ?? 0), 2, ',', '.');
        $item->estabelecimento_nome_exibicao = !empty($lancamento['estabelecimento_nome']) ? $lancamento['estabelecimento_nome'] : 'Nao localizado automaticamente';

        if (!empty($lancamento['dt_hora_autoriz']))
        {
            $formatos = ['d/m/Y H:i:s', 'd/m/Y H:i', 'Y-m-d H:i:s', 'Y-m-d H:i'];
            foreach ($formatos as $formato)
            {
                $data = DateTime::createFromFormat($formato, $lancamento['dt_hora_autoriz']);
                if ($data instanceof DateTime)
                {
                    $item->dt_hora_autoriz_formatada = $data->format('d/m/Y H:i');
                    break;
                }
            }

            if ($item->dt_hora_autoriz_formatada === '')
            {
                try
                {
                    $item->dt_hora_autoriz_formatada = (new DateTime($lancamento['dt_hora_autoriz']))->format('d/m/Y H:i');
                }
                catch (Exception $e)
                {
                    $item->dt_hora_autoriz_formatada = $lancamento['dt_hora_autoriz'];
                }
            }
        }

        return $item;
    }

    public function onSearch($param = null)
    {
        try
        {
            $data = $this->form->getData();
            $this->form->validate();
            $this->form->setData($data);
            TSession::setValue(__CLASS__ . '_filter_data', $data);

            TTransaction::open('minierp');
            $lancamentos = CartaoLancamentoService::consultarLancamentos((array) $data);
            $this->datagrid->clear();

            foreach ($lancamentos as $lancamento)
            {
                $this->datagrid->addItem(self::formatarLinhaLancamento($lancamento));
            }

            TTransaction::close();

            $resumoImportacao = null;
            if ($lancamentos)
            {
                $resumoImportacao = CartaoLancamentoService::registrarLancamentosAutomaticamente($lancamentos);
            }

            if (!$lancamentos)
            {
                $meta = TSession::getValue(CartaoLancamentoService::SESSION_META_KEY) ?? [];
                $mensagem = 'Nenhum lancamento foi encontrado para os filtros informados.';

                if (!empty($meta['qtd_contas']))
                {
                    $mensagem .= '<br><br>Usuarios consultados: ' . (int) ($meta['qtd_usuarios_consultados'] ?? 0) . '.';
                    $mensagem .= '<br>Cartoes cadastrados em dispositivos_solicitados: ' . (int) ($meta['qtd_cartoes_cadastrados'] ?? 0) . '.';
                    $mensagem .= '<br>Cartoes encontrados: ' . (int) $meta['qtd_contas'] . '.';
                    $mensagem .= '<br>Status consultados na API: ' . implode(', ', $meta['status_consultados'] ?? []);
                    $mensagem .= '<br>Lancamentos retornados pela API no total: ' . (int) ($meta['qtd_autorizacoes_brutas'] ?? 0) . '.';
                    $mensagem .= '<br>Lancamentos vinculados ao cadastro local: ' . (int) ($meta['qtd_autorizacoes_vinculadas'] ?? 0) . '.';
                    if (!empty($meta['erros_api']))
                    {
                        $primeiroErro = $meta['erros_api'][0];
                        $mensagem .= '<br>Retorno da API para o cartao ' . ($primeiroErro['cartao'] ?: 'informado') . ': ';
                        $mensagem .= trim(($primeiroErro['cod'] ?? '') . ' ' . ($primeiroErro['msg'] ?? ''));
                    }
                    elseif (($meta['qtd_autorizacoes_brutas'] ?? 0) > 0 && ($meta['qtd_autorizacoes_vinculadas'] ?? 0) === 0)
                    {
                        $mensagem .= '<br>Os lancamentos vieram da API, mas nenhum numero de cartao conseguiu casar com o numerocartao salvo em dispositivos_solicitados.';
                        if (!empty($meta['cartoes_nao_vinculados']))
                        {
                            foreach ($meta['cartoes_nao_vinculados'] as $diagnostico)
                            {
                                $mensagem .= '<br><br>CPF consultado: ' . ($diagnostico['cpf'] ?? '');
                                $mensagem .= '<br>API tentou: ' . implode(' | ', $diagnostico['api'] ?? []);
                                $mensagem .= '<br>Cadastro local tem: ' . implode(' | ', $diagnostico['cadastro'] ?? []);
                            }
                        }
                    }
                }
                else
                {
                    $mensagem .= '<br><br>Nenhum cartao foi retornado pela API para os cadastros localizados em dispositivos_solicitados.';
                }

                new TMessage('info', $mensagem);
            }
            elseif ($resumoImportacao)
            {
                $mensagem = 'Consulta concluida.';
                $mensagem .= '<br><br>Pedidos gerados automaticamente: ' . (int) ($resumoImportacao['importados'] ?? 0) . '.';
                $mensagem .= '<br>Lancamentos ja importados: ' . (int) ($resumoImportacao['ja_importados'] ?? 0) . '.';
                $mensagem .= '<br>Lancamentos ignorados por falta de veiculo/estabelecimento: ' . (int) ($resumoImportacao['ignorados_sem_cadastro'] ?? 0) . '.';

                if (!empty($resumoImportacao['pedidos_ids']))
                {
                    $mensagem .= '<br>Pedidos gerados: ' . implode(', ', $resumoImportacao['pedidos_ids']) . '.';
                }

                if (!empty($resumoImportacao['erros']))
                {
                    foreach ($resumoImportacao['erros'] as $erro)
                    {
                        $mensagem .= '<br>Erro autorizacao ' . ($erro['autorizacao'] ?: 'sem codigo') . ': ' . $erro['mensagem'];
                    }
                }

                new TMessage('info', $mensagem);
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
        TSession::setValue(__CLASS__ . '_filter_data', null);
        TSession::setValue(CartaoLancamentoService::SESSION_KEY, null);

        $this->form->clear(true);
        $data = new stdClass();
        $data->dt_inicial = date('Y-m-01');
        $data->dt_final = date('Y-m-d');
        $this->form->setData($data);
        $this->datagrid->clear();
    }

    public function onShow($param = null)
    {
        if ($data = TSession::getValue(__CLASS__ . '_filter_data'))
        {
            $this->form->setData($data);
        }
        else
        {
            $data = new stdClass();
            $data->dt_inicial = date('Y-m-01');
            $data->dt_final = date('Y-m-d');
            $this->form->setData($data);
        }

        $this->datagrid->clear();
        $launches = TSession::getValue(CartaoLancamentoService::SESSION_KEY) ?? [];
        foreach ($launches as $launch)
        {
            $this->datagrid->addItem(self::formatarLinhaLancamento($launch));
        }
    }
}
