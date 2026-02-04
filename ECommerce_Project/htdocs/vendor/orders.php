<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
// Assuming you have a vendor_auth.php similar to admin_auth.php
if (!isset($_SESSION['vendor_logged_in'])) {
    header("Location: login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch orders that contain at least one product from this vendor
$query = "SELECT DISTINCT o.*, u.name as customer_name 
          FROM orders o
          JOIN users u ON o.user_id = u.id
          JOIN order_items oi ON o.id = oi.order_id
          JOIN products p ON oi.product_id = p.id
          WHERE p.vendor_id = $vendor_id
          ORDER BY o.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Sales Orders</h2>
        <span class="badge bg-dark">Vendor: <?= htmlspecialchars($_SESSION['vendor_name']); ?></span>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Your Items</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): 
                        $order_id = $row['id'];
                        
                        // Get specific items from THIS vendor in this order
                        $item_query = "SELECT p.name, oi.quantity, oi.price 
                                     FROM order_items oi 
                                     JOIN products p ON oi.product_id = p.id 
                                     WHERE oi.order_id = $order_id AND p.vendor_id = $vendor_id";
                        $items_result = mysqli_query($conn, $item_query);
                        
                        $vendor_subtotal = 0;
                    ?>
                        <tr>
                            <td>#<?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['customer_name']); ?></td>
                            <td>
                                <ul class="list-unstyled mb-0 small">
                                    <?php while ($item = mysqli_fetch_assoc($items_result)): 
                                        $vendor_subtotal += ($item['price'] * $item['quantity']);
                                    ?>
                                        <li><?= $item['quantity']; ?>x <?= htmlspecialchars($item['name']); ?></li>
                                    <?php endwhile; ?>
                                </ul>
                                <div class="fw-bold mt-1 text-success">
                                    Your Share: $<?= number_format($vendor_subtotal, 2); ?>
                                </div>
                            </td>
                            <td><?= date('d M Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <span class="badge rounded-pill bg-secondary">
                                    <?= ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td class="text-end px-4">
                                <a href="order_details.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>