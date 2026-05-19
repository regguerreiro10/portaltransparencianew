<?php

class Fatura extends TRecord
{
    const TABLENAME  = 'fatura';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private FormaPagamento $forma_pagamento;
    private DepartamentoUnit $departamento_unit;
    private SystemUnit $system_unit;
    private SystemUsers $system_users;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('numero_fatura');
        parent::addAttribute('forma_pagamento_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('periodo_apuracao_inicial');
        parent::addAttribute('periodo_apuracao_final');
        parent::addAttribute('data_vencimento');
        parent::addAttribute('data_pagamento');
        parent::addAttribute('obs');
        parent::addAttribute('data_emissao');
        parent::addAttribute('totalgeral');
        parent::addAttribute('totalservico');
        parent::addAttribute('totalproduto');
        parent::addAttribute('desconto');
        parent::addAttribute('total');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
    
    }

    /**
     * Method set_forma_pagamento
     * Sample of usage: $var->forma_pagamento = $object;
     * @param $object Instance of FormaPagamento
     */
    public function set_forma_pagamento(FormaPagamento $object)
    {
        $this->forma_pagamento = $object;
        $this->forma_pagamento_id = $object->id;
    }

    /**
     * Method get_forma_pagamento
     * Sample of usage: $var->forma_pagamento->attribute;
     * @returns FormaPagamento instance
     */
    public function get_forma_pagamento()
    {
    
        // loads the associated object
        if (empty($this->forma_pagamento))
            $this->forma_pagamento = new FormaPagamento($this->forma_pagamento_id);
    
        // returns the associated object
        return $this->forma_pagamento;
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
     * Method getContas
     */
    public function getContas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('fatura_id', '=', $this->id));
        return Conta::getObjects( $criteria );
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
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('tipo_conta_id','{tipo_conta->nome}');
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
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('categoria_id','{categoria->nome}');
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
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('forma_pagamento_id','{forma_pagamento->nome}');
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
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('pedido_venda_id','{pedido_venda->id}');
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
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    public function set_conta_entidade_to_string($conta_entidade_to_string)
    {
        if(is_array($conta_entidade_to_string))
        {
            $values = Entidade::where('id', 'in', $conta_entidade_to_string)->getIndexedArray('id', 'id');
            $this->conta_entidade_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_entidade_to_string = $conta_entidade_to_string;
        }

        $this->vdata['conta_entidade_to_string'] = $this->conta_entidade_to_string;
    }

    public function get_conta_entidade_to_string()
    {
        if(!empty($this->conta_entidade_to_string))
        {
            return $this->conta_entidade_to_string;
        }
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('entidade_id','{entidade->id}');
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
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
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
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    public function set_conta_antecipacao_to_string($conta_antecipacao_to_string)
    {
        if(is_array($conta_antecipacao_to_string))
        {
            $values = Antecipacao::where('id', 'in', $conta_antecipacao_to_string)->getIndexedArray('id', 'id');
            $this->conta_antecipacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_antecipacao_to_string = $conta_antecipacao_to_string;
        }

        $this->vdata['conta_antecipacao_to_string'] = $this->conta_antecipacao_to_string;
    }

    public function get_conta_antecipacao_to_string()
    {
        if(!empty($this->conta_antecipacao_to_string))
        {
            return $this->conta_antecipacao_to_string;
        }
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('antecipacao_id','{antecipacao->id}');
        return implode(', ', $values);
    }

    public function set_conta_fatura_to_string($conta_fatura_to_string)
    {
        if(is_array($conta_fatura_to_string))
        {
            $values = Fatura::where('id', 'in', $conta_fatura_to_string)->getIndexedArray('id', 'id');
            $this->conta_fatura_to_string = implode(', ', $values);
        }
        else
        {
            $this->conta_fatura_to_string = $conta_fatura_to_string;
        }

        $this->vdata['conta_fatura_to_string'] = $this->conta_fatura_to_string;
    }

    public function get_conta_fatura_to_string()
    {
        if(!empty($this->conta_fatura_to_string))
        {
            return $this->conta_fatura_to_string;
        }
    
        $values = Conta::where('fatura_id', '=', $this->id)->getIndexedArray('fatura_id','{fatura->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
    

        if(Conta::where('fatura_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    public function get_status()
    {
        if(date('Y-m-d') > $this->data_vencimento && !$this->data_pagamento)
        {
            return "<label style='width:120px;' class='label label-danger'> ATRASADA </label>";
        }
        elseif(!$this->data_pagamento )
        {
            return "<label style='width:120px;' class='label label-warning'> EM ABERTA </label>";
        }
        elseif($this->data_pagamento )
        {
            return "<label style='width:120px;' class='label label-success'> QUITADA </label>";
        }
    }
    public function get_status_texto()
    {
        if(date('Y-m-d') > $this->data_vencimento && !$this->data_pagamento)
        {
            return "ATRASADA";
        }
        elseif(!$this->data_pagamento )
        {
            return "EM ABERTA";
        }
        elseif($this->data_pagamento )
        {
            return "QUITADA";
        }
    }

}

