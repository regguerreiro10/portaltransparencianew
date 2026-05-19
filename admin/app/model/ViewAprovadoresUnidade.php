<?php

//<fileHeader>
  
//</fileHeader>

class ViewAprovadoresUnidade extends TRecord
{
    const TABLENAME  = 'view_aprovadores_unidade';
    const PRIMARYKEY = 'aprovador_frotas_id';
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
        parent::addAttribute('id');
        parent::addAttribute('email');
        parent::addAttribute('name');
        parent::addAttribute('login');
        parent::addAttribute('system_group_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('active');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

