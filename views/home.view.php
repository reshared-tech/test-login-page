<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card mini-card">
            <!-- Logged-in state view -->
            <div class="header">
                <h1 class="title"><?= __('Welcome') ?></h1>
                <p class="subtitle"><?= __('Hi') ?>, <?= htmlspecialchars(\Tools\Auth::user('name')) ?></p>
                <p><a href="/logout" class="link"><?= __('Log out') ?></a></p>
            </div>

            <p id="all-unread" class="text">loading...</p>
            <div id="chats">

            </div>
        </div>
    </div>

<script src="/assets/js/chat_user.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>