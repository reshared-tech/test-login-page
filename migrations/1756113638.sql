ALTER TABLE `chat_relations`
DROP INDEX `idx_uid`,
ADD UNIQUE INDEX `uniq_chat_user` (`user_id`,`chat_id`) USING BTREE;