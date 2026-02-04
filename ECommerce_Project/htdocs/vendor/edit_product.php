<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';

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

// Fetch current variations for this product
$vars_res = mysqli_query($conn, "SELECT * FROM product_variations WHERE product_id = $product_id");
$existing_vars = mysqli_fetch_all($vars_res, MYSQLI_ASSOC);

// 2. Handle Update Logic
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image_name = $product['image']; 

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../Assets/upload/";
        $new_image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $new_image_name;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            if (file_exists($target_dir . $product['image'])) {
                unlink($target_dir . $product['image']);
            }
            $image_name = $new_image_name;
        }
    }

    $update_query = "UPDATE products SET name = '$name', price = '$price', stock = '$stock', 
                     category_id = '$category_id', description = '$description', image = '$image_name' 
                     WHERE id = $product_id AND vendor_id = $vendor_id";

    if (mysqli_query($conn, $update_query)) {
        
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

$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Vendor: Edit Product</h4>
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
                                <label class="form-label fw-bold">Base Price ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Total Global Stock</label>
                                <input type="number" name="stock" class="form-control" value="<?= $product['stock']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="mb-4 p-3 border rounded bg-light">
                            <label class="form-label fw-bold">Product Image</label>
                            <div class="d-flex align-items-center gap-3">
                                <img src="../Assets/upload/<?= $product['image']; ?>" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                                <input type="file" name="product_image" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3 fw-bold">Product Variations</h5>
                        <div id="variation-container">
                            <?php if(!empty($existing_vars)): ?>
                                <?php foreach($existing_vars as $v): ?>
                                    <div class="row variation-row mb-2">
                                        <div class="col-md-3"><input type="text" name="v_name[]" class="form-control" value="<?= htmlspecialchars($v['variation_name']); ?>" placeholder="e.g. Color"></div>
                                        <div class="col-md-3"><input type="text" name="v_value[]" class="form-control" value="<?= htmlspecialchars($v['variation_value']); ?>" placeholder="e.g. Red"></div>
                                        <div class="col-md-2"><input type="number" step="0.01" name="v_price[]" class="form-control" value="<?= $v['price_modifier']; ?>"></div>
                                        <div class="col-md-2"><input type="number" name="v_stock[]" class="form-control" value="<?= $v['stock_qty']; ?>"></div>
                                        <div class="col-md-2"><button type="button" class="btn btn-danger remove-var w-100">×</button></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="row variation-row mb-2">
                                    <div class="col-md-3"><input type="text" name="v_name[]" class="form-control" placeholder="Type"></div>
                                    <div class="col-md-3"><input type="text" name="v_value[]" class="form-control" placeholder="Value"></div>
                                    <div class="col-md-2"><input type="number" step="0.01" name="v_price[]" class="form-control" value="0.00"></div>
                                    <div class="col-md-2"><input type="number" name="v_stock[]" class="form-control" value="0"></div>
                                    <div class="col-md-2"><button type="button" class="btn btn-danger remove-var w-100">×</button></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-variation" class="btn btn-sm btn-outline-dark mb-4">+ Add Variation</button>

                        <div class="d-flex gap-2 border-top pt-3">
                            <button type="submit" name="update_product" class="btn btn-primary px-5">Save Product</button>
                            <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
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
    let newRow = rows[0].cloneNode(true);
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