<?php

class PropostaOrcamento extends TPage
{
    public function gerar($param = null, $pdf = null)
    {
        try {
            if (empty($param['id'])) {
                throw new Exception('ID da proposta não informado.');
            }

            TTransaction::open('minierp');
            $html = $this->montarHtml((int) $param['id']);

            $arquivo = 'app/output/documentocotacao.pdf';
            if (file_exists($arquivo) && !is_writable($arquivo)) {
                throw new Exception('Sem permissão para gravar: ' . $arquivo);
            }

            $dompdf = new \Dompdf\Dompdf([
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            file_put_contents($arquivo, $dompdf->output());

            TTransaction::close();
            parent::openFile($arquivo);
        } catch (Exception $e) {
            if (TTransaction::getDatabase()) {
                TTransaction::rollback();
            }
            new TMessage('error', $e->getMessage());
        }
    }

    private function montarHtml(int $propostaId): string
    {
        $proposta = new Propostas($propostaId);
        $pedido = new PedidoFrotas($proposta->pedido_frotas_id);
        $fornecedor = new Pessoa($proposta->pessoa_id);

        $veiculo = !empty($pedido->veiculos_id) ? new Veiculos($pedido->veiculos_id) : null;
        $modelo = ($veiculo && !empty($veiculo->modelo_id)) ? new Modelo($veiculo->modelo_id) : null;
        $tipoCombustivel = ($veiculo && !empty($veiculo->tipo_combustivel_id)) ? new TipoCombustivel($veiculo->tipo_combustivel_id) : null;
        $tipoManutencao = !empty($pedido->tipo_manutencao_id) ? new TipoManutencao($pedido->tipo_manutencao_id) : null;

        $unit = null;
        $administradora = null;
        $idUnit = TSession::getValue('idunit');
        if (!empty($idUnit)) {
            $unit = new SystemUnit($idUnit);
            if (!empty($unit->entidade_id)) {
                $entidade = new Entidade($unit->entidade_id);
                if (!empty($entidade->administradora_id)) {
                    $administradora = new Administradora($entidade->administradora_id);
                }
            }
        }

        $endFornecedor = PessoaEndereco::where('pessoa_id', '=', $fornecedor->id)
            ->where('principal', '=', 'T')
            ->first();

        $itensProdutos = ItensPropostas::where('propostas_id', '=', $proposta->id)
            ->where('tipo', '=', 1)
            ->where('deleted_at', 'is', null)
            ->load();

        $itensServicos = ItensPropostas::where('propostas_id', '=', $proposta->id)
            ->where('tipo', '=', 2)
            ->where('deleted_at', 'is', null)
            ->load();

        $totaisProdutos = $this->montarLinhasItens($itensProdutos, true);
        $totaisServicos = $this->montarLinhasItens($itensServicos, false);

        $totalGeralBruto = $totaisProdutos['bruto'] + $totaisServicos['bruto'];
        $totalGeralDesc = $totaisProdutos['desc'] + $totaisServicos['desc'];
        $totalGeralLiq = $totaisProdutos['liq'] + $totaisServicos['liq'];

        $descontoContratual = $this->toFloat($proposta->desconto_contratual ?? 0);
        if ($descontoContratual <= 0) {
            $descontoContratual = $this->toFloat($pedido->desconto_contratual ?? 0);
        }
        if ($descontoContratual <= 0 && $totalGeralBruto > 0 && $totalGeralDesc > 0) {
            $descontoContratual = ($totalGeralDesc / $totalGeralBruto) * 100;
        }

        $estadoNome = '';
        if (!empty($proposta->estado_pedido_frotas_id)) {
            $estado = new EstadoPedidoFrotas($proposta->estado_pedido_frotas_id);
            $estadoNome = $this->text($estado->nome ?? '');
        }

        $retiradoPor = '';
        if (!empty($proposta->motorista_retirada_id)) {
            $motorista = new Pessoa($proposta->motorista_retirada_id);
            $retiradoPor = $this->text($motorista->nome ?? '');
        }

        $aprovadoPor = '';
        $dataAprovacao = '';
        $histAprov = PropostasHistorico::where('propostas_id', '=', $proposta->id)
            ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::APROVADO)
            ->orderBy('data_historico', 'desc')
            ->first();
        if ($histAprov) {
            if (!empty($histAprov->aprovador_frotas_id)) {
                $aprovador = new AprovadorFrotas($histAprov->aprovador_frotas_id);
                if (!empty($aprovador->system_users_id)) {
                    $usuario = new SystemUsers($aprovador->system_users_id);
                    $aprovadoPor = $this->text($usuario->name ?? '');
                }
            }
            if (!empty($histAprov->data_historico)) {
                $dataAprovacao = (new DateTime($histAprov->data_historico))->format('d/m/Y H:i');
            }
        }

        $justificativa = $this->text($histAprov->obs ?? '');
        if ($justificativa === '') {
            $justificativa = $this->text($pedido->obs ?? '');
        }

        $abertoPor = '';
        $dataAbertura = '';
        $histAber = PedidoFrotasHistorico::where('pedido_frotas_id', '=', $pedido->id)
            ->where('estado_pedido_frotas_id', '=', EstadoPedidoFrotas::PENDENTE)
            ->orderBy('data_operacao', 'desc')
            ->first();
        if ($histAber) {
            if (!empty($histAber->aprovador_frotas_id)) {
                $aprovadorAbertura = new AprovadorFrotas($histAber->aprovador_frotas_id);
                if (!empty($aprovadorAbertura->system_users_id)) {
                    $usuarioAbertura = new SystemUsers($aprovadorAbertura->system_users_id);
                    $abertoPor = $this->text($usuarioAbertura->name ?? '');
                }
            }
            if (!empty($histAber->data_operacao)) {
                $dataAbertura = (new DateTime($histAber->data_operacao))->format('d/m/Y H:i');
            }
        }

        $cidadeFornecedor = '';
        if (!empty($endFornecedor->cidade_id)) {
            $cid = new Cidade($endFornecedor->cidade_id);
            $cidadeFornecedor = $this->text($cid->nome ?? '');
        }

        $responsavelTecnico = $this->text($proposta->responsavel_tecnico ?? '');
        $finalizadoEm = '';
        if (!empty($pedido->dt_finalizacao)) {
            $finalizadoEm = (new DateTime($pedido->dt_finalizacao))->format('d/m/Y');
        }
        $logoSrc = realpath('app/images/logo.png');
        $logoDataUri = null;
        if ($logoSrc && file_exists($logoSrc)) {
            $logoBinary = @file_get_contents($logoSrc);
            if ($logoBinary !== false) {
                $logoDataUri = 'data:image/png;base64,' . base64_encode($logoBinary);
            }
        }
        $logoTag = $logoDataUri
            ? "<img src='" . $this->e($logoDataUri) . "' alt='logo' style='width:34px;height:34px;display:block;'>"
            : "<div style='font-weight:700;font-size:20px;'>NP3 Benefícios</div>";

        return "<!doctype html>
<html lang='pt-BR'>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Proposta {$proposta->id}</title>
<style>
body{margin:0;font-family:DejaVu Sans,Arial,Helvetica,sans-serif;font-size:10px;color:#1b2733;background:#ffffff}
.wrap{width:100%;margin:0;padding:0}
.no-print{display:none}
.report{width:100%;border:1px solid #d8dee4;background:#ffffff}
.top{width:100%;padding:1px 3px 1px 3px;border-bottom:1px solid #e7ecf1}
.top-table{width:100%;border-collapse:collapse}
.top-table td{vertical-align:top}
.logo-cell{width:8%;vertical-align:middle;padding:0 2px 0 0}
.meta-cell{width:92%;vertical-align:middle}
.logo img{width:34px;height:34px}
.meta{text-align:right}
.chip{display:inline-block;border:1px solid #d8dee4;background:#ffffff;padding:4px 7px;margin:0 0 4px 4px;font-size:10px}
.grid-table{width:100%;border-collapse:separate;border-spacing:0;padding:4px 3px}
.grid-table td{vertical-align:top;padding:0 3px 3px 0}
.grid-table tr td:last-child{padding-right:0}
.card{min-height:40px;border:1px solid #e7ecf1;background:#fff;padding:4px 6px;font-size:10px;line-height:1.2}
.card b{display:block;font-size:9px;color:#5f6f7f;margin-bottom:1px}
.section{padding:0 3px 6px 3px}
.title{margin:3px 0 2px 0;padding:5px 7px;background:#f1f4f7;border:1px solid #e7ecf1;font-weight:700;line-height:1.15}
.tbl{width:100%;border-collapse:collapse}
.tbl th,.tbl td{padding:3px 6px;vertical-align:top;border:1px solid #e7ecf1;line-height:1.15}
.tbl th{font-weight:700;color:#2d3b48;background:#fbfcfd;text-align:left;font-size:10px}
.compact td{padding:2px 5px;line-height:1.05}
.micro td{padding:1px 4px;line-height:1.0;font-size:9px}
.inline-grid{width:100%;border-collapse:separate;border-spacing:0 3px;margin:0}
.inline-grid td{width:50%;vertical-align:top;padding:0 3px 0 0;border:none}
.inline-grid td:last-child{padding-right:0;padding-left:3px}
.inline-card{border:1px solid #e7ecf1;background:#fff}
.inline-title{padding:5px 7px;background:#f1f4f7;border-bottom:1px solid #e7ecf1;font-weight:700;font-size:10px;line-height:1.1}
.inline-table{width:100%;border-collapse:collapse;font-size:9px;line-height:1.05}
.inline-table td{padding:3px 6px;border-top:1px solid #edf1f5;vertical-align:top}
.inline-table tr:first-child td{border-top:none}
.inline-label{font-weight:700;color:#2d3b48}
.item-table th,.item-table td{padding:2px 5px;line-height:1.05}
.item-table .total-row td{padding:3px 8px 3px 5px;text-align:right;border-top:1px solid #d8dee4;background:#fbfcfd}
.summary-line{width:100%;border-collapse:collapse}
.summary-line td{border:none;padding:0;line-height:13px;height:13px}
.summary-line .label{font-weight:700;text-align:left}
.summary-line .value{text-align:right;white-space:nowrap}
.legend{margin-top:6px;font-size:9px;color:#5f6f7f}
.desc{word-wrap:break-word;word-break:break-word}
.num{text-align:right;white-space:nowrap}
.box{border:1px solid #e7ecf1;padding:0;background:#fff;font-size:11px}
.box.desc{padding:4px 6px}
.total-box{border:1px solid #d8dee4}
.total-box .tbl th{background:#f1f4f7}
.accent{color:#c00000;font-weight:700}
.item-table thead{display:table-header-group}
.item-table tr{page-break-inside:avoid}
.total-row td{font-weight:700;background:#fbfcfd}
</style>
</head>
<body>
<div class='wrap'>
  <div class='report'>
    <div class='top'>
      <table class='top-table'>
        <tr>
          <td class='logo-cell'>{$logoTag}</td>
          <td class='meta meta-cell'>
            <div class='chip'><strong>Pedido:</strong> {$this->e((string)$pedido->id)}</div>
            <div class='chip'><strong>Proposta:</strong> {$this->e((string)$proposta->id)}</div>
            <div class='chip'><strong>Status:</strong> {$this->e($estadoNome !== '' ? $estadoNome : $this->text($proposta->status ?? ''))}</div>
            <div class='chip'><strong>Data:</strong> {$this->e(date('d/m/Y'))}</div>
          </td>
        </tr>
      </table>
    </div>

    <table class='grid-table'>
      <tr>
        <td style='width:26%;'><div class='card'><b>Placa / Identificação</b>{$this->e($this->text($veiculo->placa ?? ''))}</div></td>
        <td style='width:42%;'><div class='card'><b>Unidade / Departamento</b>{$this->e($this->text($unit->name ?? ''))}</div></td>
        <td style='width:32%;'><div class='card'><b>Retirado por</b>{$this->e($retiradoPor)}</div></td>
      </tr>
      <tr>
        <td style='width:34%;'><div class='card'><b>Pedido aberto por</b>{$this->e($abertoPor)}<br><span style='color:#5f6f7f'>Data: {$this->e($dataAbertura)}</span></div></td>
        <td style='width:33%;'><div class='card'><b>Aprovado por</b>{$this->e($aprovadoPor)}<br><span style='color:#5f6f7f'>Data: {$this->e($dataAprovacao)}</span></div></td>
        <td style='width:33%;'><div class='card'><b>Responsável técnico</b>{$this->e($responsavelTecnico)}<br><span style='color:#5f6f7f'>Finalizado em: {$this->e($finalizadoEm)}</span></div></td>
      </tr>
    </table>

    <div class='section'>
      <table class='inline-grid'>
        <tr>
          <td>
            <div class='inline-card'>
              <div class='inline-title'>Dados da gerenciadora</div>
              <table class='inline-table'>
                <tr>
                  <td width='58%'>{$this->e($this->text($administradora->nome ?? ''))}</td>
                  <td width='42%'><span class='inline-label'>CNPJ:</span> {$this->e($this->text($administradora->cnpj ?? ''))}</td>
                </tr>
                <tr>
                  <td width='58%'><span class='inline-label'>Endereço:</span> {$this->e($this->text(($administradora->rua ?? '').' Nº '.($administradora->numero ?? '')))}</td>
                  <td width='42%'><span class='inline-label'>Bairro:</span> {$this->e($this->text($administradora->bairro ?? ''))}</td>
                </tr>
                <tr>
                  <td width='58%'><span class='inline-label'>Cidade:</span> {$this->e($this->text($administradora->cidade ?? ''))}</td>
                  <td width='42%'><span class='inline-label'>Telefone:</span> {$this->e($this->text($administradora->telefone01 ?? ''))}</td>
                </tr>
                <tr>
                  <td colspan='2'><span class='inline-label'>Email:</span> {$this->e($this->text($administradora->email ?? ''))}</td>
                </tr>
              </table>
            </div>
          </td>
          <td>
            <div class='inline-card'>
              <div class='inline-title'>Dados do cliente solicitante</div>
              <table class='inline-table'>
                <tr>
                  <td width='58%'>{$this->e($this->text($unit->name ?? ''))}</td>
                  <td width='42%'><span class='inline-label'>CNPJ:</span> {$this->e($this->text($unit->cnpj ?? ''))}</td>
                </tr>
                <tr>
                  <td width='58%'><span class='inline-label'>Endereço:</span> {$this->e($this->text(($unit->rua ?? '').' Nº '.($unit->numero ?? '')))}</td>
                  <td width='42%'><span class='inline-label'>Bairro:</span> {$this->e($this->text($unit->bairro ?? ''))}</td>
                </tr>
                <tr>
                  <td width='58%'><span class='inline-label'>Cidade:</span> {$this->e($this->text($unit->cidade ?? ''))}</td>
                  <td width='42%'><span class='inline-label'>Telefone:</span> {$this->e($this->text($unit->telefone01 ?? ''))}</td>
                </tr>
                <tr>
                  <td colspan='2'><span class='inline-label'>Email:</span> {$this->e($this->text($unit->email ?? ''))}</td>
                </tr>
              </table>
            </div>
          </td>
        </tr>
      </table>

      <table class='inline-grid'>
        <tr>
          <td>
            <div class='inline-card'>
              <div class='inline-title'>Dados do estabelecimento</div>
              <table class='inline-table'>
                <tr>
                  <td width='58%'>{$this->e($this->text($fornecedor->nome ?? ''))}</td>
                  <td width='42%'><span class='inline-label'>CNPJ:</span> {$this->e($this->text($fornecedor->documento ?? ''))}</td>
                </tr>
                <tr>
                  <td width='58%'><span class='inline-label'>Endereço:</span> {$this->e($this->text((($endFornecedor->rua ?? '').' Nº '.($endFornecedor->numero ?? ''))))}</td>
                  <td width='42%'><span class='inline-label'>Bairro:</span> {$this->e($this->text($endFornecedor->bairro ?? ''))}</td>
                </tr>
                <tr>
                  <td width='58%'><span class='inline-label'>Cidade:</span> {$this->e($cidadeFornecedor)}</td>
                  <td width='42%'><span class='inline-label'>Telefone:</span> {$this->e($this->text($fornecedor->fone ?? ''))}</td>
                </tr>
                <tr>
                  <td colspan='2'><span class='inline-label'>Email:</span> {$this->e($this->text($fornecedor->email ?? ''))}</td>
                </tr>
              </table>
            </div>
          </td>
          <td>
            <div class='inline-card'>
              <div class='inline-title'>Dados do orçamento</div>
              <table class='inline-table'>
                <tr>
                  <td width='62%'><span class='inline-label'>Título manutenção:</span> {$this->e($this->text($pedido->descricaopedido ?? ''))}</td>
                  <td width='38%'><span class='inline-label'>Ano Fab.:</span> {$this->e($this->text($veiculo->anof ?? ''))} <span class='inline-label'>Ano Mod.:</span> {$this->e($this->text($veiculo->anom ?? ''))}</td>
                </tr>
                <tr>
                  <td><span class='inline-label'>Chassi:</span> {$this->e($this->text($veiculo->chassi ?? ''))} <span class='inline-label'>Km/Horímetro:</span> {$this->e($this->text($pedido->km ?? ''))}</td>
                  <td><span class='inline-label'>Tipo Serviço:</span> {$this->e($this->text($tipoManutencao->descricao ?? ''))}</td>
                </tr>
                <tr>
                  <td><span class='inline-label'>Aprovado por:</span> {$this->e($aprovadoPor)}</td>
                  <td><span class='inline-label'>Data Aprovação:</span> {$this->e($dataAprovacao)}</td>
                </tr>
                <tr>
                  <td><span class='inline-label'>Modelo:</span> {$this->e($this->text($modelo->descricao ?? ''))} <span class='inline-label'>Combustível:</span> {$this->e($this->text($tipoCombustivel->descricao ?? ''))}</td>
                  <td><span class='inline-label'>Data Abertura:</span> {$this->e($dataAbertura)}</td>
                </tr>
              </table>
            </div>
          </td>
        </tr>
      </table>

      <div class='title'>Justificativa</div>
      <div class='box desc'>{$this->e($justificativa)}</div>

      <div class='title'>Produto(s)</div>
      <table class='tbl item-table'>
        <thead><tr>
          <th width='31%'>Descrição</th><th width='17%'>Tipo peça / Marca</th><th width='15%'>Garantia</th><th width='4%' class='num'>Qtd</th>
          <th width='10%' class='num'>V. Unit.</th><th width='8%' class='num'>Sub-total</th><th width='6%' class='num'>Desconto</th><th width='8%' class='num'>V. Total</th><th width='1%' class='num'>Sit.</th>
        </tr></thead>
        <tbody>
        {$totaisProdutos['rows']}
        <tr class='total-row'><td colspan='9' class='num'>Valor total dos Produtos {$this->moeda($totaisProdutos['liq'])}</td></tr>
        </tbody>
      </table>

      <div class='title'>Serviço(s)</div>
      <table class='tbl item-table'>
        <thead><tr>
          <th width='48%'>Descrição</th><th width='15%'>Garantia</th><th width='4%' class='num'>Qtd</th>
          <th width='10%' class='num'>V. Unit.</th><th width='8%' class='num'>Sub-total</th><th width='6%' class='num'>Desconto</th><th width='8%' class='num'>V. Total</th><th width='1%' class='num'>Sit.</th>
        </tr></thead>
        <tbody>
        {$totaisServicos['rows']}
        <tr class='total-row'><td colspan='8' class='num'>Valor total dos Serviços {$this->moeda($totaisServicos['liq'])}</td></tr>
        </tbody>
      </table>

      <div class='title'>Fechamento da proposta</div>
      <div class='total-box'>
        <table class='tbl'>
          <tr>
            <td>
              <table class='summary-line'><tr><td class='label'>Serviços sem descontos</td><td class='value'>{$this->moeda($totaisServicos['bruto'])}</td></tr></table>
            </td>
            <td>
              <table class='summary-line'><tr><td class='label'>Produtos sem descontos</td><td class='value'>{$this->moeda($totaisProdutos['bruto'])}</td></tr></table>
            </td>
            <td>
              <table class='summary-line'><tr><td class='label'>Total geral sem descontos</td><td class='value'>{$this->moeda($totalGeralBruto)}</td></tr></table>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
              <table class='summary-line'><tr><td class='label'>Desconto contratual</td><td class='value'>{$this->pct($descontoContratual)}</td></tr></table>
            </td>
            <td>
              <table class='summary-line'><tr><td class='label accent'>Total do desconto</td><td class='value accent'>{$this->moeda($totalGeralDesc)}</td></tr></table>
            </td>
          </tr>
          <tr>
            <td>
              <table class='summary-line'><tr><td class='label'>Serviços com desconto</td><td class='value'>{$this->moeda($totaisServicos['liq'])}</td></tr></table>
            </td>
            <td>
              <table class='summary-line'><tr><td class='label'>Produtos com desconto</td><td class='value'>{$this->moeda($totaisProdutos['liq'])}</td></tr></table>
            </td>
            <td>
              <table class='summary-line'><tr><td class='label'>Total geral com desconto</td><td class='value'>{$this->moeda($totalGeralLiq)}</td></tr></table>
            </td>
          </tr>
        </table>
      </div>
      <div class='legend'>Legenda: `P` Pré-aprovado; `A` Aprovado; `R` Reprovado</div>
    </div>
  </div>
</div>
</body>
</html>";
    }

    private function montarLinhasItens($itens, bool $mostrarTipoMarca = true): array
    {
        $rows = '';
        $bruto = 0.0;
        $desc = 0.0;
        $liq = 0.0;

        if ($itens) {
            foreach ($itens as $item) {
                $nomeProduto = $this->text($item->produto->nome ?? '');
                $descricaoItem = $this->text($item->descricao ?? '');
                $nome = $nomeProduto !== '' ? $nomeProduto : $descricaoItem;
                $marca = '';
                if ($mostrarTipoMarca) {
                    $tipoItem = (int) ($item->tipo ?? 0);
                    $tipoPeca = '';
                    if ($tipoItem === 1 && !empty($item->tipo_pecas_id)) {
                        try {
                            $tipoPecaObj = $item->get_tipo_pecas();
                            $tipoPeca = $this->text($tipoPecaObj->descricao ?? '');
                        } catch (Exception $e) {
                            $tipoPeca = '';
                        }
                    }

                    $marcaBase = $this->text($item->marca_modelo ?? '');
                    if ($marcaBase === '') {
                        $marcaBase = $this->text($item->fabricante ?? '');
                    }
                    $marca = trim($tipoPeca . ($marcaBase !== '' ? ' | ' . $marcaBase : ''), " |");
                }
                $garantia = $this->garantiaDoItem($item);
                $situacao = $this->siglaStatusItem($item->estado_pedido_frotas_id ?? null);

                $qtde = $this->toFloat($item->qtde ?? 0);
                $valor = $this->toFloat($item->valor ?? 0);
                $desconto = $this->toFloat($item->perc_desconto ?? 0);   // valor do desconto
                $valorTotal = $this->toFloat($item->valor_total ?? 0);   // valor líquido já salvo
                $subtotal = $valorTotal + $desconto; // bruto a partir dos valores persistidos

                $bruto += $subtotal;
                $desc += $desconto;
                $liq += $valorTotal;

                $rows .= '<tr>'
                    . '<td class="desc">' . $this->e($nome) . '</td>';

                if ($mostrarTipoMarca) {
                    $rows .= '<td>' . $this->e($marca !== '' ? $marca : '-') . '</td>';
                }

                $rows .= '<td>' . $this->e($garantia) . '</td>'
                    . '<td class="num">' . number_format($qtde, 0, ',', '.') . '</td>'
                    . '<td class="num">' . $this->moeda($valor) . '</td>'
                    . '<td class="num">' . $this->moeda($subtotal) . '</td>'
                    . '<td class="num">' . $this->moeda($desconto) . '</td>'
                    . '<td class="num">' . $this->moeda($valorTotal) . '</td>'
                    . '<td class="num">' . $this->e($situacao) . '</td>'
                    . '</tr>';
            }
        }

        if ($rows === '') {
            $rows = '<tr><td colspan="' . ($mostrarTipoMarca ? '9' : '8') . '" class="desc">Sem itens</td></tr>';
        }

        return ['rows' => $rows, 'bruto' => $bruto, 'desc' => $desc, 'liq' => $liq];
    }

    private function garantia($dias, $km): string
    {
        if (!empty($dias) || !empty($km)) {
            $garantia = '';

            if (!empty($dias)) {
                $garantia .= (int) $dias . ' Dias ';
            }

            if (!empty($km)) {
                $garantia .= (int) $km . ' Km';
            }

            return trim($garantia);
        }

        return '';
    }

    private function garantiaDoItem($item): string
    {
        $dias = $item->diasdegarantia ?? null;
        $km = $item->qtdekmgarantia ?? null;

        // Fallback: alguns itens herdam garantia do item do pedido.
        if ((empty($dias) && empty($km)) && !empty($item->itens_pedido_frotas_id)) {
            try {
                $itemPedido = new ItensPedidoFrotas((int) $item->itens_pedido_frotas_id);
                $dias = $itemPedido->diasdegarantia ?? $dias;
                $km = $itemPedido->qtdekmgarantia ?? $km;
            } catch (Exception $e) {
                // mantém valores atuais
            }
        }

        return $this->garantia($dias, $km);
    }

    private function siglaStatusItem($estadoId): string
    {
        if (empty($estadoId)) {
            return '';
        }

        try {
            $estado = new EstadoPedidoFrotas((int) $estadoId);
            $nome = strtoupper($this->text($estado->nome ?? ''));
            if ($nome === '') {
                return '';
            }

            if (strpos($nome, 'PR') === 0) {
                return 'P';
            }
            if (strpos($nome, 'AP') === 0) {
                return 'A';
            }
            if (strpos($nome, 'RE') === 0) {
                return 'R';
            }

            return mb_substr($nome, 0, 1, 'UTF-8');
        } catch (Exception $e) {
            return '';
        }
    }

    private function moeda($valor): string
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }

    private function pct($valor): string
    {
        return number_format((float) $valor, 2, ',', '.') . '%';
    }

    private function toFloat($value): float
    {
        if ($value === null) {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $v = trim((string) $value);
        if ($v === '') {
            return 0.0;
        }

        $v = str_replace(['R$', ' ', '%'], '', $v);

        if (strpos($v, ',') !== false && strpos($v, '.') !== false) {
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
        } elseif (strpos($v, ',') !== false) {
            $v = str_replace(',', '.', $v);
        }

        return is_numeric($v) ? (float) $v : 0.0;
    }

    private function e(string $v): string
    {
        return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function text($value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $value = (string) $value;
            } elseif (isset($value->nome)) {
                $value = $value->nome;
            } elseif (isset($value->descricao)) {
                $value = $value->descricao;
            } elseif (isset($value->sigla)) {
                $value = $value->sigla;
            } else {
                return '';
            }
        }

        $text = (string) $value;
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252,ISO-8859-1,UTF-8');
        }

        // Tenta múltiplas conversões e fica com a menos "garbled".
        $best = $text;
        $bestScore = $this->mojibakeScore($best);

        for ($i = 0; $i < 4; $i++) {
            $candidates = [];
            $candidates[] = $best;

            $c1 = @iconv('Windows-1252', 'UTF-8//IGNORE', $best);
            if ($c1 !== false && $c1 !== '') $candidates[] = $c1;

            $c2 = @iconv('ISO-8859-1', 'UTF-8//IGNORE', $best);
            if ($c2 !== false && $c2 !== '') $candidates[] = $c2;

            $c3 = @mb_convert_encoding($best, 'UTF-8', 'Windows-1252');
            if ($c3 !== false && $c3 !== '') $candidates[] = $c3;

            $c4 = @mb_convert_encoding($best, 'UTF-8', 'ISO-8859-1');
            if ($c4 !== false && $c4 !== '') $candidates[] = $c4;

            $improved = false;
            foreach ($candidates as $cand) {
                $score = $this->mojibakeScore($cand);
                if ($score < $bestScore) {
                    $best = $cand;
                    $bestScore = $score;
                    $improved = true;
                }
            }

            if (!$improved || $bestScore === 0) {
                break;
            }
        }

        return trim($best);
    }

    private function mojibakeScore(string $text): int
    {
        $score = 0;
        $patterns = [
            '/Ã/u',
            '/Â/u',
            '/â/u',
            '/�/u',
            '/\x{FFFD}/u',
        ];

        foreach ($patterns as $p) {
            if (preg_match_all($p, $text, $m)) {
                $score += count($m[0]);
            }
        }

        return $score;
    }
}
