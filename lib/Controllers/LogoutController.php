<?php

namespace App\Controllers;

require_once '../config.php';
require_once '../vendor/autoload.php';
require_once '../lib/model.php';

use App\Model;

class LogoutController
{
    public function logout()
    {
        $config = getAppConfig();
        $model = new Model($config);
        $pdo = $model->databaseConnection('../');

        session_start();
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $model->removeIpCount($pdo, $ipAddress);

        session_unset();

        session_destroy();

        header('Location: /');
        die;
    }
}
