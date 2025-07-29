<?php

namespace App\Controllers;

abstract class Controller
{
    public function handle()
    {
        $action = $_GET['action'] ?: 'index';

        try {
            call_user_func([$this, $action]);
        } catch (\Exception|\Throwable $e) {
            $this->view(500, ['message' => $e->getMessage()]);
        }
    }

    protected function string($key, $min = 0, $max = 255)
    {

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
}