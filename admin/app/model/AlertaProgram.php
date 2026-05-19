<?php

//<fileHeader>
  
//</fileHeader>

class AlertaProgram extends TRecord
{
    const TABLENAME  = 'alerta_program';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private SystemProgram $system_program;
    private SystemUnit $system_unit;
    private Entidade $entidade;
    private SystemUsers $system_users;
    
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
        parent::addAttribute('system_program_id');
        parent::addAttribute('mensagem');
        parent::addAttribute('ativo');
        parent::addAttribute('system_users_id');
        parent::addAttribute('entidade_id');
        parent::addAttribute('system_unit_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_system_program
     * Sample of usage: $var->system_program = $object;
     * @param $object Instance of SystemProgram
     */
    public function set_system_program(SystemProgram $object)
    {
        $this->system_program = $object;
        $this->system_program_id = $object->id;
    }
    
    /**
     * Method get_system_program
     * Sample of usage: $var->system_program->attribute;
     * @returns SystemProgram instance
     */
    public function get_system_program()
    {
        
        // loads the associated object
        if (empty($this->system_program))
            $this->system_program = new SystemProgram($this->system_program_id);
        
        // returns the associated object
        return $this->system_program;
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

