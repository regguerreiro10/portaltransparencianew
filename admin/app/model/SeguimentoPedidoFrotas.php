<?php

//<fileHeader>
  
//</fileHeader>

class SeguimentoPedidoFrotas extends TRecord
{
    const TABLENAME  = 'seguimento_pedido_frotas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    
    private $pedido_frotas;
    private $seguimento;
    
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
        parent::addAttribute('pedido_frotas_id');
        parent::addAttribute('seguimento_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_pedido_frotas
     * Sample of usage: $var->pedido_frotas = $object;
     * @param $object Instance of PedidoFrotas
     */
    public function set_pedido_frotas(PedidoFrotas $object)
    {
        $this->pedido_frotas = $object;
        $this->pedido_frotas_id = $object->id;
    }
    
    /**
     * Method get_pedido_frotas
     * Sample of usage: $var->pedido_frotas->attribute;
     * @returns PedidoFrotas instance
     */
    public function get_pedido_frotas()
    {
        
        // loads the associated object
        if (empty($this->pedido_frotas))
            $this->pedido_frotas = new PedidoFrotas($this->pedido_frotas_id);
        
        // returns the associated object
        return $this->pedido_frotas;
    }
    /**
     * Method set_seguimento
     * Sample of usage: $var->seguimento = $object;
     * @param $object Instance of Seguimento
     */
    public function set_seguimento(Seguimento $object)
    {
        $this->seguimento = $object;
        $this->seguimento_id = $object->id;
    }
    
    /**
     * Method get_seguimento
     * Sample of usage: $var->seguimento->attribute;
     * @returns Seguimento instance
     */
    public function get_seguimento()
    {
        
        // loads the associated object
        if (empty($this->seguimento))
            $this->seguimento = new Seguimento($this->seguimento_id);
        
        // returns the associated object
        return $this->seguimento;
    }
    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

