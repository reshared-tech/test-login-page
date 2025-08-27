<?php

namespace Tools;

/**
 * Database connection and query handler class
 * Manages PDO connections and provides methods for database operations
 */
class Database
{
    /**
     * Static PDO instance for database connection
     * Shared across all Database class instances
     * @var \PDO
     */
    static $pdo;

    /**
     * PDO statement object for prepared queries
     * @var \PDOStatement
     */
    protected $stmt;

    /**
     * Database constructor: Initializes PDO connection if not already established
     *
     * @param bool $refresh Force reinitialization of PDO connection if true
     * @param bool $useDb Whether to include database name in connection parameters
     */
    public function __construct($refresh = false, $useDb = true)
    {
        // Create new PDO connection if none exists or refresh is forced
        if (!self::$pdo || $refresh) {
            // Get database configuration from Config class
            $config = Config::database;

            // Remove database name from config if $useDb is false
            if (!$useDb) {
                unset($config['dbname']);
            }

            // Initialize PDO connection with config parameters
            self::$pdo = new \PDO(
            // Build DSN string from config array (e.g., "mysql:host=127.0.0.1;port=3306;dbname=testdb")
                'mysql:' . http_build_query($config, '', ';'),
                $config['username'],
                $config['password'],
                [
                    // Set default fetch mode to associative array
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    // Enable exception mode for error handling
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]
            );
        }
    }

    /**
     * Prepare and execute a SQL query with parameters
     *
     * @param string $sql SQL query string with placeholders
     * @param array $attributes Associative array of parameter values
     * @return $this Instance of Database class for method chaining
     */
    public function prepare($sql, $attributes = [])
    {
        // Prepare SQL statement using PDO
        $this->stmt = self::$pdo->prepare($sql);

        // Execute prepared statement with provided parameters
        $this->stmt->execute($attributes);

        // Return instance for method chaining (e.g., prepare()->find())
        return $this;
    }

    /**
     * Execute a SQL query with parameters and return execution result
     *
     * @param string $sql SQL query string with placeholders
     * @param array $attributes Associative array of parameter values
     * @return bool True on successful execution, false otherwise
     */
    public function execute($sql, $attributes = [])
    {
        // Prepare SQL statement using PDO
        $this->stmt = self::$pdo->prepare($sql);

        // Execute and return result
        return $this->stmt->execute($attributes);
    }

    /**
     * Execute a SQL statement directly (for queries without parameters)
     *
     * @param string $sql SQL query string
     * @return int Number of rows affected by the query
     */
    public function exec($sql)
    {
        return self::$pdo->exec($sql);
    }

    /**
     * Fetch a single row from the result set
     *
     * @return array|false Associative array of row data or false if no row found
     */
    public function find()
    {
        return $this->stmt->fetch();
    }

    /**
     * Fetch all rows from the result set
     *
     * @return array Array of associative arrays containing row data
     */
    public function findAll()
    {
        return $this->stmt->fetchAll();
    }

    /**
     * Get the ID of the last inserted row
     *
     * @return string|false Last insert ID as string or false on failure
     */
    public function lastId()
    {
        return self::$pdo->lastInsertId();
    }
}