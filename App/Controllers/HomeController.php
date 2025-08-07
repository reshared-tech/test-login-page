<?php

namespace App\Controllers;

use App\Models\UserModel;
use Tools\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Check the user login information
        Auth::checkAuth();
    }

    public function index()
    {
        // Show the home page
        view('home', [
            'title' => 'Welcome'
        ]);
    }

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

    }
}