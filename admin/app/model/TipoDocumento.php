<?php

//<fileHeader>
  
//</fileHeader>

class TipoDocumento extends TRecord
{
    const TABLENAME  = 'tipo_documento';
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
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('descricao');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    
    /**
     * Method getDocumentosPessoas
     */
    public function getDocumentosPessoas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_documento_id', '=', $this->id));
        return DocumentosPessoa::getObjects( $criteria );
    }

    
    public function set_documentos_pessoa_pessoa_to_string($documentos_pessoa_pessoa_to_string)
    {
        if(is_array($documentos_pessoa_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $documentos_pessoa_pessoa_to_string)->getIndexedArray('nome', 'nome');
            $this->documentos_pessoa_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->documentos_pessoa_pessoa_to_string = $documentos_pessoa_pessoa_to_string;
        }

        $this->vdata['documentos_pessoa_pessoa_to_string'] = $this->documentos_pessoa_pessoa_to_string;
    }

    public function get_documentos_pessoa_pessoa_to_string()
    {
        if(!empty($this->documentos_pessoa_pessoa_to_string))
        {
            return $this->documentos_pessoa_pessoa_to_string;
        }
        
        $values = DocumentosPessoa::where('tipo_documento_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->nome}');
        return implode(', ', $values);
    }

    
    public function set_documentos_pessoa_tipo_documento_to_string($documentos_pessoa_tipo_documento_to_string)
    {
        if(is_array($documentos_pessoa_tipo_documento_to_string))
        {
            $values = TipoDocumento::where('id', 'in', $documentos_pessoa_tipo_documento_to_string)->getIndexedArray('id', 'id');
            $this->documentos_pessoa_tipo_documento_to_string = implode(', ', $values);
        }
        else
        {
            $this->documentos_pessoa_tipo_documento_to_string = $documentos_pessoa_tipo_documento_to_string;
        }

        $this->vdata['documentos_pessoa_tipo_documento_to_string'] = $this->documentos_pessoa_tipo_documento_to_string;
    }

    public function get_documentos_pessoa_tipo_documento_to_string()
    {
        if(!empty($this->documentos_pessoa_tipo_documento_to_string))
        {
            return $this->documentos_pessoa_tipo_documento_to_string;
        }
        
        $values = DocumentosPessoa::where('tipo_documento_id', '=', $this->id)->getIndexedArray('tipo_documento_id','{tipo_documento->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
        //<onBeforeDeleteCode>
  
        //</onBeforeDeleteCode>

        if(DocumentosPessoa::where('tipo_documento_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
        
    }
    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

