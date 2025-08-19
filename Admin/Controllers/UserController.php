<?php

namespace Admin\Controllers;

use App\Models\UserModel;

class UserController extends Controller
{
    public function index()
    {
        // Number of items per page
        $size = 10;

        // Get current page number from query parameters, default to 1
        $page = max($this->validator->number($_GET, 'page', 1), 1);

        // Initialize user model and get data
        $userModel = new UserModel();
        $total = $userModel->getUserTotal();
        $data = $userModel->getUserList($page, $size);

        // Render the admin dashboard view with data
        view('admin.user.list', [
            'heads' => [
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'Users list',
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'size' => $size,
        ]);
    }

    public function show($id)
    {
        $userModel = new UserModel();
        $user = $userModel->getUserById($id);
        $user['avatar'] = nameAvatar($user['name']);
        view('admin.user.show', [
            'heads' => [
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'User Info',
            'user' => $user,
        ]);
    }

    public function lockUser()
    {
        $id = $this->validator->number($_POST, 'id');
        $lock = $this->validator->string($_POST, 'lock') === 'true';

        $userModel = new UserModel();
        if ($userModel->updateById($id, ['status' => $lock ? 0 : 1])) {
            json([
                'code' => 10000,
                'message' => 'ok'
            ]);
        } else {
            json([
                'code' => 10002,
                'message' => 'failed'
            ]);
        }
    }

    public function unLockUser()
    {
        $id = $this->validator->number($_POST, 'id');

        $userModel = new UserModel();
        if ($userModel->updateById($id, ['status' => 1])) {
            json([
                'code' => 10000,
                'message' => 'ok'
            ]);
        } else {
            json([
                'code' => 10002,
                'message' => 'failed'
            ]);
        }
    }

    public function usersApiList()
    {
        $userModel = new UserModel();
        $data = $userModel->getUserAllList();
        json($data);
    }
}