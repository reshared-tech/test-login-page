const form = document.getElementById('form');

const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirm_password');

function validate() {
    const errors = validateProfileForm(); // Includes email/password validation

    if (!nameInput) {
        return errors;
    }

    if (!nameInput.value.trim()) {
        errors.push({field: 'name', message: '名前を入力してください'});
    }

    if (passwordInput.value !== confirmPasswordInput.value) {
        errors.push({
            field: 'confirm_password', message: 'パスワードの確認が一致しません'
        });
    }

    return errors;
}

function validateProfileForm() {
    const errors = [];

    if (!emailInput.value.trim()) {
        errors.push({field: 'email', message: 'メールアドレスを入力してください'});
    }

    if (!passwordInput.value.trim()) {
        errors.push({field: 'password', message: '暗証番号を入力してください'});
    } else if (passwordInput.value.length < 6) {
        errors.push({field: 'password', message: 'パスワードは6文字以上でなければなりません'});
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
                window.location.href = base_path ? base_path : '/';
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

[nameInput, emailInput, passwordInput, confirmPasswordInput].forEach(input => {
    if (input) {
        input.addEventListener('input', clearError.bind(null, input));
    }
});