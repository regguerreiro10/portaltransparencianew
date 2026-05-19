<?php

//<fileHeader>
  
//</fileHeader>

class TabelaFipeFinalCorrigida extends TRecord
{
    const TABLENAME  = 'tabela_fipe_final_corrigida';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    
    
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
        parent::addAttribute('Type');
        parent::addAttribute('Brand_Code');
        parent::addAttribute('Brand_Value');
        parent::addAttribute('Model_Code');
        parent::addAttribute('Model_Value');
        parent::addAttribute('Year_Code');
        parent::addAttribute('Year_Value');
        parent::addAttribute('Fipe_Code');
        parent::addAttribute('Fuel_Letter');
        parent::addAttribute('Fuel_Type');
        parent::addAttribute('Price');
        parent::addAttribute('Month');
        parent::addAttribute('Especie');
        parent::addAttribute('Familia');
        parent::addAttribute('Propriedade');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

