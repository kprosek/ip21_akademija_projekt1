<?php

namespace App\Controllers;

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../lib/model.php';

use App\Model;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Laminas\Diactoros\Response;

class HomeController
{
    private $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('../lib/views/web');
        $this->twig = new Environment($loader, []);
    }

    public function renderHomeView($twig, array $dropdownList, array $favourites, ?string $username, bool $userLoggedIn)
    {
        $html = $this->twig->render('home.html.twig', ['favourites' => $favourites, 'items' => $dropdownList, 'user' => $username, 'is_logged_in' => $userLoggedIn]);
        $response = new Response();
        $response->getBody()->write($html);

        return $response;
    }

    public function showHomePage()
    {
        session_start();

        $config = getAppConfig();
        $model = new Model($config);
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

        return $this->renderHomeView($this->twig, $dropdownList, $favourites, $username, $userLoggedIn);
    }
}
