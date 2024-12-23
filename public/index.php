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

echo $twig->render('home.html.twig', ['favourites' => $favourites, 'items' => $dropdownList, 'star_class_from' => 'hidden', 'star_class_to' => 'hidden']);
