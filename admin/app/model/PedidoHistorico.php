<?php

class PedidoHistorico extends TRecord
{
    const TABLENAME  = 'pedido_historico';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $pedido_venda;
    private $estado_pedido_venda;
    private $aprovador;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pedido_venda_id');
        parent::addAttribute('estado_pedido_venda_id');
        parent::addAttribute('aprovador_id');
        parent::addAttribute('data_operacao');
        parent::addAttribute('obs');
            
    }

    /**
     * Method set_pedido
     * Sample of usage: $var->pedido = $object;
     * @param $object Instance of Pedido
     */
    public function set_pedido_venda(Pedido $object)
    {
        $this->pedido_venda = $object;
        $this->pedido_venda_id = $object->id;
    }

    /**
     * Method get_pedido_venda
     * Sample of usage: $var->pedido_venda->attribute;
     * @returns Pedido instance
     */
    public function get_pedido_venda()
    {
    
        // loads the associated object
        if (empty($this->pedido_venda))
            $this->pedido_venda = new Pedido($this->pedido_venda_id);
    
        // returns the associated object
        return $this->pedido_venda;
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

