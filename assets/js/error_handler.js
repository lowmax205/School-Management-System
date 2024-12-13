document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");

  loginForm.addEventListener("submit", function (e) {
    e.preventDefault();
    validateLoginForm();
  });

  registerForm.addEventListener("submit", function (e) {
    e.preventDefault();
    validateRegisterForm();
  });
});

function clearErrors() {
  const errorElements = document.querySelectorAll(".error-message");
  errorElements.forEach((element) => (element.textContent = ""));
}

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function validateLoginForm() {
  clearErrors();
  let isValid = true;
  const form = document.querySelector(".sign-in-form");
  const email = form.querySelector('input[type="email"]').value;
  const password = form.querySelector('input[type="password"]').value;

  if (!validateEmail(email)) {
    document.getElementById("email-error-login").textContent =
      "Please enter a valid email address";
    isValid = false;
  }

  if (password.length < 6) {
    document.getElementById("password-error-login").textContent =
      "Password must be at least 6 characters long";
    isValid = false;
  }

  if (isValid) {
    const formData = new FormData();
    formData.append("email", email);
    formData.append("password", password);

    fetch("server/login_handler.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          window.location.href = "pages/auth/base_dashboard.php";
        } else {
          document.getElementById("login-error").textContent = data.message;
        }
      })
      .catch((error) => {
        document.getElementById("login-error").textContent =
          "An error occurred. Please try again.";
        console.error("Error:", error);
      });
  }
}

function validateRegisterForm() {
  clearErrors();
  let isValid = true;

  // Get form inputs and error elements
  const form = document.querySelector(".sign-up-form");
  const inputs = form.querySelectorAll("input");
  const email = inputs[0].value;
  const password = inputs[1].value;
  const confirmPassword = inputs[2].value;
  const registerError = document.getElementById("register-error");
  const loginError = document.getElementById("login-error");

  if (!validateEmail(email)) {
    document.getElementById("email-error-register").textContent =
      "Please enter a valid email address";
    isValid = false;
  }

  if (password.length < 6) {
    document.getElementById("password-error-register").textContent =
      "Password must be at least 6 characters long";
    isValid = false;
  }

  if (password !== confirmPassword) {
    document.getElementById("confirm-password-error").textContent =
      "Passwords do not match";
    isValid = false;
  }

  if (isValid) {
    const formData = new FormData();
    formData.append("email", email);
    formData.append("password", password);
    formData.append("confirmPassword", confirmPassword);

    fetch("server/register_handler.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          // Show success message in green
          registerError.textContent = data.message;
          registerError.classList.remove("error-message");
          registerError.classList.add("success-message");

          // After 1 second, switch to login form and show success message there
          setTimeout(() => {
            const loginForm = document.querySelector(".sign-in-form");
            const registerForm = document.querySelector(".sign-up-form");

            registerForm.classList.remove("active");
            loginForm.classList.add("active");

            // Move success message to login form
            loginError.textContent = data.message;
            loginError.classList.remove("error-message");
            loginError.classList.add("success-message");
            // Clear the register form
            form.reset();
          }, 1000);
        } else {
          registerError.textContent = data.message;
          registerError.classList.add("error-message");
        }
      })
      .catch((error) => {
        registerError.textContent = "An error occurred. Please try again.";
        registerError.classList.add("error-message");
        console.error("Error:", error);
      });
  }
}
