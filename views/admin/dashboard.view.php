<?php require APP_ROOT . '/views/basic/head.view.php' ?>

<div style="display: flex; padding: 1.2rem 2rem;background-color:white;justify-content: space-between;align-items: center">
    <span><?= __('Users list') ?></span>

    <span>
        <?= __('Hi Administrator') ?>,
        <?= htmlspecialchars(authorizedUser('name')) ?>,
        <a href="/admin/logout" class="link"><?= __('Log out') ?></a>
    </span>
</div>

<div style="padding: 1rem">
    <div class="card">
        <!-- Logged-in state view -->
        <div class="header">
            <h1 class="title"><?= __('Users list') ?></h1>
        </div>

        <div class="content">
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
                <span class="text">Per: (<?= $size ?>)</span>
                <span class="text">Total: <?= $total ?></span>
                <a class="nav-btn <?= $pre ? '' : 'disabled' ?>" href="/admin/dashboard?page=<?= $pre ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </a>

                <?php foreach($pages as $p): ?>
                    <?php if ($p === '...'): ?>
                        <div class="page-ellipsis">...</div>
                    <?php else: ?>
                        <a class="page-btn <?= $p == $page ? 'active' : '' ?>" href="/admin/dashboard?page=<?= $p ?>"><?= $p ?></a>
                    <?php endif ?>
                <?php endforeach ?>

                <a class="nav-btn <?= $next ? '' : 'disabled' ?>" href="/admin/dashboard?page=<?= $next ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/chat.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
