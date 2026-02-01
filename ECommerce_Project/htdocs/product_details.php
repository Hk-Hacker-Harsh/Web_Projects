<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/db.php';

// 1. Get the ID and make it safe
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id > 0) {
    // 2. Fetch only this product
    $query = "SELECT p.*, c.name as category_name, v.name as vendor_name 
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              LEFT JOIN vendors v ON p.vendor_id = v.id
              WHERE p.id = $product_id LIMIT 1";
              
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    } else {
        echo "<div class='container mt-5'><p class='alert alert-danger'>Product not found.</p></div>";
        include 'includes/footer.php';
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <img src="Assets/upload/<?php echo $product['image']; ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
        </div>

        <div class="col-md-6">
            <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="badge bg-info text-dark mb-3"><?php echo htmlspecialchars($product['category_name']); ?></p>
            
            <h3 class="text-primary mb-4">$<?php echo number_format($product['price'], 2); ?></h3>
            
            <div class="mb-4">
                <h5>Description</h5>
                <p class="text-muted" style="line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>
            </div>

            <ul class="list-unstyled mb-4">
                <li><strong>Availability:</strong> <?php echo ($product['stock'] > 0) ? '<span class="text-success">In Stock ('.$product['stock'].')</span>' : '<span class="text-danger">Out of Stock</span>'; ?></li>
                <li><strong>Vendor:</strong> <?php echo htmlspecialchars($product['vendor_name']); ?></li>
            </ul>

            <div class="d-grid gap-2 d-md-flex">
                <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-lg px-5">
                    <i class="bi bi-cart-plus"></i> Add To Cart
                </a>
                <a href="product.php" class="btn btn-outline-secondary btn-lg">Back to Shop</a>
            </div>
        </div>
        <?php
// Handle Review Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please login to leave a review');</script>";
    } else {
        $product_id = (int)$_GET['id'];
        $user_id = $_SESSION['user_id'];
        $rating = (int)$_POST['rating'];
        $comment = mysqli_real_escape_string($conn, $_POST['comment']);

        $query = "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES ($product_id, $user_id, $rating, '$comment')";
        mysqli_query($conn, $query);
        echo "<script>window.location.href='product_details.php?id=$product_id&msg=review_added';</script>";
    }
}

// Fetch Reviews for this product
$p_id = (int)$_GET['id'];
$reviews_query = "SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = $p_id ORDER BY r.created_at DESC";
$reviews_result = mysqli_query($conn, $reviews_query);

// Calculate Average Rating
$avg_query = "SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews FROM reviews WHERE product_id = $p_id";
$avg_res = mysqli_fetch_assoc(mysqli_query($conn, $avg_query));
$avg_rating = round($avg_res['avg_rating'], 1);
?>

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
                        <textarea name="comment" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="btn btn-primary w-100">Submit Review</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Customer Reviews</h4>
            <div class="text-warning fw-bold">
                ★ <?= $avg_rating ?: '0'; ?> / 5 (<?= $avg_res['total_reviews']; ?> Reviews)
            </div>
        </div>

        <?php if (mysqli_num_rows($reviews_result) > 0): ?>
            <?php while($rev = mysqli_fetch_assoc($reviews_result)): ?>
                <div class="card mb-3 border-0 border-bottom">
                    <div class="card-body px-0">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($rev['name']); ?></h6>
                            <span class="text-muted small"><?= date('M d, Y', strtotime($rev['created_at'])); ?></span>
                        </div>
                        <div class="text-warning mb-2">
                            <?= str_repeat('★', $rev['rating']); ?><?= str_repeat('☆', 5 - $rev['rating']); ?>
                        </div>
                        <p class="text-secondary"><?= nl2br(htmlspecialchars($rev['comment'])); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted italic">No reviews yet. Be the first to review this product!</p>
        <?php endif; ?>
    </div>
</div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>