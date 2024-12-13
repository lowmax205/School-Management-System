<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="auth-container">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 10px; right: 10px;"></button>
                    <h2>Welcome to Surigao del Norte State University</h2>
                    <p class="subtitle">Excellence, Innovation, and Social Transformation</p>
                    
                    <form class="sign-in-form active" id="loginForm" novalidate action="../server/login_handler.php" method="POST">
                        <h3>Sign In</h3>
                        <div id="login-error" class="error-message"></div>
                        <div class="form-group">
                            <div id="email-error-login" class="error-message"></div>
                            <input type="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <div id="password-error-login" class="error-message"></div>
                            <input type="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                        <p class="toggle-text">Don't have an account? <a href="#" id="show-register">Register</a></p>
                    </form>

                    <form class="sign-up-form" id="registerForm" novalidate action="../server/register_handler.php" method="POST">
                        <h3>Create Account</h3>
                        <div id="register-error" class="error-message"></div>
                        
                        <div class="form-group">
                            <div id="email-error-register" class="error-message"></div>
                            <input type="email" class="form-control" placeholder="Email" required>
                        </div>
                        
                        <div class="form-group">
                            <div id="password-error-register" class="error-message"></div>
                            <input type="password" class="form-control" placeholder="Password" required>
                        </div>
                        
                        <div class="form-group">
                            <div id="confirm-password-error" class="error-message"></div>
                            <input type="password" class="form-control" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                        <p class="toggle-text">Already have an account? <a href="#" id="show-login">Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/error_handler.js"></script>