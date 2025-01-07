<?php
require_once '../vendor/autoload.php';
require_once '../lib/model.php';

session_start();

$model = new Model();

$loader = new \Twig\Loader\FilesystemLoader('../lib/views/web');
$twig = new \Twig\Environment($loader, []);
$pdo = $model->databaseConnection('../');
$errorMessage = null;
$userLoggedIn = isset($_SESSION['logged_in_as']);

if ($userLoggedIn) {
    $username = $_SESSION['user'];
    $userId = $_SESSION['logged_in_as'];
} else {
    $username = null;
    $userId = null;
}

$list = $model->getList();
$favourites = $model->displayFavouriteTokens($pdo, $userId);
$dropdownList = array_merge($favourites, array_filter($list, function ($value) use ($favourites) {
    return !in_array($value, $favourites);
}));

function renderPriceView($twig, array $dropdownList, array $favourites, string $tokenFrom, string $tokenTo, ?string $currentPrice, bool $btnFrom, bool $btnTo, ?string $errorMessage, ?string $username, bool $userLoggedIn)
{
    echo $twig->render('price.html.twig', ['favourites' => $favourites, 'items' => $dropdownList, 'token_from' => $tokenFrom, 'price' => $currentPrice, 'token_to' => $tokenTo, 'star_class_from' => $btnFrom, 'star_class_to' => $btnTo, 'error' => $errorMessage ?? null, 'user' => $username, 'is_logged_in' => $userLoggedIn]);
};

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $tokenFrom = trim($_GET['dropdown_token_from'], ' *');
    $tokenTo = trim($_GET['dropdown_token_to'], ' *');
    $currencyPair = $model->getCurrencyPair($tokenFrom, $tokenTo);

    if (($currencyPair['success']) === false) {
        renderPriceView($twig, $dropdownList, $favourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, 'Something went wrong, please try again!', $username, $userLoggedIn);
        die;
    }

    if (($currencyPair['success']) === true) {
        $btnFrom = $model->isTokenFavourite($tokenFrom, $favourites);
        $btnTo = $model->isTokenFavourite($tokenTo, $favourites);
        $currentPrice = round($currencyPair["currency pair"]["data"]["amount"], 3);
        renderPriceView($twig, $dropdownList, $favourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, $errorMessage, $username, $userLoggedIn);
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
        $model->deleteFavouriteTokens($pdo, $userId, $toggleTokenFav);
        $updatedFavourites = $model->displayFavouriteTokens($pdo, $userId);

        $btnFrom = $model->isTokenFavourite($tokenFrom, $updatedFavourites);
        $btnTo = $model->isTokenFavourite($tokenTo, $updatedFavourites);
        renderPriceView($twig, $dropdownList, $updatedFavourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, $errorMessage, $username, $userLoggedIn);
    }

    if (!in_array($toggleTokenFav[0], $favourites)) {
        $model->insertFavouriteTokens($pdo, $userId, $toggleTokenFav);
        $updatedFavourites = $model->displayFavouriteTokens($pdo, $userId);

        $btnFrom = $model->isTokenFavourite($tokenFrom, $updatedFavourites);
        $btnTo = $model->isTokenFavourite($tokenTo, $updatedFavourites);
        renderPriceView($twig, $dropdownList, $updatedFavourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, $errorMessage, $username, $userLoggedIn);
    }
}
