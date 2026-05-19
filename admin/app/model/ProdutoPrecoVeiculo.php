<?php

//<fileHeader>
  
//</fileHeader>

class ProdutoPrecoVeiculo extends TRecord
{
    const TABLENAME  = 'produto_preco_veiculo';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    

    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';
    
    
    private Produto $produto;
    private Veiculos $veiculos;
    
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
        parent::addAttribute('veiculos_id');
        parent::addAttribute('preco_venda');
        parent::addAttribute('suiv_grupo');
        parent::addAttribute('suiv_nickname_id');
        parent::addAttribute('suiv_peca_id');
        parent::addAttribute('suiv_preco_peca');
        parent::addAttribute('suiv_part_number');
        parent::addAttribute('suiv_tempo_mao_obra_id');
        parent::addAttribute('suiv_tempo_servico');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
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
     * Method set_veiculos
     * Sample of usage: $var->veiculos = $object;
     * @param $object Instance of Veiculos
     */
    public function set_veiculos(Veiculos $object)
    {
        $this->veiculos = $object;
        $this->veiculos_id = $object->id;
    }
    
    /**
     * Method get_veiculos
     * Sample of usage: $var->veiculos->attribute;
     * @returns Veiculos instance
     */
    public function get_veiculos()
    {
        
        // loads the associated object
        if (empty($this->veiculos))
            $this->veiculos = new Veiculos($this->veiculos_id);
        
        // returns the associated object
        return $this->veiculos;
    }
    


    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

