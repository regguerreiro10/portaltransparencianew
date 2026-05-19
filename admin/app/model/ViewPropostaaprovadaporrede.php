<?php

class ViewPropostaaprovadaporrede extends TRecord
{
    const TABLENAME  = 'view_propostaaprovadaporrede';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('pedido_frotas_id');
        parent::addAttribute('estado_pedido_frotas_id');
        parent::addAttribute('motorista_entrada_id');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('data_cotacao');
        parent::addAttribute('obs');
        parent::addAttribute('valor_total');
        parent::addAttribute('valor_desconto');
        parent::addAttribute('valor_liquido');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('data_entrada_veiculo');
        parent::addAttribute('data_retirada_veiculo');
        parent::addAttribute('data_previsao_entrega');
        parent::addAttribute('motorista_retirada_id');
        parent::addAttribute('km');
        parent::addAttribute('created_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('cidade_id');
        parent::addAttribute('total_produtos_sem_desconto');
        parent::addAttribute('total_servicos_sem_desconto');
        parent::addAttribute('total_geral_sem_desconto');
        parent::addAttribute('total_produtos_com_desconto');
        parent::addAttribute('desconto_contratual');
        parent::addAttribute('total_geral_com_desconto');
        parent::addAttribute('responsavel_tecnico');
        parent::addAttribute('datahora_inicioservico');
        parent::addAttribute('datahora_fimservico');
        parent::addAttribute('dt_pedido');
        parent::addAttribute('data_aprovacao');
        parent::addAttribute('nomeaprovador');
        parent::addAttribute('data_autorizacao_pagamento');
        parent::addAttribute('nomeaprovadorpagamento');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('descricaopedido');
        parent::addAttribute('total_servicos_com_desconto');
            
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
     public function set_estado_pedido_frotas(EstadoPedidoFrotas $object)
    {
        $this->estado_pedido_frotas = $object;
        $this->estado_pedido_frotas_id = $object->id;
    }
    
    /**
     * Method get_estado_pedido_frotas
     * Sample of usage: $var->estado_pedido_frotas->attribute;
     * @returns EstadoPedidoFrotas instance
     */
    public function get_estado_pedido_frotas()
    {
        
        // loads the associated object
        if (empty($this->estado_pedido_frotas))
            $this->estado_pedido_frotas = new EstadoPedidoFrotas($this->estado_pedido_frotas_id);
        
        // returns the associated object
        return $this->estado_pedido_frotas;
    }
      /**
     * Method set_niveltanque
     * Sample of usage: $var->niveltanque = $object;
     * @param $object Instance of Niveltanque
     */
    public function set_veiculos(Veiculos $object)
    {
        $this->veiculos = $object;
        $this->veiculos_id = $object->id;
    }
    
    /**
     * Method get_niveltanque
     * Sample of usage: $var->niveltanque->attribute;
     * @returns Niveltanque instance
     */
    public function get_veiculos()
    {
        
        // loads the associated object
        if (empty($this->veiculos))
            $this->veiculos = new Veiculos($this->veiculos_id);
        
        // returns the associated object
        return $this->veiculos;
    }
}

