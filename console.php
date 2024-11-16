<?php
require_once 'lib/model.php';
require_once 'lib/views/consoleView.php';

$commandGet = $argv[1] ?? null;
$cryptoGet = $argv[2] ?? null;
$currencyGet = $argv[3] ?? null;

function verifyArg($cryptoGet, $currencyGet)
{
    if ($cryptoGet === null || $currencyGet === null) {
        printHelpText('Error message: Argument cannot be null');
        die;
    }

    if ((strlen($cryptoGet) < 3 || strlen($cryptoGet) > 10)) {
        printHelpText('Error message: Wrong crypto token length');
        die;
    }

    if (strlen($currencyGet) !== 3) {
        printHelpText('Error message: Wrong currency token length');
        die;
    }
}

switch ($commandGet) {
    case 'help':
        printHelpText('Help text:' . "\n" . 'For crypto token list enter: \'list\'' . "\n" . 'For currency pair enter: \'price\' BTC USD');
        break;

    case 'list':
        $cryptoList = getList('/crypto', 'code');
        printList($cryptoList);
        break;

    case 'price':
        verifyArg($cryptoGet, $currencyGet);

        $currenciesList = getList('', 'id');
        $cryptoList = getList('/crypto', 'code');

        $verifyResult = verifyCurrencies($cryptoGet, $currencyGet, $cryptoList, $currenciesList);
        if (($verifyResult['success']) === false) {
            printHelpText($verifyResult['error']);
            return;
        }

        $currencyPair = getCurrencyPair($cryptoGet, $currencyGet);
        if (($currencyPair['success']) === false) {
            printHelpText($currencyPair['error']);
            return;
        } else {
            printPricePair($currencyPair);
        }
        break;

    default:
        printHelpText('Error message: Wrong first argument - valid arguments: help, price, list');
}
