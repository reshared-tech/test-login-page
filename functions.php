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

function jsonData()
{
    return json_decode(file_get_contents('php://input'), true);
}

/**
 * @throws \Random\RandomException
 */
function uuid($length = 16)
{
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

function isAuthorized()
{
    return \Tools\Auth::isAuthorized();
}

function authorizedUser($key = null)
{
    return \Tools\Auth::user($key);
}