<?php
require_once __DIR__ . '/admin_auth.php'; // Gatekeeper
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';

$message = "";

// 1. Handle Coupon Creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_coupon'])) {
    $code = strtoupper(mysqli_real_escape_string($conn, $_POST['code']));
    $type = mysqli_real_escape_string($conn, $_POST['discount_type']);
    $value = mysqli_real_escape_string($conn, $_POST['value']);
    $expiry = mysqli_real_escape_string($conn, $_POST['expiry']);

    $query = "INSERT INTO coupons (code, discount_type, value, expiry) 
              VALUES ('$code', '$type', '$value', '$expiry')";
    
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert alert-success p-2'>Coupon '$code' created!</div>";
    } else {
        $message = "<div class='alert alert-danger p-2'>Error: Code might already exist.</div>";
    }
}

// 2. Handle Delete
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM coupons WHERE id = $id");
    header("Location: coupons.php?msg=deleted");
    exit();
}

// 3. Fetch All Coupons
$coupons = mysqli_query($conn, "SELECT * FROM coupons ORDER BY expiry ASC");
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white"><h5>Create Coupon</h5></div>
                <div class="card-body">
                    <?= $message; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Coupon Code</label>
                            <input type="text" name="code" class="form-control" placeholder="SAVE50" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="discount_type" class="form-select">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount ($)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Discount Value</label>
                            <input type="number" step="0.01" name="value" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expiry" class="form-control" min="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <button type="submit" name="add_coupon" class="btn btn-success w-100">Create Coupon</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white"><h5>Active Coupons</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Expiry</th>
                                <th>Status</th>
                                <th class="text-end px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($coupons)): 
                                $is_expired = (strtotime($row['expiry']) < strtotime(date('Y-m-d')));
                            ?>
                            <tr class="<?= $is_expired ? 'table-light text-muted' : ''; ?>">
                                <td><strong><?= $row['code']; ?></strong></td>
                                <td>
                                    <?= ($row['discount_type'] == 'percentage') ? $row['value'].'%' : '$'.$row['value']; ?>
                                </td>
                                <td><?= date('d M Y', strtotime($row['expiry'])); ?></td>
                                <td>
                                    <?php if($is_expired): ?>
                                        <span class="badge bg-danger">Expired</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end px-4">
                                    <a href="coupons.php?delete_id=<?= $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Delete coupon?')">Delete</a>
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