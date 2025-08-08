<?php

namespace App\Controllers;

use App\Models\ChatModel;
use App\Models\UserModel;

class ChatController extends Controller
{
    /**
     * Display a specific chat room
     */
    public function chat()
    {
        // Get chat hash from GET parameter
        $hash = $this->validator->string($_GET, 'h');
        if (empty($hash)) {
            view('404');
            return;
        }

        $model = new ChatModel();
        // Get chat info by hash
        $chat = $model->getChatByHash($hash);
        if (empty($chat)) {
            view('404');
            return;
        }

        // Check if user has permission to access this chat
        if (!$model->checkRelation($chat['id'], authorizedUser('id'))) {
            view('404');
            return;
        }

        // Show the chat page with chat data
        view('chat', [
            'title' => 'Chat Room',
            'chat' => $chat
        ]);
    }

    /**
     * Get messages for a specific chat room
     */
    public function messages()
    {
        // Get JSON data from request
        $data = jsonData();
        if (empty($data)) {
            json([
                'code' => 10001,
                'message' => 'Invalid request',
            ]);
        }

        // Validate input parameters
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

        // Check if chat room exists
        $chat = $model->getChatById($chatId);
        if (empty($chat)) {
            json([
                'code' => 10002,
                'message' => 'Chat room is missing',
            ]);
        }

        // Check if user has permission to access this chat
        if (!$model->checkRelation($chatId, authorizedUser('id'))) {
            json([
                'code' => 10002,
                'message' => 'Forbidden'
            ]);
        }

        // Mark messages as read if readMessageIds are provided
        if (!empty($data['readMessageIds'])) {
            $model->saveMessageLog($chatId, authorizedUser('id'), array_unique($data['readMessageIds']));
        }

        // Get messages based on request type (latest or history)
        if ($latest) {
            $messages = $model->getLatestMessagesByChatId($chatId, $latestMessageId);
        } else {
            $messages = $model->getMessagesHistoryByChatId($chatId, $oldestMessageId);
        }

        // Get read status for messages
        $messageIds = array_merge(array_column($messages, 'id'), $data['unreadMessageIds'] ?? []);
        [$readMap, $relationCount] = $model->getReadMap($messageIds, $chatId);
        // Calculate unread counts for specified messages
        $unreadMap = [];
        if (!empty($data['unreadMessageIds'])) {
            foreach ($data['unreadMessageIds'] as $messageId) {
                $readCount = isset($readMap[$messageId]) ? count(array_keys($readMap[$messageId])) : 0;
                $unreadMap[$messageId] = $relationCount - $readCount - 1;
            }
        }
        // Return empty response if no messages found
        if (empty($messages)) {
            json([
                'code' => 10000,
                'message' => 'ok',
                'unread' => $unreadMap,
                'data' => [],
            ]);
        }

        // Reverse messages to show newest first
        $messages = array_reverse($messages);

        // Get usernames for message senders
        $otherUserIds = [];
        foreach ($messages as $message) {
            if ($message['user_id'] != authorizedUser('id')) {
                $otherUserIds[] = $message['user_id'];
            }
        }

        $otherUserIds = array_unique($otherUserIds);
        $userModel = new UserModel();
        $usernames = $userModel->getUsersNameByIds($otherUserIds);

        // Enhance message data with additional information
        foreach ($messages as $k => $message) {
            $messages[$k]['me'] = $message['user_id'] == authorizedUser('id');

            $messages[$k]['name'] = $messages[$k]['me']
                ? authorizedUser('name')
                : $usernames[$message['user_id']] ?? 'Unknown';

            // Add read status information
            if ($messages[$k]['me']) {
                $readCount = isset($readMap[$message['id']]) ? count(array_keys($readMap[$message['id']])) : 0;
                $messages[$k]['read'] = $relationCount - $readCount - 1;
            } else {
                $messages[$k]['read'] = $readMap[$message['id']][authorizedUser('id')] ?? false;
            }
        }

        // Return messages with additional metadata
        json([
            'code' => 10000,
            'message' => 'ok',
            'unread' => $unreadMap,
            'data' => $messages,
        ]);
    }

    /**
     * Create a new message in a chat room
     */
    public function newMessage()
    {
        // Validate input parameters
        $chatId = $this->validator->number($_POST, 'chat_id');
        $message = $this->validator->string($_POST, 'message', 1, 10000);

        if (empty($chatId) || empty($message)) {
            json([
                'code' => 10001,
                'message' => 'Invalid params',
            ]);
        }

        // Check if chat room exists
        $model = new ChatModel();
        $chat = $model->getChatById($chatId);
        if (empty($chat)) {
            json([
                'code' => 10002,
                'message' => 'Chat room is missing',
            ]);
        }

        // Check if user has permission to post in this chat
        if (!$model->checkRelation($chatId, authorizedUser('id'))) {
            json([
                'code' => 10002,
                'message' => 'Forbidden'
            ]);
        }

        // Save the new message
        $time = date('Y-m-d H:i:s');
        if ($messageId = $model->saveMessage($chatId, authorizedUser('id'), $message, $time)) {
            // Update chat's last activity time
            $model->touchChat($chatId);

            // Return success response with message details
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
            // Return error if message saving failed
            json([
                'code' => 10003,
                'message' => 'Send Failed, Please retry again',
            ]);
        }
    }


    public function upload()
    {
        if (empty($_FILES['files'])) {
            json([
                'code' => 10001,
                'message' => 'No files founded',
            ]);
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        foreach ($_FILES['files']['error'] as $k => $error) {
            if ($error > 0) {
                json([
                    'code' => 10001,
                    'message' => 'Some error',
                ]);
            }
            if (!in_array($_FILES['files']['type'][$k], $allowedTypes)) {
                json([
                    'code' => 10001,
                    'message' => 'Only image/jpg,jpeg,png,gif can be send',
                ]);
            }
        }

        $targetDir = APP_ROOT . '/assets/uploads';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $message = [];
        foreach ($_FILES['files']['name'] as $k => $name) {
            $ext = explode('.', $name);
            $path = uniqid(microtime(true)) . '.' . end($ext);
            $src = 'assets/uploads/' . $path;
            $target = $targetDir . '/' . $path;
            if (move_uploaded_file($_FILES['files']['tmp_name'][$k], $target)) {
                $message[] = "<img class='chat-img' src='$src'>";
            }
        }
        if (empty($message)) {
            json([
                'code' => 10003,
                'message' => 'message send failed',
            ]);
        }
        $_POST['message'] = implode("\n", $message);
        $this->newMessage();
    }
}