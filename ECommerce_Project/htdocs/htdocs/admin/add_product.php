<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
require_once __DIR__ . '/admin_auth.php';

$categories = mysqli_query($conn, "SELECT * FROM categories");
$vendors = mysqli_query($conn, "SELECT * FROM vendors");

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
            // 1. Insert Main Product
            $query = "INSERT INTO products (name, price, stock, category_id, vendor_id, description, image) 
                      VALUES ('$name', '$price', '$stock', '$category_id', '$vendor_id', '$description', '$image_name')";
            
            if (mysqli_query($conn, $query)) {
                $product_id = mysqli_insert_id($conn); // Get the newly created ID

                // 2. Handle Variations
                if (isset($_POST['v_name']) && is_array($_POST['v_name'])) {
                    $v_names = $_POST['v_name'];
                    $v_values = $_POST['v_value'];
                    $v_prices = $_POST['v_price'];
                    $v_stocks = $_POST['v_stock'];

                    for ($i = 0; $i < count($v_names); $i++) {
                        $vn = mysqli_real_escape_string($conn, $v_names[$i]);
                        $vv = mysqli_real_escape_string($conn, $v_values[$i]);
                        $vp = mysqli_real_escape_string($conn, $v_prices[$i]);
                        $vs = mysqli_real_escape_string($conn, $v_stocks[$i]);

                        if (!empty($vn) && !empty($vv)) {
                            $v_query = "INSERT INTO product_variations (product_id, variation_name, variation_value, price_modifier, stock_qty) 
                                        VALUES ($product_id, '$vn', '$vv', '$vp', '$vs')";
                            mysqli_query($conn, $v_query);
                        }
                    }
                }
                echo "<script>alert('Product and variations added successfully!'); window.location.href='products.php';</script>";
            }
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Add New Product - HK Store</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Base Price ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Total Stock</label>
                        <input type="number" name="stock" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php mysqli_data_seek($categories, 0); while($cat = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?= $cat['id']; ?>"><?= $cat['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Vendor</label>
                        <select name="vendor_id" class="form-select" required>
                            <option value="">Select Vendor</option>
                            <?php mysqli_data_seek($vendors, 0); while($ven = mysqli_fetch_assoc($vendors)): ?>
                                <option value="<?= $ven['id']; ?>"><?= $ven['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Product Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Product Image</label>
                    <input type="file" name="product_image" class="form-control" accept="image/*" required>
                </div>

                <hr>
                <h4 class="mb-3">Product Variations (Size, Color, etc.)</h4>
                <div id="variation-container">
                    <div class="row variation-row mb-3 border p-2 bg-light rounded">
                        <div class="col-md-3">
                            <label class="small fw-bold">Type (e.g. Size)</label>
                            <input type="text" name="v_name[]" class="form-control form-control-sm" placeholder="Size">
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">Value (e.g. XL)</label>
                            <input type="text" name="v_value[]" class="form-control form-control-sm" placeholder="XL">
                        </div>
                        <div class="col-md-2">
                            <label class="small fw-bold">Price +</label>
                            <input type="number" step="0.01" name="v_price[]" class="form-control form-control-sm" value="0.00">
                        </div>
                        <div class="col-md-2">
                            <label class="small fw-bold">Stock</label>
                            <input type="number" name="v_stock[]" class="form-control form-control-sm" value="0">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-var">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-variation" class="btn btn-outline-primary btn-sm mb-4">+ Add More Variation</button>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-5">Publish Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('add-variation').addEventListener('click', function() {
    let container = document.getElementById('variation-container');
    let newRow = document.querySelector('.variation-row').cloneNode(true);
    
    // Clear inputs in cloned row
    newRow.querySelectorAll('input').forEach(input => {
        if(input.name === "v_price[]" || input.name === "v_stock[]") {
            input.value = "0";
        } else {
            input.value = "";
        }
    });
    
    container.appendChild(newRow);
});

// Use event delegation for remove buttons
document.getElementById('variation-container').addEventListener('click', function(e) {
    if(e.target.classList.contains('remove-var')) {
        let rows = document.querySelectorAll('.variation-row');
        if(rows.length > 1) {
            e.target.closest('.variation-row').remove();
        } else {
            alert("At least one variation row is required if adding variations.");
        }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>