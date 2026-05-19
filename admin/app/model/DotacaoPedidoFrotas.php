<?php

//<fileHeader>
  
//</fileHeader>

class DotacaoPedidoFrotas extends TRecord
{
    const TABLENAME  = 'dotacao_pedido_frotas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private Propostas $propostas;
    private PedidoFrotas $pedido_frotas;
    private SaldoDepartamento $saldo_departamento;
    
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
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('pedido_frotas_id');
        parent::addAttribute('saldo_departamento_id');
        parent::addAttribute('valor');
        parent::addAttribute('saldo_atual');
        parent::addAttribute('propostas_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_propostas
     * Sample of usage: $var->propostas = $object;
     * @param $object Instance of Propostas
     */
    public function set_propostas(Propostas $object)
    {
        $this->propostas = $object;
        $this->propostas_id = $object->id;
    }
    
    /**
     * Method get_propostas
     * Sample of usage: $var->propostas->attribute;
     * @returns Propostas instance
     */
    public function get_propostas()
    {
        
        // loads the associated object
        if (empty($this->propostas))
            $this->propostas = new Propostas($this->propostas_id);
        
        // returns the associated object
        return $this->propostas;
    }
    /**
     * Method set_pedido_frotas
     * Sample of usage: $var->pedido_frotas = $object;
     * @param $object Instance of PedidoFrotas
     */
    public function set_pedido_frotas(PedidoFrotas $object)
    {
        $this->pedido_frotas = $object;
        $this->pedido_frotas_id = $object->id;
    }
    
    /**
     * Method get_pedido_frotas
     * Sample of usage: $var->pedido_frotas->attribute;
     * @returns PedidoFrotas instance
     */
    public function get_pedido_frotas()
    {
        
        // loads the associated object
        if (empty($this->pedido_frotas))
            $this->pedido_frotas = new PedidoFrotas($this->pedido_frotas_id);
        
        // returns the associated object
        return $this->pedido_frotas;
    }
    /**
     * Method set_saldo_departamento
     * Sample of usage: $var->saldo_departamento = $object;
     * @param $object Instance of SaldoDepartamento
     */
    public function set_saldo_departamento(SaldoDepartamento $object)
    {
        $this->saldo_departamento = $object;
        $this->saldo_departamento_id = $object->id;
    }
    
    /**
     * Method get_saldo_departamento
     * Sample of usage: $var->saldo_departamento->attribute;
     * @returns SaldoDepartamento instance
     */
    public function get_saldo_departamento()
    {
        
        // loads the associated object
        if (empty($this->saldo_departamento))
            $this->saldo_departamento = new SaldoDepartamento($this->saldo_departamento_id);
        
        // returns the associated object
        return $this->saldo_departamento;
    }
    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

