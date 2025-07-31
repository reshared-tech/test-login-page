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
}