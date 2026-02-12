<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
require_once __DIR__ . '/admin_auth.php';

// 1. Handle Delete Request
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    // Safety: Check if products exist in this category before deleting
    $check_products = mysqli_query($conn, "SELECT id FROM products WHERE category_id = $delete_id");
    
    if (mysqli_num_rows($check_products) > 0) {
        $error = "Cannot delete category! There are products linked to it.";
    } else {
        mysqli_query($conn, "DELETE FROM categories WHERE id = $delete_id");
        header("Location: categories.php?msg=deleted");
        exit();
    }
}

// 2. Fetch Categories with Item Count
// We use LEFT JOIN so categories with 0 items still show up
$query = "SELECT c.id, c.name, COUNT(p.id) as total_items 
          FROM categories c 
          LEFT JOIN products p ON c.id = p.category_id 
          GROUP BY c.id 
          ORDER BY c.name ASC";

$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Categories</h2>
        <a href="add_category.php" class="btn btn-primary">+ Add New Category</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success">Category deleted successfully!</div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th class="text-center">Items Count</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><strong><?= htmlspecialchars($row['name']); ?></strong></td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-info text-dark">
                                    <?= $row['total_items']; ?> Items
                                </span>
                            </td>
                            <td class="text-end px-4">
                                <a href="categories.php?delete_id=<?= $row['id']; ?>" 
                                   class="btn btn-outline-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this category?')">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>