<?php
require_once 'lib/model.php';
require_once 'lib/views/consoleView.php';

$command = $argv[1] ?? null;
$crypto = $argv[2] ?? null;
$currency = $argv[3] ?? null;

function verifyArg(?string $crypto, ?string $currency, ConsoleView $view): void
{
    if ($crypto === null || $currency === null) {
        $view->printHelpText('Error message: Argument cannot be null');
        die;
    }

    if ((strlen($crypto) < 3 || strlen($crypto) > 10)) {
        $view->printHelpText('Error message: Wrong crypto token length');
        die;
    }

    if (strlen($currency) !== 3) {
        $view->printHelpText('Error message: Wrong currency token length');
        die;
    }
}

function verifyCurrencyError(string $currency, ConsoleView $view, Model $model, array $listOfCurrenciesFromController): void
{
    $checkCurrency = $model->verifyCurrency($currency, $listOfCurrenciesFromController);
    if (($checkCurrency['success']) === false) {
        $view->printHelpText($checkCurrency['error']);
        die;
    }
};

function finalOutput(?string $command, ?string $crypto, ?string $currency): void
{
    $view = new ConsoleView();
    $model = new Model();
    $listOfCurrenciesFromController = $model->getList();

    switch ($command) {
        case 'help':
            $view->printHelpText('Help text:' . "\n" . 'For crypto token list enter: \'list\'' . "\n" . 'For currency pair enter: \'price\' BTC USD');
            break;

        case 'list':
            $list = $model->getList();
            $view->printList($list);
            break;

        case 'price':
            verifyArg($crypto, $currency, $view);
            verifyCurrencyError($currency, $view, $model, $listOfCurrenciesFromController);
            verifyCurrencyError($crypto, $view, $model, $listOfCurrenciesFromController);

            $currencyPair = $model->getCurrencyPair($crypto, $currency);
            if (($currencyPair['success']) === false) {
                $view->printHelpText($currencyPair['error']);
                die;
            }
            $view->printPricePair($currencyPair);
            break;

        default:
            $view->printHelpText('Error message: Wrong first argument - valid arguments: help, price, list');
    }
}

finalOutput($command, $crypto, $currency);
