<?php

namespace Admin\Controllers;

use App\Models\ChatModel;
use App\Models\UserModel;
use Exception;

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
            $data[$k]['status'] = $datum['deleted_at'] ? 'DELETE' : ChatModel::STATUS_MAP[$datum['status']];
        }

        view('admin.chat.list', [
            'heads' => [
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'Chats',
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'size' => $size,
            'modalTitle' => __('New Chat'),
        ]);
    }

    public function show($id)
    {
        $model = new ChatModel();
        $userModel = new UserModel();
        $chat = $model->getChatById($id, true);

        if ($chat['creator_type']) {
            $user = $userModel->getUserById($chat['creator_id']);
            $chat['creator'] = 'User: ' . $user['name'];
        } else {
            $admin = $this->model->getById($chat['creator_id']);
            $chat['creator'] = 'Administrator: ' . $admin['name'];
        }

        $members = $userModel->getUsersInfoByIds(
            $model->getUserIdsByChatId($id)
        );
        foreach ($members as $k => $member) {
            $members[$k]['avatar'] = nameAvatar($member['name']);
        }

        view('admin.chat.show', [
            'heads' => [
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'Chat Info',
            'chat' => $chat,
            'members' => $members,
            'selected' => json_encode(array_column($members, 'id')),
        ]);
    }

    public function messages()
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
        $id = $data['id'] ?? 0;
        $data['name'] = trim($data['name']);
        $data['users'] = array_unique($data['users']);

        if (empty($data['name'])) {
            json([
                'code' => 10001,
                'message' => 'Please input chat name',
            ]);
        }

        // Validate data structure
        if (count($data['users']) < 2) {
            json([
                'code' => 10001,
                'message' => 'Please select two users to chat',
            ]);
        }

        $model = new ChatModel();
        try {
            if ($id) {
                $action = 'update chat room';
                $chat = $model->updateChat($id, $data['name'], $data['users']);
            } else {
                $action = 'create chat room';
                // Attempt to create new chat
                $chat = $model->newChat($data['name'], $data['users']);
            }

            if ($chat) {
                // save admin action log
                $this->saveLog($action, array_merge(['id' => $id], $data));

                json([
                    'code' => 10000,
                    'message' => 'ok',
                    'data' => $chat
                ]);
            }

            json([
                'code' => 10003,
                'message' => 'Failed to create chat',
            ]);
        } catch (Exception $e) {
            json([
                'code' => 10003,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function lockChat()
    {
        $id = $this->validator->number($_POST, 'id');
        $lock = $this->validator->string($_POST, 'lock') === 'true';

        $model = new ChatModel();
        $data = ['status' => $lock ? 0 : 1];
        if ($model->updateById($id, $data)) {
            // save admin action log
            $this->saveLog('lock-chat', array_merge(['id' => $id], $data));

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

    public function delete($id)
    {
        $model = new ChatModel();

        if ($model->softDelete($id)) {
            // save admin action log
            $this->saveLog('soft-delete-chat', ['id' => $id]);

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
}