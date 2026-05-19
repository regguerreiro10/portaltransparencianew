  <?php


/**
 * Gera e retorna o conteúdo do orçamento HTML para o pedido de frotas
 *
 * @param array $dados Dados necessários para preencher o orçamento
 * @return string HTML do orçamento
 */
class PedidoFrotasOrcamento extends TPage
{
     public function gerar($param = null)
     {
        try 
        {
                //code here
                 require_once 'app/control/compras/qrcode.php';
                //        $data = $this->form->getData();
                 //code here
                 TTransaction::open('minierp');

                 $conn = TConnection::open('minierp');

                 if (isset($param['fatura']) && $param['fatura'] == true) {
                 } else {
                     $pdf = new FPDF("P","pt","A4");
                }
                 //code here

                 $linha=0;   
                 $pag=1;
                 $alturalinha=231;
                 $unidade='';
                 $qt = 0;
                 $vl = 0;
                 $vlt = 0;
                 $qtitens=0;
      
                 $pedido = new PedidoFrotas($param['id']);
                 if (!$pedido->estabelecimento_id) {
                    throw new Exception('Pedido não tem orçamento de cliente');
                 } 
                 $obj1 = Propostas::where('pedido_frotas_id','=',$pedido->id)
                                               ->where('pessoa_id','=',$pedido->estabelecimento_id)
                                               ->load();
                 if ($obj1)  {
                    foreach ($obj1 as $obj) {
                         break;
                    }
                 }              
                
                 $idpedido = $pedido->id;
                 $pessoa = new Pessoa($obj->pessoa_id);      
                 //$txbancaria = $pessoa->taxabancaria / 100;
                
                 $taxaspessoa = TaxasPessoa::where('pessoa_id','=',$pessoa->id)
                                                 ->where('deleted_at','is',null)
                                                 ->where('entidade_id','=',TSession::getValue('entidade'))
                                                 ->where('system_unit_id','=',TSession::getValue('idunit'))
                                                 ->load();
                    if ($taxaspessoa) {
                        foreach ($taxaspessoa as $tx) {
                            $txbancaria = $tx->taxabancaria / 100;
                      //      $txcontrato = $tx->taxacontrato;
                            break;
                        }
                    } else {
                 //       $txcontrato = 0;
                        $txbancaria = 0;
                    }
                    
                  $txcontrato = TSession::getValue('taxacontrato');
                 $obj1 = Propostas::where('pedido_frotas_id','=',$idpedido)
                                               ->where('pessoa_id','=',$pessoa->id)
                                               ->load();

              

                 $itenscotacao = ItensPropostas::where('propostas_id','=',$obj1[0]->id)
                                               ->where('tipo','=',1)
                                               ->where('deleted_at','is',null)
                                            ->load();
                 if ($itenscotacao) {
                     //cabecalho
                     if ( ($linha==0) || ($linha >= 40) ){
                       $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                       $pag=$pag + 1; 
                       $alturalinha = 231;
                       $linha = 12;
                    }
                    $alturalinha= $this->cabecalhoProduto($pdf,$alturalinha,$param);

                     foreach ($itenscotacao as $itens) {
                         //detalhes
                         if ( ($linha==0) || ($linha >= 40) ){
                            $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                           $linha = 0;
                           $pag=$pag + 1; 
                           $alturalinha = 231;
                           $alturalinha= $this->cabecalhoProduto($pdf,$alturalinha,$param);
                         
                        }
                          $produto = new Produto($itens->produto_id);

                         $pdf->setFont('arial','',6);   
                         $pdf->SetXY(25,$alturalinha);
                         $pdf->Cell(70,5,utf8_decode($itens->id),0,1,'L');

                         $nome = (string) ($produto->nome ?? '');
                         $tamanho = strlen($nome);
                         //$tamanho = strlen($itens->nome);
                          if ($tamanho>=71) {
                            $pdf->SetXY(45,$alturalinha-2);
                            $descitens = $nome ?? '';
                            $pdf->MultiCell(340,10,substr(utf8_decode($descitens),0,255),0,'L',false);
                            $pdf->setFont('arial','',6);
                            $pdf->SetXY(260,$alturalinha);
                            if (!empty($itens->diasdegarantia) || !empty($itens->qtdekmgarantia)) {
                                $garantia = '';
                            
                                if (!empty($itens->diasdegarantia)) {
                                    $garantia .= $itens->diasdegarantia . ' Dias ';
                                }
                            
                                if (!empty($itens->qtdekmgarantia)) {
                                    $garantia .= $itens->qtdekmgarantia . ' Km';
                                }
                            
                                $pdf->Cell(70, 5, $garantia, 0, 1, 'L');
                            }                            $pdf->SetXY(360,$alturalinha);
                            $pdf->Cell(70,5,$itens->qtde,0,1,'C');
                            $pdf->SetXY(420,$alturalinha);
                            $pdf->Cell(70,5,'R$ '.number_format($itens->valor, 2),0,1,'R');
                            $pdf->SetXY(500,$alturalinha);
                            $pdf->Cell(70,5,'R$ '.number_format($itens->valor_total, 2),0,1,'R');
                            $alturalinha += 24;
                          } else {
                            $pdf->SetXY(45,$alturalinha);
                            $descitens = $nome ?? '';
                            $pdf->MultiCell(340,5,utf8_decode($descitens),0,'L', false);
                            $pdf->setFont('arial','',6);   
                            $pdf->SetXY(260,$alturalinha);
                            if (!empty($itens->diasdegarantia) || !empty($itens->qtdekmgarantia)) {
                                $garantia = '';
                            
                                if (!empty($itens->diasdegarantia)) {
                                    $garantia .= $itens->diasdegarantia . ' Dias ';
                                }
                            
                                if (!empty($itens->qtdekmgarantia)) {
                                    $garantia .= $itens->qtdekmgarantia . ' Km';
                                }
                            
                                $pdf->Cell(70, 5, $garantia, 0, 1, 'L');
                            }                            $pdf->SetXY(360,$alturalinha);
                            $pdf->Cell(70,5,$itens->qtde,0,1,'C');
                            $pdf->SetXY(420,$alturalinha);
                            $pdf->Cell(70,5,'R$ '.number_format($itens->valor, 2),0,1,'R');
                            $pdf->SetXY(500,$alturalinha);
                            $pdf->Cell(70,5,'R$ '.number_format($itens->valor_total, 2),0,1,'R');
                          }

                         $alturalinha += 15; 
                         $linha +=1;

                         $pdf->ln(1); 
                         $qtitens++;
                         $qt += $itens->qtde;
                         $vl += $itens->valor;
                         $vlt += $itens->valor_total;
                     }
                     $alturalinha+=15;
                     //rodape
                     //   $pdf->SetXY(25,$alturalinha);
                  //   $pdf->Cell(0,15,"","B",1,'C');
                    //     $alturalinha+=15;
                     $pdf->SetFont('arial','B',6); 
                     $pdf->SetXY(25,$alturalinha);
                     $pdf->SetFillColor(235,239,240);
                     $pdf->Rect(26, $alturalinha-5, 542, 15, 'F');
                     $pdf->Cell(70,5,utf8_decode('Total Geral '.$qtitens.' Itens'),0,1,'L');

                     $pdf->SetXY(360,$alturalinha);
                     $pdf->Cell(70,5,$qt,0,1,'C');
                     $pdf->SetXY(420,$alturalinha);
                     $pdf->Cell(70,5,'R$ '.number_format($vl, 2),0,1,'R');
                     $pdf->SetXY(500,$alturalinha);
                     $pdf->Cell(70,5,'R$ '.number_format($vlt, 2),0,1,'R');

                 //    $pdf->Cell(0,15,"","B",1,'C');

                     $pdf->SetFont('arial','B',10); 
                     $alturalinha+=26;

                     $pdf->SetXY(82,$alturalinha);
                     $pdf->Cell(70,5,'Valor Bruto: '.'R$ '.number_format($obj->valor_total, 2),0,1,'R');

                     $pdf->SetXY(320,$alturalinha);

                  

                     $taxas = ((TSession::getValue('taxacontrato'))) ;
                      
                     $pdf->Cell(70,5,'Valor Desconto: ('.number_format($taxas, 2).'%)'.' R$ '.number_format($obj->valor_desconto, 2),0,1,'R');

                     $pdf->SetXY(500,$alturalinha);
                     $pdf->Cell(70,5,'Valor Liquido: '.'R$ '.number_format($obj->valor_liquido, 2),0,1,'R');

                    }


                    //serviços
                    $unidade='';
                    $qt = 0;
                    $vl = 0;
                    $vlt = 0;
                    $qtitens=0;
   
                    $pedido = new PedidoFrotas($param['id']);
                 if (!$pedido->estabelecimento_id) {
                    throw new Exception('Pedido não tem orçamento de cliente');
                 } 
                 $obj1 = Propostas::where('pedido_frotas_id','=',$pedido->id)
                                               ->where('pessoa_id','=',$pedido->estabelecimento_id)
                                               ->load();
                 if ($obj1)  {
                    foreach ($obj1 as $obj) {
                         break;
                    }
                 }           
                      
                 $idpedido = $pedido->id;
                 $pessoa = new Pessoa($obj->pessoa_id);      
               
                
                 $obj1 = Propostas::where('pedido_frotas_id','=',$idpedido)
                                               ->where('pessoa_id','=',$pessoa->id)
                                               ->load();
   
                    $itenscotacao = ItensPropostas::where('propostas_id','=',$obj1[0]->id)
                                                  ->where('tipo','=',2)
                                                  ->where('deleted_at','is',null)
                                               ->load();

                    if ($itenscotacao) {
                        //cabecalho
                        if ( ($linha==0) || ($linha >= 40) ){
                          $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                          $pag=$pag + 1; 
                          $alturalinha = 231;
                          $linha = 12;
                       } 
                       $alturalinha= $this->cabecalhoServico($pdf,$param,$alturalinha);
                       foreach ($itenscotacao as $itens) {
                            //detalhes
                            if ( ($linha==0) || ($linha >= 40) ){
                               $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                              $linha = 0;
                              $pag=$pag + 1; 
                              $alturalinha = 231;
                              $alturalinha= $this->cabecalhoServico($pdf,$param,$alturalinha);

                            }
                             $produto = new Produto($itens->produto_id);
   
                            $pdf->setFont('arial','',6);   
                            $pdf->SetXY(25,$alturalinha);
                            $pdf->Cell(70,5,utf8_decode($itens->id),0,1,'L');

                            $nome = (string) ($produto->nome ?? '');
                            $tamanho = strlen($nome);
                            // $tamanho = strlen($itens->nome);
                             if ($tamanho>=71) {
                               $pdf->SetXY(45,$alturalinha-2);
                               $pdf->MultiCell(340,10,substr(utf8_decode($nome),0,255),0,'L',false);
                               $pdf->setFont('arial','',6);  
                               $pdf->SetXY(260,$alturalinha);
                               if (!empty($itens->diasdegarantia) || !empty($itens->qtdekmgarantia)) {
                                $garantia = '';
                            
                                if (!empty($itens->diasdegarantia)) {
                                    $garantia .= $itens->diasdegarantia . ' Dias ';
                                }
                            
                                if (!empty($itens->qtdekmgarantia)) {
                                    $garantia .= $itens->qtdekmgarantia . ' Km';
                                }
                            
                                $pdf->Cell(70, 5, $garantia, 0, 1, 'L');
                            }                               $pdf->SetXY(360,$alturalinha);
                               $pdf->Cell(70,5,$itens->qtde,0,1,'C');
                               $pdf->SetXY(420,$alturalinha);
                               $pdf->Cell(70,5,'R$ '.number_format($itens->valor, 2),0,1,'R');
                               $pdf->SetXY(500,$alturalinha);
                               $pdf->Cell(70,5,'R$ '.number_format($itens->valor_total, 2),0,1,'R');
                               $alturalinha += 24;
                             } else {
                               $pdf->SetXY(45,$alturalinha);
                               $pdf->MultiCell(340,5,utf8_decode($nome),0,'L', false);
                               $pdf->setFont('arial','',6);   
                               $pdf->SetXY(260,$alturalinha);
                               if (!empty($itens->diasdegarantia) || !empty($itens->qtdekmgarantia)) {
                                $garantia = '';
                            
                                if (!empty($itens->diasdegarantia)) {
                                    $garantia .= $itens->diasdegarantia . ' Dias ';
                                }
                            
                                if (!empty($itens->qtdekmgarantia)) {
                                    $garantia .= $itens->qtdekmgarantia . ' Km';
                                }
                            
                                $pdf->Cell(70, 5, $garantia, 0, 1, 'L');
                            }                               $pdf->SetXY(360,$alturalinha);
                               $pdf->Cell(70,5,$itens->qtde,0,1,'C');
                               $pdf->SetXY(420,$alturalinha);
                               $pdf->Cell(70,5,'R$ '.number_format($itens->valor, 2),0,1,'R');
                               $pdf->SetXY(500,$alturalinha);
                               $pdf->Cell(70,5,'R$ '.number_format($itens->valor_total, 2),0,1,'R');
                             }
   
                            $alturalinha += 15; 
                            $linha +=1;
   
                            $pdf->ln(1); 
                            $qtitens++;
                            $qt += $itens->qtde;
                            $vl += $itens->valor;
                            $vlt += $itens->valor_total;
                        }
                        $alturalinha+=15;
                        //rodape
                        //   $pdf->SetXY(25,$alturalinha);
                     //   $pdf->Cell(0,15,"","B",1,'C');
                       //     $alturalinha+=15;
                        $pdf->SetFont('arial','B',6); 
                        $pdf->SetXY(25,$alturalinha);
                        $pdf->SetFillColor(235,239,240);
                        $pdf->Rect(26, $alturalinha-5, 542, 15, 'F');
                        $pdf->Cell(70,5,utf8_decode('Total Geral '.$qtitens.' Itens'),0,1,'L');
   
                        $pdf->SetXY(360,$alturalinha);
                        $pdf->Cell(70,5,$qt,0,1,'C');
                        $pdf->SetXY(420,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($vl, 2),0,1,'R');
                        $pdf->SetXY(500,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($vlt, 2),0,1,'R');
   
                        $pdf->Cell(0,15,"","B",1,'C');
   
                        $pdf->SetFont('arial','I',6); 
                        $alturalinha+=26;
   
                        $pdf->SetXY(25,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total dos Serviços sem descontos: '.'R$ '.number_format($obj->total_servicos_sem_desconto, 2)),0,1,'L');
   
                        $pdf->SetXY(260,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total dos Produtos sem descontos: '.'R$ '.number_format($obj->total_produtos_sem_desconto, 2)),0,1,'L');

                        $pdf->SetXY(500,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total geral sem descontos: '.'R$ '.number_format($obj->total_geral_sem_desconto, 2)),0,1,'R');
   
                        $alturalinha+=15;
                        $pdf->SetFont('arial','B',6); 
                        $pdf->SetTextColor(255, 0, 0);

                        $desconto_contratual = $obj->desconto_contratual ?? 0;
                        $desconto_contratual = is_numeric($desconto_contratual) ? (float) $desconto_contratual : 0.0;

                        $pdf->SetXY(260,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Desconto contratual de : '.number_format($desconto_contratual, 2).'%'),0,1,'L');                                            

                        $total_desconto = 0;
                        if (isset($obj->total_geral_sem_desconto) && isset($obj->total_geral_com_desconto)) {
                            $total_desconto = $obj->total_geral_sem_desconto - $obj->total_geral_com_desconto;
                        }
                        $pdf->SetXY(500,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total do desconto: '.'R$ '.number_format($total_desconto, 2)),0,1,'R');

                        $alturalinha+=15;
                        $pdf->SetTextColor(0, 0, 0);

                        $pdf->SetFont('arial','I',6); 
                        $pdf->SetXY(25,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total dos Serviços com descontos: '.'R$ '.number_format($obj->total_servicos_com_desconto, 2)),0,1,'L');
   
                        $pdf->SetXY(260,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total dos Produtos com descontos: '.'R$ '.number_format($obj->total_produtos_com_desconto, 2)),0,1,'L');

                        $pdf->SetXY(500,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total geral com descontos: '.'R$ '.number_format($obj->total_geral_com_desconto, 2)),0,1,'R');
   
//                        $taxas = (($obj->taxacontrato +$txbancaria)) ;
  //                      $pdf->Cell(70,5,'Valor Desconto: ('.number_format($taxas, 2).'%)'.' R$ '.number_format($obj->valor_desconto, 2),0,1,'R');
   
   
                       }

                  if (isset($param['fatura']) && $param['fatura'] == true) {               
                  } else {
                        $nome = 'documentocotacao.pdf';

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
                  }
                
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


    function cabecalhoDCot($pdf, $linha,$pag, $unidade, $id, $datacotacao, $idcot, $txbancaria)
    {
        $label = '';
        $datacotacao = new DateTime($datacotacao);
        $datacotacao = $datacotacao->format('d/m/Y');

        $cot = new Propostas($idcot);
        $ped = new PedidoFrotas($cot->pedido_frotas_id);
        $dep = new DepartamentoUnit($ped->departamento_unit_id);
        $unit = new SystemUnit($dep->system_unit_id);
        $retiradopor = new Pessoa($cot->motorista_retirada_id);
        $retiradapor = $retiradopor->nome;
        $nomedepartamento = new DepartamentoUnit($ped->departamento_unit_id);
        $nomedepartamento = $nomedepartamento->name;

        $pessoa = new Pessoa($cot->pessoa_id);      
                     
        $cnpj = $pessoa->documento;
        $nome = $pessoa->nome;

        $pessoa_endereco = PessoaEndereco::where('pessoa_id','=',$cot->pessoa_id)
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

        //historico de aprovado
        $histpedaprovado = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $ped->id)
                                                ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
                                                ->orderBy('data_operacao', 'desc')
                                                ->load();  
                                            
        if ($histpedaprovado) {
            foreach ($histpedaprovado as $histpeda) {
                $aprovadorfrotas = new AprovadorFrotas($histpeda->aprovador_frotas_id);
                $usuarioaprovado = new SystemUsers($aprovadorfrotas->system_users_id);
                $usuarioaprovado = $usuarioaprovado->name;
                $status = new EstadoPedidoFrotas($histpeda->estado_pedido_frotas_id);
                $statusaprovado = $status->nome;
                $dataaprovado = $histpeda->data_operacao;
                $dataaprovado = new DateTime($dataaprovado);
                $dataaprovado = $dataaprovado->format('d/m/Y H:i');
                $obsaprovado = $histpeda->obs;
                break;              
            }
        } else {
            $usuarioaprovado = '';
            $statusaprovado = '';
            $dataaprovado = '';
            $obsaprovado = '';
        }

        //historico de abertura
        $histpedabertura = PedidoFrotasHistorico::where('pedido_frotas_id','=',$ped->id)
                                                ->where('estado_pedido_frotas_id','=',EstadoPedidoFrotas::PENDENTE)
                                                ->orderBy('data_operacao','desc')
                                                ->load();  

        if ($histpedabertura) {
            foreach ($histpedabertura as $histped) {
                $aprovadorfrotas = new AprovadorFrotas($histpeda->aprovador_frotas_id);
                $usuarioabertura = new SystemUsers($aprovadorfrotas->system_users_id);
                $usuarioabertura = $usuarioabertura->name;
                $status = new EstadoPedidoFrotas($histped->estado_pedido_frotas_id);
                $statusabertura = $status->nome;
                $dataabertura = new DateTime($histped->data_operacao);
                $dataabertura= $dataabertura->format('d/m/Y H:i');
                break;              
            }
            } else {
                $usuarioabertura = '';
                $statusabertura = '';
                $dataabertura = '';
            }

            // histórico de entregue
            $histpedentregue = PedidoFrotasHistorico::where('pedido_frotas_id','=',$ped->id)
            ->where('estado_pedido_frotas_id','=',EstadoPedidoFrotas::ENTREGUE)
            ->orderBy('data_operacao','desc')
            ->load();  

            if ($histpedentregue) {
            foreach ($histpedentregue as $histped) {
            $aprovadorfrotas = new AprovadorFrotas($histpeda->aprovador_frotas_id);    
            $usuarioentregue = new SystemUsers($aprovadorfrotas->system_users_id);
            $usuarioentregue = $usuarioentregue->name;
            $status = new EstadoPedidoFrotas($histped->estado_pedido_frotas_id);
            $statusentregue = $status->nome;
            $dataentregue = new DateTime($histped->data_operacao);
            $dataentregue = $dataentregue->format('d/m/Y H:i');
            break;              
            }
            } else {
            $usuarioentregue = '';
            $statusentregue = '';
            $dataentregue = '';
            }

        
             //historico ataul
             $histpedatual = PedidoFrotasHistorico::where('pedido_frotas_id','=',$ped->id)
                                                    ->orderBy('data_operacao','desc')
                                                    ->load();  

            if ($histpedatual) {
            foreach ($histpedatual as $histped) {
            $aprovadorfrotas = new AprovadorFrotas($histpeda->aprovador_frotas_id);
            $usuarioatual = new SystemUsers($aprovadorfrotas->system_users_id);
            $usuarioatual = $usuarioatual->name;
            $status = new EstadoPedidoFrotas($histped->estado_pedido_frotas_id);
            $statusatual = $status->nome;
            $dataatual = new DateTime($histped->data_operacao);
            break;              
            }
            } else {
            $usuarioatual = '';
            $statusatual = '';
            $dataatual = '';
            }


        $veiculo = new Veiculos($ped->veiculos_id);
        $modelo = new Modelo($veiculo->modelo_id);
        $tipocombustivel = new TipoCombustivel($veiculo->tipo_combustivel_id);
        $tipomanutencao = new TipoManutencao($ped->tipo_manutencao_id);
       
      
     

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
        $pdf->Cell($pdf->GetStringWidth('Pedido Nº :') + 2, 5, utf8_decode('Pedido Nº :'), 0, 0, 'L');
        
        // Valor do pedido em fonte normal
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell($pdf->GetStringWidth($ped->id) + 5, 5, utf8_decode($ped->id), 0, 0, 'L');
        
        // Negrito para "Proposta Nº :"
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Proposta Nº :') + 2, 5, utf8_decode('Proposta Nº :'), 0, 0, 'L');
        
        // Valor da proposta em fonte normal
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode($cot->id), 0, 1, 'L');
        

        $pdf->SetXY(350, $y);

        // Negrito para "Status:"
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Retirado por: ') + 1, 5, utf8_decode('Retirado por: '), 0, 0, 'L');
        
        // Normal para o valor da variável
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$retiradapor), 0, 1, 'L');
        
        
        $y += $lineHeight;
        
        // Segunda linha (Placa e Status de entrega)
        $pdf->SetXY(110, $y);
        
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Placa / Identificação / Matrícula :') + 1, 5, utf8_decode('Placa / Identificação / Matrícula :'), 0, 0, 'L');
        
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$veiculo->placa), 0, 1, 'L');

        $pdf->SetXY(350, $y);
        $pdf->SetFont('Arial', 'B', 6);
        
        $pdf->SetXY(350,$y);
        $pdf->Cell(70,5,utf8_decode('Informações da entrega : '.$statusentregue.' em '.$dataentregue.' por '.$usuarioentregue),0,1,'L');

       

       
        
        $y += $lineHeight;
        
      
        $pdf->SetXY(110, $y);
               
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Pedido aberto por:') + 1, 5, utf8_decode('Pedido aberto por:'), 0, 0, 'L');
        
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$usuarioabertura), 0, 1, 'L');

        $pdf->SetXY(350, $y);

        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Prazo de entrega:') + 1, 5, utf8_decode('Prazo de entrega:'), 0, 0, 'L');
        
        $pdf->SetFont('Arial', '', 6);
        if (!empty($cot->data_previsao_entrega)) {
            $dataentrega = new DateTime($cot->data_previsao_entrega);
            $dataentrega = $dataentrega->format('d/m/Y');
        } else {
            $dataentrega='';
        }
        $pdf->Cell(0, 5, utf8_decode(' '.$dataentrega), 0, 1, 'L');

        
        $y += $lineHeight;
        
        $unit = new SystemUnit(TSession::getValue('idunit'));
        $entidade = new Entidade($unit->entidade_id);
        $administradora = new Administradora($entidade->administradora_id);
        $cidadeadm = new Cidade($administradora->cidade_id);
        $estadoadm = new Estado($cidadeadm->estado_id);

          //dados do orgão
          $cidadeunit = new Cidade($unit->cidade_id);
          $estadounit = new Estado($cidadeunit->estado_id);

        // Quarta linha (Unidade e Status atual)
    
        $pdf->SetXY(110, $y);
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Unidade/Departamento:') + 1, 5, utf8_decode('Unidade/Departamento:'), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.substr($nomedepartamento,0,48)), 0, 1, 'L');
        $pdf->SetXY(350, $y);
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Status atual:') + 1, 5, utf8_decode('Status atual:'), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$statusatual), 0, 1, 'L');
       
        $y += $lineHeight;
     
     /*   $pdf->Ln(4); // Pequeno espaço adicional, se necessário
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
        $pdf->ln(1);*/
    
  

        $pdf->SetY(85); // Linha separadora superior
        $pdf->Cell(0,5,"","B",1,'C');
        
        // Fonte e fundo
        $pdf->SetFont('arial','B',6); 
        $pdf->SetFillColor(235,239,240);
        
        // Retângulo e texto: Dados da Gerenciadora
        $pdf->Rect(26, 97, 270, 11, 'F');
        $pdf->SetXY(26, 100); // Pequeno recuo para o texto
        $pdf->Cell(0,5,utf8_decode('Dados da gerenciadora'),0,1,'L');
        
        // Retângulo e texto: Dados do Cliente Solicitante
        $pdf->Rect(296, 97, 270, 11, 'F');
        $pdf->SetXY(299, 100); // Recuo também aqui
        $pdf->Cell(0,5,utf8_decode('Dados do cliente solicitante'),0,1,'L');
        
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(25,110);
        $pdf->Cell(70,5,utf8_decode(substr($administradora->nome,0,58)),0,1,'L');   
        $pdf->SetXY(180,110);
        $pdf->Cell(70,5,utf8_decode('CNPJ: '.$administradora->cnpj),0,1,'L');
        
        $pdf->SetXY(300,110);
        $pdf->Cell(70,5,utf8_decode(substr($unit->name,0,46)),0,1,'L');   
        $pdf->SetXY(465,110);
        $pdf->Cell(70,5,utf8_decode('CNPJ: '.$unit->cnpj),0,1,'L');
        $pdf->SetFont('arial','',8);

        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(25,121);
        $pdf->Cell(70,5,utf8_decode(substr('Endereço : '.$administradora->rua.' nº '.$administradora->numero,0,58)),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(180,121);
        $pdf->Cell(70,5,utf8_decode('Bairro : '.$administradora->bairro),0,1,'L');
       
        $pdf->SetXY(300,121);
        $pdf->Cell(70,5,utf8_decode(substr('Endereço : '.$unit->rua.' nº '.$unit->numero,0,58)),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(465,121);
        $pdf->Cell(70,5,utf8_decode('Bairro : '.$unit->bairro),0,1,'L');

        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(25,132);
        $pdf->Cell(70,5,utf8_decode('Cidade : '.$cidadeadm->nome.' - '.$estadoadm->sigla),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(180,132);
        $pdf->Cell(70,5,utf8_decode('Telefone : '.$administradora->telefone01),0,1,'L');

        $pdf->SetXY(300,132);
        $pdf->Cell(70,5,utf8_decode('Cidade : '.$cidadeunit->nome .' - '.$estadounit->sigla),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(465,132);
        $pdf->Cell(70,5,utf8_decode('Telefone : '.$unit->telefone01),0,1,'L');
        

        $pdf->SetXY(25,143);
        $pdf->Cell(70,5,utf8_decode('Email : '.$administradora->email),0,1,'L');

        $pdf->SetXY(300,143);
        $pdf->Cell(70,5,utf8_decode('Email : '.$unit->email),0,1,'L');



        // dados estabelecimento e dados do orçamento
        $pdf->SetFont('arial','B',6); 
        $pdf->SetFillColor(235,239,240);
        
        // Retângulo e texto: Dados da Gerenciadora
        $pdf->Rect(26, 151, 270, 11, 'F');
        $pdf->SetXY(26, 154); // Pequeno recuo para o texto
        $pdf->Cell(0,5,utf8_decode('Dados da estabelecimento'),0,1,'L');
        
        // Retângulo e texto: Dados do Cliente Solicitante
        $pdf->Rect(296, 151, 270, 11, 'F');
        $pdf->SetXY(299, 154); // Recuo também aqui
        $pdf->Cell(0,5,utf8_decode('Dados do orçamento'),0,1,'L');
        
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(25,165);
        $pdf->Cell(70,5,utf8_decode(substr($pessoa->nome,0,58)),0,1,'L');   
        $pdf->SetXY(180,165);
        $pdf->Cell(70,5,utf8_decode('CNPJ: '.$pessoa->documento),0,1,'L');
        
        $pdf->SetXY(300,165);
        $pdf->Cell(70,5,utf8_decode(substr('Título manutenção: '.$ped->descricaopedido,0,58)),0,1,'L');   
        $pdf->SetXY(465,165);
        $pdf->Cell(70,5,utf8_decode('Ano Fab.: '.$veiculo->anof.' Ano Mod.:'.$veiculo->anom),0,1,'L');
        $pdf->SetFont('arial','',8);

        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(25,176);
        $pdf->Cell(70,5,utf8_decode(substr('Endereço : '.$pe->rua.' nº '.$pe->numero,0,58)),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(180,176);
        $pdf->Cell(70,5,utf8_decode('Bairro : '.$pe->bairro),0,1,'L');
    
        $pdf->SetXY(300,176);
        $pdf->Cell(70,5,utf8_decode('Chassi : '.$veiculo->chassi.' Km/Horimetro:'.$ped->km),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(465,176);
        $pdf->Cell(70,5,utf8_decode('Tipo Serviço : '.$tipomanutencao->descricao),0,1,'L');

        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(25,187);
        $pdf->Cell(70,5,utf8_decode('Cidade : '.$nomecidade),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(180,187);
        $pdf->Cell(70,5,utf8_decode('Telefone : '.$pessoa->fone),0,1,'L');

        $pdf->SetXY(300,187);
        $pdf->Cell(70,5,utf8_decode('Aprovado por : '.$nomecidade),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(465,187);

        $pdf->Cell(70,5,utf8_decode('Data Aprovação : '.$dataaprovado),0,1,'L');
        

        $pdf->SetXY(25,198);
        $pdf->Cell(70,5,utf8_decode('Email : '.$pessoa->email),0,1,'L');

        $pdf->SetXY(300,198);
        $pdf->Cell(70,5,utf8_decode(substr('Modelo : '.$modelo->descricao. '  Combustível: '.$tipocombustivel->descricao,0,58) ),0,1,'L');

        $pdf->SetXY(465,198);
        $pdf->Cell(70,5,utf8_decode('Data Abertura : '.$dataabertura),0,1,'L');
        $pdf->Cell(0,11,"","B",1,'C');
            $pdf->SetXY(25,220);
            $pdf->Cell(70,5,utf8_decode(substr('Obs : '.$cot->obs,0,100)),0,1,'L');
            $pdf->SetFont('arial','',6);
           

            $pdf->SetFont('Arial', '', 6);
            $pdf->SetXY(300,220);
            $pdf->Cell(70,5,utf8_decode(substr('Justificativa : '.$obsaprovado,0,100)),0,1,'L');
            
           
 
   }
   function cabecalhoProduto($pdf,  $alturalinha, $param = null) {
        $pdf->SetFont('arial','B',8); 
        $pdf->SetXY(25,$alturalinha);
        $pdf->SetFillColor(235,239,240);
        $pdf->Rect(26, $alturalinha-3, 542, 15, 'F');
        $pdf->Cell(70,5,utf8_decode('Produto (s)'),0,1,'L');
        $pdf->SetFont('arial','B',6); 
        $alturalinha = $alturalinha + 15;

   //     $pdf->Cell(0,17,"","B",1,'C');
        $pdf->SetXY(25,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('ID'),0,1,'L');
        $pdf->SetXY(45,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Produto'),0,1,'L');
        $pdf->SetXY(260,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Garantia'),0,1,'L');
        $pdf->SetXY(340,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Quantidade'),0,1,'R');
        $pdf->SetXY(420,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Valor'),0,1,'R');
        $pdf->SetXY(500,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Valor Total'),0,1,'R');

        $pdf->ln(1);
        return $alturalinha+15;
    }
    function cabecalhoServico($pdf, $linha, $alturalinha) {
        $pdf->SetFont('arial','B',8); 
        $pdf->SetXY(25,$alturalinha);
        $pdf->SetFillColor(235,239,240);
        $pdf->Rect(26, $alturalinha-4, 542, 15, 'F');
        $pdf->Cell(70,5,utf8_decode('Serviço (s)'),0,1,'L');
        $pdf->SetFont('arial','B',6); 

         $alturalinha = $alturalinha + 15;
       // $pdf->Cell(0,17,"","B",1,'C');
        $pdf->SetXY(25,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('ID'),0,1,'L');
        $pdf->SetXY(45,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Serviço'),0,1,'L');
        $pdf->SetXY(260,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Garantia'),0,1,'L');
        $pdf->SetXY(340,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Quantidade'),0,1,'R');
        $pdf->SetXY(420,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Valor'),0,1,'R');
        $pdf->SetXY(500,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Valor Total'),0,1,'R');

        $pdf->ln(1);
        return $alturalinha+15;
    }
}
?>