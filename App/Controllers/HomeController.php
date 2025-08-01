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
        $data = jsonData();
        if (empty($data)) {
            json([
                'code' => 10001,
                'message' => 'Invalid request',
            ]);
        }

        $chatId = $this->validator->number($data, 'id');
        $oldestMessageId = $this->validator->number($data, 'oldestMessageId', 0);
        $latestMessageId = $this->validator->number($data, 'latestMessageId', 0);
        $latest = $this->validator->boolean($data, 'latest', false);

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

        // 将用户已读信息写入日志，标记为已读
        if (!empty($data['readMessageIds'])) {
            $model->saveMessageLog($chatId, authorizedUser('id'), array_unique($data['readMessageIds']));
        }

        if ($latest) {
            $messages = $model->getLatestMessagesByChatId($chatId, $latestMessageId);
        } else {
            $messages = $model->getMessagesHistoryByChatId($chatId, $oldestMessageId);
        }

        // Get messages read log
        $messageIds = array_merge(array_column($messages, 'id'), $data['unreadMessageIds'] ?? []);
        [$readMap, $relationCount] = $model->getReadMap($messageIds, $chatId);

        $unreadMap = [];
        if (!empty($data['unreadMessageIds'])) {
            foreach ($data['unreadMessageIds'] as $messageId) {
                $readCount = isset($readMap[$messageId]) ? count(array_keys($readMap[$messageId])) : 0;
                $unreadMap[$messageId] = $relationCount - $readCount - 1;
            }
        }

        if (empty($messages)) {
            json([
                'code' => 10000,
                'message' => 'ok',
                'unread' => $unreadMap,
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

            if ($messages[$k]['me']) {
                $readCount = isset($readMap[$message['id']]) ? count(array_keys($readMap[$message['id']])) : 0;
                $messages[$k]['read'] = $relationCount - $readCount - 1;
            } else {
                $messages[$k]['read'] = $readMap[$message['id']][authorizedUser('id')] ?? false;
            }
        }

        json([
            'code' => 10000,
            'message' => 'ok',
            'unread' => $unreadMap,
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
        $time = date('Y-m-d H:i:s');
        if ($messageId = $model->saveMessage($chatId, authorizedUser('id'), $message, $time)) {
            $model->touchChat($chatId);

            json([
                'code' => 10000,
                'message' => 'ok',
                'data' => [
                    'message_id' => $messageId,
                    'content' => $message,
                    'created_at' => $time,
                ],
            ]);
        } else {
            json([
                'code' => 10003,
                'message' => 'Send Failed, Please retry again',
            ]);
        }
    }
}