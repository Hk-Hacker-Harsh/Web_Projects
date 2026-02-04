<?php
session_start();

// Change 'id' to 'cart_key' to handle variations
if (isset($_GET['id']) && isset($_GET['action'])) {
    $cart_key = $_GET['id']; // This will now be something like "1_14"
    $action = $_GET['action'];

    if ($action == 'inc') {
        $_SESSION['cart'][$cart_key]++;
    } 
    elseif ($action == 'dec') {
        if ($_SESSION['cart'][$cart_key] > 1) {
            $_SESSION['cart'][$cart_key]--;
        } else {
            unset($_SESSION['cart'][$cart_key]);
        }
    } 
    elseif ($action == 'delete') {
        unset($_SESSION['cart'][$cart_key]);
    }
}

header("Location: cart.php");
exit();
?>