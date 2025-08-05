<?php

namespace Admin\Models;

use App\Models\BaseModel;

class AdministratorModel extends BaseModel
{
    public function getByName($name)
    {
        return $this->database->prepare("SELECT * FROM `administrators` WHERE `name` = :name", [
            'name' => $name
        ])->find();
    }
}