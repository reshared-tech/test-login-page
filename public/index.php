<?php
define('APP_ROOT', dirname(__DIR__));

spl_autoload_register(function ($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require APP_ROOT . '/' . $path . '.php';
});

function dd(...$vars)
{
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    exit;
}

function __($key)
{
    return \App\Tools\Language::show($key);
}

$path = explode('/', parse_url($_SERVER['REQUEST_URI'])['path']);
$controller = $path[1] ? ucfirst($path[1]) : 'Home';
$class = '\App\Controllers\\' . $controller . 'Controller';
$instance = class_exists($class) ? new $class() : new \App\Controllers\HomeController();
$instance->handle($path[2] ?? 'index');