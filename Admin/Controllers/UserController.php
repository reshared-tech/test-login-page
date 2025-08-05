<?php

namespace Admin\Controllers;

use App\Models\UserModel;

class UserController extends Controller
{
    public function users()
    {
        // Number of items per page
        $size = 10;

        // Get current page number from query parameters, default to 1
        $page = max($this->validator->number($_GET, 'page', 1), 1);

        // Initialize user model and get data
        $userModel = new UserModel();
        $total = $userModel->getUserTotal();
        $data = $userModel->getUserList($page, $size);

        // Calculate pagination values (previous, next, and all page numbers)
        [$pre, $next, $pages] = $this->pages($total, $page, $size);

        // Render the admin dashboard view with data
        view('admin.users', [
            'heads' => [
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'Users list',
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'size' => $size,
            'pre' => $pre, // Previous page number
            'next' => $next, // Next page number
            'pages' => $pages, // All available page numbers
        ]);
    }
}