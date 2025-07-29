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