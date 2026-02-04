<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';

// 1. Authentication Check
if (!isset($_SESSION['vendor_logged_in'])) {
    header("Location: login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Fetch Order & Customer Details
$query = "SELECT o.*, u.name as customer_name, u.email as customer_email 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.id = $order_id LIMIT 1";
$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Order not found.</div></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit();
}

// 3. Fetch specific items from THIS vendor in this order
// Note: We join with products to filter by vendor_id
$items_query = "SELECT oi.*, p.name as product_name, p.image 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = $order_id AND p.vendor_id = $vendor_id";
$items_result = mysqli_query($conn, $items_query);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order Details #<?= $order['id']; ?></h2>
        <a href="orders.php" class="btn btn-outline-secondary btn-sm">Back to Orders</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">Customer Information</div>
                <div class="card-body">
                    <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']); ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']); ?></p>
                    <hr>
                    <p class="mb-1"><strong>Order Date:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                    <p class="mb-1"><strong>Payment:</strong> <span class="badge bg-info text-dark"><?= strtoupper($order['payment_method']); ?></span></p>
                    <p class="mb-0"><strong>Status:</strong> <span class="badge bg-warning text-dark"><?= ucfirst($order['status']); ?></span></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Items to Ship</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $vendor_total = 0;
                            while ($item = mysqli_fetch_assoc($items_result)): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $vendor_total += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../Assets/upload/<?= $item['image']; ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($item['product_name']); ?></div>
                                            <?php if(!empty($item['variations'])): ?>
                                                <small class="text-muted"><?= htmlspecialchars($item['variations']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>$<?= number_format($item['price'], 2); ?></td>
                                <td><?= $item['quantity']; ?></td>
                                <td class="text-end">$<?= number_format($subtotal, 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Your Earnings for this Order:</th>
                                <th class="text-end text-success h5">$<?= number_format($vendor_total, 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="alert alert-info mt-3 small">
                <i class="bi bi-info-circle"></i> This view only shows items assigned to your vendor account. The customer's Grand Total may include items from other vendors.
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>