<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session.php'; // Ensure session is loaded

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($product_id > 0) {
    // 1. Fetch Product Details
    $query = "SELECT p.*, c.name as category_name, v.name as vendor_name 
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              LEFT JOIN vendors v ON p.vendor_id = v.id
              WHERE p.id = $product_id LIMIT 1";
              
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        
        // 2. Fetch Variations
        $v_query = "SELECT * FROM product_variations WHERE product_id = $product_id";
        $v_result = mysqli_query($conn, $v_query);
        $variations = [];
        while($row = mysqli_fetch_assoc($v_result)) {
            $variations[$row['variation_name']][] = $row;
        }

        // 3. Check if Product is in User's Wishlist
        $is_wishlisted = false;
        if ($user_id) {
            $w_check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
            if (mysqli_num_rows($w_check) > 0) $is_wishlisted = true;
        }

    } else {
        echo "<div class='container mt-5'><p class='alert alert-danger'>Product not found.</p></div>";
        include 'includes/footer.php';
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// 4. Handle Review Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
    if (!$user_id) {
        echo "<script>alert('Please login to leave a review');</script>";
    } else {
        $rating = (int)$_POST['rating'];
        $comment = mysqli_real_escape_string($conn, $_POST['comment']);
        $q = "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES ($product_id, $user_id, $rating, '$comment')";
        mysqli_query($conn, $q);
        echo "<script>window.location.href='product_details.php?id=$product_id&msg=review_added';</script>";
    }
}
?>

<div class="container mt-5">
    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <img src="Assets/upload/<?php echo $product['image']; ?>" class="img-fluid rounded" alt="...">
            </div>
        </div>

        <div class="col-md-6">
            <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="badge bg-info text-dark mb-3"><?php echo htmlspecialchars($product['category_name']); ?></p>
            
            <h3 class="text-primary mb-4" id="base-price" data-price="<?php echo $product['price']; ?>">
                $<?php echo number_format($product['price'], 2); ?>
            </h3>
            
            <div class="mb-4">
                <h5>Description</h5>
                <p class="text-muted"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>

            <form action="add_to_cart.php" method="GET">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                
                <?php if (!empty($variations)): ?>
                    <div class="variation-selectors mb-4">
                        <?php foreach ($variations as $name => $options): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select <?php echo htmlspecialchars($name); ?></label>
                                <select name="variation[]" class="form-select variation-select" required>
                                    <option value="" data-modifier="0">Choose an option...</option>
                                    <?php foreach ($options as $opt): ?>
                                        <option value="<?php echo $opt['id']; ?>" data-modifier="<?php echo $opt['price_modifier']; ?>">
                                            <?php echo htmlspecialchars($opt['variation_value']); ?> 
                                            (+ $<?php echo $opt['price_modifier']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <ul class="list-unstyled mb-4">
                    <li><strong>Availability:</strong> <?php echo ($product['stock'] > 0) ? '<span class="text-success">In Stock</span>' : '<span class="text-danger">Out of Stock</span>'; ?></li>
                    <li><strong>Vendor:</strong> <?php echo htmlspecialchars($product['vendor_name']); ?></li>
                </ul>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-lg px-4 flex-grow-1">
                        <i class="bi bi-cart-plus"></i> Add To Cart
                    </button>
                    
                    <button type="button" onclick="toggleWishlist(<?= $product_id; ?>)" class="btn <?= $is_wishlisted ? 'btn-danger' : 'btn-outline-danger'; ?> btn-lg px-4">
                        <i class="bi <?= $is_wishlisted ? 'bi-heart-fill' : 'bi-heart'; ?>"></i> 
                        <?= $is_wishlisted ? 'Saved' : 'Wishlist'; ?>
                    </button>

                    <a href="product.php" class="btn btn-outline-secondary btn-lg px-3">Back</a>
                </div>
            </form>
        </div>
    </div>

    <hr class="my-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Leave a Review</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <select name="rating" class="form-select" required>
                                <option value="5">5 - Excellent</option>
                                <option value="4">4 - Very Good</option>
                                <option value="3">3 - Good</option>
                                <option value="2">2 - Fair</option>
                                <option value="1">1 - Poor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Your Comment</label>
                            <textarea name="comment" class="form-control" rows="3" placeholder="Share your experience..." required></textarea>
                        </div>
                        <button type="submit" name="submit_review" class="btn btn-primary w-100">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h4>Customer Reviews</h4>
            <?php
            $rev_query = "SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = $product_id ORDER BY r.created_at DESC";
            $rev_res = mysqli_query($conn, $rev_query);
            if (mysqli_num_rows($rev_res) > 0):
                while($rev = mysqli_fetch_assoc($rev_res)): ?>
                    <div class="border-bottom mb-3 pb-3">
                        <div class="d-flex justify-content-between">
                            <strong><?= htmlspecialchars($rev['name']); ?></strong>
                            <span class="text-warning"><?= str_repeat('★', $rev['rating']); ?><?= str_repeat('☆', 5-$rev['rating']); ?></span>
                        </div>
                        <p class="text-muted small mb-1"><?= date('M d, Y', strtotime($rev['created_at'])); ?></p>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($rev['comment'])); ?></p>
                    </div>
                <?php endwhile;
            else: ?>
                <p class="text-muted">No reviews yet. Be the first to review!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Wishlist Toggle Function
function toggleWishlist(prodId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'toggle_wishlist.php';
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'product_id';
    input.value = prodId;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}

// Price Modifier Function
document.querySelectorAll('.variation-select').forEach(select => {
    select.addEventListener('change', function() {
        let basePrice = parseFloat(document.getElementById('base-price').getAttribute('data-price'));
        let totalModifier = 0;
        document.querySelectorAll('.variation-select').forEach(s => {
            let selectedOption = s.options[s.selectedIndex];
            totalModifier += parseFloat(selectedOption.getAttribute('data-modifier') || 0);
        });
        document.getElementById('base-price').innerText = '$' + (basePrice + totalModifier).toFixed(2);
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>