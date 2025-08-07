const form = document.getElementById('form');

const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');

function validateProfileForm() {
    const errors = [];

    if (!emailInput.value.trim()) {
        errors.push({field: 'email', message: 'メールアドレスを入力してください'});
    }

    if (!nameInput.value.trim()) {
        errors.push({field: 'name', message: 'メールアドレスを入力してください'});
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

    const errors = validateProfileForm();

    if (errors.length > 0) {
        displayErrors(errors);
        return;
    }

    const body = new FormData();

    body.append('email', emailInput.value.trim());
    body.append('name', nameInput.value.trim());

    fetch(form.action, {
        method: 'POST',
        body: body,
    }).then(res => res.json())
        .then(res => {
            if (res.code === 10000) {
                alert(res.message);
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

[nameInput, emailInput].forEach(input => {
    if (input) {
        input.addEventListener('input', clearError.bind(null, input));
    }
});