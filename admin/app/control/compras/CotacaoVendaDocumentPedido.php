<?php

class CotacaoVendaDocumentPedido extends TPage
{
    private static $database = 'minierp';
    private static $activeRecord = 'Pedido';
    private static $primaryKey = 'id';
    private static $htmlFile = 'app/documents/CotacaoVendaDocumentPedidoTemplate.html';
    
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {

    }

    public static function onGenerate($param)
    {
        try 
        {
            include 'app/control/compras/qrcode.php';
            
            TTransaction::open(self::$database);

            $class = "Cotacao";
           

            $pedido = new Pedido($param['key']);
            $object1 = Cotacao::where('pedido_id','=',$pedido->id) //$class($param['key']);
                             ->where('pessoa_id','=',$pedido->cliente_id)
                             ->load();
            if ($object1) {
                foreach ($object1 as $obj) {

                }
            }
             
            $object = new $class($obj->id); 
            
            $departamento = new DepartamentoUnit($pedido->departamento_unit_id);
            empty($departamento->name) ? $object->departamento = '' : $object->departamento = $departamento->name;
             
            $unidade = new SystemUnit($departamento->system_unit_id);
            empty($unidade->name) ? $object->unidade = '' : $object->unidade = $unidade->name;
            
            $historicopedido = PedidoHistorico::where('pedido_venda_id','=',$pedido->id)
                                             ->where('estado_pedido_venda_id','=',EstadoPedido::APROVADO)
                                             ->orderBy('data_operacao','desc')
                                             ->load();
            if ($historicopedido) {
                foreach($historicopedido as $histped) {
                   $user = new SystemUsers($histped->aprovador_id);
                   $object->usuarioaprovou = $user->name;                
                   break;              
                }
            } else {$object->usuarioaprovou = '';}
           
            $municipio = PessoaEndereco::where('pessoa_id','=',$pedido->cliente_id)  
                                       ->where('principal','=','T')
                                       ->load();
            if ($municipio){
                foreach($municipio as $mun){
                    $cid = new Cidade($mun->cidade_id);
                    $estado = new Estado($cid->estado_id);
                    $object->municipio = $cid->nome.'/'.$estado->sigla;                
                }
            } else {$object->municipio = '';}

            $pessoa = new Pessoa($pedido->cliente_id);
            if ($pessoa) {
                $cnpj=$pessoa->documento;
                   $object->cnpj = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj);               
                  // var_dump($object->cnpj);
                 //  die();
            } else {$object->cnpj = '';}
            
            $object->data_pedido = TDate::date2br($pedido->dt_pedido);
            
            if(isset($object->id)){
        $text = $object->id.".png";

     //   $name = md5(time()) . ".png";

        $file = "app/documents/{$text}";
        $options = array(
            'w' => 500,
            'h' => 500
        );

        $generator = new QRCode($object->id, $options);
        $image = $generator->render_image();
        imagepng($image, $file);
       

    };

           // $object->unidade  = 'teste';
            $html = new AdiantiHTMLDocumentParser(self::$htmlFile);
           // $object1 = new $class($param['key']);
            $html->setMaster($object);
            
  //          var_dump($object);
    //        die();

//            $pedido = new Pedido($object->id);

            $criteria_ItensPedido_pedido_venda_id = new TCriteria();
            $criteria_ItensPedido_pedido_venda_id->add(new TFilter('cotacao_id', '=', $object->id));

            
           // $objectsItensPedido_pedido_venda_id = ItensCotacao::getObjects($criteria_ItensPedido_pedido_venda_id);
            $objectsItensPedido_pedido_venda_id = ItensCotacao::where('cotacao_id','=',$object->id)
                                                              ->load();
            
            $count_records = count($objectsItensPedido_pedido_venda_id);
            
            $html->setDetail('ItensCotacao.cotacao_id', $objectsItensPedido_pedido_venda_id);

           
            $pageSize = 'A4';
            $document = 'tmp/'.uniqid().'.pdf'; 

            $object->data_cotacao = TDate::date2br($object->data_cotacao);

          /*  if($objectsPedidoVendaItem_pedido_venda_id)
            {
                foreach($objectsPedidoVendaItem_pedido_venda_id as $item)
                {
                    if(!$item->desconto)
                    {
                        $item->desconto = '0.00';
                    }
                }
            }*/

            $html->process();
            
            if ($count_records>=65 ) {
                $html->addPageBreak();
            }
            $html->saveAsPDF($document, $pageSize, 'portrait');

            imagedestroy($image);

            TTransaction::close();

            if(empty($param['returnFile']))
            {
                parent::openFile($document);

                new TMessage('info', _t('Document successfully generated'));    
            }
            else
            {
                return $document;
            }
        } 
        catch (Exception $e) 
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());

            // undo all pending operations
            TTransaction::rollback();
        }
    }

}

