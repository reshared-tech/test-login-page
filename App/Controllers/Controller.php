<?php

namespace App\Controllers;

use Tools\Auth;
use Tools\Language;
use Tools\Validator;

/**
 * Abstract base controller for all application controllers
 * Provides core initialization (session, language, validation, authentication)
 * to be inherited by child controllers
 */
abstract class Controller
{
    /**
     * Flag to control whether authentication check is required
     * Set to true by default (enforces login for most controllers)
     * @var bool
     */
    protected $checkAuth = true;

    /**
     * Validator instance for input validation in child controllers
     * @var Validator
     */
    protected $validator;

    /**
     * Controller constructor: Initializes core application services
     * Runs automatically when child controllers are instantiated
     */
    public function __construct()
    {
        // Start user session to maintain state (e.g., authentication, user data)
        session_start();

        // Set application language to Japanese (via LanguageTool)
        Language::setLang(Language::JP);

        // Initialize Validator instance for input validation
        $this->validator = new Validator();

        // Enforce authentication check if $checkAuth is true
        // Redirects unauthenticated users to login (handled by Auth::checkAuth())
        if ($this->checkAuth) {
            Auth::checkAuth();
        }
    }
}