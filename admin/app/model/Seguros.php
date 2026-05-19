<?php

class Seguros extends TRecord
{
    const TABLENAME  = 'seguros';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private SaldoEntidadeContrato $saldo_entidade_contrato;
    private TipoSeguro $tipo_seguro;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('saldo_entidade_contrato_id');
        parent::addAttribute('tipo_seguro_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('data_inicio');
        parent::addAttribute('data_final');
        parent::addAttribute('numero_apolice');
        parent::addAttribute('valor_cobertura');
        parent::addAttribute('obs');
    
    }

    /**
     * Method set_saldo_entidade_contrato
     * Sample of usage: $var->saldo_entidade_contrato = $object;
     * @param $object Instance of SaldoEntidadeContrato
     */
    public function set_saldo_entidade_contrato(SaldoEntidadeContrato $object)
    {
        $this->saldo_entidade_contrato = $object;
        $this->saldo_entidade_contrato_id = $object->id;
    }

    /**
     * Method get_saldo_entidade_contrato
     * Sample of usage: $var->saldo_entidade_contrato->attribute;
     * @returns SaldoEntidadeContrato instance
     */
    public function get_saldo_entidade_contrato()
    {
    
        // loads the associated object
        if (empty($this->saldo_entidade_contrato))
            $this->saldo_entidade_contrato = new SaldoEntidadeContrato($this->saldo_entidade_contrato_id);
    
        // returns the associated object
        return $this->saldo_entidade_contrato;
    }
    /**
     * Method set_tipo_seguro
     * Sample of usage: $var->tipo_seguro = $object;
     * @param $object Instance of TipoSeguro
     */
    public function set_tipo_seguro(TipoSeguro $object)
    {
        $this->tipo_seguro = $object;
        $this->tipo_seguro_id = $object->id;
    }

    /**
     * Method get_tipo_seguro
     * Sample of usage: $var->tipo_seguro->attribute;
     * @returns TipoSeguro instance
     */
    public function get_tipo_seguro()
    {
    
        // loads the associated object
        if (empty($this->tipo_seguro))
            $this->tipo_seguro = new TipoSeguro($this->tipo_seguro_id);
    
        // returns the associated object
        return $this->tipo_seguro;
    }

    /**
     * Method getAnexosSeguross
     */
    public function getAnexosSeguross()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('seguros_id', '=', $this->id));
        return AnexosSeguros::getObjects( $criteria );
    }

    public function set_anexos_seguros_seguros_to_string($anexos_seguros_seguros_to_string)
    {
        if(is_array($anexos_seguros_seguros_to_string))
        {
            $values = Seguros::where('id', 'in', $anexos_seguros_seguros_to_string)->getIndexedArray('id', 'id');
            $this->anexos_seguros_seguros_to_string = implode(', ', $values);
        }
        else
        {
            $this->anexos_seguros_seguros_to_string = $anexos_seguros_seguros_to_string;
        }

        $this->vdata['anexos_seguros_seguros_to_string'] = $this->anexos_seguros_seguros_to_string;
    }

    public function get_anexos_seguros_seguros_to_string()
    {
        if(!empty($this->anexos_seguros_seguros_to_string))
        {
            return $this->anexos_seguros_seguros_to_string;
        }
    
        $values = AnexosSeguros::where('seguros_id', '=', $this->id)->getIndexedArray('seguros_id','{seguros->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
    

        if(AnexosSeguros::where('seguros_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

}

