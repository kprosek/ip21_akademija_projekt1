<?php
$cryptoGet = $argv[1] ?? null;
$currencyGet = $argv[2] ?? null;
$apiUrl = 'https://api.coinbase.com/v2/prices/' . $cryptoGet . '-' . $currencyGet . '/spot';

function getApiData($api)
{
    $json = file_get_contents($api);
    $dataArray = json_decode($json, true);
    return $dataArray;
}
if ($cryptoGet === 'help') {
    echo sprintf('Error message: Need to add arguments in the input - example: BTC USD');
    die;
}

// GET Currencies
function currencyList()
{
    $api = 'https://api.coinbase.com/v2/currencies';
    $currencies = getApiData($api);
    $list = [];
    foreach ($currencies['data'] as $currency) {
        $list[] = $currency['id'];
    }
    return $list;
}

$currencies = currencyList();

// GET Crypto Currencies
function cryptoList()
{
    $api = 'https://api.coinbase.com/v2/currencies/crypto';
    $crypto = getApiData($api);
    $list = [];
    foreach ($crypto['data'] as $cry) {
        $list[] = $cry['code'];
    }
    return $list;
}

$crypto = cryptoList();

// Error handling
if ($cryptoGet === null || $currencyGet === null) {
    echo sprintf('Error message: Missing arguments in the input');
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
