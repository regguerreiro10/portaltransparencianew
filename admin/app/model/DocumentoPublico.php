<?php

class DocumentoPublico extends TRecord
{
    const TABLENAME  = 'documento_publico';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private TabelaDeTabela $documento_publico_tipo;
    private TabelaDeTabela $documento_publico_status;
    private SystemUnit $system_unit;
    private SystemUsers $system_users;
    private Entidade $entidade;
    private DepartamentoUnit $departamento_unit;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('numero_documento');
        parent::addAttribute('tipo');
        parent::addAttribute('documento_publico_tipo_id');
        parent::addAttribute('data_documento');
        parent::addAttribute('assunto');
        parent::addAttribute('nome');
        parent::addAttribute('orgao');
        parent::addAttribute('status');
        parent::addAttribute('documento_publico_status_id');
        parent::addAttribute('downloads');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('deleted_at');
        parent::addAttribute('system_users_id');
        parent::addAttribute('entidade_id');
        parent::addAttribute('departamento_unit_id');
            
    }

    /**
     * Method set_tabela_de_tabela
     * Sample of usage: $var->tabela_de_tabela = $object;
     * @param $object Instance of TabelaDeTabela
     */
    public function set_documento_publico_tipo(TabelaDeTabela $object)
    {
        $this->documento_publico_tipo = $object;
        $this->documento_publico_tipo_id = $object->id;
    }

    /**
     * Method get_documento_publico_tipo
     * Sample of usage: $var->documento_publico_tipo->attribute;
     * @returns TabelaDeTabela instance
     */
    public function get_documento_publico_tipo()
    {
    
        // loads the associated object
        if (empty($this->documento_publico_tipo))
            $this->documento_publico_tipo = new TabelaDeTabela($this->documento_publico_tipo_id);
    
        // returns the associated object
        return $this->documento_publico_tipo;
    }
    /**
     * Method set_tabela_de_tabela
     * Sample of usage: $var->tabela_de_tabela = $object;
     * @param $object Instance of TabelaDeTabela
     */
    public function set_documento_publico_status(TabelaDeTabela $object)
    {
        $this->documento_publico_status = $object;
        $this->documento_publico_status_id = $object->id;
    }

    /**
     * Method get_documento_publico_status
     * Sample of usage: $var->documento_publico_status->attribute;
     * @returns TabelaDeTabela instance
     */
    public function get_documento_publico_status()
    {
    
        // loads the associated object
        if (empty($this->documento_publico_status))
            $this->documento_publico_status = new TabelaDeTabela($this->documento_publico_status_id);
    
        // returns the associated object
        return $this->documento_publico_status;
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

    /**
     * Method getDocumentoPublicoAnexos
     */
    public function getDocumentoPublicoAnexos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('documento_publico_id', '=', $this->id));
        return DocumentoPublicoAnexo::getObjects( $criteria );
    }

    public function set_documento_publico_anexo_documento_publico_to_string($documento_publico_anexo_documento_publico_to_string)
    {
        if(is_array($documento_publico_anexo_documento_publico_to_string))
        {
            $values = DocumentoPublico::where('id', 'in', $documento_publico_anexo_documento_publico_to_string)->getIndexedArray('id', 'id');
            $this->documento_publico_anexo_documento_publico_to_string = implode(', ', $values);
        }
        else
        {
            $this->documento_publico_anexo_documento_publico_to_string = $documento_publico_anexo_documento_publico_to_string;
        }

        $this->vdata['documento_publico_anexo_documento_publico_to_string'] = $this->documento_publico_anexo_documento_publico_to_string;
    }

    public function get_documento_publico_anexo_documento_publico_to_string()
    {
        if(!empty($this->documento_publico_anexo_documento_publico_to_string))
        {
            return $this->documento_publico_anexo_documento_publico_to_string;
        }
    
        $values = DocumentoPublicoAnexo::where('documento_publico_id', '=', $this->id)->getIndexedArray('documento_publico_id','{documento_publico->id}');
        return implode(', ', $values);
    }

    
}
