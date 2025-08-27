<?php

// Define the root directory of the application (points to the directory containing this migration script)
define('APP_ROOT', __DIR__);

// Set the default directory where SQL migration files are stored
$migrationsDir = './migrations';

// Handle the 'new' command: Create an empty SQL migration file with timestamp in the filename
// Triggered when running the script with "php migrate.php new"
if (isset($argv[1]) && $argv[1] === 'new') {
    // Generate filename with current Unix timestamp (ensures unique migration files)
    $filename = $migrationsDir . '/' . time() . '.sql';
    // Create empty SQL file
    file_put_contents($filename, '');
    // Exit and print confirmation message with the created file path
    exit($filename . ' created!' . PHP_EOL);
}

// Register a custom autoloader to automatically load required classes (e.g., Tools\Database)
spl_autoload_register(function ($class) {
    // Convert namespace backslashes (\) to OS-specific directory separators (e.g., / on Linux, \ on Windows)
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    // Require the class file from the application root directory
    require APP_ROOT . '/' . $path . '.php';
});

// Set the application's default timezone using the value configured in Tools\Config
date_default_timezone_set(\Tools\Config::timezone);

// Check if the migrations directory exists; exit with error if not found
if (!is_dir($migrationsDir)) {
    exit('Directory not found: ' . $migrationsDir);
}

// ------------------------------
// Database Initialization Logic
// ------------------------------
// Get the target database name from the configuration
$dbName = \Tools\Config::database['dbname'];
// Create Database instance WITHOUT selecting a specific database (second parameter = false)
$db = new \Tools\Database(false, false);

// Check if the target database already exists (query INFORMATION_SCHEMA)
$databaseExists = $db->prepare(
    "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$dbName}'"
)->find();

// If the database does NOT exist: prompt user to create it
if (empty($databaseExists)) {
    confirm('Database "' . $dbName . '" does not exist. Create it?');
    try {
        // Flag to indicate this is a newly created database
        $isNewDatabase = true;
        // Execute SQL to create the database
        $db->exec("CREATE DATABASE `$dbName`");
        // Reconnect to the Database instance WITH the new database selected (refresh connection)
        $db = new \Tools\Database(true);
    } catch (Exception $e) {
        // Exit with error message if database creation fails
        exit($e->getMessage());
    }
} else {
    // If database exists: reconnect WITH the database selected
    $db = new \Tools\Database(true);
}

// ------------------------------
// Migration Execution Logic
// ------------------------------
// Get list of all existing tables in the target database
$tables = $db->prepare(
    "SELECT TABLE_NAME FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '{$dbName}'"
)->findAll();

// If the database has existing tables
if (!empty($tables)) {
    // Extract table names from the query result (convert to [table1, table2, ...])
    $tables = array_column($tables, 'TABLE_NAME');

    // Check if the "migrations" table exists (tracks executed migration files)
    if (in_array('migrations', $tables)) {
        // Get history of already executed migrations from the "migrations" table
        $history = $db->prepare('SELECT * FROM `migrations`')->findAll();

        // If "migrations" table is empty (no migrations run yet)
        if (empty($history)) {
            // Prompt user for confirmation (warns about potential data loss)
            confirm("Migrations table is empty. Initialize database: $dbName? (WARNING: Data will be deleted!)");
            // Execute all migration files in the migrations directory
            executeMigrations($migrationsDir);
        } else {
            // Extract filenames of already executed migrations (to avoid re-running them)
            $history = array_column($history, 'migration');
            // Execute only migration files that haven't been run yet
            executeMigrations($migrationsDir, $history);
        }
        // Exit with success message after migration completes
        exit('Migration completed successfully!' . PHP_EOL);
    } else {
        // Exit with error if database has tables but no "migrations" table (unsafe to proceed)
        exit('Database contains tables but no migrations table. Using non-empty databases is unsafe. Please change dbname.' . PHP_EOL);
    }
} else {
    // If database is empty (no tables) and not newly created: prompt for initialization
    if (empty($isNewDatabase)) {
        confirm("Database \"{$dbName}\" is empty. Initialize it?");
    }
    // Execute all migration files to set up initial database structure
    executeMigrations($migrationsDir);
    // Exit with success message after initialization
    exit('Database initialized successfully!');
}

/**
 * Prompt the user for confirmation before performing a critical operation
 * Halts execution if the user declines (inputs anything other than Y/yes)
 *
 * @param string $question The confirmation message to display to the user
 */
function confirm($question)
{
    // Print the confirmation question
    echo $question . PHP_EOL . PHP_EOL;
    // Prompt user for input (Y = yes, N = no)
    echo "Y[yes] N[no]\n";
    // Read and normalize user input (trim whitespace, convert to lowercase)
    $input = strtolower(trim(fgets(STDIN)));
    // If input is not "y" or "yes": exit and cancel the operation
    if ($input !== 'y' && $input !== 'yes') {
        exit('Operation cancelled.' . PHP_EOL);
    }
}

/**
 * Execute SQL migration files from the specified directory
 * Skips already executed files (via $except array) and non-SQL files
 * Records executed migrations in the "migrations" table
 *
 * @param string $path Path to the directory containing migration files
 * @param array $except Array of migration filenames to skip (already executed)
 */
function executeMigrations($path, $except = [])
{
    // Create a new Database instance to run migration queries
    $db = new \Tools\Database();
    // Get list of all files/directories in the migrations directory
    $files = scandir($path);

    // Iterate through each file in the migrations directory
    foreach ($files as $file) {
        // Skip current directory (.), parent directory (..), and non-SQL files
        if ($file === '.' || $file === '..' || !strpos($file, '.sql')) {
            continue;
        }

        // Skip files that are already in the executed migrations list ($except)
        if (in_array($file, $except)) {
            continue;
        }

        // Read the content of the SQL migration file
        $sql = file_get_contents($path . '/' . $file);
        try {
            // Execute the SQL in the migration file
            if ($db->prepare($sql)) {
                // Record the successful migration in the "migrations" table (tracks executed files)
                $db->prepare(
                    'INSERT INTO `migrations`(`migration`) VALUE(:file)',
                    [
                        'file' => $file
                    ]
                );
            }
        } catch (Exception $e) {
            // Exit with error message if the migration fails
            exit('Migration failed: ' . $e->getMessage() . PHP_EOL);
        }
    }
}