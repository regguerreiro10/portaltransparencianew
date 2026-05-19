<?php

class ViewCidadeestado extends TRecord
{
    const TABLENAME  = 'view_cidadeestado';
    const PRIMARYKEY = 'idcidade';
    const IDPOLICY   =  'max'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nomecidade');
        parent::addAttribute('idestado');
        parent::addAttribute('nomeestado');
        parent::addAttribute('sigla');
            
    }

    
}

