document.addEventListener('DOMContentLoaded', function() {
    const showRegister = document.getElementById('show-register');
    const showLogin = document.getElementById('show-login');
    const loginForm = document.querySelector('.sign-in-form');
    const registerForm = document.querySelector('.sign-up-form');

    // Initialize state
    loginForm.classList.add('active');

    showRegister.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.classList.remove('active');
        // Small delay to allow the first animation to start
        setTimeout(() => {
            registerForm.classList.add('active');
        }, 50);
    });

    showLogin.addEventListener('click', (e) => {
        e.preventDefault();
        registerForm.classList.remove('active');
        // Small delay to allow the first animation to start
        setTimeout(() => {
            loginForm.classList.add('active');
        }, 50);
    });
});
