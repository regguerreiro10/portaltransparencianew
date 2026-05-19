<?php

//<fileHeader>
  
//</fileHeader>

class PedidocompraAsCliente extends TRecord
{
    const TABLENAME  = 'pedidocompra_as_cliente';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private Pessoa $pessoa;
    private Pedido $pedido;
    
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
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('pedido_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;
    }
    
    /**
     * Method get_pessoa
     * Sample of usage: $var->pessoa->attribute;
     * @returns Pessoa instance
     */
    public function get_pessoa()
    {
        
        // loads the associated object
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->pessoa_id);
        
        // returns the associated object
        return $this->pessoa;
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
    


    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

