<?php

//<fileHeader>
  
//</fileHeader>

class ViewRelatoriopecasveiculos extends TRecord
{
    const TABLENAME  = 'view_relatoriopecasveiculos';
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
        parent::addAttribute('estado_pedido_frotas_id');
        parent::addAttribute('dt_pedido');
        parent::addAttribute('dt_finalizacao');
        parent::addAttribute('data_historico');
        parent::addAttribute('nome_usuario');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('nome_estabelecimento');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('placa');
        parent::addAttribute('marca');
        parent::addAttribute('modelo');
        parent::addAttribute('km');
        parent::addAttribute('tipo');
        parent::addAttribute('valor_unitario_produto');
        parent::addAttribute('valor_unitario_servico');
        parent::addAttribute('qtde_produto');
        parent::addAttribute('qtde_servico');
        parent::addAttribute('perc_desconto_produto');
        parent::addAttribute('perc_desconto_servico');
        parent::addAttribute('valor_total_produto');
        parent::addAttribute('valor_total_servico');
        parent::addAttribute('km_garantia_produto');
        parent::addAttribute('km_garantia_servico');
        parent::addAttribute('dias_garantia_produto');
        parent::addAttribute('dias_garantia_servico');
        parent::addAttribute('desconto_contratual');
        parent::addAttribute('nome_produto_servico');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
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

