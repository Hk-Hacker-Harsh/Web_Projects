<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/header.php';

$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Fetch products from database
if ($searchTerm !== '') {
    // Search in both name and description
    $query = "SELECT * FROM products WHERE name LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%'";
} else {
    $query = "SELECT * FROM products";
}
$result = mysqli_query($conn, $query);

// 1. Get category ID from URL, default to 0 if none
$cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// 2. Build the query based on what the user clicked
if ($cat_id > 0) {
    // Filter by Category
    $query = "SELECT * FROM products WHERE category_id = $cat_id";
} elseif ($search != '') {
    // Filter by Search Bar
    $query = "SELECT * FROM products WHERE name LIKE '%$search%'";
} else {
    // Show everything
    $query = "SELECT * FROM products";
}

$result = mysqli_query($conn, $query);

?>
<style>
    .ourprotitle{
        font-weight: 700;
        text-decoration: underline;
    }
</style>


<div class="container mt-5">
    <h2 class="text-center mb-4 ourprotitle">Our Products</h2>
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="Assets/upload/<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo $row['name']; ?></h5>
                        <p class="card-text text-muted"><?php echo substr($row['description'], 0, 80); ?>...</p>
                        
                        <div class="mt-auto">
                            <h4 class="text-primary">$<?php echo number_format($row['price'], 2); ?></h4>
                            
                            <a href="add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn btn-success w-40">
                                <i class="bi bi-cart-plus"></i> Add To Cart
                            </a>
                            <a href="product_details.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary w-40">
                                <i class="bi bi-cart-plus"></i> View Details
                            </a>
                            
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>