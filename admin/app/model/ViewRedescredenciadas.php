<?php

//<fileHeader>
  
//</fileHeader>

class ViewRedescredenciadas extends TRecord
{
    const TABLENAME  = 'view_redescredenciadas';
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
        parent::addAttribute('rua');
        parent::addAttribute('cidade_id');
        parent::addAttribute('nomecidade');
        parent::addAttribute('sigla');
        parent::addAttribute('email');
        parent::addAttribute('horariofuncionamento');
        parent::addAttribute('responsavel');
        parent::addAttribute('proprietario');
        parent::addAttribute('data_desativacao');
        parent::addAttribute('fone');
        parent::addAttribute('NFProduto');
        parent::addAttribute('NFServico');
        parent::addAttribute('QTDEOS');
        parent::addAttribute('QTDEOSAndamento');
        parent::addAttribute('MediaAvaliacao');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

