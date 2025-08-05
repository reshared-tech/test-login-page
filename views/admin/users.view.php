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
                <?php foreach ($users ?? [] as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['created_at'] ?></td>
                        <td>
                            <span class="tip"></span>
                            <button class="action-btn action-btn-edit new-chat"
                                    data-id="<?= $user['id'] ?>"><?= __('New Chat') ?></button>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span>Per: (<?= $size ?>)</span>
            <span>Total: <?= $total ?></span>
            <a class="nav-btn <?= $pre ? '' : 'disabled' ?>" href="admin/users?page=<?= $pre ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </a>

            <?php foreach ($pages as $p): ?>
                <?php if ($p === '...'): ?>
                    <div class="page-ellipsis">...</div>
                <?php else: ?>
                    <a class="page-btn <?= $p == $page ? 'active' : '' ?>"
                       href="admin/users?page=<?= $p ?>"><?= $p ?></a>
                <?php endif ?>
            <?php endforeach ?>

            <a class="nav-btn <?= $next ? '' : 'disabled' ?>" href="admin/users?page=<?= $next ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        </div>
    </div>
</main>

<script src="assets/js/chat/create.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
