<?php

//<fileHeader>
  
//</fileHeader>

class ViewPessoaCnh extends TRecord
{
    const TABLENAME  = 'view_pessoa_cnh';
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
        parent::addAttribute('numero_registro_cnh');
        parent::addAttribute('data_validade_cnh');
        parent::addAttribute('status_cnh');
        parent::addAttribute('ordem_status');
        parent::addAttribute('dias_para_vencer');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('system_unit_name');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    


    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

