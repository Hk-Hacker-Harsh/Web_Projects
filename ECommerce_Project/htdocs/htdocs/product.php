<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/header.php';

// 1. Pagination Configuration
$limit = 9; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $limit;

$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// 2. Determine Sorting Logic
switch ($sort) {
    case 'price_low':  $order_by = "p.price ASC"; break;
    case 'price_high': $order_by = "p.price DESC"; break;
    case 'popular':    $order_by = "total_sales DESC"; break;
    case 'top_rated':  $order_by = "avg_rating DESC"; break;
    default:           $order_by = "p.id DESC"; break;
}

// 3. Optimized JOIN Query (Solves N+1 Issue)
// We join products with order_items (for sales) and reviews (for ratings)
$query = "SELECT p.*, 
          COUNT(DISTINCT oi.id) as total_sales, 
          AVG(r.rating) as avg_rating
          FROM products p
          LEFT JOIN order_items oi ON p.id = oi.product_id
          LEFT JOIN reviews r ON p.id = r.product_id";

// Filtering Logic
$where_clauses = [];
if ($cat_id > 0) {
    $where_clauses[] = "p.category_id = $cat_id";
}
if ($searchTerm != '') {
    $where_clauses[] = "(p.name LIKE '%$searchTerm%' OR p.description LIKE '%$searchTerm%')";
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(' AND ', $where_clauses);
}

// Grouping is REQUIRED when using aggregate functions like COUNT/AVG with JOINs
$query .= " GROUP BY p.id";
$query .= " ORDER BY $order_by";

// 4. Handle Pagination Count
$count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM ($query) as sub");
$total_results = mysqli_fetch_assoc($count_res)['total'];
$total_pages = ceil($total_results / $limit);

// 5. Finalize Query with Limits
$query .= " LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <h2 class="fw-bold border-bottom border-3 border-primary pb-2">Our Products</h2>
        
        <form method="GET" class="d-flex gap-2 align-items-center">
            <?php if($cat_id > 0): ?> <input type="hidden" name="cat_id" value="<?= $cat_id; ?>"> <?php endif; ?>
            <?php if($searchTerm != ''): ?> <input type="hidden" name="search" value="<?= $searchTerm; ?>"> <?php endif; ?>
            
            <label class="small fw-bold text-muted">Sort By:</label>
            <select name="sort" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="newest" <?= $sort == 'newest' ? 'selected' : ''; ?>>Newest</option>
                <option value="price_low" <?= $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_high" <?= $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="popular" <?= $sort == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                <option value="top_rated" <?= $sort == 'top_rated' ? 'selected' : ''; ?>>Top Rated</option>
            </select>
        </form>
    </div>

    <div class="row">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): 
                $p_id = $row['id'];
                // Check for variations (Keep this lightweight)
                $v_res = mysqli_query($conn, "SELECT id FROM product_variations WHERE product_id = $p_id LIMIT 1");
                $has_variations = mysqli_num_rows($v_res) > 0;
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="Assets/upload/<?= $row['image']; ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']); ?>" style="object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($row['name']); ?></h5>
                            
                            <div class="mb-2">
                                <?php if($row['avg_rating'] > 0): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> <?= number_format($row['avg_rating'], 1); ?></span>
                                <?php endif; ?>
                                <?php if($row['total_sales'] > 0): ?>
                                    <span class="badge bg-light text-muted border"><?= $row['total_sales']; ?> sold</span>
                                <?php endif; ?>
                            </div>

                            <p class="card-text text-muted small"><?= substr(htmlspecialchars($row['description']), 0, 75); ?>...</p>
                            
                            <div class="mt-auto">
                                <h4 class="text-primary fw-bold mb-3">$<?= number_format($row['price'], 2); ?></h4>
                                <div class="d-flex gap-2">
                                    <?php if($has_variations): ?>
                                        <a href="product_details.php?id=<?= $p_id; ?>" class="btn btn-outline-warning flex-grow-1 btn-sm">Options</a>
                                    <?php else: ?>
                                        <a href="add_to_cart.php?id=<?= $p_id; ?>" class="btn btn-success flex-grow-1 btn-sm">Add to Cart</a>
                                    <?php endif; ?>
                                    <a href="product_details.php?id=<?= $p_id; ?>" class="btn btn-secondary flex-grow-1 btn-sm">Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <nav class="col-12 mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?= $page - 1; ?>&sort=<?= $sort; ?>&cat_id=<?= $cat_id; ?>&search=<?= urlencode($searchTerm); ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?= $i; ?>&sort=<?= $sort; ?>&cat_id=<?= $cat_id; ?>&search=<?= urlencode($searchTerm); ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?= $page + 1; ?>&sort=<?= $sort; ?>&cat_id=<?= $cat_id; ?>&search=<?= urlencode($searchTerm); ?>">Next</a>
                    </li>
                </ul>
            </nav>

        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="display-1 text-muted opacity-25"><i class="bi bi-search"></i></div>
                <p class="fs-5 text-muted mt-3">No products matched your search.</p>
                <a href="product.php" class="btn btn-primary">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>