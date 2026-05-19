<?php

class TipoSeguro extends TRecord
{
    const TABLENAME  = 'tipo_seguro';
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
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    
    }

    /**
     * Method getSeguross
     */
    public function getSeguross()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_seguro_id', '=', $this->id));
        return Seguros::getObjects( $criteria );
    }

    public function set_seguros_saldo_entidade_contrato_to_string($seguros_saldo_entidade_contrato_to_string)
    {
        if(is_array($seguros_saldo_entidade_contrato_to_string))
        {
            $values = SaldoEntidadeContrato::where('id', 'in', $seguros_saldo_entidade_contrato_to_string)->getIndexedArray('id', 'id');
            $this->seguros_saldo_entidade_contrato_to_string = implode(', ', $values);
        }
        else
        {
            $this->seguros_saldo_entidade_contrato_to_string = $seguros_saldo_entidade_contrato_to_string;
        }

        $this->vdata['seguros_saldo_entidade_contrato_to_string'] = $this->seguros_saldo_entidade_contrato_to_string;
    }

    public function get_seguros_saldo_entidade_contrato_to_string()
    {
        if(!empty($this->seguros_saldo_entidade_contrato_to_string))
        {
            return $this->seguros_saldo_entidade_contrato_to_string;
        }
    
        $values = Seguros::where('tipo_seguro_id', '=', $this->id)->getIndexedArray('saldo_entidade_contrato_id','{saldo_entidade_contrato->id}');
        return implode(', ', $values);
    }

    public function set_seguros_tipo_seguro_to_string($seguros_tipo_seguro_to_string)
    {
        if(is_array($seguros_tipo_seguro_to_string))
        {
            $values = TipoSeguro::where('id', 'in', $seguros_tipo_seguro_to_string)->getIndexedArray('id', 'id');
            $this->seguros_tipo_seguro_to_string = implode(', ', $values);
        }
        else
        {
            $this->seguros_tipo_seguro_to_string = $seguros_tipo_seguro_to_string;
        }

        $this->vdata['seguros_tipo_seguro_to_string'] = $this->seguros_tipo_seguro_to_string;
    }

    public function get_seguros_tipo_seguro_to_string()
    {
        if(!empty($this->seguros_tipo_seguro_to_string))
        {
            return $this->seguros_tipo_seguro_to_string;
        }
    
        $values = Seguros::where('tipo_seguro_id', '=', $this->id)->getIndexedArray('tipo_seguro_id','{tipo_seguro->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
    

        if(Seguros::where('tipo_seguro_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

}

