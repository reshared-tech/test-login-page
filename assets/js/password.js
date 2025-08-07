const form = document.getElementById('form');

const currentPasswordInput = document.getElementById('current_password');
const newPasswordInput = document.getElementById('new_password');
const confirmPasswordInput = document.getElementById('confirm_password');

function validate() {
    const errors = [];

    if (!currentPasswordInput.value.trim()) {
        errors.push({field: 'current_password', message: '現在のパスワードをお願いします。'});
    }

    if (!newPasswordInput.value.trim()) {
        errors.push({field: 'new_password', message: '新しいパスワードをお願いします。'});
    }

    if (newPasswordInput.value.trim() !== confirmPasswordInput.value.trim()) {
        errors.push({field: 'confirm_password', message: 'パスワードが新しいパスワードと一致しないことを確認しました'});
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
    body.append('current_password', currentPasswordInput.value.trim());
    body.append('new_password', newPasswordInput.value.trim());

    fetch(form.action, {
        method: 'POST', body: body,
    }).then(res => res.json())
        .then(res => {
            if (res.code === 10000) {
                alert(res.message);
                window.location.reload();
            } else {
                if (typeof res.message === 'string') {
                    displayErrors([{field: 'current_password', message: res.message}]);
                } else {
                    displayErrors(Object.keys(res.message).map(field => ({
                        field, message: res.message[field],
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

[currentPasswordInput, newPasswordInput, confirmPasswordInput].forEach(input => {
    if (input) {
        input.addEventListener('input', clearError.bind(null, input));
    }
});