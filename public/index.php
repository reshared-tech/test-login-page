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

\App\Tools\Language::setLang(\App\Tools\Language::JP);

(new \App\Controllers\AuthController)->handle();