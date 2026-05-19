<?php

//<fileHeader>
  
//</fileHeader>

class ViewConsumosrealizados extends TRecord
{
    const TABLENAME  = 'view_consumosrealizados';
    const PRIMARYKEY = 'pedido_frotas_id';
    const IDPOLICY   =  'max'; // {max, serial}
    

    
    
    
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
        parent::addAttribute('veiculos_id');
        parent::addAttribute('placa');
        parent::addAttribute('marca');
        parent::addAttribute('modelo');
        parent::addAttribute('anof');
        parent::addAttribute('km');
        parent::addAttribute('fornecedor');
        parent::addAttribute('total_geral_com_desconto');
        parent::addAttribute('cidade_id');
        parent::addAttribute('nome_cidade');
        parent::addAttribute('estado_id');
        parent::addAttribute('sigla_estado');
        parent::addAttribute('mes');
        parent::addAttribute('ano');
        parent::addAttribute('valor_transacao_credito');
        parent::addAttribute('valor_transacao_debito');
        parent::addAttribute('saldo_atual');
        parent::addAttribute('km_anterior');
        parent::addAttribute('km_rodado');
        parent::addAttribute('custo_por_km');
        parent::addAttribute('qtd_pedidos_mes');
        parent::addAttribute('total_mensal_manutencao');
        parent::addAttribute('custo_medio_mensal');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    

    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

