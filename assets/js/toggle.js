document.addEventListener('DOMContentLoaded', function() {
    const showRegister = document.getElementById('show-register');
    const showLogin = document.getElementById('show-login');
    const loginForm = document.querySelector('.sign-in-form');
    const registerForm = document.querySelector('.sign-up-form');

  // Initialize state
  loginForm.classList.add("active");

  // Handle form switching
  function switchForms(hideForm, showForm) {
    hideForm.classList.remove("active");
    setTimeout(() => {
      showForm.classList.add("active");
    }, 300);
  }

  showRegister.addEventListener("click", (e) => {
    e.preventDefault();
    switchForms(loginForm, registerForm);
  });

  showLogin.addEventListener("click", (e) => {
    e.preventDefault();
    switchForms(registerForm, loginForm);
  });

  // Reset form state when modal is closed
  authModal.addEventListener("hidden.bs.modal", function () {
    registerForm.classList.remove("active");
    loginForm.classList.add("active");
  });
});
