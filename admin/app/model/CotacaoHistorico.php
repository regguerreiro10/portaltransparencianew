<?php

class CotacaoHistorico extends TRecord
{
    const TABLENAME  = 'cotacao_historico';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $estado_pedido;
    private $aprovador;
    private $cotacao;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('estado_pedido_id');
        parent::addAttribute('data_historico');
        parent::addAttribute('obs');
        parent::addAttribute('aprovador_id');
        parent::addAttribute('cotacao_id');
            
    }

    /**
     * Method set_estado_pedido
     * Sample of usage: $var->estado_pedido = $object;
     * @param $object Instance of EstadoPedido
     */
    public function set_estado_pedido(EstadoPedido $object)
    {
        $this->estado_pedido = $object;
        $this->estado_pedido_id = $object->id;
    }

    /**
     * Method get_estado_pedido
     * Sample of usage: $var->estado_pedido->attribute;
     * @returns EstadoPedido instance
     */
    public function get_estado_pedido()
    {
    
        // loads the associated object
        if (empty($this->estado_pedido))
            $this->estado_pedido = new EstadoPedido($this->estado_pedido_id);
    
        // returns the associated object
        return $this->estado_pedido;
    }
    /**
     * Method set_aprovador
     * Sample of usage: $var->aprovador = $object;
     * @param $object Instance of Aprovador
     */
    public function set_aprovador(Aprovador $object)
    {
        $this->aprovador = $object;
        $this->aprovador_id = $object->id;
    }

    /**
     * Method get_aprovador
     * Sample of usage: $var->aprovador->attribute;
     * @returns Aprovador instance
     */
    public function get_aprovador()
    {
    
        // loads the associated object
        if (empty($this->aprovador))
            $this->aprovador = new Aprovador($this->aprovador_id);
    
        // returns the associated object
        return $this->aprovador;
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

