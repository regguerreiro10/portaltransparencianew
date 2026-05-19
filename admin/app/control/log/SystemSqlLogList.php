<?php
/**
 * SystemSqlLogList
 *
 * @version    1.0
 * @package    control
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemSqlLogList extends TStandardList
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $loaded;

    private const SEARCH_FLAG = 'SystemSqlLog_has_searched';

    private function hasSqlTextFilter($data): bool
    {
        return !empty(trim((string) ($data->sql_command ?? '')))
            || !empty(trim((string) ($data->sql_command_2 ?? '')));
    }

    private function getAllowedLoginsByUnit($unitId)
    {
        $unitId = (int) $unitId;
        if ($unitId <= 0)
        {
            return [];
        }

        $cacheKey = 'SystemSqlLogList_allowed_logins_' . $unitId;
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
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        parent::setDatabase('log');            // defines the database
        parent::setActiveRecord('SystemSqlLog');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        parent::addFilterField('login', 'like'); // add a filter field
        parent::addFilterField('database_name', 'like'); // add a filter field
        parent::addFilterField('sql_command', 'like'); // add a filter field
        parent::addFilterField('sql_command_2', 'like'); // add a filter field
        parent::addFilterField('class_name', 'like'); // add a filter field
        parent::addFilterField('session_id', 'like'); // add a filter field
        parent::addFilterField('request_id', '='); // add a filter field
        parent::addFilterField('logdate', '>=', 'logdate_start'); // add a filter field
        parent::addFilterField('logdate', '<=', 'logdate_end'); // add
        parent::setLimit(20);

      

        $basename   = urlencode('log-sql-list.pdf');
        $download   = "download.php?file=app/manual/log-sql-list.pdf&basename={$basename}";

        $manual = "
            <span style='float:right;'>
                <a href='{$download}'
                target='_blank'
                style='text-decoration:none;margin-left:10px;'>
                    <i class='fa fa-question-circle'> </i>
                </a>
            </span>
        "; 
        
        // creates the form, with a table inside
        $this->form = new BootstrapFormBuilder('form_search_SystemSqlLog');
        $this->form->setFormTitle("SQL Log {$manual}");
        
        // create the form fields
        $login       = new TEntry('login');
        $database    = new TEntry('database_name');
        $sql         = new TEntry('sql_command');
        $sql2        = new TEntry('sql_command_2');
        $class_name  = new TEntry('class_name');
        $session_id  = new TEntry('session_id');
        $request_id  = new TEntry('request_id');
        $logdate_start = new TDateTime('logdate_start');
        $logdate_end   = new TDateTime('logdate_end');

        // máscara para o usuário (com hora e minuto – se quiser, pode incluir segundos)
        $logdate_start->setMask('dd/mm/yyyy hh:ii');
        $logdate_end->setMask('dd/mm/yyyy hh:ii');

        // máscara para o banco (ajuste para o formato do seu campo DATETIME/TIMESTAMP)
        $logdate_start->setDatabaseMask('yyyy-mm-dd hh:ii:ss');
        $logdate_end->setDatabaseMask('yyyy-mm-dd hh:ii:ss');


        // add the fields
        $this->form->addFields( [new TLabel(_t('Login'))], [$login], [new TLabel(_t('Program'))], [$class_name] );
        $this->form->addFields( [new TLabel(_t('Database'))], [$database], [new TLabel(_t('Session'))], [$session_id] );
        $this->form->addFields( [new TLabel('SQL 1')], [$sql], [new TLabel('SQL 2')], [$sql2] );
        $this->form->addFields( [new TLabel(_t('Request'))], [$request_id] );
        $this->form->addFields( [new TLabel('Data Inicial')], [$logdate_start], [new TLabel('Data Final')], [$logdate_end] );
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('SystemSqlLog_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setGroupColumn('transaction_id', 'Transaction: <b>{transaction_id}</b>');
        $this->datagrid->enablePopover(_t('Execution trace'), '{log_trace_formatted}');
        
        // creates the datagrid columns
        //$transaction_id = $this->datagrid->addQuickColumn('Uniqid', 'transaction_id', 'center');
        $id = $this->datagrid->addQuickColumn('ID', 'id', 'center', 50, new TAction(array($this, 'onReload')), array('order', 'id'));
        $logdate = $this->datagrid->addQuickColumn(_t('Date'), 'logdate', 'center', NULL, new TAction(array($this, 'onReload')), array('order', 'logdate'));
        $login = $this->datagrid->addQuickColumn(_t('Login'), 'login', 'center', NULL, new TAction(array($this, 'onReload')), array('order', 'login'));
        $database = $this->datagrid->addQuickColumn(_t('Database'), 'database_name', 'center', NULL, new TAction(array($this, 'onReload')), array('order', 'database_name'));
        $sql = $this->datagrid->addQuickColumn('SQL', 'sql_command', 'left', NULL);
        $class_name = $this->datagrid->addQuickColumn(_t('Program'), 'class_name', 'center');
        $php_sapi = $this->datagrid->addQuickColumn('SAPI', 'php_sapi', 'center');
        $access_ip  = $this->datagrid->addQuickColumn('IP', 'access_ip', 'center');
        
        $sql->setTransformer(function($sql_string) {
            $original_sql = $sql_string;
            $m = [];
            preg_match_all("/'([^']+)'/", $sql_string, $matches);
            
            if (count($matches[0]) > 0)
            {
                foreach ($matches[0] as $found_string)
                {
                    $sql_string = str_replace($found_string, '<b class="orange">'.$found_string.'</b>', $sql_string);
                }
            }
            
            $sql_string = str_replace('INSERT INTO ', '<b class="blue">INSERT INTO </b>', $sql_string);
            $sql_string = str_replace('DELETE FROM ', '<b class="blue">DELETE FROM </b>', $sql_string);
            $sql_string = str_replace('UPDATE ',  '<b class="blue">UPDATE </b>',  $sql_string);
            $sql_string = str_replace(' FROM ',   '<b class="blue"> FROM </b>',   $sql_string);
            $sql_string = str_replace(' WHERE ',  '<b class="blue"> WHERE </b>',  $sql_string);
            $sql_string = str_replace(' SET ',    '<b class="blue"> SET </b>',    $sql_string);
            $sql_string = str_replace(' VALUES ', '<b class="blue"> VALUES </b>', $sql_string);
            
            $div = new TElement('span');
            $div->style="text-shadow:none; font-size:12px";
            if (substr($original_sql, 0, 11) == 'INSERT INTO')
            {
                $div->class="label label-success";
                $div->add('INSERT');
            }
            else if (substr($original_sql, 0, 11) == 'DELETE FROM')
            {
                $div->class="label label-danger";
                $div->add('DELETE');
            }
            if (substr($original_sql, 0, 6) == 'UPDATE')
            {
                $div->class="label label-info";
                $div->add('UPDATE');
            }
            
            return $div . $sql_string;
        });
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid)->style='overflow-x:auto';
        $panel->addFooter($this->pageNavigation);
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
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

        TSession::setValue('SystemSqlLog_filter_data', $data);
        TSession::setValue(self::SEARCH_FLAG, true);

        $this->onReload($param);
    }
    
    /**
     *
     */
    public function filterRequest($param)
    {
        parent::clearFilters();
        
        $data = new stdClass;
        $data->request_id = $param['request_id'] ?? null;
        $this->form->setData($data);

        TSession::setValue('SystemSqlLog_filter_data', $data);
        TSession::setValue(self::SEARCH_FLAG, true);

        $this->onReload($param);
    }

        /**
     * Load the datagrid
     */
    public function onReload($param = NULL)
    {
        try
        {
            if (!TSession::getValue(self::SEARCH_FLAG))
            {
                $this->datagrid->clear();
                $this->pageNavigation->setCount(0);
                $this->pageNavigation->setProperties((array) $param);
                $this->pageNavigation->setLimit($this->limit ?? 20);
                $this->loaded = true;
                return;
            }

            $userunitid = (int) TSession::getValue('userunitid');
            $logins = $this->getAllowedLoginsByUnit($userunitid);
            TTransaction::open('log');

            // cria criterio
            $criteria = new TCriteria;

            // critérios externos (quando a tela pai usa $this->filter_criteria)
            if (!empty($this->filter_criteria))
            {
                $criteria->add($this->filter_criteria);
            }

            // filtros vindos da sessão (setados no onSearch)
            $data = TSession::getValue('SystemSqlLog_filter_data');
            if ($data)
            {
                $this->form->setData($data);

                // aqui você pode aplicar filtros manualmente (principalmente datas)
                if (!empty($data->login))
                {
                    $criteria->add(new TFilter('login', 'like', "%{$data->login}%"));
                }

                if (!empty($data->database_name))
                {
                    $criteria->add(new TFilter('database_name', 'like', "%{$data->database_name}%"));
                }

                if (!empty($data->sql_command))
                {
                    $criteria->add(new TFilter('sql_command', 'like', "%{$data->sql_command}%"));
                }

                if (!empty($data->sql_command_2))
                {
                    $criteria->add(new TFilter('sql_command', 'like', "%{$data->sql_command_2}%"));
                }

                if (!empty($data->class_name))
                {
                    $criteria->add(new TFilter('class_name', 'like', "%{$data->class_name}%"));
                }

                if (!empty($data->session_id))
                {
                    $criteria->add(new TFilter('session_id', 'like', "%{$data->session_id}%"));
                }

                if (!empty($data->request_id))
                {
                    $criteria->add(new TFilter('request_id', '=', (int) $data->request_id));
                }

                // Datas (TDateTime com databaseMask já vai vir no formato yyyy-mm-dd hh:ii:ss)
                if (!empty($data->logdate_start))
                {
                    $criteria->add(new TFilter('logdate', '>=', $data->logdate_start));
                }

                if (!empty($data->logdate_end))
                {
                    $criteria->add(new TFilter('logdate', '<=', $data->logdate_end));
                }
            }

            // paginação
            $hasSqlTextFilter = $this->hasSqlTextFilter($data ?? null);
            $param = (array) $param;
            $limit = $this->limit ?? 20;
            $criteria->setProperty('limit', $hasSqlTextFilter ? ($limit + 1) : $limit);

            if (isset($param['offset']))
            {
                $criteria->setProperty('offset', (int) $param['offset']);
            }
            else
            {
                $criteria->setProperty('offset', 0);
            }

            // ordenação
            if (isset($param['order']))
            {
                $criteria->setProperty('order', $param['order']);
                $criteria->setProperty('direction', $param['direction'] ?? 'asc');
            }
            else
            {
                $criteria->setProperty('order', 'id');
                $criteria->setProperty('direction', 'asc');
            }

            if (!empty($logins))
            {
                $criteria->add(new TFilter('login', 'IN', $logins));
            }
            else
            {
                $criteria->add(new TFilter('id', '=', -1));
            }

            // limpa grid
            $this->datagrid->clear();

            // carrega objetos
            $repository = new TRepository('SystemSqlLog');
            $objects    = $repository->load($criteria, FALSE);
            $hasMore = false;

            if ($hasSqlTextFilter && is_array($objects) && count($objects) > $limit)
            {
                $hasMore = true;
                $objects = array_slice($objects, 0, $limit);
            }

            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }

            // contador total
            if ($hasSqlTextFilter)
            {
                $offset = (int) $criteria->getProperty('offset');
                $loadedCount = is_array($objects) ? count($objects) : 0;
                $count = $offset + $loadedCount + ($hasMore ? 1 : 0);
            }
            else
            {
                $countCriteria = clone $criteria;
                $countCriteria->resetProperties();
                $count = $repository->count($countCriteria);
            }

            // navegação
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Search datagrid
     */
    public function onSearch($param = NULL)
    {
        try
        {
            // pega dados do form
            $data = $this->form->getData();

            // guarda no form e na sessão
            $this->form->setData($data);
            TSession::setValue('SystemSqlLog_filter_data', $data);
            TSession::setValue(self::SEARCH_FLAG, true);

            // reseta paginação e recarrega
            $param = (array) $param;
            $param['offset'] = 0;
            $param['first_page'] = 1;

            $this->onReload($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Clear filters
     */
    public function onClear($param = NULL)
    {
        $this->form->clear(true);
        TSession::setValue('SystemSqlLog_filter_data', NULL);
        TSession::setValue(self::SEARCH_FLAG, false);

        $this->datagrid->clear();
        $this->pageNavigation->setCount(0);
        $this->pageNavigation->setProperties((array) $param);
        $this->pageNavigation->setLimit($this->limit ?? 20);
        $this->loaded = true;
    }

    /**
     * Show page
     */
    public function show()
    {
        parent::show();
    }

}
