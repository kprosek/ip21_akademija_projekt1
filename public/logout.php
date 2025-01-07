<?php
require_once '../vendor/autoload.php';
require_once '../lib/model.php';

$model = new Model();
$pdo = $model->databaseConnection('../');

session_start();
$ipAddress = $_SERVER['REMOTE_ADDR'];

$model->removeIpCount($pdo, $ipAddress);

session_unset();

session_destroy();

header('Location: index.php');
die;
