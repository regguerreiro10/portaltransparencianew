<?php

class TabelaSinapiService
{

    private $baseUrl;
    private $username;
    private $password;
    private $token;
    private $itemsPerPages;
    private $insumosMT05 = '';
   
    public function __construct()
    {
        $this->baseUrl = 'https://api.apisinapi.com.br';
        $this->username = 'np3gestao@gmail.com';
        $this->password = 'zRCOdXpk';
    }

    private function request($endpoint, $method = 'GET', $data = null, $auth = false)
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init($url);

        $headers = [
            'Content-Type: application/json',
        ];

        if ($auth && $this->token) {
            $headers[] = 'authorization: Bearer ' . $this->token;
        }
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $handle = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $handle);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'GET' && $data) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
		//echo $url."\n";
        $response = curl_exec($ch);

        rewind($handle);
        $log = stream_get_contents($handle);
        //  echo 'log curl:<pre>', htmlspecialchars($log),'</pre><br>';
        curl_close($ch);

        return json_decode($response, false);
    }

    public function login()
    {
        $endpoint = '/api/Authentication/login';
        $data = [
            'login' => $this->username,
            'senha' => $this->password,
        ];
        $response = $this->request($endpoint, 'POST', $data);
        if (isset($response->token)) {
            $this->token = $response->token;
        } else {
            throw new Exception('Login failed');
        }
    }

    public function refreshToken($admin = false)
    {
        $endpoint = '/api/Authentication/refresh';
        $data = ['admin' => $admin];
        return $this->request($endpoint, 'POST', $data, true);
    }

    public function getEstados($params = [])
    {
        $endpoint = '/api/Estados';
        return $this->request($endpoint, 'GET', $params, true);
    }

    public function getInsumos($params = [])
    {
        $endpoint = '/api/Insumos';
        return $this->request($endpoint, 'GET', $params, true);
    }

    public function getPosts($params = [])
    {
        $endpoint = '/posts';
        return $this->request($endpoint, 'GET', $params, true);
    }

    public function getTabelas($params = [])
    {
        $endpoint = '/api/Tabelas';
        return $this->request($endpoint, 'GET', $params, true);
    }

    public function getAnos($params = [])
    {
        $endpoint = '/api/Tabelas/anos/select';
        $data=$this->request($endpoint, 'GET', $params, true);
		return $data;
    }

    public function getMeses($params = [])
    {
        $endpoint = '/api/Tabelas/meses/select';
        return $this->request($endpoint, 'GET', $params, true);
    }

    public function getUltimoAno($params = [])
    {
        $data = $this->getAnos($params);
		$values = array_column($data, 'value');
        return  max($values);
    }

    public function getUltimoMes($params = [])
    {
        $data = $this->getMeses($params);
        $values = array_column($data, 'value');
        return  max($values);
    }
}
