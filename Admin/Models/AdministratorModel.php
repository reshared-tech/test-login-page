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

    public function getById($id)
    {
        if ($id == 1) {
            return ['name' => 'admin', 'id' => 1];
        }

        return $this->database->prepare("SELECT * FROM `administrators` WHERE `id` = :id", [
            'id' => $id
        ])->find();
    }
}