<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
require_once __DIR__ . '/admin_auth.php';

// 1. Lifetime Stats
$lifetime_query = "SELECT 
                    SUM(total_amount) as total_rev, 
                    COUNT(id) as total_orders,
                    AVG(total_amount) as avg_order_value
                   FROM orders WHERE status IN ('paid', 'delivered')";
$lifetime_res = mysqli_fetch_assoc(mysqli_query($conn, $lifetime_query));

// 2. Sales by Category (Data Science approach: Aggregation)
$cat_query = "SELECT c.name, SUM(oi.price * oi.quantity) as revenue
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              JOIN categories c ON p.category_id = c.id
              GROUP BY c.id
              ORDER BY revenue DESC";
$cat_result = mysqli_query($conn, $cat_query);

// 3. Top 5 Best Selling Products
$top_prod_query = "SELECT p.name, SUM(oi.quantity) as units_sold, SUM(oi.price * oi.quantity) as total_sales
                   FROM order_items oi
                   JOIN products p ON oi.product_id = p.id
                   GROUP BY p.id
                   ORDER BY units_sold DESC LIMIT 5";
$top_prod_result = mysqli_query($conn, $top_prod_query);
?>

<div class="container mt-5 mb-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold">Business Intelligence Report</h2>
            <p class="text-muted">Analyzing sales data and product performance for HK Store.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-light">
                <h6 class="text-muted small text-uppercase">Lifetime Revenue</h6>
                <h3 class="text-primary">$<?= number_format($lifetime_res['total_rev'], 2); ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-light">
                <h6 class="text-muted small text-uppercase">Completed Orders</h6>
                <h3 class="text-success"><?= $lifetime_res['total_orders']; ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-light">
                <h6 class="text-muted small text-uppercase">Avg. Order Value</h6>
                <h3 class="text-info">$<?= number_format($lifetime_res['avg_order_value'], 2); ?></h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Revenue by Category</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($cat['name']); ?></td>
                                <td class="text-end fw-bold">$<?= number_format($cat['revenue'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Top 5 Best Sellers</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Units</th>
                                <th class="text-end">Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($prod = mysqli_fetch_assoc($top_prod_result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($prod['name']); ?></td>
                                <td><?= $prod['units_sold']; ?></td>
                                <td class="text-end fw-bold">$<?= number_format($prod['total_sales'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <button onclick="window.print()" class="btn btn-outline-dark">
            <i class="bi bi-printer"></i> Print Report
        </button>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>