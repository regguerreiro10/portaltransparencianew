<?php

class TipoPecas extends TRecord
{
    const TABLENAME  = 'tipo_pecas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method getItensPropostass
     */
    public function getItensPropostass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_pecas_id', '=', $this->id));
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
    
        $values = ItensPropostas::where('tipo_pecas_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
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
    
        $values = ItensPropostas::where('tipo_pecas_id', '=', $this->id)->getIndexedArray('itens_pedido_frotas_id','{itens_pedido_frotas->id}');
        return implode(', ', $values);
    }

    public function set_itens_propostas_tipo_pecas_to_string($itens_propostas_tipo_pecas_to_string)
    {
        if(is_array($itens_propostas_tipo_pecas_to_string))
        {
            $values = TipoPecas::where('id', 'in', $itens_propostas_tipo_pecas_to_string)->getIndexedArray('id', 'id');
            $this->itens_propostas_tipo_pecas_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_propostas_tipo_pecas_to_string = $itens_propostas_tipo_pecas_to_string;
        }

        $this->vdata['itens_propostas_tipo_pecas_to_string'] = $this->itens_propostas_tipo_pecas_to_string;
    }

    public function get_itens_propostas_tipo_pecas_to_string()
    {
        if(!empty($this->itens_propostas_tipo_pecas_to_string))
        {
            return $this->itens_propostas_tipo_pecas_to_string;
        }
    
        $values = ItensPropostas::where('tipo_pecas_id', '=', $this->id)->getIndexedArray('tipo_pecas_id','{tipo_pecas->id}');
        return implode(', ', $values);
    }

    public function set_itens_propostas_estado_pedido_frotas_to_string($itens_propostas_estado_pedido_frotas_to_string)
    {
        if(is_array($itens_propostas_estado_pedido_frotas_to_string))
        {
            $values = EstadoPedidoFrotas::where('id', 'in', $itens_propostas_estado_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->itens_propostas_estado_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_propostas_estado_pedido_frotas_to_string = $itens_propostas_estado_pedido_frotas_to_string;
        }

        $this->vdata['itens_propostas_estado_pedido_frotas_to_string'] = $this->itens_propostas_estado_pedido_frotas_to_string;
    }

    public function get_itens_propostas_estado_pedido_frotas_to_string()
    {
        if(!empty($this->itens_propostas_estado_pedido_frotas_to_string))
        {
            return $this->itens_propostas_estado_pedido_frotas_to_string;
        }
    
        $values = ItensPropostas::where('tipo_pecas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(ItensPropostas::where('tipo_pecas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

