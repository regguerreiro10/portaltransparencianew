<?php

use Adianti\Database\TTransaction;

class PessoaList extends TPage

{

    private $form; // form

    private $datagrid; // listing

    private $pageNavigation;

    private $loaded;

    private $filter_criteria;

    private static $database = 'minierp';

    private static $activeRecord = 'Pessoa';

    private static $primaryKey = 'id';

    private static $formName = 'form_PessoaList';

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

 $basename   = urlencode('estabelecimento-list.pdf');
$download   = "download.php?file=app/manual/estabelecimento-list.pdf&basename={$basename}";

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

        $this->form->setFormTitle("Listagem de Estabelecimentos {$manual}");

        $this->limit = 20;



        $criteria_tipo_cliente_id = new TCriteria();
        $criteria_tipo_cliente_id->add(new TFilter('sigla', '=', 'J'));

        $criteria_estado_id = new TCriteria();

        $criteria_cidade_id = new TCriteria();

        $criteria_system_unit_id = new TCriteria();
        $criteria_system_unit_id->add(new TFilter('id', '=', TSession::getValue('idunit'))); 
        $criteria_seguimento_id = new TCriteria();


        $id = new TEntry('id');

        $tipo_cliente_id = new TDBCombo('tipo_cliente_id', 'minierp', 'TipoCliente', 'id', '{nome}','nome asc' , $criteria_tipo_cliente_id );

        $estado_id = new TDBSelect('estado_id', 'minierp', 'Estado', 'id', '{nome} - {sigla}','nome asc' , $criteria_estado_id );

        $cidade_id = new TDBMultiSearch('cidade_id', 'minierp', 'Cidade', 'id', 'nome','nome asc' , $criteria_cidade_id );

        $system_unit_id = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_system_unit_id );
        $seguimento_id = new TDBSelect('seguimento_id', 'minierp', 'Seguimento', 'id', '{descricao}','descricao asc' , $criteria_seguimento_id );

        $nome = new TEntry('nome');

        $documento = new TEntry('documento');

        $email = new TEntry('email');

        $fone = new TEntry('fone');

        $ativo = new TCombo('ativo');

        $selo = new TEntry('selo');





        $ativo->addItems(["T"=>"Sim","F"=>"Não"]);

          $ativo->setSize('100%');



        $cidade_id->setMinLength(2);
        $cidade_id->setMask('{nome} - {estado->sigla}');

                $estado_id->enableSearch();
        $seguimento_id->enableSearch();



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

        $column_documento = new TDataGridColumn('documento', "CNPJ", 'left');

        $column_nome = new TDataGridColumn('nome', "Nome", 'left');


        $column_pessoa_rua = new TDataGridColumn('column_pessoa_rua', "Endereço", 'left');

        $column_cidade_id_transformed = new TDataGridColumn('cidade_id', "Cidade / Estado", 'left');

        $column_pessoa_seguimento = new TDataGridColumn('column_pessoa_seguimento', "Segmento", 'Left');

        $column__transformed = new TDataGridColumn('', "Demais Localidades", 'left');

        $column_created_at = new TDataGridColumn('created_at', "Data Criação", 'left');

        $column_fone = new TDataGridColumn('fone', "Fone", 'left');

        $column_ativo_transformed = new TDataGridColumn('ativo', "Ativo", 'left');

        $column_pessoa_endereco_pessoa_selo1 = new TDataGridColumn('selo', "Selo Ambiental", 'center');

        $column_pessoa_rua->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open('minierp');
            $value='Não informado!';
            $objects = PessoaEndereco::where('pessoa_id','=',$object->id)
                                      ->first();
            if ($objects) {
               // code...
               $value = $objects->rua.', '.$objects->numero.' - '.$objects->bairro;
            }
            TTransaction::close();
            return $value;


        });  
         $column_pessoa_seguimento->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open('minierp');
            $value='Não informado!';
                $objects = SeguimentoPessoa::where('pessoa_id','=',$object->id)
                                        ->first();
            if ($objects) {
                $seguimento = new Seguimento($objects->seguimento_id);
               // code...
               $value = $seguimento->descricao;
            }
            TTransaction::close();
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

        $column_pessoa_endereco_pessoa_selo1->setTransformer(function($value)
        {
             if ($value === 1) {
                return '<i class="fab fa-pagelines" style="color:#28a745;"></i>';                  // ou retorne $icon direto
            }
            return ''; // nada quando não for 1
            
        });



        $column_fone->setTransformer(function($value, $object)
        {
            $telefone = trim((string) $value);
            if ($telefone !== '')
            {
                return $telefone;
            }

            TTransaction::open('minierp');
            $contato = PessoaContato::where('pessoa_id', '=', $object->id)->first();
            $telefoneContato = trim((string) ($contato->telefone ?? ''));
            TTransaction::close();

            return $telefoneContato !== '' ? $telefoneContato : 'Não informado!';
        });

        $order_id = new TAction(array($this, 'onReload'));

        $order_id->setParameter('order', 'id');

        $column_id->setAction($order_id);

        $order_cidade_id_transformed = new TAction(array($this, 'onReload'));

        $order_cidade_id_transformed->setParameter('order', 'cidade_id');

        $column_cidade_id_transformed->setAction($order_cidade_id_transformed);



        $this->datagrid->addColumn($column_id);

        $this->datagrid->addColumn($column_documento);
        $this->datagrid->addColumn($column_nome);

        $this->datagrid->addColumn($column_fone);

        $this->datagrid->addColumn($column_created_at);
        $this->datagrid->addColumn($column_pessoa_rua);

        $this->datagrid->addColumn($column_cidade_id_transformed);
        $this->datagrid->addColumn($column_pessoa_seguimento);


        $this->datagrid->addColumn($column__transformed);

        $this->datagrid->addColumn($column_ativo_transformed);

        $this->datagrid->addColumn($column_pessoa_endereco_pessoa_selo1);



        $action_onShow = new TDataGridAction(array($this, 'onSetProject'));

        $action_onShow->setUseButton(false);

        $action_onShow->setButtonClass('btn btn-default btn-sm');

        $action_onShow->setLabel("");

        $action_onShow->setImage('fas:search-plus #673AB7');

        $action_onShow->setField(self::$primaryKey);



        $this->datagrid->addAction($action_onShow);



        $action_onEdit = new TDataGridAction(array('PessoaForm', 'onEdit'));

        $action_onEdit->setUseButton(false);

        $action_onEdit->setButtonClass('btn btn-default btn-sm');

        $action_onEdit->setLabel("Editar");

        $action_onEdit->setImage('far:edit #478fca');

        $action_onEdit->setField(self::$primaryKey);



        $this->datagrid->addAction($action_onEdit);



        $action_onDelete = new TDataGridAction(array('PessoaList', 'onDelete'));

        $action_onDelete->setUseButton(false);

        $action_onDelete->setButtonClass('btn btn-default btn-sm');

        $action_onDelete->setLabel("Excluir");

        $action_onDelete->setImage('fas:trash-alt #dd5a43');

        $action_onDelete->setField(self::$primaryKey);



        $this->datagrid->addAction($action_onDelete);



          $action_onTaxas = new TDataGridAction(array('TaxasPessoaList', 'onSetProject'));

        $action_onTaxas->setUseButton(false);

        $action_onTaxas->setButtonClass('btn btn-default btn-sm');

        $action_onTaxas->setLabel("Taxas");

        $action_onTaxas->setImage('fas:file-invoice-dollar #e8871e');

        $action_onTaxas->setField(self::$primaryKey);



        $this->datagrid->addAction($action_onTaxas);





        //      $action_onAntecipacao = new TDataGridAction(array('AntecipacaoList', 'onShow'));

        // $action_onAntecipacao->setUseButton(false);

        // $action_onAntecipacao->setButtonClass('btn btn-default btn-sm');

        // $action_onAntecipacao->setLabel("Antecipacao");

        // $action_onAntecipacao->setImage('fas:hand-holding-usd rgb(3, 15, 34)');

        // $action_onAntecipacao->setField(self::$primaryKey);



        // $this->datagrid->addAction($action_onAntecipacao);

        // create the datagrid model
$this->applyDatagridProperties();

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



        $button_cadastrar = new TButton('button_button_cadastrar');

        $button_cadastrar->setAction(new TAction(['PessoaForm', 'onShow']), "Cadastrar");

        $button_cadastrar->addStyleClass('btn-default');

        $button_cadastrar->setImage('fas:plus #69aa46');



        $this->datagrid_form->addField($button_cadastrar);



        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');

        $btnShowCurtainFilters->setAction(new TAction(['PessoaList', 'onShowCurtainFilters']), "Filtros");

        $btnShowCurtainFilters->addStyleClass('btn-default');

        $btnShowCurtainFilters->setImage('fas:filter #000000');



        $this->datagrid_form->addField($btnShowCurtainFilters);



        $button_limpar_filtros = new TButton('button_button_limpar_filtros');

        $button_limpar_filtros->setAction(new TAction(['PessoaList', 'onClearFilters']), "Limpar filtros");

        $button_limpar_filtros->addStyleClass('btn-default');

        $button_limpar_filtros->setImage('fas:eraser #f44336');



        $this->datagrid_form->addField($button_limpar_filtros);



        $button_atualizar = new TButton('button_button_atualizar');

        $button_atualizar->setAction(new TAction(['PessoaList', 'onRefresh']), "Atualizar");

        $button_atualizar->addStyleClass('btn-default');

        $button_atualizar->setImage('fas:sync-alt #03a9f4');



        $this->datagrid_form->addField($button_atualizar);



        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');

        $dropdown_button_exportar->setPullSide('right');

        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');

        // $dropdown_button_exportar->addPostAction( "CSV", new TAction(['PessoaList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );

        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['PessoaList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );

        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['PessoaList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );

        $dropdown_button_exportar->addPostAction( "XML", new TAction(['PessoaList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );


           $dropdown_button_importar = new TDropDown("Importar", 'fas:file-upload #000000');
        $dropdown_button_importar->setPullSide('right');
        $dropdown_button_importar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_importar->addPostAction( "XLS", new TAction(['PessoaList', 'onImportarXLS'],['static' => 1]), self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_importar->addPostAction( "MYSQL", new TAction(['PessoaList', 'onImportarMYSQL'],['static' => 1]), self::$formName, 'fas:database  #614caf' );


        $head_left_actions->add($button_cadastrar);

        $head_left_actions->add($btnShowCurtainFilters);

        $head_left_actions->add($button_limpar_filtros);

        $head_left_actions->add($button_atualizar);



        $head_right_actions->add($dropdown_button_exportar);
            $head_right_actions->add($dropdown_button_importar);



        $this->btnShowCurtainFilters = $btnShowCurtainFilters;



        // vertical box container

        $container = new TVBox;

        $container->style = 'width: 100%';

        if(empty($param['target_container']))

        {

          //  $container->add(TBreadCrumb::create(["Estabelecimentos","Estabelecimentos"]));

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

                $tables = [
                    'PedidoFrotas' => ['column' => 'estabelecimento_id', 'alias' => 'Pedido Frotas'],
                    'Pedido' => ['column' => 'cliente_id', 'alias' => 'Pedido Compras'],
                    'Conta' => ['column' => 'pessoa_id', 'alias' => 'Contas a Pagar/Receber'],
                    'Propostas' => ['column' => 'pessoa_id', 'alias' => 'Propostas'],
                    'Multas' => ['column' => 'condutor_id', 'alias' => 'Condutor'],
                    'Veiculos' => ['column' => 'responsavel_id', 'alias' => 'Responsável pelo veículo'],
                    'TaxasPessoa' => ['column' => 'pessoa_id', 'alias' => 'Taxas Pessoa'],
                    'PedidoAsCliente' => ['column' => 'pessoa_id', 'alias' => 'Pessoa do pedido frotas'],
                ];
                foreach ($tables as $table => $info) {
                    $repository = new TRepository($table);
                    $criteria = new TCriteria();
                    $criteria->add(new TFilter($info['column'], '=', $key)); // Corrigido aqui
                    
                    if ($repository->count($criteria) > 0) {
                        throw new Exception("Não é possível excluir este estabelecimento porque existem registros associados em {$info['alias']}.");
                    }
                } 


                // instantiates object

                $object = new Pessoa($key, FALSE); 




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

                             //   $row[] = mb_convert_encoding($cidade->nome.'-'.$estado->sigla, 'ISO-8859-1', 'UTF-8');


                            if(($column_name == 'cidade_id') || ($column_name =='nome')){



                                if($column_name == 'cidade_id'){

                                $cidade = new Cidade($object->$column_name);

                                $estado = new Estado($cidade->estado_id);

                                $row[] = $cidade->nome.'-'.$estado->sigla;



                                }

                                else{

                                $row[] = $object->$column_name;

                                }

                            }
                                elseif (($column_name == 'column_pessoa_rua') || ($column_name =='column_pessoa_rua')){

                                $row[] ='Não informado!';
                                $objects = PessoaEndereco::where('pessoa_id','=',$object->id)
                                                        ->first();
                                if ($objects) {
                                    $row[]  = $objects->rua.', '.$objects->numero.' - '.$objects->bairro;
                                }

                            }

                            elseif (($column_name == 'column_pessoa_seguimento') || ($column_name =='column_pessoa_seguimento')){
                                $row[] ='Não informado!';
                                    $objects = SeguimentoPessoa::where('pessoa_id','=',$object->id)
                                                            ->first();
                                if ($objects) {
                                    $seguimento = new Seguimento($objects->seguimento_id);
                                // code...
                                $row[]  = $seguimento->descricao;
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

                                $value = $cidade->nome.'-'.$estado->sigla;



                                }

                                else{

                                $value = $object->$column_name;

                                }

                            }
                            elseif (($column_name == 'column_pessoa_rua') || ($column_name =='column_pessoa_rua')){

                                $value='Não informado!';
                                $objects = PessoaEndereco::where('pessoa_id','=',$object->id)
                                                        ->first();
                                if ($objects) {
                                    $value = $objects->rua.', '.$objects->numero.' - '.$objects->bairro;
                                }

                            }

                            elseif (($column_name == 'column_pessoa_seguimento') || ($column_name =='column_pessoa_seguimento')){
                                $value='Não informado!';
                                    $objects = SeguimentoPessoa::where('pessoa_id','=',$object->id)
                                                            ->first();
                                if ($objects) {
                                    $seguimento = new Seguimento($objects->seguimento_id);
                                // code...
                                $value = $seguimento->descricao;
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

                                $value = $cidade->nome.'-'.$estado->sigla;

                                $row->appendChild($dom->createElement($column_name_raw, $value));



                                }

                                else{

                                $value = $object->$column_name;



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

            $page->setProperty('page-name', 'PessoaListSearch');

            $page->setProperty('page_name', 'PessoaListSearch');

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

        if(isset($data->system_unit_id) AND ( (is_scalar($data->system_unit_id) AND $data->system_unit_id !== '') OR (is_array($data->system_unit_id) AND (!empty($data->system_unit_id)) )) )
        
        {
            $filters[] = new TFilter('system_users_id', 'IN', '(SELECT id from system_users where system_unit_id='.$data->system_unit_id.' )');}


          if (isset($data->cidade_id) AND ( (is_scalar($data->cidade_id) AND $data->cidade_id !== '') OR (is_array($data->cidade_id) AND (!empty($data->cidade_id)) )) )

        {



            $filters[] = new TFilter('cidade_id', 'in', $data->cidade_id);// create the filter 

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



            $criteria->add(new TFilter('id', 'IN', 

    "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_pessoa_id = " . GrupoPessoa::FORNECEDOR . ")"

));

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

        $pedido = Pedido::where('system_unit_id', '=', TSession::getValue('idunit'))->load();

        if (TSession::getValue('sistema')=='compras') 

        {

            TApplication::loadPage('PessoaFormView', 'onShow', ['key' => $param['id']]);



        } 

        elseif (TSession::getValue('sistema')=='frotas')

        {   

            TApplication::loadPage('PessoaFormFrotasView', 'onShow', ['key' => $param['id']]);

        }

        TTransaction::close();



    }
   private function executarImportacaoMYSQL()
    {
        try
        {
            TTransaction::open('minierp');

            $host ="localhost";
            $user = "root";
            $password = "";
            $db="dbsistema_np3";

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            $mysqli = new mysqli($host, $user, $password, $db);
            $mysqli->set_charset('utf8mb4');

            //departamento
            $querydepartamento = "SELECT *  FROM departamento_cliente dc where cliente_id=".$row['id']." ORDER BY dc.id ASC";
                    $resultdepartamento = $mysqli->query($querydepartamento);

                    while ($rowdepartamento = $resultdepartamento->fetch_assoc()) {
                        $departamentopessoa = new DepartamentoPessoa();
                        $departamentopessoa->pessoa_id = $pessoa->id;
                        $departamento = Departamento::where('idold','=',$rowdepartamento['departamento_id'])->first();
                        if (!$departamento)
                        {
                            continue;
                        }
                        $departamentopessoa->departamento_id = $departamento->id;
                        $departamentopessoa->store();
                    }

            $query = "SELECT c.id, c.nome, c.status, c.empresa, c.cnpj, c.email, c.tel, c.banco1, c.ag1, c.cta1, c.tipo1, c.obs, c.endereco, c.numero, c.bairro, c.cidade, c.uf, c.cel, c.responsavel  FROM clientes c ORDER BY c.id ASC";
            $result = $mysqli->query($query);

            while ($row = $result->fetch_assoc())
            {
                $cnpj = preg_replace('/\D/', '', $row['cnpj']);
                if (strlen($cnpj) != 14)
                {
                    // cnpj inválido, pula (ou lance Exception, se preferir)
                    continue;
                }
                $pessoa = Pessoa::where('documento', '=', $cnpj)->first();
                if ($pessoa)                {
                    // se já existe pessoa com esse CNPJ, atualiza o ID antigo e pula
                    $pessoa->idold = (int) $row['id'];
                    $pessoa->store();
                    continue;
                } else {
                    // se não existe, cria nova pessoa
                    $pessoa = new Pessoa();
                    $pessoa->nome = trim($row['empresa']);
                    $pessoa->documento = $cnpj;
                    $pessoa->email = trim($row['email']);
                    $pessoa->fone = trim($row['tel']);
                    $pessoa->ativo = ($row['status'] == 'ativo') ? 'T' : 'F';
                    $pessoa->tipo_cliente_id = 1;
                    $pessoa->obs = trim($row['obs']);
                    $pessoa->agencia = trim($row['ag1']);
                    $pessoa->conta = trim($row['cta1']);
                    $pessoa->banco = trim($row['banco1']);
                    $pessoa->operacao = trim($row['tipo1']);

                    $pessoa->idold = (int) $row['id'];
                    $pessoa->store();

                    $pessoa_grupo = new PessoaGrupo();
                    $pessoa_grupo->pessoa_id = $pessoa->id;
                    $pessoa_grupo->grupo_pessoa_id = GrupoPessoa::FORNECEDOR;
                    $pessoa_grupo->store();

                    $pessoa_contato = new PessoaContato();
                    $pessoa_contato->pessoa_id = $pessoa->id;
                    $pessoa_contato->nome = trim($row['responsavel']);
                    $pessoa_contato->email = trim($row['email']);
                    $pessoa_contato->telefone = trim($row['cel']);
                    $pessoa_contato->store();

                    $pessoa_endereco = new PessoaEndereco();
                    $pessoa_endereco->pessoa_id = $pessoa->id;
                    $pessoa_endereco->rua = trim($row['endereco']);
                    $pessoa_endereco->numero = trim($row['numero']);
                    $pessoa_endereco->bairro = trim($row['bairro']);
                    $cidade = Cidade::where('idold', '=', trim($row['cidade_id']))->first();
                    if ($cidade)
                    {
                        $pessoa_endereco->cidade_id = $cidade->id;
                    }
                               // cidade e estado serão tratados abaixo
                    $pessoa_endereco->principal = 'T';
                    $pessoa_endereco->store();

                    //segmento
                    $queryseguimento = "SELECT *  FROM cliente_has_seguimento c where cliente_id=".$row['id']." ORDER BY c.id ASC";
                    $resultseguimento = $mysqli->query($queryseguimento);

                    while ($rowseguimento = $resultseguimento->fetch_assoc()) {
                        $seguimentopessoa = new SeguimentoPessoa();
                        $seguimentopessoa->pessoa_id = $pessoa->id;
                        $seguimento = Seguimento::where('idold','=',$rowseguimento['seguimento_id'])->first();
                        if (!$seguimento)
                        {
                            continue;
                        }
                        $seguimentopessoa->seguimento_id = $seguimento->id;
                        $seguimentopessoa->store();
                    }
                  
                    //departamento
                    $querydepartamento = "SELECT *  FROM departamento_cliente dc where cliente_id=".$row['id']." ORDER BY dc.id ASC";
                    $resultdepartamento = $mysqli->query($querydepartamento);

                    while ($rowdepartamento = $resultdepartamento->fetch_assoc()) {
                        $departamentopessoa = new DepartamentoPessoa();
                        $departamentopessoa->pessoa_id = $pessoa->id;
                        $departamento = Departamento::where('idold','=',$rowdepartamento['departamento_id'])->first();
                        if (!$departamento)
                        {
                            continue;
                        }
                        $departamentopessoa->departamento_id = $departamento->id;
                        $departamentopessoa->store();
                    }
                }
                $cidade_nome = trim($row['nome']);

                // busca o UF do estado na base antiga (1x só)
                $queryestado = "SELECT uf FROM estado WHERE id = " . (int) $row['estado'] . " LIMIT 1";
                $resEstado = $mysqli->query($queryestado);
                $rowestado = $resEstado->fetch_assoc();

                if (!$rowestado || empty($rowestado['uf']))
                {
                    // se não achou UF na base antiga, pula (ou lance Exception, se preferir)
                    continue;
                }

                $uf = trim($rowestado['uf']);

                // acha o estado no minierp
                $estado = Estado::where('sigla', '=', $uf)->first();
                if (!$estado)
                {
                    // se o estado ainda não existe no minierp, você pode criar ou pular
                    // aqui vou pular para evitar cidade sem estado
                    continue;
                }

                // agora sim compara com ID real
                $existe = Cidade::where('nome', '=', $cidade_nome)
                                ->where('estado_id', '=', $estado->id)
                                ->first();

                if ($existe)
                {
                    $existe->idold = (int) $row['id'];
                    $existe->store();
                }
                else
                {
                    $novo = new Cidade();
                    $novo->nome       = $cidade_nome;
                    $novo->estado_id  = $estado->id;
                    $novo->codigoibge = null; // ou '' se seu campo não aceita null
                    $novo->idold      = (int) $row['id'];
                    $novo->store();
                }
            }

            $result->free();
            $mysqli->close();

            TTransaction::close();

            TToast::show('success', 'Importação concluída com sucesso!', 'topRight', 'far:check-circle');
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }




     public static function onExibirImportar($object = null)
    {
        try
        {
            return (TSession::getValue('iduser') == 1);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }

        return false;
    }

    public function onImportarMYSQL()
    {
        $form = new TForm('form_auth');
        $form->style = 'padding:20px';

        $senha = new TEntry('senha');
        $senha->setProperty('type', 'password');
        $senha->setSize('100%');

        $form->add(new TLabel('Senha'));
        $form->add($senha);
        $form->addField($senha);

        $action = new TAction([self::class, 'onValidarSenhaImportacao']);

        new TInputDialog(
            'Confirmação de Segurança',
            $form,
            $action,
            'Confirmar'
        );
    }
    public function onValidarSenhaImportacao($param)
    {
        try
        {
            if (empty($param['senha']))
            {
                throw new Exception('Informe a senha');
            }

            // 🔐 validação (exemplo)
            if ($param['senha'] !== '@codeg7')
            {
                throw new Exception('Senha incorreta');
            }

            // ✅ senha correta → executa importação
            $this->executarImportacaoMYSQL();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
     public function onImportarXLS($param = null) 
    {
        try
        {
            
          
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }




}



