<?php
// 1. SYSTEM LOGIC: MUST BE AT THE VERY TOP
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session.php';

// Authentication Check: Must happen before header.php to avoid header errors
protect_page(); 

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. DATA FETCHING WITH SECURITY CHECK
// We join with the orders table to ensure this order_id actually belongs to the logged-in user_id
$order_query = "SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id LIMIT 1";
$order_result = mysqli_query($conn, $order_query);
$order_data = mysqli_fetch_assoc($order_result);

// If order doesn't exist or doesn't belong to the user, redirect to profile
if (!$order_data) {
    header("Location: profile.php"); 
    exit();
}

// Fetch items within this order
$items_query = "SELECT oi.*, p.name, p.image 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);

// 3. DISPLAY LOGIC: START SENDING HTML
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Order Details</h2>
        <a href="profile.php" class="btn btn-outline-secondary btn-sm">Back to History</a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0 py-1">Summary</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Order ID:</strong> #<?= $order_data['id']; ?></p>
                    <p class="mb-1"><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($order_data['created_at'])); ?></p>
                    <p class="mb-1"><strong>Payment:</strong> <?= strtoupper($order_data['payment_method'] ?? 'COD'); ?></p>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Status:</span>
                        <?php 
                            $status_class = match($order_data['status']) {
                                'paid' => 'bg-success',
                                'pending' => 'bg-warning text-dark',
                                'shipped' => 'bg-info',
                                'delivered' => 'bg-primary',
                                default => 'bg-secondary'
                            };
                        ?>
                        <span class="badge <?= $status_class; ?>"><?= ucfirst($order_data['status']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total Paid:</span>
                        <span class="text-primary">$<?= number_format($order_data['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="mt-3 text-center">
                <button onclick="window.print()" class="btn btn-link btn-sm text-muted">
                    <i class="bi bi-printer"></i> Print Invoice
                </button>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end px-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="Assets/upload/<?= $item['image']; ?>" class="rounded border me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <span class="fw-bold"><?= htmlspecialchars($item['name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center">$<?= number_format($item['price'], 2); ?></td>
                                    <td class="text-center"><?= $item['quantity']; ?></td>
                                    <td class="text-end px-4 fw-bold">$<?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>