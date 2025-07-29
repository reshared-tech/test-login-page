<?php
// Start session for user authentication
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
            Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            line-height: 1.5;
        }

        /* Layout Styles */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .card {
            background-color: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
            0 4px 6px -4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 28rem;
            padding: 2rem 1.5rem;
        }

        /* Header Styles */
        .header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #64748b;
            font-size: 1rem;
        }

        /* Form Styles */
        .form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form .item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form .label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #334155;
        }

        .form .input {
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.375rem;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form .input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        /* Error Message Styles */
        .tip {
            font-size: 0.75rem;
            color: #f77070;
            display: none;
        }

        /* Button Styles */
        .form button {
            background-color: #3b82f6;
            color: #ffffff;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.15s ease-in-out;
        }

        .form button:hover {
            background-color: #2563eb;
        }

        /* Link Styles */
        .link {
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.15s ease-in-out;
        }

        .link:hover {
            color: #2563eb;
            text-decoration: underline;
        }

        /* Footer Styles */
        .footer {
            margin-top: 1rem;
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Registration-specific fields */
        .form .only-register {
            display: none;
        }

        /* Responsive Styles */
        @media (max-width: 640px) {
            .card {
                padding: 1.5rem 1rem;
            }

            .title {
                font-size: 1.25rem;
            }

            .subtitle {
                font-size: 0.875rem;
            }

            .form .input {
                padding: 0.5rem 0.75rem;
            }

            .form button {
                padding: 0.5rem 0.75rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <?php if (isset($_SESSION['user'])): ?>
            <!-- Logged-in state view -->
            <div class="header">
                <h1 class="title">Welcome!</h1>
                <p class="subtitle">Hi, <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
                <p><a href="javascript:;" id="logout" class="link">Log out</a></p>
                <p class="tip for-logout"></p>
            </div>
        <?php else: ?>
            <!-- Guest state view (login/register form) -->
            <div class="header">
                <h1 class="title" id="form-title">Welcome Back</h1>
                <p class="subtitle" id="form-subtitle">Please log in your account</p>
            </div>

            <form class="form" id="auth-form">
                <!-- Registration-only fields (hidden by default) -->
                <div class="only-register item">
                    <label for="name" class="label">Name</label>
                    <input id="name" type="text" name="name" class="input"
                           placeholder="Please input your name.">
                    <p class="tip for-name"></p>
                </div>

                <!-- Common fields -->
                <div class="item">
                    <label for="email" class="label">Email address</label>
                    <input id="email" type="email" name="email" class="input"
                           placeholder="Please input your email address." required>
                    <p class="tip for-email"></p>
                </div>

                <div class="item">
                    <label for="password" class="label">Password</label>
                    <input id="password" type="password" name="password" class="input"
                           placeholder="Please input your password." required minlength="6">
                    <p class="tip for-password"></p>
                </div>

                <!-- Registration-only fields (hidden by default) -->
                <div class="only-register item">
                    <label for="confirm_password" class="label">Confirm password</label>
                    <input id="confirm_password" type="password" name="confirm_password" class="input"
                           placeholder="Please repeat your password.">
                    <p class="tip for-confirm-password"></p>
                </div>

                <button id="submit-btn" type="submit">Log in</button>

                <div class="footer">
                    <p><span id="footer-info">Don't have an account?</span>
                        <a href="javascript:;" id="toggle-form" class="link">Sign up</a></p>
                </div>
            </form>
        <?php endif ?>
    </div>
</div>

<script>
    /**
     * Authentication Form Handler
     * Manages login/registration form toggling and submission
     */

// Configuration for text content
    const FORM_TEXTS = {
        login: {
            title: 'Welcome Back',
            subtitle: 'Please log in your account',
            footerInfo: 'Don\'t have an account?',
            link: 'Sign up',
            submit: 'Log in',
        },
        register: {
            title: 'Welcome',
            subtitle: 'Register a new account',
            footerInfo: 'Already have an account?',
            link: 'Log in',
            submit: 'Sign up',
        }
    };

    // DOM Elements
    const authForm = document.getElementById('auth-form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const registerFields = document.querySelectorAll('.only-register');
    const toggleFormBtn = document.getElementById('toggle-form');
    const submitBtn = document.getElementById('submit-btn');
    const formTitle = document.getElementById('form-title');
    const formSubtitle = document.getElementById('form-subtitle');
    const footerInfo = document.getElementById('footer-info');
    const logoutBtn = document.getElementById('logout');

    // State tracking
    let isLoginForm = true;

    // Initialize form
    if (authForm) {
        setupEventListeners();
    } else if (logoutBtn) {
        logoutBtn.addEventListener('click', handleLogout);
    }

    /**
     * Sets up all event listeners for the form
     */
    function setupEventListeners() {
        // Input validation listeners
        [nameInput, emailInput, passwordInput, confirmPasswordInput].forEach(input => {
            if (input) {
                input.addEventListener('input', clearError.bind(null, input));
            }
        });

        // Form toggle listener
        if (toggleFormBtn) {
            toggleFormBtn.addEventListener('click', toggleForm);
        }

        // Form submission listener
        if (authForm) {
            authForm.addEventListener('submit', handleFormSubmit);
        }
    }

    /**
     * Clears error message for a given input field
     * @param {HTMLInputElement} input - The input field to clear errors for
     */
    function clearError(input) {
        const errorField = document.querySelector(`.for-${input.name}`);
        if (errorField) {
            errorField.style.display = 'none';
        }
    }

    /**
     * Toggles between login and registration forms
     */
    function toggleForm() {
        isLoginForm = !isLoginForm;
        const texts = isLoginForm ? FORM_TEXTS.login : FORM_TEXTS.register;

        // Update form text content
        formTitle.textContent = texts.title;
        formSubtitle.textContent = texts.subtitle;
        footerInfo.textContent = texts.footerInfo;
        toggleFormBtn.textContent = texts.link;
        submitBtn.textContent = texts.submit;

        // Toggle registration fields
        registerFields.forEach(field => {
            field.style.display = isLoginForm ? 'none' : 'flex';
        });

        // Clear form values
        if (nameInput) nameInput.value = '';
        if (confirmPasswordInput) confirmPasswordInput.value = '';
        if (emailInput) emailInput.value = '';
        if (passwordInput) passwordInput.value = '';
    }

    /**
     * Handles form submission (both login and registration)
     * @param {Event} e - The submit event
     */
    function handleFormSubmit(e) {
        e.preventDefault();

        if (isLoginForm) {
            handleLogin();
        } else {
            handleRegistration();
        }
    }

    /**
     * Validates and processes login form submission
     */
    function handleLogin() {
        const errors = validateLoginForm();

        if (errors.length > 0) {
            displayErrors(errors);
            return;
        }

        submitForm({
            email: emailInput.value.trim(),
            password: passwordInput.value.trim()
        });
    }

    /**
     * Validates and processes registration form submission
     */
    function handleRegistration() {
        const errors = validateRegistrationForm();

        if (errors.length > 0) {
            displayErrors(errors);
            return;
        }

        submitForm({
            name: nameInput.value.trim(),
            email: emailInput.value.trim(),
            password: passwordInput.value.trim()
        });
    }

    /**
     * Validates login form fields
     * @returns {Array} Array of error messages
     */
    function validateLoginForm() {
        const errors = [];

        if (!emailInput.value.trim()) {
            errors.push({ field: 'email', message: 'Please input your email address.' });
        }

        if (!passwordInput.value.trim()) {
            errors.push({ field: 'password', message: 'Please input your password.' });
        } else if (passwordInput.value.length < 6) {
            errors.push({ field: 'password', message: 'Password must be at least 6 characters.' });
        }

        return errors;
    }

    /**
     * Validates registration form fields
     * @returns {Array} Array of error messages
     */
    function validateRegistrationForm() {
        const errors = validateLoginForm(); // Includes email/password validation

        if (!nameInput.value.trim()) {
            errors.push({ field: 'name', message: 'Please input your name.' });
        }

        if (passwordInput.value !== confirmPasswordInput.value) {
            errors.push({
                field: 'confirm_password',
                message: 'Password confirmation does not match.'
            });
        }

        return errors;
    }

    /**
     * Displays validation errors on the form
     * @param {Array} errors - Array of error objects
     */
    function displayErrors(errors) {
        errors.forEach(error => {
            const errorField = document.querySelector(`.for-${error.field}`);
            if (errorField) {
                errorField.textContent = error.message;
                errorField.style.display = 'block';
            }
        });
    }

    /**
     * Submits form data to the server
     * @param {Object} data - Form data to submit
     */
    function submitForm(data) {
        const formData = new FormData();
        Object.entries(data).forEach(([key, value]) => {
            formData.append(key, value);
        });

        fetch('/auth.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(handleResponse)
            .catch(handleError);
    }

    /**
     * Handles server response
     * @param {Object} response - Server response
     */
    function handleResponse(response) {
        if (response.code === 10000) {
            window.location.reload();
        } else {
            const errorField = document.querySelector('.for-email');
            if (errorField) {
                errorField.textContent = response.msg || 'Something went wrong. Please try again.';
                errorField.style.display = 'block';
            }
        }
    }

    /**
     * Handles fetch errors
     * @param {Error} error - The error object
     */
    function handleError(error) {
        console.error('Error:', error);
        const errorField = document.querySelector('.for-email');
        if (errorField) {
            errorField.textContent = 'Network error. Please try again.';
            errorField.style.display = 'block';
        }
    }

    /**
     * Handles logout request
     */
    function handleLogout() {
        fetch('auth.php')
            .then(response => response.json())
            .then(response => {
                if (response.code === 10000) {
                    window.location.reload();
                } else {
                    const errorField = document.querySelector('.for-logout');
                    if (errorField) {
                        errorField.textContent = response.msg || 'Logout failed. Please try again.';
                        errorField.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                const errorField = document.querySelector('.for-logout');
                if (errorField) {
                    errorField.textContent = 'Network error. Please try again.';
                    errorField.style.display = 'block';
                }
            });
    }
</script>
</body>
</html>