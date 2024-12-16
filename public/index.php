<?php
require_once '../vendor/autoload.php';
require_once '../lib/model.php';
$model = new Model();

$loader = new \Twig\Loader\FilesystemLoader('../lib/views/web');
$twig = new \Twig\Environment($loader, []);
$pdo = $model->databaseConnectionWeb();

$list = $model->getList();
$favourites = $model->displayFavouriteTokens($pdo);

echo $twig->render('list.html.twig', ['favourites' => $favourites, 'items' => $list]);