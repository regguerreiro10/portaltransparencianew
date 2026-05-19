<?php

//<fileHeader>
  
//</fileHeader>

class DocumentosPropostas extends TRecord
{
    const TABLENAME  = 'documentos_propostas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private $tipo_documentos_propostas; 
    private $propostas;
    
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
        parent::addAttribute('propostas_id');
        parent::addAttribute('caminho');
        parent::addAttribute('numero');
        parent::addAttribute('valor');
        parent::addAttribute('tipo_documentos_propostas_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');

        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_propostas
     * Sample of usage: $var->propostas = $object;
     * @param $object Instance of Propostas
     */
    public function set_propostas(Propostas $object)
    {
        $this->propostas = $object;
        $this->propostas_id = $object->id;
    }
    
    /**
     * Method set_pedido_frotas
     * Sample of usage: $var->pedido_frotas = $object;
     * @param $object Instance of PedidoFrotas
     */
    public function set_tipo_documentos_propostas(TipoDocumentosPropostas $object)
    {
        $this->TipoDocumentosPropostas = $object;
        $this->TipoDocumentosPropostas_id = $object->id;
    }
    
    /**
     * Method get_pedido_frotas
     * Sample of usage: $var->pedido_frotas->attribute;
     * @returns PedidoFrotas instance
     */
    public function get_tipo_documentos_propostas()
    {
        
        // loads the associated object
        if (empty($this->tipo_documentos_propostas))
            $this->tipo_documentos_propostas = new TipoDocumentosPropostas($this->tipo_documentos_propostas_id);
        
        // returns the associated object
        return $this->tipo_documentos_propostas;
    }

    public function get_propostas()
    {
        
        // loads the associated object
        if (empty($this->propostas))
            $this->propostas = new Propostas($this->propostas_id);
        
        // returns the associated object
        return $this->propostas;
    }
    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

                

