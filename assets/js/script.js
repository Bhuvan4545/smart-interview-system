// Basic client-side form validation

function showError(element, message) {
    const errorSpanId = element.id + '-error';
    let span = document.getElementById(errorSpanId);
    if (!span) {
        span = document.createElement('div');
        span.id = errorSpanId;
        span.className = 'alert alert-error';
        element.parentNode.appendChild(span);
    }
    span.textContent = message;
}

function clearErrors(form) {
    const alerts = form.querySelectorAll('.alert-error');
    alerts.forEach(a => a.remove());
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

document.addEventListener('DOMContentLoaded', function () {
    // Student registration form
    const studentRegisterForm = document.getElementById('studentRegisterForm');
    if (studentRegisterForm) {
        studentRegisterForm.addEventListener('submit', function (e) {
            clearErrors(studentRegisterForm);

            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');

            let valid = true;

            if (!name.value.trim()) {
                showError(name, 'Name is required.');
                valid = false;
            }

            if (!email.value.trim()) {
                showError(email, 'Email is required.');
                valid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showError(email, 'Please enter a valid email address.');
                valid = false;
            }

            if (!password.value) {
                showError(password, 'Password is required.');
                valid = false;
            } else if (password.value.length < 6) {
                showError(password, 'Password must be at least 6 characters.');
                valid = false;
            }

            if (!confirmPassword.value) {
                showError(confirmPassword, 'Please confirm your password.');
                valid = false;
            } else if (password.value !== confirmPassword.value) {
                showError(confirmPassword, 'Passwords do not match.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    // Student login form
    const studentLoginForm = document.getElementById('studentLoginForm');
    if (studentLoginForm) {
        studentLoginForm.addEventListener('submit', function (e) {
            clearErrors(studentLoginForm);

            const email = document.getElementById('email');
            const password = document.getElementById('password');
            let valid = true;

            if (!email.value.trim()) {
                showError(email, 'Email is required.');
                valid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showError(email, 'Please enter a valid email address.');
                valid = false;
            }

            if (!password.value) {
                showError(password, 'Password is required.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    // Admin login form
    const adminLoginForm = document.getElementById('adminLoginForm');
    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', function (e) {
            clearErrors(adminLoginForm);

            const email = document.getElementById('email');
            const password = document.getElementById('password');
            let valid = true;

            if (!email.value.trim()) {
                showError(email, 'Email is required.');
                valid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showError(email, 'Please enter a valid email address.');
                valid = false;
            }

            if (!password.value) {
                showError(password, 'Password is required.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    // Add question form
    const addQuestionForm = document.getElementById('addQuestionForm');
    if (addQuestionForm) {
        addQuestionForm.addEventListener('submit', function (e) {
            clearErrors(addQuestionForm);

            const question = document.getElementById('question');
            const option1 = document.getElementById('option1');
            const option2 = document.getElementById('option2');
            const option3 = document.getElementById('option3');
            const option4 = document.getElementById('option4');
            const correctOption = document.getElementById('correct_option');

            let valid = true;

            if (!question.value.trim()) {
                showError(question, 'Question text is required.');
                valid = false;
            }
            if (!option1.value.trim()) {
                showError(option1, 'Option 1 is required.');
                valid = false;
            }
            if (!option2.value.trim()) {
                showError(option2, 'Option 2 is required.');
                valid = false;
            }
            if (!option3.value.trim()) {
                showError(option3, 'Option 3 is required.');
                valid = false;
            }
            if (!option4.value.trim()) {
                showError(option4, 'Option 4 is required.');
                valid = false;
            }
            if (!correctOption.value) {
                showError(correctOption, 'Select the correct option.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }
});