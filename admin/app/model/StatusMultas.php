<?php

class StatusMultas extends TRecord
{
    const TABLENAME  = 'status_multas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('cor');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method getMultass
     */
    public function getMultass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('status_multas_id', '=', $this->id));
        return Multas::getObjects( $criteria );
    }

    public function set_multas_veiculos_to_string($multas_veiculos_to_string)
    {
        if(is_array($multas_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $multas_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->multas_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->multas_veiculos_to_string = $multas_veiculos_to_string;
        }

        $this->vdata['multas_veiculos_to_string'] = $this->multas_veiculos_to_string;
    }

    public function get_multas_veiculos_to_string()
    {
        if(!empty($this->multas_veiculos_to_string))
        {
            return $this->multas_veiculos_to_string;
        }
    
        $values = Multas::where('status_multas_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    public function set_multas_condutor_to_string($multas_condutor_to_string)
    {
        if(is_array($multas_condutor_to_string))
        {
            $values = Pessoa::where('id', 'in', $multas_condutor_to_string)->getIndexedArray('nome', 'nome');
            $this->multas_condutor_to_string = implode(', ', $values);
        }
        else
        {
            $this->multas_condutor_to_string = $multas_condutor_to_string;
        }

        $this->vdata['multas_condutor_to_string'] = $this->multas_condutor_to_string;
    }

    public function get_multas_condutor_to_string()
    {
        if(!empty($this->multas_condutor_to_string))
        {
            return $this->multas_condutor_to_string;
        }
    
        $values = Multas::where('status_multas_id', '=', $this->id)->getIndexedArray('condutor_id','{condutor->nome}');
        return implode(', ', $values);
    }

    public function set_multas_system_unit_to_string($multas_system_unit_to_string)
    {
        if(is_array($multas_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $multas_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->multas_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->multas_system_unit_to_string = $multas_system_unit_to_string;
        }

        $this->vdata['multas_system_unit_to_string'] = $this->multas_system_unit_to_string;
    }

    public function get_multas_system_unit_to_string()
    {
        if(!empty($this->multas_system_unit_to_string))
        {
            return $this->multas_system_unit_to_string;
        }
    
        $values = Multas::where('status_multas_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    public function set_multas_departamento_unit_to_string($multas_departamento_unit_to_string)
    {
        if(is_array($multas_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $multas_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->multas_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->multas_departamento_unit_to_string = $multas_departamento_unit_to_string;
        }

        $this->vdata['multas_departamento_unit_to_string'] = $this->multas_departamento_unit_to_string;
    }

    public function get_multas_departamento_unit_to_string()
    {
        if(!empty($this->multas_departamento_unit_to_string))
        {
            return $this->multas_departamento_unit_to_string;
        }
    
        $values = Multas::where('status_multas_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    public function set_multas_system_users_to_string($multas_system_users_to_string)
    {
        if(is_array($multas_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $multas_system_users_to_string)->getIndexedArray('name', 'name');
            $this->multas_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->multas_system_users_to_string = $multas_system_users_to_string;
        }

        $this->vdata['multas_system_users_to_string'] = $this->multas_system_users_to_string;
    }

    public function get_multas_system_users_to_string()
    {
        if(!empty($this->multas_system_users_to_string))
        {
            return $this->multas_system_users_to_string;
        }
    
        $values = Multas::where('status_multas_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_multas_status_multas_to_string($multas_status_multas_to_string)
    {
        if(is_array($multas_status_multas_to_string))
        {
            $values = StatusMultas::where('id', 'in', $multas_status_multas_to_string)->getIndexedArray('id', 'id');
            $this->multas_status_multas_to_string = implode(', ', $values);
        }
        else
        {
            $this->multas_status_multas_to_string = $multas_status_multas_to_string;
        }

        $this->vdata['multas_status_multas_to_string'] = $this->multas_status_multas_to_string;
    }

    public function get_multas_status_multas_to_string()
    {
        if(!empty($this->multas_status_multas_to_string))
        {
            return $this->multas_status_multas_to_string;
        }
    
        $values = Multas::where('status_multas_id', '=', $this->id)->getIndexedArray('status_multas_id','{status_multas->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(Multas::where('status_multas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

