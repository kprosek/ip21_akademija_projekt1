<?php
require_once 'vendor/autoload.php';
require_once 'lib/model.php';
$model = new Model();

$loader = new \Twig\Loader\FilesystemLoader('lib/views/web');
$twig = new \Twig\Environment($loader, [
]);

$list = ['items' => $model->getList()];

echo $twig->render('list.html.twig', $list);
