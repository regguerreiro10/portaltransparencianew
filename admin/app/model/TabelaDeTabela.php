<?php

class TabelaDeTabela extends TRecord
{
    const TABLENAME  = 'tabela_de_tabela';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private Tabela $tabela;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tabela_id');
        parent::addAttribute('descricao');
        parent::addAttribute('cor');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method set_tabela
     * Sample of usage: $var->tabela = $object;
     * @param $object Instance of Tabela
     */
    public function set_tabela(Tabela $object)
    {
        $this->tabela = $object;
        $this->tabela_id = $object->id;
    }

    /**
     * Method get_tabela
     * Sample of usage: $var->tabela->attribute;
     * @returns Tabela instance
     */
    public function get_tabela()
    {
    
        // loads the associated object
        if (empty($this->tabela))
            $this->tabela = new Tabela($this->tabela_id);
    
        // returns the associated object
        return $this->tabela;
    }

    /**
     * Method getDocumentoPublicos
     */
    public function getDocumentoPublicosByDocumentoPublicoTipos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('documento_publico_tipo_id', '=', $this->id));
        return DocumentoPublico::getObjects( $criteria );
    }
    /**
     * Method getDocumentoPublicos
     */
    public function getDocumentoPublicosByDocumentoPublicoStatuss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('documento_publico_status_id', '=', $this->id));
        return DocumentoPublico::getObjects( $criteria );
    }

    public function set_documento_publico_documento_publico_tipo_to_string($documento_publico_documento_publico_tipo_to_string)
    {
        if(is_array($documento_publico_documento_publico_tipo_to_string))
        {
            $values = TabelaDeTabela::where('id', 'in', $documento_publico_documento_publico_tipo_to_string)->getIndexedArray('id', 'id');
            $this->documento_publico_documento_publico_tipo_to_string = implode(', ', $values);
        }
        else
        {
            $this->documento_publico_documento_publico_tipo_to_string = $documento_publico_documento_publico_tipo_to_string;
        }

        $this->vdata['documento_publico_documento_publico_tipo_to_string'] = $this->documento_publico_documento_publico_tipo_to_string;
    }

    public function get_documento_publico_documento_publico_tipo_to_string()
    {
        if(!empty($this->documento_publico_documento_publico_tipo_to_string))
        {
            return $this->documento_publico_documento_publico_tipo_to_string;
        }
    
        $values = DocumentoPublico::where('documento_publico_status_id', '=', $this->id)->getIndexedArray('documento_publico_tipo_id','{documento_publico_tipo->id}');
        return implode(', ', $values);
    }

    public function set_documento_publico_documento_publico_status_to_string($documento_publico_documento_publico_status_to_string)
    {
        if(is_array($documento_publico_documento_publico_status_to_string))
        {
            $values = TabelaDeTabela::where('id', 'in', $documento_publico_documento_publico_status_to_string)->getIndexedArray('id', 'id');
            $this->documento_publico_documento_publico_status_to_string = implode(', ', $values);
        }
        else
        {
            $this->documento_publico_documento_publico_status_to_string = $documento_publico_documento_publico_status_to_string;
        }

        $this->vdata['documento_publico_documento_publico_status_to_string'] = $this->documento_publico_documento_publico_status_to_string;
    }

    public function get_documento_publico_documento_publico_status_to_string()
    {
        if(!empty($this->documento_publico_documento_publico_status_to_string))
        {
            return $this->documento_publico_documento_publico_status_to_string;
        }
    
        $values = DocumentoPublico::where('documento_publico_status_id', '=', $this->id)->getIndexedArray('documento_publico_status_id','{documento_publico_status->id}');
        return implode(', ', $values);
    }

    public function set_documento_publico_system_unit_to_string($documento_publico_system_unit_to_string)
    {
        if(is_array($documento_publico_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $documento_publico_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->documento_publico_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->documento_publico_system_unit_to_string = $documento_publico_system_unit_to_string;
        }

        $this->vdata['documento_publico_system_unit_to_string'] = $this->documento_publico_system_unit_to_string;
    }

    public function get_documento_publico_system_unit_to_string()
    {
        if(!empty($this->documento_publico_system_unit_to_string))
        {
            return $this->documento_publico_system_unit_to_string;
        }
    
        $values = DocumentoPublico::where('documento_publico_status_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    public function set_documento_publico_system_users_to_string($documento_publico_system_users_to_string)
    {
        if(is_array($documento_publico_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $documento_publico_system_users_to_string)->getIndexedArray('name', 'name');
            $this->documento_publico_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->documento_publico_system_users_to_string = $documento_publico_system_users_to_string;
        }

        $this->vdata['documento_publico_system_users_to_string'] = $this->documento_publico_system_users_to_string;
    }

    public function get_documento_publico_system_users_to_string()
    {
        if(!empty($this->documento_publico_system_users_to_string))
        {
            return $this->documento_publico_system_users_to_string;
        }
    
        $values = DocumentoPublico::where('documento_publico_status_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_documento_publico_entidade_to_string($documento_publico_entidade_to_string)
    {
        if(is_array($documento_publico_entidade_to_string))
        {
            $values = Entidade::where('id', 'in', $documento_publico_entidade_to_string)->getIndexedArray('id', 'id');
            $this->documento_publico_entidade_to_string = implode(', ', $values);
        }
        else
        {
            $this->documento_publico_entidade_to_string = $documento_publico_entidade_to_string;
        }

        $this->vdata['documento_publico_entidade_to_string'] = $this->documento_publico_entidade_to_string;
    }

    public function get_documento_publico_entidade_to_string()
    {
        if(!empty($this->documento_publico_entidade_to_string))
        {
            return $this->documento_publico_entidade_to_string;
        }
    
        $values = DocumentoPublico::where('documento_publico_status_id', '=', $this->id)->getIndexedArray('entidade_id','{entidade->id}');
        return implode(', ', $values);
    }

    public function set_documento_publico_departamento_unit_to_string($documento_publico_departamento_unit_to_string)
    {
        if(is_array($documento_publico_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $documento_publico_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->documento_publico_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->documento_publico_departamento_unit_to_string = $documento_publico_departamento_unit_to_string;
        }

        $this->vdata['documento_publico_departamento_unit_to_string'] = $this->documento_publico_departamento_unit_to_string;
    }

    public function get_documento_publico_departamento_unit_to_string()
    {
        if(!empty($this->documento_publico_departamento_unit_to_string))
        {
            return $this->documento_publico_departamento_unit_to_string;
        }
    
        $values = DocumentoPublico::where('documento_publico_status_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

    
    }

    
}
