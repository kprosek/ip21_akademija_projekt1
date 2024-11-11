<?php
require_once 'lib/model.php';
require_once 'lib/views/consoleView.php';

$commandGet = $argv[1] ?? null;
$cryptoGet = $argv[2] ?? null;
$currencyGet = $argv[3] ?? null;

function printHelpText($cryptoGet, $currencyGet)
{
    if ($cryptoGet === null || $currencyGet === null) {
        echo ('Error message: Argument cannot be null');
        die;
    }

    if ((strlen($cryptoGet) < 3 || strlen($cryptoGet) > 10)) {
        echo ('Error message: Wrong crypto token length');
        die;
    }

    if (strlen($currencyGet) !== 3) {
        echo ('Error message: Wrong currency token length');
        die;
    }
}

function printErrorText($cryptoGet, $currencyGet)
{
    $currenciesList = getListCurrencies();
    $cryptoList = getListCrypto();
    if ($currenciesList === false || $cryptoList === false) {
        echo ('Error message: Unsupported token pair, empty or invalid .json file for currencies or crypto token list');
        die;
    };

    if (in_array($cryptoGet, $cryptoList) === false || in_array($currencyGet, $currenciesList) === false) {
        echo ('Error message: Invalid crypto or currency token');
        die;
    }

    $currencyPair = getCurrencyPair($cryptoGet, $currencyGet);
    if ($currencyPair === false) {
        echo ('Error message: Unsupported token pair, empty or invalid .json file for currencies pair');
        die;
    };
}

switch ($commandGet) {
    case 'help':
        echo sprintf('Help text:' . "\n" . 'For crypto token list enter: \'list\'' . "\n" . 'For currency pair enter: \'price\' BTC USD');
        break;
    case 'list':
        $cryptoList = getListCrypto();
        printlist($cryptoList);
        break;
    case 'price':
        printHelpText($cryptoGet, $currencyGet);
        printErrorText($cryptoGet, $currencyGet);
        $currencyPair = getCurrencyPair($cryptoGet, $currencyGet);
        printPricePair($currencyPair);
        break;
    default:
        echo ('Error message: Wrong first argument - valid arguments: help, price, list');
}
