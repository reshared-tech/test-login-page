<?php require APP_ROOT . '/views/basic/head.view.php' ?>
<?php require APP_ROOT . '/views/admin/sidebar.view.php' ?>

<main>
    <div class="content">
        <h3>Chats List</h3>

        <div class="table-container">
            <table class="table">
                <thead>
                <tr>
                    <th><?= __('ID') ?></th>
                    <th><?= __('Chat Name') ?></th>
                    <th><?= __('Users') ?></th>
                    <th><?= __('Status') ?></th>
                    <th><?= __('CreatedAt') ?></th>
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
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <?php require APP_ROOT . '/views/admin/paginator.view.php' ?>
    </div>
</main>

<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
