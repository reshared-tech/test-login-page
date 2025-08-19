<?php require APP_ROOT . '/views/basic/head.view.php' ?>
<?php require APP_ROOT . '/views/admin/sidebar.view.php' ?>

<main>
    <div class="content">
        <h3>Users List</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                <tr>
                    <th><?= __('ID') ?></th>
                    <th><?= __('Name') ?></th>
                    <th><?= __('Email') ?></th>
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
                        <td><?= $datum['email'] ?></td>
                        <td>
                            <?php if ($datum['status']): ?>
                                <span class="text-green">Normal</span>
                            <?php else: ?>
                                <span class="text-red">Locked</span>
                            <?php endif ?>
                        </td>
                        <td><?= $datum['created_at'] ?></td>
                        <td>
                            <a class="link" href="admin/users/<?= $datum['id'] ?>">view</a>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <?php require APP_ROOT . '/views/admin/paginator.view.php' ?>
    </div>
</main>

<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
