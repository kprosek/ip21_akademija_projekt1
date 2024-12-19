<?php
require_once '../vendor/autoload.php';
require_once '../lib/model.php';
$model = new Model();

$loader = new \Twig\Loader\FilesystemLoader('../lib/views/web');
$twig = new \Twig\Environment($loader, []);
$pdo = $model->databaseConnection('../');

$list = $model->getList();
$favourites = $model->displayFavouriteTokens($pdo);

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $tokenFrom = $_GET['dropdown_token_from'];
    $tokenTo = $_GET['dropdown_token_to'];

    $currencyPair = $model->getCurrencyPair($tokenFrom, $tokenTo);
    if (($currencyPair['success']) === false) {
        echo $twig->render('error.html.twig', ['items' => $list, 'token_from' => $tokenFrom, 'token_to' => $tokenTo]);
    }

    if (($currencyPair['success']) === true) {
        $currentPrice = round($currencyPair["currency pair"]["data"]["amount"], 3);
        echo $twig->render('price.html.twig', ['favourites' => $favourites, 'items' => $list, 'token_from' => $tokenFrom, 'price' => $currentPrice, 'token_to' => $tokenTo]);
    }
}
