<?php

//use Adianti\Widget\Form\THidden;

class DocumentosPropostasForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'minierp';
    private static $activeRecord = 'DocumentosPropostas';
    private static $primaryKey = 'id';
    private static $formName = 'form_DocumentosPropostasForm';

    use BuilderMasterDetailTrait;
    use Adianti\Base\AdiantiFileSaveTrait;
    use BuilderMasterDetailFieldListTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de documentos propostas");

        $criteria_tipo_documentos_propostas_id = new TCriteria;

        $id = new THidden('id');
        $numero = new TEntry('numero');
        $valor = new TNumeric('valor', 2, ',', '.');
        $caminho = new TMultiFile('caminho');
        $tipo_documentos_propostas_id = new TDBCombo('tipo_documentos_propostas_id', 'minierp', 'TipoDocumentosPropostas', 'id', '{descricao}', 'id asc', $criteria_tipo_documentos_propostas_id);
        $tipo_documentos_propostas_id->configureNoResultsQuickRegister(new TAction(['TipoDocumentosPropostasForm', 'onQuickSave']), "Cadastrar", "fas:plus #69aa46", "btn-default");
        $tipo_documentos_propostas_id->setNoResultsMessage("Nenhum tipo de documento encontrado. Clique em cadastrar");

        $id->setEditable(false);
        $caminho->enableFileHandling();
        $tipo_documentos_propostas_id->enableSearch();
        // $tipo_documentos_propostas_id->addItems(['1'=>"Produto", '2'=>"Serviço"]);

        $id->setSize(100);
        $caminho->setSize('100%');
        $numero->setSize('100%');
        $valor->setSize('100%');
        $tipo_documentos_propostas_id->setSize('100%');

        // $tipo_documentos_propostas_id->addValidation("Tipo Documentos Propostas", new TRequiredValidator());

        $row1 = $this->form->addFields(
            [new TLabel("Numero documento:", null, '14px', null), $id, $numero],
            [new TLabel("Arquivo *:", '#ff0000', '14px'), $caminho]
        );
        $row1->layout = ['col-sm-6', 'col-sm-6'];

        $row2 = $this->form->addFields(
            [new TLabel("Tipo Documento Proposta:", null, '14px', null, '100%'), $tipo_documentos_propostas_id],
            [new TLabel("Valor:", null, '14px', null, '100%'), $valor]
        );
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary');

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['DocumentosPropostasList', 'onShow']), 'fas:arrow-left #000000');
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

    public function onSave($param = null)
    {
        try
        {
            TTransaction::open(self::$database);

            $this->form->validate();
            $data = $this->form->getData();

            $caminho_dir = 'app/documentos/redes';
            $prop = Propostas::find(TSession::getValue('idcotanexo'));

            if (!empty($data->id))
            {
                $object = new DocumentosPropostas($data->id);
                
            }
            else
            {
                $object = new DocumentosPropostas;
                $tipoDocumentoId = (int) ($data->tipo_documentos_propostas_id ?? $object->tipo_documentos_propostas_id ?? 0);

                if (
                    in_array($tipoDocumentoId, [1, 2], true) &&
                    !in_array((string) $prop->estado_pedido_frotas_id, [
                        EstadoPedidoFrotas::ENTREGUE,
                        EstadoPedidoFrotas::FINALIZADO,
                        EstadoPedidoFrotas::PGTOAPROVADO,
                        EstadoPedidoFrotas::APROVADO,
                    ], true)
                ) {
                    throw new Exception("Não é permitido inserir este documento, pois a proposta está em um estado que não permite alterações.");
                }
            }

            $object->propostas_id = TSession::getValue('idcotanexo');
            $object->numero = $data->numero ?? null;
            $object->valor = $data->valor ?? null;
            $object->tipo_documentos_propostas_id = $data->tipo_documentos_propostas_id ?? null;
            $object->caminho = $data->caminho;

            $object->store();

            $this->saveFilesByComma($object, $data, 'caminho', $caminho_dir);

            $data->id = $object->id;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', "Registro salvo", 'topRight');
            TApplication::loadPage('DocumentosPropostasList', 'onShow', $param);

            TScript::create("Template.closeRightPanel()");
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                TTransaction::open(self::$database);

                $object = new DocumentosPropostas($key);

                if (!empty($object->caminho)) {
                    if (!is_array($object->caminho)) {
                        $object->caminho = explode(',', $object->caminho);
                    }
                } else {
                    $object->caminho = [];
                }

                $this->form->setData($object);

                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear($param)
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
