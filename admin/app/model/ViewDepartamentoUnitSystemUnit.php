<?php

//<fileHeader>
  
//</fileHeader>

class ViewDepartamentoUnitSystemUnit extends TRecord
{
    const TABLENAME  = 'view_departamento_unit_system_unit';
    const PRIMARYKEY = 'departamento_unit_id';
    const IDPOLICY   =  'max'; // {max, serial}
    //<classProperties>
  
    //</classProperties>
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        //<onBeforeConstruct>
  
        //</onBeforeConstruct>
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_unit_id');
        parent::addAttribute('name_departamento_unit');
        parent::addAttribute('name_system_unit');
        parent::addAttribute('departamento_unit_id');
  
        //</onAfterConstruct>
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
 
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

