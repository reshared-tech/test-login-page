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
                    <?php if ($chat['status']): ?>
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
                    <span><button class="action-btn" onclick="manageMember(<?= $chat['id'] ?>)">管理メンバーリストです</button></span>
                </div>
            </div>
        </div>
    </div>
</main>
<div id="modal">
    <div class="content" style="min-height: auto">
        <div class="header">
            <p id="question"></p>
        </div>
        <div class="footer">
            <button onclick="confirm()" class="action-btn action-btn-delete">確認します</button>
            <button onclick="cancel()" class="action-btn">取り消します</button>
        </div>
    </div>
</div>
<script src="assets/js/admin/chat/create.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
