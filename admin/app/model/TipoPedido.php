<?php

class TipoPedido extends TRecord
{
    const TABLENAME  = 'tipo_pedido';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $categoria;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('categoria_id');
        parent::addAttribute('nome');
            
    }

    /**
     * Method set_categoria
     * Sample of usage: $var->categoria = $object;
     * @param $object Instance of Categoria
     */
    public function set_categoria(Categoria $object)
    {
        $this->categoria = $object;
        $this->categoria_id = $object->id;
    }

    /**
     * Method get_categoria
     * Sample of usage: $var->categoria->attribute;
     * @returns Categoria instance
     */
    public function get_categoria()
    {
    
        // loads the associated object
        if (empty($this->categoria))
            $this->categoria = new Categoria($this->categoria_id);
    
        // returns the associated object
        return $this->categoria;
    }

    /**
     * Method getPedidos
     */
    public function getPedidos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_pedido_id', '=', $this->id));
        return Pedido::getObjects( $criteria );
    }
    /**
     * Method getPedidoVendas
     */
    public function getPedidoVendas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_pedido_id', '=', $this->id));
        return PedidoVenda::getObjects( $criteria );
    }

    public function set_pedido_tipo_pedido_to_string($pedido_tipo_pedido_to_string)
    {
        if(is_array($pedido_tipo_pedido_to_string))
        {
            $values = TipoPedido::where('id', 'in', $pedido_tipo_pedido_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_tipo_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_tipo_pedido_to_string = $pedido_tipo_pedido_to_string;
        }

        $this->vdata['pedido_tipo_pedido_to_string'] = $this->pedido_tipo_pedido_to_string;
    }

    public function get_pedido_tipo_pedido_to_string()
    {
        if(!empty($this->pedido_tipo_pedido_to_string))
        {
            return $this->pedido_tipo_pedido_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('tipo_pedido_id','{tipo_pedido->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_cliente_to_string($pedido_cliente_to_string)
    {
        if(is_array($pedido_cliente_to_string))
        {
            $values = Pessoa::where('id', 'in', $pedido_cliente_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_cliente_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_cliente_to_string = $pedido_cliente_to_string;
        }

        $this->vdata['pedido_cliente_to_string'] = $this->pedido_cliente_to_string;
    }

    public function get_pedido_cliente_to_string()
    {
        if(!empty($this->pedido_cliente_to_string))
        {
            return $this->pedido_cliente_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_vendedor_to_string($pedido_vendedor_to_string)
    {
        if(is_array($pedido_vendedor_to_string))
        {
            $values = Pessoa::where('id', 'in', $pedido_vendedor_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_vendedor_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_vendedor_to_string = $pedido_vendedor_to_string;
        }

        $this->vdata['pedido_vendedor_to_string'] = $this->pedido_vendedor_to_string;
    }

    public function get_pedido_vendedor_to_string()
    {
        if(!empty($this->pedido_vendedor_to_string))
        {
            return $this->pedido_vendedor_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_estado_pedido_venda_to_string($pedido_estado_pedido_venda_to_string)
    {
        if(is_array($pedido_estado_pedido_venda_to_string))
        {
            $values = EstadoPedido::where('id', 'in', $pedido_estado_pedido_venda_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_estado_pedido_venda_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_estado_pedido_venda_to_string = $pedido_estado_pedido_venda_to_string;
        }

        $this->vdata['pedido_estado_pedido_venda_to_string'] = $this->pedido_estado_pedido_venda_to_string;
    }

    public function get_pedido_estado_pedido_venda_to_string()
    {
        if(!empty($this->pedido_estado_pedido_venda_to_string))
        {
            return $this->pedido_estado_pedido_venda_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('estado_pedido_venda_id','{estado_pedido_venda->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_condicao_pagamento_to_string($pedido_condicao_pagamento_to_string)
    {
        if(is_array($pedido_condicao_pagamento_to_string))
        {
            $values = CondicaoPagamento::where('id', 'in', $pedido_condicao_pagamento_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_condicao_pagamento_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_condicao_pagamento_to_string = $pedido_condicao_pagamento_to_string;
        }

        $this->vdata['pedido_condicao_pagamento_to_string'] = $this->pedido_condicao_pagamento_to_string;
    }

    public function get_pedido_condicao_pagamento_to_string()
    {
        if(!empty($this->pedido_condicao_pagamento_to_string))
        {
            return $this->pedido_condicao_pagamento_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('condicao_pagamento_id','{condicao_pagamento->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_transportadora_to_string($pedido_transportadora_to_string)
    {
        if(is_array($pedido_transportadora_to_string))
        {
            $values = Pessoa::where('id', 'in', $pedido_transportadora_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_transportadora_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_transportadora_to_string = $pedido_transportadora_to_string;
        }

        $this->vdata['pedido_transportadora_to_string'] = $this->pedido_transportadora_to_string;
    }

    public function get_pedido_transportadora_to_string()
    {
        if(!empty($this->pedido_transportadora_to_string))
        {
            return $this->pedido_transportadora_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('transportadora_id','{transportadora->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_negociacao_to_string($pedido_negociacao_to_string)
    {
        if(is_array($pedido_negociacao_to_string))
        {
            $values = Negociacao::where('id', 'in', $pedido_negociacao_to_string)->getIndexedArray('id', 'id');
            $this->pedido_negociacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_negociacao_to_string = $pedido_negociacao_to_string;
        }

        $this->vdata['pedido_negociacao_to_string'] = $this->pedido_negociacao_to_string;
    }

    public function get_pedido_negociacao_to_string()
    {
        if(!empty($this->pedido_negociacao_to_string))
        {
            return $this->pedido_negociacao_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('negociacao_id','{negociacao->id}');
        return implode(', ', $values);
    }

    public function set_pedido_situacao_pedido_to_string($pedido_situacao_pedido_to_string)
    {
        if(is_array($pedido_situacao_pedido_to_string))
        {
            $values = EstadoPedido::where('id', 'in', $pedido_situacao_pedido_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_situacao_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_situacao_pedido_to_string = $pedido_situacao_pedido_to_string;
        }

        $this->vdata['pedido_situacao_pedido_to_string'] = $this->pedido_situacao_pedido_to_string;
    }

    public function get_pedido_situacao_pedido_to_string()
    {
        if(!empty($this->pedido_situacao_pedido_to_string))
        {
            return $this->pedido_situacao_pedido_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('situacao_pedido_id','{situacao_pedido->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_departamento_unit_to_string($pedido_departamento_unit_to_string)
    {
        if(is_array($pedido_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $pedido_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->pedido_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_departamento_unit_to_string = $pedido_departamento_unit_to_string;
        }

        $this->vdata['pedido_departamento_unit_to_string'] = $this->pedido_departamento_unit_to_string;
    }

    public function get_pedido_departamento_unit_to_string()
    {
        if(!empty($this->pedido_departamento_unit_to_string))
        {
            return $this->pedido_departamento_unit_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    public function set_pedido_centrocusto_to_string($pedido_centrocusto_to_string)
    {
        if(is_array($pedido_centrocusto_to_string))
        {
            $values = Centrocusto::where('id', 'in', $pedido_centrocusto_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_centrocusto_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_centrocusto_to_string = $pedido_centrocusto_to_string;
        }

        $this->vdata['pedido_centrocusto_to_string'] = $this->pedido_centrocusto_to_string;
    }

    public function get_pedido_centrocusto_to_string()
    {
        if(!empty($this->pedido_centrocusto_to_string))
        {
            return $this->pedido_centrocusto_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('centrocusto_id','{centrocusto->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_system_users_to_string($pedido_system_users_to_string)
    {
        if(is_array($pedido_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $pedido_system_users_to_string)->getIndexedArray('name', 'name');
            $this->pedido_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_system_users_to_string = $pedido_system_users_to_string;
        }

        $this->vdata['pedido_system_users_to_string'] = $this->pedido_system_users_to_string;
    }

    public function get_pedido_system_users_to_string()
    {
        if(!empty($this->pedido_system_users_to_string))
        {
            return $this->pedido_system_users_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_pedido_cartao_to_string($pedido_cartao_to_string)
    {
        if(is_array($pedido_cartao_to_string))
        {
            $values = Cartao::where('id', 'in', $pedido_cartao_to_string)->getIndexedArray('id', 'id');
            $this->pedido_cartao_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_cartao_to_string = $pedido_cartao_to_string;
        }

        $this->vdata['pedido_cartao_to_string'] = $this->pedido_cartao_to_string;
    }

    public function get_pedido_cartao_to_string()
    {
        if(!empty($this->pedido_cartao_to_string))
        {
            return $this->pedido_cartao_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('cartao_id','{cartao->id}');
        return implode(', ', $values);
    }

    public function set_pedido_veiculos_to_string($pedido_veiculos_to_string)
    {
        if(is_array($pedido_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $pedido_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->pedido_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_veiculos_to_string = $pedido_veiculos_to_string;
        }

        $this->vdata['pedido_veiculos_to_string'] = $this->pedido_veiculos_to_string;
    }

    public function get_pedido_veiculos_to_string()
    {
        if(!empty($this->pedido_veiculos_to_string))
        {
            return $this->pedido_veiculos_to_string;
        }
    
        $values = Pedido::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    public function set_pedido_venda_tipo_pedido_to_string($pedido_venda_tipo_pedido_to_string)
    {
        if(is_array($pedido_venda_tipo_pedido_to_string))
        {
            $values = TipoPedido::where('id', 'in', $pedido_venda_tipo_pedido_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_venda_tipo_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_venda_tipo_pedido_to_string = $pedido_venda_tipo_pedido_to_string;
        }

        $this->vdata['pedido_venda_tipo_pedido_to_string'] = $this->pedido_venda_tipo_pedido_to_string;
    }

    public function get_pedido_venda_tipo_pedido_to_string()
    {
        if(!empty($this->pedido_venda_tipo_pedido_to_string))
        {
            return $this->pedido_venda_tipo_pedido_to_string;
        }
    
        $values = PedidoVenda::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('tipo_pedido_id','{tipo_pedido->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_venda_cliente_to_string($pedido_venda_cliente_to_string)
    {
        if(is_array($pedido_venda_cliente_to_string))
        {
            $values = Pessoa::where('id', 'in', $pedido_venda_cliente_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_venda_cliente_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_venda_cliente_to_string = $pedido_venda_cliente_to_string;
        }

        $this->vdata['pedido_venda_cliente_to_string'] = $this->pedido_venda_cliente_to_string;
    }

    public function get_pedido_venda_cliente_to_string()
    {
        if(!empty($this->pedido_venda_cliente_to_string))
        {
            return $this->pedido_venda_cliente_to_string;
        }
    
        $values = PedidoVenda::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_venda_vendedor_to_string($pedido_venda_vendedor_to_string)
    {
        if(is_array($pedido_venda_vendedor_to_string))
        {
            $values = Pessoa::where('id', 'in', $pedido_venda_vendedor_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_venda_vendedor_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_venda_vendedor_to_string = $pedido_venda_vendedor_to_string;
        }

        $this->vdata['pedido_venda_vendedor_to_string'] = $this->pedido_venda_vendedor_to_string;
    }

    public function get_pedido_venda_vendedor_to_string()
    {
        if(!empty($this->pedido_venda_vendedor_to_string))
        {
            return $this->pedido_venda_vendedor_to_string;
        }
    
        $values = PedidoVenda::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_venda_estado_pedido_venda_to_string($pedido_venda_estado_pedido_venda_to_string)
    {
        if(is_array($pedido_venda_estado_pedido_venda_to_string))
        {
            $values = EstadoPedidoVenda::where('id', 'in', $pedido_venda_estado_pedido_venda_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_venda_estado_pedido_venda_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_venda_estado_pedido_venda_to_string = $pedido_venda_estado_pedido_venda_to_string;
        }

        $this->vdata['pedido_venda_estado_pedido_venda_to_string'] = $this->pedido_venda_estado_pedido_venda_to_string;
    }

    public function get_pedido_venda_estado_pedido_venda_to_string()
    {
        if(!empty($this->pedido_venda_estado_pedido_venda_to_string))
        {
            return $this->pedido_venda_estado_pedido_venda_to_string;
        }
    
        $values = PedidoVenda::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('estado_pedido_venda_id','{estado_pedido_venda->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_venda_condicao_pagamento_to_string($pedido_venda_condicao_pagamento_to_string)
    {
        if(is_array($pedido_venda_condicao_pagamento_to_string))
        {
            $values = CondicaoPagamento::where('id', 'in', $pedido_venda_condicao_pagamento_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_venda_condicao_pagamento_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_venda_condicao_pagamento_to_string = $pedido_venda_condicao_pagamento_to_string;
        }

        $this->vdata['pedido_venda_condicao_pagamento_to_string'] = $this->pedido_venda_condicao_pagamento_to_string;
    }

    public function get_pedido_venda_condicao_pagamento_to_string()
    {
        if(!empty($this->pedido_venda_condicao_pagamento_to_string))
        {
            return $this->pedido_venda_condicao_pagamento_to_string;
        }
    
        $values = PedidoVenda::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('condicao_pagamento_id','{condicao_pagamento->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_venda_transportadora_to_string($pedido_venda_transportadora_to_string)
    {
        if(is_array($pedido_venda_transportadora_to_string))
        {
            $values = Pessoa::where('id', 'in', $pedido_venda_transportadora_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_venda_transportadora_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_venda_transportadora_to_string = $pedido_venda_transportadora_to_string;
        }

        $this->vdata['pedido_venda_transportadora_to_string'] = $this->pedido_venda_transportadora_to_string;
    }

    public function get_pedido_venda_transportadora_to_string()
    {
        if(!empty($this->pedido_venda_transportadora_to_string))
        {
            return $this->pedido_venda_transportadora_to_string;
        }
    
        $values = PedidoVenda::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('transportadora_id','{transportadora->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_venda_negociacao_to_string($pedido_venda_negociacao_to_string)
    {
        if(is_array($pedido_venda_negociacao_to_string))
        {
            $values = Negociacao::where('id', 'in', $pedido_venda_negociacao_to_string)->getIndexedArray('id', 'id');
            $this->pedido_venda_negociacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_venda_negociacao_to_string = $pedido_venda_negociacao_to_string;
        }

        $this->vdata['pedido_venda_negociacao_to_string'] = $this->pedido_venda_negociacao_to_string;
    }

    public function get_pedido_venda_negociacao_to_string()
    {
        if(!empty($this->pedido_venda_negociacao_to_string))
        {
            return $this->pedido_venda_negociacao_to_string;
        }
    
        $values = PedidoVenda::where('tipo_pedido_id', '=', $this->id)->getIndexedArray('negociacao_id','{negociacao->id}');
        return implode(', ', $values);
    }

    
}

