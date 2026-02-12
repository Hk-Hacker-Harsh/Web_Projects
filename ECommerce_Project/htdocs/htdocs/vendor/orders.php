<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';

if (!isset($_SESSION['vendor_logged_in'])) {
    header("Location: login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch orders with shipping_address included
$query = "SELECT DISTINCT o.*, u.name as customer_name 
          FROM orders o
          JOIN users u ON o.user_id = u.id
          JOIN order_items oi ON o.id = oi.order_id
          JOIN products p ON oi.product_id = p.id
          WHERE p.vendor_id = $vendor_id
          ORDER BY o.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">My Sales Orders</h2>
            <p class="text-muted">Manage shipping and track your vendor earnings.</p>
        </div>
        <span class="badge bg-dark p-2">Vendor: <?= htmlspecialchars($_SESSION['vendor_name']); ?></span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>Customer & Shipping</th> <th>Your Items</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): 
                            $order_id = $row['id'];
                            
                            // Get specific items from THIS vendor in this order
                            $item_query = "SELECT p.name, oi.quantity, p.price 
                                         FROM order_items oi 
                                         JOIN products p ON oi.product_id = p.id 
                                         WHERE oi.order_id = $order_id AND p.vendor_id = $vendor_id";
                            $items_result = mysqli_query($conn, $item_query);
                            
                            $vendor_subtotal = 0;
                        ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= $row['id']; ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['customer_name']); ?></div>
                                    <div class="small text-muted" style="max-width: 200px;">
                                        <i class="bi bi-geo-alt text-danger"></i> 
                                        <?= nl2br(htmlspecialchars($row['shipping_address'] ?? 'Address not found')); ?>
                                    </div>
                                </td>
                                <td>
                                    <ul class="list-unstyled mb-0 small">
                                        <?php while ($item = mysqli_fetch_assoc($items_result)): 
                                            $vendor_subtotal += ($item['price'] * $item['quantity']);
                                        ?>
                                            <li><i class="bi bi-box-seam me-1"></i> <?= $item['quantity']; ?>x <?= htmlspecialchars($item['name']); ?></li>
                                        <?php endwhile; ?>
                                    </ul>
                                    <div class="fw-bold mt-1 text-primary">
                                        Subtotal: $<?= number_format($vendor_subtotal, 2); ?>
                                    </div>
                                </td>
                                <td><?= date('d M Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <?php 
                                        $status_class = match($row['status']) {
                                            'paid' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'shipped' => 'bg-info',
                                            'delivered' => 'bg-primary',
                                            default => 'bg-secondary'
                                        };
                                    ?>
                                    <span class="badge <?= $status_class; ?> rounded-pill">
                                        <?= ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="order_details.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary">Details</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No orders found for your products.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>