<?php

//<fileHeader>
  
//</fileHeader>

class Viewsaldoempenho extends TRecord
{
    const TABLENAME  = 'viewsaldoempenho';
    const PRIMARYKEY = 'dt_pedido';
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
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('entidade_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('total_servicos');
        parent::addAttribute('total_produtos');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

