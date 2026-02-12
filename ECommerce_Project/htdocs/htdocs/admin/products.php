<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
require_once __DIR__ . '/admin_auth.php';

// 1. Handle Delete Request
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    // Fetch image filename to delete file from server
    $img_query = mysqli_query($conn, "SELECT image FROM products WHERE id = $delete_id");
    $img_data = mysqli_fetch_assoc($img_query);
    
    if ($img_data) {
        $file_path = "../Assets/upload/" . $img_data['image'];
        if (file_exists($file_path)) { unlink($file_path); }
        
        mysqli_query($conn, "DELETE FROM products WHERE id = $delete_id");
        header("Location: products.php?msg=deleted");
        exit();
    }
}

// 2. Fetch products JOINING Categories and Vendors
$query = "SELECT p.*, c.name as category_name, v.name as vendor_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN vendors v ON p.vendor_id = v.id 
          ORDER BY p.id DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container-fluid mt-5 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Inventory Management</h2>
        <a href="add_product.php" class="btn btn-primary">+ Add New Product</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success">Product removed successfully!</div>
        <?php elseif ($_GET['msg'] == 'updated'): ?>
            <div class="alert alert-success">Product updated successfully!</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Product Details</th>
                        <th>Category</th>
                        <th>Vendor</th> 
                        <th>Price</th>
                        <th>Stock</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td>
                                <img src="../Assets/upload/<?= $row['image']; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover;" class="rounded border">
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($row['name']); ?></div>
                                <small class="text-muted"><?= substr(htmlspecialchars($row['description']), 0, 40); ?>...</small>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['category_name'] ?? 'N/A'); ?></span></td>
                            
                            <td><span class="text-secondary"><?= htmlspecialchars($row['vendor_name'] ?? 'In-House'); ?></span></td>
                            
                            <td class="fw-bold text-primary">$<?= number_format($row['price'], 2); ?></td>
                            <td>
                                <?php if($row['stock'] <= 5): ?>
                                    <span class="badge bg-danger">Low: <?= $row['stock']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $row['stock']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end px-4">
                                <div class="btn-group">
                                    <a href="../product_details.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-sm btn-outline-info">View</a>
                                    
                                    <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    
                                    <a href="products.php?delete_id=<?= $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Delete this product?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>