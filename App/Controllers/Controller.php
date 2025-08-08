<?php

namespace App\Controllers;

use Tools\Auth;
use Tools\Language;
use Tools\Validator;

abstract class Controller
{
    protected $checkAuth = true;
    protected $validator;

    public function __construct()
    {
        session_start();

        Language::setLang(Language::JP);

        $this->validator = new Validator();

        if ($this->checkAuth) {
            Auth::checkAuth();
        }
    }
}