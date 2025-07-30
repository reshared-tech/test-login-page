<?php

namespace Admin\Models;

class AdministratorModel extends BaseModel
{
    protected $table = 'administrators';

    public function getByName($name)
    {
        return $this->database->prepare("SELECT * FROM `{$this->table}` WHERE `name` = :name", [
            'name' => $name
        ])->find();
    }
}