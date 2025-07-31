CREATE TABLE `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'primary index',
  `name` varchar(255) NOT NULL COMMENT 'username',
  `email` varchar(255) NOT NULL COMMENT 'email',
  `password` varchar(255) NOT NULL COMMENT 'user password',
  `failed_count` tinyint(4) unsigned NOT NULL DEFAULT 0 COMMENT 'login failed times',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'user register time',
  `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'last update time',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'user id',
  `user_agent` varchar(255) NOT NULL COMMENT 'user agent',
  `note` varchar(255) NOT NULL COMMENT 'some description',
  `ip` varchar(255) NOT NULL COMMENT 'user ip address',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'log time',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `administrators` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'primary index',
  `name` varchar(255) NOT NULL COMMENT 'manager name',
  `password` varchar(255) NOT NULL COMMENT 'manager password',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'user register time',
  `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'last update time',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `hash` varchar(50) NOT NULL COMMENT 'uniq hash',
  `name` varchar(255) NOT NULL COMMENT 'The chat name might be empty at the beginning',
  `status` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT 'The chat status is set by default to 1- normal 0- closed. It is not used at the beginning',
  `creator_id` bigint(20) unsigned NOT NULL COMMENT 'The creator id can be either the user id or the administrator id',
  `creator_type` tinyint(4) unsigned NOT NULL DEFAULT 0 COMMENT 'Creator type: 0- Administrator 1- User',
  `users_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Users count in the chat',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'create time',
  `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'update time',
  `deleted_at` datetime DEFAULT NULL COMMENT 'The deletion time is null before deletion. After deletion, the deletion time is recorded and the user will no longer be able to see this chat',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_hash` (`hash`) USING BTREE,
  KEY `idx_status_ctime` (`status`,`created_at`) USING BTREE,
  KEY `idx_utime` (`updated_at`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chat_relations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary id',
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'user id',
  `chat_id` bigint(20) unsigned NOT NULL COMMENT 'chat id',
  `unread_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'unread count',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'join time',
  `deleted_at` datetime DEFAULT NULL COMMENT 'quit time',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`user_id`) USING BTREE,
  KEY `idx_chat_id` (`chat_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chat_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary id',
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'user id',
  `chat_id` bigint(20) unsigned NOT NULL COMMENT 'chat id',
  `content` text NOT NULL COMMENT 'message content',
  `created_at` datetime NOT NULL COMMENT 'send time',
  `updated_at` datetime NOT NULL COMMENT 'message update time',
  `deleted_at` datetime DEFAULT NULL COMMENT 'message delete time',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`user_id`) USING BTREE,
  KEY `idx_cid_time` (`chat_id`,`created_at`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chat_message_read_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary id',
  `chat_id` bigint(20) unsigned NOT NULL COMMENT 'chat id',
  `message_id` bigint(20) unsigned NOT NULL COMMENT 'message id',
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'read user id',
  `created_at` datetime NOT NULL COMMENT 'read time',
  PRIMARY KEY (`id`),
  KEY `idx_cid` (`chat_id`) USING BTREE,
  KEY `idx_mid_uid` (`user_id`,`message_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;