<?php

namespace Admin\Controllers;

use Admin\Models\AdministratorModel;
use Exception;
use Tools\Auth;
use Tools\Language;
use Tools\Validator;

/**
 * Base controller for all admin-related controllers
 * Provides core functionalities like authentication, model initialization, and log management
 */
class Controller
{
    /**
     * Validator instance for input validation
     * @var Validator
     */
    protected $validator;

    /**
     * Administrator model instance for admin-related database operations
     * @var AdministratorModel
     */
    protected $model;

    /**
     * Controller constructor: Initializes session, auth, language, validator and model
     */
    public function __construct()
    {
        // Start user session to maintain authentication state
        session_start();

        // Set authentication namespace to "admin" (for distinguishing admin/user sessions)
        Auth::name('admin');

        // Set application language to Japanese
        Language::setLang(Language::JP);

        // Check if current user is authorized (redirect to login if not)
        $this->checkAuth();

        // Initialize validator for handling input validation in child controllers
        $this->validator = new Validator();

        // Initialize administrator model for database operations
        $this->initModel();
    }

    /**
     * Initialize AdministratorModel if it's not already set
     * Ensures model is available for admin-related operations
     */
    private function initModel()
    {
        // Only create new model instance if $model is not initialized
        if (!$this->model) {
            $this->model = new AdministratorModel();
        }
    }

    /**
     * Check user authentication status
     * Enforces basic authentication for unauthenticated users
     */
    private function checkAuth()
    {
        // Proceed only if user is not authorized
        if (!Auth::isAuthorized()) {
            // Trigger basic authentication prompt if PHP auth credentials are missing
            if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
                $this->forbidden();
            }

            // Sanitize input credentials (remove extra whitespace)
            $name = trim($_SERVER['PHP_AUTH_USER']);
            $password = trim($_SERVER['PHP_AUTH_PW']);

            // Ensure AdministratorModel is initialized before database operations
            $this->initModel();

            // Special case: Validate default "admin" credentials (hardcoded)
            if ($name === 'admin' && $password === 'admin') {
                // Store admin auth data in session
                Auth::addAuth([
                    'id' => 1,
                    'name' => $name,
                ]);

                // Log the successful login action
                $this->saveLog('login');
                return;
            }

            // Fetch administrator data from database by username
            $data = $this->model->getByName($name);

            // Reject access if no admin record matches the username
            if (empty($data)) {
                $this->forbidden();
            }

            // Verify if the input password matches the stored hashed password
            if (!password_verify($password, $data['password'])) {
                $this->forbidden();
            }

            // Store valid admin auth data in session
            Auth::addAuth([
                'id' => $data['id'],
                'name' => $data['name'],
            ]);

            // Log the successful login action
            $this->saveLog('login');
        }
    }

    /**
     * Save administrator action log to database
     * Catches exceptions to avoid breaking main functionality
     *
     * @param string $action Description of the admin action (e.g., "login", "update chat")
     * @param array $detail Optional additional details about the action (e.g., target IDs, input data)
     */
    protected function saveLog($action, $detail = [])
    {
        try {
            // Get authorized user's ID and save log via AdministratorModel
            $this->model->saveLog(authorizedUser('id'), $action, $detail);
        } catch (Exception $e) {
            // Silent fail: Do not throw error if log saving fails (avoids interrupting core logic)
        }
    }

    /**
     * Trigger 401 Unauthorized response with basic authentication prompt
     * Stops script execution after sending headers
     */
    protected function forbidden()
    {
        // Prompt browser to show basic authentication dialog
        header('WWW-Authenticate: Basic realm="Please log in"');
        // Send 401 HTTP status code (Unauthorized)
        header('HTTP/1.0 401 Unauthorized');
        // Terminate script to prevent further execution
        exit;
    }
}