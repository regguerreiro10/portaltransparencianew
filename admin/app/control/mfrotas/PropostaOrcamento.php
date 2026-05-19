  <?php


/**
 * Gera e retorna o conteúdo do orçamento HTML para o pedido de frotas
 *
 * @param array $dados Dados necessários para preencher o orçamento
 * @return string HTML do orçamento
 */
class PropostaOrcamento extends TPage
{
     public function gerar($param = null, $pdf = null)
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
                        // If it's a fatura, we don't create a new PDF instance here
                    } else {
                        // Create a new PDF instance for the proposal
                        //code here
                        $pdf = new FPDF("P","pt","A4");
                    }
               
                 $linha=0;   
                 $pag=1;
                 $alturalinha=231;
                 $limitePagina = 760;
                 $unidade='';
                 $qt = 0;
                 $vl = 0;
                 $dc = 0;
                 $vlt = 0;
                 $qtitens=0;
                
                 $obj = new Propostas($param['id']);
                 $pedido = new PedidoFrotas($obj->pedido_frotas_id);
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
                   //     $txcontrato = 0;
                        $txbancaria = 0;
                    }
                    
                    $txcontrato = ((TSession::getValue('taxacontrato'))) ;
                
                 $obj1 = Propostas::where('pedido_frotas_id','=',$idpedido)
                                               ->where('pessoa_id','=',$pessoa->id)
                                               ->load();

              

                 $forcarNovaPagina = isset($param['fatura']) && $param['fatura'] == true;
                 if ($forcarNovaPagina) {
                    $alturalinha = $this->cabecalhoDCot($pdf, $linha, $pag, $unidade, $pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                    $pag = $pag + 1;
                    $linha = 12;
                    $forcarNovaPagina = false;
                 }

                 $itenscotacao = ItensPropostas::where('propostas_id','=',$obj1[0]->id)
                                               ->where('tipo','=',1)
                                               ->where('deleted_at','is',null)
                                            ->load();
                 if ($itenscotacao) {
                     //cabecalho
                     if ($linha == 0){
                       $alturalinha = $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                       $pag=$pag + 1; 
                       $linha = 12;
                       $forcarNovaPagina = false;
                    }
                    $alturalinha= $this->cabecalhoProduto($pdf,$alturalinha,$param);

                     foreach ($itenscotacao as $itens) {
                        $tamanho = strlen($itens->produto->nome);
                        $alturaItem = ($tamanho >= 71) ? 39 : 15;
                        if (($alturalinha + $alturaItem + 20) > $limitePagina){
                            $alturalinha = $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                            $linha = 0;
                            $pag=$pag + 1; 
                            $alturalinha= $this->cabecalhoProduto($pdf,$alturalinha,$param);
                        }
                         //detalhes
                        //  $produto = new Produto($itens->produto_id);

                         $pdf->setFont('arial','',6);   
                       
                         $pdf->SetXY(23,$alturalinha);
                         $pdf->Cell(70,5,utf8_decode($itens->id),0,1,'L');

                          if ($tamanho>=71) {

                            $pdf->SetXY(45,$alturalinha-2);
                            $pdf->MultiCell(340,10,substr(utf8_decode($itens->produto->nome),0,255),0,'L',false);
                            $pdf->setFont('arial','',4);

                            $tipopeca = new TipoPecas($itens->tipo_pecas_id);
                            $pdf->SetXY(245,$alturalinha);
                            $pdf->Cell(70,5,$tipopeca->descricao,0,1,'L');

                            $pdf->SetXY(290,$alturalinha);
                            if (!empty($itens->diasdegarantia) || !empty($itens->qtdekmgarantia)) {
                                $garantia = '';
                            
                                if (!empty($itens->diasdegarantia)) {
                                    $garantia .= $itens->diasdegarantia . ' Dias ';
                                }
                            
                                if (!empty($itens->qtdekmgarantia)) {
                                    $garantia .= $itens->qtdekmgarantia . ' Km';
                                }
                            
                                $pdf->Cell(70, 5, $garantia, 0, 1, 'L');
                            }            
                                              
                            $pdf->SetXY(340,$alturalinha);
                            $pdf->Cell(70,5,substr($itens->marca_modelo,0,15),0,1,'L');
                            $pdf->setFont('arial','',6);   
                            $pdf->SetXY(350,$alturalinha);
                            $pdf->Cell(70,5,$itens->qtde,0,1,'C');

                            $pdf->SetXY(370,$alturalinha);
                            $pdf->Cell(70,5,'R$ '.number_format($itens->valor, 2),0,1,'R');
                            $pdf->SetXY(420,$alturalinha);
                            $pdf->Cell(70,5,'R$ '.number_format($itens->perc_desconto, 2),0,1,'R');
                            $pdf->SetXY(485,$alturalinha);
                            $pdf->Cell(70,5,'R$ '.number_format($itens->valor_total, 2),0,1,'R');
                            if ($itens->estado_pedido_frotas_id<>NULL) {
                                  $status = new EstadoPedidoFrotas($itens->estado_pedido_frotas_id);
                                  $pdf->SetXY(555,$alturalinha);
                                  $pdf->Cell(70,5,substr($status->nome,0,1),0,1,'L');
                               }
                               $alturalinha += 24;
                          } else {
                            $pdf->SetXY(45,$alturalinha);
                            $pdf->MultiCell(340,5,utf8_decode($itens->produto->nome),0,'L', false);

                            $pdf->setFont('arial','',4);   

                            $pdf->SetXY(290,$alturalinha);
                            if (!empty($itens->diasdegarantia) || !empty($itens->qtdekmgarantia)) {
                                $garantia = '';
                            
                                if (!empty($itens->diasdegarantia)) {
                                    $garantia .= $itens->diasdegarantia . ' Dias ';
                                }
                            
                                if (!empty($itens->qtdekmgarantia)) {
                                    $garantia .= $itens->qtdekmgarantia . ' Km';
                                }
                            
                                $pdf->Cell(70, 5, $garantia, 0, 1, 'L');
                            }                            
                            
                            $tipopeca = new TipoPecas($itens->tipo_pecas_id);
                            $pdf->SetXY(245,$alturalinha);
                            $pdf->Cell(70,5,$tipopeca->descricao,0,1,'L');
                            
                            $pdf->SetXY(340,$alturalinha);
                            $marcaModelo = $itens->marca_modelo ?? '';                 // evita null
                            $marcaModelo = mb_substr($marcaModelo, 0, 15, 'UTF-8');    // corta com segurança

                            // Se seu FPDF não está em UTF-8, use utf8_decode:
                            $pdf->Cell(70, 5, utf8_decode($marcaModelo), 0, 1, 'L');
                            $pdf->setFont('arial','',6);   
                            $pdf->SetXY(355,$alturalinha);
                            $pdf->Cell(70,5,$itens->qtde,0,1,'C');
                            $xvalor = $itens->valor ?? 0; // evita null
                            $pdf->SetXY(370,$alturalinha);
                            $pdf->Cell(70,5,'R$ '.number_format($xvalor, 2),0,1,'R');

                            $xperc_desconto = $itens->perc_desconto ?? 0; // evita null
                            $pdf->SetXY(420,$alturalinha);
                            $pdf->Cell(70,5,'R$ '.number_format($xperc_desconto, 2),0,1,'R');

                            $xvalor_total = $itens->valor_total ?? 0; // evita null
                            $pdf->SetXY(485,$alturalinha);
                            $pdf->Cell(70,5,'R$ '.number_format($xvalor_total, 2),0,1,'R');

                            if ($itens->estado_pedido_frotas_id<>NULL) {
                                  $status = new EstadoPedidoFrotas($itens->estado_pedido_frotas_id);
                                  $pdf->SetXY(555,$alturalinha);
                                  $pdf->Cell(70,5,substr($status->nome,0,1),0,1,'L');
                               }
                             }

                         $alturalinha += 15; 
                         $linha +=1;

                         $pdf->ln(1); 
                         $qtitens++;
                         $qt += $itens->qtde;
                         $vl += round(($itens->valor* $itens->qtde) ?? 0, 2);
                         $dc += round($itens->perc_desconto ?? 0, 2);
                         $vlt += round(($itens->valor* $itens->qtde)- $itens->perc_desconto ?? 0, 2);
                     }
                     if (($alturalinha + 45) > $limitePagina){
                        $alturalinha = $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                        $linha = 0;
                        $pag = $pag + 1;
                        $alturalinha = $this->cabecalhoProduto($pdf,$alturalinha,$param);
                     }
                     $alturalinha+=5;
                     //rodape
                     //   $pdf->SetXY(25,$alturalinha);
                  //   $pdf->Cell(0,15,"","B",1,'C');
                    //     $alturalinha+=15;
                     $pdf->SetFont('arial','B',6); 
                     $pdf->SetXY(25,$alturalinha);
                     $pdf->SetFillColor(235,239,240);
                     $pdf->Rect(26, $alturalinha-5, 542, 15, 'F');
                     $pdf->Cell(70,5,utf8_decode('Total Geral '.$qtitens.' Itens'),0,1,'L');

                     $pdf->SetXY(355,$alturalinha);
                     $pdf->Cell(70,5,$qt,0,1,'C');
                     $pdf->SetXY(370,$alturalinha);
                     $pdf->Cell(70,5,'R$ '.number_format($vl, 2),0,1,'R');

                     $pdf->SetXY(420,$alturalinha);
                     $pdf->Cell(70,5,'R$ '.number_format($dc, 2),0,1,'R');

                     $pdf->SetXY(485,$alturalinha);
                     $pdf->Cell(70,5,'R$ '.number_format($vlt, 2),0,1,'R');

                     $alturalinha+=15;
                 //    $pdf->Cell(0,15,"","B",1,'C');

                    //  $pdf->SetFont('arial','B',6); 
                    //  $alturalinha+=26;

                    //  $pdf->SetXY(82,$alturalinha);
                    //  $pdf->Cell(70,5,'Valor Bruto: '.'R$ '.number_format($obj->valor_total, 2),0,1,'R');

                    //  $pdf->SetXY(320,$alturalinha);

                  

                    //  $taxas = ((TSession::getValue('taxacontrato'))) ;
                    //  $pdf->Cell(70,5,'Valor Desconto: ('.number_format($taxas, 2).'%)'.' R$ '.number_format($obj->valor_desconto, 2),0,1,'R');

                    //  $pdf->SetXY(485,$alturalinha);
                    //  $pdf->Cell(70,5,'Valor Liquido: '.'R$ '.number_format($obj->valor_liquido, 2),0,1,'R');

                    }

                  
                    //serviços
                    $unidade='';
                    $qt = 0;
                    $vl = 0;
                    $dc = 0;
                    $vlt = 0;
                    $qtitens=0;
   
                       $obj = new Propostas($param['id']);
                 $pedido = new PedidoFrotas($obj->pedido_frotas_id);
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
                        // Se a proposta tiver apenas serviços, a página ainda não existe.
                        if ($pdf->PageNo() == 0 || $forcarNovaPagina) {
                          $alturalinha = $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                          $pag=$pag + 1;
                          $linha = 12;
                          $forcarNovaPagina = false;
                        } elseif (($alturalinha + 120) > $limitePagina) {
                          $alturalinha = $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                          $pag=$pag + 1; 
                          $linha = 12;
                        } 
                       $alturalinha= $this->cabecalhoServico($pdf,$param,$alturalinha);
                       foreach ($itenscotacao as $itens) {
                            $tamanho = strlen($itens->produto->nome);
                            $alturaItem = ($tamanho >= 71) ? 39 : 15;
                            if (($alturalinha + $alturaItem + 20) > $limitePagina){
                               $alturalinha = $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                               $linha = 0;
                               $pag=$pag + 1; 
                               $alturalinha= $this->cabecalhoServico($pdf,$param,$alturalinha);
                            }
                            //detalhes
                           //  $produto = new Produto($itens->produto_id);
   
                            $pdf->setFont('arial','',6);   
                            $pdf->SetXY(23,$alturalinha);
                            $pdf->Cell(70,5,utf8_decode($itens->id),0,1,'L');
   
                             if ($tamanho>=71) {
                               $pdf->SetXY(45,$alturalinha-2);
                               $pdf->MultiCell(340,10,substr(utf8_decode($itens->produto->nome),0,255),0,'L',false);
                               $pdf->setFont('arial','',4);  
                               $pdf->SetXY(290,$alturalinha);
                               if (!empty($itens->diasdegarantia) || !empty($itens->qtdekmgarantia)) {
                                $garantia = '';
                            
                                if (!empty($itens->diasdegarantia)) {
                                    $garantia .= $itens->diasdegarantia . ' Dias ';
                                }
                            
                                if (!empty($itens->qtdekmgarantia)) {
                                    $garantia .= $itens->qtdekmgarantia . ' Km';
                                }
                            
                                $pdf->Cell(70, 5, $garantia, 0, 1, 'L');
                               }                  
                                $pdf->setFont('arial','',6);  
             
                               $pdf->SetXY(355,$alturalinha);
                               $pdf->Cell(70,5,$itens->qtde,0,1,'C');

                               $pdf->SetXY(365,$alturalinha);
                               $pdf->Cell(70,5,'R$ '.number_format($itens->valor, 2),0,1,'R');

                               $pdf->SetXY(420,$alturalinha);
                               $pdf->Cell(70,5,'R$ '.number_format($itens->perc_desconto, 2),0,1,'R');

                               $pdf->Cell(70,5,'R$ '.number_format($itens->valor_total, 2),0,1,'R');
                               if ($itens->estado_pedido_frotas_id<>NULL) {
                                  $status = new EstadoPedidoFrotas($itens->estado_pedido_frotas_id);
                                  $pdf->SetXY(555,$alturalinha);
                                  $pdf->Cell(70,5,substr($status->nome,0,1),0,1,'L');
                               }
                               $alturalinha += 24;
                             } else {
                               $pdf->SetXY(45,$alturalinha);
                               $pdf->MultiCell(340,5,utf8_decode($itens->produto->nome),0,'L', false);
                               $pdf->setFont('arial','',4);   
                               $pdf->SetXY(290,$alturalinha);
                               if (!empty($itens->diasdegarantia) || !empty($itens->qtdekmgarantia)) {
                                $garantia = '';
                            
                                if (!empty($itens->diasdegarantia)) {
                                    $garantia .= $itens->diasdegarantia . ' Dias ';
                                }
                            
                                if (!empty($itens->qtdekmgarantia)) {
                                    $garantia .= $itens->qtdekmgarantia . ' Km';
                                }
                            
                                $pdf->Cell(70, 5, $garantia, 0, 1, 'L');
                            }        
                                                           $pdf->setFont('arial','',6);   
                       
                               $pdf->SetXY(355,$alturalinha);
                               $pdf->Cell(70,5,$itens->qtde,0,1,'C');

                               $xvalor = $itens->valor ?? 0; // evita null
                               $pdf->SetXY(365,$alturalinha);
                               $pdf->Cell(70,5,'R$ '.number_format($xvalor, 2),0,1,'R');

                               $xperc_desconto = $itens->perc_desconto ?? 0; // evita null
                               $pdf->SetXY(420,$alturalinha);
                               $pdf->Cell(70,5,'R$ '.number_format($xperc_desconto, 2),0,1,'R');
   
                               $xvalor_total = $itens->valor_total ?? 0; // evita null
                               $pdf->SetXY(485,$alturalinha);
                               $pdf->Cell(70,5,'R$ '.number_format($xvalor_total, 2),0,1,'R');
                               if ($itens->estado_pedido_frotas_id<>NULL) {
                                  $status = new EstadoPedidoFrotas($itens->estado_pedido_frotas_id);
                                  $pdf->SetXY(555,$alturalinha);
                                  $pdf->Cell(70,5,substr($status->nome,0,1),0,1,'L');
                                  
                               }
                             }
   
                            $alturalinha += 15; 
                            $linha +=1;
   
                            $pdf->ln(1); 
                            $qtitens++;
                            $qt += $itens->qtde;
                            $vl += $itens->valor * $itens->qtde;
                            $dc += $itens->perc_desconto;
                            $vlt += $itens->valor_total;
                        }
                        if ($pdf->PageNo() == 0) {
                            $alturalinha = $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                            $linha = 12;
                            $pag = $pag + 1;
                        } elseif (($alturalinha + 90) > $limitePagina) {
                            $alturalinha = $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                            $linha = 0;
                            $pag = $pag + 1;
                            $alturalinha = $this->cabecalhoServico($pdf,$param,$alturalinha);
                        }
                        $alturalinha+=5;
                        //rodape
                        //   $pdf->SetXY(25,$alturalinha);
                     //   $pdf->Cell(0,15,"","B",1,'C');
                       //     $alturalinha+=15;
                        $pdf->SetFont('arial','B',6); 
                        $pdf->SetXY(25,$alturalinha);
                        $pdf->SetFillColor(235,239,240);
                        $pdf->Rect(26, $alturalinha-5, 542, 15, 'F');
                        $pdf->Cell(70,5,utf8_decode('Total Geral '.$qtitens.' Itens'),0,1,'L');
   
                        $pdf->SetXY(355,$alturalinha);
                        $pdf->Cell(70,5,$qt,0,1,'C');

                        $xvl = $vl ?? 0; // evita null
                        $pdf->SetXY(365,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($obj->total_servicos_sem_desconto ?? 0, 2),0,1,'R');

                        $xdc = $dc ?? 0; // evita null
                        $pdf->SetXY(420,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format(($obj->total_servicos_sem_desconto-$obj->total_servicos_com_desconto) ?? 0, 2),0,1,'R');

                        $xvlt = $vlt ?? 0; // evita null     
                        $pdf->SetXY(485,$alturalinha);
                        $pdf->Cell(70,5,'R$ '.number_format($obj->total_servicos_com_desconto ?? 0, 2),0,1,'R');
                    }
                        if ($pdf->PageNo() == 0) {
                            $alturalinha = $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                            $linha = 12;
                            $pag = $pag + 1;
                        } elseif (($alturalinha + 90) > $limitePagina) {
                            $alturalinha = $this->cabecalhoDCot($pdf, $linha,$pag,$unidade,$pedido->id, $pedido->dt_pedido, $obj->id, $txbancaria);
                            $linha = 0;
                            $pag = $pag + 1;
                        }
                        $pdf->Cell(0,15,"","B",1,'C');
   
                        $pdf->SetFont('arial','I',6); 
                        $alturalinha+=26;

                        $xtotal_servicos_sem_desconto = $obj->total_servicos_sem_desconto ?? 0; // evita null
                        $pdf->SetXY(25,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total dos Serviços sem descontos: '.'R$ '.number_format($xtotal_servicos_sem_desconto, 2)),0,1,'L');
   
                        $xtotal_produtos_sem_desconto = $obj->total_produtos_sem_desconto ?? 0; // evita null
                        $pdf->SetXY(260,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total dos Produtos sem descontos: '.'R$ '.number_format($xtotal_produtos_sem_desconto, 2)),0,1,'L');

                        $xtotal_geral_sem_desconto = $obj->total_geral_sem_desconto ?? 0; // evita null
                        $pdf->SetXY(485,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total geral sem descontos: '.'R$ '.number_format($xtotal_geral_sem_desconto, 2)),0,1,'R');
   
                        $alturalinha+=15;
                        $pdf->SetFont('arial','B',6); 
                        $pdf->SetTextColor(255, 0, 0);

                        $xdescontocontratual = $obj->desconto_contratual ?? 0; // evita null
                        $pdf->SetXY(260,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Desconto contratual de : '.number_format($xdescontocontratual, 2).'%'),0,1,'L');                                            

                        $total_desconto = 0;
                        if (isset($obj->total_geral_sem_desconto) && isset($obj->total_geral_com_desconto)) {
                            $total_desconto = ($obj->total_geral_sem_desconto - $obj->total_geral_com_desconto) ?? 0; // evita null
                        }
                        $pdf->SetXY(485,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total do desconto: '.'R$ '.number_format($total_desconto, 2)),0,1,'R');

                        $alturalinha+=15;
                        $pdf->SetTextColor(0, 0, 0);


                        $xtotal_servicos_com_desconto = $obj->total_servicos_com_desconto ?? 0; // evita null 
   
                        $xtotal_produtos_com_desconto = $obj->total_produtos_com_desconto ?? 0; // evita null

                        $xtotal_geral_com_desconto = $obj->total_geral_com_desconto ?? 0; // evita null

                        $pdf->SetFont('arial','I',6); 
                        $pdf->SetXY(25,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total dos Serviços com descontos: '.'R$ '.number_format($xtotal_servicos_com_desconto, 2)),0,1,'L');
   
                        $pdf->SetXY(260,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total dos Produtos com descontos: '.'R$ '.number_format($xtotal_produtos_com_desconto, 2)),0,1,'L');

                        $pdf->SetXY(485,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Total geral com descontos: '.'R$ '.number_format($xtotal_geral_com_desconto, 2)),0,1,'R');
   
                        $alturalinha+=15;
                        $pdf->Cell(0,15,"","B",1,'C');
                        $alturalinha+=15;
                        $pdf->SetXY(25,$alturalinha);
                        $pdf->Cell(70,5,utf8_decode('Legenda: P: PRE-APROVADO; A: APROVADO; R: REPROVADO '),0,1,'L');
   
//                        $taxas = (($obj->taxacontrato +$txbancaria)) ;
  //                      $pdf->Cell(70,5,'Valor Desconto: ('.number_format($taxas, 2).'%)'.' R$ '.number_format($obj->valor_desconto, 2),0,1,'R');
   
   
                       
                
                 if (isset($param['fatura']) && $param['fatura'] == true) {
                     // If it's a fatura, we don't output the PDF here
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
                if ($aprovadorfrotas) {
                    $usuarioaprovado = new SystemUsers($aprovadorfrotas->system_users_id);
                    $usuarioaprovado = $usuarioaprovado->name;
                    $status = new EstadoPedidoFrotas($histpeda->estado_pedido_frotas_id);
                    $statusaprovado = $status->nome;
                    $dataaprovado = $histpeda->data_operacao;
                    $dataaprovado = new DateTime($dataaprovado);
                    $dataaprovado = $dataaprovado->format('d/m/Y H:i');
                    $obsaprovado = $histpeda->obs;
                    break;              
                } else {
                    $usuarioaprovado = '';
                    $statusaprovado = '';
                    $dataaprovado = '';
                    $obsaprovado = '';
                }
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
                $aprovadorfrotas = new AprovadorFrotas($histped->aprovador_frotas_id);
                if ($aprovadorfrotas) {                    
                    $usuarioabertura = new SystemUsers($aprovadorfrotas->system_users_id);
                    $usuarioabertura = $usuarioabertura->name;
                    $status = new EstadoPedidoFrotas($histped->estado_pedido_frotas_id);
                    $statusabertura = $status->nome;
                    $dtfinalizacao = $ped->dt_finalizacao;
                    $dataabertura = new DateTime($histped->data_operacao);
                    $dataabertura= $dataabertura->format('d/m/Y H:i');
                    break;           
                }   
                else {
                    $usuarioabertura = '';
                    $statusabertura = '';
                    $dataabertura = '';
                }
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
                $aprovadorfrotas = new AprovadorFrotas($histped->aprovador_frotas_id);    
                if ($aprovadorfrotas) {
                    $usuarioentregue = new SystemUsers($aprovadorfrotas->system_users_id);
                    $usuarioentregue = $usuarioentregue->login;
                    $status = new EstadoPedidoFrotas($histped->estado_pedido_frotas_id);
                    $statusentregue = $status->nome;
                    $dataentregue = new DateTime($histped->data_operacao);
                    $dataentregue = $dataentregue->format('d/m/Y H:i');
                    break;  
                } else {
                    $usuarioentregue = '';
                    $statusentregue = '';
                    $dataentregue = '';
                }
                       
            }
            } else {
               $usuarioentregue = '';
               $statusentregue = '';
               $dataentregue = '';
            }

        
             //historico ataul
             $histpedatual = PropostasHistorico::where('propostas_id','=',$cot->id)
                                                    ->orderBy('data_historico','desc')
                                                    ->load();  

            if ($histpedatual) {
                foreach ($histpedatual as $histped) {
                    $aprovadorfrotas = new AprovadorFrotas($histped->aprovador_frotas_id);
                    if ($aprovadorfrotas) {
                        $usuarioatual = new SystemUsers($aprovadorfrotas->system_users_id);
                        $usuarioatual = $usuarioatual->name;
                        $status = new EstadoPedidoFrotas($histped->estado_pedido_frotas_id);
                        $statusatual = $status->nome;
                        $dataatual = new DateTime($histped->data_historico);
                        break;              
                    } else {
                        $usuarioatual = '';
                        $statusatual = '';
                        $dataatual = '';
                    }
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
        $pdf->Cell(70,5,utf8_decode('Informações da entrega : '.$statusentregue.' em '.$dataentregue),0,1,'L');

       

       
        
        $y += $lineHeight;
        
      
        $pdf->SetXY(110, $y);
               
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Pedido aberto por:') + 1, 5, utf8_decode('Pedido aberto por:'), 0, 0, 'L');
        
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, utf8_decode(' '.$usuarioabertura), 0, 1, 'L');

        $pdf->SetXY(350, $y);

        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Por '.$usuarioentregue.' Prazo de entrega:') + 1, 5, utf8_decode('Por '.$usuarioentregue.' Prazo de entrega:'), 0, 0, 'L');
        
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


          $pdf->SetXY(110, $y);
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Responsável técnico:') + 1, 5, utf8_decode('Responsável técnico:'), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 6);
        $resp = $cot->responsavel_tecnico ?? '';           // evita null
        $resp = mb_substr((string)$resp, 0, 100, 'UTF-8'); // corta com segurança

        // FPDF não é UTF-8. Converta para ISO-8859-1 (ou use utf8_decode se preferir)
        $pdf->Cell(
            0,
            5,
            iconv('UTF-8','ISO-8859-1//TRANSLIT',' '.$resp),
            0,
            1,
            'L'
        );

        

        $pdf->SetXY(350, $y);
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell($pdf->GetStringWidth('Finalizado em:') + 1, 5, utf8_decode('Finalizado em:'), 0, 0, 'L');
 
        if (!empty($dtfinalizacao)) {
            $dataFormatada = date('d/m/Y', strtotime($dtfinalizacao));
        } else {
            $dataFormatada = ''; // ou ' / / ' ou 'N/A', como preferir
        }

        $pdf->Cell(0, 5, utf8_decode(' '.$dataFormatada), 0, 1, 'L');
        // $pdf->Cell(0, 5, utf8_decode(' '.substr($cot->responsavel_tecnico,0,100)), 0, 1, 'L');
        // $pdf->SetXY(350, $y);
        // $pdf->SetFont('Arial', 'B', 6);
        // $pdf->Cell($pdf->GetStringWidth('Status atual:') + 1, 5, utf8_decode('Status atual:'), 0, 0, 'L');
        // $pdf->SetFont('Arial', '', 6);
        // $pdf->Cell(0, 5, utf8_decode(' '.$statusatual), 0, 1, 'L');
     
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
        $xnome = $administradora->nome ?? '';           // evita null
        $pdf->Cell(70,5,utf8_decode(substr($xnome,0,58)),0,1,'L');   
        $pdf->SetXY(190,110);
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
        $pdf->SetXY(190,121);
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
        $pdf->SetXY(190,132);
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
        $pdf->SetXY(190,165);
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
        $pdf->SetXY(190,176);
        $pdf->Cell(70,5,utf8_decode('Bairro : '.$pe->bairro),0,1,'L');
    
        $pdf->SetXY(300,176);
        $pdf->Cell(70,5,utf8_decode('Chassi : '.$veiculo->chassi.' Km/Horimetro:'.$ped->km),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(465,176);
        $pdf->SetFont('arial','',5);
        $tipoServicoTexto = 'Tipo Serviço : '.(string) ($tipomanutencao->descricao ?? '');
        $tipoServicoTexto = trim($tipoServicoTexto);
        $tipoServicoLimite = 95;
        while (mb_strlen($tipoServicoTexto, 'UTF-8') > 0 && $pdf->GetStringWidth(utf8_decode($tipoServicoTexto)) > $tipoServicoLimite) {
            $tipoServicoTexto = rtrim(mb_substr($tipoServicoTexto, 0, mb_strlen($tipoServicoTexto, 'UTF-8') - 1, 'UTF-8'));
        }
        if ($tipoServicoTexto !== 'Tipo Serviço : '.(string) ($tipomanutencao->descricao ?? '')) {
            $tipoServicoTexto = rtrim($tipoServicoTexto, " .") . '...';
        }
        $pdf->Cell(95,5,utf8_decode($tipoServicoTexto),0,1,'L');
        $pdf->SetFont('arial','',6);

        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(25,187);
        $pdf->Cell(70,5,utf8_decode('Cidade : '.$nomecidade),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(190,187);
        $pdf->Cell(70,5,utf8_decode('Telefone : '.$pessoa->fone),0,1,'L');

        $pdf->SetXY(300,187);
        $pdf->Cell(70,5,utf8_decode('Aprovado por : '.$usuarioaprovado),0,1,'L');
        $pdf->SetFont('arial','',6);
        $pdf->SetXY(465,187);

        $pdf->Cell(70,5,utf8_decode('Data Aprovação : '.$dataaprovado),0,1,'L');
        

        $pdf->SetXY(25,198);
        $pdf->Cell(70,5,utf8_decode('Email : '.$pessoa->email),0,1,'L');

        $pdf->SetXY(300,198);
        $pdf->Cell(70,5,utf8_decode(substr('Modelo : '.$modelo->descricao. '  Combustível: '.$tipocombustivel->descricao,0,54) ),0,1,'L');

        $pdf->SetXY(465,198);
        $pdf->Cell(70,5,utf8_decode('Data Abertura : '.$dataabertura),0,1,'L');
        $pdf->Cell(0,11,"","B",1,'C');
        $pdf->SetFont('Arial', '', 6);
        $observ = trim((string) ($cot->obs ?? ''));
        $obsx = trim((string) ($obsaprovado ?? ''));

        if ($observ === '') {
            $observ = '-';
        }

        if ($obsx === '') {
            $obsx = '-';
        }

        $alturaLinhaTexto = 8;
        $larguraTexto = 540;

        $pdf->SetXY(25,220);
        $pdf->MultiCell($larguraTexto, $alturaLinhaTexto, utf8_decode('Obs : '.$observ), 0, 'L', false);

        $alturaObs = $this->calcularAlturaBlocoTexto('Obs : '.$observ, 135, $alturaLinhaTexto);
        $yJustificativa = 220 + $alturaObs + 2;

        $pdf->SetXY(25, $yJustificativa);
        $pdf->MultiCell($larguraTexto, $alturaLinhaTexto, utf8_decode('Justificativa : '.$obsx), 0, 'L', false);

        $alturaJustificativa = $this->calcularAlturaBlocoTexto('Justificativa : '.$obsx, 135, $alturaLinhaTexto);

        return $yJustificativa + $alturaJustificativa + 8;
            $pdf->SetXY(25,220);
            $observ = $cot->obs ?? '';                 // evita null
            $observ = mb_substr($observ, 0, 15, 'UTF-8');    // corta com segurança

            $pdf->Cell(70,5,utf8_decode(substr('Obs : '.substr($observ,0,70),0,100)),0,1,'L');
            $pdf->SetFont('arial','',6);
           
            $obsx = !empty($obsaprovado) ? $obsaprovado : "''";
            $pdf->SetFont('Arial', '', 6);
            $pdf->SetXY(300,220);
            $pdf->Cell(70,5,utf8_decode(substr('Justificativa : '.substr($obsx,0,63),0,100)),0,1,'L');
            
           
 
   }
   private function calcularAlturaBlocoTexto($texto, $caracteresPorLinha = 135, $alturaLinha = 8)
   {
        $texto = trim((string) $texto);

        if ($texto === '') {
            return $alturaLinha;
        }

        $linhas = preg_split("/\r\n|\r|\n/", $texto);
        $totalLinhas = 0;

        foreach ($linhas as $linha) {
            $quantidadeCaracteres = mb_strlen($linha, 'UTF-8');
            $totalLinhas += max(1, (int) ceil($quantidadeCaracteres / $caracteresPorLinha));
        }

        return max(1, $totalLinhas) * $alturaLinha;
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
        
        $pdf->SetXY(245,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Tipo peça'),0,1,'L');

        $pdf->SetXY(290,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Garantia'),0,1,'L');

        $pdf->SetXY(340,$alturalinha);       
        $pdf->Cell(70,5,utf8_decode('Marca'),0,1,'L');

        $pdf->SetXY(327,$alturalinha);       
        $pdf->Cell(70,5,utf8_decode('Qtde'),0,1,'R');

        $pdf->SetXY(365,$alturalinha);        
        $pdf->Cell(70,5,utf8_decode('Valor'),0,1,'R');

        $pdf->SetXY(420,$alturalinha);        
        $pdf->Cell(70,5,utf8_decode('Desconto'),0,1,'R');

        $pdf->SetXY(485,$alturalinha);
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

        $pdf->SetXY(290,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Garantia'),0,1,'L');

        $pdf->SetXY(327,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Qtde'),0,1,'R');

        $pdf->SetXY(360,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Valor'),0,1,'R');

        $pdf->SetXY(420,$alturalinha);        
        $pdf->Cell(70,5,utf8_decode('Desconto'),0,1,'R');

        $pdf->SetXY(485,$alturalinha);
        $pdf->Cell(70,5,utf8_decode('Valor Total'),0,1,'R');

        $pdf->ln(1);
        return $alturalinha+15;
    }
}
?>
