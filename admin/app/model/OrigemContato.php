<?php

class OrigemContato extends TRecord
{
    const TABLENAME  = 'origem_contato';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
            
    }

    /**
     * Method getNegociacaos
     */
    public function getNegociacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('origem_contato_id', '=', $this->id));
        return Negociacao::getObjects( $criteria );
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
    
        $values = Negociacao::where('origem_contato_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->nome}');
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
    
        $values = Negociacao::where('origem_contato_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->nome}');
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
    
        $values = Negociacao::where('origem_contato_id', '=', $this->id)->getIndexedArray('origem_contato_id','{origem_contato->nome}');
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
    
        $values = Negociacao::where('origem_contato_id', '=', $this->id)->getIndexedArray('etapa_negociacao_id','{etapa_negociacao->nome}');
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
    
        $values = Negociacao::where('origem_contato_id', '=', $this->id)->getIndexedArray('departamento_unit_id','{departamento_unit->name}');
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
    
        $values = Negociacao::where('origem_contato_id', '=', $this->id)->getIndexedArray('system_users_id','{system_users->name}');
        return implode(', ', $values);
    }

    
}

