<?php
define('APP_ROOT', dirname(__DIR__));

spl_autoload_register(function ($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require APP_ROOT . '/' . $path . '.php';
});

date_default_timezone_set('Asia/Shanghai');

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
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);
$router->get('/api/chats', [\App\Controllers\HomeController::class, 'chats']);
$router->post('/api/fetch', [\App\Controllers\HomeController::class, 'messages']);
$router->post('/api/messages', [\App\Controllers\HomeController::class, 'newMessage']);
$router->get('/chats', [\App\Controllers\HomeController::class, 'chat']);

$router->get('/admin/dashboard', [\Admin\Controllers\DashboardController::class, 'index']);
$router->get('/admin/logout', [\Admin\Controllers\DashboardController::class, 'logout']);
$router->post('/admin/chat', [\Admin\Controllers\ChatController::class, 'store']);

$router->dispatch();
