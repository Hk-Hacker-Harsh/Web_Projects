<?php
$current_page = basename($_SERVER['PHP_SELF']);

$is_admin = isset($_SESSION['admin_logged_in']);
$is_vendor = isset($_SESSION['vendor_logged_in']);

if ($is_admin || $is_vendor): ?>
<div class="d-flex shadow bg-light">
    <nav id="sidebar" class="bg-dark text-white border-end" style="width: 250px; min-height: 100vh;">
        <div class="p-3">
            <h5 class="fw-bold border-bottom pb-3 mb-4">
                <i class="bi bi-person-badge me-2"></i>
                <?= $is_admin ? 'Admin Panel' : 'Vendor Panel'; ?>
            </h5>
            
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white <?= ($current_page == 'dashboard.php') ? 'active bg-primary' : ''; ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                
                

                <?php if ($is_admin): ?>
                    <li class="nav-item mb-2">
                        <a href="products.php" class="nav-link text-white <?= ($current_page == 'products.php' || $current_page == 'add_product.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-box-seam me-2"></i> Products
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="orders.php" class="nav-link text-white <?= ($current_page == 'orders.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-receipt me-2"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="users.php" class="nav-link text-white <?= ($current_page == 'users.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-people me-2"></i> Users
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="categories.php" class="nav-link text-white <?= ($current_page == 'categories.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-tags me-2"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="manage_banners.php" class="nav-link text-white <?= ($current_page == 'manage_banners.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-images me-2"></i> Banners
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="coupons.php" class="nav-link text-white <?= ($current_page == 'coupons.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-ticket-perforated me-2"></i> Coupons
                        </a>
                    </li>
                	<li class="nav-item mb-2">
                        <a href="manage_reviews.php" class="nav-link text-white <?= ($current_page == 'manage_reviews.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-ticket-perforated me-2"></i> Reviews
                        </a>
                    </li>
                	<li class="nav-item mb-2">
                        <a href="reports.php" class="nav-link text-white <?= ($current_page == 'reports.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-ticket-perforated me-2"></i> Reports
                        </a>
                    </li>
                	<li class="nav-item mb-2">
                        <a href="vendors.php" class="nav-link text-white <?= ($current_page == 'vendors.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-ticket-perforated me-2"></i> Vendors
                        </a>
                    </li>
                	<li class="nav-item mb-2">
                        <a href="contact_messages.php" class="nav-link text-white <?= ($current_page == 'contact_messages.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-ticket-perforated me-2"></i> Messages
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="seo_tools.php" class="nav-link text-white <?= ($current_page == 'seo_tools.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-graph-up me-2"></i> SEO Tools
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="data_management.php" class="nav-link text-white <?= ($current_page == 'data_management.php') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-database me-2"></i> CSV Data
                        </a>
                    </li>
                
                
                 <li class="nav-item mb-2 border-top pt-3">
                    <a href="../index.php" class="nav-link text-white">
                        <i class="bi bi-globe me-2"></i> Visit Store
                    </a>
                </li>

                <li class="nav-item">
                    <a href="https://dashboard.stripe.com/" class="nav-link text-white">
                        <i class="bi bi-box-arrow-left me-2"></i> Stripe
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="../logout.php" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-left me-2"></i> Logout
                    </a>
                </li>
        
                
                <?php elseif ($is_vendor): ?>
                <li class="nav-item mb-2">
                    <a href="products.php" class="nav-link text-white <?= ($current_page == 'products.php') ? 'active bg-primary' : ''; ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Inventory
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="orders.php" class="nav-link text-white <?= ($current_page == 'orders.php') ? 'active bg-primary' : ''; ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Orders
                    </a>
                </li>
                
                <li class="nav-item mb-2 border-top pt-3">
                    <a href="../index.php" class="nav-link text-white">
                        <i class="bi bi-globe me-2"></i> Visit Store
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="../logout.php" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-left me-2"></i> Logout
                    </a>
                </li>
                <?php endif; ?>
                
            </ul>
        </div>
    </nav>

    <main class="flex-grow-1 p-4" style="background-color: #f8f9fa; min-height: 100vh;">
<?php endif; ?>