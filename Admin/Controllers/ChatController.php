<?php

namespace Admin\Controllers;

use App\Models\ChatModel;
use App\Models\UserModel;

class ChatController extends Controller
{
    public function index()
    {
        // Number of items per page
        $size = 10;
        // Get current page number from query parameters, default to 1
        $page = max($this->validator->number($_GET, 'page', 1), 1);

        $model = new ChatModel();
        $total = $model->getChatTotal();
        $data = $model->getChatList($page, $size);

        // Get chat relations
        $relations = $model->getChatRelations(array_column($data, 'id'));
        // Get users name
        $userModel = new UserModel();
        $users = $userModel->getUsersNameByIds(array_unique(array_column($relations, 'user_id')));

        $relationMap = [];
        foreach ($relations as $relation) {
            $relationMap[$relation['chat_id']][] = isset($users[$relation['user_id']]) ? htmlspecialchars($users[$relation['user_id']]) : '';
        }

        foreach ($data as $k => $datum) {
            $users = $relationMap[$datum['id']] ?? [];
            $data[$k]['users'] = implode("\n", $users);
            $data[$k]['status'] = ChatModel::STATUS_MAP[$datum['status']];
        }

        view('admin.chats', [
            'heads' => [
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'Chats',
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'size' => $size,
        ]);
    }

    public function show()
    {
        $chatId = $this->validator->number($_GET, 'id');
        if (empty($chatId)) {
            redirect('admin/chats');
        }

        // Number of items per page
        $size = 10;
        // Get current page number from query parameters, default to 1
        $page = max($this->validator->number($_GET, 'page', 1), 1);

        $model = new ChatModel();
        $total = $model->getMessageTotal($chatId);
        $data = $model->getMessageList($chatId, $page, $size);

        // Get read logs
        [$readMap, $usersCount, $userIds] = $model->getReadMap(array_column($data, 'id'), $chatId, true);

        // Get users name
        $userModel = new UserModel();
        $users = $userModel->getUsersNameByIds($userIds);

        foreach ($data as $k => $datum) {
            $data[$k]['username'] = $users[$datum['user_id']] ?? "({$datum['user_id']})";

            if (empty($readMap[$datum['id']])) {
                $data[$k]['read_users'] = '';
            } else {
                $usernames = [];
                foreach ($readMap[$datum['id']] as $userId => $_) {
                    $usernames[] = $users[$userId] ?? "($userId)";
                }
                $data[$k]['read_users'] = implode("\n", $usernames);
            }
        }

        view('admin.messages', [
            'heads' => [
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'chatId' => $chatId,
            'title' => 'Messages History',
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'size' => $size,
            'usersCount' => $usersCount,
        ]);
    }

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