<?php

class Entidade extends TRecord
{
    const TABLENAME  = 'entidade';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cnpj');
        parent::addAttribute('email');
        parent::addAttribute('cep');
        parent::addAttribute('rua');
        parent::addAttribute('numero');
        parent::addAttribute('bairro');
        parent::addAttribute('complemento');
        parent::addAttribute('cidade_id');
        parent::addAttribute('telefone01');
        parent::addAttribute('telefone02');
        parent::addAttribute('longitude');
        parent::addAttribute('latitude');
        parent::addAttribute('administradora_id');
        parent::addAttribute('logo');
        parent::addAttribute('compras');
        parent::addAttribute('frotas');
        parent::addAttribute('taxacontrato');
        parent::addAttribute('tipo_frota');
        parent::addAttribute('numero_documento');
        parent::addAttribute('numero_processo');
        parent::addAttribute('abastecimento');
            
    }

    public function get_cidade_nome()
    {
        $cidade = new Cidade($this->cidade_id);
        return $cidade->nome;
    }

    
}

