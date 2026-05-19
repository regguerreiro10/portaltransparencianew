<?php

//<fileHeader>
  
//</fileHeader>

class ViewRelatoriomanutencao extends TRecord
{
    const TABLENAME  = 'view_relatoriomanutencao';
    const PRIMARYKEY = 'pedido_frotas_id';
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
        parent::addAttribute('propostas_id');
        parent::addAttribute('dt_pedido');
        parent::addAttribute('estado_pedido_id');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('km');
        parent::addAttribute('valor_total_pedido');
        parent::addAttribute('valor_total_proposta');
        parent::addAttribute('valor_desconto_proposta');
        parent::addAttribute('valor_liquido_proposta');
        parent::addAttribute('system_user_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('entidade_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('estado_proposta_id');
        parent::addAttribute('motorista_entrada_id');
        parent::addAttribute('motorista_retirada_id');
        parent::addAttribute('valor_totalp');
        parent::addAttribute('valor_descontop');
        parent::addAttribute('valor_liquidop');
        parent::addAttribute('data_entrada_veiculo');
        parent::addAttribute('data_retirada_veiculo');
        parent::addAttribute('data_previsao_entrega');
        parent::addAttribute('datahora_inicioservico');
        parent::addAttribute('datahora_fimservico');
        parent::addAttribute('qtd_servico');
        parent::addAttribute('valor_servico');
        parent::addAttribute('perc_desc_servico');
        parent::addAttribute('valor_total_servico');
        parent::addAttribute('qtd_produto');
        parent::addAttribute('valor_produto');
        parent::addAttribute('perc_desc_produto');
        parent::addAttribute('valor_total_produto');
        parent::addAttribute('unidade');
        parent::addAttribute('placa');
        parent::addAttribute('marca_id');
        parent::addAttribute('marca');
        parent::addAttribute('modelo_id');
        parent::addAttribute('modelo');
<<<<<<< HEAD
=======
        // parent::addAttribute('tipo_veiculo_id');
>>>>>>> 1041c3c42843d5b022faa009e777874f318b1d7f
        parent::addAttribute('anof');
        parent::addAttribute('anom');
        parent::addAttribute('nomepessoa');
        parent::addAttribute('cidade_id');
        parent::addAttribute('cidade');
        parent::addAttribute('estado_id');
        parent::addAttribute('estado');
        parent::addAttribute('cnpj');
        parent::addAttribute('descricaopedido');
        parent::addAttribute('tipo_manutencao_id');
        parent::addAttribute('tipomanutencao');
        parent::addAttribute('nomeaprovador');
                parent::addAttribute('system_unit_id');
<<<<<<< HEAD
=======
        // parent::addAttribute('idaprovador');
>>>>>>> 1041c3c42843d5b022faa009e777874f318b1d7f

        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }
/**
     * Method set_system_unit
     * Sample of usage: $var->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $var->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
        
        // returns the associated object
        return $this->system_unit;
    }
    /**
     * Method set_departamento_unit
     * Sample of usage: $var->departamento_unit = $object;
     * @param $object Instance of DepartamentoUnit
     */
    public function set_departamento_unit(DepartamentoUnit $object)
    {
        $this->departamento_unit = $object;
        $this->departamento_unit_id = $object->id;
    }
    
    /**
     * Method get_departamento_unit
     * Sample of usage: $var->departamento_unit->attribute;
     * @returns DepartamentoUnit instance
     */
    public function get_departamento_unit()
    {
        
        // loads the associated object
        if (empty($this->departamento_unit))
            $this->departamento_unit = new DepartamentoUnit($this->departamento_unit_id);
        
        // returns the associated object
        return $this->departamento_unit;
    }
    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

