<?php

//<fileHeader>
  
//</fileHeader>

class ViewPropostas extends TRecord
{
    const TABLENAME  = 'view_propostas';
    const PRIMARYKEY = 'id';
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
        parent::addAttribute('pedido_frotas_id');
        parent::addAttribute('estabelecimento_id');
        parent::addAttribute('estado_pedido_frotas_id');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('placa');
        parent::addAttribute('modelo');
        parent::addAttribute('data_cotacao');
        parent::addAttribute('obs');
        parent::addAttribute('valor_total');
        parent::addAttribute('valor_desconto');
        parent::addAttribute('valor_liquido');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('data_entrada_veiculo');
        parent::addAttribute('horimetro_entrada_aeronave');
        parent::addAttribute('ciclos_entrada_aeronave');
        parent::addAttribute('data_retirada_veiculo');
        parent::addAttribute('horimetro_retirada_aeronave');
        parent::addAttribute('ciclos_retirada_aeronave');
        parent::addAttribute('data_previsao_entrega');
        parent::addAttribute('km');
        parent::addAttribute('ciclos');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('responsavel_tecnico');
        parent::addAttribute('datahora_inicioservico');
        parent::addAttribute('horimetro_inicioservico');
        parent::addAttribute('ciclos_inicioservico');
        parent::addAttribute('datahora_fimservico');
        parent::addAttribute('horimetro_fimservico');
        parent::addAttribute('ciclos_fimservico');
        parent::addAttribute('total_produtos_sem_desconto');
        parent::addAttribute('total_servicos_sem_desconto');
        parent::addAttribute('total_geral_sem_desconto');
        parent::addAttribute('total_produtos_com_desconto');
        parent::addAttribute('total_servicos_com_desconto');
        parent::addAttribute('desconto_contratual');
        parent::addAttribute('motorista_entrada_id');
        parent::addAttribute('total_geral_com_desconto');
        parent::addAttribute('entidade_id');
        parent::addAttribute('cidade_id');
        parent::addAttribute('motorista_retirada_id');
        parent::addAttribute('data_limite_resposta');
        parent::addAttribute('estado_pedido_frotas1_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    


    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

