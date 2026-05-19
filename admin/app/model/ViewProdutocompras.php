<?php

//<fileHeader>
  
//</fileHeader>

class ViewProdutocompras extends TRecord
{
    const TABLENAME  = 'view_produtocompras';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}
    

    
    
    
    //<classProperties>
  /**
     * Method set_system_unit
     * Sample of usage: $var->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $var->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
        
        // returns the associated object
        return $this->system_unit;
    }
    /**
     * Method set_departamento_unit
     * Sample of usage: $var->departamento_unit = $object;
     * @param $object Instance of DepartamentoUnit
     */
    public function set_departamento_unit(DepartamentoUnit $object)
    {
        $this->departamento_unit = $object;
        $this->departamento_unit_id = $object->id;
    }
    
    /**
     * Method get_departamento_unit
     * Sample of usage: $var->departamento_unit->attribute;
     * @returns DepartamentoUnit instance
     */
    public function get_departamento_unit()
    {
        
        // loads the associated object
        if (empty($this->departamento_unit))
            $this->departamento_unit = new DepartamentoUnit($this->departamento_unit_id);
        
        // returns the associated object
        return $this->departamento_unit;
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
    //</classProperties>
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        //<onBeforeConstruct>
  
        //</onBeforeConstruct>
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pedido_id');
        parent::addAttribute('estado_pedido_venda_id');
        parent::addAttribute('data_cotacao');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('entidade_id');
        parent::addAttribute('qtde');
        parent::addAttribute('valor_unitario');
        parent::addAttribute('valor_total');
        parent::addAttribute('nomeproduto');
        parent::addAttribute('nomeestabelecimento');
        parent::addAttribute('dt_pedido');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    


    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

