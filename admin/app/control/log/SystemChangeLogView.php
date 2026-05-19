<?php
/**
 * SystemChangeLogView
 *
 * @version    1.0
 * @package    control
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemChangeLogView extends TStandardList
{
    protected $form;      // formulário de cadastro
    protected $datagrid;  // listagem
    protected $loaded;
    protected $pageNavigation;  // pagination component
    protected $activeRecord;
    protected $formgrid;
    protected $formfields;
    protected $delAction;

    private function getAllowedLoginsByUnit($unitId)
    {
        $unitId = (int) $unitId;
        if ($unitId <= 0)
        {
            return [];
        }

        $cacheKey = 'SystemChangeLogView_allowed_logins_' . $unitId;
        $cached = TSession::getValue($cacheKey);
        if (is_array($cached))
        {
            return $cached;
        }

        TTransaction::open('minierp');
        $conn = TTransaction::get();
        $stmt = $conn->prepare(
            'SELECT DISTINCT su.login
               FROM system_user_unit suu
               INNER JOIN system_users su ON su.id = suu.system_user_id
              WHERE suu.system_unit_id = ?
                AND su.login IS NOT NULL
                AND su.login <> \'\''
        );
        $stmt->execute([$unitId]);
        $logins = $stmt->fetchAll(PDO::FETCH_COLUMN);
        TTransaction::close();

        TSession::setValue($cacheKey, $logins);
        return $logins;
    }
    
    /*
     * método construtor
     * Cria a página, o formulário e a listagem
     */
    public function __construct()
    {
        parent::__construct();
        
        parent::setDatabase('log');
        parent::setActiveRecord('SystemChangeLog');
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        parent::addFilterField('tablename');
        parent::addFilterField('login','like');
        parent::addFilterField('class_name', 'like'); // add a filter field
        parent::addFilterField('session_id', 'like'); // add a filter field
        parent::addFilterField('oldvalue', 'like', 'oldvalue');
         
        // Período (BDateRange): campo da tabela 'logdate' filtrando entre os dois inputs
        // parent::addFilterField('logdate', '>=', 'logdate'.' 00:00:00');        // data inicial
        // parent::addFilterField('logdate', '<=', 'logdate_final'. ' 23:59:59');  // data final
        // e agora o range com hora funciona direto:
        parent::addFilterField('logdate', '>=', 'logdate_ini');        // início (datetime)
        parent::addFilterField('logdate', '<=', 'logdate_final');  // fim (datetime)
        parent::setLimit(20);
   
        $basename   = urlencode('log-alteracoes-list.pdf');
        $download   = "download.php?file=app/manual/log-alteracoes-list.pdf&basename={$basename}";

        $manual = "
            <span style='float:right;'>
                <a href='{$download}'
                target='_blank'
                style='text-decoration:none;margin-left:10px;'>
                    <i class='fa fa-question-circle'> </i>
                </a>
            </span>
        "; 

        $this->form = new BootstrapFormBuilder('form_table_logger');
        $this->form->setFormTitle("Log de Alterações {$manual}");
        
        // cria os campos do formulário
        $tablename   = new TEntry('tablename');
        $login       = new TEntry('login');
        $class_name  = new TEntry('class_name');
        $session_id  = new TEntry('session_id');
        $oldvalue    = new TEntry('oldvalue');
        $logdate_ini  = new TDateTime('logdate_ini');        // início
        $logdate_fim  = new TDateTime('logdate_final');  // fim

        $logdate_ini->setMask('dd/mm/yyyy hh:ii');
        $logdate_ini->setDatabaseMask('yyyy-mm-dd hh:ii:ss');

        $logdate_fim->setMask('dd/mm/yyyy hh:ii');
        $logdate_fim->setDatabaseMask('yyyy-mm-dd hh:ii:ss');


        
        $this->form->addFields( [new TLabel(_t('Table'))], [$tablename], [new TLabel(_t('Program'))], [$class_name] );
        $this->form->addFields( [new TLabel('Login')], [$login], [new TLabel(_t('Session'))], [$session_id]);
        // no addFields:
        $this->form->addFields(
            [new TLabel('Período (início)')], [$logdate_ini],
            [new TLabel('Período (fim)')],    [$logdate_fim]
        );
        $this->form->setData( TSession::getValue('SystemChangeLogView_filter_data') );
        
        $btn = $this->form->addAction(_t('Search'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        $this->formgrid = new TForm;
        
        // instancia objeto DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->setGroupColumn('transaction_id', 'Transaction: <b>{transaction_id}</b>');
        $this->datagrid->enablePopover(_t('Execution trace'), '{log_trace_formatted}');
        
        parent::setTransformer(array($this, 'onBeforeLoad'));
        
        // datagrid inside form
        $this->formgrid->add($this->datagrid);
        
        // instancia as colunas da DataGrid
        $id         = new TDataGridColumn('pkvalue',    'PK',      'center');
        $date       = new TDataGridColumn('logdate',    _t('Date'),   'center');
        $login      = new TDataGridColumn('login',      'Login',   'center');
        $name       = new TDataGridColumn('tablename',  _t('Table'),  'center');
        $column     = new TDataGridColumn('columnname', _t('Column'), 'center');
        $operation  = new TDataGridColumn('operation',  _t('Operation'), 'center');
        $oldvalue   = new TDataGridColumn('oldvalue',   _t('Old value'), 'left');
        $newvalue   = new TDataGridColumn('newvalue',   _t('New value'), 'left');
        $class_name = new TDataGridColumn('class_name',  _t('Program'), 'center');
        $php_sapi   = new TDataGridColumn('php_sapi',   'SAPI', 'center');
        $access_ip  = new TDataGridColumn('access_ip',  'IP', 'center');
        
        $operation->setTransformer( function($value, $object, $row) {
            $div = new TElement('span');
            $div->style="text-shadow:none; font-size:12px";
            if ($value == 'created')
            {
                $div->class="label label-success";
            }
            else if ($value == 'deleted')
            {
                $div->class="label label-danger";
            }
            else if ($value == 'changed')
            {
                $div->class="label label-info";
            }
            $div->add($value);
            return $div;
        });
        
        $order1= new TAction(array($this, 'onReload'));
        $order2= new TAction(array($this, 'onReload'));
        $order3= new TAction(array($this, 'onReload'));
        $order4= new TAction(array($this, 'onReload'));
        $order5= new TAction(array($this, 'onReload'));
        
        $order1->setParameter('order', 'pkvalue');
        $order2->setParameter('order', 'logdate');
        $order3->setParameter('order', 'login');
        $order4->setParameter('order', 'tablename');
        $order5->setParameter('order', 'columnname');
        
        $id->setAction($order1);
        $date->setAction($order2);
        $login->setAction($order3);
        $name->setAction($order4);
        $column->setAction($order5);
        
        // adiciona as colunas à DataGrid
        $this->datagrid->addColumn($date);
        $this->datagrid->addColumn($login);
        $this->datagrid->addColumn($name);
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($column);
        $this->datagrid->addColumn($operation);
        $this->datagrid->addColumn($oldvalue);
        $this->datagrid->addColumn($newvalue);
        $this->datagrid->addColumn($class_name);
        $this->datagrid->addColumn($php_sapi);
        $this->datagrid->addColumn($access_ip);
        
        // cria o modelo da DataGrid, montando sua estrutura
        $this->datagrid->createModel();
        
        // cria o paginador
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid)->style='overflow-x:auto';
        $panel->addFooter($this->pageNavigation);
        
        $container = new TVBox;
        $container->style = 'width: 100%';
       // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
    
    /**
    *
    */
    public function filterSession($param)
    {
        parent::clearFilters();

        $data = new stdClass;
        $data->session_id = $param['session_id'] ?? null;
        $this->form->setData($data);

        // só passa session_id no param e deixa o onReload cuidar do resto
        $this->onReload(['session_id' => $param['session_id']]);
    }

    public function onReload($param = NULL)
    {
        try
        {
            $userunitid = (int) TSession::getValue('userunitid');
            $logins = $this->getAllowedLoginsByUnit($userunitid);
            TTransaction::open('log');

            // cria criteria
            $criteria = new TCriteria;

            // paginação
            $param = (array) $param;

            $limit  = $this->limit ?? 20;
            $offset = isset($param['offset']) ? (int) $param['offset'] : 0;

            $criteria->setProperty('limit',  $limit);
            $criteria->setProperty('offset', $offset);

            // ordenação
            if (isset($param['order']) && $param['order']) {
                $criteria->setProperty('order', $param['order']);
                $criteria->setProperty('direction', $param['direction'] ?? 'asc');
            } else {
                // default order
                $criteria->setProperty('order', 'id');
                $criteria->setProperty('direction', 'asc');
            }

            // aplica os filtros do form (salvos em sessão)
            $data = TSession::getValue('SystemChangeLogView_filter_data');
            if ($data)
            {
                // mantém o form preenchido
                $this->form->setData($data);

                if (!empty($data->tablename)) {
                    $criteria->add(new TFilter('tablename', 'like', "%{$data->tablename}%"));
                }

                if (!empty($data->login)) {
                    $criteria->add(new TFilter('login', 'like', "%{$data->login}%"));
                }

                if (!empty($data->class_name)) {
                    $criteria->add(new TFilter('class_name', 'like', "%{$data->class_name}%"));
                }

                if (!empty($data->session_id)) {
                    $criteria->add(new TFilter('session_id', 'like', "%{$data->session_id}%"));
                }

                if (!empty($data->oldvalue)) {
                    $criteria->add(new TFilter('oldvalue', 'like', "%{$data->oldvalue}%"));
                }

                // período (datetime)
                if (!empty($data->logdate_ini)) {
                    $criteria->add(new TFilter('logdate', '>=', $data->logdate_ini));
                }
                if (!empty($data->logdate_final)) {
                    $criteria->add(new TFilter('logdate', '<=', $data->logdate_final));
                }
            }
                if (!empty($logins)) {
                    $criteria->add(new TFilter('login', 'IN', $logins));
                } else {
                    $criteria->add(new TFilter('id', '=', -1));
                }

            // limpa datagrid
            $this->datagrid->clear();

            // carrega registros
            $repository = new TRepository('SystemChangeLog');
            $objects    = $repository->load($criteria, FALSE);

            if ($objects)
            {
                foreach ($objects as $object)
                {
                    // se você usa transformer via parent::setTransformer, mantém:
                    if (method_exists($this, 'onBeforeLoad')) {
                        call_user_func([$this, 'onBeforeLoad'], $object);
                    }

                    $this->datagrid->addItem($object);
                }
            }

            // total para o paginador
            $countCriteria = clone $criteria;
            $countCriteria->resetProperties();
            $count = $repository->count($countCriteria);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            $this->loaded = false;
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }


  
    public function onSearch($param = null)
    {
        // get the form data
        $data = $this->form->getData();

        // store data in the session
        TSession::setValue('SystemChangeLogView_filter_data', $data);

        // fill the form with data again
        $this->form->setData($data);

        // force reload with first page
        $param = (array) $param;
        $param['offset'] = 0;
        $param['first_page'] = 1;

        return $this->onReload($param);
    }


}
