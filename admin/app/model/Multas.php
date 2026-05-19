<?php

class Multas extends TRecord
{
    const TABLENAME  = 'multas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private Veiculos $veiculos;
    private Pessoa $condutor;
    private SystemUnit $system_unit;
    private DepartamentoUnit $departamento_unit;
    private SystemUsers $system_users;
    private StatusMultas $status_multas;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('veiculos_id');
        parent::addAttribute('condutor_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('status_multas_id');
        parent::addAttribute('numero_alt');
        parent::addAttribute('enquadramento');
        parent::addAttribute('descricao');
        parent::addAttribute('data_infracao');
        parent::addAttribute('local_infracao');
        parent::addAttribute('orgao_autuador');
        parent::addAttribute('pontos_cnh');
        parent::addAttribute('valor_original');
        parent::addAttribute('valor_desconto');
        parent::addAttribute('parcela');
        parent::addAttribute('data_vencimento');
        parent::addAttribute('data_pagamento');
        parent::addAttribute('valor_pago');
        parent::addAttribute('motivo_cancelamento');
        parent::addAttribute('obs');
        parent::addAttribute('deleted_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('created_at');
            
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
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_condutor(Pessoa $object)
    {
        $this->condutor = $object;
        $this->condutor_id = $object->id;
    }

    /**
     * Method get_condutor
     * Sample of usage: $var->condutor->attribute;
     * @returns Pessoa instance
     */
    public function get_condutor()
    {
    
        // loads the associated object
        if (empty($this->condutor))
            $this->condutor = new Pessoa($this->condutor_id);
    
        // returns the associated object
        return $this->condutor;
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
     * Method set_status_multas
     * Sample of usage: $var->status_multas = $object;
     * @param $object Instance of StatusMultas
     */
    public function set_status_multas(StatusMultas $object)
    {
        $this->status_multas = $object;
        $this->status_multas_id = $object->id;
    }

    /**
     * Method get_status_multas
     * Sample of usage: $var->status_multas->attribute;
     * @returns StatusMultas instance
     */
    public function get_status_multas()
    {
    
        // loads the associated object
        if (empty($this->status_multas))
            $this->status_multas = new StatusMultas($this->status_multas_id);
    
        // returns the associated object
        return $this->status_multas;
    }

    /**
     * Method getMultasAnexoss
     */
    public function getMultasAnexoss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('multas_id', '=', $this->id));
        return MultasAnexos::getObjects( $criteria );
    }

    public function set_multas_anexos_multas_to_string($multas_anexos_multas_to_string)
    {
        if(is_array($multas_anexos_multas_to_string))
        {
            $values = Multas::where('id', 'in', $multas_anexos_multas_to_string)->getIndexedArray('id', 'id');
            $this->multas_anexos_multas_to_string = implode(', ', $values);
        }
        else
        {
            $this->multas_anexos_multas_to_string = $multas_anexos_multas_to_string;
        }

        $this->vdata['multas_anexos_multas_to_string'] = $this->multas_anexos_multas_to_string;
    }

    public function get_multas_anexos_multas_to_string()
    {
        if(!empty($this->multas_anexos_multas_to_string))
        {
            return $this->multas_anexos_multas_to_string;
        }
    
        $values = MultasAnexos::where('multas_id', '=', $this->id)->getIndexedArray('multas_id','{multas->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(MultasAnexos::where('multas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

