<?php

class EstadoPedidoAprovador extends TRecord
{
    const TABLENAME  = 'estado_pedido_aprovador';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $estado_pedido_venda;
    private $aprovador;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('estado_pedido_venda_id');
        parent::addAttribute('aprovador_id');
            
    }

    /**
     * Method set_estado_pedido
     * Sample of usage: $var->estado_pedido = $object;
     * @param $object Instance of EstadoPedido
     */
    public function set_estado_pedido_venda(EstadoPedido $object)
    {
        $this->estado_pedido_venda = $object;
        $this->estado_pedido_venda_id = $object->id;
    }

    /**
     * Method get_estado_pedido_venda
     * Sample of usage: $var->estado_pedido_venda->attribute;
     * @returns EstadoPedido instance
     */
    public function get_estado_pedido_venda()
    {
    
        // loads the associated object
        if (empty($this->estado_pedido_venda))
            $this->estado_pedido_venda = new EstadoPedido($this->estado_pedido_venda_id);
    
        // returns the associated object
        return $this->estado_pedido_venda;
    }
    /**
     * Method set_aprovador
     * Sample of usage: $var->aprovador = $object;
     * @param $object Instance of Aprovador
     */
    public function set_aprovador(Aprovador $object)
    {
        $this->aprovador = $object;
        $this->aprovador_id = $object->id;
    }

    /**
     * Method get_aprovador
     * Sample of usage: $var->aprovador->attribute;
     * @returns Aprovador instance
     */
    public function get_aprovador()
    {
    
        // loads the associated object
        if (empty($this->aprovador))
            $this->aprovador = new Aprovador($this->aprovador_id);
    
        // returns the associated object
        return $this->aprovador;
    }

    
}

