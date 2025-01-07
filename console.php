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
    $pdo = $model->databaseConnection('');

    if (PHP_SAPI === 'cli') {
        $userId = 0;
    }

    switch ($command) {
        case 'help':
            $view->printHelpText('How to use:' . PHP_EOL . '1. For crypto token list and marking Favourites enter: \'list\'' . PHP_EOL . '2. To delete Favourite enter: \'delete\'' . PHP_EOL . '3. For Currency pair enter: \'price\' BTC USD' . PHP_EOL . '4. To add new User enter: \'add user\'' . PHP_EOL);
            break;

        case 'list':
            $list = $model->getList();

            $view->printList($list);

            $favourites = implode(PHP_EOL, $model->displayFavouriteTokens($pdo, $userId));
            $view->printFavouriteTokens($favourites);

            $ifSaveUserFavouriteTokens = readline('Do you wish to mark any as favorite? (y/n)');

            $savedUserTokens = [];
            if ($ifSaveUserFavouriteTokens === 'y') {
                $saveUsersFavouriteTokens = str_replace(' ', '', readline('Please enter the number in front of the currency you wish to favorite: '));
                $savedUserTokenKeys = explode(',', $saveUsersFavouriteTokens);

                $savedUserTokens = [];
                foreach ($savedUserTokenKeys as $key) {
                    if (array_key_exists($key, $list) === false) {
                        $view->printHelpText('You entered a wrong number. Marking token as favourite was not successful');
                        die;
                    }
                    $savedUserTokens[] = $list[$key];
                }
                $model->insertFavouriteTokens($pdo, $userId, $savedUserTokens);
                $view->printHelpText('Favourite tokens saved!');
            }
            break;

        case 'delete':
            $ifDeleteUserFavouriteTokens = readline('Do you wish to remove a token from favorites? (y/n)');
            if ($ifDeleteUserFavouriteTokens === 'y') {
                $deleteUserFavouriteTokens = str_replace(' ', '', readline('Please enter the token you wish to delete: '));
                $deleteUserTokens = explode(',', $deleteUserFavouriteTokens);
                $model->deleteFavouriteTokens($pdo, $userId, $deleteUserTokens);
                $view->printHelpText('Favourite tokens removed!');
            }
            break;

        case 'price':
            verifyArg($crypto, $currency, $view);
            isCurrencyInMasterList($currency, $view, $model, $masterCurrencyList);
            isCurrencyInMasterList($crypto, $view, $model, $masterCurrencyList);

            $currencyPair = $model->getCurrencyPair($crypto, $currency);
            if (($currencyPair['success']) === false) {
                $view->printHelpText($currencyPair['error']);
            }
            if (($currencyPair['success']) === true) {
                $view->printPricePair($currencyPair['currency pair']);
            }
            break;

        case 'add user':
            $addNewUsername = readline('To add new User, enter Username:');
            if ($addNewUsername !== null) {
                $addNewUserPassword = readline('Set a Password:');
            }

            if (!filter_var($addNewUsername, FILTER_VALIDATE_EMAIL)) {
                $view->printHelpText('Error: Username must be an email');
                die;
            }

            $userValidation = $model->validateRegisterUser($pdo, $addNewUsername);

            if ($userValidation['mail'] === false) {
                $view->printHelpText($userValidation['error']);
                die;
            }

            if ($userValidation['mail'] === true) {
                $registerNewUser = $model->addNewUser($pdo, $addNewUsername, $addNewUserPassword);

                if ($registerNewUser['credentials'] === true) {
                    $view->printHelpText($registerNewUser['message']);
                }
                if ($registerNewUser['credentials'] !== true) {
                    $view->printHelpText('Error: Something went wrong, please try again');
                }
            }
            break;

        default:
            $view->printHelpText('Error: Wrong first argument - valid arguments: help, price, list');
    }
}

finalOutput($command, $crypto, $currency);
