<?php

class DocumentosPedido extends TRecord
{
    const TABLENAME  = 'documentos_pedido';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $pedido;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('caminho');
        parent::addAttribute('pedido_id');
            
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

    
}

