<?php

class SystemUnit extends TRecord
{
    const TABLENAME  = 'system_unit';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    private $cidade;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('connection_name');
        parent::addAttribute('email');
        parent::addAttribute('cep');
        parent::addAttribute('rua');
        parent::addAttribute('recaptcha');
        parent::addAttribute('numero');
        parent::addAttribute('bairro');
        parent::addAttribute('complemento');
        parent::addAttribute('cnpj');
        parent::addAttribute('telefone01');
        parent::addAttribute('telefone02');
        parent::addAttribute('telefone03');
        parent::addAttribute('cidade_id');
        parent::addAttribute('utilizasinapi');
        parent::addAttribute('testar_valor_venal');
        parent::addAttribute('entidade_id');
        parent::addAttribute('logo');
        parent::addAttribute('aprovacao_por_item');
        parent::addAttribute('selecao_redes_aleatoria');
        parent::addAttribute('testar_revisao');
        parent::addAttribute('pedido_base');
        parent::addAttribute('longitude');
        parent::addAttribute('latitude');
        parent::addAttribute('valor_base_aprovacao');
        parent::addAttribute('enviar_email_auto_relatorio');
        parent::addAttribute('garantia_dias');
        parent::addAttribute('percentual_produto_similar');
        parent::addAttribute('garantia_km');
        parent::addAttribute('utiliza_temparia');
        parent::addAttribute('bloqueio_valor_temparia');
        parent::addAttribute('exige_dotacao_empenho_frotas');
        parent::addAttribute('exibir_popup_plano_manutencao');
        parent::addAttribute('checklist_vistoria_veiculo');
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
     * Method getDepartamentoUnits
     */
    public function getDepartamentoUnits()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_unit_id', '=', $this->id));
        return DepartamentoUnit::getObjects( $criteria );
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
    
        $values = DepartamentoUnit::where('system_unit_id', '=', $this->id)->getIndexedArray('cidade_id','{cidade->nome}');
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
    
        $values = DepartamentoUnit::where('system_unit_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    
}

