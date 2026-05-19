<?php

class TaxasRedes extends TRecord
{
    const TABLENAME  = 'taxas_redes';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('adm');
        parent::addAttribute('bancaria');
        parent::addAttribute('desconto');
        parent::addAttribute('conectividade');
        parent::addAttribute('adesao');
        parent::addAttribute('manutencao');
        parent::addAttribute('anuidade');
        parent::addAttribute('ir');
        parent::addAttribute('csll');
        parent::addAttribute('confins');
        parent::addAttribute('pis');
        parent::addAttribute('iss');
        parent::addAttribute('optante');
        parent::addAttribute('estabelecimento_id');
        parent::addAttribute('usuario');
        parent::addAttribute('data');
        parent::addAttribute('ip');
        parent::addAttribute('ir_servico');
        parent::addAttribute('csll_servico');
        parent::addAttribute('confins_servico');
        parent::addAttribute('pis_servico');
        parent::addAttribute('iss_servico');
        parent::addAttribute('column_24');
            
    }

    
}

