<?php

class CalculoTaxasImpostosService
{
    public static function money($v): float
    {
        return round((float)$v, 2);
    }

    /**
     * Monta contexto a partir da SUA estrutura atual:
     * - $c (Conta)
     * - $mixPorPedido (array)
     * - $taxasImpostos (array com percentuais e impostos)
     */
    public static function montarContextoConta($pedido, $valorProd, $valorServ, $taxaspessoa): array
    {
        $pedidoIdConta = (int) ($pedido->id ?? 0);

        // $totPedido = (float) ($mixPorPedido[$pedidoIdConta]['total'] ?? 0.0);
        // $totProd   = (float) ($mixPorPedido[$pedidoIdConta]['prod']  ?? 0.0);
        // $totServ   = (float) ($mixPorPedido[$pedidoIdConta]['serv']  ?? 0.0);

        //verificar valor_total_proposta se refere a $bruto
        $bruto = $valorProd + $valorServ;

        $porcProduto = ($bruto > 0) ? ((float)$valorProd / $bruto) : 0.0;
        $porcServico = ($bruto > 0) ? ((float)$valorServ / $bruto) : 0.0;

        // totais de imposto (se não vierem já prontos)
        $totalPorcProduto = (float) (
            ($taxaspessoa->ir     ?? 0) +
            ($taxaspessoa->csll   ?? 0) +
            ($taxaspessoa->cofins ?? 0) +
            ($taxaspessoa->pis    ?? 0)
        );

        $totalPorcServico = (float) (
            ($taxaspessoa->ir_servico     ?? 0) +
            ($taxaspessoa->csll_servico   ?? 0) +
            ($taxaspessoa->cofins_servico ?? 0) +
            ($taxaspessoa->pis_servico    ?? 0) +
            ($taxaspessoa->iss_servico    ?? 0)
        );

        return [
            'pedido_id' => $pedidoIdConta,
            'bruto'     => (float) ($bruto ?? 0),

            'porcProduto' => $porcProduto,
            'porcServico' => $porcServico,

            'perc_tx_contrato' => (float) ((TSession::getValue('taxacontrato')) ?? 0),
            'perc_tx_adm'      => (float) ($taxaspessoa->taxaadm                ?? 0),
            'perc_tx_ant'      => (float) ($taxaspessoa->taxaantecipacao        ?? 0),

            'totalPorcProduto' => $totalPorcProduto,
            'totalPorcServico' => $totalPorcServico,

            // (opcional) guarda individuais pra você usar depois no PDF/colunas
            'impostos' => [
                'ir'           => (float) ($taxaspessoa->ir ?? 0),
                'csll'         => (float) ($taxaspessoa->csll ?? 0),
                'cofins'       => (float) ($taxaspessoa->cofins ?? 0),
                'pis'          => (float) ($taxaspessoa->pis ?? 0),
                'ir_servico'   => (float) ($taxaspessoa->ir_servico ?? 0),
                'csll_servico' => (float) ($taxaspessoa->csll_servico ?? 0),
                'cofins_servico'=> (float) ($taxaspessoa->cofins_servico ?? 0),
                'pis_servico'  => (float) ($taxaspessoa->pis_servico ?? 0),
                'iss_servico'  => (float) ($taxaspessoa->iss_servico ?? 0),
            ],
        ];
    }

    /**
     * Calcula e devolve NUMÉRICOS (sem formatar R$/%).
     * Retorna exatamente os campos que você preenche hoje na Conta.
     */
    public static function calcularPorContexto(array $ctx): array
    {
        $bruto = (float) ($ctx['bruto'] ?? 0);

        $porcProduto = (float) ($ctx['porcProduto'] ?? 0);
        $porcServico = (float) ($ctx['porcServico'] ?? 0);

        $perc_tx_contrato = (float) ($ctx['perc_tx_contrato'] ?? 0);
        $valor_txcontrato_fixado = array_key_exists('valor_txcontrato_fixado', $ctx)
            ? self::money((float) ($ctx['valor_txcontrato_fixado'] ?? 0))
            : null;
        $perc_tx_adm      = (float) ($ctx['perc_tx_adm'] ?? 0);
        $perc_tx_ant      = (float) ($ctx['perc_tx_ant'] ?? 0);

        $totalPorcProduto = (float) ($ctx['totalPorcProduto'] ?? 0);
        $totalPorcServico = (float) ($ctx['totalPorcServico'] ?? 0);

        // 0.1 bruto separado
        $bruto_prod = self::money($bruto * $porcProduto);
        $bruto_serv = self::money($bruto - $bruto_prod);

        // 1) tx contrato
        if ($valor_txcontrato_fixado !== null) {
            $valor_txcontrato = self::money(min(max($valor_txcontrato_fixado, 0), $bruto));
        } else {
            $valor_txcontrato = self::money($bruto * ($perc_tx_contrato / 100));
        }
        $base_pos_txcontrato = max(0.0, self::money($bruto - $valor_txcontrato));

        // base por mix
        $base_prod_ctxc = self::money($base_pos_txcontrato * $porcProduto);
        $base_serv_ctxc = self::money($base_pos_txcontrato - $base_prod_ctxc);

        // 2) impostos
        $vl_imp_prod = self::money($base_prod_ctxc * ($totalPorcProduto / 100));
        $vl_imp_serv = self::money($base_serv_ctxc * ($totalPorcServico / 100));

        $liq_prod_pos_imp = self::money($base_prod_ctxc - $vl_imp_prod);
        $liq_serv_pos_imp = self::money($base_serv_ctxc - $vl_imp_serv);

        $total_pos_impostos = self::money($liq_prod_pos_imp + $liq_serv_pos_imp);

        // 3) tx adm/ant
        $valor_txadm = self::money($total_pos_impostos * ($perc_tx_adm / 100));
        $base_pos_adm = self::money($total_pos_impostos - $valor_txadm);

        $valor_txantecipacao = self::money($base_pos_adm * ($perc_tx_ant / 100));
        $valor_final = self::money($base_pos_adm - $valor_txantecipacao);

        return [
            // seus campos atuais
            'valor_txcontrato' => $valor_txcontrato,

            'valor_produto_s_desc_txc' => $bruto_prod,
            'valor_servico_s_desc_txc' => $bruto_serv,
            'base_pos_txcontrato' => $base_pos_txcontrato,

            'valor_produto_c_desc_txc' => $base_prod_ctxc,
            'valor_servico_c_desc_txc' => $base_serv_ctxc,

            'vl_imp_prod' => $vl_imp_prod,
            'vl_imp_serv' => $vl_imp_serv,

            'valor_liqbase_prod_posimp' => $liq_prod_pos_imp,
            'valor_liqbase_serv_posimp' => $liq_serv_pos_imp,
            'valor_txc_imp_produto_servico' => $total_pos_impostos,

            'valor_txadm' => $valor_txadm,
            'valor_txantecipacao' => $valor_txantecipacao,
            'valor_total_liq_tx_conta' => $valor_final,
        ];
    }

    /**
     * Aplica os valores calculados na Conta (sem formatar)
     */
    // public static function aplicarNaConta(Conta $c, array $calc): void
    // {
    //     foreach ($calc as $campo => $valor) {
    //         $c->$campo = $valor;
    //     }
    // }
}
