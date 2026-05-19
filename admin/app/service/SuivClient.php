<?php
namespace app\service;

class SuivClient
{
    private const BASE = 'https://api.suiv.com.br/api/v3';
    private const AUTH = 'JiJHAug2nLqpMb4845UO2O8t16BuGOTctVUg3hmfUsazzvZUDpEN3Bw7WKPoIWyXdqlh_cyzGepmVqMkiO6wIdsb1eqfh-VbfooF'; // troque isso depois por env/ini

    private static function buildCurlOptions(): array
    {
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . self::AUTH,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];

        $caInfo = ini_get('curl.cainfo') ?: ini_get('openssl.cafile');
        if (!empty($caInfo) && is_string($caInfo) && file_exists($caInfo)) {
            $options[CURLOPT_CAINFO] = $caInfo;
        }

        return $options;
    }

    private static function isCertificateLocationError(string $error): bool
    {
        $error = strtolower($error);

        return strpos($error, 'certificate verify locations') !== false
            || strpos($error, 'cacert') !== false
            || strpos($error, 'error setting certificate verify locations') !== false;
    }

    public static function isNotFoundError(\Exception $e): bool
    {
        return stripos($e->getMessage(), 'HTTP 404') !== false;
    }

    public static function isUnavailableError(\Exception $e): bool
    {
        $message = strtolower($e->getMessage());

        return strpos($message, 'http 401') !== false
            || strpos($message, 'http 429') !== false
            || strpos($message, 'no remaining requests') !== false
            || strpos($message, 'remaining requests') !== false
            || strpos($message, 'too many requests') !== false
            || strpos($message, 'erro curl') !== false
            || strpos($message, 'timed out') !== false
            || strpos($message, 'timeout') !== false;
    }

    public static function shouldUseLocalFallback(\Exception $e): bool
    {
        return self::isNotFoundError($e) || self::isUnavailableError($e);
    }

    private static function executeRequest(string $url, bool $allowInsecureFallback = true)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, self::buildCurlOptions());

        $resp = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        if ($resp === false && $allowInsecureFallback && ($errno === 77 || self::isCertificateLocationError($error))) {
            curl_close($ch);

            $ch = curl_init($url);
            $options = self::buildCurlOptions();
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = 0;
            curl_setopt_array($ch, $options);

            $resp = curl_exec($ch);
            $error = curl_error($ch);
            $errno = curl_errno($ch);
        }

        if ($resp === false) {
            curl_close($ch);
            throw new \Exception('Erro CURL: ' . $error);
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [$code, $resp];
    }

    private static function get(string $path, array $query = [])
    {
        $url = self::BASE . $path . (empty($query) ? '' : ('?' . http_build_query($query)));
        [$code, $resp] = self::executeRequest($url);

        if ($code < 200 || $code >= 300) {
            throw new \Exception("HTTP $code: $resp"); // \Exception
        }
        return json_decode($resp, true);
    }

    public static function getVehicleTokenByPlate(string $placa): ?string
    {
        $placa = preg_replace('/[^A-Za-z0-9]/', '', $placa);
        if (empty($placa)) {
            return false;
        }

        try {
            $vi = self::get('/VehicleInfo/byplate', ['plate' => $placa]);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'HTTP 404') !== false) {
                return false;
            }
            throw $e;
        }
        if (empty($vi['suivDataCollection'][0]['versionId']) || empty($vi['yearFab'])) {
            // throw new \Exception("Dados insuficientes retornados para a placa: $placa  Contacte a Equipe de Suporte da NP3 e informe a placa"); // \Exception
            return false;
        }
        $versionId = $vi['suivDataCollection'][0]['versionId'];
        $yearFab   = $vi['yearFab'];

        try {
            $vt = self::get('/VehicleToken', ['versionId' => $versionId, 'year' => $yearFab]);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'HTTP 404') !== false) {
                return false;
            }
            throw $e;
        }
        return isset($vt['token']) ? $vt['token'] : false; // compatível com PHP 7.0
    }

    public static function getSets(string $vehicleToken): array
    {
        $res = self::get('/Sets', ['vehicleToken' => $vehicleToken]);
        return is_array($res) ? $res : [];
    }

    // Removido o "int|string" para suportar PHP 7.x
    public static function getNicknames(string $vehicleToken, $setId): array
    {
        $res = self::get('/Nicknames', ['vehicleToken' => $vehicleToken, 'setId' => $setId]);
        return is_array($res) ? $res : [];
    }

    public static function getParts(string $vehicleToken, $nicknameId): array
    {
        $res = self::get('/Parts', ['vehicleToken' => $vehicleToken, 'nicknameId' => $nicknameId]);
        return is_array($res) ? $res : [];
    }
      /**
     * Consulta tempos de mão de obra (individualoverlapsbynickname)
     * para múltiplas peças (apelidos) de um veículo.
     *
     * @param string       $vehicleToken  Token do veículo (vehicletoken)
     * @param array|string $nicknameIds   IDs dos apelidos das peças (ex: [7688, 7575] ou "7688,7575")
     *
     * @return array
     * @throws \Exception
     */
    public static function getLaborTimesByNicknames(string $vehicleToken, $nicknameIds): array
    {
        // se não vier nada, não chama a API
        if (empty($nicknameIds)) {
            return [];
        }

        // Se veio string "7688,7575", transforma em array
        if (is_string($nicknameIds)) {
            $nicknameIds = explode(',', $nicknameIds);
        } elseif (!is_array($nicknameIds)) {
            // caso venha um único valor escalar (ex: 7688)
            $nicknameIds = [$nicknameIds];
        }

        // Garante que tudo é número e monta o formato "7688,7575"
        $partsParam = implode(',', array_map('intval', $nicknameIds));

        $res = self::get('/individualoverlapsbynickname', [
            'parts'        => $partsParam,
            'vehicletoken' => $vehicleToken,
        ]);

        return is_array($res) ? $res : [];
    }
    public static function convertTimeStringToDecimalHours(string $timeStr): float
    {
        $timeStr = trim($timeStr);

        // Aceita HH:MM ou HH:MM:SS
        if (preg_match('/^(\d+):(\d{2})(?::(\d{2}))?$/', $timeStr, $m)) {
            $hours   = (int) $m[1];
            $minutes = (int) $m[2];
            $seconds = isset($m[3]) ? (int) $m[3] : 0;

            return $hours + ($minutes / 60) + ($seconds / 3600);
        }

        return 0.0;
    }
    public static function convertDecimalHoursToTimeString(float $decimalHours, bool $showSeconds = true): string
    {
        // evita negativo (se quiser permitir, remova isso e trate o sinal)
        if ($decimalHours < 0) {
            $decimalHours = 0;
        }

        // transforma em segundos totais (arredonda pra não dar 59.999...)
        $totalSeconds = (int) round($decimalHours * 3600);

        $hours = intdiv($totalSeconds, 3600);
        $totalSeconds %= 3600;

        $minutes = intdiv($totalSeconds, 60);
        $seconds = $totalSeconds % 60;

        if ($showSeconds) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        // se não mostrar segundos, arredonda minutos a partir dos segundos
        if ($seconds >= 30) {
            $minutes++;
            if ($minutes >= 60) {
                $minutes = 0;
                $hours++;
            }
        }

        return sprintf('%02d:%02d', $hours, $minutes);
    }


}
?>
