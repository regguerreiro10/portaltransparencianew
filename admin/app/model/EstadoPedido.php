<?php

class EstadoPedido extends TRecord
{
    const TABLENAME  = 'estado_pedido';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    const FINALIZADO = '8';
    const CANCELADO = '9';
    const REPROVADO = '10';
    const PENDENTE = '11';
    const AGUARDANDO = '12';
    const APROVADO = '13';
    const NAOENVIADO = '15';
    const ENVIADO = '17';
    const PGTOAPROVADO = '18';
    const COMPROPOSTA = '19';
    const ENTREGUE = '20';
    const REVISAO = '21';
    const PREAPROVADO = '1';
    const VISUALIZARCOTACAO = '23'; //23


    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cor');
        parent::addAttribute('kanban');
        parent::addAttribute('ordem');
        parent::addAttribute('estado_final');
        parent::addAttribute('estado_inicial');
        parent::addAttribute('permite_edicao');
        parent::addAttribute('permite_exclusao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    
    }

    /**
     * Method getCotacaos
     */
    public function getCotacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_id', '=', $this->id));
        return Cotacao::getObjects( $criteria );
    }
    /**
     * Method getCotacaoHistoricos
     */
    public function getCotacaoHistoricos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_id', '=', $this->id));
        return CotacaoHistorico::getObjects( $criteria );
    }
    /**
     * Method getEstadoPedidoAprovadors
     */
    public function getEstadoPedidoAprovadors()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_venda_id', '=', $this->id));
        return EstadoPedidoAprovador::getObjects( $criteria );
    }
    /**
     * Method getMatrizEstadoPedidos
     */
    public function getMatrizEstadoPedidosByEstadoPedidoVendaOrigems()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_venda_origem_id', '=', $this->id));
        return MatrizEstadoPedido::getObjects( $criteria );
    }
    /**
     * Method getMatrizEstadoPedidos
     */
    public function getMatrizEstadoPedidosByEstadoPedidoVendaDestinos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_venda_destino_id', '=', $this->id));
        return MatrizEstadoPedido::getObjects( $criteria );
    }
    /**
     * Method getPedidos
     */
    public function getPedidosBySituacaoPedidos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('situacao_pedido_id', '=', $this->id));
        return Pedido::getObjects( $criteria );
    }
    /**
     * Method getPedidos
     */
    public function getPedidosByEstadoPedidoVendas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_venda_id', '=', $this->id));
        return Pedido::getObjects( $criteria );
    }
    /**
     * Method getPedidoHistoricos
     */
    public function getPedidoHistoricos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_venda_id', '=', $this->id));
        return PedidoHistorico::getObjects( $criteria );
    }

    public function set_cotacao_pedido_to_string($cotacao_pedido_to_string)
    {
        if(is_array($cotacao_pedido_to_string))
        {
            $values = Pedido::where('id', 'in', $cotacao_pedido_to_string)->getIndexedArray('id', 'id');
            $this->cotacao_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->cotacao_pedido_to_string = $cotacao_pedido_to_string;
        }

        $this->vdata['cotacao_pedido_to_string'] = $this->cotacao_pedido_to_string;
    }

    public function get_cotacao_pedido_to_string()
    {
        if(!empty($this->cotacao_pedido_to_string))
        {
            return $this->cotacao_pedido_to_string;
        }
    
        $values = Cotacao::where('estado_pedido_id', '=', $this->id)->getIndexedArray('pedido_id','{pedido->id}');
        return implode(', ', $values);
    }

    public function set_cotacao_pessoa_to_string($cotacao_pessoa_to_string)
    {
        if(is_array($cotacao_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $cotacao_pessoa_to_string)->getIndexedArray('nome', 'nome');
            $this->cotacao_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->cotacao_pessoa_to_string = $cotacao_pessoa_to_string;
        }

        $this->vdata['cotacao_pessoa_to_string'] = $this->cotacao_pessoa_to_string;
    }

    public function get_cotacao_pessoa_to_string()
    {
        if(!empty($this->cotacao_pessoa_to_string))
        {
            return $this->cotacao_pessoa_to_string;
        }
    
        $values = Cotacao::where('estado_pedido_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
        return implode(', ', $values);
    }

    public function set_cotacao_system_users_to_string($cotacao_system_users_to_string)
    {
        if(is_array($cotacao_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $cotacao_system_users_to_string)->getIndexedArray('name', 'name');
            $this->cotacao_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->cotacao_system_users_to_string = $cotacao_system_users_to_string;
        }

        $this->vdata['cotacao_system_users_to_string'] = $this->cotacao_system_users_to_string;
    }

    public function get_cotacao_system_users_to_string()
    {
        if(!empty($this->cotacao_system_users_to_string))
        {
            return $this->cotacao_system_users_to_string;
        }
    
        $values = Cotacao::where('estado_pedido_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_cotacao_estado_pedido_to_string($cotacao_estado_pedido_to_string)
    {
        if(is_array($cotacao_estado_pedido_to_string))
        {
            $values = EstadoPedido::where('id', 'in', $cotacao_estado_pedido_to_string)->getIndexedArray('nome', 'nome');
            $this->cotacao_estado_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->cotacao_estado_pedido_to_string = $cotacao_estado_pedido_to_string;
        }

        $this->vdata['cotacao_estado_pedido_to_string'] = $this->cotacao_estado_pedido_to_string;
    }

    public function get_cotacao_estado_pedido_to_string()
    {
        if(!empty($this->cotacao_estado_pedido_to_string))
        {
            return $this->cotacao_estado_pedido_to_string;
        }
    
        $values = Cotacao::where('estado_pedido_id', '=', $this->id)->getIndexedArray('estado_pedido_id','{estado_pedido->nome}');
        return implode(', ', $values);
    }

    public function set_cotacao_historico_estado_pedido_to_string($cotacao_historico_estado_pedido_to_string)
    {
        if(is_array($cotacao_historico_estado_pedido_to_string))
        {
            $values = EstadoPedido::where('id', 'in', $cotacao_historico_estado_pedido_to_string)->getIndexedArray('nome', 'nome');
            $this->cotacao_historico_estado_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->cotacao_historico_estado_pedido_to_string = $cotacao_historico_estado_pedido_to_string;
        }

        $this->vdata['cotacao_historico_estado_pedido_to_string'] = $this->cotacao_historico_estado_pedido_to_string;
    }

    public function get_cotacao_historico_estado_pedido_to_string()
    {
        if(!empty($this->cotacao_historico_estado_pedido_to_string))
        {
            return $this->cotacao_historico_estado_pedido_to_string;
        }
    
        $values = CotacaoHistorico::where('estado_pedido_id', '=', $this->id)->getIndexedArray('estado_pedido_id','{estado_pedido->nome}');
        return implode(', ', $values);
    }

    public function set_cotacao_historico_aprovador_to_string($cotacao_historico_aprovador_to_string)
    {
        if(is_array($cotacao_historico_aprovador_to_string))
        {
            $values = Aprovador::where('id', 'in', $cotacao_historico_aprovador_to_string)->getIndexedArray('id', 'id');
            $this->cotacao_historico_aprovador_to_string = implode(', ', $values);
        }
        else
        {
            $this->cotacao_historico_aprovador_to_string = $cotacao_historico_aprovador_to_string;
        }

        $this->vdata['cotacao_historico_aprovador_to_string'] = $this->cotacao_historico_aprovador_to_string;
    }

    public function get_cotacao_historico_aprovador_to_string()
    {
        if(!empty($this->cotacao_historico_aprovador_to_string))
        {
            return $this->cotacao_historico_aprovador_to_string;
        }
    
        $values = CotacaoHistorico::where('estado_pedido_id', '=', $this->id)->getIndexedArray('aprovador_id','{aprovador->id}');
        return implode(', ', $values);
    }

    public function set_cotacao_historico_cotacao_to_string($cotacao_historico_cotacao_to_string)
    {
        if(is_array($cotacao_historico_cotacao_to_string))
        {
            $values = Cotacao::where('id', 'in', $cotacao_historico_cotacao_to_string)->getIndexedArray('id', 'id');
            $this->cotacao_historico_cotacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->cotacao_historico_cotacao_to_string = $cotacao_historico_cotacao_to_string;
        }

        $this->vdata['cotacao_historico_cotacao_to_string'] = $this->cotacao_historico_cotacao_to_string;
    }

    public function get_cotacao_historico_cotacao_to_string()
    {
        if(!empty($this->cotacao_historico_cotacao_to_string))
        {
            return $this->cotacao_historico_cotacao_to_string;
        }
    
        $values = CotacaoHistorico::where('estado_pedido_id', '=', $this->id)->getIndexedArray('cotacao_id','{cotacao->id}');
        return implode(', ', $values);
    }

    public function set_estado_pedido_aprovador_estado_pedido_venda_to_string($estado_pedido_aprovador_estado_pedido_venda_to_string)
    {
        if(is_array($estado_pedido_aprovador_estado_pedido_venda_to_string))
        {
            $values = EstadoPedido::where('id', 'in', $estado_pedido_aprovador_estado_pedido_venda_to_string)->getIndexedArray('nome', 'nome');
            $this->estado_pedido_aprovador_estado_pedido_venda_to_string = implode(', ', $values);
        }
        else
        {
            $this->estado_pedido_aprovador_estado_pedido_venda_to_string = $estado_pedido_aprovador_estado_pedido_venda_to_string;
        }

        $this->vdata['estado_pedido_aprovador_estado_pedido_venda_to_string'] = $this->estado_pedido_aprovador_estado_pedido_venda_to_string;
    }

    public function get_estado_pedido_aprovador_estado_pedido_venda_to_string()
    {
        if(!empty($this->estado_pedido_aprovador_estado_pedido_venda_to_string))
        {
            return $this->estado_pedido_aprovador_estado_pedido_venda_to_string;
        }
    
        $values = EstadoPedidoAprovador::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('estado_pedido_venda_id','{estado_pedido_venda->nome}');
        return implode(', ', $values);
    }

    public function set_estado_pedido_aprovador_aprovador_to_string($estado_pedido_aprovador_aprovador_to_string)
    {
        if(is_array($estado_pedido_aprovador_aprovador_to_string))
        {
            $values = Aprovador::where('id', 'in', $estado_pedido_aprovador_aprovador_to_string)->getIndexedArray('id', 'id');
            $this->estado_pedido_aprovador_aprovador_to_string = implode(', ', $values);
        }
        else
        {
            $this->estado_pedido_aprovador_aprovador_to_string = $estado_pedido_aprovador_aprovador_to_string;
        }

        $this->vdata['estado_pedido_aprovador_aprovador_to_string'] = $this->estado_pedido_aprovador_aprovador_to_string;
    }

    public function get_estado_pedido_aprovador_aprovador_to_string()
    {
        if(!empty($this->estado_pedido_aprovador_aprovador_to_string))
        {
            return $this->estado_pedido_aprovador_aprovador_to_string;
        }
    
        $values = EstadoPedidoAprovador::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('aprovador_id','{aprovador->id}');
        return implode(', ', $values);
    }

    public function set_matriz_estado_pedido_estado_pedido_venda_origem_to_string($matriz_estado_pedido_estado_pedido_venda_origem_to_string)
    {
        if(is_array($matriz_estado_pedido_estado_pedido_venda_origem_to_string))
        {
            $values = EstadoPedido::where('id', 'in', $matriz_estado_pedido_estado_pedido_venda_origem_to_string)->getIndexedArray('nome', 'nome');
            $this->matriz_estado_pedido_estado_pedido_venda_origem_to_string = implode(', ', $values);
        }
        else
        {
            $this->matriz_estado_pedido_estado_pedido_venda_origem_to_string = $matriz_estado_pedido_estado_pedido_venda_origem_to_string;
        }

        $this->vdata['matriz_estado_pedido_estado_pedido_venda_origem_to_string'] = $this->matriz_estado_pedido_estado_pedido_venda_origem_to_string;
    }

    public function get_matriz_estado_pedido_estado_pedido_venda_origem_to_string()
    {
        if(!empty($this->matriz_estado_pedido_estado_pedido_venda_origem_to_string))
        {
            return $this->matriz_estado_pedido_estado_pedido_venda_origem_to_string;
        }
    
        $values = MatrizEstadoPedido::where('estado_pedido_venda_destino_id', '=', $this->id)->getIndexedArray('estado_pedido_venda_origem_id','{estado_pedido_venda_origem->nome}');
        return implode(', ', $values);
    }

    public function set_matriz_estado_pedido_estado_pedido_venda_destino_to_string($matriz_estado_pedido_estado_pedido_venda_destino_to_string)
    {
        if(is_array($matriz_estado_pedido_estado_pedido_venda_destino_to_string))
        {
            $values = EstadoPedido::where('id', 'in', $matriz_estado_pedido_estado_pedido_venda_destino_to_string)->getIndexedArray('nome', 'nome');
            $this->matriz_estado_pedido_estado_pedido_venda_destino_to_string = implode(', ', $values);
        }
        else
        {
            $this->matriz_estado_pedido_estado_pedido_venda_destino_to_string = $matriz_estado_pedido_estado_pedido_venda_destino_to_string;
        }

        $this->vdata['matriz_estado_pedido_estado_pedido_venda_destino_to_string'] = $this->matriz_estado_pedido_estado_pedido_venda_destino_to_string;
    }

    public function get_matriz_estado_pedido_estado_pedido_venda_destino_to_string()
    {
        if(!empty($this->matriz_estado_pedido_estado_pedido_venda_destino_to_string))
        {
            return $this->matriz_estado_pedido_estado_pedido_venda_destino_to_string;
        }
    
        $values = MatrizEstadoPedido::where('estado_pedido_venda_destino_id', '=', $this->id)->getIndexedArray('estado_pedido_venda_destino_id','{estado_pedido_venda_destino->nome}');
        return implode(', ', $values);
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('tipo_pedido_id','{tipo_pedido->nome}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->nome}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->nome}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('estado_pedido_venda_id','{estado_pedido_venda->nome}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('condicao_pagamento_id','{condicao_pagamento->nome}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('transportadora_id','{transportadora->nome}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('negociacao_id','{negociacao->id}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('situacao_pedido_id','{situacao_pedido->nome}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('centrocusto_id','{centrocusto->nome}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('cartao_id','{cartao->id}');
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
    
        $values = Pedido::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    public function set_pedido_historico_pedido_venda_to_string($pedido_historico_pedido_venda_to_string)
    {
        if(is_array($pedido_historico_pedido_venda_to_string))
        {
            $values = Pedido::where('id', 'in', $pedido_historico_pedido_venda_to_string)->getIndexedArray('id', 'id');
            $this->pedido_historico_pedido_venda_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_historico_pedido_venda_to_string = $pedido_historico_pedido_venda_to_string;
        }

        $this->vdata['pedido_historico_pedido_venda_to_string'] = $this->pedido_historico_pedido_venda_to_string;
    }

    public function get_pedido_historico_pedido_venda_to_string()
    {
        if(!empty($this->pedido_historico_pedido_venda_to_string))
        {
            return $this->pedido_historico_pedido_venda_to_string;
        }
    
        $values = PedidoHistorico::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('pedido_venda_id','{pedido_venda->id}');
        return implode(', ', $values);
    }

    public function set_pedido_historico_estado_pedido_venda_to_string($pedido_historico_estado_pedido_venda_to_string)
    {
        if(is_array($pedido_historico_estado_pedido_venda_to_string))
        {
            $values = EstadoPedido::where('id', 'in', $pedido_historico_estado_pedido_venda_to_string)->getIndexedArray('nome', 'nome');
            $this->pedido_historico_estado_pedido_venda_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_historico_estado_pedido_venda_to_string = $pedido_historico_estado_pedido_venda_to_string;
        }

        $this->vdata['pedido_historico_estado_pedido_venda_to_string'] = $this->pedido_historico_estado_pedido_venda_to_string;
    }

    public function get_pedido_historico_estado_pedido_venda_to_string()
    {
        if(!empty($this->pedido_historico_estado_pedido_venda_to_string))
        {
            return $this->pedido_historico_estado_pedido_venda_to_string;
        }
    
        $values = PedidoHistorico::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('estado_pedido_venda_id','{estado_pedido_venda->nome}');
        return implode(', ', $values);
    }

    public function set_pedido_historico_aprovador_to_string($pedido_historico_aprovador_to_string)
    {
        if(is_array($pedido_historico_aprovador_to_string))
        {
            $values = Aprovador::where('id', 'in', $pedido_historico_aprovador_to_string)->getIndexedArray('id', 'id');
            $this->pedido_historico_aprovador_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_historico_aprovador_to_string = $pedido_historico_aprovador_to_string;
        }

        $this->vdata['pedido_historico_aprovador_to_string'] = $this->pedido_historico_aprovador_to_string;
    }

    public function get_pedido_historico_aprovador_to_string()
    {
        if(!empty($this->pedido_historico_aprovador_to_string))
        {
            return $this->pedido_historico_aprovador_to_string;
        }
    
        $values = PedidoHistorico::where('estado_pedido_venda_id', '=', $this->id)->getIndexedArray('aprovador_id','{aprovador->id}');
        return implode(', ', $values);
    }

    public static function getProximoEstadoPedidoVenda($estado_pedido_venda_id_atual)
    {
        $estadoPedidoVendaAtual = new EstadoPedido($estado_pedido_venda_id_atual);
    
        $estadoPedidoVenda = EstadoPedido::where('id', 'in', "(SELECT estado_pedido_venda_destino_id FROM matriz_estado_pedido_venda WHERE estado_pedido_venda_origem_id = {$estado_pedido_venda_id_atual})")
                                              ->where('ordem', '>', $estadoPedidoVendaAtual->ordem)
                                              ->orderBy('ordem', 'asc')
                                              ->first();
        if($estadoPedidoVenda)
        {
            return $estadoPedidoVenda;
        }
    
        return false;
    
    }

    
}

