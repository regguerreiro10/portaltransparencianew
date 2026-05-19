<?php

//<fileHeader>
  
//</fileHeader>

class AprovadorFrotas extends TRecord
{
    const TABLENAME  = 'aprovador_frotas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    private static $estadosDisponveis = [];
    
    private $system_users;
    
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
        parent::addAttribute('system_users_id');
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
     * Method getEstadoPedidoFrotasAprovadors
     */
    public function getEstadoPedidoFrotasAprovadors()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('aprovador_frotas_id', '=', $this->id));
        return EstadoPedidoFrotasAprovador::getObjects( $criteria );
    }
    /**
     * Method getPedidoFrotasHistoricos
     */
    public function getPedidoFrotasHistoricos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('aprovador_frotas_id', '=', $this->id));
        return PedidoFrotasHistorico::getObjects( $criteria );
    }
    /**
     * Method getPropostasHistoricos
     */
    public function getPropostasHistoricos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('aprovador_frotas_id', '=', $this->id));
        return PropostasHistorico::getObjects( $criteria );
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
        
        $values = EstadoPedidoFrotasAprovador::where('aprovador_frotas_id', '=', $this->id)->getIndexedArray('aprovador_frotas_id','{aprovador_frotas->id}');
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
        
        $values = EstadoPedidoFrotasAprovador::where('aprovador_frotas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
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
        
        $values = PedidoFrotasHistorico::where('aprovador_frotas_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
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
        
        $values = PedidoFrotasHistorico::where('aprovador_frotas_id', '=', $this->id)->getIndexedArray('aprovador_frotas_id','{aprovador_frotas->id}');
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
        
        $values = PedidoFrotasHistorico::where('aprovador_frotas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
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
        
        $values = PropostasHistorico::where('aprovador_frotas_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
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
        
        $values = PropostasHistorico::where('aprovador_frotas_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
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
        
        $values = PropostasHistorico::where('aprovador_frotas_id', '=', $this->id)->getIndexedArray('aprovador_frotas_id','{aprovador_frotas->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
        //<onBeforeDeleteCode>
  
        //</onBeforeDeleteCode>

        if(EstadoPedidoFrotasAprovador::where('aprovador_frotas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
        if(PedidoFrotasHistorico::where('aprovador_frotas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
        if(PropostasHistorico::where('aprovador_frotas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
    }
    public static function getAprovadorAtualFromPedidoFrotas($pedido_venda)
    {
        $criteria = new TCriteria();
        $criteria->add(new TFilter('estado_pedido_frotas_id', '=', $pedido_venda->estado_pedido_frotas_id));
    
        $aprovadores_id = EstadoPedidoFrotasAprovador::getIndexedArray('aprovador_frotas_id', 'aprovador_frotas_id', $criteria);
    
        $aprovadores = AprovadorFrotas::where('id', 'in', $aprovadores_id)->load();
    
        return $aprovadores;
    }
    
    public static function getEstadosDisponiveis()
    {
        if(empty(self::$estadosDisponveis))
        {
            $estadosDisponveis = [-1]; // estado fictcio caso não tenha permissão....
    
            $aprovador = AprovadorFrotas::where('system_users_id', '=', TSession::getValue('userid'))->first();
        
            if($aprovador)
            {
                $criteria = new TCriteria();
                $criteria->add(new TFilter('aprovador_frotas_id', '=', $aprovador->id));
            
                $estadosDisponveis = EstadoPedidoFrotasAprovador::getIndexedArray('estado_pedido_frotas_id', 'estado_pedido_frotas_id', $criteria);
            }
        
            self::$estadosDisponveis = $estadosDisponveis;
        }
    
    
        return self::$estadosDisponveis;
    
    }
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

