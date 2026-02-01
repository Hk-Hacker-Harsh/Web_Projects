<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session.php';
?>
<hr>
<div id="carouselExampleRide" class="carousel slide" data-bs-ride="true">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="/Assets/Banners/1.png" class="d-block w-100" alt="Banner 1">
    </div>
    <div class="carousel-item">
      <img src="/Assets/Banners/2.png" class="d-block w-100" alt="Banner 2">
    </div>
    <div class="carousel-item">
      <img src="/Assets/Banners/3.png" class="d-block w-100" alt="Banner 3">
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleRide" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleRide" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
<hr>

<?php 
require_once __DIR__ . '/product.php';
require_once __DIR__ . '/includes/footer.php';
?>