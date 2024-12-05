<?php
$auth_response = $_SESSION['auth_response'] ?? null;
unset($_SESSION['auth_response']);
?>
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="auth-container">
                    <h2>Welcome to Surigao del Norte State University</h2>
                    <p class="subtitle">Excellence, Innovation, and Social Transformation</p>
                    
                    <form class="sign-in-form active">
                        <h3>Sign In</h3>
                        <div id="login-message"></div>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                        <p class="toggle-text">Don't have an account? <a href="#" id="show-register">Register</a></p>
                    </form>

                    <form class="sign-up-form" method="POST" action="server/register.php">
                        <h3>Create Account</h3>
                        <?php if ($auth_response): ?>
                            <div class="alert alert-<?php echo $auth_response['status'] === 'success' ? 'success' : 'danger'; ?> mb-3">
                                <?php echo $auth_response['message']; ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                        <p class="toggle-text">Already have an account? <a href="#" id="show-login">Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($auth_response): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var authModal = new bootstrap.Modal(document.getElementById('authModal'));
    authModal.show();
    <?php if ($auth_response['status'] === 'error'): ?>
    document.querySelector('.sign-up-form').classList.add('active');
    document.querySelector('.sign-in-form').classList.remove('active');
    <?php endif; ?>
});
</script>
<?php endif; ?>
