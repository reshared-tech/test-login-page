<?php

session_start();

if (isset($_SESSION['user'])) {
    if (empty($_POST)) {
        session_destroy();
        response(10000, 'Your have been log out');
    }

    response(10000, 'Your have been log in');
}

foreach ($_POST as $k => $item) {
    $_POST[$k] = trim($item);
}

// check is login or register
if (isset($_POST['name'])) {
    // is register request
    if (strlen($_POST['name']) === 0) {
        response(10001, 'The name filed length must between 0 - 255.');
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        response(10001, 'The email filed is invalid.');
    }
    if (strlen($_POST['password']) < 6 || strlen($_POST['password']) > 200) {
        response(10001, 'The password filed length must between 0 - 200.');
    }

    // check email repeat
    $stmt = pdo()->prepare('select `id` from `users` where `email` = :email');
    $stmt->execute([
        'email' => $_POST['email'],
    ]);
    $data = $stmt->fetch();
    if (!empty($data['id'])) {
        response(10002, 'This email address has been registered. Please log in directly');
    }

    // save data into database
    $pdo = pdo();
    $res = $pdo->prepare('insert into users(`name`, `email`, `password`, `created_at`) values(:name, :email, :password, :now)')->execute([
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'now' => date('Y-m-d H:i:s'),
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
    ]);
    if ($res) {
        $_SESSION['user'] = [
            'id' => $pdo->lastInsertId(),
            'name' => $_POST['name'],
            'email' => $_POST['email'],
        ];
        response(10000, 'ok');
    }

    response(10001, 'register fail! Please contact manager');
} else {
    // is login request
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        response(10001, 'The email filed is invalid.');
    }
    if (strlen($_POST['password']) < 6 || strlen($_POST['password']) > 200) {
        response(10001, 'The password filed length must between 0 - 200.');
    }

    // check email exists
    $stmt = pdo()->prepare('select * from `users` where `email` = :email');
    $stmt->execute([
        'email' => $_POST['email'],
    ]);
    $data = $stmt->fetch();
    if (empty($data)) {
        response(10003, 'Your email address has not been registered. Please register first');
    }

    // if failed times greater than 10, limit
    if ($data['failed_count'] >= 10 && time() - strtotime($data['updated_at']) < 10 * 60) {
        response(10004, 'Your account has been locked. Please try again in 10 minutes');
    }

    if (!password_verify($_POST['password'], $data['password'])) {
        // increment failed count
        pdo()->prepare('update users set `failed_count` = `failed_count` + 1, `updated_at` = :now')->execute([
            'now' => date('Y-m-d H:i:s'),
        ]);
        response(10003, 'Incorrect password');
    }

    if ($data['failed_count'] > 0) {
        // clear failed count
        pdo()->prepare('update users set `failed_count` = :count, `updated_at` = :now')->execute([
            'now' => date('Y-m-d H:i:s'),
            'count' => 0,
        ]);
    }

    $_SESSION['user'] = [
        'id' => $data['id'],
        'name' => $data['name'],
        'email' => $data['email'],
    ];
    response(10000, 'ok');
}


function response($code, $msg)
{
    header('Content-Type: application/json');
    exit(json_encode(compact('code', 'msg')));
}

function pdo()
{
    return new PDO('mysql:host=127.0.0.1;port=3306;dbname=testdb;charset=utf8mb4', 'root', '123123', [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}
