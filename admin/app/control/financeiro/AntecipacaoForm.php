<?php

use Adianti\Database\TTransaction;

class AntecipacaoForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'Antecipacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_AntecipacaoForm';

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
        $this->form->setFormTitle("Cadastro de antecipação");
        $this->form->setProperty('id', 'form_AntecipacaoForm');
        $this->form->setProperty('id', 'form_AntecipacaoForm');

        $criteria_forma_pagamento_id = new TCriteria();
        $criteria_itens_conta = new TCriteria();

    /*    $filterVar = "pessoa_id";
        $criteria_itens_conta->add(new TFilter('pessoa_id', '=', $filterVar)); 
        $filterVar = NULL;
        $criteria_itens_conta->add(new TFilter('dt_pagamento', 'is', $filterVar)); */

        $filterVar = TSession::getValue('ContaPagarEmAbertoSimpleListbuilder_datagrid_check');
        $criteria_itens_conta->add(new TFilter('id', 'in', $filterVar)); 

        if(!TSession::getValue('ContaPagarEmAbertoSimpleListbuilder_datagrid_check'))
        {
            new TMessage('info', 'Seleciona ao menos uma conta', new TAction(['ContaPagarEmAbertoSimpleList', 'onShow']));
            return true;
        }
        $data_antecipacao = new TDate('data_antecipacao');
        $id = new THidden('id');
        $forma_pagamento_id = new TDBCombo('forma_pagamento_id', 'minierp', 'FormaPagamento', 'id', '{nome}','nome asc' , $criteria_forma_pagamento_id );
        $obs = new TText('obs');
        $valor_bruto_total = new TNumeric('valor_bruto_total', '2', '.', ',' );
        $txcontrato = new TNumeric('txcontrato', '2', '.', ',' );
        $txadm = new TNumeric('txadm', '2', '.', ',' );
        $txbancaria = new TNumeric('txbancaria', '2', '.', ',' );
        $txantecipacao = new TNumeric('txantecipacao', '2', '.', ',' );
        $valor_liquido = new TNumeric('valor_liquido', '2', '.', ',' );
        $itens_conta = new TCheckList('itens_conta');
     //   $itens_conta->setEditable(false);

//        $itens_conta->setChangeAction(new TAction([$this, 'onRecalcularValores']));
      //  $itens_conta->setExitAction(new TAction([$this, 'onRecalcularValores']));



        $data_antecipacao->setMask('dd/mm/yyyy');
        $data_antecipacao->setDatabaseMask('yyyy-mm-dd');
        $forma_pagamento_id->enableSearch();
        $id->setSize(200);
        $txadm->setSize('100%');
        $obs->setSize('100%', 70);
        $txcontrato->setSize('100%');
        $txbancaria->setSize('100%');
        $data_antecipacao->setSize(110);
        $txantecipacao->setSize('100%');
        $valor_liquido->setSize('100%');
        $valor_bruto_total->setSize('100%');
        $forma_pagamento_id->setSize('100%');

        $itens_conta->setIdColumn('id');
         $itens_conta->setValue(TSession::getValue('ContaPagarEmAbertoSimpleListbuilder_datagrid_check'));

        $column_itens_conta_id = $itens_conta->addColumn('id', "Id", 'center' , '9%');
        $column_itens_conta_valor = $itens_conta->addColumn('valor', "Valor", 'center' , '9%');
        $column_itens_conta_valor_taxacontrato = $itens_conta->addColumn('valor_taxacontrato', "Valor taxacontrato", 'center' , '9%');
        $column_itens_conta_valor_taxaadm = $itens_conta->addColumn('valor_taxaadm', "Valor taxaadm", 'center' , '9%');
        $column_itens_conta_valor_taxabancaria = $itens_conta->addColumn('valor_taxabancaria', "Valor taxabancaria", 'center' , '9%');
        $column_itens_conta_valor_taxaantecip = $itens_conta->addColumn('valor_taxaantecip', "Valor taxaantecip", 'center' , '9%');
        $column_itens_conta_valor_total = $itens_conta->addColumn('valor_total', "Valor total", 'center' , '9%');
        $column_itens_conta_valor->setTransformer(function($value, $object, $row) 
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
           $column_itens_conta_valor_taxacontrato->setTransformer(function($value, $object, $row) 
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
           $column_itens_conta_valor_taxaadm->setTransformer(function($value, $object, $row) 
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
           $column_itens_conta_valor_taxabancaria->setTransformer(function($value, $object, $row) 
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
           $column_itens_conta_valor_taxaantecip->setTransformer(function($value, $object, $row) 
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
           $column_itens_conta_valor_total->setTransformer(function($value, $object, $row) 
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
        $itens_conta->setHeight(250);
        $itens_conta->makeScrollable();

        $itens_conta->fillWith('minierp', 'Conta', 'id', 'id asc' , $criteria_itens_conta);
     //   $itens_conta->setChangeAction(new TAction([$this, 'onRecalcularValores']));


        $row1 = $this->form->addFields([new TLabel("Data antecipacao:", null, '14px', null, '100%'),$data_antecipacao,$id],[new TLabel("Forma pagamento id:", null, '14px', null, '100%'),$forma_pagamento_id],[new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row1->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row2 = $this->form->addFields([new TLabel("Valor bruto total:", null, '14px', null, '100%'),$valor_bruto_total],[new TLabel("Txcontrato:", null, '14px', null, '100%'),$txcontrato],[new TLabel("Txadm:", null, '14px', null, '100%'),$txadm]);
        $row2->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row3 = $this->form->addFields([new TLabel("Txbancaria:", null, '14px', null, '100%'),$txbancaria],[new TLabel("Txantecipacao:", null, '14px', null, '100%'),$txantecipacao],[new TLabel("Valor liquido:", null, '14px', null, '100%'),$valor_liquido]);
        $row3->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

         $row4 = $this->form->addFields([new TFormSeparator("<br>Itens em aberto a serem antecipados", '#333', '18', '#eee')]);
        $row4->layout = [' col-sm-12'];
        
        $row5 = $this->form->addFields([$itens_conta]);
        $row5->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['AntecipacaoList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);
       

        parent::add($this->form);
    //    $this->form->setId('form_AntecipacaoForm');


    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Antecipacao(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

              //quitar as contas e gravar no conta antecipacao_id e os valores

            $repository = Conta::where('antecipacao_id', '=', $object->id);
            $repository->delete(); 

            if ($data->itens_conta) 
            {
                foreach ($data->itens_conta as $itens_conta_value) 
                {
                    $conta = new Conta;

                    $conta->antecipacao_id = $itens_conta_value;
                    $conta->antecipacao_id = $object->id;
                    $conta->store();
                }
            }

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('AntecipacaoList', 'onShow', $loadPageParam); 

                        TScript::create("Template.closeRightPanel();"); 

        }
        catch (Exception $e) // in case of exception
        {

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Antecipacao($key); // instantiates the Active Record 

                $object->itens_conta = Conta::where('antecipacao_id', '=', $object->id)->getIndexedArray('antecipacao_id', 'antecipacao_id');

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
        try {
            TTransaction::open('minierp');

            if (!isset($param['key'])) {
                throw new Exception('ID da pessoa não informado');
            }

            $pessoa = new Pessoa($param['key']);

            $selecionadas = TSession::getValue('ContaPagarEmAbertoSimpleListbuilder_datagrid_check');
            if (!$selecionadas) {
                TTransaction::close(); // Fechar antes de sair
                new TMessage('info', 'Selecione ao menos uma conta', new TAction(['ContaPagarEmAbertoSimpleList', 'onShow']));
                return;
            }

            $valor_bruto = 0;    
            $contas = Conta::where('id', 'in', $selecionadas)->load();
            foreach ($contas as $ct) {
                $valor_bruto += $ct->valor;
            }

            $objectant = new stdClass();
            $objectant->data_antecipacao   = date('d-m-Y');
            $objectant->forma_pagamento_id = 1;
            $objectant->valor_bruto_total  = $valor_bruto;
            $objectant->txcontrato         = $valor_bruto * ($pessoa->taxacontrato / 100);
            $objectant->txadm              = $valor_bruto * ($pessoa->taxaadm / 100);
            $objectant->txbancaria         = $pessoa->taxabancaria;
            $objectant->txantecipacao      = $valor_bruto * ($pessoa->taxaantecipacao / 100);
            $valor_liquido =  $valor_bruto - (
                                                $objectant->txcontrato + 
                                                $objectant->txadm + 
                                                $objectant->txbancaria + 
                                                $objectant->txantecipacao
                                            );
            $objectant->valor_liquido      = $valor_liquido;

            TForm::sendData('form_AntecipacaoForm', $objectant);
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public static function getFormName()
    {
        return self::$formName;
    }
    public static function onRecalcularValores($param)
    {
        try {
            TTransaction::open(self::$database);

            $valor_bruto_total = 0;
            $txcontrato = 0;
            $txadm = 0;
            $txbancaria = 0;
            $txantecipacao = 0;
            $valor_liquido = 0;

            $itens = $param['itens_conta'] ?? [];

            foreach ($itens as $conta_id) {
                $conta = new Conta($conta_id);
                $valor = $conta->valor;

                $valor_bruto_total += $valor;

                $pessoa = new Pessoa($conta->pessoa_id); // suposição que existe pessoa_id na conta
                $txcontrato += ($valor * ($pessoa->tx_contrato ?? 0)) / 100;
                $txadm += ($valor * ($pessoa->tx_administrativa ?? 0)) / 100;
                $txbancaria += ($pessoa->tx_bancaria_valor ?? 0); // valor fixo
                $txantecipacao += ($valor * ($pessoa->tx_antecipacao ?? 0)) / 100;
            }

            $valor_liquido = $valor_bruto_total - ($txcontrato + $txadm + $txbancaria + $txantecipacao);

            $retorno = [
                'valor_bruto_total' => number_format($valor_bruto_total, 2, ',', '.'),
                'txcontrato' => number_format($txcontrato, 2, ',', '.'),
                'txadm' => number_format($txadm, 2, ',', '.'),
                'txbancaria' => number_format($txbancaria, 2, ',', '.'),
                'txantecipacao' => number_format($txantecipacao, 2, ',', '.'),
                'valor_liquido' => number_format($valor_liquido, 2, ',', '.'),
            ];

            TTransaction::close();

            TForm::sendData(self::$formName, (object)$retorno);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

}

