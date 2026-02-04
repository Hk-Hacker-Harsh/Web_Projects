<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
require_once __DIR__ . '/admin_auth.php';
$message = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $cat_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    
    if (!empty($cat_name)) {
        $query = "INSERT INTO categories (name) VALUES ('$cat_name')";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert alert-success'>Category '$cat_name' added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Fetch categories to display in a list
$all_categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY created_at DESC");
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Add New Category</h4>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="category_name" class="form-control" placeholder="e.g. Electronics, Fashion" required>
                        </div>
                        <button type="submit" name="add_category" class="btn btn-success w-100">Save Category</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Existing Categories</h4>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($all_categories) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($all_categories)): ?>
                                    <tr>
                                        <td><?= $row['id']; ?></td>
                                        <td><strong><?= htmlspecialchars($row['name']); ?></strong></td>
                                        <td><?= date('d M Y', strtotime($row['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No categories found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>