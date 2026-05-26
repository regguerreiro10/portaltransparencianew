<?php

class Tabela extends TRecord
{
    const TABLENAME  = 'tabela';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method getTabelaDeTabelas
     */
    public function getTabelaDeTabelas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tabela_id', '=', $this->id));
        return TabelaDeTabela::getObjects( $criteria );
    }

    public function set_tabela_de_tabela_tabela_to_string($tabela_de_tabela_tabela_to_string)
    {
        if(is_array($tabela_de_tabela_tabela_to_string))
        {
            $values = Tabela::where('id', 'in', $tabela_de_tabela_tabela_to_string)->getIndexedArray('id', 'id');
            $this->tabela_de_tabela_tabela_to_string = implode(', ', $values);
        }
        else
        {
            $this->tabela_de_tabela_tabela_to_string = $tabela_de_tabela_tabela_to_string;
        }

        $this->vdata['tabela_de_tabela_tabela_to_string'] = $this->tabela_de_tabela_tabela_to_string;
    }

    public function get_tabela_de_tabela_tabela_to_string()
    {
        if(!empty($this->tabela_de_tabela_tabela_to_string))
        {
            return $this->tabela_de_tabela_tabela_to_string;
        }
    
        $values = TabelaDeTabela::where('tabela_id', '=', $this->id)->getIndexedArray('tabela_id','{tabela->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(TabelaDeTabela::where('tabela_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

