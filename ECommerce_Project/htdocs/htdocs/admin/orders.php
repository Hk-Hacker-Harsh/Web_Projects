<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
require_once __DIR__ . '/admin_auth.php';

// 1. Handle Status Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_query = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
    if (mysqli_query($conn, $update_query)) {
        $message = "<div class='alert alert-success mt-3'>Order #$order_id updated to $new_status.</div>";
    }
}

// 2. Fetch All Orders with User Info and Vendor List
// Included o.shipping_address in the selection
$query = "SELECT o.*, u.name as customer_name, 
          GROUP_CONCAT(DISTINCT v.name SEPARATOR ', ') as vendors_involved
          FROM orders o
          JOIN users u ON o.user_id = u.id
          LEFT JOIN order_items oi ON o.id = oi.order_id
          LEFT JOIN products p ON oi.product_id = p.id
          LEFT JOIN vendors v ON p.vendor_id = v.id
          GROUP BY o.id
          ORDER BY o.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<div class="container-fluid mt-5 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order Management</h2>
    </div>

    <?php if (isset($message)) echo $message; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer & Shipping</th> <th>Vendors Involved</th>
                        <th>Total Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>#<?= $row['id']; ?></td>
                            <td>
                                <div><strong><?= htmlspecialchars($row['customer_name']); ?></strong></div>
                                <div class="small text-muted" style="max-width: 250px;">
                                    <i class="bi bi-geo-alt-fill"></i> 
                                    <?= nl2br(htmlspecialchars($row['shipping_address'] ?? 'No address provided')); ?>
                                </div>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars($row['vendors_involved'] ?? 'N/A'); ?></small></td>
                            <td class="fw-bold text-primary">$<?= number_format($row['total_amount'], 2); ?></td>
                            <td><?= date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                            <td>
                                <?php 
                                    $badge = match($row['status']) {
                                        'paid' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'shipped' => 'bg-info',
                                        'delivered' => 'bg-primary',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                ?>
                                <span class="badge <?= $badge; ?>"><?= ucfirst($row['status']); ?></span>
                            </td>
                            <td class="text-end px-4">
                                <form action="orders.php" method="POST" class="d-inline-flex gap-2">
                                    <input type="hidden" name="order_id" value="<?= $row['id']; ?>">
                                    <select name="status" class="form-select form-select-sm" style="width: 130px;">
                                        <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="paid" <?= $row['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="shipped" <?= $row['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="delivered" <?= $row['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="cancelled" <?= $row['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-sm btn-dark">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>