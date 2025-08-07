TRUNCATE `chat_message_read_logs`;

ALTER TABLE `chat_message_read_logs`
DROP INDEX `idx_mid_uid`,
ADD UNIQUE INDEX `idx_mid_uid` (`user_id`,`message_id`) USING BTREE;