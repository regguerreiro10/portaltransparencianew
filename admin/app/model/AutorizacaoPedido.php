<?php

class AutorizacaoPedido extends TRecord
{
    const TABLENAME  = 'autorizacao_pedido';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private PedidoFrotas $pedido_frotas;
    private Veiculos $veiculos;
    private SystemUsers $system_users;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pedido_frotas_id');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('data_autorizacao');
        parent::addAttribute('historico');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method set_pedido_frotas
     * Sample of usage: $var->pedido_frotas = $object;
     * @param $object Instance of PedidoFrotas
     */
    public function set_pedido_frotas(PedidoFrotas $object)
    {
        $this->pedido_frotas = $object;
        $this->pedido_frotas_id = $object->id;
    }

    /**
     * Method get_pedido_frotas
     * Sample of usage: $var->pedido_frotas->attribute;
     * @returns PedidoFrotas instance
     */
    public function get_pedido_frotas()
    {
    
        // loads the associated object
        if (empty($this->pedido_frotas))
            $this->pedido_frotas = new PedidoFrotas($this->pedido_frotas_id);
    
        // returns the associated object
        return $this->pedido_frotas;
    }
    /**
     * Method set_veiculos
     * Sample of usage: $var->veiculos = $object;
     * @param $object Instance of Veiculos
     */
    public function set_veiculos(Veiculos $object)
    {
        $this->veiculos = $object;
        $this->veiculos_id = $object->id;
    }

    /**
     * Method get_veiculos
     * Sample of usage: $var->veiculos->attribute;
     * @returns Veiculos instance
     */
    public function get_veiculos()
    {
    
        // loads the associated object
        if (empty($this->veiculos))
            $this->veiculos = new Veiculos($this->veiculos_id);
    
        // returns the associated object
        return $this->veiculos;
    }
    /**
     * Method set_system_users
     * Sample of usage: $var->system_users = $object;
     * @param $object Instance of SystemUsers
     */
    public function set_system_users(SystemUsers $object)
    {
        $this->system_users = $object;
        $this->system_users_id = $object->id;
    }

    /**
     * Method get_system_users
     * Sample of usage: $var->system_users->attribute;
     * @returns SystemUsers instance
     */
    public function get_system_users()
    {
    
        // loads the associated object
        if (empty($this->system_users))
            $this->system_users = new SystemUsers($this->system_users_id);
    
        // returns the associated object
        return $this->system_users;
    }

    /**
     * Method getDocumentoAutorizacaoPedidos
     */
    public function getDocumentoAutorizacaoPedidos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('autorizacao_pedido_id', '=', $this->id));
        return DocumentoAutorizacaoPedido::getObjects( $criteria );
    }

    public function set_documento_autorizacao_pedido_autorizacao_pedido_to_string($documento_autorizacao_pedido_autorizacao_pedido_to_string)
    {
        if(is_array($documento_autorizacao_pedido_autorizacao_pedido_to_string))
        {
            $values = AutorizacaoPedido::where('id', 'in', $documento_autorizacao_pedido_autorizacao_pedido_to_string)->getIndexedArray('id', 'id');
            $this->documento_autorizacao_pedido_autorizacao_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->documento_autorizacao_pedido_autorizacao_pedido_to_string = $documento_autorizacao_pedido_autorizacao_pedido_to_string;
        }

        $this->vdata['documento_autorizacao_pedido_autorizacao_pedido_to_string'] = $this->documento_autorizacao_pedido_autorizacao_pedido_to_string;
    }

    public function get_documento_autorizacao_pedido_autorizacao_pedido_to_string()
    {
        if(!empty($this->documento_autorizacao_pedido_autorizacao_pedido_to_string))
        {
            return $this->documento_autorizacao_pedido_autorizacao_pedido_to_string;
        }
    
        $values = DocumentoAutorizacaoPedido::where('autorizacao_pedido_id', '=', $this->id)->getIndexedArray('autorizacao_pedido_id','{autorizacao_pedido->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(DocumentoAutorizacaoPedido::where('autorizacao_pedido_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

