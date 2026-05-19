<?php

class MovimentoDispositivos extends TRecord
{
    const TABLENAME  = 'movimento_dispositivos';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private DispositivosSolicitados $dispositivos_solicitados;
    private Pessoa $estabelecimento;
    private Pessoa $condutor;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('estabelecimento_id');
        parent::addAttribute('condutor_id');
        parent::addAttribute('dispositivos_solicitados_id');
        parent::addAttribute('datahora');
        parent::addAttribute('qtde');
        parent::addAttribute('valor_unitario');
        parent::addAttribute('valor_total');
        parent::addAttribute('valor_desconto');
        parent::addAttribute('valor_liquido');
        parent::addAttribute('obs');
        parent::addAttribute('localizacao');
        parent::addAttribute('sentidodapassagem');
        parent::addAttribute('operadorapedagio');
        parent::addAttribute('tipodavia');
        parent::addAttribute('idtransacao');
            
    }

    /**
     * Method set_dispositivos_solicitados
     * Sample of usage: $var->dispositivos_solicitados = $object;
     * @param $object Instance of DispositivosSolicitados
     */
    public function set_dispositivos_solicitados(DispositivosSolicitados $object)
    {
        $this->dispositivos_solicitados = $object;
        $this->dispositivos_solicitados_id = $object->id;
    }

    /**
     * Method get_dispositivos_solicitados
     * Sample of usage: $var->dispositivos_solicitados->attribute;
     * @returns DispositivosSolicitados instance
     */
    public function get_dispositivos_solicitados()
    {
    
        // loads the associated object
        if (empty($this->dispositivos_solicitados))
            $this->dispositivos_solicitados = new DispositivosSolicitados($this->dispositivos_solicitados_id);
    
        // returns the associated object
        return $this->dispositivos_solicitados;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_estabelecimento(Pessoa $object)
    {
        $this->estabelecimento = $object;
        $this->estabelecimento_id = $object->id;
    }

    /**
     * Method get_estabelecimento
     * Sample of usage: $var->estabelecimento->attribute;
     * @returns Pessoa instance
     */
    public function get_estabelecimento()
    {
    
        // loads the associated object
        if (empty($this->estabelecimento))
            $this->estabelecimento = new Pessoa($this->estabelecimento_id);
    
        // returns the associated object
        return $this->estabelecimento;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_condutor(Pessoa $object)
    {
        $this->condutor = $object;
        $this->condutor_id = $object->id;
    }

    /**
     * Method get_condutor
     * Sample of usage: $var->condutor->attribute;
     * @returns Pessoa instance
     */
    public function get_condutor()
    {
    
        // loads the associated object
        if (empty($this->condutor))
            $this->condutor = new Pessoa($this->condutor_id);
    
        // returns the associated object
        return $this->condutor;
    }

    
}

