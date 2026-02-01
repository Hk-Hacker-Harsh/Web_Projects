<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';

// Authentication Check
if (!isset($_SESSION['vendor_logged_in'])) {
    header("Location: login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id']; // Captured during shared login
$message = "";

// 1. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Handle Image Upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../Assets/upload/";
        $image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // Insert into DB with the session's vendor_id
            $query = "INSERT INTO products (name, price, stock, category_id, vendor_id, description, image) 
                      VALUES ('$name', '$price', '$stock', '$category_id', '$vendor_id', '$description', '$image_name')";
            
            if (mysqli_query($conn, $query)) {
                $message = "<div class='alert alert-success'>Product added successfully to " . htmlspecialchars($_SESSION['vendor_name']) . "!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Database Error: " . mysqli_error($conn) . "</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Failed to upload image.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Please select a product image.</div>";
    }
}

// 2. Fetch Categories for the dropdown
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Add New Product</h4>
                </div>
                <div class="card-body p-4">
                    <?= $message; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Product Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Price ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Stock Quantity</label>
                                <input type="number" name="stock" class="form-control" placeholder="e.g. 50" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Product Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe your product..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Product Image</label>
                            <input type="file" name="product_image" class="form-control" accept="image/*" required>
                            <small class="text-muted">Recommended size: 800x800px. Max size: 2MB.</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="products.php" class="btn btn-outline-secondary px-4">Back to List</a>
                            <button type="submit" name="submit_product" class="btn btn-success px-5">Publish Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>