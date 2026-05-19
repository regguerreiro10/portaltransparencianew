<?php

//<fileHeader>

//</fileHeader>
$__suivClientPath = __DIR__ . '/../../service/SuivClient.php';
if (!file_exists($__suivClientPath)) {
    throw new Exception('SuivClient.php não encontrado em: ' . $__suivClientPath);
}
require_once $__suivClientPath;

if (!class_exists(\app\service\SuivClient::class)) {
    throw new Exception('Classe \app\service\SuivClient não foi carregada');
}

use app\service\SuivClient; // importa o nome curto

use Adianti\Database\TTransaction;

class ItensPropostasSimpleList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private static $database = 'minierp';
    private static $activeRecord = 'ItensPropostas';
    private static $primaryKey = 'id';
    private static $formName = 'form_ItensPropostasFormList';
    private $limit = 20;

    use BuilderMasterDetailFieldListTrait;

    //<classProperties>

    //</classProperties>

    private static function validarBloqueioSuivDaProposta($propostaId)
    {
        $divergencias = ItensPropostas::getDivergenciasSuivPorProposta($propostaId);

        if (!empty($divergencias)) {
            throw new Exception("Não é permitido pré-aprovar/aprovar proposta com item acima da tabela SUIV.\n" . implode("\n", $divergencias));
        }
    }

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param)
    {
        parent::__construct();
        // creates the form

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setProperty('style', 'width: 100%;');

          if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }
//$this->form->setProperty('style', 'width: 100%; margin: auto;');

        if (isset($param['tipoacao'])) {
            TSession::setValue('tipoacao', $param['tipoacao']);
        } 
      

        if (TSession::getValue('tipoacao') == 'Aprovar')
        {
            $this->form->setFormTitle("Aprovar Itens da proposta");
        } elseif (TSession::getValue('tipoacao') == 'Reprovar')
        {
            $this->form->setFormTitle("Reprovar Itens da proposta");
        } elseif (TSession::getValue('tipoacao') == 'PreAprovar'){
            $this->form->setFormTitle("Pré Aprovar Itens da proposta");
        }
        // define the form title
        //$this->form->setFormTitle("Aprovar/Reprovar Itens da proposta");
        $this->limit = 0;

        //<onBeginPageCreation>

        //</onBeginPageCreation>
       $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id = new TCriteria();
        $filterVar = TSession::getValue('entidade');
        $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->add(new TFilter('saldo_entidade_contrato_id', 'in', "(SELECT id FROM saldo_entidade_contrato WHERE  deleted_at is null AND entidade_id in (SELECT id FROM entidade WHERE id = '{$filterVar}'))")); 
        $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->add(new TFilter('status_saldo_departamento_id', '<>', StatusSaldoDepartamento::ENCERRADO));
        $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->add(new TFilter('status_saldo_departamento_id', '<>', StatusSaldoDepartamento::ANULADO));

        $id = new THidden('id');
        $pedido_frotas_id = new TEntry('pedido_frotas_id');
        $propostas_id = new TEntry('propostas_id');
        $justificativa = new TText('justificativa');

        $id->setEditable(false);
        $propostas_id->setEditable(false);
        $pedido_frotas_id->setEditable(false);
        $id->setSize(100);
        $justificativa->setSize('100%', 70);
        $TAlert = new TAlert('danger', "Atenção: valores em vermelho e negrito indicam divergência entre o preço da tabela e o valor informado. Verifique antes de Pre-aprovar/Aprovar.");
        $total_produtos = new TNumeric('total_produtos', '2', ',', '.' );
        $total_servicos = new TNumeric('total_servicos', '2', ',', '.' );
        $total_produtos_servicos = new TNumeric('total_produtos_servicos', '2', ',', '.' );
        if (TSession::getValue('tipoacao') <> 'Reprovar') {
            $dotacao_pedido_frotas_pedido_frotas_id = new THidden('dotacao_pedido_frotas_pedido_frotas_id[]');
            $dotacao_pedido_frotas_pedido_frotas___row__id = new THidden('dotacao_pedido_frotas_pedido_frotas___row__id[]');
            $dotacao_pedido_frotas_pedido_frotas___row__data = new THidden('dotacao_pedido_frotas_pedido_frotas___row__data[]');
            $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id = new TDBCombo('dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id[]', 'minierp', 'SaldoDepartamento', 'id', '{departamento_unit->name} - {numero_documento_empenho} - {valor_empenho_formatado} - {tipos} - {status_saldo_departamento}', 'numero_documento_empenho asc', $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id);
            $dotacao_pedido_frotas_pedido_frotas_saldo_atual = new TNumeric('dotacao_pedido_frotas_pedido_frotas_saldo_atual[]', '2', ',', '.' );
            $dotacao_pedido_frotas_pedido_frotas_valor = new TNumeric('dotacao_pedido_frotas_pedido_frotas_valor[]', '2', ',', '.' );
            $this->fieldList_6881430e7887f = new TFieldList();
            $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->setChangeAction(new TAction([$this,'onCalcValor']));

            $this->fieldList_6881430e7887f->addField(null, $dotacao_pedido_frotas_pedido_frotas_id, []);
            $this->fieldList_6881430e7887f->addField(null, $dotacao_pedido_frotas_pedido_frotas___row__id, ['uniqid' => true]);
            $this->fieldList_6881430e7887f->addField(null, $dotacao_pedido_frotas_pedido_frotas___row__data, []);
            $this->fieldList_6881430e7887f->addField(new TLabel("Saldo departamento: *", '#FF0000', '14px', null), $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id, ['width' => '33%']);
            $this->fieldList_6881430e7887f->addField(new TLabel("Saldo atual:", null, '14px', null), $dotacao_pedido_frotas_pedido_frotas_saldo_atual, ['width' => '33%']);
            $this->fieldList_6881430e7887f->addField(new TLabel("Valor: *", '#FF0000', '14px', null), $dotacao_pedido_frotas_pedido_frotas_valor, ['width' => '33%']);

            $this->fieldList_6881430e7887f->width = '100%';
            $this->fieldList_6881430e7887f->setFieldPrefix('dotacao_pedido_frotas_pedido_frotas');
            $this->fieldList_6881430e7887f->name = 'fieldList_6881430e7887f';

            $this->criteria_fieldList_6881430e7887f = new TCriteria();
            $this->default_item_fieldList_6881430e7887f = new stdClass();

            $this->form->addField($dotacao_pedido_frotas_pedido_frotas_id);
            $this->form->addField($dotacao_pedido_frotas_pedido_frotas___row__id);
            $this->form->addField($dotacao_pedido_frotas_pedido_frotas___row__data);
            $this->form->addField($dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id);
            $this->form->addField($dotacao_pedido_frotas_pedido_frotas_saldo_atual);
            $this->form->addField($dotacao_pedido_frotas_pedido_frotas_valor);

            $this->fieldList_6881430e7887f->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");
            $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->addValidation("dotação orçamentária", new TRequiredListValidator()); 
            $dotacao_pedido_frotas_pedido_frotas_valor->addValidation("Valor", new TRequiredListValidator()); 

            $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->enableSearch();
            $dotacao_pedido_frotas_pedido_frotas_saldo_atual->setEditable(false);
            $dotacao_pedido_frotas_pedido_frotas_valor->setSize('100%');
            $dotacao_pedido_frotas_pedido_frotas_saldo_atual->setSize('100%');
            $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->setSize(400);
        }
        $total_produtos->setEditable(false);
        $total_servicos->setEditable(false);
        $total_produtos_servicos->setEditable(false);

        $propostas_id->setSize('100%');
        $pedido_frotas_id->setSize('100%');
        $total_produtos->setSize('100%');
        $total_servicos->setSize('100%');
        $total_produtos_servicos->setSize('100%');

        //<onBeforeAddFieldsToForm>
        $row0 = $this->form->addFields([$TAlert]);
        $row0->layout = [' col-sm-12'];

        $row01 = $this->form->addFields([new TLabel("ID Pedido", null, '14px', null, '100%'),$pedido_frotas_id],[new TLabel("ID Proposta", null, '14px', null, '100%'),$propostas_id]);
        $row01->layout = ['col-sm-6','col-sm-6'];
        //</onBeforeAddFieldsToForm>
        $row1 = $this->form->addFields([$id,new TLabel("Justificativa:", null, '14px', null, '100%'),$justificativa]);
        $row1->layout = [' col-sm-12'];
        
        $row3 = $this->form->addFields([new TLabel("Total de Produtos", null, '14px', null, '100%'),$total_produtos],[new TLabel("Total de Serviços", null, '14px', null, '100%'),$total_servicos], [new TLabel("Total Geral", null, '14px', null, '100%'),$total_produtos_servicos]);
        $row3->layout = ['col-sm-4','col-sm-4','col-sm-4'];
        if (TSession::getValue('tipoacao') <> 'Reprovar') {
            $row4 = $this->form->addFields([new TFormSeparator("<br>"."Dotação Orçamentária *", '#FF0000', '18', '#eee')]);
            $row4->layout = [' col-sm-12'];

            $row5 = $this->form->addFields([$this->fieldList_6881430e7887f]);
            $row5->layout = [' col-sm-12'];
        }

        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onaction = $this->form->addAction("Voltar", new TAction([$this, 'onAction']), 'fas:arrow-left #000000');
        $this->btn_onaction = $btn_onaction;
 
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "ID", 'center' , '70px');
        $column_propostas_id = new TDataGridColumn('propostas_id', "ID Proposta", 'left');
        $column_tipo_transformed = new TDataGridColumn('tipo', "Tipo", 'left');
        $column_produto_id = new TDataGridColumn('produto->nome', "Produto/Serviço", 'left');
        $column_descricao = new TDataGridColumn('descricao', "Descrição", 'left');
        $column_tipo_pecas = new TDataGridColumn('tipo_pecas->descricao', "Tipo Peças", 'left');
        $column_qtde = new TDataGridColumn('qtde', "Qtde", 'left');
        $column_valor_transformed = new TDataGridColumn('valor', "Valor Unitário", 'left');
        $column_desconto_transformed = new TDataGridColumn('perc_desconto', "Desconto", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_total', "Valor total", 'left');
        $column_estado_pedido_frotas_id_transformed = new TDataGridColumn('estado_pedido_frotas_id', "Estado Proposta", 'left');
        $column_created_at_transformed = new TDataGridColumn('created_at', "Data criação", 'left');
        $column_valor_temparia_transformed = new TDataGridColumn('valor_total', "Hrs/Valor Temparia", 'left');
        $column_qtde->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if ($object->tipo==2)
            {
                return SuivClient::convertDecimalHoursToTimeString($value);
            }
            else
            {
                return $value;
            }
        });
         
          $column_valor_temparia_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (!TTransaction::getDatabase()) {
                TTransaction::open(self::$database);
                $close = true;
            } else {
                $close = false;
            }
            if ($object->tipo==1)
            {
                if (ItensPropostas::isBloqueioValorTempariaAtivo()) {
                    $produto = Produto::find($object->produto_id);
                    $mens = '';
                    $preco_formatado = '-';
                    $valorReferencia = 0;

                    if ($produto) {
                        $valorReferencia = (float) $produto->suiv_preco_peca;

                        if ($valorReferencia <= 0) {
                            $valorReferencia = (float) $produto->preco_venda;
                        }
                    }

                    if ($valorReferencia > 0) {
                        $preco_formatado = number_format($valorReferencia, 2, ',', '.');

                        if ((float) $object->valor > $valorReferencia) {
                            $mens = "<span style='color:red; font-weight: bold;'>R$ {$preco_formatado}</span>";
                        } else {
                            $mens = "R$ {$preco_formatado}";
                        }
                    }

                    if ($close) {
                        TTransaction::close();
                    }

                    return $mens ?: $preco_formatado;
                }

                $taxa = Entidade::where('id','=',TSession::getValue('entidade'))
                                ->load();
                if ($taxa) {
                $taxacontrato = $taxa[0]->taxacontrato/100;
                }
                else {
                    $taxacontrato = 0;
                }

                $value = $object->valor - ($object->valor *  $taxacontrato);

                $produto = Produto::find($object->produto_id);
                $mens = '';
                $valorReferencia = 0;

                if ($produto) {
                    $valorReferencia = (float) $produto->preco_venda;

                    if ($valorReferencia <= 0) {
                        $valorReferencia = (float) $produto->suiv_preco_peca;
                    }
                }

                if ($valorReferencia > 0 &&  $value > $valorReferencia ) {
                    $preco_formatado = number_format($valorReferencia, 2, ',', '.');
                    $mens = "<span style='color:red; font-weight: bold;'>R$ {$preco_formatado}</span>";
                } else {
                        $preco_formatado = $valorReferencia > 0 ? number_format($valorReferencia, 2, ',', '.') : '-';
                }

                if ($close) {
                    TTransaction::close();
                }

                return $mens ?: $preco_formatado;
            } else
            {             
                $produto = Produto::find($object->produto_id);
                $mens = '';

                if (ItensPropostas::isBloqueioValorTempariaAtivo()) {
                    if ($produto &&  $object->qtde > $produto->suiv_tempo_mao_obra_id ) {
                        $preco_formatado = $produto->suiv_tempo_servico;
                        $mens = "<span style='color:red; font-weight: bold;'>{$preco_formatado}</span>";
                    } else {
                        $preco_formatado =  $produto->suiv_tempo_servico;
                    }
                } else {
                    if ($produto &&  $object->qtde > $produto->suiv_tempo_mao_obra_id ) {
                        $preco_formatado = $produto->suiv_tempo_servico;
                        $mens = "<span style='color:red; font-weight: bold;'>{$preco_formatado}</span>";
                    } else {
                        $preco_formatado =  $produto->suiv_tempo_servico;
                    }
                }

                if ($close) {
                    TTransaction::close();
                }

                return $mens ?: $preco_formatado;
            }

        });
        $column_tipo_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
             if ($object->tipo == 1) {
                return "<span style='background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 8px; font-weight: bold;'>Produto</span>";
            } else {
                return "<span style='background-color: #2196F3; color: white; padding: 2px 8px; border-radius: 8px; font-weight: bold;'>Serviço</span>";
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

        $column_estado_pedido_frotas_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
             TTransaction::open(self::$database);
          $estado_pedido_frotas = EstadoPedidoFrotas::find($object->estado_pedido_frotas_id);
            TTransaction::close();
           if (!$estado_pedido_frotas) {
                return '';
            }
            return "<span class='label label-default' style='width:240px; background-color:{$estado_pedido_frotas->cor}'> {$estado_pedido_frotas->nome} <span>";
           

        });

        $column_created_at_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
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

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        //<onBeforeColumnsCreation>

        //</onBeforeColumnsCreation>

        $this->builder_datagrid_check_all = new TCheckButton('builder_datagrid_check_all');
        $this->builder_datagrid_check_all->setIndexValue('on');
        $this->builder_datagrid_check_all->onclick = "Builder.checkAll(this)";
        $this->builder_datagrid_check_all->style = 'cursor:pointer';
        $this->builder_datagrid_check_all->setProperty('class', 'filled-in');
        $this->builder_datagrid_check_all->id = 'builder_datagrid_check_all';

        $label = new TLabel('');
        $label->style = 'margin:0';
        $label->class = 'checklist-label';
        $this->builder_datagrid_check_all->after($label);
        $label->for = 'builder_datagrid_check_all';

        $this->builder_datagrid_check = $this->datagrid->addColumn( new TDataGridColumn('builder_datagrid_check', $this->builder_datagrid_check_all, 'center',  '1%') );

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_propostas_id);
        $this->datagrid->addColumn($column_tipo_transformed);
        $this->datagrid->addColumn($column_produto_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_tipo_pecas);
        $this->datagrid->addColumn($column_qtde);
        $this->datagrid->addColumn($column_valor_transformed);
        $this->datagrid->addColumn($column_desconto_transformed);
        $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column_valor_temparia_transformed);
        $this->datagrid->addColumn($column_estado_pedido_frotas_id_transformed);
      //  $this->datagrid->addColumn($column_created_at_transformed);

        //<onAfterColumnsCreation>

        //</onAfterColumnsCreation>

        //<onAfterActionsCreation>

        //</onAfterActionsCreation>

        // create the datagrid model
        $this->datagrid->createModel();

        $panel = new TPanelGroup();
       // $panel->setProperty('style', 'width: 80%; margin: auto;'); // <- AQUI aumenta o painel inteiro
$panel->setProperty('style', 'width: 100%;');

        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        //<onAfterHeaderActionsCreation>

        //</onAfterHeaderActionsCreation>

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        //<onAfterPageCreation>

        //</onAfterPageCreation>

        parent::add($this->form);
        parent::add($panel);
        $style = new TStyle('right-panel > .container-part');
        $style->width = '60% !important';
      //  $style = new TStyle('right-panel > .container-part[page-name=ItensPropostasFormList]');
    //    $style->width = '100% !important';   
        $style->show(true);

    }

    // public function onSave($param)
    // {
    //     try {
    //         TTransaction::open('minierp'); // ajuste o banco de dados
 
          
    //         $object = new PedidoFrotas(TSession::getValue('idpedido'));
    //         $data = $this->form->getData(); // get form data as array
    //         $object->fromArray( (array) $data); // load the object with data
    //         $pedidoId = $data->pedido_frotas_id;
    //         $propostaId = $data->propostas_id;

    //         if (empty($data->justificativa)) {
    //             throw new Exception('Justificativa é obrigatória.');
    //         }

    //         $itensSelecionadas = TSession::getValue('ItensPropostasSimpleListbuilder_datagrid_check');

    //         if (empty($itensSelecionadas) || !is_array($itensSelecionadas) || count($itensSelecionadas) <= 0) {
    //             throw new Exception('Nenhum item foi selecionado para clicar no botão salvar.');
    //         }
    //         $this->form->validate(); // validate form data

    //         $loadPageParam = [];

    //         if(!empty($param['target_container']))
    //         {
    //             $loadPageParam['target_container'] = $param['target_container'];
    //         }
    //         $loadPageParam["pedido_frotas_id"] = TSession::getValue('idpedido');

    //      /*   $data = $this->datagrid_form->getData(); // pega os dados do form
    //         $checked_items = $data->builder_datagrid_check ?? [];

    //         if (empty($checked_items)) {
    //             throw new Exception('Nenhum item selecionado.');
    //         }*/

    //          // Agora salva as redes selecionadas novamente
    //         $itensSelecionadas = TSession::getValue('ItensPropostasSimpleListbuilder_datagrid_check');
    //         $contar_itensselecionados = 0;
    //         if ($itensSelecionadas && is_array($itensSelecionadas)) {
    //             foreach ($itensSelecionadas as $item_id) {
    //                 $item = new ItensPropostas($item_id);
    //                 // Exemplo: alterar status ou marcar como aprovado
    //                 if (TSession::getValue('tipoacao') == 'Aprovar') {
    //                     $item->estado_pedido_frotas_id = EstadoPedidoFrotas::APROVADO;
    //                 } elseif (TSession::getValue('tipoacao') == 'Reprovar') {
    //                     $item->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
    //                 } elseif (TSession::getValue('tipoacao') == 'PreAprovar') {
    //                     $item->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
    //                 }
    //                 $item->store();
    //                 $contar_itensselecionados++;
    //             }
    //             if (TSession::getValue('tipoacao') == 'PreAprovar') {

    //                   //verifica se selecionou todos os itens para gravar a data de aprovacao e encerrar as propostas e tbm as pre aprovacoes e aprovacoes

    //                 $object = new PedidoFrotas(TSession::getValue('idpedido'));
    //                 $object->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
    //                 $object->store();



    //                 // Define estado da proposta conforme ação
    //                 $proposta = new Propostas(TSession::getValue('idproposta'));
    //                 $proposta->obs = $object->obs;
    //                 $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
    //                 $proposta->store();

    //                 // Históricos
    //                 $aprovador = AprovadorFrotas::where('system_users_id', '=', TSession::getValue('userid'))->load();

    //                 foreach ($aprovador as $aprovadores) {
    //                     $histPedido = new PedidoFrotasHistorico();
    //                     $histPedido->pedido_frotas_id = $object->id;
    //                     $histPedido->aprovador_frotas_id = $aprovadores->id;
    //                     $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
    //                     $histPedido->data_operacao = date('Y-m-d H:i:s');
    //                     $histPedido->obs = $object->obs;
    //                     $histPedido->store();


    //                     $histProposta = new PropostasHistorico();
    //                     $histProposta->propostas_id = $proposta->id;
    //                     $histProposta->aprovador_frotas_id =$aprovadores->id;
    //                     $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
    //                     $histProposta->data_historico = date('Y-m-d H:i:s');
    //                     $histProposta->obs = $object->obs;
    //                     $histProposta->store();
    //                     break;
    //                 }
    //             } elseif (TSession::getValue('tipoacao') == 'Aprovar') {
    //                 // Atualiza o pedido
    //                  $object = new PedidoFrotas(TSession::getValue('idpedido'));
    //                  $object->valor_total_proposta    = 0;
    //                  $object->valor_total    = 0;
    //                  $object->valor_desconto_proposta = 0;
    //                  $object->valor_liquido_proposta  = 0;
    //                  $object->store();

    //                 // Define estado da proposta conforme ação
    //                 $proposta = new Propostas(TSession::getValue('idproposta'));
    //                 $proposta->obs = $object->obs;
    //                 $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::APROVADO;
    //                 $proposta->store();

    //                  $propostasaprovadas = Propostas::where('pedido_frotas_id', '=', TSession::getValue('idpedido'))
    //                                                 ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
    //                                                 ->load(); 
    //                 if ($propostasaprovadas) {
    //                     foreach ($propostasaprovadas as $proposta) {
    //                         //buscar os itens aprovados da proposta
    //                         $itensAprovados = ItensPropostas::where('propostas_id', '=', $proposta->id)
    //                             ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
    //                             ->load();
    //                         if ($itensAprovados) {
    //                             // Adiciona os novos itens ao pedido
    //                             // Verifica se existem itens para adicionar
    //                             foreach ($itensAprovados as $item) {
    //                             if ($item->estado_pedido_frotas_id==EstadoPedidoFrotas::APROVADO) {
    //                            /*     $taxa = TaxasPessoa::where('system_unit_id','=',TSession::getValue('idunit'))->load();
    //                                 if ($taxa) {
    //                                    $taxacontrato = $taxa[0]->taxacontrato/100;
    //                                 }
    //                                 else {
    //                                     $taxacontrato = 0;
    //                                 }*/
                                    
    //                                 $object = new PedidoFrotas(TSession::getValue('idpedido'));
    //                                 $object->estado_pedido_frotas_id = EstadoPedidoFrotas::APROVADO;
    //                                 $object->valor_total += ($item->valor*$item->qtde); //*$taxacontrato);
    //                                 $object->valor_total_proposta += ($item->valor*$item->qtde); //*$taxacontrato);
    //                                 $object->valor_desconto_proposta += $item->perc_desconto; //*$taxacontrato);
    //                                 $object->valor_liquido_proposta += ($item->valor_total - $item->valor_desconto_proposta);
    //                                 $object->store();
    //                                 }
    //                             }
    //                         }
    //                     }

    //                 }

    //                 // Históricos
    //                 $aprovador = AprovadorFrotas::where('system_users_id', '=', TSession::getValue('userid'))->load();

    //                 foreach ($aprovador as $aprovadores) {
    //                     $histPedido = new PedidoFrotasHistorico();
    //                     $histPedido->pedido_frotas_id = $object->id;
    //                     $histPedido->aprovador_frotas_id = $aprovadores->id;
    //                     $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
    //                     $histPedido->data_operacao = date('Y-m-d H:i:s');
    //                     $histPedido->obs = $object->obs;
    //                     $histPedido->store();


    //                     $histProposta = new PropostasHistorico();
    //                     $histProposta->propostas_id = $proposta->id;
    //                     $histProposta->aprovador_frotas_id =$aprovadores->id;
    //                     $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
    //                     $histProposta->data_historico = date('Y-m-d H:i:s');
    //                     $histProposta->obs = $object->obs;
    //                     $histProposta->store();
    //                     break;
    //                 }
    //                 //atualizar os itens do pedido
    //                 $this->AtualizarItensPedido(TSession::getValue('idpedido'), TSession::getValue('idproposta'));

    //                 // Criar manutenção com base na proposta aprovada
    //                 if (TSession::getValue('tipoacao') == 'Aprovar') {
    //                     $itens = ItensPropostas::where('propostas_id', '=', $proposta->id)
    //                     ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
    //                     ->load();
    //                     foreach ($itens as $item) {
    //                         $add = false;
    //                         $data_garantia = null;

    //                         if ($item->qtdekmgarantia > 0) {
    //                             $km_atual = $object->km;
    //                             $media_km_dia = 50;
    //                             $km_faltante = $item->qtdekmgarantia - $km_atual;

    //                             if ($km_faltante > 0 && $media_km_dia > 0) {
    //                                 $dias_estimados = ceil($km_faltante / $media_km_dia);
    //                                 $data_garantia = date('Y-m-d', strtotime("+$dias_estimados days"));
    //                                 $add = true;
    //                             }
    //                         }

    //                         if ($item->diasdegarantia > 0) {
    //                             $data_garantia = date('Y-m-d', strtotime($item->created_at . " +{$item->diasdegarantia} days"));
    //                             $add = true;
    //                         }

    //                         if ($add) {

    //                             // Verifica se já existe uma manutenção para o item
    //                             $manutencaoExistente = ManutencaoGarantia::where('pedido_frotas_id', '=', $object->id)
    //                                 ->where('propostas_id', '=', $proposta->id)
    //                                 ->where('ativo', '=', 'S')
    //                                 ->where('produto_id', '=', $item->produto_id)
    //                                 ->load();

    //                             if ($manutencaoExistente) {
    //                              } else {
    //                                 $manutencao = new ManutencaoGarantia();
    //                                 $manutencao->itens_propostas_id = $item->id;
    //                                 $manutencao->veiculos_id = $object->veiculos_id;
    //                                 $manutencao->pedido_frotas_id = $object->id;
    //                                 $manutencao->propostas_id = $proposta->id;
    //                                 $manutencao->created_at = date('Y-m-d H:i:s');
    //                                 $manutencao->tipo = $item->tipo;
    //                                 $manutencao->km_manutencao = $item->qtdekmgarantia;
    //                                 $manutencao->dias_garantia = $item->diasdegarantia;
    //                                 $manutencao->datagarantia = $data_garantia;
    //                                 $manutencao->descricao = $item->descricao;
    //                                 $manutencao->produto_id = $item->produto_id;
    //                                 $manutencao->obs = $object->obs;
    //                                 $manutencao->qtde = $item->qtde;
    //                                 $manutencao->ativo = 'S';
    //                                 $manutencao->ciclos_manutencao = $item->ciclos;
    //                                 $manutencao->tbo_horas = $item->tbo_horas;
    //                                 $manutencao->tbo_ciclos = $item->tbo_ciclos;
    //                                 $manutencao->tsn_horas = $item->tsn_horas;
    //                                 $manutencao->tso_horas = $item->tso_horas;
    //                                 $manutencao->csn_ciclos = $item->csn_ciclos;
    //                                 $manutencao->cso_ciclos = $item->cso_ciclos;
    //                                 $manutencao->store();
    //                               } 
    //                             }
    //                     }
    //                 }

    //             } elseif (TSession::getValue('tipoacao') == 'Reprovar') {
    //               if ($contar_itensselecionados == TSession::getValue('contador_itens')) 
    //               {    
    //                     // Define estado da proposta conforme ação
    //                     $proposta = new Propostas(TSession::getValue('idproposta'));
    //                     $proposta->obs = $object->obs;
    //                     $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
    //                     $proposta->store();

               
    //                     // Históricos
    //                     $aprovador = AprovadorFrotas::where('system_users_id', '=', TSession::getValue('userid'))->load();

    //                     foreach ($aprovador as $aprovadores) 
    //                     {
    //                         $histPedido = new PedidoFrotasHistorico();
    //                         $histPedido->pedido_frotas_id = $object->id;
    //                         $histPedido->aprovador_frotas_id = $aprovadores->id;
    //                         $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
    //                         $histPedido->data_operacao = date('Y-m-d H:i:s');
    //                         $histPedido->obs = $object->obs;
    //                         $histPedido->store();


    //                         $histProposta = new PropostasHistorico();
    //                         $histProposta->propostas_id = $proposta->id;
    //                         $histProposta->aprovador_frotas_id =$aprovadores->id;
    //                         $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
    //                         $histProposta->data_historico = date('Y-m-d H:i:s');
    //                         $histProposta->obs = $object->obs;
    //                         $histProposta->store();
    //                         break;
    //                     }
    //                 }
    //             }

    //         }
    //         if (TSession::getValue('tipoacao') <> 'Reprovar') {
    //             $valor = 0;
    //             $dotacao_pedido_frotas_pedido_frotas_items = $this->storeItems(
    //                 'DotacaoPedidoFrotas',
    //                 'pedido_frotas_id',
    //                 $object,
    //                 $this->fieldList_6881430e7887f,
    //                 function($masterObject, $detailObject) use (&$valor) {
    //                     $valor += $detailObject->valor;
    //                     $detailObject->propostas_id = $masterObject->propostas_id;
    //                 },
    //                 $this->criteria_fieldList_6881430e7887f
    //             );
    //             $valor = (float) $valor;
    //             $total_produtos_servicos = (float) TSession::getValue('total_produtos_servicos');
    //             $valor = round($valor, 2);
    //             $total_produtos_servicos = round($total_produtos_servicos, 2);

    //             if ($valor > ($total_produtos_servicos)) {
    //                 throw new Exception('Valor total da dotação orçamentária não pode ser maior que o valor total do pedido.');
    //             } elseif ($valor < $total_produtos_servicos) {
    //                 throw new Exception('Valor total da dotação orçamentária não pode ser menor que o valor total do pedido.');
    //             }
    //         }
    //         if (in_array(TSession::getValue('tipoacao'), ['PreAprovar', 'Aprovar'])) {

    //             $propostaId = $proposta->id;

    //             // Pegar todos os itens da proposta
    //             $itensAprovados = ItensPropostas::where('propostas_id', '=', $propostaId)->load();

    //             $todositens = 0;

    //             if ($itensAprovados) {
    //                 foreach ($itensAprovados as $item) {
    //                     if (!in_array($item->estado_pedido_frotas_id, [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PREAPROVADO])) {
    //                         $todositens = 1;
    //                     }
    //                 }
    //             }

    //             // Se todos os itens estão aprovados ou preaprovados
    //             if ($todositens <= 0) {
    //                 $object = new PedidoFrotas(TSession::getValue('idpedido'));
    //                 $object->data_aprovacao = date('Y-m-d H:i:s');
    //                 $object->store();
              

    //                     $propostasDoPedido = Propostas::where('pedido_frotas_id', '=', $object->id)->load();

    //                     foreach ($propostasDoPedido as $p) {
    //                         // Reprova todas as outras propostas aguardando, exceto a atual
    //                         if ($p->id != $propostaId && $p->estado_pedido_frotas_id == EstadoPedidoFrotas::AGUARDANDO) {

    //                             $p->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
    //                             $p->obs = 'Reprovada automaticamente após aprovação de outra proposta';
    //                             $p->store();

    //                             // Salva histórico da reprovação
    //                             $histReprovada = new PropostasHistorico();
    //                             $histReprovada->propostas_id = $p->id;
    //                             $histReprovada->aprovador_frotas_id = $aprovadores->id ?? null;
    //                             $histReprovada->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
    //                             $histReprovada->data_historico = date('Y-m-d H:i:s');
    //                             $histReprovada->obs = 'Reprovada automaticamente após aprovação de outra proposta';
    //                             $histReprovada->store();
    //                         }
    //                     }
    //             }
        
    //         }

          
           
    //         // Limpa a sessão de itens selecionados
    //         TTransaction::close();


    //         if (TSession::getValue('tipoacao') == 'Aprovar') {
    //            new TMessage('info', 'Itens marcados foram salvos com sucesso.');
    //         } elseif (TSession::getValue('tipoacao') == 'Reprovar') {
    //             new TMessage('info', 'Itens marcados foram reprovados com sucesso.');
    //         } elseif (TSession::getValue('tipoacao') == 'PreAprovar') {
    //             new TMessage('info', 'Itens marcados foram pré-aprovados com sucesso.');
                
    //         }
           

      
    //         TSession::setValue('tipoacao', null);
    //         // TTransaction::close();

    //         TApplication::loadPage('PedidoFrotasList', 'onReload');
    //         TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);
    //    //     new TMessage('info', 'Aprovação da proposta realizada com sucesso!');
    //         TScript::create("Template.closeRightPanel();"); 

    //     } catch (Exception $e) {
    //         new TMessage('error', $e->getMessage()); // shows the exception error message

    //         $objectpro = new stdClass();
    //         $objectpro->pedido_frotas_id = $pedidoId;
    //         $objectpro->propostas_id = $propostaId;
    //         $objectpro->total_produtos = $data->total_produtos;
    //         $objectpro->total_servicos = $data->total_servicos;
    //         $objectpro->justificativa = $data->justificativa;
    //         $objectpro->total_produtos_servicos = $data->total_produtos_servicos;


    //         $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

    //            //code here
    //            $detailObject->valor = str_replace(',', '', $detailObject->valor);
    //            $detailObject->saldo_atual = str_replace(',', '', $detailObject->saldo_atual);
    //            $detailObject->propostas_id = $masterObject->propostas_id;

    //         }, $this->criteria_fieldList_6881430e7887f); 
    //         TForm::sendData('form_ItensPropostasFormList', $objectpro);        }
    // }

    public function onSave($param)
    {
        try {
            TTransaction::open('minierp'); // abre transação
         

            $object = new PedidoFrotas(TSession::getValue('idpedido'));
            $data   = $this->form->getData(); // get form data as array
            $object->fromArray((array) $data); // load the object with data
            $this->validarSaldosSelecionados($data->dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id ?? []);
            $this->validarValoresPorSaldoDepartamento(
                $data->dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id ?? [],
                $data->dotacao_pedido_frotas_pedido_frotas_valor ?? [],
                (int) ($data->pedido_frotas_id ?? 0)
            );

            $pedidoId   = $data->pedido_frotas_id;
            $propostaId = $data->propostas_id;

            if (in_array(TSession::getValue('tipoacao'), ['PreAprovar', 'Aprovar'])) {
                self::validarBloqueioSuivDaProposta($propostaId);
            }

            // if (TSession::getValue('idunit')==26) // jiparana
            // {
            //     throw new Exception('Aprovação de proposta não permitida.
            //     O limite de crédito do órgão foi excedido.
            //     Para prosseguir, entre em contato com o setor financeiro.');
            // }

            if (empty($data->justificativa)) {
                throw new Exception('Justificativa é obrigatória.');
            }

            $itensSelecionadas = TSession::getValue('ItensPropostasSimpleListbuilder_datagrid_check');

            if (empty($itensSelecionadas) || !is_array($itensSelecionadas) || count($itensSelecionadas) <= 0) {
                throw new Exception('Nenhum item foi selecionado para clicar no botão salvar.');
            }

            

            $this->form->validate(); // validate form data

            $loadPageParam = [];

            if (!empty($param['target_container'])) {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = TSession::getValue('idpedido');

            // Agora salva as redes selecionadas novamente
            $itensSelecionadas        = TSession::getValue('ItensPropostasSimpleListbuilder_datagrid_check');
            $contar_itensselecionados = 0;

            if ($itensSelecionadas && is_array($itensSelecionadas)) {
                foreach ($itensSelecionadas as $item_id) {
                    $item = new ItensPropostas($item_id);

                    if (TSession::getValue('tipoacao') == 'Aprovar') {
                        $item->estado_pedido_frotas_id = EstadoPedidoFrotas::APROVADO;
                    } elseif (TSession::getValue('tipoacao') == 'Reprovar') {
                        $item->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                    } elseif (TSession::getValue('tipoacao') == 'PreAprovar') {
                        $item->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
                    }

                    $item->store();
                    $contar_itensselecionados++;
                }

                if (TSession::getValue('tipoacao') == 'PreAprovar') {

                    //verifica se selecionou todos os itens para gravar a data de aprovacao e encerrar as propostas e tbm as pre aprovacoes e aprovacoes

                    $object = new PedidoFrotas(TSession::getValue('idpedido'));
                    $object->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
                    $object->store();

                    // Define estado da proposta conforme ação
                    $proposta                         = new Propostas(TSession::getValue('idproposta'));
                    $proposta->obs                    = $object->obs;
                    $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
                    $proposta->store();

                    // Históricos
                    $aprovador = AprovadorFrotas::where('system_users_id', '=', TSession::getValue('userid'))->load();

                    foreach ($aprovador as $aprovadores) {
                        $histPedido                       = new PedidoFrotasHistorico();
                        $histPedido->pedido_frotas_id      = $object->id;
                        $histPedido->aprovador_frotas_id   = $aprovadores->id;
                        $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                        $histPedido->data_operacao         = date('Y-m-d H:i:s');
                        $histPedido->obs                   = $object->obs;
                        $histPedido->store();

                        $histProposta                       = new PropostasHistorico();
                        $histProposta->propostas_id          = $proposta->id;
                        $histProposta->aprovador_frotas_id   = $aprovadores->id;
                        $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                        $histProposta->data_historico        = date('Y-m-d H:i:s');
                        $histProposta->obs                   = $object->obs;
                        $histProposta->store();
                        break;
                    }
                } elseif (TSession::getValue('tipoacao') == 'Aprovar') {
                    // Atualiza o pedido
                    $object                         = new PedidoFrotas(TSession::getValue('idpedido'));
                    $object->valor_total_proposta    = 0;
                    $object->valor_total             = 0;
                    $object->valor_desconto_proposta = 0;
                    $object->valor_liquido_proposta  = 0;
                    $object->store();

                    // Define estado da proposta conforme ação
                    $proposta                         = new Propostas(TSession::getValue('idproposta'));
                    $proposta->obs                    = $object->obs;
                    $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::APROVADO;
                    $proposta->store();

                    $propostasaprovadas = Propostas::where('pedido_frotas_id', '=', TSession::getValue('idpedido'))
                        ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
                        ->load();
                    if ($propostasaprovadas) {
                        foreach ($propostasaprovadas as $proposta) {
                            //buscar os itens aprovados da proposta
                            $itensAprovados = ItensPropostas::where('propostas_id', '=', $proposta->id)
                                ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
                                ->load();
                            if ($itensAprovados) {
                                // Adiciona os novos itens ao pedido
                                foreach ($itensAprovados as $item) {
                                    if ($item->estado_pedido_frotas_id == EstadoPedidoFrotas::APROVADO) {
                                        $object                                = new PedidoFrotas(TSession::getValue('idpedido'));
                                        $object->estado_pedido_frotas_id       = EstadoPedidoFrotas::APROVADO;
                                        $object->valor_total                  += ($item->valor * $item->qtde);
                                        $object->valor_total_proposta         += ($item->valor * $item->qtde);
                                        $object->valor_desconto_proposta      += $item->perc_desconto;
                                        $object->valor_liquido_proposta       += ($item->valor_total - $item->valor_desconto_proposta);
                                        $object->store();
                                    }
                                }
                            }
                        }
                    }

                    // Históricos
                    $aprovador = AprovadorFrotas::where('system_users_id', '=', TSession::getValue('userid'))->load();

                    foreach ($aprovador as $aprovadores) {
                        $histPedido                         = new PedidoFrotasHistorico();
                        $histPedido->pedido_frotas_id        = $object->id;
                        $histPedido->aprovador_frotas_id     = $aprovadores->id;
                        $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                        $histPedido->data_operacao           = date('Y-m-d H:i:s');
                        $histPedido->obs                     = $object->obs;
                        $histPedido->store();

                        $histProposta                         = new PropostasHistorico();
                        $histProposta->propostas_id            = $proposta->id;
                        $histProposta->aprovador_frotas_id     = $aprovadores->id;
                        $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                        $histProposta->data_historico          = date('Y-m-d H:i:s');
                        $histProposta->obs                     = $object->obs;
                        $histProposta->store();
                        break;
                    }

                    //atualizar os itens do pedido
                    $this->AtualizarItensPedido(TSession::getValue('idpedido'), TSession::getValue('idproposta'));

                    // Criar manutenção com base na proposta aprovada
                    if (TSession::getValue('tipoacao') == 'Aprovar') {
                        $itens = ItensPropostas::where('propostas_id', '=', $proposta->id)
                            ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
                            ->load();
                        foreach ($itens as $item) {
                            $add           = false;
                            $data_garantia = null;

                            if ($item->qtdekmgarantia > 0) {
                                $km_atual      = $object->km;
                                $media_km_dia  = 50;
                                $km_faltante   = $item->qtdekmgarantia - $km_atual;

                                if ($km_faltante > 0 && $media_km_dia > 0) {
                                    $dias_estimados = ceil($km_faltante / $media_km_dia);
                                    $data_garantia  = date('Y-m-d', strtotime("+$dias_estimados days"));
                                    $add            = true;
                                }
                            }

                            if ($item->diasdegarantia > 0) {
                                $data_garantia = date('Y-m-d', strtotime($item->created_at . " +{$item->diasdegarantia} days"));
                                $add           = true;
                            }

                            if ($add) {

                                // Verifica se já existe uma manutenção para o item
                                $manutencaoExistente = ManutencaoGarantia::where('pedido_frotas_id', '=', $object->id)
                                    ->where('propostas_id', '=', $proposta->id)
                                    ->where('ativo', '=', 'S')
                                    ->where('produto_id', '=', $item->produto_id)
                                    ->load();

                                if ($manutencaoExistente) {
                                    // não faz nada
                                } else {
                                    $manutencao                   = new ManutencaoGarantia();
                                    $manutencao->itens_propostas_id = $item->id;
                                    $manutencao->veiculos_id        = $object->veiculos_id;
                                    $manutencao->pedido_frotas_id   = $object->id;
                                    $manutencao->propostas_id       = $proposta->id;
                                    $manutencao->created_at         = date('Y-m-d H:i:s');
                                    $manutencao->tipo               = $item->tipo;
                                    $manutencao->km_manutencao      = $item->qtdekmgarantia;
                                    $manutencao->dias_garantia      = $item->diasdegarantia;
                                    $manutencao->datagarantia       = $data_garantia;
                                    $manutencao->descricao          = $item->descricao;
                                    $manutencao->produto_id         = $item->produto_id;
                                    $manutencao->obs                = $object->obs;
                                    $manutencao->qtde               = $item->qtde;
                                    $manutencao->ativo              = 'S';
                                    $manutencao->ciclos_manutencao  = $item->ciclos;
                                    $manutencao->tbo_horas          = $item->tbo_horas;
                                    $manutencao->tbo_ciclos         = $item->tbo_ciclos;
                                    $manutencao->tsn_horas          = $item->tsn_horas;
                                    $manutencao->tso_horas          = $item->tso_horas;
                                    $manutencao->csn_ciclos         = $item->csn_ciclos;
                                    $manutencao->cso_ciclos         = $item->cso_ciclos;
                                    $manutencao->store();
                                }
                            }
                        }
                    }

                } elseif (TSession::getValue('tipoacao') == 'Reprovar') {
                    if ($contar_itensselecionados == TSession::getValue('contador_itens')) {
                        // Define estado da proposta conforme ação
                        $proposta                         = new Propostas(TSession::getValue('idproposta'));
                        $proposta->obs                    = $object->obs;
                        $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                        $proposta->store();

                        // Históricos
                        $aprovador = AprovadorFrotas::where('system_users_id', '=', TSession::getValue('userid'))->load();

                        foreach ($aprovador as $aprovadores) {
                            $histPedido                         = new PedidoFrotasHistorico();
                            $histPedido->pedido_frotas_id        = $object->id;
                            $histPedido->aprovador_frotas_id     = $aprovadores->id;
                            $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                            $histPedido->data_operacao           = date('Y-m-d H:i:s');
                            $histPedido->obs                     = $object->obs;
                            $histPedido->store();

                            $histProposta                         = new PropostasHistorico();
                            $histProposta->propostas_id            = $proposta->id;
                            $histProposta->aprovador_frotas_id     = $aprovadores->id;
                            $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                            $histProposta->data_historico          = date('Y-m-d H:i:s');
                            $histProposta->obs                     = $object->obs;
                            $histProposta->store();
                            break;
                        }
                    }
                }

            }
                $valor = 0;

            if (TSession::getValue('tipoacao') <> 'Reprovar') {
                $dotacao_pedido_frotas_pedido_frotas_items = $this->storeItems(
                    'DotacaoPedidoFrotas',
                    'pedido_frotas_id',
                    $object,
                    $this->fieldList_6881430e7887f,
                    function ($masterObject, $detailObject) use (&$valor) {
                        $detailObject->valor       = $this->normalizarValorMonetario($detailObject->valor);
                        $detailObject->saldo_atual = $this->normalizarValorMonetario($detailObject->saldo_atual);
                        $valor                  += $detailObject->valor;
                        $detailObject->propostas_id = $masterObject->propostas_id;
                    },
                    $this->criteria_fieldList_6881430e7887f
                );
                $this->atualizarStatusSaldosUtilizados($dotacao_pedido_frotas_pedido_frotas_items);
                $valor                   = (float) $valor;
                $total_produtos_servicos = (float) TSession::getValue('total_produtos_servicos');
                $valor                   = round($valor, 2);
                $total_produtos_servicos = round($total_produtos_servicos, 2);

                if ($valor > ($total_produtos_servicos)) {
                    throw new Exception('Valor total da dotação orçamentária não pode ser maior que o valor total do pedido.');
                } elseif ($valor < $total_produtos_servicos) {
                    throw new Exception('Valor total da dotação orçamentária não pode ser menor que o valor total do pedido.');
                }
            }
         

            if ($valor==0) {
                throw new Exception('Nenhum item de dotação orçamentária foi informado.');
            } 

            if (in_array(TSession::getValue('tipoacao'), ['PreAprovar', 'Aprovar'])) {

                $propostaId = $proposta->id;

                // Pegar todos os itens da proposta
                $itensAprovados = ItensPropostas::where('propostas_id', '=', $propostaId)->load();

                $todositens = 0;

                if ($itensAprovados) {
                    foreach ($itensAprovados as $item) {
                        if (!in_array($item->estado_pedido_frotas_id, [EstadoPedidoFrotas::APROVADO, EstadoPedidoFrotas::PREAPROVADO])) {
                            $todositens = 1;
                        }
                    }
                }

                // Se todos os itens estão aprovados ou preaprovados
                if ($todositens <= 0) {
                    $object                 = new PedidoFrotas(TSession::getValue('idpedido'));
                    $object->data_aprovacao = date('Y-m-d H:i:s');
                    $object->store();

                    $propostasDoPedido = Propostas::where('pedido_frotas_id', '=', $object->id)->load();

                    foreach ($propostasDoPedido as $p) {
                        // Reprova todas as outras propostas aguardando, exceto a atual
                        if ($p->id != $propostaId && $p->estado_pedido_frotas_id == EstadoPedidoFrotas::AGUARDANDO) {

                            $p->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                            $p->obs                     = 'Reprovada automaticamente após aprovação de outra proposta';
                            $p->store();

                            // Salva histórico da reprovação
                            $histReprovada                         = new PropostasHistorico();
                            $histReprovada->propostas_id            = $p->id;
                            $histReprovada->aprovador_frotas_id     = $aprovadores->id ?? null;
                            $histReprovada->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                            $histReprovada->data_historico          = date('Y-m-d H:i:s');
                            $histReprovada->obs                     = 'Reprovada automaticamente após aprovação de outra proposta';
                            $histReprovada->store();
                        }
                    }
                }
            }

            // fecha transação só aqui, uma vez
            TTransaction::close();

            // === PARTE FORA DA TRANSAÇÃO (UI / navegação) ===

            if (TSession::getValue('tipoacao') == 'Aprovar') {
                new TMessage('info', 'Itens marcados foram salvos com sucesso.');
            } elseif (TSession::getValue('tipoacao') == 'Reprovar') {
                new TMessage('info', 'Itens marcados foram reprovados com sucesso.');
            } elseif (TSession::getValue('tipoacao') == 'PreAprovar') {
                new TMessage('info', 'Itens marcados foram pré-aprovados com sucesso.');
            }

            TSession::setValue('tipoacao', null);

            TApplication::loadPage('PedidoFrotasList', 'onReload');
            TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);
            TScript::create("Template.closeRightPanel();");

        } catch (Exception $e) {

            // garante rollback se a transação ainda estiver aberta
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }

            new TMessage('error', $e->getMessage()); // shows the exception error message

            // reenvia dados do form
            $objectpro                         = new stdClass();
            $objectpro->pedido_frotas_id        = $pedidoId ?? null;
            $objectpro->propostas_id            = $propostaId ?? null;
            $objectpro->total_produtos          = $data->total_produtos ?? null;
            $objectpro->total_servicos          = $data->total_servicos ?? null;
            $objectpro->justificativa           = $data->justificativa ?? null;
            $objectpro->total_produtos_servicos = $data->total_produtos_servicos ?? null;

            try {
                // se precisar recarregar itens da fieldlist, abre outra transação rápida
                TTransaction::open(self::$database);

                $this->fieldList_6881430e7887f_items = $this->loadItems(
                    'DotacaoPedidoFrotas',
                    'pedido_frotas_id',
                    $object,
                    $this->fieldList_6881430e7887f,
                    function ($masterObject, $detailObject, $objectItems) {

                        $detailObject->valor       = $this->normalizarValorMonetario($detailObject->valor);
                        $detailObject->saldo_atual = $this->normalizarValorMonetario($detailObject->saldo_atual);
                        $detailObject->propostas_id = $masterObject->propostas_id;

                    },
                    $this->criteria_fieldList_6881430e7887f
                );

                TTransaction::close();
            } catch (Exception $e2) {
                if (TTransaction::getDatabase()) {
                    TTransaction::rollback();
                }
            }

            TForm::sendData('form_ItensPropostasFormList', $objectpro);
        }
    }


//<generated-FormAction-onAction>
    public function onAction($param = null) 
    {
        try 
        {
            //code here
                TSession::setValue('tipoacao', null);

                    $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = TSession::getValue('idpedido');

            TApplication::loadPage('PedidoFrotasList', 'onReload');
            TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }//</end>
//</generated-FormAction-onAction>

//<generated-onEdit>
    public function onEdit( $param )//</ini>
    {
        try
        {
            $total_produtos = 0;
            $total_servicos = 0;
            $justificativa = '';
            // Garantir chave da proposta
            $propostaId = $param['key'] ?? $param['id'] ?? null;
            $pedidoId   = $param['pedido_frotas_id'] ?? null;
            $tipoacao   = $param['tipoacao'] ?? null;

            if (!$propostaId || !$pedidoId) {
                throw new Exception('Parâmetros inválidos para abrir os itens da proposta.');
            }

            // Sessão
            TSession::setValue('idpedido',   $pedidoId);
            TSession::setValue('idproposta', $propostaId);
            TSession::setValue('tipoacao',   $tipoacao);

            if ($propostaId !== null)
            {
                if (TTransaction::getDatabase()) {
                    // Já existe uma transação aberta
                } else {
                    // Não existe transação aberta
                   TTransaction::open(self::$database); // open a transaction
                }


                $object = ItensPropostas::where('propostas_id','=',$propostaId)->load(); // instantiates the Active Record //</blockLine>

             
                if (TSession::getValue('tipoacao') == 'Aprovar')
                {
                    $pedidofrotas = new PedidoFrotas(TSession::getValue('idpedido'));
                    if ($pedidofrotas->estado_pedido_frotas_id == EstadoPedidoFrotas::PREAPROVADO) {
                        //PEGAR JUSTIFICATIVA NO PEDIDO HISTORICO
                        $justificativa = PedidoFrotasHistorico::where('pedido_frotas_id', '=', TSession::getValue('idpedido'))
                            ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::PREAPROVADO)
                            ->orderBy('data_operacao', 'desc')
                            ->first();
                        if ($justificativa) {
                            $object[0]->justificativa = $justificativa->obs;
                        } else {
                            $object[0]->justificativa = '';
                        }
                    }

                }
                $objects = new PedidoFrotas(TSession::getValue('idpedido')); // instantiates the Active Record 

                $histPedido = PedidoFrotasHistorico::where('pedido_frotas_id', '=', TSession::getValue('idpedido'))
                    ->orderBy('data_operacao', 'desc')
                    ->last();
                if ($histPedido) {
                    $justificativa = $histPedido->obs;
                } else {
                    $justificativa = '';
                }
                if (TSession::getValue('tipoacao') <> 'Reprovar') {
                    $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $objects, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

                        //code herecur

                    }, $this->criteria_fieldList_6881430e7887f); 
                }
                
                $itens_propostas = ItensPropostas::where('propostas_id', '=', TSession::getValue('idproposta'))->load();
                $total_produtos = 0;
                $total_servicos = 0;
                $total_produtos_servicos = 0;
                if ($itens_propostas) {
                    foreach ($itens_propostas as $item) {
                        if ($item->tipo == 1) {
                            $total_produtos += $item->valor_total;
                        } elseif ($item->tipo == 2) {
                            $total_servicos += $item->valor_total;
                        }
                        $total_produtos_servicos = $total_produtos + $total_servicos;
                    }
                }
                $objProposta = new Propostas(TSession::getValue('idproposta'));

                $saldoDisponivelEmpenho = 0;
                $saldosEmpenho = SaldoDepartamento::where('departamento_unit_id', '=', $objProposta->departamento_unit_id)->load();

                if ($saldosEmpenho) {
                    foreach ($saldosEmpenho as $saldoEmpenho) {
                        $saldoDisponivelEmpenho += (float) ($saldoEmpenho->saldo_total ?? 0);
                    }
                }

                if ((float) $total_produtos_servicos > (float) $saldoDisponivelEmpenho) {
                    $totalFormatado = number_format((float) $total_produtos_servicos, 2, ',', '.');
                    $saldoFormatado = number_format((float) $saldoDisponivelEmpenho, 2, ',', '.');
                    // $unidadeNome = $object->departamento_unit_id->name ?? '';

                    throw new Exception("O total de produtos e serviços (R$ {$totalFormatado}) está acima do saldo disponível de empenho do Orgão (R$ {$saldoFormatado}).");
                }
                
                $objectpro = new stdClass();
                $objectpro->propostas_id = TSession::getValue('idproposta');
                $objectpro->pedido_frotas_id = TSession::getValue('idpedido');
                $objectpro->total_produtos = round(str_replace(',', '', $total_produtos),2);
                $objectpro->total_servicos = round(str_replace(',', '', $total_servicos),2);
                $objectpro->justificativa = $justificativa;
                $objectpro->total_produtos_servicos = round(str_replace(',', '',$total_produtos_servicos),2);
                TForm::sendData('form_ItensPropostasFormList', $objectpro);


                TSession::setValue('total_produtos', null);
                TSession::setValue('total_servicos', null);
                TSession::setValue('total_produtos_servicos', null);

                //</beforeSetDataAutoCode> //</blockLine>
                TSession::setValue(__CLASS__.'builder_datagrid_check', null);
                TTransaction::close(); // close the transaction 

                $this->form->setData($object); // fill the form //</blockLine>
                // $this->onReload();
                //</afterSetDataAutoCode> //</blockLine>
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
    }//</end>
//</generated-onEdit>

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
                $opened = self::safeOpen();

           
            // open a transaction with database 'minierp'
            TTransaction::open(self::$database);

            // creates a repository for ItensPropostas
            $repository = new TRepository(self::$activeRecord);
            // creates a criteria
            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'tipo,id';    
            }
            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            $session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');
            //<onBeforeDatagridLoad>

            //</onBeforeDatagridLoad>
             // ==== FILTRO DE PROPOSTA (sempre tentar restringir) ====
            $propostaId = TSession::getValue('idproposta') ?? ($param['propostas_id'] ?? $param['key'] ?? null);

            if ($propostaId) {
                $criteria->add(new TFilter('propostas_id', '=', $propostaId));
            } else {
                // Se por algum motivo não tiver proposta ainda, evita trazer tudo
                $criteria->add(new TFilter('id', '=', 0)); // não retorna nada
            }
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            TSession::setValue('contador_itens', null);
            TSession::setValue('contador_itens', count($objects));
            // if no objects were loaded, show a message

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    $check = new TCheckGroup('builder_datagrid_check');
                    $check->addItems([$object->id => '']);
                    $check->getButtons()[$object->id]->onclick = 'event.stopPropagation()';

                    if(!$this->datagrid_form->getField('builder_datagrid_check[]'))
                    {
                        $this->datagrid_form->setFields([$check]);
                    }

                    $check->setChangeAction(new TAction([$this, 'builderSelectCheck']));
                    $object->builder_datagrid_check = $check;

                    // Recupera o array da sessão ou cria um novo
                    $session_checks = TSession::getValue(__CLASS__ . 'builder_datagrid_check') ?? [];

                    // Se o checkbox estiver na sessão OU o estado já estiver definido, marca como selecionado
                    if (!empty($session_checks[$object->id])) {
                        $object->builder_datagrid_check->setValue([$object->id => $object->id]);

                        // Garante que o item seja adicionado na sessão
                        $session_checks[$object->id] = $object->id;
                        TSession::setValue(__CLASS__ . 'builder_datagrid_check', $session_checks);
                    }
                    //<onBeforeDatagridAddItem>

                    //</onBeforeDatagridAddItem>
                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";
                    //<onAfterDatagridAddItem>

                    //</onAfterDatagridAddItem>
                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            //<onBeforeDatagridTransactionClose>

            //</onBeforeDatagridTransactionClose>

            // close the transaction
            TTransaction::close();
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

  /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

        $this->fieldList_6881430e7887f->addHeader();
        $this->fieldList_6881430e7887f->addDetail($this->default_item_fieldList_6881430e7887f);

        $this->fieldList_6881430e7887f->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    }

    public function onShow($param = null)
    {
        if (TSession::getValue('tipoacao') <> 'Reprovar') {
            $this->fieldList_6881430e7887f->addHeader();
            $this->fieldList_6881430e7887f->addDetail($this->default_item_fieldList_6881430e7887f);

            $this->fieldList_6881430e7887f->addCloneAction(null, 'fas:plus #69aa46', "Clonar");
       }
    } 

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload')))) )
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

    //</hideLine> <addUserFunctionsCode/>

    // public static function builderSelectCheck($param)
    // {
    //     TTransaction::open('minierp');

    //     $session_checks = TSession::getValue(__CLASS__ . 'builder_datagrid_check') ?? [];

    //     $valueOn = null;
    //     if (!empty($param['_field_data_json'])) {
    //         $obj = json_decode($param['_field_data_json']);
    //         if ($obj) {
    //             $valueOn = $obj->valueOn ?? null;
    //         }
    //     }

    //     $key = empty($param['key']) ? $valueOn : $param['key'];
    //     $object = new ItensPropostas($key);

    //     $total_produtos  = TSession::getValue('total_produtos') ?? 0;
    //     $total_servicos  = TSession::getValue('total_servicos') ?? 0;
    //     $total_produtos_servicos  = TSession::getValue('total_produtos_servicos') ?? 0;

    //     if (empty($param['builder_datagrid_check']) && !empty($session_checks[$key])) {
    //         // desmarcou
    //         if ($object->tipo == 1) {
    //             $total_produtos += $object->valor_total;
    //         } else {
    //             $total_servicos += $object->valor_total;
    //         }
    //         $total_produtos_servicos = ($total_produtos + $total_servicos);
    //         unset($session_checks[$key]);
    //     } elseif (!empty($param['builder_datagrid_check']) && !in_array($key, $param['builder_datagrid_check']) && !empty($session_checks[$key])) {
    //         // desmarcou estando na sessão
    //         if ($object->tipo == 1) {
    //             $total_produtos -= $object->valor_total;
    //         } else {
    //             $total_servicos -= $object->valor_total;
    //         }
    //         $total_produtos_servicos = ($total_produtos + $total_servicos);
    //         unset($session_checks[$key]);
    //     } elseif (!empty($param['builder_datagrid_check']) && in_array($key, $param['builder_datagrid_check'])) {
    //         // marcou
    //         if (!isset($session_checks[$key])) {
    //             if ($object->tipo == 1) {
    //                 $total_produtos += $object->valor_total;
    //             } else {
    //                 $total_servicos += $object->valor_total;
    //             }
    //             $total_produtos_servicos = ($total_produtos + $total_servicos);
    //             $session_checks[$key] = $key;
    //         }
    //     }

    //     // Atualiza os valores nas sessões
    //     TSession::setValue('total_produtos', $total_produtos);
    //     TSession::setValue('total_servicos', $total_servicos);
    //     TSession::setValue('total_produtos_servicos', $total_produtos_servicos);
    //     TSession::setValue(__CLASS__ . 'builder_datagrid_check', $session_checks);

    //     // Envia para o formulário
    //     $objectpro = new stdClass();
    //     $objectpro->total_produtos = round($total_produtos, 2);
    //     $objectpro->total_servicos = round($total_servicos, 2);
    //     $objectpro->total_produtos_servicos = round($total_produtos + $total_servicos, 2);

    //     TForm::sendData('form_ItensPropostasFormList', $objectpro);

    //     TTransaction::close();
    // }

    public static function builderSelectCheck($param)
    {
        try {
            TTransaction::open('minierp');

            $session_checks = TSession::getValue(__CLASS__ . 'builder_datagrid_check') ?? [];

            $valueOn = null;
            if (!empty($param['_field_data_json'])) {
                $obj = json_decode($param['_field_data_json']);
                if ($obj) {
                    $valueOn = $obj->valueOn ?? null;
                }
            }

            $key    = empty($param['key']) ? $valueOn : $param['key'];
            $object = new ItensPropostas($key);

            $total_produtos           = TSession::getValue('total_produtos') ?? 0;
            $total_servicos           = TSession::getValue('total_servicos') ?? 0;
            $total_produtos_servicos  = TSession::getValue('total_produtos_servicos') ?? 0;

            if (empty($param['builder_datagrid_check']) && !empty($session_checks[$key])) {
                // desmarcou
                if ($object->tipo == 1) {
                    $total_produtos += $object->valor_total;
                } else {
                    $total_servicos += $object->valor_total;
                }
                $total_produtos_servicos = ($total_produtos + $total_servicos);
                unset($session_checks[$key]);
            } elseif (!empty($param['builder_datagrid_check']) && !in_array($key, $param['builder_datagrid_check']) && !empty($session_checks[$key])) {
                // desmarcou estando na sessão
                if ($object->tipo == 1) {
                    $total_produtos -= $object->valor_total;
                } else {
                    $total_servicos -= $object->valor_total;
                }
                $total_produtos_servicos = ($total_produtos + $total_servicos);
                unset($session_checks[$key]);
            } elseif (!empty($param['builder_datagrid_check']) && in_array($key, $param['builder_datagrid_check'])) {
                // marcou
                if (!isset($session_checks[$key])) {
                    if ($object->tipo == 1) {
                        $total_produtos += $object->valor_total;
                    } else {
                        $total_servicos += $object->valor_total;
                    }
                    $total_produtos_servicos = ($total_produtos + $total_servicos);
                    $session_checks[$key]    = $key;
                }
            }

            // Atualiza os valores nas sessões
            TSession::setValue('total_produtos', $total_produtos);
            TSession::setValue('total_servicos', $total_servicos);
            TSession::setValue('total_produtos_servicos', $total_produtos_servicos);
            TSession::setValue(__CLASS__ . 'builder_datagrid_check', $session_checks);

            // Envia para o formulário
            $objectpro                             = new stdClass();
            $objectpro->total_produtos             = round($total_produtos, 2);
            $objectpro->total_servicos             = round($total_servicos, 2);
            $objectpro->total_produtos_servicos    = round($total_produtos + $total_servicos, 2);

            TForm::sendData('form_ItensPropostasFormList', $objectpro);

            TTransaction::close();
        } catch (Exception $e) {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }
            throw $e; // deixa estourar pra você perceber
        }
    }

    public static function manageRow($id, $param = [])
    {
        $list = new self($param);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new ItensPropostas($id);

        $session_checks = TSession::getValue(__CLASS__.'builder_datagrid_check');

        $check = new TCheckGroup('builder_datagrid_check');
        $check->addItems([$object->id => '']);
        $check->getButtons()[$object->id]->onclick = 'event.stopPropagation()';

        if(!$list->datagrid_form->getField('builder_datagrid_check[]'))
        {
            $list->datagrid_form->setFields([$check]);
        }

        $check->setChangeAction(new TAction([$list, 'builderSelectCheck']));
        $object->builder_datagrid_check = $check;

        if(!empty($session_checks[$object->id]))
        {
            $object->builder_datagrid_check->setValue([$object->id=>$object->id]);
        }

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    public function AtualizarItensPedido($pedidoId)
    {

        $propostasaprovadas = Propostas::where('pedido_frotas_id', '=', $pedidoId)
            ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
            ->load(); 
        if ($propostasaprovadas) {
           foreach ($propostasaprovadas as $proposta) {
           
             $propostaId = $proposta->id;
             //pegar somente os itens da proposta que estão aprovados
            $itensAprovados = ItensPropostas::where('propostas_id', '=', $propostaId)
                ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
                ->load();
            if ($itensAprovados) {
            // Adiciona os novos itens ao pedido
            // Verifica se existem itens para adicionar
                foreach ($itensAprovados as $item) {
                    // Atualiza o pedido_frotas_id para cada item
                    if ($item->itens_pedido_frotas_id) {
                        $itemPedidoFrotas = new ItensPedidoFrotas($item->itens_pedido_frotas_id);
                    } else {
                        $itemPedidoFrotas = new ItensPedidoFrotas();
                    }
                    $itemPedidoFrotas->tipo = $item->tipo;
                    $itemPedidoFrotas->produto_id = $item->produto_id;
                    $itemPedidoFrotas->descricao = $item->descricao;
                    $itemPedidoFrotas->qtde = $item->qtde;
                    $itemPedidoFrotas->valor_unitario = $item->valor;
                    $itemPedidoFrotas->valor_desconto = $item->valor_desconto;
                    $itemPedidoFrotas->valor_total = $item->valor_total;
                    $itemPedidoFrotas->marca_modelo = $item->marca_modelo;
                    $itemPedidoFrotas->fabricante = $item->fabricante;
                    $itemPedidoFrotas->codigo = $item->codigo;
                    $itemPedidoFrotas->qtdekmgarantia = $item->qtdekmgarantia;
                    $itemPedidoFrotas->diasdegarantia = $item->diasdegarantia;
                    $itemPedidoFrotas->qtdehoras = $item->qtdehoras;
                    $itemPedidoFrotas->perc_desconto = $item->perc_desconto;
                    $itemPedidoFrotas->pedido_frotas_id = $pedidoId;
                    $itemPedidoFrotas->created_at = date('Y-m-d H:i:s');
                    $itemPedidoFrotas->tbo_horas = $item->tbo_horas;
                    $itemPedidoFrotas->tbo_ciclos = $item->tbo_ciclos;
                    $itemPedidoFrotas->tsn_horas = $item->tsn_horas;
                    $itemPedidoFrotas->tso_horas = $item->tso_horas;
                    $itemPedidoFrotas->csn_ciclos = $item->csn_ciclos;
                    $itemPedidoFrotas->cso_ciclos = $item->cso_ciclos;                    
                    $itemPedidoFrotas->store();
                }
               /* // Carrega os itens do pedido atual
                $itensPedido = ItensPedidoFrotas::where('pedido_frotas_id', '=', $pedidoId)->load();

                foreach ($itensPedido as $item) {
                    // Verifica se existe algum item aprovado em qualquer proposta aprovada que combine com esse do pedido
                    $itensPropostasAprovadas = ItensPropostas::where('descricao', '=', $item->descricao)
                        ->where('tipo', '=', $item->tipo)
                        ->where('qtde', '=', $item->qtde)
                        ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
                        ->whereIn('propostas_id', function($criteria) use ($pedidoId) {
                            $criteria->add(new TFilter('pedido_frotas_id', '=', $pedidoId));
                            $criteria->add(new TFilter('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO));
                        })
                        ->load();

                    // Se nenhum item aprovado correspondente for encontrado, então remove do pedido
                    if (empty($itensPropostasAprovadas)) {
                        $item->delete();
                    }
                }*/
            }
          }
       }



    }

    public function onConfirmReprovarPedido($param)
    {
        try {
          //  TTransaction::open('minierp');

            $pedido_id = TSession::getValue('idpedido');
            $proposta_id = TSession::getValue('idproposta');

            $proposta = new Propostas($proposta_id);
            $pedido   = new PedidoFrotas($pedido_id);

            $pedido->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
            $pedido->valor_total_proposta    = $proposta->valor_total;
            $pedido->valor_desconto_proposta = $proposta->valor_desconto;
            $pedido->valor_liquido_proposta  = $proposta->valor_liquido;
            $pedido->store();

     //       TTransaction::close();

       //     new TMessage('info', 'Pedido reprovado com sucesso.');
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
    //  public static function onCalcValor($param = null) 
    // {
    //     try 
    //     {
    //         //code here
    //         TTransaction::open(self::$database); // open a transaction
    //         $id1=$param['_field_id'];
    //         $conteudojson = $param['_field_data_json'];
    //         $idproduto = json_decode($conteudojson);
    //         if (isset($idproduto->{'row'})) {
    //         $idproduto1 = $idproduto->{'row'}; // 1234
        
    //         $idsaldo =  (int) str_replace(['.', ','], [',', '.'],($param['dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id'][$idproduto1]));

    //         $saldoatual = 0;
    //         $saldodepartamento = new SaldoDepartamento($idsaldo);
    //         if ($saldodepartamento) {
    //             $saldoatual = $saldodepartamento->saldo_total;
    //         } else {
    //             throw new Exception('Saldo do departamento não encontrado.');
    //         }

    //         $subquery = 'SELECT id FROM pedido_frotas WHERE estado_pedido_frotas_id IN (' . 
    //                     EstadoPedidoFrotas::APROVADO . ',' .
    //                     EstadoPedidoFrotas::FINALIZADO . ',' .
    //                     EstadoPedidoFrotas::ENTREGUE . ',' .
    //                     EstadoPedidoFrotas::PGTOAPROVADO . ')';

    //         $pedidofrotas = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $idsaldo)
    //             ->where('pedido_frotas_id', 'IN', "($subquery)")
    //             ->load();                                               
    //         if ($pedidofrotas) {
    //            // $saldoatual = 0;
    //             foreach ($pedidofrotas as $pedido) {
    //                 $saldoatual -= $pedido->valor;
    //             }
    //         }

    //         $saldo_formatado = number_format((float) $saldoatual, 2, '.', '');


    //         TScript::create("$('#{$id1}').parent().parent().find('[name=\"dotacao_pedido_frotas_pedido_frotas_saldo_atual[]\"]').val({$saldo_formatado});");   

    //         TTransaction::close();
    //         }

    //     }
    //     catch (Exception $e) 
    //     {
    //         new TMessage('error', $e->getMessage());    
    //     }
    // }

    public static function onCalcValor($param = null) 
    {
        try 
        {
            TTransaction::open(self::$database); // open a transaction

            $id1          = $param['_field_id'] ?? null;
            $conteudojson = $param['_field_data_json'] ?? null;
            $idproduto    = $conteudojson ? json_decode($conteudojson) : null;

            if ($id1 && $idproduto && isset($idproduto->{'row'})) {

                $idproduto1 = $idproduto->{'row'};

                $idsaldo = (int) ($param['_field_value'] ?? 0);
                if ($idsaldo <= 0) {
                    $idsaldo = (int) ($param['dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id'][$idproduto1] ?? 0);
                }
                self::validarUsoSaldoDepartamentoId($idsaldo);

                $saldoatual       = 0;
                $saldodepartamento = new SaldoDepartamento($idsaldo);
                if ($saldodepartamento) {
                    $saldoatual = (float) $saldodepartamento->saldo_total;
                    if ($saldoatual <= 0) {
                        $saldoatual = (float) $saldodepartamento->saldo_produto + (float) $saldodepartamento->saldo_servico;
                    }
                } else {
                    throw new Exception('Saldo do departamento não encontrado.');
                }

                $subquery = 'SELECT id FROM pedido_frotas WHERE estado_pedido_frotas_id IN (' . 
                            EstadoPedidoFrotas::APROVADO . ',' .
                            EstadoPedidoFrotas::FINALIZADO . ',' .
                            EstadoPedidoFrotas::ENTREGUE . ',' .
                            EstadoPedidoFrotas::PGTOAPROVADO . ')';

                $pedidofrotas = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $idsaldo)
                    ->where('pedido_frotas_id', 'IN', "($subquery)")
                    ->load();                                               

                if ($pedidofrotas) {
                    foreach ($pedidofrotas as $pedido) {
                        $saldoatual -= $pedido->valor;
                    }
                }

                $saldo_formatado = number_format((float) $saldoatual, 2, ',', '.');
                $saldo_js = json_encode($saldo_formatado);
                $row_js = json_encode((int) $idproduto1);
                $id_js = json_encode((string) $id1);

                TScript::create("
                    (function() {
                        var saldo = {$saldo_js};
                        var row = {$row_js};
                        var fieldId = {$id_js};
                        var fieldName = 'dotacao_pedido_frotas_pedido_frotas_saldo_atual[]';
                        var \$field = $('[name=\"' + fieldName + '\"]').eq(row);

                        if (!\$field.length && fieldId) {
                            \$field = $('#' + fieldId).closest('tr, .row, .form-group').find('[name=\"' + fieldName + '\"]').first();
                        }

                        if (\$field.length) {
                            \$field.val(saldo).trigger('change').trigger('blur');
                        }
                    })();
                ");
            }

            TTransaction::close();
        }
        catch (Exception $e) 
        {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());    
        }
    }

    protected static function safeOpen()
    {
        if (!TTransaction::get()) {
            TTransaction::open(self::$database);
            return true;
        }
        return false;
    }

    protected static function safeClose($opened)
    {
        if ($opened && TTransaction::get()) {
            TTransaction::close();
        }
    }

    protected static function safeRollback($opened)
    {
        if ($opened && TTransaction::get()) {
            TTransaction::rollback();
        }
    }

    protected function atualizarStatusSaldosUtilizados($itens): void
    {
        if (empty($itens) || !is_iterable($itens)) {
            return;
        }

        $saldoIds = [];

        foreach ($itens as $item) {
            $saldoId = null;

            if (is_object($item)) {
                $saldoId = $item->saldo_departamento_id ?? null;
            } elseif (is_array($item)) {
                $saldoId = $item['saldo_departamento_id'] ?? null;
            }

            $saldoId = (int) $saldoId;
            if ($saldoId > 0) {
                $saldoIds[$saldoId] = $saldoId;
            }
        }

        foreach ($saldoIds as $saldoId) {
            $saldoDepartamento = new SaldoDepartamento($saldoId);
            $statusAtual = (string) $saldoDepartamento->status_saldo_departamento_id;

            if ($statusAtual === (string) StatusSaldoDepartamento::ANULADO) {
                continue;
            }

            $saldoTotal = (float) $saldoDepartamento->saldo_total;
            if ($saldoTotal <= 0) {
                $saldoTotal = (float) $saldoDepartamento->saldo_produto + (float) $saldoDepartamento->saldo_servico;
            }
            $totalUtilizado = 0.0;
            $dotacoes = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $saldoId)->load();

            if ($dotacoes) {
                foreach ($dotacoes as $dotacao) {
                    if (empty($dotacao->deleted_at)) {
                        $totalUtilizado += (float) $dotacao->valor;
                    }
                }
            }

            if ($saldoTotal > 0 && $totalUtilizado >= ($saldoTotal - 0.01)) {
                if ($statusAtual !== (string) StatusSaldoDepartamento::ENCERRADO) {
                    $saldoDepartamento->status_saldo_departamento_id = StatusSaldoDepartamento::ENCERRADO;
                    $saldoDepartamento->store();
                }
                continue;
            }

            if ($statusAtual === (string) StatusSaldoDepartamento::AGUARDANDOINIC) {
                $saldoDepartamento->status_saldo_departamento_id = StatusSaldoDepartamento::EMANDAMENTO;
                $saldoDepartamento->store();
            }
        }
    }

    protected function validarSaldosSelecionados($saldoIds): void
    {
        if (empty($saldoIds) || !is_array($saldoIds)) {
            return;
        }

        foreach ($saldoIds as $saldoId) {
            self::validarUsoSaldoDepartamentoId((int) $saldoId);
        }
    }

    protected function validarValoresPorSaldoDepartamento($saldoIds, $valores, int $pedidoId = 0): void
    {
        if (empty($saldoIds) || !is_array($saldoIds)) {
            return;
        }

        $totaisPorSaldo = [];

        foreach ($saldoIds as $index => $saldoId) {
            $saldoId = (int) $saldoId;
            if ($saldoId <= 0) {
                continue;
            }

            $valorInformado = $this->normalizarValorMonetario($valores[$index] ?? 0);
            if ($valorInformado <= 0) {
                throw new Exception('Informe um valor maior que zero para cada dotação orçamentária selecionada.');
            }

            if (!isset($totaisPorSaldo[$saldoId])) {
                $totaisPorSaldo[$saldoId] = 0.0;
            }

            $totaisPorSaldo[$saldoId] += $valorInformado;
        }

        foreach ($totaisPorSaldo as $saldoId => $valorInformado) {
            $saldoDisponivel = $this->getSaldoDisponivelDepartamento($saldoId, $pedidoId);

            if ($valorInformado > ($saldoDisponivel + 0.01)) {
                $saldoDepartamento = new SaldoDepartamento($saldoId);
                $numeroEmpenho = $saldoDepartamento->numero_documento_empenho ?? $saldoId;

                throw new Exception(
                    sprintf(
                        'O valor informado para o empenho %s ultrapassa o saldo disponível. Disponível: R$ %s. Informado: R$ %s.',
                        $numeroEmpenho,
                        number_format($saldoDisponivel, 2, ',', '.'),
                        number_format($valorInformado, 2, ',', '.')
                    )
                );
            }
        }
    }

    protected static function validarUsoSaldoDepartamentoId(int $saldoId): void
    {
        if ($saldoId <= 0) {
            return;
        }

        $saldoDepartamento = new SaldoDepartamento($saldoId);
        $statusId = (string) $saldoDepartamento->status_saldo_departamento_id;

        if (in_array($statusId, [StatusSaldoDepartamento::ENCERRADO, StatusSaldoDepartamento::ANULADO], true)) {
            $statusTexto = ($statusId === StatusSaldoDepartamento::ENCERRADO) ? 'Encerrado' : 'Anulado';
            throw new Exception("Não é permitido utilizar o empenho selecionado, pois ele está {$statusTexto}.");
        }
    }
    protected function getSaldoDisponivelDepartamento(int $saldoId, int $pedidoId = 0): float
    {
        if ($saldoId <= 0) {
            return 0.0;
        }

        $saldoDepartamento = new SaldoDepartamento($saldoId);
        $saldoDisponivel = (float) $saldoDepartamento->saldo_total;
        if ($saldoDisponivel <= 0) {
            $saldoDisponivel = (float) $saldoDepartamento->saldo_produto + (float) $saldoDepartamento->saldo_servico;
        }

        $subquery = 'SELECT id FROM pedido_frotas WHERE estado_pedido_frotas_id IN (' .
                    EstadoPedidoFrotas::APROVADO . ',' .
                    EstadoPedidoFrotas::FINALIZADO . ',' .
                    EstadoPedidoFrotas::ENTREGUE . ',' .
                    EstadoPedidoFrotas::PGTOAPROVADO . ')';

        $dotacoesQuery = DotacaoPedidoFrotas::where('saldo_departamento_id', '=', $saldoId)
            ->where('pedido_frotas_id', 'IN', "($subquery)");

        if ($pedidoId > 0) {
            $dotacoesQuery->where('pedido_frotas_id', '<>', $pedidoId);
        }

        $dotacoes = $dotacoesQuery->load();

        if ($dotacoes) {
            foreach ($dotacoes as $dotacao) {
                if (empty($dotacao->deleted_at)) {
                    $saldoDisponivel -= (float) $dotacao->valor;
                }
            }
        }

        return max(0, round($saldoDisponivel, 2));
    }

    protected function normalizarValorMonetario($valor): float
    {
        if (is_numeric($valor)) {
            return (float) $valor;
        }

        if (is_string($valor)) {
            $valor = trim($valor);
            $valor = str_replace(["R$", " "], '', $valor);

            $temVirgula = strpos($valor, ',') !== false;
            $temPonto = strpos($valor, '.') !== false;

            if ($temVirgula && $temPonto) {
                if (strrpos($valor, ',') > strrpos($valor, '.')) {
                    $valor = str_replace('.', '', $valor);
                    $valor = str_replace(',', '.', $valor);
                } else {
                    $valor = str_replace(',', '', $valor);
                }
            } elseif ($temVirgula) {
                $valor = str_replace('.', '', $valor);
                $valor = str_replace(',', '.', $valor);
            } elseif ($temPonto && preg_match('/^\d{1,3}(\.\d{3})+$/', $valor)) {
                $valor = str_replace('.', '', $valor);
            }
        }

        return (float) $valor;
    }


    //<userCustomFunctions>

    //</userCustomFunctions>

}

