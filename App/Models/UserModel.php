<?php

namespace App\Models;

use Tools\Utils;

/**
 * Model class for handling user-related database operations
 * Inherits core CRUD utilities from BaseModel
 * Manages user authentication, profiles, logs, and account status
 */
class UserModel extends BaseModel
{
    /**
     * Encrypt a password using PHP's default password hashing algorithm
     *
     * @param string $password Plain text password to encrypt
     * @return string Hashed password
     */
    public function passwordEncrypt($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Retrieve a user record by email address
     *
     * @param string $email User's email address
     * @return mixed User record or null if not found
     */
    public function getUserByEmail($email)
    {
        return $this->database->prepare(
            'SELECT * FROM `users` WHERE `email` = :email',
            [
                'email' => $email
            ]
        )->find();
    }

    /**
     * Retrieve a user record by ID
     *
     * @param int $id User's ID
     * @return mixed User record or null if not found
     */
    public function getUserById($id)
    {
        return $this->database->prepare(
            'SELECT * FROM `users` WHERE `id` = :id',
            [
                'id' => $id
            ]
        )->find();
    }

    /**
     * Update a user record by ID
     *
     * @param int $id User ID to update
     * @param array $data Associative array of fields to update
     * @return mixed Database execution result or false on exception
     */
    public function updateById($id, $data)
    {
        try {
            // Generate UPDATE query and parameters via BaseModel method
            [$sql, $val] = $this->parseUpdate('users', $data);
            // Add user ID to parameters for WHERE clause
            $val['id'] = $id;
            // Execute query with WHERE clause to target specific user
            return $this->database->prepare($sql . ' WHERE `id` = :id', $val);
        } catch (\Exception $e) {
            // Return false if exception occurs (e.g., empty data)
            return false;
        }
    }

    /**
     * Log user actions (login attempts, etc.) with metadata
     *
     * @param int $userId User ID associated with the log
     * @param string $note Description of the action (e.g., "login failed 5")
     * @return mixed Database execution result
     */
    public function addUserLog($userId, $note)
    {
        return $this->database->prepare(
            'INSERT INTO `user_logs`(`user_id`, `user_agent`, `note`, `ip`, `created_at`) VALUES(:user_id, :user_agent, :note, :ip, :created_at)',
            [
                'user_id' => $userId,
                'user_agent' => Utils::getRequestAgent(),  // Get client's user agent
                'ip' => Utils::getRequestIp(),             // Get client's IP address
                'note' => $note,                           // Action description
                'created_at' => date('Y-m-d H:i:s'),       // Log timestamp
            ]
        );
    }

    /**
     * Reset failed login attempt counter for a user
     * Typically called after a successful login
     *
     * @param int $userId User ID to reset counter for
     * @return mixed Database execution result
     */
    public function cleanFailedCount($userId)
    {
        return $this->database->prepare(
            'UPDATE users SET `failed_count` = 0, `updated_at` = :now WHERE `id` = :id',
            [
                'id' => $userId,
                'now' => date('Y-m-d H:i:s'),  // Update timestamp
            ]
        );
    }

    /**
     * Increment failed login attempt counter for a user
     *
     * @param int $userId User ID to increment counter for
     * @return mixed Database execution result
     */
    public function incrementFailedCount($userId)
    {
        return $this->database->prepare(
            'UPDATE users SET `failed_count` = `failed_count` + 1, `updated_at` = :now WHERE `id` = :id',
            [
                'id' => $userId,
                'now' => date('Y-m-d H:i:s'),  // Update timestamp
            ]
        );
    }

    /**
     * Create a new user account
     *
     * @param string $name User's full name
     * @param string $email User's email address (used for login)
     * @param string $password Plain text password (will be encrypted)
     * @return int|bool New user's ID on success, false on failure
     */
    public function addUser($name, $email, $password)
    {
        // Insert new user with encrypted password
        $res = $this->database->prepare(
            'INSERT INTO users(`name`, `email`, `password`, `created_at`) VALUES(:name, :email, :password, :now)',
            [
                'name' => $name,
                'email' => $email,
                'password' => $this->passwordEncrypt($password),  // Encrypt password before storage
                'now' => date('Y-m-d H:i:s'),                     // Creation timestamp
            ]
        );

        // Return false if insertion failed
        if (!$res) {
            return false;
        }

        // Return auto-generated ID of the new user
        return $this->database->lastId();
    }

    /**
     * Get usernames for a list of user IDs (for display purposes)
     *
     * @param array $ids Array of user IDs
     * @return array Associative array (user_id => username)
     */
    public function getUsersNameByIds($ids)
    {
        // Return empty array if no user IDs are provided
        if (empty($ids)) {
            return [];
        }

        // Convert user IDs to comma-separated string for SQL IN clause
        $idStr = implode(',', $ids);
        // Fetch ID and name for each user
        $data = $this->database->prepare(
            'SELECT `id`,`name` FROM  `users` WHERE `id` in (' . $idStr . ')'
        )->findAll();

        // Return associative array mapping user IDs to usernames
        return array_column($data, 'name', 'id');
    }

    /**
     * Get full user information for a list of user IDs
     *
     * @param array $ids Array of user IDs
     * @return array List of user records
     */
    public function getUsersInfoByIds($ids)
    {
        // Return empty array if no user IDs are provided
        if (empty($ids)) {
            return [];
        }

        // Convert user IDs to comma-separated string for SQL IN clause
        $idStr = implode(',', $ids);
        // Fetch full user records for the provided IDs
        return $this->database->prepare(
            'SELECT * FROM  `users` WHERE `id` in (' . $idStr . ')'
        )->findAll();
    }

    /**
     * Get total number of user records in the database
     *
     * @return int Total user count
     */
    public function getUserTotal()
    {
        return $this->getTotal('users');
    }

    /**
     * Get paginated list of users
     *
     * @param int $page Page number (default: 1)
     * @param int $size Number of users per page (default: 10)
     * @return array List of user records (sorted by ID descending)
     */
    public function getUserList($page = 1, $size = 10)
    {
        return $this->getList('users', $page, $size);
    }

    /**
     * Get list of all active users (status > 0)
     * Returns basic user info (ID, name, email)
     *
     * @return array List of active user records (sorted by ID)
     */
    public function getUserAllList()
    {
        return $this->database->prepare(
            "SELECT `id`,`name`,`email` FROM `users` WHERE `status` > 0 ORDER BY `id`"
        )->findAll();
    }
}