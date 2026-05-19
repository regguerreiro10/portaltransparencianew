<?php

class TaxasPessoa extends TRecord
{
    const TABLENAME  = 'taxas_pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private SystemUnit $system_unit;
    private SystemUsers $system_users;
    private Entidade $entidade;
    private Pessoa $pessoa;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pessoa_id');
        parent::addAttribute('entidade_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('taxaadm');
        parent::addAttribute('deleted_at');
        parent::addAttribute('taxabancaria');
        parent::addAttribute('taxaantecipacao');
        parent::addAttribute('taxacontrato');
        parent::addAttribute('taxadesconto');
        parent::addAttribute('optante');
        parent::addAttribute('ir');
        parent::addAttribute('csll');
        parent::addAttribute('cofins');
        parent::addAttribute('pis');
        parent::addAttribute('ir_servico');
        parent::addAttribute('csll_servico');
        parent::addAttribute('cofins_servico');
        parent::addAttribute('pis_servico');
        parent::addAttribute('iss_servico');
            
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

    
}

