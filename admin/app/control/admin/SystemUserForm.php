<?php

use Adianti\Widget\Wrapper\TDBCheckList;

class SystemUserForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'SystemUsers';
    private static $primaryKey = 'id';
    private static $formName = 'form_SystemUsers';

    use BuilderMasterDetailFieldListTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Usuário");

        $criteria_system_unit_id = new TCriteria();
        $criteria_frontpage_id = new TCriteria();
        $criteria_units = new TCriteria();
        
        $criteria_system_user_departamento_unit_system_users_departamento_unit_id = new TCriteria();
        $criteria_groups = new TCriteria();
        $criteria_system_programs = new TCriteria();
        $criteria_system_user_departamento_unit = new TCriteria();

        // $unitsselected = TSession::getValue('unitsselected');
        // if (is_array($unitsselected) && !empty($unitsselected)) {
        //     $criteria_system_user_departamento_unit->add(new TFilter('system_unit_id', 'in', $unitsselected));
        // } else {
        //     // força resultado vazio para não quebrar a query
        //     $criteria_system_user_departamento_unit->add(new TFilter('id', '=', 0));
        // }
        $id = new TEntry('id');
        $name = new TEntry('name');
        $cpf = new TEntry('cpf');
        $cpf->setMask('999.999.999-99');
        $login = new TEntry('login');
        $email = new TEntry('email');
        $bhelper_681d0fddd0dc2 = new BHelper();
        $system_unit_id = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_system_unit_id );
        $frontpage_id = new TDBCombo('frontpage_id', 'minierp', 'SystemProgram', 'id', '{name}','name asc' , $criteria_frontpage_id );
        $password = new TPassword('password');
        $repassword = new TPassword('repassword');
        $units = new TDBCheckGroup('units', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_units );
        $notificarusuario = new TCheckButton('notificarusuario');
        $notificarusuario->setValue('2');
        $notificarusuario->setUseSwitch(true, 'blue');
        $notificarusuario->setIndexValue("1");
        $notificarusuario->setInactiveIndexValue("2");
        // após criar $units e $system_user_departamento_unit
        $units->setChangeAction(new TAction([$this, 'onChangeUnits']));
        $system_user_departamento_unit_system_users_id = new THidden('system_user_departamento_unit_system_users_id[]');
        $system_user_departamento_unit_system_users___row__id = new THidden('system_user_departamento_unit_system_users___row__id[]');
        $system_user_departamento_unit_system_users___row__data = new THidden('system_user_departamento_unit_system_users___row__data[]');
        $system_user_departamento_unit_system_users_departamento_unit_id = new TDBCombo('system_user_departamento_unit_system_users_departamento_unit_id[]', 'minierp', 'DepartamentoUnit', 'id', '{system_unit->name}  - {name}','name asc' , $criteria_system_user_departamento_unit_system_users_departamento_unit_id );
        
        $this->fieldList_6685b7dc2e48f = new TFieldList();
        $groups = new TDBCheckGroup('groups', 'minierp', 'SystemGroup', 'id', '{name}','name asc' , $criteria_groups );
        $system_programs = new TCheckList('system_programs');
        $system_user_departamento_unit = new TCheckList('system_user_departamento_unit');

        $this->fieldList_6685b7dc2e48f->addField(null, $system_user_departamento_unit_system_users_id, []);
        $this->fieldList_6685b7dc2e48f->addField(null, $system_user_departamento_unit_system_users___row__id, ['uniqid' => true]);
        $this->fieldList_6685b7dc2e48f->addField(null, $system_user_departamento_unit_system_users___row__data, []);
        $this->fieldList_6685b7dc2e48f->addField(new TLabel("Departamento unit id", null, '14px', null), $system_user_departamento_unit_system_users_departamento_unit_id, ['width' => '100%']);

        $this->fieldList_6685b7dc2e48f->width = '100%';
        $this->fieldList_6685b7dc2e48f->setFieldPrefix('system_user_departamento_unit_system_users');
        $this->fieldList_6685b7dc2e48f->name = 'fieldList_6685b7dc2e48f';

        $this->criteria_fieldList_6685b7dc2e48f = new TCriteria();
        $this->default_item_fieldList_6685b7dc2e48f = new stdClass();

        $this->form->addField($system_user_departamento_unit_system_users_id);
        $this->form->addField($system_user_departamento_unit_system_users___row__id);
        $this->form->addField($system_user_departamento_unit_system_users___row__data);
        $this->form->addField($system_user_departamento_unit_system_users_departamento_unit_id);

        $this->fieldList_6685b7dc2e48f->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $name->addValidation("Nome", new TRequiredValidator()); 
        $login->addValidation("Login", new TRequiredValidator()); 
        $password->addValidation("Password", new TRequiredValidator()); 

        $id->setEditable(false);
        $bhelper_681d0fddd0dc2->enableHover();
        $bhelper_681d0fddd0dc2->setSide("right");
        $bhelper_681d0fddd0dc2->setIcon(new TImage("fas:question #fa931f"));
        $bhelper_681d0fddd0dc2->setTitle("Email");
        $bhelper_681d0fddd0dc2->setContent("Atenção: os e-mails informados devem ser válidos.
        <br>Eles serão utilizados para notificações dentro do sistema e também para envio de mensagens por e-mail.
        <br>
        <br>");
        $units->setLayout('horizontal');
        $groups->setLayout('horizontal');

        $frontpage_id->enableSearch();
        $system_unit_id->enableSearch();
        $system_user_departamento_unit_system_users_departamento_unit_id->enableSearch();

        $id->setSize('100%');
        $units->setSize(200);
        $groups->setSize(200);
        $name->setSize('100%');
        $cpf->setSize('100%');
        $email->setSize('90%');
        $login->setSize('100%');
        $password->setSize('100%');
        $repassword->setSize('100%');
        $frontpage_id->setSize('100%');
        $system_unit_id->setSize('100%');
        $bhelper_681d0fddd0dc2->setSize('18');
        $system_user_departamento_unit_system_users_departamento_unit_id->setSize('100%');

        $system_programs->setIdColumn('id');

        $column_system_programs_id = $system_programs->addColumn('id', "ID", 'center' , '33%');
        $column_system_programs_name = $system_programs->addColumn('name', "Nome", 'center' , '33%');
        $column_system_programs_controller_transformed = $system_programs->addColumn('controller', "Caminho do menu", 'center' , '33%');

        $column_system_programs_controller_transformed->setTransformer(function($value, $object, $row)
        {
            $menuparser = new TMenuParser('menu.xml');
            $paths = $menuparser->getPath($value);

            if ($paths)
            {
                return implode(' &raquo; ', $paths);
            }

        });        

        $system_programs->setHeight(250);
        $system_programs->makeScrollable();

        $system_programs->fillWith('minierp', 'SystemProgram', 'id', 'name asc' , $criteria_system_programs);


         $system_user_departamento_unit->setIdColumn('id');

        $column_system_user_departamento_unit_id = $system_user_departamento_unit->addColumn('id', "ID", 'center' , '10%');
        $column_system_user_departamento_unit_name = $system_user_departamento_unit->addColumn('name', "Nome", 'left' , '90%');

        $column_system_user_departamento_unit_name->setTransformer(function($value, $object, $row)
        {
            TTransaction::open('minierp');
            $unid1 = new SystemUnit($object->system_unit_id);
            if ($unid1)
            {
                TTransaction::close();
                return $unid1->name.' - '.$object->name;
            }
            TTransaction::close();

        });        

        $system_user_departamento_unit->setHeight(250);
        $system_user_departamento_unit->makeScrollable();

        $system_user_departamento_unit->fillWith('minierp', 'DepartamentoUnit', 'id', 'system_unit_id, name asc' , $criteria_system_user_departamento_unit);

        $row1 = $this->form->addFields([new TLabel("ID", null, '14px', null, '100%'),$id]);
        $row1->layout = ['col-sm-3'];

        $row2 = $this->form->addFields([new TLabel("Nome", '#ff0000', '14px', null),$name],[new TLabel("E-mail", '#FF0000', '14px', null, '100%'),$email,$bhelper_681d0fddd0dc2]);
        $row2->layout = [' col-sm-6',' col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Unidade principal", null, '14px', null),$system_unit_id],[new TLabel("Tela inicial", null, '14px', null),$frontpage_id]);
        $row3->layout = [' col-sm-6',' col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("CPF", 'null', '14px', null),$cpf],[new TLabel("Login", '#ff0000', '14px', null),$login]);
        $row4->layout = [' col-sm-6',' col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Senha", '#FF0000', '14px', null),$password],[new TLabel("Confirma senha", '#FF0000', '14px', null),$repassword]);
        $row5->layout = [' col-sm-6',' col-sm-6'];

          $row6 = $this->form->addFields([new TLabel("Notificar Usuário ?", null, '14px', null, '100%'),$notificarusuario],[]);
        $row6->layout = [' col-sm-6',' col-sm-6'];
        $tab_6685724cc2d06 = new BootstrapFormBuilder('tab_6685724cc2d06');
        $this->tab_6685724cc2d06 = $tab_6685724cc2d06;
        $tab_6685724cc2d06->setProperty('style', 'border:none; box-shadow:none;');

        $tab_6685724cc2d06->appendPage("Orgão / Unidades");

        $tab_6685724cc2d06->addFields([new THidden('current_tab_tab_6685724cc2d06')]);
        $tab_6685724cc2d06->setTabFunction("$('[name=current_tab_tab_6685724cc2d06]').val($(this).attr('data-current_page'));");

        $row6 = $tab_6685724cc2d06->addFields([$units]);
        $row6->layout = [' col-sm-12'];

        $tab_6685724cc2d06->appendPage("Escolha qual Unidades / Dep / Secretárias o Usuário esta alocado");
        // $row6 = $tab_6685724cc2d06->addFields([$this->fieldList_6685b7dc2e48f]);
        $row7 = $tab_6685724cc2d06->addFields([$system_user_departamento_unit]);
        $row7->layout = [' col-sm-12'];

        $tab_6685724cc2d06->appendPage("Grupos");
        $row8 = $tab_6685724cc2d06->addFields([$groups]);
        $row8->layout = [' col-sm-12'];

        $tab_6685724cc2d06->appendPage("Programas");
        $row9 = $tab_6685724cc2d06->addFields([$system_programs]);
        $row9->layout = [' col-sm-12'];

        $row10 = $this->form->addFields([$tab_6685724cc2d06]);
        $row10->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onreload = $this->form->addAction("Voltar", new TAction(['SystemUserList', 'onReload']), 'far:arrow-alt-circle-left #478fca');
        $this->btn_onreload = $btn_onreload;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=SystemUserForm]');
        $style->width = '70% !important';   
        $style->show(true);

    }

    public function onSave($param = null) 
    {
        try 
{
  // open a transaction with database 'permission'
            TTransaction::open('minierp');

            $object = new SystemUsers;
            $object->fromArray( $param );

            $data = $this->form->getData();
            $this->form->setData($data);

            $senha = $object->password;
            $object->notificarusuario = $data->notificarusuario;

            if( empty($object->login) )
            {
                throw new Exception(TAdiantiCoreTranslator::translate('The field ^1 is required', _t('Login')));
            }

            if( empty($object->id) )
            {
                if (SystemUsers::newFromLogin($object->login) instanceof SystemUsers)
                {
                    throw new Exception(_t('An user with this login is already registered'));
                }

                if (SystemUsers::newFromEmail($object->email) instanceof SystemUsers)
                {
                    throw new Exception(_t('An user with this e-mail is already registered'));
                }

                if ( empty($object->password) )
                {
                    throw new Exception(TAdiantiCoreTranslator::translate('The field ^1 is required', _t('Password')));
                }

                $object->active = 'Y';
            }

            if( $object->password )
            {
                if( $object->password !== $param['repassword'] )
                    throw new Exception(_t('The passwords do not match'));

                $object->password = md5($object->password);
            }
            else
            {
                unset($object->password);
            }

            $object->store();
            $object->clearParts();

            if( !empty($param['groups']) )
            {
                foreach( $param['groups'] as $group_id )
                {
                    $object->addSystemUserGroup( new SystemGroup($group_id) );
                }
            }

            if( !empty($param['units']) )
            {
                foreach( $param['units'] as $unit_id )
                {
                    $object->addSystemUserUnit( new SystemUnit($unit_id) );
                }
            }

            if (!empty($data->system_programs))
            {
                foreach ($data->system_programs as $program)
                {
                    $object->addSystemUserProgram( new SystemProgram( $program ) );
                }
            }
            // logo após $object->store() (ou antes de regravar as relações)
            SystemUserDepartamentoUnit::where('system_users_id', '=', $object->id)->delete();

             // agora re-adiciona os selecionados
            if (!empty($data->system_user_departamento_unit)) {
                foreach ($data->system_user_departamento_unit as $depId) {
                    $userdep = new SystemUserDepartamentoUnit;
                    $userdep->departamento_unit_id = $depId;
                    $userdep->system_users_id = $object->id;
                    $userdep->store();
                    // $object->addSystemUserDepartamentoUnit(new DepartamentoUnit($depId));
                }
            }

            
/*
            $seguimento_pessoa_pessoa_items = $this->storeItems('SystemUserDepartamentoUnit', 'system_users_id', $object, $this->fieldList_666bb0ef7738c, function($masterObject, $detailObject){ 

                //code here

            }, $this->criteria_fieldList_666bb0ef7738c); 
*/

                // $this->fieldList_6685b7dc2e48f_items = $this->storeItems('SystemUserDepartamentoUnit', 'system_users_id', $object, $this->fieldList_6685b7dc2e48f, function($masterObject, $detailObject){ 

                //     //code here

                // }, $this->criteria_fieldList_6685b7dc2e48f); 
    //</hideLine> //</fieldList-2093476-17007375>
           // $departamento_itens = $this->storeItems('SystemUserDepartamentoUnit','system_users_id',$object,$this->detalhe_departamento, function($masterObject, $detailObject) {
        //    }, $this->criteria_detalhe_departamento);

            $data = new stdClass;
            $data->id = $object->id;
            TForm::sendData('form_System_user', $data);

            // close the transaction
            TTransaction::close();

            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));

        }
        catch (Exception $e) 
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());

            // undo all pending operations
            TTransaction::rollback();    
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new SystemUsers($key); // instantiates the Active Record 
                unset($object->password);

                $object->units = SystemUserUnit::where('system_user_id', '=', $object->id)->getIndexedArray('system_unit_id', 'system_unit_id');

                $object->groups = SystemUserGroup::where('system_user_id', '=', $object->id)->getIndexedArray('system_group_id', 'system_group_id');

                $object->system_programs = SystemUserProgram::where('system_user_id', '=', $object->id)->getIndexedArray('system_program_id', 'system_program_id');

                $object->system_user_departamento_unit = SystemUserDepartamentoUnit::where('system_users_id', '=', $object->id)->getIndexedArray('departamento_unit_id', 'departamento_unit_id');

            // // Selecionados do usuário (departamentos)
            //     $selectedDeps = SystemUserDepartamentoUnit::where('system_users_id', '=', $object->id)
            //                     ->getIndexedArray('departamento_unit_id', 'departamento_unit_id');

                // Ids das unidades já associadas ao usuário (converta para int)
                $unitIds = [];
                if (!empty($object->units)) {
                    // se $object->units vem indexado por id:
                    $unitIds = array_map('intval', array_keys((array) $object->units));

                    // se $object->units for um array de objetos SystemUnit, use:
                    // $unitIds = array_map(fn($u) => (int) $u->id, (array) $object->units);
                }
                TSession::setValue('unitsselected', null); // para uso futuro, se necessário
                TSession::setValue('unitsselected', $unitIds); // para uso futuro, se necessário
                // $criteria = new TCriteria();

                // // Se houver unidades, filtra por elas; se não, força resultado vazio
                // if (!empty($unitIds)) {
                //     $criteria->add(new TFilter('system_unit_id', 'in', $unitIds)); // << array, sem parênteses!
                // } else {
                //     // evita IN () e não traz nada (ajuste conforme sua UX: talvez trazer todos)
                //     $criteria->add(new TFilter('id', '=', 0));
                // }

                // $deps = DepartamentoUnit::getObjects($criteria);

                // // Monte as opções (ajuste o campo do rótulo conforme seu AR: name ou nome)
                // $options = [];
                // foreach ($deps as $d) {
                //     $options[(int) $d->id] = $d->name ?? $d->nome;
                // }

                // // Selecionados: TCheckList espera array de valores selecionados
                // $selected = array_values($selectedDeps); // ex.: [3, 7, 9]

                // // Recarrega o checklist
                // TCheckList::reload($this->form->getName(), 'system_user_departamento_unit', $options, $selected, true);

                // $this->fieldList_6685b7dc2e48f_items = $this->loadItems('SystemUserDepartamentoUnit', 'system_users_id', $object, $this->fieldList_6685b7dc2e48f, function($masterObject, $detailObject, $objectItems){ 

                //     //code here

                // }, $this->criteria_fieldList_6685b7dc2e48f); 

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
                $this->onClear($param);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

        $this->fieldList_6685b7dc2e48f->addHeader();
        $this->fieldList_6685b7dc2e48f->addDetail($this->default_item_fieldList_6685b7dc2e48f);

        $this->fieldList_6685b7dc2e48f->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    }

    public function onShow($param = null)
    {
        $this->fieldList_6685b7dc2e48f->addHeader();
        $this->fieldList_6685b7dc2e48f->addDetail($this->default_item_fieldList_6685b7dc2e48f);

        $this->fieldList_6685b7dc2e48f->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    } 

    public static function getFormName()
    {
        return self::$formName;
    }
   

   public static function onChangeUnits($param)
    {
        try {
            // $form = self::$formName; // 'form_SystemUsers'

            // // Normaliza e guarda na sessão (se precisar em outros pontos)
            // $ids = isset($param['units']) ? (array) $param['units'] : [];
            // $ids = array_values(array_filter(array_map('intval', $ids)));
            // TSession::setValue('unitsselected', $ids);

            // TTransaction::open('minierp');

            // $options = [];

            // if (!empty($ids)) {
            //     $criteria = new TCriteria();
            //     // IN deve receber array
            //     $criteria->add(new TFilter('system_unit_id', 'in', $ids));
            //     $criteria->setProperty('order', 'system_unit_id, name');
            //     $criteria->setProperty('direction', 'asc');

            //     $repo = new TRepository('DepartamentoUnit');
            //     $items = $repo->load($criteria, FALSE);

            //     if ($items) {
            //         foreach ($items as $it) {
            //             // Mostra "Unidade - Departamento" (se quiser só o nome, use $it->name)
            //             $options[(int)$it->id] = "{$it->system_unit->name} - {$it->name}";
            //         }
            //     }
            // }

            // // Recarrega o checklist (limpa seleção atual)
            // // TCheckList::reloadFromModel($form, 'system_user_departamento_unit', $options, [], true);
            
            
            // $objectpro = new stdClass();
            // $objectpro->system_user_departamento_unit = $ids;

            // TForm::sendData('form_SystemUsers', $objectpro);

            // TTransaction::close();
        } catch (Exception $e) {
            // TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
}

