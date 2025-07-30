<?php

namespace App\Controllers;

use App\Models\UserModel;
use Tools\Auth;

class AuthController extends Controller
{
    public function login()
    {
        // Only unlogged-in users are allowed to log in
        Auth::checkGuest();

        // Show the login page
        view('login', [
            'title' => 'Login Page'
        ]);
    }

    public function register()
    {
        // Only unlogged-in users are allowed to register
        Auth::checkGuest();

        // Show the register page
        view('register', [
            'title' => 'Register Page'
        ]);
    }

    public function loginSubmit()
    {
        $email = $this->validator->email($_POST, 'email');
        $password = $this->validator->string($_POST, 'password', 6, 200);

        if ($this->validator->hasError()) {
            json([
                'code' => 10001,
                'message' => $this->validator->errors(),
            ]);
        }

        // Check if user exists
        $userModel = new UserModel();
        $user = $userModel->getUserByEmail($email);

        if (!$user) {
            json([
                'code' => 10003,
                'message' => 'Email not registered. Please register first.',
            ]);
        }

        // Check account lock status
        if ($user['failed_count'] >= 10 && time() - strtotime($user['updated_at']) < 600) {
            json([
                'code' => 10004,
                'message' => 'Account locked. Try again in 10 minutes.',
            ]);
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            // Save log in failed log when failed count is 5
            if ($user['failed_count'] == 4) {
                $userModel->addUserLog($user['id'], 'login failed 5');
            }

            // Increment failed attempt counter
            $userModel->incrementFailedCount($user['id']);

            json([
                'code' => 10003,
                'message' => 'Incorrect password',
            ]);
        }

        // Reset failed attempts on successful login
        if ($user['failed_count'] > 0) {
            $userModel->cleanFailedCount($user['id']);
        }

        $userModel->addUserLog($user['id'], 'user log in');

        // Set session and return success
        Auth::addAuth([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ]);

        json([
            'code' => 10000,
            'message' => 'Login successful',
        ]);
    }

    public function registerSubmit()
    {
        $name = $this->validator->string($_POST, 'name');
        $email = $this->validator->email($_POST, 'email');
        $password = $this->validator->string($_POST, 'password', 6, 200);

        // Check for existing email
        $userModel = new UserModel();

        if ($userModel->getUserByEmail($email)) {
            json([
                'code' => 10002,
                'message' => 'Email already registered. Please login.',
            ]);
        }

        // Create new user
        $id = $userModel->addUser($name, $email, $password);
        if ($id) {
            Auth::addAuth([
                'id' => $id,
                'name' => $name,
                'email' => $email,
            ]);

            json([
                'code' => 10000,
                'message' => 'Registration successful.',
            ]);
        }

        json([
            'code' => 10002,
            'message' => 'Registration failed. Please contact administrator.',
        ]);
    }

    public function logout()
    {
        // remove session
        Auth::removeAuth();

        // return message
        redirect();
    }
}