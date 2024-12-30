<?php
require_once '../vendor/autoload.php';
require_once '../lib/model.php';

$model = new Model();

$loader = new \Twig\Loader\FilesystemLoader('../lib/views/web');
$twig = new \Twig\Environment($loader, []);
$pdo = $model->databaseConnection('../');

$list = $model->getList();
$favourites = $model->displayFavouriteTokens($pdo);
$dropdownList = array_merge($favourites, array_filter($list, function ($value) use ($favourites) {
    return !in_array($value, $favourites);
}));
$errorMessage = null;

function renderPriceView($twig, $dropdownList, $favourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, $errorMessage)
{
    echo $twig->render('price.html.twig', ['favourites' => $favourites, 'items' => $dropdownList, 'token_from' => $tokenFrom, 'price' => $currentPrice, 'token_to' => $tokenTo, 'star_class_from' => $btnFrom, 'star_class_to' => $btnTo, 'error' => $errorMessage ?? null]);
};

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $tokenFrom = trim($_GET['dropdown_token_from'], ' *');
    $tokenTo = trim($_GET['dropdown_token_to'], ' *');
    $currencyPair = $model->getCurrencyPair($tokenFrom, $tokenTo);

    if (($currencyPair['success']) === false) {
        renderPriceView($twig, $dropdownList, $favourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, 'Something went wrong, please try again!');
        die;
    }

    if (($currencyPair['success']) === true) {
        $btnFrom = $model->isTokenFavourite($tokenFrom, $favourites);
        $btnTo = $model->isTokenFavourite($tokenTo, $favourites);
        $currentPrice = round($currencyPair["currency pair"]["data"]["amount"], 3);
        renderPriceView($twig, $dropdownList, $favourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, $errorMessage);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tokenFrom = trim($_POST['dropdown_token_from'], ' *');
    $tokenTo = trim($_POST['dropdown_token_to'], ' *');
    $currentPrice = null;

    $toggleTokenFav = [];

    if (isset($_POST['btn_from'])) {
        $toggleTokenFav[] = $tokenFrom;
    } elseif (isset($_POST['btn_to'])) {
        $toggleTokenFav[] = $tokenTo;
    }

    if (in_array($toggleTokenFav[0], $favourites)) {
        $model->deleteFavouriteTokens($pdo, $toggleTokenFav);
        $updatedFavourites = $model->displayFavouriteTokens($pdo);

        $btnFrom = $model->isTokenFavourite($tokenFrom, $updatedFavourites);
        $btnTo = $model->isTokenFavourite($tokenTo, $updatedFavourites);
        renderPriceView($twig, $dropdownList, $updatedFavourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, $errorMessage);
    }

    if (!in_array($toggleTokenFav[0], $favourites)) {
        $model->insertFavouriteTokens($pdo, $toggleTokenFav);
        $updatedFavourites = $model->displayFavouriteTokens($pdo);

        $btnFrom = $model->isTokenFavourite($tokenFrom, $updatedFavourites);
        $btnTo = $model->isTokenFavourite($tokenTo, $updatedFavourites);
        renderPriceView($twig, $dropdownList, $updatedFavourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, $errorMessage);
    }
}
