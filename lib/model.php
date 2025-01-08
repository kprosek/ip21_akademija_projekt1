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
        asort($list);

        foreach ($listCrypto['data'] as $data) {
            $list[] = $data['code'];
        }
        asort($list);

        $listOfOrderedTokens =  [];
        $currentIndex = 1;
        foreach ($list as $index => $token) {
            $listOfOrderedTokens[$currentIndex] = $token;
            $currentIndex += 1;
        }

        $this->listOfCurrencies = $listOfOrderedTokens;
        return $listOfOrderedTokens;
    }

    public function verifyCurrency(string $currency, array $masterCurrencyList): array
    {
        if ($masterCurrencyList === false) {
            return [
                'success' => false,
                'error' => 'Error: Unsupported token pair, empty or invalid .json file'
            ];
        }

        if (!in_array($currency, $masterCurrencyList)) {
            return [
                'success' => false,
                'error' => 'Error: Invalid crypto or currency token'
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
                'error' => 'Error: Unsupported token pair, empty or invalid .json file'
            ];
        }

        return [
            'success' => true,
            'currency pair' => $currencyPair
        ];
    }

    public function databaseConnection(string $path)
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

    public function insertFavouriteTokens(PDO $pdo, string $userId, array $tokens)
    {
        $stmt = $pdo->prepare("INSERT INTO favourites (user_id, token_name) VALUES (:user_id, :token_name) ON DUPLICATE KEY UPDATE token_name = :token_name");

        foreach ($tokens as $token) {
            $stmt->execute([':user_id' => $userId, ':token_name' => $token]);
        }
    }

    public function displayFavouriteTokens(PDO $pdo, ?string $userId)
    {
        $stmt = $pdo->prepare("SELECT * FROM favourites WHERE user_id = :id");
        $stmt->execute(['id' => $userId]);
        $result = $stmt->fetchAll();

        if ($result === false) {
            return [];
        }

        if ($result !== false) {
            $userFavouriteTokens = [];
            foreach ($result as $row) {
                $userFavouriteTokens[] = $row['token_name'];
            }
            return $userFavouriteTokens;
        }
    }

    public function deleteFavouriteTokens(PDO $pdo, string $userId, array $token)
    {
        $deleteToken = implode('', $token);
        $stmt = $pdo->prepare("DELETE FROM favourites WHERE token_name = :token AND user_id = :id");
        $stmt->execute(['id' => $userId, 'token' => $deleteToken]);
    }

    public function isTokenFavourite(string $token, array $favourites)
    {
        if (!in_array($token, $favourites)) {
            return false;
        };

        return true;
    }

    public function validateRegisterUser(PDO $pdo, string $username)
    {
        $registeredEmails = $pdo->query("SELECT mail FROM users")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($registeredEmails as $email) {

            if ($username === $email['mail']) {
                return [
                    'mail' => false,
                    'error' => 'Error: Invalid User credentials'
                ];
            } else {
                return [
                    'mail' => true,
                ];
            }
        }
    }

    public function addNewUser(PDO $pdo, string $username, string $password)
    {
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (mail, password) VALUES (:username, :password)");
        $stmt->execute([':username' => $username, ':password' => $passwordHashed]);
        return [
            'credentials' => true,
            'message' => 'Created New User!'
        ];
    }

    public function authenticateLoginUser(PDO $pdo, string $username, string $password)
    {
        $allUsers = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($allUsers as $email) {
            if ($username === $email['mail']) {
                if (password_verify($password, $email['password'])) {
                    return $email['id'];
                }
            }
        }

        return false;
    }

    public function ipCount(PDO $pdo, string $ipAddress)
    {
        $allIpAddresses = $pdo->query("SELECT client_ip_address FROM ip_count")->fetchAll();

        if ($allIpAddresses === []) {
            $stmt = $pdo->prepare("INSERT INTO ip_count (client_ip_address, count) VALUES (:ip_address, :count)");
            $stmt->execute(['ip_address' => $ipAddress, 'count' => 1]);
            return true;
        }

        $ipInDatabase = false;
        foreach ($allIpAddresses as $address) {
            if (in_array($ipAddress, $address)) {
                $ipInDatabase = true;
            }
        }

        if ($ipInDatabase === false) {
            $stmt = $pdo->prepare("INSERT INTO ip_count (client_ip_address, count) VALUES (:ip_address, :count)");
            $stmt->execute(['ip_address' => $ipAddress, 'count' => 1]);
            return true;
        }

        if ($ipInDatabase == true) {
            $stmt = $pdo->prepare("SELECT timestamp, count FROM ip_count WHERE client_ip_address = :ip");
            $stmt->execute(['ip' => $ipAddress]);
            $result = $stmt->fetch();

            if ($result['count'] <= 3) {
                $newCount = $result['count'] + 1;

                $stmt = $pdo->prepare("UPDATE ip_count SET timestamp = CURRENT_TIMESTAMP , count = :count WHERE client_ip_address = :ip");
                $stmt->execute(['count' => $newCount, 'ip' => $ipAddress]);
                return true;
            }

            if ($result['count'] > 3) {

                $currentTime = new DateTime();
                $lastTimeStamp = new DateTime($result['timestamp']);
                $timeDifference = $currentTime->getTimestamp() - $lastTimeStamp->getTimestamp();

                if ($timeDifference < 60) {
                    return false;
                }

                if ($timeDifference > 60) {
                    $stmt = $pdo->prepare("UPDATE ip_count SET timestamp = CURRENT_TIMESTAMP , count = :count WHERE client_ip_address = :ip");
                    $stmt->execute(['count' => 1, 'ip' => $ipAddress]);
                    return true;
                }
            }
        }
    }

    public function removeIpCount(PDO $pdo, string $ipAddress)
    {
        $stmt = $pdo->prepare("UPDATE ip_count SET timestamp = CURRENT_TIMESTAMP , count = :count WHERE client_ip_address = :ip");
        $stmt->execute(['count' => 1, 'ip' => $ipAddress]);
    }
}
