<?php

namespace Admin\Controllers;

use Tools\Auth;

/**
 * Controller for handling admin home/dashboard related functionalities
 * Inherits core features (auth, validation, logging) from the base Controller class
 */
class HomeController extends Controller
{
    /**
     * Display the admin dashboard view page
     * Renders the main dashboard with necessary styles and title
     *
     * @return void
     */
    public function dashboard()
    {
        // Load the dashboard view and pass required data to the template
        view('admin.dashboard', [
            'heads' => [
                // Include admin-specific CSS stylesheet for dashboard styling
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'dashboard'  // Page title for the dashboard
        ]);
    }

    /**
     * Display the administrators action logs view page
     * Handles paginated log data, admin name mapping, and view rendering
     *
     * @return void
     */
    public function logs()
    {
        // Define number of log items to display per page (pagination size)
        $size = 10;

        // Get current page number from URL query parameters:
        // - Use validator to get "page" parameter (default to 1 if not present)
        // - Ensure page number is at least 1 to avoid invalid pagination
        $page = max($this->validator->number($_GET, 'page', 1), 1);

        // Fetch total number of admin action logs (for pagination calculation)
        $total = $this->model->getLogTotal();
        // Fetch paginated list of admin action logs using current page and size
        $data = $this->model->getLogList($page, $size);

        // Get unique admin IDs from log data to fetch corresponding admin names
        $adminIds = array_unique(array_column($data, 'admin_id'));
        $admins = $this->model->getByIds($adminIds);

        // Process admin data into an ID-to-name map for quick lookup:
        if (!empty($admins)) {
            // Convert admin list to associative array (key: admin ID, value: admin name)
            $admins = array_column($admins, 'name', 'id');
            // Hardcode name for admin ID 1 (default admin account)
            $admins[1] = 'admin';
        } else {
            // Fallback: Set default admin if no admin data is found
            $admins = [1 => 'admin'];
        }

        // Enhance log data with admin names (replace admin ID with readable name):
        foreach ($data as $k => $datum) {
            // Assign admin name to log entry (empty string if admin not found)
            $data[$k]['admin'] = $admins[$datum['admin_id']] ?? '';
        }

        // Load the admin action logs view and pass paginated data
        view('admin.logs', [
            'heads' => [
                // Include admin-specific CSS stylesheet for logs page styling
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'Administrator Action Logs',  // Page title for logs page
            'data' => $data,                        // Paginated admin action logs
            'total' => $total,                      // Total number of log records
            'page' => $page,                        // Current page number
            'size' => $size                         // Number of logs per page
        ]);
    }

    /**
     * Handle administrator logout process
     * Logs the logout action, clears auth data, and triggers re-authentication
     *
     * @return void
     */
    public function logout()
    {
        // Save "logout" action to admin logs (tracks when admins log out)
        $this->saveLog('logout');

        // Remove admin authentication data from session (ends current session)
        Auth::removeAuth();

        // Redirect to 401 Unauthorized page (triggers login prompt for next access)
        $this->forbidden();
    }
}