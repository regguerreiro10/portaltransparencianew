<?php

class SaldoDepartamento extends TRecord
{
    const TABLENAME  = 'saldo_departamento';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    const SERVICO = 1;
    const PRODUTO = 2;
    
    const DEBITO = 'D';
    const CREDITO = 'C';

    private DepartamentoUnit $departamento_unit;
    private StatusSaldoDepartamento $status_saldo_departamento_obj;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('tipotransacao');
        parent::addAttribute('datatransacao');
        parent::addAttribute('historico');
        parent::addAttribute('saldo_produto');
        parent::addAttribute('saldo_servico');
        parent::addAttribute('documento_empenho');
        parent::addAttribute('numero_documento_empenho');
        parent::addAttribute('data_documento_empenho');
        parent::addAttribute('saldo_entidade_contrato_id');
        parent::addAttribute('saldo_total');
        parent::addAttribute('tipo');
        parent::addAttribute('status_saldo_departamento_id');
        parent::addAttribute('data_processo');
        parent::addAttribute('numero_processo');
        parent::addAttribute('system_users_id');
        parent::addAttribute('data_anulado');

    }

    /**
     * Constructor method
     */
    public function get_saldo_empenho_servico()
    {
       $depart = SaldoDepartamento::where('departamento_unit_id', '=', $this->departamento_unit_id)
                                    ->where('datatransacao', '<=', $this->datatransacao)
                                    ->where('id', '<=', $this->id)
                                   ->load();

        $saldo_sdebito = 0;
        $saldo_scredito = 0;
        $empenho_servico = 0;

        foreach($depart as $dep)
        {
            if($dep->tipotransacao == SaldoDepartamento::DEBITO)
            {
                $saldo_sdebito += $dep->saldo_servico;
            }
            elseif($dep->tipotransacao == SaldoDepartamento::CREDITO)            
            {
                $saldo_scredito += $dep->saldo_servico;
            }
        }
        

        $empenho_servico = $saldo_scredito - $saldo_sdebito;


        //somar itens do pedido de servico ate a data do pedido atual
        $itens = Viewsaldoempenho::where('dt_pedido', '<=', $this->datatransacao)
                                  ->where('departamento_unit_id', '=', $this->departamento_unit_id)
                                  ->load();

        $total_servico = 0;
        if($itens)
        {                                   
            foreach($itens as $item)
            {
                    $total_servico += $item->total_servicos;
            }
        }
        

        $empenho_servico = $empenho_servico - $total_servico;

        return 'R$ ' . number_format((float) $empenho_servico, 2, ',', '.');
    }

    public function get_saldo_empenho_produto()
    {
        $depart = SaldoDepartamento::where('departamento_unit_id', '=', $this->departamento_unit_id)
                                    ->where('datatransacao', '<=', $this->datatransacao)
                                    ->load();

        $saldo_pdebito = 0;
        $saldo_pcredito = 0;
        $empenho_produto = 0;

        foreach($depart as $dep)
        {
            if($dep->tipotransacao == SaldoDepartamento::DEBITO)
            {
                $saldo_pdebito += $dep->saldo_produto;
            }
            elseif($dep->tipotransacao == SaldoDepartamento::CREDITO)            
            {
                $saldo_pcredito += $dep->saldo_produto;
            }
        }

        $empenho_produto = $saldo_pcredito - $saldo_pdebito;

        //somar itens do pedido de produto ate a data do pedido atual
        $itens = Viewsaldoempenho::where('dt_pedido', '<=', $this->datatransacao)
                                  ->where('departamento_unit_id', '=', $this->departamento_unit_id)
                                  ->load();

        $total_produto = 0;
        if($itens)
        {                                   
            foreach($itens as $item)
            {
                    $total_produto += $item->total_produtos;
            }
        }
        
        $empenho_produto = $empenho_produto - $total_produto;
        

        return 'R$ ' . number_format((float) $empenho_produto, 2, ',', '.');
    }

      public function get_valor_item_produto()
    {

        //somar itens do pedido de produto ate a data do pedido atual
        $itens = Viewsaldoempenho::where('dt_pedido', '<=', $this->datatransacao)
                                  ->where('departamento_unit_id', '=', $this->departamento_unit_id)
                                  ->load();

        $total_produto = 0;
        if($itens)
        {                                   
            foreach($itens as $item)
            {
                    $total_produto += $item->total_produtos;
            }
        }
        
               

        return 'R$ ' . number_format((float) $total_produto, 2, ',', '.');
    }

      /**
     * Constructor method
     */
    public function get_valor_item_servico()
    {
     


        //somar itens do pedido de servico ate a data do pedido atual
        $itens = Viewsaldoempenho::where('dt_pedido', '<=', $this->datatransacao)
                                  ->where('departamento_unit_id', '=', $this->departamento_unit_id)
                                  ->load();

        $total_servico = 0;
        if($itens)
        {                                   
            foreach($itens as $item)
            {
                    $total_servico += $item->total_servicos;
            }
        }
        

       
        return 'R$ ' . number_format((float) $total_servico, 2, ',', '.');
    }
    // public function get_total_itens_produtos()
    // {
    //     $pedido = PedidoFrotas::where('dt_pedido', '<=', $this->dt_pedido)
    //                             ->load();
                                
        
    //     $total_produto = 0;  
    //     foreach($pedido as $ped)
    //     {

    //         $itens = ItensPedidoFrotas::where('pedido_frotas_id', '=', $ped->id)
    //                                 ->load();

            
                                            
    //         foreach($itens as $item)
    //         {
    //             if($item->tipo == '2')
    //             {
    //                 $total_produto += $item->valor_total;
    //             }
    //         }
    //     }

    //     return $total_produto;
    // }

    // public function get_total_itens_servico()
    // {
    //     $pedido = PedidoFrotas::where('dt_pedido', '<=', $this->dt_pedido)
    //                             ->load();
                                
        
    //     $valor_servico = 0;  
    //     foreach($pedido as $ped)
    //     {

    //         $itens = ItensPedidoFrotas::where('pedido_frotas_id', '=', $ped->id)
    //                                 ->load();

                                             
    //         foreach($itens as $item)
    //         {
    //             if($item->tipo == '1')
    //             {
    //                 $valor_servico += $item->valor_total;
    //             }
    //         }
    //     }

    //     return $valor_servico;
    // }

    /**
     * Method set_departamento_unit
     * Sample of usage: $var->departamento_unit = $object;
     * @param $object Instance of DepartamentoUnit
     */
    public function set_departamento_unit(DepartamentoUnit $object)
    {
        $this->departamento_unit = $object;
        $this->departamento_unit_id = $object->id;
    }

    /**
     * Method get_departamento_unit
     * Sample of usage: $var->departamento_unit->attribute;
     * @returns DepartamentoUnit instance
     */
    public function get_departamento_unit()
    {
    
        // loads the associated object
        if (empty($this->departamento_unit))
            $this->departamento_unit = new DepartamentoUnit($this->departamento_unit_id);
    
        // returns the associated object
        return $this->departamento_unit;
    }
    
    public function get_status_saldo_departamento_obj()
    {
        if (empty($this->status_saldo_departamento_obj) && !empty($this->status_saldo_departamento_id))
        {
            $this->status_saldo_departamento_obj = new StatusSaldoDepartamento($this->status_saldo_departamento_id);
        }

        return $this->status_saldo_departamento_obj;
    }

    public function get_valor_empenho_formatado()
    {
        return 'R$ ' . number_format($this->saldo_total, 2, ',', '.');
    }

    public function get_tipos()
    {
        if (!isset($this->tipo)) return '-';
        return $this->tipo === 'P' ? 'Produto' : 'Serviço';
    }

    public function get_status_saldo_departamento()
    {
        if (empty($this->status_saldo_departamento_id))
        {
            return '-';
        }

        try
        {
            $status = $this->get_status_saldo_departamento_obj();

            if (empty($status))
            {
                return '-';
            }

            $descricao = !empty($status->descricao) ? htmlspecialchars((string) $status->descricao, ENT_QUOTES, 'UTF-8') : 'Sem status';
            $cor = $this->normalizeStatusColor($status->cor ?: $this->getDefaultStatusColor());
            $textColor = $this->getContrastTextColor($cor);

            return "<span style='background-color:{$cor}; color: {$textColor}; padding: 2px 9px; border-radius: 10px; font-weight: bold; font-size:12px'>{$descricao}</span>";
        }
        catch (Exception $e)
        {
            return '-';
        }
    }
    public function get_departamento_unit1()
    {
        return new DepartamentoUnit($this->departamento_unit_id);
    }

    private function getDefaultStatusColor()
    {
        switch ((string) $this->status_saldo_departamento_id) {
            case '1':
                return '#e1c752f6';
            case '2':
                return '#4bad138a';
            case '3':
                return '#cb1111ce';
            case '4':
                return '#0000008d';
            default:
                return '#6C757D';
        }
    }

    private function normalizeStatusColor($color)
    {
        $color = trim((string) $color);

        if ($color === '')
        {
            return $this->getDefaultStatusColor();
        }

        if (preg_match('/^#[0-9a-fA-F]{6}$/', $color))
        {
            return $color;
        }

        if (preg_match('/^[0-9a-fA-F]{6}$/', $color))
        {
            return '#' . $color;
        }

        return $color;
    }

    private function getContrastTextColor($backgroundColor)
    {
        if (!preg_match('/^#([0-9a-fA-F]{6})$/', $backgroundColor, $matches))
        {
            return '#FFFFFF';
        }

        $hex = $matches[1];
        $red = hexdec(substr($hex, 0, 2));
        $green = hexdec(substr($hex, 2, 2));
        $blue = hexdec(substr($hex, 4, 2));
        $luminance = (($red * 299) + ($green * 587) + ($blue * 114)) / 1000;

        return $luminance >= 160 ? '#222222' : '#FFFFFF';
    }

    
}


