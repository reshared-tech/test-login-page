<?php

namespace App\Controllers;

use App\Models\UserModel;
use Tools\Auth;

/**
 * Controller for handling user authentication functionalities (login, register, logout)
 * Inherits from the base Controller class
 */
class AuthController extends Controller
{
    /**
     * Flag to skip authentication check for this controller
     * (Allows unauthenticated users to access login/register pages)
     * @var bool
     */
    protected $checkAuth = false;

    /**
     * Display the user login page
     * Restricts access to only guest (unauthenticated) users
     *
     * @return void
     */
    public function login()
    {
        // Redirect authenticated users away from login page (only guests allowed)
        Auth::checkGuest();

        // Load and render the login view with page title
        view('login', [
            'title' => 'Login Page'
        ]);
    }

    /**
     * Display the user registration page
     * Restricts access to only guest (unauthenticated) users
     *
     * @return void
     */
    public function register()
    {
        // Redirect authenticated users away from registration page (only guests allowed)
        Auth::checkGuest();

        // Load and render the registration view with page title
        view('register', [
            'title' => 'Register Page'
        ]);
    }

    /**
     * Handle login form submission (AJAX endpoint)
     * Validates input, checks user credentials, and manages login state
     *
     * @return void Returns JSON response
     */
    public function loginSubmit()
    {
        // Validate input data:
        // - Email: Ensure valid email format
        // - Password: Ensure it's a string (6-200 characters)
        $email = $this->validator->email($_POST, 'email');
        $password = $this->validator->string($_POST, 'password', 6, 200);

        // Return error if input validation fails
        if ($this->validator->hasError()) {
            json([
                'code' => 10001,
                'message' => $this->validator->errors(),
            ]);
        }

        // Initialize UserModel to check user existence
        $userModel = new UserModel();
        $user = $userModel->getUserByEmail($email);

        // Return error if no user is found with the provided email
        if (!$user) {
            json([
                'code' => 10003,
                'message' => 'メール登録されていない。まずレジスタください。',
            ]);
        }

        // Return error if the user account is locked (status = 0)
        if ($user['status'] == 0) {
            json([
                'code' => 10004,
                'message' => 'すみません、口座がロックされていますので、管理人に連絡してください',
            ]);
        }

        // Return error if account is temporarily locked (10+ failed attempts in last 10 minutes)
        if ($user['failed_count'] >= 10 && time() - strtotime($user['updated_at']) < 600) {
            json([
                'code' => 10004,
                'message' => '口座ロックされてる10分後にもう一度お試しください。',
            ]);
        }

        // Verify if the provided password matches the stored hashed password
        if (!password_verify($password, $user['password'])) {
            // Log a special record when failed attempts reach 5
            if ($user['failed_count'] == 4) {
                $userModel->addUserLog($user['id'], 'login failed 5');
            }

            // Increment the failed login attempt counter for the user
            $userModel->incrementFailedCount($user['id']);

            // Return password mismatch error
            json([
                'code' => 10003,
                'message' => '正しくないパスワード',
            ]);
        }

        // Reset failed attempt counter if it's greater than 0 (on successful login)
        if ($user['failed_count'] > 0) {
            $userModel->cleanFailedCount($user['id']);
        }

        // Log the successful user login action
        $userModel->addUserLog($user['id'], 'user log in');

        // Store user authentication data in session (mark user as logged in)
        Auth::addAuth([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ]);

        // Return successful login response
        json([
            'code' => 10000,
            'message' => '登録成功',
        ]);
    }

    /**
     * Handle registration form submission (AJAX endpoint)
     * Validates input, checks for existing emails, and creates new users
     *
     * @return void Returns JSON response
     */
    public function registerSubmit()
    {
        // Validate input data:
        // - Name: Ensure it's a valid string
        // - Email: Ensure valid email format
        // - Password: Ensure it's a string (6-200 characters)
        $name = $this->validator->string($_POST, 'name');
        $email = $this->validator->email($_POST, 'email');
        $password = $this->validator->string($_POST, 'password', 6, 200);

        // Initialize UserModel to check email existence and create users
        $userModel = new UserModel();

        // Return error if email is already registered
        if ($userModel->getUserByEmail($email)) {
            json([
                'code' => 10002,
                'message' => 'Email already registered. Please login.',
            ]);
        }

        // Attempt to create a new user (store name, email, and hashed password)
        $id = $userModel->addUser($name, $email, $password);
        if ($id) {
            // Auto-login the new user by storing auth data in session
            Auth::addAuth([
                'id' => $id,
                'name' => $name,
                'email' => $email,
            ]);

            // Return successful registration response
            json([
                'code' => 10000,
                'message' => 'Registration successful.',
            ]);
        }

        // Return error if user creation fails
        json([
            'code' => 10002,
            'message' => 'Registration failed. Please contact administrator.',
        ]);
    }

    /**
     * Handle user logout
     * Clears authentication session and redirects to default page
     *
     * @return void
     */
    public function logout()
    {
        // Remove user authentication data from session (end current login session)
        Auth::removeAuth();

        // Redirect to the default page (e.g., homepage or login page)
        redirect();
    }
}