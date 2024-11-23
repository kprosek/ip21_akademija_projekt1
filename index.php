<?php
require_once 'vendor/autoload.php';
require_once 'lib/model.php';
$model = new Model();

$loader = new \Twig\Loader\FilesystemLoader('lib/views/web');
$twig = new \Twig\Environment($loader, [
]);

// echo $twig->render('index.html.twig', ['name' => 'Klara']);

$list = ['items' => $model->getList()];

echo $twig->render('list.html.twig', $list);
