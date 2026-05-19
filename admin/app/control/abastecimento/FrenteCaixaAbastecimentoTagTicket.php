<?php

class FrenteCaixaAbastecimentoTagTicket extends TPage
{
    public function onShow($param = null)
    {
        $this->gerar($param);
    }

    public function gerar($param = null)
    {
        try
        {
            if (empty($param['id']))
            {
                throw new Exception('ID do abastecimento nÃ£o informado.');
            }

            TTransaction::open('minierp');

            $html = $this->montarHtml((int) $param['id']);
            $arquivo = 'app/output/ticket_abastecimento_tag.html';

            if (!file_exists($arquivo) || is_writable($arquivo))
            {
                file_put_contents($arquivo, $html);
            }
            else
            {
                throw new Exception('Sem permissÃ£o para gravar: ' . $arquivo);
            }

            TTransaction::close();
            parent::openFile($arquivo);
        }
        catch (Exception $e)
        {
            if (TTransaction::getDatabase())
            {
                TTransaction::rollback();
            }

            new TMessage('error', $e->getMessage());
        }
    }

    private function montarHtml(int $pedidoId): string
    {
        $pedido = new PedidoFrotas($pedidoId);
        $veiculo = !empty($pedido->veiculos_id) ? new Veiculos($pedido->veiculos_id) : null;
        $estabelecimento = !empty($pedido->estabelecimento_id) ? new Pessoa($pedido->estabelecimento_id) : null;
        $usuario = !empty($pedido->system_users_id) ? new SystemUsers($pedido->system_users_id) : null;
        $itens = ItensPedidoFrotas::where('pedido_frotas_id', '=', $pedido->id)->load();

        $linhasItens = '';
        if ($itens)
        {
            foreach ($itens as $item)
            {
                $valorBrutoItem = (float) ($item->valor_unitario ?? 0) * (float) ($item->qtde ?? 0);

                $linhasItens .= sprintf(
                    '<tr><td>%s</td><td style="text-align:right;">%s</td><td style="text-align:right;">%s</td></tr>',
                    $this->text($item->descricao ?? ''),
                    number_format((float) ($item->qtde ?? 0), 3, ',', '.'),
                    'R$ ' . number_format($valorBrutoItem, 2, ',', '.')
                );
            }
        }

        if ($linhasItens === '')
        {
            $linhasItens = '<tr><td colspan="3" style="text-align:center;">Nenhum item encontrado</td></tr>';
        }

        $dataPedido = !empty($pedido->dt_pedido) ? (new DateTime($pedido->dt_pedido))->format('d/m/Y H:i') : '';
        $status = $pedido->estado_pedido_frotas->nome ?? '';
        $veiculoTexto = trim(($veiculo->placa ?? '') . ' - ' . ($veiculo->marca->descricao ?? '') . ' - ' . ($veiculo->modelo->descricao ?? ''));
        $valorBruto = (float) ($pedido->valor_total ?? 0);
        $valorDesconto = (float) ($pedido->valor_desconto_proposta ?? 0);
        $valorLiquido = (float) ($pedido->valor_liquido_proposta ?? $valorBruto);
        $taxaContratual = 0.0;

        $proposta = Propostas::where('pedido_frotas_id', '=', $pedido->id)
            ->where('deleted_at', 'is', null)
            ->first();

        if ($proposta)
        {
            $taxaContratual = (float) ($proposta->desconto_contratual ?? 0);
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Comprovante Interno de Abastecimento TAG</title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; background:#f3f4f6; margin:0; padding:24px; color:#111827; }
    .no-print { text-align:center; margin-bottom:14px; }
    .no-print .btn { display:inline-block; padding:10px 16px; background:#1565c0; color:#fff; text-decoration:none; border-radius:6px; }
    .ticket { width: 360px; max-width:100%; margin:0 auto; background:#fff; border:1px solid #d1d5db; border-radius:10px; padding:18px; box-shadow:0 10px 30px rgba(0,0,0,.08); }
    .title { text-align:center; font-weight:700; font-size:20px; margin-bottom:4px; }
    .subtitle { text-align:center; color:#6b7280; font-size:12px; margin-bottom:16px; }
    .status { display:inline-block; padding:5px 10px; border-radius:999px; background:#198754; color:#fff; font-size:12px; font-weight:700; }
    .section { margin-top:14px; padding-top:12px; border-top:1px dashed #d1d5db; }
    .row { margin-bottom:8px; }
    .label { font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; }
    .value { font-size:14px; font-weight:600; }
    table { width:100%; border-collapse:collapse; margin-top:8px; }
    th, td { font-size:12px; padding:6px 0; border-bottom:1px solid #eceff3; vertical-align:top; }
    th { text-align:left; color:#6b7280; font-weight:700; }
    .total { margin-top:12px; padding-top:12px; border-top:2px solid #111827; display:flex; justify-content:space-between; font-size:16px; font-weight:700; }
    .summary { margin-top:10px; }
    .summary-row { display:flex; justify-content:space-between; gap:12px; padding:4px 0; font-size:13px; }
    .summary-row.muted { color:#6b7280; }
    .summary-row.final { font-size:16px; font-weight:700; color:#111827; border-top:1px solid #eceff3; margin-top:4px; padding-top:10px; }
    .footer { margin-top:16px; font-size:11px; color:#6b7280; text-align:center; }
    @media print {
      body { background:#fff; padding:0; }
      .no-print { display:none; }
      .ticket { width:100%; border:none; border-radius:0; box-shadow:none; margin:0; }
    }
  </style>
</head>
<body>
  <div class="no-print"><a class="btn" href="javascript:FrenteCaixaAbastecimentoTagTicket_printAndBack()">Imprimir ticket</a></div>
  <div class="ticket">
    <div class="title">Comprovante Interno de Abastecimento</div>
    <div class="subtitle">Operacao registrada por TAG para controle interno</div>
    <div style="text-align:center; margin-bottom:10px;"><span class="status">{$this->text($status)}</span></div>

    <div class="row">
      <div class="label">Pedido</div>
      <div class="value">#{$pedido->id}</div>
    </div>
    <div class="row">
      <div class="label">Data/Hora</div>
      <div class="value">{$dataPedido}</div>
    </div>

    <div class="section">
      <div class="row">
        <div class="label">Veiculo</div>
        <div class="value">{$this->text($veiculoTexto)}</div>
      </div>
      <div class="row">
        <div class="label">KM/Hodometro</div>
        <div class="value">{$this->text((string) ($pedido->km ?? ''))}</div>
      </div>
      <div class="row">
        <div class="label">Estabelecimento</div>
        <div class="value">{$this->text($estabelecimento->nome ?? '')}</div>
      </div>
      <div class="row">
        <div class="label">Usuario</div>
        <div class="value">{$this->text($usuario->name ?? '')}</div>
      </div>
    </div>

    <div class="section">
      <div class="label">Itens</div>
      <table>
        <thead>
          <tr>
            <th>Descricao</th>
            <th style="text-align:right;">Qtde</th>
            <th style="text-align:right;">Valor bruto</th>
          </tr>
        </thead>
        <tbody>
          {$linhasItens}
        </tbody>
      </table>
      <div class="summary">
        <div class="summary-row">
          <span>Subtotal abastecimento</span>
          <span>R$ {$this->money($valorBruto)}</span>
        </div>
        <div class="summary-row muted">
          <span>Desconto contratual interno ({$this->money($taxaContratual)}%)</span>
          <span>R$ {$this->money($valorDesconto)}</span>
        </div>
        <div class="summary-row final">
          <span>Total liquido interno</span>
          <span>R$ {$this->money($valorLiquido)}</span>
        </div>
      </div>
    </div>

    <div class="section">
      <div class="row">
        <div class="label">Descricao</div>
        <div class="value">{$this->text($pedido->descricaopedido ?? '')}</div>
      </div>
      <div class="row">
        <div class="label">Observacao</div>
        <div class="value">{$this->text($pedido->obs ?? '')}</div>
      </div>
    </div>

    <div class="section">
      <div class="row">
        <div class="label">Aviso</div>
        <div class="value">Este comprovante e de uso interno e nao substitui cupom fiscal, NFC-e ou qualquer documento fiscal emitido no sistema do estabelecimento.</div>
      </div>
    </div>

    <div class="footer">Comprovante gerado em {$this->text(date('d/m/Y H:i:s'))}</div>
  </div>
</body>
<script>
function FrenteCaixaAbastecimentoTagTicket_goBack() {
  window.location.href = 'index.php?class=FrenteCaixaAbastecimentoTagList&method=onShow';
}

function FrenteCaixaAbastecimentoTagTicket_printAndBack() {
  var returned = false;
  var done = function () {
    if (returned) {
      return;
    }
    returned = true;
    FrenteCaixaAbastecimentoTagTicket_goBack();
  };

  window.onafterprint = done;
  window.print();
  setTimeout(done, 1200);
}
</script>
</html>
HTML;
    }

    private function text(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    private function money($value): string
    {
        return number_format((float) $value, 2, ',', '.');
    }
}
