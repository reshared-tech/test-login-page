<?php

namespace App\Controllers;

use App\Models\ChatModel;
use App\Models\UserModel;
use Tools\Auth;
use Tools\Validator;

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

    public function chat()
    {
        $hash = $this->validator->string($_GET, 'h');
        if (empty($hash)) {
            view('404');
            return;
        }

        $model = new ChatModel();
        // Get chat info
        $chat = $model->getChatByHash($hash);
        if (empty($chat)) {
            view('404');
            return;
        }

        // Get chat relations
        if (!$model->checkRelation($chat['id'], authorizedUser('id'))) {
            view('404');
            return;
        }

        // Show the chat page
        view('chat', [
            'chat' => $chat
        ]);
    }

    public function messages()
    {
        $chatId = $this->validator->number($_GET, 'id');
        $lastMessageId = $this->validator->number($_GET, 'lastMessageId', 0);

        if (empty($chatId)) {
            json([
                'code' => 10001,
                'message' => 'Invalid request',
            ]);
        }

        $model = new ChatModel();

        // Check chat room exists
        $chat = $model->getChatById($chatId);
        if (empty($chat)) {
            json([
                'code' => 10002,
                'message' => 'Chat room is missing',
            ]);
        }

        // Check user permission
        if (!$model->checkRelation($chatId, authorizedUser('id'))) {
            json([
                'code' => 10002,
                'message' => 'Forbidden'
            ]);
        }

        $messages = $model->getMessagesByChatId($chatId, $lastMessageId);
        if (empty($messages)) {
            json([
                'code' => 10000,
                'message' => 'ok',
                'data' => [],
            ]);
        }

        //reverse messages
        $messages = array_reverse($messages);

        // Get other users name
        $otherUserIds = [];
        foreach ($messages as $message) {
            if ($message['user_id'] != authorizedUser('id')) {
                $otherUserIds[] = $message['user_id'];
            }
        }

        $otherUserIds = array_unique($otherUserIds);
        $userModel = new UserModel();
        $usernames = $userModel->getUsersNameByIds($otherUserIds);

        foreach ($messages as $k => $message) {
            $messages[$k]['me'] = $message['user_id'] == authorizedUser('id');

            $messages[$k]['name'] = $messages[$k]['me']
                ? authorizedUser('name')
                : $usernames[$message['user_id']] ?? 'Unknown';
        }

        json([
            'code' => 10000,
            'message' => 'ok',
            'data' => $messages,
        ]);
    }

    public function newMessage()
    {
        $chatId = $this->validator->number($_POST, 'chat_id');
        $message = $this->validator->string($_POST, 'message', 1, 10000);

        if (empty($chatId) || empty($message)) {
            json([
                'code' => 10001,
                'message' => 'Invalid params',
            ]);
        }

        // Check chat room exists
        $model = new ChatModel();
        $chat = $model->getChatById($chatId);
        if (empty($chat)) {
            json([
                'code' => 10002,
                'message' => 'Chat room is missing',
            ]);
        }

        // Check user permission
        if (!$model->checkRelation($chatId, authorizedUser('id'))) {
            json([
                'code' => 10002,
                'message' => 'Forbidden'
            ]);
        }

        // Save Message
        if ($model->saveMessage($chatId, authorizedUser('id'), $message)) {
            json([
                'code' => 10000,
                'message' => 'ok'
            ]);
        } else {
            json([
                'code' => 10003,
                'message' => 'Send Failed, Please retry again',
            ]);
        }
    }
}