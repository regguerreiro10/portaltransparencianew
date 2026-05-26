<?php

class DocumentoPublicoAnexo extends TRecord
{
    const TABLENAME  = 'documento_publico_anexo';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private DocumentoPublico $documento_publico;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('documento_publico_id');
        parent::addAttribute('nome');
        parent::addAttribute('arquivo');
        parent::addAttribute('ordem');
        parent::addAttribute('created_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('updated_at');
            
    }

    /**
     * Method set_documento_publico
     * Sample of usage: $var->documento_publico = $object;
     * @param $object Instance of DocumentoPublico
     */
    public function set_documento_publico(DocumentoPublico $object)
    {
        $this->documento_publico = $object;
        $this->documento_publico_id = $object->id;
    }

    /**
     * Method get_documento_publico
     * Sample of usage: $var->documento_publico->attribute;
     * @returns DocumentoPublico instance
     */
    public function get_documento_publico()
    {
    
        // loads the associated object
        if (empty($this->documento_publico))
            $this->documento_publico = new DocumentoPublico($this->documento_publico_id);
    
        // returns the associated object
        return $this->documento_publico;
    }

    
}

