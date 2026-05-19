<?php

class DocumentosCotacao extends TRecord
{
    const TABLENAME  = 'documentos_cotacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $cotacao;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cotacao_id');
        parent::addAttribute('caminho');
    
    }

    /**
     * Method set_cotacao
     * Sample of usage: $var->cotacao = $object;
     * @param $object Instance of Cotacao
     */
    public function set_cotacao(Cotacao $object)
    {
        $this->cotacao = $object;
        $this->cotacao_id = $object->id;
    }

    /**
     * Method get_cotacao
     * Sample of usage: $var->cotacao->attribute;
     * @returns Cotacao instance
     */
    public function get_cotacao()
    {
    
        // loads the associated object
        if (empty($this->cotacao))
            $this->cotacao = new Cotacao($this->cotacao_id);
    
        // returns the associated object
        return $this->cotacao;
    }

}

