<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card mini-card">
            <div class="header">
                <h1 class="title"><?= __('Update password') ?></h1>
                <p class="subtitle"><?= __('Ensure your account is using a long, random password to stay secure') ?></p>
            </div>

            <form class="form" id="form" method="post" action="password">
                <div class="item">
                    <label for="current_password" class="label"><?= __('Current Password') ?></label>
                    <input id="current_password" type="password" name="current_password" class="input"
                           placeholder="<?= __('Please input your current password.') ?>" required minlength="6">
                    <p class="tip for-current-password"></p>
                </div>

                <div class="item">
                    <label for="password" class="label"><?= __('New Password') ?></label>
                    <input id="password" type="password" name="password" class="input"
                           placeholder="<?= __('Please input your new password.') ?>" required minlength="6">
                    <p class="tip for-password"></p>
                </div>

                <div class="item">
                    <label for="confirm_password" class="label"><?= __('Confirm password') ?></label>
                    <input id="confirm_password" type="password" name="confirm_password" class="input"
                           placeholder="<?= __('Please repeat your new password.') ?>">
                    <p class="tip for-confirm_password"></p>
                </div>

                <button id="submit-btn" type="submit"><?= __('Save') ?></button>
            </form>
        </div>
    </div>

<script src="assets/js/chat/password.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>