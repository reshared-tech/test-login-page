<?php require APP_ROOT . '/views/basic/head.view.php' ?>
<?php require APP_ROOT . '/views/admin/sidebar.view.php' ?>

<main>
    <div class="content">
        <h3>Chats List</h3>
        <div class="actions">
            <button id="create-chat" class="action-btn action-btn-edit"><?= __('New Chat') ?></button>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                <tr>
                    <th><?= __('ID') ?></th>
                    <th><?= __('Chat Name') ?></th>
                    <th><?= __('Users') ?></th>
                    <th><?= __('Status') ?></th>
                    <th><?= __('CreatedAt') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data ?? [] as $datum): ?>
                    <tr>
                        <td><?= $datum['id'] ?></td>
                        <td><?= htmlspecialchars($datum['name']) ?></td>
                        <td>
                            <pre><?= $datum['users'] ?></pre>
                        </td>
                        <td><?= $datum['status'] ?></td>
                        <td><?= $datum['created_at'] ?></td>
                        <td>
                            <a class="link" href="admin/chat?id=<?= $datum['id'] ?>">チャットの記録です</a>
                            <a class="link" href="admin/chats/<?= $datum['id'] ?>">会話の詳細です</a>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <?php require APP_ROOT . '/views/admin/paginator.view.php' ?>
    </div>
</main>
<?php require APP_ROOT . '/views/admin/common/chat.model.view.php' ?>
<script src="assets/js/chat/create.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
