<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card">
            <!-- Logged-in state view -->
            <div class="header">
                <h1 class="title"><?= __('Welcome') ?></h1>
                <p class="subtitle"><?= __('Hi') ?>, <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
                <p><a href="/?action=logout" class="link"><?= __('Log out') ?></a></p>
            </div>
        </div>
    </div>

<?php require APP_ROOT . '/views/basic/foot.view.php' ?>