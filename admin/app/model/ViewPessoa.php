<?php

class ViewPessoa extends TRecord
{
    const TABLENAME  = 'view_pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('id_pessoa_grupo');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('grupo_pessoa_id');
            
    }

    
}

