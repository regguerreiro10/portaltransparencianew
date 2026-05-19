<?php

//<fileHeader>
  
//</fileHeader>

class Veiculos extends TRecord
{
    const TABLENAME  = 'veiculos';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private Modelo $modelo;
    private TipoVeiculo $tipo_veiculo;
    private TipoCombustivel $tipo_combustivel;
    private Dispositivos $dispositivos;
    private Propriedade $propriedade;
    private SystemUnit $system_unit;
    private Marca $marca;
    private Corveiculo $corveiculo;
    private DepartamentoUnit $departamento_unit;
    private SystemUsers $system_users;
    private StatusVeiculo $status_veiculo;
    private Especie $especie;
    private Familia $familia;
    private Pessoa $responsavel;

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
        parent::addAttribute('dispositivos_id');
        parent::addAttribute('prefixo');
        parent::addAttribute('placa');
        parent::addAttribute('marca_id');
        parent::addAttribute('modelo_id');
        parent::addAttribute('anof');
        parent::addAttribute('anom');
        parent::addAttribute('chassi');
        parent::addAttribute('renavam');
        parent::addAttribute('capacidade_tanque');
        parent::addAttribute('hodometroatual');
        parent::addAttribute('tipo_veiculo_id');
        parent::addAttribute('status');
        parent::addAttribute('valor_tabela_fipe');
        parent::addAttribute('identificacao');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('deleted_at');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('saldo_veiculo');
        parent::addAttribute('tipo_combustivel_id');
        parent::addAttribute('propriedade_id');
        parent::addAttribute('status_veiculo_id');
        parent::addAttribute('corveiculo_id');
        parent::addAttribute('especie_id');
        parent::addAttribute('familia_id');
        parent::addAttribute('responsavel_id');
        parent::addAttribute('ciclos');
        parent::addAttribute('codigo_patrimonio');
        parent::addAttribute('numero_dispositivo');

        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_modelo
     * Sample of usage: $var->modelo = $object;
     * @param $object Instance of Modelo
     */
    public function set_modelo(Modelo $object)
    {
        $this->modelo = $object;
        $this->modelo_id = $object->id;
    }
    
    /**
     * Method get_modelo
     * Sample of usage: $var->modelo->attribute;
     * @returns Modelo instance
     */
    public function get_modelo()
    {
        
        // loads the associated object
        if (empty($this->modelo))
            $this->modelo = new Modelo($this->modelo_id);
        
        // returns the associated object
        return $this->modelo;
    }
    /**
     * Method set_tipo_veiculo
     * Sample of usage: $var->tipo_veiculo = $object;
     * @param $object Instance of TipoVeiculo
     */
    public function set_tipo_veiculo(TipoVeiculo $object)
    {
        $this->tipo_veiculo = $object;
        $this->tipo_veiculo_id = $object->id;
    }
    
    /**
     * Method get_tipo_veiculo
     * Sample of usage: $var->tipo_veiculo->attribute;
     * @returns TipoVeiculo instance
     */
    public function get_tipo_veiculo()
    {
        
        // loads the associated object
        if (empty($this->tipo_veiculo))
            $this->tipo_veiculo = new TipoVeiculo($this->tipo_veiculo_id);
        
        // returns the associated object
        return $this->tipo_veiculo;
    }
    /**
     * Method set_tipo_combustivel
     * Sample of usage: $var->tipo_combustivel = $object;
     * @param $object Instance of TipoCombustivel
     */
    public function set_tipo_combustivel(TipoCombustivel $object)
    {
        $this->tipo_combustivel = $object;
        $this->tipo_combustivel_id = $object->id;
    }
    
    /**
     * Method get_tipo_combustivel
     * Sample of usage: $var->tipo_combustivel->attribute;
     * @returns TipoCombustivel instance
     */
    public function get_tipo_combustivel()
    {
        
        // loads the associated object
        if (empty($this->tipo_combustivel))
            $this->tipo_combustivel = new TipoCombustivel($this->tipo_combustivel_id);
        
        // returns the associated object
        return $this->tipo_combustivel;
    }
    /**
     * Method set_dispositivos
     * Sample of usage: $var->dispositivos = $object;
     * @param $object Instance of Dispositivos
     */
    public function set_dispositivos(Dispositivos $object)
    {
        $this->dispositivos = $object;
        $this->dispositivos_id = $object->id;
    }
    
    /**
     * Method get_dispositivos
     * Sample of usage: $var->dispositivos->attribute;
     * @returns Dispositivos instance
     */
    public function get_dispositivos()
    {
        
        // loads the associated object
        if (empty($this->dispositivos))
            $this->dispositivos = new Dispositivos($this->dispositivos_id);
        
        // returns the associated object
        return $this->dispositivos;
    }
    /**
     * Method set_propriedade
     * Sample of usage: $var->propriedade = $object;
     * @param $object Instance of Propriedade
     */
    public function set_propriedade(Propriedade $object)
    {
        $this->propriedade = $object;
        $this->propriedade_id = $object->id;
    }
    
    /**
     * Method get_propriedade
     * Sample of usage: $var->propriedade->attribute;
     * @returns Propriedade instance
     */
    public function get_propriedade()
    {
        
        // loads the associated object
        if (empty($this->propriedade))
            $this->propriedade = new Propriedade($this->propriedade_id);
        
        // returns the associated object
        return $this->propriedade;
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
     * Method set_marca
     * Sample of usage: $var->marca = $object;
     * @param $object Instance of Marca
     */
    public function set_marca(Marca $object)
    {
        $this->marca = $object;
        $this->marca_id = $object->id;
    }
    
    /**
     * Method get_marca
     * Sample of usage: $var->marca->attribute;
     * @returns Marca instance
     */
    public function get_marca()
    {
        
        // loads the associated object
        if (empty($this->marca))
            $this->marca = new Marca($this->marca_id);
        
        // returns the associated object
        return $this->marca;
    }
    /**
     * Method set_corveiculo
     * Sample of usage: $var->corveiculo = $object;
     * @param $object Instance of Corveiculo
     */
    public function set_corveiculo(Corveiculo $object)
    {
        $this->corveiculo = $object;
        $this->corveiculo_id = $object->id;
    }
    
    /**
     * Method get_corveiculo
     * Sample of usage: $var->corveiculo->attribute;
     * @returns Corveiculo instance
     */
    public function get_corveiculo()
    {
        
        // loads the associated object
        if (empty($this->corveiculo))
            $this->corveiculo = new Corveiculo($this->corveiculo_id);
        
        // returns the associated object
        return $this->corveiculo;
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
     * Method set_status_veiculo
     * Sample of usage: $var->status_veiculo = $object;
     * @param $object Instance of StatusVeiculo
     */
    public function set_status_veiculo(StatusVeiculo $object)
    {
        $this->status_veiculo = $object;
        $this->status_veiculo_id = $object->id;
    }
    
    /**
     * Method get_status_veiculo
     * Sample of usage: $var->status_veiculo->attribute;
     * @returns StatusVeiculo instance
     */
    public function get_status_veiculo()
    {
        
        // loads the associated object
        if (empty($this->status_veiculo))
            $this->status_veiculo = new StatusVeiculo($this->status_veiculo_id);
        
        // returns the associated object
        return $this->status_veiculo;
    }
    /**
     * Method set_especie
     * Sample of usage: $var->especie = $object;
     * @param $object Instance of Especie
     */
    public function set_especie(Especie $object)
    {
        $this->especie = $object;
        $this->especie_id = $object->id;
    }
    
    /**
     * Method get_especie
     * Sample of usage: $var->especie->attribute;
     * @returns Especie instance
     */
    public function get_especie()
    {
        
        // loads the associated object
        if (empty($this->especie))
            $this->especie = new Especie($this->especie_id);
        
        // returns the associated object
        return $this->especie;
    }
    /**
     * Method set_familia
     * Sample of usage: $var->familia = $object;
     * @param $object Instance of Familia
     */
    public function set_familia(Familia $object)
    {
        $this->familia = $object;
        $this->familia_id = $object->id;
    }
    
    /**
     * Method get_familia
     * Sample of usage: $var->familia->attribute;
     * @returns Familia instance
     */
    public function get_familia()
    {
        
        // loads the associated object
        if (empty($this->familia))
            $this->familia = new Familia($this->familia_id);
        
        // returns the associated object
        return $this->familia;
    }
    
    /**
     * Method getDispositivosSolicitadoss
     */
    public function getDispositivosSolicitadoss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('veiculos_id', '=', $this->id));
        return DispositivosSolicitados::getObjects( $criteria );
    }
    /**
     * Method getPropostass
     */
    public function getPropostass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('veiculos_id', '=', $this->id));
        return Propostas::getObjects( $criteria );
    }
    /**
     * Method getAbastecimentoss
     */
    public function getAbastecimentoss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('veiculos_id', '=', $this->id));
        return Abastecimentos::getObjects( $criteria );
    }
    /**
     * Method getAnexosVeiculos
     */
    public function getAnexosVeiculos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('veiculos_id', '=', $this->id));
        return AnexosVeiculo::getObjects( $criteria );
    }
    /**
     * Method getSaldoVeiculos
     */
    public function getSaldoVeiculos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('veiculos_id', '=', $this->id));
        return SaldoVeiculo::getObjects( $criteria );
    }
    /**
     * Method getFotosVeiculoss
     */
    public function getFotosVeiculoss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('veiculos_id', '=', $this->id));
        return FotosVeiculos::getObjects( $criteria );
    }
    /**
     * Method getManutencaoGarantias
     */
    public function getManutencaoGarantias()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('veiculos_id', '=', $this->id));
        return ManutencaoGarantia::getObjects( $criteria );
    }
    /**
     * Method getAutorizacaoPedidos
     */
    public function getAutorizacaoPedidos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('veiculos_id', '=', $this->id));
        return AutorizacaoPedido::getObjects( $criteria );
    }

    
    public function set_dispositivos_solicitados_dispositivos_to_string($dispositivos_solicitados_dispositivos_to_string)
    {
        if(is_array($dispositivos_solicitados_dispositivos_to_string))
        {
            $values = Dispositivos::where('id', 'in', $dispositivos_solicitados_dispositivos_to_string)->getIndexedArray('id', 'id');
            $this->dispositivos_solicitados_dispositivos_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_dispositivos_to_string = $dispositivos_solicitados_dispositivos_to_string;
        }

        $this->vdata['dispositivos_solicitados_dispositivos_to_string'] = $this->dispositivos_solicitados_dispositivos_to_string;
    }

    public function get_dispositivos_solicitados_dispositivos_to_string()
    {
        if(!empty($this->dispositivos_solicitados_dispositivos_to_string))
        {
            return $this->dispositivos_solicitados_dispositivos_to_string;
        }
        
        $values = DispositivosSolicitados::where('veiculos_id', '=', $this->id)->getIndexedArray('dispositivos_id','{dispositivos->id}');
        return implode(', ', $values);
    }

    
    public function set_dispositivos_solicitados_veiculos_to_string($dispositivos_solicitados_veiculos_to_string)
    {
        if(is_array($dispositivos_solicitados_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $dispositivos_solicitados_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->dispositivos_solicitados_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_veiculos_to_string = $dispositivos_solicitados_veiculos_to_string;
        }

        $this->vdata['dispositivos_solicitados_veiculos_to_string'] = $this->dispositivos_solicitados_veiculos_to_string;
    }

    public function get_dispositivos_solicitados_veiculos_to_string()
    {
        if(!empty($this->dispositivos_solicitados_veiculos_to_string))
        {
            return $this->dispositivos_solicitados_veiculos_to_string;
        }
        
        $values = DispositivosSolicitados::where('veiculos_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    
    public function set_dispositivos_solicitados_status_dispositivos_to_string($dispositivos_solicitados_status_dispositivos_to_string)
    {
        if(is_array($dispositivos_solicitados_status_dispositivos_to_string))
        {
            $values = StatusDispositivos::where('id', 'in', $dispositivos_solicitados_status_dispositivos_to_string)->getIndexedArray('id', 'id');
            $this->dispositivos_solicitados_status_dispositivos_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_status_dispositivos_to_string = $dispositivos_solicitados_status_dispositivos_to_string;
        }

        $this->vdata['dispositivos_solicitados_status_dispositivos_to_string'] = $this->dispositivos_solicitados_status_dispositivos_to_string;
    }

    public function get_dispositivos_solicitados_status_dispositivos_to_string()
    {
        if(!empty($this->dispositivos_solicitados_status_dispositivos_to_string))
        {
            return $this->dispositivos_solicitados_status_dispositivos_to_string;
        }
        
        $values = DispositivosSolicitados::where('veiculos_id', '=', $this->id)->getIndexedArray('status_dispositivos_id','{status_dispositivos->id}');
        return implode(', ', $values);
    }

    
    public function set_dispositivos_solicitados_system_unit_to_string($dispositivos_solicitados_system_unit_to_string)
    {
        if(is_array($dispositivos_solicitados_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $dispositivos_solicitados_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->dispositivos_solicitados_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_system_unit_to_string = $dispositivos_solicitados_system_unit_to_string;
        }

        $this->vdata['dispositivos_solicitados_system_unit_to_string'] = $this->dispositivos_solicitados_system_unit_to_string;
    }

    public function get_dispositivos_solicitados_system_unit_to_string()
    {
        if(!empty($this->dispositivos_solicitados_system_unit_to_string))
        {
            return $this->dispositivos_solicitados_system_unit_to_string;
        }
        
        $values = DispositivosSolicitados::where('veiculos_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_dispositivos_solicitados_departamento_unit_to_string($dispositivos_solicitados_departamento_unit_to_string)
    {
        if(is_array($dispositivos_solicitados_departamento_unit_to_string))
        {
            $values = DepartamentoUnit::where('id', 'in', $dispositivos_solicitados_departamento_unit_to_string)->getIndexedArray('name', 'name');
            $this->dispositivos_solicitados_departamento_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_departamento_unit_to_string = $dispositivos_solicitados_departamento_unit_to_string;
        }

        $this->vdata['dispositivos_solicitados_departamento_unit_to_string'] = $this->dispositivos_solicitados_departamento_unit_to_string;
    }

    public function get_dispositivos_solicitados_departamento_unit_to_string()
    {
        if(!empty($this->dispositivos_solicitados_departamento_unit_to_string))
        {
            return $this->dispositivos_solicitados_departamento_unit_to_string;
        }
        
        $values = DispositivosSolicitados::where('veiculos_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_dispositivos_solicitados_system_users_to_string($dispositivos_solicitados_system_users_to_string)
    {
        if(is_array($dispositivos_solicitados_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $dispositivos_solicitados_system_users_to_string)->getIndexedArray('name', 'name');
            $this->dispositivos_solicitados_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->dispositivos_solicitados_system_users_to_string = $dispositivos_solicitados_system_users_to_string;
        }

        $this->vdata['dispositivos_solicitados_system_users_to_string'] = $this->dispositivos_solicitados_system_users_to_string;
    }

    public function get_dispositivos_solicitados_system_users_to_string()
    {
        if(!empty($this->dispositivos_solicitados_system_users_to_string))
        {
            return $this->dispositivos_solicitados_system_users_to_string;
        }
        
        $values = DispositivosSolicitados::where('veiculos_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
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
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
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
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
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
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
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
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    
    public function set_propostas_motorista_entrada_to_string($propostas_motorista_entrada_to_string)
    {
        if(is_array($propostas_motorista_entrada_to_string))
        {
            $values = Pessoa::where('id', 'in', $propostas_motorista_entrada_to_string)->getIndexedArray('nome', 'nome');
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
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('motorista_entrada_id','{motorista_entrada->nome}');
        return implode(', ', $values);
    }

    
    public function set_propostas_entidade_to_string($propostas_entidade_to_string)
    {
        if(is_array($propostas_entidade_to_string))
        {
            $values = Entidade::where('id', 'in', $propostas_entidade_to_string)->getIndexedArray('id', 'id');
            $this->propostas_entidade_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_entidade_to_string = $propostas_entidade_to_string;
        }

        $this->vdata['propostas_entidade_to_string'] = $this->propostas_entidade_to_string;
    }

    public function get_propostas_entidade_to_string()
    {
        if(!empty($this->propostas_entidade_to_string))
        {
            return $this->propostas_entidade_to_string;
        }
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('entidade_id','{entidade->id}');
        return implode(', ', $values);
    }

    
    public function set_propostas_cidade_to_string($propostas_cidade_to_string)
    {
        if(is_array($propostas_cidade_to_string))
        {
            $values = Cidade::where('id', 'in', $propostas_cidade_to_string)->getIndexedArray('nome', 'nome');
            $this->propostas_cidade_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_cidade_to_string = $propostas_cidade_to_string;
        }

        $this->vdata['propostas_cidade_to_string'] = $this->propostas_cidade_to_string;
    }

    public function get_propostas_cidade_to_string()
    {
        if(!empty($this->propostas_cidade_to_string))
        {
            return $this->propostas_cidade_to_string;
        }
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('cidade_id','{cidade->nome}');
        return implode(', ', $values);
    }

    
    public function set_propostas_motorista_retirada_to_string($propostas_motorista_retirada_to_string)
    {
        if(is_array($propostas_motorista_retirada_to_string))
        {
            $values = Pessoa::where('id', 'in', $propostas_motorista_retirada_to_string)->getIndexedArray('nome', 'nome');
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
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('motorista_retirada_id','{motorista_retirada->nome}');
        return implode(', ', $values);
    }

    
    public function set_propostas_estado_pedido_frotas1_to_string($propostas_estado_pedido_frotas1_to_string)
    {
        if(is_array($propostas_estado_pedido_frotas1_to_string))
        {
            $values = EstadoPedidoFrotas::where('id', 'in', $propostas_estado_pedido_frotas1_to_string)->getIndexedArray('id', 'id');
            $this->propostas_estado_pedido_frotas1_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_estado_pedido_frotas1_to_string = $propostas_estado_pedido_frotas1_to_string;
        }

        $this->vdata['propostas_estado_pedido_frotas1_to_string'] = $this->propostas_estado_pedido_frotas1_to_string;
    }

    public function get_propostas_estado_pedido_frotas1_to_string()
    {
        if(!empty($this->propostas_estado_pedido_frotas1_to_string))
        {
            return $this->propostas_estado_pedido_frotas1_to_string;
        }
        
        $values = Propostas::where('veiculos_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas1_id','{estado_pedido_frotas1->id}');
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
        
        $values = Abastecimentos::where('veiculos_id', '=', $this->id)->getIndexedArray('estabelecimento_id','{estabelecimento->nome}');
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
        
        $values = Abastecimentos::where('veiculos_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
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
        
        $values = Abastecimentos::where('veiculos_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
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
        
        $values = Abastecimentos::where('veiculos_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
        
        $values = Abastecimentos::where('veiculos_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    
    public function set_anexos_veiculo_veiculos_to_string($anexos_veiculo_veiculos_to_string)
    {
        if(is_array($anexos_veiculo_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $anexos_veiculo_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->anexos_veiculo_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->anexos_veiculo_veiculos_to_string = $anexos_veiculo_veiculos_to_string;
        }

        $this->vdata['anexos_veiculo_veiculos_to_string'] = $this->anexos_veiculo_veiculos_to_string;
    }

    public function get_anexos_veiculo_veiculos_to_string()
    {
        if(!empty($this->anexos_veiculo_veiculos_to_string))
        {
            return $this->anexos_veiculo_veiculos_to_string;
        }
        
        $values = AnexosVeiculo::where('veiculos_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    
    public function set_saldo_veiculo_veiculos_to_string($saldo_veiculo_veiculos_to_string)
    {
        if(is_array($saldo_veiculo_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $saldo_veiculo_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->saldo_veiculo_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->saldo_veiculo_veiculos_to_string = $saldo_veiculo_veiculos_to_string;
        }

        $this->vdata['saldo_veiculo_veiculos_to_string'] = $this->saldo_veiculo_veiculos_to_string;
    }

    public function get_saldo_veiculo_veiculos_to_string()
    {
        if(!empty($this->saldo_veiculo_veiculos_to_string))
        {
            return $this->saldo_veiculo_veiculos_to_string;
        }
        
        $values = SaldoVeiculo::where('veiculos_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    
    public function set_fotos_veiculos_veiculos_to_string($fotos_veiculos_veiculos_to_string)
    {
        if(is_array($fotos_veiculos_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $fotos_veiculos_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->fotos_veiculos_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->fotos_veiculos_veiculos_to_string = $fotos_veiculos_veiculos_to_string;
        }

        $this->vdata['fotos_veiculos_veiculos_to_string'] = $this->fotos_veiculos_veiculos_to_string;
    }

    public function get_fotos_veiculos_veiculos_to_string()
    {
        if(!empty($this->fotos_veiculos_veiculos_to_string))
        {
            return $this->fotos_veiculos_veiculos_to_string;
        }
        
        $values = FotosVeiculos::where('veiculos_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    
    public function set_manutencao_garantia_itens_propostas_to_string($manutencao_garantia_itens_propostas_to_string)
    {
        if(is_array($manutencao_garantia_itens_propostas_to_string))
        {
            $values = ItensPropostas::where('id', 'in', $manutencao_garantia_itens_propostas_to_string)->getIndexedArray('id', 'id');
            $this->manutencao_garantia_itens_propostas_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_garantia_itens_propostas_to_string = $manutencao_garantia_itens_propostas_to_string;
        }

        $this->vdata['manutencao_garantia_itens_propostas_to_string'] = $this->manutencao_garantia_itens_propostas_to_string;
    }

    public function get_manutencao_garantia_itens_propostas_to_string()
    {
        if(!empty($this->manutencao_garantia_itens_propostas_to_string))
        {
            return $this->manutencao_garantia_itens_propostas_to_string;
        }
        
        $values = ManutencaoGarantia::where('veiculos_id', '=', $this->id)->getIndexedArray('itens_propostas_id','{itens_propostas->id}');
        return implode(', ', $values);
    }

    
    public function set_manutencao_garantia_veiculos_to_string($manutencao_garantia_veiculos_to_string)
    {
        if(is_array($manutencao_garantia_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $manutencao_garantia_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->manutencao_garantia_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_garantia_veiculos_to_string = $manutencao_garantia_veiculos_to_string;
        }

        $this->vdata['manutencao_garantia_veiculos_to_string'] = $this->manutencao_garantia_veiculos_to_string;
    }

    public function get_manutencao_garantia_veiculos_to_string()
    {
        if(!empty($this->manutencao_garantia_veiculos_to_string))
        {
            return $this->manutencao_garantia_veiculos_to_string;
        }
        
        $values = ManutencaoGarantia::where('veiculos_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    
    public function set_manutencao_garantia_pedido_frotas_to_string($manutencao_garantia_pedido_frotas_to_string)
    {
        if(is_array($manutencao_garantia_pedido_frotas_to_string))
        {
            $values = PedidoFrotas::where('id', 'in', $manutencao_garantia_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->manutencao_garantia_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_garantia_pedido_frotas_to_string = $manutencao_garantia_pedido_frotas_to_string;
        }

        $this->vdata['manutencao_garantia_pedido_frotas_to_string'] = $this->manutencao_garantia_pedido_frotas_to_string;
    }

    public function get_manutencao_garantia_pedido_frotas_to_string()
    {
        if(!empty($this->manutencao_garantia_pedido_frotas_to_string))
        {
            return $this->manutencao_garantia_pedido_frotas_to_string;
        }
        
        $values = ManutencaoGarantia::where('veiculos_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_manutencao_garantia_propostas_to_string($manutencao_garantia_propostas_to_string)
    {
        if(is_array($manutencao_garantia_propostas_to_string))
        {
            $values = Propostas::where('id', 'in', $manutencao_garantia_propostas_to_string)->getIndexedArray('id', 'id');
            $this->manutencao_garantia_propostas_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_garantia_propostas_to_string = $manutencao_garantia_propostas_to_string;
        }

        $this->vdata['manutencao_garantia_propostas_to_string'] = $this->manutencao_garantia_propostas_to_string;
    }

    public function get_manutencao_garantia_propostas_to_string()
    {
        if(!empty($this->manutencao_garantia_propostas_to_string))
        {
            return $this->manutencao_garantia_propostas_to_string;
        }
        
        $values = ManutencaoGarantia::where('veiculos_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
        return implode(', ', $values);
    }

    
    public function set_manutencao_garantia_produto_to_string($manutencao_garantia_produto_to_string)
    {
        if(is_array($manutencao_garantia_produto_to_string))
        {
            $values = Produto::where('id', 'in', $manutencao_garantia_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->manutencao_garantia_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_garantia_produto_to_string = $manutencao_garantia_produto_to_string;
        }

        $this->vdata['manutencao_garantia_produto_to_string'] = $this->manutencao_garantia_produto_to_string;
    }

    public function get_manutencao_garantia_produto_to_string()
    {
        if(!empty($this->manutencao_garantia_produto_to_string))
        {
            return $this->manutencao_garantia_produto_to_string;
        }
        
        $values = ManutencaoGarantia::where('veiculos_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

    
    public function set_autorizacao_pedido_pedido_frotas_to_string($autorizacao_pedido_pedido_frotas_to_string)
    {
        if(is_array($autorizacao_pedido_pedido_frotas_to_string))
        {
            $values = PedidoFrotas::where('id', 'in', $autorizacao_pedido_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->autorizacao_pedido_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->autorizacao_pedido_pedido_frotas_to_string = $autorizacao_pedido_pedido_frotas_to_string;
        }

        $this->vdata['autorizacao_pedido_pedido_frotas_to_string'] = $this->autorizacao_pedido_pedido_frotas_to_string;
    }

    public function get_autorizacao_pedido_pedido_frotas_to_string()
    {
        if(!empty($this->autorizacao_pedido_pedido_frotas_to_string))
        {
            return $this->autorizacao_pedido_pedido_frotas_to_string;
        }
        
        $values = AutorizacaoPedido::where('veiculos_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_autorizacao_pedido_veiculos_to_string($autorizacao_pedido_veiculos_to_string)
    {
        if(is_array($autorizacao_pedido_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $autorizacao_pedido_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->autorizacao_pedido_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->autorizacao_pedido_veiculos_to_string = $autorizacao_pedido_veiculos_to_string;
        }

        $this->vdata['autorizacao_pedido_veiculos_to_string'] = $this->autorizacao_pedido_veiculos_to_string;
    }

    public function get_autorizacao_pedido_veiculos_to_string()
    {
        if(!empty($this->autorizacao_pedido_veiculos_to_string))
        {
            return $this->autorizacao_pedido_veiculos_to_string;
        }
        
        $values = AutorizacaoPedido::where('veiculos_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    
    public function set_autorizacao_pedido_system_users_to_string($autorizacao_pedido_system_users_to_string)
    {
        if(is_array($autorizacao_pedido_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $autorizacao_pedido_system_users_to_string)->getIndexedArray('name', 'name');
            $this->autorizacao_pedido_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->autorizacao_pedido_system_users_to_string = $autorizacao_pedido_system_users_to_string;
        }

        $this->vdata['autorizacao_pedido_system_users_to_string'] = $this->autorizacao_pedido_system_users_to_string;
    }

    public function get_autorizacao_pedido_system_users_to_string()
    {
        if(!empty($this->autorizacao_pedido_system_users_to_string))
        {
            return $this->autorizacao_pedido_system_users_to_string;
        }
        
        $values = AutorizacaoPedido::where('veiculos_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }
/**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_responsavel(Pessoa $object)
    {
        $this->responsavel = $object;
        $this->responsavel_id = $object->id;
    }
    
    /**
     * Method get_responsavel
     * Sample of usage: $var->responsavel->attribute;
     * @returns Pessoa instance
     */
    public function get_responsavel()
    {
        
        // loads the associated object
        if (empty($this->responsavel))
            $this->responsavel = new Pessoa($this->responsavel_id);
        
        // returns the associated object
        return $this->responsavel;
    }
    
    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

