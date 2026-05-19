<?php

//<fileHeader>
  
//</fileHeader>

class EstadoPedidoFrotas extends TRecord
{
    const TABLENAME  = 'estado_pedido_frotas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    
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
    const PREAPROVADO = '24';
    const VALORVENAL = '25';
    const REVISAO = '26';
    const APROVACAOVB = '27';
    const APROVACAODISPOSITIVO = '28';


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
        parent::addAttribute('nome');
        parent::addAttribute('cor');
        parent::addAttribute('kanban');
        parent::addAttribute('ordem');
        parent::addAttribute('estado_final');
        parent::addAttribute('estado_inicial');
        parent::addAttribute('permite_edicao');
        parent::addAttribute('permite_exclusao');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    
    /**
     * Method getEstadoPedidoFrotasAprovadors
     */
    public function getEstadoPedidoFrotasAprovadors()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_frotas_id', '=', $this->id));
        return EstadoPedidoFrotasAprovador::getObjects( $criteria );
    }
    /**
     * Method getPedidoFrotasHistoricos
     */
    public function getPedidoFrotasHistoricos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_frotas_id', '=', $this->id));
        return PedidoFrotasHistorico::getObjects( $criteria );
    }
    /**
     * Method getPropostass
     */
    public function getPropostass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_frotas_id', '=', $this->id));
        return Propostas::getObjects( $criteria );
    }
    /**
     * Method getPropostasHistoricos
     */
    public function getPropostasHistoricos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_frotas_id', '=', $this->id));
        return PropostasHistorico::getObjects( $criteria );
    }
    /**
     * Method getPedidoFrotass
     */
    public function getPedidoFrotass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_pedido_frotas_id', '=', $this->id));
        return PedidoFrotas::getObjects( $criteria );
    }

    
    public function set_estado_pedido_frotas_aprovador_aprovador_frotas_to_string($estado_pedido_frotas_aprovador_aprovador_frotas_to_string)
    {
        if(is_array($estado_pedido_frotas_aprovador_aprovador_frotas_to_string))
        {
            $values = AprovadorFrotas::where('id', 'in', $estado_pedido_frotas_aprovador_aprovador_frotas_to_string)->getIndexedArray('id', 'id');
            $this->estado_pedido_frotas_aprovador_aprovador_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->estado_pedido_frotas_aprovador_aprovador_frotas_to_string = $estado_pedido_frotas_aprovador_aprovador_frotas_to_string;
        }

        $this->vdata['estado_pedido_frotas_aprovador_aprovador_frotas_to_string'] = $this->estado_pedido_frotas_aprovador_aprovador_frotas_to_string;
    }

    public function get_estado_pedido_frotas_aprovador_aprovador_frotas_to_string()
    {
        if(!empty($this->estado_pedido_frotas_aprovador_aprovador_frotas_to_string))
        {
            return $this->estado_pedido_frotas_aprovador_aprovador_frotas_to_string;
        }
        
        $values = EstadoPedidoFrotasAprovador::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('aprovador_frotas_id','{aprovador_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string($estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string)
    {
        if(is_array($estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string))
        {
            $values = EstadoPedidoFrotas::where('id', 'in', $estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string = $estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string;
        }

        $this->vdata['estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string'] = $this->estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string;
    }

    public function get_estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string()
    {
        if(!empty($this->estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string))
        {
            return $this->estado_pedido_frotas_aprovador_estado_pedido_frotas_to_string;
        }
        
        $values = EstadoPedidoFrotasAprovador::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
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
        
        $values = PedidoFrotasHistorico::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
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
        
        $values = PedidoFrotasHistorico::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('aprovador_frotas_id','{aprovador_frotas->id}');
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
        
        $values = PedidoFrotasHistorico::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
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
        
        $values = Propostas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
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
        
        $values = Propostas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
        
        $values = Propostas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
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
        
        $values = Propostas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('motorista_entrada_id','{motorista_entrada->id}');
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
        
        $values = Propostas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
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
        
        $values = Propostas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
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
        
        $values = Propostas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
        
        $values = Propostas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
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
        
        $values = Propostas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('motorista_retirada_id','{motorista_retirada->id}');
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
        
        $values = PropostasHistorico::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
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
        
        $values = PropostasHistorico::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_propostas_historico_aprovador_frotas_to_string($propostas_historico_aprovador_frotas_to_string)
    {
        if(is_array($propostas_historico_aprovador_frotas_to_string))
        {
            $values = AprovadorFrotas::where('id', 'in', $propostas_historico_aprovador_frotas_to_string)->getIndexedArray('id', 'id');
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
        
        $values = PropostasHistorico::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('aprovador_frotas_id','{aprovador_frotas->id}');
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
        
        $values = PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
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
        
        $values = PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('estabelecimento_id','{estabelecimento->nome}');
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
        
        $values = PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('niveltanque_id','{niveltanque->id}');
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
        
        $values = PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('condutor_entrada_id','{condutor_entrada->id}');
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
        
        $values = PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('condutor_retirada_id','{condutor_retirada->id}');
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
        
        $values = PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('tipo_manutencao_id','{tipo_manutencao->id}');
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
        
        $values = PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('negociacao_id','{negociacao->id}');
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
        
        $values = PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('condicao_pagamento_id','{condicao_pagamento->nome}');
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
        
        $values = PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
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
        
        $values = PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
        
        $values = PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
        //<onBeforeDeleteCode>
  
        //</onBeforeDeleteCode>

        if(EstadoPedidoFrotasAprovador::where('estado_pedido_frotas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
        if(PedidoFrotasHistorico::where('estado_pedido_frotas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
        if(Propostas::where('estado_pedido_frotas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
        if(PropostasHistorico::where('estado_pedido_frotas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
        if(PedidoFrotas::where('estado_pedido_frotas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
    }
    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}