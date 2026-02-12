<?php
// 1. SESSION & AUTH FIRST (No HTML output allowed here)
require_once __DIR__ . '/../includes/session.php'; 

// Check if the vendor is logged in BEFORE including header.php
if (!isset($_SESSION['vendor_logged_in']) || $_SESSION['vendor_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 2. DATABASE & OTHER LOGIC
require_once __DIR__ . '/../db.php';
$vendor_id = $_SESSION['vendor_id'];

// Fetch Stats for THIS Vendor
$earning_query = "SELECT SUM(oi.price * oi.quantity) as total 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id 
                  JOIN orders o ON oi.order_id = o.id
                  WHERE p.vendor_id = $vendor_id AND o.status IN ('paid', 'delivered')";
$earning_res = mysqli_query($conn, $earning_query);
$total_earnings = mysqli_fetch_assoc($earning_res)['total'] ?? 0;

$product_count_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM products WHERE vendor_id = $vendor_id");
$total_products = mysqli_fetch_assoc($product_count_res)['total'] ?? 0;

// 3. NOW START SENDING HTML
require_once __DIR__ . '/../includes/header.php'; // HTML output starts here!
require_once __DIR__ . '/../includes/dashboard_nav.php';
?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">Business Overview</h2>
            <p class="text-muted small">Storefront: <span class="badge bg-dark"><?= htmlspecialchars($_SESSION['vendor_name']); ?></span></p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Total Earnings</h6>
                    <h2 class="mb-0">$<?= number_format($total_earnings, 2); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Active Listings</h6>
                    <h2 class="mb-0"><?= $total_products; ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3">
        <div class="col-md-6">
            <a href="products.php" class="btn btn-primary w-100 py-3">Manage Inventory</a>
        </div>
        <div class="col-md-6">
            <a href="orders.php" class="btn btn-success w-100 py-3">View Orders</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>