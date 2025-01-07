<?php
require_once '../vendor/autoload.php';

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../lib/views/web');
$twig = new \Twig\Environment($loader, []);

if (isset($_SESSION['login-error'])) {
    $error = $_SESSION['login-error'];
    unset($_SESSION['login-error']);
} else {
    $error = null;
}

if (isset($_SESSION['login-timeout'])) {
    $timeout = $_SESSION['login-timeout'];
    $error = null;
    unset($_SESSION['login-timeout']);
} else {
    $timeout = null;
}

echo $twig->render('login.html.twig', ['error' => $error, 'timeout' => $timeout]);
