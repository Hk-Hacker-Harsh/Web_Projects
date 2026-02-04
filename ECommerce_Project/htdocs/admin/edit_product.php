<?php
// 1. SYSTEM LOGIC: MUST BE AT THE VERY TOP
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/admin_auth.php'; 

$message = "";
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. DATA RETRIEVAL (Product & Existing Variations)
$query = "SELECT * FROM products WHERE id = $product_id LIMIT 1";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: products.php"); 
    exit();
}

// Fetch current variations for this product
$existing_vars_res = mysqli_query($conn, "SELECT * FROM product_variations WHERE product_id = $product_id");
$existing_vars = mysqli_fetch_all($existing_vars_res, MYSQLI_ASSOC);

// 3. POST PROCESSING (Update Logic)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    
    // Logic: If nothing selected, it defaults to 0 (In-House)
    $vendor_id = isset($_POST['vendor_id']) ? (int)$_POST['vendor_id'] : 0;
    
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image_name = $product['image']; 

    // Handle Image Upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../Assets/upload/";
        $new_image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $new_image_name;
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            if (file_exists($target_dir . $product['image'])) { unlink($target_dir . $product['image']); }
            $image_name = $new_image_name;
        }
    }

    $update_sql = "UPDATE products SET name = '$name', price = '$price', stock = '$stock', 
                   category_id = '$category_id', vendor_id = '$vendor_id', 
                   description = '$description', image = '$image_name' WHERE id = $product_id";

    if (mysqli_query($conn, $update_sql)) {
        
        // --- SYNC VARIATIONS ---
        mysqli_query($conn, "DELETE FROM product_variations WHERE product_id = $product_id");

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
                    mysqli_query($conn, "INSERT INTO product_variations (product_id, variation_name, variation_value, price_modifier, stock_qty) 
                                         VALUES ($product_id, '$vn', '$vv', '$vp', '$vs')");
                }
            }
        }

        header("Location: products.php?msg=updated");
        exit();
    } else {
        $message = "<div class='alert alert-danger'>Update failed: " . mysqli_error($conn) . "</div>";
    }
}

// 4. DISPLAY LOGIC
require_once __DIR__ . '/../includes/header.php';
$categories = mysqli_query($conn, "SELECT * FROM categories");
$vendors_query = mysqli_query($conn, "SELECT id, name FROM vendors");
require_once __DIR__ . '/../includes/dashboard_nav.php';
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0">Edit Product: <?= htmlspecialchars($product['name']); ?></h4>
                </div>
                <div class="card-body p-4">
                    <?= $message; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Product Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Assign Vendor</label>
                                <select name="vendor_id" class="form-select">
                                    <option value="0" <?= ($product['vendor_id'] == 0) ? 'selected' : ''; ?>>In-House / Admin</option>
                                    <?php while($v = mysqli_fetch_assoc($vendors_query)): ?>
                                        <option value="<?= $v['id']; ?>" <?= ($v['id'] == $product['vendor_id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($v['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?= $cat['id']; ?>" <?= ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Price ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Total Stock</label>
                                <input type="number" name="stock" class="form-control" value="<?= $product['stock']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="mb-4 p-3 border rounded bg-light">
                            <label class="form-label fw-bold d-block">Product Image</label>
                            <div class="d-flex align-items-center gap-3">
                                <img src="../Assets/upload/<?= $product['image']; ?>" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                                <input type="file" name="product_image" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3 fw-bold">Manage Variations</h5>
                        <div id="variation-container">
                            <?php if(!empty($existing_vars)): ?>
                                <?php foreach($existing_vars as $v): ?>
                                    <div class="row variation-row mb-2">
                                        <div class="col-md-3"><input type="text" name="v_name[]" class="form-control" value="<?= $v['variation_name']; ?>" placeholder="Type"></div>
                                        <div class="col-md-3"><input type="text" name="v_value[]" class="form-control" value="<?= $v['variation_value']; ?>" placeholder="Value"></div>
                                        <div class="col-md-2"><input type="number" step="0.01" name="v_price[]" class="form-control" value="<?= $v['price_modifier']; ?>"></div>
                                        <div class="col-md-2"><input type="number" name="v_stock[]" class="form-control" value="<?= $v['stock_qty']; ?>"></div>
                                        <div class="col-md-2"><button type="button" class="btn btn-danger remove-var w-100">×</button></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="row variation-row mb-2">
                                    <div class="col-md-3"><input type="text" name="v_name[]" class="form-control" placeholder="Type (Size)"></div>
                                    <div class="col-md-3"><input type="text" name="v_value[]" class="form-control" placeholder="Value (XL)"></div>
                                    <div class="col-md-2"><input type="number" step="0.01" name="v_price[]" class="form-control" value="0.00"></div>
                                    <div class="col-md-2"><input type="number" name="v_stock[]" class="form-control" value="0"></div>
                                    <div class="col-md-2"><button type="button" class="btn btn-danger remove-var w-100">×</button></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-variation" class="btn btn-sm btn-outline-primary mb-4">+ Add Variation</button>

                        <div class="d-flex justify-content-between border-top pt-4">
                            <a href="products.php" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" name="update_product" class="btn btn-primary px-5 fw-bold">Save All Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('add-variation').addEventListener('click', function() {
    let container = document.getElementById('variation-container');
    let rows = document.querySelectorAll('.variation-row');
    let firstRow = rows[0];
    let newRow = firstRow.cloneNode(true);
    newRow.querySelectorAll('input').forEach(i => i.value = (i.type === 'number' ? '0' : ''));
    container.appendChild(newRow);
});

document.getElementById('variation-container').addEventListener('click', function(e) {
    if(e.target.classList.contains('remove-var')) {
        let rows = document.querySelectorAll('.variation-row');
        if(rows.length > 1) e.target.closest('.variation-row').remove();
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>