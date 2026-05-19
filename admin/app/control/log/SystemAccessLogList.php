<?php

use Adianti\Database\TTransaction;

/**
 * SystemAccessLogList
 *
 * @version    1.0
 * @package    control
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemAccessLogList extends TStandardList
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        parent::setDatabase('log');            // defines the database
        parent::setActiveRecord('SystemAccessLog');   // defines the active record
        parent::setDefaultOrder('id', 'desc');         // defines the default order
        parent::addFilterField('login', 'like'); // add a filter field
        parent::addFilterField('login_time', '>=', 'login_time_ini');
        parent::addFilterField('logout_time', '<=', 'logout_time_fim');
       
        parent::setLimit(20);
        
        $criteria = new TCriteria;
        $userunitid = (int) TSession::getValue('userunitid');
        $logins = [];
        
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
        $stmt->execute([$userunitid]);
        $logins = $stmt->fetchAll(PDO::FETCH_COLUMN);
        TTransaction::close();

        if (!empty($logins))
        {
            // Garante apenas logins existentes em system_users da unidade logada
            $criteria->add(new TFilter('login', 'IN', $logins));
        }
        else
        {
            // Sem usuários para a unidade logada: retorna vazio
            $criteria->add(new TFilter('id', '=', -1));
        }

        parent::setCriteria($criteria);

        $basename = urlencode('log-acesso-list.pdf');
        $download = "download.php?file=app/manual/log-acesso-list.pdf&basename={$basename}";

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
        $this->form = new BootstrapFormBuilder('form_search_SystemAccessLog');
        $this->form->setFormTitle("Log de Acesso {$manual}");
        
        // create the form fields
        $login = new TEntry('login');
        $login_time_ini = new TDateTime('login_time_ini');
        $logout_time_fim = new TDateTime('logout_time_fim');
        $login_time_ini->setMask('dd/mm/yyyy hh:ii');
        $login_time_ini->setDatabaseMask('yyyy-mm-dd hh:ii:ss');
        $logout_time_fim->setMask('dd/mm/yyyy hh:ii');
        $logout_time_fim->setDatabaseMask('yyyy-mm-dd hh:ii:ss');

        // add the fields
        $this->form->addFields( [new TLabel(_t('Login'))], [$login] );
        $this->form->addFields( [new TLabel('Login inicial')], [$login_time_ini], [new TLabel('Logout até')], [$logout_time_fim] );
        $login->setSize('100%');
        $login_time_ini->setSize('100%');
        $logout_time_fim->setSize('100%');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('SystemAccessLog_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->setHeight(320);
        

        // creates the datagrid columns
        $id = $this->datagrid->addQuickColumn('id', 'id', 'center');
        $sessionid = $this->datagrid->addQuickColumn('sessionid', 'sessionid', 'left');
        $login = $this->datagrid->addQuickColumn(_t('Login'), 'login', 'center');
        $impersonated_by = $this->datagrid->addQuickColumn(_t('Impersonated by'), 'impersonated_by', 'center');
        $login_time = $this->datagrid->addQuickColumn('login_time', 'login_time', 'center');
        $logout_time = $this->datagrid->addQuickColumn('logout_time', 'logout_time', 'center');
        $access_ip = $this->datagrid->addQuickColumn('IP', 'access_ip', 'center');
        
        $action = new TDataGridAction(['SystemSqlLogList', 'filterSession'], ['session_id' => '{sessionid}']);
        $action2 = new TDataGridAction(['SystemChangeLogView', 'filterSession'], ['session_id' => '{sessionid}']);
        $action3 = new TDataGridAction(['SystemRequestLogList', 'filterSession'], ['session_id' => '{sessionid}']);
        
        $action->setImage('fa:database blue');
        $action2->setImage('fa:film green');
        $action3->setImage('fa:globe orange');
        $action->setLabel(_t('SQL Log'));
        $action2->setLabel(_t('Change Log'));
        $action3->setLabel(_t('Request Log'));
        $action->setUseButton(true);
        $action2->setUseButton(true);
        $action3->setUseButton(true);
        $this->datagrid->addAction($action); 
        $this->datagrid->addAction($action2);
        $this->datagrid->addAction($action3);
        
        $login->setTransformer( function($value, $object, $row) {
            if ($object->impersonated == 'Y')
            {
                $div = new TElement('span');
                $div->class = "label label-info";
                $div->style = "text-shadow:none; font-size:12px";
                $div->add(_t('Impersonated'));
                
                return $value . ' ' . $div;
            }
            return $value;
        });
        
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $id->setAction($order_id);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
}
