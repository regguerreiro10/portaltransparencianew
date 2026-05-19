<?php

class Seguimento extends TRecord
{
    const TABLENAME  = 'seguimento';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('idold');
            
    }

    /**
     * Method getPedidoSeguimentos
     */
    public function getPedidoSeguimentos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('seguimento_id', '=', $this->id));
        return PedidoSeguimento::getObjects( $criteria );
    }
    /**
     * Method getSeguimentoPessoas
     */
    public function getSeguimentoPessoas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('seguimento_id', '=', $this->id));
        return SeguimentoPessoa::getObjects( $criteria );
    }

    public function set_pedido_seguimento_pedido_to_string($pedido_seguimento_pedido_to_string)
    {
        if(is_array($pedido_seguimento_pedido_to_string))
        {
            $values = Pedido::where('id', 'in', $pedido_seguimento_pedido_to_string)->getIndexedArray('id', 'id');
            $this->pedido_seguimento_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_seguimento_pedido_to_string = $pedido_seguimento_pedido_to_string;
        }

        $this->vdata['pedido_seguimento_pedido_to_string'] = $this->pedido_seguimento_pedido_to_string;
    }

    public function get_pedido_seguimento_pedido_to_string()
    {
        if(!empty($this->pedido_seguimento_pedido_to_string))
        {
            return $this->pedido_seguimento_pedido_to_string;
        }
    
        $values = PedidoSeguimento::where('seguimento_id', '=', $this->id)->getIndexedArray('pedido_id','{pedido->id}');
        return implode(', ', $values);
    }

    public function set_pedido_seguimento_seguimento_to_string($pedido_seguimento_seguimento_to_string)
    {
        if(is_array($pedido_seguimento_seguimento_to_string))
        {
            $values = Seguimento::where('id', 'in', $pedido_seguimento_seguimento_to_string)->getIndexedArray('id', 'id');
            $this->pedido_seguimento_seguimento_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_seguimento_seguimento_to_string = $pedido_seguimento_seguimento_to_string;
        }

        $this->vdata['pedido_seguimento_seguimento_to_string'] = $this->pedido_seguimento_seguimento_to_string;
    }

    public function get_pedido_seguimento_seguimento_to_string()
    {
        if(!empty($this->pedido_seguimento_seguimento_to_string))
        {
            return $this->pedido_seguimento_seguimento_to_string;
        }
    
        $values = PedidoSeguimento::where('seguimento_id', '=', $this->id)->getIndexedArray('seguimento_id','{seguimento->id}');
        return implode(', ', $values);
    }

    public function set_seguimento_pessoa_seguimento_to_string($seguimento_pessoa_seguimento_to_string)
    {
        if(is_array($seguimento_pessoa_seguimento_to_string))
        {
            $values = Seguimento::where('id', 'in', $seguimento_pessoa_seguimento_to_string)->getIndexedArray('id', 'id');
            $this->seguimento_pessoa_seguimento_to_string = implode(', ', $values);
        }
        else
        {
            $this->seguimento_pessoa_seguimento_to_string = $seguimento_pessoa_seguimento_to_string;
        }

        $this->vdata['seguimento_pessoa_seguimento_to_string'] = $this->seguimento_pessoa_seguimento_to_string;
    }

    public function get_seguimento_pessoa_seguimento_to_string()
    {
        if(!empty($this->seguimento_pessoa_seguimento_to_string))
        {
            return $this->seguimento_pessoa_seguimento_to_string;
        }
    
        $values = SeguimentoPessoa::where('seguimento_id', '=', $this->id)->getIndexedArray('seguimento_id','{seguimento->id}');
        return implode(', ', $values);
    }

    public function set_seguimento_pessoa_pessoa_to_string($seguimento_pessoa_pessoa_to_string)
    {
        if(is_array($seguimento_pessoa_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $seguimento_pessoa_pessoa_to_string)->getIndexedArray('nome', 'nome');
            $this->seguimento_pessoa_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->seguimento_pessoa_pessoa_to_string = $seguimento_pessoa_pessoa_to_string;
        }

        $this->vdata['seguimento_pessoa_pessoa_to_string'] = $this->seguimento_pessoa_pessoa_to_string;
    }

    public function get_seguimento_pessoa_pessoa_to_string()
    {
        if(!empty($this->seguimento_pessoa_pessoa_to_string))
        {
            return $this->seguimento_pessoa_pessoa_to_string;
        }
    
        $values = SeguimentoPessoa::where('seguimento_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(PedidoSeguimento::where('seguimento_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(SeguimentoPessoa::where('seguimento_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

