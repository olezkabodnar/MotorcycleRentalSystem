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

// Adapter: PHPMailer behind our MailerInterface, settings from .env
$mailer = new App\Mail\PhpMailerAdapter($_ENV['MAIL_HOST'], $_ENV['MAIL_PORT'], $_ENV['MAIL_USERNAME'], $_ENV['MAIL_PASSWORD'], $_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);

// Observer: each listener reacts to one reservation event and sends its email
$dispatcher = new App\Event\EventDispatcher();
$dispatcher->subscribe(App\Event\ReservationCreated::class, new App\Event\SendConfirmationEmail($mailer));
$dispatcher->subscribe(App\Event\ReservationCancelled::class, new App\Event\SendCancellationEmail($mailer));

$reservationRepository = new App\Repository\ReservationRepository($pdo);
$reservationService = new App\Service\ReservationService($reservationRepository, $customerRepository, new App\Pricing\PricingStrategyFactory(), $dispatcher);
$motorcycleRepository = new App\Repository\MotorcycleRepository($pdo);
$extraRepository = new App\Repository\ExtraRepository($pdo);
$reservationController = new App\Controller\ReservationController($reservationService, $customerService, $motorcycleRepository, $extraRepository, $view);

$controllers = [
    'CustomerController' => $customerController,
    'ReservationController' => $reservationController,
];

$router = new App\Kernel\Router($routes, $basePath, $controllers);

$router->dispatch();
