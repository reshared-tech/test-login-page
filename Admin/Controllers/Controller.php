<?php

namespace Admin\Controllers;

use Tools\Auth;
use Tools\Validator;

class Controller
{
    protected $validator;

    public function __construct()
    {
        session_start();

        Auth::name('admin');

        Auth::checkAuth();

        $this->validator = new Validator();
    }
}