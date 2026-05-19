<?php
/**
 * SystemSessionDumpView
 *
 * @version    1.0
 * @package    control
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemSessionDumpView extends TPage
{
    private $datagrid;
    
    public function __construct()
    {
        parent::__construct();
        
            $ini = ini_get_all();
        $log_errors      = $ini['log_errors']['local_value'];
        $error_log       = $ini['error_log']['local_value'] ?? '';
        $display_errors  = $ini['display_errors']['local_value'];

        if (empty($log_errors))
        {
            new TMessage('warning', 'Este formulário possui acesso exclusivo para o login Administrador, conforme as permissões do sistema.' );
            return;
        }
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        $basename   = urlencode('session-dump-list.pdf');
        $download   = "download.php?file=app/manual/session-dump-list.pdf&basename={$basename}";

        $manual = "
            <span style='float:right;'>
                <a href='{$download}'
                target='_blank'
                style='text-decoration:none;margin-left:10px;'>
                    <i class='fa fa-question-circle'> </i>
                </a>
            </span>
        "; 
        
        $name  = new TDataGridColumn('name',    _t('Name'),    'left',   '20%');
        $value = new TDataGridColumn('value',   _t('Value'),   'left',   '80%');
        
        $value->setTransformer(function($value) {
            return '<pre style="border:none;background:none">'.print_r($value,true).'</pre>';
        });
        
        $this->datagrid->addColumn($name);
        $this->datagrid->addColumn($value);
        
        $action1 = new TDataGridAction([$this, 'onDeleteSessionVar'],   ['name'=>'{name}' ] );
        $action1->setUseButton(TRUE);
        $this->datagrid->addAction($action1, _t('Delete'), 'fas:trash-alt red');
        
        $input_search = new TEntry('input_search');
        $input_search->placeholder = _t('Search');
        $input_search->setSize('100%');
        
        $this->datagrid->enableSearch($input_search, 'name');
        $this->datagrid->createModel();
        
        if ($_SESSION[APPLICATION_NAME])
        {
            foreach ($_SESSION[APPLICATION_NAME] as $name => $value)
            {
                $data = new stdClass;
                $data->name = $name;
                $data->value = $value;
                
                $this->datagrid->addItem($data);
            }
        }
        
        $panel = new TPanelGroup("Sessão {$manual}");
        $panel->addHeaderWidget($input_search);
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        
        $vbox = new TVBox;
        $vbox->style = 'width:100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);
        
        parent::add($panel);
    }
    
    /**
     * Ask before deletion
     */
    public static function onDeleteSessionVar($param)
    {
        $action1 = new TAction(array(__CLASS__, 'deleteSessionVar'));
        $action1->setParameters($param);
        new TQuestion('Do you really want to delete ?', $action1);
    }
    
    /**
     * Delete session var
     */
    public static function deleteSessionVar($param)
    {
        TSession::delValue($param['name']);
        AdiantiCoreApplication::gotoPage('SystemSessionDumpView');
    }
}
