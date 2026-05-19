<?php

class PedidoSeguimento extends TRecord
{
    const TABLENAME  = 'pedido_seguimento';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $pedido;
    private $seguimento;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pedido_id');
        parent::addAttribute('seguimento_id');
            
    }

    /**
     * Method set_pedido
     * Sample of usage: $var->pedido = $object;
     * @param $object Instance of Pedido
     */
    public function set_pedido(Pedido $object)
    {
        $this->pedido = $object;
        $this->pedido_id = $object->id;
    }

    /**
     * Method get_pedido
     * Sample of usage: $var->pedido->attribute;
     * @returns Pedido instance
     */
    public function get_pedido()
    {
    
        // loads the associated object
        if (empty($this->pedido))
            $this->pedido = new Pedido($this->pedido_id);
    
        // returns the associated object
        return $this->pedido;
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

    
}

