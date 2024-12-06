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

function isCurrencyInMasterList(string $currency, ConsoleView $view, Model $model, array $masterCurrencyList): void
{
    $checkCurrency = $model->verifyCurrency($currency, $masterCurrencyList);
    if (($checkCurrency['success']) === false) {
        $view->printHelpText($checkCurrency['error']);
        die;
    }
};

function finalOutput(?string $command, ?string $crypto, ?string $currency): void
{
    $view = new ConsoleView();
    $model = new Model();
    $masterCurrencyList = $model->getList();

    switch ($command) {
        case 'help':
            $view->printHelpText('Help text:' . "\n" . 'For crypto token list enter: \'list\'' . "\n" . 'For currency pair enter: \'price\' BTC USD');
            break;

        case 'list':
            $list = $model->getList();

            $view->printList($list);

            $ifSaveUserFavouriteTokens = readline("Do you wish to mark any as favorite? (y/n)");

            $savedUserTokens = [];
            if ($ifSaveUserFavouriteTokens === "y") {
                $saveUsersFavouriteTokens = readline("Please enter the number in front of the currency you wish to favorite:");
                $savedUserTokenKeys = explode(',', $saveUsersFavouriteTokens);
                foreach ($savedUserTokenKeys as $key) {
                    if (array_key_exists($key, $list) === false) {
                        $view->printHelpText('You entered a wrong number. Marking token as favourite was not successful');
                        die;
                    }
                    $savedUserTokens[] = $list[$key];
                }
            }
            var_dump($savedUserTokens);
            break;

        case 'price':
            verifyArg($crypto, $currency, $view);
            isCurrencyInMasterList($currency, $view, $model, $masterCurrencyList);
            isCurrencyInMasterList($crypto, $view, $model, $masterCurrencyList);

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
