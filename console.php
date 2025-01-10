<?php

require_once 'config.php';
require_once 'lib/model.php';
require_once 'lib/views/consoleView.php';

use App\Model;

$command = $argv[1] ?? null;
$crypto = $argv[2] ?? null;
$currency = $argv[3] ?? null;

function verifyArg(?string $crypto, ?string $currency, ConsoleView $view): void
{
    if ($crypto === null || $currency === null) {
        $view->printErrorText('Error: Argument cannot be null');
        die;
    }

    if ((strlen($crypto) < 3 || strlen($crypto) > 10)) {
        $view->printErrorText('Error: Wrong token length');
        die;
    }

    if (strlen($currency) < 3 || strlen($currency) > 10) {
        $view->printErrorText('Error: Wrong token length');
        die;
    }
}

function isCurrencyInMasterList(string $currency, ConsoleView $view, Model $model, array $masterCurrencyList): void
{
    $checkCurrency = $model->verifyCurrency($currency, $masterCurrencyList);
    if (($checkCurrency['success']) === false) {
        $view->printErrorText($checkCurrency['error']);
        die;
    }
};

function finalOutput(?string $command, ?string $crypto, ?string $currency): void
{
    $config = getAppConfig();
    $view = new ConsoleView();
    $model = new Model($config);
    $masterCurrencyList = $model->getList();
    $pdo = $model->databaseConnection('');

    if (PHP_SAPI === 'cli') {
        $userId = $config->get('cli.default_user_id');
    }

    switch ($command) {
        case 'help':
            $view->printHelpText('How to use:' . PHP_EOL . '1. To view Token list and marking Favourites: \'list\'' . PHP_EOL . '2. To Delete a Favourite from the list: \'delete\'' . PHP_EOL . '3. To view Currency pair: \'price\' BTC USD' . PHP_EOL . '4. To register new User to database: \'add user\'' . PHP_EOL);
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
                        $view->printErrorText('Error: You entered a wrong number. Marking token as favourite was not successful');
                        die;
                    }
                    $savedUserTokens[] = $list[$key];
                }
                $model->insertFavouriteTokens($pdo, $userId, $savedUserTokens);
                $view->printSuccessText('Favourite tokens saved!');
            }
            break;

        case 'delete':
            $favourites = $model->displayFavouriteTokens($pdo, $userId);

            $ifDeleteUserFavouriteTokens = readline('Do you wish to remove a token from favorites? (y/n)');
            if ($ifDeleteUserFavouriteTokens === 'y') {
                $deleteUserFavouriteTokens = str_replace(' ', '', readline('Please enter the token you wish to delete: '));
                $deleteUserTokens = explode(',', $deleteUserFavouriteTokens);

                foreach ($deleteUserTokens as $token) {
                    if (!in_array($token, $favourites)) {
                        $view->printErrorText('Error: Invalid token data!');
                        die;
                    }
                }
                $model->deleteFavouriteTokens($pdo, $userId, $deleteUserTokens);
                $view->printSuccessText('Favourite tokens removed!');
            }
            break;

        case 'price':
            verifyArg($crypto, $currency, $view);
            isCurrencyInMasterList($currency, $view, $model, $masterCurrencyList);
            isCurrencyInMasterList($crypto, $view, $model, $masterCurrencyList);

            $currencyPair = $model->getCurrencyPair($crypto, $currency);
            if (($currencyPair['success']) === false) {
                $view->printErrorText($currencyPair['error']);
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

            if ($addNewUserPassword === '') {
                $view->printErrorText('Error: Password cannot be an empty string');
                die;
            }

            if (!filter_var($addNewUsername, FILTER_VALIDATE_EMAIL)) {
                $view->printErrorText('Error: Username must be an email');
                die;
            }

            $userValidation = $model->validateRegisterUser($pdo, $addNewUsername);

            if ($userValidation['mail'] === false) {
                $view->printErrorText($userValidation['error']);
                die;
            }

            if ($userValidation['mail'] === true) {
                $registerNewUser = $model->addNewUser($pdo, $addNewUsername, $addNewUserPassword);

                if ($registerNewUser['credentials'] === true) {
                    $view->printSuccessText($registerNewUser['message']);
                }
                if ($registerNewUser['credentials'] !== true) {
                    $view->printErrorText('Error: Something went wrong, please try again');
                }
            }
            break;

        default:
            $view->printErrorText('Error: Wrong first argument - valid arguments: help, list, price, delete or add user');
    }
}

finalOutput($command, $crypto, $currency);
