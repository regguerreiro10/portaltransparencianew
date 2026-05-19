<?php

//<fileHeader>
  
//</fileHeader>

class TipoManutencao extends TRecord
{
    const TABLENAME  = 'tipo_manutencao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    
    
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
        parent::addAttribute('descricao');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    
    /**
     * Method getPedidoFrotass
     */
    public function getPedidoFrotass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_manutencao_id', '=', $this->id));
        return PedidoFrotas::getObjects( $criteria );
    }

    
    public function set_pedido_frotas_estado_pedido_frotas_to_string($pedido_frotas_estado_pedido_frotas_to_string)
    {
        if(is_array($pedido_frotas_estado_pedido_frotas_to_string))
        {
            $values = EstadoPedidoFrotas::where('id', 'in', $pedido_frotas_estado_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->pedido_frotas_estado_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_estado_pedido_frotas_to_string = $pedido_frotas_estado_pedido_frotas_to_string;
        }

        $this->vdata['pedido_frotas_estado_pedido_frotas_to_string'] = $this->pedido_frotas_estado_pedido_frotas_to_string;
    }

    public function get_pedido_frotas_estado_pedido_frotas_to_string()
    {
        if(!empty($this->pedido_frotas_estado_pedido_frotas_to_string))
        {
            return $this->pedido_frotas_estado_pedido_frotas_to_string;
        }
        
        $values = PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_estabelecimento_to_string($pedido_frotas_estabelecimento_to_string)
    {
        if(is_array($pedido_frotas_estabelecimento_to_string))
        {
            $values = Pessoa::where('id', 'in', $pedido_frotas_estabelecimento_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_frotas_estabelecimento_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_estabelecimento_to_string = $pedido_frotas_estabelecimento_to_string;
        }

        $this->vdata['pedido_frotas_estabelecimento_to_string'] = $this->pedido_frotas_estabelecimento_to_string;
    }

    public function get_pedido_frotas_estabelecimento_to_string()
    {
        if(!empty($this->pedido_frotas_estabelecimento_to_string))
        {
            return $this->pedido_frotas_estabelecimento_to_string;
        }
        
        $values = PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->getIndexedArray('estabelecimento_id','{estabelecimento->nome}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_niveltanque_to_string($pedido_frotas_niveltanque_to_string)
    {
        if(is_array($pedido_frotas_niveltanque_to_string))
        {
            $values = Niveltanque::where('id', 'in', $pedido_frotas_niveltanque_to_string)->getIndexedArray('id', 'id');
            $this->pedido_frotas_niveltanque_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_niveltanque_to_string = $pedido_frotas_niveltanque_to_string;
        }

        $this->vdata['pedido_frotas_niveltanque_to_string'] = $this->pedido_frotas_niveltanque_to_string;
    }

    public function get_pedido_frotas_niveltanque_to_string()
    {
        if(!empty($this->pedido_frotas_niveltanque_to_string))
        {
            return $this->pedido_frotas_niveltanque_to_string;
        }
        
        $values = PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->getIndexedArray('niveltanque_id','{niveltanque->id}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_condutor_entrada_to_string($pedido_frotas_condutor_entrada_to_string)
    {
        if(is_array($pedido_frotas_condutor_entrada_to_string))
        {
            $values = Pessoa::where('id', 'in', $pedido_frotas_condutor_entrada_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_frotas_condutor_entrada_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_condutor_entrada_to_string = $pedido_frotas_condutor_entrada_to_string;
        }

        $this->vdata['pedido_frotas_condutor_entrada_to_string'] = $this->pedido_frotas_condutor_entrada_to_string;
    }

    public function get_pedido_frotas_condutor_entrada_to_string()
    {
        if(!empty($this->pedido_frotas_condutor_entrada_to_string))
        {
            return $this->pedido_frotas_condutor_entrada_to_string;
        }
        
        $values = PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->getIndexedArray('condutor_entrada_id','{condutor_entrada->nome}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_condutor_retirada_to_string($pedido_frotas_condutor_retirada_to_string)
    {
        if(is_array($pedido_frotas_condutor_retirada_to_string))
        {
            $values = Pessoa::where('id', 'in', $pedido_frotas_condutor_retirada_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_frotas_condutor_retirada_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_condutor_retirada_to_string = $pedido_frotas_condutor_retirada_to_string;
        }

        $this->vdata['pedido_frotas_condutor_retirada_to_string'] = $this->pedido_frotas_condutor_retirada_to_string;
    }

    public function get_pedido_frotas_condutor_retirada_to_string()
    {
        if(!empty($this->pedido_frotas_condutor_retirada_to_string))
        {
            return $this->pedido_frotas_condutor_retirada_to_string;
        }
        
        $values = PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->getIndexedArray('condutor_retirada_id','{condutor_retirada->nome}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_tipo_manutencao_to_string($pedido_frotas_tipo_manutencao_to_string)
    {
        if(is_array($pedido_frotas_tipo_manutencao_to_string))
        {
            $values = TipoManutencao::where('id', 'in', $pedido_frotas_tipo_manutencao_to_string)->getIndexedArray('id', 'id');
            $this->pedido_frotas_tipo_manutencao_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_tipo_manutencao_to_string = $pedido_frotas_tipo_manutencao_to_string;
        }

        $this->vdata['pedido_frotas_tipo_manutencao_to_string'] = $this->pedido_frotas_tipo_manutencao_to_string;
    }

    public function get_pedido_frotas_tipo_manutencao_to_string()
    {
        if(!empty($this->pedido_frotas_tipo_manutencao_to_string))
        {
            return $this->pedido_frotas_tipo_manutencao_to_string;
        }
        
        $values = PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->getIndexedArray('tipo_manutencao_id','{tipo_manutencao->id}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_negociacao_to_string($pedido_frotas_negociacao_to_string)
    {
        if(is_array($pedido_frotas_negociacao_to_string))
        {
            $values = Negociacao::where('id', 'in', $pedido_frotas_negociacao_to_string)->getIndexedArray('id', 'id');
            $this->pedido_frotas_negociacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_negociacao_to_string = $pedido_frotas_negociacao_to_string;
        }

        $this->vdata['pedido_frotas_negociacao_to_string'] = $this->pedido_frotas_negociacao_to_string;
    }

    public function get_pedido_frotas_negociacao_to_string()
    {
        if(!empty($this->pedido_frotas_negociacao_to_string))
        {
            return $this->pedido_frotas_negociacao_to_string;
        }
        
        $values = PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->getIndexedArray('negociacao_id','{negociacao->id}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_condicao_pagamento_to_string($pedido_frotas_condicao_pagamento_to_string)
    {
        if(is_array($pedido_frotas_condicao_pagamento_to_string))
        {
            $values = CondicaoPagamento::where('id', 'in', $pedido_frotas_condicao_pagamento_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_frotas_condicao_pagamento_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_condicao_pagamento_to_string = $pedido_frotas_condicao_pagamento_to_string;
        }

        $this->vdata['pedido_frotas_condicao_pagamento_to_string'] = $this->pedido_frotas_condicao_pagamento_to_string;
    }

    public function get_pedido_frotas_condicao_pagamento_to_string()
    {
        if(!empty($this->pedido_frotas_condicao_pagamento_to_string))
        {
            return $this->pedido_frotas_condicao_pagamento_to_string;
        }
        
        $values = PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->getIndexedArray('condicao_pagamento_id','{condicao_pagamento->nome}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_system_unit_to_string($pedido_frotas_system_unit_to_string)
    {
        if(is_array($pedido_frotas_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $pedido_frotas_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->pedido_frotas_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_system_unit_to_string = $pedido_frotas_system_unit_to_string;
        }

        $this->vdata['pedido_frotas_system_unit_to_string'] = $this->pedido_frotas_system_unit_to_string;
    }

    public function get_pedido_frotas_system_unit_to_string()
    {
        if(!empty($this->pedido_frotas_system_unit_to_string))
        {
            return $this->pedido_frotas_system_unit_to_string;
        }
        
        $values = PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_departamento_unit_to_string($pedido_frotas_departamento_unit_to_string)
    {
        if(is_array($pedido_frotas_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $pedido_frotas_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->pedido_frotas_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_departamento_unit_to_string = $pedido_frotas_departamento_unit_to_string;
        }

        $this->vdata['pedido_frotas_departamento_unit_to_string'] = $this->pedido_frotas_departamento_unit_to_string;
    }

    public function get_pedido_frotas_departamento_unit_to_string()
    {
        if(!empty($this->pedido_frotas_departamento_unit_to_string))
        {
            return $this->pedido_frotas_departamento_unit_to_string;
        }
        
        $values = PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_system_users_to_string($pedido_frotas_system_users_to_string)
    {
        if(is_array($pedido_frotas_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $pedido_frotas_system_users_to_string)->getIndexedArray('name', 'name');
            $this->pedido_frotas_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_system_users_to_string = $pedido_frotas_system_users_to_string;
        }

        $this->vdata['pedido_frotas_system_users_to_string'] = $this->pedido_frotas_system_users_to_string;
    }

    public function get_pedido_frotas_system_users_to_string()
    {
        if(!empty($this->pedido_frotas_system_users_to_string))
        {
            return $this->pedido_frotas_system_users_to_string;
        }
        
        $values = PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
        //<onBeforeDeleteCode>
  
        //</onBeforeDeleteCode>

        if(PedidoFrotas::where('tipo_manutencao_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
    }
    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

