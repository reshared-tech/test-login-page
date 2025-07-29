<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card">
            <div class="header">
                <h1 class="title" id="form-title">Welcome Back</h1>
                <p class="subtitle" id="form-subtitle">Please log in your account</p>
            </div>

            <form class="form" id="form" method="post" action="/?action=login">
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

                <button id="submit-btn" type="submit">Log in</button>

                <div class="footer">
                    <p>Don't have an account? <a href="javascript:;" class="link">Sign up</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="/assets/js/auth.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>