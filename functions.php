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
    if (isset($data['message'])) {
        $data['message'] = \Tools\Language::show($data['message']);
    }
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

ffmpeg -i 1.mov -vf "scale=1280:-2,fps=30" -c:v libx264 -crf 23 -c:a aac 1.mp4
ffmpeg -i 2.mov -vf "scale=1280:-2,fps=30" -c:v libx264 -crf 23 -c:a aac 2.mp4
ffmpeg -i 3.mov -vf "scale=1280:-2,fps=30" -c:v libx264 -crf 23 -c:a aac 3.mp4
ffmpeg -i 4.mov -vf "scale=1280:-2,fps=30" -c:v libx264 -crf 23 -c:a aac 4.mp4
ffmpeg -i 5.mov -vf "scale=1280:-2,fps=30" -c:v libx264 -crf 23 -c:a aac 5.mp4
ffmpeg -i 6.mov -vf "scale=1280:-2,fps=30" -c:v libx264 -crf 23 -c:a aac 6.mp4
ffmpeg -i 7.mov -vf "scale=1280:-2,fps=30" -c:v libx264 -crf 23 -c:a aac 7.mp4
ffmpeg -i 8.mov -vf "scale=1280:-2,fps=30" -c:v libx264 -crf 23 -c:a aac 8.mp4
ffmpeg -i 9.mov -vf "scale=1280:-2,fps=30" -c:v libx264 -crf 23 -c:a aac 9.mp4
ffmpeg -i 10.mov -vf "scale=1280:-2,fps=30" -c:v libx264 -crf 23 -c:a aac 10.mp4
