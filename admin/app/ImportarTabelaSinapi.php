<?php

class ImportarTabelaSinapi
{
    private $api;
    private $_instance;

    public function __construct()
    {
        $this->api = new TabelaSinapiService();
        $this->api->login();
    }



    public static function import()
    {
        ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
        $instance = new self();
        $pagina = 0;
        $uf='BA';
        $ano = $instance->api->getUltimoAno(['tipotabela' => 'sinapi', 'uf' => $uf]);
        $mes = $instance->api->getUltimoMes(['tipotabela' => 'sinapi', 'uf' => $uf, 'ano' => $ano]);
        
        echo  "importando ... $ano - $mes\n" ;
        $params = ['ano' => $ano, 'mes' => $mes, 'uf' => $uf, 'Composicao' => 'false', 'limit' => 1, 'page' => 1];
        $result = $instance->api->getInsumos($params);
         //var_dump($result->totalRows);
        $limit = 50;
        $total = $result->totalRows;
        $paginas = ceil($total / $limit);

        for ($i = $pagina; $i < $paginas; $i++) {
            $params['page'] = $i;
            $params['limit'] = $limit;
            //echo "pagina: $i \n";
            $result = $instance->api->getInsumos($params);
            $items = $result->items;
            //var_dump($items);
            try {
                TTransaction::open('minierp');

                foreach ($items as $item) {

                    $unit_id = $instance->insertOrUpdateUnit($item->unidade);

                    $family_id = $instance->insertOrUpdateFamily($item->classe);

                    $instance->insertProduct($item, $unit_id, $family_id);
                }

                TTransaction::close();
            } catch (Exception $e) {
                TTransaction::rollback();
                throw $e;
            }
        }
        echo "processados $total registros";
    }

    private function insertOrUpdateUnit($unit)
    {
        $repository = new TRepository('UnidadeMedida');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', '=', $unit->id));
        $units = $repository->load($criteria);

        if (empty($units)) {
            $unitObject = new UnidadeMedida;
            $unitObject->id = $unit->id;
            $unitObject->nome = $unit->nome;
            $unitObject->store();
        } else {
            $unitObject = $units[0];
        }

        return $unitObject->id;
    }

    private function insertOrUpdateFamily($family)
    {
        $repository = new TRepository('FamiliaProduto');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', '=', $family->id));
        $families = $repository->load($criteria);

        if (empty($families)) {
            $familyObject = new FamiliaProduto;
            $familyObject->id = $family->id;
            $familyObject->nome = $family->nome;
            $familyObject->store();
        } else {
            $familyObject = $families[0];
        }

        return $familyObject->id;
    }

    private function insertProduct($item, $unit_id, $family_id)
    {
        $repository = new TRepository('Produto');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('codigo_sinapi', '=', $item->codigo));
        $products = $repository->load($criteria);

        if (empty($products)) {
            $productObject = new Produto;
            $productObject->nome = $item->nome;
            $productObject->codigo_sinapi = $item->codigo;
            $productObject->preco_venda = $item->valorNaoOnerado;
            $productObject->unidade_medida_id = $unit_id;
            $productObject->familia_produto_id = $family_id;
            $productObject->ativo = 'T';
            $productObject->store();
        }
    }
}
