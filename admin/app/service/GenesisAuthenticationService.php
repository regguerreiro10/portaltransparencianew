<?php

class GenesisAuthenticationService extends ApplicationAuthenticationService
{
    public static function authenticate($email,  $password,$emailcliente=null, $load_session_vars = true)
    {
        $ini  = AdiantiApplicationConfig::get();
        
        TTransaction::open('permission');

        if ($emailcliente<>null) {
           $user = SystemUsers2::validate( $emailcliente );
        } else {
           $user = SystemUsers2::validate( $email );
        }
        
        // call loaders to made available this attrs outside transactions
        $user->get_unit();
        $user->get_frontpage();
        
        if ($user)
        {
            if (!empty($ini['permission']['auth_service']) and class_exists($ini['permission']['auth_service']))
            {
                $service = $ini['permission']['auth_service'];
                $service::authenticate( $login, $password );
            }
            else
            {
                SystemUsers2::authenticate( $email, $password );
            }
            
            if($load_session_vars)
            {
                self::loadSessionVars($user);
            }
            
            TTransaction::close();
            
            return $user;
        }
        
        TTransaction::close();
    }
    
    private static function validateUser($email)
    {
        $user = self::newFromemail($email);
    
        if ($user instanceof SystemUsers)
        {
            if ($user->active == 'N')
            {
                throw new Exception(_t('Inactive user'));
            }
        }
        else
        {
            throw new Exception(_t('User not found'));
        }
    
        return $user;
    }
}
