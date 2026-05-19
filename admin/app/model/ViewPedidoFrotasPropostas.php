<?php

//<fileHeader>
  
//</fileHeader>

class ViewPedidoFrotasPropostas extends TRecord
{
    const TABLENAME  = 'view_pedido_frotas_propostas';
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
        parent::addAttribute('descricaopedido');
        parent::addAttribute('dt_pedido');
        parent::addAttribute('valor_total');
        parent::addAttribute('valor_desconto_proposta');
        parent::addAttribute('valor_liquido_proposta');
        parent::addAttribute('estado_pedido_frotas_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('estabelecimento_id');
        parent::addAttribute('cidade_id');
        parent::addAttribute('dt_finalizacao');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('placa');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

