const form = document.getElementById('form');

const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirm_password');

function validate() {
    const errors = validateLoginForm(); // Includes email/password validation

    if (!nameInput) {
        return errors;
    }

    if (!nameInput.value.trim()) {
        errors.push({field: 'name', message: 'Please input your name.'});
    }

    if (passwordInput.value !== confirmPasswordInput.value) {
        errors.push({
            field: 'confirm_password', message: 'Password confirmation does not match.'
        });
    }

    return errors;
}

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

function displayErrors(errors) {
    console.log(errors);
    errors.forEach(error => {
        const errorField = document.querySelector(`.for-${error.field}`);
        if (errorField) {
            errorField.textContent = error.message;
            errorField.style.display = 'block';
        }
    });
}

form.addEventListener('submit', function (e) {
    e.preventDefault();

    const errors = validate();

    if (errors.length > 0) {
        displayErrors(errors);
        return;
    }

    const body = new FormData();

    body.append('email', emailInput.value.trim());
    body.append('password', passwordInput.value.trim());
    if (nameInput) {
        body.append('name', nameInput.value.trim());
    }

    fetch(form.action, {
        method: 'POST',
        body: body,
    }).then(res => res.json())
        .then(res => {
            if (res.code === 10000) {
                window.location.reload();
            } else {
                if (typeof res.message === 'string') {
                    displayErrors([{field: 'email', message: res.message}]);
                } else {
                    displayErrors(Object.keys(res.message).map(field => ({
                        field,
                        message: res.message[field],
                    })));
                }
            }
        })
});

if (nameInput) {
    nameInput.addEventListener()
}
