<?php

namespace Admin\Controllers;

use Tools\Auth;

class HomeController extends Controller
{
    public function dashboard()
    {
        view('admin.dashboard', [
            'heads' => [
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'dashboard'
        ]);
    }

    public function logs()
    {
        // Number of items per page
        $size = 10;

        // Get current page number from query parameters, default to 1
        $page = max($this->validator->number($_GET, 'page', 1), 1);

        $total = $this->model->getLogTotal();
        $data = $this->model->getLogList($page, $size);

        $admins = $this->model->getByIds(array_unique(array_column($data, 'admin_id')));
        if (!empty($admins)) {
            $admins = array_column($admins, 'name', 'id');
            $admins[1] = 'admin';
        } else {
            $admins = [1 => 'admin'];
        }

        foreach ($data as $k => $datum) {
            $data[$k]['admin'] = $admins[$datum['admin_id']] ?? '';
        }

        // Render the admin dashboard view with data
        view('admin.logs', [
            'heads' => [
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'Administrator Action Logs',
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'size' => $size,
        ]);
    }

    /**
     * Handle administrator logout
     */
    public function logout()
    {
        // Add logout log
        $this->saveLog('logout');

        // Remove authentication credentials
        Auth::removeAuth();

        // Redirect to forbidden/unauthorized page
        $this->forbidden();
    }
}