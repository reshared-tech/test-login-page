<?php

namespace App\Models;

use Admin\Models\BaseModel;
use Exception;

class ChatModel extends BaseModel
{
    const CHAT_STATUS_NORMAL = 1;
    const CHAT_STATUS_STOP = 0;
    const CREATOR_TYPE_ADMIN = 0;
    const CREATOR_TYPE_USER = 1;

    public function getChatByUserId($userId)
    {
        $relations = $this->database->prepare('SELECT `chat_id` FROM `chat_relations` WHERE `user_id` = :user_id AND `deleted_at` IS NULL', [
            'user_id' => $userId
        ])->findAll();

        if (empty($relations)) {
            return [];
        }

        $chatIds = implode(',', array_unique(array_column($relations, 'chat_id')));

        return $this->database->prepare('SELECT `id`,`hash`,`name`,`updated_at` FROM `chats` WHERE `id` in (' . $chatIds . ') AND `status` = ' . self::CHAT_STATUS_NORMAL . ' AND `deleted_at` IS NULL ORDER BY `updated_at` DESC')->findAll();
    }

    public function calcUnread($userId, $chatIds)
    {
        if (empty($chatIds)) {
            return [];
        }

        $chatIdStr = implode(',', $chatIds);

        $readCounts = $this->database->prepare("SELECT count(*) as `total`,`chat_id` FROM `chat_message_read_logs` WHERE `user_id` = {$userId} AND `chat_id` in ({$chatIdStr}) GROUP BY `chat_id`")->findAll();

        $allCounts = $this->database->prepare("SELECT `chat_id`, count(*) as `total` FROM `chat_messages` WHERE `user_id` != {$userId} AND `chat_id` in ({$chatIdStr}) AND `deleted_at` IS NULL GROUP BY `chat_id`")->findAll();

        $readCountMap = array_column($readCounts, 'total', 'chat_id');

        $result = [];
        foreach ($allCounts as $count) {
            $read = isset($readCountMap[$count['chat_id']]) ? (int)$readCountMap[$count['chat_id']] : 0;
            $result[$count['chat_id']] = $count['total'] - $read;
        }

        return $result;
    }

    public function getChatById($chatId)
    {
        return $this->database->prepare('SELECT * FROM `chats` WHERE `id` = :chat_id AND `status` = :status AND `deleted_at` IS NULL', [
            'chat_id' => $chatId,
            'status' => self::CHAT_STATUS_NORMAL,
        ])->find();
    }

    public function getChatByHash($hash)
    {
        return $this->database->prepare('SELECT * FROM `chats` WHERE `hash` = :hash AND `status` = :status AND `deleted_at` IS NULL', [
            'hash' => $hash,
            'status' => self::CHAT_STATUS_NORMAL,
        ])->find();
    }

    public function checkRelation($chatId, $userId)
    {
        $relation = $this->database->prepare('SELECT id FROM `chat_relations` WHERE `chat_id` = :chat_id AND `user_id` = :user_id AND `deleted_at` IS NULL', [
            'user_id' => $userId,
            'chat_id' => $chatId,
        ])->find();

        return !empty($relation['id']);
    }

    public function getMessagesByChatId($chatId, $lastMessageId = 0)
    {
        if ($lastMessageId > 0) {
            return $this->database->prepare('SELECT `id`,`user_id`,`content`,`created_at` FROM `chat_messages` WHERE `chat_id` = :chat_id AND `id` < :last_id AND `deleted_at` IS NULL ORDER BY `id` DESC LIMIT 0,10', [
                'chat_id' => $chatId,
                'last_id' => $lastMessageId,
            ])->findAll();
        }

        return $this->database->prepare('SELECT `id`,`user_id`,`content`,`created_at` FROM `chat_messages` WHERE `chat_id` = :chat_id AND `deleted_at` IS NULL ORDER BY `id` DESC LIMIT 0,10', [
            'chat_id' => $chatId,
        ])->findAll();
    }

    public function saveMessage($chatId, $userId, $content)
    {
        return $this->database->prepare('INSERT INTO `chat_messages`(`user_id`, `chat_id`, `content`, `created_at`, `updated_at`) VALUES(:user_id, :chat_id, :content, :created_at, :updated_at)', [
            'chat_id' => $chatId,
            'user_id' => $userId,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}