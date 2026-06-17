/**
 * Pure validation helpers extracted from script.js for testability.
 */

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

function validateRegistrationForm(name, email, password, confirmPassword) {
    const errors = [];

    if (!name || !name.trim()) {
        errors.push('Name is required.');
    }

    if (!email || !email.trim()) {
        errors.push('Email is required.');
    } else if (!isValidEmail(email.trim())) {
        errors.push('Please enter a valid email address.');
    }

    if (!password) {
        errors.push('Password is required.');
    } else if (password.length < 6) {
        errors.push('Password must be at least 6 characters.');
    }

    if (!confirmPassword) {
        errors.push('Please confirm your password.');
    } else if (password !== confirmPassword) {
        errors.push('Passwords do not match.');
    }

    return errors;
}

function validateLoginForm(email, password) {
    const errors = [];

    if (!email || !email.trim()) {
        errors.push('Email is required.');
    } else if (!isValidEmail(email.trim())) {
        errors.push('Please enter a valid email address.');
    }

    if (!password) {
        errors.push('Password is required.');
    }

    return errors;
}

function validateAddQuestionForm(question, option1, option2, option3, option4, correctOption) {
    const errors = [];

    if (!question || !question.trim()) {
        errors.push('Question text is required.');
    }
    if (!option1 || !option1.trim()) {
        errors.push('Option 1 is required.');
    }
    if (!option2 || !option2.trim()) {
        errors.push('Option 2 is required.');
    }
    if (!option3 || !option3.trim()) {
        errors.push('Option 3 is required.');
    }
    if (!option4 || !option4.trim()) {
        errors.push('Option 4 is required.');
    }
    if (!correctOption) {
        errors.push('Select the correct option.');
    }

    return errors;
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        isValidEmail,
        validateRegistrationForm,
        validateLoginForm,
        validateAddQuestionForm,
    };
}
