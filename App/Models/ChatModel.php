<?php

namespace App\Models;

use Exception;

/**
 * Model class for handling chat-related database operations
 * Inherits core CRUD utilities from BaseModel
 * Manages chats, chat members, messages, and read status
 */
class ChatModel extends BaseModel
{
    // Chat status constants
    const CHAT_STATUS_NORMAL = 1;  // Normal (active) chat status
    const CHAT_STATUS_STOP = 0;    // Stopped (inactive) chat status

    // Chat creator type constants
    const CREATOR_TYPE_ADMIN = 0;  // Creator is administrator
    const CREATOR_TYPE_USER = 1;   // Creator is regular user

    // Mapping of status codes to Japanese status text (for display)
    const STATUS_MAP = [
        self::CHAT_STATUS_NORMAL => '正常です',
        self::CHAT_STATUS_STOP => '削除しました',
    ];

    /**
     * Perform soft delete on a chat (mark as deleted instead of permanent removal)
     *
     * @param int $id Chat ID to soft delete
     * @return bool True on success, false on failure
     */
    public function softDelete($id)
    {
        // Update chat with current timestamp in deleted_at field
        return $this->updateById($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Update a chat record by ID
     *
     * @param int $id Chat ID to update
     * @param array $data Associative array of fields to update
     * @return bool True on successful update, false on failure/exception
     */
    public function updateById($id, $data)
    {
        try {
            // Generate UPDATE query and parameters via BaseModel method
            [$sql, $val] = $this->parseUpdate('chats', $data);
            // Add chat ID to parameters for WHERE clause
            $val['id'] = $id;
            // Execute query with WHERE clause to target specific chat
            return $this->database->execute($sql . ' WHERE `id` = :id', $val);
        } catch (Exception $e) {
            // Return false if exception occurs (e.g., empty data)
            return false;
        }
    }

    /**
     * Update an existing chat (name and members)
     *
     * @param int $id Chat ID to update
     * @param string $name New chat name
     * @param array $userIds Array of user IDs to set as chat members
     * @return bool True on success
     * @throws Exception If chat update or relation sync fails
     */
    public function updateChat($id, $name, $userIds)
    {
        // Update chat name and member count; throw exception if update fails
        if (!$this->updateById($id, ['name' => $name, 'users_count' => count($userIds)])) {
            throw new Exception('update chat failed');
        }
        // Sync chat members (add new, restore deleted, mark removed as deleted)
        $this->addRelations($id, $userIds, true);
        return true;
    }

    /**
     * Create a new chat room
     *
     * @param string $name Chat room name
     * @param array $userIds Array of user IDs to add as initial members
     * @return array|bool New chat data (with ID) on success, false on failure
     * @throws Exception If chat creation or member addition fails
     */
    public function newChat($name, $userIds)
    {
        // Prepare new chat data
        $chatData = [
            'hash' => uuid(),  // Generate unique hash for chat access
            'name' => $name,
            'status' => self::CHAT_STATUS_NORMAL,  // Set chat to active
            'creator_id' => authorizedUser('id'),  // Get current authorized user ID as creator
            'creator_type' => self::CREATOR_TYPE_ADMIN,  // Mark creator as admin
            'users_count' => count($userIds),  // Set initial member count
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Insert new chat and get its auto-generated ID
        $chatData['id'] = $this->database->prepare(
            'INSERT INTO `chats`(`hash`, `name`, `status`, `creator_id`, `creator_type`, `users_count`, `created_at`, `updated_at`) 
            VALUES(:hash, :name, :status, :creator_id, :creator_type, :users_count, :created_at, :updated_at)',
            $chatData
        )->lastId();

        // Throw exception if chat ID is not generated (insert failed)
        if (!$chatData['id']) {
            throw new Exception('Create new chat failed');
        }

        // Add members to the new chat; return chat data on success
        if ($this->addRelations($chatData['id'], $userIds)) {
            return $chatData;
        }

        return false;
    }

    /**
     * Add or sync chat-member relationships (chat_relations table)
     *
     * @param int $chatId Target chat ID
     * @param array $userIds Array of user IDs to link with the chat
     * @param bool $update If true, sync members (restore/remove); if false, only add new
     * @return bool True on success
     * @throws Exception If member count < 2 or relation insertion fails
     */
    private function addRelations($chatId, $userIds, $update = false)
    {
        // Throw exception if fewer than 2 members (chat requires at least 2 users)
        if (count($userIds) < 2) {
            throw new Exception('at least 2 users');
        }

        $now = date('Y-m-d H:i:s');
        $inserts = [];

        // Prepare bulk insert data for chat_relations
        foreach ($userIds as $userId) {
            $inserts[] = ['chat_id' => $chatId, 'user_id' => $userId, 'created_at' => $now];
        }

        // Generate bulk INSERT query via BaseModel method
        [$sql, $data] = $this->parseInserts('chat_relations', $inserts);
        // Throw exception if insert fails
        if (!$this->database->execute($sql, $data)) {
            throw new Exception('add relations failed');
        }

        // Sync members if $update is true (for existing chats)
        if ($update) {
            $userIdStr = implode(',', $userIds);
            // Restore members previously marked as deleted (set deleted_at to NULL)
            $this->database->execute("UPDATE `chat_relations` SET `deleted_at` = NULL WHERE `chat_id` = $chatId AND `user_id` IN ($userIdStr) AND `deleted_at` IS NOT NULL");
            // Mark members not in $userIds as deleted (set deleted_at to current time)
            $this->database->execute("UPDATE `chat_relations` SET `deleted_at` = '$now' WHERE `chat_id` = $chatId AND `user_id` NOT IN ($userIdStr) AND `deleted_at` IS NULL");
        }

        return true;
    }

    /**
     * Check if a 2-user chat already exists between two users
     *
     * @param int $userId First user ID
     * @param int $anotherUserId Second user ID
     * @return bool True if 2-user chat exists, false otherwise
     */
    public function isExists($userId, $anotherUserId)
    {
        // Get all chat-member relations for the two users (excluding deleted)
        $relations = $this->database->prepare(
            'SELECT `chat_id`, `user_id` FROM `chat_relations` WHERE `user_id` IN (:uid1, :uid2) AND deleted_at is NULL',
            [
                'uid1' => $userId,
                'uid2' => $anotherUserId,
            ]
        )->findAll();

        // Return false if no relations exist
        if (empty($relations)) {
            return false;
        }

        // Find common chat IDs (chats both users are part of)
        $chatHash = [];
        $commonChatIds = [];
        foreach ($relations as $relation) {
            // If chat ID exists in hash and belongs to the other user, mark as common
            if (isset($chatHash[$relation['chat_id']]) && $chatHash[$relation['chat_id']] != $relation['user_id']) {
                $commonChatIds[] = $relation['chat_id'];
            } else {
                // Store user ID for current chat ID
                $chatHash[$relation['chat_id']] = $relation['user_id'];
            }
        }

        // Return false if no common chats
        if (empty($commonChatIds)) {
            return false;
        }

        // Check if any common chat has exactly 2 members (excluding deleted chats)
        $idString = implode(',', array_unique($commonChatIds));
        $chats = $this->database->prepare(
            "SELECT count(*) as `total` FROM `chats` WHERE `id` IN ($idString) AND `users_count` = 2 AND `deleted_at` is NULL"
        )->find();

        // Return true if at least one valid 2-user chat exists
        return $chats['total'] > 0;
    }

    /**
     * Get paginated list of chats a specific user is part of
     *
     * @param int $userId User ID to fetch chats for
     * @param int $page Page number (default: 1)
     * @param int $size Number of chats per page (default: 10)
     * @return array List of chat records
     */
    public function getChatByUserId($userId, $page = 1, $size = 10)
    {
        // Calculate offset for pagination
        $offset = ($page - 1) * $size;

        // Join chats and chat_relations to get user's active chats (sorted by update time, newest first)
        return $this->database->prepare(
            "SELECT `chats`.* FROM `chats`
            LEFT JOIN `chat_relations` 
            ON `chat_relations`.`chat_id` = `chats`.`id` 
            AND `chat_relations`.`deleted_at` IS NULL 
            WHERE `chat_relations`.`user_id` = :user_id 
            AND `chats`.`deleted_at` IS NULL 
            ORDER BY `chats`.`updated_at` DESC 
            LIMIT $offset,$size",
            [
                'user_id' => $userId
            ]
        )->findAll();
    }

    /**
     * Get total number of active chats a specific user is part of
     *
     * @param int $userId User ID to count chats for
     * @return int Total number of chats
     */
    public function getChatTotalByUserId($userId)
    {
        // Count active chats via join with chat_relations
        $res = $this->database->prepare(
            "SELECT count(*) as `total` FROM `chats`
            LEFT JOIN `chat_relations` 
            ON `chat_relations`.`chat_id` = `chats`.`id` 
            AND `chat_relations`.`deleted_at` IS NULL 
            WHERE `chat_relations`.`user_id` = :user_id 
            AND `chats`.`deleted_at` IS NULL",
            [
                'user_id' => $userId
            ]
        )->find();

        // Return count as integer
        return (int)$res['total'];
    }

    /**
     * Calculate unread message count for each chat (for a specific user)
     *
     * @param int $userId User ID to calculate unread count for
     * @param array $chatIds Array of chat IDs to check
     * @return array Associative array (chat_id => unread_count)
     */
    public function calcUnread($userId, $chatIds)
    {
        // Return empty array if no chat IDs are provided
        if (empty($chatIds)) {
            return [];
        }

        // Convert chat IDs to comma-separated string for SQL IN clause
        $chatIdStr = implode(',', $chatIds);

        // Get number of messages the user has read (grouped by chat)
        $readCounts = $this->database->prepare(
            "SELECT count(*) as `total`,`chat_id` FROM `chat_message_read_logs` WHERE `user_id` = {$userId} AND `chat_id` in ({$chatIdStr}) GROUP BY `chat_id`"
        )->findAll();

        // Get total number of messages sent by others (grouped by chat, excluding deleted)
        $allCounts = $this->database->prepare(
            "SELECT `chat_id`, count(*) as `total` FROM `chat_messages` WHERE `user_id` != {$userId} AND `chat_id` in ({$chatIdStr}) AND `deleted_at` IS NULL GROUP BY `chat_id`"
        )->findAll();

        // Convert read counts to associative array (chat_id => read_count)
        $readCountMap = array_column($readCounts, 'total', 'chat_id');

        // Calculate unread count for each chat (total messages - read messages)
        $result = [];
        foreach ($allCounts as $count) {
            $read = isset($readCountMap[$count['chat_id']]) ? (int)$readCountMap[$count['chat_id']] : 0;
            $result[$count['chat_id']] = $count['total'] - $read;
        }

        return $result;
    }

    /**
     * Get other members (excluding owner) for a list of chats
     *
     * @param int $ownerId User ID to exclude (chat owner/current user)
     * @param array $chatIds Array of chat IDs to fetch members for
     * @return array Associative array (chat_id => user_id)
     */
    public function getUsersByChatIds($ownerId, $chatIds)
    {
        // Return empty array if no chat IDs are provided
        if (empty($chatIds)) {
            return [];
        }

        // Convert chat IDs to comma-separated string
        $chatIdStr = implode(',', $chatIds);

        // Get members for each chat (excluding owner and deleted relations)
        $data = $this->database->prepare(
            "SELECT `chat_id`, `user_id` FROM `chat_relations` WHERE `chat_id` in ($chatIdStr) AND `user_id` != :owner_id AND `deleted_at` IS NULL",
            [
                'owner_id' => $ownerId,
            ]
        )->findAll();

        // Return associative array mapping chat IDs to user IDs
        return array_column($data, 'user_id', 'chat_id');
    }

    /**
     * Get chat details by ID
     *
     * @param int $chatId Chat ID to fetch
     * @param bool $ignoreStatus If true, skip status/deleted checks (for admin views)
     * @return mixed Chat record or null if not found
     */
    public function getChatById($chatId, $ignoreStatus = false)
    {
        if ($ignoreStatus) {
            // Fetch chat without status/deleted checks (returns any chat by ID)
            return $this->database->prepare(
                'SELECT * FROM `chats` WHERE `id` = :chat_id',
                [
                    'chat_id' => $chatId,
                ]
            )->find();
        }

        // Fetch only active, non-deleted chats
        return $this->database->prepare(
            'SELECT * FROM `chats` WHERE `id` = :chat_id AND `status` = :status AND `deleted_at` IS NULL',
            [
                'chat_id' => $chatId,
                'status' => self::CHAT_STATUS_NORMAL,
            ]
        )->find();
    }

    /**
     * Get chat details by its unique hash
     *
     * @param string $hash Unique chat hash (used for public access)
     * @return mixed Chat record or null if not found (only non-deleted)
     */
    public function getChatByHash($hash)
    {
        return $this->database->prepare(
            'SELECT * FROM `chats` WHERE `hash` = :hash AND `deleted_at` IS NULL',
            [
                'hash' => $hash,
            ]
        )->find();
    }

    /**
     * Check if a user is a member of a specific chat (active relation)
     *
     * @param int $chatId Chat ID to check
     * @param int $userId User ID to verify
     * @return bool True if user is an active member, false otherwise
     */
    public function checkRelation($chatId, $userId)
    {
        // Check for active (non-deleted) chat-member relation
        $relation = $this->database->prepare(
            'SELECT id FROM `chat_relations` WHERE `chat_id` = :chat_id AND `user_id` = :user_id AND `deleted_at` IS NULL',
            [
                'user_id' => $userId,
                'chat_id' => $chatId,
            ]
        )->find();

        // Return true if relation exists
        return !empty($relation['id']);
    }

    /**
     * Get message history for a chat (older messages)
     *
     * @param int $chatId Chat ID to fetch history for
     * @param int $oldestMessageId Fetch messages older than this ID (0 = fetch latest 10)
     * @return array List of message records (id, user_id, content, created_at)
     */
    public function getMessagesHistoryByChatId($chatId, $oldestMessageId = 0)
    {
        if ($oldestMessageId > 0) {
            // Fetch messages older than $oldestMessageId (max 10)
            return $this->database->prepare(
                'SELECT `id`,`user_id`,`content`,`created_at` FROM `chat_messages` WHERE `chat_id` = :chat_id AND `id` < :id AND `deleted_at` IS NULL ORDER BY `id` DESC LIMIT 0,10',
                [
                    'chat_id' => $chatId,
                    'id' => $oldestMessageId,
                ]
            )->findAll();
        }

        // Fetch latest 10 messages if no $oldestMessageId is provided
        return $this->database->prepare(
            'SELECT `id`,`user_id`,`content`,`created_at` FROM `chat_messages` WHERE `chat_id` = :chat_id AND `deleted_at` IS NULL ORDER BY `id` DESC LIMIT 0,10',
            [
                'chat_id' => $chatId,
            ]
        )->findAll();
    }

    /**
     * Get latest messages for a chat (newer than a specific ID)
     *
     * @param int $chatId Chat ID to fetch messages for
     * @param int $latestMessageId Fetch messages newer than this ID (0 = fetch none)
     * @return array List of message records (id, user_id, content, created_at)
     */
    public function getLatestMessagesByChatId($chatId, $latestMessageId = 0)
    {
        return $this->database->prepare(
            'SELECT `id`,`user_id`,`content`,`created_at` FROM `chat_messages` WHERE `chat_id` = :chat_id AND `id` > :id AND `deleted_at` IS NULL ORDER BY `id` DESC LIMIT 0,10',
            [
                'chat_id' => $chatId,
                'id' => $latestMessageId,
            ]
        )->findAll();
    }

    /**
     * Update a chat's last activity time (updated_at)
     *
     * @param int $chatId Chat ID to update
     * @return mixed Database execution result
     */
    public function touchChat($chatId)
    {
        return $this->database->prepare(
            'UPDATE `chats` SET `updated_at` = :now WHERE `id` = :chat_id',
            [
                'now' => date('Y-m-d H:i:s'),
                'chat_id' => $chatId,
            ]
        );
    }

    /**
     * Save a new message to a chat
     *
     * @param int $chatId Target chat ID
     * @param int $userId Message sender's user ID
     * @param string $content Message content (text or HTML for images)
     * @param string $time Message creation timestamp (Y-m-d H:i:s)
     * @return int Auto-generated ID of the new message
     */
    public function saveMessage($chatId, $userId, $content, $time)
    {
        return $this->database->prepare(
            'INSERT INTO `chat_messages`(`user_id`, `chat_id`, `content`, `created_at`, `updated_at`) VALUES(:user_id, :chat_id, :content, :created_at, :updated_at)',
            [
                'chat_id' => $chatId,
                'user_id' => $userId,
                'content' => $content,
                'created_at' => $time,
                'updated_at' => $time,
            ]
        )->lastId();
    }

    /**
     * Get read status map for messages (which users read which messages)
     *
     * @param array $messageIds Array of message IDs to check
     * @param int $chatId Chat ID associated with the messages
     * @param bool $ignoreStatus If true, include deleted members
     * @return array [read_map, member_count, user_ids]
     * - read_map: Associative array (message_id => [user_id => true])
     * - member_count: Total number of chat members
     * - user_ids: Array of all chat member IDs
     */
    public function getReadMap($messageIds, $chatId, $ignoreStatus = false)
    {
        // Return empty data if no message IDs are provided
        if (empty($messageIds)) {
            return [[], 0, []];
        }

        // Get chat members (include deleted if $ignoreStatus is true)
        if ($ignoreStatus) {
            $users = $this->database->prepare(
                'SELECT `user_id` FROM `chat_relations` WHERE `chat_id` = :chat_id',
                [
                    'chat_id' => $chatId,
                ]
            )->findAll();
        } else {
            $users = $this->database->prepare(
                'SELECT `user_id` FROM `chat_relations` WHERE `chat_id` = :chat_id AND `deleted_at` IS NULL',
                [
                    'chat_id' => $chatId,
                ]
            )->findAll();
        }

        // Return empty data if no members are found
        if (empty($users)) {
            return [];
        }

        // Extract user IDs and convert to comma-separated string
        $userIds = array_column($users, 'user_id');
        $userIdStr = implode(',', $userIds);
        // Remove duplicate message IDs and convert to string
        $messageIdStr = implode(',', array_unique($messageIds));

        // Get read logs for the messages and members
        $logs = $this->database->prepare(
            "SELECT `message_id`, `user_id` FROM `chat_message_read_logs` WHERE `user_id` in ({$userIdStr}) AND `message_id` in ({$messageIdStr})"
        )->findAll();

        // Build read map (message_id => [user_id => true])
        $result = [];
        foreach ($logs as $log) {
            $result[$log['message_id']][$log['user_id']] = true;
        }

        // Return read map, member count, and user IDs
        return [$result, count($userIds), $userIds];
    }

    /**
     * Save read status logs for multiple messages (mark messages as read)
     *
     * @param int $chatId Chat ID
     * @param int $userId User ID who read the messages
     * @param array $messageIds Array of message IDs to mark as read
     * @return bool True on success, false on exception
     */
    public function saveMessageLog($chatId, $userId, $messageIds)
    {
        // Map message IDs to read log data (chat_id, user_id, message_id, timestamp)
        $data = array_map(function ($messageId) use ($chatId, $userId) {
            return [
                'chat_id' => $chatId,
                'user_id' => $userId,
                'message_id' => $messageId,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }, $messageIds);

        try {
            // Generate bulk INSERT query for read logs
            [$sql, $data] = $this->parseInserts('chat_message_read_logs', $data);
            // Execute query
            return $this->database->prepare($sql, $data);
        } catch (Exception $e) {
            // Return false if exception occurs
            return false;
        }
    }

    /**
     * Get total number of chat records (all chats, including deleted)
     *
     * @return int Total chat count
     */
    public function getChatTotal()
    {
        return $this->getTotal('chats');
    }

    /**
     * Get paginated list of all chats (all statuses, including deleted)
     *
     * @param int $page Page number (default: 1)
     * @param int $size Number of chats per page (default: 10)
     * @return array List of chat records (sorted by ID descending)
     */
    public function getChatList($page = 1, $size = 10)
    {
        return $this->getList('chats', $page, $size);
    }

    /**
     * Get total number of messages for a specific chat
     *
     * @param int $chatId Chat ID to count messages for
     * @return int Total message count
     */
    public function getMessageTotal($chatId)
    {
        $res = $this->database->prepare(
            "SELECT count(*) as `total` FROM `chat_messages` WHERE `chat_id` = :chat_id",
            [
                'chat_id' => $chatId
            ]
        )->find();

        return (int)$res['total'];
    }

    /**
     * Get paginated list of messages for a specific chat
     *
     * @param int $chatId Chat ID to fetch messages for
     * @param int $page Page number (default: 1)
     * @param int $size Number of messages per page (default: 10)
     * @return array List of message records (sorted by ID descending)
     */
    public function getMessageList($chatId, $page = 1, $size = 10)
    {
        // Calculate offset for pagination
        $offset = ($page - 1) * $size;

        return $this->database->prepare(
            "SELECT * FROM `chat_messages` WHERE `chat_id` = :chat_id ORDER BY `id` DESC LIMIT $offset,$size",
            [
                'chat_id' => $chatId
            ]
        )->findAll();
    }

    /**
     * Get chat-member relations for a list of chats (active only)
     *
     * @param array $chatIds Array of chat IDs to fetch relations for
     * @return array List of relation records (chat_id, user_id)
     */
    public function getChatRelations($chatIds)
    {
        // Return empty array if no chat IDs are provided
        if (empty($chatIds)) {
            return [];
        }

        // Convert chat IDs to comma-separated string
        $chatStr = implode(',', $chatIds);

        return $this->database->prepare(
            "SELECT `chat_id`, `user_id` FROM `chat_relations` WHERE `chat_id` IN ($chatStr) AND deleted_at is NULL"
        )->findAll();
    }

    /**
     * Get all member IDs for a specific chat (active only)
     *
     * @param int $id Chat ID to fetch members for
     * @return array List of user IDs
     */
    public function getUserIdsByChatId($id)
    {
        // Get active relations for the chat
        $data = $this->database->prepare(
            "SELECT `user_id` FROM `chat_relations` WHERE `chat_id` = {$id} AND deleted_at is NULL"
        )->findAll();

        // Extract and return user IDs
        return array_column($data, 'user_id');
    }

    /**
     * Get the latest message content for each chat in a list
     *
     * @param array $chatIds Array of chat IDs to fetch latest messages for
     * @return array Associative array (chat_id => latest_message_content)
     */
    public function getLastMessages($chatIds)
    {
        // Return empty array if no chat IDs are provided
        if (empty($chatIds)) {
            return [];
        }

        // Convert chat IDs to comma-separated string
        $chatStr = implode(',', $chatIds);

        // Subquery: Get latest message ID for each user-chat pair
        // Main query: Get content of the latest messages
        $data = $this->database->prepare(
            "SELECT `chat_messages`.* FROM `chat_messages` 
            INNER JOIN (
                SELECT `user_id`, MAX(`id`) as `last_id` FROM `chat_messages` 
                WHERE `chat_id` in ($chatStr)
                AND `deleted_at` IS NULL 
                GROUP BY `user_id`,`chat_id`
           ) `latest`
           ON `chat_messages`.`id` = `latest`.`last_id`"
        )->findAll();

        // Return associative array mapping chat IDs to latest message content
        return array_column($data, 'content', 'chat_id');
    }
}