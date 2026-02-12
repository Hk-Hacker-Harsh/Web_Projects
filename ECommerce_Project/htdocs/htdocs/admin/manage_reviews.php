<?php
require_once __DIR__ . '/admin_auth.php'; // Gatekeeper
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';

// 1. Handle Delete Request
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $query = "DELETE FROM reviews WHERE id = $delete_id";
    
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert alert-success'>Review deleted successfully.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error deleting review.</div>";
    }
}

// 2. Fetch All Reviews with Product and User Details
$query = "SELECT r.*, u.name as user_name, u.email as user_email, p.name as product_name 
          FROM reviews r
          JOIN users u ON r.user_id = u.id
          JOIN products p ON r.product_id = p.id
          ORDER BY r.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<div class="container-fluid mt-5 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Customer Reviews Management</h2>
        <span class="badge bg-dark"><?= mysqli_num_rows($result); ?> Total Reviews</span>
    </div>

    <?php if (isset($message)) echo $message; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td>
                                    <div class="fw-bold text-primary"><?= htmlspecialchars($row['product_name']); ?></div>
                                    <small class="text-muted">ID: #<?= $row['product_id']; ?></small>
                                </td>
                                <td>
                                    <div><?= htmlspecialchars($row['user_name']); ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($row['user_email']); ?></small>
                                </td>
                                <td>
                                    <div class="text-warning">
                                        <?= str_repeat('★', $row['rating']); ?><?= str_repeat('☆', 5 - $row['rating']); ?>
                                    </div>
                                </td>
                                <td style="max-width: 300px;">
                                    <div class="text-truncate" title="<?= htmlspecialchars($row['comment']); ?>">
                                        <?= htmlspecialchars($row['comment']); ?>
                                    </div>
                                </td>
                                <td><?= date('d M Y', strtotime($row['created_at'])); ?></td>
                                <td class="text-end px-4">
                                    <a href="manage_reviews.php?delete_id=<?= $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this review? This action cannot be undone.')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No reviews found in the database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>