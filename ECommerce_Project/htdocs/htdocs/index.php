<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session.php';

// 1. Fetch Active Banners from DB
$banner_res = mysqli_query($conn, "SELECT * FROM banners WHERE status = 1 ORDER BY id DESC");

// 2. Fetch Categories with a sample product image for the thumbnail
$cat_query = "SELECT c.*, 
             (SELECT p.image FROM products p WHERE p.category_id = c.id LIMIT 1) as cat_thumb 
              FROM categories c";
$cat_result = mysqli_query($conn, $cat_query);
?>

<style>
    /* Category Scroller Styling */
    .category-scroll-wrapper {
        width: 100%;
        overflow: hidden;
        padding: 10px 0;
    }

    .category-scroll-container {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
        gap: 20px;
        padding: 10px 10px 20px 10px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin; /* For Firefox */
        scrollbar-color: #0d6efd #f1f1f1;
    }

    /* Custom Scrollbar for Chrome/Safari */
    .category-scroll-container::-webkit-scrollbar {
        height: 6px;
    }
    .category-scroll-container::-webkit-scrollbar-thumb {
        background: #0d6efd;
        border-radius: 10px;
    }
    .category-scroll-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .category-item {
        flex: 0 0 auto;
        width: 100px;
        text-align: center;
        transition: transform 0.2s;
    }

    .category-circle {
        width: 85px;
        height: 85px;
        margin: 0 auto;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #0d6efd;
        transition: all 0.3s ease;
    }

    .category-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .category-name {
        display: block;
        margin-top: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #333;
        text-transform: capitalize;
    }

    .category-item:hover .category-circle {
        transform: scale(1.1);
        box-shadow: 0 0 0 3px #0a58ca;
    }

    /* Banner Image Constraint */
    .carousel-item img {
        max-height: 500px;
        object-fit: cover;
    }
</style>

<div id="homeBannerCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php 
        $active = true;
        if (mysqli_num_rows($banner_res) > 0):
            while($banner = mysqli_fetch_assoc($banner_res)): 
        ?>
            <div class="carousel-item <?= $active ? 'active' : ''; ?>">
                <img src="Assets/Banners/<?= $banner['image_name']; ?>" class="d-block w-100" alt="<?= htmlspecialchars($banner['title']); ?>">
                <?php if(!empty($banner['title'])): ?>
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                        <h2 class="fw-bold"><?= htmlspecialchars($banner['title']); ?></h2>
                    </div>
                <?php endif; ?>
            </div>
        <?php 
            $active = false; 
            endwhile; 
        else: 
        ?>
            <div class="carousel-item active">
                <img src="Assets/Banners/1.png" class="d-block w-100" alt="Default Banner">
            </div>
        <?php endif; ?>
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#homeBannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#homeBannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center px-2">
        <h5 class="fw-bold text-uppercase mb-0" style="text-decoration:underline;">Explore Categories</h5>
    </div>
    
    <div class="category-scroll-wrapper">
        <div class="category-scroll-container">
            <?php 
            if(mysqli_num_rows($cat_result) > 0):
                while($cat = mysqli_fetch_assoc($cat_result)): 
                    $thumb = !empty($cat['cat_thumb']) ? 'Assets/upload/'.$cat['cat_thumb'] : 'Assets/Banners/default_cat.png';
            ?>
                <div class="category-item">
                    <a href="product.php?cat_id=<?= $cat['id']; ?>" class="text-decoration-none">
                        <div class="category-circle shadow-sm">
                            <img src="<?= $thumb; ?>" alt="<?= htmlspecialchars($cat['name']); ?>">
                        </div>
                        <span class="category-name"><?= htmlspecialchars($cat['name']); ?></span>
                    </a>
                </div>
            <?php 
                endwhile; 
            endif;
            ?>
        </div>
    </div>
</div>

<hr class="container my-4">


<?php 
require_once __DIR__ . '/product.php'; 
?>