<?php

class ViewCotacao extends TRecord
{
    const TABLENAME  = 'view_cotacao';
    const PRIMARYKEY = 'cotacao_id';
    const IDPOLICY   =  'max'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_cotacao');
        parent::addAttribute('unidade');
        parent::addAttribute('departamento');
        parent::addAttribute('pedido_id');
        parent::addAttribute('descricaopedido');
        parent::addAttribute('nomeautorizado');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('nomefornecedor');
        parent::addAttribute('cnpj');
        parent::addAttribute('nomecidade');
        parent::addAttribute('uf');
            
    }

    
}

