<?php

class CotacaoVendaDocument extends TPage
{
    private static $database = 'minierp';
    private static $activeRecord = 'Cotacao';
    private static $primaryKey = 'id';
    private static $htmlFile = 'app/documents/CotacaoVendaDocumentTemplate.html';

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
            TTransaction::open(self::$database);

            $class = self::$activeRecord;
            $object = new $class($param['key']);
          
            $html = new AdiantiHTMLDocumentParser(self::$htmlFile);
            $html->setMaster($object);

            $criteria_ItensPedido_pedido_venda_id = new TCriteria();
            $criteria_ItensPedido_pedido_venda_id->add(new TFilter('cotacao_id', '=', $param['key']));

            
            $objectsItensPedido_pedido_venda_id = ItensCotacao::getObjects($criteria_ItensPedido_pedido_venda_id);
            
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

            $html->saveAsPDF($document, $pageSize, 'portrait');

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

