<?php

class PedidoCompraSeguradoraDocument extends TPage
{
    private static $database = 'minierp';
    private static $activeRecord = 'Pedido';
    private static $primaryKey = 'id';
    private static $htmlFile = 'app/documents/PedidoCompraSeguradoraDocumentTemplate.html';

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
            $criteria_ItensPedido_pedido_venda_id->add(new TFilter('pedido_venda_id', '=', $param['key']));

            $objectsItensPedido_pedido_venda_id = ItensPedido::getObjects($criteria_ItensPedido_pedido_venda_id);
            $html->setDetail('ItensPedido.pedido_venda_id', $objectsItensPedido_pedido_venda_id);

            $pageSize = 'A4';
            $document = 'tmp/'.uniqid().'.pdf'; 

            $object->dt_pedido = TDate::date2br($object->dt_pedido);

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

