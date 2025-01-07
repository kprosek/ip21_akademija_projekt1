<?php
require_once '../vendor/autoload.php';
require_once '../lib/model.php';

session_start();

$model = new Model();

$loader = new \Twig\Loader\FilesystemLoader('../lib/views/web');
$twig = new \Twig\Environment($loader, []);
$pdo = $model->databaseConnection('../');
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

function renderIndexView($twig, array $dropdownList, array $favourites, ?string $username, bool $userLoggedIn)
{
    echo $twig->render('index.html.twig', ['favourites' => $favourites, 'items' => $dropdownList, 'user' => $username, 'is_logged_in' => $userLoggedIn]);
};

renderIndexView($twig, $dropdownList, $favourites, $username, $userLoggedIn);
