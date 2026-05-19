<?php

class ManutencaoGarantia extends TRecord
{
    const TABLENAME  = 'manutencao_garantia';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private $pedido_frotas;
    private $propostas;
    private $itens_propostas;
    private $veiculos;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('itens_propostas_id');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('pedido_frotas_id');
        parent::addAttribute('propostas_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('tipo');
        parent::addAttribute('km_manutencao');
        parent::addAttribute('dias_garantia');
        parent::addAttribute('datagarantia');
        parent::addAttribute('descricao');
        parent::addAttribute('obs');
        parent::addAttribute('ativo');
        parent::addAttribute('qtde');
        parent::addAttribute('ciclos_manutencao');
        parent::addAttribute('tbo_horas');
        parent::addAttribute('tbo_ciclos');
        parent::addAttribute('tsn_horas');
        parent::addAttribute('tso_horas');
        parent::addAttribute('csn_ciclos');
        parent::addAttribute('cso_ciclos');
            
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
     * Method set_itens_propostas
     * Sample of usage: $var->itens_propostas = $object;
     * @param $object Instance of ItensPropostas
     */
    public function set_itens_propostas(ItensPropostas $object)
    {
        $this->itens_propostas = $object;
        $this->itens_propostas_id = $object->id;
    }

    /**
     * Method get_itens_propostas
     * Sample of usage: $var->itens_propostas->attribute;
     * @returns ItensPropostas instance
     */
    public function get_itens_propostas()
    {
    
        // loads the associated object
        if (empty($this->itens_propostas))
            $this->itens_propostas = new ItensPropostas($this->itens_propostas_id);
    
        // returns the associated object
        return $this->itens_propostas;
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
     * Method set_produto
     * Sample of usage: $var->produto = $object;
     * @param $object Instance of Produto
     */
    public function set_produto(Produto $object)
    {
        $this->produto = $object;
        $this->produto_id = $object->id;
    }
    
    /**
     * Method get_produto
     * Sample of usage: $var->produto->attribute;
     * @returns Produto instance
     */
    public function get_produto()
    {
        
        // loads the associated object
        if (empty($this->produto))
            $this->produto = new Produto($this->produto_id);
        
        // returns the associated object
        return $this->produto;
    }

    
}

