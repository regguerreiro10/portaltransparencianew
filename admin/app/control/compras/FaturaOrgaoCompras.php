  <?php


/**
 * Gera e retorna o conteúdo do orçamento HTML para o pedido de frotas
 *
 * @param array $dados Dados necessários para preencher o orçamento
 * @return string HTML do orçamento
 */
class FaturaOrgaoCompras extends TPage
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

    private static function toMoneyBr($v): string
    {
        return number_format(self::toFloatMoneyForm($v), 2, ',', '.');
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

        $formats = [
            'Y-m-d H:i:s' => 'd/m/Y H:i',
            'Y-m-d H:i' => 'd/m/Y H:i',
            'Y-m-d' => 'd/m/Y',
            'd/m/Y H:i:s' => 'd/m/Y H:i',
            'd/m/Y H:i' => 'd/m/Y H:i',
            'd/m/Y' => 'd/m/Y',
        ];

        foreach ($formats as $inputFormat => $outputFormat) {
            $dt = DateTime::createFromFormat($inputFormat, $s);
            $errors = DateTime::getLastErrors();

            if ($dt && (!$errors || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $dt->format($outputFormat);
            }
        }

        try {
            $format = strpos($s, ':') !== false ? 'd/m/Y H:i' : 'd/m/Y';
            return (new DateTime($s))->format($format);
        } catch (Throwable $e) {
            return $s;
        }
    }

    private static function getVencimentoFromPeriodoInicial($v): string
    {
        $s = trim((string) ($v ?? ''));

        if ($s === '') {
            return '';
        }

        $dt = DateTime::createFromFormat('d/m/Y', substr($s, 0, 10));

        if (!$dt) {
            try {
                $dt = new DateTime($s);
            } catch (Throwable $e) {
                return '';
            }
        }

        $dt->modify('first day of next month');

        return $dt->format('d/m/Y');
    }

    public function gerar($param = null)
    {
        try 
        {
            $param = (array) ($param ?? []);
            $param['data_emissao'] = self::toDateBr($param['data_emissao'] ?? date('d/m/Y H:i'));
            $param['periodo_apuracao_inicial'] = self::toDateBr($param['periodo_apuracao_inicial'] ?? '');
            $param['periodo_apuracao_final'] = self::toDateBr($param['periodo_apuracao_final'] ?? '');
            $param['vencimento'] = self::getVencimentoFromPeriodoInicial($param['periodo_apuracao_inicial']);
            $param['total'] = self::toMoneyBr($param['total'] ?? 0);
            $param['desconto'] = self::toMoneyBr($param['desconto'] ?? 0);
            $param['totalproduto'] = self::toMoneyBr($param['totalproduto'] ?? 0);
            $param['totalservico'] = self::toMoneyBr($param['totalservico'] ?? 0);
            $param['totalgeral'] = self::toMoneyBr($param['total_com_desconto']  ?? 0);
            $param['numero_fatura'] = $param['numero_fatura'] ?? '';

            // require_once 'app/control/compras/PedidoFrotasOrcamento.php';
            // $data = $this->form->getData();
            include 'app/control/compras/qrcode.php';

            TTransaction::open('minierp');
            $conn = TConnection::open('minierp');

            $pdf = new FPDF("P","pt","A4");

            $linha = 0;   
            $pag = 1;
            $alturalinha = 231;
        
            $this->capa($pdf, $linha, $pag, $param);
            $pdf->ln(1); 

            $this->itens($pdf, $linha, $pag, $param);

            $alturalinha = 90;
            $alturalinha = $this->cabecalho($pdf, $alturalinha,$param);

            $contasSelecionadas = TSession::getValue('ContaPagarListbuilder_datagrid_check');
            $sistema  = TSession::getValue('sistema');
            $idunit   = TSession::getValue('idunit');

            // acumuladores (subtotal)
            $accProdSem  = 0.0; $accProdCom  = 0.0;   // produtos (sem / com desconto)
            $accServSem  = 0.0; $accServCom  = 0.0;   // aqui vamos acumular o DESCONTO
            $accGeralSem = 0.0; $accGeralCom = 0.0;   // totais gerais (somente produto)
            $contador = 0;

            if ($contasSelecionadas && is_array($contasSelecionadas)) 
            {
                foreach ($contasSelecionadas as $conta_id) 
                {
                    if (!is_numeric($conta_id)) continue;

                    $conta = new Conta((int) $conta_id);
                    if (empty($conta->pedido_venda_id)) continue;

                    if ($sistema == 'compras') 
                    {
                        // carrega cotações do pedido
                        $cotacao = Cotacao::where('pedido_id', '=', $conta->pedido_venda_id)
                                        ->where('estado_pedido_id', 'IN', [8, 13, 18, 20, 24])
                                        ->load();
                        if (!$cotacao) continue;

                        // --------- UMA LINHA POR COTAÇÃO ---------
                        foreach ($cotacao as $cotacaos) 
                        {
                            // dados fixos
                            $pessoa = new Pessoa($conta->pessoa_id);
                            $pedido = new Pedido($conta->pedido_venda_id);

                            // histórico/aprovador/usuário (1x por pedido)
                            $usuarioLoginData = '';
                            $hist = CotacaoHistorico::where('cotacao_id', '=', $cotacaos->id)
                                                    ->where('estado_pedido_id', '=', 18)
                                                    ->load();
                            if (!empty($hist)) {
                                foreach ($hist as $h) {
                                    if (!empty($h->aprovador_id)) {
                                        $aprovador = Aprovador::find($h->aprovador_id);
                                        if ($aprovador) {
                                            $usuario = SystemUsers::find($aprovador->system_user_id);
                                            $usuarioLoginData = trim(($usuario->login ?? ''));
                                            break; // primeiro válido
                                        }
                                    }
                                }
                            }

                            // ----- taxa e totais desta cotação (NÃO acumulados)
                            $taxaContrato = (float) (TSession::getValue('taxacontrato') ?? 0.0);
                            if ($taxaContrato < 0)   { $taxaContrato = 0.0; }
                            if ($taxaContrato > 100) { $taxaContrato = 100.0; }

                            // produto bruto e com desconto
                            $prodSem = (float) ($cotacaos->valor_total ?? 0.0); // bruto
                            $prodCom = $prodSem * (1.0 - ($taxaContrato / 100.0)); // com desconto

                            // DESCONTO obtido
                            $desconto = $prodSem - $prodCom;

                            // 👉 usar colunas de "Serviço" para exibir o DESCONTO
                            $servSem = $desconto;
                            $servCom = $desconto;

                            // Totais gerais da linha (mantemos como SOMENTE produto)
                            $geralSem = $prodSem;
                            $geralCom = $prodCom;

                            // soma nos acumuladores
                            $accProdSem  += $prodSem;   $accProdCom  += $prodCom;
                            $accServSem  += $servSem;   $accServCom  += $servCom;   // soma dos descontos
                            $accGeralSem += $geralSem;  $accGeralCom += $geralCom;  // total geral (somente produto)

                            // veículo (mantido como no seu código; comentado)
                            $placaVeiculo = '';
                            // if (!empty($proposta->veiculos_id)) {
                            //     $veiculos = Veiculos::where('id', '=', $proposta->veiculos_id)
                            //                         ->where('system_unit_id', '=', $idunit)
                            //                         ->load();
                            //     if (!empty($veiculos)) {
                            //         $placaVeiculo = $veiculos[0]->placa ?? '';
                            //     }
                            // }

                            // notas (mantido comentado)
                            $notas = '';
                            // $anexos = DocumentosCotacao::where('propostas_id', '=', $proposta->id)->load();
                            // if ($anexos) {
                            //     foreach ($anexos as $anexo) {
                            //         $num = $anexo->numero ?? '';
                            //         if ($num !== '') $notas .= $num . '; ';
                            //     }
                            //     $notas = rtrim($notas, '; ');
                            // }

                            // quebra de página
                            if ($alturalinha >= 542) {
                                $pdf->AddPage();
                                $alturalinha = 90;
                                $alturalinha = $this->cabecalho($pdf, $alturalinha,$param);
                            }

                            // imprime a linha DA COTAÇÃO
                            $pdf->SetFont('arial', '', 5);

                            // Fornecedor
                            $pdf->SetXY(25, $alturalinha);
                            $pdf->Cell(70, 5, utf8_decode(substr($pessoa->id . ' - ' . ($pessoa->nome ?? ''), 0, 48)), 0, 1, 'L');

                            // Pedido
                            $pdf->SetXY(120, $alturalinha);
                            $pdf->Cell(70, 5, utf8_decode($pedido->id ?? ''), 0, 1, 'R');

                            // Proposta/Cotação - usar id da COTAÇÃO
                            $pdf->SetXY(143, $alturalinha);
                            $pdf->Cell(70, 5, utf8_decode($cotacaos->id ?? ''), 0, 1, 'R');

                            // Placa
                            $pdf->SetXY(168, $alturalinha);
                            $pdf->Cell(70, 5, utf8_decode($placaVeiculo), 0, 1, 'R');

                            // Usuário aprovador (login)
                            $pdf->SetXY(235, $alturalinha);
                            $pdf->Cell(70, 5, utf8_decode(substr($usuarioLoginData, 0, 15)), 0, 1, 'L');

                            // Data finalização do pedido
                            $dtfinalizacao = '';
                            if (!empty($pedido->dtfinalizacao)) {
                                $dtf = new DateTime($pedido->dtfinalizacao);
                                $dtfinalizacao = $dtf->format('d/m/Y');
                            }
                            $pdf->SetXY(287, $alturalinha);
                            $pdf->Cell(25, 5, trim($dtfinalizacao), 0, 1, 'R');

                            // Observações/Notas
                            $pdf->SetFont('arial', '', 4);
                            $pdf->SetXY(280, $alturalinha);
                            $pdf->Cell(70, 5, utf8_decode($notas), 0, 1, 'R');

                            // Totais da linha
                            $pdf->SetXY(315, $alturalinha);
                            $pdf->Cell(70, 5, number_format($prodSem, 2, ',', '.'), 0, 1, 'R');   // Produto bruto

                            $pdf->SetXY(350, $alturalinha);
                            $pdf->Cell(70, 5, number_format($prodCom, 2, ',', '.'), 0, 1, 'R');   // Produto com desconto

                            // // Serviços = DESCONTO
                            // $pdf->SetXY(385, $alturalinha);
                            // $pdf->Cell(70, 5, number_format($servSem, 2, ',', '.'), 0, 1, 'R');   // Desconto (sem)

                            $pdf->SetXY(420, $alturalinha);
                            $pdf->Cell(70, 5, number_format($servCom, 2, ',', '.'), 0, 1, 'R');   // Desconto (com)

                            // Totais gerais (mantidos como SOMENTE produto)
                            $pdf->SetXY(460, $alturalinha);
                            $pdf->Cell(70, 5, number_format($geralSem, 2, ',', '.'), 0, 1, 'R');   // Total bruto

                            $pdf->SetXY(500, $alturalinha);
                            $pdf->Cell(70, 5, number_format($geralCom, 2, ',', '.'), 0, 1, 'R');   // Total com desconto

                            // Avança linha (seu código usava -5 para “subir” a linha; mantive)
                            $alturalinha += -5;
                        } // fim foreach cotação

                        $alturalinha += 15;
                        $contador++;

                    } else {
                        // outros sistemas (se aparecer)
                        continue;
                    }
                }
            }

            // --------- SUBTOTAL / RODAPÉ ---------
            if ($alturalinha >= 700) {
                $pdf->AddPage();
                $alturalinha = 90;
                $alturalinha = $this->cabecalho($pdf, $alturalinha,$param);
            }

            $alturalinha += 15;
            $pdf->SetFont('arial', 'B', 4);

            // Etiqueta "Total geral dos pedidos"
            $pdf->SetXY(235, $alturalinha);
            $pdf->Cell(70, 5, utf8_decode('Total geral dos pedidos ('.$contador.')'), 0, 1, 'L');

            // Produtos (subtotal)
            $pdf->SetXY(315, $alturalinha);
            $pdf->Cell(70, 5, number_format($accProdSem, 2, ',', '.'), 0, 1, 'R'); // Produtos brutos

            $pdf->SetXY(350, $alturalinha);
            $pdf->Cell(70, 5, number_format($accProdCom, 2, ',', '.'), 0, 1, 'R'); // Produtos c/ desconto

            // Serviços (subtotal) = DESCONTO total
            // $pdf->SetXY(385, $alturalinha);
            // $pdf->Cell(70, 5, number_format($accServSem, 2, ',', '.'), 0, 1, 'R'); // Total desconto (sem)

            $pdf->SetXY(420, $alturalinha);
            $pdf->Cell(70, 5, number_format($accServCom, 2, ',', '.'), 0, 1, 'R'); // Total desconto (com)

            // Totais gerais (somente produto)
            $pdf->SetXY(460, $alturalinha);
            $pdf->Cell(70, 5, number_format($accGeralSem, 2, ',', '.'), 0, 1, 'R'); // Total bruto

            $pdf->SetXY(500, $alturalinha);
            $pdf->Cell(70, 5, number_format($accGeralCom, 2, ',', '.'), 0, 1, 'R'); // Total c/ desconto

            // --------- IMPRESSÃO DAS FATURAS ---------
            if ($contasSelecionadas && is_array($contasSelecionadas)) {
                foreach ($contasSelecionadas as $conta_id) {
                    if (!is_numeric($conta_id)) continue;

                    $conta = new Conta((int) $conta_id);
                    if (empty($conta->pedido_venda_id)) continue;

                    if ($sistema == 'compras') {
                        $param['fatura'] = true;

                        $cotacao = Cotacao::where('pedido_id', '=', $conta->pedido_venda_id)
                                        ->where('estado_pedido_id', 'IN', [8, 13, 18, 20, 24])
                                        ->load();
                        if (!$cotacao) continue;

                        foreach ($cotacao as $cotacaos) {
                            $param['id'] = $cotacaos->id;
                            $this->onImprimir($param, $pdf);
                        }
                    }
                }
            }

            $nome = 'faturaorgaocompras.pdf';

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
            TTransaction::close();

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }




    function capa($pdf, $linha,$pag, $param)
    {
        $param['data_emissao'] = self::toDateBr($param['data_emissao'] ?? date('d/m/Y H:i'));
        $param['periodo_apuracao_inicial'] = self::toDateBr($param['periodo_apuracao_inicial'] ?? '');
        $param['periodo_apuracao_final'] = self::toDateBr($param['periodo_apuracao_final'] ?? '');
        $param['vencimento'] = self::getVencimentoFromPeriodoInicial($param['periodo_apuracao_inicial']);

        $unit = new SystemUnit(TSession::getValue('idunit'));
        $entidade = new Entidade($unit->entidade_id);
        $administradora = new Administradora($entidade->administradora_id);
        $cidadeadm = new Cidade($administradora->cidade_id);
        $estadoadm = new Estado($cidadeadm->estado_id);

          //dados do orgão
        $cidadeunit = new Cidade($unit->cidade_id);
        $estadounit = new Estado($cidadeunit->estado_id);
   
        $pdf->AddPage();
        $pdf->SetFont('arial','B',6);
        $pdf->Image('app/images/logo.png', 25, 25, 60);

        // Variável para controle do eixo Y
        $y = 40; 
        $lineHeight = 10; // altura entre linhas
        
       // $pdf->SetFont('arial','B',10);
        
        // Primeira linha (Pedido e Status)
 
       
        $pdf->SetXY(110, $y);
             // Negrito para "Pedido Nº :"
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Fatura Nº :') + 2, 5, utf8_decode('Fatura Nº :'), 0, 0, 'L');
        
        // Valor do pedido em fonte normal
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell($pdf->GetStringWidth(1) + 5, 5, utf8_decode($param['numero_fatura']), 0, 0, 'L');
        
       

        $pdf->SetXY(350, $y);

           
        $y += $lineHeight;
        
        // Segunda linha (Placa e Status de entrega)
        $pdf->SetXY(110, $y);
       
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Data de Emissão:') + 1, 5, utf8_decode('Data de Emissão :'), 0, 0, 'L');
        
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$param['data_emissao']), 0, 1, 'L');
    

       
        
        $y += $lineHeight;
        
      
        $pdf->SetXY(110, $y);               
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Periodo de Apuração:') + 1, 5, utf8_decode('Periodo de Apuração:'), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$param['periodo_apuracao_inicial'].' a '.$param['periodo_apuracao_final']), 0, 1, 'L');

        $y += $lineHeight;
        $pdf->SetXY(110, $y);
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(0, 5, utf8_decode('Vencimento:  '.$param['vencimento']), 0, 1, 'L');

        
        $pdf->SetFont('Arial', '', 6);
        // if (!empty($cot->data_previsao_entrega)) {
        //     $dataentrega = new DateTime($cot->data_previsao_entrega);
        //     $dataentrega = $dataentrega->format('d/m/Y');
        // } else {
        //     $dataentrega='';
        // }
        $pdf->Cell(0, 5, utf8_decode(' '.'10/09/2025'), 0, 1, 'L');

        
        $y += $lineHeight;
  
  

        $pdf->SetY(85); // Linha separadora superior
        $pdf->Cell(0,5,"","B",1,'C');
        
        // Fonte e fundo
        $pdf->SetFont('arial','B',6); 
        $pdf->SetFillColor(235,239,240);
        
        // Retângulo e texto: Dados da Gerenciadora
        $pdf->Rect(26, 97, 290, 11, 'F');
        $pdf->SetXY(26, 100); // Pequeno recuo para o texto
        $pdf->Cell(0,5,utf8_decode('Dados da gerenciadora'),0,1,'L');
        
        // Retângulo e texto: Dados do Cliente Solicitante
        $pdf->Rect(296, 97, 270, 11, 'F');
        $pdf->SetXY(290, 100); // Recuo também aqui
        $pdf->Cell(0,5,utf8_decode('Dados do cliente solicitante'),0,1,'L');
        
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(25,110);
        $pdf->Cell(70,5,utf8_decode(substr($administradora->nome,0,58)),0,1,'L');   
        $pdf->SetXY(190,110);
        $pdf->Cell(70,5,utf8_decode('CNPJ: '.$administradora->cnpj),0,1,'L');
        
        $pdf->SetXY(290,110);
        $pdf->Cell(70,5,utf8_decode(substr($unit->name,0,46)),0,1,'L');   
        $pdf->SetXY(455,110);
        $pdf->Cell(70,5,utf8_decode('CNPJ: '.$unit->cnpj),0,1,'L');
        $pdf->SetFont('arial','',8);

        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(25,121);
        $pdf->Cell(70,5,utf8_decode(substr('Endereço : '.$administradora->rua.' nº '.$administradora->numero,0,58)),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(190,121);
        $pdf->Cell(70,5,utf8_decode('Bairro : '.$administradora->bairro),0,1,'L');
       
        $pdf->SetXY(290,121);
        $pdf->Cell(70,5,utf8_decode(substr('Endereço : '.$unit->rua.' nº '.$unit->numero,0,58)),0,1,'L');
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

        // largura = 542 para corresponder ao retângulo
        $pdf->Cell(542, 5, utf8_decode('Descrição da fatura'), 0, 1, 'C');
        $pdf->SetFont('arial','B',6);
        
        $pdf->SetFont('arial','B',6);
        $unidade = $unit->name;
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(25, 190);

        // Texto da fatura
        $descricaofatura = $unidade.','. ' período de '.$param['periodo_apuracao_inicial'].' a '.$param['periodo_apuracao_final'].' - Valor Comissão e Corretagem: R$ 0,00 - não existe retenção de imposto conf. IN - SRFN. 1234 DE 11/01/2012 - AUTOGESTÃO INFORMATIZADA VIA WEB PARA GERENCIAMENTO E INTERMEDIAÇÃO DE AQUISIÇÕES DE MATERIAIS PARA CONSTRUÇÃO - Valor Bruto: R$ '.$param['total'].' - Valor Considerado:  R$ 0,00 - Valor Desconto: R$ '.$param['desconto'].'- Valor Líquido: R$ '.$param['totalgeral'].'.';

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
        $descricaoempenho = '*** ATRASO NO PAGAMENTO SERÁ COBRADO JUROS NA FORMA PREVISTA EM CONTRATO E NA LEGISLAÇÃO ***';

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
        $pdf->Cell(70,5,utf8_decode('Descrição'),0,1,'L');
        $pdf->SetXY(315, 253);
        $pdf->Cell(70,5,utf8_decode('Qtd'),0,1,'L');
        $pdf->SetXY(385,253);
        $pdf->Cell(70,5,utf8_decode('Valor Unitário (CD)'),0,1,'R');
        $pdf->SetXY(500,253);
        $pdf->Cell(70,5,utf8_decode('Total'),0,1,'R');

        $pdf->Cell(0,9,"","B",1,'C');

        $pdf->SetXY(25,273);
        $pdf->Cell(70,5,utf8_decode('Despesas com fornecimento de produtos '),0,1,'L');
        $pdf->SetXY(315,273);
        $pdf->Cell(70,5,utf8_decode('---'),0,1,'L');
        $pdf->SetXY(385,273);
        $pdf->Cell(70,5,self::toMoneyBr($param['totalproduto'] ?? 0),0,1,'R');
     
         $taxa = (TSession::getValue('taxacontrato') / 100)+1;
        // $adiantamento=str_replace('.','',$param['totalproduto']);
        // $adiantamento=str_replace(',','.',$param['totalproduto']);
        //  $adiantamento_C=(double)$adiantamento;
        // $totalprodutox = $adiantamento_C * $taxa;
        $pdf->SetXY(500,273);        
        $pdf->Cell(70,5,self::toMoneyBr($param['totalproduto'] ?? 0),0,1,'R');

        // Converte serviço para float
        $totalservicoy = self::toFloatMoneyForm($param['totalservico'] ?? 0);

        // Converte produto para float
        $totalprodutoy = self::toFloatMoneyForm($param['totalproduto'] ?? 0);

        $totalconsumido_liq = ($totalprodutoy + $totalservicoy) - (($totalprodutoy + $totalservicoy) * (TSession::getValue('taxacontrato')/100)) ; // ex.: 61433.20

        $totalconsumidoy     = number_format($totalconsumido_liq,      2, ',', '.'); // 61.433,20
        // Converte serviço para float
        $totalgeralxx = self::toFloatMoneyForm($param['total'] ?? 0);
        $totalconsumidoz     = number_format($totalgeralxx,      2, ',', '.'); // 61.433,20

        $pdf->SetXY(25,283);
        $pdf->Cell(70,5,utf8_decode('Total consumido '),0,1,'L');
        $pdf->SetXY(315,283);
        $pdf->Cell(70,5,utf8_decode('---'),0,1,'L');
        $pdf->SetXY(385,283);
        $pdf->Cell(70,5,'---',0,1,'R');
        $pdf->SetXY(500,283);
        $pdf->Cell(70,5,self::toMoneyBr($param['total'] ?? 0),0,1,'R');

         $pdf->SetXY(25,293);
        $pdf->Cell(70,5,utf8_decode('Desconto contratual '),0,1,'L');
        $pdf->SetXY(315,293);
        $pdf->Cell(70,5,utf8_decode('---'),0,1,'L');
        $pdf->SetXY(385,293);
        $pdf->Cell(70,5,'---',0,1,'R');
        $pdf->SetXY(500,293);
        $pdf->Cell(70,5,number_format(TSession::getValue('taxacontrato'), 2).'%',0,1,'R');

        $pdf->SetXY(25,303);
        $pdf->Cell(70,5,utf8_decode('Total consumido com desconto '),0,1,'L');
        $pdf->SetXY(315,303);
        $pdf->Cell(70,5,utf8_decode('---'),0,1,'L');
        $pdf->SetXY(385,303);
        $pdf->Cell(70,5,'---',0,1,'R');
        $pdf->SetXY(500,303);
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

          //dados do orgão
        $cidadeunit = new Cidade($unit->cidade_id);
        $estadounit = new Estado($cidadeunit->estado_id);
   
        $pdf->AddPage();
        $pdf->SetFont('arial','B',6);
        $pdf->Image('app/images/logo.png', 25, 25, 60);

        // Variável para controle do eixo Y
        $y = 40;
        $lineHeight = 10; // altura entre linhas
        
       // $pdf->SetFont('arial','B',10);
        
        // Primeira linha (Pedido e Status)
 
       
        $pdf->SetXY(110, $y);
             // Negrito para "Pedido Nº :"
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
        $pdf->Cell($pdf->GetStringWidth('Periodo de Apuração:') + 1, 5, utf8_decode('Periodo de Apuração:'), 0, 0, 'L');
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

   function cabecalho($pdf, $alturalinha, $param = null) {
        $pdf->SetFont('arial','B',5); 
        $pdf->SetXY(25,$alturalinha);
        $pdf->SetFillColor(235,239,240);
        $pdf->Rect(26, $alturalinha-3, 545, 15, 'F');
        $pdf->SetXY(25,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('ID-Rede'),0,1,'L');
        $pdf->SetXY(120,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Pedido'),0,1,'R');
        $pdf->SetXY(143,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Cotação'),0,1,'R');
        $pdf->SetXY(168,$alturalinha);
        $pdf->Cell(70,5,utf8_decode(''),0,1,'R');
        $pdf->SetXY(235,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Autorizado por'),0,1,'L');
        $pdf->SetXY(243,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Dt finalização'),0,1,'R');
        $pdf->SetXY(280,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('No. Notas'),0,1,'R');

        $pdf->SetFont('arial','',4); 
        $pdf->SetXY(315,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('(SD) Vl Produto'),0,1,'R');
        $pdf->SetXY(350,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('(CD) Vl Produto'),0,1,'R');
        // $pdf->SetXY(385,$alturalinha);
        // $pdf->Cell(70,5,utf8_decode('(SD) Vl Serviço'),0,1,'R');
        $pdf->SetXY(420,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('(CD) Vl Desconto'),0,1,'R');
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

    public function onImprimir($param = null, $pdf = null) 
    {
        try 
         {
        //        $data = $this->form->getData();
                 //code here
                 TTransaction::open('minierp');
    
                 $conn = TConnection::open('minierp');
    
                 //code here
               //  $pdf = new FPDF("P","pt","A4");
    
                   
                 $linha=0;   
                 $pag=1;
                 $alturalinha=255;
                 $unidade='';
                 $qt = 0;
                 $vl = 0;
                 $vlt = 0;
                 $qtitens=0;

                 $obj = new Cotacao($param['id']);
                 $pedido = new Pedido($obj->pedido_id);
    
                 $itenscotacao = ItensCotacao::where('cotacao_id','=',$param['id'])
                                            ->load();
                 if ($itenscotacao) {
                     //cabecalho
                     if ( ($linha==0) || ($linha >= 33) ){
                       $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id);
                       $pag=$pag + 1; 
                       $alturalinha = 255;
                       $linha = 12;
                    }
                     foreach ($itenscotacao as $itens) {
                         //detalhes
                         if ( ($linha==0) || ($linha >= 33) ){
                            $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id);
                           $linha = 12;
                           $pag=$pag + 1; 
                           $alturalinha = 255;
                         }
                          $produto = new Produto($itens->produto_id);

                     $pdf->setFont('arial','',7);   
                     $pdf->SetXY(22,$alturalinha);
                     $pdf->Cell(70,5,utf8_decode($produto->id),0,1,'L');

                        $nome_produto = utf8_decode($produto->nome);

                        // Define largura da célula do nome e número máximo de linhas
                        $largura_nome = 340;
                        $altura_linha = 8;
                        $max_linhas = 3;

                        // Calcula altura necessária com base no número de linhas que o nome ocuparia
                        $linhas_necessarias = ceil($pdf->GetStringWidth($nome_produto) / $largura_nome);
                        $linhas_exibidas = min($linhas_necessarias, $max_linhas);
                        $altura_total = $linhas_exibidas * $altura_linha;

                        // Se ultrapassar o limite de linhas, corta e adiciona reticências
                        if ($linhas_necessarias > $max_linhas) {
                            // Estimativa segura para cortar
                            $nome_cortado = mb_strimwidth($nome_produto, 0, 200, '...');
                        } else {
                            $nome_cortado = $nome_produto;
                        }

                        // Nome do produto com quebra automática controlada
                        $pdf->SetXY(45, $alturalinha-1);
                        $pdf->MultiCell($largura_nome, $altura_linha, $nome_cortado, 0, 'L', false);

                        // Mantém os demais campos alinhados na mesma linha base
                        $pdf->SetFont('arial', '', 7);
                        $pdf->SetXY(350, $alturalinha);
                        $pdf->Cell(70, 5, $produto->unidade_medida->nome, 0, 1, 'C');
                        $pdf->SetXY(380, $alturalinha);
                        $pdf->Cell(70, 5, $itens->qtde, 0, 1, 'C');
                        $pdf->SetXY(420, $alturalinha);
                        $pdf->Cell(70, 5, 'R$ ' . number_format($itens->valor, 2), 0, 1, 'R');
                        $pdf->SetXY(490, $alturalinha);
                        $pdf->Cell(70, 5, 'R$ ' . number_format($itens->valor_total, 2), 0, 1, 'R');

    
                         $alturalinha += 24; 
                         $linha +=1;
    
                         $pdf->ln(1); 
                        // if ($itens->estado_pedido_id == EstadoPedido::APROVADO || is_null($itens->estado_pedido_id)) {
                                $qtitens++;
                                $qt  += $itens->qtde;
                                $vl  += $itens->valor;
                                $vlt += $itens->valor_total;
                      //  }

                     }
                     $alturalinha+=15;
                     //rodape
                  //   $pdf->SetXY(25,$alturalinha);
                    // $pdf->Cell(0,25,"","B",1,'C');
                //     $alturalinha+=15;
                     $pdf->SetFont('arial','B',10); 
                     $pdf->SetXY(25,$alturalinha);
                     $pdf->SetFillColor(235,239,240);
                     $pdf->Rect(26, $alturalinha-5, 542, 15, 'F');
                     $pdf->Cell(70,5,utf8_decode('Total Geral '.$qtitens.' Itens'),0,1,'L');
    
                     $pdf->SetXY(360,$alturalinha);
                     $pdf->Cell(70,5,$qt,0,1,'C');
                     $pdf->SetXY(420,$alturalinha);
                     $pdf->Cell(70,5,'R$ '.number_format($vl, 2),0,1,'R');
                     $pdf->SetXY(490,$alturalinha);
                     $pdf->Cell(70,5,'R$ '.number_format($vlt, 2),0,1,'R');
                     
                    
                     $pdf->Cell(0,15,"","B",1,'C');

                     $pdf->SetFont('arial','I',10); 
                     $alturalinha+=26;
                     
                     $pdf->SetXY(82,$alturalinha);
                     $pdf->Cell(70,5,'Valor Bruto: '.'R$ '.number_format($vlt, 2),0,1,'R');
                                         
                     $pdf->SetXY(320,$alturalinha);
                     
                     $taxas = ((TSession::getValue('taxacontrato'))) ;
                     $desconto = ($vlt * $taxas) / 100;
                     $pdf->Cell(70,5,'Valor Desconto: ('.number_format($taxas, 2).'%)'.' R$ '.number_format($desconto, 2),0,1,'R');
                     
                     $pdf->SetXY(490,$alturalinha);
                     $liquido = $vlt - $desconto;
                     $pdf->Cell(70,5,'Valor Liquido: '.'R$ '.number_format($liquido, 2),0,1,'R');
                     
                     $alturalinha+=25;
                     $pdf->Cell(0,15,"","B",1,'C');
                     $pdf->SetXY(45,$alturalinha);
                     $pdf->Cell(70,5,utf8_decode('Legenda: P: PRE-APROVADO; A: APROVADO; R: REPROVADO '),0,1,'L');                     //qrcode

                     $text = $pedido->id.".png";
                     $file = "app/documents/{$text}";
                     $options = array(
                            'w' => 500,
                            'h' => 500
                     );
    
                     $generator = new QRCode($pedido->id, $options);
                     $image = $generator->render_image();
                     imagepng($image, $file);
                     $pdf->SetXY(255,750);
                     $pdf->Cell(70,5,'Agora falta pouco, escaneie o QR Code para efetuar a entrega dos seus produtos.',0,1,'C');
    
                     $pdf->Image('app/documents/'.$pedido->id.'.png', 250, 760, 80);
    
                 }
    
                //   $nome = 'documentocotacao.pdf';
    
                //  // stores the file
                //  if (!file_exists("app/output/{$nome}") OR is_writable("app/output/{$nome}"))
                //  {
                //     $pdf->Output("app/output/{$nome}","F");
                //  }
                //  else
                //  {
                //     throw new Exception(_t('Permission denied') . ': ' . "app/output/{$nome}");
                //  }
    
                //  // open the report file
                //  parent::openFile("app/output/{$nome}");
                //  // shows the success message
                //  new TMessage('info', 'Pedidos gerado com sucesso. Por favor, habilite popups no navegador.');
    
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
       private function cabecalhoDCot($pdf, $linha,$pag, $unidade, $id, $datacotacao, $idcot)
   {
       $label = '';
       $datacotacao = new DateTime($datacotacao);
       $datacotacao = $datacotacao->format('d/m/Y');

       $cot = new Cotacao($idcot);

       $ped = new Pedido($id);
       $dep = new DepartamentoUnit($ped->departamento_unit_id);
       $unit = new SystemUnit($dep->system_unit_id);

       $pessoa = new Pessoa($cot->pessoa_id);                           
       $cnpj = $pessoa->documento;
       $nome = $pessoa->nome;

       $pessoa_endereco = PessoaEndereco::where('pessoa_id','=',$ped->cliente_id)
                                        ->where('principal','=','T')
                                        ->load();
       $nomecidade = '';
       if ($pessoa_endereco) {
           foreach ($pessoa_endereco as $pe) {
           $cidade = new Cidade($pe->cidade_id);
           $estado = new Estado($cidade->estado_id);
           $nomecidade = $cidade->nome.'/'.$estado->sigla;
           }
       }

       $historicopedido = PedidoHistorico::where('pedido_venda_id','=',$ped->id)
                                         ->where('estado_pedido_venda_id','=',EstadoPedido::APROVADO)
                                         ->orderBy('data_operacao','desc')
                                         ->load();
       $usuario='';
       if ($historicopedido) {
           foreach($historicopedido as $histped) {
              $user = new SystemUsers($histped->aprovador_id);
              $usuario = $user->name;                
              break;              
           }
       } else {$usuario = '';}

       $pdf->AddPage();
       $pdf->SetFont('arial','B',10);

       $pdf->Image('app/images/logo.png', 25, 02, 80);
       $pdf->SetXY(300,40);
       $pdf->Cell(70,5,utf8_decode('Pedido de Compra: '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(345,40);
       $pdf->Cell(70,5,'#'.$id,0,1,'R');

       $pdf->SetFont('arial','B',10);
       $pdf->SetXY(445,40);
       $pdf->Cell(70,5,utf8_decode('Cotação de Venda: '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(500,40);
       $pdf->Cell(70,5,'#'.$idcot,0,1,'R');
       $pdf->Ln(4);

       $pdf->SetFont('arial','B',10);
       $pdf->SetXY(425,55);
       $pdf->Cell(70,5,utf8_decode('Data da Cotação: '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(500,55);
       $pdf->Cell(70,5,$datacotacao,0,1,'R');
       $pdf->ln(1);

       $pdf->SetFont('arial','B',10); 
       $pdf->SetXY(500,70);
       $pdf->Cell(70,5,utf8_decode(' Página: '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(500,70);
       $pdf->Cell(70,5,$pag,0,1,'R');
       $pdf->ln(1);

       $pdf->Cell(0,5,"","B",1,'C');
       $pdf->SetFont('arial','B',10); 
       $pdf->SetXY(25,100);
       $pdf->SetFillColor(235,239,240);
       $pdf->Rect(26, 95, 542, 15, 'F');
       $pdf->Cell(70,5,utf8_decode('Dados da Cotação - '.$unit->name),0,1,'L');

       $pdf->SetXY(25,118);
       $pdf->Cell(70,5,utf8_decode('Descrição do Pedido '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(25,133);
       $pdf->Cell(70,5,utf8_decode($ped->descricaopedido),0,1,'L');

       $pdf->SetFont('arial','B',10);
       $pdf->SetXY(355,118);
       $pdf->Cell(70,5,utf8_decode('Departamento '),0,1,'L');
       $pdf->SetFont('arial','',8);
       $pdf->SetXY(355,133);
       $pdf->Cell(70,5,utf8_decode($dep->name),0,1,'L');

       $pdf->SetFont('arial','B',10);
       $pdf->SetXY(25,148);
       $pdf->Cell(70,5,utf8_decode('Fornecedor '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(25,163);
       $pdf->Cell(70,5,utf8_decode($cnpj.' - '.substr($nome,0,38)),0,1,'L');
       $pdf->SetXY(25,178);
       $pdf->Cell(70,5,utf8_decode($nomecidade),0,1,'L');

       $pdf->SetFont('arial','B',10);
       $pdf->SetXY(355,148);
       $pdf->Cell(70,5,utf8_decode('Autorizador por '),0,1,'L');
       $pdf->SetFont('arial','',10);
       $pdf->SetXY(355,163);
       $pdf->Cell(70,5,utf8_decode($usuario),0,1,'L');

       $pdf->SetFont('arial','B',10); 
       $pdf->SetXY(25,208);
       $pdf->SetFillColor(235,239,240);
       $pdf->Rect(26, 203, 542, 15, 'F');
       $pdf->Cell(70,5,utf8_decode('Itens da Cotação '),0,1,'L');

       $pdf->SetFont('arial','B',10); 
       $pdf->SetFillColor(149,192,230);
       $pdf->Rect(26, 233, 542, 15, 'F');

       $pdf->SetXY(25,238);
       $pdf->Cell(70,5,utf8_decode('ID'),0,1,'L');
       $pdf->SetXY(45,238);
       $pdf->Cell(70,5,utf8_decode('Produto'),0,1,'L');
        $pdf->SetXY(350,238);
        $pdf->Cell(70,5,utf8_decode('Und'),0,1,'C');
        $pdf->SetXY(380,238);
        $pdf->Cell(70,5,utf8_decode('Qtde'),0,1,'C');
       $pdf->SetXY(420,238);
       $pdf->Cell(70,5,utf8_decode('Valor'),0,1,'R');
       $pdf->SetXY(490,238);
       $pdf->Cell(70,5,utf8_decode('Valor Total'),0,1,'R');

       $pdf->ln(1);
    }
    
}
?>
