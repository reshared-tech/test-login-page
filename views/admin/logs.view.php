<?php require APP_ROOT . '/views/basic/head.view.php' ?>
<?php require APP_ROOT . '/views/admin/sidebar.view.php' ?>

<main>
    <div class="content">
        <h3>Administrator Action Logs</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                <tr>
                    <th><?= __('Administrator') ?></th>
                    <th><?= __('Action') ?></th>
                    <th><?= __('Time') ?></th>
                    <th><?= __('Detail') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data ?? [] as $datum): ?>
                    <tr>
                        <td><?= $datum['admin'] ?></td>
                        <td><?= $datum['action'] ?></td>
                        <td><?= $datum['created_at'] ?></td>
                        <td><?= htmlspecialchars($datum['detail'] ?? '') ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <?php require APP_ROOT . '/views/admin/paginator.view.php' ?>
    </div>
</main>

<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
