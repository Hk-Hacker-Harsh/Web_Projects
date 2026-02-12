<?php
// 1. LOGIC & AUTH FIRST (No HTML output allowed yet)
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session.php';

// Check if user is already logged in BEFORE sending header.php
redirect_if_logged_in();

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $checkEmail = "SELECT email FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $checkEmail);
    
    if (mysqli_num_rows($result) > 0) {
        // Redirect if account exists
        header("Location: signin.php?msg=exists");
        exit();
    } else {
        $query = "INSERT INTO users (name, email, password) VALUES ('$fullname', '$email', '$password')";
        if (mysqli_query($conn, $query)) {
            header("Location: signin.php?msg=success");
            exit();
        } else {
            $error_msg = "Registration failed. Please try again.";
        }
    }
}

// 2. UI START: Only now do we include the header
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .auth-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .signup-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        padding: 40px;
        width: 100%;
        max-width: 400px;
    }
    .btn-primary {
        background: #764ba2 !important;
        border: none;
        padding: 12px;
    }
</style>

<div class="auth-bg">
    <div class="signup-container">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark">Create Account</h2>
            <p class="text-muted">Join Hk Store</p>
        </div>

        <?php if($error_msg): ?>
            <div class="alert alert-danger py-2 small"><?= $error_msg; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label text-dark">Full Name</label>
                <input type="text" name="fullname" class="form-control" placeholder="John Doe" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-dark">Email</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-dark">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="terms" required>
                <label class="form-check-label small text-dark" for="terms">I agree to the <a href="t&c.php">Terms & Conditions</a></label>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold text-white">Sign Up</button>
        </form>
        
        <div class="text-center mt-4">
            <p class="mb-0 small text-dark">Already have an account? <a href="signin.php" class="text-decoration-none fw-bold" style="color: #764ba2;">Login</a></p>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/includes/footer.php';
?>