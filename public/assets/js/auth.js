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
        errors.push({field: 'email', message: 'Please input your email address.'});
    }

    if (!passwordInput.value.trim()) {
        errors.push({field: 'password', message: 'Please input your password.'});
    } else if (passwordInput.value.length < 6) {
        errors.push({field: 'password', message: 'Password must be at least 6 characters.'});
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
        errors.push({field: 'name', message: 'Please input your name.'});
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