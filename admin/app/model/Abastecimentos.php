<?php

//<fileHeader>
  
//</fileHeader>

class Abastecimentos extends TRecord
{
    const TABLENAME  = 'abastecimentos';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    
    private $estabelecimento;
    private $condutor;
    private $veiculos;
    private $system_unit;
    private $departamento_unit;
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
        parent::addAttribute('estabelecimento_id');
        parent::addAttribute('condutor_id');
        parent::addAttribute('data_abastecimento');
        parent::addAttribute('numero_cartao');
        parent::addAttribute('placa');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('modelo');
        parent::addAttribute('tipo_combustivel_id');
        parent::addAttribute('qtde');
        parent::addAttribute('valor_unitario');
        parent::addAttribute('valor_total');
        parent::addAttribute('valor_desconto');
        parent::addAttribute('valor_liquido');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
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
    /**
     * Method set_condutor
     * Sample of usage: $var->condutor = $object;
     * @param $object Instance of Condutor
     */
    public function set_condutor(Condutor $object)
    {
        $this->condutor = $object;
        $this->condutor_id = $object->id;
    }
    
    /**
     * Method get_condutor
     * Sample of usage: $var->condutor->attribute;
     * @returns Condutor instance
     */
    public function get_condutor()
    {
        
        // loads the associated object
        if (empty($this->condutor))
            $this->condutor = new Condutor($this->condutor_id);
        
        // returns the associated object
        return $this->condutor;
    }
    /**
     * Method set_veiculos
     * Sample of usage: $var->veiculos = $object;
     * @param $object Instance of Veiculos
     */
    public function set_veiculos(Veiculos $object)
    {
        $this->veiculos = $object;
        $this->veiculos_id = $object->id;
    }
    
    /**
     * Method get_veiculos
     * Sample of usage: $var->veiculos->attribute;
     * @returns Veiculos instance
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


