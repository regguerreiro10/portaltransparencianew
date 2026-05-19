<?php

class Pedido extends TRecord
{
    const TABLENAME  = 'pedido';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    
    private $veiculos;
    private $departamento_unit;
    private $centrocusto;
    private $situacao_pedido;
    private $cliente;
    private $vendedor;
    private $estado_pedido_venda;
    private $condicao_pagamento;
    private $transportadora;
    private $tipo_pedido;
    private $negociacao;
    private $system_users;
    private $cartao;
    private $saldo_departamento;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_pedido_id');
        parent::addAttribute('descricaopedido');
        parent::addAttribute('cliente_id');
        parent::addAttribute('vendedor_id');
        parent::addAttribute('estado_pedido_venda_id');
        parent::addAttribute('estado_pedido1_id');
        parent::addAttribute('condicao_pagamento_id');
        parent::addAttribute('transportadora_id');
        parent::addAttribute('negociacao_id');
        parent::addAttribute('dt_pedido');
        parent::addAttribute('obs');
        parent::addAttribute('frete');
        parent::addAttribute('situacao_pedido_id');
        parent::addAttribute('mes');
        parent::addAttribute('ano');
        parent::addAttribute('valor_total');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('obs_comercial');
        parent::addAttribute('obs_financeiro');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('centrocusto_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('valor_total_cotacao');
        parent::addAttribute('valor_total_cotacao');
        parent::addAttribute('valor_desconto_cotacao');
        parent::addAttribute('valor_liquido_cotacao');
        parent::addAttribute('cidade_id');
        parent::addAttribute('cartao_id');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('dt_finalizacao');
        parent::addAttribute('entidade_id');
        parent::addAttribute('saldo_departamento_id');
        parent::addAttribute('data_limite_resposta');


    
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
     * Method set_departamento_unit
     * Sample of usage: $var->departamento_unit = $object;
     * @param $object Instance of DepartamentoUnit
     */
    public function set_departamento_unit(DepartamentoUnit $object)
    {
        $this->departamento_unit = $object;
        $this->departamento_unit_id = $object->id;
    }

    /**
     * Method get_departamento_unit
     * Sample of usage: $var->departamento_unit->attribute;
     * @returns DepartamentoUnit instance
     */
    public function get_departamento_unit()
    {
    
        // loads the associated object
        if (empty($this->departamento_unit))
            $this->departamento_unit = new DepartamentoUnit($this->departamento_unit_id);
    
        // returns the associated object
        return $this->departamento_unit;
    }
    /**
     * Method set_centrocusto
     * Sample of usage: $var->centrocusto = $object;
     * @param $object Instance of Centrocusto
     */
    public function set_centrocusto(Centrocusto $object)
    {
        $this->centrocusto = $object;
        $this->centrocusto_id = $object->id;
    }

    /**
     * Method get_centrocusto
     * Sample of usage: $var->centrocusto->attribute;
     * @returns Centrocusto instance
     */
    public function get_centrocusto()
    {
    
        // loads the associated object
        if (empty($this->centrocusto))
            $this->centrocusto = new Centrocusto($this->centrocusto_id);
    
        // returns the associated object
        return $this->centrocusto;
    }
    /**
     * Method set_estado_pedido
     * Sample of usage: $var->estado_pedido = $object;
     * @param $object Instance of EstadoPedido
     */
    public function set_situacao_pedido(EstadoPedido $object)
    {
        $this->situacao_pedido = $object;
        $this->situacao_pedido_id = $object->id;
    }

    /**
     * Method get_situacao_pedido
     * Sample of usage: $var->situacao_pedido->attribute;
     * @returns EstadoPedido instance
     */
    public function get_situacao_pedido()
    {
    
        // loads the associated object
        if (empty($this->situacao_pedido))
            $this->situacao_pedido = new EstadoPedido($this->situacao_pedido_id);
    
        // returns the associated object
        return $this->situacao_pedido;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_cliente(Pessoa $object)
    {
        $this->cliente = $object;
        $this->cliente_id = $object->id;
    }

    /**
     * Method get_cliente
     * Sample of usage: $var->cliente->attribute;
     * @returns Pessoa instance
     */
    public function get_cliente()
    {
    
        // loads the associated object
        if (empty($this->cliente))
            $this->cliente = new Pessoa($this->cliente_id);
    
        // returns the associated object
        return $this->cliente;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_vendedor(Pessoa $object)
    {
        $this->vendedor = $object;
        $this->vendedor_id = $object->id;
    }

    /**
     * Method get_vendedor
     * Sample of usage: $var->vendedor->attribute;
     * @returns Pessoa instance
     */
    public function get_vendedor()
    {
    
        // loads the associated object
        if (empty($this->vendedor))
            $this->vendedor = new Pessoa($this->vendedor_id);
    
        // returns the associated object
        return $this->vendedor;
    }
    /**
     * Method set_estado_pedido
     * Sample of usage: $var->estado_pedido = $object;
     * @param $object Instance of EstadoPedido
     */
    public function set_estado_pedido_venda(EstadoPedido $object)
    {
        $this->estado_pedido_venda = $object;
        $this->estado_pedido_venda_id = $object->id;
    }

    /**
     * Method get_estado_pedido_venda
     * Sample of usage: $var->estado_pedido_venda->attribute;
     * @returns EstadoPedido instance
     */
    public function get_estado_pedido_venda()
    {
    
        // loads the associated object
        if (empty($this->estado_pedido_venda))
            $this->estado_pedido_venda = new EstadoPedido($this->estado_pedido_venda_id);
    
        // returns the associated object
        return $this->estado_pedido_venda;
    }
    /**
     * Method set_condicao_pagamento
     * Sample of usage: $var->condicao_pagamento = $object;
     * @param $object Instance of CondicaoPagamento
     */
    public function set_condicao_pagamento(CondicaoPagamento $object)
    {
        $this->condicao_pagamento = $object;
        $this->condicao_pagamento_id = $object->id;
    }

    /**
     * Method get_condicao_pagamento
     * Sample of usage: $var->condicao_pagamento->attribute;
     * @returns CondicaoPagamento instance
     */
    public function get_condicao_pagamento()
    {
    
        // loads the associated object
        if (empty($this->condicao_pagamento))
            $this->condicao_pagamento = new CondicaoPagamento($this->condicao_pagamento_id);
    
        // returns the associated object
        return $this->condicao_pagamento;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_transportadora(Pessoa $object)
    {
        $this->transportadora = $object;
        $this->transportadora_id = $object->id;
    }

    /**
     * Method get_transportadora
     * Sample of usage: $var->transportadora->attribute;
     * @returns Pessoa instance
     */
    public function get_transportadora()
    {
    
        // loads the associated object
        if (empty($this->transportadora))
            $this->transportadora = new Pessoa($this->transportadora_id);
    
        // returns the associated object
        return $this->transportadora;
    }
    /**
     * Method set_tipo_pedido
     * Sample of usage: $var->tipo_pedido = $object;
     * @param $object Instance of TipoPedido
     */
    public function set_tipo_pedido(TipoPedido $object)
    {
        $this->tipo_pedido = $object;
        $this->tipo_pedido_id = $object->id;
    }

    /**
     * Method get_tipo_pedido
     * Sample of usage: $var->tipo_pedido->attribute;
     * @returns TipoPedido instance
     */
    public function get_tipo_pedido()
    {
    
        // loads the associated object
        if (empty($this->tipo_pedido))
            $this->tipo_pedido = new TipoPedido($this->tipo_pedido_id);
    
        // returns the associated object
        return $this->tipo_pedido;
    }
    /**
     * Method set_negociacao
     * Sample of usage: $var->negociacao = $object;
     * @param $object Instance of Negociacao
     */
    public function set_negociacao(Negociacao $object)
    {
        $this->negociacao = $object;
        $this->negociacao_id = $object->id;
    }

    /**
     * Method get_negociacao
     * Sample of usage: $var->negociacao->attribute;
     * @returns Negociacao instance
     */
    public function get_negociacao()
    {
    
        // loads the associated object
        if (empty($this->negociacao))
            $this->negociacao = new Negociacao($this->negociacao_id);
    
        // returns the associated object
        return $this->negociacao;
    }
    /**
     * Method set_system_users
     * Sample of usage: $var->system_users = $object;
     * @param $object Instance of SystemUsers
     */
    public function set_system_users(SystemUsers $object)
    {
        $this->system_users = $object;
        $this->system_users_id = $object->id;
    }

    /**
     * Method get_system_users
     * Sample of usage: $var->system_users->attribute;
     * @returns SystemUsers instance
     */
    public function get_system_users()
    {
    
        // loads the associated object
        if (empty($this->system_users))
            $this->system_users = new SystemUsers($this->system_users_id);
    
        // returns the associated object
        return $this->system_users;
    }
    /**
     * Method set_cartao
     * Sample of usage: $var->cartao = $object;
     * @param $object Instance of Cartao
     */
    public function set_cartao(Cartao $object)
    {
        $this->cartao = $object;
        $this->cartao_id = $object->id;
    }

    /**
     * Method get_cartao
     * Sample of usage: $var->cartao->attribute;
     * @returns Cartao instance
     */
    public function get_cartao()
    {
    
        // loads the associated object
        if (empty($this->cartao))
            $this->cartao = new Cartao($this->cartao_id);
    
        // returns the associated object
        return $this->cartao;
    }

    /**
     * Method getCidadePedidos
     */
    public function getCidadePedidos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_id', '=', $this->id));
        return CidadePedido::getObjects( $criteria );
    }
    /**
     * Method getContas
     */
    public function getContas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_venda_id', '=', $this->id));
        return Conta::getObjects( $criteria );
    }
    /**
     * Method getCotacaos
     */
    public function getCotacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_id', '=', $this->id));
        return Cotacao::getObjects( $criteria );
    }
    /**
     * Method getDocumentosPedidos
     */
    public function getDocumentosPedidos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_id', '=', $this->id));
        return DocumentosPedido::getObjects( $criteria );
    }
    /**
     * Method getItensPedidos
     */
    public function getItensPedidos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_venda_id', '=', $this->id));
        return ItensPedido::getObjects( $criteria );
    }
    /**
     * Method getNotaFiscals
     */
    public function getNotaFiscals()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_venda_id', '=', $this->id));
        return NotaFiscal::getObjects( $criteria );
    }
    /**
     * Method getPedidoHistoricos
     */
    public function getPedidoHistoricos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_venda_id', '=', $this->id));
        return PedidoHistorico::getObjects( $criteria );
    }
    /**
     * Method getPedidoSeguimentos
     */
    public function getPedidoSeguimentos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_id', '=', $this->id));
        return PedidoSeguimento::getObjects( $criteria );
    }

    public function set_cidade_pedido_cidade_to_string($cidade_pedido_cidade_to_string)
    {
        if(is_array($cidade_pedido_cidade_to_string))
        {
            $values = Cidade::where('id', 'in', $cidade_pedido_cidade_to_string)->getIndexedArray('nome', 'nome');
            $this->cidade_pedido_cidade_to_string = implode(', ', $values);
        }
        else
        {
            $this->cidade_pedido_cidade_to_string = $cidade_pedido_cidade_to_string;
        }

        $this->vdata['cidade_pedido_cidade_to_string'] = $this->cidade_pedido_cidade_to_string;
    }

    public function get_cidade_pedido_cidade_to_string()
    {
        if(!empty($this->cidade_pedido_cidade_to_string))
        {
            return $this->cidade_pedido_cidade_to_string;
        }
    
        $values = CidadePedido::where('pedido_id', '=', $this->id)->getIndexedArray('cidade_id','{cidade->nome}');
        return implode(', ', $values);
    }

    public function set_cidade_pedido_pedido_to_string($cidade_pedido_pedido_to_string)
    {
        if(is_array($cidade_pedido_pedido_to_string))
        {
            $values = Pedido::where('id', 'in', $cidade_pedido_pedido_to_string)->getIndexedArray('id', 'id');
            $this->cidade_pedido_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->cidade_pedido_pedido_to_string = $cidade_pedido_pedido_to_string;
        }

        $this->vdata['cidade_pedido_pedido_to_string'] = $this->cidade_pedido_pedido_to_string;
    }

    public function get_cidade_pedido_pedido_to_string()
    {
        if(!empty($this->cidade_pedido_pedido_to_string))
        {
            return $this->cidade_pedido_pedido_to_string;
        }
    
        $values = CidadePedido::where('pedido_id', '=', $this->id)->getIndexedArray('pedido_id','{pedido->id}');
        return implode(', ', $values);
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
    
        $values = Conta::where('pedido_venda_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
    
        $values = Conta::where('pedido_venda_id', '=', $this->id)->getIndexedArray('tipo_conta_id','{tipo_conta->nome}');
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
    
        $values = Conta::where('pedido_venda_id', '=', $this->id)->getIndexedArray('categoria_id','{categoria->nome}');
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
    
        $values = Conta::where('pedido_venda_id', '=', $this->id)->getIndexedArray('forma_pagamento_id','{forma_pagamento->nome}');
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
    
        $values = Conta::where('pedido_venda_id', '=', $this->id)->getIndexedArray('pedido_venda_id','{pedido_venda->id}');
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
    
        $values = Conta::where('pedido_venda_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
    
        $values = Conta::where('pedido_venda_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
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
    
        $values = Cotacao::where('pedido_id', '=', $this->id)->getIndexedArray('pedido_id','{pedido->id}');
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
    
        $values = Cotacao::where('pedido_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
    
        $values = Cotacao::where('pedido_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
    
        $values = Cotacao::where('pedido_id', '=', $this->id)->getIndexedArray('estado_pedido_id','{estado_pedido->nome}');
        return implode(', ', $values);
    }

    public function set_documentos_pedido_pedido_to_string($documentos_pedido_pedido_to_string)
    {
        if(is_array($documentos_pedido_pedido_to_string))
        {
            $values = Pedido::where('id', 'in', $documentos_pedido_pedido_to_string)->getIndexedArray('id', 'id');
            $this->documentos_pedido_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->documentos_pedido_pedido_to_string = $documentos_pedido_pedido_to_string;
        }

        $this->vdata['documentos_pedido_pedido_to_string'] = $this->documentos_pedido_pedido_to_string;
    }

    public function get_documentos_pedido_pedido_to_string()
    {
        if(!empty($this->documentos_pedido_pedido_to_string))
        {
            return $this->documentos_pedido_pedido_to_string;
        }
    
        $values = DocumentosPedido::where('pedido_id', '=', $this->id)->getIndexedArray('pedido_id','{pedido->id}');
        return implode(', ', $values);
    }

    public function set_itens_pedido_pedido_venda_to_string($itens_pedido_pedido_venda_to_string)
    {
        if(is_array($itens_pedido_pedido_venda_to_string))
        {
            $values = Pedido::where('id', 'in', $itens_pedido_pedido_venda_to_string)->getIndexedArray('id', 'id');
            $this->itens_pedido_pedido_venda_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_pedido_pedido_venda_to_string = $itens_pedido_pedido_venda_to_string;
        }

        $this->vdata['itens_pedido_pedido_venda_to_string'] = $this->itens_pedido_pedido_venda_to_string;
    }

    public function get_itens_pedido_pedido_venda_to_string()
    {
        if(!empty($this->itens_pedido_pedido_venda_to_string))
        {
            return $this->itens_pedido_pedido_venda_to_string;
        }
    
        $values = ItensPedido::where('pedido_venda_id', '=', $this->id)->getIndexedArray('pedido_venda_id','{pedido_venda->id}');
        return implode(', ', $values);
    }

    public function set_itens_pedido_produto_to_string($itens_pedido_produto_to_string)
    {
        if(is_array($itens_pedido_produto_to_string))
        {
            $values = Produto::where('id', 'in', $itens_pedido_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->itens_pedido_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_pedido_produto_to_string = $itens_pedido_produto_to_string;
        }

        $this->vdata['itens_pedido_produto_to_string'] = $this->itens_pedido_produto_to_string;
    }

    public function get_itens_pedido_produto_to_string()
    {
        if(!empty($this->itens_pedido_produto_to_string))
        {
            return $this->itens_pedido_produto_to_string;
        }
    
        $values = ItensPedido::where('pedido_venda_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

    public function set_nota_fiscal_cliente_to_string($nota_fiscal_cliente_to_string)
    {
        if(is_array($nota_fiscal_cliente_to_string))
        {
            $values = Pessoa::where('id', 'in', $nota_fiscal_cliente_to_string)->getIndexedArray('nome', 'nome');
            $this->nota_fiscal_cliente_to_string = implode(', ', $values);
        }
        else
        {
            $this->nota_fiscal_cliente_to_string = $nota_fiscal_cliente_to_string;
        }

        $this->vdata['nota_fiscal_cliente_to_string'] = $this->nota_fiscal_cliente_to_string;
    }

    public function get_nota_fiscal_cliente_to_string()
    {
        if(!empty($this->nota_fiscal_cliente_to_string))
        {
            return $this->nota_fiscal_cliente_to_string;
        }
    
        $values = NotaFiscal::where('pedido_venda_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->nome}');
        return implode(', ', $values);
    }

    public function set_nota_fiscal_pedido_venda_to_string($nota_fiscal_pedido_venda_to_string)
    {
        if(is_array($nota_fiscal_pedido_venda_to_string))
        {
            $values = Pedido::where('id', 'in', $nota_fiscal_pedido_venda_to_string)->getIndexedArray('id', 'id');
            $this->nota_fiscal_pedido_venda_to_string = implode(', ', $values);
        }
        else
        {
            $this->nota_fiscal_pedido_venda_to_string = $nota_fiscal_pedido_venda_to_string;
        }

        $this->vdata['nota_fiscal_pedido_venda_to_string'] = $this->nota_fiscal_pedido_venda_to_string;
    }

    public function get_nota_fiscal_pedido_venda_to_string()
    {
        if(!empty($this->nota_fiscal_pedido_venda_to_string))
        {
            return $this->nota_fiscal_pedido_venda_to_string;
        }
    
        $values = NotaFiscal::where('pedido_venda_id', '=', $this->id)->getIndexedArray('pedido_venda_id','{pedido_venda->id}');
        return implode(', ', $values);
    }

    public function set_nota_fiscal_condicao_pagamento_to_string($nota_fiscal_condicao_pagamento_to_string)
    {
        if(is_array($nota_fiscal_condicao_pagamento_to_string))
        {
            $values = CondicaoPagamento::where('id', 'in', $nota_fiscal_condicao_pagamento_to_string)->getIndexedArray('nome', 'nome');
            $this->nota_fiscal_condicao_pagamento_to_string = implode(', ', $values);
        }
        else
        {
            $this->nota_fiscal_condicao_pagamento_to_string = $nota_fiscal_condicao_pagamento_to_string;
        }

        $this->vdata['nota_fiscal_condicao_pagamento_to_string'] = $this->nota_fiscal_condicao_pagamento_to_string;
    }

    public function get_nota_fiscal_condicao_pagamento_to_string()
    {
        if(!empty($this->nota_fiscal_condicao_pagamento_to_string))
        {
            return $this->nota_fiscal_condicao_pagamento_to_string;
        }
    
        $values = NotaFiscal::where('pedido_venda_id', '=', $this->id)->getIndexedArray('condicao_pagamento_id','{condicao_pagamento->nome}');
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
    
        $values = PedidoHistorico::where('pedido_venda_id', '=', $this->id)->getIndexedArray('pedido_venda_id','{pedido_venda->id}');
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
    
        $values = PedidoHistorico::where('pedido_venda_id', '=', $this->id)->getIndexedArray('estado_pedido_venda_id','{estado_pedido_venda->nome}');
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
    
        $values = PedidoHistorico::where('pedido_venda_id', '=', $this->id)->getIndexedArray('aprovador_id','{aprovador->id}');
        return implode(', ', $values);
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
    
        $values = PedidoSeguimento::where('pedido_id', '=', $this->id)->getIndexedArray('pedido_id','{pedido->id}');
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
    
        $values = PedidoSeguimento::where('pedido_id', '=', $this->id)->getIndexedArray('seguimento_id','{seguimento->id}');
        return implode(', ', $values);
    }

    public static function createFromNegociacao($negociacao, $propriedadesPedidoVenda)
    {
        $estadoPedidoVendaInicial = EstadoPedidoVenda::where('estado_inicial', '=', 'T')
                                                     ->first();
    
        $pedidoVenda = new PedidoVenda();
        $pedidoVenda->cliente_id = $negociacao->cliente_id;
        $pedidoVenda->vendedor_id = $negociacao->vendedor_id;
        $pedidoVenda->estado_pedido_venda_id = $estadoPedidoVendaInicial->id;
        $pedidoVenda->dt_pedido = date('Y-m-d');
        $pedidoVenda->mes = date('m');
        $pedidoVenda->ano = date('Y');
        $pedidoVenda->valor_total = 0;
        $pedidoVenda->negociacao_id = $negociacao->id;
    
        $pedidoVenda->tipo_pedido_id = $propriedadesPedidoVenda->tipo_pedido_id;
        $pedidoVenda->condicao_pagamento_id = $propriedadesPedidoVenda->condicao_pagamento_id;
        $pedidoVenda->transportadora_id = $propriedadesPedidoVenda->transportadora_id;
    
        $pedidoVenda->store();
    
        $negociacaoItems = $negociacao->getNegociacaoItems();
    
        if($negociacaoItems)
        {
            foreach($negociacaoItems as $negociacaoItem)
            {
                $pedidoVendaItem = new PedidoVendaItem();
                $pedidoVendaItem->produto_id = $negociacaoItem->produto_id;
                $pedidoVendaItem->quantidade = $negociacaoItem->quantidade;
                $pedidoVendaItem->valor = $negociacaoItem->valor;
                $pedidoVendaItem->valor_total = $negociacaoItem->valor_total;
            
                $pedidoVendaItem->pedido_venda_id = $pedidoVenda->id;
                $pedidoVendaItem->store();
            
                $pedidoVenda->valor_total += $pedidoVendaItem->valor_total;
            }
        
            $pedidoVenda->store();
        }
    
        return $pedidoVenda;
    }

}

