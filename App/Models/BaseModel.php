<?php

namespace App\Models;

use Exception;
use Tools\Database;

/**
 * Abstract base model class providing core database operations
 * Serves as a parent class for all application models
 * Implements common CRUD utilities (insert, update, list, total count)
 */
abstract class BaseModel
{
    /**
     * Database connection instance
     * @var Database
     */
    protected $database;

    /**
     * BaseModel constructor: Initializes database connection
     * Automatically called when child models are instantiated
     */
    public function __construct()
    {
        // Create new Database instance to handle database interactions
        $this->database = new Database();
    }

    /**
     * Generate UPDATE query and parameter values from data array
     *
     * @param string $table Database table name to update
     * @param array $data Associative array of field-value pairs to update
     * @return array [SQL query string, parameter values array]
     * @throws Exception If update data is empty
     */
    protected function parseUpdate($table, $data)
    {
        // Throw exception if no data is provided for update
        if (empty($data)) {
            throw new Exception('Update data is empty');
        }

        $keys = [];      // Stores SET clause fragments (e.g., "`name` = :name")
        $values = [];    // Stores parameter values (e.g., [":name" => "John"])

        // Build SET clauses and parameter values from data array
        foreach ($data as $k => $v) {
            $keys[] = "`$k` = :$k";         // Create SET fragment with parameter placeholder
            $values[":{$k}"] = $v;          // Map parameter placeholder to value
        }

        // Combine SET fragments into a single string (e.g., "`name` = :name, `age` = :age")
        $keyStr = implode(',', $keys);

        // Return UPDATE query and parameter values
        return ["UPDATE `$table` SET $keyStr", $values];
    }

    /**
     * Generate INSERT query and parameter values from data array
     *
     * @param string $table Database table name to insert into
     * @param array $data Associative array of field-value pairs to insert
     * @return array [SQL query string, parameter values array]
     * @throws Exception If insert data is empty
     */
    protected function parseInsert($table, $data)
    {
        // Throw exception if no data is provided for insertion
        if (empty($data)) {
            throw new Exception('Inserts data is empty');
        }

        $value = [];    // Stores parameter placeholders (e.g., [":name", ":email"])
        $result = [];   // Stores parameter values (e.g., ["name" => "John", "email" => "john@example.com"])

        // Build parameter placeholders and values from data array
        foreach ($data as $key => $val) {
            $value[] = ":$key";             // Create parameter placeholder
            $result[$key] = $val;           // Map key to value
        }

        // Format field names as comma-separated quoted strings (e.g., "`name`,`email`")
        $fields = '`' . implode('`,`', array_keys($data)) . '`';
        // Combine parameter placeholders into comma-separated string (e.g., ":name,:email")
        $value = implode(',', $value);

        // Construct INSERT query
        $sql = "INSERT INTO `$table`($fields) VALUE ($value)";

        // Return INSERT query and parameter values
        return [$sql, $result];
    }

    /**
     * Generate bulk INSERT query for multiple records
     * Ensures all records have identical field structures
     *
     * @param string $table Database table name to insert into
     * @param array $data Array of associative arrays (each representing a record)
     * @return array [SQL query string, parameter values array]
     * @throws Exception If data is empty or records have inconsistent fields
     */
    protected function parseInserts($table, $data)
    {
        // Throw exception if no data is provided for bulk insertion
        if (empty($data)) {
            throw new Exception('Inserts data is empty');
        }

        $fields = '';       // Stores field names (e.g., "`name`,`email`")
        $values = [];       // Stores row parameter groups (e.g., [ "(:name0,:email0)", "(:name1,:email1)" ])
        $result = [];       // Stores parameter values with unique keys (e.g., ["name0" => "John", "email0" => "john@example.com"])

        // Process each record to build query components
        foreach ($data as $k => $items) {
            // Sort fields to ensure consistent order across all records
            ksort($items);
            // Format current record's fields as quoted strings (e.g., "`name`,`email`")
            $keys = '`' . implode('`,`', array_keys($items)) . '`';

            // Set base fields from first record
            if ($fields === '') {
                $fields = $keys;
            } // Throw exception if subsequent records have different fields
            elseif ($fields !== $keys) {
                throw new Exception('Inserts data has different key');
            }

            $value = [];
            // Build unique parameter placeholders for each field in the record
            foreach ($items as $key => $val) {
                $paramKey = $key . $k;              // Create unique key (e.g., "name0" for first record's name)
                $value[] = ":$paramKey";            // Create parameter placeholder
                $result[$paramKey] = $val;          // Map unique key to value
            }
            // Combine parameters for this record into a grouped string (e.g., "(:name0,:email0)")
            $values[$k] = '(' . implode(',', $value) . ')';
        }

        // Combine all row parameter groups into comma-separated string
        $valStr = implode(',', $values);

        // Construct bulk INSERT query with IGNORE to skip duplicate entries
        $sql = "INSERT IGNORE INTO `$table`($fields) VALUES $valStr";

        // Return bulk INSERT query and parameter values
        return [$sql, $result];
    }

    /**
     * Get total number of records in a table
     *
     * @param string $table Database table name
     * @return int Total number of records
     */
    protected function getTotal($table)
    {
        // Execute COUNT query to get total records
        $res = $this->database->prepare("SELECT count(*) as `total` FROM `$table`")->find();

        // Return total as integer (cast result to ensure numeric value)
        return (int)$res['total'];
    }

    /**
     * Get paginated list of records from a table
     * Sorts records by ID in descending order (newest first)
     *
     * @param string $table Database table name
     * @param int $page Page number (default: 1)
     * @param int $size Number of records per page (default: 10)
     * @return array List of records matching pagination criteria
     */
    protected function getList($table, $page = 1, $size = 10)
    {
        // Calculate offset for pagination (number of records to skip)
        $offset = ($page - 1) * $size;

        // Execute paginated SELECT query (sorted by ID descending)
        return $this->database->prepare("SELECT * FROM `$table` ORDER BY `id` DESC LIMIT $offset,$size")->findAll();
    }
}