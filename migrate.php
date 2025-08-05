<?php

// Define application root directory
define('APP_ROOT', __DIR__);

// Default migrations directory
$migrationsDir = './migrations';

// Handle 'new' command to create empty migration file
if (isset($argv[1]) && $argv[1] === 'new') {
    $filename = $migrationsDir . '/' . time() . '.sql';
    file_put_contents($filename, '');
    exit($filename . ' created!' . PHP_EOL);
}

// Register autoloader for classes
spl_autoload_register(function ($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require APP_ROOT . '/' . $path . '.php';
});

// Set default timezone from config
date_default_timezone_set(\Tools\Config::timezone);

// Check if migrations directory exists
if (!is_dir($migrationsDir)) {
    exit('Directory not found: ' . $migrationsDir);
}

// Database initialization
$dbName = \Tools\Config::database['dbname'];
$db = new \Tools\Database(false, false);
$databaseExists = $db->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$dbName}'")->find();

// Create database if it doesn't exist
if (empty($databaseExists)) {
    confirm('Database "' . $dbName . '" does not exist. Create it?');
    try {
        $isNewDatabase = true;
        $db->exec("CREATE DATABASE `$dbName`");
        $db = new \Tools\Database(true); // Reconnect with database selected
    } catch (Exception $e) {
        exit($e->getMessage());
    }
} else {
    $db = new \Tools\Database(true); // Reconnect with database selected
}

// Check existing tables in the database
$tables = $db->prepare("SELECT TABLE_NAME FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '{$dbName}'")->findAll();

if (!empty($tables)) {
    $tables = array_column($tables, 'TABLE_NAME');

    // Check if migrations table exists
    if (in_array('migrations', $tables)) {
        $history = $db->prepare('SELECT * FROM `migrations`')->findAll();

        if (empty($history)) {
            confirm("Migrations table is empty. Initialize database: $dbName? (WARNING: Data will be deleted!)");
            executeMigrations($migrationsDir);
        } else {
            $history = array_column($history, 'migration');
            executeMigrations($migrationsDir, $history);
        }
        exit('Migration completed successfully!' . PHP_EOL);
    } else {
        exit('Database contains tables but no migrations table. Using non-empty databases is unsafe. Please change dbname.' . PHP_EOL);
    }
} else {
    if (empty($isNewDatabase)) {
        confirm("Database \"{$dbName}\" is empty. Initialize it?");
    }
    executeMigrations($migrationsDir);
    exit('Database initialized successfully!');
}

/**
 * Prompt user for confirmation
 * @param string $question The confirmation question
 */
function confirm($question)
{
    echo $question . PHP_EOL . PHP_EOL;
    echo "Y[yes] N[no]\n";
    $input = strtolower(trim(fgets(STDIN)));
    if ($input !== 'y' && $input !== 'yes') {
        exit('Operation cancelled.' . PHP_EOL);
    }
}

/**
 * Execute migration files
 * @param string $path Path to migrations directory
 * @param array $except Files to exclude from execution
 */
function executeMigrations($path, $except = [])
{
    $db = new \Tools\Database();
    $files = scandir($path);

    foreach ($files as $file) {
        // Skip directories and non-SQL files
        if ($file === '.' || $file === '..' || !strpos($file, '.sql')) {
            continue;
        }

        // Skip excluded files
        if (in_array($file, $except)) {
            continue;
        }

        $sql = file_get_contents($path . '/' . $file);
        try {
            if ($db->prepare($sql)) {
                // Record migration in database
                $db->prepare('INSERT INTO `migrations`(`migration`) VALUE(:file)', [
                    'file' => $file
                ]);
            }
        } catch (Exception $e) {
            exit('Migration failed: ' . $e->getMessage() . PHP_EOL);
        }
    }
}