<?php

namespace App\Controllers;

use App\Models\ChatModel;
use App\Models\UserModel;

class HomeController extends Controller
{
    public function index()
    {
        $size = 10;

        // Get current page number from query parameters, default to 1
        $page = max($this->validator->number($_GET, 'page', 1), 1);

        $model = new ChatModel();
        $userModel = new UserModel();
        // Get chats by user ID
        $total = $model->getChatTotalByUserId(authorizedUser('id'));
        if ($total > 0) {
            $chats = $model->getChatByUserId(authorizedUser('id'), $page, $size);
        } else {
            $chats = [];
        }
        if (!empty($chats)) {
            $chatIds = array_column($chats, 'id');
            // Calculate unread messages count for each chat
            $unreadMap = $model->calcUnread(authorizedUser('id'), $chatIds);
            // Get last messages
            $lastMessages = $model->getLastMessages($chatIds);
            // Add unread count to each chat
            foreach ($chats as $k => $chat) {
                $content = $lastMessages[$chat['id']] ?? '';
                if (strpos($content, '<img ') === 0) {
                    $content = '[image]';
                }
                $chats[$k]['user'] = $chat['name'];
                $chats[$k]['avatar'] = nameAvatar($chat['name']);
                $chats[$k]['unread'] = $unreadMap[$chat['id']] ?? 0;
                $chats[$k]['content'] = $content;
                $chats[$k]['updated_at'] = timeHuman($chat['updated_at']);
            }
        }

        // Show the home page
        view('home', [
            'title' => 'Welcome',
            'chats' => $chats,
            'page' => $page,
            'size' => $size,
            'total' => $total,
        ]);
    }
}