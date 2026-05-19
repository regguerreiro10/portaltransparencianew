<?php

class StatusSaldoDepartamento extends TRecord
{
    const TABLENAME  = 'status_saldo_departamento';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    const AGUARDANDOINIC = '1';
    const EMANDAMENTO = '2';
    const ENCERRADO = '3';
    const ANULADO = '4';

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('descricao');
        parent::addAttribute('cor');
            
    }

    /**
     * Method getSaldoDepartamentos
     */
    public function getSaldoDepartamentos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('status_saldo_departamento_id', '=', $this->id));
        return SaldoDepartamento::getObjects( $criteria );
    }

    public function set_saldo_departamento_departamento_unit_to_string($saldo_departamento_departamento_unit_to_string)
    {
        if(is_array($saldo_departamento_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $saldo_departamento_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->saldo_departamento_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->saldo_departamento_departamento_unit_to_string = $saldo_departamento_departamento_unit_to_string;
        }

        $this->vdata['saldo_departamento_departamento_unit_to_string'] = $this->saldo_departamento_departamento_unit_to_string;
    }

    public function get_saldo_departamento_departamento_unit_to_string()
    {
        if(!empty($this->saldo_departamento_departamento_unit_to_string))
        {
            return $this->saldo_departamento_departamento_unit_to_string;
        }
    
        $values = SaldoDepartamento::where('status_saldo_departamento_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    public function set_saldo_departamento_system_users_to_string($saldo_departamento_system_users_to_string)
    {
        if(is_array($saldo_departamento_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $saldo_departamento_system_users_to_string)->getIndexedArray('name', 'name');
            $this->saldo_departamento_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->saldo_departamento_system_users_to_string = $saldo_departamento_system_users_to_string;
        }

        $this->vdata['saldo_departamento_system_users_to_string'] = $this->saldo_departamento_system_users_to_string;
    }

    public function get_saldo_departamento_system_users_to_string()
    {
        if(!empty($this->saldo_departamento_system_users_to_string))
        {
            return $this->saldo_departamento_system_users_to_string;
        }
    
        $values = SaldoDepartamento::where('status_saldo_departamento_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_saldo_departamento_saldo_entidade_contrato_to_string($saldo_departamento_saldo_entidade_contrato_to_string)
    {
        if(is_array($saldo_departamento_saldo_entidade_contrato_to_string))
        {
            $values = SaldoEntidadeContrato::where('id', 'in', $saldo_departamento_saldo_entidade_contrato_to_string)->getIndexedArray('id', 'id');
            $this->saldo_departamento_saldo_entidade_contrato_to_string = implode(', ', $values);
        }
        else
        {
            $this->saldo_departamento_saldo_entidade_contrato_to_string = $saldo_departamento_saldo_entidade_contrato_to_string;
        }

        $this->vdata['saldo_departamento_saldo_entidade_contrato_to_string'] = $this->saldo_departamento_saldo_entidade_contrato_to_string;
    }

    public function get_saldo_departamento_saldo_entidade_contrato_to_string()
    {
        if(!empty($this->saldo_departamento_saldo_entidade_contrato_to_string))
        {
            return $this->saldo_departamento_saldo_entidade_contrato_to_string;
        }
    
        $values = SaldoDepartamento::where('status_saldo_departamento_id', '=', $this->id)->getIndexedArray('saldo_entidade_contrato_id','{saldo_entidade_contrato->id}');
        return implode(', ', $values);
    }

    public function set_saldo_departamento_status_saldo_departamento_to_string($saldo_departamento_status_saldo_departamento_to_string)
    {
        if(is_array($saldo_departamento_status_saldo_departamento_to_string))
        {
            $values = StatusSaldoDepartamento::where('id', 'in', $saldo_departamento_status_saldo_departamento_to_string)->getIndexedArray('id', 'id');
            $this->saldo_departamento_status_saldo_departamento_to_string = implode(', ', $values);
        }
        else
        {
            $this->saldo_departamento_status_saldo_departamento_to_string = $saldo_departamento_status_saldo_departamento_to_string;
        }

        $this->vdata['saldo_departamento_status_saldo_departamento_to_string'] = $this->saldo_departamento_status_saldo_departamento_to_string;
    }

    public function get_saldo_departamento_status_saldo_departamento_to_string()
    {
        if(!empty($this->saldo_departamento_status_saldo_departamento_to_string))
        {
            return $this->saldo_departamento_status_saldo_departamento_to_string;
        }
    
        $values = SaldoDepartamento::where('status_saldo_departamento_id', '=', $this->id)->getIndexedArray('status_saldo_departamento_id','{status_saldo_departamento->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(SaldoDepartamento::where('status_saldo_departamento_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

