<?php

class SituacaoVeiculo extends TRecord
{
    const TABLENAME  = 'situacao_veiculo';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('ativar_desativar');
            
    }

    /**
     * Method getManutencaoVeiculos
     */
    public function getManutencaoVeiculos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('situacao_veiculo_id', '=', $this->id));
        return ManutencaoVeiculo::getObjects( $criteria );
    }

    public function set_manutencao_veiculo_situacao_veiculo_to_string($manutencao_veiculo_situacao_veiculo_to_string)
    {
        if(is_array($manutencao_veiculo_situacao_veiculo_to_string))
        {
            $values = SituacaoVeiculo::where('id', 'in', $manutencao_veiculo_situacao_veiculo_to_string)->getIndexedArray('id', 'id');
            $this->manutencao_veiculo_situacao_veiculo_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_veiculo_situacao_veiculo_to_string = $manutencao_veiculo_situacao_veiculo_to_string;
        }

        $this->vdata['manutencao_veiculo_situacao_veiculo_to_string'] = $this->manutencao_veiculo_situacao_veiculo_to_string;
    }

    public function get_manutencao_veiculo_situacao_veiculo_to_string()
    {
        if(!empty($this->manutencao_veiculo_situacao_veiculo_to_string))
        {
            return $this->manutencao_veiculo_situacao_veiculo_to_string;
        }
    
        $values = ManutencaoVeiculo::where('situacao_veiculo_id', '=', $this->id)->getIndexedArray('situacao_veiculo_id','{situacao_veiculo->id}');
        return implode(', ', $values);
    }

    public function set_manutencao_veiculo_veiculos_to_string($manutencao_veiculo_veiculos_to_string)
    {
        if(is_array($manutencao_veiculo_veiculos_to_string))
        {
            $values = Veiculos::where('id', 'in', $manutencao_veiculo_veiculos_to_string)->getIndexedArray('id', 'id');
            $this->manutencao_veiculo_veiculos_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_veiculo_veiculos_to_string = $manutencao_veiculo_veiculos_to_string;
        }

        $this->vdata['manutencao_veiculo_veiculos_to_string'] = $this->manutencao_veiculo_veiculos_to_string;
    }

    public function get_manutencao_veiculo_veiculos_to_string()
    {
        if(!empty($this->manutencao_veiculo_veiculos_to_string))
        {
            return $this->manutencao_veiculo_veiculos_to_string;
        }
    
        $values = ManutencaoVeiculo::where('situacao_veiculo_id', '=', $this->id)->getIndexedArray('veiculos_id','{veiculos->id}');
        return implode(', ', $values);
    }

    public function set_manutencao_veiculo_oficina_to_string($manutencao_veiculo_oficina_to_string)
    {
        if(is_array($manutencao_veiculo_oficina_to_string))
        {
            $values = Pessoa::where('id', 'in', $manutencao_veiculo_oficina_to_string)->getIndexedArray('nome', 'nome');
            $this->manutencao_veiculo_oficina_to_string = implode(', ', $values);
        }
        else
        {
            $this->manutencao_veiculo_oficina_to_string = $manutencao_veiculo_oficina_to_string;
        }

        $this->vdata['manutencao_veiculo_oficina_to_string'] = $this->manutencao_veiculo_oficina_to_string;
    }

    public function get_manutencao_veiculo_oficina_to_string()
    {
        if(!empty($this->manutencao_veiculo_oficina_to_string))
        {
            return $this->manutencao_veiculo_oficina_to_string;
        }
    
        $values = ManutencaoVeiculo::where('situacao_veiculo_id', '=', $this->id)->getIndexedArray('oficina_id','{oficina->nome}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(ManutencaoVeiculo::where('situacao_veiculo_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

