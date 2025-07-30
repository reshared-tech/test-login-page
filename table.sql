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
  `email` varchar(255) NOT NULL COMMENT 'manager email',
  `password` varchar(255) NOT NULL COMMENT 'manager password',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'user register time',
  `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'last update time',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;