<?php

//<fileHeader>
  
//</fileHeader>

class Propostas extends TRecord
{
    const TABLENAME  = 'propostas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private $pedido_frotas;
    private $pessoa;
    private $estado_pedido_frotas;
    private $veiculos;
    private $system_unit;
    private $departamento_unit;
    private $system_users;
    private $entidade;
    private $motorista_entrada;
    private $motorista_retirada;
    private $cidade;
    
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
        parent::addAttribute('pedido_frotas_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('estado_pedido_frotas_id');
        parent::addAttribute('estado_pedido_frotas1_id');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('placa');
        parent::addAttribute('modelo');
        parent::addAttribute('data_cotacao');
        parent::addAttribute('obs');
        parent::addAttribute('valor_total');
        parent::addAttribute('valor_desconto');
        parent::addAttribute('valor_liquido');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('data_entrada_veiculo');
        parent::addAttribute('data_retirada_veiculo');
        parent::addAttribute('data_previsao_entrega');
        parent::addAttribute('km');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('responsavel_tecnico');
        parent::addAttribute('datahora_inicioservico');
        parent::addAttribute('datahora_fimservico');
        parent::addAttribute('total_produtos_sem_desconto');
        parent::addAttribute('total_servicos_sem_desconto');
        parent::addAttribute('total_geral_sem_desconto');
        parent::addAttribute('total_produtos_com_desconto');
        parent::addAttribute('desconto_contratual');
        parent::addAttribute('total_servicos_com_desconto');
        parent::addAttribute('motorista_entrada_id');
        parent::addAttribute('total_geral_com_desconto');
        parent::addAttribute('entidade_id');
        parent::addAttribute('motorista_retirada_id');
        parent::addAttribute('cidade_id');
        parent::addAttribute('data_limite_resposta');
        parent::addAttribute('horimetro_entrada_aeronave');
        parent::addAttribute('ciclos_entrada_aeronave');
        parent::addAttribute('horimetro_retirada_aeronave');
        parent::addAttribute('ciclos_retirada_aeronave'); 
        parent::addAttribute('ciclos');
        parent::addAttribute('horimetro_inicioservico'); 
        parent::addAttribute('ciclos_inicioservico');
        parent::addAttribute('horimetro_fimservico');
        parent::addAttribute('ciclos_fimservico'); 
         parent::addAttribute('abastecimento');

 
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_pedido_frotas
     * Sample of usage: $var->pedido_frotas = $object;
     * @param $object Instance of PedidoFrotas
     */
    public function set_pedido_frotas(PedidoFrotas $object)
    {
        $this->pedido_frotas = $object;
        $this->pedido_frotas_id = $object->id;
    }
    
    /**
     * Method get_pedido_frotas
     * Sample of usage: $var->pedido_frotas->attribute;
     * @returns PedidoFrotas instance
     */
    public function get_pedido_frotas()
    {
        
        // loads the associated object
        if (empty($this->pedido_frotas))
            $this->pedido_frotas = new PedidoFrotas($this->pedido_frotas_id);
        
        // returns the associated object
        return $this->pedido_frotas;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;
    }
    
    /**
     * Method get_pessoa
     * Sample of usage: $var->pessoa->attribute;
     * @returns Pessoa instance
     */
    public function get_pessoa()
    {
        
        // loads the associated object
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->pessoa_id);
        
        // returns the associated object
        return $this->pessoa;
    }
    /**
     * Method set_estado_pedido_frotas
     * Sample of usage: $var->estado_pedido_frotas = $object;
     * @param $object Instance of EstadoPedidoFrotas
     */
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
     * Method set_entidade
     * Sample of usage: $var->entidade = $object;
     * @param $object Instance of Entidade
     */
    public function set_entidade(Entidade $object)
    {
        $this->entidade = $object;
        $this->entidade_id = $object->id;
    }
    
    /**
     * Method get_entidade
     * Sample of usage: $var->entidade->attribute;
     * @returns Entidade instance
     */
    public function get_entidade()
    {
        
        // loads the associated object
        if (empty($this->entidade))
            $this->entidade = new Entidade($this->entidade_id);
        
        // returns the associated object
        return $this->entidade;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_motorista_entrada(Pessoa $object)
    {
        $this->motorista_entrada = $object;
        $this->motorista_entrada_id = $object->id;
    }
    
    /**
     * Method get_motorista_entrada
     * Sample of usage: $var->motorista_entrada->attribute;
     * @returns Pessoa instance
     */
    public function get_motorista_entrada()
    {
        
        // loads the associated object
        if (empty($this->motorista_entrada))
            $this->motorista_entrada = new Pessoa($this->motorista_entrada_id);
        
        // returns the associated object
        return $this->motorista_entrada;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_motorista_retirada(Pessoa $object)
    {
        $this->motorista_retirada = $object;
        $this->motorista_retirada_id = $object->id;
    }
    
    /**
     * Method get_motorista_retirada
     * Sample of usage: $var->motorista_retirada->attribute;
     * @returns Pessoa instance
     */
    public function get_motorista_retirada()
    {
        
        // loads the associated object
        if (empty($this->motorista_retirada))
            $this->motorista_retirada = new Pessoa($this->motorista_retirada_id);
        
        // returns the associated object
        return $this->motorista_retirada;
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
     * Method getItensPropostass
     */
    public function getItensPropostass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('propostas_id', '=', $this->id));
        return ItensPropostas::getObjects( $criteria );
    }
    /**
     * Method getPropostasHistoricos
     */
    public function getPropostasHistoricos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('propostas_id', '=', $this->id));
        return PropostasHistorico::getObjects( $criteria );
    }
    /**
     * Method getDocumentosPropostass
     */
    public function getDocumentosPropostass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('propostas_id', '=', $this->id));
        return DocumentosPropostas::getObjects( $criteria );
    }
    /**
     * Method getComentarioPropostas
     */
    public function getComentarioPropostas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('propostas_id', '=', $this->id));
        return ComentarioProposta::getObjects( $criteria );
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
        
        $values = ItensPropostas::where('propostas_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
        return implode(', ', $values);
    }

    
    public function set_propostas_historico_propostas_to_string($propostas_historico_propostas_to_string)
    {
        if(is_array($propostas_historico_propostas_to_string))
        {
            $values = Propostas::where('id', 'in', $propostas_historico_propostas_to_string)->getIndexedArray('id', 'id');
            $this->propostas_historico_propostas_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_historico_propostas_to_string = $propostas_historico_propostas_to_string;
        }

        $this->vdata['propostas_historico_propostas_to_string'] = $this->propostas_historico_propostas_to_string;
    }

    public function get_propostas_historico_propostas_to_string()
    {
        if(!empty($this->propostas_historico_propostas_to_string))
        {
            return $this->propostas_historico_propostas_to_string;
        }
        
        $values = PropostasHistorico::where('propostas_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
        return implode(', ', $values);
    }

    
    public function set_propostas_historico_estado_pedido_frotas_to_string($propostas_historico_estado_pedido_frotas_to_string)
    {
        if(is_array($propostas_historico_estado_pedido_frotas_to_string))
        {
            $values = EstadoPedidoFrotas::where('id', 'in', $propostas_historico_estado_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->propostas_historico_estado_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_historico_estado_pedido_frotas_to_string = $propostas_historico_estado_pedido_frotas_to_string;
        }

        $this->vdata['propostas_historico_estado_pedido_frotas_to_string'] = $this->propostas_historico_estado_pedido_frotas_to_string;
    }

    public function get_propostas_historico_estado_pedido_frotas_to_string()
    {
        if(!empty($this->propostas_historico_estado_pedido_frotas_to_string))
        {
            return $this->propostas_historico_estado_pedido_frotas_to_string;
        }
        
        $values = PropostasHistorico::where('propostas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_propostas_historico_aprovador_frotas_to_string($propostas_historico_aprovador_frotas_to_string)
    {
        if(is_array($propostas_historico_aprovador_frotas_to_string))
        {
            $values = SystemUsers::where('id', 'in', $propostas_historico_aprovador_frotas_to_string)->getIndexedArray('name', 'name');
            $this->propostas_historico_aprovador_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->propostas_historico_aprovador_frotas_to_string = $propostas_historico_aprovador_frotas_to_string;
        }

        $this->vdata['propostas_historico_aprovador_frotas_to_string'] = $this->propostas_historico_aprovador_frotas_to_string;
    }

    public function get_propostas_historico_aprovador_frotas_to_string()
    {
        if(!empty($this->propostas_historico_aprovador_frotas_to_string))
        {
            return $this->propostas_historico_aprovador_frotas_to_string;
        }
        
        $values = PropostasHistorico::where('propostas_id', '=', $this->id)->getIndexedArray('aprovador_frotas_id','{aprovador_frotas->name}');
        return implode(', ', $values);
    }

    
    public function set_documentos_propostas_propostas_to_string($documentos_propostas_propostas_to_string)
    {
        if(is_array($documentos_propostas_propostas_to_string))
        {
            $values = Propostas::where('id', 'in', $documentos_propostas_propostas_to_string)->getIndexedArray('id', 'id');
            $this->documentos_propostas_propostas_to_string = implode(', ', $values);
        }
        else
        {
            $this->documentos_propostas_propostas_to_string = $documentos_propostas_propostas_to_string;
        }

        $this->vdata['documentos_propostas_propostas_to_string'] = $this->documentos_propostas_propostas_to_string;
    }

    public function get_documentos_propostas_propostas_to_string()
    {
        if(!empty($this->documentos_propostas_propostas_to_string))
        {
            return $this->documentos_propostas_propostas_to_string;
        }
        
        $values = DocumentosPropostas::where('propostas_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
        return implode(', ', $values);
    }

    
    public function set_comentario_proposta_propostas_to_string($comentario_proposta_propostas_to_string)
    {
        if(is_array($comentario_proposta_propostas_to_string))
        {
            $values = Propostas::where('id', 'in', $comentario_proposta_propostas_to_string)->getIndexedArray('id', 'id');
            $this->comentario_proposta_propostas_to_string = implode(', ', $values);
        }
        else
        {
            $this->comentario_proposta_propostas_to_string = $comentario_proposta_propostas_to_string;
        }

        $this->vdata['comentario_proposta_propostas_to_string'] = $this->comentario_proposta_propostas_to_string;
    }

    public function get_comentario_proposta_propostas_to_string()
    {
        if(!empty($this->comentario_proposta_propostas_to_string))
        {
            return $this->comentario_proposta_propostas_to_string;
        }
        
        $values = ComentarioProposta::where('propostas_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
        return implode(', ', $values);
    }

    
    public function set_comentario_proposta_system_users_to_string($comentario_proposta_system_users_to_string)
    {
        if(is_array($comentario_proposta_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $comentario_proposta_system_users_to_string)->getIndexedArray('name', 'name');
            $this->comentario_proposta_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->comentario_proposta_system_users_to_string = $comentario_proposta_system_users_to_string;
        }

        $this->vdata['comentario_proposta_system_users_to_string'] = $this->comentario_proposta_system_users_to_string;
    }

    public function get_comentario_proposta_system_users_to_string()
    {
        if(!empty($this->comentario_proposta_system_users_to_string))
        {
            return $this->comentario_proposta_system_users_to_string;
        }
        
        $values = ComentarioProposta::where('propostas_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

