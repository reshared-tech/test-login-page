<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card mini-card">
            <table class="table">
                <tbody>
                <tr>
                    <td>email</td>
                    <td><?= $user['email'] ?></td>
                </tr>
                <tr>
                    <td>name</td>
                    <td><?= $user['name'] ?></td>
                </tr>
                <tr>
                    <td>password</td>
                    <td><a class="link">change</a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

<script src="assets/js/chat/list.js"></script>
<?php require APP_ROOT . '/views/basic/foot.view.php' ?>