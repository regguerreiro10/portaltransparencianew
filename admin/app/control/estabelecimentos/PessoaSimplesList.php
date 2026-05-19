<?php

use Adianti\Widget\Wrapper\TDBSelect;

class PessoaSimplesList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minierp';
    private static $activeRecord = 'Pessoa';
    private static $primaryKey = 'id';
    private static $formName = 'form_PessoaSimplesList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;
    use BuilderDatagridTrait;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

                $basename   = urlencode('pessoa-simples-list.pdf');
        $download   = "download.php?file=app/manual/pessoa-simples-list.pdf&basename={$basename}";

        $manual = "
            <span style='float:right;'>
                <a href='{$download}'
                target='_blank'
                style='text-decoration:none;margin-left:10px;'>
                    <i class='fa fa-question-circle'> </i>
                </a>
            </span>
        "; 

        // define the form title
        $this->form->setFormTitle("Listagem de Estabelecimentos");
        $this->limit = 20;

        $criteria_tipo_cliente_id = new TCriteria();
        $criteria_estado_id = new TCriteria();
        $criteria_cidade_id = new TCriteria();
        $criteria_seguimento_id = new TCriteria();

        $id = new TEntry('id');
        $tipo_cliente_id = new TDBCombo('tipo_cliente_id', 'minierp', 'TipoCliente', 'id', '{nome}','nome asc' , $criteria_tipo_cliente_id );
        $seguimento_id = new TDBSelect('seguimento_id', 'minierp', 'Seguimento', 'id', '{descricao}','descricao asc' , $criteria_seguimento_id );
        $estado_id = new TDBSelect('estado_id', 'minierp', 'Estado', 'id', '{nome} - {sigla}','nome asc' , $criteria_estado_id );
        $cidade_id = new TDBMultiSearch('cidade_id', 'minierp', 'Cidade', 'id', 'nome','nome asc' , $criteria_cidade_id );
        $nome = new TEntry('nome');
        $documento = new TEntry('documento');
        $email = new TEntry('email');
        $fone = new TEntry('fone');
        $ativo = new TCombo('ativo');
        $ativo->addItems(["T"=>"Sim","F"=>"Não"]);
        $ativo->setSize('100%');

        $cidade_id->setMinLength(2);
        $cidade_id->setMask('{nome} - {estado->sigla}');
        $seguimento_id->enableSearch();
        $estado_id->enableSearch();
        $nome->setMaxLength(500);
        $fone->setMaxLength(255);
        $email->setMaxLength(255);
        $documento->setMaxLength(20);
        $documento->setMask('##.###.###/####-##');

        $id->setSize(100);
        $nome->setSize('100%');
        $fone->setSize('100%');
        $email->setSize('100%');
        $cidade_id->setSize('100%');
        $documento->setSize('100%');
        $estado_id->setSize('100%');
        $tipo_cliente_id->setSize('100%');
        $seguimento_id->setSize('100%');
        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Tipo de cliente:", null, '14px', null, '100%'),$tipo_cliente_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Estado:", null, '14px', null, '100%'),$estado_id],[new TLabel("Cidade:", null, '14px', null, '100%'),$cidade_id]);
        $row2->layout = ['col-sm-6',' col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Nome:", null, '14px', null, '100%'),$nome],[new TLabel("Documento/CNPJ:", null, '14px', null, '100%'),$documento]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Email:", null, '14px', null, '100%'),$email],[new TLabel("Fone:", null, '14px', null, '100%'),$fone]);
        $row4->layout = ['col-sm-6','col-sm-6'];
      
        $row5 = $this->form->addFields([new TLabel("Ativo:", null, '14px', null, '100%'),$ativo],[ new TLabel("Seguimento:", null, '14px', null, '100%'),$seguimento_id]);
        $row5->layout = ['col-sm-6','col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');
        $this->datagrid->enableUserProperties(
            'fa fa-cog',
            'btn btn-default',
            new TAction([$this, 'setDatagridProperties'], ['static' => 1])
        );

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_nome = new TDataGridColumn('nome', "Nome", 'left');
        $column_fone = new TDataGridColumn('fone', "Fone", 'left');
        $column_pessoa_rua = new TDataGridColumn('column_pessoa_rua', "Endereço", 'left');
        $column_cidade_id_transformed = new TDataGridColumn('cidade_id', "Cidade / Estado", 'left');
        $column_pessoa_seguimento = new TDataGridColumn('column_pessoa_seguimento', "Segmento", 'left');
        $column__transformed = new TDataGridColumn('', "Demais Localidades", 'left');
        $column_created_at = new TDataGridColumn('created_at', "Data Criação", 'left');
  $column_ativo_transformed = new TDataGridColumn('ativo', "Ativo", 'left');
        $column_pessoa_rua->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $value = 'Não informado!';
            $objects = PessoaEndereco::where('pessoa_id','=',$object->id)->first();
            if ($objects)
            {
               $value = $objects->rua.', '.$objects->numero.' - '.$objects->bairro;
            }
            return $value;
        });
        $column_pessoa_seguimento->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $value = 'Não informado!';
            $objects = SeguimentoPessoa::where('pessoa_id','=',$object->id)->first();
            if ($objects)
            {
                $seguimento = new Seguimento($objects->seguimento_id);
                $value = $seguimento->descricao;
            }
            return $value;
        });
        $column_ativo_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if($value === 'T' || $value === true || $value === 't' || $value === 's')
            {
                return '<span class="label label-success">Sim</span>';
            }

            return '<span class="label label-danger">Não</span>';

        }); 
        $column_cidade_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here

            //code here
            // Código gerado pelo snippet: "Conexão com banco de dados"
            TTransaction::open('minierp');

                        $value='Não informado!';    
                        $objects = PessoaEndereco::where('pessoa_id','=',$object->id)
                                                 ->where('principal','=','T')
                                                  ->load();

                        if ($objects) {
                            foreach ($objects as $obj) {
                               // code...
                               $cid = new Cidade($obj->cidade_id);
                               if ($cid)
                               {
                                   $est = new Estado($cid->estado_id);
                                   if ($est)
                                   {
                                      $value = $cid->nome.' - '.$est->sigla;
                                   }
                               }
                               break;
                            }
                        }

                        return $value;
                        // code

                        TTransaction::close();
                // -----

        });

        $column__transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
                //code here
            // Código gerado pelo snippet: "Conexão com banco de dados"
            TTransaction::open('minierp');

                        $value='';    
                        $objects = PessoaEndereco::where('pessoa_id','=',$object->id)
                                                 ->where('principal','<>','T')
                                                  ->load();

                        if ($objects) {
                            foreach ($objects as $obj) {
                               // code...
                               $cid = new Cidade($obj->cidade_id);
                               if ($cid)
                               {
                                   $est = new Estado($cid->estado_id);
                                   if ($est)
                                   {
                                      $value .= $cid->nome.' - '.$est->sigla.'; <br> ';
                                   }
                               }

                            }
                        }

                        return $value;
                        // code

                        TTransaction::close();
                // -----
        });    
           $column_created_at->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        $order_cidade_id_transformed = new TAction(array($this, 'onReload'));
        $order_cidade_id_transformed->setParameter('order', 'cidade_id');
        $column_cidade_id_transformed->setAction($order_cidade_id_transformed);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_fone);
        $this->datagrid->addColumn($column_created_at);
        $this->datagrid->addColumn($column_pessoa_rua);
        $this->datagrid->addColumn($column_cidade_id_transformed);
        $this->datagrid->addColumn($column_pessoa_seguimento);

      
        $this->applyDatagridProperties();

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de Estabelecimentos {$manual}");
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $panel->getBody()->insert(0, $headerActions);

        

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['PessoaSimplesList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['PessoaSimplesList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

       

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['PessoaSimplesList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['PessoaSimplesList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['PessoaSimplesList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['PessoaSimplesList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_limpar_filtros);
   //     $head_left_actions->add($button_atualizar);

        $head_right_actions->add($dropdown_button_exportar);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            // $container->add(TBreadCrumb::create(["Fornecedores / Clientes","Pessoas"]));
        }

        $container->add($panel);

        parent::add($container);

    }

    public function onDelete($param = null) 
    { 
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);
                $conn = TConnection::open('minierp');

                // instantiates object
                $object = new Pessoa($key, FALSE); 

                $sql = 'select * FROM pedido where cliente_id  =  ' . $param['key'];
                $Recordsudu = $conn->query($sql);
                $count = $Recordsudu->rowCount();

                if ($count>0) 
                {
                    new TMessage('error','Existe registro de pedido para este fornecedor, exclusão nao permitida!');
                    
                } else 
                {
                    // deletes the object from the database
                    $object->delete();

                    $sql = 'delete FROM pessoa_grupo where pessoa_id  =  ' . $param['key'];
                    $Recordsudu = $conn->query($sql);

                    $sql = 'delete FROM seguimento_pessoa where pessoa_id  =  ' . $param['key'];
                    $Recordsudu = $conn->query($sql);

                    $sql = 'delete FROM pessoa_contato where pessoa_id  =  ' . $param['key'];
                    $Recordsudu = $conn->query($sql);

                    $sql = 'delete FROM pessoa_departamento where pessoa_id  =  ' . $param['key'];
                    $Recordsudu = $conn->query($sql);

                    $sql = 'delete FROM pessoa_endereco where pessoa_id = ' . $param['key'];
                    $Recordsudu = $conn->query($sql);

                    new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
                }
                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                //new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
    }
    public function onExportCsv($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    $handler = fopen($output, 'w');
                    TTransaction::open(self::$database);

                    foreach ($objects as $object)
                    {
                        $row = [];
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            var_dump($column);

                            if(($column_name == 'cidade_id') || ($column_name =='nome')){

                                if($column_name == 'cidade_id'){
                                $cidade = new Cidade($object->$column_name);
                                $estado = new Estado($cidade->estado_id);
                                $row[] = mb_convert_encoding($cidade->nome.'-'.$estado->sigla, 'ISO-8859-1', 'UTF-8');

                                }
                                else{
                                $row[] = mb_convert_encoding($object->$column_name, 'ISO-8859-1', 'UTF-8');
                                }
                            }
                            else{

                                    if (isset($object->$column_name))
                                    {
                                        $row[] = is_scalar($object->$column_name) ? $object->$column_name : '';

                                    }
                                    else if (method_exists($object, 'render'))
                                    {
                                        $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                        $row[] = $object->render($column_name);
                                    }
                            }
                        }

                        fputcsv($handler, $row);
                    }

                    fclose($handler);
                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXls($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xls';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $widths = [];
                $titles = [];

                foreach ($this->datagrid->getColumns() as $column)
                {
                    $titles[] = $column->getLabel();
                    $width    = 100;

                    if (is_null($column->getWidth()))
                    {
                        $width = 100;
                    }
                    else if (strpos($column->getWidth(), '%') !== false)
                    {
                        $width = ((int) $column->getWidth()) * 5;
                    }
                    else if (is_numeric($column->getWidth()))
                    {
                        $width = $column->getWidth();
                    }

                    $widths[] = $width;
                }

                $table = new \TTableWriterXLS($widths);
                $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                $table->addStyle('data',   'Helvetica', '10', '',  '#000000', '#FFFFFF', 'LR');

                $table->addRow();

                foreach ($titles as $title)
                {
                    $table->addCell($title, 'center', 'title');
                }

                $this->limit = 0;
                $objects = $this->onReload();

                TTransaction::open(self::$database);
                if ($objects)
                {
                    foreach ($objects as $object)
                    {
                        $table->addRow();
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            //var_dump($column_name, $object->$column_name, $object);
                            //die();

                           if(($column_name == 'cidade_id') || ($column_name =='nome')){

                                if($column_name == 'cidade_id'){
                                $cidade = new Cidade($object->$column_name);
                                $estado = new Estado($cidade->estado_id);
                                $value = mb_convert_encoding($cidade->nome.'-'.$estado->sigla, 'ISO-8859-1', 'UTF-8');

                                }
                                else{
                                $value = mb_convert_encoding($object->$column_name, 'ISO-8859-1', 'UTF-8');
                                }
                            }
                            else{
                                //$value = '';
                                if (isset($object->$column_name))

                                {
                                    $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                                }
                                else if (method_exists($object, 'render'))
                                {
                                    $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                    $value = $object->render($column_name);
                                }
                            }

                            $table->addCell($value, 'center', 'data');
                        }
                    }
                }
                $table->save($output);
                TTransaction::close();

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportPdf($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.pdf';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->onReload();

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . file_get_contents('app/resources/styles-print-list.html') . PdfListHeader::render(__CLASS__) . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = $output;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($object);
                $window->show();
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXml($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xml';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    TTransaction::open(self::$database);

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->{'formatOutput'} = true;
                    $dataset = $dom->appendChild( $dom->createElement('dataset') );

                    foreach ($objects as $object)
                    {
                        $row = $dataset->appendChild( $dom->createElement( self::$activeRecord ) );

                        foreach ($this->datagrid->getColumns() as $column)
                        {

                            $column_name = $column->getName();

                            $column_name_raw = str_replace(['(','{','->', '-','>','}',')', ' '], ['','','_','','','','','_'], $column_name);

                            if(($column_name == 'cidade_id') || ($column_name =='nome')){

                                if($column_name == 'cidade_id'){
                                $cidade = new Cidade($object->$column_name);
                                $estado = new Estado($cidade->estado_id);
                                $value = mb_convert_encoding($cidade->nome.'-'.$estado->sigla, 'ISO-8859-1', 'UTF-8');
                                $row->appendChild($dom->createElement($column_name_raw, $value));

                                }
                                else{
                                $value = mb_convert_encoding($object->$column_name, 'ISO-8859-1', 'UTF-8');

                                }
                            }
                            else{

                                    if (isset($object->$column_name))
                                    {
                                        $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                                        $row->appendChild($dom->createElement($column_name_raw, $value)); 
                                    }
                                    else if (method_exists($object, 'render'))
                                    {
                                        $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                        $value = $object->render($column_name);
                                        $row->appendChild($dom->createElement($column_name_raw, $value));
                                    }
                            }
                        }
                    }

                    $dom->save($output);

                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    public static function onShowCurtainFilters($param = null) 
    {
        try 
        {
            //code here

                        $filter = new self([]);

            $btnClose = new TButton('closeCurtain');
            $btnClose->class = 'btn btn-sm btn-default';
            $btnClose->style = 'margin-right:10px;';
            $btnClose->onClick = "Template.closeRightPanel();";
            $btnClose->setLabel("Fechar");
            $btnClose->setImage('fas:times');

            $filter->form->addHeaderWidget($btnClose);

            $page = new TPage();
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('page-name', 'PessoaSimplesListSearch');
            $page->setProperty('page_name', 'PessoaSimplesListSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }
    public function onRefresh($param = null) 
    {
        $this->onReload([]);
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->tipo_cliente_id) AND ( (is_scalar($data->tipo_cliente_id) AND $data->tipo_cliente_id !== '') OR (is_array($data->tipo_cliente_id) AND (!empty($data->tipo_cliente_id)) )) )
        {

            $filters[] = new TFilter('tipo_cliente_id', '=', $data->tipo_cliente_id);// create the filter 
        }

        if (isset($data->system_user_id) AND ( (is_scalar($data->system_user_id) AND $data->system_user_id !== '') OR (is_array($data->system_user_id) AND (!empty($data->system_user_id)) )) )
        {

            $filters[] = new TFilter('system_user_id', '=', $data->system_user_id);// create the filter 
        }

        if (isset($data->cidade_id) AND ( (is_scalar($data->cidade_id) AND $data->cidade_id !== '') OR (is_array($data->cidade_id) AND (!empty($data->cidade_id)) )) )
        {

            $filters[] = new TFilter('cidade_id', 'in', $data->cidade_id);// create the filter 
        }

        if (isset($data->nome) AND ( (is_scalar($data->nome) AND $data->nome !== '') OR (is_array($data->nome) AND (!empty($data->nome)) )) )
        {

            $filters[] = new TFilter('nome', 'like', "%{$data->nome}%");// create the filter 
        }

        if (isset($data->documento) AND ( (is_scalar($data->documento) AND $data->documento !== '') OR (is_array($data->documento) AND (!empty($data->documento)) )) )
        {

            $filters[] = new TFilter('documento', 'like', "%{$data->documento}%");// create the filter 
        }

        if (isset($data->email) AND ( (is_scalar($data->email) AND $data->email !== '') OR (is_array($data->email) AND (!empty($data->email)) )) )
        {

            $filters[] = new TFilter('email', 'like', "%{$data->email}%");// create the filter 
        }

        if (isset($data->fone) AND ( (is_scalar($data->fone) AND $data->fone !== '') OR (is_array($data->fone) AND (!empty($data->fone)) )) )
        {

            $filters[] = new TFilter('fone', 'like', "%{$data->fone}%");// create the filter 
        }
        if (isset($data->estado_id) && (
            (is_scalar($data->estado_id) && $data->estado_id !== '') ||
            (is_array($data->estado_id) && !empty($data->estado_id))
        ))
        {
            // Monta a lista de IDs (seguro contra injeção)
            if (is_array($data->estado_id)) {
                $estado_ids = implode(',', array_map('intval', $data->estado_id));
            } else {
                $estado_ids = (int) $data->estado_id;
            }

            $filters[] = new TFilter('id', 'IN',
                "(SELECT pessoa_id 
                FROM pessoa_endereco 
                WHERE cidade_id IN (
                    SELECT id 
                        FROM cidade 
                    WHERE estado_id IN ({$estado_ids})
                )
                )"
            );
        }
           if (isset($data->ativo) AND ( (is_scalar($data->ativo) AND $data->ativo !== '') OR (is_array($data->ativo) AND (!empty($data->ativo)) )) )
        {

            $filters[] = new TFilter('ativo', '=', $data->ativo);// create the filter 
        }
        if (isset($data->seguimento_id) AND ( (is_scalar($data->seguimento_id) AND $data->seguimento_id !== '') OR (is_array($data->seguimento_id) AND (!empty($data->seguimento_id)) )) )
        {
            // o $data->seguimento_id [e um array de IDs]
            $filters[] = new TFilter('id', 'IN', "(SELECT pessoa_id FROM seguimento_pessoa WHERE seguimento_id IN (" . implode(',', array_map('intval', (array)$data->seguimento_id)) . "))");// create the filter
        }
      /*  if (isset($data->cidade_id) AND ( (is_scalar($data->cidade_id) AND $data->cidade_id !== '') OR (is_array($data->cidade_id) AND (!empty($data->cidade_id)) )) )
        {

            $filters[] = new TFilter('taxaadm', 'IN', "(SELECT cidade_id FROM pessoa_endereco WHERE principal='T' AND pessoa_id=".$data->id." AND cidade_id=".$data->cidade_id.")");// create the filter 
        }*/

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
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

            // creates a repository for Pessoa
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }
          //  $criteria->add(new TFilter('id', 'IN', '(SELECT produto_id from departamento_unit where system_unit_id='.TSession::getValue('idunit').' )')); 
            //</blockLine><btnShowCurtainFiltersAutoCode>
            if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }
            //</blockLine></btnShowCurtainFiltersAutoCode>
            $criteria->add(new TFilter('id', 'IN',"(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = " . GrupoPessoa::FORNECEDOR . ")"));
            // $criteria->add(new TFilter('system_unit_id', '=',TSession::getValue('idunit')));
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);


            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

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

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
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

    public static function manageRow($id)
    {
        $list = new self([]);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Pessoa($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }
    public function onSetProject($param = null)
    {
        TTransaction::open(self::$database);
        $object = new Pessoa($param['key']);
        $pedido = Pedido::where('cliente_id', '=', $object->id)->load();
        if ($pedido) 
        {
            TApplication::loadPage('PessoaFormView', 'onShow', ['key' => $param['id']]);

        } 
        else 
        {   
            TApplication::loadPage('PessoaFormFrotasView', 'onShow', ['key' => $param['id']]);
        }
        TTransaction::close();

    }


}

