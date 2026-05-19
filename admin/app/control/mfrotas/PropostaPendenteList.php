<?php

use Adianti\Database\TCriteria;
use Adianti\Widget\Dialog\TMessage;

class PropostaPendenteList extends TPage
{
    
    use BuilderDatagridTrait;
private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Propostas';
    private static $primaryKey = 'id';
    private static $formName = 'form_PropostaPendenteList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

    private static function propostaTemBloqueioSuiv($propostaId)
    {
        return !empty(ItensPropostas::getDivergenciasSuivPorProposta($propostaId));
    }

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Consulta Propostas Pendentes ");
        
        $this->limit = 0;

        $criteria_pessoa_id = new TCriteria();

        $id = new TEntry('id');
        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa_id);
        $bhelper_681bcd3d66910 = new BHelper();


        $pessoa_id->enableSearch();
        $id->setSize(100);
        $pessoa_id->setSize('100%');
      
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "Id", 'left');
        $column_pedido_id = new TDataGridColumn('pedido_frotas_id', "Pedido id", 'left');
        $column_pessoa_nome = new TDataGridColumn('pessoa->nome', "Pessoa", 'left');
        $column_veiculos_id_transformed = new TDataGridColumn('veiculos_id', "Placa", 'left');
        $column_data_cotacao_transformed = new TDataGridColumn('data_cotacao', "Data Proposta", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_liquido', "Vl Total Proposta", 'left');
        $column_valor_total_itens_transformed = new TDataGridColumn('', "Vl Total Itens", 'left');
        $column_system_users_name = new TDataGridColumn('system_users->name', "Usuário", 'left');
        $column_bloqueio_suiv_transformed = new TDataGridColumn('id', "Bloqueio tempária", 'left');
        $column_estado_pedido_nome_transformed = new TDataGridColumn('estado_pedido_frotas->nome', "Estado da proposta", 'left');
        $column_cidade_id_transformed = new TDataGridColumn('cidade_id', "Cidade", 'left');
        $column_data_limite_resposta_transformed = new TDataGridColumn('data_limite_resposta', "Data Limite Resposta", 'left');
        // $column_estado_pedido_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        // {
        //     //code here
        //       //code here
        //     $temnotafiscal = false;
        //         TTransaction::open('minierp');

        //     if ($object->estado_pedido_frotas_id==EstadoPedidoFrotas::FINALIZADO || $object->estado_pedido_frotas_id==EstadoPedidoFrotas::APROVADO || $object->estado_pedido_frotas_id==EstadoPedidoFrotas::PGTOAPROVADO || $object->estado_pedido_frotas_id==EstadoPedidoFrotas::ENTREGUE) {
        //         $doccot = DocumentosPropostas::where('propostas_id','=',$object->id)
        //                                      ->load();
        //         if ($doccot){
        //            $temnotafiscal = true;
        //         }
        //     }

       
        //     $revisao = '';
        //     if (TSession::getValue('testar_revisao')==1) {            
        //         //entrou em revisão
        //         $revisao = '';
        //         if ($object->estado_pedido_frotas1_id !== null) {
        //             $estadorevisao = new EstadoPedidoFrotas($object->estado_pedido_frotas1_id);
        //             $revisao = "<span style='font-size: 10px; font-style: italic; color: #eee;'>({$estadorevisao->nome})</span>";
        //         }
        //     }
        //     TTransaction::close();

        //     if ($temnotafiscal) {
        //         $anexo = $object->estado_pedido_frotas->nome . " <i class='fa fa-paperclip' aria-hidden='true'></i>";
        //     } else {
        //         $anexo = $object->estado_pedido_frotas->nome;
        //     }

        //     return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido_frotas->cor}; display:inline-block;'> {$anexo} {$revisao} </span>";



        // });
        $column_estado_pedido_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $temnotafiscal = false;

            if ($object->estado_pedido_frotas_id == EstadoPedidoFrotas::FINALIZADO ||
                $object->estado_pedido_frotas_id == EstadoPedidoFrotas::APROVADO  ||
                $object->estado_pedido_frotas_id == EstadoPedidoFrotas::PGTOAPROVADO ||
                $object->estado_pedido_frotas_id == EstadoPedidoFrotas::ENTREGUE) {

                TTransaction::open('minierp');
                try {
                    $doccot = DocumentosPropostas::where('propostas_id','=',$object->id)->load();
                    if ($doccot) {
                        $temnotafiscal = true;
                    }
                } finally {
                    if (TTransaction::getDatabase()) {
                        TTransaction::close();
                    }
                }
            }

            $revisao = '';
            if (TSession::getValue('testar_revisao')==1) {            
                if ($object->estado_pedido_frotas1_id !== null) {
                    $estadorevisao = new EstadoPedidoFrotas($object->estado_pedido_frotas1_id);
                    $revisao       = "<span style='font-size: 10px; font-style: italic; color: #eee;'>({$estadorevisao->nome})</span>";
                }
            }

            if ($temnotafiscal) {
                $anexo = $object->estado_pedido_frotas->nome . " <i class='fa fa-paperclip' aria-hidden='true'></i>";
            } else {
                $anexo = $object->estado_pedido_frotas->nome;
            }

            return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido_frotas->cor}; display:inline-block;'> {$anexo} {$revisao} </span>";
        });

        $column_bloqueio_suiv_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (!ItensPropostas::isUtilizaTempariaAtivo() || !ItensPropostas::isBloqueioValorTempariaAtivo()) {
                return '';
            }

            $divergencias = ItensPropostas::getDivergenciasSuivPorProposta($object->id);

            if (empty($divergencias)) {
                return '';
            }

            $itensHtml = '';
            foreach ($divergencias as $divergencia) {
                $itensHtml .= '<div style="margin-top:6px;">- ' . htmlspecialchars($divergencia, ENT_QUOTES, 'UTF-8') . '</div>';
            }

            $tooltipHtml = htmlspecialchars(
                '<div style="max-width:460px; white-space:normal; line-height:1.45;">'
                . '<strong>Produtos bloqueados:</strong>'
                . $itensHtml
                . '</div>',
                ENT_QUOTES,
                'UTF-8'
            );

            return "<span class='label label-danger js-bloqueio-temparia' data-bloqueio-html='{$tooltipHtml}' style='display:inline-block; background-color:#d9534f; cursor:help;'>Bloqueado tempária</span>";
            // if (!ItensPropostas::isBloqueioValorTempariaAtivo()) {
            //     return '';
            // }

            // if ((int) $object->estado_pedido_frotas_id !== (int) EstadoPedidoFrotas::AGUARDANDO) {
            //     return '';
            // }

            // $divergencias = ItensPropostas::getDivergenciasSuivPorProposta($object->id);

            // if (empty($divergencias)) {
            //     return '';
            // }

            // $tooltip = htmlspecialchars(implode(' | ', $divergencias), ENT_QUOTES, 'UTF-8');

            // return "<span class='label label-danger' title='{$tooltip}' style='display:inline-block; background-color:#d9534f;'>Bloqueado tempária</span>";
        });

        // $column_veiculos_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        // {
        //     //code here
        //     TTransaction::open('minierp');

        //         $ped = new PedidoFrotas($object->pedido_frotas_id);
        //         $veiculos = new Veiculos($ped->veiculos_id);
        //         if ($veiculos) {
        //             $marca  = new Marca($veiculos->marca_id);
        //             $modelo = new Modelo($veiculos->modelo_id);
        //                             TTransaction::close();

        //             return "Placa: {$veiculos->placa} - Marca: {$marca->descricao} - Modelo: {$modelo->descricao}";

        //         } else {
        //                             TTransaction::close();

        //             return "";

        //         }

        // });
        $column_veiculos_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open('minierp');
            try {
                $ped      = new PedidoFrotas($object->pedido_frotas_id);
                $veiculos = new Veiculos($ped->veiculos_id);

                if ($veiculos) {
                    $marca  = new Marca($veiculos->marca_id);
                    $modelo = new Modelo($veiculos->modelo_id);
                    return "Placa: {$veiculos->placa} - Marca: {$marca->descricao} - Modelo: {$modelo->descricao}";
                } else {
                    return "";
                }
            } finally {
                if (TTransaction::getDatabase()) {
                    TTransaction::close();
                }
            }
        });

        $column_data_limite_resposta_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        });
        $column_data_cotacao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        }); 

        // $column_valor_total_itens_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        // {
        //      TTransaction::open('minierp');
            
           
        //      $value=0;    
        //      if (TSession::getValue('aprovacao_por_item')==1) {
        //         $objects = ItensPropostas::where('propostas_id','=',$object->id)
        //                               ->where('estado_pedido_frotas_id','=',EstadoPedidoFrotas::APROVADO)
        //                                 ->load();
            
        //     } else {
        //         $objects = ItensPropostas::where('propostas_id','=',$object->id)
        //                                 ->load();
        //     }
        //     if ($objects) {
        //         foreach ($objects as $obj) {
        //             $value = $value + ($obj->valor * $obj->qtde) - $obj->perc_desconto;
        //         }
        //     }
        //     TTransaction::close();

        //      if(is_numeric($value))
        //     {
        //         return "R$ " . number_format($value, 2, ",", ".");
        //     }
        //     else
        //     {
        //         return $value;
        //     }

        // });

        $column_valor_total_itens_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open('minierp');
            try {
                $value = 0;    
                if (TSession::getValue('aprovacao_por_item')==1) {
                    $objects = ItensPropostas::where('propostas_id','=',$object->id)
                                            ->where('estado_pedido_frotas_id','=',EstadoPedidoFrotas::APROVADO)
                                            ->load();
                } else {
                    $objects = ItensPropostas::where('propostas_id','=',$object->id)->load();
                }

                if ($objects) {
                    foreach ($objects as $obj) {
                        $value = $value + ($obj->valor * $obj->qtde) - $obj->perc_desconto;
                    }
                }

                if (is_numeric($value)) {
                    return "R$ " . number_format($value, 2, ",", ".");
                } else {
                    return $value;
                }
            } finally {
                if (TTransaction::getDatabase()) {
                    TTransaction::close();
                }
            }
        });

        $column_valor_total_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        });

    

        // $column_cidade_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        // {
        //     //code here

        //         TTransaction::open('minierp');

        //         $cidade = new Cidade($object->cidade_id);
        //         if ($cidade) {
        //             $estado = new Estado($cidade->estado_id);
        //             $cidadexx =  "{$cidade->nome} - {$estado->sigla}";
        //                         TTransaction::close();

        //             return $cidadexx;

        //         } else {
        //                         TTransaction::close();

        //             return "Não informado!!!";

        //         }


        // });        

        $column_cidade_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open('minierp');
            try {
                $cidade = new Cidade($object->cidade_id);
                if ($cidade) {
                    $estado = new Estado($cidade->estado_id);
                    return "{$cidade->nome} - {$estado->sigla}";
                } else {
                    return "Não informado!!!";
                }
            } finally {
                if (TTransaction::getDatabase()) {
                    TTransaction::close();
                }
            }
        });

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_pedido_id);
        $this->datagrid->addColumn($column_pessoa_nome);
        $this->datagrid->addColumn($column_veiculos_id_transformed);
        
        $this->datagrid->addColumn($column_data_cotacao_transformed);
        $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column_valor_total_itens_transformed);
        $this->datagrid->addColumn($column_system_users_name);
        $this->datagrid->addColumn($column_bloqueio_suiv_transformed);
        $this->datagrid->addColumn($column_estado_pedido_nome_transformed);
        $this->datagrid->addColumn($column_cidade_id_transformed);
        $this->datagrid->addColumn($column_data_limite_resposta_transformed);

        if (TSession::getValue('aprovacao_por_item')==2) {
            $action1 = new TDataGridAction(['TStatusPedido', 'onShowModal'], [
                'id'       => '{id}',
                'tipoacao' => 'PreAprovar' // sem chaves se for valor fixo
            ]);     
             $action2 = new TDataGridAction(['TStatusPedido', 'onShowModal'], [
                'id'       => '{id}',
                'tipoacao' => 'Aprovar' // sem chaves se for valor fixo
            ]);     
             $action3 = new TDataGridAction(['TStatusPedido', 'onShowModal'], [
                'id'       => '{id}',
                'tipoacao' => 'Reprovar' // sem chaves se for valor fixo
            ]);            
        } else {

               $action1 = new TDataGridAction(['ItensPropostasSimpleList', 'onEdit'], [
                'id'       => '{id}',
                'tipoacao' => 'PreAprovar' // sem chaves se for valor fixo
            ]);     
             $action2 = new TDataGridAction(['ItensPropostasSimpleList', 'onEdit'], [
                'id'       => '{id}',
                'tipoacao' => 'Aprovar' // sem chaves se for valor fixo
            ]);     
             $action3 = new TDataGridAction(['ItensPropostasSimpleList', 'onEdit'], [
                'id'       => '{id}',
                'tipoacao' => 'Reprovar' // sem chaves se for valor fixo
            ]);      
            


        }
         
     //   $action5 = new TDataGridAction(['PedidoFrotasFormView', 'onShow'],     ['id' => '{id}']); // EDITAR (APROVAR; PRE-APROVAR; E REPROVAR)
    //    $action6 = new TDataGridAction(['PedidoFrotasFormView', 'onShow'],     ['id' => '{id}']); // GERAR FINANCEIRO
            $action1->setLabel('Pré-Aprovar');
            $action1->setImage('far:thumbs-up #9C27B0');
            $action1->setDisplayCondition('PropostaPendenteList::onExibirPreAprovada');
            $action1->setParameter('pedido_frotas_id', '{pedido_frotas_id}');


            $action2->setLabel('Aprovar');
            $action2->setImage('fas:thumbs-up #9C27B0');
            $action2->setDisplayCondition('PropostaPendenteList::onExibirAprovada');
            $action2->setParameter('pedido_frotas_id', '{pedido_frotas_id}');

            $action3->setLabel('Reprovar');
            $action3->setImage('fas:thumbs-down #F44336');
            $action3->setDisplayCondition('PropostaPendenteList::onExibirReprovada');
            $action3->setParameter('pedido_frotas_id', '{pedido_frotas_id}');



        $action4 = new TDataGridAction(['PropostaPendenteList', 'onImprimir'],     ['id' => '{id}']);
        $action4->setLabel('Orçamento');
        $action4->setImage('far:file-pdf #000000');
        $action4->setParameter('pedido_frotas_id', '{pedido_frotas_id}');


        $actionDel = new TDataGridAction([__CLASS__, 'onExcluir'], ['key' => '{id}']);
        $actionDel->setLabel('Excluir');
        $actionDel->setImage('far:trash-alt #F44336');

        // opcional: condicionar exibição (ex: só se AGUARDANDO e usuário tem permissão)
        $actionDel->setDisplayCondition([__CLASS__, 'onExibirExcluir']);


        $action_group = new TDataGridActionGroup('Clique Ações ', 'fa:th red');

        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action3);
        $action_group->addAction($action4);
        $action_group->addAction($actionDel);
                

        $this->datagrid->addActionGroup($action_group);

        // create the datagrid model
        $this->applyDatagridProperties();

        $this->datagrid->createModel();

       
//
        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

  

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $panel->getBody()->insert(0, $headerActions);

    

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
      
        }
        $container->add($panel);

        parent::add($container);

    }
    
   
    // public static function onExibirPreAprovada($object)
    // {
    //     try 
    //     {
    //          if (TSession::getValue('aprovacao_por_item')==1) {
    //             $estado  = $object->estado_pedido_frotas_id;
    //             $estado1 = $object->estado_pedido_frotas1_id;
    //             $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

    //             if (TSession::getValue('testar_revisao')==1) {                
    //                 // Impede exibição se estiver em revisão
    //                 if ($estado1 == EstadoPedidoFrotas::REVISAO) {
    //                     return false;
    //                 }
    //             }
    //             // Exibe o botão apenas se:
    //             // - O estado atual for AGUARDANDO
    //             // - E o usuário tiver permissão para PREAPROVAR
    //             if ($estado == EstadoPedidoFrotas::AGUARDANDO &&
    //                 in_array(EstadoPedidoFrotas::PREAPROVADO, $estadosPermitidos))
    //             {
    //                 return true;
    //             }
    //             return true;

    //        } else {

    //         TTransaction::open('minierp');

    //         if (!empty($pedido_frotas->data_aprovacao)) {
    //             return false;
    //         }
    //         // Vai exibir o pré-aprovado se não tiver nenhum pedido de frotas pré-aprovado ou aprovado
    //         $pedido_frotas_id = $object->pedido_frotas_id;

    //         $objects = Propostas::where('pedido_frotas_id', '=', $pedido_frotas_id)
    //                             ->where('estado_pedido_frotas_id', 'in', [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PREAPROVADO])
    //                             ->load();

    //         if (count($objects) > 0) {
    //             // Existe um pedido com estado diferente de APROVADO ou PREAPROVADO com ID diferente
    //             TTransaction::close();
    //             return false;
    //         }
    //         TTransaction::close();
    //         $estado  = $object->estado_pedido_frotas_id;
    //         $estado1 = $object->estado_pedido_frotas1_id;
    //         $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

    //         if (TSession::getValue('testar_revisao')==1) {                
    //                 // Impede exibição se estiver em revisão
    //                 if ($estado1 == EstadoPedidoFrotas::REVISAO) {
    //                     return false;
    //                 }
    //             }
    //         // Exibe botão apenas se o estado atual for AGUARDANDO ou PREAPROVADO
    //         // E se o usuário puder aprovar (tem o estado APROVADO nos permitidos)
    //             if (
    //                 in_array($estado, [EstadoPedidoFrotas::AGUARDANDO]) &&
    //                 in_array(EstadoPedidoFrotas::AGUARDANDO, $estadosPermitidos) &&
    //                 in_array(EstadoPedidoFrotas::PREAPROVADO, $estadosPermitidos)
    //             ) {
    //                 return true;
    //             }              
    //        }
    //         return false;
    //     }
    //     catch (Exception $e) 
    //     {
    //         new TMessage('error', $e->getMessage());    
    //     }
    // }
    public static function onExibirPreAprovada($object)
    {
        try 
        {
            if (TSession::getValue('aprovacao_por_item')==1) {
                if (self::propostaTemBloqueioSuiv($object->id)) {
                    return false;
                }

                $estado            = $object->estado_pedido_frotas_id;
                $estado1           = $object->estado_pedido_frotas1_id;
                $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

                if (TSession::getValue('testar_revisao')==1) {                
                    if ($estado1 == EstadoPedidoFrotas::REVISAO) {
                        return false;
                    }
                }

                if ($estado == EstadoPedidoFrotas::AGUARDANDO &&
                    in_array(EstadoPedidoFrotas::PREAPROVADO, $estadosPermitidos))
                {
                    return true;
                }
                return false;

            } else {
                if (self::propostaTemBloqueioSuiv($object->id)) {
                    return false;
                }

                TTransaction::open('minierp');
                try {
                    // ATENÇÃO: aqui está o código original, só envolvendo com try/finally
                    if (!empty($pedido_frotas->data_aprovacao)) {
                        return false;
                    }

                    $pedido_frotas_id = $object->pedido_frotas_id;

                    $objects = Propostas::where('pedido_frotas_id', '=', $pedido_frotas_id)
                                        ->where('estado_pedido_frotas_id', 'in', [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PREAPROVADO])
                                        ->load();

                    if (count($objects) > 0) {
                        return false;
                    }
                } finally {
                    if (TTransaction::getDatabase()) {
                        TTransaction::close();
                    }
                }

                $estado            = $object->estado_pedido_frotas_id;
                $estado1           = $object->estado_pedido_frotas1_id;
                $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

                if (TSession::getValue('testar_revisao')==1) {                
                    if ($estado1 == EstadoPedidoFrotas::REVISAO) {
                        return false;
                    }
                }

                if (
                    in_array($estado, [EstadoPedidoFrotas::AGUARDANDO]) &&
                    in_array(EstadoPedidoFrotas::AGUARDANDO, $estadosPermitidos) &&
                    in_array(EstadoPedidoFrotas::PREAPROVADO, $estadosPermitidos)
                ) {
                    return true;
                }              
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }


    // public static function onExibirAprovada($object)
    // {
    //     try 
    //     {
    //          if (TSession::getValue('aprovacao_por_item')==1) {
    //         $estado  = $object->estado_pedido_frotas_id;
    //         $estado1 = $object->estado_pedido_frotas1_id;
    //         $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

    //         if (TSession::getValue('testar_revisao')==1) {                
    //                 // Impede exibição se estiver em revisão
    //                 if ($estado1 == EstadoPedidoFrotas::REVISAO) {
    //                     return false;
    //                 }
    //             }
    //         // Exibe botão apenas se o estado atual for AGUARDANDO ou PREAPROVADO
    //         // E se o usuário puder aprovar (tem o estado APROVADO nos permitidos)
    //         if (in_array($estado, [EstadoPedidoFrotas::AGUARDANDO, EstadoPedidoFrotas::PREAPROVADO]) &&
    //             in_array(EstadoPedidoFrotas::APROVADO, $estadosPermitidos))
    //         {
    //             return true;
    //         }
    //         } else {
    //             TTransaction::open('minierp');

    //             // Vai exibir o pré-aprovado se não tiver nenhum pedido de frotas pré-aprovado ou aprovado
    //             $pedido_frotas_id = $object->pedido_frotas_id;

    //             $objects = Propostas::where('pedido_frotas_id', '=', $pedido_frotas_id)
    //                                 ->where('estado_pedido_frotas_id', 'in', [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PREAPROVADO])
    //                                 ->load();

    //             if (count($objects) > 0 && $objects[0]->id != $object->id) {
    //                 // Existe um pedido com estado diferente de APROVADO ou PREAPROVADO com ID diferente
    //                 TTransaction::close();
    //                 return false;
    //             }
    //             TTransaction::close();
    //               $estado  = $object->estado_pedido_frotas_id;
    //             $estado1 = $object->estado_pedido_frotas1_id;
    //             $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

    //             if (TSession::getValue('testar_revisao')==1) {                
    //                 // Impede exibição se estiver em revisão
    //                 if ($estado1 == EstadoPedidoFrotas::REVISAO) {
    //                     return false;
    //                 }
    //             }                // Exibe botão apenas se o estado atual for AGUARDANDO ou PREAPROVADO
    //             // E se o usuário puder aprovar (tem o estado APROVADO nos permitidos)
    //             if (in_array($estado, [EstadoPedidoFrotas::AGUARDANDO, EstadoPedidoFrotas::PREAPROVADO]) &&
    //                 in_array(EstadoPedidoFrotas::APROVADO, $estadosPermitidos))
    //             {
    //                 return true;
    //             }                
    //         }
    //         return false;
    //     }
    //     catch (Exception $e) 
    //     {
    //         new TMessage('error', $e->getMessage());    
    //     }
    // }

    public static function onExibirAprovada($object)
    {
        try 
        {
            if (TSession::getValue('aprovacao_por_item')==1) {
                if (self::propostaTemBloqueioSuiv($object->id)) {
                    return false;
                }

                $estado            = $object->estado_pedido_frotas_id;
                $estado1           = $object->estado_pedido_frotas1_id;
                $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

                if (TSession::getValue('testar_revisao')==1) {                
                    if ($estado1 == EstadoPedidoFrotas::REVISAO) {
                        return false;
                    }
                }

                if (in_array($estado, [EstadoPedidoFrotas::AGUARDANDO, EstadoPedidoFrotas::PREAPROVADO]) &&
                    in_array(EstadoPedidoFrotas::APROVADO, $estadosPermitidos))
                {
                    return true;
                }
            } else {
                if (self::propostaTemBloqueioSuiv($object->id)) {
                    return false;
                }

                TTransaction::open('minierp');
                try {
                    $pedido_frotas_id = $object->pedido_frotas_id;

                    $objects = Propostas::where('pedido_frotas_id', '=', $pedido_frotas_id)
                                        ->where('estado_pedido_frotas_id', 'in', [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PREAPROVADO])
                                        ->load();

                    if (count($objects) > 0 && $objects[0]->id != $object->id) {
                        return false;
                    }
                } finally {
                    if (TTransaction::getDatabase()) {
                        TTransaction::close();
                    }
                }

                $estado            = $object->estado_pedido_frotas_id;
                $estado1           = $object->estado_pedido_frotas1_id;
                $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

                if (TSession::getValue('testar_revisao')==1) {                
                    if ($estado1 == EstadoPedidoFrotas::REVISAO) {
                        return false;
                    }
                }

                if (in_array($estado, [EstadoPedidoFrotas::AGUARDANDO, EstadoPedidoFrotas::PREAPROVADO]) &&
                    in_array(EstadoPedidoFrotas::APROVADO, $estadosPermitidos))
                {
                    return true;
                }                
            }
            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    

    public static function onExibirReprovada($object)
    {
        try 
        {
            $estado  = $object->estado_pedido_frotas_id;
            $estado1 = $object->estado_pedido_frotas1_id;
            $estadosPermitidos = AprovadorFrotas::getEstadosDisponiveis();

            if (TSession::getValue('testar_revisao')==1) {                
                    // Impede exibição se estiver em revisão
                    if ($estado1 == EstadoPedidoFrotas::REVISAO) {
                        return false;
                    }
                }            // Impede exibição se estiver em revisão

            // Exibe o botão apenas se:
            // - O estado atual for AGUARDANDO ou PREAPROVADO
            // - E o usuário tiver permissão para REPROVAR
            if (in_array($estado, [EstadoPedidoFrotas::AGUARDANDO, EstadoPedidoFrotas::PREAPROVADO]) &&
                in_array(EstadoPedidoFrotas::REPROVADO, $estadosPermitidos))
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onExibirExcluir($object)
    {
        try {
            $bloqueados = [
                (int) EstadoPedidoFrotas::APROVADO,
                (int) EstadoPedidoFrotas::ENTREGUE,
                (int) EstadoPedidoFrotas::PGTOAPROVADO,
                (int) EstadoPedidoFrotas::FINALIZADO,
                (int) EstadoPedidoFrotas::PREAPROVADO,
                (int) EstadoPedidoFrotas::CANCELADO,
                (int) EstadoPedidoFrotas::REPROVADO,
            ];

            return !in_array((int) $object->estado_pedido_frotas_id, $bloqueados, true);
        } catch (Exception $e) {
            return false;
        }
    }


    public function onExcluir($param = null)
    {
        if (isset($param['delete']) && (int)$param['delete'] === 1) {

            try {
                $key = (int) ($param['key'] ?? 0);
                if (!$key) {
                    throw new Exception('ID da proposta não informado.');
                }

                TTransaction::open(self::$database);

                // instantiates object (Propostas)
                $object = new Propostas($key, FALSE);
                // $pedido_frotas_id = $param['pedido_frotas_id'];

                $pedido_frotas = new PedidoFrotas($object->pedido_frotas_id);

                $pedidoSessao = (int) TSession::getValue('pedido_frotas_id');
                if ((int)$object->pedido_frotas_id !== $pedidoSessao) {
                    throw new Exception('Essa proposta não pertence ao pedido atual.');
                }


                                // regra de estados bloqueados para exclusao
                $bloqueados = [
                    (int) EstadoPedidoFrotas::APROVADO,
                    (int) EstadoPedidoFrotas::ENTREGUE,
                    (int) EstadoPedidoFrotas::PGTOAPROVADO,
                    (int) EstadoPedidoFrotas::FINALIZADO,
                    (int) EstadoPedidoFrotas::PREAPROVADO,
                    (int) EstadoPedidoFrotas::CANCELADO,
                    (int) EstadoPedidoFrotas::REPROVADO,
                ];

                if (in_array((int) $pedido_frotas->estado_pedido_frotas_id, $bloqueados, true)) {
                    throw new Exception("NÃ£o Ã© possÃ­vel excluir a proposta para este estado do pedido.");
                }
/**
                 * EXCLUI FILHOS (se tiver FK)
                 * (se o seu Adianti suportar ->delete() direto na where, pode usar.
                 *  senão, faz load + foreach igual você fez)
                 */
                // ItensPropostas::where('propostas_id', '=', $key)->delete();
                // DocumentosPropostas::where('propostas_id', '=', $key)->delete();
                // PropostasHistorico::where('propostas_id', '=', $key)->delete();

                // Itens da proposta
                $itens = ItensPropostas::where('propostas_id', '=', $key)->load();
                if ($itens) {
                    foreach ($itens as $item) {
                        $item->delete();
                    }
                }

                // Documentos/anexos da proposta
                $docs = DocumentosPropostas::where('propostas_id', '=', $key)->load();
                if ($docs) {
                    foreach ($docs as $doc) {
                        $doc->delete();
                    }
                }

                // Histórico da proposta
                $hist = PropostasHistorico::where('propostas_id', '=', $key)->load();
                if ($hist) {
                    foreach ($hist as $h) {
                        $h->delete();
                    }
                }

                // por fim, exclui a proposta
                $object = Propostas::where('id','=',$key)->first();
                $xped = $object->pedido_frotas_id;
                $xpess = $object->pessoa_id;
                if ($object){
                   $object->delete();
                }

                $pedascli = PedidoAsCliente::where('pedido_frotas_id','=',$xped)
                                           ->where('pessoa_id','=',$xpess)
                                           ->load();
                if ($pedascli) {
                    foreach ($pedascli as $ped) {
                        $ped->delete();
                    }
                }

                TTransaction::close();

                $param['pedido_frotas_id'] = TSession::getValue('pedido_frotas_id');

                $this->onReload($param);
                new TMessage('info', 'Proposta excluída com sucesso!');
            }
            catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }

        } else {
            $action = new TAction([$this, 'onExcluir']);
            $action->setParameters($param);
            $action->setParameter('delete', 1);

            new TQuestion('Deseja realmente excluir esta proposta?', $action);
        }
    }



    public function onExportCsv($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    $handler = fopen($output, 'w');
                    TTransaction::open(self::$database);

                    foreach ($objects as $object)
                    {
                        $row = [];
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();

                            if (isset($object->$column_name))
                            {
                                $row[] = is_scalar($object->$column_name) ? $object->$column_name : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $row[] = $object->render($column_name);
                            }
                        }

                        fputcsv($handler, $row);
                    }

                    fclose($handler);
                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXls($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xls';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $widths = [];
                $titles = [];

                foreach ($this->datagrid->getColumns() as $column)
                {
                    $titles[] = $column->getLabel();
                    $width    = 100;

                    if (is_null($column->getWidth()))
                    {
                        $width = 100;
                    }
                    else if (strpos((string)$column->getWidth(), '%') !== false)
                    {
                        $width = ((int) $column->getWidth()) * 5;
                    }
                    else if (is_numeric($column->getWidth()))
                    {
                        $width = $column->getWidth();
                    }

                    $widths[] = $width;
                }

                $table = new \TTableWriterXLS($widths);
                $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                $table->addStyle('data',   'Helvetica', '10', '',  '#000000', '#FFFFFF', 'LR');

                $table->addRow();

                foreach ($titles as $title)
                {
                    $table->addCell($title, 'center', 'title');
                }

                $this->limit = 0;
                $objects = $this->onReload();

                TTransaction::open(self::$database);
                if ($objects)
                {
                    foreach ($objects as $object)
                    {
                        $table->addRow();
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $value = '';
                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                            }

                            $transformer = $column->getTransformer();
                            if ($transformer)
                            {
                                $value = strip_tags((string)call_user_func($transformer, $value, $object, null));
                            }

                            $table->addCell($value, 'center', 'data');
                        }
                    }
                }
                $table->save($output);
                TTransaction::close();

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportPdf($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.pdf';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->onReload();

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . file_get_contents('app/resources/styles-print-list.html') . PdfListHeader::render(__CLASS__) . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('iframe');
                $object->src  = $output;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($object);
                $window->show();
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXml($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xml';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    TTransaction::open(self::$database);

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->{'formatOutput'} = true;
                    $dataset = $dom->appendChild( $dom->createElement('dataset') );

                    foreach ($objects as $object)
                    {
                        $row = $dataset->appendChild( $dom->createElement( self::$activeRecord ) );

                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $column_name_raw = str_replace(['(','{','->', '-','>','}',')', ' '], ['','','_','','','','','_'], $column_name);

                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                                $row->appendChild($dom->createElement($column_name_raw, $value)); 
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                                $row->appendChild($dom->createElement($column_name_raw, $value));
                            }
                        }
                    }

                    $dom->save($output);

                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->pessoa_id) AND ( (is_scalar($data->pessoa_id) AND $data->pessoa_id !== '') OR (is_array($data->pessoa_id) AND (!empty($data->pessoa_id)) )) )
        {

            $filters[] = new TFilter('pessoa_id', '=', $data->pessoa_id);// create the filter 
        }

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'minierp'
            TTransaction::open('minierp');

            // creates a repository for Cotacao
            $repository = new TRepository('Propostas');

            $criteria = new TCriteria();

            if (empty($param['order']))
            {
                $param['order'] = 'valor_liquido';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'asc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

               
                $criteria->add(new TFilter('pedido_frotas_id','=',TSession::getValue('pedido_frotas_id')));
            

                // Recupera valores da sessão
            $userid = TSession::getValue('userid');
            $idunit = TSession::getValue('idunit');

            // Monta a subquery para verificar se a pessoa está no grupo CONDUTOR
            $subquery = '(SELECT pessoa_id FROM pessoa_grupo pg WHERE pg.grupo_pessoa_id = ' . GrupoPessoa::CONDUTOR . ')';

            // Busca pessoas ligadas ao usuário logado e que não sejam do grupo CONDUTOR
            $pes1 = Pessoa::where('system_user_id', '=', $userid)
                        ->where('id', 'NOT IN', $subquery)
                        ->load();

            // Cria os critérios
         //   $criteria = new TCriteria();

            if ($pes1) {
                // Extrai os IDs das pessoas encontradas
                  $ids = array_map(fn($p) => $p->id, $pes1);

                // Aplica filtro com IN
                $criteria->add(new TFilter('pessoa_id', 'IN', $ids));

            }
           // $criteria->add(new TFilter('pedido_frotas_id', '=', TSession::getValue('idpedidofrotascp'))); 

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

         //   $this->pageNavigation->setCount($count); // count of records
       //     $this->pageNavigation->setProperties($param); // order, page
         //   $this->pageNavigation->setLimit($this->limit); // limit

            // close the transaction
            TTransaction::close();

            TScript::create("
                (function() {
                    if (!window.__bloqueioTempariaTooltip) {
                        var tooltip = document.createElement('div');
                        tooltip.id = 'bloqueio-temparia-tooltip';
                        tooltip.style.position = 'fixed';
                        tooltip.style.zIndex = '99999';
                        tooltip.style.display = 'none';
                        tooltip.style.background = '#fff';
                        tooltip.style.color = '#333';
                        tooltip.style.border = '1px solid rgba(0,0,0,0.15)';
                        tooltip.style.borderRadius = '6px';
                        tooltip.style.boxShadow = '0 8px 24px rgba(0,0,0,0.18)';
                        tooltip.style.padding = '10px 12px';
                        tooltip.style.fontSize = '12px';
                        tooltip.style.pointerEvents = 'none';
                        document.body.appendChild(tooltip);
                        window.__bloqueioTempariaTooltip = tooltip;
                    }

                    var tooltip = window.__bloqueioTempariaTooltip;

                    function positionTooltip(event) {
                        tooltip.style.left = Math.min(event.clientX + 14, window.innerWidth - tooltip.offsetWidth - 12) + 'px';
                        tooltip.style.top = Math.min(event.clientY + 14, window.innerHeight - tooltip.offsetHeight - 12) + 'px';
                    }

                    document.querySelectorAll('.js-bloqueio-temparia').forEach(function(el) {
                        if (el.dataset.tooltipBound === '1') {
                            return;
                        }

                        el.dataset.tooltipBound = '1';

                        el.addEventListener('mouseenter', function(event) {
                            tooltip.innerHTML = this.getAttribute('data-bloqueio-html') || '';
                            tooltip.style.display = 'block';
                            positionTooltip(event);
                        });

                        el.addEventListener('mousemove', function(event) {
                            if (tooltip.style.display === 'block') {
                                positionTooltip(event);
                            }
                        });

                        el.addEventListener('mouseleave', function() {
                            tooltip.style.display = 'none';
                        });
                    });
                })();
            ");

            $this->loaded = true;

            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {
       $this->onAtualizar();
    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public static function manageRow($id)
    {
        $list = new self([]);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Propostas($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    function onSetProject($param = null) {
        TSession::setValue('idpedidofrotascp',NULL);
        TSession::setValue('idpedidofrotascp',$param['id']);  
        $this->onReload();
    }
    private function registrarHistoricoPedido($pedido)
    {
        $hist = new PedidoFrotasHistorico();
        $hist->pedido_frotas_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d H:i:s');
        $hist->estado_pedido_venda_id = EstadoPedidoFrotas::APROVADO; 
        $hist->aprovador_id = TSession::getValue('iduser');
        $hist->store();
    }

    private function registrarHistoricoCotacao($cotacao)
    {
        $histcotacao = new PropostasHistorico();
        $histcotacao->propostas_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d H:i:s');
        $histcotacao->estado_pedido_frotas_id = EstadoPedido::APROVADO; 
        $histcotacao->aprovador_frotas_id = TSession::getValue('iduser');
        $histcotacao->store();
    }
     private function registrarHistoricoPedidoReprovar($pedido)
    {
        $hist = new PedidoFrotasHistorico();
        $hist->pedido_venda_id = $pedido->id;
        $hist->data_operacao = date('Y-m-d H:i:s');
        $hist->estado_pedido_frotas_id = EstadoPedido::REPROVADO; 
        $hist->aprovador_frotas_id = TSession::getValue('iduser');
        $hist->store();
    }

    private function registrarHistoricoCotacaoReprovar($cotacao)
    {
        $histcotacao = new PropostasHistorico();
        $histcotacao->propostas_id = $cotacao->id;
        $histcotacao->data_historico = date('Y-m-d H:i:s');
        $histcotacao->estado_pedido_frotas_id = EstadoPedido::REPROVADO; 
        $histcotacao->aprovador_id = TSession::getValue('iduser');
        $histcotacao->store();
    }


   
     public function onImprimir($param = null) 
    {
        try 
        {
            include_once 'app/control/mfrotas/PedidoFrotasOrcamento.php';

            $orcamento = new PropostaOrcamento;
            $orcamento->gerar($param);

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }


 
    
    private function registrarHistoricoCotacaoAguardando($propostas)
    {
        $histpropostas = new PropostasHistorico();
        $histpropostas->propostas_id = $propostas->id;
        $histpropostas->data_historico = date('Y-m-d H:i:s');
        $histpropostas->estado_pedido_frotas_id = EstadoPedidoFrotas::AGUARDANDO; 
        $histpropostas->aprovador_frotas_id = TSession::getValue('iduser');
        $histpropostas->store();
    }
    public static function onAtualizar($param = [])
    {
        try {
            TTransaction::open('minierp');

            $idunit = (int) TSession::getValue('idunit');

            // 1) Carrega as dotações da unidade (ordenado por saldo_departamento e id)
            $dotacoes = ViewDotacaoPedidoFrotas::where('system_unit_id', '=', $idunit)
                ->orderBy('saldo_departamento_id, id')
                ->load();

            if (!$dotacoes) {
                TTransaction::close();
                return;
            }

            // 2) Colete os IDs de saldo_departamento que aparecem nas dotações
            $idsSaldo = [];
            foreach ($dotacoes as $dpf) {
                if (!empty($dpf->saldo_departamento_id)) {
                    $idsSaldo[] = (int) $dpf->saldo_departamento_id;
                }
            }
            $idsSaldo = array_values(array_unique($idsSaldo));

            // 3) Pré-carrega os saldos atuais só para esses IDs
            $saldos = [];
            if ($idsSaldo) {
                $criteria = new TCriteria;
                $criteria->add(new TFilter('id', 'in', $idsSaldo));
                $repo = new TRepository('SaldoDepartamento');
                $rows = $repo->load($criteria);

                if ($rows) {
                    foreach ($rows as $sd) {
                        $saldos[(int) $sd->id] = (float) ($sd->saldo_total ?? 0);
                    }
                }
            }

            // 4) Processa as dotações com guarda para índices faltantes
            foreach ($dotacoes as $dpf) {
                $idSaldo = (int) ($dpf->saldo_departamento_id ?? 0);
                $valor   = (float) ($dpf->valor ?? 0);

                // se não existir no mapa, considere zero (ou busque on-demand se preferir)
                if (!array_key_exists($idSaldo, $saldos)) {
                    $saldos[$idSaldo] = 0.0;
                }

                $saldo_atual = $saldos[$idSaldo] - $valor;

                // Se a sua VIEW tem a PK da tabela real em outro campo, use-o aqui.
                // Ex.: $dpf->dotacao_pedido_frotas_id
                $pk = (int) ($dpf->dotacao_pedido_frotas_id ?? $dpf->id);

                $dotacaodf = DotacaoPedidoFrotas::find($pk);

                if ($dotacaodf) { // garante UPDATE e não INSERT
                    if ($dotacaodf->saldo_atual != $saldo_atual) {
                        $dotacaodf->saldo_atual = $saldo_atual;
                        $dotacaodf->store();
                    }
                } else {
                    // Se não deve criar quando não existe, apenas ignore
                    // continue;

                    // Se quiser UPSERT, crie SEM setar id (deixe o auto-increment):
                    // $dotacaodf = new DotacaoPedidoFrotas;
                    // unset($dotacaodf->id); // garante INSERT com auto-increment
                    // $dotacaodf->saldo_departamento_id = $idSaldo; // e os demais FKs obrigatórios
                    // $dotacaodf->saldo_atual = $saldo_atual;
                    // $dotacaodf->store();
                }


                // atualiza o acumulado para as próximas linhas do mesmo departamento
                $saldos[$idSaldo] = $saldo_atual;
            }

            TTransaction::close();
        } catch (Exception $e) {
            // importante garantir fechamento e surfaced error
            if (TTransaction::get()) {
                TTransaction::rollback();
            }
            throw $e;
        }
    }

 
    
}
