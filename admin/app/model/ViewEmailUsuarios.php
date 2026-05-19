<?php

//<fileHeader>
  
//</fileHeader>

class ViewEmailUsuarios extends TRecord
{
    const TABLENAME  = 'view_email_usuarios';
    const PRIMARYKEY = 'system_users_id';
    const IDPOLICY   =  'max'; // {max, serial}
    

    
    
    
    //<classProperties>
  
    //</classProperties>
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        //<onBeforeConstruct>
  
        //</onBeforeConstruct>
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('email');
        parent::addAttribute('system_unit_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

