<?php

namespace App\Controllers;

require_once '../config.php';
require_once '../vendor/autoload.php';
require_once '../lib/model.php';

use App\Model;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Laminas\Diactoros\Response;

class ShowPriceController
{
    private $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('../lib/views/web');
        $this->twig = new Environment($loader, []);
    }

    public function renderPriceView($twig, array $dropdownList, array $favourites, string $tokenFrom, string $tokenTo, ?string $currentPrice, bool $btnFrom, bool $btnTo, ?string $errorMessage, ?string $username, bool $userLoggedIn)
    {
        $html = $this->twig->render('price.html.twig', ['favourites' => $favourites, 'items' => $dropdownList, 'token_from' => $tokenFrom, 'price' => $currentPrice, 'token_to' => $tokenTo, 'star_class_from' => $btnFrom, 'star_class_to' => $btnTo, 'error' => $errorMessage ?? null, 'user' => $username, 'is_logged_in' => $userLoggedIn]);
        $response = new Response();
        $response->getBody()->write($html);

        return $response;
    }

    public function showPricePage()
    {
        session_start();

        $config = getAppConfig();
        $model = new Model($config);
        $pdo = $model->databaseConnection('../');

        $errorMessage = null;
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

        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $tokenFrom = trim($_GET['dropdown_token_from'], ' *');
            $tokenTo = trim($_GET['dropdown_token_to'], ' *');

            $currencyPair = $model->getCurrencyPair($tokenFrom, $tokenTo);

            if (($currencyPair['success']) === false) {
                $btnFrom = $model->isTokenFavourite($tokenFrom, $favourites);
                $btnTo = $model->isTokenFavourite($tokenTo, $favourites);
                $currentPrice = null;
                return $this->renderPriceView($this->twig, $dropdownList, $favourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, 'Something went wrong, please try again!', $username, $userLoggedIn);
            }

            if (($currencyPair['success']) === true) {
                $btnFrom = $model->isTokenFavourite($tokenFrom, $favourites);
                $btnTo = $model->isTokenFavourite($tokenTo, $favourites);
                $currentPrice = round($currencyPair["currency pair"]["data"]["amount"], 3);
                return $this->renderPriceView($this->twig, $dropdownList, $favourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, $errorMessage, $username, $userLoggedIn);
            }
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $tokenFrom = trim($_POST['dropdown_token_from'], ' *');
            $tokenTo = trim($_POST['dropdown_token_to'], ' *');
            $currentPrice = null;

            $toggleTokenFav = [];

            if (isset($_POST['btn_from'])) {
                $toggleTokenFav[] = $tokenFrom;
            } elseif (isset($_POST['btn_to'])) {
                $toggleTokenFav[] = $tokenTo;
            }

            if (in_array($toggleTokenFav[0], $favourites)) {
                $model->deleteFavouriteTokens($pdo, $userId, $toggleTokenFav);
                $updatedFavourites = $model->displayFavouriteTokens($pdo, $userId);

                $btnFrom = $model->isTokenFavourite($tokenFrom, $updatedFavourites);
                $btnTo = $model->isTokenFavourite($tokenTo, $updatedFavourites);
                return $this->renderPriceView($this->twig, $dropdownList, $updatedFavourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, $errorMessage, $username, $userLoggedIn);
            }

            if (!in_array($toggleTokenFav[0], $favourites)) {
                $model->insertFavouriteTokens($pdo, $userId, $toggleTokenFav);
                $updatedFavourites = $model->displayFavouriteTokens($pdo, $userId);

                $btnFrom = $model->isTokenFavourite($tokenFrom, $updatedFavourites);
                $btnTo = $model->isTokenFavourite($tokenTo, $updatedFavourites);
                return $this->renderPriceView($this->twig, $dropdownList, $updatedFavourites, $tokenFrom, $tokenTo, $currentPrice, $btnFrom, $btnTo, $errorMessage, $username, $userLoggedIn);
            }
        }
    }
}
