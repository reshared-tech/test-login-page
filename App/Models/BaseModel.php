<?php

namespace App\Models;

use App\Tools\Database;

abstract class BaseModel
{
    protected $database;

    public function __construct()
    {
        $this->database = new Database();
    }
}