<?php
// front controller, controller that handles all requests for a web site 
//https://dev.to/xxzeroxx/php-design-patterns-front-controller-jjo

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require_once __DIR__ . '/../config/routes.php';

$basePath = dirname($_SERVER['SCRIPT_NAME']);

$router = new App\Kernel\Router($routes, $basePath);
$router->dispatch();
