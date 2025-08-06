<?php

/**
 * Dump and die - Output variables and terminate execution
 *
 * @param mixed ...$vars Variables to dump
 */
function dd(...$vars): void
{
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    exit;
}

/**
 * Translate a language key
 *
 * @param string $key Translation key
 * @return string Translated text
 */
function __(string $key): string
{
    return Tools\Language::show($key);
}

/**
 * Calculate pagination data
 *
 * @param int $total Total items count
 * @param int $currentPage Current page number
 * @param int $size Items per page (default: 10)
 * @param int $show Maximum page numbers to show (default: 7)
 * @return array [previous page, next page, page numbers array]
 */
function calcPages(int $total, int $currentPage, int $size = 10, int $show = 7): array
{
    $totalPages = max(1, ceil($total / $size));  // Ensure at least 1 page
    $pre = $currentPage <= 1 ? 0 : $currentPage - 1;
    $next = $currentPage >= $totalPages ? 0 : $currentPage + 1;

    // Simple case when total pages <= show pages
    if ($totalPages <= $show) {
        return [$pre, $next, range(1, $totalPages)];
    }

    $half = floor($show / 2);
    $left = max(1, $currentPage - $half);  // Ensure doesn't go below 1
    $right = min($totalPages, $currentPage + $half);  // Ensure doesn't exceed total pages

    // Adjust window if near boundaries
    if ($left === 1) {
        $right = $show;
    } elseif ($right === $totalPages) {
        $left = max(1, $totalPages - $show + 1);
    }

    $pages = range($left, $right);

    // Add first page and ellipsis if needed
    if ($left > 1) {
        array_unshift($pages, $left > 2 ? '...' : 1, 1);
    }

    // Add last page and ellipsis if needed
    if ($right < $totalPages) {
        array_push($pages, $right < $totalPages - 1 ? '...' : $totalPages, $totalPages);
    }

    return [$pre, $next, array_unique($pages)];  // Remove potential duplicates
}

/**
 * Render a view template
 *
 * @param string $path View path (dots converted to slashes)
 * @param array $vars Variables to extract
 */
function view(string $path, array $vars = []): void
{
    $path = str_replace('.', '/', $path);

    // Automatically handle pagination if these vars exist
    if (isset($vars['page'], $vars['size'], $vars['total'])) {
        [$vars['pre'], $vars['next'], $vars['pages']] = calcPages(
            $vars['total'],
            $vars['page'],
            $vars['size']
        );
    }

    extract($vars);
    require APP_ROOT . '/views/' . $path . '.view.php';
}

/**
 * Generate page URL for pagination
 *
 * @param int $p Page number
 * @return string Generated URL
 */
function pageUrl(int $p): string
{
    $url = parse_url($_SERVER['REQUEST_URI']);
    parse_str($url['query'] ?? '', $params);
    $params['page'] = $p;
    return $url['path'] . '?' . http_build_query($params);
}

/**
 * Generate base HTML tag with path information
 *
 * @return string Base tag HTML or empty string
 */
function base_tag(): string
{
    if (empty(\Tools\Config::domain) || \Tools\Config::domain === '/') {
        return '';
    }
    $path = trim(\Tools\Config::domain, '/') . '/';
    return "<base href=\"{$path}\"><script>const base_path=\"{$path}\"</script>";
}

/**
 * Redirect to another URL
 *
 * @param string $to Redirect destination (default: '/')
 */
function redirect(string $to = '/'): void
{
    header('Location: ' . \Tools\Router::$basePath . $to);
    exit;
}

/**
 * Output JSON response and terminate
 *
 * @param array $data Data to encode as JSON
 */
function json(array $data): void
{
    header('Content-Type: application/json');
    if (isset($data['message'])) {
        $data['message'] = \Tools\Language::show($data['message']);
    }
    exit(json_encode($data));
}

/**
 * Get JSON data from request body
 *
 * @return array Decoded JSON data
 */
function jsonData(): array
{
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

/**
 * Generate a random UUID
 *
 * @param int $length Desired length (default: 16)
 * @return string Generated UUID
 * @throws \Random\RandomException
 */
function uuid(int $length = 16): string
{
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

/**
 * Check if user is authorized
 *
 * @return bool Authorization status
 */
function isAuthorized(): bool
{
    return \Tools\Auth::isAuthorized();
}

/**
 * Get authorized user data
 *
 * @param string|null $key Specific user data key to retrieve
 * @return mixed User data or specific value
 */
function authorizedUser(?string $key = null)
{
    return \Tools\Auth::user($key);
}