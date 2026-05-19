<?php

//<fileHeader>
  
//</fileHeader>

class ModeloAno extends TRecord
{
    const TABLENAME  = 'modelo_ano';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    
    private Modelo $modelo;
    
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
        parent::addAttribute('ano');
        parent::addAttribute('modelo_id');
        parent::addAttribute('preco');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_modelo
     * Sample of usage: $var->modelo = $object;
     * @param $object Instance of Modelo
     */
    public function set_modelo(Modelo $object)
    {
        $this->modelo = $object;
        $this->modelo_id = $object->id;
    }
    
    /**
     * Method get_modelo
     * Sample of usage: $var->modelo->attribute;
     * @returns Modelo instance
     */
    public function get_modelo()
    {
        
        // loads the associated object
        if (empty($this->modelo))
            $this->modelo = new Modelo($this->modelo_id);
        
        // returns the associated object
        return $this->modelo;
    }
    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

