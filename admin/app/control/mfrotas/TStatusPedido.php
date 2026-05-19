<?php
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

class TStatusPedido extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'PedidoFrotas';
    private static $primaryKey = 'id';
    private static $formName = 'form_TStatusPedidoFrotasForm';

    use BuilderMasterDetailFieldListTrait;

    private static function validarBloqueioSuivDaProposta($propostaId)
    {
        $divergencias = ItensPropostas::getDivergenciasSuivPorProposta($propostaId);

        if (!empty($divergencias)) {
            throw new Exception("Não é permitido pré-aprovar/aprovar proposta com item acima da tabela SUIV.\n" . implode("\n", $divergencias));
        }
    }

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
        $this->form->setFormTitle("Cadastro Aprovação de Pedido");

        $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id = new TCriteria();
        $filterVar = TSession::getValue('entidade');
        $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->add(new TFilter('saldo_entidade_contrato_id', 'in', "(SELECT id FROM saldo_entidade_contrato WHERE  deleted_at is null AND entidade_id in (SELECT id FROM entidade WHERE id = '{$filterVar}'))")); 
        $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->add(new TFilter('status_saldo_departamento_id', '<>', StatusSaldoDepartamento::ENCERRADO));
        $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id->add(new TFilter('status_saldo_departamento_id', '<>', StatusSaldoDepartamento::ANULADO));

        if (isset($param['tipoacao'])) {
            TSession::setValue('tipoacao', $param['tipoacao']);
        } 
        $this->form->setFormTitle(
            (TSession::getValue('tipoacao') === 'Aprovar') ? 'Aprovar Proposta' :
            ((TSession::getValue('tipoacao') === 'PreAprovar') ? 'Pré-Aprovar Proposta' : 'Reprovar Proposta')
        );

      
        $id = new TEntry('id');
        $propostas_id = new TEntry('propostas_id');
        $justificativa = new TText('justificativa');
        $total_produtos = new TNumeric('total_produtos', '2', ',', '.' );
        $total_servicos = new TNumeric('total_servicos', '2', ',', '.' );
        $total_produtos_servicos = new TNumeric('total_produtos_servicos', '2', ',', '.' );
        if (TSession::getValue('tipoacao') != 'Reprovar'
            && TSession::getValue('idunit')) {
            $dotacao_pedido_frotas_pedido_frotas_id = new THidden('dotacao_pedido_frotas_pedido_frotas_id[]');
            $dotacao_pedido_frotas_pedido_frotas___row__id = new THidden('dotacao_pedido_frotas_pedido_frotas___row__id[]');
            $dotacao_pedido_frotas_pedido_frotas___row__data = new THidden('dotacao_pedido_frotas_pedido_frotas___row__data[]');
        //    $dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id = new TDBCombo('dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id[]', 'minierp', 'SaldoDepartamento', 'id', '{id}','id asc' , $criteria_dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id );
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
        $justificativa->addValidation("Justificativa", new TRequiredValidator()); 
        $total_produtos->setEditable(false);
        $total_servicos->setEditable(false);
        $total_produtos_servicos->setEditable(false);

         $id->setSize('100%');
        $justificativa->setSize('100%', 70);
        $propostas_id->setSize('100%');
        $total_produtos->setSize('100%');
        $total_servicos->setSize('100%');
        $total_produtos_servicos->setSize('100%');
        $id->setEditable(false);
        $propostas_id->setEditable(false);
    

        $row0 = $this->form->addFields([new TLabel("ID Pedido", null, '14px', null, '100%'),$id],[new TLabel("ID Proposta", null, '14px', null, '100%'),$propostas_id]);
        $row0->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Justificativa: *", '#FF0000', '14px', null),$justificativa]);
        $row2->layout = [' col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("Total de Produtos", null, '14px', null, '100%'),$total_produtos],[new TLabel("Total de Serviços", null, '14px', null, '100%'),$total_servicos], [new TLabel("Total Geral", null, '14px', null, '100%'),$total_produtos_servicos]);
        $row3->layout = ['col-sm-4','col-sm-4','col-sm-4'];
        if (TSession::getValue('tipoacao') <> 'Reprovar'  && TSession::getValue('idunit')) {
            $row4 = $this->form->addFields([new TFormSeparator("<br>"."Dotação Orçamentária *", '#FF0000', '18', '#eee')]);
            $row4->layout = [' col-sm-12'];

            $row5 = $this->form->addFields([$this->fieldList_6881430e7887f]);
            $row5->layout = [' col-sm-12'];
        }

        // Botão de salvar
        if (TSession::getValue('tipoacao') === 'Aprovar') {
            $btn_onsave = $this->form->addAction("Aprovar", new TAction([$this, 'onSaveAprovar']), 'fas:check #ffffff');
            $this->btn_onsave = $btn_onsave;
            $btn_onsave->addStyleClass('btn-primary');
        } elseif (TSession::getValue('tipoacao') === 'Reprovar') {
            $btn_onsave = $this->form->addAction("Reprovar", new TAction([$this, 'onSaveReprovar']), 'fas:times #ffffff');
            $this->btn_onsave = $btn_onsave;
            $btn_onsave->addStyleClass('btn-primary');
        } elseif (TSession::getValue('tipoacao') === 'PreAprovar') {        
            $btn_onsave = $this->form->addAction("Pré Aprovar", new TAction([$this, 'onSavePreAprovar']), 'fas:check #ffffff');
            $this->btn_onsave = $btn_onsave;
            $btn_onsave->addStyleClass('btn-primary');
        }

        /*

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['TStatusPedidoFrotasList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

       */

        
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
        $column_qtde = new TDataGridColumn('qtde', "Qtde", 'left');
        $column_valor_transformed = new TDataGridColumn('valor', "Valor Unitário", 'left');
        $column_desconto_transformed = new TDataGridColumn('perc_desconto', "Desconto", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_total', "Valor total", 'left');
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
                    if ($produto &&  $object->qtde > $produto->suiv_tempo_mao_obra_id  ) {
                        $preco_formatado = $produto->suiv_tempo_servico;
                        $mens = "<span style='color:red; font-weight: bold;'>{$preco_formatado}</span>";
                    } else {
                        $preco_formatado =  $produto->suiv_tempo_servico;
                    }
                } else {
                    if ($produto &&  $object->qtde > $produto->suiv_tempo_mao_obra_id  ) {
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

        // $column_estado_pedido_frotas_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        // {
        //      TTransaction::open(self::$database);
        //   $estado_pedido_frotas = EstadoPedidoFrotas::find($object->estado_pedido_frotas_id);
        //     TTransaction::close();
        //    if (!$estado_pedido_frotas) {
        //         return '';
        //     }
        //     return "<span class='label label-default' style='width:240px; background-color:{$estado_pedido_frotas->cor}'> {$estado_pedido_frotas->nome} <span>";
           

        // });

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

        // $this->builder_datagrid_check = $this->datagrid->addColumn( new TDataGridColumn('builder_datagrid_check', $this->builder_datagrid_check_all, 'center',  '1%') );

         $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_propostas_id);
        $this->datagrid->addColumn($column_tipo_transformed);
        $this->datagrid->addColumn($column_produto_id);
        $this->datagrid->addColumn($column_descricao);
        // $this->datagrid->addColumn($column_tipo_pecas);
        $this->datagrid->addColumn($column_qtde);
        $this->datagrid->addColumn($column_valor_transformed);
        $this->datagrid->addColumn($column_desconto_transformed);
        $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column_valor_temparia_transformed);
        // $this->datagrid->addColumn($column_estado_pedido_frotas_id_transformed);
      //  $this->datagrid->addColumn($column_created_at_transformed);

        //<onAfterColumnsCreation>

        //</onAfterColumnsCreation>

        //<onAfterActionsCreation>

        //</onAfterActionsCreation>

        // create the datagrid model
        $this->datagrid->createModel();

        $panel = new TPanelGroup('Itens da Proposta');
       // $panel->setProperty('style', 'width: 80%; margin: auto;'); // <- AQUI aumenta o painel inteiro
$panel->setProperty('style', 'width: 100%;');

        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';


        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);
        if (TSession::getValue('utiliza_temparia')==1) {
            $this->onReload($param);
            $this->form->addContent([$panel]);
        }

        parent::add($this->form);
        // parent::add($panel);


    }

    public function onSaveAprovar($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $pedidoId = TSession::getValue('idpedido');
            $propostaId = TSession::getValue('idproposta');
            $tipoAcao = TSession::getValue('tipoacao');
            $userId = TSession::getValue('userid');


            $messageAction = null;

            //$this->form->validate(); // validate form data
            
          
            $object = new PedidoFrotas($pedidoId); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data
            $this->validarSaldosSelecionados($data->dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id ?? []);
            $this->validarValoresPorSaldoDepartamento(
                $data->dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id ?? [],
                $data->dotacao_pedido_frotas_pedido_frotas_valor ?? [],
                (int) $pedidoId
            );
            // if (TSession::getValue('idunit')==26) // jiparana
            // {
            //     throw new Exception('Aprovação de proposta não permitida.
            //     O limite de crédito do órgão foi excedido.
            //     Para prosseguir, entre em contato com o setor financeiro.');
            // }

            if (empty($data->justificativa)) {
                throw new Exception('Justificativa é obrigatória.');
            }
            self::validarBloqueioSuivDaProposta($propostaId);
            $proposta = new Propostas($propostaId);
            $this->validarTotalDotacaoProposta(
                $data->dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id ?? [],
                $data->dotacao_pedido_frotas_pedido_frotas_valor ?? [],
                (float) $proposta->valor_liquido,
                'aprovar'
            );

            // === Controle de aprovação por valor-base (onSaveAprovar) ===
            $unit = SystemUnit::where('id', '=', $object->system_unit_id)->load();

            // Se não houver unidade ou não houver valor base (>0), não testa nada
            if ($unit && (float)$unit[0]->valor_base_aprovacao > 0) {

                $valorBase  = (float)$unit[0]->valor_base_aprovacao;
                $valorTotal = (float)$proposta->valor_liquido;

                // Se o total ultrapassa o valor-base, avaliar alçada
                if ($valorTotal > $valorBase) {

                    // Se o pedido já está na etapa de aprovação por VB, e o usuário tem alçada, deixa seguir
                    // $usuarioPodeAprovarVB = $this->usuarioPodeAprovarVB(); // implemente conforme sua regra de permissão

                    // if ($usuarioPodeAprovarVB) {
                    //     // Usuário tem alçada → segue a aprovação normalmente
                    //     // (não faça nenhum teste adicional)
                    // } else {
                        // Usuário NÃO tem alçada → encaminha para APROVACAOVB, notifica gestor e barra aprovação agora
                        if (in_array(EstadoPedidoFrotas::APROVACAOVB, AprovadorFrotas::getEstadosDisponiveis())) {
                           // Notificar o gestor responsável pela aprovação por VB
                            $this->notificarGestorAprovacaoVB($object, $proposta, $valorBase, $valorTotal, $unit);
                       } else {
                            
                            // Notificar o gestor responsável pela aprovação por VB
                      //      $this->notificarGestorAprovacaoVB($object, $proposta, $valorBase, $valorTotal, $unit);

                            // new TMessage(
                            //     'error',
                            //     sprintf(
                            //         'Valor total da proposta (R$ %s) é MAIOR que o valor-base de aprovação da unidade (R$ %s). ' .
                            //         'Pedido encaminhado para aprovação do gestor.
                            //         Confirmar o usuário que vai fazer aprovação.',
                            //         number_format($valorTotal, 2, ',', '.'),
                            //         number_format($valorBase, 2, ',', '.')
                            //     )
                            // );

                            throw new Exception(
                                sprintf(
                                    'Valor total da proposta (R$ %s) é MAIOR que o valor-base de aprovação da unidade (R$ %s). ' .
                                    'Pedido encaminhado para aprovação do gestor.
                                    Confirmar o usuário que vai fazer aprovação.',
                                    number_format($valorTotal, 2, ',', '.'),
                                    number_format($valorBase, 2, ',', '.')
                                ));
                            
                        }
                    }
                //}
            }
            // === fim do controle VB ===

            // Verifica se já existe uma proposta aprovada para esse pedido
            $pedidoAprovado = false;
            $propostasDoPedido = Propostas::where('pedido_frotas_id', '=', $pedidoId)->load();

            foreach ($propostasDoPedido as $p) {
                if ($p->estado_pedido_frotas_id == EstadoPedidoFrotas::APROVADO) {
                    $pedidoAprovado = true;
                    break;
                }
            }
            if ($pedidoAprovado) {
                throw new Exception('Já existe uma proposta aprovada para este pedido.');
            }
            
            self::validarBloqueioSuivDaProposta($propostaId);

            // Define estado da proposta conforme ação
            $proposta = new Propostas($propostaId);
            $proposta->obs = $object->obs;
            $proposta->estado_pedido_frotas_id = ($tipoAcao === 'Aprovar') ? EstadoPedidoFrotas::APROVADO : EstadoPedidoFrotas::REPROVADO;
            $proposta->store();

            // Atualiza o pedido
            $object->estabelecimento_id = $proposta->pessoa_id;
            $object->estado_pedido_frotas_id = EstadoPedidoFrotas::APROVADO;
            $object->valor_total = $proposta->valor_total;
            $object->valor_total_proposta = $proposta->valor_total;
            $object->valor_desconto_proposta = $proposta->valor_desconto;
            $object->valor_liquido_proposta = $proposta->valor_liquido;
            $object->data_aprovacao = date('Y-m-d H:i:s');
            $object->store(); 

            $propostasDoPedido = Propostas::where('pedido_frotas_id','=', $object->id)->load();

            // Reprova todas as outras propostas do pedido, exceto a atual
            foreach ($propostasDoPedido as $p) {
                if ($p->id != $propostaId && $p->estado_pedido_frotas_id == EstadoPedidoFrotas::AGUARDANDO) {
                    $p->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                    $p->obs = 'Reprovada automaticamente após aprovação de outra proposta';
                    $p->store();
                    
                    // Opcional: salva histórico da reprovação
                    $histReprovada = new PropostasHistorico();
                    $histReprovada->propostas_id = $p->id;
                    $histReprovada->aprovador_frotas_id = $aprovadores->id ?? null;
                    $histReprovada->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                    $histReprovada->data_historico = date('Y-m-d H:i:s');
                    $histReprovada->obs = 'Reprovada automaticamente após aprovação de outra proposta';
                    $histReprovada->store();
                }
            }

            // Históricos
            $aprovador = AprovadorFrotas::where('system_users_id', '=', $userId)->load();

            foreach ($aprovador as $aprovadores) {
                $histPedido = new PedidoFrotasHistorico();
                $histPedido->pedido_frotas_id = $object->id;
                $histPedido->aprovador_frotas_id = $aprovadores->id;
                $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $data->justificativa;
                $histPedido->store();


                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id =$aprovadores->id;
                $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs =  $data->justificativa;
                $histProposta->store();
                break;
            }

            $this->AtualizarItensPedido($object->id, $proposta->id);

            // Criar manutenção com base na proposta aprovada
            if ($tipoAcao === 'Aprovar') {
                $itens = ItensPropostas::where('propostas_id', '=', $proposta->id)->load();
                foreach ($itens as $item) {
                    $add = false;
                    $data_garantia = null;

                    if ($item->qtdekmgarantia > 0) {
                        if (TSession::getValue('tipofrota')==2) {
                            // Frota de máquinas -> usar horas
                            $horimetro_atual = (float) ($object->km ?? 0);
                            $horas_garantia  = (float) $item->qtdekmgarantia; // campo já existente
                            
                            $horas_restantes = $horas_garantia - $horimetro_atual;
                            $media_horas_dia = 5; // ajuste conforme o uso real
                            
                            if ($horas_restantes > 0 && $media_horas_dia > 0) {
                                $dias_estimados = ceil($horas_restantes / $media_horas_dia);
                                $data_garantia  = date('Y-m-d', strtotime("+$dias_estimados days"));
                                $add = true;
                            }                       
                        } 
                        else {
                            $km_atual = $object->km;
                            $media_km_dia = 50;
                            $km_faltante = $item->qtdekmgarantia - $km_atual;

                            if ($km_faltante > 0 && $media_km_dia > 0) {
                                $dias_estimados = ceil($km_faltante / $media_km_dia);
                                $data_garantia = date('Y-m-d', strtotime("+$dias_estimados days"));
                                $add = true;
                            }
                        }
                    }

                    if ($item->diasdegarantia > 0) {
                        $data_garantia = date('Y-m-d', strtotime($item->created_at . " +{$item->diasdegarantia} days"));
                        $add = true;
                    }

                    if ($add) {
                        $manutencao = new ManutencaoGarantia();
                        $manutencao->itens_propostas_id = $item->id;
                        $manutencao->veiculos_id = $object->veiculos_id;
                        $manutencao->pedido_frotas_id = $object->id;
                        $manutencao->propostas_id = $proposta->id;
                        $manutencao->created_at = date('Y-m-d H:i:s');
                        $manutencao->tipo = $item->tipo;
                        $manutencao->km_manutencao = $item->qtdekmgarantia;
                        $manutencao->dias_garantia = $item->diasdegarantia;
                        $manutencao->datagarantia = $data_garantia;
                        $manutencao->descricao = $item->descricao;
                        $manutencao->produto_id = $item->produto_id;
                        $manutencao->obs = $data->justificativa;
                        $manutencao->qtde = $item->qtde;
                        $manutencao->ativo = 'S';
                        $manutencao->ciclos_manutencao = $item->ciclos;
                        $manutencao->tbo_horas = $item->tbo_horas;
                        $manutencao->tbo_ciclos = $item->tbo_ciclos;
                        $manutencao->tsn_horas = $item->tsn_horas;
                        $manutencao->tso_horas = $item->tso_horas;
                        $manutencao->csn_ciclos = $item->csn_ciclos;
                        $manutencao->cso_ciclos = $item->cso_ciclos;
                        $manutencao->store();
                    }
                }
            }

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = $object->id;

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $valor = 0;
            if ( TSession::getValue('idunit')) {
                $dotacao_pedido_frotas_pedido_frotas_items = $this->storeItems(
                    'DotacaoPedidoFrotas',
                    'pedido_frotas_id',
                    $object,
                    $this->fieldList_6881430e7887f,
                    function($masterObject, $detailObject) use (&$valor) {
                        // Corrige valor numérico se vier sem ponto
                        $detailObject->valor = $this->toFloat($detailObject->valor);
                        $detailObject->saldo_atual = $this->toFloat($detailObject->saldo_atual);
                        $detailObject->propostas_id = $masterObject->propostas_id;
                        $valor += $detailObject->valor;
                    },
                    $this->criteria_fieldList_6881430e7887f
                );
                $this->atualizarStatusSaldosUtilizados($dotacao_pedido_frotas_pedido_frotas_items);
            }
             $valor = (float) $valor;
            $total_produtos_servicos = (float) TSession::getValue('total_produtos_servicos');
            $valor = round($valor, 2);
            $total_produtos_servicos = round($total_produtos_servicos, 2);

            
            if ($valor==0) {
                throw new Exception('Nenhum item de dotação orçamentária foi informado.');
            } 
            $this->validarTotalDotacaoPropostaValor($valor, (float) $proposta->valor_liquido, 'aprovar');
            // if ($valor > ($total_produtos_servicos)) {
            //     throw new Exception('Valor total da dotação orçamentária não pode ser maior que o valor total do pedido.');
            // } elseif ($valor < $total_produtos_servicos) {
            //     throw new Exception('Valor total da dotação orçamentária não pode ser menor que o valor total do pedido.');
            // }
            
         

    //        $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro aprovado com sucesso!", 'topRight', 'far:check-circle');
            
           TApplication::loadPage('PedidoFrotasList', 'onReload');
           TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);

             TScript::create("Template.closeRightPanel();");
            //TForm::sendData(self::$formName, (object)['id' => $object->id]);

        }
        catch (Exception $e) // in case of exception
        {

            new TMessage('error', $e->getMessage()); // shows the exception error message

            $objectpro = new stdClass();
            $objectpro->id = $pedidoId;
            $objectpro->propostas_id = $propostaId;
            $objectpro->total_produtos = TSession::getValue('total_produtos');
            $objectpro->total_servicos = TSession::getValue('total_servicos');
            $objectpro->justificativa = $object->justificativa;
            $objectpro->total_produtos_servicos = TSession::getValue('total_produtos_servicos');


            $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

               //code here
               $detailObject->valor = $this->toFloat($detailObject->valor);
               $detailObject->saldo_atual = $this->toFloat($detailObject->saldo_atual);
               $detailObject->propostas_id = $masterObject->propostas_id;

            }, $this->criteria_fieldList_6881430e7887f); 
            TForm::sendData('form_TStatusPedidoFrotasForm', $objectpro);

            TTransaction::rollback(); // undo all pending operations
        }
    }

    function toFloat($valor) {
        // Se for string com vírgula como decimal (formato brasileiro): "4.719,48"
        if (is_string($valor) && strpos($valor, ',') !== false && strpos($valor, '.') !== false) {
            $valor = str_replace('.', '', $valor); // remove separador de milhar
            $valor = str_replace(',', '.', $valor); // troca vírgula por ponto decimal
        } elseif (is_string($valor) && strpos($valor, ',') !== false) {
            $valor = str_replace(',', '.', $valor); // troca vírgula por ponto decimal
        }
        return (float) $valor;
    }
    public function onSavePreAprovar($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $pedidoId = TSession::getValue('idpedido');
            $propostaId = TSession::getValue('idproposta');
            $tipoAcao = TSession::getValue('tipoacao');
            $userId = TSession::getValue('userid');

            $messageAction = null;

            //$this->form->validate(); // validate form data
            
          
            $object = new PedidoFrotas($pedidoId); // create an empty object 
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data
            $this->validarSaldosSelecionados($data->dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id ?? []);
            $this->validarValoresPorSaldoDepartamento(
                $data->dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id ?? [],
                $data->dotacao_pedido_frotas_pedido_frotas_valor ?? [],
                (int) $pedidoId
            );

            if (empty($data->justificativa)) {
                throw new Exception('Justificativa é obrigatória.');
            }
           

            // Define estado da proposta conforme ação
            $proposta = new Propostas($propostaId);
            $proposta->obs = $object->obs;
            self::validarBloqueioSuivDaProposta($propostaId);
            $this->validarTotalDotacaoProposta(
                $data->dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id ?? [],
                $data->dotacao_pedido_frotas_pedido_frotas_valor ?? [],
                (float) $proposta->valor_liquido,
                'pre-aprovar'
            );
            $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
            $proposta->store();

            // Atualiza o pedido
          //  $object->estabelecimento_id = $proposta->pessoa_id;
            $object->estado_pedido_frotas_id = EstadoPedidoFrotas::PREAPROVADO;
            $object->valor_total = $proposta->valor_total;
            $object->valor_total_proposta = $proposta->valor_total;
            $object->valor_desconto_proposta = $proposta->valor_desconto;
            $object->valor_liquido_proposta = $proposta->valor_liquido;
            $object->data_aprovacao = date('Y-m-d H:i:s');
            $object->store(); 

            // Históricos
            $aprovador = AprovadorFrotas::where('system_users_id', '=', $userId)->load();

            foreach ($aprovador as $aprovadores) {
                 $histPedido = new PedidoFrotasHistorico();
                $histPedido->pedido_frotas_id = $object->id;
                $histPedido->aprovador_frotas_id = $aprovadores->id;
                $histPedido->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $data->justificativa;
                $histPedido->store();


                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id =$aprovadores->id;
                $histProposta->estado_pedido_frotas_id = $proposta->estado_pedido_frotas_id;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs = $data->justificativa;
                $histProposta->store();
                break;
            }

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = $object->id;
            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $valor = 0;
             $itens = $this->storeItems(
                'DotacaoPedidoFrotas',
                'pedido_frotas_id',
                $object,
                $this->fieldList_6881430e7887f,
                function($masterObject, $detailObject) use (&$valor) {
                    $detailObject->valor = $this->toFloat($detailObject->valor);
                    $detailObject->saldo_atual = $this->toFloat($detailObject->saldo_atual);
                    $valor += $detailObject->valor;
                    $detailObject->propostas_id = $masterObject->propostas_id;
                },
                $this->criteria_fieldList_6881430e7887f
            ) ?? [];
            $this->atualizarStatusSaldosUtilizados($itens);

             if (empty( $itens)) {
                throw new Exception('Nenhum item de dotação orçamentária foi informado.');
            } 
            $this->validarTotalDotacaoPropostaValor($valor, (float) $proposta->valor_liquido, 'pre-aprovar');

    
            $valor = (float) $valor;
            $total_produtos_servicos = (float) TSession::getValue('total_produtos_servicos');
            $valor = round($valor, 2);
            $total_produtos_servicos = round($total_produtos_servicos, 2);

            
            // if ($valor > ($total_produtos_servicos)) {
            //     throw new Exception('Valor total da dotação orçamentária não pode ser maior que o valor total do pedido.');
            // } elseif ($valor < $total_produtos_servicos) {
            //     throw new Exception('Valor total da dotação orçamentária não pode ser menor que o valor total do pedido.');
            // }
            

    //        $this->form->setData($data); // fill form data
            TTransaction::close();

            TToast::show('success', "Registro pre-aprovado com sucesso!", 'topRight', 'far:check-circle');
            
           TApplication::loadPage('PedidoFrotasList', 'onReload');
           TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);

             TScript::create("Template.closeRightPanel();");
            //TForm::sendData(self::$formName, (object)['id' => $object->id]);

        }
        catch (Exception $e) // in case of exception
        {

            new TMessage('error', $e->getMessage()); // shows the exception error message

            $objectpro = new stdClass();
            $objectpro->id = $pedidoId;
            $objectpro->propostas_id = $propostaId;
            $objectpro->total_produtos = TSession::getValue('total_produtos');
            $objectpro->total_servicos = TSession::getValue('total_servicos');
            $objectpro->justificativa = $object->justificativa;
            $objectpro->total_produtos_servicos = TSession::getValue('total_produtos_servicos');


            $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

               //code here
               $detailObject->valor = $this->toFloat($detailObject->valor);
               $detailObject->saldo_atual = $this->toFloat($detailObject->saldo_atual);
               $detailObject->propostas_id = $masterObject->propostas_id;

            }, $this->criteria_fieldList_6881430e7887f); 
            TForm::sendData('form_TStatusPedidoFrotasForm', $objectpro);

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

                $object = new PedidoFrotas($key); // instantiates the Active Record 

                
                $histPedido = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $object->id)
                    ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::COMPROPOSTA)
                    ->orderBy('data_operacao', 'desc')
                    ->last();
                if ($histPedido) {
                    $object->justificativa = $histPedido->obs;
                } else {
                    $object->justificativa = '';
                }


                $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }, $this->criteria_fieldList_6881430e7887f); 

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
        if (TSession::getValue('tipoacao') <> 'Reprovar'  && TSession::getValue('idunit')) {
            $this->fieldList_6881430e7887f->addHeader();
            $this->fieldList_6881430e7887f->addDetail($this->default_item_fieldList_6881430e7887f);

            $this->fieldList_6881430e7887f->addCloneAction(null, 'fas:plus #69aa46', "Clonar");
        }

    }

    public function onShow($param = null)
    {
        if (TSession::getValue('tipoacao') <> 'Reprovar'  && TSession::getValue('idunit')) {
            $this->fieldList_6881430e7887f->addHeader();
            $this->fieldList_6881430e7887f->addDetail($this->default_item_fieldList_6881430e7887f);

            $this->fieldList_6881430e7887f->addCloneAction(null, 'fas:plus #69aa46', "Clonar");
       }
    } 

    public static function getFormName()
    {
        return self::$formName;
    }

    // Método para exibir o modal com os dados
    public function onShowModal($param) {

        try
        {

            TTransaction::open(self::$database); // open a transaction
            
            $total_produtos = 0;
            $total_servicos = 0;
            $justificativa = '';
            if (isset($param['id'])) {

                $id = $param['id'];


                // Busca os dados do Pedido
                $proposta = new Propostas($id);
                TSession::setValue('idpedido', null);
                TSession::setValue('idpedido', $proposta->pedido_frotas_id);
                TSession::setValue('idproposta', null);
                TSession::setValue('idproposta', $id);
                TSession::setValue('tipoacao', null);
                TSession::setValue('tipoacao', $param['tipoacao']);
                // Finaliza a transação
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
                                $justificativa = $justificativa->obs;
                            } else {
                                $justificativa->justificativa = '';
                            }
                        }

                    }
            }

            if (isset($param['pedido_frotas_id']))
            {
                
                $key = $param['pedido_frotas_id'];  // get the parameter $key

                $object = new PedidoFrotas($key); // instantiates the Active Record 

                $histPedido = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $id)
                    ->orderBy('data_operacao', 'desc')
                    ->last();
                if ($histPedido) {
                    $object->justificativa = $histPedido->obs;
                } else {
                    $object->justificativa = '';
                }
                if (TSession::getValue('tipoacao') <> 'Reprovar') {
                    $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

                        //code here

                    }, $this->criteria_fieldList_6881430e7887f); 
                }
                $itens_propostas = ItensPropostas::where('propostas_id', '=', $id)->load();
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
                $this->form->setData($object); // fill the form 

            }
            else
            {
                $this->form->clear();
            }
            
            $objectpro = new stdClass();
            $objectpro->propostas_id = $param['id'];
            $objectpro->total_produtos = round(str_replace(',', '', $total_produtos),2);
            $objectpro->total_servicos = round(str_replace(',', '', $total_servicos),2);
            $objectpro->justificativa = $justificativa;
            $objectpro->total_produtos_servicos = round(str_replace(',', '',$total_produtos_servicos),2);

             TSession::setValue('total_produtos', null);
                TSession::setValue('total_produtos', str_replace(',', '', round(str_replace(',', '', $total_produtos),2)));
             TSession::setValue('total_servicos', null);
                TSession::setValue('total_servicos', str_replace(',', '', round(str_replace(',', '', $total_servicos),2)));
             TSession::setValue('total_produtos_servicos', null);
                TSession::setValue('total_produtos_servicos', str_replace(',', '', round(str_replace(',', '',$total_produtos + $total_servicos),2)));
            TForm::sendData('form_TStatusPedidoFrotasForm', $objectpro);
           $this->onReload($param);


            TTransaction::close(); // close the transaction 



        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }


    public function AtualizarItensPedido($pedidoId, $propostaId)
    {
        // Lógica para atualizar os itens do pedido
        $itens = ItensPropostas::where('propostas_id', '=', $propostaId)->load();
        // Verifica se existem itens para atualizar
        if (empty($itens)) {
            throw new Exception('Nenhum item encontrado para atualizar.');
        }

        // Adiciona os novos itens ao pedido
        // Verifica se existem itens para adicionar

        foreach ($itens as $item) {
            // Atualiza o pedido_frotas_id para cada item
            if ($item->itens_pedido_frotas_id) {
                $itemPedidoFrotas = new ItensPedidoFrotas($item->itens_pedido_frotas_id);
            } else {
                $itemPedidoFrotas = new ItensPedidoFrotas();
            }
            $itemPedidoFrotas->tipo = $item->tipo;
            $itemPedidoFrotas->descricao = $item->descricao;
            $itemPedidoFrotas->produto_id = $item->produto_id;
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
        // remover itens pedido frotas
        $itensPedido = ItensPedidoFrotas::where('pedido_frotas_id', '=', $pedidoId)->load();
        foreach ($itensPedido as $item) {
            $itenspropostas = ItensPropostas::where('propostas_id', '=', $propostaId)
                                            ->where('produto_id', '=', $item->produto_id)
                                            ->where('tipo', '=', $item->tipo)
                                            ->where('qtde', '=', $item->qtde)
                                            ->load();
            // Verifica se o item não está mais na proposta
            if (empty($itenspropostas)) {
            ///    $item->delete();
            }

        }
    }

   
    public function onSaveReprovar($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $pedidoId = TSession::getValue('idpedido');
            $propostaId = TSession::getValue('idproposta');
            $tipoAcao = TSession::getValue('tipoacao');
            $userId = TSession::getValue('userid');

            $messageAction = null;

            //$this->form->validate(); // validate form data
            
          
            $object = new PedidoFrotas($pedidoId); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            if (empty($data->justificativa)) {
                throw new Exception('Justificativa é obrigatória.');
            }

         // Verifica se já existe uma proposta aprovada para esse pedido
            $propostasDoPedido = Propostas::where('pedido_frotas_id', '=', $pedidoId)->load();

            $pedidoReprovado = true;

            foreach ($propostasDoPedido as $p) {
                // pula a proposta que está sendo reprovada
                if ($p->id != $propostaId && $p->estado_pedido_frotas_id != EstadoPedidoFrotas::REPROVADO) {
                    $pedidoReprovado = false;
                    break; // já encontrou uma ativa (exceto a reprovada)
                }
            }
            
            // Históricos
            $aprovador = AprovadorFrotas::where('system_users_id', '=', $userId)->load();

            if ($pedidoReprovado)
            {
                $object->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                $object->store();

                         // Define estado da proposta conforme ação
                $proposta = new Propostas($propostaId);
                $proposta->obs = $data->justificativa;
                $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                $proposta->store();

               

                $histPedido = new PedidoFrotasHistorico();
                $histPedido->pedido_frotas_id = $object->id;
                $histPedido->aprovador_frotas_id = $aprovador[0]->id;
                $histPedido->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                $histPedido->data_operacao = date('Y-m-d H:i:s');
                $histPedido->obs = $data->justificativa;
                $histPedido->store();

                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id = $aprovador[0]->id;
                $histProposta->estado_pedido_frotas_id =EstadoPedidoFrotas::REPROVADO;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs =$data->justificativa;
                $histProposta->store();

            } else {

                  // Define estado da proposta conforme ação
                $proposta = new Propostas($propostaId);
                $proposta->obs = $data->justificativa;
                $proposta->estado_pedido_frotas_id = EstadoPedidoFrotas::REPROVADO;
                $proposta->store();

                $histProposta = new PropostasHistorico();
                $histProposta->propostas_id = $proposta->id;
                $histProposta->aprovador_frotas_id = $aprovador[0]->id;
                $histProposta->estado_pedido_frotas_id =EstadoPedidoFrotas::REPROVADO;
                $histProposta->data_historico = date('Y-m-d H:i:s');
                $histProposta->obs = $data->justificativa;
                $histProposta->store();
 
            }

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }
            $loadPageParam["pedido_frotas_id"] = $object->id;

             if (TSession::getValue('tipoacao') <> 'Reprovar') {
                $dotacao_pedido_frotas_pedido_frotas_items = $this->storeItems(
                    'DotacaoPedidoFrotas',
                    'pedido_frotas_id',
                    $object,
                    $this->fieldList_6881430e7887f,
                    function($masterObject, $detailObject) {
                        // Ajusta valor de campos numéricos
                        if (isset($detailObject->valor)) {
                            // Corrige valor numérico se vier sem ponto
                            // Corrige valor numérico se vier sem ponto
                            $detailObject->valor = $this->toFloat($detailObject->valor);
                            $detailObject->saldo_atual = $this->toFloat($detailObject->saldo_atual);
                            $detailObject->propostas_id = $masterObject->propostas_id;
                        }
                    },
                    $this->criteria_fieldList_6881430e7887f
                );
            }
            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

    //        $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro Reprovado com sucesso!", 'topRight', 'far:check-circle');
            
           TApplication::loadPage('PedidoFrotasList', 'onReload');
          TApplication::loadPage('PropostaPendenteList', 'onShow', $loadPageParam);

             TScript::create("Template.closeRightPanel();");
            //TForm::sendData(self::$formName, (object)['id' => $object->id]);

        }
        catch (Exception $e) // in case of exception
        {

            new TMessage('error', $e->getMessage()); // shows the exception error message

            $objectpro = new stdClass();
            $objectpro->id = $pedidoId;
            $objectpro->propostas_id = $propostaId;
            $objectpro->total_produtos = TSession::getValue('total_produtos');
            $objectpro->total_servicos = TSession::getValue('total_servicos');
            $objectpro->justificativa = $object->justificativa;
            $objectpro->total_produtos_servicos = TSession::getValue('total_produtos_servicos');


            // $this->fieldList_6881430e7887f_items = $this->loadItems('DotacaoPedidoFrotas', 'pedido_frotas_id', $object, $this->fieldList_6881430e7887f, function($masterObject, $detailObject, $objectItems){ 

            //    //code here
            //    $detailObject->valor = str_replace(',', '', $detailObject->valor);
            //    $detailObject->saldo_atual = str_replace(',', '', $detailObject->saldo_atual);

            // }, $this->criteria_fieldList_6881430e7887f); 
            TForm::sendData('form_TStatusPedidoFrotasForm', $objectpro);

            TTransaction::rollback(); // undo all pending operations
        }
    }
     public static function onCalcValor($param = null) 
    {
        try 
        {
            //code here
            TTransaction::open(self::$database); // open a transaction
            $id1 = $param['_field_id'] ?? null;
            $conteudojson = $param['_field_data_json'] ?? null;
            $idproduto = $conteudojson ? json_decode($conteudojson) : null;
            if (isset($idproduto->{'row'})) {
            $idproduto1 = $idproduto->{'row'}; // 1234
        
            $idsaldo = (int) ($param['_field_value'] ?? 0);
            if ($idsaldo <= 0) {
                $idsaldo = (int) ($param['dotacao_pedido_frotas_pedido_frotas_saldo_departamento_id'][$idproduto1] ?? 0);
            }
            self::validarUsoSaldoDepartamentoId($idsaldo);

            $saldoatual = 0;
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
               // $saldoatual = 0;
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

            TTransaction::close();
            }

        }
        catch (Exception $e) 
        {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());    
        }
    }

    private function atualizarStatusSaldosUtilizados($itens): void
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

    private function validarSaldosSelecionados($saldoIds): void
    {
        if (empty($saldoIds) || !is_array($saldoIds)) {
            return;
        }

        foreach ($saldoIds as $saldoId) {
            self::validarUsoSaldoDepartamentoId((int) $saldoId);
        }
    }

    private function validarValoresPorSaldoDepartamento($saldoIds, $valores, int $pedidoId = 0): void
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

            $valorInformado = $this->toFloat($valores[$index] ?? 0);
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

    private function validarTotalDotacaoProposta($saldoIds, $valores, float $valorProposta, string $acao): void
    {
        if (empty($saldoIds) || !is_array($saldoIds)) {
            throw new Exception("Nao e possivel {$acao}. Nenhuma dotacao orcamentaria foi informada.");
        }

        $totalDotacao = 0.0;
        $possuiDotacaoValida = false;

        foreach ($saldoIds as $index => $saldoId) {
            $saldoId = (int) $saldoId;
            $valorInformado = $this->toFloat($valores[$index] ?? 0);

            if ($saldoId <= 0) {
                throw new Exception("Nao e possivel {$acao}. Existe dotacao orcamentaria sem empenho selecionado.");
            }

            if ($valorInformado <= 0) {
                throw new Exception("Nao e possivel {$acao}. Existe dotacao orcamentaria com valor zerado ou invalido.");
            }

            $totalDotacao += $valorInformado;
            $possuiDotacaoValida = true;
        }

        if (!$possuiDotacaoValida) {
            throw new Exception("Nao e possivel {$acao}. Nenhuma dotacao orcamentaria valida foi informada.");
        }

        $this->validarTotalDotacaoPropostaValor($totalDotacao, $valorProposta, $acao);
    }

    private function validarTotalDotacaoPropostaValor(float $totalDotacao, float $valorProposta, string $acao): void
    {
        $totalDotacao = round($totalDotacao, 2);
        $valorProposta = round($valorProposta, 2);

        if ($valorProposta <= 0) {
            throw new Exception("Nao e possivel {$acao}. A proposta nao possui valor liquido valido.");
        }

        if (abs($totalDotacao - $valorProposta) <= 0.01) {
            return;
        }

        throw new Exception(
            sprintf(
                'Nao e possivel %s. O total da dotacao orcamentaria deve ser igual ao valor liquido da proposta. Valor da proposta: R$ %s. Total informado: R$ %s.',
                $acao,
                number_format($valorProposta, 2, ',', '.'),
                number_format($totalDotacao, 2, ',', '.')
            )
        );
    }

    private static function validarUsoSaldoDepartamentoId(int $saldoId): void
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

    private function getSaldoDisponivelDepartamento(int $saldoId, int $pedidoId = 0): float
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

    private function usuarioPodeAprovarVB(): bool
    {
        // Exemplo: checar se o usuário logado pertence a um grupo com a permissão
        // Ajuste às suas tabelas (system_user, system_user_group, system_group, etc.)
        $userId = TSession::getValue('userid');

        // Retorne true se o usuário tiver a permissão/grupo de “Gestor Aprovador VB”
        return SystemPermission::check('APROVADORVB', $userId);
    }

    private function notificarGestorAprovacaoVB($pedido, $propostas, float $valorBase, float $valorTotal, $unit): void
    {
        // ID correto do template
        $codigo_email_template_id = EmailTemplate::NOTIFICACAO_VALORBASE;

        try {
            $emailTemplate = new EmailTemplate($codigo_email_template_id);
            if (!$emailTemplate) {
                throw new Exception('Template de e-mail não encontrado.');
            }

        
                     // Dados básicos

            // Carrega veículo e compõe identificação
            $veiculo = $pedido->veiculos_id ? new Veiculos($pedido->veiculos_id) : null;
            $marca  = $veiculo && $veiculo->marca ? $veiculo->marca->descricao : '';
            $modelo = $veiculo && $veiculo->modelo ? $veiculo->modelo->descricao : '';
            $placa  = $veiculo && $veiculo->placa ? $veiculo->placa : '';
            $identificacaoVeiculo = trim($placa . ' - ' . $marca . ' - ' . $modelo, ' -');

            // Mensagem / título originais
            $titulo   = (string) $emailTemplate->titulo;
            $mensagem = (string) $emailTemplate->mensagem;

            // Formatações numéricas (BR)
            $valorTotalFmt = number_format($valorTotal, 2, ',', '.');
            $valorBaseFmt  = number_format($valorBase, 2, ',', '.');


                //aprovadores 
            // usuario do mesmo orgão e que tenha o aprovacaovb
            $repo     = new TRepository('AprovadorFrotas');
            $criteria = new TCriteria();

            // filtro por subselect do estado APROVADORVB
            $criteria->add(new TFilter(
                'id',
                'IN',
                '(SELECT aprovador_frotas_id
                    FROM estado_pedido_frotas_aprovador
                WHERE estado_pedido_frotas_id = ' . EstadoPedidoFrotas::APROVACAOVB . ')'
            ));

            // filtro por unidade ativa da sessão
            $criteria->add(new TFilter(
                'system_users_id', // ou 'system_user_id'
                'IN',
                '(SELECT system_user_id
                    FROM system_user_unit
                WHERE system_unit_id = ' . (int) TSession::getValue('idunit') . ')'
            ));

            $aprovadores = $repo->load($criteria);   

            foreach ($aprovadores as $aps)
            {
                $usr = new SystemUsers($aps->system_users_id);

                // Placeholders suportados no template
                $replacements = [
                    '{nome_aprovador}'       => $usr->name ?? '',
                    '{id}'                   => $pedido->id ?? '',
                    '{data_pedido}'          => isset($pedido->dt_pedido) ? TDate::date2br($pedido->dt_pedido) : '',
                    '{valor_pedido}'         => $valorTotalFmt,
                    '{descricao_pedido}'     => $pedido->descricaopedido ?? '',
                    '{identificacao_veiculo}'=> $identificacaoVeiculo,
                    '{unidade}'              => $pedido->system_unit->name ?? ($unit->name ?? ''), // usa $unit se vier
                    '{departamento}'         => $pedido->departamento_unit->name ?? '',
                ];

                // Aplica substituições de uma vez
                $titulo   = strtr($titulo, $replacements);
                $mensagem = strtr($mensagem, $replacements);

                // Caso use renderização com variáveis do ActiveRecord
                if (method_exists($pedido, 'render')) {
                    $titulo   = $pedido->render($titulo);
                    $mensagem = $pedido->render($mensagem);
                }

                // Notificação + e-mail
                if (!empty($propostas->pessoa->email)) {
                    $pessoa = new Pessoa($propostas->pessoa_id);

                    $notificationParam = ['key' => $propostas->id];
                    $icon = 'fas fa-file-invoice-dollar';

                    SystemNotification::registerpedidofrotas(
                        $usr->id,
                        $titulo,
                        $mensagem,
                        new TAction(['PropostaPendenteList', 'onShow'], $notificationParam),
                        'Visualizar Proposta',
                        $icon
                    );

                 //   MailService::send($usr->email, $titulo, $mensagem, 'html');
                }
            }
        } catch (Exception $e) {
            // Logue de acordo com seu padrão
            TLog::error('EMAIL_NOTIF_APROVACAO', $e->getMessage());
            // opcional: TToast::show('error', 'Falha ao enviar notificação: ' . $e->getMessage());
        }
    }

     /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'minierp'
            TTransaction::open(self::$database);

            // creates a repository for ItensPropostas
            $repository = new TRepository('ItensPropostas');    
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
   


}

