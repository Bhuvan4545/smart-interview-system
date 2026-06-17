const {
    isValidEmail,
    validateRegistrationForm,
    validateLoginForm,
    validateAddQuestionForm,
} = require('../../assets/js/validation');

// -------------------------------------------------------------------
// isValidEmail
// -------------------------------------------------------------------

describe('isValidEmail', () => {
    test('accepts standard email', () => {
        expect(isValidEmail('user@example.com')).toBe(true);
    });

    test('accepts email with subdomain', () => {
        expect(isValidEmail('user@mail.example.com')).toBe(true);
    });

    test('accepts email with plus alias', () => {
        expect(isValidEmail('user+tag@example.com')).toBe(true);
    });

    test('rejects empty string', () => {
        expect(isValidEmail('')).toBe(false);
    });

    test('rejects plain text', () => {
        expect(isValidEmail('not-an-email')).toBe(false);
    });

    test('rejects email without domain', () => {
        expect(isValidEmail('user@')).toBe(false);
    });

    test('rejects email without user', () => {
        expect(isValidEmail('@example.com')).toBe(false);
    });

    test('rejects email with spaces', () => {
        expect(isValidEmail('user @example.com')).toBe(false);
    });

    test('is case-insensitive', () => {
        expect(isValidEmail('User@Example.COM')).toBe(true);
    });

    test('rejects double at sign', () => {
        expect(isValidEmail('user@@example.com')).toBe(false);
    });
});

// -------------------------------------------------------------------
// validateRegistrationForm
// -------------------------------------------------------------------

describe('validateRegistrationForm', () => {
    test('returns no errors for valid input', () => {
        const errors = validateRegistrationForm('John', 'john@example.com', 'secret123', 'secret123');
        expect(errors).toEqual([]);
    });

    test('requires name', () => {
        const errors = validateRegistrationForm('', 'john@example.com', 'secret123', 'secret123');
        expect(errors).toContain('Name is required.');
    });

    test('requires name (whitespace only)', () => {
        const errors = validateRegistrationForm('   ', 'john@example.com', 'secret123', 'secret123');
        expect(errors).toContain('Name is required.');
    });

    test('requires email', () => {
        const errors = validateRegistrationForm('John', '', 'secret123', 'secret123');
        expect(errors).toContain('Email is required.');
    });

    test('rejects invalid email', () => {
        const errors = validateRegistrationForm('John', 'bad', 'secret123', 'secret123');
        expect(errors).toContain('Please enter a valid email address.');
    });

    test('requires password', () => {
        const errors = validateRegistrationForm('John', 'j@e.co', '', '');
        expect(errors).toContain('Password is required.');
    });

    test('rejects short password', () => {
        const errors = validateRegistrationForm('John', 'j@e.co', 'abc', 'abc');
        expect(errors).toContain('Password must be at least 6 characters.');
    });

    test('requires confirm password', () => {
        const errors = validateRegistrationForm('John', 'j@e.co', 'secret123', '');
        expect(errors).toContain('Please confirm your password.');
    });

    test('detects password mismatch', () => {
        const errors = validateRegistrationForm('John', 'j@e.co', 'secret123', 'different');
        expect(errors).toContain('Passwords do not match.');
    });

    test('accepts exactly 6-char password', () => {
        const errors = validateRegistrationForm('John', 'j@e.co', '123456', '123456');
        expect(errors).toEqual([]);
    });

    test('returns multiple errors at once', () => {
        const errors = validateRegistrationForm('', '', '', '');
        expect(errors.length).toBeGreaterThanOrEqual(3);
    });

    test('handles null/undefined name', () => {
        const errors = validateRegistrationForm(null, 'j@e.co', 'secret123', 'secret123');
        expect(errors).toContain('Name is required.');
    });

    test('handles undefined email', () => {
        const errors = validateRegistrationForm('John', undefined, 'secret123', 'secret123');
        expect(errors).toContain('Email is required.');
    });
});

// -------------------------------------------------------------------
// validateLoginForm
// -------------------------------------------------------------------

describe('validateLoginForm', () => {
    test('returns no errors for valid input', () => {
        const errors = validateLoginForm('user@example.com', 'pass');
        expect(errors).toEqual([]);
    });

    test('requires email', () => {
        const errors = validateLoginForm('', 'pass');
        expect(errors).toContain('Email is required.');
    });

    test('rejects invalid email', () => {
        const errors = validateLoginForm('bad', 'pass');
        expect(errors).toContain('Please enter a valid email address.');
    });

    test('requires password', () => {
        const errors = validateLoginForm('user@example.com', '');
        expect(errors).toContain('Password is required.');
    });

    test('handles null password', () => {
        const errors = validateLoginForm('user@example.com', null);
        expect(errors).toContain('Password is required.');
    });

    test('both empty returns two errors', () => {
        const errors = validateLoginForm('', '');
        expect(errors).toHaveLength(2);
    });
});

// -------------------------------------------------------------------
// validateAddQuestionForm
// -------------------------------------------------------------------

describe('validateAddQuestionForm', () => {
    test('returns no errors for valid input', () => {
        const errors = validateAddQuestionForm('What is 1+1?', 'A', 'B', 'C', 'D', '1');
        expect(errors).toEqual([]);
    });

    test('requires question text', () => {
        const errors = validateAddQuestionForm('', 'A', 'B', 'C', 'D', '1');
        expect(errors).toContain('Question text is required.');
    });

    test('requires option1', () => {
        const errors = validateAddQuestionForm('Q?', '', 'B', 'C', 'D', '1');
        expect(errors).toContain('Option 1 is required.');
    });

    test('requires option2', () => {
        const errors = validateAddQuestionForm('Q?', 'A', '', 'C', 'D', '1');
        expect(errors).toContain('Option 2 is required.');
    });

    test('requires option3', () => {
        const errors = validateAddQuestionForm('Q?', 'A', 'B', '', 'D', '1');
        expect(errors).toContain('Option 3 is required.');
    });

    test('requires option4', () => {
        const errors = validateAddQuestionForm('Q?', 'A', 'B', 'C', '', '1');
        expect(errors).toContain('Option 4 is required.');
    });

    test('requires correct option', () => {
        const errors = validateAddQuestionForm('Q?', 'A', 'B', 'C', 'D', '');
        expect(errors).toContain('Select the correct option.');
    });

    test('requires correct option (null)', () => {
        const errors = validateAddQuestionForm('Q?', 'A', 'B', 'C', 'D', null);
        expect(errors).toContain('Select the correct option.');
    });

    test('all empty returns many errors', () => {
        const errors = validateAddQuestionForm('', '', '', '', '', '');
        expect(errors.length).toBeGreaterThanOrEqual(6);
    });

    test('whitespace-only question is invalid', () => {
        const errors = validateAddQuestionForm('   ', 'A', 'B', 'C', 'D', '1');
        expect(errors).toContain('Question text is required.');
    });
});
