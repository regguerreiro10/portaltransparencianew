<?php

use Adianti\Base\AdiantiFileSaveTrait;

class ContaDespesasForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private TFieldList $fieldListContaAnexos;
    private stdClass $defaultContaAnexoItem;
    private static $database = 'minierp';
    private static $activeRecord = 'Conta';
    private static $primaryKey = 'id';
    private static $formName = 'form_ContaDespesasForm';

    use AdiantiFileSaveTrait;

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
        $this->form->setFormTitle("Cadastro de conta a pagar");

        $criteria_pessoa_id = new TCriteria();
        $criteria_categoria_id = new TCriteria();
        $criteria_forma_pagamento_id = new TCriteria();

        $filterVar = GrupoPessoa::FORNECEDOR;
        $criteria_pessoa_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = '{$filterVar}')")); 
        $filterVar = TipoConta::DESPESA;
        $criteria_categoria_id->add(new TFilter('tipo_conta_id', '=', $filterVar)); 

        $id = new TEntry('id');
        $pessoa_id = new TDBCombo('pessoa_id', 'minierp', 'Pessoa', 'id', '{nome}','nome asc' , $criteria_pessoa_id );
        $categoria_id = new TDBCombo('categoria_id', 'minierp', 'Categoria', 'id', '{nome}','nome asc' , $criteria_categoria_id );
        $forma_pagamento_id = new TDBCombo('forma_pagamento_id', 'minierp', 'FormaPagamento', 'id', '{nome}','nome asc' , $criteria_forma_pagamento_id );
        $dt_emissao = new TDate('dt_emissao');
        $dt_pagamento = new TDate('dt_pagamento');
        $dt_vencimento = new TDate('dt_vencimento');
        $valor = new TNumeric('valor', '2', ',', '.' );
        $parcela = new TSpinner('parcela');
        $obs = new TText('obs');
        $conta_anexo_id = new THidden('conta_anexo_id[]');
        $conta_anexo_arquivo_atual = new THidden('conta_anexo_arquivo_atual[]');
        $conta_anexo_tipo_anexo_id = new TDBCombo('conta_anexo_tipo_anexo_id[]', 'minierp', 'TipoAnexo', 'id', '{nome}', 'nome asc');
        $conta_anexo_arquivo = new TFile('conta_anexo_arquivo[]');
        $this->fieldListContaAnexos = new TFieldList();

        $this->fieldListContaAnexos->addField(null, $conta_anexo_id, []);
        $this->fieldListContaAnexos->addField(null, $conta_anexo_arquivo_atual, []);
        $this->fieldListContaAnexos->addField(new TLabel("Tipo", null, '14px', null), $conta_anexo_tipo_anexo_id, ['width' => '30%']);
        $this->fieldListContaAnexos->addField(new TLabel("Arquivo", null, '14px', null), $conta_anexo_arquivo, ['width' => '70%']);

        $this->fieldListContaAnexos->width = '100%';
        $this->fieldListContaAnexos->class .= ' conta-despesas-anexos-list';
        $this->fieldListContaAnexos->setFieldPrefix('conta_anexo');
        $this->fieldListContaAnexos->name = 'fieldListContaAnexos';
        $this->fieldListContaAnexos->setRemoveAction(null, 'fas:times #dd5a43', "Excluir");
        $this->fieldListContaAnexos->addButtonFunction('contaDespesasAbrirAnexo(this)', 'fas:external-link-alt #0d6efd', 'Abrir anexo');

        $this->defaultContaAnexoItem = new stdClass();

        $this->form->addField($conta_anexo_id);
        $this->form->addField($conta_anexo_arquivo_atual);
        $this->form->addField($conta_anexo_tipo_anexo_id);
        $this->form->addField($conta_anexo_arquivo);

        $pessoa_id->addValidation("Pessoa", new TRequiredValidator()); 
        $categoria_id->addValidation("Categoria", new TRequiredValidator()); 
        $forma_pagamento_id->addValidation("Forma de pagamento", new TRequiredValidator()); 

        $id->setEditable(false);
        $parcela->setRange(1, 2000, 1);
        $pessoa_id->enableSearch();
        $categoria_id->enableSearch();
        $forma_pagamento_id->enableSearch();
        $conta_anexo_arquivo->enableFileHandling();

        $dt_emissao->setMask('dd/mm/yyyy');
        $dt_pagamento->setMask('dd/mm/yyyy');
        $dt_vencimento->setMask('dd/mm/yyyy');

        $dt_emissao->setDatabaseMask('yyyy-mm-dd');
        $dt_pagamento->setDatabaseMask('yyyy-mm-dd');
        $dt_vencimento->setDatabaseMask('yyyy-mm-dd');

        $id->setSize(100);
        $valor->setSize('100%');
        $dt_emissao->setSize(110);
        $parcela->setSize('100%');
        $obs->setSize('100%', 100);
        $pessoa_id->setSize('100%');
        $dt_pagamento->setSize(110);
        $dt_vencimento->setSize(110);
        $categoria_id->setSize('100%');
        $forma_pagamento_id->setSize('100%');
        $conta_anexo_tipo_anexo_id->setSize('100%');
        $conta_anexo_arquivo->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Fornecedor:", '#ff0000', '14px', null, '100%'),$pessoa_id],[new TLabel("Categoria:", '#ff0000', '14px', null, '100%'),$categoria_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Forma de pagamento:", '#ff0000', '14px', null, '100%'),$forma_pagamento_id],[]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Data de emissão:", null, '14px', null, '100%'),$dt_emissao],[new TLabel("Data de pagamento:", null, '14px', null, '100%'),$dt_pagamento],[new TLabel("Data de vencimento:", null, '14px', null, '100%'),$dt_vencimento]);
        $row4->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row5 = $this->form->addFields([new TLabel("Valor:", null, '14px', null, '100%'),$valor],[new TLabel("Parcela:", null, '14px', null, '100%'),$parcela]);
        $row5->layout = ['col-sm-6','col-sm-6'];

        $row6 = $this->form->addFields([new TLabel("Obs:", null, '14px', null, '100%'),$obs]);
        $row6->layout = [' col-sm-12'];

        $row7 = $this->form->addFields([new TLabel("Anexos:", null, '14px', null, '100%'),$this->fieldListContaAnexos]);
        $row7->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['ContaDespesasList', 'onShow']), 'fas:arrow-left #000000');
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

        TScript::create("
            if (!document.getElementById('conta-despesas-anexos-style')) {
                var style = document.createElement('style');
                style.id = 'conta-despesas-anexos-style';
                style.textContent = `
                    .conta-despesas-anexos-list {
                        table-layout: auto;
                        width: 100%;
                    }

                    .conta-despesas-anexos-list th,
                    .conta-despesas-anexos-list td {
                        vertical-align: middle;
                        white-space: nowrap;
                    }

                    .conta-despesas-anexos-list td.field {
                        overflow: hidden;
                    }

                    .conta-despesas-anexos-list tbody tr td.field:nth-child(3) {
                        width: 220px;
                        min-width: 220px;
                        max-width: 220px;
                    }

                    .conta-despesas-anexos-list tbody tr td.field:nth-child(4) {
                        width: auto;
                        min-width: 420px;
                        max-width: 520px;
                    }

                    .conta-despesas-anexos-list tbody tr td:nth-last-child(1),
                    .conta-despesas-anexos-list tbody tr td:nth-last-child(2) {
                        width: 42px;
                        min-width: 42px;
                        max-width: 42px;
                        padding-left: 4px;
                        padding-right: 4px;
                        text-align: center;
                        overflow: visible;
                    }

                    .conta-despesas-anexos-list .div_file {
                        display: flex !important;
                        align-items: center;
                        gap: 8px;
                        width: 100% !important;
                        max-width: 100%;
                        overflow: hidden;
                    }

                    .conta-despesas-anexos-list .div_file input[type='file'] {
                        flex: 0 0 170px;
                        width: 170px;
                        min-width: 170px;
                        max-width: 170px;
                        box-sizing: border-box;
                    }

                    .conta-despesas-anexos-list .div_file a {
                        display: block !important;
                        flex: 1 1 auto;
                        min-width: 0;
                        width: auto;
                        max-width: 100%;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        margin: 0 !important;
                    }

                    .conta-despesas-anexos-list .btn {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        width: 34px;
                        min-width: 34px;
                        height: 34px;
                        padding: 0;
                    }
                `;
                document.head.appendChild(style);
            }
        ");

        TScript::create("
            function contaDespesasAbrirAnexo(button) {
                var data = tfieldlist_get_row_data(button);
                var file = data.conta_anexo_arquivo_atual || '';

                if (!file) {
                    __adianti_message('Aviso', 'Esta linha ainda nao possui anexo salvo.');
                    return;
                }

                file = file.replace(/^\\/+/, '');
                window.open('download.php?file=' + encodeURIComponent(file), '_blank');
            }
        ");
    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Conta(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->tipo_conta_id = TipoConta::DESPESA;

            $object->store(); // save the object 

            $this->storeContaAnexos($object);

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
            TApplication::loadPage('ContaDespesasList', 'onShow', $loadPageParam); 

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

                $object = new Conta($key); // instantiates the Active Record 

                $this->loadContaAnexos($object);

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
        $this->loadContaAnexos();

    }

    public function onShow($param = null)
    {
        $this->loadContaAnexos();

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

    public  function onQuitar($param = null) 
    {
        try 
        {
            $this->onEdit($param);

            $this->form->getField('pessoa_id')->setEditable(true);
            $this->form->getField('categoria_id')->setEditable(false);
            $this->form->getField('valor')->setEditable(false);
            $this->form->getField('dt_vencimento')->setEditable(false);
            $this->form->getField('dt_emissao')->setEditable(false);
            $this->form->getField('parcela')->setEditable(false);

            $this->form->getField('dt_pagamento')->setValue(date('d/m/Y'));

            $this->form->setFormTitle("Quitar conta a pagar");

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    private function loadContaAnexos(?Conta $conta = null): void
    {
        $this->fieldListContaAnexos->addHeader();

        if ($conta && !empty($conta->id))
        {
            $criteria = new TCriteria();
            $criteria->add(new TFilter('conta_id', '=', $conta->id));
            $criteria->add(new TFilter('deleted_at', 'is', null));

            $items = ContaAnexo::getObjects($criteria);

            if ($items)
            {
                foreach ($items as $item)
                {
                    $detail = new stdClass();
                    $detail->conta_anexo_id = $item->id;
                    $detail->conta_anexo_tipo_anexo_id = $item->tipo_anexo_id;
                    $detail->conta_anexo_arquivo_atual = $item->arquivo;
                    $detail->conta_anexo_arquivo = $this->encodeFileValue($item->arquivo, $item->id);

                    $this->fieldListContaAnexos->addDetail($detail);
                }

                $this->fieldListContaAnexos->addCloneAction(null, 'fas:plus #69aa46', "Adicionar");
                return;
            }
        }

        $this->fieldListContaAnexos->addDetail($this->defaultContaAnexoItem);
        $this->fieldListContaAnexos->addCloneAction(null, 'fas:plus #69aa46', "Adicionar");
    }

    private function storeContaAnexos(Conta $conta): void
    {
        $rows = $this->fieldListContaAnexos->getPostData();
        $submittedIds = [];

        foreach ($rows as $row)
        {
            $id = !empty($row->id) ? (int) $row->id : null;
            $tipoAnexoId = !empty($row->tipo_anexo_id) ? (int) $row->tipo_anexo_id : null;
            $arquivo = $row->arquivo ?? null;
            $arquivoAtual = $row->arquivo_atual ?? null;

            if (!$id && !$tipoAnexoId && !$arquivo)
            {
                continue;
            }

            if (!$tipoAnexoId)
            {
                throw new Exception('Informe o tipo do anexo.');
            }

            if (!$id && !$arquivo)
            {
                throw new Exception('Selecione um arquivo para o novo anexo.');
            }

            if ($id && !$arquivo && !$arquivoAtual)
            {
                throw new Exception('Selecione um arquivo para o anexo.');
            }

            if ($id)
            {
                $anexo = new ContaAnexo($id);
                $submittedIds[] = $id;
            }
            else
            {
                $anexo = new ContaAnexo();
                $anexo->conta_id = $conta->id;
            }

            $anexo->conta_id = $conta->id;
            $anexo->tipo_anexo_id = $tipoAnexoId;

            if ($arquivo)
            {
                $anexo->store();

                $dataFile = new stdClass();
                $dataFile->arquivo = $arquivo;
                $this->saveFile($anexo, $dataFile, 'arquivo', 'app/anexos');
            }
            else
            {
                $anexo->arquivo = $arquivoAtual;
                $anexo->store();
            }

            if (!$id)
            {
                $submittedIds[] = (int) $anexo->id;
            }
        }

        $criteria = new TCriteria();
        $criteria->add(new TFilter('conta_id', '=', $conta->id));
        $existingItems = ContaAnexo::getObjects($criteria);

        foreach ($existingItems as $existing)
        {
            if (!in_array((int) $existing->id, $submittedIds, true))
            {
                $existing->delete();
            }
        }
    }

    private function encodeFileValue(?string $file, ?int $id = null): ?string
    {
        if (empty($file))
        {
            return null;
        }

        return urlencode(json_encode([
            'fileName' => $file,
            'idFile' => $id,
            'newFile' => false,
            'delFile' => false,
        ]));
    }

}
