<?php

class Ouvidoria extends TRecord
{
    const TABLENAME  = 'ouvidoria';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private $tipo_ouvidoria;
    private $system_users;
    private $departamento_unit;

    private $system_unit;  // <— adicione
    private $pessoa;       // <— adicione

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_ouvidoria_id');
        parent::addAttribute('nome');
        parent::addAttribute('telefone');
        parent::addAttribute('email');
        parent::addAttribute('mensagem');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('system_users_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('pessoa_id');
            
    }

    /**
     * Method set_tipo_ouvidoria
     * Sample of usage: $var->tipo_ouvidoria = $object;
     * @param $object Instance of TipoOuvidoria
     */
    public function set_tipo_ouvidoria(TipoOuvidoria $object)
    {
        $this->tipo_ouvidoria = $object;
        $this->tipo_ouvidoria_id = $object->id;
    }

    /**
     * Method get_tipo_ouvidoria
     * Sample of usage: $var->tipo_ouvidoria->attribute;
     * @returns TipoOuvidoria instance
     */
    public function get_tipo_ouvidoria()
    {
    
        // loads the associated object
        if (empty($this->tipo_ouvidoria))
            $this->tipo_ouvidoria = new TipoOuvidoria($this->tipo_ouvidoria_id);
    
        // returns the associated object
        return $this->tipo_ouvidoria;
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

    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }

    /**
     * Method get_departamento_unit
     * Sample of usage: $var->departamento_unit->attribute;
     * @returns DepartamentoUnit instance
     */
    public function get_system_unit()
    {
    
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
        
        // returns the associated object
        return $this->system_unit;
    }

    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;

    }

    public function get_pessoa()
    {
        // loads the associated object
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->pessoa_id);

        return $this->pessoa;
    }
    
}

