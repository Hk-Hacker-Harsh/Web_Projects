<?php
// Determine if we are in the admin or vendor folder to set the correct base path
$is_admin = isset($_SESSION['admin_logged_in']);
$is_vendor = isset($_SESSION['vendor_logged_in']);

if ($is_admin || $is_vendor): ?>
<div class="bg-light border-bottom py-2 mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="../index.php" class="text-decoration-none text-muted">
                        <i class="bi bi-house-door"></i> Store
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <a href="dashboard.php" class="text-decoration-none fw-bold">
                        <?= $is_admin ? 'Admin Dashboard' : 'Vendor Panel'; ?>
                    </a>
                </li>
            </ol>
        </nav>

        <div class="d-flex gap-3">
            <?php if ($is_admin): ?>
                <a href="products.php" class="btn btn-sm btn-outline-dark">Products</a>
                <a href="orders.php" class="btn btn-sm btn-outline-dark">Orders</a>
                <a href="manage_banners.php" class="btn btn-sm btn-outline-dark">Banners</a>
            <?php elseif ($is_vendor): ?>
                <a href="products.php" class="btn btn-sm btn-outline-dark">My Products</a>
                <a href="orders.php" class="btn btn-sm btn-outline-dark">My Sales</a>
                <a href="add_product.php" class="btn btn-sm btn-success">+ New</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>