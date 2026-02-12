<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';

// Authentication Check
if (!isset($_SESSION['vendor_logged_in'])) {
    header("Location: login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

// 1. Handle Delete Request (Security: Check ownership before deleting)
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    // Security check: Does this product actually belong to this vendor?
    $check_query = "SELECT image FROM products WHERE id = $delete_id AND vendor_id = $vendor_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if ($img_data = mysqli_fetch_assoc($check_result)) {
        $file_path = "../Assets/upload/" . $img_data['image'];
        if (file_exists($file_path)) { unlink($file_path); }
        
        mysqli_query($conn, "DELETE FROM products WHERE id = $delete_id");
        header("Location: products.php?msg=deleted");
        exit();
    } else {
        $error = "Unauthorized action or product not found.";
    }
}

// 2. Fetch only THIS vendor's products
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.vendor_id = $vendor_id 
          ORDER BY p.id DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>My Inventory</h2>
            <p class="text-muted">Managing products for: <strong><?= htmlspecialchars($_SESSION['vendor_name']); ?></strong></p>
        </div>
        <a href="add_product.php" class="btn btn-success">+ Add New Product</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <img src="../Assets/upload/<?= $row['image']; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover;" class="rounded border">
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['name']); ?></div>
                                    <small class="text-muted">ID: #<?= $row['id']; ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></span></td>
                                <td class="fw-bold text-primary">$<?= number_format($row['price'], 2); ?></td>
                                <td>
                                    <?php if($row['stock'] <= 5): ?>
                                        <span class="badge bg-danger">Low Stock: <?= $row['stock']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $row['stock']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end px-4">
                                    <div class="btn-group">
                                        <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="products.php?delete_id=<?= $row['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Delete this product permanently?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No products found in your inventory.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>