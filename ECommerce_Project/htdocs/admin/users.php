<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/admin_auth.php'; // Auth check before any output

$message = "";

// 1. Handle Delete Request (MUST happen before header.php)
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    // Assuming admin_auth starts the session, we can check user_id
    if ($delete_id == $_SESSION['admin_id']) {
        $message = "<div class='alert alert-danger'>You cannot delete your own account!</div>";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id = $delete_id");
        header("Location: users.php?msg=deleted");
        exit();
    }
}

// 2. Handle Create/Update User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = $_POST['password'];
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

    if ($user_id > 0) {
        // UPDATE Existing User
        $query = "UPDATE users SET name='$name', email='$email', role='$role' WHERE id=$user_id";
        if (!empty($password)) {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET name='$name', email='$email', role='$role', password='$hashed_pass' WHERE id=$user_id";
        }
        mysqli_query($conn, $query);
        $message = "<div class='alert alert-success'>User updated successfully!</div>";
    } else {
        // CREATE New User
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_pass', '$role')";
        if (mysqli_query($conn, $query)) {
            $new_id = mysqli_insert_id($conn);
            if ($role == 'vendor') {
                mysqli_query($conn, "INSERT INTO vendors (user_id, name, status) VALUES ($new_id, '$name', 'active')");
            }
            $message = "<div class='alert alert-success'>User created successfully!</div>";
        }
    }
}

// 3. Logic to Fetch Data for Edit Form & User List
$edit_user = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE id = $edit_id");
    $edit_user = mysqli_fetch_assoc($res);
}

$query = "SELECT u.id, u.name, u.email, u.role, u.created_at, 
          COUNT(o.id) as total_orders, 
          SUM(o.total_amount) as total_spent 
          FROM users u 
          LEFT JOIN orders o ON u.id = o.user_id 
          GROUP BY u.id 
          ORDER BY u.created_at DESC";

$result = mysqli_query($conn, $query);

// 4. NOW START SENDING HTML
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
?>

<div class="container-fluid mt-5 px-4">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?= $edit_user ? 'Edit User' : 'Create New User' ?></h5>
                </div>
                <div class="card-body">
                    <?= $message ?>
                    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') echo "<div class='alert alert-success'>User deleted successfully!</div>"; ?>
                    <form method="POST">
                        <?php if ($edit_user): ?>
                            <input type="hidden" name="user_id" value="<?= $edit_user['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?= $edit_user ? htmlspecialchars($edit_user['name']) : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= $edit_user ? htmlspecialchars($edit_user['email']) : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="customer" <?= ($edit_user && $edit_user['role'] == 'customer') ? 'selected' : '' ?>>Customer</option>
                                <option value="vendor" <?= ($edit_user && $edit_user['role'] == 'vendor') ? 'selected' : '' ?>>Vendor</option>
                                <option value="admin" <?= ($edit_user && $edit_user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <?= $edit_user ? '<small>(Leave blank to keep current)</small>' : '' ?></label>
                            <input type="password" name="password" class="form-control" <?= $edit_user ? '' : 'required' ?>>
                        </div>
                        <button type="submit" name="save_user" class="btn btn-success w-100">Save User</button>
                        <?php if ($edit_user): ?>
                            <a href="users.php" class="btn btn-link w-100 mt-2 text-decoration-none">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">User Management</h5>
                    <span class="badge bg-primary"><?= mysqli_num_rows($result); ?> Total Users</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name & Role</th>
                                <th>Email</th>
                                <th class="text-center">Activity</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['name']); ?>&size=32&background=random" class="rounded-circle me-2">
                                            <div>
                                                <div class="fw-bold"><?= htmlspecialchars($row['name']); ?></div>
                                                <span class="badge <?= $row['role'] == 'admin' ? 'bg-danger' : ($row['role'] == 'vendor' ? 'bg-warning text-dark' : 'bg-secondary') ?> px-2 py-1" style="font-size: 0.7rem;">
                                                    <?= strtoupper($row['role']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><small><?= htmlspecialchars($row['email']); ?></small></td>
                                    <td class="text-center">
                                        <div class="small">Orders: <?= $row['total_orders']; ?></div>
                                        <div class="fw-bold text-success">$<?= number_format($row['total_spent'] ?? 0, 2); ?></div>
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="btn-group">
                                            <a href="users.php?edit_id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="users.php?delete_id=<?= $row['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Delete user permanently?')">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>