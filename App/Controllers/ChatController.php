<?php

namespace App\Controllers;

use App\Models\ChatModel;
use App\Models\UserModel;
use Tools\Config;
use Tools\Image;

/**
 * Controller for handling user-side chat functionalities
 * (e.g., accessing chat rooms, fetching messages, sending messages, uploading images)
 * Inherits core features from the base Controller class
 */
class ChatController extends Controller
{
    /**
     * Display a specific chat room page
     * Validates chat access permission and renders the chat interface
     *
     * @return void
     */
    public function chat()
    {
        // Get chat unique hash from GET parameter (identifies the chat room)
        $hash = $this->validator->string($_GET, 'h');
        // Render 404 page if chat hash is empty (invalid request)
        if (empty($hash)) {
            view('errors.404');
            return;
        }

        // Initialize ChatModel to fetch chat data
        $model = new ChatModel();
        // Get chat information using the unique hash
        $chat = $model->getChatByHash($hash);
        // Render 404 page if no chat exists for the provided hash
        if (empty($chat)) {
            view('errors.404');
            return;
        }

        // Check if the current authorized user has permission to access this chat
        // (verifies if the user is a member of the chat)
        if (!$model->checkRelation($chat['id'], authorizedUser('id'))) {
            view('errors.404');
            return;
        }

        // Load and render the chat room view with chat data
        view('chat', [
            'title' => $chat['name'] . ' - Chat Room',  // Page title (chat name + "Chat Room")
            'chat' => $chat                             // Chat details (passed to frontend)
        ]);
    }

    /**
     * Fetch messages for a specific chat room (AJAX endpoint)
     * Supports fetching latest messages or message history, and updates read status
     *
     * @return void Returns JSON response
     */
    public function messages()
    {
        // Get JSON-formatted data from the request (typically from AJAX)
        $data = jsonData();
        // Return error if request data is empty (invalid request)
        if (empty($data)) {
            json([
                'code' => 10001,
                'message' => 'Invalid request',
            ]);
        }

        // Validate input parameters from the JSON data:
        // - chatId: Unique ID of the target chat room
        // - oldestMessageId: ID of the oldest message (for fetching history)
        // - latestMessageId: ID of the latest message (for fetching new messages)
        // - latest: Boolean flag (true = fetch latest messages; false = fetch history)
        $chatId = $this->validator->number($data, 'id');
        $oldestMessageId = $this->validator->number($data, 'oldestMessageId', 0);
        $latestMessageId = $this->validator->number($data, 'latestMessageId', 0);
        $latest = $this->validator->boolean($data, 'latest', false);

        // Return error if chat ID is empty (invalid request)
        if (empty($chatId)) {
            json([
                'code' => 10001,
                'message' => 'Invalid request',
            ]);
        }

        // Initialize ChatModel to interact with chat/message data
        $model = new ChatModel();

        // Check if the target chat room exists
        $chat = $model->getChatById($chatId);
        if (empty($chat)) {
            json([
                'code' => 10002,
                'message' => 'Chat room is missing',
            ]);
        }

        // Check if the current user has permission to access the chat
        if (!$model->checkRelation($chatId, authorizedUser('id'))) {
            json([
                'code' => 10002,
                'message' => 'Forbidden'
            ]);
        }

        // Mark specified messages as read if "readMessageIds" are provided in the request
        if (!empty($data['readMessageIds'])) {
            $model->saveMessageLog(
                $chatId,
                authorizedUser('id'),
                array_unique($data['readMessageIds'])  // Remove duplicate message IDs
            );
        }

        // Fetch messages based on the request type (latest or history)
        if ($latest) {
            // Fetch latest messages (newer than $latestMessageId)
            $messages = $model->getLatestMessagesByChatId($chatId, $latestMessageId);
        } else {
            // Fetch message history (older than $oldestMessageId)
            $messages = $model->getMessagesHistoryByChatId($chatId, $oldestMessageId);
        }

        // Collect all relevant message IDs:
        // - IDs from fetched messages
        // - IDs from "unreadMessageIds" (if provided)
        $messageIds = array_merge(array_column($messages, 'id'), $data['unreadMessageIds'] ?? []);
        // Get read status map (message ID → users who read it) and total chat members
        [$readMap, $relationCount] = $model->getReadMap($messageIds, $chatId);

        // Calculate unread count for each message in "unreadMessageIds"
        $unreadMap = [];
        if (!empty($data['unreadMessageIds'])) {
            foreach ($data['unreadMessageIds'] as $messageId) {
                // Number of users who read the message
                $readCount = isset($readMap[$messageId]) ? count(array_keys($readMap[$messageId])) : 0;
                // Unread count = total members - read users - current user (excludes self)
                $unreadMap[$messageId] = $relationCount - $readCount - 1;
            }
        }

        // Return empty data response if no messages are found
        if (empty($messages)) {
            json([
                'code' => 10000,
                'message' => 'ok',
                'unread' => $unreadMap,
                'data' => [],
            ]);
        }

        // Reverse messages to display newest messages first (frontend-friendly order)
        $messages = array_reverse($messages);

        // Collect IDs of other users (excluding current user) who sent messages
        $otherUserIds = [];
        foreach ($messages as $message) {
            if ($message['user_id'] != authorizedUser('id')) {
                $otherUserIds[] = $message['user_id'];
            }
        }

        // Remove duplicate user IDs and fetch their usernames
        $otherUserIds = array_unique($otherUserIds);
        $userModel = new UserModel();
        $usernames = $userModel->getUsersNameByIds($otherUserIds);

        // Enhance message data with additional metadata (for frontend rendering)
        foreach ($messages as $k => $message) {
            // Flag to indicate if the current user is the message sender
            $messages[$k]['me'] = $message['user_id'] == authorizedUser('id');

            // Set display name for the message sender:
            // - Use current user's name if they sent the message
            // - Use fetched username (or "Unknown") for other senders
            $messages[$k]['name'] = $messages[$k]['me']
                ? authorizedUser('name')
                : $usernames[$message['user_id']] ?? 'Unknown';

            // Add read status information:
            if ($messages[$k]['me']) {
                // For messages sent by current user: show number of other users who read it
                $readCount = isset($readMap[$message['id']]) ? count(array_keys($readMap[$message['id']])) : 0;
                $messages[$k]['read'] = $relationCount - $readCount - 1;
            } else {
                // For messages received: show if current user has read it
                $messages[$k]['read'] = $readMap[$message['id']][authorizedUser('id')] ?? false;
            }
        }

        // Return successful response with messages and unread status
        json([
            'code' => 10000,
            'message' => 'ok',
            'unread' => $unreadMap,
            'data' => $messages,
        ]);
    }

    /**
     * Send a new text message to a chat room (AJAX endpoint)
     * Validates input, checks permissions, and saves the message
     *
     * @return void Returns JSON response
     */
    public function newMessage()
    {
        // Validate input parameters from POST data:
        // - chat_id: ID of the target chat room
        // - message: Text content of the message (1-10000 characters)
        $chatId = $this->validator->number($_POST, 'chat_id');
        $message = $this->validator->string($_POST, 'message', 1, 10000);

        // Return error if chat ID or message content is empty
        if (empty($chatId) || empty($message)) {
            json([
                'code' => 10001,
                'message' => 'Invalid params',
            ]);
        }

        // Initialize ChatModel to save the message and validate chat
        $model = new ChatModel();
        // Check if the target chat room exists
        $chat = $model->getChatById($chatId);
        if (empty($chat)) {
            json([
                'code' => 10002,
                'message' => 'Chat room is missing',
            ]);
        }

        // Check if the current user has permission to send messages in the chat
        if (!$model->checkRelation($chatId, authorizedUser('id'))) {
            json([
                'code' => 10002,
                'message' => 'Forbidden'
            ]);
        }

        // Get current timestamp (used for message creation time)
        $time = date('Y-m-d H:i:s');
        // Attempt to save the new message to the database
        if ($messageId = $model->saveMessage($chatId, authorizedUser('id'), $message, $time)) {
            // Update the chat's last activity time (marks chat as recently used)
            $model->touchChat($chatId);

            // Return successful response with message details
            json([
                'code' => 10000,
                'message' => 'ok',
                'data' => [
                    'message_id' => $messageId,  // ID of the newly saved message
                    'content' => $message,       // Message text content
                    'created_at' => $time,       // Message creation timestamp
                ],
            ]);
        } else {
            // Return error if message saving fails
            json([
                'code' => 10003,
                'message' => 'Send Failed, Please retry again',
            ]);
        }
    }

    /**
     * Handle image uploads for chat messages (AJAX endpoint)
     * Validates image files, processes them, and converts to chat-compatible HTML
     *
     * @return void Returns JSON response (via newMessage() method)
     */
    public function upload()
    {
        // Return error if no files are uploaded
        if (empty($_FILES['files'])) {
            json([
                'code' => 10001,
                'message' => 'No files founded',
            ]);
        }

        // Define allowed image MIME types (restricts to common image formats)
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        // Validate each uploaded file
        foreach ($_FILES['files']['error'] as $k => $error) {
            // Return error if file upload encountered an error (e.g., partial upload)
            if ($error > 0) {
                json([
                    'code' => 10001,
                    'message' => 'Some error',
                ]);
            }
            // Return error if file size exceeds the maximum allowed size (from Config)
            if ($_FILES['files']['size'][$k] > Config::upload['max_size']) {
                json([
                    'code' => 10001,
                    'message' => 'The image is too large to upload',
                ]);
            }
            // Return error if file type is not in the allowed list
            if (!in_array($_FILES['files']['type'][$k], $allowedTypes)) {
                json([
                    'code' => 10001,
                    'message' => 'Only image/jpg,jpeg,png,gif can be send',
                ]);
            }
        }

        // Initialize Image tool for processing uploaded images
        $imageTool = new Image();

        // Array to store HTML for uploaded images (chat-compatible format)
        $message = [];
        foreach ($_FILES['files']['name'] as $k => $name) {
            // Generate a unique filename to avoid overwrites
            $name = $imageTool->uniqFilename($name);
            try {
                // Process the uploaded image (e.g., resize, save to server)
                if ($imageTool->formatUpload($name, $_FILES['files']['tmp_name'][$k])) {
                    // Get the public URL/path of the processed image
                    $src = $imageTool->fileSrc($name);

                    // Create HTML img tag for the image (chat-friendly class)
                    $message[] = "<img class='chat-img' src='$src'>";
                }
            } catch (\Exception $e) {
                // Silent fail: Ignore exceptions to avoid breaking other uploads
            }
        }

        // Return error if no images were successfully processed
        if (empty($message)) {
            json([
                'code' => 10003,
                'message' => 'message send failed',
            ]);
        }

        // Convert image HTML array to a single string (newline-separated)
        $_POST['message'] = implode("\n", $message);
        // Reuse newMessage() method to send the image HTML as a chat message
        $this->newMessage();
    }
}