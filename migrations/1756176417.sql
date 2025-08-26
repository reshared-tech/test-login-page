CREATE TABLE `admin_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `admin_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT 'administrator id',
  `action` varchar(255) NOT NULL DEFAULT '' COMMENT 'action name',
  `from` text DEFAULT NULL COMMENT 'The original data of the impact',
  `to` text DEFAULT NULL COMMENT 'The new data of the impact',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'log time',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;