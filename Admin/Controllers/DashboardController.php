<?php

namespace Admin\Controllers;

use App\Models\UserModel;

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

        $pre = $page == 1 ? 0 : $page - 1;
        $next = $page == ceil($total / $size) ? 0 : $page + 1;

        // Show the page
        view('admin.dashboard', [
            'title' => 'Users list',
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'size' => $size,
            'pre' => $pre,
            'next' => $next,
        ]);
    }
}