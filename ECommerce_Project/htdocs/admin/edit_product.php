<?php
// 1. SYSTEM LOGIC: MUST BE AT THE VERY TOP
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/admin_auth.php'; // Your gatekeeper check

$message = "";
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. DATA RETRIEVAL
$query = "SELECT * FROM products WHERE id = $product_id LIMIT 1";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

// If product doesn't exist, redirect immediately (safe because no HTML sent yet)
if (!$product) {
    header("Location: products.php"); 
    exit();
}

// 3. POST PROCESSING (Update Logic)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $vendor_id = mysqli_real_escape_string($conn, $_POST['vendor_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image_name = $product['image']; 

    // Handle Image Upload logic
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../Assets/upload/";
        $new_image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $new_image_name;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // Remove old image from server
            if (file_exists($target_dir . $product['image'])) {
                unlink($target_dir . $product['image']);
            }
            $image_name = $new_image_name;
        }
    }

    $update_sql = "UPDATE products SET 
                    name = '$name', 
                    price = '$price', 
                    stock = '$stock', 
                    category_id = '$category_id', 
                    vendor_id = '$vendor_id', 
                    description = '$description', 
                    image = '$image_name' 
                   WHERE id = $product_id";

    if (mysqli_query($conn, $update_sql)) {
        // SUCCESSFUL REDIRECT: This only works if no HTML has been echoed yet
        header("Location: products.php?msg=updated");
        exit();
    } else {
        $message = "<div class='alert alert-danger'>Update failed: " . mysqli_error($conn) . "</div>";
    }
}

// 4. DISPLAY LOGIC: ONLY NOW DO WE START OUTPUTTING HTML
require_once __DIR__ . '/../includes/header.php'; // HTML output starts at line 108

// Fetch Categories and Vendors for the dropdown menus
$categories = mysqli_query($conn, "SELECT * FROM categories");
$vendors = mysqli_query($conn, "SELECT id, name FROM vendors");
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0">Admin: Edit Product #<?= $product['id']; ?></h4>
                </div>
                <div class="card-body p-4">
                    <?= $message; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Product Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']); ?>" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?= $cat['id']; ?>" <?= ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Assigned Vendor</label>
                                <select name="vendor_id" class="form-select" required>
                                    <option value="0">In-House / Admin</option>
                                    <?php while($v = mysqli_fetch_assoc($vendors)): ?>
                                        <option value="<?= $v['id']; ?>" <?= ($v['id'] == $product['vendor_id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($v['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Price ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Stock Quantity</label>
                                <input type="number" name="stock" class="form-control" value="<?= $product['stock']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold d-block">Product Image</label>
                            <div class="d-flex align-items-start gap-3">
                                <img src="../Assets/upload/<?= $product['image']; ?>" class="rounded border shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <input type="file" name="product_image" class="form-control mb-1" accept="image/*">
                                    <small class="text-muted italic">Only upload a new file if you want to replace the image above.</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="products.php" class="btn btn-outline-secondary px-4">Back to List</a>
                            <button type="submit" name="update_product" class="btn btn-primary px-5 fw-bold">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>