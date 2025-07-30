<?php

namespace Admin\Controllers;

use Admin\Models\AdministratorModel;
use Tools\Auth;
use Tools\Validator;

class Controller
{
    protected $validator;

    public function __construct()
    {
        session_start();

        Auth::name('admin');

        $this->checkAuth();

        $this->validator = new Validator();
    }

    private function checkAuth()
    {
        if (! Auth::isAuthorized()) {
            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                $this->forbidden();
            }

            $name = trim($_SERVER['PHP_AUTH_USER']);
            $password = trim($_SERVER['PHP_AUTH_PW']);

            $model = new AdministratorModel();
            $data = $model->getByName($name);

            if (empty($data)) {
                $this->forbidden();
            }

            if (! password_verify($password, $data['password'])) {
                $this->forbidden();
            }

            Auth::addAuth([
                'id' => $data['id'],
                'name' => $data['name'],
            ]);
        }
    }

    private function forbidden()
    {
        header('WWW-Authenticate: Basic realm="Please log in"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    }
}