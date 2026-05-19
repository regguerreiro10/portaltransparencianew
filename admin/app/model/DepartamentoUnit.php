<?php

class DepartamentoUnit extends TRecord
{
    const TABLENAME  = 'departamento_unit';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $system_unit;
    private $cidade;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('rua');
        parent::addAttribute('cep');
        parent::addAttribute('bairro');
        parent::addAttribute('numero');
        parent::addAttribute('cidade_id');
        parent::addAttribute('email');
        parent::addAttribute('valor_empenho');
        parent::addAttribute('system_unit_id');
            
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
     * Method get_system_unit
     * Sample of usage: $var->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_name_name_unit()
    {
        $system_unit = new SystemUnit($this->system_unit_id);
        $departamento_unit = $this;
        return $system_unit->name . ' - ' . $departamento_unit->name;
        
    }
    /**
     * Method set_cidade
     * Sample of usage: $var->cidade = $object;
     * @param $object Instance of Cidade
     */
    public function set_cidade(Cidade $object)
    {
        $this->cidade = $object;
        $this->cidade_id = $object->id;
    }

    /**
     * Method get_cidade
     * Sample of usage: $var->cidade->attribute;
     * @returns Cidade instance
     */
    public function get_cidade()
    {
    
        // loads the associated object
        if (empty($this->cidade))
            $this->cidade = new Cidade($this->cidade_id);
    
        // returns the associated object
        return $this->cidade;
    }

    /**
     * Method getContas
     */
    public function getContas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('departamento_unit_id', '=', $this->id));
        return Conta::getObjects( $criteria );
    }
    /**
     * Method getNegociacaos
     */
    public function getNegociacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('departamento_unit_id', '=', $this->id));
        return Negociacao::getObjects( $criteria );
    }
    /**
     * Method getOuvidorias
     */
    public function getOuvidorias()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('departamento_unit_id', '=', $this->id));
        return Ouvidoria::getObjects( $criteria );
    }
    /**
     * Method getPedidos
     */
    public function getPedidos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('departamento_unit_id', '=', $this->id));
        return Pedido::getObjects( $criteria );
    }
    /**
     * Method getPessoaDepartamentos
     */
    public function getPessoaDepartamentos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('departamento_unit_id', '=', $this->id));
        return PessoaDepartamento::getObjects( $criteria );
    }
    /**
     * Method getSystemUserDepartamentoUnits
     */
    public function getSystemUserDepartamentoUnits()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('departamento_unit_id', '=', $this->id));
        return SystemUserDepartamentoUnit::getObjects( $criteria );
    }
    /**
     * Method getCartaos
     */
    public function getCartaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('departamento_unit_id', '=', $this->id));
        return Cartao::getObjects( $criteria );
    }

    public function set_conta_pessoa_to_string($conta_pessoa_to_string)
    {
        if(is_array($conta_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $conta_pessoa_to_string)->getIndexedArray('nome', 'nome');
            $this->conta_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_pessoa_to_string = $conta_pessoa_to_string;
        }

        $this->vdata['conta_pessoa_to_string'] = $this->conta_pessoa_to_string;
    }

    public function get_conta_pessoa_to_string()
    {
        if(!empty($this->conta_pessoa_to_string))
        {
            return $this->conta_pessoa_to_string;
        }
    
        $values = Conta::where('departamento_unit_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
        return implode(', ', $values);
    }

    public function set_conta_tipo_conta_to_string($conta_tipo_conta_to_string)
    {
        if(is_array($conta_tipo_conta_to_string))
        {
            $values = TipoConta::where('id', 'in', $conta_tipo_conta_to_string)->getIndexedArray('nome', 'nome');
            $this->conta_tipo_conta_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_tipo_conta_to_string = $conta_tipo_conta_to_string;
        }

        $this->vdata['conta_tipo_conta_to_string'] = $this->conta_tipo_conta_to_string;
    }

    public function get_conta_tipo_conta_to_string()
    {
        if(!empty($this->conta_tipo_conta_to_string))
        {
            return $this->conta_tipo_conta_to_string;
        }
    
        $values = Conta::where('departamento_unit_id', '=', $this->id)->getIndexedArray('tipo_conta_id','{tipo_conta->nome}');
        return implode(', ', $values);
    }

    public function set_conta_categoria_to_string($conta_categoria_to_string)
    {
        if(is_array($conta_categoria_to_string))
        {
            $values = Categoria::where('id', 'in', $conta_categoria_to_string)->getIndexedArray('nome', 'nome');
            $this->conta_categoria_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_categoria_to_string = $conta_categoria_to_string;
        }

        $this->vdata['conta_categoria_to_string'] = $this->conta_categoria_to_string;
    }

    public function get_conta_categoria_to_string()
    {
        if(!empty($this->conta_categoria_to_string))
        {
            return $this->conta_categoria_to_string;
        }
    
        $values = Conta::where('departamento_unit_id', '=', $this->id)->getIndexedArray('categoria_id','{categoria->nome}');
        return implode(', ', $values);
    }

    public function set_conta_forma_pagamento_to_string($conta_forma_pagamento_to_string)
    {
        if(is_array($conta_forma_pagamento_to_string))
        {
            $values = FormaPagamento::where('id', 'in', $conta_forma_pagamento_to_string)->getIndexedArray('nome', 'nome');
            $this->conta_forma_pagamento_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_forma_pagamento_to_string = $conta_forma_pagamento_to_string;
        }

        $this->vdata['conta_forma_pagamento_to_string'] = $this->conta_forma_pagamento_to_string;
    }

    public function get_conta_forma_pagamento_to_string()
    {
        if(!empty($this->conta_forma_pagamento_to_string))
        {
            return $this->conta_forma_pagamento_to_string;
        }
    
        $values = Conta::where('departamento_unit_id', '=', $this->id)->getIndexedArray('forma_pagamento_id','{forma_pagamento->nome}');
        return implode(', ', $values);
    }

    public function set_conta_pedido_venda_to_string($conta_pedido_venda_to_string)
    {
        if(is_array($conta_pedido_venda_to_string))
        {
            $values = Pedido::where('id', 'in', $conta_pedido_venda_to_string)->getIndexedArray('id', 'id');
            $this->conta_pedido_venda_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_pedido_venda_to_string = $conta_pedido_venda_to_string;
        }

        $this->vdata['conta_pedido_venda_to_string'] = $this->conta_pedido_venda_to_string;
    }

    public function get_conta_pedido_venda_to_string()
    {
        if(!empty($this->conta_pedido_venda_to_string))
        {
            return $this->conta_pedido_venda_to_string;
        }
    
        $values = Conta::where('departamento_unit_id', '=', $this->id)->getIndexedArray('pedido_venda_id','{pedido_venda->id}');
        return implode(', ', $values);
    }

    public function set_conta_departamento_unit_to_string($conta_departamento_unit_to_string)
    {
        if(is_array($conta_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $conta_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->conta_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_departamento_unit_to_string = $conta_departamento_unit_to_string;
        }

        $this->vdata['conta_departamento_unit_to_string'] = $this->conta_departamento_unit_to_string;
    }

    public function get_conta_departamento_unit_to_string()
    {
        if(!empty($this->conta_departamento_unit_to_string))
        {
            return $this->conta_departamento_unit_to_string;
        }
    
        $values = Conta::where('departamento_unit_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    public function set_conta_system_users_to_string($conta_system_users_to_string)
    {
        if(is_array($conta_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $conta_system_users_to_string)->getIndexedArray('name', 'name');
            $this->conta_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_system_users_to_string = $conta_system_users_to_string;
        }

        $this->vdata['conta_system_users_to_string'] = $this->conta_system_users_to_string;
    }

    public function get_conta_system_users_to_string()
    {
        if(!empty($this->conta_system_users_to_string))
        {
            return $this->conta_system_users_to_string;
        }
    
        $values = Conta::where('departamento_unit_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_negociacao_cliente_to_string($negociacao_cliente_to_string)
    {
        if(is_array($negociacao_cliente_to_string))
        {
            $values = Pessoa::where('id', 'in', $negociacao_cliente_to_string)->getIndexedArray('nome', 'nome');
            $this->negociacao_cliente_to_string = implode(', ', $values);
        }
        else
        {
            $this->negociacao_cliente_to_string = $negociacao_cliente_to_string;
        }

        $this->vdata['negociacao_cliente_to_string'] = $this->negociacao_cliente_to_string;
    }

    public function get_negociacao_cliente_to_string()
    {
        if(!empty($this->negociacao_cliente_to_string))
        {
            return $this->negociacao_cliente_to_string;
        }
    
        $values = Negociacao::where('departamento_unit_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->nome}');
        return implode(', ', $values);
    }

    public function set_negociacao_vendedor_to_string($negociacao_vendedor_to_string)
    {
        if(is_array($negociacao_vendedor_to_string))
        {
            $values = Pessoa::where('id', 'in', $negociacao_vendedor_to_string)->getIndexedArray('nome', 'nome');
            $this->negociacao_vendedor_to_string = implode(', ', $values);
        }
        else
        {
            $this->negociacao_vendedor_to_string = $negociacao_vendedor_to_string;
        }

        $this->vdata['negociacao_vendedor_to_string'] = $this->negociacao_vendedor_to_string;
    }

    public function get_negociacao_vendedor_to_string()
    {
        if(!empty($this->negociacao_vendedor_to_string))
        {
            return $this->negociacao_vendedor_to_string;
        }
    
        $values = Negociacao::where('departamento_unit_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->nome}');
        return implode(', ', $values);
    }

    public function set_negociacao_origem_contato_to_string($negociacao_origem_contato_to_string)
    {
        if(is_array($negociacao_origem_contato_to_string))
        {
            $values = OrigemContato::where('id', 'in', $negociacao_origem_contato_to_string)->getIndexedArray('nome', 'nome');
            $this->negociacao_origem_contato_to_string = implode(', ', $values);
        }
        else
        {
            $this->negociacao_origem_contato_to_string = $negociacao_origem_contato_to_string;
        }

        $this->vdata['negociacao_origem_contato_to_string'] = $this->negociacao_origem_contato_to_string;
    }

    public function get_negociacao_origem_contato_to_string()
    {
        if(!empty($this->negociacao_origem_contato_to_string))
        {
            return $this->negociacao_origem_contato_to_string;
        }
    
        $values = Negociacao::where('departamento_unit_id', '=', $this->id)->getIndexedArray('origem_contato_id','{origem_contato->nome}');
        return implode(', ', $values);
    }

    public function set_negociacao_etapa_negociacao_to_string($negociacao_etapa_negociacao_to_string)
    {
        if(is_array($negociacao_etapa_negociacao_to_string))
        {
            $values = EtapaNegociacao::where('id', 'in', $negociacao_etapa_negociacao_to_string)->getIndexedArray('nome', 'nome');
            $this->negociacao_etapa_negociacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->negociacao_etapa_negociacao_to_string = $negociacao_etapa_negociacao_to_string;
        }

        $this->vdata['negociacao_etapa_negociacao_to_string'] = $this->negociacao_etapa_negociacao_to_string;
    }

    public function get_negociacao_etapa_negociacao_to_string()
    {
        if(!empty($this->negociacao_etapa_negociacao_to_string))
        {
            return $this->negociacao_etapa_negociacao_to_string;
        }
    
        $values = Negociacao::where('departamento_unit_id', '=', $this->id)->getIndexedArray('etapa_negociacao_id','{etapa_negociacao->nome}');
        return implode(', ', $values);
    }

    public function set_negociacao_departamento_unit_to_string($negociacao_departamento_unit_to_string)
    {
        if(is_array($negociacao_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $negociacao_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->negociacao_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->negociacao_departamento_unit_to_string = $negociacao_departamento_unit_to_string;
        }

        $this->vdata['negociacao_departamento_unit_to_string'] = $this->negociacao_departamento_unit_to_string;
    }

    public function get_negociacao_departamento_unit_to_string()
    {
        if(!empty($this->negociacao_departamento_unit_to_string))
        {
            return $this->negociacao_departamento_unit_to_string;
        }
    
        $values = Negociacao::where('departamento_unit_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    public function set_negociacao_system_users_to_string($negociacao_system_users_to_string)
    {
        if(is_array($negociacao_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $negociacao_system_users_to_string)->getIndexedArray('name', 'name');
            $this->negociacao_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->negociacao_system_users_to_string = $negociacao_system_users_to_string;
        }

        $this->vdata['negociacao_system_users_to_string'] = $this->negociacao_system_users_to_string;
    }

    public function get_negociacao_system_users_to_string()
    {
        if(!empty($this->negociacao_system_users_to_string))
        {
            return $this->negociacao_system_users_to_string;
        }
    
        $values = Negociacao::where('departamento_unit_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_ouvidoria_tipo_ouvidoria_to_string($ouvidoria_tipo_ouvidoria_to_string)
    {
        if(is_array($ouvidoria_tipo_ouvidoria_to_string))
        {
            $values = TipoOuvidoria::where('id', 'in', $ouvidoria_tipo_ouvidoria_to_string)->getIndexedArray('nome', 'nome');
            $this->ouvidoria_tipo_ouvidoria_to_string = implode(', ', $values);
        }
        else
        {
            $this->ouvidoria_tipo_ouvidoria_to_string = $ouvidoria_tipo_ouvidoria_to_string;
        }

        $this->vdata['ouvidoria_tipo_ouvidoria_to_string'] = $this->ouvidoria_tipo_ouvidoria_to_string;
    }

    public function get_ouvidoria_tipo_ouvidoria_to_string()
    {
        if(!empty($this->ouvidoria_tipo_ouvidoria_to_string))
        {
            return $this->ouvidoria_tipo_ouvidoria_to_string;
        }
    
        $values = Ouvidoria::where('departamento_unit_id', '=', $this->id)->getIndexedArray('tipo_ouvidoria_id','{tipo_ouvidoria->nome}');
        return implode(', ', $values);
    }

    public function set_ouvidoria_system_users_to_string($ouvidoria_system_users_to_string)
    {
        if(is_array($ouvidoria_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $ouvidoria_system_users_to_string)->getIndexedArray('name', 'name');
            $this->ouvidoria_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->ouvidoria_system_users_to_string = $ouvidoria_system_users_to_string;
        }

        $this->vdata['ouvidoria_system_users_to_string'] = $this->ouvidoria_system_users_to_string;
    }

    public function get_ouvidoria_system_users_to_string()
    {
        if(!empty($this->ouvidoria_system_users_to_string))
        {
            return $this->ouvidoria_system_users_to_string;
        }
    
        $values = Ouvidoria::where('departamento_unit_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_ouvidoria_departamento_unit_to_string($ouvidoria_departamento_unit_to_string)
    {
        if(is_array($ouvidoria_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $ouvidoria_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->ouvidoria_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->ouvidoria_departamento_unit_to_string = $ouvidoria_departamento_unit_to_string;
        }

        $this->vdata['ouvidoria_departamento_unit_to_string'] = $this->ouvidoria_departamento_unit_to_string;
    }

    public function get_ouvidoria_departamento_unit_to_string()
    {
        if(!empty($this->ouvidoria_departamento_unit_to_string))
        {
            return $this->ouvidoria_departamento_unit_to_string;
        }
    
        $values = Ouvidoria::where('departamento_unit_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('tipo_pedido_id','{tipo_pedido->nome}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->nome}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->nome}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('estado_pedido_venda_id','{estado_pedido_venda->nome}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('condicao_pagamento_id','{condicao_pagamento->nome}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('transportadora_id','{transportadora->nome}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('negociacao_id','{negociacao->id}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('situacao_pedido_id','{situacao_pedido->nome}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('centrocusto_id','{centrocusto->nome}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('cartao_id','{cartao->id}');
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
    
        $values = Pedido::where('departamento_unit_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    public function set_pessoa_departamento_pessoa_to_string($pessoa_departamento_pessoa_to_string)
    {
        if(is_array($pessoa_departamento_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $pessoa_departamento_pessoa_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_departamento_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_departamento_pessoa_to_string = $pessoa_departamento_pessoa_to_string;
        }

        $this->vdata['pessoa_departamento_pessoa_to_string'] = $this->pessoa_departamento_pessoa_to_string;
    }

    public function get_pessoa_departamento_pessoa_to_string()
    {
        if(!empty($this->pessoa_departamento_pessoa_to_string))
        {
            return $this->pessoa_departamento_pessoa_to_string;
        }
    
        $values = PessoaDepartamento::where('departamento_unit_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
        return implode(', ', $values);
    }

    public function set_pessoa_departamento_departamento_unit_to_string($pessoa_departamento_departamento_unit_to_string)
    {
        if(is_array($pessoa_departamento_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $pessoa_departamento_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->pessoa_departamento_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_departamento_departamento_unit_to_string = $pessoa_departamento_departamento_unit_to_string;
        }

        $this->vdata['pessoa_departamento_departamento_unit_to_string'] = $this->pessoa_departamento_departamento_unit_to_string;
    }

    public function get_pessoa_departamento_departamento_unit_to_string()
    {
        if(!empty($this->pessoa_departamento_departamento_unit_to_string))
        {
            return $this->pessoa_departamento_departamento_unit_to_string;
        }
    
        $values = PessoaDepartamento::where('departamento_unit_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    public function set_system_user_departamento_unit_departamento_unit_to_string($system_user_departamento_unit_departamento_unit_to_string)
    {
        if(is_array($system_user_departamento_unit_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $system_user_departamento_unit_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->system_user_departamento_unit_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->system_user_departamento_unit_departamento_unit_to_string = $system_user_departamento_unit_departamento_unit_to_string;
        }

        $this->vdata['system_user_departamento_unit_departamento_unit_to_string'] = $this->system_user_departamento_unit_departamento_unit_to_string;
    }

    public function get_system_user_departamento_unit_departamento_unit_to_string()
    {
        if(!empty($this->system_user_departamento_unit_departamento_unit_to_string))
        {
            return $this->system_user_departamento_unit_departamento_unit_to_string;
        }
    
        $values = SystemUserDepartamentoUnit::where('departamento_unit_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    public function set_system_user_departamento_unit_system_users_to_string($system_user_departamento_unit_system_users_to_string)
    {
        if(is_array($system_user_departamento_unit_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $system_user_departamento_unit_system_users_to_string)->getIndexedArray('name', 'name');
            $this->system_user_departamento_unit_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->system_user_departamento_unit_system_users_to_string = $system_user_departamento_unit_system_users_to_string;
        }

        $this->vdata['system_user_departamento_unit_system_users_to_string'] = $this->system_user_departamento_unit_system_users_to_string;
    }

    public function get_system_user_departamento_unit_system_users_to_string()
    {
        if(!empty($this->system_user_departamento_unit_system_users_to_string))
        {
            return $this->system_user_departamento_unit_system_users_to_string;
        }
    
        $values = SystemUserDepartamentoUnit::where('departamento_unit_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_cartao_departamento_unit_to_string($cartao_departamento_unit_to_string)
    {
        if(is_array($cartao_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $cartao_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->cartao_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->cartao_departamento_unit_to_string = $cartao_departamento_unit_to_string;
        }

        $this->vdata['cartao_departamento_unit_to_string'] = $this->cartao_departamento_unit_to_string;
    }

    public function get_cartao_departamento_unit_to_string()
    {
        if(!empty($this->cartao_departamento_unit_to_string))
        {
            return $this->cartao_departamento_unit_to_string;
        }
    
        $values = Cartao::where('departamento_unit_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(Conta::where('departamento_unit_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(Negociacao::where('departamento_unit_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(Ouvidoria::where('departamento_unit_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(Pedido::where('departamento_unit_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(PessoaDepartamento::where('departamento_unit_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(SystemUserDepartamentoUnit::where('departamento_unit_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(Cartao::where('departamento_unit_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}