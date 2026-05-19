<?php

//<fileHeader>
  
//</fileHeader>

class ViewPedidosAsCliente extends TRecord
{
    const TABLENAME  = 'view_pedidos_as_cliente';
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
        parent::addAttribute('pessoa_id');
        parent::addAttribute('id_pessoa');
        parent::addAttribute('nome');
        parent::addAttribute('fone');
        parent::addAttribute('email');
        parent::addAttribute('id_pessoa_endereco');
        parent::addAttribute('cidade_id');
        parent::addAttribute('id_cidade');
        parent::addAttribute('estado_id');
        parent::addAttribute('nome_cidade');
        parent::addAttribute('id_estado');
        parent::addAttribute('sigla');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}