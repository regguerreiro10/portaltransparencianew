<?php

//<fileHeader>
  
//</fileHeader>

class SaldoVeiculo extends TRecord
{
    const TABLENAME  = 'saldo_veiculo';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    
    private $veiculos;
    
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
        parent::addAttribute('tipo_transacao');
        parent::addAttribute('system_users_id');
        parent::addAttribute('motivo_transacao');
        parent::addAttribute('data_transacao');
        parent::addAttribute('valor_transacao');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('limite_mensal');
        parent::addAttribute('saldo_disponivel');
        parent::addAttribute('mes_transacao');
        parent::addAttribute('ano_transacao');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
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
    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}



