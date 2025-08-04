<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card middle-card">
            <div class="header">
                <h1 class="title"><?= __('Chat Room') ?></h1>
<!--                <p class="subtitle">--><?php //= $chat['name'] ?><!--</p>-->
            </div>

            <div id="messages">

            </div>

            <textarea id="input" class="textarea" placeholder="<?= __('Input your message here') ?>"></textarea>
            <p id="tip" class="tip"></p>
            <button id="send-btn" class="action-btn action-btn-edit"><?= __('Send') ?></button>
        </div>
    </div>

<script>const chatId = <?= $chat['id']; ?></script>
<script src="assets/js/chat/dialog.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>