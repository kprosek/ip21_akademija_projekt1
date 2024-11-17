<?php

class Model
{
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

    private $listOfCurrencies = null;

    public function getList(): array|false
    {
        if ($this->listOfCurrencies !== null) {
            return $this->listOfCurrencies;
        }

        $apiCurrencies = 'https://api.coinbase.com/v2/currencies';
        $listCurrencies = $this->getApiData($apiCurrencies);
        if ($listCurrencies === false) {
            return $listCurrencies;
        }

        $list = [];
        foreach ($listCurrencies['data'] as $data) {
            $list[] = $data['id'];
        }

        $apiCrypto = 'https://api.coinbase.com/v2/currencies/crypto';
        $listCrypto = $this->getApiData($apiCrypto);
        if ($listCrypto === false) {
            return $listCrypto;
        }

        foreach ($listCrypto['data'] as $data) {
            $list[] = $data['code'];
        }

        $this->listOfCurrencies = $list;
        return $list;
    }

    public function verifyCurrency(string $currency): array
    {
        $list = $this->getList();

        if ($list === false) {
            return [
                'success' => false,
                'error' => 'Error message: Unsupported token pair, empty or invalid .json file'
            ];
        }

        if (!in_array($currency, $list)) {
            return [
                'success' => false,
                'error' => 'Error message: Invalid crypto or currency token'
            ];
        }

        return ['success' => true];
    }

    public function getCurrencyPair(string $cryptoGet, string $currencyGet): array
    {
        $api = 'https://api.coinbase.com/v2/prices/' . $cryptoGet . '-' . $currencyGet . '/spot';
        $currencyPair = $this->getApiData($api);
        if ($currencyPair === false) {
            return [
                'success' => false,
                'error' => 'Error message: Unsupported token pair, empty or invalid .json file'
            ];
        } else {
            return $currencyPair;
        }
    }
}
