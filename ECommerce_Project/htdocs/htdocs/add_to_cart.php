<?php
session_start();
require_once 'db.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$selected_variations = isset($_GET['variation']) ? $_GET['variation'] : [];

// Create a unique key for this specific variation
// Example: Product 1 with Variation 14 becomes key "1_14"
$variation_string = !empty($selected_variations) ? implode('_', $selected_variations) : '0';
$cart_key = $product_id . '_' . $variation_string;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If this specific combination exists, increment; otherwise, set to 1
if (isset($_SESSION['cart'][$cart_key])) {
    $_SESSION['cart'][$cart_key]++;
} else {
    $_SESSION['cart'][$cart_key] = 1;
}

header("Location: cart.php?msg=added");
exit();
?>