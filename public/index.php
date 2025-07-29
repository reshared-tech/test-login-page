<?php
define('APP_ROOT', dirname(__DIR__));

spl_autoload_register(function ($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require $path;
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

$controller = (new \App\Controllers\AuthController)->handle();