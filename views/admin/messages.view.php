<?php require APP_ROOT . '/views/basic/head.view.php' ?>
<?php require APP_ROOT . '/views/admin/sidebar.view.php' ?>

<main>
    <div class="content">
        <h3>Messages History</h3>

        <div class="table-container">
            <div id="messages">

            </div>
        </div>
    </div>
</main>
<script>const chatId = "<?= $chatId ?? ''; ?>";</script>
<script src="assets/js/admin/messages.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>
