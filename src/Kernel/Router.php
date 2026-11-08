<?php

namespace App\Kernel;

class Router
{
    private $routes;
    private $basePath;

    public function __construct($routes, $basePath)
    {
        $basePath = rtrim($basePath, '/');
        $this->routes = $routes;
        $this->basePath = $basePath;


    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD']; 
        if ($method == 'POST' && isset($_POST['_method'])) {
            $method = $_POST['_method'];// Override the method if _method is set in POST data
        }
        
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); 
        $uri = substr($uri, strlen($this->basePath)); // Remove the base path from the URI
        $key = $method . ' ' . $uri;

        if (array_key_exists($key, $this->routes)) { 
            $controller = $this->routes[$key]['controller']; 
            $action = $this->routes[$key]['action']; 
            
            echo "Route found: $controller -> $action";
            return;
        }
        
        foreach ($this->routes as $routeKey => $route) { 
            $routeParts = explode(' ', $routeKey); 
            $routeMethod = $routeParts[0]; // Get the HTTP method from the route key
            $routePath = $routeParts[1]; // Get the path from the route key

            if ($routeMethod !== $method) {
                continue;
            }

            $routeSegments = explode('/', $routePath); // Split the route and URI path into segments
            $uriSegments = explode('/', $uri);

            if (count($routeSegments) !== count($uriSegments)) {
                continue;
            }

            $matched = true; 
            $id = null;

            // Check each segment of the route and URI to see if they match
            foreach ($routeSegments as $i => $segment) {
 
                if (!empty($segment) && strpos($segment, '{') === 0) { 
                    $id = $uriSegments[$i];
                } elseif ($segment !== $uriSegments[$i]) {
                    $matched = false;
                    break;
                }
            }

            if ($matched) {
                echo "Matched route: $routeMethod $routePath with ID: $id"; 
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
