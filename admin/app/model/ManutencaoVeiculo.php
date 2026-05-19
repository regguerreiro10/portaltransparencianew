<?php

class ManutencaoVeiculo extends TRecord
{
    const TABLENAME  = 'manutencao_veiculo';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $veiculos;
    private $situacao_veiculo;
    private $oficina;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('datamanutencao');
        parent::addAttribute('valor');
        parent::addAttribute('km');
        parent::addAttribute('obs');
        parent::addAttribute('situacao_veiculo_id');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('servicos_produtos');
        parent::addAttribute('oficina_id');
            
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
    /**
     * Method set_situacao_veiculo
     * Sample of usage: $var->situacao_veiculo = $object;
     * @param $object Instance of SituacaoVeiculo
     */
    public function set_situacao_veiculo(SituacaoVeiculo $object)
    {
        $this->situacao_veiculo = $object;
        $this->situacao_veiculo_id = $object->id;
    }

    /**
     * Method get_situacao_veiculo
     * Sample of usage: $var->situacao_veiculo->attribute;
     * @returns SituacaoVeiculo instance
     */
    public function get_situacao_veiculo()
    {
    
        // loads the associated object
        if (empty($this->situacao_veiculo))
            $this->situacao_veiculo = new SituacaoVeiculo($this->situacao_veiculo_id);
    
        // returns the associated object
        return $this->situacao_veiculo;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_oficina(Pessoa $object)
    {
        $this->oficina = $object;
        $this->oficina_id = $object->id;
    }

    /**
     * Method get_oficina
     * Sample of usage: $var->oficina->attribute;
     * @returns Pessoa instance
     */
    public function get_oficina()
    {
    
        // loads the associated object
        if (empty($this->oficina))
            $this->oficina = new Pessoa($this->oficina_id);
    
        // returns the associated object
        return $this->oficina;
    }

    
}

