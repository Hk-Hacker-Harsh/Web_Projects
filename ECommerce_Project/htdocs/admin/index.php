<?php
require_once __DIR__ . '/../includes/session.php'; 
require_once __DIR__ . '/../db.php';

// 1. SESSION CHECK (Safe here because no HTML sent yet)
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

// 2. PROCESS LOGIN LOGIC (Safe here because no HTML sent yet)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email' AND role = 'admin' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_id'] = $row['id'];
            
            // REDIRECT (This is line 30, it will now work!)
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid Admin Credentials.";
        }
    } else {
        $error = "Access Denied: Not an Admin Account.";
    }
}

// 3. NOW INCLUDE THE UI (HTML output starts here)
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .adminlog { margin-top: 25px; }
</style>

<div class="container adminlog">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">Admin Portal</h3>
                    <?php if($error): ?>
                        <div class="alert alert-danger p-2 small"><?= $error; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Admin Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Enter Dashboard</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/../includes/footer.php';
?>