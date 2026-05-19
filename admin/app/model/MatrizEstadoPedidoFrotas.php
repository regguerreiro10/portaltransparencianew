<?php

//<fileHeader>
  
//</fileHeader>

class MatrizEstadoPedidoFrotas extends TRecord
{
    const TABLENAME  = 'matriz_estado_pedido_frotas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    
    private $estado_pedido_frotas_origem;
    private $estado_pedido_frotas_destino;
    
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
        parent::addAttribute('estado_pedido_frotas_origem_id');
        parent::addAttribute('estado_pedido_frotas_destino_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_estado_pedido_frotas
     * Sample of usage: $var->estado_pedido_frotas = $object;
     * @param $object Instance of EstadoPedidoFrotas
     */
    public function set_estado_pedido_frotas_origem(EstadoPedidoFrotas $object)
    {
        $this->estado_pedido_frotas_origem = $object;
        $this->estado_pedido_frotas_origem_id = $object->id;
    }
    
    /**
     * Method get_estado_pedido_frotas_origem
     * Sample of usage: $var->estado_pedido_frotas_origem->attribute;
     * @returns EstadoPedidoFrotas instance
     */
    public function get_estado_pedido_frotas_origem()
    {
        
        // loads the associated object
        if (empty($this->estado_pedido_frotas_origem))
            $this->estado_pedido_frotas_origem = new EstadoPedidoFrotas($this->estado_pedido_frotas_origem_id);
        
        // returns the associated object
        return $this->estado_pedido_frotas_origem;
    }
    /**
     * Method set_estado_pedido_frotas
     * Sample of usage: $var->estado_pedido_frotas = $object;
     * @param $object Instance of EstadoPedidoFrotas
     */
    public function set_estado_pedido_frotas_destino(EstadoPedidoFrotas $object)
    {
        $this->estado_pedido_frotas_destino = $object;
        $this->estado_pedido_frotas_destino_id = $object->id;
    }
    
    /**
     * Method get_estado_pedido_frotas_destino
     * Sample of usage: $var->estado_pedido_frotas_destino->attribute;
     * @returns EstadoPedidoFrotas instance
     */
    public function get_estado_pedido_frotas_destino()
    {
        
        // loads the associated object
        if (empty($this->estado_pedido_frotas_destino))
            $this->estado_pedido_frotas_destino = new EstadoPedidoFrotas($this->estado_pedido_frotas_destino_id);
        
        // returns the associated object
        return $this->estado_pedido_frotas_destino;
    }
    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}
