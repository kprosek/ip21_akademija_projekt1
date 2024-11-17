<?php
require_once 'lib/model.php';
require_once 'lib/views/consoleView.php';

$commandGet = $argv[1] ?? null;
$cryptoGet = $argv[2] ?? null;
$currencyGet = $argv[3] ?? null;

$view = new ConsoleView();

function verifyArg(?string $cryptoGet, ?string $currencyGet, ConsoleView $view): void
{
    if ($cryptoGet === null || $currencyGet === null) {
        $view->printHelpText('Error message: Argument cannot be null');
        die;
    }

    if ((strlen($cryptoGet) < 3 || strlen($cryptoGet) > 10)) {
        $view->printHelpText('Error message: Wrong crypto token length');
        die;
    }

    if (strlen($currencyGet) !== 3) {
        $view->printHelpText('Error message: Wrong currency token length');
        die;
    }
}

$model = new Model();

function verifyResult(string $currency, ConsoleView $view, Model $model): void
{
    $verifyResult = $model->verifyCurrency($currency);
    if (($verifyResult['success']) === false) {
        $view->printHelpText($verifyResult['error']);
        die;
    }
};

function finalOutput(?string $commandGet, ?string $cryptoGet, ?string $currencyGet, ConsoleView $view, Model $model): void
{
    switch ($commandGet) {
        case 'help':
            $view->printHelpText('Help text:' . "\n" . 'For crypto token list enter: \'list\'' . "\n" . 'For currency pair enter: \'price\' BTC USD');
            break;

        case 'list':
            $list = $model->getList();
            $view->printList($list);
            break;

        case 'price':
            verifyArg($cryptoGet, $currencyGet, $view);
            verifyResult($currencyGet, $view, $model);
            verifyResult($cryptoGet, $view, $model);

            $currencyPair = $model->getCurrencyPair($cryptoGet, $currencyGet);
            if (($currencyPair['success']) === false) {
                $view->printHelpText($currencyPair['error']);
                die;
            } else {
                $view->printPricePair($currencyPair);
            }
            break;

        default:
            $view->printHelpText('Error message: Wrong first argument - valid arguments: help, price, list');
    }
}

finalOutput($commandGet, $cryptoGet, $currencyGet, $view, $model);
