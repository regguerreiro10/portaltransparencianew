<?php

//<fileHeader>
  
//</fileHeader>

class EstadoPedidoFrotasAprovador extends TRecord
{
    const TABLENAME  = 'estado_pedido_frotas_aprovador';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    
    private $estado_pedido_frotas;
    private $aprovador_frotas;
    
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
        parent::addAttribute('aprovador_frotas_id');
        parent::addAttribute('estado_pedido_frotas_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_estado_pedido_frotas
     * Sample of usage: $var->estado_pedido_frotas = $object;
     * @param $object Instance of EstadoPedidoFrotas
     */
    public function set_estado_pedido_frotas(EstadoPedidoFrotas $object)
    {
        $this->estado_pedido_frotas = $object;
        $this->estado_pedido_frotas_id = $object->id;
    }
    
    /**
     * Method get_estado_pedido_frotas
     * Sample of usage: $var->estado_pedido_frotas->attribute;
     * @returns EstadoPedidoFrotas instance
     */
    public function get_estado_pedido_frotas()
    {
        
        // loads the associated object
        if (empty($this->estado_pedido_frotas))
            $this->estado_pedido_frotas = new EstadoPedidoFrotas($this->estado_pedido_frotas_id);
        
        // returns the associated object
        return $this->estado_pedido_frotas;
    }
    /**
     * Method set_aprovador_frotas
     * Sample of usage: $var->aprovador_frotas = $object;
     * @param $object Instance of AprovadorFrotas
     */
    public function set_aprovador_frotas(AprovadorFrotas $object)
    {
        $this->aprovador_frotas = $object;
        $this->aprovador_frotas_id = $object->id;
    }
    
    /**
     * Method get_aprovador_frotas
     * Sample of usage: $var->aprovador_frotas->attribute;
     * @returns AprovadorFrotas instance
     */
    public function get_aprovador_frotas()
    {
        
        // loads the associated object
        if (empty($this->aprovador_frotas))
            $this->aprovador_frotas = new AprovadorFrotas($this->aprovador_frotas_id);
        
        // returns the associated object
        return $this->aprovador_frotas;
    }
    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}