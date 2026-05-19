<?php

class TipoCombustivel extends TRecord
{
    const TABLENAME  = 'tipo_combustivel';
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
        $criteria->add(new TFilter('tipo_combustivel_id', '=', $this->id));
        return Veiculos::getObjects( $criteria );
    }
    /**
     * Method getAbastecimentoss
     */
    public function getAbastecimentoss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_combustivel_id', '=', $this->id));
        return Abastecimentos::getObjects( $criteria );
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
    
        $values = Veiculos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('dispositivos_id','{dispositivos->id}');
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
    
        $values = Veiculos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('marca_id','{marca->id}');
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
    
        $values = Veiculos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('modelo_id','{modelo->id}');
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
    
        $values = Veiculos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('tipo_combustivel_id','{tipo_combustivel->id}');
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
    
        $values = Veiculos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('corveiculo_id','{corveiculo->id}');
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
    
        $values = Veiculos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('propriedade_id','{propriedade->id}');
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
    
        $values = Veiculos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('tipo_veiculo_id','{tipo_veiculo->id}');
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
    
        $values = Veiculos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    public function set_veiculos_subsystem_unit_to_string($veiculos_subsystem_unit_to_string)
    {
        if(is_array($veiculos_subsystem_unit_to_string))
        {
            $values = SubsystemUnit::where('id', 'in', $veiculos_subsystem_unit_to_string)->getIndexedArray('name', 'name');
            $this->veiculos_subsystem_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->veiculos_subsystem_unit_to_string = $veiculos_subsystem_unit_to_string;
        }

        $this->vdata['veiculos_subsystem_unit_to_string'] = $this->veiculos_subsystem_unit_to_string;
    }

    public function get_veiculos_subsystem_unit_to_string()
    {
        if(!empty($this->veiculos_subsystem_unit_to_string))
        {
            return $this->veiculos_subsystem_unit_to_string;
        }
    
        $values = Veiculos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('subsystem_unit_id','{subsystem_unit->name}');
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
    
        $values = Veiculos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_abastecimentos_estabelecimento_to_string($abastecimentos_estabelecimento_to_string)
    {
        if(is_array($abastecimentos_estabelecimento_to_string))
        {
            $values = Estabelecimento::where('id', 'in', $abastecimentos_estabelecimento_to_string)->getIndexedArray('nome', 'nome');
            $this->abastecimentos_estabelecimento_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_estabelecimento_to_string = $abastecimentos_estabelecimento_to_string;
        }

        $this->vdata['abastecimentos_estabelecimento_to_string'] = $this->abastecimentos_estabelecimento_to_string;
    }

    public function get_abastecimentos_estabelecimento_to_string()
    {
        if(!empty($this->abastecimentos_estabelecimento_to_string))
        {
            return $this->abastecimentos_estabelecimento_to_string;
        }
    
        $values = Abastecimentos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('estabelecimento_id','{estabelecimento->nome}');
        return implode(', ', $values);
    }

    public function set_abastecimentos_condutor_to_string($abastecimentos_condutor_to_string)
    {
        if(is_array($abastecimentos_condutor_to_string))
        {
            $values = Condutor::where('id', 'in', $abastecimentos_condutor_to_string)->getIndexedArray('id', 'id');
            $this->abastecimentos_condutor_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_condutor_to_string = $abastecimentos_condutor_to_string;
        }

        $this->vdata['abastecimentos_condutor_to_string'] = $this->abastecimentos_condutor_to_string;
    }

    public function get_abastecimentos_condutor_to_string()
    {
        if(!empty($this->abastecimentos_condutor_to_string))
        {
            return $this->abastecimentos_condutor_to_string;
        }
    
        $values = Abastecimentos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('condutor_id','{condutor->id}');
        return implode(', ', $values);
    }

    public function set_abastecimentos_veiculos_to_string($abastecimentos_veiculos_to_string)
    {
        if(is_array($abastecimentos_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $abastecimentos_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->abastecimentos_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_veiculos_to_string = $abastecimentos_veiculos_to_string;
        }

        $this->vdata['abastecimentos_veiculos_to_string'] = $this->abastecimentos_veiculos_to_string;
    }

    public function get_abastecimentos_veiculos_to_string()
    {
        if(!empty($this->abastecimentos_veiculos_to_string))
        {
            return $this->abastecimentos_veiculos_to_string;
        }
    
        $values = Abastecimentos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    public function set_abastecimentos_tipo_combustivel_to_string($abastecimentos_tipo_combustivel_to_string)
    {
        if(is_array($abastecimentos_tipo_combustivel_to_string))
        {
            $values = TipoCombustivel::where('id', 'in', $abastecimentos_tipo_combustivel_to_string)->getIndexedArray('id', 'id');
            $this->abastecimentos_tipo_combustivel_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_tipo_combustivel_to_string = $abastecimentos_tipo_combustivel_to_string;
        }

        $this->vdata['abastecimentos_tipo_combustivel_to_string'] = $this->abastecimentos_tipo_combustivel_to_string;
    }

    public function get_abastecimentos_tipo_combustivel_to_string()
    {
        if(!empty($this->abastecimentos_tipo_combustivel_to_string))
        {
            return $this->abastecimentos_tipo_combustivel_to_string;
        }
    
        $values = Abastecimentos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('tipo_combustivel_id','{tipo_combustivel->id}');
        return implode(', ', $values);
    }

    public function set_abastecimentos_system_unit_to_string($abastecimentos_system_unit_to_string)
    {
        if(is_array($abastecimentos_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $abastecimentos_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->abastecimentos_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_system_unit_to_string = $abastecimentos_system_unit_to_string;
        }

        $this->vdata['abastecimentos_system_unit_to_string'] = $this->abastecimentos_system_unit_to_string;
    }

    public function get_abastecimentos_system_unit_to_string()
    {
        if(!empty($this->abastecimentos_system_unit_to_string))
        {
            return $this->abastecimentos_system_unit_to_string;
        }
    
        $values = Abastecimentos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    public function set_abastecimentos_subsystem_unit_to_string($abastecimentos_subsystem_unit_to_string)
    {
        if(is_array($abastecimentos_subsystem_unit_to_string))
        {
            $values = SubsystemUnit::where('id', 'in', $abastecimentos_subsystem_unit_to_string)->getIndexedArray('name', 'name');
            $this->abastecimentos_subsystem_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_subsystem_unit_to_string = $abastecimentos_subsystem_unit_to_string;
        }

        $this->vdata['abastecimentos_subsystem_unit_to_string'] = $this->abastecimentos_subsystem_unit_to_string;
    }

    public function get_abastecimentos_subsystem_unit_to_string()
    {
        if(!empty($this->abastecimentos_subsystem_unit_to_string))
        {
            return $this->abastecimentos_subsystem_unit_to_string;
        }
    
        $values = Abastecimentos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('subsystem_unit_id','{subsystem_unit->name}');
        return implode(', ', $values);
    }

    public function set_abastecimentos_system_users_to_string($abastecimentos_system_users_to_string)
    {
        if(is_array($abastecimentos_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $abastecimentos_system_users_to_string)->getIndexedArray('name', 'name');
            $this->abastecimentos_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_system_users_to_string = $abastecimentos_system_users_to_string;
        }

        $this->vdata['abastecimentos_system_users_to_string'] = $this->abastecimentos_system_users_to_string;
    }

    public function get_abastecimentos_system_users_to_string()
    {
        if(!empty($this->abastecimentos_system_users_to_string))
        {
            return $this->abastecimentos_system_users_to_string;
        }
    
        $values = Abastecimentos::where('tipo_combustivel_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(Veiculos::where('tipo_combustivel_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(Abastecimentos::where('tipo_combustivel_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

