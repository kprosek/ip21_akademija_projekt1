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
}
