<?php

class PedidoFrotasHistoricoTimeLine extends TPage
{
    private static $database = 'minierp';
    private static $activeRecord = 'PedidoFrotasHistorico';
    private static $primaryKey = 'id';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null )
    {
        try
        {
            parent::__construct();

            TTransaction::open(self::$database);

            if(!empty($param['target_container']))
            {
                $this->adianti_target_container = $param['target_container'];
            }

            $this->timeline = new TTimeline;
            $this->timeline->setItemDatabase(self::$database);
            $this->timelineCriteria = new TCriteria;

            if(!empty($param['key']))
        {
            TSession::setValue(__CLASS__.'load_filter_pedido_frotas_id', $param['key']);
        }
        $filterVar = TSession::getValue(__CLASS__.'load_filter_pedido_frotas_id');
            $this->timelineCriteria->add(new TFilter('pedido_frotas_id', '=', $filterVar));

            $limit = 0;

            $this->timelineCriteria->setProperty('limit', $limit);
            $this->timelineCriteria->setProperty('order', 'id asc');


 $objects = PedidoFrotasHistorico::getObjects($this->timelineCriteria);

            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $object->estado_pedido_frotas->nome = call_user_func(function($value, $object, $row)
                    {

                        return "<span class='label label-default' style='width:300px; background-color:{$object->estado_pedido_frotas->cor}'> {$object->estado_pedido_frotas->nome} <span>";

                    }, $object->estado_pedido_frotas->nome, $object, null);

                    $id = $object->id;
                     $aprovadorfrotas = new AprovadorFrotas($object->aprovador_frotas_id);
                     $user = new SystemUsers($aprovadorfrotas->system_users_id);
                    //$aprovador = empty($user->name) ? $aprovador = ' ' ? $aprovador = $user->name;
                    if ($user){ 
                        $aprovador=$user->name;
                    } else $aprovador='';
                    $obs=$object->obs;
                    $title = "{estado_pedido_frotas->nome}";
                    $htmlTemplate = "<b>Aprovador</b> <br>
{$aprovador}<br>
<b>Observações</b> <br>
 {$obs}  ";
                    $date = date('d/m/Y H:i', strtotime($object->data_operacao));
                    $icon = 'fa:arrow-left bg-green';
                    $position = 'left';

                    if(empty($positionValue[$object->estado_pedido_frotas_id]))
                    {
                        $lastPosition = (empty($lastPosition) || $lastPosition == 'right') ? 'left' : 'right';
                        $bg = ($lastPosition == 'left') ? 'bg-green' : 'bg-blue';

                        $positionValue[$object->estado_pedido_frotas_id]['position'] = $lastPosition;
                        $positionValue[$object->estado_pedido_frotas_id]['bg'] = $bg;
                        $position = $positionValue[$object->estado_pedido_frotas_id]['position'];
                        $icon = "fa:arrow-{$lastPosition} {$bg}";
                    }
                    else
                    {
                        $position = $positionValue[$object->estado_pedido_frotas_id]['position'];
                        $lastPosition = $position;
                        $icon = "fa:arrow-{$lastPosition} {$positionValue[$object->estado_pedido_frotas_id]['bg']}";
                    }

                    //<onBeforeTimelineAddItem>

                    //</onBeforeTimelineAddItem>
                    $this->timeline->addItem($id, $title, $htmlTemplate, $date, $icon, $position, $object);
                    //<onAfterTimelineAddItem>

                    //</onAfterTimelineAddItem>
                }
            }

            $this->timeline->setUseBothSides();
            $this->timeline->setTimeDisplayMask('dd/mm/yyyy');
            $this->timeline->setFinalIcon( 'fas:flag-checkered #ffffff #de1414' );

            $container = new TVBox;

            $container->style = 'width: 100%';
            $container->class = 'form-container';
            if(empty($param['target_container']))
            {    
                $container->add(TBreadCrumb::create(["Manutenção Frotas","Linha do tempo de histórico de pedido de frotas"]));
            }
            $container->add($this->timeline);

            TTransaction::close();

            parent::add($container);
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {

    } 

}

