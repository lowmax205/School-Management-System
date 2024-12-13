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
  const form = document.querySelector('.sign-in-form');
  const email = form.querySelector('input[type="email"]').value;
  const password = form.querySelector('input[type="password"]').value;

  if (!validateEmail(email)) {
      document.getElementById('email-error-login').textContent = 'Please enter a valid email address';
      isValid = false;
  }

  if (password.length < 6) {
      document.getElementById('password-error-login').textContent = 'Password must be at least 6 characters long';
      isValid = false;
  }

  if (isValid) {
      const formData = new FormData();
      formData.append('email', email);
      formData.append('password', password);

      fetch('server/login_handler.php', {
          method: 'POST',
          body: formData
      })
      .then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              window.location.href = 'pages/auth/base_dashboard.php';
          } else {
              document.getElementById('login-error').textContent = data.message;
          }
      })
      .catch(error => {
          document.getElementById('login-error').textContent = 'An error occurred. Please try again.';
          console.error('Error:', error);
      });
  }
}

function validateRegisterForm() {
  clearErrors();
  let isValid = true;
  
  // Get form inputs with more specific selectors
  const form = document.querySelector('.sign-up-form');
  const inputs = form.querySelectorAll('input');
  const email = inputs[0].value;
  const password = inputs[1].value;
  const confirmPassword = inputs[2].value;

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
      const formData = new FormData();
      formData.append('email', email);
      formData.append('password', password);
      formData.append('confirmPassword', confirmPassword);

      fetch('/School-Management-System/server/register_handler.php', {
          method: 'POST',
          body: formData
      })
      .then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              registerForm.reset();
              document.getElementById('register-error').textContent = data.message;
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