<?php
define('APP_ROOT', __DIR__);
spl_autoload_register(function ($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require APP_ROOT . '/' . $path . '.php';
});
date_default_timezone_set(\Tools\Config::timezone);

// Some functions
require APP_ROOT . '/functions.php';

// Create router instance
$router = new Tools\Router();

// Add routes
$router->get('/', [\App\Controllers\HomeController::class, 'index']);
$router->get('/login', [\App\Controllers\AuthController::class, 'login']);
$router->post('/login', [\App\Controllers\AuthController::class, 'loginSubmit']);
$router->get('/register', [\App\Controllers\AuthController::class, 'register']);
$router->post('/register', [\App\Controllers\AuthController::class, 'registerSubmit']);
$router->get('/profile', [\App\Controllers\HomeController::class, 'profile']);
$router->post('/profile', [\App\Controllers\HomeController::class, 'saveProfile']);
$router->get('/password', [\App\Controllers\HomeController::class, 'password']);
$router->post('/password', [\App\Controllers\HomeController::class, 'savePassword']);
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);
$router->get('/api/chats', [\App\Controllers\ChatController::class, 'chats']);
$router->post('/api/fetch', [\App\Controllers\ChatController::class, 'messages']);
$router->post('/api/messages', [\App\Controllers\ChatController::class, 'newMessage']);
$router->get('/chats', [\App\Controllers\ChatController::class, 'chat']);
$router->post('/api/upload', [\App\Controllers\ChatController::class, 'upload']);

$router->get('/admin', [\Admin\Controllers\HomeController::class, 'dashboard']);
$router->get('/admin/logout', [\Admin\Controllers\HomeController::class, 'logout']);
$router->get('/admin/users', [\Admin\Controllers\UserController::class, 'users']);
$router->get('/admin/chats', [\Admin\Controllers\ChatController::class, 'index']);
$router->get('/admin/chat', [\Admin\Controllers\ChatController::class, 'show']);
$router->post('/admin/chat', [\Admin\Controllers\ChatController::class, 'store']);

$router->dispatch();
