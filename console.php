<?php
$commandGet = $argv[1] ?? null;
$cryptoGet = $argv[2] ?? null;
$currencyGet = $argv[3] ?? null;
$apiUrl = 'https://api.coinbase.com/v2/prices/' . $cryptoGet . '-' . $currencyGet . '/spot';
$apiCurrency = 'https://api.coinbase.com/v2/currencies';
$apiCryptoCurrency = 'https://api.coinbase.com/v2/currencies/crypto';

function getApiData($api)
{
    $json = file_get_contents($api);
    if (empty($json)) {
        echo sprintf('Error message: .json file is empty');
        die;
    }

    $dataArray = json_decode($json, true);
    if ($dataArray === null) {
        echo sprintf('Error message: invalid input .json file');
        die;
    }

    return $dataArray;
}

// GET Currencies
$currencies = getApiData($apiCurrency);
$currenciesList = [];
foreach ($currencies['data'] as $currency) {
    $currenciesList[] = $currency['id'];
}

// GET Crypto Currencies
$crypto = getApiData($apiCryptoCurrency);
$cryptoList = [];
foreach ($crypto['data'] as $cry) {
    $cryptoList[] = $cry['code'];
}

if ($commandGet === null && $commandGet !== 'price' && $commandGet !== 'list') {
    echo sprintf('Error message: Wrong first argument');
    die;
}

if ($commandGet === 'price') {
    // Error handling
    if ($cryptoGet === null || $currencyGet === null) {
        echo sprintf('Error message: Missing arguments in the input');
        die;
    }

    if ($cryptoGet === 'help') {
        echo sprintf('Error message: Need to add arguments in the input - example: BTC USD');
        die;
    }

    if ((strlen($cryptoGet) < 3 || strlen($cryptoGet) > 10) || strlen($currencyGet) !== 3) {
        echo sprintf('Error message: Wrong crypto or currency token length');
        die;
    }

    if (in_array($cryptoGet, $cryptoList) === false || in_array($currencyGet, $currenciesList) === false) {
        echo sprintf('Error message: Invalid crypto or currency token');
        die;
    }

    if (file_get_contents($apiUrl) === false) {
        echo sprintf('Error message: Unsupported token pair');
        die;
    };

    // Output of data
    $currencyPair = getApiData($apiUrl);
    echo sprintf('%s: %.2f %s', $currencyPair['data']['base'], $currencyPair['data']['amount'], $currencyPair['data']['currency']);
}

if ($commandGet === 'list') {
    $list = implode(', ', $cryptoList);
    echo sprintf($list);
}