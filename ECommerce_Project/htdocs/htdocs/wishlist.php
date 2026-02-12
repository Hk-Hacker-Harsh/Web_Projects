<?php
// 1. SYSTEM LOGIC
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session.php';

// 2. AUTHENTICATION CHECK
protect_page(); 

$user_id = $_SESSION['user_id'];

// 3. DATA FETCHING
// Fetch all products saved by the user
$query = "SELECT p.*, c.name as category_name 
          FROM wishlist w 
          JOIN products p ON w.product_id = p.id 
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE w.user_id = $user_id 
          ORDER BY w.created_at DESC";
$result = mysqli_query($conn, $query);

// 4. DISPLAY LOGIC
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="container mt-5 mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="profile.php">Profile</a></li>
            <li class="breadcrumb-item active">Wishlist</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-heart-fill text-danger me-2"></i>My Wishlist</h2>
        <span class="badge bg-secondary rounded-pill"><?php echo mysqli_num_rows($result); ?> Items</span>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="row g-4">
            <?php while ($product = mysqli_fetch_assoc($result)): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm border-0 position-relative">
                        
                        <form action="toggle_wishlist.php" method="POST" class="position-absolute top-0 end-0 p-2" style="z-index: 5;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-white btn-sm rounded-circle shadow-sm" title="Remove from wishlist">
                                <i class="bi bi-x-lg text-danger"></i>
                            </button>
                        </form>

                        <a href="product_details.php?id=<?php echo $product['id']; ?>">
                            <img src="Assets/upload/<?php echo $product['image']; ?>" class="card-img-top" alt="..." style="height: 200px; object-fit: cover;">
                        </a>

                        <div class="card-body d-flex flex-column">
                            <p class="text-muted small mb-1"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <h6 class="card-title fw-bold text-truncate">
                                <a href="product_details.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h6>
                            <h5 class="text-primary fw-bold mb-3">$<?php echo number_format($product['price'], 2); ?></h5>
                            
                            <div class="mt-auto d-grid gap-2">
                                <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-sm">
                                    <i class="bi bi-cart-plus me-1"></i> Add to Cart
                                </a>
                                <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-dark btn-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 shadow-sm bg-white rounded">
            <div class="mb-4">
                <i class="bi bi-heart text-light" style="font-size: 5rem;"></i>
            </div>
            <h3 class="fw-bold">Your wishlist is empty!</h3>
            <p class="text-muted mb-4">Explore our products and save your favorites here.</p>
            <a href="product.php" class="btn btn-primary btn-lg px-5">Go to Shop</a>
        </div>
    <?php endif; ?>
</div>

<style>
    .btn-white {
        background-color: white;
        border: 1px solid #eee;
    }
    .btn-white:hover {
        background-color: #f8f9fa;
    }
    .card {
        transition: transform 0.2s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>