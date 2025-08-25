<?php require APP_ROOT . '/views/basic/head.view.php' ?>
<?php require APP_ROOT . '/views/admin/sidebar.view.php' ?>

<main>
    <div class="content">
        <h3>Chat Info</h3>

        <div class="card">
            <div class="flex flex-col">
                <div class="user-item">
                    <span>Name: </span>
                    <span><?= $chat['name'] ?></span>
                </div>
                <div class="user-item">
                    <span>Status: </span>
                    <?php if ($chat['deleted_at']): ?>
                        <span class="text-red">DELETED</span>
                    <?php elseif ($chat['status']): ?>
                        <span class="text-green">Normal</span>
                    <?php else: ?>
                        <span class="text-red">Closed</span>
                    <?php endif ?>
                </div>
                <div class="user-item">
                    <span>Creator: </span>
                    <span><?= $chat['creator'] ?></span>
                </div>
                <div class="user-item">
                    <span>Created time: </span>
                    <span><?= $chat['created_at'] ?></span>
                </div>
                <div class="user-item">
                    <span>Latest Message Sent time: </span>
                    <span><?= $chat['updated_at'] ?></span>
                </div>
                <div class="user-item">
                    <span>Members Count: </span>
                    <span><?= $chat['users_count'] ?></span>
                </div>
                <div class="user-item">
                    <?php if (!$chat['deleted_at']): ?>
                        <?php if ($chat['status']): ?>
                            <button class="action-btn action-btn-delete" onclick="lockChat()">Lock</button>
                        <?php else: ?>
                            <button class="action-btn" onclick="unLockChat()">UnLock</button>
                        <?php endif ?>
                        <button class="action-btn" onclick="manageMember()">編集します</button>

                        <button class="action-btn action-btn-delete" onclick="deleteChat()">Delete</button>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require APP_ROOT . '/views/admin/common/chat.model.view.php' ?>
<?php require APP_ROOT . '/views/admin/common/lock.modal.view.php' ?>
<script>
    const theChatId = Number(<?= $chat['id'] ?? '' ?>);
    const chatUserIds = JSON.parse('<?= $selected ?? '[]' ?>');
    const theChatName = '<?= $chat['name'] ?? '' ?>';
</script>
<script src="assets/js/chat/create.js"></script>
<script src="assets/js/admin/chat.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
