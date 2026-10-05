<?php
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/Auth.php';

class App
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $url = isset($_GET['url']) ? $_GET['url'] : '';
        $router = new Router($url);
        $router->run();
    }
}
