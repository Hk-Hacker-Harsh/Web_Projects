<?php
// 1. LOGIC FIRST: Load dependencies and check sessions
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session.php';

// Check if user is already logged in BEFORE sending any HTML
redirect_if_logged_in();

$error_msg = "";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. PROCESS LOGIN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // We fetch the user by email
    $checkEmail = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $checkEmail);
    
    if(mysqli_num_rows($result) > 0){
        $user = mysqli_fetch_assoc($result);
        
        // Check 1: Verify Password
        if(password_verify($password, $user['password'])){
            
            // Check 2: Verify Role (The missing piece!)
            if($user['role'] !== 'customer') {
                $error_msg = "Access Denied: This login is for customers only.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                
                $url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : "profile.php";
                unset($_SESSION['redirect_url']); 
                
                header("Location: " . $url);
                exit(); 
            }
        } else {
            $error_msg = "Incorrect Password!";
        }
    } else {
        $error_msg = "User Not Registered! Please SignUp First";
    }
}

// 3. UI START: Only now do we include the header
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
            <h2 class="fw-bold text-dark">Login Account</h2>
            <p class="text-muted">Join Hk Store</p>
        </div>

        <?php if($error_msg): ?>
            <div class="alert alert-danger py-2 small"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label text-dark">Email</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-dark">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold text-white">Sign In</button>
        </form>
        
        <div class="text-center mt-4">
            <p class="mb-0 small text-dark">Don't have an account? <a href="signup.php" class="text-decoration-none fw-bold" style="color: #764ba2;">Signup</a></p>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/includes/footer.php';
?>