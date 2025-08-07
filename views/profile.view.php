<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card mini-card">
            <div class="header">
                <h1 class="title"><?= __('Profile information') ?></h1>
                <p class="subtitle"><?= __('Update your name and email address') ?></p>
            </div>

            <form class="form" id="form" method="post" action="profile">
                <div class="item">
                    <label for="name" class="label"><?= __('Name') ?></label>
                    <input id="name" type="text" name="name" class="input" value="<?= $user['name'] ?>"
                           placeholder="<?= __('Please input your name.') ?>" required>
                    <p class="tip for-name"></p>
                </div>

                <div class="item">
                    <label for="email" class="label"><?= __('Email address') ?></label>
                    <input id="email" type="email" name="email" class="input" value="<?= $user['email'] ?>"
                           placeholder="<?= __('Please input your email address.') ?>" required>
                    <p class="tip for-email"></p>
                </div>

                <button id="submit-btn" type="submit"><?= __('Save') ?></button>
            </form>
        </div>
    </div>

<script src="assets/js/chat/profile.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>