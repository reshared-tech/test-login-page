<?php

namespace Tools;

/**
 * Application configuration class
 * Stores system-wide configuration constants (database, paths, limits, etc.)
 */
class Config
{
    /**
     * Database connection configuration
     * Contains credentials and connection details for MySQL database
     */
    const database = [
        'host' => '',       // Database server hostname/IP
        'port' => 3306,     // Database server port (default MySQL port)
        'dbname' => '',     // Database name
        'username' => '',   // Database user username
        'password' => '',   // Database user password
    ];

    /**
     * Default application timezone
     * Used for date/time functions across the application
     */
    const timezone = 'Asia/Shanghai';

    /**
     * Base domain URL of the application
     * Used for generating absolute URLs
     */
    const domain = 'http://localhost/test-login-page';

    /**
     * File upload configuration
     * Defines settings for handling user file uploads
     */
    const upload = [
        'path' => 'assets/uploads',        // Server-side directory for storing uploads
        'max_width' => 1024,               // Maximum width (in pixels) for uploaded images
        'max_size' => 8 * 1024 * 1024,     // Maximum file size (8MB in bytes)
    ];
}