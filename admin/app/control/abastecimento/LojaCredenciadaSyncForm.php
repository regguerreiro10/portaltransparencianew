<?php

class LojaCredenciadaSyncForm extends TPage
{
    private $form;
    private $datagrid;

    private static $database = 'minierp';
    private static $formName = 'form_LojaCredenciadaSyncForm';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Sincronizacao de lojas credenciadas');

        $cpf = new TEntry('cpf');
        $somente_novas = new TCombo('somente_novas');
        $atualizar_existentes = new TCombo('atualizar_existentes');

        $cpf->setMask('999.999.999-99');
        $cpf->setSize('100%');
        $somente_novas->setSize('100%');
        $atualizar_existentes->setSize('100%');

        $somente_novas->addItems(['N' => 'Todas as lojas', 'S' => 'Somente novas']);
        $atualizar_existentes->addItems(['S' => 'Sim', 'N' => 'Nao']);
        $somente_novas->setValue('N');
        $atualizar_existentes->setValue('S');

        $row1 = $this->form->addFields(
            [new TLabel('CPF do usuario', null, '14px', null, '100%'), $cpf],
            [new TLabel('Filtro do preview', null, '14px', null, '100%'), $somente_novas],
            [new TLabel('Atualizar dados existentes', null, '14px', null, '100%'), $atualizar_existentes]
        );
        $row1->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $btnConsultar = $this->form->addAction('Consultar lojas', new TAction([$this, 'onSearch']), 'fas:sync-alt #ffffff');
        $btnConsultar->addStyleClass('btn-primary');

        $btnSincronizar = $this->form->addAction('Sincronizar preview', new TAction([$this, 'onSync']), 'fas:database #ffffff');
        $btnSincronizar->addStyleClass('btn-success');

        $btnLimpar = $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');

        $observacao = new TElement('div');
        $observacao->style = 'margin:10px 0 0 0;padding:12px;border:1px solid #e5e7eb;background:#f8fafc;color:#334155;border-radius:6px;';
        $observacao->add('A rotina consulta as lojas credenciadas da NP3 a partir dos cartoes vinculados em dispositivos_solicitados da unidade atual. No modo de sincronizacao, os dados sao gravados em pessoa, pessoa_grupo e pessoa_endereco, incluindo latitude e longitude quando a API informar.');
        $this->form->addContent([$observacao]);

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->style = 'width:100%';
        $this->datagrid->setHeight(420);

        $this->datagrid->addColumn(new TDataGridColumn('status_sync', 'Status', 'center', '16%'));
        $this->datagrid->addColumn(new TDataGridColumn('codigo_loja', 'Codigo loja', 'center', '10%'));
        $this->datagrid->addColumn(new TDataGridColumn('nome_exibicao', 'Loja / Razao social', 'left', '24%'));
        $this->datagrid->addColumn(new TDataGridColumn('cnpj', 'CNPJ', 'center', '12%'));
        $this->datagrid->addColumn(new TDataGridColumn('cidade_uf', 'Cidade/UF', 'left', '14%'));
        $this->datagrid->addColumn(new TDataGridColumn('ramo_atividade', 'Ramo atividade', 'left', '18%'));
        $this->datagrid->addColumn(new TDataGridColumn('telefone', 'Telefone', 'left', '12%'));
        $this->datagrid->addColumn(new TDataGridColumn('email', 'Email', 'left', '18%'));
        $this->datagrid->addColumn(new TDataGridColumn('pessoa_exibicao', 'Pessoa interna', 'left', '18%'));
        $this->datagrid->addColumn(new TDataGridColumn('fornecedor_vinculado', 'Grupo fornecedor', 'center', '10%'));
        $this->datagrid->addColumn(new TDataGridColumn('origem_exibicao', 'Origem API', 'left', '22%'));
        $this->datagrid->addColumn(new TDataGridColumn('sync_resultado', 'Resultado sync', 'left', '18%'));

        $actionSyncItem = new TDataGridAction([$this, 'onSyncItem'], ['row_key' => '{row_key}']);
        $actionSyncItem->setLabel('Sincronizar item');
        $actionSyncItem->setImage('fas:database #2E7D32');
        $this->datagrid->addAction($actionSyncItem, 'Sincronizar item', 'fas:database #2E7D32');

        $this->datagrid->createModel();

        $panel = new TPanelGroup('Preview das lojas credenciadas');
        $panel->add($this->datagrid);
        $panel->getBody()->class .= ' table-responsive';

        $container = new TVBox;
        $container->style = 'width:100%';
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);
    }

    private static function formatarDocumento(?string $valor, int $tamanho): string
    {
        $valor = preg_replace('/\D+/', '', (string) $valor);

        if ($tamanho === 14 && strlen($valor) === 14)
        {
            return substr($valor, 0, 2) . '.' . substr($valor, 2, 3) . '.' . substr($valor, 5, 3) . '/' . substr($valor, 8, 4) . '-' . substr($valor, 12, 2);
        }

        if ($tamanho === 11 && strlen($valor) === 11)
        {
            return substr($valor, 0, 3) . '.' . substr($valor, 3, 3) . '.' . substr($valor, 6, 3) . '-' . substr($valor, 9, 2);
        }

        return (string) $valor;
    }

    private static function formatarLinhaLoja(array $loja): stdClass
    {
        $item = new stdClass();
        $item->row_key = $loja['row_key'] ?? '';
        $item->status_sync = $loja['status_sync'] ?? '';
        $item->codigo_loja = $loja['codigo_loja'] ?? '';
        $item->nome_exibicao = trim((string) (($loja['razao_social'] ?? '') ?: ($loja['nome'] ?? '')));
        $item->cnpj = self::formatarDocumento($loja['cnpj'] ?? '', 14);
        $item->cidade_uf = trim((string) (($loja['cidade'] ?? '') . ((empty($loja['cidade']) || empty($loja['uf'])) ? '' : '/') . ($loja['uf'] ?? '')));
        $item->ramo_atividade = trim((string) ($loja['ramo_atividade'] ?? ''));
        $item->telefone = $loja['telefone'] ?? '';
        $item->email = $loja['email'] ?? '';
        $item->pessoa_exibicao = !empty($loja['pessoa_id']) ? ('#' . $loja['pessoa_id'] . ' - ' . ($loja['pessoa_nome'] ?? '')) : 'Nao localizada';
        $item->fornecedor_vinculado = $loja['fornecedor_vinculado'] ?? 'Nao';
        $item->origem_exibicao = 'CPF(s): ' . implode(', ', array_map(function ($cpf) {
            return self::formatarDocumento($cpf, 11);
        }, $loja['cpfs_origem'] ?? []));

        if (!empty($loja['cartoes_origem']))
        {
            $item->origem_exibicao .= '<br>Cartao(s): ' . implode(', ', $loja['cartoes_origem']);
        }

        if (!empty($loja['redes_origem']))
        {
            $item->origem_exibicao .= '<br>Rede(s): ' . implode(', ', $loja['redes_origem']);
        }

        $item->sync_resultado = $loja['sync_resultado'] ?? '';

        return $item;
    }

    private function carregarGrid(): void
    {
        $this->datagrid->clear();

        $preview = TSession::getValue(LojaCredenciadaSyncService::SESSION_KEY) ?? [];
        foreach ($preview as $loja)
        {
            $this->datagrid->addItem(self::formatarLinhaLoja($loja));
        }
    }

    public function onSearch($param = null)
    {
        try
        {
            $data = $this->form->getData();
            $this->form->setData($data);
            TSession::setValue(__CLASS__ . '_filter_data', $data);

            TTransaction::open(self::$database);
            $lojas = LojaCredenciadaSyncService::consultarLojas((array) $data);
            $this->carregarGrid();
            TTransaction::close();

            if (!$lojas)
            {
                $meta = TSession::getValue(LojaCredenciadaSyncService::SESSION_META_KEY) ?? [];
                $mensagem = 'Nenhuma loja credenciada foi encontrada para os filtros informados.';

                if (!empty($meta))
                {
                    $mensagem .= '<br><br>Usuarios consultados: ' . (int) ($meta['qtd_usuarios_consultados'] ?? 0) . '.';
                    $mensagem .= '<br>Cartoes cadastrados: ' . (int) ($meta['qtd_cartoes_cadastrados'] ?? 0) . '.';
                    $mensagem .= '<br>Contas retornadas pela API: ' . (int) ($meta['qtd_contas'] ?? 0) . '.';
                    $mensagem .= '<br>Lojas retornadas pela API: ' . (int) ($meta['qtd_lojas_brutas'] ?? 0) . '.';
                    if (!empty($meta['erros_api']))
                    {
                        $erro = $meta['erros_api'][0];
                        $mensagem .= '<br>Primeiro erro da API: ' . trim(($erro['cod'] ?? '') . ' ' . ($erro['msg'] ?? ''));
                    }
                }

                new TMessage('info', $mensagem);
                return;
            }

            $meta = TSession::getValue(LojaCredenciadaSyncService::SESSION_META_KEY) ?? [];
            $mensagem = 'Preview carregado com sucesso.';
            $mensagem .= '<br><br>Lojas unicas no preview: ' . count($lojas) . '.';
            $mensagem .= '<br>Lojas brutas retornadas pela API: ' . (int) ($meta['qtd_lojas_brutas'] ?? 0) . '.';
            $mensagem .= '<br>Usuarios consultados: ' . (int) ($meta['qtd_usuarios_consultados'] ?? 0) . '.';
            $mensagem .= '<br>Cartoes base da consulta: ' . (int) ($meta['qtd_cartoes_cadastrados'] ?? 0) . '.';
            new TMessage('info', $mensagem);
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

    public function onSync($param = null)
    {
        try
        {
            $data = $this->form->getData();
            $this->form->setData($data);
            TSession::setValue(__CLASS__ . '_filter_data', $data);

            TTransaction::open(self::$database);
            $resumo = LojaCredenciadaSyncService::sincronizarPreview((array) $data);
            TTransaction::close();

            $this->carregarGrid();

            $mensagem = 'Sincronizacao concluida com sucesso.';
            $mensagem .= '<br><br>Processadas: ' . (int) ($resumo['processadas'] ?? 0) . '.';
            $mensagem .= '<br>Pessoas criadas: ' . (int) ($resumo['criadas'] ?? 0) . '.';
            $mensagem .= '<br>Pessoas atualizadas: ' . (int) ($resumo['atualizadas'] ?? 0) . '.';
            $mensagem .= '<br>Grupos fornecedor criados: ' . (int) ($resumo['grupos_criados'] ?? 0) . '.';
            $mensagem .= '<br>Enderecos criados: ' . (int) ($resumo['enderecos_criados'] ?? 0) . '.';
            $mensagem .= '<br>Enderecos atualizados: ' . (int) ($resumo['enderecos_atualizados'] ?? 0) . '.';
            $mensagem .= '<br>Ignoradas: ' . (int) ($resumo['ignoradas'] ?? 0) . '.';
            new TMessage('info', $mensagem);
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

    public function onSyncItem($param = null)
    {
        if (!isset($param['confirm']) || $param['confirm'] != 1)
        {
            $action = new TAction([$this, 'onSyncItem']);
            $action->setParameters($param);
            $action->setParameter('confirm', 1);

            new TQuestion('Tem certeza que deseja sincronizar este item?', $action);
            return;
        }

        try
        {
            $rowKey = $param['row_key'] ?? null;
            if (empty($rowKey))
            {
                throw new Exception('Item do preview nao informado para sincronizacao.');
            }

            $data = $this->form->getData();
            $this->form->setData($data);
            TSession::setValue(__CLASS__ . '_filter_data', $data);

            TTransaction::open(self::$database);
            $resultado = LojaCredenciadaSyncService::sincronizarItemPreview($rowKey, (array) $data);
            TTransaction::close();

            $this->carregarGrid();

            if (($resultado['status'] ?? '') === 'ignored')
            {
                new TMessage('info', $resultado['motivo'] ?? 'Item ignorado na sincronizacao.');
                return;
            }

            $mensagem = 'Item sincronizado com sucesso.';
            $mensagem .= '<br><br>Pessoa: ' . ($resultado['pessoa_acao'] ?? 'unchanged') . '.';
            $mensagem .= '<br>Grupo fornecedor: ' . ($resultado['grupo_acao'] ?? 'unchanged') . '.';
            $mensagem .= '<br>Endereco principal: ' . ($resultado['endereco_acao'] ?? 'unchanged') . '.';
            new TMessage('info', $mensagem);
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
        TSession::setValue(LojaCredenciadaSyncService::SESSION_KEY, null);
        TSession::setValue(LojaCredenciadaSyncService::SESSION_META_KEY, null);

        $this->form->clear(true);
        $data = new stdClass();
        $data->somente_novas = 'N';
        $data->atualizar_existentes = 'S';
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
            $data->somente_novas = 'N';
            $data->atualizar_existentes = 'S';
            $this->form->setData($data);
        }

        $this->carregarGrid();
    }
}
