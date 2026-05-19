<?php

//<fileHeader>
  
//</fileHeader>

class PropostasHistorico extends TRecord
{
    const TABLENAME  = 'propostas_historico';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    
    private $propostas;
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
        parent::addAttribute('propostas_id');
        parent::addAttribute('estado_pedido_frotas_id');
        parent::addAttribute('aprovador_frotas_id');
        parent::addAttribute('data_historico');
        parent::addAttribute('obs');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_propostas
     * Sample of usage: $var->propostas = $object;
     * @param $object Instance of Propostas
     */
    public function set_propostas(Propostas $object)
    {
        $this->propostas = $object;
        $this->propostas_id = $object->id;
    }
    
    /**
     * Method get_propostas
     * Sample of usage: $var->propostas->attribute;
     * @returns Propostas instance
     */
    public function get_propostas()
    {
        
        // loads the associated object
        if (empty($this->propostas))
            $this->propostas = new Propostas($this->propostas_id);
        
        // returns the associated object
        return $this->propostas;
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

