<?php

class PedidoFrotasKanbanView extends TPage
{
    private static $database = 'minierp';
    private static $activeRecord = 'Propostas';
    private static $primaryKey = 'id';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        try
        {
            parent::__construct();

            $kanban = new TKanban;
            $kanban->setItemDatabase(self::$database);

            $limit = 20;
            $kanban->setLoadMoreAction(new TAction([$this, 'onLoadMore'], $param), $limit);

            $criteriaStage = new TCriteria();
            $criteriaItem = new TCriteria();

            $criteriaStage->setProperty('order', 'ordem asc');
            $criteriaItem->setProperty('order', 'id desc');

            $filterVar = "T";
            $criteriaStage->add(new TFilter('kanban', '=', $filterVar)); 

            TSession::setValue(__CLASS__.'load_filter_estabelecimento_id', null);
            TSession::setValue(__CLASS__.'load_filter_system_users_id', null);
            TSession::setValue(__CLASS__.'load_filter_ano', null);
            TSession::setValue(__CLASS__.'load_filter_mes', null);

            if(!empty($param['estabelecimento_id']))
            {
                TSession::setValue(__CLASS__.'load_filter_estabelecimento_id', $param['estabelecimento_id']);
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_estabelecimento_id');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('pessoa_id', '=', $filterVar)); 
            }
            if(!empty($param['usuario_id']))
            {
                TSession::setValue(__CLASS__.'load_filter_system_users_id', $param['usuario_id']);
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_system_users_id');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('system_users_id', '=', $filterVar)); 
            }
            $criteriaItem->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit'))); 
            if(!empty($param['ano']))
            {
                TSession::setValue(__CLASS__.'load_filter_ano', $param['ano']);
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_ano');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('ano', '=', $filterVar)); 
            }
            if(!empty($param['mes']))
            {
                TSession::setValue(__CLASS__.'load_filter_mes', $param['mes']);
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_mes');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('mes', '=', $filterVar)); 
            }

            TTransaction::open(self::$database);
            $stages = EstadoPedidoFrotas::getObjects($criteriaStage);

            if($stages)
            {
                foreach ($stages as $key => $stage)
                {

                    $criteriaItemStage = clone $criteriaItem;
                    $criteriaItemStage->add(new TFilter('estado_pedido_frotas_id', '=', $stage->id));
                    $criteriaItemStage->setProperty('limit', $limit);

                    $kanban->addStage($stage->id, "{nome}", $stage ,$stage->cor);

                    $items = Propostas::getObjects($criteriaItemStage);

                    if($items)
                    {
                        foreach ($items as $key => $item)
                        {

                            $item->data_cotacao = call_user_func(function($value, $object, $row) 
                            {
                                if(!empty(trim($value)))
                                {
                                    try
                                    {
                                        $date = new DateTime($value);
                                        return $date->format('d/m/Y');
                                    }
                                    catch (Exception $e)
                                    {
                                        return $value;
                                    }
                                }
                            }, $item->data_cotacao, $item, null);

                            $item->valor_liquido = call_user_func(function($value, $object, $row) 
                            {
                                if(!$value)
                                {
                                    $value = 0;
                                }

                                if(is_numeric($value))
                                {
                                    return "R$ " . number_format($value, 2, ",", ".");
                                }
                                else
                                {
                                    return $value;
                                }
                            }, $item->valor_liquido, $item, null);

                            $kanban->addItem($item->pedido_frotas_id, $item->estado_pedido_frotas_id, "Pedido #{pedido_frotas_id}", "<b>Data:</b> {data_cotacao}<br>
<b>Estabelecimento:</b> {pessoa->nome}<br>
<b>Usuário:</b> {system_users->name} <br>
<b>Veículos:</b> {veiculos->placa} <br>
<b>Valor total:</b> R$ {valor_liquido}    ", $item->estado_pedido_frotas->cor, $item);

                        }    
                    }
                }    
            }

            $kanbanItemAction_PedidoFrotasFormView_onShow = new TAction(['PedidoFrotasFormView', 'onShow']);

            $kanban->addItemAction("Visualizar Pedido", $kanbanItemAction_PedidoFrotasFormView_onShow, 'fas:search-plus #000000');

            //$kanban->setTemplatePath('app/resources/card.html');

            TTransaction::close();

            $container = new TVBox;

            $container->style = 'width: 100%';
            $container->class = 'form-container';
            if(empty($param['target_container']))
            {
             //   TApplication::loadPage('PedidoFrotasKanbanFormView', 'onShow');
                $container->add(TBreadCrumb::create(["Manutenção Frotas","PedidoFrotasKanbanView"]));
              
            }
            
            $container->add($kanban);

            parent::add($container);
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onLoadMore($param)
    {
        try
        {
            TTransaction::open(self::$database);
            $criteriaItem = new TCriteria;

            TSession::setValue(__CLASS__.'load_filter_estabelecimento_id', null);
            TSession::setValue(__CLASS__.'load_filter_system_users_id', null);
            TSession::setValue(__CLASS__.'load_filter_ano', null);
            TSession::setValue(__CLASS__.'load_filter_mes', null);

            if(!empty($param['estabelecimento_id']))
            {
                TSession::setValue(__CLASS__.'load_filter_estabelecimento_id', $param['estabelecimento_id']);
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_estabelecimento_id');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('estabelecimento_id', '=', $filterVar)); 
            }
            if(!empty($param['usuario_id']))
            {
                TSession::setValue(__CLASS__.'load_filter_system_users_id', $param['usuario_id']);
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_system_users_id');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('system_users_id', '=', $filterVar)); 
            }
            if(!empty($param['ano']))
            {
                TSession::setValue(__CLASS__.'load_filter_ano', $param['ano']);
            }
             $criteriaItem->add(new TFilter('system_unit_id', '=', TSession::getValue('idunit'))); 
            $filterVar = TSession::getValue(__CLASS__.'load_filter_ano');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('ano', '=', $filterVar)); 
            }
            if(!empty($param['mes']))
            {
                TSession::setValue(__CLASS__.'load_filter_mes', $param['mes']);
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_mes');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('mes', '=', $filterVar)); 
            }

            $criteriaItem->add(new TFilter('estado_pedido_frotas_id', '=', $param['key'])); 
            $criteriaItem->setProperty('offset', $param['offset']);
            $criteriaItem->setProperty('limit', $param['limit']);
            $criteriaItem->setProperty('order', 'id desc');

            $items = Propostas::getObjects($criteriaItem);

            if ($items)
            {
                $actions = [];
                $kanbanItemAction_PedidoFrotasFormView_onShow = new TAction(['PedidoFrotasFormView', 'onShow']);

                $actions[] = ["Visualizar Pedido", $kanbanItemAction_PedidoFrotasFormView_onShow, 'fas:search-plus #000000'];

                foreach($items as $item)
                {

                    $item->dt_pedido = call_user_func(function($value, $object, $row) 
                    {
                        if(!empty(trim($value)))
                        {
                            try
                            {
                                $date = new DateTime($value);
                                return $date->format('d/m/Y');
                            }
                            catch (Exception $e)
                            {
                                return $value;
                            }
                        }
                    }, $item->dt_pedido, $item, null);

                    $item->valor_liquido_proposta = call_user_func(function($value, $object, $row) 
                    {
                        if(!$value)
                        {
                            $value = 0;
                        }

                        if(is_numeric($value))
                        {
                            return "R$ " . number_format($value, 2, ",", ".");
                        }
                        else
                        {
                            return $value;
                        }
                    }, $item->valor_total, $item, null);

                    TKanban::createItem($item->pedido_frotas_id, $item->estado_pedido_frotas_id, "Pedido #{pedido_frotas_id}", "<b>Data:</b> {dt_pedido}<br>
<b>Estabelecimento:</b> {pessoa->nome}<br>
<b>Usuário:</b> {system_users->name} <br>
<b>Veículos:</b> {veiculos->placa} <br>
<b>Valor total:</b> R$ {valor_total_cotacao}    ", $item->estado_pedido_frotas->cor, $item, null, $actions);

                }
            }

            TTransaction::close();
        }
        catch(Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {

    } 

}

