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

function calcPages($total, $currentPage, $size = 10, $show = 7)
{
    $totalPages = ceil($total / $size);
    $pre = $currentPage == 1 ? 0 : $currentPage - 1;
    $next = $currentPage == $totalPages ? 0 : $currentPage + 1;

    if ($totalPages <= $show) {
        return [$pre, $next, range(1, $totalPages)];
    }

    $half = floor($show / 2);
    $left = $currentPage - $half;
    $right = $currentPage + $half;

    if ($left < 1) {
        $left = 1;
        $right = $show;
    }

    if ($right > $totalPages) {
        $right = $totalPages;
        $left = $totalPages - $show + 1;
    }

    $pages = range($left, $right);

    if ($left > 1) {
        if ($left > 2) {
            array_unshift($pages, '...');
        }
        array_unshift($pages, 1);
    }

    if ($right < $totalPages) {
        if ($right < $totalPages - 1) {
            $pages[] = '...';
        }
        $pages[] = $totalPages;
    }

    return [$pre, $next, $pages];
}

function view($path, $vars = [])
{
    $path = str_replace('.', '/', $path);

    if (isset($vars['page']) && isset($vars['size']) && isset($vars['total'])) {
        [$pre, $next, $pages] = calcPages($vars['total'], $vars['page'], $vars['size']);
        $vars['pre'] = $pre;
        $vars['next'] = $next;
        $vars['pages'] = $pages;
    }

    extract($vars);

    require APP_ROOT . '/views/' . $path . '.view.php';
}

function pageUrl($p)
{
    $current = $_SERVER['REQUEST_URI'];
    if (strpos($current, '?') !== false) {
        return $current . '&page=' . $p;
    }

    return $current . '?page=' . $p;
}

function base_tag()
{
    if (empty(\Tools\Config::domain) || \Tools\Config::domain === '/') {
        return '';
    }
    $path = trim(\Tools\Config::domain, '/') . '/';
    return "<base href=\"{$path}\"><script>const base_path=\"{$path}\"</script>";
}

function redirect($to = '/')
{
    header('Location: ' . \Tools\Router::$basePath . $to);
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