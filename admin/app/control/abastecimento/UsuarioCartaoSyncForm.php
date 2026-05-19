<?php

class UsuarioCartaoSyncForm extends TPage
{
    private $form;
    private $datagrid;

    private static $database = 'minierp';
    private static $formName = 'form_UsuarioCartaoSyncForm';

    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Sincronizacao de usuarios do cartao');

        $cpf = new TEntry('cpf');
        $atualizar_existentes = new TCombo('atualizar_existentes');

        $cpf->setMask('999.999.999-99');
        $cpf->setSize('100%');
        $atualizar_existentes->setSize('100%');
        $atualizar_existentes->addItems(['S' => 'Sim', 'N' => 'Nao']);
        $atualizar_existentes->setValue('S');

        $row1 = $this->form->addFields(
            [new TLabel('CPF', null, '14px', null, '100%'), $cpf],
            [new TLabel('Atualizar existentes', null, '14px', null, '100%'), $atualizar_existentes]
        );
        $row1->layout = ['col-sm-6', 'col-sm-6'];

        $btnConsultar = $this->form->addAction('Consultar cartoes', new TAction([$this, 'onSearch']), 'fas:sync-alt #ffffff');
        $btnConsultar->addStyleClass('btn-primary');

        $btnSync = $this->form->addAction('Sincronizar preview', new TAction([$this, 'onSync']), 'fas:database #ffffff');
        $btnSync->addStyleClass('btn-success');

        $btnClear = $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');

        $observacao = new TElement('div');
        $observacao->style = 'margin:10px 0 0 0;padding:12px;border:1px solid #e5e7eb;background:#f8fafc;color:#334155;border-radius:6px;';
        $observacao->add('Se o CPF ficar em branco, a consulta busca todos os cartoes da unidade atual que ja possuem usuario vinculado em dispositivos_solicitados.');
        $this->form->addContent([$observacao]);

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->style = 'width:100%';
        $this->datagrid->setHeight(420);

        $this->datagrid->addColumn(new TDataGridColumn('status_sync', 'Status', 'center', '14%'));
        $this->datagrid->addColumn(new TDataGridColumn('nome', 'Usuario', 'left', '18%'));
        $this->datagrid->addColumn(new TDataGridColumn('cpf', 'CPF', 'center', '10%'));
        $this->datagrid->addColumn(new TDataGridColumn('cartao', 'Cartao', 'center', '14%'));
        $this->datagrid->addColumn(new TDataGridColumn('saldo_atual_formatado', 'Saldo atual', 'right', '10%'));
        $this->datagrid->addColumn(new TDataGridColumn('saldo_limite_formatado', 'Saldo limite', 'right', '10%'));
        $this->datagrid->addColumn(new TDataGridColumn('pessoa_exibicao', 'Pessoa local', 'left', '18%'));
        $this->datagrid->addColumn(new TDataGridColumn('grupos_exibicao', 'Grupos', 'center', '10%'));
        $this->datagrid->addColumn(new TDataGridColumn('dispositivo_exibicao', 'Cadastro cartao', 'left', '16%'));
        $this->datagrid->addColumn(new TDataGridColumn('veiculo_exibicao', 'Veiculo', 'left', '16%'));
        $this->datagrid->addColumn(new TDataGridColumn('sync_resultado', 'Resultado', 'left', '14%'));

        $actionSyncItem = new TDataGridAction([$this, 'onSyncItem'], ['row_key' => '{row_key}']);
        $actionSyncItem->setLabel('Sincronizar item');
        $actionSyncItem->setImage('fas:database #2E7D32');
        $this->datagrid->addAction($actionSyncItem, 'Sincronizar item', 'fas:database #2E7D32');

        $this->datagrid->createModel();

        $panel = new TPanelGroup('Preview de usuarios/cartoes');
        $panel->add($this->datagrid);
        $panel->getBody()->class .= ' table-responsive';

        $container = new TVBox;
        $container->style = 'width:100%';
        $container->add($this->form);
        $container->add($panel);
        parent::add($container);
    }

    private static function formatarCpf(?string $cpf): string
    {
        $cpf = preg_replace('/\D+/', '', (string) $cpf);
        if (strlen($cpf) !== 11)
        {
            return (string) $cpf;
        }

        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    private static function formatarLinha(array $item): stdClass
    {
        $obj = new stdClass();
        $obj->row_key = $item['row_key'] ?? '';
        $obj->status_sync = $item['status_sync'] ?? '';
        $obj->nome = $item['nome'] ?? '';
        $obj->cpf = self::formatarCpf($item['cpf'] ?? '');
        $obj->cartao = $item['cartao'] ?? '';
        $obj->saldo_atual_formatado = 'R$ ' . number_format((float) ($item['saldo_atual'] ?? 0), 2, ',', '.');
        $obj->saldo_limite_formatado = 'R$ ' . number_format((float) ($item['saldo_limite'] ?? 0), 2, ',', '.');
        $obj->pessoa_exibicao = !empty($item['pessoa_id']) ? ('#' . $item['pessoa_id'] . ' - ' . ($item['pessoa_nome'] ?? '')) : 'Nao localizada';
        $obj->grupos_exibicao = (($item['tem_grupo_condutor'] ?? false) ? '5' : '-') . ' / ' . (($item['tem_grupo_usuario'] ?? false) ? '9' : '-');
        $obj->dispositivo_exibicao = !empty($item['dispositivos_solicitados_id']) ? ('#' . $item['dispositivos_solicitados_id']) : 'Novo';
        $obj->veiculo_exibicao = !empty($item['veiculo_descricao']) ? $item['veiculo_descricao'] : 'Sem vinculo automatico';
        $obj->sync_resultado = $item['sync_resultado'] ?? '';
        return $obj;
    }

    private function carregarGrid(): void
    {
        $this->datagrid->clear();
        foreach ((TSession::getValue(UsuarioCartaoSyncService::SESSION_KEY) ?? []) as $item)
        {
            $this->datagrid->addItem(self::formatarLinha($item));
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
            $preview = UsuarioCartaoSyncService::consultarPorCpf((array) $data);
            TTransaction::close();

            $this->carregarGrid();
            new TMessage('info', 'Preview carregado com ' . count($preview) . ' cartao(oes).');
        }
        catch (Exception $e)
        {
            if (TTransaction::get()) TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onSync($param = null)
    {
        if (!isset($param['confirm']) || $param['confirm'] != 1)
        {
            $action = new TAction([$this, 'onSync']);
            $action->setParameters($param);
            $action->setParameter('confirm', 1);
            new TQuestion('Tem certeza que deseja sincronizar todos os itens do preview?', $action);
            return;
        }

        try
        {
            $data = $this->form->getData();
            $this->form->setData($data);
            TTransaction::open(self::$database);
            $resumo = UsuarioCartaoSyncService::sincronizarPreview((array) $data);
            TTransaction::close();
            $this->carregarGrid();

            $msg = 'Sincronizacao concluida.';
            foreach ($resumo as $chave => $valor)
            {
                $msg .= '<br>' . ucfirst(str_replace('_', ' ', $chave)) . ': ' . (int) $valor . '.';
            }
            new TMessage('info', $msg);
        }
        catch (Exception $e)
        {
            if (TTransaction::get()) TTransaction::rollback();
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
            TTransaction::open(self::$database);
            $resultado = UsuarioCartaoSyncService::sincronizarItemPreview((string) ($param['row_key'] ?? ''), (array) $this->form->getData());
            TTransaction::close();
            $this->carregarGrid();
            new TMessage('info', 'Item sincronizado. Pessoa: ' . ($resultado['pessoa_acao'] ?? '') . ' | Cartao: ' . ($resultado['dispositivo_acao'] ?? ''));
        }
        catch (Exception $e)
        {
            if (TTransaction::get()) TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onClear($param = null)
    {
        TSession::setValue(__CLASS__ . '_filter_data', null);
        TSession::setValue(UsuarioCartaoSyncService::SESSION_KEY, null);
        TSession::setValue(UsuarioCartaoSyncService::SESSION_META_KEY, null);
        $this->form->clear(true);
        $this->form->setData((object) ['atualizar_existentes' => 'S']);
        $this->datagrid->clear();
    }

    public function onShow($param = null)
    {
        $data = TSession::getValue(__CLASS__ . '_filter_data') ?: (object) ['atualizar_existentes' => 'S'];
        $this->form->setData($data);
        $this->carregarGrid();
    }
}
