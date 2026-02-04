<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
require_once __DIR__ . '/admin_auth.php';

// 1. Fetch Quick Stats
$revenue_res = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status IN ('paid', 'delivered')");
$total_revenue = mysqli_fetch_assoc($revenue_res)['total'] ?? 0;

$order_count_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM orders");
$total_orders = mysqli_fetch_assoc($order_count_res)['total'] ?? 0;

$low_stock_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM products WHERE stock <= 5");
$low_stock_count = mysqli_fetch_assoc($low_stock_res)['total'] ?? 0;

$user_count_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM users");
$total_users = mysqli_fetch_assoc($user_count_res)['total'] ?? 0;

$msg_count_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM contact_messages");
$total_messages = mysqli_fetch_assoc($msg_count_res)['total'] ?? 0;

// NEW: Fetch Active Banner Count
$banner_count_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM banners WHERE status = 1");
$total_banners = mysqli_fetch_assoc($banner_count_res)['total'] ?? 0;
?>

<div class="container mt-5 mb-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="display-6 fw-bold">Welcome, Admin</h2>
            <p class="text-muted">Overview of HK Store performance and management.</p>
        </div>
    </div>

    <div class="row g-3 mb-5 text-center">
        <div class="col-md-2">
            <div class="card bg-primary text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Revenue</h6>
                    <h3 class="mb-0">$<?= number_format($total_revenue, 2); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Orders</h6>
                    <h3 class="mb-0"><?= $total_orders; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Low Stock</h6>
                    <h3 class="mb-0"><?= $low_stock_count; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Users</h6>
                    <h3 class="mb-0"><?= $total_users; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Messages</h6>
                    <h3 class="mb-0"><?= $total_messages; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Live Banners</h6>
                    <h3 class="mb-0"><?= $total_banners; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-4">Management Console</h4>
    <div class="row g-4">
        
        <div class="col-md-12">
            <div class="card h-100 shadow-sm border-primary hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-primary mb-3"><i class="bi bi-globe"></i></div>
                    <h5>Storefront</h5>
                    <p class="small text-muted">Live preview of HK Store.</p>
                    <a href="../index.php" target="_blank" class="btn btn-primary w-100 mt-2">Go to Site</a>
                </div>
            </div>
        </div>
        
        

        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-primary mb-3"><i class="bi bi-box-seam"></i></div>
                    <h5>Products</h5>
                    <p class="small text-muted">Manage inventory levels and details.</p>
                    <a href="products.php" class="btn btn-outline-primary w-100 mt-2">View Product List</a>
                    <a href="add_product.php" class="btn btn-link btn-sm mt-1 text-decoration-none">+ Add New Product</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-success mb-3"><i class="bi bi-receipt"></i></div>
                    <h5>Orders</h5>
                    <p class="small text-muted">Process customer orders and shipping.</p>
                    <a href="orders.php" class="btn btn-outline-success w-100 mt-2">Manage Orders</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow border-secondary">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-secondary mb-3"><i class="bi bi-chat-left-dots"></i></div>
                    <h5>Messages</h5>
                    <p class="small text-muted">Respond to customer contact inquiries.</p>
                    <a href="contact_messages.php" class="btn btn-outline-secondary w-100 mt-2">View Inquiries</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow border-primary">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-primary mb-3"><i class="bi bi-images"></i></div>
                    <h5>Banners</h5>
                    <p class="small text-muted">Manage homepage slider and promotions.</p>
                    <a href="manage_banners.php" class="btn btn-outline-primary w-100 mt-2">Manage Banners</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-info mb-3"><i class="bi bi-tags"></i></div>
                    <h5>Categories</h5>
                    <p class="small text-muted">Organize catalog grouping and labels.</p>
                    <a href="categories.php" class="btn btn-outline-info w-100 mt-2">Manage Categories</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-secondary mb-3"><i class="bi bi-truck"></i></div>
                    <h5>Vendors</h5>
                    <p class="small text-muted">Control your supplier and partner list.</p>
                    <a href="vendors.php" class="btn btn-outline-secondary w-100 mt-2">Manage Vendors</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-dark mb-3"><i class="bi bi-people"></i></div>
                    <h5>Users</h5>
                    <p class="small text-muted">Manage customer accounts and permissions.</p>
                    <a href="users.php" class="btn btn-outline-dark w-100 mt-2">Manage Users</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-danger mb-3"><i class="bi bi-ticket-perforated"></i></div>
                    <h5>Coupons</h5>
                    <p class="small text-muted">Create discounts and promotional codes.</p>
                    <a href="coupons.php" class="btn btn-outline-danger w-100 mt-2">Manage Coupons</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-warning mb-3"><i class="bi bi-star"></i></div>
                    <h5>Reviews</h5>
                    <p class="small text-muted">Moderate customer feedback and ratings.</p>
                    <a href="manage_reviews.php" class="btn btn-outline-warning w-100 mt-2">Manage Reviews</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-indigo mb-3" style="color: #6610f2;"><i class="bi bi-graph-up"></i></div>
                    <h5>Reports</h5>
                    <p class="small text-muted">Sales analytics and business insights.</p>
                    <a href="reports.php" class="btn btn-outline-indigo w-100 mt-2" style="border-color: #6610f2; color: #6610f2;">View Reports</a>
                </div>
            </div>
        </div>

        
        
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-indigo mb-3" style="color: #6610f2;"><i class="bi bi-graph-up"></i></div>
                    <h5>SEO</h5>
                    <p class="small text-muted">SEO Tools and Sitemap.</p>
                    <a href="seo_tools.php" class="btn btn-outline-indigo w-100 mt-2" style="border-color: #6610f2; color: #6610f2;">Check Here</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-indigo mb-3" style="color: #6610f2;"><i class="bi bi-graph-up"></i></div>
                    <h5>Stripe</h5>
                    <p class="small text-muted">Visit Stripe Dashboard.</p>
                    <a href="https://dashboard.stripe.com/" class="btn btn-outline-indigo w-100 mt-2" style="border-color: #6610f2; color: #6610f2;">Go to Stripe Dashboard</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center py-4">
                    <div class="display-5 text-indigo mb-3" style="color: #6610f2;"><i class="bi bi-graph-up"></i></div>
                    <h5>CSV</h5>
                    <p class="small text-muted">Export and Import CSV.</p>
                    <a href="data_management.php" class="btn btn-outline-indigo w-100 mt-2" style="border-color: #6610f2; color: #6610f2;">Export And Import</a>
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
    .hover-shadow { transition: all 0.3s ease; }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>