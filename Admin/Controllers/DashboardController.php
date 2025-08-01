<?php

namespace Admin\Controllers;

use App\Models\UserModel;
use Tools\Auth;

class DashboardController extends Controller
{
    /**
     * Display dashboard with paginated user list
     */
    public function index()
    {
        // Number of items per page
        $size = 10;

        // Get current page number from query parameters, default to 1
        $page = max($this->validator->number($_GET, 'page', 1), 1);

        // Initialize user model and get data
        $userModel = new UserModel();
        $total = $userModel->getUserTotal();
        $users = $userModel->getUserList($page, $size);

        // Calculate pagination values (previous, next, and all page numbers)
        [$pre, $next, $pages] = $this->pages($total, $page, $size);

        // Render the admin dashboard view with data
        view('admin.dashboard', [
            'title' => 'Users list',
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'size' => $size,
            'pre' => $pre, // Previous page number
            'next' => $next, // Next page number
            'pages' => $pages, // All available page numbers
        ]);
    }

    /**
     * Handle administrator logout
     */
    public function logout()
    {
        // Remove authentication credentials
        Auth::removeAuth();

        // Redirect to forbidden/unauthorized page
        $this->forbidden();
    }
}