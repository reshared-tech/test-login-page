<?php
/**
 * Define the root directory of the application
 * __DIR__ refers to the directory where this entry file is located
 */
define('APP_ROOT', __DIR__);

/**
 * Register a custom autoloader for automatic class loading
 * Converts namespace paths (with backslashes) to file system paths (with directory separators)
 * Loads the corresponding PHP file when a class is accessed
 */
spl_autoload_register(function ($class) {
    // Replace namespace backslashes (\) with OS-specific directory separators (e.g., / on Linux, \ on Windows)
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    // Require the class file from the application root directory
    require APP_ROOT . '/' . $path . '.php';
});

/**
 * Set the default timezone for the application
 * Uses the timezone configured in the Tools\Config class (e.g., Asia/Shanghai)
 */
date_default_timezone_set(\Tools\Config::timezone);

// Load common helper functions (e.g., dd(), view(), redirect())
require APP_ROOT . '/functions.php';

// Create a new instance of the Router class to handle route registration and dispatching
$router = new Tools\Router();

// ------------------------------
// Register USER-SIDE Routes
// ------------------------------

// Home page: Show the user's chat list (requires authentication)
$router->get('/', [\App\Controllers\HomeController::class, 'index']);

// Login: Show login page (GET) and handle login submission (POST)
$router->get('/login', [\App\Controllers\AuthController::class, 'login']);
$router->post('/login', [\App\Controllers\AuthController::class, 'loginSubmit']);

// Register: Show registration page (GET) and handle registration submission (POST)
$router->get('/register', [\App\Controllers\AuthController::class, 'register']);
$router->post('/register', [\App\Controllers\AuthController::class, 'registerSubmit']);

// Profile: Show user profile page (GET) and handle profile update (POST)
$router->get('/profile', [\App\Controllers\ProfileController::class, 'profile']);
$router->post('/profile', [\App\Controllers\ProfileController::class, 'saveProfile']);

// Password: Show password update page (GET) and handle password change (POST)
$router->get('/password', [\App\Controllers\ProfileController::class, 'password']);
$router->post('/password', [\App\Controllers\ProfileController::class, 'savePassword']);

// Logout: Destroy user session and redirect to home/login
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);

// Chat API: Fetch chat messages (POST) and send new messages (POST)
$router->post('/api/fetch', [\App\Controllers\ChatController::class, 'messages']);
$router->post('/api/messages', [\App\Controllers\ChatController::class, 'newMessage']);

// Chat Room: Show a specific chat room (requires chat hash via GET parameter "h")
$router->get('/chats', [\App\Controllers\ChatController::class, 'chat']);

// Chat File Upload: Handle image uploads for chat messages (POST)
$router->post('/api/upload', [\App\Controllers\ChatController::class, 'upload']);

// ------------------------------
// Register ADMIN-SIDE Routes
// ------------------------------

// Admin Dashboard: Show admin home page (requires admin authentication)
$router->get('/admin', [\Admin\Controllers\HomeController::class, 'dashboard']);

// Admin Logout: Destroy admin session and redirect
$router->get('/admin/logout', [\Admin\Controllers\HomeController::class, 'logout']);

// Admin User Management: Show user list (GET) and user details (GET with dynamic user ID)
$router->get('/admin/users', [\Admin\Controllers\UserController::class, 'index']);
$router->get('/admin/users/:id', [\Admin\Controllers\UserController::class, 'show']);

// Admin Logs: Show system/action logs (e.g., login attempts, user locks)
$router->get('/admin/logs', [\Admin\Controllers\HomeController::class, 'logs']);

// Admin User API: Fetch all users (for dropdowns/selects) and lock/unlock users (POST)
$router->get('/admin/api/users', [\Admin\Controllers\UserController::class, 'usersApiList']);
$router->post('/admin/api/users/lock', [\Admin\Controllers\UserController::class, 'lockUser']);

// Admin Chat API: Lock/unlock chat rooms (POST) and delete chats (DELETE with dynamic chat ID)
$router->post('/admin/api/chats/lock', [\Admin\Controllers\ChatController::class, 'LockChat']);
$router->delete('/admin/api/chats/:id', [\Admin\Controllers\ChatController::class, 'delete']);

// Admin Chat Management: Show chat list (GET), chat messages (GET), and specific chat details (GET with dynamic chat ID)
$router->get('/admin/chats', [\Admin\Controllers\ChatController::class, 'index']);
$router->get('/admin/chat', [\Admin\Controllers\ChatController::class, 'messages']);
$router->get('/admin/chats/:id', [\Admin\Controllers\ChatController::class, 'show']);

// Admin Chat Creation: Handle new chat room creation (POST)
$router->post('/admin/chat', [\Admin\Controllers\ChatController::class, 'store']);

// ------------------------------
// Dispatch the Router
// Match the current request to a registered route and execute the corresponding controller method
// ------------------------------
$router->dispatch();
