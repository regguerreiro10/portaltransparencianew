<?php

//<fileHeader>
  
//</fileHeader>

class Pessoa extends TRecord
{
    const TABLENAME  = 'pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private $system_users;
    private $system_unit;
    private $tipo_cliente;
    private $categoria_cliente;
    private $system_user;
    
    //<classProperties>
  
    //</classProperties>
    
    use SystemChangeLogTrait;
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        //<onBeforeConstruct>
  
        //</onBeforeConstruct>
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_cliente_id');
        parent::addAttribute('categoria_cliente_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('nome');
        parent::addAttribute('documento');
        parent::addAttribute('obs');
        parent::addAttribute('fone');
        parent::addAttribute('email');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('login');
        parent::addAttribute('senha');
        parent::addAttribute('deleted_at');
        parent::addAttribute('banco');
        parent::addAttribute('agencia');
        parent::addAttribute('conta');
        parent::addAttribute('operacao');
        parent::addAttribute('favorecido');
        parent::addAttribute('chavepix');
        parent::addAttribute('tipochavepix');
        parent::addAttribute('taxaadm');
        parent::addAttribute('taxabancaria');
        parent::addAttribute('taxaantecipacao');
        parent::addAttribute('taxacontrato');
        parent::addAttribute('abrirpedido');
        parent::addAttribute('cidade_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('optante');
        parent::addAttribute('ir');
        parent::addAttribute('csll');
        parent::addAttribute('confins');
        parent::addAttribute('pis');
        parent::addAttribute('iss');
        parent::addAttribute('ir_servico');
        parent::addAttribute('csll_servico');
        parent::addAttribute('confins_servico');
        parent::addAttribute('pis_servico');
        parent::addAttribute('iss_servico');      
        parent::addAttribute('data_emissao_cnh');
        parent::addAttribute('data_validade_cnh');
        parent::addAttribute('numero_registro_cnh');
        parent::addAttribute('numero_registro');
        parent::addAttribute('categoria_cnh_id');
        parent::addAttribute('rg');
        parent::addAttribute('cpf');
        parent::addAttribute('ativo');
        parent::addAttribute('selo');
        parent::addAttribute('data_desativacao');
        parent::addAttribute('horariofuncionamento');
        parent::addAttribute('idold');

        //<onAfterConstruct>
  
        //</onAfterConstruct>
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
     * Method set_tipo_cliente
     * Sample of usage: $var->tipo_cliente = $object;
     * @param $object Instance of TipoCliente
     */
    public function set_tipo_cliente(TipoCliente $object)
    {
        $this->tipo_cliente = $object;
        $this->tipo_cliente_id = $object->id;
    }
    
    /**
     * Method get_tipo_cliente
     * Sample of usage: $var->tipo_cliente->attribute;
     * @returns TipoCliente instance
     */
    public function get_tipo_cliente()
    {
        
        // loads the associated object
        if (empty($this->tipo_cliente))
            $this->tipo_cliente = new TipoCliente($this->tipo_cliente_id);
        
        // returns the associated object
        return $this->tipo_cliente;
    }
    /**
     * Method set_categoria_cliente
     * Sample of usage: $var->categoria_cliente = $object;
     * @param $object Instance of CategoriaCliente
     */
    public function set_categoria_cliente(CategoriaCliente $object)
    {
        $this->categoria_cliente = $object;
        $this->categoria_cliente_id = $object->id;
    }
    
    /**
     * Method get_categoria_cliente
     * Sample of usage: $var->categoria_cliente->attribute;
     * @returns CategoriaCliente instance
     */
    public function get_categoria_cliente()
    {
        
        // loads the associated object
        if (empty($this->categoria_cliente))
            $this->categoria_cliente = new CategoriaCliente($this->categoria_cliente_id);
        
        // returns the associated object
        return $this->categoria_cliente;
    }
    /**
     * Method set_system_users
     * Sample of usage: $var->system_users = $object;
     * @param $object Instance of SystemUsers
     */
    public function set_system_user(SystemUsers $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $var->system_user->attribute;
     * @returns SystemUsers instance
     */
    public function get_system_user()
    {
        
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUsers($this->system_user_id);
        
        // returns the associated object
        return $this->system_user;
    }
    
    /**
     * Method getPropostass
     */
    public function getPropostass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return Propostas::getObjects( $criteria );
    }
    /**
     * Method getAbastecimentoss
     */
    public function getAbastecimentoss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estabelecimento_id', '=', $this->id));
        return Abastecimentos::getObjects( $criteria );
    }
    /**
     * Method getContas
     */
    public function getContas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return Conta::getObjects( $criteria );
    }
    /**
     * Method getCotacaos
     */
    public function getCotacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return Cotacao::getObjects( $criteria );
    }
    /**
     * Method getNegociacaos
     */
    public function getNegociacaosByClientes()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cliente_id', '=', $this->id));
        return Negociacao::getObjects( $criteria );
    }
    /**
     * Method getNegociacaos
     */
    public function getNegociacaosByVendedors()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('vendedor_id', '=', $this->id));
        return Negociacao::getObjects( $criteria );
    }
    /**
     * Method getPedidos
     */
    public function getPedidosByClientes()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cliente_id', '=', $this->id));
        return Pedido::getObjects( $criteria );
    }
    /**
     * Method getPedidos
     */
    public function getPedidosByVendedors()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('vendedor_id', '=', $this->id));
        return Pedido::getObjects( $criteria );
    }
    /**
     * Method getPedidos
     */
    public function getPedidosByTransportadoras()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('transportadora_id', '=', $this->id));
        return Pedido::getObjects( $criteria );
    }
    /**
     * Method getPessoaContatos
     */
    public function getPessoaContatos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return PessoaContato::getObjects( $criteria );
    }
    /**
     * Method getPessoaDepartamentos
     */
    public function getPessoaDepartamentos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return PessoaDepartamento::getObjects( $criteria );
    }
    /**
     * Method getPessoaEnderecos
     */
    public function getPessoaEnderecos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return PessoaEndereco::getObjects( $criteria );
    }
    /**
     * Method getPessoaGrupos
     */
    public function getPessoaGrupos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return PessoaGrupo::getObjects( $criteria );
    }
    /**
     * Method getProdutos
     */
    public function getProdutos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('fornecedor_id', '=', $this->id));
        return Produto::getObjects( $criteria );
    }
    /**
     * Method getSeguimentoPessoas
     */
    public function getSeguimentoPessoas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return SeguimentoPessoa::getObjects( $criteria );
    }
    /**
     * Method getPedidoFrotass
     */
    public function getPedidoFrotass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estabelecimento_id', '=', $this->id));
        return PedidoFrotas::getObjects( $criteria );
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
        
        $values = Propostas::where('pessoa_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
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
        
        $values = Propostas::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
        
        $values = Propostas::where('pessoa_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_propostas_motorista_entrada_to_string($propostas_motorista_entrada_to_string)
    {
        if(is_array($propostas_motorista_entrada_to_string))
        {
            $values = Condutor::where('id', 'in', $propostas_motorista_entrada_to_string)->getIndexedArray('id', 'id');
            $this->propostas_motorista_entrada_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_motorista_entrada_to_string = $propostas_motorista_entrada_to_string;
        }

        $this->vdata['propostas_motorista_entrada_to_string'] = $this->propostas_motorista_entrada_to_string;
    }

    public function get_propostas_motorista_entrada_to_string()
    {
        if(!empty($this->propostas_motorista_entrada_to_string))
        {
            return $this->propostas_motorista_entrada_to_string;
        }
        
        $values = Propostas::where('pessoa_id', '=', $this->id)->getIndexedArray('motorista_entrada_id','{motorista_entrada->id}');
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
        
        $values = Propostas::where('pessoa_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
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
        
        $values = Propostas::where('pessoa_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
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
        
        $values = Propostas::where('pessoa_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
        
        $values = Propostas::where('pessoa_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    
    public function set_propostas_motorista_retirada_to_string($propostas_motorista_retirada_to_string)
    {
        if(is_array($propostas_motorista_retirada_to_string))
        {
            $values = Condutor::where('id', 'in', $propostas_motorista_retirada_to_string)->getIndexedArray('id', 'id');
            $this->propostas_motorista_retirada_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_motorista_retirada_to_string = $propostas_motorista_retirada_to_string;
        }

        $this->vdata['propostas_motorista_retirada_to_string'] = $this->propostas_motorista_retirada_to_string;
    }

    public function get_propostas_motorista_retirada_to_string()
    {
        if(!empty($this->propostas_motorista_retirada_to_string))
        {
            return $this->propostas_motorista_retirada_to_string;
        }
        
        $values = Propostas::where('pessoa_id', '=', $this->id)->getIndexedArray('motorista_retirada_id','{motorista_retirada->id}');
        return implode(', ', $values);
    }

    
    public function set_abastecimentos_estabelecimento_to_string($abastecimentos_estabelecimento_to_string)
    {
        if(is_array($abastecimentos_estabelecimento_to_string))
        {
            $values = Pessoa::where('id', 'in', $abastecimentos_estabelecimento_to_string)->getIndexedArray('nome', 'nome');
            $this->abastecimentos_estabelecimento_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_estabelecimento_to_string = $abastecimentos_estabelecimento_to_string;
        }

        $this->vdata['abastecimentos_estabelecimento_to_string'] = $this->abastecimentos_estabelecimento_to_string;
    }

    public function get_abastecimentos_estabelecimento_to_string()
    {
        if(!empty($this->abastecimentos_estabelecimento_to_string))
        {
            return $this->abastecimentos_estabelecimento_to_string;
        }
        
        $values = Abastecimentos::where('estabelecimento_id', '=', $this->id)->getIndexedArray('estabelecimento_id','{estabelecimento->nome}');
        return implode(', ', $values);
    }

    
    public function set_abastecimentos_condutor_to_string($abastecimentos_condutor_to_string)
    {
        if(is_array($abastecimentos_condutor_to_string))
        {
            $values = Condutor::where('id', 'in', $abastecimentos_condutor_to_string)->getIndexedArray('id', 'id');
            $this->abastecimentos_condutor_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_condutor_to_string = $abastecimentos_condutor_to_string;
        }

        $this->vdata['abastecimentos_condutor_to_string'] = $this->abastecimentos_condutor_to_string;
    }

    public function get_abastecimentos_condutor_to_string()
    {
        if(!empty($this->abastecimentos_condutor_to_string))
        {
            return $this->abastecimentos_condutor_to_string;
        }
        
        $values = Abastecimentos::where('estabelecimento_id', '=', $this->id)->getIndexedArray('condutor_id','{condutor->id}');
        return implode(', ', $values);
    }

    
    public function set_abastecimentos_veiculos_to_string($abastecimentos_veiculos_to_string)
    {
        if(is_array($abastecimentos_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $abastecimentos_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->abastecimentos_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_veiculos_to_string = $abastecimentos_veiculos_to_string;
        }

        $this->vdata['abastecimentos_veiculos_to_string'] = $this->abastecimentos_veiculos_to_string;
    }

    public function get_abastecimentos_veiculos_to_string()
    {
        if(!empty($this->abastecimentos_veiculos_to_string))
        {
            return $this->abastecimentos_veiculos_to_string;
        }
        
        $values = Abastecimentos::where('estabelecimento_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    
    public function set_abastecimentos_system_unit_to_string($abastecimentos_system_unit_to_string)
    {
        if(is_array($abastecimentos_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $abastecimentos_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->abastecimentos_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_system_unit_to_string = $abastecimentos_system_unit_to_string;
        }

        $this->vdata['abastecimentos_system_unit_to_string'] = $this->abastecimentos_system_unit_to_string;
    }

    public function get_abastecimentos_system_unit_to_string()
    {
        if(!empty($this->abastecimentos_system_unit_to_string))
        {
            return $this->abastecimentos_system_unit_to_string;
        }
        
        $values = Abastecimentos::where('estabelecimento_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_abastecimentos_departamento_unit_to_string($abastecimentos_departamento_unit_to_string)
    {
        if(is_array($abastecimentos_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $abastecimentos_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->abastecimentos_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_departamento_unit_to_string = $abastecimentos_departamento_unit_to_string;
        }

        $this->vdata['abastecimentos_departamento_unit_to_string'] = $this->abastecimentos_departamento_unit_to_string;
    }

    public function get_abastecimentos_departamento_unit_to_string()
    {
        if(!empty($this->abastecimentos_departamento_unit_to_string))
        {
            return $this->abastecimentos_departamento_unit_to_string;
        }
        
        $values = Abastecimentos::where('estabelecimento_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_abastecimentos_system_users_to_string($abastecimentos_system_users_to_string)
    {
        if(is_array($abastecimentos_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $abastecimentos_system_users_to_string)->getIndexedArray('name', 'name');
            $this->abastecimentos_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->abastecimentos_system_users_to_string = $abastecimentos_system_users_to_string;
        }

        $this->vdata['abastecimentos_system_users_to_string'] = $this->abastecimentos_system_users_to_string;
    }

    public function get_abastecimentos_system_users_to_string()
    {
        if(!empty($this->abastecimentos_system_users_to_string))
        {
            return $this->abastecimentos_system_users_to_string;
        }
        
        $values = Abastecimentos::where('estabelecimento_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
        
        $values = Conta::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
        
        $values = Conta::where('pessoa_id', '=', $this->id)->getIndexedArray('tipo_conta_id','{tipo_conta->nome}');
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
        
        $values = Conta::where('pessoa_id', '=', $this->id)->getIndexedArray('categoria_id','{categoria->nome}');
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
        
        $values = Conta::where('pessoa_id', '=', $this->id)->getIndexedArray('forma_pagamento_id','{forma_pagamento->nome}');
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
        
        $values = Conta::where('pessoa_id', '=', $this->id)->getIndexedArray('pedido_venda_id','{pedido_venda->id}');
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
        
        $values = Conta::where('pessoa_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
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
        
        $values = Conta::where('pessoa_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
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
        
        $values = Conta::where('pessoa_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
        
        $values = Conta::where('pessoa_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
        
        $values = Cotacao::where('pessoa_id', '=', $this->id)->getIndexedArray('pedido_id','{pedido->id}');
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
        
        $values = Cotacao::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
        
        $values = Cotacao::where('pessoa_id', '=', $this->id)->getIndexedArray('estado_pedido_id','{estado_pedido->nome}');
        return implode(', ', $values);
    }

    
    public function set_cotacao_system_unit_to_string($cotacao_system_unit_to_string)
    {
        if(is_array($cotacao_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $cotacao_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->cotacao_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->cotacao_system_unit_to_string = $cotacao_system_unit_to_string;
        }

        $this->vdata['cotacao_system_unit_to_string'] = $this->cotacao_system_unit_to_string;
    }

    public function get_cotacao_system_unit_to_string()
    {
        if(!empty($this->cotacao_system_unit_to_string))
        {
            return $this->cotacao_system_unit_to_string;
        }
        
        $values = Cotacao::where('pessoa_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_cotacao_departamento_unit_to_string($cotacao_departamento_unit_to_string)
    {
        if(is_array($cotacao_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $cotacao_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->cotacao_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->cotacao_departamento_unit_to_string = $cotacao_departamento_unit_to_string;
        }

        $this->vdata['cotacao_departamento_unit_to_string'] = $this->cotacao_departamento_unit_to_string;
    }

    public function get_cotacao_departamento_unit_to_string()
    {
        if(!empty($this->cotacao_departamento_unit_to_string))
        {
            return $this->cotacao_departamento_unit_to_string;
        }
        
        $values = Cotacao::where('pessoa_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
        
        $values = Cotacao::where('pessoa_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
        
        $values = Negociacao::where('vendedor_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->nome}');
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
        
        $values = Negociacao::where('vendedor_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->nome}');
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
        
        $values = Negociacao::where('vendedor_id', '=', $this->id)->getIndexedArray('origem_contato_id','{origem_contato->nome}');
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
        
        $values = Negociacao::where('vendedor_id', '=', $this->id)->getIndexedArray('etapa_negociacao_id','{etapa_negociacao->nome}');
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
        
        $values = Negociacao::where('vendedor_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
        
        $values = Negociacao::where('vendedor_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('tipo_pedido_id','{tipo_pedido->nome}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->nome}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->nome}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('estado_pedido_venda_id','{estado_pedido_venda->nome}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('condicao_pagamento_id','{condicao_pagamento->nome}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('transportadora_id','{transportadora->nome}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('negociacao_id','{negociacao->id}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('situacao_pedido_id','{situacao_pedido->nome}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('centrocusto_id','{centrocusto->nome}');
        return implode(', ', $values);
    }

    
    public function set_pedido_system_unit_to_string($pedido_system_unit_to_string)
    {
        if(is_array($pedido_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $pedido_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->pedido_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->pedido_system_unit_to_string = $pedido_system_unit_to_string;
        }

        $this->vdata['pedido_system_unit_to_string'] = $this->pedido_system_unit_to_string;
    }

    public function get_pedido_system_unit_to_string()
    {
        if(!empty($this->pedido_system_unit_to_string))
        {
            return $this->pedido_system_unit_to_string;
        }
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
        
        $values = Pedido::where('transportadora_id', '=', $this->id)->getIndexedArray('cartao_id','{cartao->id}');
        return implode(', ', $values);
    }

    
    public function set_pessoa_contato_pessoa_to_string($pessoa_contato_pessoa_to_string)
    {
        if(is_array($pessoa_contato_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $pessoa_contato_pessoa_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_contato_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_contato_pessoa_to_string = $pessoa_contato_pessoa_to_string;
        }

        $this->vdata['pessoa_contato_pessoa_to_string'] = $this->pessoa_contato_pessoa_to_string;
    }

    public function get_pessoa_contato_pessoa_to_string()
    {
        if(!empty($this->pessoa_contato_pessoa_to_string))
        {
            return $this->pessoa_contato_pessoa_to_string;
        }
        
        $values = PessoaContato::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
        
        $values = PessoaDepartamento::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
        
        $values = PessoaDepartamento::where('pessoa_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_pessoa_endereco_pessoa_to_string($pessoa_endereco_pessoa_to_string)
    {
        if(is_array($pessoa_endereco_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $pessoa_endereco_pessoa_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_endereco_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_endereco_pessoa_to_string = $pessoa_endereco_pessoa_to_string;
        }

        $this->vdata['pessoa_endereco_pessoa_to_string'] = $this->pessoa_endereco_pessoa_to_string;
    }

    public function get_pessoa_endereco_pessoa_to_string()
    {
        if(!empty($this->pessoa_endereco_pessoa_to_string))
        {
            return $this->pessoa_endereco_pessoa_to_string;
        }
        
        $values = PessoaEndereco::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
        return implode(', ', $values);
    }

    
    public function set_pessoa_endereco_cidade_to_string($pessoa_endereco_cidade_to_string)
    {
        if(is_array($pessoa_endereco_cidade_to_string))
        {
            $values = Cidade::where('id', 'in', $pessoa_endereco_cidade_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_endereco_cidade_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_endereco_cidade_to_string = $pessoa_endereco_cidade_to_string;
        }

        $this->vdata['pessoa_endereco_cidade_to_string'] = $this->pessoa_endereco_cidade_to_string;
    }

    public function get_pessoa_endereco_cidade_to_string()
    {
        if(!empty($this->pessoa_endereco_cidade_to_string))
        {
            return $this->pessoa_endereco_cidade_to_string;
        }
        
        $values = PessoaEndereco::where('pessoa_id', '=', $this->id)->getIndexedArray('cidade_id','{cidade->nome}');
        return implode(', ', $values);
    }

    
    public function set_pessoa_grupo_pessoa_to_string($pessoa_grupo_pessoa_to_string)
    {
        if(is_array($pessoa_grupo_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $pessoa_grupo_pessoa_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_grupo_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_grupo_pessoa_to_string = $pessoa_grupo_pessoa_to_string;
        }

        $this->vdata['pessoa_grupo_pessoa_to_string'] = $this->pessoa_grupo_pessoa_to_string;
    }

    public function get_pessoa_grupo_pessoa_to_string()
    {
        if(!empty($this->pessoa_grupo_pessoa_to_string))
        {
            return $this->pessoa_grupo_pessoa_to_string;
        }
        
        $values = PessoaGrupo::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
        return implode(', ', $values);
    }

    
    public function set_pessoa_grupo_grupo_pessoa_to_string($pessoa_grupo_grupo_pessoa_to_string)
    {
        if(is_array($pessoa_grupo_grupo_pessoa_to_string))
        {
            $values = GrupoPessoa::where('id', 'in', $pessoa_grupo_grupo_pessoa_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_grupo_grupo_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_grupo_grupo_pessoa_to_string = $pessoa_grupo_grupo_pessoa_to_string;
        }

        $this->vdata['pessoa_grupo_grupo_pessoa_to_string'] = $this->pessoa_grupo_grupo_pessoa_to_string;
    }

    public function get_pessoa_grupo_grupo_pessoa_to_string()
    {
        if(!empty($this->pessoa_grupo_grupo_pessoa_to_string))
        {
            return $this->pessoa_grupo_grupo_pessoa_to_string;
        }
        
        $values = PessoaGrupo::where('pessoa_id', '=', $this->id)->getIndexedArray('grupo_pessoa_id','{grupo_pessoa->nome}');
        return implode(', ', $values);
    }

    
    public function set_produto_tipo_produto_to_string($produto_tipo_produto_to_string)
    {
        if(is_array($produto_tipo_produto_to_string))
        {
            $values = TipoProduto::where('id', 'in', $produto_tipo_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_tipo_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_tipo_produto_to_string = $produto_tipo_produto_to_string;
        }

        $this->vdata['produto_tipo_produto_to_string'] = $this->produto_tipo_produto_to_string;
    }

    public function get_produto_tipo_produto_to_string()
    {
        if(!empty($this->produto_tipo_produto_to_string))
        {
            return $this->produto_tipo_produto_to_string;
        }
        
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('tipo_produto_id','{tipo_produto->nome}');
        return implode(', ', $values);
    }

    
    public function set_produto_familia_produto_to_string($produto_familia_produto_to_string)
    {
        if(is_array($produto_familia_produto_to_string))
        {
            $values = FamiliaProduto::where('id', 'in', $produto_familia_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_familia_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_familia_produto_to_string = $produto_familia_produto_to_string;
        }

        $this->vdata['produto_familia_produto_to_string'] = $this->produto_familia_produto_to_string;
    }

    public function get_produto_familia_produto_to_string()
    {
        if(!empty($this->produto_familia_produto_to_string))
        {
            return $this->produto_familia_produto_to_string;
        }
        
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('familia_produto_id','{familia_produto->nome}');
        return implode(', ', $values);
    }

    
    public function set_produto_unidade_medida_to_string($produto_unidade_medida_to_string)
    {
        if(is_array($produto_unidade_medida_to_string))
        {
            $values = UnidadeMedida::where('id', 'in', $produto_unidade_medida_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_unidade_medida_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_unidade_medida_to_string = $produto_unidade_medida_to_string;
        }

        $this->vdata['produto_unidade_medida_to_string'] = $this->produto_unidade_medida_to_string;
    }

    public function get_produto_unidade_medida_to_string()
    {
        if(!empty($this->produto_unidade_medida_to_string))
        {
            return $this->produto_unidade_medida_to_string;
        }
        
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('unidade_medida_id','{unidade_medida->nome}');
        return implode(', ', $values);
    }

    
    public function set_produto_fornecedor_to_string($produto_fornecedor_to_string)
    {
        if(is_array($produto_fornecedor_to_string))
        {
            $values = Pessoa::where('id', 'in', $produto_fornecedor_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_fornecedor_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_fornecedor_to_string = $produto_fornecedor_to_string;
        }

        $this->vdata['produto_fornecedor_to_string'] = $this->produto_fornecedor_to_string;
    }

    public function get_produto_fornecedor_to_string()
    {
        if(!empty($this->produto_fornecedor_to_string))
        {
            return $this->produto_fornecedor_to_string;
        }
        
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('fornecedor_id','{fornecedor->nome}');
        return implode(', ', $values);
    }

    
    public function set_produto_fabricante_to_string($produto_fabricante_to_string)
    {
        if(is_array($produto_fabricante_to_string))
        {
            $values = Fabricante::where('id', 'in', $produto_fabricante_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_fabricante_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_fabricante_to_string = $produto_fabricante_to_string;
        }

        $this->vdata['produto_fabricante_to_string'] = $this->produto_fabricante_to_string;
    }

    public function get_produto_fabricante_to_string()
    {
        if(!empty($this->produto_fabricante_to_string))
        {
            return $this->produto_fabricante_to_string;
        }
        
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('fabricante_id','{fabricante->nome}');
        return implode(', ', $values);
    }

    
    public function set_produto_system_unit_to_string($produto_system_unit_to_string)
    {
        if(is_array($produto_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $produto_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->produto_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_system_unit_to_string = $produto_system_unit_to_string;
        }

        $this->vdata['produto_system_unit_to_string'] = $this->produto_system_unit_to_string;
    }

    public function get_produto_system_unit_to_string()
    {
        if(!empty($this->produto_system_unit_to_string))
        {
            return $this->produto_system_unit_to_string;
        }
        
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_produto_system_users_to_string($produto_system_users_to_string)
    {
        if(is_array($produto_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $produto_system_users_to_string)->getIndexedArray('name', 'name');
            $this->produto_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_system_users_to_string = $produto_system_users_to_string;
        }

        $this->vdata['produto_system_users_to_string'] = $this->produto_system_users_to_string;
    }

    public function get_produto_system_users_to_string()
    {
        if(!empty($this->produto_system_users_to_string))
        {
            return $this->produto_system_users_to_string;
        }
        
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
        
        $values = SeguimentoPessoa::where('pessoa_id', '=', $this->id)->getIndexedArray('seguimento_id','{seguimento->id}');
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
        
        $values = SeguimentoPessoa::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
        return implode(', ', $values);
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
        
        $values = PedidoFrotas::where('estabelecimento_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
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
        
        $values = PedidoFrotas::where('estabelecimento_id', '=', $this->id)->getIndexedArray('estabelecimento_id','{estabelecimento->nome}');
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
        
        $values = PedidoFrotas::where('estabelecimento_id', '=', $this->id)->getIndexedArray('niveltanque_id','{niveltanque->id}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_condutor_entrada_to_string($pedido_frotas_condutor_entrada_to_string)
    {
        if(is_array($pedido_frotas_condutor_entrada_to_string))
        {
            $values = Condutor::where('id', 'in', $pedido_frotas_condutor_entrada_to_string)->getIndexedArray('id', 'id');
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
        
        $values = PedidoFrotas::where('estabelecimento_id', '=', $this->id)->getIndexedArray('condutor_entrada_id','{condutor_entrada->id}');
        return implode(', ', $values);
    }

    
    public function set_pedido_frotas_condutor_retirada_to_string($pedido_frotas_condutor_retirada_to_string)
    {
        if(is_array($pedido_frotas_condutor_retirada_to_string))
        {
            $values = Condutor::where('id', 'in', $pedido_frotas_condutor_retirada_to_string)->getIndexedArray('id', 'id');
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
        
        $values = PedidoFrotas::where('estabelecimento_id', '=', $this->id)->getIndexedArray('condutor_retirada_id','{condutor_retirada->id}');
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
        
        $values = PedidoFrotas::where('estabelecimento_id', '=', $this->id)->getIndexedArray('tipo_manutencao_id','{tipo_manutencao->id}');
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
        
        $values = PedidoFrotas::where('estabelecimento_id', '=', $this->id)->getIndexedArray('negociacao_id','{negociacao->id}');
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
        
        $values = PedidoFrotas::where('estabelecimento_id', '=', $this->id)->getIndexedArray('condicao_pagamento_id','{condicao_pagamento->nome}');
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
        
        $values = PedidoFrotas::where('estabelecimento_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
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
        
        $values = PedidoFrotas::where('estabelecimento_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
        
        $values = PedidoFrotas::where('estabelecimento_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}


