<?php

class PropostasFormView extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Propostas';
    private static $primaryKey = 'id';
    private static $formName = 'form_PropostasFormView';

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

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de propostas");

        $criteria_pessoa_id = new TCriteria();
        $criteria_estado_pedido_frotas_id = new TCriteria();
        $criteria_veiculos_id = new TCriteria();
        $criteria_motorista_entrada_id = new TCriteria();
        $criteria_motorista_retirada_id = new TCriteria();

        $id = new TEntry('id');
        $pedido_frotas_id = new TEntry('pedido_frotas_id');
        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa_id );
        $estado_pedido_frotas_id = new TDBCombo('estado_pedido_frotas_id', 'minierp', 'EstadoPedidoFrotas', 'id', '{nome}','id asc' , $criteria_estado_pedido_frotas_id );
        $veiculos_id = new TDBCombo('veiculos_id', 'minierp', 'Veiculos', 'id', '{placa}','id asc' , $criteria_veiculos_id );
        $data_cotacao = new TDate('data_cotacao');
        $data_previsao_entrega = new TDate('data_previsao_entrega');
        $obs = new TText('obs');
        $total_produtos_sem_desconto = new TNumeric('total_produtos_sem_desconto', '2', ',', '.' );
        $total_servicos_sem_desconto = new TNumeric('total_servicos_sem_desconto', '2', ',', '.' );
        $total_geral_sem_desconto = new TNumeric('total_geral_sem_desconto', '2', ',', '.' );
        $desconto_contratual = new TNumeric('desconto_contratual', '2', ',', '.' );
        $total_produtos_com_desconto = new TNumeric('total_produtos_com_desconto', '2', ',', '.' );
        $total_servicos_com_desconto = new TNumeric('total_servicos_com_desconto', '2', ',', '.' );
        $total_geral_com_desconto = new TNumeric('total_geral_com_desconto', '2', ',', '.' );
        $comentario_time_line = new BPageContainer();
        $comentario = new TText('comentario');
        $button_enviar = new TButton('button_enviar');
        $datahora_inicioservico = new TDateTime('datahora_inicioservico');
        $datahora_fimservico = new TDateTime('datahora_fimservico');
        $km = new TEntry('km');
        $responsavel_tecnico = new TEntry('responsavel_tecnico');
        $data_entrada_veiculo = new TDateTime('data_entrada_veiculo');
        $motorista_entrada_id = new TDBCombo('motorista_entrada_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_motorista_entrada_id );
        $data_retirada_veiculo = new TDateTime('data_retirada_veiculo');
        $motorista_retirada_id = new TDBCombo('motorista_retirada_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_motorista_retirada_id );


        $comentario_time_line->setId('b67eda83e27566');
        $comentario_time_line->hide();
        $button_enviar->addStyleClass('btn-default');
        $button_enviar->setImage('fas:paper-plane #FF0000');
        $km->setValue('NULL');
        $data_previsao_entrega->setValue('NULL');

        $button_enviar->setAction(new TAction([$this, 'onEnviar']), "Enviar");
        $comentario_time_line->setAction(new TAction(['ComentarioPropostaTimeLine', 'onShow']));

        $pessoa_id->enableSearch();
        $veiculos_id->enableSearch();
        $motorista_entrada_id->enableSearch();
        $motorista_retirada_id->enableSearch();
        $estado_pedido_frotas_id->enableSearch();

        $data_cotacao->setMask('dd/mm/yyyy');
        $data_previsao_entrega->setMask('dd/mm/yyyy');
        $datahora_fimservico->setMask('dd/mm/yyyy hh:ii');
        $data_entrada_veiculo->setMask('dd/mm/yyyy hh:ii');
        $data_retirada_veiculo->setMask('dd/mm/yyyy hh:ii');
        $datahora_inicioservico->setMask('dd/mm/yyyy hh:ii');

        $data_cotacao->setDatabaseMask('yyyy-mm-dd');
        $data_previsao_entrega->setDatabaseMask('yyyy-mm-dd');
        $datahora_fimservico->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_entrada_veiculo->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_retirada_veiculo->setDatabaseMask('yyyy-mm-dd hh:ii');
        $datahora_inicioservico->setDatabaseMask('yyyy-mm-dd hh:ii');

        $id->setEditable(false);
        $pessoa_id->setEditable(false);
        $veiculos_id->setEditable(false);
        $data_cotacao->setEditable(false);
        $pedido_frotas_id->setEditable(false);
        $desconto_contratual->setEditable(false);
        $estado_pedido_frotas_id->setEditable(false);
        $total_geral_sem_desconto->setEditable(false);
        $total_geral_com_desconto->setEditable(false);
        $total_produtos_sem_desconto->setEditable(false);
        $total_servicos_sem_desconto->setEditable(false);
        $total_produtos_com_desconto->setEditable(false);
        $total_servicos_com_desconto->setEditable(false);

        $id->setSize(100);
        $km->setSize('100%');
        $obs->setSize('100%', 70);
        $pessoa_id->setSize('100%');
        $data_cotacao->setSize(110);
        $veiculos_id->setSize('100%');
        $comentario->setSize('100%', 70);
        $pedido_frotas_id->setSize('100%');
        $datahora_fimservico->setSize(160);
        $data_entrada_veiculo->setSize(160);
        $data_previsao_entrega->setSize(110);
        $data_retirada_veiculo->setSize(160);
        $desconto_contratual->setSize('100%');
        $datahora_inicioservico->setSize(160);
        $responsavel_tecnico->setSize('100%');
        $comentario_time_line->setSize('100%');
        $motorista_entrada_id->setSize('100%');
        $motorista_retirada_id->setSize('100%');
        $estado_pedido_frotas_id->setSize('100%');
        $total_geral_sem_desconto->setSize('100%');
        $total_geral_com_desconto->setSize('100%');
        $total_produtos_sem_desconto->setSize('100%');
        $total_servicos_sem_desconto->setSize('100%');
        $total_produtos_com_desconto->setSize('100%');
        $total_servicos_com_desconto->setSize('100%');

        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $comentario_time_line->add($loadingContainer);

        $this->comentario_time_line = $comentario_time_line;

        $tab_67e6af8606113 = new BootstrapFormBuilder('tab_67e6af8606113');
        $this->tab_67e6af8606113 = $tab_67e6af8606113;
        $tab_67e6af8606113->setProperty('style', 'border:none; box-shadow:none;');

        $tab_67e6af8606113->appendPage("Dados proposta");

        $tab_67e6af8606113->addFields([new THidden('current_tab_tab_67e6af8606113')]);
        $tab_67e6af8606113->setTabFunction("$('[name=current_tab_tab_67e6af8606113]').val($(this).attr('data-current_page'));");

        $row1 = $tab_67e6af8606113->addFields([new TLabel("ID Proposta", null, '14px', null, '100%'),$id],[new TLabel("ID Pedido", null, '14px', null, '100%'),$pedido_frotas_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $tab_67e6af8606113->addFields([new TLabel("Pessoa:", null, '14px', null, '100%'),$pessoa_id],[new TLabel("Estado pedido:", null, '14px', null, '100%'),$estado_pedido_frotas_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $tab_67e6af8606113->addFields([new TLabel("Veiculos:", null, '14px', null, '100%'),$veiculos_id],[new TLabel("Data proposta:", null, '14px', null, '100%'),$data_cotacao]);
        $row3->layout = ['col-sm-6',' col-sm-6'];

        $row4 = $tab_67e6af8606113->addFields([new TLabel("Data previsao entrega:", null, '14px', null, '100%'),$data_previsao_entrega],[new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row4->layout = ['col-sm-6',' col-sm-6'];

        $row5 = $tab_67e6af8606113->addFields([new TFormSeparator("Valor Total dos itens", '#333', '18', '#eee')]);
        $row5->layout = ['col-sm-12'];

        $row6 = $tab_67e6af8606113->addFields([new TLabel("Total dos Produtos sem desconto:", null, '14px', null, '100%'),$total_produtos_sem_desconto],[new TLabel("Total dos Serviços sem desconto:", null, '14px', null, '100%'),$total_servicos_sem_desconto],[new TLabel("Total geral sem descontos:", null, '14px', null, '100%'),$total_geral_sem_desconto]);
        $row6->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row7 = $tab_67e6af8606113->addFields([],[],[new TLabel("(%) Desconto contratual:", '#FF0000', '14px', 'B', '100%'),$desconto_contratual]);
        $row7->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row8 = $tab_67e6af8606113->addFields([new TLabel("Total dos Produtos com desconto:", null, '14px', null, '100%'),$total_produtos_com_desconto],[new TLabel("Total dos Serviços com desconto:", null, '14px', null, '100%'),$total_servicos_com_desconto],[new TLabel("Total geral com desconto:", null, '14px', null, '100%'),$total_geral_com_desconto]);
        $row8->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row9 = $tab_67e6af8606113->addFields([new TFormSeparator("<BR>Comentários", '#333', '18', '#eee')]);
        $row9->layout = [' col-sm-12'];

        $row10 = $tab_67e6af8606113->addFields([$comentario_time_line]);
        $row10->layout = [' col-sm-12'];

        $row11 = $tab_67e6af8606113->addFields([new TFormSeparator("Escreva um comentário", '#333', '18', '#eee')]);
        $row11->layout = [' col-sm-12'];

        $row12 = $tab_67e6af8606113->addFields([$comentario,$button_enviar]);
        $row12->layout = [' col-sm-12'];

        $tab_67e6af8606113->appendPage("Registro inicio e fim do serviço");
        $row13 = $tab_67e6af8606113->addFields([new TLabel("Data e hora do inicio do serviço:", null, '14px', null, '100%'),$datahora_inicioservico],[new TLabel("Data e hora do fim do serviço:", null, '14px', null, '100%'),$datahora_fimservico]);
        $row13->layout = [' col-sm-6',' col-sm-6'];

        $row14 = $tab_67e6af8606113->addFields([new TLabel("Km:", null, '14px', null, '100%'),$km],[new TLabel("Responsável técnico:", null, '14px', null, '100%'),$responsavel_tecnico]);
        $row14->layout = ['col-sm-6','col-sm-6'];

        $row15 = $tab_67e6af8606113->addFields([new TFormSeparator("Dados da entrada e retirada do veículo", '#333', '18', '#eee')]);
        $row15->layout = [' col-sm-12'];

        $row16 = $tab_67e6af8606113->addFields([new TLabel("Data e hora da entrada do veiculo", null, '14px', null, '100%'),$data_entrada_veiculo],[new TLabel("Condutor do veículo:", null, '14px', null, '100%'),$motorista_entrada_id],[new TLabel("Data e hora da retirada do veiculo", null, '14px', null, '100%'),$data_retirada_veiculo],[new TLabel("Condutor do veículo", null, '14px', null, '100%'),$motorista_retirada_id]);
        $row16->layout = [' col-sm-2',' col-sm-4',' col-sm-2',' col-sm-4'];

        $row17 = $this->form->addFields([$tab_67e6af8606113]);
        $row17->layout = [' col-sm-12'];

        

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=PropostasFormView]');
        $style->width = '80% !important';   
        $style->show(true);

    }

    public  function onEnviar($param = null) 
    {
        /*try 
        {
              TTransaction::open(self::$database); // open a transaction

              $object = new ComentarioProposta(); // create an empty object //</blockLine>

              $data = $this->form->getData(); // get form data as array
              $object->fromArray( (array) $data); // load the object with data

              $objectcom = new stdClass();
              $objectcom->created_at      = date();
              $objectcom->comentario      = $object->comentario;
              $objectcom->propostas_id    = $object->id;
              $objectcom->system_users_id = TSession::getValue('userid');

             TForm::sendData('form_PropostasFormView', $objectcom);

             TTransaction::close();*/

             try {
                   TTransaction::open(self::$database); // open a transaction

                  $object = new Propostas(); // create an empty object //</blockLine>

                  $data = $this->form->getData(); // get form data as array
                  $object->fromArray( (array) $data); // load the object with data

                  $objectcom = new ComentarioProposta();
                  $objectcom->created_at      = date('Y-m-d H:i:s');
                  $objectcom->comentario      = $data->comentario;
                  $objectcom->propostas_id    = $object->id;
                  $objectcom->system_users_id = TSession::getValue('userid');
                  $objectcom->store();

                 TForm::sendData('form_PropostasFormView', $object);

                
                $this->comentario_time_line->unhide();

                 TTransaction::close();

            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }

/*

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }*/
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Propostas($key); // instantiates the Active Record 
                
                $param['id'] = $key;

                TSession::setValue('parametros',null);
                TSession::setValue('parametros',$param);

                                $this->comentario_time_line->unhide();

                 $criteria = new TCriteria();
                $criteria->add(new TFilter('propostas_id', '=', $object->id));
                $criteria->add(new TFilter('system_users_id', '<>', TSession::getValue('userid')));
                $criteria->add(new TFilter('leitura_dt', 'IS', NULL));

                $repo = new TRepository('ComentarioProposta');
                $com = $repo->load($criteria);

                if ($com)
                {
                    foreach ($com as $comm) {
                          $comm->leitura_dt=date('Y-m-d H:i:s');
                          $comm->store();
                    }
                }

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function onShow($param = null)
    {

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

