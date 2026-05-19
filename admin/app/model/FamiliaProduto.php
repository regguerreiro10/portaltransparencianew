<?php

class FamiliaProduto extends TRecord
{
    const TABLENAME  = 'familia_produto';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('suiv_id');
    }

    /**
     * Method getProdutos
     */
    public function getProdutos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('familia_produto_id', '=', $this->id));
        return Produto::getObjects($criteria);
    }

    public function set_produto_tipo_produto_to_string($produto_tipo_produto_to_string)
    {
        if (is_array($produto_tipo_produto_to_string))
        {
            $values = TipoProduto::where('id', 'in', $produto_tipo_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_tipo_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_tipo_produto_to_string = $produto_tipo_produto_to_string;
        }

        $this->vdata['produto_tipo_produto_to_string'] = $this->produto_tipo_produto_to_string;
    }

    public function get_produto_tipo_produto_to_string()
    {
        if (!empty($this->produto_tipo_produto_to_string))
        {
            return $this->produto_tipo_produto_to_string;
        }

        $values = Produto::where('familia_produto_id', '=', $this->id)->getIndexedArray('tipo_produto_id', '{tipo_produto->nome}');
        return implode(', ', $values);
    }

    public function get_nome_familia_suiv()
    {
        $total_produtos = Produto::where('familia_produto_id', '=', $this->id)->count();

        if ($total_produtos > 0) {
            return "ðŸŸ¢ {$this->nome} - TOTAL PRODUTOS: {$total_produtos}";
        } else {
            return "ðŸ”´ {$this->nome} - SEM PRODUTOS";
        }
    }

    public function get_nome_familia_servico_suiv()
    {
        $total_servicos = Produto::where('familia_produto_id', '=', $this->id)
                                 ->where('tipo_produto_id', '=', 2)->count();

        if ($total_servicos > 0) {
            return "ðŸŸ¢ {$this->nome} - TOTAL SERVIÃ‡OS: {$total_servicos}";
        } else {
            return "ðŸ”´ {$this->nome} - SEM SERVIÃ‡OS";
        }
    }

    public function set_produto_familia_produto_to_string($produto_familia_produto_to_string)
    {
        if (is_array($produto_familia_produto_to_string))
        {
            $values = FamiliaProduto::where('id', 'in', $produto_familia_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_familia_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_familia_produto_to_string = $produto_familia_produto_to_string;
        }

        $this->vdata['produto_familia_produto_to_string'] = $this->produto_familia_produto_to_string;
    }

    public function get_produto_familia_produto_to_string()
    {
        if (!empty($this->produto_familia_produto_to_string))
        {
            return $this->produto_familia_produto_to_string;
        }

        $values = Produto::where('familia_produto_id', '=', $this->id)->getIndexedArray('familia_produto_id', '{familia_produto->nome}');
        return implode(', ', $values);
    }

    public function set_produto_unidade_medida_to_string($produto_unidade_medida_to_string)
    {
        if (is_array($produto_unidade_medida_to_string))
        {
            $values = UnidadeMedida::where('id', 'in', $produto_unidade_medida_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_unidade_medida_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_unidade_medida_to_string = $produto_unidade_medida_to_string;
        }

        $this->vdata['produto_unidade_medida_to_string'] = $this->produto_unidade_medida_to_string;
    }

    public function get_produto_unidade_medida_to_string()
    {
        if (!empty($this->produto_unidade_medida_to_string))
        {
            return $this->produto_unidade_medida_to_string;
        }

        $values = Produto::where('familia_produto_id', '=', $this->id)->getIndexedArray('unidade_medida_id', '{unidade_medida->nome}');
        return implode(', ', $values);
    }

    public function set_produto_fornecedor_to_string($produto_fornecedor_to_string)
    {
        if (is_array($produto_fornecedor_to_string))
        {
            $values = Pessoa::where('id', 'in', $produto_fornecedor_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_fornecedor_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_fornecedor_to_string = $produto_fornecedor_to_string;
        }

        $this->vdata['produto_fornecedor_to_string'] = $this->produto_fornecedor_to_string;
    }

    public function get_produto_fornecedor_to_string()
    {
        if (!empty($this->produto_fornecedor_to_string))
        {
            return $this->produto_fornecedor_to_string;
        }

        $values = Produto::where('familia_produto_id', '=', $this->id)->getIndexedArray('fornecedor_id', '{fornecedor->nome}');
        return implode(', ', $values);
    }

    public function set_produto_fabricante_to_string($produto_fabricante_to_string)
    {
        if (is_array($produto_fabricante_to_string))
        {
            $values = Fabricante::where('id', 'in', $produto_fabricante_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_fabricante_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_fabricante_to_string = $produto_fabricante_to_string;
        }

        $this->vdata['produto_fabricante_to_string'] = $this->produto_fabricante_to_string;
    }

    public function get_produto_fabricante_to_string()
    {
        if (!empty($this->produto_fabricante_to_string))
        {
            return $this->produto_fabricante_to_string;
        }

        $values = Produto::where('familia_produto_id', '=', $this->id)->getIndexedArray('fabricante_id', '{fabricante->nome}');
        return implode(', ', $values);
    }

    public function set_produto_system_users_to_string($produto_system_users_to_string)
    {
        if (is_array($produto_system_users_to_string))
        {
            $values = SystemUsers::where('id', 'in', $produto_system_users_to_string)->getIndexedArray('name', 'name');
            $this->produto_system_users_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_system_users_to_string = $produto_system_users_to_string;
        }

        $this->vdata['produto_system_users_to_string'] = $this->produto_system_users_to_string;
    }

    public function get_produto_system_users_to_string()
    {
        if (!empty($this->produto_system_users_to_string))
        {
            return $this->produto_system_users_to_string;
        }

        $values = Produto::where('familia_produto_id', '=', $this->id)->getIndexedArray('system_users_id', '{system_users->name}');
        return implode(', ', $values);
    }

    public function get_nome_familia_suiv_veiculo()
    {
        $total_produtos = Produto::where('familia_produto_id', '=', $this->id)->count();
        $familias_veiculo = TSession::getValue('familiaIDS') ?: [];
        $session_pedido = TSession::getValue('pedido_frotas_form_data');
        $veiculo_id = $session_pedido->veiculos_id ?? null;
        $placa = '';

        if (!empty($veiculo_id)) {
            $veiculo = new Veiculos($veiculo_id);
            $placa = trim((string) ($veiculo->placa ?? ''));
        }

        $prefixo_veiculo = in_array($this->id, $familias_veiculo)
            ? '🟢 [' . ($placa ?: 'VEICULO SELECIONADO') . '] '
            : '⚪ ';

        if ($total_produtos > 0) {
            return "{$prefixo_veiculo}{$this->nome} - TOTAL PRODUTOS: {$total_produtos}";
        }

        return "{$prefixo_veiculo}{$this->nome} - SEM PRODUTOS";
    }

    public function get_nome_familia_servico_suiv_veiculo()
    {
        $total_servicos = Produto::where('familia_produto_id', '=', $this->id)
                                 ->where('tipo_produto_id', '=', 2)
                                 ->count();
        $familias_veiculo = TSession::getValue('familiaIDS') ?: [];
        $session_pedido = TSession::getValue('pedido_frotas_form_data');
        $veiculo_id = $session_pedido->veiculos_id ?? null;
        $placa = '';

        if (!empty($veiculo_id)) {
            $veiculo = new Veiculos($veiculo_id);
            $placa = trim((string) ($veiculo->placa ?? ''));
        }

        $prefixo_veiculo = in_array($this->id, $familias_veiculo)
            ? '🟢 [' . ($placa ?: 'VEICULO SELECIONADO') . '] '
            : '⚪ ';

        if ($total_servicos > 0) {
            return "{$prefixo_veiculo}{$this->nome} - TOTAL SERVICOS: {$total_servicos}";
        }

        return "{$prefixo_veiculo}{$this->nome} - SEM SERVICOS";
    }
}
