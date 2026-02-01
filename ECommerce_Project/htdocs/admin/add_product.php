<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/admin_auth.php';

// Fetch Categories and Vendors for the dropdowns
$categories = mysqli_query($conn, "SELECT * FROM categories");
$vendors = mysqli_query($conn, "SELECT * FROM vendors");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $vendor_id = mysqli_real_escape_string($conn, $_POST['vendor_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        
        $target_dir = "../Assets/upload/";
        $image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
    
            $query = "INSERT INTO products (name, price, stock, category_id, vendor_id, description, image) VALUES ('$name', '$price', '$stock', '$category_id', '$vendor_id', '$description', '$image_name')";
            mysqli_query($conn, $query);
        }
	}
}
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Add New Product - HK Store</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Price ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" name="stock" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?= $cat['id']; ?>"><?= $cat['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-select" required>
                            <option value="">Select Vendor</option>
                            <?php while($ven = mysqli_fetch_assoc($vendors)): ?>
                                <option value="<?= $ven['id']; ?>"><?= $ven['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="product_image" class="form-control" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-success px-5">Add Product</button>
            </form>
        </div>
    </div>
</div>
<hr>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>