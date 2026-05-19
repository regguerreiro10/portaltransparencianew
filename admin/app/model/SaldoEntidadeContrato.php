<?php

//<fileHeader>
  
//</fileHeader>

class SaldoEntidadeContrato extends TRecord
{
    const TABLENAME  = 'saldo_entidade_contrato';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
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
        parent::addAttribute('entidade_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('tipotransacao');
        parent::addAttribute('datatransacao');
        parent::addAttribute('historico');
        parent::addAttribute('valor_saldo');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('dtinicio');
        parent::addAttribute('dtfinal');
        parent::addAttribute('ativo');
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
    
    public function get_dtinicio_br()
    {
        return TDate::date2br($this->dtinicio);
    }

    public function get_dtfinal_br()
    {
        return TDate::date2br($this->dtfinal);
    }

    public function get_valor_saldo_br()
    {
        return 'R$ ' . number_format($this->valor_saldo, 2, ',', '.');
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

