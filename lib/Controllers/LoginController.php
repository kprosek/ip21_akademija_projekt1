<?php

namespace App\Controllers;

require_once '../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Laminas\Diactoros\Response;

class LoginController
{
    private $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('../lib/views/web');
        $this->twig = new Environment($loader, []);
    }

    public function showLoginPage()
    {
        session_start();

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

        $html = $this->twig->render('login.html.twig', ['error' => $error, 'timeout' => $timeout]);

        $response = new Response();
        $response->getBody()->write($html);

        return $response;
    }
}
