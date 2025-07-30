<?php

namespace App\Controllers;

use Tools\Language;
use Tools\Validator;

abstract class Controller
{
    protected $validator;

    public function __construct()
    {
        session_start();

        Language::setLang(Language::JP);

        $this->validator = new Validator();
    }
}