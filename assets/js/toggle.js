document.addEventListener("DOMContentLoaded", function () {
  const showRegister = document.getElementById("show-register");
  const showLogin = document.getElementById("show-login");
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");
  const authModal = document.getElementById("authModal");

  loginForm.style.display = "block";
  registerForm.style.display = "none";

  function switchForms(hideForm, showForm) {
    hideForm.style.display = "none";
    showForm.style.display = "block";
  }

  showRegister.addEventListener("click", (e) => {
    e.preventDefault();
    switchForms(loginForm, registerForm);
  });

  showLogin.addEventListener("click", (e) => {
    e.preventDefault();
    switchForms(registerForm, loginForm);
  });

  authModal.addEventListener("hidden.bs.modal", function () {
    registerForm.style.display = "none";
    loginForm.style.display = "block";
  });

  const script = document.createElement("script");
  script.src = "assets/js/error_handler.js";
  document.body.appendChild(script);
});
