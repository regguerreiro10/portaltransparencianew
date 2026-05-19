<?php

class Condutor extends TRecord
{
    const TABLENAME  = 'condutor';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; 

    private $system_users;
    private $system_unit;

    public function __construct($id = NULL, $callObjectLoad = TRUE) {
        parent::addAttribute('nome');
        parent::addAttribute('cpf');
        parent::addAttribute('celular');
        parent::addAttribute('cnh');
        parent::addAttribute('categoria');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('numero_dispositivo');
        parent::addAttribute('codigo_patrimonio');

    }

    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    public function get_system_unit()
    {
        
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
        
        return $this->system_unit;
    }

    public function set_system_user(SystemUsers $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    public function get_system_user()
    {
        if (empty($this->system_user))
            $this->system_user = new SystemUsers($this->system_user_id);
        
        return $this->system_user;
    }

    public function set_departamento_unit(DepartamentoUnit $object)
    {
        $this->departamento_unit = $object;
        $this->departamento_unit_id = $object->id;
    }

    public function get_departamento_unit()
    {
        
        if (empty($this->departamento_unit))
            $this->departamento_unit = new DepartamentoUnit($this->departamento_unit_id);
        
        return $this->departamento_unit;
    }
}