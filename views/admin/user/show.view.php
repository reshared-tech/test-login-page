<?php require APP_ROOT . '/views/basic/head.view.php' ?>
<?php require APP_ROOT . '/views/admin/sidebar.view.php' ?>

<main>
    <div class="content">
        <h3>User Info</h3>

        <div class="card">
            <div class="flex flex-col">
                <div class="user-item">
                    <span>Avatar: </span>
                    <span class="avatar"><?= $user['avatar'] ?></span>
                </div>
                <div class="user-item">
                    <span>Name: </span>
                    <span><?= $user['name'] ?></span>
                </div>
                <div class="user-item">
                    <span>Email: </span>
                    <span><?= $user['email'] ?></span>
                </div>
                <div class="user-item">
                    <span>Status: </span>
                    <?php if ($user['status']): ?>
                        <span class="text-green">Normal</span>
                    <?php else: ?>
                        <span class="text-red">Locked</span>
                    <?php endif ?>
                </div>
                <div class="user-item">
                    <span>Password: </span>
                    <span>***</span>
                </div>
                <div class="user-item">
                    <span>Registration time: </span>
                    <span><?= $user['created_at'] ?></span>
                </div>
                <div class="user-item">
                    <span>Latest login time: </span>
                    <span><?= $user['updated_at'] ?></span>
                </div>
                <div class="user-item">
                    <span>Failed Count Last time: </span>
                    <span><?= $user['failed_count'] ?></span>
                </div>
                <div class="user-item">
                    <?php if ($user['status']): ?>
                        <button class="action-btn action-btn-delete" onclick="lockUser(<?= $user['id'] ?>)">Lock
                        </button>
                    <?php else: ?>
                        <button class="action-btn" onclick="unLockUser(<?= $user['id'] ?>)">UnLock
                        </button>
                    <?php endif ?>
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
<script src="assets/js/admin/user.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
