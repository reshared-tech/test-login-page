<?php

namespace App\Controllers;

use App\Models\UserModel;
use Tools\Auth;

/**
 * Controller for handling user profile management
 * Manages profile viewing, profile updates, and password changes
 * Inherits core features from the base Controller class
 */
class ProfileController extends Controller
{
    /**
     * Display the user's profile page
     * Shows current user information (name, email, etc.)
     *
     * @return void
     */
    public function profile()
    {
        // Get currently authenticated user's data from session
        $user = authorizedUser();

        // Render the profile view with user data and page title
        view('profile', [
            'title' => 'Profile',  // Page title for the profile page
            'user' => $user        // Current user's data to display
        ]);
    }

    /**
     * Handle profile update submission (AJAX endpoint)
     * Updates user's name and/or email after validation
     *
     * @return void Returns JSON response
     */
    public function saveProfile()
    {
        // Validate input data from POST:
        // - name: Ensure valid string input
        // - email: Ensure valid email format
        $name = $this->validator->string($_POST, 'name');
        $email = $this->validator->email($_POST, 'email');

        // Get current user data from session (to compare changes)
        $current = authorizedUser();
        $update = [];  // Array to store fields that need updating

        // Add name to update array only if it has changed
        if ($current['name'] != $name) {
            $update['name'] = $name;
        }

        // Add email to update array only if it has changed
        if ($current['email'] != $email) {
            $update['email'] = $email;
        }

        // Return success if no changes were detected
        if (empty($update)) {
            json([
                'code' => 10000,
                'message' => 'no profile info change'
            ]);
        }

        // Initialize UserModel for database operations
        $model = new UserModel();

        // Check if new email is already registered by another user
        if (isset($update['email']) && $model->getUserByEmail($email)) {
            json([
                'code' => 10002,
                'message' => 'Email already registered.',
            ]);
        }

        // Attempt to update user data in the database
        if ($model->updateById(authorizedUser('id'), $update)) {
            // Update user data in session to reflect changes
            Auth::updateAuth($update);

            // Return successful update response
            json([
                'code' => 10000,
                'message' => 'Save Success',
            ]);
        } else {
            // Return error if update failed
            json([
                'code' => 10003,
                'message' => 'Something wrong',
            ]);
        }
    }

    /**
     * Display the password update page
     * Shows form for changing user's password
     *
     * @return void
     */
    public function password()
    {
        // Render the password update view with page title
        view('password', [
            'title' => 'Update password',  // Page title for the password page
        ]);
    }

    /**
     * Handle password change submission (AJAX endpoint)
     * Validates current password and updates to new password
     *
     * @return void Returns JSON response
     */
    public function savePassword()
    {
        // Validate input data from POST:
        // - current_password: Ensure valid string input
        // - new_password: Ensure valid string input
        $currentPassword = $this->validator->string($_POST, 'current_password');
        $newPassword = $this->validator->string($_POST, 'new_password');

        // Initialize UserModel to fetch user data and update password
        $model = new UserModel();
        // Get current user's data from database (to verify password)
        $user = $model->getUserById(authorizedUser('id'));

        // Verify if current password matches the stored hashed password
        if (!password_verify($currentPassword, $user['password'])) {
            json([
                'code' => 10003,
                'message' => 'Incorrect current password',
            ]);
        }

        // Prevent using the same password as current password
        if ($currentPassword == $newPassword) {
            json([
                'code' => 10001,
                'message' => '新旧のパスワードは同じではいけません。',
            ]);
        }

        // Attempt to update password (encrypt new password before saving)
        if ($model->updateById($user['id'], ['password' => $model->passwordEncrypt($newPassword)])) {
            // Return successful password update response
            json([
                'code' => 10000,
                'message' => 'Save Success',
            ]);
        } else {
            // Return error if password update failed
            json([
                'code' => 10003,
                'message' => 'Something wrong',
            ]);
        }
    }
}