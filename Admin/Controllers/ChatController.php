<?php

namespace Admin\Controllers;

use Admin\Models\ChatModel;

class ChatController extends Controller
{
    /**
     * Create a new chat session between two users
     *
     * @return void Returns JSON response
     */
    public function store()
    {
        // Get and validate input data
        $data = jsonData();

        // Validate data structure
        if (!is_array($data) || count($data) !== 2) {
            json([
                'code' => 10001,
                'message' => 'Please select two users to chat',
            ]);
        }

        // Validate users are different
        if ($data[0] == $data[1]) {
            json([
                'code' => 10001,
                'message' => 'Please select two different users to chat',
            ]);
        }

        // Validate user IDs are numeric
        if (!is_numeric($data[0]) || !is_numeric($data[1])) {
            json([
                'code' => 10001,
                'message' => 'Invalid parameters - user IDs must be numeric',
            ]);
        }

        $model = new ChatModel();

        // Check if chat relation already exists
        if ($model->isExists($data[0], $data[1])) {
            json([
                'code' => 10002,
                'message' => 'A chat already exists between these users',
            ]);
        }

        try {
            // Attempt to create new chat
            $chat = $model->newChat($data[0], $data[1]);

            if ($chat) {
                json([
                    'code' => 10000,
                    'message' => 'Chat created successfully',
                    'data' => $chat
                ]);
            }

            json([
                'code' => 10003,
                'message' => 'Failed to create chat',
            ]);
        } catch (\Exception $e) {
            json([
                'code' => 10003,
                'message' => $e->getMessage(),
            ]);
        }
    }
}