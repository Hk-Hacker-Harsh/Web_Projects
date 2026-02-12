<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
require_once __DIR__ . '/admin_auth.php';

$message = "";

// 1. Handle Add/Update Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $v_name = mysqli_real_escape_string($conn, $_POST['vendor_name']);
    $user_id = (int)$_POST['user_id'];

    if (isset($_POST['update_vendor'])) {
        // UPDATE Logic
        $v_id = (int)$_POST['vendor_id'];
        $update_query = "UPDATE vendors SET name = '$v_name', user_id = $user_id WHERE id = $v_id";
        if (mysqli_query($conn, $update_query)) {
            $message = "<div class='alert alert-success'>Vendor updated successfully!</div>";
        }
    } elseif (isset($_POST['add_vendor'])) {
        // ADD Logic
        $add_query = "INSERT INTO vendors (name, user_id, status) VALUES ('$v_name', $user_id, 'active')";
        if (mysqli_query($conn, $add_query)) {
            $message = "<div class='alert alert-success'>New vendor added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// 2. Handle Delete Request
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $check = mysqli_query($conn, "SELECT id FROM products WHERE vendor_id = $delete_id LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $message = "<div class='alert alert-danger'>Cannot delete vendor! Products are linked to them.</div>";
    } else {
        mysqli_query($conn, "DELETE FROM vendors WHERE id = $delete_id");
        $message = "<div class='alert alert-success'>Vendor profile removed!</div>";
    }
}

// 3. Fetch Vendors with Product Counts and User Email
$query = "SELECT v.*, u.email as user_email, COUNT(p.id) as product_count 
          FROM vendors v 
          JOIN users u ON v.user_id = u.id
          LEFT JOIN products p ON v.id = p.vendor_id 
          GROUP BY v.id 
          ORDER BY v.name ASC";
$result = mysqli_query($conn, $query);

// 4. Logic to fetch data for the Edit Form
$edit_vendor = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $res = mysqli_query($conn, "SELECT * FROM vendors WHERE id = $edit_id");
    $edit_vendor = mysqli_fetch_assoc($res);
}

// 5. Fetch all users with 'vendor' role for the dropdown
$vendor_users = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role = 'vendor'");
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?php echo $edit_vendor ? 'Edit Vendor' : 'Add New Vendor'; ?></h5>
                </div>
                <div class="card-body">
                    <?php if ($message) echo $message; ?>
                    <form action="vendors.php" method="POST">
                        <?php if ($edit_vendor): ?>
                            <input type="hidden" name="vendor_id" value="<?php echo $edit_vendor['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Store/Vendor Name</label>
                            <input type="text" name="vendor_name" class="form-control" 
                                   value="<?php echo $edit_vendor ? htmlspecialchars($edit_vendor['name']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Linked User Account</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Select User Account</option>
                                <?php while($u = mysqli_fetch_assoc($vendor_users)): ?>
                                    <option value="<?= $u['id']; ?>" <?= ($edit_vendor && $edit_vendor['user_id'] == $u['id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($u['name']); ?> (<?= htmlspecialchars($u['email']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <small class="text-muted">Only users with 'vendor' role are listed.</small>
                        </div>
                        
                        <?php if ($edit_vendor): ?>
                            <button type="submit" name="update_vendor" class="btn btn-warning w-100">Update Vendor</button>
                            <a href="vendors.php" class="btn btn-link w-100 mt-2">Cancel Edit</a>
                        <?php else: ?>
                            <button type="submit" name="add_vendor" class="btn btn-success w-100">Add Vendor</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">All Vendors</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Store Name</th>
                                <th>Linked Account</th>
                                <th class="text-center">Products</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['name']); ?></div>
                                        <small class="text-muted">Vendor ID: #<?= $row['id']; ?></small>
                                    </td>
                                    <td>
                                        <div class="small"><?= htmlspecialchars($row['user_email']); ?></div>
                                        <small class="text-muted">User ID: <?= $row['user_id']; ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark"><?= $row['product_count']; ?></span>
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="btn-group">
                                            <a href="vendors.php?edit_id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="vendors.php?delete_id=<?= $row['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Confirm deletion?')">Delete</a>
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