<?php

//<fileHeader>
  
//</fileHeader>

class ViewRedesdisponiveis extends TRecord
{
    const TABLENAME  = 'view_redesdisponiveis';
    const PRIMARYKEY = 'id';
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
        parent::addAttribute('nome');
        parent::addAttribute('documento');
        parent::addAttribute('fone');
        parent::addAttribute('email');
        parent::addAttribute('cidade_id');
        parent::addAttribute('estado_id');
        parent::addAttribute('selo');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

