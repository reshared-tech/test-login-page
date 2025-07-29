<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card">
            <div class="header">
                <h1 class="title" id="form-title"><?= __('Welcome Back') ?></h1>
                <p class="subtitle" id="form-subtitle"><?= __('Please log in your account') ?></p>
            </div>

            <form class="form" id="form" method="post" action="/?action=loginSubmit">
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

                <button id="submit-btn" type="submit"><?= __('Log in') ?></button>

                <div class="footer">
                    <p><?= __('Don\'t have an account?') ?> <a href="/?action=register" class="link"><?= __('Sign up') ?></a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="/assets/js/auth.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>