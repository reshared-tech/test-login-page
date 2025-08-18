<?php

namespace Tools;

class Auth
{
    static $name = 'app';

    public static function name($name)
    {
        self::$name = $name;
    }

    public static function user($key = null)
    {
        $user = $_SESSION[self::$name]['user'] ?? null;
        if (empty($user)) {
            if (empty($key)) {
                return null;
            }

            return '';
        }

        return $key ? ($user[$key] ?? '') : $user;
    }

    public static function isAuthorized()
    {
        return !empty(self::user('id'));
    }

    public static function addAuth($user)
    {
        $_SESSION[self::$name]['user'] = $user;
    }

    public static function updateAuth($data)
    {
        foreach ($data as $k => $v) {
            $_SESSION[self::$name]['user'][$k] = $v;
        }
    }

    public static function removeAuth()
    {
        unset($_SESSION[self::$name]);
    }

    public static function checkAuth($to = 'login')
    {
        if (!self::isAuthorized()) {
            redirect($to);
        }
    }

    public static function checkGuest($to = '/')
    {
        if (self::isAuthorized()) {
            if ($to === '/') {
                $to = Router::$basePath;
            }
            redirect($_SERVER['HTTP_REFERER'] ?? $to);
        }
    }
}