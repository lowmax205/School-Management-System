document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        validateLoginForm();
    });

    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        validateRegisterForm();
    });
});

function clearErrors() {
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(element => element.textContent = '');
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateLoginForm() {
    clearErrors();
    let isValid = true;
    const email = document.querySelector('.sign-in-form input[type="email"]').value;
    const password = document.querySelector('.sign-in-form input[type="password"]').value;

    if (!validateEmail(email)) {
        document.getElementById('email-error-login').textContent = 'Please enter a valid email address';
        isValid = false;
    }

    if (password.length < 6) {
        document.getElementById('password-error-login').textContent = 'Password must be at least 6 characters long';
        isValid = false;
    }

    if (isValid) {
        // Here you would typically make an AJAX call to your server
        console.log('Login form is valid, ready to submit');
    }
}

function validateRegisterForm() {
    clearErrors();
    let isValid = true;
    const email = document.querySelector('.sign-up-form input[type="email"]').value;
    const password = document.querySelector('.sign-up-form input[type="password"]:nth-of-type(1)').value;
    const confirmPassword = document.querySelector('.sign-up-form input[type="password"]:nth-of-type(2)').value;

    if (!validateEmail(email)) {
        document.getElementById('email-error-register').textContent = 'Please enter a valid email address';
        isValid = false;
    }

    if (password.length < 6) {
        document.getElementById('password-error-register').textContent = 'Password must be at least 6 characters long';
        isValid = false;
    }

    if (password !== confirmPassword) {
        document.getElementById('confirm-password-error').textContent = 'Passwords do not match';
        isValid = false;
    }

    if (isValid) {
        // Send data to server
        fetch('/School-Management-System/server/register_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: email,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear form and show success message
                registerForm.reset();
                document.getElementById('register-error').textContent = data.message;
                // Optionally switch to login form after successful registration
                document.getElementById('show-login').click();
            } else {
                document.getElementById('register-error').textContent = data.message;
            }
        })
        .catch(error => {
            document.getElementById('register-error').textContent = 'An error occurred. Please try again.';
            console.error('Error:', error);
        });
    }
}
