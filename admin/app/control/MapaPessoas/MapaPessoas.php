<?php
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Wrapper\TDBUniqueSearch;

class MapaPessoas extends TPage
{
    private $form;
    private $divMapa;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_maps');
           $basename   = urlencode('mapa-pessoas.pdf');
$download   = "download.php?file=app/manual/mapa-pessoas.pdf&basename={$basename}";

$manual = "
    <span style='float:right;'>
        <a href='{$download}'
           target='_blank'
           style='text-decoration:none;margin-left:10px;'>
            <i class='fa fa-question-circle'> </i>
        </a>
    </span>
"; 
        $this->form->setFormTitle("Localize no mapa {$manual}");

        // Filtros com critérios para valores distintos
        $criteria_estado = new TCriteria;
        $criteria_cidade = new TCriteria;
        $criteria_bairro = new TCriteria;
        $criteria_nome = new TCriteria;
        $criteria_nomerota = new TCriteria();

           $criteria_system_unit_id = new TCriteria();
        $criteria_departamento_unit_id = new TCriteria();
  $filterVar = TSession::getValue('idunit');
        $criteria_system_unit_id->add(new TFilter('id', '=', $filterVar)); 
        // $filterVar = TSession::getValue('idunit');
        // $criteria_departamento_unit_id->add(new TFilter('system_unit_id', '=', $filterVar)); 
        //Campos
        $estado = new TDBSelect('estado_nome', 'minierp', 'Estado', 'id', '{nome}', 'nome ASC', $criteria_estado);
        $cidade = new TDBSelect('cidade_id', 'minierp', 'Cidade', 'id', '{nome}', 'nome ASC', $criteria_cidade);
        $bairro = new TDBSelect('bairro', 'minierp', 'PessoaEndereco', 'bairro', '{bairro}', 'bairro ASC', $criteria_bairro);
        $nome   = new TDBSelect('nome', 'minierp', 'PessoaEndereco', 'nome', '{nome}', 'nome ASC', $criteria_nome);
        $nomerota = new TDBUniqueSearch('nomerota', 'minierp', 'Pessoa', 'nome', 'nome','nome asc' , $criteria_nomerota );
        $system_unit_id = new TDBCombo('system_unit_id', 'minierp', 'SystemUnit', 'id', '{name}','name asc' , $criteria_system_unit_id );
        // $departamento_unit_id = new TDBCombo('departamento_unit_id', 'minierp', 'DepartamentoUnit', 'id', '{name}','name asc' , $criteria_departamento_unit_id );

        $estado->enableSearch();
        $cidade->enableSearch();
        $bairro->enableSearch();
        $nome->enableSearch();
            $system_unit_id->enableSearch();
            $nomerota->enableSearch();
        // $departamento_unit_id->enableSearch();
          $system_unit_id->setSize('100%');
        // $departamento_unit_id->setSize('100%');

        $nome->setSize('100%', 70);
        $nomerota->setSize('100%', 70);
        $bairro->setSize('100%', 70);
        $cidade->setSize('100%', 70);
        $estado->setSize('100%', 70);

          $tab_67410dd86de9d = new BootstrapFormBuilder('tab_67410dd86de9d');
        $this->tab_67410dd86de9d = $tab_67410dd86de9d;
        $tab_67410dd86de9d->setProperty('style', 'border:none; box-shadow:none;');

        $tab_67410dd86de9d->appendPage("Traçar rota entre Unidade e a Rede Credenciada");

        $tab_67410dd86de9d->addFields([new THidden('current_tab_tab_67410dd86de9d')]);
        $tab_67410dd86de9d->setTabFunction("$('[name=current_tab_tab_67410dd86de9d]').val($(this).attr('data-current_page'));");

        $tab_67410dd86de9d->addFields([new TLabel('Unidade:'), $system_unit_id]);
        $tab_67410dd86de9d->addFields([new TLabel('Nome:'), $nomerota]);
   //     $this->form->addContent([$tab_67410dd86de9d]);
 
        $tab_67410dd86de9d->appendPage("Filtro redes credenciadas");
        $tab_67410dd86de9d->addFields([new TLabel('Estado:'), $estado]);
        $tab_67410dd86de9d->addFields([new TLabel('Cidade:'), $cidade]);
        $tab_67410dd86de9d->addFields([new TLabel('Bairro:'), $bairro]);
        $tab_67410dd86de9d->addFields([new TLabel('Nome:'), $nome]);


        $this->form->addContent([$tab_67410dd86de9d]);

        

        $estado->setChangeAction(new TAction([$this, 'onChangeEstado']));
        $cidade->setChangeAction(new TAction([$this, 'onChangeCidade']));
        $bairro->setChangeAction(new TAction([$this, 'onChangeBairro']));

        // Botões
        $this->form->addAction('Filtrar', new TAction([$this, 'onFilter'], ['static' => 1]), 'fa:search blue');
        $this->form->addAction('Limpar Filtros', new TAction([$this, 'onClear']), 'fa:eraser red');
      
        // Div que receberá o mapa
        $this->divMapa = new TElement('div');
        $this->divMapa->id = 'mapa-container';
        $this->divMapa->style = 'width: 100%; height: 600px; margin-top:20px; border-radius: 15px;';

        $botaoToggle = new TElement('button');
        $botaoToggle->type = 'button';
        $botaoToggle->add('🔽 Mostrar/Ocultar Filtros');
        $botaoToggle->style = 'width: 100%; height: 50px; margin: 10px; padding: 5px 10px; font-weight: bold; border-radius: 10px;';
        $botaoToggle->onclick = "toggleFiltros()";

        $filtrosWrapper = new TElement('div');
        $filtrosWrapper->id = 'filtros-wrapper';
        $filtrosWrapper->add($this->form);
        $filtrosWrapper->style = 'display: none';

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($botaoToggle);
        $container->add($filtrosWrapper);
        // $container->add($this->form);
        $container->add($this->divMapa);

        parent::add($container);

        TScript::create("
            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCZMwfV3zdFBNpnYn62PQ_cdXLZWyHhhPI&libraries=marker';
            script.async = true;
            document.head.appendChild(script);
        ");

        TScript::create("
            function toggleFiltros() {
                const filtros = document.getElementById('filtros-wrapper');
                if (filtros.style.display === 'none') {
                    filtros.style.display = 'block';
                } else {
                    filtros.style.display = 'none';
                }
            }
        ");

        // Exibir todos os marcadores inicialmente
        $this->onFilter([]);
    }

    public function onFilter($param)
    {
        try {
            TTransaction::open('minierp');

            $criteria = new TCriteria;

            $tracarrota = false;
            if (!empty($param['nomerota']) && !empty($param['system_unit_id'])) {
                $tracarrota = true;
                $sunit = new SystemUnit($param['system_unit_id']);
                $longitudesunit = 0;
                $latitudesunit = 0;
                if ($sunit) {
                    $longitudesunit = (float) ($sunit->longitude ?? 0);
                    $latitudesunit = (float) ($sunit->latitude ?? 0);
                        $nomeOrigem     = $sunit->name ?? 'Origem';

                }
                $pessoa = Pessoa::where('nome','=',$param['nomerota'])->first();
                $pes = PessoaEndereco::where('pessoa_id', '=',$pessoa->id)->first();
                $longitudepes = 0;
                $latitudepes = 0;
                if ($pes) {
                    $longitudepes = (float) ($pes->longitude ?? 0);
                    $latitudepes = (float) ($pes->latitude ?? 0);
                        $nomeDestino  = $pessoa->nome ?? 'Destino';

                }
                //como traço a rota?
                 // 👉 Traçar rota aqui
                if (
                        !empty($latitudesunit) &&
                        !empty($longitudesunit) &&
                        !empty($latitudepes) &&
                        !empty($longitudepes) &&
                        (
                            $latitudesunit != $latitudepes ||
                            $longitudesunit != $longitudepes
                        )
                    ) {
            // Fecha transação (não vamos mais usar o banco aqui)
                    TTransaction::close();

                    // Monta URL de rota do Google Maps (sem API, sem billing)
                    $origin      = $latitudesunit . ',' . $longitudesunit;
                    $destination = $latitudepes   . ',' . $longitudepes;

                    $url = 'https://www.google.com/maps/dir/?api=1'
                        . '&origin=' . urlencode($origin)
                        . '&destination=' . urlencode($destination)
                        . '&travelmode=driving';

                        
                    // Abre em nova aba / janela
                    $js = "window.open('{$url}', '_blank');";
                    TScript::create($js);

                    return;
                } else {
                    throw new Exception('Não foi possível traçar a rota: informe latitude/longitude da origem e do destino, e verifique se são diferentes.');
                }
            } else {
                $tracarrota = false;
                if (!empty($param['estado_nome']) && is_array($param['estado_nome'])) {
                    $ids = implode(',', array_map('intval', $param['estado_nome']));
                    
                    $criteria->add(
                        new TFilter(
                            'cidade_id',
                            'IN',
                            "(SELECT cidade_id FROM pessoa_endereco pe
                            INNER JOIN cidade c ON c.id = pe.cidade_id
                            INNER JOIN estado e ON e.id = c.estado_id
                            WHERE e.id IN ({$ids}))"
                        )
                    );
                }

                if (!empty($param['cidade_id'])) {
                    $criteria->add(new TFilter('cidade_id', 'in', $param['cidade_id']));
                }
                if (!empty($param['bairro'])) {
                    $criteria->add(new TFilter('bairro', 'in', $param['bairro']));
                }
                if (!empty($param['nome'])) {
                    $criteria->add(new TFilter('nome', 'in', $param['nome']));
                }

                $repo = new TRepository('PessoaEndereco');
                $locais = $repo->load($criteria);

                $marcadores = '';
                $latitude = [];
                $longitude = [];
                foreach ($locais as $local) {
                    $lat = (float) ($local->latitude ?? 0);
                    $lon = (float) ($local->longitude ?? 0);


                    if(!empty($lat) && !empty($lon) && is_numeric($lat) && is_numeric($lon))    
                    {
                        $nome = htmlspecialchars($local->nome ?? '', ENT_QUOTES, 'UTF-8');
                        // $url = "index.php?class=PedidoFrotasForm&method=onShow&idforn=" . urlencode($local->id);
                        $marcadores .= "<gmp-advanced-marker position=\"{$lat},{$lon}\" title=\"{$nome};\"></gmp-advanced-marker>\n";

                        $latitude [] = (float) $lat;
                        $longitude [] = (float) $lon;
                    }
                }
                
                if(count($latitude) > 0 && count($longitude) > 0)
                {
                        // $mediaLat = array_sum($latitude) / count($latitude);
                        // $mediaLon = array_sum($longitude) / count($longitude);
                        $zoom = '7';
                    }
                else{
                        // Sem resultados: centro padrão
                        // $mediaLat = '-15.77972';
                        // $mediaLon = '-47.92972';
                        $zoom = '4';
                        // $marcadores = '<gmp-advanced-marker position="-15.77972,-47.92972" title="Nenhum local encontrado"></gmp-advanced-marker>';
                        new TMessage('info', 'Nesta localização não existem marcadores.');
                }

                TTransaction::close();

                $htmlMapa = <<<HTML
                    <gmp-map
                        center="-15.8025661, -56.1231608"
                        zoom="{$zoom}"
                        map-id="DEMO_MAP_ID"
                        style="height: 100% border-radius: 10px"
                    >
                        {$marcadores}
                    </gmp-map>
                    <!-- <script async
                        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZMwfV3zdFBNpnYn62PQ_cdXLZWyHhhPI&libraries=marker">
                    </script> -->
                HTML;

                $js = <<<JS
                    document.getElementById('mapa-container').innerHTML = `$htmlMapa`;
                JS;

                TScript::create($js);
            }

            

        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onClear()
    {
        // Limpa os filtros
        TForm::clearFields('form_maps');
        // Recarrega todos os dados
        $this->onFilter([]);
    }

    public function onShow($param = null)
    {
        $this->onFilter([]);
    }

    public static function onChangeEstado($param)
    {
        try {
            TTransaction::open('minierp');

            $items_cidade = [];

            if (!empty($param['estado_nome'])) {
                $estado_ids = is_array($param['estado_nome']) ? $param['estado_nome'] : [$param['estado_nome']];
                $estado_ids = array_map('intval', $estado_ids);

                $criteria = new TCriteria;
                $criteria->add(new TFilter('estado_id', 'IN', $estado_ids)); // aqui a correção
                $criteria->setProperty('order', 'nome');

                $items_cidade = Cidade::getIndexedArray('id', 'nome', $criteria);
            }

            $formdata = new stdClass;
            $formdata->cidade_id = '';
            $formdata->bairro = '';
            $formdata->nome = '';
            TForm::sendData('form_maps', $formdata);

            $script = "let select = document.querySelector('[name=cidade_id]');
                    select.innerHTML = '';";
            foreach ($items_cidade as $id => $nome) {
                $script .= "select.add(new Option('{$nome}', '{$id}'));";
            }

            TScript::create($script);

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onChangeCidade($param)
    {
        try {
            TTransaction::open('minierp');

            $items_bairro = [];

            if (!empty($param['cidade_id'])) {
                $criteria = new TCriteria;
                $criteria->add(new TFilter('cidade_id', 'IN', $param['cidade_id']));
                $criteria->setProperty('order', 'bairro');

                $items_bairro = PessoaEndereco::getIndexedArray('bairro', 'bairro', $criteria);
            }

            $formdata = new stdClass;
            $formdata->bairro = '';
            $formdata->nome = '';
            TForm::sendData('form_maps', $formdata);

            $script = "let select = document.querySelector('[name=bairro]');
                    select.innerHTML = '';";

            foreach ($items_bairro as $bairro => $rotulo) {
                $script .= "select.add(new Option('{$rotulo}', '{$bairro}'));";
            }

            TScript::create($script);

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }


    public static function onChangeBairro($param)
    {
        try {
            TTransaction::open('minierp');

            $items_nomes = [];

            if (!empty($param['bairro']) && !empty($param['cidade_id'])) {
                $criteria = new TCriteria;
                $criteria->add(new TFilter('bairro', 'IN', $param['bairro']));
                $criteria->add(new TFilter('cidade_id', 'IN', $param['cidade_id']));
                $criteria->setProperty('order', 'nome');

                $items_nomes = PessoaEndereco::getIndexedArray('nome', 'nome', $criteria);
            }

            $formdata = new stdClass;
            $formdata->nome = '';
            TForm::sendData('form_maps', $formdata);

            $script = "let select = document.querySelector('[name=nome]');
                    select.innerHTML = '';";

            foreach ($items_nomes as $nome => $rotulo) {
                $script .= "select.add(new Option('{$rotulo}', '{$nome}'));";
            }

            TScript::create($script);

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

   
}

?>
