<?php

//<fileHeader>
  
//</fileHeader>

class TipoDocumentosPropostas extends TRecord
{
    const TABLENAME  = 'tipo_documentos_propostas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    
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
        parent::addAttribute('descricao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    
    /**
     * Method getDocumentosPropostass
     */
    public function getDocumentosPropostass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_documentos_propostas_id', '=', $this->id));
        return DocumentosPropostas::getObjects( $criteria );
    }

    
    public function set_documentos_propostas_propostas_to_string($documentos_propostas_propostas_to_string)
    {
        if(is_array($documentos_propostas_propostas_to_string))
        {
            $values = Propostas::where('id', 'in', $documentos_propostas_propostas_to_string)->getIndexedArray('id', 'id');
            $this->documentos_propostas_propostas_to_string = implode(', ', $values);
        }
        else
        {
            $this->documentos_propostas_propostas_to_string = $documentos_propostas_propostas_to_string;
        }

        $this->vdata['documentos_propostas_propostas_to_string'] = $this->documentos_propostas_propostas_to_string;
    }

    public function get_documentos_propostas_propostas_to_string()
    {
        if(!empty($this->documentos_propostas_propostas_to_string))
        {
            return $this->documentos_propostas_propostas_to_string;
        }
        
        $values = DocumentosPropostas::where('tipo_documentos_propostas_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
        return implode(', ', $values);
    }

    
    public function set_documentos_propostas_tipo_documentos_propostas_to_string($documentos_propostas_tipo_documentos_propostas_to_string)
    {
        if(is_array($documentos_propostas_tipo_documentos_propostas_to_string))
        {
            $values = TipoDocumentosPropostas::where('id', 'in', $documentos_propostas_tipo_documentos_propostas_to_string)->getIndexedArray('id', 'id');
            $this->documentos_propostas_tipo_documentos_propostas_to_string = implode(', ', $values);
        }
        else
        {
            $this->documentos_propostas_tipo_documentos_propostas_to_string = $documentos_propostas_tipo_documentos_propostas_to_string;
        }

        $this->vdata['documentos_propostas_tipo_documentos_propostas_to_string'] = $this->documentos_propostas_tipo_documentos_propostas_to_string;
    }

    public function get_documentos_propostas_tipo_documentos_propostas_to_string()
    {
        if(!empty($this->documentos_propostas_tipo_documentos_propostas_to_string))
        {
            return $this->documentos_propostas_tipo_documentos_propostas_to_string;
        }
        
        $values = DocumentosPropostas::where('tipo_documentos_propostas_id', '=', $this->id)->getIndexedArray('tipo_documentos_propostas_id','{tipo_documentos_propostas->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
        //<onBeforeDeleteCode>
  
        //</onBeforeDeleteCode>

        if(DocumentosPropostas::where('tipo_documentos_propostas_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
    }

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

