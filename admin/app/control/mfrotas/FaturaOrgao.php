  <?php

use Adianti\Registry\TSession;

/**
 * Gera e retorna o conteudo do orcamento HTML para o pedido de frotas
 *
 * @param array $dados Dados necessarios para preencher o orcamento
 * @return string HTML do orcamento
 */
class FaturaOrgao extends TPage
{
    private static function toFloatMoney($v): float
    {
        if (is_null($v) || $v === '') {
            return 0.0;
        }
        if (is_numeric($v)) {
            return (float) $v;
        }
        $s = trim((string) $v);
        if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
            return (float) $s;
        }
        if (strpos($s, ',') !== false) {
            return (float) str_replace(',', '.', $s);
        }
        return (float) $s;
    }

    private static function toMoneyBr($v): string
    {
        return number_format(self::toFloatMoneyForm($v), 2, ',', '.');
    }

    private static function toFloatMoneyForm($v): float
    {
        if (is_string($v)) {
            $s = trim($v);

            if ($s !== '' && ctype_digit($s) && strlen($s) > 2) {
                return ((float) $s) / 100;
            }
        }

        return self::toFloatMoney($v);
    }

    private static function toDateBr($v): string
    {
        $s = trim((string) ($v ?? ''));

        if ($s === '') {
            return '';
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $s)) {
            return $s;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) {
            $dt = DateTime::createFromFormat('Y-m-d', $s);
            return $dt ? $dt->format('d/m/Y') : $s;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $s)) {
            $format = strlen($s) === 19 ? 'Y-m-d H:i:s' : 'Y-m-d H:i';
            $dt = DateTime::createFromFormat($format, $s);
            return $dt ? $dt->format('d/m/Y H:i') : $s;
        }

        try {
            $format = str_contains($s, ':') ? 'd/m/Y H:i' : 'd/m/Y';
            return (new DateTime($s))->format($format);
        } catch (Throwable $e) {
            return $s;
        }
    }

    private static function replaceIsoDatesInText(string $text): string
    {
        return preg_replace_callback(
            '/\b\d{4}-\d{2}-\d{2}(?: \d{2}:\d{2}(?::\d{2})?)?\b/',
            function ($matches) {
                return self::toDateBr($matches[0]);
            },
            $text
        );
    }

    private static function carregarPropostasDaConta(Conta $conta)
    {
        if (empty($conta->pedido_frotas_id)) {
            return [];
        }

        $query = Propostas::where('pedido_frotas_id', '=', $conta->pedido_frotas_id)
            ->where('estado_pedido_frotas_id', 'IN', [8, 13, 18, 20, 24]);

        if (!empty($conta->pessoa_id)) {
            $query->where('pessoa_id', '=', $conta->pessoa_id);
        }

        if (!empty($conta->cidade_id)) {
            $query->where('cidade_id', '=', $conta->cidade_id);
        }

        $propostas = $query->load();

        if (!$propostas && !empty($conta->pessoa_id)) {
            $propostas = Propostas::where('pedido_frotas_id', '=', $conta->pedido_frotas_id)
                ->where('estado_pedido_frotas_id', 'IN', [8, 13, 18, 20, 24])
                ->where('pessoa_id', '=', $conta->pessoa_id)
                ->load();
        }

        if (!$propostas) {
            $propostas = Propostas::where('pedido_frotas_id', '=', $conta->pedido_frotas_id)
                ->where('estado_pedido_frotas_id', 'IN', [8, 13, 18, 20, 24])
                ->load();
        }

        return $propostas ?: [];
    }

     public function gerar($param = null)
     {
        try 
        {
                   $param = (array) ($param ?? []);
                   $param['numero_fatura'] = $param['numero_fatura'] ?? '';
                   $param['data_emissao'] = self::toDateBr($param['data_emissao'] ?? date('d/m/Y H:i'));
                   $param['periodo_apuracao_inicial'] = $param['periodo_apuracao_inicial'] ?? '';
                   $param['periodo_apuracao_final'] = $param['periodo_apuracao_final'] ?? '';
                   $param['data_vencimento'] = self::toDateBr($param['data_vencimento'] ?? '');
                   $param['periodo_apuracao_inicial'] = self::toDateBr($param['periodo_apuracao_inicial']);
                   $param['periodo_apuracao_final'] = self::toDateBr($param['periodo_apuracao_final']);
                   $param['total'] = $param['total'] ?? '0,00';
                   $param['desconto'] = $param['desconto'] ?? '0,00';
                   $param['totalproduto'] = $param['totalproduto'] ?? '0,00';
                   $param['totalservico'] = $param['totalservico'] ?? '0,00';
                   $param['totalgeral'] = $param['totalgeral'] ?? ($param['total_liq_tx_conta'] ?? '0,00');
                   $param['id'] = $param['id'] ?? '';

                   $param['total'] = self::toMoneyBr($param['total']);
                   $param['desconto'] = self::toMoneyBr($param['desconto']);
                   $param['descricaofatura'] = isset($param['descricaofatura'])
                        ? self::replaceIsoDatesInText((string) $param['descricaofatura'])
                        : null;
                   //code here
                   require_once 'app/control/mfrotas/PedidoFrotasOrcamento.php';
                   //        $data = $this->form->getData();
                    //code here
                    TTransaction::open('minierp');

                    $conn = TConnection::open('minierp');

                    //code here
                    $pdf = new FPDF("P","pt","A4");
  
                    $linha=0;   
                    $pag=1;
                    $alturalinha=231;
                
                    $this->capa($pdf, $linha,$pag, $param);
                    $pdf->ln(1); 

                    $this->itens($pdf, $linha,$pag, $param);

                    $alturalinha = 90;
                    $alturalinha = $this->cabecalho($pdf, $alturalinha, $param);

                    $contasSelecionadas = TSession::getValue('ContaPagarListbuilder_datagrid_check');
                    $sistema  = TSession::getValue('sistema');
                    $idunit   = TSession::getValue('idunit');

                                // acumuladores (subtotal do pedido)
                    $accProdSem = 0.0; $accProdCom = 0.0;
                    $accServSem = 0.0; $accServCom = 0.0;
                    $accGeralSem = 0.0; $accGeralCom = 0.0;
                    $contador=0;
                    $propostasProcessadas = [];

                    if ($contasSelecionadas && is_array($contasSelecionadas)) {

                        foreach ($contasSelecionadas as $conta_id) {
                            if (!is_numeric($conta_id)) continue;

                            $conta = new Conta((int) $conta_id);
                            if (empty($conta->pedido_frotas_id)) continue;

                            if ($sistema == 'frotas') {

                                $propostas = self::carregarPropostasDaConta($conta);
                                if (!$propostas) continue;

                                // dados fixos por pedido
                                $pessoa       = new Pessoa($conta->pessoa_id);
                                $pedidofrotas = new PedidoFrotas($conta->pedido_frotas_id);

                                // historico/aprovador/usuario (1x por pedido)
                                $usuarioLoginData = '';
                                $hist = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $conta->pedido_frotas_id)
                                                            ->where('estado_pedido_frotas_id', '=', 18)
                                                            ->load();
                                if (!empty($hist)) {
                                    foreach ($hist as $h) {
                                        if (!empty($h->aprovador_frotas_id)) {
                                            $aprovador = new AprovadorFrotas($h->aprovador_frotas_id);
                                            $usuario   = new SystemUsers($aprovador->system_users_id); // use sua classe
                                            $dataoperacao = new DateTime($h->data_operacao);
                                            $dataoperacao = $dataoperacao->format('d/m/Y H:i'); // Data e hora (ex: 29/07/2025 14:35)
                                            $usuarioLoginData = trim(($usuario->login ?? '')); //. ' ' . ($dataoperacao ?? ''));
                                            break; // pega o primeiro historico com aprovador
                                        }
                                    }
                                }


                                // --------- UMA LINHA POR PROPOSTA ---------
                                foreach ($propostas as $proposta) {
                                    if (isset($propostasProcessadas[$proposta->id])) {
                                        continue;
                                    }
                                    $propostasProcessadas[$proposta->id] = true;

                                    // totais desta proposta (NAO acumulados)
                                    $prodSem = (float) ($proposta->total_produtos_sem_desconto ?? 0);
                                    $prodCom = (float) ($proposta->total_produtos_com_desconto ?? 0);
                                    $servSem = (float) ($proposta->total_servicos_sem_desconto ?? 0);
                                    $servCom = (float) ($proposta->total_servicos_com_desconto ?? 0);
                                    $geralSem = (float) ($proposta->total_geral_sem_desconto ?? 0);
                                    $geralCom = (float) ($proposta->total_geral_com_desconto ?? 0);

                                    // soma no subtotal do pedido
                                    $accProdSem += $prodSem;  $accProdCom += $prodCom;
                                    $accServSem += $servSem;  $accServCom += $servCom;
                                    $accGeralSem += $geralSem; $accGeralCom += $geralCom;

                                    // veiculo desta proposta
                                    $placaVeiculo = '';
                                    if (!empty($proposta->veiculos_id)) {
                                        $veiculos = Veiculos::where('id', '=', $proposta->veiculos_id)
                                                            ->where('system_unit_id', '=', $idunit)
                                                            ->load();
                                        if (!empty($veiculos)) {
                                            $placaVeiculo = $veiculos[0]->placa ?? '';
                                        }
                                    }

                                    // notas desta proposta
                                    $notas = '';
                                    $anexos = DocumentosPropostas::where('propostas_id', '=', $proposta->id)->load();
                                    if ($anexos) {
                                        foreach ($anexos as $anexo) {
                                            $num = $anexo->numero ?? '';
                                            if ($num !== '') $notas .= $num . '; ';
                                        }
                                        $notas = rtrim($notas, '; ');
                                    }

                                    // quebra de pagina
                                    if ($alturalinha >= 542) {
                                       // $pdf->AddPage();
                                          $this->itens($pdf, $linha,$pag, $param);
                                        $alturalinha = 90;
                                        $alturalinha = $this->cabecalho($pdf, $alturalinha,$param);
                                    }

                                    // imprime a linha DA PROPOSTA
                                    $pdf->SetFont('arial', '', 4);

                                    $pdf->SetXY(25, $alturalinha);
                                    $pdf->Cell(70, 5, utf8_decode(substr($pessoa->id . ' - ' . ($pessoa->nome ?? ''), 0, 48)), 0, 1, 'L');

                                    $pdf->SetXY(120, $alturalinha);
                                    $pdf->Cell(70, 5, utf8_decode($pedidofrotas->id ?? ''), 0, 1, 'R');

                                    $pdf->SetXY(133, $alturalinha);
                                    $pdf->Cell(70, 5, utf8_decode($proposta->id ?? ''), 0, 1, 'R');

                                    $pdf->SetXY(158, $alturalinha);
                                    $pdf->Cell(70, 5, utf8_decode($placaVeiculo), 0, 1, 'R');

                                    $pdf->SetXY(225, $alturalinha);
                                    $pdf->Cell(70, 5, utf8_decode(substr($usuarioLoginData,0,15)), 0, 1, 'L');

                                        // valor cru do banco/AR
                                        $raw = $pedidofrotas->dtfinalizacao ?? null;

                                        // formata dd/mm/YYYY ou retorna vazio
                                        if ($raw && trim($raw) !== '' && $raw !== '0000-00-00' && $raw !== '0000-00-00 00:00:00') {
                                            try {
                                                $dtfinalizacao = (new DateTime($raw))->format('d/m/Y');
                                            } catch (Throwable $e) {
                                                $dtfinalizacao = ''; // ou '-'
                                            }
                                        } else {
                                            $dtfinalizacao = ''; // ou '-'
                                        }                                    
                                    $pdf->SetXY(257, $alturalinha);
                                    $pdf->Cell(25, 5, trim($dtfinalizacao), 0, 1, 'R');

                                    $pdf->SetXY(282, $alturalinha);
                                    $pdf->Cell(70, 5, substr(utf8_decode($notas),0,35), 0, 1, 'R');
                                    // totais da PROPOSTA (nao cumulativos)
                                    $pdf->SetXY(315, $alturalinha);
                                    $pdf->Cell(70, 5, number_format($prodSem, 2, ',', '.'), 0, 1, 'R');

                                    $pdf->SetXY(350, $alturalinha);
                                    $pdf->Cell(70, 5, number_format($prodCom, 2, ',', '.'), 0, 1, 'R');

                                    $pdf->SetXY(385, $alturalinha);
                                    $pdf->Cell(70, 5, number_format($servSem, 2, ',', '.'), 0, 1, 'R');

                                    $pdf->SetXY(420, $alturalinha);
                                    $pdf->Cell(70, 5, number_format($servCom, 2, ',', '.'), 0, 1, 'R');

                                    $pdf->SetXY(460, $alturalinha);
                                    $pdf->Cell(70, 5, number_format($geralSem, 2, ',', '.'), 0, 1, 'R');

                                    $pdf->SetXY(500, $alturalinha);
                                    $pdf->Cell(70, 5, number_format($geralCom, 2, ',', '.'), 0, 1, 'R');
                                    $alturalinha += -5;
                                    $pdf->SetFont('arial', '', 5);
                                } // fim foreach propostas



                                $alturalinha += 15;
                                $contador++;

                            } elseif ($sistema == 'compras') {
                                // vou escrever o codigo aqui nao e para tirar IA
                                continue;
                            }
                        }
                    }
                                                    // --------- SUBTOTAL POR CONTA/PEDIDO (opcional) ---------
                                if ($alturalinha >= 700) {
                                    $pdf->AddPage();
                                    $alturalinha = 90;
                                    $alturalinha = $this->cabecalho($pdf, $alturalinha, $param);
                                }
                                $alturalinha += 15;
                                $pdf->SetFont('arial', 'B', 4);

                                // pode imprimir uma etiqueta "Subtotal" em uma das colunas de texto
                                $pdf->SetXY(235, $alturalinha);
                                $pdf->Cell(70, 5, utf8_decode('Total geral dos pedidos ('.$contador.')'), 0, 1, 'L');

                                $pdf->SetXY(315, $alturalinha);
                                $pdf->Cell(70, 5, number_format($accProdSem, 2, ',', '.'), 0, 1, 'R');
                                $accProdCom = $accProdSem - (($accProdSem * TSession::getValue('taxacontrato') / 100) );

                                $pdf->SetXY(350, $alturalinha);
                                $pdf->Cell(70, 5, number_format($accProdCom, 2, ',', '.'), 0, 1, 'R');

                                $pdf->SetXY(385, $alturalinha);
                                $pdf->Cell(70, 5, number_format($accServSem, 2, ',', '.'), 0, 1, 'R');

                                $accServCom = $accServSem - (($accServSem * TSession::getValue('taxacontrato') / 100) );
                                $pdf->SetXY(420, $alturalinha);
                                $pdf->Cell(70, 5, number_format($accServCom, 2, ',', '.'), 0, 1, 'R');

                                $pdf->SetXY(460, $alturalinha);
                                $pdf->Cell(70, 5, number_format($accGeralSem, 2, ',', '.'), 0, 1, 'R');

                                $totalcomdesconto = $accGeralSem - ($accGeralSem * TSession::getValue('taxacontrato') / 100);
                                $pdf->SetXY(500, $alturalinha);
                                $pdf->Cell(70, 5, number_format($totalcomdesconto, 2, ',', '.'), 0, 1, 'R');

          //          $pdf->AddPage();

                    if ($contasSelecionadas && is_array($contasSelecionadas)) {
                        $propostasAnexadas = [];
                        foreach ($contasSelecionadas as $conta_id) {
                            if (!is_numeric($conta_id)) continue;

                            $conta = new Conta((int) $conta_id);
                            if (empty($conta->pedido_frotas_id)) continue;

                            if ($sistema == 'frotas') {       
                                $param['fatura'] = true;
                                $propostas = self::carregarPropostasDaConta($conta);
                                if (!$propostas) continue;
                                foreach ($propostas as $proposta) {
                                    if (isset($propostasAnexadas[$proposta->id])) {
                                        continue;
                                    }
                                    $propostasAnexadas[$proposta->id] = true;
                                    $param['id'] = $proposta->id;

                                    $orcamento = new PropostaOrcamento;
                                    $orcamento->gerar($param,$pdf);
                                }
                            }
                        }
                    }
                  $nome = 'faturaorgao.pdf';

                 // stores the file
                  if (!file_exists("app/output/{$nome}") OR is_writable("app/output/{$nome}"))
                 {
                    $pdf->Output("app/output/{$nome}","F");
                 }
                 else
                 {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/{$nome}");
                 }

                 // open the report file
                 parent::openFile("app/output/{$nome}");
                 // shows the success message
              //   new TMessage('info', 'Pedidos gerado com sucesso. Por favor, habilite popups no navegador.');

                 // fill the form with the active record data
             //    $this->form->setData($data);
                 TTransaction::close();

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
         
    }


    function capa($pdf, $linha,$pag, $param)
    {
        $unit = new SystemUnit(TSession::getValue('idunit'));
        $entidade = new Entidade($unit->entidade_id);
        $administradora = new Administradora($entidade->administradora_id);
        $cidadeadm = new Cidade($administradora->cidade_id);
        $estadoadm = new Estado($cidadeadm->estado_id);

          //dados do orgao
        $cidadeunit = new Cidade($unit->cidade_id);
        $estadounit = new Estado($cidadeunit->estado_id);
   
        $pdf->AddPage();
        $pdf->SetFont('arial','B',6);
        $pdf->Image('app/images/logo.png', 25, 25, 60);

        // Variavel para controle do eixo Y
        $y = 40;
        $lineHeight = 10; // altura entre linhas
        
       // $pdf->SetFont('arial','B',10);
        
        // Primeira linha (Pedido e Status)
 
       
        $pdf->SetXY(110, $y);
             // Negrito para "Pedido No :"
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Fatura N° :') + 2, 5, utf8_decode('Fatura N° :'), 0, 0, 'L');

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$param['numero_fatura']), 0, 1, 'L');
        
        // Valor do pedido em fonte normal
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell($pdf->GetStringWidth(1) + 5, 5, utf8_decode($param['id']), 0, 0, 'L');
        
       

        $pdf->SetXY(350, $y);

           
        $y += $lineHeight;
        
        // Segunda linha (Placa e Status de entrega)
        $pdf->SetXY(110, $y);
       
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Data de Emissao:') + 1, 5, utf8_decode('Data de Emissao :'), 0, 0, 'L');
        
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$param['data_emissao']), 0, 1, 'L');
    

       
        
        $y += $lineHeight;
         
      
        $pdf->SetXY(110, $y);               
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Periodo de Apuracao:') + 1, 5, utf8_decode('Periodo de Apuracao:'), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$param['periodo_apuracao_inicial'].' a '.$param['periodo_apuracao_final']), 0, 1, 'L');

        $y += $lineHeight;
        $pdf->SetXY(110, $y);
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Vencimento') + 1, 5, utf8_decode('Vencimento'), 0, 0, 'L');

        
        $pdf->SetFont('Arial', '', 6);
        // if (!empty($cot->data_previsao_entrega)) {
        //     $dataentrega = new DateTime($cot->data_previsao_entrega);
        //     $dataentrega = $dataentrega->format('d/m/Y');
        // } else {
        //     $dataentrega='';
        // }
        $pdf->Cell(0, 5, utf8_decode(' '.$param['data_vencimento']), 0, 1, 'L');

        
        $y += $lineHeight;
  
  

        $pdf->SetY(85); // Linha separadora superior
        $pdf->Cell(0,5,"","B",1,'C');
        
        // Fonte e fundo
        $pdf->SetFont('arial','B',6); 
        $pdf->SetFillColor(235,239,240);
        
        // Retangulo e texto: Dados da Gerenciadora
        $pdf->Rect(26, 97, 290, 11, 'F');
        $pdf->SetXY(26, 100); // Pequeno recuo para o texto
        $pdf->Cell(0,5,utf8_decode('Dados da gerenciadora'),0,1,'L');
        
        // Retangulo e texto: Dados do Cliente Solicitante
        $pdf->Rect(296, 97, 270, 11, 'F');
        $pdf->SetXY(290, 100); // Recuo tambem aqui
        $pdf->Cell(0,5,utf8_decode('Dados do cliente solicitante'),0,1,'L');
        
        $pdf->SetFont('Arial', '', 6);
        $xnome = $administradora->nome ?? '';
        $xcnpj = $administradora->cnpj ?? '';
        $pdf->SetXY(25,110);
        $pdf->Cell(70,5,utf8_decode(substr($xnome,0,58)),0,1,'L');   
        $pdf->SetXY(190,110);
        $pdf->Cell(70,5,utf8_decode('CNPJ: '.$xcnpj),0,1,'L');
        
        $pdf->SetXY(290,110);
        $pdf->Cell(70,5,utf8_decode(substr($unit->name,0,46)),0,1,'L');   
        $pdf->SetXY(455,110);
        $pdf->Cell(70,5,utf8_decode('CNPJ: '.$unit->cnpj),0,1,'L');
        $pdf->SetFont('arial','',8);

        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(25,121);
        $pdf->Cell(70,5,utf8_decode(substr('Endereco : '.$administradora->rua.' no '.$administradora->numero,0,58)),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(190,121);
        $pdf->Cell(70,5,utf8_decode('Bairro : '.$administradora->bairro),0,1,'L');
       
        $pdf->SetXY(290,121);
        $pdf->Cell(70,5,utf8_decode(substr('Endereco : '.$unit->rua.' no '.$unit->numero,0,58)),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(455,121);
        $pdf->Cell(70,5,utf8_decode('Bairro : '.$unit->bairro),0,1,'L');

        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(25,132);
        $pdf->Cell(70,5,utf8_decode('Cidade : '.$cidadeadm->nome.' - '.$estadoadm->sigla),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(190,132);
        $pdf->Cell(70,5,utf8_decode('Telefone : '.$administradora->telefone01),0,1,'L');

        $pdf->SetXY(290,132);
        $pdf->Cell(70,5,utf8_decode('Cidade : '.$cidadeunit->nome .' - '.$estadounit->sigla),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(455,132);
        $pdf->Cell(70,5,utf8_decode('Telefone : '.$unit->telefone01),0,1,'L');
        

        $pdf->SetXY(25,143);
        $pdf->Cell(70,5,utf8_decode('Email : '.$administradora->email),0,1,'L');

        $pdf->SetXY(290,143);
        $pdf->Cell(70,5,utf8_decode('Email : '.$unit->email),0,1,'L');

        $pdf->Cell(0,11,"","B",1,'C');

        $pdf->SetFont('arial','B',8);
        $pdf->SetXY(25,172);
        $pdf->SetFillColor(235,239,240);
        $pdf->Rect(26, 170-3, 542, 15, 'F');

        // largura = 542 para corresponder ao retangulo
        $pdf->Cell(542, 5, utf8_decode('Descricao da fatura'), 0, 1, 'C');
        $pdf->SetFont('arial','B',6);
        
        $pdf->SetFont('arial','B',6);
        $unidade = $unit->name;
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(25, 190);
        $totalliquido = self::toMoneyBr(self::toFloatMoney($param['total']) - self::toFloatMoney($param['desconto']));

        // Texto da fatura
        $descricaofatura = $param['descricaofatura'] ?? ($unidade.','. ' periodo de '.$param['periodo_apuracao_inicial'].' a '.$param['periodo_apuracao_final'].' - Valor Comissao e Corretagem: R$ 0,00 - nao existe retencao de imposto conf. IN - SRFN. 1234 DE 11/01/2012 - ART PARAG. 1 E 2 - Manutencao de veiculos, aeronaves e equipamentos - Valor Bruto: R$ '.$param['total'].' - Valor Considerado:  R$ 0,00 - Valor Desconto: R$ '.$param['desconto'].' - Valor Liquido: R$ '.$totalliquido.'.');

        // MultiCell justificado
        $pdf->MultiCell(
            542, // largura (210 - margens esquerda e direita)
            10,   // altura da linha
            utf8_decode($descricaofatura),
            0,   // sem borda
            'J', // justificado
            false // sem preenchimento
        );
                $pdf->Cell(0,5,"","B",1,'C');

        $pdf->SetFont('arial','',6);
        $pdf->SetXY(25, 228);

        // Texto da fatura
        $descricaoempenho = '*** ATRASO NO PAGAMENTO SERA COBRADO JUROS NA FORMA PREVISTA EM CONTRATO E NA LEGISLACAO ***';

        // MultiCell justificado
        $pdf->MultiCell(
            542, // largura (210 - margens esquerda e direita)
            10,   // altura da linha
            utf8_decode($descricaoempenho),
            0,   // sem borda
            'C', // justificado
            false // sem preenchimento
        );
        $pdf->Cell(0,9,"","B",1,'C');

        $pdf->SetXY(25,253);
        $pdf->Cell(70,5,utf8_decode('Descricao'),0,1,'L');
        $pdf->SetXY(315, 253);
        $pdf->Cell(70,5,utf8_decode('Qtd'),0,1,'L');
        $pdf->SetXY(385,253);
        $pdf->Cell(70,5,utf8_decode('Valor Unitario (CD)'),0,1,'R');
        $pdf->SetXY(500,253);
        $pdf->Cell(70,5,utf8_decode('Total'),0,1,'R');

        $pdf->Cell(0,9,"","B",1,'C');

        $pdf->SetXY(25,273);
        $pdf->Cell(70,5,utf8_decode('Despesas com fornecimento de pecas '),0,1,'L');
        $pdf->SetXY(315,273);
        $pdf->Cell(70,5,utf8_decode('---'),0,1,'L');
        $pdf->SetXY(385,273);
        $pdf->Cell(70,5,self::toMoneyBr($param['totalproduto'] ?? 0),0,1,'R');
     
//          $taxa = (TSession::getValue('taxacontrato') / 100)+1;
//          $valor = str_replace('.', '',$param['total']);   // "5145,00"
// $valor = str_replace(',', '.',  $valor ); // "5145.00"

// $vltotalx = (double) $valor;
//          $totalconsumido = $vltotalx - ($vltotalx * (TSession::getValue('taxacontrato') / 100));
//         // $adiantamento=str_replace('.','',$param['totalproduto']);
//         // $adiantamento=str_replace(',','.',$param['totalproduto']);
//         //  $adiantamento_C=(double)$adiantamento;
//         // $totalprodutox = $adiantamento_C * $taxa;
        $pdf->SetXY(500,273);        
        $pdf->Cell(70,5,self::toMoneyBr($param['totalproduto'] ?? 0),0,1,'R');

        $pdf->SetXY(25, 283);
        $pdf->Cell(70,5,utf8_decode('Despesas com mao de obras '),0,1,'L');
         $pdf->SetXY(315,283);
        $pdf->Cell(70,5,utf8_decode('---'),0,1,'L');
        $pdf->SetXY(385,283);
        $pdf->Cell(70,5,self::toMoneyBr($param['totalservico'] ?? 0),0,1,'R');
        $pdf->SetXY(500,283);
        $pdf->Cell(70,5,self::toMoneyBr($param['totalservico'] ?? 0),0,1,'R');
        $totalservicoy = self::toFloatMoneyForm($param['totalservico'] ?? 0);
        $totalprodutoy = self::toFloatMoneyForm($param['totalproduto'] ?? 0);

        $totalconsumido_liq = $totalprodutoy + $totalservicoy; // ex.: 61433.20

        // Converte desconto (aceita 41.716,63 e 41716.63)
        $descontoy = self::toFloatMoneyForm($param['desconto'] ?? 0);

        $totalconsumidoy = number_format(($totalconsumido_liq - $descontoy), 2, ',', '.');
        $totalgeralxx = self::toFloatMoneyForm($param['total'] ?? 0);
        $totalconsumidoz     = number_format($totalgeralxx,      2, ',', '.'); // 61.433,20
        $totalconsumidoz     = number_format($totalgeralxx,      2, ',', '.'); // 61.433,20

        $pdf->SetXY(25,293);
        $pdf->Cell(70,5,utf8_decode('Total consumido '),0,1,'L');
        $pdf->SetXY(315,293);
        $pdf->Cell(70,5,utf8_decode('---'),0,1,'L');
        $pdf->SetXY(385,293);
        $pdf->Cell(70,5,'---',0,1,'R');
        $pdf->SetXY(500,293);
        $pdf->Cell(70,5, $totalconsumidoz,0,1,'R');

         $pdf->SetXY(25,303);
        $pdf->Cell(70,5,utf8_decode('Desconto contratual '),0,1,'L');
        $pdf->SetXY(315,303);
        $pdf->Cell(70,5,utf8_decode('---'),0,1,'L');
        $pdf->SetXY(385,303);
        $pdf->Cell(70,5,'---',0,1,'R');
        $pdf->SetXY(500,303);
        $pdf->Cell(70,5,number_format(TSession::getValue('taxacontrato'), 2).'%',0,1,'R');

        $pdf->SetXY(25,313);
        $pdf->Cell(70,5,utf8_decode('Total consumido com desconto '),0,1,'L');
        $pdf->SetXY(315,313);
        $pdf->Cell(70,5,utf8_decode('---'),0,1,'L');
        $pdf->SetXY(385,313);
        $pdf->Cell(70,5,'---',0,1,'R');
        $pdf->SetXY(500,313);
        $pdf->Cell(70,5,$totalconsumidoy,0,1,'R');

        $pdf->Cell(0,9,"","B",1,'C');

        $pdf->SetFont('arial','B',6);
        $pdf->SetXY(25,390);
        $pdf->Cell(0, 5, utf8_decode(str_repeat('-', 60)), 0, 1, 'L');
        $pdf->SetXY(25,400);
        $pdf->Cell(70,5,'ATESTO',0,1,'R');
        

  
   }

   function itens($pdf, $linha,$pag, $param)
    {
        $unit = new SystemUnit(TSession::getValue('idunit'));
        $entidade = new Entidade($unit->entidade_id);
        $administradora = new Administradora($entidade->administradora_id);
        $cidadeadm = new Cidade($administradora->cidade_id);
        $estadoadm = new Estado($cidadeadm->estado_id);

          //dados do orgao
        $cidadeunit = new Cidade($unit->cidade_id);
        $estadounit = new Estado($cidadeunit->estado_id);
   
        $pdf->AddPage();
        $pdf->SetFont('arial','B',6);
        $pdf->Image('app/images/logo.png', 25, 25, 60);

        // Variavel para controle do eixo Y
        $y = 40;
        $lineHeight = 10; // altura entre linhas
        
       // $pdf->SetFont('arial','B',10);
        
        // Primeira linha (Pedido e Status)
 
       
        $pdf->SetXY(110, $y);
             // Negrito para "Pedido No :"
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Gestor :') + 2, 5, utf8_decode('Gestor:'), 0, 0, 'L');
        
        // Valor do pedido em fonte normal
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell($pdf->GetStringWidth(1) + 5, 5, utf8_decode(substr($unit->name,0,46)), 0, 0, 'L');
        
       

        $pdf->SetXY(350, $y);

           
        $y += $lineHeight;
        
        // Segunda linha (Placa e Status de entrega)
        $pdf->SetXY(110, $y);
       
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('CNPJ:') + 1, 5, utf8_decode('CNPJ:'), 0, 0, 'L');
        
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$unit->cnpj), 0, 1, 'L');
    

       
        
        $y += $lineHeight;
        
      
        $pdf->SetXY(110, $y);               
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Periodo de Apuracao:') + 1, 5, utf8_decode('Periodo de Apuracao:'), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$param['periodo_apuracao_inicial'].' a '.$param['periodo_apuracao_final']), 0, 1, 'L');

        $pdf->SetXY(360,$y);
        $pdf->Cell(70,5,utf8_decode('SD => Sem desconto de contrato'),0,1,'L');


        $y += $lineHeight;
        $pdf->SetXY(110, $y);
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Desconto contratual') + 1, 5, utf8_decode('Desconto contratutal'), 0, 0, 'L');

        
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.number_format(TSession::getValue('taxacontrato'), 2).'%'), 0, 1, 'L');
        $pdf->SetXY(360,$y);
        $pdf->Cell(70,5,utf8_decode('CD => Com desconto de contrato'),0,1,'L');
        $pdf->Cell(0,10,"","B",1,'C');
   }

   function cabecalho($pdf,  $alturalinha, $param = null) {
        $pdf->SetFont('arial','B',5); 
        $pdf->SetXY(25,$alturalinha);
        $pdf->SetFillColor(235,239,240);
        $pdf->Rect(26, $alturalinha-3, 545, 15, 'F');
        $pdf->SetXY(25,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('ID-Rede'),0,1,'L');
        $pdf->SetXY(120,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('IDPd'),0,1,'R');
        $pdf->SetXY(133,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('IDPro'),0,1,'R');
        $pdf->SetXY(158,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Placa'),0,1,'R');
        $pdf->SetXY(225,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Autorizado'),0,1,'L');
        $pdf->SetXY(226,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Dt finalizacao'),0,1,'R');
        $pdf->SetXY(282,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('No. Notas'),0,1,'R');

        $pdf->SetFont('arial','',4); 
        $pdf->SetXY(315,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('(SD) Vl Produto'),0,1,'R');
        $pdf->SetXY(350,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('(CD) Vl Produto'),0,1,'R');
        $pdf->SetXY(385,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('(SD) Vl Servico'),0,1,'R');
        $pdf->SetXY(420,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('(CD) Vl Servico'),0,1,'R');
        $pdf->SetXY(460,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('(SD) Vl Total'),0,1,'R');
        $pdf->SetXY(500,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('(CD) Vl Total'),0,1,'R');

        $alturalinha += 15;
        $pdf->SetFont('arial','',5); 

        // $pdf->SetXY(25,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode(substr('0000001 - CODEG7 DEVELOPMENT & MARKETING DIGITAL LTDA',0,48)),0,1,'L');
        // $pdf->SetXY(120,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('123456'),0,1,'R');
        // $pdf->SetXY(143,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('123456'),0,1,'R');
        // $pdf->SetXY(168,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('OOJ-7565'),0,1,'R');
        // $pdf->SetXY(235,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('Luis Augusto'),0,1,'L');
        // $pdf->SetXY(243,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('10/01/2025'),0,1,'R');
        // $pdf->SetXY(280,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('123; 751'),0,1,'R');
        // $pdf->SetFont('arial','',4); 

        // $pdf->SetXY(315,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('R$ 125 000.00'),0,1,'R');
        // $pdf->SetXY(350,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('R$ 125 000.00'),0,1,'R');
        // $pdf->SetXY(385,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('R$ 125 000.00'),0,1,'R');
        // $pdf->SetXY(420,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('R$ 125 000.00'),0,1,'R');
        // $pdf->SetXY(460,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('R$ 1150 000.00'),0,1,'R');
        // $pdf->SetXY(500,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('R$ 1150 000.00'),0,1,'R');
        // $pdf->SetFont('arial','',5); 

        $pdf->ln(1);
        //2
        return $alturalinha+15;
    }
    
}
?>

