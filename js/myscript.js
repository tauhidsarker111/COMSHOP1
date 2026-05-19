function showError(input, message) {
    var error = input.parentElement.querySelector('.js-' + input.name + '-error');
    if (!error) {
        error = input.nextElementSibling;
    }
    if (error && error.classList.contains('field-error')) {
        error.textContent = message;
    }
}

function validEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function bindRegistrationValidation() {
    var form = document.getElementById('registrationForm');
    if (!form) return;

    form.addEventListener('submit', function (event) {
        var isValid = true;
        var name = form.querySelector('[name="uname"]');
        var email = form.querySelector('[name="myemail"]');
        var password = form.querySelector('[name="pass"]');
        var confirmPassword = form.querySelector('[name="confirm_pass"]');

        if (name.value.trim().length < 3) {
            showError(name, 'Name must be at least 3 characters.');
            isValid = false;
        }
        if (!validEmail(email.value.trim())) {
            showError(email, 'Enter a valid email address.');
            isValid = false;
        }
        if (password.value.length < 8) {
            showError(password, 'Password must be at least 8 characters.');
            isValid = false;
        }
        if (password.value !== confirmPassword.value) {
            showError(confirmPassword, 'Passwords do not match.');
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
        }
    });
}

function bindLoginValidation() {
    var form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', function (event) {
        var isValid = true;
        var email = form.querySelector('[name="myemail"]');
        var password = form.querySelector('[name="pass"]');

        if (!validEmail(email.value.trim())) {
            showError(email, 'Enter a valid email address.');
            isValid = false;
        }
        if (password.value.length === 0) {
            showError(password, 'Password is required.');
            isValid = false;
        }
        if (!isValid) {
            event.preventDefault();
        }
    });
}

function bindProfileValidation() {
    var form = document.getElementById('profileForm');
    if (!form) return;

    form.addEventListener('submit', function (event) {
        var isValid = true;
        var name = form.querySelector('[name="uname"]');
        var email = form.querySelector('[name="myemail"]');
        var currentPassword = form.querySelector('[name="current_pass"]');
        var newPassword = form.querySelector('[name="new_pass"]');
        var confirmPassword = form.querySelector('[name="confirm_pass"]');

        if (name.value.trim().length < 3) {
            showError(name, 'Name must be at least 3 characters.');
            isValid = false;
        }
        if (!validEmail(email.value.trim())) {
            showError(email, 'Enter a valid email address.');
            isValid = false;
        }
        if (currentPassword.value || newPassword.value || confirmPassword.value) {
            if (currentPassword.value.length === 0) {
                showError(currentPassword, 'Current password is required.');
                isValid = false;
            }
            if (newPassword.value.length < 8) {
                showError(newPassword, 'New password must be at least 8 characters.');
                isValid = false;
            }
            if (newPassword.value !== confirmPassword.value) {
                showError(confirmPassword, 'Passwords do not match.');
                isValid = false;
            }
        }

        if (!isValid) {
            event.preventDefault();
        }
    });
}

function bindUserSearch() {
    var input = document.getElementById('userSearch');
    var result = document.getElementById('searchResult');
    if (!input || !result) return;

    var timer = null;
    input.addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            var value = input.value.trim();
            if (value.length < 2) {
                result.innerHTML = '<p class="muted">Type at least 2 characters.</p>';
                return;
            }

            fetch('../control/profile_process.php?search=' + encodeURIComponent(value))
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.users || data.users.length === 0) {
                        result.innerHTML = '<p class="muted">No users found.</p>';
                        return;
                    }

                    result.innerHTML = data.users.map(function (user) {
                        return '<div class="result-item">' +
                            '<strong>' + escapeHtml(user.name) + '</strong>' +
                            '<span>' + escapeHtml(user.email) + ' - ' + escapeHtml(user.role) + '</span>' +
                        '</div>';
                    }).join('');
                })
                .catch(function () {
                    result.innerHTML = '<p class="field-error">Could not load users right now.</p>';
                });
        }, 250);
    });
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.addEventListener('DOMContentLoaded', function () {
    bindRegistrationValidation();
    bindLoginValidation();
    bindProfileValidation();
    bindUserSearch();
});
