<?php

class PedidoFrotasFormView extends TPage
{
    protected $form; // form
    private static $database = 'minierp';
    private static $activeRecord = 'PedidoFrotas';
    private static $primaryKey = 'id';
    private static $formName = 'formView_PedidoFrotas';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        TTransaction::open(self::$database);
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setTagName('div');

        $pedido = new PedidoFrotas($param['key']);
        // define the form title
        $this->form->setFormTitle("Consulta de Pedido Frotas");

        $transformed_pedido_departamento_unit_id = call_user_func(function($value, $object, $row)
        {
            //code here

            $dep = new DepartamentoUnit($object->departamento_unit_id);
            if ($dep){
                $unidade = new SystemUnit($dep->system_unit_id);

                if ($unidade)
                {
                    return $unidade->name;
                } else return 'Não informado!';
            } else {
               return 'Não informado!'; 
            }
        }, 
        
        $pedido->departamento_unit_id, $pedido, null);

        $criteria_tdbarrowstep1 = new TCriteria();

        $filterVar = "T";
        $criteria_tdbarrowstep1->add(new TFilter('kanban', '=', $filterVar)); 

      //  $condutorEntrada = Condutor::find($pedido->condutor_entrada_id);
      //  $condutorRetirada = Condutor::find($pedido->condutor_retirada_id);

        $tdbarrowstep1 = new TDBArrowStep('tdbarrowstep1', 'minierp', 'EstadoPedidoFrotas', 'id', '{nome}','ordem asc' , $criteria_tdbarrowstep1);
        $label = new TLabel("Id:", '', '14px', 'B', '100%');
        $text1 = new TTextDisplay($pedido->id, '', '16px', '');
        $fornecedor = new TLabel("Fornecedor:", '', '14px', 'B', '100%');
        $text7 = new TTextDisplay($pedido->estabelecimento->nome, '', '16px', '');
        $label6 = new TLabel("Unidade", '', '14px', 'B', '100%');
        $text101 = new TTextDisplay($transformed_pedido_departamento_unit_id, '', '16px', '');
        $label50 = new TLabel("Departamento", '', '14px', 'B', '100%');
        $textccusto = new TTextDisplay($pedido->departamento_unit->name, '', '16px', '');
        $label8 = new TLabel("Data do Pedido:", '', '14px', 'B', '100%');
        $text5 = new TTextDisplay(TDate::convertToMask($pedido->dt_pedido, 'yyyy-mm-dd', 'dd/mm/yyyy'), '', '16px', '');
        $label18 = new TLabel("Valor total:", '', '14px', 'B', '100%');
        $text10 = new TTextDisplay(number_format((double)$pedido->valor_liquido_proposta, '2', ',', '.'), '', '16px', '');
        $label10 = new TLabel("Placa/Descrição", '', '14px', 'B', '100%');
        $text2 = new TTextDisplay('Placa: '.$pedido->veiculos->placa.'/'.$pedido->descricaopedido, '', '16px', '');
        $label12 = new TLabel("Usuário", '', '14px', 'B', '100%');
        $usuarioPedido = 'Não informado';
        if (!empty($pedido->system_users_id)) {
            try {
                $usuarioPedido = $pedido->system_users->name ?? $usuarioPedido;
            } catch (Exception $e) {
                $usuarioPedido = 'Não informado';
            }
        }

        $text3 = new TTextDisplay($usuarioPedido, '', '16px', '');
        $label14 = new TLabel("Observações:", '', '14px', 'B', '100%');
      //  $label19 = new TLabel("Data de entrada", '', '14px', 'B', '100%');
      //  $text19 = new TTextDisplay($pedido->dataentrada, '', '16px', '');
       // $label20 = new TLabel("Condutor de entrada", '', '14px', 'B', '100%');
      //  $text20 = new TTextDisplay($condutorEntrada ? $condutorEntrada->nome : 'Não informado', '', '16px', '');
      //  $label21 = new TLabel("Data de retirada", '', '14px', 'B', '100%');
      //  $text21 = new TTextDisplay($pedido->dataretirada, '', '16px', '');
     //   $label22 = new TLabel("Condutor de retirada", '', '14px', 'B', '100%');
     //   $text22 = new TTextDisplay($condutorRetirada ? $condutorRetirada->nome : 'Não informado', '', '16px', '');
        $label14 = new TLabel("Observações:", '', '14px', 'B', '100%');
        $text6 = new TTextDisplay($pedido->obs, '', '16px', '');
        $Descricao = new TLabel("Rótulo:", '', '12px', '');
        $Cidade = new TLabel("Rótulo:", '', '12px', '');
        $label66 = new TLabel("Rótulo:", '', '12px', '');
        $label88 = new TLabel("Rótulo:", '', '12px', '');
        $label2 = new TLabel("Rótulo:", '', '12px', '');
        $linha_do_tempo = new BPageContainer();

        $tdbarrowstep1->setEditable(false);
        $tdbarrowstep1->setColorColumn('cor');
        $tdbarrowstep1->setFilledColor('#fd9308');
        $tdbarrowstep1->setFilledFontColor('#ffffff');
        $tdbarrowstep1->setUnfilledColor('#d3d3d3');
        $tdbarrowstep1->setUnfilledFontColor('#333333');
        $tdbarrowstep1->setWidth('100%');
        $tdbarrowstep1->setHeight('60');
        $tdbarrowstep1->setValue($pedido->estado_pedido_frotas_id);
        $linha_do_tempo->setAction(new TAction(['PedidoFrotasHistoricoTimeLine', 'onShow'], ['key' => $pedido->id]));
        $linha_do_tempo->setId('b627af0e8e2a08');
        $text7->setSize('100%');
        $text2->setSize('100%');
        $text3->setSize('100%');
        $textccusto->setSize('100%');
        $linha_do_tempo->setSize('100%');

        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $linha_do_tempo->add($loadingContainer);


        $row1 = $this->form->addFields([$tdbarrowstep1]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([$label,$text1],[$fornecedor,$text7],[$label6,$text101],[$label50,$textccusto]);
        $row2->layout = [' col-sm-2','col-sm-3',' col-sm-4',' col-sm-3'];

        $row3 = $this->form->addFields([$label8,$text5],[$label18,$text10],[$label10,$text2],[$label12,$text3]);
        $row3->layout = [' col-sm-2',' col-sm-3',' col-sm-4',' col-sm-3'];

    //    $row3 = $this->form->addFields([$label19, $text19],[$label20,$text20], [$label21, $text21], [$label22, $text22]);
     //   $row3->layout = [' col-sm-2',' col-sm-3',' col-sm-4',' col-sm-3'];

        $row4 = $this->form->addFields([$label14,$text6]);
        $row4->layout = [' col-sm-12'];

        $tab_66adfb0d6cf63 = new BootstrapFormBuilder('tab_66adfb0d6cf63');
        $this->tab_66adfb0d6cf63 = $tab_66adfb0d6cf63;
        $tab_66adfb0d6cf63->setProperty('style', 'border:none; box-shadow:none;');

        $tab_66adfb0d6cf63->appendPage("Produtos/Serviços");

        $tab_66adfb0d6cf63->addFields([new THidden('current_tab_tab_66adfb0d6cf63')]);
        $tab_66adfb0d6cf63->setTabFunction("$('[name=current_tab_tab_66adfb0d6cf63]').val($(this).attr('data-current_page'));");

        $this->itens_pedido_pedido_frotas_id_list = new TQuickGrid;
        $this->itens_pedido_pedido_frotas_id_list->disableHtmlConversion();
        $this->itens_pedido_pedido_frotas_id_list->style = 'width:100%';
        $this->itens_pedido_pedido_frotas_id_list->disableDefaultClick();
         {

          //ITENS PEDIDO
          $column_id = $this->itens_pedido_pedido_frotas_id_list->addQuickColumn("ID", 'id', 'left');
          $column_tipo = $this->itens_pedido_pedido_frotas_id_list->addQuickColumn("Tipo", 'tipo', 'left');
        $column_produto_id = $this->itens_pedido_pedido_frotas_id_list->addQuickColumn("Produto/Serviço", 'produto->nome', 'left');
        $column_descricao = $this->itens_pedido_pedido_frotas_id_list->addQuickColumn("Obs", 'descricao', 'left');
        $column_quantidade = $this->itens_pedido_pedido_frotas_id_list->addQuickColumn("Quantidade", 'qtde', 'left');
        $column_valor_total_transformed = $this->itens_pedido_pedido_frotas_id_list->addQuickColumn("Valor", 'valor_unitario', 'left');
        $column_valor_totalgeral_transformed = $this->itens_pedido_pedido_frotas_id_list->addQuickColumn("Total", 'valor_total', 'left');
        $column_valor_desconto_transformed = $this->itens_pedido_pedido_frotas_id_list->addQuickColumn("Desconto", 'perc_desconto', 'left');
        $column_valor_liquido_transformed = $this->itens_pedido_pedido_frotas_id_list->addQuickColumn("Valor liquido", 'valor_total', 'left');

      $column_valor_totalgeral_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
          {
              $value = $object->qtde * $object->valor_unitario;
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

          $column_valor_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

          $column_valor_liquido_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

          $this->itens_pedido_pedido_frotas_id_list->createModel();

          $criteria_itens_pedido_pedido_frotas_id = new TCriteria();
          $criteria_itens_pedido_pedido_frotas_id->add(new TFilter('pedido_frotas_id', '=', $pedido->id));

          $criteria_itens_pedido_pedido_frotas_id->setProperty('order', 'id desc');

          $itens_pedido_pedido_frotas_id_items = ItensPedidoFrotas::getObjects($criteria_itens_pedido_pedido_frotas_id);            
        }

        $this->itens_pedido_pedido_frotas_id_list->addItems($itens_pedido_pedido_frotas_id_items);

        $icon1 = new TImage('fas:boxes #2196F3');
        $title1 = new TTextDisplay("{$icon1} PRODUTOS/SERVIÇOS", '#333', '16px', '{$fontStyle}');

        $panel1 = new TPanelGroup($title1, '#f5f5f5');
        $panel1->class = 'panel panel-default formView-detail';
        $panel1->add(new BootstrapDatagridWrapper($this->itens_pedido_pedido_frotas_id_list));

        $tab_66adfb0d6cf63->addContent([$panel1]);
        $row5 = $this->form->addFields([$tab_66adfb0d6cf63]);
        $row5->layout = [' col-sm-12'];

        // redes
        $this->redes_enviadas_list = new TQuickGrid;
        $this->redes_enviadas_list->disableHtmlConversion();
        $this->redes_enviadas_list->style = 'width:100%';
        $this->redes_enviadas_list->disableDefaultClick();

        $column_nome= $this->redes_enviadas_list->addQuickColumn("Nome", 'nome', 'left');
        $column_email = $this->redes_enviadas_list->addQuickColumn("Email", 'email', 'left');
        $column_telefone = $this->redes_enviadas_list->addQuickColumn("Telefone", 'fone', 'left');
        $column_cidade = $this->redes_enviadas_list->addQuickColumn("Cidade/UF", 'cidade_id', 'left');
        $column_cidade->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
               
                    return "{$object->nome_cidade} - {$object->sigla}";

        });

        $this->redes_enviadas_list->createModel();

        $criteria_redes_enviadas = new TCriteria();
        $criteria_redes_enviadas->add(new TFilter('pedido_frotas_id', '=', $pedido->id));

        $criteria_redes_enviadas->setProperty('order', 'id desc');

        $detalhes_criteria_redes_enviadas = ViewPedidosAsCliente::getObjects($criteria_redes_enviadas);

        $this->redes_enviadas_list->addItems($detalhes_criteria_redes_enviadas);

        $icon = new TImage('fa:city #2196F3'); 
        $title = new TTextDisplay("{$icon} Redes enviadas", '#333', '16px', '{$fontStyle}');

        $panel = new TPanelGroup($title, '#f5f5f5');
        $panel->class = 'panel panel-default formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->redes_enviadas_list));

        $tab_66adfb0d6cf63->appendPage("Redes enviadas");
        $row5 = $tab_66adfb0d6cf63->addFields([$panel]);
        $row5->layout = [' col-sm-12'];

        // anexos do pedido
        $this->documentos_pedido_frotas_list = new TQuickGrid;
        $this->documentos_pedido_frotas_list->disableHtmlConversion();
        $this->documentos_pedido_frotas_list->style = 'width:100%';
        $this->documentos_pedido_frotas_list->disableDefaultClick();

        $column_caminho_pedido = $this->documentos_pedido_frotas_list->addQuickColumn("Caminho", 'caminho', 'left');
        $column_usuario_pedido = $this->documentos_pedido_frotas_list->addQuickColumn("Usuário", 'system_users_id', 'left');
        $column_criacao_pedido = $this->documentos_pedido_frotas_list->addQuickColumn("Data Criação", 'created_at', 'left');
        $column_usuario_pedido->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (empty($value)) {
                return 'Não informado';
            }

            try {
                $usuario = $object->get_system_users();
                return $usuario->name ?? 'Não informado';
            } catch (Exception $e) {
                return 'Não informado';
            }
        });

        $column_caminho_pedido->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (empty($value))
            {
                return '';
            }

            $fileName = $value;

            if (strpos($value, '%7B') !== false)
            {
                $valueObject = json_decode(urldecode($value));
                $fileName = $valueObject->fileName ?? $value;
            }
            else
            {
                $pathParts = explode('/', str_replace('\\', '/', $value));
                $fileName = end($pathParts) ?: $value;
            }

            $a = new TElement('a');
            $a->href = "download.php?file={$value}";
            $a->class = 'btn btn-link';
            $a->add($fileName);
            $a->target = '_blank';

            return $a;
        });

         $column_criacao_pedido->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y h:i:s');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $this->documentos_pedido_frotas_list->createModel();

        $criteria_documentos_pedido_frotas = new TCriteria();
        $criteria_documentos_pedido_frotas->add(new TFilter('pedido_frotas_id', '=', $pedido->id));
        $criteria_documentos_pedido_frotas->setProperty('order', 'id desc');

        $documentos_pedido_frotas = DocumentosPedidoFrotas::getObjects($criteria_documentos_pedido_frotas);
        $this->documentos_pedido_frotas_list->addItems($documentos_pedido_frotas);

        $iconPedido = new TImage('fa:paperclip #2196F3');
        $titlePedido = new TTextDisplay("{$iconPedido} ANEXOS DO PEDIDO", '#333', '16px', '{$fontStyle}');

        $panelPedido = new TPanelGroup($titlePedido, '#f5f5f5');
        $panelPedido->class = 'panel panel-default formView-detail';
        $panelPedido->add(new BootstrapDatagridWrapper($this->documentos_pedido_frotas_list));

        $tab_66adfb0d6cf63->appendPage("Anexos do pedido");
        $rowPedido = $tab_66adfb0d6cf63->addFields([$panelPedido]);
        $rowPedido->layout = [' col-sm-12'];

         // arquivos

        $this->documentos_pedido_list = new TQuickGrid;
        $this->documentos_pedido_list->disableHtmlConversion();
        $this->documentos_pedido_list->style = 'width:100%';
        $this->documentos_pedido_list->disableDefaultClick();

        if (in_array($pedido->estado_pedido_frotas_id, [EstadoPedido::APROVADO, EstadoPedido::PGTOAPROVADO, EstadoPedido::FINALIZADO, EstadoPedido::ENTREGUE]) )
        {
        //ITE
        $column_nomearquivo_transformed = $this->documentos_pedido_list->addQuickColumn("Descrição", 'caminho', 'left');
        $column_nomearquivo_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $value = explode(',', $value);
            if(count($value) == 0)
            {
                $value = $value[0];
            }

            if(is_array($value))
            {
                $files = $value;
                $divFiles = new TElement('div');
                foreach($files as $file)
                {
                    $fileName = $file;
                    if (strpos($file, '%7B') !== false) 
                    {
                        if (!empty($file)) 
                        {
                            $fileObject = json_decode(urldecode($file));

                            $fileName = $fileObject->fileName;
                        }
                    }

                    $a = new TElement('a');
                    $a->href = "download.php?file={$fileName}";
                    $a->class = 'btn btn-link';
                    $a->add($fileName);
                    $a->target = '_blank';

                    $divFiles->add($a);

                }

                return $divFiles;
            }
            else
            {
                if (strpos($value, '%7B') !== false) 
                {
                    if (!empty($value)) 
                    {
                        $value_object = json_decode(urldecode($value));
                        $value = $value_object->fileName;
                    }
                }

                if($value)
                {
                    $a = new TElement('a');
                    $a->href = "download.php?file={$value}";
                    $a->class = 'btn btn-default';
                    $a->add($value);
                    $a->target = '_blank';

                    return $a;
                }

                return $value;
            }
        });   

        $this->documentos_pedido_list->createModel();

        $criteria_documentos_pedido = new TCriteria();
        $cotacao = Propostas::where('pedido_frotas_id','=',$pedido->id)
        ->load();
        $id = [];
        if ($cotacao)
        {                    
            foreach($cotacao as $cot){
                $id[$cot->id] = $cot->id;

             }
         }

        $criteria_documentos_pedido->add(new TFilter('propostas_id', 'in', $id));

        $criteria_documentos_pedido->setProperty('order', 'id desc');

        $detalhes_documentos_pedido = DocumentosPropostas::getObjects($criteria_documentos_pedido);
        } else {
            $column_nomearquivo = $this->documentos_pedido_list->addQuickColumn("Descrição", 'caminho', 'left');

            $this->documentos_pedido_list->createModel();

            $criteria_documentos_pedido = new TCriteria();
            $criteria_documentos_pedido->add(new TFilter('pedido_frotas_id', '=', $pedido->id));

            $criteria_documentos_pedido->setProperty('order', 'id desc');

            $detalhes_documentos_pedido = DocumentosPedidoFrotas::getObjects($criteria_documentos_pedido);
        }

        $this->documentos_pedido_list->addItems($detalhes_documentos_pedido);

        $icon4 = new TImage('fa:file #2196F3');
        $title4 = new TTextDisplay("{$icon4} ANEXOS DAS PROPOSTAS", '#333', '16px', '{$fontStyle}');

        $panel3 = new TPanelGroup($title4, '#f5f5f5');
        $panel3->class = 'panel panel-default formView-detail';
        $panel3->add(new BootstrapDatagridWrapper($this->documentos_pedido_list));

        $tab_66adfb0d6cf63->appendPage("Anexos das propostas");
        $row7 = $tab_66adfb0d6cf63->addFields([$panel3]);
        $row7->layout = [' col-sm-12'];

        // propostas
        $this->cotacoes_list = new TQuickGrid;
        $this->cotacoes_list->disableHtmlConversion();
        $this->cotacoes_list->style = 'width:100%';
        $this->cotacoes_list->disableDefaultClick();

        $column_nome = $this->cotacoes_list->addQuickColumn("Nome", 'pessoa->nome', 'left');
        $column_valor_transformed = $this->cotacoes_list->addQuickColumn("Valor Liquido", 'valor_liquido', 'left');
         $column_valor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
             TTransaction::open('minierp');
            
           
             $value=0;    
            $objects = ItensPropostas::where('propostas_id','=',$object->id)
                                        ->load();
            if ($objects) {
                foreach ($objects as $obj) {
                    $value = $value + ($obj->valor * $obj->qtde) - $obj->perc_desconto;
                }
            }
             if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
            TTransaction::close();

        });

        $column_status = $this->cotacoes_list->addQuickColumn("Estado", 'estado_pedido_frotas->nome', 'left');
        $column_cidade = $this->cotacoes_list->addQuickColumn("Cidade", 'cidade_id', 'left');
        $column_cidade->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here

                TTransaction::open('minierp');

                $cidade = new Cidade($object->cidade_id);
                if ($cidade) {
                    $estado = new Estado($cidade->estado_id);
                    return "{$cidade->nome} - {$estado->sigla}";

                } else {
                    return "Não informado!!!";

                }

                TTransaction::close();

        });
        $column_status->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
            $temnotafiscal = false;

         //   if (in_array($object->estado_pedido_frotas_id, [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PGTOAPROVADO, EstadoPedidoFrotas::FINALIZADO, EstadoPedidoFrotas::ENTREGUE]) ) {
                // var_dump($object);
            //die();  
                TTransaction::open('minierp');

                $cot = Propostas::where('id','=',$object->id)
                                ->load();

                if ($cot)
                {
                    foreach ($cot as $cots) {
                        $doccot = DocumentosPropostas::where('propostas_id','=',$cots->id)
                                                   ->load();
                        if ($doccot){
                            $temnotafiscal = true;
                        }
                        break;
                    }
                }

                TTransaction::close();
           // }
            if ($temnotafiscal) {
               $anexo = $object->estado_pedido_frotas->nome.' <i class="fa fa-paperclip" aria-hidden="true"></i>';
                return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido_frotas->cor}'> {$anexo} <span>";
            } else {
                return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido_frotas->cor}'> {$object->estado_pedido_frotas->nome} <span>";
            }

        });
   /*     $column_comentario = $this->cotacoes_list->addQuickColumn("Comentario", '', 'left');
        $column_comentario->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
             //code here

                // var_dump($object);
            //die();  
            TTransaction::open('minierp');

            $criteria = new TCriteria();
            $criteria->add(new TFilter('propostas_id', '=', $object->id));
            $criteria->add(new TFilter('system_users_id', '<>', TSession::getValue('userid')));
            $criteria->add(new TFilter('leitura_dt', 'IS', NULL));

            $repo = new TRepository('ComentarioProposta');
            $com = $repo->load($criteria);

            $qtcom = 0;
            if ($com) {
                foreach ($com as $comm) {
                    $qtcom++;
                }
            }

            TTransaction::close();

            $anexo = ' <i class="fa fa-comment-alt"></i> ' . $qtcom . ' Comentários';

            $bgcolor = ($qtcom > 0) ? '#10c246' : '#6d799d';

            // Cria a ação que aponta para o PropostasForm::onEdit com o ID do objeto

            $action = new TAction(['PropostasFormView', 'onEdit'], ['key'=>$object->id]);
            $a = new TElement('a');
            $a->class = 'btn btn-link';
            $a->style = "background-color: {$bgcolor}; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none;";
            $a->generator = 'adianti';
            $a->href = $action->serialize();
            $a->add("{$anexo}");
            return $a;

        }); */
        $this->cotacoes_list->createModel();

        $criteria_cotacoes = new TCriteria();
        $criteria_cotacoes->add(new TFilter('pedido_frotas_id', '=', $pedido->id));

        $criteria_cotacoes->setProperty('order', 'id desc');

        $detalhes_cotacoes = Propostas::getObjects($criteria_cotacoes);

        $this->cotacoes_list->addItems($detalhes_cotacoes);

        $icon5 = new TImage('fa:file #2196F3');
        $title5 = new TTextDisplay("{$icon5} PROPOSTAS", '#333', '16px', '{$fontStyle}');

        $panel4 = new TPanelGroup($title5, '#f5f5f5');
        $panel4->class = 'panel panel-default formView-detail';
        $panel4->add(new BootstrapDatagridWrapper($this->cotacoes_list));

        $tab_66adfb0d6cf63->appendPage("Propostas");
        $row8 = $tab_66adfb0d6cf63->addFields([$panel4]);
        $row8->layout = [' col-sm-12'];

        $row9 = $this->form->addFields([$linha_do_tempo]);
        $row9->layout = [' col-sm-12'];

        $btnPedidoVendaListOnImprimePedidoAction = new TAction(['PedidoFrotasList', 'onImprimir'],['id'=>$pedido->id]);
        $btnPedidoVendaListOnImprimePedidoLabel = new TLabel("Documento");

        $btnPedidoVendaListOnImprimePedido = $this->form->addHeaderAction($btnPedidoVendaListOnImprimePedidoLabel, $btnPedidoVendaListOnImprimePedidoAction, 'far:file-pdf #9C27B0'); 
        $btnPedidoVendaListOnImprimePedidoLabel->setFontSize('12px'); 
        $btnPedidoVendaListOnImprimePedidoLabel->setFontColor('#333'); 

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        TTransaction::close();
      
      /*  parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=PedidoFrotasFormView]');
        $style->width = '90% !important';   
        $style->show(true);*/


        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=PedidoFrotasFormView]');
        $style->width = '80% !important';   
        $style->show(true);
    }
    public function onShow($param = null)
    {

    } 
   

         
}

