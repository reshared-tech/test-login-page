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
            $this->view(500, ['message' => $e->getMessage()]);
        }
    }

    protected function string($key, $min = 0, $max = 255)
    {
        $val = trim($_POST[$key]);

        $len = strlen($val);

        if ($len <= $min) {
            $this->errors[$key] = "$key must be at least $min characters";
        }
        if ($len > $max) {
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

    protected function removeAuth()
    {
        session_destroy();
    }
}