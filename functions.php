<?php

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
    return Tools\Language::show($key);
}

function view($path, $vars = [])
{
    $path = str_replace('.', '/', $path);

    extract($vars);

    require APP_ROOT . '/views/' . $path . '.view.php';
}

function redirect($to = '/')
{
    header('Location: ' . $to);
    exit;
}

function json($data)
{
    header('Content-Type: application/json');
    exit(json_encode($data));
}