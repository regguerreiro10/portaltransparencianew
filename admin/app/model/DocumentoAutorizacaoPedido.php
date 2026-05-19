<?php

//<fileHeader>
  
//</fileHeader>

class DocumentoAutorizacaoPedido extends TRecord
{
    const TABLENAME  = 'documento_autorizacao_pedido';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private AutorizacaoPedido $autorizacao_pedido;
    
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
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('autorizacao_pedido_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_autorizacao_pedido
     * Sample of usage: $var->autorizacao_pedido = $object;
     * @param $object Instance of AutorizacaoPedido
     */
    public function set_autorizacao_pedido(AutorizacaoPedido $object)
    {
        $this->autorizacao_pedido = $object;
        $this->autorizacao_pedido_id = $object->id;
    }
    
    /**
     * Method get_autorizacao_pedido
     * Sample of usage: $var->autorizacao_pedido->attribute;
     * @returns AutorizacaoPedido instance
     */
    public function get_autorizacao_pedido()
    {
        
        // loads the associated object
        if (empty($this->autorizacao_pedido))
            $this->autorizacao_pedido = new AutorizacaoPedido($this->autorizacao_pedido_id);
        
        // returns the associated object
        return $this->autorizacao_pedido;
    }
    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

