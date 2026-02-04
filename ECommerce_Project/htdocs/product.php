<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/header.php';

$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// 1. Determine the Order By Clause
switch ($sort) {
    case 'price_low':
        $order_by = "p.price ASC";
        break;
    case 'price_high':
        $order_by = "p.price DESC";
        break;
    case 'popular':
        // Sort by total quantity sold in order_items
        $order_by = "total_sales DESC";
        break;
    case 'top_rated':
        // Sort by average rating in reviews
        $order_by = "avg_rating DESC";
        break;
    default:
        $order_by = "p.id DESC";
        break;
}

// 2. Build the query with Subqueries for Rating and Sales
$query = "SELECT p.*, 
          (SELECT COUNT(*) FROM order_items WHERE product_id = p.id) as total_sales,
          (SELECT AVG(rating) FROM reviews WHERE product_id = p.id) as avg_rating
          FROM products p";

if ($cat_id > 0) {
    $query .= " WHERE p.category_id = $cat_id";
} elseif ($searchTerm != '') {
    $query .= " WHERE p.name LIKE '%$searchTerm%' OR p.description LIKE '%$searchTerm%'";
}

$query .= " ORDER BY $order_by";
$result = mysqli_query($conn, $query);
?>

<style>
    .ourprotitle { font-weight: 700; text-decoration: underline; }
    .btn-custom-width { width: 48%; }
    .sort-select { width: auto; min-width: 200px; }
</style>

<div class="container mt-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <h2 class="ourprotitle mb-3 mb-md-0">Our Products</h2>
        
        <form method="GET" class="d-flex gap-2 align-items-center">
            <?php if($cat_id > 0): ?> <input type="hidden" name="cat_id" value="<?= $cat_id; ?>"> <?php endif; ?>
            <?php if($searchTerm != ''): ?> <input type="hidden" name="search" value="<?= $searchTerm; ?>"> <?php endif; ?>
            
            <label class="fw-bold small text-muted text-nowrap">Sort By:</label>
            <select name="sort" class="form-select form-select-sm sort-select" onchange="this.form.submit()">
                <option value="newest" <?= $sort == 'newest' ? 'selected' : ''; ?>>Newest Arrivals</option>
                <option value="price_low" <?= $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_high" <?= $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="popular" <?= $sort == 'popular' ? 'selected' : ''; ?>>Most Popular (Sales)</option>
                <option value="top_rated" <?= $sort == 'top_rated' ? 'selected' : ''; ?>>Top Rated (Reviews)</option>
            </select>
        </form>
    </div>

    <div class="row">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): 
                $p_id = $row['id'];
                $check_v = mysqli_query($conn, "SELECT id FROM product_variations WHERE product_id = $p_id LIMIT 1");
                $has_variations = mysqli_num_rows($check_v) > 0;
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="Assets/upload/<?php echo $row['image']; ?>" class="card-img-top" alt="...">
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            
                            <div class="mb-2">
                                <?php if($row['avg_rating'] > 0): ?>
                                    <span class="badge bg-warning text-dark small">
                                        <i class="bi bi-star-fill"></i> <?= number_format($row['avg_rating'], 1); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if($row['total_sales'] > 0): ?>
                                    <span class="badge bg-light text-muted border small">
                                        <?= $row['total_sales']; ?> sold
                                    </span>
                                <?php endif; ?>
                            </div>

                            <p class="card-text text-muted small"><?php echo substr($row['description'], 0, 80); ?>...</p>
                            
                            <div class="mt-auto">
                                <h4 class="text-primary mb-3">$<?php echo number_format($row['price'], 2); ?></h4>
                                
                                <div class="d-flex justify-content-between">
                                    <?php if($has_variations): ?>
                                        <a href="product_details.php?id=<?php echo $p_id; ?>" class="btn btn-warning btn-custom-width btn-sm">
                                            <i class="bi bi-list-ul"></i> Options
                                        </a>
                                    <?php else: ?>
                                        <a href="add_to_cart.php?id=<?php echo $p_id; ?>" class="btn btn-success btn-custom-width btn-sm">
                                            <i class="bi bi-cart-plus"></i> Add To Cart
                                        </a>
                                    <?php endif; ?>

                                    <a href="product_details.php?id=<?php echo $p_id; ?>" class="btn btn-secondary btn-custom-width btn-sm">
                                        <i class="bi bi-info-circle"></i> Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search display-1 text-light"></i>
                <p class="mt-3 text-muted">No products found matching your criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>