<?php

class ViewEnviarcotacaoSeguradora extends TRecord
{
    const TABLENAME  = 'view_enviarcotacao_seguradora';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cidade_id');
        parent::addAttribute('seguimento_id');
        parent::addAttribute('nome');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('deleted_at');
            
    }

    
}

