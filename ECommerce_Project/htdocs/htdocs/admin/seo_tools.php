<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/admin_auth.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';

$message = "";

// --- 1. HANDLE SITEMAP GENERATION ---
if (isset($_POST['generate_sitemap'])) {
    // Basic Setup
    $base_url = "http://" . $_SERVER['HTTP_HOST'] . "/"; 
    $today = date('Y-m-d');
    
    // Start XML String
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    /**
     * Helper Function to append URLs to XML
     */
    function addUrl(&$xml, $loc, $lastmod, $changefreq, $priority) {
        $xml .= "  <url>" . PHP_EOL;
        $xml .= "    <loc>{$loc}</loc>" . PHP_EOL;
        $xml .= "    <lastmod>{$lastmod}</lastmod>" . PHP_EOL;
        $xml .= "    <changefreq>{$changefreq}</changefreq>" . PHP_EOL;
        $xml .= "    <priority>{$priority}</priority>" . PHP_EOL;
        $xml .= "  </url>" . PHP_EOL;
    }

    // A. Static Pages (Highest Priority)
    $static_pages = [
        'index.php' => '1.0',
        'product.php' => '0.9',
        'contact.php' => '0.7',
        'about.php' => '0.7'
    ];
    foreach ($static_pages as $page => $priority) {
        addUrl($xml, $base_url . $page, $today, 'daily', $priority);
    }

    // B. Dynamic Categories
    $cats = mysqli_query($conn, "SELECT id FROM categories");
    while($c = mysqli_fetch_assoc($cats)) {
        addUrl($xml, $base_url . "product.php?cat_id=" . $c['id'], $today, 'weekly', '0.8');
    }

    // C. Dynamic Products
    $prods = mysqli_query($conn, "SELECT id FROM products");
    while($p = mysqli_fetch_assoc($prods)) {
        addUrl($xml, $base_url . "product_details.php?id=" . $p['id'], $today, 'weekly', '0.7');
    }

    // D. Vendor Profiles (New!)
    $vendors = mysqli_query($conn, "SELECT id FROM vendors");
    while($v = mysqli_fetch_assoc($vendors)) {
        addUrl($xml, $base_url . "vendor_shop.php?id=" . $v['id'], $today, 'monthly', '0.5');
    }

    $xml .= '</urlset>';
    
    // Write to root directory
    if (file_put_contents(__DIR__ . '/../sitemap.xml', $xml)) {
        $message = "<div class='alert alert-success shadow-sm'><strong>Success!</strong> Complete sitemap.xml has been generated including static pages, categories, products, and vendors.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: Could not write file. Check folder permissions.</div>";
    }
}

// --- 2. HANDLE SEO TAGS UPDATE ---
if (isset($_POST['update_seo'])) {
    $title = mysqli_real_escape_string($conn, $_POST['meta_title']);
    $desc = mysqli_real_escape_string($conn, $_POST['meta_description']);
    $keys = mysqli_real_escape_string($conn, $_POST['meta_keywords']);

    mysqli_query($conn, "UPDATE seo_settings SET meta_title='$title', meta_description='$desc', meta_keywords='$keys' WHERE id=1");
    $message = "<div class='alert alert-success shadow-sm'>Global SEO settings updated successfully!</div>";
}

// Fetch current SEO settings
$seo_res = mysqli_query($conn, "SELECT * FROM seo_settings WHERE id=1");
$seo = mysqli_fetch_assoc($seo_res);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">SEO & Sitemap Tools</h2>
        <a href="../sitemap.xml" target="_blank" class="btn btn-outline-dark btn-sm">
            <i class="bi bi-box-arrow-up-right"></i> View Sitemap
        </a>
    </div>

    <?= $message; ?>

    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-search me-2"></i>Global Meta Tags</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Site Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($seo['meta_title']); ?>" placeholder="e.g. HK Store | Your Best Shop">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="4" placeholder="Briefly describe your store for search engines..."><?= htmlspecialchars($seo['meta_description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control" value="<?= htmlspecialchars($seo['meta_keywords']); ?>" placeholder="e.g. fashion, electronics, buy online">
                            <div class="form-text">Separate keywords with commas.</div>
                        </div>
                        <button type="submit" name="update_seo" class="btn btn-primary px-4">
                            Save SEO Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Sitemap Engine</h5>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-robot display-1 text-success opacity-25"></i>
                    </div>
                    <p class="text-muted mb-4 px-3">
                        Running the generator will crawl your database to find all active links and build a standard XML sitemap for Google and Bing.
                    </p>
                    <form method="POST">
                        <button type="submit" name="generate_sitemap" class="btn btn-success btn-lg px-5 shadow-sm">
                            <i class="bi bi-play-fill me-2"></i> Generate Now
                        </button>
                    </form>
                    
                    <div class="mt-4 pt-3 border-top mx-4">
                        <p class="small text-muted mb-0">
                            <strong>Format:</strong> XML 1.0<br>
                            <strong>Status:</strong> <?= file_exists('../sitemap.xml') ? '<span class="text-success">Live</span>' : '<span class="text-danger">Not Found</span>'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>