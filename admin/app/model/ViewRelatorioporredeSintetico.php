<?php

//<fileHeader>
  
//</fileHeader>

class ViewRelatorioporredeSintetico extends TRecord
{
    const TABLENAME  = 'view_relatorioporrede_sintetico';
    const PRIMARYKEY = 'proposta_id';
    const IDPOLICY   =  'max'; // {max, serial}
    

    
    
    
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
        parent::addAttribute('pedido_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('qtd_proposta_recebida');
        parent::addAttribute('dt_abertura');
        parent::addAttribute('qtd_proposta_finalizado');
        parent::addAttribute('dt_finalizado');
        parent::addAttribute('dt_aprovado');
        parent::addAttribute('qtd_proposta_entregue');
        parent::addAttribute('qtd_proposta_aguardando');
        parent::addAttribute('vl_produto');
        parent::addAttribute('vl_servico');
        parent::addAttribute('vl_total');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
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

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

