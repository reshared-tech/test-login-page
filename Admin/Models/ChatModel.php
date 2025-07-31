<?php

namespace Admin\Models;

use Exception;

class ChatModel extends BaseModel
{
    const CHAT_STATUS_NORMAL = 1;
    const CHAT_STATUS_STOP = 0;
    const CREATOR_TYPE_ADMIN = 0;
    const CREATOR_TYPE_USER = 1;

    /**
     * @throws Exception
     */
    public function newChat($userId, $anotherUserId)
    {
        $chatData = [
            'hash' => uuid(),
            'name' => 'A New Chat',
            'status' => self::CHAT_STATUS_NORMAL,
            'creator_id' => authorizedUser('id'),
            'creator_type' => self::CREATOR_TYPE_ADMIN,
            'users_count' => 2,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $chatData['id'] = $this->database->prepare('INSERT INTO `chats`(`hash`, `name`, `status`, `creator_id`, `creator_type`, `users_count`, `created_at`) 
            VALUES(:hash, :name, :status, :creator_id, :creator_type, :users_count, :created_at)', $chatData)->lastId();

        if (!$chatData['id']) {
            throw new Exception('Create new chat failed');
        }

        [$sql, $data] = $this->parseInsert('chat_relations', [
            ['chat_id' => $chatData['id'], 'user_id' => $userId, 'created_at' => date('Y-m-d H:i:s')],
            ['chat_id' => $chatData['id'], 'user_id' => $anotherUserId, 'created_at' => date('Y-m-d H:i:s')],
        ]);

        if ($this->database->prepare($sql, $data)) {
            return $chatData;
        }

        return false;
    }

    public function isExists($userId, $anotherUserId)
    {
        // Get all chat id
        $relations = $this->database->prepare('SELECT `chat_id`, `user_id` FROM `chat_relations` WHERE `user_id` IN (:uid1, :uid2) AND deleted_at is NULL', [
            'uid1' => $userId,
            'uid2' => $anotherUserId,
        ])->findAll();

        if (empty($relations)) {
            return false;
        }

        // Foreach relations, pick up common chat id
        $chatHash = [];
        $commonChatIds = [];
        foreach ($relations as $relation) {
            if (isset($chatHash[$relation['chat_id']]) && $chatHash[$relation['chat_id']] != $relation['user_id']) {
                $commonChatIds[] = $relation['chat_id'];
            } else {
                $chatHash[$relation['chat_id']] = $relation['user_id'];
            }
        }

        if (empty($commonChatIds)) {
            return false;
        }

        // Get the chat info with only 2 users
        $idString = implode(',', array_unique($commonChatIds));
        $chats = $this->database->prepare("SELECT count(*) as `total` FROM `chats` WHERE `id` IN ($idString) AND `users_count` = 2 AND `deleted_at` is NULL")->find();

        return $chats['total'] > 0;
    }
}