<?php

namespace App\Controllers;

abstract class Controller
{
    protected $errors = [];

    public function __construct()
    {
        session_start();
    }

    public function handle()
    {
        $action = $_GET['action'] ?? 'index';

        try {
            call_user_func([$this, $action]);
        } catch (\Exception|\Throwable $e) {
            $this->json([
                'code' => 10005,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function string($key, $min = 1, $max = 255)
    {
        $val = trim($_POST[$key]);

        $len = strlen($val);

        if ($len === 0) {
            $this->errors[$key] = "Please input $key";
        } elseif ($len < $min) {
            $this->errors[$key] = "$key must be at least $min characters";
        } elseif ($len > $max) {
            $this->errors[$key] = "$key must be less than $max characters";
        }

        if ($this->hasError()) {
            return null;
        }

        return $val;
    }

    protected function json($data)
    {
        header('Content-Type: application/json');
        exit(json_encode($data));
    }

    protected function view($path, $vars = [])
    {
        extract($vars);

        require APP_ROOT . '/views/' . $path . '.view.php';
    }

    protected function hasError()
    {
        return !empty($this->errors);
    }

    protected function addAuth($user)
    {
        $_SESSION['user'] = $user;
    }

    protected function isAuthorized()
    {
        return isset($_SESSION['user']);
    }

    protected function removeAuth()
    {
        session_destroy();
    }

    protected function redirect($to = '/')
    {
        header('Location: ' . $to);
    }
}