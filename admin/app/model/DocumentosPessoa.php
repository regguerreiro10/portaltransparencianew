<?php

//<fileHeader>
  
//</fileHeader>

class DocumentosPessoa extends TRecord
{
    const TABLENAME  = 'documentos_pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private TipoDocumento $tipo_documento;
    private Pessoa $pessoa;
    
    //<classProperties>
  
    //</classProperties>
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        //<onBeforeConstruct>
  
        //</onBeforeConstruct>
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('caminho');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('tipo_documento_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_tipo_documento
     * Sample of usage: $var->tipo_documento = $object;
     * @param $object Instance of TipoDocumento
     */
    public function set_tipo_documento(TipoDocumento $object)
    {
        $this->tipo_documento = $object;
        $this->tipo_documento_id = $object->id;
    }
    
    /**
     * Method get_tipo_documento
     * Sample of usage: $var->tipo_documento->attribute;
     * @returns TipoDocumento instance
     */
    public function get_tipo_documento()
    {
        
        // loads the associated object
        if (empty($this->tipo_documento))
            $this->tipo_documento = new TipoDocumento($this->tipo_documento_id);
        
        // returns the associated object
        return $this->tipo_documento;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;
    }
    
    /**
     * Method get_pessoa
     * Sample of usage: $var->pessoa->attribute;
     * @returns Pessoa instance
     */
    public function get_pessoa()
    {
        
        // loads the associated object
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->pessoa_id);
        
        // returns the associated object
        return $this->pessoa;
    }
    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

