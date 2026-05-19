<?php

class ViewItenscotacaoProduto extends TRecord
{
    const TABLENAME  = 'view_itenscotacao_produto';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('produto_id');
        parent::addAttribute('qtde');
        parent::addAttribute('valor');
        parent::addAttribute('valor_total');
        parent::addAttribute('cotacao_id');
        parent::addAttribute('id_produto');
        parent::addAttribute('nome');
            
    }

    
}

