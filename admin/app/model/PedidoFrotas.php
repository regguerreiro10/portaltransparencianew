<?php

//<fileHeader>

//</fileHeader>

class PedidoFrotas extends TRecord
{
    const TABLENAME  = 'pedido_frotas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    const DELETEDAT  = 'deleted_at';
    
    
    private $estado_pedido_frotas;
    private $niveltanque;
    private $tipo_manutencao;
    private $negociacao;
    private $condicao_pagamento;
    private $system_unit;
    private $departamento_unit;
    private $system_users;
    private $estabelecimento;
    private $condutor_entrada;
    private $condutor_retirada;
    private $veiculos; 
        private $saldo_departamento;

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
        parent::addAttribute('dt_pedido');
        parent::addAttribute('descricaopedido');
        parent::addAttribute('dtprevisaoentrega');
        parent::addAttribute('estado_pedido_frotas_id');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('estabelecimento_id');
        parent::addAttribute('km');
        parent::addAttribute('condutor_entrada_id');
        parent::addAttribute('condutor_retirada_id');
        parent::addAttribute('dataretirada');
        parent::addAttribute('dataentrada');
        parent::addAttribute('obs');
        parent::addAttribute('mes');
        parent::addAttribute('ano');
        parent::addAttribute('dt_finalizacao');
        parent::addAttribute('cidade_id');
        parent::addAttribute('tipo_manutencao_id');
        parent::addAttribute('negociacao_id');
        parent::addAttribute('condicao_pagamento_id');
        parent::addAttribute('valor_total');
        parent::addAttribute('valor_total_proposta');
        parent::addAttribute('valor_desconto_proposta');
        parent::addAttribute('valor_liquido_proposta');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('entidade_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('data_limite_resposta');
        parent::addAttribute('estado_pedido_frotas1_id');
        parent::addAttribute('saldo_departamento_id');
        parent::addAttribute('data_aprovacao');
        parent::addAttribute('orcamento_base');
        parent::addAttribute('orcamento_base_id');
        parent::addAttribute('ciclos');
        parent::addAttribute('abastecimento');
        parent::addAttribute('dispositivos_solicitados_id');
        parent::addAttribute('valor_litro');
        parent::addAttribute('filtro_redes');
        

        //<onAfterConstruct>

        //</onAfterConstruct>
    }

    /**
     * Method set_estado_pedido_frotas
     * Sample of usage: $var->estado_pedido_frotas = $object;
     * @param $object Instance of EstadoPedidoFrotas
     */

    public function get_total_itens_produtos()
    {
        $itens = ItensPedidoFrotas::where('pedido_frotas_id', '=', $this->id)
                                  ->load();

        
        $total_produto = 0;                                  
        foreach($itens as $item)
        {
            if($item->tipo == '1')
            {
                $total_produto += $item->valor_total;
            }
        }

        return $total_produto;
    }

    public function get_total_itens_servico()
    {
        $itens = ItensPedidoFrotas::where('pedido_frotas_id', '=', $this->id)
                                  ->load();

        $valor_servico = 0;                                  
        foreach($itens as $item)
        {
            if($item->tipo == '2')
            {
                $valor_servico += $item->valor_total;
            }
        }

        return $valor_servico;
    }

    public function set_estado_pedido_frotas(EstadoPedidoFrotas $object)
    {
        $this->estado_pedido_frotas = $object;
        $this->estado_pedido_frotas_id = $object->id;
    }
    
    /**
     * Method get_estado_pedido_frotas
     * Sample of usage: $var->estado_pedido_frotas->attribute;
     * @returns EstadoPedidoFrotas instance
     */
    public function get_estado_pedido_frotas()
    {
        
        // loads the associated object
        if (empty($this->estado_pedido_frotas))
            $this->estado_pedido_frotas = new EstadoPedidoFrotas($this->estado_pedido_frotas_id);
        
        // returns the associated object
        return $this->estado_pedido_frotas;
    }
   
    /**
     * Method set_niveltanque
     * Sample of usage: $var->niveltanque = $object;
     * @param $object Instance of Niveltanque
     */
    public function set_veiculos(Veiculos $object)
    {
        $this->veiculos = $object;
        $this->veiculos_id = $object->id;
    }
    
    /**
     * Method get_niveltanque
     * Sample of usage: $var->niveltanque->attribute;
     * @returns Niveltanque instance
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
     * Method set_tipo_manutencao
     * Sample of usage: $var->tipo_manutencao = $object;
     * @param $object Instance of TipoManutencao
     */
    public function set_tipo_manutencao(TipoManutencao $object)
    {
        $this->tipo_manutencao = $object;
        $this->tipo_manutencao_id = $object->id;
    }
    
    /**
     * Method get_tipo_manutencao
     * Sample of usage: $var->tipo_manutencao->attribute;
     * @returns TipoManutencao instance
     */
    public function get_tipo_manutencao()
    {
        
        // loads the associated object
        if (empty($this->tipo_manutencao))
            $this->tipo_manutencao = new TipoManutencao($this->tipo_manutencao_id);
        
        // returns the associated object
        return $this->tipo_manutencao;
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
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_estabelecimento(Pessoa $object)
    {
        $this->estabelecimento = $object;
        $this->estabelecimento_id = $object->id;
    }
    
    /**
     * Method get_estabelecimento
     * Sample of usage: $var->estabelecimento->attribute;
     * @returns Pessoa instance
     */
    public function get_estabelecimento()
    {
        
        // loads the associated object
        if (empty($this->estabelecimento))
            $this->estabelecimento = new Pessoa($this->estabelecimento_id);
        
        // returns the associated object
        return $this->estabelecimento;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_condutor_entrada(Pessoa $object)
    {
        $this->condutor_entrada = $object;
        $this->condutor_entrada_id = $object->id;
    }
    
    /**
     * Method get_condutor_entrada
     * Sample of usage: $var->condutor_entrada->attribute;
     * @returns Pessoa instance
     */
    public function get_condutor_entrada()
    {
        
        // loads the associated object
        if (empty($this->condutor_entrada))
            $this->condutor_entrada = new Pessoa($this->condutor_entrada_id);
        
        // returns the associated object
        return $this->condutor_entrada;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_condutor_retirada(Pessoa $object)
    {
        $this->condutor_retirada = $object;
        $this->condutor_retirada_id = $object->id;
    }
    
    /**
     * Method get_condutor_retirada
     * Sample of usage: $var->condutor_retirada->attribute;
     * @returns Pessoa instance
     */
    public function get_condutor_retirada()
    {
        
        // loads the associated object
        if (empty($this->condutor_retirada))
            $this->condutor_retirada = new Pessoa($this->condutor_retirada_id);
        
        // returns the associated object
        return $this->condutor_retirada;
    }
    
    /**
     * Method getItensPedidoFrotass
     */
    public function getItensPedidoFrotass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_frotas_id', '=', $this->id));
        return ItensPedidoFrotas::getObjects( $criteria );
    }
    /**
     * Method getPedidoFrotasHistoricos
     */
    public function getPedidoFrotasHistoricos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_frotas_id', '=', $this->id));
        return PedidoFrotasHistorico::getObjects( $criteria );
    }
    /**
     * Method getCidadePedidoFrotass
     */
    public function getCidadePedidoFrotass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_frotas_id', '=', $this->id));
        return CidadePedidoFrotas::getObjects( $criteria );
    }
    /**
     * Method getSeguimentoPedidoFrotass
     */
    public function getSeguimentoPedidoFrotass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_frotas_id', '=', $this->id));
        return SeguimentoPedidoFrotas::getObjects( $criteria );
    }
    /**
     * Method getPropostass
     */
    public function getPropostass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_frotas_id', '=', $this->id));
        return Propostas::getObjects( $criteria );
    }
    /**
     * Method getContas
     */
    public function getContas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_frotas_id', '=', $this->id));
        return Conta::getObjects( $criteria );
    }
    /**
     * Method getDocumentosPedidoFrotass
     */
    public function getDocumentosPedidoFrotass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pedido_frotas_id', '=', $this->id));
        return DocumentosPedidoFrotas::getObjects( $criteria );
    }

    
    public function set_itens_pedido_frotas_pedido_frotas_to_string($itens_pedido_frotas_pedido_frotas_to_string)
    {
        if(is_array($itens_pedido_frotas_pedido_frotas_to_string))
        {
            $values = PedidoFrotas::where('id', 'in', $itens_pedido_frotas_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->itens_pedido_frotas_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_pedido_frotas_pedido_frotas_to_string = $itens_pedido_frotas_pedido_frotas_to_string;
        }

        $this->vdata['itens_pedido_frotas_pedido_frotas_to_string'] = $this->itens_pedido_frotas_pedido_frotas_to_string;
    }

    public function get_itens_pedido_frotas_pedido_frotas_to_string()
    {
        if(!empty($this->itens_pedido_frotas_pedido_frotas_to_string))
        {
            return $this->itens_pedido_frotas_pedido_frotas_to_string;
        }
        
        $values = ItensPedidoFrotas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_historico_pedido_frotas_to_string($pedido_frotas_historico_pedido_frotas_to_string)
    {
        if(is_array($pedido_frotas_historico_pedido_frotas_to_string))
        {
            $values = PedidoFrotas::where('id', 'in', $pedido_frotas_historico_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->pedido_frotas_historico_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_historico_pedido_frotas_to_string = $pedido_frotas_historico_pedido_frotas_to_string;
        }

        $this->vdata['pedido_frotas_historico_pedido_frotas_to_string'] = $this->pedido_frotas_historico_pedido_frotas_to_string;
    }

    public function get_pedido_frotas_historico_pedido_frotas_to_string()
    {
        if(!empty($this->pedido_frotas_historico_pedido_frotas_to_string))
        {
            return $this->pedido_frotas_historico_pedido_frotas_to_string;
        }
        
        $values = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_historico_aprovador_frotas_to_string($pedido_frotas_historico_aprovador_frotas_to_string)
    {
        if(is_array($pedido_frotas_historico_aprovador_frotas_to_string))
        {
            $values = AprovadorFrotas::where('id', 'in', $pedido_frotas_historico_aprovador_frotas_to_string)->getIndexedArray('id', 'id');
            $this->pedido_frotas_historico_aprovador_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_historico_aprovador_frotas_to_string = $pedido_frotas_historico_aprovador_frotas_to_string;
        }

        $this->vdata['pedido_frotas_historico_aprovador_frotas_to_string'] = $this->pedido_frotas_historico_aprovador_frotas_to_string;
    }

    public function get_pedido_frotas_historico_aprovador_frotas_to_string()
    {
        if(!empty($this->pedido_frotas_historico_aprovador_frotas_to_string))
        {
            return $this->pedido_frotas_historico_aprovador_frotas_to_string;
        }
        
        $values = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('aprovador_frotas_id','{aprovador_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_historico_estado_pedido_frotas_to_string($pedido_frotas_historico_estado_pedido_frotas_to_string)
    {
        if(is_array($pedido_frotas_historico_estado_pedido_frotas_to_string))
        {
            $values = EstadoPedidoFrotas::where('id', 'in', $pedido_frotas_historico_estado_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->pedido_frotas_historico_estado_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_frotas_historico_estado_pedido_frotas_to_string = $pedido_frotas_historico_estado_pedido_frotas_to_string;
        }

        $this->vdata['pedido_frotas_historico_estado_pedido_frotas_to_string'] = $this->pedido_frotas_historico_estado_pedido_frotas_to_string;
    }

    public function get_pedido_frotas_historico_estado_pedido_frotas_to_string()
    {
        if(!empty($this->pedido_frotas_historico_estado_pedido_frotas_to_string))
        {
            return $this->pedido_frotas_historico_estado_pedido_frotas_to_string;
        }
        
        $values = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_cidade_pedido_frotas_pedido_frotas_to_string($cidade_pedido_frotas_pedido_frotas_to_string)
    {
        if(is_array($cidade_pedido_frotas_pedido_frotas_to_string))
        {
            $values = PedidoFrotas::where('id', 'in', $cidade_pedido_frotas_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->cidade_pedido_frotas_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->cidade_pedido_frotas_pedido_frotas_to_string = $cidade_pedido_frotas_pedido_frotas_to_string;
        }

        $this->vdata['cidade_pedido_frotas_pedido_frotas_to_string'] = $this->cidade_pedido_frotas_pedido_frotas_to_string;
    }

    public function get_cidade_pedido_frotas_pedido_frotas_to_string()
    {
        if(!empty($this->cidade_pedido_frotas_pedido_frotas_to_string))
        {
            return $this->cidade_pedido_frotas_pedido_frotas_to_string;
        }
        
        $values = CidadePedidoFrotas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_cidade_pedido_frotas_cidade_to_string($cidade_pedido_frotas_cidade_to_string)
    {
        if(is_array($cidade_pedido_frotas_cidade_to_string))
        {
            $values = Cidade::where('id', 'in', $cidade_pedido_frotas_cidade_to_string)->getIndexedArray('nome', 'nome');
            $this->cidade_pedido_frotas_cidade_to_string = implode(', ', $values);
        }
        else
        {
            $this->cidade_pedido_frotas_cidade_to_string = $cidade_pedido_frotas_cidade_to_string;
        }

        $this->vdata['cidade_pedido_frotas_cidade_to_string'] = $this->cidade_pedido_frotas_cidade_to_string;
    }

    public function get_cidade_pedido_frotas_cidade_to_string()
    {
        if(!empty($this->cidade_pedido_frotas_cidade_to_string))
        {
            return $this->cidade_pedido_frotas_cidade_to_string;
        }
        
        $values = CidadePedidoFrotas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('cidade_id','{cidade->nome}');
        return implode(', ', $values);
    }

    
    public function set_seguimento_pedido_frotas_pedido_frotas_to_string($seguimento_pedido_frotas_pedido_frotas_to_string)
    {
        if(is_array($seguimento_pedido_frotas_pedido_frotas_to_string))
        {
            $values = PedidoFrotas::where('id', 'in', $seguimento_pedido_frotas_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->seguimento_pedido_frotas_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->seguimento_pedido_frotas_pedido_frotas_to_string = $seguimento_pedido_frotas_pedido_frotas_to_string;
        }

        $this->vdata['seguimento_pedido_frotas_pedido_frotas_to_string'] = $this->seguimento_pedido_frotas_pedido_frotas_to_string;
    }

    public function get_seguimento_pedido_frotas_pedido_frotas_to_string()
    {
        if(!empty($this->seguimento_pedido_frotas_pedido_frotas_to_string))
        {
            return $this->seguimento_pedido_frotas_pedido_frotas_to_string;
        }
        
        $values = SeguimentoPedidoFrotas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_seguimento_pedido_frotas_seguimento_to_string($seguimento_pedido_frotas_seguimento_to_string)
    {
        if(is_array($seguimento_pedido_frotas_seguimento_to_string))
        {
            $values = Seguimento::where('id', 'in', $seguimento_pedido_frotas_seguimento_to_string)->getIndexedArray('id', 'id');
            $this->seguimento_pedido_frotas_seguimento_to_string = implode(', ', $values);
        }
        else
        {
            $this->seguimento_pedido_frotas_seguimento_to_string = $seguimento_pedido_frotas_seguimento_to_string;
        }

        $this->vdata['seguimento_pedido_frotas_seguimento_to_string'] = $this->seguimento_pedido_frotas_seguimento_to_string;
    }

    public function get_seguimento_pedido_frotas_seguimento_to_string()
    {
        if(!empty($this->seguimento_pedido_frotas_seguimento_to_string))
        {
            return $this->seguimento_pedido_frotas_seguimento_to_string;
        }
        
        $values = SeguimentoPedidoFrotas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('seguimento_id','{seguimento->id}');
        return implode(', ', $values);
    }

    
    public function set_propostas_pedido_frotas_to_string($propostas_pedido_frotas_to_string)
    {
        if(is_array($propostas_pedido_frotas_to_string))
        {
            $values = PedidoFrotas::where('id', 'in', $propostas_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->propostas_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_pedido_frotas_to_string = $propostas_pedido_frotas_to_string;
        }

        $this->vdata['propostas_pedido_frotas_to_string'] = $this->propostas_pedido_frotas_to_string;
    }

    public function get_propostas_pedido_frotas_to_string()
    {
        if(!empty($this->propostas_pedido_frotas_to_string))
        {
            return $this->propostas_pedido_frotas_to_string;
        }
        
        $values = Propostas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_propostas_pessoa_to_string($propostas_pessoa_to_string)
    {
        if(is_array($propostas_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $propostas_pessoa_to_string)->getIndexedArray('nome', 'nome');
            $this->propostas_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_pessoa_to_string = $propostas_pessoa_to_string;
        }

        $this->vdata['propostas_pessoa_to_string'] = $this->propostas_pessoa_to_string;
    }

    public function get_propostas_pessoa_to_string()
    {
        if(!empty($this->propostas_pessoa_to_string))
        {
            return $this->propostas_pessoa_to_string;
        }
        
        $values = Propostas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
        return implode(', ', $values);
    }

    
    public function set_propostas_estado_pedido_frotas_to_string($propostas_estado_pedido_frotas_to_string)
    {
        if(is_array($propostas_estado_pedido_frotas_to_string))
        {
            $values = EstadoPedidoFrotas::where('id', 'in', $propostas_estado_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->propostas_estado_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_estado_pedido_frotas_to_string = $propostas_estado_pedido_frotas_to_string;
        }

        $this->vdata['propostas_estado_pedido_frotas_to_string'] = $this->propostas_estado_pedido_frotas_to_string;
    }

    public function get_propostas_estado_pedido_frotas_to_string()
    {
        if(!empty($this->propostas_estado_pedido_frotas_to_string))
        {
            return $this->propostas_estado_pedido_frotas_to_string;
        }
        
        $values = Propostas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_propostas_veiculos_to_string($propostas_veiculos_to_string)
    {
        if(is_array($propostas_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $propostas_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->propostas_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_veiculos_to_string = $propostas_veiculos_to_string;
        }

        $this->vdata['propostas_veiculos_to_string'] = $this->propostas_veiculos_to_string;
    }

    public function get_propostas_veiculos_to_string()
    {
        if(!empty($this->propostas_veiculos_to_string))
        {
            return $this->propostas_veiculos_to_string;
        }
        
        $values = Propostas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    
    public function set_propostas_system_unit_to_string($propostas_system_unit_to_string)
    {
        if(is_array($propostas_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $propostas_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->propostas_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_system_unit_to_string = $propostas_system_unit_to_string;
        }

        $this->vdata['propostas_system_unit_to_string'] = $this->propostas_system_unit_to_string;
    }

    public function get_propostas_system_unit_to_string()
    {
        if(!empty($this->propostas_system_unit_to_string))
        {
            return $this->propostas_system_unit_to_string;
        }
        
        $values = Propostas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_propostas_departamento_unit_to_string($propostas_departamento_unit_to_string)
    {
        if(is_array($propostas_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $propostas_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->propostas_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_departamento_unit_to_string = $propostas_departamento_unit_to_string;
        }

        $this->vdata['propostas_departamento_unit_to_string'] = $this->propostas_departamento_unit_to_string;
    }

    public function get_propostas_departamento_unit_to_string()
    {
        if(!empty($this->propostas_departamento_unit_to_string))
        {
            return $this->propostas_departamento_unit_to_string;
        }
        
        $values = Propostas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_propostas_system_users_to_string($propostas_system_users_to_string)
    {
        if(is_array($propostas_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $propostas_system_users_to_string)->getIndexedArray('name', 'name');
            $this->propostas_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_system_users_to_string = $propostas_system_users_to_string;
        }

        $this->vdata['propostas_system_users_to_string'] = $this->propostas_system_users_to_string;
    }

    public function get_propostas_system_users_to_string()
    {
        if(!empty($this->propostas_system_users_to_string))
        {
            return $this->propostas_system_users_to_string;
        }
        
        $values = Propostas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
        
        $values = Conta::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
        
        $values = Conta::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('tipo_conta_id','{tipo_conta->nome}');
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
        
        $values = Conta::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('categoria_id','{categoria->nome}');
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
        
        $values = Conta::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('forma_pagamento_id','{forma_pagamento->nome}');
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
        
        $values = Conta::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('pedido_venda_id','{pedido_venda->id}');
        return implode(', ', $values);
    }

    
    public function set_conta_pedido_frotas_to_string($conta_pedido_frotas_to_string)
    {
        if(is_array($conta_pedido_frotas_to_string))
        {
            $values = PedidoFrotas::where('id', 'in', $conta_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->conta_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_pedido_frotas_to_string = $conta_pedido_frotas_to_string;
        }

        $this->vdata['conta_pedido_frotas_to_string'] = $this->conta_pedido_frotas_to_string;
    }

    public function get_conta_pedido_frotas_to_string()
    {
        if(!empty($this->conta_pedido_frotas_to_string))
        {
            return $this->conta_pedido_frotas_to_string;
        }
        
        $values = Conta::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_conta_system_unit_to_string($conta_system_unit_to_string)
    {
        if(is_array($conta_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $conta_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->conta_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_system_unit_to_string = $conta_system_unit_to_string;
        }

        $this->vdata['conta_system_unit_to_string'] = $this->conta_system_unit_to_string;
    }

    public function get_conta_system_unit_to_string()
    {
        if(!empty($this->conta_system_unit_to_string))
        {
            return $this->conta_system_unit_to_string;
        }
        
        $values = Conta::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
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
        
        $values = Conta::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
        
        $values = Conta::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    
    public function set_documentos_pedido_frotas_pedido_frotas_to_string($documentos_pedido_frotas_pedido_frotas_to_string)
    {
        if(is_array($documentos_pedido_frotas_pedido_frotas_to_string))
        {
            $values = PedidoFrotas::where('id', 'in', $documentos_pedido_frotas_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->documentos_pedido_frotas_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->documentos_pedido_frotas_pedido_frotas_to_string = $documentos_pedido_frotas_pedido_frotas_to_string;
        }

        $this->vdata['documentos_pedido_frotas_pedido_frotas_to_string'] = $this->documentos_pedido_frotas_pedido_frotas_to_string;
    }

    public function get_documentos_pedido_frotas_pedido_frotas_to_string()
    {
        if(!empty($this->documentos_pedido_frotas_pedido_frotas_to_string))
        {
            return $this->documentos_pedido_frotas_pedido_frotas_to_string;
        }
        
        $values = DocumentosPedidoFrotas::where('pedido_frotas_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    //<userCustomFunctions>
    
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
    
        //</userCustomFunctions>
}
