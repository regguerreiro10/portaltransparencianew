<?php

class PedidoVendaFormView extends TPage
{
    protected $form; // form
    private static $database = 'minierp';
    private static $activeRecord = 'Pedido';
    private static $primaryKey = 'id';
    private static $formName = 'formView_Pedido';

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

        $pedidoId = $param['pedido_id'] ?? ($param['key'] ?? null);
        $pedido = new Pedido($pedidoId);
        // define the form title
        $this->form->setFormTitle("Consulta de Pedido ");

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
        }, $pedido->departamento_unit_id, $pedido, null);

        $criteria_tdbarrowstep1 = new TCriteria();

        $filterVar = "T";
        $criteria_tdbarrowstep1->add(new TFilter('kanban', '=', $filterVar)); 

        $tdbarrowstep1 = new TDBArrowStep('tdbarrowstep1', 'minierp', 'EstadoPedido', 'id', '{nome}','ordem asc' , $criteria_tdbarrowstep1);
        $label = new TLabel("Id:", '', '14px', 'B', '100%');
        $text1 = new TTextDisplay($pedido->id, '', '16px', '');
        $fornecedor = new TLabel("Fornecedor:", '', '14px', 'B', '100%');
        $text7 = new TTextDisplay($pedido->cliente->nome, '', '16px', '');
        $label6 = new TLabel("Unidade", '', '14px', 'B', '100%');
        $text101 = new TTextDisplay($transformed_pedido_departamento_unit_id, '', '16px', '');
        $label50 = new TLabel("Departamento", '', '14px', 'B', '100%');
        $textccusto = new TTextDisplay($pedido->departamento_unit->name, '', '16px', '');
        $label8 = new TLabel("Data do Pedido:", '', '14px', 'B', '100%');
        $text5 = new TTextDisplay(TDate::convertToMask($pedido->dt_pedido, 'yyyy-mm-dd', 'dd/mm/yyyy'), '', '16px', '');
        $label18 = new TLabel("Valor total:", '', '14px', 'B', '100%');
        $text10 = new TTextDisplay(number_format((double)$pedido->valor_liquido_cotacao, '2', ',', '.'), '', '16px', '');
        $label10 = new TLabel("Descrição do Pedido", '', '14px', 'B', '100%');
        $text2 = new TTextDisplay($pedido->descricaopedido, '', '16px', '');
        $label12 = new TLabel("Usuário", '', '14px', 'B', '100%');
        $text3 = new TTextDisplay($pedido->system_users->name, '', '16px', '');
        $label14 = new TLabel("Observações:", '', '14px', 'B', '100%');
        $text6 = new TTextDisplay($pedido->obs, '', '16px', '');
        $Descrição = new TLabel("Rótulo:", '', '12px', '');
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
        $tdbarrowstep1->setValue($pedido->estado_pedido_venda_id);
        $linha_do_tempo->setAction(new TAction(['PedidoVendaHistoricoTimeLine', 'onShow'], ['key' => $pedido->id]));
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


/*
        $row1 = $this->form->addFields([$tdbarrowstep1],[]);
        $row1->layout = [' col-sm-12','col-sm-2'];

        $row2 = $this->form->addFields([$label,$text1],[$fornecedor,$text7],[$label6,$text101],[$label50,$textccusto]);
        $row2->layout = [' col-sm-2','col-sm-3',' col-sm-4',' col-sm-3'];

        $row3 = $this->form->addFields([$label8,$text5],[$label18,$text10],[$label10,$text2],[$label12,$text3]);
        $row3->layout = [' col-sm-2',' col-sm-3',' col-sm-4',' col-sm-3'];

        $row4 = $this->form->addFields([$label14,$text6]);
        $row4->layout = [' col-sm-12'];

        $tab_66adfb0d6cf63 = new BootstrapFormBuilder('tab_66adfb0d6cf63');
        $this->tab_66adfb0d6cf63 = $tab_66adfb0d6cf63;
        $tab_66adfb0d6cf63->setProperty('style', 'border:none; box-shadow:none;');

        $tab_66adfb0d6cf63->appendPage("Produtos");

        $tab_66adfb0d6cf63->addFields([new THidden('current_tab_tab_66adfb0d6cf63')]);
        $tab_66adfb0d6cf63->setTabFunction("$('[name=current_tab_tab_66adfb0d6cf63]').val($(this).attr('data-current_page'));");

        $this->itens_pedido_pedido_venda_id_list = new TQuickGrid;
        $this->itens_pedido_pedido_venda_id_list->disableHtmlConversion();
        $this->itens_pedido_pedido_venda_id_list->style = 'width:100%';
        $this->itens_pedido_pedido_venda_id_list->disableDefaultClick();

        $column_produto_familia_produto_nome = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Família", 'produto->familia_produto->nome', 'left');
        $column_produto_nome = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Produto", 'produto->nome', 'left');
        $column_quantidade = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Quantidade", 'quantidade', 'left');
        $column_valor_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Valor", 'valor', 'left');
        $column_desconto_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Desconto", 'desconto', 'left');
        $column_valor_total_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Valor total", 'valor_total', 'left');
        $column_id = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Nova coluna", 'id', 'left');

        $column_valor_total_transformed->setTotalFunction( function($values) { 
            return array_sum((array) $values); 
        }); 

        $column_valor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $this->itens_pedido_pedido_venda_id_list->createModel();

        $criteria_itens_pedido_pedido_venda_id = new TCriteria();
        $criteria_itens_pedido_pedido_venda_id->add(new TFilter('pedido_venda_id', '=', $pedido->id));

        $criteria_itens_pedido_pedido_venda_id->setProperty('order', 'id desc');

        $itens_pedido_pedido_venda_id_items = ItensPedido::getObjects($criteria_itens_pedido_pedido_venda_id);

        $this->itens_pedido_pedido_venda_id_list->addItems($itens_pedido_pedido_venda_id_items);

        $icon = new TImage('fas:boxes #2196F3');
        $title = new TTextDisplay("{$icon} PRODUTOS", '#333', '16px', '{$fontStyle}');

        $panel = new TPanelGroup($title, '#f5f5f5');
        $panel->class = 'panel panel-default formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->itens_pedido_pedido_venda_id_list));

        $tab_66adfb0d6cf63->addContent([$panel]);

        $tab_66adfb0d6cf63->appendPage("Cidades a Receber");
        $row5 = $tab_66adfb0d6cf63->addFields([$Descrição],[$Cidade]);
        $row5->layout = [' col-sm-6',' col-sm-6'];

        $tab_66adfb0d6cf63->appendPage("Seguimentos a receber");
        $row6 = $tab_66adfb0d6cf63->addFields([$label66]);
        $row6->layout = [' col-sm-12'];

        $tab_66adfb0d6cf63->appendPage("Anexos");
        $row7 = $tab_66adfb0d6cf63->addFields([$label88]);
        $row7->layout = [' col-sm-12'];

        $tab_66adfb0d6cf63->appendPage("Propostas ");
        $row8 = $tab_66adfb0d6cf63->addFields([$label2]);
        $row8->layout = [' col-sm-12'];

        $row9 = $this->form->addFields([$tab_66adfb0d6cf63]);
        $row9->layout = [' col-sm-12'];

        $row10 = $this->form->addFields([$linha_do_tempo]);
        $row10->layout = [' col-sm-12'];

        if(!empty($param['current_tab']))
        {
            $this->form->setCurrentPage($param['current_tab']);
        }

        if(!empty($param['current_tab_tab_66adfb0d6cf63']))
        {
            $this->tab_66adfb0d6cf63->setCurrentPage($param['current_tab_tab_66adfb0d6cf63']);
        }

*/
 //</onBeforeAddFieldsToForm>
               $row1 = $this->form->addFields([$tdbarrowstep1]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([$label,$text1],[$fornecedor,$text7],[$label6,$text101],[$label50,$textccusto]);
        $row2->layout = [' col-sm-2','col-sm-3',' col-sm-4',' col-sm-3'];

        $row3 = $this->form->addFields([$label8,$text5],[$label18,$text10],[$label10,$text2],[$label12,$text3]);
        $row3->layout = [' col-sm-2',' col-sm-3',' col-sm-4',' col-sm-3'];

        $row4 = $this->form->addFields([$label14,$text6]);
        $row4->layout = [' col-sm-12'];

        $tab_66adfb0d6cf63 = new BootstrapFormBuilder('tab_66adfb0d6cf63');
        $this->tab_66adfb0d6cf63 = $tab_66adfb0d6cf63;
        $tab_66adfb0d6cf63->setProperty('style', 'border:none; box-shadow:none;');

        $tab_66adfb0d6cf63->appendPage("Produtos");

        $tab_66adfb0d6cf63->addFields([new THidden('current_tab_tab_66adfb0d6cf63')]);
        $tab_66adfb0d6cf63->setTabFunction("$('[name=current_tab_tab_66adfb0d6cf63]').val($(this).attr('data-current_page'));");

        $this->itens_pedido_pedido_venda_id_list = new TQuickGrid;
        $this->itens_pedido_pedido_venda_id_list->disableHtmlConversion();
        $this->itens_pedido_pedido_venda_id_list->style = 'width:100%';
        $this->itens_pedido_pedido_venda_id_list->disableDefaultClick();
      
          //ITENS PEDIDO
          $column_produto_familia_produto_nome = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Família", 'produto->familia_produto->nome', 'left');
          $column_produto_nome = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Produto", 'produto->nome', 'left');
          $column_produto_unidade_medida = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Und", 'produto->unidade_medida->nome', 'left');
          $column_quantidade = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Quantidade", 'quantidade', 'left');
          $column_valor_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Valor", 'valor', 'left');
          $column_desconto_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Desconto", 'desconto', 'left');
          $column_valor_total_transformed = $this->itens_pedido_pedido_venda_id_list->addQuickColumn("Valor total", 'valor_total', 'left');

          $column_valor_total_transformed->setTotalFunction( function($values) { 
              return array_sum((array) $values); 
          }); 

          $column_valor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

          $column_desconto_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

          $this->itens_pedido_pedido_venda_id_list->createModel();

          $criteria_itens_pedido_pedido_venda_id = new TCriteria();
          $criteria_itens_pedido_pedido_venda_id->add(new TFilter('pedido_venda_id', '=', $pedido->id));

          $criteria_itens_pedido_pedido_venda_id->setProperty('order', 'id desc');

          $itens_pedido_pedido_venda_id_items = ItensPedido::getObjects($criteria_itens_pedido_pedido_venda_id);            
        

        $this->itens_pedido_pedido_venda_id_list->addItems($itens_pedido_pedido_venda_id_items);

        $icon1 = new TImage('fas:boxes #2196F3');
        $title1 = new TTextDisplay("{$icon1} PRODUTOS", '#333', '16px', '{$fontStyle}');

        $panel1 = new TPanelGroup($title1, '#f5f5f5');
        $panel1->class = 'panel panel-default formView-detail';
        $panel1->add(new BootstrapDatagridWrapper($this->itens_pedido_pedido_venda_id_list));

        $tab_66adfb0d6cf63->addContent([$panel1]);
        $row5 = $this->form->addFields([$tab_66adfb0d6cf63]);
        $row5->layout = [' col-sm-12'];

        $this->cidade_pedido_list = new TQuickGrid;
        $this->cidade_pedido_list->disableHtmlConversion();
        $this->cidade_pedido_list->style = 'width:100%';
        $this->cidade_pedido_list->disableDefaultClick();

        $column_nomecidade = $this->cidade_pedido_list->addQuickColumn("Descrição", 'cidade_id', 'left');
        $column_nomecidade->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        //$column_data = $this->cidade_pedido_list->addQuickColumn("Data", 'data_cotacao', 'left');

        $this->cidade_pedido_list->createModel();

        $criteria_cidade_pedido = new TCriteria();
        $criteria_cidade_pedido->add(new TFilter('pedido_id', '=', $pedido->id));

        $criteria_cidade_pedido->setProperty('order', 'id desc');

        $detalhes_cidade_pedido = CidadePedido::getObjects($criteria_cidade_pedido);

        $this->cidade_pedido_list->addItems($detalhes_cidade_pedido);

        $icon = new TImage('fa:city #2196F3'); 
        $title = new TTextDisplay("{$icon} CIDADE A RECEBER", '#333', '16px', '{$fontStyle}');

        $panel = new TPanelGroup($title, '#f5f5f5');
        $panel->class = 'panel panel-default formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->cidade_pedido_list));

        $tab_66adfb0d6cf63->appendPage("Cidades a Receber");
        $row5 = $tab_66adfb0d6cf63->addFields([$panel]);
        $row5->layout = [' col-sm-12'];

        // seguimento

        $this->seguimento_pedido_list = new TQuickGrid;
        $this->seguimento_pedido_list->disableHtmlConversion();
        $this->seguimento_pedido_list->style = 'width:100%';
        $this->seguimento_pedido_list->disableDefaultClick();

        $column_nomeseguimento = $this->seguimento_pedido_list->addQuickColumn("Descrição", 'seguimento->descricao', 'left');
        //var_dump($column_nomeseguimento);

        $this->seguimento_pedido_list->createModel();

        $criteria_seguimento_pedido = new TCriteria();
        $criteria_seguimento_pedido->add(new TFilter('pedido_id', '=', $pedido->id));

        $criteria_seguimento_pedido->setProperty('order', 'id desc');

        $detalhes_seguimento_pedido = PedidoSeguimento::getObjects($criteria_seguimento_pedido);

        $this->seguimento_pedido_list->addItems($detalhes_seguimento_pedido);

        $icon3 = new TImage('fa:building #2196F3');
        $title3 = new TTextDisplay("{$icon3} SEGUIMENTOS A RECEBER", '#333', '16px', '{$fontStyle}');

        $panel2 = new TPanelGroup($title3, '#f5f5f5');
        $panel2->class = 'panel panel-default formView-detail';
        $panel2->add(new BootstrapDatagridWrapper($this->seguimento_pedido_list));

        $tab_66adfb0d6cf63->appendPage("Seguimentos a receber");
        $row6 = $tab_66adfb0d6cf63->addFields([$panel2]);
        $row6->layout = [' col-sm-12'];

         // arquivos

        $this->documentos_pedido_list = new TQuickGrid;
        $this->documentos_pedido_list->disableHtmlConversion();
        $this->documentos_pedido_list->style = 'width:100%';
        $this->documentos_pedido_list->disableDefaultClick();

        //if (in_array($pedido->estado_pedido_venda_id, [EstadoPedido::APROVADO, EstadoPedido::PGTOAPROVADO, EstadoPedido::FINALIZADO, EstadoPedido::ENTREGUE]) )
       // {
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
      //  $this->documentos_pedido_list->createModel();

     /*   $criteria_documentos_pedido = new TCriteria();
        $cotacao = Cotacao::where('pedido_id','=',$pedido->id)
        ->where('pessoa_id','=',$pedido->cliente_id)
        ->load();
        if ($cotacao)
        {                    
            foreach($cotacao as $cot){
                $criteria_itens_pedido_pedido_venda_id->add(new TFilter('cotacao_id', '=', $cot->id));

             }
         }

        $criteria_documentos_pedido->add(new TFilter('cotacao_id', '=', $cot->id));

        $criteria_documentos_pedido->setProperty('order', 'id desc');

        $detalhes_documentos_pedido = DocumentosCotacao::getObjects($criteria_documentos_pedido); */
      /*  } else {*/
            $column_nomearquivo = $this->documentos_pedido_list->addQuickColumn("Descrição", 'caminho', 'left');

            $this->documentos_pedido_list->createModel();

            $criteria_documentos_pedido = new TCriteria();
            $criteria_documentos_pedido->add(new TFilter('pedido_id', '=', $pedido->id));

            $criteria_documentos_pedido->setProperty('order', 'id desc');

            $detalhes_documentos_pedido = DocumentosPedido::getObjects($criteria_documentos_pedido);
        

        $this->documentos_pedido_list->addItems($detalhes_documentos_pedido);

        $icon4 = new TImage('fa:file #2196F3');
        $title4 = new TTextDisplay("{$icon4} ANEXOS", '#333', '16px', '{$fontStyle}');

        $panel3 = new TPanelGroup($title4, '#f5f5f5');
        $panel3->class = 'panel panel-default formView-detail';
        $panel3->add(new BootstrapDatagridWrapper($this->documentos_pedido_list));

        $tab_66adfb0d6cf63->appendPage("Anexos");
        $row7 = $tab_66adfb0d6cf63->addFields([$panel3]);
        $row7->layout = [' col-sm-12'];

        // propostas
        $this->cotacoes_list = new TQuickGrid;
        $this->cotacoes_list->disableHtmlConversion();
        $this->cotacoes_list->style = 'width:100%';
        $this->cotacoes_list->disableDefaultClick();

        $column_id_cotacao = $this->cotacoes_list->addQuickColumn("ID", 'id', 'left');
        $column_id_cotacao->setProperty('style', 'width:60px');
        $column_nome = $this->cotacoes_list->addQuickColumn("Nome", 'pessoa->nome', 'left');
        $column_valor_transformed = $this->cotacoes_list->addQuickColumn("Valor Liquido", 'valor_liquido', 'left');
        $column_valor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
          {
              $temEstado = in_array(EstadoPedido::VISUALIZARCOTACAO, Aprovador::getEstadosDisponiveis());

                if (!$temEstado) {
                    return '****';
                }
  
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
        $column_status = $this->cotacoes_list->addQuickColumn("Estado", 'estado_pedido->nome', 'left');
        $column_cidade = $this->cotacoes_list->addQuickColumn("Cidade", 'cidade_id', 'left');
        $action_onDeleteCotacao = new TDataGridAction([$this, 'onDeleteCotacao']);
        $action_onDeleteCotacao->setUseButton(false);
        $action_onDeleteCotacao->setLabel('Excluir');
        $action_onDeleteCotacao->setImage('fas:trash-alt #dd5a43');
        $action_onDeleteCotacao->setField('id');
        $action_onDeleteCotacao->setDisplayCondition(function($object) {
            return isset($object->estado_pedido_id) && (int) $object->estado_pedido_id === (int) EstadoPedido::PENDENTE;
        });
        $action_onDeleteCotacao->setParameter('pedido_id', $pedido->id);
        if (!empty($param['target_container']))
        {
            $action_onDeleteCotacao->setParameter('target_container', $param['target_container']);
        }
        $this->cotacoes_list->addAction($action_onDeleteCotacao);
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

            if (in_array($object->estado_pedido_id, [EstadoPedido::APROVADO, EstadoPedido::PGTOAPROVADO, EstadoPedido::FINALIZADO, EstadoPedido::ENTREGUE]) ) {
                // var_dump($object);
            //die();  
                TTransaction::open('minierp');

                $cot = Cotacao::where('pedido_id','=',$object->pedido_id)
                              ->where('pessoa_id','=',$object->pessoa_id)
                              ->load();

                if ($cot)
                {
                    foreach ($cot as $cots) {
                        $doccot = DocumentosCotacao::where('cotacao_id','=',$cots->id)
                                                   ->load();
                        if ($doccot){
                            $temnotafiscal = true;
                        }
                        break;
                    }
                }

                TTransaction::close();
            }
            if ($temnotafiscal) {
               $anexo = $object->estado_pedido->nome.' <i class="fa fa-paperclip" aria-hidden="true"></i>';
                return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido->cor}'> {$anexo} <span>";
            } else {
                return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido->cor}'> {$object->estado_pedido->nome} <span>";
            }

        });
        $this->cotacoes_list->createModel();

        $criteria_cotacoes = new TCriteria();
        $criteria_cotacoes->add(new TFilter('pedido_id', '=', $pedido->id));

        $criteria_cotacoes->setProperty('order', 'id desc');

        $detalhes_cotacoes = Cotacao::getObjects($criteria_cotacoes);

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

        $btnPedidoVendaListOnImprimePedidoAction = new TAction(['PedidoVendaList', 'onImprimePedido'],['key'=>$pedido->id]);
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
        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=PedidoVendaFormView]');
        $style->width = '80% !important';   
        $style->show(true);

    }

    public function onShow($param = null)
    {     

    }

    public function onDeleteCotacao($param = null)
    {
        if (isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                TTransaction::open(self::$database);

                $cotacaoId = $param['key'] ?? null;
                if (empty($cotacaoId))
                {
                    throw new Exception('Cotação não informada');
                }

                $cotacao = new Cotacao($cotacaoId, false);

                if ((int) $cotacao->estado_pedido_id !== (int) EstadoPedido::PENDENTE)
                {
                    throw new Exception('Somente propostas pendentes podem ser excluídas');
                }

                $itens = ItensCotacao::where('cotacao_id', '=', $cotacaoId)->load();
                if ($itens)
                {
                    foreach ($itens as $item)
                    {
                        $item->delete();
                    }
                }

                $documentos = DocumentosCotacao::where('cotacao_id', '=', $cotacaoId)->load();
                if ($documentos)
                {
                    foreach ($documentos as $documento)
                    {
                        $documento->delete();
                    }
                }

                $historicos = CotacaoHistorico::where('cotacao_id', '=', $cotacaoId)->load();
                if ($historicos)
                {
                    foreach ($historicos as $historico)
                    {
                        $historico->delete();
                    }
                }

                $cotacao->delete();

                TTransaction::close();

                TToast::show('success', 'Proposta excluída com sucesso', 'topRight', 'far:check-circle');

                $reloadParam = [
                    'key' => $param['pedido_id'] ?? null,
                ];

                if (!empty($param['target_container']))
                {
                    $reloadParam['target_container'] = $param['target_container'];
                }

                TApplication::loadPage(__CLASS__, 'onShow', $reloadParam);
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        }
        else
        {
            $action = new TAction([$this, 'onDeleteCotacao']);
            $action->setParameters($param);
            $action->setParameter('delete', 1);

            new TQuestion('Tem certeza que deseja excluir esta proposta?', $action);
        }
    }

             //</autoCode>
            //</autoCode>

}

