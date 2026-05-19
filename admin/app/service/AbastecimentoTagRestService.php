<?php

class AbastecimentoTagRestService
{
    const DATABASE = 'minierp';

    public static function consultarTag($param)
    {
        try
        {
            TTransaction::open(self::DATABASE);

            $uid = trim((string) ($param['uid_tag'] ?? ''));
            if ($uid === '')
            {
                throw new Exception('Informe uid_tag.');
            }

            $systemUnitId = !empty($param['system_unit_id']) ? (int) $param['system_unit_id'] : null;
            $resumo = AbastecimentoTagService::obterResumoTag($uid, $systemUnitId);

            TTransaction::close();

            return $resumo;
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            throw $e;
        }
    }

    public static function registrarAbastecimento($param)
    {
        try
        {
            TTransaction::open(self::DATABASE);

            foreach (['uid_tag', 'data_abastecimento', 'estabelecimento_id', 'tipo_combustivel_id', 'qtde_litros', 'valor_litro'] as $campo)
            {
                if (empty($param[$campo]) && $param[$campo] !== '0')
                {
                    throw new Exception("Campo obrigatório: {$campo}");
                }
            }

            $resultado = AbastecimentoTagService::registrarAbastecimento($param);

            TTransaction::close();

            return $resultado;
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            throw $e;
        }
    }
}
