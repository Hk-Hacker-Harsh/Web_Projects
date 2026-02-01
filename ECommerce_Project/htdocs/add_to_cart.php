<?php
session_start();

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // If cart doesn't exist, create it
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // If product is already in cart, increment quantity. Otherwise, set to 1.
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    header("Location: cart.php");
    exit();
}
?>