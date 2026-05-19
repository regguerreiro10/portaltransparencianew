<?php

class Administradora extends TRecord
{
    const TABLENAME  = 'administradora';
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
        parent::addAttribute('cd_grupo');
        parent::addAttribute('de_login_usu');
        parent::addAttribute('de_senha_usu');

            
    }

    
}

