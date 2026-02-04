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
            // Insert into products table
            $query = "INSERT INTO products (name, price, stock, category_id, vendor_id, description, image) 
                      VALUES ('$name', '$price', '$stock', '$category_id', '$vendor_id', '$description', '$image_name')";
            
            if (mysqli_query($conn, $query)) {
                $new_product_id = mysqli_insert_id($conn);

                // --- NEW: Handle Variations ---
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
                                        VALUES ($new_product_id, '$vn', '$vv', '$vp', '$vs')";
                            mysqli_query($conn, $v_query);
                        }
                    }
                }

                $message = "<div class='alert alert-success'>Product and variations added successfully!</div>";
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
        <div class="col-md-10">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Vendor Panel: Add New Product</h4>
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
                                <label class="form-label fw-bold">Base Price ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Total Stock</label>
                                <input type="number" name="stock" class="form-control" placeholder="e.g. 50" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Product Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Describe your product..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Product Image</label>
                            <input type="file" name="product_image" class="form-control" accept="image/*" required>
                        </div>

                        <hr>
                        <h5 class="mb-3 fw-bold">Add Product Variations</h5>
                        <div id="variation-container">
                            <div class="row variation-row mb-3 p-2 bg-light rounded border">
                                <div class="col-md-3">
                                    <label class="small fw-bold">Variation Type</label>
                                    <input type="text" name="v_name[]" class="form-control" placeholder="Size / Color">
                                </div>
                                <div class="col-md-3">
                                    <label class="small fw-bold">Value</label>
                                    <input type="text" name="v_value[]" class="form-control" placeholder="XL / Red">
                                </div>
                                <div class="col-md-2">
                                    <label class="small fw-bold">Price +</label>
                                    <input type="number" step="0.01" name="v_price[]" class="form-control" value="0.00">
                                </div>
                                <div class="col-md-2">
                                    <label class="small fw-bold">Stock</label>
                                    <input type="number" name="v_stock[]" class="form-control" value="0">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger remove-var w-100">×</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-variation" class="btn btn-sm btn-outline-primary mb-4">+ Add Another Variation</button>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <a href="products.php" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" name="submit_product" class="btn btn-success px-5">Publish Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dynamic variation rows logic
document.getElementById('add-variation').addEventListener('click', function() {
    let container = document.getElementById('variation-container');
    let firstRow = document.querySelector('.variation-row');
    let newRow = firstRow.cloneNode(true);
    
    // Reset values in the new row
    newRow.querySelectorAll('input').forEach(input => {
        if(input.type === 'number') {
            input.value = "0.00";
        } else {
            input.value = "";
        }
    });
    
    container.appendChild(newRow);
});

// Event delegation for removing rows
document.getElementById('variation-container').addEventListener('click', function(e) {
    if(e.target.classList.contains('remove-var')) {
        let rows = document.querySelectorAll('.variation-row');
        if(rows.length > 1) {
            e.target.closest('.variation-row').remove();
        } else {
            alert("Provide at least one variation row or leave blank if none.");
        }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>