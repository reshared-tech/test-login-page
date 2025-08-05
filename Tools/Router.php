<?php

namespace Tools;

class Router
{
    protected $routes = [];
    public static $basePath = '/';

    public function __construct()
    {
        self::$basePath = parse_url(trim(Config::domain, '/'), PHP_URL_PATH);
    }

    protected function parseUri()
    {
        // Parse the request URI to get path segments as an array
        $uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriPath = rtrim(str_replace(self::$basePath, '', $uriPath), '/');
        if ($uriPath === '') {
            $uriPath = '/';
        }
        return $uriPath;
    }

    protected function add($uri, $method, $class)
    {
        $this->routes[$uri][strtoupper($method)] = $class;
    }

    public function get($uri, $class)
    {
        $this->add($uri, 'GET', $class);
    }

    public function post($uri, $class)
    {
        $this->add($uri, 'POST', $class);
    }

    public function put($uri, $class)
    {
        $this->add($uri, 'PUT', $class);
    }

    public function patch($uri, $class)
    {
        $this->add($uri, 'PATCH', $class);
    }

    public function delete($uri, $class)
    {
        $this->add($uri, 'DELETE', $class);
    }

    /**
     * Route Parsing and Controller Dispatching
     *
     * 1. Parse the request URI to get the path segments
     * 2. Determine the namespace (Admin or App) based on the path
     * 3. Determine the controller class name
     * 4. Instantiate the controller and call the handler method
     */
    public function dispatch()
    {
        $uriPath = $this->parseUri();

        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        if (isset($this->routes[$uriPath][$method])) {
            try {
                $class = $this->routes[$uriPath][$method][0];

                call_user_func([new $class(), $this->routes[$uriPath][$method][1]]);
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        } else {
            require APP_ROOT . '/views/errors/404.view.php';
        }
    }
}