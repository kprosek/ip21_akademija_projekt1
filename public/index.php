<?php

require_once '../vendor/autoload.php';

use League\Route\Router;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\ProcessLoginController;
use App\Controllers\ShowPriceController;
use App\Controllers\LogoutController;

$router = new Router();

$router->map('GET', '/', [HomeController::class, 'showHomePage']);
$router->map('GET', '/login', [LoginController::class, 'showLoginPage']);
$router->map('POST', '/process-login', [ProcessLoginController::class, 'processLogin']);
$router->map(['GET', 'POST'], '/show-price', [ShowPriceController::class, 'showPricePage']);
$router->map('GET', '/logout', [LogoutController::class, 'logout']);

$request = ServerRequestFactory::fromGlobals();
$response = $router->dispatch($request);

(new SapiEmitter())->emit($response);
