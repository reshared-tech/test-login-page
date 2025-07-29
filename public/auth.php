<?php
/**
 * User Authentication Handler
 * Handles user registration and login with session management
 */

session_start();

// Check if user is already logged in
if (isset($_SESSION['user'])) {
    // If POST data is empty, interpret as logout request
    if (empty($_POST)) {
        session_destroy();
        response(10000, 'You have been logged out');
    }
    response(10000, 'You are already logged in');
}

// Trim all POST input values
foreach ($_POST as $k => $item) {
    $_POST[$k] = trim($item);
}

// Determine if this is a registration or login request
if (isset($_POST['name'])) {
    handleRegistration();
} else {
    handleLogin();
}

/**
 * Handles user registration process
 */
function handleRegistration() {
    // Validate registration fields
    if (strlen($_POST['name']) === 0) {
        response(10001, 'Name must be between 1-255 characters');
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        response(10001, 'Invalid email format');
    }
    if (strlen($_POST['password']) < 6 || strlen($_POST['password']) > 200) {
        response(10001, 'Password must be 6-200 characters');
    }

    // Check for existing email
    $stmt = pdo()->prepare('SELECT `id` FROM `users` WHERE `email` = :email');
    $stmt->execute(['email' => $_POST['email']]);
    if ($stmt->fetch()) {
        response(10002, 'Email already registered. Please login');
    }

    // Create new user
    $pdo = pdo();
    $stmt = $pdo->prepare(
        'INSERT INTO users(`name`, `email`, `password`, `created_at`) 
         VALUES(:name, :email, :password, :now)'
    );

    $success = $stmt->execute([
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'now' => date('Y-m-d H:i:s'),
    ]);

    if ($success) {
        // Set session and return success
        $_SESSION['user'] = [
            'id' => $pdo->lastInsertId(),
            'name' => $_POST['name'],
            'email' => $_POST['email'],
        ];
        response(10000, 'Registration successful');
    }

    response(10001, 'Registration failed. Please contact administrator');
}

/**
 * Handles user login process
 */
function handleLogin() {
    // Validate login fields
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        response(10001, 'Invalid email format');
    }
    if (strlen($_POST['password']) < 6 || strlen($_POST['password']) > 200) {
        response(10001, 'Password must be 6-200 characters');
    }

    // Check if user exists
    $stmt = pdo()->prepare('SELECT * FROM `users` WHERE `email` = :email');
    $stmt->execute(['email' => $_POST['email']]);
    $user = $stmt->fetch();

    if (!$user) {
        response(10003, 'Email not registered. Please register first');
    }

    // Check account lock status
    if ($user['failed_count'] >= 10 && time() - strtotime($user['updated_at']) < 600) {
        response(10004, 'Account locked. Try again in 10 minutes');
    }

    // Verify password
    if (!password_verify($_POST['password'], $user['password'])) {
        // Increment failed attempt counter
        pdo()->prepare(
            'UPDATE users SET `failed_count` = `failed_count` + 1, `updated_at` = :now
             WHERE `id` = :id'
        )->execute([
            'now' => date('Y-m-d H:i:s'),
            'id' => $user['id']
        ]);
        response(10003, 'Incorrect password');
    }

    // Reset failed attempts on successful login
    if ($user['failed_count'] > 0) {
        pdo()->prepare(
            'UPDATE users SET `failed_count` = 0, `updated_at` = :now 
             WHERE `id` = :id'
        )->execute([
            'now' => date('Y-m-d H:i:s'),
            'id' => $user['id']
        ]);
    }

    // Set session and return success
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
    ];
    response(10000, 'Login successful');
}

/**
 * Returns JSON response and terminates script
 * @param int $code Response code
 * @param string $msg Response message
 */
function response($code, $msg) {
    header('Content-Type: application/json');
    exit(json_encode(compact('code', 'msg')));
}

/**
 * Creates and returns PDO database connection
 * @return PDO
 */
function pdo() {
    static $pdo = null;

    if (!$pdo) {
        $pdo = new PDO(
            'mysql:host=127.0.0.1;port=3306;dbname=testdb;charset=utf8mb4',
            'root',
            '123123',
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }

    return $pdo;
}