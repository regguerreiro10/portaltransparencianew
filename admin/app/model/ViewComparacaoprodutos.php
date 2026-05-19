<?php

//<fileHeader>
  
//</fileHeader>

class ViewComparacaoprodutos extends TRecord
{
    const TABLENAME  = 'view_comparacaoprodutos';
    const PRIMARYKEY = 'item_proposta_id';
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
        parent::addAttribute('estabelecimento_id');
        parent::addAttribute('nome_estabelecimento');
        parent::addAttribute('cidade_id');
        parent::addAttribute('nomecidade');
        parent::addAttribute('estado_id');
        parent::addAttribute('uf');
        parent::addAttribute('produto_id');
        parent::addAttribute('nome_produto');
        parent::addAttribute('tipo_produto_id');
        parent::addAttribute('valor');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

