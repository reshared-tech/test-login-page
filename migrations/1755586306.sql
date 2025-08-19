ALTER TABLE `users`
ADD COLUMN `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'user status' AFTER `name`;