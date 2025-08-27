<?php

namespace Tools;

/**
 * Authentication utility class for managing user sessions
 * Handles user login state, session data, and access control
 */
class Auth
{
    /**
     * Session namespace for storing authentication data
     * @var string
     */
    static $name = 'app';

    /**
     * Set the session namespace for authentication data
     *
     * @param string $name Custom namespace name
     * @return void
     */
    public static function name($name)
    {
        self::$name = $name;
    }

    /**
     * Retrieve user data from the session
     *
     * @param string|null $key Specific user data key to retrieve (e.g., 'id', 'name')
     * @return mixed|null User data array if no key provided, specific value if key exists, empty string or null otherwise
     */
    public static function user($key = null)
    {
        // Get user data from session using the current namespace
        $user = $_SESSION[self::$name]['user'] ?? null;

        // Return null if no user data exists and no specific key is requested
        if (empty($user)) {
            if (empty($key)) {
                return null;
            }
            // Return empty string if specific key is requested but no user data exists
            return '';
        }

        // Return specific key value if requested, otherwise return full user data array
        return $key ? ($user[$key] ?? '') : $user;
    }

    /**
     * Check if a user is currently authenticated (logged in)
     *
     * @return bool True if user is authenticated, false otherwise
     */
    public static function isAuthorized()
    {
        // Return true if user ID exists in session data
        return !empty(self::user('id'));
    }

    /**
     * Store user data in the session (login)
     *
     * @param array $user User data array to store in session
     * @return void
     */
    public static function addAuth($user)
    {
        $_SESSION[self::$name]['user'] = $user;
    }

    /**
     * Update specific fields in the stored user session data
     *
     * @param array $data Associative array of user data fields to update
     * @return void
     */
    public static function updateAuth($data)
    {
        // Update each specified field in the session user data
        foreach ($data as $k => $v) {
            $_SESSION[self::$name]['user'][$k] = $v;
        }
    }

    /**
     * Remove all authentication data from the session (logout)
     *
     * @return void
     */
    public static function removeAuth()
    {
        unset($_SESSION[self::$name]);
    }

    /**
     * Enforce authentication: redirect unauthenticated users to login page
     *
     * @param string $to Target URL to redirect unauthenticated users (default: 'login')
     * @return void
     */
    public static function checkAuth($to = 'login')
    {
        if (!self::isAuthorized()) {
            redirect($to);
        }
    }

    /**
     * Enforce guest status: redirect authenticated users away from public pages (e.g., login/register)
     *
     * @param string $to Target URL to redirect authenticated users (default: '/')
     * @return void
     */
    public static function checkGuest($to = '/')
    {
        if (self::isAuthorized()) {
            // Use base path if default target is '/'
            if ($to === '/') {
                $to = Router::$basePath;
            }
            // Redirect to referrer if available, otherwise to target URL
            redirect($_SERVER['HTTP_REFERER'] ?? $to);
        }
    }
}