<?php

//<fileHeader>
  
//</fileHeader>

class CategoriaCnh extends TRecord
{
    const TABLENAME  = 'categoria_cnh';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    
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
        parent::addAttribute('descricao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    
    /**
     * Method getPessoas
     */
    public function getPessoas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('categoria_cnh_id', '=', $this->id));
        return Pessoa::getObjects( $criteria );
    }

    
    public function set_pessoa_tipo_cliente_to_string($pessoa_tipo_cliente_to_string)
    {
        if(is_array($pessoa_tipo_cliente_to_string))
        {
            $values = TipoCliente::where('id', 'in', $pessoa_tipo_cliente_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_tipo_cliente_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_tipo_cliente_to_string = $pessoa_tipo_cliente_to_string;
        }

        $this->vdata['pessoa_tipo_cliente_to_string'] = $this->pessoa_tipo_cliente_to_string;
    }

    public function get_pessoa_tipo_cliente_to_string()
    {
        if(!empty($this->pessoa_tipo_cliente_to_string))
        {
            return $this->pessoa_tipo_cliente_to_string;
        }
        
        $values = Pessoa::where('categoria_cnh_id', '=', $this->id)->getIndexedArray('tipo_cliente_id','{tipo_cliente->nome}');
        return implode(', ', $values);
    }

    
    public function set_pessoa_categoria_cliente_to_string($pessoa_categoria_cliente_to_string)
    {
        if(is_array($pessoa_categoria_cliente_to_string))
        {
            $values = CategoriaCliente::where('id', 'in', $pessoa_categoria_cliente_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_categoria_cliente_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_categoria_cliente_to_string = $pessoa_categoria_cliente_to_string;
        }

        $this->vdata['pessoa_categoria_cliente_to_string'] = $this->pessoa_categoria_cliente_to_string;
    }

    public function get_pessoa_categoria_cliente_to_string()
    {
        if(!empty($this->pessoa_categoria_cliente_to_string))
        {
            return $this->pessoa_categoria_cliente_to_string;
        }
        
        $values = Pessoa::where('categoria_cnh_id', '=', $this->id)->getIndexedArray('categoria_cliente_id','{categoria_cliente->nome}');
        return implode(', ', $values);
    }

    
    public function set_pessoa_system_user_to_string($pessoa_system_user_to_string)
    {
        if(is_array($pessoa_system_user_to_string))
        {
            $values = SystemUsers::where('id', 'in', $pessoa_system_user_to_string)->getIndexedArray('name', 'name');
            $this->pessoa_system_user_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_system_user_to_string = $pessoa_system_user_to_string;
        }

        $this->vdata['pessoa_system_user_to_string'] = $this->pessoa_system_user_to_string;
    }

    public function get_pessoa_system_user_to_string()
    {
        if(!empty($this->pessoa_system_user_to_string))
        {
            return $this->pessoa_system_user_to_string;
        }
        
        $values = Pessoa::where('categoria_cnh_id', '=', $this->id)->getIndexedArray('system_user_id','{system_user->name}');
        return implode(', ', $values);
    }

    
    public function set_pessoa_system_unit_to_string($pessoa_system_unit_to_string)
    {
        if(is_array($pessoa_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $pessoa_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->pessoa_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_system_unit_to_string = $pessoa_system_unit_to_string;
        }

        $this->vdata['pessoa_system_unit_to_string'] = $this->pessoa_system_unit_to_string;
    }

    public function get_pessoa_system_unit_to_string()
    {
        if(!empty($this->pessoa_system_unit_to_string))
        {
            return $this->pessoa_system_unit_to_string;
        }
        
        $values = Pessoa::where('categoria_cnh_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_pessoa_system_users_to_string($pessoa_system_users_to_string)
    {
        if(is_array($pessoa_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $pessoa_system_users_to_string)->getIndexedArray('name', 'name');
            $this->pessoa_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_system_users_to_string = $pessoa_system_users_to_string;
        }

        $this->vdata['pessoa_system_users_to_string'] = $this->pessoa_system_users_to_string;
    }

    public function get_pessoa_system_users_to_string()
    {
        if(!empty($this->pessoa_system_users_to_string))
        {
            return $this->pessoa_system_users_to_string;
        }
        
        $values = Pessoa::where('categoria_cnh_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    
    public function set_pessoa_categoria_cnh_to_string($pessoa_categoria_cnh_to_string)
    {
        if(is_array($pessoa_categoria_cnh_to_string))
        {
            $values = CategoriaCnh::where('id', 'in', $pessoa_categoria_cnh_to_string)->getIndexedArray('id', 'id');
            $this->pessoa_categoria_cnh_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_categoria_cnh_to_string = $pessoa_categoria_cnh_to_string;
        }

        $this->vdata['pessoa_categoria_cnh_to_string'] = $this->pessoa_categoria_cnh_to_string;
    }

    public function get_pessoa_categoria_cnh_to_string()
    {
        if(!empty($this->pessoa_categoria_cnh_to_string))
        {
            return $this->pessoa_categoria_cnh_to_string;
        }
        
        $values = Pessoa::where('categoria_cnh_id', '=', $this->id)->getIndexedArray('categoria_cnh_id','{categoria_cnh->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
        //<onBeforeDeleteCode>
  
        //</onBeforeDeleteCode>

        if(Pessoa::where('categoria_cnh_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
    }
    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

