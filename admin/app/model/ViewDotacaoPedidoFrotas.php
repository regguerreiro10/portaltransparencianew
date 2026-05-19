<?php

//<fileHeader>
  
//</fileHeader>

class ViewDotacaoPedidoFrotas extends TRecord
{
    const TABLENAME  = 'view_dotacao_pedido_frotas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}
    

    private $veiculos;
    private $departamento_unit;
    private $estabelecimento;
    private $estado_pedido_frotas;
    private $system_users;

    
    
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
        parent::addAttribute('saldo_departamento_id');
        parent::addAttribute('pedido_frotas_id');
        parent::addAttribute('descricaopedido');
        parent::addAttribute('estabelecimento_id');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('km');
        parent::addAttribute('valor');
        parent::addAttribute('saldo_atual');
        parent::addAttribute('valor_liquido_proposta');
        parent::addAttribute('dt_pedido');
        parent::addAttribute('dt_finalizacao');
        parent::addAttribute('estado_pedido_frotas_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('cidade_id');
        parent::addAttribute('departamento_unit_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    
 /**
     * Method set_niveltanque
     * Sample of usage: $var->niveltanque = $object;
     * @param $object Instance of Niveltanque
     */
    public function set_veiculos(Veiculos $object)
    {
        $this->veiculos = $object;
        $this->veiculos_id = $object->id;
    }
    
    /**
     * Method get_niveltanque
     * Sample of usage: $var->niveltanque->attribute;
     * @returns Niveltanque instance
     */
    public function get_veiculos()
    {
        
        // loads the associated object
        if (empty($this->veiculos))
            $this->veiculos = new Veiculos($this->veiculos_id);
        
        // returns the associated object
        return $this->veiculos;
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
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_estabelecimento(Pessoa $object)
    {
        $this->estabelecimento = $object;
        $this->estabelecimento_id = $object->id;
    }
    
    /**
     * Method get_estabelecimento
     * Sample of usage: $var->estabelecimento->attribute;
     * @returns Pessoa instance
     */
    public function get_estabelecimento()
    {
        
        // loads the associated object
        if (empty($this->estabelecimento))
            $this->estabelecimento = new Pessoa($this->estabelecimento_id);
        
        // returns the associated object
        return $this->estabelecimento;
    }
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
     * Method set_system_users
     * Sample of usage: $var->system_users = $object;
     * @param $object Instance of SystemUsers
     */
    public function set_system_users(SystemUsers $object)
    {
        $this->system_users = $object;
        $this->system_users_id = $object->id;
    }
    
    /**
     * Method get_system_users
     * Sample of usage: $var->system_users->attribute;
     * @returns SystemUsers instance
     */
    public function get_system_users()
    {
        
        // loads the associated object
        if (empty($this->system_users))
            $this->system_users = new SystemUsers($this->system_users_id);
        
        // returns the associated object
        return $this->system_users;
    }
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

