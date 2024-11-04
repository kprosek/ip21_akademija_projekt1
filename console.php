<?php
$currencyGet = $argv[1];
$pairGet = $argv[2];
$apiUrl = 'https://api.coinbase.com/v2/prices/' . $currencyGet . '-' . $pairGet . '/spot';
$apiCurrency = 'https://api.coinbase.com/v2/currencies';
$apiCryptoCurrency = 'https://api.coinbase.com/v2/currencies/crypto';

function data($api)
{;
    $json = file_get_contents($api);
    $dataArray = json_decode($json, true);
    return $dataArray;
}

// Error handling
if (($currencyGet === null || $pairGet === null) || $currencyGet === 'help') {
    echo ('Help text');
    die;
} else if ((strlen($currencyGet) < 3 && strlen($currencyGet) > 10) || strlen($pairGet) !== 3) {
    echo ('Error: Not a currency token');
    die;
}

if (file_get_contents($apiUrl) === false) {
    echo ('Error: Wrong currency pair');
    die;
};

$currencyPair = data($apiUrl);
echo sprintf('%s: %.2f %s', $currencyPair['data']['base'], $currencyPair['data']['amount'], $currencyPair['data']['currency']);

$currencies = data($apiCurrency);
$currenciesList = [];
foreach ($currencies['data'] as $currency) {
    $currenciesList[] = $currency['id'];
}

$crypto = data($apiCryptoCurrency);
$cryptoList = [];
foreach ($crypto['data'] as $cry) {
    $cryptoList[] = $cry['code'];
}

var_dump($cryptoList);
