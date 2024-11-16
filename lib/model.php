<?php

function getApiData($api)
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

function getList($endpoint, $key)
{
    $api = 'https://api.coinbase.com/v2/currencies' . $endpoint;
    $listData = getApiData($api);
    if ($listData === false) {
        return $listData;
    }

    $list = [];
    foreach ($listData['data'] as $data) {
        $list[] = $data[$key];
    }
    return $list;
}

function verifyCurrencies($cryptoGet, $currencyGet, $cryptoList, $currenciesList)
{
    if ($currenciesList === false || $cryptoList === false) {
        return [
            'success' => false,
            'error' => 'Error message: Unsupported token pair, empty or invalid .json file'
        ];
    }

    if (!in_array($cryptoGet, $cryptoList) || !in_array($currencyGet, $currenciesList)) {
        return [
            'success' => false,
            'error' => 'Error message: Invalid crypto or currency token'
        ];
    }

    return ['success' => true];
}

function getCurrencyPair($cryptoGet, $currencyGet)
{
    $api = 'https://api.coinbase.com/v2/prices/' . $cryptoGet . '-' . $currencyGet . '/spot';
    $currencyPair = getApiData($api);
    if ($currencyPair === false) {
        return [
            'success' => false,
            'error' => 'Error message: Unsupported token pair, empty or invalid .json file'
        ];
    } else {
        return $currencyPair;
    }
}
