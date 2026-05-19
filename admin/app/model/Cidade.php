<?php

class Cidade extends TRecord
{
    const TABLENAME  = 'cidade';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $estado;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('estado_id');
        parent::addAttribute('nome');
        parent::addAttribute('codigo_ibge');
        parent::addAttribute('idold');
            
    }

    /**
     * Method set_estado
     * Sample of usage: $var->estado = $object;
     * @param $object Instance of Estado
     */
    public function set_estado(Estado $object)
    {
        $this->estado = $object;
        $this->estado_id = $object->id;
    }

    /**
     * Method get_estado
     * Sample of usage: $var->estado->attribute;
     * @returns Estado instance
     */
    public function get_estado()
    {
    
        // loads the associated object
        if (empty($this->estado))
            $this->estado = new Estado($this->estado_id);
    
        // returns the associated object
        return $this->estado;
    }

    /**
     * Method getCidadePedidos
     */
    public function getCidadePedidos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cidade_id', '=', $this->id));
        return CidadePedido::getObjects( $criteria );
    }
    /**
     * Method getDepartamentoUnits
     */
    public function getDepartamentoUnits()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cidade_id', '=', $this->id));
        return DepartamentoUnit::getObjects( $criteria );
    }
    /**
     * Method getPessoaEnderecos
     */
    public function getPessoaEnderecos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cidade_id', '=', $this->id));
        return PessoaEndereco::getObjects( $criteria );
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
    
        $values = CidadePedido::where('cidade_id', '=', $this->id)->getIndexedArray('cidade_id','{cidade->nome}');
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
    
        $values = CidadePedido::where('cidade_id', '=', $this->id)->getIndexedArray('pedido_id','{pedido->id}');
        return implode(', ', $values);
    }

    public function set_departamento_unit_cidade_to_string($departamento_unit_cidade_to_string)
    {
        if(is_array($departamento_unit_cidade_to_string))
        {
            $values = Cidade::where('id', 'in', $departamento_unit_cidade_to_string)->getIndexedArray('nome', 'nome');
            $this->departamento_unit_cidade_to_string = implode(', ', $values);
        }
        else
        {
            $this->departamento_unit_cidade_to_string = $departamento_unit_cidade_to_string;
        }

        $this->vdata['departamento_unit_cidade_to_string'] = $this->departamento_unit_cidade_to_string;
    }

    public function get_departamento_unit_cidade_to_string()
    {
        if(!empty($this->departamento_unit_cidade_to_string))
        {
            return $this->departamento_unit_cidade_to_string;
        }
    
        $values = DepartamentoUnit::where('cidade_id', '=', $this->id)->getIndexedArray('cidade_id','{cidade->nome}');
        return implode(', ', $values);
    }

    public function set_departamento_unit_system_unit_to_string($departamento_unit_system_unit_to_string)
    {
        if(is_array($departamento_unit_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $departamento_unit_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->departamento_unit_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->departamento_unit_system_unit_to_string = $departamento_unit_system_unit_to_string;
        }

        $this->vdata['departamento_unit_system_unit_to_string'] = $this->departamento_unit_system_unit_to_string;
    }

    public function get_departamento_unit_system_unit_to_string()
    {
        if(!empty($this->departamento_unit_system_unit_to_string))
        {
            return $this->departamento_unit_system_unit_to_string;
        }
    
        $values = DepartamentoUnit::where('cidade_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
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
    
        $values = PessoaEndereco::where('cidade_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
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
    
        $values = PessoaEndereco::where('cidade_id', '=', $this->id)->getIndexedArray('cidade_id','{cidade->nome}');
        return implode(', ', $values);
    }

    
}

