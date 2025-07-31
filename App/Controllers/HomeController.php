<?php

namespace App\Controllers;

use App\Models\ChatModel;
use Tools\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Check the user login information
        Auth::checkAuth();
    }

    public function index()
    {
        // Show the home page
        view('home', [
            'title' => 'Welcome'
        ]);
    }

    public function chats()
    {
        $model = new ChatModel();
        $chats = $model->getChatByUserId(authorizedUser('id'));

        if (empty($chats)) {
            json([
                'code' => 10000,
                'msg' => 'ok',
                'data' => [
                    'unread' => 0,
                    'chats' => [],
                ],
            ]);
        }

        $unreadMap = $model->calcUnread(authorizedUser('id'), array_column($chats, 'id'));

        $unread = array_sum($unreadMap);

        foreach ($chats as $k => $chat) {
            $chats[$k]['unread'] = $unreadMap[$chat['id']] ?? 0;
        }

        json([
            'code' => 10000,
            'msg' => 'ok',
            'data' => [
                'unread' => $unread,
                'chats' => $chats,
            ],
        ]);
    }
}