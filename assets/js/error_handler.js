document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const registerForm = document.getElementById("registerForm");
  
    function showError(input, message) {
      const errorDiv = document.getElementById(`${input.id}-error`);
      errorDiv.textContent = message;
      errorDiv.style.display = "block";
      input.classList.add("is-invalid");
    }
  
    function clearErrors(form) {
      const errorContainers = form.querySelectorAll(".error-container");
      errorContainers.forEach((div) => {
        div.textContent = "";
        div.style.display = "none";
      });
      const inputs = form.querySelectorAll(".form-control");
      inputs.forEach((input) => {
        input.classList.remove("is-invalid");
      });
    }
  
    function validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    }
  
    function validatePassword(password) {
      return password.length >= 6;
    }
  
    function validateLoginForm() {
      clearErrors(loginForm);
      let isValid = true;
      const email = loginForm.querySelector("#login-email");
      const password = loginForm.querySelector("#login-password");
  
      if (!email.value) {
        showError(email, "Email is required");
        isValid = false;
      } else if (!validateEmail(email.value)) {
        showError(email, "Please enter a valid email address");
        isValid = false;
      }
  
      if (!password.value) {
        showError(password, "Password should not be empty");
        isValid = false;
      }
  
      return isValid;
    }
  
    function validateRegisterForm() {
      clearErrors(registerForm);
      let isValid = true;
      const email = registerForm.querySelector("#register-email");
      const password = registerForm.querySelector("#register-password");
      const confirmPassword = registerForm.querySelector("#register-confirm-password");
  
      if (!email.value) {
        showError(email, "Email is required");
        isValid = false;
      } else if (!validateEmail(email.value)) {
        showError(email, "Please enter a valid email address");
        isValid = false;
      }
  
      if (!password.value) {
        showError(password, "Password is required");
        isValid = false;
      } else if (!validatePassword(password.value)) {
        showError(password, "Password must be at least 6 characters long");
        isValid = false;
      }
  
      if (!confirmPassword.value) {
        showError(confirmPassword, "Please confirm your password");
        isValid = false;
      } else if (password.value !== confirmPassword.value) {
        showError(confirmPassword, "Passwords do not match");
        isValid = false;
      }
  
      return isValid;
    }
  
    // Handle form submissions with AJAX
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();
      clearErrors(loginForm);
      if (validateLoginForm()) {
        const formData = new FormData(loginForm);
        fetch('server/login_handler.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'error') {
            const errorDiv = document.getElementById('login-errors');
            errorDiv.textContent = data.message;
            errorDiv.style.display = 'block';
            // Only show field-specific errors for empty fields
            if (data.message === 'Email is required') {
              showError(document.getElementById('login-email'), data.message);
            } else if (data.message === 'Password should not be empty') {
              showError(document.getElementById('login-password'), data.message);
            }
          } else if (data.status === 'success') {
            window.location.href = './pages/auth/base_dashboard.php'; // Redirect to dashboard
          }
        })
        .catch(error => {
          console.error('Error:', error);
          const errorDiv = document.getElementById('login-errors');
          errorDiv.textContent = 'An error occurred. Please try again later.';
          errorDiv.style.display = 'block';
        });
      }
    });
  
    registerForm.addEventListener("submit", function (e) {
      e.preventDefault();
      clearErrors(registerForm);
      if (validateRegisterForm()) {
        const formData = new FormData(registerForm);
        fetch('server/register_handler.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          const errorDiv = document.getElementById('register-errors');
          errorDiv.style.display = 'block';
          
          if (data.status === 'error') {
            errorDiv.className = 'alert alert-danger';
            errorDiv.textContent = data.message;
            // Show error under specific field if applicable
            if (data.message.toLowerCase().includes('password')) {
              showError(document.getElementById('register-password'), data.message);
            } else if (data.message.toLowerCase().includes('email')) {
              showError(document.getElementById('register-email'), data.message);
            }
          } else if (data.status === 'success') {
            errorDiv.className = 'alert alert-success';
            errorDiv.textContent = data.message;
            
            // Clear form and switch to login after successful registration
            setTimeout(() => {
              document.getElementById('show-login').click();
              errorDiv.style.display = 'none';
              registerForm.reset();
              clearErrors(registerForm);
            }, 2000);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          const errorDiv = document.getElementById('register-errors');
          errorDiv.className = 'alert alert-danger';
          errorDiv.textContent = 'An error occurred. Please try again later.';
          errorDiv.style.display = 'block';
        });
      }
    });
  });