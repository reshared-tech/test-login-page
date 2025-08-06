<?php require APP_ROOT . '/views/basic/head.view.php' ?>
<?php require APP_ROOT . '/views/admin/sidebar.view.php' ?>

<main>
    <div class="content">
        <h3>Messages History</h3>

        <div class="table-container">
            <table class="table">
                <thead>
                <tr>
                    <th><?= __('ID') ?></th>
                    <th><?= __('Sender') ?></th>
                    <th><?= __('Content') ?></th>
                    <th><?= __('CreatedAt') ?></th>
                    <th><?= __('Read Users') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data ?? [] as $datum): ?>
                    <tr>
                        <td><?= $datum['id'] ?></td>
                        <td><?= htmlspecialchars($datum['username']) ?></td>
                        <td>
                            <pre><?= $datum['content'] ?></pre>
                        </td>
                        <td><?= $datum['created_at'] ?></td>
                        <td>
                            <pre><?= $datum['read_users'] ?></pre>
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
