<?php

use Adianti\Base\AdiantiFileSaveTrait;

class FinanceiroArquivoForm extends TPage
{
    use AdiantiFileSaveTrait;

    protected BootstrapFormBuilder $form;
    private static $database = 'minierp';
    private static $activeRecord = 'FinanceiroArquivo';
    private static $primaryKey = 'id';
    private static $formName = 'form_FinanceiroArquivoForm';

    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Arquivo financeiro');

        $id = new THidden('id');
        $financeiro_cadastro_id = new TDBCombo('financeiro_cadastro_id', 'minierp', 'FinanceiroCadastro', 'id', '{nome}', 'nome asc');
        $subcategoria_id = new TDBCombo('subcategoria_id', 'minierp', 'FinanceiroSubcategoria', 'id', '{nome} ({ano})', 'ano desc, nome asc');
        $nome_arquivo = new TEntry('nome_arquivo');
        $tipo = new TCombo('tipo');
        $caminho_arquivo = new TFile('caminho_arquivo');
        $link_externo = new TEntry('link_externo');

        $tipo->addItems([
            'arquivo' => 'Arquivo do computador',
            'link' => 'Link externo',
        ]);

        $financeiro_cadastro_id->enableSearch();
        $subcategoria_id->enableSearch();
        $tipo->enableSearch();
        $tipo->setValue('arquivo');
        $caminho_arquivo->enableFileHandling();
        $caminho_arquivo->setAllowedExtensions(['pdf', 'doc', 'docx', 'xls', 'xlsx']);

        $nome_arquivo->addValidation('Nome do arquivo', new TRequiredValidator());
        $tipo->addValidation('Tipo', new TRequiredValidator());

        $financeiro_cadastro_id->setSize('100%');
        $subcategoria_id->setSize('100%');
        $nome_arquivo->setSize('100%');
        $tipo->setSize('100%');
        $caminho_arquivo->setSize('100%');
        $link_externo->setSize('100%');

        $this->form->addFields([$id]);
        $row1 = $this->form->addFields(
            [new TLabel('Cadastro principal:', null, '14px', null, '100%'), $financeiro_cadastro_id],
            [new TLabel('Subcategoria:', null, '14px', null, '100%'), $subcategoria_id]
        );
        $row1->layout = ['col-sm-6', 'col-sm-6'];

        $row2 = $this->form->addFields(
            [new TLabel('Nome do arquivo:', '#ff0000', '14px', null, '100%'), $nome_arquivo],
            [new TLabel('Origem:', '#ff0000', '14px', null, '100%'), $tipo]
        );
        $row2->layout = ['col-sm-8', 'col-sm-4'];

        $row3 = $this->form->addFields(
            [new TLabel('Upload do arquivo (PDF, DOC/DOCX, XLS/XLSX):', null, '14px', null, '100%'), $caminho_arquivo]
        );
        $row3->layout = ['col-sm-12'];

        $row4 = $this->form->addFields(
            [new TLabel('Link externo:', null, '14px', null, '100%'), $link_externo]
        );
        $row4->layout = ['col-sm-12'];

        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btnSave->addStyleClass('btn-primary');
        $this->form->addAction('Voltar', new TAction(['FinanceiroArquivoList', 'onShow']), 'fas:arrow-left #000000');

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel('Fechar');
        $btnClose->setImage('fas:times');
        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);
    }

    public function onSave($param = null)
    {
        try {
            TTransaction::open(self::$database);
            FinanceiroPublicoSchemaHelper::ensureSchema();

            $this->form->validate();

            $data = $this->form->getData();
            $object = !empty($data->id) ? new FinanceiroArquivo((int) $data->id) : new FinanceiroArquivo();
            $object->fromArray((array) $data);

            if (empty($object->financeiro_cadastro_id) && empty($object->subcategoria_id)) {
                throw new Exception('Informe um cadastro principal ou uma subcategoria para o arquivo.');
            }

            if (!empty($object->subcategoria_id) && empty($object->financeiro_cadastro_id)) {
                $subcategoria = new FinanceiroSubcategoria((int) $object->subcategoria_id);
                $object->financeiro_cadastro_id = $subcategoria->financeiro_cadastro_id;
            }

            if ($object->tipo === 'link') {
                if (empty($object->link_externo)) {
                    throw new Exception('Informe o link externo para esse arquivo.');
                }
                $object->caminho_arquivo = null;
                $object->extensao = 'link';
            } else {
                if (empty($data->caminho_arquivo) && empty($object->caminho_arquivo)) {
                    throw new Exception('Envie um arquivo do computador.');
                }
                $object->link_externo = null;
            }

            $now = date('Y-m-d H:i:s');
            $object->updated_at = $now;
            if (empty($object->created_at)) {
                $object->created_at = $now;
            }

            $object->store();

            if ($object->tipo === 'arquivo' && !empty($data->caminho_arquivo)) {
                $this->saveFile($object, $data, 'caminho_arquivo', 'app/files/financeiro_publico');
                $object->extensao = strtolower((string) pathinfo((string) $object->caminho_arquivo, PATHINFO_EXTENSION));
                $object->store();
            }

            $data->id = $object->id;
            $this->form->setData($data);

            TTransaction::close();

            TToast::show('success', 'Arquivo salvo', 'topRight', 'far:check-circle');
            TApplication::loadPage('FinanceiroArquivoList', 'onShow', $param ?? []);
            TScript::create("Template.closeRightPanel();");
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                TTransaction::open(self::$database);
                FinanceiroPublicoSchemaHelper::ensureSchema();
                $object = new FinanceiroArquivo($param['key']);
                $this->form->setData($object);
                TTransaction::close();
            } else {
                $this->form->clear();
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {
        $data = new stdClass;

        if (!empty($param['financeiro_cadastro_id'])) {
            $data->financeiro_cadastro_id = $param['financeiro_cadastro_id'];
        }

        if (!empty($param['subcategoria_id'])) {
            $data->subcategoria_id = $param['subcategoria_id'];
            try {
                TTransaction::open(self::$database);
                FinanceiroPublicoSchemaHelper::ensureSchema();
                $subcategoria = new FinanceiroSubcategoria((int) $param['subcategoria_id']);
                $data->financeiro_cadastro_id = $subcategoria->financeiro_cadastro_id;
                TTransaction::close();
            } catch (Exception $e) {
                TTransaction::rollback();
            }
        }

        if (!empty((array) $data)) {
            $data->tipo = 'arquivo';
            $this->form->setData($data);
        }
    }
}
