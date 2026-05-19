<?php

class SeguimentoPessoa extends TRecord
{
    const TABLENAME  = 'seguimento_pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $seguimento;
    private $pessoa;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('seguimento_id');
        parent::addAttribute('pessoa_id');
            
    }

    /**
     * Method set_seguimento
     * Sample of usage: $var->seguimento = $object;
     * @param $object Instance of Seguimento
     */
    public function set_seguimento(Seguimento $object)
    {
        $this->seguimento = $object;
        $this->seguimento_id = $object->id;
    }

    /**
     * Method get_seguimento
     * Sample of usage: $var->seguimento->attribute;
     * @returns Seguimento instance
     */
    public function get_seguimento()
    {
    
        // loads the associated object
        if (empty($this->seguimento))
            $this->seguimento = new Seguimento($this->seguimento_id);
    
        // returns the associated object
        return $this->seguimento;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;
    }

    /**
     * Method get_pessoa
     * Sample of usage: $var->pessoa->attribute;
     * @returns Pessoa instance
     */
    public function get_pessoa()
    {
    
        // loads the associated object
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->pessoa_id);
    
        // returns the associated object
        return $this->pessoa;
    }

    
}

