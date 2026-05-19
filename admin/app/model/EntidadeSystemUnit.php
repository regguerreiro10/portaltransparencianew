<?php

//<fileHeader>
  
//</fileHeader>

class EntidadeSystemUnit extends TRecord
{
    const TABLENAME  = 'entidade_system_unit';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private Entidade $entidade;
    private SystemUnit $system_unit;
    
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
        parent::addAttribute('entidade_id');
        parent::addAttribute('system_unit_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_entidade
     * Sample of usage: $var->entidade = $object;
     * @param $object Instance of Entidade
     */
    public function set_entidade(Entidade $object)
    {
        $this->entidade = $object;
        $this->entidade_id = $object->id;
    }
    
    /**
     * Method get_entidade
     * Sample of usage: $var->entidade->attribute;
     * @returns Entidade instance
     */
    public function get_entidade()
    {
        
        // loads the associated object
        if (empty($this->entidade))
            $this->entidade = new Entidade($this->entidade_id);
        
        // returns the associated object
        return $this->entidade;
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
    


    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

