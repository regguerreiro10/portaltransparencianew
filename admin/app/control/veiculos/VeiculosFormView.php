<?php

class VeiculosFormView extends TPage
{
    protected $form; // form
    private static $database = 'minierp';
    private static $activeRecord = 'Veiculos';
    private static $primaryKey = 'id';
    private static $formName = 'formView_Veiculos';

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

        $veiculos = new Veiculos($param['key']);
        // define the form title
        $this->form->setFormTitle("Consulta de Veiculos, Aeronaves e/ou Equipamentos");

        $label2x = new TLabel("Unidade:", '', '14px', 'B', '100%');
        $text1x = new TTextDisplay($veiculos->system_unit->name, '', '16px', '');
        $label4x = new TLabel("Departamento:", '', '14px', 'B', '100%');
        $text10x = new TTextDisplay($veiculos->departamento_unit->name, '', '16px', '');
        $label6x = new TLabel("Usuário:", '', '14px', 'B', '100%');
        $text11x = new TTextDisplay($veiculos->system_users->name, '', '16px', '');
        $label66x = new TLabel("Valor:", '', '14px', 'B', '100%');
        $text111x = new TTextDisplay($veiculos->valor_tabela_fipe, '', '16px', '');

        
        $label2 = new TLabel("Id:", '', '14px', 'B', '100%');
        $text1 = new TTextDisplay($veiculos->id, '', '16px', '');
        $label4 = new TLabel("Criado em:", '', '14px', 'B', '100%');
        $text10 = new TTextDisplay(TDateTime::convertToMask($veiculos->created_at, 'yyyy-mm-dd hh:ii', 'dd/mm/yyyy hh:ii'), '', '16px', '');
        $label6 = new TLabel("Atualizado em:", '', '14px', 'B', '100%');
        $text11 = new TTextDisplay(TDateTime::convertToMask($veiculos->updated_at, 'yyyy-mm-dd hh:ii', 'dd/mm/yyyy hh:ii'), '', '16px', '');
        $label66 = new TLabel("Status:", '', '14px', 'B', '100%');
        $text111 = new TTextDisplay($veiculos->status_veiculo->nome, '', '16px', '');
        
        $label88 = new TLabel("Prefixo:", '', '14px', 'B', '100%');
        $text55 = new TTextDisplay($veiculos->prefixo, '', '16px', '');
         $label8 = new TLabel("Placa:", '', '14px', 'B', '100%');
        $text5 = new TTextDisplay(new TImage('fas:car #F44336').$veiculos->placa, '', '16px', '');
        $label10 = new TLabel("Marca/Modelo:", '', '14px', 'B', '100%');
        $text2 = new TTextDisplay($veiculos->marca->descricao.'/'.$veiculos->modelo->descricao, '', '16px', '');
        $label12 = new TLabel("Propriedade", '', '14px', 'B', '100%');
        $text3 = new TTextDisplay($veiculos->propriedade->descricao, '', '16px', '');

     
        $label100 = new TLabel("Cor:", '', '14px', 'B', '100%');
        $text22 = new TTextDisplay($veiculos->corveiculo->descricao, '', '16px', '');
        $label122 = new TLabel("Ano", '', '14px', 'B', '100%');
        $text33 = new TTextDisplay($veiculos->anof.'/'.$veiculos->anom, '', '16px', '');
        $label1001 = new TLabel("Chassi:", '', '14px', 'B', '100%');
        $text221 = new TTextDisplay($veiculos->chassi, '', '16px', '');
        $label1221 = new TLabel("Renavam", '', '14px', 'B', '100%');
        $text331 = new TTextDisplay($veiculos->renavam, '', '16px', '');


        $label1009 = new TLabel("Capacidade Tanque:", '', '14px', 'B', '100%');
        $text229 = new TTextDisplay($veiculos->capacidade_tanque, '', '16px', '');
        $label1229 = new TLabel("Tipo Combustivel", '', '14px', 'B', '100%');
        $text339 = new TTextDisplay($veiculos->tipo_combustivel->descricao, '', '16px', '');
        $label10019 = new TLabel("Tipo Veículo:", '', '14px', 'B', '100%');
        $text2219 = new TTextDisplay($veiculos->tipo_veiculo->descricao, '', '16px', '');
        $label12219 = new TLabel("Km", '', '14px', 'B', '100%');
        $text3319 = new TTextDisplay($veiculos->hodometroatual, '', '16px', '');


     /*   $label14 = new TLabel(new TImage('far:id-card #9C27B0')."Documento:", '', '14px', 'B', '100%');
        $text6 = new TTextDisplay($veiculos->documento, '', '16px', '');
        $label16 = new TLabel("Email:", '', '14px', 'B', '100%');
        $text9 = new TTextDisplay(new TImage('far:envelope #F44336').$veiculos->email, '', '16px', '');
        $label18 = new TLabel("Fone:", '', '14px', 'B', '100%');
        $text8 = new TTextDisplay(new TImage('fas:phone-alt #00BCD4').$veiculos->fone, '', '16px', '');
        $label20 = new TLabel(new TImage('fas:comment-alt #FF9800')."Obs:", '', '14px', 'B', '100%');
        $text7 = new TTextDisplay($veiculos->obs, '', '16px', '');
        $label22 = new TLabel("Grupos:", '', '14px', 'B', '100%');
        $grupos = new TTextDisplay($veiculos->pessoa_grupo_grupo_pessoa_to_string, '', '16px', '');*/
        $indicadores_cliente = new BPageContainer();
        $listagem_de_fotos = new BPageContainer();
        $tabela_temparia_list = new BPageContainer();
//        $action2 = new TActionLink("Gerar Extrato", new TAction(['PessoaContaPagarEmAbertoDocument', 'onGenerate'], ['key'=> $veiculos->id]), '', '12px', '', 'fas:file-invoice-dollar #F44336');
        
       // $text6->enableToggleVisibility(true);
       
        $indicadores_cliente->setSize('100%');
        $listagem_de_fotos->setSize('100%');
        $tabela_temparia_list->setSize('100%');
       

        $indicadores_cliente->setAction(new TAction(['IndicadorVeiculosForm', 'onShow'], ['veiculos_id' => $veiculos->id]));
        $listagem_de_fotos->setAction(new TAction(['FotosVeiculosSimpleList', 'onShow'], ['veiculos_id' => $veiculos->id]));
        $tabela_temparia_list->setAction(new TAction(['ProdutoPrecoVeiculoTempariaList', 'onShow'], ['veiculos_id' => $veiculos->id]));

        $indicadores_cliente->setId('b62a285e8c75b1');
        $listagem_de_fotos->setId('b62a2838bf3de0');
        $tabela_temparia_list->setId('b_temparia_veiculo_list');

    //    $action2->class = 'btn btn-default';

        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $indicadores_cliente->add($loadingContainer);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $listagem_de_fotos->add($loadingContainer);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $tabela_temparia_list->add($loadingContainer);

       
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

    


        $row0 = $this->form->addFields([$label2x,$text1x],[$label4x,$text10x],[$label6x,$text11x],[$label66x,$text111x]);
        $row0->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $row1 = $this->form->addFields([$label2,$text1],[$label4,$text10],[$label6,$text11],[$label66,$text111]);
        $row1->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $row2 = $this->form->addContent([new TFormSeparator("", '#333', '18', '#eee')]);
        $row3 = $this->form->addFields([$label88,$text55],[$label8,$text5],[$label10,$text2],[$label12,$text3]);
        $row3->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $row4 = $this->form->addFields([$label100,$text22],[$label122,$text33],[$label1001,$text221],[$label1221,$text331]);
        $row4->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $row5 = $this->form->addFields([$label1009,$text229],[$label1229,$text339],[$label10019,$text2219],[$label12219,$text3319]);
        $row5->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $row33 = $this->form->addContent([new TFormSeparator("", '#333', '18', '#eee')]);

        $row6 = $this->form->addFields([$indicadores_cliente]);
        $row6->layout = [' col-sm-12'];

        $tab_622940daf9f3b = new BootstrapFormBuilder('tab_622940daf9f3b');
        $this->tab_622940daf9f3b = $tab_622940daf9f3b;
        $tab_622940daf9f3b->setProperty('style', 'border:none; box-shadow:none;');
        //fotos veiculos
        $tab_622940daf9f3b->appendPage(new TImage('fas:car #607D8B')."Fotos");

        $tab_622940daf9f3b->addFields([new THidden('current_tab_tab_622940daf9f3b')]);
        $tab_622940daf9f3b->setTabFunction("$('[name=current_tab_tab_622940daf9f3b]').val($(this).attr('data-current_page'));");

        
        $row7 = $tab_622940daf9f3b->addFields([$listagem_de_fotos]);
        $row7->layout = [' col-sm-12'];

        // sssaçdpdddodaofodsa
        $tab_622940daf9f3b->appendPage(new TImage('fas:tools #F44336')."Pedidos");

        $this->pedidos_veiculo_id_list = new TQuickGrid;
        $this->pedidos_veiculo_id_list->disableHtmlConversion();
        $this->pedidos_veiculo_id_list->style = 'width:100%';
        $this->pedidos_veiculo_id_list->disableDefaultClick();

        $column_id = $this->pedidos_veiculo_id_list->addQuickColumn("Id",'id',  'center' , '70px');
        $column_cliente_nome = $this->pedidos_veiculo_id_list->addQuickColumn( "Fornecedor",'estabelecimento->nome', 'left');
        $column_dt_pedido_transformed = $this->pedidos_veiculo_id_list->addQuickColumn("Data do Pedido", 'dt_pedido', 'left');
        $column_valor_total_transformed = $this->pedidos_veiculo_id_list->addQuickColumn( "Valor total",'valor_total', 'left');
        $column_estado_pedido_venda_nome_transformed = $this->pedidos_veiculo_id_list->addQuickColumn( "Estado de pedido",'estado_pedido_frotas->nome', 'left');
        $column_dt_pedido_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_estado_pedido_venda_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
           
                return "<span class='label label-default' style='width:240px; background-color:{$object->estado_pedido_frotas->cor}'> {$object->estado_pedido_frotas->nome} <span>";
            

        });       
      
        $this->pedidos_veiculo_id_list->createModel();

        $criteria_pedidos_veiculo_id_list = new TCriteria();
        $criteria_pedidos_veiculo_id_list->add(new TFilter('veiculos_id', '=', $veiculos->id));

        $criteria_pedidos_veiculo_id_list->setProperty('order', 'id desc');

        $pedidos_saldo_veiculo_id_items = PedidoFrotas::getObjects($criteria_pedidos_veiculo_id_list);

        $this->pedidos_veiculo_id_list->addItems($pedidos_saldo_veiculo_id_items);

        $panel = new TElement('div');
        $panel->class = 'formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->pedidos_veiculo_id_list));

        $tab_622940daf9f3b->addContent([$panel]);


        // sssaçdpdddodaofodsa
        $tab_622940daf9f3b->appendPage(new TImage('fas:dollar-sign #F44336')."Saldo");

        $this->saldo_veiculo_id_list = new TQuickGrid;
        $this->saldo_veiculo_id_list->disableHtmlConversion();
        $this->saldo_veiculo_id_list->style = 'width:100%';
        $this->saldo_veiculo_id_list->disableDefaultClick();

        $column_tipo_transacao = $this->saldo_veiculo_id_list->addQuickColumn("Tipo Transação", 'tipo_transacao', 'left');
        $column_motivo_transacao = $this->saldo_veiculo_id_list->addQuickColumn("Motivo Transação", 'motivo_transacao', 'left');
        $column_data_transacao = $this->saldo_veiculo_id_list->addQuickColumn("Data Transação", 'data_transacao', 'left');
        $column_valor_transacao = $this->saldo_veiculo_id_list->addQuickColumn("Valor Transação", 'valor_transacao', 'right');
        $column_valor_transacao->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $column_data_transacao->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $this->saldo_veiculo_id_list->createModel();

        $criteria_saldo_veiculo_id_list = new TCriteria();
        $criteria_saldo_veiculo_id_list->add(new TFilter('veiculos_id', '=', $veiculos->id));

        $criteria_saldo_veiculo_id_list->setProperty('order', 'id desc');

        $veiculos_saldo_veiculo_id_items = SaldoVeiculo::getObjects($criteria_saldo_veiculo_id_list);

        $this->saldo_veiculo_id_list->addItems($veiculos_saldo_veiculo_id_items);

        $panel = new TElement('div');
        $panel->class = 'formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->saldo_veiculo_id_list));

        $tab_622940daf9f3b->addContent([$panel]);

        $tab_622940daf9f3b->appendPage(new TImage('fas:paperclip #607D8B')."Anexos");

        $this->anexos_veiculo_id_list = new TQuickGrid;
        $this->anexos_veiculo_id_list->disableHtmlConversion();
        $this->anexos_veiculo_id_list->style = 'width:100%';
        $this->anexos_veiculo_id_list->disableDefaultClick();

        $column_descricao_anexo = $this->anexos_veiculo_id_list->addQuickColumn("Anexo", 'descricao', 'left');

        $column_descricao_anexo->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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
        $this->anexos_veiculo_id_list->createModel();

        $criteria_anexos_veiculo_id_list = new TCriteria();
        $criteria_anexos_veiculo_id_list->add(new TFilter('veiculos_id', '=', $veiculos->id));

        $criteria_anexos_veiculo_id_list->setProperty('order', 'id desc');

        $veiculos_anexos_veiculo_id_items = AnexosVeiculo::getObjects($criteria_anexos_veiculo_id_list);

        $this->anexos_veiculo_id_list->addItems($veiculos_anexos_veiculo_id_items);

        $panel = new TElement('div');
        $panel->class = 'formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->anexos_veiculo_id_list));

        $tab_622940daf9f3b->addContent([$panel]);

      
        $tab_622940daf9f3b->appendPage(new TImage('fas:credit-card #ff0000')."Dispositivos");

        $this->dispositivos_list = new TQuickGrid;
        $this->dispositivos_list->disableHtmlConversion();
        $this->dispositivos_list->style = 'width:100%';
        $this->dispositivos_list->disableDefaultClick();

        $column_dispositivos_solicitados_id = $this->dispositivos_list->addQuickColumn("ID", 'id', 'left');
        $column_dispositivo_id = $this->dispositivos_list->addQuickColumn("Dispositivo", 'dispositivos_id', 'left');
        $column_finalidade_id = $this->dispositivos_list->addQuickColumn("Finalidade", 'dispositivos_id', 'left');
        $column_numerocartao = $this->dispositivos_list->addQuickColumn("Número do cartão / UID Tag", 'numerocartao', 'left');
        $column_datahora = $this->dispositivos_list->addQuickColumn("Data Hora", 'datasolicitacao', 'left');
        $column_status = $this->dispositivos_list->addQuickColumn("Status", 'status_dispositivos->descricao', 'left');
        $column_via = $this->dispositivos_list->addQuickColumn("Via", 'via', 'left');
        $column_rastreio = $this->dispositivos_list->addQuickColumn("Rastreio", 'rastreio', 'left');
        
        $column_dispositivo_id->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open('minierp');

            $value='Não informado!';    
            $obj = new Dispositivos($object->dispositivos_id);
            $value = $obj->descricao;
            return $value;

            TTransaction::close();

        });
        $column_finalidade_id->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open('minierp');

            $value='Não informado!';    
            $obj = new Dispositivos($object->dispositivos_id);
            $tipo_finalidade = new TipoFinalidade($obj->tipo_finalidade_id);
            if ($tipo_finalidade)
            {
                $value = $tipo_finalidade->descricao;
            }
            return $value;

            TTransaction::close();

        });
        $column_datahora->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y H:i:s');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });
        $this->dispositivos_list->createModel();

        $criteria_dispositivos = new TCriteria();
        $criteria_dispositivos->add(new TFilter('veiculos_id', '=', $veiculos->id));

        $criteria_dispositivos->setProperty('order', 'id desc');

        $veiculos_dispositivos_items = DispositivosSolicitados::getObjects($criteria_dispositivos);

        $this->dispositivos_list->addItems($veiculos_dispositivos_items);

        $panel = new TElement('div');
        $panel->class = 'formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->dispositivos_list));

        $tab_622940daf9f3b->addContent([$panel]);

        $tab_622940daf9f3b->appendPage(new TImage('fas:list-alt #f39c12')."Tabela Temparia");

        $row_temparia = $tab_622940daf9f3b->addFields([$tabela_temparia_list]);
        $row_temparia->layout = [' col-sm-12'];
      /*  $tab_622940daf9f3b->appendPage("Dispositivos");
*/
        $tab_6308050d6056b = new BootstrapFormBuilder('tab_6308050d6056b');
        $this->tab_6308050d6056b = $tab_6308050d6056b;

        $tab_6308050d6056b->setProperty('style', 'border:none; box-shadow:none;');

       // $tab_6308050d6056b->appendPage("Contas a pagar em aberto");

        $tab_6308050d6056b->addFields([new THidden('current_tab_tab_6308050d6056b')]);
        $tab_6308050d6056b->setTabFunction("$('[name=current_tab_tab_6308050d6056b]').val($(this).attr('data-current_page'));");

        //$row8 = $tab_6308050d6056b->addFields([$action2],[],[]);
     //   $row8->layout = ['col-sm-3','col-sm-3','col-sm-6'];
//$row9 = $tab_6308050d6056b->addFields([$bpagecontainer2]);
   //     $row9->layout = [' col-sm-12'];

    //    $tab_6308050d6056b->appendPage("Contas a receber em aberto");
       // $row10 = $tab_6308050d6056b->addFields([$action_gerar_extrato],[],[]);
     //  $row10->layout = ['col-sm-3','col-sm-3','col-sm-6'];

       // $row11 = $tab_6308050d6056b->addFields([$bpagecontainer_contas_em_aberto]);
      //  $row11->layout = [' col-sm-12'];

        $row12 = $tab_622940daf9f3b->addFields([$tab_6308050d6056b]);
    //    $row12->layout = [' col-sm-12'];

        $row13 = $this->form->addFields([$tab_622940daf9f3b]);
      //  $row13->layout = [' col-sm-12'];

        if(!empty($param['current_tab']))
        {
       //     $this->form->setCurrentPage($param['current_tab']);
        }

        if(!empty($param['current_tab_tab_622940daf9f3b']))
        {
      //      $this->tab_622940daf9f3b->setCurrentPage($param['current_tab_tab_622940daf9f3b']);
        }
        if(!empty($param['current_tab_tab_6308050d6056b']))
        {
        //    $this->tab_6308050d6056b->setCurrentPage($param['current_tab_tab_6308050d6056b']);
        }

        $btnPessoaFormOnEditAction = new TAction(['VeiculosForm', 'onEdit'],['key'=>$veiculos->id]);
        $btnPessoaFormOnEditLabel = new TLabel("Editar");

        $btnPessoaFormOnEdit = $this->form->addHeaderAction($btnPessoaFormOnEditLabel, $btnPessoaFormOnEditAction, 'fas:edit #2196F3'); 
        $btnPessoaFormOnEditLabel->setFontSize('12px'); 
        $btnPessoaFormOnEditLabel->setFontColor('#333'); 

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
$style = new TStyle('right-panel > .container-part');
        $style->width = '60% !important';

     //   $style = new TStyle('right-panel > .container-part[page-name=PessoaFormView]');
    //    $style->width = '80% !important';   
        $style->show(true);

    }

    public function onShow($param = null)
    {     

    }

}

