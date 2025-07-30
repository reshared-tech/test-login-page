<?php require APP_ROOT . '/views/basic/head.view.php' ?>

<div style="display: flex; padding: 1.2rem 2rem;background-color:white;justify-content: space-between;align-items: center">
    <span><?= __('Users list') ?></span>

    <?php if (\Tools\Auth::isAuthorized()): ?>
        <span>
            <?= __('Hi manager') ?>,
            <?= htmlspecialchars(\Tools\Auth::user('name')) ?>,
            <a href="/admin/logout" class="link"><?= __('Log out') ?></a>
        </span>
    <?php endif ?>
</div>

<div style="padding: 1rem">
    <div class="card">
        <!-- Logged-in state view -->
        <div class="header">
            <h1 class="title"><?= __('Users list') ?></h1>
        </div>

        <div class="content">
            <div class="table-header">
                <span>Total: <?= $total ?></span>
                <span>Per page: <?= $size ?></span>
                <span>Current Page: <?= $page ?></span>
                <?php if ($pre): ?>
                    <a href="/?page=<?= $pre ?>" class="link">Preview page</a>
                <?php endif ?>
                <?php if ($next): ?>
                    <a href="/?page=<?= $next ?>" class="link">Next page</a>
                <?php endif ?>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th><?= __('ID') ?></th>
                    <th><?= __('Name') ?></th>
                    <th><?= __('Email') ?></th>
                    <th><?= __('CreatedAt') ?></th>
                    <th><?= __('Action') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users ?? [] as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['created_at'] ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
