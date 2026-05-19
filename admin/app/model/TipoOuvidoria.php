<?php

class TipoOuvidoria extends TRecord
{
    const TABLENAME  = 'tipo_ouvidoria';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cor');
            
    }

    /**
     * Method getOuvidorias
     */
    public function getOuvidorias()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_ouvidoria_id', '=', $this->id));
        return Ouvidoria::getObjects( $criteria );
    }

    public function set_ouvidoria_tipo_ouvidoria_to_string($ouvidoria_tipo_ouvidoria_to_string)
    {
        if(is_array($ouvidoria_tipo_ouvidoria_to_string))
        {
            $values = TipoOuvidoria::where('id', 'in', $ouvidoria_tipo_ouvidoria_to_string)->getIndexedArray('nome', 'nome');
            $this->ouvidoria_tipo_ouvidoria_to_string = implode(', ', $values);
        }
        else
        {
            $this->ouvidoria_tipo_ouvidoria_to_string = $ouvidoria_tipo_ouvidoria_to_string;
        }

        $this->vdata['ouvidoria_tipo_ouvidoria_to_string'] = $this->ouvidoria_tipo_ouvidoria_to_string;
    }

    public function get_ouvidoria_tipo_ouvidoria_to_string()
    {
        if(!empty($this->ouvidoria_tipo_ouvidoria_to_string))
        {
            return $this->ouvidoria_tipo_ouvidoria_to_string;
        }
    
        $values = Ouvidoria::where('tipo_ouvidoria_id', '=', $this->id)->getIndexedArray('tipo_ouvidoria_id','{tipo_ouvidoria->nome}');
        return implode(', ', $values);
    }

    public function set_ouvidoria_system_users_to_string($ouvidoria_system_users_to_string)
    {
        if(is_array($ouvidoria_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $ouvidoria_system_users_to_string)->getIndexedArray('name', 'name');
            $this->ouvidoria_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->ouvidoria_system_users_to_string = $ouvidoria_system_users_to_string;
        }

        $this->vdata['ouvidoria_system_users_to_string'] = $this->ouvidoria_system_users_to_string;
    }

    public function get_ouvidoria_system_users_to_string()
    {
        if(!empty($this->ouvidoria_system_users_to_string))
        {
            return $this->ouvidoria_system_users_to_string;
        }
    
        $values = Ouvidoria::where('tipo_ouvidoria_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_ouvidoria_departamento_unit_to_string($ouvidoria_departamento_unit_to_string)
    {
        if(is_array($ouvidoria_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $ouvidoria_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->ouvidoria_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->ouvidoria_departamento_unit_to_string = $ouvidoria_departamento_unit_to_string;
        }

        $this->vdata['ouvidoria_departamento_unit_to_string'] = $this->ouvidoria_departamento_unit_to_string;
    }

    public function get_ouvidoria_departamento_unit_to_string()
    {
        if(!empty($this->ouvidoria_departamento_unit_to_string))
        {
            return $this->ouvidoria_departamento_unit_to_string;
        }
    
        $values = Ouvidoria::where('tipo_ouvidoria_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    
}

