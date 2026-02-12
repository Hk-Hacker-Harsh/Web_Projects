<?php
require_once __DIR__ . '/../includes/session.php';

// If already logged in as a vendor, jump to dashboard
if (isset($_SESSION['vendor_logged_in']) && $_SESSION['vendor_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

require_once __DIR__ . '/../db.php';

$error = "";

// PROCESS LOGIN LOGIC FIRST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // JOIN users and vendors to get both account details and business name
    $query = "SELECT u.*, v.id as vendor_store_id, v.name as business_name, v.status as vendor_status 
              FROM users u 
              JOIN vendors v ON u.id = v.user_id 
              WHERE u.email = '$email' AND u.role = 'vendor' LIMIT 1";
              
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        // 1. Check if the account is active
        if ($row['vendor_status'] !== 'active') {
            $error = "Your vendor account is " . $row['vendor_status'] . ". Please contact admin.";
        } 
        // 2. Verify Password
        elseif (password_verify($password, $row['password'])) {
            $_SESSION['vendor_logged_in'] = true;
            $_SESSION['user_id'] = $row['id'];           // The User ID
            $_SESSION['vendor_id'] = $row['vendor_store_id']; // The Vendor ID for product filtering
            $_SESSION['vendor_name'] = $row['business_name'];
            
            // REDIRECT (This will now work because no HTML has been sent)
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No vendor account found with this email.";
    }
}

// NOW INCLUDE THE UI
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white text-center py-3">
                    <h4 class="mb-0">Vendor Login</h4>
                </div>
                <div class="card-body p-4">
                    <?php if($error): ?>
                        <div class="alert alert-danger p-2 small"><?= $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 btn-lg">Login to Vendor Panel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>