<?php

/**
 * Dump and die - Output variables in human-readable format and terminate script execution
 * Useful for debugging to inspect variable values without continuing code flow
 *
 * @param mixed ...$vars Variable number of variables to dump (supports any data type)
 */
function dd(...$vars): void
{
    // Use <pre> tag to preserve whitespace and formatting for readability
    echo '<pre>';
    // Iterate through each variable and dump its structure/value
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    // Terminate script execution immediately after dumping
    exit;
}

/**
 * Translate a language key using the Language utility class
 * Wrapper function for easy access to localization functionality
 *
 * @param string $key Translation key (matches keys in Language::Languages array)
 * @return string Translated text (or original key if no translation exists)
 */
function __(string $key): string
{
    return Tools\Language::show($key);
}

/**
 * Calculate pagination data for generating pagination UI (e.g., page numbers, prev/next buttons)
 * Handles edge cases like small total pages, boundary pages, and truncated page lists
 *
 * @param int $total Total number of items to paginate
 * @param int $currentPage Current active page number
 * @param int $size Number of items to display per page (default: 10)
 * @param int $show Maximum number of page numbers to show in the UI (default: 7)
 * @return array Pagination data:
 *               - [0]: Previous page number (0 if on first page)
 *               - [1]: Next page number (0 if on last page)
 *               - [2]: Array of page numbers/ellipsis (e.g., [1, '...', 5, 6, 7, '...', 10])
 */
function calcPages(int $total, int $currentPage, int $size = 10, int $show = 7): array
{
    // Calculate total pages (ensure at least 1 page even if total items = 0)
    $totalPages = max(1, ceil($total / $size));
    // Determine previous page (0 if current page is 1 or less)
    $pre = $currentPage <= 1 ? 0 : $currentPage - 1;
    // Determine next page (0 if current page is total pages or more)
    $next = $currentPage >= $totalPages ? 0 : $currentPage + 1;

    // Simple case: if total pages ≤ max pages to show, return all page numbers
    if ($totalPages <= $show) {
        return [$pre, $next, range(1, $totalPages)];
    }

    // Complex case: truncate page list to avoid too many numbers
    $half = floor($show / 2);  // Half the number of pages to show (for centering current page)
    // Calculate left boundary (ensure doesn't go below 1)
    $left = max(1, $currentPage - $half);
    // Calculate right boundary (ensure doesn't exceed total pages)
    $right = min($totalPages, $currentPage + $half);

    // Adjust boundaries if current page is near the start or end
    if ($left === 1) {
        // If left boundary is 1, set right boundary to max pages to show
        $right = $show;
    } elseif ($right === $totalPages) {
        // If right boundary is total pages, set left boundary to show full range
        $left = max(1, $totalPages - $show + 1);
    }

    // Generate range of page numbers between left and right boundaries
    $pages = range($left, $right);

    // Add first page and ellipsis if there's a gap between 1 and left boundary
    if ($left > 1) {
        // Add ellipsis only if gap is more than 1 (e.g., 1 ... 5 instead of 1 2 5)
        array_unshift($pages, $left > 2 ? '...' : 1, 1);
    }

    // Add last page and ellipsis if there's a gap between right boundary and total pages
    if ($right < $totalPages) {
        // Add ellipsis only if gap is more than 1 (e.g., 8 ... 10 instead of 8 9 10)
        array_push($pages, $right < $totalPages - 1 ? '...' : $totalPages, $totalPages);
    }

    // Remove duplicate values (e.g., if ellipsis and page number overlap)
    return [$pre, $next, array_unique($pages)];
}

/**
 * Render a view template from the views directory
 * Automatically handles pagination data if required variables exist
 *
 * @param string $path View path (uses dots for directory separation, e.g., "admin.dashboard" → "admin/dashboard")
 * @param array $vars Associative array of variables to pass to the view (extracted as global variables)
 */
function view(string $path, array $vars = []): void
{
    // Convert dot-separated path to slash-separated (for directory navigation)
    $path = str_replace('.', '/', $path);

    // Auto-generate pagination data if "page", "size", and "total" are in $vars
    if (isset($vars['page'], $vars['size'], $vars['total'])) {
        [$vars['pre'], $vars['next'], $vars['pages']] = calcPages(
            $vars['total'],
            $vars['page'],
            $vars['size']
        );
    }

    // Extract variables from $vars (makes them accessible as $key = $value in the view)
    extract($vars);
    // Require the view file (APP_ROOT is the application's root directory constant)
    require APP_ROOT . '/views/' . $path . '.view.php';
}

/**
 * Generate a URL for a specific page number (preserves existing query parameters)
 * Used for pagination links to navigate between pages
 *
 * @param int $p Target page number to generate the URL for
 * @return string URL with the specified page number (and other existing query params)
 */
function pageUrl(int $p): string
{
    // Parse the current request URL to extract path and query parameters
    $url = parse_url($_SERVER['REQUEST_URI']);
    // Parse existing query parameters into an array (empty if no query string)
    parse_str($url['query'] ?? '', $params);
    // Update the "page" parameter to the target page number
    $params['page'] = $p;
    // Rebuild the URL with the updated query parameters
    return $url['path'] . '?' . http_build_query($params);
}

/**
 * Generate an HTML <base> tag and JavaScript base path variable
 * Ensures relative URLs in the app resolve correctly to the application's base domain
 *
 * @return string HTML <base> tag + script (or empty string if domain is invalid)
 */
function base_tag(): string
{
    // Return empty string if domain is empty or root (no base tag needed)
    if (empty(\Tools\Config::domain) || \Tools\Config::domain === '/') {
        return '';
    }
    // Trim trailing slashes from domain and add one back (ensures consistent path format)
    $path = trim(\Tools\Config::domain, '/') . '/';
    // Return <base> tag (for HTML relative URLs) and script (for JS relative paths)
    return "<base href=\"{$path}\"><script>const base_path=\"{$path}\"</script>";
}

/**
 * Redirect the user to another URL (handles application's base path automatically)
 * Terminates script execution after sending the redirect header
 *
 * @param string $to Target URL or path to redirect to (default: '/', the application root)
 */
function redirect(string $to = '/'): void
{
    // Combine base path and target path, replacing double slashes with single (avoids invalid URLs)
    $to = str_replace('//', '/', \Tools\Router::$basePath . $to);

    // Send HTTP redirect header
    header('Location: ' . $to);
    // Terminate script execution to prevent further code from running
    exit;
}

/**
 * Output a JSON response and terminate script execution
 * Used for AJAX endpoints to return structured data to the frontend
 *
 * @param array $data Associative array of data to encode as JSON
 */
function json(array $data): void
{
    // Set correct Content-Type header for JSON responses
    header('Content-Type: application/json');
    // Encode data to JSON and exit (ensures no extra output)
    exit(json_encode($data));
}

/**
 * Read and decode JSON data from the request body
 * Used to parse JSON payloads from AJAX POST/PUT requests
 *
 * @return array Decoded JSON data (empty array if JSON is invalid or missing)
 */
function jsonData(): array
{
    // Read raw input from php://input (where JSON payloads are stored)
    // Decode JSON to associative array; return empty array on failure
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

/**
 * Generate a cryptographically secure random UUID (Universally Unique Identifier)
 * Uses PHP's random_bytes() for secure randomness
 *
 * @param int $length Desired length of the UUID (default: 16 characters)
 * @return string Generated UUID (hexadecimal string)
 * @throws \Random\RandomException If secure random bytes cannot be generated
 */
function uuid(int $length = 16): string
{
    // Generate secure random bytes, convert to hexadecimal, and truncate to desired length
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

/**
 * Check if the current user is authorized (logged in)
 * Wrapper function for easy access to Auth::isAuthorized()
 *
 * @return bool True if user is logged in, false otherwise
 */
function isAuthorized(): bool
{
    return \Tools\Auth::isAuthorized();
}

/**
 * Get data for the currently authorized (logged in) user
 * Wrapper function for easy access to Auth::user()
 *
 * @param string|null $key Specific user data key to retrieve (e.g., 'id', 'name')
 *                         If null, returns the full user data array
 * @return mixed User data value for the key, full user array, or empty string/null if not found
 */
function authorizedUser(?string $key = null)
{
    return \Tools\Auth::user($key);
}

/**
 * Convert a timestamp to a human-readable relative time string
 * Examples: "Just now", "5 Mins ago", "14:30", "Yesterday 09:15", "06-20 18", "2024-03-15"
 *
 * @param int|string $time Timestamp (numeric) or datetime string (e.g., "2024-05-20 14:30:00")
 * @return string Human-readable time string
 */
function timeHuman($time)
{
    $now = time();  // Current Unix timestamp
    $today = strtotime(date('Y-m-d 00:00:00'));  // Unix timestamp for start of today
    // Convert $time to Unix timestamp if it's a string
    $time = is_numeric($time) ? $time : strtotime($time);

    // Handle times from today
    if ($time >= $today) {
        $diff = $now - $time;
        // Less than 60 seconds ago → "Just now"
        if ($diff < 60) {
            return 'Just now';
        }
        // Less than 1 hour ago → "X Mins ago"
        if ($diff < 3600) {
            return ceil($diff / 60) . ' Mins ago';
        }
        // Today but more than 1 hour ago → "HH:MM" (24-hour format)
        return date('H:i', $time);
    }

    // Handle times from yesterday
    $yesterday = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day')));
    if ($time >= $yesterday) {
        return 'Yesterday ' . date('H:i', $time);
    }

    // Handle times from this year (but not today/yesterday)
    $thisYear = strtotime(date('Y-01-01 00:00:00'));
    if ($time >= $thisYear) {
        return date('m-d H', $time);  // "MM-DD HH" (24-hour format)
    }

    // Handle times from previous years → "YYYY-MM-DD"
    return date('Y-m-d');
}

/**
 * Generate a text avatar from a user's name (e.g., "John Doe" → "JD", "田中" → "田")
 * Supports both Latin and UTF-8 characters (e.g., Japanese, Chinese)
 *
 * @param string $name User's full name (may contain spaces for multi-part names)
 * @return string Text avatar (1-2 characters) or "-" if name is empty
 */
function nameAvatar($name)
{
    // Return "-" if name is empty
    if (empty($name)) {
        return '-';
    }
    // Split name into parts (max 2 parts, e.g., first name and last name)
    $parts = explode(' ', $name, 2);
    $res = [];

    // Extract first character from each name part
    foreach ($parts as $part) {
        // Get first UTF-8 character (supports non-Latin scripts like Japanese)
        $letter = mb_substr($part, 0, 1, 'utf8');
        // Convert Latin lowercase letters to uppercase (e.g., "john" → "J")
        if (preg_match('/^[a-z]+$/', $letter) === 1) {
            $letter = strtoupper($letter);
        }
        $res[] = $letter;
    }

    // Combine characters to form the avatar (e.g., ["J", "D"] → "JD")
    return implode('', $res);
}