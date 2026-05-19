<?php

class ExtratoDispositivosList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $chaveApi = "$" . "aact_MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OmJkMTY5NjgxLWJjNmEtNGZmNS04ZjE2LWNkMmIwOGRiOTk5OTo6JGFhY2hfMjQ2YjkxNWQtNDU4My00MmU1LTg2NmItM2VlZGE0NjQ3MmEy";
    private $primaryKey = 'id';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        // Coluna de ações (botões)
        $column_actions = new TDataGridColumn('actions', 'Ações', 'left', '20px');
        $column_cpf = new TDataGridColumn('cpfcnpj', "CPF / CNPJ", 'center', '100px');
        $column_name = new TDataGridColumn('name', "Nome", 'left');
        $column_email = new TDataGridColumn('email', "E-mail", 'left'); 
        $column_address = new TDataGridColumn('address', "Endereço", 'left'); 
        $column_addressNumber = new TDataGridColumn('addressNumber', "Número", 'left'); 
        $column_agency = new TDataGridColumn('agency', "Agência", 'left'); 
        $column_account = new TDataGridColumn('account', "Conta", 'left'); 
        $column_accountDigit = new TDataGridColumn('accountDigit', "Dígito", 'left'); 

        // Adicionando colunas à datagrid
        $this->datagrid->addColumn($column_actions); // Ações no início
        $this->datagrid->addColumn($column_cpf);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_address);
        $this->datagrid->addColumn($column_addressNumber);
        $this->datagrid->addColumn($column_agency);
        $this->datagrid->addColumn($column_account);
        $this->datagrid->addColumn($column_accountDigit);

        // Criando botões de edição e exclusão
        $action_edit = new TDataGridAction([$this, 'onEdit']);
        $action_edit->setLabel('Editar');
        $action_edit->setImage('fa:edit blue');
        $action_edit->setField($this->primaryKey); // Definindo o campo da chave primária

        $action_delete = new TDataGridAction([$this, 'onDelete']);
        $action_delete->setLabel('Excluir');
        $action_delete->setImage('fa:trash red');
        $action_delete->setField($this->primaryKey);

        // Criando a ação na coluna
        $action_group = new TDataGridActionGroup('Ações', 'fa:tasks');
        $action_group->addAction($action_edit);
        $action_group->addAction($action_delete);

        $this->datagrid->addActionGroup($action_group);

        $this->datagrid->createModel();

        // Instanciando paginação
        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        // Criando painel e adicionando a grid e a paginação
        $panel = new TPanelGroup('Lista de Contas');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($panel);

        parent::add($container);
        $this->onReload();
    }

    public function onReload($param = null)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api-sandbox.asaas.com/v3/accounts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "access_token: {$this->chaveApi}",
                "User-Agent: MinhaAplicacao/1.0"
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        if ($response) {
            $data = json_decode($response, true);

            if (isset($data['data'])) {
                $this->datagrid->clear();

                foreach ($data['data'] as $item) {
                    $this->datagrid->addItem((object)[
                        'actions' => '', // Placeholder para os botões
                        'id' => $item['id'],
                        'cpfcnpj' => $item['cpfCnpj'],
                        'name' => $item['name'],
                        'email' => $item['email'] ?? 'Não informado',
                        'address' => $item['address'] ?? 'Não informado',
                        'addressNumber' => $item['addressNumber'] ?? 'Não informado',
                        'agency' => $item['accountNumber']['agency'] ?? 'Não informado',
                        'account' => $item['accountNumber']['account'] ?? 'Não informado',
                        'accountDigit' => $item['accountNumber']['accountDigit'] ?? 'Não informado',
                    ]);
                }
            }
        }
    }

    public function onEdit($param)
    {
        new TMessage('info', "Editar ID: {$param['id']}");
    }

    public function onDelete($param)
    {
        $action = new TAction([$this, 'confirmDelete']);
        $action->setParameters($param);
        new TQuestion('Deseja realmente excluir este registro?', $action);
    }

    public function confirmDelete($param)
    {
        new TMessage('info', "Registro ID {$param['id']} excluído.");
    }

    public function show()
    {
        $this->onReload();
        parent::show();
    }
}
