<?php

class TipoFinalidade extends TRecord
{
    const TABLENAME  = 'tipo_finalidade';
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
     * Method getDispositivoss
     */
    public function getDispositivoss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_finalidade_id', '=', $this->id));
        return Dispositivos::getObjects( $criteria );
    }

    public function set_dispositivos_tipo_finalidade_to_string($dispositivos_tipo_finalidade_to_string)
    {
        if(is_array($dispositivos_tipo_finalidade_to_string))
        {
            $values = TipoFinalidade::where('id', 'in', $dispositivos_tipo_finalidade_to_string)->getIndexedArray('id', 'id');
            $this->dispositivos_tipo_finalidade_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_tipo_finalidade_to_string = $dispositivos_tipo_finalidade_to_string;
        }

        $this->vdata['dispositivos_tipo_finalidade_to_string'] = $this->dispositivos_tipo_finalidade_to_string;
    }

    public function get_dispositivos_tipo_finalidade_to_string()
    {
        if(!empty($this->dispositivos_tipo_finalidade_to_string))
        {
            return $this->dispositivos_tipo_finalidade_to_string;
        }
    
        $values = Dispositivos::where('tipo_finalidade_id', '=', $this->id)->getIndexedArray('tipo_finalidade_id','{tipo_finalidade->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(Dispositivos::where('tipo_finalidade_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

