<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';

// Authentication Check
if (!isset($_SESSION['vendor_logged_in'])) {
    header("Location: login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. Fetch Product Data with Ownership Check
$query = "SELECT * FROM products WHERE id = $product_id AND vendor_id = $vendor_id LIMIT 1";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Product not found or access denied.</div></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit();
}

// 2. Handle Update Logic
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image_name = $product['image']; // Keep old image by default

    // Handle Image Upload if a new file is provided
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../Assets/upload/";
        $new_image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $new_image_name;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // Delete the old image file to save space
            if (file_exists($target_dir . $product['image'])) {
                unlink($target_dir . $product['image']);
            }
            $image_name = $new_image_name;
        }
    }

    $update_query = "UPDATE products SET 
                        name = '$name', 
                        price = '$price', 
                        stock = '$stock', 
                        category_id = '$category_id', 
                        description = '$description', 
                        image = '$image_name' 
                     WHERE id = $product_id AND vendor_id = $vendor_id";

    if (mysqli_query($conn, $update_query)) {
        header("Location: products.php?msg=updated");
        exit();
    } else {
        $message = "<div class='alert alert-danger'>Update failed: " . mysqli_error($conn) . "</div>";
    }
}

// Fetch categories for the dropdown
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Edit Product: <?= htmlspecialchars($product['name']); ?></h4>
                </div>
                <div class="card-body p-4">
                    <?= $message; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Product Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?= $cat['id']; ?>" <?= ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Price ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Stock Quantity</label>
                                <input type="number" name="stock" class="form-control" value="<?= $product['stock']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Product Image</label>
                            <div class="mb-2">
                                <img src="../Assets/upload/<?= $product['image']; ?>" class="rounded border" style="width: 100px; height: 100px; object-fit: cover;">
                                <small class="text-muted d-block mt-1">Current Image</small>
                            </div>
                            <input type="file" name="product_image" class="form-control" accept="image/*">
                            <small class="text-info italic">Leave blank to keep the current image.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="update_product" class="btn btn-primary px-5">Save Changes</button>
                            <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>