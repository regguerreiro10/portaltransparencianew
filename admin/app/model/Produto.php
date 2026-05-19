<?php

//<fileHeader>
  
//</fileHeader>

class Produto extends TRecord
{
    const TABLENAME  = 'produto';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private SystemUnit $system_unit;
    private TipoProduto $tipo_produto;
    private FamiliaProduto $familia_produto;
    private Fabricante $fabricante;
    private UnidadeMedida $unidade_medida;
    private Pessoa $fornecedor;
    private SystemUsers $system_users;
    
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
        parent::addAttribute('codigo_sinapi');
        parent::addAttribute('tipo_produto_id');
        parent::addAttribute('familia_produto_id');
        parent::addAttribute('unidade_medida_id');
        parent::addAttribute('fornecedor_id');
        parent::addAttribute('fabricante_id');
        parent::addAttribute('nome');
        parent::addAttribute('cod_barras');
        parent::addAttribute('preco_venda');
        parent::addAttribute('preco_custo');
        parent::addAttribute('peso_liquido');
        parent::addAttribute('peso_bruto');
        parent::addAttribute('largura');
        parent::addAttribute('altura');
        parent::addAttribute('volume');
        parent::addAttribute('estoque_minimo');
        parent::addAttribute('qtde_estoque');
        parent::addAttribute('estoque_maximo');
        parent::addAttribute('obs');
        parent::addAttribute('ativo');
        parent::addAttribute('foto');
        parent::addAttribute('data_ultimo_reajuste_preco');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('codigo_orse');
                parent::addAttribute('suiv_grupo_id');
        parent::addAttribute('suiv_nickname_id');
        parent::addAttribute('suiv_peca_id');
        parent::addAttribute('suiv_preco_peca');
        parent::addAttribute('suiv_partnumber');
        parent::addAttribute('suiv_tempo_mao_obra_id');
        parent::addAttribute('suiv_tempo_servico');


        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_system_unit
     * Sample of usage: $var->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $var->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
        
        // returns the associated object
        return $this->system_unit;
    }
    /**
     * Method set_tipo_produto
     * Sample of usage: $var->tipo_produto = $object;
     * @param $object Instance of TipoProduto
     */
    public function set_tipo_produto(TipoProduto $object)
    {
        $this->tipo_produto = $object;
        $this->tipo_produto_id = $object->id;
    }
    
    /**
     * Method get_tipo_produto
     * Sample of usage: $var->tipo_produto->attribute;
     * @returns TipoProduto instance
     */
    public function get_tipo_produto()
    {
        
        // loads the associated object
        if (empty($this->tipo_produto))
            $this->tipo_produto = new TipoProduto($this->tipo_produto_id);
        
        // returns the associated object
        return $this->tipo_produto;
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
   public function get_nome_suiv()
    {
        if (!empty($this->suiv_nickname_id)) {
            // Com SUIV
            return "🟢 {$this->nome} - SUIV {$this->suiv_nickname_id}";
        }

        // Sem SUIV
        return "🔴 {$this->nome} - SEM SUIV";
    }

    public function get_nome_com_familia()
    {
        $nome = trim((string) $this->nome);
        if ($nome === '') {
            $nome = '(Sem nome)';
        }

        $familia = '';
        if (!empty($this->familia_produto_id)) {
            try {
                $familia_obj = $this->get_familia_produto();
                $familia = trim((string) ($familia_obj->nome ?? ''));
            } catch (Exception $e) {
                $familia = '';
            }
        }

        $nomeExibicao = $familia !== '' ? "{$nome} - {$familia}" : $nome;

        $ehTemparia = !empty($this->suiv_preco_peca)
            || !empty($this->suiv_tempo_mao_obra_id)
            || !empty($this->suiv_peca_id)
            || !empty($this->suiv_nickname_id);

        if ($ehTemparia) {
            $nomeExibicao .= " - <span style='color:#fff; background:#f39c12; padding:2px 6px; border-radius:10px; font-size:10px; font-weight:bold;'>TABELA TEMPARIA</span>";
        }

        return $nomeExibicao;
    }

    public function get_nome_servico_suiv()
    {
        if (!empty($this->suiv_peca_id)) {
            // Com SUIV
            return "🟢 {$this->nome} - SUIV {$this->suiv_peca_id}";
        }

        // Sem SUIV
        return "🔴 {$this->nome} - SEM SUIV";
    }

    /**
     * Method set_fabricante
     * Sample of usage: $var->fabricante = $object;
     * @param $object Instance of Fabricante
     */
    public function set_fabricante(Fabricante $object)
    {
        $this->fabricante = $object;
        $this->fabricante_id = $object->id;
    }
    
    /**
     * Method get_fabricante
     * Sample of usage: $var->fabricante->attribute;
     * @returns Fabricante instance
     */
    public function get_fabricante()
    {
        
        // loads the associated object
        if (empty($this->fabricante))
            $this->fabricante = new Fabricante($this->fabricante_id);
        
        // returns the associated object
        return $this->fabricante;
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
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_fornecedor(Pessoa $object)
    {
        $this->fornecedor = $object;
        $this->fornecedor_id = $object->id;
    }
    
    /**
     * Method get_fornecedor
     * Sample of usage: $var->fornecedor->attribute;
     * @returns Pessoa instance
     */
    public function get_fornecedor()
    {
        
        // loads the associated object
        if (empty($this->fornecedor))
            $this->fornecedor = new Pessoa($this->fornecedor_id);
        
        // returns the associated object
        return $this->fornecedor;
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
     * Method getItensCotacaos
     */
    public function getItensCotacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('produto_id', '=', $this->id));
        return ItensCotacao::getObjects( $criteria );
    }
    /**
     * Method getItensPedidos
     */
    public function getItensPedidos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('produto_id', '=', $this->id));
        return ItensPedido::getObjects( $criteria );
    }
    /**
     * Method getNegociacaoItems
     */
    public function getNegociacaoItems()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('produto_id', '=', $this->id));
        return NegociacaoItem::getObjects( $criteria );
    }
    /**
     * Method getProdutoSystemUnits
     */
    public function getProdutoSystemUnits()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('produto_id', '=', $this->id));
        return ProdutoSystemUnit::getObjects( $criteria );
    }
    /**
     * Method getItensPropostass
     */
    public function getItensPropostass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('produto_id', '=', $this->id));
        return ItensPropostas::getObjects( $criteria );
    }
    /**
     * Method getManutencaoGarantias
     */
    public function getManutencaoGarantias()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('produto_id', '=', $this->id));
        return ManutencaoGarantia::getObjects( $criteria );
    }
    /**
     * Method getItensPedidoFrotass
     */
    public function getItensPedidoFrotass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('produto_id', '=', $this->id));
        return ItensPedidoFrotas::getObjects( $criteria );
    }

    
    public function set_itens_cotacao_produto_to_string($itens_cotacao_produto_to_string)
    {
        if(is_array($itens_cotacao_produto_to_string))
        {
            $values = Produto::where('id', 'in', $itens_cotacao_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->itens_cotacao_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_cotacao_produto_to_string = $itens_cotacao_produto_to_string;
        }

        $this->vdata['itens_cotacao_produto_to_string'] = $this->itens_cotacao_produto_to_string;
    }

    public function get_itens_cotacao_produto_to_string()
    {
        if(!empty($this->itens_cotacao_produto_to_string))
        {
            return $this->itens_cotacao_produto_to_string;
        }
        
        $values = ItensCotacao::where('produto_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

    
    public function set_itens_cotacao_cotacao_to_string($itens_cotacao_cotacao_to_string)
    {
        if(is_array($itens_cotacao_cotacao_to_string))
        {
            $values = Cotacao::where('id', 'in', $itens_cotacao_cotacao_to_string)->getIndexedArray('id', 'id');
            $this->itens_cotacao_cotacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_cotacao_cotacao_to_string = $itens_cotacao_cotacao_to_string;
        }

        $this->vdata['itens_cotacao_cotacao_to_string'] = $this->itens_cotacao_cotacao_to_string;
    }

    public function get_itens_cotacao_cotacao_to_string()
    {
        if(!empty($this->itens_cotacao_cotacao_to_string))
        {
            return $this->itens_cotacao_cotacao_to_string;
        }
        
        $values = ItensCotacao::where('produto_id', '=', $this->id)->getIndexedArray('cotacao_id','{cotacao->id}');
        return implode(', ', $values);
    }

    
    public function set_itens_cotacao_estado_pedido_to_string($itens_cotacao_estado_pedido_to_string)
    {
        if(is_array($itens_cotacao_estado_pedido_to_string))
        {
            $values = EstadoPedido::where('id', 'in', $itens_cotacao_estado_pedido_to_string)->getIndexedArray('nome', 'nome');
            $this->itens_cotacao_estado_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_cotacao_estado_pedido_to_string = $itens_cotacao_estado_pedido_to_string;
        }

        $this->vdata['itens_cotacao_estado_pedido_to_string'] = $this->itens_cotacao_estado_pedido_to_string;
    }

    public function get_itens_cotacao_estado_pedido_to_string()
    {
        if(!empty($this->itens_cotacao_estado_pedido_to_string))
        {
            return $this->itens_cotacao_estado_pedido_to_string;
        }
        
        $values = ItensCotacao::where('produto_id', '=', $this->id)->getIndexedArray('estado_pedido_id','{estado_pedido->nome}');
        return implode(', ', $values);
    }

    
    public function set_itens_cotacao_itens_pedido_to_string($itens_cotacao_itens_pedido_to_string)
    {
        if(is_array($itens_cotacao_itens_pedido_to_string))
        {
            $values = ItensPedido::where('id', 'in', $itens_cotacao_itens_pedido_to_string)->getIndexedArray('id', 'id');
            $this->itens_cotacao_itens_pedido_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_cotacao_itens_pedido_to_string = $itens_cotacao_itens_pedido_to_string;
        }

        $this->vdata['itens_cotacao_itens_pedido_to_string'] = $this->itens_cotacao_itens_pedido_to_string;
    }

    public function get_itens_cotacao_itens_pedido_to_string()
    {
        if(!empty($this->itens_cotacao_itens_pedido_to_string))
        {
            return $this->itens_cotacao_itens_pedido_to_string;
        }
        
        $values = ItensCotacao::where('produto_id', '=', $this->id)->getIndexedArray('itens_pedido_id','{itens_pedido->id}');
        return implode(', ', $values);
    }

    
    public function set_itens_cotacao_unidade_medida_to_string($itens_cotacao_unidade_medida_to_string)
    {
        if(is_array($itens_cotacao_unidade_medida_to_string))
        {
            $values = UnidadeMedida::where('id', 'in', $itens_cotacao_unidade_medida_to_string)->getIndexedArray('nome', 'nome');
            $this->itens_cotacao_unidade_medida_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_cotacao_unidade_medida_to_string = $itens_cotacao_unidade_medida_to_string;
        }

        $this->vdata['itens_cotacao_unidade_medida_to_string'] = $this->itens_cotacao_unidade_medida_to_string;
    }

    public function get_itens_cotacao_unidade_medida_to_string()
    {
        if(!empty($this->itens_cotacao_unidade_medida_to_string))
        {
            return $this->itens_cotacao_unidade_medida_to_string;
        }
        
        $values = ItensCotacao::where('produto_id', '=', $this->id)->getIndexedArray('unidade_medida_id','{unidade_medida->nome}');
        return implode(', ', $values);
    }

    
    public function set_itens_pedido_pedido_venda_to_string($itens_pedido_pedido_venda_to_string)
    {
        if(is_array($itens_pedido_pedido_venda_to_string))
        {
            $values = Pedido::where('id', 'in', $itens_pedido_pedido_venda_to_string)->getIndexedArray('id', 'id');
            $this->itens_pedido_pedido_venda_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_pedido_pedido_venda_to_string = $itens_pedido_pedido_venda_to_string;
        }

        $this->vdata['itens_pedido_pedido_venda_to_string'] = $this->itens_pedido_pedido_venda_to_string;
    }

    public function get_itens_pedido_pedido_venda_to_string()
    {
        if(!empty($this->itens_pedido_pedido_venda_to_string))
        {
            return $this->itens_pedido_pedido_venda_to_string;
        }
        
        $values = ItensPedido::where('produto_id', '=', $this->id)->getIndexedArray('pedido_venda_id','{pedido_venda->id}');
        return implode(', ', $values);
    }

    
    public function set_itens_pedido_produto_to_string($itens_pedido_produto_to_string)
    {
        if(is_array($itens_pedido_produto_to_string))
        {
            $values = Produto::where('id', 'in', $itens_pedido_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->itens_pedido_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_pedido_produto_to_string = $itens_pedido_produto_to_string;
        }

        $this->vdata['itens_pedido_produto_to_string'] = $this->itens_pedido_produto_to_string;
    }

    public function get_itens_pedido_produto_to_string()
    {
        if(!empty($this->itens_pedido_produto_to_string))
        {
            return $this->itens_pedido_produto_to_string;
        }
        
        $values = ItensPedido::where('produto_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

    
    public function set_itens_pedido_unidade_medida_to_string($itens_pedido_unidade_medida_to_string)
    {
        if(is_array($itens_pedido_unidade_medida_to_string))
        {
            $values = UnidadeMedida::where('id', 'in', $itens_pedido_unidade_medida_to_string)->getIndexedArray('nome', 'nome');
            $this->itens_pedido_unidade_medida_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_pedido_unidade_medida_to_string = $itens_pedido_unidade_medida_to_string;
        }

        $this->vdata['itens_pedido_unidade_medida_to_string'] = $this->itens_pedido_unidade_medida_to_string;
    }

    public function get_itens_pedido_unidade_medida_to_string()
    {
        if(!empty($this->itens_pedido_unidade_medida_to_string))
        {
            return $this->itens_pedido_unidade_medida_to_string;
        }
        
        $values = ItensPedido::where('produto_id', '=', $this->id)->getIndexedArray('unidade_medida_id','{unidade_medida->nome}');
        return implode(', ', $values);
    }

    
    public function set_negociacao_item_produto_to_string($negociacao_item_produto_to_string)
    {
        if(is_array($negociacao_item_produto_to_string))
        {
            $values = Produto::where('id', 'in', $negociacao_item_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->negociacao_item_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->negociacao_item_produto_to_string = $negociacao_item_produto_to_string;
        }

        $this->vdata['negociacao_item_produto_to_string'] = $this->negociacao_item_produto_to_string;
    }

    public function get_negociacao_item_produto_to_string()
    {
        if(!empty($this->negociacao_item_produto_to_string))
        {
            return $this->negociacao_item_produto_to_string;
        }
        
        $values = NegociacaoItem::where('produto_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

    
    public function set_negociacao_item_negociacao_to_string($negociacao_item_negociacao_to_string)
    {
        if(is_array($negociacao_item_negociacao_to_string))
        {
            $values = Negociacao::where('id', 'in', $negociacao_item_negociacao_to_string)->getIndexedArray('id', 'id');
            $this->negociacao_item_negociacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->negociacao_item_negociacao_to_string = $negociacao_item_negociacao_to_string;
        }

        $this->vdata['negociacao_item_negociacao_to_string'] = $this->negociacao_item_negociacao_to_string;
    }

    public function get_negociacao_item_negociacao_to_string()
    {
        if(!empty($this->negociacao_item_negociacao_to_string))
        {
            return $this->negociacao_item_negociacao_to_string;
        }
        
        $values = NegociacaoItem::where('produto_id', '=', $this->id)->getIndexedArray('negociacao_id','{negociacao->id}');
        return implode(', ', $values);
    }

    
    public function set_produto_system_unit_system_unit_to_string($produto_system_unit_system_unit_to_string)
    {
        if(is_array($produto_system_unit_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $produto_system_unit_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->produto_system_unit_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_system_unit_system_unit_to_string = $produto_system_unit_system_unit_to_string;
        }

        $this->vdata['produto_system_unit_system_unit_to_string'] = $this->produto_system_unit_system_unit_to_string;
    }

    public function get_produto_system_unit_system_unit_to_string()
    {
        if(!empty($this->produto_system_unit_system_unit_to_string))
        {
            return $this->produto_system_unit_system_unit_to_string;
        }
        
        $values = ProdutoSystemUnit::where('produto_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    
    public function set_produto_system_unit_produto_to_string($produto_system_unit_produto_to_string)
    {
        if(is_array($produto_system_unit_produto_to_string))
        {
            $values = Produto::where('id', 'in', $produto_system_unit_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_system_unit_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_system_unit_produto_to_string = $produto_system_unit_produto_to_string;
        }

        $this->vdata['produto_system_unit_produto_to_string'] = $this->produto_system_unit_produto_to_string;
    }

    public function get_produto_system_unit_produto_to_string()
    {
        if(!empty($this->produto_system_unit_produto_to_string))
        {
            return $this->produto_system_unit_produto_to_string;
        }
        
        $values = ProdutoSystemUnit::where('produto_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

    
    public function set_itens_propostas_propostas_to_string($itens_propostas_propostas_to_string)
    {
        if(is_array($itens_propostas_propostas_to_string))
        {
            $values = Propostas::where('id', 'in', $itens_propostas_propostas_to_string)->getIndexedArray('id', 'id');
            $this->itens_propostas_propostas_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_propostas_propostas_to_string = $itens_propostas_propostas_to_string;
        }

        $this->vdata['itens_propostas_propostas_to_string'] = $this->itens_propostas_propostas_to_string;
    }

    public function get_itens_propostas_propostas_to_string()
    {
        if(!empty($this->itens_propostas_propostas_to_string))
        {
            return $this->itens_propostas_propostas_to_string;
        }
        
        $values = ItensPropostas::where('produto_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
        return implode(', ', $values);
    }

    
    public function set_itens_propostas_itens_pedido_frotas_to_string($itens_propostas_itens_pedido_frotas_to_string)
    {
        if(is_array($itens_propostas_itens_pedido_frotas_to_string))
        {
            $values = ItensPedidoFrotas::where('id', 'in', $itens_propostas_itens_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->itens_propostas_itens_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_propostas_itens_pedido_frotas_to_string = $itens_propostas_itens_pedido_frotas_to_string;
        }

        $this->vdata['itens_propostas_itens_pedido_frotas_to_string'] = $this->itens_propostas_itens_pedido_frotas_to_string;
    }

    public function get_itens_propostas_itens_pedido_frotas_to_string()
    {
        if(!empty($this->itens_propostas_itens_pedido_frotas_to_string))
        {
            return $this->itens_propostas_itens_pedido_frotas_to_string;
        }
        
        $values = ItensPropostas::where('produto_id', '=', $this->id)->getIndexedArray('itens_pedido_frotas_id','{itens_pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_itens_propostas_tipo_pecas_to_string($itens_propostas_tipo_pecas_to_string)
    {
        if(is_array($itens_propostas_tipo_pecas_to_string))
        {
            $values = TipoPecas::where('id', 'in', $itens_propostas_tipo_pecas_to_string)->getIndexedArray('id', 'id');
            $this->itens_propostas_tipo_pecas_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_propostas_tipo_pecas_to_string = $itens_propostas_tipo_pecas_to_string;
        }

        $this->vdata['itens_propostas_tipo_pecas_to_string'] = $this->itens_propostas_tipo_pecas_to_string;
    }

    public function get_itens_propostas_tipo_pecas_to_string()
    {
        if(!empty($this->itens_propostas_tipo_pecas_to_string))
        {
            return $this->itens_propostas_tipo_pecas_to_string;
        }
        
        $values = ItensPropostas::where('produto_id', '=', $this->id)->getIndexedArray('tipo_pecas_id','{tipo_pecas->id}');
        return implode(', ', $values);
    }

    
    public function set_itens_propostas_estado_pedido_frotas_to_string($itens_propostas_estado_pedido_frotas_to_string)
    {
        if(is_array($itens_propostas_estado_pedido_frotas_to_string))
        {
            $values = EstadoPedidoFrotas::where('id', 'in', $itens_propostas_estado_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->itens_propostas_estado_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_propostas_estado_pedido_frotas_to_string = $itens_propostas_estado_pedido_frotas_to_string;
        }

        $this->vdata['itens_propostas_estado_pedido_frotas_to_string'] = $this->itens_propostas_estado_pedido_frotas_to_string;
    }

    public function get_itens_propostas_estado_pedido_frotas_to_string()
    {
        if(!empty($this->itens_propostas_estado_pedido_frotas_to_string))
        {
            return $this->itens_propostas_estado_pedido_frotas_to_string;
        }
        
        $values = ItensPropostas::where('produto_id', '=', $this->id)->getIndexedArray('estado_pedido_frotas_id','{estado_pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_itens_propostas_produto_to_string($itens_propostas_produto_to_string)
    {
        if(is_array($itens_propostas_produto_to_string))
        {
            $values = Produto::where('id', 'in', $itens_propostas_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->itens_propostas_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_propostas_produto_to_string = $itens_propostas_produto_to_string;
        }

        $this->vdata['itens_propostas_produto_to_string'] = $this->itens_propostas_produto_to_string;
    }

    public function get_itens_propostas_produto_to_string()
    {
        if(!empty($this->itens_propostas_produto_to_string))
        {
            return $this->itens_propostas_produto_to_string;
        }
        
        $values = ItensPropostas::where('produto_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
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
        
        $values = ManutencaoGarantia::where('produto_id', '=', $this->id)->getIndexedArray('itens_propostas_id','{itens_propostas->id}');
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
        
        $values = ManutencaoGarantia::where('produto_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    
    public function set_manutencao_garantia_pedido_frotas_to_string($manutencao_garantia_pedido_frotas_to_string)
    {
        if(is_array($manutencao_garantia_pedido_frotas_to_string))
        {
            $values = PedidoFrotas::where('id', 'in', $manutencao_garantia_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->manutencao_garantia_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_garantia_pedido_frotas_to_string = $manutencao_garantia_pedido_frotas_to_string;
        }

        $this->vdata['manutencao_garantia_pedido_frotas_to_string'] = $this->manutencao_garantia_pedido_frotas_to_string;
    }

    public function get_manutencao_garantia_pedido_frotas_to_string()
    {
        if(!empty($this->manutencao_garantia_pedido_frotas_to_string))
        {
            return $this->manutencao_garantia_pedido_frotas_to_string;
        }
        
        $values = ManutencaoGarantia::where('produto_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_manutencao_garantia_propostas_to_string($manutencao_garantia_propostas_to_string)
    {
        if(is_array($manutencao_garantia_propostas_to_string))
        {
            $values = Propostas::where('id', 'in', $manutencao_garantia_propostas_to_string)->getIndexedArray('id', 'id');
            $this->manutencao_garantia_propostas_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_garantia_propostas_to_string = $manutencao_garantia_propostas_to_string;
        }

        $this->vdata['manutencao_garantia_propostas_to_string'] = $this->manutencao_garantia_propostas_to_string;
    }

    public function get_manutencao_garantia_propostas_to_string()
    {
        if(!empty($this->manutencao_garantia_propostas_to_string))
        {
            return $this->manutencao_garantia_propostas_to_string;
        }
        
        $values = ManutencaoGarantia::where('produto_id', '=', $this->id)->getIndexedArray('propostas_id','{propostas->id}');
        return implode(', ', $values);
    }

    
    public function set_manutencao_garantia_produto_to_string($manutencao_garantia_produto_to_string)
    {
        if(is_array($manutencao_garantia_produto_to_string))
        {
            $values = Produto::where('id', 'in', $manutencao_garantia_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->manutencao_garantia_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_garantia_produto_to_string = $manutencao_garantia_produto_to_string;
        }

        $this->vdata['manutencao_garantia_produto_to_string'] = $this->manutencao_garantia_produto_to_string;
    }

    public function get_manutencao_garantia_produto_to_string()
    {
        if(!empty($this->manutencao_garantia_produto_to_string))
        {
            return $this->manutencao_garantia_produto_to_string;
        }
        
        $values = ManutencaoGarantia::where('produto_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

    
    public function set_itens_pedido_frotas_pedido_frotas_to_string($itens_pedido_frotas_pedido_frotas_to_string)
    {
        if(is_array($itens_pedido_frotas_pedido_frotas_to_string))
        {
            $values = PedidoFrotas::where('id', 'in', $itens_pedido_frotas_pedido_frotas_to_string)->getIndexedArray('id', 'id');
            $this->itens_pedido_frotas_pedido_frotas_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_pedido_frotas_pedido_frotas_to_string = $itens_pedido_frotas_pedido_frotas_to_string;
        }

        $this->vdata['itens_pedido_frotas_pedido_frotas_to_string'] = $this->itens_pedido_frotas_pedido_frotas_to_string;
    }

    public function get_itens_pedido_frotas_pedido_frotas_to_string()
    {
        if(!empty($this->itens_pedido_frotas_pedido_frotas_to_string))
        {
            return $this->itens_pedido_frotas_pedido_frotas_to_string;
        }
        
        $values = ItensPedidoFrotas::where('produto_id', '=', $this->id)->getIndexedArray('pedido_frotas_id','{pedido_frotas->id}');
        return implode(', ', $values);
    }

    
    public function set_itens_pedido_frotas_produto_to_string($itens_pedido_frotas_produto_to_string)
    {
        if(is_array($itens_pedido_frotas_produto_to_string))
        {
            $values = Produto::where('id', 'in', $itens_pedido_frotas_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->itens_pedido_frotas_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->itens_pedido_frotas_produto_to_string = $itens_pedido_frotas_produto_to_string;
        }

        $this->vdata['itens_pedido_frotas_produto_to_string'] = $this->itens_pedido_frotas_produto_to_string;
    }

    public function get_itens_pedido_frotas_produto_to_string()
    {
        if(!empty($this->itens_pedido_frotas_produto_to_string))
        {
            return $this->itens_pedido_frotas_produto_to_string;
        }
        
        $values = ItensPedidoFrotas::where('produto_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

