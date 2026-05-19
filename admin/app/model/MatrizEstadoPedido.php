<?php

class MatrizEstadoPedido extends TRecord
{
    const TABLENAME  = 'matriz_estado_pedido';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $estado_pedido_venda_origem;
    private $estado_pedido_venda_destino;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('estado_pedido_venda_origem_id');
        parent::addAttribute('estado_pedido_venda_destino_id');
            
    }

    /**
     * Method set_estado_pedido
     * Sample of usage: $var->estado_pedido = $object;
     * @param $object Instance of EstadoPedido
     */
    public function set_estado_pedido_venda_origem(EstadoPedido $object)
    {
        $this->estado_pedido_venda_origem = $object;
        $this->estado_pedido_venda_origem_id = $object->id;
    }

    /**
     * Method get_estado_pedido_venda_origem
     * Sample of usage: $var->estado_pedido_venda_origem->attribute;
     * @returns EstadoPedido instance
     */
    public function get_estado_pedido_venda_origem()
    {
    
        // loads the associated object
        if (empty($this->estado_pedido_venda_origem))
            $this->estado_pedido_venda_origem = new EstadoPedido($this->estado_pedido_venda_origem_id);
    
        // returns the associated object
        return $this->estado_pedido_venda_origem;
    }
    /**
     * Method set_estado_pedido
     * Sample of usage: $var->estado_pedido = $object;
     * @param $object Instance of EstadoPedido
     */
    public function set_estado_pedido_venda_destino(EstadoPedido $object)
    {
        $this->estado_pedido_venda_destino = $object;
        $this->estado_pedido_venda_destino_id = $object->id;
    }

    /**
     * Method get_estado_pedido_venda_destino
     * Sample of usage: $var->estado_pedido_venda_destino->attribute;
     * @returns EstadoPedido instance
     */
    public function get_estado_pedido_venda_destino()
    {
    
        // loads the associated object
        if (empty($this->estado_pedido_venda_destino))
            $this->estado_pedido_venda_destino = new EstadoPedido($this->estado_pedido_venda_destino_id);
    
        // returns the associated object
        return $this->estado_pedido_venda_destino;
    }

    
}

