<?php

class Cotacao extends TRecord
{
    const TABLENAME  = 'cotacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $pedido;
    private $pessoa;
    private $estado_pedido;
    private $system_users;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pedido_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('estado_pedido_id');
        parent::addAttribute('estado_pedido1_id');
        parent::addAttribute('data_cotacao');
        parent::addAttribute('obs');
        parent::addAttribute('valor_total');
        parent::addAttribute('valor_desconto');
        parent::addAttribute('valor_liquido');
        parent::addAttribute('cidade_id');
        parent::addAttribute('txadm');
        parent::addAttribute('txbancaria');
        parent::addAttribute('txantecipacao');
        parent::addAttribute('txcontrato');
        parent::addAttribute('entidade_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('data_limite_resposta');

    }

    /**
     * Method set_pedido
     * Sample of usage: $var->pedido = $object;
     * @param $object Instance of Pedido
     */
    public function set_pedido(Pedido $object)
    {
        $this->pedido = $object;
        $this->pedido_id = $object->id;
    }

    /**
     * Method get_pedido
     * Sample of usage: $var->pedido->attribute;
     * @returns Pedido instance
     */
    public function get_pedido()
    {
    
        // loads the associated object
        if (empty($this->pedido))
            $this->pedido = new Pedido($this->pedido_id);
    
        // returns the associated object
        return $this->pedido;
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
     * Method set_estado_pedido
     * Sample of usage: $var->estado_pedido = $object;
     * @param $object Instance of EstadoPedido
     */
    public function set_estado_pedido(EstadoPedido $object)
    {
        $this->estado_pedido = $object;
        $this->estado_pedido_id = $object->id;
    }

    /**
     * Method get_estado_pedido
     * Sample of usage: $var->estado_pedido->attribute;
     * @returns EstadoPedido instance
     */
    public function get_estado_pedido()
    {
    
        // loads the associated object
        if (empty($this->estado_pedido))
            $this->estado_pedido = new EstadoPedido($this->estado_pedido_id);
    
        // returns the associated object
        return $this->estado_pedido;
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
     * Method getCotacaoHistoricos
     */
    public function getCotacaoHistoricos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cotacao_id', '=', $this->id));
        return CotacaoHistorico::getObjects( $criteria );
    }
    /**
     * Method getDocumentosCotacaos
     */
    public function getDocumentosCotacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cotacao_id', '=', $this->id));
        return DocumentosCotacao::getObjects( $criteria );
    }
    /**
     * Method getItensCotacaos
     */
    public function getItensCotacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cotacao_id', '=', $this->id));
        return ItensCotacao::getObjects( $criteria );
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
    
        $values = CotacaoHistorico::where('cotacao_id', '=', $this->id)->getIndexedArray('estado_pedido_id','{estado_pedido->nome}');
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
    
        $values = CotacaoHistorico::where('cotacao_id', '=', $this->id)->getIndexedArray('aprovador_id','{aprovador->id}');
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
    
        $values = CotacaoHistorico::where('cotacao_id', '=', $this->id)->getIndexedArray('cotacao_id','{cotacao->id}');
        return implode(', ', $values);
    }

    public function set_documentos_cotacao_cotacao_to_string($documentos_cotacao_cotacao_to_string)
    {
        if(is_array($documentos_cotacao_cotacao_to_string))
        {
            $values = Cotacao::where('id', 'in', $documentos_cotacao_cotacao_to_string)->getIndexedArray('id', 'id');
            $this->documentos_cotacao_cotacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->documentos_cotacao_cotacao_to_string = $documentos_cotacao_cotacao_to_string;
        }

        $this->vdata['documentos_cotacao_cotacao_to_string'] = $this->documentos_cotacao_cotacao_to_string;
    }

    public function get_documentos_cotacao_cotacao_to_string()
    {
        if(!empty($this->documentos_cotacao_cotacao_to_string))
        {
            return $this->documentos_cotacao_cotacao_to_string;
        }
    
        $values = DocumentosCotacao::where('cotacao_id', '=', $this->id)->getIndexedArray('cotacao_id','{cotacao->id}');
        return implode(', ', $values);
    }

    public function set_itens_cotacao_produto_to_string($itens_cotacao_produto_to_string)
    {
        if(is_array($itens_cotacao_produto_to_string))
        {
            $values = Produto::where('id', 'in', $itens_cotacao_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->itens_cotacao_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_cotacao_produto_to_string = $itens_cotacao_produto_to_string;
        }

        $this->vdata['itens_cotacao_produto_to_string'] = $this->itens_cotacao_produto_to_string;
    }

    public function get_itens_cotacao_produto_to_string()
    {
        if(!empty($this->itens_cotacao_produto_to_string))
        {
            return $this->itens_cotacao_produto_to_string;
        }
    
        $values = ItensCotacao::where('cotacao_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

    public function set_itens_cotacao_cotacao_to_string($itens_cotacao_cotacao_to_string)
    {
        if(is_array($itens_cotacao_cotacao_to_string))
        {
            $values = Cotacao::where('id', 'in', $itens_cotacao_cotacao_to_string)->getIndexedArray('id', 'id');
            $this->itens_cotacao_cotacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_cotacao_cotacao_to_string = $itens_cotacao_cotacao_to_string;
        }

        $this->vdata['itens_cotacao_cotacao_to_string'] = $this->itens_cotacao_cotacao_to_string;
    }

    public function get_itens_cotacao_cotacao_to_string()
    {
        if(!empty($this->itens_cotacao_cotacao_to_string))
        {
            return $this->itens_cotacao_cotacao_to_string;
        }
    
        $values = ItensCotacao::where('cotacao_id', '=', $this->id)->getIndexedArray('cotacao_id','{cotacao->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(CotacaoHistorico::where('cotacao_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(DocumentosCotacao::where('cotacao_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(ItensCotacao::where('cotacao_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

