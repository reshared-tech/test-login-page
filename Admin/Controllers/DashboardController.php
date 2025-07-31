<?php

namespace Admin\Controllers;

use App\Models\UserModel;
use Tools\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $size = 10;

        // Get page
        $page = max($this->validator->number($_GET, 'page', 1), 1);

        // Get users from database
        $userModel = new UserModel();
        $total = $userModel->getUserTotal();
        $users = $userModel->getUserList($page, $size);

        [$pre, $next, $pages] = $this->pages($total, $page, $size);

        // Show the page
        view('admin.dashboard', [
            'title' => 'Users list',
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'size' => $size,
            'pre' => $pre,
            'next' => $next,
            'pages' => $pages,
        ]);
    }

    public function logout()
    {
        Auth::removeAuth();

        $this->forbidden();
    }
}