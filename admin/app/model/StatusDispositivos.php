<?php

class StatusDispositivos extends TRecord
{
    const TABLENAME  = 'status_dispositivos';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('cor');
        parent::addAttribute('ordem');
        parent::addAttribute('mensagem');
            
    }

    /**
     * Method getDispositivosSolicitadoss
     */
    public function getDispositivosSolicitadoss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('status_dispositivos_id', '=', $this->id));
        return DispositivosSolicitados::getObjects( $criteria );
    }

    public function set_dispositivos_solicitados_dispositivos_to_string($dispositivos_solicitados_dispositivos_to_string)
    {
        if(is_array($dispositivos_solicitados_dispositivos_to_string))
        {
            $values = Dispositivos::where('id', 'in', $dispositivos_solicitados_dispositivos_to_string)->getIndexedArray('id', 'id');
            $this->dispositivos_solicitados_dispositivos_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_dispositivos_to_string = $dispositivos_solicitados_dispositivos_to_string;
        }

        $this->vdata['dispositivos_solicitados_dispositivos_to_string'] = $this->dispositivos_solicitados_dispositivos_to_string;
    }

    public function get_dispositivos_solicitados_dispositivos_to_string()
    {
        if(!empty($this->dispositivos_solicitados_dispositivos_to_string))
        {
            return $this->dispositivos_solicitados_dispositivos_to_string;
        }
    
        $values = DispositivosSolicitados::where('status_dispositivos_id', '=', $this->id)->getIndexedArray('dispositivos_id','{dispositivos->id}');
        return implode(', ', $values);
    }

    public function set_dispositivos_solicitados_veiculos_to_string($dispositivos_solicitados_veiculos_to_string)
    {
        if(is_array($dispositivos_solicitados_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $dispositivos_solicitados_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->dispositivos_solicitados_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_veiculos_to_string = $dispositivos_solicitados_veiculos_to_string;
        }

        $this->vdata['dispositivos_solicitados_veiculos_to_string'] = $this->dispositivos_solicitados_veiculos_to_string;
    }

    public function get_dispositivos_solicitados_veiculos_to_string()
    {
        if(!empty($this->dispositivos_solicitados_veiculos_to_string))
        {
            return $this->dispositivos_solicitados_veiculos_to_string;
        }
    
        $values = DispositivosSolicitados::where('status_dispositivos_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    public function set_dispositivos_solicitados_status_dispositivos_to_string($dispositivos_solicitados_status_dispositivos_to_string)
    {
        if(is_array($dispositivos_solicitados_status_dispositivos_to_string))
        {
            $values = StatusDispositivos::where('id', 'in', $dispositivos_solicitados_status_dispositivos_to_string)->getIndexedArray('id', 'id');
            $this->dispositivos_solicitados_status_dispositivos_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_status_dispositivos_to_string = $dispositivos_solicitados_status_dispositivos_to_string;
        }

        $this->vdata['dispositivos_solicitados_status_dispositivos_to_string'] = $this->dispositivos_solicitados_status_dispositivos_to_string;
    }

    public function get_dispositivos_solicitados_status_dispositivos_to_string()
    {
        if(!empty($this->dispositivos_solicitados_status_dispositivos_to_string))
        {
            return $this->dispositivos_solicitados_status_dispositivos_to_string;
        }
    
        $values = DispositivosSolicitados::where('status_dispositivos_id', '=', $this->id)->getIndexedArray('status_dispositivos_id','{status_dispositivos->id}');
        return implode(', ', $values);
    }

    public function set_dispositivos_solicitados_system_unit_to_string($dispositivos_solicitados_system_unit_to_string)
    {
        if(is_array($dispositivos_solicitados_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $dispositivos_solicitados_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->dispositivos_solicitados_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_system_unit_to_string = $dispositivos_solicitados_system_unit_to_string;
        }

        $this->vdata['dispositivos_solicitados_system_unit_to_string'] = $this->dispositivos_solicitados_system_unit_to_string;
    }

    public function get_dispositivos_solicitados_system_unit_to_string()
    {
        if(!empty($this->dispositivos_solicitados_system_unit_to_string))
        {
            return $this->dispositivos_solicitados_system_unit_to_string;
        }
    
        $values = DispositivosSolicitados::where('status_dispositivos_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    public function set_dispositivos_solicitados_departamento_unit_to_string($dispositivos_solicitados_departamento_unit_to_string)
    {
        if(is_array($dispositivos_solicitados_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $dispositivos_solicitados_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->dispositivos_solicitados_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_departamento_unit_to_string = $dispositivos_solicitados_departamento_unit_to_string;
        }

        $this->vdata['dispositivos_solicitados_departamento_unit_to_string'] = $this->dispositivos_solicitados_departamento_unit_to_string;
    }

    public function get_dispositivos_solicitados_departamento_unit_to_string()
    {
        if(!empty($this->dispositivos_solicitados_departamento_unit_to_string))
        {
            return $this->dispositivos_solicitados_departamento_unit_to_string;
        }
    
        $values = DispositivosSolicitados::where('status_dispositivos_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    public function set_dispositivos_solicitados_system_users_to_string($dispositivos_solicitados_system_users_to_string)
    {
        if(is_array($dispositivos_solicitados_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $dispositivos_solicitados_system_users_to_string)->getIndexedArray('name', 'name');
            $this->dispositivos_solicitados_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_system_users_to_string = $dispositivos_solicitados_system_users_to_string;
        }

        $this->vdata['dispositivos_solicitados_system_users_to_string'] = $this->dispositivos_solicitados_system_users_to_string;
    }

    public function get_dispositivos_solicitados_system_users_to_string()
    {
        if(!empty($this->dispositivos_solicitados_system_users_to_string))
        {
            return $this->dispositivos_solicitados_system_users_to_string;
        }
    
        $values = DispositivosSolicitados::where('status_dispositivos_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    
}

