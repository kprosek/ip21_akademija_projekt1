<?php
function verifyData($cryptoGet, $currencyGet, $cryptoList, $currenciesList, $currencyPair)
{
    if ($currenciesList === false || $cryptoList === false) {
        printHelpText('Error message: Unsupported token pair, empty or invalid .json file for currencies or crypto token list');
        die;
    };

    if (in_array($cryptoGet, $cryptoList) === false || in_array($currencyGet, $currenciesList) === false) {
        printHelpText('Error message: Invalid crypto or currency token');
        die;
    }

    if ($currencyPair === false) {
        printHelpText('Error message: Unsupported token pair, empty or invalid .json file for currencies pair');
        die;
    };
}

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

function getCurrencyPair($cryptoGet, $currencyGet)
{

    $api = 'https://api.coinbase.com/v2/prices/' . $cryptoGet . '-' . $currencyGet . '/spot';
    return getApiData($api);
}

function getListCrypto()
{
    $api = 'https://api.coinbase.com/v2/currencies/crypto';
    $listData = getApiData($api);
    $list = [];
    foreach ($listData['data'] as $data) {
        $list[] = $data['code'];
    }
    return $list;
}

function getListCurrencies()
{
    $api = 'https://api.coinbase.com/v2/currencies';
    $listData = getApiData($api);
    $list = [];
    foreach ($listData['data'] as $data) {
        $list[] = $data['id'];
    }
    return $list;
}
