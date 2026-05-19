<?php

class MultasAnexos extends TRecord
{
    const TABLENAME  = 'multas_anexos';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private Multas $multas;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('multas_id');
        parent::addAttribute('arquivo');
        parent::addAttribute('obs');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method set_multas
     * Sample of usage: $var->multas = $object;
     * @param $object Instance of Multas
     */
    public function set_multas(Multas $object)
    {
        $this->multas = $object;
        $this->multas_id = $object->id;
    }

    /**
     * Method get_multas
     * Sample of usage: $var->multas->attribute;
     * @returns Multas instance
     */
    public function get_multas()
    {
    
        // loads the associated object
        if (empty($this->multas))
            $this->multas = new Multas($this->multas_id);
    
        // returns the associated object
        return $this->multas;
    }

    
}

