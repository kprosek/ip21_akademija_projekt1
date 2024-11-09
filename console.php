<?php
require_once 'lib/model.php';
require_once 'lib/views/consoleView.php';

$commandGet = $argv[1] ?? null;
$cryptoGet = $argv[2] ?? null;
$currencyGet = $argv[3] ?? null;
$apiUrl = 'https://api.coinbase.com/v2/prices/' . $cryptoGet . '-' . $currencyGet . '/spot';
$apiCurrencies = 'https://api.coinbase.com/v2/currencies';
$apiCrypto = 'https://api.coinbase.com/v2/currencies/crypto';

$currencyPair = getApiData($apiUrl);
$currencies = getList($apiCurrencies, 'id');
$crypto = getList($apiCrypto, 'code');

if ($commandGet === 'price') {
    printHelpText($apiUrl, $cryptoGet, $currencyGet, $crypto, $currencies);
    printPricePair($currencyPair);
} else if ($commandGet === 'list') {
    printlist($crypto);
} else {
    echo sprintf('Error message: Wrong first argument');
    die;
}
