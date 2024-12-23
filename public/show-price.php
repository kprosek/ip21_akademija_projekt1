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

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $tokenFrom = trim($_GET['dropdown_token_from'], ' *');
    $tokenTo = trim($_GET['dropdown_token_to'], ' *');
    $currencyPair = $model->getCurrencyPair($tokenFrom, $tokenTo);

    if (($currencyPair['success']) === false) {
        echo $twig->render('error.html.twig', ['items' => $list, 'token_from' => $tokenFrom, 'token_to' => $tokenTo]);
    }

    if (($currencyPair['success']) === true) {
        $btnFrom = $model->isTokenFavourite($tokenFrom, $favourites);
        $btnTo = $model->isTokenFavourite($tokenTo, $favourites);

        $currentPrice = round($currencyPair["currency pair"]["data"]["amount"], 3);
        echo $twig->render('price.html.twig', ['favourites' => $favourites, 'items' => $list, 'token_from' => $tokenFrom, 'price' => $currentPrice, 'token_to' => $tokenTo, 'star_class_from' => $btnFrom, 'star_class_to' => $btnTo]);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tokenTo = trim($_POST['dropdown_token_to'], ' *');
    $tokenFrom = trim($_POST['dropdown_token_from'], ' *');
    $currencyPair = $model->getCurrencyPair($tokenFrom, $tokenTo);
    $toggleTokenFav = [];
    $btn = "";

    $btnFrom = $model->isTokenFavourite($tokenFrom, $favourites);
    $btnTo = $model->isTokenFavourite($tokenTo, $favourites);

    if (isset($_POST['btn_from'])) {
        $toggleTokenFav[] = $tokenFrom;
        $btn = 'btn_from';
    }

    if (isset($_POST['btn_to'])) {
        $toggleTokenFav[] = $tokenTo;
        $btn = 'btn_to';
    }

    if (in_array($toggleTokenFav[0], $favourites)) {
        $model->deleteFavouriteTokens($pdo, $toggleTokenFav);
        $updatedFavourites = $model->displayFavouriteTokens($pdo);

        if ($btn === 'btn_from') {
            $btnFrom = 'fa-regular';
            echo $twig->render('price.html.twig', ['favourites' => $updatedFavourites, 'items' => $list, 'token_from' => $tokenFrom, 'token_to' => $tokenTo, 'star_class_from' => $btnFrom, 'star_class_to' => $btnTo]);
        }

        if ($btn === 'btn_to') {
            $btnTo = 'fa-regular';
            echo $twig->render('price.html.twig', ['favourites' => $updatedFavourites, 'items' => $list, 'token_from' => $tokenFrom, 'token_to' => $tokenTo, 'star_class_from' => $btnFrom, 'star_class_to' => $btnTo]);
        }
    }

    if (in_array($toggleTokenFav[0], $favourites) === false) {
        $model->insertFavouriteTokens($pdo, $toggleTokenFav);
        $updatedFavourites = $model->displayFavouriteTokens($pdo);

        if ($btn === 'btn_from') {
            $btnFrom = 'fa-solid';
            echo $twig->render('price.html.twig', ['favourites' => $updatedFavourites, 'items' => $list, 'token_from' => $tokenFrom, 'token_to' => $tokenTo, 'star_class_from' => $btnFrom, 'star_class_to' => $btnTo]);
        }

        if ($btn === 'btn_to') {
            $btnTo = 'fa-solid';
            echo $twig->render('price.html.twig', ['favourites' => $updatedFavourites, 'items' => $list, 'token_from' => $tokenFrom, 'token_to' => $tokenTo, 'star_class_from' => $btnFrom, 'star_class_to' => $btnTo]);
        }
    }
}
