<?php
// front controller, controller that handles all requests for a web site 
//https://dev.to/xxzeroxx/php-design-patterns-front-controller-jjo

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/routes.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$database = new App\Kernel\Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
$pdo = $database->connect();
$basePath = dirname($_SERVER['SCRIPT_NAME']);//determine the base path of the application

$customerRepository = new App\Repository\CustomerRepository($pdo);
$customerService = new App\Service\CustomerService($customerRepository);
$view = new App\Kernel\View();
$customerController = new App\Controller\CustomerController($customerService, $view);


$controllers = ['CustomerController' => $customerController];

$router = new App\Kernel\Router($routes, $basePath, $controllers);

$router->dispatch();
