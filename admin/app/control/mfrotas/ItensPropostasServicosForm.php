<?php

class ItensPropostasServicosForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'ItensPropostas';
    private static $primaryKey = 'id';
    private static $formName = 'form_ItensPropostasServicosForm';

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
        $this->form->setFormTitle("Cadastro de itens serviços propostas");
        $criteria_produto_id = new TCriteria(); 
        $criteria_familia_produto_id = new TCriteria();
        $criteria_produto_id->add(
            new TFilter('system_unit_id', 'IN',
                "(SELECT su.id FROM system_unit su 
                LEFT JOIN entidade e ON e.id = su.entidade_id 
                WHERE e.frotas = 1)"
            )
        );
        $criteria_produto_id->add(new TFilter('tipo_produto_id', '=', 2));

        $id = new THidden('id');
        if (TSession::getValue('utiliza_temparia')==1) {
            $descricao = new TText('descricao');
        } else {
           $descricao = new TEntry('descricao');
        }
        $propostas_id = new THidden('propostas_id');
        $tipo = new THidden('tipo');
        $qtde = new TNumeric('qtde', '2', ',', '.' );
        $valor = new TNumeric('valor', '2', ',', '.' );
        $valor_total = new TNumeric('valor_total', '2', ',', '.' );
        $perc_desconto = new TNumeric('perc_desconto', '2', ',', '.' );
        $qtdekmgarantia = new TEntry('qtdekmgarantia');
        $diasdegarantia = new TEntry('diasdegarantia');
        $uso = new TText('uso');
        $finalidade = new TText('finalidade');
        $aplicacao = new TText('aplicacao');

        $familia_produto_id = new TDBCombo('familia_produto_id', 'minierp', 'FamiliaProduto', 'id', '{nome}','nome asc' , $criteria_familia_produto_id );
        // if (TSession::getValue('utiliza_temparia')==1) {
        //     $produto_id = new TCombo('produto_id');
        // $produto_id->configureNoResultsQuickRegister(new TAction(['ProdutoSimpleFormSuiv', 'onQuickSave']), "Cadastrar", "fas:plus #69AA46", "btn-default");
        // $produto_id->setNoResultsMessage("Cadastre um novo serviço");
        // } else {
           $produto_id = new TDBUniqueSearch('produto_id', 'minierp', 'Produto', 'id', 'nome_com_familia','nome asc' , $criteria_produto_id );
           $produto_id->setMinLength(2);
           $produto_id->setFilterColumns(["nome"]);
        // }
        // $produto_id = new TDBUniqueSearch('produto_id', 'minierp', 'Produto', 'id', 'nome','nome asc' , $criteria_produto_id );
         $perc_desconto->setEditable(false);
        $valor_total->setEditable(false);

        $qtde->setExitAction(new TAction([$this,'onExitQuantidade']));
        $valor->setExitAction(new TAction([$this,'onExitValor']));
        $qtde->addValidation("Quantidade", new TRequiredValidator()); 
                $familia_produto_id->setChangeAction(new TAction([$this,'onChangefamilia_produto_id']));

                   $id_field = $familia_produto_id->getId();

        // injeta JS pra bloquear a UI assim que o usuário mudar o valor
        TScript::create("
            $(function() {
                $('#{$id_field}').on('change', function() {
                    if ($(this).val()) {
                        __adianti_block_ui('Carregando');
                    }
                });
            });
        ");
 if (TSession::getValue('utiliza_temparia')==1) {
            $familia_produto_id->addValidation("Grupo", new TRequiredValidator()); 
        }
//        $descricao->addValidation("Descrição", new TRequiredValidator()); 
      //  $valor_total->addValidation("Valor total", new TRequiredValidator()); 
        $produto_id->addValidation("Serviço", new TRequiredValidator()); 
          
        $descricao->setMaxLength(200);
        $tipo->setValue('2');
        $propostas_id->setValue(TSession::getValue('parametros')['id']);
  $familia_produto_id->enableSearch();
  if (TSession::getValue('utiliza_temparia')==1) {
  $produto_id->enableSearch();
    }

        $id->setSize(200);
        $tipo->setSize(200);
        $qtde->setSize('100%');
        $valor->setSize('100%'); 
        $descricao->setSize('100%');
        $propostas_id->setSize(200);
        $valor_total->setSize('100%');
        $perc_desconto->setSize('100%');
        $qtdekmgarantia->setSize('100%');
        $diasdegarantia->setSize('100%');
        $uso->setSize('100%', 70);
        $finalidade->setSize('100%', 70);
        $aplicacao->setSize('100%', 70);

                                 $produto_id->setSize('100%');
                                                 $familia_produto_id->setSize('100%');

        if (isset($param['key'])) {
            $familia_produto_id->setEditable(false);
            $produto_id->setEditable(false);
            $qtde->setEditable(false);
        } else {
            $familia_produto_id->setEditable(true);
            $produto_id->setEditable(true);
            $qtde->setEditable(true);
        }
        $tab_67410dd86de9d = new BootstrapFormBuilder('tab_67410dd86de9d');
        $this->tab_67410dd86de9d = $tab_67410dd86de9d;
        $tab_67410dd86de9d->setProperty('style', 'border:none; box-shadow:none;');

        $tab_67410dd86de9d->appendPage("Item serviço");

        $tab_67410dd86de9d->addFields([new THidden('current_tab_tab_67410dd86de9d')]);
        $tab_67410dd86de9d->setTabFunction("$('[name=current_tab_tab_67410dd86de9d]').val($(this).attr('data-current_page'));");

        if (TSession::getValue('utiliza_temparia')==2) {

        $produto_id->configureNoResultsQuickRegister(new TAction(['ServicoServicoSimpleForm', 'onQuickSave']), "Cadastrar", "fas:plus #69AA46", "btn-default");
        $produto_id->setNoResultsMessage("Cadastre um novo serviço");                                 
        }        
         if (TSession::getValue('utiliza_temparia')==1) {
            $row0 = $tab_67410dd86de9d->addFields([new TLabel("Serviço:*", '#FF0000', '14px', null, '100%'),$produto_id,$id,$propostas_id,$tipo]);
            $row0->layout = ['col-sm-12'];
         } else {
            $row0 = $tab_67410dd86de9d->addFields([new TLabel("Serviço:*", '#FF0000', '14px', null, '100%'),$produto_id],[$id,new TLabel("Descrição:", null, '14px', null, '100%'),$descricao,$propostas_id,$tipo]);
            $row0->layout = ['col-sm-6',' col-sm-6'];

        }
        $row1 = $tab_67410dd86de9d->addFields([new TLabel("Dias de garantia:", null, '14px', null, '100%'),$diasdegarantia],[new TLabel("Qtde km garantia:", null, '14px', null, '100%'),$qtdekmgarantia] );
        $row1->layout = ['col-sm-6',' col-sm-6'];
       
        $row3 = $tab_67410dd86de9d->addFields([new TLabel("Qtde em horas:*", '#FF0000', '14px', null, '100%'),$qtde], [new TLabel("Valor:*", '#FF0000', '14px', null, '100%'),$valor]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row2 = $tab_67410dd86de9d->addFields([new TLabel("Valor desconto:", null, '14px', null, '100%'),$perc_desconto], [new TLabel("Valor total:", null, '14px', null, '100%'),$valor_total]);
        $row2->layout = ['col-sm-6',' col-sm-6'];
         if (TSession::getValue('utiliza_temparia')==1) {
            $row3 = $tab_67410dd86de9d->addFields([new TLabel("Obs:", null, '14px', null, '100%'),$descricao]);
            $row3->layout = ['col-sm-12'];

        }

        $row90 = $this->form->addFields([$tab_67410dd86de9d]);
        $row90->layout = ['col-sm-12'];

         $tab_67410dd86de9d->appendPage("Descrição Detalhada / Uso / Finalidade / Aplicação");

        $row10 = $tab_67410dd86de9d->addFields([new TLabel("Uso:", null, '14px', null, '100%'),$uso]);
        $row10->layout = ['col-sm-12'];

        $row11 = $tab_67410dd86de9d->addFields([new TLabel("Finalidade:", null, '14px', null, '100%'),$finalidade]);      
        $row11->layout = ['col-sm-12'];

        $row12 = $tab_67410dd86de9d->addFields([new TLabel("Aplicação:", null, '14px', null, '100%'),$aplicacao]);
        $row12->layout = ['col-sm-12'];
        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        // $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        // $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['ItensPropostasServicosList', 'onShow']), 'fas:arrow-left #000000');
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

    }

    public static function onExitQuantidade($param = null)
    {
        try
        {
            $parseNumero = static function ($valor): float {
                if ($valor === null || $valor === '') {
                    return 0.0;
                }

                $s = trim((string) $valor);
                $s = preg_replace('/[^0-9.,\\-]/', '', $s);

                $posVirgula = strrpos($s, ',');
                $posPonto = strrpos($s, '.');

                if ($posVirgula !== false || $posPonto !== false) {
                    $sepDecimal = ($posVirgula !== false && $posVirgula > $posPonto) ? ',' : '.';
                    $partes = explode($sepDecimal, $s);
                    $decimal = array_pop($partes);
                    $inteiro = implode('', $partes);
                    $inteiro = str_replace([',', '.'], '', $inteiro);
                    $normalizado = $inteiro . '.' . $decimal;
                } else {
                    $normalizado = str_replace([',', '.'], '', $s);
                }

                return is_numeric($normalizado) ? (float) $normalizado : 0.0;
            };

            if ($parseNumero($param['qtde'] ?? 0) == 0.0)
            {
                $objecti = new stdClass();
                $objecti->valor = number_format(0, 2, ',', '.');
                $objecti->perc_desconto = number_format(0, 2, ',', '.');
                $objecti->valor_total = number_format(0, 2, ',', '.');

                TForm::sendData(self::$formName, $objecti);
                return;
            }

            $calc = self::calcularTotaisItem(
                $param['valor'] ?? 0,
                $param['qtde'] ?? 0,
                TSession::getValue('taxacontrato') ?? 0
            );

            $objecti = new stdClass();
            $objecti->perc_desconto = number_format($calc['desconto'], 2, ',', '.');
            $objecti->valor_total = number_format($calc['valor_total'], 2, ',', '.');

            TForm::sendData(self::$formName, $objecti);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    // public static function onExitQuantidade_ultima_versao_IA($param = null) 
    // {
    //     try 
    //     {
    //         $parseNumero = static function ($valor): float {
    //             if ($valor === null || $valor === '') {
    //                 return 0.0;
    //             }

    //             $s = trim((string) $valor);
    //             $s = preg_replace('/[^0-9.,\\-]/', '', $s);

    //             $posVirgula = strrpos($s, ',');
    //             $posPonto = strrpos($s, '.');

    //             if ($posVirgula !== false || $posPonto !== false) {
    //                 $sepDecimal = ($posVirgula !== false && $posVirgula > $posPonto) ? ',' : '.';
    //                 $partes = explode($sepDecimal, $s);
    //                 $decimal = array_pop($partes);
    //                 $inteiro = implode('', $partes);
    //                 $inteiro = str_replace([',', '.'], '', $inteiro);
    //                 $normalizado = $inteiro . '.' . $decimal;
    //             } else {
    //                 $normalizado = str_replace([',', '.'], '', $s);
    //             }

    //             return is_numeric($normalizado) ? (float) $normalizado : 0.0;
    //         };
    //         $valorUni = $parseNumero($param['valor'] ?? 0);
    //         $qtd = $parseNumero($param['qtde'] ?? 0);
    //         $subtotalCents = (int) round(($valorUni * $qtd) * 100, 0, PHP_ROUND_HALF_UP);

    //         // Calcula em centavos para eliminar divergencia de ponto flutuante
    //         $taxaContrato = $parseNumero(TSession::getValue('taxacontrato') ?? 0);
    //         $taxaBps = (int) round($taxaContrato * 100, 0, PHP_ROUND_HALF_UP); // ex.: 28,50% => 2850
    //         $fatorLiquidoBps = 10000 - $taxaBps;
    //         $valorTotalCents = (int) floor((($subtotalCents * $fatorLiquidoBps) + 5000) / 10000); // half-up
    //         $descontoCents = $subtotalCents - $valorTotalCents;

    //         $descontoExibicao = $descontoCents / 100;
    //         $valorTotalExibicao = $valorTotalCents / 100;

    //         $objecti = new stdClass();
    //         $objecti->perc_desconto = number_format($descontoExibicao, 2, ',', '.');
    //         $objecti->valor_total = number_format($valorTotalExibicao, 2, ',', '.');

    //         TForm::sendData(self::$formName, $objecti);

    //     }
    //     catch (Exception $e) 
    //     {
    //         new TMessage('error', $e->getMessage());    
    //     }
    // }

    //   public static function onExitQuantidade($param = null) 
    // {
    //     try 
    //     {
    //             //code here

    //         if(!empty($param['qtde']) && !empty($param['valor']))
    //         {
    //             $txcontrato = ((TSession::getValue('taxacontrato')/100)) ;
    //             $qtde = (double) str_replace(',', '.', str_replace('.', '', $param['qtde']));
    //             $valor = (double) str_replace(',', '.', str_replace('.', '', $param['valor']));

    //             $valor_total = $qtde * $valor ;
    //             $objecti = new stdClass();
    //             $objecti->valor_total = number_format($valor_total, 2, ',', '.');
    //             $perc_desconto = $valor_total * $txcontrato; // exemplo de desconto de 10%
    //             $objecti->perc_desconto = number_format( ($perc_desconto), 2, ',', '.'); // exemplo de desconto de 10%
    //             $objecti->valor_total = number_format( ($valor_total - $perc_desconto), 2, ',', '.');

    //             TForm::sendData(self::$formName, $objecti); 
    //         }   

    //     }
    //     catch (Exception $e) 
    //     {
    //         new TMessage('error', $e->getMessage());    
    //     }
    // }

    public static function onExitValor($param = null) 
    {
        try 
        {
            //code here
           self::onExitQuantidade($param);

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $parseNumero = static function ($valor): float {
                if ($valor === null || $valor === '') {
                    return 0.0;
                }

                $s = trim((string) $valor);
                $s = preg_replace('/[^0-9.,\\-]/', '', $s);

                $posVirgula = strrpos($s, ',');
                $posPonto = strrpos($s, '.');

                if ($posVirgula !== false || $posPonto !== false) {
                    $sepDecimal = ($posVirgula !== false && $posVirgula > $posPonto) ? ',' : '.';
                    $partes = explode($sepDecimal, $s);
                    $decimal = array_pop($partes);
                    $inteiro = implode('', $partes);
                    $inteiro = str_replace([',', '.'], '', $inteiro);
                    $normalizado = $inteiro . '.' . $decimal;
                } else {
                    $normalizado = str_replace([',', '.'], '', $s);
                }

                return is_numeric($normalizado) ? (float) $normalizado : 0.0;
            };

            $data = $this->form->getData();
            if ($parseNumero($data->qtde ?? 0) == 0.0)
            {
                $data->valor = number_format(0, 2, ',', '.');
                $data->perc_desconto = number_format(0, 2, ',', '.');
                $data->valor_total = number_format(0, 2, ',', '.');
                $this->form->setData($data);
            }

            $this->form->validate(); // validate form data

            $object = new ItensPropostas(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->propostas_id = TSession::getValue('parametros')['id'];
            $object->tipo = 2; // tipo 2 = servico
            $calc = self::calcularTotaisItem(
                $object->valor ?? 0,
                $object->qtde ?? 0,
                TSession::getValue('taxacontrato') ?? 0
            );

            $object->perc_desconto = $calc['desconto'];
            $object->valor_total = $calc['valor_total'];

              $ItemNovo = false ;

            if(!$data->id)
            {
                $ItemNovo = true;
                TSession::setValue('inseridoitem', true);
            }
            $object->store(); // save the object 

            if (TSession::getValue('garantia_dias') >0) {
                if ($object->diasdegarantia < TSession::getValue('garantia_dias')) {
                    throw new Exception("A garantia mínima é de ".TSession::getValue('garantia_dias')." dias. Verifique!");
                }
            }
            if (TSession::getValue('garantia_km') >0) {
                if ($object->qtdekmgarantia < TSession::getValue('garantia_km')) {
                    throw new Exception("A garantia mínima é de ".TSession::getValue('garantia_km')." km. Verifique!");
                }
            }
            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

             if ($ItemNovo)
            {
                TSession::setValue('inseridoitem', true);

            } else {
                   $loadPageParam["propostas_id"] = TSession::getValue('parametros')['id'];
                   if (TSession::getValue('old_item')->qtde<> $object->qtde) {
                      TSession::setValue('inseridoitem', true);
                   }
            }

            if (empty($loadPageParam["propostas_id"]))
            {
                $loadPageParam["propostas_id"] = TSession::getValue('parametros')['id'];
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            $this->fireEvents($object);
            TTransaction::close(); // close the transaction
            $objectpro = PropostasForm::onRefreshTotais(['propostas_id' => TSession::getValue('parametros')['id']]);
            if ($objectpro)
            {
                TForm::sendData('form_PropostasForm', $objectpro, false, true, 300);
            }
/*

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('ItensPropostasProdutosList', 'onShow', $loadPageParam); 

*/
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('ItensPropostasServicosList', 'onShow', $loadPageParam);

                        TScript::create("Template.closeRightPanel();"); 

        }
        catch (Exception $e) // in case of exception
        {
            $this->fireEvents($this->form->getData());

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

                $object = new ItensPropostas($key); // instantiates the Active Record 
              //  TSession::setValue('inseridoitem', null);
                TSession::setValue('old_item',null);
                TSession::setValue('old_item',$object);
                $this->form->setData($object); // fill the form 
                                $this->fireEvents($object);

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
        TSession::setValue('inseridoitem', null);
        TSession::setValue('tipo', 2);
    } 

    public static function getFormName()
    {
        return self::$formName;
    }

    //</hideLine> <addUserFunctionsCode/>

     public static function onChangefamilia_produto_id($param)
    {
        try
        {
            TSession::setValue('familia_produto_id', null);
            TSession::setValue('familia_produto_id', $param['familia_produto_id'] ?? null);
                    TSession::setValue('tipo', 2);

            if (!empty($param['key']))
            { 
                $criteria = new TCriteria();
                $criteria = TCriteria::create([
                    'familia_produto_id' => $param['familia_produto_id'],
                    'tipo_produto_id'    => 2,
                ]);                
                TDBCombo::reloadFromModel(self::$formName, 'produto_id', 'minierp', 'Produto', 'id', '{nome}', 'nome asc', $criteria, TRUE); 
                // TDBCombo::reloadFromModel(self::$formName, 'itens_pedido_frotas_pedido_frotas_produto_id_'.$field_id, 'minierp', 'Produto', 'id', '{nome}', 'nome asc', $criteria, TRUE); 
            } 
            else 
            { 
                TCombo::clearField(self::$formName, 'produto_id'); 
            }  
             TScript::create("__adianti_unblock_ui();");

        }
        catch (Exception $e)
        {
             TScript::create("__adianti_unblock_ui();");
            new TMessage('error', $e->getMessage());
        }
    }

   
     public static function fireEvents( $object )
    {
          if (TSession::getValue('utiliza_temparia')!=1) {
            return;
        }
        $obj = new stdClass;
        if(is_object($object) && get_class($object) == 'stdClass')
        {
            if(isset($object->familia_produto_id))
            {
                $value = $object->familia_produto_id;

                $obj->familia_produto_id = $value;
            }
            if(isset($object->produto_id))
            {
                $value = $object->produto_id;

                $obj->produto_id = $value;
            }
        }
        elseif(is_object($object))
        {
            if(isset($object->familia_produto_id))
            {
                $value = $object->familia_produto_id;

                $obj->familia_produto_id = $value;
            }
            if(isset($object->produto_id))
            {
                $value = $object->produto_id;

                $obj->produto_id = $value;
            }
        }
        TForm::sendData(self::$formName, $obj);
    }  

    function normalizaDecimal($valor)
{
    if (!is_string($valor)) return $valor;

    $s = trim($valor);

    // remove tudo que não for número, ponto, vírgula ou sinal
    $s = preg_replace('/[^0-9\.,\-]/', '', $s);

    // qual é o último separador (decimal)?
    $posVirg = strrpos($s, ',');
    $posPonto = strrpos($s, '.');

    $decimalSep = null;
    if ($posVirg !== false || $posPonto !== false) {
        $decimalSep = ($posVirg > $posPonto) ? ',' : '.';
    }

    if ($decimalSep) {
        $partes = explode($decimalSep, $s);
        $decimal = array_pop($partes); // tudo após o último separador
        $inteiro = implode('', $partes); // junta o resto

        // remove qualquer separador que sobrou na parte inteira
        $inteiro = str_replace([',', '.'], '', $inteiro);

        return $inteiro . '.' . $decimal; // mantém só ponto na decimal
    }

    // sem decimal: remove separadores e retorna inteiro
    return str_replace([',', '.'], '', $s);
}

    private static function calcularTotaisItem($valor, $qtde, $taxaContrato): array
    {
        $parseNumero = static function ($valor): float {
            if ($valor === null || $valor === '') {
                return 0.0;
            }

            $s = trim((string) $valor);
            $s = preg_replace('/[^0-9.,\\-]/', '', $s);

            $posVirgula = strrpos($s, ',');
            $posPonto = strrpos($s, '.');

            if ($posVirgula !== false || $posPonto !== false) {
                $sepDecimal = ($posVirgula !== false && $posVirgula > $posPonto) ? ',' : '.';
                $partes = explode($sepDecimal, $s);
                $decimal = array_pop($partes);
                $inteiro = implode('', $partes);
                $inteiro = str_replace([',', '.'], '', $inteiro);
                $normalizado = $inteiro . '.' . $decimal;
            } else {
                $normalizado = str_replace([',', '.'], '', $s);
            }

            return is_numeric($normalizado) ? (float) $normalizado : 0.0;
        };

        $valorUni = $parseNumero($valor);
        $qtdNum = $parseNumero($qtde);
        $subtotalCents = (int) round(($valorUni * $qtdNum) * 100, 0, PHP_ROUND_HALF_UP);

        $taxa = $parseNumero($taxaContrato);
        $taxaBps = (int) round($taxa * 100, 0, PHP_ROUND_HALF_UP);
        $descontoCents = (int) floor((($subtotalCents * $taxaBps) + 5000) / 10000);
        $valorTotalCents = $subtotalCents - $descontoCents;

        return [
            'desconto' => $descontoCents / 100,
            'valor_total' => $valorTotalCents / 100,
        ];
    }

}

