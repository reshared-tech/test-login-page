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
                    <th><?= __('CreatedAt') ?></th>
                    <th style="width:25rem"><?= __('Action') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data ?? [] as $datum): ?>
                    <tr>
                        <td><?= $datum['id'] ?></td>
                        <td><?= htmlspecialchars($datum['name']) ?></td>
                        <td><?= $datum['email'] ?></td>
                        <td><?= $datum['created_at'] ?></td>
                        <td>
                            <span class="tip"></span>
                            <button class="action-btn action-btn-edit new-chat"
                                    data-id="<?= $datum['id'] ?>"><?= __('New Chat') ?></button>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <?php require APP_ROOT . '/views/admin/paginator.view.php' ?>
    </div>
</main>

<script src="assets/js/chat/create.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
