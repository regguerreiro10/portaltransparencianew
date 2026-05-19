<?php

//<fileHeader>
  
//</fileHeader>

class ViewSaldoempenhocompras extends TRecord
{
    const TABLENAME  = 'view_saldoempenhocompras';
    const PRIMARYKEY = 'saldo_departamento_id';
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
        parent::addAttribute('entidade_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('mes');
        parent::addAttribute('ano');
        parent::addAttribute('estado_pedido_venda_id');
        parent::addAttribute('documento_empenho');
        parent::addAttribute('total_produtos');
        parent::addAttribute('saldo_empenho');
        parent::addAttribute('saldoatual');
        parent::addAttribute('numero_documento_empenho');
        parent::addAttribute('datatransacao');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

