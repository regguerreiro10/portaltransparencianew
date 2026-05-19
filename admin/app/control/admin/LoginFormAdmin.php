<?php
/**
 * LoginForm
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class LoginFormAdmin extends TPage
{
    protected $form; // form
    protected $recaptcha_key; // form
    protected $recaptcha_secret_key; // form
    protected $utilizarecaptcha='N';
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct($param)
    {
        parent::__construct();
        
        $ini  = AdiantiApplicationConfig::get();
       
        $this->recaptcha_key='6LfrMv8pAAAAAPZeNxVsI5buBu5a_CV6keU2GHkm';
        $this->recaptcha_secret_key='6LfrMv8pAAAAAEydcydC5bBb1PnIUu8Nt9VWEkuQ';
        $this->style = 'clear:both';
        // creates the form
        $this->form = new BootstrapFormBuilder('form_login');
        $this->form->setFormTitle( 'Faça o seu login' );
       // $this->form->style = 'background:app/images/builderBackgroundLogin.png';
        
        // create the form fields
     //   $login = new TEntry('login');
        $email = new TEntry('email');
        $emailcliente = new TEntry('emailcliente');
        $password = new TPassword('password');

        $previous_class = new THidden('previous_class');
        $previous_method = new THidden('previous_method');
        $previous_parameters = new THidden('previous_parameters');
        
        TSession::setValue('recaptcha0',NULL);
         TTransaction::open('minierp');
        $unit0 = SystemUnit::where('id','=',1)
                           ->load();
        if ($unit0) {
            foreach($unit0 as $units0){
                TSession::setValue('recaptcha0',$units0->recaptcha);
                break;                   
            }
        }
        TTransaction::close();
        if (!empty($param['previous_class']) && $param['previous_class'] !== 'LoginForm')
        {
            $previous_class->setValue($param['previous_class']);
            
            if (!empty($param['previous_method']))
            {
                $previous_method->setValue($param['previous_method']);
            }
            
            $previous_parameters->setValue(serialize($param));
        }
        
        // define the sizes
       // $login->setSize('100%', 40);
        $email->setSize('100%', 40);
        $emailcliente->setSize('100%', 40);
        $password->setSize('100%', 40);

    //    $login->style = 'height:35px; font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        $email->style = 'height:35px; font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        $emailcliente->style = 'height:35px; font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        $password->style = 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        
      //  $login->placeholder = _t('User');
        $email->placeholder = _t('User');
        $emailcliente->placeholder = _t('User');
        $password->placeholder = _t('Password');
        
        $email->autofocus = 'autofocus';

        $user   = '<span class="login-avatar"><span class="fa fa-user"></span></span>';
        $mail   = '<span class="login-avatar"><span class="fa fa-envelope"></span></span>';
        $locker = '<span class="login-avatar"><span class="fa fa-lock"></span></span>';
//        $unit   = '<span class="login-avatar"><span class="fa fa-university"></span></span>';
        $unit   = '';
        $lang   = '<span class="login-avatar"><span class="fa fa-globe"></span></span>';
        
        $row = $this->form->addFields( [$mail, $email] );
        $row->layout = ['col-sm-12 display-flex'];
        if ($param['previous_class'] == 'LoginFormAdmin') {
            $row = $this->form->addFields( [$mail, $emailcliente] );
            $row->layout = ['col-sm-12 display-flex'];
        }

        $row = $this->form->addFields( [$locker, $password] );
        $row->layout = ['col-sm-12 display-flex'];
        $this->form->addFields( [$previous_class, $previous_method, $previous_parameters] );
        if (!empty($ini['general']['multiunit']) and $ini['general']['multiunit'] == '1')
        {
            $unit_id = new TCombo('unit_id');
            $unit_id->enableSearch();
            $unit_id->setSize('100%');
            $unit_id->style = 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
            $row = $this->form->addFields( [$unit, $unit_id] );
            $row->layout = ['col-sm-12 display-flex'];
            $email->setExitAction(new TAction( [$this, 'onExitUser'] ) );
        }
        
        if (!empty($ini['general']['multi_lang']) and $ini['general']['multi_lang'] == '1')
        {
            $lang_id = new TCombo('lang_id');
            $lang_id->setSize('100%');
            $lang_id->style = 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
            $lang_id->addItems( $ini['general']['lang_options'] );
            $lang_id->setValue( $ini['general']['language'] );
            $lang_id->setDefaultOption(FALSE);
            $row = $this->form->addFields( [$lang, $lang_id] );
            $row->layout = ['col-sm-12 display-flex'];
        }
        
        $btn = $this->form->addAction('ENTRAR', new TAction(array($this, 'onLogin')), '');
        $btn->class = 'btn ';
        $btn->style = 'height: 40px;width: 90%;display: block;margin: auto;font-size:17px;';

     
        if (TSession::getValue('recaptcha0')=='S') {
            $element=new TElement('div');
            $element->class ='g-recaptcha brochure__form__captcha';
            $element->setProperty('data-sitekey',$this->recaptcha_key);
        
            $recaptcha_div = new TElement('div');
            $recaptcha_div->style ='text-align:center';
            $recaptcha_div->add($element);
            $this->form->addFooterWidget($recaptcha_div);
        }
        
        
        $wrapper = new TElement('div');
        $wrapper->style = 'margin:auto; margin-top:100px;max-width:460px;';
        $wrapper->id = 'login-wrapper';
        
        $h3 = new TElement('h1');
        $h3->style = 'text-align:center;';
        $h3->add('Bem-Vindo');
        
        $divLogo = new TElement('div');
        $divLogo->class = 'login-medium-logo';
        
        $wrapper->add($divLogo);
        $wrapper->add($h3);
        $wrapper->add($this->form);
        
        if (TSession::getValue('recaptcha0')=='S') {
           $script = new TElement('script');
           $script->src = 'https://www.google.com/recaptcha/api.js';
           $this->form->addFooterWidget($script);
        
           $style= new TElement('style');
           $style->add('.g-recaptcha div {margin-left: auto;margin-right: auto;margin-top:15px;}');
           $this->form->addFooterWidget($style);
        }
        parent::add($wrapper);
    }
    
    /**
     * user exit action
     * Populate unit combo
     */
    public static function onExitUser($param)
    {
        try
        {
            TTransaction::open('permission');
            
            $user = SystemUsers::newFromEmail( $param['email'] );
            if ($user instanceof SystemUsers)
            {
                $units = $user->getSystemUserUnits();
                $options = [];
                
                if ($units)
                {
                    foreach ($units as $unit)
                    {
                        $options[$unit->id] = $unit->name;
                    }
                }
                TCombo::reload('form_login', 'unit_id', $options);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error',$e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Authenticate the User
     */
    public function onLogin($param)
    {
        $ini  = AdiantiApplicationConfig::get();
        
        try
        {
            $data = (object) $param;
            
            (new TRequiredValidator)->validate( _t('Email'),    $data->email);
            (new TRequiredValidator)->validate( _t('Password'), $data->password);
            
            if (!empty($ini['general']['multiunit']) and $ini['general']['multiunit'] == '1')
            {
                (new TRequiredValidator)->validate( _t('Unit'), $data->unit_id);
            }
            
            if (!empty($ini['general']['require_terms']) && $ini['general']['require_terms'] == '1' && !empty($param['usage_term_policy']) AND empty($data->accept))
            {
                throw new Exception(_t('You need read and agree to the terms of use and privacy policy'));
            }

            TSession::regenerate();

            TScript::create("__adianti_clear_tabs()");
             if (TSession::getValue('recaptcha0')=='S') {
                $recaptcha = $data->{'g-recaptcha-response'};
                $result=$this->reCaptcha($recaptcha);

              //  if (!$result['success'] ){
              //     throw new Exception('Erro no Captcha');
              //  }
             }
            $user = GenesisAuthenticationService::authenticate( $data->email, $data->password );
            $term_policy = SystemPreference::findInTransaction('permission', 'term_policy');

            if (!empty($ini['general']['require_terms']) && $ini['general']['require_terms'] == '1' && $user->accepted_term_policy !== 'Y' && !empty($term_policy) && empty($data->accept))
            {
                TSession::freeSession();
                $param['usage_term_policy'] = 'Y';
                $action = new TAction(['LoginForm', 'onLogin'], $param);
                $form = new BootstrapFormBuilder('term_policy');

                $content = new TElement('div');
                $content->style = "max-height: 45vh; overflow: auto; margin-bottom: 10px;";
                $content->add($term_policy->preference);

                $check = new TCheckGroup('accept');
                $check->addItems(['Y' => _t('I have read and agree to the terms of use and privacy policy')]);

                $form->addContent([$content]);
                $form->addFields([$check]);
                $form->addAction( _t('Accept'), $action, 'fas:check');

                return new TInputDialog(_t('Terms of use and privacy policy'), $form);
        
            }
            
            if (!empty($ini['general']['require_terms']) && $ini['general']['require_terms'] == '1' && $user->accepted_term_policy !== 'Y' && !empty($term_policy) && !empty($data->accept))
            {
                TTransaction::open('permission');
                $user->accepted_term_policy = 'Y';
                $user->accepted_term_policy_at = date('Y-m-d H:i:s');
                $user->store();
                TTransaction::close();
            }

            if ($user)
            {
                TTransaction::open('permission');
                ApplicationAuthenticationService::setUnit( $data->unit_id ?? null );
                ApplicationAuthenticationService::setLang( $data->lang_id ?? null );
                SystemAccessLogService::registerLogin();
                SystemAccessNotificationLogService::registerLogin();
            
                TSession::setValue('idunit', $data->unit_id);
                TSession::setValue('userunitid', $data->unit_id);
                TSession::setValue('iduser', $user->id);
            
                $unit = new SystemUnit($data->unit_id);
                TSession::setValue('entidade',null); 
                 TSession::setValue('taxacontrato',null); 
                          
                if ($unit) {
                    TSession::setValue('entidade',$unit->entidade_id);
                    $entidade = New Entidade(TSession::getValue('entidade'));
                    TSession::setValue('taxacontrato',$entidade->taxacontrato);
                    if ($entidade->compras==1) {
                       TSession::setValue('sistema','compras');
                    } elseif ($entidade->frotas==1) {
                       TSession::setValue('sistema','frotas');
                    }
                    TSession::setValue('aprovacao_por_item',$unit->aprovacao_por_item);
                    TSession::setValue('selecao_redes_aleatoria',$unit->selecao_redes_aleatoria);
                }

                $suserdep = SystemUserDepartamentoUnit::where('system_users_id','=',TSession::getValue('userid'))
                                                      ->load();
                TSession::setValue('depunitid',null);
                if ($suserdep)
                {
                    foreach($suserdep as $depunit){
                        TSession::setValue('depunitid', $depunit->departamento_unit_id);
                    }
                    
                }
              //  var_dump($depunit->departamento_unit_id);
             //   die();
                
              //  $conexao   = TTransaction::get(); 
              //  $conexao->exec( "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));" );
             //   TTransaction::close();
                $frontpage = $user->frontpage;
                if (!empty($param['previous_class']) && $param['previous_class'] !== 'LoginForm')
                {
                    AdiantiCoreApplication::gotoPage($param['previous_class'], $param['previous_method'], unserialize($param['previous_parameters'])); // reload
                }
                else if ($frontpage instanceof SystemProgram and $frontpage->controller)
                {
                    AdiantiCoreApplication::gotoPage($frontpage->controller); // reload
                    TSession::setValue('frontpage', $frontpage->controller);
                }
                else
                {
                    AdiantiCoreApplication::gotoPage('EmptyPage'); // reload
                    TSession::setValue('frontpage', 'EmptyPage');
                }
            }
        }
        catch (Exception $e)
        {
            TSession::freeSession();
            new TMessage('error',$e->getMessage());
            // sleep(2);
            TTransaction::rollback();
        }
    }
    
    /** 
     * Reload permissions
     */
    public static function reloadPermissions()
    {
        try
        {
            TTransaction::open('permission');
            $user = SystemUsers::newFromLogin( TSession::getValue('login') );
            
            if ($user)
            {
                ApplicationAuthenticationService::loadSessionVars($user, false);
                
                $frontpage = $user->frontpage;
                if ($frontpage instanceof SystemProgram AND $frontpage->controller)
                {
                    TApplication::gotoPage($frontpage->controller); // reload
                }
                else
                {
                    TApplication::gotoPage('EmptyPage'); // reload
                }
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     *
     */
    public function onLoad($param)
    {
    }
    
    /**
     * Logout
     */
    public static function onLogout()
    {
        SystemAccessLogService::registerLogout();
        TSession::freeSession();
        AdiantiCoreApplication::gotoPage('LoginForm', '');
    }
    
    protected function reCaptcha($recaptcha)
    {
        $secret = $this->recaptcha_secret_key;
        $ip = $_SERVER['REMOTE_ADDR'];

        $postvars = array("secret" => $secret, "response" => $recaptcha, "remoteip" => $ip);
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data, true);
    }
    public function onMensagem($classe)
    {
            TTransaction::open('minierp');
            $alertprogram = AlertaProgram::where('system_unit_id', '=', TSession::getValue('idunit'))
                ->where('system_program_id', 'in', "(select id from system_program where controller = '{$classe}')")
                ->where('ativo', '=', 1)
                ->load();            
    
             $mensagem = '';
            if ($alertprogram) {
                foreach ($alertprogram as $ap) {  
                    $mensagem = $ap->mensagem .'<br><br>';
                }
            }
            return $mensagem;
            TTransaction::close();

    }

}
