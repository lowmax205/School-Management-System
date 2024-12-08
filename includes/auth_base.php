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
                            <form id="loginForm" class="sign-in-form" action="server/login.php" method="POST">
                                <h3>Sign In</h3>
                                <div id="login-errors" class="alert alert-danger" style="display: none;"></div>
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Login</button>
                                <p class="toggle-text">Don't have an account? <a href="#" id="show-register">Register</a></p>
                            </form>
                            <form id="registerForm" class="sign-up-form" method="POST" action="server/register.php" novalidate>
                                <h3>Create Account</h3>
                                <div id="register-errors" class="alert alert-danger" style="display: none;"></div>
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email" id="register-email" placeholder="Email" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password" id="register-password" placeholder="Password" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" name="confirmPassword" id="register-confirm-password" placeholder="Confirm Password" required>
                                    <div class="invalid-feedback"></div>
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