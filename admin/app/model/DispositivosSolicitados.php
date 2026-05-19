<?php

class DispositivosSolicitados extends TRecord
{
    const TABLENAME  = 'dispositivos_solicitados';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private Dispositivos $dispositivos;
    private Veiculos $veiculos;
    private StatusDispositivos $status_dispositivos;
    private SystemUnit $system_unit;
    private DepartamentoUnit $departamento_unit;
    private SystemUsers $system_users;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('numerocartao');
        parent::addAttribute('datasolicitacao');
        parent::addAttribute('dispositivos_id');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('status_dispositivos_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('via');
        parent::addAttribute('rastreio');
        parent::addAttribute('coringa');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('saldo_atual');
        parent::addAttribute('saldo_limite');
            
    }

    /**
     * Method set_dispositivos
     * Sample of usage: $var->dispositivos = $object;
     * @param $object Instance of Dispositivos
     */
    public function set_dispositivos(Dispositivos $object)
    {
        $this->dispositivos = $object;
        $this->dispositivos_id = $object->id;
    }

    /**
     * Method get_dispositivos
     * Sample of usage: $var->dispositivos->attribute;
     * @returns Dispositivos instance
     */
    public function get_dispositivos()
    {
    
        // loads the associated object
        if (empty($this->dispositivos))
            $this->dispositivos = new Dispositivos($this->dispositivos_id);
    
        // returns the associated object
        return $this->dispositivos;
    }
    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;
    }

    /**
     * Method get_pessoa
     * Sample of usage: $var->pessoa->attribute;
     * @returns pessoa instance
     */
    public function get_pessoa()
    {
    
        // loads the associated object
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->pessoa_id);
    
        // returns the associated object
        return $this->pessoa;
    }
    /**
     * Method set_veiculos
     * Sample of usage: $var->veiculos = $object;
     * @param $object Instance of Veiculos
     */
    public function set_veiculos(Veiculos $object)
    {
        $this->veiculos = $object;
        $this->veiculos_id = $object->id;
    }

    /**
     * Method get_veiculos
     * Sample of usage: $var->veiculos->attribute;
     * @returns Veiculos instance
     */
    public function get_veiculos()
    {
    
        // loads the associated object
        if (empty($this->veiculos))
            $this->veiculos = new Veiculos($this->veiculos_id);
    
        // returns the associated object
        return $this->veiculos;
    }
    /**
     * Method set_status_dispositivos
     * Sample of usage: $var->status_dispositivos = $object;
     * @param $object Instance of StatusDispositivos
     */
    public function set_status_dispositivos(StatusDispositivos $object)
    {
        $this->status_dispositivos = $object;
        $this->status_dispositivos_id = $object->id;
    }

    /**
     * Method get_status_dispositivos
     * Sample of usage: $var->status_dispositivos->attribute;
     * @returns StatusDispositivos instance
     */
    public function get_status_dispositivos()
    {
    
        // loads the associated object
        if (empty($this->status_dispositivos))
            $this->status_dispositivos = new StatusDispositivos($this->status_dispositivos_id);
    
        // returns the associated object
        return $this->status_dispositivos;
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
     * Method getMovimentoDispositivoss
     */
    public function getMovimentoDispositivoss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('dispositivos_solicitados_id', '=', $this->id));
        return MovimentoDispositivos::getObjects( $criteria );
    }

    public function set_movimento_dispositivos_estabelecimento_to_string($movimento_dispositivos_estabelecimento_to_string)
    {
        if(is_array($movimento_dispositivos_estabelecimento_to_string))
        {
            $values = Pessoa::where('id', 'in', $movimento_dispositivos_estabelecimento_to_string)->getIndexedArray('nome', 'nome');
            $this->movimento_dispositivos_estabelecimento_to_string = implode(', ', $values);
        }
        else
        {
            $this->movimento_dispositivos_estabelecimento_to_string = $movimento_dispositivos_estabelecimento_to_string;
        }

        $this->vdata['movimento_dispositivos_estabelecimento_to_string'] = $this->movimento_dispositivos_estabelecimento_to_string;
    }

    public function get_movimento_dispositivos_estabelecimento_to_string()
    {
        if(!empty($this->movimento_dispositivos_estabelecimento_to_string))
        {
            return $this->movimento_dispositivos_estabelecimento_to_string;
        }
    
        $values = MovimentoDispositivos::where('dispositivos_solicitados_id', '=', $this->id)->getIndexedArray('estabelecimento_id','{estabelecimento->nome}');
        return implode(', ', $values);
    }

    public function set_movimento_dispositivos_condutor_to_string($movimento_dispositivos_condutor_to_string)
    {
        if(is_array($movimento_dispositivos_condutor_to_string))
        {
            $values = Pessoa::where('id', 'in', $movimento_dispositivos_condutor_to_string)->getIndexedArray('nome', 'nome');
            $this->movimento_dispositivos_condutor_to_string = implode(', ', $values);
        }
        else
        {
            $this->movimento_dispositivos_condutor_to_string = $movimento_dispositivos_condutor_to_string;
        }

        $this->vdata['movimento_dispositivos_condutor_to_string'] = $this->movimento_dispositivos_condutor_to_string;
    }

    public function get_movimento_dispositivos_condutor_to_string()
    {
        if(!empty($this->movimento_dispositivos_condutor_to_string))
        {
            return $this->movimento_dispositivos_condutor_to_string;
        }
    
        $values = MovimentoDispositivos::where('dispositivos_solicitados_id', '=', $this->id)->getIndexedArray('condutor_id','{condutor->nome}');
        return implode(', ', $values);
    }

    public function set_movimento_dispositivos_dispositivos_solicitados_to_string($movimento_dispositivos_dispositivos_solicitados_to_string)
    {
        if(is_array($movimento_dispositivos_dispositivos_solicitados_to_string))
        {
            $values = DispositivosSolicitados::where('id', 'in', $movimento_dispositivos_dispositivos_solicitados_to_string)->getIndexedArray('id', 'id');
            $this->movimento_dispositivos_dispositivos_solicitados_to_string = implode(', ', $values);
        }
        else
        {
            $this->movimento_dispositivos_dispositivos_solicitados_to_string = $movimento_dispositivos_dispositivos_solicitados_to_string;
        }

        $this->vdata['movimento_dispositivos_dispositivos_solicitados_to_string'] = $this->movimento_dispositivos_dispositivos_solicitados_to_string;
    }

    public function get_movimento_dispositivos_dispositivos_solicitados_to_string()
    {
        if(!empty($this->movimento_dispositivos_dispositivos_solicitados_to_string))
        {
            return $this->movimento_dispositivos_dispositivos_solicitados_to_string;
        }
    
        $values = MovimentoDispositivos::where('dispositivos_solicitados_id', '=', $this->id)->getIndexedArray('dispositivos_solicitados_id','{dispositivos_solicitados->id}');
        return implode(', ', $values);
    }

    
}

