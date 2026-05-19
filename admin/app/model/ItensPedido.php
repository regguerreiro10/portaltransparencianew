<?php

class ItensPedido extends TRecord
{
    const TABLENAME  = 'itens_pedido';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private Produto $produto;
    private Pedido $pedido_venda;
    private UnidadeMedida $unidade_medida;

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
        parent::addAttribute('pedido_venda_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('quantidade');
        parent::addAttribute('valor');
        parent::addAttribute('desconto');
        parent::addAttribute('valor_total');
        parent::addAttribute('obs');
        parent::addAttribute('valor_cotacao');
        parent::addAttribute('valor_cotacao_total');
        parent::addAttribute('deleted_at');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('unidade_medida_id');

    
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
     * Method set_unidade_medida
     * Sample of usage: $var->unidade_medida = $object;
     * @param $object Instance of UnidadeMedida
     */
    public function set_unidade_medida(UnidadeMedida $object)
    {
        $this->unidade_medida = $object;
        $this->unidade_medida_id = $object->id;
    }
    
    /**
     * Method get_unidade_medida
     * Sample of usage: $var->unidade_medida->attribute;
     * @returns UnidadeMedida instance
     */
    public function get_unidade_medida()
    {
        
        // loads the associated object
        if (empty($this->unidade_medida))
            $this->unidade_medida = new UnidadeMedida($this->unidade_medida_id);
        
        // returns the associated object
        return $this->unidade_medida;
    }
    /**
     * Method set_pedido
     * Sample of usage: $var->pedido = $object;
     * @param $object Instance of Pedido
     */
    public function set_pedido_venda(Pedido $object)
    {
        $this->pedido_venda = $object;
        $this->pedido_venda_id = $object->id;
    }

    /**
     * Method get_pedido_venda
     * Sample of usage: $var->pedido_venda->attribute;
     * @returns Pedido instance
     */
    public function get_pedido_venda()
    {
    
        // loads the associated object
        if (empty($this->pedido_venda))
            $this->pedido_venda = new Pedido($this->pedido_venda_id);
    
        // returns the associated object
        return $this->pedido_venda;
    }
      public function set_itens_cotacao_unidade_medida_to_string($itens_cotacao_unidade_medida_to_string)
    {
        if(is_array($itens_cotacao_unidade_medida_to_string))
        {
            $values = UnidadeMedida::where('id', 'in', $itens_cotacao_unidade_medida_to_string)->getIndexedArray('nome', 'nome');
            $this->itens_cotacao_unidade_medida_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_cotacao_unidade_medida_to_string = $itens_cotacao_unidade_medida_to_string;
        }

        $this->vdata['itens_cotacao_unidade_medida_to_string'] = $this->itens_cotacao_unidade_medida_to_string;
    }

    public function get_itens_cotacao_unidade_medida_to_string()
    {
        if(!empty($this->itens_cotacao_unidade_medida_to_string))
        {
            return $this->itens_cotacao_unidade_medida_to_string;
        }
        
        $values = ItensCotacao::where('itens_pedido_id', '=', $this->id)->getIndexedArray('unidade_medida_id','{unidade_medida->nome}');
        return implode(', ', $values);
    }

    /**
     * Method getNotaFiscalItems
     */
    public function getNotaFiscalItems()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_venda_item_id', '=', $this->id));
        return NotaFiscalItem::getObjects( $criteria );
    }

    public function set_nota_fiscal_item_pedido_venda_item_to_string($nota_fiscal_item_pedido_venda_item_to_string)
    {
        if(is_array($nota_fiscal_item_pedido_venda_item_to_string))
        {
            $values = ItensPedido::where('id', 'in', $nota_fiscal_item_pedido_venda_item_to_string)->getIndexedArray('id', 'id');
            $this->nota_fiscal_item_pedido_venda_item_to_string = implode(', ', $values);
        }
        else
        {
            $this->nota_fiscal_item_pedido_venda_item_to_string = $nota_fiscal_item_pedido_venda_item_to_string;
        }

        $this->vdata['nota_fiscal_item_pedido_venda_item_to_string'] = $this->nota_fiscal_item_pedido_venda_item_to_string;
    }

    public function get_nota_fiscal_item_pedido_venda_item_to_string()
    {
        if(!empty($this->nota_fiscal_item_pedido_venda_item_to_string))
        {
            return $this->nota_fiscal_item_pedido_venda_item_to_string;
        }
    
        $values = NotaFiscalItem::where('pedido_venda_item_id', '=', $this->id)->getIndexedArray('pedido_venda_item_id','{pedido_venda_item->id}');
        return implode(', ', $values);
    }

    public function set_nota_fiscal_item_nota_fiscal_to_string($nota_fiscal_item_nota_fiscal_to_string)
    {
        if(is_array($nota_fiscal_item_nota_fiscal_to_string))
        {
            $values = NotaFiscal::where('id', 'in', $nota_fiscal_item_nota_fiscal_to_string)->getIndexedArray('id', 'id');
            $this->nota_fiscal_item_nota_fiscal_to_string = implode(', ', $values);
        }
        else
        {
            $this->nota_fiscal_item_nota_fiscal_to_string = $nota_fiscal_item_nota_fiscal_to_string;
        }

        $this->vdata['nota_fiscal_item_nota_fiscal_to_string'] = $this->nota_fiscal_item_nota_fiscal_to_string;
    }

    public function get_nota_fiscal_item_nota_fiscal_to_string()
    {
        if(!empty($this->nota_fiscal_item_nota_fiscal_to_string))
        {
            return $this->nota_fiscal_item_nota_fiscal_to_string;
        }
    
        $values = NotaFiscalItem::where('pedido_venda_item_id', '=', $this->id)->getIndexedArray('nota_fiscal_id','{nota_fiscal->id}');
        return implode(', ', $values);
    }

    public function set_nota_fiscal_item_produto_to_string($nota_fiscal_item_produto_to_string)
    {
        if(is_array($nota_fiscal_item_produto_to_string))
        {
            $values = Produto::where('id', 'in', $nota_fiscal_item_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->nota_fiscal_item_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->nota_fiscal_item_produto_to_string = $nota_fiscal_item_produto_to_string;
        }

        $this->vdata['nota_fiscal_item_produto_to_string'] = $this->nota_fiscal_item_produto_to_string;
    }

    public function get_nota_fiscal_item_produto_to_string()
    {
        if(!empty($this->nota_fiscal_item_produto_to_string))
        {
            return $this->nota_fiscal_item_produto_to_string;
        }
    
        $values = NotaFiscalItem::where('pedido_venda_item_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

      /**
     * Method getPedidoVendaItems
     */
    public function getItensPedido()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_venda_id', '=', $this->id));
        return ItensPedido::getObjects( $criteria );
    }

}

