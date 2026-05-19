<?php

//<fileHeader>
  
//</fileHeader>

class ComentarioProposta extends TRecord
{
    const TABLENAME  = 'comentario_proposta';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private $propostas;
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
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('comentario');
        parent::addAttribute('propostas_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('leitura_dt');

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

