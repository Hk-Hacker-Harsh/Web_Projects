<?php
    require_once __DIR__ . '/session.php';
	require_once __DIR__ . '/../db.php';
?>

<!DOCTYPE html>
<html lang="en">
<?php
// Fetch SEO settings
$seo_res = mysqli_query($conn, "SELECT * FROM seo_settings WHERE id=1");
$seo_data = mysqli_fetch_assoc($seo_res);
?>
<head>
    <title><?= $seo_data['meta_title']; ?></title>
    <meta name="description" content="<?= $seo_data['meta_description']; ?>">
    <meta name="keywords" content="<?= $seo_data['meta_keywords']; ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Assets/bootstrap.min.css">
    <style>
        body{
            background-color:white;
        }
        .headpart{
            background-color: rgb(85, 85, 250) !important;
        }

        .sitename{
            color: white;
            font-weight: 700;
        }

        .menu > li > a{
            color: white;
        }

        .searchbut{
            color: white;
            border-color: white;
        }

        .searchbut:hover{
            background-color: white;
            color: black;
            border-color: black;
        }

        .base{
            background-color: rgb(71, 71, 211);
            display: flex;
            height: 18px;
        }

        .mainheader{
            margin-left: 20px;
            margin-right: 20px;
        }
    </style>
</head>
    
<body>
    <!-- Bootstrap Code -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary headpart">
    <div class="container-fluid mainheader">
        <a class="navbar-brand sitename" href="index.php">
        Hk Store
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 menu">
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="http://ecoproject.infinityfree.me/index.php">Home</a>
            </li>
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Category
            </a>
            <ul class="dropdown-menu">
                <?php 
                    $cat_query = "SELECT * FROM categories ORDER BY name ASC";
                    $cat_result = mysqli_query($conn, $cat_query);

                    if (mysqli_num_rows($cat_result) > 0) {
                        while ($cat = mysqli_fetch_assoc($cat_result)) {
                            // Generate link: product.php?cat_id=1
                            echo '<li><a class="dropdown-item" href="http://ecoproject.infinityfree.me/product.php?cat_id=' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a></li>';
                        }
                    } else {
                        echo '<li><a class="dropdown-item disabled">No Categories</a></li>';
                    }
                 
                    echo "<li><hr class='dropdown-divider'></li>";
                    echo "<li><a class='dropdown-item' href='http://ecoproject.infinityfree.me/product.php'>View All Products</a></li>";
                ?>
            </ul>
            <li class="nav-item">
            <a class="nav-link" href="http://ecoproject.infinityfree.me/contact.php">Contact Us</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="http://ecoproject.infinityfree.me/profile.php">My Account</a>
            </li>
            <li class="nav-item" style="align-content: center;">
            <a class="nav-link" style="display: inline;" href="cart.php">
                <img src="http://ecoproject.infinityfree.me/Assets/shopping-cart.png" alt="" height=25px style="display: inline;">
                Cart
            </a>
            </li>
            <?php if(isset($_SESSION['user_id'])) {
                echo "<li class='nav-item'><a class='nav-link' href='http://ecoproject.infinityfree.me/logout.php' style='color:pink'>Logout</a></li>";
            } ?>
        </ul>
        <form class="d-flex" role="search" action="product.php" method="GET">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search"/>
            <button class="btn btn-outline-success searchbut" type="submit">Search</button>
        </form>
        </div>
    </div>
    </nav>
    <div class="base"></div>
