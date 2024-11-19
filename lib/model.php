<?php

class Model
{
    private $listOfCurrencies = null;
    private $apiUrl = 'https://api.coinbase.com/v2/';

    private function getApiData(string $api): array|false
    {
        $json = file_get_contents($api);
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

        $listCurrencies = $this->getApiData($this->apiUrl . 'currencies');
        if ($listCurrencies === false) {
            return $listCurrencies;
        }

        $list = [];
        foreach ($listCurrencies['data'] as $data) {
            $list[] = $data['id'];
        }

        $listCrypto = $this->getApiData($this->apiUrl . 'currencies/crypto');
        if ($listCrypto === false) {
            return $listCrypto;
        }

        foreach ($listCrypto['data'] as $data) {
            $list[] = $data['code'];
        }

        $this->listOfCurrencies = $list;
        return $list;
    }

    public function verifyCurrency(string $currency, array $listOfCurrenciesFromController): array
    {
        if ($listOfCurrenciesFromController === false) {
            return [
                'success' => false,
                'error' => 'Error message: Unsupported token pair, empty or invalid .json file'
            ];
        }

        if (!in_array($currency, $listOfCurrenciesFromController)) {
            return [
                'success' => false,
                'error' => 'Error message: Invalid crypto or currency token'
            ];
        }

        return ['success' => true];
    }

    public function getCurrencyPair(string $cryptoGet, string $currencyGet): array
    {
        $api = $this->apiUrl . 'prices/' . $cryptoGet . '-' . $currencyGet . '/spot';
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
