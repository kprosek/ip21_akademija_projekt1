<?php

class Model
{
    private $listOfCurrencies = null;
    private $apiUrl = 'https://api.coinbase.com/v2/';

    private function getApiData(string $apiEndpoint): array|false
    {
        $json = file_get_contents($this->apiUrl . $apiEndpoint);
        if (empty($json)) {
            return false;
        }

        $dataArray = json_decode($json, true);
        if ($dataArray === null) {
            return false;
        }

        return $dataArray;
    }

    public function getList(): array|false
    {
        if ($this->listOfCurrencies !== null) {
            return $this->listOfCurrencies;
        }

        $listCurrencies = $this->getApiData('currencies');
        if ($listCurrencies === false) {
            return $listCurrencies;
        }

        $listCrypto = $this->getApiData('currencies/crypto');
        if ($listCrypto === false) {
            return $listCrypto;
        }

        $list = [];
        foreach ($listCurrencies['data'] as $data) {
            $list[] = $data['id'];
        }

        foreach ($listCrypto['data'] as $data) {
            $list[] = $data['code'];
        }

        $listOfOrderedTokens =  [];
        foreach ($list as $index => $token) {
            $listOfOrderedTokens[$index + 1] = $token;
        }

        $this->listOfCurrencies = $listOfOrderedTokens;
        return $listOfOrderedTokens;
    }

    public function verifyCurrency(string $currency, array $masterCurrencyList): array
    {
        if ($masterCurrencyList === false) {
            return [
                'success' => false,
                'error' => 'Error message: Unsupported token pair, empty or invalid .json file'
            ];
        }

        if (!in_array($currency, $masterCurrencyList)) {
            return [
                'success' => false,
                'error' => 'Error message: Invalid crypto or currency token'
            ];
        }

        return ['success' => true];
    }

    public function getCurrencyPair(string $cryptoGet, string $currencyGet): array
    {
        $api = 'prices/' . $cryptoGet . '-' . $currencyGet . '/spot';
        $currencyPair = $this->getApiData($api);
        if ($currencyPair === false) {
            return [
                'success' => false,
                'error' => 'Error message: Unsupported token pair, empty or invalid .json file'
            ];
        }

        return $currencyPair;
    }

    public function databaseConnection($path)
    {
        $env = parse_ini_file($path . '.env');

        $host = $env['DB_HOST'];
        $port = $env['DB_PORT'];
        $dbname = $env['DB_DATABASE'];
        $user = 'root';
        $password = $env['DB_PASSWORD'];

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=UTF8";
        $pdo = new PDO($dsn, $user, $password);
        return $pdo;
    }

    public function insertFavouriteTokens($pdo, $tokens)
    {
        $sql = "INSERT INTO favourites (token_name) VALUES (:token_name) ON DUPLICATE KEY UPDATE token_name = :token_name";
        $stmt = $pdo->prepare($sql);

        foreach ($tokens as $token) {
            $stmt->execute([':token_name' => $token]);
        }
    }

    public function displayFavouriteTokens($pdo)
    {
        $data = $pdo->query("SELECT * FROM favourites")->fetchAll();
        $userFavouriteTokens = [];
        foreach ($data as $row) {
            $userFavouriteTokens[] = $row['token_name'];
        }
        return $userFavouriteTokens;
    }

    public function deleteFavouriteTokens($pdo, $tokens)
    {
        $placeholders = implode(',', array_fill(0, count($tokens), '?'));
        $sql = "DELETE FROM favourites WHERE token_name IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($tokens);
    }
}
