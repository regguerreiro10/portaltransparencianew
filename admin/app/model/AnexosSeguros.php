<?php

class AnexosSeguros extends TRecord
{
    const TABLENAME  = 'anexos_seguros';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private Seguros $seguros;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('seguros_id');
        parent::addAttribute('caminho');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method set_seguros
     * Sample of usage: $var->seguros = $object;
     * @param $object Instance of Seguros
     */
    public function set_seguros(Seguros $object)
    {
        $this->seguros = $object;
        $this->seguros_id = $object->id;
    }

    /**
     * Method get_seguros
     * Sample of usage: $var->seguros->attribute;
     * @returns Seguros instance
     */
    public function get_seguros()
    {
    
        // loads the associated object
        if (empty($this->seguros))
            $this->seguros = new Seguros($this->seguros_id);
    
        // returns the associated object
        return $this->seguros;
    }

    
}

