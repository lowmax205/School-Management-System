<div class="modal fade" id="authModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="auth-container">
                    <h2>Welcome to Surigao del Norte State University</h2>
                    <p class="subtitle">Excellence, Innovation, and Social Transformation</p>
                    <div class="forms-container">
                        <div class="signin-signup">
                            <form id="loginForm" class="sign-in-form" action="server/login_handler.php" method="POST">
                                <h3>Sign In</h3>
                                <div id="login-errors" class="alert alert-danger" style="display: none;"></div>
                                <div class="form-group">
                                    <div class="error-container" id="login-email-error" style="color: red;"></div>
                                    <input type="email" class="form-control" name="email" id="login-email" placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <div class="error-container" id="login-password-error" style="color: red;"></div>
                                    <input type="password" class="form-control" name="password" id="login-password" placeholder="Password">
                                </div>
                                <button type="submit" class="btn btn-primary">Login</button>
                                <p class="toggle-text">Don't have an account? <a href="#" id="show-register">Register</a></p>
                            </form>
                            <form id="registerForm" class="sign-up-form" method="POST" action="server/register_handler.php">
                                <h3>Create Account</h3>
                                <div id="register-errors" class="alert alert-danger" style="display: none;" style="color: red;"></div>
                                <div class="form-group">
                                    <div class="error-container" id="register-email-error"></div>
                                    <input type="email" class="form-control" name="email" id="register-email" placeholder="Email" >
                                </div>
                                <div class="form-group">
                                    <div class="error-container" id="register-password-error" style="color: red;"></div>
                                    <input type="password" class="form-control" name="password" id="register-password" placeholder="Password" >
                                </div>
                                <div class="form-group">
                                    <div class="error-container" id="register-confirm-password-error" style="color: red;"></div>
                                    <input type="password" class="form-control" name="confirmPassword" id="register-confirm-password" placeholder="Confirm Password" >
                                </div>
                                <button type="submit" class="btn btn-primary">Register</button>
                                <p class="toggle-text">Already have an account? <a href="#" id="show-login">Login</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>