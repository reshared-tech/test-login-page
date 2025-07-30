<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card auth-card">
            <div class="header">
                <h1 class="title" id="form-title"><?= __('Welcome') ?></h1>
                <p class="subtitle" id="form-subtitle"><?= __('Register a new account') ?></p>
            </div>

            <form class="form" id="form" method="post" action="/auth/registerSubmit">
                <div class="item">
                    <label for="name" class="label"><?= __('Name') ?></label>
                    <input id="name" type="text" name="name" class="input"
                           placeholder="<?= __('Please input your name.') ?>" required>
                    <p class="tip for-name"></p>
                </div>

                <div class="item">
                    <label for="email" class="label"><?= __('Email address') ?></label>
                    <input id="email" type="email" name="email" class="input"
                           placeholder="<?= __('Please input your email address.') ?>" required>
                    <p class="tip for-email"></p>
                </div>

                <div class="item">
                    <label for="password" class="label"><?= __('Password') ?></label>
                    <input id="password" type="password" name="password" class="input"
                           placeholder="<?= __('Please input your password.') ?>" required minlength="6">
                    <p class="tip for-password"></p>
                </div>

                <div class="item">
                    <label for="confirm_password" class="label"><?= __('Confirm password') ?></label>
                    <input id="confirm_password" type="password" name="confirm_password" class="input"
                           placeholder="<?= __('Please repeat your password.') ?>">
                    <p class="tip for-confirm_password"></p>
                </div>

                <button id="submit-btn" type="submit"><?= __('Sign up') ?></button>

                <div class="footer">
                    <p><?= __('Already have an account?') ?> <a href="/auth/login" class="link"><?= __('Log in') ?></a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="/assets/js/auth.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>