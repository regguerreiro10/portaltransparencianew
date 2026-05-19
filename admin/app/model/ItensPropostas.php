<?php

//<fileHeader>
  
//</fileHeader>

class ItensPropostas extends TRecord
{
    const TABLENAME  = 'itens_propostas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private $propostas;
        private TipoPecas $tipo_pecas;

    
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
        parent::addAttribute('tipo');
        parent::addAttribute('descricao');
        parent::addAttribute('qtdekmgarantia');
        parent::addAttribute('diasdegarantia');
        parent::addAttribute('qtdehoras');
        parent::addAttribute('qtde');
        parent::addAttribute('valor');
        parent::addAttribute('perc_desconto');
        parent::addAttribute('valor_total');
        parent::addAttribute('marca_modelo');
        parent::addAttribute('fabricante');
        parent::addAttribute('codigo');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('itens_pedido_frotas_id');
        parent::addAttribute('estado_pedido_frotas_id');
        parent::addAttribute('tipo_pecas_id');
        parent::addAttribute('familia_produto_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('tbo_horas');
        parent::addAttribute('tbo_ciclos');
        parent::addAttribute('tsn_horas');
        parent::addAttribute('tso_horas');
        parent::addAttribute('csn_ciclos');
        parent::addAttribute('cso_ciclos');
        parent::addAttribute('uso');
        parent::addAttribute('finalidade');
        parent::addAttribute('aplicacao');
        parent::addAttribute('valor_ajuste_arredondamento');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }
 /**
     * Method set_familia_produto
     * Sample of usage: $var->familia_produto = $object;
     * @param $object Instance of FamiliaProduto
     */
    public function set_familia_produto(FamiliaProduto $object)
    {
        $this->familia_produto = $object;
        $this->familia_produto_id = $object->id;
    }
    
    /**
     * Method get_familia_produto
     * Sample of usage: $var->familia_produto->attribute;
     * @returns FamiliaProduto instance
     */
    public function get_familia_produto()
    {
        
        // loads the associated object
        if (empty($this->familia_produto))
            $this->familia_produto = new FamiliaProduto($this->familia_produto_id);
        
        // returns the associated object
        return $this->familia_produto;
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
     * Method get_propostas
     * Sample of usage: $var->propostas->attribute;
     * @returns Propostas instance
     */
    public function get_propostas()
    {
        
        // loads the associated object
        if (empty($this->propostas))
            $this->propostas = new Propostas($this->propostas_id);
        
        // returns the associated object
        return $this->propostas;
    }

  
    
    
    
    /**
     * Method getManutencaoGarantias
     */
    public function getManutencaoGarantias()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('itens_propostas_id', '=', $this->id));
        return ManutencaoGarantia::getObjects( $criteria );
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
    
    
    public function set_manutencao_garantia_itens_propostas_to_string($manutencao_garantia_itens_propostas_to_string)
    {
        if(is_array($manutencao_garantia_itens_propostas_to_string))
        {
            $values = ItensPropostas::where('id', 'in', $manutencao_garantia_itens_propostas_to_string)->getIndexedArray('id', 'id');
            $this->manutencao_garantia_itens_propostas_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_garantia_itens_propostas_to_string = $manutencao_garantia_itens_propostas_to_string;
        }

        $this->vdata['manutencao_garantia_itens_propostas_to_string'] = $this->manutencao_garantia_itens_propostas_to_string;
    }

    public function get_manutencao_garantia_itens_propostas_to_string()
    {
        if(!empty($this->manutencao_garantia_itens_propostas_to_string))
        {
            return $this->manutencao_garantia_itens_propostas_to_string;
        }
        
        $values = ManutencaoGarantia::where('itens_propostas_id', '=', $this->id)->getIndexedArray('itens_propostas_id','{itens_propostas->id}');
        return implode(', ', $values);
    }

    
    public function set_manutencao_garantia_veiculos_to_string($manutencao_garantia_veiculos_to_string)
    {
        if(is_array($manutencao_garantia_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $manutencao_garantia_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->manutencao_garantia_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_garantia_veiculos_to_string = $manutencao_garantia_veiculos_to_string;
        }

        $this->vdata['manutencao_garantia_veiculos_to_string'] = $this->manutencao_garantia_veiculos_to_string;
    }

    public function get_manutencao_garantia_veiculos_to_string()
    {
        if(!empty($this->manutencao_garantia_veiculos_to_string))
        {
            return $this->manutencao_garantia_veiculos_to_string;
        }
        
        $values = ManutencaoGarantia::where('itens_propostas_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

      /**
     * Method set_tipo_pecas
     * Sample of usage: $var->tipo_pecas = $object;
     * @param $object Instance of TipoPecas
     */
    public function set_tipo_pecas(TipoPecas $object)
    {
        $this->tipo_pecas = $object;
        $this->tipo_pecas_id = $object->id;
    }
    
    /**
     * Method get_tipo_pecas
     * Sample of usage: $var->tipo_pecas->attribute;
     * @returns TipoPecas instance
     */
    public function get_tipo_pecas()
    {
        
        // loads the associated object
        if (empty($this->tipo_pecas))
            $this->tipo_pecas = new TipoPecas($this->tipo_pecas_id);
        
        // returns the associated object
        return $this->tipo_pecas;
    }

    public static function isBloqueioValorTempariaAtivo()
    {
        return self::getConfiguracaoTempariaAtual('bloqueio_valor_temparia') === 1;
    }

    public static function isUtilizaTempariaAtivo()
    {
        return self::getConfiguracaoTempariaAtual('utiliza_temparia') === 1;
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

        $unitId = (int) (TSession::getValue('idunit') ?? TSession::getValue('userunitid') ?? 0);
        if ($unitId <= 0) {
            return 0.0;
        }

        $openedHere = false;

        if (!TTransaction::getDatabase()) {
            TTransaction::open('minierp');
            $openedHere = true;
        }

        try {
            $unit = new SystemUnit($unitId);
            if (empty($unit->entidade_id)) {
                return 0.0;
            }

            $entidade = new Entidade($unit->entidade_id);
            return (float) ($entidade->taxacontrato ?? 0);
        } catch (Exception $e) {
            return 0.0;
        } finally {
            if ($openedHere && TTransaction::getDatabase()) {
                TTransaction::close();
            }
        }
    }

    public function get_veiculo_id_referencia_suiv()
    {
        if (empty($this->propostas_id)) {
            return null;
        }

        $proposta = $this->get_propostas();
        $veiculoId = (int) ($proposta->veiculos_id ?? 0);

        if ($veiculoId <= 0 && !empty($proposta->pedido_frotas_id)) {
            $pedido = $proposta->get_pedido_frotas();
            $veiculoId = (int) ($pedido->veiculos_id ?? 0);
        }

        return $veiculoId > 0 ? $veiculoId : null;
    }

    public function get_produto_preco_veiculo_referencia()
    {
        $veiculoId = $this->get_veiculo_id_referencia_suiv();

        if (empty($veiculoId) || empty($this->produto_id)) {
            return null;
        }

        return ProdutoPrecoVeiculo::where('produto_id', '=', $this->produto_id)
                                  ->where('veiculos_id', '=', $veiculoId)
                                  ->first();
    }

    public function get_valor_referencia_suiv()
    {
        if (!self::isUtilizaTempariaAtivo() || empty($this->produto_id) || (int) $this->tipo !== 1) {
            return null;
        }

        $produtoPrecoVeiculo = $this->get_produto_preco_veiculo_referencia();
        if (empty($produtoPrecoVeiculo)) {
            return null;
        }

        $valorReferencia = (float) ($produtoPrecoVeiculo->suiv_preco_peca ?? 0);

        return $valorReferencia > 0 ? $valorReferencia : null;
    }

    public function excede_tabela_suiv()
    {
        if (!self::isUtilizaTempariaAtivo() || !self::isBloqueioValorTempariaAtivo() || (int) $this->tipo !== 1) {
            return false;
        }

        $valorReferencia = $this->get_valor_referencia_suiv();

        if ($valorReferencia === null) {
            return false;
        }
        $descontocontratual = ($this->get_taxa_contrato_atual() / 100);
        $valorproduto = (float) $this->valor;
        $valorcomdesconto = $valorproduto - ($valorproduto * $descontocontratual);
        if ((int) $this->tipo === 1) {
            return $valorcomdesconto  > $valorReferencia;
        }

        return false;
    }

    public function get_descricao_divergencia_suiv()
    {
        if (!$this->excede_tabela_suiv()) {
            return null;
        }

        $produto = !empty($this->produto_id) ? $this->get_produto() : null;
        $nomeItem = $produto ? $produto->nome : ($this->descricao ?: "Item {$this->id}");
        $valorComDesconto = (float) $this->valor;

        if ((int) $this->tipo === 1) {
            $descontoContratual = ($this->get_taxa_contrato_atual() / 100);
            $valorComDesconto = $valorComDesconto - ($valorComDesconto * $descontoContratual);
        }

        if ((int) $this->tipo === 1) {
            return sprintf(
                '%s: valor com desconto R$ %s maior que SUIV R$ %s',
                $nomeItem,
                number_format($valorComDesconto, 2, ',', '.'),
                number_format((float) $this->get_valor_referencia_suiv(), 2, ',', '.')
            );
        }

        return sprintf(
            '%s: horas informadas %s maior que SUIV %s',
            $nomeItem,
            number_format((float) $this->qtde, 2, ',', '.'),
            number_format((float) $this->get_valor_referencia_suiv(), 2, ',', '.')
        );
    }

    public static function getDivergenciasSuivPorProposta($propostaId)
    {
        if (!self::isBloqueioValorTempariaAtivo()) {
            return [];
        }

        $divergencias = [];
        $itens = self::where('propostas_id', '=', $propostaId)->load();

        if ($itens) {
            foreach ($itens as $item) {
                if ($item->excede_tabela_suiv()) {
                    $divergencias[] = $item->get_descricao_divergencia_suiv();
                }
            }
        }

        return array_values(array_filter($divergencias));
    }

   /**
     * Method set_familia_produto
     * Sample of usage: $var->familia_produto = $object;
     * @param $object Instance of FamiliaProduto
     */
    // public function set_familia_produto(FamiliaProduto $object)
    // {
    //     $this->familia_produto = $object;
    //     $this->familia_produto_id = $object->id;
    // }
    
    /**
     * Method get_familia_produto
     * Sample of usage: $var->familia_produto->attribute;
     * @returns FamiliaProduto instance
     */
    // public function get_familia_produto()
    // {
        
    //     // loads the associated object
    //     if (empty($this->familia_produto))
    //         $this->familia_produto = new FamiliaProduto($this->familia_produto_id);
        
    //     // returns the associated object
    //     return $this->familia_produto;
    // }   
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}
