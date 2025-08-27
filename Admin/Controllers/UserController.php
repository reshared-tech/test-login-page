<?php

namespace Admin\Controllers;

use App\Models\UserModel;

/**
 * Controller for handling admin-side user management functionalities
 * Inherits core features (authentication, validation, logging) from the base Controller class
 */
class UserController extends Controller
{
    /**
     * Display the paginated list of all users
     * Handles pagination setup, user data fetching, and view rendering
     *
     * @return void
     */
    public function index()
    {
        // Define the number of user items to display per page (pagination size)
        $size = 10;

        // Get current page number from URL query parameters:
        // - Use validator to retrieve "page" parameter (defaults to 1 if not provided)
        // - Ensure page number is at least 1 to prevent invalid pagination (e.g., page 0 or negative)
        $page = max($this->validator->number($_GET, 'page', 1), 1);

        // Initialize UserModel to interact with user-related database operations
        $userModel = new UserModel();
        // Fetch total number of user records (used for pagination calculation)
        $total = $userModel->getUserTotal();
        // Fetch paginated user list based on current page and page size
        $data = $userModel->getUserList($page, $size);

        // Load the user list view and pass required data to the template
        view('admin.user.list', [
            'heads' => [
                // Include admin-specific CSS stylesheet for user list page styling
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'Users list',  // Page title for the user list
            'data' => $data,          // Paginated user data to display
            'total' => $total,        // Total number of user records (for pagination UI)
            'page' => $page,          // Current page number (for pagination navigation)
            'size' => $size           // Number of users per page (for pagination logic)
        ]);
    }

    /**
     * Display detailed information of a specified user
     * Supports user profile viewing (including avatar generation)
     *
     * @param int $id Unique identifier of the user to view
     * @return void
     */
    public function show($id)
    {
        // Initialize UserModel to fetch user details
        $userModel = new UserModel();
        // Get complete user information by user ID
        $user = $userModel->getUserById($id);
        // Generate a text avatar for the user (based on username via nameAvatar() function)
        $user['avatar'] = nameAvatar($user['name']);

        // Load the user detail view and pass user data
        view('admin.user.show', [
            'heads' => [
                // Include admin-specific CSS stylesheet for user detail page styling
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'User Info',  // Page title for the user detail page
            'user' => $user          // Detailed user data to display
        ]);
    }

    /**
     * Handle user locking or unlocking operations
     * Returns JSON response to indicate success/failure (for AJAX requests)
     *
     * @return void
     */
    public function lockUser()
    {
        // Get and validate user ID from POST data (ensures it's a number)
        $id = $this->validator->number($_POST, 'id');
        // Get and convert lock status from POST data:
        // - Converts string 'true' to boolean true (lock user), others to false (unlock user)
        $lock = $this->validator->string($_POST, 'lock') === 'true';

        // Initialize UserModel to update user status
        $userModel = new UserModel();
        // Set user status: 0 = locked, 1 = unlocked (matches UserModel status definition)
        $data = ['status' => $lock ? 0 : 1];

        // Attempt to update user status by ID
        if ($userModel->updateById($id, $data)) {
            // Log the "lock/unlock user" action (includes user ID and status for audit)
            $this->saveLog('lock-user', array_merge(['id' => $id], $data));

            // Return successful JSON response (agreed code 10000 for success)
            json([
                'code' => 10000,
                'message' => 'ok'
            ]);
        } else {
            // Return failed JSON response (agreed code 10002 for update failure)
            json([
                'code' => 10002,
                'message' => 'failed'
            ]);
        }
    }

    /**
     * API endpoint to fetch the complete list of users
     * Returns all user data as JSON (for frontend dynamic operations, e.g., dropdowns)
     *
     * @return void
     */
    public function usersApiList()
    {
        // Initialize UserModel to fetch all users
        $userModel = new UserModel();
        // Get the full list of all users (no pagination)
        $data = $userModel->getUserAllList();
        // Return the complete user list as JSON response
        json($data);
    }
}