<?php

namespace Tools;

class Router
{
    protected $routes = [];
    public static $basePath = '/';

    public function __construct()
    {
        $path = parse_url(trim(Config::domain, '/'), PHP_URL_PATH);
        if ($path) {
            self::$basePath = $path . '/';
        }
    }

    protected function parseUri()
    {
        // Parse the request URI to get path segments as an array
        $uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (self::$basePath !== '/') {
            $uriPath = '/' . trim(str_replace(self::$basePath, '', $uriPath), '/');
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
            $this->call($this->routes[$uriPath][$method][0], $this->routes[$uriPath][$method][1]);
        } else {
            foreach ($this->routes as $path => $route) {
                if (!isset($route[$method]) || strpos($path, '/:') === false) {
                    continue;
                }
                preg_match_all('/\/(:\w+)/s', $path, $matches);
                $pattern = str_replace($matches[1], '([\w|\d]+)', $path);
                $pattern = str_replace('/', '\/', $pattern);
                preg_match("/$pattern/s", $uriPath, $res);
                if (!$res || $res[0] != $uriPath) {
                    continue;
                }
                $params = [];
                foreach ($matches[1] as $n => $param) {
                    if (isset($res[$n + 1])) {
                        $params[trim($param, ':')] = $res[$n + 1];
                    }
                }
                $this->call($route[$method][0], $route[$method][1], $params);
                exit;
            }

            require APP_ROOT . '/views/errors/404.view.php';
        }
    }

    private function call($class, $method, $params = [])
    {
        try {
            call_user_func_array([new $class(), $method], $params);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}