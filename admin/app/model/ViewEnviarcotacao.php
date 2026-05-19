<?php

class ViewEnviarcotacao extends TRecord
{
    const TABLENAME  = 'view_enviarcotacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

     const DELETEDAT  = 'deleted_at';

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cidade_id');
        parent::addAttribute('seguimento_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('deleted_at');
    
    }

}

