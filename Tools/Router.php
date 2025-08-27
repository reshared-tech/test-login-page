<?php

namespace Tools;

use Exception;

/**
 * Routing utility class for handling HTTP requests
 * Maps request URIs and methods to corresponding controller classes/methods
 * Supports namespace detection (Admin/App) and dynamic route parameters
 */
class Router
{
    /**
     * Stores registered routes: [URI => [HTTP_METHOD => [ControllerClass, MethodName]]]
     * @var array
     */
    protected $routes = [];

    /**
     * Base path of the application (derived from Config::domain)
     * Used to resolve relative URIs
     * @var string
     */
    public static $basePath = '/';

    /**
     * Router constructor: Initialize base path from application domain
     * Extracts path segment from Config::domain to set correct base path
     */
    public function __construct()
    {
        // Parse path segment from the configured domain (trim trailing slashes first)
        $path = parse_url(trim(Config::domain, '/'), PHP_URL_PATH);
        // Set base path (add trailing slash for consistent URI matching) if path exists
        if ($path) {
            self::$basePath = $path . '/';
        }
    }

    /**
     * Parse the request URI to get the relative path (excluding base path)
     * Normalizes URI to ensure consistent matching against registered routes
     *
     * @return string Normalized relative URI path
     */
    protected function parseUri()
    {
        // Extract the path segment from the full request URI
        $uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove base path from the request URI (if base path is not root)
        if (self::$basePath !== '/') {
            $uriPath = '/' . trim(str_replace(self::$basePath, '', $uriPath), '/');
        }

        return $uriPath;
    }

    /**
     * Register a route with a specific URI, HTTP method, and controller mapping
     * Internal helper method used by HTTP verb-specific methods (get(), post(), etc.)
     *
     * @param string $uri Route URI pattern (e.g., '/login', '/chat/:id')
     * @param string $method HTTP method (GET, POST, PUT, etc.)
     * @param array $class Controller and method mapping [ControllerClass, MethodName]
     * @return void
     */
    protected function add($uri, $method, $class)
    {
        // Store route with uppercase method (ensures case-insensitive method matching)
        $this->routes[$uri][strtoupper($method)] = $class;
    }

    /**
     * Register a GET route
     *
     * @param string $uri Route URI pattern
     * @param array $class Controller and method mapping [ControllerClass, MethodName]
     * @return void
     */
    public function get($uri, $class)
    {
        $this->add($uri, 'GET', $class);
    }

    /**
     * Register a POST route
     *
     * @param string $uri Route URI pattern
     * @param array $class Controller and method mapping [ControllerClass, MethodName]
     * @return void
     */
    public function post($uri, $class)
    {
        $this->add($uri, 'POST', $class);
    }

    /**
     * Register a PUT route
     *
     * @param string $uri Route URI pattern
     * @param array $class Controller and method mapping [ControllerClass, MethodName]
     * @return void
     */
    public function put($uri, $class)
    {
        $this->add($uri, 'PUT', $class);
    }

    /**
     * Register a PATCH route
     *
     * @param string $uri Route URI pattern
     * @param array $class Controller and method mapping [ControllerClass, MethodName]
     * @return void
     */
    public function patch($uri, $class)
    {
        $this->add($uri, 'PATCH', $class);
    }

    /**
     * Register a DELETE route
     *
     * @param string $uri Route URI pattern
     * @param array $class Controller and method mapping [ControllerClass, MethodName]
     * @return void
     */
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
        // Get normalized relative URI path from request
        $uriPath = $this->parseUri();
        // Get uppercase HTTP method (ensures case-insensitive matching)
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        // First, check for exact match between parsed URI and registered routes
        if (isset($this->routes[$uriPath][$method])) {
            // Call the corresponding controller method for exact match
            $this->call(
                $this->routes[$uriPath][$method][0],  // Controller class
                $this->routes[$uriPath][$method][1]   // Controller method
            );
        } else {
            // If no exact match, check for dynamic routes (with parameters like :id)
            foreach ($this->routes as $path => $route) {
                // Skip routes that don't match the HTTP method or have no dynamic parameters
                if (!isset($route[$method]) || strpos($path, '/:') === false) {
                    continue;
                }

                // Extract dynamic parameters (e.g., :id) from the route pattern
                preg_match_all('/\/(:\w+)/s', $path, $matches);
                // Convert route pattern to regex (replace :param with ([\w|\d]+) to match alphanumeric values)
                $pattern = str_replace($matches[1], '([\w|\d]+)', $path);
                // Escape slashes in regex pattern for proper matching
                $pattern = str_replace('/', '\/', $pattern);
                // Check if parsed URI matches the dynamic route regex
                preg_match("/$pattern/s", $uriPath, $res);

                // Skip if no match or if the full match doesn't equal the parsed URI
                if (!$res || $res[0] != $uriPath) {
                    continue;
                }

                // Extract dynamic parameter values from regex matches
                $params = [];
                foreach ($matches[1] as $n => $param) {
                    // Map parameter name (without :) to its value (from regex match groups)
                    if (isset($res[$n + 1])) {
                        $params[trim($param, ':')] = $res[$n + 1];
                    }
                }

                // Call the corresponding controller method with dynamic parameters
                $this->call(
                    $route[$method][0],  // Controller class
                    $route[$method][1],  // Controller method
                    $params              // Dynamic parameters (e.g., ['id' => 123])
                );
                // Exit after dispatching to prevent further processing
                exit;
            }

            // If no routes match, load and display 404 error page
            require APP_ROOT . '/views/errors/404.view.php';
        }
    }

    /**
     * Instantiate a controller and call its method with optional parameters
     * Handles exceptions thrown during controller/method execution
     *
     * @param string $class Controller class name (with namespace)
     * @param string $method Controller method name to call
     * @param array $params Optional parameters to pass to the method
     * @return void
     */
    private function call($class, $method, $params = [])
    {
        try {
            // Create controller instance and call the method with parameters
            // call_user_func_array handles variable-length parameter lists
            call_user_func_array([new $class(), $method], $params);
        } catch (Exception $e) {
            // Dump exception message (for debugging; replace with proper error handling in production)
            dd($e->getMessage());
        }
    }
}