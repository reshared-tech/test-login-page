<?php

define('APP_ROOT', __DIR__);

$path = './migrations';

if (isset($argv[1]) && $argv[1] === 'new') {
    $name = $path . '/' . time() . '.sql';
    file_put_contents($name, '');
    exit($name . ' created!' . PHP_EOL);
}

spl_autoload_register(function ($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require APP_ROOT . '/' . $path . '.php';
});

date_default_timezone_set(\Tools\Config::timezone);

if (!is_dir($path)) {
    exit('no dir ' . $path . ' founded');
}

$db = new \Tools\Database();
$dbName = \Tools\Config::database['dbname'];
$tables = $db->prepare("SELECT TABLE_NAME FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '{$dbName}'")->findAll();
if (!empty($tables)) {
    $tables = array_column($tables, 'TABLE_NAME');
    if (in_array('migrations', $tables)) {
        $history = $db->prepare('SELECT * FROM `migrations`')->findAll();
        if (empty($history)) {
            confirm("Migrations data is empty. Are you sure to init this database: $dbName? (!!data would be delete!!)");
            execute($path);
        } else {
            $history = array_column($history, 'migration');
            execute($path, $history);
        }
        exit('Successful!' . PHP_EOL);
    } else {
        exit('This database is not empty. Non-empty databases cannot be used; otherwise, it will cause data insecurity. Please change the dbname' . PHP_EOL);
    }
} else {
    confirm("This is a empty database. Are you sure to start with db: {$dbName}?\n");
    execute($path);
    exit('Done!');
}

function confirm($question)
{
    echo $question . PHP_EOL . PHP_EOL;
    echo "Y[yes] N[no]\n";
    $input = strtolower(trim(fgets(STDIN)));
    if ($input === 'y' || $input === 'yes') {
        return;
    } else {
        exit('nothing happen!' . PHP_EOL);
    }
}

function execute($path, $except = [])
{
    $db = new \Tools\Database();
    $files = scandir($path);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        if (!strpos($file, '.sql')) {
            continue;
        }
        if (in_array($file, $except)) {
            continue;
        }
        $sql = file_get_contents($path . '/' . $file);
        try {
            if ($db->prepare($sql)) {
                $db->prepare('INSERT INTO `migrations`(`migration`) VALUE(:file)', [
                    'file' => $file
                ]);
            }
        } catch (Exception $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }
}