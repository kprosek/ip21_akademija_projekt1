<?php

namespace App\Controllers;

require_once '../config.php';
require_once '../vendor/autoload.php';
require_once '../lib/model.php';

use App\Model;

class ProcessLoginController
{

    public function processLogin()
    {
        $config = getAppConfig();
        $model = new Model($config);
        $pdo = $model->databaseConnection('../');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $username = $_POST['input_username'];
            $password = $_POST['input_password'];

            $ipCount = $model->ipCount($pdo, $ipAddress);

            if ($ipCount === false) {
                session_start();
                $_SESSION['login-timeout'] = 'Too many failed login attempts! Try again after 60s.';
                header('Location: /login');
            }

            if ($ipCount === true) {
                $userId = $model->authenticateLoginUser($pdo, $username, $password);

                if ($userId) {
                    session_start();
                    $_SESSION['logged_in_as'] = $userId;
                    $_SESSION['user'] = $username;
                    header('Location: /');
                }

                if ($userId === false) {
                    session_start();
                    $_SESSION['login-error'] = 'Invalid credentials';
                    header('Location: /login');
                }
            }
        }
    }
}
