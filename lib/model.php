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
