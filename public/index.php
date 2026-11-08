<?php
// front controller, controller that handles all requests for a web site 
//https://dev.to/xxzeroxx/php-design-patterns-front-controller-jjo

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require_once __DIR__ . '/../config/routes.php';


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = dirname($_SERVER['SCRIPT_NAME']);
$uri = substr($uri, strlen($basePath));



if (array_key_exists($uri, $routes)) {
    $controller = $routes[$uri]['controller'];
    $action = $routes[$uri]['action'];
    

    echo "<br>" . "Route found: $controller -> $action";


} else {
    http_response_code(404);
    echo "404 Not Found";
}

