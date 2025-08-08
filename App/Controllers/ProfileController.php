<?php

namespace App\Controllers;

use App\Models\UserModel;
use Tools\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = authorizedUser();

        view('profile', [
            'title' => 'Profile',
            'user' => $user,
        ]);
    }

    public function saveProfile()
    {
        $name = $this->validator->string($_POST, 'name');
        $email = $this->validator->email($_POST, 'email');

        $current = authorizedUser();
        $update = [];
        if ($current['name'] != $name) {
            $update['name'] = $name;
        }
        if ($current['email'] != $email) {
            $update['email'] = $email;
        }
        if (empty($update)) {
            json([
                'code' => 10000,
                'message' => 'no profile info change'
            ]);
        }

        $model = new UserModel();
        if (isset($update['email']) && $model->getUserByEmail($email)) {
            json([
                'code' => 10002,
                'message' => 'Email already registered.',
            ]);
        }

        if ($model->updateById(authorizedUser('id'), $update)) {
            Auth::updateAuth($update);
            json([
                'code' => 10000,
                'message' => 'Save Success',
            ]);
        } else {
            json([
                'code' => 10003,
                'message' => 'Something wrong',
            ]);
        }
    }

    public function password()
    {
        view('password', [
            'title' => 'Update password',
        ]);
    }

    public function savePassword()
    {
        $currentPassword = $this->validator->string($_POST, 'current_password');
        $newPassword = $this->validator->string($_POST, 'new_password');

        $model = new UserModel();
        $user = $model->getUserById(authorizedUser('id'));
        if (!password_verify($currentPassword, $user['password'])) {
            json([
                'code' => 10003,
                'message' => 'Incorrect current password',
            ]);
        }

        if ($currentPassword == $newPassword) {
            json([
                'code' => 10001,
                'message' => '新旧のパスワードは同じではいけません。',
            ]);
        }

        if ($model->updateById($user['id'], ['password' => $model->passwordEncrypt($newPassword)])) {
            json([
                'code' => 10000,
                'message' => 'Save Success',
            ]);
        } else {
            json([
                'code' => 10003,
                'message' => 'Something wrong',
            ]);
        }
    }
}