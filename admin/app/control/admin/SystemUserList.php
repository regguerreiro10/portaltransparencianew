<?php

class SystemUserList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'SystemUsers';
    private static $primaryKey = 'id';
    private static $formName = 'formList_SystemUsers';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Usuários");
        $this->limit = 20;

        $id = new TEntry('id');
        $name = new TEntry('name');
        $email = new TEntry('email');
        $active = new TCombo('active');

  // calcula idade média da unidade logada
        $qtdeusuario = self::SomaQtdeUsuario((int) TSession::getValue('idunit'));
   

        // monta o badge com padding e cor neutra
        $badgeQtde = new TElement('span');
        $badgeQtde->class = 'label label-default';
        $badgeQtde->style = 'margin-right:12px; padding:4px 10px; font-size:12px; display:inline-flex; align-items:center; justify-content:center; border-radius:12px; background-color:#6c757d; color:#fff; line-height:1;';
        $badgeQtde->add('Quantidade: ' . $qtdeusuario);

        // adiciona o badge antes do menu Exportar 
        $active->addItems(["Y"=>"Sim","N"=>"Não"]);
        $id->setSize('100%');
        $name->setSize('100%');
        $email->setSize('100%');
        $active->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("ID:", null, '14px', null, '100%'),$id],[new TLabel("Nome:", null, '14px', null),$name],[new TLabel("Email:", null, '14px', null),$email],[new TLabel("Ativo:", null, '14px', null),$active]);
        $row1->layout = [' col-sm-2',' col-sm-4',' col-sm-4','col-sm-2'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        $btn_onedit = $this->form->addAction("Novo", new TAction(['SystemUserForm', 'onEdit']), 'fas:plus #69aa46');
        $this->btn_onedit = $btn_onedit;

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_name = new TDataGridColumn('name', "Name", 'left');
        $column_login = new TDataGridColumn('login', "Login", 'left');
        $column_email = new TDataGridColumn('email', "Email", 'left');
        $column_active_transformed = new TDataGridColumn('active', "Ativo", 'left');
        $column_two_factor_enabled = new TDataGridColumn('two_factor_enabled', "2FA", 'center');
        $column_notificarusuario = new TDataGridColumn('notificarusuario', "Notificar", 'center');

        $column_active_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $class = ($value=='N') ? 'danger' : 'success';
            $label = ($value=='N') ? _t('No') : _t('Yes');
            $div = new TElement('span');
            $div->class="label label-{$class}";
            $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
            $div->add($label);
            return $div;
        });       
           $column_two_factor_enabled->setTransformer( function($value, $object, $row) {
            $class = (empty($value) || $value=='N') ? 'danger' : 'success';
            $label = (empty($value) || $value=='N') ? _t('No') : _t('Yes');
            $div = new TElement('span');
            $div->class="label label-{$class}";
            $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
            
            $div->add($label);
            return $div;
        });
 
         $column_notificarusuario->setTransformer( function($value, $object, $row) {
            $class = (empty($value) || $value=='2') ? 'danger' : 'success';
            $label = (empty($value) || $value=='2') ? _t('No') : _t('Yes');
            $div = new TElement('span');
            $div->class="label label-{$class}";
            $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
            
            $div->add($label);
            return $div;
        });

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_login);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_two_factor_enabled);
        $this->datagrid->addColumn($column_active_transformed);
        $this->datagrid->addColumn($column_notificarusuario);
        $action_onEdit = new TDataGridAction(array('SystemUserForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('SystemUserList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('far:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

        $action_onTurnOnOff = new TDataGridAction(array('SystemUserList', 'onTurnOnOff'));
        $action_onTurnOnOff->setUseButton(false);
        $action_onTurnOnOff->setButtonClass('btn btn-default btn-sm');
        $action_onTurnOnOff->setLabel("Ativar/desativar");
        $action_onTurnOnOff->setImage('fas:power-off #ff892a');
        $action_onTurnOnOff->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onTurnOnOff);
 
        // create ONOFF action
        $action_person = new TDataGridAction(array('System2FAForm', 'onEditarUsuario'));
        $action_person->setButtonClass('btn btn-default');
        $action_person->setLabel('Configurar 2FA');
        $action_person->setImage('fa:lock #FF5722');
        $action_person->setFields(['id','login','email']);


        $this->datagrid->addAction($action_person);


        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->addFooter($this->pageNavigation);
$panel->addHeaderWidget($badgeQtde);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["admin","Usuários"]));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public function onDelete($param = null) 
    { 
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);
                $conn = TConnection::open('minierp');

                // instantiates object
                $object = new SystemUsers($key, FALSE); 

                 $aprovador = Aprovador::where('system_user_id', '=', $key)->first();
                if ($aprovador) {
                   throw new Exception("Não é possível excluir o usuário, pois ele está vinculado a um aprovador. Por favor, remova o vínculo antes de excluir o usuário.");
                }
                   $aprovadorfrotas = AprovadorFrotas::where('system_users_id', '=', $key)->first();
                if ($aprovadorfrotas) {
                   throw new Exception("Não é possível excluir o usuário, pois ele está vinculado a um aprovador. Por favor, remova o vínculo antes de excluir o usuário.");
                }
                $pedido = PedidoFrotas::where('system_users_id', '=', $key)->first();
                if ($pedido) {
                   throw new Exception("Não é possível excluir o usuário, pois ele está vinculado a um pedido de frota. Por favor, remova o vínculo antes de excluir o usuário.");
                }
                $pedidocompra = Pedido::where('system_users_id', '=', $key)->first();
                if ($pedidocompra) {
                   throw new Exception("Não é possível excluir o usuário, pois ele está vinculado a um pedido de compra. Por favor, remova o vínculo antes de excluir o usuário.");
                }
                


                // deletes the object from the database
                $object->delete();

                $sql = 'delete FROM system_user_departamento_unit where system_users_id not in (select id from system_users)  and system_users_id =  ' . $param['key'];
                $Recordsudu = $conn->query($sql);

               
                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
    }
    public function onTurnOnOff($param = null) 
    {

        try
        {
            TTransaction::open('permission');
            $user = SystemUsers::find($param['id']);
            if ($user instanceof SystemUsers)
            {
                $user->active = $user->active == 'Y' ? 'N' : 'Y';
                $user->store();
            }

            TTransaction::close();

            $this->onReload($param);
            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->name) AND ( (is_scalar($data->name) AND $data->name !== '') OR (is_array($data->name) AND (!empty($data->name)) )) )
        {

            $filters[] = new TFilter('name', 'like', "%{$data->name}%");// create the filter 
        }

        if (isset($data->email) AND ( (is_scalar($data->email) AND $data->email !== '') OR (is_array($data->email) AND (!empty($data->email)) )) )
        {

            $filters[] = new TFilter('email', 'like', "%{$data->email}%");// create the filter 
        }

        if (isset($data->active) AND ( (is_scalar($data->active) AND $data->active !== '') OR (is_array($data->active) AND (!empty($data->active)) )) )
        {

            $filters[] = new TFilter('active', '=', $data->active);// create the filter 
        }

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'minierp'
            TTransaction::open(self::$database);

            // creates a repository for SystemUsers
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            $user   = new SystemUsers(TSession::getValue('userid'));
            $email  = strtolower((string)($user->email ?? ''));
            $login  = strtolower((string)($user->login ?? '')); // caso "admin" seja login
            $dominios = ['np3', 'xp3'];

            $isAdmin = ($email === 'admin' || $login === 'admin');
            $isDominioInterno = false;
            foreach ($dominios as $dominio)
            {
                if (strpos($email, $dominio) !== false)
                {
                    $isDominioInterno = true;
                    break;
                }
            }

            if ($isDominioInterno || $isAdmin) {
                // não filtra
            } else {
                $criteria->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit')));
            }
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;

            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public static function manageRow($id, $param = [])
    {
        $list = new self($param);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new SystemUsers($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }
    private static function SomaQtdeUsuario(?int $systemUnitId = null): ?float
    {
        
        $unit = $systemUnitId ?? (int) TSession::getValue('idunit');

        try {
            TTransaction::open(self::$database);
            $pdo = TTransaction::get();

            $sql = "
                SELECT count(*) AS qtde
                FROM system_users su
                WHERE su.system_unit_id = :unit
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':unit'      => $unit,
            ]);

            $qtde = $stmt->fetchColumn();
            TTransaction::close();

            return $qtde !== false ? (float) $qtde : null;
        } catch (Exception $e) {
            TTransaction::rollback();
            throw $e;
        }
    }



}

