<?php

//<fileHeader>

//</fileHeader>

class ItensCotacao extends TRecord
{
    const TABLENAME  = 'itens_cotacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private Cotacao $cotacao;
    private Produto $produto;
    private EstadoPedido $estado_pedido;
    private ItensPedido $itens_pedido;
    private UnidadeMedida $unidade_medida;
    
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
        parent::addAttribute('produto_id');
        parent::addAttribute('qtde');
        parent::addAttribute('valor');
        parent::addAttribute('valor_total');
        parent::addAttribute('valor_sinapi');
        parent::addAttribute('cotacao_id');
        parent::addAttribute('estado_pedido_id');
        parent::addAttribute('itens_pedido_id');
        parent::addAttribute('deleted_at');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('unidade_medida_id');
        //<onAfterConstruct>

        //</onAfterConstruct>
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
    /**
     * Method set_produto
     * Sample of usage: $var->produto = $object;
     * @param $object Instance of Produto
     */
    public function set_produto(Produto $object)
    {
        $this->produto = $object;
        $this->produto_id = $object->id;
    }
    
    /**
     * Method get_produto
     * Sample of usage: $var->produto->attribute;
     * @returns Produto instance
     */
    public function get_produto()
    {
        
        // loads the associated object
        if (empty($this->produto))
            $this->produto = new Produto($this->produto_id);
        
        // returns the associated object
        return $this->produto;
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
     * Method set_itens_pedido
     * Sample of usage: $var->itens_pedido = $object;
     * @param $object Instance of ItensPedido
     */
    public function set_itens_pedido(ItensPedido $object)
    {
        $this->itens_pedido = $object;
        $this->itens_pedido_id = $object->id;
    }
    
    /**
     * Method get_itens_pedido
     * Sample of usage: $var->itens_pedido->attribute;
     * @returns ItensPedido instance
     */
    public function get_itens_pedido()
    {
        
        // loads the associated object
        if (empty($this->itens_pedido))
            $this->itens_pedido = new ItensPedido($this->itens_pedido_id);
        
        // returns the associated object
        return $this->itens_pedido;
    }
    /**
     * Method set_unidade_medida
     * Sample of usage: $var->unidade_medida = $object;
     * @param $object Instance of UnidadeMedida
     */
    public function set_unidade_medida(UnidadeMedida $object)
    {
        $this->unidade_medida = $object;
        $this->unidade_medida_id = $object->id;
    }
    
    /**
     * Method get_unidade_medida
     * Sample of usage: $var->unidade_medida->attribute;
     * @returns UnidadeMedida instance
     */
    public function get_unidade_medida()
    {
        
        // loads the associated object
        if (empty($this->unidade_medida))
            $this->unidade_medida = new UnidadeMedida($this->unidade_medida_id);
        
        // returns the associated object
        return $this->unidade_medida;
    }
    

    
    //<userCustomFunctions>

    public static function isBloqueioValorTempariaAtivo()
    {
        return self::getConfiguracaoTempariaAtual('bloqueio_valor_temparia') === 1;
    }

    private static function getConfiguracaoTempariaAtual($campo)
    {
        $valorSessao = TSession::getValue($campo);
        if ($valorSessao !== null && $valorSessao !== '') {
            return (int) $valorSessao;
        }

        $unitId = (int) (TSession::getValue('idunit') ?? TSession::getValue('userunitid') ?? 0);
        if ($unitId <= 0) {
            return 0;
        }

        $openedHere = false;

        if (!TTransaction::getDatabase()) {
            TTransaction::open('minierp');
            $openedHere = true;
        }

        try {
            $unit = new SystemUnit($unitId);
            return (int) ($unit->{$campo} ?? 0);
        } catch (Exception $e) {
            return 0;
        } finally {
            if ($openedHere && TTransaction::getDatabase()) {
                TTransaction::close();
            }
        }
    }

    private function get_taxa_contrato_atual()
    {
        $taxaSessao = TSession::getValue('taxacontrato');
        if ($taxaSessao !== null && $taxaSessao !== '') {
            return (float) $taxaSessao;
        }

        $entidadeId = (int) TSession::getValue('entidade');
        if ($entidadeId <= 0) {
            return 0.0;
        }

        try {
            $entidade = new Entidade($entidadeId);
            return (float) ($entidade->taxacontrato ?? 0);
        } catch (Exception $e) {
            return 0.0;
        }
    }

    public function get_valor_referencia_bloqueio()
    {
        if (!self::isBloqueioValorTempariaAtivo() || empty($this->produto_id)) {
            return null;
        }

        $produto = $this->get_produto();
        $codigoSinapi = trim((string) ($produto->codigo_sinapi ?? ''));

        if ($codigoSinapi === '' || (is_numeric($codigoSinapi) && (float) $codigoSinapi == 0.0)) {
            return null;
        }

        $precoVenda = (float) ($produto->preco_venda ?? 0);

        return $precoVenda > 0 ? $precoVenda : null;
    }

    public function get_valor_unitario_com_desconto_bloqueio()
    {
        $valor = (float) $this->valor;
        $descontoContratual = $this->get_taxa_contrato_atual() / 100;

        return $valor - ($valor * $descontoContratual);
    }

    public function excede_valor_bloqueio()
    {
        $valorReferencia = $this->get_valor_referencia_bloqueio();

        if ($valorReferencia === null) {
            return false;
        }

        return $this->get_valor_unitario_com_desconto_bloqueio() > $valorReferencia;
    }

    public function get_descricao_divergencia_bloqueio()
    {
        if (!$this->excede_valor_bloqueio()) {
            return null;
        }

        $produto = !empty($this->produto_id) ? $this->get_produto() : null;
        $nomeItem = $produto ? $produto->nome : "Item {$this->id}";

        return sprintf(
            '%s: valor com desconto R$ %s maior que tabela R$ %s',
            $nomeItem,
            number_format($this->get_valor_unitario_com_desconto_bloqueio(), 2, ',', '.'),
            number_format((float) $this->get_valor_referencia_bloqueio(), 2, ',', '.')
        );
    }

    public static function getDivergenciasBloqueioPorCotacao($cotacaoId, $itemIds = null)
    {
        if (!self::isBloqueioValorTempariaAtivo()) {
            return [];
        }

        $criteria = new TCriteria;
        $criteria->add(new TFilter('cotacao_id', '=', $cotacaoId));

        if (is_array($itemIds)) {
            $itemIds = array_values(array_filter($itemIds));
            if (empty($itemIds)) {
                return [];
            }

            $criteria->add(new TFilter('id', 'in', $itemIds));
        }

        $itens = self::getObjects($criteria);
        $divergencias = [];

        if ($itens) {
            foreach ($itens as $item) {
                if ($item->excede_valor_bloqueio()) {
                    $divergencias[] = $item->get_descricao_divergencia_bloqueio();
                }
            }
        }

        return array_values(array_filter($divergencias));
    }

    //</userCustomFunctions>
}

