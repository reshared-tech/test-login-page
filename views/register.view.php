<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card">
            <div class="header">
                <h1 class="title" id="form-title">Welcome</h1>
                <p class="subtitle" id="form-subtitle">Register a new account</p>
            </div>

            <form class="form" id="form" method="post" action="/?action=register">
                <div class="item">
                    <label for="name" class="label">Name</label>
                    <input id="name" type="text" name="name" class="input"
                           placeholder="Please input your name.">
                    <p class="tip for-name"></p>
                </div>

                <div class="item">
                    <label for="email" class="label">Email address</label>
                    <input id="email" type="email" name="email" class="input"
                           placeholder="Please input your email address." required>
                    <p class="tip for-email"></p>
                </div>

                <div class="item">
                    <label for="password" class="label">Password</label>
                    <input id="password" type="password" name="password" class="input"
                           placeholder="Please input your password." required minlength="6">
                    <p class="tip for-password"></p>
                </div>

                <div class="item">
                    <label for="confirm_password" class="label">Confirm password</label>
                    <input id="confirm_password" type="password" name="confirm_password" class="input"
                           placeholder="Please repeat your password.">
                    <p class="tip for-confirm_password"></p>
                </div>

                <button id="submit-btn" type="submit">Sign up</button>

                <div class="footer">
                    <p>Already have an account? <a href="/login" class="link">Log in</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="/assets/js/auth.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>