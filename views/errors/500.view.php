<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card">
            <!-- Logged-in state view -->
            <div class="header">
                <h1 class="title">500</h1>
                <p class="subtitle"><?= __('Oops, 500') ?></p>
                <p><?= $message ?? 'Something Wrong!' ?></p>
                <p><a href="/" class="link"><?= __('Go Home') ?></a></p>
            </div>
        </div>
    </div>

<?php require APP_ROOT . '/views/basic/foot.view.php' ?>