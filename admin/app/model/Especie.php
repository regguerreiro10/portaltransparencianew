<?php

class Especie extends TRecord
{
    const TABLENAME  = 'especie';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
            
    }

    /**
     * Method getVeiculoss
     */
    public function getVeiculoss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('especie_id', '=', $this->id));
        return Veiculos::getObjects( $criteria );
    }

    public function set_veiculos_dispositivos_to_string($veiculos_dispositivos_to_string)
    {
        if(is_array($veiculos_dispositivos_to_string))
        {
            $values = Dispositivos::where('id', 'in', $veiculos_dispositivos_to_string)->getIndexedArray('id', 'id');
            $this->veiculos_dispositivos_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_dispositivos_to_string = $veiculos_dispositivos_to_string;
        }

        $this->vdata['veiculos_dispositivos_to_string'] = $this->veiculos_dispositivos_to_string;
    }

    public function get_veiculos_dispositivos_to_string()
    {
        if(!empty($this->veiculos_dispositivos_to_string))
        {
            return $this->veiculos_dispositivos_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('dispositivos_id','{dispositivos->id}');
        return implode(', ', $values);
    }

    public function set_veiculos_marca_to_string($veiculos_marca_to_string)
    {
        if(is_array($veiculos_marca_to_string))
        {
            $values = Marca::where('id', 'in', $veiculos_marca_to_string)->getIndexedArray('id', 'id');
            $this->veiculos_marca_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_marca_to_string = $veiculos_marca_to_string;
        }

        $this->vdata['veiculos_marca_to_string'] = $this->veiculos_marca_to_string;
    }

    public function get_veiculos_marca_to_string()
    {
        if(!empty($this->veiculos_marca_to_string))
        {
            return $this->veiculos_marca_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('marca_id','{marca->id}');
        return implode(', ', $values);
    }

    public function set_veiculos_modelo_to_string($veiculos_modelo_to_string)
    {
        if(is_array($veiculos_modelo_to_string))
        {
            $values = Modelo::where('id', 'in', $veiculos_modelo_to_string)->getIndexedArray('id', 'id');
            $this->veiculos_modelo_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_modelo_to_string = $veiculos_modelo_to_string;
        }

        $this->vdata['veiculos_modelo_to_string'] = $this->veiculos_modelo_to_string;
    }

    public function get_veiculos_modelo_to_string()
    {
        if(!empty($this->veiculos_modelo_to_string))
        {
            return $this->veiculos_modelo_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('modelo_id','{modelo->id}');
        return implode(', ', $values);
    }

    public function set_veiculos_tipo_veiculo_to_string($veiculos_tipo_veiculo_to_string)
    {
        if(is_array($veiculos_tipo_veiculo_to_string))
        {
            $values = TipoVeiculo::where('id', 'in', $veiculos_tipo_veiculo_to_string)->getIndexedArray('id', 'id');
            $this->veiculos_tipo_veiculo_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_tipo_veiculo_to_string = $veiculos_tipo_veiculo_to_string;
        }

        $this->vdata['veiculos_tipo_veiculo_to_string'] = $this->veiculos_tipo_veiculo_to_string;
    }

    public function get_veiculos_tipo_veiculo_to_string()
    {
        if(!empty($this->veiculos_tipo_veiculo_to_string))
        {
            return $this->veiculos_tipo_veiculo_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('tipo_veiculo_id','{tipo_veiculo->id}');
        return implode(', ', $values);
    }

    public function set_veiculos_system_unit_to_string($veiculos_system_unit_to_string)
    {
        if(is_array($veiculos_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $veiculos_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->veiculos_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_system_unit_to_string = $veiculos_system_unit_to_string;
        }

        $this->vdata['veiculos_system_unit_to_string'] = $this->veiculos_system_unit_to_string;
    }

    public function get_veiculos_system_unit_to_string()
    {
        if(!empty($this->veiculos_system_unit_to_string))
        {
            return $this->veiculos_system_unit_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    public function set_veiculos_departamento_unit_to_string($veiculos_departamento_unit_to_string)
    {
        if(is_array($veiculos_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $veiculos_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->veiculos_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_departamento_unit_to_string = $veiculos_departamento_unit_to_string;
        }

        $this->vdata['veiculos_departamento_unit_to_string'] = $this->veiculos_departamento_unit_to_string;
    }

    public function get_veiculos_departamento_unit_to_string()
    {
        if(!empty($this->veiculos_departamento_unit_to_string))
        {
            return $this->veiculos_departamento_unit_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    public function set_veiculos_system_users_to_string($veiculos_system_users_to_string)
    {
        if(is_array($veiculos_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $veiculos_system_users_to_string)->getIndexedArray('name', 'name');
            $this->veiculos_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_system_users_to_string = $veiculos_system_users_to_string;
        }

        $this->vdata['veiculos_system_users_to_string'] = $this->veiculos_system_users_to_string;
    }

    public function get_veiculos_system_users_to_string()
    {
        if(!empty($this->veiculos_system_users_to_string))
        {
            return $this->veiculos_system_users_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_veiculos_tipo_combustivel_to_string($veiculos_tipo_combustivel_to_string)
    {
        if(is_array($veiculos_tipo_combustivel_to_string))
        {
            $values = TipoCombustivel::where('id', 'in', $veiculos_tipo_combustivel_to_string)->getIndexedArray('id', 'id');
            $this->veiculos_tipo_combustivel_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_tipo_combustivel_to_string = $veiculos_tipo_combustivel_to_string;
        }

        $this->vdata['veiculos_tipo_combustivel_to_string'] = $this->veiculos_tipo_combustivel_to_string;
    }

    public function get_veiculos_tipo_combustivel_to_string()
    {
        if(!empty($this->veiculos_tipo_combustivel_to_string))
        {
            return $this->veiculos_tipo_combustivel_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('tipo_combustivel_id','{tipo_combustivel->id}');
        return implode(', ', $values);
    }

    public function set_veiculos_propriedade_to_string($veiculos_propriedade_to_string)
    {
        if(is_array($veiculos_propriedade_to_string))
        {
            $values = Propriedade::where('id', 'in', $veiculos_propriedade_to_string)->getIndexedArray('id', 'id');
            $this->veiculos_propriedade_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_propriedade_to_string = $veiculos_propriedade_to_string;
        }

        $this->vdata['veiculos_propriedade_to_string'] = $this->veiculos_propriedade_to_string;
    }

    public function get_veiculos_propriedade_to_string()
    {
        if(!empty($this->veiculos_propriedade_to_string))
        {
            return $this->veiculos_propriedade_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('propriedade_id','{propriedade->id}');
        return implode(', ', $values);
    }

    public function set_veiculos_status_veiculo_to_string($veiculos_status_veiculo_to_string)
    {
        if(is_array($veiculos_status_veiculo_to_string))
        {
            $values = StatusVeiculo::where('id', 'in', $veiculos_status_veiculo_to_string)->getIndexedArray('id', 'id');
            $this->veiculos_status_veiculo_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_status_veiculo_to_string = $veiculos_status_veiculo_to_string;
        }

        $this->vdata['veiculos_status_veiculo_to_string'] = $this->veiculos_status_veiculo_to_string;
    }

    public function get_veiculos_status_veiculo_to_string()
    {
        if(!empty($this->veiculos_status_veiculo_to_string))
        {
            return $this->veiculos_status_veiculo_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('status_veiculo_id','{status_veiculo->id}');
        return implode(', ', $values);
    }

    public function set_veiculos_corveiculo_to_string($veiculos_corveiculo_to_string)
    {
        if(is_array($veiculos_corveiculo_to_string))
        {
            $values = Corveiculo::where('id', 'in', $veiculos_corveiculo_to_string)->getIndexedArray('id', 'id');
            $this->veiculos_corveiculo_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_corveiculo_to_string = $veiculos_corveiculo_to_string;
        }

        $this->vdata['veiculos_corveiculo_to_string'] = $this->veiculos_corveiculo_to_string;
    }

    public function get_veiculos_corveiculo_to_string()
    {
        if(!empty($this->veiculos_corveiculo_to_string))
        {
            return $this->veiculos_corveiculo_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('corveiculo_id','{corveiculo->id}');
        return implode(', ', $values);
    }

    public function set_veiculos_especie_to_string($veiculos_especie_to_string)
    {
        if(is_array($veiculos_especie_to_string))
        {
            $values = Especie::where('id', 'in', $veiculos_especie_to_string)->getIndexedArray('id', 'id');
            $this->veiculos_especie_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_especie_to_string = $veiculos_especie_to_string;
        }

        $this->vdata['veiculos_especie_to_string'] = $this->veiculos_especie_to_string;
    }

    public function get_veiculos_especie_to_string()
    {
        if(!empty($this->veiculos_especie_to_string))
        {
            return $this->veiculos_especie_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('especie_id','{especie->id}');
        return implode(', ', $values);
    }

    public function set_veiculos_familia_to_string($veiculos_familia_to_string)
    {
        if(is_array($veiculos_familia_to_string))
        {
            $values = Familia::where('id', 'in', $veiculos_familia_to_string)->getIndexedArray('id', 'id');
            $this->veiculos_familia_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_familia_to_string = $veiculos_familia_to_string;
        }

        $this->vdata['veiculos_familia_to_string'] = $this->veiculos_familia_to_string;
    }

    public function get_veiculos_familia_to_string()
    {
        if(!empty($this->veiculos_familia_to_string))
        {
            return $this->veiculos_familia_to_string;
        }
    
        $values = Veiculos::where('especie_id', '=', $this->id)->getIndexedArray('familia_id','{familia->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(Veiculos::where('especie_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

