<?php

//<fileHeader>

//</fileHeader>

class ItensPedidoFrotas extends TRecord
{
    const TABLENAME  = 'itens_pedido_frotas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    const SERVICO = 1;
    const PRODUTO = 2;
    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private PedidoFrotas $pedido_frotas;
    
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
        parent::addAttribute('pedido_frotas_id');
        parent::addAttribute('tipo');
        parent::addAttribute('descricao');
        parent::addAttribute('qtde');
        parent::addAttribute('valor_unitario');
        parent::addAttribute('valor_desconto');
        parent::addAttribute('valor_total'); 
        parent::addAttribute('marca_modelo');
        parent::addAttribute('fabricante');
        parent::addAttribute('codigo');
        parent::addAttribute('qtdekmgarantia');
        parent::addAttribute('diasdegarantia');
        parent::addAttribute('qtdehoras');
        parent::addAttribute('perc_desconto');
        parent::addAttribute('produto_id');
        parent::addAttribute('deleted_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('created_at');
        parent::addAttribute('tbo_horas');
        parent::addAttribute('tbo_ciclos');
        parent::addAttribute('tsn_horas');
        parent::addAttribute('tso_horas');
        parent::addAttribute('csn_ciclos');
        parent::addAttribute('cso_ciclos');
        parent::addAttribute('familia_produto_id');
        parent::addAttribute('uso');
        parent::addAttribute('finalidade');
        parent::addAttribute('aplicacao');
               //<onAfterConstruct>
               //<onAfterConstruct>

        //</onAfterConstruct>
    }
 /**
     * Method set_familia_produto
     * Sample of usage: $var->familia_produto = $object;
     * @param $object Instance of FamiliaProduto
     */
    public function set_familia_produto(FamiliaProduto $object)
    {
        $this->familia_produto = $object;
        $this->familia_produto_id = $object->id;
    }
    
    /**
     * Method get_familia_produto
     * Sample of usage: $var->familia_produto->attribute;
     * @returns FamiliaProduto instance
     */
    public function get_familia_produto()
    {
        
        // loads the associated object
        if (empty($this->familia_produto))
            $this->familia_produto = new FamiliaProduto($this->familia_produto_id);
        
        // returns the associated object
        return $this->familia_produto;
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
    /**
     * Method getItensPropostass
     */
    public function getItensPropostass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('itens_pedido_frotas_id', '=', $this->id));
        return ItensPropostas::getObjects( $criteria );
    }

    
    public function set_itens_propostas_propostas_to_string($itens_propostas_propostas_to_string)
    {
        if(is_array($itens_propostas_propostas_to_string))
        {
            $values = Propostas::where('id', 'in', $itens_propostas_propostas_to_string)->getIndexedArray('id', 'id');
            $this->itens_propostas_propostas_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_propostas_propostas_to_string = $itens_propostas_propostas_to_string;
        }

        $this->vdata['itens_propostas_propostas_to_string'] = $this->itens_propostas_propostas_to_string;
    }

    public function get_itens_propostas_propostas_to_string()
    {
        if(!empty($this->itens_propostas_propostas_to_string))
        {
            return $this->itens_propostas_propostas_to_string;
        }
        
        $values = ItensPropostas::where('itens_pedido_frotas_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
        return implode(', ', $values);
    }

    
    public function set_itens_propostas_itens_pedido_frotas_to_string($itens_propostas_itens_pedido_frotas_to_string)
    {
        if(is_array($itens_propostas_itens_pedido_frotas_to_string))
        {
            $values = ItensPedidoFrotas::where('id', 'in', $itens_propostas_itens_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->itens_propostas_itens_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_propostas_itens_pedido_frotas_to_string = $itens_propostas_itens_pedido_frotas_to_string;
        }

        $this->vdata['itens_propostas_itens_pedido_frotas_to_string'] = $this->itens_propostas_itens_pedido_frotas_to_string;
    }

    public function get_itens_propostas_itens_pedido_frotas_to_string()
    {
        if(!empty($this->itens_propostas_itens_pedido_frotas_to_string))
        {
            return $this->itens_propostas_itens_pedido_frotas_to_string;
        }
        
        $values = ItensPropostas::where('itens_pedido_frotas_id', '=', $this->id)->getIndexedArray('itens_pedido_frotas_id','{itens_pedido_frotas->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
        //<onBeforeDeleteCode>

        //</onBeforeDeleteCode>

        if(ItensPropostas::where('itens_pedido_frotas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
    }
    
    //<userCustomFunctions>

    //</userCustomFunctions>
}
