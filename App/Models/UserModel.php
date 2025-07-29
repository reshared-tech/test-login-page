<?php

namespace App\Models;

use App\Tools\Utils;

class UserModel extends BaseModel
{
    public function getUserByEmail($email)
    {
        return $this->database->prepare('SELECT * FROM `users` WHERE `email` = :email', [
            'email' => $email
        ])->find();
    }

    public function addUserLog($userId, $note)
    {
        return $this->database->prepare('INSERT INTO `user_logs`(`user_id`, `user_agent`, `note`, `ip`, `created_at`) VALUES(:user_id, :user_agent, :note, :ip, created_at)', [
            'user_id' => $userId,
            'user_agent' => Utils::getRequestAgent(),
            'ip' => Utils::getRequestIp(),
            'note' => $note,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function cleanFailedCount($userId)
    {
        return $this->database->prepare('UPDATE users SET `failed_count` = 0, `updated_at` = :now WHERE `id` = :id', [
            'id' => $userId,
            'now' => date('Y-m-d H:i:s'),
        ]);
    }

    public function incrementFailedCount($userId)
    {
        return $this->database->prepare('UPDATE users SET `failed_count` = `failed_count` + 1, `updated_at` = :now WHERE `id` = :id', [
            'id' => $userId,
            'now' => date('Y-m-d H:i:s'),
        ]);
    }

    public function addUser($name, $email, $password)
    {
        $res = $this->database->prepare('INSERT INTO users(`name`, `email`, `password`, `created_at`) VALUES(:name, :email, :password, :now)', [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'now' => date('Y-m-d H:i:s'),
        ]);
        if (!$res) {
            return false;
        }
        return $this->database->lastId();
    }
}