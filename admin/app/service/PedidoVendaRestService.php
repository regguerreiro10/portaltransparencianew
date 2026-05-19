<?php

class PedidoVendaRestService
{
    public static function salvar($param)
    {
        try 
        {
            TTransaction::open(MAIN_DATABASE);
        
            $pedidoVenda = new PedidoVenda();
            $pedidoVenda->tipo_pedido_id = $param['tipo_pedido_id'];
            $pedidoVenda->cliente_id = $param['cliente_id'];
            $pedidoVenda->vendedor_id = $param['vendedor_id'];
            $pedidoVenda->estado_pedido_venda_id = $param['estado_pedido_venda_id'];
            $pedidoVenda->condicao_pagamento_id = $param['condicao_pagamento_id'];
            $pedidoVenda->transportadora_id = $param['transportadora_id'];
            $pedidoVenda->dt_pedido = $param['dt_pedido'];
            $pedidoVenda->obs = $param['obs'];
            $pedidoVenda->valor_total = 0; // deixamos ele como zero sempre, pois iremos calcular dentro do foreach de itens
            
            $dataPedido = new DateTime($pedidoVenda->dt_pedido); //formato americano Y-m-d
            
            $pedidoVenda->mes = $dataPedido->format('m');
            $pedidoVenda->ano = $dataPedido->format('Y');
            
            $pedidoVenda->store();
            
            if(!empty($param['itens']))
            {
                foreach($param['itens'] as $item)
                {
                    $pedidoVendaItem = new PedidoVendaItem();
                    $pedidoVendaItem->pedido_venda_id = $pedidoVenda->id;
                    
                    $pedidoVendaItem->produto_id = $item['produto_id'];
                    $pedidoVendaItem->quantidade = $item['quantidade'];
                    $pedidoVendaItem->valor = $item['valor'];
                    $pedidoVendaItem->desconto = $item['desconto'];
                    $pedidoVendaItem->valor_total = $item['valor_total'];
                    
                    $pedidoVendaItem->store();
                    
                    $pedidoVenda->valor_total += $pedidoVendaItem->valor_total;
                }
                
                $pedidoVenda->store();
            }
            
            TTransaction::close();
            
            return true;
        } 
        catch (Exception $e) 
        {
            TTransaction::rollback();
            throw $e;
        }
    }
}
