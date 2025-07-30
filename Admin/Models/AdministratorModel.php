<?php

namespace Admin\Models;

class AdministratorModel extends BaseModel
{
    protected $table = 'administrators';

    public function getByEmail($email)
    {
        return $this->database->prepare("SELECT * FROM `{$this->table}` WHERE `email` = :email", [
            'email' => $email
        ])->find();
    }
}