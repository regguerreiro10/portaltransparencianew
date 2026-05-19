<?php

class SystemUsers2 extends SystemUsers
{

    public static function validate($email, $emailcliente = null)
    {
        $user = self::newFromEmail($email);
        
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
    
    public static function authenticate($email, $password)
    {
        $user = self::newFromEmail($email);
        if ($user->password !== md5($password))
        {
            throw new Exception(_t('Wrong password'));
        }
        
        return $user;
    }
}