<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends Controller
{
    public function index()
    {
        if ($this->isAuthorized()) {
            $this->view('home', [
                'title' => 'Welcome'
            ]);
        } else {
            $this->redirect('/?action=login');
        }
    }

    public function login()
    {
        if ($this->isAuthorized()) {
            $this->redirect();
        } else {
            $this->view('login', [
                'title' => 'Login Page'
            ]);
        }
    }

    public function register()
    {
        if ($this->isAuthorized()) {
            $this->redirect();
        } else {
            $this->view('register', [
                'title' => 'Register Page'
            ]);
        }
    }

    public function loginSubmit()
    {
        $email = $this->string('email');
        $password = $this->string('password', 6, 200);

        if ($this->hasError()) {
            $this->json([
                'code' => 10001,
                'message' => $this->errors,
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json([
                'code' => 10001,
                'message' => [
                    'email' => 'Invalid email format',
                ],
            ]);
        }

        // Check if user exists
        $userModel = new UserModel();
        $user = $userModel->getUserByEmail($email);

        if (!$user) {
            $this->json([
                'code' => 10003,
                'message' => 'Email not registered. Please register first.',
            ]);
        }

        // Check account lock status
        if ($user['failed_count'] >= 10 && time() - strtotime($user['updated_at']) < 600) {
            $this->json([
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

            $this->json([
                'code' => 10003,
                'message' => 'Incorrect password',
            ]);
        }

        // Reset failed attempts on successful login
        if ($user['failed_count'] > 0) {
            $userModel->cleanFailedCount($user['id']);
        }

        // Set session and return success
        $this->addAuth([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ]);

        $this->json([
            'code' => 10000,
            'message' => 'Login successful',
        ]);
    }

    public function registerSubmit()
    {
        $name = $this->string('name');
        $email = $this->string('email');
        $password = $this->string('password', 6, 200);

        // Check for existing email
        $userModel = new UserModel();

        if ($userModel->getUserByEmail($email)) {
            $this->json([
                'code' => 10002,
                'message' => 'Email already registered. Please login.',
            ]);
        }

        // Create new user
        $id = $userModel->addUser($name, $email, $password);
        if ($id) {
            $this->addAuth([
                'id' => $id,
                'name' => $name,
                'email' => $email,
            ]);

            $this->json([
                'code' => 10000,
                'message' => 'Registration successful.',
            ]);
        }

        $this->json([
            'code' => 10002,
            'message' => 'Registration failed. Please contact administrator.',
        ]);
    }

    public function logout()
    {
        // remove session
        $this->removeAuth();

        // return message
        $this->redirect();
    }
}